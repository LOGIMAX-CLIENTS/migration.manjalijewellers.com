/**
 * Partly Sale Module — Frontend JavaScript
 * 
 * Handles:
 * - Tag scan → auto-calculate minimum parts + render table
 * - Re-calculate when user increases parts
 * - Real-time weight validation (oninput)
 * - Save via AJAX → show "Add to Estimation" buttons
 * 
 * @module Partly Sale
 * @version 2.0
 */

$(document).ready(function () {

    var baseUrl = $('#base_url').val();
    var baseTagData = null;
    var childrenData = [];
    var costLimit = 200000;  // Dynamic from ret_settings.max_cash_amt
    var goldRate = 0;
    var wastagePct = 0;
    var mcType = 0;
    var mcValue = 0;
    var calcBased = 0;
    var baseCost = 0;  // Total base tag cost from server
    var savedPartsId = null; // After save, holds the parts ID

    // =========================================================================
    // Event: Scan / Fetch Tag
    // =========================================================================

    $('#tag_scan_code').on('keydown', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            fetchTag();
        }
    });

    $('#btn_fetch_tag').click(function () {
        fetchTag();
    });

    function fetchTag() {
        var tagCode = $.trim($('#tag_scan_code').val());
        if (tagCode === '') {
            showScanError('Please enter a tag code');
            return;
        }

        $('#btn_fetch_tag').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        hideScanError();

        $.ajax({
            url: baseUrl + 'index.php/admin_ret_tag_parts/get_tag_details',
            type: 'POST',
            data: { tag_code: tagCode },
            dataType: 'json',
            success: function (response) {
                if (response.status) {

                    // Already parted — show success card directly with existing children
                    if (response.already_parted) {
                        baseTagData = response.data;
                        displayBaseInfo(baseTagData);

                        var branchId = baseTagData.current_branch || '';
                        var childCodes = response.child_codes || [];
                        savedPartsId = response.parts_id;

                        renderSuccessCard(childCodes, response.parts_id, branchId);

                        // Hide weight form, show info
                        $('#child_weight_tbody, .totals-row, .validation-row, #step_actions').hide();
                        $('#btn_save_parts').hide();
                        $('#tag_details_placeholder').hide();
                        $('#step_base_info').show();

                        $.toaster({ priority: 'info', title: 'Active Parts', message: baseTagData.tag_code + ' has ' + childCodes.length + ' active parts' });
                        return;
                    }

                    baseTagData = response.data;
                    costLimit = response.cost_limit || 200000;
                    baseCost = response.base_cost || 0;
                    goldRate = response.gold_rate || 0;
                    wastagePct = response.wastage_pct || 0;
                    mcType = response.mc_type || 0;
                    mcValue = response.mc_value || 0;
                    calcBased = response.calc_based || 0;

                    displayBaseInfo(baseTagData);

                    // Auto-split base weight into cost-limit-sized rows
                    var baseGwt = parseFloat(baseTagData.gross_wt);
                    var baseNwt = parseFloat(baseTagData.net_wt);
                    var baseLessWt = parseFloat(baseTagData.less_wt);
                    var baseStoneWt = parseFloat(baseTagData.stone_wt);
                    var baseDiaWt = parseFloat(baseTagData.dia_wt);

                    var maxGwtPerPart = baseGwt; // default: no split
                    if (goldRate > 0 && costLimit > 0) {
                        var costPerGram = computeCostForGwt(baseGwt) / baseGwt;
                        if (costPerGram > 0) {
                            maxGwtPerPart = Math.floor((costLimit / costPerGram) * 1000) / 1000;
                            if (maxGwtPerPart <= 0) maxGwtPerPart = baseGwt;
                        }
                    }

                    var autoChildren = [];
                    var numParts = Math.ceil(baseGwt / maxGwtPerPart);
                    if (numParts < 1) numParts = 1;
                    var perPartGwt = Math.floor((baseGwt / numParts) * 1000) / 1000;
                    var usedGwt = 0, usedNwt = 0, usedLess = 0, usedStone = 0, usedDia = 0;
                    for (var partNum = 1; partNum <= numParts; partNum++) {
                        var partGwt, partNwt, partLess, partStone, partDia;
                        if (partNum === numParts) {
                            // Last part absorbs remainder for ALL fields
                            partGwt   = Math.round((baseGwt - usedGwt) * 1000) / 1000;
                            partNwt   = Math.round((baseNwt - usedNwt) * 1000) / 1000;
                            partLess  = Math.round((baseLessWt - usedLess) * 1000) / 1000;
                            partStone = Math.round((baseStoneWt - usedStone) * 1000) / 1000;
                            partDia   = Math.round((baseDiaWt - usedDia) * 1000) / 1000;
                        } else {
                            // Stagger each row by -0.01g so no two rows are identical
                            partGwt = Math.round((perPartGwt - ((partNum - 1) * 0.01)) * 1000) / 1000;
                            if (partGwt < 0.001) partGwt = perPartGwt; // safety floor

                            var ratio = partGwt / baseGwt;
                            partNwt   = Math.round(baseNwt * ratio * 1000) / 1000;
                            partLess  = Math.round(baseLessWt * ratio * 1000) / 1000;
                            partStone = Math.round(baseStoneWt * ratio * 1000) / 1000;
                            partDia   = Math.round(baseDiaWt * ratio * 1000) / 1000;
                        }

                        autoChildren.push({
                            child_order: partNum,
                            tag_code: baseTagData.tag_code + '-' + partNum,
                            gwt: partGwt,
                            nwt: partNwt,
                            less_wt: partLess,
                            stone_wt: partStone,
                            dia_wt: partDia
                        });
                        usedGwt += partGwt;
                        usedNwt += partNwt;
                        usedLess += partLess;
                        usedStone += partStone;
                        usedDia += partDia;
                    }

                    childrenData = autoChildren;
                    renderChildWeightTable(autoChildren, response.base);
                    $('#parts_count').val(autoChildren.length);

                    $.toaster({ priority: 'success', title: 'Tag Found', message: baseTagData.tag_code + ' — divided into ' + autoChildren.length + ' parts' });
                } else {
                    var errMsg = response.message || 'Tag not found';
                    showScanError(errMsg);
                    $.toaster({ priority: 'danger', title: 'Partly Sale', message: errMsg });
                    resetForm();
                }
            },
            error: function () {
                showScanError('Server error. Please try again.');
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Server error while fetching tag. Please try again.' });
            },
            complete: function () {
                $('#btn_fetch_tag').prop('disabled', false).html('<i class="fa fa-search"></i> Scan');
            }
        });
    }

    // =========================================================================
    // Display Base Tag Info
    // =========================================================================

    function displayBaseInfo(tag) {
        $('#base_tag_code_display').text(tag.tag_code);
        $('#base_tag_id').val(tag.tag_id);
        $('#base_tag_code').val(tag.tag_code);

        $('#info_product').text(tag.product_name || '-');
        $('#info_design').text(tag.design_name || '-');
        $('#info_purity').text(tag.purity_name || '-');
        $('#info_lot').text(tag.tag_lot_id || '-');

        $('#info_gwt').text(parseFloat(tag.gross_wt).toFixed(3));
        $('#info_nwt').text(parseFloat(tag.net_wt).toFixed(3));
        $('#info_less_wt').text(parseFloat(tag.less_wt).toFixed(3));
        $('#info_stone_wt').text(parseFloat(tag.stone_wt).toFixed(3));
        $('#info_dia_wt').text(parseFloat(tag.dia_wt).toFixed(3));

        // Display estimated base cost
        if (baseCost > 0) {
            $('#info_cost').html('₹' + Math.round(baseCost).toLocaleString('en-IN'));
        } else {
            $('#info_cost').text('-');
        }

        // Set branch and load branch details for customer slider defaults
        var tagBranch = tag.current_branch || '';
        if (tagBranch) {
            $('#id_branch').val(tagBranch);
            // Fetch branch details for country/state/city defaults
            $.ajax({
                url: baseUrl + 'index.php/admin_manage/getBranchDetails',
                type: 'POST',
                dataType: 'json',
                data: { id_branch: tagBranch },
                success: function(data) {
                    $.each(data, function(key, item) {
                        if (tagBranch == item.id_branch) {
                            $('#branch_id_country').val(item.id_country);
                            $('#branch_id_state').val(item.id_state);
                            $('#branch_id_city').val(item.id_city);
                            $('#branch_pincode').val(item.pincode);
                            $('#branch_id_village').val(item.id_village);
                        }
                    });
                }
            });
        }

        // Hide zero-value detail items
        var zeroFields = ['info_less_wt', 'info_stone_wt', 'info_dia_wt'];
        $.each(zeroFields, function(i, id) {
            var v = parseFloat($('#' + id).text()) || 0;
            $('#' + id).closest('.ts-detail-item').toggle(v !== 0);
        });

        $('#tag_details_placeholder').hide();
        $('#step_base_info').show();
        $('#parts_count').focus().select();
    }

    // =========================================================================
    // Event: Re-calculate Parts
    // =========================================================================

    $('#btn_calculate_parts').click(function () {
        recalculateParts();
    });

    $('#parts_count').on('keydown', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            recalculateParts();
        }
    });

    function recalculateParts() {
        if (!baseTagData) {
            $.toaster({ priority: 'warning', title: 'Partly Sale', message: 'Please scan a tag first' });
            return;
        }

        var partsCount = parseInt($('#parts_count').val());
        if (isNaN(partsCount) || partsCount < 2 || partsCount > 99) {
            $.toaster({ priority: 'warning', title: 'Partly Sale', message: 'Parts count must be between 2 and 99' });
            return;
        }

        // min_parts is advisory only — no enforcement

        $('#btn_calculate_parts').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Calculating...');

        $.ajax({
            url: baseUrl + 'index.php/admin_ret_tag_parts/calculate_parts',
            type: 'POST',
            data: {
                tag_id: baseTagData.tag_id,
                tag_code: baseTagData.tag_code,
                parts_count: partsCount
            },
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    childrenData = response.children;
                    renderChildWeightTable(response.children, response.base);
                } else {
                    $.toaster({ priority: 'danger', title: 'Error', message: response.message || 'Calculation failed' });
                }
            },
            error: function () {
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Server error during calculation.' });
            },
            complete: function () {
                $('#btn_calculate_parts').prop('disabled', false).html('<i class="fa fa-calculator"></i> Re-calculate');
            }
        });
    }

    // =========================================================================
    // Render Child Weight Table
    // =========================================================================

    function renderChildWeightTable(children, base) {
        var tbody = '';

        $.each(children, function (i, child) {
            var legendHtml = '<td class="col-legend">' + computeLegend(child) + '</td>';
            tbody += '<tr data-index="' + i + '">' +
                '<td>' + child.child_order + '</td>' +
                '<td><span class="child-code">' + child.tag_code + '</span></td>' +
                '<td class="col-gwt"><input type="number" step="0.001" class="child-gwt" data-index="' + i + '" value="' + parseFloat(child.gwt).toFixed(3) + '"/></td>' +
                '<td class="col-nwt"><input type="number" step="0.001" class="child-nwt" data-index="' + i + '" value="' + parseFloat(child.nwt).toFixed(3) + '"/></td>' +
                '<td class="col-less"><input type="number" step="0.001" class="child-less" data-index="' + i + '" value="' + parseFloat(child.less_wt).toFixed(3) + '"/></td>' +
                '<td class="col-stone"><input type="number" step="0.001" class="child-stone" data-index="' + i + '" value="' + parseFloat(child.stone_wt).toFixed(3) + '"/></td>' +
                '<td class="col-dia"><input type="number" step="0.001" class="child-dia" data-index="' + i + '" value="' + parseFloat(child.dia_wt).toFixed(3) + '"/></td>' +
                legendHtml +
                '</tr>';
        });

        $('#child_weight_tbody').html(tbody);

        // Set base totals
        $('#base_gwt').text(parseFloat(base.gwt).toFixed(3));
        $('#base_nwt').text(parseFloat(base.nwt).toFixed(3));
        $('#base_less_wt').text(parseFloat(base.less_wt).toFixed(3));
        $('#base_stone_wt').text(parseFloat(base.stone_wt).toFixed(3));
        $('#base_dia_wt').text(parseFloat(base.dia_wt).toFixed(3));

        // Show balance banner
        if (baseTagData) {
            $('#bal_tag_code').text(baseTagData.tag_code);
            $('#bal_base_gwt').text(parseFloat(base.gwt).toFixed(3));
            $('#balance_banner').show();
        }

        // Show the actions (Save/Cancel buttons in table header)
        $('#step_actions').show();
        $('#child_weight_table tfoot').show();
        $('#child_weight_tbody, .totals-row, .validation-row').show();

        // Hide columns where base value is zero
        hideZeroColumns(base);

        // Bind validation on input change + dynamic row creation
        bindWeightInputs();

        // Initial validation
        validatePartsWeights();
    }

    // =========================================================================
    // Hide Zero Columns
    // =========================================================================

    function hideZeroColumns(base) {
        // Clear values (not columns) where base weight is zero
        var cols = [
            { key: 'less_wt',  cls: 'child-less',  totalId: 'total_less_wt',  baseId: 'base_less_wt',  validateId: 'validate_less_wt',  detailId: 'info_less_wt' },
            { key: 'stone_wt', cls: 'child-stone', totalId: 'total_stone_wt', baseId: 'base_stone_wt', validateId: 'validate_stone_wt', detailId: 'info_stone_wt' },
            { key: 'dia_wt',   cls: 'child-dia',   totalId: 'total_dia_wt',   baseId: 'base_dia_wt',   validateId: 'validate_dia_wt',   detailId: 'info_dia_wt' }
        ];

        $.each(cols, function(i, col) {
            var val = parseFloat(base[col.key]) || 0;
            if (val === 0) {
                // Clear input values and make them readonly
                $('#child_weight_tbody .' + col.cls).val('').attr('readonly', true).attr('placeholder', '-');
                // Clear footer texts
                $('#' + col.totalId).text('-');
                $('#' + col.baseId).text('-');
                $('#' + col.validateId).html('<span style="color:#ccc;">—</span>');
            }
        });
    }

    // =========================================================================
    // Real-time Weight Validation
    // =========================================================================

    function validatePartsWeights() {
        var tolerance = 0.001;
        var fields = [
            { class: 'child-gwt', totalId: 'total_gwt', validateId: 'validate_gwt', baseId: 'base_gwt' },
            { class: 'child-nwt', totalId: 'total_nwt', validateId: 'validate_nwt', baseId: 'base_nwt' },
            { class: 'child-less', totalId: 'total_less_wt', validateId: 'validate_less_wt', baseId: 'base_less_wt' },
            { class: 'child-stone', totalId: 'total_stone_wt', validateId: 'validate_stone_wt', baseId: 'base_stone_wt' },
            { class: 'child-dia', totalId: 'total_dia_wt', validateId: 'validate_dia_wt', baseId: 'base_dia_wt' }
        ];

        var allValid = true;

        $.each(fields, function (i, f) {
            var baseText = $('#' + f.baseId).text();
            var baseVal = parseFloat(baseText) || 0;

            // Skip zero-weight columns
            if (baseVal === 0) {
                $('#' + f.totalId).text('-');
                $('#' + f.validateId).html('<span style="color:#ccc;">—</span>');
                return true; // continue
            }

            var sum = 0;
            var hasNegative = false;

            $('#child_weight_tbody .' + f.class).each(function () {
                var val = parseFloat($(this).val()) || 0;
                if (val < 0) hasNegative = true;
                sum += val;
            });

            var diff = Math.abs(sum - baseVal);
            var valid = diff <= tolerance && !hasNegative;

            // Update totals display
            $('#' + f.totalId).text(sum.toFixed(3));

            // Update validation indicator
            if (valid) {
                $('#' + f.validateId).html('<span class="valid-indicator"><i class="fa fa-check-circle"></i></span>');
            } else {
                $('#' + f.validateId).html('<span class="invalid-indicator"><i class="fa fa-times-circle"></i></span>');
                allValid = false;
            }

            // Mark individual inputs
            $('#child_weight_tbody .' + f.class).each(function () {
                var val = parseFloat($(this).val()) || 0;
                $(this).toggleClass('is-invalid', val < 0);
                $(this).toggleClass('is-valid', val >= 0 && valid);
            });
        });

        // Gate save button
        $('#btn_save_parts').prop('disabled', !allValid);

        // Update balance banner
        var gwtTotal = parseFloat($('#total_gwt').text()) || 0;
        var baseGwt = parseFloat($('#base_gwt').text()) || 0;
        var balance = baseGwt - gwtTotal;
        $('#bal_remaining').text(balance.toFixed(3));
        if (Math.abs(balance) <= tolerance) {
            $('#bal_remaining').removeClass('ts-bal-warn ts-bal-error').addClass('ts-bal-ok');
        } else if (balance > 0) {
            $('#bal_remaining').removeClass('ts-bal-ok ts-bal-error').addClass('ts-bal-warn');
        } else {
            $('#bal_remaining').removeClass('ts-bal-ok ts-bal-warn').addClass('ts-bal-error');
        }

        // Update legend for each row
        updateAllLegends();

        // Update children data from inputs
        updateChildrenFromInputs();

        return allValid;
    }

    function updateChildrenFromInputs() {
        childrenData = [];
        $('#child_weight_tbody tr').each(function () {
            var idx = $(this).data('index');
            childrenData.push({
                child_order: idx + 1,
                gwt: parseFloat($(this).find('.child-gwt').val()) || 0,
                nwt: parseFloat($(this).find('.child-nwt').val()) || 0,
                less_wt: parseFloat($(this).find('.child-less').val()) || 0,
                stone_wt: parseFloat($(this).find('.child-stone').val()) || 0,
                dia_wt: parseFloat($(this).find('.child-dia').val()) || 0
            });
        });
    }

    // =========================================================================
    // Save Partly Sale
    // =========================================================================

    $('#btn_save_parts').click(function () {
        if (!baseTagData) {
            $.toaster({ priority: 'warning', title: 'Partly Sale', message: 'No base tag loaded' });
            return;
        }

        if (childrenData.length < 2) {
            $.toaster({ priority: 'warning', title: 'Partly Sale', message: 'Minimum 2 parts required to save. This tag is already under the cost limit — increase the parts count or add directly to estimation.' });
            return;
        }

        if (!validatePartsWeights()) {
            $.toaster({ priority: 'danger', title: 'Validation Failed', message: 'Weight totals must match the base tag exactly' });
            return;
        }

        if (!confirm('Confirm Partly Sale?\n\nBase tag: ' + baseTagData.tag_code + '\nParts: ' + childrenData.length + '\n\nThis action cannot be easily undone.')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: baseUrl + 'index.php/admin_ret_tag_parts/save',
            type: 'POST',
            data: {
                tag_code: baseTagData.tag_code,
                parts_count: childrenData.length,
                children: JSON.stringify(childrenData)
            },
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    var childCodes = response.child_codes || [];
                    savedPartsId = response.parts_id;
                    $.toaster({ priority: 'success', title: 'Saved!', message: 'Partly Sale ID: ' + response.parts_id });

                    var branchId = baseTagData.current_branch || '';

                    renderSuccessCard(childCodes, response.parts_id, branchId);

                    // Hide form elements and show success
                    $('#child_weight_tbody, .totals-row, .validation-row, #step_actions').hide();
                    $btn.hide();
                } else {
                    $.toaster({ priority: 'danger', title: 'Failed', message: response.message || 'Save operation failed' });
                    $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save');
                }
            },
            error: function () {
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Server error. Please try again.' });
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save');
            }
        });
    });

    // =========================================================================
    // Reusable: Render success card with child tag rows
    // =========================================================================
    function renderSuccessCard(childCodes, partsId, branchId) {
        // Remove any existing success card
        $('.ts-parts-success').remove();

        var successHtml = '<div class="ts-parts-success" style="background:linear-gradient(135deg,#d4edda,#c3e6cb);border:2px solid #28a745;border-radius:8px;padding:20px;text-align:center;margin-top:15px;">';
        successHtml += '<h4 style="color:#155724;margin:0 0 12px;"><i class="fa fa-check-circle"></i> Partly Sale Completed!</h4>';
        successHtml += '<p style="color:#155724;font-size:13px;margin-bottom:15px;">ID: <b>' + partsId + '</b> | Base: <b>' + baseTagData.tag_code + '</b></p>';

        // Batch selection header
        successHtml += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;padding:0 4px;">';
        successHtml += '<label style="margin:0;font-size:12px;color:#155724;cursor:pointer;"><input type="checkbox" class="ts-child-select-all" style="margin-right:5px;" /> Select All</label>';
        successHtml += '<button class="btn btn-info btn-xs ts-batch-estimation-btn" disabled style="padding:3px 12px;font-weight:600;"><i class="fa fa-calculator"></i> Add Selected to Estimation</button>';
        successHtml += '</div>';

        successHtml += '<div style="display:flex;flex-direction:column;gap:8px;align-items:stretch;">';
        $.each(childCodes, function(i, code) {
            successHtml += '<div class="ts-child-row" style="background:#fff;border:1.5px solid #28a745;border-radius:6px;padding:8px 12px;" data-code="' + code + '" data-branch="' + branchId + '">';
            successHtml += '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">';
            successHtml += '<input type="checkbox" class="ts-child-select" data-code="' + code + '" style="margin-right:2px;" />';
            successHtml += '<b style="font-size:13px;color:#2c3e50;white-space:nowrap;">' + code + '</b>';
            successHtml += '<div class="input-group" style="flex:1;min-width:180px;">';
            successHtml += '<input type="text" class="form-control input-sm ts-cus-mobile" placeholder="Customer Mobile / Name" autocomplete="off" style="font-size:12px;" />';
            successHtml += '<input type="hidden" class="ts-cus-id" value="" />';
            successHtml += '<span class="input-group-btn">';
            successHtml += '<button class="btn btn-success btn-sm ts-add-cus-btn" type="button" title="Add New Customer" style="padding:4px 8px;"><i class="fa fa-plus"></i></button>';
            successHtml += '<button class="btn btn-primary btn-sm ts-edit-cus-btn" type="button" title="Edit Customer" style="padding:4px 8px;display:none;"><i class="fa fa-external-link"></i></button>';
            successHtml += '</span></div>';
            successHtml += '<button class="btn btn-default btn-xs ts-copy-btn" data-code="' + code + '" title="Copy Tag Code" style="padding:3px 8px;"><i class="fa fa-copy"></i> Copy</button>';
            successHtml += '<button class="btn btn-primary btn-xs ts-add-estimation-btn" title="Add to Estimation" style="padding:3px 8px;"><i class="fa fa-calculator"></i> Estimation</button>';
            successHtml += '</div>';
            successHtml += '</div>';
        });
        successHtml += '</div>';
        successHtml += '<button class="btn btn-primary btn-sm" id="btn_new_parts" style="margin-top:15px;border-radius:20px;padding:6px 20px;"><i class="fa fa-plus"></i> New Partly Sale</button>';
        successHtml += '</div>';

        $('.ts-table-scroll').append(successHtml);

        // Initialize autocomplete on each customer input
        $('.ts-parts-success .ts-cus-mobile').each(function() {
            var $input = $(this);
            var $row = $input.closest('.ts-child-row');
            $input.autocomplete({
                source: function(request, response) {
                    if (request.term.length < 3) { response([]); return; }
                    $.ajax({
                        url: baseUrl + 'index.php/admin_ret_estimation/getCustomersBySearch/',
                        type: 'POST',
                        dataType: 'json',
                        data: { searchTxt: request.term, esti_for: 1 },
                        success: function(data) { response(data || []); }
                    });
                },
                minLength: 3,
                select: function(e, i) {
                    e.preventDefault();
                    var dupFound = false;
                    $('.ts-child-row').not($row).each(function() {
                        if ($(this).find('.ts-cus-id').val() == i.item.value) { dupFound = true; return false; }
                    });
                    if (dupFound) {
                        $.toaster({ priority: 'warning', title: 'Duplicate', message: 'This customer is already assigned to another part' });
                        $input.val('');
                        return;
                    }
                    $input.val(i.item.label);
                    $row.find('.ts-cus-id').val(i.item.value);
                    $row.find('.ts-edit-cus-btn').show();
                }
            });
        });
    }

    // Copy button handler (delegated)
    $(document).on('click', '.ts-copy-btn', function() {
        var code = $(this).data('code');
        var tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(code).select();
        document.execCommand('copy');
        tempInput.remove();
        $(this).html('<i class="fa fa-check"></i>').removeClass('btn-success btn-default').addClass('btn-default');
        var btn = $(this);
        setTimeout(function() { btn.html('<i class="fa fa-copy"></i>'); }, 1500);
        $.toaster({ priority: 'info', title: 'Copied!', message: code + ' copied to clipboard' });
    });

    // Add New Customer — opens the offcanvas slider with branch defaults
    $(document).on('click', '.ts-add-cus-btn', function() {
        var $row = $(this).closest('.ts-child-row');
        window._tsActiveChildRow = $row;

        // Clear slider form fields for fresh entry
        $('#id_customer').val('');
        $('#cus_first_name').val('');
        $('#cus_mobile').val('').prop('readonly', false);
        $('#cus_email').val('');
        $('#address1, #address2, #address3').val('');
        $('#gst_no').val('');
        $('#pin_code_add').val($('#branch_pincode').val() || '');
        $('#title').val('Mr');

        // Set branch-based country/state/city defaults
        $('#id_country').val($('#branch_id_country').val());
        $('#id_state').val($('#branch_id_state').val());
        $('#id_city').val($('#branch_id_city').val());

        // Load country dropdown and open slider
        if (typeof get_country === 'function') get_country();
        if (typeof get_villages_by_pincode === 'function') get_villages_by_pincode($('#branch_pincode').val() || '');

        $('.gst').hide();
        $('#demo').offcanvas('show');
    });

    // Edit Customer — fetch details, prefill slider, and open
    $(document).on('click', '.ts-edit-cus-btn', function() {
        var $row = $(this).closest('.ts-child-row');
        var cusId = $row.find('.ts-cus-id').val();
        if (!cusId) {
            $.toaster({ priority: 'warning', title: 'Customer', message: 'Select a customer first' });
            return;
        }
        window._tsActiveChildRow = $row;

        // Fetch customer details and prefill slider (same as estimation get_customer)
        $.ajax({
            type: 'POST',
            url: baseUrl + 'index.php/admin_ret_estimation/get_customer?nocache=' + new Date().getUTCSeconds(),
            cache: false,
            dataType: 'json',
            data: { id_customer: cusId },
            success: function(data) {
                $('#id_customer').val(cusId);
                $('#cus_first_name').val(data.firstname);
                $('#cus_mobile').val(data.mobile).prop('readonly', true);
                $('#cus_email').val(data.email);
                $('#address1').val(data.address1);
                $('#address2').val(data.address2);
                $('#address3').val(data.address3);
                $('#pin_code_add').val(data.pincode);
                $('#gst_no').val(data.gst_number);
                $('#id_country').val(data.id_country);
                $('#id_state').val(data.id_state);
                $('#id_city').val(data.id_city);
                $('#title').val(data.title);
                $('#pan').val(data.pan_no);
                $('#aadharid').val(data.aadharid);

                if (data.is_vip == 1) { $('#vip1').prop('checked', true); }
                else { $('#vip0').prop('checked', true); }

                if (typeof get_country === 'function') get_country();
                if (typeof get_profession === 'function') get_profession();
                if (typeof get_villages_by_pincode === 'function') get_villages_by_pincode(data.pincode);

                $('#demo').offcanvas('show');
            }
        });
    });

    // Add to Estimation — check tag availability (billed/reserved) then open
    $(document).on('click', '.ts-add-estimation-btn', function() {
        var $btn = $(this);
        var $row = $btn.closest('.ts-child-row');
        var code = $row.data('code') || $btn.data('code');
        var branch = $row.data('branch') || $btn.data('branch') || '';
        var cusId = $row.find('.ts-cus-id').val() || '';
        var cusName = $row.find('.ts-cus-mobile').val() || '';

        var origHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Checking...').prop('disabled', true);

        $.ajax({
            url: baseUrl + 'index.php/admin_ret_tag_parts/checkTagEstimation',
            type: 'POST',
            dataType: 'json',
            data: { tag_code: code },
            success: function(data) {
                if (!data.available) {
                    $.toaster({ priority: 'danger', title: 'Not Available', message: data.message || 'Tag cannot be added to estimation' });
                    $btn.html(origHtml).prop('disabled', false);
                    return;
                }
                // Tag is available — open estimation
                var url = baseUrl + 'index.php/admin_ret_estimation/estimation/add?tag_code=' + encodeURIComponent(code) + '&select_tag_details=1&from_parts=1';
                if (branch) url += '&branch_id=' + encodeURIComponent(branch);
                if (cusId) {
                    url += '&cus_id=' + encodeURIComponent(cusId);
                    url += '&cus_name=' + encodeURIComponent(cusName);
                }
                window.open(url, '_blank');
                $btn.html(origHtml).prop('disabled', false);
            },
            error: function() {
                $.toaster({ priority: 'danger', title: 'Error', message: 'Could not verify tag status' });
                $btn.html(origHtml).prop('disabled', false);
            }
        });
    });

    // =========================================================================
    // Checkbox Selection — Success Card
    // =========================================================================

    // Select All checkbox in success card
    $(document).on('change', '.ts-child-select-all', function() {
        var checked = $(this).prop('checked');
        $(this).closest('.ts-parts-success').find('.ts-child-select').prop('checked', checked);
        updateBatchBtnState('.ts-parts-success', '.ts-child-select', '.ts-batch-estimation-btn');
    });

    // Individual child checkbox in success card
    $(document).on('change', '.ts-child-select', function() {
        var $container = $(this).closest('.ts-parts-success');
        var total = $container.find('.ts-child-select').length;
        var checked = $container.find('.ts-child-select:checked').length;
        $container.find('.ts-child-select-all').prop('checked', total === checked && total > 0);
        updateBatchBtnState('.ts-parts-success', '.ts-child-select', '.ts-batch-estimation-btn');
    });

    // Batch Add to Estimation — Success Card
    $(document).on('click', '.ts-batch-estimation-btn', function() {
        batchAddToEstimation('.ts-parts-success', '.ts-child-select', $(this));
    });

    // =========================================================================
    // Checkbox Selection — Detail Modal
    // =========================================================================

    // Select All checkbox in detail modal
    $(document).on('change', '.ts-modal-select-all', function() {
        var checked = $(this).prop('checked');
        $(this).closest('.modal-body, .bootbox-body').find('.ts-modal-child-select').prop('checked', checked);
        updateBatchBtnState('.modal-body, .bootbox-body', '.ts-modal-child-select', '.ts-modal-batch-estimation-btn');
    });

    // Individual child checkbox in detail modal
    $(document).on('change', '.ts-modal-child-select', function() {
        var $container = $(this).closest('.modal-body, .bootbox-body');
        var total = $container.find('.ts-modal-child-select').length;
        var checked = $container.find('.ts-modal-child-select:checked').length;
        $container.find('.ts-modal-select-all').prop('checked', total === checked && total > 0);
        updateBatchBtnState('.modal-body, .bootbox-body', '.ts-modal-child-select', '.ts-modal-batch-estimation-btn');
    });

    // Batch Add to Estimation — Detail Modal
    $(document).on('click', '.ts-modal-batch-estimation-btn', function() {
        batchAddToEstimation('.modal-body, .bootbox-body', '.ts-modal-child-select', $(this));
    });

    // =========================================================================
    // Batch Estimation Helpers
    // =========================================================================

    function updateBatchBtnState(containerSel, checkboxSel, btnSel) {
        var checked = $(containerSel).find(checkboxSel + ':checked').length;
        $(containerSel).find(btnSel).prop('disabled', checked === 0);
        // Update button text with count
        if (checked > 0) {
            $(containerSel).find(btnSel).html('<i class="fa fa-calculator"></i> Add ' + checked + ' to Estimation');
        } else {
            $(containerSel).find(btnSel).html('<i class="fa fa-calculator"></i> Add Selected to Estimation');
        }
    }

    function batchAddToEstimation(containerSel, checkboxSel, $btn) {
        var $container = $btn.closest(containerSel);
        if (!$container.length) $container = $(containerSel);

        var selectedCodes = [];
        var branchId = '';
        var cusId = '';
        var cusName = '';
        var customerConflict = false;

        $container.find(checkboxSel + ':checked').each(function() {
            var code = $(this).data('code');
            if (code) selectedCodes.push(code);

            // Get branch from row data attribute or button
            if (!branchId) {
                branchId = $(this).closest('.ts-child-row').data('branch') || $(this).closest('tr').find('.ts-add-estimation-btn').data('branch') || $btn.data('branch') || '';
            }

            // Collect customer — enforce same-customer rule
            var rowCusId = $(this).closest('.ts-child-row').find('.ts-cus-id').val() || '';
            if (rowCusId && rowCusId !== '') {
                if (cusId === '' || cusId === rowCusId) {
                    // First customer found, or same customer — OK
                    cusId = rowCusId;
                    cusName = $(this).closest('.ts-child-row').find('.ts-cus-mobile').val() || '';
                } else {
                    // Different non-empty customer — conflict
                    customerConflict = true;
                }
            }
        });

        if (selectedCodes.length === 0) {
            $.toaster({ priority: 'warning', title: 'No Selection', message: 'Please select at least one tag' });
            return;
        }

        // Block if selected tags have different customers assigned
        if (customerConflict) {
            $.toaster({ priority: 'danger', title: 'Customer Mismatch', message: 'Selected tags have different customers assigned. Please select tags with the same customer or remove conflicting assignments.' });
            return;
        }

        // Check availability of all selected tags via AJAX
        var origHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Checking ' + selectedCodes.length + ' tags...').prop('disabled', true);

        var validCodes = [];
        var blockedMsgs = [];
        var checked = 0;

        $.each(selectedCodes, function(i, code) {
            $.ajax({
                url: baseUrl + 'index.php/admin_ret_tag_parts/checkTagEstimation',
                type: 'POST',
                dataType: 'json',
                data: { tag_code: code },
                success: function(data) {
                    if (data.available) {
                        validCodes.push(code);
                    } else {
                        blockedMsgs.push(data.message || code + ' not available');
                    }
                },
                error: function() {
                    blockedMsgs.push(code + ' — check failed');
                },
                complete: function() {
                    checked++;
                    if (checked === selectedCodes.length) {
                        // All checks done — show blocked warnings
                        if (blockedMsgs.length > 0) {
                            $.toaster({ priority: 'warning', title: 'Unavailable Tags', message: blockedMsgs.join(', ') });
                        }
                        if (validCodes.length === 0) {
                            $.toaster({ priority: 'info', title: 'Nothing to Add', message: 'No available tags to add to estimation' });
                            $btn.html(origHtml).prop('disabled', false);
                            return;
                        }
                        // Open estimation with valid codes
                        var url = baseUrl + 'index.php/admin_ret_estimation/estimation/add?tag_codes=' + encodeURIComponent(validCodes.join(',')) + '&select_tag_details=1&from_split=1';
                        if (branchId) url += '&branch_id=' + encodeURIComponent(branchId);
                        if (cusId) {
                            url += '&cus_id=' + encodeURIComponent(cusId);
                            url += '&cus_name=' + encodeURIComponent(cusName);
                        }
                        window.open(url, '_blank');
                        $btn.html(origHtml).prop('disabled', false);
                    }
                }
            });
        });
    }

    // New partly sale button
    $(document).on('click', '#btn_new_parts', function() {
        $('.ts-parts-success').remove();
        $('#child_weight_tbody, .totals-row, .validation-row').show();
        resetForm();
        $('#btn_save_parts').show().prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save');
    });

    // =========================================================================
    // Cancel / Reset
    // =========================================================================

    $('#btn_cancel_parts, #btn_reset_tag').click(function () {
        resetForm();
    });

    function resetForm() {
        baseTagData = null;
        childrenData = [];
        costLimit = 200000;
        goldRate = 0;
        wastagePct = 0;
        mcType = 0;
        mcValue = 0;
        calcBased = 0;
        baseCost = 0;
        savedPartsId = null;
        $('#tag_scan_code').val('');
        $('#base_tag_id').val('');
        $('#base_tag_code').val('');
        $('#parts_count').attr('min', 2).val(2);
        $('#step_base_info, #step_actions, #balance_banner').hide();
        $('#tag_details_placeholder').show();
        $('#child_weight_tbody').html('');
        $('.ts-parts-success').remove();
        hideScanError();
        $('#tag_scan_code').focus();
    }

    // =========================================================================
    // Helper: Error display
    // =========================================================================

    function showScanError(msg) {
        $('#tag_scan_error_text').text(msg);
        $('#tag_scan_error').show();
    }

    function hideScanError() {
        $('#tag_scan_error').hide();
        $('#tag_scan_error_text').text('');
    }

    // =========================================================================
    // Phase 4: Cost Legend per row (✓/✗)
    // =========================================================================

    function computeCostForGwt(gwt) {
        if (!baseTagData) return 0;
        var baseGwt = parseFloat(baseTagData.gross_wt) || 0;
        if (baseGwt <= 0) return 0;

        // Prefer sell_rate (proportional)
        if (baseCost > 0) {
            return baseCost * (gwt / baseGwt);
        }

        // Fallback: dynamic calc using gold rate + wastage + MC
        if (goldRate <= 0) return 0;
        var baseNwt = parseFloat(baseTagData.net_wt) || 0;
        var ratio = gwt / baseGwt;
        var nwt = baseNwt * ratio;

        var wastBasis = (calcBased == 0) ? gwt : nwt;
        var wastWt = wastBasis * (wastagePct / 100);
        var wastAmt = wastWt * goldRate;

        var mcAmt = 0;
        if (mcType == 1) mcAmt = mcValue * ratio;
        else if (mcType == 2) mcAmt = mcValue * ((calcBased == 1) ? nwt : gwt);
        else if (mcType == 3) mcAmt = (goldRate * (nwt + wastWt)) * (mcValue / 100);

        return (goldRate * nwt) + wastAmt + mcAmt;
    }

    function computeLegend(child) {
        var gwt = parseFloat(child.gwt) || 0;
        var cost = computeCostForGwt(gwt);
        if (cost <= 0) return '<i class="fa fa-minus" style="color:#ccc;"></i>';
        if (cost <= costLimit) {
            return '<i class="fa fa-check" style="color:#16a34a;" title="₹' + Math.round(cost).toLocaleString() + ' within limit"></i>';
        } else {
            return '<i class="fa fa-times" style="color:#dc2626;" title="₹' + Math.round(cost).toLocaleString() + ' exceeds limit"></i>';
        }
    }

    function updateAllLegends() {
        $('#child_weight_tbody tr').each(function() {
            var child = {
                gwt: parseFloat($(this).find('.child-gwt').val()) || 0,
                nwt: parseFloat($(this).find('.child-nwt').val()) || 0
            };
            $(this).find('.col-legend').html(computeLegend(child));
        });
    }

    // =========================================================================
    // Dynamic Row Creation: On GWT edit, auto-add balance row
    // =========================================================================

    function bindWeightInputs() {
        $('#child_weight_tbody input.child-gwt').off('input').on('input', function () {
            handleGwtEdit($(this));
        });
        $('#child_weight_tbody input').off('input.validate').on('input.validate', function () {
            validatePartsWeights();
        });
    }

    function handleGwtEdit($input) {
        if (!baseTagData) return;
        var baseGwt = parseFloat(baseTagData.gross_wt) || 0;
        var baseNwt = parseFloat(baseTagData.net_wt) || 0;
        var baseLess = parseFloat(baseTagData.less_wt) || 0;
        var baseStone = parseFloat(baseTagData.stone_wt) || 0;
        var baseDia = parseFloat(baseTagData.dia_wt) || 0;
        if (baseGwt <= 0) return;

        var editedIdx = parseInt($input.data('index'));
        var editedGwt = parseFloat($input.val()) || 0;

        // Proportionally recalc the edited row's other weights
        var editedRatio = editedGwt / baseGwt;
        var $editedRow = $input.closest('tr');
        $editedRow.find('.child-nwt').val((baseNwt * editedRatio).toFixed(3));
        $editedRow.find('.child-less').val((baseLess * editedRatio).toFixed(3));
        $editedRow.find('.child-stone').val((baseStone * editedRatio).toFixed(3));
        $editedRow.find('.child-dia').val((baseDia * editedRatio).toFixed(3));

        // Remove all rows AFTER the edited row
        $('#child_weight_tbody tr').each(function() {
            if (parseInt($(this).data('index')) > editedIdx) {
                $(this).remove();
            }
        });

        // Calculate balance from all remaining rows
        var usedGwt = 0;
        $('#child_weight_tbody tr').each(function() {
            usedGwt += parseFloat($(this).find('.child-gwt').val()) || 0;
        });
        var balance = Math.round((baseGwt - usedGwt) * 1000) / 1000;

        // Auto-split balance into rows respecting cost limit
        if (balance > 0.000) {
            var maxGwtPerPart = balance; // default: all in one row

            // Determine max GWT per part based on cost limit
            if (goldRate > 0 && costLimit > 0) {
                var costPerGram = computeCostForGwt(baseGwt) / baseGwt;
                if (costPerGram > 0) {
                    maxGwtPerPart = Math.floor((costLimit / costPerGram) * 1000) / 1000;
                    if (maxGwtPerPart <= 0) maxGwtPerPart = balance;
                }
            }

            // Determine how many new parts needed, then distribute evenly with stagger
            var numNewParts = Math.ceil(balance / maxGwtPerPart);
            if (numNewParts < 1) numNewParts = 1;
            var perPartGwt = Math.floor((balance / numNewParts) * 1000) / 1000;
            var usedBalance = 0;

            for (var p = 1; p <= numNewParts; p++) {
                var partGwt;
                if (p === numNewParts) {
                    // Last part absorbs remainder
                    partGwt = Math.round((balance - usedBalance) * 1000) / 1000;
                } else {
                    // Stagger each row by -0.01g so no two rows are identical
                    partGwt = Math.round((perPartGwt - ((p - 1) * 0.01)) * 1000) / 1000;
                    if (partGwt < 0.001) partGwt = perPartGwt; // safety floor
                }

                var newIdx = $('#child_weight_tbody tr').length;
                var ratio = partGwt / baseGwt;
                var newChild = {
                    child_order: newIdx + 1,
                    tag_code: baseTagData.tag_code + '-' + (newIdx + 1),
                    gwt: partGwt,
                    nwt: Math.round(baseNwt * ratio * 1000) / 1000,
                    less_wt: Math.round(baseLess * ratio * 1000) / 1000,
                    stone_wt: Math.round(baseStone * ratio * 1000) / 1000,
                    dia_wt: Math.round(baseDia * ratio * 1000) / 1000
                };

                var legendHtml = '<td class="col-legend">' + computeLegend(newChild) + '</td>';
                var row = '<tr data-index="' + newIdx + '">' +
                    '<td>' + newChild.child_order + '</td>' +
                    '<td><span class="child-code">' + newChild.tag_code + '</span></td>' +
                    '<td class="col-gwt"><input type="number" step="0.001" class="child-gwt" data-index="' + newIdx + '" value="' + newChild.gwt.toFixed(3) + '"/></td>' +
                    '<td class="col-nwt"><input type="number" step="0.001" class="child-nwt" data-index="' + newIdx + '" value="' + newChild.nwt.toFixed(3) + '"/></td>' +
                    '<td class="col-less"><input type="number" step="0.001" class="child-less" data-index="' + newIdx + '" value="' + newChild.less_wt.toFixed(3) + '"/></td>' +
                    '<td class="col-stone"><input type="number" step="0.001" class="child-stone" data-index="' + newIdx + '" value="' + newChild.stone_wt.toFixed(3) + '"/></td>' +
                    '<td class="col-dia"><input type="number" step="0.001" class="child-dia" data-index="' + newIdx + '" value="' + newChild.dia_wt.toFixed(3) + '"/></td>' +
                    legendHtml +
                    '</tr>';

                $('#child_weight_tbody').append(row);
                usedBalance += partGwt;
            }

            // Re-bind inputs for new rows
            bindWeightInputs();

            // Update parts count display
            $('#parts_count').val($('#child_weight_tbody tr').length);
        }

        // Update children data
        updateChildrenFromInputs();
        validatePartsWeights();
        updateAllLegends();
    }

});

