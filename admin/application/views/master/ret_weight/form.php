<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
        Add Weight Range
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>Master</a></li>
		<li class="active"> Add Weight Range </li>
		</ol>
	</section>
     <!-- Default box -->
    <section class="content">
      <form id="add_reorder">  
		<div class="box">
			<div class="box-header with-border">
              <h3 class="box-title"> Add Weight Range </h3>
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
				                <label>Select Product<span class="error"> *</span></label>
                                <select id="weight_prod" class="form-control" style="width:100%;"></select>
                                <input type="hidden" id="product" value=''>
							</div>
				        </div>		
			
                        <!-- <div class="col-md-2">
					    	<div class='form-group'>
					           <label>Units<span class="error">*</span></label>
                       	       <select id="uom" id="uom" style="width:100%;"></select>
							</div>
						</div> -->

                        <div class="col-md-2">
					    	<div class='form-group'>
					           <label>Value<span class="error">*</span></label>
                               <input type="number" class="form-control" id="name" name="name" placeholder="Weight Range Value" autocomplete="off"> 
							</div>
						</div>

                        <div class="col-md-2">
					    	<div class='form-group'>
					           <label>From Weight<span class="error">*</span></label>
                               <input type="number" step="any" class="form-control" id="from_weight" name="from_weight" placeholder="Enter From Weight"> 
							</div>
						</div>

                        <div class="col-md-2">
					    	<div class='form-group'>
					           <label>To Weight<span class="error">*</span></label>
                               <input type="number" step="any" class="form-control" id="to_weight" name="to_weight" placeholder="Enter To Weight"> 
							</div>
						</div>

                        <div class="col-md-2">
					    	<div class='form-group'>
					           <label>To Description<span class="error">*</span></label>
                               <input type="text" class="form-control" id="weight_desc" name="weight_desc" placeholder="Enter Description"> 
							</div>
						</div>

						<div class="col-md-2">
					    	<div class='form-group'>
					           </br>
							   <button id="add_weight" type="button" class="btn btn-success pull-left"><i class="fa fa-plus"></i> Add item</button>
							</div>
						</div>
					</div>
                </div>   
            
                    <div class="box-body">
                     <div class="row">
                    <table id="total_weight_items_preview" class="table table-bordered table-striped text-center">
                    <input id="weight" type="hidden" value=""/>
                    <input  type="hidden" id="total_weight" value=""/>
                    <input  type="hidden" id="weight_saved" value=""/>
                    <input  type="hidden" value="0" id="i_increment"/>	

                    <thead>
                      <tr>
					    <th>Product</th>
                        <!-- <th>Units</th> -->
                        <th>Values</th>
                        <th>From Weight</th>
                        <th>To Weight</th>
                        <th>To Description</th>
                        <th>Action</th>
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
				               <button type="button" class="btn btn-default btn-cancel">Cancel</button>
				            </div> <br/>
			            </div>
		            </div> 
                    			
            </div>
        </form>
    </section>
</div>