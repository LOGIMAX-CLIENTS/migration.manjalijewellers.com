<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<title>CashFree mobile Payments</title>

		<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">

		 <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" type="text/css" /> 

		<script src="<?php echo base_url() ?>assets/js/jquery-1.7.2.min.js"></script> 
	</head>



	<body>

	           <div class="jumbotron vertical-center"> <!-- 

                      ^--- Added class  -->

				  <div class="container">

					<h1>Please wait your CashFree payment is processing ...</h1>

				  </div>

				</div>

					
 
		 <form id="mobile_cashfreepayment" action='https://test.cashfree.com/billpay/checkout/post/submit' target="_self" method='post'>


		<!--<form id="mobile_cashfreepayment" action='https://www.cashfree.com/checkout/post/submit' target="_self" method='post'>-->
			
		

			<input type="hidden" name="appId" value ="<?php echo $cashfreepay['appId'];?>" />

			<input type="hidden" name="orderId" value="<?php echo $cashfreepay['orderId'];?>" />

			<input type="hidden" name="orderAmount" value="<?php echo $cashfreepay['orderAmount'];?>" />	
			
			<input type="hidden" name="orderCurrency" value="<?php echo $cashfreepay['orderCurrency'];?>" />		
			
			<input type="hidden" name="orderNote" value="<?php echo $cashfreepay['orderNote'];?>" />		

			<input type="hidden" name="customerName" value="<?php echo $cashfreepay['customerName'];?>" />
			
			<input type="hidden" name="customerPhone" value="<?php echo $cashfreepay['customerPhone'];?>" />

			<input type="hidden" name="customerEmail" value="<?php echo $cashfreepay['customerEmail'];?>" />
			
			<input type="hidden" name="returnUrl" value="<?php echo $cashfreepay['returnUrl'];?>" />	
			
		    <input type="hidden" name="notifyUrl" value="<?php echo $cashfreepay['notifyUrl'];?>" />
			
				<input type="hidden" name="signature" value="<?php echo $cashfreepay['signature'];?>" />
				

			<input type= "submit" value="submit" style="display:none">

		</form>
	</body>

</html>

<script type="text/javascript">
$(window).load(function() {
 
	//$(".loader").fadeOut("slow");

})

$(document).ready(function(){

   $("form#mobile_cashfreepayment").submit();

});
</script>




