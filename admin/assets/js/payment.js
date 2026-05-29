var path = url_params();
var ctrl_page = path.route.split('/');
var payment_device_details = [];
var payment_bank_details = [];
var nb_values = [];
var nbpayment = [];
var bank_details = []; //esakki
let indianCurrency = Intl.NumberFormat("en-IN"); // esakki 01-10
count = 0;
//voucher mode starts
var vch_values = [];
var vchpayment = [];
var rows_added;
//voucher mode ends
$(document).ready(function () {
    $('#select_employee').select2({
        placeholder: "Select Employee",
        allowClear: true
    }).on("change", function () {
        $("#id_employee1").val(this.value || ''); // Set empty string if no value is selected
    }).on("select2:clear", function () {
        $("#id_employee1").val(''); // Explicitly clear the hidden input when using Select2 clear button
    });
    $(document).on('blur', '.ref_no', function () {
        var index = $(this).attr('id').split('_').pop();
        var input_type = $(this).attr('name').split('_')[0];
        var ref_span_length = $("#ref_span_" + index).text().length;
        var nb_ref_span_length = $("#nb_ref_span_" + index).text().length;
        if (ref_span_length > 0) {
            $("#cardref_no_" + index).val("");
            $("#ref_span_" + index).text("");
        } else if (nb_ref_span_length > 0) {
            $("#nbref_no_" + index).val("");
            $("#nb_ref_span_" + index).text("");
        }
    });
    // esakki 17-09
    $(document).on('blur', '.cheque_no', function () {
        var index = $(this).attr('id').split('_').pop();
        var input_type = $(this).attr('name').split('_')[0];
        var chq_ref_span_length = $("#chq_ref_span_" + index).text().length;
        if (chq_ref_span_length > 0) {
            $("#chq_no_" + index).val("");
            $("#chq_ref_span_" + index).text("");
        }
    });
    function startTimer() {
        timer = setInterval(updateTimer, 1000);
    }
    function updateTimer() {
        if (timeLeft === 0) {
            clearInterval(timer);
            document.getElementById("timer").innerText = "";
            $("#resendotp").prop("disabled", false);
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
    $('#pay_save').on('change', function () {
        $("#timer").css("display", "block");
        clearInterval(timer); // Clear the existing timer
        timeLeft = 5; // Reset the time left
        let hours = Math.floor(timeLeft / 3600);
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes; // Format minutes
        seconds = seconds < 10 ? '0' + seconds : seconds; // Format seconds
        document.getElementById("timer").innerText = "Your OTP will expire within " + hours + "h:" + minutes + "m:" + seconds + "s";
        startTimer();
    });
    get_status();
    let enable = $('#enable_old_sch_acc_no').val();
    if (enable == "1") {
        $('#oldAccWrapper').show(); // show
    } else {
        $('#oldAccWrapper').hide(); // hide
    }
    $("#Scheme_account_no").on('input', function () {
        if ($("#oldAccountCheck").is(":checked")) {
            // allow alphanumeric
            this.value = this.value.replace(/[^a-zA-Z0-9\-]/g, "");
        } else {
            // allow only numbers
            this.value = this.value.replace(/[^0-9]/g, "");
        }
    });
    $("#Scheme_account_no").autocomplete({
        source: function (request, response) {
            var account = $("#Scheme_account_no").val();
            var isOldAccount = $("#oldAccountCheck").is(":checked") ? 1 : 0;
            // var Split = account.split('-');
            // var scheme_code = Split[0];
            // var acc_no = Split[1];
            // console.log(account);return;
            $.ajax({
                url: base_url + 'index.php/admin_customer/ajax_get_scheme_account_list',
                dataType: "json",
                type: 'POST',
                data: { 'acc_no': account, 'old_acc': isOldAccount },
                success: function (data) {
                    var data = JSON.stringify(data);
                    data = JSON.parse(data);
                    var cus_list = new Array(data.length);
                    var i = 0;
                    data.forEach(function (entry) {
                        console.log(entry.mobile);
                        var oldNum = (enable == "1" && entry.old_scheme_acc_number) ?
                            ' (' + entry.old_scheme_acc_number + ')' :
                            '';
                        var customer = {
                            label: entry.name + ' ' + entry.scheme_acc_number + oldNum,
                            id_scheme_account: entry.id_scheme_account,
                            mobile: entry.mobile,
                            id_customer: entry.id_customer
                        };
                        cus_list[i] = customer;
                        i++;
                    });
                    response(cus_list);
                }
            });
        },
        minLength: 1,
        delay: 800, // this is in milliseconds
        select: function (e, i) {
            e.preventDefault();
            $('#Scheme_account_no').prop('disabled', true);
            $('#mobile_number').prop('disabled', true);
            $("#mobile_number").val(i.item.mobile);
            $("#id_customer").val(i.item.id_customer);
            $("#id_scheme_account").val(i.item.id_scheme_account);
            $('.overlay').css('display', 'block');
            $('#scheme_account').empty();
            my_Date = new Date();
            var id_customer = $('#id_customer').val();
            var id_scheme_account = $('#id_scheme_account').val();
            var cus_mobile = $('#customer_mobile').val();
            if ($('#id_customer').val() != '') {
                // Nominee details & OTP verify proceed payments -- STARTS
                /* $.ajax({
                     type:'GET',
                     url:base_url+'index.php/customer/get_customer/'+id_customer,
                     dataType:'json',
                     success:function(data){
                         if(data.nominee_mobile == '' && data.nominee_name == ''){
                             alert("Please update nominee details to proceed for payment.");
                             window.location = base_url+"index.php/customer/edit/"+id_customer;
                         }else{
                              if(cus_mobile !== '' && cus_mobile !== null){
                                 $.ajax({
                                     url:base_url+ "index.php/admin_payment/resend_otp?nocache=" + my_Date.getUTCSeconds(),
                                     data :  {'id_customer':id_customer},  	
                                     type : "POST",
                                     dataType: 'json',
                                     success : function(data){
                                         if(data.result==3){
                                             $('#otp_model').modal({
                                                             backdrop: 'static',
                                                             keyboard: false
                                             });
                                             $("#msg").html(data.msg);
                                             $("div.overlay").css("display", "none"); 
                                        }
                                     }
                                 });
                                 $("div.overlay").css("display", "none"); 
                             }
                         }
                     }
                 });*/
                // Nominee details & OTP verify proceed payments -- ENDS
                $.ajax({
                    type: 'GET',
                    url: base_url + 'index.php/payment/get/ajax/customer/account/' + id_customer + '?nocache=' + my_Date.getUTCSeconds(),
                    dataType: 'json',
                    cache: false,
                    success: function (data) {
                        $('#scheme_account').empty();
                        if ($('#scheme_account').length > 0) {
                            $.each(data.accounts, function (key, acc) {
                                if (id_scheme_account == acc.id_scheme_account) {
                                    $('#scheme_account').append(
                                        $("<option></option>")
                                            .attr("value", acc.id_scheme_account)
                                            .text(acc.scheme_acc_number)
                                    );
                                }
                            });
                            $(".eligible_walletamt").css("display", "none");
                            if (data.wallet_balance) {
                                console.log(data.wallet_balance);
                                $('.wallet_balance').val(parseFloat(data.wallet_balance.wal_balance));
                                $('.redeem_percent').val(parseFloat(data.wallet_balance.redeem_percent));
                                if ($('.wallet_balance').val() != '0') {
                                    $(".eligible_walletamt").css("display", "block");
                                }
                                $('.wallet').val(parseFloat(data.wallet_balance.wal_balance));
                            }
                            $("#scheme_account").select2({
                                placeholder: "Select scheme account",
                                allowClear: true
                            });
                            $("#scheme_account").select2("val", id_scheme_account);
                            // load_account_detail(id_scheme_account);
                        }
                        //disable spinner
                        $('.overlay').css('display', 'none');
                    },
                    error: function (error) {
                        console.log(error);
                        //disable spinner
                        $('.overlay').css('display', 'none');
                    }
                });
            } else {
                $("#scheme_account").select2("val", '');
                $('#scheme_account').empty();
                $('#scheme-detail-box').addClass('box-default');
                $('#mobile_number').val('');
                $('#id_customer').val('');
                $('#id_scheme_account').val('');
                clear_account_detail();
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Invalid Details" });
                //  alert('Invalid Details');
            }
        },
        response: function (e, i) {
            // ui.content is the array that's about to be sent to the response callback.
            if (i.content.length === 0) {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter a valid Acc.no" });
                // alert('Please Enter a valid Acc.no');
                $('#Scheme_account_no').val('');
            }
        },
        focus: function (e, i) {
            e.preventDefault();
            $("#Scheme_account_no").val(i.item.label);
        }
    });
    // for onload issue after scheme account joining.... start 
    var sch_id = $('#id_scheme_account').val();
    var id_cus = $('#id_customer').val();
    if (sch_id != null && sch_id != undefined && sch_id != '' && sch_id != 0) {
        load_account_detail(sch_id);
        loadschemeaccountbyidcus(id_cus);
        //get_branchname();
    }
    function loadschemeaccountbyidcus(id_cus) {
        if (id_cus != '') {
            $.ajax({
                type: 'GET',
                url: base_url + 'index.php/payment/get/ajax/customer/account/' + id_cus + '?nocache=' + my_Date.getUTCSeconds(),
                dataType: 'json',
                cache: false,
                success: function (data) {
                    $('#scheme_account').empty();
                    if ($('#scheme_account').length > 0) {
                        $.each(data.accounts, function (key, acc) {
                            $('#scheme_account').append(
                                $("<option></option>")
                                    .attr("value", acc.id_scheme_account)
                                    .text(acc.scheme_acc_number)
                            );
                        });
                        $(".eligible_walletamt").css("display", "none");
                        if (data.wallet_balance) {
                            console.log(data.wallet_balance);
                            $('.wallet_balance').val(parseFloat(data.wallet_balance.wal_balance));
                            $('.redeem_percent').val(parseFloat(data.wallet_balance.redeem_percent));
                            if ($('.wallet_balance').val() != '0') {
                                $(".eligible_walletamt").css("display", "block");
                            }
                            $('.wallet').val(parseFloat(data.wallet_balance.wal_balance));
                        }
                        $("#scheme_account").select2({
                            placeholder: "Select scheme account",
                            allowClear: true
                        });
                        $("#scheme_account").select2("val", ($('#id_scheme_account').val() != null ? $('#id_scheme_account').val() : ''));
                    }
                    //disable spinner
                    $('.overlay').css('display', 'none');
                },
                error: function (error) {
                    console.log(error);
                    //disable spinner
                    $('.overlay').css('display', 'none');
                }
            });
        }
    }
    // for onload issue after scheme account joining.... ends
    get_payment_device_details(); //#EP
    get_payment_bank_details(); //#EP
    if (ctrl_page[1] == 'insertTrans' || ctrl_page[1] == 'add') {
        load_paystatus_select();
    }
    if (session_data.branch_settings !== 1 && session_data.branch_settings !== undefined && session_data.branch_settings !== null && session_data.branch_settings == '0') {
        getBranchEmployee();
    } else {
        get_branchnames();
        getBranchEmployee();
    }
    //get_paymentModes();
    $('#edit_payment').css('overflow-y', 'auto'); //#EP
    //	get_payments_data_list();
    /*if(ctrl_page[1]=='add')
    {
        $.each(customerListArr, function(key, val)
        {
            customerList.push({'label' : val.mobile+'  '+val.name, 'value' : val.id});
        });	
        $( "#mobile_number" ).autocomplete(
        {
            source: customerList,
            select: function(e, i)
            {
                e.preventDefault();
                $("#mobile_number" ).val(i.item.label);
                $("#id_customer").val(i.item.value);
                var id_customer=$('#id_customer').val();
                $('.overlay').css('display','block');
                var my_Date = new Date();
                if($('#id_customer').val()!='')
                {
                    $.ajax({
                      type: 'GET',
                      url:  base_url+'index.php/payment/get/ajax/customer/account/'+id_customer+'?nocache=' + my_Date.getUTCSeconds(),
                      dataType: 'json',
                      cache:false,
                        success: function(data) 
                        {
                            if($('#scheme_account').length>0)
                                 {
                                    $('#scheme_account').empty();
                                    $("#scheme_account").select2("val",'');
                                    $.each(data.accounts, function (key, acc) {
                                        $('#scheme_account').append(
                                            $("<option></option>")
                                              .attr("value", acc.id_scheme_account)
                                              .text(acc.scheme_acc_number)
                                        );
                                    });
                                    $(".eligible_walletamt").css("display","none");
                                    if(data.wallet_balance){
                                        console.log(data.wallet_balance);
                                        $('.wallet_balance').val(parseFloat(data.wallet_balance.wal_balance));
                                        $('.redeem_percent').val(parseFloat(data.wallet_balance.redeem_percent));
                                        if($('.wallet_balance').val()!='0'){ 
                                            $(".eligible_walletamt").css("display","block"); 
                                        } 
                                        $('.wallet').val(parseFloat(data.wallet_balance.wal_balance));
                                    }
                                    $("#scheme_account").select2({
                                      placeholder: "Select scheme account",
                                        allowClear: true
                                    });		
                                    $("#scheme_account").select2("val", ($('#id_scheme_account').val()!=null?$('#id_scheme_account').val():''));
                                 }
                             //disable spinner
                            $('.overlay').css('display','none');
                        },
                        error:function(error)
                        {
                        console.log(error);
                        //disable spinner
                        $('.overlay').css('display','none');
                        }	
                    });
                }
                else
                {
                    $("#scheme_account").select2("val",'');
                    $('#scheme_account').empty();
                    $('#scheme-detail-box').addClass('box-default');
                    $('#mobile_number').val('');
                    $('#id_customer').val('');
                    $('#id_scheme_account').val('');
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
        });
    }*/
    if (ctrl_page[1] == 'add') {
        /*	$.each(customerListArr, function(key, val)
            {
                customerList.push({'label' : val.mobile+'  '+val.name, 'value' : val.id});
            });	*/
        get_scheme();
        get_payment_device_details();
        get_payment_bank_details();
        $("#mobile_number").autocomplete({
            source: function (request, response) {
                var mobile = $("#mobile_number").val();
                var id_scheme = $("#id_scheme").val();
                $.ajax({
                    url: base_url + 'index.php/admin_customer/ajax_get_customers_list',
                    dataType: "json",
                    type: 'POST',
                    data: { 'mobile': mobile, 'id_scheme': id_scheme },
                    success: function (data) {
                        var data = JSON.stringify(data);
                        data = JSON.parse(data);
                        var cus_list = new Array(data.length);
                        var i = 0;
                        data.forEach(function (entry) {
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
            delay: 300, // this is in milliseconds
            select: function (e, i) {
                e.preventDefault();
                $("#mobile_number").val(i.item.label);
                $("#id_customer").val(i.item.value);
                //$("#id_scheme_account").val(i.item.id_scheme_account);
                $('.overlay').css('display', 'block');
                $('#scheme_account').empty();
                my_Date = new Date();
                var id_customer = $('#id_customer').val();
                var cus_mobile = $('#customer_mobile').val();
                var scheme_acc_number = 0;
                if ($('#id_customer').val() != '') {
                    // Nominee details & OTP verify proceed payments -- STARTS
                    /*$.ajax({
                        type:'GET',
                        url:base_url+'index.php/customer/get_customer/'+id_customer,
                        dataType:'json',
                        success:function(data){
                            if(data.nominee_mobile == '' && data.nominee_name == ''){
                                alert("Please update nominee details to proceed for payment.");
                                window.location = base_url+"index.php/customer/edit/"+id_customer;
                            }else{
                                 if(cus_mobile !== '' && cus_mobile !== null){
                                    $.ajax({
                                        url:base_url+ "index.php/admin_payment/resend_otp?nocache=" + my_Date.getUTCSeconds(),
                                        data :  {'id_customer':id_customer},  	
                                        type : "POST",
                                        dataType: 'json',
                                        success : function(data){
                                            if(data.result==3){
                                                $('#otp_model').modal({
                                                                backdrop: 'static',
                                                                keyboard: false
                                                });
                                                $("#msg").html(data.msg);
                                                $("div.overlay").css("display", "none"); 
                                           }
                                        }
                                    });
                                    $("div.overlay").css("display", "none"); 
                                }
                            }
                        }
                    });*/
                    // Nominee details & OTP verify proceed payments -- ENDS
                    $.ajax({
                        type: 'GET',
                        url: base_url + 'index.php/payment/get/ajax/customer/account/' + id_customer + '?nocache=' + my_Date.getUTCSeconds(),
                        dataType: 'json',
                        cache: false,
                        success: function (data) {
                            $('#scheme_account').empty();
                            if ($('#scheme_account').length > 0) {
                                $.each(data.accounts, function (key, acc) {
                                    /*	if(acc.is_lucky_draw == 1)
                                        {
                                            scheme_acc_number= acc.scheme_group_code+' '+acc.scheme_acc_number;
                                        }
                                        else
                                        {
                                            if(acc.schemeaccNo_displayFrmt == 0)
                                            {   //only acc num
                                                scheme_acc_number= acc.scheme_acc_number;
                                            }
                                            else if(acc.schemeaccNo_displayFrmt == 1)
                                            { //based on acc number generation setting
                                                if(acc.scheme_wise_acc_no==0){
                                                    scheme_acc_number= acc.scheme_acc_number;
                                                }else if(acc.scheme_wise_acc_no==1){
                                                    scheme_acc_number= acc.acc_branch+'-'+acc.scheme_acc_number;
                                                }else if(acc.scheme_wise_acc_no==2){
                                                    scheme_acc_number= acc.code+'-'+acc.scheme_acc_number;
                                                }else if(acc.scheme_wise_acc_no==3){
                                                    scheme_acc_number= acc.code+''+acc.acc_branch+'-'+acc.scheme_acc_number;
                                                }else if(acc.scheme_wise_acc_no==4){
                                                    scheme_acc_number= acc.start_year+'-'+acc.scheme_acc_number;
                                                }else if(acc.scheme_wise_acc_no==5){
                                                    scheme_acc_number= acc.start_year+''+acc.code+'-'+acc.scheme_acc_number;
                                                }else if(acc.scheme_wise_acc_no==6){
                                                    scheme_acc_number= acc.start_year+''+acc.code+''+acc.acc_branch+'-'+acc.scheme_acc_number;
                                                }
                                            }
                                            else if(acc.schemeaccNo_displayFrmt == 2)
                                            {  //customised
                                                scheme_acc_number= acc.scheme_acc_number;
                                            }
                                        }*/
                                    $('#scheme_account').append(
                                        $("<option></option>")
                                            .attr("value", acc.id_scheme_account)
                                            .text(acc.scheme_acc_number)
                                    );
                                });
                                $(".eligible_walletamt").css("display", "none");
                                if (data.wallet_balance) {
                                    console.log(data.wallet_balance);
                                    $('.wallet_balance').val(parseFloat(data.wallet_balance.wal_balance));
                                    $('.redeem_percent').val(parseFloat(data.wallet_balance.redeem_percent));
                                    if ($('.wallet_balance').val() != '0') {
                                        $(".eligible_walletamt").css("display", "block");
                                    }
                                    // $('.wallet').val(parseFloat(data.wallet_balance.wal_balance));
                                }
                                $("#scheme_account").select2({
                                    placeholder: "Select scheme account",
                                    allowClear: true
                                });
                                $("#scheme_account").select2("val", ($('#id_scheme_account').val() != null ? $('#id_scheme_account').val() : ''));
                            }
                            //disable spinner
                            $('.overlay').css('display', 'none');
                        },
                        error: function (error) {
                            console.log(error);
                            //disable spinner
                            $('.overlay').css('display', 'none');
                        }
                    });
                } else {
                    $("#scheme_account").select2("val", '');
                    $('#scheme_account').empty();
                    $('#scheme-detail-box').addClass('box-default');
                    $('#mobile_number').val('');
                    $('#id_customer').val('');
                    $('#id_scheme_account').val('');
                    alert('Invalid Details');
                }
            },
            response: function (e, i) {
                // ui.content is the array that's about to be sent to the response callback.
                if (i.content.length === 0) {
                    alert('Please Enter a valid Number');
                    $('#mobile_number').val('');
                }
            },
            focus: function (e, i) {
                e.preventDefault();
                $("#mobile_number").val(i.item.label);
            }
        });
    }
    $("#mobile_number").on('keyup', function (event) {
        if ($("#mobile_number").val().length == 0) {
            $("#scheme_account").select2("val", '');
            $('#scheme_account').empty();
            $('#scheme-detail-box').addClass('box-default');
            $('#mobile_number').val('');
            $('#id_customer').val('');
            $('#id_scheme_account').val('');
        }
    });
    $('#resendotp').on('click', function () {
        var id_customer = $("#id_customer").val();
        $.ajax({
            url: base_url + "index.php/admin_payment/resend_otp?nocache=" + my_Date.getUTCSeconds(),
            data: { 'id_customer': id_customer },
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if (data.result == 3) {
                    alert(data.msg);
                    clearInterval(timer); // Clear the existing timer
                    timeLeft = 5; // Reset the time left
                    let hours = Math.floor(timeLeft / 3600);
                    let minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;
                    hours = hours < 10 ? '0' + hours : hours;
                    minutes = minutes < 10 ? '0' + minutes : minutes; // Format minutes
                    seconds = seconds < 10 ? '0' + seconds : seconds; // Format seconds
                    document.getElementById("timer").innerText = "Your OTP will expire within " + hours + "h:" + minutes + "m:" + seconds + "s";
                    startTimer();
                }
            }
        });
    });
    $('#verify_otp').on('click', function () {
        $("#verify_otp").attr("disabled", true);
        var post_data = $('#pay_form').serialize();
        update_otp(post_data);
    });
    $('#pay_table').DataTable({
        "oLanguage": { sLengthMenu: "Show Entries: _MENU_" },
        "order": [
            [0, 'desc']
        ],
        fixedColumns: true
    });
    if (ctrl_page[1] == 'edit' && $('#pay_status').val() != 1) {
        //$('#payment_status').prop("disabled", true);
    }
    get_bank_details(); //esakki
    $('#payment_list1').empty();
    $('#payment_list2').empty();
    $('#payment_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
    $('#payment_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
    // esakki
    $('#payment_date_range').html(moment().subtract(6, 'days').format('DD-MM-YYYY') + ' - ' + moment().format('DD-MM-YYYY'));
    $('#payment-dt-btn').daterangepicker({
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
            // esakki
            $('#payment_date_range').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
            var id_branch = $('#id_branch').val();
            var id_employee = $('#id_employee').val();
            var id_status = $('#id_status').text();
            var id_customer = $('#customer_id').val();
            get_payment_list(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), id_branch, id_employee, id_status, id_customer)
            $('#payment_list1').text(start.format('YYYY-MM-DD'));
            $('#payment_list2').text(end.format('YYYY-MM-DD'));
        }
    );
    if (path.route == 'payment/list') {
        get_employee_list();
        $('body').addClass("sidebar-collapse");
        var date = new Date();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 6, 1);
        var from_date = firstDay.getFullYear() + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();
        var to_date = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
        //var to_date=(date.getFullYear()+"-"+(date.getMonth())+"-"+(date.getDate()));
        var id_branch = $('#id_branch').val();
        get_payment_list(from_date, to_date, id_branch);
    } else {
        $(".redeem_request").keyup(function () {
            var totamt = (parseFloat($('#total_amt').val()) > 0 ? parseFloat($('#total_amt').val()) : 0);
            var wallet_balance = parseFloat($('.wallet_balance').val());
            var entered_val = parseFloat($('.redeem_request').val());
            $("#wallet_msg").text('Your wallet amount : Rs.' + wallet_balance + ',Chit amount : Rs.' + totamt);
            if (entered_val > 0 && entered_val != '') {
                if ((entered_val > totamt && totamt > 0) || entered_val > wallet_balance) {
                    if (totamt > 0 && totamt > wallet_balance) {
                        $(".redeem_request").val(wallet_balance);
                    } else if (totamt > 0) {
                        $(".redeem_request").val(totamt);
                    }
                    /* else {
                        $(".redeem_request").val(wallet_balance);
                    } */
                }
            } else {
                $(".redeem_request").val('');
            }
            calc_walletbalance(); // esakki 31-07-2025
            /* if ((parseFloat($(".wallet").val()) < parseFloat($(".redeem_request").val()) || parseFloat($(".redeem_request").val()) < 0)) {
                $(".redeem_request").val($(".wallet").val());
            } */
        });
        $(".ischk_wallet_pay").on("click", function (ev) {
            // Set total amount and wallet amount 
            calc_walletbalance(); // esakki 31-07-2025
        })
        $(document).on('click', '#select_payrow', function () {
            $('#tableRow .select_payrow').prop('checked', $(this).prop('checked'));
            //console.log(get_selected_tablerows('tableRow'));
        });
        //load_customer_select();
        load_schemeno_select();
        //  $('#pay_date').datepicker("setDate", new Date());
        /* if ($("#enable_editing").is(":checked")) {
          var content =
            '<div class="input-group date"><input type="text" class="form-control input-sm date" name="generic[date_payment]"   data-date-end-date="0d" id="pay_datetimepicker"  data-date-format="dd-mm-yyyy" /> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span> </div>';
          $("#date_payment_block").empty();
          $("#date_payment_block").append(content);
        } else { */
        // metal rate date edit - esakki 21-07-2025
        var d = new Date();
        var date =
            d.getFullYear() +
            "-" +
            (d.getMonth() + 1) +
            "-" +
            d.getDate() +
            " " +
            d.getHours() +
            ":" +
            d.getMinutes() +
            ":" +
            d.getSeconds();
        var content =
            '<input type="text" class="form-control" readonly name="generic[date_payment]" tabindex="-1" value="' +
            date +
            '" />';
        $("#date_payment_block").empty();
        $("#date_payment_block").append(content);
        if ($("#enable_editing").is(":checked")) {
            $('#make_pay_cash').val('');
            $('#make_pay_cash').prop('disabled', true);
            $('#rate_edit_enable').val(1);
            $('#metal_rate').val('');
            $('.current_date_rate').css('display', 'none');
            $('.editing_date_rate').css('display', 'block');
            var edit_content =
                '<div class="input-group date"><input type="text" class="form-control input-sm" name="generic[edit_metalrate_date]"   data-date-end-date="0d" id="edit_metalrate_date"  data-date-format="dd-mm-yyyy" /> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span> </div>';
            $("#metalrate_date_payment").empty();
            $("#metalrate_date_payment").append(edit_content);
            $("#select_metal_rate").empty();
            $("#select_metal_rate").select2({
                placeholder: "Select metal rate",
                allowClear: true,
            });
            $('#edit_metalrate_date').prop('required', true);
            $('#select_metal_rate').prop('required', true);
            calculatePaymentCost();
        } else {
            $('#make_pay_cash').prop('disabled', false);
            $('#rate_edit_enable').val(0);
            $('.current_date_rate').css('display', 'block');
            $('.editing_date_rate').css('display', 'none');
            $('#edit_metalrate_date').prop('required', false);
            $('#select_metal_rate').prop('required', false);
            calculatePaymentCost();
            // metalrate_find(date);
        }
        // }
        if ($("#edit_custom_entry_date").val() == 0) {
            var d = new Date();
            var date = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
            var entry_date = '<div class="input-group date"><input type="text" readonly class="form-control" name="generic[entry_date]"   value=' + date + '    </div>';
            $('#entry_date_payment_block').empty();
            $('#entry_date_payment_block').append(entry_date);
        } else {
            var d = new Date();
            var date = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
            var entry_date = '<div class="input-group date"><input type="text" class="form-control input-sm date" name="generic[entry_date]" value=' + date + '  data-date-end-date="0d" id="entry_date"  data-date-format="dd-mm-yyyy" /> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span> </div>';
            $('#entry_date_payment_block').empty();
            $('#entry_date_payment_block').append(entry_date);
        }
        $('body').on('focus', "#pay_datetimepicker", function () {
            $('#pay_datetimepicker').attr("readonly", true);
            $('#pay_datetimepicker').datetimepicker({
                format: 'yyyy-mm-dd hh:ii:ss',
                timezone: 'GMT'
            });
        });
        // metal rate date edit - esakki 21-07-2025
        $("body").on("focus", "#edit_metalrate_date", function () {
            $("#edit_metalrate_date").attr("readonly", true);
            var monthsBack = parseInt($("#metal_rate_datelimit").val()) || 0;
            var today = new Date();
            var startDate = new Date(today.getFullYear(), today.getMonth() - monthsBack, today.getDate());
            var endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1); // yesterday
            $("#edit_metalrate_date").datepicker({
                format: "yyyy-mm-dd",
                // endDate: '-1d', // Restricts to dates before today
                endDate: endDate,
                startDate: startDate,
                timezone: "GMT",
                beforeShowDay: function (date) {
                    if (date >= startDate && date <= endDate) {
                        return {
                            enabled: true,
                            classes: 'highlight-enabled' // Custom class
                        };
                    }
                    return false; // disables the date
                }
            });
        });
        $('body').on('focus', "#edit_custom_entry_date", function () {
            $('#edit_custom_entry_date').attr("readonly", true);
            $('#edit_custom_entry_date').datetimepicker({
                format: 'yyyy-mm-dd hh:ii:ss',
                timezone: 'GMT'
            });
        });
        $("#expiry").inputmask("mm/yyyy", { "placeholder": "mm/yyyy" });
        $("#weight").on('keyup', function () {
            calculate_total();
        });
    }
    // metal rate date edit - esakki 21-07-2025
    $('body').on('changeDate', "#pay_datetimepicker", function () {
        my_Date = new Date();
        var date_pay = format_date($('#pay_datetimepicker').val());
        var sch_join_date = $('#start_date').html().split("-").reverse().join("-");
        var current_date = format_date(my_Date);
        var date_pay_str = new Date(date_pay);
        var current_date_str = new Date(current_date);
        var sch_join_date_str = new Date(sch_join_date);
        if (date_pay_str < sch_join_date_str) {
            $.toaster({
                priority: "danger",
                title: "Warning!",
                message: "" + "</br>Payment Date cannot be before than scheme joined date...",
            });
            $("#pay_datetimepicker").val("");
        } else if (date_pay_str > current_date_str) {
            $.toaster({
                priority: "danger",
                title: "Warning!",
                message: "" + "</br>Payment Date cannot exceed current date...",
            });
            $("#pay_datetimepicker").val("");
        }
        $("div.overlay").css("display", "block");
        metalrate_find(date_pay);
    });
    $("body").on("changeDate", "#edit_metalrate_date", function () {
        my_Date = new Date();
        var edit_metal_date = $("#edit_metalrate_date").val();
        $("div.overlay").css("display", "block");
        metalrate_find(edit_metal_date);
    });
    //selected weights   
    $('#btn-payment').on('click', function () {
        $("div.overlay").css("display", "block");
    });
    $('#is_preclose').on('change', function () {
        if ($('#scheme_type').text() != 'Amount' && $('#scheme_type').text() != 'Amount to Weight') {
            $("input[name=weight_gold]").attr('checked', false);
            $('#selected_weight').val(" ");
            $('#total_amt').val(" ");
            $('#payment_amt').val(" ");
            $('#gst_amt').val(" ");
            $('#sel_wt').text("0.000");
        }
        if ($(this).is(':checked')) {
            //console.log($('#due_type').val());
            $('#due_type').val('PC');
            $('#allowed_dues').val($('#preclose').text());
            $('#allowed_dues').prop('readonly', true);
            $('#btn-submit').css('display', 'block');
            $("div.overlay").css("display", "block");
        } else {
            $('#due_type').val($('#act_due_type').val());
            $('#allowed_dues').val($('#act_allowed_dues').val());
            if ($('#act_allowed_dues').val() > 0) {
                $('#allowed_dues').prop('readonly', true);
            } else {
                $('#allowed_dues').prop('readonly', false);
            }
        }
        var amt = parseFloat($('#payamt').val());
        //GST Calculation
        var gst_val = 0;
        var gst_amt = 0;
        var gst = 0;
        if (parseFloat($('#gst_percent').val()) > 0) {
            gst_val = parseFloat(amt) - (parseFloat(amt) * (100 / (100 + parseFloat($('#gst_percent').val()))));
            gst_amt = gst_val * parseFloat($('#allowed_dues').val());
            if (parseFloat($('#gst_type').val()) == 1) {
                gst = gst_amt;
            }
        }
        total = parseFloat(parseFloat(amt) * parseFloat($('#allowed_dues').val())).toFixed(2);
        if ($('#is_flexible_wgt').val() == 0 && $('#scheme_type').text() == 'Weight') {
            if (parseFloat($('#selected_weight').val()) > 0) {
                $('#total_amt').val(total);
                $('#gst_amt').val((gst_amt).toFixed(0));
            }
        } else {
            $('#total_amt').val(total);
            $('#gst_amt').val((gst_amt).toFixed(0));
        }
        // wallet calculation
        /* var can_redeem = 0;
        if ($(".ischk_wallet_pay").is(":checked") && parseFloat($(".wallet_balance").val()) > 0) {
            var allowed_redeem = ($('.redeem_percent').val() > 0 ? (total * (parseFloat($('.redeem_percent').val()) / 100)) : 0);
            wallet_balance = parseFloat($('.wallet_balance').val());
            if (allowed_redeem > wallet_balance) {
                can_redeem = wallet_balance;
            } else {
                can_redeem = allowed_redeem;
            }
        }
        $('.wallet').val(can_redeem);
        $('.redeem_request').val(can_redeem); */
        calc_walletbalance(); // esakki 31-07-2025
        $('#payment_amt').val((parseFloat(gst) + parseFloat(total)).toFixed(0));
        if ($('#scheme_type').text() == 'Amount to Weight') {
            var total_amt = (parseFloat($('#gst_type').val()) == 1 ? parseFloat($('#total_amt').val()) : parseFloat($('#total_amt').val()) - parseFloat(gst_amt));
            var metal_rate = parseFloat($('#metal_rate').val());
            if (total_amt != '' && metal_rate != '') {
                var weight = total_amt / metal_rate;
                // $("#amttowgt").html(parseFloat(weight.toFixed(3)) + ' ' + '<strong>' + 'gm' + '</strong>');
                $("#amttowgt").html(formatMetalWeight(weight) + ' ' + '<strong>' + 'gm' + '</strong>');
            }
        }
        $("div.overlay").css("display", "none");
    });
    // metal rate date edit - esakki 21-07-2025
    $("#enable_editing").on("change", function () {
        /* if ($("#enable_editing").is(":checked")) {
          var content =
            '<div class="input-group date"><input type="text" class="form-control input-sm date" name="generic[date_payment]"   data-date-end-date="0d" id="pay_datetimepicker"  data-date-format="dd-mm-yyyy" /> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span> </div>';
          $("#date_payment_block").empty();
          $("#date_payment_block").append(content);
        } else { */
        var d = new Date();
        var date = format_date(d);
        // var date = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        console.log(date); // Example: "2025-05-07 13:15:48"
        var content =
            '<input type="text" class="form-control" readonly name="generic[date_payment]" value="' +
            date +
            '" />';
        $("#date_payment_block").empty();
        $("#date_payment_block").append(content);
        if ($("#enable_editing").is(":checked")) {
            $('#rate_edit_enable').val(1);
            $('#make_pay_cash').val('');
            $('#make_pay_cash').prop('disabled', true);
            $('#metal_rate').val('');
            $('.current_date_rate').css('display', 'none');
            $('.editing_date_rate').css('display', 'block');
            var edit_content =
                '<div class="input-group date"><input type="text" class="form-control input-sm" name="generic[edit_metalrate_date]"   data-date-end-date="0d" id="edit_metalrate_date"  data-date-format="dd-mm-yyyy" /> <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span> </div>';
            $("#metalrate_date_payment").empty();
            $("#metalrate_date_payment").append(edit_content);
            $("#select_metal_rate").empty();
            $("#select_metal_rate").select2({
                placeholder: "Select metal rate",
                allowClear: true,
            });
            $('#edit_metalrate_date').prop('required', true);
            $('#select_metal_rate').prop('required', true);
            calculatePaymentCost();
        } else {
            $('#make_pay_cash').prop('disabled', false);
            $('#rate_edit_enable').val(0);
            $('.current_date_rate').css('display', 'block');
            $('.editing_date_rate').css('display', 'none');
            $('#edit_metalrate_date').prop('required', false);
            $('#select_metal_rate').prop('required', false);
            metalrate_find(date);
            calculatePaymentCost();
        }
        /*   }*/
    });
    $("#revert_approval").click(function () {
        if ($("input[name='pay_id[]']:checked").val()) {
            var selected = [];
            $("#payment_list tbody tr").each(function (index, value) {
                $("input[name='pay_id[]']:checked").each(function () {
                    if ($(value).find("input[name='pay_id[]']:checked").is(":checked")) {
                        clientid = $(value).find(".clientid").val();
                        id_branch = $(value).find(".id_branch").val();
                        id_payment = $(this).val();
                        console.log(id_branch);
                        console.log(clientid);
                        console.log(id_payment);
                        selected.push({ 'id_payment': id_payment, 'id_branch': id_branch, 'clientid': clientid });
                    }
                });
                payData = selected;
            })
            revert_approved(payData);
        }
    });
});
//esakki start
function get_bank_details() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_billing/get_bank_acc_details',
        dataType: 'json',
        success: function (data) {
            bank_details = data;
        }
    });
}
//esakki end
function revert_approved(payData = "") {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/admin_payment/revertApproval_jil?nocache=" + my_Date.getUTCSeconds(),
        data: { 'payData': payData },
        type: "POST",
        async: false,
        success: function (data) {
            $("div.overlay").css("display", "none");
            location.reload(true);
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function get_payment_list(from_date = "", to_date = "", id_branch = "", id_employee = "", id_status = "", id_customer = "") {
    my_Date = new Date();
    var type = $('#date_Select').find(":selected").val();
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/payment/ajax_list?nocache=" + my_Date.getUTCSeconds(),
        data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'id_employee': id_employee, 'id_status': id_status, 'id_customer': id_customer, 'date_type': type } : ''),
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            $('#total_payments').text(data.data.length);
            console.log(data.dat);
            set_payment_list(data);
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function set_payment_list(data) {
    var payment = data.data;
    var access = data.access;
    var profile = data.profile;
    var oTable = $('#payment_list').DataTable();
    if (access.add == '0') {
        $('#add_post_payment').attr('disabled', 'disabled');
    }
    oTable.clear().draw();
    if (payment != null && payment.length > 0) {
        var receipt_no_set = (typeof data.data == 'undefined' ? '' : data.data[0].receipt_no_set);
        var entry_date = data.data[0].edit_custom_entry_date;
        if (receipt_no_set == 1 || receipt_no_set == 2) {
            var pay_listDate1 = $('#payment_list1').text();
            var pay_listDate2 = $('#payment_list2').text();
            // Convert date strings to Date objects
            var date1 = new Date(pay_listDate1);
            var date2 = new Date(pay_listDate2);
            var branch_name = getBranchTitle();
            var title = '';
            title += get_title(formatDate(date1), formatDate(date2), 'Scheme Payment List - ' + branch_name);
            let column = [{
                "mDataProp": function (row, type, val, meta) {
                    if (row.id_status == 1 && row.is_offline == 0) {
                        chekbox = ' <input type="checkbox" class="pay_status" name="pay_id[]" value="' + row.id_payment + '"  /> <input class="id_branch" type="hidden" name="id_branch" value="' + row.id_branch + '" /><input type="hidden" class="clientid" name="clientid" value="' + row.ref_no + '" />'
                        return chekbox + " " + row.id_payment;
                    } else {
                        return row.id_payment;
                    }
                }
            },
            /*               { "mDataProp": function ( row, type, val, meta ){
                              if(row.edit_custom_entry_date==0){
                              return row.date_payment;
                              }
                              else{
                              return row.entry_Date;
                              }
                          }},   */
            { "mDataProp": "date_payment" },
            { "mDataProp": "entry_Date" },
            { "mDataProp": "name" },
            { "mDataProp": "account_name" },
            { "mDataProp": "code" },
            { "mDataProp": "scheme_group_code" },
            /*     { "mDataProp": function ( row, type, val, meta ){
                     if(row.scheme_wise_acc_no==3)
                     {
                         return row.branch_code+row.code+'-'+row.scheme_acc_number;
                     }
                     else
                     {
                         return row.scheme_acc_number;
                     }
                 }},*/
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
                  }},   */
            { "mDataProp": "scheme_acc_number" },
            { "mDataProp": "mobile" },
            //{ "mDataProp": "paid_installments" },
            {
                "mDataProp": function (row, type, val, meta) {
                    if (row.show_ins_type == 1) {
                        return row.paid_installments + "/" + row.total_installments;
                    } else {
                        return row.paid_installments;
                    }
                }
            },
            { "mDataProp": "installment" },
            { "mDataProp": "payment_type" },
            { "mDataProp": "payment_mode" },
            { "mDataProp": "id_transaction" },
            // { mDataProp: "metal_rate" },
            {
                "mDataProp": function (row, type, val, meta) {
                    if (row.is_editing_enabled == 1 && row.metalrate_edit_date) {
                        return '<a  data-toggle="tooltip" title="' + row.metalrate_edit_date + '">' + row.metal_rate + '</a>';
                    } else {
                        return row.metal_rate;
                    }
                }
            },
            { "mDataProp": function (row, type, val, meta) {
                    if (row.metal_weight != "") {
                        return formatMetalWeight(row.metal_weight);
                    } else {
                        return "-";
                    }
                } },
            {
                "mDataProp": function (row, type, val, meta) {
                    return (row.one_time_premium == 1 && row.fixed_wgt != '' && row.fixed_wgt != null ? row.fixed_wgt : '-');
                }
            },
            {
                "mDataProp": function (row, type, val, meta) {
                    return (row.payment_type == 'Payu Checkout' && row.id_status != 1 && (row.due_type == 'A' || row.due_type == 'P') ? row.act_amount : row.payment_amount);
                }
            },
            { "mDataProp": function (row, type, val, meta) {
                if (row.saved_benefits != "") {
                    return formatMetalWeight(row.saved_benefits);
                } else {
                    return "-";
                }
            } },
            { "mDataProp": "saved_benefit_amt" },
             { "mDataProp": function (row, type, val, meta) {
                if (row.dg_other_benefit_wgt != "") {
                    return formatMetalWeight(row.dg_other_benefit_wgt);
                } else {
                    return "-";
                }
            } },
{
                    "mDataProp": function (row, type, val, meta) {
                        if (row.payment_ref_details && row.payment_ref_details !== '-') {
                            var details = row.payment_ref_details.split('||');
                            var html = '<div>';
                            html += '<button class="btn btn-xs btn-link" type="button" onclick="$(this).next().toggle()"><i class="fa fa-plus-circle"></i> View Details</button>';
                            html += '<div style="display:none; margin-top:5px; background:#f9f9f9; padding:5px; border:1px solid #ddd; border-radius:4px; font-size:11px;">';
                            html += '<table class="table table-condensed" style="margin-bottom:0px;">';
                            html += '<thead><tr><th>Mode</th><th>Amt</th><th>Ref</th><th>Date</th></tr></thead>';
                            html += '<tbody>';
                            details.forEach(function (d) {
                                var parts = d.split('//$$//');
                                html += '<tr>';
                                html += '<td>' + (parts[0] || '-') + '</td>';
                                html += '<td>' + (parts[1] || '-') + '</td>';
                                html += '<td>' + (parts[2] || '-') + '</td>';
                                html += '<td>' + (parts[3] || '-') + '</td>';
                                html += '</tr>';
                            });
                            html += '</tbody></table></div></div>';
                            return html;
                        }
                        return row.payment_ref_number || '-';
                    }
                },            {
                "mDataProp": function (row, type, val, meta) { return "<span class='label bg-" + row.status_color + "-active'>" + row.payment_status + "</span>"; }
            },
            {
                "mDataProp": function (row, type, val, meta) {
                    id = row.id_payment;
                    id_scheme_account = row.id_scheme_account; // Get Payment page Print chked//hh
                    edit_url = (access.edit == '1' ? base_url + 'index.php/payment/edit/' + id : '#');
                    status_url = base_url + 'index.php/payment/status/' + id + '/' + id_scheme_account;
                    detail_url = base_url + 'index.php/online/get/ajax_payment/' + id;
                    printbtn_normalrecpt = '';
                    delbtn = '';
                    printbtn_thermalrecpt = '';
                    existing_print = '';
                    print_passbook_url = '';
                    print_passbook_btn = '';
                    if (row.id_status == '1') {
                        //print_normalurl=(access.edit=='1' ? base_url+'index.php/payment/invoice/'+id+'/'+id_scheme_account : '#' );
                        print_normalurl = base_url + 'index.php/payment/invoice/' + id + '/' + id_scheme_account;
                        printbtn_normalrecpt = '<li><a href="' + print_normalurl + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print</a></li>';
                        print_thermalurl = base_url + 'index.php/payment/thermal_invoice/' + id + '/' + 'Payment';
                        passbook_url = base_url + 'index.php/admin_payment/old_passbook/' + id + '/' + id_scheme_account;
                        // 09-12-2022 clinton print pass book back start
                        id = btoa(id);
                        print_passbook_url = base_url + 'index.php/admin_manage/passbook_print/PAY/' + id_scheme_account + '/' + id;
                        print_passbook_btn = '<li><a href="' + print_passbook_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print Passbook</a></li>';
                        // 09-12-2022 clinton print pass book back start
                        printbtn_thermalrecpt = '<li><a href="' + print_thermalurl + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i>Rc Print</a></li>';
                        existing_print = '<li><a href="' + passbook_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i>Existing Rc</a></li>';
                    } else {
                        //print_passbook_url=base_url+'index.php/admin_manage/passbook_print/B/'+id_scheme_account;
                        //print_passbook_btn='<li><a href="'+print_passbook_url+'" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print Passbook</a></li>';
                        delete_url = (access.delete == '1' ? base_url + 'index.php/payment/delete/' + row.id_payment : '#');
                        delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                        delbtn = '<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li>'
                    }
                    // var edit_pay =  (access.edit == 1  ? '<li><a href="#" class="btn-edit" onclick="edit_2('+id+')"><i class="fa fa-edit" ></i> Edit</a></li>' : '');
                    // esakki 18-09
                    var edit_pay =
                        access.edit == 1 ?
                            (row.added_by == 0 || row.added_by == 3) &&
                                row.id_payGateway == null &&
                                row.active == 1 &&
                                row.is_closed == 0 ?
                                (row.payment_mode != "FP" ? '<li><a href="#" class="btn-edit" onclick="edit_2(' +
                                    row.id_payment +
                                    ')"><i class="fa fa-edit" ></i> Edit</a></li>' : "") :
                                "" :
                            "";
                    action_content =
                        '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                        edit_pay +
                        '<li><a href="#" class="btn-edit" onClick="transaction_detail(' +
                        row.id_payment +
                        ')"><i class="fa fa-eye" ></i> Detail</a></li>' +
                        delbtn +
                        '<li><a href="' +
                        status_url +
                        '" class="btn-edit"><i class="fa fa-search-plus" ></i> Status</a></li>' +
                        printbtn_normalrecpt +
                        " " +
                        printbtn_thermalrecpt +
                        " " +
                        existing_print +
                        " " +
                        print_passbook_btn +
                        "</ul></div>";
                    return action_content;
                }
            },
            // { "mDataProp": "receipt_no" },
            /*	{ "mDataProp": function ( row, type, val, meta ){
                            if(row.receiptNo_displayFrmt == 0){   //only acc num
                                return row.receipt_no;
                            }else if(row.receiptNo_displayFrmt == 1){ //based on acc number generation setting
                                if(row.scheme_wise_receipt==1){
                                    return row.receipt_no;
                                }else if(row.scheme_wise_receipt==2){
                                    return row.acc_branch+'-'+row.receipt_no;
                                }else if(row.scheme_wise_receipt==3){
                                    return row.code+'-'+row.receipt_no;
                                }else if(row.scheme_wise_receipt==4){
                                    return row.code+''+row.acc_branch+'-'+row.receipt_no;
                                }else if(row.scheme_wise_receipt==5){
                                    return row.start_year+'-'+row.receipt_no;
                                }else if(row.scheme_wise_receipt==6){
                                    return row.start_year+''+row.code+''+row.acc_branch+'-'+row.receipt_no;
                                }
                            }else if(row.receiptNo_displayFrmt == 2){  //customised
                                return row.receipt_no;
                            }
                    }},  */
            { "mDataProp": "receipt_no" },
            {
                "mDataProp": function (row, type, val, meta) {
                    var remark = "'" + row.remark + "'";
                    var edit_pay = (access.edit == 1 ? '<span>' + row.remark + '<a href="#" class="btn-edit" onclick="edit_3(' + id + ',' + remark + ')"><i class="fa fa-edit" ></i></a></span>' : '');
                    return edit_pay;
                }
            },
            {
                "mDataProp": function (row, type, val, meta) {
                    if (row.employee == null) {
                        return '-';
                    } else {
                        return row.emp_code + ' - ' + row.employee;
                    }
                }
            },
            {
                "mDataProp": function (row, type, val, meta) {
                    if (row.agent == null) {
                        return '-';
                    }
                    else {
                        return row.agent_code + ' - ' + row.agent;
                    }
                }
            },
            { "mDataProp": "payment_branch" },
            {
                "mDataProp": function (row, type, val, meta) {
                    return (row.added_by == '1' ? "Web" : (row.added_by == '0' ? "Admin" : (row.added_by == '2' ? "Mobile" : (row.added_by == '3' ? "Collection App" : (row.added_by == '4' ? "Retail" : (row.added_by == '5' ? "Sync" : (row.added_by == '6' ? "Import" : "-")))))));
                }
            }
            ];
            if ($("#old_sch_acc_num").val() == "1") {
                column.splice(3, 0, {
                    mDataProp: "old_scheme_acc_number",
                    visible: true
                });
            }
            oTable = $('#payment_list').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": true,
                "dom": 'lBfrtip',
                "buttons": ['excel', {
                    extend: 'print',
                    title: '',
                    customize: function (win) {
                        $(win.document.body)
                            .prepend(title);
                        $(win.document.body).find('table')
                            .addClass('compact');
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', '10px')
                            .css('font-family', 'sans-serif');
                        $(win.document.body).find('tr:nth-child(odd) td').each(function (index) {
                            $(this).css('font-weight', 'bold');
                        });
                    },
                    exportOptions: { columns: ':visible' },
                },],
                "columnDefs": [
                    //   {
                    // 	  targets: [0,1,2,3,4,5,6,7,9,13,14,15,16,17], 
                    // 	  className: 'dt-left'
                    //   },
                    {
                        targets: [10, 14, 15, 16, 17],
                        className: 'dt-right'
                    },
                    //   {"width": "120px", "targets": 1},
                ],
                // "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
                "aaData": payment,
                "order": [
                    [0, "desc"]
                ],
                "aoColumns": column,
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if (aData['payment_type'] == 'Payu Checkout') {
                        switch (aData['due_type']) {
                            case 'A':
                                if (aData['id_status'] == 2 || aData['id_status'] == 7)
                                    $(nRow).css('color', '#e71847');
                                break;
                            case 'P':
                                if (aData['id_status'] == 2 || aData['id_status'] == 7)
                                    $(nRow).css('color', '#e71847');
                                break;
                        }
                    }
                }
            });
        } else {
            let column = [{
                "mDataProp": function (row, type, val, meta) {
                    if (row.scheme_acc_number != 'Not Allocated' && (row.receipt_no_set == '0' && (row.receipt_no == null || row.receipt_no == '') && row.id_status == '1')) {
                        return '<input type="checkbox" id="select_ids_' + row.id_payment + '" class="select_ids"  value="' + row.id_payment + '">';
                    } else {
                        return null;
                    }
                }
            },
            { "mDataProp": "id_payment" },
            /*        { "mDataProp": function ( row, type, val, meta ){
                    if(row.edit_custom_entry_date==0){
                    return row.date_payment;
                    }
                    else{
                    return row.entry_Date;
                    }
                }},   */
            { "mDataProp": "date_payment" },
            { "mDataProp": "entry_Date" },
            /* {
                "mDataProp": "old_scheme_acc_number",
                "visible": ($('#old_sch_acc_num').val() == 1)
            }, */
            { "mDataProp": "name" },
            { "mDataProp": "account_name" },
            {
                "mDataProp": function (row, type, val, meta) {
                    return row.code;
                }
            },
            { "mDataProp": "scheme_group_code" },
            /*  { "mDataProp": function ( row, type, val, meta ){
            if(row.scheme_wise_acc_no==3)
                {
                    return row.branch_code+row.code+'-'+row.scheme_acc_number;
                }
                else
                {
                    return row.code+' '+row.scheme_acc_number;
                }
            }},*/
            { "mDataProp": "scheme_acc_number" },
            { "mDataProp": "mobile" },
            { "mDataProp": "paid_installments" },
            { "mDataProp": "installment" },
            { "mDataProp": "payment_type" },
            { "mDataProp": "payment_mode" },
            { "mDataProp": "id_transaction" },
            // { mDataProp: "metal_rate" },
            {
                mDataProp: function (row, type, val, meta) {
                    if (row.is_editing_enabled == 1 && row.metalrate_edit_date) {
                        return '<a  data-toggle="tooltip" title="' + row.metalrate_edit_date + '">' + row.metal_rate + '</a>';
                        // return '<span title="' + row.metalrate_edit_date + '">' + row.metal_rate + '</span>';
                    } else {
                        return row.metal_rate;
                    }
                }
            },
             { "mDataProp": function (row, type, val, meta) {
                        if (row.metal_weight != "") {
                            return formatMetalWeight(row.metal_weight);
                        } else {
                            return "-";
                        }
                    } },
            {
                "mDataProp": function (row, type, val, meta) {
                    return (row.payment_type == 'Payu Checkout' && row.id_status != 1 && (row.due_type == 'A' || row.due_type == 'P') ? row.act_amount : row.payment_amount);
                }
            },
            { "mDataProp": function (row, type, val, meta) {
                        if (row.saved_benefits != "") {
                            return formatMetalWeight(row.saved_benefits);
                        } else {
                            return "-";
                        }
                    } },
                    { "mDataProp": "saved_benefit_amt" },
            { "mDataProp": "payment_ref_number" },
            /*	{ "mDataProp": function ( row, type, val, meta ){
                            if(row.scheme_acc_number!='Not Allocated' && (row.receipt_no_set=='0' && (row.receipt_no==null||row.receipt_no=='') && row.id_status=='1')){	
                               return '<input  type="text"  id="receipt_no" class="receiptno"  disabled="true" value="">';}
                            else{
                                if(receipt_no_set == 1){
                                    return row.receipt_year+' - ' +row.receipt_no; 
                                }else{
                                    return row.receipt_no; 
                                }
                            } 
                      }
                                },*/
            //commented and replaced by Durga 22.05.2023
            {
                "mDataProp": function (row, type, val, meta) {
                    if (row.scheme_acc_number != 'Not Allocated' && (row.receipt_no_set == '0' && (row.receipt_no == null || row.receipt_no == '') && row.id_status == '1')) {
                        return '<input  type="text"  id="receipt_no" class="receiptno"  disabled="true" value="">';
                    } else {
                        return row.receipt_no;
                    }
                }
            },
            {
                "mDataProp": function (row, type, val, meta) { return "<span class='label bg-" + row.status_color + "-active'>" + row.payment_status + "</span>"; }
            },
            {
                "mDataProp": function (row, type, val, meta) {
                    id = row.id_payment;
                    is_print_taken = row.is_print_taken;
                    id_scheme_account = row.id_scheme_account;
                    // console.log(id1);
                    edit_url = (access.edit == '1' ? base_url + 'index.php/payment/edit/' + id : '#');
                    status_url = base_url + 'index.php/payment/status/' + id + '/' + id_scheme_account;
                    detail_url = base_url + 'index.php/online/get/ajax_payment/' + id;
                    printbtn = '';
                    delbtn = '';
                    if (row.id_status == '1') {
                        //if(row.receipt=='0'){
                        //print_url=(access.edit=='1' ? base_url+'index.php/payment/invoice/'+id+'/'+id_scheme_account : '#' );
                        print_url = base_url + 'index.php/payment/invoice/' + id + '/' + id_scheme_account;
                        printbtn = '<li><a href="' + print_url + '" target="_blank" class="btn-print"><i class="fa fa-print" ></i> Print</a></li>';
                        /*}else{
                         printbtn='<li><a href="#" onclick="get_print_data('+id+')" class="custom_print"><i class="fa fa-print" ></i> Print</a></li>';
                        }*/
                    } else {
                        delete_url = (access.delete == '1' ? base_url + 'index.php/payment/delete/' + id : '#');
                        delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
                        delbtn = '<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li>'
                    }
                    //<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>
                    // esakki 18-09
                    var edit_pay = (access.edit == 1 ? ((row.added_by == 0 || row.added_by == 3) && row.id_payGateway == null && row.active == 1 && row.is_closed == 0 && row.payment_mode != "FP" ? '<li><a href="#" class="btn-edit" onclick="edit_2(' + id + ')"><i class="fa fa-edit" ></i> Edit</a></li>' : '') : '');
                    action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
                        edit_pay +
                        // (access.edit == 1  ? '<li><a href="#" class="btn-edit" onclick="edit_2('+id+')"><i class="fa fa-edit" ></i> Edit</a></li>' : '')
                        '<li><a href="#" class="btn-edit" onClick="transaction_detail(' + id + ')"><i class="fa fa-eye" ></i> Detail</a></li>' + delbtn +
                        '<li><a href="' + status_url + '"  class="btn-edit"><i class="fa fa-search-plus" ></i> Status</a></li>' + (is_print_taken == 0 || profile == 2 || profile == 1 ? printbtn : '') + '</ul></div>';
                    return action_content;
                }
            },
            { "mDataProp": "remark" },
            //   { "mDataProp": "employee" },
            {
                "mDataProp": function (row, type, val, meta) { return row.emp_code + '- ' + row.employee; }
            },
            {
                "mDataProp": function (row, type, val, meta) { return row.agent_code + '- ' + row.agent; }
            },
            { "mDataProp": "payment_branch" },
            {
                "mDataProp": function (row, type, val, meta) {
                    return (row.added_by == '1' ? "Web" : (row.added_by == '0' ? "Admin" : (row.added_by == '2' ? "Mobile" : (row.added_by == '3' ? "Collection App" : (row.added_by == '4' ? "Retail" : (row.added_by == '5' ? "Sync" : (row.added_by == '6' ? "Import" : "-")))))));
                }
            }
            ];
            if ($("#old_sch_acc_num").val() == "1") {
                column.splice(4, 0, {
                    mDataProp: "old_scheme_acc_number",
                    visible: true
                });
            }
            oTable = $('#payment_list').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "dom": 'lBfrtip',
                "buttons": ['excel'],
                "columnDefs": [{
                    targets: [10, 14, 15, 16, 17],
                    className: 'dt-right'
                },],
                "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
                "aaData": payment,
                "order": [
                    [1, "desc"]
                ],
                'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    "bSort": true,
                    'className': 'dt-body-center',
                }],
                "aoColumns": column,
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if (aData['payment_type'] == 'Payu Checkout') {
                        switch (aData['due_type']) {
                            case 'A':
                                if (aData['id_status'] == 2 || aData['id_status'] == 7)
                                    $(nRow).css('color', '#e71847');
                                break;
                            case 'P':
                                if (aData['id_status'] == 2 || aData['id_status'] == 7)
                                    $(nRow).css('color', '#e71847');
                                break;
                        }
                    }
                }
            });
        }
        // if (showExport == 0) {
        // 	$(".dt-buttons").css("display", "none");
        // }
    }
}
/*  Receipt number  manual enrty */
var selectdatas = [];
$(document).on('click', '#select_recpt', function (e) {
    if ($(this).prop("checked") == true) {
        $("tbody tr td input[type='checkbox']").prop('checked', true);
        $(".receiptno").attr('disabled', false);
    } else if ($(this).prop("checked") == false) {
        $(".receiptno").val('');
        $(".receiptno").attr('disabled', true);
        $("tbody tr td input[type='checkbox']").prop('checked', false);
    }
});
$(document).on('click', '.select_ids', function (e) {
    $("#payment_list tbody tr").each(function (index, value) {
        if (!$(value).find(".select_ids").is(":checked")) {
            $(value).find(".receiptno").empty();
            $(value).find(".receiptno").attr('disabled', true);
            $(value).find(".receiptno").val('');
        } else if ($(value).find(".select_ids").is(":checked")) {
            $(value).find(".receiptno").attr('disabled', false);
        }
    });
});
var selected = [];
$(document).on('click', '.conform_recpt', function (e) {
    $("#payment_list tbody tr").each(function (index, value) {
        if (!$(value).find(".select_ids").is(":checked")) {
            $(value).find(".receiptno").empty();
            $(value).find(".receiptno").attr('disabled', true);
        } else if (($(value).find(".select_ids").is(":checked") && $(value).find(".receiptno").val() != '')) {
            $("#conform_save").attr('disabled', true);
            $(value).find(".receiptno").attr('disabled', false);
            var id_payment = $(value).find(".select_ids").val();
            var scheme_acc_number = $(value).find(".receiptno").val();
            var data = { 'id_payment': id_payment, 'receipt_no': scheme_acc_number };
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
            url: base_url + "index.php/receipt_number/update",
            data: { 'selected': selected },
            dataType: "JSON",
            type: "POST",
            success: function (data) {
                $("div.overlay").css("display", "none");
                location.reload(true);
            },
            error: function (error) {
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
/*  Receipt number  manual enrty */
function load_customer_select() {
    my_Date = new Date();
    //show spinner
    $('.overlay').css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/customer/get_customers?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (data) {
            if ($('#customer').length > 0) {
                $.each(data, function (key, cus) {
                    $('#customer').append(
                        $("<option></option>")
                            .attr("value", cus.id)
                            .text(cus.mobile + " " + cus.name)
                    );
                });
                $("#customer").select2({
                    placeholder: "Enter Mobile Number",
                    allowClear: true
                });
                $("#customer").select2("val", ($('#id_customer').val() != null ? $('#id_customer').val() : ''));
            }
            //disable spinner
            $('.overlay').css('display', 'none');
        },
        error: function (error) {
            console.log(error);
            //disable spinner
            $('.overlay').css('display', 'none');
        }
    });
}
$('#branch_select').on('change', function (e) {
    if (this.value != '') {
        $("#id_branch").val(this.value);
        get_check_customer_payment_det();
        get_scheme_cash_total();
        $('#employee_select').trigger('change.select2');
        getBranchEmployee();
    } else {
        $("#id_branch").val('');
    }
});
$('#customer').select2().on("change", function (e) {
    //	alert("1");
    //console.log("change val=" + this.value);
    if (this.value != '') {
        $("#id_customer").val(this.value);
        my_Date = new Date();
        //load customer schemes
        //show spinner
        $('.overlay').css('display', 'block');
        $.ajax({
            type: 'GET',
            url: base_url + 'index.php/payment/get/ajax/customer/account/' + this.value + '?nocache=' + my_Date.getUTCSeconds(),
            dataType: 'json',
            cache: false,
            success: function (data) {
                if ($('#scheme_account').length > 0) {
                    $('#scheme_account').empty();
                    $.each(data.accounts, function (key, acc) {
                        $('#scheme_account').append(
                            $("<option></option>")
                                .attr("value", acc.id_scheme_account)
                                .text(acc.scheme_acc_number)
                        );
                    });
                    $(".eligible_walletamt").css("display", "none");
                    if (data.wallet_balance) {
                        console.log(data.wallet_balance);
                        $('.wallet_balance').val(parseFloat(data.wallet_balance.wal_balance));
                        $('.redeem_percent').val(parseFloat(data.wallet_balance.redeem_percent));
                        if ($('.wallet_balance').val() != '0') {
                            $(".eligible_walletamt").css("display", "block");
                        }
                        $('.wallet').val(parseFloat(data.wallet_balance.wal_balance));
                    }
                    $("#scheme_account").select2({
                        placeholder: "Select scheme account",
                        allowClear: true
                    });
                    $("#scheme_account").select2("val", ($('#id_scheme_account').val() != null ? $('#id_scheme_account').val() : ''));
                }
                //disable spinner
                $('.overlay').css('display', 'none');
            },
            error: function (error) {
                console.log(error);
                //disable spinner
                $('.overlay').css('display', 'none');
            }
        });
    } else {
        $("#scheme_account").select2("val", '');
        $('#scheme_account').empty();
    }
});
function load_schemeno_select(id_scheme = '') {
    my_Date = new Date();
    //show spinner
    $('.overlay').css('display', 'block');
    $.ajax({
        type: 'POST',
        data: { 'id_scheme': id_scheme },
        url: base_url + 'index.php/payment/get/ajax_data?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (data) {
            console.log($('#scheme_account option').length);
            if (ctrl_page[1] == 'edit') {
                $('#scheme_account').prop('disabled', true);
            }
            /*$.each(data.account, function (key, acc) {
                  $('#scheme_account').append(
                  $("<option></option>")
                    .attr("value", acc.id_scheme_account)
                    .text(acc.scheme_acc_number)
                  );
          });*/
            $("#scheme_account").select2({
                placeholder: "Select scheme account",
                allowClear: true
            });
            $("#scheme_account").select2("val", ($('#id_scheme_account').val() != null ? $('#id_scheme_account').val() : ''));
            if ($('#pay_mode').length) {
                $.each(data.mode, function (key, mode) {
                    if (mode.mode_name != 'ECS') {
                        $('#pay_mode').append(
                            $("<option></option>")
                                .attr("value", mode.short_code)
                                .text(mode.mode_name)
                        );
                    }
                });
                if (data.mode.length == 0) {
                    var payment_mode = '';
                } else {
                    var payment_mode = data.mode[0].short_code;
                }
                $("#pay_mode").select2({
                    placeholder: "Select payment mode",
                    allowClear: true
                });
                $("#pay_mode").select2("val", payment_mode);
            }
            //   if($('#payment_status').length)
            //   {
            //   	$.each(data.payment_status, function (key, pay) {
            //    	$('#payment_status').append(
            // $("<option></option>")
            //   .attr("value", pay.id_status_msg)
            //   .text(pay.payment_status)
            // );
            // });
            // if(data.payment_status.length == 0){
            // 	var payment_status = '';
            // }else{
            // 	var payment_status = data.payment_status[0].id_status_msg;	
            // }
            // $('#pay_status').val(payment_status); 
            // $("#payment_status").select2({
            //     placeholder: "Select payment status",
            //     allowClear: true
            // });
            // $("#payment_status").select2("val", ($('#pay_status').val()!=null?$('#pay_status').val():''));
            //   }
            $('#pay_status').val(1);
            if ($('#payee_bank').length) {
                $.each(data.bank, function (key, item) {
                    $('#payee_bank').append(
                        $("<option></option>")
                            .attr("value", item.id_bank)
                            .text(item.bank_name)
                    );
                });
                $("#payee_bank").select2({
                    placeholder: "Select payee bank",
                    allowClear: true
                });
                $("#payee_bank").select2("val", '');
            }
            if ($('#payment_status').length) {
                $.each(data.drawee, function (key, bank) {
                    $('#drawee_acc_no').append(
                        $("<option></option>")
                            .attr("value", bank.id_drawee)
                            .text(bank.account_no)
                    );
                });
                $("#drawee_acc_no").select2({
                    placeholder: "Select account number",
                    allowClear: true
                });
                $("#drawee_acc_no").select2("val", ($('#id_drawee_bank').val() != null ? $('#id_drawee_bank').val() : ''));
            }
            //get rate from api
            get_rate();
            //disable spinner
            $('.overlay').css('display', 'none');
        },
        error: function (error) {
            console.log(error);
            //disable spinner
            $('.overlay').css('display', 'none');
        }
    });
}
//on selecting drawee account
$('#drawee_acc_no').select2()
    .on("change", function (e) {
        //console.log("change val=" + this.value);
        if (this.value != '') {
            $("#id_drawee_bank").val(this.value);
            get_drawee_detail(this.value);
        }
    });
//     $('#payment_status').select2()
//         .on("change", function(e) {
//           //console.log("change val=" + this.value);
//           if(this.value!='')
//           {
//           	 $("#pay_status").val(this.value);
// 	  }
//    });  
$('#pay_mode').select2()
    .on("change", function (e) {
        //console.log("change val=" + this.value);
        if (this.value != '') {
            $("#payment_mode").val(this.value);
        }
    });
$('.weight').select2()
    .on("change", function (e) {
        console.log(1);
    });
//to get drawee detail
function get_drawee_detail(id) {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/settings/drawee/ajax_list/' + id + '?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (data) {
            $('#drawee_bank').val(data.data.bank_name);
            $('#drawee_bank_branch').val(data.data.branch);
            $('#drawee_ifsc').val(data.data.ifsc_code);
        }
    });
}
if (ctrl_page[1] == 'status') {
    $("#id_scheme_account").val();
    // load_account_detail($("#id_scheme_account").val());
    load_account(ctrl_page[2], ctrl_page[3]);
} else {
    $('.overlay').css('display', 'block');
    //get account detail on change
    $('#scheme_account').select2()
        .on("change", function (e) {
            //console.log("change val=" + this.value);
            if (this.value != '') {
                $("#id_scheme_account").val(this.value);
                load_account_detail(this.value);
            } else {
                clear_account_detail();
            }
        });
}
$('#pay_mode').select2()
    .on("change", function (e) {
        if (this.value == 'CHQ') {
            $('.cheque-container').css('display', 'block');
        } else {
            $('.cheque-container').css('display', 'none');
        }
    });
//get rate   
function get_rate() {
    my_Date = new Date();
    var baseURL = base_url.replace('admin/', '');
    $.ajax({
        type: "GET",
        url: baseURL + "api/rate.txt" + "?nocache=" + my_Date.getUTCSeconds(),
        dataType: "json",
        cache: false,
        success: function (data) {
            var currentRate = data.goldrate_22ct;
            $("#metal_rate").val(currentRate);
            $("#current_metal_rate").val(currentRate);
        },
    });
}
//get weights   
function get_weight(element, eligible) {
    my_Date = new Date();
    $.ajax({
        type: "GET",
        url: base_url + "index.php/settings/weight_list?nocache=" + my_Date.getUTCSeconds(),
        dataType: "json",
        cache: false,
        success: function (data) {
            var weights = data.data;
            if (weights != '') {
                $.each(weights, function (key, weight) {
                    if (weight.weight <= eligible) {
                        $('#' + element).append(
                            $("<option></option>")
                                .attr("value", weight.weight)
                                .text(weight.weight)
                        );
                    }
                });
                $("#" + element).select2({
                    placeholder: "Select weight",
                    allowClear: true
                });
                $("#" + element).select2("val", '');
            }
        }
    });
}
//to get account detail         
function load_account_detail(id) {
    $('#select_branch').empty();
    my_Date = new Date();
    //show spinner
    $('.overlay').css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/payment/get/ajax/account/' + id + '?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        timeout: 30000,
        success: function (data) {
            try {
            if (!data || !data.account) {
                console.log('No account data returned');
                $('.overlay').css('display', 'none');
                return;
            }
            //  $('#employee_select').empty().append('<option value="">Select branch</option>');   //AB-1907 commented
            //    $('#id_employee').val('');
            //    $("#select_branch").select2("val",'');
            $('#reference_no').val((data.account.reference_no));
            $('#sync_scheme_code').val((data.account.sync_scheme_code));
            $('#nominee_name').val((data.account.nominee_name));
            $('#nominee_relationship').val((data.account.nominee_relationship));
            $('#nominee_address1').val((data.account.nominee_address1));
            $('#nominee_address2').val((data.account.nominee_address2));
            $('#nominee_mobile').val((data.account.nominee_mobile));
            $('#emp_name').val((data.account.emp_name));
            $('#referal_code').val((data.account.referal_code));
            $('#allow_cash_limit').val(data.account.allow_cash_limit);
            $('#disable_acc_payments').val(data.account.disable_acc_payments);
            $('#chit_number').val(data.account.chit_number);
            if (data.account.disable_pay == '1') {
                $('#allow_cash').html('Allowed cash payment limit : INR ' + data.account.allow_cash_limit);
                if (data.account.allow_cash_limit == '0') {
                    $(".mode_CSH").css("pointer-events", 'none');
                    $('.mode_CSH').css("opacity", '0.4');
                }
            }
            //DGS-DCNM
            $('#daily_pay_limit').val((data.account.daily_pay_limit));
            if (data.account.restrict_payment == 1 && data.account.joined_date_diff >= data.account.total_days_to_pay) {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>You are unable to make payment...as you have exceeded maximum pay days limit..." });
            }
            //#DCNM-DGS 
            $('#select_branch').empty();
            $('#id_scheme_account').val((data.account.id_scheme_account));
            $('#id_customer').val((data.account.id_customer));
            //$('#mobile_number').val((data.account.mobile)); Old code 05-12-2022
            $('#mobile_number').val((data.account.mobile + ' ' + data.account.firstname)); // New Code 05-12-2022
            //   console.log(data);
            //added by Durga 15.05.2023 starts here 
            if (data.account.payOtherBranch == 0 && $('#branch_settings').val() == 1) {
                $('#id_branch').val(data.account.id_branch);
                if ($('#branch_select').data('select2')) {
                    $('#branch_select').select2("val", data.account.id_branch);
                } else {
                    $('#branch_select').val(data.account.id_branch).trigger('change');
                }
                $("#branch_select").attr("disabled", true);
            }
            //added by Durga 15.05.2023 ends here 
            if (data.account.cost_center == 1 || data.account.cost_center == 2) {
                $('#id_branch').val((data.account.id_branch));
                $("#select_branch").attr("disabled", true);
                if ($('#branch_select').data('select2')) {
                    $('#branch_select').select2("val", data.account.id_branch);
                } else {
                    $('#branch_select').val(data.account.id_branch).trigger('change');
                }
                var id_branch = $('#id_branch').val();
                $.each(data, function (key, item) {
                    $('#select_branch').append(
                        $("<option></option>")
                            .attr("value", item.id_branch)
                            .text(item.name)
                    );
                });
                $("#select_branch").select2({
                    placeholder: "Select branch name",
                    allowClear: true
                });
            }
            //line added by Durga 09.05.2023 (Gopal Task) starts here
            // if(data.account.get_amt_in_schjoin==1  && data.account.firstPayamt_as_payamt==1 || data.account.firstPayamt_maxpayable==1)
            if (data.account.get_amt_in_schjoin == 1 && data.account.firstPayamt_as_payamt == 1) //esakki
            {
                $("#total_amt").prop('readonly', true);
                // document.getElementById("#total_amt").readOnly = true;
            } else {
                $("#total_amt").prop('readonly', false);
                // document.getElementById("#total_amt").readOnly = false;
            }
            //line added by Durga 09.05.2023 (Gopal Task) ends here
            account_detail_view(data.account).catch(function(err) {
                console.error('account_detail_view error:', err);
                $('.overlay').css('display', 'none');
            });
            $.AdminLTE.boxWidget.activate();
            } catch(err) {
                console.error('load_account_detail success error:', err);
            } finally {
                $('.overlay').css('display', 'none');
            }
        },
        error: function (error) {
            console.log(error);
            //disable spinner
            $('.overlay').css('display', 'none');
        }
    });
}
function load_account(id, id_scheme_account) {
    my_Date = new Date();
    //show spinner
    $('.overlay').css('display', 'block');
    $.ajax({
        type: 'GET',
        data: { 'id_payment': id, 'id_sch_ac': id_scheme_account },
        url: base_url + 'index.php/admin_payment/ajax_load_account',
        dataType: 'json',
        cache: false,
        success: function (data) {
            account_detail_view(data.account)
            $.AdminLTE.boxWidget.activate();
            $('.overlay').css('display', 'none');
        },
        error: function (error) {
            console.log(error);
            //disable spinner
            $('.overlay').css('display', 'none');
        }
    });
}
function clear_account_detail() {
    $("#error-msg").html("");
    $("#start_date").html("");
    $("#acc_name").html("");
    $("#disable_pay_reason").html("");
    $("#disable_payment").html("");
    $("#scheme_code").html("");
    $("#scheme_type").html("");
    $("#payable").html("");
    $("#paid_installments").html("");
    $("#total_amount_paid").html("");
    $("#total_weight_paid").html("");
    $("#total_amt").val("");
    $("#gst_amt").val("");
    $("#payment_amt").val("");
    $("#make_pay_cash").val("");
    $(".sum_of_amt").html("");
    $(".bal_amount").html("");
    $("#payment_weight").val("");
    $(".hidden_allow").hide();
    $("#last_paid_date").html("");
    $("#unpaid_dues").html("");
    $("#total_pdc").html("");
    $("#allow_pay").html("");
    $("#is_preclose").val(0);
    $("#payment_container").html("");
    $('#scheme-detail-box').removeClass('box-success');
    $('#scheme-detail-box').removeClass('box-danger');
    $('#scheme-detail-box').addClass('box-default');
    $("#fix_weight").val("");
    $("#is_flexible_wgt").val("");
    $("#wgt_cvrt").val("");
    $("#sch_amt").val("");
    $("#firstPayamt_maxpayable").val("");
    $("#firstPayment_amt").val("");
    $("#firstPayment_as_wgt").val("");
    $("#sch_type").val("");
    $("#flexible_sch_type").val("");
    $("#metal_wgt_roundoff").val("");
    $("#metal_wgt_decimal").val("");
    $("#total_installments").val("");
    // $("#branch_select").val("");
    // $("#branch_select").val(null).trigger('change')
    // $("#employee_select").val(null).trigger('change')
    // $("#employee_select").empty();
    $('#payment_modes').css("pointer-events", 'none');
    $('#payment_modes').css("opacity", '0.4');
    /*if($("#clear_form").val() == 1)
    {
        $("#scheme_account").val("");
        $("#id_scheme_account").val("");
        $("#id_branch").val("");
        $('#btn-submit').css('display', 'none');
        $("#btn-submit").css("pointer-events", 'none'); 
        $('#btn-submit').css("opacity", '0.9');
        $(window).scrollTop(0);
    }*/
}
//to load account detail view
async function account_detail_view(data) {
    console.log(data);
    //$("#overlay").css("display","none");
    clear_account_detail();
    // $('.toup_sch').css("display","none");
    $('.toup_sch').hide();
    // $('.topup_sch_hide').css("display","block");
    $('.topup_sch_hide').show();
    $('#est_list > tbody').empty();
    calculate_old_metal();
    $('#max_dues').val(data.allowed_dues);
    $('#pay').val(data.payable);
    $('#otp_price_fixing').val(data.otp_price_fixing);
    $('#is_otp_scheme').val(data.is_otp_scheme);
    $('#wgt_store_as').val(data.wgt_store_as);
    $("#cash_paymts").val(data.csh_payments);
    $("#disable_pay_amt").val(data.disable_pay_amt);
    $("#tot_amt_paid").val(data.curday_total_paid); //DGS-DCNM
    var sel_due = $('#sel_due').val();
    var table = "";
    maximum_weight = 0;
    if (data.due_type == 'AN' && data.allowed_dues > 1) {
        var allowed_dues = 1;
        $('#allowed_dues').prop('readonly', false);
    } else {
        var allowed_dues = parseInt(data.allowed_dues);
        $('#allowed_dues').prop('readonly', true);
    }
    var allowed_dues = 1;
    var schID = $("#id_scheme_account").val();
    $('#discount_type').val(data.discount_type);
    $('#discount_installment').val(data.discount_installment);
    $("#flexible_sch_type").val(data.flexible_sch_type);
    $("#sch_type").val(data.scheme_type);
    $("#maturity_type").val(data.maturity_type);
    $("#total_installments").val(data.total_installments);
    $('#discount').val(data.discount);
    $('#firstPayDisc_value').val(data.firstPayDisc_value);
    $('#cost_center').val(data.cost_center);
    console.log(data.cost_center);
    if (data.cost_center == 1 || data.cost_center == 2) {
        $('#select_branch').val(data.id_branch);
        var id_branch = $('#id_branch').val();
    }
    $('#metal_rate').val(data.metal_rate);
    $("#current_metal_rate").val(data.metal_rate);
    $("#id_scheme_account").val(data.id_scheme_account);
    var discount_installment = $('#discount_installment').val();
    var discount_type = $('#discount_type').val();
    var discount = $('#discount').val();
    var paid_installments = $('#paidinstall').val();
    var firstPayDisc_value = $('#firstPayDisc_value').val();
    var one_time_premium = $('#one_time_premium').val(data.one_time_premium);
    $("#metal_wgt_roundoff").val(data.metal_wgt_roundoff);
    $("#metal_wgt_decimal").val(data.metal_wgt_decimal);
    if (data.discount_type == 0) {
        $('#discountedAmt').val(data.firstPayDisc_value);
    } else if (data.discount_installment == data.current_ins) {
        $('#discountedAmt').val(data.firstPayDisc_value);
    } else {
        $('#discountedAmt').val('');
    }

    console.log(schID);
    if (schID != '') {
        if (data.allow_pay == 'Y') {
            $('#scheme-detail-box').addClass('box-success');
            $("#allow_pay").html("<span class='label label-success'>Yes</span>");
            $('#payment_container').html("<table id='tableHead' class='table table-bordered'></table><table id='tableRow' class='table table-bordered'></table>");
            $('#btn-submit').css('display', 'block');
        } else {
            $('#btn-payment').prop('disabled', true);
            $("#allow_pay").html("<span class='label label-danger'>No</span>");
            $('#scheme-detail-box').addClass('box-danger');
            $('#btn-submit').css('display', 'none');
        }
        if (data.is_topup_scheme == 1 && data.installment_cycle == 4) {
            var toup_pay_data = data.toup_pay_data;
            $('#topup_slab_value').empty();
            // $('.toup_sch').css("display","block");
            $('.toup_sch').show();
            // $('.topup_sch_hide').css("display","none");
            $('.topup_sch_hide').hide();
            $("#topup_amount").html(indianCurrency.format(data.topup_amount));
            $("#topup_weight").html(data.topup_weight);
            $("#topup_rate").html(indianCurrency.format(data.topup_rate));
            $("#topup_amount_paid").html(indianCurrency.format(data.total_paid_amount));
            $("#topup_balance_pay").html(indianCurrency.format(data.topup_balance_pay));
            $("#topup_min_payable").html(indianCurrency.format(data.min_amount));
            $("#topup_max_payable").html(indianCurrency.format(data.max_amount));
            $("#topup_estimated_paid_amount").html(indianCurrency.format(data.topup_estimated_paid_amount));
            if (data.allow_pay == 'Y') {
                //$("#topup_slab_value").html(data.topup_slab_value+''+data.topup_slab_type);
                $("#topup_slab_endday").html(data.topup_slab_endday);
                $("#due_date_to").html(formatDate(data.due_date_to));
                $('#topup_slab_value').css('display', 'block');
                $.each(toup_pay_data, function (key, item) {
                    $('#topup_slab_value').append(
                        $("<option></option>")
                            .attr("value", item.range_to)
                            .text(item.payable_value + '%')
                    );
                });
                $('#topup_slab_value').on('change', function () {
                    const selectedValue = parseInt($(this).val());
                    var matched = toup_pay_data.find(item => parseInt(item.range_to) === selectedValue);
                    if (matched) {
                        data.min_amount = matched.minimum_payable_amount;
                        data.max_amount = matched.maximum_payable_amount;
                        $('#error-msg .alert').hide();
                        $("#topup_min_payable").html(indianCurrency.format(matched.minimum_payable_amount));
                        $("#topup_max_payable").html(indianCurrency.format(matched.maximum_payable_amount));
                        $("#topup_slab_endday").html(matched.range_to);
                        $("#topup_estimated_paid_amount").html(indianCurrency.format(matched.estimated_paid_amount));
                        $("#due_date_to").html(formatDate(matched.due_date_to));
                        $("#total_amt").val(matched.minimum_payable_amount);
                        $("#payment_amt").val(matched.minimum_payable_amount);
                        calculatePaymentCost();
                    } else {
                        $("#topup_min_payable").html(indianCurrency.format(data.min_amount));
                        $("#topup_max_payable").html(indianCurrency.format(data.max_amount));
                        $("#topup_slab_endday").html(data.topup_slab_endday);
                        $("#topup_estimated_paid_amount").html(indianCurrency.format(data.estimated_paid_amount));
                        $("#due_date_to").html(formatDate(data.due_date_to));
                        $("#total_amt").val(data.min_amount);
                        $("#payment_amt").val(data.min_amount);
                        calculatePaymentCost();
                    }
                });
            } else {
                $("#due_date_to,#topup_slab_endday").html('-');
            }
        }
        if (data.scheme_type == 0 || data.scheme_type == 2) // AMOUNT , AMOUNT TO WEIGHT
        {
            $('#total_amt').prop('readonly', true);
            $('#proced').css("display", 'block');
            $('#enable_editing_blk').css("display", 'block');
            $("#payable").html("<strong>" + data.currency_symbol + "</strong> " + parseFloat(data.payable).toFixed(2));
            $("#total_amount_paid").html("<strong>" + data.currency_symbol + "</strong> " + parseFloat(data.total_paid_amount).toFixed(2));
            // esakki 22-11
            $("#total_weight_paid").html(
                
                formatMetalWeight(data.total_paid_weight)
             + " <strong>gm</strong>"

                // parseFloat(session_data.metal_wgt_roundoff == 0 ? trimDecimal(data.total_paid_weight, session_data.metal_wgt_decimal) : data.total_paid_weight.toFixed(session_data.metal_wgt_decimal)) + " <strong>gm</strong>"
            );
            //draw_payment_table(data);
            var sch_dis = (data.discount == 1 ? (data.discount_type == 0 ? data.firstPayDisc_value : (data.discount_installment == data.current_ins ? data.firstPayDisc_value : 0.00)) : 0.00);
            var amount = parseFloat(data.payable).toFixed(2) * parseFloat(allowed_dues).toFixed(2) - parseFloat(sch_dis);
            $('#total_amt').val(parseFloat(amount).toFixed(0)); // esakki 19-09
            $('#payamt').val(data.payable);
            $('.hidden_allow').show();
            /*	if(allowed_dues >1)
            {
            $('#allowed_dues').prop('readonly',false);
            amount = parseFloat(data.payable).toFixed(2) * parseFloat(allowed_dues).toFixed(2)-parseFloat((discount==1 ?(discount_type==0 ?firstPayDisc_value :(discount_installment==paid_installments ?firstPayDisc_value :0.00)) :0.00));
            $('#total_amt').val(amount);
            }  */
            $('#payment_container').html('');
            var pending_dues = parseInt(data.total_installments - data.paid_installments);
            if (data.preclose == 1 && parseInt(data.preclose_benefits) == pending_dues) {
                allowed_dues = parseInt(data.preclose_benefits);
                amount = parseFloat(data.payable).toFixed(2) * parseFloat(allowed_dues).toFixed(2) - parseFloat((discount == 1 ? (discount_type == 0 ? firstPayDisc_value : (discount_installment == paid_installments ? firstPayDisc_value : 0.00)) : 0.00));
                $('#total_amt').val(parseFloat(amount).toFixed(0)); // esakki 19-09
            }
            // wallet calculation
            var total_amount = parseFloat($('#total_amt').val());
            /* var can_redeem = 0;
            if ($(".ischk_wallet_pay").is(":checked") && parseFloat($(".wallet_balance").val()) > 0) {
                var allowed_redeem = ($('.redeem_percent').val() > 0 ? (total_amount * (parseFloat($('.redeem_percent').val()) / 100)) : 0);
                wallet_balance = parseFloat($('.wallet_balance').val());
                if (allowed_redeem > wallet_balance) {
                    can_redeem = wallet_balance;
                }
                else {
                    can_redeem = allowed_redeem;
                }
            }
            $('.wallet').val(can_redeem);
            $('.redeem_request').val(can_redeem); */
            calc_walletbalance(); // esakki 31-07-2025
            //GST Calculation
            var gst_val = 0;
            var gst_amt = 0;
            var gst = 0;
            if (data.gst > 0) {
                // esakki 19-09
                if (data.gst_type == 0) {
                    gst_val = parseFloat(data.payable) - (parseFloat(data.payable) * (100 / (100 + parseFloat(data.gst))));
                    gst_amt = gst_val * allowed_dues;
                    gst = gst_amt;
                    $('#payment_amt').val((parseFloat(amount)).toFixed(0));
                } else {
                    gst_val = (parseFloat(data.payable) * (parseFloat(data.gst) / 100));
                    gst_amt = gst_val * allowed_dues;
                    gst = gst_amt;
                    $('#payment_amt').val((parseFloat(gst) + parseFloat(amount)).toFixed(0));
                }
            } else {
                $('#payment_amt').val((parseFloat(amount)).toFixed(0));
            }
            // esakki 17-09
            $('#gst_amt').val((gst_amt).toFixed(0));
            // $('#payment_amt').val((parseFloat(gst)+parseFloat(amount)).toFixed(0));
            if ($('#payment_amt').val() > 0) {
                $("#proced").on("click", function (event) {
                    /* Restrict payment and direct to customer kyc based on dynamic KYC rules */
                    if (typeof window.trigger_dynamic_kyc_modal === 'function') {
                        window.trigger_dynamic_kyc_modal('#id_scheme', '#id_customer', '#payment_amt', '#id_scheme_account', '#btn-submit');
                    }
                    /* /KYC check */
                    if (data.scheme_type == 2) {
                        calculate_total_weight(data) // esakki 19-09
                    }
                    $('#payment_modes').css("pointer-events", 'all');
                    $('#payment_modes').css("opacity", '0.9');
                });
                calculatePaymentCost(); // esakki 18-09
            }
        }
        // flxi scheme 
        else if (data.scheme_type == 3) {
            $('#total_amt').prop('readonly', false);
            if (data.paid_installments > 0 && data.one_time_premium == 1) {
                msg = '<div class = "alert alert-warning"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>  You have already paid ' + data.total_paid_amount + '</strong></div>';
                $('#error-msg').html(msg);
                $('#proced').css("display", 'none');
                $('#total_amt').prop("readonly", true);
            } else if (data.current_chances_used == data.max_chance && data.min_chance > data.max_chance) {
                msg = '<div class = "alert alert-warning"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>  You have already reached max payment chance [' + data.max_chance + ']</strong></div>';
                $('#error-msg').html(msg);
                $('#proced').css("display", 'none');
                $('#total_amt').prop("readonly", true);
            } else if (data.max_amount == 0 && (data.flexible_sch_type == 1 || data.flexible_sch_type == 2 || data.flexible_sch_type == 5)) {
                msg = '<div class = "alert alert-warning"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>  You have already reached max payment amount ' + data.current_total_amount + '</strong></div>';
                $('#error-msg').html(msg);
                $('#proced').css("display", 'none');
                $('#total_amt').prop("readonly", true);
            } else {
                console.log(data.get_amt_in_schjoin);
                if (data.flexible_sch_type <= 2 && data.get_amt_in_schjoin != 1) {
                    $('#total_amt').prop('readonly', false);
                    msg = '<div class = "alert alert-info"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Min ' + data.min_amount + '  Max ' + data.max_amount + '</strong></div>';
                    $('#error-msg').html(msg);
                } else if (data.flexible_sch_type == 3) {
                    msg = '<div class = "alert alert-info"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Min ' + data.min_weight + '  Max ' + data.max_weight + '</strong></div>';
                    $('#error-msg').html(msg);
                } else if (data.flexible_sch_type == 4) {
                    if (data.firstPayment_as_wgt == 1 && data.paid_installments == 0) {
                        // First payment with weight fixing — show weight slab table
                        $('#total_amt').prop('readonly', true);
                        $('#proced').css("display", 'none');
                        $('#enable_editing_blk').css("display", 'none');
                        var eligible_weight = session_data.metal_wgt_roundoff == 0 ? formatMetalWeight(data.max_weight) : data.max_weight.toFixed(session_data.metal_wgt_decimal) - session_data.metal_wgt_roundoff == 0 ? formatMetalWeight(data.current_total_weight) : data.current_total_weight.toFixed(session_data.metal_wgt_decimal);
                        $("#payable").html(parseFloat(data.payable).toFixed(2) + " <strong>gm</strong> ");
                        $("#total_amount_paid").html("<strong>" + data.currency_symbol + "</strong> " + indianCurrency.format(parseFloat(data.total_paid_amount).toFixed(2)));
                        $("#total_weight_paid").html(formatMetalWeight(data.total_paid_weight) + " <strong>gm</strong>");
                        var weight_check = '<div class="rate-table"><table class="table table-bordered table-striped table-responsive text-center">' +
                            '<tr><th colspan="3" style="text-align:center" ><h3> Gold 22k 1gm rate  : ' + data.currency_symbol + ' ' + parseFloat($('#metal_rate').val()).toFixed(2) + '</h3></th></tr>' +
                            '<tr><td><h4><div style="float:left">Eligible:</div><div style="float:right">' + formatMetalWeight(eligible_weight) + ' g<input type="hidden" id="eligible_weight" value="' + formatMetalWeight(eligible_weight) + '" /></div></h4></td><td><h4><div style="float:left">Selected:</div><div style="float:right"><span id="sel_wt" >0.0000</span> g</div><input type="hidden"  id="selected_weight" name="generic[metal_weight]"  value="0"/></h3></td></tr>' +
                            '<tr><th>Weight</th><th>Amount</th></tr>';
                        $.each(data.weights, function () {
                            if ((parseFloat(data.current_total_weight) + parseFloat(this.weight)) <= parseFloat(data.max_weight) && (parseFloat(data.current_total_weight) + parseFloat(this.weight)) >= parseFloat(data.min_weight)) {
                                weight_check += "<tr><td><input type='checkbox' name='weight_gold' value='" + this.weight + "' />\t" + formatMetalWeight(this.weight) + " gram </td><td>  " + data.currency_symbol + " " + parseFloat(this.weight * $('#metal_rate').val()).toFixed(2) + " </td></tr>";
                            }
                        });
                        weight_check += '<table></div>';
                        $('#payment_container').html(weight_check);
                    } else {
                        // Normal type 4 behavior — show min/max weight info
                        $('#total_amt').prop('readonly', false);
                        msg = '<div class = "alert alert-info"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Min ' + data.min_weight + '  Max ' + data.max_weight + '</strong></div>';
                        $('#error-msg').html(msg);
                    }
                }
                //chinannan wgt sch
                else if (data.flexible_sch_type == 8 && (data.firstPayment_as_wgt == 1 && data.paid_installments == 0)) {
                    $('#total_amt').prop('readonly', true);
                    $('#proced').css("display", 'none');
                    $('#enable_editing_blk').css("display", 'none');
                    // var eligible_weight = parseFloat(data.max_weight).toFixed(3) - parseFloat(data.current_total_weight).toFixed(3);
                     var eligible_weight = session_data.metal_wgt_roundoff == 0 ? formatMetalWeight(data.max_weight) : data.max_weight.toFixed(session_data.metal_wgt_decimal) - session_data.metal_wgt_roundoff == 0 ? formatMetalWeight(data.current_total_weight) : data.current_total_weight.toFixed(session_data.metal_wgt_decimal);
                    $("#payable").html(parseFloat(data.payable).toFixed(2) + " <strong>gm</strong> ");
                    $("#total_amount_paid").html("<strong>" + data.currency_symbol + "</strong> " + indianCurrency.format(parseFloat(data.total_paid_amount).toFixed(2)));
                    // $("#total_weight_paid").html(parseFloat(data.total_paid_weight).toFixed(3) + " <strong>gm</strong>");
                     $("#total_weight_paid").html(formatMetalWeight(data.total_paid_weight) + " <strong>gm</strong>");
                    var weight_check = '<div class="rate-table"><table class="table table-bordered table-striped table-responsive text-center">' +
                        '<tr><th colspan="3" style="text-align:center" ><h3> Gold 22k 1gm rate  : ' + data.currency_symbol + ' ' + parseFloat($('#metal_rate').val()).toFixed(2) + '</h3></th></tr>' +
                        // '<tr><td><h4><div style="float:left">Eligible:</div><div style="float:right">' + parseFloat(eligible_weight).toFixed(3) + ' g<input type="hidden" id="eligible_weight" value="' + parseFloat(eligible_weight).toFixed(3) + '" /></div></h4></td><td><h4><div style="float:left">Selected:</div><div style="float:right"><span id="sel_wt" >0.000</span> g</div><input type="hidden" id="selected_weight" name="generic[metal_weight]"  value="0"/></h3></td></tr>' +
                        '<tr><td><h4><div style="float:left">Eligible:</div><div style="float:right">' + formatMetalWeight(eligible_weight) + ' g<input type="hidden" id="eligible_weight" value="' + formatMetalWeight(eligible_weight) + '" /></div></h4></td><td><h4><div style="float:left">Selected:</div><div style="float:right"><span id="sel_wt" >0.0000</span> g</div><input type="hidden"  id="selected_weight" name="generic[metal_weight]"  value="0"/></h3></td></tr>' +
                        '<tr><th>Weight</th><th>Amount</th></tr>';
                    $.each(data.weights, function () {
                        //console.log(data.current_total_weight);
                        if ((parseFloat(data.current_total_weight) + parseFloat(this.weight)) <= parseFloat(data.max_weight) && (parseFloat(data.current_total_weight) + parseFloat(this.weight)) >= parseFloat(data.min_weight)) {
                            // weight_check += "<tr><td><input type='checkbox' name='weight_gold' value='" + this.weight + "' />	" + parseFloat(this.weight).toFixed(3) + " gram </td><td>  " + data.currency_symbol + " " + parseFloat(this.weight * $('#metal_rate').val()).toFixed(2) + " </td></tr>";
                             weight_check += "<tr><td><input type='checkbox' name='weight_gold' value='" + this.weight + "' />	" + formatMetalWeight(this.weight) + " gram </td><td>  " + data.currency_symbol + " " + parseFloat(this.weight * $('#metal_rate').val()).toFixed(2) + " </td></tr>";
                        }
                    });
                    weight_check += '<table></div>';
                    $('#payment_container').html(weight_check);
                } else if (data.flexible_sch_type == 8 && data.firstPayment_as_wgt == 0) {
                    $('#total_amt').prop('readonly', false);
                    msg = '<div class = "alert alert-info"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Min ' + data.min_weight + '  Max ' + data.max_weight + '</strong></div>';
                    $('#error-msg').html(msg);
                }
                //ends
                // else if(data.flexible_sch_type <= 4 && data.get_amt_in_schjoin ==1 && data.paid_installments > 0 && data.due_type != 'GEN_ADV'){  // firstPayment_amt get from customer based on the scheme settings//HH
                else if (data.flexible_sch_type <= 4 && data.get_amt_in_schjoin == 1 && data.paid_installments > 0 && data.due_type != 'GEN_ADV' && data.firstPayamt_maxpayable != 1) { // firstPayment_amt get from customer based on the scheme settings//HH   //esakki
                    msg = '<div class = "alert alert-info"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> You have already Fixed  ' + data.firstPayment_amt + '</strong></div>';
                    $('#total_amt').prop('readonly', true);
                    $('#proced').css("display", 'block');
                    $('#error-msg').html(msg);
                }
                if (data.firstPayment_amt > 0 || data.firstpayment_wgt > 0 && data.due_type != 'GEN_ADV') {
                    if (data.firstPayment_amt > 0 && data.firstPayment_amt != null) {
                        calculate_payAmt(data.firstPayment_amt);
                        if (data.firstPayamt_maxpayable != 1) { //esakki
                            $('#total_amt').prop('readonly', true);
                        }
                        $('#total_amt').val(parseFloat(data.firstPayment_amt).toFixed(0)); // esakki 19-09
                    } else if (data.firstpayment_wgt > 0 && data.firstpayment_wgt != null) {
                        var totAmt = data.firstpayment_wgt * $('#metal_rate').val();
                        console.log(totAmt);
                        calculate_payAmt(totAmt);
                        msg = '<div class = "alert alert-info"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> You have already Fixed  ' + data.firstpayment_wgt + ' Grm</strong></div>';
                        $('#total_amt').prop('readonly', true);
                        $('#total_amt').val(parseFloat(totAmt).toFixed(0)); // esakki 19-09
                        $('#error-msg').html(msg);
                    }
                    if (data.allow_pay == 'Y') {
                        $("#btn-submit").css("display", "block");
                    }
                    $('#proced').css("display", 'block');
                    //$('#total_amt').css("readonly", true);
                } else {
                    $('#total_amt').val(data.min_amount);
                    calculate_payAmt(data.min_amount);
                    $('#proced').css("display", 'block');
                    $("#btn-submit").css("display", "none");
                }
            }
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $('#enable_editing_blk').css("display", 'block');
            if (data.flexible_sch_type == 1 || data.flexible_sch_type == 2) {
                $("#payable").html("<strong>" + data.currency_symbol + "</strong> " + parseFloat(data.payable).toFixed(2));
            } else if (data.firstPayment_as_wgt == 1 && data.firstpayment_wgt > 0) {
                $("#payable").html("<strong>" + data.currency_symbol + "</strong> " + indianCurrency.format(parseFloat(data.payable).toFixed(2)) + " (" + formatMetalWeight(data.firstpayment_wgt) + " g)");
            } else {
                // $("#payable").html("Max " + parseFloat(data.payable).toFixed(3) + " g/month");
                $("#payable").html("Max " + formatMetalWeight(data.payable) + " g/month");
            }
            $("#total_amount_paid").html("<strong>" + data.currency_symbol + "</strong> " + indianCurrency.format(parseFloat(data.total_paid_amount).toFixed(2)));
            // $("#total_weight_paid").html(parseFloat(data.total_paid_weight) + " <strong>gm</strong>");
             $("#total_weight_paid").html(formatMetalWeight(data.total_paid_weight) + " <strong>gm</strong>");        
            // Set Payable weight
            if (data.firstPayment_wgt > 0) {
                var installment_amt = $('#metal_rate').val() * data.firstPayment_wgt;
                $('#total_amt').val(installment_amt);
                $('#total_amt').prop('readonly', true);
                calculate_payAmt(installment_amt);
            }
            if (data.firstPayamt_as_payamt == 1 && data.flexible_sch_type != 3) {
                $('#total_amt').prop('readonly', false);
                $('#total_amt').val(data.payable);
                $('#payment_amt').val(data.payable);
            }
            if (data.firstPayment_amt > 0) {
                $('#total_amt').val(data.firstPayment_amt);
            }
            $('.hidden_allow').show();
            if (allowed_dues > 1) {
                $('#allowed_dues').prop('readonly', false);
            }
            if (data.flexible_sch_type == 5) {
                $('#total_amt').val(data.payable);
                //var amount= parseFloat(data.payable).toFixed(2) * parseFloat(allowed_dues).toFixed(2)-parseFloat(sch_dis);
                //$('#total_amt').val(amount);
                // $('#total_amt').attr("disabled",true);
                $('#old_metal').css("display", "block");
            } else {
                $('#old_metal').css("display", "none");
                $('#total_amt').attr("disabled", false);
            }
            if (data.avg_payable > 0 && data.avg_payable != null) {
                msg = '<div class = "alert alert-info"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Min ' + data.min_amount + '  Max ' + data.avg_payable + '</strong></div>';
                $('#error-msg').html(msg);
                $('#total_amt').val(data.avg_payable);
                $('#total_amt').prop('readonly', false);
            }
            $('.hidden_allow').show();
            if (allowed_dues > 1) {
                $('#allowed_dues').prop('readonly', false);
            }
            // esakki
            /*   $("#total_amt").on('input',function()
              { 
                  var amt=$('#total_amt').val();
                  $('#payment_amt').val(amt);
                  var cur_metal_rate = $('#metal_rate').val();
                   //display weight for all weight schemes based on current metal rate and payment amount.
                  var wgt = parseFloat(amt/cur_metal_rate).toFixed(3);
                  $('#payment_weight').val(wgt);
          //display weight for all weight schemes based on current metal rate and payment amount.
                  if(amt % (data.flx_denomintion)!=0 && data.flx_denomintion != null && (data.flexible_sch_type == 1 || data.flexible_sch_type == 2 || data.flexible_sch_type == 5) )
                  {
                      alert('Please Enter a amount in  multiples of '+data.flx_denomintion+'');
                     //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter a amount in  multiples of "+data.flx_denomintion+"...."});
                      $("#total_amt").val('');
                      $("#btn-submit").css("display", "none"); 
                  }
                  else
                  {
                      $('#proced').css("display", 'block');
                  }
                  calculatePaymentCost();
              }); */
            $("#proced").on("click", function (event) //esakki
            {
                /* Restrict payment and direct to customer kyc based on dynamic KYC rules */
                if (typeof window.trigger_dynamic_kyc_modal === 'function') {
                    window.trigger_dynamic_kyc_modal('#id_scheme', '#id_customer', '#payment_amt', '#id_scheme_account', '#btn-submit');
                }
                /* /KYC check */
                var amt = $('#total_amt').val();
                var cur_metal_rate = $('#metal_rate').val();
                //display weight for all weight schemes based on current metal rate and payment amount.
                var wgt = formatMetalWeight(amt / cur_metal_rate);
                $('#payment_weight').val(wgt);
                if (amt % (data.flx_denomintion) != 0 && data.flx_denomintion != null && (data.flexible_sch_type == 1 || data.flexible_sch_type == 2 || data.flexible_sch_type == 5)) {
                    alert('Please Enter a amount in  multiples of ' + data.flx_denomintion + '');
                    //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter a amount in  multiples of "+data.flx_denomintion+"...."});
                    $("#total_amt").val('');
                    $("#btn-submit").css("display", "none");
                    $('#payment_modes').css("pointer-events", 'none');
                    $('#payment_modes').css("opacity", '0.4');
                    $('#payamt').val('');
                    $('#payment_amt').val('');
                } else {
                    if (data.flexible_sch_type == 8 && $('#sel_wt').html() > 0) {
                        $('#payment_weight').val($('#sel_wt').html());
                    }
                    /* Restrict payment and direct to customer kyc based on pan required amount and required by starts...#Dt_add:25-01-2024, #AB */
                    if ($("#total_amt").val() > 0) {
                        amt = $("#total_amt").val();
                    } else {
                        amt = 0;
                    }
                    /* var overallAmountSum = parseInt(amt) + parseInt(data.cus_overall_amount);
                    var schemeAmountSum = parseInt(amt) + parseInt(data.total_paid_amount);
                    
                    if (
                        (data.direct_to_kyc == "Y" &&
                            data.pan_required_by == 2 &&
                            schemeAmountSum >= data.pan_req_amt) ||
                        (data.pan_required_by == 1 &&
                            overallAmountSum >= data.pan_req_amt)
                    ) {
                        var redirect_kyc =
                            "You are about to reach PAN required amount... Kindly provide the required PAN details in Customer Kyc to proceed further payments...";
                        alert(redirect_kyc);
                        window.location.href =
                            base_url + "index.php/customer/edit/" + data.id_customer;
                    } else if ( //sch_pan_amt,sch_pan_req
                        (data.sch_pan_req > 0 &&
                            data.sch_pan_req == 2 &&
                            data.sch_pan_get == true &&
                            schemeAmountSum >= data.sch_pan_amt) ||
                        (data.sch_pan_req == 1 &&
                            data.sch_pan_get == true &&
                            overallAmountSum >= data.sch_pan_amt)
                    ) {
                        var redirect_kyc =
                            "You are about to reach PAN required amount... Kindly provide the required PAN details in Customer Kyc to proceed further payments...";
                        alert(redirect_kyc);
                        window.location.href =
                            base_url + "index.php/customer/edit/" + data.id_customer;
                    } */
                    /* Redirect to customer kyc ends*/
                    $('#payment_modes').css("pointer-events", 'all');
                    $('#payment_modes').css("opacity", '0.9');
                    if (data.is_digi == 1) {
                        console.log(data);
                        $(".digi_benefit_block").css('display', 'block');
                        let digi_data = {
                            benefit_type: data.dg_benefit_type,
                            benefit_value: data.dg_benefit_value,
                            benefit_content: data.dg_benefit_content,
                            currency_symbol: data.currency_symbol,
                            is_other_benefit: data.is_other_benefit,
                            other_benefit_type: data.dg_other_benefit_type,
                            other_benefit_value: data.dg_other_benefit_value,
                            other_metal: data.metal_name,
                            other_metal_rate: data.other_metal_rate,
                            other_benefit_content: data.dg_other_benefit_content
                        };
                        digi_benefit_calculation(digi_data);
                    }
                    //esakki 19-09
                    if (data.flexible_sch_type == 8) {
                        if (data.firstPayment_as_wgt != 1) {
                            $("#payamt").val(amt);
                            $("#payment_amt").val(amt);
                            calculate_total_weight(data);
                        } else if (
                            data.firstPayment_as_wgt == 1 &&
                            data.paid_installments != 0
                        ) {
                            $("#payamt").val(amt);
                            $("#payment_amt").val(amt);
                        }
                    } else if (data.flexible_sch_type != 8) {
                        $("#payamt").val(amt);
                        $("#payment_amt").val(amt);
                        calculate_total_weight(data);
                    }
                    calc_walletbalance(); // esakki 31-07-2025
                    var metal_weight_cal = $('#payment_weight').val(); // esakki 19-09
                    var tot_est_amt = $('.tot_est_amt').html(); // esakki 19-09
                    var tot_est_weight = $('.tot_est_weight').html(); // esakki 19-09
                    console.log(tot_est_amt);
                    if (tot_est_amt > 0) {
                        amount = parseFloat(tot_est_amt) + parseFloat(amount);
                    }
                    if (tot_est_weight > 0) {
                        metal_weight_cal = parseFloat(tot_est_weight) + parseFloat(metal_weight_cal);
                    }
                    //#DSG - DCNM daily pay limit restriction... start
                    var daily_pay_limit = $("#daily_pay_limit").val(); //15000
                    var tot_amt_paid = $("#tot_amt_paid").val(); //3000
                    var amt_after_paid = parseFloat(amt) + parseFloat(tot_amt_paid);
                    var elgi_pay_amt = parseFloat(daily_pay_limit) - parseFloat(tot_amt_paid);
                    //#DSG - DCNM daily pay limit restriction... end
                    var amount = $("#total_amt").val();
                    if (amount >= parseFloat(data.min_amount) && amount <= parseFloat(data.max_amount) && (parseFloat(data.max_chance) > parseFloat(data.current_chances_used) || (data.allow_advance == 1 && data.allowed_dues != 0))) {
                        console.log(data);
                        //#DSG - DCNMdaily pay limit restriction... start			
                        if (daily_pay_limit > 0) {
                            if (tot_amt_paid >= daily_pay_limit) {
                                msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> You have already reached daily payment limit INR ' + daily_pay_limit + '...</strong></div>';
                                $("div.overlay").css("display", "none");
                                $('#error-msg').html(msg);
                                //$("#btn-submit").css("display", "block"); 
                            } else if (amt_after_paid > daily_pay_limit) {
                                if (elgi_pay_amt >= data.min_amount) {
                                    msg = '<div class = "alert alert-warning"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> You are about to reach daily payment limit...You are eligible to pay INR ' + data.min_amount + ' to INR ' + elgi_pay_amt + ' today</strong></div>';
                                    $("div.overlay").css("display", "none");
                                    $('#error-msg').html(msg);
                                    //$("#btn-submit").css("display", "block");
                                } else {
                                    msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> You are about to reach daily payment limit. Unable to make payment less than INR ' + data.min_amount + '. You can make payment tomorrow....</strong></div>';
                                    $("div.overlay").css("display", "none");
                                    $('#error-msg').html(msg);
                                    //$("#btn-submit").css("display", "block");
                                }
                            } else {
                                msg = '<div class = "alert " style="background-color:green; color:white;"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Sucess Click Save </div>';
                                $("div.overlay").css("display", "none");
                                //stop the form from submitting
                                $('#error-msg').html(msg);
                                $("div.overlay").css("display", "none");
                                $("#btn-submit").css("display", "block");
                                calculate_payAmt(amount / sel_due);
                            }
                        } else {
                            msg = '<div class = "alert " style="background-color:green; color:white;"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Sucess Click Save </div>';
                            $("div.overlay").css("display", "none");
                            //stop the form from submitting
                            $('#error-msg').html(msg);
                            $("div.overlay").css("display", "none");
                            $("#btn-submit").css("display", "block");
                            calculate_payAmt(amount / sel_due);
                        }
                        //DGS-DCNM  ends                       
                    } else {
                        // alert(data.due_type);
                        //var  Eligible_pay = data.firstPayamt_maxpayable==1 && data.paid_installments>0 || data.is_registered==1 ? data.max_amount:(data.max_amount!=0 && data.max_weight==0 ?  parseFloat(data.max_amount) - parseFloat(data.current_total_amount):(parseFloat((parseFloat(data.max_weight) - parseFloat(data.current_total_weight))*$("#metal_rate").val()).toFixed(3)));
                        if (data.paid_installments > 0 && data.one_time_premium == 1 && data.due_type != 'GEN_ADV') {
                            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>  You have already paid ' + data.total_paid_amount + '</strong></div>';
                        } else if (data.current_chances_used == data.max_chance && data.due_type != 'GEN_ADV') {
                            msg = '<div class = "alert alert-warning"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>  You have already reached max payment chance [' + data.max_chance + ']</strong></div>';
                        }
                        /* else if((data.flexible_sch_type == 1 || data.flexible_sch_type == 2 || data.flexible_sch_type == 5)  && data.due_type != 'GEN_ADV'){ */
                        else if ((data.flexible_sch_type == 1 || data.flexible_sch_type == 2 || data.flexible_sch_type == 5) && data.due_type != 'GEN_ADV' && (amount > data.max_amount || amount < data.min_amount)) {
                            /*var  Eligible_pay = data.max_amount!=0 && data.max_weight==0 ? parseFloat(data.max_amount) - parseFloat(data.current_total_amount):(parseFloat((parseFloat(data.max_weight) - parseFloat(data.current_total_weight))*$("#metal_rate").val()).toFixed(3));*/
                                var Eligible_pay = data.firstPayamt_as_payamt == 1 && data.paid_installments > 0 || data.is_registered == 1 ? data.max_amount : (data.max_amount != 0 && data.max_weight == 0 ? parseFloat(data.max_amount) : formatMetalWeight((parseFloat(data.max_weight) - parseFloat(data.current_total_weight)) * $("#metal_rate").val()));
                            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>  You could not pay less than <strong> Rs  ' + data.min_amount + '</strong>  and' + ' ' + ' You could not pay more than <strong> Rs  ' + data.max_amount + '</strong></div>';
                            $("div.overlay").css("display", "none");
                            //stop the form from submitting
                            $('#error-msg').html(msg);
                            $("#btn-submit").css("display", "none");
                            $('#payment_amt').val(0);
                            $('#payment_weight').val(0);
                            return false;
                        } else {
                            msg = '<div class = "alert " style="background-color:green; color:white;"><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Sucess Click Save </div>';
                            $("div.overlay").css("display", "none");
                            //stop the form from submitting
                            $('#error-msg').html(msg);
                            $("div.overlay").css("display", "none");
                            $("#btn-submit").css("display", "block");
                            calculate_payAmt(amount / sel_due);
                        }
                    }
                }
                calculatePaymentCost();
            });
        }
        // flxi scheme 
        else if (data.scheme_type == 1 && data.is_flexible_wgt == 0) {
            $('#total_amt').prop('readonly', true);
            $('#proced').css("display", 'none');
            $('#enable_editing_blk').css("display", 'block');
            $('.hidden_allow').show();
            $('#payamt').val(data.max_weight * parseFloat($('#metal_rate').val()).toFixed(2));
            if (allowed_dues > 1) {
                $('#allowed_dues').prop('readonly', false);
            }
            var eligible_weight = formatMetalWeight(data.max_weight);
            $("#payable").html(formatMetalWeight(data.payable) + " <strong>gm</strong> ");
            $("#total_amount_paid").html("<strong>" + data.currency_symbol + "</strong> " + indianCurrency.format(parseFloat(data.total_paid_amount).toFixed(2)));
            $("#total_weight_paid").html(formatMetalWeight(data.total_paid_weight) + " <strong>gm</strong>");
            //draw_payment_table(data);
            var weight_check = '<div class="rate-table"><table class="table table-bordered table-striped table-responsive text-center">' +
                '<tr><th colspan="3" style="text-align:center" ><h3 > Gold 22k 1gm rate  : <span id="rate"> ' + data.currency_symbol + ' ' + parseFloat($('#metal_rate').val()).toFixed(2) + '</span></h3></th></tr>' +
                '<tr><td><h4><div style="float:left">Eligible:</div><div style="float:right">' + formatMetalWeight(eligible_weight) + ' g<input type="hidden" id="eligible_weight" value="' + formatMetalWeight(eligible_weight) + '" /></div></h4></td><td><h4><div style="float:left">Selected:</div><div style="float:right"><span id="sel_wt" >0.000</span> g</div><input type="text" id="selected_weight" name="generic[metal_weight]"  value="0"/></h3></td></tr>' +
                '<tr "><th colspan="3">Weight</th></tr>';
            $.each(data.weights, function () {
                console.log(this.weight);
                if (parseFloat(this.weight) == parseFloat(data.max_weight)) {
                    weight_check += "<tr style='text-align:center'><td><input type='checkbox' name='weight_gold' value='" + this.weight + "' />	" + formatMetalWeight(this.weight) + " gram </td></tr>";
                }
            });
            weight_check += '<table></div>';
            console.log(weight_check);
            $('#payment_container').html(weight_check);
            // esakki 17-09
            $("#proced").on("click", function (event) {
                $('#payment_modes').css("pointer-events", 'all');
                $('#payment_modes').css("opacity", '0.9');
            });
        } else if (data.scheme_type == 1 && data.is_flexible_wgt == 1) {
            $('#total_amt').prop('readonly', true);
            $('#proced').css("display", 'none');
            $('#enable_editing_blk').css("display", 'none');
            var eligible_weight = formatMetalWeight(data.max_weight) - formatMetalWeight(data.current_total_weight);
            $("#payable").html(formatMetalWeight(data.payable) + " <strong>gm</strong> ");
            $("#total_amount_paid").html("<strong>" + data.currency_symbol + "</strong> " + indianCurrency.format(parseFloat(data.total_paid_amount).toFixed(2)));
            $("#total_weight_paid").html(formatMetalWeight(data.total_paid_weight) + " <strong>gm</strong>");
            //draw_payment_table(data);
            var weight_check = '<div class="rate-table"><table class="table table-bordered table-striped table-responsive text-center">' +
                '<tr><th colspan="3" style="text-align:center" ><h3> Gold 22k 1gm rate  : ' + data.currency_symbol + ' ' + parseFloat($('#metal_rate').val()).toFixed(2) + '</h3></th></tr>' +
                // '<tr><td><h4><div style="float:left">Eligible:</div><div style="float:right">' + parseFloat(eligible_weight).toFixed(3) + ' g<input type="hidden" id="eligible_weight" value="' + parseFloat(eligible_weight).toFixed(3) + '" /></div></h4></td><td><h4><div style="float:left">Selected:</div><div style="float:right"><span id="sel_wt" >0.000</span> g</div><input type="hidden" id="selected_weight" name="generic[metal_weight]"  value="0"/></h3></td></tr>' +
                '<tr><td><h4><div style="float:left">Eligible:</div><div style="float:right">' + formatMetalWeight(eligible_weight) + ' g<input type="hidden" id="eligible_weight" value="' + formatMetalWeight(eligible_weight) + '" /></div></h4></td><td><h4><div style="float:left">Selected:</div><div style="float:right"><span id="sel_wt" >0.000</span> g</div><input type="text" id="selected_weight" name="generic[metal_weight]"  value="0"/></h3></td></tr>' +
                '<tr><th>Weight</th><th>Amount</th></tr>';
            $.each(data.weights, function () {
                //console.log(data.current_total_weight);
                if ((parseFloat(data.current_total_weight) + parseFloat(this.weight)) <= parseFloat(data.max_weight) && (parseFloat(data.current_total_weight) + parseFloat(this.weight)) >= parseFloat(data.min_weight)) {
                    weight_check += "<tr><td><input type='checkbox' name='weight_gold' value='" + this.weight + "' />	" + formatMetalWeight(this.weight) + " gram </td><td>  " + data.currency_symbol + " " + parseFloat(this.weight * $('#metal_rate').val()).toFixed(2) + " </td></tr>";
                }
            });
            weight_check += '<table></div>';
            $('#payment_container').html(weight_check);
            // esakki 17-09
            $("#proced").on("click", function (event) {
                $('#payment_modes').css("pointer-events", 'all');
                $('#payment_modes').css("opacity", '0.9');
            });
        }
        if (data.scheme_type == 2 || data.scheme_type == 3 && (data.flexible_sch_type == 2 && data.wgt_convert != 2)) {
            $("#amt_to_wgt").html("<span class='label label-success'>Yes</span>");
            var total_amt = (parseFloat($('#gst_type').val()) == 1 ? parseFloat($('#total_amt').val()) : parseFloat($('#total_amt').val()) - parseFloat(gst_amt));
            var metal_rate = parseFloat($('#metal_rate').val());
            if (total_amt != '' && metal_rate != '') {
                var weight = total_amt / metal_rate;
                // $("#amttowgt").html(parseFloat(weight.toFixed(3)) + ' ' + '<strong>' + 'gm' + '</strong>');
                 $("#amttowgt").html((formatMetalWeight(weight)) + ' ' + '<strong>' + 'gm' + '</strong>');
            }
        } else {
            $("#amt_to_wgt").html("<span class='label label-danger'>No</span>");
            $("#amttowgt").html("N/A");
        }
        if (data.scheme_type == 3 && data.flexible_sch_type == 3) {
            $("#amt_to_wgt").html("<span class='label label-success'>Yes</span>");
        }
        if (data.allow_preclose == 1) {
            $("#is_preclose_blk").show();
        }
        var id_scheme_account = $('#id_scheme_account').val();
        var url = base_url + 'index.php/reports/payment/account/' + id_scheme_account;
        $("#start_date").html(data.start_date);
        $("#acc_name").html(data.account_name);
        $("#scheme_code").html(data.code);
        $("#scheme_type").html((data.scheme_type == 0 ? 'Amount' : (data.scheme_type == 1 ? 'Weight' : data.scheme_type == 2 ? 'Amount to Weight' : (data.scheme_type == 3 ? (data.flexible_sch_type == 2 ? "Flexible Amount" : (data.flexible_sch_type == 3 ? "Flexible Weight" : "Flexible")) : ""))));
        $("#last_paid_date").html((data.last_paid_date != null ? data.last_paid_date : "-"));
        // $("#paid_installments").html("<span class='badge bg-green'><a style='color:white;' target='_blank' href='"+url+"'>"+data.paid_installments+"/"+data.total_installments+"</a></span>");
        if (data.show_ins_type == 1) {
            $("#paid_installments").html("<span class='badge bg-green'><a style='color:white;' target='_blank' href='" + url + "'>" + data.paid_installments + "/" + data.total_installments + "</a></span>");
        } else {
            $("#paid_installments").html("<span class='badge bg-green'><a style='color:white;' target='_blank' href='" + url + "'>" + data.paid_installments + "</a></span>");
        }
        //display weight for all weight schemes based on current metal rate and payment amount.
        var pay_amt = $('#total_amt').val();
        var cur_metal_rate = $('#metal_rate').val();
        var schemeType = data.scheme_type;
        var flex_schType = data.flexible_sch_type;
        // var pay_wgt = parseFloat(pay_amt / cur_metal_rate).toFixed(3);
         var pay_wgt = (formatMetalWeight(pay_amt / cur_metal_rate));
        if (schemeType == 1 || schemeType == 2 || schemeType == 3 && (flex_schType == 2 || flex_schType == 3 || flex_schType == 4 || flex_schType == 5 || flex_schType == 8)) {
            $('#payment_weight').val(pay_wgt);
        } else {
            $('#payment_weight_block').css('display', 'none');
        }
        //display weight for all weight schemes based on current metal rate and payment amount.
        $("#paid_ins").val(data.paid_installments);
        $("#fix_weight").val(data.scheme_type);
        $("#wgt_cvrt").val(data.wgt_convert);
        $("#is_flexible_wgt").val(data.is_flexible_wgt);
        $("#sch_amt").val(data.payable);
        $("#unpaid_dues").html((data.totalunpaid > 0 ? data.totalunpaid : 0));
        $("#due_type").val(data.due_type);
        $("#act_due_type").val(data.due_type);
        $("#allowed_dues").val(allowed_dues);
        $("#act_allowed_dues").val(data.allowed_dues);
        $("#total_pdc").html((data.cur_month_pdc > 0 ? data.cur_month_pdc + " / " : '') + data.cur_month_pdc);
        $("#preclose").html(data.preclose);
        $('#gst_percent').val(data.gst);
        $('#gst_type').val(data.gst_type);
        $('#ref_benifit_ins').val(data.ref_benifitadd_ins);
        $('#referal_code').val(data.referal_code);
        $('#ref_benifitadd_by').val(data.ref_benifitadd_ins_type);
        $("#paidinstall").val(data.paid_installments);
        $("#flexible_sch_type").val(data.flexible_sch_type);
        $('#agent_code').val(data.agent_code);
        $('#id_agent').val(data.id_agent);
        $('#id_scheme').val(data.id_scheme);
        $('#agent_refferal').val(data.agent_refferal);
        $('#emp_refferal').val(data.emp_refferal);
        $("#cus_refferal").val(data.cus_refferal); // 22-10
        $('#firstPayamt_as_payamt').val(data.firstPayamt_as_payamt);
        $('#firstPayamt_maxpayable').val(data.firstPayamt_maxpayable);
        $('#firstPayment_as_wgt').val(data.firstPayment_as_wgt);
        if (data.allow_pay == 'Y') {
            $("#btn-submit").css("display", "block");
            $('#proced').css("display", 'block');
        } else {
            $("#btn-submit").css("display", "none");
            $('#proced').css("display", 'none');
        }
        if ($('#amt_to_wgt span').text().trim() == 'No') {
            $('.is_weight').hide();
        } else {
            $('.is_weight').show();
        }
        return false;
    } else {
        clear_account_detail();
    }
}
$('#pay_save').on('change', function () {
    $('input[type=radio]').removeAttr('checked');
    $("#btn-submit label").addClass("active");
    // $("#btn-submit label").addClass("inactive");
});
$('#pay_print').on('change', function () {
    // $("#btn-submit label").addClass("inactive");
    $('input[type=radio]').removeAttr('checked');
    $("#btn-submit label").addClass("active");
});
//esakki
/* $("#proced").on( "click", function(event) 
                {
                     $('#payment_modes').css("pointer-events", 'all');
                     $('#payment_modes').css("opacity", '0.9');
                }); */
