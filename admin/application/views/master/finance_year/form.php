      <!-- Content Wrapper. Contains page content -->
    <style>
    	.remove-btn{
			margin-top: -168px;
		    margin-left: -38px;
		    background-color: #e51712 !important;
		    border: none;
		    color: white !important;
		}
    </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
        	Financial Year
            <small>Tag</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Financial Year</a></li>
            <li class="active">Financial Year</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Financial Year</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div class="row">
	             <!-- form -->
				<?php echo form_open_multipart(( $finance['fin_id'] != NULL && $finance['fin_id'] > 0 ? 'admin_ret_catalog/financial_year/update/'.$finance['fin_id']:'admin_ret_catalog/financial_year/save')); ?>
				<?php 
					$metal_rates=$this->admin_settings_model->metal_ratesDB("last");	
				?>
				<div class="col-sm-12"> 
					<legend class="sub-title">Financial Year Details</legend>
						 	<div class="row">				    	
						 		<label for="country" class="col-md-4 col-md-offset-1">Financial Name </label>
						 		<div class="col-md-4 ">
						 			<div class="form-group">
							 			<div class="input-group ">
											<input class="form-control" id="fin_year_name" name="finance[fin_year_name]" type="text"  placeholder="Enter Financial Code"  value="<?php echo set_value('finance[fin_code]',isset($finance['fin_code'])?$finance['fin_code']:NULL); ?>" autocomplete="off"/>
											<span id="designAlert"></span>
										</div>
									</div>
						 		</div>
						 	</div>
						 	<div class="row">				    	
						 		<label for="country" class="col-md-4 col-md-offset-1">Financial Year Code </label>
						 		<div class="col-md-4 ">
						 			<div class="form-group">
							 			<div class="input-group ">
											<input class="form-control" id="fin_year_code" name="finance[fin_year_code]" type="text" maxlength="4" placeholder="e.g. 26 (max 4 digits)" value="<?php echo set_value('finance[fin_year_code]',isset($finance['fin_year_code'])?$finance['fin_year_code']:NULL); ?>" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,4)" pattern="[0-9]{1,4}" title="Only numeric values allowed, max 4 digits (e.g. 25, 26)"/>
										</div>
									</div>
						 		</div>
						 	</div>
						 	<div class="row">				    	
						 		<label for="country" class="col-md-4 col-md-offset-1">From Date</label>
						 		<div class="col-md-4 ">
						 			<div class="form-group">
							 			<div class="input-group ">							<input type='text' id='fin_year_from' name="finance[fin_year_from]" value="<?php echo set_value('finance[fin_year_from]',isset($finance['fin_year_from'])?$finance['fin_year_from']:NULL); ?>">
										</div>
									</div>
						 		</div>
						 	</div>
						 	<div class="row">				    	
						 		<label for="country" class="col-md-4 col-md-offset-1">To Date </label>
						 		<div class="col-md-4 ">
						 			<div class="form-group">
							 			<div class="input-group ">
											<input type='text' id='fin_year_to' name="finance[fin_year_to]" value="<?php echo set_value('finance[fin_year_to]',isset($finance['fin_year_to'])?$finance['fin_year_to']:NULL); ?>">
											<span id="designAlert"></span>
										</div>
									</div>
						 		</div>
						 	</div>
			
				 	<p class="help-block"></p>			 
				</div>	<!--/ Col --> 
			</div>	 <!--/ row -->
				 			  	  
			     <div class="row">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" id="finance_submit"  class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div> 
			      
	            </div>  
	          <?php echo form_close();?>
	            <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

	             <!-- /form -->
	          </div>
             </section>
     
            </div>
            
