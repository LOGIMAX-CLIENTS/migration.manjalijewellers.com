  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            In-Transit Dashboard
            <small>Sales Transfer & Return items currently in transit</small>
          </h1>
        </section>
        <!-- Main content -->
        <section class="content">
          <!-- KPI Summary Cards -->
          <div class="row" id="transit_kpi_row">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-truck"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">In-Transit Bills</span>
                  <span class="info-box-number" id="kpi_total_bills">0</span>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-tags"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Items</span>
                  <span class="info-box-number" id="kpi_total_items">0</span>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-inr"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Value</span>
                  <span class="info-box-number" id="kpi_total_value">₹0</span>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box bg-red">
                <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Aging > 3 Days</span>
                  <span class="info-box-number" id="kpi_aging_count">0</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Row -->
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-3">
                      <label>From Branch</label>
                      <select class="form-control select2" id="filter_from_branch">
                        <option value="">All</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>To Branch</label>
                      <select class="form-control select2" id="filter_to_branch">
                        <option value="">All</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>Transfer Type</label>
                      <select class="form-control" id="filter_trans_type">
                        <option value="">All</option>
                        <option value="1">Tagged</option>
                        <option value="2">Non-Tagged</option>
                        <option value="3">Old Gold</option>
                        <option value="4">Sales Return</option>
                        <option value="5">Partly Sold</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>Direction</label>
                      <select class="form-control" id="filter_direction">
                        <option value="">All</option>
                        <option value="13">Sales Transfer (Outward)</option>
                        <option value="14">Sales Return Transfer (Inward)</option>
                      </select>
                    </div>
                  </div>
                  <div class="row" style="margin-top:10px;">
                    <div class="col-md-12 text-right">
                      <button type="button" class="btn btn-primary" id="btn_filter_transit"><i class="fa fa-search"></i> Filter</button>
                      <button type="button" class="btn btn-default" id="btn_reset_filter"><i class="fa fa-refresh"></i> Reset</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Data Table -->
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-list"></i> In-Transit Items</h3>
                  <div class="pull-right">
                    <button type="button" class="btn btn-success btn-sm" id="btn_refresh_transit"><i class="fa fa-refresh"></i> Refresh</button>
                    <a class="btn btn-primary btn-sm" href="<?php echo base_url('index.php/admin_ret_sales_transfer/sales_transfer/list');?>"><i class="fa fa-arrow-left"></i> Back to List</a>
                  </div>
                </div>
                <div class="box-body">
                  <div class="table-responsive">
                    <table id="transit_list" class="table table-bordered table-striped text-center" style="width:100%">
                      <thead>
                        <tr>
                          <th width="8%">Bill No</th>
                          <th width="8%">Bill Date</th>
                          <th width="10%">From Branch</th>
                          <th width="10%">To Branch</th>
                          <th width="8%">Direction</th>
                          <th width="8%">Type</th>
                          <th width="6%">Items</th>
                          <th width="10%">Amount (₹)</th>
                          <th width="8%">Days In Transit</th>
                          <th width="10%">Status</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="overlay" id="transit_overlay" style="display:none">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
              </div>
            </div>
          </div>

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

<script>
$(document).ready(function () {
    var BASE_URL = '<?php echo base_url(); ?>';
    var typeLabels = {1: 'Tagged', 2: 'Non-Tagged', 3: 'Old Gold', 4: 'Sales Return', 5: 'Partly Sold'};
    var typeColors = {1: 'label-primary', 2: 'label-info', 3: 'label-warning', 4: 'label-danger', 5: 'label-default'};

    // Load branch dropdowns
    function loadBranches() {
        $.post(BASE_URL + 'index.php/admin_ret_sales_transfer/sales_transfer/getInTransitBranches', function (data) {
            var res = JSON.parse(data);
            if (res.branches) {
                var opts = '<option value="">All</option>';
                $.each(res.branches, function (i, b) {
                    opts += '<option value="' + b.id_branch + '">' + b.name + '</option>';
                });
                $('#filter_from_branch, #filter_to_branch').html(opts);
            }
        });
    }

    // Main data load
    function loadTransitData() {
        $('#transit_overlay').show();
        var postData = {
            from_branch: $('#filter_from_branch').val(),
            to_branch: $('#filter_to_branch').val(),
            trans_type: $('#filter_trans_type').val(),
            direction: $('#filter_direction').val()
        };

        $.post(BASE_URL + 'index.php/admin_ret_sales_transfer/sales_transfer/getInTransitList', postData, function (data) {
            var res = JSON.parse(data);
            $('#transit_overlay').hide();

            // Update KPIs
            $('#kpi_total_bills').text(res.kpi.total_bills);
            $('#kpi_total_items').text(res.kpi.total_items);
            $('#kpi_total_value').text('₹' + parseFloat(res.kpi.total_value).toLocaleString('en-IN', {minimumFractionDigits: 2}));
            $('#kpi_aging_count').text(res.kpi.aging_count);

            // Build table
            if ($.fn.DataTable.isDataTable('#transit_list')) {
                $('#transit_list').DataTable().destroy();
            }

            var rows = '';
            $.each(res.list, function (i, item) {
                var transType = parseInt(item.salesTransType) || 1;
                var label = typeLabels[transType] || 'Tagged';
                var color = typeColors[transType] || 'label-primary';
                var direction = item.bill_type == 14 ? '<span class="label label-danger">SRT ↩</span>' : '<span class="label label-success">ST →</span>';
                var daysInTransit = parseInt(item.days_in_transit) || 0;
                var agingClass = daysInTransit > 3 ? 'text-red' : (daysInTransit > 1 ? 'text-yellow' : 'text-green');

                rows += '<tr>' +
                    '<td>' + item.bill_no + '</td>' +
                    '<td>' + item.bill_date + '</td>' +
                    '<td>' + (item.from_branch_name || '-') + '</td>' +
                    '<td>' + (item.to_branch_name || '-') + '</td>' +
                    '<td>' + direction + '</td>' +
                    '<td><span class="label ' + color + '">' + label + '</span></td>' +
                    '<td>' + (item.item_count || 0) + '</td>' +
                    '<td class="text-right">' + parseFloat(item.tot_bill_amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td>' +
                    '<td class="' + agingClass + ' text-bold">' + daysInTransit + ' day' + (daysInTransit != 1 ? 's' : '') + '</td>' +
                    '<td><span class="label label-warning"><i class="fa fa-truck"></i> In Transit</span></td>' +
                    '</tr>';
            });

            $('#transit_list tbody').remove();
            $('#transit_list').append('<tbody>' + rows + '</tbody>');
            $('#transit_list').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[8, "desc"]],
                "pageLength": 25
            });
        });
    }

    // Event bindings
    $('#btn_filter_transit, #btn_refresh_transit').on('click', function () { loadTransitData(); });
    $('#btn_reset_filter').on('click', function () {
        $('#filter_from_branch, #filter_to_branch, #filter_trans_type, #filter_direction').val('');
        loadTransitData();
    });

    // Init
    loadBranches();
    loadTransitData();
});
</script>
