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
						<div align="center"><legend class="head">Rate History</legend></div>
						<div class="col-md-12 ">	
							<?php if($header_content['branch_settings']== 1 && (($header_content['is_branchwise_rate']==1 && $header_content['is_branchwise_cus_reg']==1) || ($header_content['is_branchwise_rate']==1 && $header_content['is_branchwise_cus_reg']==0)	 ) ){?>
							<div class="col-md-2">
								<!--<label>Select Branch to view Gold Rate's &nbsp;</label>-->
								<select id="branch_select" class="form-control" required>
								</select>
								<input id="id_branch" name="id_branch" type="hidden" value=""/>
								<input id="branch_settings" name="branch_settings" type="hidden" value="<?php echo $header_content['branch_settings']; ?>"/>
								<!--	<input type="hidden"  id="is_branchwise_cus_reg" value="<?php echo$data['is_branchwise_cus_reg']; ?>" >-->
							</div>
							<?php } ?>
							<div class="col-md-10 ">
								<div class="col-md-8">
									<div class="col-md-2">
										<label ><b>From : </b></label>
									</div>
									<div class="col-md-4">
										<input data-provide="datepicker" style="margin-left: -33px;"  type="text" id="from_date" name="from_date" value="<?php echo date('m/d/Y'); ?>"  class="form-control"  />
									</div>
									<div class="col-md-2">
										<label ><b>To: </b></label>
									</div>
									<div class="col-md-4">
										<input data-provide="datepicker"  data-date-end-date="0d" style="margin-left: -33px;"  type="text" id="To_date" name="To_date" value="<?php echo date('m/d/Y'); ?>" class="form-control"  />
									</div>
								</div>
								<input type="button" style="margin-left: 10px;" onclick="submit_ratehis()" id="rate_search" name="rate_search" class="button btn btn-primary btn-large" value="Submit" >
							</div>
						<!--	<div class="col-md-8">
						<strong>From Date:</strong> <input type="date" id="from_date" name="from_date" required > 
						<strong>To Date:</strong> <input type="date" id="To_date" name="To_date" required>
						<input type="button" style="margin-left: 10px;" id="rate_search" name="rate_search" value="Submit" >
						</div>-->
						<br>
						</div>
					</div>
					<div class="schemeTable col-md-12" >
						<div class="table-responsive">
							<table  id="rate_history"  class="table table-bordered table-striped table-responsive display">
								<thead>
									<tr style="text-align: center;">
										<th>Date</th>
										<th>Gold Rate 22ct</th> 
										<th>Sliver Rate 1grm</th>
										<th>Platinum 1grm</th>
									</tr>
								</thead>
								<tbody>
									<tr >
										<td></td><td></td><td></td><td></td>
									</tr>
								</tbody> 
							</table>
						</div>			 
					</div>
				</div>	
			</div>
			<div class="overlayy" style=" position: absolute;width: 100%;height: 100%;font-size: 20px;z-index: 50;background: rgba(255,255,255,0.7);border-radius: 3px;margin-left: 10;display:none;">
			<i class="fa fa-refresh fa-spin" style="margin-left: 50%;"></i>
			</div>
			<!-- /container --> 
		</div>
		<!-- /main-inner --> 
	</div>
	<!-- /main -->	