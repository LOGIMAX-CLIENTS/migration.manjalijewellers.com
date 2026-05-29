<!doctype html>
<!--<html>-->
<!--	<head>-->
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cus_receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
<!--	</head>-->
	
	<!--<body class="margin">-->
	<div class="PDF_CusReceipt">
		
		<div class="" style="font-size:16px !important;" align="center"> 
			<img src="<?php echo base_url() ?>assets/img/receipt_logo.jpg" class="img-responsive" style="width:100% !important;"></br></br></br>
			<label style="justify-content: center;margin-top: 10px;">Online Advance Booking Acknowledgement</label><br/>
			
			<p style="text-align: justify;">Congratulations ! Advance Payment for <?php echo ($content['history'][0]['type']==1 ? 'Booking Amount' : 'Booking Weight '. $content['history'][0]['metal_weight'].' g'); ?> of Rs.<?php echo $content['history'][0]['payment_amount']; ?>-/- is successful.</p>		
			<p style="text-align: justify;">Your Advance Payment Booking No. is <b><?php echo $content['history'][0]['id_purch_payment']; ?>.</b> You may use this Booking No. for any Further Communication.</p>				
			<p style="text-align: justify;">Thanks for booking with us.</p>
			<?php echo ($comp_details['tollfree1'] != '' ?  "<p>Toll Free : ".$comp_details['tollfree1'] : ""); ?></p>				
			<p>Email : <?php echo $comp_details['email']; ?></p>				
		</div>
	</div>
	</div>
		<script type="text/javascript"> 
		this.print(); 
		</script> 
	<!--</body>
	
</html>-->