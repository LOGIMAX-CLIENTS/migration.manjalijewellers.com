<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
	    Scheme 
	    <small>Manage scheme</small>
	 </h1>
	  <ol class="breadcrumb">
	    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	    <li><a href="#">Masters</a></li>
	    <li class="active">Scheme</li>
	  </ol>
	</section>
	<!-- Main content -->
	<section class="content">
	  <!-- Default box -->
	  <div class="box">
	    <div class="box-header with-border">
	      <h3 class="box-title">Scheme Master</h3>
	      <div class="box-tools pull-right">
	        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
	        <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
	      </div>
	    </div>
	    <div class="box-body">
	      <div class="row">
	      	<div class="col-md-12">
      			<ul class="nav nav-pills nav-stacked col-md-1">
		  			<li><a href="#tab_basic" data-toggle="pill">Basic</a></li>
		  			<li   class="active"><a href="#tab_plan_sett" data-toggle="pill">Plan Settings</a></li>
		  			<!--<li><a href="#tab_maturity" data-toggle="pill">Maturity</a></li>-->
		  			<li><a href="#tab_pay_sett" data-toggle="pill">Payable Settings</a></li>
		  			<li><a href="#tab_pay_chances" data-toggle="pill">Payment chances</a></li>
		  			<li><a href="#tab_benefit" data-toggle="pill">Benefit</a></li>
		  			<li><a href="#tab_preclose" data-toggle="pill">Pre-close</a></li>
		  			<li><a href="#tab_incentive" data-toggle="pill">Incentive</a></li>
		  			<li><a href="#tab_kyc" data-toggle="pill">KYC</a></li>
		  			<!--<li><a href="#tab_gst" data-toggle="pill">GST</a></li>-->
		  			<li><a href="#tab_lucky_draw" data-toggle="pill">Lucky Draw</a></li>
		  			<li><a href="#gen_advance" data-toggle="pill">General Advance</a></li>
		  			<li><a href="#tab_noti" data-toggle="pill">Notification</a></li>
		  			<li><a href="#tab_desc" data-toggle="pill">Description</a></li>
		  			<li><a href="#tab_sch_type" data-toggle="pill"><strike>Scheme Type Old</strike></a></li> 
		        </ul>
		        <div class="tab-content col-md-11">
	              <div class="tab-pane " id="tab_basic">
	                <?php $this->load->view("master/crm_scheme/basic"); ?>
	              </div>
	              <div class="tab-pane " id="tab_sch_type">
	                <?php  $this->load->view("master/crm_scheme/sch_type"); ?>
	              </div>
	              <div class="tab-pane active" id="tab_plan_sett">
	                <?php $this->load->view("master/crm_scheme/plan_settings"); ?>
	              </div>
	              <!--<div class="tab-pane" id="tab_maturity">
	                <?php //$this->load->view("master/crm_scheme/maturity"); ?>
	              </div>-->
	              <div class="tab-pane " id="tab_pay_sett">
	                <?php $this->load->view("master/crm_scheme/payable_settings"); ?>
	              </div>
	              <div class="tab-pane" id="tab_benefit">
	                <?php $this->load->view("master/crm_scheme/benefit.php"); ?>
	              </div>
	              <div class="tab-pane" id="tab_preclose">
	                <?php $this->load->view("master/crm_scheme/preclose"); ?>
	              </div>
	              <div class="tab-pane" id="tab_incentive">
	                <?php $this->load->view("master/crm_scheme/incentive.php"); ?>
	              </div>
	              <div class="tab-pane" id="tab_kyc">
	                <?php $this->load->view("master/crm_scheme/kyc.php"); ?>
	              </div>
	              <!--<div class="tab-pane" id="tab_gst">
	              	<?php //$this->load->view("master/crm_scheme/gst.php"); ?>
	              </div>-->
	              <div class="tab-pane" id="tab_lucky_draw">
	              	
	              	<?php $this->load->view("master/crm_scheme/lucky_draw.php"); ?>
	              </div>
	              <div class="tab-pane" id="tab_pay_chances">
	              	
	              	<?php $this->load->view("master/crm_scheme/payment_chances.php"); ?>
	              </div>
	              <div class="tab-pane" id="gen_advance">
	              	
	              	<?php $this->load->view("master/crm_scheme/gen_advance.php"); ?>
	              </div>
	              <div class="tab-pane" id="tab_noti">
	              	<div class="row">
						<div class="col-md-12">
					      <div class="box box-default box-solid">
					        <div class="box-header with-border">
					          <h3 class="box-title">Notification</h3>
					        </div>
					        <div class="box-body">
					          <textarea class="form-control" placeholder="Enter Notification Content" name="sch[noti_msg]" id="noti_msg" cols="35" rows="5" tabindex="4"></textarea>
					          
					        </div><!-- /.box-body -->
					      </div><!-- /.box -->
					    </div>    
					</div>
	              </div>
	              <div class="tab-pane" id="tab_desc">
	              	<div class="row">
						<div class="col-md-12">
					      <div class="box box-default box-solid">
					        <div class="box-header with-border">
					          <h3 class="box-title">Description - Plan Terms & Conditions</h3>
					        </div>
					        <div class="box-body">
					            Select language
					          <textarea class="form-control" placeholder="Enter Content" name="sch[noti_msg]" id="noti_msg" cols="35" rows="5" tabindex="4"></textarea>
					          
					        </div><!-- /.box-body -->
					      </div><!-- /.box -->
					    </div>    
					</div>
	              </div>
	            </div>
      		</div>
	      </div>
	      
	    </div><!-- /.box-body -->
	    <div class="box-footer">
	    </div><!-- /.box-footer-->
	  </div><!-- /.box -->
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->   