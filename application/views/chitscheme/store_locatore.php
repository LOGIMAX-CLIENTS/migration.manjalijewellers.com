<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<style>
    .addr-blk{ 
        padding:5px;
        box-shadow: 0px 4px 19px -1px #b3b3b378;
        min-height:130px;
    }
    .branch-blk{
        padding-left: 81px;
        margin:5% 0px 1% 0px;
        background:#fff;
        
    }
    .top-left{
        position: absolute;
        top: 8px;
        /*left: 16px;*/
        background: #f0ad4e;
        color: #fff;
        padding: 8px;
        border-radius: 0px 20px 20px 0px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        font-size: small;
    }
    .img-blk{
        /*width: 120%;
        height: 240px;*/
        display: inline-block;
        padding: 0px;  
    }
    .img-blk img{
        width: 325px;
        height: 325px;
       /* border-radius: 25px;*/
    } 
</style>
<div class="main-container">
    <div class="main" >
        <!-- main-inner --> 
        <div class="main-inner">
			<!-- container --> 
			<div class="container">
				<div class="row">
					<div class="span">
						<div align="center"><legend class="head"> Store Locator</legend></div> 
						<?php
						if($this->session->flashdata('successMsg')) { ?>
						<div class="alert alert-success" align="center">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
						</div>      
						<?php } else if($this->session->flashdata('errMsg')) { ?>							 
						<div class="alert alert-danger" align="center">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
						</div>
						<?php }?> 
					</div>	
				</div>
				<!-- branch master based Store Locatore Form hh-->             
			   
						 <!-- <label>Search by Store</label>
						
						 <input type="text"  required="true" name="" id="store"  value="<?php echo set_value('record[name]',$record['name']); ?>">
					   <input id="id_branch" name="record[id_branch]" type="hidden" value="<?php echo set_value('record[id_branch]',$record['id_branch']); ?>" />-->
					<!--<button type="submit" id="store_submit" name="store_submit" class="btn btn-primary">Submit</button>-->
				
				<div class="row">
					<?php
						if(isset($content)){
						foreach($content as $record){  ?> 
						<div class="col-md-4 col-sm-4 branch-blk">
							<h4 class="top-left"><?php echo $record['name'] ?></h4> 
							<div class="img-blk" class="center"> 
								<?php if($record['logo'] !=''){ ?>
								<img class="img-responsive center"  onerror="this.onerror=null;this.src='https://coimbatorejewellery.in/wcrm/v4_1/admin/assets/img/branch/';" src="<?php echo  base_url()?>admin/assets/img/branch/<?php echo $record['logo'];?>"/>
								<?php } else { ?>
								<img class="img-responsive center"  onerror="this.onerror=null;this.src='https://coimbatorejewellery.in/wcrm/v4_1/admin/assets/img/no_images.png';" src="<?php echo  base_url()?>admin/assets/img/<?php echo $record['logo'];?>"/>
								<?php }?>
							</div>
							<div class="addr-blk">
								<div class=""> <!-- col-md-offset-2  --> 
									<?php if($record['map_url'] !=''){ ?>
									<a class="pull-right btn btn-warning" href="<?php echo $record['map_url'] ?>" /><i class="fa fa-map-marker" ></i>  View Map </a>
									<?php }?>
									<h6><i class="fa fa-map-marker"  ></i> <?php echo $record['address1'] ?> </h6> 
									<h6><?php echo $record['address2'] ?></h6> 
									  <?php if($record['pincode'] !=''){ ?>
									<h6><?php echo $record['city'] ?> - <?php echo $record['pincode'] ?> </h6> 
									   <?php } else { ?>
										  <i class=""  ></i> <?php echo $record['city'] ?>
											  <?php }?>
									<h6><?php echo $record['state'] ?></h6> 
									<div><i class="fa fa-phone"  ></i> <?php echo $record['phone'] ?></div> 
								   <?php if($record['mobile'] !=''){ ?>
									<div>
										<i class="fa fa-mobile"  ></i> <?php echo $record['mobile'] ?>
										<?php } else { ?>
										<i class=""  ></i> <?php echo $record['mobile'] ?>
									  <?php }?>
									</div> 
								 </div>
							</div>
						</div> 
					<?php } }?>
				</div>
				<!-- /container --> 
			</div>
			<!-- /main-inner --> 
        </div>
        <!-- /main -->	
    </div>
</div>
<script type="text/javascript">
    var branchList  = new Array();
    var branchListArr = new Array();
    branchListArr = JSON.parse('<?php echo json_encode($content); ?>');
</script>
<br />
<br />
<br />