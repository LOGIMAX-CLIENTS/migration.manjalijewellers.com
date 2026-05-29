<style>

.gallery-title
{
    font-size: 36px;
    color: #42B32F;
    text-align: center;
    font-weight: 500;
    margin-bottom: 70px;
}
.gallery-title:after {
    content: "";
    position: absolute;
    width: 7.5%;
    left: 46.5%;
    height: 45px;
    border-bottom: 1px solid #5e5e5e;
}
.filter-button
{
    font-size: 18px;
    border: 1px solid #42B32F;
    border-radius: 5px;
    text-align: center;
    color: #42B32F;
    margin-bottom: 30px;

}
.filter-button:hover
{
    font-size: 18px;
    border: 1px solid #42B32F;
    border-radius: 5px;
    text-align: center;
    color: #ffffff;
    background-color: #42B32F;

}
.btn-default:active .filter-button:active
{
    background-color: #42B32F;
    color: white;
}

.port-image
{
    width: 100%;
}

.gallery_product
{
    margin-bottom: 30px;
}

.ecom
{
margin-top: 100px;
  margin-bottom: 100px;
  margin-right: 150px;
  margin-left: 80px;
 
}

</style>
<div class="main-inner">
	<div class="main-inner">
		<div class="container">
			<div align="center"><legend class="head">NEW ARRIVALS</legend></div>
			<?php  if(sizeof($content)!=null){  ?>  
			<?php foreach($content as $value){ ?>
			<div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe">	
				<a  href="<?php echo base_url() ?>index.php/user/newarrivals_description/<?php echo $value['id_new_arrivals']; ?>"> <img class="img-responsive"   style="    width: 100%;
				height: 230px;display: inline-block;padding: 6px;" src="<?php echo $value['new_arrivals_img_path']?>"/></a>							
			</div>
			<?php }	?>	
			<?php } else { ?> <p align="center"> NO NEW ARRIVALS AVAILABLE</p><?php }?>
			<div class "ecom">
				<?php if($this->config->item('ecom_url') != ''){ ?>
				<a class="pull-right btn btn-warning"   style=" width: 134px; height: 34px;" href="<?php echo $this->config->item('ecom_url');?>" /><i class="fa fa-link" align="center"></i> More item </a>
				<?php }	?>
			</div>
		</div>
	</div>
</div>

