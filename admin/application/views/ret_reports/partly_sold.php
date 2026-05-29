  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Tag(s) Partly sold</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Partly Sold</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Partly Sold List</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-offset-2 col-md-8">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								<div class="col-md-3"> 
									<div class="form-group tagged">
										<label>Select Branch</label>
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
								</div> 
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
								<?php }?> 
								<div class="col-md-3"> 
									<div class="form-group">
										<label>Product</label> 
										<input type="text" class="form-control" id="product" name="product" placeholder="Enter Product Name/Code" autocomplete="off"/>
										<input type="hidden" class="form-control" id="id_product" name="id_product">
										<div  id="prodAlert" name=""></div>
									</div> 
								</div>
								<div class="col-md-3"> 
									 <div class="form-group">

            		                    <div class="input-group">

            		                        <br>

            		                       <button class="btn btn-default btn_date_range" id="rpt_date_picker">

            							    

            		                        <i class="fa fa-calendar"></i> Date range picker

            		                        <i class="fa fa-caret-down"></i>

            		                      </button>

										  <span  style="display:none;" id="rpt_from_date"></span>

            							<span  style="display:none;" id="rpt_to_date"></span>

										  <span id="reportrange"></span>

            		                    </div>

            		                 </div><!-- /.form group -->

								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="partlySold_search" class="btn btn-info">Search</button>   
									</div>
								</div>
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div> 
                
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
			  
                   <div class="row">
	                   <div class="col-md-12">
	                   	<div class="table-responsive">
		                 <table id="partlysold_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
							  	<th colspan="7"></th>
							  	<th colspan="3">Stock</th>
								<th colspan="3">Sold (g)</th>
								<th colspan="4">Balance (g)</th>
		                      </tr>
							  <tr>
							    <th width="10%">Branch</th>
							    <th width="10%">Bill Date</th>
							    <th width="10%">Bill No</th>
							    <th width="5%">Tag ID</th>
							     <th width="5%"></th> 
								  <th width="10%">Old Tag ID</th>   
							    <th width="15%">Product</th>                                     
							    <th width="15%">Design</th> 
							                                        
		                        <th width="5%">Pieces</th>   
		                        <th width="10%">Gross Wt</th>   
	                            <th width="10%">Net Wt</th> 
								
								<th width="5%">Pieces</th>   
								<th width="10%">Gross Wt</th>   
	                            <th width="10%">Net Wt</th> 
								                                   
		                        <th width="5%">Pieces</th>  
		                        <th width="10%">Gross Wt</th>  
	                            <th width="10%">Net Wt</th> 
							  </tr>
		                    </thead> 
							   <tfoot>
		                    	<tr>
		                    	    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		                    	</tr>
		                    </tfoot>
		                 </table>
	                  </div>
	                   </div>
                   </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

