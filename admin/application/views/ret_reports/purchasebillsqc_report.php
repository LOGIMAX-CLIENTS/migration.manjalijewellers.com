<style>
  	/* CSS for Drill-down */
  	.drill-collapsed {
	    display: none;
	}
	.drill-close {
	    display: none;
	}
	.drill-open {
	    display: block;
	}
	.drill-detail {
	    background:#fdfdfd
	}
	/* .CSS for Drill-down */
  </style>
     <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>QC Issue/ Receipt Details</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">QC Issue/ Receipt Details Report</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">QC Issue/ Receipt</h3>  <span id="total_count" class="badge bg-green"></span>
                </div>
                 <div class="box-body">
                  <div class="row">
				  	<div class="col-md-12">
	                  <div class="box box-default">
	                   <div class="box-body">
						   <div class="row">

						   		<div class="col-md-2">
								     <label></label>
    								 <div class="form-group">
                                          <button class="btn btn-default btn_date_range"  id="rpt_date_picker">
                                                    <i class="fa fa-calendar"></i> Date range picker
                                                    <i class="fa fa-caret-down"></i>
                                            </button>
                                                <span style="display:none;" id="rpt_from_date"></span>
                                                <span style="display:none;" id="rpt_to_date"></span>
                                         </div><!-- /.form group -->
                                </div>

								<div class="col-md-2">
									<label>Select Po Ref No's</label>
									<select id="select_po_ref_no" class="form-control" style="width:100%;"></select>
								</div>

								<div class="col-md-2">
									<label></label>
									<div class="form-group">
										<button type="button" id="po_qcbills_search" class="btn btn-info">Search</button>
									</div>
								</div>
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
				   	<div class="box box-info purchase_qcbills">
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="purchase_qcbills" class="table table-bordered table-striped text-center">
										 <thead>
										  <tr>
                				            <th width="5%;">PO Ref No</th>
                				            <th width="5%;">Issue Date</th>
                				            <th width="5%;">Employee</th>
                				            <th width="5%;">Product</th>
                				            <th width="5%;">Design</th>
                				            <th width="5%;">Sub Design</th>
                				            <th width="5%;">Pcs</th>
                				            <th width="5%;">Gwt</th>
                				            <th width="5%;">Lwt</th>
                				            <th width="5%;">Nwt</th>
                				            <th width="5%;">Status</th>
                				            <th width="5%;">Accepted Pcs</th>
                				            <th width="5%;">AcceptedGwt</th>
                				            <th width="5%;">AcceptedNwt</th>
                				            <th width="5%;">AcceptedLwt</th>
											<th width="5%;">RejectedPcs</th>
                				            <th width="5%;">RejectedGwt</th>
                				            <th width="5%;">RejectedNwt</th>
                				            <th width="5%;">RejectedLwt</th>
                				          </tr>
					                    </thead><tbody></tbody>
					                    <tfoot>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                         <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
											<td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                    </tfoot>
									 </table>
								  </div>
								</div>
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