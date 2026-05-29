var kycCamActive = false;
var activeKycPreviewId = null;
var activeKycHiddenId = null;

function open_kyc_camera(preview_id, hidden_id) {
    if (typeof Webcam === 'undefined') {
        console.log("Webcam library is not loaded.");
        return;
    }
    
    // Always aggressively reset any existing Webcam attachment from other scripts 
    // (e.g., customer.js #my_camera) before opening ours to prevent black screen locks.
    try { Webcam.reset(); } catch(e) {}
    
    activeKycPreviewId = preview_id;
    activeKycHiddenId = hidden_id;
    
    // The webcam modal is nested inside #kyc_collection_modal (loaded via AJAX in kyc_tab.php).
    // Bootstrap cannot properly position a modal inside another modal's DOM.
    // Move it to <body> so it renders as a proper top-level centered modal.
    var $wcModal = $('#kyc_webcam_modal');
    if ($wcModal.length && !$wcModal.data('kyc-moved-to-body')) {
        $wcModal.data('kyc-original-parent', $wcModal.parent());
        $wcModal.appendTo('body');
        $wcModal.data('kyc-moved-to-body', true);
    }
    
    $wcModal.modal('show');
}

// Use delegated event binding — #kyc_webcam_modal is loaded via AJAX inside kyc_tab.php,
// so it doesn't exist when kyc.js first loads. $(document).on() handles this correctly.
$(document).on('shown.bs.modal', '#kyc_webcam_modal', function () {
    try {
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        
        Webcam.on('error', function(err) {
            console.log("Camera Error: " + err);
            if (typeof $.toaster === 'function') {
                $.toaster({ priority : 'danger', title : 'Webcam Warning', message : ("Could not load webcam: " + err).substring(0, 100)});
            }
            close_kyc_camera();
        });

        Webcam.attach('#modal_kyc_camera');
        kycCamActive = true;
    } catch (e) {
        console.log("Initialization Error: " + e.message);
        close_kyc_camera();
    }
});

function close_kyc_camera() {
    if (kycCamActive && typeof Webcam !== 'undefined') {
        try { Webcam.reset(); } catch(e) {}
    }
    kycCamActive = false;
    
    var $wcModal = $('#kyc_webcam_modal');
    $wcModal.modal('hide');
    
    // Move the modal back to its original parent so the DOM stays intact for re-use
    var origParent = $wcModal.data('kyc-original-parent');
    if (origParent && origParent.length) {
        $wcModal.appendTo(origParent);
        $wcModal.removeData('kyc-moved-to-body');
    }
}

// Use delegated click handler for the snap button (also dynamically loaded via AJAX)
$(document).on('click', '#btn_actually_snap', function() {
    if (!kycCamActive || !activeKycPreviewId) return;

    try {
        Webcam.snap(function(data_uri) {
            document.getElementById(activeKycPreviewId).src = data_uri;
            document.getElementById(activeKycHiddenId).value = data_uri;
            
            var fileInputId = activeKycHiddenId.replace('hidden', 'file');
            var fileInput = document.getElementById(fileInputId);
            if(fileInput) fileInput.value = '';
            
            close_kyc_camera();

            if (typeof $.toaster === 'function') {
                $.toaster({ priority : 'success', title : 'Success!', message : "Snapshot captured successfully."});
            }
        });
    } catch (e) {
        console.log("Error taking snapshot: " + e.message);
        if (typeof $.toaster === 'function') {
            $.toaster({ priority : 'danger', title : 'Snapshot Failed', message : "Webcam snapshot failed. Please check camera connection."});
        }
        close_kyc_camera();
    }
});

