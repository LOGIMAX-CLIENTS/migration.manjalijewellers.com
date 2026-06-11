var ctrl_page = path.route.split('/');
let inr_format = Intl.NumberFormat('en-IN');
var pre_img_resource = [];
var pre_img_files = [];
var deleted = 0; //to store table _length when delete button clicked (gift table)
var rows_added = 0; //to store table length after a row added (gift table)
var deleted_array = []; //to store the deleted rows id (gift table)
let formatCurrency = Intl.NumberFormat('en-IN', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
$(document).ready(function() {
    //if(ctrl_page[0] != 'account' && ctrl_page[1] != 'add'){
    get_group_name();
    // cus_image();
    //}

    // --- KYC: trigger on amount change ---
    $('#firstPayment_amt').on('change input', function() {
        if ($('#scheme').val() > 0 && $('#id_customer').val() > 0) {
            fetch_dynamic_kyc();
        }
    });
    setTimeout(function() {
        if ($('#scheme').val() > 0 && $('#id_customer').val() > 0) {
            fetch_dynamic_kyc();
        }
    }, 500);

    if (ctrl_page[0] == 'account' && ctrl_page[1] == 'add') {
        $('#active_check').css('display', 'none');
    } else {
        $('#active_check').css('display', 'block');
    }
    if ($("#block_payment").is(":checked") == true) {
        $("#area").css("display", "block");
    }
    $("#block_payment").on("switchChange.bootstrapSwitch", function(event, state) {
        $("#area").css("display", "none");
        if ($("#block_payment").is(":checked") == true) {
            $("#area").css("display", "block");
        }
    });
    $('#join_day').on("change", function(e) {
        if (this.value != '' && this.value != 0) {
            var from_date = $('#account_list1').text();
            var to_date = $('#account_list2').text();
            var id_customer = $("#id_customer").val();
            var id_scheme = $('#id_scheme').val();
            var id_branch = $("#id_branch").val();
            get_scheme_acc_list(from_date, to_date, id_branch, id_customer, id_scheme);
        }
    });
    // gift_issued_list(ctrl_page[2]);
    // --- KYC-guarded acc_join submit ---
    $('#acc_join').on('submit', function(e) {
        e.preventDefault();
        if (window.payment_kyc_missing) {
            e.stopImmediatePropagation();
            $('#kyc_collection_modal').appendTo('body').modal('show');
            if (typeof $.toaster === 'function') {
                $.toaster({ priority: 'danger', title: 'Warning!', message: 'Mandatory KYC documents are required for this scheme threshold.' });
            }
            $("div.overlay").css("display", "none");
            setTimeout(function() { $('#submit').prop('disabled', false); }, 100);
            return false;
        }
        if (typeof window.validateKycImages === 'function') {
            if (!window.validateKycImages('#acc_join')) {
                return false;
            }
        }
        $('#submit').prop('disabled', true);
        var url = base_url + 'index.php/account/' + ($('#id_scheme_account').val() > 0 ? 'update/' + $('#id_scheme_account').val() : 'save');
        var form_data = $('#acc_join').serializeArray();
        var execute_final_save = function() {
            $.ajax({
                url: url,
                data: form_data,
                dataType: "JSON",
                type: "POST",
                async: false,
                success: function(data) {
                     if (data.status) {
                        if ($('#id_scheme_account').val() > 0) {
                            window.location.href = base_url + "index.php/account/new";
                        } else {
                            window.location.href = base_url + "index.php/" + (data.type == 1 ? "payment/add" : "account/new");
                        }
                    } else {
                        window.location.href = base_url + "index.php/account/new";
                    }
                },
                error: function(error) {
                    $("div.overlay").css("display", "none");
                    $('#submit').prop('disabled', false);
                }
            });
        };

        var id_scheme = $('#scheme').val() || 0;
        var id_customer = $('#id_customer').val() || 0;
        var payment_amt = $('#firstPayment_amt').val() || 0;
        if (payment_amt === '' || isNaN(payment_amt)) payment_amt = 0;
        if (id_scheme > 0 && id_customer > 0) {
            $("div.overlay").show();
            $.ajax({
                url: base_url + "index.php/admin_manage/ajax_get_dynamic_kyc/" + id_scheme + "/" + id_customer + "/" + payment_amt + "/0",
                type: "POST",
                success: function(response) {
                    var clean_html = $.trim(response);
                    if (clean_html.length > 0 && $(clean_html).find('.kyc-dynamic-input').length > 0) {
                        $("div.overlay").hide();
                        window.payment_kyc_missing = true;
                        $('#dynamic_kyc_modal_wrapper').html(clean_html);
                        if (typeof window.apply_kyc_inputmasks === 'function') window.apply_kyc_inputmasks();
                        $('#dynamic_kyc_modal_cus_id').val(id_customer);
                        $('#dynamic_kyc_modal_scheme_id').val(id_scheme);
                        $('#dynamic_kyc_modal_pay_amount').val(payment_amt);
                        $('#dynamic_kyc_modal_sch_acc_id').val('0');
                        $('#kyc_collection_modal').appendTo('body').modal('show');
                        if (typeof $.toaster === 'function') {
                            $.toaster({ priority: 'warning', title: 'KYC Required', message: 'Please provide KYC documents to proceed.' });
                        }
                        $('#submit').prop('disabled', false);
                    } else {
                        window.payment_kyc_missing = false;
                        execute_final_save();
                    }
                },
                error: function() {
                    $("div.overlay").hide();
                    $('#submit').prop('disabled', false);
                }
            });
        } else {
            execute_final_save();
        }
    });
    $(document).on('click', ".rate_fix_warning", function(e) {
        $.toaster({ priority: 'warning', title: 'Closing Warning', message: '' + "</br>You cannot close account without fixing rate. Kindly fix rate to close this scheme account.." });
    });
    //get_group_list();
    $("#account_name").on('keypress', function(event) {
        var regex = new RegExp("^[a-zA-Z _ \r\s]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            alert('Account name must contain alphabets only')
            return false;
        }
    });
    customers = [];
    schCodes = [];
    //get_Schemegroup
    if (ctrl_page[1] == 'edit') {
        $("#button_otp").hide();
        gift_issued_list(ctrl_page[2]);
        var table_rows = $('#chart_gift_creation_tbl tbody tr').length;
        rows_added = $('#chart_gift_creation_tbl tbody tr').length;
        $("#table_row_length").val(table_rows);
        for (var i = 1; i <= table_rows; i++) {
            get_gift_names(i);
        }
    }
    if (ctrl_page[0] == 'account' && ctrl_page[1] == 'add') {
        $(".hasgift").css("display", "none");
        $("#otp_txt_box").hide();
        get_otpstatus_for_gift();
        //$('#start_date').prop('disabled', true);
    }
    $("#button_otp").on('click', function() {
        $("#otp_txt_box").hide();
        var mobile_data = $("#mobile_number").val();
        if (mobile_data == "") {
            $.toaster({ priority: 'warning', title: 'Warning', message: '' + "</br> Please enter valid customer .." });
            $("#button_otp").attr("disabled", true);
        } else {
            $('#verify_otp_modal').modal('show');
        }
    });
    $("#issue_gift").on('click', function() {
        $(".show_gifts").css("display", "block");
    });
    $("#issue_prize").on('click', function() {
        $(".show_prizes").css("display", "block");
    });
    $('#gift_list').select2().on("change", function(e) {
        if (this.value != '') {
            $('#gift_val').val(this.value);
        }
    });
    //get)closed branch name//HH
    if (ctrl_page[1] == 'close') {
        get_schemename();
        get_employee_name();
        if (ctrl_page[0] == 'account') {
            if (ctrl_page[2] != 'scheme') {
                get_cls_branchname();
            } else {
                // calculate_closing_balance();
            }
        }
    }
    //webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB
    if (ctrl_page[1] == 'close' && ctrl_page[2] == 'scheme') {
        Webcam.set({
            width: 290,
            height: 190,
            image_format: 'jpg',
            jpeg_quality: 90
        });
        Webcam.attach('#my_camera');
        Webcam.on('error', function(err) {
            console.log('Error accessing webcam:', err);
        });
        $("#imageModal").setShortcutKey(17, 73, function() {
            take_snapshot('pre_images');
        });
    }
    $('#otp').on('keyup', function() {
        var inputLength = $(this).val().length;
        if (inputLength === 6) {
            $('#verify_otp').prop('disabled', false);
        } else {
            $('#verify_otp').prop('disabled', true);
        }
    });
    $('#imageModal  #update_img').on('click', function() {
        $('#imageModal').modal('toggle');
        $('#image_closeing').val(encodeURIComponent(JSON.stringify(pre_img_resource)));
    });
    //webcam upload ends....	
    if (ctrl_page[0] == 'account' && ctrl_page[1] == 'close' && ctrl_page[2] == 'scheme') { // esakki #14-12
        $("#add_benefits").on("input", function() {
            let value = $(this).val();
            if ($("#fixed_wgtschamt").is(':checked')) {
                if (!/^[0-9]+(\.[0-9]{1,2})?$/.test(value)) {
                    $(this).val(value.slice(0, -1));
                }
            } else if ($("#fixed_wgtschwgt").is(':checked')) {
                if (!/^[0-9]+(\.[0-9]{1,3})?$/.test(value)) {
                    $(this).val(value.slice(0, -1));
                }
            }
        });
    }
    if (ctrl_page[1] == 'add') {
        // esakki 11-11
        if ($('#session_cus_mobile').val() != "" && $('#session_cus_mobile').val() != null && $('#session_cus_mobile').val() > 0 && $('#session_cus_mobile').val() != "" && $('#session_cus_mobile').val() != null && $('#session_cus_mobile').val() > 0 && $('#session_cus_name').val() != "" && $('#session_cus_name').val() != null) {
            var mobile_num = $('#session_cus_mobile').val();
            var cus_name = $('#session_cus_name').val();
            var cus_id = $('#session_cus_id').val();
            $('#id_customer').val(cus_id);
            $('#mobile_number').val((mobile_num + '-' + cus_name));
            get_customer_detail(cus_id);
        }
        $("#mobile_number").on("keyup", function(e) {
            var customer = $("#mobile_number").val();
            if (customer.length == 0) {
                // var filterval = "blur(8px)";
                //	$("#gift_list").attr('disabled',true);
                //	$("#prize_details").attr('disabled',true);
                // $('#gift_articles').css('filter', filterval);
                // $('#gift_articles').css('pointer-events', 'none');
                // $("#button_otp").show();
            }
            if (customer.length >= 2) {
                getSearchCustomers(customer);
            }
        });

        function getSearchCustomers(searchTxt) {
            my_Date = new Date();
            $.ajax({
                url: base_url + 'index.php/admin_manage/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),
                dataType: "json",
                method: "POST",
                data: { 'searchTxt': searchTxt },
                success: function(data) {
                    $("#mobile_number").autocomplete({
                        source: data,
                        select: function(e, i) {
                            e.preventDefault();
                            $("#mobile_number").val(i.item.label);
                            $("#id_customer").val(i.item.value);
                            $("#txt_mobile").val(i.item.mobile); //lines added by durga - 19/12/2022 from here #gift otp
                            $("#txt_mob").val(i.item.mobile);
                            $("#txt_mobile").attr("disabled", true); //lines added by durga - 19/12/2022 from here #gift otp
                            $("#button_otp").attr("disabled", false); //lines added by durga - 19/12/2022 ends here
                            get_customer_detail(i.item.value);
                        },
                        change: function(event, ui) {
                            if (ui.item === null) {
                                $("#mobile_number").val('');
                                $("#id_customer").val('');
                            }
                        },
                        response: function(e, i) {
                            // ui.content is the array that's about to be sent to the response callback.
                            if (searchTxt != "") {
                                if (i.content.length === 0) {
                                    var filterval = "blur(8px)"; //lines added by durga - 19/12/2022 from here #gift otp
                                    $('#gift_articles').css('filter', filterval);
                                    $('#gift_articles').css('pointer-events', 'none');
                                    //	$("#gift_list").attr('disabled',true);
                                    //	$("#prize_details").attr('disabled',true);
                                    //	$("#button_otp").attr('disabled',true);
                                    $("#button_otp").show();
                                    alert('Please Enter a valid Number');
                                }
                            } else {}
                        },
                        minLength: 3,
                    });
                }
            });
        }
        /*console.log(customerListArr);
	   $.each(customerListArr, function(key, val)
	   {
		   customerList.push({'label' : val.mobile+'  '+val.name, 'value' : val.id});
	   });*/
        /*	$( "#mobile_number" ).autocomplete(
        	{
        	source: customerList,
        	select: function(e, i)
        	{
        	console.log("successfully");
        	e.preventDefault();
        	$("#mobile_number" ).val(i.item.label);
        	$("#id_customer").val(i.item.value);
        	var id_customer=$('#id_customer').val();
        	 if($("#mobile_number" ).val().length>0)
        	 {
        		get_customer_detail(id_customer);
        	 }
        	},
        	response: function(e, i) {
        		// ui.content is the array that's about to be sent to the response callback.
        		if (i.content.length === 0) {
        		   alert('Please Enter a valid Number');
        		   $('#mobile_number').val('');
        		} 
        	},
        	 minLength: 4,
        	});*/
    }
    if (ctrl_page[1] == 'scheme_group' && (ctrl_page[2] == 'add' || ctrl_page[2] == 'edit')) {
        var dateToday = new Date();
        $('#date_add').datepicker({
            format: 'dd-mm-yyyy',
            "setValue": new Date(),
            "autoclose": true
        }).datepicker("setDate");
        $('#date_update').datepicker({
            format: 'dd-mm-yyyy',
            "setValue": new Date(),
            "autoclose": true
        }).datepicker("setDate");
        get_schemename();
    } else if (ctrl_page[1] == 'scheme_group' && ctrl_page[2] == 'edit') {
        get_schemename();
    } else if (ctrl_page[0] == 'account' && (ctrl_page[1] == 'add' || ctrl_page[1] == 'edit')) {
        get_emp_branchwise(); //Get Branch wise emp name in Scheme Join Page admin //
        //get_gift_names();
        get_schemes();
    }
    if (ctrl_page[1] == 'scheme_group' && ctrl_page[2] == 'list') {
        get_group_list();
    }
    // enable & disable save button//hh	
    if (ctrl_page[1] == 'scheme_group' && (ctrl_page[2] == 'add')) {
        //$('#group').prop('disabled', true); old 05-12-2022
        $('#group').prop('disabled', false); // New 05-12-2022
    } else if (ctrl_page[2] == 'add' || ctrl_page[2] == 'edit') {
        $('#group').prop('disabled', false);
    }
    // enable & disable save button//hh		
    $('#group_code').on('blur onchange', function() {
        var group_code = this.value;
        check_groupcode(group_code);
    });
    // scheme group//
    if (ctrl_page[1] == 'scheme_reg' && ctrl_page[2] == 'list') {
        // existing scheme reg// 
        if (ctrl_page[3]) {
            $('#filtered_status').val(ctrl_page[3]);
        }
        // existing scheme reg// 
        initialize_branch_list("sel_branch");
        console.log(ctrl_page[1]);
        get_request_list();
        $('#sel_branch').select2().on("change", function(e) {
            if (this.value != '') {
                status = $('#filtered_status').val();
                get_request_list('', '', this.value, status);
            }
        });
        $('#filtered_status').on('change', function() {
            if (this.value == 1) {
                $("#reject").css("display", "none");
                $("#approve").css("display", "none");
                $("#revert").css("display", "block");
            } else {
                $("#reject").css("display", "block");
                $("#approve").css("display", "block");
                $("#revert").css("display", "none");
            }
            get_request_list('', '', '', this.value);
        });
        $('#payment_list1').empty();
        $('#payment_list2').empty();
        $('#payment_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
        $('#payment_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
        $('#reqList-dt-btn').daterangepicker({
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
            function(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#payment_list1').text(start.format('YYYY-MM-DD'));
                $('#payment_list2').text(end.format('YYYY-MM-DD'));
                get_request_list(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), '', $('#filtered_status').val())
            }
        );
        $("input[name='upd_status_btn']:radio").change(function() {
            if ($("input[name='reg_request_id[]']:checked").val()) {
                var selected = [];
                var approve = false;
                $("#scheme_reg_list tbody tr").each(function(index, value) {
                    if ($(value).find("input[name='reg_request_id[]']:checked").is(":checked")) {
                        var firstPayment_amt = $(value).find(".firstPayment_amt").val();
                        var scheme_type = $(value).find(".scheme_type").val();
                        var firstPayamt_payable = $(value).find(".firstPayamt_payable").val();
                        if (scheme_type == 3 && firstPayment_amt == '' && firstPayamt_payable == 1) {
                            alert('Please Enter a first Installment Payment Amount');
                            approve = false;
                            $('input[name=upd_status_btn]').removeAttr('checked');
                        } else {
                            approve = true;
                        }
                        transData = {
                            'id_reg_request': $(value).find(".id_reg_request").val(),
                            'remark': $(value).find(".remark").val(),
                            'id_scheme': $(value).find(".id_scheme").val(),
                            'id_branch': $(value).find(".id_branch").val(),
                            'scheme_acc_number': $(value).find(".chit_no").val(),
                            'scheme_group_code': $(value).find(".scheme_group_code").val(),
                            'group_code': $(value).find(".group_code").val(),
                            'id_customer': $(value).find(".id_customer").val(),
                            'ac_name': $(value).find(".ac_name").val(),
                            'mobile': $(value).find(".mobile").val(),
                            'email': $(value).find(".email").val(),
                            'added_by': $(value).find(".added_by").val(),
                            'pan_no': $(value).find(".pan_no").val(),
                            'firstPayment_amt': $(value).find(".firstPayment_amt").val(),
                            'paid_installments': $(value).find(".paid_installments").val(),
                            'balance_amount': $(value).find(".balance_amount").val(),
                            'balance_weight': $(value).find(".balance_weight").val(),
                            'last_paid_weight': $(value).find(".last_paid_weight").val(),
                            'last_paid_chances': $(value).find(".last_paid_chances").val(),
                            'last_paid_date': $(value).find(".last_paid_date").val(),
                        }
                        selected.push(transData);
                    }
                })
                req_status = $("input[name='upd_status_btn']:checked").val();
                req_data = selected;
                console.log(req_data);
                if (approve == true) {
                    update_request_status(req_status, req_data);
                }
            }
        });
    }
    $("#block_payment").bootstrapSwitch();
    $("#show_gift_article").bootstrapSwitch();
    $("#duplicate_passbook_issued").bootstrapSwitch();
    $('#show_gift_article').on('switchChange.bootstrapSwitch', function(event, state) {
        var x = 1;
        var y = 0;
        if ($("#show_gift_article").is(':checked')) {
            $("#show_gift_article").val(x);
        } else {
            $("#show_gift_article").val(y);
        }
    });
    //to show Closed Acc detail
    $(document).on('click', "a.closed_ac_detail", function(e) {
        e.preventDefault();
        $('.closed_acc_detail').html(closedAccDetail($(this).data('id')));
        $('#clsd_acc_detail').modal('show', { backdrop: 'static' });
    });
    $('#close_save').click(function(event) {
        if ($('#remark_close').val() == '') {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter comment.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg').html(msg);
            return false;
        }
        if ($("input[name='account[closed_by]']:checked").val() == 1 && $('#nominee_mobile').val().length != 10) {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Valid 10 digit nominee mobile no.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg').html(msg);
            return false;
        }
        return true;
    });
    // esakki 26-11
    $('#close_save_print').click(function(event) {
        if ($('#remark_close').val() == '') {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter comment.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg').html(msg);
            return false;
        }
        if ($("input[name='account[closed_by]']:checked").val() == 1 && $('#nominee_mobile').val().length != 10) {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter Valid 10 digit nominee mobile no.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg').html(msg);
            return false;
        }
        return true;
    });
    $("#add_benefits,#add_charges").focus(function() {
        $('.close_actionBtns').css("display", "block");
    });
    // MC/ VA Discount--start 
    $("#discount_select").select2({
        placeholder: "select discount",
        allowClear: false
    });
    // MC/ VA Discount--End 
    $('#calc_blc').click(function(event) {
        calculate_closing_balance();
    });
    $('#add_benefits').on('input', function(event) {
        calculate_closing_balance();
    });
    $('#add_charges').on('input', function(event) {
        calculate_closing_balance();
    });
    // add_benefits weight or amount
    if ($('#sch_typ').val() != 0 || $('#sch_typ').val() != 3) {
        $("#fixed_wgtschamt").prop("checked", "checked");
        $("#fixed_wgtschamt").val(0);
        $('#add_benefits').val('0.00');
        $('#wgt_symbol').hide();
    }
    /* $("input[name='account[add_benefixed]']:radio").change(function () {
		   var closing_amt=$('#closing_amt').val();
			var closing_weight=$('#closing_weight').val();	
		 if($("#fixed_wgtschamt").is(':checked'))
		 {
			 $("#fixed_wgtschamt").val(0);
			  $('#fixed_wgtschwgt').val('');
			  $('#add_benefits').val('0.00');
			  $('#wgt_symbol').hide();
			  $('#curren_symbol').show();
			$('#calc_blc').attr('disabled', false);
			 $('#closing_amount').val(closing_amt);
			 $('#closing_balance').val(closing_weight);
		 }
		 else if($("#fixed_wgtschwgt").is(':checked'))
		 {
			  $('#fixed_wgtschwgt').val(1);
			  $("#fixed_wgtschamt").val('');
			  $("#add_benefits").val('0.00');
			  $('#wgt_symbol').show();
			  $('#curren_symbol').hide();
			  $('#calc_blc').attr('disabled', false);
			   $('#closing_amount').val(closing_amt);
			  $('#closing_balance').val(closing_weight);
		 }
  });*/
    $("input[name='account[add_benefixed]']:radio").change(function() {
        var metal_rate;
        var metal_rate_val = parseFloat($('#metal_rate').val());
        if (metal_rate_val != '' && metal_rate_val != null && metal_rate_val != 'undefined' && metal_rate_val != 0 && !isNaN(metal_rate_val)) {
            metal_rate = metal_rate_val;
        } else {
            metal_rate = 1;
        }
        //console.log(metal_rate);
        var voucher_deduct_amt = $('#voucher_deduction').val();
        var voucher_weight;
        if (voucher_deduct_amt != '' && voucher_deduct_amt != null && voucher_deduct_amt != 'undefined' && voucher_deduct_amt != 0) {
            voucher_weight = formatMetalWeight(parseFloat(voucher_deduct_amt) / metal_rate);
        } else {
            voucher_weight = 0;
        }
        //console.log(voucher_weight);
        //console.log(voucher_deduct_amt);
        //var closing_amt=$('#closing_amt').val()-voucher_deduct_amt;
        var closing_amt = $('#closing_paid_amt').val();
        var closing_weight = $('#closing_weight').val() - voucher_weight;
        if ($("#fixed_wgtschamt").is(':checked')) {
            $("#fixed_wgtschamt").val(0);
            $('#fixed_wgtschwgt').val('');
            $('#add_benefits').val('0');
            $("#add_benefits").attr({
                'type': 'number',
                'step': '0.01',
                'pattern': '^[0-9]+(\\.[0-9]{1,2})?$',
                'title': 'Enter a valid amount (e.g., 123.45)',
                'placeholder': 'Enter Amount'
            });
            $('#wgt_symbol').hide();
            $('#curren_symbol').show();
            $('#calc_blc').attr('disabled', false);
            /* $('#closing_amount').val(closing_amt);
            $('#closing_balance').val(closing_weight);*/
            calculate_closing_balance();
            initializeNumberInputListeners();
        } else if ($("#fixed_wgtschwgt").is(':checked')) {
            $('#fixed_wgtschwgt').val(1);
            $("#fixed_wgtschamt").val('');
            $("#add_benefits").val('0.000');
            $("#add_benefits").attr({
                'type': 'number',
                'step': '0.001',
                'pattern': '^[0-9]+(\\.[0-9]{1,3})?$',
                'title': 'Enter a valid weight (e.g., 0.001, 12.345)',
                'placeholder': 'Enter Weight'
            });
            $('#wgt_symbol').show();
            $('#curren_symbol').hide();
            $('#calc_blc').attr('disabled', false);
            calculate_closing_balance();
            initializeAnyInputListeners();
        }
    });
    //Commented by Durga 21.06.2023
    /* $("#add_charges").keyup(function () {
    	var closing_amt=$('#closing_amt').val();
    	var closing_weight=$('#closing_weight').val();	
    	$('#closing_amount').val(closing_amt);
    	$('#closing_balance').val(closing_weight);
     });*/
    /*	$('#clear_blc').click(function(event){
    	   //var closing_amt=$('#closing_amt').val();
    	   var closing_weight=$('#closing_weight').val();
    	   var closing_amt=$('#closing_paid_amt').val();
    	   $('#add_benefits').val('0.00');
    	   $('#add_charges').val('0.00');
    	   $('#closing_amount').val(closing_amt);
    	   $('#closing_balance').val(closing_weight);
    		 $("#closing_wgt_amount").val(closing_amt); 
    	   $('#calc_blc').attr('disabled', false);
    	});*/
    $('#clear_blc').click(function(event) {
        //var closing_amt=$('#closing_amt').val();
        var metal_rate;
        var metal_rate_val = parseFloat($('#metal_rate').val());
        if (metal_rate_val != '' && metal_rate_val != null && metal_rate_val != 'undefined' && metal_rate_val != 0 && !isNaN(metal_rate_val)) {
            metal_rate = metal_rate_val;
        } else {
            metal_rate = 1;
        }
        console.log(metal_rate);
        var voucher_deduct_amt = $('#voucher_deduction').val();
        var voucher_weight;
        if (voucher_deduct_amt != '' && voucher_deduct_amt != null && voucher_deduct_amt != 'undefined' && voucher_deduct_amt != 0) {
            voucher_weight = formatMetalWeight(parseFloat(voucher_deduct_amt) / metal_rate);
        } else {
            voucher_weight = 0;
        }
        console.log(voucher_weight);
        console.log(voucher_deduct_amt);
        if ($('#sch_typ').val() == 0) {
            var closing_weight = $('#closing_weight').val() - voucher_deduct_amt;
            var closing_amt = $('#closing_paid_amt').val();
            console.log(closing_weight);
            console.log(closing_amt);
        } else {
            var closing_weight = $('#closing_weight').val() - voucher_weight;
            var closing_amt = $('#closing_paid_amt').val() - voucher_deduct_amt;
        }
        $('#add_benefits').val('0');
        $('#add_charges').val('0');
        $('#closing_amount').val(closing_amt);
        $('#closing_balance').val(closing_weight);
        $("#closing_wgt_amount").val(closing_amt);
        $('#calc_blc').attr('disabled', false);
    });
    // add_benefits weight or amount 
    function closedAccDetail(id) {
        var transaction = "";
        $("div.overlay").css("display", "block");
        $.ajax({
            url: base_url + "index.php/account/closed/view/" + id + "?nocache=" + my_Date.getUTCSeconds(),
            dataType: "JSON",
            type: "POST",
            async: false,
            success: function(data) {
                //transaction  = "<table class='table table-bordered trans'><tr><th>Customer Name</th><td>"+data.name+"</td></tr><tr><th>Customer Mobile</th><td>"+data.mobile+"</td></tr><tr><th>Account Name</th><td>"+data.account_name+"</td></tr><tr><th>Account No.</th><td>"+scheme_acc_number+"</td></tr><tr><th>Start Date</th><td>"+data.start_date+"</td></tr><tr><th>Closed Date</th><td>"+data.closing_date+"</td></tr><tr><th>Closed Employee</th><td>"+data.employee_closed+"</tr></td><tr><th>Scheme Name</th><td>"+data.scheme_name+"</td></tr><tr><th>Scheme Type</th><td>"+data.scheme_type+"</td></tr><tr><th>Amount Payable</th><td>"+data.sch_amt+"</td></tr><tr><th>Total Paid Installments</th><td>"+data.paid_installments+'/'+data.total_installments+"</td></tr><tr><th>Total Paid</th><td>"+data.total_paid+"</td></tr>"+(data.total_installments==data.paid_installments ? "<tr><th>Scheme Benefits</th><td>"+data.interest+"</td></tr>" :'')+"<tr><th>Additional Benefits</th><td>"+data.additional_benefits+"</td></tr><tr><th>Detections/Tax</th><td>"+data.tax+"</td></tr><tr><th>Bank Charges</th><td>"+data.bank_chgs+"</td></tr><tr><th>Additional Charges</th><td>"+data.closing_add_chgs+"</td></tr>"+(data.sch_typ==1 ? "<tr><th>Closing Weight</th><td>"+data.closing_balance+' g'+"</td></tr>" :"<tr><th>Closing Balance</th><td>"+data.closing_balance+"</td></tr>")+"<tr><th>Closed Request By</th><td>"+data.closed_by+" ("+data.closedBy+")</td></tr><tr><th>OTP Verified Mobile</th><td>"+data.otp_verified_mob+"</td></tr><tr><th>Closing Comments</th><td><span class='label bg-yellow'>"+data.remark_close+"</span></td></tr>"+
                transaction = "<table class='table table-bordered trans'><tr><th>Customer Name</th><td>" + data.name + "</td></tr><tr><th>Customer Mobile</th><td>" + data.mobile + "</td></tr><tr><th>Account Name</th><td>" + data.account_name + "</td></tr><tr><th>Account No.</th><td>" + data.scheme_acc_number + "</td></tr><tr><th>Start Date</th><td>" + data.start_date + "</td></tr><tr><th>Closed Date</th><td>" + data.closing_date + "</td></tr><tr><th>Closed Employee</th><td>" + data.employee_closed + "</tr></td><tr><th>Scheme Name</th><td>" + data.scheme_name + "</td></tr><tr><th>Scheme Type</th><td>" + data.scheme_type + "</td></tr><tr><th>Amount Payable</th><td>" + data.sch_amt + "</td></tr><tr><th>Total Paid Installments</th><td>" + data.paid_installments + '/' + data.total_installments + "</td></tr><tr><th>Total Paid</th><td>" + data.total_paid + "</td></tr>" + (data.total_installments == data.paid_installments ? "<tr><th>Scheme Benefits</th><td>" + data.interest + "</td></tr>" : '') + "<tr><th>Additional Benefits</th><td>" + data.additional_benefits + "</td></tr><tr><th>Detections/Tax</th><td>" + data.tax + "</td></tr><tr><th>Bank Charges</th><td>" + data.bank_chgs + "</td></tr><tr><th>Additional Charges</th><td>" + data.closing_add_chgs + "</td></tr>" + (data.sch_typ == 1 ? "<tr><th>Closing Weight</th><td>" + data.closing_balance + ' g' + "</td></tr>" : "<tr><th>Closing Balance</th><td>" + data.closing_balance + "</td></tr>") + "<tr><th>Closed Request By</th><td>" + data.closed_by + " (" + data.closedBy + ")</td></tr><tr><th>OTP Verified Mobile</th><td>" + data.otp_verified_mob + "</td></tr><tr><th>Closing Comments</th><td><span class='label bg-yellow'>" + data.remark_close + "</span></td></tr>" +
                    "<tr><th>Closing Image</th><td>" + (data.img_path ? "<img src=" + base_url + data.img_path + ">" : "-") + "</td></tr>" +
                    "</table>";
                return transaction;
                $("div.overlay").css("display", "none");
            },
            error: function(error) {
                $("div.overlay").css("display", "none");
            }
        });
        $("div.overlay").css("display", "none");
        return transaction;
    }
    if (ctrl_page[1] == 'new') {
        var date = new Date();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 6, 1);
        var from_date = firstDay.getFullYear() + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();
        var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
        $('#account_list1').empty();
        $('#account_list2').empty();
        var id_customer = $("#id_customer").val();
        var id_scheme = $('#id_scheme').val();
        var id_branch = $("#id_branch").val();
        //get_scheme_acc_list(from_date,to_date);
        get_scheme_acc_list(from_date, to_date, id_branch, id_customer, id_scheme);
        $('#account_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
        $('#account_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
        $('#account-dt-btn').daterangepicker({
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
            function(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                var id_branch = $('#id_branch').val();
                var id_customer = $('#id_customer').val();
                var id_scheme = $('#id_scheme').val();
                //get_scheme_acc_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_branch,id_customer)			 
                get_scheme_acc_list(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), id_branch, id_customer, id_scheme);
                $('#account_list1').text(start.format('YYYY-MM-DD'));
                $('#account_list2').text(end.format('YYYY-MM-DD'));
            }
        );
    }
    if (ctrl_page[1] == 'close') {
        if ($('#enable_closing_otp').val() == 0) {
            $('#otp').prop('disabled', true);
            $('#send_otp').prop('disabled', true);
            $('#verify_otp').prop('disabled', false);
            $('#otp_block').hide();
            $('#otp_status_block').hide();
            $('#close_actionBtns').css('display', 'block'); //disabled hh//
        } else {
            $('#otp').prop('disabled', false);
            $('#send_otp').prop('disabled', false);
            $('#verify_otp').prop('disabled', true);
            $('#close_save').prop('disabled', false); //disabled hh//
            $('#close_actionBtns').css('display', 'none'); //disabled hh//
            $('#otp_block').show();
            $('#otp_status_block').show();
        }
        $('#closed_list1').empty();
        $('#closed_list2').empty();
        $('#closed_list1').text(moment().format('YYYY-MM-DD'));
        $('#closed_list2').text(moment().format('YYYY-MM-DD'));
        $('#closed_list_date').text(moment().format('DD-MM-YYYY') + ' to ' + moment().format('DD-MM-YYYY'));
        get_closed_acc_list(moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'));
        $('#closed-acc-dt-btn').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment(),
                endDate: moment()
            },
            function(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                get_closed_acc_list(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))
                $('#closed_list1').text(start.format('YYYY-MM-DD'));
                $('#closed_list2').text(end.format('YYYY-MM-DD'));
                $('#closed_list_date').text(start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
            }
        );
    }
    /*-- Coded by ARVK --*/
    var fewSeconds = 90;
    $('#send_otp,#send_otp_to_branch').click(function(event) {
        if ($("input[name='account[closed_by]']:checked").val() == 1 && $('#branch_otp_clik').val() != 1) {
            var nom_mobile = $("#nominee_mobile").val();
            var nom_name = $("#nominee_name").val();
            if (nom_mobile != '' && nom_name != '') {
                /*var btn = $(this);
                btn.prop('disabled', true);
                setTimeout(function(){
                btn.prop('disabled', false);
                btn.prop('value', 'Resend OTP');
                }, fewSeconds*1000);
                close_acc_otp();*/
            } else {
                if (nom_mobile == '') {
                    $.toaster({ priority: 'warning', title: 'Nominee Details ', message: '' + "</br>Enter valid Nominee Mobile Number" });
                    $("#nominee_mobile").focus();
                } else if (nom_name == '') {
                    $.toaster({ priority: 'warning', title: 'Nominee Details ', message: '' + "</br>Enter valid Nominee Name" });
                    $("#nominee_name").focus();
                } else if (nom_mobile == '' && nom_name == '') {
                    $.toaster({ priority: 'warning', title: 'Nominee Details ', message: '' + "</br>Enter valid Nominee Name and Mobile number " });
                    $("#nominee_name").focus();
                }
            }
        } else {
            var btn = $(this);
            btn.prop('disabled', true);
            setTimeout(function() {
                btn.prop('disabled', false);
                btn.prop('value', 'Resend OTP');
            }, fewSeconds * 1000);
            if ($(this).context.id != 'send_otp_to_branch') {
                close_acc_otp();
                settimmer();
            } else {
                var id_customer = $.trim($("#id_customer").val());
                var name = $("#firstname").val();
                var br_mobile = $('#branch_otp_mobile').val();
                console.log(br_mobile);
                if (br_mobile != '') {
                    $('#send_otp').prop('disabled', true);
                    $('#branch_otp_clik').val(1);
                    send_closing_otp(br_mobile, id_customer, name);
                    settimmer();
                } else {
                    $.toaster({ priority: 'danger', title: 'Branch Employee Mobile', message: '' + "</br>Please provide the branch employee mobile number in branch master..." });
                }
            }
        }
    });

    function settimmer() {
        $('#timer').css("display", "block");
        let sendOrResendBtn = document.getElementById('send_otp');
        if (sendOrResendBtn.innerText === 'Send OTP to Customer') {
            startTimer();
        } else {
            clearInterval(timer); // Clear the existing timer
            timeLeft = 90; // Reset the time left
            document.getElementById('timer').innerText = ' Your OTP will expire within ' + timeLeft + ' Seconds'; // Update the display
            // sendOrResendBtn.innerText = 'Send'; // Change button text back to 'Send'
            // sendOrResendBtn.disabled = false; // Enable the button
            let hours = Math.floor(timeLeft / 3600);
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes; // Format minutes
            seconds = seconds < 10 ? '0' + seconds : seconds; // Format seconds
            document.getElementById("timer").innerText = "Your OTP will expire within " + hours + "h:" + minutes + "m:" + seconds + "s";
            startTimer()
        }
        $('#countdown').css("display", "block")
    }

    function startTimer() {
        // sendOrResendBtn.disabled = true;
        timer = setInterval(updateTimer, 1000);
    }
    /*function updateTimer() {
    	timeLeft--;
    	document.getElementById('timer').innerText = 'Your OTP will expire within ' + timeLeft + ' Seconds';
    	if (timeLeft === 0) {
    		document.getElementById('timer').innerText = '';
    		 clearInterval(timer);
    	}
    }*/
    function updateTimer() {
        if (timeLeft === 0) {
            clearInterval(timer);
            document.getElementById("timer").innerText = "";
        } else {
            let hours = Math.floor(timeLeft / 3600);
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes; // Format minutes
            seconds = seconds < 10 ? '0' + seconds : seconds; // Format seconds
            document.getElementById("timer").innerText = "Your OTP will expire within " + hours + "h:" + minutes + "m:" + seconds + "s";
            timeLeft--; // Decrement the time left
        }
    }
    //refferal report
    /* $("#referal_code").on("keyup", function (event) {  // esakki 11-11
    	if ($("#referal_code").val() != "") {
    		event.preventDefault();
    		var codes = $("#referal_code").val();
    		var referalcode = $("#referalcode_val").val();
    		if (codes != referalcode) {
    			checkreferalcode(codes);
    		}
    	}
    	else {
    		$("#ref_name").text("");
    		$('#submit').prop('disabled', false);
    		$("#referal_code").val("");
    	}
    });
    function checkreferalcode(codes) {
    	var id_customer = $("#id_customer").val();
    	$('.overlay').css('display', 'block');
    	$.ajax({
    		type: 'POST',
    		data: { 'referal_code': codes, 'id_customer': id_customer },
    		url: base_url + 'index.php/admin_manage/referralcode_check',
    		dataType: 'json',
    		success: function (data) {
    			$('.overlay').css('display', 'none');
    			//  	if(data.status==0)
    			// {
    			//   alert(data.msg);
    			//   $('#referal_code').val('');
    			//   $('#referal_code').attr("placeholder", data.msg);			
    			// 	return false;
    			// }	
    			//  set Ref  name ---15-12-23 // santhosh			
    			if (data.status == true) {
    				$('#ref_name').text('Employee Name: ' + data.emp_name);
    				$('#submit').prop('disabled', false);
    				return true;
    			} else {
    				$('#ref_name').text('No employee in this id');
    				// 	$('#referal_code').val('')
    				$('#submit').prop('disabled', true);
    				//	$('#referal_code').prop("required",true) 
    			}
    			//  set Ref  name ---15-12-23 // santhosh		
    		},
    	});
    } */
    /* New Referal Flow - Esakki 28-07-2025 Starts */
    $("input[name='scheme[referal_by]']:radio").change(function() {
        $("#referal_code").val('');
        $("#ref_name").text("");
        $('#referal_by').val("");
        $('#ref_code_input').css('display', 'block')
        var refer_by = $("input[name='scheme[referal_by]']:checked").val();
        $('#referal_by').val(refer_by);
    });
    $("#referal_code").on("keyup", function(e) {
        var ref_no = $("#referal_code").val();
        var ref_by = $('#referal_by').val();
        if (ref_no.length == 0) {
            $("#ref_name").text("");
            // $('#referal_by').val("");
        }
        if (ref_by == 1 && ref_no.length >= 2) {
            getRefBy(ref_no, ref_by);
        } else if (ref_by == 0 && ref_no.length >= 4) {
            getRefBy(ref_no, ref_by);
        }
    });

    function getRefBy(ref_no, ref_by) {
        my_Date = new Date();
        $.ajax({
            url: base_url + 'index.php/admin_manage/referralcode_check',
            dataType: "json",
            method: "POST",
            data: { 'referal_code': ref_no, 'ref_by': ref_by },
            success: function(data) {
                var formatted = "";
                if (data.data != "") {
                    formatted = data.data.map(item => ({
                        label: item.code, // shown in dropdown
                        value: item.code, // inserted into input
                        name: item.name // extra info for display
                    }));
                }
                $("#referal_code").autocomplete({
                    source: formatted,
                    select: function(e, i) {
                        console.log("Autocomplete select triggered:", i);
                        e.preventDefault();
                        $("#referal_code").val(i.item.value);
                        if (ref_by == 1) {
                            $('#ref_name').text('Employee Name: ' + i.item.name);
                        } else if (ref_by == 0) {
                            $('#ref_name').text('Customer Name: ' + i.item.name);
                        }
                    },
                    change: function(event, ui) {
                        console.log("Autocomplete change triggered:", i);
                        if (ui.item === null) {
                            $("#referal_code").val('');
                            $("#ref_name").text("");
                        }
                    },
                    response: function(e, i) {
                        // ui.content is the array that's about to be sent to the response callback.
                        console.log("Autocomplete response triggered:", i);
                        if (ref_no != "") {
                            if (i.content.length === 0) {
                                if (ref_by == 1) {
                                    $('#ref_name').text('No employee in this code');
                                } else if (ref_by == 0) {
                                    $('#ref_name').text('No customer in this code');
                                }
                            }
                        } else {}
                    },
                    minLength: 2,
                });
            }
        });
    }
    /* New Referal Flow - Esakki 28-07-2025 Ends */
    //refferal report
    $('#verify_otp').click(function(event) {
        $(this).prop('disabled', 'disabled');
        verify_close_acc_otp();
    });
    /*-- / Coded by ARVK --*/
    $('#select_all').click(function(event) {
        $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
        event.stopPropagation();
    });
    /* $("tbody tr td input[type='checkbox']").click(function(event) {
    	 $("#select_all").prop('checked', $(this).prop('checked'));
    		 event.stopPropagation();
     }); */
    //for confirm closing active account
    $(document).on('click', "a.confirm-close", function(e) {
        e.preventDefault();
        var link = $(this).data('href');
        $('#confirm-close').find('.btn-confirm').attr('href', link);
    });
    //for reverting close account 
    $(document).on('click', "a.confirm-revert", function(e) {
        e.preventDefault();
        var link = $(this).data('href');
        $('#confirm-revert').find('.btn-confirm').attr('href', link);
    });
    $(document).on('click', "a.btn-revert", function(e) {
        e.preventDefault(); // Prevent default action
        var link = $(this).data('href'); // Get the base URL for the Revert action
        // Make the AJAX GET request
        $.ajax({
            url: link, // Use the link directly
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert(response.message);
                }
            },
        });
    });
    $('#confirm-delete .btn-cancel').on('click', function(e) {
        $('.btn-confirm').attr('href', "#");
    });
    // if ($('#maturity_date').length > 0) {
    // 	$('#maturity_date').datepicker({
    // 		format: 'dd-mm-yyyy'
    // 	})
    // 		.on('changeDate', function (ev) {
    // 			$(this).datepicker('hide');
    // 		});
    // }
    if ($('#maturity_date').length > 0) {
        $('#maturity_date').datepicker({
                format: 'dd-mm-yyyy'
            })
            .on('changeDate', function(ev) {
                $(this).datepicker('hide');
            });
    }
    $('#last_paid_date').datepicker({
            format: 'dd-mm-yyyy'
        })
        .on('changeDate', function(ev) {
            $(this).datepicker('hide');
        });
    // if ($('#close_date').length > 0) {
    // 	$('#close_date').datepicker({
    // 		format: 'dd-mm-yyyy'
    // 	})
    // 		.on('changeDate', function (ev) {
    // 			$(this).datepicker('hide');
    // 		});
    // }
    if ($("#is_opening").length > 0) {
        prev_bal_edit();
        $('#is_opening').on('change', function() {
            prev_bal_edit();
        });
    }
    if ($('#customer_select').length > 0) {
        get_customers();
    }
    // if ($('#scheme').length > 0) {
    // 	$('#scheme').empty();
    // 	// get_schemes();
    // }
    if ($('#emp_select').length > 0) {
        get_employee_name();
    }
    $('#scheme').on('select2:unselecting', function(e) {
        $("#voucher_info_block").css("display", "none");
    });
    $('#scheme').on('change', function() {
        if (this.value) {
            get_scheme_detail(this.value);
            var scheme_val = $('#id_scheme').val();
            var scheme = $('#scheme').val();
            var hidden_id_scheme = $('#hid_id_scheme').val();
            if (hidden_id_scheme != scheme) {
                // $("#branch_select").select2("val",'');
                /* if ($("#emp_branch").val() == '') {
                	$("#employee_select").select2("val", '');
                } */
                // $("#referal_code").val('');  // esakki ref
                $("#agent_code").val('');
                //$("#account_name").val('');
                // $("#id_branch").val('');
                $("#firstPayment_amt").val('');
            }
        } else {
            $("#voucher_info_block").css("display", "none");
        }
        // }
        /*if(this.value!='')
           {
        		get_group_name(this.value);    // get_groupname for new sch join //hh
           }*/
    });
    $('.btn-acc-close').click(function(e) {
        e.preventDefault();
        var link = $(this).data('href');
        $('#confirm-close').find('.btn-confirm').attr('href', link);
    });
    $("input[name='account[closed_by]']:radio").change(function() {
        var closed_by = $("input[name='account[closed_by]']:checked").val();
        $('#otp_status').stop(true, true).hide();
        $('#otp_status').val('');
        $('#timer').val('');
        $('#timer').css("display", "none");
        $('#otp_status_block').css("display", "none");
        $('#send_otp').prop('disabled', false);
        $('#send_otp').css('display', 'block');
        $('#otp').val('');
        $('#otp').prop('required', false);
        $('#otp').prop('readonly', true);
        clearInterval(timer); // Clear the existing timer
        if (closed_by == 1) {
            $("#nominee_block").css('display', 'block');
            $("#otp_block").css('display', 'none');
            $("#nominee_mobile").prop("disabled", false);
            $("#nominee_name").prop("disabled", false);
            $("#nominee_mobile").prop("required", true);
            $("#nominee_name").prop("required", true);
            $('#otp').prop('required', false)
            $('#close_actionBtns').css('display', 'block');
            var nominee = $("#nominee_mobile").val();
            if (nominee == '') {
                $.toaster({ priority: 'warning', title: 'Nominee Details ', message: '' + "</br>Enter valid Nominee Mobile Number and name " });
                $("#nominee_mobile").focus();
                // 	$('#close_actionBtns').css('display','none');
            } else {
                //  $('#close_actionBtns').css('display', 'block');
            }
        } else {
            $('#close_actionBtns').css('display', 'none');
            if ($('#enable_closing_otp').val() == 0) {
                $("#nominee_block").css('display', 'block');
                $("#otp_block").css('display', 'none');
                $("#otp_status_block").css('display', 'none');
            } else {
                $("#nominee_block").css('display', 'none');
                $("#otp_block").css('display', 'block');
                $("#otp_status_block").css('display', 'block');
            }
            $("#nominee_mobile").prop("disabled", true);
            $("#nominee_name").prop("disabled", true);
        }
    });
    /*	$('#nominee_mobile').blur(function () {
    	   if($(this).val().length==10)
    	   {
    					$('#otp').prop('disabled',false);
    			$('#send_otp').prop('disabled',false);
    			$('#verify_otp').prop('disabled',false);
    	   }
    	   else
    	   {
    				  $('#otp').prop('disabled',true);
    			$('#send_otp').prop('disabled',true);
    			$('#verify_otp').prop('disabled',true);
    			$(this).val('');
    	   }
    });   */
    //****allow only numeric value and '.' start****
    $("#add_charges").keypress(function(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode != 46 && (charCode > 31 && (charCode < 48 || charCode > 57))) {
            return false;
        }
    });
    $("#detections").keypress(function(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode != 46 && (charCode > 31 && (charCode < 48 || charCode > 57))) {
            return false;
        }
    });
    //****allow only numeric value and '.' end****
    //**** 0 to 0.00 Start****
    $('#add_charges').on('keyup', function() {
        if ($('#add_charges').val() == '0') // || $('#add_charges').val()=='0')
        {
            $('#add_charges').val(0);
        }
    });
    $('#detections').on('keyup', function() {
        if ($('#detections').val() == '0') // || $('#detections').val()=='0')
        {
            $('#detections').val(parseFloat('0').toFixed(2));
        }
    });
    //**** 0 to 0.00 end****
    //****Benefits and Detections calculation start****
    $('#add_charges').on('blur', function() {
        if ($('#add_charges').val() == '') {
            $('#add_charges').val(0);
        }
    });
    /* $('#add_charges').on('change blur onfocus',function(){
    	 if($('#add_charges').val()!='' && $('#sch_typ').val()==0)
    		  {
    		   var c_bal = (parseFloat($('#closing_amount').val()) + parseFloat($('#benefits').val())) - (parseFloat($('#detections').val()) + parseFloat($('#bank_chgs').val()) + parseFloat($('#add_charges').val()));
    			   $('#closing_balance').val(parseFloat(c_bal).toFixed(2));
    	}
    });*/
    //added by Durga 21.06.2023 starts here
    $("#add_benefits,#add_charges").keyup(function() {
        $('#calc_blc').attr('disabled', false);
    });
    $('#detections').on('change blur onfocus', function() {
        if ($('#detections').val() != '' && $('#sch_typ').val() == 0) {
            var c_bal = (parseFloat($('#closing_amount').val()) + parseFloat($('#benefits').val())) - (parseFloat($('#detections').val()) + parseFloat($('#bank_chgs').val()) + parseFloat($('#add_charges').val()));
            $('#closing_balance').val(parseFloat(c_bal).toFixed(2));
        }
    });
    //****Benefits and Detections calculation end****
    // scheme group //
    //get_group_list();
    $('#branch_select').val('2').trigger('change');
});
// esakki 23-12
$("input[name='account[store_closing_balance_as]']").on('change', function() {
    // Get the selected value
    calculate_closing_balance();
});

