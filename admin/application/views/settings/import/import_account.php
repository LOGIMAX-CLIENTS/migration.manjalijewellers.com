
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Import data
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Settings</a></li>
            <li class="active">Import Data</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Import Scheme Data</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div>
               <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?>      
		        <?php echo form_open_multipart('settings/import/account_data');?>
		     <?php if(isset($reject)) {?>   
		      <div class="row">
		      	<div class="col-md-12">
		      	  <div class="pull-right">
		      	  	 <a href="#" target="_blank" class="btn btn-danger">Rejected list</a>
		      	  </div>
		      	</div>
		      </div>
		     <?php }?> 
		   
				  <div class="callout callout-danger">
	                    <h5><i class="icon fa fa-warning"> </i> <b>NOTE</b></h5>
	                      <ol>
	                      	<li>Donot leave empty rows inbetween records.</li>
	                      	<li>Follow the below mentioned excel format.</li>
	                      	<li>Reference No. should not be repeated.</li>
	                      	<li>Scheme codes in excel file should match with your existing scheme codes.</li>
	                      </ol>
				 </div>	
				
				 <div class="row" style="display: none;">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Scheme Type</label>
                       <div class="col-md-4">
                       	 <input type="checkbox" class="form-control" id="import_type" name="import[type]" data-on-text="Weight"  data-off-text="Amount" value="1"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>  
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Select the Excel file</label>
                       <div class="col-md-4">
                       <input type="file" class="form-control"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="import_file" />
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				     <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">First row</label>
                       <div class="col-md-4">
                       	<label><input type="checkbox"  id="is_heading" name="import[is_heading]"  value="1" checked> 
                       	Is Heading
                       	</label> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>     
				<div class="row" style="display: none;">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Send SMS</label>
                       <div class="col-md-4">
                       	 <input type="checkbox"  id="send_sms" name="import[send_sms]"  data-on-text="YES" data-off-text="NO" value="1"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				  <div class="row">
				  	<div class="col-md-12">
				  	  <label>Excel Format</label>
				  	  <div class="form-group">
				  		<div id="excel_format"></div>
				  	 </div> 	
				  	</div>
					<div class="callout ">
	                    <h5><b>Instructions</b></h5>
	                      <ul>
	                      	<li>Select <b>date</b> format in your excel sheet for columns Start date and Last paid date.</li>
	                      	<li>Date format should be <b>mm/dd/yyyy</b>.  Ex:01/22/2016.</li>
	                      	<li>Mention <b>'Yes'</b> for new customer and  <b>'No'</b> for existing customer in <b>Is new Customer</b> column.</li>
	                      	
	                      </ul>
				 </div>	
				  </div>
				   
				  <div class="row">
				  <div class="col-md-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-4">
						<button type="submit" class="btn btn-primary">Import</button> 
						<button type="button" class="btn btn-danger btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
					</div>
				  </div> 
			
					
				</form>	
					
					
					
				 </div>
			
			  
			  
            </div><!-- /.box-body -->
            <div class="box-footer">
           
            </div><!-- /.box-footer-->
          </div><!-- /.box -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