/*kyc numbers validation in customer master kyc tab..... addedOn : 21/06/2023 By Abi  starts */
function validate_kyc_pan(){
    var kyc_pan = $('#kyc_pan').val();
	var regexp = /^[A-Z]{5}\d{4}[A-Z]{1}$/;
    if(kyc_pan != '' && !regexp.test(kyc_pan)){
		 $('#kyc_pan').val('');
		 $('#kyc_pan').attr('placeholder', 'Enter Valid pan No')
	}
}
function validate_aadhaar(){
    var kyc_aadhar = $('#kyc_aadhar').val();
    var regexp = /^\d{12}$/;
    if(kyc_aadhar != '' && !regexp.test(kyc_aadhar)){
		 $('#kyc_aadhar').val('');
		 $('#kyc_aadhar').attr('placeholder', 'Enter Valid Aadhaar No')
	}
}
function validate_dl(){
    var kyc_dl = $('#kyc_dl').val();
    var regexp = /^(([A-Z]{2}[0-9]{2})( )|([A-Z]{2}-[0-9]{2}))((19|20)[0-9][0-9])[0-9]{7}$/;
    if(kyc_dl != '' && !regexp.test(kyc_dl)){
		 $('#kyc_dl').val('');
		 $('#kyc_dl').attr('placeholder', 'Enter Valid driving license No')
	}
}
//kyc validation ends...

// Solves the issue where HTML5 validation fails silently when the required input is in a hidden tab
var hasSwitchedToInvalidTab = false;
document.addEventListener('invalid', function(e) {
    if (!hasSwitchedToInvalidTab) {
        var element = e.target;
        var hasHiddenTabs = false;
        
        // Loop upwards through all nested tab panes and show them
        $(element).parents('.tab-pane').each(function() {
            hasHiddenTabs = true;
            var tabPaneId = $(this).attr('id');
            var tabLink = $('a[href="#' + tabPaneId + '"], a[data-target="#' + tabPaneId + '"]');
            if (tabLink.length) {
                tabLink.tab('show');
            }
        });

        if (hasHiddenTabs) {
            // Allow Bootstrap transition time, then focus the element
            setTimeout(function() { 
                element.focus(); 
            }, 300);
        }
        
        hasSwitchedToInvalidTab = true;
        
        // Reset the lock so it works on subsequent submit attempts
        setTimeout(function() { 
            hasSwitchedToInvalidTab = false; 
        }, 1000);

        if (typeof $.toaster === 'function') {
            var fieldName = $(element).closest('.form-group').find('label').text() || $(element).attr('placeholder') || "a required field";
            $.toaster({ priority : 'danger', title : 'Required Field Missing', message : "Please fill out " + fieldName + " to continue."});
        }
    }
}, true);

// Globally expose the image validator so it can run synchronously inside custom AJAX submit handlers
window.apply_kyc_inputmasks = function() {
    if ($.fn.inputmask) {
        $('.kyc-dynamic-input').each(function() {
            var str = $(this).attr('data-regex');
            if (str && str.trim()) {
                var maskStr = str.replace(/^\^|\$$/g, '');
                
                // Convert \d{N} or [0-9]{N} to '9's
                maskStr = maskStr.replace(/(?:\\d|\[0-9\])\{(\d+)\}/g, function(match, countStr) {
                    var count = parseInt(countStr, 10);
                    return new Array(count + 1).join('9');
                });
                
                // Convert [A-Z]{N} or [A-Za-z]{N} to 'a's
                maskStr = maskStr.replace(/\[[a-zA-Z0-9\-]+\]\{(\d+)\}/g, function(match, countStr) {
                    var count = parseInt(countStr, 10);
                    var charMap = match.indexOf('0-9') !== -1 ? '*' : 'a';
                    return new Array(count + 1).join(charMap);
                });
                
                // Convert \s to literal space
                maskStr = maskStr.replace(/\\s/g, ' ').replace(/\\-/g, '-');
                
                // Only instantiate if it safely matches flat regex structure
                if (maskStr.indexOf('|') === -1 && maskStr.indexOf('(') === -1 && maskStr.indexOf('[') === -1 && maskStr.indexOf('+') === -1) {
                    $(this).inputmask(maskStr);
                }
            }
        });
    }
};

