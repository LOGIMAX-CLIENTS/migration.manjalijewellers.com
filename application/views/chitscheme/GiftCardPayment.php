<?php 
$header_content = $this->login_model->company_details();
?>
<div class="main-container">
	<!-- main -->		  
	<div class="main"  id="schemPayList">
		<!-- main-inner --> 
		<div class="main-inner">
			<!-- container --> 
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div align="center">
							<legend><span class="head" style="padding:25px 0px 10px;">Gift Card Payment</span></legend>
						</div>
						<div class="col-md-12 ">
							<br>
						</div>
					</div>		
					<div class="schemeTable col-md-12" >
						<div class="table-responsive">
						<?php if(isset($giftcard[0][amount])){?>
							<table  id="Giftcard_pay"  class="table table-bordered table-striped table-responsive display">
								<thead>
									<tr style="text-align: center;">
										<th>Sl no</th>
										<th>Payment Mode</th> 
										<th>Amount</th>
										<th>Status</th>
									 <!--	<th>code</th>-->
									 </tr>
								</thead>
								<tbody>
								
								 
								<tr >
								<td></td><td></td><td></td><td></td>
								</tr>
								
								</tbody> 
							</table>
							<?php }else{?>
							<div class="alert alert-danger" align="center">
								<strong>You didn't have any gift card payments.</strong>
							</div>
							<?php } ?>
						</div>	   
					</div>		
				</div>	
			</div>
			<!-- /container --> 
		</div>
		<!-- /main-inner --> 
	</div>
	<!-- /main -->	



