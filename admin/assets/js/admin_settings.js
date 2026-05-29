//general setings Functions here
var path = url_params();
var ctrl_page = path.route.split('/');
console.log(ctrl_page);
$(document).ready(function () {
    if (ctrl_page[1] != 'access') {
        get_branchnames();
    }
    //Code Added by Durga starts here 20-06-2023
    if (ctrl_page['1'] == 'general') {
        var displayformat = $("#display_format").val();
        var display_receipt_format = $("#display_receipt_format").val();
        if (displayformat == 2) {
            var allValues = $('#selected_field_list option').map(function () {
                return {
                    value: $(this).val(),
                    text: $(this).text().trim().replace(/\s+/g, ' ')
                };
            }).get();
            var arrayString = JSON.stringify(allValues);
            $("#acc_format_hidden").val(arrayString);
            show_sample(allValues, 'ACCOUNT');
            $("#ac_num_block").css('display', 'block');
        }
        else {
            $("#ac_num_block").css('display', 'none');
        }
        if (display_receipt_format == 2) {
            var allValues = $('#selected_receipt_field_list option').map(function () {
                return {
                    value: $(this).val(),
                    text: $(this).text().trim().replace(/\s+/g, ' ')
                };
            }).get();
            show_sample(allValues, 'RECEIPT');
            var arrayString = JSON.stringify(allValues);
            $("#rcpt_format_hidden").val(arrayString);
            $("#rcpt_num_block").css('display', 'block');
        }
        else {
            $("#rcpt_num_block").css('display', 'none');
        }
    }
    $('input[type="radio"][name="general[schemeaccNo_displayFrmt]"]').change(function () {
        // Code to execute when the Scheme A/C No. Display Format radio button changes
        var selectedValue = $(this).val();
        if (selectedValue == 2) {
            $("#ac_num_block").css('display', 'block');
        }
        else {
            $("#ac_num_block").css('display', 'none');
        }
    });
    $('input[type="radio"][name="general[receiptNo_displayFrmt]"]').change(function () {
        // Code to execute when the Scheme A/C No. Display Format radio button changes
        var selectedValue = $(this).val();
        if (selectedValue == 2) {
            $("#rcpt_num_block").css('display', 'block');
        }
        else {
            $("#rcpt_num_block").css('display', 'none');
        }
    });
    //Code Added by Durga ends here 20-06-2023
    set_modules_list();
    set_paymentgateway_list();
    set_upi_paymentgateway_list();
    if (ctrl_page['1'] == 'module') {
        set_modules_list();
    }
    if (ctrl_page['1'] == 'payment') {
        get_offrate_list();
    }
    //get_branchname()
    if (ctrl_page['1'] == 'payment') {
        set_paymentgateway_list();
        set_upi_paymentgateway_list();
    }
    if (ctrl_page['1'] == 'terms_and_conditions') {
        get_terms_and_conditions();
        if ($('#content').length > 0) {
            CKEDITOR.replace('content');
        }
    }
    if (ctrl_page['1'] == 'retail_setting') {
        set_retail_settings_list();
    }
    //Offline Rate List Admin with date picker//HH 
    $('#offrate_list1').empty();
    $('#offrate_list2').empty();
    $('#offrate_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
    $('#offrate_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
    $('#offrate-dt-btn').daterangepicker(
        {
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function (start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#offrate_list1').text(start.format('YYYY-MM-DD'));
            $('#offrate_list2').text(end.format('YYYY-MM-DD'));
            var id_branch = $("#branch_select").val();
            get_offrate_list(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), id_branch)
        }
    );
    //Offline Rate List Admin with date picker//HH
    $("input[name='metal_type']").click(function () {
        if ($("#metal_type_partial").is(":checked")) {
            $("#partial_silverate").removeAttr("disabled");
            $("#partial_goldrate").removeAttr("disabled");
            $("#partial_silverate").focus();
            $("#partial_goldrate").focus();
        } else {
            $('#partial_silverate').val(0);
            $('#partial_goldrate').val(0);
            $("#partial_silverate").attr("disabled", "disabled");
            $("#partial_goldrate").attr("disabled", "disabled");
        }
    });
    $("input[name='ed_metal_type']").click(function () {
        if ($("#rate_partial").is(":checked")) {
            $("#ed_partial_silverate").removeAttr("disabled");
            $("#ed_partial_goldrate").removeAttr("disabled");
            $("#ed_partial_silverate").focus();
            $("#ed_partial_goldrate").focus();
        } else {
            $('#ed_partial_goldrate').val(0);
            $('#ed_partial_silverate').val(0);
            $("#ed_partial_silverate").attr("disabled", "disabled");
            $("#ed_partial_goldrate").attr("disabled", "disabled");
        }
    });
    $('body').on('focus', "#entry_date", function () {
        $('#entry_date').datetimepicker({
            format: 'yyyy-mm-dd',
        });
    });
    var edit_custom_entry_date = $("#edit_custom_entry_date").is(':checked') ? 1 : 0;
    if (edit_custom_entry_date == 0) {
        $('#entry_date').attr('disabled', true);
    }
    else {
        $('#entry_date').attr('disabled', false);
    }
    $("input[name='general[edit_custom_entry_date]']:checkbox").on('change', function () {
        var edit_custom_entry_date = $("#edit_custom_entry_date").is(':checked') ? 1 : 0;
        if (edit_custom_entry_date == 0) {
            $('#entry_date').attr('disabled', true);
            $('#entry_date').val('');
        }
        else {
            $('#entry_date').val('');
            $('#entry_date').attr('disabled', false);
        }
    });
    if ($('#is_branchwise_cus_reg').val() == 1) {
        get_branchname();
    }
    //mail settings
    if ($('#send_through:checked').val() == 0) {
        $("input[name='mail[server_type]']:radio").attr('disabled', true);
        $('#smtp_pass').attr('disabled', true);
        $('#smtp_host').attr('disabled', true);
        $('#smtp_user	').attr('disabled', true);
    }
    if ($('#server_type:checked').val() == 1) {
        $('#smtp_pass').attr('required', true);
        $('#smtp_host').attr('required', true);
        $('#smtp_user	').attr('required', true);
    }
    $("input[name='mail[server_type]']:radio").on('change', function () {
        if ($(this).val() == 1) {
            $('#smtp_pass').attr('required', true);
            $('#smtp_host').attr('required', true);
            $('#smtp_user	').attr('required', true);
        }
        else {
            $('#smtp_pass').attr('required', false);
            $('#smtp_host').attr('required', false);
            $('#smtp_user	').attr('required', false);
        }
    });
    $("input[name='mail[send_through]']:radio").on('change', function () {
        if ($(this).val() == 0) {
            $("input[name='mail[server_type]']:radio").attr('disabled', true);
            $('#smtp_pass').attr('disabled', true);
            $('#smtp_host').attr('disabled', true);
            $('#smtp_user	').attr('disabled', true);
        }
        else {
            $("input[name='mail[server_type]']:radio").attr('disabled', false);
            $('#smtp_pass').attr('disabled', false);
            $('#smtp_host').attr('disabled', false);
            $('#smtp_user	').attr('disabled', false);
        }
    });
    //mail settings
    $("#update_clsfy").on('click', function () {
        var clsfy = {
            classification_name: $("#ed_clsfy").val(),
            description: CKEDITOR.instances.description1.getData(),
            file: $("#edit_sch_clsfy_img")[0].files[0]
        };
        console.log(clsfy);
        var id = $("#edit-id").val();
        update_classification(clsfy, id);
    });
    $("#sch_clsfy_img,#edit_sch_clsfy_img").change(function (e) {
        e.preventDefault();
		validate_Image1(this);
    });
    if ($('#allow_referral:checked').val() == 1) {
        $('#sch_benefit').prop('disabled', false);
        $('#walllet_benefit').prop('disabled', false);
    }
    else {
        $('#sch_benefit').prop('disabled', true);
        $('#walllet_benefit').prop('disabled', true);
    }
    $("input[name='general[allow_referral]']:radio").on('change', function () {
        if ($(this).val() == 1) {
            $('#sch_benefit').prop('disabled', false);
            $('#walllet_benefit').prop('disabled', false);
        }
        else {
            $('#sch_benefit').prop('disabled', true);
            $('#walllet_benefit').prop('disabled', true);
        }
    });
    if ($('#wallet_balance_type:checked').val() == 0) {
        $('#wallet_points').prop('disabled', true);
        $('#wallet_amt_per_points').prop('disabled', true);
    }
    else {
        $('#wallet_points').prop('disabled', false);
        $('#wallet_amt_per_points').prop('disabled', false);
    }
    $("input[name='general[wallet_balance_type]']:radio").on('change', function () {
        if ($(this).val() == 1) {
            $('#wallet_points').prop('disabled', false);
            $('#wallet_amt_per_points').prop('disabled', false);
        }
        else {
            $('#wallet_points').prop('disabled', true);
            $('#wallet_amt_per_points').prop('disabled', true);
        }
    });
    $("input[name='general[isOTPRegForPayment]']:checkbox").on("change", function () {
        if ($("input[name='general[isOTPRegForPayment]']:checked").is(":checked")) {
            $("#payOTP_exp").prop("disabled", false);
        } else {
            $("#payOTP_exp").prop("disabled", true);
        }
    });
    if ($("input[name='general[isOTPRegForPayment]']:checked").is(":checked")) {
        $("#payOTP_exp").prop("disabled", false);
    } else {
        $("#payOTP_exp").prop("disabled", true);
    }
    $("input[name='general[isOTPReqToLogin]']:checkbox").on("change", function () {
        if ($("input[name='general[isOTPReqToLogin]']:checked").is(":checked")) {
            $("#loginOTP_exp").prop("disabled", false);
        } else {
            $("#loginOTP_exp").prop("disabled", true);
        }
    });
    if ($("input[name='general[isOTPReqToLogin]']:checked").is(":checked")) {
        $("#loginOTP_exp").prop("disabled", false);
    } else {
        $("#loginOTP_exp").prop("disabled", true);
    }
    //Check box for gift otp functions starts here
    $("input[name='general[isOTPReqToGift]']:checkbox").on("change", function () {
        if ($("input[name='general[isOTPReqToGift]']:checked").is(":checked")) {
            $("#isOTPReqToGift").val(1);
            $("#giftOTP_exp").prop("disabled", false);
        } else {
            $("#isOTPReqToGift").val(0);
            $("#giftOTP_exp").prop("disabled", true);
        }
        var s = $("#isOTPReqToGift").val();
        console.log("cc:" + s);
    });
    if ($("input[name='general[isOTPReqToGift]']:checked").is(":checked")) {
        $("#isOTPReqToGift").val(1);
        $("#giftOTP_exp").prop("disabled", false);
    } else {
        $("#isOTPReqToGift").val(0);
        $("#giftOTP_exp").prop("disabled", true);
    }
    //Check box for gift issue functions ends here
    // promotion credit enable 
    if ($('#enable_promot').is(':checked')) {
        $('#enable_promot1').val(1);
        $('#create_promotion').prop('disabled', true);
    }
    else {
        $('#enable_promot1').val(0);
        $('#create_promotion').prop('disabled', true);
    }
    $(".enable_promotion").on('change', function () {
        if ($('#enable_promot').is(':checked')) {
            $('#enable_promot1').val(1);
            $('#create_promotion').prop('disabled', false);
        }
        else {
            $('#enable_promot1').val(0);
            $('#create_promotion').prop('disabled', true);
        }
    });
    // promotion credit enable 	
    // discount amount settings //
    $("input[name='general[enableGoldrateDisc]']:checkbox").on("change", function () {
        if ($("input[name='general[enableGoldrateDisc]']:checked").is(":checked")) {
            $("#goldDiscAmt").prop("disabled", false);
        } else {
            $("#goldDiscAmt").prop("disabled", true);
        }
    });
    if ($("input[name='general[enableGoldrateDisc]']:checked").is(":checked")) {
        $("#goldDiscAmt").prop("disabled", false);
    } else {
        $("#goldDiscAmt").prop("disabled", true);
    }
    $("input[name='general[enableGoldrateDisc_18k]']:checkbox").on("change", function () {
        if ($("input[name='general[enableGoldrateDisc_18k]']:checked").is(":checked")) {
            $("#goldDiscAmt_18k").prop("disabled", false);
        } else {
            $("#goldDiscAmt_18k").prop("disabled", true);
        }
    });
    if ($("input[name='general[enableGoldrateDisc_18k]']:checked").is(":checked")) {
        $("#goldDiscAmt_18k").prop("disabled", false);
    } else {
        $("#goldDiscAmt_18k").prop("disabled", true);
    }
    // otp credit enable 
    if ($('#enable_otpsms').is(':checked')) {
        $('#enable_otp1').val(1);
        $('#credit_sms').prop('disabled', true);
    }
    else {
        $('#enable_otp1').val(0);
        $('#credit_sms').prop('disabled', true);
    }
    $(".enable_otp").on('change', function () {
        if ($('#enable_otpsms').is(':checked')) {
            $('#enable_otp1').val(1);
            $('#credit_sms').prop('disabled', false);
        }
        else {
            $('#enable_otp1').val(0);
            $('#credit_sms').prop('disabled', true);
        }
    });
    // otp credit enable 	
    if (ctrl_page[1] == 'village') {
        get_village_list();
    }
    switch (ctrl_page[0]) {
        case 'branch':
            set_branch_table()
            break;
        case 'settings':
            if (ctrl_page[1] == 'rate' && ctrl_page[2] == 'add') {
                // get_branchname();
            }
            break;
    }
    $("#update_card_branch").on('click', function () {
        var card = $("#edcard_brand").val();
        var code = $("#edshort_code").val();
        var card_type = $("#edcard_type").val();
        var id = $("#edit-id").val();
        update_cardbrand(card_type, card, code, id);
    });
    $("input[name='edcardtype']:radio").on('change', function () {
        if ($(this).val() == 1) {
            $('#edcard_type').val(1);
        }
        else {
            $('#edcard_type').val(2);
        }
    });
    $("#add_cardbrand").on('click', function () {
        if ($('#card_brand').length > 0) {
            add_card_brand($('#card_type').val(), $('#card_brand').val(), $('#short_code').val());
            $('#card_brand').val('');
        }
    });
    $('#m_active').bootstrapSwitch();
    val_ckeck();
    $('#allow_advance_payment').click(function () {
        val_ckeck();
    });
    $('#pre_closer').click(function () {
        val_ckeck();
    });
    $('#allow_pending_due').click(function () {
        val_ckeck();
    });
    $('#m_save').click(function () {
        get_maintenance();
    });
    $("#offer_img,#new_arrivals_img").change(function () {
        event.preventDefault();
        validateImage(this);
    });
    //added by durga 12.05.2023 starts here
    // version number validation using regular expression
    $('#version_no').on('blur onchange', function () {
        var inputtxt = $("#version_no").val();
        //with starting one alphabet pattern like (v00.11.22)
        //var pattern=/^(?:([a-zA-Z]\d{1,2})\.)?(?:(\d{1,2})\.)?(\*|\d{1,2})$/;
        //pattern like 11.00.22
        var pattern = /^(?:(\d{1,2})\.)?(?:(\d{1,2})\.)?(\*|\d{1,2})$/;
        var result = inputtxt.match(pattern);
        if (!result) {
            alert("Please Enter Valid Version Number");
            $("#version_no").val("");
        }
    });
    //added by durga 12.05.2023 ends here
    $('#mobile').on('blur onchange', function () {
        if (this.value.length != mob_no_len) {
            $(this).val('');
            $(this).attr('placeholder', 'Enter valid mobile number');
            /*$(this).focus();*/
        }
    });
    //console.log(mob_no_len);	
    $('#mobile1').on('blur onchange', function () {
        if (this.value.length != mob_no_len) {
            $(this).val('');
            $(this).attr('placeholder', 'Enter valid mobile number');
            /*$(this).focus();*/
        }
    });
    if ($('#country').length > 0) {
        get_country();
    }
    if ($('#countryCurr').length > 0) {
        get_countryCurr();
    }
    $('#country').on('change', function () {
        get_state(this.value);
    });
    $('#state').on('change', function () {
        get_city(this.value);
    });
    //For editor
    if ($('#description').length > 0) {
        CKEDITOR.replace('description');
    }
    if ($('#description1').length > 0) {
        CKEDITOR.replace('description1');
    }
    if ($('#description3').length > 0) {
        CKEDITOR.replace('description3');
    }
	// Code by Kumaran 29/07/2025 -- Need Remove Option - Scheme Classification
	// Started
	$('.remove-img-btn').on('click', function () {
		var action = $(this).data('action');		
		if(action === "ADD"){
			var currentImageSrc = $('#sch_clsfy_img_preview').attr('src');
			if (!currentImageSrc.endsWith('no_image.png')) {
				$('#sch_clsfy_img').val('');
				$('#sch_clsfy_img_preview').attr('src', base_url + 'assets/img/no_image.png');
				$('#confirm-delete-img').modal('hide');
			}
		}else if(action === "EDIT"){
			var currentImageSrc = $('#edit_sch_clsfy_img_preview').attr('src');
			if (!currentImageSrc.endsWith('no_image.png')) {
				$('#edit_sch_clsfy_img').val('');
				$('#edit_sch_clsfy_img_preview').attr('src', base_url + 'assets/img/no_image.png');
				$('#edit-confirm-delete-img').modal('hide');
		}
	}
	})
$(document).on('hidden.bs.modal', function () {
    if ($('.modal.in').length > 0) {
        // Reapply modal-open to body if other modals are still open
        $('body').addClass('modal-open');
    }
});
	// Ended
    // Code By Karthikai Kumaran for show social media links ON 16/10/2025
    // Toggle switch
            // Run on page load
             $('.social-toggle').bootstrapSwitch();
            toggleSocialMediaUrls();
            // Run on change
            $('.social-toggle').on('switchChange.bootstrapSwitch', function(event, state) {
                toggleSocialMediaUrls();
            });
                const regexPatterns = {
                    facebook: /^https:\/\/(www\.)?facebook\.com\/[A-Za-z0-9_.\-\/@]+$/i,
                    instagram: /^https:\/\/(www\.)?instagram\.com\/[A-Za-z0-9_.\-\/@]+\/?$/i,
                    youtube: /^https:\/\/(www\.)?youtube\.com\/(@[A-Za-z0-9_.\-]+|channel\/[A-Za-z0-9_\-]+|c\/[A-Za-z0-9_\-]+|user\/[A-Za-z0-9_\-]+)$/i,
                    whatsapp: /^https:\/\/(wa\.me|api\.whatsapp\.com)\/[A-Za-z0-9\/?=_.\-]+$/i
                };
            // Regex validation for URL
             $('#fb_url').on('blur change', function () {
                validateUrl('#fb_url', regexPatterns.facebook, 'Facebook');
            });
            $('#whatsapp_url').on('blur change', function () {
                validateUrl('#whatsapp_url', regexPatterns.whatsapp, 'WhatsApp');
            });
            $('#insta_url').on('blur change', function () {
                validateUrl('#insta_url', regexPatterns.instagram, 'Instagram');
            });
            $('#youtube_url').on('blur change', function () {
                validateUrl('#youtube_url', regexPatterns.youtube, 'YouTube');
            });
            // Function to validate a pan req amt
            $('#pan_req_amt').on('input', function(){
                let value = $(this).val();
                // Allow numbers with up to 2 decimal places
                if (!/^\d*\.?\d{0,2}$/.test(value)) {
                    // Remove invalid characters
                    value = value.slice(0, -1);
                    $(this).val(value);
                }
            });
});
//GG
$('#start_date').change(function () {
    var datep = $('#start_date').val();
    var d = new Date();
    var currentDate = new Date(d),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
    var output = (month < 10 ? '0' : '') + month + '/' + (day < 10 ? '0' : '') + day + '/' + year;
    if (Date.parse(datep) - Date.parse(output) < 0) {
        alert("Selected date is in the past");
        $("#start_date").val('');
    }
});
//GG
function validateImage() {
    var height = ($(this).height());
    var width = ($(this).width());
    if (arguments[0].id == 'offer_img') {
        var preview = $('#offer_img_preview');
    }
    else {
        var preview = $('#new_arrivals_img_preview');
    }
    //   1mb size 1048576
    // 10 mb size 10534243
    if (arguments[0].files[0].size > 10534243) {
        alert('File size cannot be greater than 1 MB');
        arguments[0].value = "";
        preview.css('display', 'none');
    }
    else {
        var fileName = arguments[0].value;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        ext = ext.toLowerCase();
        if (ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg") {
            alert("Upload JPG or PNG Images only");
            arguments[0].value = "";
            preview.css('display', 'none');
        }
        /*if(width>960 && height>525)
        {
            alert("Width and height should be less than  960 * 525 ");
            arguments[0].value = "";
            preview.css('display','none');
        }*/
        else {
            var file = arguments[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.prop('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
                preview.css('display', '');
            }
            else {
                preview.prop('src', '');
                preview.css('display', 'none');
            }
        }
    }
}
function val_ckeck() {
    if (!($('#allow_advance_payment').is(':checked'))) {
        $('#Allow_Payment_wt,#Allow_Payment_amt').attr("disabled", "disabled");
    }
    else {
        $('#Allow_Payment_wt,#Allow_Payment_amt').removeAttr("disabled", "false");
    }
    if (!($('#pre_closer').is(':checked'))) {
        $('#ins_pending').attr("disabled", "disabled");
        $('#benefits').bootstrapSwitch('disabled', true);
    }
    else {
        $('#ins_pending').removeAttr("disabled", "false");
        $('#benefits').bootstrapSwitch('disabled', false);
    }
    if (!($('#allow_pending_due').is(':checked'))) {
        $('#allow_pending_wgt,#allow_pending_amt').attr("disabled", "disabled");
    }
    else {
        $('#allow_pending_wgt,#allow_pending_amt').removeAttr("disabled", "false");
    }
}
//select all customers in imported list        
$('#sel_imported_all').click(function (event) {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
    event.stopPropagation();
});
//send SMS to selected customers in imported list
$("#sendSMS").click(function () {
    var data = { 'account_id[]': [] };
    $("input[name='account_id[]']:checked").each(function () {
        data['account_id[]'].push($(this).val());
    });
    $("div.overlay").css("display", "block");
    $.ajax({
        type: "POST",
        url: base_url + "index.php/settings/import/send_login",
        data: data,
        sync: false,
        success: function (data) {
            $("div.overlay").css("display", "none");
            $('#alert_msg').html(data);
            $(".alert").css("display", "block");
        }
    });
});
//send Email to selected customers in imported list
$("#sendEmail").click(function () {
    var data = { 'account_id[]': [] };
    $("input[name='account_id[]']:checked").each(function () {
        data['account_id[]'].push($(this).val());
    });
    $("div.overlay").css("display", "block");
    $.ajax({
        type: "POST",
        url: base_url + "index.php/settings/import/send_login_email",
        data: data,
        sync: false,
        success: function (data) {
            $("div.overlay").css("display", "none");
            $('#alert_msg').html(data);
            $(".alert").css("display", "block");
        }
    });
});
//initialize Plugin
$(function () {
    //form menu item
    $(".test").select2({
        placeholder: 'Enter name',
        //Does the user have to enter any data before sending the ajax request
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            //How long the user has to pause their typing before sending the next request
            quietMillis: 150,
            //The url of the json service
            url: base_url + "index.php/settings/menu/parent_dropdown",
            dataType: 'jsonp',
            //Our search term and what page we are on
            data: function (term, page) {
                return {
                    pageSize: 50,
                    pageNum: page,
                    searchTerm: term
                };
            },
            results: function (data, page) {
                //Used to determine whether or not there are more results available,
                //and if requests for more data should be sent in the infinite scrolling
                var more = (page * pageSize) < data.Total;
                return { data: data };
            }
        }
    });
});
$(document).ready(function () {
    //global variables
    menus = []; //for menu
    var pathArray = window.location.pathname.split('php/');
    var ctrl_page = pathArray[1].split('/');
    //load request based on url
    switch (ctrl_page[1]) {
        case 'profession':
            set_profession_table();
            break;
        case 'access':
            set_permission_view();
            break;
        case 'bank':
            set_bank_table();
            break;
        case 'notification':
            set_notification_table();
            break;
        case 'gift':
            get_all_gifts();
            $('#gift_list1').empty();
            $('#gift_list2').empty();
            $('#gift_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
            $('#gift_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
            set_gift_table();
            $('#gift-dt-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#gift_list1').text(start.format('YYYY-MM-DD'));
                    $('#gift_list2').text(end.format('YYYY-MM-DD'));
                    set_gift_table(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
                }
            );
            break;
            break;
        case 'dept':
            set_dept_table();
            break;
        case 'offers':
            set_offers_table();
            break;
        case 'new_arrivals':
            set_new_arrivals_table();
            $("#new_arrival_submit").on("click", function (e) {
                // e.preventDefault();
                //  if($("#new_arrival_type").is(':checked') || $("#gift_artical").is(':checked'))
                if (($.trim($("#new_arrival_type").val()) != '' || $.trim($("#gift_artical").val()) != '') && $.trim($("#name").val()) == '' || $.trim($("#product_code").val()) == '' || $.trim($("#price").val()) == '' || $.trim($("#product_description").val()) == '' || $.trim($("#new_arrivals_content").val()) == '' || $.trim($("#expiry_date").val()) == '' || $.trim($("#new_arrivals_img").val()) == '') {
                    //$('#new_arrival_form').submit();
                    alert('Please Fill The Required Fields..');
                }
                else {
                    // alert('Please Fill The Required Fields..');
                    // return  false;
                    $('#new_arrival_form').submit();
                }
            });
            break;
        case 'design':
            set_design_table();
            break;
        case 'drawee':
            if (ctrl_page[2] == 'add' || ctrl_page[2] == 'edit') {
                load_bank();
            }
            if (ctrl_page[2] == 'list') {
                set_drawee_table();
            }
            break;
        case 'general':
            if (ctrl_page[2] == 'edit') {
                $('.sel_block').prop('checked', false);
                $('.sel_block').prop('disabled', true);
                $("input[type='radio'][name='clear_data']").change(function () {
                    var selected = (this.value == 1 ? true : false);
                    $('.sel_block').prop('disabled', selected);
                    if (selected == true) {
                        $('.sel_block').prop('checked', false);
                    }
                });
                /*-- coded by ARVK --*/
                //Limit settings
                if ($('#limit_cust').is(':checked')) {
                    $('#cust_max_count').prop('disabled', false);
                } else {
                    $('#cust_max_count').prop('disabled', true);
                }
                if ($('#limit_sch').is(':checked')) {
                    $('#sch_max_count').prop('disabled', false);
                } else {
                    $('#sch_max_count').prop('disabled', true);
                }
                if ($('#limit_branch').is(':checked')) {
                    $('#branch_max_count').prop('disabled', false);
                } else {
                    $('#branch_max_count').prop('disabled', true);
                }
                if ($('#limit_sch_acc').is(':checked')) {
                    $('#sch_acc_max_count').prop('disabled', false);
                } else {
                    $('#sch_acc_max_count').prop('disabled', true);
                }
                $('#limit_cust').on("click", function (e) {
                    if ($('#limit_cust').is(':checked')) {
                        $('#cust_max_count').prop('disabled', false);
                    } else {
                        $('#cust_max_count').prop('disabled', true);
                        $('#cust_max_count').val(0);
                    }
                });
                $('#limit_sch').on("click", function (e) {
                    if ($('#limit_sch').is(':checked')) {
                        $('#sch_max_count').prop('disabled', false);
                    } else {
                        $('#sch_max_count').prop('disabled', true);
                        $('#sch_max_count').val(0);
                    }
                });
                $('#limit_branch').on("click", function (e) {
                    if ($('#limit_branch').is(':checked')) {
                        $('#branch_max_count').prop('disabled', false);
                    } else {
                        $('#branch_max_count').prop('disabled', true);
                        $('#branch_max_count').val(0);
                    }
                });
                $('#limit_sch_acc').on("click", function (e) {
                    if ($('#limit_sch_acc').is(':checked')) {
                        $('#sch_acc_max_count').prop('disabled', false);
                    } else {
                        $('#sch_acc_max_count').prop('disabled', true);
                        $('#sch_acc_max_count').val(0);
                    }
                });
                /*-- / coded by ARVK--*/
                //Clearing database
                $('#clr_proceed').click(function () {
                    var mode = $("#tab_5 input[type='radio'][name='clear_data']:checked").val();
                    if (mode == 1) {
                        $('#confirm-truncate').modal('show');
                    }
                    else {
                        if ($("input[type='checkbox'][name='clr_db']:checked").length > 0) {
                            $('#confirm-truncate').modal('show');
                        }
                        else {
                            $('#clr_alert').addClass('alert-danger');
                            $('#clr_alert span').html('Select atleast one option.');
                            $('#clr_alert').css('display', 'block');
                        }
                    }
                });
                $(document).on("click", "#confirm-truncate #confirm_clear", function (event) {
                    $('.modal.in').modal().hide();
                    $('.modal-backdrop').remove();
                    my_Date = new Date();
                    var mode = $("#tab_5 input[type='radio'][name='clear_data']:checked").val();
                    var selected = [];
                    $('#tab_5 input[name="clr_db"]:checked').each(function () {
                        selected.push($(this).val());
                    });
                    $.ajax({
                        type: 'POST',
                        data: { 'mode': mode, 'selected': selected },
                        url: base_url + 'index.php/settings/clear/database?nocache=' + my_Date.getUTCSeconds(),
                        cache: false,
                        success: function (data) {
                            $('#clr_alert').addClass('alert-success');
                            $('#clr_alert span').html(data);
                            $('#clr_alert').css('display', 'block');
                        }
                    });
                });
                //backup database
                $('#db_backup').click(function () {
                    load_db_list();
                });
            }
            break;
        case 'rate':
            if (ctrl_page[2] == 'list') {
                load_metalrate_list();
            }
            else {
                $("input[type='text']").focus(function () { $(this).select(); });
            }
            break;
        case 'ledger':
            $('.select2').select2({
                placeholder: "Select Options",
                allowClear: true,
                width: '100%'
            });
            break;
        case 'menu':
            set_menu_table();
            load_parent_menu();
            break;
        case 'payment_charges':
            set_charges_table();
            break;
        case 'paymode':
            set_paymode_table();
            break;
        case 'profile':
            set_profile_table();
            break;
        case 'weight':
            set_weight_table();
            break;
        case 'cardbrand':
            set_cardbrand_table();
            break;
        case 'classification':
            set_classification_table();
            break;
        case 'import':
            if (ctrl_page[2] == 'list') {
                get_import_list();
            }
            else if (ctrl_page[2] == 'customer_list') {
                get_customer_list();
            }
            break;
        //Added by Durga 12.05.2023 starts here
        case 'version':
            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 6, 1);
            var from_date = firstDay.getFullYear() + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();
            var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
            //$('#version_list1').empty();
            //$('#version_list2').empty();
            set_version_table(from_date, to_date);
            $('#version_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
            $('#version_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
            $('#version-dt-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    set_version_table(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))
                    $('#rpt_payments1').text(start.format('YYYY-MM-DD'));
                    $('#rpt_payments2').text(end.format('YYYY-MM-DD'));
                }
            );
            break;
        //Added by Durga 12.05.2023 ends here
    }
    excel_format(0);
    customer_excel_format(0);
    $('#import_type').bootstrapSwitch();
    $('#send_sms').bootstrapSwitch();
    if ($('#import_type').length > 0) {
        $('#import_type').prop('checked', false);
        $('#import_type').on('switchChange.bootstrapSwitch', function (event, state) {
            excel_format(state);
        });
    }
    //$(".btn-edit").on('click',function(e){
    $(document).on('click', "#cardbrand_list a.btn-edit", function (e) {
        $("#edcard_brand").val('');
        $("#edshort_code").val('');
        e.preventDefault();
        id = $(this).data('id');
        get_cardbrand(id);
        $("#edit-id").val(id);
    });
    $(document).on('click', "#weight_list a.btn-edit", function (e) {
        $("#ed_weight").val('');
        e.preventDefault();
        id = $(this).data('id');
        get_weight(id);
        $("#edit-id").val(id);
    });
    $(document).on('click', "#sch_clsfy_list a.btn-edit", function (e) {
        $("#ed_clsfy").val('');
        e.preventDefault();
        id = $(this).data('id');
        get_classification(id);
        $("#edit-id").val(id);
    });
    $(document).on('click', "#dept_list  a.btn-edit", function (e) {
        $("#ed_dept").val('');
        e.preventDefault();
        id = $(this).data('id');
        get_dept(id);
        $("#edit-id").val(id);
    });
    $(document).on('click', "#design_list  a.btn-edit", function (e) {
        $("#ed_design").val('');
        e.preventDefault();
        id = $(this).data('id');
        get_design(id);
        $("#edit-id").val(id);
    });
    //update weight	
    $("#update_weight").on('click', function () {
        var weight = formatMetalWeight($("#ed_weight").val());
        var id = $("#edit-id").val();
        update_weight(weight, id);
    });
    //update classification	
    $("#update_clsfy").on('click', function () {
        var clsfy = {
            classification_name: $("#ed_clsfy").val(),
            description: CKEDITOR.instances.description1.getData()
        };
        var id = $("#edit-id").val();
        update_classification(clsfy, id);
    });
    //update dept
    $("#update_dept").on('click', function () {
		var dept_name = $('#ed_dept').val().trim();
        var id = $("#edit-id").val();
        if (dept_name != '') {
            update_dept(dept_name, id);
            $('#ed_dept').val('');
        }
        else {
			$('#ed_dept').val('');
            $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Department Name Should not be Empty ..." });
            $('#ed_dept').attr('placeholder', 'Enter a valid Department Name');
            $("#ed_dept").focus();
            return false;
        }
    });
    //update design
    $("#update_design").on('click', function () {
		var design = $("#ed_design").val().trim();
        var id = $("#edit-id").val();
        if (design != '') {
            update_design(design, id);
            $('#ed_design').val('');
        }
        else {
			$('#ed_design').val('');
            $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Designation Name Should not be Empty ..." });
            $('#ed_design').attr('placeholder', 'Enter a valid Designation Name');
            $("#ed_design").focus();
            return false;
        }
    });
    $("#parent").on("autocompleteselect", function (event, ui) {
        var flag = false;
        $.each(menus, function (key, value) {
            if ((value.label).toLowerCase() == (ui.item.label).toLowerCase()) {
                flag = true;
                $("#id_parent").val(value.id_menu);
                return false;
            }
        });
        if (flag == false) {
            $("#parent").val("");
            $("#id_parent").val("");
        }
    });
    __set_export_view()
    __set_import_list()
});
//set import list
function __set_import_list() {
    $('#select_all').click(function (event) {
        $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
        event.stopPropagation();
    });
    /*  $("tbody tr td input[type='checkbox']").click(function(event) {
          $("#select_all").prop('checked', $(this).prop('checked'));
        event.stopPropagation();
      }); */
}
//set export form
function __set_export_view() {
    var export_data = null;
    $("input[name=pay_status][value=0]").prop('checked', true);
    $("#is_to_date").prop("checked", false);
    $("#to_date").prop("disabled", true);
    //required to date for export
    if ($('#is_to_date').length > 0) {
        $('#is_to_date').change(function () {
            $("#to_date").prop("disabled", !$(this).is(':checked'));
        });
    }
    $("#export").on('click', function () {
        my_Date = new Date();
        var status = $("input:radio[name='pay_status']:checked").val();
        var from_date = $("#from_date").val();
        var to_date = null;
        if ($("#is_to_date").is(':checked')) {
            to_date = $("#to_date").val();
        }
        $.ajax({
            type: 'POST',
            cache: false,
            data: { 'status': status, 'from': from_date, 'to': to_date },
            url: base_url + 'index.php/settings/export_to_excel?nocache=' + my_Date.getUTCSeconds(),
            cache: false,
            success: function (path) {
                window.open(base_url + path, '_blank');
            }
        });
    });
    $("#get_account").on('click', function () {
        my_Date = new Date();
        var status = $("input:radio[name='filter_by']:checked").val();
        var from_date = $("#from_date").val();
        var to_date = null;
        if ($("#is_to_date").is(':checked')) {
            to_date = $("#to_date").val();
        }
        alert(from_date + " to " + to_date);
        $.ajax({
            type: 'POST',
            data: { 'status': status, 'from': from_date, 'to': to_date },
            url: base_url + 'index.php/settings/export/get_account/?nocache=' + my_Date.getUTCSeconds(),
            dataType: 'json',
            cache: false,
            success: function (data) {
                export_data = data;
                var trHTML = '';
                $.each(data, function (i, item) {
                    trHTML += '<tr><td>' + item.id_payment
                        + '</td><td>' + (item.name != null ? item.name : "-")
                        + '</td><td>' + (item.ref_no != null ? item.ref_no : "-")
                        + '</td><td>' + (item.code != null ? item.code : "-")
                        + '</td><td>' + (item.mobile != null ? item.mobile : "-")
                        + '</td><td>' + (item.date_payment != null ? item.date_payment : "-")
                        + '</td><td>' + (item.payment_amount != null ? item.payment_amount : "-")
                        + '</td><td>' + (item.payment_mode != null ? item.payment_mode : "-")
                        + '</td><td>' + (item.bank_acc_no != null ? item.bank_acc_no : "-")
                        + '</td><td>' + (item.bank_name != null ? item.bank_name : "-")
                        + '</td><td>' + (item.bank_branch != null ? item.bank_branch : "-")
                        + '</td><td>' + (item.bank_IFSC != null ? item.bank_IFSC : "-")
                        + '</td><td>' + (item.bank_charges != null ? item.bank_charges : "-")
                        + '</td><td>' + (item.trans_id != null ? item.trans_id : "-")
                        + '</td><td>' + (item.payment_status != null ? item.payment_status : "-")
                        + '</td></tr>';
                });
                $('#account_content').html(trHTML);
            }
        });
    });
    $("#get_record").on('click', function () {
        my_Date = new Date();
        var status = $("input:radio[name='pay_status']:checked").val();
        var from_date = $("#from_date").val();
        var to_date = null;
        if ($("#is_to_date").is(':checked')) {
            to_date = $("#to_date").val();
        }
        $.ajax({
            type: 'POST',
            data: { 'status': status, 'from': from_date, 'to': to_date },
            url: base_url + 'index.php/settings/export_list/?nocache=' + my_Date.getUTCSeconds(),
            dataType: 'json',
            cache: false,
            success: function (data) {
                export_data = data;
                var trHTML = '';
                $.each(data, function (i, item) {
                    trHTML += '<tr><td>' + item.id_payment
                        + '</td><td>' + (item.name != null ? item.name : "-")
                        + '</td><td>' + (item.ref_no != null ? item.ref_no : "-")
                        + '</td><td>' + (item.code != null ? item.code : "-")
                        + '</td><td>' + (item.mobile != null ? item.mobile : "-")
                        + '</td><td>' + (item.date_payment != null ? item.date_payment : "-")
                        + '</td><td>' + (item.payment_amount != null ? item.payment_amount : "-")
                        + '</td><td>' + (item.payment_mode != null ? item.payment_mode : "-")
                        + '</td><td>' + (item.bank_acc_no != null ? item.bank_acc_no : "-")
                        + '</td><td>' + (item.bank_name != null ? item.bank_name : "-")
                        + '</td><td>' + (item.bank_branch != null ? item.bank_branch : "-")
                        + '</td><td>' + (item.bank_IFSC != null ? item.bank_IFSC : "-")
                        + '</td><td>' + (item.bank_charges != null ? item.bank_charges : "-")
                        + '</td><td>' + (item.trans_id != null ? item.trans_id : "-")
                        + '</td><td>' + (item.payment_status != null ? item.payment_status : "-")
                        + '</td></tr>';
                });
                $('#payment_content').html(trHTML);
            }
        });
    });
}
/*-- Coded by ARVK --*/
//to load countries list in country settings
function get_countryCurr() {
    $('.overlay').css('display', 'block');
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/company/getcountry?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (country) {
            //	console.log(country);
            $.each(country, function (key, country) {
                $('#countryCurr').append(
                    $("<option></option>")
                        .attr("value", country.id)
                        .text(country.name)
                );
            });
            $("#countryCurr").select2({
                placeholder: "Enter Country",
                allowClear: true
            });
            $("#countryCurr").select2("val", ($('#countryCurrval').val() != null ? $('#countryCurrval').val() : ''));
        }
    });
    $('.overlay').css('display', 'none');
}
//to get currency code, mobile code and mob no length
$('#countryCurr').select2().on("change", function (e) {
    if (this.value != '') {
        $("#countryCurrval").val(this.value);
        my_Date = new Date();
        $.ajax({
            type: 'GET',
            url: base_url + 'index.php/settings/country/getcurrency/' + this.value + '?nocache=' + my_Date.getUTCSeconds(),
            dataType: 'json',
            cache: false,
            success: function (currency) {
                //console.log(currency);
                $("#currency_name").val(currency.currency_name);
                $("#currency_code").val(currency.currency_code);
                $("#mob_code").val(currency.mob_code);
                $("#mob_no_len").val(currency.mob_no_len);
            }
        });
    }
});
/*-- / Coded by ARVK --*/
//to get countries
function get_country() {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/company/getcountry?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (country) {
            //	console.log(country);
            $.each(country, function (key, country) {
                $('#country').append(
                    $("<option></option>")
                        .attr("value", country.id)
                        .text(country.name)
                );
            });
            $("#country").select2({
                placeholder: "Enter Country",
                allowClear: true
            });
            $("#country").select2("val", ($('#countryval').val() > 0 ? $('#countryval').val() : ''));
            var selectid = $('#countryval').val();
            if (selectid != null) {
                $('#country').val(selectid);
                get_state(selectid);
            }
        }
    });
}
//to get state details based on country selection
function get_state(id) {
    my_Date = new Date();
    $('#state option').remove();
    $.ajax({
        type: 'POST',
        data: { 'id_country': id },
        url: base_url + 'index.php/settings/company/getstate/?nocache=' + my_Date.getUTCSeconds(),
        cache: false,
        dataType: 'json',
        success: function (state) {
            $.each(state, function (key, state) {
                $('#state').append(
                    $("<option></option>")
                        .attr("value", state.id)
                        .text(state.name)
                );
            });
            $("#state").select2({
                placeholder: "Enter State",
                allowClear: true
            });
            $("#state").select2("val", ($('#stateval').val() != null ? $('#stateval').val() : ''));
            var selectid = $('#stateval').val();
            if (selectid != null) {
                $('#state').val(selectid);
                get_city(selectid);
            }
        }
    });
}
//to get city based on state selection
function get_city(id) {
    my_Date = new Date();
    $('#city option').remove();
    my_Date = new Date();
    $.ajax({
        type: 'POST',
        data: { 'id_state': id },
        url: base_url + "index.php/settings/company/getcity?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: 'json',
        success: function (city) {
            $.each(city, function (key, city) {
                $('#city').append(
                    $("<option></option>")
                        .attr("value", city.id)
                        .text(city.name)
                );
            });
            $("#city").select2({
                placeholder: "Enter City",
                allowClear: true
            });
            $("#city").select2("val", ($('#cityval').val() != null ? $('#cityval').val() : ''));
            var selectid = $('#cityval').val();
            if (selectid != null) {
                $('#city').val(selectid);
            }
        }
    });
}
//to add weight 
function add_weight(weight) {
    my_Date = new Date();
    var wt = weight;
    $.ajax({
        data: { "weight": wt },
        url: base_url + "index.php/settings/weight/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function (data) {
            $('#weight').val('');
            msg = '<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Weight added successfully.</div>';
            $('#chit_alert').html(msg);
            location.reload(true);
        }
    });
}
//to add classification 
function add_classification(clsfy) {
    var classification_name = clsfy.classification_name;
    var description = clsfy.description;
    var file = clsfy.file;
    my_Date = new Date();
    var form_data = new FormData();
    form_data.append('file', file);
    form_data.append('classification_name', classification_name);
    form_data.append('description', description);
    $.ajax({
        data: form_data,
        url: base_url + "index.php/settings/classification/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        success: function (data) {
            location.reload(true);
        }
    });
}
//to load bank
function load_bank() {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + "index.php/settings/bank/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            $.each(data.data, function (key, bank) {
                $('#bank_dropdown').append(
                    $("<option></option>")
                        .attr("value", bank.id_bank)
                        .text(bank.bank_name)
                );
            });
            $("#bank_dropdown").select2({
                placeholder: "Select Bank",
                allowClear: true
            });
            $("#bank_dropdown").select2("val", ($('#id_bank').val() != null ? $('#id_bank').val() : ''));
        }
    });
}
//bank dropdown event 
$('#bank_dropdown').select2()
    .on("change", function (e) {
        if (this.value != '') {
            $("#id_bank").val(this.value);
        }
    });