function get_customers() {
    $(".overlay").css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/customer/get_customers',
        dataType: 'json',
        success: function(data) {
            var id_customer = $('#id_customer').val();
            $.each(data, function(key, item) {
                $('#customer_select').append(
                    $("<option></option>")
                    .attr("value", item.id)
                    .text(item.mobile + ' ' + item.name)
                );
            });
            $("#customer_select").select2({
                placeholder: "Enter mobile number",
                allowClear: true
            });
            $("#customer_select").select2("val", (id_customer != '' && id_customer > 0 ? id_customer : ''));
            $(".overlay").css("display", "none");
        }
    });
}
//on selecting drawee account
$('#customer_select')
    .on("change", function(e) {
        //console.log("change val=" + this.value);
        if (this.value != '') {
            $("#id_customer").val(this.value);
            get_customer_detail(this.value);
        }
    });

function get_schemes(customerId = "", id_branch = '') {
    $('#scheme').empty();
    $(".overlay").css('display', 'block');
    //RAHUL 03_12_25
    branchwise_scheme = $("#branchwise_scheme").val();
    login_branch = $("#login_branch").val();
    if (branchwise_scheme == 1 && login_branch == 1 && id_branch == '') {
        id_branch = $("#id_branch").val();
    }
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/scheme/get_schemes',
        dataType: 'json',
        data: {
            customerId: customerId,
            id_branch: id_branch
        },
        success: function(data) {
            var selectid = $('#scheme_val').val();
            schCodes = data;
            console.log(schCodes);
            if ($('#scheme').hasClass("select2-hidden-accessible")) {
                $('#scheme').select2('destroy');
            }
            $("#scheme").empty();
            $.each(data, function(key, data) {
                var name = (data.name.concat(data.active == 0 ? '(Inactive)' : ''));
                $('#scheme').append(
                    $("<option></option>")
                    .attr("value", data.id_scheme)
                    .text(name)
                );
            });
            $("#scheme").select2({
                placeholder: "Enter scheme",
                allowClear: true
            });
            $("#scheme").select2("val", (selectid != null && selectid > 0 ? selectid : ''));
            $(".overlay").css('display', 'none');
        }
    });
}
$('#scheme').on('change', function() {
    if ($('#scheme').val() != null) {
        var scheme = schCodes.filter(function(schCode) {
            return schCode.id_scheme == $('#scheme').val();
        });
        console.log(scheme[0].is_pan_required);
        $('#pan_req_amt').val(scheme[0].pan_req_amt);
        $('#is_pan_required').val(scheme[0].is_pan_required);
        $('#get_amt_in_schjoin').val(scheme[0].get_amt_in_schjoin);
        var firstPayment_amt = parseInt($('#firstPayment_amt').val());
        if (scheme[0].is_pan_required == '1' && scheme[0].pan_req_amt != 0 && scheme[0].pan_req_amt != '' && scheme[0].pan_req_amt != null && firstPayment_amt > scheme[0].pan_req_amt) {
            $('div#panimage').css('display', 'block');
            $('div#pan_number').css('display', 'block');
            $('#bank-file-input').prop('required', true);
            $('#pan_no').prop('required', true);
            $('#pan_no').prop('minlength', 10);
        } else {
            $('div#panimage').css('display', 'none');
            $('div#pan_number').css('display', 'none');
            $('#bank-file-input').prop('required', false);
            $('#pan_no').prop('required', false);
            $('#pan_no').prop('minlength', 0);
        }
        if (scheme[0].scheme_type == 3 && (scheme[0].get_amt_in_schjoin) == 1) {
            $('#get_amt').css('display', 'block');
        } else {
            $('#firstPayment_amt').val('');
            $('#get_amt').css('display', 'none');
        }
    }
});

