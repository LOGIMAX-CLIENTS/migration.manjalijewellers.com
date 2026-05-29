<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<style>
	.card {
	  background-color: white;
	  border-radius: 0.25rem;
/*	  box-shadow: 0 20px 40px -14px rgba(0, 0, 0, 0.25);*/
	  border: 1.5px solid #eee;
	  display: flex;
	  flex-direction: column;
	  overflow: hidden;
	  margin-bottom: 2px;
	}
	.card_content {
	  padding: 1rem;
	  background: linear-gradient(to bottom left, #EF8D9C 40%, #FFC39E 100%);
	}
	.card_title {
	  font-size: 1.5rem;
	  font-weight: 700;
	  letter-spacing: 1px;
	  text-transform: capitalize;
	  margin: 5px;
	  color:#fff;
	}
</style>
<div class="main-container">
	<div class="main">
		<div class="main-inner">
			<div class="container dashboard">
				<div class="row">
					<div class="col-md-12">
						<div align="center">
							<legend class="head">Auto-Debit Subscription</legend>
							<?php if($content['cf_status'] !=null) {
								if($content['app'] == "web_app"){
									if($content['cf_status'] == "ACTIVE"){
										echo '<h2 style="color: green;"><i class="fa fa-check-square fa-lg"></i><br/> ';
									}
									echo "<h4>".$cf_message."</h4><br/>";
									echo "<h5>Login to check the plan details!!</h4>";
									echo '<a href="'.base_url().'index.php/user/login/" class="btn btn-info">Login</a>';
								}
							}?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