//to load drawee list
function set_drawee_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/drawee/ajax?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var drawee = data.data;
            var access = data.access;
            $('#total_drawee').text(drawee.length);
            if (access.add == '0') {
                $('#add_drawee').attr('disabled', 'disabled');
            }
            var oTable = $('#drawee_list').DataTable();
            oTable.clear().draw();
            if (drawee != null && drawee.length > 0) {
                oTable = $('#drawee_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": drawee,
                    "columnDefs":
                        [
                            {
                                targets: [0, 1, 2, 3, 4, 5],
                                className: 'dt-left'
                            },
                        ],
                    "aoColumns": [{ "mDataProp": "id_drawee" },
                    { "mDataProp": "account_no" },
                    { "mDataProp": "account_name" },
                    { "mDataProp": "bank_name" },
                    { "mDataProp": "branch" },
                    { "mDataProp": "ifsc_code" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_drawee;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/drawee/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/drawee/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                (access.edit == '1' ? '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' : '') +
                                (access.delete == '1' ? '<li><a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li>' : '');
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load payment mode list
$('#mode_name').on('keydown', function (e) {
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$("#mode_name").focus();
	}
});
$('#mode_name').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		// console.log("Valid paste — allowing.");
		$('#mode_name').val(pasteData);
		return;
	} else {
		e.preventDefault();
		// console.log("Invalid paste — blocking.");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$("#mode_name").focus();
	}
})
function set_paymode_table() {
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/paymode/ajax?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var paymode = data.data;
            var access = data.access;
            $('#total_paymode').text(paymode.length);
            if (access.add == '0') {
                $('#add_paymode').attr('disabled', 'disabled');
            }
            var oTable = $('#paymode_list').DataTable();
            oTable.clear().draw();
            if (paymode != null && paymode.length > 0) {
                oTable = $('#paymode_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": paymode,
                    "columnDefs":
                        [
                            {
                                targets: [0, 1, 2],
                                className: 'dt-left'
                            },
							// code by jothika on 10-7-2025 [removing filter from column]
							{
								targets: [3],
								orderable: false,
								searchable: false
							},
                        ],
                    "aoColumns": [{ "mDataProp": "id_mode" },
                    { "mDataProp": "mode_name" },
                    { "mDataProp": "short_code" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_mode;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/paymode/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/paymode/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li>' + (access.edit == '1' ? '<a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a>' : '') + '</li>' +
                                '<li>' + (access.delete == '1' ? '<a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>' : '') + '</li>';
                            return action_content;
                        }
                    }]
                });
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load profile list
function set_profile_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/profile/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var profile = data.profile;
            var access = data.access;
            $('#total_profiles').text(profile.length);
            if (access.add == '0') {
                $('#add_profile').attr('disabled', 'disabled');
            }
            var oTable = $('#profile_list').DataTable();
            oTable.clear().draw();
            if (profile != null && profile.length > 0) {
                oTable = $('#profile_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": profile,
                    "aoColumns": [{ "mDataProp": "id_profile" },
                    { "mDataProp": "profile_name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_profile;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/profile/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/profile/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
                                '<li><a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li>';
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load values to table
function set_menu_table() {
    my_Date = new Date();
    var oTable = $('#menu_list').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ajax": base_url + "index.php/settings/menu/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        "columns": [
            {
                "data": function (row, type, val, meta) {
                    return '<input type="checkbox" id="quick_link" class="link_id" name="link_id[]" value="' + row.id_menu + '" />' + row.id_menu;
                }
            },
            { "data": "label" },
            { "data": "link" },
            { "data": "parentname" },
            { "data": "submenus" },
            { "data": "sort" },
            {
                "data": function (row, type, val, meta) {
                    menu_status = row.active;
                    status_content = (menu_status == 1 ? 'Active' : 'Disabled');
                    return status_content;
                }
            },
            {
                "data": function (row, type, val, meta) {
                    id = row.id_menu;
                    action_content = "<div class='btn-group inline' ><a href='" + base_url + "index.php/settings/menu/edit/" + id + "' class='btn btn-primary btn-edit' role='button' data-toggle='modal' data-id=" + id + " ><i class='fa fa-edit' ></i> Edit</a> <a href='#' class='btn btn-danger btn-del' data-href=" + base_url + "index.php/settings/menu/delete/" + id + " data-toggle='modal' data-target='#confirm-delete' ><i class='fa fa-trash'></i> Delete</a></div>";
                    return action_content;
                }
            }]
    });
}
//to load parent menu Dropdown()
function load_parent_menu() {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + "index.php/settings/menu/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            $.each(data.data, function (key, menu) {
                $('#parent').append(
                    $("<option></option>")
                        .attr("value", menu.id_menu)
                        .text(menu.label)
                );
            });
            $("#parent").select2({
                placeholder: "Select parent menu",
                allowClear: true
            });
            $("#parent").select2("val", ($('#id_parent').val() != null ? $('#id_parent').val() : ''));
        }
    });
}
//to load values to table
function set_weight_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/weight_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var weight = data.data;
            var access = data.access;
            $('#total_weights').text(weight.length);
            if (access.add == '0') {
                $('#add_wt').attr('disabled', 'disabled');
            }
            else {
                $("#add_weight").on('click', function () {
                    if ($('#weight').length > 0) {
                        add_weight($('#weight').val());
                        $('#weight').val('');
                    }
                });
            }
            var oTable = $('#weight_list').DataTable();
            oTable.clear().draw();
            if (weight != null && weight.length > 0) {
                oTable = $('#weight_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": weight,
                    "columnDefs":
                        [
                            {
                                targets: [0, 1],
                                className: 'dt-left'
                            },
                        ],
                    "aoColumns": [{ "mDataProp": "id_weight" },
                    { "mDataProp": "weight" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_weight;
                            edit_target = (access.edit == '0' ? "" : "#confirm-edit");
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/weight/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = (access.edit == '1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i> Edit</a>' : '') + (access.delete == 1 ? '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>' : '')
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load values to classification table
function set_classification_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/classification_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var clsfy = data.data;
            var access = data.access;
            $('#total_classifications').text(clsfy.length);
            if (access.add == '0') {
                $('#add_cls').attr('disabled', 'disabled');
            }
            else {
                $("#add_clsfy").on('click', function () {
                    if ($('#clsfy').length > 0) {
                        var clsfy = {
                            classification_name: $("#clsfy").val(),
                            description: CKEDITOR.instances.description1.getData(),
                            file: $("#sch_clsfy_img")[0].files[0]
                        };
                        add_classification(clsfy);
                        $('#clsfy').val('');
                        CKEDITOR.instances.description.setData('');
                    }
                });
            }
            var oTable = $('#sch_clsfy_list').DataTable();
            oTable.clear().draw();
            if (clsfy != null && clsfy.length > 0) {
                oTable = $('#sch_clsfy_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": clsfy,
                    "columnDefs":
                        [
                            {
                                targets: [0, 1],
                                className: 'dt-left'
                            },
                        ],
                    "aoColumns": [{ "mDataProp": "id_classification" },
                    { "mDataProp": "classification_name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            if (row.logo != null) {
                                return "<img src='" + row.logo + "' style='width: 75px;height: 50px;' />";
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_classification;
                            edit_target = (access.edit == '0' ? "" : "#confirm-edit");
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/classification/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = (access.edit == '1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i> Edit</a>' : '') + (access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>' : '')
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load values to table
//Created by Durga Starts here 04.03.2023
// coded by jothika on 10-7-2025 [preventing special character entries in input ]
function verifyStr(key) {
	// const key = e.key;
	const allowedKeys = [
		'Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete', ' '
	];
    var regexp = /^[a-zA-Z ]*$/;
	if (regexp.test(key) || allowedKeys.includes(key)) {
		return true;
	}
}
$('#department').on('keydown', function (e) {
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
		// $("#department").val("");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#department').attr('placeholder', 'Enter a valid Department Name');
		$("#department").focus();
	}
});
$('#department').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		$('#department').val(pasteData);
		return;
	} else {
		e.preventDefault();
        $("#department").val("");
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
        $('#department').attr('placeholder', 'Enter a valid Department Name');
        $("#department").focus();
    }
})
$(document).on('click', '.dept_close', function () {
	$('#department').val('');
});
$('#designation').on('keydown', function (e) {
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
		// $("#designation").val("");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#designation').attr('placeholder', 'Enter a valid Designation Name');
		$("#designation").focus();
	}
});
$('#designation').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		$('#designation').val(pasteData);
		return;
	} else {
		e.preventDefault();
        $("#designation").val("");
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
        $('#designation').attr('placeholder', 'Enter a valid Designation Name');
        $("#designation").focus();
    }
})
$(document).on('click', '.desig_close', function () {
	$('#designation').val('');
});
$('#ed_design').on('keydown', function (e) {
	// var designation = this.value;
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
		// $("#ed_design").val("");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#ed_design').attr('placeholder', 'Enter a valid Designation Name');
		$("#ed_design").focus();
	}
});
$('#ed_design').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		$('#ed_design').val(pasteData);
		return;
	} else {
		e.preventDefault();
        $("#ed_design").val("");
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
        $('#ed_design').attr('placeholder', 'Enter a valid Designation Name');
        $("#ed_design").focus();
    }
})
$('#ed_dept').on('keydown', function (e) {
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
		// $("#ed_design").val("");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#ed_dept').attr('placeholder', 'Enter a valid Department Name');
		$("#ed_dept").focus();
	}
});
$('#ed_dept').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		$('#ed_dept').val(pasteData);
		return;
	} else {
		e.preventDefault();
        $("#ed_dept").val("");
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#ed_dept').attr('placeholder', 'Enter a valid Department Name');
        $("#ed_dept").focus();
    }
})
$('#profession').on('keydown', function (e) {
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
		// $("#ed_design").val("");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#profession').attr('placeholder', 'Enter a valid profession Name');
		$("#profession").focus();
	}
})
$('#profession').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		// console.log("Valid paste — allowing.");
		$('#profession').val(pasteData);
		return;
	} else {
		e.preventDefault();
		// console.log("Invalid paste — blocking.");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#profession').attr('placeholder', 'Enter a valid profession Name');
		$("#profession").focus();
	}
})
$(document).on('click', '.profes_close', function () {
	$('#profession').val('');
});
$('#ed_profession').on('keydown', function (e) {
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
		// $("#ed_design").val("");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#ed_profession').attr('placeholder', 'Enter a valid profession Name');
		$("#ed_profession").focus();
	}
})
$('#ed_profession').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		// console.log("Valid paste — allowing.");
		$('#ed_profession').val(pasteData);
		return;
	} else {
		e.preventDefault();
		// console.log("Invalid paste — blocking.");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$('#ed_profession').attr('placeholder', 'Enter a valid profession Name');
		$("#ed_profession").focus();
	}
})
$("#add_dept").on('click', function () {
	// coded by jothika on 9-7-2025 adding spaceonly as input issue
	var dept_name = $('#department').val().trim();
	if (dept_name !== '') {
        add_dept($('#department').val());
        $('#department').val('');
    }
    else {
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Department Name Should not be Empty ..." });
        $('#department').attr('placeholder', 'Enter a valid Designation Name');
		$("#department").val("");
        $("#department").focus();
        return false;
    }
});
// coded by jothika on 10-7-2025 adding spaceonly as input issue
$("#add_design").on('click', function () {
	var design_name = $('#designation').val().trim();
    if (design_name != '') {
        add_design(design_name);
        $('#designation').val('');
    }
    else {
		$('#designation').val('');
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Designation Name Should not be Empty ..." });
		$('#designation').attr('placeholder', 'Enter a valid Designation Name');
        $("#designation").focus();
        return false;
    }
});
//Created by Durga Ends here 04.03.2023
//to load values to table
function set_dept_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/dept_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var dept = data.data;
            var access = data.access;
            $('#total_depts').text(dept.length);
            if (access.add == '0') {
                $('#add_dpt').attr('disabled', 'disabled');
            }
            /* else{
                $("#add_dept").on('click',function(){
                if($('#department').val())
                {			
                    add_dept($('#department').val());
                    $('#department').val('');
                }
                });	
             }*/
            var oTable = $('#dept_list').DataTable();
            oTable.clear().draw();
            if (dept != null && dept.length > 0) {
                oTable = $('#dept_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
					// code by jothika on 10-7-2025 [removing filter from column]
					"columnDefs": [
						{
							targets: [2],
							orderable: false,
							searchable: false
						}
					],
                    "bSort": true,
                    "aaData": dept,
                    "aoColumns": [{ "mDataProp": "id_dept" },
                    { "mDataProp": "name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_dept;
                            edit_target = (access.edit == '0' ? "" : "#confirm-edit");
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/dept/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = (access.edit == '1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i> Edit</a>' : "") + (access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>' : "")
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load values to table
function set_offers_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/offers_list?nocache=" + my_Date,
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var offersBanners = data.data.offersBanners;
            var popup = data.data.popup;
            var access = data.access;
            $('#total_offers').text(offersBanners.length);
            $('#total_popup').text(popup.length);
            if (access.add == '0') {
                $('#popup_list').attr('disabled', 'disabled');
            }
            // Popup
            var oTable1 = $('#popup_list').DataTable();
            oTable1.clear().draw();
            if (popup != null && popup.length > 0) {
                oTable1 = $('#popup_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": popup,
                    "aoColumns": [{ "mDataProp": "id_offer" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            action_content = '<img src="' + row.offer_img_path + '" width="80px" heigth="100px">'
                            return action_content;
                        }
                    },
                    { "mDataProp": "name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            var id = row.id_offer;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/offers/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/offers/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                            return action_content;
                        }
                    }]
                });
            }
            // Offer / Banner
            var oTable = $('#offer_list').DataTable();
            oTable.clear().draw();
            if (offersBanners != null && offersBanners.length > 0) {
                oTable = $('#offer_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": offersBanners,
                    "aoColumns": [{ "mDataProp": "id_offer" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            return (row.type == 0 ? 'Offer' : (row.type == 5 ? 'Footer' : (row.type == 2 ? 'Popup' : (row.type == 3 ? 'Category' : (row.type == 4 ? 'Brochure' : (row.type == 1 ? 'Banner' : ''))))));
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            action_content = '<img src="' + row.offer_img_path + '" width="80px" heigth="100px">'
                            return action_content;
                        }
                    },
                    { "mDataProp": "name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            var id = row.id_offer;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/offers/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/offers/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load values to table
function set_new_arrivals_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/new_arrivals_list?nocache=" + my_Date,
        dataType: "JSON",
        type: "POST",
        cache: false,
        success: function (data) {
            var arrivals = data.data;
            var access = data.access;
            $('#total_new_arrivals').text(arrivals.length);
            if (access.add == '0') {
                $('#add_offer').attr('disabled', 'disabled');
            }
            var oTable = $('#new_arrivals_list').DataTable();
            oTable.clear().draw();
            if (arrivals != null && arrivals.length > 0) {
                oTable = $('#new_arrivals_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": arrivals,
                    "aoColumns": [{ "mDataProp": "id_new_arrivals" },
                    { "mDataProp": "product_code" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            action_content = '<img src="' + row.new_arrivals_img_path + '" width="80px" heigth="100px">'
                            return action_content;
                        }
                    },
                    { "mDataProp": "name" },
                    { "mDataProp": "price" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            var id = row.id_new_arrivals;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/new_arrivals/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/new_arrivals/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//to load values to table
// coded by jothika on 10-7-2025 [preventing special character entries in input ]
function verifyNum(keys) {
	const allowedKeys = [
		'Backspace', 'ArrowLeft', 'ArrowRight', 'Delete'
	];
	var regexp = /^[0-9]*$/;
	if (regexp.test(keys) || allowedKeys.includes(keys)) {
		return true;
	}
}
$('#bank_name').on('keydown', function (e) {
	const key = e.key;
	if (!verifyStr(key)) {
		e.preventDefault();
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		// $('#bank_name').attr('placeholder', 'Enter a valid Bank Name');
        $("#bank_name").focus();
    }
});
$('#bank_name').on('paste', function (e) {
	const regexp = /^[a-zA-Z ]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		// console.log("Valid paste — allowing.");
		$('#bank_name').val(pasteData);
		return;
	} else {
		e.preventDefault();
		// console.log("Invalid paste — blocking.");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$("#bank_name").focus();
	}
})
$('#short_code').on('keydown', function (e) {
	const key = e.key;
	const allowedKeys = ['Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Control', 'Shift', 'Alt', 'Meta', 'CapsLock', 'Escape'];
	if (allowedKeys.includes(key)) return true;
	if (/^[a-zA-Z_]$/.test(key)) return true;
	// Block all other keys including space and show toast
	e.preventDefault();
	$.toaster({ priority: 'error', title: 'Warning!', message: 'Special Characters and Spaces not allowed, Only Alphabets allowed ...' });
	return false;
});
$('#short_code').on('paste', function (e) {
	const regexp = /^[a-zA-Z_]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		// console.log("Valid paste — allowing.");
		$('#short_code').val(pasteData);
		return;
	} else {
		e.preventDefault();
		// console.log("Invalid paste — blocking.");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Space and Alphabets allowed ..." });
		$("#short_code").focus();
	}
})
$('#acc_number').on('keypress', function (e) {
	const key = e.key;
	if (!verifyNum(key)) {
		e.preventDefault();
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Spaces and Special Characters not allowed ,Only Numbers are allowed ..." });
		// $('#bank_name').attr('placeholder', 'Enter a valid Bank Name');
		$("#acc_number").focus();
	}
});
$('#acc_number').on('paste', function (e) {
	const regexp = /^[0-9]*$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		// console.log("Valid paste — allowing.");
		$('#acc_number').val(pasteData);
		return;
	} else {
		e.preventDefault();
		// console.log("Invalid paste — blocking.");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Spaces and Special Characters not allowed ,Only Numbers are allowed ..." });
		$("#acc_number").focus();
	}
})
$('#ifsc_code').on('keypress', function (e) {
	const key = e.key;
	const allowedKeys = [
		'Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab', ' '
	];
	var regexp = /^[a-zA-Z0-9 ]$/;
	if (regexp.test(key) || allowedKeys.includes(key)) {
		return true;
	}
	else {
		e.preventDefault();
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Numbers and Alphabets allowed ..." });
		$("#ifsc_code").focus();
	}
});
$('#ifsc_code').on('paste', function (e) {
	const regexp = /^[a-zA-Z0-9 ]$/;
	const pasteData = (e.originalEvent || e).clipboardData.getData('text');
	if (regexp.test(pasteData)) {
		// console.log("Valid paste — allowing.");
		$('#ifsc_code').val(pasteData);
		return;
	} else {
		e.preventDefault();
		// console.log("Invalid paste — blocking.");
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Special Characters not allowed ,Only Numbers and Alphabets allowed ..." });
		$("#ifsc_code").focus();
	}
})
function set_bank_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/bank/ajax?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var bank = data.data;
            var access = data.access;
            $('#total_banks').text(bank.length);
            if (access.add == '0') {
                $('#add_bnk').attr('disabled', 'disabled');
            }
            var oTable = $('#bank_list').DataTable();
            oTable.clear().draw();
            if (bank != null && bank.length > 0) {
                oTable = $('#bank_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": bank,
                    "columnDefs":
                        [
                            {
                                targets: [0, 1, 2],
                                className: 'dt-left'
                            },
							// code by jothika on 10-7-2025 [removing filter from column]
							{
								targets: [3],
								orderable: false,
								searchable: false
							}
                        ],
                    "aoColumns": [{ "mDataProp": "id_bank" },
                    { "mDataProp": "bank_name" },
                    { "mDataProp": "short_code" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_bank;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/bank/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/bank/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li>' + (access.edit == '1' ? '<a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a>' : '') + '</li>' +
                                '<li>' + (access.delete == '1' ? '<a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>' : '') + '</li>';
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function set_gift_table() {
    my_Date = new Date();
    var from_date = $("#gift_list1").text();
    var to_date = $("#gift_list2").text();
    $("#gift_date_range").text(from_date + " to " + to_date);
    var branch = $("#branch_select").val();
    var gift_name = $("#gift_name_select").val();
    pagetitle = get_title(from_date, to_date, "Gift List");
    $.ajax({
        url: base_url + "index.php/settings/gift/ajax?nocache=" + my_Date.getUTCSeconds(),
        data: { "from_date": from_date, "to_date": to_date, "id_branch": branch, "gift_id": gift_name },
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var gift = data.data;
            var access = data.access;
            $('#total_banks').text(gift.length);
            if (access.add == '0') {
                $('#add_bnk').attr('disabled', 'disabled');
            }
            var oTable = $('#gift_list').DataTable();
            oTable.clear().draw();
            if (gift != null && gift.length > 0) {
                oTable = $('#gift_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "dom": 'lBfrtip',
                    "buttons": [
                        {
                            extend: 'print',
                            footer: true,
                            title: "",
                            messageTop: pagetitle,
                            exportOptions: {
                                columns: ':lt(6)' // Select only the first 6 columns
                            },
                            customize: function (win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                        {
                            extend: 'excel',
                            footer: true,
                            exportOptions:
                            {
                                columns: ':lt(6)' // Select only the first 6 columns
                            },
                            title: "Gift List " + from_date + "  to  " + to_date,
                        }
                    ],
                    "columnDefs":
                        [
                            {
                                targets: [0, 1, 2],
                                className: 'dt-left'
                            },
                            {
                                targets: [3, 4, 5],
                                className: 'dt-right'
                            },
                        ],
                    "aaData": gift,
                    "aoColumns": [{ "mDataProp": "id_gift" },
                    { "mDataProp": "gift_name" },
                    { "mDataProp": "name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            if (row.quantity == row.out_stock) {
                                var quantity = '<p><span class="badge bg-red">' + row.quantity + '</span></p>';
                                return quantity;
                            }
                            else {
                                return row.quantity;
                            }
                        }
                    },
                    { "mDataProp": "out_stock" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            return parseInt(row.quantity) - parseInt(row.out_stock);
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            active_url = base_url + "index.php/admin_settings/update_gift_status/" + (row.status == 1 ? 0 : 1) + "/" + row.id_gift;
                            return "<a href='" + active_url + "'><i class='fa " + (row.status == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.status == 1 ? 'green' : 'red') + "'></i></a>"
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_gift;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/gift/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/gift/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                (access.edit == '1' ? '<li><a href="#" class="btn-edit" data-href=' + edit_url + ' data-toggle="modal" data-target="#gift_form" onclick="set_gift_form(' + id + ')"><i class="fa fa-edit" ></i> Edit</a></li>' : '') +
                                (access.delete == '1' ? '<li><a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li>' : '');
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
$("#new_gift_add").on('click', function () {
    $('#active').bootstrapSwitch('state', true);
});
function get_all_gifts() {
    $('#gift_name_select').empty();
    $.ajax({
        url: base_url + "index.php/admin_settings/get_all_gifts",
        dataType: "JSON",
        type: "POST",
        data: { 'id_branch': $('#branch_select').val() },
        success: function (data) {
            var gift_selected = $("#gift_name_select").val();
            $("#gift_name_select").select2
                ({
                    placeholder: "Select Gift",
                    allowClear: true
                });
            $.each(data, function (key, item) {
                $('#gift_name_select').append
                    (
                        $("<option></option>")
                            .attr("value", item.id_gift)
                            .text(item.gift_name)
                    );
            });
            $("#gift_name_select").select2("val", (gift_selected != '' && gift_selected > 0 ? gift_selected : ''));
            $(".overlay").css("display", "none");
        }
    });
}
$('#gift_name_select').select2().on("change", function (e) {
    if (this.value != '' && this.value != null) {
        set_gift_table();
    }
});
function set_gift_form(id = "") {
    var branch_setting = $("#gift_branch_settings").val();
    var emp_branch = $("#gift_id_branch").val();
    if (id == '') {
        //$("#gift_form").val("");
        $('#gift_form_title').text('ADD GIFT');
    }
    else {
        $('#gift_form_title').text('EDIT GIFT');
        $.ajax({
            url: base_url + "index.php/admin_settings/get_gift_name_byId?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            data: { "id": id },
            success: function (data) {
                console.log(data);
                $('#id_gift_modal').val(id);
                $('#gift_name_modal').val(data.gift_name);
                $('#form_type').val('EDIT');
                $("input[name='gift[gift_type]'][value=" + data.gift_type + "]").prop("checked", "checked");
                // $("#metal_select option[value="+data.metal+"]").attr('selected','selected');
                $('#metal_select').select2('val', data.metal);
                $('#gift_weight').val(data.net_weight);
                $('#gift_qty').val(data.quantity);
                if (branch_setting == 1 && emp_branch == '') {
                    $('#branch_form_select').select2('val', data.id_branch);
                }
                if (data.status == 1) {
                    $('#active').bootstrapSwitch('state', true);
                }
                else {
                    $('#active').bootstrapSwitch('state', false);
                }
            }
        });
    }
}
$("#gift_cancel").on('click', function () {
    window.location.reload(true);
}
);
$("#gift_modal_submit").on('click', function () {
    var id = $('#id_gift_modal').val();
    var name = $('#gift_name_modal').val();
    var form_type = $('#form_type').val();
    //16-12-2022,AB
    var type = $("input[name='gift[gift_type]']:checked").val()
    var metal = $('#metal_select').val();
    var weight = $('#gift_weight').val();
    var gift_qty = $('#gift_qty').val();
    var gift_status = $("#active").bootstrapSwitch('state');
    if (gift_status) {
        gift_status = 1;
    }
    else {
        gift_status = 0;
    }
    var branch_setting = $("#gift_branch_settings").val();
    if (branch_setting == 0) {
        var gift_branch = $("#gift_id_branch").val();
    }
    else if (branch_setting == 1 && $("#gift_id_branch").val() != '') {
        var gift_branch = $("#gift_id_branch").val();
    }
    else {
        var gift_branch = $('#branch_form_select').val();
    }
    if (name != '' && type != '' && type != undefined && gift_qty != '' && gift_qty > 0) {
        //var postdata = {"id":id,"gift_name":name,"gift_type":type,"gift_metal":metal,"gift_weight":weight,"gift_qty":gift_qty}
        if (branch_setting == 1 && gift_branch == null) {
            $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Fill the Branch Details..." });
        }
        else {
            var postdata = { "id": id, "gift_name": name, "gift_type": type, "gift_metal": metal, "gift_weight": weight, "gift_qty": gift_qty, "id_branch": gift_branch, "status": gift_status }
            if (name != '') {
                if (form_type == 'ADD') {
                    $.ajax({
                        url: base_url + "index.php/admin_settings/add_gift",
                        dataType: "JSON",
                        type: "POST",
                        data: postdata,
                        success: function (data) {
                            $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>Gift added successfully..." });
                            window.location.href = base_url + "index.php/settings/gift/list";
                        }
                    });
                } else {
                    $.ajax({
                        url: base_url + "index.php/admin_settings/update_gift",
                        dataType: "JSON",
                        type: "POST",
                        data: postdata,
                        success: function (data) {
                            $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>Gift updated successfully..." });
                            window.location.href = base_url + "index.php/settings/gift/list";
                        }
                    });
                }
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Fill the gift name..." });
            }
        }
    }
    else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Fill All the Required Fields..." });
    }
});
//to load design values to table
function set_design_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/design_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var design = data.data;
            var access = data.access;
            $('#total_designations').text(design.length);
            if (access.add == '0') {
                $('#add_designation').attr('disabled', 'disabled');
            }
            /*else{
               $("#add_design").on('click',function(){
               if($('#designation').val())
               {			
                   add_design($('#designation').val());
                   $('#designation').val('');
               }
           });
            }*/
            var oTable = $('#design_list').DataTable();
            oTable.clear().draw();
            if (design != null && design.length > 0) {
                oTable = $('#design_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
					// code by jothika on 10-7-2025 [removing filter from column]
					"columnDefs": [
						{
							targets: [2],
							orderable: false,
							searchable: false
						}],
                    "bSort": true,
                    "aaData": design,
                    "aoColumns": [{ "mDataProp": "id_design" },
                    { "mDataProp": "name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_design;
                            edit_target = (access.edit == '0' ? "" : "#confirm-edit");
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/design/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = (access.edit == '1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i> Edit</a>' : "") + (access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>' : "")
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//update weight
function update_weight(weight, id) {
    my_Date = new Date();
    $.ajax({
        data: { "weight": weight },
        url: base_url + "index.php/settings/weight/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function () {
            console.log(get_weight(id));
            window.location.reload(true);
            //set_table();
        }
    });
}
//get weight by id
function get_weight(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/settings/weight/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            wt = data.weight;
            $('#ed_weight').val(wt);
            // console.log(wt);
        }
    });
}
//update Classification
function update_classification(clsfy, id) {
    my_Date = new Date();
    var classification_name = clsfy.classification_name;
    var description = clsfy.description;
    var file = clsfy.file;
    my_Date = new Date();
    var form_data = new FormData();
    form_data.append('file', file);
    form_data.append('classification_name', classification_name);
    form_data.append('description', description);
    $.ajax({
        data: form_data,
        url: base_url + "index.php/settings/classification/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        success: function () {
            //	  window.location.reload(true); Old 05-12-2022
            window.location.reload(true); //New 05-12-2022
        }
    });
}
//get weight by id
function get_classification(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/settings/classification/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            classification_name = data.classification_name;
            description = data.description;
            logo = data.logo;
            $('#ed_clsfy').val(classification_name);
            if (logo != "" && logo != null) {
                var img = base_url + "assets/img/sch_classify/" + logo;
                $("#edit_sch_clsfy_img_preview").attr('src', img);
            }
            else {
                var img = base_url + "assets/img/no_image.png";
                $("#edit_sch_clsfy_img_preview").attr('src', img);
            }
            CKEDITOR.instances.description1.setData(description);
        }
    });
}
//get dept by id
function get_dept(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/settings/dept/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            dept = data.name;
            $('#ed_dept').val(dept);
            // console.log(wt);
        }
    });
}
//get design by id
function get_design(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/settings/design/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            dept = data.name;
            $('#ed_design').val(dept);
            // console.log(wt);
        }
    });
}
//to add dept 
function add_dept(dept) {
    my_Date = new Date();
    $.ajax({
        data: { "dept": dept },
        url: base_url + "index.php/settings/dept/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function (data) {
            location.reload(true);
        }
    });
}
//update dept
function update_dept(dept, id) {
    my_Date = new Date();
    $.ajax({
        data: { "dept": dept },
        url: base_url + "index.php/settings/dept/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function () {
            window.location.reload(true);
        }
    });
}
//to add design
function add_design(design) {
    my_Date = new Date();
    $.ajax({
        data: { "design": design },
        url: base_url + "index.php/settings/design/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function (data) {
            //$('#department').val('');
            window.location.reload(true);
        }
    });
}
function update_design(design, id) {
    my_Date = new Date();
    $.ajax({
        data: { "design": design },
        url: base_url + "index.php/settings/design/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function () {
            //console.log(get_weight(id));
            window.location.reload(true);
            //set_table();
        }
    });
}
function excel_format(type) {
    var xl_format;
    var account = "<table class='table table-bordered'>";
    account += "<tr><td>First Name</td><td>Last Name</td><td>Mobile</td><td>Email</td><td>Pan</td><td>Ref No</td><td>A/c Name</td><td>Scheme Code</td><td>Start Date</td><td>No of Paid Installments</td><td>Total Paid Amount</td><td>Total Paid Weight</td><td>Last Paid Weight</td><td>Last Paid Chance</td><td>Last Paid Date</td><td>Is New Customer</td></tr></table>";
    xl_format = account;
    $('#excel_format').html(xl_format);
}
function customer_excel_format(type) {
    var xl_format;
    var account = "<table class='table table-bordered'>";
    account += "<tr><td>First Name</td><td>Last Name</td><td>Mobile</td><td>Email</td><td>Pan</td><td>Address1</td><td>Address2</td><td>Address3</td><td>Pincode</td><td>City</td><td>State</td><td>Country</td><td>Nominee Name</td><td>Nominee Relationship</td><td>Nominee Mobile</td><td>Nominee Address1</td><td>Nominee Address2</td><td>Is New</td><td>Branch Id</td></tr></table>";
    xl_format = account;
    $('#customer_excel_format').html(xl_format);
}
/*******  Permission tab **********/
$('#project_status').find(".tab-access").on("change", "[name='row_access']", function (e) {
    var curRow = $(this).closest('tr');
    if ($(this).is(":checked")) {
        curRow.find('.access_view').prop('checked', true);
        curRow.find('.access_add').prop('checked', true);
        curRow.find('.access_edit').prop('checked', true);
        curRow.find('.access_delete').prop('checked', true);
    } else {
        curRow.find('.access_view').prop('checked', false);
        curRow.find('.access_add').prop('checked', false);
        curRow.find('.access_edit').prop('checked', false);
        curRow.find('.access_delete').prop('checked', false);
    }
    get_tab_values();
});
$('#project_status').find(".tab-access").on("change", ".access_view,.access_add,.access_edit,.access_delete", function (e) {
    var curRow = $(this).closest('tr');
    if ($(this).is(":checked")) {
        curRow.find('.access_row').prop('checked', true);
    } else {
        curRow.find('.access_row').prop('checked', false);
        $(".selectall_access").attr("checked", false);
    }
    get_tab_values();
});
$('#project_status').find(".tab-access").on("change", "[name='selectall_view'],[name='selectall_add'],[name='selectall_edit'],[name='selectall_delete']", function (e) {
    var curRow = $(this).closest('tr');
    if ($(this).is(":checked")) {
        //curRow.find('.access_row').prop('checked', true);
    } else {
        curRow.find('.access_row').prop('checked', false);
        $(".selectall_access").attr("checked", false);
        $('#project_status').find(".tab-content .active .access_row").prop('checked', $(this).prop('checked'));
    }
    get_tab_values();
});
//Select row all
$('#project_status').find(".tab-access").on("change", "[name='selectall_row']", function (e) {
    $('#project_status').find("tr input:checkbox").prop('checked', $(this).prop('checked'));
    get_tab_values();
});
//view all
$('#project_status').find(".tab-access").on("change", "[name='selectall_view']", function (e) {
    $('#project_status').find(".tab-content .active .access_view").prop('checked', $(this).prop('checked'));
    get_tab_values();
});
//add all
$('#project_status').find(".tab-access").on("change", "[name='selectall_add']", function (e) {
    $('#project_status').find(".tab-content .active .access_add").prop('checked', $(this).prop('checked'));
    get_tab_values();
});
//edit all
$('#project_status').find(".tab-access").on("change", "[name='selectall_edit']", function (e) {
    $('#project_status').find(".tab-content .active .access_edit").prop('checked', $(this).prop('checked'));
    get_tab_values();
});
//delete all
$('#project_status').find(".tab-access").on("change", "[name='selectall_delete']", function (e) {
    $('#project_status').find(".tab-content .active .access_delete").prop('checked', $(this).prop('checked'));
    get_tab_values();
});
//check box other than select all
$('#project_status').find(".tab-access").on("change", "[type='checkbox']:not(.selectall_access)", function () {
    var table_data = [];
    var values = {};
    var row = $(this).closest("tr");
    var tds = row.find("td input");
    $.each(tds, function () {
        if ($(this).attr('type') == 'checkbox') {
            values[$(this).attr('name')] = ($(this).is(':checked') ? $(this).val() : 0);
        }
        else {
            values[$(this).attr('name')] = $(this).val();
        }
    });
    table_data.push(values);
    // post_access(table_data);
});
$('#retail').find(".tab-access").on("change", "[name='row_access_dash']", function (e) {
    var curRow = $(this).closest('tr');
    if ($(this).is(":checked")) {
        curRow.find('.access_view').prop('checked', true);
        curRow.find('.access_add').prop('checked', true);
        curRow.find('.access_edit').prop('checked', true);
        curRow.find('.access_delete').prop('checked', true);
    } else {
        curRow.find('.access_view').prop('checked', false);
        curRow.find('.access_add').prop('checked', false);
        curRow.find('.access_edit').prop('checked', false);
        curRow.find('.access_delete').prop('checked', false);
    }
    get_dashboardtab_values();
});
$('#retail').find(".tab-access").on("change", ".access_view,.access_add,.access_edit,.access_delete", function (e) {
    var curRow = $(this).closest('tr');
    if ($(this).is(":checked")) {
        curRow.find('.access_row_dash').prop('checked', true);
    } else {
        curRow.find('.access_row_dash').prop('checked', false);
        $(".selectall_dashboardaccess").attr("checked", false);
    }
    get_dashboardtab_values();
});
$('#retail').find(".tab-access").on("change", ".access_default", function (e) {
    var curRow = $(this).closest('tr');
    if ($(this).is(":checked")) {
        $('.access_default').prop('checked', false);
        curRow.find('.access_view').prop('checked', true)
        curRow.find('.access_default').prop('checked', true);
        get_dashboardtab_values();
        //$('#retail').find(".tab-content .active .access_default").prop('checked', $(this).prop('unchecked'));
    } else {
        curRow.find('.access_default').prop('checked', true);
        //	$('#retail').find(".tab-content .active .access_default").prop('checked', $(this).prop('checked'));
        //$(".selectall_dashboardaccess").attr("checked", false);
    }
});
$('#retail').find(".tab-access").on("change", "[name='selectall_dashboardview'],[name='selectall_dashboardadd'],[name='selectall_dashboardedit'],[name='selectall_dashboarddelete']", function (e) {
    var curRow = $(this).closest('tr');
    if ($(this).is(":checked")) {
        //curRow.find('.access_row').prop('checked', true);
    } else {
        curRow.find('.access_row_dash').prop('checked', false);
        $(".selectall_dashboardaccess").attr("checked", false);
    }
    get_dashboardtab_values();
});
//Select row all
$('#retail').find(".tab-access").on("change", "[name='selectall_dashboardrow']", function (e) {
    $('#retail').find("tr input:checkbox").prop('checked', $(this).prop('checked'));
    get_dashboardtab_values();
});
//view all
$('#retail').find(".tab-access").on("change", "[name='selectall_dashboardview']", function (e) {
    $('#retail').find(".tab-content .active .access_view").prop('checked', $(this).prop('checked'));
    get_dashboardtab_values();
});
//add all
$('#retail').find(".tab-access").on("change", "[name='selectall_dashboardadd']", function (e) {
    $('#retail').find(".tab-content .active .access_add").prop('checked', $(this).prop('checked'));
    get_dashboardtab_values();
});
//edit all
$('#retail').find(".tab-access").on("change", "[name='selectall_dashboardedit']", function (e) {
    $('#retail').find(".tab-content .active .access_edit").prop('checked', $(this).prop('checked'));
    get_dashboardtab_values();
});
//delete all
$('#retail').find(".tab-access").on("change", "[name='selectall_dashboarddelete']", function (e) {
    $('#retail').find(".tab-content .active .access_delete").prop('checked', $(this).prop('checked'));
    get_dashboardtab_values();
});
$('#retail').find(".tab-access").on("change", "[type='checkbox']:not(.selectall_dashboardaccess)", function () {
    var table_data = [];
    var values = {};
    var row = $(this).closest("tr");
    var tds = row.find("td input");
    $.each(tds, function () {
        if ($(this).attr('type') == 'checkbox') {
            values[$(this).attr('name')] = ($(this).is(':checked') ? $(this).val() : 0);
        }
        else {
            values[$(this).attr('name')] = $(this).val();
        }
    });
    table_data.push(values);
    //post_dashbaordaccess(table_data);
});
//click tab event
$(".tab-access").on("click", "ul.nav.nav-pills li a", function (e) {
    var id_profile = $(this).data("id");
    generate_access_table(id_profile)
});
function set_permission_view() {
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/profile/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        type: "GET",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            //set_table();
            var profile = "";
            var content = "";
            var dashboard_profile = "";
            var dashboard_content = "";
            var id_profile = "";
            $.each(data.profile, function (i, item) {
                profile += "<li " + (i == '0' ? 'class=active' : '') + "><a href='#tab_" + item.id_profile + "' data-id='" + item.id_profile + "' data-toggle='pill'>" + item.profile_name + "</a></li>";
                content += "<div class='tab-pane " + (i == '0' ? 'active' : '') + "' id='tab_" + item.id_profile + "'><p></p></div>";
                // Dashboard Records
                dashboard_profile += "<li " + (i == '0' ? 'class=active' : '') + "><a href='#tab_dash" + item.id_profile + "' data-id='" + item.id_profile + "' data-toggle='pill'>" + item.profile_name + "</a></li>";
                dashboard_content += "<div class='tab-pane " + (i == '0' ? 'active' : '') + "' id='tab_dash" + item.id_profile + "'><p></p></div>";
                if (i == 0) {
                    id_profile = item.id_profile;
                }
            });
            $('#project_status').find('.tab-access ul').append(profile);
            $('#project_status').find('.tab-access .tab-content').append(content);
            //Dashboard Menu Records
            $('#retail').find('.tab-access ul').append(dashboard_profile);
            $('#retail').find('.tab-access .tab-content').append(dashboard_content);
            generate_access_table(id_profile);
            $("div.overlay").css("display", "none");
        }
    });
}
function generate_access_table(id_profile) {
    var access_table = "";
    var dashboard_access_table = "";
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/access/ajax_list/" + id_profile + "?nocache=" + my_Date.getUTCSeconds(),
        type: "GET",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            access_table = "<table class='table access-table' id='access_" + id_profile + "'><theader><tr><th>Menu</th>" +
                "<th><div class='checkbox'><label><input type='checkbox' class='selectall_access' name='selectall_row' value='1'> Select All</label></div></th>" +
                "<th><div class='checkbox'><label><input type='checkbox' class='selectall_access' name='selectall_view' value='1'> View</label></div></th>" +
                "<th><div class='checkbox'><label><input type='checkbox' class='selectall_access' name='selectall_add' value='1'> Add</label></div></th>" +
                "<th><div class='checkbox'><label><input type='checkbox' class='selectall_access' name='selectall_edit' value='1'> Edit</label></div></th>" +
                "<th><div class='checkbox'><label><input type='checkbox' class='selectall_access' name='selectall_delete' value='1'> Delete</label></div></th>" +
                "</tr></theader><tbody>";
            $.each(data.menu, function (i, item) {
                access_table += "<tr class=" + (item.submenus > 0 ? 'parent_menu' : '') + "><td>" + item.label + "<input type='hidden' name='id_profile' value='" + id_profile + "'  /><input type='hidden' name='id_menu' value='" + item.id_menu + "'  /></td>" +
                    "<td><input type='checkbox' class='access_row' name='row_access'  value='1' " + (item.view == '1' && item.add == '1' && item.edit == '1' && item.delete == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "<td><input type='checkbox' class='access_view' name='view'  value='1' " + (item.view == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "<td><input type='checkbox' class='access_add' name='add'   value='1' " + (item.add == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "<td><input type='checkbox' class='access_edit' name='edit' value='1' " + (item.edit == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "<td><input type='checkbox' class='access_delete' name='delete' value='1' " + (item.delete == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "</tr>";
            });
            access_table += "</tbody></table>";
            $('#project_status').find('.tab-access .tab-content #tab_' + id_profile + " ").html(access_table);
            // DashBoard Menu Access
            dashboard_access_table = "<table class='table dashboard-access-table' id='dashboard_access_" + id_profile + "'><theader><tr><th>Menu</th>" +
                // "<th><div class='checkbox'><label><input type='checkbox' class='selectall_dashboardaccess' name='selectall_dashboardrow' value='1'> Select All</label></div></th>" +
                "<th><div class='checkbox'><label><input type='checkbox' class='selectall_dashboardaccess' name='selectall_dashboardview' value='1'> View</label></div></th>" +
                "<th><div class='checkbox'> Default</label></div></th>" +
                // "<th><div class='checkbox'><label><input type='checkbox' class='selectall_dashboardaccess' name='selectall_dashboardedit' value='1'> Edit</label></div></th>" +
                // "<th><div class='checkbox'><label><input type='checkbox' class='selectall_dashboardaccess' name='selectall_dashboarddelete' value='1'> Delete</label></div></th>" +
                "</tr></theader><tbody>";
            $.each(data.dashboard_menu, function (i, item) {
                dashboard_access_table += "<tr id=''  class=" + (item.submenus > 0 ? 'parent_menu' : '') + "><td>" + item.label + "<input type='hidden' name='id_profile' value='" + id_profile + "'  /><input type='hidden' name='id_menu' value='" + item.id_menu + "'  /></td>" +
                    // "<td><input type='checkbox' class='access_row_dash' name='row_access_dash'  value='1' " + (item.view == '1' && item.add == '1' && item.edit == '1' && item.delete == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "<td><input type='checkbox' class='access_view' name='view'  value='1' " + (item.view == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "<td><input type='checkbox' class='access_default' name='is_default'   value='1' " + (item.is_default == '1' ? 'checked="checked"' : '') + "'></td>" +
                    // "<td><input type='checkbox' class='access_edit' name='edit' value='1' " + (item.edit == '1' ? 'checked="checked"' : '') + "'></td>" +
                    // "<td><input type='checkbox' class='access_delete' name='delete' value='1' " + (item.delete == '1' ? 'checked="checked"' : '') + "'></td>" +
                    "</tr>";
            });
            dashboard_access_table += "</tbody></table>";
            $('#retail').find('.tab-access .tab-content #tab_dash' + id_profile + " ").html(dashboard_access_table);
            //return access_table;
            $("div.overlay").css("display", "none");
        }
    });
}
//Get all selected values
function get_tab_values() {
    var table_data = [];
    var values = {};
    $("#project_status").find(".tab-content .active table > tbody > tr").each(function (i) {
        values = new Object;
        $('td input', this).each(function () {
            if ($(this).attr('type') == 'checkbox') {
                values[$(this).attr('name')] = ($(this).is(':checked') ? $(this).val() : 0);
            }
            else {
                values[$(this).attr('name')] = $(this).val();
            }
        });
        table_data.push(values);
    });
    //removes the first elemet
    table_data.shift();
    post_access(table_data);
}
function get_dashboardtab_values() {
    var table_data = [];
    var values = {};
    $("#retail").find(".tab-content .active table > tbody > tr").each(function (i) {
        values = new Object;
        $('td input', this).each(function () {
            if ($(this).attr('type') == 'checkbox') {
                values[$(this).attr('name')] = ($(this).is(':checked') ? $(this).val() : 0);
            }
            else {
                values[$(this).attr('name')] = $(this).val();
            }
        });
        table_data.push(values);
    });
    //removes the first elemet
    table_data.shift();
    post_dashbaordaccess(table_data);
}
//post access values
function post_access(table_data) {
    $("div.overlay").css("display", "block");
    var postData = { 'access_data': JSON.stringify(table_data) };
    var my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/access/add",
        type: "POST",
        data: postData,
        //dataType: 'json',
        success: function (result) {
            $("div.overlay").css("display", "none");
            $.toaster({ priority: result.class, title: result.class, message: '' + "</br>" + "Permission updated successfully." });
            /*$('#access-alert').delay(500).fadeIn('normal', function() {
             $(this).find("p").html(result);
             $(this).addClass("alert-success ");
               $(this).delay(1000).fadeOut();
          });*/
        },
        error: function (error) {
            console.log(error);
            $("div.overlay").css("display", "none");
            $('#access-alert').delay(500).fadeIn('normal', function () {
                $(this).find("p").html("Unable to proceed request");
                $(this).addClass("alert-danger ");
                $(this).delay(2500).fadeOut();
            });
        }
    });
}
//post access values
function post_dashbaordaccess(table_data) {
    $("div.overlay").css("display", "block");
    var postData = { 'access_data': JSON.stringify(table_data) };
    var my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/access/dashboard_add",
        type: "POST",
        data: postData,
        //dataType: 'json',
        success: function (result) {
            $("div.overlay").css("display", "none");
            $.toaster({ priority: result.class, title: result.class, message: '' + "</br>" + "Permission updated successfully." });
            /*$('#access-alert').delay(500).fadeIn('normal', function() {
             $(this).find("p").html(result);
             $(this).addClass("alert-success ");
               $(this).delay(1000).fadeOut();
          });*/
        },
        error: function (error) {
            console.log(error);
            $("div.overlay").css("display", "none");
            $('#access-alert').delay(500).fadeIn('normal', function () {
                $(this).find("p").html("Unable to proceed request");
                $(this).addClass("alert-danger ");
                $(this).delay(2500).fadeOut();
            });
        }
    });
}
/******* End Permission tab **********/
function load_metalrate_list() {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    var oTable = $('#metalrate_list').DataTable();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/rate/ajax_list?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        success: function (data) {
            var access = data.access;
            $('#total_metals').text(data.rates.length);
            if (access.add == '0') {
                $('#add_metal').attr('disabled', 'disabled');
            }
            var max_id = data.max_id;
            oTable = $('#metalrate_list').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": true,
                "aaSorting": [[0, "desc"]],
                "aaData": data.rates,
                "aoColumns": [
                    { "mDataProp": "id_metalrates" },
                    { "mDataProp": "mjdmagoldrate_22ct" },
                    { "mDataProp": "mjdmasilverrate_1gm" },
                    { "mDataProp": "market_gold_18ct" },
                     {"mDataProp": "goldrate_14ct"},
                    {"mDataProp": "goldrate_9ct"},
                    { "mDataProp": "goldrate_18ct" },
                    { "mDataProp": "market_gold_20ct" },
                    { "mDataProp": "goldrate_22ct" },
                    { "mDataProp": "goldrate_24ct" },
                    { "mDataProp": "coin_gold_22ct" },
                    { "mDataProp": "silverrate_1gm" },
                    { "mDataProp": "silverrate_1kg" },
                    { "mDataProp": "platinum_1g" },
                    { "mDataProp": "employee" },
                    { "mDataProp": "updatetime" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_metalrates;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/rate/edit/' + id : "#");
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li></ul></div>';
                            return (id == max_id ? action_content : '-');
                        }
                    }
                ]
            });
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function set_charges_table() {
    my_Date = new Date();
    var oTable = $('#charges_list').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ajax": base_url + "/index.php/settings/payment_charges/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        "columns": [
            { "data": "id_charges" },
            { "data": "payment_mode" },
            { "data": "code" },
            { "data": "service_tax" },
            {
                "data": function (row, type, val, metal) {
                    status = row.active;
                    return (status == 1 ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Disabled</span>');
                }
            },
            {
                "data": function (row, type, val, meta) {
                    id = row.id_charges;
                    action_content = '<a class="btn btn-primary" role="button" href="' + base_url + 'index.php/settings/payment_charges/edit/' + id + '"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href=' + base_url + "index.php/settings/payment_charges/delete/" + id + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                    return action_content;
                }
            }
        ]
    });
}
function add_charges_row(obj, e) {
    e.preventDefault();
    var flag = validate_row(obj);
    if (flag == true) {
        $("#charges_range tbody tr").eq($(obj).parent().parent().index()).find('td').eq(4).html("");
        var row = $('<tr><td><input type="number"  name="charges[range][lower_limit][]" step="any" value=""/></td><td><input name="charges[range][upper_limit][]" type="number" step="any" value="" /></td><td><select name="charges[range][charge_type][]" id="charge_type"><option value="0"> % </option><option value="1">Value</option></select></td><td><input type="number" step="any" name="charges[range][charges_value][]" id="charges_value" value=""/></td><td><button type="submit" class="btn btn-success btn-sm" onclick="add_charges_row(this,event)">Add</button> <button type="submit" class="btn btn-danger  btn-sm" onclick="del_charges_row(this,event)">Delete</button></td></tr>');
        $("#charges_range tbody").append(row);
    }
}
function del_charges_row(obj, e) {
    e.preventDefault();
    var answer = confirm("Are you sure want to remove this row?")
    if (answer) {
        $(obj).parent().parent().remove();
        var trIndex = parseFloat($("#charges_range tbody tr").length) - 1;
        console.log($("#charges_range tbody tr").length);
        if ($("#charges_range tbody tr").length == 1) {
            $("#charges_range tbody tr").eq(trIndex).find('td').eq(4).html('<button type="submit" class="btn btn-success btn-sm" onclick="add_charges_row(this,event)">Add</button>')
        }
        else {
            $("#charges_range tbody tr").eq(trIndex).find('td').eq(4).html('<button type="submit" class="btn btn-success btn-sm" onclick="add_charges_row(this,event)">Add</button> <button type="submit" class="btn btn-danger  btn-sm" onclick="del_charges_row(this,event)">Delete</button>')
        }
    }
}
function validate_row(x) {
    return true;
}
$('#btn-backup').click(function () {
    $("div.overlay").css("display", "block");
    document.location = base_url + 'index.php/settings/backup/database';
    load_db_list()
});
$('#btn-walletbackup').click(function () {
    $("div.overlay").css("display", "block");
    document.location = base_url + 'index.php/admin_settings/interWalletAcc_backup';
    load_db_list()
});
function load_db_list() {
    $("div.overlay").css("display", "block");
    var oTable = $('#db_list').DataTable();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/backup/database/list',
        dataType: 'json',
        success: function (data) {
            oTable = $('#db_list').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": true,
                "aaSorting": [[0, "desc"]],
                "aaData": data,
                "aoColumns": [
                    { "mDataProp": "id_dbbackup" },
                    { "mDataProp": "backup_date" },
                    { "mDataProp": "employee" },
                    { "mDataProp": "filename" }
                ]
            });
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//To load imported records list 
function get_import_list() {
    my_Date = new Date();
    var lower = $('#lower').val();
    var upper = $('#upper').val();
    console.log(lower);
    console.log(upper);
    $.ajax({
        url: base_url + "index.php/settings/import/ajax_list/" + lower + "/" + upper + "?nocache=" + my_Date.getUTCSeconds(),
        data: { 'lower': lower, 'upper': upper },
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            set_import_list(data);
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function set_import_list(data) {
    var imported = data.imported;
    var oTable = $('#imported_list').DataTable();
    oTable.clear().draw();
    if (imported != null && imported.length > 0) {
        oTable = $('#imported_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "aaData": imported,
            "aoColumns": [{
                "mDataProp": function (row, type, val, meta) {
                    return "<label class='checkbox-inline'><input type='checkbox' name='account_id[]' value='" + row.id_customer + "'/> " + row.id_customer + "</label>"
                }
            },
            { "mDataProp": "name" },
            { "mDataProp": "mobile" },
            { "mDataProp": "email" }]
        });
    }
}
function get_cardbrand(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/settings/cardbrand/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            wt = data.card_brand;
            sc = data.short_code;
            ct = data.card_type;
            $('#edcard_brand').val(wt);
            $('#edshort_code').val(sc);
            $('#edcardtype').val(ct);
            if (ct == 1) {
                $("#ed_cc").prop("checked", true);
                $("#ed_dc").prop("checked", false);
            }
            else {
                $("#ed_cc").prop("checked", false);
                $("#ed_dc").prop("checked", true);
            }
        }
    });
}
function update_cardbrand(card_type, cardbrand, short_code, id) {
    my_Date = new Date();
    $.ajax({
        data: { "card_type": card_type, "card_brand": cardbrand, "short_code": short_code },
        url: base_url + "index.php/settings/cardbrand/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function () {
            window.location.reload(true);
            //set_table();
        }
    });
}
function set_cardbrand_table() {
    my_Date = new Date();
    var oTable = $('#cardbrand_list').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ajax": base_url + "index.php/settings/cardbrand_list?nocache=" + my_Date.getUTCSeconds(),
        "columns": [
            { "data": "id_card_brand" },
            { "data": "card_type" },
            { "data": "card_brand" },
            { "data": "short_code" },
            {
                "data": function (row, type, val, meta) {
                    id = row.id_card_brand;
                    action_content = '<a href="#" class="btn btn-primary btn-edit" role="button" data-toggle="modal" data-id=' + id + '  data-target="#confirm-edit"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href=' + base_url + "index.php/settings/cardbrand/delete/" + id + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                    return action_content;
                }
            }
        ]
    });
}
function add_card_brand(card_type, brand, code) {
    my_Date = new Date();
    $.ajax({
        data: { "card_type": card_type, "card_brand": brand, "short_code": code },
        url: base_url + "index.php/settings/cardbrand/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function (data) {
            $('#weight').val('');
            location.reload(true);
        }
    });
}
//branch settings
$(document).on("click", ".branch_setting", function (event) {
    $('#branch_save').hide();
});
$('#branch_enable').click(function () {
    var branch_change = $("#tab_branch input[type='radio'][name='general[branch_settings]']:checked").val();
    if (branch_change != '') {
        $('#confirm-changebranch').modal('show');
    }
    else {
        $('#clr_alert').addClass('alert-danger');
        $('#clr_alert span').html('Select atleast one option.');
        $('#clr_alert').css('display', 'block');
    }
});
$(document).on("click", "#confirm_change", function (event) {
    $('.modal.in').modal().hide();
    $('.modal-backdrop').remove();
    $('#branch_save').show();
    //alert('Click Save Option Change Your Branch Settings');
});
//branch settings	
// branch add
$('#ed_countrys').on('change', function () {
    get_states(this.value);
    $('#ed_states').clear();
});
$('#ed_states').on('change', function () {
    get_citys(this.value);
});
//branch work
//add & upd branch with image//HH
$(document).on('click', "#branch_list  a.btn-edit", function (e) {
    $("#ed_branch").val('');
    e.preventDefault();
    id = $(this).data('id');
	// get_branch(id);
    get_country();
    $("#edit-id").val(id);
});
$("#branch_pincode,#ed_pincode").on('change', function () {
    var pincode = this.value;
    if (pincode != '') {
        var regexp = /^([0-9]{6})?$/;
        if (!regexp.test(pincode)) {
            $("#branch_pincode").val("");
            $("#branch_pincode").focus();
            $("#ed_pincode").val("");
            $("#ed_pincode").focus();
        }
    }
});
$("#mobile,#ed_mobile").on('change', function () {
    var mobile = this.value;
    if (mobile != '') {
        var regexp = /^([0-9]{10})?$/;
        if (!regexp.test(mobile)) {
            $("#mobile").val("");
            $("#mobile").focus();
            $("#ed_mobile").val("");
            $("#ed_mobile").focus();
        }
    }
});
// Google Review URL validator — allows only HTTPS Google domains
function isValidGoogleReviewUrl(url) {
    if (!url || url.trim() === '') return true; // empty = OK (optional field)
    var pattern = /^https:\/\/(www\.)?(google\.(com|co\.\w{2,3})|maps\.google\.(com|co\.\w{2,3})|search\.google\.com|g\.page)(\/.*)?$/i;
    return pattern.test(url.trim());
}
$("#add_branch").on('click', function () {
    if ($('#branch').val()) {
        // Validate Google Review URL before proceeding
        var reviewUrl = $('#google_review_url').val();
        if (!isValidGoogleReviewUrl(reviewUrl)) {
            $.toaster({ priority: 'error', title: 'Invalid URL', message: 'Google Review URL must be a valid https://google.com or https://g.page link' });
            $('#google_review_url').focus();
            return;
        }
        var metal_type = $("input[name='metal_type']:checked").val();
        var show_to_all = $("input[name='show_to_all']:checked").val();
        var is_ho = $("input[name='is_ho']:checked").val();
        var file = $("#branch_img")[0].files[0];
        var gst = $("#gst_number").val();
        if (gst != '') {
            if (gst.length < 15) {
                //alert(gst.length);
                $("#gst_number").val("");
                $("#gst_number").focus();
                $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>GST number should not be less than 15 format: 11AAAAA1111Z1A1 " });
            }
            else if (gst == 15) {
                checkgst(gst);
            }
            else {
                $('#confirm-add').modal('hide');
                if (branch) {
                    add_branch($('#branch').val(), is_ho, $('#map_url').val(), $('#short_name').val(), $('#address1').val(), $('#address2').val(), $('#country').val(), $('#state').val(), $('#city').val(), $('#branch_pincode').val(), $('#sel_village').val(), $('#phone').val(), $('#mobile').val(), $('#cusromercare').val(), metal_type, $('#day_close').val(), show_to_all, $('#partial_goldrate').val(), $('#partial_silverate').val(), file);
                }
            }
        }
        else {
            $('#confirm-add').modal('hide');
            if (branch) {
                add_branch($('#branch').val(), is_ho, $('#map_url').val(), $('#short_name').val(), $('#address1').val(), $('#address2').val(), $('#country').val(), $('#state').val(), $('#city').val(), $('#branch_pincode').val(), $('#sel_village').val(), $('#phone').val(), $('#mobile').val(), $('#cusromercare').val(), metal_type, $('#day_close').val(), show_to_all, $('#partial_goldrate').val(), $('#partial_silverate').val(), file);
            }
        }
        var metal_type = $("input[name='metal_type']:checked").val();
        var show_to_all = $("input[name='show_to_all']:checked").val();
        var file = $("#branch_img")[0].files[0];
        //add_branch($('#branch').val(), is_ho, $('#map_url').val(), $('#short_name').val(), $('#address1').val(), $('#address2').val(), $('#country').val(), $('#state').val(), $('#city').val(), $('#branch_pincode').val(),$('#id_village').val(), $('#phone').val(), $('#mobile').val(), $('#cusromercare').val(), metal_type, $('#day_close').val(), show_to_all, $('#partial_goldrate').val(), $('#partial_silverate').val(), file);
        $('#branch').val('');
        $('#branch_img').val('');
        $('#is_ho').val('');
        $('#map_url').val('');
        $('#short_name').val('');
        $('#show_to_all').val('');
        $('#metal_type').val('');
        $('#day_close').val('');
        $('#partial_goldrate').val('');
        $('#partial_silverate').val('');
    } else {
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Enter All required values" });
        $('#branch').focus();
    }
});
$("#update_branch").on('click', function () {
    // Validate Google Review URL before proceeding
    var reviewUrl = $('#ed_google_review_url').val();
    if (!isValidGoogleReviewUrl(reviewUrl)) {
        $.toaster({ priority: 'error', title: 'Invalid URL', message: 'Google Review URL must be a valid https://google.com or https://g.page link' });
        $('#ed_google_review_url').focus();
        return;
    }
    var file = $("#edit_sch_branch_img")[0].files[0];
    var branch = $("#ed_branch").val();
    var map_url = $("#ed_map_url").val();
    //var short_name=$("#ed_short_name").val()	
    var short_name = $("#ed_short_name").val();
    var address1 = $("#ed_address1").val();
    var address2 = $("#ed_address2").val();
    var country = $("#ed_countrys").val();
    var state = $("#ed_states").val();
    var city = $("#ed_citys").val();
    var pincode = $("#ed_pincode").val();
    var village = $("#ed_sel_village").val();
    var phone = $("#ed_phone").val();
    var mobile = $("#ed_mobile").val();
    var ed_day_close = $("#ed_day_close").val();
    var is_ho = $("input[name='ed_is_ho']:checked").val();
    var metal_rate_type = $("input[name='ed_metal_type']:checked").val();
    var show_to_all = $("input[name='ed_show_to_all']:checked").val();
    var ed_enable_gift_voucher = $("input[name='ed_enable_gift_voucher']:checked").val();
    var cusromercare = $("#ed_customer_care").val();
    var partial_goldrate_diff = $("#ed_partial_goldrate").val();
    var partial_silverrate_diff = $("#ed_partial_silverate").val();
    var id = $("#edit-id").val();
    var gst = $("#ed_gst_number").val();
    if (gst != '') {
        if (gst.length < 15) {
            //alert(gst.length);
            $("#ed_gst_number").val("");
            $("#ed_gst_number").focus();
            $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>GST number should not be less than 15 format: 11AAAAA1111Z1A1 " });
        }
        else if (gst == 15) {
            checkgst(gst);
        }
        else {
            $('#confirm-edit').modal('hide');
            if (branch) {
                update_branch(branch, map_url, is_ho, ed_day_close, short_name, address1, address2, country, state, city, pincode, village, phone, mobile, cusromercare, metal_rate_type, show_to_all, partial_goldrate_diff, partial_silverrate_diff, ed_enable_gift_voucher, file);
            }
        }
    }
    else {
        $('#confirm-edit').modal('hide');
        if (branch) {
            update_branch(branch, map_url, is_ho, ed_day_close, short_name, address1, address2, country, state, city, pincode, village, phone, mobile, cusromercare, metal_rate_type, show_to_all, partial_goldrate_diff, partial_silverrate_diff, ed_enable_gift_voucher, file);
        }
    }
    //console.log(metal_rate_type);
    /*	if(branch)
     {
        update_branch(branch,map_url,is_ho,ed_day_close,short_name,address1,address2,country,state,city,pincode,phone,mobile,cusromercare,metal_rate_type,show_to_all,partial_goldrate_diff,partial_silverrate_diff,ed_enable_gift_voucher,file);
     }  */
});
// function set_branch_table() {
// 	my_Date = new Date();
// 	var oTable = $('#branch_list').dataTable({
// 		"aProcessing": true,
// 		"aServerSide": true,
// 		"ajax": base_url + "index.php/branch/branch_list?nocache=" + my_Date.getUTCSeconds(),
// 		"columnDefs":
// 			[
// 				{
// 					targets: [0, 1, 2],
// 					className: 'dt-left'
// 				},
// 			],
// 		"columns": [
// 			{ "data": "id_branch" },
// 			{ "data": "name" },
// 			{ "data": "short_name" },
// 			{
// 				"mDataProp": function (row, type, val, meta) {
// 					active_url = base_url + "index.php/branch/branch_stat/" + row.id_branch + "/" + (row.active == 1 ? 0 : 1);
// 					return "<a href='" + active_url + "'><i class='fa " + (row.active == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.active == 1 ? 'green' : 'red') + "'></i></a>"
// 				}
// 			},
// 			{
// 				"data": function (row, type, val, meta) {
// 					console.log(row.access);
// 					id = row.id_branch;
// 					action_content = '<a href="#" class="btn btn-primary btn-edit" role="button" data-toggle="modal" data-id=' + id + '  data-target="#confirm-edit"><i class="fa fa-edit" ></i> Edit</a> ';
// 					return action_content;
// 				}
// 			}
// 		]
// 	});
// }
function set_branch_table() {
    let my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/branch/branch_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            let branches = data.data;
            let access = data.access;
            // Clear any existing data
            let oTable = $('#branch_list').DataTable();
            oTable.clear().draw();
            // Check if there is data to display
            if (branches != null && branches.length > 0) {
                oTable = $('#branch_list').DataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "columnDefs": [
                        {
                            targets: [0, 1, 2],
                            className: 'dt-left'
                        }
                    ],
                    "aaData": branches,
                    "aoColumns": [
                        { "mDataProp": "id_branch" },
                        { "mDataProp": "name" },
                        { "mDataProp": "short_name" },
                        {
                            "mDataProp": function (row, type, val, meta) {
                                let active_url = base_url + "index.php/branch/branch_stat/" + row.id_branch + "/" + (row.active == 1 ? 0 : 1);
                                return (access.edit == '1' ? "<a href='" + active_url + "'><i class='fa " + (row.active == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.active == 1 ? 'green' : 'red') + "'></i></a>" : "");
                            }
                        },
                        {
                            "mDataProp": function (row, type, val, meta) {
                                let id = row.id_branch;
                                let edit_url = (access.edit == '1' ? base_url + 'index.php/branch/edit/' + id : '#');
                                // let delete_url = (access.delete == '1' ? base_url + 'index.php/branch/delete/' + id : '#');
                                let action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                    /* (access.edit =='1' ? '<li><a href="#" class="btn-edit" data-href=' + edit_url + ' data-toggle="modal" data-target="#branch_form" onclick="set_branch_form(' + id + ')"><i class="fa fa-edit"></i> Edit</a></li>' :'' )  + */
                                    (access.edit == '1' ? '<li><a href="#" class="btn-edit" data-href=' + edit_url + ' data-toggle="modal" data-target="#confirm-edit" onclick="get_branch(' + id + ')"><i class="fa fa-edit"></i> Edit</a></li>' : '') +
                                    '</ul></div>';
                                return action_content;
                            }
                        }
                    ]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
            console.error("Error fetching branch data:", error);
        }
    });
}
function add_branch(branch, is_ho, map_url, short_name, address1, address2, country, state, city, pincode, village, phone, mobile, cusromercare, metal_type, day_close, show_to_all, partial_goldrate_diff, partial_silverrate_diff, file) {
    my_Date = new Date();
    var file = file;
    var data = new FormData();
    var form_data = new FormData();
    form_data.append('file', file);
    form_data.append('branch', branch);
    form_data.append('is_ho', is_ho);
    form_data.append('map_url', map_url);
    form_data.append('short_name', short_name);
    form_data.append('address1', address1);
    form_data.append('address2', address2);
    form_data.append('country', country);
    form_data.append('state', state);
    form_data.append('city', city);
    form_data.append('pincode', pincode);
    form_data.append('sel_village', village);
    form_data.append('phone', phone);
    form_data.append('mobile', mobile);
    form_data.append('cusromercare', cusromercare);
    form_data.append('metal_type', metal_type);
    form_data.append('day_close', day_close);
    form_data.append('show_to_all', show_to_all);
    form_data.append('partial_goldrate_diff', partial_goldrate_diff);
    form_data.append('partial_silverrate_diff', partial_silverrate_diff);
    form_data.append('partial_silverrate_diff', partial_silverrate_diff);
    form_data.append('gst_number', $('#gst_number').val());
    form_data.append('google_review_url', $('#google_review_url').val());
    $.ajax({
        data: form_data,
        url: base_url + "index.php/branch/branch_name/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        cache: false,
        success: function (data) {
            location.reload(true);
        }
    });
}
function get_branch(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/branch/branch_name/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            branch = data.name;
            var x = data.is_ho;
            if (x == 1) {
                $('input:radio[name="ed_is_ho"][value="1"]').attr('checked', true);
            } else if (x == 0) {
                $('input:radio[name="ed_is_ho"][value="0"]').attr('checked', true);
            }
            if (data.enable_gift_voucher == 1) {
                $('#ed_enable_gift_voucher_yes').attr('checked', true);
            } else {
                $('#ed_enable_gift_voucher_no').attr('checked', true);
            }
            $('#ed_id').val(id);
            $('#ed_day_close').val(data.day_close);
            $('#edit_sch_branch_img').val(data.edit_sch_branch_img);
            $('#ed_branch').val(branch);
            $('#ed_short_name').val(data.short_name);
            $('#ed_map_url').val(data.map_url);
            $('#ed_address1').val(data.address1);
            $('#ed_address2').val(data.address2);
            $('#ed_pincode').val(data.pincode).keyup();
            $('#ed_id_village').val(data.id_village);
            $('#ed_phone').val(data.phone);
            $('#ed_mobile').val(data.mobile);
            $('#ed_customer_care').val(data.cusromercare);
            $('#ed_stae').val(data.id_state);
            $('#ed_country').val(data.id_country);
            $('#ed_city').val(data.id_city);
            $('#ed_metal_type').val(data.metal_rate_type);
            $('#ed_show_to_all').val(data.show_to_all);
            $('#ed_partial_goldrate').val(data.partial_goldrate_diff);
            $('#ed_partial_silverate').val(data.partial_silverrate_diff);
            $('#ed_gst_number').val(data.gst_number);
            $('#ed_google_review_url').val(data.google_review_url);
            if (data.metal_rate_type != 2) {
                $("#ed_partial_silverate").attr("disabled", "disabled");
                $("#ed_partial_goldrate").attr("disabled", "disabled");
            }
            $("input[name=ed_show_to_all][value=" + data.show_to_all + "]").attr('checked', true);
            logo = data.logo;
            // $('#ed_clsfy').val(classification_name);
            if (logo != "" && logo != null) {
                var img = base_url + "assets/img/branch/" + logo;
                $("#edit_sch_branch_img_preview").attr('src', img);
            }
            else {
                var img = base_url + "assets/img/no_image.png";
                $("#edit_sch_branch_img_preview").attr('src', img);
            }
            if (data.metal_rate_type == 0) {
                $("#rate_manual").prop('checked', true);
            }
            else if (data.metal_rate_type == 1) {
                $("#rate_auto").prop('checked', true);
            }
            else {
                $("#rate_partial").prop('checked', true);
            }
            edit_country();
        }
    });
}
//add & upd branch with image//HH
function edit_country() {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/company/getcountry?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (country) {
            //	console.log(country);
            $.each(country, function (key, country) {
                $('#ed_countrys').append(
                    $("<option></option>")
                        .attr("value", country.id)
                        .text(country.name)
                );
            });
            var selectid = $('#ed_country').val();
            if (selectid != null) {
                $('#ed_countrys').val(selectid);
                get_states(selectid);
            }
        }
    });
}
function get_states(id) {
    my_Date = new Date();
    $('#ed_states option').remove();
    $.ajax({
        type: 'POST',
        data: { 'id_country': id },
        url: base_url + 'index.php/settings/company/getstate/?nocache=' + my_Date.getUTCSeconds(),
        cache: false,
        dataType: 'json',
        success: function (state) {
            $.each(state, function (key, state) {
                $('#ed_states').append(
                    $("<option></option>")
                        .attr("value", state.id)
                        .text(state.name)
                );
            });
            var selectid = $('#ed_stae').val();
            if (selectid != null) {
                $('#ed_states').val(selectid);
                get_citys(selectid);
            }
        }
    });
}
function get_citys(id) {
    my_Date = new Date();
    $('#ed_citys option').remove();
    my_Date = new Date();
    $.ajax({
        type: 'POST',
        data: { 'id_state': id },
        url: base_url + "index.php/settings/company/getcity?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: 'json',
        success: function (city) {
            $.each(city, function (key, city) {
                $('#ed_citys').append(
                    $("<option></option>")
                        .attr("value", city.id)
                        .text(city.name)
                );
            });
            var selectid = $('#ed_city').val();
            if (selectid != null) {
                $('#ed_citys').val(selectid);
            }
        }
    });
}
function update_branch(branch, map_url, is_ho, ed_day_close, short_name, address1, address2, country, state, city, pincode, village, phone, mobile, cusromercare, metal_type, show_to_all, partial_goldrate_diff, partial_silverrate_diff, ed_enable_gift_voucher, file) {
    my_Date = new Date();
    var file = file;
    var data = new FormData();
    var form_data = new FormData();
    form_data.append('file', file);
    form_data.append('branch', branch);
    form_data.append('is_ho', is_ho);
    form_data.append('map_url', map_url);
    form_data.append('short_name', short_name);
    form_data.append('address1', address1);
    form_data.append('address2', address2);
    form_data.append('country', country);
    form_data.append('state', state);
    form_data.append('city', city);
    form_data.append('pincode', pincode);
    form_data.append('phone', phone);
    form_data.append('sel_village', village);
    form_data.append('mobile', mobile);
    form_data.append('cusromercare', cusromercare);
    form_data.append('day_close', ed_day_close);
    form_data.append('metal_type', metal_type);
    form_data.append('show_to_all', show_to_all);
    form_data.append('partial_goldrate_diff', partial_goldrate_diff);
    form_data.append('partial_silverrate_diff', partial_silverrate_diff);
    form_data.append('partial_silverrate_diff', partial_silverrate_diff);
    form_data.append('ed_enable_gift_voucher', ed_enable_gift_voucher);
    form_data.append('gst_number', $("#ed_gst_number").val());
    form_data.append('google_review_url', $("#ed_google_review_url").val());
    var id = $('#ed_id').val();
    $.ajax({
        data: form_data,
        url: base_url + "index.php/branch/branch_name/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        success: function (data) {
            location.reload(true);
        }
    });
}
// end branch add
//add & upd branch with image//HH
// metal_rate list branch 
$('#branch_select').select2().on("change", function (e) {
    if ((ctrl_page[2] == 'list' && this.value != '')) {
        var id_branch = $(this).val();
        barnch_metalratelist(id_branch);
    }
    if (((ctrl_page[2] == 'add' || ctrl_page[2] == 'edit') && this.value != ''))  // based on the branch settings to showed Offer & New Arrivals page branch filter//HH
    {
        var id_branch = $(this).val();
        $('#id_branch').val(id_branch);
    }
    if ((ctrl_page[1] == 'gift' && ctrl_page[2] == 'list' && this.value != '')) {
        get_all_gifts();
        set_gift_table();
    }
});
function get_branchnames() {
    $(".overlay").css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/branch/branchname_list',
        dataType: 'json',
        success: function (data) {
            var scheme_val = $('#id_branch').val();
            if (ctrl_page[1] == 'gift' && ctrl_page[2] == 'list') {
                $('#branch_select').append(
                    $("<option></option>")
                        .attr("value", 0)
                        .text('All')
                );
            }
            $("#branch_select").select2({
                placeholder: "Select branch name",
                allowClear: true
            });
            $.each(data.branch, function (key, item) {
                $('#branch_select').append(
                    $("<option></option>")
                        .attr("value", item.id_branch)
                        .text(item.name)
                );
            });
            $("#branch_select").select2("val", (scheme_val != '' && scheme_val > 0 ? scheme_val : ''));
            if (ctrl_page[1] == 'gift' && ctrl_page[2] == 'list') {
                $("#branch_form_select").select2({
                    placeholder: "Select branch name",
                    allowClear: true
                });
                /*$('#branch_form_select').append(
                    $("<option></option>")
                    .attr("value", 0)						  
                    .text('ALL' )
                );*/
                var branch_val = $('#branch_form_select').val();
                $.each(data.branch, function (key, item) {
                    $('#branch_form_select').append(
                        $("<option></option>")
                            .attr("value", item.id_branch)
                            .text(item.name)
                    );
                });
                $("#branch_form_select").select2("val", (branch_val != '' && branch_val > 0 ? branch_val : ''));
            }
            $(".overlay").css("display", "none");
        }
    });
}
function barnch_metalratelist(id) {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    var oTable = $('#metalrate_list').DataTable();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/branch/metal_rate/ajax_list/' + id,
        dataType: 'json',
        success: function (data) {
            var access = data.access;
            $('#total_metals').text(data.rates.length);
            if (access.add == '0') {
                $('#add_metal').attr('disabled', 'disabled');
            }
            var max_id = data.max_id;
            oTable = $('#metalrate_list').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": true,
                "aaSorting": [[0, "desc"]],
                "aaData": data.rates,
                "aoColumns": [
                    { "mDataProp": "id_metalrates" },
                    { "mDataProp": "updatetime" },
                    { "mDataProp": "goldrate_22ct" },
                    { "mDataProp": "goldrate_24ct" },
                    { "mDataProp": "silverrate_1gm" },
                    { "mDataProp": "silverrate_1kg" },
                    { "mDataProp": "employee" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_metalrates;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/rate/edit/' + id : "#");
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li></ul></div>';
                            return (id == max_id ? action_content : '-');
                        }
                    }
                ]
            });
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
// metal_rate list
function validate_Image1() {
	var preview ;
    var height = ($(this).height());
    var width = ($(this).width());
    if (arguments[0].id == 'edit_sch_clsfy_img') {
		preview = $('#edit_sch_clsfy_img_preview');
    }
    else if (arguments[0].id == 'sch_clsfy_img') {
		preview = $('#sch_clsfy_img_preview');
    }
    if (arguments[0].files[0].size > 1048576) {
        alert('File size cannot be greater than 1 MB');
        arguments[0].value = "";
        preview.css('display', 'none');
    }
    else {
        var fileName = arguments[0].value;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        ext = ext.toLowerCase();
        if (ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg") {
            alert("Upload JPG or PNG Images only");
            arguments[0].value = "";
            preview.css('display', 'none');
        }
        /*if(width>960 && height>525)
        {
            alert("Width and height should be less than  960 * 525 ");
            arguments[0].value = "";
            preview.css('display','none');
        }*/
        else {
            var file = arguments[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.prop('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
                preview.css('display', '');
            }
            else {
                preview.prop('src', '');
                preview.css('display', 'none');
            }
        }
    }
}
//payment gateway//
$("input[name='gateway_active']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#gateway_active').val(1);
    }
    else {
        $('#gateway_active').val(0);
    }
});
$("input[name='save_card']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#save_card').val(1);
    }
    else {
        $('#save_card').val(0);
    }
});
$("input[name='debit_card']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#debit_card').val(1);
    }
    else {
        $('#debit_card').val(0);
    }
});
$("input[name='credit_card']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#credit_card').val(1);
    }
    else {
        $('#credit_card').val(0);
    }
});
$("input[name='netbanking']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#netbanking').val(1);
    }
    else {
        $('#netbanking').val(0);
    }
});
$("input[name='ed_gateway']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#ed_gateway').val(1);
    }
    else {
        $('#ed_gateway').val(0);
    }
});
$("input[name='ed_save_card']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#ed_save_card').val(1);
    }
    else {
        $('#ed_save_card').val(0);
    }
});
$("input[name='ed_debit_card']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#ed_debit_card').val(1);
    }
    else {
        $('#ed_debit_card').val(0);
    }
});
$("input[name='ed_credit_card']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#ed_credit_card').val(1);
    }
    else {
        $('#ed_credit_card').val(0);
    }
});
$("input[name='ed_netbanking']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#ed_netbanking').val(1);
    }
    else {
        $('#ed_netbanking').val(0);
    }
});
$("input[name='cardtype']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#card_type').val(1);
    }
    else {
        $('#card_type').val(2);
    }
});
$("input[name='edcardtype']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#edcard_type').val(1);
    }
    else {
        $('#edcard_type').val(2);
    }
});
$("#add_paymentgateway").on('click', function () {
    var file = $("#pay_gateway_img")[0].files[0];
    var id_metal = $('#id_metal').val();
    add_payment_gateway($('#gateway_name').val(), $('#gateway_code').val(), $('#gateway_active').val(), $('#save_card').val(), $('#debit_card').val(), $('#credit_card').val(), $('#netbanking').val(), file, $("#gateway_description").val(), id_metal);
});
$("#update_payment_gateway").on('click', function () {
    // var file = $("#edit_pay_gateway_img")[0].files[0];
    var file = $("#edit_pay_gateway_img")[0].files[0];
    var id = $("#edit-id").val();
    var ed_id_metal = $("#ed_id_metal").val();
    update_payment_gateway($("#ed_pg_name").val(), $("#ed_code").val(), $("#ed_gateway").val(), $("#ed_save_card").val(), $("#ed_credit_card").val(), $("#ed_debit_card").val(), $("#ed_netbanking").val(), $("#edit-id").val(), file, $("#ed_gateway_description").val(), ed_id_metal);
});
$(document).on('click', "#paymentgateway_list a.btn-edit", function (e) {
    $("#ed_pg_name").val('');
    $("#ed_code").val('');
    e.preventDefault();
    id = $(this).data('id');
    get_paymentgateway(id);
    $("#edit-id").val(id);
});
$("#pay_gateway_img").change(function () {
    event.preventDefault();
    console.log(this);
    validate_cls_Image(this);
});
$("#edit_pay_gateway_img").change(function () {
    event.preventDefault();
    console.log(this);
    validate_cls_Image(this);
});
function validate_cls_Image() {
    if (arguments[0].id == 'edit_pay_gateway_img') {
        var preview = $('#edit_paymentgateway_img_preview');
    }
    else if (arguments[0].id == 'pay_gateway_img') {
        var preview = $('#pay_gateway_img_preview');
    }
    if (arguments[0].files[0].size > 1048576) {
        alert('File size cannot be greater than 1 MB');
        arguments[0].value = "";
        preview.css('display', 'none');
    }
    else {
        var fileName = arguments[0].value;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        ext = ext.toLowerCase();
        if (ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg") {
            alert("Upload JPG or PNG Images only");
            arguments[0].value = "";
            preview.css('display', 'none');
        }
        else {
            var file = arguments[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.prop('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
                preview.css('display', '');
            }
            else {
                preview.prop('src', '');
                preview.css('display', 'none');
            }
        }
    }
}
function add_payment_gateway(gateway_name, code, gateway_active, savecard, debit_card, credit_card, netbanking, file, description, id_metal) {
    my_Date = new Date();
    console.log(file);
    var form_data = new FormData();
    form_data.append('gateway_name', gateway_name);
    form_data.append('code', code);
    form_data.append('gateway_active', gateway_active);
    form_data.append('savecard', savecard);
    form_data.append('debit_card', debit_card);
    form_data.append('creditcard', credit_card);
    form_data.append('netbanking', netbanking);
    form_data.append('file', file);
    form_data.append('description', description);
    form_data.append('id_metal', id_metal);
    console.log(form_data);
    $.ajax({
        data: form_data,
        url: base_url + "index.php/settings/payment_gateway/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        success: function (data) {
            location.reload(true);
        }
    });
}
/*function set_paymentgateway_list()
{
    my_Date = new Date();
    var oTable = $('#paymentgateway_list').dataTable({
                "aProcessing": true,
                "ajax": base_url+"index.php/settings/payment_gateway_list?nocache=" + my_Date.getUTCSeconds(),
                "columns": [
                { "data": "id_pg_settings" },
                { "data": "pg_name" },
                { "mDataProp": function ( row, type, val, meta ) {
                                       id   = row.pg_icon;
                                       if(row.pg_icon!=""&& row.pg_icon!=null)
                                       {
                                              img=base_url+"assets/img/gateway/"+row.pg_icon;
                                       }
                                       else
                                       {
                                               img=base_url+"assets/img/no_image.png";
                                       }
                                       action_content='<a href="#"><img src= '+img+' width=50px;" height="50px;"></a>';
                                           return action_content;
                  }}, 
                { "data": "active" },
                { "data": function ( row, type, val, meta ) {
                     id= row.id_pg_settings;
                     action_content='<a href="#" class="btn btn-primary btn-edit" role="button" data-toggle="modal" data-id='+id+'  data-target="#confirm-edit"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+base_url+"index.php/settings/paymentgateway/delete/"+id+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                    return action_content;
                    }
            }
              ]
        });
}*/
function set_paymentgateway_list(id_branch = '') {
    my_Date = new Date();
    $.ajax({
        data: { 'id_branch': id_branch },
        url: base_url + "index.php/settings/payment_gateway_list?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        success: function (data) {
            set_gateway_list(data);
        }
    });
}
function set_gateway_list(data) {
    var customer = data;
    var oTable = $('#paymentgateway_list').DataTable();
    oTable.clear().draw();
    oTable = $('#paymentgateway_list').dataTable({
        "bDestroy": true,
        "bSort": true,
        "dom": 'T<"clear">lfrtip',
        "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
        "aaData": data,
        "aoColumns": [
            { "mDataProp": "id_pg" },
            { "mDataProp": "pg_name" },
            { "mDataProp": "type" },
            {
                "mDataProp": function (row, type, val, meta) {
                    id = row.pg_icon;
                    if (row.pg_icon != "" && row.pg_icon != null) {
                        img = base_url + "assets/img/gateway/" + row.pg_icon;
                    }
                    else {
                        img = base_url + "assets/img/no_image.png";
                    }
                    action_content = '<a href="#"><img src= ' + img + ' width=50px;" height="50px;"></a>';
                    return action_content;
                }
            },
            { "mDataProp": "active" },
            {
                "data": function (row, type, val, meta) {
                    id = row.id_pg;
                    action_content = '<a href="#" class="btn btn-primary btn-edit" role="button" data-toggle="modal" data-id=' + id + '  data-target="#confirm-edit"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href=' + base_url + "index.php/settings/paymentgateway/delete/" + id + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                    return action_content;
                }
            },
        ]
    });
}
function update_payment_gateway(pg_name, code, ed_gateway, savecard, creditcard, debitcard, netbanking, id, file, description, id_metal) {
    my_Date = new Date();
    var form_data = new FormData();
    var id_branch = $("#id_branch").val();
    form_data.append('gateway_name', pg_name);
    form_data.append('code', code);
    form_data.append('active', ed_gateway);
    form_data.append('savecard', savecard);
    form_data.append('debit_card', debitcard);
    form_data.append('creditcard', creditcard);
    form_data.append('netbanking', netbanking);
    form_data.append('file', file);
    form_data.append('id', id);
    form_data.append('description', description);
    form_data.append('id_metal', id_metal);
    form_data.append('id_branch', id_branch);
    console.log(form_data);
    $.ajax({
        data: form_data,
        url: base_url + "index.php/settings/paymentgateway/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        cache: false,
        success: function () {
            window.location.reload(true);
            //set_table();
        }
    });
}
function get_paymentgateway(id) {
    my_Date = new Date();
    var id_branch = $("#id_branch").val();
    $.ajax({
        type: "POST",
        data: { 'id_branch': id_branch, 'id_pg': id },
        url: base_url + "index.php/settings/payment_gateway/edit?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            console.log(data);
            pg_name = data.pg_name;
            pg_code = data.pg_code;
            active = data.active;
            savecard = data.savecard;
            debitcard = data.debitcard;
            creditcard = data.creditcard;
            netbanking = data.netbanking;
            pg_icon = data.pg_icon;
            description = data.description;
            $('#ed_gateway_description').val(description);
            $('#ed_pg_name').val(pg_name);
            $('#ed_code').val(pg_code);
            if (active == 1) {
                $('#ed_gateway_active').prop('checked', true);
                $('#ed_gateway_inactive').prop('checked', false);
                $('#ed_gateway').val(1);
            }
            else {
                $('#ed_gateway_active').prop('checked', false);
                $('#ed_gateway_inactive').prop('checked', true);
                $('#ed_gateway').val(0);
            }
            if (savecard == 1) {
                $('#ed_save_card_active').prop('checked', true);
                $('#ed_save_card_inactive').prop('checked', false);
                $('#ed_save_card').val(1);
            }
            else {
                $('#ed_save_card_active').prop('checked', false);
                $('#ed_save_card_inactive').prop('checked', true);
                $('#ed_save_card').val(0);
            }
            if (debitcard == 1) {
                $('#ed_debit_card_active').prop('checked', true);
                $('#ed_debit_card_inactive').prop('checked', false);
                $('#ed_debit_card').val(1);
            }
            else {
                $('#ed_debit_card_active').prop('checked', false);
                $('#ed_debit_card_inactive').prop('checked', true);
                $('#ed_debit_card').val(0);
            }
            if (creditcard == 1) {
                $('#ed_credit_card_active').prop('checked', true);
                $('#ed_credit_card_inactive').prop('checked', false);
                $('#ed_credit_card').val(1);
            }
            else {
                $('#ed_credit_card_active').prop('checked', false);
                $('#ed_credit_card_inactive').prop('checked', true);
                $('#ed_credit_card').val(0);
            }
            if (netbanking == 1) {
                $('#ed_netbanking_active').prop('checked', true);
                $('#ed_netbanking_inactive').prop('checked', false);
                $('#ed_netbanking').val(1);
            }
            else {
                $('#ed_netbanking_active').prop('checked', false);
                $('#ed_netbanking_inactive').prop('checked', true);
                $('#ed_netbanking').val(0);
            }
            if (pg_icon != "" && pg_icon != null) {
                var img = base_url + "assets/img/gateway/" + data.pg_icon;
                $("#edit_paymentgateway_img_preview").attr('src', img);
            }
            else {
                var no_img = base_url + "assets/img/no_image.png";
                $("#edit_paymentgateway_img_preview").attr('src', no_img);
            }
            // Populate Commodity dropdown
            var id_metal = data.id_metal ? data.id_metal : '';
            $('#ed_id_metal').val(id_metal);
        }
    });
}
//payment gateway//
$("input[name='general[enableSilver_rateDisc]']:checkbox").on("change", function () {
    if ($("input[name='general[enableSilver_rateDisc]']:checked").is(":checked")) {
        $("#silverDiscAmt").prop("disabled", false);
    } else {
        $("#silverDiscAmt").prop("disabled", true);
    }
});
if ($("input[name='general[enableSilver_rateDisc]']:checked").is(":checked")) {
    $("#silverDiscAmt").prop("disabled", false);
} else {
    $("#silverDiscAmt").prop("disabled", true);
}
$('#branch_select').select2().on("change", function (e) {
    switch (ctrl_page[1]) {
        case 'payment':
            if (this.value != '') {
                var id_branch = $(this).val();
                $("#id_branch").val(this.value);
                set_paymentgateway_list(id_branch);
            }
            break;
        case 'general':
            if (this.value != '') {
                $("#id_branch").val(this.value);
                var id_branch = $("#id_branch").val();
                var pg_code = $("#pg_code").val();
                var type = $("#type").val();
                if (pg_code != '') {
                    get_gateway(type, pg_code, id_branch);
                }
            }
            break;
    }
});
$("input[name='demo[is_default]']:checkbox").on("change", function () {
    if ($("input[name='demo[is_default]']:checked").is(":checked")) {
        $('#is_default').val(1);
    }
    else {
        $('#is_default').val(0)
    }
});
function get_gateway(type, pg_code, id_branch) {
    my_Date = new Date();
    $.ajax({
        data: { 'type': type, 'pg_code': pg_code, 'id_branch': id_branch },
        url: base_url + 'index.php/admin_settings/ajax_paymentgateway?nocache=' + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        success: function (data) {
            console.log(data.param_1);
            $('#param_1').val(data.param_1);
            $('#param_2').val(data.param_2);
            $('#param_3').val(data.param_3);
            $('#param_4').val(data.param_4);
            $('#api_url').val(data.api_url);
            $('#pg_name').val(data.pg_name);
            $('#gateway_content').show();
            $('#id_pg').val(data.id_pg);
            if (data.is_default == 1) {
                $('#is_default').prop("checked", true);
            }
            $(".overlay").css('display', 'none');
        }
    });
}
function update_gateway() {
    var param_1 = $('#param_1').val();
    var param_2 = $('#param_2').val();
    var param_3 = $('#param_3').val();
    var param_4 = $('#param_4').val();
    var api_url = $('#api_url').val();
    var is_default = $('#is_default').val();
    var pg_code = $("#pg_code").val();
    var type = $("#type").val();
    var id_pg = $("#id_pg").val();
    var id_branch = $("#id_branch").val();
    my_Date = new Date();
    $.ajax({
        data: { 'id_branch': id_branch, 'param_1': param_1, 'param_2': param_2, 'param_3': param_3, 'param_4': param_4, 'api_url': api_url, 'is_default': is_default, 'id_pg': id_pg },
        url: base_url + 'index.php/admin_settings/update_gateway?nocache=' + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        success: function () {
            // window.location.reload();
        }
    });
}
$("#gateway").on('click', "li a.gateway_list", function (event) {
    event.preventDefault();
    var pg_code = ($(this).attr('value'));
    $('#gateway_type').css({ "display": "block" });
    $("#pg_code").val(pg_code);
    var type = $("#type").val();
    var id_branch = $("#id_branch").val();
    if ($("#type").val() != '') {
        get_gateway(type, pg_code, id_branch);
    }
});
$("#gateway_type").on('click', "li a.gateway_type", function (event) {
    var type = ($(this).attr('value'));
    $(".overlay").css('display', 'block');
    $('#type').val(type);
    var pg_code = $("#pg_code").val();
    var id_branch = $("#id_branch").val();
    var type = $("#type").val();
    get_gateway(type, pg_code, id_branch);
});
$("#gateway_submit").on('submit', function (event) {
    event.preventDefault();
    update_gateway();
});
function get_branchname() {
    $(".overlay").css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/branch/branchname_list',
        dataType: 'json',
        success: function (data) {
            $("#branch_select option").remove(); // New code 05-12-2022
            //	var id_branch =  $('#id_branch').val();		   
            $.each(data.branch, function (key, item) {
                $('#branch_select').append(
                    $("<option></option>")
                        .attr("value", item.id_branch)
                        .text(item.name)
                );
            });
            $("#branch_select").select2({
                placeholder: "Select branch name",
                allowClear: true
            });
            console.log(data);
            var ar = $('#sel_bran').data('sel_bran');
            console.log(ar);
            $('#branch_select').select2('val', ar);
             	/* $("#branch_select").select2("val",(id_branch!=''?id_branch:''));
             	 */$(".overlay").css("display", "none");
        }
    });
}
//catlog module//HH
function set_modules_list() {
    my_Date = new Date();
    $("div.overlay").css('display', 'block');
    $.ajax({
        url: base_url + "index.php/settings/module/ajax?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var module = data.module;
            var access = data.access;
            //$('#total_module').text(.module.length);
            if (access.add == '0') {
                $('#add_modules').attr('disabled', 'disabled');
                //	$('#add_modules').style.visibility = 'hidden';
            }
            var oTable = $('#module_list').DataTable();
            oTable.clear().draw();
            if (module != null && module.length > 0) {
                oTable = $('#module_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": module,
                    "aoColumns": [{ "mDataProp": "id_module" },
                    { "mDataProp": "m_name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            module_url = base_url + "index.php/admin_usersms/module_status/" + 'm_app' + '/' + (row.m_app == 1 ? 0 : 1) + "/" + row.id_module;
                            return "<a href='" + module_url + "'><i class='fa " + (row.m_app == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.m_app == 1 ? 'green' : 'red') + "'></i></a>"
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            module_url = base_url + "index.php/admin_usersms/module_status/" + 'm_web' + '/' + (row.m_web == 1 ? 0 : 1) + "/" + row.id_module;
                            return "<a href='" + module_url + "'><i class='fa " + (row.m_web == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.m_web == 1 ? 'green' : 'red') + "'></i></a>"
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            module_url = base_url + "index.php/admin_usersms/module_status/" + 'm_active' + '/' + (row.m_active == 1 ? 0 : 1) + "/" + row.id_module;
                            return "<a href='" + module_url + "'><i class='fa " + (row.m_active == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.m_active == 1 ? 'green' : 'red') + "'></i></a>"
                        }
                    },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_module;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/module/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/module/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
                                '<li><a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li>';
                            return action_content;
                        }
                    }]
                });
            }
            $("div.overlay").css('display', 'none');
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//catlog module//HH  
function get_village_list() {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_settings/ajax_village_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "GET",
        success: function (data) {
            set_village_list(data);
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function set_village_list(data) {
    var customer = data.list;
    var access = data.access;
    var oTable = $('#village_list').DataTable();
    oTable.clear().draw();
    oTable = $('#village_list').dataTable({
        "bDestroy": true,
        "bInfo": true,
        "bFilter": true,
        "bSort": true,
        "dom": 'T<"clear">lfrtip',
        "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
        "aaData": customer,
        "order": [[0, "desc"]],
        "aoColumns": [
            { "mDataProp": "id_village" },
            { "mDataProp": "village_name" },
            { "mDataProp": "post_office" },
            { "mDataProp": "taluk" },
            {
                "mDataProp": function (row, type, val, meta) {
                    id = row.id_village;
                    edit_url = base_url + 'index.php/settings/village_form/edit/' + id;
                    delete_url = base_url + 'index.php/admin_settings/village_form/delete/' + id;
                    delete_confirm = '#confirm-delete';
                    action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                        (access.edit == '1' ? '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' : '') +
                        (access.delete == '1' ? '<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li>'
                            : '') + '</ul></div>';
                    return action_content;
                }
            }]
    });
}
//
$("#submit_maintn_tab,#submit_others_tab,#submit_sch_pay_tab,#submit_metal_tab,#submit_config_tab").click(function (e) {
    e.preventDefault();
    $("#tab_name").val($(this).val());
    $("#config_tab_name").val($(this).val());// added by durga 30/12/2022
    var acc_settings = $('input[name="general[schemeaccNo_displayFrmt]"]:checked').val();
    var rcpt_settings = $('input[name="general[receiptNo_displayFrmt]"]:checked').val();
    if (acc_settings == 2) {
        var selectedcount = $("#selected_field_list option").length;
        if (selectedcount == 0) {
            alert("select account fields for customization");
            return false;
        }
    }
    if (rcpt_settings == 2) {
        var selectedcount = $("#selected_receipt_field_list option").length;
        if (selectedcount == 0) {
            alert("select receipt fields for customization");
            return false;
        }
    }
    $("#gen_settings").submit();
});
//edit branch with image//HH    
/*$("#edit_branch_img").change( function(e){
        e.preventDefault(); 
         //alert(asd);
        validat_Image(this);
    });*/
function validat_Image() {
    var height = ($(this).height());
    var width = ($(this).width());
    if (arguments[0].id == 'edit_sch_branch_img') {
        var preview = $('#edit_sch_branch_img_preview');
    }
    else if (arguments[0].id == 'branch_img') {
        var preview = $('#sch_branch_img_preview');
    }
    if (arguments[0].files[0].size > 1048578) {
        alert('File size cannot be greater than 1 MB');
        arguments[0].value = "";
        preview.css('display', 'none');
    }
    else {
        var fileName = arguments[0].value;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        ext = ext.toLowerCase();
        if (ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg") {
            alert("Upload JPG or PNG Images only");
            arguments[0].value = "";
            preview.css('display', 'none');
        }
        /*if(width>960 && height>525)
        {
            alert("Width and height should be less than  960 * 525 ");
            arguments[0].value = "";
            preview.css('display','none');
        }*/
        else {
            var file = arguments[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.prop('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
                preview.css('display', '');
            }
            else {
                preview.prop('src', '');
                preview.css('display', 'none');
            }
        }
    }
}
/* $("#update_branch").on('click',function(){
        var branch = {
        description:  CKEDITOR.instances.description1.getData()
        };
        var id=$("#edit-id").val();			  
        update_branch(branch,id);
    });	
        $("#update_branch").on('click',function(){
        var branch = {
        description:  CKEDITOR.instances.description1.getData(),
            file : $("#edit_sch_branch_img")[0].files[0]
        };
        console.log(branch);
        var id=$("#edit-id").val();			  
        update_branch(branch,id);
    });*/
$("#branch_img,#edit_sch_branch_img").change(function (e) {
    e.preventDefault();
    validat_Image(this);
});
//edit branch with image//HH  
//Offline Rate List Admin with Branch Filter//HH
$('#branch_select').select2().on("change", function (e) {
    if (this.value != '') {
        var from_date = $('#offrate_list1').text();
        var To_date = $('#offrate_list2').text();
        var id_branch = $(this).val();
        get_offrate_list(from_date, To_date, id_branch);
    }
});
//Offline Rate List Admin with date picker//HH
function get_offrate_list(from_date = "", To_date = "", id_branch = '') {
    my_Date = new Date();
    postData = (from_date != '' && To_date != '' || id_branch != '' ? { 'from_date': from_date, 'To_date': To_date, 'id_branch': id_branch } : ''),
        $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_settings/offratelist_data?nocache=" + my_Date.getUTCSeconds(),
        data: (postData),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            $('#total_offrate').text(data.length);
            set_offrate_list(data);
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function set_offrate_list(data) {
    var ej_metalratehistory = data;
    var oTable = $('#offrate_list').DataTable();
    oTable.clear().draw();
    //console.log(kyc);
    oTable = $('#offrate_list').dataTable({
        "bDestroy": true,
        "bInfo": true,
        "bFilter": true,
        "bSort": true,
        "dom": 'T<"clear">lfrtip',
        "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
        "aaData": ej_metalratehistory,
        "order": [[0, "desc"]],
        "aoColumns": [{
            "mDataProp": function (row, type, val, meta) {
                chekbox = '<input type="checkbox" class="id_metalrates" name="id_metalrates[]" value="' + row.id_metalrates + '"/>'
                return chekbox + " " + row.id_metalrates;
            }
        },
        { "mDataProp": "updatetime" },
        { "mDataProp": "add_date" },
        { "mDataProp": "goldrate_22ct" },
        { "mDataProp": "silverrate_1gm" },
        { "mDataProp": "platinum_1g" },
        /*{ "mDataProp": "date" },
        { "mDataProp": "RATE" },
        { "mDataProp": "TIME" },
        { "mDataProp": "CREATEDDATETIME" },*/
        { "mDataProp": "id_branch" }
        ]
    });
    $("div.overlay").css("display", "none");
}
//Offline Rate List Admin with date picker//
// Terms For App from admin side add//HH
function get_terms_and_conditions() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/terms_and_conditions/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        type: "GET",
        dataType: 'json',
        cache: false,
        success: function (data) {
            set_terms_and_conditions(data.terms);
        }
    });
}
$('#select_type').on('change', function () {
    if (this.value != '') {
        $('#type').val(this.value);
    }
    else {
        $('#type').val('');
    }
});
function set_terms_and_conditions(data) {
    var oTable = $('#general_list').DataTable();
    oTable.clear().draw();
    oTable = $('#general_list').dataTable({
        "bDestroy": true,
        "bSort": true,
        "dom": 'T<"clear">lfrtip',
        "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
        "aaData": data,
        "aoColumns": [
            { "mDataProp": "id_general" },
            { "mDataProp": "name" },
            {
                "data": function (row, type, val, meta) {
                    id = row.id_general;
                    action_content = '<a  class="btn btn-primary btn-edit" role="button" href=' + base_url + "index.php/settings/terms_and_conditions/edit/" + id + '  ><i class="fa fa-edit" ></i> Edit</a> <a  class="btn btn-danger btn-del"  data-href=' + base_url + "index.php/settings/terms_and_conditions/delete/" + id + '  data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash" ></i> Delete</a>'
                    //action_content='<a  class="btn btn-primary btn-edit" role="button" href='+base_url+"index.php/settings/terms_and_conditions/edit/"+id+'  ><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href='+base_url+"settings/terms_and_conditions/delete/"+id+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                    return action_content;
                }
            },
        ]
    });
}
//Retail Settings //HH 
function set_retail_settings_list() {
    my_Date = new Date();
    $("div.overlay").css('display', 'block');
    $.ajax({
        url: base_url + "index.php/settings/retail_setting/ajax?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var retail_setting = data.retail_setting;
            var access = data.access;
            //$('#total_retail_settings').text(.retail_setting.length);
            if (access.add == '0') {
                $('#add_ret_setting').attr('disabled', 'disabled');
                //	$('#add_ret_setting').style.visibility = 'hidden';
            }
            var oTable = $('#ret_setting_list').DataTable();
            oTable.clear().draw();
            if (retail_setting != null && retail_setting.length > 0) {
                oTable = $('#ret_setting_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": retail_setting,
                    "aoColumns": [{ "mDataProp": "id_ret_settings" },
                    { "mDataProp": "name" },
                    { "mDataProp": "value" },
                    { "mDataProp": "description" },
                    { "mDataProp": "created_by" },
                    { "mDataProp": "updated_by" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_ret_settings;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/retail_setting/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/retail_setting/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
                                '<li><a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li>';
                            return action_content;
                        }
                    }]
                });
            }
            $("div.overlay").css('display', 'none');
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//Retail Settings //
//Profile Settings
$("input[name='profile[est_tag]']").click(function () {
    if ($("#est_tag").is(":checked")) {
        $('#est_tag').val(1);
    }
    else {
        $('#est_tag').val(0);
    }
});
$("input[name='profile[est_non_tag]']").click(function () {
    if ($("#est_non_tag").is(":checked")) {
        $('#est_non_tag').val(1);
    }
    else {
        $('#est_non_tag').val(0);
    }
});
$("input[name='profile[est_home_bill]']").click(function () {
    if ($("#est_home_bill").is(":checked")) {
        $('#est_home_bill').val(1);
    }
    else {
        $('#est_home_bill').val(0);
    }
});
$("input[name='profile[est_old_metal]']").click(function () {
    if ($("#est_old_metal").is(":checked")) {
        $('#est_old_metal').val(1);
    }
    else {
        $('#est_old_metal').val(0);
    }
});
$("input[name='profile[tag_transfer]']").click(function () {
    if ($("#tag_transfer").is(":checked")) {
        $('#tag_transfer').val(1);
    }
    else {
        $('#tag_transfer').val(0);
    }
});
$("input[name='profile[packaging_item_transfer]']").click(function () {
    if ($("#packaging_item_transfer").is(":checked")) {
        $('#packaging_item_transfer').val(1);
    }
    else {
        $('#packaging_item_transfer').val(0);
    }
});
$("input[name='profile[purchase_item_transfer]']").click(function () {
    if ($("#purchase_item_transfer").is(":checked")) {
        $('#purchase_item_transfer').val(1);
    }
    else {
        $('#purchase_item_transfer').val(0);
    }
});
$("input[name='profile[non_tag_transfer]']").click(function () {
    if ($("#non_tag_transfer").is(":checked")) {
        $('#non_tag_transfer').val(1);
    }
    else {
        $('#non_tag_transfer').val(0);
    }
});
$("input[name='profile[tag_details]']").click(function () {
    if ($("#tag_details").is(":checked")) {
        $('#tag_details').val(1);
    }
    else {
        $('#tag_details').val(0);
    }
});
$("input[name='profile[purchase_details]']").click(function () {
    if ($("#purchase_details").is(":checked")) {
        $('#purchase_details').val(1);
    }
    else {
        $('#purchase_details').val(0);
    }
});
$("input[name='profile[stone_details]']").click(function () {
    if ($("#stone_details").is(":checked")) {
        $('#stone_details').val(1);
    }
    else {
        $('#stone_details').val(0);
    }
});
$("input[name='profile[estimation]']").click(function () {
    if ($("#estimation").is(":checked")) {
        $('#estimation').val(1);
    }
    else {
        $('#estimation').val(0);
    }
});
$("input[name='profile[branch_transfer_details]']").click(function () {
    if ($("#branch_transfer_details").is(":checked")) {
        $('#branch_transfer_details').val(1);
    }
    else {
        $('#branch_transfer_details').val(0);
    }
});
$("input[name='profile[section_transfer_details]']").click(function () {
    if ($("#section_transfer_details").is(":checked")) {
        $('#section_transfer_details').val(1);
    }
    else {
        $('#section_transfer_details').val(0);
    }
});
$("input[name='profile[scan_details]']").click(function () {
    if ($("#scan_details").is(":checked")) {
        $('#scan_details').val(1);
    }
    else {
        $('#scan_details').val(0);
    }
});
$("input[name='profile[stock_issue_details]']").click(function () {
    if ($("#stock_issue_details").is(":checked")) {
        $('#stock_issue_details').val(1);
    }
    else {
        $('#stock_issue_details').val(0);
    }
});
//Profile Settings
function get_customer_list() {
    my_Date = new Date();
    var lower = $('#lower').val();
    var upper = $('#upper').val();
    console.log(lower);
    console.log(upper);
    $.ajax({
        url: base_url + "index.php/settings/import/ajax_customer_list/" + lower + "/" + upper + "?nocache=" + my_Date.getUTCSeconds(),
        data: { 'lower': lower, 'upper': upper },
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            set_import_customer_list(data);
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function set_import_customer_list(data) {
    var imported = data.imported;
    var oTable = $('#customer_imported_list').DataTable();
    oTable.clear().draw();
    if (imported != null && imported.length > 0) {
        oTable = $('#customer_imported_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "aaData": imported,
            "aoColumns": [{
                "mDataProp": function (row, type, val, meta) {
                    return "<label class='checkbox-inline'><input type='checkbox' name='account_id[]' value='" + row.id_customer + "'/> " + row.id_customer + "</label>"
                }
            },
            { "mDataProp": "name" },
            { "mDataProp": "mobile" },
            { "mDataProp": "email" }]
        });
    }
}
$(document).on('click', "#profession_list  a.btn-edit", function (e) {
    $("#ed_profession").val('');
    e.preventDefault();
    id = $(this).data('id');
    get_profession(id);
    $("#edit-id").val(id);
});
$("#update_profession").on('click', function () {
	var profession = $("#ed_profession").val().trim()
    var id = $("#edit-id").val();
	if (profession != '') {
        update_profession(profession, id);
	} else {
		$('#ed_profession').val('');
		$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Profession Name Should not be Empty ..." });
		$('#ed_profession').attr('placeholder', 'Enter a valid profession Name');
		$("#ed_profession").focus();
		return false;
    }
});
function set_profession_table() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/profession_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            var profession = data.data;
            var access = data.access;
            $('#total_profession').text(profession.length);
            if (access.add == '0') {
                $('#add_profession').attr('disabled', 'disabled');
            }
            else {
                $("#add_profession").on('click', function () {
					const profession = $('#profession').val().trim();
					if (profession != '') {
                        add_profession($('#profession').val());
                        $('#profession').val('');
                    }
					else {
						$.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>Profession Name Should not be Empty ..." });
						$('#profession').attr('placeholder', 'Enter a valid profession Name');
						$("#profession").val("");
						$("#profession").focus();
						return false;
					}
                });
            }
            var oTable = $('#profession_list').DataTable();
            oTable.clear().draw();
            if (profession != null && profession.length > 0) {
                oTable = $('#profession_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "aaData": profession,
                    "columnDefs":
                        [
                            {
                                targets: [0, 1],
                                className: 'dt-left'
                            },
							// code by jothika on 10-7-2025 [removing filter from column]
							{
								targets: [2],
								orderable: false,
								searchable: false
							}
                        ],
                    "aoColumns": [{ "mDataProp": "id_profession" },
                    { "mDataProp": "name" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_profession;
                            edit_target = (access.edit == '0' ? "" : "#confirm-edit");
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/profession/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = (access.edit == '1' ? '<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-edit" ></i> Edit</a>' : "") + (access.delete == '1' ? '<a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>' : "")
                            return action_content;
                        }
                    }]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function get_profession(id) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/settings/profession/edit/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            profession = data.name;
            $('#ed_profession').val(profession);
            // console.log(wt);
        }
    });
}
function add_profession(profession) {
    my_Date = new Date();
    $.ajax({
        data: { "profession": profession },
        url: base_url + "index.php/settings/profession/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function (data) {
            //$('#department').val('');
            window.location.reload(true);
        }
    });
}
function update_profession(profession, id) {
    my_Date = new Date();
    $.ajax({
        data: { "profession": profession },
        url: base_url + "index.php/settings/profession/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        cache: false,
        success: function () {
            //console.log(get_weight(id));
            window.location.reload(true);
            //set_table();
        }
    });
}
$('#ed_gst_number,#gst_number').on('keyup', function () {
    var gst_length = this.value.length;
    var gst_number = this.value;
    if (gst_length == 15) {
        checkgst(gst_number);
    }
});
function checkgst(gst_number) {
    var regexp = /^([0-9]{2}[a-zA-Z]{4}([a-zA-Z]{1}|[0-9]{1})[0-9]{4}[a-zA-Z]{1}([a-zA-Z]|[0-9]){3}){0,15}$/;
    if (!regexp.test(gst_number)) {
        $("#ed_gst_number").val("");
        $("#gst_number").val("");
        $.toaster({ priority: 'error', title: 'Warning!', message: '' + "</br>GST Number is not valid. It should be in this '11AAAAA1111Z1A1' format" });
        $('#ed_gst_number').attr('placeholder', 'ex:11AAAAA1111Z1A1');
        $('#gst_number').attr('placeholder', 'ex:11AAAAA1111Z1A1');
        $("#gst_number").focus();
        $("#ed_gst_number").focus();
    }
}
$('#pan,#ifsc_code,#acc_number,#short_code,#bank_name,#mode_name,#ed_profession,#profession,#ed_weight,#weight,#ed_clsfy,#clsfy,#branch,#account_name,#account_no').bind("cut copy paste", function (e) {
    e.preventDefault();
});
// Added by Durga Starts here 12.05.2023
function set_version_table(from_date = "", to_date = "") {
    console.log(from_date);
    console.log(to_date);
    $("div.overlay").css("display", "block");
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/settings/version/ajax?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        data: { 'from_date': from_date, 'to_date': to_date },
        success: function (data) {
            var version_data = data.data;
            console.log(version_data);
            if (version_data.length > 0) {
                var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>"
                    + version_data[0].client + "</span></b><b><span style='font-size:12pt;'></span></b></br>" +
                    "<div style='text-align: center;'><b><span style='font-size:15pt;'>" +
                    "Version Details Report</span></b><b><span style='font-size:12pt;'></span></b></br>" +
                    "<div style='text-align: center;'><b><span style='font-size:15pt;'>" +
                    "Report Range : " + from_date + " to " + to_date + "</span></b><b><span style='font-size:12pt;'></span></b></br>";
            }
            var access = data.access;
            $('#version_count').text(version_data.length);
            if (access.add == '0') {
                $('#add_version').attr('disabled', 'disabled');
            }
            var oTable = $('#version_list').DataTable();
            oTable.clear().draw();
            if (version_data != null && version_data.length > 0) {
                oTable = $('#version_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "scrollX": '100%',
                    "bSort": true,
                    "dom": 'lBfrtip',
                    "order": [[0, "desc"]],
                    "buttons": [
                        {
                            extend: 'print',
                            footer: true,
                            title: "",
                            messageTop: title,
                            customize: function (win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                        {
                            extend: 'excel',
                            footer: true,
                            title: "Version Details    " + from_date + "  to  " + to_date,
                        }
                    ],
                    "aaData": version_data,
                    "columnDefs": [
                        {
                            targets: [0, 1, 2, 3, 4],
                            className: 'dt-left'
                        },
                    ],
                    "aoColumns": [{ "mDataProp": "id_version" },
                    { "mDataProp": "version_no" },
                    { "mDataProp": "description" },
                    // { "mDataProp": "client" },
                    { "mDataProp": "date" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            id = row.id_version;
                            edit_url = (access.edit == '1' ? base_url + 'index.php/settings/version/edit/' + id : '#');
                            delete_url = (access.delete == '1' ? base_url + 'index.php/settings/version/delete/' + id : '#');
                            delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                            action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                                '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
                                '<li><a href="#" class=" btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li>';
                            return action_content;
                        }
                    }
                    ]
                });
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
// Added by Durga ends here 12.05.2023
function get_title(from_date, to_date, title) {
    var company_name = $('#company_name').val();
    var company_code = $('#company_code').val();
    var company_address1 = $('#company_address1').val();
    var company_address2 = $('#company_address2').val();
    var company_city = $('#company_city').val();
    var pincode = $('#pincode').val();
    var company_email = $('#company_email').val();
    var company_gst_number = $('#company_gst_number').val();
    var phone = $('#phone').val();
    var select_date = "<div style='text-align: center;'><b><span style='font-size:12pt;'>" + company_code + "</span></b></br>"
        + "<span style='font-size:11pt;'>" + company_address1 + "</span></br>"
        + "<span style='font-size:11pt;'>" + company_address2 + company_city + "-" + pincode + "</span></br>"
        + "<span style='font-size:11pt;'>GSTIN:" + company_gst_number + ", EMAIL:" + company_email + "</span></br>"
        + "<span style='font-size:11pt;'>Contact :" + phone + "</span></br>"
        + "<b><span style='font-size:15pt;'>" + title + "</span></b></br>"
        + "<span style=font-size:13pt;>Details &nbsp;&nbsp;From Date&nbsp;:&nbsp;" + from_date + " &nbsp;&nbsp;To Date&nbsp;:&nbsp;" + to_date + "</span><br>"
        + "<span style=font-size:11pt;>Print Taken On : " + moment().format("dddd, MMMM Do YYYY, h:mm:ss a")
        + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
        + "<span style=font-size:11pt;>Print Taken By : " + $('.hidden-xs').html() + "</span></div>";
    return select_date;
}
//Added by Durga Account and receipt number customization starts here 26-06-2023
function insertFormat() {
    var selected_field = document.getElementById("field_list").value;
    var selected_text = $("#field_list option:selected").text();
    if (selected_field) {
        $('#selected_field_list').append(`<option value="${selected_field}"> 
		${selected_text} 
   		</option>`);
        if (selected_field != '@@hyphen@@' && selected_field != '@@space@@') {
            $('#field_list option:selected').remove();
        }
    }
    else {
        alert("Select a value")
    }
    var allValues = $('#selected_field_list option').map(function () {
        return {
            value: $(this).val(),
            text: $(this).text().trim().replace(/\s+/g, ' ')
        };
    }).get();
    var arrayString = JSON.stringify(allValues);
    console.log(allValues);
    console.log(arrayString);
    $("#acc_format_hidden").val(arrayString);
    show_sample(allValues, 'ACCOUNT');
}
function insertReceiptFormat() {
    var selected_field = document.getElementById("field_receipt_list").value;
    var selected_text = $("#field_receipt_list option:selected").text();
    if (selected_field) {
        $('#selected_receipt_field_list').append(`<option value="${selected_field}"> 
		${selected_text} 
   		</option>`);
        if (selected_field != '@@hyphen@@' && selected_field != '@@space@@') {
            $('#field_receipt_list option:selected').remove();
        }
    }
    else {
        alert("Select a value")
    }
    var allValues = $('#selected_receipt_field_list option').map(function () {
        return {
            value: $(this).val(),
            text: $(this).text().trim().replace(/\s+/g, ' ')
        };
    }).get();
    var arrayString = JSON.stringify(allValues);
    console.log(allValues);
    console.log(arrayString);
    $("#rcpt_format_hidden").val(arrayString);
    show_sample(allValues, 'RECEIPT');
}
function revertFormat() {
    var selected_field = document.getElementById("selected_field_list").value;
    var selected_text = $("#selected_field_list option:selected").text();
    if (selected_field) {
        if (selected_field != '@@hyphen@@' && selected_field != '@@space@@') {
            $('#field_list').append(`<option value="${selected_field}"> 
				${selected_text} 
				</option>`);
        }
        $('#selected_field_list option:selected').remove();
    }
    else {
        alert("Select a value")
    }
    var allValues = $('#selected_field_list option').map(function () {
        return {
            value: $(this).val(),
            text: $(this).text().trim().replace(/\s+/g, ' ')
        };
    }).get();
    var arrayString = JSON.stringify(allValues);
    console.log(allValues);
    $("#acc_format_hidden").val(arrayString);
    show_sample(allValues, 'ACCOUNT');
    console.log(arrayString);
}
function revertReceiptFormat() {
    var selected_field = document.getElementById("selected_receipt_field_list").value;
    var selected_text = $("#selected_receipt_field_list option:selected").text();
    if (selected_field) {
        if (selected_field != '@@hyphen@@' && selected_field != '@@space@@') {
            $('#field_receipt_list').append(`<option value="${selected_field}"> 
			${selected_text} 
			</option>`);
        }
        $('#selected_receipt_field_list option:selected').remove();
    }
    else {
        alert("Select a value")
    }
    var allValues = $('#selected_receipt_field_list option').map(function () {
        return {
            value: $(this).val(),
            text: $(this).text().trim().replace(/\s+/g, ' ')
        };
    }).get();
    var arrayString = JSON.stringify(allValues);
    console.log(allValues);
    $("#rcpt_format_hidden").val(arrayString);
    console.log(arrayString);
    show_sample(allValues, 'RECEIPT');
}
function show_sample(format_array, type) {
    if (format_array.length > 0) {
        var sample_text = '';
        $.each(format_array, function (key, item) {
            switch (item.text) {
                case 'Branch Code(BB)':
                    sample_text += 'BB';
                    break;
                case 'Receipt Number(232)':
                    sample_text += '232';
                    break;
                case 'Group Code(GG)':
                    sample_text += 'GG';
                    break;
                case 'Scheme Account Number(122)':
                    sample_text += '122';
                    break;
                case 'Receipt Year(23)':
                    sample_text += '23';
                    break;
                case 'Financial Year(23)':
                    sample_text += '23';
                    break;
                case 'Scheme Code(SSS)':
                    sample_text += 'SSS';
                    break;
                case 'Hyphen(-)':
                    sample_text += '-';
                    break;
                case 'Space':
                    sample_text += ' ';
                    break;
            }
            if (type == 'ACCOUNT') {
                $("#display_acc_span").text(sample_text);
            }
            else {
                $("#display_rcpt_span").text(sample_text);
            }
        });
    }
    else {
        if (type == 'ACCOUNT') {
            $("#display_acc_span").text('');
        }
        else {
            $("#display_rcpt_span").text('');
        }
    }
}
/* function load_rcpt_field() {
    $.ajax({
        type: "POST",
        url: base_url + "index.php/admin_settings/get_custom_receipt_fields",
        dataType: 'json',
        sync: false,
        success: function (data) {
            receipt_fields = data.receipt_num_fields[0];
            if (receipt_fields.length > 0) {
                $.each(receipt_fields, function (key, field) {
                    $('#field_receipt_list').append(
                        $("<option></option>")
                            .attr("value", '@@' + field.value + '@@')
                            .text(field.text)
                    );
                });
            }
            console.log(data);
        }
    });
} */
$("#quick_link").on('click', function () {
    if ($("input[name='link_id[]']:checked").length > 0) {
        var selected = [];
        $("#menu_list tbody tr").each(function (index, value) {
            if ($(value).find("input[name='link_id[]']:checked").is(":checked")) {
                var link_id = $(value).find(".link_id").val();
                selected.push(link_id);
            }
        });
        var req_data = { 'link_id': selected }; // Send the array as an object
        menu_data(req_data);
    }
    else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please select Any Id to proceed..' });
    }
});
function menu_data(req_data) {
    $.ajax({
        url: base_url + 'index.php/admin_settings/quick_link',
        type: 'POST',
        data: req_data,
        dataType: 'json',
        success: function (data) {
            if (data.status) {
                $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
            }
            else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
            }
            window.location.reload();
        },
    });
}
$("#quick_revert_link").on('click', function () {
    if ($("input[name='link_id[]']:checked").length > 0) {
        var selected = [];
        $("#menu_list tbody tr").each(function (index, value) {
            if ($(value).find("input[name='link_id[]']:checked").is(":checked")) {
                var link_id = $(value).find(".link_id").val();
                selected.push(link_id);
            }
        });
        var req_revert_data = { 'link_id': selected }; // Send the array as an object
        menu_revert_data(req_revert_data);
    } else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please select Any Id to proceed..' });
    }
});
function menu_revert_data(req_revert_data) {
    $.ajax({
        url: base_url + 'index.php/admin_settings/quick_link_revert',
        type: 'POST',
        data: req_revert_data,
        dataType: 'json',
        success: function (data) {
            if (data.status) {
                $.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });
            }
            else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
            }
            window.location.reload();
        },
    });
}
//Added by Durga Account and receipt number customization ends here 26-06-2023
//code added by Santhosh on 15-12-2023 starts....
document.addEventListener('DOMContentLoaded', function () {
    updateFieldList();
    document.getElementById('3').addEventListener('change', function () {
        updateFieldList();
    });
});
function updateFieldList() {
    // alert(1);
    var selectedValue = document.getElementById('3').value;
    var fieldList = document.getElementById('field_list');
    fieldList.innerHTML = '';
    switch (selectedValue) {
        case '0':
            // Common
            addOption('acc_num', 'Scheme Account Number(122)');
            break;
        case '1':
            // Common with branch wise
            addOption('acc_num', 'Scheme Account Number(122)');
            addOption('hyphen', 'Hyphen(-)');
            addOption('br_code', 'Branch Code(BB)');
            break;
        case '2':
            // Scheme Wise
            addOption('sch_code', 'Scheme Code(SSS)');
            break;
        case '3':
            // Scheme wise With Branch Wise
            addOption('sch_code', 'Scheme Code(SSS)');
            addOption('hyphen', 'Hyphen(-)');
            addOption('br_code', 'Branch Code(BB)');
            break;
        case '4':
            // Financial Year Wise
            addOption('fin_yr', 'Financial Year(23)');
            break;
        case '5':
            // Financial Year with Scheme Wise
            addOption('fin_yr', 'Financial Year(23)');
            addOption('hyphen', 'Hyphen(-)');
            addOption('sch_code', 'Scheme Code(SSS)');
            break;
        case '6':
            // Financial Year with Scheme & Branch Wise
            addOption('fin_yr', 'Financial Year(23)');
            addOption('sch_code', 'Scheme Code(SSS)');
            addOption('hyphen', 'Hyphen(-)');
            addOption('br_code', 'Branch Code(BB)');
            break;
        default:
            addOption('br_code', 'Branch Code(BB)');
            addOption('sch_code', 'Scheme Code(SSS)');
            addOption('grp_code', 'Group Code(GG)');
            addOption('fin_yr', 'Financial Year(23)');
            addOption('acc_num', 'Scheme Account Number(122)');
            addOption('hyphen', 'Hyphen(-)');
            addOption('space', 'Space');
            break;
    }
}
function addOption(value, text) {
    var option = document.createElement('option');
    option.value = '@@' + value + '@@';
    option.text = text;
    document.getElementById('field_list').add(option);
}
document.addEventListener('DOMContentLoaded', function () {
    updateReceiptFieldList();
    document.querySelectorAll('input[name="general[scheme_wise_receipt]"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            updateReceiptFieldList();
        });
    });
});
function updateReceiptFieldList() {
    var selectedValue = document.querySelector('input[name="general[scheme_wise_receipt]"]:checked').value;
    var receiptFieldList = document.getElementById('field_receipt_list');
    receiptFieldList.innerHTML = '';
    // Add options based on the selected value
    switch (selectedValue) {
        case '1':
            // Common
            addOption1('acc_num', 'Scheme Account Number(122)');
            break;
        case '2':
            // Branch-wise
            addOption1('br_code', 'Branch Code(BB)');
            addOption('hyphen', 'Hyphen(-)');
            break;
        case '3':
            // Scheme-wise
            addOption1('sch_code', 'Scheme Code(SSS)');
            addOption1('hyphen', 'Hyphen(-)');
            break;
        case '4':
            // Scheme with Branch-wise
            addOption1('sch_code', 'Scheme Code(SSS)');
            addOption1('hyphen', 'Hyphen(-)');
            addOption1('br_code', 'Branch Code(BB)');
            break;
        case '5':
            // Financial Year-wise
            addOption1('fin_yr', 'Financial Year(23)');
            addOption('hyphen', 'Hyphen(-)');
            break;
        case '6':
            // Financial Year with Scheme & Branch-wise
            addOption1('fin_yr', 'Financial Year(23)');
            addOption1('sch_code', 'Scheme Code(SSS)');
            addOption1('hyphen', 'Hyphen(-)');
            addOption1('br_code', 'Branch Code(BB)');
            break;
        default:
            addOption1('br_code', 'Branch Code(BB)');
            addOption1('sch_code', 'Scheme Code(SSS)');
            addOption1('grp_code', 'Group Code(GG)');
            addOption1('fin_yr', 'Financial Year(23)');
            addOption1('acc_num', 'Scheme Account Number(122)');
            addOption1('hyphen', 'Hyphen(-)');
            addOption1('space', 'Space');
            break;
    }
}
function addOption1(value, text) {
    var option = document.createElement('option');
    option.value = '@@' + value + '@@';
    option.text = text;
    document.getElementById('field_receipt_list').add(option);
}
//code added by Santhosh on 15-12-2023 end
// Notification Listing --Gopal--17-01-2024 ---Start
function set_notification_table() {
    $.ajax({
        url: base_url + "index.php/settings/notification/ajaxlist?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            $('#total_notification').text(data.length);
            var oTable = $('#notification_list').DataTable();
            oTable.clear().draw();
            if (data != null && data.length > 0) {
                oTable = $('#notification_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "dom": 'lBfrtip',
                    "buttons": [
                        {
                            extend: 'print',
                            footer: true,
                            title: "",
                            customize: function (win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                        {
                            extend: 'excel',
                            footer: true,
                            title: "Notification List ",
                        }
                    ],
                    "aaData": data,
                    "aoColumns": [{ "mDataProp": "id_notification" },
                    { "mDataProp": "noti_name" },
                    { "mDataProp": "noti_general" },
                    { "mDataProp": "noti_sub" },
                    { "mDataProp": "noti_msg" },
                    { "mDataProp": "noti_footer" },
                    { "mDataProp": "send_notif_on" },
                    { "mDataProp": "send_daily_from" },
                    {
                        "mDataProp": function (row, type, val, meta) {
                            active_url = base_url + "index.php/admin_settings/update_notification_status/" + (row.active == 1 ? 0 : 1) + "/" + row.id_notification;
                            return "<a href='" + active_url + "'><i class='fa " + (row.active == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.active == 1 ? 'green' : 'red') + "'></i></a>"
                        }
                    },
                    ]
                });
            }
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//Upi Payment Gateway List 
function set_upi_paymentgateway_list(id_branch = '') {
    my_Date = new Date();
    $.ajax({
        data: { 'id_branch': id_branch },
        url: base_url + "index.php/settings/upi_payment_gateway_list?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        success: function (data) {
            set_upi_gateway_list(data);
        }
    });
}
function set_upi_gateway_list(data) {
    var customer = data;
    var oTable = $('#upi_paymentgateway_list').DataTable();
    oTable.clear().draw();
    oTable = $('#upi_paymentgateway_list').dataTable({
        "bDestroy": true,
        "bSort": true,
        "dom": 'T<"clear">lfrtip',
        "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
        "aaData": data,
        "aoColumns": [
            { "mDataProp": "id_offline_api" },
            { "mDataProp": "upi_name" },
            { "mDataProp": "upi_id" },
            {
                "mDataProp": function (row, type, val, meta) {
                    id = row.upi_image;
                    if (row.upi_image != "" && row.upi_image != null) {
                        img = base_url + "assets/img/gateway/upi_data/" + row.upi_image;
                    }
                    else {
                        img = base_url + "assets/img/no_image.png";
                    }
                    action_content = '<a href="#"><img src= ' + img + ' width=50px;" height="50px;"></a>';
                    return action_content;
                }
            },
            { "mDataProp": "status" },
            {
                "data": function (row, type, val, meta) {
                    id = row.id_offline_api;
                    action_content = '<a href="#" class="btn btn-primary btn-edit" role="button" data-toggle="modal" data-id=' + id + '  data-target="#confirm-edit"><i class="fa fa-edit" ></i> Edit</a> <a href="#" class="btn btn-danger btn-del" data-href=' + base_url + "index.php/settings/upi_payment_gateway_list/delete/" + id + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>'
                    return action_content;
                }
            },
        ]
    });
}
$("#add_upi_paymentgateway").on('click', function () {
    var file = $("#upi_gateway_img")[0].files[0];
    add_upi_payment_gateway($('#upi_gateway_name').val(), $('#upi_gateway_id').val(), $('#upi_gateway_active').val(), file);
});
$(document).on('click', "#upi_paymentgateway_list a.btn-edit", function (e) {
    $("#ed_upi_gateway_name").val('');
    $("#ed_upi_gateway_id").val('');
    e.preventDefault();
    id = $(this).data('id');
    console.log(id, 'yyyy');
    get_upi_paymentgateway(id);
    $("#edit-id").val(id);
});
function get_upi_paymentgateway(id) {
    my_Date = new Date();
    $.ajax({
        type: "POST",
        data: { 'id_offline_api': id },
        url: base_url + "index.php/settings/upi_payment_gateway_list/edit?nocache=" + my_Date.getUTCSeconds(),
        cache: false,
        dataType: "JSON",
        success: function (data) {
            console.log(data, 'ttttttttttttt');
            upi_gateway_name = data.upi_name;
            upi_gateway_id = data.upi_id;
            upi_gateway_active = data.status;
            upi_image = data.upi_image;
            $('#ed_upi_gateway_name').val(upi_gateway_name);
            $('#ed_upi_gateway_id').val(upi_gateway_id);
            if (upi_gateway_active == 1) {
                $('#ed_upi_gateway_active').prop('checked', true);
                $('#ed_upi_gateway_inactive').prop('checked', false);
                $('#ed_upi_gateway').val(1);
            }
            else {
                $('#ed_upi_gateway_active').prop('checked', false);
                $('#ed_upi_gateway_inactive').prop('checked', true);
                $('#ed_upi_gateway').val(0);
            }
            if (upi_image != "" && upi_image != null) {
                var img = base_url + "assets/img/gateway/upi_data/" + data.upi_image;
                $("#edit_upi_gateway_img_preview").attr('src', img);
            }
            else {
                var no_img = base_url + "assets/img/no_image.png";
                $("#edit_upi_gateway_img_preview").attr('src', no_img);
            }
        }
    });
}
function add_upi_payment_gateway(upi_gateway_name, upi_gateway_id, upi_gateway_active, file) {
    my_Date = new Date();
    console.log(file);
    var form_data = new FormData();
    form_data.append('upi_gateway_name', upi_gateway_name);
    form_data.append('upi_gateway_id', upi_gateway_id);
    form_data.append('upi_gateway_active', upi_gateway_active);
    form_data.append('file', file);
    console.log(form_data);
    $.ajax({
        data: form_data,
        url: base_url + "index.php/settings/upi_payment_gateway_list/add?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        success: function (data) {
            location.reload(true);
        }
    });
}
$("input[name='upi_gateway_active']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#upi_gateway_active').val(1);
    }
    else {
        $('#upi_gateway_active').val(0);
    }
});
$("input[name='ed_upi_gateway']:radio").on('change', function () {
    if ($(this).val() == 1) {
        $('#ed_upi_gateway').val(1);
    }
    else {
        $('#ed_upi_gateway').val(0);
    }
});
$("#update_upi_payment_gateway").on('click', function () {
    var file = $("#edit_upi_gateway_img")[0].files[0];
    var id = $("#edit-id").val();
    update_upi_payment_gateway($("#ed_upi_gateway_name").val(), $("#ed_upi_gateway_id").val(), $("#ed_upi_gateway").val(), file);
});
function update_upi_payment_gateway(upi_gateway_name, upi_gateway_id, ed_upi_gateway, file) {
    my_Date = new Date();
    var form_data = new FormData();
    form_data.append('upi_gateway_name', upi_gateway_name);
    form_data.append('upi_gateway_id', upi_gateway_id);
    form_data.append('upi_gateway_active', ed_upi_gateway);
    form_data.append('file', file);
    form_data.append('id', id);
    console.log(form_data, 'uuuuuuuuuuuuuu');
    $.ajax({
        data: form_data,
        url: base_url + "index.php/settings/upi_payment_gateway_list/update/" + id + "?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        cache: false,
        success: function () {
            window.location.reload(true);
            //set_table();
        }
    });
}
$("#edit_upi_gateway_img").change(function () {
    event.preventDefault();
    console.log(this);
    validate_upi_Image(this);
});
function validate_upi_Image() {
    if (arguments[0].id == 'edit_upi_gateway_img') {
        var preview = $('#edit_upi_gateway_img_preview');
    }
    else if (arguments[0].id == 'upi_gateway_img') {
        var preview = $('#upi_gateway_img_preview');
    }
    if (arguments[0].files[0].size > 1048576) {
        alert('File size cannot be greater than 1 MB');
        arguments[0].value = "";
        preview.css('display', 'none');
    }
    else {
        var fileName = arguments[0].value;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        ext = ext.toLowerCase();
        if (ext != "jpg" && ext != "png" && ext != "jpeg" && ext != "svg") {
            alert("Upload JPG or PNG Images only");
            arguments[0].value = "";
            preview.css('display', 'none');
        }
        else {
            var file = arguments[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.prop('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
                preview.css('display', '');
            }
            else {
                preview.prop('src', '');
                preview.css('display', 'none');
            }
        }
    }
}
$("#upi_gateway_img").change(function () {
    event.preventDefault();
    console.log(this);
    validate_upi_Image(this);
});
// Notification Listing --Gopal--17-01-2024 ---End
$("#ret_product_img").change(function () {
    event.preventDefault();
    validateImage(this);
});
function validateImage() {
    switch (arguments[0].id) {
        case 'category_img':
            var preview = $('#category_img_preview');
            break;
        case 'sub_category_img':
            var preview = $('#sub_category_img_preview');
            break;
        case 'default_prod_img':
            var preview = $('#default_img_preview');
            break;
        case 'ret_product_img':
            var preview = $('#ret_product_img_preview');
            break;
        default:
            console.log(arguments[0].id);
            var preview = $('#' + arguments[0].id + '_Preview');
            break;
    }
    if (arguments[0].files[0].size > 1048576) {
        alert('File size cannot be greater than 1 MB');
        arguments[0].value = "";
        preview.css('display', 'none');
    }
    else {
        var fileName = arguments[0].value;
        var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
        ext = ext.toLowerCase();
        if (ext != "jpg" && ext != "png" && ext != "jpeg") {
            alert("Upload JPG or PNG Images only");
            arguments[0].value = "";
            preview.css('display', 'none');
        }
        else {
            var file = arguments[0].files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.prop('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
                preview.css('display', '');
            }
            else {
                preview.prop('src', '');
                preview.css('display', 'none');
            }
        }
    }
}
//preview selected images
function readURL(input, preview) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#' + preview).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
 // Code By Karthikai Kumaran for show social media links ON 16/10/2025
// Helper function for Social Media URLs
function toggleSocialMediaUrls() {
    $('.social-toggle').each(function() {
        var isChecked = $(this).is(':checked') ? 1 : 0;
        if(isChecked) {
            $('#urlsForSocialMediaLink').show();
        } else {
            $('#urlsForSocialMediaLink').hide();
        }
    });
}
function validateUrl(id, pattern, platformName) {
        const $input = $(id);
        const url = $input.val().trim();
        // Clear previous message
        $input.next('.help-block').text('');
        // Skip empty values (optional)
        if (url === '') return true;
    // e.preventDefault();
        // Validate format
        if (!pattern.test(url)) {
            $input.next('.help-block').text('⚠️ Please enter a valid ' + platformName + ' URL');
            $input.addClass('is-invalid');
            $input.val('');
            $input.focus();
            return false;
        } else {
            $input.removeClass('is-invalid');
            $input.next('.help-block').text('');
            return true;
        }
    }
$('#ledger_name').on('paste', function (e) {
    var data = (e.originalEvent || e).clipboardData.getData('text'), self = $(this);
    if (/[^A-Za-z0-9-\s]/.test(data)) {
        e.preventDefault();
        self.val(data);
        $.toaster({ priority: 'danger', title: 'Error', message: 'Ledger Name should contain only alphabets, numbers, hyphen and space' });
        setTimeout(function () { self.val(''); }, 500);
    }
});

$('#opening_balance, #min_balance').on('paste', function (e) {
    var data = (e.originalEvent || e).clipboardData.getData('text'), self = $(this);
    if (parseFloat(data) < 0 || data.includes('-')) {
        e.preventDefault();
        self.val(data);
        $.toaster({ priority: 'danger', title: 'Error', message: (this.id === 'opening_balance' ? 'Opening' : 'Minimum') + ' Balance cannot be negative' });
        setTimeout(function () { self.val(''); }, 500);
    }
});
// Ledger Form Submission
$('#save_ledger').on('click', function (e) {
    e.preventDefault();
    
    var ledger_name = $('#ledger_name').val().trim();
    var opening_balance = $('#opening_balance').val();
    var opening_date = $('#opening_date').val();
    var min_balance = $('#min_balance').val();
    var bank_ids = $('#bank_ids').val();
    var paymode_ids = $('#paymode_ids').val();
    var id_ledger = $('#id_ledger').val();

    if (ledger_name === '' && (opening_balance === '' || opening_balance == '0.00') && opening_date === '' ) {
        $.toaster({ priority: 'danger', title: 'Error', message: 'Please fill the mandatory fields' });
        return false;
    }

    if (ledger_name === '') {
        $.toaster({ priority: 'danger', title: 'Error', message: 'Ledger Name is required' });
        $('#ledger_name').focus();
        return false;
    }

    var nameRegex = /^[A-Za-z0-9-\s]+$/;
    if (!nameRegex.test(ledger_name)) {
        $.toaster({ priority: 'danger', title: 'Error', message: 'Ledger Name should contain only alphabets, numbers, hyphen and space' });
        $('#ledger_name').focus();
        return false;
    }

    if (opening_balance === '') {
        $.toaster({ priority: 'danger', title: 'Error', message: 'Opening Balance is required' });
        $('#opening_balance').focus();
        return false;
    }
    
    if (parseFloat(opening_balance) < 0 || opening_balance.toString().includes('-')) {
        $.toaster({ priority: 'danger', title: 'Error', message: 'Opening Balance cannot be negative' });
        $('#opening_balance').focus();
        return false;
    }

    if (opening_date === '') {
        $.toaster({ priority: 'danger', title: 'Error', message: 'Opening Date is required' });
        $('#opening_date').focus();
        return false;
    }

    if (min_balance !== '' && (parseFloat(min_balance) < 0 || min_balance.toString().includes('-'))) {
        $.toaster({ priority: 'danger', title: 'Error', message: 'Minimum Balance cannot be negative' });
        $('#min_balance').focus();
        return false;
    }

    save_ledger_data(ledger_name, opening_balance, opening_date, min_balance, bank_ids, paymode_ids, id_ledger);
});
function save_ledger_data(ledger_name, opening_balance, opening_date, min_balance, bank_ids, paymode_ids, id) {
    var my_Date = new Date();
    var form_data = new FormData();
    form_data.append('ledger_name', ledger_name);
    form_data.append('opening_balance', opening_balance);
    form_data.append('opening_date', opening_date);
    form_data.append('min_balance', min_balance);
    // Append array fields
    if (bank_ids && Array.isArray(bank_ids)) {
        $.each(bank_ids, function (i, val) { form_data.append('bank_ids[]', val); });
    }
    if (paymode_ids && Array.isArray(paymode_ids)) {
        $.each(paymode_ids, function (i, val) { form_data.append('paymode_ids[]', val); });
    }
    var url = (ctrl_page[1] == 'ledger' && ctrl_page[2] == 'add'
        ? base_url + "index.php/settings/ledger/Save?nocache="
        : base_url + "index.php/settings/ledger/Update/" + id + "?nocache=");
    var submitBtn = $('#save_ledger');
    var originalBtnText = submitBtn.text();
    $.ajax({
        url: url + my_Date.getUTCSeconds(),
        type: 'POST',
        data: form_data,
        dataType: 'json',
        cache: false,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status === 'success') {
                $.toaster({ priority: 'success', title: response.title, message: response.message });
                setTimeout(function () {
                    window.location.href = response.redirect;
                }, 1500);
            } else {
                $.toaster({ priority: 'error', title: response.title, message: response.message });
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        }
    });
}

