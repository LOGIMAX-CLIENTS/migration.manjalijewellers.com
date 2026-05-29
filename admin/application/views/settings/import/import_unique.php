
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
            <li><a href="#">Import</a></li>
            <li class="active">Unique</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Import Unique Code</h3>
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
		        <?php echo form_open_multipart('settings/import/unique_data');?>
	
				 
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
			
				  <div class="row">
				  	<div class="col-md-12">
				  	  <label>Excel Format</label>
				  	  <div class="form-group">
				  		<div id="excel_pay_format"></div>
				  	 </div> 	
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