function get_customer_detail(id) {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/customer/get_customer/' + id,
        dataType: 'json',
        success: function(data) {
            set_customer(data);
            // Call get_schemes after branch is set so schemes are filtered by customer's branch
            get_schemes(id, data.id_branch || '');
        }
    });
}

function get_scheme_detail(id) {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/scheme/get_scheme/' + id + '/' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function(data) {
            //checking has_voucher data ,if 1 enable voucher block starts here
            if (data.has_voucher == 1) {
                $("#voucher_info_block").css("display", "block");
            } else {
                $("#voucher_info_block").css("display", "none");
            }
            //checking has_voucher data ,if 1 enable voucher block ends here
            if (data.sch_limit_value != null && data.sch_limit == 1) {
                if (data.sch_joined_acc >= data.sch_limit_value) {
                    alert('maximum scheme group limit reached');
                    window.location.reload(true);
                }
            }
            if (data.sch_type == 3 && (data.flexible_sch_type == 1 || data.flexible_sch_type == 2) && $('#get_amt_in_schjoin').val() == 1) {
                $('#firstPayment_amt').prop('required', true);
                $('#flx_denomintion').val(data.flx_denomintion);
                $('#min_amount').val(data.min_amount);
                $('#max_amount').val(data.max_amount);
            } else {
                $('#firstPayment_amt').prop('required', false);
            }
            set_scheme(data);
            $("#scheme_type").val(data.scheme_type);
            prev_bal_edit();
            //lines added by Durga (Gopal Code) 09.05.2023 starts here
            if (data.get_amt_in_schjoin == 1 && data.firstPayamt_as_payamt == 1 || data.firstPayamt_maxpayable == 1) {
                pan_req_amt = Math.trunc(data.pan_req_amt);
                var firstpayment_amt = $('#firstPayment_amt').val()
                if (firstpayment_amt >= pan_req_amt && data.pan_req_amt != 0 && data.is_pan_required == 1) {
                    $('#pan_number').show()
                    $('#panimage').show()
                } else {
                    $('#pan_number').hide()
                    $('#panimage').hide()
                }
            } else if ((data.sch_type != 3 && data.sch_type != 1) && data.tot_sch_amount >= data.pan_req_amt && data.pan_req_amt != 0 && data.is_pan_required == 1) {
                $('#pan_number').show();
                $('#panimage').show();
            } else {
                $('#pan_number').hide()
                $('#panimage').hide()
            }
            // Lines added by Gopal 01-02-2024 starts here...scheme Approval
            if (data.sch_approval == 1) {
                $('#active').val(2)
                $('#sch_approval').val(1)
                    //scheme Approval
            }
            // Lines added by Gopal 01-02-2024	 Ends here 
            //lump scheme starts...
            console.log(data);
            $('#total_installments').val(data.total_installments);
            $('#is_lumpSum').val(data.is_lumpSum);
            $('#lumpwgt_slabs').val(data.lump_joined_weight);
            $('#lumpwgt_slabs_block').css('display', 'none');
            if (data.is_lumpSum == 1) {
                $('#get_amt').css('display', 'none');
                $('#firstPayment_amt').val('');
                $.each(data.lumpwgt_slabs, function(key, item) {
                    $('#lumpwgt_slabs').append(
                        $("<option></option>")
                        .attr("value", item.weight)
                        .text(item.weight)
                    );
                });
                $("#lumpwgt_slabs").select2({
                    placeholder: "Select Weight",
                    allowClear: true
                });
                $("#lumpwgt_slabs").select2("val", ($('#lump_joined_weight').val() != null ? $('#lump_joined_weight').val() : ''));
                $('#lumpwgt_slabs_block').css('display', 'block');
                if (data.paid_installments > 0) {
                    $('#lumpwgt_slabs_block').prop('disabled', 'true');
                }
            }
            //lump scheme ends...
            //lines added by Durga (Gopal Code) 09.05.2023 ends here
            /* TopUp Scheme Starts Esakki_20250802 */
            if (data.is_topup_scheme == 1) {
                $('#topup_sch').css('display', 'block');
                if (ctrl_page[0] == 'account' && ctrl_page[1] == 'add') {
                    $('#topup_rate').val(data.metal_rate);
                }
                $('#topup_booking_type').val(data.topup_booking_type);
                $('#topup_min_value').val(data.topup_min_value);
                $('#topup_max_value').val(data.topup_max_value);
                $('#topup_denomination').val(data.topup_denomination);
                $("#topup_weight,#topup_amount,#topup_rate").prop("required", true);
                if (data.topup_booking_type == 1) {
                    $('#topup_weight').prop('readonly', true);
                    msg_txt = "Min Amount : " + data.topup_min_value + "; Max Amount : " + data.topup_max_value + "; Denomination : " + data.topup_denomination;
                    $('#topup_msg').text(msg_txt);
                } else if (data.topup_booking_type == 2) {
                    $('#topup_amount').prop('readonly', true);
                    msg_txt = "Min Weight : " + data.topup_min_value + "; Max Weight : " + data.topup_max_value + "; Denomination : " + data.topup_denomination;
                    $('#topup_msg').text(msg_txt);
                }
            } else {
                $("#topup_weight,#topup_amount,#topup_rate").prop("required", false);
            }
            /* TopUp Scheme Ends Esakki_20250802 */
        }
    });
}
//lump scheme starts...
$('#lumpwgt_slabs').on('change', function() {
    $('#lump_joined_weight').val($(this).val());
});
//lump scheme ends...
$('#firstPayment_amt').on('change', function() {
    var pay_amt = $(this).val();
    var flx_denomintion = parseInt($('#flx_denomintion').val());
    var min_amount = parseInt($('#min_amount').val());
    var max_amount = parseInt($('#max_amount').val());
    var pan_req_amt = parseInt($('#pan_req_amt').val());
    var is_pan_required = parseInt($('#is_pan_required').val());
    if (flx_denomintion != '') {
        if ((pay_amt >= min_amount) && (pay_amt <= max_amount)) {
            if ((pay_amt % flx_denomintion) != 0) {
                alert('Please Enter a Amount in Multiples of ' + flx_denomintion + '');
                $("#firstPayment_amt").val('');
            } else {
                $validAmt = true;
            }
        } else {
            alert('Your Amount Shold be Minimum' + min_amount + ' and Maximum ' + max_amount + '');
            $("#firstPayment_amt").val('');
        }
    }
    if (is_pan_required == 1 || is_pan_required == 2) {
        if (pan_req_amt != 0 && pan_req_amt != '' && pan_req_amt != null && pay_amt >= pan_req_amt) {
            $('div#panimage').css('display', 'block');
            $('div#pan_number').css('display', 'block');
            $('#bank-file-input').prop('required', true);
            $('#pan_no').prop('required', true);
            $('#pan_no').prop('minlength', 10);
        } else {
            $('div#panimage').css('display', 'none');
            $('div#pan_number').css('display', 'none');
            $('#bank-file-input').prop('required', false);
            $('#pan_no').prop('required', false);
            $('#pan_no').prop('minlength', 0);
        }
    }
});

function prev_bal_edit() {
    var scheme_type = $("#scheme_type").val();
    if ($("#is_opening").prop('checked') == true) {
        if (scheme_type == "Weight") {
            $("#paid_installments").prop('disabled', false);
            $("#balance_amount").prop('disabled', false);
            $("#last_paid_date").prop('disabled', false);
            $("#balance_weight").prop('disabled', false);
            $("#last_paid_weight").prop('disabled', false);
            $("#last_paid_chances").prop('disabled', false);
        } else if (scheme_type == "Amount to Weight") {
            $("#paid_installments").prop('disabled', false);
            $("#balance_amount").prop('disabled', false);
            $("#last_paid_date").prop('disabled', false);
            $("#balance_weight").prop('disabled', false);
            $("#last_paid_weight").prop('disabled', false);
            $("#last_paid_chances").prop('disabled', false);
        } else if (scheme_type == "Amount") {
            $("#paid_installments").prop('disabled', false);
            $("#balance_amount").prop('disabled', false);
            $("#last_paid_date").prop('disabled', false);
            $("#balance_weight").prop('disabled', true);
            $("#last_paid_weight").prop('disabled', true);
            $("#last_paid_chances").prop('disabled', true);
        } else {
            $("#paid_installments").prop('disabled', true);
            $("#balance_amount").prop('disabled', true);
            $("#last_paid_date").prop('disabled', true);
            $("#balance_weight").prop('disabled', true);
            $("#last_paid_weight").prop('disabled', true);
            $("#last_paid_chances").prop('disabled', true);
        }
    } else {
        $("#paid_installments").prop('disabled', true);
        $("#balance_amount").prop('disabled', true);
        $("#last_paid_date").prop('disabled', true);
        $("#balance_weight").prop('disabled', true);
        $("#last_paid_weight").prop('disabled', true);
        $("#last_paid_chances").prop('disabled', true);
    }
}

function cus_image() {
    var id_customer = $.trim($("#id_customer").val());
    var default_img = base_url + 'assets/img/default.png';
    var img_url = base_url + 'assets/img/customer/' + id_customer + '/customer.jpg';
    var cus_img_src = default_img;
    imageExists(img_url, function(exists) {
        if (exists) {
            cus_img_src = img_url;
        }
        $('#cus_img').attr('src', cus_img_src);
    });
    $('#cus_img').attr("src", cus_img_src);
}

function imageExists(url, callback) {
    var img = new Image();
    img.onload = function() { callback(true); };
    img.onerror = function() { callback(false); };
    img.src = url;
}

function set_customer(data) {
    // Auto-select customer's branch in branch dropdown
    if (data.id_branch != null && data.id_branch != '' && data.id_branch > 0) {
        if ($('#branch_select').length > 0) {
            $('#branch_select').val(data.id_branch).trigger('change');
        }
        $('#id_branch').val(data.id_branch);
    }

    var ac_name = $('#account_name').val();
    var address = (data.address ? data.address : "") + (data.address1 ? data.address1 + ", " : "") + (data.city ? data.city : "") + (data.pincode ? " - " + data.pincode : "");
    var mobile = (data.mobile ? data.mobile : "-");
    var img_url = base_url + 'assets/img/customer/' + data.id_customer + "/";
    var default_img = base_url + 'assets/img/default.png';
    var cus_img_src = (data.cus_img != null ? base_url + data.cus_img : default_img);
    var name = (ac_name == null ? (data.firstname ? __capitalizeString(data.firstname) : "") + " " + (data.lastname ? __capitalizeString(data.lastname) : "") : ac_name);
    if (ctrl_page[1] == 'add' && ctrl_page[0] == 'account' && $('#cusName_edit').val() == 0) { // esakki 11-11
        $('#cus_name').val(data.firstname);
    }
    if (ctrl_page[1] == 'add' && ctrl_page[0] == 'account' && $('#cusName_edit').val() == 1) {
        $('#account_name').val(data.name);
    }
    /* if(ctrl_page[1] == 'add'&& ctrl_page[0] == 'account')
      {
     if(data.referal_code!=null){
    	$('#referal_code').val(data.referal_code!=''?data.referal_code:'');
    	$('#referalcode_val').val(data.mobile);
       $('div#referalcode').css('display','none');
      }else{
    	   $('#referalcode_val').val(data.mobile);
    	   $('div#referalcode').css('display','block');
    	   $('#referal_code').val('');
    	}
      }else if(ctrl_page[1] == 'edit' && ctrl_page[0] == 'account'){
    	 $('#referal_code').attr('disabled', true);
      }*/
    if (ctrl_page[1] == 'edit' && ctrl_page[0] == 'account') {
        // $('#referal_code').attr('disabled', true);  // esakki ref
        $('.referalcode').css('display', 'none');
    }
    $('#cus_address').text((address ? address : "-"));
    $('#cus_mobile').text(mobile);
    $('#cus_img').attr("src", cus_img_src);
    // $('#account_name').val(name);
}

