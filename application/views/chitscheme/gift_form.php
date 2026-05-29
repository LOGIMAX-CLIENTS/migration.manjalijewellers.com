<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<style>
* {
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}
ul, li {
    list-style: none;
    text-decoration: none !important;
    padding: 0;
    margin: 0;
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
.my_square{
    background: #fff;
    float: right;}

.branch-blk {
	background: #DE1B5F;
	color: #fff;
	border-radius: 12px;
	margin-bottom: 50px;
	/*padding-bottom: 31px;*/
}
.img-blk{
    display: flex;
    align-items: center;
    /*justify-content: center;*/
}
.gift_dis{
	border-left: 1px solid #f7f0f0f2;  
} 
.white{
    color:#fff;
}
</style>

<div class="main-container">
	<div class="main-container">
		<!-- main -->		  
		<div class="main" >
			<!-- main-inner --> 
			<div class="main-inner">
				<!-- container --> 
				<div class="container">
					<div align="center"><legend class="head" style="margin-left: 16px;">Gift</legend></div>
					<!-- alert -->
					<div class="row">
						<div class="widget-content">
							<?php ?>
							<div class="col-md-12">
								<div class="tab-pane active">					
									<ul class="nav nav-tabs col-md-4">
										<li class="active" id="1"><a href="#tab_gift" data-toggle="tab">My Gifts</a></li>
										<li class="" id="2"><a href="#tab_card" data-toggle="tab">Gifted Cards</a></li>
									</ul>						
									<div id="tab_gift_content" class="tab-content col-md-10">
										<div class="tab-pane overflow active" id="tab_gift">
											<?php
												if(isset($content)){
												foreach($content as $record){  ?> 
											<div class="col-md-offset-1 col-sm-offset col-md-5 col-sm-5 branch-blk"><!---->
												<div class="row">
													<div class="col-md-4  img-blk" class="left">  
														<img class="img-responsive" style="width:50px;height:50px" onerror="this.onerror=null;this.src='https://coimbatorejewellery.in/wcrm/v4_1/admin/assets/img/100.png';" src="<?php echo  base_url()?>admin/assets/img/">
													</div>
													<div class="col-md-4  img-blk" class="left"> 
														<?php if($record['purchased'] = '1'){ ?>
														PURCHASED   
														<?php } else { ?>
														<i class=""  ></i> <?php echo $record['trans_from'] ?>
														<?php }?>
													</div>                         
													<div class="col-md-4 pull-right" >
														<h4> <a href="<?php echo base_url('index.php/user/gifts_form/'.$record['id_gift_card'])?>"/> <i class="fa fa-share-square-o  pull-right white"></i></a></h4>
													</div>
												</div>
												<div class="row" style="padding: 1%;">
													<div class="col-md-4" align="center">
														<h4>Rs. <?php echo number_format($record['amount']) ?> </h4> 
														Use Code <br/>
														<?php echo $record['code']; ?>
													</div>
													<div class="col-md-5 gift_dis">
														Voucher Valid Until <br/> 
														<?php echo $record['valid_to'] ?> ONLY <br/>
														<?php echo $record['redeem_at'] ?>
													</div>
													<div class="col-md-3" class="right"> 
														<img class="img-responsive right" align= "center" onerror="this.onerror=null;this.src='https://coimbatorejewellery.in/wcrm/v4_1/admin/assets/img/qr_code.png';" src="<?php echo  base_url()?>admin/assets/img/"></h5>
													</div>
												</div>
											</div> 
											<?php  }}?>
										</div>
										<div class="tab-pane overflow" id="tab_card">
											<?php
												if(isset($content)){
												foreach($content as $record){  ?> 
											<div class="col-md-offset-1 col-sm-offset col-md-5 col-sm-5 branch-blk"><!---->
												<div class="row">
													<div class="col-md-4  img-blk" class="left">  
														<img class="img-responsive" style="width:50px;height:50px" onerror="this.onerror=null;this.src='https://coimbatorejewellery.in/wcrm/v4_1/admin/assets/img/100.png';" src="<?php echo  base_url()?>admin/assets/img/">
													</div>
													<div class="col-md-4  img-blk" class="left"> 
														<?php if($record['purchased'] = '1'){ ?>
														PURCHASED   
														<?php } else { ?>
														<i class=""  ></i> <?php echo $record['trans_from'] ?>
														<?php }?>
													</div>                         
													<div class="col-md-4 pull-right" >
														<h4> <a href="<?php echo base_url('index.php/user/gifts_form/'.$record['id_gift_card'])?>"/> <i class="fa fa-share-square-o  pull-right white"></i></a></h4>
													</div>
												</div>
												<div class="row" style="padding: 1%;">
													<div class="col-md-4" align="center">
														<h4>Rs. <?php echo number_format($record['amount']) ?> </h4> 
													</div>
													<div class="col-md-5 gift_dis">
														Voucher Valid Until <br/> 
														<?php echo $record['valid_to'] ?> ONLY <br/>
														<?php echo $record['redeem_at'] ?>
													</div>
													<div class="col-md-3" class="right"> 
														<img class="img-responsive right" align= "center" onerror="this.onerror=null;this.src='https://coimbatorejewellery.in/wcrm/v4_1/admin/assets/img/qr_code.png';" src="<?php echo  base_url()?>admin/assets/img/"></h5>
													</div>
												</div>
											</div> 
										<?php  }}?>
										</div>
									</div>
								</div>
								<br/>
								<br/>
								<br/>
								<br/>
							</div>
						</div>
					</div>		
				<!-- /alert -->  
				</div><!-- /.box-body -->
			</div>
			<!-- /container --> 
		</div>
		<!-- /main-inner --> 
	</div>
</div>
</div>
<!-- /main -->		  
<br />
<br />
<br />