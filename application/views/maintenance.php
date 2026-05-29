<!DOCTYPE html>
<html  dir="ltr" lang="en-US">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title><?php echo $header_data['company_name'];?>	&#124;  Maintenance Page </title>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url();?>/assets/maintenance/css/style.css">
<link rel="stylesheet" href="<?php echo base_url();?>/assets/maintenance/css/font-awesome.min.css">
<link href='//fonts.googleapis.com/css?family=Signika+Negative:300,400' rel='stylesheet' type='text/css'>

<script type='text/javascript' src='<?php echo base_url();?>/assets/maintenance/js/jquery.js'></script>
<style type="text/css">
				body {
				background: url("<?php echo base_url();?>assets/maintenance/img/background.jpg") no-repeat fixed center;
				background-size: cover;
			}
						a, a:hover, .copyright a:hover,a:visited  {
			color: #1BBC9B;
		}
		.subscribe-button button{
			background-color: #1BBC9B		}
		.copyright img {
			vertical-align: middle; 
			 height: auto;margin-bottom:0;
		}
	
</style>
<script type="text/javascript">$SA = {s:243437, asynch: 1, useBlacklistUrl: 1};(function() {   var sa = document.createElement("script");   sa.type = "text/javascript";   sa.async = true;   sa.src = ("https:" == document.location.protocol ? "https://" + $SA.s + ".sa" : "http://" + $SA.s + ".a") + ".siteapps.com/" + $SA.s + ".js";   var t = document.getElementsByTagName("script")[0];   t.parentNode.insertBefore(sa, t);})();</script></head>

<body class="home" >
	<div class="page-wrap" id="content-wrap">
		<section id="content">
							<div class="logo">
					<img alt="Maintenance Page" src="<?php echo base_url(); ?>admin/assets/img/icon.png" width="125" height="125">
				</div><!-- .logo -->
			
								<h1 class="site-title">MAINTENANCE PAGE</a></h1>
											<div class="construction-msg">
											<?php if($m_text !=null) {
														echo $m_text; 
													} 
													else{	?>
					<p>We are very sorry for this inconvenience. We are currently working on something new and we will be back soon with awesome new features. Thanks for your patience.</p>
											<?php }?>
				</div>
						<!--<div class="social-icons clearfix">
				<ul>
											<li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li>
																<li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li>
																<li class="google-plus"><a href="#"><i class="fa fa-pinterest"></i></a></li>
																<li class="youtube"><a href="#"><i class="fa fa-google-plus"></i></a></li>
																<li class="pinterest"><a href="#"><i class="fa fa-youtube"></i></a></li>
																<li class="linkedin"><a href="#"><i class="fa fa-linkedin"></i></a></li>
									</ul>
			</div>--><!-- .social-icons -->
		</section>

		
		<footer id="colophon" class="site-footer">
						<div class="col-md-6 copyright pull-left">Copyright &copy; <a href="<?php echo base_url(); ?>"><?php echo $header_data['company_name'];?> </a></div>
						<div class="col-md-6 copyright pull-right">Powered by <a target="_blank" href="http://www.logimaxindia.com/">
							<img src="<?php echo base_url('assets/img/logimax.png');?>" /></a>
						</div>
		</footer>
	</div><!-- #content-wrap -->
</body>
</html>
<?php exit(); ?>