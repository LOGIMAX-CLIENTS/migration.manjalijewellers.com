  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Sync Settled payments
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Payment</a></li>
            <li class="active">Payment List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Fetch Gateway Settled Payments</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    
	           <form id="sync_settled_payments"> 	           	 
				 	 <br /> 
	            	 <div class="row">
	            	     	<?php if($this->session->userdata('branch_settings')==1){?>	
						<div class="col-md-3">							
							<div class="form-group">
								<label class="control-label col-sm-3" for="frm_date">Branch &nbsp;&nbsp;</label>
								<select id="branch_select" name="id_branch" class="form-control" style="width:150px;" ></select>
								<!--<input id="id_branch" name="id_branch" type="hidden" value=""/>-->
							</div>							
						</div>
						<?php }else{?>
							<input id="id_branch" name="id_branch" type="hidden" value=""/>
						<?php }?>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label col-sm-3" for="frm_date">Gateway</label>
								<div class="col-sm-8"> 
								   <select name="id_gateway" id="id_gateway">
								   	<option value="4">Cash Free</option>
								   	<option value="1">Payu</option>
								   	<option value="2">CCAvenue</option>
								   	<option value="3">Tech Process</option>
								   </select>
								</div>   
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label col-sm-4" for="frm_date">Settlement Date</label>
								<div class="col-sm-8"> 
								   <input class="form-control myDatePicker"  data-date-format="dd-mm-yyyy" id="request_date" name="request_date" value="" required="true" type="text" readonly/>
								</div>   
							</div>
						</div> 
						<div class="col-md-3">
						<?php if($access['add']==1){?>
		                	<button class="btn btn-primary" type="button" id='fetch_settlement'>Fetch Settlement Details </button>
							<?php }?>
						</div>
	               	 </div>
               	 </form>
               	 <br />               
	           	 <div id='error-msg'></div>	   
	           		<div class="box">
		                <div class="box-header">
						
		                  <h3 class="box-title">Sync Gateway Settled Payments</h3><span class="badge bg-" id="total_settlement"></span>
						  <?php if($access['edit']==1){?>
						  <button class="btn btn-primary pull-right" type="button" id='sync_settled_pay'><i class="fa fa-retweet"></i> Update Payments </button>
						  <?php }?>
						</div><!-- /.box-header -->
		                <div class="box-body">	
		                <div id='alert-msg'></div>		           		
		           		<br />
					  <div class="table-responsive">
	                  <table id="settled_txns" class="table table-bordered table-striped table-condensed text-center">
	                    <thead>
	                      <tr>
	                        <!--<th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>-->
	                        <th>Trans ID</th>	
	                        <th>Payu ID</th>                              					
							<th>Amount</th>         	                        	
							<th width="100px">Paid Date</th>
							<th>Settlement Status</th>        
	                      </tr>
	                    </thead>
	                    <tbody>
	                    </tbody>
	                 <tfoot>
	                    </tfoot> 
	                  </table> 
	                  </div>
	                  </div>
                  </div>
                   <div class="callout callout-info" style="display: block">
                    <h4><i class="icon fa fa-warning"></i> Note </h4>
                      <ol>
                      	  <li> <b>Fetch Settlement Details </b> will retrieve Settlement Details for the merchant.</li>
                    	  <li><b>Settlement Date</b> is the date for which Settlement Details are required.</li>
                    	  <li>Settled payment list will be shown in <a href="<?php echo base_url('index.php/online/payment');?>">Payment Approval</a> page.</li><br/>
                    	 
                      </ol>
                  </div>
                </div>
                 <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div>
            </div>
        </section>
      </div>

<style type="text/css">
.popover1{
    width:230px;
    height:330px;    
}
.trans tr{
	 width:50%;
    height:50%;
	font-size:15px;
	
}
</style>