window.validateKycImages = function(formSelector) {
    var isImagesValid = true;
    var firstInvalidImageTab = null;
    
    var rule_logic = $('#kyc_rule_logic').length > 0 ? $('#kyc_rule_logic').val() : 0;

    $(formSelector).find('.kyc-mandatory-file-group').each(function() {
        var fileInput = $(this).find('input[type="file"]');
        var hiddenInput = $(this).find('input[type="hidden"]');
        
        if (fileInput.length > 0 && hiddenInput.length > 0) {
            if (fileInput.val() === '' && hiddenInput.val() === '') {
                isImagesValid = false;
                $(this).css({'border': '2px solid #dd4b39', 'box-shadow': '0 0 5px #dd4b39'}); // Red highlight
                
                if (!firstInvalidImageTab) {
                    firstInvalidImageTab = $(this).closest('.tab-pane').attr('id');
                }
            } else {
                $(this).css({'border': '1px solid #ddd', 'box-shadow': 'none'}); // Reset
            }
        }
    });
    
    // Minimum-One Document Logic Enforcer
    if (rule_logic == 1) {
        var hasAtLeastOne = false;
        
        // Check all dynamic text inputs
        $(formSelector).find('.kyc-dynamic-input').each(function() {
            if ($(this).val().trim() !== '') {
                hasAtLeastOne = true;
            }
        });
        
        // Check all file inputs (including optional ones)
        $(formSelector).find('.kyc-optional-file-group, .kyc-mandatory-file-group').each(function() {
            var fileInput = $(this).find('input[type="file"]');
            var hiddenInput = $(this).find('input[type="hidden"]');
            if (fileInput.length > 0 && hiddenInput.length > 0) {
                if (fileInput.val() !== '' || (hiddenInput.val() !== '' && hiddenInput.val() !== 'existing')) {
                    hasAtLeastOne = true;
                }
            }
        });
        
        if (!hasAtLeastOne) {
            isImagesValid = false;
            // Highlight the entire KYC tab area generically to indicate missing data
            if (typeof $.toaster === 'function') {
                $.toaster({ priority : 'danger', title : 'KYC Required', message : "Please provide at least one valid KYC document (e.g., Aadhar, PAN, or Voter ID) according to the rule policy."});
            }
            return false; // Force stop
        }
    }

    if (!isImagesValid && rule_logic == 0) {
        if (typeof $.toaster === 'function') {
            $.toaster({ priority : 'danger', title : 'Mandatory Document Missing', message : "Please upload or capture all mandatory KYC images before saving."});
        }
        
        if (firstInvalidImageTab) {
            $('a[href="#' + firstInvalidImageTab + '"], a[data-target="#' + firstInvalidImageTab + '"]').tab('show');
        }
    }
    
    return isImagesValid;
};

// Form Submit Validation for Composite File/Webcam Images on Customer Form
// (Interceptor merged into global delegated listener below)

// Dynamic KYC Regex Validation on Blur
$(document).on('blur', '.kyc-dynamic-input', function() {
    var val = $(this).val();
    var regexStr = $(this).attr('data-regex');
    
    if (val !== '' && regexStr) {
        var regexp = new RegExp(regexStr);
        if (!regexp.test(val)) {
            $(this).val('');
            $(this).css('border-color', '');
            $(this).css('color', '');
            $(this).attr('placeholder', 'Invalid format');
            if (typeof $.toaster === 'function') {
                $.toaster({ priority : 'warning', title : 'Validation Error', message : "The entered format is incorrect based on the required pattern."});
            }
        } else {
            $(this).css('border-color', '#00a65a'); // Green border
            $(this).css('color', '#00a65a'); // Green text
        }
    } else {
        $(this).css('border-color', ''); // Reset if empty
        $(this).css('color', '');
    }
});

// Provide live visual feedback while typing
$(document).on('input keyup', '.kyc-dynamic-input', function() {
    var val = $(this).val();
    var regexStr = $(this).attr('data-regex');
    
    if (val !== '' && regexStr) {
        var regexp = new RegExp(regexStr);
        if (regexp.test(val)) {
            $(this).css('border-color', '#00a65a'); // Green border
            $(this).css('color', '#00a65a'); // Green text
        } else {
            $(this).css('border-color', ''); // Default
            $(this).css('color', ''); // Default
        }
    } else {
        $(this).css('border-color', ''); // Default
        $(this).css('color', ''); // Default
    }
});


let dynamicKycCheckTimeout = null;

