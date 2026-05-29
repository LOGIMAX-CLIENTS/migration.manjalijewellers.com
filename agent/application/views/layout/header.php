<?php

/*include("assets/browser/lib/Browser.php");
$browser = new Browser();
if($browser->getBrowser() != "Firefox" && $browser->getBrowser() != "Chrome")
	exit("<h4>Please use firefox or chrome for better performance</h4>");*/
	
$header_content = $this->user_model->company_details();
$metal_rate = $this->user_model->metal_rates();
$modules = $this->user_model->getModules();
$kyc = $this->user_model->checkAgentKycStatus();

$kyc_status = $kyc['kyc_status'];
$kyc_count = $kyc['kyc_count'];
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
    .visible-xs{
        display: inline !important;
    }
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
/*		letter-spacing: 1px; */
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
	
	/* Media Query for Mobile Devices */
    @media (max-width: 480px) {
        .responsive-logo{
            max-width: 60%;
        }
    }
      
    /* Media Query for low resolution  Tablets, Ipads */
    @media (min-width: 481px) and (max-width: 767px) {
        .responsive-logo{
            max-width: 50%;
        }
    }
      
    /* Media Query for Tablets Ipads portrait mode */
    @media (min-width: 768px) and (max-width: 1024px){
        .responsive-logo{
            max-width: 100%;
        }
    }
      
    /* Media Query for Laptops and Desktops */
    @media (min-width: 1025px) and (max-width: 1280px){
        .responsive-logo{
            max-width: 60%;
        }
    }
      
    /* Media Query for Large screens */
    @media (min-width: 1281px) {
        .responsive-logo{
            max-width: 60%;
        }
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
<!-- DATA TABLES -->
    <link href="<?php echo base_url('assets/plugins/datatables/dataTables.bootstrap.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/plugins/datatables/extensions/rowReorder/rowReorder.min.css'); ?>" rel="stylesheet" type="text/css" />

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
    <div class="container-fluid">
		<div class="visible-xs">
			<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
				<div class="container">
					<div class="navbar-header">
						<a href="#" class="mobile_logo"><img  src="<?php echo base_url(); ?>assets/img/logo.png" style=""></a><button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav navbar-right">
							<?php if($this->session->userdata('username')) { ?>
								<li  <?php if($page == 'dashboard') {  ?> class="active" <?php } ?>>
								    <?php if($kyc_status == 1 && $kyc_count == 3) { ?>
								    <a href="<?php echo base_url() ?>index.php/dashboard"><span>HOME</span> </a> 
								    <?php }  else if($kyc_count < 3) {?>
								    <a href="<?php echo base_url() ?>index.php/user/kyc_form"><span>HOME</span> </a>
								    <?php } else if($kyc_status == 0 && $kyc_count == 3) {?>
								    <a href="<?php echo base_url() ?>index.php/user/kyc_msg"><span>HOME</span> </a>
								    <?php }?>
								    </li>
								<li  <?php if($page == 'kyc_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/kyc_form"><span>KYC</span> </a> </li>
								<li  <?php if($page == 'shop') {  ?> class="active" <?php } ?>><a href="#" target="_blank"><span>SHOP ONLINE</span> </a> </li>
								<!--<li  <?php if($page == 'req_settlmt') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/req_settlement"><span>REQUEST SETTLEMENT</span> </a> </li>-->
								<li  <?php if($page == 'refer_earn') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/refer_earn"><span>REFER & EARN</span> </a> </li>
								<li  <?php if($page == 'myaccount') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/myaccount"><span>MY ACCOUNT</span> </a> </li>
								<li  <?php if($page == 'aboutus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/aboutus">ABOUT US </a> </li>
								<li  <?php if($page == 'contactus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/contactus">CONTACT US </a> </li>
							    <li ><a href="<?php echo base_url() ?>index.php/user/logout"><span>LOGOUT</span> </a> </li>      
							<?php } else { ?>									
								<li <?php if($page == 'login') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/login">LOGIN</a></li>
								<li  <?php if($page == 'aboutus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/aboutus">ABOUT US </a> </li>
								<li  <?php if($page == 'contactus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/contactus">CONTACT US </a> </li>
                	<?php } ?>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	<!-- WEB HEADER -->
	
	
    <div class="container-fluid"> 
		<div class="row hidden-xs">
			<div class="topContainer"> 
				<div class="row"> 
				    <div class="col-md-offset-1 col-md-5 col-sm-offset-1 col-sm-4" > 
						<img src="<?php echo base_url(); ?>assets/img/logo.png" class="responsive-logo">
					</div>							
					
				</div>
					
				</div>

				</div>
			</div> 
	
		<div class="row">
			<div class="logoContainer">
				<div class="container" style="width: fit-content;">									
					<?php if($this->session->userdata('username')) { ?>
						<div class="hidden-xs">
							<ul class="menu">
							    <li  <?php if($page == 'dashboard') {  ?> class="active" <?php } ?>>
								    <?php if($kyc_status == 1 && $kyc_count == 3) { ?>
								    <a href="<?php echo base_url() ?>index.php/dashboard"><span>HOME</span> </a> 
								    <?php }  else if($kyc_count < 3) {?>
								    <a href="<?php echo base_url() ?>index.php/user/kyc_form"><span>HOME</span> </a>
								    <?php } else if($kyc_status == 0 && $kyc_count == 3) {?>
								    <a href="<?php echo base_url() ?>index.php/user/kyc_msg"><span>HOME</span> </a>
								    <?php }?>
								</li>
							    <li  <?php if($page == 'kyc_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/kyc_form"><span>KYC</span> </a> </li>
								<li  <?php if($page == 'shop') {  ?> class="active" <?php } ?>><a href="#" target="_blank"><span>SHOP ONLINE</span> </a> </li>
								<!--<li  <?php if($page == 'req_settlmt') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/req_settlement"><span>REQUEST SETTLEMENT</span> </a> </li>-->
								<li  <?php if($page == 'refer_earn') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/refer_earn"><span>REFER & EARN</span> </a> </li>
								<li  <?php if($page == 'myaccount') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/myaccount"><span>MY ACCOUNT</span> </a> </li>
								<li <?php if($page == 'aboutus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/aboutus">ABOUT US </a> </li>
								<li <?php if($page == 'contactus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/contactus">CONTACT US </a> </li>
								<li><a href="<?php echo base_url() ?>index.php/user/logout"><span>LOGOUT</span> </a> </li> 
							</ul>
						</div>
					<?php } else { ?>
						<div class="hidden-xs"> 
							<ul class="menu">
								<li <?php if($page == 'login') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/login">LOGIN</a></li>
								<li <?php if($page == 'aboutus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/aboutus">ABOUT US </a> </li>
								<li <?php if($page == 'contactus') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/contactus">CONTACT US </a> </li>
							 </ul>
						</div>
						<?php } ?>
						</div>
					</div>
		        </div>
			</div>
		</div>						
		
			