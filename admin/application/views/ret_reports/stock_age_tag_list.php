  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Stock Age Analysis Tag</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Stock age</li>
          </ol>
        </section>

		<style>
			table tfoot td:nth-child(11),
			table tfoot td:nth-child(12) {
				text-align: right;
			}
		</style>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Stock Age Analysis Tag List</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
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
				   
				   <div style="display:none;" class="form-group tagged">
        		        <label>Select Branch</label>
        				<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
						<input type="hidden" id="id_branch">
        		    </div>

                   <div class="row">
                         <div class="col-md-4">
                             <div class="btn-group" data-toggle="buttons">
    					        <label class="btn btn-success" id="approve">
    					            <input type="radio" name="upd_status_btn" value="1"><i class="icon fa fa-check"></i> Mark Green Tag
    					        </label>
    					        <label class="btn btn-danger" id="reject">
    					            <input type="radio" name="upd_status_btn"  value="0"><i class="icon fa fa-remove"></i> Unmark Green Tag
    					        </label>
    						</div>
                        </div>  
                        <div class="col-md-2">
						 		    <div class="btn-group" data-toggle="buttons">
							 			<button type="button" class="btn btn-success" id="tag_print" >Tag Print</button>
									</div>
						 </div>
                   </div></br>
                  <div class="table-responsive">
	                 <table id="stock_age_tag" class="table table-bordered table-striped text-center">
	                    <thead>
						  <tr>
						    <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>  
						    <th>Tag Code</th>  
						    <th>Old Tag Code</th>  
						    <th>Tag Date</th>  
						    <th>Old Tag Date</th>  
						    <!-- <th>No of Days</th>   -->
						    <th>Tag Age</th>
						    <th>Received Date</th>
						    <th>Received Age</th>
						    <th>Product Name</th>  
						    <th>Karigar</th>  
						    <th>Lot</th>  
							<th>Pieces</th>
						    <th>Gwt</th>  
						    <th>V.A(%)</th>  
						    <th>MC</th>  
						    <th>Status</th>  
						  </tr>
	                    </thead> 
	                    <tbody></tbody>
	                    <tfoot>
							<tr>
								<td></td><td></td><td></td><td></td><td></td><td></td>
								<td></td><td></td>
								<td></td><td></td><td></td><td></td><td></td><td></td>
								<td></td><td></td>
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
      