window.trigger_dynamic_kyc_modal = function(scheme_sel, cus_sel, pay_amt_sel, acc_sel, btn_sel, show_modal_immediately) {
    if (typeof window.payment_kyc_missing === 'undefined') {
        window.payment_kyc_missing = false;
    }
    
    $(btn_sel).css({'pointer-events': 'none', 'opacity': '0.5'});
    clearTimeout(dynamicKycCheckTimeout);
    
    dynamicKycCheckTimeout = setTimeout(function() {
        var id_scheme = $(scheme_sel).length ? $(scheme_sel).val() : 0;
        var id_customer = $(cus_sel).length ? $(cus_sel).val() : 0;
        
        var payment_amt = $(pay_amt_sel).length ? $(pay_amt_sel).val() : 0;
        if (payment_amt === '' || isNaN(payment_amt)) {
            payment_amt = $('.sum_of_amt').length ? $('.sum_of_amt').html() : 0;
        }
        if (payment_amt === '' || isNaN(payment_amt)) payment_amt = 0;
        
        var id_scheme_account = $(acc_sel).length ? $(acc_sel).val() : 0;
        if (!id_scheme_account || id_scheme_account === '') id_scheme_account = 0;
        
        if (!id_scheme || id_scheme == 0 || !id_customer || id_customer == 0) {
            $(btn_sel).css({'pointer-events': 'all', 'opacity': '1'});
            return;
        }
        
        $.ajax({
            url: base_url + "index.php/admin_manage/ajax_get_dynamic_kyc/" + id_scheme + "/" + id_customer + "/" + payment_amt + "/" + id_scheme_account,
            type: "POST",
            success: function(response) {
                var clean_html = $.trim(response);
                if (clean_html.length > 0 && $(clean_html).find('.kyc-dynamic-input, .kyc-mandatory-file-group, .kyc-optional-file-group').length > 0) {
                    window.payment_kyc_missing = true;
                    $('#dynamic_kyc_modal_wrapper').html(clean_html);
                    if (typeof window.apply_kyc_inputmasks === 'function') window.apply_kyc_inputmasks();
                    $('#dynamic_kyc_modal_cus_id').val(id_customer);
                    $('#dynamic_kyc_modal_scheme_id').val(id_scheme);
                    $('#dynamic_kyc_modal_pay_amount').val(payment_amt);
                    $('#dynamic_kyc_modal_sch_acc_id').val(id_scheme_account);
                    
                    if (show_modal_immediately !== false) {
                        $('#kyc_collection_modal').appendTo('body').modal('show');
                    }
                } else {
                    window.payment_kyc_missing = false;
                    $('#dynamic_kyc_modal_wrapper').empty();
                    $('#kyc_collection_modal').modal('hide');
                }
                $(btn_sel).css({'pointer-events': 'all', 'opacity': '1'});
            },
            error: function() {
                $(btn_sel).css({'pointer-events': 'all', 'opacity': '1'});
            }
        });
    }, 500);
};

