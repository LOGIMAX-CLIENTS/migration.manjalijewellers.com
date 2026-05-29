<style>
* {
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
.offer_img_path img {
  max-width: 100%;
  -moz-transition: all 0.5s;
  -webkit-transition: all 0.5s;
  transition: all 0.5s;
}
img {
  max-width: 100%;
  -moz-transition: all 0.5s;
  -webkit-transition: all 0.5s;
  transition: all 0.5s;
}
.offer_img_path:hover img {
	display:inner-block;
  -moz-transform: scale(1.1);
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
off_name
{
	position: relative;
  border: 1px solid #333;
  margin: 2%;
  overflow: hidden;
  top:30px;
}
.off_name img {
  max-width: 100%;
  -moz-transition: all 0.5s;
  -webkit-transition: all 0.5s;
  transition: all 0.5s;
} 
.off_name:hover img {
  -moz-transform: scale(1.1);
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
  <!-- container --> 
<div class="container-fluid header">
	<div class="main-container">
		<div class="main" >
		<!--<img id="slideshow" src="<?php echo base_url(); ?>assets/img/SpecialOffers.jpg">-->
		<!-- main-inner --> 
			<div class="main-inner">
				<!-- container --> 	
				<div class="container">
					<div></div>
					<div class="row" align="center">
						<div align="center"><legend class="head">OFFERS</legend></div>
						<?php   if ($content!=null){ ?>
						<?php
						foreach($content as $value){
						?>
						<div class="col-md-6" align="center" >
							<div class="offer_img_path">
								<div class="align">
									<a  href="<?php echo base_url() ?>index.php/user/offer_description/<?php echo $value['id_offer']; ?>"> <img class="image-responsive" src="<?php echo $value['offer_img_path']?>"/></a>
								</div>
								<b><p align="center" class="off_name" style="background-color:#eade86;"><?php echo $value['name'] ?></p></b>
								<div class="offer_img_path-overlay top"></div>
							</div>
						</div>
						<?php } ?>  
						<?php } else { ?><div> <p> NO OFFERS AVAILABLE</p><?php }?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