$('#pay_form').submit(function (e) {
    if ($('#scheme_type').html() == 'Weight') {
        var eligible_weight = parseFloat($('#eligible_weight').val()).toFixed(2);
        var selected_weight = parseFloat($('#selected_weight').val()).toFixed(2);
        if (parseFloat(selected_weight) > parseFloat(eligible_weight)) {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Your have selected weight more than eligible.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $('#error-msg').html(msg);
            return false;
        }
        if (parseFloat(selected_weight) == 0) {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please select at least one weight to proceed payment.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $('#error-msg').html(msg);
            return false;
        }
    }
    if ($('#pay_datetimepicker').val() == '') {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please select payment date.</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $('#error-msg').html(msg);
        return false;
    }
    if ($('#scheme_account').val() == null) {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select Scheme A/C No.</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $('#error-msg').html(msg);
        return false;
    }
    if (!$(".ischk_wallet_pay").is(":checked") && parseFloat($('.sum_of_amt').html()) == 0 || ($('.sum_of_amt').html()) == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Invalid payment amount.." });
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        //$("#btn-submit label").addClass("inactive");
        // $("#btn-submit label").removeClass("active");
        return false;
    }
    if (parseFloat($('#payment_amt').val()) > parseFloat($('.sum_of_amt').html()) && parseFloat($('.bal_amount').html()) != 0) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Received amount less than payment amount.." });
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        //$("#btn-submit label").removeClass("active");
        return false;
    }
    if (parseFloat($('.sum_of_amt').html()) > parseFloat($('#payment_amt').val())) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Received amount more than payment amount.." });
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        // $("#btn-submit label").removeClass("active");
        return false;
    }
    if ($('#payment_status').val() == null) {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select payment status.</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $('#error-msg').html(msg);
        return false;
    }
})
$('#allowed_dues').on('keyup change', function (e) {
    $("div.overlay").css("display", "block");
    var max = parseInt($('#act_allowed_dues').val());
    var min = 1;
    var amt = parseFloat($('#payamt').val());
    var discount_installment = $('#discount_installment').val();
    var discount_type = $('#discount_type').val();
    var discount = $('#discount').val();
    var paid_installments = $('#paidinstall').val();
    var firstPayDisc_value = $('#firstPayDisc_value').val();
    var discountedAmt = $('#discountedAmt').val();
    if (this.value == discount_installment) {
        var discountedAmt = firstPayDisc_value;
        $('#discountedAmt').val(discountedAmt);
    } else if (this.value <= discount_installment) {
        $('#discountedAmt').val('');
    }
    console.log(discountedAmt);
    if (parseInt(this.value) < min || isNaN(this.value) || this.value.length <= 0)
        this.value = min;
    else if (parseInt(this.value) > max)
        this.value = max;
    else this.value = this.value;
    total = parseFloat(parseFloat(amt) * parseFloat(this.value)).toFixed(2) - parseFloat((discount == 1 ? (discount_type == 0 ? firstPayDisc_value : ((discount_installment == (this.value) || discountedAmt != '') ? firstPayDisc_value : 0.00)) : 0.00));
    console.log(total);
    if ($('#is_flexible_wgt').val() == 0 && $('#scheme_type').text() == 'Weight') {
        if (parseFloat($('#selected_weight').val()) > 0) {
            $('#total_amt').val(total);
        }
    } else {
        $('#total_amt').val(total);
    }
    /* var can_redeem = 0;
    if ($(".ischk_wallet_pay").is(":checked") && parseFloat($(".wallet_balance").val()) > 0) {
        var allowed_redeem = ($('.redeem_percent').val() > 0 ? (total * (parseFloat($('.redeem_percent').val()) / 100)) : 0);
        wallet_balance = parseFloat($('.wallet_balance').val());
        if (allowed_redeem > wallet_balance) {
            can_redeem = wallet_balance;
        } else {
            can_redeem = allowed_redeem;
        }
    }
    console.log(parseFloat($('.ischk_wallet_pay').val()));
    $('.wallet').val(can_redeem);
    $('.redeem_request').val(can_redeem); */
    calc_walletbalance(); // esakki 31-07-2025
    //GST Calculation
    var gst_val = 0;
    var gst_amt = 0;
    var gst = 0;
    if (parseFloat($('#gst_percent').val()) > 0) {
        gst_val = parseFloat(amt) - (parseFloat(amt) * (100 / (100 + parseFloat($('#gst_percent').val()))));
        gst_amt = gst_val * parseFloat($('#allowed_dues').val());
        if (parseFloat($('#gst_type').val()) == 1) {
            gst = gst_amt;
        }
    }
    $('#gst_amt').val((gst_amt).toFixed(0)); // esakki 17-09
    $('#payment_amt').val((parseFloat(gst) + parseFloat(total)).toFixed(0)); // esakki 17-09
    if ($('#scheme_type').text() == 'Amount to Weight') {
        var total_amt = (parseFloat($('#gst_type').val()) == 1 ? parseFloat($('#total_amt').val()) : parseFloat($('#total_amt').val()) - parseFloat(gst_amt));
        var metal_rate = parseFloat($('#metal_rate').val());
        if (total_amt != '' && metal_rate != '') {
            var weight = total_amt / metal_rate;
            // $("#amttowgt").html(parseFloat(weight.toFixed(3)) + ' ' + '<strong>' + 'gm' + '</strong>');
              $("#amttowgt").html((formatMetalWeight(weight)) + ' ' + '<strong>' + 'gm' + '</strong>');
        }
    }
    $("div.overlay").css("display", "none");
});
$(document).on('change', '[type=checkbox][name=weight_gold]', function () {
    var selected_weight = 0.000;
    var eligible_weight = parseFloat($('#eligible_weight').val()).toFixed(2);
    var metal_rate = parseFloat($('#metal_rate').val()).toFixed(2);
    $("input[name=weight_gold]:checked").each(function () {
        console.log($(this).val());
        // selected_weight = parseFloat(parseFloat(selected_weight) + parseFloat($(this).val())).toFixed(3);
          selected_weight = ((selected_weight) + parseFloat(formatMetalWeight($(this).val())));
    });
    console.log(selected_weight);
    $('#selected_weight').val(selected_weight);
    $('#payment_weight').val(selected_weight);		// esakki 17-09
    // $('#sel_wt').html(parseFloat(selected_weight).toFixed(3));
    $('#sel_wt').html((formatMetalWeight(selected_weight)));
    var tot_amt = Math.round(parseFloat(selected_weight) * parseFloat(metal_rate) * parseFloat($('#sel_due').val()));
    $('#total_amt').val(parseFloat(tot_amt).toFixed(2));
    if ($('#total_amt').val() > 0) {
        $('#proced').css("display", 'block');
    } else {
        $('#proced').css("display", 'none');
    }
    /* var can_redeem = 0;
    if ($(".ischk_wallet_pay").is(":checked") && parseFloat($(".wallet_balance").val()) > 0) {
        var allowed_redeem = ($('.redeem_percent').val() > 0 ? (tot_amt * (parseFloat($('.redeem_percent').val()) / 100)) : 0);
        wallet_balance = parseFloat($('.wallet_balance').val());
        if (allowed_redeem > wallet_balance) {
            can_redeem = wallet_balance;
        } else {
            can_redeem = allowed_redeem;
        }
    }
    $('.wallet').val(can_redeem);
    $('.redeem_request').val(can_redeem); */
    calc_walletbalance(); // esakki 31-07-2025
    //GST Calculation
    var gst_val = 0;
    var gst_amt = 0;
    var gst = 0;
    if (parseFloat($('#gst_percent').val()) > 0) {
        gst_val = parseFloat(tot_amt) - (parseFloat(tot_amt) * (100 / (100 + parseFloat($('#gst_percent').val()))));
        if (parseFloat($('#gst_type').val()) == 1) {
            gst_val = parseFloat(tot_amt) * parseFloat($('#gst_percent').val()) / 100;
        }
        if (parseFloat($('#gst_type').val()) == 0) {
            gst_val = (parseFloat(tot_amt) * ((100 + parseFloat($('#gst_percent').val())) / 100)) - parseFloat(tot_amt); //esakki 19-09
        }
        gst_amt = gst_val * parseFloat($('#allowed_dues').val());
    }
    $('#gst_amt').val((gst_amt).toFixed(0)); // esakki 17-09
    var pay_amount = parseFloat(gst_val) + parseFloat(tot_amt);
    $('#payment_amt').val(parseInt(pay_amount));
    calculatePaymentCost();
});
function sumSelected(ele, eligible) {
    var id = $(ele).attr('id');
    var metal_rate = parseFloat($('#metal_rate').val());
    var idno = id.split('t');
    var spanid = '#total_amt' + idno[1];
    var amtid = '#amount' + idno[1];
    var wtid = '#metal_wt' + idno[1];
    var sum = 0;
    $('#' + id + ' :selected').each(function () {
        if ($(this).val() <= eligible) {
            sum += Number($(this).val());
        }
    });
    if (sum <= eligible) {
        total = parseFloat(sum * metal_rate).toFixed(2);
        console.log(sum * metal_rate);
        $(spanid).html(total);
        $(amtid).val(parseFloat(total).toFixed(2));
        // $(wtid).val(parseFloat(sum).toFixed(3));
        $(wtid).val((formatMetalWeight(sum)));
    }
    $('#grand_total').html(parseFloat(sum_by_class('payment_amount')).toFixed(2));
    // $('#grand_weight').html(parseFloat(sum_by_class('payment_weight')).toFixed(3));
    $('#grand_weight').html(formatMetalWeight(parseFloat(sum_by_class('payment_weight'))));
}
$('#adjust_unpaid').change(function () {
    if ($(this).is(':checked')) {
        $('#no_of_unpaids').prop('disabled', false);
    } else {
        $('#no_of_unpaids').prop('disabled', true);
    }
});
function sum_by_class(classname) {
    var sum = 0;
    $('.' + classname).each(function () {
        sum += parseFloat($(this).val());
    });
    return sum;
}
//to calculate weight
function calculate_total() {
    var schID = $("#id_scheme_account").val();
    if (schID != '') {
        $("#payment_amount").val(0);
        if ($("#scheme_type").html() == 'Weight') {
            var eligibleQty = isNaN($("#eligible_qty").html()) || $("#eligible_qty").html() == '' ? 0 : $("#eligible_qty").html();
            var weight = isNaN($("#weight").val()) || $("#weight").val() == '' ? 0 : $("#weight").val();
            if (parseFloat(weight) <= parseFloat(eligibleQty)) {
                totalAmt = parseFloat($("#weight").val()) * parseFloat($("#metal_rate").val());
                $("#payment_amount").val(parseFloat(isNaN(totalAmt) ? 0.00 : totalAmt).toFixed(2));
            } else {
                $("#payment_amount").val(0);
                $("#weight").val(0);
            }
        }
    }
}
function sumColumn(selector, column) {
    var sum = 0;
    $("#" + selector + " > tbody > tr").each(function () {
        var row = $(this);
        value = row.find('td:eq(' + column + ')').html();
        console.log(value);
        // add only if the value is number
        if (!isNaN(value) && value.length != 0) {
            sum += parseFloat(value);
        }
    });
    return sum;
}
function getselected_data() {
    var sum = 0;
    $("#rep_post_payment_list > tbody > tr ").each(function () {
        var row = $(this);
        value = row.find('td:eq(9)').html();
        // add only if the value is number
        if (!isNaN(value) && value.length != 0) {
            sum += parseFloat(value);
        }
        $('#ftotal').html(parseFloat(sum).toFixed(2));
    });
}
$("input[name='type']:checkbox").change(function () {
    if ($('#scheme_type').html() == 'Weight') {
        //	$('#btn-submit').load(path +  ' #btn-submit');
        var eligible_weight = parseFloat($('#eligible_weight').val()).toFixed(2);
        var selected_weight = parseFloat($('#selected_weight').val()).toFixed(2);
        if (parseFloat(selected_weight) > parseFloat(eligible_weight)) {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Your have selected weight more than eligible.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $('#error-msg').html(msg);
            return false;
        }
        if (parseFloat(selected_weight) == 0 || selected_weight == 'NaN' || $('#total_amt').val() == 0.00) {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please select at least one weight to proceed payment.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $('#error-msg').html(msg);
            return false;
        }
    }
    if ($('#pay_datetimepicker').val() == '') {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please select payment date.</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $('#error-msg').html(msg);
        return false;
    }
    if ($('#scheme_account').val() == null) {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select Scheme A/C No.</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $('#error-msg').html(msg);
        return false;
    }
    if (!$(".ischk_wallet_pay").is(":checked") && parseFloat($('.sum_of_amt').html()) == 0 || ($('.sum_of_amt').html()) == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Invalid payment amount.." });
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        // $("#btn-submit label").removeClass("active");
        // $("#btn-submit label").addClass("inactive");
        return false;
    }
    if (parseFloat($('#payment_amt').val()) > parseFloat($('.sum_of_amt').html()) && parseFloat($('.bal_amount').html()) != 0) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Received amount less than payment amount.." });
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        //$("#btn-submit label").removeClass("active");
        return false;
    }
    if (parseFloat($('.sum_of_amt').html()) > parseFloat($('#payment_amt').val())) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Received amount more than payment amount.." });
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        //$("#btn-submit label").removeClass("active");
        return false;
    }
    if ($('#payment_status').val() == null) {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select payment status.</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $('#error-msg').html(msg);
        return false;
    }
    if ($('#branch_settings').val() == 1) {
        if ($('#id_branch').val() == '') {
            msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select branch.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $('#error-msg').html(msg);
            return false;
        }
    }
    if ($('#id_employee').val() == '') {
        msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select Employee.</div>';
        $("div.overlay").css("display", "none");
        $('#error-msg').html(msg);
        return false;
    }
    // esakki 09-07-2025 - Backdate entry
    if ($("#enable_editing").is(":checked") && ($('#edit_metalrate_date').val() == '' || !$('#metalrate_edit_date').val())) {
        if ($('#edit_metalrate_date').val() == '') {
            msg =
                '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select metal rate date.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $("#error-msg").html(msg);
            return false;
        }
        if (!$('#metalrate_edit_date').val()) {
            msg =
                '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select metal rate.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $("#error-msg").html(msg);
            return false;
        }
    }
    // esakki 04-10
    if (
        $("#sch_type").val() == 1 ||
        ($("#sch_type").val() == 3 && $("#flexible_sch_type").val() == 8)
    ) {
        var gst = isValid($("#gst_amt").val()) ? $("#gst_amt").val() : 0; // esakki 04-12
        var amt = parseFloat($("#total_amt").val()) + parseFloat(gst);
        if (amt != $("#payment_amt").val()) {
            msg =
                '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please click proced. Received amount and Payment amount is not tallied.</div>';
            $("div.overlay").css("display", "none");
            //stop the form from submitting
            $("#error-msg").html(msg);
            return false;
        }
    } else {
        if ($("#gst_type").val() == 1) {
            var gst = isValid($("#gst_amt").val()) ? $("#gst_amt").val() : 0; // esakki 04-12
            var amt = parseFloat($("#total_amt").val()) + parseFloat(gst);
            if (amt != $("#payment_amt").val()) {
                msg =
                    '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please click proced. Received amount and Payment amount is not tallied.</div>';
                $("div.overlay").css("display", "none");
                //stop the form from submitting
                $("#error-msg").html(msg);
                return false;
            }
        } else {
            if (parseFloat($('.redeem_request').val()) > 0) {
                paymentAmt = parseFloat($("#payment_amt").val()) + parseFloat($('.redeem_request').val());
                if (parseFloat($("#total_amt").val()) != paymentAmt) {
                    msg =
                        '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please click proced. Received amount and Payment amount is not tallied.</div>';
                    $("div.overlay").css("display", "none");
                    //stop the form from submitting
                    $("#error-msg").html(msg);
                    return false;
                }
            } else {
                if (parseFloat($("#total_amt").val()) != parseFloat($("#payment_amt").val())) {
                    msg =
                        '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please click proced. Received amount and Payment amount is not tallied.</div>';
                    $("div.overlay").css("display", "none");
                    //stop the form from submitting
                    $("#error-msg").html(msg);
                    return false;
                }
            }
        }
    }
    /*	if($('#make_pay_cash').val() > 0 && ($('#cash_paymts').val() + $('#total_amt').val()) > $('#disable_pay_amt').val())
        {
          msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Your have reached for CASH payments. Proceed with other modes</div>';
        $("div.overlay").css("display", "none"); 
                //stop the form from submitting
                 $('#error-msg').html(msg);
        return false;
        } */
    var form_data = $('#pay_form').serialize();
    $("#btn-submit").css("pointer-events", 'none');
    $('#btn-submit').css("opacity", '0.3');
    insert_payment(form_data);
});
function insert_payment(post_data) {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    // $("#btn-submit").prop("disabled", true); 
    //$("#btn-submit").prop("disabled", true); 
    $("#btn-submit").css("pointer-events", 'none');
    $('#btn-submit').css("opacity", '0.3');
    var id_customer = $("#id_customer").val();
    if ($('#isOTPRegForPayment').val() == 1) {
        $.ajax({
            url: base_url + "index.php/admin_payment/generateotp?nocache=" + my_Date.getUTCSeconds(),
            data: { 'id_customer': id_customer },
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if (data.result == 3) {
                    $('#otp_modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $("div.overlay").css("display", "none");
                }
            }
        });
    } else {
        payment_success(post_data);
    }
}
function update_otp(post_data) {
    var post_otp = $('#otp').val();
    $.ajax({
        url: base_url + "index.php/admin_payment/update_otp",
        data: { 'otp': post_otp },
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            if (data.result == 1) {
                payment_success(post_data, post_otp);
            } else {
                $("#resendotp").attr("disabled", false);
                $("#verify_otp").attr("disabled", false);
                $('#otp').val('');
                alert(data.msg);
            }
        }
    });
}
function payment_success(post_data, post_otp = "") //tkv code 
{
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $("#pay_print").attr("disabled", true);
    $("#pay_save").attr("disabled", true);
    $("#verify_otp").attr("disabled", true);
    var id_scheme_account = $("#id_scheme_account").val();
    //TKV double bonus scheme..	
    if ($('#due_type').val() == 'GEN_ADV') {
        var form_save_url = base_url + "index.php/admin_payment/payment/general_advance?nocache=" + my_Date.getUTCSeconds();
        //	var receipt_print_url = base_url+'index.php/admin_payment/generateAdvanceInvoice/'+value.id_payment+'/'+value.id_scheme_account;
        var redirect_listing_url = base_url + 'index.php/reports/general_advance';
    } else {
        var form_save_url = base_url + "index.php/payment/save_all?nocache=" + my_Date.getUTCSeconds();
        //	var receipt_print_url = base_url+'index.php/admin_manage/passbook_print/PAY/'+id_scheme_account+'/'+value;
        var redirect_listing_url = base_url + 'index.php/payment/list';
    }
    //tkv	
    $.ajax({
        url: form_save_url, // TKV double bonus scheme
        data: post_data,
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            $("#pay_print").attr("disabled", 'disabled');
            $("#pay_save").attr("disabled", 'disabled');
            if (data.payment_status == 1) {
                if (data.type == 1 && data.payment_status == 1 && data.is_topup_scheme == 1) {
                    $.each(data.payid, function (index, value) {
                        window.open(base_url + 'index.php/admin_payment/generateInvoice/' + value + '/' + data.id_scheme_account, '_blank');
                    });
                }
                if (data.type == 1 && data.payment_status == 1 && data.is_topup_scheme == 0) {
                    $.each(data.payid, function (index, value) {
                        if ($('#otp_price_fixing').val() == 1) {
                            window.open(base_url + 'index.php/admin_manage/get_scheme_receipt/' + id_scheme_account, '_blank');
                        }
                        //TKV double bonus scheme...
                        if ($('#due_type').val() == 'GEN_ADV') {
                            window.open(base_url + 'index.php/admin_payment/generateAdvanceInvoice/' + value.id_payment + '/' + value.id_scheme_account, '_blank');
                        } else {
                            //window.open(base_url + 'index.php/admin_manage/passbook_print/PAY/' + id_scheme_account + '/' + value, '_blank');
                            window.open(base_url + 'index.php/admin_payment/generateInvoice/' + value + '/' + data.id_scheme_account, '_blank');
                        }
                    });
                }
                //else if (data.type == 2 && data.payment_status == 1) {
                window.location.href = redirect_listing_url; //tkv
                $.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + 'Payment Success' });
                //}
            } else {
                $("div.overlay").css("display", "none");
                window.location.href = redirect_listing_url; //tkv
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Unable to proceed your request' });
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("#pay_print").attr("disabled", false);
            $("#pay_save").attr("disabled", false);
            $("div.overlay").css("display", "none");
        }
    });
}
$("input[name='type1']:radio").change(function () {
    var edit_data = $('#payment_form').serialize();
    update_payment(edit_data);
});
function update_payment(post_data) {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    var id = $('#payment_id').val();
    $.ajax({
        url: base_url + 'index.php/payment/update/' + id + '?nocache=' + my_Date.getUTCSeconds(),
        data: post_data,
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            console.log(data);
            if (data.type1 == 1 && data.payment_status == 1) {
                $.each(data.paymentid, function (index, value) {
                    window.open(base_url + 'index.php/admin_payment/generateInvoice/' + value + '/' + data.id_scheme_account, '_blank');
                });
                $("div.overlay").css("display", "none");
                window.location.href = base_url + 'index.php/payment/list';
            } else {
                $("div.overlay").css("display", "none");
                window.location.href = base_url + 'index.php/payment/list';
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
}
function transaction_detail(id) {
    $('.trans-det').html(transactionData(id));
    $('#pay_detail').modal('show', { backdrop: 'static' });
}
function transactionData(id) {
    var transaction = "";
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + "index.php/online/get/ajax_payment/" + id,
        dataType: "JSON",
        type: "POST",
        async: false,
        success: function (data) {
            payment = data;
            console.log(payment);
            var gst = (payment.gst > 0 ? payment.currency_symbol + ' ' + (payment.gst_type == 1 ? (payment.payment_amount * (payment.gst / 100)) : Math.round(parseFloat(payment.payment_amount) - (parseFloat(payment.payment_amount) * (100 / (100 + parseFloat(payment.gst)))))) + ' ' + (payment.gst_type == 0 ? "(Amount inclusive of GST)" : "(Amount exclusive of GST)") : '0.00');
            var discount = payment.discount > 0 ? "<tr ><th>Discount</th><td>" + payment.discount + "</td></tr></tr>" : '';
            transaction = "<table class='table table-bordered trans'><tr><th>Account Name</th><td>" + data.account_name + "</td></tr><tr><th>Mobile</th><td>" + data.mobile + "</td></tr><tr><th>Account No.</th><td>" + data.scheme_acc_number + "</td></tr><tr><th>Date</th><td>" + payment.date_payment + "</td></tr><tr><th>Status</th><td>" + payment.payment_status + "</td></tr><tr><th>Receipt No</th><td>" + data.receipt_no + "</td></tr><tr><th>Transaction ID</th><td>" + payment.trans_id + "</td></tr><tr><th>PayU ID</th><td>" + payment.payu_id + "</td></tr><tr><th>Mode</th><td>" + payment.payment_mode + "</td></tr><tr><th>Bank</th><td>" + payment.bank_name + "</td></tr><tr><th>Card No</th><td>" + payment.card_no + "</td></tr><tr><th>Paid Amount</th><td> " + payment.currency_symbol + "  " + (payment.no_of_dues > 1 ? payment.act_amount : payment.payment_amount) + ' + Charge : ' + payment.currency_symbol + ' ' + payment.bank_charges + "</td></tr><tr ><th>GST</th><td>" + (gst) + "</td></tr></tr>" + discount + "<tr ><th>Remark</th><td><span class='label bg-yellow'>" + payment.remark + "</span></td></tr></table>"
            //transaction  = "<table class='table table-bordered trans'><tr><th>Account Name</th><td>"+data.account_name+"</td></tr><tr><th>Mobile</th><td>"+data.mobile+"</td></tr><tr><th>Account No.</th><td>"+scheme_acc_number+"</td></tr><tr><th>Date</th><td>"+payment.date_payment+"</td></tr><tr><th>Status</th><td>"+payment.payment_status+"</td></tr><tr><th>Receipt No</th><td>"+receipt_no+"</td></tr><tr><th>Transaction ID</th><td>"+payment.trans_id+"</td></tr><tr><th>PayU ID</th><td>"+payment.payu_id+"</td></tr><tr><th>Mode</th><td>"+payment.payment_mode+"</td></tr><tr><th>Bank</th><td>"+payment.bank_name+"</td></tr><tr><th>Card No</th><td>"+payment.card_no+"</td></tr><tr><th>Paid Amount</th><td> "+payment.currency_symbol+"  "+(payment.no_of_dues>1?payment.act_amount:payment.payment_amount)+' + Charge : '+payment.currency_symbol+' '+payment.bank_charges+"</td></tr><tr ><th>GST</th><td>"+(gst)+"</td></tr></tr>"+discount+"<tr ><th>Remark</th><td><span class='label bg-yellow'>"+payment.remark+"</span></td></tr></table>"
            return transaction;
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        }
    });
    $("div.overlay").css("display", "none");
    return transaction;
}
//branch select/////pay_list/hh
$('#branch_select').select2().on("change", function (e) {
    switch (ctrl_page[1]) {
        case 'list':
            if (this.value != '') {
                var from_date = $('#payment_list1').text();
                var to_date = $('#payment_list2').text();
                var id_employee = $('#id_employee').text();
                var id_status = $('#id_status').text();
                var id_customer = $('#customer_id').val();
                var id = $(this).val();
                get_payment_list(from_date, to_date, id, id_employee, id_status, id_customer);
            }
            break;
    }
});
//scheme_name
function get_scheme() {
    $(".overlay").css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_scheme/ajax_get_schemes',
        dataType: 'json',
        success: function (data) {
            var id_scheme = $('#id_scheme').val();
            $.each(data, function (key, item) {
                $('#scheme_select').append(
                    $("<option></option>")
                        .attr("value", item.id_scheme)
                        .text(item.name)
                );
            });
            $("#scheme_select").select2({
                placeholder: "Select scheme name",
                allowClear: true
            });
            $("#scheme_select").select2("val", (id_scheme != '' ? id_scheme : ''));
            $(".overlay").css("display", "none");
        }
    });
}
$('#scheme_select').select2().on("change", function (e) {
    if (this.value != '') {
        $("#id_scheme").val(this.value);
        var id_scheme = $("#id_scheme").val();
        //load_schemeno_select(id_scheme);
        //$('#id_scheme_account').val('');
        //$('#scheme_account').empty();
    } else {
        $("#id_scheme").val('');
    }
});
// get_payment data Using Trans Id//HH
$('#trans_submit').on('click', function () {
    //alert(1);
    var ref_trans_id = $('#transid').val();
    // var id_branch=$('#id_branch').val();
    get_payments_data_list(ref_trans_id);
});
function get_payments_data_list(ref_trans_id) {
    $("div.overlay").css("display", "block");
    $('body').addClass("sidebar-collapse");
    my_Date = new Date();
    var oTable = $('#payments_data_list').DataTable();
    oTable.clear().draw();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/payment/payments_data_list',
        data: { 'ref_trans_id': ref_trans_id },
        dataType: 'json',
        success: function (data) {
            $("div.overlay").css("display", "none");
            oTable = $('#payments_data_list').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                // 'searchable':false,
                "bSort": true,
                "aaSorting": [
                    [0, "desc"]
                ],
                "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'all' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'all' } }] },
                "aaData": data,
                "order": [
                    [0, "desc"]
                ],
                "aoColumns": [{ "mDataProp": "id_payment" },
                {
                    "mDataProp": function (row, type, val, meta) { return row.id_transaction + '<br>B.Ref No &nbsp;:&nbsp; ' + row.payment_ref_number + ' <br>Act Amt &nbsp; :&nbsp; ' + row.act_amount; }
                },
                { "mDataProp": "payment_type" },
                { "mDataProp": "date_payment" },
                { "mDataProp": "name" },
                { "mDataProp": "account_name" },
                {
                    "mDataProp": function (row, type, val, meta) {
                        if (row.has_lucky_draw == 1) {
                            return row.scheme_group_code + ' ' + row.scheme_acc_number;
                        } else {
                            return row.code + ' ' + row.scheme_acc_number;
                        }
                    }
                },
                { "mDataProp": "id_branch" },
                {
                    "mDataProp": function (row, type, val, meta) { return 'MR: ' + row.metal_rate + '<br> Wgt: ' + row.metal_weight + 'g' + '<br> Amt: INR' + row.payment_amount; }
                },
                { "mDataProp": "receipt_no" },
                {
                    "mDataProp": function (row, type, val, meta) { return "<span class='label bg-" + row.status_color + "-active'>" + row.payment_status + "</span>"; }
                },
                { "mDataProp": "remark" },
                { "mDataProp": "last_update" },
                ],
                /*	"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
           if(aData['payment_type']=='Payu Checkout'){	 
           switch(aData['due_type'])
{
case 'A':
 if(aData['id_status']==2 || aData['id_status']==7)
$(nRow).css('color', '#e71847');
break;
case 'P':
if(aData['id_status']==2 || aData['id_status']==7)
$(nRow).css('color', '#e71847');
break;
}
}
}*/
            });
        }
    });
}
// get_payment data //  
$(document).on('click', '.incre_due', function (e) {
    var incre_due_id = ($(this).val());
    var sel_due = $('#sel_due').val();
    var allowed_dues = $('#max_dues').val();
    var sel_value = parseFloat(sel_due) + parseFloat(1);
    var discount_installment = $('#discount_installment').val();
    var discount_type = $('#discount_type').val();
    var discount = $('#discount').val();
    var paid_installments = $('#paidinstall').val();
    var firstPayDisc_value = $('#firstPayDisc_value').val();
    var payable = $('#pay').val();
    if (sel_value > allowed_dues) {
        $('#sel_due').val(1);
        $('#discountedAmt').val(0.00);
    } else {
        $('#sel_due').val(sel_value);
    }
    if (discount_type == 0) {
        $('#discountedAmt').val(firstPayDisc_value);
    } else if (discount_installment == $('#sel_due').val()) {
        $('#discountedAmt').val(firstPayDisc_value);
    }
    var payable_amt = ($('#sch_type').val() == 1 || $('#flexible_sch_type').val() == 4 ? payable * $('#metal_rate').val() : parseFloat(payable).toFixed(2));
    var amount = payable_amt * parseFloat($('#sel_due').val()).toFixed(2) - ($('#discountedAmt').val() != '' ? parseFloat($('#discountedAmt').val()) : 0.00);
    console.log(payable_amt + '--' + payable + '--' + $('#sch_type').val() + '--' + $('#metal_rate').val());
    $('#total_amt').val(amount);
    $('#payment_amt').val(amount);
    calculate_payAmt(payable_amt);
    calculatePaymentCost();
});
$(document).on('click', '.dec_due', function (e) {
    var incre_due_id = ($(this).val());
    var sel_due = $('#sel_due').val();
    var allowed_dues = $('#max_dues').val();
    var sel_value = parseFloat(sel_due) - parseFloat(1);
    var discount_installment = $('#discount_installment').val();
    var discount_type = $('#discount_type').val();
    var discount = $('#discount').val();
    var paid_installments = $('#paidinstall').val();
    var firstPayDisc_value = $('#firstPayDisc_value').val();
    var payable = $('#pay').val();
    $('#discountedAmt').val(0.00);
    if (sel_value > 0) {
        $('#sel_due').val(sel_value);
        $('#discountedAmt').val(0.00);
    } else {
        $('#sel_due').val(1);
    }
    if (discount_type == 0) {
        $('#discountedAmt').val(firstPayDisc_value);
    } else if (discount_installment == $('#sel_due').val()) {
        $('#discountedAmt').val(firstPayDisc_value);
    }
    var payable_amt = ($('#sch_type').val() == 1 || $('#flexible_sch_type').val() == 4 ? payable * $('#metal_rate').val() : parseFloat(payable).toFixed(2));
    var amount = payable_amt * parseFloat($('#sel_due').val()).toFixed(2) - ($('#discountedAmt').val() != '' ? parseFloat($('#discountedAmt').val()) : 0.00);
    console.log(payable_amt + '--' + payable + '--' + $('#sch_type').val() + '--' + $('#metal_rate').val());
    $('#total_amt').val(amount);
    $('#payment_amt').val(amount);
    calculate_payAmt(payable_amt);
    calculatePaymentCost();
});
function get_employee_list() {
    $(".overlay").css('display', 'block');
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_employee/get_employee',
        dataType: 'json',
        success: function (data) {
            var id_employee = $('#id_employee').val();
            $.each(data, function (key, item) {
                $('#employee_select').append(
                    $("<option></option>")
                        .attr("value", item.id_employee)
                        .text(item.firstname)
                );
            });
            $("#employee_select").select2({
                placeholder: "Select Employee name",
                allowClear: true
            });
            $("#employee_select").select2("val", ($('#id_employee').val() != null ? $('#id_employee').val() : ''));
            var selectid = $('#id_employee').val();
            if (selectid != null && selectid > 0) {
                $('#id_employee').val(selectid);
                $('.overlay').css('display', 'block');
            }
        }
    });
    $(".overlay").css('display', 'none');
}
$('#employee_select').select2().on("change", function (e) {
    if (this.value != '') {
        $('#id_employee').val(this.value);
        var from_date = $('#payment_list1').text();
        var to_date = $('#payment_list2').text();
        var id_branch = $('#id_branch').text();
        var id_employee = $(this).val();
        var id_status = $('#id_status').text();
        var id_customer = $('#customer_id').val();
        if (ctrl_page[0] == 'payment' && ctrl_page[1] == 'add') { } else {
            get_payment_list(from_date, to_date, id_branch, id_employee, id_status, id_customer);
        }
    }
});
$('#date_Select').on('change', function (e) {
    if (this.value != '') {
        $('#id_type').val(this.value);
    } else {
        $('#id_type').val('');
    }
});
//offline date insert manual
function load_paystatus_select() {
    my_Date = new Date();
    //show spinner
    $('.overlay').css('display', 'block');
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/payment/get/ajax_data?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (data) {
            if ($('#pay_mode').length) {
                $.each(data.mode, function (key, mode) {
                    $('#pay_mode').append(
                        $("<option></option>")
                            .attr("value", mode.short_code)
                            .text(mode.mode_name)
                    );
                });
                if (data.mode.length == 0) {
                    var payment_mode = '';
                }
                $("#pay_mode").select2({
                    placeholder: "Select payment mode",
                    allowClear: true
                });
                $("#pay_mode").select2("val", payment_mode);
            }
            //  	  if($('#payment_status').length)
            //   {
            //   	$.each(data.payment_status, function (key, pay) {
            //    	$('#payment_status').append(
            // $("<option></option>")
            //   .attr("value", pay.id_status_msg)
            //   .text(pay.payment_status)
            // );
            // });
            // //lines added by Durga 08.05.2023 for default payment status starts here 
            // 	if(data.payment_status.length == 0)
            // 	{
            // 	    var payment_status = '';
            //     }
            // 	else
            //     {
            // 	    var payment_status = data.payment_status[0].id_status_msg;	
            //     }
            // //lines added by Durga 08.05.2023 for default payment status ends here 
            // $('#pay_status').val(payment_status); 
            // $("#payment_status").select2({
            //     placeholder: "Select payment status",
            //     allowClear: true
            // });
            // $("#payment_status").select2("val", ($('#pay_status').val()!=null?$('#pay_status').val():''));
            //   }
            $('#pay_status').val(1);
            //get rate from api
            get_rate();
            //disable spinner
            $('.overlay').css('display', 'none');
        },
        error: function (error) {
            console.log(error);
            //disable spinner
            $('.overlay').css('display', 'none');
        }
    });
}
//offline date insert manual
//Chit Deposit
/*  $('#cash_deposit').on('change',function(){
      if($(this).is(":checked"))
      {
          $(this).val(1);
          $('#total_amt').prop('disabled',false);
          $('#total_amt').prop('disabled',false);
      }
      else
      {
          $(this).val(0);
          $('#total_amt').prop('disabled',true);
          $('#total_amt').prop('disabled',true);
          $('#total_amt').val(0);
          $("#proced").trigger('click');
      }
  });*/