function set_scheme(data) {
    var sch_type = data.scheme_type;
    var sch_code = (data.code ? data.code : "-");
    var sch_duration = (data.total_installments ? data.total_installments : "-");
    var pay_type = (data.payment_type ? data.payment_type : "-");
    var amount = (data.amount != "" && data.amount != null && data.amount != undefined ? data.amount : '0.00') // esakki 11-11
    var interest = (data.interest ? data.interest : "-");
    var tax = (data.tax ? data.tax : "-");
    var min_weight = (data.min_weight ? data.min_weight : "-");
    var max_weight = (data.max_weight ? data.max_weight : "-");
    var min_chance = (data.min_chance ? data.min_chance : "-");
    var max_chance = (data.max_chance ? data.max_chance : "-");
    var html = __label2("Scheme Type", sch_type)
    html += __label2("Scheme Code", sch_code);
    html += __label2("Installments", sch_duration);
    html += __label2("Payment Type", pay_type);
    if (pay_type = "Multiple") {
        html += __label2("Payment Chance", max_chance);
    }
    if (sch_type == 'Weight') {
        html += __label2("Min Weight", min_weight);
        html += __label2("Max Weight", max_weight);
    } else {
        html += __label2("Amount", amount);
    }
    html += __label2("Interest", interest);
    html += __label2("Tax", tax)
    $('#sch_content').html(html);
}
//get cus mobile no// hh
if (ctrl_page[1] == 'new') {
    get_schemename();
    $('#mobilenumber').on('keyup', function() {
        var mobile = $('#mobilenumber').val();
        if (mobile.length == 0) {
            $("#id_customer").val('');
        }
    });
}
if (ctrl_page[1] == 'new') {
    $("#mobilenumber").autocomplete({
        source: function(request, response) {
            var mobile = $("#mobilenumber").val();
            my_Date = new Date();
            $.ajax({
                url: base_url + "index.php/admin_customer/ajax_get_customers_list?nocache=" + my_Date.getUTCSeconds(),
                dataType: "json",
                type: 'POST',
                data: { 'mobile': mobile },
                success: function(data) {
                    var data = JSON.stringify(data);
                    data = JSON.parse(data);
                    var cus_list = new Array(data.length);
                    var i = 0;
                    data.forEach(function(entry) {
                        console.log(entry.mobile);
                        var customer = {
                            label: entry.mobile + '  ' + entry.firstname,
                            value: entry.id_customer
                        };
                        cus_list[i] = customer;
                        i++;
                    });
                    response(cus_list);
                }
            });
        },
        minLength: 4,
        delay: 300,
        select: function(e, i) {
            e.preventDefault();
            $("#mobilenumber").val(i.item.label);
            $("#id_customer").val(i.item.value);
            //$("#id_scheme_account").val(i.item.id_scheme_account);
            $('.overlay').css('display', 'block');
            if ($('#id_customer').val() != '') {
                var from_date = $('#account_list1').text();
                var to_date = $('#account_list2').text();
                var id_customer = $('#id_customer').val();
                var id_branch = $('#id_branch').val();
                var id_scheme = $('#id_scheme').val();
                get_scheme_acc_list(from_date, to_date, id_branch, id_customer, id_scheme);
            }
        },
        response: function(e, i) {
            // ui.content is the array that's about to be sent to the response callback.
            if (i.content.length === 0) {
                alert('Please Enter a valid Number');
                $('#mobilenumber').val('');
            }
        },
    });
}
//scheme account list not closed
function get_scheme_acc_list(from_date = "", to_date = "", id_branch = "", id_customer = "", id_scheme = '') {
    $("#manage_account_date").text(from_date + " To " + to_date);
    my_Date = new Date();
    var type = $('#date_Select').find(":selected").val();
    $("div.overlay").css("display", "block");
    var join_days = $('#join_day').find(":selected").val();
    var group_code = $('#group_select').val();
    $.ajax({
        url: base_url + "index.php/account/get/ajax_account_list?nocache=" + my_Date.getUTCSeconds(),
        //data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_customer':id_customer,'type':type,'join_days':join_days}: ''),
        data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date, 'id_branch': $('#id_branch').val(), 'id_scheme': $('#id_scheme').val(), 'id_customer': id_customer, 'type': type, 'join_days': join_days, 'group_code': group_code } : ''),
        dataType: "JSON",
        type: "POST",
        success: function(data) {
            set_scheme_acc_list(data);
            $('body').addClass("sidebar-collapse");
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}

function set_scheme_acc_list(data) {
    var account = data.data;
    var access = data.access;
    var close_acc = 1;
    $('#total_accounts').text(account.length);
    $('#total_acc').text(account.length);
    if (access.add == '0') {
        $('#add').attr('disabled', 'disabled');
    }
    var oTable = $('#sch_acc_list').DataTable();
    var id_branch = $("#branch_select option:selected").text();
    oTable.clear().draw();
    if (account != null && account.length > 0) {
        var acc_listDate1 = $('#account_list1').text();
        var acc_listDate2 = $('#account_list2').text();
        // Convert date strings to Date objects
        var date1 = new Date(acc_listDate1);
        var date2 = new Date(acc_listDate2);
        var branch_name = getBranchTitle();
        var title = '';
        title += get_title(formatDate(date1), formatDate(date2), 'Scheme Account List - ' + branch_name);
        var excel_title = "";
        excel_title += get_excel_title('', '', 'Scheme Account List');
        var schemeacc_no_set = (typeof data.data == 'undefined' ? '' : data.data[0].schemeacc_no_set);
        // console.log(schemeacc_no_set);
        if ((schemeacc_no_set == 0 || schemeacc_no_set == 2)) {
            let columns = [{
                    "mDataProp": function(row, type, val, meta) {
                        var url = base_url + 'index.php/reports/payment/account/' + row.id_scheme_account;
                        action = ((row.status == 2) ? '<input  class="sch_approve_acc"  type="checkbox" name="account_approval[]" value="' + row.id_scheme_account + '">' : '') + '<a href="' + url + '" target="_blank">' + row.id_scheme_account + '</a>';
                        return action;
                    }
                },
                { "mDataProp": "id_customer" },
                { "mDataProp": "name" },
                { "mDataProp": "mobile" },
                { "mDataProp": "branch_name" },
                { "mDataProp": "account_name" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return row.code;
                    }
                },
                /*  { "mDataProp": function ( row, type, val, meta ){
                				if(row.is_lucky_draw == 1){
                					return row.scheme_group_code+' '+row.scheme_acc_number;
                				}
                				else{ 
                					if(row.schemeaccNo_displayFrmt == 0){   //only acc num
                						return row.scheme_acc_number;
                					}else if(row.schemeaccNo_displayFrmt == 1){ //based on acc number generation setting
                						if(row.scheme_wise_acc_no==0){
                							return row.scheme_acc_number;
                						}else if(row.scheme_wise_acc_no==1){
                							return row.acc_branch+'-'+row.scheme_acc_number;
                						}else if(row.scheme_wise_acc_no==2){
                							return row.code+'-'+row.scheme_acc_number;
                						}else if(row.scheme_wise_acc_no==3){
                							return row.code+''+row.acc_branch+'-'+row.scheme_acc_number;
                						}else if(row.scheme_wise_acc_no==4){
                							return row.start_year+'-'+row.scheme_acc_number;
                						}else if(row.scheme_wise_acc_no==5){
                							return row.start_year+''+row.code+'-'+row.scheme_acc_number;
                						}else if(row.scheme_wise_acc_no==6){
                							return row.start_year+''+row.code+''+row.acc_branch+'-'+row.scheme_acc_number;
                						}
                					}else if(row.schemeaccNo_displayFrmt == 2){  //customised
                						return row.scheme_acc_number;
                					}
                				}
                			}},  */
                { "mDataProp": "scheme_acc_number" },
                //{ "mDataProp": "group_code" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.group_code != '') {
                            return row.group_code;
                        } else {
                            return '-';
                        }
                    }
                },
                { "mDataProp": "is_new" },
                { "mDataProp": "start_date" },
                { "mDataProp": "custom_entry_date" },
                /*	{ "mDataProp": function ( row, type, val, meta ){
                	if(row.edit_custom_entry_date==0){
                	return row.start_date;
                	}
                	else{
                	return row.custom_entry_date;
                	}
                	}},   */
                //	{ "mDataProp": "maturity_days" },
                { "mDataProp": "last_paid_date" },
                { "mDataProp": "scheme_type" },
                /*	{ "mDataProp": function ( row, type, val, meta ){
                		amount=row.currency_symbol+" "+row.amount;
                		weight="Max "+row.payable+" g/month";
                		if(row.scheme_types == 0 || row.scheme_types == 2 || row.scheme_types == 3 && row.flexible_sch_type == 1 || row.flexible_sch_type == 2)
                		{ 
                			return amount; 
                		}
                		if(row.scheme_types == 1 || row.scheme_types == 3 && row.flexible_sch_type == 3 || row.flexible_sch_type == 4 || row.flexible_sch_type == 5)
                		{
                			return weight; 
                		}   
                	  }
                	},   */
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.is_lumpSum == 1 && row.lump_joined_weight > 0 && row.lump_payable_weight > 0) {
                            return '<b>Joined Wgt:</b> ' + row.lump_joined_weight + ' g <br/> <b>Payable:</b> ' + row.lump_payable_weight + ' g/ins';
                        } else {
                            amount = row.currency_symbol + " " + inr_format.format(row.max_amount);
                            weight = "Max " + row.max_weight + " g/month";
                            if (row.scheme_types == 2) {
                                return row.currency_symbol + " " + inr_format.format(row.amount);
                            }
                            if (row.scheme_types == 0) {
                                return row.currency_symbol + " " + inr_format.format(row.payable);
                            }
                            // if(row.scheme_types == 2 || row.scheme_types == 3 && row.flexible_sch_type == 1 || row.flexible_sch_type == 2)
                            // { 
                            //     return amount; 
                            // }
                            // if(row.scheme_types==2)
                            // {
                            // 	return amount;
                            // }
                            if (row.scheme_types == 3 && (row.flexible_sch_type == 1 || row.flexible_sch_type == 2)) {
                                if ((row.firstPayamt_as_payamt == 1 || row.firstPayamt_maxpayable == 1) && row.firstPayment_amt != '' && row.firstPayment_amt != null) {
                                    return row.currency_symbol + " " + inr_format.format(row.firstPayment_amt);
                                } else {
                                    return "Max : " + row.currency_symbol + " " + inr_format.format(row.max_amount);
                                }
                            }
                            if (row.scheme_types == 3 && (row.flexible_sch_type == 6 || row.flexible_sch_type == 7)) {
                                return 'Partly Flexible';
                            }
                            if (row.scheme_types == 1 || row.scheme_types == 3 && row.flexible_sch_type == 3 || row.flexible_sch_type == 4 || row.flexible_sch_type == 5) {
                                return weight;
                            }
                        }
                    }
                },
                { "mDataProp": "pan_no" },
                //{ "mDataProp":"paid_installments"},
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.show_ins_type == 1) {
                            return row.paid_installments + "/" + row.total_installments;
                        } else {
                            return row.paid_installments;
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return row.general_advance != 0 && row.general_advance != '' ? inr_format.format(row.general_advance) : '-';
                    }
                },
                //	{ "mDataProp":"gift_article"},
                /*{ "mDataProp": function ( row, type, val, meta ){
                	if(row.gift_article!=null && row.gift_article!='')
                	{
                		return row.gift_article;
                	}
                	else
                	{
                		return '-';
                	}
                }},*/
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.issue_self_giftBonus == 1) {
                            return 'Gift';
                        } else if (row.issue_self_giftBonus == 0) {
                            return 'Bonus';
                        } else if (row.issue_self_giftBonus == 2) {
                            if (row.gift_article != null && row.gift_article != '') {
                                return row.gift_article;
                            } else {
                                return '-';
                            }
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        // 	active_url =base_url+"index.php/account/status/"+(row.active=='Active'?0:1)+"/"+row.id_scheme_account; 
                        active_url = base_url + "index.php/account/status/" + (row.active == 'Active' ? 0 : (row.active == 'under_approval' ? 2 : 1)) + "/" + row.id_scheme_account;
                        // 	return "<a href='"+active_url+"'><i class='fa "+(row.active=='Active'?'fa-check':'fa-remove')+"' style='color:"+(row.active=='Active'?'green':'red')+"'></i></a>"
                        if (row.active == 'under_approval') {
                            return "Under Approval"
                        } else {
                            return "<a href='" + active_url + "'><i class='fa " + (row.active == 'Active' ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.active == 'Active' ? 'green' : 'red') + "'></i></a>"
                        }
                    }
                },
                //DGS-DCNM...
                {
                    "mDataProp": function(row, type, val, meta) {
                        wallet_url = base_url + "index.php/admin_manage/chit_detail_report/" + row.id_scheme_account;
                        if (row.show_wallet > 0 && row.scheme_acc_number != 'Not Allocated') {
                            return '<button type="button" class="btn btn-default" data-href="#" data-toggle="modal" data-target="#chit_wallet_screen" onclick="set_chit_wallet_screen(' + row.id_scheme_account + ')">DIGI WALLET</button>';
                        } else {
                            return '-';
                        }
                    }
                },
                //DGS-DCNM...
                {
                    "mDataProp": function(row, type, val, meta) {
                        return (row.added_by == '0' ? "Web" : (row.added_by == '1' ? "Admin" : (row.added_by == '2' ? "Mobile" : (row.added_by == '3' ? "Collection App" : (row.added_by == '4' ? "Retail" : (row.added_by == '5' ? "Sync" : (row.added_by == '6' ? "Import" : "-")))))));
                    }
                },
                { "mDataProp": "created_by" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.one_time_premium == 1 && row.firstPayment_amt > 0 && row.otp_price_fix_type == 1 && row.fixed_metal_rate == null) {
                            return '<button type="button"  class="btn btn-primary" style="padding: 4px; margin-bottom: -14px;" onClick="otp_model(' + row.id_scheme_account + ',' + row.mobile + ')">Fix Rate</button>';
                        }
                        if (row.fixed_metal_rate != null) {
                            return ' Rate Fixed ';
                        } else {
                            return ' - '
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.total_saved_wgt > 0) {
                            return row.total_saved_wgt;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.total_saved_benefit > 0) {
                                return formatMetalWeight(row.total_saved_benefit);
                        } else {
                            return '-';
                        }
                    }
                },
                { "mDataProp": "emp_name" },
                { "mDataProp": "referal_code" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.agent_code != null) {
                            return row.agent_name + "  " + row.agent_code;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.duplicate_passbook_issued != null && row.duplicate_passbook_issued != '') {
                            if (row.duplicate_passbook_issued == 1) {
                                return 'Duplicate';
                            } else if (row.duplicate_passbook_issued == 2) {
                                return 'Original';
                            } else {
                                return '-';
                            }
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        id = row.id_scheme_account;
                        close_url = '';
                        print_url = base_url + 'index.php/admin_manage/receipt_account/' + id; //15-12-2022(customer detail but)
                        // 09-12-2022 print pass book back start
                        print_passbook_url = base_url + 'index.php/admin_manage/passbook_print/B/' + id;
                        print_passbook_btn = '<li><a href="' + print_passbook_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print Passbook</a></li>';
                        scheme_receipt_url = (row.one_time_premium == 1 && row.otp_price_fix_type == 1 ? base_url + 'index.php/admin_manage/get_scheme_receipt/' + id : '#');
                        edit_url = (access.edit == '1' ? base_url + 'index.php/account/edit/' + id : '#');
                        close_url = (close_acc == '1' && row.paid_installments >= 1 ? (row.one_time_premium == 1 && row.otp_price_fix_type == 1 && row.fixed_metal_rate == null ? '#' : base_url + 'index.php/account/close/scheme/' + id) : '#');
                        delete_url = (access.delete == '1' ? base_url + 'index.php/account/delete/' + id : '#');
                        delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                        action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                            '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
                            '<li>' + (access.allow_acc_closing == 1 ? ((row.one_time_premium == 1 && row.otp_price_fix_type == 1 && row.fixed_metal_rate == null) ? '<a class="rate_fix_warning"><i class="fa fa-close" ></i> Close</a>' : '<a href="' + close_url + '" class="btn-edit"><i class="fa fa-close" ></i> Close</a>') : '') + '</li>' +
                            (row.one_time_premium == 1 && row.otp_price_fix_type == 1 && row.paid_installments > 0 ? '<li><a href="' + scheme_receipt_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print</a></li>' : '') +
                            '<li><a href="' + print_passbook_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i>Passbook Print</a></li>' +
                            //'<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li>''; old code 15-12-2022
                            //New code
                            '<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li>' + '<li><a href="' + print_url + '" target="_blank"><i class="fa fa-print"></i> RC Print</a></li>';
                        return action_content;
                    }
                }
            ];
            if ($("#old_sch_acc_num").val() == "1") {
                columns.splice(3, 0, {
                    mDataProp: "old_scheme_acc_number",
                    visible: true
                });
            }
            oTable = $('#sch_acc_list').dataTable({
                columnDefs: [{
                    "defaultContent": "-",
                    "targets": "_all"
                }],
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": false,
                "order": [
                    [0, "desc"]
                ],
                "dom": 'lBfrtip',
                "buttons": [{
                        extend: 'print',
                        title: '',
                        customize: function(win) {
                            $(win.document.body)
                                .prepend(title);
                            $(win.document.body).find('table')
                                .addClass('compact');
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', '10px')
                                .css('font-family', 'sans-serif');
                            // 	 $(win.document.body).find('tr:nth-child(odd) td').each(function(index){
                            // 		 $(this).css('font-weight','bold');
                            // 	 });
                        },
                        exportOptions: { columns: ':visible' },
                    },
                    {
                        extend: 'excel',
                        footer: true,
                        title: 'Scheme Account List',
                        messageTop: excel_title,
                    },
                ],
                "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
                "aaData": account,
                "order": [
                    [0, "desc"]
                ],
                // "columnDefs":
                // 	[
                // 		{
                // 			targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28],
                // 			className: 'dt-left'
                // 		},
                // 		{
                // 			targets: [16, 17],
                // 			className: 'dt-right'
                // 		},
                // 	],
                "aoColumns": columns
            });
        } else {
            let column = [{
                    "mDataProp": function(row, type, val, meta) {
                        if ((row.scheme_acc_number == 'Not Allocated' && row.paid_installments == 1 && row.schemeacc_no_set == 1)) {
                            return '<input type="checkbox" id="select_ids_' + row.id_scheme_account + '" class="select_ids"  value="' + row.id_scheme_account + '">';
                        } else {
                            return '<input type="hidden" id="select_ids_' + row.id_scheme_account + '" class="select_ids" disabled="true" value="' + row.id_scheme_account + '">';
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        var url = base_url + 'index.php/reports/payment/account/' + row.id_scheme_account;
                        action = '<a href="' + url + '" target="_blank">' + row.id_scheme_account + '</a>';
                        return action;
                    }
                },
                { "mDataProp": "id_customer" },
                { "mDataProp": "name" },
                { "mDataProp": "mobile" },
                { "mDataProp": "branch_name" },
                { "mDataProp": "account_name" },
                { "mDataProp": "code" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if ((row.scheme_acc_number == 'Not Allocated' && row.paid_installments == 1 && row.schemeacc_no_set == 1)) {
                            return '<input  type="number"  id="id_schemeaccount" class="schemeaccount"  disabled="true" value="">';
                        } else {
                            return row.scheme_acc_number;
                        }
                    }
                },
                //	{ "mDataProp": "group_code" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.group_code != '') {
                            return row.group_code;
                        } else {
                            return '-';
                        }
                    }
                },
                { "mDataProp": "is_new" },
                { "mDataProp": "start_date" },
                { "mDataProp": "custom_entry_date" },
                /*	{ "mDataProp": function ( row, type, val, meta ){
                	if(row.edit_custom_entry_date==0){
                	return row.start_date;
                	}
                	else{
                	return row.custom_entry_date;
                	}
                	}},   */
                //	{ "mDataProp": "maturity_days" },
                { "mDataProp": "last_paid_date" },
                { "mDataProp": "scheme_type" },
                /*{ "mDataProp": function ( row, type, val, meta ){
                amount=row.currency_symbol+" "+row.amount;
                weight="Max "+row.amount+" g/month";
                if(row.scheme_types == '0')
                {
                return amount;
                }
                else if(row.scheme_types == '1')
                {
                return weight;
                }
                else if(row.scheme_types == '3')
                {
                return amount;
                }
                else if(row.scheme_types=='2')
                {
                return amount;
                }
                else(row.scheme_types=='')
                }
                },*/
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.is_lumpSum == 1 && row.lump_joined_weight > 0 && row.lump_payable_weight > 0) {
                            return '<b>Joined Wgt:</b> ' + row.lump_joined_weight + ' g <br/> <b>Payable:</b> ' + row.lump_payable_weight + ' g/ins';
                        } else {
                            amount = row.currency_symbol + " " + inr_format.format(row.max_amount);
                            weight = "Max " + row.max_weight + " g/month";
                            if (row.scheme_types == 2) {
                                return row.currency_symbol + " " + inr_format.format(row.amount);
                            }
                            if (row.scheme_types == 0) {
                                return row.currency_symbol + " " + inr_format.format(row.payable);
                            }
                            // if(row.scheme_types == 2 || row.scheme_types == 3 && row.flexible_sch_type == 1 || row.flexible_sch_type == 2)
                            // { 
                            //     return amount; 
                            // }
                            // if(row.scheme_types==2)
                            // {
                            // 	return amount;
                            // }
                            if (row.scheme_types == 3 && (row.flexible_sch_type == 1 || row.flexible_sch_type == 2)) {
                                if ((row.firstPayamt_as_payamt == 1 || row.firstPayamt_maxpayable == 1) && row.firstPayment_amt != '' && row.firstPayment_amt != null) {
                                    return row.currency_symbol + " " + inr_format.format(row.firstPayment_amt);
                                } else {
                                    return "Max : " + row.currency_symbol + " " + inr_format.format(row.max_amount);
                                }
                            }
                            if (row.scheme_types == 3 && (row.flexible_sch_type == 6 || row.flexible_sch_type == 7)) {
                                return 'Partly Flexible';
                            }
                            if (row.scheme_types == 1 || row.scheme_types == 3 && row.flexible_sch_type == 3 || row.flexible_sch_type == 4 || row.flexible_sch_type == 5) {
                                return weight;
                            }
                        }
                    }
                },
                { "mDataProp": "pan_no" },
                { "mDataProp": "paid_installments" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return row.general_advance != 0 && row.general_advance != '' ? inr_format.format(row.general_advance) : '-';
                    }
                },
                //	{ "mDataProp":"gift_article"},
                /*{ "mDataProp": function ( row, type, val, meta ){
                			if(row.gift_article!=null && row.gift_article!='')
                			{
                				return row.gift_article;
                			}
                			else
                			{
                				return '-';
                			}
                		}},*/
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.issue_self_giftBonus == 1) {
                            return 'Gift';
                        } else if (row.issue_self_giftBonus == 0) {
                            return 'Bonus';
                        } else if (row.issue_self_giftBonus == 2) {
                            if (row.gift_article != null && row.gift_article != '') {
                                return row.gift_article;
                            } else {
                                return '-';
                            }
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        active_url = base_url + "index.php/account/status/" + (row.active == 'Active' ? 0 : 1) + "/" + row.id_scheme_account;
                        return "<a href='" + active_url + "'><i class='fa " + (row.active == 'Active' ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.active == 'Active' ? 'green' : 'red') + "'></i></a>"
                    }
                },
                //DGS-DCNM...
                {
                    "mDataProp": function(row, type, val, meta) {
                        wallet_url = base_url + "index.php/admin_manage/chit_detail_report/" + row.id_scheme_account;
                        if (row.show_wallet > 0 && row.scheme_acc_number != 'Not Allocated') {
                            return '<button type="button" class="btn btn-default" data-href="#" data-toggle="modal" data-target="#chit_wallet_screen" onclick="set_chit_wallet_screen(' + row.id_scheme_account + ')">DIGI WALLET</button>';
                        } else {
                            return '-';
                        }
                    }
                },
                //DGS-DCNM...
                {
                    "mDataProp": function(row, type, val, meta) {
                        return (row.added_by == '0' ? "Web" : (row.added_by == '1' ? "Admin" : (row.added_by == '2' ? "Mobile" : (row.added_by == '3' ? "Collection App" : (row.added_by == '4' ? "Retail" : (row.added_by == '5' ? "Sync" : (row.added_by == '6' ? "Import" : "-")))))));
                    }
                },
                { "mDataProp": "created_by" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.one_time_premium == 1 && row.firstPayment_amt > 0 && row.otp_price_fix_type == 1 && row.fixed_metal_rate == null) {
                            return '<button type="button"  class="btn btn-primary" style="padding: 4px; margin-bottom: -14px;" onClick="otp_model(' + row.id_scheme_account + ',' + row.mobile + ')">Fix Rate</button>';
                        }
                        if (row.fixed_metal_rate != null) {
                            return ' Rate Fixed ';
                        } else {
                            return ' - '
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.total_saved_wgt > 0) {
                            return row.total_saved_wgt;
                        } else {
                            return '-';
                        }
                    }
                },
                 {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.total_saved_benefit > 0) {
                                return formatMetalWeight(row.total_saved_benefit);
                        } else {
                            return '-';
                        }
                    }
                },
                { "mDataProp": "emp_name" },
                { "mDataProp": "referal_code" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        if (row.duplicate_passbook_issued != null && row.duplicate_passbook_issued != '') {
                            if (row.duplicate_passbook_issued == 1) {
                                return 'Duplicate';
                            } else if (row.duplicate_passbook_issued == 2) {
                                return 'Original';
                            } else {
                                return '-';
                            }
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        id = row.id_scheme_account;
                        close_url = '';
                        scheme_receipt_url = (row.one_time_premium == 1 && row.otp_price_fix_type == 1 && row.fixed_metal_rate != null ? base_url + 'index.php/admin_manage/get_scheme_receipt/' + id : '#');
                        edit_url = (access.edit == '1' ? base_url + 'index.php/account/edit/' + id : '#');
                        close_url = (close_acc == '1' && row.paid_installments >= 1 ? (row.one_time_premium == 1 && row.otp_price_fix_type == 1 && row.fixed_metal_rate == null ? '#' : base_url + 'index.php/account/close/scheme/' + id) : '#');
                        delete_url = (access.delete == '1' ? base_url + 'index.php/account/delete/' + id : '#');
                        delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                        action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                            '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
                            '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' + (access.allow_acc_closing == 1 ? '<li><a href="' + close_url + '" class="btn-edit"><i class="fa fa-close" ></i> Close</a></li>' : '') +
                            (row.one_time_premium == 1 && row.otp_price_fix_type == 1 && row.fixed_metal_rate != null ? '<li><a href="' + scheme_receipt_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print</a></li>' : '') +
                            '<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li>';
                        return action_content;
                    }
                }
            ];
            if ($("#old_sch_acc_num").val() == "1") {
                column.splice(3, 0, {
                    mDataProp: "old_scheme_acc_number",
                    visible: true
                });
            }
            oTable = $('#sch_acc_list').dataTable({
                columnDefs: [{
                    "defaultContent": "-",
                    "targets": "_all"
                }],
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": true,
                "order": [
                    [1, "desc"]
                ],
                // "columnDefs":
                // 	[
                // 		{
                // 			targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 18, 19, 20, 21, 22, 23, 24, 25, 26],
                // 			className: 'dt-left'
                // 		},
                // 		{
                // 			targets: [16, 17],
                // 			className: 'dt-right'
                // 		},
                // 	],
                "dom": 'lBfrtip',
                "buttons": ['excel', 'print'],
                "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
                "aaData": account,
                "aoColumns": column
            });
        }
    }
}
// scheme account number  manual
var selectdatas = [];
$(document).on('click', '#select_aldata', function(e) {
    if ($(this).prop("checked") == true) {
        $("tbody tr td input[type='checkbox']").prop('checked', true);
        $(".schemeaccount").attr('disabled', false);
    } else if ($(this).prop("checked") == false) {
        $(".schemeaccount").val('');
        $(".schemeaccount").attr('disabled', true);
        $("tbody tr td input[type='checkbox']").prop('checked', false);
    }
});
$(document).on('click', '.select_ids', function(e) {
    $("#sch_acc_list tbody tr").each(function(index, value) {
        if (!$(value).find(".select_ids").is(":checked")) {
            $(value).find(".schemeaccount").empty();
            $(value).find(".schemeaccount").attr('disabled', true);
            $(value).find(".schemeaccount").val('');
        } else if ($(value).find(".select_ids").is(":checked")) {
            $(value).find(".schemeaccount").attr('disabled', false);
        }
    });
});
var selected = [];
$(document).on('click', '.conform_sch', function(e) {
    $("#sch_acc_list tbody tr").each(function(index, value) {
        if (!$(value).find(".select_ids").is(":checked")) {
            $(value).find(".schemeaccount").empty();
            $(value).find(".schemeaccount").attr('disabled', true);
        } else if (($(value).find(".select_ids").is(":checked") && $(value).find(".schemeaccount").val() != '')) {
            $("#conform_save").attr('disabled', true);
            $(value).find(".schemeaccount").attr('disabled', false);
            var id_scheme_account = $(value).find(".select_ids").val();
            var scheme_acc_number = $(value).find(".schemeaccount").val();
            var data = { 'id_scheme_account': id_scheme_account, 'scheme_acc_number': scheme_acc_number };
            // var sech = JSON.stringify(data);
            selected.push(data);
        } else {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Select to proceed</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $('#error-msg').html(msg);
            return false;
        }
    });
    if (selected.length > 0) {
        $("div.overlay").css("display", "block");
        $.ajax({
            url: base_url + "index.php/schemeaccount/update",
            data: { 'selected': selected },
            dataType: "JSON",
            type: "POST",
            success: function(data) {
                $("div.overlay").css("display", "none");
                location.reload(true);
            },
            error: function(error) {
                $("div.overlay").css("display", "none");
            }
        });
    } else {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Select to proceed</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $('#error-msg').html(msg);
        return false;
    }
});
//closed scheme account list 
function get_closed_acc_list(from_date = "", to_date = "", id_branch = "", id_employee = "", close_id_branch = "", is_utilized = "") {
    //alert(HH);
    //$('body').addClass("sidebar-collapse");
    my_Date = new Date();
    var group_code = $("#group_select").val();
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/account/get/ajax_closed_acc_list?nocache=" + my_Date.getUTCSeconds(),
        // data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch}: ''),
        data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'id_employee': id_employee, 'close_id_branch': close_id_branch, 'id_scheme': $("#scheme_select").val(), 'group_code': group_code, 'is_utilized': is_utilized } : ''),
        dataType: "JSON",
        type: "POST",
        success: function(data) {
            console.log(data);
            set_closed_acc_list(data);
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}
/*-- Coded by ARVK --*/
function set_closed_acc_list(data) {
    var account = data.data;
    var access = data.access;
    var close_acc = data.close_acc;
    var is_utilized = data.is_utilized;
    $('#total_closed_accounts').text(account.length);
    var oTable = $('#closed_list').DataTable();
    var from_date = $('#closed_list1').text();
    var to_date = $('#closed_list2').text();
    oTable.clear().draw();
    if (account != null && account.length > 0) {
        var acc_listDate1 = $('#closed_list1').text();
        var acc_listDate2 = $('#closed_list2').text();
        // Convert date strings to Date objects
        var date1 = new Date(acc_listDate1);
        var date2 = new Date(acc_listDate2);
        var branch_name = getBranchTitle();
        var title = '';
        title += get_title(formatDate(date1), formatDate(date2), 'Closed Scheme Account List - ' + branch_name);
        let column = [{
                "mDataProp": function(row, type, val, meta) {
                    var chit_url = base_url + 'index.php/reports/payment/account/' + row.id_scheme_account;
                    action = '<a href="' + chit_url + '" target="_blank">' + row.id_scheme_account + '</a>';
                    return action;
                }
            },
            { "mDataProp": "name" },
            { "mDataProp": "mobile" },
            { "mDataProp": "account_name" },
            { "mDataProp": "scheme_acc_number" },
            { "mDataProp": "code" },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.group_code != '') {
                        return row.group_code;
                    } else {
                        return '-';
                    }
                }
            },
            { "mDataProp": "start_date" },
            { "mDataProp": "scheme_type" },
            { "mDataProp": "amount" },
            { "mDataProp": "employee_closed" },
            { "mDataProp": "branchname" },
            { "mDataProp": "Closing_id_branch" },
            { "mDataProp": "closing_date" },
            //    { "mDataProp": "pay_amount" },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.show_ins_type == 1) {
                        return row.paid_installments + "/" + row.total_installments;
                    } else {
                        return row.paid_installments;
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.paid_installments != row.total_installments) {
                        var bonus_deduction = parseFloat(row.paid_installments) * parseFloat(row.firstPayDisc_value);
                        return formatCurrency.format(parseFloat(row.pay_amount) - parseFloat(bonus_deduction));
                    } else {
                        return formatCurrency.format(row.pay_amount);
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    return formatCurrency.format(row.closing_amount);
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    return formatMetalWeight(row.closing_weight);
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    /*	if(row.scheme_type == 0 || (row.scheme_type == 3 && row.flexible_sch_type == 1))
                    	{
                    		return row.currency_symbol+' '+formatCurrency.format(row.closing_balance);
                    	}
                    	else
                    	{
                    		return row.closing_balance+' g';
                    	} */
                    var is_weight = 0; // esakki 10-10
                    if (row.scheme_type == 0 || row.scheme_type == 3 && (row.flexible_sch_type == 1 || row.flexible_sch_type == 6 || (row.flexible_sch_type == 2 && row.wgt_convert == 2))) {
                        is_weight = 0;
                    } else if (row.scheme_type == 1 || row.scheme_type == 2 || (row.scheme_type == 3 && ((row.flexible_sch_type == 2 && (row.wgt_convert == 0 || row.wgt_convert == 1)) || row.flexible_sch_type == 3 || row.flexible_sch_type == 4 || row.flexible_sch_type == 5 || row.flexible_sch_type == 7 || row.flexible_sch_type == 8))) {
                        if ((row.flexible_sch_type == 2 && (row.wgt_convert == 0 || row.wgt_convert == 1)) || row.flexible_sch_type != 2) {
                            is_weight = 1;
                        } else if (row.flexible_sch_type == 2 && row.wgt_convert == 2) {
                            is_weight = 0;
                        } else {
                            is_weight = 1;
                        }
                    }
                    if (row.store_closing_balance_as == 1) {
                        return row.closing_balance + ' g';
                    } else if (row.store_closing_balance_as == 0) {
                        return row.currency_symbol + ' ' + formatCurrency.format(row.closing_balance);
                    } else {
                        if (is_weight == 1) {
                            if ((row.flexible_sch_type == 2 && row.wgt_convert == 0)) {
                                return row.closing_balance + ' g';
                            } else if (row.flexible_sch_type == 2 && row.wgt_convert == 1) {
                                return row.currency_symbol + ' ' + formatCurrency.format(row.closing_balance);
                            } else {
                                return row.closing_balance + ' g';
                            }
                        } else {
                            return row.currency_symbol + ' ' + formatCurrency.format(row.closing_balance);
                        }
                    }
                    // 		//as amount
                    // 		if(is_weight == 0 || (is_weight == 1 && row.store_closing_balance_as == 0)){
                    // 			return row.currency_symbol+' '+formatCurrency.format(row.closing_balance);
                    // 		//as weight        
                    // 		}else if(is_weight == 1) {
                    // 			if((row.flexible_sch_type==2 && row.wgt_convert == 0 )){
                    // 				return row.closing_balance+' g';
                    // 			}else if(row.flexible_sch_type==2 && row.wgt_convert == 1 ){
                    // 				return row.currency_symbol+' '+formatCurrency.format(row.closing_balance);
                    // 			}else{
                    // 				return row.closing_balance+' g';
                    // 			}
                    // 		}
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    var is_weight = 0; // esakki 10-10
                    if (row.scheme_type == 0 || row.scheme_type == 3 && (row.flexible_sch_type == 1 || row.flexible_sch_type == 6 || (row.flexible_sch_type == 2 && row.wgt_convert == 2))) {
                        is_weight = 0;
                    } else if (row.scheme_type == 1 || row.scheme_type == 2 || (row.scheme_type == 3 && ((row.flexible_sch_type == 2 && (row.wgt_convert == 0 || row.wgt_convert == 1)) || row.flexible_sch_type == 3 || row.flexible_sch_type == 4 || row.flexible_sch_type == 5 || row.flexible_sch_type == 7 || row.flexible_sch_type == 8))) {
                        if ((row.flexible_sch_type == 2 && (row.wgt_convert == 0 || row.wgt_convert == 1)) || row.flexible_sch_type != 2) {
                            is_weight = 1;
                        } else if (row.flexible_sch_type == 2 && row.wgt_convert == 2) {
                            is_weight = 0;
                        } else {
                            is_weight = 1;
                        }
                    }
                    if (is_weight == 1) {
                        if (row.add_ben_type == 0 && parseFloat(row.additional_benefits) > 0) {
                            return row.currency_symbol + ' ' + formatCurrency.format(row.total_benefits);
                        } else {
                            return row.total_benefits + ' g';
                        }
                    } else {
                        return row.currency_symbol + ' ' + formatCurrency.format(row.total_benefits);
                    }
                }
            },
            {
                "mDataProp": "dg_other_benefit_wgt",
                render: function(data, type, row) {
                    return data ? data + " g" : "0 g";
                }
            },
            /*General advance paid and benefits starts*/
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.tot_genadv_amt_paid > 0) {
                        return 'INR ' + formatCurrency.format(row.tot_genadv_amt_paid) + ' / ' + row.tot_genadv_wgt_paid + ' g';
                    } else {
                        return '-';
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.tot_genadv_benefit > 0) {
                        return 'INR ' + formatCurrency.format(row.tot_genadv_benefit) + ' / ' + row.tot_genadv_benefit_wgt + ' g';
                    } else {
                        return '-';
                    }
                }
            },
            //ga ends
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.gift_status == 0 || row.gift_status == 1) {
                        if (row.paid_installments <= row.cus_deduct_ins) {
                            return '<button type="button"  class="btn btn-primary" style="padding: 4px; margin-bottom: -14px;" onClick="update_gift_status(' + row.id_scheme_account + ')">Deduct Gift</button>';
                        } else {
                            return (row.gift_status == 0 ? 'Not issued' : (row.gift_status == 1 ? 'Issued' : '-'));
                        }
                    } else if (row.gift_status == 2) {
                        return 'Deducted';
                    } else {
                        return 'No gifts';
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    return formatCurrency.format(row.closing_add_chgs);
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    return (row.discountAmt * row.paid_installments);
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.is_utilized == 0) {
                        return 'No';
                    } else {
                        return 'Yes';
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.booking_amt > 0) {
                        return inr_format.format(row.booking_amt);
                    } else {
                        return '-';
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.booking_wgt > 0) {
                        return row.booking_wgt;
                    } else {
                        return '-';
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    if (row.booking_rate > 0) {
                        return inr_format.format(row.booking_rate);
                    } else {
                        return '-';
                    }
                }
            },
            {
                "mDataProp": function(row, type, val, meta) {
                    id = row.id_scheme_account;
                    is_utilized = row.is_utilized;
                    close_url = (access.edit == '1' ? base_url + 'index.php/account/revert/' + id : '#');
                    printbtn = '';
                    print_invoice_url = base_url + 'index.php/account/close/invoice_his_custom/' + id;
                    print_url = base_url + 'index.php/account/close/scheme_history/' + id;
                    printbtn = '<li><a href="' + print_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print</a></li>';
                    revert_confirm = (row.is_utilized == 1 ? ('#utilized') : ('#confirm-revert'));
                    console.log(row);
                    if (row.is_utilized == '1') {
                        action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                            '<li><a href="#" class="closed_ac_detail" data-href="#" data-toggle="modal" data-id="' + id + '" data-target="#closed_acc_detail"><i class="fa fa-list-alt"></i> Details</a></li>' +
                            '<li><a href="#" class="btn-del confirm-revert" data-href="' + close_url + '" data-toggle="modal" data-target="' + revert_confirm + '"  ><i class="fa fa-backward"></i> Revert</a></li>' +
                            '<li><a href="' + print_url + '" target="_blank" class="btn-edit"><i class="fa fa-search-plus" ></i> Print</a></li>' +
                            '<li><a href="' + print_invoice_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Close Invoice</a></li>';
                        return action_content;
                    }
                    action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                        '<li><a href="#" class="closed_ac_detail" data-href="#" data-toggle="modal" data-id="' + id + '" data-target="#closed_acc_detail"><i class="fa fa-list-alt"></i> Details</a></li>' +
                        '<li><a href="#" class="btn-del confirm-revert" data-href="' + close_url + '" data-toggle="modal" data-target="' + revert_confirm + '"  ><i class="fa fa-backward"></i> Revert</a></li>' +
                        '<li><a href="' + print_url + '" target="_blank" class="btn-edit"><i class="fa fa-search-plus" ></i> Print</a></li>';
                    //'<li><a href="'+print_invoice_url+'" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Close Invoice</a></li>';
                    return action_content;
                }
            }
        ];
        if ($("#old_sch_acc_num").val() == "1") {
            column.splice(2, 0, {
                mDataProp: "old_scheme_acc_number",
                visible: true
            });
        }
        oTable = $('#closed_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "dom": 'lBfrtip',
            "buttons": ['excel', {
                extend: 'print',
                title: '',
                customize: function(win) {
                    $(win.document.body)
                        .prepend(title);
                    $(win.document.body).find('table')
                        .addClass('compact');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '10px')
                        .css('font-family', 'sans-serif');
                    $(win.document.body).find('tr:nth-child(odd) td').each(function(index) {
                        $(this).css('font-weight', 'bold');
                    });
                },
                exportOptions: { columns: ':visible' },
            }, ],
            "columnDefs": [{
                    targets: [0, 9, 14, 15, 16, 17, 18, 19, 20, 21, 23, 24],
                    className: 'dt-right'
                },
                {
                    targets: [1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 12, 13, 22, 25],
                    className: 'dt-left'
                }
            ],
            "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
            "aaData": account,
            "order": [
                [0, "desc"]
            ],
            "aoColumns": column
        });
    }
}