// =============================================
// New KYC Settings - JS Behaviour
// =============================================
// function initNewKycSettings() {
    // --- On page load: restore visibility state ---
$(document).ready(function () {
    var kycRequired = $('input[name="kyc[kyc_required]"]:checked').val();
    if (kycRequired == '1') {
        $('#kyc_settings_block').show();
    } else {
        $('#kyc_settings_block').hide();
    }

    var kycMode = $('input[name="kyc[kyc_mode]"]:checked').val();
    if (kycMode == '0') {
        $('#kyc_scheme_rules_block').show();
        $('#kyc_customer_rules_block').hide();
    } else if (kycMode == '1') {
        $('#kyc_scheme_rules_block').hide();
        $('#kyc_customer_rules_block').show();
    } else {
        $('#kyc_scheme_rules_block').hide();
        $('#kyc_customer_rules_block').hide();
    }

    var kycInteg = $('input[name="kyc[kyc_integration_type]"]:checked').val();
    if (kycInteg == '1') {
        $('#kyc_auto_api_block').show();
        $('#kyc_verification_type_block').hide();
    } else {
        $('#kyc_auto_api_block').hide();
        $('#kyc_verification_type_block').show();
    }

    // --- Load KYC master documents from API ---
    loadKycDocs();

    // --- Load Schemes from API ---
    loadKycSchemes();

    // --- 1. KYC Required change ---
    $('input[name="kyc[kyc_required]"]').on('change', function () {
        if ($(this).val() == '1') {
            
            $('#kyc_settings_block').slideDown();
        } else {
            $('#kyc_settings_block').slideUp();
        }
    });

    // --- 2. KYC Mode change ---
    $('input[name="kyc[kyc_mode]"]').on('change', function () {
        if ($(this).val() == '0') {
            $('#kyc_scheme_rules_block').slideDown();
            $('#kyc_customer_rules_block').slideUp();
        } else if ($(this).val() == '1') {
            $('#kyc_scheme_rules_block').slideUp();
            $('#kyc_customer_rules_block').slideDown();
        } else {
            $('#kyc_scheme_rules_block').slideUp();
            $('#kyc_customer_rules_block').slideUp();
        }
    });

    // --- 3. Integration Type change ---
    $('input[name="kyc[kyc_integration_type]"]').on('change', function () {
        if ($(this).val() == '1') {
            $('#kyc_auto_api_block').slideDown();
            $('#kyc_verification_type_block').slideUp();
        } else {
            $('#kyc_auto_api_block').slideUp();
            $('#kyc_verification_type_block').slideDown();
        }
    });
    // --- 4. Add scheme-rule row ---
    $('#kyc_add_scheme_row').on('click', function () {
        var newRow = '<tr class="kyc_scheme_row">' +
            '<td>' +
                '<select name="kyc_rule_scheme_new[]" class="form-control kyc_rule_scheme_select input-sm" multiple="multiple" style="width:100%;">' +
                '</select>' +
            '</td>' +
            '<td>' +
                '<select name="kyc_rule_docs_new[]" class="form-control kyc_rule_docs_select input-sm" multiple="multiple" style="width:100%;">' +
                '</select>' +
            '</td>' +
            '<td>' +
                '<select name="kyc_rule_when[]" class="form-control kyc_rule_when_select input-sm">' +
                    '<option value="0">Account Opening</option>' +
                    '<option value="1">During Payment</option>' +
                    '<option value="2">Account Closing</option>' +
                '</select>' +
            '</td>' +
            '<td class="kyc_logic_cell">' +
                '<select name="kyc_rule_logic[]" class="form-control kyc_rule_logic_select input-sm">' +
                    '<option value="0">All Required</option>' +
                    '<option value="1">At Least One</option>' +
                '</select>' +
            '</td>' +
            '<td class="kyc_type_cell" style="display:none;">' +
                '<select name="kyc_rule_type[]" class="form-control kyc_rule_type_select input-sm">' +
                    '<option value="0">Total Amount</option>' +
                    '<option value="1">Installment Count</option>' +
                '</select>' +
            '</td>' +
            '<td class="kyc_amount_cell" style="display:none;"><input type="text" name="kyc_rule_amount[]" class="form-control input-sm" placeholder="..."></td>' +
            '<td style="text-align:center;"><button type="button" class="btn btn-xs btn-danger kyc_remove_row"><i class="fa fa-trash"></i></button></td>' +
        '</tr>';
        $('#kyc_scheme_rows_container').append(newRow);

        // Load schemes into the newly added select
        var $newSchemeSelect = $('#kyc_scheme_rows_container tr:last .kyc_rule_scheme_select');
        loadKycSchemes($newSchemeSelect, '');

        // Load KYC docs into the newly added docs select
        var $newDocsSelect = $('#kyc_scheme_rows_container tr:last .kyc_rule_docs_select');
        loadKycDocs($newDocsSelect, []);
    });

    // --- 5. When dropdown change: show/hide Type + Amount columns ---
    $(document).on('change', '.kyc_rule_when_select', function () {
        var $row = $(this).closest('tr');
        if ($(this).val() == '1') {
            $row.find('.kyc_type_cell').show();
            $row.find('.kyc_amount_cell').show();
        } else {
            $row.find('.kyc_type_cell').hide();
            $row.find('.kyc_amount_cell').hide();
            $row.find('.kyc_amount_cell input').val('');
        }
    });

    // --- 6. Remove scheme-rule row ---
    $(document).on('click', '.kyc_remove_row', function () {
        $(this).closest('tr').remove();
        updateKycSchemeRulesHidden();
    });

    // --- 7. Add customer-rule row ---
    $('#kyc_add_customer_row').on('click', function () {
        var custRow = '<tr class="kyc_customer_row">' +
            '<td>' +
                '<select name="kyc_cust_rule_attr_new[]" class="form-control kyc_cust_rule_attr_select input-sm" multiple="multiple" style="width:100%;">' +
                '</select>' +
            '</td>' +
            '<td>' +
                '<select name="kyc_cust_rule_when[]" class="form-control kyc_cust_rule_when_select input-sm">' +
                    '<option value="0">On Creation</option>' +
                    '<option value="1">Exceeds Amount</option>' +
                '</select>' +
            '</td>' +
            '<td class="kyc_cust_amount_cell" style="display:none;"><input type="text" name="kyc_cust_rule_amount[]" class="form-control input-sm" placeholder="0.00"></td>' +
            '<td class="kyc_cust_logic_cell">' +
                '<select name="kyc_cust_rule_logic[]" class="form-control kyc_cust_rule_logic_select input-sm">' +
                    '<option value="0">All Required</option>' +
                    '<option value="1">At Least One</option>' +
                '</select>' +
            '</td>' +
            '<td style="text-align:center;"><button type="button" class="btn btn-xs btn-danger kyc_remove_customer_row"><i class="fa fa-trash"></i></button></td>' +
        '</tr>';
        $('#kyc_customer_rows_container').append(custRow);

        // Load KYC docs into the newly added attr select
        var $newAttrSelect = $('#kyc_customer_rows_container tr:last .kyc_cust_rule_attr_select');
        loadKycDocs($newAttrSelect, '');
    });

    // --- 8. Customer rule When change: show/hide Amount ---
    $(document).on('change', '.kyc_cust_rule_when_select', function () {
        var $row = $(this).closest('tr');
        if ($(this).val() == '1') {
            $row.find('.kyc_cust_amount_cell').show();
        } else {
            $row.find('.kyc_cust_amount_cell').hide();
            $row.find('.kyc_cust_amount_cell input').val('');
        }
    });

    // --- 9. Remove customer-rule row ---
    $(document).on('click', '.kyc_remove_customer_row', function () {
        $(this).closest('tr').remove();
        updateKycCustomerRulesHidden();
    });

    // --- 10. Before form submit: serialize both scheme + customer rules ---
    $('form').on('submit', function () {
        updateKycSchemeRulesHidden();
        updateKycCustomerRulesHidden();
    });
});

