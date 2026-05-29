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
		/*padding: 0px 4px 0px 4px !important; */ 
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
		padding:4px 6px 4px 6px !important;
	} 
	.metalrate td{padding: 0px 4px 0px 4px !important;}
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
<div class="container-fluid visible-xs">
	<nav class="navbar navbar-default" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<a href="#" class="col-xs-10 mobile_logo"><img  src="<?php echo base_url(); ?>assets/img/logo.png" style=""></a><button type="button" class="col-xs-2 navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<?php if($this->session->userdata('username')) { ?>
					<?php if($this->session->userdata('branch_settings')==1 &&$this->session->userdata('branch_name')!='' ){?>
						<li class="pull-left theme-bg"><a href="#"><span class="theme-bg"> Branch : <?php echo $this->session->userdata('branch_name')?></span> </a> </li>
						<?php }?>
						<li <?php if($page == 'dashboard') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/dashboard"><span>Dashboard</span> </a> </li>
						<li  <?php if($page == 'schemes'  ) {  ?>  class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/schemes"><span>Join Purchase plan</span></a> </li>
						 <li  <?php if($page == 'payment' || $page == 'otherPay') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/paymt"><span>Pay Dues</span> </a> </li>      
						<li  <?php if($page == 'payHistory') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/paymt/payment_history"><span>Payment History</span> </a> </li>
						<li  <?php if($page == 'rate_history') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/ratehistory"><span>Rate History</span> </a> </li>
						<li  <?php if($page == 'profile') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/register_update"><span>Profile</span> </a> </li>
						<li  <?php if($page == 'kyc_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/kyc_form"><span>KYC</span> </a> </li>
						<li  <?php if($page == 'reset_pass') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/reset_passwd"><span>Reset Password</span> </a> </li>
						<li  <?php if($page == 'offers') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/offers"><span>Offers</span> </a> </li>
						<li  <?php if($page == 'newarrivals') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/newarrivals"><span>New arrivals</span> </a> </li>
					<!--	<li <?php if($page == 'list_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/list_form">Gift Card</a></li>-->
						<?php if(sizeof($gift) > 0&& ($gift['gift_type']==1)   ){?>
							<li  <?php if($page == 'gift_artical') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/gift_artical"><span>Gift Article</span> </a> </li>
							<?php } ?> 
					<?php if($header_content['enable_dth']==0) {?>
						<li <?php if($page == 'dth_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/dth_form">Direct To home</a></li>
					<?php } ?>	
					<li  <?php if($page == 'store_locatore') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/store_locatore"><span>STORE LOCATORE</span> </a> </li>
					
					<li ><a href="<?php echo base_url() ?>index.php/user/logout"><span>Logout</span> </a> </li>      
					<?php } else { ?>									
						<li <?php if($page == 'home') {  ?> class="active" <?php } ?>><a target="black" href="#" >HOME</a></li>
						<li <?php if($page == 'login') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/login">LOGIN</a></li>
						<li  <?php if($page == 'schemes'  ) {  ?>  class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/schemes"><span>Join Purchase plan</span></a> </li>							
						<li  <?php if($page == 'offers') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/offers">OFFERS </a> </li>
						<li  <?php if($page == 'newarrivals') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/newarrivals">NEW ARRIVALS </a> </li> 
						<?php if(sizeof($gift) > 0 && ($gift['gift_type']==1)   ){?>
							<li  <?php if($page == 'gift_artical') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/gift_artical"><span>GIFT ARTICLE</span> </a> </li>
					<?php } ?> 
						<!--<li <?php if($page == 'list_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/list_form">List</a></li>-->
			<?php } ?>
				</ul>
			</div>
		</div>
	</nav>
</div>
<div class="row visible-xs">
	<div class="topContainer">
		<div class="container">
				<!--<div class="col-md-4 col-sm-4 log_link">
					 <a target="_blank" href="https://coimbatorejewellery.in/"><img src="<?php echo base_url(); ?>assets/img/logo.png ?>"/></a>
					<!--<img  src="<?php echo base_url(); ?>assets/img/logo.png">-->
				<!--</div>-->
				<!--<div class="col-sm-2 col-md-2" align="center"> 
						<?php if($header_content['branch_settings']==1  && $this->session->userdata('username')=='' ){?>
					   <label>Select Branch wise Gold Rate's &nbsp;</label>
						<select id="branch_selec" class="form-control" required>
							
						</select>
						<!--<input id="id_branch" name="id_branch" type="hidden" value=""/>
						

						<input type="hidden"  id="is_branchwise_cus_reg" value="<?php echo$data['is_branchwise_cus_reg']; ?>" >
				   <?php } ?>
				   </div>-->
				<?php  if( $this->session->userdata('username') ) { ?>
			   <div class="col-sm-7 col-md-4 col-xs-12 metalrate-top" align="center"> 
					<div class="col-sm-5 col-md-5 col-xs-12 metalrate-top" align="center"> 
						<table class="metalrate">
							<thead class="">
								<tr>
									<td class="rt-bdr">Today&#039;s Rate</td>
									<?php /* if($metal_rate['goldrate_18ct']> 0) { ?>
									<td class="gold">Gold 18CT </td>
									<?php } */
									if($metal_rate['goldrate_22ct']> 0) { ?>
									<td class="gold">Gold 22CT</td>
									<?php } 
									if($metal_rate['goldrate_24ct']> 0) { ?>
									<td class="gold rt-bdr">Gold 24CT</td>
									<?php } 
									if($metal_rate['silverrate_1gm']> 0) { ?>
									<td class="silver rt-bdr">Silver</td>
									<?php } 
									if($metal_rate['platinum_1g']> 0) { ?>
									<td class="platinum">Platinum</td>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<tr class="larger">
									<td class="rt-bdr">
										<?php if($header_content['enableGoldrateDisc']==1 || $header_content['enableSilver_rateDisc']==1) { echo "Our Rate (1g)";}else{ echo " 1 Gram";}?> 
									</td>
									<?php 
									/*if($metal_rate['goldrate_18ct']> 0) { 
										echo "<td>&#8377;".$metal_rate['goldrate_18ct']."</td>";
									}*/
									if($metal_rate['goldrate_22ct']> 0) {  
										echo "<td>&#8377;".$metal_rate['goldrate_22ct']."</td>";
									}
									if($metal_rate['goldrate_24ct']> 0) { 
										echo "<td class='rt-bdr'>&#8377;".$metal_rate['goldrate_24ct']."</td>";
									}
									if($metal_rate['silverrate_1gm']> 0) { 
										echo "<td class='rt-bdr'>&#8377;".$metal_rate['silverrate_1gm']."</td>";
									}
									if($metal_rate['platinum_1g']> 0) { 
										echo "<td>&#8377;".$metal_rate['platinum_1g']."</td>";
									} ?>
								</tr>
								<?php if($header_content['enableGoldrateDisc']==1 || $header_content['enableSilver_rateDisc']==1) {?>
								<tr>
									<td class="rt-bdr">
										Market Rate (1g) 
									</td>
									<?php 
									// 18 CT
								/*	if($metal_rate['market_gold_18ct']> 0) {																					echo "<td>&#8377;".$metal_rate['market_gold_18ct']."</td>";
									}else if($metal_rate['market_gold_18ct']> 0 ) {																				echo "<td>&#8377;".$metal_rate['market_gold_18ct']."</td>";
									} */
									// 22 CT
									if($metal_rate['mjdmagoldrate_22ct']> 0) {  
										echo "<td>&#8377;".$metal_rate['mjdmagoldrate_22ct']."</td>";
									}else if($metal_rate['goldrate_22ct']> 0) {  
										echo "<td>&#8377;".$metal_rate['goldrate_22ct']."</td>";
									}
									// 24 CT
									if($metal_rate['goldrate_24ct']> 0) { 
										echo "<td>&#8377;".$metal_rate['goldrate_24ct']."</td>";
									}
									// Siver
									if($metal_rate['mjdmasilverrate_1gm'] >0) { 
										echo "<td>&#8377;".$metal_rate['mjdmasilverrate_1gm']."</td>";
									}else if($metal_rate['silverrate_1gm'] >0) { 
										echo "<td>&#8377;".$metal_rate['silverrate_1gm']."</td>";
									}
									// Platinum
									if($metal_rate['platinum_1g']> 0) { 
										echo "<td>&#8377;".$metal_rate['platinum_1g']."</td>";
									} ?>
								</tr>
								<?php } ?>
							</tbody>
						</table>	
						<?php } else { ?>
						<div class="col-sm-7 col-md-4 metalrate-top" align="center" style="margin-left:-15px;">
							<table class="metalrate" id="metal_rates" >
						   
						   </table>
						</div>
						<?php } ?>									
					</div>
				<!--<div class="col-md-3">
					<div align="center"><a href="" style="color:#000;text-decoration: none;">Available on play store</a></div>
				</div>-->
			</div>
		</div>
	</div>
</div>
<!-- WEB HEADER -->
<div class="container-fluid"> 
	<div class="row hidden-xs">
		<div class="topContainer"> 
			<div class=""> 
				<!--<div class="col-md-offset-6 col-sm-offset-4 col-md-6 col-sm-8  current_price" align="center"> -->
				<div class="container"> 
				<!--<div class="col-md-offset-1 col-md-5 col-sm-5" > 
					<img src="<?php echo base_url(); ?>assets/img/logo.png" class="img-responsive">
				</div>-->
				
				<!--<div class="col-md-offset-1 col-md-5 col-sm-5 log_link">
				 <a target="_blank" href="https://coimbatorejewellery.in/"><img src="<?php echo base_url(); ?>assets/img/logo.png" class="img-responsive" ?></a>
				<!--<img  src="<?php echo base_url(); ?>assets/img/logo.png">-->
			<!--</div>-->
			
					<div class="col-md-5 col-sm-5" > 
						<a href="#"><img src="<?php echo base_url(); ?>assets/img/logo.png" class="img-responsive logo"></a>
					</div>
					<?php if($header_content['branch_settings']==1  && $header_content['is_branchwise_rate']==1  ){ ?>		
					<div class="col-sm-2 col-md-2" align="center">	
						<label>Branch &nbsp;</label>
						<select id="select_branch" class="form-control" required>
							
						</select>
						<input id="branch_id" name="id_branch" type="hidden" value=""/>
						<input type="hidden" id="hdnSession" data-value="<?php echo $this->session->userdata('id_branch')?>" />
						<input type="hidden" id="branch_settingss" value="<?php echo $header_content['branch_settings'] ?>" />
					</div>	
						
					<div class="col-sm-7 col-md-4" align="center" style="margin-left:-15px;">
						<table class="metalrate" id="metal_rate" >
					   
						</table>
					</div>
					<?php } ?>
					<?php  if( ($header_content['branch_settings']==0 || $header_content['is_branchwise_rate']==0 )  ) { ?>
					<div class="col-sm-7 col-md-6 metalrate-top" align="center">   
						<table class="metalrate">
							<thead class="">
								<tr>
									<td class="rt-bdr">Today&#039;s Rate</td>
									<?php /*if($metal_rate['goldrate_18ct']> 0) { ?>
									<td class="gold">Gold 18CT </td>
									<?php } */
									if($metal_rate['goldrate_22ct']> 0) { ?>
									<td class="gold">Gold 22CT</td>
									<?php } 
									if($metal_rate['goldrate_24ct']> 0) { ?>
									<td class="gold rt-bdr">Gold 24CT</td>
									<?php } 
									if($metal_rate['silverrate_1gm']> 0) { ?>
									<td class="silver rt-bdr">Silver</td>
									<?php } 
									if($metal_rate['platinum_1g']> 0) { ?>
									<td class="platinum">Platinum</td>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<tr class="larger">
									<td class="rt-bdr">
										<?php if($header_content['enableGoldrateDisc']==1 || $header_content['enableSilver_rateDisc']==1) { echo "Our Rate (1g)";}else{ echo " 1 Gram";}?> 
									</td>
									<?php 
									/*if($metal_rate['goldrate_18ct']> 0) { 
										echo "<td>&#8377;".$metal_rate['goldrate_18ct']."</td>";
									}*/
									if($metal_rate['goldrate_22ct']> 0) {  
										echo "<td>&#8377;".$metal_rate['goldrate_22ct']."</td>";
									}
									if($metal_rate['goldrate_24ct']> 0) { 
										echo "<td class='rt-bdr'>&#8377;".$metal_rate['goldrate_24ct']."</td>";
									}
									if($metal_rate['silverrate_1gm']> 0) { 
										echo "<td class='rt-bdr'>&#8377;".$metal_rate['silverrate_1gm']."</td>";
									}
									if($metal_rate['platinum_1g']> 0) { 
										echo "<td>&#8377;".$metal_rate['platinum_1g']."</td>";
									} ?>
								</tr>
								<?php if($header_content['enableGoldrateDisc']==1 || $header_content['enableSilver_rateDisc']==1) {?>
								<tr>
									<td class="rt-bdr">
										Market Rate (1g) 
									</td>
									<?php 
									// 18 CT
								/*	if($metal_rate['market_gold_18ct']> 0) {																					echo "<td>&#8377;".$metal_rate['market_gold_18ct']."</td>";
									}else if($metal_rate['market_gold_18ct']> 0 ) {																				echo "<td>&#8377;".$metal_rate['market_gold_18ct']."</td>";
									}*/
									// 22 CT
									if($metal_rate['mjdmagoldrate_22ct']> 0) {  
										echo "<td>&#8377;".$metal_rate['mjdmagoldrate_22ct']."</td>";
									}else if($metal_rate['goldrate_22ct']> 0) {  
										echo "<td>&#8377;".$metal_rate['goldrate_22ct']."</td>";
									}
									// 24 CT
									if($metal_rate['goldrate_24ct']> 0) { 
										echo "<td>&#8377;".$metal_rate['goldrate_24ct']."</td>";
									}
									// Siver
									if($metal_rate['mjdmasilverrate_1gm'] >0) { 
										echo "<td>&#8377;".$metal_rate['mjdmasilverrate_1gm']."</td>";
									}else if($metal_rate['silverrate_1gm'] >0) { 
										echo "<td>&#8377;".$metal_rate['silverrate_1gm']."</td>";
									}
									// Platinum
									if($metal_rate['platinum_1g']> 0) { 
										echo "<td>&#8377;".$metal_rate['platinum_1g']."</td>";
									} ?>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<?php } ?>
				</div>
				
				<!--<div class="col-md-7  current_price" align="center"> 
					 <div class="row" align="center"> 
						 <div class="col-md-6 col-sm-6 ">
							<div><?php if( $header_content['enableGoldrateDisc']==1 && ($metal_rate['mjdmagoldrate_22ct']>=0) ){ ?>Market Gold (22K) 1gm  <?php  echo $header_content['currency_symbol'].'  '. ($metal_rate['mjdmagoldrate_22ct']); }?></div>
							<div><?php if( $header_content['enableGoldrateDisc']==1 && ($metal_rate['goldrate_18ct']>=0) ){ ?>Market Gold (18k) 1gm  <?php  echo $header_content['currency_symbol'].'  '. ($metal_rate['goldrate_18ct']); }?></div>										
							<div>
								<?php if($metal_rate['goldrate_22ct']>0){ ?><div><?php if( $header_content['enableGoldrateDisc']==1) { ?> Our Rate<?php }?> Gold (22K) 1gm  <?php echo $header_content['currency_symbol'];?> <?php echo $metal_rate['goldrate_22ct']; ?></div><?php } ?>
							</div>
							<div>
								<?php if($metal_rate['goldrate_18ct']>0){ ?><div><?php if( $header_content['enableGoldrateDisc']==1) { ?> Our Rate<?php }?> Gold (22K) 1gm  <?php echo $header_content['currency_symbol'];?> <?php echo $metal_rate['goldrate_18ct']; ?></div><?php } ?>
							</div>
								<?php if($metal_rate['goldrate_24ct']>0){ ?><div>&nbsp; Gold (24K) 1gm &nbsp;&nbsp;|&nbsp; <?php echo $header_content['currency_symbol'];?>&nbsp; <?php echo $metal_rate['goldrate_24ct']; ?></div><?php } ?>
						 </div>
						 <div class="col-md-6 col-sm-6 ">
							 <?php if($metal_rate['mjdmasilverrate_1gm']>0&& $header_content['enableSilver_rateDisc']==1) { ?>
								<div>&nbsp;Market Silver 1gm &nbsp;&nbsp;|&nbsp; <?php echo $header_content['currency_symbol']?>&nbsp;<?php echo $metal_rate['mjdmasilverrate_1gm']; ?></div>
							 <?php } ?>
							 <?php if($metal_rate['silverrate_1gm']>0){ ?>
								<div><?php if( $header_content['enableSilver_rateDisc']==1){ ?>   Our Rate <?php  }?>&nbsp;Silver 1gm &nbsp;&nbsp;|&nbsp; <?php echo $header_content['currency_symbol'];?>&nbsp; <?php echo $metal_rate['silverrate_1gm']; ?></div>
							 <?php } ?>
							 <?php if($metal_rate['platinum_1g']>0){ ?>
								<div>&nbsp;Platinum 1gm &nbsp;&nbsp;|&nbsp; <?php echo $header_content['currency_symbol'];?>&nbsp; <?php echo $metal_rate['platinum_1g']; ?></div>
							 <?php } ?>
						 </div>
					</div>	
				</div> 
			</div>-->
			</div>
		</div>
	</div>
</div>
<!--	</div>--> 

<div class="row">
	<div class="logoContainer">
		<div class="container" style="width: fit-content;">									
			<?php if($this->session->userdata('username')) { ?>
				<div class="hidden-xs">
					<ul class="menu">
						<?php if($this->session->userdata('branch_settings')==1 &&$this->session->userdata('branch_name')!='' ){?>
						<li class="pull-left  theme-bg" ><a href="#"><span class="theme-bg">Branch : <?php echo $this->session->userdata('branch_name')?> </span> </a> </li>
						<?php }?>
						<li <?php if($page == 'dashboard') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/dashboard"><span>Dashboard</span> </a> </li>
						<li  <?php if($page == 'schemes'  ) {  ?>  class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/schemes"><span>Join Purchase plan</span></a> </li>
						 <li  <?php if($page == 'payment' || $page == 'otherPay') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/paymt"><span>Pay Dues</span> </a> </li>      
						<li  <?php if($page == 'payHistory') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/paymt/payment_history"><span>Payment History</span> </a> </li>
						<li  <?php if($page == 'rate_history') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/ratehistory"><span>Rate History</span> </a> </li>
						<li  <?php if($page == 'profile') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/register_update"><span>Profile</span> </a> </li>
						<li  <?php if($page == 'kyc_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/kyc_form"><span>KYC</span> </a> </li>
						<li  <?php if($page == 'offers') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/offers"><span>Offers </span></a> </li>
						<li  <?php if($page == 'newarrivals') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/newarrivals"><span>New arrivals </span></a> </li>
						<!--<li <?php if($page == 'list_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/list_form">Gift Card</a></li>-->
						<?php if(sizeof($gift) > 0 && ($gift['gift_type']==1)   ){?>
						<li  <?php if($page == 'gift_artical') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/gift_artical"><span>Gift Article</span> </a> </li>
						<?php } ?>
						<?php if($header_content['enable_dth']==0) {?>
						<li <?php if($page == 'dth_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/dth_form">Direct To home</a></li>
					<?php } ?>		
					<li  <?php if($page == 'store_locatore') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/store_locatore"><span>STORE LOCATORE</span> </a> </li>
					<li ><a href="<?php echo base_url() ?>index.php/user/logout"><span>Logout</span> </a> </li> 
					</ul>	
						<?php } else { ?>
					<div class="hidden-xs"> 
						<ul class="menu">
							<li <?php if($page == 'home') {  ?> class="active" <?php } ?>><a target="black" href="#">HOME</a></li>
							<li <?php if($page == 'login') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/login" >LOGIN</a></li>
							<li  <?php if($page == 'schemes'  ) {  ?>  class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/chitscheme/schemes"><span>Join Purchase plan</span></a> </li>
							<li  <?php if($page == 'offers') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/offers">OFFERS </a> </li>
							<li  <?php if($page == 'newarrivals') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/newarrivals">NEW ARRIVALS </a> </li> 
						<?php if(sizeof($gift) > 0 && ($gift['gift_type']==1)   ){?>
							<li  <?php if($page == 'gift_artical') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/gift_artical">GIFT ARTICLE </a> </li> 
							<?php } ?>
						
					<!--	<li <?php if($page == 'list_form') {  ?> class="active" <?php } ?>><a href="<?php echo base_url() ?>index.php/user/list_form">List</a></li>-->
						 </ul>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>						
		
			