function close_acc_otp() {
    //console.log($("input[name='account[closed_by]']:checked").val());
    $("div.overlay").css("display", "block");
    var acc_num = $('#scheme_acc_no').val();
    if ($("#closed_by").val() == 0) // esakki #14-12
    {
        var mobile = $.trim($("#mobile").val());
        var email = $.trim($("#email").val());
        var id_customer = $.trim($("#id_customer").val());
        var name = $("#firstname").val();
    } else {
        var mobile = $.trim($("#nominee_mobile").val());
        var email = $.trim($("#email").val());
        var id_customer = $.trim($("#id_customer").val());
        var name = $("#nominee_name").val();
    }
    var otp = {
        id_sch_acc: $("input[name='account[id_scheme_account]']").val(),
        id_emp: $("input[name='account[employee_closed]']").val(),
        send_resend: ($("#send_otp").val() == 'Send OTP' ? '0' : '1'),
        acc_num: $('#scheme_acc_no').val()
    }
    console.log(otp);
    send_closing_otp(mobile, id_customer, name); //#ABI	
}
if (ctrl_page[1] == 'close' && ctrl_page[2] == 'scheme' && ctrl_page[3] > 0) {
    var br_mobile = $('#branch_otp_mobile').val();
    if (br_mobile != '') {
        $('#send_otp_to_branch').prop('disabled', false);
        //RAHUL DIGI GOLD UPDATES 16/07
    } else {
        $('#send_otp_to_branch').prop('disabled', true);
    }
    $("input[name='account[store_closing_balance_as]'][value='0']").prop("checked", true);
    calculate_closing_balance();
}

