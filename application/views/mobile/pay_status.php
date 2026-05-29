<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Payment Success</title>
<meta name="" content="">
 <link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" type="text/css" />  </link>
</head>
<body>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-<?php echo  ($status==1?'success':'danger');?>">
			<div class="panel-heading">
				Payment <?php echo  ($status==1?'Success':'Failed');?>
			</div>
			<div class="panel-content">
				 <p><?php echo $msg; ?></p>
				 <p ><small>Click the arrow before payment view to go to previous page.</small></p>
				 
			</div>
		</div>
	</div>
</div>
</body>
</html>
 <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.js" type="text/javascript"></script>   