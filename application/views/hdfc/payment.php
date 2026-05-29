<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title>CCAvenue Payments</title>

	<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">

	<link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" type="text/css" /> 

	<script src="<?php echo base_url() ?>assets/js/jquery-1.7.2.min.js"></script> 

	</head>

	<body>

	   <div class="jumbotron vertical-center"> 

		  <div class="container">

			<h1>Please wait your payment is processing ...</h1>

		  </div>

		</div>			

		<?php

			$encrypted_data = $hdfcpay['encRequest'];

			$access_code    = $hdfcpay['access_code'];

		?>  

		<form method="post" name="redirect" action="https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
			<!--https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction-->
    			<?php
    			echo "<input type='hidden' name='encRequest' value=$encrypted_data>";
    			echo "<input type='hidden' name='access_code' value=$access_code>";  
    			?>
    		</form>

		<script language='javascript'>document.redirect.submit();</script>

	</body>

</html>