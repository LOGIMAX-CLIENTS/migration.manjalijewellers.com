<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">


<html>


	<head>


		<title>Payu Payments</title>


		<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">


		 <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" type="text/css" /> 


		<script src="<?php echo base_url() ?>assets/js/jquery-1.7.2.min.js"></script> 


	</head>


	


	<body>


	           <div class="jumbotron vertical-center"> <!-- 


                      ^--- Added class  -->


				  <div class="container">


					<h1>Please wait your payment is processing ...</h1>


				  </div>


				</div>


		


		<!--


		<div class="loader"><div class="load-text"><h1>Please wait your payment is processing ...</h1><div></div>-->


			<!--<form id="mobile_payment" action='https://secure.payu.in/_payment' target="_self" method='post'>-->
			 
			 
	    <form id="mobile_payment" action='https://test.payu.in/_payment' target="_self" method='post'>


			<input type="hidden" name="firstname" value="<?php echo $pay['firstname'];?>" />


			<input type="hidden" name="lastname" value="<?php echo $pay['lastname'];?>" />


			<input type="hidden" name="surl" value="<?php echo $pay['surl'];?>" />


			<input type="hidden" name="phone" value="<?php echo $pay['phone'];?>" />


			<input type="hidden" name="key" value="<?php echo $pay['key'];?>" />


			<input type="hidden" name="curl" value="<?php echo $pay['curl'];?>" />


			<input type="hidden" name="furl" value="<?php echo $pay['furl'];?>" />


			<input type="hidden" name="txnid" value="<?php echo $pay['txnid'];?>" />


			<input type="hidden" name="productinfo" value="<?php echo $pay['productinfo'];?>" />


			<input type="hidden" name="amount" value="<?php echo $pay['amount'];?>" />


			<input type="hidden" name="email" value="<?php echo $pay['email'];?>" />


			<input type="hidden" name="udf1" value="<?php echo $pay['udf1'];?>" />


			<input type="hidden" name="udf2" value="<?php echo $pay['udf2'];?>" />


			<input type="hidden" name="udf3" value="<?php echo $pay['udf3'];?>" />


			<input type="hidden" name="udf4" value="<?php echo $pay['udf4'];?>" />


			<input type="hidden" name="udf5" value="<?php echo $pay['udf5'];?>" />


			


			<input type="hidden" name="pg" value="<?php echo $pay['pg'];?>" />


			<input type="hidden" name="bankcode" value="<?php echo $pay['bankcode'];?>" />


			


			<?php if($pay['pg'] != 'NB') { ?>


			<input type="hidden" name="ccnum" value="<?php echo $pay['ccnum'];?>" />


			<input type="hidden" name="ccname" value="<?php echo $pay['ccname'];?>" />


			<input type="hidden" name="ccvv" value="<?php echo $pay['ccvv'];?>" />


			<input type="hidden" name="ccexpmon" value="<?php echo $pay['ccexpmon'];?>" />


			<input type="hidden" name="ccexpyr" value="<?php echo $pay['ccexpyr'];?>" />

			<input type="hidden" name="user_credentials" value="<?php echo $pay['user_credentials'];?>" />
			<input type="hidden" name="store_card" value="<?php echo $pay['store_card'];?>" />
			<input type="hidden" name="store_card_token" value="<?php echo $pay['store_card_token'];?>" />

			<?php } ?>


			


			<input type="hidden" name="hash" value ="<?php echo $pay['hash'];?>" />


			


			<input type= "submit" value="submit" style="display:none">


		</form>


	</body>


</html>


<script type="text/javascript">


$(window).load(function() {


	//$(".loader").fadeOut("slow");


})


$(document).ready(function(){


   $("form#mobile_payment").submit();


});


</script>