function updateKycSchemeRulesHidden() {
    var rules = [];
    $('#kyc_scheme_rows_container .kyc_scheme_row').each(function () {
        var scheme = $(this).find('.kyc_rule_scheme_select').val() || [];
        var kyc_docs = $(this).find('.kyc_rule_docs_select').val() || [];
        var when = $(this).find('select[name="kyc_rule_when[]"]').val();
        var type = $(this).find('select[name="kyc_rule_type[]"]').val();
        var amount = $(this).find('input[name="kyc_rule_amount[]"]').val();
        var logic = $(this).find('.kyc_rule_logic_select').val();
        if (scheme.length > 0) {
            rules.push({ scheme: scheme, kyc_docs: kyc_docs, when: when, type: type || '', amount: amount || '', logic: logic || 0 });
        }
    });
    $('#kyc_scheme_rules_hidden').val(JSON.stringify(rules));
}

function updateKycCustomerRulesHidden() {
    var rules = [];
    $('#kyc_customer_rows_container .kyc_customer_row').each(function () {
        var attribute = $(this).find('.kyc_cust_rule_attr_select').val() || [];
        var when = $(this).find('select[name="kyc_cust_rule_when[]"]').val();
        var amount = $(this).find('input[name="kyc_cust_rule_amount[]"]').val();
        var logic = $(this).find('.kyc_cust_rule_logic_select').val();
        if (attribute) {
            rules.push({ attribute: attribute, when: when, amount: amount || '', logic: logic || 0 });
        }
    });
    $('#kyc_customer_rules_hidden').val(JSON.stringify(rules));
}