window.validateKycImagesModal = function(form) {
    var rule_logic = $('#kyc_rule_logic').length > 0 ? $('#kyc_rule_logic').val() : 0;
    var firstInvalidEl = null;
    
    // --- Per-tab validation: each KYC document tab is validated independently ---
    var tabResults = []; // { tabId, tabName, isComplete, hasErrors }
    
    $(form).find('.tab-pane[id^="tab_kyc_"]').each(function() {
        var $tab = $(this);
        var tabId = $tab.attr('id');
        var tabName = $('a[href="#' + tabId + '"]').text().trim() || 'Document';
        var tabComplete = true;  // all mandatory fields filled correctly
        var tabHasErrors = false; // any field has a format error
        var tabHasAnyData = false; // user started filling this tab
        
        // Check text inputs in this tab
        $tab.find('.kyc-dynamic-input').each(function() {
            var val = $(this).val().trim();
            var regexStr = $(this).attr('data-regex');
            var label = $(this).closest('.col-md-5').find('label').text() || tabName;
            label = label.trim();
            
            if (val !== '') {
                tabHasAnyData = true;
                
                // Validate format if regex exists
                if (regexStr && regexStr.trim()) {
                    try {
                        var regex = new RegExp(regexStr);
                        if (!regex.test(val)) {
                            tabComplete = false;
                            tabHasErrors = true;
                            $(this).css({'border': '2px solid #dd4b39'});
                            if (!firstInvalidEl) firstInvalidEl = $(this);
                            if (typeof $.toaster === 'function') {
                                $.toaster({ priority: 'danger', title: 'Invalid Format', message: label + ': Please enter a valid value.' });
                            }
                        } else {
                            $(this).css({'border': ''});
                        }
                    } catch(e) { $(this).css({'border': ''}); }
                } else {
                    $(this).css({'border': ''});
                }
            } else {
                // Empty mandatory text field = tab is not complete
                tabComplete = false;
                $(this).css({'border': ''});
            }
        });
        
        // Check mandatory image uploads in this tab
        $tab.find('.kyc-mandatory-file-group').each(function() {
            var hasFile = false;
            var fileInput = $(this).find('input[type="file"]');
            if (fileInput.length > 0 && fileInput.val() !== '') { hasFile = true; }
            
            var hiddenInput = $(this).find('input[type="hidden"]');
            if (hiddenInput.length > 0 && (hiddenInput.val().indexOf('base64') !== -1 || hiddenInput.val() === 'existing')) { hasFile = true; }
            
            if (hasFile) {
                tabHasAnyData = true;
                $(this).css({'border': '1px solid #ddd', 'box-shadow': 'none'});
            } else {
                tabComplete = false;
            }
        });
        
        tabResults.push({
            tabId: tabId,
            tabName: tabName,
            isComplete: tabComplete,
            hasErrors: tabHasErrors,
            hasAnyData: tabHasAnyData,
            $tab: $tab
        });
    });
    
    // --- If no tabs found (simple flat form), fall back to basic validation ---
    if (tabResults.length === 0) {
        var flatValid = true;
        $(form).find('.kyc-dynamic-input').each(function() {
            var val = $(this).val().trim();
            var regexStr = $(this).attr('data-regex');
            if (val !== '' && regexStr && regexStr.trim()) {
                try {
                    if (!new RegExp(regexStr).test(val)) {
                        flatValid = false;
                        $(this).css({'border': '2px solid #dd4b39'});
                        if (!firstInvalidEl) firstInvalidEl = $(this);
                    }
                } catch(e) {}
            }
        });
        return flatValid;
    }
    
    // --- Apply rule logic ---
    var isValid = true;
    
    if (rule_logic == 0) {
        // ALL REQUIRED: every tab must be complete
        for (var i = 0; i < tabResults.length; i++) {
            var t = tabResults[i];
            if (!t.isComplete) {
                isValid = false;
                // Highlight empty mandatory text fields
                t.$tab.find('.kyc-dynamic-input').each(function() {
                    if ($(this).val().trim() === '') {
                        $(this).css({'border': '2px solid #dd4b39'});
                        if (!firstInvalidEl) firstInvalidEl = $(this);
                    }
                });
                // Highlight missing mandatory images
                t.$tab.find('.kyc-mandatory-file-group').each(function() {
                    var hasFile = false;
                    var fi = $(this).find('input[type="file"]');
                    if (fi.length > 0 && fi.val() !== '') hasFile = true;
                    var hi = $(this).find('input[type="hidden"]');
                    if (hi.length > 0 && (hi.val().indexOf('base64') !== -1 || hi.val() === 'existing')) hasFile = true;
                    if (!hasFile) {
                        $(this).css({'border': '2px solid red', 'box-shadow': '0px 0px 5px red'});
                    }
                });
            }
        }
        if (!isValid && typeof $.toaster === 'function') {
            $.toaster({ priority: 'danger', title: 'KYC Incomplete', message: 'All mandatory KYC documents must be completed.' });
        }
    } else {
        // AT LEAST ONE: at least one tab must be fully complete (number + images)
        var anyTabComplete = false;
        var anyFormatError = false;
        
        for (var i = 0; i < tabResults.length; i++) {
            if (tabResults[i].isComplete) { anyTabComplete = true; }
            if (tabResults[i].hasErrors) { anyFormatError = true; }
        }
        
        // Format errors always block submission
        if (anyFormatError) {
            isValid = false;
        }
        
        if (!anyTabComplete) {
            isValid = false;
            
            // Find partially filled tabs and highlight what's missing
            var highlighted = false;
            for (var i = 0; i < tabResults.length; i++) {
                var t = tabResults[i];
                if (t.hasAnyData && !t.isComplete) {
                    // User started this tab but didn't finish — show what's missing
                    t.$tab.find('.kyc-dynamic-input').each(function() {
                        if ($(this).val().trim() === '') {
                            $(this).css({'border': '2px solid #dd4b39'});
                            if (!firstInvalidEl) firstInvalidEl = $(this);
                            highlighted = true;
                        }
                    });
                    t.$tab.find('.kyc-mandatory-file-group').each(function() {
                        var hasFile = false;
                        var fi = $(this).find('input[type="file"]');
                        if (fi.length > 0 && fi.val() !== '') hasFile = true;
                        var hi = $(this).find('input[type="hidden"]');
                        if (hi.length > 0 && (hi.val().indexOf('base64') !== -1 || hi.val() === 'existing')) hasFile = true;
                        if (!hasFile) {
                            $(this).css({'border': '2px solid red', 'box-shadow': '0px 0px 5px red'});
                        }
                    });
                }
            }
            
            // If no tab was started, highlight the first tab's required fields
            if (!highlighted && tabResults.length > 0) {
                var ft = tabResults[0];
                ft.$tab.find('.kyc-dynamic-input').each(function() {
                    if ($(this).val().trim() === '') {
                        $(this).css({'border': '2px solid #dd4b39'});
                        if (!firstInvalidEl) firstInvalidEl = $(this);
                    }
                });
                ft.$tab.find('.kyc-mandatory-file-group').each(function() {
                    $(this).css({'border': '2px solid red', 'box-shadow': '0px 0px 5px red'});
                });
            }
            
            if (typeof $.toaster === 'function') {
                $.toaster({ priority: 'danger', title: 'KYC Required', message: 'Please complete at least one KYC document fully (number + images).' });
            }
        }
    }
    
    // Focus on first invalid element and switch to its tab
    if (!isValid && firstInvalidEl) {
        var tabPaneId = firstInvalidEl.closest('.tab-pane').attr('id');
        if (tabPaneId) {
            $('a[href="#' + tabPaneId + '"]').tab('show');
        }
        setTimeout(function() { firstInvalidEl.focus(); }, 300);
    }
    
    return isValid;
};

