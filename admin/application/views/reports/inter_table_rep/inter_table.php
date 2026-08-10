<!-- line added by durga 28/12/2022 to get usertype -->
 <?php $username=($this->session->userdata['profile']);?>
<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Sync Tool Records
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Inter Records</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
      
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
				
					<div class="box-header with-border">
					  <h3 class="box-title">Sync Tool Inter Table Records</h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
					  </div>
					</div>
                </div><!-- /.box-header -->
               

			   <div class="box-body">
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
			
				<div id="alert_msg"></div>
					       
<style>
.sync-toolbar {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 16px 20px;
    margin-bottom: 20px;
}
.sync-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    margin-bottom: 6px;
    display: block;
}
.sync-date-wrapper {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    padding: 3px 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    cursor: pointer;
}
.sync-date-wrapper input {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    width: 105px;
    text-align: center;
    box-shadow: none !important;
    height: 30px;
    cursor: pointer;
}
.sync-date-wrapper input:focus {
    outline: none;
}
.sync-date-wrapper .sep {
    color: #94a3b8;
    font-size: 11px;
    font-weight: bold;
}
.btn-sync-action {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 5px;
    padding: 0 20px;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25);
    transition: all 0.2s ease;
    cursor: pointer;
    height: 38px;
}
.btn-sync-action:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    box-shadow: 0 4px 8px rgba(37, 99, 235, 0.35);
}
.btn-update-action {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 5px;
    padding: 0 20px;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.25);
    transition: all 0.2s ease;
    cursor: pointer;
    height: 38px;
    line-height: 38px;
}
.btn-update-action:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.35);
}

/* Search Filter Card Styles */
.search-filter-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 16px 24px;
    margin-bottom: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.search-filter-wrapper {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 12px;
    flex-wrap: nowrap;
    width: 100%;
    overflow-x: auto;
}
.filter-field-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.filter-field-item label {
    font-size: 13px !important;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0;
    white-space: nowrap;
}
.filter-field-item input.form-control {
    height: 38px !important;
    font-size: 13px !important;
    border-radius: 5px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 6px 10px !important;
    color: #1e293b;
    width: 130px !important;
    box-shadow: none !important;
    background-color: #ffffff;
    flex-shrink: 0;
}
.filter-field-item input.form-control:focus {
    border-color: #2563eb !important;
    outline: none !important;
}
.search-filter-card .btn-search {
    height: 38px;
    padding: 0 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    flex-shrink: 0;
}
</style>

<div class="sync-toolbar">
    <div class="row" style="display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap;">
        
        <!-- Table Select -->
        <div class="col-md-3 col-sm-4">
            <label class="sync-label"><i class="fa fa-table"></i> Select Table</label>
            <select id="Table_Select" class="form-control" style="width: 100%; border-radius: 5px; height: 38px;">
                <option value="">Table</option>
                <option value="1">Customer Reg</option>
                <option value="2">Transactions</option>
            </select>
            <input id="id_cus" name="customer_reg[id_cus]" type="hidden" value=""/>
        </div>

        <!-- Date Range Filter -->
        <div class="col-md-6 col-sm-8" style="padding-left: 40px;">
            <label class="sync-label"><i class="fa fa-calendar"></i> Sync Transfer Date Range</label>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button type="button" class="btn btn-default btn_date_range" id="sync_daterange_btn" style="height: 38px; border-radius: 5px; border: 1px solid #cbd5e1; background: #ffffff; font-weight: 600; font-size: 13px; color: #1e293b; padding: 0 15px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa fa-calendar" style="color: #64748b;"></i>
                    <span id="sync_daterange_text"><?php echo date('Y-m-d').' To '.date('Y-m-d'); ?></span>
                    <i class="fa fa-caret-down" style="color: #64748b; margin-left: 4px;"></i>
                </button>
                <input type="hidden" id="sync_from_date" value="<?php echo date('Y-m-d'); ?>" />
                <input type="hidden" id="sync_to_date" value="<?php echo date('Y-m-d'); ?>" />

                <button type="button" id="sync_data" class="btn-sync-action">
                    <i class="fa fa-refresh"></i> Sync Data
                </button>
            </div>
        </div>

        <!-- Update Action Button -->
        <div class="col-md-3 col-sm-12 text-right">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-update-action" id="update">
                    <input type="radio" name="upd_mob_btn" value="1"><i class="icon fa fa-check"></i> Update Selected
                </label>
            </div>
        </div>

    </div>
