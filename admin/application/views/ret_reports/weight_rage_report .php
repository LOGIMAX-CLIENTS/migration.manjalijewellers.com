
<!-- Content Wrapper. Contains page content -->

   <div class="content-wrapper">
     <!-- Content Header (Page header) -->
     <section class="content-header">
       <h1>
         Weight Range Report 
       </h1>
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
                                	<label>Select category</label>
                                	<select id="category" class="form-control"style="width: 100%;"></select>
                                </div>
								<div class="col-md-2"> 
									<div class="form-group">    
										<label>Select Product</label> 
										<select id="prod_select" class="form-control" style="width:100%;"></select>
										
									</div> 
								</div>
								<div class="col-md-2"> 
									<div class="form-group">    
										<label>Select Design</label> 
										<select id="des_select" style="width:100%;"></select>
										
									</div> 
								</div>
								
								<div class="col-md-2"> 
									<div class="form-group">    
										<label>Select Sub Design</label> 
										<select id="sub_des_select" style="width:100%;"></select>
										<option></option>
									</div> 
								</div>
								
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="weight_range_design_search" class="btn btn-info">Search</button>   
									</div>
								</div>
								
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div> 
                
				  
			  
                   <div class="row">
	                   <div class="col-md-12">
	                   	<div class="table-responsive">
                           <table id="weight_rage_report_list" class="table table-bordered table-striped text-center sales_list" style="width:100%;">
                                <thead style="text-transform:uppercase;">
                                    <tr>
									  
                                        <th>Product</th>
                                        <th>Design</th>
                                        <th>Sub Design</th>
                                        <th>From Wt</th>
                                        <th>To Wt</th>
                                        <th>Mc Percent</th>
                                        <th>Mc</th>
										<th>Mc Min</th>
                                        <th>Mc Max</th>
										<th>Wastage Min</th>
                                        <th>Wastage Max</th>
                                    </tr>
                                </thead>
                                <tbody ></tbody>    
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
   

