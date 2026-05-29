<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
        Reports
            <small>Lot Merge Report</small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Lot Merge Report</a></li>
        <li class="active">Lot Merge Report</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
			        <div class="box-header with-border">
                        <h3 class="box-title">Lot Merge Report</h3>  <span id="total_count" class="badge bg-green"></span> 
                    </div>
                    <div class="box-body">
                        <div class="row">
    				  	    <div class="col-md-offset-2 col-md-6">  
    	                        <div class="box box-default">  
    	                            <div class="box-body">  
    						             <div class="row">		
    								        <div class="col-md-3"> 
    									        <div class="form-group">    
    										        <label>Select Lot</label> 
    										        <select id="lot_nos" class="form-control" style="width:100%;"></select>
    									        </div> 
    								        </div>
    							        </div>
    						         </div>
    	                        </div> 
    	                    </div> 
                        </div>
                        <div class="box box-info stock_details">
                            <div class="box-header with-border">
                                <h3 class="box-title">Lot Merge Details</h3>
						        <div class="box-tools pull-right">
							        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						        </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table id="lot_merge_list" class="table table-bordered table-striped text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Lot No</th>
                                                        <th>Category</th>
                                                        <th>Product</th>
                                                        <th>Karigar Name</th>
                                                        <th>Pcs</th>
                                                        <th>Gross Wt</th>
                                                        <th>Net Wt</th>
                                                        <th>Dia Wt</th>
                                                        <th>Stn Wt</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead> 
                                                <tbody>
                                                    <tfoot style="font-weight:bold;">
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align:right;"></td>
                                                        <td style="text-align:right;"></td>
                                                        <td style="text-align:right;"></td>
                                                        <td style="text-align:right;"></td>
                                                        <td style="text-align:right;"></td>
                                                        <td></td>
                                                    </tfoot>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </div>    
    </section><!-- /.content -->
</div>    