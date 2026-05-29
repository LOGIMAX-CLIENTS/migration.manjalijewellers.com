    <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>
            Reports
			 <small>Advance Total</small>
          </h1>

          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Advance Total &amp; Adjustment</li>
          </ol>

        </section>

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               <div class="box box-primary">

			    <div class="box-header with-border">
                  <h3 class="box-title">Advance Total &amp; Adjustment List</h3>  <span id="total_count" class="badge bg-green"></span>
                </div>

                 <div class="box-body">

                <!-- ═══ Filter Bar — centered, single row, no Bootstrap grid inside ═════ -->
                <div style="text-align:center; margin-bottom:12px; padding:0 15px;">
                    <div style="display:inline-flex; align-items:flex-end; flex-wrap:nowrap; gap:8px;">

                            <!-- Branch (multi-branch only) -->
                            <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0):?>
                            <div style="min-width:160px; max-width:200px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Select Branch</label>
                                    <select id="branch_select" class="form-control branch_filter" style="width:100%;" multiple></select>
                                </div>
                            </div>
                            <?php else:?>
                            <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                            <input type="hidden" id="branch_name"   value="<?php echo $this->session->userdata('branch_name') ?>">
                            <?php endif;?>

                            <!-- Filter By -->
                            <div style="width:130px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Filter By</label>
                                    <select id="filter_by" class="form-control">
                                        <option value="date_range">Date Range</option>
                                        <option value="as_on_date">As on Date</option>
                                        <option value="customer">Customer</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Date Range picker (visible in Date Range mode) -->
                            <div style="width:200px;" id="date_range_col">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Date Range
                                        <span id="aod_label" style="display:none;">
                                            — <span id="aod_display_date"></span>
                                        </span>
                                    </label>
                                    <?php
                                        $fromdt = date("d/m/Y");
                                        $todt   = date("d/m/Y");
                                    ?>
                                    <input type="text" class="form-control" id="rpt_date_picker"
                                           placeholder="From - To Date"
                                           value="<?php echo $fromdt.' - '.$todt; ?>" readonly style="background:#fff; cursor:pointer;">
                                    <span style="display:none;" id="rpt_from_date"></span>
                                    <span style="display:none;" id="rpt_to_date"></span>
                                </div>
                            </div>

                            <!-- As on Date (hidden placeholder — see date_range_col label for display) -->
                            <div id="aod_col" style="display:none;"></div>

                            <!-- Customer Select2 (visible only in Customer mode) -->
                            <div style="width:200px; display:none;" id="customer_col">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Search Customer</label>
                                    <select id="adv_customer_select" class="form-control" style="width:100%;">
                                        <option value="">-- All Customers --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Mobile No (shown in Customer mode) -->
                            <div style="width:140px; display:none;" id="mobile_col">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Mobile No</label>
                                    <input class="form-control" type="text" pattern="[1-9]{1}[0-9]{9}" maxlength="10"
                                           placeholder="Enter Mob No" id="Mob_search">
                                    <input type="hidden" id="Cus_id" value="">
                                    <span id="mob_err" style="font-size:11px; color:red;"></span>
                                </div>
                            </div>

                            <!-- Search Button -->
                            <div style="padding-bottom:1px;">
                                <button type="button" id="advance_total_search" class="btn btn-info">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>

                    </div><!-- /.inline-flex -->
                </div><!-- /.filter-bar-wrapper -->
                <!-- ═══ End Filter Bar ══════════════════════════════════════════════════════ -->

                <!-- Alerts -->
			   <div class="row">
					<div class="col-xs-12">
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

                <!-- Data Table -->
                   <div class="row">
	                   <div class="col-md-12">
	                   	<div class="table-responsive">
		                 <table id="advance_total_list" class="table table-bordered table-striped text-center">
		                    <thead>
						  <tr>
						    <th>Name</th>
						    <th>Mobile</th>
                                <th>Receipted Amount</th>
                                <th>Advance Received From - Name</th>
                                <th>Advance Received From - Mobile</th>
                                <th>Advance Received Amount</th>
                                <th>Advance Received Receipt No</th>
                                <th>Utilized Amount</th>
						    <th>Refund Amount</th>
						    <th>Transfer Amount</th>
						    <th>Balance Amount</th>
						    <th>Receipt Weight</th>
                                <th>Utilized Weight</th>
                                <th>Balance Weight</th>
                                <th>Details</th>
						  </tr>
		                    </thead>
		                    <tfoot><tr style="font-weight:bold; color: red">
		                        <td></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                <td style="text-align: right"></td>
                                </tr></tfoot>
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
