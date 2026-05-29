  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Re-order Items</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Retail Reports</a></li>
            <li class="active">Re-order Items</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-12">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								<div class="col-md-2"> 
									<div class="form-group">    
										<!--<label>Select Product</label>--> 
										<select id="prod_select" class="form-control" style="width:100%;"></select>
									</div> 
								</div>
								<div class="col-md-2"> 
									<div class="form-group">    
										<!--<label>Select Design</label> -->
										<select id="des_select" style="width:100%;"></select>
									</div> 
								</div>
								
								<div class="col-md-2"> 
									<div class="form-group">    
										<!--<label>Select Sub Design</label> -->
										<select id="sub_des_select" style="width:100%;"></select>
									</div> 
								</div>
								
								<div class="col-md-2"> 
									<div class="form-group">    
									<!--	<label>Select Weight</label> -->
										<select id="wt_select" style="width: 100%;" multiple></select>
									</div> 
								</div>
								
								<div class="col-md-2"> 
									<div class="form-group">    
										<!--<label>Select Size</label> -->
										<select id="select_size" style="width: 100%;"></select>
									</div> 
								</div>
								
								<div class="col-md-1"> 
									<!--<label></label>-->
									<div class="form-group">
										<button type="button" id="branchreorder_items_search" class="btn btn-info">Search</button>   
									</div>
								</div>
								
								<!-- <div class="col-md-1"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="add_to_cart" class="btn btn-primary">+Cart</button>   
									</div>
								</div> -->
								
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
		                 <table id="reorder_item_list"class="table table-striped table-bordered">
		                    <thead>
							  <tr>
							    <th width="2%" rowspan="2">Product</th>
							    <th width="2%" rowspan="2">Design</th>
							    <th width="2%" rowspan="2">Sub Design</th>
							    <th width="2%" rowspan="2">Size</th>
							    <th width="2%" rowspan="2">Weight Range</th>
							    <th colspan="5" style="text-align: center;">SOUTH AVANI</th>
							    <th colspan="5" style="text-align: center;">WEST MASI</th>
							    <th colspan="5" style="text-align: center;">MELUR</th>
							    <th colspan="5" style="text-align: center;">AATHINAM</th>
							  </tr>
							  <tr>
							    <th width="2%">Grs Wt</th>
							    <th width="2%">Net Wt</th>
							    <th width="2%">Min/Max Pcs</th>
							    <th width="2%">Avl Pcs</th>
							    <th width="1%">Short/Excess</th>
							    
							    <th width="2%">Grs Wt</th>
							    <th width="2%">Net Wt</th>
							    <th width="2%">Min/Max Pcs</th>
							    <th width="2%">Avl Pcs</th>
							    <th width="1%">Short/Excess</th>
							    
							    <th width="2%">Grs Wt</th>
							    <th width="2%">Net Wt</th>
							    <th width="2%">Min/Max Pcs</th>
							    <th width="2%">Avl Pcs</th>
							    <th width="1%">Short/Excess</th>
							    
							    <th width="2%">Grs Wt</th>
							    <th width="2%">Net Wt</th>
							    <th width="2%">Min/Max Pcs</th>
							    <th width="2%">Avl Pcs</th>
							    <th width="1%">Short/Excess</th>

							  </tr>
		                    </thead> 
		                        <tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
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
      
      
<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<input  type="hidden" value="0" id="i_increment" />	
				<h4 class="modal-title" id="myModalLabel">Add to Cart</h4>
			    Product  : <b><span id="product_name"></span> | </b>
			    Design  : <b><span id="design_name"></span> | </b>
			    Weight Range : <b><span id="weight_name"></span> | </b>
			    Min Pcs : <b><span id="min_pcs"></span> | </b>
			    Max Pcs : <b><span id="max_pcs"></span> | </b>
			</div>
			<div id="chit_alert" style="width: 92%;margin-left: 3%;"></div>
			<form id="order_cart">
			<div class="modal-body">
			    
			</div>
			</form>
		  <div class="modal-footer">
			<a href="#" id="create_order" class="btn btn-success">Add to Cart</a>
			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>


