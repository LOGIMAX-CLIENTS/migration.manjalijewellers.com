	<?php
/*include("assets/browser/lib/Browser.php");
$browser = new Browser();
if($browser->getBrowser() != "Firefox" && $browser->getBrowser() != "Chrome")
	exit("<h4>Please use firefox or chrome for better performance</h4>");*/

	

	$header_content = $this->login_model->company_details();
	$metal_rate = $this->login_model->metal_rates();
	$gift = $this->services_modal->gift();  
//print_r($this->session->userdata);
		//print_r($header_content);
	
?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title> <?php echo $footer_data['company_name']." - ".ucfirst($footer_data['page']);?> </title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="shortcut icon" href="<?php echo base_url() ?>favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo base_url() ?>favicon.ico" type="image/x-icon">
<meta name="apple-mobile-web-app-capable" content="yes">
<style>
	td{
		padding: 0px 4px 0px 4px !important; 
	}
	table{
		border: 1px solid #d6d6d6;
	}
	thead{
		border-bottom: 1px solid #dfd6d6;
		letter-spacing: 1px;
	} 
	.metalrate{
		margin-top: 12px;
		letter-spacing: 1px; 
	} 
	.gold{
		border-bottom: 3px solid #F7D100;
		/*background: #F7D100;*/ 
	}
	.silver{
		/*background: #B9BABC;*/
		border-bottom: 3px solid #B9BABC;
	}
	.platinum{
		/*background: #A7A8AD;*/
		border-bottom: 3px solid #A7A8AD;
	} 
	.larger{
		font-size: larger !important;
	} 
	
	.rt-bdr{
		border-right: 1px solid #d6d6d6;
	}
</style>
<script type="text/javascript">
	var baseURL = "<?php echo base_url() ?>"; 
	<?php if($this->session->userdata('username')) {  ?>
	function url_params()
	{
		var url = window.location.href;
		var path=window.location.pathname;
		var params = path.split( 'php/' );
		
		return {'url':url,'pathname':path,'route':params[1]};
	}
	function DisableBackButton() {
		var path =  url_params();
		var ctrl_page = path.route.split('/');
		if(ctrl_page[0]=='paymt')
		{
			window.history.forward()
		}
	}
	DisableBackButton();
	window.onload = DisableBackButton;
	window.onpageshow = function(evt) { if (evt.persisted) DisableBackButton() }
	window.onunload = function() { void (0) }
	<?php } ?>
</script>
<?php
 	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Connection: close");
?>
<link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
<!--<link href="<?php echo base_url() ?>assets/css/main.css" rel="stylesheet">-->
<link href='https://fonts.googleapis.com/css?family=Raleway:400,600,700' rel='stylesheet' type='text/css'>
<link href="<?php echo base_url() ?>assets/css/font-awesome.css" rel="stylesheet">
<!--<link rel="stylesheet" href="<?php echo base_url() ?>assets/ /css/bootstrap-datepicker.css" />-->
<link rel="stylesheet" href="<?php echo base_url() ?>assets/datepicker/css/bootstrap-datepicker.standalone.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/dt/dt-1.10.8/datatables.min.css"/>
<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/main.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/media.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/css/popover-jquery.css" rel="stylesheet">

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 for third party icon useing plugins hh-->
<link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />


<link rel="stylesheet" href="<?php echo base_url() ?>assets/nivo-slider/nivo-slider.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url() ?>assets/nivo-slider/themes/default/default.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url() ?>assets/nivo-slider/themes/light/light.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url() ?>assets/nivo-slider/themes/dark/dark.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo base_url() ?>assets/nivo-slider/themes/bar/bar.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/nivo-slider/jquery.nivo.slider.js"></script>
<!-- Select2 -->
<link rel="stylesheet" href="<?php echo base_url('assets/select2/select2.min.css'); ?>">
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
            <div class="container-fluid header_responstive" >
				<div class="visible-xs">
					<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
						<div class="container">
							<div class="navbar-header">
								<a href="#"><img src="<?php echo base_url(); ?>assets/img/logo.png" class="img-responsive logo_responsive"></a><button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
							</div>
							
						</div>
					</nav>
				</div>
                
				<!--<div class="row visible-xs">
					<div class="topContainer">
						<div class="container">
						<div class="col-md-4 col-sm-4 hidden-xs">
							<img  src="<?php echo base_url(); ?>assets/img/logo.png" style="width: 100%;">
						</div>		
						</div>
					</div>
				</div>-->
			</div>
			
			
			<!-- WEB HEADER -->
			
			
            <div class="container-fluid"> 
				<div class="row hidden-xs">
					<div class="topContainer"> 
						<div class=""> 
							<!--<div class="col-md-offset-6 col-sm-offset-4 col-md-6 col-sm-8  current_price" align="center"> -->
							<div class="row"> 
							<div class="col-md-offset-1 col-md-5 col-sm-5 hidden-xs" > 
								<img src="<?php echo base_url(); ?>assets/img/logo.png" class="img-responsive logo">
							</div>
							 
														
							
							</div> 
						</div>
</div>
						</div>
					</div>
			<!--	</div>-->  
				</div>						
		
			