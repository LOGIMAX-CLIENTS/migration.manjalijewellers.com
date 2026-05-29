      <!-- Content Wrapper. Contains page content -->
    <style>
    	.remove-btn{
			margin-top: -168px;
		    margin-left: -38px;
		    background-color: #e51712 !important;
		    border: none;
		    color: white !important;
		}
		.sm{
			font-weight: normal;
		}
		}
    </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
       

        <!-- Main content -->
        <section class="content order">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">PURCHASE ORDER DELIVERY</h3>
            </div>
            <div class="box-body">
             <!-- form container --> 
	             <!-- form -->
				<form id="order_submit">
				<div class="row">
				    <div class="col-md-12">
				        <div class="col-md-2">
    	                     <div class="form-group">
    	                       <label>Select Karigar<span class="error">*</span></label>
    								<select id="select_karigar" class="form-control" name="order[id_karigar]" style="width:100%;" tabindex="1">></select>
    	                     </div> 
				        </div>
				         <div class="col-md-2">
    	                     <div class="form-group">
    	                       <label>Select PO NO<span class="error">*</span></label>
    								<select id="select_pur_ord_no" class="form-control"  style="width:100%;" tabindex="2"></select>
    	                     </div> 
    			        </div>
    			        <div class="col-md-2">
    	                     <div class="form-group">
    	                            <br>
    								<button type="button" id="search_pur_order" class="btn btn-primary"  tabindex="3">Search</button>
    	                     </div> 
    			        </div>
				    </div>
				</div>	
				<div class="row">
					<div class="col-md-12">
					    <div class="table-responsive">
						<p class="help-block"></p></legend>
						 <table id="item_detail" class="table table-bordered table-striped">
							<thead>
						          <tr>
						            <th width="5%;">PO NO</th> 
						            <th width="10%;">Product</th> 
						            <th width="10%;">Design</th> 
						            <th width="10%;">Sub Design</th> 
						            <th width="10%;">Size</th> 
						            <th width="10%;">Wgt Range</th> 
						            <th width="10%;">Order Pcs</th> 
						            <th width="10%;">Delivered Pcs</th> 
						            <th width="10%;">Delivery Pcs</th> 
						            <th width="10%;">Delivery Wt</th> 
						          </tr>
					         </thead>
					         <tbody> 
					         </tbody>
						</table>
					    </div>
					</div> 
				</div>	
				<p class="help-block"></p>
			
				     
			     <div class="row">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="button" id="order_delviery" class="btn btn-primary">save</button>
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div> 
	           </div>  
	            <?php echo form_close();?>
	           <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
	       </div>  
        </section>
    </div>
            