// --- Load KYC docs: if $select is passed, populate that one; otherwise populate all existing ---
function loadKycDocs($select, selectedVal) {
    $.ajax({
        url: base_url + 'index.php/admin_settings/kyc_master',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if (!data || data.length == 0) return;

            if ($select) {
                // Single select mode (for dynamically added rows)
                var selectedArr = [];
                if (Array.isArray(selectedVal)) {
                    selectedArr = $.map(selectedVal, function(v) { return String(v); });
                } else {
                    selectedArr = [String(selectedVal)];
                }
                $.each(data, function (i, doc) {
                    var isSelected = ($.inArray(String(doc.id_mas_kyc), selectedArr) > -1) ? 'selected' : '';
                    $select.append('<option value="' + doc.id_mas_kyc + '" ' + isSelected + '>' + doc.name + '</option>');
                });
                $select.select2({width: '100%'});
            } else {
                // Page load mode: populate all existing selects
                $('.kyc_cust_rule_attr_select').each(function () {
                    var $el = $(this);
                    var selectedArr = $el.data('selected') || [];
                    if (typeof selectedArr === 'string') {
                        try { selectedArr = JSON.parse(selectedArr); } catch(e) { selectedArr = []; }
                    }
                    var selectedStrs = $.map(selectedArr, function(v) { return String(v); });
                    $.each(data, function (i, doc) {
                        var isSelected = ($.inArray(String(doc.id_mas_kyc), selectedStrs) > -1) ? 'selected' : '';
                        $el.append('<option value="' + doc.id_mas_kyc + '" ' + isSelected + '>' + doc.name + '</option>');
                    });
                    $el.select2({width: '100%'});
                });
                $('.kyc_rule_docs_select').each(function () {
                    var $el = $(this);
                    var selectedArr = $el.data('selected') || [];
                    if (typeof selectedArr === 'string') {
                        try { selectedArr = JSON.parse(selectedArr); } catch(e) { selectedArr = []; }
                    }
                    var selectedStrs = $.map(selectedArr, function(v) { return String(v); });
                    $.each(data, function (i, doc) {
                        var isSelected = ($.inArray(String(doc.id_mas_kyc), selectedStrs) > -1) ? 'selected' : '';
                        $el.append('<option value="' + doc.id_mas_kyc + '" ' + isSelected + '>' + doc.name + '</option>');
                    });
                    $el.select2({width: '100%'});
                });
            }
        }
    });
}


