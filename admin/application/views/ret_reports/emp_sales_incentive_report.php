<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            Employee Sales Incentive
            <small>Report</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Employee Sales Incentive Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Employee Sales Incentive Report</h3>
                    </div>
                    <div class="box-body">

                        <!-- Filters -->
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" id="date_from" class="form-control"
                                           value="<?php echo date('Y-m-01'); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" id="date_to" class="form-control"
                                           value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select id="filter_branch" class="form-control select2">
                                        <option value="">All</option>
                                        <?php foreach ($branches as $b) { ?>
                                            <option value="<?php echo $b['id_branch']; ?>"><?php echo $b['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <select id="filter_employee" class="form-control select2">
                                        <option value="">All</option>
                                        <?php foreach ($employees as $e) { ?>
                                            <option value="<?php echo $e['id_employee']; ?>">
                                                <?php echo $e['emp_name'] . ' (' . $e['emp_code'] . ')'; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select id="filter_pay_status" class="form-control">
                                        <option value="">All</option>
                                        <option value="0" selected>Pending</option>
                                        <option value="1">Given</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btn_search" class="btn btn-info btn-block">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr/>

                        <!-- Summary Cards -->
                        <div class="row" id="summary_cards" style="display:none;">
                            <div class="col-md-3">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Employees</span>
                                        <span class="info-box-number" id="summary_employees">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Transactions</span>
                                        <span class="info-box-number" id="summary_transactions">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-inr"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending Incentive</span>
                                        <span class="info-box-number" id="summary_pending">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Given Incentive</span>
                                        <span class="info-box-number" id="summary_given">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="row" id="action_bar" style="display:none;">
                            <div class="col-md-12">
                                <button type="button" id="btn_mark_paid" class="btn btn-success btn-sm" disabled>
                                    <i class="fa fa-check"></i> Mark Selected as Given
                                </button>
                                <label style="margin-left:15px;">
                                    <input type="checkbox" id="chk_select_all"> Select All Pending
                                </label>
                                <span class="text-muted" style="margin-left:15px;">
                                    Selected: <strong id="selected_count">0</strong> |
                                    Amount: <strong>₹ <span id="selected_amount">0.00</span></strong>
                                </span>
                            </div>
                        </div>

                        <br/>

                        <!-- Report Table -->
                        <div class="table-responsive">
                            <table id="incentive_report_table" class="table table-bordered table-striped table-condensed" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width:30px;"><input type="checkbox" id="chk_header_all"></th>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Bill No</th>
                                        <th>Employee</th>
                                        <th>Branch</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Design</th>
                                        <th class="text-right">Net Wt (g)</th>
                                        <th class="text-right">Item Cost (₹)</th>
                                        <th>Calc Basis</th>
                                        <th class="text-right">Base Qty</th>
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Incentive (₹)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr class="info" style="font-weight:bold;">
                                        <td></td>
                                        <td colspan="9" class="text-right">Grand Total:</td>
                                        <td class="text-right" id="gt_item_cost">0.00</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right" id="gt_incentive">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div><!-- /.box-body -->
                    <div class="overlay" id="loading_overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
window.addEventListener('load', function () {
    var base_url = '<?php echo base_url(); ?>';
    var reportData = []; // Store loaded data for mark-paid

    // Init Select2
    $('.select2').select2({ allowClear: true, placeholder: 'Select...' });

    // Search
    $('#btn_search').click(function () {
        loadReport();
    });

    // Select All checkbox
    $('#chk_select_all, #chk_header_all').change(function () {
        var checked = $(this).is(':checked');
        // Sync both checkboxes
        $('#chk_select_all, #chk_header_all').prop('checked', checked);
        // Only check pending rows
        $('.chk_row').each(function () {
            var idx = $(this).data('index');
            if (reportData[idx] && reportData[idx].is_paid == 0) {
                $(this).prop('checked', checked);
            }
        });
        updateSelectedCount();
    });

    // Row checkbox change
    $(document).on('change', '.chk_row', function () {
        updateSelectedCount();
    });

    // Mark as Given button
    $('#btn_mark_paid').click(function () {
        var selected = [];
        $('.chk_row:checked').each(function () {
            var idx = $(this).data('index');
            var item = reportData[idx];
            if (item && item.is_paid == 0) {
                selected.push({
                    bill_id:          item.bill_id,
                    est_item_id:      item.est_item_id,
                    id_employee:      item.id_employee,
                    config_id:        item.config_id,
                    id_branch:        item.id_branch,
                    calc_basis:       0,
                    base_qty:         item.base_qty,
                    incentive_rate:   item.incentive_rate,
                    incentive_amount: item.incentive_amount,
                    bill_date:        item.bill_date
                });
            }
        });

        if (selected.length === 0) {
            toastr.warning('No pending items selected');
            return;
        }

        if (!confirm('Mark ' + selected.length + ' incentive(s) as Given?')) return;

        $('#loading_overlay').show();
        $.ajax({
            url: base_url + 'index.php/reports/emp_sales_incentive/mark_paid',
            type: 'POST',
            data: { items: selected },
            dataType: 'json',
            success: function (resp) {
                if (resp.status === 'success') {
                    toastr.success(resp.message);
                    loadReport(); // Reload
                } else {
                    toastr.error(resp.message || 'Error marking as given');
                }
                $('#loading_overlay').hide();
            },
            error: function () {
                toastr.error('Server error');
                $('#loading_overlay').hide();
            }
        });
    });

    function loadReport() {
        $('#loading_overlay').show();

        var filters = {
            date_from:   $('#date_from').val(),
            date_to:     $('#date_to').val(),
            id_branch:   $('#filter_branch').val(),
            id_employee: $('#filter_employee').val(),
            pay_status:  $('#filter_pay_status').val()
        };

        $.ajax({
            url: base_url + 'index.php/reports/emp_sales_incentive/ajax',
            type: 'POST',
            data: filters,
            dataType: 'json',
            success: function (response) {
                var list = response.list;
                reportData = list; // Store for mark-paid
                var rows = '';
                var totalItemCost = 0;
                var totalIncentive = 0;
                var totalPending = 0;
                var totalGiven = 0;
                var empSet = {};

                $.each(list, function (i, item) {
                    var itemCost = parseFloat(item.item_cost) || 0;
                    var incentiveAmt = parseFloat(item.incentive_amount) || 0;
                    var isPaid = parseInt(item.is_paid) || 0;

                    totalItemCost += itemCost;
                    totalIncentive += incentiveAmt;
                    empSet[item.id_employee] = true;

                    if (isPaid == 1) {
                        totalGiven += incentiveAmt;
                    } else {
                        totalPending += incentiveAmt;
                    }

                    var statusBadge = isPaid == 1
                        ? '<span class="label label-success">Given</span>'
                        : '<span class="label label-warning">Pending</span>';

                    var checkbox = isPaid == 0
                        ? '<input type="checkbox" class="chk_row" data-index="' + i + '">'
                        : '<i class="fa fa-check text-success"></i>';

                    var calcLabel = item.calc_basis_label || '-';
                    var baseQty = parseFloat(item.base_qty) || 0;
                    var rate = parseFloat(item.incentive_rate) || 0;

                    rows += '<tr class="' + (isPaid == 1 ? 'success' : '') + '">' +
                        '<td class="text-center">' + checkbox + '</td>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + formatDate(item.bill_date) + '</td>' +
                        '<td>' + (item.sales_ref_no || '-') + '</td>' +
                        '<td>' + item.emp_name + ' <small>(' + item.emp_code + ')</small></td>' +
                        '<td>' + item.branch_name + '</td>' +
                        '<td>' + item.category_name + '</td>' +
                        '<td>' + item.product_name + '</td>' +
                        '<td>' + item.design_name + '</td>' +
                        '<td class="text-right">' + parseFloat(item.net_wt || 0).toFixed(3) + '</td>' +
                        '<td class="text-right">' + itemCost.toFixed(2) + '</td>' +
                        '<td>' + calcLabel + '</td>' +
                        '<td class="text-right">' + baseQty.toFixed(4) + '</td>' +
                        '<td class="text-right">' + rate.toFixed(4) + '</td>' +
                        '<td class="text-right"><strong>' + incentiveAmt.toFixed(2) + '</strong></td>' +
                        '<td class="text-center">' + statusBadge + '</td>' +
                        '</tr>';
                });

                // Update table
                if ($('#incentive_report_table').hasClass('dataTable')) {
                    $('#incentive_report_table').DataTable().destroy();
                }
                $('#incentive_report_table tbody').html(rows);
                $('#incentive_report_table').DataTable({
                    "order": [],
                    "pageLength": 50,
                    "dom": 'Bfrtip',
                    "buttons": ['excel', 'pdf', 'print']
                });

                // Update totals
                $('#gt_item_cost').text(totalItemCost.toFixed(2));
                $('#gt_incentive').text(totalIncentive.toFixed(2));

                // Update summary cards
                $('#summary_employees').text(Object.keys(empSet).length);
                $('#summary_transactions').text(list.length);
                $('#summary_pending').text(totalPending.toFixed(2));
                $('#summary_given').text(totalGiven.toFixed(2));
                $('#summary_cards').show();
                $('#action_bar').show();

                // Reset selections
                $('#chk_select_all, #chk_header_all').prop('checked', false);
                updateSelectedCount();

                $('#loading_overlay').hide();
            },
            error: function () {
                $('#loading_overlay').hide();
                toastr.error('Error loading report data.');
            }
        });
    }

    function updateSelectedCount() {
        var count = 0;
        var amount = 0;
        $('.chk_row:checked').each(function () {
            var idx = $(this).data('index');
            var item = reportData[idx];
            if (item && item.is_paid == 0) {
                count++;
                amount += parseFloat(item.incentive_amount) || 0;
            }
        });
        $('#selected_count').text(count);
        $('#selected_amount').text(amount.toFixed(2));
        $('#btn_mark_paid').prop('disabled', count === 0);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        var parts = dateStr.split('-');
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }
});
</script>
