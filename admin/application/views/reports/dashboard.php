<style type="text/css">
   	hr {   margin-bottom: 8px!important;
    margin-top: 8px!important;}
	.bg-pink{
		background-color:#d63384;
	}

</style>   
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
  <h1>
   Dashboard 
    <small>Control panel</small>
  </h1>
</div> 
<section class="content">
 
  <div class="row">
    <div class="col-xs-12"> 
   
  
     
    <?php  if($access == 1 ) {?>
           
    <div class="box box-primary"> 
        <div class="row"> 
        	<div class="col-md-12"> 
				<?php if($this->session->userdata('branch_settings')==1){?>
				<div class="col-md-4">  
					<label> </label>
					<div class="form-group">
					   <button class="btn btn-default btn_date_range" id="payment-dt-btn"> 
						<span  style="display:none;" id="payment_list1"></span>
						<span  style="display:none;" id="payment_list2"></span>
						<i class="fa fa-calendar"></i> Date range picker
						<i class="fa fa-caret-down"></i>
						</button>
					</div> 
				 </div>	
				 	
				<?php if($this->session->userdata('id_branch')==''){?>
				 <div class="col-md-2">  
					<label></label>
					<div class="form-group">
					    <input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch');?>">
					   <select class="form-control" id="branch_select" style="width:100%;"></select>
					</div> 
				 </div>	
				 <?php }else{?>
				    <input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch');?>">
				 <?php }?>
				 <?php }?>
				
				 <div class="col-md-3 pull-right">
					<div class="input-group margin">
						<input type="text" name="" id="mobilenumber" placeholder="Enter The Mobile Number" class="form-control"/>
						<span class="input-group-btn">
							<button type="submit" id="mob_submit" name="mob_submit" class="btn btn-info btn-flat">Search
						</span>
					</div> 
				 </div>
			
        	</div> 
			
        </div>
    <?php  if($this->session->userdata('profile') != 23) {?>    
        <!-- Main content -->
	<section class="content">
	<div align="left"  style="background: #f5f5f5">
		<ul class="nav nav-tabs">
	      	<li class="active"><a id="tab_crm" href="#crm" data-toggle="tab">CRM</a></li>
		  	<li id="tab_livecockpit"><a href="#live_cockpit" data-toggle="tab">Live CockPit</a></li>
		  	<li id="tab_sales"><a href="#sales" data-toggle="tab">Sales</a></li>
			<li style="display:none" id="tab_credit_history"><a href="#credit_history" data-toggle="tab">Credit History</a></li>
			<li id="tab_stock_and_branch_transfer"><a href="#stock_and_branch_transfer" data-toggle="tab">Stock & Branch Transfer</a></li>
			<li style="display:none" id="tab_villagewise_saleschit"><a href="#villagewise_saleschit" data-toggle="tab">Village Wise Sales & Chit</a></li>
			<li  id="tab_order_management"><a href="#order_management" data-toggle="tab">Order Management</a></li>
			<li id="tab_sale_gchart"><a href="#sale_gchart" data-toggle="tab">Sales Chart</a></li>
			<li id="tab_stock_gchart"><a href="#stock_gchart" data-toggle="tab">Stock Chart</a></li>
			<li style="display:none" id="tab_stats"><a href="#stats" data-toggle="tab">Stats</a></li>
	    </ul>
	</div>
	<div class="tab-content"  style="background: #f5f5f5">
	<br/>
		<div class="tab-pane" id="live_cockpit">
			<?php $this->load->view("dashboard/cockpit"); ?>
		</div>
		<div class="tab-pane" id="sales">
			<?php $this->load->view("dashboard/sales"); ?>
		</div>
		<div class="tab-pane" id="credit_history">
			<?php $this->load->view("dashboard/credit_history"); ?>
		</div>
		<div class="tab-pane" id="stock_and_branch_transfer">
			<?php $this->load->view("dashboard/branch_transfer"); ?>
		</div>
		<div class="tab-pane" id="villagewise_saleschit">
			<?php $this->load->view("dashboard/villagewise_saleschit"); ?>
		</div>
		<div class="tab-pane" id="order_management">
			<?php $this->load->view("dashboard/order_management"); ?>
		</div>
		
		<div class="tab-pane" id="sale_gchart">
			<?php $this->load->view("dashboard/sale_gchart"); ?>
		</div>
		<div class="tab-pane" id="stock_gchart">
			<?php $this->load->view("dashboard/stock_gchart"); ?>
		</div>
		
		<div class="tab-pane" id="stats">
			<?php $this->load->view("dashboard/stats"); ?>
		</div>
          <!-- Small boxes (Stat box) -->
		  <div class="tab-pane active" id="crm">
		  
		<!-- <div class="tab-content">-->
          <div class="row crm">
		  
			 <?php $data=$this->dashboard_model->scheme_group();
            	if($data['walletIntegration']==1)
            	{?>
             <div class="col-lg-3 col-xs-6">
               <div class="info-box  bg-purple">
                <span class="info-box-icon"><i class="fa fa-group"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Wallet Accounts</span>
                  <span class="info-box-number"><?php echo $wallets;  ?></span>
                
                  	 <a style="color: white"  href="<?php echo base_url('index.php/wallet/account/list');?>" class="small-box-footer">Create Wallet A/c <i class="fa fa-arrow-circle-right"></i></a>
	                <a style="color: white" href="<?php echo base_url('index.php/wallet/transaction/list');?>" class="small-box-footer">Wallet Transaction <i class="fa fa-plus-circle"></i></a>
                  	
                
                 	
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- ./col --> 
			
				<?php }else {?>
				
					 <div class="col-lg-3 col-xs-6">
              <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
				<?php if($this->session->userdata('branch_settings')!=0){?>
					<span class="info-box-text" style="display: flex; 
						justify-content: center;align-items: center; font-size: 25px;margin-top:20px;">Dues</span>
				<?php }else{ ?>
					<span class="info-box-text">Dues</span>
				<?php } ?>
                 <!-- <span class="info-box-number"><?php $dues=($payment['all_pay_old']['unpaid']-$payment['all_pay_old']['previous_paid']); echo $dues < 0 ? 0 : $dues; ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: <?php echo $payment['unpaid_avg']?>%"></div>
                  </div>
                  <span class="progress-description">
                    <?php echo $payment['unpaid_avg'];?>% in 30 Days
                  </span> -->
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- ./col --> 
				<?php }?>
   <div id="payments">
            <div class="col-lg-3 col-xs-6">
             <div class="info-box bg-green">
               <span class="info-box-icon"><i class="fa fa-paypal"></i></span>
               <div class="info-box-content">
			   <?php if($this->session->userdata('branch_settings')!=0){?>
					<span class="info-box-text" style="display: flex; 
							justify-content: center;align-items: center; font-size: 25px;margin-top:20px;">Payments</span>
				<?php }else{ ?>
					<span class="info-box-text">Payments</span>
				<?php } ?>
				<span class="info-box-number" id=""></span>
 
 	<?php if($this->session->userdata('branch_settings')!=1){?>
<span class="info-box-number"><?php echo $payment['month']['paid']; ?></span>
<div class="progress">
                   <div class="progress-bar" style="width: <?php echo $payment['paid_avg']?>%"></div>
                 </div>
                 <span class="progress-description">
                   <?php echo $payment['paid_avg'];?>% in 30 Days
                 </span> 
<?php }?>
               </div><!-- /.info-box-content -->
             </div><!-- /.info-box -->
           </div><!-- ./col -->
       </div>
   <div id="accounts">
            <div class="col-lg-3 col-xs-6" >
              <div class="info-box bg-aqua">
               <span class="info-box-icon"><i class="fa fa-group"></i></span>
               <div class="info-box-content">
			   <?php if($this->session->userdata('branch_settings')!=0){?>
					<span class="info-box-text" style="display: flex; 
						justify-content: center;align-items: center; font-size: 25px;margin-top:20px;">Accounts</span>
				<?php }else{ ?>
					<span class="info-box-text">Accounts</span>
				<?php } ?>
				 <!-- <span class="info-box-number" id="all_reg"></span> -->
 
 			<?php if($this->session->userdata('branch_settings')!=1){?>
                
 
					<!-- <span class="info-box-number"><?php echo $account['all_reg']; ?></span> -->
					<div class="progress">
									<div class="progress-bar" style="width: <?php echo $account['increase']?>%"></div>
									</div>
									<span class="progress-description">
									<?php echo $account['increase'];?>% in 30 Days
									</span> 
					
				<?php }?>
               </div><!-- /.info-box-content -->
             </div><!-- /.info-box -->
           </div><!-- ./col -->
           	
           </div>
           <div class="col-lg-3 col-xs-6">
         
              <!-- /.info-box --> 
              <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="ion ion-person-add"></i></span>
                <div class="info-box-content">
				<?php if($this->session->userdata('branch_settings')!=0){?>
                  <span class="info-box-text" style="display: flex; 
       				justify-content: center;align-items: center; font-size: 25px;margin-top:20px;">Customers</span>
				<?php }else{ ?>
					<span class="info-box-text">Customers</span>
				<?php } ?>
                  <!-- <span class="info-box-number"><?php echo $customer['all_reg']; ?></span> -->
                  <!-- <div class="progress">
                    <div class="progress-bar" style="width: <?php echo $customer['increase']?>%"></div> 
                  </div>
                  <span class="progress-description">
                    <?php echo $customer['increase'];?>% in 30 Days
                  </span> -->
                </div> <!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- ./col -->
        
           
                
          </div><!-- /.row -->
          
          <!-- main row -->
            <div class="row" >
			
			 <?php $data=$this->dashboard_model->scheme_group();
            	if($data['walletIntegration']==1)
            	{?>
            	<div class="col-md-3"><!--/ payments -->
            		<div class="box box-success">
		                <div class="box-header">
		                  <h3 class="box-title">Inter Wallet </h3>
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<?php  if($this->session->userdata('branch_settings')==0){?>	
		                  <table class="table table-condensed">
		                    <tr>
		                      <th>Payments</th>
		                      <th>Credit</th>
		                      <th>Redeem</th>
		                     
		                    </tr>
							 <tr>                     
		                      <td>Today</td>
		                      <td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/t'); ?>"><span class="badge bg-green" id="t_trans"><?php echo $inter_wallet['t_trans']; ?></span></a></td>
		                        <td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/tr'); ?>"><span class="badge bg-green" id="t_redeem_trans"><?php echo $inter_wallet['t_redeem_trans']; ?></span></a></td>
		                     
		                    </tr>
		                    <tr>                     
		                      <td>Yesterday</td>
		                      <td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/y'); ?>"><span class="badge bg-green" id="y_trans"><?php echo $inter_wallet['y_trans']; ?></span></a></td>
		                       <td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/yr'); ?>"><span class="badge bg-green" id="y_redeem_trans"><?php echo $inter_wallet['y_redeem_trans']; ?></span></a></td>
		                     
		                    </tr> 
		                   
		                    <tr>
		                    	<td>This Week</td>
		                    	<td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/twr')?>"><span class="badge bg-green" id="tw_trans"><?php echo $inter_wallet['tw_trans']; ?></span></a></td>
		                    	<td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/tw')?>"><span class="badge bg-green" id="tw_redeem_trans"><?php echo $inter_wallet['tw_redeem_trans']; ?></span></a></td>
		                    
		                    </tr>     
		                    <tr>
		                    	<td>This Month</td>
		                    	<td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/tm'); ?>"><span class="badge bg-green" id="tm_trans"><?php echo $inter_wallet['tm_trans']; ?></span></a></td>
		                    	<td><a href="<?php echo base_url('index.php/reports/inter_wallet_detail/tmr'); ?>"><span class="badge bg-green" id="tm_redeem_trans"><?php echo $inter_wallet['tm_redeem_trans']; ?></span></a></td>
		                    	
		                    
		                    </tr>
		                  
		                    
		                  </table>
		              <?php }else {?>
		              	<table  id="interWalList" class="table table-bordered  text-center">
               				<thead>
	                      <tr>
	                        <th>Branch</th>
	                        <th>Credit</th>
	                        <th>Debit</th>
	                       </tr>
	                    </thead> 
               			</table>
               			<!-- <table  id="redeem_list" class="table table-bordered  text-center">
               				<thead>
	                      <tr>
	                        <th>Branch</th>
	                        <th>Redeem</th>
	                       </tr>
	                    </thead> 
               			</table> -->
               			
		              <?php }?>
		                </div><!-- /.box-body -->
<!--Added Division by ARVK-->		                	
		                
		              </div><!-- /.box -->
		              <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Wallet customers</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body no-padding">
                            <!--<table class="table table-condensed">
                                <tr>
                                    <td>Registered</td> 
                                    <td><a href="<?php echo base_url('index.php/reports/inter_wallet_wc'); ?>"><span class="badge bg-green"><?php echo $inter_wallet_accounts; ?></span></a></td> 
                                </tr>  
                                <tr>
                                    <td>Not Registered</td> 
                                    <td><a href="<?php echo base_url('index.php/reports/inter_wallet_woc'); ?>"><span class="badge bg-green" ><?php echo $inter_wallet_accounts_woc; ?></span></a></td> 
                                </tr>  
                            </table>-->
							
							 <table  id="wallet_customer" class="table table-condensed">
                                <tr>
                                    <td>Registered</td> 
                                      <td id="registered"></td> 
                                </tr>  
                                <tr>
                                    <td>Not Registered</td> 
                                       <td id="not_registered"></td> 
                                </tr>  
                            </table>
							
                            </div>
                        </div>
            	</div><!--/ payments -->     
				
					<?php }else {?>
				
				
				
					<div class="col-md-3">
            		   <div class="box box-danger">
			                <div class="box-header">
			                  <table>
								<tr>
									<th style="width: 50%;">Dues</th>
									<th style="width: 50%;text-align: center;"><span class="label bg-red"><?php echo $due['month_due']['due_count']; ?></span></th>
									<th style="width: 50%;text-align: center;"><span class="label bg-red"><?php echo $due['month_due']['amount']; ?></span></th>
								</tr>
							  </table>
							  <?php
							  function moneyFormatIndia($num) {
							  return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num); }?>
							  
			                </div><!-- /.box-header -->
			                <div class="box-body no-padding">
			                  <table class="table table-condensed">
			                    <tr>
			                      <th style="width: 50%;">Type</th>
			                      <th style="width: 50%;text-align: center;">Dues</th>
			                      <th style="width: 50%;text-align: center;">Amount</th>
			                      
			                    </tr>
			                     
								  <tr>
								  <td>Today</td>
			                      
			                      <td style="text-align: center;"><a href="<?php echo base_url('index.php/reports/detail/due/T');?>"><span class="badge bg-red"><?php echo $due['today_due']['due_count'];?></span></a></td>
								  <td style="text-align:right;"><?php echo '&#8377;'.(isset($due['today_due']['amount'])?moneyFormatIndia($due['today_due']['amount']):'0');?></td>
			                    </tr> 
			                    
								<tr>
			                      <td>Yesterday</td>
			                      
			                      <td style="text-align: center;"><a href="<?php echo base_url('index.php/reports/detail/due/Y');?>"><span class="badge bg-red"><?php echo $due['yesterday_due']['due_count'];?></span></a></td>
								  <td style="text-align:right;"><?php echo '&#8377;'.(isset($due['yesterday_due']['amount'])? moneyFormatIndia($due['yesterday_due']['amount']):'0');?></td>
			                    </tr>
								
								<tr>
								
			                      <td>This Week</td>
			                      
			                      <td style="text-align: center;"><a href="<?php echo base_url('index.php/reports/detail/due/TW');?>"><span class="badge bg-red"><?php echo $due['week_due']['due_count'];?></span></a></td>
								  <td style="text-align:right;"><?php echo '&#8377;'.(isset($due['week_due']['amount'])?moneyFormatIndia($due['week_due']['amount']):'0');?></td>
			                    </tr>
								<tr>
			                      <td>This Month</td>
			                      
			                      <td style="text-align: center;"><a href="<?php echo base_url('index.php/reports/detail/due/TM');?>"><span class="badge bg-red"><?php echo $due['month_due']['due_count'];?></span></a></td>
								  <td style="text-align:right;"><?php echo '&#8377;'.(isset($due['month_due']['amount'])?moneyFormatIndia($due['month_due']['amount']):'0');?></td>
			                    </tr>								
			                    <!--<tr>
			                      <td>Cheque/ECS Presentable</td>
			                      
			                       <td style="text-align: right;"><a href="<?php echo base_url('index.php/reports/detail/postdated/pay_status/all/ecs/7');?>"><span class="badge bg-red"><?php echo $pdc['chq_tt_prestable']['payments'];?> / <?php echo  $pdc['ecs_tt_prestable']['payments'];?></span></a></td>
			                    </tr> 
			                    <tr>
			                      <td>Cheque/ECS Presented</td>
			                     
			                      <td style="text-align: right;"><a href="<?php echo base_url('index.php/reports/detail/postdated/pay_status/all/ecs/2');?>"><span class="badge bg-red"><?php echo  $pdc['chq_tt_prestd']['payments'];?> / <?php echo  $pdc['ecs_tt_prestd']['payments'];?></span></a></td>
			                    </tr> -->
			                   		                   
			                  </table>     
			         
			                </div><!-- /.box-body -->
			              </div><!-- /.box -->
                         
                 <!--customer wedding and birthday dates-->
		                     	<div class="box ">
		              <div class="box-header">
		                  <h3 class="box-title">Celebration Dates</h3>
						  <!-- <span class="badge bg-orange" style="margin-left: 70px;">Today</span> -->
		                 
		                </div><!-- /.box-header -->

		                <div class="box-body no-padding">
		                	<table class="table table-condensed">
		                		<tr>
		                        <th></th>  
		                        <th>Birthday</th>  
		                        <th>Wedding day</th>  
								
		                      </tr>
							  <tr>
		                        <td>Today</td> 
		                        <td style="text-align: center;"><a href="<?php echo base_url('index.php/admin_dashboard/customer_wishes/1/T');?>" target="_blank" ><span class="badge bg-pink"><?php echo $birthday['today'];?></span></a></td> 
								 <td style="text-align: center;"><a href="<?php echo base_url('index.php/admin_dashboard/customer_wishes/2/T');?>" target="_blank" ><span class="badge bg-pink"><?php echo $wedding['today'];?></span></a></td> 
		                      </tr>
		                	<tr>
		                        <td>Tomorow</td> 
		                       <td style="text-align: center;"><a href="<?php echo base_url('index.php/admin_dashboard/customer_wishes/1/TMRW');?>" target="_blank" ><span class="badge bg-pink"><?php echo $birthday['tomorrow'];?></span></a></td> 
								 <td style="text-align: center;"><a href="<?php echo base_url('index.php/admin_dashboard/customer_wishes/2/TMRW');?>" target="_blank" ><span class="badge bg-pink"><?php echo $wedding['tomorrow'];?></span></a></td>
		                      </tr>   
							  <tr>
		                        <td>This Week</td> 
		                       <td style="text-align: center;"><a href="<?php echo base_url('index.php/admin_dashboard/customer_wishes/1/TW');?>" target="_blank" ><span class="badge bg-pink"><?php echo $birthday['this_week'];?></span></a></td> 
								 <td style="text-align: center;"><a href="<?php echo base_url('index.php/admin_dashboard/customer_wishes/2/TW');?>" target="_blank" ><span class="badge bg-pink"><?php echo $wedding['this_week'];?></span></a></td>
		                      </tr>
		                	  
		                	</table>
		                </div>
		                </div> 
		 <!--customer wedding and birthday dates-->
						  
            	</div>
				
				<?php }?>
        		<div id="payment_details">
        			
            	<div class="col-md-3"><!--/ payments -->
            		<div class="box box-success" >
		                <div class="box-header" >
		                  <h3 class="box-title"> Total Payments </h3>
		                  
		                  
		                  <div class="pull-right">
		                 <!--	<span class="label bg-green" id="tot_pay"><?php echo $payment['all_pay']['paid']; ?></span> --> 
					<span class="label bg-green" id="tot_pay"><?php echo $payment['today']['paid'];; ?></span>
		                  </div>
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<?php  if($this->session->userdata('branch_settings')==0){?>	
		                  <table class="table table-condensed">
		                    <tr>
		                      <th>Payments</th>
		                      <th>Paid</th>
		                     
		                    </tr>
							 <tr>                     
		                      <td>Today</td>
		                      <td><a href="<?php echo base_url('index.php/reports/detail/payment/t'); ?>"><span class="badge bg-green" id="t_paid"><?php echo $payment['today']['paid']; ?></span></a></td>
		                     
		                    </tr>
		                    <tr>                     
		                      <td>Yesterday</td>
		                      <td><a href="<?php echo base_url('index.php/reports/detail/payment/y'); ?>"><span class="badge bg-green" id="y_paid"><?php echo $payment['yesterday']['paid']; ?></span></a></td>
		                     
		                    </tr> 
		                   
		                    <tr>
		                    	<td>This Week</td>
		                    	<td><a href="<?php echo base_url('index.php/reports/detail/payment/tw')?>"><span class="badge bg-green" id="tw_paid"><?php echo $payment['week']['paid']; ?></span></a></td>
		                    
		                    </tr>     
		                    <tr>
		                    	<td>This Month</td>
		                    	<td><a href="<?php echo base_url('index.php/reports/detail/payment/tm'); ?>"><span class="badge bg-green"  id="tm_paid"><?php echo $payment['month']['paid']; ?></span></a></td>
		                    
		                    </tr>
<!--
		                   <tr>
		                    	<td>Total Payments</td>
		                    	<td><a href="<?php echo base_url('index.php/reports/detail/payment/all'); ?>"><span class="badge bg-green"><?php echo $payment['all_pay']['paid']; ?></span></a></td>
		                    
		                    </tr>-->
		                   <tr>
		                    	<td>Awaiting Payments</td>
		                    	<td><a href="<?php echo base_url('index.php/reports/detail/awaiting'); ?>"><span class="badge bg-orange"  id="awaiting"><?php echo $payment['awaiting']['awtng_count']; ?></span></a></td>
		                    
		                    </tr>
		                    
		                  </table>
		              <?php }else{?>
		               
	                 <!-- <table id="payment_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>Branch</th>
	                        <th >Today</th>
	                       
	                    </thead> 
	                   	<tbody id="name"></tbody>
	                 </table> -->
               		
               			<table  id="payment_list" class="table table-bordered  text-center">
               				<thead>
	                      <tr>
	                        <th>Branch</th>
	                        <th>Sch Pay</th>
	                        <th>Act Paid</th>
	                        <th>Count</th>
	                       </tr>
	                    </thead> 
               			</table>
               		
		              <?php }?>
		                </div><!-- /.box-body -->
<!--Added Division by ARVK-->		                	
		                	<div class="box ">
		              <div class="box-header">
		                  <h3 class="box-title">Payment Through</h3>
		                 
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<?php  if($this->session->userdata('branch_settings')==0){?>
		                	<table  id="pay_join"class="table table-condensed">
		                	<tr>
		                        <td>Mobile App</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/payment/MOBILE'); ?>"><span class="badge bg-green" id="thr_m"><?php echo $payment['mob_paid']['joined_thro']; ?></span></a></td> 
		                      </tr>  
		                	<tr>
		                        <td>Web App</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/payment/WEB'); ?>"><span class="badge bg-green" id="thr_w"><?php echo $payment['web_paid']['joined_thro']; ?></span></a></td> 
		                      </tr>  
		                	<tr>
		                        <td>Admin</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/payment/ADMIN'); ?>"><span class="badge bg-green" id="thr_a"><?php echo $payment['admin_paid']['joined_thro']; ?></span></a></td> 
		                   </tr>  
		                   <tr>
		                        <td>Collection App</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/payment/COLLECTION'); ?>"><span class="badge bg-green" id="thr_c"><?php echo $payment['collection_paid']['joined_thro']; ?></span></a></td> 
		                  </tr>	
		                	</table>
		                <?php } else {?>
		                		<table class="table table-condensed">
		                	<tr>
		                        <td>Mobile App</td> 
		                        <td style="text-align:right"id="thr_m"></td> 
		                        <td style="text-align:right"id="thr_mw"></td> 
		                      </tr>  
		                	<tr>
		                        <td>Web App</td> 
		                        <td style="text-align:right" id="thr_w"></td> 	
		                        <td style="text-align:right" id="thr_ww"></td> 	
		                      </tr>  
		                	<tr>
		                        <td>Admin</td> 
		                        <td style="text-align:right" id="thr_a"></td> 
		                        <td style="text-align:right" id="thr_aw"></td> 
		                   </tr>  
		                   <tr>
		                        <td>Collection App</td> 
		                        <td style="text-align:right" id="thr_c"></td>  
		                        <td style="text-align:right" id="thr_cw"></td>  
		                  </tr>	
		                	</table>
		                <?php }?>
		                </div>
		             </div> 
<!--  / Added Division by ARVK-->
		              </div><!-- /.box -->
            	</div><!--/ payments --> 
            </div>
            	<div id="accounts_detail">
            		
            		<div class="col-md-3"><!--/ Account -->
            		<div class="box box-info">
		              <div class="box-header">
		                  <h3 class="box-title">Total Accounts</h3>
		                  <div class="pull-right">
		                  	<span class="label bg-aqua" id="all_acc"><?php echo $account['all_reg']; ?></span>
		                  </div>
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<?php  if($this->session->userdata('branch_settings')==0){?>
		                	<table class="table table-condensed">
							  <tr>
		                        <td>Without Pay</td> 
		                        <td><a href="<?php echo base_url('index.php/account/withoutPayment'); ?>"><span class="badge bg-red" id="acc_wo_pay"><?php echo $account['acc_wo_pay']; ?></span></a></td> 
		                      </tr> 
		                      <tr>
		                        <th>Account</th>
		                        <th>Joined</th>		                     
		                      </tr>
							  <tr>
		                        <td>Today</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/account/t');?>"><span class="badge bg-aqua"  id="today_reg"><?php echo $account['today_reg']; ?></span></a></td> 
		                      </tr> 
		                      <tr>
		                        <td>Yesterday</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/account/y');?>"><span class="badge bg-aqua" id="yes_reg"><?php echo $account['yes_reg']; ?></span></a></td> 
		                      </tr>     
		                    
		                      <tr>
		                        <td>This Week</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/account/tw');?>"><span class="badge bg-aqua" id="wk_reg"><?php echo $account['wk_reg']; ?></span></a></td> 
		                      </tr>   
		                      <tr>
		                        <td>This Month</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/account/tm');?>"><span class="badge bg-aqua" id="m_reg"><?php echo $account['m_reg']; ?></span></a></td> 
		                      </tr>  
		                	
		                	</table>
		                <?php }else{?>
		                	<table  id="account_list" class="table table-bordered  text-center">
               				<thead>
    	                      <tr>
    	                        <th>Branch</th>
    	                        <th>Account</th>
    	                        <th>Collected Amount</th>
    	                        <th>Collected Weight</th>
    	                       <!-- <th>Without Pay</th>-->
    	                    </thead> 
               			</table>
		                <?php }?>
		                </div>
<!--Added Division by ARVK-->		                	
		                	<div class="box ">
		              <div class="box-header">
		                  <h3 class="box-title">Created Through</h3>
		                 
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<?php  if($this->session->userdata('branch_settings')==0){?>
		                	<table class="table table-condensed">
		                	<tr>
		                        <td>Mobile App</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/account/ma');?>"><span class="badge bg-aqua" id="mob_reg"><?php echo $account['mob']['joined_thro']; ?></span></a></td> 
		                      </tr>  
		                	<tr>
		                        <td>Web App</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/account/wa');?>"><span class="badge bg-aqua"  id="w_reg"><?php echo $account['web']['joined_thro']; ?></span></a></td> 
		                      </tr>  
		                	<tr>
		                        <td>Admin</td> 
		                        <td><a href="<?php echo base_url('index.php/reports/detail/account/a');?>"><span class="badge bg-aqua"  id="a_reg"><?php echo $account['admin']['joined_thro']; ?></span></a></td> 
		                      </tr> 
	                      <tr>
	                        <td>Collection App</td> 
	                        <td><a href="<?php echo base_url('index.php/reports/detail/account/c');?>"><span class="badge bg-aqua"  id="c_reg"><?php echo $account['collection']['joined_thro']; ?></span></a></td> 
	                      </tr>	
		                	</table>
		                <?php }else{?>
		                			<table id="pay_joined" class="table table-condensed">
		                			    <!--<thead>
    	                      <tr>
    	                        <th></th>
    	                        <th>Account</th>
    	                        <th>Collected Amount</th>
    	                       
    	                    </thead> --> 
		                	<tr>
		                        <td>Mobile App</td> 
		                        <td id="mob"></td> 
		                        <td id="mob_c" style="text-align:right"></td> 
		                      </tr>  
		                	<tr>
		                        <td>Web App</td> 
		                       <td id="web"></td> 
		                       <td id="web_c" style="text-align:right"></td> 
		                      </tr>  
		                	<tr>
		                        <td>Admin</td> 
		                       <td id="admin"></td> 
		                       <td id="admin_c" style="text-align:right"></td> 
		                    </tr> 
		                    <tr>
                                <td>Collection App</td> 
                                <td id="collection"></td> 
                                <td id="collection_c" style="text-align:right"></td> 
		                  </tr>	
		                	</table>
		                <?php }?>
		                </div>
		             </div> 
<!--  / Added Division by ARVK-->		
		             
		             </div>   
		           </div> 
            	</div>
            	     	
		           
		           <div class="col-md-3"><!--/ Customers -->
            		<div class="box box-warning">
		              <div class="box-header">
		                  <h3 class="box-title">Customers</h3>
						  <?php  if($this->session->userdata('branch_settings')==1){?>
		                   <div class="pull-right">
								<!-- <span class="label bg-aqua" id="all_acc"><?php echo $customer['all_reg']; ?></span> -->
								<!-- <a href="<?php echo base_url('index.php/reports/detail/registration/all'); ?>">  -->
								
								
								<span  id="customer_count_span" style="cursor: pointer;" ></span></a>
								<!-- <span class="label bg-orange" id="customer_count_span" style="cursor: pointer;" ><?php echo $customer['all_reg']; ?></span></a> -->
								<!-- <span class="label bg-orange" id="customer_count_span" onclick=get_customer_details_data() style="cursor: pointer;"><?php echo $customer['all_reg']; ?></span></a> -->
		                  </div>
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<table class="table table-condensed">
	                       
		                	  <tr>
								
		                      	<td>With out Accounts</td>
			
		                      	<td></td>
		                       <!--<td><a href="<?php echo base_url('index.php/customer/withoutAccount'); ?>"><span class="badge bg-red"><?php $wc= $customer['wo_acc']; $cl=$closed; echo $data=$wc+$cl; ?></a></span></td>-->
		                      	<!-- <td style="text-align:right;"><a target="_blank" href="<?php echo base_url('index.php/customer/withoutAccount'); ?>"><span class="badge bg-red"><?php echo $customer['wo_acc']; ?></a></span></td> -->
								  <td id="without_acc_cus_count" style="text-align:right;"></td>
		                      </tr>
		                      
		                      <tr>
		                      	<td>Interested Customers</td>
		                      	<td></td>
		                      	<!-- <td style="text-align:right;"><a target="_blank" href="<?php echo base_url('index.php/account/withoutPayment'); ?>"><span class="badge bg-red"><?php echo $customer['wo_pay']; ?></a></span></td> -->
								<td id="without_pay_acc_count" style="text-align:right;"></td>
		                      </tr>
		                      
		                    <?php } else
							{

							?>
							 <div class="pull-right">
								
								<a href="<?php echo base_url('index.php/reports/detail/registration/all'); ?>">
								<span class="label bg-aqua" id="all_acc"><?php echo $customer['all_reg']; ?></span>
							 </a>
								
								
								<!-- <span  id="customer_count_span" style="cursor: pointer;" ></span></a> -->
								<!-- <span class="label bg-orange" id="customer_count_span" style="cursor: pointer;" ><?php echo $customer['all_reg']; ?></span></a> -->
								<!-- <span class="label bg-orange" id="customer_count_span" onclick=get_customer_details_data() style="cursor: pointer;"><?php echo $customer['all_reg']; ?></span></a> -->
		                  </div>
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<table class="table table-condensed">
	                       
		                	  <tr>
								
		                      	<td>With out Accounts</td>
			
		                      	<td></td>
		                       <!--<td><a href="<?php echo base_url('index.php/customer/withoutAccount'); ?>"><span class="badge bg-red"><?php $wc= $customer['wo_acc']; $cl=$closed; echo $data=$wc+$cl; ?></a></span></td>-->
		                      	<td style="text-align:right;"><a target="_blank" href="<?php echo base_url('index.php/customer/withoutAccount'); ?>"><span class="badge bg-red"><?php echo $customer['wo_acc']; ?></a></span></td>
								  <!-- <td id="without_acc_cus_count" style="text-align:right;"></td> -->
		                      </tr>
		                      
		                      <tr>
		                      	<td>Interested Customers</td>
		                      	<td></td>
		                      	<td style="text-align:right;"><a target="_blank" href="<?php echo base_url('index.php/account/withoutPayment'); ?>"><span class="badge bg-red"><?php echo $customer['wo_pay']; ?></a></span></td>
								<!-- <td id="without_pay_acc_count" style="text-align:right;"></td> -->
		                      </tr>
							<?php } ?>

		                      <tr>
		                        <th>Customers</th>
		                        <th></th>
		                        <th>Registered</th>		                     
		                      </tr>
							  <tr>
		                      	<td>Today</td>
		                      	<td></td>
		                      	<td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/t'); ?>"><span class="badge bg-orange"><?php echo $customer['today_reg']; ?></a></span></td>
		                      </tr>
		                      <tr>
		                      	<td>Yesterday</td>
		                      	<td></td>
		                      	<td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/y'); ?>"><span class="badge bg-orange"><?php echo $customer['yes_reg']; ?></span></a></td>
		                      </tr>     
		                       
		                      <tr>
		                      	<td>This Week</td>
		                      	<td></td>
		                      	<td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/tw'); ?>"><span class="badge bg-orange"><?php echo $customer['wk_reg']; ?></a></span></td>
		                      </tr>       
		                      <tr>
		                      	<td>This Month</td>
		                      	<td></td>
		                      	<td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/tm'); ?>"><span class="badge bg-orange"><?php echo $customer['m_reg']; ?></a></span></td>
		                      </tr>
		                	</table>
                          
		                </div>
<!--date wise filter cus reg  by hh-->		                	
		                	<div class="box ">
		              <div class="box-header">
		                  <h3 class="box-title">Registered Through</h3>
		                 
		                </div><!-- /.box-header -->
		                
		                <?php if($this->session->userdata('branch_settings')==0){?>
		                <div class="box-body no-padding">
		                	<table class="table table-condensed">
		                	<tr>
		                        <td>Mobile App</td> 
					
		                        <td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/ma');?>"><span class="badge bg-orange"><?php echo $customer['mob']['joined_thro']; ?></span></a></td> 
		                      </tr>  
		                	<tr>
		                        <td>Web App</td> 
		                        <td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/wa');?>"><span class="badge bg-orange"><?php echo $customer['web']['joined_thro']; ?></span></a></td> 
		                      </tr>  
		                	<tr>
		                        <td>Admin</td> 
		                        <td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/a');?>"><span class="badge bg-orange"><?php echo $customer['admin']['joined_thro']; ?></span></a></td> 
		                      </tr>  
		                   <tr>
		                        <td>Collection App</td> 
		                        <td style="text-align:right;"><a href="<?php echo base_url('index.php/reports/detail/registration/c');?>"><span class="badge bg-orange"><?php echo $customer['collection']['joined_thro']; ?></span></a></td> 
		                      </tr>
		                	</table>
		                </div>
		                
		                <?php }else {?>
		                
		                    <div class="box-body no-padding">
		                        
                        <table class="table table-condensed">
		                	<tr>
		                        <td >Mobile App</td> 
					<td></td>
		                        <td style="text-align:right;" id="cus_mob"></td> 
		                      </tr>  
		                	<tr>
		                        <td>Web App</td> 
					<td></td>
		                       <td style="text-align:right;" id="cus_web"></td> 
		                      </tr>  
		                	<tr>
		                       <td>Admin</td> 
					<td></td>
		                       <td style="text-align:right;" id="cus_admin"></td> 
		                      </tr>  
                            <tr>
                                <td>Collection App</td> 
                                <td></td>
								<td id="cus_collection" style="text-align:right;"></td> 
                            </tr>	
		                	</table>
		                        
		                  </div>
		                
		                <?php }?>
		             </div> 
<!--date wise filter cus reg  by hh-->


		             
		             </div>   
		           </div>   
            </div>
          <!-- /main row -->
             <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('profile')== 1 ){  ?>   
      <div class="row">
            
	         <div class="col-md-12">
             	 <div class="box box-solid ">
    
                   <div class="box-header">
                     <h3 class="box-title">Collection</h3><br/>
                        <div align="left">
                            <button class="btn" id="summary">Collection Summary</button>
                            <button class="btn" id="detailed">Detailed Collection</button>
							<?php if($this->session->userdata('id_branch')==''){?>
                            <button class="btn" id="print" value="">Print</button>
							<?php }?>
                        </div>
                        <br/>
                        <table id="collSummary" style="overflow:auto;" class="table table-bordered  text-center" >
                             <thead>
                                <tr class="titlerow">
                                   <th>Branch</th>
                                   <th class="bg-purple">Op Balance (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                   <th class="bg-green">Collection (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                   <th class="bg-orange">Closed (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                   <th class="bg-red">Cancelled (<?php echo $this->session->userdata('currency_symbol') ?>)</th> 
                                   <th class="bg-blue">Closing balance (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                </tr>
                             </thead> 
							 <tfoot>
								<tr style="font-weight: bold;">
								  <td  class="total"  id="total">Total</td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="op_total"></td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="closing_total"></td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="closed_total"></td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="cancelled_total"></td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="closing_balance_total"></td>
								</tr>
						  </tfoot>
                      </table>
                     	<table style="display:none;overflow:auto;" id="collDetail" class="table table-bordered  text-center">
                         <thead>
                            <tr>
                               <th>Branch</th>
                               <th class="bg-purple">Op Balance (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                               <th class="bg-purple">Op Balance (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                              
                               <th class="bg-green">Collection (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                               <th class="bg-green">Collection (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                              
                               
                               <th class="bg-orange">Closed (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                               <th class="bg-orange">Closed (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                              
                               
                               <th class="bg-red">Cancelled (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                               <th class="bg-red">Cancelled (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                              
                               
                               <th class="bg-blue">Closing balance (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                               <th class="bg-blue">Closing balance (W)  <?php echo $this->session->userdata('currency_symbol') ?></th>
                              
                            </tr>
                         </thead> 
						  <tfoot>
								<tr style="font-weight: bold;">
								  <td  class="total"  id="total">Total</td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="op_total_amt"></td>
								  
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8">
										<span id="op_total_wt"></span><br>
										<span id="op_total_wt_scheme"></span>
								  </td>
								   
								   
								   <td  class="total" style="padding:5px;text-align: right;color:#605ca8">
										<span id="collection_tot_amt"></span><br>
								   </td>
								   
								   <td  class="total" style="padding:5px;text-align: right;color:#605ca8">
										<span id="today_collection_wgt"></span><br>
										<span id="collection_tot_wt"></span><br>
								   </td>
									
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="closed_total_amt"></td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8">
									<span id="closed_total_wt"></span><br>
									<span id="closed_total_wt_scheme"></span><br>
								  
								  </td>
								  
								  
								   <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="cancelled_total_amt"></td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8">
										<span id="cancelled_total_wt"></span><br>
										<span id="cancelled_total_wt_scheme"></span><br>
								  </td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8" id="closing_balance_total_amt"></td>
								  <td  class="total" style="padding:5px;text-align: right;color:#605ca8">
								  <span id="closing_balance_total_wt"></span><br>
									<span id="closing_balance_total_wt_scheme"></span><br>
								  </td>
								</tr>
						  </tfoot>
                  </table>
                   </div>
                </div>   
           </div>
        </div>
        <?php }?>
        <div class="row">
            <!--  <div class="col-md-6">
                <div class="box box-solid bg-teal-gradient">
	                <div class="box-header">
	                  <i class="fa fa-th"></i>
	                  <h3 class="box-title">Rate Graph</h3>
	                  <div class="box-tools pull-right">
	                    <a class="btn bg-teal btn-sm" href="<?php echo base_url('index.php/settings/rate/list');?>"><i class="fa fa-plus-circle"></i> Add rate</a> 
	                    <button class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
	         
	                  </div>
	                </div>
	                <div class="box-body border-radius-none">
	                  <div class="chart" id="rate-chart" style="height: 250px;"></div>
	                </div>
	             </div>
             	 <?php if($this->session->userdata('profile') == 1 ) { ?>
             	 <div class="box box-solid ">
    
                   <div class="box-header">
                     <h3 class="box-title">Collection</h3>
                     	<table  id="collection_list" class="table table-bordered  text-center">
                  <thead>
                         <tr>
                           <th>Branch</th>
                           <th>Op Balance </th>
                           <th>Collection </th>
                           <th>Closed </th>
                           <th>Closing balance</th>
                          </tr>
                       </thead> 
                  </table>
                   </div>
                </div>  
                <?php }?>-->
           </div> 
		   
		     <?php if($existing_request['reg_existing']==1){?>
            <div class="col-md-3">
			
			 
            	<div class="info-box bg-green">
 				<span class="info-box-icon"><i class="ion ion-person-add"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Existing Request</span>
                  <span class="info-box-number" id="e_all_reg"><?php echo $existing_request['all_reg']; ?></span>
                 <div class="progress">
                    <div class="progress-bar" style="width: <?php echo $existing_request['increase']?>%"></div>
                  </div>
                  <span class="progress-description">
                    <?php echo $existing_request['increase'];?>% in 30 Days
                  </span>
                </div><!-- /.info-box-content -->
              </div>
            	<div class="box">
		                <div class="box-body no-padding">
		                	<table class="table table-condensed">
		                	<div class="box-header">
		                  <h3 class="box-title">Existing Request</h3>
		                  <div class="pull-right">
		                  	 <a href="<?php echo base_url('index.php/account/scheme_reg/list'); ?>"><span class="label bg-green" id="total_request"><?php echo $existing_request['total_request']; ?></span></a>
		                  </div>
							
							
							<tr>
		                    	<td>Processing Account</td>
		                    	<td><a href="<?php echo base_url('index.php/account/scheme_reg/list/0'); ?>"><span class="badge bg-green" id="exiting_processing"><?php echo$existing_request['exiting_processing']; ?></span></a></td>
		                    
		                    </tr>
		                	
							<tr>
		                    	<td>Approved Account</td>
		                    	<td><a href="<?php echo base_url('index.php/account/scheme_reg/list/1'); ?>"><span class="badge bg-green" id="exiting_approved"><?php echo $existing_request['exiting_approved']; ?></span></a></td>
		                    </tr>
							
							<tr>
		                    	<td>Rejected Account</td>
		                    	<td><a href="<?php echo base_url('index.php/account/scheme_reg/list/2'); ?>"><span class="badge bg-orange" id="exiting_rejected"><?php echo $existing_request['exiting_rejected']; ?></span></a></td>
		                    
		                    </tr>
							
							
							</table>
		                </div>
		             </div>  
		              
            </div>
			
				<?php }?>
						
            
            <div class="col-md-3">
                    <div class="info-box bg-blue">
    
                        <span class="info-box-icon"><i class="fa fa-user-times"></i></span>
        
                        <div class="info-box-content">
        
                          <span class="info-box-text">Closed Accounts</span>
        
                          <span class="info-box-number" id="closed"><?php echo $closed; ?></span>
        
                        </div><!-- /.info-box-content -->
        
                      </div>
          		       <div class="box box-primary">
		              <div class="box-header">
		                  <h3 class="box-title">Accounts</h3>
		                  <div class="pull-right">
		                  	<a href="<?php echo base_url('index.php/reports/detail/closed_acc/all'); ?>"><span class="label bg-blue"><?php echo $closed; ?></span></a>
		                  </div>
		                </div><!-- /.box-header -->
		                <div class="box-body no-padding">
		                	<table class="table table-condensed">
		                	  <tr>
		                        <th>About to Close</th>
		                        <th>Accounts</th>		                     
		                      </tr>
							 
		                        <td>One Installments Remaining</td> 
		                        <td style="text-align: center;"><a href="<?php echo base_url('index.php/reports/detail/close_due/onepending');?>"><span class="badge bg-blue" id="one_pending"><?php echo $one_pending; ?></span></a></td> 
		                      </tr>     
		                    
		                      <tr>
		                        <td>Two Installments Remaining</td> 
		                        <td style="text-align: center;"><a href="<?php echo base_url('index.php/reports/detail/close_due/twopending');?>"><span class="badge bg-blue" id="two_pending"><?php echo  $two_pending; ?></span></a></td> 
		                      </tr>   
		                      <tr>
		                        <td style="color: red;font-weight:bold;">Renewal</td> 
		                        <td style="text-align: center;"><a href="<?php echo base_url('index.php/reports/detail/renewals/all');?>"><span class="badge bg-red"  id="renewal"> <?php echo $renewal; ?></span></a></td> 
		                      </tr>  
		                	</table>
		                </div>
		            </div>
             
	           
	          </div>
          
	         <div class="col-md-3">
	         
	        <!-- Customer Feedback N-->
			
			<?php if($this->session->userdata('id_branch')==''){?>
			 
			   <div class="info-box bg-red">    <!-- Customer Feedback-->
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Customer Feedback</span>
                  
               <a href="<?php echo base_url('index.php/reports/customer_enquiry'); ?>"><span style="color: #fff !important" class="info-box-number"><?php echo $feedback_count; ?></span></a>
                </div><!-- /.info-box-content -->
              </div><!-- /.i
			  
			<?php }?>
	            
				<!-- Customer Feedback-->
	            
	            <!-- /.info-box --> 
            <!--  <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Schemes</span>
                  
                  <a href="<?php echo base_url('index.php/scheme'); ?>"><span style="color: #fff !important" class="info-box-number"><?php echo $scheme_count; ?></span></a>
                </div>
              </div>-->
			  
			  <?php $data=$this->dashboard_model->scheme_group();
            	if($data['walletIntegration']==1)
            	{?>
			  
			   
               <div class="col-md-12">
            		   <div class="box box-danger">
			                <div class="box-header">
			                  <h3 class="box-title">Dues</h3>
			                  
			               <!--   <div class="pull-right">
		                  	 <a href="<?php echo base_url('index.php/reports/detail/due/TM'); ?>"><span class="label bg-red"><?php $dues=($payment['all_pay_old']['unpaid']-$payment['all_pay_old']['previous_paid']); echo $dues < 0 ? 0 : $dues; ?></span></a>
		                  </div>-->
			                </div><!-- /.box-header -->
			                <div class="box-body no-padding">
			                  <table class="table table-condensed">
			                    <tr>
			                      <th style="width: 50%;">Type</th>
			                      
			                      <th style="width: 50%;text-align: right;">Dues</th>
			                    </tr>
			                    <tr>
			                      <td>This Month</td>
			                      
			                      <td style="text-align: right;"><a href="<?php echo base_url('index.php/reports/detail/due/TM');?>"><span class="badge bg-red"><?php $dues=($payment['all_pay_old']['unpaid']-$payment['all_pay_old']['previous_paid']); echo $dues < 0 ? 0 : $dues; ?></span></a></td>
			                    </tr> 
			                   <!-- <tr>
			                      <td>This Week</td>
			                      
			                      <td style="text-align: right;"><a href="<?php echo base_url('index.php/reports/detail/due/TW');?>"><span class="badge bg-red"><?php echo $due['week_due']['due_count'];?></span></a></td>
			                    </tr> 
			                    <tr>
			                      <td>Cheque/ECS Presentable</td>
			                      
			                       <td style="text-align: right;"><a href="<?php echo base_url('index.php/reports/detail/postdated/pay_status/all/ecs/7');?>"><span class="badge bg-red"><?php echo $pdc['chq_tt_prestable']['payments'];?> / <?php echo  $pdc['ecs_tt_prestable']['payments'];?></span></a></td>
			                    </tr> 
			                    <tr>
			                      <td>Cheque/ECS Presented</td>
			                     
			                      <td style="text-align: right;"><a href="<?php echo base_url('index.php/reports/detail/postdated/pay_status/all/ecs/2');?>"><span class="badge bg-red"><?php echo  $pdc['chq_tt_prestd']['payments'];?> / <?php echo  $pdc['ecs_tt_prestd']['payments'];?></span></a></td>
			                    </tr> -->
			                   		                   
			                  </table>     
			         
			                </div> <!-- /.box-body --> 
			              </div><!-- /.box -->
                         
                 
						  
            	</div>
				
				<?php }?>
			  
			  
			 <?php $data=$this->dashboard_model->scheme_group();
            	if($data['has_lucky_draw']==1)
            	{?>
               <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Schemes Groups</span>
                  
                  <a href="<?php echo base_url('index.php/account/scheme_group/list'); ?>"><span style="color: #fff !important" class="info-box-number"><?php echo $group_count; ?></span></a>
                </div><!-- /.info-box-content -->
              </div>
            	<?php }
               ?>
             </div>
			 
			 
			 
	     </div>
            <!-- </div>-->
			 
			 </div>
			 </div>
        </section><!-- /.content -->
		   <?php }?>
	 	<div class="overlay"  style="display:none">
			<i class="fa fa-refresh fa-spin"></i>
		</div>
      </div> 
      
      </div> 
      
      	  <?php }else if($this->session->userdata('profile') == 7){?>  
      	  <div class="col-md-2">
    			<div class="pull-left">
    				<div class="form-group">
    				   <button class="btn btn-default btn_date_range" id="payment-dt-btn">
    					
    					<span  style="display:none;" id="payment_list1"></span>
    					<span  style="display:none;" id="payment_list2"></span>
    					<i class="fa fa-calendar"></i> Date range picker
    					<i class="fa fa-caret-down"></i>
    					</button>
    				</div>
    			 </div>						
    		</div>	
              	  <div class="row">
                    
        	         <div class="col-md-12">
        
                     	 <div class="box box-solid ">
            
                           <div class="box-header">
                             <h3 class="box-title">Collection</h3><br/>
                                <div align="left">
                                    <button class="btn" id="summary">Collection Summary</button>
                                    <button class="btn" id="detailed">Detailed Collection</button>
                                    <button class="btn" id="print" value="">Print</button>
                                </div>
                                <br/>
                                <table id="collSummary" class="table table-bordered  text-center">
                                     <thead>
                                        <tr>
                                           <th>Branch</th>
                                           <th class="bg-purple">Op Balance (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                           <th class="bg-green">Collection (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                           <th class="bg-orange">Closed (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                           <th class="bg-red">Cancelled (<?php echo $this->session->userdata('currency_symbol') ?>)</th> 
                                           <th class="bg-blue">Closing balance (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                        </tr>
                                     </thead> 
                                </table>
                             	<table style="display:none;" id="collDetail" class="table table-bordered  text-center">
                                 <thead>
                                    <tr>
                                       <th>Branch</th>
                                       <th class="bg-purple">Op Balance (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                       <th class="bg-purple">Op Balance (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                      
                                       <th class="bg-green">Collection (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                       <th class="bg-green">Collection (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                      
                                       
                                       <th class="bg-orange">Closed (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                       <th class="bg-orange">Closed (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                      
                                       
                                       <th class="bg-red">Cancelled (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                       <th class="bg-red">Cancelled (W) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                      
                                       
                                       <th class="bg-blue">Closing balance (A) <?php echo $this->session->userdata('currency_symbol') ?></th>
                                       <th class="bg-blue">Closing balance (W)  <?php echo $this->session->userdata('currency_symbol') ?></th>
                                      
                                    </tr>
                                 </thead> 
                          </table>
                           </div>
                        </div>   
                   </div>
                </div>
      	  <?php } ?>  
     
      </div> 
      </section>
      </div><!-- /.content-wrapper -->
	  
				
	        
<script>
var branch_id = "<?php echo $this->session->userdata('dashboard_branch');?>";
</script>