// --- Load Schemes: if $select is passed, populate that one; otherwise populate all existing ---
function loadKycSchemes($select, selectedVal) {
    $.ajax({
        url: base_url + 'index.php/admin_settings/get_active_schemes',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if (!data || data.length == 0) return;

            if ($select) {
                // Single select mode (for dynamically added rows)
                var selectedArr = [];
                if (Array.isArray(selectedVal)) {
                    selectedArr = $.map(selectedVal, function(v) { return String(v); });
                } else {
                    selectedArr = [String(selectedVal)];
                }
                $.each(data, function (i, scheme) {
                    var isSelected = ($.inArray(String(scheme.id_scheme), selectedArr) > -1) ? 'selected' : '';
                    $select.append('<option value="' + scheme.id_scheme + '" ' + isSelected + '>' + scheme.scheme_name + '</option>');
                });
                $select.select2({width: '100%'});
            } else {
                // Page load mode: populate all existing selects
                $('.kyc_rule_scheme_select').each(function () {
                    var $el = $(this);
                    var selectedArr = $el.data('selected') || [];
                    if (typeof selectedArr === 'string') {
                        try { selectedArr = JSON.parse(selectedArr); } catch(e) { selectedArr = []; }
                    }
                    var selectedStrs = $.map(selectedArr, function(v) { return String(v); });
                    $.each(data, function (i, scheme) {
                        var isSelected = ($.inArray(String(scheme.id_scheme), selectedStrs) > -1) ? 'selected' : '';
                        $el.append('<option value="' + scheme.id_scheme + '" ' + isSelected + '>' + scheme.scheme_name + '</option>');
                    });
                    $el.select2({width: '100%'});
                });
            }
        }
    });
}