$('#cash_deposit').on('click', function () {
    $('#total_amt').prop('disabled', false);
    $('#total_amt').prop('disabled', false);
});
$('#search_est_no').on('click', function () {
    if ($('#filter_est_no').val() == '') {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Est No' });
    } else {
        if ($('#branch_settings').val() == 1) {
            if ($('#id_branch').val() == '') {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Branch' });
            } else {
                get_EstimationDetails();
            }
        } else {
            get_EstimationDetails();
        }
    }
});
function get_EstimationDetails() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_payment/get_EstimationDetails/ajax?nocache=" + my_Date.getUTCSeconds(),
        data: { 'est_no': $('#filter_est_no').val(), 'id_branch': $('#id_branch').val() },
        type: "POST",
        dataType: "JSON",
        cache: false,
        success: function (data) {
            if (data.status) {
                var trHtml = '';
                var rowExist = false;
                $('#est_list > tbody tr').each(function (bidx, brow) {
                    var curRow = $(this);
                    if (curRow.find('.esti_no').val() == $('#filter_est_no').val()) {
                        rowExist = true;
                        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Estimation Already Exists.' });
                    }
                });
                if (!rowExist) {
                    trHtml = '<tr>' +
                        '<td><input type="hidden" class="estimation_id" name="estimation[estimation_id][]" value="' + data.estimation_id + '"><input type="hidden" class="esti_no" value="' + data.esti_no + '">' + data.esti_no + '<?td>' +
                        '<td><input type="hidden" class="total_amt" name="estimation[est_amount][]" value="' + data.total_amt + '">' + data.total_amt + '</td>' +
                        '<td><input type="hidden" class="total_weight" name="estimation[est_weight][]" value="' + data.total_weight + '">' + data.total_weight + '</td>' +
                        '<td><a href="#"  onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
                        '</tr>';
                    $('#est_list tbody').append(trHtml);
                    calculate_old_metal();
                }
                $('#filter_est_no').val('');
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
                $('#filter_est_no').val('');
            }
        }
    });
}
function remove_row(curRow) {
    curRow.remove();
    calculate_old_metal();
    $("#proced").trigger('click');
}
function calculate_old_metal() {
    var est_amt = 0;
    var est_weight = 0;
    $('#est_list > tbody  > tr').each(function (index, tr) {
        est_amt += parseFloat($(this).find('.total_amt').val());
        est_weight += parseFloat($(this).find('.total_weight').val());
    });
    $('.tot_est_amt').html(parseFloat(est_amt).toFixed(2));
    $('#total_est_amt').val(parseFloat(est_amt).toFixed(2));
    /* $('.tot_est_weight').html(parseFloat(est_weight).toFixed(3)); 
    $('#total_est_wgt').val(parseFloat(est_weight).toFixed(3)); */
     $('.tot_est_weight').html((formatMetalWeight(est_weight)));
    $('#total_est_wgt').val((formatMetalWeight(est_weight)));
}
/*$('.received_amt').on('change',function(){
    if((parseInt($('.received_amt').val())>=parseInt($('#min_pan_amt').val())) && ($('#min_pan_amt').val()!=0))
    {
        $('#total_amt').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Cash Amount Minimum of Rs."+$('#min_pan_amt').val()});
    }
});*/
//Chit Deposit
function calculate_payAmt(instalment_amt) {
    var gst_percent = parseFloat($('#gst_percent').val());
    var gold_metal_rate = parseFloat($('#metal_rate').val());
    var gst = 0;
    var gst_type = parseFloat($('#gst_type').val());
    var sel_dues = parseFloat($('#sel_due').val());
    var discount = parseFloat($('#discountedAmt').val());
    var metal_weight = 0;
    var insAmt_withoutDisc = instalment_amt - discount;
    var gst_amt = 0;
    if (gst_percent > 0) {
        if (gst_type == 0) {
            console.log("gst_percent : " + gst_percent);
            console.log("sel_dues : " + sel_dues);
            // Inclusive
            var gst_removed_amt = insAmt_withoutDisc * 100 / (100 + gst_percent);
            gst_amt = insAmt_withoutDisc - gst_removed_amt;
            // Set Value
            if ($("#flexible_sch_type").val() == 2 || $("#sch_type").val() == 2) {
                metal_weight = (gst_removed_amt + discount) / gold_metal_rate;
            } else if ($("#sch_type").val() == 1) {
                metal_weight = $('#selected_weight').val();
            }
            /*$('#payment_weight').val(metal_weight*sel_dues);
            $('#gst_amt').val(gst_amt*sel_dues); 
            $('#payment_amt').val(insAmt_withoutDisc*sel_dues); */
            var metal_weight = setMetalWgt(metal_weight);
            console.log({ "gst_removed_amt": gst_removed_amt, "gst_amt": gst_amt, "metal_weight": metal_weight });
            return { "payment_amt": insAmt_withoutDisc, "gst_removed_amt": gst_removed_amt, "gst_amt": gst_amt, "metal_weight": metal_weight };
        } else if (gst_type == 1) {
            // Exclusive
            var amt_with_gst = insAmt_withoutDisc * ((100 + gst_percent) / 100);
            gst_amt = amt_with_gst - insAmt_withoutDisc;
            // Set Value
            if ($("#flexible_sch_type").val() == 2 || $("#sch_type").val() == 2) {
                metal_weight = instalment_amt / gold_metal_rate;
            } else if ($("#sch_type").val() == 1) {
                metal_weight = $('#selected_weight').val();
            }
            /*	$('#payment_weight').val(metal_weight*sel_dues);
                $('#gst_amt').val(gst_amt*sel_dues); 
                $('#payment_amt').val(amt_with_gst*sel_dues); */
            var metal_weight = setMetalWgt(metal_weight);
            console.log({ "amt_with_gst": amt_with_gst, "gst_amt": gst_amt, "metal_weight": metal_weight });
            return { "payment_amt": amt_with_gst, "amt_with_gst": amt_with_gst, "gst_amt": gst_amt, "metal_weight": metal_weight };
        }
    } else {
        if ($("#flexible_sch_type").val() == 2 || $("#sch_type").val() == 2) {
            metal_weight = instalment_amt / gold_metal_rate;
        } else if ($("#sch_type").val() == 1) {
            metal_weight = $('#selected_weight').val();
        }
        /*	$('#payment_weight').val(metal_weight*sel_dues);
            $('#gst_amt').val(gst_amt*sel_dues); 
            $('#payment_amt').val(insAmt_withoutDisc*sel_dues); */
        var metal_weight = setMetalWgt(metal_weight);
        return { "payment_amt": insAmt_withoutDisc, "gst_amt": gst_amt, "metal_weight": metal_weight };
    }
    function setMetalWgt(metal_wgt) {
        var metal_weight = metal_wgt.toString();
        console.log(metal_weight);
        //var metal_wgt_roundoff = $("#metal_wgt_roundoff").val();
        //var metal_wgt_decimal = $("#metal_wgt_decimal").val(); 
        var metal_wgt_roundoff = 0;
        var metal_wgt_decimal = 2;
        let isnum = /^\d+$/.test(metal_wgt);
        //console.log(metal_weight +'--'+ isnum);
        console.log(metal_weight + '--' + isnum);
        if (metal_wgt_roundoff == 0 && isnum == false && metal_wgt != "" && metal_weight != NaN) {
            var arr = metal_weight.split(".");
            var str = arr[1];
            //var deci = str.substring(0, metal_wgt_decimal); // Take first 2 decimal places
            var deci = 2;
            console.log(deci);
            return arr[0] + "." + deci;
        } else {
            return metal_wgt;
        }
    }
}
// Multi-mode payment :: STARTS
//Credit card starts
$('#new_card').on('click', function () {
    $("#cardPayAlert span").remove();
    if (validateCardDetailRow()) {
        create_new_empty_cardpay_row();
    } else {
        $("#cardPayAlert").append("<span>Please fill all fields in current row.</span>");
        $('#cardPayAlert span').delay(20000).fadeOut(500);
    }
});
function get_payment_device_details() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_billing/get_payment_device_details',
        dataType: 'json',
        success: function (data) {
            payment_device_details = data;
        }
    });
}
function get_payment_bank_details() {
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_ret_billing/get_bank_acc_details',
        dataType: 'json',
        success: function (data) {
            payment_bank_details = data;
        }
    });
}
function validateCardDetailRow() {
    var row_validate = true;
    $('#card_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.card_name').val() == "" || $(this).find('.card_type').val() == "" || $(this).find('.card_no').val() == "" || $(this).find('.card_amt').val() == "" || $(this).find('.ref_no').val() == "" || $(this).find('.id_device').val() == "") {
            row_validate = false;
        }
    });
    return row_validate;
}
function create_new_empty_cardpay_row() {
    var card_rows_added = $('#card_details tbody tr').length;
    var card_count = card_rows_added++;
    var row = "";
    var device_list = '';
    $.each(payment_device_details, function (pkey, item) {
        device_list += "<option value='" + item.id_device + "'>" + item.device_name + "</option>";
    });
    console.log(device_list);
    row += '<tr>' +
        '<td><select name="card_details[card_name][]" class="card_name"><option value="1">RuPay</option><option value="2">VISA</option><option value="3">Mastro</option><option value="4">Master</option></select></td>' +
        '<td><select name="card_details[card_type][]" class="card_type"><option value="1">CC</option><option value="2">DC</option></select></td>' +
        '<td><select class="form-control id_device" name="card_details[id_device][]" style="width: 100px !important;">' + device_list + '</select></td> ' +
        '<td><input type="number" step="any" class="card_no" name="card_details[card_no][]"/></td>' +
        '<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]" onkeypress="return /[0-9]/i.test(event.key)"/></td>'
        //+'<td><input type="text" step="any" class="ref_no" name="card_details[ref_no][]"/></td>'
        +
        '<td><input type="number" step="any" class="ref_no" id="cardref_no_' + card_count + '" onkeyup="check_ref_no(this,this.value,' + card_count + ')" name="card_details[ref_no][]"/><span class="error" id="ref_span_' + card_count + '" ></span></td>' +
        '<td><a href="#" onClick="removeCC_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
        '</tr>';
    $('#card_details tbody').append(row);
}
$(document).on('keyup', '.card_amt', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        if (validateCardDetailRow()) {
            create_new_empty_cardpay_row();
        } else {
            alert("Please fill required fields");
        }
    }
    calculate_creditCard_Amount();
});
function removeCC_row(curRow) {
    curRow.remove();
    calculate_creditCard_Amount();
}
function calculate_creditCard_Amount() {
    var cc_amount = 0;
    var dc_amount = 0;
    card_payment = [];
    $('#card-detail-modal .modal-body #card_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.card_amt').val() != "") {
            if ($(this).find('.card_type').val() == 1) {
                cc_amount += parseFloat($(this).find('.card_amt').val());
            } else if ($(this).find('.card_type').val() == 2) {
                dc_amount += parseFloat($(this).find('.card_amt').val());
            }
            // card_payment.push({'card_name':$(this).find('.card_name').val(),'id_device':$('.id_device').val(),'card_type':$(this).find('.card_type').val(),'card_no':$(this).find('.card_no').val(),'card_amt':$(this).find('.card_amt').val()});
            // var tot_pay_amt = parseFloat(parseFloat(cc_amount) + parseFloat(dc_amount)).toFixed(2);
            // if(tot_pay_amt > total_amount ){
            // 	$("#tot_span_"+index).text("Enter valid amount");
            // 	$("#add_newcc").addClass("disabled");
            // }else{
            // 	$("#tot_span_"+index).text("");
            // 	$("#add_newcc").removeClass("disabled"); 
            // }
        }
    });
    $('.cc_total_amt').html(parseFloat(cc_amount).toFixed(2));
    $('.dc_total_amt').html(parseFloat(dc_amount).toFixed(2));
    $('.cc_total_amount').html(parseFloat(parseFloat(cc_amount) + parseFloat(dc_amount)).toFixed(2));
    calculate_model_payment(); //esakki
}
$('#add_newcc').on('click', function () {
    calculate_creditCard_Amount(); // esakki 18-09
    if (validateCardDetailRow()) {
        card_payment = [];
        $('#card-detail-modal .modal-body #card_details > tbody  > tr').each(function (index, tr) {
            if ($(this).find('.card_amt').val() != "") {
                card_payment.push({ 'card_name': $(this).find('.card_name').val(), 'id_device': $('.id_device').val(), 'card_type': $(this).find('.card_type').val(), 'card_no': $(this).find('.card_no').val(), 'card_amt': $(this).find('.card_amt').val(), 'ref_no': $(this).find('.ref_no').val() });
            }
        });
        $('#payment_modes > tbody >tr').each(function (bidx, brow) {
            bill_card_pay_row = $(this);
            bill_card_pay_row.find('.CC').html($('.cc_total_amt').html());
            bill_card_pay_row.find('.DC').html($('.dc_total_amt').html());
            bill_card_pay_row.find('#card_payment').val(card_payment.length > 0 ? JSON.stringify(card_payment) : '');
        });
        $('#card-detail-modal').modal('toggle');
        $('#edit_payment').css('overflow-y', 'auto');
        calculatePaymentCost();
    } else {
        alert("Please fill required fields");
    }
});
//Credit card ends
//Chque starts
$('#new_chq').on('click', function () {
    $("#chqPayAlert span").remove();
    if (validateChqDetailRow()) {
        create_new_empty_chqpay_row();
    } else {
        $("#chqPayAlert").append("<span>Please fill all fields in current row.</span>");
        $('#chqPayAlert span').delay(20000).fadeOut(500);
    }
});
function validateChqDetailRow() {
    var row_validate = true;
    $('#chq_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.bank_name').val() == "" || $(this).find('.bank_branch').val() == "" || $(this).find('.cheque_no').val() == "" || $(this).find('.payment_amount').val() == "") {
            row_validate = false;
        }
    });
    return row_validate;
}
function create_new_empty_chqpay_row() //esakki
{
    var bank_list = '';
    $.each(bank_details, function (pkey, item) {
        bank_list += "<option value='" + item.acc_number + "'>" + item.acc_number + "</option>";
    });
    // esakki 17-09
    rows_added = $('#chq_details tbody tr').length;
    var chq_row_count = rows_added++;
    var row = "";
    row += '<tr>' +
        '<td><input class="cheque_date" data-date-format="dd-mm-yyyy" name="cheque_details[cheque_date][]" type="text" placeholder="Cheque Date" /></td>'
        // +'<td><input name="cheque_details[bank_name][]" type="text" class="bank_name" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>'
        +
        '<td><select class="bank_name" name="cheque_details[bank_name][]">' + bank_list + '</select></td> ' +
        '<td><input name="cheque_details[bank_branch][]" type="text" class="bank_branch" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>' +
        '<td><input type="number" step="any" class="cheque_no" id="chq_no_' + chq_row_count + '" name="cheque_details[cheque_no][]" onkeyup="check_ref_no(this,this.value,' + chq_row_count + ')" /><span class="error" id="chq_ref_span_' + chq_row_count + '"></span></td>' // esakki 17-09
        +
        '<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)" /></td>' +
        '<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td>' +
        '<td><a href="#" onClick="removeChq_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
        '</tr>';
    $('#chq_details tbody').append(row);
    $('#chq_details > tbody').find('tr:last .cheque_date').focus();
}
$(document).on('keyup', '.payment_amount', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        if (validateChqDetailRow()) {
            create_new_empty_chqpay_row();
        } else {
            alert("Please fill required fields");
        }
    }
    calculate_chq_Amount();
});
function removeChq_row(curRow) {
    curRow.remove();
    calculate_chq_Amount();
}
function calculate_chq_Amount() {
    var total_amount = 0;
    //esakki
    // var total_amount = ($('.bal_amount').html() != '' ? parseFloat($('.bal_amount').html()) : 0);
    var chq_amount = 0;
    chq_payment = [];
    $('#cheque-detail-modal .modal-body #chq_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.payment_amount').val() != "") {
            chq_amount += parseFloat($(this).find('.payment_amount').val());
            chq_payment.push({ 'cheque_date': $(this).find('.cheque_date').val(), 'cheque_no': $(this).find('.cheque_no').val(), 'bank_branch': $(this).find('.bank_branch').val(), 'bank_name': $(this).find('.bank_name').val(), 'payment_amount': $(this).find('.payment_amount').val(), 'bank_IFSC': $(this).find('.bank_IFSC').val() });
        }
    });
    $('.chq_total_amount').html(parseFloat(chq_amount).toFixed(2));
    calculate_model_payment(); //esakki
}
$('#add_newchq').on('click', function () { //esakki
    //lines added by Durga starts here -12.04.2023
    var amount_limit = parseFloat($("#payment_amt").val());
    var payment_amt = ($('#payment_amt').val() != '' ? $('#payment_amt').val() : 0);
    var make_pay_cash = ($('#make_pay_cash').val() != '' ? $('#make_pay_cash').val() : 0);
    var cc = ($('.CC').html() != '' ? $('.CC').html() : 0);
    var dc = ($('.DC').html() != '' ? $('.DC').html() : 0);
    var chq = ($('.CHQ').html() != '' ? $('.CHQ').html() : 0);
    var NB = ($('.NB').html() != '' ? $('.NB').html() : 0);
    var adv_adj_amt = ($('#tot_adv_adj').html() != '' ? $('#tot_adv_adj').html() : 0);
    var total_amount = parseFloat(parseFloat(make_pay_cash) + parseFloat(cc) + parseFloat(dc) + parseFloat(NB) + parseFloat(adv_adj_amt)).toFixed(2);
    var can_pay = parseFloat(amount_limit) - parseFloat(total_amount);
    var total_sum = $(".chq_total_amount").html();
    //lines added by Durga ends here -12.04.2023
    if (validateChqDetailRow()) {
        if (total_sum > can_pay) {
            alert("Can Pay upto INR " + can_pay);
            $("#cheque_amount").val("");
            $('.chq_total_amount').html("");
        } else if (total_sum > amount_limit) {
            //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Amount Should not exceed INR "+amount_limit});
            alert("Amount Should not exceed INR " + amount_limit);
            $("#cheque_amount").val("");
            $('.chq_total_amount').html("");
        } else {
            if (total_sum != '') {
                calculate_chq_Amount();
                $('#payment_modes > tbody >tr').each(function (bidx, brow) {
                    bill_card_pay_row = $(this);
                    bill_card_pay_row.find('.CHQ').html($('.chq_total_amount').html());
                    bill_card_pay_row.find('#chq_payment').val(chq_payment.length > 0 ? JSON.stringify(chq_payment) : '');
                });
                $('#cheque-detail-modal').modal('toggle');
                calculatePaymentCost();
            } else {
                alert("Can Pay upto INR " + can_pay);
            }
        }
    } else {
        alert("Please fill required fields");
    }
});
$(document).on('focus', '.cheque_date', function (e) {
    var row = $(this).closest('tr');
    row.find('.cheque_date').datepicker({
        format: 'dd-mm-yyyy'
    });
});
//Cheque ends
//Net banking starts
$('#new_net_bank').on('click', function () {
    $("#NetBankAlert span").remove();
    if (validateNBDetailRow()) {
        create_new_empty_net_banking_row();
    } else {
        $("#NetBankAlert").append("<span>Please fill all fields in current row.</span>");
        $('#NetBankAlert span').delay(20000).fadeOut(500);
    }
});
function validateNBDetailRow() {
    var row_validate = true;
    $('#net_bank_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.nb_type').val() == "" || $(this).find('.ref_no').val() == "" || $(this).find('.amount').val() == "") {
            $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Please Fill The Required Fields.." });
            row_validate = false;
            return true;
        }
        if ($(this).find('.nb_type').val() == 3) {
            if ($(this).find('.id_device').val() == "") {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Select Device Type.." });
                row_validate = false;
                return true;
            }
        } else if ($(this).find('.nb_type').val() == 1 || $(this).find('.nb_type').val() == 2) {
            if ($(this).find('.id_bank').val() == "") {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Select The Bank" });
                row_validate = false;
                return true;
            }
        }
    });
    return row_validate;
}
function create_new_empty_net_banking_row() {
    console.log("succes");
    //var i=nb_values.length-1;
    /*console.log(nb_values[i].nb_type);
    if(nb_values[i].nb_type!=undefined && nb_values[i].nb_type!="")
    {
            nbpayment.push(nb_values[i]);	
    }*/
    /*nb_values=[];
    count += 1;*/
    var devicelist = '';
    var banklist = '';
    rows_added = $('#net_bank_details tbody tr').length;
    var nb_row_count = rows_added++;
    $.each(payment_device_details, function (pkey, item) {
        devicelist += "<option value='" + item.id_device + "'>" + item.device_name + "</option>";
    });
    $.each(payment_bank_details, function (pkey, item) {
        banklist += "<option value='" + item.id_bank + "'>" + item.acc_number + "</option>";
    });
    var row = "";
    row += '<tr>'
        //	+'<td><select name="nb_details[nb_type][]" class="nb_type" ><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>'
        //	+'<td class="upi_type"><select name="nb_details[nb_bank][]" class="id_bank" style="width:150px;"><option value="">Select Bank</option>'+banklist+'</select></td>'
        //	+'<td class="device" style="display:none;"><select name="nb_details[nb_device][]" class="id_device" style="width:150px;"><option value="">Select Device</option>'+devicelist+'</select></td>'
        +
        '<td><select name="nb_details[nb_type][]" class="nb_type" id="nb_type_' + nb_row_count + '" onchange="setdevice(this.value,' + nb_row_count + ')" style="width:100%;"><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option><option value=3 selected>UPI</option></select></td>' +
        '<td class="upi_type" id="upi_type_' + nb_row_count + '" style="display:none;"><select name="nb_details[nb_bank][]"  class="id_bank" style="width:100%;" style="width:150px;"><option value="">Select Bank</option>' + banklist + '</select></td>' +
        '<td class="device" id="device_' + nb_row_count + '"><select name="nb_details[nb_device][]" class="id_device"  style="width:100%;"><option value="">Select Device</option>' + devicelist + '</select></td>' +
        '<td><input class="form-control  datemask date nb_date" data-date-format="yyyy-mm-dd" name="nb_details[nb_date][]" type="text" placeholder="NB Date" style="width: 100px;" /></td>'
        //+'<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td>'
        +
        '<td><input type="number" step="any" class="ref_no" id="nbref_no_' + nb_row_count + '" name="nb_details[ref_no][]" onkeyup="check_ref_no(this,this.value,' + nb_row_count + ')"  style="width:100%;"/><span class="error" id="nb_ref_span_' + nb_row_count + '"></span></td>' +
        '<td><input type="number" step="any" class="amount" name="nb_details[amount][]" onkeypress="return /[0-9]/i.test(event.key)"  style="width:100%;"/></td>' +
        '<td><a href="#" onClick="removeNb_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
        '</tr>';
    $('#net_bank_details tbody').append(row);
    $('#net_bank_details > tbody').find('tr:last .nb_date').focus();
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    // $('.nb_date').datepicker({ dateFormat: 'yyyy-mm-dd',endDate:today });
    $("#net_bank_details > tbody").find("tr:last .nb_date")
        .datepicker({
            dateFormat: "yyyy-mm-dd",
            endDate: today,
        })
        .datepicker("setDate", today);
}
$(document).on('keyup', '.amount', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        if (validateNBDetailRow()) {
            create_new_empty_net_banking_row();
        } else {
            alert("Please fill required fields");
        }
    }
    $('#net_banking_modal .modal-body #net_bank_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.amount').val() != "") {
            console.log(count);
            nb_values.push({ 'nb_type': $(this).find('.nb_type' + count).val(), 'ref_no': $(this).find('.ref_no').val(), 'amount': $(this).find('.amount').val(), 'bank': $(this).find('.nb_bank' + count).val(), 'device': $(this).find('.nb_device' + count).val(), 'acc_no': $('.nb_bank' + count).find(':selected').text() });
        }
    });
    var len = nb_values.length - 1;
    console.log(nb_values[len]);
    calculate_NB_Amount();
});
function removeNb_row(curRow) {
    if (curRow.length > 0) {
        if (nbpayment.length - 1 >= curRow[0].id) {
            nbpayment.splice(curRow[0].id);
        }
        console.log(nbpayment.length - 1 >= curRow[0].id);
    }
    console.log(nbpayment);
    curRow.remove();
    calculate_NB_Amount();
}
function calculate_NB_Amount() {
    var total_amount = 0;
    var nb_amount = 0;
    nb_payment = [];
    $('#net_banking_modal .modal-body #net_bank_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.amount').val() != "") {
            nb_amount += parseFloat($(this).find('.amount').val());
            nb_payment.push({ 'nb_type': $(this).find('.nb_type' + count).val(), 'ref_no': $(this).find('.ref_no').val(), 'amount': $(this).find('.amount').val(), 'bank': $(this).find('.nb_bank' + count).val(), 'device': $(this).find('.nb_device' + count).val(), 'acc_no': $('.nb_bank' + count).find(':selected').text() });
        }
    });
    $('.nb_total_amount').html(parseFloat(nb_amount).toFixed(2));
    calculate_model_payment(); //esakki
}
$('#add_newnb').on('click', function () {
    if (validateNBDetailRow()) {
        var nbpayment = [];
        $('#net_banking_modal .modal-body #net_bank_details > tbody  > tr').each(function (index, tr) {
            if ($(this).find('.amount').val() != "") {
                nbpayment.push({
                    'nb_type': $(this).find('.nb_type').val(),
                    'id_bank': $(this).find('.id_bank').val(),
                    'nb_date': $(this).find('.nb_date').val(),
                    'id_device': $(this).find('.id_device').val(),
                    'amount': $(this).find('.amount').val(),
                    'ref_no': $(this).find('.ref_no').val()
                });
            }
        });
        $('#payment_modes > tbody >tr').each(function (bidx, brow) {
            bill_card_pay_row = $(this);
            bill_card_pay_row.find('.NB').html($('.nb_total_amount').html());
            bill_card_pay_row.find('#nb_payment').val(nbpayment.length > 0 ? JSON.stringify(nbpayment) : '');
            nb_values = [];
        });
        $('#net_banking_modal').modal('toggle');
        calculatePaymentCost();
    } else {
        alert("Please fill required fields");
    }
    console.log($('#nb_payment').val());
});
/*  $(document).on('change','.nb_type',function(e){
      $('.device').hide();
      $('.upi_type').hide();
      if(this.value==3)
      {
          $('.device').show();
      }
      else if(this.value==2 || this.value==1)
      {
          $('.upi_type').show();
      }
  });*/
