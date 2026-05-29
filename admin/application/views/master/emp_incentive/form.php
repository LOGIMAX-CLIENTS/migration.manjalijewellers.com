<style>
.select2-container { width: 100% !important; }
input[type="number"].form-control { text-align: right; }

/* Compact form styling */
.scope-selection-fields .form-group {
    margin-bottom: 8px;
}

.scope-selection-fields label {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 3px;
}

.scope-selection-fields.select2-container--default {
    width: 100% !important;
}

.select2-container--default .select2-selection--single {
    height: 32px;
    border-radius: 3px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 32px;
    padding: 0 6px;
    font-size: 12px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}

/* Multi-select compact styling */
.select2-container--default .select2-selection--multiple {
    min-height: 32px;
    border-radius: 3px;
    font-size: 12px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    font-size: 11px;
    padding: 1px 5px;
    margin-top: 3px;
}

.select2-container--default .select2-selection--multiple .select2-selection__rendered {
    padding: 0 4px;
}

.select2-dropdown--below {
    margin-top: -4px;
}

/* Small input field for fixed value */
.form-control-sm {
    height: 32px !important;
    font-size: 12px;
    padding: 4px 8px !important;
}

/* Error message styling */
#range_error_msg {
    font-size: 12px;
    padding: 8px 12px;
}

/* Range row styling */
.range_row input.form-control {
    height: 32px;
    font-size: 12px;
}

/* Action buttons styling */
.range_row .btn {
    padding: 2px 6px;
    font-size: 11px;
}

