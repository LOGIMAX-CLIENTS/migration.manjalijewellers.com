  <!-- Content Wrapper. Contains page content -->
  <style>
  	.custom-label{
		font-weight: 400;
	}
  </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Tagging
            <small>Manage your tag(s)</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Tagging</a></li>
            <li class="active">Tag</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Tagging List</h3>  <span id="total_tagging" class="badge bg-green"></span>  
                  <!--<div class="pull-right">
                     <a class="btn btn-warning" id="add_tagging" href="<?php echo base_url('index.php/admin_ret_tagging/tagging/bulk_edit');?>" ><i class="fa fa-edit"></i>Bulk Edit</a>
                  	 <a class="btn btn-success" id="add_tagging" href="<?php echo base_url('index.php/admin_ret_tagging/tagging/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
				  </div>-->
                </div>
                 <div class="box-body">  
                   <div class="row">
					   <div class="form-group">
						  <div class="col-md-2">
							<div class="pull-left">
							    <div class="form-group"> 
								<button class="btn btn-default btn_date_range" id="tag-dt-btn">
								<span  style="display:none;" id="tag_date1"></span>
								<span  style="display:none;" id="tag_date2"></span>
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
								</button>
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
				    	 <div class="col-md-2">                  
                               <div class="form-group">
                               		<button class="btn btn-warning" id="print_all"><i class="fa fa-print"></i>Print All
                               		</button>
                               </div>
                          </div>
                          <div class="col-md-2">                  
                               <div class="form-group">
                               		<button class="btn btn-success" id="tag_print"><i class="fa fa-print"></i>Selected Print
                               		</button>
                               </div>
                          </div>
                          <div class="col-md-2">                  
                               <div class="form-group">
                                    <label>Select Employee</label>
                               		<select class="form-control" id="emp_select"></select>
                               		<input type="hidden" id="id_employee" value="">
                               		</button>
                               </div>
                          </div>
                         <!-- <div class="col-md-2">                  
                               <a class="btn btn-primary"  href="<?php echo base_url('index.php/admin_ret_tagging/tagging/duplicate_print');?>" ><i class="fa fa-print"></i>Duplicate Print</a>
                          </div>-->
				    </div>
                  <div class="table-responsive">
	                 <table id="tagging_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
							<th width="5%">S.NO</th>
	                        <th width="5%"><label class="checkbox-inline"></label> ID </th>
							<th width="10%">Lot No.</th>
							<th width="10%">PO No.</th>
							<th width="5%">Kargiar</th>
	                        <th width="10%">Image</th>
	                        <th width="10%">Tag Code</th>
	                        <th width="10%">Old Tag Code</th>
	                        <th width="10%">Tag Date</th>
							<th width="5%">Status</th>
							<th width="5%">Category</th>
							<th width="10%">Purity</th>
	                        <th width="10%">Product</th>
	                        <th width="10%">Design</th>
	                        <th width="10%">Sub Design</th>
	                        <th width="10%">Section</th>
	                        <th width="10%">HUID</th>
	                        <th width="10%">No of Tags</th>
	                        <th width="5%">Pieces</th>
							<th width="5%">Gross Wgt</th>
	                        <th width="5%">Net Wgt</th>
	                        <th width="5%">Less Wgt</th>
	                        <th width="5%">Dia Pcs</th>
	                        <th width="5%">Dia Wt</th>
	                        <th width="5%">Dia Value</th>
	                        <th width="5%">Gram (Wgt)</th>
	                        <th width="5%">CT (Wgt)</th>
	                        <th width="5%">Size/Length</th>
	                        <th width="5%">Wastage Wt</th>
	                        <th width="5%">Wastage %</th>
	                        <th width="5%">MC/G</th>
	                        <th width="5%">MC/Pcs</th>
	                        <th width="5%">MC Type</th>
	                		<th width="5%">Cost</th>
							<th width="5%">Tagged User Name</th>
							<th width="5%">Age</th>
	                        <th width="10%">Remarks</th>
	                      </tr>
	                    </thead> 
						<tfoot>
							<tr>
								<th>Total</th>
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th>
								<th></th> 
								<th></th>
								<th></th>
								<th></th> 
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
							</tr>
						</tfoot>
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
<!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Tagging</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this tagging?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  
<!--  Edit Tag-->
<div class="modal fade" id="tagEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:70%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Tag Details</h4>
      </div>
     <div class="modal-body">
     	<div class="row"> 
			<div class="col-md-4">
	     			<label>Tag Code &nbsp;: &nbsp;</label> <span id="tag_code"></span>
			</div>
			<div class="col-md-4">
	     			<label>Lot No &nbsp;: &nbsp;</label> <span id="lot_no"></span>
			</div>
			<div class="col-md-4">
	     			<label>Branch &nbsp;: &nbsp;</label> <span id="branch"></span>
			</div> 
     	</div><br/>
		<div class="row"> 
			<div class="col-md-4">
				<label>Design &nbsp;: &nbsp;</label> <span id="lt_design_id"></span>
			</div>
			<div class="col-md-4"> 
				<label>Bal Gross Weight &nbsp;:&nbsp;</label><span type="number" id="lot_bal_wt" name="lot_bal_wt"></span> 
			</div>
			<div class="col-md-4">
				<label>Bal Pieces &nbsp;:&nbsp;</label><span type="number" id="lot_bal_pcs" name="lot_bal_pcs"></span>
			</div> 
		</div>
		<hr />
     	<form id="tagging">
			<div class="row"> 
				<div class="col-md-4">
				 <div class="form-group">
				   <label >Calculation Type</label>  
						<div>
							<input type="radio" id="type0" class="calculation_based_on" name="calculation_based_on" value="0" > <label for="type0" class="custom-label">Mc & Wast On Gross </label>
						</div>
						<div>
						<input type="radio" id="type1" class="calculation_based_on" name="calculation_based_on" value="1"> <label for="type1" class="custom-label"> Mc & Wast On Net </label>
						</div>
						<div>
						<input type="radio" id="type2" class="calculation_based_on" name="calculation_based_on" value="2"> <label for="type2" class="custom-label">Mc on Gross,Wast On Net </label>
						</div>
						<div>
						<input type="radio" id="type3" class="calculation_based_on" name="calculation_based_on" value="3"> <label for="type3" class="custom-label">Fixed Rate </label>
						</div>
						<div>
						<input type="radio" id="type4" class="calculation_based_on" name="calculation_based_on" value="4"> <label for="type4" class="custom-label">Fixed Rate based on Weight </label>
						</div>
				 </div> 
				</div>
				<div class="col-md-8">
					<div class="row">
		     			<div class="col-md-12"> 
		     				<div class="col-md-4">
		     					<div class="form-group">
				     				<label>Gross Weight &nbsp;&nbsp;</label><input type="number" step="any" id="gross_wt" name="gross_wt">
				     			<input type="hidden" id="cur_gross_wt" name="">
				     			</div>
		     				</div>
		     				<div class="col-md-4">
		     					<div class="form-group">
				     				<label>Less Weight &nbsp;&nbsp;</label><input type="number" step="any" id="less_wt" name="less_wt">
				     			</div>
		     				</div>
		     				<div class="col-md-4">
		     					<div class="form-group">
				     				<label>Net Weight &nbsp;&nbsp;</label><input type="number" step="any" id="net_wt" name="net_wt">
				     			</div>
		     				</div> 
				     </div>
		     	   </div><br/>
		     	   <div class="row">
		     			<div class="col-md-12">
		     				<div class="col-md-2">
		     					<div class="form-group">
				     				<label>Pieces &nbsp;&nbsp;</label>
				     				<input type="number" step="any" id="piece" name="pieces"  style="width: 75px"><input type="hidden" id="cur_pieces" name="cur_pieces">
		     					</div>
		     				</div>
		     				<div class="col-md-2">
		     					<div class="form-group">
				     				<label>Size   &nbsp;&nbsp;</label>
				     				<input type="number" step="any" id="size" name="size" style="width: 75px">
		     					</div>
		     				</div>
		     				<div class="col-md-4"> 
						     	<label>Making Charge &nbsp;&nbsp;</label>
		     					<div class="row">
		     						<div class="col-md-6" >
		     							<div class="input-group">
						     				<input type="number" step="any" id="making_charge" name="tag_mc_value" style="width: 75px" >   				 
						     			</div>
		     						</div>
		     						<div class="col-md-4" style="padding: 0px">
		     							<select class="tag_mc_type" id="tag_mc_type" name="tag_mc_type" class="col-md-6">
						     				<option value="1">Per Gram</option>
						     				<option value="2">Per Pieces</option>
					     				</select>
		     						</div>
		     					</div> 
		     				</div> 
		     				<div class="col-md-4">
		     					<div class="form-group">
				     				<label>Wastage Percentage &nbsp;&nbsp;</label>
				     				<input type="number" step="any" id="wastage_percentage" name="retail_max_wastage_percent">
				     			</div>
		     				</div>
				     	</div> 
		     	   </div><br/>
		     	   <div class="row">
		     			<div class="col-md-12">
		     				<div class="col-md-4"> 
		     	   				<div class="form-group">
				     				<label>Sell Rate <span class="sell_rate_type"></span></label> 
				     				<input type="number" step="any" id="sell_rate" name="sell_rate">
		     					</div>
		     				</div>
		     				<div class="col-md-2">
		     					<div class="form-group">
				     				<label>Item Rate</label> 
				     				<input type="number" step="any" id="item_rate" name="item_rate" style="width:75px" readonly="">
		     					</div>
		     				</div>
		     				<div class="col-md-2">
		     					<div class="form-group">
				     				<label>Round Off</label> 
				     				<input type="number" step="any" id="round_off" name="round_off" style="width:75px">
		     					</div>
		     				</div>
		     				<div class="col-md-4">
		     					<div class="form-group">
					     			<label>Sale Value &nbsp; &nbsp;</label><input step="any" type="number" id="sales_value" name="sale_value" readonly>
					     			<input type="hidden" id="metal_rate" name="">
					     			<input type="hidden" id="tag_id" name="tag_id">
					     			<input type="hidden" id="tax_percentage" name="">
					     			<input type="hidden" id="tgi_calculation" name="">
					     			<input type="hidden" id="img_source" name="img_source">
		     					</div>
		     				</div>
				     	</div>
		     	   </div>  
	     	   </div><br/> 
			</div><br/>
			  <div class="row">
			  	<div class="form-group">
     			
		     </div>
     	   </div><br/>
		   
     	   <div class="row" style="margin-right: 1px;">
    			<div class="box-tools pull-right">
    			    <button type="button" id="add_stone_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
    			</div>
			</div>
     	   <div class="row" style="width: 100%;margin: 1px;">
     	   		<div class="table-responsive">
					 <table id="tagging_stone_details" class="table table-bordered table-striped text-center">
						<thead>
						  <tr>
							<th width="10%">Stone</th>
							<th width="10%">Pieces</th>
							<th width="10%">Weight</th>    
							<th width="10%">Amount</th>
							<th width="10%">Action</th>
						  </tr>
						</thead> 
						<tbody>
						</tbody>
					 </table>
				  </div>
     	   </div>
    <div class="row">
     	   	<div id="uploadArea_p_stn" class="col-md-12">
     	   	<input type="file" name="pre_images" id="pre_images" multiple="multiple">
     	   	<div id="preview"></div>
     	   </div>
     	   </form>
     </div><br/>
      <div class="modal-footer">
      	<button type="button" class="btn btn-success" id="update_tag">Update</button>
      	<button type="button" class="btn btn-warning" id="close">Close</button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- Less Wt Details Modal -->
<div class="modal fade" id="lessWtModal" tabindex="-1" role="dialog" aria-labelledby="lessWtModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:98%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
	    <h4 class="modal-title">Stone Details</h4>
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="stoneDetailsBody">
       	<div class="row">

					<input type="hidden" id="stone_active_row" value="0">

					<table id="tag_stone_details" class="table table-bordered table-striped text-center">

					<thead>

					<tr>

					<th>LWT</th>

					<th>Stone Type</th>

					<th>Stone Name</th>

					<th>Code</th>

					<th>Pcs</th>

					<th>Wt</th>

					<th>Cal.Type</th>

					<th>Cut</th>

					<th>Color</th>

					<th>Clarity</th>

					<th>Shape</th>

					<th>Rate</th>

					<th>Amount</th>

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
  </div>
</div>