function setdevice(value, index) {
    //alert();
    if (value == 3) {
        // $('.device').show();
        // $('.upi_type').hide();
        $('#device_' + index).css("display", "block");
        $('#upi_type_' + index).css("display", "none");
    } else if (value == 2 || value == 1) {
        //$('#upi_type_'+index).show();
        $('#upi_type_' + index).css("display", "block");
        $('#device_' + index).css("display", "none");
        // $('.upi_type').show();
        // $('.device').hide();
    }
}
function check_ref_no(inputelement, value, index) {
    var inputName = inputelement.name;
    var parts = inputName.split('_');
    var input_type = parts[0];
    var ress = false;
    if (input_type == 'nb') {
        $('#net_bank_details tbody tr').each(function (i, row) {
            console.log("indexx", index);
            console.log("iiii", i);
            var prev_val = $(this).find('#nbref_no_' + i).val();
            console.log("previous value", prev_val);
            if (index != i) {
                if (value == prev_val) {
                    ress = true;
                }
            }
        });
    } else if (input_type == 'cheque') { // esakki 17-09
        $('#chq_details tbody tr').each(function (i, row) {
            console.log("indexx", index);
            console.log("iiii", i);
            var prev_val = $(this).find('#chq_no_' + i).val();
            console.log("previous value", prev_val);
            if (index != i) {
                if (value == prev_val) {
                    ress = true;
                }
            }
        });
    } else {
        $('#card_details tbody tr').each(function (i, row) {
            console.log("indexx", index);
            console.log("iiii", i);
            var prev_val = $(this).find('#cardref_no_' + i).val();
            console.log("previous value", prev_val);
            if (index != i) {
                if (value == prev_val) {
                    ress = true;
                }
            }
        });
    }
    console.log("ress", ress);
    if (isValid(value)) {
        my_Date = new Date();
        // esakki 17-09
        if (input_type != 'cheque') {
            $.ajax({
                url: base_url + 'index.php/admin_payment/get_ref_num/?nocache=' + my_Date.getUTCSeconds(),
                dataType: "json",
                method: "POST",
                data: { 'value': value },
                success: function (data) {
                    //console.log(data);
                    if (data.payment_amount > 0) {
                        if (input_type == 'nb') {
                            $("#nb_ref_span_" + index).text("Ref no already Used");
                            var link = $("#add_newnb");
                            link.removeAttr('href');
                            link.addClass('disabled');
                            link.css('pointer-events', 'none');
                        } else {
                            $("#ref_span_" + index).text("Ref no already Used");
                            var link = $("#add_newcc");
                            link.removeAttr('href');
                            link.addClass('disabled');
                            link.css('pointer-events', 'none');
                        }
                    } else {
                        if (input_type == 'nb') {
                            if (ress == true) {
                                $("#nb_ref_span_" + index).text("Ref no already Used.");
                            } else {
                                $("#nb_ref_span_" + index).text("");
                                var $link = $("#add_newnb");
                                $link.removeClass('disabled');
                                $link.css('pointer-events', 'auto');
                            }
                        } else {
                            if (ress == true) {
                                $("#ref_span_" + index).text("Ref no already Used.");
                            } else {
                                $("#ref_span_" + index).text("");
                                var $link = $("#add_newcc");
                                $link.removeClass('disabled');
                                $link.css('pointer-events', 'auto');
                            }
                        }
                    }
                }
            });
        } else {
            $.ajax({
                url: base_url + 'index.php/admin_payment/get_chq_num/?nocache=' + my_Date.getUTCSeconds(),
                dataType: "json",
                method: "POST",
                data: { 'value': value },
                success: function (data) {
                    if (data.payment_amount > 0) {
                        $("#chq_ref_span_" + index).text("Cheque no already Used");
                        var link = $("#add_newchq");
                        link.removeAttr('href');
                        link.addClass('disabled');
                        link.css('pointer-events', 'none');
                    } else {
                        if (ress == true) {
                            $("#chq_ref_span_" + index).text("Cheque no already Used.");
                        } else {
                            $("#chq_ref_span_" + index).text("");
                            var $link = $("#add_newchq");
                            $link.removeClass('disabled');
                            $link.css('pointer-events', 'auto');
                        }
                    }
                }
            });
        }
    } else {
        if (input_type == 'nb') {
            $("#nb_ref_span_" + index).text("");
        } else {
            $("#ref_span_" + index).text("");
        }
    }
}
$('.net_banking_modal').on('click', function () {
    if ($('.nb_type' + count).val() == 1 || $('.nb_type' + count).val() == 2) {
        $('.nb_bank' + count).show();
        $('.nb_device' + count).hide();
    } else if ($('#nb_type' + count).val() == 3) {
        $('.nb_device' + count).show();
        $('.nb_bank' + count).hide();
    }
});
$('.nb_type' + count).on('change', function () {
    if (this.value == 1 || this.value == 2) {
        $('.nb_bank' + count).show();
        $('.nb_device' + count).hide();
    } else if (this.value == 3) {
        $('.nb_device' + count).show();
        $('.nb_bank' + count).hide();
    }
});
function showhide(event, count) {
    if (event.target.value == 1 || event.target.value == 2) {
        $('.nb_bank' + count).show();
        $('.nb_device' + count).hide();
    } else if (event.target.value == 3) {
        $('.nb_device' + count).show();
        $('.nb_bank' + count).hide();
    }
}
$('#card_detail_modal').on('click', function () {
    var existingBalance = parseFloat($(".bal_amount").text().trim());
    console.log("balanceeee::::::", existingBalance);
    $(".modal_balance_amount").text(
        isNaN(existingBalance) ? 0 : indianCurrency.format(existingBalance)
    );
    if (validateCardDetailRow()) {
        //esakki
        if ($('#card_details > tbody > tr').length == 0) {
            create_new_empty_cardpay_row();
        }
    }
});
$('#netbankmodal').on('click', function () {
    if (validateNBDetailRow()) {
        //esakki
        var existingBalance = parseFloat($(".bal_amount").text().trim());
        $(".modal_balance_amount").text(
            isNaN(existingBalance) ? 0 : indianCurrency.format(existingBalance)
        );
        if ($("#net_bank_details > tbody > tr").length == 0) {
            create_new_empty_net_banking_row();
        }
    }
    /*var rowCount = $('#net_bankdetails tr').length;
    if(rowCount>1)
    {
        $("#net_bankdetails").find("tr:gt(0)").remove();
    }
    $('.ref_no').val("");
    $('.nb_total_amount').text("");
    $('.amount').val("");
    $('.NB').text("");
    $('.nb_type0').val("");
    $('.nb_bank0').val("");
    $('.nb_device0').val("");
    nbpayment=[];
    count=0;*/
});
//Net banking ends
//esakki   
//cheque
$('#cheque_modal').on('click', function () {
    //esakki
    var existingBalance = parseFloat($('.bal_amount').text().trim());
    $(".modal_balance_amount").text(
        isNaN(existingBalance) ? 0 : indianCurrency.format(existingBalance)
    );
    if ($('#chq_details > tbody > tr').length == 0) {
        create_new_empty_chqpay_row();
    }
});
//voucher starts here
function validatevchetailRow() {
    var row_validate = true;
    $('#vch_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.voucher_code').val() == "" || $(this).find('.voucher_amount').val() == "") {
            $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Please Fill The Required Fields.." });
            row_validate = false;
            return true;
        }
    });
    return row_validate;
}
$('#vch_newvch').on('click', function () {
    if (validatevchetailRow()) {
        var vchpayment = [];
        $('#vch-detail-modal .modal-body #vch_details > tbody  > tr').each(function (index, tr) {
            if ($(this).find('.voucher_amount').val() != "") {
                vchpayment.push({
                    'card_no': $(this).find('.voucher_code').val(),
                    'payment_amount': $(this).find('.voucher_amount').val(),
                });
            }
        });
        $('#payment_modes > tbody >tr').each(function (bidx, brow) {
            bill_card_pay_row = $(this);
            bill_card_pay_row.find('.VCH').html($('.vch_total_amount').html());
            bill_card_pay_row.find('#vch_payment').val(vchpayment.length > 0 ? JSON.stringify(vchpayment) : '');
            vch_values = [];
        });
        $('#vch-detail-modal').modal('toggle');
        calculatePaymentCost();
    } else {
        alert("Please fill required fields");
    }
    console.log($('#vch_payment').val());
});
$('#new_vch').on('click', function () {
    $("#vchPayAlert span").remove();
    if (validatevchetailRow()) {
        create_new_empty_vch_row();
    } else {
        $("#vchPayAlert").append("<span>Please fill all fields in current row.</span>");
        $('#vchPayAlert span').delay(20000).fadeOut(500);
    }
});
function create_new_empty_vch_row() {
    var row = "";
    row += '<tr>' +
        '<td><input type="text" step="any" class="voucher_code" name="vch_details[voucher_code][]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"  /></td>' +
        '<td><input type="number" step="any" class="voucher_amount" name="vch_details[payment_amount][]"/></td>' +
        '<td><a href="#" onClick="removevch_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
        '</tr>';
    $('#vch_details tbody').append(row);
}
$(document).on('keyup', '.voucher_amount', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        if (validateCardDetailRow()) {
            create_new_empty_vch_row();
        } else {
            alert("Please fill required fields");
        }
    }
    calculate_vch_Amount();
});
function removevch_row(curRow) {
    curRow.remove();
    calculate_vch_Amount();
}
function calculate_vch_Amount() {
    var total_amount = 0;
    var vch_amount = 0;
    vch_payment = [];
    $('#vch-detail-modal .modal-body #vch_details > tbody  > tr').each(function (index, tr) {
        if ($(this).find('.voucher_amount').val() != "") {
            vch_amount += parseFloat($(this).find('.voucher_amount').val());
            vch_payment.push({ 'card_no': $(this).find('.voucher_code').val(), 'payment_amount': $(this).find('.voucher_amount').val() });
        }
    });
    $('.vch_total_amount').html(parseFloat(vch_amount).toFixed(2));
    calculate_model_payment();
}
//voucher ends here
$('#make_pay_cash,.receive_amount').on('keyup', function () {
    calculatePaymentCost();
});
$('#make_pay_cash').on('wheel', function (e) {
    this.blur();
});
//Added by Durga 22.05.2023 starts here
$('#cash_div').on('click', function () {
    var allowed_advance = parseInt($("#sel_due").val());
    if (allowed_advance > 1) {
        $('#card_div').css("pointer-events", 'none');
        $('#card_div').css("opacity", '0.4');
        $('#cheque_div').css("pointer-events", 'none');
        $('#cheque_div').css("opacity", '0.4');
        $('#nb_div').css("pointer-events", 'none');
        $('#nb_div').css("opacity", '0.4');
        $('#adv_adj_div').css("pointer-events", 'none');
        $('#adv_adj_div').css("opacity", '0.4');
        $('#VCH_div').css("pointer-events", 'none');
        $('#VCH_div').css("opacity", '0.4');
    }
});
$('#card_detail_modal').on('click', function () {
    var allowed_advance = parseInt($("#sel_due").val());
    if (allowed_advance > 1) {
        $("#new_card").hide();
        $('#cash_div').css("pointer-events", 'none');
        $('#cash_div').css("opacity", '0.4');
        $('#cheque_div').css("pointer-events", 'none');
        $('#cheque_div').css("opacity", '0.4');
        $('#nb_div').css("pointer-events", 'none');
        $('#nb_div').css("opacity", '0.4');
        $('#adv_adj_div').css("pointer-events", 'none');
        $('#adv_adj_div').css("opacity", '0.4');
        $('#VCH_div').css("pointer-events", 'none');
        $('#VCH_div').css("opacity", '0.4');
    }
});
$('#cheque-detail-modal').on('click', function () {
    var allowed_advance = parseInt($("#sel_due").val());
    if (allowed_advance > 1) {
        $("#new_chq").hide();
        $('#cash_div').css("pointer-events", 'none');
        $('#cash_div').css("opacity", '0.4');
        $('#card_div').css("pointer-events", 'none');
        $('#card_div').css("opacity", '0.4');
        $('#nb_div').css("pointer-events", 'none');
        $('#nb_div').css("opacity", '0.4');
        $('#adv_adj_div').css("pointer-events", 'none');
        $('#adv_adj_div').css("opacity", '0.4');
        $('#VCH_div').css("pointer-events", 'none');
        $('#VCH_div').css("opacity", '0.4');
    }
});
$('#net_banking_modal').on('click', function () {
    var allowed_advance = parseInt($("#sel_due").val());
    if (allowed_advance > 1) {
        $("#new_net_bank").hide();
        $('#cash_div').css("pointer-events", 'none');
        $('#cash_div').css("opacity", '0.4');
        $('#card_div').css("pointer-events", 'none');
        $('#card_div').css("opacity", '0.4');
        $('#cheque_div').css("pointer-events", 'none');
        $('#cheque_div').css("opacity", '0.4');
        $('#adv_adj_div').css("pointer-events", 'none');
        $('#adv_adj_div').css("opacity", '0.4');
        $('#VCH_div').css("pointer-events", 'none');
        $('#VCH_div').css("opacity", '0.4');
    }
});
$('#adv_adj_modal').on('click', function () {
    //esakki
    var existingBalance = parseFloat($('.bal_amount').text().trim());
    $('.modal_balance_amount').text(isNaN(existingBalance) ? 0 : existingBalance);
    var allowed_advance = parseInt($("#sel_due").val());
    if (allowed_advance > 1) {
        $("#new_net_bank").hide();
        $('#cash_div').css("pointer-events", 'none');
        $('#cash_div').css("opacity", '0.4');
        $('#card_div').css("pointer-events", 'none');
        $('#card_div').css("opacity", '0.4');
        $('#cheque_div').css("pointer-events", 'none');
        $('#cheque_div').css("opacity", '0.4');
        $('#nb_div').css("pointer-events", 'none');
        $('#nb_div').css("opacity", '0.4');
        $('#VCH_div').css("pointer-events", 'none');
        $('#VCH_div').css("opacity", '0.4');
    }
});
//esakki
$('#vch_modal').on('click', function () {
    var existingBalance = parseFloat($('.bal_amount').text().trim());
    $(".modal_balance_amount").text(
        isNaN(existingBalance) ? 0 : indianCurrency.format(existingBalance)
    );
});
$('#vch-detail-modal').on('click', function () {
    var allowed_advance = parseInt($("#sel_due").val());
    if (allowed_advance > 1) {
        $("#new_net_bank").hide();
        $('#cash_div').css("pointer-events", 'none');
        $('#cash_div').css("opacity", '0.4');
        $('#card_div').css("pointer-events", 'none');
        $('#card_div').css("opacity", '0.4');
        $('#cheque_div').css("pointer-events", 'none');
        $('#cheque_div').css("opacity", '0.4');
        $('#nb_div').css("pointer-events", 'none');
        $('#nb_div').css("opacity", '0.4');
        $('#adv_adj_div').css("pointer-events", 'none');
        $('#adv_adj_div').css("opacity", '0.4');
    }
});
//Added by Durga 22.05.2023 ends here
$('#make_pay_cash').on('change', function () {
    if ((parseInt($('#make_pay_cash').val()) > parseInt($('#min_pan_amt').val())) && ($('#min_pan_amt').val() != 0)) {
        $('#make_pay_cash').val(0);
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Enter The Cash Amount Minimum of Rs." + $('#min_pan_amt').val() });
    }
    calculatePaymentCost();
});
async function calculatePaymentCost() {
    // validate_max_cash();
    var total_amount = 0;
    var bal_amount = 0;
    var wallet_blc = 0;
    var payment_amt = ($('#payment_amt').val() != '' ? $('#payment_amt').val() : 0);
    var make_pay_cash = ($('#make_pay_cash').val() != '' ? $('#make_pay_cash').val() : 0);
    var cc = ($('.CC').html() != '' ? $('.CC').html() : 0);
    var dc = ($('.DC').html() != '' ? $('.DC').html() : 0);
    var chq = ($('.CHQ').html() != '' ? $('.CHQ').html() : 0);
    var NB = ($('.NB').html() != '' ? $('.NB').html() : 0);
    var adv_adj_amt = ($('#tot_adv_adj').html() != '' ? $('#tot_adv_adj').html() : 0);
    var vch = (($('.VCH').html() != '' && $('.VCH').html() !== undefined) ? $('.VCH').html() : 0);
    total_amount = parseFloat(parseFloat(make_pay_cash) + parseFloat(cc) + parseFloat(dc) + parseFloat(chq) + parseFloat(NB) + parseFloat(adv_adj_amt) + parseFloat(vch)).toFixed(2);
    bal_amount = parseFloat(parseFloat(payment_amt) - parseFloat(total_amount)).toFixed(2);
    $('.sum_of_amt').html(total_amount);
    $('.bal_amount').html(bal_amount);

    if (($('#payment_amt').val() == 0)) {
        $('#pay_submit').prop('disabled', false);
    }
}
//Advance starts     #EP
function get_advance_details() {
    $('#bill_adv_adj > tbody').empty();
    my_Date = new Date();
    $.ajax({
        url: base_url + 'index.php/admin_ret_billing/get_advance_details/?nocache=' + my_Date.getUTCSeconds(),
        dataType: "json",
        method: "POST",
        data: { 'bill_cus_id': $('#id_customer').val(), 'id_payment': $('#id_payment').val() },
        success: function (data) {
            rec_id_ret_wallet = data[0].id_ret_wallet;
            total_sum_adjusted_bill_amount = 0;
            $.each(data, function (key, items) {
                total_sum_adjusted_bill_amount += parseInt((parseFloat(items.amount).toFixed(2)));
            });
            console.log(total_sum_adjusted_bill_amount);
            if (total_sum_adjusted_bill_amount > 0) {
                $('#adv-adj-confirm-add').modal('show');
                var row = "";
                var html = '';
                var metal_rate = $('.per-grm-sale-value').html();
                var weight_amt = parseFloat(data.weight * data.rate_per_gram);
                $('#id_ret_wallet').val(data.id_ret_wallet);
                //onclick="handleCheckboxClick(this)" ---> added by Durga 19.05.2023
                let advAdjPayArray = $('#advance_muliple_receipt').val();
                $.each(data, function (key, items) {
                    html += '<tr data-id="' + items.id_issue_receipt + '">' +
                        '<td><input type="checkbox" class="id_issue_receipt" onclick="handleCheckboxClick(this)"  name="adv_adj[id_issue_receipt]" value="' + items.id_issue_receipt + '"><input type="hidden" class="id_ret_wallet" value="' + items.id_ret_wallet + '"><input type="hidden" class="adv_cash_paid" value="' + items.cash_pay + '"><input type="hidden" class="is_currentday_adv" value="' + items.is_currentday_adv + '"></td>' +
                        '<td><div class="adv_bill_no" value="' + items.bill_no + '">' + items.bill_no + '</div></td>' +
                        '<td><div class="advance_amount" >' + items.amount + '</div></td>' +
                        '<td><input type="number" class="form-control adj_amount" name="adv_adj[adj_amount]" ></td>' +
                        '<td><input type="number" class="form-control blc_amount" name="adv_adj[blc_amount]" readonly></td>' +
                        '</tr>';
                });
                $('.total_adv_amt').html(parseFloat(total_sum_adjusted_bill_amount).toFixed(2));
                $('.total_bill_amt').html(parseFloat($('#payment_amt').val()).toFixed(2));
                $('#bill_adv_adj > tbody').append(html);
                if (advAdjPayArray.length != 0) {
                    advAdjPayArray = JSON.parse(advAdjPayArray);
                    $.each(advAdjPayArray, function (key, item) {
                        let row = $('#bill_adv_adj tbody tr[data-id="' + item.id_issue_receipt + '"]');
                        row.find('.adj_amount').val(item.adj_amount);
                    })
                }
                //    $('#edit_bill_adv_adj > tbody').append(html);
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Your Wallet Amount is 0" });
            }
        }
    });
}
function get_edit_advance_details(paidData) {
    var paidData1 = JSON.parse(paidData);
    var adv_adj_value = paidData1;
    $('#bill_adv_adj > tbody').empty();
    my_Date = new Date();
    $.ajax({
        url: base_url + 'index.php/admin_ret_billing/get_advance_details/?nocache=' + my_Date.getUTCSeconds(),
        dataType: "json",
        method: "POST",
        data: { 'bill_cus_id': $('#id_customer').val() },
        success: function (data) {
            // $('#edit_bill_adv_adj > tr').remove();
            if (data.length > 0) {
                rec_id_ret_wallet = data[0].id_ret_wallet;
                total_sum_adjusted_bill_amount = 0;
                totalSumAdjBillAmtEdit = 0;
                $.each(data, function (key, items) {
                    total_sum_adjusted_bill_amount += parseInt((parseFloat(items.amount).toFixed(2)));
                });
                console.log(total_sum_adjusted_bill_amount);
                if (total_sum_adjusted_bill_amount > 0) {
                    $('#adv-adj-confirm-add').modal('show');
                    var row = "";
                    var html = '';
                    var metal_rate = $('.per-grm-sale-value').html();
                    var weight_amt = parseFloat(data.weight * data.rate_per_gram);
                    $('#id_ret_wallet').val(data.id_ret_wallet);
                    $.each(data, function (key, items) {
                        var advAmtWithPaid = parseFloat(items.amount) + parseFloat(adv_adj_value[key].payment_amount);
                        if (ctrl_page[0] == "payment" && ctrl_page[1] == 'list') {
                            totalSumAdjBillAmtEdit += parseInt((advAmtWithPaid));
                        }
                        html += '<tr>' +
                            '<td><input type="checkbox" class="id_issue_receipt"  name="adv_adj[id_issue_receipt]" value="' + items.id_issue_receipt + '"><input type="hidden" class="id_ret_wallet" value="' + items.id_ret_wallet + '"></td>' +
                            '<td><div class="adv_bill_no" value="' + items.bill_no + '">' + items.bill_no + '</div></td>' +
                            '<td><div class="advance_amount" >' + items.amount + '</div> <input type="hidden" class="adv_amount" value="' + advAmtWithPaid + '"></td>' +
                            '<td><input type="number" class="form-control adj_amount" name="adv_adj[adj_amount]" value="' + adv_adj_value[key].payment_amount + '" ></td>' +
                            '<td><input type="number" class="form-control blc_amount" name="adv_adj[blc_amount]" value="" readonly></td>' +
                            '</tr>';
                        // html += '<tr>'
                        //     + '<td><input type="checkbox" class="id_issue_receipt"  name="adv_adj[id_issue_receipt]" value="' + items.id_issue_receipt + '"><input type="hidden" class="id_ret_wallet" value="' + items.id_ret_wallet + '"></td>'
                        //     + '<td><div class="adv_bill_no" value="' + items.bill_no + '">' + items.bill_no + '</div></td>'
                        //     + '<td><div class="advance_amount" >' + items.total_amount + '</div></td>'
                        //     + '<td><input type="number" class="form-control adj_amount" name="adv_adj[adj_amount]" value="" ></td>'
                        //     + '<td><input type="number" class="form-control blc_amount" name="adv_adj[blc_amount]" value="' + parseFloat(total_sum_adjusted_bill_amount).toFixed(2) + '" readonly></td>'
                        //     + '</tr>';
                    });
                    $('.total_adv_amt').html(parseFloat(total_sum_adjusted_bill_amount).toFixed(2));
                    $('.total_bill_amt').html(parseFloat($('#payment_amt').val()).toFixed(2));
                    $('#bill_adv_adj > tbody').append(html);
                    if (ctrl_page[0] == "payment" && ctrl_page[1] == 'list') {
                        $('#bill_adv_adj >tbody').append("<input type='hidden' id='editTotalbalamt' value='" + totalSumAdjBillAmtEdit + "'>");
                    }
                } else {
                    $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Your Wallet Amount is 0" });
                }
            } else {
                $('#adv-adj-confirm-add').modal('show');
                console.log(data);
            }
        }
    });
}
$(document).on('change', ".id_issue_receipt", function (e) {
    var row = $(this).closest('tr');
    var advance_amount = row.find('.advance_amount').html();
    if (row.find('.id_issue_receipt').is(':checked')) {
        row.find('.adj_amount').val(parseFloat(advance_amount).toFixed(2));
    } else {
        row.find('.adj_amount').val(0);
    }
    calculate_advance_adjust_amount();
});
function calculate_advance_adjust_amount() {
    var total_amount = 0;
    adjusted_amt = 0;
    balance_amt = 0;
    $('#adv-adj-confirm-add .modal-body #bill_adv_adj > tbody  > tr').each(function (index, tr) {
        var row = $(this).closest('tr');
        if (row.find('.id_issue_receipt').is(':checked')) {
            adjusted_amt += (isNaN(row.find('.adj_amount').val()) || (row.find('.adj_amount').val() == '') ? 0 : parseFloat(row.find('.adj_amount').val()));
            balance_amt += (isNaN(row.find('.blc_amount').val()) || (row.find('.blc_amount').val() == '') ? 0 : parseFloat(row.find('.blc_amount').val()));
        }
    });
    $('.total_adj_adv_amt').html(parseFloat(adjusted_amt).toFixed(2));
    // esakki 19-09
    total_amount = $('.total_adv_amt').html();
    // $('.total_blc_amt').html(parseFloat(balance_amt).toFixed(2));
    if (ctrl_page[0] == "payment" && ctrl_page[1] == 'list') {
        // $('.editTotalbalamt').val((parseFloat('.total_adv_amt')- parseFloat()).toFixed(2));
        $('.total_blc_amt').html((parseFloat($('#editTotalbalamt').val()) - parseFloat(adjusted_amt)).toFixed(2));
    } else {
        $('.total_blc_amt').html((parseFloat(total_amount) - parseFloat(adjusted_amt)).toFixed(2));
    }
    calculate_model_payment(); //esakki
}
$(document).on('keyup', ".adj_amount", function (e) {
    var row = $(this).closest('tr');
    var advance_amount = parseFloat(row.find('.advance_amount').html());
    if (row.find('.adj_amount').val() != '' && row.find('.adj_amount').val() > 0) {
        row.find('.id_issue_receipt').prop('checked', true);
        if (parseFloat(advance_amount) < parseFloat(row.find('.adj_amount').val())) {
            row.find('.id_issue_receipt').prop('checked', false);
            row.find('.adj_amount').val(0);
            row.find('.blc_amount').val(0);
            $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Your Receipt Amount Exceed" });
        } else {
            row.find('.id_issue_receipt').prop('checked', true);
            if (ctrl_page[0] == "payment" && ctrl_page[1] == 'list') {
                var advAmtWithPaid = parseFloat(row.find('.adv_amount').val());
                row.find('.blc_amount').val(parseFloat(parseFloat(advAmtWithPaid) - parseFloat(row.find('.adj_amount').val())).toFixed(2));
            } else {
                row.find('.blc_amount').val(parseFloat(parseFloat(advance_amount) - parseFloat(row.find('.adj_amount').val())).toFixed(2));
            }
        }
    } else {
        row.find('.id_issue_receipt').prop('checked', false);
        row.find('.adj_amount').val(0);
        row.find('.blc_amount').val(0);
    }
    calculate_advance_adjust_amount();
});
$('input[type=radio][name="receipt[receipt_as]"]').change(
    function () {
        if (this.value == 1) {
            $('#esti_no').prop('disabled', true);
        } else {
            $('#esti_no').prop('disabled', false);
        }
    });
