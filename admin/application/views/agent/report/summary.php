<style>
.profile-user-img {
    margin: 0 auto;
    width: 100px;
    padding: 3px;
    border: 3px solid #d2d6de;
}
.circle {
    border-radius: 50%;
    width: 34px;
    height: 34px;
    padding: 10px;
    background: #fff;
    border: 3px solid #000;
    color: #000;
    text-align: center;
    font: 32px Arial, sans-serif;
  }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
<h1>
Reports
<small>Agent Summary</small>
</h1>
<ol class="breadcrumb">
<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
<li><a href="#">Reports</a></li>
<li class="active">Agent Summary</li>
</ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
					<h3 class="box-title">Agent Summary</h3>
					<div class="box-tools pull-right">
						<div class="input-group col-sm-offset-9 col-sm-3">
							<input type="text" name="" id="mobilenumber" placeholder="Enter The Mobile Number" class="form-control" autocomplete="off"/>
							<span class="input-group-btn">
								<button type="submit" id="cus_search" name="cus_search" class="btn btn-info btn-flat">Search
							</span>
						</div>
						<p></p>
					</div>
				</div>
                <div class="box-body">
    				 <div class="row">
						<div class="col-md-4"> 
				          <div class="box box-primary">
				            <div class="box-body box-profile">
				              <p class="text-muted text-center no-margin"><span id="cus_active" class="pull-left"></span>&nbsp;<span id="loyalty_cus_type" class="pull-right"></span></p>
				              <img id="cus_img" class="profile-user-img img-responsive img-circle" src="<?php echo base_url().'assets/img/default.png'?>" alt="User profile picture" width="150" height="150">
				              <h3 class="profile-username text-center"><span id="cus_name"></span><span id="nick_name"></span></h3>
				              <input type="hidden" id="id_customer" value=""/>
				              <p class="text-muted text-center"><span id="cus_mobile"></span></p>
				              <p class="text-muted text-center" id="cus_mail"></p>
				               
							  
				              <ul class="list-group list-group-unbordered">
				                <li class="list-group-item">
				                  <div class="row">
									<div class="col-md-5">
										<strong><i class='fa fa-map-marker'></i> Address</strong>
										<p id="address"></p>
									</div>
									<div class="col-md-7 no-padding" >
										<p id="social_media"></p>
									</div>
								  </div>
				                </li>
				                <span class="profile-list"></span>
				              </ul>
                              <a class="btn btn-primary btn-block" id="editInfProf" href="#" data-toggle="modal" data-target="#confirm-edit" ><i class="fa fa-edit"></i> EDIT</a>
				            </div>
				            <!-- /.box-body -->
				          </div>
				          <!-- /.box --> 
				        </div>
				        <!-- /.col -->
						<div class="col-md-8">
							<div class="box">
								<div class="box-body">
									<div class="row">
						                <div class="col-sm-3 border-right influencer-blk">
						                  <div class="description-block">
						                    <h5 class="description-header" id="invites_count">0</h5>
						                    <span class="description-text">REFERRALS</span>
						                  </div>
						                </div>
						                <div class="col-sm-3 border-right influencer-blk">
						                  <div class="description-block">
						                    <h5 class="description-header" id="conversions_count">0</h5>
						                    <span class="description-text">CONVERSIONS</span>
						                  </div>
						                </div>
						                <div class="col-sm-3 border-right influencer-blk">
						                  <div class="description-block">
						                    <h5 class="description-header" id="sales_count">0</h5>
						                    <span class="description-text">UNPAID</span>
						                  </div>
						                </div>
						                <div class="col-sm-3">
						                  <div class="description-block">
						                    <h5 class="description-header" id="earnings"></h5>
						                    <input type="hidden" id="cus_cash_points" value="0"/>
						                    <span class="description-text">EARNINGS</span>
						                  </div>
						                </div>
						                
						            </div>
					            </div>
				            </div>
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-2"> 
											<label>Transaction Date</label> 
										</div>
									<!--	<div class="col-md-3"> 
											<div class="form-group">    
												<?php   
													$fromdt = date("d/m/Y", strtotime("-1 months"));
													$todt = date("d/m/Y");
												?>
												<input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
											</div> 
										</div>-->
										<div class="col-md-2">
									    <br/>
										<div class="form-group">
										   <button class="btn btn-default btn_date_range" id="payment-dt-btn"> 
											<span  style="display:none;" id="summary_date1"></span>
											<span  style="display:none;" id="summary_date2"></span>
											<i class="fa fa-calendar"></i> Date range picker
											<i class="fa fa-caret-down"></i>
											</button>
										</div>					
									</div> 
									</div>
									<p></p>
									<div class="nav-tabs-custom">
										<ul class="nav nav-tabs">
											<li class="active influencer-blk my_trans"> <a href="#transactions" data-toggle="pill">My Transactions</a></li>
											<li class="influencer-blk"> <a href="#referrals" data-toggle="pill">My Referrals</a></li>
											<li class="influencer-blk"> <a href="#pending-settlmt" data-toggle="pill">Pending Settlement</a></li>
										</ul>	 
										<div class="tab-content">
											<div class="active tab-pane" id="transactions">
												<div class="table-responsive">
	                            	                 <table id="transactions_list" class="table table-bordered table-striped text-center">
	                            	                    <thead>
	                            	                      <tr>
	                            	                        <th width="5%">#</th>
	                            	                        <th width="10%">Date</th>
	                            	                        <th width="10%">Customer</th>
	                            	                        <th width="10%">Mobile</th>
	                            	                        <th width="10%">Receipt No</th>
	                            							<th width="10%">Pay Amount</th>
	                            	                        <th width="10%">Earnings</th>
	                            	                        <th width="10%">Status</th>
	                            	                      </tr>
	                            	                    </thead>
	                            	                    <tbody></tbody>
	                            	                 </table>
	                                              </div>
											</div>
											
											<div class="tab-pane" id="referrals">
												<div class="table-responsive">
	                            	                 <table id="referrals_list" class="table table-bordered table-striped text-center">
	                            	                    <thead>
	                            	                      <tr>
	                            	                        <th width="5%">#</th>
	                            	                        <th width="10%">Date</th>
	                            	                        <th width="10%">Customer</th>
	                            	                        <th width="10%">Mobile</th>
	                            	                        <th width="10%">Earnings</th>
	                            	                        <th width="10%">Status</th>
	                            	                      </tr>
	                            	                    </thead>
	                            	                    <tbody></tbody>
	                            	                 </table>
	                                              </div>
											</div>
										
											<div class="tab-pane" id="pending-settlmt">
											    <div class='row upd_settlmt_blk'>
											        <div class="col-md-4">
        											    <label>Minimum amount required to settle </label>
        											    <input type="text" id="influ_minimum_amt_required_to_settle" class="form-control" value=0 readonly/>
        											</div>
											        <!--<div class="col-md-8">
        											    <button type="button" id="influ_settlement" class="btn btn-success pull-right" onclick="updInfluencerSettlement();">Settle</button>
        											</div>-->
												</div>
												<div class="table-responsive">
	                            	                 <table id="pending_settlmt_list" class="table table-bordered table-striped text-center">
	                            	                    <thead>
	                            	                      <tr>
	                            	                        <th width="5%">#</th>
	                            	                        <th width="10%">Date</th>
	                            	                        <th width="10%">Amt To Settle</th>
	                            							<th width="10%">Acc No</th>
	                            	                      
	                            	                      </tr>
	                            	                    </thead>
	                            	                    <tbody></tbody>
	                            	                 </table>
	                                              </div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
    				 </div>  
            	</div> 
                <div class="overlay" style="display:none;">
                    <i class="fa fa-refresh fa-spin"></i>
                </div> 
            </div>
        </div>
    </div>
 </section>   
</div>
<!-- modal -->      
<div class="modal fade" id="confirm-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Profile</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label for="" class="col-md-3 col-md-offset-1 ">Payment mode</label>
                <div class="col-md-4">
                    <input type="hidden" id="edit-id-cus" value="" />
                    <select id="ed_pay_mode" name="payment_mode" class="form-control box">
						<option value="1">CASH</option>
						<option value="2">ONLINE</option>
					</select>
                    <p class="help-block"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="" class="col-md-3 col-md-offset-1 ">Bank Name</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="ed_bank_name" name="bank_name" placeholder="Enter Bank Name"> 
                    <p class="help-block"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="" class="col-md-3 col-md-offset-1 ">Bank IFSC</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="ed_bank_ifsc" name="bank_ifsc" placeholder="Enter Bank IFSC"> 
                    <p class="help-block"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="" class="col-md-3 col-md-offset-1 ">Bank Account Number</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="ed_bank_acc_no" name="bank_acc_no" placeholder="Enter Bank Account Number"> 
                    <p class="help-block"></p>
                </div>
            </div>
        </div>      
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_Infprofile" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  

