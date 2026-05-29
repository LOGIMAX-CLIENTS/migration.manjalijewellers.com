<div class="main-container">
	<!-- main -->		  
	<div class="main"  id="schemPayList">
		<!-- main-inner --> 
		<div class="main-inner">
			<!-- container --> 
			<div class="container">
				<div class="row">
					<div align="center"><legend>New Arrival</div></legend>
				</div>
				<div class="row bordar">	
					<div class="col-md-4">
						<img id="img_offer"class="new_arrival_desc_imgs" align="" src="<?php echo $content['new_arrivals_img_path']?>" data-zoom-image="<?php echo $content['new_arrivals_img_path']?>"></a>
					</div>
					<div class="col-md-7" >
					<!-- show rate based updt hh-->
						<div class="bordar">
							<p class="off_name" align="" style=""><b><?php echo $content['name'] ?></p>
							<?php if($content['show_rate'] ==1){ ?>
							<p class="fa fa-inr" align="" style=""><b><?php echo $content['price'] ?></p>
							<?php } else if($content['show_rate'] ==0){ ?>
							<p class="off_price" align="" style=""><b><?php echo $content[''] ?></p>
							<?php } else { ?>
							<p class="off_price" align="" style=""><b><?php echo $content['price'] ?></p>
							<?php }?>
							<p class="off_contant" align="left" style=""><?php echo $content['product_description'] ?></p>
						</div>
					</div>
				</div>	
			</div>
			<!-- /container --> 
		</div>
		<!-- /main-inner --> 
	</div>
</div>
</div>