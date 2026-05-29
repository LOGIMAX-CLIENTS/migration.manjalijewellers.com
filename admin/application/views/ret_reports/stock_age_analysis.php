  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Stock Age Analysis</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Stock age</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Stock Age Analysis Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                  <div class="row">
				  <div class="col-md-offset-1 col-md-10">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						<div class="row"> 
							<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
        		                  <div class="col-md-3"> 
        		                     <div class="form-group tagged">
        		                       <label>Select Branch</label>
        									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
        		                     </div> 
        		                  </div> 
        						    <?php }else{?>
        		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
        		                  <?php }?>  
							<div class="col-md-2">
								<div class="form-group">
									<label>Select Metal</label> 
									<select class="form-control" id="metal"></select> 
								</div> 
							</div> 
							<div class="col-md-2">
								<div class="form-group">
									<label>Category</label> 
									<select id="category" class="form-control"></select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
									<label>Product</label> 
									<select id="prod_select" class="form-control"></select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
									<label>Based on</label> 
									<select id="age_type" class="form-control" style="width:100%;">
										<option value="1" selected>Tag Date</option>
										<option value="2">Received Date</option>
									</select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
								    <br>
								 <button type="button" id="stockAge_search" class="btn btn-info">Search</button>   
								</div>  
							</div> 
						
					   </div>
					   
	                   </div> 
	                  </div> 
                   </div> 
                
                </div>
                <p></p>
                
				   <div class="row">
						<div class="col-xs-12">
						<!-- Alert -->
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
						</div>
				   </div>
			  
                  <div class="table-responsive">
	                 <table id="stock_age_list" class="table table-bordered table-striped text-center">
	                    <thead>
						  <tr style="text-align: left;">
						    <th  style="text-align: left;" width="5%">ID</th>  
						    <th  style="text-align: left;" width="20%">Product</th>  
                            <th  style="text-align: right;" width="15%">Below 120 Days </br>Wt / Pcs</th> 
                            <th  style="text-align: right;" width="15%">Above 120 Days </br>Wt / Pcs</th> 
                            <th  style="text-align: right;" width="15%">Above 180 Days </br>Wt / Pcs</th> 
                            <th  style="text-align: right;" width="15%">Above 240 Days </br>Wt / Pcs</th> 
                            <th  style="text-align: right;" width="15%">Above 365 Days </br>Wt / Pcs</th> 
						  </tr>
	                    </thead> 
	                    
	                 </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

