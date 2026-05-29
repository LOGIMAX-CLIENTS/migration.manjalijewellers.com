<script src="js/jquery-1.6.js"></script>
<script src="js/jquery.jqzoom-core.js"></script>
<link href="css/jquery.jqzoom.css" rel="stylesheet" /> 
<style>
</style>
<script type="text/javascript">
	$(document).ready(function () {
		$('.new_arrival_desc_imgs').jqzoom({
			zoomType: 'standard',
			lens: true,
			preloadImages: false,
			alwaysOn: false,
			zoomHeight: 200,
			zoomWidth:200
		});
	});
</script>
<div class="main-container">
	<!-- main -->		  
	<div class="main"  id="schemPayList">
	  <!-- main-inner --> 
		<div class="main-inner">
			 <!-- container --> 
			<div class="container">
				<div class="row">
					<div align="center"><legend> <?php echo $content['name'] ?></legend></div>
					<div class="col-md-12">
						<div class="">
							<img id="img_offer"class="new_arrival_desc_imgs" align="" src="<?php echo $content['offer_img_path']?>"/></a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" >
						<div class="col-md-07">
						  <br>
							<p class="off_contant" align="" style=""><?php echo $content['offer_content'] ?></p>
						</div>
					</div>	
				</div>
			<!-- /container --> 
			</div>
		  <!-- /main-inner --> 
		</div>
	</div>
</div>