</div>
					       
					       
					       
				<div class="row">
					<div class="col-md-12" id="table" style="display: none; margin-bottom: 15px;">
						<div class="search-filter-card">
							<div class="search-filter-wrapper">
								<span id="mob_wrap" class="filter-field-item">
									<label id="mob">Mobile No</label>
									<input type="text" id="mobilenumber" class="form-control" placeholder="Mobile No" autocomplete="off" />
								</span>

								<span class="filter-field-item">
									<label>ClientId</label>
									<input type="text" id="clientid" class="form-control" placeholder="Client ID" autocomplete="off" />
								</span>

								<span class="filter-field-item">
									<label>Ref No</label>
									<input type="text" id="ref_no" class="form-control" placeholder="Ref No" autocomplete="off" />
								</span>

								<span id="group_wrap" class="filter-field-item">
									<label id="mob1">Group Code</label>
									<input type="text" id="group_code" class="form-control" placeholder="Group Code" autocomplete="off" />
								</span>

								<span class="filter-field-item">
									<label>Id Sch Acc</label>
									<input type="text" id="id_scheme_account" class="form-control" placeholder="Id Sch Acc" autocomplete="off" />
								</span>

								<button type="button" id="mob_submit" name="mob_submit" class="btn btn-primary btn-search">
									<i class="fa fa-search"></i> Search
								</button>
							</div>
						</div>
					</div> 
				</div>
		<!-- table wise Filter & change  option for cus reg, trans hh -->	
				
				<!-- Alert -->
                     <!-- line added by durga 28/12/2022 to get usertype -->
                  <input type="hidden" id="hiddenuserdata" value=<?php echo $username ?> >
                  <div class="table-responsive" id="table1">
                  <table id="intertable_list" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr> 
                        <!--<th><label class="checkbox-inline"><input type="checkbox" id="select_mob"  name="select_all" value="all"/>All</label></th>-->
	                    <th>Cus Reg ID</th>
                        <th>Client ID</th>
                        <th>Branch</th>
                        <th>Record To</th>
                        <th>Is Modified</th>
                        <th>Reg Date</th>
                        <th>Maturity Date</th>
                        <th>Acc Name</th>
                        <th>First Nmae</th>
                        <th>Last Name</th>
                        <th>Add1</th>
                        <th>Add2</th>
                        <th>Add3</th>
                        <th>Mobile No</th>
                        <th>New Customer</th>
                        <th>Ref No</th>
                        <th>Id Sch Acc</th>
                        <th>Sync Sch Code</th> 
                        <th>Group Code</th>  
                        <th>Scheme Acc No</th>
                        <th>Is Closed</th>
                        <th>Closed By</th>
                        <th>Closing Date</th>
                        <th>Closing Amount</th>
                        <th>Closing Weight</th>
                        <th>Is Transferred</th>
                        <th>Trans Date</th>
                        <th>Date Upd</th>
                        <th>Date Add</th>
                       <th>Is Reg Online</th>
            
                      </tr>
                    </thead>
                  </table>
               
				</div>  	<div class="overlay" style="display: none;">
                   <i class="fa fa-refresh fa-spin"></i>
                	</div>
				
			 <div class="table-responsive" id="table2" style="display: none;">
                  <table id="intertable_translist" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr> 
                        <!--<th><label class="checkbox-inline"><input type="checkbox" id="select_mob"  name="select_all" value="all"/>All</label></th>-->
	                    <th>Trans ID</th>
                        <th>Client ID</th>
                        <th>Record To</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Metal Weight</th>
                        <th>Saved Benefit</th>
                        <th>Saved Benefit Amt</th>
                        <th>Metal Rate</th>
                        <th>Pay Mode</th>
                        <th>Ref No</th>
                        <th>Is Transferred</th>
                        <th>Is Modified</th>
                        <th>Trans Date</th>
                        <th>New Customer</th>  
                        <th>Id Sch Acc</th>
                        <th>Id Branch</th>
                        <th>Pay Status</th>
                        <th>Pay Type</th>
                        <th>Due Type</th>
                        <th>Receipt No</th>
                        <th>Date Add</th>
                        <th>Date Upd</th>
                        <th>Install No</th>
                        <th>Emp Code</th>
            
                      </tr>
                    </thead>
                  </table>
               
				</div>
			      <div class="overlay" style="display: none;">
                   <i class="fa fa-refresh fa-spin"></i>
                	</div>
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

<script type="text/javascript">
$(document).ready(function() {
    function bindSyncDaterangepicker() {
        if (typeof $.fn.daterangepicker === 'undefined' || typeof moment === 'undefined') {
            setTimeout(bindSyncDaterangepicker, 100);
            return;
        }

        var start = moment();
        var end = moment();

        function setSyncDates(start, end) {
            var fromStr = start.format('YYYY-MM-DD');
            var toStr = end.format('YYYY-MM-DD');
            $('#sync_from_date').val(fromStr);
            $('#sync_to_date').val(toStr);
            $('#sync_daterange_text').html(fromStr + ' To ' + toStr);
        }

        $('#sync_daterange_btn').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end) {
            setSyncDates(start, end);
        });

        $('#sync_daterange_btn').on('apply.daterangepicker', function(ev, picker) {
            setSyncDates(picker.startDate, picker.endDate);
        });

        setSyncDates(start, end);
    }

    bindSyncDaterangepicker();
});
</script>