// ─── Feature Flags CRUD ───────────────────────────────────────────────────────

var ff_flags_data = [];     // in-memory store

/**
 * Builds a single <tr> for a feature flag row.
 * idx  = 0-based row index (for row number display)
 * flag = {code, description, status}
 * isNew = true if this is a fresh empty row
 */
function ff_build_row(idx, flag, isNew) {
    var status = (flag.status == 1) ? 1 : 0;
    var switchId = 'ff_switch_' + idx;
    var tr = '<tr class="ff_flag_row" data-idx="' + idx + '">' +
        '<td style="text-align:center; vertical-align:middle;">' + (idx + 1) + '</td>' +
        '<td style="vertical-align:middle;">' +
        '  <input type="text" class="form-control input-sm ff_code_input" ' +
        '    value="' + ($('<div>').text(flag.code).html()) + '" ' +
        '    placeholder="e.g. SHOW_REGION" ' +
        '    style="font-family:monospace; text-transform:uppercase;" />' +
        '</td>' +
        '<td style="vertical-align:middle;">' +
        '  <input type="text" class="form-control input-sm ff_desc_input" ' +
        '    value="' + ($('<div>').text(flag.description).html()) + '" ' +
        '    placeholder="Short description of this flag" />' +
        '</td>' +
        '<td style="text-align:center; vertical-align:middle;">' +
        '  <input type="checkbox" class="ff_status_switch" id="' + switchId + '" ' +
        (status == 1 ? 'checked="checked"' : '') + ' />' +
        '</td>' +
        '<td style="text-align:center; vertical-align:middle;">' +
        '  <button type="button" class="btn btn-xs btn-danger ff_remove_row" title="Remove">' +
        '    <i class="fa fa-trash"></i>' +
        '  </button>' +
        '</td>' +
        '</tr>';
    return tr;
}

