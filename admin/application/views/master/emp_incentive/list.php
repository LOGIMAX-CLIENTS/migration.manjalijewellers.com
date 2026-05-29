<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Employee Sales Incentive
            <small>Master Configuration</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Employee Sales Incentive</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Incentive Rules</h3>
                        <span id="total_count" class="badge bg-green"></span>
                        <span id="filtered_count" class="badge bg-yellow" style="display:none;"></span>
                        <div class="pull-right">
                            <a class="btn btn-success" href="<?php echo base_url('index.php/admin_emp_incentive/config_form/Add'); ?>">
                                <i class="fa fa-plus-circle"></i> Add Rule
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <br />
                        <div class="row">
                            <div class="col-xs-12">
                                <?php
                                if ($this->session->flashdata('chit_info')) {
                                    $message = $this->session->flashdata('chit_info');
                                ?>
                                    <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                                        <?php echo $message['message']; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Filter Panel -->
                        <div class="row" id="filter_panel">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <select id="filter_branch" class="form-control select2" multiple="multiple" data-placeholder="All Branches">
                                        <?php foreach ($branches as $b) { ?>
                                            <option value="<?php echo $b['id_branch']; ?>"><?php echo $b['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select id="filter_category" class="form-control select2" multiple="multiple" data-placeholder="All Categories">
                                        <?php foreach ($categories as $c) { ?>
                                            <option value="<?php echo $c['id_ret_category']; ?>"><?php echo $c['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Product</label>
                                    <select id="filter_product" class="form-control select2" multiple="multiple" data-placeholder="All Products">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Design</label>
                                    <select id="filter_design" class="form-control select2" multiple="multiple" data-placeholder="All Designs">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sub-Design</label>
                                    <select id="filter_sub_design" class="form-control select2" multiple="multiple" data-placeholder="All Sub-Designs">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select id="filter_status" class="form-control">
                                        <option value="">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btn_clear_filters" class="btn btn-default btn-block" title="Clear All Filters">
                                        <i class="fa fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- /Filter Panel -->

                        <div class="table-responsive">
                            <table id="incentive_config_list" class="table table-bordered table-striped text-center" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Branch</th>
                                        <th>Metal</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Design</th>
                                        <th>Sub-Design</th>
                                        <th>Calc Basis</th>
                                        <th>Rate Type</th>
                                        <th>Value</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="overlay" style="display:none">
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
    var allData = []; // Store full dataset for client-side filtering

    // Init Select2 for multi-selects
    $('#filter_branch, #filter_category, #filter_product, #filter_design, #filter_sub_design').select2({
        allowClear: true
    });

    // Load list
    loadConfigList();

    // ============================================================
    // CASCADING FILTER DROPDOWNS
    // ============================================================

    // Category change → load products
    $('#filter_category').on('change', function () {
        var catIds = $(this).val();
        $('#filter_product').html('').trigger('change');
        $('#filter_design').html('').trigger('change');
        $('#filter_sub_design').html('').trigger('change');

        if (catIds && catIds.length > 0) {
            // Load products for each selected category
            var allProducts = [];
            var remaining = catIds.length;
            $.each(catIds, function(i, catId) {
                $.post(base_url + 'index.php/admin_emp_incentive/get_products_by_category', { cat_id: catId }, function (data) {
                    if (typeof data === 'string') data = JSON.parse(data);
                    allProducts = allProducts.concat(data);
                    remaining--;
                    if (remaining === 0) {
                        // Remove duplicates by pro_id
                        var seen = {};
                        var unique = [];
                        $.each(allProducts, function(j, p) {
                            if (!seen[p.pro_id]) {
                                seen[p.pro_id] = true;
                                unique.push(p);
                            }
                        });
                        var opts = '';
                        $.each(unique, function(j, p) {
                            opts += '<option value="' + p.pro_id + '">' + p.product_name + '</option>';
                        });
                        $('#filter_product').html(opts);
                    }
                });
            });
        }
        applyFilters();
    });

    // Product change → load designs
    $('#filter_product').on('change', function () {
        var prodIds = $(this).val();
        $('#filter_design').html('').trigger('change');
        $('#filter_sub_design').html('').trigger('change');

        if (prodIds && prodIds.length > 0) {
            var allDesigns = [];
            var remaining = prodIds.length;
            $.each(prodIds, function(i, prodId) {
                $.post(base_url + 'index.php/admin_emp_incentive/get_designs_by_product', { product_id: prodId }, function (data) {
                    if (typeof data === 'string') data = JSON.parse(data);
                    allDesigns = allDesigns.concat(data);
                    remaining--;
                    if (remaining === 0) {
                        var seen = {};
                        var unique = [];
                        $.each(allDesigns, function(j, d) {
                            if (!seen[d.design_no]) {
                                seen[d.design_no] = true;
                                unique.push(d);
                            }
                        });
                        var opts = '';
                        $.each(unique, function(j, d) {
                            opts += '<option value="' + d.design_no + '">' + d.design_name + '</option>';
                        });
                        $('#filter_design').html(opts);
                    }
                });
            });
        }
        applyFilters();
    });

    // Design change → load sub-designs
    $('#filter_design').on('change', function () {
        var desIds = $(this).val();
        $('#filter_sub_design').html('').trigger('change');

        if (desIds && desIds.length > 0) {
            var prodIds = $('#filter_product').val();
            var firstProd = (prodIds && prodIds.length > 0) ? prodIds[0] : '';
            var allSubDesigns = [];
            var remaining = desIds.length;
            $.each(desIds, function(i, desId) {
                $.post(base_url + 'index.php/admin_emp_incentive/get_sub_designs_by_design', { design_id: desId, product_id: firstProd }, function (data) {
                    if (typeof data === 'string') data = JSON.parse(data);
                    allSubDesigns = allSubDesigns.concat(data);
                    remaining--;
                    if (remaining === 0) {
                        var seen = {};
                        var unique = [];
                        $.each(allSubDesigns, function(j, s) {
                            if (!seen[s.id_sub_design]) {
                                seen[s.id_sub_design] = true;
                                unique.push(s);
                            }
                        });
                        var opts = '';
                        $.each(unique, function(j, s) {
                            opts += '<option value="' + s.id_sub_design + '">' + s.sub_design_name + '</option>';
                        });
                        $('#filter_sub_design').html(opts);
                    }
                });
            });
        }
        applyFilters();
    });

    // Sub-Design change → just filter
    $('#filter_sub_design').on('change', function () {
        applyFilters();
    });

    // Branch change → filter
    $('#filter_branch').on('change', function () {
        applyFilters();
    });

    // Status change → filter
    $('#filter_status').on('change', function () {
        applyFilters();
    });

    // Clear all filters
    $('#btn_clear_filters').on('click', function () {
        $('#filter_branch').val(null).trigger('change');
        $('#filter_category').val(null).trigger('change');
        $('#filter_product').html('').val(null).trigger('change');
        $('#filter_design').html('').val(null).trigger('change');
        $('#filter_sub_design').html('').val(null).trigger('change');
        $('#filter_status').val('');
        applyFilters();
    });

    // ============================================================
    // DATA LOADING
    // ============================================================

    function loadConfigList() {
        $.ajax({
            url: base_url + 'index.php/admin_emp_incentive/ajax_get_configs',
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                allData = response.data;
                $('#total_count').text(allData.length + ' rules');
                applyFilters();
            }
        });
    }

    // ============================================================
    // CLIENT-SIDE FILTERING
    // ============================================================

    function applyFilters() {
        var fBranch    = $('#filter_branch').val()     || [];
        var fCategory  = $('#filter_category').val()   || [];
        var fProduct   = $('#filter_product').val()    || [];
        var fDesign    = $('#filter_design').val()     || [];
        var fSubDesign = $('#filter_sub_design').val() || [];
        var fStatus    = $('#filter_status').val();

        var filtered = [];

        $.each(allData, function (i, item) {

            // Status filter
            if (fStatus !== '' && String(item.status) !== fStatus) return true;

            // Branch filter — match if item's CSV branch IDs overlap with selected filter values
            if (fBranch.length > 0 && !csvOverlap(item.id_branch, fBranch)) return true;

            // Category filter
            if (fCategory.length > 0 && !csvOverlap(item.id_category, fCategory)) return true;

            // Product filter
            if (fProduct.length > 0 && !csvOverlap(item.id_product, fProduct)) return true;

            // Design filter
            if (fDesign.length > 0 && !csvOverlap(item.id_design, fDesign)) return true;

            // Sub-Design filter
            if (fSubDesign.length > 0 && !csvOverlap(item.id_sub_design, fSubDesign)) return true;

            filtered.push(item);
        });

        renderTable(filtered);

        // Show filtered count if filtering is active
        var isFiltering = fBranch.length > 0 || fCategory.length > 0 || fProduct.length > 0 ||
                          fDesign.length > 0 || fSubDesign.length > 0 || fStatus !== '';
        if (isFiltering) {
            $('#filtered_count').text('Showing ' + filtered.length + ' of ' + allData.length).show();
        } else {
            $('#filtered_count').hide();
        }
    }

    /**
     * Check if a comma-separated DB value overlaps with the selected filter array.
     * If DB value is null/empty (meaning "All"), it matches any filter.
     */
    function csvOverlap(csvValue, filterArr) {
        if (!csvValue || csvValue === '' || csvValue === null) return true; // "All" matches everything
        var dbIds = String(csvValue).split(',');
        for (var i = 0; i < dbIds.length; i++) {
            if (filterArr.indexOf(dbIds[i].trim()) !== -1) return true;
        }
        return false;
    }

    // ============================================================
    // TABLE RENDERING
    // ============================================================

    function renderTable(data) {
        var rows = '';

        $.each(data, function (i, item) {
            var statusBadge = item.status == 1
                ? '<span class="label label-success btn-toggle-status" data-id="' + item.id + '" style="cursor:pointer" title="Click to deactivate">Active</span>'
                : '<span class="label label-danger btn-toggle-status" data-id="' + item.id + '" style="cursor:pointer" title="Click to activate">Inactive</span>';

            var valueDisplay = '';
            if (item.rate_type == '1' || item.rate_type == '4') {
                valueDisplay = parseFloat(item.incentive_value).toFixed(2);
            } else {
                valueDisplay = '<span class="text-info">Range Based</span>';
            }

            var actions = '<a href="' + base_url + 'index.php/admin_emp_incentive/config_form/Edit/' + item.id + '" class="btn btn-xs btn-primary" title="Edit"><i class="fa fa-pencil"></i></a>';

            rows += '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + item.branch_name + '</td>' +
                '<td>' + item.metal_name + '</td>' +
                '<td>' + item.category_name + '</td>' +
                '<td>' + item.product_name + '</td>' +
                '<td>' + item.design_name + '</td>' +
                '<td>' + item.sub_design_name + '</td>' +
                '<td>' + item.calc_basis_label + '</td>' +
                '<td>' + item.rate_type_label + '</td>' +
                '<td>' + valueDisplay + '</td>' +
                '<td>' + statusBadge + '</td>' +
                '<td>' + actions + '</td>' +
                '</tr>';
        });

        if ($('#incentive_config_list').hasClass('dataTable')) {
            $('#incentive_config_list').DataTable().destroy();
        }
        $('#incentive_config_list tbody').html(rows);
        $('#incentive_config_list').DataTable({
            "order": [],
            "pageLength": 25
        });
    }

    // ============================================================
    // TOGGLE STATUS
    // ============================================================

    $(document).on('click', '.btn-toggle-status', function () {
        var $badge = $(this);
        var id = $badge.data('id');
        var currentLabel = $badge.text().trim();
        var confirmMsg = currentLabel === 'Active'
            ? 'Are you sure you want to deactivate this rule?'
            : 'Are you sure you want to activate this rule?';

        if (!confirm(confirmMsg)) return;

        $.ajax({
            url: base_url + 'index.php/admin_emp_incentive/ajax_toggle_status',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    // Update the data store
                    for (var i = 0; i < allData.length; i++) {
                        if (allData[i].id == id) {
                            allData[i].status = res.new_status;
                            break;
                        }
                    }
                    // Re-apply filters to refresh the table with correct S.No
                    applyFilters();
                } else {
                    alert(res.message);
                }
            }
        });
    });
});
</script>
