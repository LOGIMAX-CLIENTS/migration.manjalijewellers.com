      <!-- Content Wrapper. Contains page content -->
    <style>
    	.remove-btn{
			margin-top: -168px;
		    margin-left: -38px;
		    background-color: #e51712 !important;
		    border: none;
		    color: white !important;
		}
    </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
        	Tagging
            <small>Tag Scan</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Tagging</a></li>
            <li class="active">Tag Scan</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content product">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Tag Scan list</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div class="row">
				<div class="col-md-offset-2 col-sm-12"> 
				 	<div class="row">
				 		<div class="col-md-offset-2 col-sm-2">
				 			<label>Tag Scan</label>
				 			<input type="text" class="form-control" id="tag_id" name="" autofocus="true">
						</div>
						<!--<div class="col-sm-2">
							<button class="btn btn-warning" id="add_scan" >Add New</button>
						</div>-->
				 	</div>		 
				 	<p class="help-block"></p>			 
				</div>	<!--/ Col --> 
			</div>	 <!--/ row -->
			<div class="table-responsive">
	                 <table id="tagging_scan_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="5%">Tag Code</th>
							<th width="5%">Lot No</th>
	                        <th width="5%">Gross Wgt</th>
	                        <th width="10%">Net Wgt</th>
							<th width="10%">Less Wgt</th>
	                      </tr>
	                    </thead>
	                    <tbody></tbody> 
	                 </table>
                  </div>		
                </div> 
	            <div class="overlay" style="display:none;">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
	             <!-- /form -->
	          </div>
             </section>
     </div>

            
