      <!-- Content Wrapper. Contains page content -->
    <style>
    	
		.remove-btn{
			margin-top: -168px;
		    margin-left: -38px;
		    background-color: #e51712 !important;
		    border: none;
		    color: white !important;
		}
        .input-group {
                position :relative;
                display: inherit;
                border-collapse: separate;
            }
       .input-group .input-group-addon {
                border-radius: 0;
                border-color: #d2d6de;
                background-color: #fff;
                border-left: 1px solid #000;
                height: 34px;
            }
		
		
    </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
       

        <!-- Main content -->
        <section class="content order">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Non Tag Item Details</h3>
            </div>
            <div class="box-body">
             <!-- form container --> 
	             <!-- form -->
				<form id="lot_generate_form">
				<div class="row">
					
				    <div class="col-md-12">
				        <div class="col-md-2">
    	                     <div class="form-group">
    	                       <label>Lot Received At</label>
    							    <select class="form-control" id="branch_select" disabled></select>
                                    <input type="hidden" id="id_branch" value="1">
    	                     </div> 
				        </div>
                        <div class="col-md-2">
    	                     <div class="form-group">
    	                       <label>Karigar</label>
    							<select class="form-control" id="select_karigar" ></select>
    	                     </div> 
				        </div>
				    </div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
					    <div class="table-responsive">
					        <input type="hidden" id="stone_active_id">
						 <table id="nontag_lot_item_detail" class="table table-bordered table-striped">
							<thead style="text-transform:uppercase;">
						          <tr>
						            <th width="1%;"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th> 
                                    <th width="5%;">Category</th> 
						            <th width="5%;">Counter</th> 
						            <th width="5%;">Product</th> 
                                    <th width="1%">Purity</th>
						            <th width="5%;">Pcs</th> 
						            <th width="7%;">Gwt</th> 
						            <th width="7%;">Nwt</th> 
						            <th width="1%;">Action</th> 
						          </tr>
					         </thead>
					         <tbody></tbody>
                             <tfoot style="font-weight:bold;">
                                <tr>
                                    <td></td>
                                    <td>Total:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>         
                                    <td class="tot_nt_pcs"></td>
                                    <td class="tot_nt_grswt"></td>
                                    <td class="tot_nt_netwt"></td>
                                    <td></td>
                                </tr>
                             </tfoot>
						</table>
					    </div>
					</div> 
				</div>	
				<p class="help-block"></p>
 
				  <?php echo form_close();?>
	           </div>  
               <div class="row">
                    <div class="box box-default"><br />
                        <div class="col-xs-offset-5">
                            <button type="button" id="nontag_lot_save" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-default btn-cancel">Cancel</button>

                        </div> <br />
                    </div>
                </div>
	            <div class="overlay" style="display:none">
        		  <i class="fa fa-refresh fa-spin"></i>
        		</div>
	           
	       </div>  
        </section>
        
</div>

<div class="modal fade" id="lgt_stoneModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:73%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Stone Details</h4>
			</div>
			<div class="modal-body">
    			<div class="row">
					   
    					<table id="lgt_cus_item_details" class="table table-bordered table-striped text-center">
        					<thead>
            					<tr>
                				
                					<th width="10%">ID</th>
                					<th width="10%">Stone Name</th>
                					<th width="10%">Stone Pcs</th> 
                					<th width="10%">Stone Wt</th>
             
            					</tr>
        					</thead> 
        					<tbody>
							</tbody>										
        					<tfoot><tr style="font-weight:bold;">
							<td>Total</td>
							<td></td>
							<td class="ps_tot_pcs"></td>
							<td class="ps_tot_wt"></td>
							</tr></tfoot>
    					</table>
    			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>  

<div class="modal fade" id="cus_stoneModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:72%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>
			</div>
			<div class="modal-body">
    			<div class="row">
    			        <input type="hidden" id="activeRow">
    					<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">
        					<thead>
            					<tr>
                					<th width="15%">#</th>
                					<th width="15%">Stone</th>
                					<th width="5%">Pcs</th>   
                					<th width="22%">Wt</th>
                					<th width="12%">Rate</th>
                					<th width="15%">Amount</th>
            					</tr>
        					</thead> 
        					<tbody></tbody>										
        					<tfoot>
        					    <tr></tr>
        					</tfoot>
    					</table>
    			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" id="remove_stone_details" class="btn btn-success">Save</button>
			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>
            