function send_closing_otp(mobile, id_customer, name) {
    var otp = {
        id_sch_acc: $("input[name='account[id_scheme_account]']").val(),
        id_emp: $("input[name='account[employee_closed]']").val(),
        send_resend: ($("#send_otp").val() == 'Send OTP' ? '0' : '1'),
        acc_num: $('#scheme_acc_no').val(),
        name: name
    }
    console.log(otp);
    $.ajax({
        url: base_url + "index.php/account/close/otp/" + mobile + "/" + id_customer + "/otp",
        type: "POST",
        data: otp,
        dataType: 'json',
        success: function(result) {
            console.log(result);
            if (result.result == 1) {
                $('#otp').prop('required', true);
                $('#otp').prop('readonly', false);
                $('#otp_status').fadeIn();
                $("#otp_status").text("OTP Sent Successfully, Kindly verify it by entering in the above Text box.");
                $("#otp_status").css("color", 'green');
                $("div.overlay").css("display", "none");
                $('#otp_status').delay(10000).fadeOut(500);
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
            console.log(error);
        }
    });
}

function verify_close_acc_otp() {
    $("div.overlay").css("display", "block");
    if ($("#otp").val().length == 6) {
        var id_sch_acc = $("input[name='account[id_scheme_account]']").val();
        var otp_entered = $("#otp").val();
        //console.log(id_sch_acc);
        $.ajax({
            url: base_url + "index.php/account/fetch/otp/" + id_sch_acc + "/" + otp_entered,
            type: "POST",
            dataType: 'json',
            success: function(result) {
                if (result == 1) {
                    //console.log('success');
                    $("#close_save").prop('disabled', false);
                    $("#close_save_print").prop('disabled', false);
                    $("#otp").prop('disabled', 'disabled');
                    $("input[name='account[closedBy]']").val($("input[name='account[closed_by]']:checked").val());
                    $("input[name='account[closed_by]']").prop('disabled', 'disabled');
                    $("#send_otp").hide();
                    $("#send_otp_to_branch").hide();
                    $("#verify_otp").hide();
                    $('#otp_status').fadeIn();
                    $("#otp_status").text("OTP verified successfully, Kindly proceed with scheme closing.");
                    document.getElementById('timer').innerText = '';
                    clearInterval(timer);
                    $("#otp_status").css("color", 'green');
                    $("div.overlay").css("display", "none");
                    $("#close_actionBtns").css("display", "block"); // closed valide hh//
                    //$('#otp_status').delay(10000).fadeOut(500);
                } else {
                    $("#verify_otp").prop('disabled', false);
                    $("#close_save").prop('disabled', 'disabled');
                    document.getElementById('timer').innerText = '';
                    clearInterval(timer);
                    $('#send_otp').prop('value', 'Resend OTP');
                    $('#send_otp').prop('disabled', false);
                    $("#close_save_print").prop('disabled', 'disabled');
                    $('#otp_status').fadeIn();
                    $("#otp_status").text("Incorrect OTP, Kindly enter the correct one.");
                    $("#otp_status").css("color", 'red');
                    $("div.overlay").css("display", "none");
                    $('#otp_status').delay(10000).fadeOut(500);
                }
            },
            error: function(error) {
                $("div.overlay").css("display", "none");
                console.log(error);
            }
        });
    } else {
        $("#verify_otp").prop('disabled', false);
        $('#otp_status').fadeIn();
        $("#otp_status").text("Kindly enter 6 digit OTP to verify.");
        $("#otp_status").css("color", 'red');
        $("div.overlay").css("display", "none");
        $('#otp_status').delay(5000).fadeOut(500);
        console.log('I\'m in else');
    }
}
//branch_name
//closing branch
$('#close_branch_select').select2().on("change", function(e) {
    if ($(this).val() != '') {
        var from_date = $('#closed_list1').text();
        var to_date = $('#closed_list2').text();
        var close_id_branch = $(this).val();
        var id_employee = $('#id_employee').val();
        var is_utilized = $('#is_utilized').val();
        get_closed_acc_list(from_date, to_date, '', id_employee, close_id_branch, is_utilized);
    }
});
//  this is for utilized filter in closed account list 
// Code by Karthikai Kumaran 08/10/2025
$('#is_utilized').select2().on("change", function(e) {
    if ($(this).val() != '') {
        // console.log($(this).val());
        var from_date = $('#closed_list1').text();
        var to_date = $('#closed_list2').text();
        var closed_id_branch = $('#close_branch_select').val();
        var id_employee = $("#id_employee").val();
        get_closed_acc_list(from_date, to_date, '', id_employee, closed_id_branch, $(this).val());
    }
});
$('#branch_select').select2().on("change", function(e) {
    switch (ctrl_page[1]) {
        case 'new':
            if (this.value != '') {
                $("#id_branch").val(this.value)
                var from_date = $('#account_list1').text();
                var to_date = $('#account_list2').text();
                var id_branch = $(this).val();
                var id_customer = $("#id_customer").val();
                var id_scheme = $('#id_scheme').val();
                get_scheme_acc_list(from_date, to_date, id_branch, id_customer, id_scheme);
            }
            break;
        case 'scheme_group':
            if (this.value != '') {
                $("#id_branch").val(this.value);
                get_group_list(this.value);
            }
            break;
            /* case 'close':
            if(this.value!='')
            {  
            	var from_date = $('#closed_list1').text();
            	var to_date  = $('#closed_list2').text();
            	var id=$(this).val();						
            	get_closed_acc_list(from_date,to_date,id);
            }
             break;*/
        case 'close':
            if (this.value != '') {
                if (ctrl_page[0] == 'account') {
                    if (ctrl_page[2] == 'scheme') {
                        var branch_select_val = e.target.value;
                        var test = $("#id_branch").val(branch_select_val);
                    }
                }
            }
            if (this.value != '') {
                /*	if(ctrl_page[2]!='scheme'){
                	$('#emp_select').empty();
                	var from_date = $('#closed_list1').text();
                	var to_date = $('#closed_list2').text();
                	var id = $(this).val();
                	var close_id_branch = $('#close_id_branch').val();
                	var id_employee = $('#id_employee').val();
                	get_closed_acc_list(from_date, to_date, id, id_employee, close_id_branch);
                	get_employee_name(id);
                } */
            }
            break;
        case 'add':
            if (this.value != '') {
                $("#id_branch").val(this.value);
                var id_branch = $(this).val();
                var selected_customer = $("#id_customer").val();
                get_schemes((selected_customer && selected_customer != '' ? selected_customer : ''), id_branch);
            }
            break;
    }
});
// Existing reg request functions 
function get_request_list(from_date = "", to_date = "", branch = "", status = "") {
    my_Date = new Date();
    postData = (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date, 'status': status } : (branch != '' ? { 'id_branch': branch, 'status': status } : { 'status': $('#filtered_status').val() }));
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_manage/ajax_requests_list?nocache=" + my_Date.getUTCSeconds(),
        data: (postData),
        dataType: "JSON",
        type: "POST",
        success: function(data) {
            $('#total_requests').text(data.requests.length);
            set_request_list(data);
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}

function set_request_list(data) {
    $('body').addClass("sidebar-collapse");
    var accounts = data.requests;
    var oTable = $('#scheme_reg_list').DataTable();
    var schemesOptions = '';
    var branchOptions = '';
    var GroupOptions = '';
    var pan_no = '';
    if (data.requests != '') {
        var getExisting_balance = data.requests[0]['getExisting_balance'];
    } else {
        var getExisting_balance = '';
    }
    $.each(data.branches, function(key, item) {
        branchOptions += '<option value="' + item.id_branch + '">' + item.name + '</option>';
    });
    $.each(data.schemes, function(key, item) {
        schemesOptions += '<option value="' + item.id_scheme + '">' + item.code + '</option>';
    });
    $.each(data.groups, function(key, item) {
        GroupOptions += '<option value="' + item.group_code + '">' + item.group_code + '</option>';
    });
    oTable.clear().draw();
    if (accounts != null && accounts.length > 0 && getExisting_balance == 1) {
        oTable = $('#scheme_reg_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [
                [0, "desc"]
            ],
            "dom": 'lBfrtip',
            "buttons": ['excel', 'print'],
            "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
            "aaData": accounts,
            "aoColumns": [{
                    "mDataProp": function(row, type, val, meta) {
                        chekbox = '<input type="checkbox" class="id_reg_request" name="reg_request_id[]" value="' + row.id_reg_request + '"/> '
                        if (row.status == 0) {
                            return chekbox + " " + row.id_reg_request;
                        } else {
                            return row.id_reg_request;
                        }
                    }
                },
                { "mDataProp": "name" },
                { "mDataProp": "mobile" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        element = '<select class="id_scheme form-control" name="id_scheme">' + schemesOptions + '</select>';
                        return element + '<input type="hidden" value="' + row.group_code + '"/>';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        element = '<select class="scheme_group_code form-control" name="scheme_group_code">' + GroupOptions + '</select>';
                        return element;
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="chit_no form-control" name="scheme_acc_number" value="' + row.scheme_acc_number + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="paid_installments form-control" name="paid_installments" value="' + row.paid_installments + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="balance_amount form-control" name="balance_amount" value="' + row.balance_amount + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="last_paid_date form-control" name="last_paid_date" id="last_paid_date" value="' + row.last_paid_date + '"  />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="balance_weight form-control" name="balance_weight" value="' + row.balance_weight + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="last_paid_weight form-control" name="last_paid_weight" value="' + row.last_paid_weight + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="last_paid_chances form-control" name="last_paid_chances" value="' + row.last_paid_chances + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="firstPayment_amt form-control" name="firstPayment_amt" value="' + (row.firstPayment_amt == 0.00 ? '' : row.firstPayment_amt) + '" type="text" required="' + (row.scheme_type == 3 ? "true" : '') + '" /><input class="scheme_type form-control" name="scheme_type" value="' + (row.scheme_type) + '" type="hidden" /><input class="firstPayamt_payable form-control" name="firstPayamt_payable" value="' + (row.firstPayamt_payable) + '" type="hidden" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="ac_name form-control" name="ac_name" value="' + row.ac_name + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        element = '<select class="id_branch form-control" name="id_branch">' + branchOptions + '</select>';
                        return element + '<input type="hidden" class="id_customer" value="' + row.id_customer + '"/><input type="hidden" class="pan_no" value="' + row.pan_no + '"/><input type="hidden" class="mobile" value="' + row.mobile + '"/><input type="hidden" class="email" value="' + row.email + '"/><input type="hidden" class="added_by" value="' + row.added_by + '"/>';
                    }
                },
                { "mDataProp": "date_add" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return (row.status == 0 ? 'Processing' : (row.status == 1 ? 'Approved' : 'Rejected'));
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="form-control remark" value="' + row.remark + '" name="remark" type="text" />';
                    }
                }
            ],
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td .id_branch', nRow).val(aData['id_branch']);
                $('td .id_scheme', nRow).val(aData['id_scheme']);
                $('td .scheme_group_code', nRow).val(aData['scheme_group_code']);
            }
        });
    } else {
        Table = $('#scheme_reg_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [
                [0, "desc"]
            ],
            "dom": 'lBfrtip',
            "buttons": ['excel', 'print'],
            "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
            "aaData": accounts,
            "aoColumns": [{
                    "mDataProp": function(row, type, val, meta) {
                        chekbox = '<input type="checkbox" class="id_reg_request" name="reg_request_id[]" value="' + row.id_reg_request + '"/> '
                        if (row.status == 0) {
                            return chekbox + " " + row.id_reg_request;
                        } else {
                            return row.id_reg_request;
                        }
                    }
                },
                { "mDataProp": "name" },
                { "mDataProp": "mobile" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        element = '<select class="id_scheme form-control" name="id_scheme">' + schemesOptions + '</select>';
                        return element + '<input type="hidden" value="' + row.group_code + '"/>';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        element = '<select class="scheme_group_code form-control" name="scheme_group_code">' + GroupOptions + '</select>';
                        return element;
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="chit_no form-control" name="scheme_acc_number" value="' + row.scheme_acc_number + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="firstPayment_amt form-control" name="firstPayment_amt" value="' + (row.firstPayment_amt == 0.00 ? '' : row.firstPayment_amt) + '" type="text" required="' + (row.scheme_type == 3 ? "true" : '') + '" /><input class="scheme_type form-control" name="scheme_type" value="' + (row.scheme_type) + '" type="hidden" /><input class="firstPayamt_payable form-control" name="firstPayamt_payable" value="' + (row.firstPayamt_payable) + '" type="hidden" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="ac_name form-control" name="ac_name" value="' + row.ac_name + '" type="text" />';
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        element = '<select class="id_branch form-control" name="id_branch">' + branchOptions + '</select>';
                        return element + '<input type="hidden" class="id_customer" value="' + row.id_customer + '"/><input type="hidden" class="pan_no" value="' + row.pan_no + '"/><input type="hidden" class="mobile" value="' + row.mobile + '"/><input type="hidden" class="email" value="' + row.email + '"/><input type="hidden" class="added_by" value="' + row.added_by + '"/>';
                    }
                },
                { "mDataProp": "date_add" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return (row.status == 0 ? 'Processing' : (row.status == 1 ? 'Approved' : 'Rejected'));
                    }
                },
                {
                    "mDataProp": function(row, type, val, meta) {
                        return '<input class="form-control remark" value="' + row.remark + '" name="remark" type="text" />';
                    }
                }
            ],
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td .id_branch', nRow).val(aData['id_branch']);
                $('td .id_scheme', nRow).val(aData['id_scheme']);
                $('td .scheme_group_code', nRow).val(aData['scheme_group_code']);
            }
        });
    }
}

function update_request_status(status = "", data = "") {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_manage/update_request?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
        data: { 'status': status, 'req_data': data },
        type: "POST",
        async: false,
        success: function(data) {
            location.reload(false);
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            console.log(error);
            $("div.overlay").css("display", "none");
        }
    });
}

function initialize_branch_list(elementID) {
    my_Date = new Date();
    $('.overlay').css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/branches?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function(data) {
            $.each(data.branches, function(key, item) {
                $('#sync_branch').append(
                    $("<option></option>")
                    .attr("value", item.id_branch)
                    .text(item.name)
                );
            });
            $("#sync_branch").select2({
                placeholder: "Branch",
                allowClear: true
            });
            $.each(data.branches, function(key, item) {
                $('#' + elementID).append(
                    $("<option></option>")
                    .attr("value", item.id_branch)
                    .text(item.name)
                );
            });
            $("#" + elementID).select2({
                placeholder: "Select branch",
                allowClear: true
            });
            $("#sel_branch").select2("val", '');
            //disable spinner
            $('.overlay').css('display', 'none');
        }
    });
}
//get_group_list //hh
function get_group_list(branch = "", from_date = "", to_date = "") {
    my_Date = new Date();
    postData = (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date } : (branch != '' ? { 'id_branch': branch } : ''));
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/account/ajaxscheme_group/list?nocache=" + my_Date.getUTCSeconds(),
        data: (postData),
        dataType: "JSON",
        type: "POST",
        success: function(data) {
            set_group_list(data);
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}
// scheme group //  
$('#scheme_select').select2().on("change", function(e) {
    if (this.value != '') {
        if (ctrl_page[0] == 'account' && ctrl_page[1] == 'scheme_group' && ctrl_page[2] == 'add' || ctrl_page[2] == 'edit') {
            $('#id_scheme').val(this.value)
        }
        switch (ctrl_page[1]) {
            case 'new':
                $("#id_scheme").val((this).value);
                var from_date = $('#account_list1').text();
                var to_date = $('#account_list2').text();
                var id_customer = $('#id_customer').val();
                var id_branch = $('#id_branch').val();
                var id_scheme = $('#id_scheme').val();
                get_scheme_acc_list(from_date, to_date, id_branch, id_customer, id_scheme);
                break;
            case 'close':
                //RAHUL DIGI GOLD UPDATES 16/07
                $("#id_scheme").val(this.value);
                var from_date = $('#closed_list1').text();
                var to_date = $('#closed_list2').text();
                var closed_id_branch = $('#close_branch_select').val();
                var id_employee = $("#id_employee").val();
                var id_scheme = $("#id_scheme").val();
                var is_utilized = $('#is_utilized').val();
                get_closed_acc_list(from_date, to_date, '', id_employee, closed_id_branch, is_utilized);
                break;
        }
    } else {
        $('#id_scheme').val('');
    }
});
$('#branch_select').select2().on("change", function(e) {
    if (this.value != '') {
        $("#id_branch").val((this).value);
    }
});
$('#employee_select').select2().on("change", function(e) {
    if (this.value != '') {
        $("#id_employee").val((this).value);
    }
});
// scheme group //hh  
function set_group_list(data) {
    console.log(data);
    $('body').addClass("sidebar-collapse");
    var accounts = data.requests;
    var access = data.access;
    console.log(accounts);
    var oTable = $('#group_list').DataTable();
    oTable.clear().draw();
    if (accounts != null && accounts.length > 0) {
        $("#total_group").text(accounts.length);
        oTable = $('#group_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [
                [0, "desc"]
            ],
            "aaData": accounts,
            "aoColumns": [
                { "mDataProp": "id_scheme_group" },
                { "mDataProp": "scheme_code" },
                { "mDataProp": "branch_name" },
                { "mDataProp": "group_code" },
                { "mDataProp": "start_date" },
                { "mDataProp": "end_date" },
                {
                    "mDataProp": function(row, type, val, meta) {
                        id = row.id_scheme_group;
                        edit_url = (access.edit == '1' ? base_url + 'index.php/account/scheme_group/edit/' + id : '#');
                        delete_url = (access.delete == '1' ? base_url + 'index.php/account/scheme_group/delete/' + id : '#');
                        delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                        action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                            (access.edit == '1' ? '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' : "") +
                            (access.delete == '1' ? '<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li>' : "");
                        return action_content;
                    }
                }
            ],
        });
    }
}
//get_group_list
function get_schemename() {
    $(".overlay").css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/get/schemename_list',
        dataType: 'json',
        success: function(data) {
            console.log(data);
            //var scheme_val =  $('#id_scheme').val();
            //console.log( $('#id_schemes').val());
            $('#scheme_select').append(
                $("<option></option>")
                .attr("value", 0)
                .text('All')
            );
            $.each(data, function(key, item) {
                $('#scheme_select').append(
                    $("<option></option>")
                    .attr("value", item.id_scheme)
                    .text(item.code)
                );
            });
            $("#scheme_select").select2({
                placeholder: "Select Scheme name",
                allowClear: true
            });
            //console.log($('#id_scheme').val());
            //$("#scheme_select").select2("val",(scheme_val!='' && scheme_val>0?scheme_val:''));
            console.log($('#id_scheme').val());
            $("#scheme_select").select2("val", ($('#id_scheme').val() != null ? $('#id_scheme').val() : ''));
            $("#scheme_select").select2("val", ($('#scheme_val').val() != null ? $('#scheme_val').val() : ''));
            $(".overlay").css("display", "none");
        }
    });
}

