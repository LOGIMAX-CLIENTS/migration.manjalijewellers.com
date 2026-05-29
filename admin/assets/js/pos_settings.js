/**
 * POS Settings — Provider & Device Master JS
 * File: assets/js/pos_settings.js
 * BIL-NR01
 * 
 * This runs after window load (jQuery + base_url are ready from footer)
 */
window.addEventListener('load', function(){

    // =============================================
    // CONFIRM MODAL (replaces native confirm())
    // =============================================
    
    // Inject confirm modal HTML if not present
    if($('#posConfirmModal').length === 0){
        $('body').append(
            '<div class="modal fade" id="posConfirmModal" tabindex="-1">' +
                '<div class="modal-dialog modal-sm">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header" style="background:#f0ad4e;color:#fff;">' +
                            '<button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>' +
                            '<h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm</h4>' +
                        '</div>' +
                        '<div class="modal-body" id="posConfirmMsg" style="font-size:14px;"></div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                            '<button type="button" class="btn btn-warning" id="posConfirmYes">Yes, Proceed</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    /**
     * Show a styled confirm modal instead of native confirm()
     * @param {string} message - HTML-safe message to display
     * @param {function} onYes - callback if user clicks Yes
     */
    function posConfirm(message, onYes){
        $('#posConfirmMsg').html(message);
        $('#posConfirmYes').off('click').on('click', function(){
            $('#posConfirmModal').modal('hide');
            if(typeof onYes === 'function') onYes();
        });
        $('#posConfirmModal').modal('show');
    }

    // =============================================
    // PROVIDER FUNCTIONS
    // =============================================
    
    window.openAddProviderModal = function(){
        $('#providerModalTitle').text('Add POS Provider');
        $('#frm_pos_provider')[0].reset();
        $('#prov_id_provider').val('');
        $('#prov_is_active').prop('checked', true);
        $('#providerModal').modal('show');
    };

    window.editProvider = function(providerId){
        $.ajax({
            url: base_url + 'index.php/admin_pos/getPOSProviderById',
            type: 'POST',
            data: { id_provider: providerId },
            dataType: 'json',
            success: function(p){
                $('#providerModalTitle').text('Edit: ' + p.provider_name);
                $('#prov_id_provider').val(p.id_provider);
                $('#prov_provider_name').val(p.provider_name);
                $('#prov_provider_code').val(p.provider_code);
                $('#prov_auth_type').val(p.auth_type);
                $('#prov_api_url_uat_init').val(p.api_url_uat_init);
                $('#prov_api_url_uat_status').val(p.api_url_uat_status);
                $('#prov_api_url_uat_cancel').val(p.api_url_uat_cancel);
                $('#prov_api_url_live_init').val(p.api_url_live_init);
                $('#prov_api_url_live_status').val(p.api_url_live_status);
                $('#prov_api_url_live_cancel').val(p.api_url_live_cancel);
                $('#prov_is_env_live').val(p.is_env_live);
                $('#prov_has_qr_display').prop('checked', p.has_qr_display == 1);
                $('#prov_has_callback').prop('checked', p.has_callback == 1);
                $('#prov_is_active').prop('checked', p.is_active == 1);
                $('#providerModal').modal('show');
            },
            error: function(xhr){
                $.toaster({ priority: 'danger', title: 'Error!', message: '</br>Failed to load provider: ' + xhr.statusText });
            }
        });
    };

    window.saveProvider = function(){
        var formData = $('#frm_pos_provider').serialize();
        if(!$('#prov_has_qr_display').is(':checked')) formData += '&has_qr_display=0';
        if(!$('#prov_has_callback').is(':checked')) formData += '&has_callback=0';
        if(!$('#prov_is_active').is(':checked')) formData += '&is_active=0';
        
        var isEdit = $('#prov_id_provider').val() != '';
        $.ajax({
            url: base_url + 'index.php/admin_pos/' + (isEdit ? 'updatePOSProvider' : 'savePOSProvider'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res){
                if(res.status == 'success'){
                    $.toaster({ priority: 'success', title: 'Success!', message: '</br>' + res.message });
                    setTimeout(function(){ location.reload(); }, 1000);
                } else {
                    $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + res.message });
                }
            },
            error: function(xhr){
                $.toaster({ priority: 'danger', title: 'Network Error!', message: '</br>' + xhr.statusText });
            }
        });
    };

    window.toggleProviderEnv = function(providerId, currentState){
        var newState = currentState ? 0 : 1;
        var msg = newState
            ? '<strong>⚠️ Switch to LIVE?</strong><br>This will enable <b>real transactions</b> with this provider.'
            : 'Switch back to <b>UAT (sandbox)</b> mode?';
        
        posConfirm(msg, function(){
            $.ajax({
                url: base_url + 'index.php/admin_pos/toggleProviderEnv',
                type: 'POST',
                data: { id_provider: providerId, is_env_live: newState },
                dataType: 'json',
                success: function(){
                    $.toaster({ priority: 'success', title: 'Success!', message: '</br>Environment switched to ' + (newState ? 'LIVE' : 'UAT') });
                    setTimeout(function(){ location.reload(); }, 1000);
                },
                error: function(xhr){
                    $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + xhr.statusText });
                }
            });
        });
    };

    window.viewProviderUrls = function(providerId){
        $.ajax({
            url: base_url + 'index.php/admin_pos/getPOSProviderById',
            type: 'POST',
            data: { id_provider: providerId },
            dataType: 'json',
            success: function(p){
                var html = '<h4>' + p.provider_name + ' <code>' + p.provider_code + '</code></h4>';
                html += '<table class="table table-bordered">';
                html += '<tr class="success"><th colspan="2">UAT (Sandbox)</th></tr>';
                html += '<tr><td width="15%">Init</td><td><code>' + (p.api_url_uat_init||'-') + '</code></td></tr>';
                html += '<tr><td>Status</td><td><code>' + (p.api_url_uat_status||'-') + '</code></td></tr>';
                html += '<tr><td>Cancel</td><td><code>' + (p.api_url_uat_cancel||'-') + '</code></td></tr>';
                html += '<tr class="danger"><th colspan="2">LIVE (Production)</th></tr>';
                html += '<tr><td>Init</td><td><code>' + (p.api_url_live_init||'-') + '</code></td></tr>';
                html += '<tr><td>Status</td><td><code>' + (p.api_url_live_status||'-') + '</code></td></tr>';
                html += '<tr><td>Cancel</td><td><code>' + (p.api_url_live_cancel||'-') + '</code></td></tr>';
                html += '</table>';
                $('#providerUrlsTitle').text(p.provider_name + ' — API URLs');
                $('#providerUrlsBody').html(html);
                $('#providerUrlsModal').modal('show');
            },
            error: function(xhr){
                $.toaster({ priority: 'danger', title: 'Error!', message: '</br>Failed to load URLs: ' + xhr.statusText });
            }
        });
    };

    // =============================================
    // DEVICE FUNCTIONS
    // =============================================

    window.toggleDeviceFields = function(){
        var code = $('#dev_id_provider').find(':selected').data('code') || '';
        $('#pinelabs_fields').toggle(code == 'pinelabs');
        $('#phonepe_fields').toggle(code.indexOf('phonepe') === 0);
    };

    window.openAddDeviceModal = function(){
        $('#deviceModalTitle').text('Add POS Device');
        $('#frm_pos_device')[0].reset();
        $('#dev_id_device').val('');
        $('#pinelabs_fields').hide();
        $('#phonepe_fields').hide();
        $('#deviceModal').modal('show');
    };

    window.editDevice = function(deviceId){
        $.ajax({
            url: base_url + 'index.php/admin_pos/getPOSDeviceById',
            type: 'POST',
            data: { id_device: deviceId },
            dataType: 'json',
            success: function(d){
                $('#deviceModalTitle').text('Edit: ' + d.dispname);
                $('#dev_id_device').val(d.id_device);
                $('#dev_dispname').val(d.dispname);
                $('#dev_id_provider').val(d.id_provider);
                $('#dev_merchantid').val(d.merchantid);
                $('#dev_devicetype').val(d.devicetype);
                $('#dev_is_default').val(d.is_default);
                $('#dev_securitytoken').val(d.securitytoken || '');
                $('#dev_imei').val(d.imei || '');
                $('#dev_poscode').val(d.poscode || '');
                $('#dev_salt_key').val(d.salt_key || '');
                $('#dev_salt_index').val(d.salt_index || '1');
                $('#dev_provider_id').val(d.provider_id || '');
                $('#dev_store_id').val(d.store_id || '');
                $('#dev_terminal_id').val(d.terminal_id || '');
                $('#dev_callback_url').val(d.callback_url || '');
                $('#dev_id_pay_device').val(d.id_pay_device || '');
                toggleDeviceFields();
                $('#deviceModal').modal('show');
            },
            error: function(xhr){
                $.toaster({ priority: 'danger', title: 'Error!', message: '</br>Failed to load device: ' + xhr.statusText });
            }
        });
    };

    window.saveDevice = function(){
        var formData = $('#frm_pos_device').serialize();
        var isEdit = $('#dev_id_device').val() != '';
        $.ajax({
            url: base_url + 'index.php/admin_pos/' + (isEdit ? 'updatePOSDevice' : 'savePOSDevice'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res){
                if(res.status == 'success'){
                    $.toaster({ priority: 'success', title: 'Success!', message: '</br>' + res.message });
                    setTimeout(function(){ location.reload(); }, 1000);
                } else {
                    $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + res.message });
                }
            },
            error: function(xhr){
                $.toaster({ priority: 'danger', title: 'Network Error!', message: '</br>' + xhr.statusText });
            }
        });
    };

    window.deleteDevice = function(deviceId){
        posConfirm('Are you sure you want to <b>deactivate</b> this device?', function(){
            $.ajax({
                url: base_url + 'index.php/admin_pos/deletePOSDevice',
                type: 'POST',
                data: { id_device: deviceId },
                dataType: 'json',
                success: function(res){
                    $.toaster({ priority: 'success', title: 'Success!', message: '</br>' + res.message });
                    setTimeout(function(){ location.reload(); }, 1000);
                },
                error: function(xhr){
                    $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + xhr.statusText });
                }
            });
        });
    };

    window.setDefaultDevice = function(deviceId){
        $.ajax({
            url: base_url + 'index.php/admin_pos/setDefaultPOSDevice',
            type: 'POST',
            data: { id_device: deviceId },
            dataType: 'json',
            success: function(){
                $.toaster({ priority: 'success', title: 'Success!', message: '</br>Default device updated' });
                setTimeout(function(){ location.reload(); }, 1000);
            },
            error: function(xhr){
                $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + xhr.statusText });
            }
        });
    };

    console.log('POS Settings JS loaded ✅');
});
