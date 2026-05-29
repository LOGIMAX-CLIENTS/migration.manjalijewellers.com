/**
 * IRN E-Invoice Settings Dashboard JS
 * Features: Global GSP config, per-branch credentials with inheritance toggle,
 *           Sync All to HO, and activity log
 */

// Cache loaded branch data for client-side inheritance
var _irn_branches = [];

$(document).ready(function () {
    load_irn_settings();
    load_irn_activity();

    // ═══════════════════  SAVE CONFIG  ═══════════════════
    $('#save_global_settings').on('click', function () {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        var settings = {
            'gsp_auth_base_url': $('#gsp_auth_base_url').val(),
            'gsp_einvoice_base_url': $('#gsp_einvoice_base_url').val(),
            'usp_id': $('#usp_id').val(),
            'ci_password': $('#ci_password').val(),
            'sandbox_auth_url': $('#sandbox_auth_url').val(),
            'sandbox_einvoice_url': $('#sandbox_einvoice_url').val(),
            'irn_error_email': $('#irn_error_email').val()
        };

        $.ajax({
            url: base_url + 'index.php/admin_irn_settings/save_settings',
            type: 'POST',
            data: { 'settings': settings },
            dataType: 'JSON',
            success: function (data) {
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Configuration');
                if (data.status) {
                    show_irn_alert('success', '<i class="fa fa-check-circle"></i> Configuration saved successfully');
                    $('#global_save_ok').fadeIn().delay(2500).fadeOut();
                } else {
                    show_irn_alert('danger', '<i class="fa fa-times-circle"></i> Failed to save configuration');
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Configuration');
                show_irn_alert('danger', '<i class="fa fa-times-circle"></i> Server error');
            }
        });
    });

    // ═══════════════════  SYNC ALL TO HO  ═══════════════════
    $('#sync_all_branches').on('click', function () {
        // Find the HO branch and its GST group
        var ho_branch = null;
        var ho_gst = '';
        $.each(_irn_branches, function (i, br) {
            if (br.is_ho === '1') { ho_branch = br; ho_gst = (br.gst_number || '').trim(); return false; }
        });
        if (!ho_branch) {
            if (_irn_branches.length > 0) { ho_branch = _irn_branches[0]; ho_gst = (ho_branch.gst_number || '').trim(); }
        }
        if (!ho_branch || (!ho_branch.aspid && !ho_branch.gsp_password)) {
            show_irn_alert('warning', '<i class="fa fa-exclamation-triangle"></i> HO branch has no credentials to sync');
            return;
        }

        // Read current values from the HO GST group card (user may have edited them)
        var ho_group_id = ho_gst ? 'gst_' + ho_gst.replace(/[^a-zA-Z0-9]/g, '_') : '';
        var ho_aspid = ho_group_id ? ($('#aspid_' + ho_group_id).val() || ho_branch.aspid || '') : (ho_branch.aspid || '');
        var ho_user  = ho_group_id ? ($('#gsp_user_name_' + ho_group_id).val() || ho_branch.gsp_user_name || '') : (ho_branch.gsp_user_name || '');
        var ho_pwd   = ho_group_id ? ($('#gsp_password_' + ho_group_id).val() || ho_branch.gsp_password || '') : (ho_branch.gsp_password || '');
        var ho_einv  = ho_group_id ? ($('#eInvPwd_' + ho_group_id).val() || ho_branch.eInvPwd || '') : (ho_branch.eInvPwd || '');

        if (!confirm('Copy HO credentials (ASP ID, GSP Username, GSP Password, eInv Password) to ALL non-HO GST groups?\n\nGSTIN will NOT be changed.')) {
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing...');

        // Collect all non-HO branches
        var non_ho_branches = [];
        $.each(_irn_branches, function (i, br) {
            if (br.id_branch === ho_branch.id_branch) return;
            non_ho_branches.push(br);
        });

        if (non_ho_branches.length === 0) {
            btn.prop('disabled', false).html('<i class="fa fa-clone"></i> Sync All to HO');
            show_irn_alert('info', 'No other branches to sync');
            return;
        }

        var pending = non_ho_branches.length;
        var errors = 0;

        $.each(non_ho_branches, function (i, br) {
            $.ajax({
                url: base_url + 'index.php/admin_irn_settings/save_branch',
                type: 'POST',
                data: {
                    'id_branch': br.id_branch,
                    'aspid': ho_aspid,
                    'gsp_password': ho_pwd,
                    'gsp_user_name': ho_user,
                    'eInvPwd': ho_einv
                },
                dataType: 'JSON',
                success: function (data) {
                    if (!data.status) errors++;
                    pending--;
                    if (pending <= 0) _syncAllComplete(btn, errors);
                },
                error: function () {
                    errors++;
                    pending--;
                    if (pending <= 0) _syncAllComplete(btn, errors);
                }
            });
        });
    });

    function _syncAllComplete(btn, errors) {
        btn.prop('disabled', false).html('<i class="fa fa-clone"></i> Sync All to HO');
        if (errors === 0) {
            show_irn_alert('success', '<i class="fa fa-check-circle"></i> All branches synced to HO credentials');
            $('#sync_all_ok').fadeIn().delay(3000).fadeOut();
            // Reload to refresh GST-grouped cards with updated values
            load_irn_settings();
        } else {
            show_irn_alert('danger', '<i class="fa fa-times-circle"></i> ' + errors + ' branch(es) failed to sync');
        }
    }

    // Refresh activity button
    $('#refresh_activity').on('click', function () {
        load_irn_activity();
    });
});

// ═══════════════════  LOAD SETTINGS  ═══════════════════
function load_irn_settings() {
    $(".overlay").show();
    $.ajax({
        url: base_url + 'index.php/admin_irn_settings/ajax',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
            $(".overlay").hide();
            _irn_branches = data.branches || [];
            render_status_strip(data.settings, data.branches, data.current_base_url);
            render_prod_warning(data.settings, data.current_base_url);
            populate_config_fields(data.settings);
            render_branch_cards(data.branches, data.branch_status);
        },
        error: function () {
            $(".overlay").hide();
            show_irn_alert('danger', '<i class="fa fa-times-circle"></i> Failed to load IRN settings. Check if the migration has been run.');
        }
    });
}

// ═══════════════════  STATUS STRIP  ═══════════════════
function render_status_strip(s, branches, current_url) {
    var mode_val = s.is_auto_gen_irn || '0';
    var mode_label, mode_class, mode_icon;
    switch (mode_val) {
        case '1': mode_label = 'Debug (Print JSON)'; mode_class = 'sc-debug'; mode_icon = 'fa-bug'; break;
        case '2': mode_label = 'Live (Auto Generate)'; mode_class = 'sc-live'; mode_icon = 'fa-bolt'; break;
        case '3': mode_label = 'Manual (Report Button)'; mode_class = 'sc-live'; mode_icon = 'fa-hand-pointer-o'; break;
        default:  mode_label = 'Disabled'; mode_class = 'sc-disabled'; mode_icon = 'fa-power-off'; break;
    }

    var env_val = s.is_production || '0';
    var env_label = env_val === '1' ? 'Production' : 'Non-Production';
    var env_class = env_val === '1' ? 'sc-prod' : 'sc-sandbox';
    var env_icon  = env_val === '1' ? 'fa-server' : 'fa-flask';

    var total_branches = branches.length;
    var configured = 0;
    $.each(branches, function(i, br) {
        if (br.aspid && br.gsp_password && br.gsp_user_name && br.eInvPwd && br.gst_number) configured++;
    });

    var html = '';
    html += '<div class="status-card ' + mode_class + '">';
    html += '  <div class="sc-icon"><i class="fa ' + mode_icon + '"></i></div>';
    html += '  <div class="sc-label">IRN Generation Mode</div>';
    html += '  <div class="sc-value">' + mode_label + '</div>';
    html += '  <div class="sc-hint">Change in Settings → Retail Settings</div>';
    html += '</div>';

    html += '<div class="status-card ' + env_class + '">';
    html += '  <div class="sc-icon"><i class="fa ' + env_icon + '"></i></div>';
    html += '  <div class="sc-label">Environment</div>';
    html += '  <div class="sc-value">' + env_label + '</div>';
    html += '  <div class="sc-hint">Change in Settings → Retail Settings</div>';
    html += '</div>';

    html += '<div class="status-card sc-branches">';
    html += '  <div class="sc-icon"><i class="fa fa-building-o"></i></div>';
    html += '  <div class="sc-label">Branches Configured</div>';
    html += '  <div class="sc-value">' + configured + ' / ' + total_branches + '</div>';
    html += '  <div class="sc-hint">' + (configured === total_branches ? 'All branches ready' : (total_branches - configured) + ' branch(es) need credentials') + '</div>';
    html += '</div>';

    $('#status_strip').html(html);
}

// ═══════════════════  PRODUCTION WARNING  ═══════════════════
function render_prod_warning(s, current_url) {
    if (s.is_production !== '1') {
        $('#prod_warning').hide();
        return;
    }
    var prod_url = s.production_base_url || '';
    var matches = (prod_url !== '' && current_url === prod_url);

    var body = 'IRN e-invoices will be submitted to the <strong>live GSP API</strong>.<br>';
    body += '<div style="margin-top:8px;">';
    body += '<span style="opacity:.7;">Production URL:</span> <span class="pw-url">' + (prod_url || 'NOT SET') + '</span><br>';
    body += '<span style="opacity:.7;">Current base_url():</span> <span class="pw-url">' + current_url + '</span>';
    if (matches) {
        body += ' <span class="pw-match pw-match-yes"><i class="fa fa-check"></i> Match — IRN ACTIVE</span>';
    } else {
        body += ' <span class="pw-match pw-match-no"><i class="fa fa-times"></i> Mismatch — IRN Blocked</span>';
    }
    body += '</div>';
    $('#prod_warning_body').html(body);
    $('#prod_warning').show();
}

// ═══════════════════  POPULATE CONFIG FIELDS  ═══════════════════
function populate_config_fields(s) {
    if (s) {
        $('#gsp_auth_base_url').val(s.gsp_auth_base_url || '');
        $('#gsp_einvoice_base_url').val(s.gsp_einvoice_base_url || '');
        $('#usp_id').val(s.usp_id || '');
        $('#ci_password').val(s.ci_password || '');
        $('#sandbox_auth_url').val(s.sandbox_auth_url || 'http://gstsandbox.charteredinfo.com/eivital/dec/v1.03/auth');
        $('#sandbox_einvoice_url').val(s.sandbox_einvoice_url || 'http://gstsandbox.charteredinfo.com/eicore/dec/v1.03/Invoice');
        $('#irn_error_email').val(s.irn_error_email || '');
    }
}

// ═══════════════════  BRANCH CARDS (Grouped by GST)  ═══════════════════
// Cache the GST-grouped structure for save operations
var _irn_gst_groups = {};

function render_branch_cards(branches, branch_status) {
    $('#branch_count').text(branches.length + ' branch' + (branches.length !== 1 ? 'es' : ''));

    // ─── Group branches by gst_number ───
    var gst_groups = {};   // { gst_number: [branch, branch, ...] }
    var gst_order  = [];   // preserve display order (HO GSTs first)
    var no_gst     = [];   // branches with no GST set

    $.each(branches, function (i, br) {
        var gst = (br.gst_number || '').trim();
        if (!gst) {
            no_gst.push(br);
            return;
        }
        if (!gst_groups[gst]) {
            gst_groups[gst] = [];
            gst_order.push(gst);
        }
        gst_groups[gst].push(br);
    });

    // Sort: GST groups containing HO come first
    gst_order.sort(function (a, b) {
        var a_has_ho = gst_groups[a].some(function (br) { return br.is_ho === '1'; });
        var b_has_ho = gst_groups[b].some(function (br) { return br.is_ho === '1'; });
        if (a_has_ho && !b_has_ho) return -1;
        if (!a_has_ho && b_has_ho) return 1;
        return 0;
    });

    // Store for save operations
    _irn_gst_groups = gst_groups;

    // Find HO branch for inheritance dropdown
    var ho_id = '';
    $.each(branches, function (i, br) {
        if (br.is_ho === '1') { ho_id = br.id_branch; return false; }
    });
    if (!ho_id && branches.length > 0) ho_id = branches[0].id_branch;

    var html = '';

    // ─── Render one card per unique GST ───
    $.each(gst_order, function (gi, gst) {
        var group = gst_groups[gst];
        // Use the first branch in the group as the "representative" for credential values
        var rep = group[0];
        var group_id = 'gst_' + gst.replace(/[^a-zA-Z0-9]/g, '_');
        var branch_ids = [];
        var branch_names = [];
        var has_ho = false;
        var has_token = false;

        $.each(group, function (bi, br) {
            branch_ids.push(br.id_branch);
            var bname = br.name;
            if (br.is_ho === '1') { bname += ' (HO)'; has_ho = true; }
            branch_names.push(bname);
            if (br.authtoken && br.authtoken.length > 0) has_token = true;
        });

        var has_all = (rep.aspid && rep.gsp_password && rep.gsp_user_name && rep.eInvPwd);
        var badge_class = has_all ? 'bc-complete' : 'bc-missing';
        var badge_text  = has_all ? 'Configured' : 'Incomplete';

        html += '<div class="col-md-12">';
        html += '  <div class="branch-card" id="gst_card_' + group_id + '">';

        // Header — GST as primary, branch names as secondary
        html += '    <div class="bc-header">';
        html += '      <div>';
        html += '        <div class="bc-gst" style="font-size:13px;font-weight:700;color:#2d3436;margin-bottom:4px;">';
        html += '          <i class="fa fa-id-card-o" style="margin-right:6px;color:#6c5ce7;"></i>GST: ' + gst;
        html += '        </div>';
        html += '        <div style="display:flex;flex-wrap:wrap;gap:6px;">';
        $.each(group, function (bi, br) {
            var is_ho = br.is_ho === '1';
            html += '<span style="font-size:11px;padding:2px 8px;border-radius:12px;';
            if (is_ho) {
                html += 'background:#6c5ce7;color:#fff;font-weight:600;">';
                html += '<i class="fa fa-building" style="margin-right:3px;"></i>' + br.name + ' (HO)';
            } else {
                html += 'background:#dfe6e9;color:#636e72;font-weight:500;">';
                html += '<i class="fa fa-building-o" style="margin-right:3px;"></i>' + br.name;
            }
            html += '</span>';
        });
        html += '        </div>';
        html += '      </div>';
        html += '      <span class="bc-badge ' + badge_class + '">' + badge_text + '</span>';
        html += '    </div>';

        // Body — single set of credential fields per GST
        html += '    <div class="bc-body">';

        // ─── Inheritance Toggle Row (only for non-HO GST groups) ───
        if (!has_ho) {
            html += '      <div class="bc-inherit-row">';
            html += '        <label class="inherit-switch">';
            html += '          <input type="checkbox" id="inherit_toggle_' + group_id + '" class="inherit-toggle-gst" data-gst="' + gst + '">';
            html += '          <span class="slider"></span>';
            html += '        </label>';
            html += '        <label class="toggle-label" for="inherit_toggle_' + group_id + '">Copy credentials from</label>';
            html += '        <select class="bc-source-select-gst" id="inherit_source_' + group_id + '" data-gst="' + gst + '" disabled>';
            // Build dropdown: other GST groups
            $.each(gst_order, function (oi, other_gst) {
                if (other_gst === gst) return; // skip self
                var other_group = gst_groups[other_gst];
                var label = other_gst;
                var other_has_ho = other_group.some(function (br) { return br.is_ho === '1'; });
                if (other_has_ho) label += ' (HO)';
                var selected = other_has_ho ? ' selected' : '';
                html += '<option value="' + other_gst + '"' + selected + '>' + label + '</option>';
            });
            html += '        </select>';
            html += '      </div>';
        }

        // ─── Credential Fields ───
        html += '      <div class="row">';
        html += '        <div class="col-md-6"><div class="form-group" style="margin-bottom:10px;"><label>ASP ID</label>';
        html += '          <input type="text" class="form-control cred-field" id="aspid_' + group_id + '" value="' + (rep.aspid || '') + '" placeholder="ASP ID">';
        html += '        </div></div>';
        html += '        <div class="col-md-6"><div class="form-group" style="margin-bottom:10px;"><label>GSP Username</label>';
        html += '          <input type="text" class="form-control cred-field" id="gsp_user_name_' + group_id + '" value="' + (rep.gsp_user_name || '') + '" placeholder="API Username">';
        html += '        </div></div>';
        html += '      </div>';
        html += '      <div class="row">';
        html += '        <div class="col-md-6"><div class="form-group" style="margin-bottom:6px;"><label>GSP Password</label>';
        html += '          <input type="password" class="form-control cred-field" id="gsp_password_' + group_id + '" value="' + (rep.gsp_password || '') + '" placeholder="GSP Password">';
        html += '        </div></div>';
        html += '        <div class="col-md-6"><div class="form-group" style="margin-bottom:6px;"><label>E-Invoice Password</label>';
        html += '          <input type="password" class="form-control cred-field" id="eInvPwd_' + group_id + '" value="' + (rep.eInvPwd || '') + '" placeholder="E-Invoice Password">';
        html += '        </div></div>';
        html += '      </div>';
        html += '    </div>';

        // Footer
        html += '    <div class="bc-footer">';
        if (has_token) {
            html += '      <span class="auth-token"><i class="fa fa-key" style="margin-right:4px;"></i>Token active</span>';
        } else {
            html += '      <span class="auth-token"><i class="fa fa-key" style="margin-right:4px;color:#ddd;"></i>No token</span>';
        }
        html += '      <div>';
        html += '        <button type="button" class="btn btn-primary save-gst-btn" data-gst="' + gst + '" data-group-id="' + group_id + '" style="font-size:12px;padding:5px 14px;border-radius:4px;">';
        html += '          <i class="fa fa-save"></i> Save';
        html += '        </button>';
        html += '        <span class="save-ok" id="gst_save_ok_' + group_id + '"><i class="fa fa-check-circle"></i> Saved</span>';
        html += '      </div>';
        html += '    </div>';

        html += '  </div>';
        html += '</div>';
    });

    // ─── Render branches with no GST set ───
    $.each(no_gst, function (ni, br) {
        var is_ho = (br.is_ho === '1');
        html += '<div class="col-md-12">';
        html += '  <div class="branch-card" id="branch_card_' + br.id_branch + '" style="border-color:#fab1a0;">';
        html += '    <div class="bc-header" style="background:#fff5f5;">';
        html += '      <div>';
        html += '        <div class="bc-name"><i class="fa fa-building-o" style="margin-right:6px;color:#d63031;"></i>' + br.name;
        if (is_ho) html += ' <span style="font-size:9px;background:#6c5ce7;color:#fff;padding:1px 6px;border-radius:10px;margin-left:4px;">HO</span>';
        html += '</div>';
        html += '        <div class="bc-gst" style="color:#d63031;"><i class="fa fa-exclamation-triangle" style="margin-right:4px;"></i>GST Number Not Set</div>';
        html += '      </div>';
        html += '      <span class="bc-badge bc-missing">No GST</span>';
        html += '    </div>';
        html += '    <div class="bc-body" style="padding:12px 16px;color:#636e72;font-size:12px;">';
        html += '      <i class="fa fa-info-circle" style="margin-right:4px;"></i>Set GST number in Branch Master before configuring GSP credentials.';
        html += '    </div>';
        html += '  </div>';
        html += '</div>';
    });

    $('#branch_cards_container').html(html);

    // ─── Bind: Save per GST group (saves all branches in the group) ───
    $('.save-gst-btn').on('click', function () {
        var gst = $(this).data('gst');
        var group_id = $(this).data('group-id');
        save_gst_group(gst, group_id, $(this));
    });

    // ─── Bind: Inheritance toggle (GST-level) ───
    $('.inherit-toggle-gst').on('change', function () {
        var gst = $(this).data('gst');
        var group_id = 'gst_' + gst.replace(/[^a-zA-Z0-9]/g, '_');
        var checked = $(this).is(':checked');
        var sourceSelect = $('#inherit_source_' + group_id);
        var fields = ['aspid', 'gsp_user_name', 'gsp_password', 'eInvPwd'];

        if (checked) {
            sourceSelect.prop('disabled', false);
            _populateFromGstSource(group_id, sourceSelect.val());
            $.each(fields, function (fi, f) {
                $('#' + f + '_' + group_id).addClass('inherited').prop('readonly', true);
            });
        } else {
            sourceSelect.prop('disabled', true);
            $.each(fields, function (fi, f) {
                $('#' + f + '_' + group_id).removeClass('inherited').prop('readonly', false);
            });
        }
    });

    // ─── Bind: Source GST dropdown change ───
    $('.bc-source-select-gst').on('change', function () {
        var gst = $(this).data('gst');
        var group_id = 'gst_' + gst.replace(/[^a-zA-Z0-9]/g, '_');
        var toggle = $('#inherit_toggle_' + group_id);
        if (toggle.is(':checked')) {
            _populateFromGstSource(group_id, $(this).val());
        }
    });
}

/**
 * Populate credential fields from a source GST group (client-side from cached data)
 */
function _populateFromGstSource(targetGroupId, sourceGst) {
    var src_group = _irn_gst_groups[sourceGst];
    if (!src_group || src_group.length === 0) return;
    var src = src_group[0]; // use the representative branch from source GST

    $('#aspid_' + targetGroupId).val(src.aspid || '');
    $('#gsp_user_name_' + targetGroupId).val(src.gsp_user_name || '');
    $('#gsp_password_' + targetGroupId).val(src.gsp_password || '');
    $('#eInvPwd_' + targetGroupId).val(src.eInvPwd || '');
}

// ═══════════════════  SAVE GST GROUP  ═══════════════════
/**
 * Save credentials to ALL branches in a GST group
 */
function save_gst_group(gst, group_id, btn) {
    var group = _irn_gst_groups[gst];
    if (!group || group.length === 0) return;

    var aspid        = $('#aspid_' + group_id).val();
    var gsp_password = $('#gsp_password_' + group_id).val();
    var gsp_user_name= $('#gsp_user_name_' + group_id).val();
    var eInvPwd      = $('#eInvPwd_' + group_id).val();

    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    var pending = group.length;
    var errors  = 0;

    $.each(group, function (i, br) {
        $.ajax({
            url: base_url + 'index.php/admin_irn_settings/save_branch',
            type: 'POST',
            data: {
                'id_branch': br.id_branch,
                'aspid': aspid,
                'gsp_password': gsp_password,
                'gsp_user_name': gsp_user_name,
                'eInvPwd': eInvPwd
            },
            dataType: 'JSON',
            success: function (data) {
                if (!data.status) errors++;
                // Update cached branch data
                br.aspid = aspid;
                br.gsp_password = gsp_password;
                br.gsp_user_name = gsp_user_name;
                br.eInvPwd = eInvPwd;
                pending--;
                if (pending <= 0) _gstSaveComplete(gst, group_id, btn, errors);
            },
            error: function () {
                errors++;
                pending--;
                if (pending <= 0) _gstSaveComplete(gst, group_id, btn, errors);
            }
        });
    });
}

function _gstSaveComplete(gst, group_id, btn, errors) {
    btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
    if (errors === 0) {
        $('#gst_save_ok_' + group_id).fadeIn().delay(2500).fadeOut();
        // Update badge
        var card = $('#gst_card_' + group_id);
        var allFilled = ($('#aspid_' + group_id).val() && $('#gsp_password_' + group_id).val() && $('#gsp_user_name_' + group_id).val() && $('#eInvPwd_' + group_id).val());
        card.find('.bc-badge').removeClass('bc-missing bc-complete').addClass(allFilled ? 'bc-complete' : 'bc-missing').text(allFilled ? 'Configured' : 'Incomplete');
    } else {
        show_irn_alert('danger', '<i class="fa fa-times-circle"></i> ' + errors + ' branch(es) failed to save for GST ' + gst);
    }
}

// ═══════════════════  ACTIVITY LOG  ═══════════════════
function load_irn_activity() {
    $.ajax({
        url: base_url + 'index.php/admin_irn_settings/irn_activity_ajax',
        type: 'GET',
        data: { limit: 30 },
        dataType: 'JSON',
        success: function (resp) {
            var rows = resp.data || [];
            if (rows.length === 0) {
                $('#irn_activity_body').html('<tr><td colspan="7" class="text-center" style="color:#b2bec3;padding:24px;"><i class="fa fa-inbox" style="font-size:18px;display:block;margin-bottom:6px;"></i>No billing records found</td></tr>');
                return;
            }
            var html = '';
            var bill_types = {1: 'Sale', 2: 'Sale', 3: 'Transfer', 4: 'Transfer', 14: 'Return'};
            $.each(rows, function (i, r) {
                var status_class, status_text;
                if (r.irn_status === 'success') {
                    status_class = 'irn-badge-success';
                    status_text = 'Generated';
                } else {
                    status_class = 'irn-badge-pending';
                    status_text = 'Pending';
                }
                var type_label = bill_types[r.bill_type] || 'Sale';
                var irn_display = r.irn_number ? '<span style="font-family:monospace;font-size:10px;" title="' + r.irn_number + '">' + r.irn_number.substring(0, 24) + '...</span>' : '<span style="color:#b2bec3;">—</span>';
                
                html += '<tr>';
                html += '<td style="font-weight:600;"><a href="' + base_url + 'index.php/admin_ret_billing/billing_invoice/' + r.bill_id + '" target="_blank" style="color:#0984e3;text-decoration:none;">' + (r.bill_no || r.bill_id) + '</a></td>';
                html += '<td>' + r.bill_date + '</td>';
                html += '<td>' + r.customer_name + '</td>';
                html += '<td>' + (r.branch_name || '—') + '</td>';
                html += '<td>' + type_label + '</td>';
                html += '<td><span class="irn-status-badge ' + status_class + '">' + status_text + '</span></td>';
                html += '<td>' + irn_display + '</td>';
                html += '</tr>';
            });
            $('#irn_activity_body').html(html);
        },
        error: function () {
            $('#irn_activity_body').html('<tr><td colspan="7" class="text-center" style="color:#d63031;padding:24px;"><i class="fa fa-exclamation-triangle"></i> Failed to load activity log</td></tr>');
        }
    });
}

// ═══════════════════  ALERT HELPER  ═══════════════════
function show_irn_alert(type, message) {
    $('#irn_alert_box').removeClass('alert-success alert-danger alert-warning alert-info').addClass('alert-' + type);
    $('#irn_alert_msg').html(message);
    $('#irn_alert').slideDown();
    setTimeout(function () { $('#irn_alert').slideUp(); }, 4000);
}