function check_groupcode(group_code) {
    $("div.overlay").css("display", "block");
    $.ajax({
        type: 'POST',
        data: { 'group_code': group_code },
        url: base_url + 'index.php/account/check_group',
        dataType: 'json',
        success: function(avail) {
            // console.log(avail); 
            if (avail == 1) {
                $('#group_code').val('');
                $("#group").prop('disabled', true);
                $('#group_code').attr('placeholder', 'group code already exists')
            } else {
                $("#group").prop('disabled', false);
            }
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}
$('#pan_no').on('change', function() {
    if ($("#pan_no").val() != "") {
        var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;
        if (!regexp.test($("#pan_no").val())) {
            $("#pan_no").val("");
            alert("Not a valid PAN No.");
            $("#pan_no").focus();
            $isValidPan = false;
        }
    } else {
        alert("Enter valid PAN No.");
        $isValidPan = false;
    }
});

function get_scheme_acc(mobile) {
    my_Date = new Date();
    // $("div.overlay").css("display", "block"); 
    $.ajax({
        url: base_url + "index.php/account/get/ajax_account?nocache=" + my_Date.getUTCSeconds(),
        data: { 'mobile': mobile },
        dataType: "JSON",
        type: "POST",
        success: function(data) {
            set_scheme_acc_list(data);
            $('body').addClass("sidebar-collapse");
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//gift issue store db option hh//
$('#verify_issue').click(function(event) {
    var gifts = $('#gift_issued').val();
    if (gifts != null && gifts != '') {
        $(this).prop('disabled', 'disabled');
        verify_gift_issued();
    } else {
        $("#err").html("Select Any Gift");
    }
});

function verify_gift_issued() {
    $("div.overlay").css("display", "block");
    var id_sch_acc = $("#id_scheme_account").val();
    var issue_entered = $("#gift_issued").val();
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_manage/gift_issue?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        data: { 'id_sch_acc': id_sch_acc, 'issue_entered': issue_entered },
        dataType: 'json',
        success: function(result) {
            if (result == 1) {
                $('#status').fadeIn();
                $("#status").text("Gift Issued  Successfully,");
                //$("#gift_issued").val('');
                //$("#gift_issued").select2("val",'');	 
                $("#status").css("color", 'green');
                $('#status').delay(5000).fadeOut(500);
                //$("#gift_issued").css("display", 'none');
                //$("#gift_issued").prop("required", false);
                $("#verify_issue").css("display", 'none');
                $("div.overlay").css("display", "none");
                gift_issued_list(ctrl_page[2]);
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
            console.log(error);
        }
    });
}
$('#calc_blc').on('click', function() {
    $(".close_actionBtns").css("display", 'block');
    $("#gift_issued").prop("required", true);
    $("#gift_issued").css("display", '');
    $("#verify_issue").css("display", '');
    $("#verify_issue").prop('disabled', false);
});
//gift issue store db option hh//
//gift issued list hh //
function gift_issued_list(id) {
    $("div.overlay").css("display", "block");
    var oTable = $('#gift_issued_lists').DataTable();
    $.ajax({
        type: 'GET',
        url: base_url + "index.php/admin_manage/get_gift_issued_list?id_scheme_account=" + id,
        dataType: 'json',
        success: function(data) {
            if (data.length > 0) {
                $.each(data, function(key, item) {
                    /*if(item.type == 'GIFT'){
                    	$("#show_gifts").css("display", 'none');
                    }
                    if(item.type == 'PRIZE'){
                    	$("#show_prizes").css("display", 'none');
                    } */
                    $("#show_gifts").css("display", 'block');
                    $("#show_prizes").css("display", 'block');
                });
            }
            oTable = $('#gift_issued_lists').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": true,
                "aaData": data,
                "aoColumns": [
                    { "mDataProp": "id_gift_issued" },
                    { "mDataProp": "type" },
                    { "mDataProp": "gift_desc" },
                    { "mDataProp": "quantity" },
                    {
                        "mDataProp": function(row, type, val, meta) {
                            return (row.gift_status == 0 ? 'Not issued' : (row.gift_status == 1 ? 'Issued' : (row.gift_status == 2 ? 'Deducted' : '-')));
                        }
                    },
                    { "mDataProp": "id_employee" },
                    { "mDataProp": "date_issued" }
                ]
            });
            $("div.overlay").css("display", "none");
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}
//gift issued list hh //
// get_groupname for group filter
function get_group_name() {
    //alert();
    my_Date = new Date();
    $(".overlay").css('display', 'block');
    $.ajax({
        type: 'POST',
        //data:{'id_scheme':id_scheme},
        url: base_url + "index.php/admin_manage/get_groups?nocache=" + my_Date.getUTCSeconds(),
        dataType: 'json',
        success: function(data) {
            $("#spinner").css('display', 'none');
            console.log(data);
            $('#group_select').empty();
            $.each(data, function(key, item) {
                $('#group_select').append(
                    $("<option></option>")
                    .attr("value", item.group_code)
                    .text(item.group_code)
                );
            });
            $("#group_select").select2({
                placeholder: "Select group code",
                allowClear: true
            });
            $("#group_select").select2("val", ($('#id_group').val() != null ? $('#id_group').val() : ''));
            $(".overlay").css("display", "none");
        }
    });
}
$('#group_select').select2().on("change", function(e) {
    $("#group_code").val($(this).val());
    if (this.value) {
        switch (ctrl_page[1]) {
            case 'new':
                var from_date = $('#account_list1').text();
                var to_date = $('#account_list2').text();
                var id_branch = $("#branch_select").val();
                var id_customer = $("#id_customer").val();
                var id_scheme = $('#id_scheme').val();
                get_scheme_acc_list(from_date, to_date, id_branch, id_customer, id_scheme);
                break;
                /*	case 'close':
                		var from_date = $('#closed_list1').text();
                	var to_date = $('#closed_list2').text();
                	var closed_id_branch = $('#close_branch_select').val();
                	var id_employee = $("#id_employee").val();
                	get_closed_acc_list(from_date, to_date, '', id_employee, closed_id_branch,);
                		break;  */
        }
    }
});

function get_employee_name(id_branch = '') {
    //$("#spinner").css('display','none');
    //$(".overlay").css('display','block');
    $.ajax({
        type: 'POST',
        data: { 'id_branch': id_branch },
        url: base_url + 'index.php/reports/employee_list',
        dataType: 'json',
        success: function(data) {
            console.log(data);
            $("#spinner").css('display', 'none');
            $.each(data.employee, function(key, item) {
                //console.log(item.id_employee);					  	
                $('#emp_select').append(
                    $("<option></option>")
                    .attr("value", item.id_employee)
                    .text(item.employee_name)
                );
            });
            $("#emp_select").select2({
                placeholder: "Select Employee Name ",
                allowClear: true,
            });
            $("#emp_select").select2("val", ($('#id_employee').val() != null ? $('#id_employee').val() : ''));
            $(".overlay").css("display", "none");
        }
    });
}
$('#emp_select').select2().on("change", function(e) {
    if (this.value != '') {
        $('#id_employee').val(this.value);
        var from_date = $('#closed_list1').text();
        var to_date = $('#closed_list2').text();
        var close_branch = $('#close_branch_select').val();
        var id_branch = $('#id_branch').val();
        var id_employee = $('#id_employee').val()
        var is_utilized = $('#is_utilized').val();
        get_closed_acc_list(from_date, to_date, '', id_employee, close_branch, is_utilized);
    }
    /* else
    {
    	$('#id_employee').val('');
    	get_closed_acc_list(from_date,to_date,'','',close_branch);
    }  */
});
//Get Branch wise emp name in Scheme Join Page admin //HH
function get_emp_branchwise(login_branches) {
    $.ajax({
        type: 'GET',
        data: { 'login_branches': login_branches },
        url: base_url + 'index.php/reports/employee_list_brancwise',
        dataType: 'json',
        success: function(data) {
            //	console.log(data);
            // $("#spinner").css('display','none');
            var id_employee = $('#id_employee').val();
            $.each(data.employee, function(key, item) {
                // console.log(item.id_employee);					  	
                $('#employee_select').append(
                    $("<option></option>")
                    .attr("value", item.id_employee)
                    .text(item.employee_name)
                );
            });
            $("#employee_select").select2({
                placeholder: "Select Employee Name ",
                allowClear: true,
            });
            $("#employee_select").select2("val", (id_employee != '' && id_employee > 0 ? id_employee : ''));
            // $("#employee_select").select2("val", ($('#id_employee').val()!=null?$('#id_employee').val():''));
            $(".overlay").css("display", "none");
            if (ctrl_page[0] == 'account' && ctrl_page[1] == 'edit' && ctrl_page[2] != '') {
                get_schemes();
            }
        }
    });
}
//closing branch name//HH
function get_cls_branchname() {
    //alert('sa.js');
    //$(".overlay").css('display','block');	
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/branch/branchname_list',
        dataType: 'json',
        success: function(data) {
            //	console.log(data);
            var id_branch = $('#close_id_branch').val();
            $.each(data.branch, function(key, item) {
                $('#close_branch_select').append(
                    $("<option></option>")
                    .attr("value", item.id_branch)
                    .text(item.name)
                );
            });
            $("#close_branch_select").select2({
                placeholder: "Select branch name",
                allowClear: true
            });
            $("#close_branch_select").select2("val", (close_id_branch != '' && close_id_branch > 0 ? close_id_branch : ''));
            $(".overlay").css("display", "none");
        }
    });
}
//closing branch name//
// Rate Fixing based on the Otp  verify//HH
function otp_model(id_scheme_account, mobile) {
    my_Date = new Date();
    $.ajax({
        url: base_url + 'index.php/admin_manage/rateFixing_otp?nocache=' + my_Date.getUTCSeconds(),
        data: { 'id_scheme_account': id_scheme_account, 'mobile': mobile },
        type: "POST",
        dataType: 'json',
        success: function(data) {
            if (data.result == 3) {
                $('#otp_model').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#id_scheme_account").val(id_scheme_account);
                $("#mobile").val(mobile);
                $("div.overlay").css("display", "none");
            }
        }
    });
}
$('#submits').on('click', function() {
    var post_data = $('#otp_model').serialize();
    console.log(post_data);
    submit_otp(post_data);
});
$("submit").on("click", function(e) {
    var scheme_join_otp = $('#scheme_join_otp').val()
    if ($('#scheme_join_otp_valid').val() != 1) {
        if (scheme_join_otp === "1") {
            e.preventDefault();
            $('#acc_join').attr('onsubmit', 'return false;');
            $('#verify_otp_scheme_join').modal('show');
        }
    }
});

function submit_otp(post_data) {
    var post_otp = $('#otp').val();
    var mobile = $("#mobile").val();
    var id_scheme_account = $("#id_scheme_account").val();
    $.ajax({
        url: base_url + 'index.php/admin_manage/submit_ratefix/',
        data: { 'otp': post_otp, 'sch_ac_no': $('#scheme_acc_number').val(), 'mobile': mobile, 'id_scheme_account': id_scheme_account },
        type: "POST",
        dataType: "JSON",
        success: function(data) {
            if (data.success == false) {
                alert(data.msg);
                $('#otp').val('');
            } else {
                alert(data.msg);
                window.open(base_url + 'index.php/admin_manage/get_scheme_receipt/' + id_scheme_account, '_blank');
                location.reload(true);
            }
        }
    });
}
$('#resendotp').on('click', function() {
    var post_data = $('#otp_model').serialize();
    console.log(post_data);
    resend_otp(post_data);
});

function resend_otp(post_data) {
    var mobile = $("#mobile").val();
    $.ajax({
        url: base_url + 'index.php/admin_manage/rateFixing_otp?nocache=' + my_Date.getUTCSeconds(),
        data: { 'mobile': mobile },
        type: "POST",
        dataType: "JSON",
        success: function(data) {
            if (data.result == 3) {
                $('#otp_model').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            }
        }
    });
}

function RateFixing_Data() {
    var metal_rate = $('#metal_rate').val();
    var scheme_acc_number = $('#scheme_acc_number').val();
    var id_scheme_account = $('#id_scheme_account').val();
    $.ajax({
        url: "https://121.200.48.187/EJAPIS/RateFixing",
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', make_base_auth('re0625@ejindia.com', 'karthik014'));
        },
        type: "post",
        data: { 'scheme_acc_number': scheme_acc_number, 'metal_rate': metal_rate, 'id_scheme_account': id_scheme_account },
        dataType: 'json',
        success: function(data) {
            if (data.success == true) {
                alert(data.msg);
                location.reload(true);
            }
        }
    });
}

function make_base_auth(user, password) {
    var tok = user + ':' + password;
    var hash = btoa(tok);
    return 'Basic ' + hash;
}
// Rate Fixing based on the Otp  verify//
/* function get_gift_names()
 {
		   $.ajax({		
			   type: 'GET',		
			   url: base_url+'index.php/admin_manage/loadGiftData',		
			   dataType:'json',		
			   success:function(data){	
			 console.log(data);
				   var gift_issued =  $('#gift_list').val();		   
				   $.each(data, function (key, item) {	
					   $('#gift_list').append(						
					   $("<option></option>")						
					   .attr("value", item.gift_name)						  						  
					   .text(item.gift_name)						  					
					   );			   											
				   });						
				 $("#gift_list").select2({			    
					   placeholder: "Select Gift",			    
					   allowClear: true		    
				   });				 
					   $("#gift_list").select2("val",(gift_issued!='' && gift_issued>0?gift_issued:''));	 
					 $(".overlay").css("display", "none");			
			   }	
		 }); 
 } */
function get_gift_names(row_index = "") {
    var branch_setting = $("#branch_setting").val();
    var emp_login_branch = $("#emp_branch").val();
    var selected_branch = $("#branch_select").val();
    var id_branch = 0;
    if (ctrl_page[0] == 'account' && ctrl_page[1] == 'edit' && branch_setting == 1) {
        if (emp_login_branch != '') {
            id_branch = emp_login_branch;
        } else {
            id_branch = $("#id_branch").val();
        }
    } else if (branch_setting == 0) {
        id_branch = null;
    } else if (emp_login_branch != '') {
        id_branch = emp_login_branch;
    } else if (branch_setting == 1) {
        id_branch = selected_branch;
    }
    var table_rows = $('#chart_gift_creation_tbl tbody tr').length;
    var row_length = $("#table_row_length").val();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_manage/get_gift_bystock',
        dataType: 'json',
        data: { "id_branch": id_branch },
        success: function(data) {
            console.log(data);
            if (!row_index) {
                $('#gift_list_' + row_length).append(
                    $("<option></option>")
                    .attr("value", "")
                    .text("-----Select Gift----")
                );
                $.each(data, function(key, item) {
                    $('#gift_list_' + row_length).append(
                        $("<option></option>")
                        .attr("value", item.id_gift)
                        .text(item.gift_name)
                    );
                });
            } else {
                $('#gift_list_' + row_index).append(
                    $("<option></option>")
                    .attr("value", "")
                    .text("-----Select Gift----")
                );
                $.each(data, function(key, item) {
                    $('#gift_list_' + row_index).append(
                        $("<option></option>")
                        .attr("value", item.id_gift)
                        .text(item.gift_name)
                    );
                });
                $.ajax({
                    type: 'GET',
                    url: base_url + 'index.php/admin_manage/get_gift_issued_Dropdown',
                    dataType: 'json',
                    data: { 'id_scheme_account': ctrl_page[2] },
                    success: function(data) {
                        var i = 1;
                        //console.log("gift_list: "+data[0]);
                        $.each(data, function(key, item) {
                            if (item.type == 'GIFT') {
                                //alert(item.gift_desc);
                                //$('#gift_list_'+i).val(item.gift_desc);
                                $('#gift_list_' + i).val(item.id_gift);
                                $('#gift_val_' + i).val(item.id_gift);
                                $('#gift_list_' + i).val(item.id_gift).trigger('change');
                            }
                            i++;
                        })
                    }
                });
            }
            // $("#gift_list_"+row_length).select2({			    
            //  	placeholder: "Select Gift",			    
            //  	allowClear: true		    
            // });				 
            //$("#gift_list_"+row_length).select2("val",(gift_issued!='' && gift_issued>0?gift_issued:''));	 
            $(".overlay").css("display", "none");
        }
    });
}

function is_agent_exist(object) {
    var agent_code = $('#agent_code').val();
    var mob_regex = /^[6-9]{1}[0-9]{9}$/;
    if (agent_code.length == 10) {
        if (mob_regex.test(agent_code) == false) {
            alert('Agent code ' + agent_code + ' is not in the correct format!');
            $('#agent_code').val('');
        } else {
            $.ajax({
                url: base_url + 'index.php/admin_manage/is_agent_exist',
                data: { 'agent_code': agent_code },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.status == 0) {
                        alert(data.msg);
                        $('#agent_code').val('');
                    }
                }
            });
        }
    } else {
        alert("Not a valid agent code...");
        $('#agent_code').val('');
    }
}

function update_gift_status(id_sch_acc) {
    $.ajax({
        url: base_url + 'index.php/admin_manage/update_gift_status',
        data: { 'id_sch_acc': id_sch_acc },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            if (data == 1) {
                $.toaster({ priority: 'success', title: 'Gift Status', message: '' + "</br> Gift deducted successfully.." });
                window.location.reload(true);
            } else {
                $.toaster({ priority: 'warning', title: 'Gift Deduction', message: '' + "</br> Unable to proceed your request.." });
            }
        }
    });
}
//DGS-DCNM
function set_chit_wallet_screen(id) {
    $.ajax({
        url: base_url + 'index.php/admin_manage/digi_wallet_screen',
        data: { 'id_sch_acc': id },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            console.log(data);
            $('#int_td').css('display', 'block');
            $('#debit_td').css('display', 'block');
            $("#chit_tab tbody").remove();
            $('#pay_count').html(data.tot.pay_count);
            $('#paid_tot').html("INR " + data.tot.total_paid);
            $('#saved_wgt').html(data.tot.saved_wgt + " gms");
            $('#saved_int').html(data.tot.total_benefit + " gms");
            $('#start_date').html(data.tot.join_date);
            $('#allow_pay_till').html(data.tot.allow_pay_till);
            $('#cur_day').html(data.tot.cur_date);
            $('#interest').html(data.tot.interest);
            $('#days_count').html(data.tot.date_difference);
            $('#redeem_duration').html(data.preclose_date);
            $('#debit_int').html(data.preclose_interest);
            $('#debit_intval').html(data.preclose_benefit + " gms");
            $('#debit_day').html(data.tot.allow_pay_till);
            /* if(data.tot.interest != ''){
            	$('#int_td').css('display','block');
            	$('#happy_td').css('display','block');
            	var app = '';
             app += '<tr><td style="text-align:center">Total interest saved till '+data.tot.cur_date+' <br> Interest : '+data.tot.interest+'</td><td style="text-align:center">'+data.tot.total_benefit+' grms</td>';
            app +='</tr><tr><td colspan="2" style="text-align:center">Date Crossed... STAY HAPPY 😀</td></tr>';
            $('#chit_tab').append(app);
            }else{
            	$('#int_td').css('display','none');
            	$('#happy_td').css('display','none');
            } */
            if (data.tot.interest == '') {
                $('#int_td').css('display', 'none');
                $('#happy_td').css('display', 'none');
                $('#debit_td').css('display', 'none');
            }
            document.querySelector("#chit_report_link").setAttribute("href", base_url + 'index.php/admin_manage/chit_detail_report/' + id);
        }
    });
}
//DGS-DCNM ends....
// 05-12-2022
//Commented by Durga 21.06.2023
/*$('#add_benefits').keyup(function(event){
	calculate_closing_balance();
});*/
//webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB
function take_snapshot(type) {
    //Snap Shots Disables
    pre_img_resource = [];
    pre_img_files = [];
    $('#snap_shots').prop('disabled', true);
    if (type == 'pre_images') {
        var preview = 'uploadArea_p_stn';
    }
    Webcam.snap(function(data_uri) {
        $(".image-cust").val(data_uri);
        pre_img_resource.push({ 'src': data_uri, 'name': (Math.floor(100000 + Math.random() * 900000)) + 'jpg', 'is_default': "0" });
        pre_img_files.push(data_uri);
        alert("Your Webcam Images Take Snap Shot Successfullys.");
    });
    setTimeout(function() {
        var resource = [];
        $('#' + preview + ' div').remove();
        if (type == 'pre_images') {
            resource = pre_img_resource;
        }
        $.each(resource, function(key, item) {
            if (item) {
                var div = document.createElement("div");
                div.setAttribute('class', 'images');
                div.setAttribute('id', +key);
                param = { "key": key, "preview": preview, "stone_type": type };
                div.innerHTML += "<span style='float:left;'><a onclick='remove_stn_img(" + JSON.stringify(param) + ")' style='cursor:pointer;color:inherit;' data-toggle='tooltip' data-placement='bottom' title='Delete This Images'>&nbsp;<i class='fa fa-trash'></i></a></span>&nbsp;&nbsp;<img class='thumbnail' src='" + item.src + "'" + "style='width: 100px;height: 100px;'/>";
                $('#' + preview).append(div);
            }
            $('#lot_img_upload').css('display', '');
        });
        $('#snap_shots').prop('disabled', false);
    }, 1000);
}

function remove_stn_img(param) {
    $('#' + param.preview + ' #' + param.key).remove();
    $('#customer_images').val();
    var preview = $('#cus_img_preview');
    preview.prop('src', '');
    preview.css('display', 'none');
    $('#customer_images').val();
}
//webcam upload ends....
//Crated by RK -13/12/2022
function get_otpstatus_for_gift() {
    var mobile_data = $("#mobile_number").val();
    if (mobile_data == "") {
        $("#button_otp").attr("disabled", true);
    }
    $.ajax({
        url: base_url + 'index.php/admin_settings/get_otp_giftstatus',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            var result = data.general;
            console.log(result);
            var scheme_join_otp = result.otp_scheme_join;
            $('#scheme_join_otp').val(scheme_join_otp)
            var otpval = result.isOTPReqToGift;
            var otpexp = result.giftOTP_exp; //line added by durga 20/12/2022
            $('#otp_exp').val(otpexp); //line added by durga 20/12/2022
            $('#otp_value').val(otpval);
            var otpvalue = $('#otp_value').val();
            if (mobile_data == "") {
                $("#button_otp").attr("disabled", true);
            }
            if (otpvalue == 1) {
                var filterval = "blur(8px)";
                //	$("#gift_list").attr('disabled',true);
                //	$("#prize_details").attr('disabled',true);
                $('#gift_articles').css('filter', filterval);
                $('#gift_articles').css('pointer-events', 'none');
                //	$("#button_otp").attr("disabled",true);
            } else {
                $("#div_showotp_button").hide();
            }
        }
    });
}
//send otp button in verify otp modal - gift issue --- starts here
//var fewSecondsgift = 90;
$('#send_otp_gift').click(function(event) {
    var fewSecondsgift = $('#otp_exp').val(); //line added by durga 20/12/2022
    $("#send_otp_gift").attr('disabled', true);
    setTimeout(function() {
        $("#send_otp_gift").attr('disabled', false);
    }, fewSecondsgift * 1000); //line altered by durga 20/12/2022
    $("#otp_txt_box").val(''); //line added by durga 20/12/2022
    $("#otp_txt_box").show();
    gift_send_otp();
});
$('#send_otp_sche_join').click(function(event) {
    var fewSecondsgift = $('#otp_exp').val(); //line added by durga 20/12/2022
    $("#send_otp_sche_join").attr('disabled', true);
    setTimeout(function() {}, fewSecondsgift * 1000); //line altered by durga 20/12/2022
    $("#otp_txt_boxs").val(''); //line added by durga 20/12/2022
    $("#otp_txt_boxs").show();
    send_otp_sche_join();
});

function send_otp_sche_join() {
    $("div.overlay").css("display", "block");
    var cust_id = $("#id_customer").val();
    var mobile = $.trim($("#txt_mob").val());
    $.ajax({
        url: base_url + "index.php/admin_manage/sendotp_scheme_join",
        type: "POST",
        data: { mobile: mobile, id_cust: cust_id },
        //dataType:'json',
        success: function(data) {
            $.toaster({
                priority: 'success',
                title: 'Success',
                message: "OTP verified successfully"
            });
        }
    });
}
$('#verify_otp_sche_join').click(function(event) {
    $("div.overlay").css("display", "block");
    var otp = $("#otp_scheme").val();
    console.log(otp);
    $.ajax({
        url: base_url + "index.php/admin_manage/verifyotp_scheme_join",
        type: "POST",
        data: { otp: otp },
        dataType: 'json',
        success: function(data) {
            console.log(data.result);
            if (data.result == 1) {
                {
                    $.toaster({
                        priority: 'success',
                        title: 'Success',
                        message: "OTP verified successfully"
                    });
                    var filterval = "blur(0px)";
                    $('#verify_otp_scheme_join').modal('toggle');
                    $('#scheme_join_otp_valid').val('1')
                    $('#acc_join').attr('onsubmit', 'return true;');
                    // $("#submit").click()
                }
            } else if (data.result == 5) {
                $.toaster({
                    priority: 'warning',
                    title: 'warning',
                    message: data.msg
                });
            } else if (data.result == 6) {
                $.toaster({
                    priority: 'warning',
                    title: 'warning',
                    message: data.msg
                });
            }
        }
    });
});
//send otp button in verify otp modal - gift issue --- ends here
function gift_send_otp() {
    $("div.overlay").css("display", "block");
    var cust_id = $("#id_customer").val();
    var mobile = $.trim($("#txt_mobile").val());
    $.ajax({
        url: base_url + "index.php/admin_manage/sendotp_gift",
        type: "POST",
        data: { mobile: mobile, id_cust: cust_id },
        //dataType:'json',
        success: function(result) {
            //	alert();
            console.log(result);
        }
    });
}
$('#verify_otp_gift').click(function(event) {
    $("div.overlay").css("display", "block");
    var otp = $("#otp_data").val();
    console.log(otp);
    $.ajax({
        url: base_url + "index.php/admin_manage/verifyotp_gift",
        type: "POST",
        data: { otp: otp },
        dataType: 'json',
        success: function(data) {
            //alert();
            console.log(data.result);
            if (data.result == 1) {
                {
                    $.toaster({
                        priority: 'success',
                        title: 'Success',
                        message: "OTP verified successfully"
                    });
                    var filterval = "blur(0px)";
                    $('#verify_otp_modal').modal('toggle');
                    $('#gift_articles').css('filter', filterval);
                    //	$("#gift_list").attr("disabled",false);
                    //	$("#prize_details").attr("disabled",false);
                    $('#gift_articles').css('pointer-events', 'auto');
                    $("#button_otp").hide();
                }
            } else if (data.result == 5) {
                $.toaster({
                    priority: 'warning',
                    title: 'warning',
                    message: data.msg
                });
            } else if (data.result == 6) {
                $.toaster({
                    priority: 'warning',
                    title: 'warning',
                    message: data.msg
                });
            }
        }
    });
});
// created by Durga starts here 13.02.2023
// Creating Multiple gift chart for CJ 
$("#add_gift_chart").on("click", function() {
    $("#err_msg").text("");
    var delarr_len = deleted_array.length;
    var table_rows = $('#chart_gift_creation_tbl tbody tr').length;
    //if any gift added 
    var branch_setting = $("#branch_setting").val();
    var emp_login_branch = $("#emp_branch").val();
    var selected_branch = $("#branch_select").val();
    if (branch_setting == 1 && emp_login_branch == '' && (selected_branch == null || selected_branch == '')) {
        $.toaster({
            priority: 'warning',
            title: 'Warning',
            message: '' +
                "</br>Please Select Branch"
        });
    } else {
        if (table_rows > 0) {
            console.log(deleted_array);
            // rows_added variable holds total number of rows added including deleted rows
            for (var i = 1; i <= rows_added; i++) {
                var t = 0;
                //deleted_array holds the id of deleted rows
                //checking for whether all the rows are filled with values before adding another row 
                //not necessary to check for deleted rows 
                if (deleted_array.includes(i) === false) {
                    var value = $("#gift_quantity" + i).val();
                    if (value == '' || value == null || value == 0) {
                        //if any row is not filled stop checking and stop the loop
                        t = 1;
                        break;
                    }
                }
            }
            //if all rows are filled add new row
            if (t == 0) {
                create_gift_chart_table();
            } else {
                //$("#err_msg").text("Please fill all the gift details");
                $.toaster({
                    priority: 'warning',
                    title: 'Warning',
                    message: '' +
                        "</br>Please fill all the Gift Details"
                });
            }
        }
        //if no gift added 
        else {
            create_gift_chart_table();
        }
    }
});
// function to create row in gift table 
function create_gift_chart_table() {
    //rows_added variable is incremented every row added ,it holds total number of rows added including deleted rows
    rows_added++;
    var i = 1;
    var table_rows = $('#chart_gift_creation_tbl tbody tr').length;
    //table_rows=table_rows+1;
    table_rows = rows_added;
    i = table_rows;
    var row = "<tr rowID='" + i + "' id='" + table_rows + "'>" +
        "<td>" +
        "<select style='width:200px;' required name='gifts' id='gift_list_" + table_rows + "' name='gift_select[" + table_rows + "]' class='form-control' onchange='set_selected_value(this.value," + table_rows + ")'></select><input type='hidden' id='quantity_limit_" + table_rows + "' />"
        //+"<select name='gifts' id='gift_list' class='form-control'></select>"
        +
        "<input style='width:100px;' id='gift_val_" + table_rows + "' name='gift_list_data[" + table_rows + "]' type='hidden' value=''/>" +
        "</td>" +
        "<td>"
        //quantity for each gift
        +
        "<input style='width:80px;' type='number' id='gift_quantity" + table_rows + "' name='gift_quantity[" + table_rows + "]' required='true' oninput='check_quantity_limit(this.value," + table_rows + ")' disabled/><br/><span class='error' id='limit_msg" + table_rows + "'></span></td>"
        //gift bar code value for each gift
        +
        "<td><input style='width:110px;' type='text' id='gift_barcode" + table_rows + "' name='gift_barcode[" + table_rows + "]' disabled/><br/</td>" +
        "<td><div><button id='btn_delete_" + table_rows + "' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>"; +
    "</tr>";
    $('#chart_gift_creation_tbl tbody').append(row);
    //table_row_length hidden input in form for adding and updating in db
    $("#table_row_length").val(table_rows);
    get_gift_names();
}
// onchange function for select box in every row
function set_selected_value(selected, table_rows) {
    //quantity input will only be enable if the value selected in dropdown
    if (selected) {
        $("#gift_val_" + table_rows).val(selected);
        $("#gift_quantity" + table_rows).attr('disabled', false);
        $("#gift_barcode" + table_rows).attr('disabled', false);
        $.ajax({
            url: base_url + "index.php/admin_settings/get_gift_name_byId",
            type: "POST",
            data: { "id": selected },
            dataType: 'json',
            success: function(data) {
                var balance_stock = parseInt(data.quantity) - parseInt(data.out_stock);
                $("#quantity_limit_" + table_rows).val(balance_stock);
                $("#limit_msg" + table_rows).text("Qty limit : " + balance_stock);
            }
        });
    } else {
        $("#gift_quantity" + table_rows).val("");
        $("#gift_quantity" + table_rows).attr('disabled', true);
        $("#gift_barcode" + table_rows).attr('disabled', true);
    }
}

