<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
        Add Wastage Discount Range
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>Master</a></li>
		<li class="active"> Wastage Discount </li>
		</ol>
	</section>
     <!-- Default box -->
    <section class="content">
      <form id="add_reorder">  
		<div class="box">
			<div class="box-header with-border">
              <h3 class="box-title"> Add Wastage Discount Range </h3>
                <div class="box-tools pull-right">
                 <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                 <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

                </div>
            </div> 
            
            <input type="hidden" name="settings[branch_settings]" id="branch_settings" value="<?php echo $this->session->userdata('branch_settings');?>">
            <div id="error-msg"></div>
            <div class="box-body">
				    <div class='row'>	
                        <div class="col-md-2">
				            <div class='form-group'>
				                <label>Select Metal<span class="error"> *</span></label>
                                <select id="metal" class="form-control" style="width:100%;"></select>
                                <input type="hidden" id="id_metal" value=''>
							</div>
				        </div>		                     			
                        <div class="col-md-2">
							<div class='form-group'>
								<label>From Wastage <span class="error">*</span></label>
								<input type="number" step="any" class="form-control" id="from_wastage_disc" name="from_wastage" placeholder="Enter From Wastage %"> 
							</div>
						</div>
						
                        <div class="col-md-2">
							<div class='form-group'>
								<label>To Wastage <span class="error">*</span></label>
								<input type="number" step="any" class="form-control" id="to_wastage_disc" name="to_wastage" placeholder="Enter To Wastage %"> 
							</div>
						</div>
						
						<div class="col-md-2">
							<div class='form-group'>
							   <label>Value<span class="error">*</span></label>
							   <input type="number" class="form-control" id="va_value" name="va_disc_value" placeholder="Wastage Value in %" autocomplete="off"> 
							</div>
						</div>

						<div class="col-md-2">
							<div class='form-group'>
								</br>
								<button id="add_wastage_disc" type="button" class="btn btn-success pull-left"><i class="fa fa-plus"></i> Add item</button>
							</div>
						</div>
					</div>
                </div>   
            
                    <div class="box-body">
                     <div class="row">
                    <table id="total_wastage_items_preview" class="table table-bordered table-striped text-center">
                    <input id="wastage_disc" type="hidden" value=""/>
                    <input  type="hidden" id="total_va" value=""/>
                    <input  type="hidden" id="va_saved" value=""/>
                    <input  type="hidden" value="0" id="i_increment"/>	

                    <thead>
                      <tr>
					    <th>Metal</th>
                        <th>From Wastage %</th>
                        <th>To Wastage %</th>
                        <th>Value</th>
                      </tr>
                 	</thead>
                     <tbody>
                     
                     </tbody>
                  </table>
                   </div>

		            <div class="row">
		                <div class="box box-default"><br/>
			                <div class="col-xs-offset-5">
				               <!-- <button type="button" id="add_weight"class="btn btn-primary">save</button>  -->
				               <button type="button" class="btn btn-default" onclick="window.location.href='<?php echo base_url('index.php/admin_ret_catalog/wastage_discount/list'); ?>'"><i class="fa fa-arrow-left"></i> Back</button>
				            </div> <br/>
			            </div>
		            </div> 
                    			
            </div>
        </form>
    </section>
</div>