/* Batch items preview table */
#batch_items_section {
    margin-top: 15px;
}
#batch_items_table {
    font-size: 12px;
}
#batch_items_table th {
    background: #3c8dbc;
    color: #fff;
    font-size: 11px;
    padding: 6px 8px;
}
#batch_items_table td {
    padding: 5px 8px;
    vertical-align: middle;
}
#batch_items_table .badge {
    font-size: 10px;
}
.batch-count-badge {
    font-size: 14px;
    padding: 4px 10px;
    margin-left: 8px;
}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            Employee Sales Incentive
            <small><?php echo $process_type; ?> Rule</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li><a href="<?php echo base_url('index.php/admin_emp_incentive/config_list'); ?>">Employee Sales Incentive</a></li>
            <li class="active"><?php echo $process_type; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form id="incentive_form" method="post"
                      action="<?php echo base_url('index.php/admin_emp_incentive/config_post/' . $process_type . '/' . $config['id']); ?>">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-cogs"></i> Incentive Rule Configuration</h3>
                        </div>
                        <div class="box-body">

                            <!-- ===== SCOPE SELECTION ===== -->
                            <fieldset>
                                <legend><i class="fa fa-filter"></i> Scope Selection</legend>

                                <div class="row scope-selection-fields">
                                    <!-- Branch -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_branch">Branch</label>
                                            <select name="config[id_branch][]" id="id_branch" class="form-control select2" multiple="multiple">
                                                <?php 
                                                $selected_branches = !empty($config['id_branch']) ? (is_array($config['id_branch']) ? $config['id_branch'] : explode(',', $config['id_branch'])) : array();
                                                foreach ($branches as $b) { ?>
                                                    <option value="<?php echo $b['id_branch']; ?>"
                                                        <?php echo in_array($b['id_branch'], $selected_branches) ? 'selected' : ''; ?>>
                                                        <?php echo $b['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Metal -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_metal">Metal</label>
                                            <select name="config[id_metal][]" id="id_metal" class="form-control select2" multiple="multiple">
                                                <?php 
                                                $selected_metals = !empty($config['id_metal']) ? (is_array($config['id_metal']) ? $config['id_metal'] : explode(',', $config['id_metal'])) : array();
                                                foreach ($metals as $m) { ?>
                                                    <option value="<?php echo $m['id_metal']; ?>"
                                                        <?php echo in_array($m['id_metal'], $selected_metals) ? 'selected' : ''; ?>>
                                                        <?php echo $m['metal']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_category">Category</label>
                                            <select name="config[id_category][]" id="id_category" class="form-control select2" multiple="multiple">
                                                <?php 
                                                $selected_categories = !empty($config['id_category']) ? (is_array($config['id_category']) ? $config['id_category'] : explode(',', $config['id_category'])) : array();
                                                foreach ($categories as $c) { ?>
                                                    <option value="<?php echo $c['id_ret_category']; ?>"
                                                        <?php echo in_array($c['id_ret_category'], $selected_categories) ? 'selected' : ''; ?>>
                                                        <?php echo $c['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Product -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_product">Product</label>
                                            <select name="config[id_product][]" id="id_product" class="form-control select2" multiple="multiple">
                                                <?php 
                                                $selected_products = !empty($config['id_product']) ? (is_array($config['id_product']) ? $config['id_product'] : explode(',', $config['id_product'])) : array();
                                                foreach ($products as $p) { ?>
                                                    <option value="<?php echo $p['pro_id']; ?>"
                                                        <?php echo in_array($p['pro_id'], $selected_products) ? 'selected' : ''; ?>>
                                                        <?php echo $p['product_name'] . ' (' . $p['product_short_code'] . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Design -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_design">Design</label>
                                            <select name="config[id_design][]" id="id_design" class="form-control select2" multiple="multiple">
                                                <?php 
                                                $selected_designs = !empty($config['id_design']) ? (is_array($config['id_design']) ? $config['id_design'] : explode(',', $config['id_design'])) : array();
                                                foreach ($designs as $d) { ?>
                                                    <option value="<?php echo $d['design_no']; ?>"
                                                        <?php echo in_array($d['design_no'], $selected_designs) ? 'selected' : ''; ?>>
                                                        <?php echo $d['design_name'] . ' (' . $d['design_code'] . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Sub-Design -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_sub_design">Sub-Design</label>
                                            <select name="config[id_sub_design][]" id="id_sub_design" class="form-control select2" multiple="multiple">
                                                <?php 
                                                $selected_sub_designs = !empty($config['id_sub_design']) ? (is_array($config['id_sub_design']) ? $config['id_sub_design'] : explode(',', $config['id_sub_design'])) : array();
                                                foreach ($sub_designs as $sd) { ?>
                                                    <option value="<?php echo $sd['id_sub_design']; ?>"
                                                        <?php echo in_array($sd['id_sub_design'], $selected_sub_designs) ? 'selected' : ''; ?>>
                                                        <?php echo $sd['sub_design_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-2" style="display:none;">
                                        <div class="form-group">
                                            <label for="config_status">Status</label>
                                            <div class="btn-group" data-toggle="buttons" style="width: 100%;">
                                                <label class="btn btn-default btn-sm active" style="width: 50%;">
                                                    <input type="radio" name="config[status]" value="1"
                                                        <?php echo ($config['status'] == 1) ? 'checked' : ''; ?>>
                                                    Active
                                                </label>
                                                <label class="btn btn-default btn-sm" style="width: 50%;">
                                                    <input type="radio" name="config[status]" value="0"
                                                        <?php echo ($config['status'] == 0) ? 'checked' : ''; ?>>
                                                    Inactive
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <hr/>

                            <!-- ===== CALCULATION SETTINGS ===== -->
                            <fieldset>
                                <legend><i class="fa fa-calculator"></i> Calculation Settings</legend>

                                <div class="row">
                                    <!-- Calculation Basis -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Calculation Basis</label>
                                            <div class="radio-group">
                                                <label class="radio-inline">
                                                    <input type="radio" name="config[calc_basis]" value="1"
                                                        <?php echo ($config['calc_basis'] == 1) ? 'checked' : ''; ?>>
                                                    <strong>Per Gram</strong>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="config[calc_basis]" value="2"
                                                        <?php echo ($config['calc_basis'] == 2) ? 'checked' : ''; ?>>
                                                    <strong>Per Carat</strong>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="config[calc_basis]" value="3"
                                                        <?php echo ($config['calc_basis'] == 3) ? 'checked' : ''; ?>>
                                                    <strong>% of Total Sales Value</strong>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="config[calc_basis]" value="4"
                                                        <?php echo ($config['calc_basis'] == 4) ? 'checked' : ''; ?>>
                                                    <strong>Age Based</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rate Type -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Rate Type</label>
                                            <div class="radio-group">
                                                <label class="radio-inline">
                                                    <input type="radio" name="config[rate_type]" value="1" class="rate_type_radio"
                                                        <?php echo ($config['rate_type'] == 1) ? 'checked' : ''; ?>>
                                                    <strong>Fixed</strong>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="config[rate_type]" value="2" class="rate_type_radio"
                                                        <?php echo ($config['rate_type'] == 2) ? 'checked' : ''; ?>>
                                                    <strong>Weight Range</strong>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="config[rate_type]" value="3" class="rate_type_radio"
                                                        <?php echo ($config['rate_type'] == 3) ? 'checked' : ''; ?>>
                                                    <strong>Carat Range</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Age Master Selection (Age Based) -->
                                <div class="row" id="age_master_selection_row" style="<?php echo ($config['calc_basis'] != 4) ? 'display:none;' : ''; ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_stock_age_master">Select Age Range <span class="text-danger">*</span></label>
                                            <select name="config[id_stock_age_master]" id="id_stock_age_master" class="form-control select2">
                                                <option value="">-- Select Age Range --</option>
                                                <?php 
                                                if (!empty($stock_age_masters)) {
                                                    foreach ($stock_age_masters as $key => $label) {
                                                        $selected = ($config['id_stock_age_master'] == $key) ? 'selected' : '';
                                                        echo '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fixed Value Input -->
                                <div class="row" id="fixed_value_row" style="<?php echo ($config['rate_type'] != 1) ? 'display:none;' : ''; ?>">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label id="fixed_value_label">Incentive Value</label>
                                            <input type="number" step="any" min="0" name="config[incentive_value]" id="incentive_value"
                                                   class="form-control form-control-sm" placeholder="Enter value"
                                                   value="<?php echo $config['incentive_value']; ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Stone Type Selection (Carat Range only) -->
                                <div class="row" id="stone_type_row" style="<?php echo ($config['rate_type'] != 3) ? 'display:none;' : ''; ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="stone_type">Stone Type <span class="text-danger">*</span></label>
                                            <select name="config[stone_type]" id="stone_type" class="form-control select2">
                                                <option value="">-- Select Stone Type --</option>
                                                <?php 
                                                if (!empty($stone_types)) {
                                                    foreach ($stone_types as $st) {
                                                        $selected = ($config['stone_type'] == $st['id_stone_type']) ? 'selected' : '';
                                                        echo '<option value="' . $st['id_stone_type'] . '" ' . $selected . '>' . $st['stone_type'] . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range Slabs Table -->
                                <div id="range_slabs_section" style="<?php echo ($config['rate_type'] == 1 || empty($config['rate_type'])) ? 'display:none;' : ''; ?>">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h4>
                                                <span id="range_type_label">Range</span> Slabs
                                            </h4>
                                            <div id="range_error_msg" class="alert alert-danger" style="display:none; margin-bottom:10px;">
                                                <strong>Error:</strong> <span id="error_text"></span>
                                            </div>
                                            <table class="table table-bordered table-striped" id="range_table">
                                                <thead>
                                                    <tr>
                                                        <th width="30%" id="from_label">From</th>
                                                        <th width="30%" id="to_label">To</th>
                                                        <th width="25%">Incentive Value</th>
                                                        <th width="15%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (!empty($config['ranges'])) {
                                                        foreach ($config['ranges'] as $idx => $range) {
                                                            echo '<tr class="range_row">';
                                                            echo '<td><input type="number" step="any" min="0" name="config[ranges][' . $idx . '][range_from]" class="form-control range_from_input" value="' . $range['range_from'] . '"></td>';
                                                            echo '<td><input type="number" step="any" min="0" name="config[ranges][' . $idx . '][range_to]" class="form-control range_to_input" value="' . $range['range_to'] . '"></td>';
                                                            echo '<td><input type="number" step="any" min="0" name="config[ranges][' . $idx . '][incentive_value]" class="form-control" value="' . $range['incentive_value'] . '"></td>';
                                                            echo '<td>';
                                                            echo '<button type="button" class="btn btn-xs btn-success add_range_row" title="Add Slab"><i class="fa fa-plus"></i></button> ';
                                                            echo '<button type="button" class="btn btn-xs btn-danger remove_range_row" title="Delete"><i class="fa fa-trash"></i></button>';
                                                            echo '</td>';
                                                            echo '</tr>';
                                                        }
                                                    } else {
                                                        // Show one empty row initially (no delete button for first row)
                                                        echo '<tr class="range_row first_range_row">';
                                                        echo '<td><input type="number" step="any" min="0" name="config[ranges][0][range_from]" class="form-control range_from_input" placeholder=""></td>';
                                                        echo '<td><input type="number" step="any" min="0" name="config[ranges][0][range_to]" class="form-control range_to_input" placeholder=""></td>';
                                                        echo '<td><input type="number" step="any" min="0" name="config[ranges][0][incentive_value]" class="form-control" placeholder="Value"></td>';
                                                        echo '<td>';
                                                        echo '<button type="button" class="btn btn-xs btn-success add_range_row" title="Add Slab"><i class="fa fa-plus"></i></button>';
                                                        echo '</td>';
                                                        echo '</tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <a href="<?php echo base_url('index.php/admin_emp_incentive/config_list'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <?php if ($process_type == 'Add') { ?>
                                <button type="button" class="btn btn-info pull-right" id="btn_add_item">
                                    <i class="fa fa-plus-circle"></i> Add Item
                                </button>
                            <?php } else { ?>
                                <button type="submit" class="btn btn-primary pull-right" id="btn_save">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            <?php } ?>
                        </div>
                    </div><!-- /.box -->
                </form>

                <?php if ($process_type == 'Add') { ?>
                <!-- Batch Items Preview -->
                <div class="box box-success" id="batch_items_section" style="display:none;">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list"></i> Added Items</h3>
                        <span class="batch-count-badge badge bg-green" id="batch_count">0</span>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="batch_items_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
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
                    <div class="box-footer">
                        <button type="button" class="btn btn-primary pull-right" id="btn_save_all">
                            <i class="fa fa-save"></i> Save All
                        </button>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<script>
window.addEventListener('load', function () {
    var base_url = '<?php echo base_url(); ?>';
    var rangeIndex = <?php echo !empty($config['ranges']) ? count($config['ranges']) : 1; ?>;

    // Init Select2
    $('.select2').select2({ allowClear: true, placeholder: 'Select...' });
    // Re-init multi-selects with proper placeholder
    $('#id_branch, #id_metal, #id_category, #id_product, #id_design, #id_sub_design').select2({ placeholder: '-- All --', allowClear: true });

    // Initialize range table headers based on current rate_type
    var currentRateType = $('input[name="config[rate_type]"]:checked').val();
    if (currentRateType == '2') {
        $('#range_type_label').text('Weight');
        $('#from_label').text('Weight From');
        $('#to_label').text('Weight To');
    } else if (currentRateType == '3') {
        $('#range_type_label').text('Carat');
        $('#from_label').text('Carat From');
        $('#to_label').text('Carat To');
    }

    // ===== Cascading Dropdowns =====

    // Metal -> Category (filter categories by selected metal)
    $('#id_metal').change(function () {
        var metal_ids = $(this).val(); // returns array for multi-select
        $('#id_category').html('').trigger('change.select2');
        $('#id_product').html('').trigger('change.select2');
        $('#id_design').html('').trigger('change.select2');
        $('#id_sub_design').html('').trigger('change.select2');

        if (metal_ids && metal_ids.length) {
            $.post(base_url + 'index.php/admin_emp_incentive/get_categories_by_metal', { metal_id: metal_ids.join(',') }, function (data) {
                var categories = JSON.parse(data);
                $.each(categories, function (j, c) {
                    $('#id_category').append('<option value="' + c.id_ret_category + '">' + c.name + '</option>');
                });
                $('#id_category').trigger('change.select2');
            });
        }
    });

    // Category -> Product (multi-select aware)
    $('#id_category').change(function () {
        var cat_ids = $(this).val(); // returns array for multi-select
        $('#id_product').html('').trigger('change.select2');
        $('#id_design').html('').trigger('change.select2');
        $('#id_sub_design').html('').trigger('change.select2');

        if (cat_ids && cat_ids.length) {
            // Load products for all selected categories
            $.each(cat_ids, function(i, cat_id) {
                $.post(base_url + 'index.php/admin_emp_incentive/get_products_by_category', { cat_id: cat_id }, function (data) {
                    var products = JSON.parse(data);
                    $.each(products, function (j, p) {
                        // Avoid duplicates
                        if ($('#id_product option[value="' + p.pro_id + '"]').length === 0) {
                            $('#id_product').append('<option value="' + p.pro_id + '">' + p.product_name + ' (' + p.product_short_code + ')</option>');
                        }
                    });
                    $('#id_product').trigger('change.select2');
                });
            });
        }
    });

    // Product -> Design (multi-select aware)
    $('#id_product').change(function () {
        var product_ids = $(this).val();
        $('#id_design').html('').trigger('change.select2');
        $('#id_sub_design').html('').trigger('change.select2');

        if (product_ids && product_ids.length) {
            $.each(product_ids, function(i, product_id) {
                $.post(base_url + 'index.php/admin_emp_incentive/get_designs_by_product', { product_id: product_id }, function (data) {
                    var designs = JSON.parse(data);
                    $.each(designs, function (j, d) {
                        if ($('#id_design option[value="' + d.design_no + '"]').length === 0) {
                            $('#id_design').append('<option value="' + d.design_no + '">' + d.design_name + ' (' + d.design_code + ')</option>');
                        }
                    });
                    $('#id_design').trigger('change.select2');
                });
            });
        }
    });

    // Design -> Sub-Design (multi-select aware)
    $('#id_design').change(function () {
        var design_ids = $(this).val();
        var product_ids = $('#id_product').val();
        $('#id_sub_design').html('').trigger('change.select2');

        if (design_ids && design_ids.length) {
            $.each(design_ids, function(i, design_id) {
                var pid = (product_ids && product_ids.length) ? product_ids[0] : '';
                $.post(base_url + 'index.php/admin_emp_incentive/get_sub_designs_by_design', { design_id: design_id, product_id: pid }, function (data) {
                    var sub_designs = JSON.parse(data);
                    $.each(sub_designs, function (j, sd) {
                        if ($('#id_sub_design option[value="' + sd.id_sub_design + '"]').length === 0) {
                            $('#id_sub_design').append('<option value="' + sd.id_sub_design + '">' + sd.sub_design_name + '</option>');
                        }
                    });
                    $('#id_sub_design').trigger('change.select2');
                });
            });
        }
    });

    // ===== Rate Type Toggle =====

    $('input[name="config[rate_type]"]').change(function () {
        var val = $(this).val();
        if (val == '1') {
            $('#fixed_value_row').show();
            $('#range_slabs_section').hide();
            $('#stone_type_row').hide();
        } else {
            $('#fixed_value_row').hide();
            $('#range_slabs_section').show();
            if (val == '2') {
                $('#range_type_label').text('Weight');
                $('#from_label').text('Weight From');
                $('#to_label').text('Weight To');
                $('#stone_type_row').hide();
            } else {
                $('#range_type_label').text('Carat');
                $('#from_label').text('Carat From');
                $('#to_label').text('Carat To');
                $('#stone_type_row').show();
            }
        }
    });

    // ===== Calc Basis Toggle (Age Based) =====

    $('input[name="config[calc_basis]"]').change(function () {
        var val = $(this).val();
        if (val == '4') {
            // Age Based selected — show age range dropdown
            $('#age_master_selection_row').show();
        } else {
            $('#age_master_selection_row').hide();
        }
        // Update help text
        switch(val) {
            case '1': $('#value_help_text').text('Rate per Gram'); break;
            case '2': $('#value_help_text').text('Rate per Carat'); break;
            case '3': $('#value_help_text').text('Percentage of Total Sales Value'); break;
            case '4': $('#value_help_text').text('Age Based Incentive'); break;
        }
    });

    // ===== Dynamic Range Rows =====

    // Validate range slab for overlaps (same logic as Stock Age Master)
    // Two ranges overlap if: newFrom < existingTo AND newTo > existingFrom
    function validateRangeOverlap(fromInput, toInput, currentRow) {
        var fromVal = parseFloat(fromInput.val());
        var toVal = parseFloat(toInput.val());
        
        if (isNaN(fromVal) || isNaN(toVal)) return { error: false };

        // from must be less than to
        if (fromVal >= toVal) {
            return { error: true, message: 'Range "From" value must be less than "To" value.' };
        }

        var overlapFound = false;
        var overlapRange = '';
        
        $('#range_table tbody tr').each(function() {
            if ($(this).is(currentRow)) return; // Skip current row
            
            var existingFrom = parseFloat($(this).find('.range_from_input').val());
            var existingTo = parseFloat($(this).find('.range_to_input').val());

            if (isNaN(existingFrom) || isNaN(existingTo)) return;

            // Overlap check (inclusive boundaries): newFrom <= existingTo AND newTo >= existingFrom
            if (fromVal <= existingTo && toVal >= existingFrom) {
                overlapFound = true;
                overlapRange = existingFrom + ' - ' + existingTo;
                return false; // break
            }
        });

        if (overlapFound) {
            return { error: true, message: 'Range ' + fromVal + '-' + toVal + ' overlaps with existing range ' + overlapRange + '. Overlapping ranges are not allowed.' };
        }
        
        return { error: false };
    }

    // Validate ALL slabs in the table for overlaps (called on Add Item)
    function validateAllSlabs() {
        var rows = $('#range_table tbody tr');
        if (rows.length <= 1) return { valid: true };

        var slabs = [];
        rows.each(function() {
            var f = parseFloat($(this).find('.range_from_input').val());
            var t = parseFloat($(this).find('.range_to_input').val());
            if (!isNaN(f) && !isNaN(t)) {
                slabs.push({ from: f, to: t });
            }
        });

        for (var i = 0; i < slabs.length; i++) {
            if (slabs[i].from >= slabs[i].to) {
                return { valid: false, message: 'Slab ' + (i+1) + ': "From" (' + slabs[i].from + ') must be less than "To" (' + slabs[i].to + ').' };
            }
            for (var j = i + 1; j < slabs.length; j++) {
                if (slabs[i].from <= slabs[j].to && slabs[i].to >= slabs[j].from) {
                    return { valid: false, message: 'Slab ' + slabs[i].from + '-' + slabs[i].to + ' overlaps with ' + slabs[j].from + '-' + slabs[j].to + '. Overlapping ranges are not allowed.' };
                }
            }
        }
        return { valid: true };
    }

    // Add blur event to detect overlapping ranges in real-time
    $(document).on('blur', '.range_from_input, .range_to_input', function () {
        var row = $(this).closest('tr');
        var fromInput = row.find('.range_from_input');
        var toInput = row.find('.range_to_input');
        
        if (fromInput.val() && toInput.val()) {
            var result = validateRangeOverlap(fromInput, toInput, row);
            if (result.error) {
                $('#error_text').text(result.message);
                $('#range_error_msg').show();
                
                // Clear current row values
                row.find('input').val('');
                
                // Auto-hide error after 5 seconds
                setTimeout(function() {
                    $('#range_error_msg').fadeOut();
                }, 5000);
            } else {
                $('#range_error_msg').hide();
            }
        }
    });

    // Add Slab button click handler
    $(document).on('click', '.add_range_row', function (e) {
        e.preventDefault();
        var rateType = $('input[name="config[rate_type]"]:checked').val();
        var placeholder = rateType == '2' ? 'Weight' : 'Carat';
        var row = '<tr class="range_row">' +
            '<td><input type="number" step="any" min="0" name="config[ranges][' + rangeIndex + '][range_from]" class="form-control range_from_input" placeholder="' + placeholder + ' From"></td>' +
            '<td><input type="number" step="any" min="0" name="config[ranges][' + rangeIndex + '][range_to]" class="form-control range_to_input" placeholder="' + placeholder + ' To"></td>' +
            '<td><input type="number" step="any" min="0" name="config[ranges][' + rangeIndex + '][incentive_value]" class="form-control" placeholder="Value"></td>' +
            '<td>' +
            '<button type="button" class="btn btn-xs btn-success add_range_row" title="Add Slab"><i class="fa fa-plus"></i></button> ' +
            '<button type="button" class="btn btn-xs btn-danger remove_range_row" title="Delete"><i class="fa fa-trash"></i></button>' +
            '</td>' +
            '</tr>';
        $('#range_table tbody').append(row);
        rangeIndex++;
    });

    $(document).on('click', '.remove_range_row', function () {
        $(this).closest('tr').remove();
    });

    // Block negative values and alphabets in number fields
    $(document).on('keydown', 'input[type="number"]', function (e) {
        // Allow: backspace, delete, tab, escape, enter, decimal point
        if ([46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
            // Allow: Ctrl/Cmd+A,C,V,X
            (e.keyCode >= 65 && e.keyCode <= 90 && (e.ctrlKey || e.metaKey)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            return;
        }
        // Block: minus, plus, 'e'
        if (e.keyCode === 189 || e.keyCode === 187 || e.keyCode === 69) {
            e.preventDefault();
            return;
        }
        // Block anything that is not a number (0-9 top row or numpad)
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // ===== BATCH MODE (Add) / SINGLE MODE (Edit) =====
    var processType = '<?php echo $process_type; ?>';
    var batchItems = [];

    // Helper: get display labels for selected multi-select options
    function getSelectedLabels(selectId) {
        var $sel = $(selectId);
        var selected = $sel.find('option:selected');
        if (!selected.length) return 'All';
        return selected.map(function() { return $(this).text(); }).get().join(', ');
    }
    function getSelectedValues(selectId) {
        var vals = $(selectId).val();
        return (vals && vals.length) ? vals : [];
    }

    // Helper: get calc basis / rate type label
    function getCalcBasisLabel(v) {
        var map = {'1':'Per Gram', '2':'Per Carat', '3':'% of Sales Value', '4':'Age Based'};
        return map[v] || '';
    }
    function getRateTypeLabel(v) {
        var map = {'1':'Fixed', '2':'Weight Range', '3':'Carat Range'};
        return map[v] || '';
    }

    // Validate current form inputs
    function validateFormInputs() {
        var rateType = $('input[name="config[rate_type]"]:checked').val();
        var calcBasis = $('input[name="config[calc_basis]"]:checked').val();

        // Validate age-based fields when calc_basis = Age Based
        if (calcBasis == '4') {
            var ageRange = $('#id_stock_age_master').val();
            if (!ageRange) { alert('Please select an age range.'); return false; }
        }

        // Validate stone type for Carat Range
        if (rateType == '3') {
            var stoneType = $('#stone_type').val();
            if (!stoneType) {
                alert('Please select a Stone Type for Carat Range.');
                return false;
            }
        }

        // Validate rate type fields
        if (rateType == '1') {
            var val = $('#incentive_value').val();
            if (!val || parseFloat(val) <= 0) {
                alert('Please enter a valid incentive value.');
                return false;
            }
        } else {
            var rows = $('#range_table tbody tr');
            if (rows.length === 0) {
                alert('Please add at least one range slab.');
                return false;
            }
            // Check for overlapping slabs
            var slabCheck = validateAllSlabs();
            if (!slabCheck.valid) {
                alert(slabCheck.message);
                return false;
            }
            // Check all slabs have values filled
            var incomplete = false;
            rows.each(function() {
                var f = $(this).find('.range_from_input').val();
                var t = $(this).find('.range_to_input').val();
                var v = $(this).find('input:last').val();
                if (!f || !t || !v) { incomplete = true; return false; }
            });
            if (incomplete) {
                alert('Please fill in all range slab fields (From, To, Value).');
                return false;
            }
        }
        return true;
    }

    // Capture current form state into an item object
    function captureItem() {
        var rateType = $('input[name="config[rate_type]"]:checked').val();
        var calcBasis = $('input[name="config[calc_basis]"]:checked').val();
        var status = $('input[name="config[status]"]:checked').val();

        var item = {
            id_branch: getSelectedValues('#id_branch'),
            id_metal: getSelectedValues('#id_metal'),
            id_category: getSelectedValues('#id_category'),
            id_product: getSelectedValues('#id_product'),
            id_design: getSelectedValues('#id_design'),
            id_sub_design: getSelectedValues('#id_sub_design'),
            calc_basis: calcBasis,
            rate_type: rateType,
            incentive_value: 0,
            id_stock_age_master: null,
            status: status || '1',
            ranges: [],
            // Display labels
            _branch_label: getSelectedLabels('#id_branch'),
            _metal_label: getSelectedLabels('#id_metal'),
            _category_label: getSelectedLabels('#id_category'),
            _product_label: getSelectedLabels('#id_product'),
            _design_label: getSelectedLabels('#id_design'),
            _sub_design_label: getSelectedLabels('#id_sub_design'),
            _calc_basis_label: getCalcBasisLabel(calcBasis),
            _rate_type_label: getRateTypeLabel(rateType),
            _value_display: '',
            stone_type: (rateType == '3') ? ($('#stone_type').val() || '') : '',
            _stone_type_label: (rateType == '3') ? ($('#stone_type option:selected').text() || '') : ''
        };

        if (rateType == '1') {
            item.incentive_value = parseFloat($('#incentive_value').val()) || 0;
            item._value_display = item.incentive_value.toFixed(2);
        } else {
            // Collect range slabs
            var rangeRows = [];
            $('#range_table tbody tr').each(function() {
                var rf = $(this).find('.range_from_input').val();
                var rt = $(this).find('.range_to_input').val();
                var rv = $(this).find('input:last').val();
                if (rf !== '' && rt !== '' && rv !== '') {
                    rangeRows.push({ range_from: rf, range_to: rt, incentive_value: rv });
                }
            });
            item.ranges = rangeRows;
            item._value_display = rangeRows.length + ' slab(s)';
            // Append stone type for Carat Range
            if (rateType == '3' && item._stone_type_label) {
                item._value_display += ' | ' + item._stone_type_label;
            }
        }

        // Capture age-based data when calc_basis = Age Based
        if (calcBasis == '4') {
            item.id_stock_age_master = $('#id_stock_age_master').val();
            item._value_display += (item._value_display ? ' | ' : '') + 'Age: ' + $('#id_stock_age_master option:selected').text();
        }

        return item;
    }

    // Render a preview row in the batch table
    function renderBatchTable() {
        var tbody = $('#batch_items_table tbody');
        tbody.empty();
        $.each(batchItems, function(i, item) {
            var statusBadge = item.status == '1'
                ? '<span class="badge bg-green">Active</span>'
                : '<span class="badge bg-red">Inactive</span>';
            tbody.append(
                '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + item._branch_label + '</td>' +
                '<td>' + item._metal_label + '</td>' +
                '<td>' + item._category_label + '</td>' +
                '<td>' + item._product_label + '</td>' +
                '<td>' + item._design_label + '</td>' +
                '<td>' + item._sub_design_label + '</td>' +
                '<td>' + item._calc_basis_label + '</td>' +
                '<td>' + item._rate_type_label + '</td>' +
                '<td>' + item._value_display + '</td>' +
                '<td>' + statusBadge + '</td>' +
                '<td>' +
                    '<button type="button" class="btn btn-xs btn-warning btn-edit-batch" data-idx="' + i + '" title="Edit" style="margin-right:3px"><i class="fa fa-pencil"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-danger btn-remove-batch" data-idx="' + i + '" title="Remove"><i class="fa fa-trash"></i></button>' +
                '</td>' +
                '</tr>'
            );
        });
        $('#batch_count').text(batchItems.length);
        if (batchItems.length > 0) {
            $('#batch_items_section').show();
        } else {
            $('#batch_items_section').hide();
        }
    }

    // Reset form fields after adding an item
    function resetFormFields() {
        // Reset dropdowns
        $('#id_branch, #id_metal, #id_category, #id_product, #id_design, #id_sub_design').val(null).trigger('change.select2');
        // Reset calc basis and rate type to defaults
        $('input[name="config[calc_basis]"][value="1"]').prop('checked', true).parent().addClass('active');
        $('input[name="config[calc_basis]"]').not('[value="1"]').parent().removeClass('active');
        $('input[name="config[rate_type]"][value="1"]').prop('checked', true).trigger('change');
        // Reset values
        $('#incentive_value').val('');
        $('#id_stock_age_master').val('').trigger('change.select2');
        $('#stone_type').val('').trigger('change.select2');
        $('#stone_type_row').hide();
        // Reset ranges
        $('#range_table tbody').html(
            '<tr class="range_row first_range_row">' +
            '<td><input type="number" step="any" min="0" name="config[ranges][0][range_from]" class="form-control range_from_input"></td>' +
            '<td><input type="number" step="any" min="0" name="config[ranges][0][range_to]" class="form-control range_to_input"></td>' +
            '<td><input type="number" step="any" min="0" name="config[ranges][0][incentive_value]" class="form-control" placeholder="Value"></td>' +
            '<td><button type="button" class="btn btn-xs btn-success add_range_row"><i class="fa fa-plus"></i></button></td>' +
            '</tr>'
        );
        // Show fixed value row, hide others
        $('#fixed_value_row').show();
        $('#range_slabs_section').hide();
        $('#age_master_selection_row').hide();
    }

    if (processType == 'Add') {
        // Disable default form submit in Add mode
        $('#incentive_form').submit(function(e) {
            e.preventDefault();
            return false;
        });


        // Remove item from batch
        $(document).on('click', '.btn-remove-batch', function() {
            var idx = $(this).data('idx');
            batchItems.splice(idx, 1);
            renderBatchTable();
        });

        // Edit item from batch — load back into form
        var editingIndex = -1;
        $(document).on('click', '.btn-edit-batch', function() {
            var idx = $(this).data('idx');
            var item = batchItems[idx];
            editingIndex = idx;

            // Restore dropdowns
            function setMultiSelect(selectId, values, labels) {
                var $sel = $(selectId);
                // Make sure options exist (add if missing using labels)
                if (labels && labels !== 'All') {
                    var lblArr = labels.split(', ');
                    $.each(values, function(i, v) {
                        if ($sel.find('option[value="' + v + '"]').length === 0) {
                            $sel.append('<option value="' + v + '">' + (lblArr[i] || v) + '</option>');
                        }
                    });
                }
                $sel.val(values).trigger('change.select2');
            }

            setMultiSelect('#id_branch', item.id_branch, item._branch_label);
            setMultiSelect('#id_metal', item.id_metal, item._metal_label);
            setMultiSelect('#id_category', item.id_category, item._category_label);
            setMultiSelect('#id_product', item.id_product, item._product_label);
            setMultiSelect('#id_design', item.id_design, item._design_label);
            setMultiSelect('#id_sub_design', item.id_sub_design, item._sub_design_label);

            // Restore calc basis
            $('input[name="config[calc_basis]"]').parent().removeClass('active');
            $('input[name="config[calc_basis]"][value="' + item.calc_basis + '"]').prop('checked', true).parent().addClass('active');
            if (item.calc_basis == '4') {
                $('#age_master_selection_row').show();
                $('#id_stock_age_master').val(item.id_stock_age_master).trigger('change.select2');
            } else {
                $('#age_master_selection_row').hide();
            }

            // Restore rate type
            $('input[name="config[rate_type]"][value="' + item.rate_type + '"]').prop('checked', true).trigger('change');

            if (item.rate_type == '1') {
                $('#incentive_value').val(item.incentive_value);
            } else {
                // Restore range slabs
                var tbody = $('#range_table tbody');
                tbody.empty();
                if (item.ranges && item.ranges.length > 0) {
                    $.each(item.ranges, function(i, r) {
                        tbody.append(
                            '<tr class="range_row">' +
                            '<td><input type="number" step="any" min="0" name="config[ranges][' + i + '][range_from]" class="form-control range_from_input" value="' + r.range_from + '"></td>' +
                            '<td><input type="number" step="any" min="0" name="config[ranges][' + i + '][range_to]" class="form-control range_to_input" value="' + r.range_to + '"></td>' +
                            '<td><input type="number" step="any" min="0" name="config[ranges][' + i + '][incentive_value]" class="form-control" value="' + r.incentive_value + '"></td>' +
                            '<td>' +
                            '<button type="button" class="btn btn-xs btn-success add_range_row" title="Add Slab"><i class="fa fa-plus"></i></button> ' +
                            '<button type="button" class="btn btn-xs btn-danger remove_range_row" title="Delete"><i class="fa fa-trash"></i></button>' +
                            '</td></tr>'
                        );
                    });
                    rangeIndex = item.ranges.length;
                }
            }

            // Restore stone type
            if (item.rate_type == '3' && item.stone_type) {
                $('#stone_type').val(item.stone_type).trigger('change.select2');
            }

            // Restore status
            $('input[name="config[status]"]').parent().removeClass('active');
            $('input[name="config[status]"][value="' + item.status + '"]').prop('checked', true).parent().addClass('active');

            // Change button text to "Update Item"
            $('#btn_add_item').html('<i class="fa fa-pencil"></i> Update Item');

            // Scroll to form
            $('html, body').animate({ scrollTop: $('#incentive_form').offset().top - 60 }, 300);
        });

        // Check if two scope sets overlap (both are arrays)
        function scopeOverlaps(arr1, arr2) {
            // If either is empty = "All", they overlap
            if (!arr1.length || !arr2.length) return true;
            // Check if any value in arr1 is also in arr2
            for (var i = 0; i < arr1.length; i++) {
                if (arr2.indexOf(arr1[i]) >= 0) return true;
            }
            return false;
        }

        // Check if new item conflicts with any existing batch item
        function checkBatchConflict(newItem, skipIndex) {
            for (var i = 0; i < batchItems.length; i++) {
                if (i === skipIndex) continue; // skip self when editing
                var existing = batchItems[i];
                // Check if all 6 scope fields overlap
                var allOverlap = scopeOverlaps(newItem.id_branch, existing.id_branch)
                    && scopeOverlaps(newItem.id_metal, existing.id_metal)
                    && scopeOverlaps(newItem.id_category, existing.id_category)
                    && scopeOverlaps(newItem.id_product, existing.id_product)
                    && scopeOverlaps(newItem.id_design, existing.id_design)
                    && scopeOverlaps(newItem.id_sub_design, existing.id_sub_design);

                if (allOverlap && newItem.calc_basis != existing.calc_basis) {
                    return {
                        conflict: true,
                        index: i + 1,
                        existingBasis: existing._calc_basis_label,
                        newBasis: newItem._calc_basis_label
                    };
                }
            }
            return { conflict: false };
        }

        // Add/Update Item button — handles both new items and editing existing ones
        $('#btn_add_item').click(function() {
            if (!validateFormInputs()) return;
            var item = captureItem();
            var $btn = $(this);

            // Step 1: Check within batch items for scope conflicts
            var batchConflict = checkBatchConflict(item, editingIndex);
            if (batchConflict.conflict) {
                $.toaster({ priority: 'danger', title: 'Conflicting Configuration!', message: '</br>Item #' + batchConflict.index + ' in the batch already uses <b>"' + batchConflict.existingBasis + '"</b> for the same scope.<br>You cannot configure <b>"' + batchConflict.newBasis + '"</b> for the same product/scope.<br><br>Please change the scope or remove the conflicting item first.' });
                return;
            }

            $btn.prop('disabled', true);

            // Step 2: Check against database for existing saved configs
            var checkData = {
                id_branch: item.id_branch.length ? item.id_branch.join(',') : '',
                id_metal: item.id_metal.length ? item.id_metal.join(',') : '',
                id_category: item.id_category.length ? item.id_category.join(',') : '',
                id_product: item.id_product.length ? item.id_product.join(',') : '',
                id_design: item.id_design.length ? item.id_design.join(',') : '',
                id_sub_design: item.id_sub_design.length ? item.id_sub_design.join(',') : ''
            };

            $.ajax({
                url: base_url + 'index.php/admin_emp_incentive/ajax_check_duplicate',
                type: 'POST',
                data: JSON.stringify(checkData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false);
                    if (res.status) {
                        // Conflicts found in DB — block with warning toast
                        $.toaster({ priority: 'warning', title: 'Duplicate Configuration!', message: '</br>' + res.message + '<br><br>Please deactivate existing rules first or adjust the scope.' });
                        return;
                    }
                    // Proceed with add/update
                    if (editingIndex >= 0) {
                        batchItems[editingIndex] = item;
                        editingIndex = -1;
                        $('#btn_add_item').html('<i class="fa fa-plus-circle"></i> Add Item');
                    } else {
                        batchItems.push(item);
                    }
                    renderBatchTable();
                    resetFormFields();
                    $('html, body').animate({ scrollTop: $('#batch_items_section').offset().top - 60 }, 300);
                },
                error: function() {
                    $btn.prop('disabled', false);
                    // On error, still allow (fail-open) but batch check already passed
                    if (editingIndex >= 0) {
                        batchItems[editingIndex] = item;
                        editingIndex = -1;
                        $('#btn_add_item').html('<i class="fa fa-plus-circle"></i> Add Item');
                    } else {
                        batchItems.push(item);
                    }
                    renderBatchTable();
                    resetFormFields();
                    $('html, body').animate({ scrollTop: $('#batch_items_section').offset().top - 60 }, 300);
                }
            });
        });

        // Save All — post batch to server
        $('#btn_save_all').click(function() {
            if (batchItems.length === 0) {
                $.toaster({ priority: 'warning', title: 'No Items', message: '</br>Please add at least one item before saving.' });
                return;
            }
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            // Prepare data — strip display labels
            var postItems = [];
            $.each(batchItems, function(i, item) {
                postItems.push({
                    id_branch: item.id_branch.length ? item.id_branch.join(',') : '',
                    id_metal: item.id_metal.length ? item.id_metal.join(',') : '',
                    id_category: item.id_category.length ? item.id_category.join(',') : '',
                    id_product: item.id_product.length ? item.id_product.join(',') : '',
                    id_design: item.id_design.length ? item.id_design.join(',') : '',
                    id_sub_design: item.id_sub_design.length ? item.id_sub_design.join(',') : '',
                    calc_basis: item.calc_basis,
                    rate_type: item.rate_type,
                    incentive_value: item.incentive_value,
                    id_stock_age_master: item.id_stock_age_master || '',
                    stone_type: item.stone_type || '',
                    status: item.status,
                    ranges: item.ranges
                });
            });

            $.ajax({
                url: base_url + 'index.php/admin_emp_incentive/config_batch_save',
                type: 'POST',
                data: JSON.stringify({ items: postItems }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        $.toaster({ priority: 'success', title: 'Success!', message: '</br>' + res.message });
                        setTimeout(function() { window.location.href = base_url + 'index.php/admin_emp_incentive/config_list'; }, 1000);
                    } else {
                        $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + res.message });
                        $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save All');
                    }
                },
                error: function() {
                    $.toaster({ priority: 'danger', title: 'Server Error!', message: '</br>Failed to save. Please try again.' });
                    $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save All');
                }
            });
        });
    } else {
        // Edit mode — keep single form submit validation
        $('#incentive_form').submit(function (e) {
            if (!validateFormInputs()) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