function check_quantity_limit(value, table_rows) {
    var limit = parseInt($("#quantity_limit_" + table_rows).val());
    if (value == 0 || value < 0) {
        $.toaster({
            priority: 'warning',
            title: 'Warning',
            message: '' +
                "</br>please enter valid quantity"
        });
        $("#gift_quantity" + table_rows).val("");
    } else if (value > limit) {
        //    alert("please enter quantity less than "+limit);
        $.toaster({
            priority: 'warning',
            title: 'Warning',
            message: '' +
                "</br>please enter quantity less than " + limit
        });
        $("#gift_quantity" + table_rows).val("");
    }
}
$("#chart_gift_creation_tbl").on('click', '.delete', function() {
    $(this).closest('tr').remove();
    deleted = $('#chart_gift_creation_tbl tbody tr').length;
    var trid = $(this).closest('tr').attr('id');
    //alert(trid);
    deleted_array.push(parseInt(trid));
    console.log(deleted_array);
    console.log("deleted length" + deleted_array.length);
});
// created by Durga ends here 13.02.2023
function calculate_closing_balance() {
    var closing_benefits = 0;
    var closing_deductions = 0;
    var is_weight = $('#is_weight').val();
    var show_as_amtwgt = $('#store_as_closing').val();
    var store_as_amtwgt = $("input[name='account[store_closing_balance_as]']:checked").val();
    if ($('#benefits').val() == '') {
        $('#benefits').val(parseFloat('0').toFixed(2));
    }
    if ($('#add_benefits').val() != '' || $('#add_charges').val() != '') {
        //for addtional benefits & detection new code update....
        var metal_rate;
        var metal_rate_val = parseFloat($('#metal_rate').val());
        if (metal_rate_val != '' && metal_rate_val != null && metal_rate_val != 'undefined' && metal_rate_val != 0 && !isNaN(metal_rate_val)) {
            metal_rate = metal_rate_val;
        } else {
            metal_rate = 1;
        }
        var voucher_deduct_amt = $('#voucher_deduction').val();
        var voucher_weight;
        if (voucher_deduct_amt != '' && voucher_deduct_amt != null && voucher_deduct_amt != 'undefined' && voucher_deduct_amt != 0) {
            voucher_weight = formatMetalWeight(parseFloat(voucher_deduct_amt) / metal_rate);
        } else {
            voucher_weight = 0;
        }
        var addBenefitAmtType = $('#fixed_wgtschamt').val(); //value = 0
        var addBenefitWgtType = $('#fixed_wgtschwgt').val(); //value = 1
        var sch_type = $('#sch_typ').val();
        var flexSch_type = $('#flexi_sch_typ').val();
        var wgt_convert = $('#wgt_convert').val();
        var add_benefit = ($('#add_benefits').val() > 0 ? parseFloat($('#add_benefits').val()) : 0);
        var add_detection = ($('#add_charges').val() > 0 ? parseFloat($('#add_charges').val()) : 0);
        var benefits = ($('#benefits').val() > 0 ? parseFloat($('#benefits').val()) : 0);
        if (show_as_amtwgt == 1 && store_as_amtwgt == 0) {
            $('#is_wgt').css('display', 'none');
            $('#is_amt').css('display', '');
            var closing_balance = parseFloat($('#closing_bal_hidden').val());
        } else if (show_as_amtwgt == 1 && store_as_amtwgt == 1) {
            $('#is_wgt').css('display', '');
            $('#is_amt').css('display', 'none');
            var closing_balance = parseFloat($('#closing_bal_hidden').val());
        } else {
            if (is_weight == 1) {
                $('#is_wgt').css('display', '');
                $('#is_amt').css('display', 'none');
                var closing_balance = parseFloat($('#closing_bal_hidden').val());
            } else {
                $('#is_wgt').css('display', 'none');
                $('#is_amt').css('display', '');
                var closing_balance = parseFloat($('#closing_bal_hidden').val());
            }
        }
        // var closing_wgt_amount  = parseFloat($('#closing_paid_amt').val());
        var closing_wgt_amount = parseFloat($('#closing_wgt_amount').val());
        //tkv...
        var tot_adv_amt_paid = parseFloat($('#tot_adv_amt_paid').val());
        var tot_adv_wgt_paid = parseFloat($('#tot_adv_wgt_paid').val());
        var tot_adv_benefit = parseFloat($('#tot_adv_benefit').val());
        if (add_detection > 0 || (add_benefit > 0 && (addBenefitAmtType == 0 || addBenefitWgtType == 1))) {
            //For all type of amount schemes...  //take closing_balance field
            if ((show_as_amtwgt == 1 && store_as_amtwgt == 0) || (show_as_amtwgt == 0 && is_weight == 0)) {
                var c_amt = '0.00';
                if (addBenefitAmtType == 0 && addBenefitAmtType != '' && add_benefit > 0) {
                    var c_bal = (closing_balance + add_benefit);
                    var c_amt = (closing_wgt_amount + add_benefit);
                } else if (addBenefitWgtType == 1 && addBenefitWgtType != '' && add_benefit > 0) {
                    var c_bal = (closing_balance + (add_benefit * metal_rate));
                    var c_amt = (closing_wgt_amount + (add_benefit * metal_rate));
                } else {
                    var c_bal = closing_balance;
                    var c_amt = closing_wgt_amount;
                }
                if (add_detection > 0) {
                    var c_bal = (c_bal - add_detection);
                    var c_amt = (c_amt - add_detection);
                }
                $('#closing_amount').val(parseFloat(c_bal).toFixed(2));
                $('#closing_balance').val(parseFloat(c_bal).toFixed(2));
            }
            //for all type of weight schemes...  //take closing_balance field for weight and closing_wgt_amount field for amt
            else if ((show_as_amtwgt == 1 && store_as_amtwgt == 1) || (show_as_amtwgt == 0 && is_weight == 1)) {
                if (addBenefitAmtType == 0 && addBenefitAmtType != '' && add_benefit > 0) {
                    var c_bal = (closing_balance + (add_benefit / metal_rate) + benefits);
                    var c_amt = (closing_wgt_amount + add_benefit);
                } else if (addBenefitWgtType == 1 && addBenefitWgtType != '' && add_benefit > 0) {
                    //for add benefit as weight...
                    var c_bal = (closing_balance + add_benefit + benefits);
                    var c_amt = (closing_wgt_amount + (add_benefit * metal_rate));
                } else {
                    var c_bal = closing_balance + benefits;
                    var c_amt = closing_wgt_amount;
                }
                if (add_detection > 0) {
                    var c_bal = (c_bal - (add_detection / metal_rate));
                    var c_amt = (c_amt - add_detection);
                }
                $('#closing_balance').val(formatMetalWeight((c_bal)));
            }
            // $('#closing_wgt_amount').val(parseFloat(c_amt).toFixed(2));      // amt for wgt scheme alone...
            $('#closing_amount').val(parseFloat(c_amt).toFixed(2));
        } else {
            var c_amt = closing_wgt_amount;
            if (store_as_amtwgt == 1) {
                var c_bal = $('#closing_bal_hidden').val();
                $('#closing_balance').val(formatMetalWeight((c_bal)));
            } else if (store_as_amtwgt == 0) {
                var c_bal = closing_balance;
                $('#closing_balance').val(formatMetalWeight((c_bal)));
            } else {
                if (is_weight == 1) {
                    var c_bal = $('#closing_bal_hidden').val();
                    $('#closing_balance').val(formatMetalWeight((c_bal)));
                } else {
                    var c_bal = closing_balance;
                    $('#closing_balance').val(parseFloat(c_bal).toFixed(2));
                }
            }
            //for all scheme...
            $('#closing_amount').val(parseFloat(c_amt).toFixed(2)); // amt for wgt scheme alone...
        }
    } else {
        // var c_bal     = parseFloat($('#closing_bal_hidden').val());
        var c_amt = parseFloat($('#closing_wgt_amount').val());
        if (store_as_amtwgt == 1) {
            $('#is_wgt').css('display', '');
            $('#is_amt').css('display', 'none');
            var c_bal = $('#cls_wgt').val();
            // $('#closing_balance').val(session_data.metal_wgt_roundoff == 0 ? trimDecimal((c_bal), session_data.metal_wgt_decimal) : c_bal.toFixed(session_data.metal_wgt_decimal));
        $('#closing_balance').val(formatMetalWeight((c_bal)));
        } else {
            $('#is_wgt').css('display', 'none');
            $('#is_amt').css('display', '');
            var c_bal = parseFloat($('#closing_bal_hidden').val());
            // $('#closing_balance').val(session_data.metal_wgt_roundoff == 0 ? trimDecimal((c_bal), session_data.metal_wgt_decimal) : c_bal.toFixed(session_data.metal_wgt_decimal));
            $('#closing_balance').val(parseFloat(c_bal).toFixed(2));
        } //for all scheme...
        $('#closing_amount').val(parseFloat(c_amt).toFixed(3)); // amt for wgt scheme alone...
    }
    $('#closing_deductions').val(parseFloat(closing_deductions).toFixed(2));
    $('#closing_benefits').val(parseFloat(closing_benefits).toFixed(2));
    $('#remark_close').css("display", "block");
    $('.close_actionBtns').css("display", "block");
    // 	$('#close_actionBtns').css("display","block");
    $('#calc_blc').attr('disabled', 'disabled');
}
$('#date_Select').select2().on("change", function(e) {
    if (this.value != '') {
        var from_date = $('#account_list1').text();
        var to_date = $('#account_list2').text();
        var id_branch = $("#branch_select").val();
        var id_customer = $("#id_customer").val();
        var id_scheme = $('#id_scheme').val();
        get_scheme_acc_list(from_date, to_date, id_branch, id_customer, id_scheme);
    }
});
$("#voucher_img").change(function() {
    validateImage(this);
});

function validateImage() {
    if (arguments[0].id == 'voucher_img') {
        var preview = $('#voucher_img_preview');
    }
    var file = arguments[0].files[0];
    var reader = new FileReader();
    reader.onloadend = function() {
        preview.prop('src', reader.result);
    }
    if (file) {
        reader.readAsDataURL(file);
        preview.css('display', '');
    } else {
        preview.prop('src', '');
        preview.css('display', 'none');
    }
}
$(document).on('input', '#otp_scheme', function(id) {
    $('#verify_otp_sche_join').removeAttr('disabled');
});
$("#date_of_birth").change(function() {
    console.log(this.value)
    calculateAge(this.value);
})

function calculateAge(selectedDate) {
    if (selectedDate) {
        var today = new Date();
        var selectedDateTime = parseDate(selectedDate, 'dd/mm/yyyy');
        console.log(selectedDateTime)
            // 	var selectedDateTime = new Date(selectedDate);
        if (selectedDateTime > today) {
            alert("Invalid date ");
            $("#emp_age").val('');
            $('#date_of_birth').val('')
                // You can choose to handle this error in a different way if needed
            return;
        }
        var dateParts = selectedDate.split('-');
        var dob_year = dateParts[0];
        var age = today.getFullYear() - dob_year;
        $("#emp_age").val(age);
    }
}
//Parsing string type date to original date format			  
function parseDate(dateString, format) {
    var parts = dateString.split('-');
    var year = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10);
    // Check if the month is in the range 01 to 09
    if (month >= 1 && month <= 9) {
        month -= 1; // Subtract 1 from the month
    }
    var day = parseInt(parts[2], 10);
    return new Date(year, month, day);
}

function __label2(caption, value) {
    return "<div class='col-sm-10'><div class='form-group'><label>" + caption + "</label><label class='pull-right' style='font-weight:normal!important;' id=''>" + value + "</label></div></div>";
}
//Scheme Account Approval --start 
function sch_approve() {
    // alert('sd')f
    if ($("input[name='account_approval[]']:checked").val()) {
        var selected = [];
        var in_progress = false;
        $("#sch_acc_list tbody tr").each(function(index, value) {
            if ($(value).find("input[name='account_approval[]']:checked").is(":checked")) {
                data = {
                    'id_scheme_account': $(value).find(".sch_approve_acc").val(),
                    'active': 1
                }
                console.log(data);
                in_progress = true;
                selected.push(data);
            }
        });
        sch_acc_data = selected;
        if (in_progress == true) {
            console.log(sch_acc_data);
            update_sch_account_status(sch_acc_data);
        }
    } else {
        $.toaster({ priority: 'warning', title: 'Scheme Approval Warning', message: '' + "</br>Please Choose Any one Scheme Account" });
    }
}

function update_sch_account_status(sch_acc_data) {
    $.ajax({
        type: 'post',
        url: base_url + 'index.php/admin_manage/update_sche_acc_sts',
        dataType: 'json',
        data: { 'sch_data': sch_acc_data },
        success: function(data) {
            $(".overlay").css('display', 'none');
            console.log(data);
            if (data) {
                $.toaster({
                    priority: 'success',
                    message: '' + "</br>Scheme Account Status  Updated successfully ",
                    settings: {
                        timeout: 4000, // Time in milliseconds (e.g., 3000ms = 3 seconds)
                    }
                });
                // window.location.reload(true);
                setTimeout(function() {
                    window.location.reload(true);
                }, 4 * 1000);
            } else {
                $.toaster({
                    priority: 'warning',
                    title: 'Data Warning',
                    message: '' + "</br>Scheme Account Status Not Updated ",
                    settings: {
                        timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
                    }
                });
            }
        }
    });
}

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
    var select_date = "<div style='text-align: center;'><b><span style='font-size:12pt;'>" + company_code + "</span></b></br>" +
        "<span style='font-size:11pt;'>" + company_address1 + "</span></br>" +
        "<span style='font-size:11pt;'>" + company_address2 + company_city + "-" + pincode + "</span></br>"; +
    "<span style='font-size:11pt;'>GSTIN:" + company_gst_number + ", EMAIL:" + company_email + "</span></br>"
    if (company_gst_number != '' && company_gst_number != null) {
        select_date += "<span style='font-size:11pt;'>GSTIN:" + company_gst_number + "</span></br>";
    }
    if (company_email != '') {
        select_date += " EMAIL:" + company_email + "</span></br>";
    }
    if (phone != '') {
        select_date += "<span style='font-size:11pt;'>Contact :" + phone + "</span></br>"
    }
    select_date += "<b><span style='font-size:15pt;'>" + title.toUpperCase() + "</span></b></br>";
    if (from_date != '' && to_date != '') {
        select_date += "<span style=font-size:13pt;>Details &nbsp;&nbsp;From Date&nbsp;:&nbsp;" + from_date + " &nbsp;&nbsp;To Date&nbsp;:&nbsp;" + to_date + "</span><br>";
    }
    select_date += "<span style=font-size:11pt;>Print Taken On : " + moment().format("dddd, MMMM Do YYYY, h:mm:ss a") +
        "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" +
        "<span style=font-size:11pt;>Print Taken By : " + $('.hidden-xs').html() + "</span></div>";
    return select_date;
}

function getBranchTitle() {
    var login_branch = $("#branch_filter").val();
    var branch_name;
    var selected_branch
    if (isValid(login_branch)) {
        branch_name = $("#login_branch_name").val();
    } else {
        if (ctrl_page[1] == 'closed_acc_report') {
            selected_branch = $('#close_branch_select option:selected').toArray().map(item => item.text).join();
        } else {
            selected_branch = $('#branch_select option:selected').toArray().map(item => item.text).join();
        }
        if (isValid(selected_branch)) {
            branch_name = selected_branch;
        } else {
            branch_name = "All Branch"
        }
    }
    return branch_name;
}

function isValid(value) {
    return value !== null && value !== undefined && !isNaN(value);
}

function formatDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1; // Month is zero-based
    var year = date.getFullYear();
    // Add leading zeros if necessary
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    return day + '-' + month + '-' + year;
}

function get_excel_title(from_date, to_date, title) {
    var company_name = $('#company_name').val();
    var company_code = $('#company_code').val();
    var company_address1 = $('#company_address1').val();
    var company_address2 = $('#company_address2').val();
    var company_city = $('#company_city').val();
    var pincode = $('#pincode').val();
    var company_email = $('#company_email').val();
    var company_gst_number = $('#company_gst_number').val();
    var phone = $('#phone').val();
    var select_date = "";
    var string = "";
    select_date += company_code;
    select_date += company_address1;
    select_date += company_address2 + company_city + "-" + pincode;
    select_date += "GSTIN:" + company_gst_number + ", EMAIL:" + company_email;
    select_date += "Contact :" + phone;
    select_date += title;
    select_date += (from_date !== '' && to_date != '' ? "Details From Date " + from_date + " To Date " + to_date : "");
    select_date += "Print Taken On : " + moment().format("dddd, MMMM Do YYYY, h:mm:ss a");
    select_date += "Print Taken By :" + $('.hidden-xs').html();
    // + " Login Branch : " + $('#branch_name').html()
    return select_date;
}

function checkLength(input) {
    if (input.value.length > 10) {
        input.value = input.value.slice(0, 10); // Limit the input to 10 characters
    }
}
/* TopUp Scheme Starts Esakki_20250802 */
// Allow only numbers with up to 2 decimals for #topup_amount
$('#topup_amount').on('input', function() {
    let val = $(this).val();
    val = val.replace(/[^0-9.]/g, '') // Remove non-numeric/non-dot
        .replace(/(\..*?)\..*/g, '$1') // Keep only first dot
        .replace(/^(\d+\.\d{0,2}).*$/, '$1'); // Limit to 2 decimal places
    $(this).val(val);
    calc_booking_amtwgt(val);
});
// Allow only numbers with up to 3 decimals for #topup_weight
$('#topup_weight').on('input', function() {
    let val = $(this).val();
    val = val.replace(/[^0-9.]/g, '')
        .replace(/(\..*?)\..*/g, '$1')
        .replace(/^(\d+\.\d{0,3}).*$/, '$1'); // Limit to 3 decimal places
    $(this).val(val);
    calc_booking_amtwgt(val);
});
$('#topup_amount').on('change', function() {
    let val = parseFloat($(this).val()).toFixed(2);
    var topup_min_value = parseFloat($('#topup_min_value').val());
    var topup_max_value = parseFloat($('#topup_max_value').val());
    var topup_denomination = parseFloat($('#topup_denomination').val());
    var valid = true;
    /*if (topup_min_value > val || topup_max_value < val || val % topup_denomination != 0){
    	$valid = false;
    }*/
    if (val < topup_min_value || val > topup_max_value || (topup_denomination > 0 && val % topup_denomination != 0 ? true : false)) {
        valid = false;
    }
    if (!$valid) {
        $('#topup_weight').val(0.00);
        $('#topup_amount').val(0.00);
        $.toaster({
            priority: 'warning',
            title: 'Warning',
            message: '' + "</br>Enter valid booking amount...",
            settings: {
                timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
            }
        });
    }
});
// Allow only numbers with up to 3 decimals for #topup_weight
$('#topup_weight').on('change', function() {
    let val = parseFloat($(this).val()).toFixed(3);
    var topup_min_value = parseFloat($('#topup_min_value').val());
    var topup_max_value = parseFloat($('#topup_max_value').val());
    var topup_denomination = parseFloat($('#topup_denomination').val());
    var valid = true;
    /*if (topup_min_value > val || topup_max_value < val || val % topup_denomination != 0){
    	$valid = false;
    }*/
    if (val < topup_min_value || val > topup_max_value || (topup_denomination > 0 && val % topup_denomination != 0 ? true : false)) {
        valid = false;
    }
    if (!valid) {
        $('#topup_weight').val(0.00);
        $('#topup_amount').val(0.00);
        $.toaster({
            priority: 'warning',
            title: 'Warning',
            message: '' + "</br>Enter valid booking weight...",
            settings: {
                timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
            }
        });
    }
});

function calc_booking_amtwgt(val) {
    var book_type = $('#topup_booking_type').val();
    var book_rate = parseFloat($('#topup_rate').val()).toFixed(2);
    var topup_min_value = parseFloat($('#topup_min_value').val());
    var topup_max_value = parseFloat($('#topup_max_value').val());
    var topup_denomination = parseFloat($('#topup_denomination').val());
    if (book_type == 1) {
        var book_amt = parseFloat(val).toFixed(2);
        if (topup_min_value <= book_amt && topup_max_value >= book_amt /*  && (book_amt % topup_denomination == 0) */ ) {
            book_wgt = formatMetalWeight(parseFloat(book_amt / book_rate));
            $('#topup_weight').val(book_wgt);
        } else if (topup_max_value < book_amt) {
            $('#topup_weight').val(0.00);
        }
    } else if (book_type == 2) {
        var book_wgt = formatMetalWeight(val);
        if (topup_min_value <= book_wgt && topup_max_value >= book_wgt /*  && (book_wgt % topup_denomination == 0) */ ) {
            book_amt = parseFloat(book_wgt * book_rate).toFixed(2);
            $('#topup_amount').val(book_amt);
        } else if (topup_max_value < book_wgt) {
            $('#topup_weight').val(0.000);
        }
    }
}
if (ctrl_page[0] == 'account' && ctrl_page[1] == 'edit') {
    var book_type = $('#topup_booking_type').val();
    var tot_paid_ins = $('#total_paid_ins').val();
    var is_topup_scheme = $('#is_topup_scheme').val();
    if (is_topup_scheme == 1) {
        $('#topup_sch').css('display', 'block');
        if (tot_paid_ins > 0) {
            $('#topup_weight').prop('readonly', true);
            $('#topup_amount').prop('readonly', true);
        } else {
            if (book_type == 1) {
                $('#topup_weight').prop('readonly', true);
            } else if (book_type == 2) {
                $('#topup_amount').prop('readonly', true);
            }
        }
    }
}
/* TopUp Scheme Ends Esakki_20250802 */

// --- KYC Dynamic fetch for scheme account opening ---
function fetch_dynamic_kyc() {
    if (typeof window.trigger_dynamic_kyc_modal === 'function') {
        window.trigger_dynamic_kyc_modal('#scheme', '#id_customer', '#firstPayment_amt', null, '#save', true);
    }
}