// =============================================================================
// LIST VIEW — Partly Sale History & EOD Queue
// Runs only when list page elements are present
// =============================================================================

$(document).ready(function () {

    // Guard: only run on list view
    if ($('#parts_history_table').length === 0) return;

    var baseUrl = $('#base_url').val();
    var canModify = ($('#access_edit').val() == '1');
    var canRevert = ($('#access_delete').val() == '1');

    // Initialize daterangepicker
    $('#dt_range').daterangepicker({
        format: 'DD-MM-YYYY',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(0, 'days'),
        endDate: moment()
    }, function(start, end) {
        $('#dt_range').val(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
    });

    // Load data
    loadPartsHistory();
    loadEodQueue();

    function loadPartsHistory() {
        var fromDate = '';
        var toDate = '';
        var dtRange = $('#dt_range').val();
        if (dtRange && dtRange.indexOf(' - ') > -1) {
            var parts = dtRange.split(' - ');
            fromDate = parts[0].trim();
            toDate = parts[1].trim();
        }
        var params = {
            status: $('#filter_status').val(),
            from_date: fromDate,
            to_date: toDate
        };

        $.get(baseUrl + 'index.php/admin_ret_tag_parts/get_parts_history', params, function(response) {
            try {
            var data = JSON.parse(response);
            var tbody = '';
            if (data.data && data.data.length > 0) {
                $.each(data.data, function(i, row) {
                    var statusClass = 'ts-badge-active';
                    if (row.status == 2) statusClass = 'ts-badge-reverted';
                    else if (row.status == 3) statusClass = 'ts-badge-partial';
                    else if (row.status == 4) statusClass = 'ts-badge-billed';

                    var actions = '<button class="btn btn-info btn-xs ts-action-btn" onclick="viewPartsDetail(' + row.parts_id + ')" title="View"><i class="fa fa-eye"></i></button>';

                    if (canModify && row.status == 1) {
                        actions += ' <button class="btn btn-warning btn-xs ts-action-btn" onclick="openModifyModal(' + row.parts_id + ')" title="Modify"><i class="fa fa-pencil"></i> Modify</button>';
                    }
                    if (canRevert && row.status == 1) {
                        actions += ' <button class="btn btn-danger btn-xs ts-action-btn" onclick="manualRevert(' + row.parts_id + ')" title="Revert"><i class="fa fa-undo"></i> Revert</button>';
                    }

                    tbody += '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + row.base_tag_code + '</td>' +
                        '<td>' + row.parts_count + '</td>' +
                        '<td>' + parseFloat(row.base_gwt).toFixed(4) + '</td>' +
                        '<td>' + parseFloat(row.base_nwt).toFixed(4) + '</td>' +
                        '<td><span class="ts-badge ' + statusClass + '">' + row.status_label + '</span></td>' +
                        '<td>' + row.unbilled_count + '</td>' +
                        '<td>' + (row.created_by_name || '-') + '</td>' +
                        '<td>' + row.created_on + '</td>' +
                        '<td>' + actions + '</td>' +
                        '</tr>';
                });
            } else {
                tbody = '<tr><td colspan="10" class="text-muted">No partly sale records found</td></tr>';
            }

            if ($.fn.DataTable.isDataTable('#parts_history_table')) {
                $('#parts_history_table').DataTable().destroy();
            }
            $('#parts_history_table tbody').html(tbody);
            $('#parts_history_table').DataTable({ order: [[8, 'desc']], pageLength: 25 });
            } catch(e) {
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Failed to load parts history' });
                console.error('loadPartsHistory parse error:', e, response);
            }
        });
    }

    function loadEodQueue() {
        $.get(baseUrl + 'index.php/admin_ret_tag_parts/get_eod_queue', function(response) {
            try {
            var data = JSON.parse(response);
            var tbody = '';
            var count = 0;
            if (data.data && data.data.length > 0) {
                count = data.data.length;
                $.each(data.data, function(i, row) {
                    var action = '<span class="text-muted"><i class="fa fa-clock-o"></i> Auto at Day Close</span>';
                    if (canRevert) {
                        action = '<button class="btn btn-danger btn-xs ts-action-btn" onclick="manualRevert(' + row.parts_id + ')"><i class="fa fa-undo"></i> Manual Revert</button>';
                    }
                    tbody += '<tr>' +
                        '<td>' + row.base_tag_code + '</td>' +
                        '<td>' + row.total_children + '</td>' +
                        '<td>' + row.unbilled_count + '</td>' +
                        '<td>' + parseFloat(row.base_gwt).toFixed(4) + '</td>' +
                        '<td>' + action + '</td>' +
                        '</tr>';
                });
            } else {
                tbody = '<tr><td colspan="5" class="text-muted">No active parts pending revert</td></tr>';
            }
            $('#eod_queue_tbody').html(tbody);
            $('#eod_queue_count').text(count + ' active' + (count != 1 ? ' parts' : ' part'));
            } catch(e) {
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Failed to load EOD queue' });
                console.error('loadEodQueue parse error:', e, response);
            }
        });
    }

    // Filter buttons
    $('#btn_filter_history').click(function() { loadPartsHistory(); });
    $('#btn_reset_filter').click(function() {
        $('#filter_status').val('');
        $('#dt_range').val('');
        loadPartsHistory();
    });

    // View detail modal
    window.viewPartsDetail = function(partsId) {
        $.get(baseUrl + 'index.php/admin_ret_tag_parts/get_parts_detail/' + partsId, function(response) {
            try {
            var data = JSON.parse(response);
            if (data.status) {
                var d = data.data;

                // Status badge
                var statusMap = {1:'Active', 2:'Reverted', 3:'Partial', 4:'Billed'};
                var badgeMap = {1:'ts-badge-active', 2:'ts-badge-reverted', 3:'ts-badge-partial', 4:'ts-badge-billed'};
                var statusLabel = statusMap[d.master.status] || d.master.status;
                var badgeClass = badgeMap[d.master.status] || 'ts-badge-active';

                // Base tag info card
                var html = '<div style="background:#f0f8ff; border:1px solid #d0e3f0; border-radius:6px; padding:14px 18px; margin-bottom:16px;">';
                html += '<div style="display:flex; flex-wrap:wrap; gap:20px; align-items:center;">';
                html += '<span><b style="color:#5a6a85;">Status:</b> <span class="ts-badge ' + badgeClass + '">' + statusLabel + '</span></span>';
                html += '<span><b style="color:#5a6a85;">GWT:</b> <b>' + parseFloat(d.master.base_gwt).toFixed(4) + '</b></span>';
                html += '<span><b style="color:#5a6a85;">NWT:</b> <b>' + parseFloat(d.master.base_nwt).toFixed(4) + '</b></span>';
                html += '<span><b style="color:#5a6a85;">Stone:</b> <b>' + parseFloat(d.master.base_stone_wt).toFixed(4) + '</b></span>';
                html += '<span><b style="color:#5a6a85;">Dia:</b> <b>' + parseFloat(d.master.base_dia_wt).toFixed(4) + '</b></span>';
                html += '<span><b style="color:#5a6a85;">Less:</b> <b>' + parseFloat(d.master.base_less_wt).toFixed(4) + '</b></span>';
                html += '<span><b style="color:#5a6a85;">Created:</b> <b>' + d.master.created_on + '</b></span>';
                html += '</div></div>';

                // Batch selection header for detail modal
                html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;padding:4px 8px;">';
                html += '<label style="margin:0;font-size:12px;cursor:pointer;"><input type="checkbox" class="ts-modal-select-all" style="margin-right:5px;" /> Select All</label>';
                html += '<button class="btn btn-info btn-xs ts-modal-batch-estimation-btn" disabled style="padding:3px 12px;font-weight:600;" data-branch="' + (d.master.id_branch || '') + '"><i class="fa fa-calculator"></i> Add Selected to Estimation</button>';
                html += '</div>';

                // Children table with premium styling
                html += '<table class="table text-center" style="margin:0; border-collapse:collapse;">';
                html += '<thead><tr style="background:linear-gradient(135deg,#1e3c72,#3c8dbc); color:#fff;">';
                html += '<th style="padding:10px;border:none;width:30px;"><input type="checkbox" class="ts-modal-select-all-thead" style="display:none;" /></th><th style="padding:10px;border:none;">#</th><th style="padding:10px;border:none;">Tag Code</th><th style="padding:10px;border:none;">GWT</th><th style="padding:10px;border:none;">NWT</th><th style="padding:10px;border:none;">Less Wt</th><th style="padding:10px;border:none;">Stone Wt</th><th style="padding:10px;border:none;">Dia Wt</th><th style="padding:10px;border:none;">Status</th><th style="padding:10px;border:none;">Billed</th>';
                html += '</tr></thead><tbody>';
                $.each(d.children, function(i, child) {
                    var tagStatusLabel = child.tag_status == 0 ? '<span class="ts-badge ts-badge-active">On Sale</span>' : child.tag_status == 1 ? '<span class="ts-badge ts-badge-billed">Sold</span>' : '<span class="ts-badge ts-badge-reverted">Deleted</span>';
                    var canSelect = (child.tag_status == 0 && child.is_billed != 1);
                    html += '<tr style="border-bottom:1px solid #f0f3f6;">' +
                        '<td style="padding:8px;">' + (canSelect ? '<input type="checkbox" class="ts-modal-child-select" data-code="' + child.tag_code + '" />' : '') + '</td>' +
                        '<td style="padding:8px;">' + child.child_order + '</td>' +
                        '<td style="padding:8px; font-weight:600;">' + child.tag_code + ' <button class="btn btn-xs btn-default ts-copy-btn" data-code="' + child.tag_code + '" title="Copy tag code" style="padding:1px 6px;margin-left:4px;"><i class="fa fa-copy"></i></button> <button class="btn btn-xs btn-primary ts-add-estimation-btn" data-code="' + child.tag_code + '" data-branch="' + (d.master.id_branch || '') + '" title="Add to Estimation" style="padding:1px 6px;margin-left:2px;"><i class="fa fa-calculator"></i></button></td>' +
                        '<td style="padding:8px;">' + parseFloat(child.child_gwt).toFixed(4) + '</td>' +
                        '<td style="padding:8px;">' + parseFloat(child.child_nwt).toFixed(4) + '</td>' +
                        '<td style="padding:8px;">' + parseFloat(child.child_less_wt).toFixed(4) + '</td>' +
                        '<td style="padding:8px;">' + parseFloat(child.child_stone_wt).toFixed(4) + '</td>' +
                        '<td style="padding:8px;">' + parseFloat(child.child_dia_wt).toFixed(4) + '</td>' +
                        '<td style="padding:8px;">' + tagStatusLabel + '</td>' +
                        '<td style="padding:8px;">' + (child.is_billed == 1 ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-muted"></i>') + '</td>' +
                        '</tr>';
                });
                html += '</tbody></table>';

                $('#modal_base_code').text(d.base_tag.tag_code);
                $('#parts_detail_content').html(html);
                $('#partsDetailModal').modal('show');
            } else {
                $.toaster({ priority: 'danger', title: 'Error', message: data.message || 'Failed to load parts detail' });
            }
            } catch(e) {
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Unexpected server response. Check console for details.' });
                console.error('viewPartsDetail parse error:', e, response);
            }
        });
    };

    // Copy child tag code to clipboard
    $(document).on('click', '.ts-copy-btn', function(e) {
        e.stopPropagation();
        var code = $(this).data('code');
        var $btn = $(this);
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(function() {
                $btn.html('<i class="fa fa-check"></i>').addClass('btn-success').removeClass('btn-default');
                setTimeout(function() { $btn.html('<i class="fa fa-copy"></i>').removeClass('btn-success').addClass('btn-default'); }, 1500);
            });
        } else {
            // Fallback
            var $temp = $('<input>').val(code).appendTo('body').select();
            document.execCommand('copy');
            $temp.remove();
            $btn.html('<i class="fa fa-check"></i>').addClass('btn-success').removeClass('btn-default');
            setTimeout(function() { $btn.html('<i class="fa fa-copy"></i>').removeClass('btn-success').addClass('btn-default'); }, 1500);
        }
    });

    // Add to Estimation button — opens estimation form with tag_code prefilled
    $(document).on('click', '.ts-add-estimation-btn', function(e) {
        e.stopPropagation();
        var code = $(this).data('code');
        var branch = $(this).data('branch') || '';
        var url = baseUrl + 'index.php/admin_ret_estimation/estimation/add?tag_code=' + encodeURIComponent(code);
        if (branch) {
            url += '&branch_id=' + encodeURIComponent(branch);
        }
        window.open(url, '_blank');
    });

    // Modify modal — open in-page modal
    window.openModifyModal = function(partsId) {
        loadModifyData(partsId);
    };

    // =========================================================================
    // Admin Modify Weights Modal
    // =========================================================================

    function loadModifyData(partsId) {
        $('#modify_parts_id').val(partsId);
        $('#modify_parts_id_display').text(partsId);

        $.get(baseUrl + 'index.php/admin_ret_tag_parts/get_parts_detail/' + partsId, function(response) {
            try {
            var data = JSON.parse(response);
            if (!data.status) {
                $.toaster({ priority: 'danger', title: 'Error', message: data.message || 'Failed to load parts detail' });
                return;
            }
            var d = data.data;

            $('#modify_base_code').text(d.base_tag.tag_code);
            $('#modify_base_gwt').text(parseFloat(d.master.base_gwt).toFixed(4));
            $('#modify_base_nwt').text(parseFloat(d.master.base_nwt).toFixed(4));
            $('#modify_base_stone').text(parseFloat(d.master.base_stone_wt).toFixed(4));
            $('#modify_base_dia').text(parseFloat(d.master.base_dia_wt).toFixed(4));
            $('#modify_base_less').text(parseFloat(d.master.base_less_wt).toFixed(4));

            var tbody = '';
            $.each(d.children, function(i, child) {
                var isBilled = child.is_billed == 1;
                var readonlyAttr = isBilled ? 'readonly' : '';
                var lockIcon = isBilled ? ' <i class="fa fa-lock text-danger" title="Billed — locked"></i>' : '';

                tbody += '<tr data-detail-id="' + child.parts_detail_id + '" data-child-tag-id="' + child.child_tag_id + '" data-billed="' + child.is_billed + '">' +
                    '<td>' + child.child_order + '</td>' +
                    '<td>' + child.tag_code + lockIcon + '</td>' +
                    '<td><input type="number" step="0.0001" class="modify-gwt" value="' + parseFloat(child.child_gwt).toFixed(4) + '" ' + readonlyAttr + ' style="text-align:center;width:100%;border:1.5px solid #d5dce6;border-radius:4px;padding:6px;font-size:13px;font-weight:600;"/></td>' +
                    '<td><input type="number" step="0.0001" class="modify-nwt" value="' + parseFloat(child.child_nwt).toFixed(4) + '" ' + readonlyAttr + ' style="text-align:center;width:100%;border:1.5px solid #d5dce6;border-radius:4px;padding:6px;font-size:13px;font-weight:600;"/></td>' +
                    '<td><input type="number" step="0.0001" class="modify-less" value="' + parseFloat(child.child_less_wt).toFixed(4) + '" ' + readonlyAttr + ' style="text-align:center;width:100%;border:1.5px solid #d5dce6;border-radius:4px;padding:6px;font-size:13px;font-weight:600;"/></td>' +
                    '<td><input type="number" step="0.0001" class="modify-stone" value="' + parseFloat(child.child_stone_wt).toFixed(4) + '" ' + readonlyAttr + ' style="text-align:center;width:100%;border:1.5px solid #d5dce6;border-radius:4px;padding:6px;font-size:13px;font-weight:600;"/></td>' +
                    '<td><input type="number" step="0.0001" class="modify-dia" value="' + parseFloat(child.child_dia_wt).toFixed(4) + '" ' + readonlyAttr + ' style="text-align:center;width:100%;border:1.5px solid #d5dce6;border-radius:4px;padding:6px;font-size:13px;font-weight:600;"/></td>' +
                    '<td>' + (isBilled ? '<span class="ts-badge ts-badge-billed">Billed</span>' : '<span class="ts-badge ts-badge-active">Available</span>') + '</td>' +
                    '</tr>';
            });
            $('#modify_child_tbody').html(tbody);

            $('#modify_child_tbody input').on('input', function() {
                validateModifyWeights(d.master);
            });
            validateModifyWeights(d.master);
            $('#adminModifyModal').modal('show');
            } catch(e) {
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Unexpected server response. Check console for details.' });
                console.error('loadModifyData parse error:', e, response);
            }
        });
    }

    function validateModifyWeights(master) {
        var tolerance = 0.001;
        var fields = ['gwt', 'nwt', 'less', 'stone', 'dia'];
        var baseVals = {
            gwt: parseFloat(master.base_gwt),
            nwt: parseFloat(master.base_nwt),
            less: parseFloat(master.base_less_wt),
            stone: parseFloat(master.base_stone_wt),
            dia: parseFloat(master.base_dia_wt)
        };

        var allValid = true;
        $.each(fields, function(i, f) {
            var sum = 0;
            var hasNegative = false;
            $('#modify_child_tbody .modify-' + f).each(function() {
                var v = parseFloat($(this).val()) || 0;
                if (v < 0) hasNegative = true;
                sum += v;
            });

            var diff = Math.abs(sum - baseVals[f]);
            var valid = diff <= tolerance && !hasNegative;
            var tdId = '#modify_total_' + (f == 'less' ? 'less_wt' : f == 'stone' ? 'stone_wt' : f == 'dia' ? 'dia_wt' : f);
            var validId = '#modify_validate_' + (f == 'less' ? 'less_wt' : f == 'stone' ? 'stone_wt' : f == 'dia' ? 'dia_wt' : f);

            $(tdId).text(sum.toFixed(4));

            if (valid) {
                $(validId).html('<span class="valid-indicator"><i class="fa fa-check-circle"></i></span>');
            } else {
                $(validId).html('<span class="invalid-indicator"><i class="fa fa-times-circle"></i></span>');
                allValid = false;
            }
        });

        $('#btn_save_modify').prop('disabled', !allValid);
    }

    // Save modified weights
    $('#btn_save_modify').on('click', function() {
        var partsId = $('#modify_parts_id').val();
        var children = [];

        $('#modify_child_tbody tr').each(function() {
            if ($(this).data('billed') == 1) return;
            children.push({
                parts_detail_id: $(this).data('detail-id'),
                child_tag_id: $(this).data('child-tag-id'),
                gwt: parseFloat($(this).find('.modify-gwt').val()) || 0,
                nwt: parseFloat($(this).find('.modify-nwt').val()) || 0,
                less_wt: parseFloat($(this).find('.modify-less').val()) || 0,
                stone_wt: parseFloat($(this).find('.modify-stone').val()) || 0,
                dia_wt: parseFloat($(this).find('.modify-dia').val()) || 0
            });
        });

        if (!confirm('Save modified weights? This action is logged.')) return;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.post(baseUrl + 'index.php/admin_ret_tag_parts/admin_modify', {
            parts_id: partsId,
            children: JSON.stringify(children)
        }, function(response) {
            try {
                var data = JSON.parse(response);
                if (data.status) {
                    $.toaster({ priority: 'success', title: 'Updated!', message: 'Child weights updated successfully' });
                    $('#adminModifyModal').modal('hide');
                    loadPartsHistory();
                } else {
                    $.toaster({ priority: 'danger', title: 'Update Failed', message: data.message || 'Update failed' });
                    $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Modified Weights');
                }
            } catch(e) {
                $.toaster({ priority: 'danger', title: 'Server Error', message: 'Unexpected server response. Check console for details.' });
                console.error('admin_modify parse error:', e, response);
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Modified Weights');
            }
        });
    });

    // Manual revert
    window.manualRevert = function(partsId) {
        if (confirm('Are you sure you want to revert this partly sale? All unbilled child tags will be cancelled and the base tag will be restored.')) {
            $.post(baseUrl + 'index.php/admin_ret_tag_parts/manual_revert', { parts_id: partsId }, function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.status) {
                        $.toaster({ priority: 'success', title: 'Reverted!', message: 'Partly sale reverted successfully' });
                        loadPartsHistory();
                        loadEodQueue();
                    } else {
                        $.toaster({ priority: 'danger', title: 'Revert Failed', message: data.message || 'Revert operation failed' });
                    }
                } catch(e) {
                    $.toaster({ priority: 'danger', title: 'Server Error', message: 'Unexpected server response. Check console for details.' });
                    console.error('manual_revert parse error:', e, response);
                }
            });
        }
    };

});
