<?php  
//$page_content=$this->user_model->pageContent();
//$agent = $this->user_model->agent_details();   
//print_r($this->session->all_userdata());exit;
?>

<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet" type="text/css">
<div class="container-fluid">
<div class="">							
	<div align="left"><legend class="referhead">REFER & EARN</legend></div>
</div>
	<div class="row">
		<div class="container">
			
			<div class="col-md-12" style="margin-bottom:100px;">
				<div class="container col-md-12" >
					<div class="row refer-img" align="center">
						<img src="<?php echo base_url() ?>assets/img/ref.svg">
					</div>
					<div class="row refer-text" align="center">
						<span><p>Refer your friends using the below code and earn commission <br> on purchase made by them.</p></span>
					</div>
					<div class="row" align="center">
						<div class="col-md-12"><div class="refer-code"><?php echo $agent['agent_code']; ?></div></div>
					</div>
					<div class="row refer-terms" align="center">
						<a href="#" id="referterms">Read Referral Terms</a>
					</div><br>
					<div class="row social-icons" align="center">
						
					<a href="https://wa.me/?text=<?php echo $message['whatsapp'];?>"><img src="<?php echo base_url() ?>assets/img/whatsapp.png"></a>
					<a href="sms:?body=<?php echo $message['whatsapp'];?>" ><img src="<?php echo base_url() ?>assets/img/sms.png"></a>
				<!--	<a href="#"><img src="<?php echo base_url() ?>assets/img/facebook-new.png"></a>
					<a href="#"><img src="<?php echo base_url() ?>assets/img/twitter.png"></a>
					<a href="#"><img src="<?php echo base_url() ?>assets/img/instagram-new.png"></a>
					<a href="#"><img src="<?php echo base_url() ?>assets/img/gmail.png"></a> -->
					</div>
				</div>
					
					</br>
					</br>
					
				<div class="modal fade" id="refercontent" tabindex="-1" role="dialog" aria-labelledby="myModalLabelRef" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
							<h4 class="modal-title" id="myModalLabelRef" align="center"><?php echo $page_content['name']; ?></h4>
							</div>
							<div class="modal-body">
								<div class="row refertermscontent">
									<?php echo $page_content['content']; ?>
								</div> 
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>	