$('input[type=radio][name="store_receipt_as"]').change(function () {
    var metal_rate = $('.per-grm-sale-value').html();
    if (adv_adj_details.length > 0) {
        adv_adj_details[0].store_receipt_as = this.value;
        if (this.value == 1) {
            adv_adj_details[0].wallet_blc = $('.excess_amt').html();
        } else {
            //adv_adj_details[0].wallet_blc = parseFloat($('.excess_amt').html() / metal_rate).toFixed(4);
            adv_adj_details[0].wallet_blc = formatMetalWeight($('.excess_amt').html() / metal_rate);
        }
    }
    console.log(adv_adj_details);
});
/* $('#add_adv_adj').on('click',function(e){
     var total_adj_adv_amt=$('.total_adj_adv_amt').html();
     var total_bill_amt=$('.total_bill_amt').html();
     if(parseFloat(total_bill_amt)<parseFloat(total_adj_adv_amt))
     {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Please Enter The Valid Adjusted Amount"});
     }else{
         var advance_adj=[];
         $('#adv-adj-confirm-add .modal-body #bill_adv_adj > tbody  > tr').each(function(index, tr) {
         if($(this).find('.id_issue_receipt').is(':checked')){
             advance_adj.push({
                 'id_issue_receipt':$(this).find('.id_issue_receipt').val(),
                 'id_ret_wallet':$(this).find('.id_ret_wallet').val(),
                 'adj_amount':$(this).find('.adj_amount').val(),
                 'blc_amount':$(this).find('.blc_amount').val(),
             });
         }
         });
         $('#payment_modes > tbody >tr').each(function(bidx, brow){
             bill_card_pay_row = $(this);
             bill_card_pay_row.find('#tot_adv_adj').html($('.total_adj_adv_amt').html());
             $('#advance_muliple_receipt').val(advance_adj.length>0 ? JSON.stringify(advance_adj):'');
             bal_excss_amt = parseInt(parseInt(total_sum_adjusted_bill_amount).toFixed(2) - parseInt(adjusted_amt).toFixed(2)).toFixed(2);
             $('#excess_adv_amt').val(bal_excss_amt);
         });
         $('#adv-adj-confirm-add').modal('toggle');
         calculatePaymentCost();
     }
 });*/