$(document).ready(function() {
    
    // CUSTOMER CREATION / EDIT INITIALIZATION
    if ($('#cus_create').length > 0) {
        var id_customer = $('#edit_id_cus_id').length ? $('#edit_id_cus_id').val() : 0;
        $.ajax({
            url: base_url + "index.php/admin_manage/ajax_get_dynamic_kyc_for_customer/" + id_customer,
            type: "GET",
            success: function(response) {
                var clean_html = $.trim(response);
                if (clean_html.length > 0 && $(clean_html).find('.kyc-dynamic-input, .kyc-mandatory-file-group, .kyc-optional-file-group').length > 0) {
                    window.payment_kyc_missing = true;
                    window.kyc_is_customer_creation_mode = true;
                    $('#dynamic_kyc_modal_wrapper').html(clean_html);
                    if (typeof window.apply_kyc_inputmasks === 'function') window.apply_kyc_inputmasks();
                } else {
                    window.payment_kyc_missing = false;
                    window.kyc_is_customer_creation_mode = false;
                }
            }
        });
    }

    // CUSTOMER CREATION SUBMIT INTERCEPT
    $(document).on('submit', '#cus_create', function(e) {
        if (window.payment_kyc_missing) {
            e.preventDefault();
            e.stopImmediatePropagation();
            // Show the generic KYC collection modal
            $('#kyc_collection_modal').appendTo('body').modal('show');
            if (typeof $.toaster === 'function') {
                $.toaster({ priority : 'danger', title : 'Warning!', message : 'Mandatory KYC documents are required to register this customer.'});
            }
            $("div.overlay").css("display", "none"); 
            setTimeout(function() { $('#save').prop('disabled', false); }, 100);
            return false;
        }

        if (typeof window.validateKycImages === 'function') {
            if (!window.validateKycImages('#cus_create')) {
                e.preventDefault();
                e.stopImmediatePropagation(); 
                setTimeout(function() { $('#save').prop('disabled', false); }, 100);
                return false;
            }
        }
    });

    $('#btn_save_dynamic_kyc_modal').on('click', function(e) {
        e.preventDefault();
        
        // Native form validation check
        var form = document.getElementById('dynamic_kyc_modal_form');
        if (typeof window.validateKycImagesModal === 'function') {
            if (!window.validateKycImagesModal(form)) {
                return false;
            }
        }
        if (!form.checkValidity()) {
            $('<input type="submit">').hide().appendTo(form).click().remove();
            return false;
        }
        
        // CUSTOMER CREATION SPECIAL PASS-THROUGH MODE
        if (window.kyc_is_customer_creation_mode === true) {
            $('.overlay').css('display', 'block');
            window.payment_kyc_missing = false;
            
            // Detach the wrapper from the modal and safely inject it permanently into the main cus_create form!
            // This allows the browser to natively package the File objects along with the customer data!
            $('#dynamic_kyc_modal_wrapper').appendTo('#cus_create').hide();
            $('#kyc_collection_modal').modal('hide');
            
            if (typeof $('#cus_create')[0].requestSubmit === 'function') {
                $('#cus_create')[0].requestSubmit();
            } else {
                $('#cus_create').submit();
            }
            return false;
        }
        
        // Native form validation check
        var form = document.getElementById('dynamic_kyc_modal_form');
        if (typeof window.validateKycImagesModal === 'function') {
            if (!window.validateKycImagesModal(form)) {
                return false;
            }
        }
        if (!form.checkValidity()) {
            $('<input type="submit">').hide().appendTo(form).click().remove();
            return false;
        }

        var formData = new FormData(form);
        
        var id_customer = $('#dynamic_kyc_modal_cus_id').val();
        var id_scheme = $('#dynamic_kyc_modal_scheme_id').val();
        var payment_amt = $('#dynamic_kyc_modal_pay_amount').val();
        var id_scheme_account = $('#dynamic_kyc_modal_sch_acc_id').val();

        $('.overlay').css('display', 'block');
        $.ajax({
            url: base_url + "index.php/admin_manage/ajax_save_kyc_dynamic/" + id_customer + "/" + id_scheme + "/" + payment_amt + "/" + id_scheme_account,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(response) {
                $('.overlay').css('display', 'none');
                if(response.status) {
                    window.payment_kyc_missing = false;
                    $('#kyc_collection_modal').modal('hide');
                    $('#dynamic_kyc_modal_wrapper').empty();
                    if (typeof $.toaster === 'function') {
                        $.toaster({ priority: 'success', title: 'Success', message: 'KYC saved successfully.' });
                    }
                    if ($('#pan').length) {
                        $('#pan').val('Updated'); // override internal missing state just in case
                    }
                } else {
                    if (typeof $.toaster === 'function') {
                        $.toaster({ priority: 'danger', title: 'Error', message: response.message || 'Failed to save KYC.' });
                    }
                }
            },
            error: function(xhr, status, error) {
                $('.overlay').css('display', 'none');
                if (typeof $.toaster === 'function') {
                    $.toaster({ priority: 'danger', title: 'Error', message: 'A server error occurred while saving.' });
                }
            }
        });
    });
    // --- KYC MASTER CRUD ---
    
    // Initialize DataTable
    if ($('#kyc_master_list').length > 0) {
        $('#kyc_master_list').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false
        });
    }

    // Add Attribute Row
    $('#add_kyc_attr').on('click', function() {
        var template = $('#attr_row_template').html();
        $('#kyc_attributes_table tbody').append(template);
    });

    // Remove Attribute Row
    $(document).on('click', '.remove-attr', function() {
        var rowCount = $('#kyc_attributes_table tbody tr').length;
        if (rowCount > 1) {
            $(this).closest('tr').remove();
        } else {
            if (typeof $.toaster === 'function') {
                $.toaster({ priority : 'warning', title : 'Warning', message : 'At least one attribute row must remain, or leave it blank.'});
            } else {
                alert('At least one attribute row must remain.');
            }
        }
    });

    // Delete KYC Master
    $(document).on('click', '.delete-kycmaster', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        if (confirm("Are you sure you want to delete this KYC Document Profile? All its attributes will also be deleted. This cannot be undone.")) {
            $.ajax({
                url: base_url + "index.php/kyc/master_delete/" + id,
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        row.remove();
                        if (typeof $.toaster === 'function') {
                            $.toaster({ priority : 'success', title : 'Deleted', message : response.msg});
                        }
                    } else {
                        if (typeof $.toaster === 'function') {
                            $.toaster({ priority : 'danger', title : 'Error', message : response.msg});
                        } else {
                            alert(response.msg);
                        }
                    }
                },
                error: function() {
                    alert('Error deleting record.');
                }
            });
        }
    });
});
