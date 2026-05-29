
    <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>EMAIL
            <small>Send group mail</small>
          </h1>
          <ol class="breadcrumb">
             <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Email</a></li> 
            <li class="active">Group Mail </li>
            
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Compose group mail</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
            <div class="col-md-12">
				  
				  <?php 
				    $attributes = array('autocomplete' => "off",'role'=>'form');
		    		// echo form_open( '' , $attributes); 
				  //form validation
				    if(validation_errors())
				    {
						echo '<div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Warning!</h4>    <strong>'.validation_errors().'</strong>              
            </div>';
					} 
					?>
					<div class="row">
						<div class="col-sm-10 col-sm-offset-1">
						<div id="chit_alert"></div>
						 
						</div>
					</div> 
					<p style="height:20px"></p>				
						<div class="row">	
							<div class="form-group">
							 
              					<label for="" class="control-label col-sm-2 col-xs-offset-1">Send Email To<span class="error">*</span></label>
									<div class="col-sm-4">
	              				    <select id="send_email_to" class="form-control" style="width: 100%;">
	              				        <option value="">-- Select --</option>
										<option value="all_cus">All Customers</option>
										<option value="sel_cus">Selected Customers</option>
										<option value="sch_cus">Scheme Group Customers</option>
									</select>
									<span class="help-block"> </span>
									</div>
								
							  </div>
						</div>		
						<div class="row">	
							<div class="form-group">
							 
              					<label for="" class="control-label col-sm-2 col-xs-offset-1">Scheme Name <span class="error">*</span></label>
									<div class="col-sm-4">
	              				    <select id="scheme" class="form-control" style="width: 100%;"></select>
									<input id="scheme_val" name="group[id_scheme]" type="hidden" value="<?php //echo set_value('scheme[id_customer]',$scheme['id_customer']); ?>" />
	              				       <span class="help-block">Select the scheme</span>
									</div>
								
							  </div>
						</div>
			      <!--     <div class="form-group">
                    <input class="form-control" name="mail[to]" placeholder="To:">
                  </div>-->
                  <div class="form-group">
                    <input class="form-control" id="subject" name="mail[subject]" placeholder="Subject:">
                  </div>
                  <div class="form-group">
                    <textarea id="compose-textarea" name="mail[message]" placeholder="Message:" class="form-control" style="height: 300px">
                
                    </textarea>
                  </div>
			
								
								<div class="row">
									<div class="form-group">
									 <label class="control-label col-sm-2"></label>
									<div class="col-sm-3">
									</div>
									<div class="col-sm-3">
								
              				</div>
									 
									</div>
								</div>
						</div>	
						
				 <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Customer List</h3>      
                  <div class="pull-right">
                  	 
                  </div>       
                </div><!-- /.box-header -->
                <div class="box-body">
                   <div class="table-responsive">
	                 <table id="customer_lists" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th><label class='checkbox-inline'><input type='checkbox'  id="select_all" />ID</label></th>
	                        <th>Name</th>
	                        <th>Mobile</th>    
	                        <th>Email</th>                        
	                      </tr>
	                    </thead> 
	                 </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
				  
					 <div class="row">
						   <div class="col-sm-12">
						   <div class="box box-default"><br/>
							  <div class="col-xs-offset-5">
								<button type="submit" id="btn-send-email"  class="btn btn-primary">Send</button> 
								<button type="button" class="btn btn-default btn-cancel">Cancel</button>
							  </div> <br/>
							</div>
							</div>
					  </div> 
                  </div><!-- /.box-body -->
			
            <div class="box-footer">
            
            </div><!-- /.box-footer-->
           
           
           
          </div><!-- /.box -->
          

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->