$('#save_receipt_adv_adj').on('click', function (e) {
    var total_adj_adv_amt = $('.total_adj_adv_amt').html();
    var total_bill_amt = $('.total_bill_amt').html();
    var adv_cash_paid = 0;
    if (parseFloat(total_bill_amt) < parseFloat(total_adj_adv_amt)) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Please Enter The Valid Adjusted Amount" });
    } else {
        var advance_adj = [];
        $('#adv-adj-confirm-add .modal-body #bill_adv_adj > tbody  > tr').each(function (index, tr) {
            if ($(this).find('.id_issue_receipt').is(':checked')) {
                var adjusted = isNaN($(this).find(".adj_amount").val()) || $(this).find(".adj_amount").val() == "" ? 0 : $(this).find(".adj_amount").val();
                var cash_pay = isNaN($(this).find(".adv_cash_paid").val()) || $(this).find(".adv_cash_paid").val() == "" ? 0 : $(this).find(".adv_cash_paid").val();
                cash_pay = parseFloat(adjusted) <= parseFloat(cash_pay) ? adjusted : cash_pay;
                if (parseFloat($(this).find(".is_currentday_adv").val()) == 0) {
                    adv_cash_paid = adv_cash_paid + parseFloat(cash_pay);
                }
                advance_adj.push({
                    'id_issue_receipt': $(this).find('.id_issue_receipt').val(),
                    'id_ret_wallet': $(this).find('.id_ret_wallet').val(),
                    'adj_amount': $(this).find('.adj_amount').val(),
                    'blc_amount': $(this).find('.blc_amount').val(),
                    'cash_pay': cash_pay
                });
                $("#adv_total_cash_amt").val(adv_cash_paid);
            }
        });
        var max_cash_valid = validate_max_cash();
        if (max_cash_valid == true) {
            $('#payment_modes > tbody >tr').each(function (bidx, brow) {
                bill_card_pay_row = $(this);
                bill_card_pay_row.find('#tot_adv_adj').html($('.total_adj_adv_amt').html());
                $('#advance_muliple_receipt').val(advance_adj.length > 0 ? JSON.stringify(advance_adj) : '');
                bal_excss_amt = parseInt(parseInt(total_sum_adjusted_bill_amount).toFixed(2) - parseInt(adjusted_amt).toFixed(2)).toFixed(2);
                $('#excess_adv_amt').val(bal_excss_amt);
            });
            $('#adv-adj-confirm-add').modal('toggle');
            calculatePaymentCost();
        } else {
            $("#adv_total_cash_amt").val(0);
        }
    }
});
$('#close_add_adj').on('click', function (e) {
    $('#adv-adj-confirm-add .modal-body').find('#bill_adv_adj tbody').empty();
    $('.tot_bill_amt').html('');
    $('.adjusted_amt').html('');
    $('.excess_amt').html('');
});
function validate_max_cash() {
    let status = true;
    let max_cash_amt = $('#max_cash_amt').val();
    let cash_pay = isNaN($("#make_pay_cash").val()) || $("#make_pay_cash").val() == "" ? 0 : parseFloat($("#make_pay_cash").val());
    let chit_cash_paid = isNaN($("#chit_cash_paid").val()) || $("#chit_cash_paid").val() == "" ? 0 : parseFloat($("#chit_cash_paid").val());
    let chit_total_cash_paid = isNaN($("#chit_total_cash_paid").val()) || $("#chit_total_cash_paid").val() == "" ? 0 : parseFloat($("#chit_total_cash_paid").val());
    let adv_total_cash_amt = isNaN($("#adv_total_cash_amt").val()) || $.trim($("#adv_total_cash_amt").val()) == "" ? 0 : parseFloat($("#adv_total_cash_amt").val());
    max_cash_amt = parseFloat(max_cash_amt);
    cash_pay = cash_pay + chit_cash_paid + adv_total_cash_amt + chit_total_cash_paid;
    if (parseFloat($('#validate_max_cash').val()) == 1 ? ((parseFloat(cash_pay)) >= parseFloat(max_cash_amt)) : false) {
        $("#make_pay_cash").val(0);
        let max_payable = (parseFloat(max_cash_amt) - parseFloat(chit_cash_paid) - parseFloat(adv_total_cash_amt) - parseFloat(chit_total_cash_paid) - 1);
        $("#make_pay_cash").focus();
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Maximum cash amount for this payment is " + max_payable });
        status = false;
    }
    return status;
}
//  Depends on cost center settings, have to make the branch selection. [select the scheme accounts branch and make the field readonly] //HH 
function get_branchnames() {
    //$(".overlay").css('display','block');	
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/branch/branchname_list',
        dataType: 'json',
        success: function (data) {
            var id_branch = $('#id_branch').val();
            var branch_id = $("#login_branch").val();
            console.log(id_branch);
            // var sch_join_branch =  $('#sch_join_branch').val();		   
            $.each(data, function (key, item) {
                $("#select_branch,#branch_select").append(
                    $("<option></option>")
                        .attr("value", item.id_branch)
                        .text(item.name)
                );
            });
            $("#select_branch,#branch_select").select2({
                placeholder: "Select branch name",
                allowClear: true
            });
            $("#branch_select").select2(
                "val",
                branch_id != "" && branch_id > 0 ? branch_id : ""
            );
            $(".overlay").css("display", "none");
        }
    });
}
/* function format_date(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
  if (month.length < 2) month = "0" + month;
  if (day.length < 2) day = "0" + day;
  return [year, month, day].join("-");
} */
function format_date(date) {
    var d = new Date(date);
    var year = d.getFullYear();
    var month = ("0" + (d.getMonth() + 1)).slice(-2);
    var day = ("0" + d.getDate()).slice(-2);
    var hours = ("0" + d.getHours()).slice(-2);
    var minutes = ("0" + d.getMinutes()).slice(-2);
    var seconds = ("0" + d.getSeconds()).slice(-2);
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}
function edit_2(id) {
    // 		$('#payment_date').datetimepicker({ format: 'yyyy-mm-dd hh:ii:ss',timezone: 'GMT'});
    $('#edit_payment').on('hidden.bs.modal', function () {
        // Reset form fields
        resetModeTable();
    });
    $('#edit_payment').modal('show');
    $('#id_payment').val(id);
    $.ajax({
        url: base_url + 'index.php/payment/edit_payment/' + id,
        dataType: "json",
        method: "POST",
        data: { 'id_payment': id },
        success: function (data) {
            console.log(data);
            $.each(data.payData, function (key, item) {
                $('#addedby').val(item.added_by);
                $('#id_customer').val(item.id_customer);
                var gst_amount =
                    item.gst_amount != "" && item.gst_amount != null ?
                        item.gst_amount :
                        0;
                $("#payment_amt").val(
                    parseFloat(item.payment_amount) + parseFloat(gst_amount)
                ); // esakki 19-09
                $('#paymentstatus').val(item.payment_status);
                if (item.added_by == 0 || item.added_by == 3) {
                    $("#paymentstatus").prop('disabled', false);
                } else {
                    $("#paymentstatus").prop('disabled', true);
                }
                get_emp_branchwise();
                $('#prev_pay_status').val(item.payment_status);
                $('#payment_date').val(item.date_payment);
                $('#metal_rate').val(item.metal_rate);
                $('#current_metal_rate').val(item.metal_rate);
                $('#metal_weight').val(item.metal_weight);
                $('#payment_ref_no').val(item.payment_ref_number);
                $('#payment_mode').val(item.payment_mode);
                $('#prev_pay_mode').val(item.payment_mode);
                $('#remark').val(item.remark);
                $('#scheme_type').val(item.scheme_type);
                $('#flexible_sch_type').val(item.flexible_sch_type);
                $('#id_pay_mode_details').val(item.id_pay_mode_details);
                $('#allow_cash_limit').val(item.allow_cash_limit);
                $('#disable_acc_payments').val(item.disable_acc_payments);
                $('#chit_number').val(item.chit_number);
                $("#select_employee").select2().val(item.id_employee).trigger("change");
                if (item.is_editing_enabled == 1 && item.metalrate_edit_date) {
                    $('#make_pay_cash').val('');
                    $('#make_pay_cash').prop('disabled', true);
                }
            });
            //Set total amount on payment form mode wise
            $.each(data.total, function (key, item) {
                if (key == 'CSH') {
                    $('#make_pay_cash').val(item);
                } else if (key == 'ADV_ADJ') {
                    $('#tot_adv_adj').html(item);
                } else {
                    $('.' + key).text(item);
                }
            });
            //lines added by Durga starts here - 12.04.2023 
            $.each(data.modeData, function (key, item) {
                //alert("key : "+key);
                get_payMode_Data(id, key);
            });
            //lines added by Durga ends here - 12.04.2023 
            calculatePaymentCost();
        }
    });
}
/* 	$("#netbankmodal").on("click",function(){
       var id_payment = $('#id_payment').val();
       var mode = $('#netbankmodal').val();
       var modal_mode = $('#netBankMode').val();
       get_payMode_Data(id_payment,modal_mode);
    });
    $("#cheque_modal").on("click",function(){
       var id_payment = $('#id_payment').val();
       var mode = $('#cheque_modal').val();
       var modal_mode = $('#chequeMode').val();
       get_payMode_Data(id_payment,modal_mode);
    });
    $("#card_detail_modal").on("click",function(){
       var mode = this.value;
       var id_payment = $('#id_payment').val();
       var modal_mode = $('#cardTypeMode').val();
    //    get_payMode_Data(id_payment,modal_mode);
    });
    //voucher mode starts
    $("#vch_detail_modal").on("click",function(){
       var mode = this.value;
       var id_payment = $('#id_payment').val();
       var modal_mode = $('#vchMode').val();
    //    get_payMode_Data(id_payment,modal_mode);
    }); */