/** Renders all rows from ff_flags_data into the table body. */
function ff_render_table() {
    var $tbody = $('#ff_rows_body');
    $tbody.empty();
    if (ff_flags_data.length === 0) {
        $tbody.append('<tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No feature flags defined. Click <b>+ Add New Flag</b> to add one.</td></tr>');
        return;
    }
    $.each(ff_flags_data, function(i, flag) {
        $tbody.append(ff_build_row(i, flag, false));
    });
    // Init bootstrap-switch for each status toggle
    $tbody.find('.ff_status_switch').each(function() {
        if ($.fn.bootstrapSwitch) {
            $(this).bootstrapSwitch({
                size: 'small',
                onText: 'ON',
                offText: 'OFF',
                onColor: 'success',
                offColor: 'default'
            });
        }
    });
}

/** Shows a success or error alert in the Feature Flags tab */
function ff_show_alert(msg, type) {
    var $a = $('#ff_alert');
    $a.removeClass('alert-success alert-danger alert-info').addClass('alert-' + type);
    $('#ff_alert_msg').html(msg);
    $a.fadeIn(200);
    setTimeout(function() { $a.fadeOut(400); }, 4000);
}

/** Loads flags from server and renders the table. */
function ff_load_flags() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/get_feature_flags',
        dataType: 'json',
        success: function(resp) {
            if (resp && resp.status === 'ok') {
                ff_flags_data = resp.flags || [];
            } else {
                ff_flags_data = [];
            }
            ff_render_table();
        },
        error: function() {
            ff_flags_data = [];
            ff_render_table();
        }
    });
}

/** Reads current table rows into ff_flags_data[] */
function ff_collect_from_table() {
    ff_flags_data = [];
    $('#ff_rows_body .ff_flag_row').each(function() {
        var code = $(this).find('.ff_code_input').val().trim().toUpperCase();
        var desc = $(this).find('.ff_desc_input').val().trim();
        var status = 0;
        var $sw = $(this).find('.ff_status_switch');
        if ($.fn.bootstrapSwitch && $sw.data('bootstrapSwitch')) {
            status = $sw.bootstrapSwitch('state') ? 1 : 0;
        } else {
            status = $sw.is(':checked') ? 1 : 0;
        }
        if (code !== '') {
            ff_flags_data.push({ code: code, description: desc, status: status });
        }
    });
}

// Load flags when tab is first shown
$('a[href="#tab_feature_flags"]').on('shown.bs.tab show.bs.tab click', function() {
    if ($('#ff_rows_body .ff_flag_row').length === 0 && ff_flags_data.length === 0) {
        ff_load_flags();
    }
});

// Also load if the tab is already active on page load
$(document).ready(function() {
    if ($('#tab_feature_flags').hasClass('active')) {
        ff_load_flags();
    }
});

// Add new blank row
$(document).on('click', '#ff_add_new_row', function() {
    var newFlag = { code: '', description: '', status: 0 };
    var idx = $('#ff_rows_body .ff_flag_row').length;
    // If table shows "no flags" placeholder, remove it first
    $('#ff_rows_body tr:not(.ff_flag_row)').remove();
    var $tr = $(ff_build_row(idx, newFlag, true));
    $('#ff_rows_body').append($tr);
    if ($.fn.bootstrapSwitch) {
        $tr.find('.ff_status_switch').bootstrapSwitch({
            size: 'small', onText: 'ON', offText: 'OFF',
            onColor: 'success', offColor: 'default'
        });
    }
    $tr.find('.ff_code_input').focus();
});

// Remove a row
$(document).on('click', '.ff_remove_row', function() {
    var $tr = $(this).closest('tr');
    $tr.fadeOut(200, function() {
        $(this).remove();
        // Re-number remaining rows
        $('#ff_rows_body .ff_flag_row').each(function(i) {
            $(this).find('td:first').text(i + 1);
            $(this).attr('data-idx', i);
        });
        if ($('#ff_rows_body .ff_flag_row').length === 0) {
            $('#ff_rows_body').append('<tr><td colspan="5" style="text-align:center; color:#aaa; padding:20px;">No feature flags defined. Click <b>+ Add New Flag</b> to add one.</td></tr>');
        }
    });
});

// UPPERCASE code field as user types
$(document).on('input keyup', '.ff_code_input', function() {
    var pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});

// Save button
$(document).on('click', '#ff_save_btn', function() {
    ff_collect_from_table();
    var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/settings/save_feature_flags',
        data: { flags_json: JSON.stringify(ff_flags_data) },
        dataType: 'json',
        success: function(resp) {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Feature Flags');
            if (resp && resp.status === 'ok') {
                ff_show_alert('<i class="fa fa-check"></i> ' + resp.message, 'success');
                ff_load_flags(); // reload to confirm
            } else {
                ff_show_alert('<i class="fa fa-times"></i> ' + (resp ? resp.message : 'Unknown error'), 'danger');
            }
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Feature Flags');
            ff_show_alert('<i class="fa fa-times"></i> Server error. Please try again.', 'danger');
        }
    });
});

// ─── End Feature Flags CRUD ───────────────────────────────────────────────────
