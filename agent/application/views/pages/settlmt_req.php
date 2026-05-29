<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<div class="main-container">
<!-- main -->		  
<div class="main">
  <!-- main-inner --> 
  <div class="main-inner">
     <!-- container --> 
    <div class="container dashboard">
      <div align="center"><legend class="head" style="margin-left: 16px;">Settlement</legend></div>    
      <div class="row">
        <div class="col-md-12">
    		<?php
    		if($this->session->flashdata('successMsg')) { ?>
    			<div class="alert alert-success" align="center">
    			  <button type="button" class="close" data-dismiss="alert">&times;</button>
    			  <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
    			</div>      
    		<?php } if($this->session->flashdata('errMsg')) { ?>							 
    			<div class="alert alert-danger" align="center">
    			  <button type="button" class="close" data-dismiss="alert">&times;</button>
    			  <strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
    			</div>
    		<?php } ?>
		 </div>
        </div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="my-stat">
	                <ul class="nav nav-pills nav-pills1">
                        <li class="active col-md-4"><a href="#pending_settlement" data-toggle="tab">PENDING SETTLEMENT</a></li>
                        <li class="col-md-4"><a href="#settlement_req_status" id="settlement_req_status" data-toggle="tab">REQUESTED SETTLEMENT STATUS</a></li>
                    </ul>
			    </div> 
                <div class="tab-content">
                    <div class="tab-pane active" id="pending_settlement">
                        <!--<div class="widget-header">Pending Settlement</div>-->
                        <div class="widget-content">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div id="bill"></div>
                                    </div> 	
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="settlement_req_status">
                        <!--<div class="widget-header">Settlement Request Status</div>-->
                        <div class="widget-content">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div id="list"></div>
                                    </div> 	
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->		  
</div>
</div><br /><br />