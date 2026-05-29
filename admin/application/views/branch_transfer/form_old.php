      <!-- Content Wrapper. Contains page content -->
    <style>
    	.remove-btn{
			margin-top: -168px;
		    margin-left: -38px;
		    background-color: #e51712 !important;
		    border: none;
		    color: white !important;
		}
		.custom-bx{
			box-shadow: none;
			border: 0.5px solid #e1e1e1;
		}
    </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Inventory
            <small>Branch Transfer</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Inventory</a></li>
            <li class="active">Branch Transfer</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add to Branch Transfer</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body"> 
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
	             <!-- form -->
				<?php  echo form_open_multipart(""); ?>	
				<div class="row">
					<div class="col-md-offset-1 col-md-3">
						<div class="form-group">
		                  <label for="">Type <span class="error"> *</span></label>
		                  <div class="form-group"> 
		                      <input type="radio" name="transfer_item_type" id="type1" value="1" checked> Tagged  &nbsp;&nbsp;
		                      <input type="radio" name="transfer_item_type" id="type2" value="2"> Non Tagged 
			              </div>
		                </div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
		                  <label for="">From Branch <span class="error"> *</span></label>
		                  <select class="form-control ret_branch" id="from_brn" required></select>
		                </div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
		                  <label for="">To Branch <span class="error"> *</span></label>
		                  <select class="form-control" id="to_brn" required></select>
		                </div>
					</div> 
				</div>
				<p class="help-block"></p> 

			    <div class="row">
			        <div class="col-md-offset-1 col-md-10">
			          <div class="box box-default custom-bx">
			            <!--<div class="box-header with-border">
			              <h3 class="box-title">Progress Bars Different Sizes</h3>
			            </div>-->
			            <!-- /.box-header -->
			            <div class="box-body">
			               <div class="row">
			               	   <div class="col-md-5">
									<div class="form-group"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Lot No</label> 
											</div>
											<div class="col-md-7">
												<select class="form-control" id="lotno"></select>
											</div>
										</div> 
									</div>
									<div class="form-group"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Product</label> 
											</div>
											<div class="col-md-7">
												<input type="text" class="form-control" id="product" placeholder="Product Name/Code" autocomplete="off">
												<input type="hidden" class="form-control" id="id_product">
												<span class="prodAlert"></span>
											</div>
										</div> 
									</div>
									<!--<div class="form-group"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Sub Product</label> 
											</div>
											<div class="col-md-7">
												<input type="text" class="form-control" id="sub_product" placeholder="Sub Product Name/Code">
												<input type="hidden" class="form-control" id="id_sub_product">
											</div>
										</div> 
									</div>-->
									<div class="form-group tagged"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Design</label> 
											</div>
											<div class="col-md-7">
												<input type="text" class="form-control" id="design" placeholder="Design"  autocomplete="off">
												<input type="hidden" class="form-control" id="id_design">
											</div>
										</div> 
									</div>
									<!--<div class="form-group"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Karigar</label> 
											</div>
											<div class="col-md-7">
												<select class="form-control" id="karigar"></select>
											</div>
										</div> 
									</div>-->
								</div> 
								<div class="col-md-6">
									<div class="form-group tagged"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Tag No</label> 
											</div>
											<div class="col-md-7">
												<input type="text" class="form-control" id="tag_no" placeholder="Tag No"  autocomplete="off">
											</div>
										</div> 
									</div>
									<div class="form-group tagged"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Tag Date Range</label> 
											</div>
											<div class="col-md-7">
												<input type="text" class="form-control pull-right dateRangePicker" id="tag_dt_rng" placeholder="Tag From Date - Tag To Date">
											</div>
										</div> 
									</div>
									<div class="form-group"> 
										<div class="row">
											<div class="col-md-5 ">
												<label for="" class="control-label pull-right">Lot Date Range</label> 
											</div>
											<div class="col-md-7">
												<input type="text" class="form-control pull-right dateRangePicker" id="lot_dt_rng" placeholder="Lot From Date - Lot To Date">
											</div>
										</div> 
									</div>
									<button type="button" class="btn btn-info btrn_search pull-right">Search</button>   
						        </div>
								<!-- ./col -->
			               </div>
			            </div>
			            <!-- /.box-body -->
			          </div>
			          <!-- /.box -->  
					</div>
				</div>
				<div class="row non_tagged " style="display: none">
					<div class="col-md-12"> 
						<p class="page-header">
							Non Tagged Search Result :
							 
					   </p>
					   <div class="table-responsive">
		                 <table id="bt_nt_search_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
		                        <th><label class="checkbox-inline"><input type="checkbox" id="nt_select_all" name="nt_select_all" value="all"/>All</label></th>   
	                          	<th width="10%">Lot No</th>   
	                          	<th width="20%">Product</th>  
	                          	<th width="10%">Lot Date</th>  
	                          	<th width="10%">Pcs</th>  
	                          	<th width="20%">G.wt</th>  
	                          	<th width="20%">N.wt</th>  
		                      </tr>
		                    </thead>
		                    <tfoot>
		                    	<tr>
		                    		<th colspan="4">Total</th>
		                    		<td><input type="text" class="nt_pieces" disabled="true" placeholder="Pieces"/></td>
		                    		<td><input type="text" class="nt_grs_wt" disabled="true" placeholder="Gross Weight"/></td>
		                    		<td><input type="text" class="nt_net_wt" disabled="true" placeholder="Net Weight"/></td>
		                    	</tr>
		                    </tfoot> 
		                 </table>
	                  </div>
					</div> 
				</div>	
				<p class="help-block"></p>
				<div class="row tagged">
					<div class="col-md-12"> 
						<p class="page-header">
							Tagged Item Search Result :
							 
					   </p>
					   <div class="table-responsive">
		                 <table id="bt_search_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
		                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>   
	                          	<th>Tag No</th>  
	                          	<th>Lot No</th>   
	                          	<th>Product</th>  
	                          	<th>Design</th>  
	                          	<th>Tag Date</th>  
	                          	<th>Pcs</th>  
	                          	<th>G.wt</th>  
	                          	<th>N.wt</th>  
		                      </tr>
		                    </thead>
		                    <tfoot>
		                    	<tr>
		                    		<th colspan="6">Total</th>
		                    		<td><input type="text" class="pieces" disabled="true" placeholder="Pieces"/></td>
		                    		<td><input type="text" class="grs_wt" disabled="true" placeholder="Gross Weight"/></td>
		                    		<td><input type="text" class="net_wt" disabled="true" placeholder="Net Weight"/></td>
		                    	</tr>
		                    </tfoot> 
		                 </table>
	                  </div>
					</div> 
				</div>	
				<p class="help-block"></p>
				<!--End of row-->
				<div class="row tagged">
					<div class="col-md-12"> 
						<p class="page-header">
							Preview :
					   </p>
					   <div class="table-responsive">
		                 <table id="bt_list" class="table table-bordered table-striped text-center">
		                    <thead>
		                      <tr>
		                        <th>#</th>  
	                          	<th>Tag No</th>  
	                          	<th>Lot No</th>   
	                          	<th>Product</th>  
	                          	<th>Design</th>  
	                          	<th>Tag Date</th>  
	                          	<th>Pcs</th>  
	                          	<th>G.wt</th>  
	                          	<th>N.wt</th>  
		                      </tr>
		                    </thead> 
		                 </table>
	                  </div>
					</div> 
				</div>	
				<!--End of row-->  
				     
			     <div class="row">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
					  	<button id="add_to_transfer" type="button" class="btn btn-success"><i class="fa fa-plus"></i> Add To Transfer</button>
						<button type="button" class="btn btn-default btn-cancel">Back</button>
					  </div> <br/>
					</div>
				  </div> 
			    
	            </div>  
	            <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
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
  