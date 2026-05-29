<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
	  <li class="active"><a href="#flexible_3" data-toggle="tab">Flexible</a></li>
	  <li ><a href="#fixed_amt_0" data-toggle="tab">Fixed Amount</a></li>
	  <li><a href="#weight_1" data-toggle="tab">Weight</a></li>
	  <li><a href="#amt_to_wgt_2" data-toggle="tab">Amount To weight</a></li>
	  <li><a href="#flexible_otp" data-toggle="tab">One Time Premium</a></li>
	</ul>
	<div class="tab-content">
	  <div class="active tab-pane" id="flexible_3">
	    <?php $this->load->view("master/crm_scheme/flexible"); ?>
	  </div>
	  <div class=" tab-pane" id="fixed_amt_0">
	    <?php $this->load->view("master/crm_scheme/fixed_amt"); ?>
	  </div>
	  <div class="tab-pane" id="weight_1">
	    <?php $this->load->view("master/crm_scheme/weight"); ?>
	  </div>
	  <div class="tab-pane" id="amt_to_wgt_2">
	    <?php $this->load->view("master/crm_scheme/amt_to_wgt"); ?>
	  </div>
	  <div class="tab-pane" id="flexible_otp">
	  	<!--It comes under Flexible scheme for user friendly purpose separated-->
	    <?php $this->load->view("master/crm_scheme/one_time_premium"); ?>
	  </div>
	</div>
	<!-- /.tab-content -->
</div>
<!-- /.nav-tabs-custom -->