//voucher mode ends
/*	$("#card_detail_modal").on("click",function(){
       var mode = this.value;
       var id_payment = $('#id_payment').val();
       var modal_mode = $('#cardTypeMode').val();
       get_payMode_Data(id_payment,modal_mode);
    }); */
function get_payMode_Data(id_payment, modal_mode) {
    /* banklist = '';
     $.each(payment_bank_details, function (pkey, item) 
     {
         banklist = "<option value='4'>FED 331227</option><option value='5'>FED 202952</option>";
     });*/
    $.ajax({
        url: base_url + 'index.php/payment/edit_payment/' + id_payment,
        dataType: "json",
        method: "POST",
        data: { 'id_payment': id_payment },
        success: function (data) {
            $.each(data.total, function (key, item) {
                if (key == 'CC') {
                    $('.cc_total_amt').html(item);
                } else if (key == 'DC') {
                    $('.dc_total_amt').html(item);
                } else if (key == 'CHQ') {
                    $('.chq_total_amount').html(item);
                } else if (key == 'NB') {
                    $('.nb_total_amount').html(item);
                } else if (key == 'VCH') {
                    $('.vch_total_amount').html(item);
                }
            });
            // esakki 17-09
            var cc_total_amt = $('.cc_total_amt').html() != "" ? $('.cc_total_amt').html() : 0;
            var dc_total_amt = $('.dc_total_amt').html() != "" ? $('.dc_total_amt').html() : 0;
            $('.cc_total_amount').html(parseFloat(parseFloat(cc_total_amt) + parseFloat(dc_total_amt)).toFixed(2));
            $.each(data.modeData, function (key, item) {
                if ((item.length) > 0) {
                    if (key == modal_mode) {
                        if (modal_mode == 'NB') {
                            $("#net_bank_details tbody tr").remove();
                            nb_payment = [];
                            $.each(item, function (mode, val) {
                                var values = val;
                                var banklist = '';
                                var devicelist = '';
                                var typelist = '';
                                var total_amount = 0;
                                var nb_amount = 0;
                                $.each(payment_bank_details, function (pkey, item) {
                                    if (item.id_bank == values.id_bank) {
                                        var bnk_selected = 'selected';
                                    } else {
                                        var bnk_selected = '';
                                    }
                                    banklist += "<option value='" + item.id_bank + "' " + bnk_selected + ">" + item.acc_number + "</option>";
                                });
                                $.each(payment_device_details, function (pkey, item) {
                                    if (item.id_device == values.id_pay_device) {
                                        var dev_selected = 'selected';
                                    } else {
                                        var dev_selected = '';
                                    }
                                    devicelist += "<option value='" + item.id_device + "' " + dev_selected + " >" + item.device_name + "</option>";
                                });
                                let nb_type = { RTGS: '1', IMPS: '2', UPI: '3' };
                                var nbtype_selected = '';
                                $.each(nb_type, function (pkey, item) {
                                    rows_added = $('#net_bank_details tbody tr').length;
                                    var nb_row_count = rows_added++;
                                    if (item == values.NB_type) {
                                        nbtype_selected = 'selected';
                                        // $('.device').css('display','block');
                                    } else {
                                        nbtype_selected = '';
                                        // $('.device').css('display','none');
                                    }
                                    if (values.NB_type == '3') {
                                        //$('.device').css('display','block');
                                        $('.device_' + nb_row_count).css('display', 'block');
                                        //$('.upi_type').css('display','none');
                                        $('.upi_type_' + nb_row_count).css('display', 'none');
                                    } else {
                                        //	$('.device').css('display','none');
                                        $('.device_' + nb_row_count).css('display', 'none');
                                        //$('.upi_type').css('display','block');
                                        $('.upi_type_' + nb_row_count).css('display', 'block');
                                    }
                                    typelist += "<option value='" + item + "' " + nbtype_selected + ">" + pkey + "</option>";
                                });
                                //append rows
                                var row = "";
                                rows_added = $('#net_bank_details tbody tr').length;
                                var nb_row_count = rows_added++;
                                row += '<tr>'
                                    //  +'<td><select name="nb_details[nb_type][]" class="nb_type" ><option value="">Select Type</option>'+typelist+'</select></td>';
                                    +
                                    '<td><select name="nb_details[nb_type][]" class="nb_type" id="nb_type_' + nb_row_count + '" onchange="setdevice(this.value,' + nb_row_count + ')"><option value="">Select Type</option>' + typelist + '</select></td>';
                                if (values.NB_type == '3') {
                                    // row+='<td class="device" style="display:block;"><select name="nb_details[nb_device][]" class="id_device" style="width:150px;"><option value="">Select Device</option>'+devicelist+'</select></td>';
                                    //   row+='<td class="upi_type" style="display:none;"><select name="nb_details[nb_bank][]" class="id_bank" style="width:150px;"><option value="">Select Bank</option>'+banklist+'</select></td>';
                                    row += '<td class="device" id="device_' + nb_row_count + '" style="display:block;"><select name="nb_details[nb_device][]" class="id_device" style="width:150px;"><option value="">Select Device</option>' + devicelist + '</select></td>';
                                    row += '<td class="upi_type" id="upi_type_' + nb_row_count + '" style="display:none;"><select name="nb_details[nb_bank][]" class="id_bank" style="width:150px;"><option value="">Select Bank</option>' + banklist + '</select></td>';
                                } else {
                                    //   	row+='<td class="device" style="display:none;"><select name="nb_details[nb_device][]" class="id_device" style="width:150px;"><option value="">Select Device</option>'+devicelist+'</select></td>';
                                    // row+='<td class="upi_type" style="display:block;"><select name="nb_details[nb_bank][]" class="id_bank" style="width:150px;"><option value="">Select Bank</option>'+banklist+'</select></td>';
                                    row += '<td class="device" id="device_' + nb_row_count + '" style="display:none;"><select name="nb_details[nb_device][]" class="id_device" style="width:150px;"><option value="">Select Device</option>' + devicelist + '</select></td>';
                                    row += '<td class="upi_type" id="upi_type_' + nb_row_count + '" style="display:block;"><select name="nb_details[nb_bank][]" class="id_bank" style="width:150px;"><option value="">Select Bank</option>' + banklist + '</select></td>';
                                }
                                row +=
                                    '<td><input type="text" step="any" class="form-control datemask date nb_date" name="nb_details[nb_date][]" value="' +
                                    val.net_banking_date +
                                    '" data-date-format="yyyy-mm-dd" style="width: 100px;"></td>'
                                    //	+'<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]" value="'+val.payment_ref_number+'" /></td>'
                                    +
                                    '<td><input type="number" step="any" class="ref_no" id="nbref_no_' + nb_row_count + '" name="nb_details[ref_no][]" value="' + val.payment_ref_number + '" onkeyup="check_ref_no(this,this.value,' + nb_row_count + ')" /><span class="error" id="nb_ref_span_' + nb_row_count + '"></span> </td>' +
                                    '<td><input type="number" step="any" class="amount" name="nb_details[amount][]" value="' + val.payment_amount + '" onkeypress="return /[0-9]/i.test(event.key)"></td>' +
                                    '<td><a href="#" onClick="removeNb_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
                                    '</tr>';
                                //console.log(row);
                                $('#net_bank_details tbody').append(row);
                                /*
                                     nb_amount+=parseFloat(val.payment_amount);
                                    nb_payment.push({'nb_type':nb_edit_type,'ref_no':val.payment_ref_number,'amount':val.payment_amount,'bank':val.id_bank,'device':val.id_pay_device,'acc_no':$('.nb_bank'+count).find(':selected').text()});
                                */
                                nb_payment.push({
                                    'nb_type': val.NB_type,
                                    'ref_no': val.payment_ref_number,
                                    'amount': val.payment_amount,
                                    'id_bank': val.id_bank,
                                    'nb_date': val.net_banking_date,
                                    'device': val.id_pay_device
                                });
                                console.log(nb_payment);
                                var date = new Date();
                                var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                                $("#net_bank_details").find(".nb_date").datepicker({
                                    dateFormat: "yyyy-mm-dd",
                                    endDate: today
                                });
                                $('#payment_modes > tbody >tr').each(function (bidx, brow) {
                                    bill_card_pay_row = $(this);
                                    bill_card_pay_row.find('.NB').html($(".nb_total_amount").html());
                                    //$(".NB").html(nb_amount);
                                    bill_card_pay_row.find('#nb_payment').val(nb_payment.length > 0 ? JSON.stringify(nb_payment) : '');
                                    //nb_values=[];
                                });
                                calculatePaymentCost();
                            });
                        }
                        if (modal_mode == 'CHQ') {
                            $("#chq_details tbody tr").remove();
                            chq_payment = [];
                            var chq_amount = 0;
                            $.each(item, function (mode, val) {
                                var bank_list = '';
                                $.each(bank_details, function (pkey, item) {
                                    if (item.acc_number == val.bank_name) {
                                        bank_list += "<option selected value='" + item.acc_number + "'>" + item.acc_number + "</option>";
                                    } else {
                                        bank_list += "<option value='" + item.acc_number + "'>" + item.acc_number + "</option>";
                                    }
                                });
                                // esakki 17-09
                                rows_added = $('#chq_details tbody tr').length;
                                var chq_row_count = rows_added++;
                                //append rows
                                var row = "";
                                row += '<tr>' +
                                    '<td><input class="cheque_date" data-date-format="dd-mm-yyyy hh:mm:ss" name="cheque_details[cheque_date][]" type="text" placeholder="Cheque Date" value="' + val.cheque_date + '" /></td>'
                                    // +'<td><input name="cheque_details[bank_name][]" type="text" class="bank_name" value="'+val.bank_name+'" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>'
                                    +
                                    '<td><select class="bank_name" name="cheque_details[bank_name][]">' + bank_list + '</select></td> ' +
                                    '<td><input name="cheque_details[bank_branch][]" type="text" class="bank_branch" value="' + val.bank_branch + '" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>'
                                    // +'<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]" value="'+val.cheque_no+'" /></td>' 
                                    +
                                    '<td><input type="number" step="any" class="cheque_no" id="chq_no_' + chq_row_count + '" name="cheque_details[cheque_no][]" value="' + val.cheque_no + '" onkeyup="check_ref_no(this,this.value,' + chq_row_count + ')" /><span class="error" id="chq_ref_span_' + chq_row_count + '" ></span></td>' // esakki 18-09
                                    +
                                    '<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]" value="' + val.bank_IFSC + '" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"/></td>' +
                                    '<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]" value="' + val.payment_amount + '"/></td>' +
                                    '<td><a href="#" onClick="removeChq_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
                                    '</tr>';
                                //	console.log(row);		
                                //lines added by Durga starts here - 12.04.2023 
                                $('#chq_details tbody').append(row);
                                chq_amount += parseFloat(val.payment_amount);
                                chq_payment.push({
                                    'cheque_date': val.cheque_date,
                                    'cheque_no': val.cheque_no,
                                    'bank_branch': val.bank_branch,
                                    'bank_name': val.bank_name,
                                    'payment_amount': val.payment_amount,
                                    'bank_IFSC': val.bank_IFSC
                                });
                                console.log(chq_payment);
                                $('#payment_modes > tbody >tr').each(function (bidx, brow) {
                                    bill_card_pay_row = $(this);
                                    bill_card_pay_row.find('.CHQ').html($('.chq_total_amount').html());
                                    bill_card_pay_row.find('#chq_payment').val(chq_payment.length > 0 ? JSON.stringify(chq_payment) : '');
                                });
                                //lines added by Durga ends here - 12.04.2023 
                                calculatePaymentCost();
                            });
                        }
                        //voucher mode starts here 
                        if (modal_mode == 'VCH') {
                            $("#vch_details tbody tr").remove();
                            vch_payment = [];
                            var vch_amount = 0;
                            $.each(item, function (mode, val) {
                                //append rows
                                var row = "";
                                row += '<tr>' +
                                    '<td><input type="number" step="any" class="voucher_code" name="vch_details[card_no][]" value="' + val.card_no + '" /></td>' +
                                    '<td><input type="number" step="any" class="voucher_amount" name="vch_details[payment_amount][]" value="' + val.payment_amount + '" /></td>' +
                                    '<td><a href="#" onClick="removevch_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
                                    '</tr>';
                                //	console.log(row);		
                                //lines added by Durga starts here - 12.04.2023 
                                $('#vch_details tbody').append(row);
                                vch_amount += parseFloat(val.payment_amount);
                                vch_payment.push({
                                    'card_no': val.card_no,
                                    'payment_amount': val.payment_amount
                                });
                                console.log(vch_payment);
                                $('#payment_modes > tbody >tr').each(function (bidx, brow) {
                                    bill_card_pay_row = $(this);
                                    bill_card_pay_row.find('.VCH').html($('.vch_total_amount').html());
                                    bill_card_pay_row.find('#vch_payment').val(vch_payment.length > 0 ? JSON.stringify(vch_payment) : '');
                                });
                                //lines added by Durga ends here - 12.04.2023 
                                calculatePaymentCost();
                            });
                        }
                        //voucher mode ends here
                        if (modal_mode == 'Card') {
                            $("#card_details tbody tr").remove();
                            var device_list = '';
                            var type_list = '';
                            var cc_amount = 0;
                            var dc_amount = 0;
                            card_payment = [];
                            $.each(item, function (mode, val) {
                                $.each(payment_device_details, function (pkey, item) {
                                    if (item.id_device == val.id_pay_device) {
                                        var selected = 'selected';
                                    } else {
                                        var selected = '';
                                    }
                                    device_list += "<option value='" + item.id_device + "' " + selected + ">" + item.device_name + "</option>";
                                });
                                //append rows
                                if (val.payment_mode == 'CC') {
                                    var cc_select = 'selected';
                                    var dc_select = '';
                                    cc_amount += parseFloat(val.payment_amount);
                                    //type_list+= "<option value= '1' selected>CC</option><option value='2'>DC</option>";
                                } else {
                                    dc_amount += parseFloat(val.payment_amount);
                                    //alert("dc" +val.payment_amount);
                                    var dc_select = 'selected';
                                    var cc_select = '';
                                    // type_list+= "<option value= '1'>CC</option><option value= '2' selected>DC</option>";
                                }
                                if (val.card_type == '1') {
                                    var rupay_select = 'selected';
                                } else if (val.card_type == '2') {
                                    var visa_select = 'selected';
                                } else if (val.card_type == '3') {
                                    var mastro_select = 'selected';
                                } else if (val.card_type == '4') {
                                    var master_select = 'selected';
                                }
                                var card_rows_added = $('#card_details tbody tr').length;
                                var card_count = card_rows_added++;
                                var row = "";
                                row += '<tr>' +
                                    '<td><select name="card_details[card_name][]" class="card_name"><option value="1" ' + rupay_select + '>RuPay</option><option value="2" ' + visa_select + '>VISA</option><option value="3" ' + mastro_select + '>Mastro</option><option value="4" ' + master_select + '>Master</option></select></td>'
                                    // +'<td><select name="card_details[card_type][]" class="card_type">'+type_list+'</select></td>'
                                    +
                                    '<td><select name="card_details[card_type][]" class="card_type"><option value="1" ' + cc_select + '>CC</option><option value="2" ' + dc_select + '>DC</option></select></td>' +
                                    '<td><select class="form-control id_device" name="card_details[id_device][]" style="width: 100px !important;">' + device_list + '</select></td> ' +
                                    '<td><input type="number" step="any" class="card_no" name="card_details[card_no][]" value="' + val.card_no + '" /></td>' +
                                    '<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]" value="' + val.payment_amount + '" /></td>'
                                    //+'<td><input type="text" step="any" class="ref_no" name="card_details[ref_no][]" value="'+val.payment_ref_number+'" /></td>'
                                    +
                                    '<td><input type="number" step="any" class="ref_no" id="cardref_no_' + card_count + '" onkeyup="check_ref_no(this,this.value,' + card_count + ')" name="card_details[ref_no][]"  value="' + val.payment_ref_number + '" /><span class="error" id="ref_span_' + card_count + '" ></span></td>' +
                                    '<td><a href="#" onClick="removeCC_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' +
                                    '</tr>';
                                console.log(row);
                                $('#card_details tbody').append(row);
                                //lines added by Durga starts here - 12.04.2023 
                                card_payment.push({
                                    'card_name': val.card_type,
                                    'id_device': val.id_pay_device,
                                    'card_type': val.payment_mode == 'CC' ? 1 : 2,
                                    'card_no': val.card_no,
                                    'card_amt': val.payment_amount,
                                    'ref_no': val.payment_ref_number
                                });
                                console.log(card_payment);
                                //lines added by Durga ends here - 12.04.2023 
                                // calculatePaymentCost();
                            });
                            $('.cc_total_amt').html(parseFloat(cc_amount).toFixed(2));
                            $('.dc_total_amt').html(parseFloat(dc_amount).toFixed(2));
                            $('.cc_total_amount').html(parseFloat(parseFloat(cc_amount) + parseFloat(dc_amount)).toFixed(2));
                            //lines added by Durga starts here - 12.04.2023 
                            $('#payment_modes > tbody >tr').each(function (bidx, brow) {
                                bill_card_pay_row = $(this);
                                bill_card_pay_row.find('.CC').html($('.cc_total_amt').html());
                                bill_card_pay_row.find('.DC').html($('.dc_total_amt').html());
                                bill_card_pay_row.find('#card_payment').val(card_payment.length > 0 ? JSON.stringify(card_payment) : '');
                            });
                            //lines added by Durga ends here - 12.04.2023 
                        }
                        if (modal_mode == 'ADV_ADJ') {
                            $('#bill_adv_adj tbody tr').remove();
                            adj_payment = [];
                            var adj_amount = 0;
                            console.log(item);
                            // get_edit_advance_details(item);
                            $('#adv_adj_modal').attr("onclick", "get_edit_advance_details('" + JSON.stringify(item) + "')");
                            //  console.log(item);
                            var adv_adj_value = item;
                            $('#bill_adv_adj > tbody').empty();
                            my_Date = new Date();
                            $.ajax({
                                url: base_url + 'index.php/admin_ret_billing/get_advance_details/?nocache=' + my_Date.getUTCSeconds(),
                                dataType: "json",
                                method: "POST",
                                data: { 'bill_cus_id': $('#id_customer').val() },
                                success: function (data) {
                                    // $('#edit_bill_adv_adj > tr').remove();
                                    if (data.length > 0) {
                                        rec_id_ret_wallet = data[0].id_ret_wallet;
                                        total_sum_adjusted_bill_amount = 0;
                                        $.each(data, function (key, items) {
                                            total_sum_adjusted_bill_amount += parseInt((parseFloat(items.amount).toFixed(2)));
                                        });
                                        // console.log(total_sum_adjusted_bill_amount);
                                        // console.log(data);
                                        if (total_sum_adjusted_bill_amount > 0) {
                                            // $('#adv-adj-confirm-add').modal('show');
                                            var row = "";
                                            var html = '';
                                            var metal_rate = $('.per-grm-sale-value').html();
                                            var weight_amt = parseFloat(data.weight * data.rate_per_gram);
                                            $('#id_ret_wallet').val(data.id_ret_wallet);
                                            $.each(data, function (key, items) {
                                                // console.log(key);
                                                // console.log(adv_adj_value[0].payment_amount);
                                                // console.log(parseFloat(items.amount) + parseFloat(adv_adj_value[0].payment_amount));
                                                var advAmtWithPaid = parseFloat(items.amount) + parseFloat(adv_adj_value[key].payment_amount);
                                                html += '<tr>' +
                                                    '<td><input type="checkbox" class="id_issue_receipt"  name="adv_adj[id_issue_receipt]" value="' + items.id_issue_receipt + '"><input type="hidden" class="id_ret_wallet" value="' + items.id_ret_wallet + '"></td>' +
                                                    '<td><div class="adv_bill_no" value="' + items.bill_no + '">' + items.bill_no + '</div></td>' +
                                                    '<td><div class="advance_amount" >' + items.amount + '</div> <input type="hidden" class="adv_amount" value="' + advAmtWithPaid + '"></td>' +
                                                    '<td><input type="number" class="form-control adj_amount" name="adv_adj[adj_amount]" value="' + adv_adj_value[key].payment_amount + '" ></td>' +
                                                    '<td><input type="number" class="form-control blc_amount" name="adv_adj[blc_amount]" value="" readonly></td>' +
                                                    '</tr>';
                                            });
                                            $('.total_adv_amt').html(parseFloat(total_sum_adjusted_bill_amount).toFixed(2));
                                            $('.total_bill_amt').html(parseFloat($('#payment_amt').val()).toFixed(2));
                                            $('#bill_adv_adj > tbody').append(html);
                                        } else {
                                            $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + "Your Wallet Amount is 0" });
                                        }
                                    } else {
                                        $('#adv-adj-confirm-add').modal('show');
                                        console.log(data);
                                    }
                                }
                            });
                        }
                        /* if(modal_mode == 'DC'){
                             $("#card_details tbody tr").remove(); 
                             $.each(item, function (mode,val){
                             //append rows
                            var row = "";
                             row += '<tr>'
                                         +'<td><select name="card_details[card_name][]" class="card_name"><option value="1">RuPay</option><option value="2">VISA</option><option value="3">Mastro</option><option value="4">Master</option></select></td>'
                                         +'<td><select name="card_details[card_type][]" class="card_type"><option value="1">CC</option><option value="2">DC</option></select></td>'
                                         +'<td><input type="number" step="any" class="card_no" name="card_details[card_no][]" value="'+val.card_no+'" /></td>'
                                         +'<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]" value="'+val.payment_amount+'"/></td>' 
                                         +'<td><a href="#" onClick="removeCC_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' 
                                     +'</tr>';
                             $('#card_details tbody').append(row);
                            });
                         }
                          */
                    } else {
                        if (modal_mode == 'NB') {
                            // $("#net_bank_details tbody tr").remove(); 
                            if ($('#net_bank_details > tbody > tr').length == 0) {
                                create_new_empty_net_banking_row();
                            }
                        }
                        /*   if(modal_mode == 'CHQ'){
                            $("#chq_details tbody tr").remove(); 
                            if(validateChqDetailRow()){
                                create_new_empty_chqpay_row();
                            } 
                         }
                         if(modal_mode == 'Card'){
                            alert("2");
                            $("#card_details tbody tr").remove(); 
                            if(validateCardDetailRow()){
                                create_new_empty_cardpay_row();
                            }
                        }  */
                    }
                }
            });
            $.each(data.payData, function (key, item) {
                $('#allow_cash_limit').val(item.allow_cash_limit);
                $('#disable_acc_payments').val(item.disable_acc_payments);
                $('#chit_number').val(item.chit_number);
            })
        }
    });
}
$("#update_payment").on("click", function () {
    //set payment mode
    /*if($(".CC").val() != "")
    {
        var mode = 'CC';
        alert(mode);
        //$("#payment_mode").val(mode);
    }else if($(".CC").val() == "" && $(".DC").val() == "" && $(".CHQ").val() == "" && $(".NB").val() == "" && $("#tot_adv_adj").val() == "")
    {
        var mode = $('#prev_pay_mode').val();
         //$("#payment_mode").val(mode);
    }
    alert(mode);*/
    if ($('.bal_amount').html() == 0) {
        if ($('#paymentstatus').val() == null || $('#paymentstatus').val() == "") {
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select the Payment Status.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
            return false;
        } else if ($('#payment_date').val() == null || $('#payment_date').val() == "") {
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select the Payment Date.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
            return false;
        }
        // else if($('#payment_ref_no').val()==null || $('#payment_ref_no').val()=="")
        // {
        // 	msg='<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Payment Ref No.</div>';
        // 	$("div.overlay").css("display", "none");
        // 	$('#error-msg1').html(msg);
        // 	return false;
        // }
        else if ($('#payment_mode').val() == null || $('#payment_mode').val() == "") {
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Payment Mode.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
            return false;
        } else if ($('#remark').val() == null || $('#remark').val() == "") {
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Remark.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
            return false;
        } else if ($("#id_employee1").val() == null || $("#id_employee1").val() == "") {
            msg =
                '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Select the employee.</div>';
            $("div.overlay").css("display", "none");
            $("#error-msg1").html(msg);
            return false;
        } else if ($('#scheme_type').val() != 0 && $('#flexible_sch_type').val() != 1 ? $('#metal_rate').val() == null || $('#metal_rate').val() == "" : false) {
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Metal Rate.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
            return false;
        } else if ($('#scheme_type').val() != 0 && $('#flexible_sch_type').val() != 1 ? $('#metal_weight').val() == null || $('#metal_weight').val() == "" : false) {
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Metal Weight.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
            return false;
        } else {
            var postdata = $('#update_pay_form').serialize();
            $("div.overlay").css("display", "block");
            $.ajax({
                url: base_url + 'index.php/payment/update_payment/' + $('#id_payment').val(),
                dataType: "json",
                method: "POST",
                data: postdata,
                //data: {'postdata':form_data,'prev_pay_status':$("#prev_pay_status").val(),'payment_status':$('#paymentstatus').val(),'payment_date':$('#payment_date').val(),'metal_rate':$('#metal_rate').val(),'metal_weight':$('#metal_weight').val(),'payment_ref_no':$('#payment_ref_no').val(),'payment_mode':$('#payment_mode').val(),'prev_pay_mode':$('#prev_pay_mode').val(),'remark':$('#remark').val(),'added_by':$('#addedby').val(),'payment_amount':$('#payment_amt').val(),'id_pay_mode_details':$('#id_pay_mode_details').val()},
                success: function (data) {
                    location.reload();
                    $("div.overlay").css("display", "none");
                }
            });
        }
    } else {
        msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Total amount should equal the payment amount....</div>';
        $("div.overlay").css("display", "none");
        $('#error-msg1').html(msg);
        return false;
    }
});
function get_paymentModes() {
    my_Date = new Date();
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_payment/payment_modes?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache: false,
        success: function (data) {
            $.each(data, function (key, val) {
                $('#payment_mode').append(
                    $("<option></option>")
                        .attr("value", val.short_code)
                        .text(val.short_code)
                );
            });
            $("#payment_mode").select2({
                placeholder: "Select Payment Mode",
                allowClear: true
            });
            //$("#payment_mode").select2("val", ($('#payment_mode').val()!=null?$('#payment_mode').val():''));
        },
        error: function (error) {
            console.log(error);
        }
    });
}
function update_metal_weight() {
    var metal_rate = $('#metal_rate').val();
    var bal_amt = $('.bal_amount').html();
    var total_amt = $('.sum_of_amt').html();
    if (bal_amt == 0) {
        if (metal_rate != null || metal_rate != '' || metal_rate != 0 && total_amt != 0 || total_amt != '') {
            var wgt = total_amt / metal_rate;
            $('#metal_weight').val(wgt);
        } else {
            alert("Unable to proceed...")
        }
    } else {
        alert("You have balance amount...");
        $('#metal_weight').val('');
        $('#metal_rate').val('');
    }
}
//Added by Durga 22.05.2023 starts here
function handleCheckboxClick(clickedCheckbox) {
    console.log(clickedCheckbox);
    var allowed_advance = $("#sel_due").val();
    if (allowed_advance > 1) {
        var row = clickedCheckbox.parentNode.parentNode;
        var checkboxes = document.getElementsByName('adv_adj[id_issue_receipt]');
        var txtbx = document.getElementsByName('adv_adj[adj_amount]');
        var textboxes = row.querySelectorAll('input[type="number"]');
        console.log(row);
        checkboxes.forEach(function (checkbox) {
            if (checkbox !== clickedCheckbox) {
                console.log(checkbox);
                checkbox.disabled = clickedCheckbox.checked;
            }
        });
        txtbx.forEach(function (textbox) {
            textbox.disabled = clickedCheckbox.checked;
        });
        textboxes.forEach(function (textbox) {
            textbox.disabled = false;
        });
    }
}
//Added by Durga 22.05.2023 ends here
$("#date_Select").select2({
    allowClear: true
});
$('#date_Select').select2().on("change", function (e) {
    if (this.value != '') {
        var from_date = $('#payment_list1').text();
        var to_date = $('#payment_list2').text();
        var id_employee = $('#id_employee').text();
        var id_branch = $("#branch_Select").val();
        var id_status = $('#id_status').text();
        var id_customer = $('#customer_id').val();
        get_payment_list(from_date, to_date, id_branch, id_employee, id_status, id_customer);
    }
});
/* by clinton */
$('#qr_scan_scheme_account_id').on("change", function (e) {
    load_account_detail_qr(this.value);
});
function load_account_detail_qr(id) {
    clear_account_detail();
    $('#select_branch').empty();
    my_Date = new Date();
    $("#id_scheme_account").val(id);
    //show spinner
    $('.overlay').css('display', 'block');
    $("#scheme_account").val(id);
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_payment/ajax_account_detail_qr?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        data: { id_scheme_account: id },
        success: function (data) {
            console.log(data);
            if (Object.keys(data).length > 0) {
                if ($('#scheme_account').length > 0) {
                    $('#scheme_account').empty();
                    $.each(data.custmer_account, function (key, acc) {
                        $('#scheme_account').append(
                            $("<option></option>")
                                .attr("value", acc.id_scheme_account)
                                .text(acc.scheme_acc_number)
                        );
                    });
                    $(".eligible_walletamt").css("display", "none");
                    if (data.wallet_balance) {
                        console.log(data.wallet_balance);
                        $('.wallet_balance').val(parseFloat(data.wallet_balance.wal_balance));
                        $('.redeem_percent').val(parseFloat(data.wallet_balance.redeem_percent));
                        if ($('.wallet_balance').val() != '0') {
                            $(".eligible_walletamt").css("display", "block");
                        }
                        $('.wallet').val(parseFloat(data.wallet_balance.wal_balance));
                    }
                    $("#scheme_account").select2({
                        placeholder: "Select scheme account",
                        allowClear: true
                    });
                    $("#scheme_account").select2("val", ($('#id_scheme_account').val() != null ? $('#id_scheme_account').val() : ''));
                }
                $('#scheme_account').val((data.account.id_scheme_account));
                $('#id_scheme_account').val((data.account.id_scheme_account));
                $('#id_customer').val((data.account.id_customer));
                $('#mobile_number').val((data.account.mobile));
                //	$('#mobile_number').val((data.account.mobile+' '+data.account.firstname));
                console.log(data.account.cost_center);
                if (data.account.cost_center == 1 || data.account.cost_center == 2) {
                    $('#id_branch').val((data.account.id_branch));
                    $("#select_branch").attr("disabled", true);
                    var id_branch = $('#id_branch').val();
                    $.each(data, function (key, item) {
                        $('#select_branch').append(
                            $("<option></option>")
                                .attr("value", item.id_branch)
                                .text(item.name)
                        );
                    });
                    $("#select_branch").select2({
                        placeholder: "Select branch name",
                        allowClear: true
                    });
                }
                account_detail_view(data.account)
                $.AdminLTE.boxWidget.activate();
                $("#qr_scan_scheme_account_id").val('');
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + ' Invaild Account.' });
                clear_account_detail();
                $("#scheme_account").select2("val", '');
                $('#scheme_account').empty();
                $('#mobile_number').val('');
                $('#qr_scan_scheme_account_id').val('');
            }
            $('.overlay').css('display', 'none');
        },
        error: function (error) {
            console.log(error);
            //disable spinner
            $('.overlay').css('display', 'none');
        }
    });
}
//ends	
//to check a value is valid or not 
function isValid(data) {
    if (data == '') {
        return false;
    } else if (data == null) {
        return false;
    } else if (data == 'undefined') {
        return false;
    } else {
        return true;
    }
}
//cash payment restiction if limit reached... date ad: 17-11-2023 BY:#AB starts	
$('#make_pay_cash').on('keyup', function () {
    var cash_given = parseInt($('#make_pay_cash').val());
    var csh_limit = parseInt($('#allow_cash_limit').val());
    var disable_acc_payments = $('#disable_acc_payments').val();
    var chit_number = $('#chit_number').val();
    var sum_of_amt = parseInt($('.sum_of_amt').html() - cash_given);
    var bal_amount = parseInt($('.bal_amount').html() + cash_given);
    if (disable_acc_payments == 'Y') {
        var sum_of_amt = parseInt($('.sum_of_amt').html() - cash_given);
        var bal_amount = parseInt($('.bal_amount').html() + cash_given);
        $('.sum_of_amt').html('');
        $('.bal_amount').html('');
        $('#make_pay_cash').val('');
        // esakki 19-09
        if (ctrl_page[1] == 'list') {
            get_emp_branchwise();
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Scheme Account No - ' + chit_number + ' Reached CASH mode payment limit</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
        }
        $("#show_msg").html("Scheme Account No - " + chit_number + " Reached CASH mode payment limit");
        $('#pay_modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    } else if (disable_acc_payments == 'N' && csh_limit < cash_given) {
        var sum_of_amt = parseInt($('.sum_of_amt').html() - cash_given);
        var bal_amount = parseInt($('.bal_amount').html() + cash_given);
        $('.sum_of_amt').html('');
        $('.bal_amount').html('');
        $('#make_pay_cash').val('');
        if (ctrl_page[1] == 'list') {
            msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Scheme Account No - ' + chit_number + ' Allowed to pay only upto INR ' + csh_limit + ' in CASH mode.</div>';
            $("div.overlay").css("display", "none");
            $('#error-msg1').html(msg);
        }
        $("#show_msg").html("Scheme Account No - " + chit_number + " Allowed to pay only upto INR " + csh_limit + " in CASH mode.");
        $('#pay_modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    }
});
//casg limit ends		
// status filter --14-12-23-santhosh--start
$('#status_select').on('change', function (e) {
    if (this.value != '') {
        $("#id_status").val(this.value);
        $
    } else {
        $("#id_status").val('');
    }
});
$('#status_select').select2().on("change", function (e) {
    if (this.value != '') {
        $('#id_status').val(this.value);
        var from_date = $('#payment_list1').text();
        var to_date = $('#payment_list2').text();
        var id_branch = $('#id_branch').text();
        var id_employee = $('#id_employee').text();
        var id_status = $(this).val();
        var id_customer = $('#customer_id').val();
        get_payment_list(from_date, to_date, id_branch, id_employee, id_status, id_customer);
    }
});
function get_status() {
    //$(".overlay").css('display','block');	
    $.ajax({
        type: 'GET',
        url: base_url + 'index.php/admin_payment/get_status',
        dataType: 'json',
        success: function (data) {
            // console.log(data);
            var id_status = $('#id_status').val();
            // console.log(id_status);
            // var sch_join_branch =  $('#sch_join_branch').val();		   
            $.each(data, function (key, item) {
                // console.log(item.id_status);				  				  			   		
                $('#status_select').append(
                    $("<option></option>")
                        .attr("value", item.id_status_msg)
                        .text(item.payment_status)
                );
            });
            $("#status_select").select2({
                placeholder: "Select status",
                allowClear: true
            });
            $("#status_select").select2("val", ($('#id_status').val() != null ? $('#id_status').val() : ''));
            var selectid = $('#id_status').val();
            if (selectid != null && selectid > 0) {
                $('#id_status').val(selectid);
                $('.overlay').css('display', 'block');
            }
            //  $("#select_branch").select2("val",(id_branch!='' && id_branch>0?id_branch:''));	 
            // $(".overlay").css("display", "none");			
        }
    });
}
// status filter --14-12-23-santhosh--end
function edit_3(id, remark) {
    console.log(remark);
    $('#id_payment').val(id);
    $("#remark1").val(remark);
    $('#payment_date').datetimepicker({ format: 'yyyy-mm-dd hh:ii:ss', timezone: 'GMT' });
    $('#edit_payment1').modal('show');
}
$("#update_payment1").on("click", function () {
    // alert(1);
    if ($('#remark1').val() == null || $('#remark1').val() == "") {
        alert(2);
        msg = '<div class = "alert alert-danger"><a href="" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter the Remark.</div>';
        $("div.overlay").css("display", "none");
        $('#error-msg1').html(msg);
        return false;
    } else {
        // var postdata=$('#update_pay_form').serialize();
        var id = $('#id_payment').val();
        var remark = $('#remark1').val();
        $("div.overlay").css("display", "block");
        $.ajax({
            url: base_url + 'index.php/admin_payment/update_remark',
            dataType: "json",
            method: "POST",
            data: { 'id_payment': id, 'remark': remark },
            success: function (data) {
                console.log(data);
                location.reload();
            }
        });
    }
});
if (ctrl_page[1] == 'list') {
    $('#mobilenumber').on('keyup', function () {
        var mobile = $('#mobilenumber').val();
        if (mobile.length == 0) {
            $("#customer_id").val('');
        }
    });
    $("#mobilenumber").autocomplete({
        source: function (request, response) {
            var mobile = $("#mobilenumber").val();
            my_Date = new Date();
            $.ajax({
                url: base_url + "index.php/admin_customer/ajax_get_customers_list?nocache=" + my_Date.getUTCSeconds(),
                dataType: "json",
                type: 'POST',
                data: { 'mobile': mobile },
                success: function (data) {
                    var data = JSON.stringify(data);
                    data = JSON.parse(data);
                    var cus_list = new Array(data.length);
                    var i = 0;
                    data.forEach(function (entry) {
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
        select: function (e, i) {
            e.preventDefault();
            $("#mobilenumber").val(i.item.label);
            $("#customer_id").val(i.item.value);
            //$("#id_scheme_account").val(i.item.id_scheme_account);
            $('.overlay').css('display', 'block');
            if ($('#customer_id').val() != '') {
                var from_date = $('#payment_list1').text();
                var to_date = $('#payment_list2').text();
                var id_customer = $('#customer_id').val();
                var id_employee = $('#id_employee').val();
                var id_branch = $('#id_branch').val();
                var id_status = $('#id_status').val();
                get_payment_list(from_date, to_date, id_branch, id_employee, id_status, id_customer);
            }
        },
        response: function (e, i) {
            // ui.content is the array that's about to be sent to the response callback.
            if (i.content.length === 0) {
                alert('Please Enter a valid Number');
                $('#mobilenumber').val('');
            }
        },
    });
}
function get_check_customer_payment_det() {
    var id_customer = $('#id_customer').val();
    var id_branch = $("#id_branch").val();
    my_Date = new Date();
    $.ajax({
        url: base_url + 'index.php/admin_ret_billing/bill_payment_details/?nocache=' + my_Date.getUTCSeconds(),
        dataType: "json",
        method: "POST",
        data: { 'id_customer': id_customer, 'id_branch': id_branch },
        success: function (data) {
            $.each(data, function (key, val) {
                $("#chit_cash_paid").val(val.bill_amount);
            });
        }
    });
}
function get_scheme_cash_total() {
    var id_customer = $('#id_customer').val();
    var id_branch = $("#id_branch").val();
    var id_scheme_acc = $("#id_scheme_account").val();
    my_Date = new Date();
    $.ajax({
        url: base_url + 'index.php/admin_payment/get_scheme_cash_total/?nocache=' + my_Date.getUTCSeconds(),
        dataType: "json",
        method: "POST",
        data: { 'id_customer': id_customer, 'id_scheme_account': id_scheme_acc, 'id_branch': id_branch },
        success: function (data) {
            $("#chit_total_cash_paid").val(data.cash_pay);
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
    // Convert to Date if not already
    if (!(date instanceof Date)) {
        date = new Date(date);
    }
    if (isNaN(date)) {
        return ''; // invalid date
    }
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    if (day < 10) day = '0' + day;
    if (month < 10) month = '0' + month;
    return day + '-' + month + '-' + year;
}
function resetModeTable() {
    // alert('1');
    $('#make_pay_cash').val('');
    $('.CC,.DC,.CHQ,.NB,.VCH,.tot_adv_adj,.sum_of_amt,.bal_amount').html('');
    //hidden inputs reset...
    const cus_pay = document.querySelectorAll('input[name^="cus_pay_mode"]');
    cus_pay.forEach(input => {
        input.value = '';
    });
    //modal charts reset...
    $("#net_bank_details tbody tr").remove();
    $("#card_details tbody tr").remove();
    $("#chq_details tbody tr").remove();
    $("#bill_adv_adj tbody tr").remove();
    //modal chart totals reset...
    $('.cc_total_amount,.nb_total_amount,.cc_total_amt,.dc_total_amt,.chq_total_amount,.total_adv_amt,.total_adj_adv_amt,.total_blc_amt,.total_bill_amt').html('');
    //payment main form reset...
    $('input[name="accounts[payment_ref_number]"]').val('');
    var pay_comments = document.querySelector('textarea[name="accounts[remark]"]');
    pay_comments.value = '';
    return true;
}
//esakki
function calculate_model_payment() {
    var card_modal_amt = $('.cc_total_amount').html();
    var nb_modal_amt = $('.nb_total_amount').html();
    var chq_modal_amt = $('.chq_total_amount').html();
    var vch_modal_amt = $('.vch_total_amount').html();
    var adv_modal_amt = $('.total_adj_adv_amt').html();
    var cash_amt = $('#make_pay_cash').val();
    var total_pay_amt = $('#payment_amt').val();
    console.log('card', parseFloat((card_modal_amt == "") ? 0 : card_modal_amt));
    console.log('net', parseFloat((nb_modal_amt == "") ? 0 : nb_modal_amt));
    console.log('cheque', parseFloat((chq_modal_amt == "") ? 0 : chq_modal_amt));
    console.log('voucher', parseFloat((vch_modal_amt == "") ? 0 : vch_modal_amt));
    console.log('advance', parseFloat((adv_modal_amt == "") ? 0 : adv_modal_amt));
    console.log('cash', parseFloat((cash_amt == "") ? 0 : cash_amt));
    total_bal_amt = parseFloat((parseFloat((total_pay_amt == "") ? 0 : total_pay_amt)) - (parseFloat((card_modal_amt == "") ? 0 : card_modal_amt) + parseFloat((nb_modal_amt == "") ? 0 : nb_modal_amt) + parseFloat((chq_modal_amt == "") ? 0 : chq_modal_amt) + parseFloat((vch_modal_amt == "") ? 0 : vch_modal_amt) + parseFloat((adv_modal_amt == "") ? 0 : adv_modal_amt) + parseFloat((cash_amt == "") ? 0 : cash_amt))).toFixed(2);
    $(".modal_balance_amount").html(indianCurrency.format(total_bal_amt));
}
// esakki 19-09
function calculate_total_weight(data) {
    if ($("#total_amt").val() > 0) {
        amt = $("#total_amt").val();
    } else {
        amt = 0;
    }
    var metal_rates = $("#metal_rate").val();
    var amount = amt;
    //GST Calculation
    var gst_val = 0;
    var gst_amt = 0;
    var weight = 0;
    var wight_amount = 0;
    var metal_weights = 0;
    var gst = 0;
    var tot_est_amt = $('.tot_est_amt').html();
    var tot_est_weight = $('.tot_est_weight').html();
    /*if(data.gst > 0 )
    {
        gst_val = parseFloat(amount)-(parseFloat(amount)*(100/(100+parseFloat(data.gst))));
        gst_amt = gst_val*allowed_dues;
        if(data.gst_type == 1)
        {	 	
            gst = gst_amt ;
        }
    }
    metal_weights = parseFloat(amount)/parseFloat(metal_rates);*/
    /* !!!! GST & METAL WEIGHT CALCULATION START FOR INCLUSIVE, EXCLUSIVE WITH DISCOUNT IF APPLICABLE !!!!  */
    if (data.gst > 0) {
        /*gst_val = parseFloat(amount)-(parseFloat(amount)*(100/(100+parseFloat(data.gst))));
        gst_amt = gst_val*allowed_dues;
        if(data.gst_type == 1)
        {	 	
            gst = gst_amt ;
        }*/
        /* !!!! GST & METAL WEIGHT CALCULATION START FOR INCLUSIVE, EXCLUSIVE WITH DISCOUNT IF APPLICABLE !!!!  */
        var disc_amt = $('#discountedAmt').val();
        var fix_weight = $('#fix_weight').val();
        var flexible_sch_type = $('#flexible_sch_type').val();
        var wgt_convert = $('#wgt_convert').val();
        var wgt_store_as = $('#wgt_store_as').val();
        //Subtracting the dicount amount from the total payment amount. So the actual amount received from customer arrived...
        var actual_pay = parseInt((parseInt(disc_amt) > 0 ? (parseInt(amount) - parseInt(disc_amt)) : parseInt(amount))); // 15000 - 250 = 14750
        if (data.gst_type == 1) {
            //FOR EXCLUSIVE GST TYPE...
            var gst_amt = parseInt(parseInt(actual_pay) * (parseInt(data.gst) / 100)); // 14750 * (3/100)  = 442.5
        } else {
            //FOR INCLUSIVE GST TYPE...
            var gst_amt = parseInt(parseInt(actual_pay) - (parseInt(actual_pay) * (100 / (100 + parseInt(data.gst))))); // 14750 - (14750 * (100 / (100 + 3)))  = 429.61
        }
        calculatePaymentCost();
    }
    //METAL WEIGHT CALC STARTS...
    var metal_wgt = '';
    var wgt_amount = (data.gst_type == 0 ? (parseInt(amount) - parseInt(gst_amt)) : parseInt(amount));
    if (fix_weight == 2) //scheme_type is assigned to fix weight
    {
        metal_wgt = parseFloat(wgt_amount) / parseFloat(metal_rates);
    } else if (fix_weight == 3) // scheme type 
    {
        // for  inclusive ---> 15000 - 429.61 = 14571
        // for  exclusive ---> 15000 
        if (flexible_sch_type == 3 || flexible_sch_type == 4 || flexible_sch_type == 7 || flexible_sch_type == 8 || (flexible_sch_type == 2 && wgt_convert != 2) || (flexible_sch_type == 5 && wgt_store_as == 1)) {
            metal_wgt = parseFloat(wgt_amount) / parseFloat(metal_rates);
            // for  inclusive ---> (14571 / 5685) = 2.563 grams
            // for  exclusive ---> (15000 / 5685) = 2.638 grams
        } else {
            // esakki 30-09
            metal_wgt = $("#payment_weight").val();
        }
    } else {
        metal_wgt = parseFloat(wgt_amount) / parseFloat(metal_rates);
    }
    // metal_weights = parseFloat(metal_wgt).toFixed(3);
    metal_weights = (formatMetalWeight(metal_wgt));
    $('#payment_weight').val(metal_weights);
    /* !!!! GST & METAL WEIGHT CALCULATION START FOR INCLUSIVE, EXCLUSIVE WITH DISCOUNT IF APPLICABLE ends!!!!  */
}
async function metalrate_find(date) {
    var sch_id = $("#id_scheme_account").val();
    type = 1;
    if ($("#enable_editing").is(":checked")) {
        type = 2;
    }
    if (sch_id > 0) {
        await load_account_detail(sch_id, date);
    }
    $.ajax({
        url: base_url +
            "index.php/admin_payment/getMetalRateBydate?nocache=" +
            my_Date.getUTCSeconds(),
        data: { date_pay: date, type: type },
        dataType: "JSON",
        type: "POST",
        success: function (data) {
            console.log(data);
            if ($("#enable_editing").is(":checked")) {
                if ($('#edit_metalrate_date').val() != '') {
                    $('#metal_rate').val('');
                    $("#select_metal_rate").empty();
                    $("#select_metal_rate").select2({
                        placeholder: "Select metal rate",
                        allowClear: true,
                    });
                    $("#select_metal_rate").append(
                        $("<option></option>")
                            .attr("value", '')
                            .text('')
                    );
                    $.each(data, function (key, item) {
                        $("#select_metal_rate").append(
                            $("<option></option>")
                                .attr("value", item.goldrate_22ct)
                                .text(item.goldrate)
                                .attr("data-updatetime", item.updatetime)
                        );
                    });
                    // $("#select_metal_rate").val(data);
                }
            } else {
                $("#metal_rate").val(data);
                $("#current_metal_rate").val(data);
                wgt_calc(data);
            }
            $("div.overlay").css("display", "none");
        },
        error: function (error) {
            $("div.overlay").css("display", "none");
        },
    });
}
function wgt_calc(data) {
    $("input[name=weight_gold]").attr("checked", false);
    $("#selected_weight").val(" ");
    $("#total_amt").val(" ");
    $("#gst_amt").val(" ");
    $("#payment_amt").val(" ");
    $("#sel_wt").text("0.000");
    $("#rate").text(data);
    var amt = parseFloat($("#payamt").val());
    //GST Calculation
    var gst_val = 0;
    var gst_amt = 0;
    var gst = 0;
    if (parseFloat($("#gst_percent").val()) > 0) {
        gst_val =
            parseFloat(amt) -
            parseFloat(amt) *
            (100 / (100 + parseFloat($("#gst_percent").val())));
        gst_amt = gst_val * parseFloat($("#allowed_dues").val());
        if (parseFloat($("#gst_type").val()) == 1) {
            gst = gst_amt;
        }
    }
    total = parseFloat(
        parseFloat(amt) * parseFloat($("#allowed_dues").val())
    ).toFixed(2);
    if (
        $("#scheme_type").text() == "Amount" ||
        $("#scheme_type").text() == "Amount to Weight"
    ) {
        $("#total_amt").val(total);
        // wallet calculation
        var can_redeem = 0;
        if (
            $(".ischk_wallet_pay").is(":checked") &&
            parseFloat($(".wallet_balance").val()) > 0
        ) {
            var allowed_redeem =
                $(".redeem_percent").val() > 0 ?
                    total * (parseFloat($(".redeem_percent").val()) / 100) :
                    0;
            wallet_balance = parseFloat($(".wallet_balance").val());
            if (allowed_redeem > wallet_balance) {
                can_redeem = wallet_balance;
            } else {
                can_redeem = allowed_redeem;
            }
        }
        $(".wallet").val(can_redeem);
        $(".redeem_request").val(can_redeem);
        $("#gst_amt").val(parseFloat(gst_amt).toFixed(0)); // esakki 17-09
        $("#payment_amt").val(
            (parseFloat(gst) + parseFloat(total)).toFixed(0)
        ); // esakki 17-09
    }
    if ($("#scheme_type").text() == "Amount to Weight") {
        var total_amt =
            parseFloat($("#gst_type").val()) == 1 ?
                parseFloat($("#total_amt").val()) :
                parseFloat($("#total_amt").val()) - parseFloat(gst_amt);
        var metal_rate = parseFloat(data);
        if (total_amt != "" && metal_rate != "") {
            var weight = total_amt / metal_rate;
            // $('#payment_weight').val(parseFloat(weight.toFixed(3)));
             $('#payment_weight').val((formatMetalWeight(weight)));
            $("#amttowgt").html(
                // parseFloat(weight.toFixed(3)) +
                 (formatMetalWeight(weight)) +
                " " +
                "<strong>" +
                "gm" +
                "</strong>"
            );
        }
    }
    $("#weightsel_block_wt").html(data);
}
$("#select_metal_rate").on("change", function (e) {
    if (this.value != "") {
        var selectedOption = $("#select_metal_rate option:selected");
        var updatetime = selectedOption.data("updatetime");
        $('#metalrate_edit_date').val(updatetime);
        $('#metal_rate').val(this.value);
        wgt_calc(this.value);
    } else {
        // $("#id_branch").val("");
    }
});
$('#employee_select').on('change', function (e) {
    if (this.value != '') {
        $("#id_employee").val(this.value);
    } else {
        $("#id_employee").val('');
    }
});
function getBranchEmployee() {
    var idBranch = $('#branch_select').val();
    $.ajax({
        type: 'POST',
        url: base_url + 'index.php/admin_manage/getEmployeeByBranch',
        data: { 'id_branch': idBranch },
        dataType: 'json',
        success: function (data) {
            $('#employee_select').empty(); // Clear existing options
            if (data.length > 0) {
                $('#employee_select').append('<option></option>'); // Placeholder option
                $.each(data, function (key, item) {
                    $('#employee_select').append(
                        $('<option></option>')
                            .attr('value', item.id_employee)
                            .text(item.emp_data)
                    );
                });
            } else {
                $('#employee_select').append('<option value="">No employees found</option>');
            }
            // Reset and reinitialize Select2
            // if(){
            // }
            // console.log(session_data.uid);
            if (ctrl_page[0] == "payment" && ctrl_page[1] == "add") {
                if (session_data.uid != '' && session_data.uid != null && session_data.uid != 0 && session_data.uid != '0') {
                    $('#employee_select').val(session_data.uid).trigger('change');
                }
            } else {
                $('#employee_select').val(null).trigger('change');
            }
            $('#employee_select').select2('destroy').select2({
                placeholder: "Select employee name",
                allowClear: true
            });
        }
    });
}
function calc_walletbalance() { // esakki 31-07-2025
    var totamt = (parseFloat($('#total_amt').val()) > 0 ? parseFloat($('#total_amt').val()) : 0);
    var can_redeem = 0;
    if ($(".ischk_wallet_pay").is(":checked") && parseFloat($(".wallet_balance").val()) > 0) {
        if ($('.redeem_request').val() > 0 || $('.redeem_request').val() == '') {
            var redeem = ($('.redeem_request').val() == '' ? 0 : $('.redeem_request').val());
            var pay_amt = parseFloat($('#total_amt').val());
            pay_amt = pay_amt - redeem;
            $('#payment_amt').val(pay_amt);
            calculatePaymentCost();
        } else {
            var allowed_redeem = ($('.redeem_percent').val() > 0 ? (totamt * (parseFloat($('.redeem_percent').val()) / 100)) : ($('.wallet_balance').val() > 0 ? $('.wallet_balance').val() : 0));
            wallet_balance = parseFloat($('.wallet_balance').val());
            // $("#wallet_msg").text('Your wallet amount : Rs.'+wallet_balance+ ',Chit amount : Rs.'+totamt);
            if (allowed_redeem > totamt) {
                can_redeem = totamt;
            } else {
                can_redeem = allowed_redeem;
            }
            if ($('#total_amt').val() > 0 && $('#total_amt').val() != "") {
                var pay_amt = parseFloat($('#total_amt').val());
                pay_amt = pay_amt - can_redeem;
                $('#payment_amt').val(pay_amt);
            }
            calculatePaymentCost();
            $('.wallet').val(can_redeem);
            $('.redeem_request').val(can_redeem);
            $("#wallet_msg").text('Your wallet amount : Rs.' + wallet_balance + ', Chit amount : Rs.' + totamt);
        }
    }
}
function digi_benefit_calculation(digi_data) {
    var digi_benefit_amt = 0;
    var digi_benefit_wgt = 0;
    var received_amt = $("#total_amt").val();
    var metal_rate = $("#metal_rate").val();
    if (digi_data['benefit_type'] == 0) { // percentage
        digi_benefit_amt = received_amt * (digi_data['benefit_value'] / 100);
        digi_benefit_wgt = digi_benefit_amt / metal_rate;
    } else if (digi_data['benefit_type'] == 1) { // amount
        digi_benefit_amt = digi_data['benefit_value'];
        digi_benefit_wgt = digi_benefit_amt / metal_rate;
    }
    $("#digi_benefit_content").html(digi_data['benefit_content']);
    $("#digi_benefit_amt").html(digi_data['currency_symbol'] + " <strong>" + digi_benefit_amt.toFixed(2) + "</strong> ");
    // $("#digi_benefit_wgt").html("<strong>" + parseFloat(digi_benefit_wgt).toFixed(3) + "</strong>" + " gm");
    // $("#digi_benefit_wgt").html("<strong>" + (Math.floor(parseFloat(digi_benefit_wgt) * 1000) / 1000).toFixed(3) + "</strong> gm");
    $("#digi_benefit_wgt").html("<strong>" + (formatMetalWeight(digi_benefit_wgt)) + "</strong> gm");
    $('.other_benefit_block').css('display', 'none');
    if (digi_data['is_other_benefit'] == 1) {
        $('.other_benefit_block').css('display', '');
        if (digi_data['other_benefit_type'] == 0) { // percentage
            digi_other_benefit_wgt = (received_amt / metal_rate).toFixed(3) * (digi_data['other_benefit_value'] / 100);
            digi_other_benefit_amt = (digi_other_benefit_wgt).toFixed(3) * digi_data['other_metal_rate'];
        } else if (digi_data['other_benefit_type'] == 1) { // amount
            digi_other_benefit_amt = digi_data['other_benefit_value'];
            digi_other_benefit_wgt = digi_other_benefit_amt / digi_data['other_metal_rate'];
        }
        $("#digi_other_benefit_content").html(digi_data['other_benefit_content']);
        // $("#digi_other_benefit_content").html("Other Benefits - (" + digi_data['other_metal'] + " 1GM : "+ digi_data['currency_symbol'] +" "+ digi_data['other_metal_rate'] +")");
        $("#digi_other_benefit_amt").html(digi_data['currency_symbol'] + " <strong>" + digi_other_benefit_amt.toFixed(2) + "</strong> ");
        $("#digi_other_benefit_wgt").html("<strong>" + (Math.floor(parseFloat(digi_other_benefit_wgt) * 1000) / 1000).toFixed(3) + "</strong> gm");
        // $("#digi_other_benefit_wgt").html("<strong>" + (Math.floor(parseFloat(digi_other_benefit_wgt) * 1000) / 1000).toFixed(3) + "</strong> gm");
    }
}
$(document).on('input keyup change', '#total_amt', function () {
    calculatePaymentCost();
});




function get_emp_branchwise(login_branches) {
  $.ajax({
    type: 'GET',
    data: { 'login_branches': login_branches },
    url: base_url + 'index.php/reports/employee_list_brancwise',
    dataType: 'json',
    success: function (data) {
      var id_employee = $('#id_employee1').val();
      var session_uid = $('#session_uid').val(); // Get the session uid from hidden input
      $('#select_employee').empty().append('<option value="">Select Employee</option>');
      $.each(data.employee, function (key, item) {
        $('#select_employee').append(
          $("<option></option>")
            .attr("value", item.id_employee)
            .text(item.employee_name)
        );
      });
      $("#select_employee").select2({
        placeholder: "Select Employee Name",
        allowClear: true,
      });
      // Set default employee to id_employee or session_uid
      // var defaultEmployee = id_employee !== '' && id_employee > 0 ? id_employee : session_uid;
      // $("#select_employee").val(defaultEmployee).trigger("change");
      // $("#id_employee1").val(defaultEmployee);
      $(".overlay").css("display", "none");
    }
  });
}