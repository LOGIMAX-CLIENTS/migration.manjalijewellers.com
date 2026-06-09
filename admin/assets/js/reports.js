var usernamedata = $("#hiddenuserdata").val();
var path = url_params();
let currencyFormat = Intl.NumberFormat(session_data.currencyFormat);
let formatCurrency = Intl.NumberFormat(session_data.currencyFormat, { minimumFractionDigits: session_data.currency_decimal, maximumFractionDigits: session_data.currency_decimal, });
// formatCurrency added by Durga 29-06-2023fr
let indianCurrency = new Intl.NumberFormat("en-IN", {
  style: "currency",
  currency: "INR",
  maximumFractionDigits: 2,
  roundingIncrement: 5,
});
var fewSeconds = 30;
var timer = null;
//global variable for member report title
var summarytitleHTML = '';
var outstandingSummaryForPrint = '';
var ctrl_page = path.route.split('/');
/* -- generalized mobile app folder path -- */
var pathArray = window.location.pathname.split('php/');
var path = pathArray[0].split('/');
var app_url = window.location.origin + '/' + path[1] + '/';
/* -- generalized mobile app  folder path -- */
$(document).ready(function () {
  $("#currency_symbol").html(session_data.currency_symbol);
  if (ctrl_page[2] == "account") {
    var cmt = $("#acc_remark").val();
    var comments = cmt.trim();
    var len = comments.length;
    $("#update_remark").on("click", function (e) {
      var scheme_id = ctrl_page[3];
      var remarktxt = $("#acc_remark").val();
      var remark_data = remarktxt.trim();
      //alert(scheme_id);
      if (remark_data.length > 0) {
        set_remark(scheme_id, remark_data);
      } else {
        $.toaster({
          priority: "danger",
          title: "Warning!",
          message: "" + "</br>Enter any comments as remark",
        });
      }
    });
  }
  function set_remark(scheme_id, remark_data) {
    my_Date = new Date();
    $.ajax({
      type: "POST",
      data: { schemeid: scheme_id, remarkdata: remark_data },
      url:
        base_url +
        "index.php/admin_manage/set_remarks_byid/?nocache=" +
        my_Date.getUTCSeconds(),
      dataType: "json",
      cache: false,
      success: function (data) {
        if (data.message == "success") {
          $.toaster({
            priority: "success",
            title: "success!",
            message: "" + "</br>Remarks updated successfully",
          });
        }
      },
    });
  }
  //outstanding report starts here
  $('input[name="tableview"]').change(function () {
    selectedValue = $('input[name="tableview"]:checked').val();
    if (selectedValue == 2) {
      var branch_select = $("#branch_select").val();
      $("#excel_export").hide();
      var scheme_select = $("#scheme_select").val();
      // 			if (branch_select > 0 || scheme_select > 0) {
      getSchemeDateRangeList();
      // 			}
      //	$("div.overlay").css("display", "none");
      $('#excel_detailed_list').show();
      $("#out_standing_table_div").css("display", "block");
      $("#summary_block").css("display", "none");
    } else {
      outstanding_summary();
      $("#excel_export").show();
      $('#excel_detailed_list').hide();
      $("#out_standing_table_div").css("display", "none");
      $("#summary_block").css("display", "block");
    }
  });
  $("#xl_export_outstanding").click(function () {
    var xl_url = "admin_reports/exl_rep_outstanding/export_excel";
    var pDaata = {
      id_scheme: $("#scheme_select").val(),
      id_group: $("#id_group").val(),
      id_branch:
        $("#branch_filter").val() != "" &&
          $("#branch_filter").val() != undefined
          ? $("#branch_filter").val()
          : $("#id_branch").val(),
    };
    manual_export(xl_url, pDaata);
  });
  //outstanding report ends here
  //created by RK - 16/12/2022
  /*	if (ctrl_page[1] == 'customer_wishes') {
    var date = new Date();
    var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 30, 1);
    var from_date = firstDay.getFullYear() + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();
    var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
    console.log(from_date);
    console.log(to_date);
    set_customer_wishes_table(from_date, to_date);
    $('#gift_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
    $('#gift_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
    $('#celeb_report_date_range').html(moment().startOf('month').format('DD-MM-YYYY') + ' to ' + moment().endOf('month').format('DD-MM-YYYY'));
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
        $('#celeb_report_date_range').html(start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
        set_customer_wishes_table(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))
      }
    );
    //function call
    var from_date = $('#gift_list1').text();
    var to_date = $('#gift_list2').text();
  }  */
  //employee wise referral reports starts here
  if (ctrl_page[2] == "refferl_account") {
    var emp_code = ctrl_page[3];
    //daterange picker
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 30,
      1
    );
    // var from_date   =   firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
    //var to_date     =   (date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());
    var from_date = ctrl_page[4];
    var to_date = ctrl_page[5];
    //	console.log(from_date);
    //	console.log(to_date);
    set_referral_table(from_date, to_date, emp_code);
    //$('#referal_list1').empty();
    // $('#referal_list2').empty();
    $("#referal_list1").text(from_date);
    $("#referal_list2").text(to_date);
    $("#referral-dt-btn").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        set_referral_table(
          start.format("DD-MM-YYYY"),
          end.format("DD-MM-YYYY"),
          emp_code
        );
        $("#referal_list1").text(start.format("DD-MM-YYYY"));
        $("#referal_list2").text(end.format("DD-MM-YYYY"));
      }
    );
    //function call
    var from_date = $("#referal_list1").text();
    var to_date = $("#referal_list2").text();
    //set_referral_table(from_date,to_date,emp_code);
  }
  $("#credit_select").on("change", function () {
    if (this.value) {
      var from_date = $("#referal_list1").text();
      var to_date = $("#referal_list2").text();
      var emp_code = ctrl_page[3];
      set_referral_table(from_date, to_date, emp_code);
    }
  });
  //employee wise refferal report ends here
  if (ctrl_page[1] == "gift_report") {
    get_employee_list();
  }
  //pending remarks report
  if (ctrl_page[1] == "accountRemarks") {
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 6,
      1
    );
    var from_date =
      firstDay.getFullYear() +
      "-" +
      (firstDay.getMonth() + 1) +
      "-" +
      firstDay.getDate();
    var to_date =
      date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
    //$('#rpt_payments1').empty();
    //$('#rpt_payments2').empty();
    get_paymentRemarks(from_date, to_date);
    $("#rpt_payments1").text(moment().startOf("month").format("YYYY-MM-DD"));
    $("#rpt_payments2").text(moment().endOf("month").format("YYYY-MM-DD"));
    $("#account-dt-btn").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        get_paymentRemarks(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD")
        );
        $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
        $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
      }
    );
  }
  if (ctrl_page[1] == "get_yet_to_issue") {
    get_gift_name();
    get_schemename();
    get_gift_report();
  }
  get_sch_enq_list();
  if (ctrl_page[1] == "payment_cancel_report") {
    get_cancel_pay_list();
  }
  if (ctrl_page[1] == "Employee_account") {
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 6,
      1
    );
    var from_date =
      firstDay.getFullYear() +
      "-" +
      (firstDay.getMonth() + 1) +
      "-" +
      firstDay.getDate();
    var to_date =
      date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
    $("#account_list1").empty();
    $("#account_list2").empty();
    get_employee_acc_list(from_date, to_date);
    $("#account_list1").text(moment().startOf("month").format("YYYY-MM-DD"));
    $("#account_list2").text(moment().endOf("month").format("YYYY-MM-DD"));
    $("#account-dt-btn").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        get_employee_acc_list(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD")
        );
        $("#account_list1").text(start.format("YYYY-MM-DD"));
        $("#account_list2").text(end.format("YYYY-MM-DD"));
      }
    );
  }
  $("#cancel_payment_list1").empty();
  $("#cancel_payment_list2").empty();
  $("#cancel_payment_list1").text(
    moment().startOf("month").format("YYYY-MM-DD")
  );
  $("#cancel_payment_list2").text(moment().endOf("month").format("YYYY-MM-DD"));
  $("#cancel_payment-dt-btn").daterangepicker(
    {
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
      },
      startDate: moment().subtract(29, "days"),
      endDate: moment(),
    },
    function (start, end) {
      $("#reportrange span").html(
        start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
      );
      $("#cancel_payment_list1").text(start.format("YYYY-MM-DD"));
      $("#cancel_payment_list2").text(end.format("YYYY-MM-DD"));
      var branch = $("#branch_select").val();
      get_cancel_pay_list(
        start.format("YYYY-MM-DD"),
        end.format("YYYY-MM-DD"),
        branch
      );
    }
  );
  //Purchase Payment - Akshaya Thiruthiyai Spl updt//
  if (ctrl_page[1] == "get_purchase_payment") {
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 30,
      1
    );
    var from_date =
      firstDay.getFullYear() +
      "-" +
      (firstDay.getMonth() + 1) +
      "-" +
      firstDay.getDate();
    var to_date =
      date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
    get_purchase_payment(from_date, to_date);
    $("#payment_list1").empty();
    $("#payment_list2").empty();
    $("#payment_list1").text(moment().startOf("month").format("YYYY-MM-DD"));
    $("#payment_list2").text(moment().endOf("month").format("YYYY-MM-DD"));
    $("#payment-dt-btn").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        get_purchase_payment(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD")
        );
        $("#payment_list1").text(start.format("YYYY-MM-DD"));
        $("#payment_list2").text(end.format("YYYY-MM-DD"));
      }
    );
    $("#mobilenumber").autocomplete({
      source: function (request, response) {
        var mobile = $("#mobilenumber").val();
        my_Date = new Date();
        $.ajax({
          url:
            base_url +
            "index.php/admin_reports/ajax_get_customers_list?nocache=" +
            my_Date.getUTCSeconds(),
          dataType: "json",
          type: "POST",
          data: { mobile: mobile },
          success: function (data) {
            var data = JSON.stringify(data);
            data = JSON.parse(data);
            var cus_list = new Array(data.length);
            var i = 0;
            data.forEach(function (entry) {
              var customer = {
                label: entry.mobile + "  " + entry.firstname,
                value: entry.id_purch_customer,
              };
              cus_list[i] = customer;
              i++;
            });
            response(cus_list);
          },
        });
      },
      minLength: 4,
      delay: 300,
      select: function (e, i) {
        e.preventDefault();
        var from_date = $("#payment_list1").text();
        var to_date = $("#payment_list2").text();
        $("#mobilenumber").val(i.item.label);
        $("#id_customer").val(i.item.value);
        get_purchase_payment(from_date, to_date, $("#id_customer").val());
      },
      response: function (e, i) {
        // ui.content is the array that's about to be sent to the response callback.
        if (i.content.length === 0) {
          alert("Please Enter a valid Number");
          $("#mobilenumber").val("");
        }
      },
    });
  }
  //Purchase Payment - Akshaya Thiruthiyai Spl updt//
  //Online Payment Report
  if (ctrl_page[1] == "online_payment_report") {
    $("#online_payment_report_list_info").hide();
    $(".dataTables_info").hide();
    get_payment_status();
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 0,
      1
    );
    var from_date =
      firstDay.getFullYear() +
      "-" +
      (firstDay.getMonth() + 1) +
      "-" +
      firstDay.getDate();
    var to_date =
      date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
    $("#from_date").html(from_date);
    $("#to_date").html(to_date);
    get_online_payment_report();
    $("#online_payment_report_date").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(0, "days"),
        endDate: moment(),
      },
      function (start, end) {
        get_online_payment_report(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD")
        );
        //$('#online_payment_report_date').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        $("#from_date").text(start.format("DD-MM-YYYY"));
        $("#to_date").text(end.format("DD-MM-YYYY"));
      }
    );
  }
  //old metal report
  if (ctrl_page[1] == "old_metal_report") {
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 0,
      1
    );
    var from_date =
      firstDay.getFullYear() +
      "-" +
      (firstDay.getMonth() + 1) +
      "-" +
      firstDay.getDate();
    var to_date =
      date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
    $("#rpt_payments1").html(from_date);
    $("#rpt_payments2").html(to_date);
    get_old_metal_report();
    $("#rpt_payment_date").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(0, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
        $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
      }
    );
  }
  //old metal report
  $("#pay_reprint").click(function () {
    if ($("input[name='payment_reprint[]']:checked").val()) {
      var selected = [];
      $("input[name='payment_reprint[]']:checked").each(function () {
        selected.push($(this).val());
      });
      pay_id = selected;
      if (selected.length) {
        pay_reprint(pay_id);
      } else {
        alert("Please select payment to reprint");
      }
    } else {
      $.toaster({
        priority: "warning",
        title: "warning!",
        message: "" + "</br> Atleast Select Any One Payment",
      });
    }
  });
  function pay_reprint(pay_id = "") {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $.ajax({
      url:
        base_url +
        "index.php/admin_manage/passbook_reprint?nocache=" +
        my_Date.getUTCSeconds(),
      data: { pay_ids: pay_id, id_scheme_account: ctrl_page[3] },
      type: "POST",
      async: false,
      success: function (data) {
        $("div.overlay").css("display", "none");
        window.open(
          base_url + "index.php/admin_manage/passbook_print/B/" + ctrl_page[3],
          "_blank"
        );
        $("input[name='payment_reprint[]']").removeAttr("checked");
      },
      error: function (error) {
        $("div.overlay").css("display", "none");
      },
    });
  }
  //Autodebit subscription Report//HH
  if (ctrl_page[1] == "get_autodebit_subscription") {
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 30,
      1
    );
    var from_date =
      firstDay.getFullYear() +
      "-" +
      (firstDay.getMonth() + 1) +
      "-" +
      firstDay.getDate();
    var to_date =
      date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
    get_autodebit_subscription(from_date, to_date);
    $("#payment_list1").empty();
    $("#payment_list2").empty();
    $("#payment_list1").text(moment().startOf("month").format("YYYY-MM-DD"));
    $("#payment_list2").text(moment().endOf("month").format("YYYY-MM-DD"));
    $("#payment-dt-btn").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        get_autodebit_subscription(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD")
        );
        $("#payment_list1").text(start.format("YYYY-MM-DD"));
        $("#payment_list2").text(end.format("YYYY-MM-DD"));
      }
    );
    $("#branch_select").on("change", function () {
      var from_date = $("#payment_list1").text();
      var to_date = $("#payment_list2").text();
      get_autodebit_subscription(from_date, to_date, $("#id_customer").val());
    });
    $("#mobilenumber").autocomplete({
      source: function (request, response) {
        var mobile = $("#mobilenumber").val();
        my_Date = new Date();
        $.ajax({
          url:
            base_url +
            "index.php/admin_reports/ajax_get_customers_lists?nocache=" +
            my_Date.getUTCSeconds(),
          dataType: "json",
          type: "POST",
          data: { mobile: mobile },
          success: function (data) {
            var data = JSON.stringify(data);
            data = JSON.parse(data);
            var cus_list = new Array(data.length);
            var i = 0;
            data.forEach(function (entry) {
              var customer = {
                label: entry.mobile + "  " + entry.firstname,
                value: entry.id_customer,
              };
              cus_list[i] = customer;
              i++;
            });
            response(cus_list);
          },
        });
      },
      minLength: 4,
      delay: 300,
      select: function (e, i) {
        e.preventDefault();
        var from_date = $("#payment_list1").text();
        var to_date = $("#payment_list2").text();
        $("#mobilenumber").val(i.item.label);
        $("#id_customer").val(i.item.value);
        get_autodebit_subscription(from_date, to_date, $("#id_customer").val());
      },
      response: function (e, i) {
        // ui.content is the array that's about to be sent to the response callback.
        if (i.content.length === 0) {
          alert("Please Enter a valid Number");
          $("#mobilenumber").val("");
        }
      },
    });
  }
  //Autodebit subscription Report//
  //get_kyc_list();
  if (
    ctrl_page[1] == "payment_employee_wise" ||
    ctrl_page[1] == "payment_daterange" ||
    ctrl_page[1] == "gift_report" ||
    ctrl_page[1] == "scheme_payment_daterange" ||
    ctrl_page[1] == "Employee_account" ||
    ctrl_page[1] == "payment_datewise_schemedata"
  ) {
    get_employee_name();
    get_branchname();
    get_payModeList();
  }
  if (ctrl_page[1] == "employee_wise_collection") {
    get_employee_name();
  }
  //closed A/C report with date picker, cost center based branch fillter//HH
  if (ctrl_page[1] == "closed_acc_report") {
    get_cls_branchname();
  }
  //Plan 2 and Plan 3 Scheme Enquiry Data with date filter//hh
  $("#sch_enq_list1").empty();
  $("#sch_enq_list2").empty();
  $("#sch_enq_list1").text(moment().startOf("month").format("YYYY-MM-DD"));
  $("#sch_enq_list2").text(moment().endOf("month").format("YYYY-MM-DD"));
  $("#sch_enq-dt-btn").daterangepicker(
    {
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
      },
      startDate: moment().subtract(29, "days"),
      endDate: moment(),
    },
    function (start, end) {
      $("#reportrange span").html(
        start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
      );
      $("#sch_enq_list1").text(start.format("YYYY-MM-DD"));
      $("#sch_enq_list2").text(end.format("YYYY-MM-DD"));
      get_sch_enq_list(start.format("YYYY-MM-DD"), end.format("YYYY-MM-DD"));
    }
  );
  //Plan 2 and Plan 3 Scheme Enquiry Data with date filter//hh
  //Kyc Approval Data status filter with date picker//hh
  if (ctrl_page[1] == "kyc_data" && ctrl_page[2] == "list") {
    $("#filtered_status").val(0);
    var type = ctrl_page[3];
    var list_type = 1;
    var from_date = $("#kyc_list1").text();
    var to_date = $("#kyc_list2").text();
    get_kyc_list(from_date, to_date, 0, type, list_type);
    $("#kyc_Select").on("change", function () {
      if (this.value != "") {
        // var type = $('#kyc_select').val();
        var type = ctrl_page[3];
        get_kyc_list(from_date, to_date, this.value, type, list_type);
      }
    });
    $("ul.nav-tabs li").click(function () {
      var tabId = $(this).attr("id");
      console.log(from_date);
      if (tabId == "tab_customer") {
        list_type = 1;
      } else {
        list_type = 2;
      }
      get_kyc_list(
        from_date,
        to_date,
        $("#filtered_status").val(),
        type,
        list_type
      );
    });
    $("#filtered_status").on("change", function () {
      var status = $(this).val();
      if (status == 0) {
        $("#in_progress").css("display", "none");
        $("#verified").css("display", "none");
        $("#reject").css("display", "none");
      } else {
        $("#in_progress").css("display", "block");
        $("#verified").css("display", "block");
        $("#reject").css("display", "block");
      }
      var type = ctrl_page[3];
      var mobile = $('#mobile_no').val() || '';
      get_kyc_list(from_date, to_date, status, type, list_type, mobile);
    });

    $('#search_kyc_mobile').on('click', function () {
      var mobile = $('#mobile_no').val() || '';
      var status = $('#filtered_status').val() || '';
      var type = typeof ctrl_page !== 'undefined' && ctrl_page.length > 3 ? ctrl_page[3] : '';
      var l_type = typeof list_type !== 'undefined' ? list_type : '';
      var f_d = $("#kyc_list1").text() || from_date || moment().startOf('month').format('YYYY-MM-DD');
      var t_d = $("#kyc_list2").text() || to_date || moment().endOf('month').format('YYYY-MM-DD');
      get_kyc_list(f_d, t_d, status, type, l_type, mobile);
    });

    $('#mobile_no').on('keypress', function (e) {
      if (e.which == 13) {
        $('#search_kyc_mobile').click();
      }
    });

    $("#mobile_no").autocomplete({
      source: function (request, response) {
        var myDate = new Date();
        $.ajax({
          url: base_url + 'index.php/admin_manage/getCustomersBySearch/?nocache=' + myDate.getUTCSeconds(),
          dataType: "json",
          method: "POST",
          data: { searchTxt: request.term },
          success: function (data) {
            response(data);
          }
        });
      },
      minLength: 3,
      select: function (event, ui) {
        event.preventDefault();
        var labelTxt = ui.item.label || '';
        var phoneMatch = labelTxt.match(/\d{8,15}/);
        var mobile_only = ui.item.mobile || (phoneMatch ? phoneMatch[0] : labelTxt);
        $("#mobile_no").val(mobile_only);
        $('#search_kyc_mobile').click();
      }
    });

    $('#mobile_no').on('keyup change', function () {
      if ($(this).val() === '') {
        var type = ctrl_page[3] || '';
        var l_type = typeof list_type !== 'undefined' ? list_type : '';
        var status = $('#filtered_status').val() || '';
        var f_d = $("#kyc_list1").text() || from_date || moment().startOf('month').format('YYYY-MM-DD');
        var t_d = $("#kyc_list2").text() || to_date || moment().endOf('month').format('YYYY-MM-DD');
        get_kyc_list(f_d, t_d, status, type, l_type, '');
      }
    });
    $("#kyc_list1").empty();
    $("#kyc_list2").empty();
    $("#kyc_list1").text(moment().startOf("month").format("YYYY-MM-DD"));
    $("#kyc_list2").text(moment().endOf("month").format("YYYY-MM-DD"));
    $("#kyc-dt-btn").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        $("#kyc_list1").text(start.format("YYYY-MM-DD"));
        $("#kyc_list2").text(end.format("YYYY-MM-DD"));
        var type = ctrl_page[3];
        var mobile = $('#mobile_no').val() || '';
        get_kyc_list(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD"),
          $("#filtered_status").val(),
          type,
          list_type,
          mobile
        );
      }
    );
    $("input[name='upd_status_btn']:radio").change(function () {
      if ($("input[name='kyc_id[]']:checked").val()) {
        var selected = [];
        var in_progress = false;
        var kyc_status = $("input[name='upd_status_btn']:checked").val();
        $("#kyc_list tbody tr").each(function (index, value) {
          if ($(value).find("input[name='kyc_id[]']:checked").is(":checked")) {
            data = {
              id_kyc: $(value).find(".kyc_id").val(),
              cus: $(value).find(".cus").val(),
              status: kyc_status,
            };
            in_progress = true;
            selected.push(data);
          }
        });
        kyc_data = selected;
        if (in_progress == true) {
          var kyc_type = ctrl_page[3];
          update_kyc_status(kyc_data, kyc_type);
        }
      }
    });
  }
  //Kyc Approval Data status filter with date picker//hh
  if (ctrl_page[1] == "Employee_account") {
    var date = new Date();
    var firstDay = new Date(
      date.getFullYear(),
      date.getMonth(),
      date.getDate() - 6,
      1
    );
    var from_date =
      firstDay.getFullYear() +
      "-" +
      (firstDay.getMonth() + 1) +
      "-" +
      firstDay.getDate();
    var to_date =
      date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
    $("#account_list1").empty();
    $("#account_list2").empty();
    get_employee_acc_list(from_date, to_date);
    $("#account_list1").text(moment().startOf("month").format("YYYY-MM-DD"));
    $("#account_list2").text(moment().endOf("month").format("YYYY-MM-DD"));
    $("#account-dt-btn").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        get_employee_acc_list(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD")
        );
        $("#account_list1").text(start.format("YYYY-MM-DD"));
        $("#account_list2").text(end.format("YYYY-MM-DD"));
      }
    );
  }
  var dateToday = new Date();
  $("#emp_list").DataTable();
  var dateToday = new Date();
  $("#filter_by").on("change", function () {
    if (this.value != "") {
      $(".filter_by_ip").css("display", "block");
    } else {
      $(".filter_by_ip").css("display", "none");
    }
  });
  $("#searchWalTrans").click(function () {
    var searchTerm = $("#searchTerm").val();
    var id_branch = $("#branch_select").val();
    var from_date = $("#rpt_payments1").text();
    var to_date = $("#rpt_payments2").text();
    if (searchTerm != "") {
      get_interWalTrans_list(from_date, to_date, "");
    }
  });
  // Enquiry KVP
  $("#add_enq_status").click(function () {
    $("div.overlay").css("display", "block");
    $("#add_enq_status").prop("disabled", true);
    var postData = {
      internal_status: $("#internal_stat").val(),
      enq_description: $("#enq_desc").val(),
      enq_status: $("#enq_status").val(),
      id_enquiry: $("#id_enquiry").val(),
    };
    $.ajax({
      url: base_url + "index.php/admin_reports/enquiry/UpdateStatus/",
      dataType: "JSON",
      data: postData,
      type: "POST",
      async: false,
      success: function (data) {
        if (data.status) {
          $("#internal_stat").val("");
          $("#enq_desc").val("");
          $("#enq_status").val(1);
          $("#id_enquiry").val("");
        }
        $("#add_enq_status").prop("disabled", false);
        $("div.overlay").css("display", "none");
        window.location.reload();
      },
      error: function (error) {
        $("div.overlay").css("display", "none");
      },
    });
  });
  $("#sub_date").datepicker({
    minDate: new Date(
      dateToday.getFullYear(),
      dateToday.getMonth(),
      dateToday.getDate()
    ),
    setDate: new Date(new Date().toString("dd/MM/yyyy")),
  });
  $("#emp_acc_list").dataTable({
    bDestroy: true,
    bInfo: true,
    bFilter: true,
    bSort: true,
    dom: 'T<"clear">lfrtip',
    tableTools: {
      aButtons: [
        { sExtends: "xls", oSelectorOpts: { page: "current" } },
        { sExtends: "pdf", oSelectorOpts: { page: "current" } },
      ],
    },
  });
  /*$('.det_pay_report').dataTable({
       "bPaginate": true,
       "bLengthChange": true,
       "bFilter": true,
       "bSort": true,
       "bAutoWidth": false,
       "order": [[ 0, "desc" ]],
       "bDestroy": true, 
       "responsive": true, 
       "bInfo": false,
        "scrollX":'100%',
       "dom": 'Bfrtip',
        "lengthMenu":[[ 10, 25, 50, -1 ],[ '10 rows', '25 rows', '50 rows', 'Show all' ]],
         "buttons": [
               {	
               extend: 'print',									   
               footer: true,
               customize: function ( win ) {
                         $(win.document.body).find( 'table' )
                           .addClass( 'compact' )
                           .css( 'font-size', 'inherit' );
                       },
              },
              {
              extend: 'excel',	
               },
               {
               extend:'pageLength',
               customize: function ( win ) {
                         $(win.document.body).find( 'table' )
                           .addClass( 'compact' )
                           .css( 'font-size', 'inherit' );
                       },
                }
                 ],
       });*/
  $(".refferal_report").dataTable({
    bPaginate: true,
    bLengthChange: true,
    bFilter: true,
    bSort: true,
    bInfo: true,
    bAutoWidth: false,
    order: [[0, "desc"]],
    dom: 'T<"clear">lfrtip',
    tableTools: {
      aButtons: [
        { sExtends: "xls", oSelectorOpts: { page: "current" } },
        { sExtends: "pdf", oSelectorOpts: { page: "current" } },
      ],
    },
  });
  $(".reff_reports").dataTable({
    bPaginate: true,
    bLengthChange: true,
    bFilter: true,
    bSort: true,
    bInfo: true,
    bAutoWidth: false,
    order: [[0, "desc"]],
    dom: 'T<"clear">lfrtip',
    tableTools: {
      aButtons: [
        { sExtends: "xls", oSelectorOpts: { page: "current" } },
        { sExtends: "pdf", oSelectorOpts: { page: "current" } },
      ],
    },
  });
  $(".date_pay_report").dataTable({
    bPaginate: true,
    bLengthChange: true,
    bFilter: true,
    bSort: true,
    bInfo: true,
    bAutoWidth: false,
    order: [[0, "asc"]],
    dom: 'T<"clear">lfrtip',
    tableTools: {
      aButtons: [
        { sExtends: "xls", oSelectorOpts: { page: "current" } },
        { sExtends: "pdf", oSelectorOpts: { page: "current" } },
      ],
    },
  });
  $(".refferal_counts").dataTable({
    bPaginate: true,
    bLengthChange: true,
    bFilter: true,
    bSort: true,
    bInfo: true,
    bAutoWidth: false,
    order: [[0, "asc"]],
    dom: 'T<"clear">lfrtip',
    tableTools: {
      aButtons: [
        { sExtends: "xls", oSelectorOpts: { page: "current" } },
        { sExtends: "pdf", oSelectorOpts: { page: "current" } },
      ],
    },
  });
  switch (ctrl_page[2]) {
    case "postdated":
      var payment_type =
        (ctrl_page[6] == 7 ? "Presentable" : "Presented") +
        " " +
        (ctrl_page[5] == "chq" ? "Cheque" : "ECS") +
        " ";
      $("#total_payments").text(0);
      $("#pay_type").text(payment_type);
      get_postdated_data(ctrl_page[4], ctrl_page[5], ctrl_page[6]);
      break;
    case "payment":
      generate_failed_payments();
      break;
    default:
      break;
  }
  switch (ctrl_page[1]) {
    //Advance payment report starts
    case "general_advance":
      $("#rpts_payments1").empty();
      $("#rpts_payments2").empty();
      $("#rpts_payments1").text(moment().startOf("today").format("DD-MM-YYYY"));
      $("#rpts_payments2").text(moment().startOf("today").format("DD-MM-YYYY"));
      get_payModeList();
      get_schemename();
      get_general_advance_list();
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment(),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          $("#rpts_payments1").text(start.format("DD-MM-YYYY"));
          $("#rpts_payments2").text(end.format("DD-MM-YYYY"));
        }
      );
      break;
    //Advance payment report ends
    //Member report starts here
    case "member_report":
      $("#rpts_payments1").empty();
      $("#rpts_payments2").empty();
      $("#rpts_payments1").text(moment().startOf("today").format("DD-MM-YYYY"));
      $("#rpts_payments2").text(moment().startOf("today").format("DD-MM-YYYY"));
      get_schemename();
      get_employee_list();
      // get_area_list();
      get_joined_through_list();
      get_member_account_type();
      get_member_report();
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment(),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          $("#rpts_payments1").text(start.format("DD-MM-YYYY"));
          $("#rpts_payments2").text(end.format("DD-MM-YYYY"));
        }
      );
      break;
    //Member report ends here
    //Maturity report starts here
    case "maturity_report":
      $("#rpts_payments1").empty();
      $("#rpts_payments2").empty();
      $("#rpts_payments1").text(moment().startOf("today").format("DD-MM-YYYY"));
      $("#rpts_payments2").text(moment().startOf("today").format("DD-MM-YYYY"));
      get_schemename();
      get_employee_list();
      get_maturity_report();
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment(),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          $("#rpts_payments1").text(start.format("DD-MM-YYYY"));
          $("#rpts_payments2").text(end.format("DD-MM-YYYY"));
        }
      );
      break;
    //Maturity report ends here
    //Scheme wise mode wise report starts here
    //Added by Durga 29-06-2023 starts here
    case "payment_modeandgroupwise_data":
      $("#rpts_payments1").empty();
      $("#rpts_payments2").empty();
      get_schemename();
      var date = new Date();
      var id_branch = $("#branch_select").val();
      console.log(moment().startOf("today").format("YYYY-MM-DD"));
      //generate_paymode_groupwise_list(moment().startOf('month').format('MMMM D, YYYY'),moment().endOf('month').format('MMMM D, YYYY'));
      generate_paymode_groupwise_list(
        moment().startOf("today").format("DD-MM-YYYY"),
        moment().startOf("today").format("DD-MM-YYYY"),
        "",
        id_branch
      );
      $("#rpts_payments1").text(moment().startOf("today").format("DD-MM-YYYY"));
      $("#rpts_payments2").text(moment().startOf("today").format("DD-MM-YYYY"));
      $("#payment_group_modewise_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment(),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          /* generate_paymode_groupwise_list(
            start.format("DD-MM-YYYY"),
            end.format("DD-MM-YYYY"),
            "",
            id_branch
          ); */
          $("#rpts_payments1").text(start.format("DD-MM-YYYY"));
          $("#rpts_payments2").text(end.format("DD-MM-YYYY"));
        }
      );
      break;
    //Scheme wise mode wise report ends here
    //Added by Durga 29-06-2023 starts here
    //outstanding report starts here
    case "scheme_customer_daterange":
      selectedValue = $('input[name="tableview"]:checked').val();
      if (selectedValue == 2) {
        var branch_select = $("#branch_select").val();
        var scheme_select = $("#scheme_select").val();
        // if (branch_select > 0 || scheme_select > 0) {
        getSchemeDateRangeList();
        // }
        $('#excel_detailed_list').show();
        $("#out_standing_table_div").css("display", "block");
        $("#summary_block").css("display", "none");
      } else {
        outstanding_summary();
        $('#excel_detailed_list').hide();
        $("#out_standing_table_div").css("display", "none");
        $("#summary_block").css("display", "block");
      }
      get_branchname();
      get_schemename();
      $("#group_select").hide();
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(0, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#from_to_repdate").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          $("#rpt_payments1").html(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").html(end.format("YYYY-MM-DD"));
          $('#hidden_from_date').val(start.format('YYYY-MM-DD'));
          $('#hidden_to_date').val(end.format('YYYY-MM-DD'));
          selectedValue = $('input[name="tableview"]:checked').val();
          $("#datesingle_search").val("");
        }
      );
      $("#singleDatepicker").daterangepicker(
        {
          singleDatePicker: true, // Use a single date picker instead of a date range picker
          locale: {
            format: "YYYY-MM-DD", // Specify the date format you want
          },
          ranges: {
            Today: [moment(), moment()],
          },
          startDate: moment(),
          endDate: moment(),
        },
        function (start) {
          $("#rpt_payments1").html("");
          $("#rpt_payments2").html("");
          selectedValue = $('input[name="tableview"]:checked').val();
          console.log(start.format("YYYY-MM-DD"));
          $("#datesingle_search").val(start.format("YYYY-MM-DD"));
        }
      );
      break;
    //outstanding report ends here
    case "msg91_translog":
      get_msg_translist();
    case "msg91_delivery":
      $("#msg_rep_date1").empty();
      $("#msg_rep_date2").empty();
      get_msgDeliv_report(
        moment().startOf("month").format("MMMM D, YYYY"),
        moment().endOf("month").format("MMMM D, YYYY")
      );
      $("#msg_rep_date1").text(moment().startOf("month").format("YYYY-MM-DD"));
      $("#msg_rep_date2").text(moment().endOf("month").format("YYYY-MM-DD"));
      $("#msg_rep_date-dt-btn").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          get_msgDeliv_report(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD")
          );
          $("#msg_rep_date1").text(start.format("YYYY-MM-DD"));
          $("#msg_rep_date2").text(end.format("YYYY-MM-DD"));
        }
      );
    // Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report -- START
    case "scheme_payment_daterange":
      // get_payModeList();
      get_schemename();
      get_employee_list();
      // get_schemeclassifyname();
      var date = new Date();
      var firstDay = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate() - 0,
        1
      );
      var from_date =
        firstDay.getDate() +
        "-" +
        (firstDay.getMonth() + 1) +
        "-" +
        firstDay.getFullYear();
      var to_date =
        date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
      $("#rpt_payments1").html(from_date);
      $("#rpt_payments2").html(to_date);
      //	get_schemewise_Data(from_date,to_date);
      getPaymentDateRangeList(from_date, to_date);
      getPaymentSummary(from_date, to_date);
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(0, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          //getPaymentDateRangeList(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
          //    get_schemewise_Data(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
          $("#rpt_payments1").html(start.format("DD-MM-YYYY"));
          $("#rpt_payments2").html(end.format("DD-MM-YYYY"));
        }
      );
      break;
    // Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report  -- END
    /*	// Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report -- START
      case 'scheme_payment_daterange':
            get_schemename();
             // get_schemeclassifyname();
            var date = new Date();
            var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1); 
            var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
            var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
            $('#rpt_payments1').html(from_date);
            $('#rpt_payments2').html(to_date);
          //	get_schemewise_Data(from_date,to_date);
            getPaymentDateRangeList(from_date,to_date);
            getPaymentSummary(from_date,to_date);
            $('#rpt_payment_date').daterangepicker(
            {
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
            },
            function (start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            //getPaymentDateRangeList(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
          //    get_schemewise_Data(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
            $('#rpt_payments1').html(start.format('YYYY-MM-DD'));
            $('#rpt_payments2').html(end.format('YYYY-MM-DD'));		 
            }
            ); 
        break;*/
    // Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report  -- END
    //gift report starts here
    case "gift_report":
      get_schemename();
      get_gift_names();
      var date = new Date();
      // var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 6, 1);
      var firstDay = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate() - 0,
        1
      );
      var from_date =
        firstDay.getFullYear() +
        "-" +
        (firstDay.getMonth() + 1) +
        "-" +
        firstDay.getDate();
      var to_date =
        date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
      //var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
      //var to_date=(date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());
      $("#rpt_payments1").html(from_date);
      $("#rpt_payments2").html(to_date);
      var id_branch = $("#id_branch").val();
      //getGiftIssuedList(from_date,to_date);
      getGiftIssuedList();
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(0, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          $("#rpt_payments1").html(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").html(end.format("YYYY-MM-DD"));
        }
      );
      break;
    // Gift issued report  ---  END
    case "payment_employee_wise": //hh
      $("#rpt_payments1").empty();
      $("#rpt_payments2").empty();
      get_schemename();
      get_paymentlist(
        moment().startOf("month").format("MMMM D, YYYY"),
        moment().endOf("month").format("MMMM D, YYYY")
      );
      $("#rpt_payments1").text(moment().startOf("month").format("YYYY-MM-DD"));
      $("#rpt_payments2").text(moment().endOf("month").format("YYYY-MM-DD"));
      $("#empwisereport_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          console.log($("#emp_select").find(":selected").val());
          console.log($("#branch_select").find(":selected").val());
          get_paymentlist(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD"),
            $("#branch_select").find(":selected").val(),
            $("#emp_select").find(":selected").val()
          ); //hh
          $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
        }
      );
      break;
    case "employee_wise_collection": //hh
      $("#rpt_payments1").empty();
      $("#rpt_payments2").empty();
      get_emp_summary_list(
        moment().startOf("month").format("MMMM D, YYYY"),
        moment().endOf("month").format("MMMM D, YYYY")
      );
      $("#rpt_payments1").text(moment().startOf("month").format("YYYY-MM-DD"));
      $("#rpt_payments2").text(moment().endOf("month").format("YYYY-MM-DD"));
      $("#empwisereport_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          /* get_emp_summary_list(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD")
          );  *///hh
          $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
        }
      );
      break;
    case "payment_modewise_data":
      $("#rpt_payments1").empty();
      $("#rpt_payments2").empty();
      get_schemename();
      generate_paymodewise_list(
        moment().startOf("month").format("MMMM D, YYYY"),
        moment().endOf("month").format("MMMM D, YYYY")
      );
      $("#rpt_payments1").text(moment().startOf("month").format("YYYY-MM-DD"));
      $("#rpt_payments2").text(moment().endOf("month").format("YYYY-MM-DD"));
      $("#paymentmodewise_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          var id_branch = $("#branch_select").val();
          generate_paymodewise_list(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD"),
            "",
            "",
            "",
            id_branch
          );
          $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
        }
      );
      break;
    case "accounts_schemewise":
      scheme_wise_account();
      break;
    //unpaid report starts here
    case "payment_details":
      $("#rpt_customer_unpaid1").empty();
      $("#rpt_customer_unpaid2").empty();
      //customer_wise_payment(moment().startOf('month').format('MMMM D, YYYY'),moment().endOf('month').format('MMMM D, YYYY'));
      $("#rpt_customer_unpaid1").text(moment().format("DD-MM-YYYY"));
      $("#rpt_customer_unpaid2").text(moment().format("DD-MM-YYYY"));
      customer_wise_payment();
      get_schemename();
      $("#rpt_customer_unpaid").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment(),
          endDate: moment(),
        },
        function (start, end) {
          var id_branch = $("#branch_select").val();
          //customer_wise_payment(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_branch);
          $("#rpt_customer_unpaid1").text(start.format("DD-MM-YYYY"));
          $("#rpt_customer_unpaid2").text(end.format("DD-MM-YYYY"));
          //customer_wise_payment();
        }
      );
      break;
    //unpaid report ends here
    case "inter_wallet_woc":
      get_inter_wallet_woc();
      break;
    case "payment_schemewise":
      $("#rpt_scheme_payment1").empty();
      $("#rpt_scheme_payment2").empty();
      payment_schemewise(
        moment().startOf("month").format("MMMM D, YYYY"),
        moment().endOf("month").format("MMMM D, YYYY")
      );
      $("#rpt_scheme_payment1").text(
        moment().startOf("month").format("YYYY-MM-DD")
      );
      $("#rpt_scheme_payment2").text(
        moment().endOf("month").format("YYYY-MM-DD")
      );
      $("#rpt_scheme_payment").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          var id_branch = $("#branch_select").val();
          payment_schemewise(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD"),
            id_branch
          );
          $("#rpt_scheme_payment1").text(start.format("YYYY-MM-DD"));
          $("#rpt_scheme_payment2").text(end.format("YYYY-MM-DD"));
        }
      );
      break;
    case "payment_datewise_schemedata":
      var selected_date = $("#schreport_date").val();
      generate_paymodewise_schemelist(selected_date);
      break;
    case "payment_online_offline_collec_data":
      var selected_date = $("#modereport_date").val();
      generate_online_offline_collection(selected_date);
      break;
    case "paydatewise_schcoll_data":
      var selected_date = $("#schwisereport_date").val();
      generate_paydatewise_schcoll(selected_date);
      break;
    case "payment_outstanding":
      var selected_date = $("#payoutcus").val();
      var id_branch = $("#branch_select").val();
      generate_payout_cuslist(selected_date, id_branch);
      break;
    case "interwalTrans_list":
      get_interWalTrans_list();
      $("#rpt_payments1").empty();
      $("#rpt_payments2").empty();
      $("#wallet_trans_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          get_interWalTrans_list(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD")
          );
          $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
        }
      );
      break;
    //closed acc report starts here
    case "closed_acc_report":
      get_schemename();
      get_account_type();
      var date = new Date();
      var firstDay = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate() - 0,
        1
      );
      var from_date =
        firstDay.getDate() +
        "-" +
        (firstDay.getMonth() + 1) +
        "-" +
        firstDay.getFullYear();
      //var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
      var to_date =
        date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
      $("#rpt_payments1").html(from_date);
      $("#rpt_payments2").html(to_date);
      get_closed_acc_list();
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(0, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          $("#rpt_payments1").text(start.format("DD-MM-YYYY"));
          $("#rpt_payments2").text(end.format("DD-MM-YYYY"));
        }
      );
      break;
    //closed acc report ends here
    case "collection_report":
      get_schemeclassifyname();
      var date = new Date();
      var firstDay = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate() - 0,
        1
      );
      var from_date =
        firstDay.getFullYear() +
        "-" +
        (firstDay.getMonth() + 1) +
        "-" +
        firstDay.getDate();
      var to_date =
        date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
      $("#rpt_payments1").html(from_date);
      $("#rpt_payments2").html(to_date);
      get_collection_report(from_date, to_date);
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          // getPaymentDateRangeList(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
          $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
        }
      );
      break;
    case "customer_account_details":
      var date = new Date();
      var firstDay = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate() - 30,
        1
      );
      var from_date =
        firstDay.getDate() +
        "-" +
        (firstDay.getMonth() + 1) +
        "-" +
        firstDay.getFullYear();
      var to_date =
        date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
      $("#rpt_payments1").text(from_date);
      $("#rpt_payments2").text(to_date);
      get_schemename();
      get_customer_account_details(from_date, to_date);
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          get_customer_account_details(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD"),
            "",
            ""
          );
          $("#rpt_payments1").text(start.format("YYYY-MM-DD"));
          $("#rpt_payments2").text(end.format("YYYY-MM-DD"));
        }
      );
      break;
    //refferal report starts here
    //emp reff_report begin
    case "employee_ref_success":
      $("#rpt_payments1").empty();
      $("#rpt_payments2").empty();
      //payment_employee_ref_success(moment().startOf('month').format('MMMM D, YYYY'),moment().endOf('month').format('MMMM D, YYYY'));
      payment_employee_ref_success(
        moment().format("DD-MM-YYYY"),
        moment().format("DD-MM-YYYY")
      );
      /* $('#rpt_emp_ref').text(moment().startOf('month').format('YYYY-MM-DD'));
      $('#rpt_emp_ref2').text(moment().endOf('month').format('YYYY-MM-DD'));  */
      $("#rpt_emp_ref").text(moment().format("DD-MM-YYYY"));
      $("#rpt_emp_ref2").text(moment().format("DD-MM-YYYY"));
      $("#rpt_payment_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          /* startDate: moment().subtract(29, 'days'),
          endDate: moment() */
          startDate: moment(),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          var id_branch = $("#branch_select").val();
          payment_employee_ref_success(
            start.format("DD-MM-YYYY"),
            end.format("DD-MM-YYYY"),
            id_branch
          );
          $("#rpt_payments1").text(start.format("DD-MM-YYYY"));
          $("#rpt_payments2").text(end.format("DD-MM-YYYY"));
        }
      );
      break;
    case "cus_ref_success":
      payment_cus_ref_success();
      $("#cus_ref_report_data").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          payment_cus_ref_success(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD")
          );
        }
      );
      break;
    case "customer_enquiry":
      $("#feed_filter_status,#feed_filter_type")
        .select2()
        .on("change", function (e) {
          get_enquiry_list();
        });
      get_enquiry_list();
      $("#enquiry_date").daterangepicker(
        {
          ranges: {
            Today: [moment(), moment()],
            Yesterday: [
              moment().subtract(1, "days"),
              moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
              moment().subtract(1, "month").startOf("month"),
              moment().subtract(1, "month").endOf("month"),
            ],
          },
          startDate: moment().subtract(29, "days"),
          endDate: moment(),
        },
        function (start, end) {
          $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
          );
          get_enquiry_list(
            start.format("YYYY-MM-DD"),
            end.format("YYYY-MM-DD")
          );
        }
      );
      break;
    //emp reff_report end
    default:
      break;
  }
  if (ctrl_page[2] == "range") {
    console.log($("input[name=pay_type]").val());
    generate_payment_list(
      moment().startOf("month").format("MMMM D, YYYY"),
      moment().endOf("month").format("MMMM D, YYYY")
    );
    $("#rpt_payment_date1").empty();
    $("#rpt_payment_date2").empty();
    $("#rpt_payment_date1").text(
      moment().startOf("month").format("YYYY-MM-DD")
    );
    $("#rpt_payment_date2").text(moment().endOf("month").format("YYYY-MM-DD"));
    $("#rpt_payment_date").daterangepicker(
      {
        ranges: {
          Today: [moment(), moment()],
          Yesterday: [
            moment().subtract(1, "days"),
            moment().subtract(1, "days"),
          ],
          "Last 7 Days": [moment().subtract(6, "days"), moment()],
          "Last 30 Days": [moment().subtract(29, "days"), moment()],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
      },
      function (start, end) {
        $("#reportrange span").html(
          start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        $("#rpt_payment_date1").text(start.format("YYYY-MM-DD"));
        $("#rpt_payment_date2").text(end.format("YYYY-MM-DD"));
        generate_payment_list(
          start.format("YYYY-MM-DD"),
          end.format("YYYY-MM-DD")
        );
      }
    );
  }
  //var payment_list = $('.payreport_customer').DataTable( { "dom": 'T<"clear">lfrtip', "tableTools": { "aButtons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] } } );
  $("#check_transaction").click(function () {
    // $('form#failed_txn').submit();
    var data = { "txn_ids[]": [] };
    $("input[name='txnid[]']:checked").each(function () {
      data["txn_ids[]"].push($(this).val());
    });
    $.ajax({
      type: "POST",
      url: base_url + "index.php/payment/verify",
      data: data,
      success: function (response) {
        $("#alert_msg").html(response);
        $(".alert").css("display", "block");
        setTimeout(function () {
          window.location.reload();
          /* or window.location = window.location.href; */
        }, 5000);
      },
    });
  });
  //date range payment report
  $("#gen_rep").click(function () {
    data = $("#payment_range").serialize();
    var p_status = $("input:radio[name='pay_status']:checked").val();
    var p_mode = $("input:radio[name='pay_mode']:checked").val();
    var frm_date = $("#frm_date").val();
    var to_date = $("#to_date").val();
    p_status = p_status != "" ? p_status : "ALL";
    p_mode = p_mode != "" ? p_mode : " ";
    if (frm_date != "" && to_date != "") {
      $.ajax({
        type: "POST",
        url: base_url + "index.php/reports/payment/range/date",
        data: {
          from_date: frm_date,
          to_date: to_date,
          p_status: p_status,
          p_mode: p_mode,
        },
        dataType: "json",
        success: function (data) {
          console.log(data);
          table_list =
            '<table id="payment_list" class="table table-bordered table-striped text-center"><thead>' +
            "<tr>" +
            "<th>P.ID</th>" +
            "<th>Paid Date</th>" +
            //	'<th>Receipt.No</th>'+
            "<th>Trans ID</th>" +
            "<th>PayU ID</th>" +
            "<th>Client ID</th>" +
            "<th>Name</th>" +
            "<th>Mobile</th>" +
            "<th>Sch. Code</th>" +
            "<th>Ms.No</th>" +
            "<th>Pay Mode</th>" +
            "<th>Card No</th>" +
            "<th>Metalrate (&#8377;)</th>" +
            "<th>Metalweight (g)</th>" +
            "<th>Amount (&#8377;)</th>" +
            //	'<th>Charge (&#8377;)</th>'+
            "<th>Total Paid (&#8377;)</th>" +
            "<th>Pay Status</th>" +
            "<th>Remark</th>" +
            "</tr></thead><tbody></tbody></table>";
          //appending header
          $("#report_wrapper").html(table_list);
          trHTML = "";
          /*var payment_list = $('#payment_list').DataTable();*/
          var payment_list = $(".payment_list").DataTable({
            dom: 'T<"clear">lfrtip',
            tableTools: {
              aButtons: [
                { sExtends: "xls", oSelectorOpts: { page: "current" } },
                { sExtends: "pdf", oSelectorOpts: { page: "current" } },
              ],
            },
          });
          //destroy datatable
          payment_list.destroy();
          $.each(data, function (i, item) {
            trHTML +=
              "<tr>" +
              "<td>" +
              item.id_payment +
              "</td>" +
              "<td>" +
              item.trans_date +
              "</td>" +
              //	'<td>' + item.receipt_jil + '</td>' +
              "<td>" +
              item.id_transaction +
              "</td>" +
              "<td>" +
              item.payu_id +
              "</td>" +
              "<td>" +
              item.client_id +
              "</td>" +
              "<td>" +
              item.name +
              "</td>" +
              "<td>" +
              item.mobile +
              "</td>" +
              "<td>" +
              item.group_code +
              "</td>" +
              "<td>" +
              item.msno +
              "</td>" +
              "<td>" +
              item.payment_mode +
              "</td>" +
              "<td>" +
              item.card_no +
              "</td>" +
              "<td>" +
              item.rate +
              "</td>" +
              "<td>" +
              item.weight +
              "</td>" +
              "<td>" +
              item.amount +
              "</td>" +
              //	'<td>' + item.bank_charges + '</td>' +
              "<td>" +
              item.paid_amt +
              "</td>" +
              "<td>" +
              item.pay_status +
              "</td>" +
              "<td>" +
              item.remark +
              "</td>" +
              "</tr>";
          });
          $("#payment_list > tbody").html(trHTML);
          /* payment_list =	$('#payment_list').dataTable({
               "bPaginate": true,
               "bLengthChange": true,
               "bFilter": true,
               "bSort": true,
               "bInfo": true,
               "bAutoWidth": true
               });*/
          payment_list = $("#payment_list").DataTable({
            dom: 'T<"clear">lfrtip',
            tableTools: {
              aButtons: [
                { sExtends: "xls", oSelectorOpts: { page: "current" } },
                { sExtends: "pdf", oSelectorOpts: { page: "current" } },
              ],
            },
          });
        },
      });
    }
  });
  //end of date range payment report
  //emp_reff_begin
  function payment_cus_ref_success(from_date = "", to_date = "") {
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $.ajax({
      type: "POST",
      url:
        base_url +
        "index.php/reports/payment_cus_ref_success?nocache=" +
        my_Date.getUTCSeconds(),
      data:
        from_date != "" && to_date != ""
          ? { from_date: from_date, to_date: to_date }
          : "",
      dataType: "json",
      success: function (data) {
        var oTable = $("#cus_refferal").DataTable();
        oTable.clear().draw();
        if (data.accounts != null && data.accounts.length > 0) {
          oTable = $("#cus_refferal").dataTable({
            bDestroy: true,
            bInfo: false,
            bFilter: true,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            dom: "Bfrtip",
            buttons: [
              {
                extend: "print",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
            ],
            aaData: data.accounts,
            aoColumns: [
              {
                mDataProp: function (row, type, val, meta) {
                  action =
                    '<a href="' +
                    base_url +
                    "index.php/reports/payment/cus_refferl_account/" +
                    row.mobile +
                    '" target="_blank">' +
                    row.id_customer +
                    "</a>";
                  return action;
                },
              },
              { mDataProp: "name" },
              { mDataProp: "cus_referalcode" },
              { mDataProp: "refferal_count" },
              { mDataProp: "benifits" },
            ],
            footerCallback: function (row, data, start, end, display) {
              if (data.length > 0) {
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  // paid Total
                  paid = api
                    .column(4, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(4).footer()).html(parseFloat(paid));
                }
              } else {
                var data = 0;
                var api = this.api(),
                  data;
                $(api.column(4).footer()).html("");
              }
            },
          });
        }
      },
      error: function (error) {
        $("div.overlay").css("display", "none");
      },
    });
  }
  function payment_employee_ref_success(
    from_date = "",
    to_date = "",
    id_branch = ""
  ) {
    var company_name = $("#company_name").val();
    var print_title = get_title(
      from_date,
      to_date,
      "Employee Refferral Report"
    );
    my_Date = new Date();
    $("#referral_date_range").html(from_date + " To " + to_date);
    $("div.overlay").css("display", "block");
    $.ajax({
      type: "POST",
      url:
        base_url +
        "index.php/reports/employee_ref_success_list?nocache=" +
        my_Date.getUTCSeconds(),
      data:
        from_date != "" && to_date != ""
          ? { from_date: from_date, to_date: to_date }
          : "",
      dataType: "json",
      success: function (data) {
        var oTable = $("#employee_refferal").DataTable();
        oTable.clear().draw();
        if (data.accounts != null && data.accounts.length > 0) {
          oTable = $("#employee_refferal").dataTable({
            bDestroy: true,
            bInfo: true,
            bFilter: true,
            bSort: true,
            dom: "lBfrtip",
            pageLength: 25,
            lengthMenu: [
              [-1, 25, 50, 100, 250],
              ["All", 25, 50, 100, 250],
            ],
            // "dom": 'Bfrtip',
            columnDefs: [
              {
                targets: [2, 3, 4, 5],
                className: "dt-right",
              },
              {
                targets: [0, 1],
                className: "dt-left",
              },
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: "",
                messageTop: print_title,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
                footer: true,
                title:
                  "Employee Refferal Report " + from_date + " To " + to_date,
              },
              /*	{
                  extend:'pdf',
                  footer: true,
                  title:'Employee Refferal Report'
                }*/
            ],
            aaData: data.accounts,
            aoColumns: [
              {
                mDataProp: function (row, type, val, meta) {
                  //action = '<input type="checkbox" id="select_emp_'+row.id_employee+'" class="select_idemp"  value="'+row.id_employee+'">'+'&nbsp;&nbsp;&nbsp;&nbsp;'+'<a href="'+base_url+'index.php/reports/payment/refferl_account/'+row.emp_code+'" target="_blank">'+row.id_employee+'</a>';
                  action =
                    '<input type="checkbox" id="select_emp_' +
                    row.id_employee +
                    '" class="select_idemp"  value="' +
                    row.id_employee +
                    '">' +
                    "&nbsp;&nbsp;&nbsp;&nbsp;" +
                    '<a href="' +
                    base_url +
                    "index.php/reports/payment/refferl_account/" +
                    row.emp_code +
                    "/" +
                    from_date +
                    "/" +
                    to_date +
                    '" target="_blank">' +
                    row.id_employee +
                    "</a>";
                  return action;
                },
              },
              { mDataProp: "name" },
              { mDataProp: "emp_code" },
              { mDataProp: "refferal_count" },
              /*   { "mDataProp": function ( row, type, val, meta ) {
                  if(row.issue_type == 'Credit')
                  {
                    action = '<b style="color:#48e116;">Credit</b>';
                  }else{
                    action = '<b style="color:red;">Debit</b>';
                  }
                  return action;
                }},     
                { "mDataProp": "total_amount" }, */
              // 	{ "mDataProp": "total_amount" },
              { mDataProp: "credit_benifits" },
              { mDataProp: "debit_benifits" },
            ],
            footerCallback: function (row, data, start, end, display) {
              if (data.length > 0) {
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  /* // paid Total 
                  paid = api
                    .column(5,{ page: 'current'})
                    .data()
                    .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0 );
                  $( api.column(5).footer() ).html(parseFloat(paid)); */
                  // Amt Total  //hh
                  /* paid = api
                     .column(4,{ page: 'current'})
                     .data()
                     .reduce( function (a, b) {
                       return intVal(a) + intVal(b);
                     }, 0 );
                   $( api.column(4).footer() ).html(parseFloat(paid));*/
                  // creditAmt Total
                  paid = api
                    .column(4, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(4).footer()).html(parseFloat(paid));
                  // debitAmt Total
                  paid = api
                    .column(5, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(5).footer()).html(parseFloat(paid));
                }
              } else {
                var data = 0;
                var api = this.api(),
                  data;
                $(api.column(4).footer()).html("");
                //$( api.column(5).footer() ).html('');
              }
            },
          });
        }
      },
      error: function (error) {
        $("div.overlay").css("display", "none");
      },
    });
  }
  // Employee referral Reports//
  $(document).on("click", "#select_emp", function (e) {
    if ($(this).prop("checked") == true) {
      $("tbody tr td input[type='checkbox']").prop("checked", true);
    } else if ($(this).prop("checked") == false) {
      $("tbody tr td input[type='checkbox']").prop("checked", false);
    }
  });
  $(document).on("click", ".print_emp", function (e) {
    var empdata = [];
    var ids = "";
    $("#employee_refferal tbody tr").each(function (index, value) {
      if (!$(value).find(".select_idemp").is(":checked")) {
        $(value).find(".select_idemp").empty();
      } else if ($(value).find(".select_idemp").is(":checked")) {
        var id_employee = $(value).find(".select_idemp").val();
        var data = { id_employee: id_employee };
        // var sech = JSON.stringify(data);
        empdata.push(data);
        ids +=
          '<input type="hidden" name=emp[] value=' +
          $(value).find(".select_idemp").val() +
          ">";
      } else {
        msg =
          '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Select to proceed</div>';
        $("div.overlay").css("display", "none");
        //stop the form from submitting
        $("#error-msg").html(msg);
        return false;
      }
    });
    if (empdata.length > 0 && ids != "") {
      $(".refdata").append(ids);
      $(".print_emp").attr("disabled", true);
      $("#emp_ref").submit();
    }
  });
  // Employee referral Reports//
  //emp_reff_end
  /*Coded by ARVK*/
  $("#report_date")
    .datepicker({
      dateFormat: "dd-mm-yyyy",
    })
    .on("changeDate", function (ev) {
      $(this).datepicker("hide");
      var selected_date = $("#report_date").val();
      //console.log(selected_date);
      my_Date = new Date();
      $.ajax({
        type: "POST",
        url:
          base_url +
          "index.php/reports/payment_datewise_ajax?nocache=" +
          my_Date.getUTCSeconds(),
        data: { date: selected_date },
        dataType: "json",
        success: function (data) {
          var oTable = $("#datewise_paid_report").DataTable();
          oTable.clear().draw();
          var today_total = 0;
          $.each(data.payments, function (i, item) {
            today_total =
              parseInt(today_total) + parseInt(item.classification_total);
          });
          var closing_bal =
            parseInt(today_total) + parseInt(data.opening_balance);
          /*console.log(parseFloat(data.opening_balance).toFixed(2));
          console.log(parseFloat(today_total).toFixed(2));
          console.log(parseFloat(closing_bal).toFixed(2));*/
          if (data.payments != null && data.payments.length > 0) {
            oTable = $("#datewise_paid_report").dataTable({
              bDestroy: true,
              bInfo: true,
              bFilter: true,
              bSort: true,
              dom: 'T<"clear">lfrtip',
              tableTools: {
                aButtons: [
                  { sExtends: "xls", oSelectorOpts: { page: "current" } },
                  { sExtends: "pdf", oSelectorOpts: { page: "current" } },
                ],
              },
              aaData: data.payments,
              aoColumns: [
                { mDataProp: "id_classification" },
                { mDataProp: "classification_name" },
                { mDataProp: "classification_total" },
              ],
            });
          }
          $("#open_bal").html(parseFloat(data.opening_balance).toFixed(2));
          $("#tot_coll").html(parseFloat(today_total).toFixed(2));
          $("#close_bal").html(parseFloat(closing_bal).toFixed(2));
        },
      });
    });
  // payment_datewise_schemedata
  $("#schreport_date")
    .datepicker({
      dateFormat: "yyyy-mm-dd",
    })
    .on("changeDate", function (ev) {
      $(this).datepicker("hide");
      var selected_date = $("#schreport_date").val();
      var id_branch = $("#branch_select").val();
      var id_employee = $("#id_employee").val();
      var added_by = $("#added_by").val();
      if (selected_date != "") {
        generate_paymodewise_schemelist(
          selected_date,
          id_branch,
          id_employee,
          added_by
        );
      }
    });
  $("#modereport_date")
    .datepicker({
      dateFormat: "yyyy-mm-dd",
    })
    .on("changeDate", function (ev) {
      $(this).datepicker("hide");
      var selected_date = $("#modereport_date").val();
      var added_by = $("#added_by").val();
      if (selected_date != "") {
        generate_online_offline_collection(selected_date, added_by);
      }
    });
  // payment outstanding
  $("#payoutcus")
    .datepicker({
      dateFormat: "yyyy-mm-dd",
    })
    .on("changeDate", function (ev) {
      $(this).datepicker("hide");
      var selected_date = $("#payoutcus").val();
      var id_branch = $("#branch_select").val();
      if (selected_date != "") {
        generate_payout_cuslist(selected_date, id_branch);
      }
    });
  // payment_datewise_schemedata
  ////paydatewise_schcoll_data
  $("#schwisereport_date")
    .datepicker({
      dateFormat: "yyyy-mm-dd",
    })
    .on("changeDate", function (ev) {
      $(this).datepicker("hide");
      var selected_date = $("#schwisereport_date").val();
      var id_branch = $("#branch_select").val();
      if (selected_date != "") {
        generate_paydatewise_schcoll(selected_date, id_branch);
      }
    });
  //paydatewise_schcoll_data
});
$('#scheme_select').select2().on("change", function (e) {
  switch (ctrl_page[1]) {
    case 'payment_daterange':
      if (this.value != '') {
        var from_date = $('#rpt_payments1').text();
        var to_date = $('#rpt_payments2').text();
        var id_branch = $('#branch_select').val();
        var id_employee = $('#id_employee').val();
        var id_scheme = $(this).val();
        generate_payment_daterange(from_date, to_date, '', '', id_scheme, id_branch, id_employee);
      }
      break;
    case 'payment_modewise_data':
      if (this.value != '') {
        var from_date = $('#rpt_payments1').text();
        var to_date = $('#rpt_payments2').text();
        var id_branch = $('#branch_select').val();
        var id_scheme = $(this).val();
        generate_paymodewise_list(from_date, to_date, '', '', id_scheme, id_branch);
      }
      break;
    case 'customer_account_details':
      var from_date = $('#rpt_payments1').text();
      var to_date = $('#rpt_payments2').text();
      get_customer_account_details(from_date, to_date);
      break;
  }
});
$('#update_status').click(function () {
  get_table_values();
});
//for select all
$('#select_all').click(function (e) {
  if (e.stopPropagation !== undefined) {
    e.stopPropagation();
    $('input[name="id_payment"]').prop('checked', $(this).prop('checked'));
  } else {
    e.cancelBubble = true;
  }
});
function generate_failed_payments() {
  $("#img_loader").show();
  $.ajax({
    type: "GET",
    url: base_url + "index.php/reports/get/payment/failed",
    dataType: "json",
    success: function (data) {
      // console.log(data);
      table_list =
        '<table id="payment_list" class="table table-bordered table-striped text-center"><thead>' +
        "<tr>" +
        '<th><label class="checkbox-inline"><input type="checkbox" id="sel_failed_all" name="select_all" value="all"/>All</label></th>' +
        "<th>Paid Date</th>" +
        "<th>Trans ID</th>" +
        "<th>PayU ID</th>" +
        "<th>Client ID</th>" +
        "<th>Name</th>" +
        "<th>Mobile</th>" +
        "<th>Chit.No</th>" +
        "<th>Pay Mode</th>" +
        "<th>Metalrate (&#8377;)</th>" +
        "<th>Metalweight (g)</th>" +
        "<th>Amount (&#8377;)</th>" +
        "<th>Total Paid (&#8377;)</th>" +
        "<th>Pay Status</th>" +
        "<th>Remark</th>" +
        "</tr></thead><tbody></tbody></table>";
      //appending header
      $("#failed_report").html(table_list);
      trHTML = "";
      /*  var payment_list = $('#payment_list').DataTable();*/
      payment_list = $("#payment_list").DataTable({
        dom: 'T<"clear">lfrtip',
        tableTools: {
          aButtons: [{ sExtends: "xls", oSelectorOpts: { page: "current" } }],
        },
      });
      //destroy datatable
      payment_list.destroy();
      $.each(data, function (i, item) {
        trHTML +=
          "<tr>" +
          '<td><label class="checkbox-inline"><input type="checkbox" name="txnid[]" value="' +
          item.trans_id +
          '"/> ' +
          item.id_payment +
          "</label></td>" +
          "<td>" +
          item.trans_date +
          "</td>" +
          "<td>" +
          item.trans_id +
          "</td>" +
          "<td>" +
          item.payu_id +
          "</td>" +
          "<td>" +
          item.client_id +
          "</td>" +
          "<td>" +
          item.name +
          "</td>" +
          "<td>" +
          item.mobile +
          "</td>" +
          "<td>" +
          item.chit_number +
          "</td>" +
          "<td>" +
          item.payment_mode +
          "</td>" +
          "<td>" +
          item.rate +
          "</td>" +
          "<td>" +
          item.weight +
          "</td>" +
          "<td>" +
          item.amount +
          "</td>" +
          "<td>" +
          item.paid_amt +
          "</td>" +
          "<td>" +
          item.pay_status +
          "</td>" +
          "<td>" +
          item.remark +
          "</td>" +
          "</tr>";
      });
      $("#payment_list > tbody").html(trHTML);
      /*payment_list =	$('#payment_list').dataTable({
          "bPaginate": true,
          "bLengthChange": true,
          "bFilter": true,
          "bSort": false,
          "bInfo": true,
          "bAutoWidth": true
          });*/
      payment_list = $("#payment_list").DataTable({
        dom: 'T<"clear">lfrtip',
        tableTools: {
          aButtons: [{ sExtends: "xls", oSelectorOpts: { page: "current" } }],
        },
      });
      $("#sel_failed_all").click(function (event) {
        $("tbody tr td input[type='checkbox']").prop(
          "checked",
          $(this).prop("checked")
        );
        event.stopPropagation();
      });
      $("#img_loader").hide();
    },
  });
}
function get_postdated_data(filter, mode, status) {
  my_Date = new Date();
  $("body").addClass("sidebar-collapse");
  $(".overlay").css("display", "block");
  $.ajax({
    type: "POST",
    url:
      base_url +
      "index.php/postdated/status/list?nocache=" +
      my_Date.getUTCSeconds(),
    data: { payment: { filter: filter, mode: mode, status: status } },
    // data:{'payment':{'status':status}},
    dataType: "json",
    success: function (data) {
      if (ctrl_page[6] == 2) {
        $("#datepicker_container").css("display", "none");
      } else {
        $("#datepicker_container").css("display", "block");
      }
      $("#total_payments").text(data.payments.length);
      //fill list
      set_postdated_list(data.payments);
      var dropdown_select =
        data.payments.length > 0 ? (ctrl_page[6] == 7 ? 2 : 1) : "";
      //fill Dropdown
      fill_status_dropdown(
        "sel_payment_status",
        data.payment_status,
        dropdown_select
      );
      //remove cheque_no column for ecs
      /* 	  if(ctrl_page[5]=='ecs')
           {
            $('#rep_post_payment_list tr').find('td:eq(4),th:eq(4)').remove();
           } */
      $(".overlay").css("display", "none");
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log("some error");
      $(".overlay").css("display", "none");
    },
  });
}
function sumByClass(column_class) {
  var sum = 0;
  // iterate through each td based on class and add the values
  $("." + column_class).each(function () {
    var value = $(this).text();
    // add only if the value is number
    if (!isNaN(value) && value.length != 0) {
      sum += parseFloat(value);
    }
  });
  return sum;
}
function set_postdated_list(data) {
  var payment = data;
  var columns = [
    {
      mDataProp: function (row, type, val, meta) {
        id = row.id_post_payment;
        return (
          "<label class='checkbox-inline'><input type='checkbox' class='flat-red' name='id_payment' value='" +
          id +
          "' /> " +
          id +
          " </label>"
        );
      },
    },
    { mDataProp: "date_payment" },
    { mDataProp: "cus_name" },
    { mDataProp: "account_name" },
    { mDataProp: "scheme_acc_number" },
    { mDataProp: "pay_mode" },
    {
      mDataProp: function (row, type, val, meta) {
        return (
          "<input type='hidden' name='cheque_no' value='" +
          row.cheque_no +
          "' />" +
          row.cheque_no
        );
      },
    },
    { mDataProp: "payee_short_code" },
    { mDataProp: "drawee_account_name" },
    { mDataProp: "drawee_acc_no" },
    { mDataProp: "drawee_short_code" },
    { mDataProp: "amount" },
    {
      mDataProp: function (row, type, val, meta) {
        return "<input type='text' class='form-control input-sm' name='payment_ref_number' />";
      },
    },
    {
      mDataProp: function (row, type, val, meta) {
        action_content =
          "<input type='hidden' name='date_payment' value='" +
          row.date_payment +
          "' />" +
          "<input type='hidden' name='id_scheme_account' value='" +
          row.id_scheme_account +
          "' />" +
          "<input type='hidden' name='pay_mode' value='" +
          row.pay_mode +
          "' />" +
          "<input type='hidden' name='bank_acc_no' value='" +
          row.payee_acc_no +
          "' />" +
          "<input type='hidden' name='bank_name' value='" +
          row.payee_bank +
          "' />" +
          "<input type='hidden' class='pdc_amount' name='payment_amount' value='" +
          row.amount +
          "' />" +
          "<input type='hidden' name='payment_status'/>" +
          "<input type='hidden'  name='date_presented' />" +
          "<input type='hidden'  name='charges' />" +
          "<span class='label bg-" +
          row.status_color +
          "-active'>" +
          row.payment_status +
          "</span>";
        //"<select class='form-control pay_status' name='payment_status'>";
        return action_content;
      },
    },
    {
      mDataProp: function (row, type, val, meta) {
        id = row.id_post_payment;
        edit_url = base_url + "index.php/postdated/payment_entry/edit/" + id;
        status_url =
          base_url + "index.php/postdated/payment_entry/status/" + id;
        action_content =
          '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
          '<li><a href="' +
          edit_url +
          '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
          '<li><a href="' +
          status_url +
          '" class="btn-edit"><i class="fa fa-search-plus" ></i> Status</a></li></ul></div>';
        return action_content;
      },
    },
  ];
  generate_datatable("rep_post_payment_list", data, columns);
  /*var tfoot = "<tfoot><td colspan='9'>Total</td><td>"+sumByClass('pdc_amount')+"</td><td></td><td></td></tfoot>";
  $('#rep_post_payment_list').append(tfoot);		*/
}
//to fill select box
function fill_status_dropdown(elementID, data, selected = "") {
  $.each(data, function (key, item) {
    $('#' + elementID).append(
      $("<option></option>")
        .attr("value", item.id_status_msg)
        .text(item.payment_status)
    );
  });
  $("#" + elementID).select2({
    placeholder: 'Select payment status',
    allowClear: true
  });
  $("#" + elementID).select2("val", selected);
}
//to fill select box
function fill_dropdownbyclass(elementID, data, selected = "") {
  $.each(data, function (key, item) {
    $('.' + elementID).append(
      $("<option></option>")
        .attr("value", item.id_status_msg)
        .text(item.payment_status)
    );
  });
  $("." + elementID).select2({
    placeholder: 'Select payment status',
    allowClear: true
  });
  $("." + elementID).select2("val", selected);
}
//for get all selected values
function get_table_values() {
  var table_data = [];
  var values = {};
  $("#rep_post_payment_list > tbody > tr").each(function (i) {
    values = new Object;
    if ($(this).find('input[type="checkbox"]').is(':checked') && $('#sel_payment_status').val() != null) {
      //update status for selected row
      $('input[name="payment_status"]').val($('#sel_payment_status').val());
      $('.pay_status').select2("val", $('#sel_payment_status').val());
      $('input[name="charges"]').val($('#sub_charge').val());
      //fetch values
      $('input', this).each(function () {
        if ($(this).val() != '') {
          if ($(this).attr('type') == 'checkbox') {
            values[$(this).attr('name')] = ($(this).is(':checked') ? $(this).val() : 0);
          }
          else {
            values[$(this).attr('name')] = $(this).val();
          }
        }
      });
      table_data.push(values);
    }
    console.log(table_data);
  });
  $("#sel_payment_status").select2("val", '');
  //removes the first elemet
  //table_data.shift(); 
  update_postdata(table_data);
}
function update_postdata(data) {
  if (data.length != 0) {
    $("div.overlay").css("display", "block");
    var postData = { 'postpay_data': JSON.stringify(data) };
    var my_Date = new Date();
    $.ajax({
      url: base_url + "index.php/postdated/payment/update",
      type: "POST",
      data: postData,
      success: function (result) {
        $("div.overlay").css("display", "none");
        $('#pdp-alert').delay(500).fadeIn('normal', function () {
          $(this).find("p").html(result);
          $(this).addClass("alert-success ");
          $(this).delay(1000).fadeOut();
        });
        window.location.reload();
      },
      error: function (error) {
        console.log(error);
        $("div.overlay").css("display", "none");
        $('#pdp-alert').delay(500).fadeIn('normal', function () {
          $(this).find("p").html("Unable to proceed request");
          $(this).addClass("alert-danger ");
          $(this).delay(2500).fadeOut();
        });
      }
    });
  }
}
function generate_payment_list(
  from_date = "",
  to_date = "",
  type = "",
  limit = ""
) {
  my_Date = new Date();
  var branch = $("#branch_select").val();
  //console.log(branch);
  $.ajax({
    url:
      base_url +
      "index.php/payment/ajax_list/range?nocache=" +
      my_Date.getUTCSeconds(),
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          type: type,
          limit: limit,
          id_branch: branch,
        }
        : "",
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var payment = data.data;
      var access = data.access;
      var oTable = $("#report_payment_daterange").DataTable();
      oTable.clear().draw();
      if (payment != null && payment.length > 0) {
        oTable = $("#report_payment_daterange").dataTable({
          bDestroy: true,
          bInfo: true,
          bFilter: true,
          scrollX: "100%",
          bSort: true,
          dom: "lBfrtip",
          order: [[0, "desc"]],
          buttons: [
            {
              extend: "print",
              footer: true,
              title:
                "Payment Date Range " +
                $("#rpt_payment_date1").text() +
                " - " +
                $("#rpt_payment_date2").text(),
              customize: function (win) {
                $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "inherit");
              },
            },
            {
              extend: "excel",
              footer: true,
              title:
                "Payment Date Range " +
                $("#rpt_payment_date1").text() +
                " - " +
                $("#rpt_payment_date2").text(),
            },
          ],
          aaData: payment,
          aoColumns: [
            { mDataProp: "id_payment" },
            { mDataProp: "date_payment" },
            { mDataProp: "name" },
            { mDataProp: "branch_name" },
            { mDataProp: "account_name" },
            { mDataProp: "scheme_acc_number" },
            { mDataProp: "mobile" },
            { mDataProp: "payment_type" },
            { mDataProp: "payment_mode" },
            { mDataProp: "metal_rate" },
            { mDataProp: "metal_weight" },
            { mDataProp: "payment_amount" },
            { mDataProp: "payment_ref_number" },
            {
              mDataProp: function (row, type, val, meta) {
                return (
                  "<span class='label bg-" +
                  row.status_color +
                  "-active'>" +
                  row.payment_status +
                  "</span>"
                );
              },
            },
          ],
        });
      } else {
        var swrTbl =
          '<div class="col-md-12" style="text-align:center;color:red;"><strong><span>No data available</span></strong></div>';
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
//new reports  
//payment_list_daterange acc wise filter//hh
$('#acc_Select').select2().on("change", function (e) {
  if (this.value != '') {
    $("#id_pay").val((this).value);
  }
});
//payment_list_daterange acc wise filter//hh
//payment_by_daterange // hh
function generate_payment_daterange(
  from_date = "",
  to_date = "",
  type = "",
  limit = "",
  id = "",
  id_branch = "",
  id_employee = "",
  acc = ""
) {
  my_Date = new Date();
  var date_type = $("#date_Select").find(":selected").val();
  var acc = $("#acc_Select").find(":selected").val();
  $("div.overlay").css("display", "block");
  $.ajax({
    url:
      base_url +
      "index.php/payment/ajax_list/range_list?nocache=" +
      my_Date.getUTCSeconds(),
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          type: type,
          limit: limit,
          id: id,
          id_branch: id_branch,
          id_employee: id_employee,
          date_type: date_type,
          acc: acc,
        }
        : "",
    dataType: "JSON",
    type: "POST",
    success: function (payment) {
      var gst_number = payment.gst_number;
      var data = payment.account;
      // get gst settings
      var gstsetting =
        typeof payment.account == "undefined"
          ? ""
          : payment.account[0].gst_setting;
      if (gstsetting == 1) {
        var gstno =
          "<span style='font-size:13pt; float:right;'> GST Number - " +
          gst_number +
          "</span>";
      } else {
        var gstno = "";
      }
      var oTable = $("#report_payment_daterange").DataTable();
      oTable.clear().draw();
      var fdate = new Date(from_date);
      var tdate = new Date(to_date);
      var date1 =
        fdate.getDate() +
        "." +
        (fdate.getMonth() + 1) +
        "." +
        fdate.getFullYear();
      var date2 =
        tdate.getDate() +
        "." +
        (tdate.getMonth() + 1) +
        "." +
        tdate.getFullYear();
      var select_date =
        "<b><span style='font-size:15pt;'>All Scheme Report</span></b></br>" +
        "<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;From Date&nbsp;:&nbsp;" +
        date1 +
        " &nbsp;&nbsp;To Date&nbsp;:&nbsp;" +
        date2 +
        "</span>" +
        gstno;
      if (data != null && data.length > 0) {
        var i = 1;
        if (gstsetting == 1) {
          oTable = $("#report_payment_daterange").dataTable({
            bDestroy: true,
            responsive: true,
            bInfo: false,
            bFilter: true,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            order: [[0, "desc"]],
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: select_date,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "sno" },
              { mDataProp: "code" },
              {
                mDataProp: function (row, type, val, meta) {
                  if (row.has_lucky_draw == 1 && row.is_lucky_draw == 1) {
                    return row.group_code + " " + row.scheme_acc_number;
                  } else {
                    return row.code + " " + row.scheme_acc_number;
                  }
                },
              },
              { mDataProp: "receipt_no" },
              { mDataProp: "name" },
              { mDataProp: "amount" },
              { mDataProp: "paid_installments" },
              { mDataProp: "date_payment" },
              { mDataProp: "emp_code" },
              { mDataProp: "payment_ref_number" },
              { mDataProp: "ref_trans_id" },
              { mDataProp: "card_no" },
              { mDataProp: "payment_mode" },
              { mDataProp: "metal_rate" },
              { mDataProp: "metal_weight" },
              { mDataProp: "bank_name" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.discountAmt).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  var gst = parseFloat(
                    parseFloat(row.cgst) + parseFloat(row.sgst)
                  ).toFixed(2);
                  return parseFloat(gst / 2).toFixed(3);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  var gst = parseFloat(
                    parseFloat(row.cgst) + parseFloat(row.sgst)
                  ).toFixed(2);
                  return parseFloat(gst / 2).toFixed(3);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.cgst) + parseFloat(row.sgst)
                  ).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.payment_amount) +
                    parseFloat(row.cgst) +
                    parseFloat(row.sgst)
                  ).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              if (data.length > 0) {
                var cshtotal = 0;
                var cctotal = 0;
                var dctotal = 0;
                var chqtotal = 0;
                var ecstotal = 0;
                var nbtotal = 0;
                var canceltotal = 0;
                var fptotal = 0;
                var length = 0;
                length = data.length;
                var api = this.api(),
                  data;
                var intVal = function (i) {
                  return typeof i === "string"
                    ? i.replace(/[\$,]/g, "") * 1
                    : typeof i === "number"
                      ? i
                      : 0;
                };
                for (var i = 0; i <= data.length - 1; i++) {
                  if (data[i]["payment_mode"] == "CSH") {
                    cshtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CC") {
                    cctotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "DC") {
                    dctotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CHQ") {
                    chqtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "ECS") {
                    ecstotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "NB") {
                    nbtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "FP") {
                    fptotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_status"] == "Canceled") {
                    canceltotal += parseFloat(data[i]["payment_amount"]);
                  }
                  //$( api.column(0).footer() ).html(length);
                  // Amount Total over this page
                  amttotal = api
                    .column(5, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(5).footer()).html(
                    parseFloat(amttotal).toFixed(2)
                  );
                  // pay_amt
                  pay_amt = api
                    .column(12, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(12).footer()).html(
                    parseFloat(pay_amt).toFixed(2)
                  );
                  // incen amount
                  incen = api
                    .column(13, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(13).footer()).html(parseFloat(incen).toFixed(2));
                  // gstamt amount
                  gstamt = api
                    .column(16)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(16).footer()).html(gstamt.toFixed(2));
                  $(api.column(14).footer()).html(
                    parseFloat(gstamt / 2).toFixed(3)
                  );
                  $(api.column(15).footer()).html(
                    parseFloat(gstamt / 2).toFixed(3)
                  );
                  // totamt amount
                  totamt = api
                    .column(17, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(17).footer()).html(
                    parseFloat(totamt).toFixed(2)
                  );
                }
              } else {
                var data = 0;
                var api = this.api(),
                  data;
                //$( api.column(0).footer() ).html("");
                $(api.column(5).footer()).html("");
                $(api.column(12).footer()).html("");
                $(api.column(13).footer()).html("");
                $(api.column(14).footer()).html("");
                $(api.column(15).footer()).html("");
                $(api.column(16).footer()).html("");
                $(api.column(17).footer()).html("");
              }
            },
          });
        } else {
          oTable = $("#report_payment_daterange").dataTable({
            bDestroy: true,
            responsive: true,
            bInfo: false,
            bFilter: true,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            order: [[0, "desc"]],
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: select_date,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "sno" },
              { mDataProp: "code" },
              {
                mDataProp: function (row, type, val, meta) {
                  if (row.has_lucky_draw == 1 && row.is_lucky_draw == 1) {
                    return row.group_code + " " + row.scheme_acc_number;
                  } else {
                    return row.code + " " + row.scheme_acc_number;
                  }
                },
              },
              { mDataProp: "receipt_no" },
              { mDataProp: "name" },
              { mDataProp: "amount" },
              { mDataProp: "paid_installments" },
              { mDataProp: "date_payment" },
              { mDataProp: "emp_code" },
              { mDataProp: "payment_ref_number" },
              { mDataProp: "id_transaction" },
              { mDataProp: "card_no" },
              { mDataProp: "payment_mode" },
              { mDataProp: "metal_rate" },
              { mDataProp: "metal_weight" },
              { mDataProp: "bank_name" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.discountAmt).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.incentive).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return (
                    parseFloat(row.payment_amount) + parseFloat(row.discountAmt)
                  ).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              if (data.length > 0) {
                var cshtotal = 0;
                var cctotal = 0;
                var dctotal = 0;
                var chqtotal = 0;
                var ecstotal = 0;
                var nbtotal = 0;
                var canceltotal = 0;
                var fptotal = 0;
                var length = 0;
                length = data.length;
                var api = this.api(),
                  data;
                var intVal = function (i) {
                  return typeof i === "string"
                    ? i.replace(/[\$,]/g, "") * 1
                    : typeof i === "number"
                      ? i
                      : 0;
                };
                for (var i = 0; i <= data.length - 1; i++) {
                  if (data[i]["payment_mode"] == "CSH") {
                    cshtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CC") {
                    cctotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "DC") {
                    dctotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CHQ") {
                    chqtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "ECS") {
                    ecstotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "NB") {
                    nbtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "FP") {
                    fptotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_status"] == "Canceled") {
                    canceltotal += parseFloat(data[i]["payment_amount"]);
                  }
                  //$( api.column(0).footer() ).html(length);
                  // Amount Total over this page
                  amttotal = api
                    .column(5, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(5).footer()).html(
                    parseFloat(amttotal).toFixed(2)
                  );
                  // pay_amt
                  pay_amt = api
                    .column(13, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(13).footer()).html(
                    parseFloat(pay_amt).toFixed(2)
                  );
                  //discount
                  pay_amt = api
                    .column(14, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(14).footer()).html(
                    parseFloat(pay_amt).toFixed(2)
                  );
                  // incen amount
                  incen = api
                    .column(15, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(15).footer()).html(parseFloat(incen).toFixed(2));
                  console.log(api);
                  // totamt amount
                  totamt = api
                    .column(16, { page: "current" })
                    .data()
                    .reduce(function (a, b, c) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(16).footer()).html(
                    parseFloat(totamt).toFixed(2)
                  );
                }
              } else {
                var data = 0;
                var api = this.api(),
                  data;
                //$( api.column(0).footer() ).html("");
                $(api.column(5).footer()).html("");
                $(api.column(12).footer()).html("");
                $(api.column(13).footer()).html("");
                $(api.column(14).footer()).html("");
                $(api.column(15).footer()).html("");
                $(api.column(16).footer()).html("");
              }
            },
          });
        }
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
//payment_by_daterange // hh
// paymode_wise_list
function generate_paymodewise_list(
  from_date = "",
  to_date = "",
  type = "",
  limit = "",
  id = "",
  id_branch = ""
) {
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  var date_type = $("#date_Select").find(":selected").val();
  $.ajax({
    url: base_url + "index.php/reports/paymentmodewise_datalist",
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          type: type,
          limit: limit,
          id: id,
          id_branch: id_branch,
          date_type: date_type,
        }
        : "",
    dataType: "JSON",
    type: "POST",
    success: function (payment) {
      var data = payment.account;
      var gst_number = payment.gst_number;
      var gstsetting =
        typeof payment.account == "undefined"
          ? ""
          : payment.account[0].gst_setting;
      if (gstsetting == 1) {
        var gstno =
          "<span style='font-size:13pt; float:right;'> GST Number - " +
          gst_number +
          "</span>";
      } else {
        var gstno = "";
      }
      var oTable = $("#paymentmodewise_list").DataTable();
      oTable.clear().draw();
      var fdate = new Date(from_date);
      var tdate = new Date(to_date);
      var date1 =
        fdate.getDate() +
        "." +
        (fdate.getMonth() + 1) +
        "." +
        fdate.getFullYear();
      var date2 =
        tdate.getDate() +
        "." +
        (tdate.getMonth() + 1) +
        "." +
        tdate.getFullYear();
      var select_date =
        "<b><span style='font-size:15pt;'>Collection Summary</span></b></br>" +
        "<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;From Date&nbsp;:&nbsp;" +
        date1 +
        " &nbsp;&nbsp;To Date&nbsp;:&nbsp;" +
        date2 +
        "</span>" +
        gstno;
      if (data != null && data.length > 0) {
        if (gstsetting == 1) {
          oTable = $("#paymentmodewise_list").dataTable({
            bDestroy: true,
            responsive: true,
            bInfo: false,
            bFilter: true,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: select_date,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "sno" },
              { mDataProp: "mode_name" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.sgst).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.cgst).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.cgst) + parseFloat(row.sgst)
                  ).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.payment_amount) +
                    parseFloat(row.sgst) +
                    parseFloat(row.cgst)
                  ).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              if (data.length > 0) {
                var length = 0;
                length = data.length;
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  $(api.column(0).footer()).html(length);
                  //collection amount
                  collection = api
                    .column(2, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(2).footer()).html(
                    parseFloat(collection).toFixed(2)
                  );
                  // sgst amt
                  sgst_amt = api
                    .column(3, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(3).footer()).html(
                    parseFloat(sgst_amt).toFixed(2)
                  );
                  // cgst amt
                  cgst_amt = api
                    .column(4, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(4).footer()).html(
                    parseFloat(cgst_amt).toFixed(2)
                  );
                  // cgst amt
                  tot_gst = api
                    .column(5, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(5).footer()).html(
                    parseFloat(tot_gst).toFixed(2)
                  );
                  // total
                  total = api
                    .column(6, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(6).footer()).html(parseFloat(total).toFixed(2));
                }
              } else {
                var data = 0;
                var api = this.api(),
                  data;
                $(api.column(0).footer()).html("");
                $(api.column(2).footer()).html("");
                $(api.column(3).footer()).html("");
                $(api.column(4).footer()).html("");
                $(api.column(5).footer()).html("");
                $(api.column(6).footer()).html("");
              }
            },
          });
        } else {
          oTable = $("#paymentmodewise_list").dataTable({
            bDestroy: true,
            responsive: true,
            bInfo: false,
            bFilter: true,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: select_date,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "sno" },
              { mDataProp: "mode_name" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              if (data.length > 0) {
                var length = 0;
                length = data.length;
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  $(api.column(0).footer()).html(length);
                  //collection amount
                  collection = api
                    .column(2, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(2).footer()).html(
                    parseFloat(collection).toFixed(2)
                  );
                  // total
                  total = api
                    .column(3, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(3).footer()).html(parseFloat(total).toFixed(2));
                }
              } else {
                var data = 0;
                var api = this.api(),
                  data;
                $(api.column(0).footer()).html("");
                $(api.column(2).footer()).html("");
                $(api.column(3).footer()).html("");
              }
            },
          });
        }
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
// payment_datewise_schemedata
function generate_paymodewise_schemelist(
  selected_date = "",
  id_branch = "",
  id_employee = "",
  added_by = ""
) {
  my_Date = new Date();
  var date_type = $("#date_Select").find(":selected").val();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/reports/payment_datewise_schemelist",
    data: {
      date: selected_date,
      id_branch: id_branch,
      id_employee: id_employee,
      date_type: date_type,
      added_by: added_by,
    },
    dataType: "JSON",
    type: "POST",
    success: function (payment) {
      var data = payment.account;
      var gst_number = payment.gst_number;
      var gstsetting =
        typeof payment.account == "undefined"
          ? ""
          : payment.account[0].gst_setting;
      if (gstsetting == 1) {
        var gstno =
          "<span style='font-size:13pt; float:right;'> GST Number - " +
          gst_number +
          "</span>";
      } else {
        var gstno = "";
      }
      var select_date =
        "<b><span style='font-size:15pt;'>All Scheme Report As on Date   </span></b></br>" +
        "<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;Selected Date&nbsp;&nbsp;:&nbsp;" +
        selected_date +
        "</span>" +
        gstno;
      var oTable = $("#schdatewise_report").DataTable();
      oTable.clear().draw();
      if (data != null && data.length > 0) {
        if (gstsetting == 1) {
          oTable = $("#schdatewise_report").dataTable({
            bDestroy: true,
            bInfo: false,
            bFilter: false,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: select_date,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              /* ,{
                       extend: 'excelHtml5',
                       footer: true,
                     } */
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "date_payment" },
              { mDataProp: "code" },
              { mDataProp: "branch_name" },
              { mDataProp: "receipt" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.sgst).toFixed(3);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.cgst).toFixed(3);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.cgst) + parseFloat(row.sgst)
                  ).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.payment_amount) +
                    parseFloat(row.sgst) +
                    parseFloat(row.cgst)
                  ).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              var cshtotal = 0;
              var cardtotal = 0;
              var chqtotal = 0;
              var ecstotal = 0;
              var nbtotal = 0;
              var fptotal = 0;
              var upitotal = 0;
              if (data.length > 0) {
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  if (data[i]["payment_mode"] == "CSH") {
                    cshtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "Card") {
                    cardtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CHQ") {
                    chqtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "ECS") {
                    ecstotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "NB") {
                    nbtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "FP") {
                    fptotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "UPI") {
                    upitotal += parseFloat(data[i]["payment_amount"]);
                  }
                  //console.log(data[i]['payment_mode']);
                  // total
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  //Total over this page
                  $(api.column(0).footer()).html("Total");
                  // recepit Total over this page
                  rec_tot = api
                    .column(2)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(2).footer()).html(rec_tot);
                  // pay_amt tot
                  pay_amt = api
                    .column(3)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(3).footer()).html(
                    parseFloat(pay_amt).toFixed(2)
                  );
                  // sgst_amt tot
                  sgst_amt = api
                    .column(4)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(4).footer()).html(
                    parseFloat(sgst_amt).toFixed(3)
                  );
                  // cgst_amt tot
                  cgst_amt = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(5).footer()).html(
                    parseFloat(cgst_amt).toFixed(3)
                  );
                  // tgst_amt tot
                  $(api.column(6).footer()).html(
                    parseFloat(
                      parseFloat(sgst_amt) + parseFloat(cgst_amt)
                    ).toFixed(2)
                  );
                  total = api
                    .column(7)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(7).footer()).html(parseFloat(total).toFixed(2));
                  $("tr:eq(1) td:eq(2)", api.table().footer()).html(
                    "Description"
                  );
                  var a = 2;
                  var tot_amt2 = 0;
                  $.each(payment.mode_wise, function (i, item) {
                    $("tr:eq(" + a + ") td:eq(1)", api.table().footer()).html(
                      item.payment_mode
                    );
                    $("tr:eq(" + a + ") td:eq(3)", api.table().footer()).html(
                      parseFloat(item.received_amt).toFixed(2)
                    );
                    tot_amt2 =
                      parseFloat(tot_amt2) + parseFloat(item.received_amt);
                    console.log(tot_amt2);
                    a++;
                  });
                  /*//cash
                  $('tr:eq(2) td:eq(1)', api.table().footer()).html('Cash  ');						
                  $('tr:eq(2) td:eq(3)', api.table().footer()).html(parseFloat(cshtotal).toFixed(2));	
                  //dc and cc card
                  $('tr:eq(3) td:eq(1)', api.table().footer()).html('Card');	
                  $('tr:eq(3) td:eq(3)', api.table().footer()).html(parseFloat(cardtotal).toFixed(2));
                  //Ecs
                  $('tr:eq(4) td:eq(1)', api.table().footer()).html('Ecs');	
                  $('tr:eq(4) td:eq(3)', api.table().footer()).html(parseFloat(ecstotal).toFixed(2));
                  //net baking
                  $('tr:eq(5) td:eq(1)', api.table().footer()).html('Net Banking  ');	
                  $('tr:eq(5) td:eq(3)', api.table().footer()).html(parseFloat(nbtotal).toFixed(2));
                  //fb
                  $('tr:eq(6) td:eq(1)', api.table().footer()).html('Free payment ');
                  $('tr:eq(6) td:eq(3)', api.table().footer()).html(parseFloat(fptotal).toFixed(2));
                  //Chq
                  $('tr:eq(7) td:eq(1)', api.table().footer()).html('Chq ');
                  $('tr:eq(7) td:eq(3)', api.table().footer()).html(parseFloat(chqtotal).toFixed(2));
                  //UPI
                  $('tr:eq(8) td:eq(1)', api.table().footer()).html('UPI');
                  $('tr:eq(8) td:eq(3)', api.table().footer()).html(parseFloat(upitotal).toFixed(2));*/
                  //total
                  $("tr:eq(" + a + ") td:eq(1)", api.table().footer()).html(
                    "Total"
                  );
                  $("tr:eq(" + a + ") td:eq(3)", api.table().footer()).html(
                    parseFloat(tot_amt2).toFixed(2)
                  );
                }
              } else {
                var api = this.api(),
                  data;
                $(api.column(0).footer()).html("");
                $(api.column(2).footer()).html("");
                $(api.column(3).footer()).html("");
                $(api.column(4).footer()).html("");
                $(api.column(5).footer()).html("");
                $(api.column(6).footer()).html("");
                $(api.column(7).footer()).html("");
                $(api.column(8).footer()).html("");
                $("tr:eq(2) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(3) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(4) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(5) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(6) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(7) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(8) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(9) td:eq(3)", api.table().footer()).html("");
                //Text CLEAR
                $("tr:eq(1) td:eq(2)", api.table().footer()).html("");
                $("tr:eq(2) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(3) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(4) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(5) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(6) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(7) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(8) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(9) td:eq(1)", api.table().footer()).html("");
              }
            },
          });
        } else {
          oTable = $("#schdatewise_report").dataTable({
            bDestroy: true,
            bInfo: false,
            bFilter: false,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: select_date,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "payment_mode" },
              { mDataProp: "code" },
              { mDataProp: "branch_name" },
              { mDataProp: "receipt" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              var cshtotal = 0;
              var cardtotal = 0;
              var chqtotal = 0;
              var ecstotal = 0;
              var nbtotal = 0;
              var fptotal = 0;
              var upitotal = 0;
              if (data.length > 0) {
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  if (data[i]["payment_mode"] == "CSH") {
                    cshtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "Card") {
                    cardtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CHQ") {
                    chqtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "ECS") {
                    ecstotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "NB") {
                    nbtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "FP") {
                    fptotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "UPI") {
                    upitotal += parseFloat(data[i]["payment_amount"]);
                  }
                  //console.log(data[i]['payment_mode']);
                  // total
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  //Total over this page
                  $(api.column(0).footer()).html("Total");
                  // recepit Total over this page
                  rec_tot = api
                    .column(2)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(2).footer()).html(rec_tot);
                  // pay_amt tot
                  pay_amt = api
                    .column(4)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(4).footer()).html(
                    parseFloat(pay_amt).toFixed(2)
                  );
                  total = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(5).footer()).html(parseFloat(total).toFixed(2));
                  $("tr:eq(1) td:eq(2)", api.table().footer()).html(
                    "Description"
                  );
                  var b = 2;
                  var tot_amt1 = 0;
                  $.each(payment.mode_wise, function (i, item) {
                    $("tr:eq(" + b + ") td:eq(1)", api.table().footer()).html(
                      item.payment_mode
                    );
                    $("tr:eq(" + b + ") td:eq(3)", api.table().footer()).html(
                      parseFloat(
                        item.received_amt != null ? item.received_amt : 0
                      ).toFixed(2)
                    );
                    tot_amt1 =
                      parseFloat(tot_amt1) +
                      parseFloat(
                        item.received_amt != null ? item.received_amt : 0
                      );
                    console.log(tot_amt1);
                    b++;
                  });
                  /*//cash
                  $('tr:eq(2) td:eq(1)', api.table().footer()).html('Cash  ');						
                  $('tr:eq(2) td:eq(3)', api.table().footer()).html(parseFloat(cshtotal).toFixed(2));	
                  //dc and cc card
                  $('tr:eq(3) td:eq(1)', api.table().footer()).html('Card');	
                  $('tr:eq(3) td:eq(3)', api.table().footer()).html(parseFloat(cardtotal).toFixed(2));
                  //Ecs
                  $('tr:eq(4) td:eq(1)', api.table().footer()).html('Ecs');	
                  $('tr:eq(4) td:eq(3)', api.table().footer()).html(parseFloat(ecstotal).toFixed(2));
                  //net baking
                  $('tr:eq(5) td:eq(1)', api.table().footer()).html('Net Banking  ');	
                  $('tr:eq(5) td:eq(3)', api.table().footer()).html(parseFloat(nbtotal).toFixed(2));
                  //fb
                  $('tr:eq(6) td:eq(1)', api.table().footer()).html('Free payment ');
                  $('tr:eq(6) td:eq(3)', api.table().footer()).html(parseFloat(fptotal).toFixed(2));
                  //Chq
                  $('tr:eq(7) td:eq(1)', api.table().footer()).html('Chq ');
                  $('tr:eq(7) td:eq(3)', api.table().footer()).html(parseFloat(chqtotal).toFixed(2));
                  //UPI
                  $('tr:eq(8) td:eq(1)', api.table().footer()).html('UPI');
                  $('tr:eq(8) td:eq(3)', api.table().footer()).html(parseFloat(upitotal).toFixed(2));*/
                  //total
                  $("tr:eq(" + b + ") td:eq(1)", api.table().footer()).html(
                    "Total"
                  );
                  $("tr:eq(" + b + ") td:eq(3)", api.table().footer()).html(
                    parseFloat(tot_amt1).toFixed(2)
                  );
                }
              } else {
                var api = this.api(),
                  data;
                $(api.column(0).footer()).html("");
                $(api.column(2).footer()).html("");
                $(api.column(4).footer()).html("");
                $(api.column(5).footer()).html("");
                $("tr:eq(2) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(3) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(4) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(5) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(6) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(7) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(8) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(9) td:eq(3)", api.table().footer()).html("");
                //Text CLEAR
                $("tr:eq(1) td:eq(2)", api.table().footer()).html("");
                $("tr:eq(2) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(3) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(4) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(5) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(6) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(7) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(8) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(9) td:eq(1)", api.table().footer()).html("");
              }
            },
          });
        }
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
// payment_datewise_schemedata
function generate_paydatewise_schcoll(selected_date = "", id_branch = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/reports/paydatewise_schcoll_list",
    data: { date: selected_date, id_branch: id_branch },
    dataType: "JSON",
    type: "POST",
    success: function (payment) {
      var data = payment.account;
      var gst_number = payment.gst_number;
      var gstsetting =
        typeof payment.account == "undefined"
          ? ""
          : payment.account[0].gst_setting;
      if (gstsetting == 1) {
        var gstno =
          "<span style='font-size:13pt; float:right;'> GST Number - " +
          gst_number +
          "</span>";
      } else {
        var gstno = "";
      }
      var select_date =
        "<b><span style='font-size:15pt;'>Payment Scheme Wise Report</span></b></br>" +
        "<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;Selected Date&nbsp;&nbsp;:&nbsp;" +
        selected_date +
        "</span>" +
        gstno;
      var oTable = $("#payschcoll_data").DataTable();
      oTable.clear().draw();
      if (data != null && data.length > 0) {
        console.log(data);
        oTable = $("#payschcoll_data").dataTable({
          bDestroy: true,
          bInfo: false,
          bFilter: true,
          scrollX: "100%",
          bAutoWidth: false,
          bSort: true,
          dom: "Bfrtip",
          lengthMenu: [
            [10, 25, 50, -1],
            ["10 rows", "25 rows", "50 rows", "Show all"],
          ],
          buttons: [
            {
              extend: "print",
              footer: true,
              title: select_date,
              customize: function (win) {
                $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "inherit");
              },
            },
            {
              extend: "excel",
              footer: true,
              title: select_date,
            },
            {
              extend: "pageLength",
              customize: function (win) {
                $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "inherit");
              },
            },
          ],
          aaData: data,
          aoColumns: [
            //	{ "mDataProp": "scheme_name" },
            {
              mDataProp: function (row, type, val, meta) {
                if (row.has_lucky_draw == 1 && row.is_lucky_draw == 1) {
                  return row.scheme_name + " - " + row.group_code;
                } else {
                  return row.scheme_name;
                }
              },
            },
            { mDataProp: "branch_name" },
            { mDataProp: "opening_bal" },
            { mDataProp: "collection" },
            { mDataProp: "incentive" },
            { mDataProp: "paid" },
            { mDataProp: "cancel_payment" },
            { mDataProp: "charge" },
            { mDataProp: "closing_balance" },
            {
              mDataProp: function (row, type, val, meta) {
                return parseFloat(
                  parseFloat(row.opening_bal) + parseFloat(row.collection)
                ).toFixed(2);
              },
            },
          ],
          footerCallback: function (row, data, start, end, display) {
            if (data.length > 0) {
              var api = this.api(),
                data;
              for (var i = 0; i <= data.length - 1; i++) {
                var intVal = function (i) {
                  return typeof i === "string"
                    ? i.replace(/[\$,]/g, "") * 1
                    : typeof i === "number"
                      ? i
                      : 0;
                };
                // opentotal amt
                opentotal = api
                  .column(2, { page: "current" })
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(2).footer()).html(
                  parseFloat(opentotal).toFixed(2)
                );
                // collection amt
                colltotal = api
                  .column(3, { page: "current" })
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(3).footer()).html(
                  parseFloat(colltotal).toFixed(2)
                );
                // collection gst
                incen = api
                  .column(4, { page: "current" })
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(4).footer()).html(parseFloat(incen));
                // paid Total
                paid = api
                  .column(5, { page: "current" })
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(5).footer()).html(parseFloat(paid));
                // cancel
                cancel = api
                  .column(6, { page: "current" })
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(6).footer()).html(parseFloat(cancel));
                // charge
                charge = api
                  .column(7, { page: "current" })
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(7).footer()).html(parseFloat(charge).toFixed(2));
                // close_bal
                close_bal = api
                  .column(8, { page: "current" })
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(8).footer()).html(
                  parseFloat(close_bal).toFixed(2)
                );
                //  Total
                Total = api
                  .column(9)
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(9).footer()).html(parseFloat(Total).toFixed(2));
              }
            } else {
              var data = 0;
              var api = this.api(),
                data;
              //$( api.column(1).footer() ).html('');
              $(api.column(3).footer()).html("");
              $(api.column(4).footer()).html("");
              $(api.column(5).footer()).html("");
              $(api.column(6).footer()).html("");
              $(api.column(7).footer()).html("");
              $(api.column(8).footer()).html("");
              $(api.column(9).footer()).html("");
            }
          },
        });
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
// payment outstanding 
function generate_payout_cuslist(selected_date = "", id_branch = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/reports/payment_outstanding_list",
    data: { 'date': selected_date, 'id_branch': id_branch },
    dataType: "JSON",
    type: "POST",
    success: function (payment) {
      console.log(payment);
      var data = payment.account;
      var gst_number = payment.gst_number;
      var gstsetting = (typeof payment.account == 'undefined' ? '' : payment.account[0].gst_setting);
      if (gstsetting == 1) {
        var gstno = "<span style='font-size:13pt; float:right;'> GST Number - " + gst_number + "</span>";
      } else {
        var gstno = '';
      }
      var select_date = "<b><span style='font-size:15pt;'>Out Standing Report</span></b></br>" + "<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;As On Date&nbsp;&nbsp;:&nbsp;" + selected_date + "<span>" + gstno;
      var oTable = $('#payout_list').DataTable();
      oTable.clear().draw();
      if (data != null && data.length > 0) {
        console.log(data);
        oTable = $('#payout_list').dataTable({
          "bDestroy": true,
          "bInfo": false,
          "bFilter": true,
          "scrollX": '100%',
          "bAutoWidth": false,
          "bSort": true,
          "dom": 'Bfrtip',
          "lengthMenu": [
            [10, 25, 50, -1],
            ['10 rows', '25 rows', '50 rows', 'Show all']
          ],
          "buttons": [
            {
              extend: 'print',
              footer: true,
              title: select_date,
              customize: function (win) {
                $(win.document.body).find('table')
                  .addClass('compact')
                  .css('font-size', 'inherit');
              },
            },
            { extend: 'excel', footer: true, title: select_date, },
            {
              extend: 'pageLength',
              customize: function (win) {
                $(win.document.body).find('table')
                  .addClass('compact')
                  .css('font-size', 'inherit');
              },
            }
          ],
          "aaData": data,
          "aoColumns": [
            { "mDataProp": "sno" },
            { "mDataProp": "code" },
            //	{ "mDataProp": "scheme_acc_number" },
            {
              "mDataProp": function (row, type, val, meta) {
                if (row.has_lucky_draw == 1 && row.is_lucky_draw == 1) {
                  return row.group_code + ' ' + row.scheme_acc_number;
                }
                else {
                  return row.code + ' ' + row.scheme_acc_number;
                }
              }
            },
            { "mDataProp": "name" },
            { "mDataProp": "total_installments" },
            { "mDataProp": "paid_installments" },
            { "mDataProp": "amount" },
            { "mDataProp": "joined_date" },
            { "mDataProp": "total_paid_amount" },
            { "mDataProp": "total_paid_weight" },
            { "mDataProp": "due_count" },
            { "mDataProp": "mobile" },
            { "mDataProp": "last_paid_date" },
          ],
        });
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
// coded by Jothika 16-10-2025 [disable payment issue]
$("#block_payment").bootstrapSwitch();
$('#block_payment').on('switchChange.bootstrapSwitch', function (event, state) {
  $(this).val(state ? '1' : '0');
  $(this).prop('checked', state);
  let payment_disable_toggle = $(this).val();
  let stop_payment = $('input[name="db_stop_payment"]').val();
  if (payment_disable_toggle != stop_payment) {
    $('#block_button').css('display', 'block');
  } else {
    $('#block_button').css('display', 'none');
  }
  // console.log('stop payment : ', payment_disable_toggle);
  if (payment_disable_toggle == 1) {
    $('#disable_pay_block').css('display', 'block');
    $('#update_reason').text('Block Payment');
  } else {
    $('#disable_pay_block').css('display', 'none');
    $('#update_reason').text('Unblock Payment');
  }
});
$(document).ready(function () {
  let stop_payment = $('input[name="db_stop_payment"]').val();
  if (stop_payment == 1) {
    $('#disable_pay_block').css('display', 'block');
  } else {
    $('#disable_pay_block').css('display', 'none');
  }
})
$("#update_reason").on("click", function (e) {
  var scheme_id = ctrl_page[3];
  var reasontxt = $("#disable_pay_reason").val();
  var blk_unblk = $('#block_payment').val();
  var reason_data = reasontxt.trim();
  // console.log('scheme_id : ',scheme_id);
  // console.log('reason data : ',reason_data);
  // console.log('Block or unblock : ',blk_unblk);
  //alert(scheme_id);
  if (blk_unblk == 1 && reason_data.length == 0) {
    $.toaster({
      priority: "danger",
      title: "Warning!",
      message: "" + "</br>Enter any reason for disabling payment",
    });
  } else {
    block_payment(scheme_id, blk_unblk, reason_data);
    // console.log("update function triggered");
  }
});
function block_payment(scheme_id, blk_unblk, reason_data = "") {
  my_Date = new Date();
  $.ajax({
    type: 'POST',
    data: { 'schemeid': scheme_id, 'stop_payment': blk_unblk, 'reasondata': reason_data },
    url: base_url + 'index.php/admin_manage/blk_payment_byid/?nocache=' + my_Date.getUTCSeconds(),
    dataType: 'json',
    cache: false,
    success: function (data) {
      if (data.message == 'success') {
        $.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>Status updated successfully" });
        window.location.reload();
      } else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Unable to proceed the process" });
      }
    }
  });
}
function get_schemename() {
  $(".overlay").css('display', 'block');
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/get/schemename_list',
    dataType: 'json',
    success: function (data) {
      if (ctrl_page[1] == 'gift_report') {
        $('#scheme_select').append(
          $("<option></option>")
            .attr("value", 0)
            .text('All')
        );
      }
      var scheme_val = $('#id_schemes').val();
      $('#scheme_select').prepend(
        $("<option></option>")
          .attr("value", 0)
          .text('All')
      );
      $.each(data, function (key, item) {
        $('#scheme_select').append(
          $("<option></option>")
            .attr("value", item.id_scheme)
            .text(item.scheme_name)
        );
      });
      $("#scheme_select").select2({
        placeholder: "Select Scheme Name",
        allowClear: true
      });
      $("#scheme_select").on('select2:clear', function () {
        scheme_val = "";
      });
      $("#scheme_select").select2("val", (scheme_val != '' && scheme_val > 0 ? scheme_val : ''));
      $(".overlay").css("display", "none");
    }
  });
}
//end of new reports
//branch on change event
$('#branch_select').select2().on("change", function (e) {
  //console.log(ctrl_page[1]);
  switch (ctrl_page[1]) {
    //monthly chit report starts
    case 'monthly_chit_report':
      get_months();
      get_schemename();
      get_month_report_data();
      break;
    //monthly chit report ends
    //Scheme wise mode wise report starts here
    //Added by Durga 29-06-2023 starts here
    /* case 'payment_modeandgroupwise_data':   // collection wise mode and group wise //HH
      if (this.value != '') {
        var from_date = $('#rpts_payments1').text();
        var to_date = $('#rpts_payments2').text();
        var id_scheme = $('#scheme_select').val();
        var id_branch = $(this).val();
        generate_paymode_groupwise_list(from_date, to_date, id_scheme, id_branch);
      }
      break; */
    //Added by Durga 29-06-2023 ends here
    //Scheme wise mode wise report ends here
    case 'payment_daterange':
      if (this.value != '') {
        $('#emp_select').empty();
        $('#id_employee').val('');
        $('#id_branch').val(this.value);
        var from_date = $('#rpt_payments1').text();
        var to_date = $('#rpt_payments2').text();
        var id_scheme = $('#scheme_select').val();
        var id_employee = $('#id_employee').val();
        var id_branch = $(this).val();
        generate_payment_daterange(from_date, to_date, '', '', id_scheme, id_branch, id_employee);
        get_employee_name(id_branch);
      }
      break;
    case 'interwalTrans_list':
      if (this.value != '') {
        var id_branch = $(this).val();
        var from_date = $('#rpt_payments1').text();
        var to_date = $('#rpt_payments2').text();
        get_interWalTrans_list(from_date, to_date, id_branch);
      }
      break;
    case 'payment_employee_wise':  //hh
      if (this.value != '') {
        $('#emp_select').empty();
        var id_branch = $(this).val();
        var from_date = $('#rpt_payments1').text();
        var to_date = $('#rpt_payments2').text();
        $('#id_branch').val(this.value);
        var id_employee = $('#id_employee').val();
        get_employee_name(id_branch);
        get_paymentlist(from_date, to_date, id_branch, id_employee);
      }
      break;
    /*  case 'employee_wise_collection':  //hh
       if (this.value != '') {
         var id_branch = $(this).val();
         var from_date = $('#rpt_payments1').text();
         var to_date = $('#rpt_payments2').text();
         var id_emp = $('#emp_select').val();
         get_emp_summary_list(from_date, to_date, id_branch, id_emp);
       }
       break; */
    case 'Employee_account':
      if (this.value != '') {
        var from_date = $('#account_list1').text();
        var to_date = $('#account_list2').text();
        $('#emp_select').empty();
        var id_branch = $(this).val();
        $('#id_branch').val(this.value);
        var id_employee = $('#id_employee').val();
        get_employee_name(id_branch);
        get_employee_acc_list(from_date, to_date, id_branch, id_employee);
      }
      break;
    //refferal report starts 
    case 'employee_ref_success':
      if (this.value != '') {
        var from_date = $('#rpt_payments1').text();
        var to_date = $('#rpt_payments2').text();
        var id_branch = $(this).val();
        payment_employee_ref_success(from_date, to_date, id_branch);
      }
      break;
    //refferal report ends 
    case 'payment_modewise_data':
      if (this.value != '') {
        var from_date = $('#rpt_payments1').text();
        var to_date = $('#rpt_payments2').text();
        var id_scheme = $('#scheme_select').val();
        var id_branch = $(this).val();
        generate_paymodewise_list(from_date, to_date, '', '', id_scheme, id_branch);
      }
      break;
    case 'accounts_schemewise':
      if (this.value != '') {
        var id_branch = $(this).val();
        scheme_wise_account(id_branch);
      }
      break;
    //unpaid report starts here 
    case 'payment_details':
      if (this.value != '') {
        var id_branch = $(this).val();
        var from_date = $('#rpt_customer_unpaid1').text();
        var to_date = $('#rpt_customer_unpaid2').text();
        //customer_wise_payment(id_branch);
      }
      break;
    //unpaid report ends here 
    case 'payment_schemewise':
      if (this.value != '') {
        var id_branch = $(this).val();
        var from_date = $('#rpt_scheme_payment1').text();
        var to_date = $('#rpt_scheme_payment2').text();
        payment_schemewise(from_date, to_date, id_branch);
      }
      break;
    case 'payment_datewise_schemedata':
      if (this.value != '') {
        $('#emp_select').empty();
        $('#id_employee').val('');
        $('#id_branch').val(this.value);
        var selected_date = $("#schreport_date").val();
        var id_branch = $(this).val();
        var id_employee = $('#id_employee').val();
        get_employee_name(id_branch);
        var added_by = $('#added_by').val();
        generate_paymodewise_schemelist(selected_date, id_branch, id_employee, added_by);
      }
      break;
    case 'payment_online_offline_collec_data':
      if (this.value != '') {
        var selected_date = $("#modereport_date").val();
        var added_by = $('#added_by').val();
        generate_online_offline_collection(selected_date, added_by);
      }
      break;
    case 'paydatewise_schcoll_data':
      if (this.value != '') {
        var selected_date = $("#schwisereport_date").val();
        var id_branch = $(this).val();
        generate_paydatewise_schcoll(selected_date, id_branch);
      }
      break;
    case 'customer_account_details':
      var from_date = $('#rpt_payments1').text();
      var to_date = $('#rpt_payments2').text();
      get_customer_account_details(from_date, to_date);
      break;
    case 'payment_cancel_report':
      if (this.value != '') {
        var id_branch = $(this).val();
        var from_date = $('#cancel_payment_list1').text();
        var to_date = $('#cancel_payment_list2').text();
        get_cancel_pay_list(from_date, to_date, id_branch);
      }
      break;
    case 'payment':
      if (this.value != '') {
        var id_branch = $(this).val();
        var from_date = $('#rpt_payment_date1').text();
        var to_date = $('#rpt_payment_date2').text();
        generate_payment_list(from_date, to_date);
      }
      break;
    case 'payment_outstanding':
      if (this.value != '') {
        var id_branch = $(this).val();
        var select_date = $('#payoutcus').val();
        generate_payout_cuslist(select_date, id_branch);
      }
      break;
    // case 'gift_report':
    // 	if(this.value!='')
    //     {
    // 	    /*var id_branch=$(this).val();
    //         var from_date=$('#rpt_payment_date1').text();
    //         var to_date=$('#rpt_payment_date2').text();
    //         getGiftIssuedList(from_date,to_date,id_branch);*/
    //         get_gift_names();
    //     }
    // break;
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
    success: function (data) {
      console.log(data);
      $("#spinner").css('display', 'none');
      $.each(data.employee, function (key, item) {
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
      if ($("#emp_select").length) {
        $("#emp_select").select2("val", ($('#id_employee').val() != null ? $('#id_employee').val() : ''));
      }
      $(".overlay").css("display", "none");
    }
  });
}
$('#emp_select').select2().on("change", function (e) {
  if (this.value != '') {
    var id_emp = this.value;
    var from_date = $('#rpt_payments1').text();
    var to_date = $('#rpt_payments2').text();
    var id_branch = $('#id_branch').val();
    $('#id_employee').val(this.value);
    if (ctrl_page[1] == 'payment_employee_wise') {
      get_paymentlist(from_date, to_date, id_branch, id_emp);  //hh
    }
    else if (ctrl_page[1] == 'payment_daterange') {
      var from_date = $('#rpt_payments1').text();
      var to_date = $('#rpt_payments2').text();
      var id_scheme = $('#scheme_select').val();
      var id_branch = $('#id_branch').val();
      var id_employee = $('#id_employee').val();
      generate_payment_daterange(from_date, to_date, '', '', id_scheme, id_branch, id_employee);
    }
    else if (ctrl_page[1] == 'Employee_account') {
      var from_date = $('#account_list1').text();
      var to_date = $('#account_list2').text();
      var id_scheme = $('#scheme_select').val();
      var id_branch = $('#id_branch').val();
      var id_employee = $('#id_employee').val();
      $('#id_employee').val(this.value);
      get_employee_acc_list(from_date, to_date, id_branch, id_employee);
    }
    else if (ctrl_page[1] == 'payment_datewise_schemedata') {
      var selected_date = $("#schreport_date").val();
      var id_branch = $('#id_branch').val();
      var id_employee = $('#id_employee').val();
      var added_by = $('#added_by').val();
      generate_paymodewise_schemelist(selected_date, id_branch, id_employee, added_by);
    }
    else if (ctrl_page[1] == 'payment_online_offline_collec_data') {
      var selected_date = $("#modereport_date").val();
      var added_by = $('#added_by').val();
      generate_online_offline_collection(selected_date, added_by);
    }
    /* else if (ctrl_page[1] == 'employee_wise_collection') {
      var id_emp = $(this).val();
      var from_date = $('#rpt_payments1').text();
      var to_date = $('#rpt_payments2').text();
      var id_branch = $('#branch_select').val();
      get_emp_summary_list(from_date, to_date, id_branch, id_emp);
    } */
  }
});
function get_paymentlist(from_date, to_date, id_branch, id_emp) {
  my_Date = new Date();
  var date_type = $("#date_Select").find(":selected").val();
  // 	var id_emp=$('#emp_select').find(":selected").val();
  // 	var id_branch=$('#branch_select').find(":selected").val();
  $("div.overlay").css("display", "block");
  $.ajax({
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          id_branch: id_branch,
          id_emp: id_emp,
          date_type: date_type,
        }
        : "", //hh
    url: base_url + "index.php/reports/payment_employee_collection",
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      get_payment(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
function get_payment(data) {
  var payment = data;
  from = $("#rpt_payments1").text();
  to = $("#rpt_payments2").text();
  var cmp_name = $("#company_name").val();
  var filename =
    "<b><span style='font-size:15pt;'> " +
    cmp_name +
    " | Admin</span></b></br>" +
    "<span style=font-size:13pt;>&nbsp;Selected Date&nbsp;:&nbsp;" +
    from +
    "</span><span style=font-size:13pt;>&nbsp;-&nbsp;" +
    to +
    "</span>";
  var oTable = $("#emp_list").DataTable();
  oTable.clear().draw();
  if (payment != null) {
    oTable = $("#emp_list").dataTable({
      bDestroy: true,
      bInfo: true,
      bFilter: true,
      bSort: true,
      dom: "lBfrtip",
      lengthMenu: [
        [10, 25, 50, -1],
        ["10 rows", "25 rows", "50 rows", "Show all"],
      ],
      buttons: [
        {
          extend: "print",
          footer: true,
          title: filename,
          orientation: "landscape",
          customize: function (win) {
            $(win.document.body).find("table").addClass("compact");
            $(win.document.body)
              .find("table")
              .addClass("compact")
              .css("font-size", "10px")
              .css("font-family", "sans-serif");
          },
        },
        {
          extend: "excel",
          footer: true,
          title: filename,
        },
      ],
      tableTools: {
        buttons: [
          { sExtends: "xls", oSelectorOpts: { page: "current" } },
          { sExtends: "pdf", oSelectorOpts: { page: "current" } },
        ],
      },
      aaData: payment.payments,
      aoColumns: [
        { mDataProp: "id_employee" },
        { mDataProp: "date_payment" },
        { mDataProp: "employee_name" },
        { mDataProp: "name" },
        { mDataProp: "mobile" },
        { mDataProp: "payment_amount" },
      ],
      footerCallback: function (row, data, start, end, display) {
        var api = this.api(),
          data;
        var length = data.length;
        // Remove the formatting to get integer data for summation   /// for total amt footer
        var intVal = function (i) {
          return typeof i === "string"
            ? i.replace(/[\$,]/g, "") * 1
            : typeof i === "number"
              ? i
              : 0;
        };
        // Total over all pages
        total = api
          .column(5)
          .data()
          .reduce(function (a, b) {
            return intVal(a) + intVal(b);
          }, 0);
        // Total over this page
        pageTotal = api
          .column(5, { page: "current" })
          .data()
          .reduce(function (a, b) {
            return intVal(a) + intVal(b);
          }, 0);
        // Update footer
        $(api.column(0).footer()).html(length);
        $(api.column(5).footer()).html(parseFloat(pageTotal).toFixed(2));
        // coded by Jothika on 25-11-2025 [total calc on employee wise payment report]
        var pageInfo = api.page.info(); // gives pagination info
        var footer = $(api.table().footer());
        // remove previously added grand total row to avoid duplication
        footer.find(".grand-total-row").remove();
        if (pageInfo.page === pageInfo.pages - 1) {
          footer.append(
            '<tr class="grand-total-row" style="background-color:#b5b5b5ff;font-weight:bold;">' +
            '<td colspan="5" class="text-right">Grand Total</td>' +
            '<td>' + total.toFixed(2) + '</td>' +
            '</tr>'
          );
        }
      },
    });
  }
}
function get_enquiry_list(from_date = "", to_date = "") {
  var type = $("#feed_filter_type").val();
  var status = $("#feed_filter_status").val();
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/ajax_enquiry_list?nocache=" + my_Date.getUTCSeconds(),
    data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date, 'type': type, 'status': status } : { 'type': type, 'status': status }),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $('#total_enquiry').text(data.enquiry.length);
      set_enquiry_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_enquiry_list(data) {
  $("body").addClass("sidebar-collapse");
  var enquiry = data.enquiry;
  var access = data.access;
  var oTable = $("#enquiry_list").DataTable();
  oTable.clear().draw();
  if (enquiry != null && enquiry.length > 0) {
    oTable = $("#enquiry_list").dataTable({
      bDestroy: true,
      bInfo: true,
      bFilter: true,
      bSort: true,
      dom: 'T<"clear">lfrtip',
      tableTools: {
        aButtons: [
          { sExtends: "xls", oSelectorOpts: { page: "current" } },
          { sExtends: "pdf", oSelectorOpts: { page: "current" } },
        ],
      },
      aaData: enquiry,
      order: [[0, "desc"]],
      aoColumns: [
        { mDataProp: "id_enquiry" },
        {
          mDataProp: function (row, type, val, meta) {
            return row.ticket_no == "" || row.ticket_no == null
              ? "-"
              : row.ticket_no;
          },
        },
        { mDataProp: "name" },
        { mDataProp: "mobile" },
        // 	{ "mDataProp": "email" },
        { mDataProp: "date_add" },
        { mDataProp: "coin_type" },
        { mDataProp: "gram" },
        { mDataProp: "product_name" },
        { mDataProp: "title" },
        { mDataProp: "comments" },
        {
          mDataProp: function (row, type, val, meta) {
            //  0-Open, 1-In Follow up, 2-Closed
            var status =
              row.status == 0
                ? "Open"
                : row.status == 1
                  ? "In Follow up"
                  : row.status == 2
                    ? "Closed"
                    : "-";
            var color =
              row.status == 0
                ? "bg-teal"
                : row.status == 1
                  ? "label-warning"
                  : row.status == 2
                    ? "bg-green"
                    : "";
            return "<span class='label " + color + "'>" + status + "</span>";
          },
        },
        {
          mDataProp: function (row, type, val, meta) {
            return row.last_narration == null ? "-" : row.last_narration;
          },
        },
        {
          mDataProp: function (row, type, val, meta) {
            return row.enq_from == 1
              ? "Web App"
              : row.enq_from == 2
                ? "Mobile App"
                : "";
          },
        },
        {
          mDataProp: function (row, type, val, meta) {
            edit =
              row.status < 2
                ? '<li><a href="#" class="btn-edit" onClick="update_enq_status(' +
                row.id_enquiry +
                ')"><i class="fa fa-edit" ></i> Update Status</a></li>'
                : "";
            action_content =
              '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
              edit +
              '<li><a href="#" class="btn-edit" onClick="enq_detail(' +
              row.id_enquiry +
              ')"><i class="fa fa-eye" ></i> Detail</a></li></ul></div>';
            return action_content;
          },
        },
      ],
    });
  }
  $("div.overlay").css("display", "none");
}
function update_enq_status(id) {
  $('#update_enq_status').modal('show', { backdrop: 'static' });
  $("#id_enquiry").val(id);
}
function enq_detail(id) {
  $('.enq_status_dtl').html(enqStatusData(id));
  $('#enq_status_detail').modal('show', { backdrop: 'static' });
}
function enqStatusData(id) {
  var transaction = "";
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/enquiry/View/" + id,
    dataType: "JSON",
    type: "POST",
    async: false,
    success: function (data) {
      transaction =
        "<table class='table table-bordered trans'><tr ><th>ID</th><th>Date</th><th>Description</th><th>Internal Status</th><th>Employee</th><th>Status</th></tr>";
      $.each(data, function (key, val) {
        var status =
          val.status == 0
            ? "Open"
            : val.status == 1
              ? "In Follow up"
              : val.status == 2
                ? "Closed"
                : "";
        var color =
          val.status == 0
            ? "bg-teal"
            : val.status == 1
              ? "label-warning"
              : val.status == 2
                ? "bg-green"
                : "";
        transaction =
          transaction +
          "<tr><td><span>" +
          val.id_cusenq_status +
          "</span></td><td><span>" +
          val.date_add +
          "</span></td><td><span>" +
          val.enq_description +
          "</span></td><td><span>" +
          val.internal_status +
          "</span></td><td>" +
          val.emp_name +
          "</td><td><span class='label " +
          color +
          "'>" +
          status +
          "</span></td></tr>";
      });
      transaction = transaction + "</table>";
      $("div.overlay").css("display", "none");
      return transaction;
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
  return transaction;
}
function get_interWalTrans_list(from_date = "", to_date = "", id_branch = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  var searchTerm = $("#searchTerm").val();
  var filterBy = $("#filter_by").val();
  var id_branch = $("#branch_select").val();
  var from_date = from_date;
  var to_date = to_date;
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/ajax_interWallet_trans?nocache=" +
      my_Date.getUTCSeconds(),
    data:
      (from_date != "" && to_date != "") || id_branch != ""
        ? filterBy != "" && searchTerm != ""
          ? {
            from_date: from_date,
            to_date: to_date,
            searchTerm: searchTerm,
            filterBy: filterBy,
            id_branch: id_branch,
          }
          : { from_date: from_date, to_date: to_date, id_branch: id_branch }
        : searchTerm != "" && filterBy != ""
          ? {
            from_date: from_date,
            to_date: to_date,
            searchTerm: searchTerm,
            filterBy: filterBy,
            id_branch: id_branch,
          }
          : "",
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      set_interWalTrans_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
function set_interWalTrans_list(data) {
  var customer = data.trans;
  var oTable = $('#interWalList').DataTable();
  $("#total_customers").text(customer.length);
  oTable.clear().draw();
  if (customer != null && customer.length > 0) {
    oTable = $('#interWalList').dataTable({
      "bDestroy": true,
      "bInfo": true,
      "bFilter": true,
      "bSort": true,
      "dom": 'lBfrtip',
      "buttons": ['excel', 'print'],
      "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
      "aaData": customer,
      "order": [[0, "desc"]],
      "aoColumns": [{ "mDataProp": "id_inter_waltransdetail" },
      { "mDataProp": "branch" },
      { "mDataProp": "mobile" },
      { "mDataProp": "name" },
      { "mDataProp": "trans_type" },
      { "mDataProp": "bill_date" },
      { "mDataProp": "trans_date" },
      { "mDataProp": "bill_no" },
      { "mDataProp": "cat_name" },
      { "mDataProp": "bill_amount" },
      { "mDataProp": "credit" },
      { "mDataProp": "debit" }]
    });
  }
}
function scheme_wise_account(id_branch = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/accounts_schemewise_detail?nocache=" + my_Date.getUTCSeconds(),
    dataType: "JSON",
    data: { 'id_branch': id_branch },
    type: "POST",
    success: function (data) {
      set_scheme_wise_account(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_scheme_wise_account(data) {
  var id_branch = $('#branch_select').val();
  var oTable = $('#scheme_wise_account').DataTable();
  oTable.clear().draw();
  oTable = $('#scheme_wise_account').dataTable({
    "bDestroy": true,
    "bInfo": false,
    "bFilter": false,
    "scrollX": '100%',
    "bAutoWidth": false,
    "bSort": true,
    "dom": 'Bfrtip',
    "lengthMenu": [
      [10, 25, 50, -1],
      ['10 rows', '25 rows', '50 rows', 'Show all']
    ],
    "buttons": [
      {
        extend: 'print',
        footer: true,
        customize: function (win) {
          $(win.document.body).find('table')
            .addClass('compact')
            .css('font-size', 'inherit');
        },
      },
      {
        extend: 'excel',
      },
      {
        extend: 'pageLength',
        customize: function (win) {
          $(win.document.body).find('table')
            .addClass('compact')
            .css('font-size', 'inherit');
        },
      }
    ],
    "aaData": data.accounts,
    "order": [[0, "desc"]],
    "aoColumns": [
      { "mDataProp": "id_scheme" },
      // { "mDataProp": "scheme_name" },
      {
        "mDataProp": function (row, type, val, meta) {
          if (row.has_lucky_draw == 1 && row.is_lucky_draw == 1) {
            return row.scheme_name + ' - ' + row.group_code;
          }
          else {
            return row.scheme_name;
          }
        }
      },
      { "mDataProp": "code" },
      { "mDataProp": "accounts" },
      { "mDataProp": "inactive" },
      {
        "mDataProp": function (row, type, val, meta) {
          return parseFloat(parseFloat(row.accounts) + parseFloat(row.inactive));
        }
      },
    ],
  });
}
function payment_schemewise(from_date = "", to_date = "", id_branch = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/payment_schemewise_detail?nocache=" + my_Date.getUTCSeconds(),
    dataType: "JSON",
    data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch } : ''),
    type: "POST",
    success: function (data) {
      set_payment_schemewise(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_payment_schemewise(data) {
  var id_branch = $('#branch_select').val();
  var oTable = $('#payment_schemewise').DataTable();
  oTable.clear().draw();
  oTable = $('#payment_schemewise').dataTable({
    "bDestroy": true,
    "bInfo": false,
    "bFilter": false,
    "scrollX": '100%',
    "bAutoWidth": false,
    "bSort": true,
    "dom": 'Bfrtip',
    "lengthMenu": [
      [10, 25, 50, -1],
      ['10 rows', '25 rows', '50 rows', 'Show all']
    ],
    "buttons": [
      {
        extend: 'print',
        footer: true,
        title: 'Scheme-wise Payment Report ' + $('#rpt_scheme_payment1').text() + ' - ' + $('#rpt_scheme_payment2').text(),
        customize: function (win) {
          $(win.document.body).find('table')
            .addClass('compact')
            .css('font-size', 'inherit');
        },
      },
      {
        extend: 'excel',
        title: 'Scheme-wise Payment Report ' + $('#rpt_scheme_payment1').text() + ' - ' + $('#rpt_scheme_payment2').text(),
      },
      {
        extend: 'pageLength',
        customize: function (win) {
          $(win.document.body).find('table')
            .addClass('compact')
            .css('font-size', 'inherit');
        },
      }
    ],
    "aaData": data.payments,
    "order": [[0, "desc"]],
    "aoColumns": [
      { "mDataProp": "id_scheme" },
      { "mDataProp": "scheme_name" },
      { "mDataProp": "name" },
      { "mDataProp": "code" },
      {
        "mDataProp": function (row, type, val, meta) {
          return parseFloat(parseFloat(row.paid) + parseFloat(row.unpaid));
        }
      },
      { "mDataProp": "paid" },
      { "mDataProp": "unpaid" },
    ],
  });
}
//unpaid report starts here
$('#search_unpaid_list').on('click', function () {
  customer_wise_payment('');
});
//print_unpaid_summary
$('#print_unpaid_summary').on('click', function () {
  const printWindow = window.open('', '_blank');
  var title = '';
  var from_date = ($('#rpt_customer_unpaid1').html());
  var to_date = ($('#rpt_customer_unpaid2').html());
  var branch_name = getBranchTitle();
  title += get_title(from_date, to_date, 'Customer Unpaid Report - Summary - ' + branch_name);
  my_Date = new Date();
  $.ajax({
    url: base_url + "index.php/admin_reports/ajax_customer_payment_details?nocache=" + my_Date.getUTCSeconds(),
    dataType: "JSON",
    data: ({ 'from_date': $('#rpt_customer_unpaid1').html(), 'to_date': $('#rpt_customer_unpaid2').html(), 'id_branch': $('#branch_select').val(), 'id_scheme': $('#scheme_select').val(), 'id_employee': $('#employee_select').val() }),
    type: "POST",
    success: function (data) {
      if (data.accounts.summary != null) {
        trHTML = '';
        trHTML += title + "<br>";
        var i = 0;
        last = Object.keys(data)[Object.keys(data).length - 1];
        //trHTML += '<table  style="margin-left:15px;"><thead><tr><th>Scheme</th><th style="text-align:center;padding:6px" width="25%">Customer Count</th><th style="text-align:right;">Amount</th></tr></thead><tbody>';
        trHTML += '<table  style="margin-left:150px;width:600px;"><thead><tr><th style="text-align:left;">SCHEME</th><th style="text-align:right;padding:6px" width="25%">ACC COUNT</th><th style="text-align:right;">UNPAID INS</th></tr></thead><tbody>';
        var Tcount = 0
        var Tamount = 0
        var grand_ins_total = 0;
        var pending_due_summary = []
        $.each(data.accounts.summary, function (sch, classification) {
          trHTML += '<tr><td style="padding:5px;font-weight:bolder;">' + sch + '</td>'
            + '</tr>';
          var sub_count_total;
          var sub_amount_total;
          var sub_ins_total = 0;
          sub_count_total = 0;
          sub_amount_total = 0;
          $.each(classification, function (key, customer) {
            var ac_count;
            var unpaid_tot = 0;
            for (ac_count = 0; ac_count < customer.length; ac_count++) {
              unpaid_tot += isNaN(customer[ac_count].unpaid_month) ? 0 : parseInt(customer[ac_count].unpaid_month);
            }
            sub_ins_total += unpaid_tot;
            grand_ins_total += unpaid_tot;
            sub_count_total += parseInt(customer.length);
            Tcount = Tcount + customer.length
            payabel_amount = customer[i].payable == 0 ? '-' : customer.length * customer[i].payable
            // payabel_amount = 0
            if (payabel_amount != '-') {
              Tamount = Tamount + payabel_amount
              sub_amount_total += isNaN(payabel_amount) ? 0 : parseFloat(payabel_amount);
              payabel_amounts = formatCurrency.format(payabel_amount)
            } else {
              payabel_amounts = payabel_amount
              sub_amount_total += isNaN(payabel_amount) ? 0 : parseFloat(payabel_amount);
            }
            var customer_pendind_due = {
              'Scheme': key,
              'Customer_count': customer.length,
              'Amount': payabel_amounts,
              'TAmount': Tamount,
              'Tcount': Tcount
            };
            pending_due_summary.push(customer_pendind_due)
            // 	 trHTML += '<td style="padding:5px">'+key+'</td>'
            trHTML += '<td style="padding:5px">' + customer[i].code + '</td>'
              + '<td style="text-align:right;">' + customer.length + '</td>'
              //+ '<td style="padding:5px;text-align:right;">'+payabel_amounts+'</td>'
              + '<td style="text-align:right;">' + unpaid_tot + '</td>'
              + '</tr>';
          });
          //trHTML+='<tr><td style="font-weight:bold;text-align:right;">Sub Total  </td><td style="font-weight:bold;text-align:right;">'+sub_count_total+'</td><td style="font-weight:bold;text-align:right;">'+indianCurrency.format(sub_amount_total)+'</td></tr>'	 ;
          trHTML += '<tr><td  class="highlighted-row" style="font-weight:bold;text-align:left;">Sub Total  </td><td  class="highlighted-row" style="font-weight:bold;text-align:right;">' + sub_count_total + '</td><td  class="highlighted-row" style="font-weight:bold;text-align:right;">' + sub_ins_total + '</td></tr>';
        });
        //trHTML+='</tbody><tfoot style="border-top: 15px solid #ecf0f5;"><tr></tr><tr><th style="font-weight: bold;text-align:right;"> Total </th><td  style="text-align: right;font-weight: bold;">'+Tcount+'</td><td  style="text-align: right;font-weight: bold;">'+indianCurrency.format(Tamount)+'</td></tr></tfoot></table>';
        trHTML += '</tbody><tfoot><tr></tr><tr><th style="font-weight: bold;text-align:left;"> Total </th><td  style="text-align: right;font-weight: bold;">' + Tcount + '</td><td  style="text-align: right;font-weight: bold;">' + grand_ins_total + '</td></tr></tfoot></table>';
      }
      var htmlToPrint = '' +
        '<style type="text/css">' +
        'td.highlighted-row {' +
        'border-top: 1px dashed black;' +
        'border-bottom: 1px dashed black;' +
        '}' +
        '</style>';
      trHTML += htmlToPrint;
      printWindow.document.write(trHTML);
      printWindow.document.close();
      printWindow.print();
      printWindow.close();
    }
  });
});
function customer_wise_payment(id_branch = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/ajax_customer_payment_details?nocache=" + my_Date.getUTCSeconds(),
    dataType: "JSON",
    data: ({ 'from_date': $('#rpt_customer_unpaid1').html(), 'to_date': $('#rpt_customer_unpaid2').html(), 'id_branch': $('#branch_select').val(), 'id_scheme': $('#scheme_select').val() }),
    type: "POST",
    success: function (data) {
      set_customer_payment_details(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_customer_payment_details(data) {
  var from_date = $("#rpt_customer_unpaid1").html();
  var to_date = $("#rpt_customer_unpaid2").html();
  $("#unpaid_daterange").text(from_date + " To " + to_date);
  if (Object.keys(data.accounts.summary).length > 0) {
    $("#print_unpaid_summary").css("display", "block");
    var i = 0;
    trHTML = "";
    last = Object.keys(data)[Object.keys(data).length - 1];
    //trHTML += '<table  style="margin-left:15px;"><thead><tr><th>Scheme</th><th style="text-align:center;padding:6px" width="25%">Customer Count</th><th style="text-align:right;">Amount</th></tr></thead><tbody>';
    trHTML +=
      '<table  style="margin-left:15px;width:600px;"><thead><tr><th>Scheme</th><th style="text-align:center;padding:6px" width="25%">Acc Count</th><th style="text-align:right;">Unpaid Ins</th></tr></thead><tbody>';
    var Tcount = 0;
    var Tamount = 0;
    var grand_ins_total = 0;
    var pending_due_summary = [];
    $.each(data.accounts.summary, function (sch, classification) {
      trHTML +=
        '<tr><td style="padding:5px;font-weight:bolder;">' +
        sch +
        "</td>" +
        "</tr>";
      var sub_count_total;
      var sub_amount_total;
      var sub_ins_total = 0;
      sub_count_total = 0;
      sub_amount_total = 0;
      $.each(classification, function (key, customer) {
        var ac_count;
        var unpaid_tot = 0;
        for (ac_count = 0; ac_count < customer.length; ac_count++) {
          unpaid_tot += isNaN(customer[ac_count].unpaid_month)
            ? 0
            : parseInt(customer[ac_count].unpaid_month);
        }
        sub_ins_total += unpaid_tot;
        grand_ins_total += unpaid_tot;
        sub_count_total += parseInt(customer.length);
        Tcount = Tcount + customer.length;
        payabel_amount =
          customer[i].payable == 0
            ? "-"
            : customer.length * customer[i].payable;
        // payabel_amount = 0
        if (payabel_amount != "-") {
          Tamount = Tamount + payabel_amount;
          sub_amount_total += isNaN(payabel_amount)
            ? 0
            : parseFloat(payabel_amount);
          payabel_amounts = formatCurrency.format(payabel_amount);
        } else {
          payabel_amounts = payabel_amount;
          sub_amount_total += isNaN(payabel_amount)
            ? 0
            : parseFloat(payabel_amount);
        }
        var customer_pendind_due = {
          Scheme: key,
          Customer_count: customer.length,
          Amount: payabel_amounts,
          TAmount: Tamount,
          Tcount: Tcount,
        };
        pending_due_summary.push(customer_pendind_due);
        // 	 trHTML += '<td style="padding:5px">'+key+'</td>'
        trHTML +=
          '<td style="padding:5px">' +
          customer[i].code +
          "</td>" +
          '<td style="text-align:right;">' +
          customer.length +
          "</td>" +
          //+ '<td style="padding:5px;text-align:right;">'+payabel_amounts+'</td>'
          '<td style="text-align:right;">' +
          unpaid_tot +
          "</td>" +
          "</tr>";
      });
      //trHTML+='<tr><td style="font-weight:bold;text-align:right;">Sub Total  </td><td style="font-weight:bold;text-align:right;">'+sub_count_total+'</td><td style="font-weight:bold;text-align:right;">'+formatCurrency.format(sub_amount_total)+'</td></tr>'	 ;
      trHTML +=
        '<tr><td  class="highlighted-row" style="font-weight:bold;text-align:left;">Sub Total  </td><td  class="highlighted-row" style="font-weight:bold;text-align:right;">' +
        sub_count_total +
        '</td><td  class="highlighted-row" style="font-weight:bold;text-align:right;">' +
        sub_ins_total +
        "</td></tr>";
    });
    //trHTML+='</tbody><tfoot style="border-top: 15px solid #ecf0f5;"><tr></tr><tr><th style="font-weight: bold;text-align:right;"> Total </th><td  style="text-align: right;font-weight: bold;">'+Tcount+'</td><td  style="text-align: right;font-weight: bold;">'+formatCurrency.format(Tamount)+'</td></tr></tfoot></table>';
    trHTML +=
      '</tbody><tfoot><tr></tr><tr><th style="font-weight: bold;text-align:left;"> Total </th><td  style="text-align: right;font-weight: bold;">' +
      Tcount +
      '</td><td  style="text-align: right;font-weight: bold;">' +
      grand_ins_total +
      "</td></tr></tfoot></table>";
    var htmlToPrint =
      "" +
      '<style type="text/css">' +
      "td.highlighted-row {" +
      "border-top: 1px dashed black;" +
      "border-bottom: 1px dashed black;" +
      "}" +
      "</style>";
    trHTML += htmlToPrint;
    $("#unpaid_payment").html(trHTML);
  } else {
    $("#print_unpaid_summary").css("display", "none");
    trHTML =
      '<p style="text-align:center;color:red;"><strong>No Data Available</strong></p>';
    $("#unpaid_payment").html(trHTML);
  }
  if (Object.keys(data.accounts.list).length > 0) {
    var account = data.accounts.list;
    $("div.overlay").css("display", "none");
    $("#customer_pay_details > tbody > tr").remove();
    $("#customer_pay_details").dataTable().fnClearTable();
    $("#customer_pay_details").dataTable().fnDestroy();
    var count = 0;
    select_date = "";
    var branch_name = getBranchTitle();
    select_date = get_title(
      from_date,
      to_date,
      "Customer Unpaid Report - " + branch_name
    );
    var trHTML = "";
    var scheme_acc_number;
    var sub_total_paid;
    var grand_total_paid = 0;
    var sublist_ins_total;
    var grandlist_ins_total = 0;
    last = pending_due_summary.length - 1;
    /*select_date+='</br><div><table class="table table-bordered table-striped text-center" style="border: 1px solid black;border-collapse: collapse; width:40%;margin-left:300px;">'+
          '<thead style="font-size:11pt;">'+
          '<tr><th style="text-align: center;" colspan="3"><span >Pending Dues report Summary</span></th>'+
          '</tr>'+
          '</thead>'+
          '<tbody style="font-size:11pt;">';         
          if(pending_due_summary.length ==0 ){
          }else{
            select_date+=' <th>Scheme</th><th>Customer Count</th><th>Amount	</th><tr>';
            $.each(pending_due_summary,function(key,customer){
               //console.log(key)
               //console.log(customer)
              select_date+= '<td>'+customer.Scheme+'</td><td>'+customer.Customer_count
                 +'</td><td>'+(customer.Amount=='-'? '-': customer.Amount)+'</td></tr>';
            });
            //  console.log(pending_due_summary[last].Tcount)
            select_date+= '<tr><th>Total</th><th>'+pending_due_summary[last].Tcount+'</th><th>'+formatCurrency.format(pending_due_summary[last].TAmount)+'</th></tr>';
          }	
          select_date+='</tbody>'+
             '</table></div>';*/
    //	console.log(select_date);
    $.each(account, function (key, ac_data) {
      trHTML +=
        "<tr>" +
        '<td style="text-align:left;" class="report-key"><strong>' +
        key +
        "</strong></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>";
      sub_total_pending = 0;
      sub_total_paid = 0;
      sublist_ins_total = 0;
      var sno = 0;
      $.each(ac_data, function (key, items) {
        var ins_till_date =
          parseInt(items.ins_till_date) > parseInt(items.total_installments)
            ? items.total_installments
            : items.ins_till_date;
        sub_total_paid += isNaN(items.paid_amount)
          ? 0
          : parseFloat(items.paid_amount);
        sublist_ins_total += isNaN(items.unpaid_month)
          ? 0
          : parseInt(items.unpaid_month);
        grandlist_ins_total += isNaN(items.unpaid_month)
          ? 0
          : parseInt(items.unpaid_month);
        //scheme_acc_number=getSchAccNumber_Format(items.is_lucky_draw,items.scheme_group_code,items.scheme_acc_number,items.schemeaccNo_displayFrmt,items.scheme_wise_acc_no,items.acc_branch,items.start_year,items.code);
        //console.log(items);
        count++;
        sno++;
        // For digi gold Scheme
        // console.log(items.is_digi);
        //   if(items.is_digi == 1){
        // 		installmentCount = '<td>' + items.paid_ins + '/' + items.total_installments + '</td>'
        // 	}else{
        // 		installmentCount = '<td>' + items.paid_ins + '/' + ins_till_date + '</td>' 
        // 	}   
        trHTML +=
          "<tr>" +
          "<td>" +
          sno +
          "</td>" +
          "<td>" +
          items.id_scheme_account +
          "</td>" +
          "<td>" +
          items.id_customer +
          "</td>" +
          "<td>" +
          items.customer_name +
          "</td>" +
          "<td>" +
          (items.group_code != "" ? items.group_code : "-") +
          "</td>" +
          "<td>" +
          items.code +
          "</td>" +
          "<td>" +
          items.scheme_acc_number +
          "</td>" +
          "<td>" +
          items.account_name +
          "</td>" +
          "<td>" +
          items.mobile +
          "</td>" +
          "<td>" +
          items.start_date +
          "</td>" +
          "<td>" +
          items.last_paid_date +
          "</td>" +
          "<td>" +
          (items.paid_amount != 0
            ? formatCurrency.format(items.paid_amount)
            : "") +
          "</td>" +
          "<td>" +
          items.total_installments +
          "</td>" +
          "<td>" +
          items.paid_ins +
          "/" +
          ins_till_date +
          "</td>" +
          "<td>" +
          items.unpaid_month +
          "</td>" +
          "<td>" +
          items.referred_employee +
          "</td>" +
          "<td>" +
          (items.employee_created != "" && items.employee_created != null
            ? items.employee_created
            : "-") +
          "</td>" +
          "</tr>";
      });
      grand_total_paid += parseFloat(sub_total_paid);
      //subtotal row
      trHTML +=
        "<tr>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        '<td class="report-sub-total">Sub Total :</td>' +
        '<td style="text-align:right;" class="report-sub-total">' +
        (sub_total_paid != 0 ? formatCurrency.format(sub_total_paid) : "") +
        "</td>" +
        "<td></td>" +
        "<td></td>" +
        '<td style="text-align:right;" class="report-sub-total">' +
        sublist_ins_total +
        "</td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>";
    });
    //Grandtotal row
    trHTML +=
      "<tr>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      '<td class="report-grand-total">Grand Total :</td>' +
      '<td style="text-align:right;" class="report-grand-total">' +
      (grand_total_paid != 0 ? formatCurrency.format(grand_total_paid) : "") +
      "</td>" +
      "<td></td>" +
      "<td></td>" +
      '<td style="text-align:right;" class="report-grand-total">' +
      grandlist_ins_total +
      "</td>" +
      "<td></td>" +
      "<td></td>" +
      "</tr>";
    $("#customer_pay_details > tbody").html(trHTML);
    $("#total_account").text(count);
    if (!$.fn.DataTable.isDataTable("#customer_pay_details")) {
      if (account != null) {
        oTable = $("#customer_pay_details").dataTable({
          bSort: false,
          bInfo: false,
          bDestroy: true,
          bAutoWidth: false,
          responsive: true,
          scrollX: "100%",
          dom: "lBfrtip",
          pageLength: 25,
          lengthMenu: [
            [-1, 25, 50, 100, 250],
            ["All", 25, 50, 100, 250],
          ],
          buttons: [
            {
              extend: "print",
              footer: true,
              title: "",
              messageTop: select_date,
              orientation: "landscape",
              customize: function (win) {
                /* $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "10px")
                  .css("font-family", "sans-serif"); */
                $(win.document.body).find('table')
                  .addClass('compact');
                $(win.document.body).find('table')
                  .addClass('compact')
                  .css('font-size', '10px')
                  .css('font-family', 'sans-serif');

                // Inject refined CSS for borders and page layout
                var css = 'table.dataTable { ' +
                  'border-collapse: collapse !important; ' +
                  'width: 99% !important; ' +
                  'margin: 0 auto !important; ' +
                  'border: 1px solid black !important; ' +
                  'box-sizing: border-box !important; } ' +
                  'table.dataTable th, table.dataTable td { ' +
                  'border: 1px solid black !important; } ' +
                  '@page { size: landscape; margin: 10mm; }';
                var head = win.document.head || win.document.getElementsByTagName('head')[0];
                var style = win.document.createElement('style');
                style.type = 'text/css';
                style.media = 'print';
                if (style.styleSheet) {
                  style.styleSheet.cssText = css;
                } else {
                  style.appendChild(win.document.createTextNode(css));
                }
                head.appendChild(style);
              },
              exportOptions: {
                columns: ":visible",
              },
            },
            {
              extend: "excel",
              footer: true,
              title:
                "Customer Unpaid Report -" +
                branch_name +
                " " +
                from_date +
                " - " +
                to_date,
            },
            {
              extend: "colvis",
              collectionLayout: "fixed columns",
              collectionTitle: "Column visibility control",
            },
          ],
          columnDefs: [
            {
              targets: [3, 4, 5, 6, 7, 9, 10, 15, 16],
              className: "dt-left",
            },
            {
              targets: [0, 1, 2, 8, 11, 12, 13, 14],
              className: "dt-right",
            },
          ],
        });
      }
    }
  } else {
    var brHTML = "";
    brHTML +=
      '<tr><td colspan=17 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
    $("#customer_pay_details > tbody").html(brHTML);
  }
}
//unpaid report ends here
function get_inter_wallet_woc() {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_dashboard/inter_wallet_accounts__woc_det?nocache=" + my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      set_acc_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_acc_list(data) {
  var oTable = $('#inter_wallet').DataTable();
  oTable.clear().draw();
  oTable = $('#inter_wallet').dataTable({
    "bDestroy": true,
    "responsive": true,
    "bInfo": false,
    "bFilter": true,
    "scrollX": '100%',
    "bAutoWidth": false,
    "bSort": true,
    "pageLength": 25,
    "lengthMenu": [
      [10, 25, 50, -1],
      ['10 rows', '25 rows', '50 rows', 'Show all']
    ],
    "order": [[0, "desc"]],
    "aaData": data.accounts,
    "aoColumns": [
      { "mDataProp": "id_inter_wal_ac" },
      { "mDataProp": "mobile" },
      { "mDataProp": "date_add" },
      { "mDataProp": "available_points" },
    ],
  });
}
/* function get_branchname(){	
     $(".overlay").css('display','block');	
     $.ajax({		
       type: 'GET',		
       url: base_url+'index.php/branch/branchname_list',		
       dataType:'json',		
       success:function(data){	
         var id_branch=$('#id_branch').val();
           $('#branch_select').append(						
           $("<option></option>")						
           .attr("value", 0)						  						  
           .text('All' )
           );			  	   
         $.each(data.branch, function (key, item) {					  				  			   		
           $('#branch_select').append(						
           $("<option></option>")						
           .attr("value", item.id_branch)						  						  
           .text(item.name )						  					
           );			   											
         });						
         $("#branch_select").select2({			    
           placeholder: "Select branch name",			    
           allowClear: true		    
         });	
         $("#branch_select").on('select2:clear', function() {
              id_branch = ""; 
          });
         $("#branch_select").select2("val",(id_branch!=''?id_branch:''));
         $(".overlay").css("display", "none");			
       }	
     }); 
   } */
function get_employee_acc_list(
  from_date = "",
  to_date = "",
  id_branch = "",
  id_employee = ""
) {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url:
      base_url +
      "index.php/reports/ajax_emp_account_list?nocache=" +
      my_Date.getUTCSeconds(),
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          id_branch: id_branch,
          id_employee: id_employee,
        }
        : "",
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      set_employee_acc_list(data);
      $("body").addClass("sidebar-collapse");
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
$(document).on('click', '.select_ids', function (e) {
  $("#emp_acc_list tbody tr").each(function (index, value) {
    if (!$(value).find(".select_ids").is(":checked")) {
      $(value).find(".schemeaccount").empty();
      $(value).find(".schemeaccount").attr('disabled', true);
      $(value).find(".schemeaccount").val('');
    }
    else if ($(value).find(".select_ids").is(":checked")) {
      $(value).find(".schemeaccount").attr('disabled', false);
    }
  });
});
function set_employee_acc_list(data) {
  var account = data;
  var oTable = $("#emp_acc_list").DataTable();
  oTable.clear().draw();
  oTable = $("#emp_acc_list").dataTable({
    bDestroy: true,
    bInfo: true,
    bFilter: true,
    bSort: true,
    order: [[0, "desc"]],
    dom: "lBfrtip",
    buttons: [
      {
        extend: "print",
        footer: true,
        title:
          "Employee Wise Scheme Accounts Report " +
          $("#account_list1").text() +
          " - " +
          $("#account_list2").text(),
        customize: function (win) {
          $(win.document.body)
            .find("table")
            .addClass("compact")
            .css("font-size", "inherit");
        },
      },
      {
        title:
          "Employee Wise Scheme Accounts Report " +
          $("#account_list1").text() +
          " - " +
          $("#account_list2").text(),
        extend: "excel",
      },
    ],
    tableTools: {
      buttons: [
        { sExtends: "xls", oSelectorOpts: { page: "current" } },
        { sExtends: "pdf", oSelectorOpts: { page: "current" } },
      ],
    },
    lengthMenu: [
      [-1, 25, 50, 100, 250],
      ["All", 25, 50, 100, 250],
    ],
    aaData: account,
    order: [[0, "desc"]],
    aoColumns: [
      {
        mDataProp: function (row, type, val, meta) {
          var url =
            base_url +
            "index.php/reports/payment/account/" +
            row.id_scheme_account;
          action =
            '<a href="' +
            url +
            '" target="_blank">' +
            row.id_scheme_account +
            "</a>";
          return action;
        },
      },
      { mDataProp: "id_customer" },
      { mDataProp: "name" },
      { mDataProp: "mobile" },
      { mDataProp: "account_name" },
      {
        mDataProp: function (row, type, val, meta) {
          return row.code;
        },
      },
      {
        mDataProp: function (row, type, val, meta) {
          if (row.has_lucky_draw == 1 && row.is_lucky_draw == 1) {
            return row.group_code + " " + row.scheme_acc_number;
          } else {
            return row.code + " " + row.scheme_acc_number;
          }
        },
      },
      { mDataProp: "is_new" },
      { mDataProp: "start_date" },
      { mDataProp: "scheme_type" },
      {
        mDataProp: function (row, type, val, meta) {
          amount = row.currency_symbol + " " + row.amount;
          weight = "Max " + row.amount + " g/month";
          if (row.scheme_types == "0") {
            return amount;
          } else if (row.scheme_types == "1") {
            return weight;
          } else if (row.scheme_types == "3") {
            return amount;
          } else if (row.scheme_types == "2") {
            return amount;
          } else row.scheme_types == "";
        },
      },
      { mDataProp: "pan_no" },
      { mDataProp: "paid_installments" },
      {
        mDataProp: function (row, type, val, meta) {
          active_url =
            base_url +
            "index.php/account/status/" +
            (row.active == "Active" ? 0 : 1) +
            "/" +
            row.id_scheme_account;
          return (
            "<a href='" +
            active_url +
            "'><i class='fa " +
            (row.active == "Active" ? "fa-check" : "fa-remove") +
            "' style='color:" +
            (row.active == "Active" ? "green" : "red") +
            "'></i></a>"
          );
        },
      },
      { mDataProp: "employee_name" },
      {
        mDataProp: function (row, type, val, meta) {
          return row.added_by == "0"
            ? "Customer"
            : row.added_by == "1"
              ? "Employee"
              : "Customer";
        },
      },
    ],
    footerCallback: function (row, data, start, end, display) {
      var api = this.api(),
        data;
      var length = data.length;
      /* // Remove the formatting to get integer data for summation   /// for total amt footer
         var intVal = function ( i ) {
         return typeof i === 'string' ?
         i.replace(/[\$,]/g, '')*1 :
         typeof i === 'number' ?
         i : 0;
         };
        // Total over all pages
         total = api
         .column( 4 )
         .data()
         .reduce( function (a, b) {
         return intVal(a) + intVal(b);
         }, 0 );
         // Total over this page
         pageTotal = api
         .column( 4, { page: 'current'} )
         .data()
         .reduce( function (a, b) {
         return intVal(a) + intVal(b);
         }, 0 );
        */
      // Update footer
      $(api.column(0).footer()).html(length);
      //$( api.column(4).footer() ).html(parseFloat(pageTotal).toFixed(2));
    },
  });
}
$('#search_empCol_list').on('click', function () {
  var id_branch = $('#id_branch').val();
  var from_date = $('#rpt_payments1').text();
  var to_date = $('#rpt_payments2').text();
  var id_emp = $('#emp_select').val();
  get_emp_summary_list(from_date, to_date, id_branch, id_emp);
});
//Employee collection summary
function get_emp_summary_list(
  from_date = "",
  to_date = "",
  id_branch = "",
  id_emp = ""
) {
  my_Date = new Date();
  console.log(id_branch);
  console.log(id_emp);
  var date_type = $("#date_Select").find(":selected").val();
  $("div.overlay").css("display", "block");
  $.ajax({
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          id_branch: id_branch,
          id_emp: id_emp,
        }
        : "", //hh
    url:
      base_url +
      "index.php/reports/employee_wise_summary?nocache=" +
      my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      get_employee_summary(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
function get_employee_summary(data) {
  var payment = data;
  var oTable = $("#emp_summary_list").DataTable();
  var groupColumn = 1;
  oTable.clear().draw();
  if (payment != null) {
    oTable = $("#emp_summary_list").dataTable({
      columnDefs: [{ visible: false, targets: groupColumn }],
      drawCallback: function (settings) {
        var api = this.api();
        var rows = api.rows({ page: "all" }).nodes();
        var last = null;
        var subTotal = new Array();
        var groupID = -1;
        var aData = new Array();
        var index = 0;
        api
          .column(groupColumn, { page: "all" })
          .data()
          .each(function (group, i) {
            var data = api.row(api.row($(rows).eq(i)).index()).data(); //Response data
            var payment_amount = data.payment_amount;
            if (typeof aData[group] == "undefined") {
              aData[group] = new Array();
              aData[group].rows = [];
              aData[group].payment_amount = [];
            }
            aData[group].rows.push(i);
            aData[group].payment_amount.push(payment_amount);
          });
        var idx = 0;
        var tot = 0;
        for (var employee in aData) {
          //column name
          var sum = 0;
          idx = Math.max.apply(Math, aData[employee].rows);
          $.each(aData[employee].payment_amount, function (k, v) {
            sum = parseFloat(sum) + parseFloat(v);
            tot += parseFloat(v);
          });
          $(rows)
            .eq(idx)
            .after(
              '<tr class="group" style="    background-color: #ccc;font-weight: bold;"><td class="tot-label" colspan="5">Total</td>' +
              '<td class="total">' +
              sum.toFixed(2) +
              "</td></tr>"
            );
        }
        // coded by Jothika on 16-10-2025 [total calc on employee wise collection report]
        var pageInfo = api.page.info(); // gives pagination info
        $('#emp_summary_list tfoot').remove();
        if (pageInfo.page === pageInfo.pages - 1) {
          // last page
          $("#emp_summary_list").append(
            '<tfoot><tr style="background-color:#b5b5b5ff;font-weight:bold;">' +
            '<td colspan="5" class="text-right">Grand Total</td>' +
            '<td>' + tot.toFixed(2) + '</td></tr></tfoot>'
          );
        }
        // console.log('total : ',tot);
      },
      bDestroy: true,
      bInfo: true,
      bFilter: true,
      bSort: true,
      dom: "lBfrtip",
      lengthMenu: [
        [10, 25, 50, -1],
        ["10 rows", "25 rows", "50 rows", "Show all"],
      ],
      buttons: [
        {
          extend: "print",
          footer: true,
          title:
            "Employee collection summary " +
            $("#rpt_payments1").text() +
            " - " +
            $("#rpt_payments2").text(),
          customize: function (win) {
            $(win.document.body)
              .find("table")
              .addClass("compact")
              .css("font-size", "inherit");
          },
        },
        {
          extend: "excel",
          footer: true,
          title:
            "Employee collection summary " +
            $("#rpt_payments1").text() +
            " - " +
            $("#rpt_payments2").text(),
        },
      ],
      lengthMenu: [
        [10, 25, 50, -1],
        ["10 rows", "25 rows", "50 rows", "Show all"],
      ],
      aaData: payment.payments,
      aoColumns: [
        { mDataProp: "id_employee" },
        { mDataProp: "firstname" },
        { mDataProp: "firstname" },
        { mDataProp: "name" },
        { mDataProp: "code" },
        { mDataProp: "receipt" },
        { mDataProp: "payment_amount" },
      ],
    });
  }
}
$("#print").on('click', function () {
  newWin = window.open("");
  var divToPrint = document.getElementById("emp_summary_list");
  $('#emp_summary_list').css('text-align', 'left');
  newWin.document.write(divToPrint.outerHTML);
  newWin.document.title = 'Summary Collection Report';
  newWin.print();
  newWin.close();
});
//Employee collection summary
$('#pay_mode').on('change', function () {
  $('#added_by').val(this.value);
  var selected_date = $("#schreport_date").val();
  var id_branch = $('#id_branch').val();
  var id_employee = $('#id_employee').val();
  var added_by = $('#added_by').val();
  generate_paymodewise_schemelist(selected_date, id_branch, id_employee, added_by);
});
$('#pay_mode').on('change', function () {
  $('#added_by').val(this.value);
  var selected_date = $("#modereport_date").val();
  var added_by = $('#added_by').val();
  generate_online_offline_collection(selected_date, added_by);
});
//mob no,ref no,clientid,sch A/c no wise filter & change options in inter table Data's // 
// Customer Reg& transaction records  // HH	
/*$("input[name='upd_mob_btn']:radio").change(function(){
     if($("input[name='id_customer_reg[]']:checked").val())
     {
       var selected = [];
       var update=true;
       $("#intertable_list tbody tr").each(function(index, value){
         if($(value).find("input[name='id_customer_reg[]']:checked").is(":checked")){ 
           data = { 'id_customer_reg'   : $(value).find(".id_customer_reg").val(), 
              'mobile'  : $(value).find(".mobile").val(),  'scheme_ac_no'  : $(value).find(".scheme_ac_no").val(), 
              'group_code'  : $(value).find(".group_code").val(), 
           }
           selected.push(data);
           update=true;
           $("input[name='upd_mob_btn']").removeAttr('checked'); 
         }
         else
         {
           update=true;
         }
       }) 
       if(update==true)
       {
         update_cus_datas(selected);
       }	
     }
   });*/
// created by durga 28/12/2022 starts here
$("#update").click(function () {
  if ($("#Table_Select").val() == 1) {
    if ($("input[name='id_customer_reg[]']:checked").val()) {
      var selected = [];
      var update = true;
      $("#intertable_list tbody tr").each(function (index, value) {
        if ($(value).find("input[name='id_customer_reg[]']:checked").is(":checked")) {
          data = {
            'id_customer_reg': $(value).find(".id_customer_reg").val(),
            'mobile': $(value).find(".mobile").val(), 'scheme_ac_no': $(value).find(".scheme_ac_no").val(),
            'group_code': $(value).find(".group_code").val(),
            'is_transferred': $(value).find(".is_transferred").val(),
          }
          console.log(data);
          selected.push(data);
          update = true;
          //$("input[name='upd_mob_btn']").removeAttr('checked'); 
        }
        else {
          update = true;
        }
      })
      if (update == true) {
        update_cus_datas(selected);
      }
    }
    else {
      $.toaster({ priority: 'warning', title: 'warning!', message: '' + "</br>Select Any Customer Reg Id" });
    }
  }
  else if ($("#Table_Select").val() == 2) {
    if ($("input[name='id_transaction[]']:checked").val()) {
      var selected = [];
      var update = true;
      $("#intertable_translist tbody tr").each(function (index, value) {
        if ($(value).find("input[name='id_transaction[]']:checked").is(":checked")) {
          data = {
            'id_transaction': $(value).find(".id_transaction").val(),
            'is_transferred': $(value).find(".is_transferred2").val(),
          }
          selected.push(data);
          update = true;
          //$("input[name='upd_mob_btn']").removeAttr('checked'); 
        }
        else {
          update = true;
        }
      })
      if (update == true) {
        update_cus_datas(selected);
      }
    }
    else {
      $.toaster({ priority: 'warning', title: 'warning!', message: '' + "</br>Select Any Trans Id" });
    }
  }
});
// created by durga 28/12/2022 ends here
function update_cus_datas(postData = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  //if block added by durga 
  if ($("#Table_Select").val() == 1) {
    var url = "index.php/admin_reports/update_cusdatas?nocache=";
  }
  else if ($("#Table_Select").val() == 2) {
    var url = "index.php/admin_reports/update_transdatas?nocache=";
    console.log(url);
  }//upto here
  $.ajax({
    url: base_url + url + my_Date.getUTCSeconds(),//line altered by durga
    data: { postData },
    type: "POST",
    async: false,
    success: function (data) {
      //  location.reload(false);
      $("div.overlay").css("display", "none");
      location.reload(true);
    },
    error: function (error) {
      console.log(error);
      $("div.overlay").css("display", "none");
    }
  });
}
$('#Table_Select').select2().on("change", function (e) {
  if (this.value == 1) {
    $("#table1").css("display", "block");
    $("#table2").css("display", "none");
    $("#table").css("display", "block");
    //$('#mobile').val(''); 
    $("#mob").show();
    $("#mobilenumber").show();
    $("#mob1").show();
    $("#group_code").show();
    var mobile = $('#mobilenumber').text();
    var clientid = $('#clientid').text();
    var ref_no = $('#ref_no').text();
    var group_code = $('#group_code').text();
    //var scheme_ac_no  = $('#scheme_ac_no').text();
    $("#id_cus").val((this).value);
    //get_intertable_cusdata(mobile,clientid,ref_no,group_code,cus="");
  }
  else if (this.value == 2) {
    $("#table1").css("display", "none");
    $("#table2").css("display", "block");
    $("#table").css("display", "block");
    $("#mob").hide();
    $("#mobilenumber").hide();
    $("#mob1").hide();
    $("#group_code").hide();
    var client_id = $('#client_id').text();
    var ref_no = $('#ref_no').text();
    //var group_code  = $('#group_code').text();
    //var scheme_ac_no  = $('#scheme_ac_no').text();
    $("#id_cus").val((this).value);
    //get_intertable_transdata(client_id,ref_no,cus="");
  }
});
$('#mob_submit').on('click', function () {
  var id_cus = $('#id_cus').val();
  if (id_cus == 1) {
    var mobile = $('#mobilenumber').val();
    var clientid = $('#clientid').val();
    var ref_no = $('#ref_no').val();
    var group_code = $('#group_code').val();
    get_intertable_cusdata(mobile, clientid, ref_no, group_code);
  }
  else {
    var clientid = $('#clientid').val();
    var ref_no = $('#ref_no').val();
    // console.log(client_id);
    get_intertable_transdata(clientid, ref_no);
  }
});
function get_intertable_cusdata(mobile, clientid, ref_no, group_code, cus = "") {
  var cus = $('#Table_Select').find(":selected").val();
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  var oTable = $('#intertable_list').DataTable();
  oTable.clear().draw();
  $.ajax({
    type: 'POST',
    url: base_url + 'index.php/reports/intertable_list',
    data: { 'mobile': mobile, 'clientid': clientid, 'ref_no': ref_no, 'group_code': group_code, 'cus': cus },
    dataType: 'json',
    success: function (data) {
      console.log(data);
      oTable = $('#intertable_list').dataTable({
        "bDestroy": true,
        "bFilter": true,
        "bSort": true,
        "aaSorting": [[0, "desc"]],
        "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'all' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'all' } }] },
        "aaData": data,
        "aoColumns": [{
          "mDataProp": function (row, type, val, meta) {
            chekbox = '<input type="checkbox" class="id_customer_reg" name="id_customer_reg[]" value="' + row.id_customer_reg + '"/> '
            if (row.is_transferred == 'N' || row.is_transferred == 'Y') {
              return chekbox + " " + row.id_customer_reg;
            }
            else {
              return row.id_customer_reg;
            }
          }
        },
        { "mDataProp": "clientid" },
        { "mDataProp": "id_branch" },
        { "mDataProp": "record_to" },
        { "mDataProp": "is_modified" },
        { "mDataProp": "reg_date" },
        // { "mDataProp": "ac_name" },
        {
          "mDataProp": function (row, type, val, meta) {
            if (row.ac_name != null && row.ac_name != '') {
              return row.ac_name;
            }
            else {
              return row.account_name;
            }
          }
        },
        { "mDataProp": "firstname" },
        { "mDataProp": "lastname" },
        { "mDataProp": "address1" },
        { "mDataProp": "address2" },
        { "mDataProp": "address3" },
        {
          "mDataProp": function (row, type, val, meta) {
            if (row.is_transferred == 'N' || row.is_transferred == 'Y') {
              return '<input type="number" class="mobile no form-control" name="mobile" value="' + row.mobile + '" type="text" />';
            }
            else {
              return row.mobile;
            }
          }
        },
        { "mDataProp": "new_customer" },
        { "mDataProp": "ref_no" },
        { "mDataProp": "id_scheme_account" },
        { "mDataProp": "sync_scheme_code" },
        {
          "mDataProp": function (row, type, val, meta) {
            if (row.is_transferred == 'N' || row.is_transferred == 'Y') {
              return '<input type="email" class="group_code no form-control" name="group_code" value="' + row.group_code + '" type="text" />';
            }
            else {
              return row.group_code;
            }
          }
        },
        {
          "mDataProp": function (row, type, val, meta) {
            if (row.is_transferred == 'N' || row.is_transferred == 'Y') {
              return '<input type="email" class="scheme_ac_no no form-control" name="scheme_ac_no" value="' + row.scheme_ac_no + '" type="text" />';
            }
            else {
              return row.scheme_ac_no;
            }
          }
        },
        { "mDataProp": "is_closed" },
        { "mDataProp": "closed_by" },
        { "mDataProp": "closing_date" },
        { "mDataProp": "closing_amount" },
        { "mDataProp": "closing_weight" },
        {
          "mDataProp": function (row, type, val, meta) {
            if (usernamedata == 1 || usernamedata == 2 || usernamedata == 3) {
              return '<input class="is_transferred no form-control" name="is_transferred" minlength=1 maxlength=1 oninput="this.value = this.value.toUpperCase()" onkeypress="return /^[YN]$/i.test(event.key)" value="' + row.is_transferred + '" type="text" />';
            }
            else {
              return row.is_transferred;
            }
          }
        },
        { "mDataProp": "transfer_date" },
        { "mDataProp": "date_update" },
        { "mDataProp": "date_add" },
        { "mDataProp": "is_registered_online" }
        ],
      });
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function get_intertable_transdata(client_id, ref_no, cus = "") {
  // var log = {'client_id':client_id,'ref_no':ref_no,'cus':cus};
  // console.log(log);
  var cus = $('#Table_Select').find(":selected").val();
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  var oTable = $('#intertable_translist').DataTable();
  oTable.clear().draw();
  $.ajax({
    type: 'POST',
    url: base_url + 'index.php/reports/intertable_translist',
    data: { 'client_id': client_id, 'ref_no': ref_no, 'cus': cus },
    dataType: 'json',
    success: function (data) {
      console.log(data);
      oTable = $('#intertable_translist').dataTable({
        "bDestroy": true,
        "bFilter": true,
        "bSort": true,
        "aaSorting": [[0, "desc"]],
        "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'all' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'all' } }] },
        "aaData": data,
        "aoColumns": [{
          "mDataProp": function (row, type, val, meta) {
            chekbox = '<input type="checkbox" class="id_transaction" name="id_transaction[]" value="' + row.id_transaction + '"/> '
            if (row.is_transferred == 'N' || row.is_transferred == 'Y') {
              return chekbox + " " + row.id_transaction;
            }
            else {
              return row.id_transaction;
            }
          }
        },
        { "mDataProp": "client_id" },
        { "mDataProp": "record_to" },
        { "mDataProp": "payment_date" },
        { "mDataProp": "amount" },
        { "mDataProp": function (row) { return formatMetalWeight(row.weight); } },
        { "mDataProp": function (row) { return formatMetalWeight(row.saved_benefits_wgt); } },
        { "mDataProp": function (row) { var v = parseFloat(row.saved_benefit_amt); return isNaN(v) ? '' : v.toFixed(2); } },
        { "mDataProp": "rate" },
        { "mDataProp": "payment_mode" },
        { "mDataProp": "ref_no" },
        {
          "mDataProp": function (row, type, val, meta) {
            if (usernamedata == 1 || usernamedata == 2 || usernamedata == 3) {
              return '<input class="is_transferred2 no form-control" name="is_transferred2" minlength=1 maxlength=1 oninput="this.value = this.value.toUpperCase()" onkeypress="return /^[YN]$/i.test(event.key)" value="' + row.is_transferred + '" type="text" />';
            }
            else {
              return row.is_transferred;
            }
          }
        },
        { "mDataProp": "is_modified" },
        { "mDataProp": "transfer_date" },
        { "mDataProp": "new_customer" },
        { "mDataProp": "id_scheme_account" },
        { "mDataProp": "id_branch" },
        { "mDataProp": "payment_status" },
        { "mDataProp": "payment_type" },
        { "mDataProp": "due_type" },
        { "mDataProp": "receipt_no" },
        { "mDataProp": "date_add" },
        { "mDataProp": "date_upd" },
        { "mDataProp": "installment_no" },
        { "mDataProp": "emp_code" }
        ],
      });
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
//mob no,ref no,clientid,sch A/c no wise filter & change options in inter table Data's // 
// Customer Reg& transaction records  // HH	    
// MSG 91 log listing
function get_msg_translist(from_date = "", to_date = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/getCreditHistory?nocache=" + my_Date.getUTCSeconds(),
    data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date } : ''),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $('#total_trans').text(data.length);
      set_msg_translist(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_msg_translist(data) {
  var trans = data;
  var oTable = $("#msg_trans_list").DataTable();
  oTable.clear().draw();
  if (trans != null && trans.length > 0) {
    oTable = $("#msg_trans_list").dataTable({
      bDestroy: true,
      bInfo: true,
      bFilter: true,
      bSort: true,
      dom: 'T<"clear">lfrtip',
      tableTools: {
        aButtons: [
          { sExtends: "xls", oSelectorOpts: { page: "current" } },
          { sExtends: "pdf", oSelectorOpts: { page: "current" } },
        ],
      },
      aaData: trans,
      order: [[0, "desc"]],
      aoColumns: [
        { mDataProp: "trans_date" },
        {
          mDataProp: function (row, type, val, meta) {
            return row.trans_type == "Add" ? "Top up" : row.trans_type;
          },
        },
        { mDataProp: "trans_sms" },
        { mDataProp: "amount" },
        {
          mDataProp: function (row, type, val, meta) {
            return row.route == 1
              ? "Promotional"
              : row.route == 4
                ? "Transactional"
                : "";
          },
        },
        { mDataProp: "From" },
      ],
      footerCallback: function (row, data, start, end, display) {
        if (data.length > 0) {
          var promo_route = 0;
          var trans_route = 0;
          var api = this.api(),
            data;
          var intVal = function (i) {
            return typeof i === "string"
              ? i.replace(/[\$,]/g, "") * 1
              : typeof i === "number"
                ? i
                : 0;
          };
          for (var i = 0; i <= data.length - 1; i++) {
            if (data[i]["route"] == 1) {
              promo_route += parseFloat(data[i]["trans_sms"]);
            }
            if (data[i]["route"] == 4) {
              trans_route += parseFloat(data[i]["trans_sms"]);
            }
            $(api.column(2).footer()).html(
              "P.Route : " + promo_route + "<br/> T.Route : " + trans_route
            );
          }
        } else {
          var data = 0;
          var api = this.api(),
            data;
          $(api.column(2).footer()).html("");
        }
      },
    });
  }
  $("div.overlay").css("display", "none");
}
// MSG 91 delivery report
function get_msgDeliv_report(from_date = "", to_date = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/reports/msg91_delivery/ajax_report?nocache=" + my_Date.getUTCSeconds(),
    data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date } : ''),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $('#total').text(data.length);
      set_msgDeliv_report(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_msgDeliv_report(data) {
  var trans = data;
  var oTable = $('#msg_deliv_report').DataTable();
  oTable.clear().draw();
  console.log(trans);
  if (trans != null && trans.length > 0) {
    oTable = $('#msg_deliv_report').dataTable({
      "bDestroy": true,
      "bInfo": true,
      "bFilter": true,
      "bSort": true,
      "dom": 'T<"clear">lfrtip',
      "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
      "aaData": trans,
      "order": [[0, "desc"]],
      "aoColumns": [{ "mDataProp": "id_msg91_status" },
      { "mDataProp": "request_id" },
      { "mDataProp": "date" },
      { "mDataProp": "receiver" },
      { "mDataProp": "description" },
      ]
    });
  }
  $("div.overlay").css("display", "none");
}
//Kyc Approval Data status filter with date picker//hh
function get_kyc_list(
  from_date = "",
  to_date = "",
  status = "",
  type = "",
  list_type = "",
  mobile = ""
) {
  my_Date = new Date();
  var id_customer = $('#kyc_id_customer').val() || ''; // fallback to hidden field if any
  postData =
    from_date != "" && to_date != ""
      ? {
        from_date: from_date,
        to_date: to_date,
        status: status,
        type: type,
        list_type: list_type,
        mobile: mobile,
        id_customer: id_customer
      }
      : {
        from_date: from_date,
        to_date: to_date,
        status: $("#filtered_status").val(),
        type: type,
        list_type: list_type,
        mobile: mobile,
        id_customer: id_customer
      };
  $("div.overlay").css("display", "block");
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/kycapproval_data?nocache=" +
      my_Date.getUTCSeconds(),
    data: postData,
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $("#total_kyc").text(data.length);
      set_kyc_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
function set_kyc_list(data) {
  var kyc = data;
  var oTable = $("#kyc_list").DataTable();
  oTable.clear().draw();
  //console.log(kyc);
  oTable = $("#kyc_list").dataTable({
    bDestroy: true,
    bInfo: true,
    bFilter: true,
    bSort: true,
    dom: 'T<"clear">lfrtip',
    tableTools: {
      aButtons: [
        { sExtends: "xls", oSelectorOpts: { page: "current" } },
        { sExtends: "pdf", oSelectorOpts: { page: "current" } },
      ],
    },
    aaData: kyc,
    order: [[0, "desc"]],
    aoColumns: [
      {
        mDataProp: function (row, type, val, meta) {
          chekbox =
            '<input type="checkbox" class="kyc_id" name="kyc_id[]" value="' +
            row.id_kyc +
            '"/> <input type="hidden" class="cus" value="' +
            row.cus +
            '"/>';
          return chekbox + " " + row.id_kyc;
          /*	if(row.status=='0' || row.status=='1')
        {
          return chekbox+" "+row.id_kyc;
        }
          else{
              return row.id_kyc;
          }*/
        },
      },
      { mDataProp: "id_customer" },
      { mDataProp: "mobile" },
      { mDataProp: "kyc_type" },
      { mDataProp: "number" },
      { mDataProp: "name" },
      { mDataProp: "bank_ifsc" },
      { mDataProp: "bank_branch" },
      { mDataProp: "status" },
      { mDataProp: "dob" },
      { mDataProp: "emp_verified_by" },
      { mDataProp: "verification_type" },
      //	{ "mDataProp": "added_by" },
      {
        mDataProp: function (row, type, val, meta) {
          if (row.added_by == 0) {
            return "Web App";
          } else if (row.added_by == 1) {
            return "Admin";
          } else if (row.added_by == 2) {
            return "Mobile App";
          } else if (row.added_by == 3) {
            return "Collection App";
          } else if (row.added_by == 4) {
            return "Retail App";
          } else if (row.added_by == 5) {
            return "Sync";
          } else if (row.added_by == 6) {
            return "Import";
          } else {
            return "-";
          }
        },
      },
      {
        mDataProp: function (row, type, val, meta) {
          id = row.id_kyc;
          //action_content='<a href="#" class="btn-del"><img src='+id+' width="50px;" height="40px;"></a>';
          action_content =
            '<a href="#" class="btn-edit" onClick="view_kyc_detail(' +
            id +
            ')"><i class="fa fa-eye" ></i> Detail</a>';
          return action_content;
        },
      },
      { mDataProp: "last_update" },
      { mDataProp: "date_add" },
      {
        mDataProp: function (row, type, val, meta) {
          var images = '';
          if (row.img_url && row.img_url != '') images += '<a href="' + row.img_url + '" target="_blank"><img src="' + row.img_url + '" width="50" height="50" style="margin:2px;border:1px solid #ccc;" onerror="this.src=\'https://placehold.co/50x50?text=Error\'"></a>';
          if (row.back_img_url && row.back_img_url != '') images += '<a href="' + row.back_img_url + '" target="_blank"><img src="' + row.back_img_url + '" width="50" height="50" style="margin:2px;border:1px solid #ccc;" onerror="this.src=\'https://placehold.co/50x50?text=Error\'"></a>';
          if (row.document_url && row.document_url != '') {
            var isPdf = row.document_url.toLowerCase().indexOf('.pdf') !== -1;
            if (isPdf) {
              images += '<a href="' + row.document_url + '" target="_blank"><i class="fa fa-file-pdf-o" style="font-size:30px;vertical-align:middle;margin:2px;color:red;"></i></a>';
            } else {
              images += '<a href="' + row.document_url + '" target="_blank"><img src="' + row.document_url + '" width="50" height="50" style="margin:2px;border:1px solid #ccc;" onerror="this.src=\'https://placehold.co/50x50?text=Error\'"></a>';
            }
          }
          return images || '-';
        }
      }
    ],
  });
  $("div.overlay").css("display", "none");
}
function update_kyc_status(kyc_data = "", kyc_type = "") {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/update_kyc?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
    data: { kyc_data, kyc_type },
    type: "POST",
    async: false,
    success: function (data) {
      location.reload(false);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      console.log(error);
      $("div.overlay").css("display", "none");
    }
  });
}
function view_kyc_detail(id) {
  $('.kyc-det').html(getkycdata_byid(id));
  $('#kyc_detail').modal('show', { backdrop: 'static' });
}
function getkycdata_byid(id) {
  var kyc_data = "";
  $.ajax({
    url: base_url + "index.php/admin_reports/getkycdata_byid",
    dataType: "JSON",
    data: { "id_kyc": id },
    type: "POST",
    async: false,
    success: function (data) {
      kyc_data += "<h3 style='text-align:center';>";
      if (data.kyc_type == 3) {
        kyc_data += "Aadhar Details</h3>";
      }
      else if (data.kyc_type == 2) {
        kyc_data += "PAN Details</h3>";
      }
      else if (data.kyc_type == 1) {
        kyc_data += "Bank Details</h3>";
      }
      else {
        kyc_data += "KYC Details</h3>";
      }
      kyc_data += "<br/><div class='row'>" +
        "<div class='col-md-5' style='margin-left:30px';>" +
        "<p><strong>Number</strong></p></div>" +
        "<div class='col-md-6'><p>" + data.number + "</p>" +
        "</div>" +
        "</div>";
      if (data.name != null && data.name != '') {
        kyc_data += "<br/><div class='row'>" +
          "<div class='col-md-5' style='margin-left:30px';>" +
          "<p><strong>Name</strong></p></div>" +
          "<div class='col-md-6'><p>" + data.name + "</p>" +
          "</div>" +
          "</div>";
      }
      if (data.kyc_type == 1) {
        kyc_data += "<br/><div class='row'>" +
          "<div class='col-md-5' style='margin-left:30px';>" +
          "<p><strong>Bank Branch </strong></p></div>" +
          "<div class='col-md-6'><p>" + data.bank_branch + "</p>" +
          "</div>" +
          "</div>";
        kyc_data += "<br/><div class='row'>" +
          "<div class='col-md-5' style='margin-left:30px';>" +
          "<p><strong>Bank IFSC </strong></p></div>" +
          "<div class='col-md-6'><p>" + data.bank_ifsc + "</p>" +
          "</div>" +
          "</div>";
      }
      if (data.img_url != null && data.img_url != '') {
        kyc_data += "<br/><div class='row'>" +
          "<div class='col-md-5' style='margin-left:30px';>" +
          "<p><strong>Card Front</strong></p></div>" +
          "<div class='col-md-6'><img class='thumbnail' src='" + data.img_url + "'" + "style='width: 250px;height: 150px;'/>" +
          "</div>" +
          "</div>";
      }
      if (data.back_img_url != null && data.back_img_url != '') {
        kyc_data += "<br/><div class='row'>" +
          "<div class='col-md-5' style='margin-left:30px';>" +
          "<p><strong>Card Back</strong></p></div>" +
          "<div class='col-md-6'><img class='thumbnail' src='" + data.back_img_url + "'" + "style='width: 250px;height: 150px;'/>" +
          "</div>" +
          "</div>";
      }
      //console.log(data);
    }
  });
  return kyc_data;
}
//Kyc Approval Data status filter with date picker//hh
function get_sch_enq_list(from_date = "", to_date = "") {
  my_Date = new Date();
  postData = (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date } : ''),
    $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/schenquiry_list?nocache=" + my_Date.getUTCSeconds(),
    data: (postData),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $('#total_sch_enq').text(data.length);
      set_sch_enq_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_sch_enq_list(data) {
  var oTable = $('#sch_enquiry_list').DataTable();
  oTable.clear().draw();
  //console.log(data);
  oTable = $('#sch_enquiry_list').dataTable({
    "bDestroy": true,
    "bInfo": true,
    "bFilter": true,
    "bSort": true,
    "dom": 'T<"clear">lfrtip',
    "tableTools": { "aButtons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
    "aaData": data,
    "order": [[0, "desc"]],
    "aoColumns": [{
      "mDataProp": function (row, type, val, meta) {
        chekbox = '<input type="checkbox" class="id_sch_enquiry" name="id_sch_enquiry[]" value="' + row.id_sch_enquiry + '"/> '
        return chekbox + " " + row.id_sch_enquiry;
      }
    },
    {
      "mDataProp": function (row, type, val, meta) {
        var title = row.title != null ? row.title + ". " : '';
        return title + "" + row.id_customer;
      }
    },
    { "mDataProp": "mobile" },
    { "mDataProp": "intresred_amt" },
    { "mDataProp": "message" },
    { "mDataProp": "intrested_wgt" },
    { "mDataProp": "enquiry_date" }
    ]
  });
  $("div.overlay").css("display", "none");
}
//Plan 2 and Plan 3 Scheme Enquiry Data with date picker//HH
//Purchase Payment - Akshaya Thiruthiyai Spl updt//
function get_purchase_payment(from_date = "", to_date = "", id_customer = "") {
  my_Date = new Date();
  $.ajax({
    url: base_url + 'index.php/admin_reports/ajax_get_purchase_payment/?nocache=' + my_Date.getUTCSeconds(),
    dataType: "json",
    method: "POST",
    data: { 'from_date': from_date, 'to_date': to_date, 'id_purch_customer': id_customer },
    success: function (data) {
      set_purchase_payment(data);
    }
  });
}
function set_purchase_payment(data) {
  var oTable = $("#purchase_history").DataTable();
  oTable.clear().draw();
  oTable = $("#purchase_history").dataTable({
    bDestroy: true,
    bInfo: true,
    bFilter: true,
    bSort: true,
    dom: "lBfrtip",
    buttons: ["excel", "print"],
    aaData: data,
    order: [[0, "desc"]],
    pageLength: 25,
    aoColumns: [
      { mDataProp: "id_purch_payment" },
      { mDataProp: "name" },
      { mDataProp: "mobile" },
      { mDataProp: "type" },
      { mDataProp: "delivery_preference" },
      //{ "mDataProp": "id_branch" },
      { mDataProp: "payment_amount" },
      { mDataProp: "metal_weight" },
      { mDataProp: "id_transaction" },
      { mDataProp: "payment_status" },
      //{ "mDataProp": "is_delivered" },
      { mDataProp: "date_add" },
      {
        mDataProp: function (row, type, val, meta) {
          if (row.is_delivered == 1) {
            //  action_content = ((row.status < 2)?'<li><a href="#" class="btn-edit" onClick="update_status('+row.id_purch_payment+')"><i class="fa fa-edit" ></i> Deliver</a></li>':'');
            return " Delivered ";
          }
          if (
            row.payment_status == "Success" ||
            row.payment_status == "Awaiting"
          ) {
            //  action_content = ((row.status < 2)?'<li><a href="#" class="btn-edit" onClick="update_status('+row.id_purch_payment+')"><i class="fa fa-edit" ></i> Deliver</a></li>':'');
            return (
              '<button type="button" onClick="otp_model(' +
              row.mobile +
              "," +
              row.id_purch_payment +
              "," +
              row.id_purch_customer +
              ')">Deliver</button>'
            );
          } else {
            /* else if(row.payment_status== 'Success' && row.payment_status== 'Awaiting' && row.is_delivered== 1) {
            return ' Delivered '
             }*/
            return " - ";
          }
        },
      },
    ],
  });
}
//Need otp when purchase the jewel for AT special //HH
function otp_model(id, id_purch_payment, id_purch_customer) {
  $("#otp_model").modal({
    backdrop: 'static',
    keyboard: false
  });
  //$('#otp_model').modal('show', {backdrop: 'static'});
  $("#id_purch_customer").val(id_purch_customer);
  $("#id_purch_payment").val(id_purch_payment);
  $("#mobile").val(id);
}
$('#close').on('click', function () {
  clearTimeout(timer); //clears the previous timer.
  $(".otp_block").css("display", 'none');
  $(".close_actionBtns").css("display", 'none');
  $("#otp_status").css("display", 'none');
  $("#otp").val('');
  $("#closed").val('');
  $("#send_otp").attr("disabled", false);
  var btn = $("#send_otp");
  btn.prop('disabled', false);
  btn.prop('value', 'Send OTP');
});
$('#verify_otp').on('click', function () {
  $("#verify_otp").attr("disabled", true);
  var post_data = $('#otp_model').serialize();
  verify_otp(post_data);
});
$('#verify_otp').on('click', function () {
  $(".close_actionBtns").css("display", 'none');
  $("#closed").prop("required", true);
  $("#closed").css("display", '');
  $("#verify_issue").css("display", '');
  $("#verify_issue").prop('disabled', false);
});
var fewSeconds = 30;
$('#send_otp').click(function (event) {
  var btn = $(this);
  btn.prop('disabled', true);
  timer = setTimeout(function () {
    btn.prop('disabled', false);
    btn.prop('value', 'Resend OTP');
  }, fewSeconds * 1000);
  close_purch_otp();
});
$('#otp').on('keyup', function () {
  if (this.value.length == 6) {
    $('#verify_otp').prop('disabled', false);
  }
  else {
    $('#verify_otp').prop('disabled', true);
    // alert('Please fill the 6 digit Otp');
  }
})
function close_purch_otp(post_data) {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $("#send_otp").attr("disabled", true);
  var mobile = $("#mobile").val();
  var id_purch_payment = $("#id_purch_payment").val();
  var id_purch_customer = $("#id_purch_customer").val();
  $.ajax({
    url: base_url + "index.php/admin_reports/generateotp?nocache=" + my_Date.getUTCSeconds(),
    data: { 'mobile': mobile, 'id_purch_payment': id_purch_payment, 'id_purch_customer': id_purch_customer },
    type: "POST",
    dataType: 'json',
    success: function (data) {
      if (data.result == 3) {
        $('#otp_model').modal({
          backdrop: 'static',
          keyboard: false
        });
        {
          $('#otp_status').fadeIn();
          $("#otp_status").text("OTP Sent Successfully, Kindly verify it by entering in the above Text box.");
          $("#otp_status").css("color", 'green');
          $(".otp_block").css("display", 'block');
          $("div.overlay").css("display", "none");
          $('#otp_status').delay(1000).fadeOut(200);
        }
        $("div.overlay").css("display", "none");
      }
    }
  });
}
function verify_otp(post_data) {
  var post_otp = $('#otp').val();
  var id_purch_payment = $("#id_purch_payment").val();
  $.ajax({
    url: base_url + "index.php/admin_reports/verify_otp",
    data: { 'otp': post_otp, 'id_purch_payment': id_purch_payment },
    type: "POST",
    dataType: "JSON",
    success: function (data) {
      //console.log(data);
      if (data.result == 1) {
        //$("#send_otp").hide();
        $(".close_actionBtns").css("display", 'block');
        $('#otp_status').fadeIn();
        $("#otp_status").text("OTP verified successfully, Kindly proceed with delivery.");
        $("#otp_status").css("color", 'green');
        $("div.overlay").css("display", "none");
        $('#otp_status').delay(1000).fadeOut(200);
      }
      else {
        $("#verify_otp").prop('disabled', false);
        $('#otp_status').fadeIn();
        $("#otp_status").text("Incorrect OTP, Kindly enter the correct one.");
        $("#otp_status").css("color", 'red');
        $("div.overlay").css("display", "none");
        $('#otp_status').delay(10000).fadeOut(500);
      }
    }
  });
}
$('#verify_issue').click(function (event) {
  $(this).prop('disabled', 'disabled');
  location.reload();
  var id_purch_payment = $("#id_purch_payment").val();
  var delivery_remark = $("#closed").val();
  var post_otp = $('#otp').val();
  $.ajax({
    url: base_url + "index.php/admin_reports/purch_delivered",
    data: { 'otp': post_otp, 'delivery_remark': delivery_remark, 'id_purch_payment': id_purch_payment },
    type: "POST",
    dataType: "JSON",
    success: function (data) {
      if (data.result == 5) {
        $("div.overlay").css("display", "none");
      }
    }
  });
});
//Purchase Payment - Akshaya Thiruthiyai Spl updt//
// Payment Online/offline collection // HH
function generate_online_offline_collection(selected_date = "", added_by = "") {
  my_Date = new Date();
  var date_type = $("#date_Select").find(":selected").val();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/reports/payment_online_offline_collec_list",
    data: { date: selected_date, date_type: date_type, added_by: added_by },
    dataType: "JSON",
    type: "POST",
    success: function (payment) {
      var data = payment.account;
      var gst_number = payment.gst_number;
      var gstsetting =
        typeof payment.account == "undefined"
          ? ""
          : payment.account[0].gst_setting;
      if (gstsetting == 1) {
        var gstno =
          "<span style='font-size:13pt; float:right;'> GST Number - " +
          gst_number +
          "</span>";
      } else {
        var gstno = "";
      }
      var title = "";
      title += get_title("All Scheme Report As on Date");
      var select_date =
        "<b><span style='font-size:15pt;'>All Scheme Report As on Date   </span></b></br>" +
        "<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;Selected Date&nbsp;&nbsp;:&nbsp;" +
        selected_date +
        "</span>" +
        gstno;
      var oTable = $("#on_off_paycollec_report").DataTable();
      oTable.clear().draw();
      if (data != null && data.length > 0) {
        if (gstsetting == 1) {
          oTable = $("#on_off_paycollec_report").dataTable({
            bDestroy: true,
            bInfo: false,
            bFilter: false,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: select_date,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              /* ,{
                       extend: 'excelHtml5',
                       footer: true,
                     } */
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "date_payment" },
              { mDataProp: "code" },
              { mDataProp: "receipt" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.sgst).toFixed(3);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.cgst).toFixed(3);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.cgst) + parseFloat(row.sgst)
                  ).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(
                    parseFloat(row.payment_amount) +
                    parseFloat(row.sgst) +
                    parseFloat(row.cgst)
                  ).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              var cshtotal = 0;
              var cardtotal = 0;
              var chqtotal = 0;
              var ecstotal = 0;
              var nbtotal = 0;
              var fptotal = 0;
              if (data.length > 0) {
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  if (data[i]["payment_mode"] == "CSH") {
                    cshtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "Card") {
                    cardtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CHQ") {
                    chqtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "ECS") {
                    ecstotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "NB") {
                    nbtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "FP") {
                    fptotal += parseFloat(data[i]["payment_amount"]);
                  }
                  //console.log(data[i]['payment_mode']);
                  // total
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  //Total over this page
                  $(api.column(0).footer()).html("Total");
                  // recepit Total over this page
                  rec_tot = api
                    .column(2)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(2).footer()).html(rec_tot);
                  // pay_amt tot
                  pay_amt = api
                    .column(3)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(3).footer()).html(
                    parseFloat(pay_amt).toFixed(2)
                  );
                  // sgst_amt tot
                  sgst_amt = api
                    .column(4)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(4).footer()).html(
                    parseFloat(sgst_amt).toFixed(3)
                  );
                  // cgst_amt tot
                  cgst_amt = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(5).footer()).html(
                    parseFloat(cgst_amt).toFixed(3)
                  );
                  // tgst_amt tot
                  $(api.column(6).footer()).html(
                    parseFloat(
                      parseFloat(sgst_amt) + parseFloat(cgst_amt)
                    ).toFixed(2)
                  );
                  total = api
                    .column(7)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(7).footer()).html(parseFloat(total).toFixed(2));
                  $("tr:eq(1) td:eq(2)", api.table().footer()).html(
                    "Description"
                  );
                  //cash
                  $("tr:eq(2) td:eq(1)", api.table().footer()).html("Cash  ");
                  $("tr:eq(2) td:eq(3)", api.table().footer()).html(
                    parseFloat(cshtotal).toFixed(2)
                  );
                  //dc and cc card
                  $("tr:eq(3) td:eq(1)", api.table().footer()).html("Card");
                  $("tr:eq(3) td:eq(3)", api.table().footer()).html(
                    parseFloat(cardtotal).toFixed(2)
                  );
                  //Ecs
                  $("tr:eq(4) td:eq(1)", api.table().footer()).html("Ecs");
                  $("tr:eq(4) td:eq(3)", api.table().footer()).html(
                    parseFloat(ecstotal).toFixed(2)
                  );
                  //net baking
                  $("tr:eq(5) td:eq(1)", api.table().footer()).html(
                    "Net Banking  "
                  );
                  $("tr:eq(5) td:eq(3)", api.table().footer()).html(
                    parseFloat(nbtotal).toFixed(2)
                  );
                  //fb
                  $("tr:eq(6) td:eq(1)", api.table().footer()).html(
                    "Free payment "
                  );
                  $("tr:eq(6) td:eq(3)", api.table().footer()).html(
                    parseFloat(fptotal).toFixed(2)
                  );
                  //Chq
                  $("tr:eq(7) td:eq(1)", api.table().footer()).html("Chq ");
                  $("tr:eq(7) td:eq(3)", api.table().footer()).html(
                    parseFloat(chqtotal).toFixed(2)
                  );
                  //total
                  $("tr:eq(8) td:eq(1)", api.table().footer()).html("Total");
                  $("tr:eq(8) td:eq(3)", api.table().footer()).html(
                    parseFloat(
                      parseFloat(cshtotal) +
                      parseFloat(cardtotal) +
                      parseFloat(ecstotal) +
                      parseFloat(nbtotal) +
                      parseFloat(fptotal) +
                      parseFloat(chqtotal)
                    ).toFixed(2)
                  );
                }
              } else {
                var api = this.api(),
                  data;
                $(api.column(0).footer()).html("");
                $(api.column(2).footer()).html("");
                $(api.column(3).footer()).html("");
                $(api.column(4).footer()).html("");
                $(api.column(5).footer()).html("");
                $(api.column(6).footer()).html("");
                $(api.column(7).footer()).html("");
                $("tr:eq(2) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(3) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(4) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(5) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(6) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(7) td:eq(3)", api.table().footer()).html("");
                $("tr:eq(8) td:eq(3)", api.table().footer()).html("");
                //Text CLEAR
                $("tr:eq(1) td:eq(2)", api.table().footer()).html("");
                $("tr:eq(2) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(3) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(4) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(5) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(6) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(7) td:eq(1)", api.table().footer()).html("");
                $("tr:eq(8) td:eq(1)", api.table().footer()).html("");
              }
            },
          });
        } else {
          oTable = $("#on_off_paycollec_report").dataTable({
            bDestroy: true,
            bInfo: false,
            bFilter: false,
            scrollX: "100%",
            bAutoWidth: false,
            bSort: true,
            dom: "Bfrtip",
            lengthMenu: [
              [10, 25, 50, -1],
              ["10 rows", "25 rows", "50 rows", "Show all"],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: "",
                messageTop: title,
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
              {
                extend: "excel",
              },
              {
                extend: "pageLength",
                customize: function (win) {
                  $(win.document.body)
                    .find("table")
                    .addClass("compact")
                    .css("font-size", "inherit");
                },
              },
            ],
            aaData: data,
            aoColumns: [
              { mDataProp: "payment_mode" },
              { mDataProp: "payment_type" },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
              {
                mDataProp: function (row, type, val, meta) {
                  return parseFloat(row.payment_amount).toFixed(2);
                },
              },
            ],
            footerCallback: function (row, data, start, end, display) {
              var cshtotal = 0;
              var cardtotal = 0;
              var upitotal = 0;
              var chqtotal = 0;
              var ecstotal = 0;
              var nbtotal = 0;
              var fptotal = 0;
              if (data.length > 0) {
                var api = this.api(),
                  data;
                for (var i = 0; i <= data.length - 1; i++) {
                  if (data[i]["payment_mode"] == "CSH") {
                    cshtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "Card") {
                    cardtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "UPI") {
                    upitotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "CHQ") {
                    chqtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "ECS") {
                    ecstotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "NB") {
                    nbtotal += parseFloat(data[i]["payment_amount"]);
                  }
                  if (data[i]["payment_mode"] == "FP") {
                    fptotal += parseFloat(data[i]["payment_amount"]);
                  }
                  //console.log(data[i]['payment_mode']);
                  // total
                  var intVal = function (i) {
                    return typeof i === "string"
                      ? i.replace(/[\$,]/g, "") * 1
                      : typeof i === "number"
                        ? i
                        : 0;
                  };
                  //Total over this page
                  $(api.column(0).footer()).html("Total");
                  // pay_amt tot
                  pay_amt = api
                    .column(2)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(2).footer()).html(
                    parseFloat(pay_amt).toFixed(2)
                  );
                  total = api
                    .column(3)
                    .data()
                    .reduce(function (a, b) {
                      return intVal(a) + intVal(b);
                    }, 0);
                  $(api.column(3).footer()).html(parseFloat(total).toFixed(2));
                }
              } else {
                var api = this.api(),
                  data;
                $(api.column(0).footer()).html("");
                $(api.column(2).footer()).html("");
                $(api.column(3).footer()).html("");
                //$(api.column(4).footer()).html('');
              }
            },
          });
        }
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
// Payment Online/offline collection //
//Autodebit subscription Status Report//HH
function get_autodebit_subscription(from_date = "", to_date = "", id_customer = "") {
  my_Date = new Date();
  $.ajax({
    url: base_url + 'index.php/admin_reports/ajax_get_autodebit_subscription/?nocache=' + my_Date.getUTCSeconds(),
    dataType: "json",
    method: "POST",
    data: { 'from_date': from_date, 'to_date': to_date, 'id_customer': id_customer, 'id_branch': $("#branch_select").val() },
    success: function (data) {
      set_autodebit_subscription(data);
    }
  });
}
function set_autodebit_subscription(data) {
  var oTable = $('#autodebit_subscription').DataTable();
  oTable.clear().draw();
  oTable = $('#autodebit_subscription').dataTable({
    "bDestroy": true,
    "bInfo": true,
    "bFilter": true,
    "bSort": true,
    "dom": 'lBfrtip',
    "buttons": ['excel', 'print'],
    "aaData": data,
    "order": [[0, "desc"]],
    "pageLength": 25,
    "aoColumns": [
      {
        "mDataProp": function (row, type, val, meta) {
          var url = base_url + 'index.php/reports/payment/account/' + row.id_scheme_account;
          action = '<a href="' + url + '" target="_blank">' + row.id_scheme_account + '</a>';
          return action;
        }
      },
      { "mDataProp": "branch_name" },
      { "mDataProp": "name" },
      { "mDataProp": "mobile" },
      { "mDataProp": "account_name" },
      { "mDataProp": "scheme_acc_number" },
      { "mDataProp": "auto_debit_status" },
      { "mDataProp": "date_upd" },
    ]
  });
}
//Autodebit subscription Status Report//
$('#scheme_wise_search').on('click', function () {
  payment_schemewise();
});
function get_schemeclassifyname() {
  $(".overlay").css('display', 'block');
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/get/schemeclassify_list',
    dataType: 'json',
    success: function (data) {
      var schemeclassify_val = $('#id_classifications').val();
      $.each(data, function (key, item) {
        $('#classify_select').append(
          $("<option></option>")
            .attr("value", item.id_classification)
            .text(item.classification_name)
        );
      });
      $("#classify_select").select2({
        placeholder: "Select Scheme Classify name",
        allowClear: true
      });
      $("#classify_select").select2("val", (schemeclassify_val != '' && schemeclassify_val > 0 ? schemeclassify_val : ''));
      $(".overlay").css("display", "none");
    }
  });
}
$('#schemw_wise_collection').on('click', function () {
  get_collection_report();
});
function get_collection_report() {
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $.ajax({
    url: base_url + "index.php/admin_reports/scheme_daily_collection_details?nocache=" + my_Date.getUTCSeconds(),
    data: ({ 'from_date': $('#rpt_payments1').html(), 'to_date': $('#rpt_payments2').html(), 'id_branch': $('#branch_select').val() }),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $("#collection_report > tbody > tr").remove();
      $('#collection_report').dataTable().fnClearTable();
      $('#collection_report').dataTable().fnDestroy();
      trHTML = '';
      opening_blc_amt = 0;
      opening_bonus_amt = 0;
      opening_blc_wgt = 0;
      today_collection_amt = 0;
      today_bonus_amt = 0;
      today_collection_wgt = 0;
      today_closed_amount = 0;
      today_bonus_deduction = 0;
      today_closed_weight = 0;
      closing_balance_amt = 0;
      closing_balance_wgt = 0;
      closing_bonus_amt = 0;
      var i = 1;
      $.each(data, function (key, items) {
        opening_blc_amt += parseFloat(items.opening_blc_amt);
        opening_bonus_amt += parseFloat(items.opening_bonus_amt);
        opening_blc_wgt += parseFloat(items.opening_blc_wgt);
        today_collection_amt += parseFloat(items.today_collection_amt);
        today_bonus_amt += parseFloat(items.today_bonus_amt);
        today_collection_wgt += parseFloat(items.today_collection_wgt);
        today_closed_amount += parseFloat(items.today_closed_amount);
        today_bonus_deduction += parseFloat(items.today_bonus_deduction);
        today_closed_weight += parseFloat(items.today_closed_weight);
        closing_balance_amt += parseFloat(items.closing_balance_amt);
        closing_balance_wgt += parseFloat(items.closing_balance_wgt);
        closing_bonus_amt += parseFloat(items.closing_bonus_amt);
        trHTML += '<tr>' +
          '<td>' + i + '</td>' +
          '<td>' + items.scheme_name + '</td>' +
          '<td>' + items.opening_blc_amt + '</td>' +
          '<td>' + items.opening_bonus_amt + '</td>' +
          '<td>' + formatMetalWeight(items.opening_blc_wgt) + '</td>' +
          '<td>' + items.today_collection_amt + '</td>' +
          '<td>' + items.today_bonus_amt + '</td>' +
          '<td>' + formatMetalWeight(items.today_collection_wgt) + '</td>' +
          '<td>' + items.today_closed_amount + '</td>' +
          '<td>' + items.today_bonus_deduction + '</td>' +
          '<td>' + formatMetalWeight(items.today_closed_weight) + '</td>' +
          '<td>' + items.closing_balance_amt + '</td>' +
          '<td>' + items.closing_bonus_amt + '</td>' +
          '<td>' + formatMetalWeight(items.closing_balance_wgt) + '</td>' +
          '</tr>';
        i++;
      });
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Opening Blc Amount</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(opening_blc_amt).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Opening Bonus Amount</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(opening_bonus_amt).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Opening Blc Weight</strong></td>' +
        '<td style="text-align:right;">' + formatMetalWeight(opening_blc_wgt) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Received Amount</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(today_collection_amt).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Bonus Allocated</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(today_bonus_amt).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Received Weight</strong></td>' +
        '<td style="text-align:right;">' + (formatMetalWeight(today_collection_wgt)) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Closed Amount</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(today_closed_amount).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Bonus Deduction</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(today_bonus_deduction).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Closed Weight</strong></td>' +
        '<td style="text-align:right;">' + (formatMetalWeight(today_closed_weight)) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Closing Blc Amount</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(closing_balance_amt).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Closing Blc Bonus</strong></td>' +
        '<td style="text-align:right;">' + parseFloat(closing_bonus_amt).toFixed(2) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td style="text-align:left;" colspan="2"><strong>Closing Blc Weight</strong></td>' +
        '<td style="text-align:right;">' + (formatMetalWeight(closing_balance_wgt)) + '</td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      $('#collection_report > tbody').html(trHTML);
      if (!$.fn.DataTable.isDataTable('#collection_report')) {
        oTable = $('#collection_report').dataTable({
          "bSort": false,
          "bInfo": true,
          "scrollX": '100%',
          "dom": 'lBfrtip',
          "lengthMenu": [[-1, 25, 50, 100, 250], ["All", 25, 50, 100, 250]],
          "buttons": [
            {
              extend: 'print',
              footer: true,
              title: 'Scheme Wise Collection Report',
              orientation: 'landscape',
              customize: function (win) {
                $(win.document.body).find('table')
                  .addClass('compact');
                $(win.document.body).find('table')
                  .addClass('compact')
                  .css('font-size', '10px')
                  .css('font-family', 'sans-serif');
              },
            },
            {
              extend: 'excel',
              footer: true,
              title: 'Scheme Wis Collection Report',
            }
          ],
        });
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
//closing branch//
function get_cls_branchname() {
  //alert('sa.js');
  //$(".overlay").css('display','block');	
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/branch/branchname_list',
    dataType: 'json',
    success: function (data) {
      console.log(data);
      var id_branch = $('#close_id_branch').val();
      $.each(data.branch, function (key, item) {
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
//closing branch //
//closed A/C report with date picker, cost center based branch fillter//HH
//closed acc report starts here
$('#closed_acc_search').on('click', function () {
  get_closed_acc_list();
});
function get_closed_acc_list() {
  var from_date = $('#rpt_payments1').text();
  var to_date = $('#rpt_payments2').text();
  $("#date_range").text("From : " + from_date + " To : " + to_date);
  my_Date = new Date();
  //postData = {'from_date':$('#rpt_payments1').text(),'to_date':$('#rpt_payments2').text(),'id_employee':$('#emp_select').val(),'close_id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#close_branch_select").val())},
  postData = { 'from_date': $('#rpt_payments1').text(), 'to_date': $('#rpt_payments2').text(), 'account_type': $('#account_type_select').val(), 'id_scheme': $('#scheme_select').val(), 'id_employee': $('#emp_select').val(), 'close_id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#close_branch_select").val()) },
    $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/closedaccount_list?nocache=" + my_Date.getUTCSeconds(),
    data: (postData),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $('#total_closed_accounts').text(data.length);
      set_closed_acc_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_closed_acc_list(data) {
  var from_date = $("#rpt_payments1").text();
  var to_date = $("#rpt_payments2").text();
  var branch_name = getBranchTitle();
  var title_details = get_title(
    from_date,
    to_date,
    "Closed Account Report -" + branch_name
  );
  var count = 0;
  var account = data.accounts;
  var sum_tot_acc = (sum_tot_amt = sum_tot_wgt = 0);
  var sub_close_amt = (grand_close_amt = 0);
  var sub_close_wgt = (grand_close_wgt = 0);
  var sub_cus_paid = (grand_cus_paid = 0);
  var sub_pre_close = (grand_pre_close = 0);
  var sub_bonus = (grand_bonus = 0);
  var summary = data.closed_summary;
  var scheme_acc_number;
  console.log(summary);
  $("div.overlay").css("display", "none");
  var srHTML = (title_summ = "");
  //title_summ - to show in print
  title_summ +=
    '<table class="table table-bordered text-center" style="width:800px;margin:0 auto;">' +
    "<thead><tr><th>Scheme Name</th><th>Account Count</th><th>Closed Amount(INR)</th><th>Closed Weight(g)</th></tr></thead>";
  $.each(summary, function (key, sum_data) {
    srHTML +=
      "<tr>" +
      '<td style="text-align:left;">' +
      sum_data.scheme_name +
      "</td>" +
      '<td style="text-align:right;">' +
      sum_data.acc_count +
      "</td>" +
      '<td style="text-align:right;">' +
      formatCurrency.format(sum_data.closing_amount) +
      "</td>" +
      '<td style="text-align:right;">' +
      (sum_data.closing_weight != 0
        // ? parseFloat(sum_data.closing_weight).toFixed(3)
        ? formatMetalWeight((sum_data.closing_weight))
        : "") +
      "</td>" +
      "</tr>";
    sum_tot_acc += parseInt(sum_data.acc_count);
    sum_tot_amt += parseFloat(sum_data.closing_amount);
    sum_tot_wgt += parseFloat(sum_data.closing_weight);
  });
  srHTML +=
    '<tr style="background: #d2d6de;">' +
    '<td style="text-align:left;font-weight:bold">Total : </td>' +
    '<td style="text-align:right;font-weight:bold">' +
    sum_tot_acc +
    "</td>" +
    '<td style="text-align:right;font-weight:bold">' +
    formatCurrency.format(sum_tot_amt) +
    "</td>" +
    '<td style="text-align:right;font-weight:bold">' +
    (sum_tot_wgt != 0 ? (formatMetalWeight(sum_tot_wgt)) : "") +
    "</td>" +
    "</tr>";
  $("#closed_summary_list > tbody").html(srHTML);
  $("#closed_list > tbody > tr").remove();
  $("#closed_list").dataTable().fnClearTable();
  $("#closed_list").dataTable().fnDestroy();
  var trHTML = "";
  $.each(account, function (key, ac_data) {
    trHTML +=
      "<tr>" +
      '<td style="text-align:left;" class="report-key"><strong>' +
      key +
      "</strong></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      //  '<td></td>'+
      "</tr>";
    sub_close_amt = 0;
    sub_close_wgt = 0;
    sub_cus_paid = 0;
    sub_pre_close = 0;
    sub_bonus = 0;
    var sno = 0;
    $.each(ac_data, function (index, items) {
      count++;
      sno++;
      //scheme_acc_number=getSchAccNumber_Format(items.is_lucky_draw,items.scheme_group_code,items.scheme_acc_number,items.schemeaccNo_displayFrmt,items.scheme_wise_acc_no,items.acc_branch,items.start_year,items.code);
      var url =
        base_url +
        "/index.php/reports/payment/account/" +
        items.id_scheme_account;
      //for account number
      var acc_num =
        "<a href=" +
        url +
        ' target="_blank">' +
        items.scheme_acc_number +
        "</a>";
      //for bill number
      var bill_url =
        base_url +
        "/index.php/admin_ret_billing/billing_invoice/" +
        items.bill_id;
      if (items.bill_id != "" && items.bill_id != null) {
        var bill_num =
          "<a href=" + bill_url + ' target="_blank">' + items.bill_no + "</a>";
      } else {
        var bill_num = "-";
      }
      //for closing amount
      /*if(items.scheme_types!=2 && items.scheme_types!=3)
      {
        var closing_bal=formatCurrency.format(items.closing_balance);
        sub_close_amt+=parseFloat(items.closing_balance);
      }
      else
      {
        var closing_bal= formatCurrency.format(items.closing_amount);
        sub_close_amt+=parseFloat(items.closing_amount);
      }*/
      //for closing weight
      /*if(items.scheme_types==2 || items.scheme_types==3)
      {
        var close_wgt=parseFloat(parseFloat(items.closing_balance!="" && items.closing_balance!=null?items.closing_balance:0)+parseFloat(items.balance_weight)).toFixed(3);
        sub_close_wgt+=parseFloat(close_wgt);
      }
      else
      {
        var close_wgt=parseFloat(0).toFixed(3);
        sub_close_wgt+=parseFloat(close_wgt);
      }*/
      //for closing branch
      if (items.closing_branch != "" && items.closing_branch != null) {
        var closing_branch = items.closing_branch;
      } else {
        var closing_branch = "-";
      }
      //closing_amount
      sub_close_amt += parseFloat(items.closing_amount);
      //closing_weight
      sub_close_wgt += parseFloat(items.closing_weight);
      //amountpaid
      sub_cus_paid += parseFloat(
        parseFloat(items.closing_paid_amt) +
        parseFloat(items.balance_amount) -
        parseFloat(items.closing_benefits)
      );
      //pre close charge
      sub_pre_close += parseFloat(items.closing_add_chgs);
      //Bonus
      sub_bonus += parseFloat(items.closing_benefits);
      trHTML +=
        "<tr>" +
        "<td>" +
        sno +
        "</td>" +
        "<td>" +
        items.id_scheme_account +
        "</td>" +
        "<td>" +
        items.code +
        "</td>" +
        "<td>" +
        acc_num +
        "</td>" +
        "<td>" +
        items.account_name +
        "</td>" +
        // '<td style="text-align:left;">'+items.name+'</td>'+
        "<td>" +
        items.mobile +
        "</td>" +
        "<td>" +
        items.start_date +
        "</td>" +
        "<td>" +
        items.scheme_type +
        "</td>" +
        "<td>" +
        items.paid_installments +
        "/" +
        items.total_installments +
        "</td>" +
        "<td>" +
        formatCurrency.format(
          parseFloat(
            parseFloat(items.closing_paid_amt) +
            parseFloat(items.balance_amount) -
            parseFloat(items.closing_benefits)
          ).toFixed(2)
        ) +
        "</td>" +
        "<td>" +
        formatCurrency.format(items.closing_amount) +
        "</td>" +
        "<td>" +
        (items.closing_weight != 0
          // ? parseFloat(items.closing_weight).toFixed(3)
          ? formatMetalWeight((items.closing_weight))
          : "") +
        "</td>" +
        "<td>" +
        (items.acc_branch != "" ? items.acc_branch : "-") +
        "</td>" +
        "<td>" +
        closing_branch +
        "</td>" +
        "<td>" +
        items.closing_date +
        "</td>" +
        "<td>" +
        (items.closing_add_chgs != 0
          ? formatCurrency.format(items.closing_add_chgs)
          : "") +
        "</td>" +
        "<td>" +
        (items.closing_benefits != 0
          ? formatCurrency.format(items.closing_benefits)
          : "") +
        "</td>" +
        // '<td>'+bill_num+'</td>'+
        "<td>" +
        items.employee_closed +
        "</td>" +
        "<td>" +
        (items.referred_employee != "" ? items.referred_employee : "-") +
        "</td>" +
        // '<td style="text-align:right;">'+formatCurrency.format(items.pay_amount)+'</td>'+
        "</tr>";
    });
    grand_close_amt += parseFloat(sub_close_amt);
    grand_close_wgt += parseFloat(sub_close_wgt);
    grand_cus_paid += parseFloat(sub_cus_paid);
    grand_pre_close += parseFloat(sub_pre_close);
    grand_bonus += parseFloat(sub_bonus);
    trHTML +=
      "<tr>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      '<td style="text-align:left;" class="report-sub-total">SUB TOTAL  </td>' +
      '<td class="report-sub-total">' +
      formatCurrency.format(sub_cus_paid) +
      "</td>" +
      '<td class="report-sub-total">' +
      formatCurrency.format(sub_close_amt) +
      "</td>" +
      '<td class="report-sub-total">' +
      (sub_close_wgt != 0 ? (formatMetalWeight(sub_close_wgt)) : "") +
      "</td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      '<td class="report-sub-total">' +
      (sub_pre_close != 0 ? formatCurrency.format(sub_pre_close) : "") +
      "</td>" +
      '<td class="report-sub-total">' +
      (sub_bonus != 0 ? formatCurrency.format(sub_bonus) : "") +
      "</td>" +
      "<td></td>" +
      "<td></td>" +
      "</tr>";
  });
  trHTML +=
    "<tr>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    '<td style="text-align:left;" class="report-grand-total">GRAND TOTAL  </td>' +
    '<td class="report-grand-total">' +
    formatCurrency.format(grand_cus_paid) +
    "</td>" +
    '<td class="report-grand-total">' +
    formatCurrency.format(grand_close_amt) +
    "</td>" +
    '<td class="report-grand-total">' +
    (grand_close_wgt != 0 ? (formatMetalWeight(grand_close_wgt)) : "") +
    "</td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    '<td class="report-grand-total">' +
    (grand_pre_close != 0 ? formatCurrency.format(grand_pre_close) : "") +
    "</td>" +
    '<td class="report-grand-total">' +
    (grand_bonus != 0 ? formatCurrency.format(grand_bonus) : "") +
    "</td>" +
    "<td></td>" +
    "<td></td>" +
    "</tr>";
  $("#closed_list > tbody").html(trHTML);
  $("#total_closed_accounts").text(count);
  title_details += title_summ + srHTML + "</table>";
  if (!$.fn.DataTable.isDataTable("#closed_list")) {
    if (summary.length !== 0 && account.length !== 0) {
      oTable = $("#closed_list").dataTable({
        bSort: false,
        bInfo: false,
        scrollX: "100%",
        dom: "lBfrtip",
        pageLength: 25,
        lengthMenu: [
          [-1, 25, 50, 100, 250],
          ["All", 25, 50, 100, 250],
        ],
        buttons: [
          {
            extend: "print",
            footer: true,
            title: "",
            messageTop: title_details,
            orientation: "landscape",
            customize: function (win) {
              /* $(win.document.body).find("table").addClass("compact");
              $(win.document.body)
                .find("table")
                .addClass("compact")
                .css("font-size", "10px")
                .css("font-family", "sans-serif"); */
              $(win.document.body).find('table')
                .addClass('compact');
              $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', '10px')
                .css('font-family', 'sans-serif');

              // Inject refined CSS for borders and page layout
              var css = 'table.dataTable { ' +
                'border-collapse: collapse !important; ' +
                'width: 99% !important; ' +
                'margin: 0 auto !important; ' +
                'border: 1px solid black !important; ' +
                'box-sizing: border-box !important; } ' +
                'table.dataTable th, table.dataTable td { ' +
                'border: 1px solid black !important; } ' +
                '@page { size: landscape; margin: 10mm; }';
              var head = win.document.head || win.document.getElementsByTagName('head')[0];
              var style = win.document.createElement('style');
              style.type = 'text/css';
              style.media = 'print';
              if (style.styleSheet) {
                style.styleSheet.cssText = css;
              } else {
                style.appendChild(win.document.createTextNode(css));
              }
              head.appendChild(style);
            },
          },
          {
            extend: "excel",
            footer: true,
            title:
              "Closed Account Report " +
              branch_name +
              " " +
              from_date +
              " - " +
              to_date,
          },
          {
            extend: "colvis",
            collectionLayout: "fixed columns",
            collectionTitle: "Column visibility control",
          },
        ],
        columnDefs: [
          {
            targets: [2, 3, 4, 5, 6, 7, 12, 13, 14, 18],
            className: "dt-left",
          },
          {
            targets: [0, 1, 8, 9, 10, 11, 15, 16, 17],
            className: "dt-right",
          },
        ],
      });
    } else {
      var sumHTML = "";
      var onHTML = "";
      sumHTML +=
        '<tr><td colspan=4 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
      $("#closed_summary_list > tbody").html(sumHTML);
      onHTML +=
        '<tr><td colspan=19 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
      $("#closed_list > tbody").html(onHTML);
    }
  }
}
//closed acc report ends here		
//Customer Account Details
function get_customer_account_details(from_date, to_date) {
  var company_name = $('#company_name').val();
  var title = "<b><span style='font-size:15pt;margin-left:30%;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>" + "<span style=font-size:13pt;margin-left:25%;>&nbsp;&nbsp;Customer Account Details Report</span>";
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $.ajax({
    url: base_url + "index.php/admin_reports/customer_account_details/ajax?nocache=" + my_Date.getUTCSeconds(),
    data: ({ 'from_date': from_date, 'to_date': to_date, 'id_branch': $('#branch_select').val(), 'id_scheme': $('#scheme_select').val(), }),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var oTable = $('#acc_list').DataTable();
      oTable.clear().draw();
      if (data != null && data.length > 0) {
        oTable = $('#acc_list').dataTable({
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
              title: "Chit Acccount Details",
            }
          ],
          "aaData": data,
          "aoColumns": [
            {
              "mDataProp": function (row, type, val, meta) {
                var url = base_url + 'index.php/admin_reports/scheme_account_report/' + row.id_scheme_account;
                return '<a href=' + url + ' target="_blank">' + row.id_scheme_account + '</a>';
              }
            },
            { "mDataProp": "name" },
            { "mDataProp": "mobile" },
            { "mDataProp": "code" },
            { "mDataProp": "scheme_acc_number" },
            { "mDataProp": "tot_acc" },
            {
              "mDataProp": function (row, type, val, meta) {
                if (row.active_acc > 0) {
                  return '<span class="badge bg-green">' + row.active_acc + '</span>';
                } else {
                  return '<span class="badge bg-red">' + row.active_acc + '</span>';
                }
              }
            },
            { "mDataProp": "start_date" },
            {
              "mDataProp": function (row, type, val, meta) {
                return row.paid_installments + '/' + row.total_installments;
              }
            },
            {
              "mDataProp": function (row, type, val, meta) {
                var pending_due = row.paid_installments - row.total_installments;
                if (pending_due < 0) {
                  return pending_due * (-1);
                } else {
                  return pending_due;
                }
              }
            },
            { "mDataProp": "last_paid_date" },
            { "mDataProp": "month_ago" },
            { "mDataProp": "closing_date" },
            {
              "mDataProp": function (row, type, val, meta) {
                if (row.is_closed == 1) {
                  return '<span class="badge bg-orange">Closed</span>';
                }
                else if (row.month_ago > 3) {
                  return '<span class="badge bg-red">Inactive</span>';
                } else {
                  return '<span class="badge bg-green">Live</span>';
                }
              }
            },
          ],
        });
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
//Customer Account Details
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
    + "<span style='font-size:11pt;'>" + company_address2 + company_city + "-" + pincode + "</span></br>";
  +"<span style='font-size:11pt;'>GSTIN:" + company_gst_number + ", EMAIL:" + company_email + "</span></br>"
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
  select_date += "<span style=font-size:11pt;>Print Taken On : " + moment().format("dddd, MMMM Do YYYY, h:mm:ss a")
    + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
    + "<span style=font-size:11pt;>Print Taken By : " + $('.hidden-xs').html() + "</span></br><span style='font-size:11pt;'>Amount in : <b>" + session_data.currency_symbol + "</b>&nbsp; | &nbsp; Weight in : <b >Gram</b></span></br></div>";
  return select_date;
}
//closed A/C report with date picker, cost center based branch fillter//
//Online Payment Report
$('#online_report_search').on('click', function () {
  get_online_payment_report();
});
function get_online_payment_report() {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    data: ({ 'from_date': $('#from_date').html(), 'to_date': $('#to_date').html(), 'id_status_msg': $('#pay_status').val(), 'id_branch': $('#branch_select').val() }),
    url: base_url + "index.php/admin_reports/get_online_payment_report?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      console.log(data);
      online_payment_report_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function online_payment_report_list(data) {
  $("div.overlay").css("display", "block");
  var online_payment = data;
  var trHtml = "";
  var total_amount = 0;
  $("#online_payment_report_list > tbody > tr").remove();
  $("#online_payment_report_list").dataTable().fnClearTable();
  $("#online_payment_report_list").dataTable().fnDestroy();
  $.each(online_payment, function (branch, payment) {
    var branch_total_amount = 0;
    trHtml +=
      '<tr style="font-weight:bold;">' +
      "<td>" +
      branch +
      "</td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "</tr>";
    $.each(payment, function (key, items) {
      total_amount += parseFloat(
        parseFloat(
          parseFloat(items.payment_amount) - parseFloat(items.discountAmt)
        ).toFixed(2)
      );
      branch_total_amount += parseFloat(
        parseFloat(
          parseFloat(items.payment_amount) - parseFloat(items.discountAmt)
        ).toFixed(2)
      );
      trHtml +=
        "<tr>" +
        "<td>" +
        items.id_payment +
        "</td>" +
        "<td>" +
        items.date_payment +
        "</td>" +
        "<td>" +
        items.name +
        "</td>" +
        "<td>" +
        items.account_name +
        "</td>" +
        "<td>" +
        items.code +
        "</td>" +
        "<td>" +
        items.scheme_acc_number +
        "</td>" +
        "<td>" +
        items.mobile +
        "</td>" +
        "<td>" +
        parseFloat(
          parseFloat(items.payment_amount) - parseFloat(items.discountAmt)
        ).toFixed(2) +
        "</td>" +
        "<td>" +
        items.discountAmt +
        "</td>" +
        "<td>" +
        formatMetalWeight(items.metal_weight) +
        "</td>" +
        "<td>" +
        items.metal_rate +
        "</td>" +
        '<td><span class="label bg-' +
        items.status_color +
        '-active">' +
        items.payment_status +
        "</span></td>" +
        "<td>" +
        (items.paid_installments != null ? items.paid_installments : 0) +
        "</td>" +
        "<td>" +
        items.payment_type +
        "</td>" +
        "<td>" +
        (items.payment_mode != null ? items.payment_mode : "") +
        "</td>" +
        "<td>" +
        items.payment_ref_number +
        "</td>" +
        "<td>" +
        (items.added_by == 0
          ? "Admin"
          : items.added_by == 1
            ? "Web App"
            : items.added_by == 2
              ? "Mobile"
              : "Collection App") +
        "</td>" +
        "</tr>";
    });
    trHtml +=
      '<tr style="font-weight:bold;">' +
      "<td>SUB TOTAL</td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td>" +
      parseFloat(branch_total_amount).toFixed(2) +
      "</td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "</tr>";
  });
  trHtml +=
    '<tr style="font-weight:bold;">' +
    "<td>GRAND TOTAL</td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td>" +
    parseFloat(total_amount).toFixed(2) +
    "</td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "<td></td>" +
    "</tr>";
  $("#online_payment_report_list > tbody").html(trHtml);
  if (!$.fn.DataTable.isDataTable("#online_payment_report_list")) {
    oTable = $("#online_payment_report_list").dataTable({
      bSort: false,
      bInfo: false,
      scrollX: "100%",
      dom: "lBfrtip",
      lengthMenu: [
        [-1, 25, 50, 100, 250],
        ["All", 25, 50, 100, 250],
      ],
      buttons: [
        {
          extend: "print",
          footer: true,
          title:
            "Online Payment Report " +
            $("#from_date").html() +
            " - " +
            $("#to_date").html(),
          orientation: "landscape",
          customize: function (win) {
            $(win.document.body).find("table").addClass("compact");
            $(win.document.body)
              .find("table")
              .addClass("compact")
              .css("font-size", "10px")
              .css("font-family", "sans-serif");
          },
        },
        {
          extend: "excel",
          footer: true,
          title:
            "Online Payment Report " +
            $("#from_date").html() +
            " - " +
            $("#to_date").html(),
        },
      ],
    });
  }
  $("div.overlay").css("display", "none");
}
//Online Payment Report
function get_payment_status() {
  $(".overlay").css('display', 'block');
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/admin_reports/get_payment_status',
    dataType: 'json',
    success: function (data) {
      var id = $('#pay_status').val();
      $.each(data, function (key, item) {
        $('#pay_status').append(
          $("<option></option>")
            .attr("value", item.id_status_msg)
            .text(item.payment_status)
        );
      });
      $("#pay_status").select2({
        placeholder: "Select Pay Status",
        allowClear: true
      });
      $("#pay_status").select2("val", (id != '' && id > 0 ? id : ''));
      $(".overlay").css("display", "none");
    }
  });
}
$('#old_metal_search').on('click', function () {
  get_old_metal_report();
});
function get_old_metal_report() {
  var company_name = $("#company_name").val();
  var title =
    "<b><span style='font-size:15pt;margin-left:30%;'>" +
    company_name +
    "</span></b><b><span style='font-size:12pt;'></span></b></br>" +
    "<span style=font-size:13pt;margin-left:25%;>&nbsp;&nbsp;Old Metal Details Report </span>" +
    $("#rpt_payments1").text() +
    " - " +
    $("#rpt_payments2").text();
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/old_metal_report/ajax?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      from_date: $("#rpt_payments1").html(),
      to_date: $("#rpt_payments2").html(),
      id_branch:
        $("#branch_select").val() != "" &&
          $("#branch_select").val() != undefined
          ? $("#branch_select").val()
          : $("#branch_filter").val(),
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var list = data.list;
      var oTable = $("#old_metal_report").DataTable();
      oTable.clear().draw();
      if (list != null && list.length > 0) {
        oTable = $("#old_metal_report").dataTable({
          bDestroy: true,
          bInfo: true,
          bFilter: true,
          scrollX: "100%",
          bSort: true,
          dom: "lBfrtip",
          order: [[0, "desc"]],
          buttons: [
            {
              extend: "print",
              footer: true,
              //title: 'Old Metal Details '+$('#rpt_payments1').text()+' - '+$('#rpt_payments2').text(),
              messageTop: title,
              customize: function (win) {
                $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "inherit");
              },
            },
            {
              extend: "excel",
              footer: true,
              title:
                "Old Metal Details " +
                $("#rpt_payments1").text() +
                " - " +
                $("#rpt_payments2").text(),
            },
          ],
          aaData: list,
          aoColumns: [
            /* { "mDataProp": function ( row, type, val, meta )
             { 
               var url = base_url+'index.php/admin_reports/scheme_account_report/'+row.id_scheme_account;
               return '<a href='+url+' target="_blank">'+row.id_scheme_account+'</a>';
             }},*/
            { mDataProp: "id_payment" },
            { mDataProp: "payment_date" },
            { mDataProp: "branch_name" },
            { mDataProp: "cus_name" },
            {
              mDataProp: function (row, type, val, meta) {
                var url =
                  base_url +
                  "index.php/payment/invoice/" +
                  row.id_payment +
                  "/" +
                  row.id_scheme_account;
                return (
                  "<a href=" +
                  url +
                  ' target="_blank">' +
                  row.acc_number +
                  "</a>"
                );
              },
            },
            { mDataProp: "account_name" },
            {
              mDataProp: function (row, type, val, meta) {
                var url =
                  base_url +
                  "index.php/admin_ret_estimation/generate_invoice/" +
                  row.estimation_id;
                return (
                  "<a href=" + url + ' target="_blank">' + row.esti_no + "</a>"
                );
              },
            },
            { mDataProp: "emp_name" },
            { mDataProp: "gross_wt" },
            { mDataProp: "net_wt" },
            { mDataProp: "old_metal_amount" },
            { mDataProp: "pay_emp" },
          ],
          footerCallback: function (row, data, start, end, display) {
            if (list.length > 0) {
              var api = this.api(),
                data;
              for (var i = 0; i <= data.length - 1; i++) {
                var intVal = function (i) {
                  return typeof i === "string"
                    ? i.replace(/[\$,]/g, "") * 1
                    : typeof i === "number"
                      ? i
                      : 0;
                };
                $(api.column(0).footer()).html("Total");
                gross_wt = api
                  .column(8)
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(8).footer()).html(parseFloat(gross_wt).toFixed(2));
                net_wt = api
                  .column(9)
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(9).footer()).html(parseFloat(net_wt).toFixed(2));
                total_amount = api
                  .column(10)
                  .data()
                  .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                  }, 0);
                $(api.column(10).footer()).html(
                  parseFloat(total_amount).toFixed(2)
                );
              }
            } else {
              var api = this.api(),
                data;
              $(api.column(9).footer()).html("");
            }
          },
        });
      }
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
function get_cancel_pay_list(from_date = "", to_date = "", branch = "") {
  my_Date = new Date();
  postData = (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date, 'id_branch': branch } : ''),
    $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/admin_reports/paymentcancel_list?nocache=" + my_Date.getUTCSeconds(),
    data: (postData),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $('#total_cancel_payments').text(data.length);
      set_cancel_pay_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_cancel_pay_list(data) {
  var oTable = $('#payment_cancel_list').DataTable();
  oTable.clear().draw();
  //console.log(data);
  oTable = $('#payment_cancel_list').dataTable({
    "bDestroy": true,
    "bInfo": true,
    "bFilter": true,
    "bSort": true,
    "dom": 'lBfrtip',
    "order": [[0, "desc"]],
    "buttons": [
      {
        extend: 'print',
        footer: true,
        title: 'Payment Cancelled Report ' + $('#cancel_payment_list1').text() + ' - ' + $('#cancel_payment_list2').text(),
        customize: function (win) {
          $(win.document.body).find('table')
            .addClass('compact')
            .css('font-size', 'inherit');
        },
      },
      {
        extend: 'excel',
        footer: true,
        title: 'Payment Cancelled Report ' + $('#cancel_payment_list1').text() + ' - ' + $('#cancel_payment_list2').text(),
      }
    ],
    "aaData": data,
    "order": [[0, "desc"]],
    "aoColumns": [{ "mDataProp": "id_payment" },
    { "mDataProp": "id_scheme_account" },
    { "mDataProp": "date_payment" },
    { "mDataProp": "approval_date" },
    { "mDataProp": "name" },
    { "mDataProp": "account_name" },
    { "mDataProp": "code" },
    {
      "mDataProp": function (row, type, val, meta) {
        if (row.has_lucky_draw == 1) {
          return row.scheme_group_code + ' ' + row.scheme_acc_number;
        }
        else {
          return row.scheme_acc_number;
        }
      }
    },
    { "mDataProp": "mobile" },
    //{ "mDataProp": "paid_installments" },
    { "mDataProp": "payment_type" },
    { "mDataProp": "payment_mode" },
    { "mDataProp": "metal_rate" },
    { "mDataProp": "metal_weight" },
    { "mDataProp": "employee" },
    {
      "mDataProp": function (row, type, val, meta) { return "<span class='label bg-" + row.status_color + "-active'>" + row.payment_status + "</span>"; }
    },
    {
      "mDataProp": function (row, type, val, meta) {
        return (row.payment_type == 'Payu Checkout' && row.id_status != 1 && (row.due_type == 'A' || row.due_type == 'P') ? row.act_amount : row.payment_amount);
      }
    },
    { "mDataProp": "payment_ref_number" },
    { "mDataProp": "emp_code" }
    ]
  });
  $("div.overlay").css("display", "none");
}
// Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report   --> START
//source wise report - print summary
function printSourceSummary() {
  var branch_name = getBranchTitle();
  const printWindow = window.open("", "_blank");
  var date_type = $("#date_Select").val();
  var title = "";
  title += get_title(
    $("#rpt_payments1").html(),
    $("#rpt_payments2").html(),
    "Summary - Source Wise Report - " + branch_name
  );
  my_Date = new Date();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/payment_summary_modewise?nocache=" +
      my_Date.getUTCSeconds(),
    //data: ( {'from_date':$('#rpt_payments1').html(),'to_date' :$('#rpt_payments2').html(),'id_classfication':$('#id_classifications').val(),'mode':$('#mode_select').val(),'id_scheme':$('#scheme_select').val(),'pay_mode':$('#select_pay_mode').val(),'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#id_branch").val())}),
    data: {
      from_date: $("#rpt_payments1").html(),
      report_type: $("#report_type").val(),
      date_type: date_type,
      to_date: $("#rpt_payments2").html(),
      acc_type: $("#select_acc_type").val(),
      id_classfication: $("#id_classifications").val(),
      mode: $("#mode_select").val(),
      id_scheme: $("#scheme_select").val(),
      pay_mode: $("#select_pay_mode").val(),
      id_branch:
        $("#branch_filter").val() != "" &&
          $("#branch_filter").val() != undefined
          ? $("#branch_filter").val()
          : $("#id_branch").val(),
      id_employee: $("#employee_select").val(),
      metal_date_type: $('#metal_date_type').val()
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      title +=
        '</br><div"><table class="table table-bordered table-striped text-center" style="border: 1px solid black;border-collapse: collapse; width:65%;margin-left:150px;">' +
        '<thead style="font-size:11pt;">' +
        //'<tr><th colspan="3">Payment Summary</th></tr>'+
        '<tr><th style="text-align: center;"><span >Showroom Collection</span></th>' +
        //'<th><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></th>'
        '<th><span style="text-align: center;"><span >Online Collection</span></th>' +
        '<th><span style="text-align: center;"><span >AdminApp Collection</span></th></tr>' +
        "</thead>" +
        '<tbody style="font-size:11pt;"></br>';
      //offline
      if (data.offline_total == null) {
        title += '<td style="text-align:center;">-</td>';
      } else {
        title += "<tr><td>";
        $.each(data.mode_wise.offline, function (key, val) {
          if (val.mode_name == null) {
            var mode = val.payment_mode;
          } else {
            var mode = val.mode_name;
          }
          if (mode != null && val.offline_amt != null) {
            var pay_mode = mode;
            var pay_amt = val.offline_amt;
            // formatCurrency.format(data.online_total)
            //title+='<td><table><tr><td>'+pay_mode+'</td><td></td><td>:</td><td></td><td><strong>'+pay_amt+'</strong></td></tr></table></td>';
            title +=
              '<span class="pull-left">' +
              pay_mode +
              '</span><span></span><span class="rightstyle" ><strong>' +
              formatCurrency.format(pay_amt) +
              "</strong></span><br>";
          }
        });
        title += "</td>";
      }
      // title+='<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
      //offline
      if (data.online_total == null) {
        title += '<td style="text-align:center;">-</td>';
      } else {
        title += "<td>";
        $.each(data.mode_wise.online, function (key, val) {
          if (val.mode_name == null) {
            var mode = val.payment_mode;
          } else {
            var mode = val.mode_name;
          }
          if (mode != null && val.online_amt != null) {
            var pay_mode = mode;
            var pay_amt = val.online_amt;
            title +=
              '<span class="pull-left">' +
              pay_mode +
              '</span><span></span><span class="rightstyle" ><b>' +
              formatCurrency.format(pay_amt) +
              "</b></span><br>";
          }
        });
        title += "</td>";
      }
      //admin app
      if (data.admin_app_total == null) {
        title += '<td style="text-align:center;">-</td>';
      } else {
        title += "<td>";
        $.each(data.mode_wise.admin_app, function (key, val) {
          if (val.mode_name == null) {
            var mode = val.payment_mode;
          } else {
            var mode = val.mode_name;
          }
          if (mode != null && val.admin_app_amt != null) {
            var pay_mode = mode;
            var pay_amt = val.admin_app_amt;
            title +=
              '<span class="pull-left">' +
              pay_mode +
              '</span><span></span><span class="rightstyle"><b>' +
              formatCurrency.format(pay_amt) +
              "</b></span><br>";
          }
        });
        title += "</td></tr>";
      }
      /*
       if(data.online_total == null){
         title+='</br></tr>'+
         '<tr>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.offline_total)+' </strong></td>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.online_total)+' </strong></td>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.admin_app_total)+' </strong></td>'+
         '</tr>';
       }else if(data.offline_total == null){
         title+='</br></tr>'+
         '<tr>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.offline_total)+' </strong></td>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.online_total)+' </strong></td>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.admin_app_total)+' </strong></td>'+
         '</tr>';
       }else if(data.admin_app_total == null){
         title+='</br></tr>'+
         '<tr>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.offline_total)+' </strong></td>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.online_total)+' </strong></td>'+
         '<td> <strong>Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+formatCurrency.format(data.admin_app_total)+' </strong></td>'+
         '</tr>';
       }else{
        title+='</br></tr><tr><td> <strong>Total Payment  &nbsp;&nbsp;: &nbsp;&nbsp;'+formatCurrency.format(data.offline_total)+'  </strong></td><td> <strong>Total Payment &nbsp;&nbsp; :&nbsp;&nbsp;  '+formatCurrency.format(data.online_total)+' </strong> </td><td> <strong>Total Payment &nbsp;&nbsp; :&nbsp;&nbsp;  '+formatCurrency.format(data.admin_app_total)+' </strong> </td></tr>'; 
       }*/
      title +=
        "</br></tr>" +
        "<tr>" +
        '<td> <strong style="float:right;">Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;' +
        formatCurrency.format(data.offline_total) +
        " </strong></td>" +
        '<td> <strong style="float:right;">Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;' +
        formatCurrency.format(data.online_total) +
        " </strong></td>" +
        '<td> <strong style="float:right;">Total Payment &nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;' +
        formatCurrency.format(data.admin_app_total) +
        " </strong></td>" +
        "</tr>";
      title += "</tbody>" + "</table></div></br>";
      var htmlToPrint =
        "" +
        '<style type="text/css">' +
        "table th, table td {" +
        "border:1px solid #000;" +
        "padding:0.5em;" +
        "}" +
        "" +
        ".rightstyle{float: right;}";
      ("</style>");
      title += htmlToPrint;
      printWindow.document.write(title);
      printWindow.document.close();
      printWindow.print();
      printWindow.close();
    },
  });
}
//reportjs
// Jothika 07-11-2025
/* $(document).on('change', '#branch_select', function () {
    if (!$(this).val()) {
        $('#id_branch').val('');
        $('#branch_filter').val('');
    }
    if (ctrl_page[1] == "payment_modeandgroupwise_data") {
        if ($('#branch_select').val() != '') {
          var from_date = $('#rpts_payments1').text();
          var to_date = $('#rpts_payments2').text();
          var id_scheme = $('#scheme_select').val();
          var id_branch = $('#id_branch').val();
          generate_paymode_groupwise_list(from_date, to_date, id_scheme, id_branch);
        }
    }
}); */
function getPaymentDateRangeList() {
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  var date_type = $("#date_Select").val();
  $.ajax({
    url: base_url + "index.php/admin_reports/scheme_payment_list_daterange?nocache=" + my_Date.getUTCSeconds(),
    data: ({ 'from_date': $('#rpt_payments1').html(), 'to_date': $('#rpt_payments2').html(), 'report_type': $('#report_type').val(), 'date_type': date_type, 'id_classfication': $('#id_classifications').val(), 'id_scheme': $('#scheme_select').val(), 'mode': $('#mode_select').val(), 'acc_type': $('#select_acc_type').val(), 'pay_mode': $('#select_pay_mode').val(), 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#id_branch").val()), 'id_employee': $('#employee_select').val(), 'metal_date_type': $('#metal_date_type').val() }),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var branch_name = getBranchTitle();
      $("#source_report_date_range").text($('#rpt_payments1').html() + " to " + $('#rpt_payments2').html());
      //  var branch_name=($('#branch_name').val()!='' && $('#branch_name').val()!=undefined ? $('#branch_name').val() : $('#branch_select option:selected').toArray().map(item => item.text).join());
      var title = '';
      title += get_title($('#rpt_payments1').html(), $('#rpt_payments2').html(), 'Source Wise Report - ' + branch_name);
      title += '</tbody>' +
        '</table></div></br>';
      $("div.overlay").css("display", "none");
      $("#report_payment_daterange > tbody > tr").remove();
      $('#report_payment_daterange').dataTable().fnClearTable();
      $('#report_payment_daterange').dataTable().fnDestroy();
      trHTML = '';
      total_pay_amount = 0;
      total_bonus_amount = 0;
      total_metal_weight = 0;
      var total_gold_metal_weight = 0;
      var total_silver_metal_weight = 0;
      var gold_metal_weight = 0;
      var silver_metal_weight = 0;
      var report_type = $('#report_type').val();
      $.each(data.schemes, function (key, payment) {
        var paid_amount = 0;
        var bonus_amount = 0;
        var metal_weight = 0;
        gold_metal_weight = 0;
        silver_metal_weight = 0;
        var scheme_acc_number;
        var receipt_no;
        if (report_type == 1 || report_type == 2) {
          trHTML += '<tr>' +
            '<td style="text-align:left;" class="report-key"><strong>' + key + '</strong></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>';
          if ($('#old_sch_acc_num').val() == 1) {
            trHTML += '<td></td>';
          }
          trHTML += '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '</tr>';
        }
        $.each(payment, function (key, items) {
          paid_amount += parseFloat(parseFloat(items.payment_amount) - parseFloat(items.discountAmt));
          bonus_amount += parseFloat(items.discountAmt);
          if (items.id_metal == 1) {
            gold_metal_weight += parseFloat(items.metal_weight);
          }
          else if (items.id_metal == 2) {
            silver_metal_weight += parseFloat(items.metal_weight);
          }
          metal_weight += parseFloat(items.metal_weight);
          var sales_ledger = base_url + 'index.php/admin_ret_reports/customer_history/list/' + items.mobile;
          var acc_ledger = base_url + 'index.php/reports/payment/account/' + items.id_scheme_account;
          var receipt_url = base_url + 'index.php/payment/invoice/' + items.id_payment + '/' + items.id_scheme_account;
          trHTML += '<tr>' +
            '<td style="text-align:right;">' + parseFloat(key + 1) + '</td>' +
            '<td style="text-align:left;">' + items.date_payment + '</td>' +
            '<td style="text-align:left;">' + items.custom_entry_date + '</td>';
          /* if ($('#old_sch_acc_num').val() == 1) {
            trHTML += '<td style="text-align:left;">' + (items.old_scheme_acc_number != '' ? items.old_scheme_acc_number : "-") + '</td>';
          } */
          trHTML += '<td style="text-align:left;">' + (items.group_code != '' ? items.group_code : "-") + '</td>' +
            '<td style="text-align:left;">' + items.scheme_acc_number + '</td>' +
            '<td style="text-align:left;">' + items.name + '</td>' +
            '<td style="text-align:left;"><input type="hidden" class="mobile" value="' + items.mobile + '"><a href=' + sales_ledger + ' target="_blank">' + items.mobile + '</td>' +
            '<td style="text-align:right;"><a href=' + receipt_url + ' target="_blank">' + items.receipt_no + '</td>' +
            '<td style="text-align:right;">' + items.paid_installments + '</td>' +
            '<td style="text-align:left;">' + items.payment_mode + '</td>' +
            '<td style="text-align:right;">' + formatCurrency.format(parseFloat(parseFloat(items.payment_amount) - parseFloat(items.discountAmt)).toFixed(2)) + '</td>' +
            // '<td>' + items.metal_rate + '</td>' +
            '<td>' + ((items.is_editing_enabled == 1 && items.metalrate_edit_date) ? '<a  data-toggle="tooltip" title="' + items.metalrate_edit_date + '">' + items.metal_rate + '</a>' : items.metal_rate) + '</td>' +
            // '<td style="text-align:right;">' + (items.metal_weight != 0 && items.metal_weight != "" ? (session_data.metal_wgt_roundoff == 0 ? trimDecimal(items.metal_weight, session_data.metal_wgt_decimal) : items.metal_weight.toFixed(session_data.metal_wgt_decimal) ): "") + '</td>' +
            // '<td style="text-align:right;">' + (items.one_time_premium == 1 && items.fixed_wgt != "" && items.fixed_wgt != null ? (session_data.metal_wgt_roundoff == 0 ? trimDecimal(items.fixed_wgt, session_data.metal_wgt_decimal) : items.fixed_wgt.toFixed(session_data.metal_wgt_decimal)) : "-") + '</td>' +
            '<td style="text-align:right;">' + (items.metal_weight != 0 && items.metal_weight != "" ? (formatMetalWeight(items.metal_weight)) : "") + '</td>' +
            '<td style="text-align:right;">' + (items.one_time_premium == 1 && items.fixed_wgt != "" && items.fixed_wgt != null ? (formatMetalWeight(items.fixed_wgt)) : "-") + '</td>' +
            '<td style="text-align:left;">' + items.pay_branch + '</td>' +
            '<td style="text-align:left;">' + items.payment_through + '</td>' +
            '<td style="text-align:left;">' + items.acc_status + '</td>' +
            '<td style="text-align:left;">' + items.ref_no + '</td>' +
            '<td>' + (items.custom_payment_date ? formatDate(items.custom_payment_date.split(' ')[0]) : '-') + '</td>' +
            '<td style="text-align:left;">' + items.paid_employee + '</td>' +
            '<td style="text-align:left;">' + items.remarks + '</td>' +
            '</tr>';
        });
        if (report_type == 1 || report_type == 2) {
          trHTML += '<tr>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>';
          if ($('#old_sch_acc_num').val() == 1) {
            trHTML += '<td></td>';
          }
          trHTML += '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td><strong class="report-sub-total">SUB TOTAL</strong></td>' +
            '<td style="text-align:right;" class="report-sub-total"><strong>' + formatCurrency.format(parseFloat(paid_amount).toFixed(2)) + '</strong></td>' +
            '<td></td>' +
            // '<td style="text-align:right;" class="report-sub-total"><strong>' + (metal_weight != 0 ? parseFloat(metal_weight).toFixed(3) : "") + '</strong></td>' +
            '<td style="text-align:right;" class="report-sub-total"><strong>' + (metal_weight != 0 ? (formatMetalWeight(metal_weight)) : "") + '</strong></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '<td></td>' +
            '</tr>';
        }
        total_pay_amount += parseFloat(paid_amount);
        total_bonus_amount += parseFloat(bonus_amount);
        total_metal_weight += parseFloat(metal_weight);
        total_gold_metal_weight += parseFloat(gold_metal_weight);
        total_silver_metal_weight += parseFloat(silver_metal_weight);
      });
      trHTML += '<tr>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>';
      if ($('#old_sch_acc_num').val() == 1) {
        trHTML += '<td></td>';
      }
      trHTML += '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td><strong class="report-grand-total">GRAND TOTAL</strong></td>' +
        '<td style="text-align:right;" class="report-grand-total">' + formatCurrency.format(parseFloat(total_pay_amount).toFixed(2)) + '</td>' +
        '<td></td>' +
        // '<td style="text-align:right;" class="report-grand-total">G : ' + (session_data.metal_wgt_roundoff == 0 ? trimDecimal(total_gold_metal_weight, session_data.metal_wgt_decimal) : total_gold_metal_weight.toFixed(session_data.metal_wgt_decimal)) + '  <br> S : ' + (session_data.metal_wgt_roundoff == 0 ? trimDecimal(total_silver_metal_weight, session_data.metal_wgt_decimal) : total_silver_metal_weight.toFixed(session_data.metal_wgt_decimal)) + ' </td>' +
        '<td style="text-align:right;" class="report-grand-total">G : ' + (formatMetalWeight(total_gold_metal_weight)) + '  <br> S : ' + (formatMetalWeight(total_silver_metal_weight)) + ' </td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      console.log(data.schemes.length !== 0);
      $('#report_payment_daterange > tbody').html(trHTML);
      // Check and initialise datatable
      if (!$.fn.DataTable.isDataTable('#report_payment_daterange')) {
        if (data.schemes.length !== 0) {
          /* oTable = $('#report_payment_daterange').dataTable({ 
         "bSort": false, 
         "bInfo": false, 
         "scrollX":'100%',  
         "dom": 'Blfrtip',
         "lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
         "buttons": [
                 {
                  extend: 'print',
                  footer: true,
                  title: '',
                  messageTop :title,
                  orientation: 'landscape',
                   //autoPrint: false,
                  customize: function ( win ) {
                     $(win.document.body).find('table')
                     .addClass('compact');
                     $(win.document.body).find( 'table' )
                       .addClass('compact')
                       .css('font-size','10px')
                       .css('font-family','sans-serif');
                     $(win.document.body).find('tr:nth-child(odd) td').each(function(index){
                       $(this).css('font-weight','bold');
                     });
                   },
                   exportOptions: {columns: ':visible'},
                 },
                 {
                 extend:'excel',
                 footer: true,
                 title: ' Source Wise Report '+$('#rpt_payments1').html()+' - '+$('#rpt_payments2').html(),
                  },
                 {
                   extend:'excelHtml5',
                   footer: true,
                   title: 'Source Wise Report - '+branch_name+' '+$('#rpt_payments1').html()+' - '+$('#rpt_payments2').html(),
                   customize: function(xlsx) {
                         var sheet = xlsx.xl.worksheets['sheet1.xml'];
                         // jQuery selector to add a border
                         //$('row c[r*="2"]', sheet).attr( 's', '25' );
                         // Loop over the cells in column `J`
                         $('row c[r^="J"]', sheet).each( function () {
                           // Get the value
                           if ( $('is t', this).text() == 'SUB TOTAL' ) {
                             $(this).attr( 's', '20' );
                           }
                           if ( $('is t', this).text() == 'GRAND TOTAL' ) {
                             $(this).attr( 's', '20' );
                           }
                         });
                       }
                 },
                 {extend: 'colvis',collectionLayout: 'fixed columns',collectionTitle: 'Column visibility control'},
               ], 
                 "columnDefs": 
                [
                  {
                    targets: [0,1,2,3,4,5,6,7,9,13,14,15,16,17], 
                    className: 'dt-left'
                  },
                  {
                    targets: [8,10,11,12], 
                    className: 'dt-right'
                  },
                  {"width": "120px", "targets": 1},
                ],
        });*/
          oTable = $('#report_payment_daterange').dataTable({
            "bSort": false,
            "bInfo": false,
            "scrollX": '100%',
            "dom": 'lBfrtip',
            "pageLength": 25,
            "lengthMenu": [[-1, 25, 50, 100, 250], ["All", 25, 50, 100, 250]],
            "buttons": [
              {
                extend: 'print',
                footer: true,
                title: '',
                messageTop: title,
                orientation: 'landscape',
                customize: function (win) {
                  $(win.document.body).find('table')
                    .addClass('compact');
                  $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', '10px')
                    .css('font-family', 'sans-serif');

                  // Inject refined CSS for borders and page layout
                  var css = 'table.dataTable { ' +
                    'border-collapse: collapse !important; ' +
                    'width: 99% !important; ' +
                    'margin: 0 auto !important; ' +
                    'border: 1px solid black !important; ' +
                    'box-sizing: border-box !important; } ' +
                    'table.dataTable th, table.dataTable td { ' +
                    'border: 1px solid black !important; } ' +
                    '@page { size: landscape; margin: 10mm; }';
                  var head = win.document.head || win.document.getElementsByTagName('head')[0];
                  var style = win.document.createElement('style');
                  style.type = 'text/css';
                  style.media = 'print';
                  if (style.styleSheet) {
                    style.styleSheet.cssText = css;
                  } else {
                    style.appendChild(win.document.createTextNode(css));
                  }
                  head.appendChild(style);
                },
                exportOptions: { columns: ':visible' },
              },
              {
                extend: 'excel',
                footer: true,
                title: 'Source Wise Report - ' + branch_name + ' ' + $('#rpt_payments1').html() + ' - ' + $('#rpt_payments2').html(),
              },
              { extend: 'colvis', collectionLayout: 'fixed columns', collectionTitle: 'Column visibility control' },
            ],
            "columnDefs":
              [
                {
                  targets: [1, 2, 3, 4, 5, 9, 14, 15, 18, 19],
                  className: 'dt-left'
                },
                {
                  targets: [0, 6, 7, 8, 10, 11, 12, 13, 16, 17],
                  className: 'dt-right'
                },
                { "width": "120px", "targets": 1 },
              ],
          });
        }
        else {
          var onHTML = '';
          onHTML += '<tr><td colspan=20 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
          $('#report_payment_daterange > tbody').html(onHTML);
        }
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function getPaymentSummary() {
  var date_type = $("#date_Select").val();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/payment_summary_modewise?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      from_date: $("#rpt_payments1").html(),
      report_type: $("#report_type").val(),
      date_type: date_type,
      to_date: $("#rpt_payments2").html(),
      acc_type: $("#select_acc_type").val(),
      id_classfication: $("#id_classifications").val(),
      mode: $("#mode_select").val(),
      id_scheme: $("#scheme_select").val(),
      pay_mode: $("#select_pay_mode").val(),
      id_branch:
        $("#branch_filter").val() != "" &&
          $("#branch_filter").val() != undefined
          ? $("#branch_filter").val()
          : $("#id_branch").val(),
      id_employee: $("#employee_select").val(),
      metal_date_type: $('#metal_date_type').val()
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      offHTML = "";
      onHTML = "";
      adminHTML = "";
      var off_tot_amt = 0;
      var on_tot_amt = 0;
      var admin_tot_amt = 0;
      var offline =
        data.offline_total != null && data.offline_total != undefined
          ? parseFloat(data.offline_total)
          : "0";
      var online =
        data.online_total != null && data.online_total != undefined
          ? parseFloat(data.online_total)
          : "0";
      var admin_app =
        data.admin_app_total != null && data.admin_app_total != undefined
          ? parseFloat(data.admin_app_total)
          : "0";
      var summary_total_amt = parseFloat(
        parseFloat(offline) + parseFloat(online) + parseFloat(admin_app)
      );
      $("#summary_total_amt").html(formatCurrency.format(summary_total_amt));
      $("#print_source_summary").css("display", "block");
      //offline
      if (data.offline_total == null) {
        //$("#print_source_summary").css('display','none');
        offHTML +=
          '<div class="col-md-12" style="text-align:center;color:red;"><strong><span>No data available</span></strong></div>';
        $("#offline_modewise").html(offHTML);
      } else {
        //  $("#print_source_summary").css('display','block');
        $.each(data.mode_wise.offline, function (key, val) {
          if (val.mode_name == null) {
            var mode = val.payment_mode;
          } else {
            var mode = val.mode_name;
          }
          if (mode != null && val.offline_amt != null) {
            offHTML +=
              ' <div class="row">' +
              '<div class="col-md-6" style="text-align: left;">' +
              mode +
              "</div>" +
              '<div class="col-md-2"><lable> : </lable></div>' +
              '<div class="col-md-4" style="text-align: right;"><strong><span>' +
              formatCurrency.format(val.offline_amt) +
              "</span></strong></div>" +
              "</div> ";
          }
        });
        offHTML +=
          '<div class="row"></div><hr>' +
          '<div class="row">' +
          '<div class="col-md-6" style="text-align: left;"> <strong>Total Payment</strong></div>' +
          '<div class="col-md-2"><lable> : </lable></div>' +
          '<div class="col-md-4" style="text-align: right;"><strong><span>' +
          formatCurrency.format(data.offline_total) +
          "</span></strong></div>" +
          "</div>";
        $("#offline_modewise").html(offHTML);
      }
      // online
      if (data.online_total == null) {
        onHTML +=
          '<div class="col-md-12" style="text-align:center;color:red;"><strong><span>No data available</span></strong></div>';
        $("#online_modewise").html(onHTML);
      } else {
        $.each(data.mode_wise.online, function (key, val) {
          if (val.mode_name == null) {
            var mode = val.payment_mode;
          } else {
            var mode = val.mode_name;
          }
          if (mode != null && val.online_amt != null) {
            onHTML +=
              ' <div class="row">' +
              '<div class="col-md-6" style="text-align: left;">' +
              mode +
              "</div>" +
              '<div class="col-md-2"><lable> : </lable></div>' +
              '<div class="col-md-4" style="text-align: right;"><strong><span>' +
              formatCurrency.format(val.online_amt) +
              "</span></strong></div>" +
              "</div> ";
          }
        });
        onHTML +=
          '<div class="row"></div><hr>' +
          '<div class="row">' +
          '<div class="col-md-6" style="text-align: left;"> <strong>Total Payment</strong></div>' +
          '<div class="col-md-2"><lable> : </lable></div>' +
          '<div class="col-md-4" style="text-align: right;"><strong><span>' +
          formatCurrency.format(data.online_total) +
          "</span></strong></div>" +
          "</div>";
        $("#online_modewise").html(onHTML);
      }
      // admin_app
      if (data.admin_app_total == null) {
        adminHTML +=
          '<div class="col-md-12" style="text-align:center;color:red;"><strong><span>No data available</span></strong></div>';
        $("#adminApp_modewise").html(adminHTML);
      } else {
        $.each(data.mode_wise.admin_app, function (key, val) {
          if (val.mode_name == null) {
            var mode = val.payment_mode;
          } else {
            var mode = val.mode_name;
          }
          if (mode != null && val.admin_app_amt != null) {
            adminHTML +=
              ' <div class="row">' +
              '<div class="col-md-6" style="text-align: left;">' +
              mode +
              "</div>" +
              '<div class="col-md-2"><lable> : </lable></div>' +
              '<div class="col-md-4" style="text-align: right;"><strong><span>' +
              formatCurrency.format(val.admin_app_amt) +
              "</span></strong></div>" +
              "</div> ";
          }
        });
        adminHTML +=
          '<div class="row"></div><hr>' +
          '<div class="row">' +
          '<div class="col-md-6" style="text-align: left;"> <strong>Total Payment</strong></div>' +
          '<div class="col-md-2"><lable> : </lable></div>' +
          '<div class="col-md-4" style="text-align: right;"><strong><span>' +
          formatCurrency.format(data.admin_app_total) +
          "</span></strong></div>" +
          "</div>";
        $("#adminApp_modewise").html(adminHTML);
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
$('#search_payment_list').on('click', function () {
  getPaymentDateRangeList();
  getPaymentSummary();
});
$('#print_source_summary').on('click', function () {
  printSourceSummary();
});
/*function set_schemewise_table(data_array)
{
        var company_name=$('#company_name').val();
         var company_code=$('#company_code').val();
         var company_address1=$('#company_address1').val();
         var company_address2=$('#company_address2').val();
         var company_city=$('#company_city').val();
         var pincode=$('#pincode').val();
         var company_email=$('#company_email').val();
         var company_gst_number=$('#company_gst_number').val();
         var phone=$('#phone').val();
         var date1=$('#rpt_payments1').html();
         var date2=$('#rpt_payments2').html();
         var grand_tot_weight=0;
        var grand_tot_quantity=0;
        var select_date="<div style='text-align: center;'><b><span style='font-size:12pt;'>"+company_code+"</span></b></br>"
        +"<span style='font-size:11pt;'>"+company_address1+"</span></br>"
        +"<span style='font-size:11pt;'>"+company_address2 + company_city+"-"+pincode+"</span></br>"
        +"<span style='font-size:11pt;'>GSTIN:"+company_gst_number +", EMAIL:"+ company_email+"</span></br>"
        +"<span style='font-size:11pt;'>Contact :"+phone +"</span></br>"
        +"<b><span style='font-size:15pt;'>Scheme Wise Report</span></b></br>"+"<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;From Date&nbsp;:&nbsp;"+date1+" &nbsp;&nbsp;To Date&nbsp;:&nbsp;"+date2+"</span><br>"
        +"<span style=font-size:11pt;>Print Taken On : "+moment().format("dddd, MMMM Do YYYY, h:mm:ss a")
        +"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
        +"<span style=font-size:11pt;>Print Taken By : "+$('.hidden-xs').html()+"</span></div>" ;
   var oTable = $('#report_payment_daterange').DataTable();
  oTable.clear().draw();
  oTable = $('#report_payment_daterange').dataTable
  ({
    "bDestroy": true,
    "bInfo": true,
    "bFilter": true,
    "bSort": true,
     "dom": 'lBfrtip',
     "columnDefs": [
      {
        targets: [0,1],
        className: 'dt-left'
      },
      {
        targets: [2,3,4],
        className: 'dt-right'
      }
    ],
       "buttons" : [{
      extend: 'print',
      footer: true,
      title:'',
      messageTop:select_date,
      orientation: 'landscape',
      customize: function ( win ) {
                      $(win.document.body).find('table')
                                  .addClass('compact');
                    $(win.document.body).find( 'table' )
                    .addClass('compact')
                    .css('font-size','10px')
                    .css('font-family','sans-serif');
                    },
      },
      {
      extend:'excel',
      footer: true,
        title: 'Scheme Wise Report '+$('#rpt_payments1').html()+' - '+$('#rpt_payments2').html(),
      }],
    "aaData": data_array,
    "order": [[ 0, "asc" ]],
    "aoColumns": [
      { "mDataProp": "sno" },
      { "mDataProp": "scheme_name" },
      { "mDataProp": "count" },
      { "mDataProp": "total_weight" },
      //{ "mDataProp": "total_amount" },
      { "mDataProp": function ( row, type, val, meta )
      {
        return(formatCurrency.format(row.total_amount));
      }
      },
      {
        "mDataProp": null,
        "sClass": "control center",
        "sDefaultContent": '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
      },
    ] ,
    "footerCallback": function( row, data, start, end, display )
      {
        if(data.length>0)
        {
          var length=0;
          length=data.length;
          //alert(length);
          var api = this.api(), data;
          for( var i=0; i<=data.length-1;i++)
          {
            var intVal = function ( i ) 
            {
                    return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                        i : 0;   
            };
            total_count = api
              .column(2,{ page: 'current'})
              .data()
              .reduce( function (a, b) {
                return intVal(a) + intVal(b);
              }, 0 );
            total_weight = api
              .column(3,{ page: 'current'})
              .data()
              .reduce( function (a, b) {
                return intVal(a) + intVal(b);
              }, 0 );
            total_amount = api
              .column(4,{ page: 'current'})
              .data()
              .reduce( function (a, b) {
                return intVal(a) + intVal(b);
              }, 0 );
            $( api.column(1).footer() ).html('Grand Total');
            $( api.column(2).footer() ).html(total_count);
            $( api.column(3).footer() ).html(total_weight+"gm");
            $( api.column(4).footer() ).html(formatCurrency.format(total_amount));
          }
        }
        else
        {
          var data=0;
          var api = this.api(), data;
           $( api.column(0).footer() ).html("");
           $( api.column(1).footer() ).html("");
           $( api.column(2).footer() ).html("");
           $( api.column(3).footer() ).html("");
           $( api.column(4).footer() ).html("");
           $( api.column(5).footer() ).html("");
        }
      }
   }); 
   var anOpen =[]; 
            $(document).on('click',"#report_payment_daterange .control", function(){ 
               var nTr = this.parentNode;
               var i = $.inArray( nTr, anOpen );
               if ( i === -1 ) { 
                $('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');
                 var oData = oTable.fnGetData( nTr ); 
                 var scheme_detail_list  = oData.scheme_list;
                 if(scheme_detail_list.length>0){ 								
                  oTable.fnOpen( nTr, fnShowSchemeDetails(oTable, nTr), 'details' );
                  anOpen.push( nTr ); 
                 }
                 else { 
                  $('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
                  oTable.fnClose( nTr );
                  anOpen.splice( i, 1 );
                }
              }
              else { 
                $('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
                oTable.fnClose( nTr );
                anOpen.splice( i, 1 );
              }
            } );
} */
function fnShowSchemeDetails(oTable, nTr) {
  //alert();
  var oData = oTable.fnGetData(nTr);
  console.log(oData);
  var rowDetail = '';
  var schemeTable =
    '<div class="innerDetails">' +
    '<table class="table table-responsive table-bordered text-center table-sm">' +
    '<tr class="bg-teal">' +
    '<th width="1%">S.No</th>' +
    '<th width="1%">Mobile</th>' +
    '<th width="1%">Recpt.No</th>' +
    '<th width="1%">Acc Name</th>' +
    '<th width="1%">Pay.Date</th>' +
    '<th width="5%">Mode</th>' +
    '<th width="5%">M.Rate</th>' +
    '<th width="1%">Ins</th>' +
    '<th width="5%">M.weight</th>' +
    '<th width="5%">Received.Amt</th>' +
    '<th width="5%">Cost Center</th>' +
    '<th width="5%">Paid Through</th>' +
    '</tr>';
  var scheme_details = oData.scheme_list;
  //alert();
  var paid_amount = 0;
  var bonus_amount = 0;
  var metal_weight = 0;
  console.log(scheme_details);
  $.each(scheme_details, function (key, items) {
    if (items.pay_branch == null || items.pay_branch == '') {
      items.pay_branch = '-';
    }
    paid_amount += parseFloat(parseFloat(items.payment_amount) - parseFloat(items.discountAmt));
    bonus_amount += parseFloat(items.discountAmt);
    metal_weight += parseFloat(items.metal_weight);
    var sales_ledger = base_url + 'index.php/admin_ret_reports/customer_history/list/' + items.mobile;
    var acc_ledger = base_url + 'index.php/reports/payment/account/' + items.id_scheme_account;
    var receipt_url = base_url + 'index.php/payment/invoice/' + items.id_payment + '/' + items.id_scheme_account;
    schemeTable += '<tr>' +
      '<td>' + parseFloat(key + 1) + '</td>' +
      '<td><input type="hidden" class="mobile" value="' + items.mobile + '"><a href=' + sales_ledger + ' target="_blank">' + items.mobile + '</td>' +
      //'<td><input type="hidden" class="mobile" value="'+items.mobile+'">'+items.mobile+'</td>'+
      '<td><a href=' + receipt_url + ' target="_blank">' + items.receipt_no + '</td>' +
      '<td>' + items.name + '</td>' +
      '<td>' + items.date_payment + '</td>' +
      '<td>' + items.payment_mode + '</td>' +
      '<td>' + items.metal_rate + '</td>' +
      '<td>' + items.paid_installments + '</td>' +
      '<td>' + items.metal_weight + '</td>' +
      '<td>' + formatCurrency.format(parseFloat(parseFloat(items.payment_amount) - parseFloat(items.discountAmt)).toFixed(2)) + '</td>' +
      '<td>' + items.pay_branch + '</td>' +
      '<td>' + items.payment_through + '</td>' +
      '</tr>';
  });
  rowDetail = schemeTable + '</table></div>';
  return rowDetail;
}
function get_payModeList() {
  $(".overlay").css('display', 'block');
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/admin_reports/ajax_getPayModeList',
    dataType: 'json',
    success: function (data) {
      var mode_val = $('#id_pay_mode').val();
      $.each(data, function (key, item) {
        $('#mode_select').append(
          $("<option></option>")
            .attr("value", item.short_code)
            .text(item.mode_name)
        );
      });
      $("#mode_select").select2({
        placeholder: "Select Mode Name",
        allowClear: true
      });
      $("#mode_select").select2("val", (mode_val != '' && mode_val > 0 ? mode_val : ''));
      $(".overlay").css("display", "none");
    }
  });
}
// Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report   --> END
$("#select_pay_mode").select2({
  allowClear: true
});
$("#select_acc_type").select2({
  allowClear: true
});
$("#report_type").select2({
  allowClear: true
});
//gift issued report -- start
function getGiftIssuedList() {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/ajax_gift_report?nocache=" +
      my_Date.getUTCSeconds(),
    // data: ({'from_date':$('#rpt_payments1').html(),'to_date' :$('#rpt_payments2').html(),'id_scheme':$('#scheme_select').val(),'id_metal':$('#metal_select').val(),'id_gift':$('#gift_list').val(),'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#id_branch").val())}),
    data: {
      from_date: $("#rpt_payments1").html(),
      to_date: $("#rpt_payments2").html(),
      gift_status: $("#gift_status").val(),
      report_type: $("#issue_group_by").val(),
      id_scheme: $("#scheme_select").val(),
      id_metal: $("#metal_select").val(),
      id_gift: $("#gift_list").val(),
      id_employee: $("#employee_select").val(),
      id_branch:
        $("#branch_filter").val() != "" &&
          $("#branch_filter").val() != undefined
          ? $("#branch_filter").val()
          : $("#branch_select").val(),
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      set_gift_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
// function set_gift_list(data) {
// 	var gift_summary = get_gift_summary(data);
// 	$("#gift_summary").html(gift_summary);
// 	$("div.overlay").css("display", "none");
// 	$("#gift_report_list > tbody > tr").remove();
// 	$('#gift_report_list').dataTable().fnClearTable();
// 	$('#gift_report_list').dataTable().fnDestroy();
// 	var count = 0;
// 	var fdate = new Date($('#rpt_payments1').html());
// 	var date1 = $('#rpt_payments1').html();
// 	var date2 = $('#rpt_payments2').html();
// 	var tdate = new Date($('#rpt_payments2').html());
// 	$("#gift_report_daterange").text(date1 + " To " + date2);
// 	var branch_name = getBranchTitle();
// 	var select_date = get_title(date1, date2, "Gift Report - " + branch_name);
// 	var grand_tot_weight = 0;
// 	var grand_tot_quantity = 0;
// 	var sum_quantity = 0;
// 	var sum_weight = 0;
// 	var weight = 0;
// 	var quantity = 0;
// 	var sumHTML = '';
// 	var total_issued_count = 0;
// 	var total_net_weight = 0;
// 	trHTML = '';
// 	var account_no;
// 	console.log(data);
// 	var gift;
// 	var selected_grouping = $("#group_by_select").val();
// 	if (selected_grouping == 2) {
// 		select_date += '<h4 class="text-center">Gift Wise Summary</h4><br/>';
// 		$(".summary_description").text(" - Gift Wise Summary");
// 		gift = data.gift['gift_wise_data'];
// 	}
// 	else if (selected_grouping == 3) {
// 		select_date += '<h4 class="text-center">Scheme wise Gift Wise Summary</h4><br/>';
// 		//var gift_array=data.gift['scheme_gift_wise_data'];
// 		$(".summary_description").text(" - Scheme Wise Gift wise Summary");
// 		setSchemeAndGiftWiseReport(data);
// 	}
// 	else {
// 		select_date += '<h4 class="text-center">Scheme Wise Summary</h4><br/>';
// 		$(".summary_description").text(" - Scheme Wise Summary");
// 		gift = data.gift['scheme_wise_data'];
// 	}
// 	if (selected_grouping != 3) {
// 		$.each(gift, function (key, gifts) {
// 			trHTML += '<tr>' +
// 				'<td colspan="3" style="text-align:left;"><strong>' + key + '</strong></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'</tr>';
// 			sum_quantity = 0;
// 			sum_weight = 0;
// 			$.each(gifts, function (key, items) {
// 				//account_no line Added by Durga 13.06.2023 to get account number based on account number settings
// 				//	account_no=getSchAccNumber_Format(items.is_lucky_draw,items.scheme_group_code,items.scheme_acc_number,items.schemeaccNo_displayFrmt,items.scheme_wise_acc_no,items.acc_branch,items.start_year,items.code);
// 				count++;
// 				// for sub total
// 				sum_quantity += parseFloat(items.quantity);
// 				sum_weight += parseFloat(items.tot_weight);
// 				quantity += parseFloat(items.quantity);
// 				weight += parseFloat(items.tot_weight);
// 				trHTML += '<tr>' +
// 					'<td style="text-align:left;">' + parseFloat(key + 1) + '</td>' +
// 					'<td style="text-align:left;">' + items.code + '</td>' +
// 					'<td style="text-align:left;">' + items.cus_name + '</td>' +
// 					'<td style="text-align:left;">' + items.mobile + '</td>' +
// 					//account number line commented and replaced by Durga 13.06.2023
// 					'<td style="text-align:left;">' + items.scheme_acc_number + '</td>' +
// 					//'<td style="text-align:left;">'+account_no+'</td>'+
// 					'<td style="text-align:left;">' + items.joined_date + '</td>' +
// 					'<td style="text-align:right;">' + items.paid_installment + '</td>' +
// 					'<td style="text-align:left;">' + items.issued_date + '</td>' +
// 					'<td style="text-align:left;">' + (items.gift_id_emp_name != "" && items.gift_id_emp_name != null ? items.gift_id_emp_name : "-") + '</td>' +
// 					'<td style="text-align:left;">' + items.pay_emp_name + '</td>' +
// 					'<td style="text-align:right;">' + indianCurrency.format(items.payment_amount) + '</td>' +
// 					'<td style="text-align:left;">' + items.gift_desc + '</td>' +
// 					'<td style="text-align:right;">' + (items.quantity != 0 && items.quantity != "" && items.quantity != null ? items.quantity : "") + '</td>' +
// 					// '<td style="text-align:right;">'+items.tot_weight+'</td>'+
// 					'<td style="text-align:right;">' + (items.tot_weight != 0 && items.tot_weight != null ? parseFloat(items.tot_weight).toFixed(3) : "") + '</td>' +
// 					'<td style="text-align:left;">' + items.barcode + '</td>' +
// 					'<td style="text-align:left;">' + items.status + '</td>' +
// 					'</tr>';
// 			});
// 			trHTML += '<tr>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'<td style="text-align:left; font-weight: bold;">Sub Total </td>' +
// 				'<td style="text-align:right; font-weight: bold;">' + sum_quantity + '</td>' +
// 				// '<td style="text-align:right; font-weight: bold;">'+sum_weight+' grms</td>'+
// 				'<td style="text-align:right; font-weight: bold;">' + (sum_weight != 0 ? parseFloat(sum_weight).toFixed(3) : "") + '</td>' +
// 				'<td></td>' +
// 				'<td></td>' +
// 				'</tr>';
// 		});
// 		grand_tot_weight += parseFloat(weight);
// 		grand_tot_quantity += parseFloat(quantity);
// 		trHTML += '<tr>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'<td style="text-align:left; font-weight: bold;">Grand Total </td>' +
// 			'<td style="text-align:right; font-weight: bold;">' + grand_tot_quantity + '</td>' +
// 			// '<td style="text-align:right; font-weight: bold;">'+grand_tot_weight+' grms</td>'+
// 			'<td style="text-align:right; font-weight: bold;">' + (grand_tot_weight != 0 ? parseFloat(grand_tot_weight).toFixed(3) : "") + '</td>' +
// 			'<td></td>' +
// 			'<td></td>' +
// 			'</tr>';
// 		select_date += gift_summary;
// 		$('#gift_report_list > tbody').html(trHTML);
// 		$("#total_gift").text(count);
// 		if (!$.fn.DataTable.isDataTable('#gift_report_list')) {
// 			//if(data.gift.length !== 0)
// 			if (count !== 0) {
// 				oTable = $('#gift_report_list').dataTable({
// 					"bSort": false,
// 					"bInfo": false,
// 					"scrollX": '100%',
// 					"dom": 'lBfrtip',
// 					"pageLength": 25,
// 					"lengthMenu": [[-1, 25, 50, 100, 250], ["All", 25, 50, 100, 250]],
// 					"buttons": [
// 						{
// 							extend: 'print',
// 							footer: true,
// 							title: '',
// 							messageTop: select_date,
// 							orientation: 'landscape',
// 							customize: function (win) {
// 								$(win.document.body).find('table')
// 									.addClass('compact');
// 								$(win.document.body).find('table')
// 									.addClass('compact')
// 									.css('font-size', '10px')
// 									.css('font-family', 'sans-serif');
// 							},
// 						},
// 						{
// 							extend: 'excel',
// 							footer: true,
// 							title: 'Gift Issued Report ' + $('#rpt_payments1').html() + ' - ' + $('#rpt_payments2').html(),
// 						}
// 					],
// 					"columnDefs":
// 						[
// 							{
// 								targets: [0, 1, 2, 3, 4, 5, 7, 8, 9, 11, 14, 15],
// 								className: 'dt-left'
// 							},
// 							{
// 								targets: [6, 10, 12, 13],
// 								className: 'dt-right'
// 							},
// 						],
// 				});
// 			}
// 		}
// 	}
// }
function set_gift_list(data) {
  var gift = data.gift;
  var gift_summary = data.summary;
  $("div.overlay").css("display", "none");
  $("#gift_report_list > tbody > tr").remove();
  $('#gift_report_list').dataTable().fnClearTable();
  $('#gift_report_list').dataTable().fnDestroy();
  var count = 0;
  var fdate = new Date($('#rpt_payments1').html());
  var date1 = $('#rpt_payments1').html();
  var date2 = $('#rpt_payments2').html();
  var tdate = new Date($('#rpt_payments2').html());
  //var date1=fdate.getDate()+'.'+ (fdate.getMonth() + 1) + '.' +  fdate.getFullYear()
  //var date2=tdate.getDate()+'.'+ (tdate.getMonth() + 1) + '.' +  tdate.getFullYear()
  $("#from_date_selected").text(date1);
  $("#to_date_selected").text(" - " + date2);
  var company_name = $('#company_name').val();
  var company_code = $('#company_code').val();
  var company_code = $('#company_code').val();
  var company_address1 = $('#company_address1').val();
  var company_address2 = $('#company_address2').val();
  var company_city = $('#company_city').val();
  var pincode = $('#pincode').val();
  var company_email = $('#company_email').val();
  var company_gst_number = $('#company_gst_number').val();
  var phone = $('#phone').val();
  var grand_tot_weight = 0;
  var grand_tot_quantity = 0;
  var sum_quantity = 0;
  var sum_weight = 0;
  var weight = 0;
  var quantity = 0;
  var select_date = "<div style='text-align: center;'><b><span style='font-size:12pt;'>" + company_code + "</span></b></br>"
    + "<span style='font-size:11pt;'>" + company_address1 + "</span></br>"
    + "<span style='font-size:11pt;'>" + company_address2 + company_city + "-" + pincode + "</span></br>"
    + "<span style='font-size:11pt;'>GSTIN:" + company_gst_number + ", EMAIL:" + company_email + "</span></br>"
    + "<span style='font-size:11pt;'>Contact :" + phone + "</span></br>"
    + "<b><span style='font-size:15pt;'>Gift Issued report</span></b></br>" + "<span style=font-size:13pt;>Transaction Details &nbsp;&nbsp;From Date&nbsp;:&nbsp;" + date1 + " &nbsp;&nbsp;To Date&nbsp;:&nbsp;" + date2 + "</span><br>"
    + "<span style=font-size:11pt;>Print Taken On : " + moment().format("dddd, MMMM Do YYYY, h:mm:ss a")
    + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
    + "<span style=font-size:11pt;>Print Taken By : " + $('.hidden-xs').html() + "</span></div>";
  var sumHTML = '';
  var total_issued_count = 0;
  var total_net_weight = 0;
  // $.each(gift_summary,function(key,giftsum)
  // {
  // var rows = giftsum.length;
  // sumHTML+='<tr>'+
  // '<td style="text-align:left;"><strong>'+key+'</strong></td>'+
  // '<td></td>'+
  // '<td></td>'+
  // '</tr>';
  // 		$.each(giftsum,function(key,items){
  // sumHTML+= '<tr>'+
  // '<td>'+items.gift_name+'</td>'+
  // '<td>'+items.issued_count+'</td>'+
  // '<td>'+items.total_weight+'</td>'+
  // '</tr>';
  // total_issued_count+=parseFloat(items.issued_count);
  // total_net_weight+=parseFloat(items.total_weight);
  // 		});
  // });
  // sumHTML+='<tr>'+
  // '<td><strong>GRAND TOTAL</strong></td>'+
  // '<td><strong>'+total_issued_count+'</strong></td>'+
  // '<td><strong>'+parseFloat(total_net_weight).toFixed(3)+'</strong></td>'+
  // '</tr>';
  // $('#gift_summary > tbody').html(sumHTML);  
  trHTML = '';
  var account_no;
  $.each(gift, function (key, gifts) {
    trHTML += '<tr>' +
      '<td colspan="2" style="text-align:left;"><strong>' + key + '</strong></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      // '<td></td>'+
      '</tr>';
    sum_quantity = 0;
    sum_weight = 0;
    $.each(gifts, function (key, items) {
      //account_no line Added by Durga 13.06.2023 to get account number based on account number settings
      //	account_no=getSchAccNumber_Format(items.is_lucky_draw,items.scheme_group_code,items.scheme_acc_number,items.schemeaccNo_displayFrmt,items.scheme_wise_acc_no,items.acc_branch,items.start_year,items.code);
      count++;
      // for sub total
      sum_quantity += parseFloat(items.quantity);
      sum_weight += parseFloat(items.tot_weight);
      quantity += parseFloat(items.quantity);
      weight += parseFloat(items.tot_weight);
      trHTML += '<tr>' +
        '<td>' + parseFloat(key + 1) + '</td>' +
        '<td >' + items.cus_name + '</td>' +
        '<td >' + items.mobile + '</td>' +
        //account number line commented and replaced by Durga 13.06.2023
        '<td >' + items.scheme_acc_number + '</td>' +
        //'<td style="text-align:left;">'+account_no+'</td>'+
        '<td>' + items.joined_date + '</td>' +
        '<td>' + items.joined_branch_name + '</td>' +
        '<td >' + items.paid_installment + '</td>' +
        '<td>' + items.issued_date + '</td>' +
        '<td >' + items.gift_id_emp_name + '</td>' +
        '<td >' + items.referred_by + '</td>' +
        '<td >' + formatCurrency.format(items.payment_amount) + '</td>' +
        '<td>' + items.gift_desc + '</td>' +
        '<td >' + items.quantity + '</td>' +
        // '<td style="text-align:right;">'+items.tot_weight+'</td>'+
        //	'<td style="text-align:right;">'+parseFloat(items.tot_weight).toFixed(2)+'</td>'+
        '<td >' + items.status + '</td>' +
        '<td >' + items.deducted_date + '</td>' +
        '<td >' + items.deducted_by + '</td>' +
        '<td >' + items.deduct_remark + '</td>' +
        '</tr>';
    });
    trHTML += '<tr>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td style="text-align:left; font-weight: bold;">Sub Total </td>' +
      '<td style="text-align:right; font-weight: bold;">' + sum_quantity + '</td>' +
      // '<td style="text-align:right; font-weight: bold;">'+sum_weight+' grms</td>'+
      //	'<td style="text-align:right; font-weight: bold;">'+parseFloat(sum_weight).toFixed(2)+'</td>'+
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '<td></td>' +
      '</tr>';
  });
  grand_tot_weight += parseFloat(weight);
  grand_tot_quantity += parseFloat(quantity);
  trHTML += '<tr>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td style="text-align:left; font-weight: bold;">Grand Total </td>' +
    '<td style="text-align:right; font-weight: bold;">' + grand_tot_quantity + '</td>' +
    // '<td style="text-align:right; font-weight: bold;">'+grand_tot_weight+' grms</td>'+
    //'<td style="text-align:right; font-weight: bold;">'+parseFloat(grand_tot_weight).toFixed(2)+'</td>'+
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '<td></td>' +
    '</tr>';
  $('#gift_report_list > tbody').html(trHTML);
  $("#total_gift").text(count);
  var title = "";
  title += get_title($('#rpt_payments1').html(), $('#rpt_payments2').html(), 'Gift Report');
  var excel_title = "";
  excel_title += get_excel_title('', '', 'Gift Report');
  if (!$.fn.DataTable.isDataTable('#gift_report_list')) {
    oTable = $('#gift_report_list').dataTable({
      "bSort": false,
      "footer": true, // Enable footer
      "bInfo": false,
      "scrollX": '100%',
      "dom": 'lBfrtip',
      "lengthMenu": [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
      "buttons": [
        {
          extend: 'print',
          footer: true,
          //    title: 'Gift Report  '+$('#giftreportrange').html(),
          messageTop: title,
          //   title: tableHtml,
          orientation: 'landscape',
          customize: function (win) {
            $(win.document.body).find('table')
              .addClass('compact');
            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', '10px')
              .css('font-family', 'sans-serif');
          },
          exportOptions: { columns: ':visible' },
        },
        {
          extend: 'excel',
          footer: true,
          title: 'Gift Issued Report',
          messageTop: excel_title,
        },
        { extend: 'colvis', collectionLayout: 'fixed columns', collectionTitle: 'Column visibility control' },
      ],
      "columnDefs":
        [{
          targets: [0, 1, 2, 3, 4, 5, 7, 8, 9, 13, 14, 15, 16],
          className: 'dt-left'
        },
        {
          targets: [6, 10, 12],
          className: 'dt-right'
        },
        { "width": "120px", "targets": 1 },
        ],
    });
  }
}
function get_gift_summary(data) {
  var group_by = $("#group_by_select").val();
  var giftHtml = '';
  var gift_data;
  var items_count;
  var sub_count;
  var grand_count;
  var item_name;
  if (group_by == 2) {
    giftHtml += '<table style="width:500px;background: #ecf0f5;">' +
      '<thead><tr>' +
      '<th style="text-align:left;padding-left:30px;">Gift Name</th>' +
      '<th style="text-align:right;padding-right:30px;">Scheme Count</th>' +
      '</tr><thead>';
    gift_data = data.gift['gift_scheme_wise_data_summary'];
    giftHtml += '<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>';
  }
  else {
    giftHtml += '<table style="width:500px;background: #ecf0f5;">' +
      '<thead><tr>' +
      '<th style="text-align:left;padding-left:30px;">Scheme Code</th>' +
      '<th style="text-align:right;padding-right:30px;">Gift Count</th>' +
      '</tr><thead>';
    gift_data = data.gift['scheme_gift_wise_data_summary'];
    //console.log(gift_data);
    giftHtml += '<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>';
  }
  grand_count = 0;
  $.each(gift_data, function (key, items) {
    giftHtml += '<tbody><tr>';
    giftHtml += '<td style="text-align:left;padding-left:30px;"><strong>' + key + '</strong></td>' +
      '<td style="text-align:left;padding-left:30px;"></td>' +
      '</tr>';
    sub_count = 0;
    $.each(items, function (detail_title, details) {
      details_count = details.length;
      sub_count += parseInt(details_count);
      grand_count += parseInt(details_count);
      giftHtml += '<tr><td style="text-align:left;padding-left:50px;">' + detail_title + '</td>' +
        '<td style="text-align:right;padding-right:30px;">' + details_count + '</td></tr>'
    });
    giftHtml += '<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>';
    giftHtml += '<tr><td class="highlighted-row" style="text-align:left;padding-left:50px;font-weight:bold;">Sub Total</td>' +
      '<td class="highlighted-row" style="text-align:right;padding-right:30px;font-weight:bold;">' + sub_count + '</td></tr>';
    giftHtml += '<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>';
  });
  giftHtml += '<tr><td style="text-align:left;padding-left:50px;padding-top:3px;font-weight:bold;">Total</td>' +
    '<td style="text-align:right;padding-right:30px;font-weight:bold;padding-top:3px;">' + grand_count + '</td></tr>';
  giftHtml += '</tbody></table><br/>';
  var styleHtml = '<style>td.highlighted-row {' +
    'padding-right:30px;' +
    'border-top: 1px dashed black;' +
    'border-bottom: 1px dashed black;' +
    '}</style>';
  giftHtml += styleHtml;
  return giftHtml;
}
$('#search_gift_list').on('click', function () {
  getGiftIssuedList();
});
function get_gift_names() {
  $('#gift_list').empty();
  var branch = $("#branch_select").val();
  $.ajax({
    type: 'POST',
    url: base_url + 'index.php/admin_manage/loadGiftData',
    dataType: 'json',
    data: { 'id_branch': $("#branch_select").val() },
    success: function (data) {
      console.log(data);
      var gift_issued = $('#gift_list').val();
      $('#gift_list').append(
        $("<option></option>")
          .attr("value", 0)
          .text('All')
      );
      $.each(data, function (key, item) {
        $('#gift_list').append(
          $("<option></option>")
            .attr("value", item.id_gift)
            .text(item.gift_name)
        );
      });
      $("#gift_list").select2({
        placeholder: "Select Gift",
        allowClear: true
      });
      $("#gift_list").select2("val", (gift_issued != '' && gift_issued > 0 ? gift_issued : ''));
      $(".overlay").css("display", "none");
    }
  });
}
function get_group_by_type() {
  $('#group_by_select').append(
    $("<option></option>")
      .attr("value", 1)
      .text("Scheme")
  );
  $('#group_by_select').append(
    $("<option></option>")
      .attr("value", 2)
      .text("Gift")
  );
  $('#group_by_select').append(
    $("<option></option>")
      .attr("value", 3)
      .text("Scheme/Gift")
  );
  $('#group_by_select').select2("val", 1);
}
$("#group_by_select").select2({
  allowClear: true,
  placeholder: "Select Grouping Type"
});
//gift issued report ends
function get_paymentRemarks(from_date, to_date, id_emp) {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date } : ''), //hh
    url: base_url + 'index.php/admin_manage/getRemarkPayments',
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      set_paymentRemarks(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function set_paymentRemarks(data) {
  var payment = data;
  from = $("#rpt_payments1").text();
  to = $("#rpt_payments2").text();
  var filename = "<b><span style='font-size:15pt;'> Pending Collection Remarks | Admin</span></b></br>" + "<span style=font-size:13pt;>&nbsp;Selected Date&nbsp;:&nbsp;" + from + "</span><span style=font-size:13pt;>&nbsp;-&nbsp;" + to + "</span>";
  var oTable = $('#pending_remarks_list').DataTable();
  oTable.clear().draw();
  if (payment != null) {
    oTable = $('#pending_remarks_list').dataTable({
      "bDestroy": true,
      "bInfo": true,
      "bFilter": true,
      "bSort": true,
      "dom": 'lBfrtip',
      "lengthMenu": [
        [10, 25, 50, -1],
        ['10 rows', '25 rows', '50 rows', 'Show all']
      ],
      "buttons": [
        {
          extend: 'print',
          footer: true,
          title: filename,
          orientation: 'landscape',
          customize: function (win) {
            $(win.document.body).find('table')
              .addClass('compact');
            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', '10px')
              .css('font-family', 'sans-serif');
          },
        },
        {
          extend: 'excel',
          footer: true,
          title: filename,
        }
      ],
      "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
      "aaData": payment,
      "aoColumns": [
        { "mDataProp": "id_employee" },
        { "mDataProp": "date_created" },
        { "mDataProp": "employee_name" },
        { "mDataProp": "name" },
        { "mDataProp": "mobile" },
        { "mDataProp": "id_scheme_account" },
        { "mDataProp": "scheme_acc_number" },
        { "mDataProp": "remark" },
      ]
    });
  }
}
function show_scheme_details(key) {
  $('#' + key).toggle();
  $('#' + key + '_icon').toggleClass("fa fa-plus fa fa-minus");
  $('.' + key).toggle();
  $('.' + key + '_icon').toggleClass("fa fa-plus fa fa-minus");
}
/*OutStanding Report function starts*/
if (ctrl_page[1] == "scheme_customer_daterange") {
  $("#search_scheme_list").on("click", function () {
    selectedValue = $('input[name="tableview"]:checked').val();
    if (selectedValue == 2) {
      var branch_select = $("#branch_select").val();
      var scheme_select = $("#scheme_select").val();
      // 		if (branch_select > 0 || scheme_select > 0) {
      getSchemeDateRangeList();
      // 		}
      //$("div.overlay").css("display", "none");
      $("#out_standing_table_div").css("display", "block");
      $("#summary_block").css("display", "none");
    } else {
      outstanding_summary();
      $("#out_standing_table_div").css("display", "none");
      $("#summary_block").css("display", "block");
    }
  });
}
function manual_export(exp_url, post_data) {
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $.ajax({
    url: base_url + "index.php/" + exp_url + "?nocache=" + my_Date.getUTCSeconds(),
    data: post_data,
    //dataType:"JSON",
    type: "POST",
    success: function (data) {
      $("div.overlay").css("display", "none");
      $.toaster({ priority: 'warning', title: data.title, message: data.msg });
    },
    error: function (error) {
      console.log(error);
      $("div.overlay").css("display", "none");
    }
  })
}
function outstanding_summary() {
  console.log($("#datesingle_search").val());
  selectedValue = $('input[name="datepick"]:checked').val();
  console.log(selectedValue);
  console.log(selectedValue);
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/scheme_summary?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      id_scheme: $("#scheme_select").val(),
      id_group: $("#id_group").val(),
      id_branch:
        $("#branch_filter").val() != "" &&
          $("#branch_filter").val() != undefined
          ? $("#branch_filter").val()
          : $("#id_branch").val(),
      ...(selectedValue != 0
        ? {
          singlefilter:
            $("#datesingle_search").val() != undefined
              ? $("#datesingle_search").val()
              : "",
          from_date:
            $("#rpt_payments1").html() != undefined
              ? $("#rpt_payments1").html()
              : "",
          to_date:
            $("#rpt_payments2").html() != undefined
              ? $("#rpt_payments2").html()
              : "",
        }
        : {}),
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var grand_scheme_count = 0;
      var grand_opening_amt = 0;
      var grand_collection_amt = 0;
      var grand_closed_amt = 0;
      var grand_balance_amt = 0;
      var grand_balance_wgt = 0;
      var grand_opening_wgt = 0;
      $("div.overlay").css("display", "none");
      //Scheme wise summary and group wise summary starts here
      var offHTML = "";
      var onHTML = "";
      var scheme_count = 0;
      if (data.scheme_summary == null) {
        offHTML +=
          '<p style="text-align:center;color:red;"><strong><span>No Data Available</span></strong></p>';
        $("#offline_modewise").html(offHTML);
      } else {
        offHTML +=
          '<table class="table"><thead><tr class="report-key">' +
          "<td >Scheme</td>" +
          '<td  style="text-align:right;">Acc Count</td>' +
          '<td  style="text-align:right;">Opening Amt</td>' +
          '<td  style="text-align:right;">Opening Wgt</td>' +
          '<td  class="outstanding-hide-col" style="text-align:right;">Collection Amt</td>' +
          '<td  class="outstanding-hide-col" style="text-align:right;">Closed Amt</td>' +
          '<td  class="outstanding-hide-col" style="text-align:right;">Balance Amt</td>' +
          '<td  class="outstanding-hide-col" style="text-align:right;">Balance Wgt</td>' +
          "</tr></thead><tbody>";
        $.each(data.scheme_summary, function (key, classification) {
          $.each(classification, function (scheme, val) {
            grand_scheme_count += parseInt(val.scheme_count);
            grand_opening_amt += parseFloat(val.opening_amount);
            grand_opening_wgt += parseFloat(val.opening_wgt);
            grand_collection_amt += parseFloat(val.current_collection_amt);
            grand_closed_amt += parseFloat(val.current_closed_amt);
            grand_balance_amt += parseFloat(val.balance_amount);
            grand_balance_wgt += parseFloat(val.balance_weight);
            offHTML +=
              " <tr>" +
              '<td style="text-align:left;">' +
              val.code +
              "</td>" +
              '<td  style="text-align:right;">' +
              parseInt(val.scheme_count) +
              "</td>" +
              '<td style="text-align:right;">' +
              (val.opening_amount != null
                ? formatCurrency.format(
                  parseFloat(val.opening_amount).toFixed(3)
                )
                : 0) +
              "</td>" +
              '<td style="text-align:right;">' +
              (val.opening_amount != null
                // ? formatCurrency.format(parseFloat(val.opening_wgt).toFixed(3))
                ? (formatMetalWeight((val.opening_wgt)))
                : 0) +
              "</td>" +
              '<td  class="outstanding-hide-col" style="text-align:right;">' +
              (val.current_collection_amt != null
                ? formatCurrency.format(
                  parseFloat(val.current_collection_amt).toFixed(2)
                )
                : 0) +
              "</td>" +
              '<td  class="outstanding-hide-col"   style="text-align:right;">' +
              (val.current_closed_amt != null
                ? formatCurrency.format(
                  parseFloat(val.current_closed_amt).toFixed(2)
                )
                : 0) +
              "</td>" +
              '<td class="outstanding-hide-col"  style="text-align:right;">' +
              (val.balance_amount != null
                ? formatCurrency.format(
                  parseFloat(val.balance_amount).toFixed(2)
                )
                : 0) +
              "</td>" +
              '<td  class="outstanding-hide-col" style="text-align:right;"><span>' +
              (val.balance_weight != null
                /* ? formatCurrency.format(
                   parseFloat(val.balance_weight).toFixed(3)
                 )  */
                ? (formatMetalWeight((val.balance_weight)))
                : 0) +
              "</td>" +
              "</tr>";
          });
        });
        offHTML +=
          '<tr class="report-grand-total">' +
          '<td style="text-align:left;">Total </td>' +
          '<td  style="text-align:right;"> ' +
          parseInt(grand_scheme_count) +
          "</td>" +
          '<td  style="text-align:right;"> ' +
          formatCurrency.format(parseFloat(grand_opening_amt).toFixed(2)) +
          " </td>" +
          '<td  style="text-align:right;"> ' +
          // formatCurrency.format(parseFloat(grand_opening_wgt).toFixed(2)) +
          (formatMetalWeight((grand_opening_wgt))) +
          " </td>" +
          '<td class="outstanding-hide-col" style="text-align:right;"> ' +
          formatCurrency.format(parseFloat(grand_collection_amt).toFixed(2)) +
          " </td>" +
          '<td  class="outstanding-hide-col" style="text-align:right;"> ' +
          formatCurrency.format(parseFloat(grand_closed_amt).toFixed(2)) +
          " </td>" +
          '<td   class="outstanding-hide-col" style="text-align:right;"> ' +
          formatCurrency.format(parseFloat(grand_balance_amt).toFixed(2)) +
          " </td>" +
          '<td class="outstanding-hide-col" style="text-align:right;"> ' +
          (grand_balance_wgt != 0
            // ? parseFloat(grand_balance_wgt).toFixed(3)
            ? (formatMetalWeight((grand_balance_wgt)))
            : "") +
          "</td>" +
          "</tr></tbody></table>";
        $("#offline_modewise").html(offHTML);
      }
      //Scheme wise summary and group wise summary ends here
      // console.log(offHTML);
      outstandingSummaryForPrint = offHTML;
      $("#rpt_payments1").html("");
      $("#rpt_payments2").html("");
      if (selectedValue != "0") {
        // If $('#datesingle_search').val() has a value, show the "Collection Amt" element
        $(".outstanding-hide-col").removeClass("outstanding-hide-col");
      } else {
        // If $('#datesingle_search').val() is empty, hide the "Collection Amt" element
        $(".outstanding-hide-col").addClass("outstanding-hide-col");
      }
      return outstandingSummaryForPrint;
    },
  });
}
$("#print_summary").on("click", () => {
  // console.log(outstandingSummaryForPrint);
  const printWindow = window.open("", "_blank");
  var print_outstanding_summary;
  var from_date = $("#rpt_payments1").text();
  var to_date = $("#rpt_payments2").text();
  var branch_name;
  branch_name = getBranchTitle();
  let $table = $("#offline_modewise").find("table").clone();
  $table.css({
    width: "100%",
    "border-collapse": "collapse",
  });
  // Add inline CSS to each th and td
  $table.find("th, td").css({
    border: "1px solid black",
    padding: ".3rem",
  });
  if (
    $(".outstanding-hide-col").css("display") == "none" ||
    $(".outstanding-hide-col").hasClass("outstanding-hide-col")
  ) {
    $table.find(".outstanding-hide-col").remove();
  }
  print_outstanding_summary += get_title(
    from_date,
    to_date,
    "Outstanding Report Summary - " + branch_name
  );
  print_outstanding_summary += $table.prop("outerHTML");
  // print_outstanding_summary = get_title(from_date, to_date, "Outstanding Report Summary - " + branch_name);
  // print_outstanding_summary += outstandingSummaryForPrint;
  printWindow.document.write(print_outstanding_summary);
  printWindow.document.close();
  printWindow.print();
  printWindow.close();
});
$('#print_summary').on('click', () => {
  // console.log(outstandingSummaryForPrint);
  const printWindow = window.open('', '_blank');
  var print_outstanding_summary;
  var from_date = $("#rpt_payments1").text();
  var to_date = $("#rpt_payments2").text();
  var branch_name;
  branch_name = getBranchTitle();
  let $table = $('#offline_modewise').find('table').clone();
  $table.css({
    'width': '100%',
    'border-collapse': 'collapse'
  });
  // Add inline CSS to each th and td
  $table.find('th, td').css({
    'border': '1px solid black',
    'padding': '.3rem',
  });
  if ($('.outstanding-hide-col').css('display') == 'none' || $('.outstanding-hide-col').hasClass('outstanding-hide-col')) {
    $table.find('.outstanding-hide-col').remove();
  }
  print_outstanding_summary += get_title(from_date, to_date, "Outstanding Report Summary - " + branch_name);
  print_outstanding_summary += $table.prop('outerHTML');
  // print_outstanding_summary = get_title(from_date, to_date, "Outstanding Report Summary - " + branch_name);
  // print_outstanding_summary += outstandingSummaryForPrint;
  printWindow.document.write(print_outstanding_summary);
  printWindow.document.close();
  printWindow.print();
  printWindow.close();
})
// coded by jothika on 29-09-2025 [excel export issue]
function exportOutstandingReportToExcel() {
  try {
    if (!export_list || Object.keys(export_list).length === 0) {
      alert("No data to export.");
      return;
    }
    const dataForExcel = [];
    // let total_pay_amount = 0;
    // let grand_weight = 0;
    // let grand_ins = 0;
    // Loop through each scheme/group
    $.each(export_list, function (schemeName, schemeData) {
      // let paid_amount = 0;
      // let ins = 0;
      // let weight = 0;
      // Add group header row
      dataForExcel.push({
        "S.No": schemeName,
        "Scheme Code": "",
        "Account No": "",
        "Account Name": "",
        "Mobile": "",
        "Customer Name": "",
        "Address1": "",
        "Address2": "",
        "Area": "",
        "City": "",
        "State": "",
        "Pincode": "",
        "Joining Date": "",
        "Arrived Outstanding(INR)": "",
        "Received Weight(In GMS)": "",
        "Last Paid Date": "",
        "Maturity Date": "",
        "Scheme Type": "",
        "Joined Through": "",
        "Referred Employee": "",
        "Employee Created": ""
      });
      let sno = 0;
      $.each(schemeData, function (key, items) {
        sno++;
        // paid_amount += parseFloat(items.totalpay_amount);
        // ins += parseFloat(items.paid_installments);
        // weight += parseFloat(items.total_wgt);
        dataForExcel.push({
          "S.No": sno,
          "Scheme Code": items.code || "-",
          "Account No": items.scheme_acc_number,
          "Account Name": items.account_name,
          "Mobile": items.mobile,
          "Customer Name": items.name,
          "Address1": items.address1 || "-",
          "Address2": items.address2 || "-",
          "Area": items.address3 || "-",
          "City": items.city || "-",
          "State": items.state || "-",
          "Pincode": items.pincode || "-",
          "Joining Date": items.start_date,
          "Arrived Outstanding(INR)": items.totalpay_amount,
          "Received Weight(In GMS)": (items.total_wgt != 0 && items.total_wgt !== "" ? parseFloat(items.total_wgt).toFixed(3) : ""),
          "Last Paid Date": items.last_paid_date,
          "Maturity Date": items.maturity_date,
          "Scheme Type": items.scheme_type,
          "Joined Through": items.joined_thru,
          "Referred Employee": items.referred_employee || "-",
          "Employee Created": items.joined_emp
        });
      });
      // Subtotal row for this scheme
      // dataForExcel.push({
      // 	"S.No": "",
      // 	"Scheme": "",
      // 	"Scheme Account No": "",
      // 	"Account Name": "",
      // 	"Mobile": "",
      // 	"Customer Name": "",
      // 	"Address1": "",
      // 	"Address2": "",
      // 	"Address3": "",
      // 	"City": "",
      // 	"State": "",
      // 	"Pincode": "",
      // 	"Start Date": "SUB TOTAL",
      // 	"Paid Amount": paid_amount,
      // 	"Weight": (weight !== 0 ? parseFloat(weight).toFixed(3) : ""),
      // 	"Last Paid Date": "",
      // 	"Maturity Date": "",
      // 	"Scheme Type": "",
      // 	"Joined Through": "",
      // 	"Referred Employee": "",
      // 	"Created By": ""
      // });
      // total_pay_amount += paid_amount;
      // grand_ins += ins;
      // grand_weight += weight;
    });
    // Grand total row
    // dataForExcel.push({
    // 	"S.No": "",
    // 	"Scheme": "",
    // 	"Scheme Account No": "",
    // 	"Account Name": "",
    // 	"Mobile": "",
    // 	"Customer Name": "",
    // 	"Address1": "",
    // 	"Address2": "",
    // 	"Address3": "",
    // 	"City": "",
    // 	"State": "",
    // 	"Pincode": "",
    // 	"Start Date": "GRAND TOTAL",
    // 	"Paid Amount": total_pay_amount,
    // 	"Weight": (grand_weight !== 0 ? parseFloat(grand_weight).toFixed(3) : ""),
    // 	"Last Paid Date": "",
    // 	"Maturity Date": "",
    // 	"Scheme Type": "",
    // 	"Joined Through": "",
    // 	"Referred Employee": "",
    // 	"Created By": ""
    // });
    // Create worksheet and workbook
    const worksheet = XLSX.utils.json_to_sheet(dataForExcel);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Outstanding Report");
    // Export to file
    XLSX.writeFile(workbook, "Outstanding_Report.xlsx");
  } catch (error) {
    console.error("Excel export failed:", error);
    alert("Export failed.");
  }
}
var export_list = [];
function getSchemeDateRangeList() {
  $("div.overlay").css("display", "block");
  selectedValue = $('input[name="datepick"]:checked').val();
  $("#out_standing_date_range").html($('#rpt_payments1').html() + " to " + $('#rpt_payments2').html());
  my_Date = new Date();
  $.ajax({
    url: base_url + "index.php/admin_reports/scheme_customer_list_daterange?nocache=" + my_Date.getUTCSeconds(),
    //data: ( {'from_date':$('#rpt_payments1').html(),'to_date' :$('#rpt_payments2').html(),'id_scheme':$('#scheme_select').val(),'is_live':$('#is_live').val(),'id_group':$('#id_group').val(),'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#id_branch").val())}),
    //  data: ( {'id_scheme':$('#scheme_select').val(),'id_group':$('#id_group').val(),'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#branch_select").val()),'from_date' :$('#rpt_payments1').html()!=undefined ? $('#rpt_payments1').html():'','to_date' :$('#rpt_payments2').html()!=undefined ? $('#rpt_payments2').html():'','singlefilter' :$('#datesingle_search').val()!=undefined ? $('#datesingle_search').val():''}),
    data: { 'id_scheme': $('#scheme_select').val(), 'id_group': $('#id_group').val(), 'id_branch': ($('#branch_select').val() != '' && $('#branch_select').val() != undefined ? $('#branch_select').val() : $("#id_branch").val()), 'from_date': (selectedValue != 0 ? ($('#rpt_payments1').html() != undefined ? $('#rpt_payments1').html() : '') : ''), 'to_date': (selectedValue != 0 ? ($('#rpt_payments2').html() != undefined ? $('#rpt_payments2').html() : '') : ''), 'singlefilter': (selectedValue != 0 ? ($('#datesingle_search').val() != undefined ? $('#datesingle_search').val() : '') : '') },
    dataType: "JSON",
    serverSide: true,
    type: "POST",
    success: function (data) {
      $("div.overlay").css("display", "none");
      //var branch_name=($('#branch_name').val()!='' && $('#branch_name').val()!=undefined ? $('#branch_name').val() : $('#branch_select option:selected').toArray().map(item => item.text).join());
      var branch_name = getBranchTitle();
      export_list = data.schemes;
      // console.log("Export List : ",export_list);
      title = get_title('', '', 'Customer Outstanding Payment Report - ' + branch_name);
      //scheme Detailed datatable starts here
      title += '</tbody>' +
        '</table></div></br>';
      //Scheme Detail Report	
      // $("div.overlay").css("display", "none");   
      $("#scheme_wise_detail_report > tbody > tr").remove();
      $('#scheme_wise_detail_report').dataTable().fnClearTable();
      $('#scheme_wise_detail_report').dataTable().fnDestroy();
      trHTML = '';
      total_pay_amount = 0;
      total_bonus_amount = 0;
      total_metal_weight = 0;
      grand_ins = 0;
      grand_weight = 0;
      $.each(data.schemes, function (key, scheme) {
        var paid_amount = 0;
        var ins = 0;
        var weight = 0;
        var bonus_amount = 0;
        var metal_weight = 0;
        trHTML += '<tr>' +
          '<td style="text-align:left;" class="report-key"><strong>' + key + '</strong></td>' +
          '<td></td>' +
          // '<td></td>'+
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          // '<td></td>'+
          '</tr>';
        $.each(scheme, function (key, items) {
          //	scheme_acc_number=getSchAccNumber_Format(items.is_lucky_draw,items.scheme_group_code,items.scheme_acc_number,items.schemeaccNo_displayFrmt,items.scheme_wise_acc_no,items.acc_branch,items.start_year,items.code);
          paid_amount += parseFloat(items.totalpay_amount);
          ins += parseFloat(items.paid_installments);
          weight += parseFloat(items.total_wgt);
          var sales_ledger = base_url + 'index.php/admin_ret_reports/customer_history/list/' + items.mobile;
          var acc_ledger = base_url + 'index.php/reports/payment/account/' + items.id_scheme_account;
          trHTML += '<tr>' +
            '<td>' + parseInt(key + 1) + '</td>' +
            '<td>' + (items.code != '' ? items.code : '-') + '</td>' +
            // '<td>'+(items.group_code!=''?items.group_code:'-')+'</td>'+
            '<td><a href=' + acc_ledger + ' target="_blank">' + items.scheme_acc_number + '</td>' +
            '<td>' + items.account_name + '</td>' +
            '<td><input type="hidden" class="mobile" value="' + items.mobile + '"><a href=' + sales_ledger + ' target="_blank">' + items.mobile + '</td>' +
            '<td>' + items.name + '</td>' +
            '<td>' + (items.address1 != '' ? items.address1 : '-') + '</td>' +
            '<td>' + (items.address2 != '' ? items.address2 : '-') + '</td>' +
            '<td>' + (items.area != '' ? items.area : '-') + '</td>' +   // esakki 11-11
            '<td>' + (items.city != '' ? items.city : '-') + '</td>' +
            '<td>' + (items.state != '' ? items.state : '-') + '</td>' +
            '<td>' + (items.pincode != '' ? items.pincode : '-') + '</td>' +
            '<td>' + items.start_date + '</td>' +
            '<td>' + indianCurrency.format(items.totalpay_amount) + '</td>' +
            // '<td>' + (items.total_wgt != 0 && items.total_wgt != "" ? parseFloat(items.total_wgt).toFixed(3) : "0.00") + '</td>' +
            '<td>' + (items.total_wgt != 0 && items.total_wgt != "" ? (formatMetalWeight(items.total_wgt)) : "0.00") + '</td>' +
            '<td>' + items.last_paid_date + '</td>' +
            '<td>' + items.maturity_date + '</td>' +
            '<td>' + items.scheme_type + '</td>' +
            '<td>' + items.joined_thru + '</td>' +
            '<td>' + (items.referred_employee != '' ? items.referred_employee : '-') + '</td>' +
            '<td>' + items.joined_emp + '</td>' +
            '</tr>';
        });
        trHTML += '<tr>' +
          '<td></td>' +
          //  '<td></td>'+
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td class="report-sub-total"><strong>SUB TOTAL</strong></td>' +
          '<td class="report-sub-total"><strong>' + formatCurrency.format(paid_amount) + '</strong></td>' +
          // '<td class="report-sub-total"><strong>' + (weight != 0 ? (session_data.metal_wgt_roundoff == 0 ? trimDecimal(weight, session_data.metal_wgt_decimal) : weight.toFixed(session_data.metal_wgt_decimal)) : "") + '</strong></td>' +
          '<td class="report-sub-total"><strong>' + (weight != 0 ? (formatMetalWeight(weight)) : "") + '</strong></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '<td></td>' +
          '</tr>';
        total_pay_amount += parseFloat(paid_amount);
        grand_ins += parseFloat(ins);
        grand_weight += parseFloat(weight);
      });
      trHTML += '<tr>' +
        '<td></td>' +
        // '<td></td>'+
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td class="report-grand-total"><strong>GRAND TOTAL</strong></td>' +
        '<td class="report-grand-total"><strong>' + formatCurrency.format(total_pay_amount) + '</strong></td>' +
        '<td class="report-grand-total"><strong>' + (grand_weight != 0 ? (session_data.metal_wgt_roundoff == 0 ? trimDecimal(grand_weight, session_data.metal_wgt_decimal) : grand_weight.toFixed(session_data.metal_wgt_decimal)) : "") + '</strong></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td></td>' +
        '</tr>';
      //Scheme Summary				
      $('#scheme_wise_detail_report > tbody').html(trHTML);
      // Check and initialise datatable
      if (!$.fn.DataTable.isDataTable('#scheme_wise_detail_report')) {
        if (data.schemes.length !== 0) {
          oTable = $('#scheme_wise_detail_report').dataTable({
            "bSort": false,
            "bInfo": false,
            "scrollX": '100%',
            "dom": 'lBfrtip',
            "pageLength": 25,
            "lengthMenu": [[-1, 25, 50, 100, 250], ["All", 25, 50, 100, 250]],
            "buttons": [
              {
                extend: 'print',
                footer: true,
                title: '',
                messageTop: title,
                orientation: 'landscape',
                customize: function (win) {
                  $(win.document.body).find('table')
                    .addClass('compact');
                  $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', '10px')
                    .css('font-family', 'sans-serif');

                  // Inject refined CSS for borders and page layout
                  var css = 'table.dataTable { ' +
                    'border-collapse: collapse !important; ' +
                    'width: 99% !important; ' +
                    'margin: 0 auto !important; ' +
                    'border: 1px solid black !important; ' +
                    'box-sizing: border-box !important; } ' +
                    'table.dataTable th, table.dataTable td { ' +
                    'border: 1px solid black !important; } ' +
                    '@page { size: landscape; margin: 10mm; }';
                  var head = win.document.head || win.document.getElementsByTagName('head')[0];
                  var style = win.document.createElement('style');
                  style.type = 'text/css';
                  style.media = 'print';
                  if (style.styleSheet) {
                    style.styleSheet.cssText = css;
                  } else {
                    style.appendChild(win.document.createTextNode(css));
                  }
                  head.appendChild(style);
                },
                exportOptions: {
                  columns: ':visible'
                },
              },
              {
                extend: 'excel',
                footer: true,
                title: 'Customer Outstanding Payment Report - ' + branch_name,
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed columns', collectionTitle: 'Column visibility control'
              },
            ],
            "columnDefs": [{
              targets: [0, 13, 14],
              className: 'dt-right'
            },
            {
              targets: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 15, 16, 17, 18, 19, 20],
              className: 'dt-left'
            }
            ],
          });
        }
        else {
          var brHTML = '';
          brHTML += '<tr><td colspan=21 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>'
          $('#scheme_wise_detail_report > tbody').html(brHTML);
        }
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
  //scheme Detailed datatable ends here
}
/*function outstanding_summary() {
  console.log($('#datesingle_search').val())
  selectedValue = $('input[name="datepick"]:checked').val();
  console.log(selectedValue)
  console.log(selectedValue)
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $.ajax({
    url: base_url + "index.php/admin_reports/scheme_summary?nocache=" + my_Date.getUTCSeconds(),
    //data: ( {'from_date':$('#rpt_payments1').html(),'to_date' :$('#rpt_payments2').html(),'id_scheme':$('#scheme_select').val(),'is_live':$('#is_live').val(),'id_group':$('#id_group').val(),'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#id_branch").val())}),
    data: { 'id_scheme': $('#scheme_select').val(), 'id_group': $('#id_group').val(), 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#id_branch").val()), ...(selectedValue != 0 ? { 'singlefilter': ($('#datesingle_search').val() != undefined ? $('#datesingle_search').val() : ''), 'from_date': ($('#rpt_payments1').html() != undefined ? $('#rpt_payments1').html() : ''), 'to_date': ($('#rpt_payments2').html() != undefined ? $('#rpt_payments2').html() : '') } : {}) },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $("div.overlay").css("display", "none");
      //Scheme wise summary and group wise summary starts here
      var offHTML = '';
      var onHTML = '';
      var scheme_count = 0;
      var scheme_ins = 0;
      var scheme_amt = 0;
      var closed_amt = 0;
      var collection_amt = 0;
      var balace_amt = 0;
      var sub_scheme_amt = 0;
      var scheme_metal_wgt = 0;
      var sub_gold_metal_wgt = 0;
      var grand_gold_metal_wgt = 0;
      var sub_silver_metal_wgt = 0;
      var grand_silver_metal_wgt = 0;
      var grp = 0;
      var grp_ins = 0;
      var grp_paid = 0;
      var grp_metal_wgt = 0;
      var grp_gold_wgt = 0;
      var grp_silver_wgt = 0;
      // console.log(offHTML)
      if (data.scheme_summary == null) {
        offHTML += '<p style="text-align:center;"><strong><span>No Data Available</span></strong></p>';
        $('#offline_modewise').html(offHTML);
        $('#online_modewise').html(offHTML);
      }
      else {
        offHTML += '<div class="row">' +
          '<div class="col-md-1"><strong><span>Scheme</span></strong></div>' +
          '<div class="col-md-1"><label> | </label></div>' +
          '<div class="col-md-1"><strong><span>No.Acc</span></strong></div>' +
          '<div class="col-md-2" style="text-align:right;"><strong><span>Opening.Amt</span></strong></div>' +
          '<div  class="col-md-1 collection-amt"><strong><span>Coll.Amt</span></strong></div>' +
          '<div  class="col-md-1 closing_amt"><strong><span>Closing Amt</span></strong></div>' +
          '<div  class="balance_amt col-md-1"><strong><span>B.Amt</span></strong></div>' +
          '<div class="col-md-1"><strong><span>Gold</span></strong></div>' +
          '<div class="col-md-1"><strong><span>Silver</span></strong></div>' +
          '</div><hr>';
        onHTML += '<div class="row">' +
          '<div class="col-md-2" style="text-align:right;"><strong><span>Scheme</span></strong></div>' +
          '<div class="col-md-1"><label> | </label></div>' +
          '<div class="col-md-2" style="text-align:right;"><strong><span>No.Acc</span></strong></div>' +
          '<div class="col-md-2" style="text-align:right;"><strong><span>Amt</span></strong></div>' +
          '<div class="col-md-2" style="text-align:right;"><strong><span>Gold.Wgt</span></strong></div>' +
          '<div class="col-md-2" style="text-align:right;"><strong><span>Silver.Wgt</span></strong></div>' +
          '</div><hr>';
        $.each(data.scheme_summary, function (key, classification) {
          console.log(key)
          offHTML += ' <div class="row">' +
            // '<div class="col-md-5" style="text-align: left;margin-left:-8px;"><strong>'+key.toUpperCase()+'</strong></div>'+
            '</div>';
          sub_gold_metal_wgt = 0;
          sub_silver_metal_wgt = 0;
          sub_closing_wt = 0;
          sub_bal_amt = 0;
          sub_collection_amt = 0;
          sub_scheme_count = 0;
          sub_scheme_amt = 0;
          $.each(classification, function (scheme, val) {
            console.log(val);
            sub_gold_metal_wgt += (val.id_metal == 1 && isValid(val.metal_weight) ? parseFloat(val.metal_weight) : 0);
            grand_gold_metal_wgt += (val.id_metal == 1 && isValid(val.metal_weight) ? parseFloat(val.metal_weight) : 0);
            sub_silver_metal_wgt += (val.id_metal == 2 && isValid(val.metal_weight) ? parseFloat(val.metal_weight) : 0);
            grand_silver_metal_wgt += (val.id_metal == 2 && isValid(val.metal_weight) ? parseFloat(val.metal_weight) : 0);
            offHTML += ' <div class="row">' +
              '<div class="col-md-1" style="text-align:left;">' + val.code + '</div>' +
              '<div class="col-md-1"><label> : </label></div>' +
              '<div class="col-md-1" style="text-align:right;"><strong><span>' + parseInt(val.scheme_count) + '</span></strong></div>' +
              // 		'<div class="col-md-1" style="text-align:right;"><strong><span>'+(val.paid_installments!=null?val.paid_installments:0)+'</span></strong></div>'+
              '<div class="col-md-2" style="text-align:right;"><strong><span>' + (val.paid_amount != null ? formatCurrency.format(parseFloat(val.paid_amount).toFixed(2)) : 0) + '</span></strong></div>' +
              '<div    class="col-md-1  collection-amt" style="text-align:right;"><strong><span>' + (val.collection_amount != null ? formatCurrency.format(parseFloat(val.collection_amount).toFixed(2)) : 0) + '</span></strong></div>' +
              // '<div class="col-md-2" style="text-align:right;"><strong><span>'+(val.paid_amount!=null?formatCurrency.format(parseFloat(val.paid_amount).toFixed(2)):0)+'</span></strong></div>'+
              // '<div class="col-md-2" style="text-align:right;"><strong><span>'+(val.paid_amount!=null?formatCurrency.format(parseFloat(val.paid_amount).toFixed(2)):0)+'</span></strong></div>'+
              '<div   class="closing_amt col-md-1" style="text-align:right;"><strong><span>' + (val.closed_amount != null ? formatCurrency.format(parseFloat(val.closed_amount).toFixed(2)) : 0) + '</span></strong></div>' +
              '<div class="balance_amt col-md-1" style="text-align:right;"><strong><span>' + (val.balance_amount != null ? formatCurrency.format(parseFloat(Math.abs(val.balance_amount)).toFixed(2)) : 0) + '</span></strong></div>' +
              '<div class="col-md-1" style="text-align:right;"><span>' + (val.id_metal == 1 && isValid(val.metal_weight) && val.metal_weight != 0 ? parseFloat(val.metal_weight).toFixed(3) : "") + '</span></div>' +
              '<div class="col-md-1" style="text-align:right;"><span>' + (val.id_metal == 2 && isValid(val.metal_weight) && val.metal_weight != 0 ? parseFloat(val.metal_weight).toFixed(3) : "") + '</span></div>' +
              '</div>';
            scheme_count += parseInt(val.scheme_count);
            sub_scheme_count += parseInt(val.scheme_count);
            scheme_ins += parseInt(val.paid_installments != null ? val.paid_installments : 0);
            scheme_amt += parseInt(val.paid_amount != null ? val.paid_amount : 0);
            collection_amt += parseInt(val.collection_amount != null ? val.collection_amount : 0);
            closed_amt += parseInt(val.closed_amount != null ? val.closed_amount : 0);
            balace_amt += parseInt(val.balance_amount != null ? val.balance_amount : 0);
            sub_closing_wt += parseInt(val.closed_amount != null ? val.closed_amount : 0);
            sub_bal_amt += parseInt(val.balance_amount != null ? val.balance_amount : 0);
            scheme_metal_wgt += parseFloat(val.metal_weight != null ? val.metal_weight : 0);
            sub_scheme_amt += parseInt(val.paid_amount != null ? val.paid_amount : 0);
            sub_collection_amt += parseInt(val.collection_amount != null ? val.collection_amount : 0);
            if (val.is_lucky_draw == 1) {
              // alert('asda')
              if (val.scheme_name != "" && val.scheme_count != "") {
                grp = 0;
                grp_ins = 0;
                grp_paid = 0;
                grp_closeamt = 0;
                grp_collectionamt = 0;
                grp_bal = 0;
                onHTML += '<strong><span>' + val.scheme_name.toUpperCase() + '</span></strong><br>';
                $.each(val.group_scheme, function (keys, vals) {
                  onHTML += ' <div class="row">' +
                    '<div class="col-md-4" style="text-align:left;">' + vals.group_code + '</div>' +
                    '<div class="col-md-1"><label> : </label></div>' +
                    '<div class="col-md-1" style="text-align:right;"><strong><span>' + parseInt(vals.count) + '</span></strong></div>' +
                    // 		'<div class="col-md-1" style="text-align:right;"><strong><span>'+(vals.paid_installments!=null?vals.paid_installments:0)+'</span></strong></div>'+
                    '<div class="col-md-2" style="text-align:right;"><strong><span>' + (vals.paid_amount != null ? formatCurrency.format(parseFloat(vals.paid_amount).toFixed(2)) : 0) + '</span></strong></div>' +
                    '<div class="col-md-1 collection-amt" style="text-align:right;"><strong><span>' + (vals.collection_amount != null ? formatCurrency.format(parseFloat(vals.collection_amount).toFixed(2)) : 0) + '</span></strong></div>' +
                    '<div  class="closing_amt col-md-1" style="text-align:right;"><strong><span>' + (val.closed_amount != null ? formatCurrency.format(parseFloat(val.closed_amount).toFixed(2)) : 0) + '</span></strong></div>' +
                    '<div   class="balance_amt col-md-2" style="text-align:right;"><strong><span>' + (val.balance_amount != null ? formatCurrency.format(parseFloat(val.balance_amount).toFixed(2)) : 0) + '</span></strong></div>' +
                    '<div class="col-md-2" style="text-align:right;"><span>' + (vals.id_metal == 1 && isValid(vals.metal_weight) && vals.metal_weight != 0 ? parseFloat(vals.metal_weight).toFixed(3) : "") + '</span></div>' +
                    '<div class="col-md-2" style="text-align:right;"><span>' + (vals.id_metal == 2 && isValid(vals.metal_weight) && vals.metal_weight != 0 ? parseFloat(vals.metal_weight).toFixed(3) : "") + '</span></div>' +
                    '</div> ';
                  grp += parseInt(vals.count);
                  grp_ins += parseInt(vals.paid_installments != null ? vals.paid_installments : 0);
                  grp_paid += parseInt(vals.paid_amount != null ? vals.paid_amount : 0);
                  grp_collectionamt += parseInt(vals.collection_amount != null ? vals.collection_amount : 0);
                  grp_closeamt += parseInt(vals.closed_amount != null ? vals.closed_amount : 0);
                  grp_bal += parseInt(vals.balance_amount != null ? vals.balance_amount : 0);
                  grp_gold_wgt += (vals.id_metal == 1 && isValid(vals.metal_weight) ? parseFloat(vals.metal_weight) : 0);
                  grp_silver_wgt += (vals.id_metal == 2 && isValid(vals.metal_weight) ? parseFloat(vals.metal_weight) : 0);
                });
                onHTML += '<div class="row">' +
                  '<div class="col-md-3" style="text-align:left;"> <strong>Total</strong></div>' +
                  '<div class="col-md-1"><label> : </label></div>' +
                  '<div class="col-md-1" style="text-align:right;"><strong><span>' + parseInt(grp) + '</span></strong></div>' +
                  // 			'<div class="col-md-1" style="text-align:right;"><strong><span>'+ grp_ins+'</span></strong></div>'+
                  '<div class="col-md-2" style="text-align:right;"><strong><span>' + formatCurrency.format(parseFloat(grp_paid).toFixed(2)) + '</span></strong></div>' +
                  '<div  class="collection-amt col-md-2" style="text-align:right;"><strong><span>' + formatCurrency.format(parseFloat(grp_collectionamt).toFixed(2)) + '</span></strong></div>' +
                  '<div   class="col-md-2 closing_amt" style="text-align:right;"><strong><span>' + formatCurrency.format(parseFloat(grp_closeamt).toFixed(2)) + '</span></strong></div>' +
                  '<div    class=" balance_amt col-md-2" style="text-align:right;"><strong><span>' + formatCurrency.format(parseFloat(grp_bal).toFixed(2)) + '</span></strong></div>' +
                  '<div class="col-md-2" style="text-align:right;"><strong><span>' + (grp_gold_wgt != 0 ? parseFloat(grp_gold_wgt).toFixed(3) : "") + '</span></strong></div>' +
                  '<div class="col-md-2" style="text-align:right;"><strong><span>' + (grp_silver_wgt != 0 ? parseFloat(grp_silver_wgt).toFixed(3) : "") + '</span></strong></div>' + '</div>';
              }
            }
          });
          offHTML += '<div class="row"></div>' +
            '<hr/><div class="row">' +
            '<div class="col-md-1" style="text-align:left;color:red;"> <strong>Sub Total </strong></div>' +
            '<div class="col-md-1"style="color:red;"><label> : </label></div>' +
            '<div class="col-md-1" style="text-align:right;color:red;"><strong><span> ' + parseInt(sub_scheme_count) + ' </span></strong></div>' +
            // '<div class="col-md-1" style="text-align:right;"><strong><span> '+scheme_ins+' </span></strong></div>'+
            '<div class="col-md-2" style="text-align:right;color:red;"><strong><span> ' + formatCurrency.format(parseFloat(sub_scheme_amt).toFixed(2)) + ' </span></strong></div>' +
            '<div   class="collection-amt col-md-1" style="text-align:right;color:red;"><strong><span> ' + formatCurrency.format(parseFloat(sub_collection_amt).toFixed(2)) + ' </span></strong></div>' +
            '<div    class="closing_amt col-md-1" style="text-align:right;color:red;"><strong><span> ' + formatCurrency.format(parseFloat(sub_closing_wt).toFixed(2)) + ' </span></strong></div>' +
            '<div    class="balance_amt col-md-1" style="text-align:right;color:red;"><strong><span> ' + formatCurrency.format(parseFloat(Math.abs(sub_bal_amt)).toFixed(2)) + ' </span></strong></div>' +
            '<div class="col-md-1" style="text-align:right;color:red;"><strong><span> ' + (sub_gold_metal_wgt != 0 ? parseFloat(sub_gold_metal_wgt).toFixed(3) : "") + ' </span></strong></div>' +
            '<div class="col-md-1" style="text-align:right;color:red;"><strong><span> ' + (sub_silver_metal_wgt != 0 ? parseFloat(sub_silver_metal_wgt).toFixed(3) : "") + ' </span></strong></div>' + '</div><hr/>'
            ;
        });
        offHTML += '<div class="row"></div><hr>' +
          '<div class="row">' +
          '<div class="col-md-1" style="text-align:left;"> <strong>Total </strong></div>' +
          '<div class="col-md-1"><label> : </label></div>' +
          '<div class="col-md-1" style="text-align:right;"><strong><span> ' + parseInt(scheme_count) + ' </span></strong></div>' +
          // 		'<div class="col-md-1" style="text-align:right;"><strong><span> '+scheme_ins+' </span></strong></div>'+
          '<div class="col-md-2" style="text-align:right;"><strong><span> ' + formatCurrency.format(parseFloat(scheme_amt).toFixed(2)) + ' </span></strong></div>' +
          '<div   class="collection-amt col-md-1" style="text-align:right;"><strong><span> ' + formatCurrency.format(parseFloat(collection_amt).toFixed(2)) + ' </span></strong></div>' +
          '<div   class="closing_amt col-md-1" style="text-align:right;"><strong><span> ' + formatCurrency.format(parseFloat(closed_amt).toFixed(2)) + ' </span></strong></div>' +
          '<div   class=" balance_amt col-md-1" style="text-align:right;"><strong><span> ' + formatCurrency.format(parseFloat(Math.abs(balace_amt)).toFixed(2)) + ' </span></strong></div>' +
          '<div class="col-md-1" style="text-align:right;"><strong><span> ' + (grand_gold_metal_wgt != 0 ? parseFloat(grand_gold_metal_wgt).toFixed(3) : "") + ' </span></strong></div>' +
          '<div class="col-md-1" style="text-align:right;"><strong><span> ' + (grand_silver_metal_wgt != 0 ? parseFloat(grand_silver_metal_wgt).toFixed(3) : "") + ' </span></strong></div>' +
          '</div>';
        $('#offline_modewise').html(offHTML);
        $('#online_modewise').html(onHTML);
      }
      //Scheme wise summary and group wise summary ends here
      $('#rpt_payments1').html('')
      $('#rpt_payments2').html('')
      if (selectedValue != '0') {
        // If $('#datesingle_search').val() has a value, show the "Collection Amt" element
        $('.collection-amt,.closing_amt,.balance_amt').css("display", 'block');
      } else {
        // If $('#datesingle_search').val() is empty, hide the "Collection Amt" element
        $('.collection-amt,.closing_amt,.balance_amt').css("display", 'none');
      }
    }
  });
}
/*OutStanding Report function ends*/
/*ends*/
// Gift report-yet to issue
$('#gift_report_search').on('click', function () {
  get_gift_report();
  //   gift_summary();
});
function get_gift_name() {
  $(".overlay").css('display', 'block');
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/get/giftname_list',
    dataType: 'json',
    success: function (data) {
      $('#gift_select').append(
        $("<option></option>")
          .attr("value", 0)
          .text('All')
      );
      //  var scheme_val =  $('#id_schemes').val();
      $.each(data, function (key, item) {
        $('#gift_select').append(
          $("<option></option>")
            .attr("value", item.id_gift)
            .text(item.gift_name)
        );
      });
      $("#gift_select").select2({
        placeholder: "Select Gift Name",
        allowClear: true
      });
      //  $("#gift_select").select2("val",(scheme_val!='' && scheme_val>0?scheme_val:''));
      $(".overlay").css("display", "none");
    }
  });
}
function get_gift_report() {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    data: ({ 'from_date': $('#gift_from_date').html(), 'to_date': $('#gift_to_date').html(), 'scheme': $('#scheme_select').val(), 'id_branch': $('#branch_select').val(), 'gift': $('#gift_select').val(), 'report_type': $('#report_type').val() }),
    url: base_url + "index.php/admin_reports/get_online_gift_report?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      console.log(data);
      gift_summary(data);
      get_gift_report_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
// gift report Ends
/* WEDDING/BIRTHDAY REPORT STARTS */
function set_customer_wishes_table(from_date, to_date) {
  my_Date = new Date();
  //alert();
  $.ajax({
    url: base_url + "index.php/admin_reports/get_cus_birthwed?nocache=" + my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "POST",
    data: (from_date != '' && to_date != '' ? { 'from_date': from_date, 'to_date': to_date }
      : { 'from_date': '', 'to_date': '' }),
    success: function (data) {
      var accounts = data.accounts;
      console.log(from_date);
      console.log(to_date);
      console.log(accounts);
      set_customer_wish_list(data, from_date, to_date);
    }
  });
}
function set_customer_wish_list(data, from_date, to_date) {
  var accounts = data.accounts;
  console.log(accounts);
  var branch_name = getBranchTitle();
  title = get_title(from_date, to_date, 'Birthday/Wedding Day Report - ' + branch_name);
  var oTable = $('#customer_list').DataTable();
  oTable.clear().draw();
  if (accounts != null && accounts.length > 0) {
    oTable = $('#customer_list').DataTable({
      "bDestroy": true,
      "bInfo": true,
      "bFilter": true,
      "bSort": true,
      "dom": 'lBfrtip',
      "pageLength": 25,
      "lengthMenu": [[-1, 25, 50, 100, 250], ["All", 25, 50, 100, 250]],
      "buttons": [
        {
          extend: 'print',
          footer: true,
          title: '',
          messageTop: title,
          orientation: 'landscape',
          customize: function (win) {
            $(win.document.body).find('table')
              .addClass('compact');
            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', '10px')
              .css('font-family', 'sans-serif');
          },
        },
        {
          extend: 'excel',
          footer: true,
          title: 'Birthday/Wedding Day Report - ' + branch_name + ' ' + from_date + ' to ' + to_date,
        },
        {
          extend: 'colvis',
          collectionLayout: 'fixed columns', collectionTitle: 'Column visibility control'
        },
      ],
      // "buttons": ['print','excel'],
      "tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
      "aaData": accounts,
      "order": [[0, "desc"]],
      "aoColumns": [
        { "mDataProp": "id_customer" },
        { "mDataProp": "cus_name" },
        { "mDataProp": "cus_mobile" },
        { "mDataProp": "village_name" },
        { "mDataProp": "date_of_birth" },
        { "mDataProp": "date_of_wed" },
        { "mDataProp": "total_gold_wt" },
        { "mDataProp": "total_silver_wt" },
        { "mDataProp": "active_acc" },
        { "mDataProp": "closed_count" }
      ],
      "columnDefs": [
        {
          targets: [0, 2, 6, 7, 8, 9],
          className: 'dt-right'
        },
        {
          targets: [1, 3, 4, 5],
          className: 'dt-left'
        },
      ],
    });
  }
  else {
    var brHTML = '';
    brHTML += '<tr><td colspan=10 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>'
    $('#customer_list > tbody').html(brHTML);
  }
}
/*ENDS*/
$('#employee_select').select2().on("change", function (e) {
  if (this.value != '') {
    $('#id_employee').val(this.value);
  }
});
/*function get_employee_list()
  {	
     $(".overlay").css('display','block');	
     $.ajax({		
       type: 'GET',		
       url: base_url+'index.php/admin_employee/get_employee',		
       dataType:'json',		
       success:function(data){		
         var id_employee=$('#id_employee').val();	
         $('#employee_select').append(
          $("<option></option>")
          .attr("value", 0)						  
            .text('All')
         );
         $.each(data, function (key, item) {					  				  			   		
           $('#employee_select').append(						
           $("<option></option>")						
           .attr("value", item.id_employee)						  						  
           .text(item.firstname )						  					
           );			   											
         });						
         $("#employee_select").select2({			    
           placeholder: "Select Employee name",			    
           allowClear: true		    
         });				
         $("#employee_select").select2("val", ($('#id_employee').val()!=null?$('#id_employee').val():''));
         var selectid=$('#id_employee').val();
           if(selectid!=null && selectid > 0)
          {
              $('#id_employee').val(selectid);
              $('.overlay').css('display','block');
          }		
       }	
    }); 
  }*/
function get_employee_list() {
  $(".overlay").css('display', 'block');
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/admin_employee/get_employee',
    dataType: 'json',
    success: function (data) {
      var id_employee = $('#id_employee').val();
      $('#employee_select').append(
        $("<option></option>")
          .attr("value", 0)
          .text('All')
      );
      if (ctrl_page[1] == 'member_report' || ctrl_page[1] == 'renewal_live_report') {
        $.each(data, function (key, item) {
          if (item.emp_code == ctrl_page[3]) {
            $("#referred_emp_name").text("Employee : " + item.firstname);
          }
          $('#employee_select').append(
            $("<option></option>")
              .attr("value", item.emp_code)
              .text(item.firstname)
          );
        });
      }
      else {
        $.each(data, function (key, item) {
          $('#employee_select').append(
            $("<option></option>")
              .attr("value", item.id_employee)
              .text(item.firstname)
          );
        });
      }
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
}
/*payment edit block starts */
// coded by Jothika 11-10-2025 [edit payment/acc] — refactored for multi-row
let toasterShown = false;
$(document).on('input', '#sch_acc_id', function (e) {
  const val = $(this).val();
  if (!/^[\d,\s]*$/.test(val)) {
    $(this).val(val.replace(/[^\d,\s]/g, ""));
    if (!toasterShown) {
      $.toaster({ priority: 'error', title: 'Warning!', message: 'Only Numbers and commas are allowed ...' });
      toasterShown = true;
      setTimeout(() => { toasterShown = false; }, 2000)
    }
    $(this).focus();
  }
});
let paytoasterShown = false;
$(document).on('input', '#pay_id', function (e) {
  const val = $(this).val();
  if (!/^[\d,\s]*$/.test(val)) {
    $(this).val(val.replace(/[^\d,\s]/g, ""));
    if (!paytoasterShown) {
      $.toaster({ priority: 'error', title: 'Warning!', message: 'Only Numbers and commas are allowed ...' });
      paytoasterShown = true;
      setTimeout(() => { paytoasterShown = false; }, 2000)
    }
    $(this).focus();
  }
});

// ==================== ACCOUNT SUBMIT (multi-row) ====================
$("#acc_submit").on("click", function () {
  var raw = $("#sch_acc_id").val().trim();
  if (raw === '') {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Scheme Account ID(s).' });
    return;
  }
  var ids = raw.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== '' && $.isNumeric(s); });
  if (ids.length === 0) {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Scheme Account ID(s).' });
    return;
  }

  var results = new Array(ids.length);
  var fetchCount = 0;
  var notFoundIds = [];
  ids.forEach(function(id, index) {
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/editAccOrPayments/get_acc_byId',
      dataType: 'json',
      data: { 'id_scheme_account': id },
      success: function (data) {
        if (Object.keys(data).length > 0) {
          results[index] = { data: data, found: true };
        } else {
          results[index] = { id: id, found: false };
          notFoundIds.push(id);
        }
      },
      error: function() { results[index] = { id: id, found: false }; notFoundIds.push(id); },
      complete: function() {
        fetchCount++;
        if (fetchCount === ids.length) {
          if (notFoundIds.length > 0) {
            $.toaster({ priority: 'warning', title: 'Warning!', message: 'Account not found for ID(s): ' + notFoundIds.join(', ') });
          }
          var validResults = [];
          for (var i = 0; i < results.length; i++) {
            if (results[i] && results[i].found) {
              results[i].rowIndex = validResults.length + 1;
              validResults.push(results[i]);
            }
          }
          if (validResults.length > 0) set_acc_table_multi(validResults);
        }
      }
    });
  });
});

// ==================== PAYMENT SUBMIT (multi-row) ====================
$("#pay_submit").on("click", function () {
  var raw = $("#pay_id").val().trim();
  if (raw === '') {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Payment ID(s).' });
    return;
  }
  var ids = raw.split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s !== '' && $.isNumeric(s); });
  if (ids.length === 0) {
    $.toaster({ priority: 'warning', title: 'Warning!', message: 'Please enter valid Payment ID(s).' });
    return;
  }

  var results = new Array(ids.length);
  var fetchCount = 0;
  var notFoundIds = [];
  ids.forEach(function(id, index) {
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/editAccOrPayments/get_pay_byId',
      dataType: 'json',
      data: { 'id_payment': id },
      success: function (data) {
        if (Object.keys(data).length > 0) {
          results[index] = { data: data, id_payment: id, found: true };
        } else {
          results[index] = { id_payment: id, found: false };
          notFoundIds.push(id);
        }
      },
      error: function() { results[index] = { id_payment: id, found: false }; notFoundIds.push(id); },
      complete: function() {
        fetchCount++;
        if (fetchCount === ids.length) {
          if (notFoundIds.length > 0) {
            $.toaster({ priority: 'warning', title: 'Warning!', message: 'Payment data not found for ID(s): ' + notFoundIds.join(', ') });
          }
          var validResults = [];
          for (var i = 0; i < results.length; i++) {
            if (results[i] && results[i].found) {
              results[i].rowIndex = validResults.length + 1;
              validResults.push(results[i]);
            }
          }
          if (validResults.length > 0) set_pay_table_multi(validResults);
        }
      }
    });
  });
});

// ==================== RENDER PAYMENT TABLE (multi-row) ====================
function set_pay_table_multi(results) {
  var srHTML = '';
  var hasOnlinePayment = false;
  results.forEach(function(item) {
    var data = item.data;
    var id_payment = item.id_payment;
    var rowIdx = item.rowIndex;
    var payment_status = '';
    if (data.added_by == 2) hasOnlinePayment = true;
    if (data.payment_status == 1) payment_status = 'Success';
    else if (data.payment_status == 7) payment_status = 'Pending';
    else if (data.payment_status == 3) payment_status = 'Failed';

    srHTML += '<tr data-row="' + rowIdx + '" data-payid="' + id_payment + '"' +
      ' data-original-date="' + data.date_payment + '"' +
      ' data-original-accid="' + data.id_scheme_account + '"' +
      ' data-installment-cycle="' + (data.installment_cycle || 0) + '"' +
      ' data-ins-days-duration="' + (data.ins_days_duration || 0) + '"' +
      ' data-payment-chances="' + (data.payment_chances || 0) + '"' +
      ' data-scheme-type="' + (data.scheme_type || 0) + '"' +
      ' data-flexible-type="' + (data.flexible_sch_type || 0) + '"' +
      ' data-acc-start="' + (data.acc_start_date || '') + '">' +
      '<td>' + rowIdx + '</td>' +
      '<td><span class="row-id-payment">' + id_payment + '</span></td>' +
      '<td><span>' + data.id_scheme_account + '</span><input type="hidden" class="row-sch-account-id" value="' + data.id_scheme_account + '"></td>' +
      '<td><div class="input-group date"><input type="text" value="' + data.date_payment + '" class="form-control input-sm date row-pay-datetimepicker" data-date-end-date="0d" data-date-format="dd-mm-yyyy" ' + (data.added_by == 2 ? 'disabled' : '') + ' /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>' +
      '<td><span class="row-pay-amt">' + parseFloat(data.payment_amount) + '</span></td>' +
      '<td><span class="row-metal-rate">' + parseFloat(data.metal_rate) + '</span></td>' +
      '<td><span class="row-metal-weight">' + (data.metal_weight != null && data.metal_weight != '' ? formatMetalWeight(data.metal_weight) : '0') + '</span></td>' +
      '<td><span class="row-saved-benefits">' + (data.saved_benefits != null && data.saved_benefits != '' && parseFloat(data.saved_benefits) > 0 ? formatMetalWeight(data.saved_benefits) : '-') + '</span></td>' +
      '<td><span class="row-receipt-no">' + data.receipt_no + '</span></td>' +
      '<td><span class="row-payment-status-text">' + payment_status + '</span>' +
      '<input type="hidden" class="row-payment-status" value="' + data.payment_status + '">' +
      '<input type="hidden" class="row-gst-amount" value="' + (parseFloat(data.gst_amount) || 0) + '">' +
      '<input type="hidden" class="row-acc-start-date" value="' + (data.acc_start_date || '') + '">' +
      '<input type="hidden" class="row-original-date" value="' + data.date_payment + '">' +
      '<input type="hidden" class="row-added-by" value="' + (data.added_by || 0) + '">' +
      '</td>' +
      '</tr>';
  });
  var note = hasOnlinePayment ? "Do not allow edit permission for online payments on the payment date and payment status columns." : "";
  $('#note').html(note);
  $('#table_paymnt_list > tbody').html(srHTML);
  $(".update_pay").show();
  $("#cancel_pay").show();
}

// ==================== RENDER ACCOUNT TABLE (multi-row) ====================
function set_acc_table_multi(results) {
  var srHTML = '';
  // Store scheme data per row for maturity recalculation
  window._accRowSchData = {};
  results.forEach(function(item) {
    var data = item.data;
    var rowIdx = item.rowIndex;
    var startDateFormatted = data.start_date ? data.start_date.substring(0, 10) : '';
    var maturityDateDisplay = data.display_maturity_date || '-';
    // Cache scheme data for maturity recalculation
    window._accRowSchData[rowIdx] = {
      maturity_days: parseInt(data.maturity_days) || 0,
      total_installments: parseInt(data.total_installments) || 0,
      installment_cycle: parseInt(data.installment_cycle) || 0,
      ins_days_duration: parseInt(data.ins_days_duration) || 0
    };
    srHTML += '<tr data-row="' + rowIdx + '" data-accid="' + data.id_scheme_account + '"' +
      ' data-original-mobile="' + data.mobile + '"' +
      ' data-original-startdate="' + startDateFormatted + '">' +
      '<td>' + rowIdx + '</td>' +
      '<td><span class="row-id-sch-acc">' + data.id_scheme_account + '</span></td>' +
      '<td><input type="text" minlength="10" maxlength="10" onkeypress="return /^[0-9]$/i.test(event.key)" class="row-cus-mobile form-control input-sm" value="' + data.mobile + '" style="width:120px;margin:0 auto;text-align:center;"></td>' +
      '<td><span class="row-cust-id">' + data.id_customer + '</span></td>' +
      '<td><span class="row-account-name">' + data.account_name + '</span></td>' +
      '<td><span class="row-acc-number">' + (data.scheme_acc_number && data.scheme_acc_number !== 'null' ? data.scheme_acc_number : '<em style="color:#999;">Not Allocated</em>') + '</span></td>' +
      '<td><input type="text" value="' + startDateFormatted + '" class="form-control input-sm row-acc-start-date" data-date-format="yyyy-mm-dd" style="width:120px;margin:0 auto;text-align:center;" /></td>' +
      '<td><span class="row-acc-maturity-date" style="font-weight:bold;color:#3c8dbc;">' + maturityDateDisplay + '</span></td>' +
      '</tr>';
  });
  $('#table_acc_list > tbody').html(srHTML);
  // Initialize datepickers for each row
  $('#table_acc_list tbody tr').each(function() {
    var $row = $(this);
    $row.find('.row-acc-start-date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true
    });
  });
  $(".update_acc").show();
  $("#cancel_acc").show();
}

// ==================== ACCOUNT START DATE CHANGE (per-row maturity recalc) ====================
$('body').on('changeDate', '.row-acc-start-date', function () {
  var $row = $(this).closest('tr');
  var rowIdx = $row.data('row');
  var schData = window._accRowSchData ? window._accRowSchData[rowIdx] : {};
  var newStartDate = $(this).val();
  if (newStartDate && schData) {
    var startDt = new Date(newStartDate);
    var maturityDt;
    if (schData.maturity_days > 0) {
      maturityDt = new Date(startDt.getTime() + schData.maturity_days * 86400000);
    } else if (schData.total_installments > 0) {
      if (schData.installment_cycle === 0) {
        maturityDt = new Date(startDt.getFullYear(), startDt.getMonth() + schData.total_installments, startDt.getDate());
      } else if (schData.installment_cycle === 1) {
        maturityDt = new Date(startDt.getTime() + schData.total_installments * 86400000);
      } else if (schData.installment_cycle === 2) {
        maturityDt = new Date(startDt.getTime() + (schData.ins_days_duration * schData.total_installments) * 86400000);
      }
    }
    if (maturityDt) {
      var yy = maturityDt.getFullYear();
      var mm = ('0' + (maturityDt.getMonth() + 1)).slice(-2);
      var dd = ('0' + maturityDt.getDate()).slice(-2);
      $row.find('.row-acc-maturity-date').text(yy + '-' + mm + '-' + dd);
    }
  }
});

// ==================== PAYMENT DATE PICKER INIT (per-row) ====================
$('body').on('focus', '.row-pay-datetimepicker', function () {
  if ($(this).data('datetimepicker')) return; // already initialized
  const today = new Date();
  $(this).datetimepicker({
    format: 'yyyy-mm-dd hh:ii:ss',
    endDate: today,
    minView: 2,
    autoclose: true,
    timezone: 'GMT'
  });
});

// ==================== PAYMENT DATE CHANGE (per-row auto-update) ====================
$('body').on('changeDate', '.row-pay-datetimepicker', function () {
  var $picker = $(this);
  var $row = $picker.closest('tr');
  var rowIdx = $row.data('row');
  var payId = $row.data('payid');
  var selectedDate = format_date($picker.val());
  var currentAccId = $row.find('.row-sch-account-id').val();

  if (currentAccId && $.isNumeric(currentAccId)) {
    $.ajax({
      url: base_url + 'index.php/admin_reports/editAccOrPayments/get_acc_start_date',
      type: 'POST',
      data: { id_scheme_account: currentAccId },
      dataType: 'json',
      success: function (res) {
        var accStartDate = (res && res.acc_start_date) ? res.acc_start_date : '';
        $row.find('.row-acc-start-date').val(accStartDate);
        if (accStartDate && selectedDate < accStartDate) {
          $.toaster({ priority: 'danger', title: 'Error!', message: 'Row #' + rowIdx + ' (Pay ID: ' + payId + '): Payment date cannot be before start date (' + accStartDate + ').' });
          var originalDate = $row.find('.row-original-date').val();
          $picker.datetimepicker('remove');
          $picker.val(originalDate);
          return;
        }
        // Auto-update metal rate, weight, and benefits for this row
        updateRowMetalRate($row, selectedDate);
      },
      error: function () {
        updateRowMetalRate($row, selectedDate);
      }
    });
  } else {
    updateRowMetalRate($row, selectedDate);
  }
});

// ==================== PER-ROW METAL RATE + WEIGHT + BENEFIT UPDATE ====================
function updateRowMetalRate($row, date_pay) {
  // Skip weight recalculation for pure amount schemes
  // type 0 = Amount scheme, flexible_sch_type 1 = Amount flexible
  var schemeType = parseInt($row.data('scheme-type')) || 0;
  var flexType = parseInt($row.data('flexible-type')) || 0;
  var isAmountOnly = (schemeType == 0 || (schemeType == 3 && flexType == 1));

  if (isAmountOnly) {
    // For amount-only schemes, just update metal rate display (no weight conversion)
    var my_Date = new Date();
    $.ajax({
      url: base_url + "index.php/admin_payment/getMetalRateBydate?nocache=" + my_Date.getUTCSeconds(),
      data: { 'date_pay': date_pay },
      dataType: "JSON",
      type: "POST",
      success: function (data) {
        $row.find('.row-metal-rate').text(data);
      }
    });
    return;
  }

  var my_Date = new Date();
  $.ajax({
    url: base_url + "index.php/admin_payment/getMetalRateBydate?nocache=" + my_Date.getUTCSeconds(),
    data: { 'date_pay': date_pay },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      $row.find('.row-metal-rate').text(data);
      var amount = parseFloat($row.find('.row-pay-amt').text()) || 0;
      var met_rate = parseFloat(data) || 0;
      var gst_amount = parseFloat($row.find('.row-gst-amount').val()) || 0;
      var weight_amount = amount - gst_amount;
      if (met_rate > 0 && weight_amount > 0) {
        $row.find('.row-metal-weight').text(formatMetalWeight(weight_amount / met_rate));
      } else {
        $row.find('.row-metal-weight').text(formatMetalWeight(0));
      }
      // Calculate and update digi benefit weight
      var currentAccId = $row.find('.row-sch-account-id').val();
      if (currentAccId && $.isNumeric(currentAccId)) {
        $.ajax({
          url: base_url + 'index.php/admin_reports/editAccOrPayments/get_digi_benefit',
          type: 'POST',
          data: {
            id_scheme_account: currentAccId,
            date_payment: date_pay,
            payment_amount: amount,
            metal_rate: met_rate,
            metal_weight: $row.find('.row-metal-weight').text()
          },
          dataType: 'json',
          success: function (benefitRes) {
            if (benefitRes && benefitRes.is_digi == 1 && parseFloat(benefitRes.saved_benefits) > 0) {
              $row.find('.row-saved-benefits').text(formatMetalWeight(benefitRes.saved_benefits));
            } else {
              $row.find('.row-saved-benefits').text('-');
            }
          }
        });
      }
    }
  });
}

function format_date(date) {
  var d = new Date(date),
    month = '' + (d.getMonth() + 1),
    day = '' + d.getDate(),
    year = d.getFullYear();
  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;
  return [year, month, day].join('-');
}

// ==================== ACCOUNT MOBILE LOOKUP (per-row) ====================
$('body').on('keyup', '.row-cus-mobile', function () {
  var $row = $(this).closest('tr');
  var mob = $(this).val().trim();
  if (mob.length > 0 && mob.length < 10) {
    $row.find('.row-cust-id').text('-');
  }
  if (mob.length == 10) {
    $.ajax({
      url: base_url + 'index.php/admin_customer/ajax_get_customers_list',
      data: { 'mobile': mob },
      dataType: "JSON",
      type: "POST",
      success: function (data) {
        var rowIdx = $row.data('row');
        if (data.length > 0)
          $row.find('.row-cust-id').text(data[0].id_customer);
        else
          $.toaster({ priority: 'warning', title: 'Warning!', message: 'Row #' + rowIdx + ': No customer found for this mobile number.' });
      }
    });
  }
});

// ==================== PAYMENT UPDATE (multi-row validation + sequential update) ====================
// Helper: get installment slot key for cross-row conflict detection
function getInstallmentSlotKey(dateStr, installmentCycle, insDaysDuration, accStartDate) {
  var d = format_date(dateStr);
  if (installmentCycle == 0) {
    // Monthly: key = YYYY-MM
    return d.substring(0, 7);
  } else if (installmentCycle == 1 || installmentCycle == 3) {
    // Daily: key = YYYY-MM-DD
    return d;
  } else if (installmentCycle == 2 && insDaysDuration > 0 && accStartDate) {
    // Custom days: key = cycle index
    var startMs = new Date(accStartDate).getTime();
    var payMs = new Date(d).getTime();
    var daysDiff = Math.floor((payMs - startMs) / 86400000);
    var cycleIdx = Math.floor(daysDiff / insDaysDuration);
    return 'cycle_' + cycleIdx;
  }
  return d; // fallback
}

$('.update_pay').on("click", function () {
  var $rows = $('#table_paymnt_list tbody tr');
  if ($rows.length === 0) return;

  // --- Cross-row installment conflict detection ---
  var slotMap = {}; // { "accId_slotKey": [rowIdx, ...] }
  var crossRowErrors = [];
  $rows.each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var payId = $row.data('payid');
    var currentDate = $row.find('.row-pay-datetimepicker').val();
    var originalDate = $row.data('original-date');
    var currentAccId = $row.find('.row-sch-account-id').val();
    var installmentCycle = parseInt($row.data('installment-cycle')) || 0;
    var insDaysDuration = parseInt($row.data('ins-days-duration')) || 0;
    var accStartDate = $row.data('acc-start') || '';
    var paymentChances = parseInt($row.data('payment-chances')) || 0;
    
    // Only check for single-payment schemes where date was changed
    if (paymentChances == 0 && format_date(currentDate) !== format_date(originalDate)) {
      var slotKey = currentAccId + '_' + getInstallmentSlotKey(currentDate, installmentCycle, insDaysDuration, accStartDate);
      if (!slotMap[slotKey]) slotMap[slotKey] = [];
      slotMap[slotKey].push({ rowIdx: rowIdx, payId: payId });
    }
  });
  // Check for conflicts
  for (var key in slotMap) {
    if (slotMap[key].length > 1) {
      var ids = slotMap[key].map(function(r) { return 'Row #' + r.rowIdx + ' (Pay ID: ' + r.payId + ')'; });
      crossRowErrors.push(ids.join(' and ') + ' would land on the same installment slot for Scheme Account ' + key.split('_')[0] + '.');
    }
  }
  if (crossRowErrors.length > 0) {
    crossRowErrors.forEach(function(err) {
      $.toaster({ priority: 'danger', title: 'Conflict!', message: err });
    });
    return;
  }

  // Validate all rows first via AJAX
  var validationPromises = [];
  var validationErrors = [];

  $rows.each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var payId = $row.data('payid');
    var currentDate = format_date($row.find('.row-pay-datetimepicker').val());
    var currentAccId = $row.find('.row-sch-account-id').val();
    var deferred = $.Deferred();

    if (currentAccId && $.isNumeric(currentAccId)) {
      $.ajax({
        url: base_url + 'index.php/admin_reports/editAccOrPayments/get_acc_start_date',
        type: 'POST',
        data: { id_scheme_account: currentAccId },
        dataType: 'json',
        success: function (res) {
          var accStartDate = (res && res.acc_start_date) ? res.acc_start_date : '';
          if (accStartDate && currentDate < accStartDate) {
            validationErrors.push('Row #' + rowIdx + ' (Pay ID: ' + payId + '): Date ' + currentDate + ' is before start date ' + accStartDate);
            $row.css('background-color', '#f2dede');
          }
          deferred.resolve();
        },
        error: function() { deferred.resolve(); }
      });
    } else {
      deferred.resolve();
    }
    validationPromises.push(deferred.promise());
  });

  $.when.apply($, validationPromises).then(function() {
    if (validationErrors.length > 0) {
      validationErrors.forEach(function(err) {
        $.toaster({ priority: 'danger', title: 'Validation Error!', message: err });
      });
      return;
    }
    $('#payupdate_confirm').modal('show');
  });
});

$(".confirm_pay_upd").on("click", function (e) {
  e.preventDefault();
  $('#payupdate_confirm').modal('hide');
  var $rows = $('#table_paymnt_list tbody tr');
  var successCount = 0;
  var failCount = 0;
  var noChangeCount = 0;
  var processedCount = 0;
  var totalRows = $rows.length;

  $rows.each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var post_data = {
      date_payment: format_date($row.find('.row-pay-datetimepicker').val()),
      id_scheme_account: $row.find('.row-sch-account-id').val(),
      id_payment: $row.find('.row-id-payment').text(),
      payment_amount: $row.find('.row-pay-amt').text(),
      metal_rate: $row.find('.row-metal-rate').text(),
      metal_weight: $row.find('.row-metal-weight').text(),
      receipt_no: $row.find('.row-receipt-no').text(),
      payment_status: $row.find('.row-payment-status').val()
    };
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/updatePaymentDetails',
      dataType: 'json',
      data: post_data,
      success: function (data) {
        if (data.status && data.no_change) {
          noChangeCount++;
          $row.css('background-color', '#f5f5f5');
          $row.find('td:last').after('<td style="min-width:100px;"><span class="text-muted"><i class="fa fa-minus-circle"></i> No changes</span></td>');
        } else if (data.status) {
          successCount++;
          $row.css('background-color', '#dff0d8');
          $row.find('td:last').after('<td style="min-width:100px;"><span class="text-success"><i class="fa fa-check"></i> Updated</span></td>');
        } else {
          failCount++;
          $row.css('background-color', '#f2dede');
          $row.find('td:last').after('<td style="min-width:100px;"><span class="text-danger"><i class="fa fa-times"></i> ' + data.msg + '</span></td>');
          $.toaster({ priority: 'danger', title: 'Update Failed!', message: 'Row #' + rowIdx + ' (Pay ID: ' + post_data.id_payment + '): ' + data.msg });
        }
      },
      error: function() {
        failCount++;
        $row.css('background-color', '#f2dede');
        $row.find('td:last').after('<td style="min-width:100px;"><span class="text-danger"><i class="fa fa-times"></i> Server Error</span></td>');
      },
      complete: function() {
        processedCount++;
        if (processedCount === totalRows) {
          if (failCount === 0 && noChangeCount === totalRows) {
            $.toaster({ priority: 'info', title: 'Info', message: 'No changes detected in any payment row.' });
          } else if (failCount === 0) {
            $.toaster({ priority: 'success', title: 'Success!', message: successCount + ' updated, ' + noChangeCount + ' unchanged out of ' + totalRows + ' payment(s).' });
            if (successCount > 0) setTimeout(function() { window.location.reload(); }, 2000);
          } else {
            $.toaster({ priority: 'warning', title: 'Completed', message: successCount + ' updated, ' + failCount + ' failed, ' + noChangeCount + ' unchanged out of ' + totalRows + ' payment(s).' });
          }
        }
      }
    });
  });
});

// ==================== ACCOUNT UPDATE (multi-row) ====================
$(".update_acc").on("click", function () {
  var $rows = $('#table_acc_list tbody tr');
  if ($rows.length === 0) return;

  // Validate each row's mobile
  var hasErrors = false;
  $rows.each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var mobile = $row.find('.row-cus-mobile').val().trim();
    if (mobile.length != 10) {
      $.toaster({ priority: 'warning', title: 'Warning!', message: 'Row #' + rowIdx + ': Invalid mobile number. Must be 10 digits.' });
      $row.find('.row-cus-mobile').focus();
      $row.css('background-color', '#f2dede');
      hasErrors = true;
    }
  });
  if (hasErrors) return;

  var successCount = 0;
  var failCount = 0;
  var noChangeCount = 0;
  var processedCount = 0;
  var totalRows = $rows.length;

  $rows.each(function() {
    var $row = $(this);
    var rowIdx = $row.data('row');
    var currentMobile = $row.find('.row-cus-mobile').val().trim();
    var currentStartDate = $row.find('.row-acc-start-date').val().trim();
    var originalMobile = String($row.data('original-mobile') || '').trim();
    var originalStartDate = String($row.data('original-startdate') || '').trim();

    // No-change detection: skip if nothing changed
    if (currentMobile === originalMobile && currentStartDate === originalStartDate) {
      noChangeCount++;
      processedCount++;
      $row.css('background-color', '#f5f5f5');
      $row.find('td:last').after('<td style="min-width:100px;"><span class="text-muted"><i class="fa fa-minus-circle"></i> No changes</span></td>');
      if (processedCount === totalRows) {
        if (noChangeCount === totalRows) {
          $.toaster({ priority: 'info', title: 'Info', message: 'No changes detected in any account row.' });
        } else if (failCount === 0) {
          $.toaster({ priority: 'success', title: 'Success!', message: successCount + ' updated, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
          if (successCount > 0) setTimeout(function() { window.location.reload(); }, 2000);
        } else {
          $.toaster({ priority: 'warning', title: 'Completed', message: successCount + ' updated, ' + failCount + ' failed, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
        }
      }
      return; // skip this row
    }

    var post_data = {
      id_scheme_account: $row.find('.row-id-sch-acc').text(),
      mobile: currentMobile,
      id_customer: $row.find('.row-cust-id').text(),
      account_name: $row.find('.row-account-name').text(),
      scheme_acc_number: $row.find('.row-acc-number').text(),
      start_date: currentStartDate
    };
    $.ajax({
      type: 'post',
      url: base_url + 'index.php/admin_reports/updateAccountDetails',
      dataType: 'json',
      data: post_data,
      success: function (data) {
        if (data.status) {
          successCount++;
          $row.css('background-color', '#dff0d8');
          $row.find('td:last').after('<td style="min-width:100px;"><span class="text-success"><i class="fa fa-check"></i> Updated</span></td>');
        } else {
          failCount++;
          $row.css('background-color', '#f2dede');
          $row.find('td:last').after('<td style="min-width:100px;"><span class="text-danger"><i class="fa fa-times"></i> ' + data.msg + '</span></td>');
        }
      },
      error: function() {
        failCount++;
        $row.css('background-color', '#f2dede');
        $row.find('td:last').after('<td style="min-width:100px;"><span class="text-danger"><i class="fa fa-times"></i> Server Error</span></td>');
      },
      complete: function() {
        processedCount++;
        if (processedCount === totalRows) {
          if (failCount === 0 && noChangeCount === totalRows) {
            $.toaster({ priority: 'info', title: 'Info', message: 'No changes detected in any account row.' });
          } else if (failCount === 0) {
            $.toaster({ priority: 'success', title: 'Success!', message: successCount + ' updated, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
            if (successCount > 0) setTimeout(function() { window.location.reload(); }, 2000);
          } else {
            $.toaster({ priority: 'warning', title: 'Completed', message: successCount + ' updated, ' + failCount + ' failed, ' + noChangeCount + ' unchanged out of ' + totalRows + ' account(s).' });
          }
        }
      }
    });
  });
});

// Cancel Account Edit — reset only the account section
$("#cancel_acc").on("click", function (e) {
  e.preventDefault();
  e.stopPropagation();
  // Clear the table body
  $('#table_acc_list > tbody').html('');
  // Clear the search input
  $('#sch_acc_id').val('');
  // Hide update/cancel buttons
  $('.update_acc').hide();
  $('#cancel_acc').hide();
  return false;
});

// Cancel Payment Edit — reset only the payment section
$("#cancel_pay").on("click", function (e) {
  e.preventDefault();
  e.stopPropagation();
  // Clear the table body
  $('#table_paymnt_list > tbody').html('');
  // Clear the search input
  $('#pay_id').val('');
  // Hide update/cancel buttons
  $('.update_pay').hide();
  $('#cancel_pay').hide();
  // Clear any notes
  $('#note').html('');
  return false;
});
/*ends*/
$("#gen_transid").on("click", function () {
  $("#khimji_msg").text("");
  var post_data = { id_payment: $("#trans_idpay").val() };
  console.log(post_data);
  $.ajax({
    type: "post",
    url: base_url + "index.php/admin_reports/generateTransUniqId",
    dataType: "json",
    data: post_data,
    success: function (data) {
      console.log(data);
      location.reload(true);
      /*if(data.status)
      {
        $('#khimji_msg').text(data.msg);
      }
      else
      {
        $('#khimji_msg').text(data.msg);
      }*/
    },
  });
});
$("#gen_accrcpt").on("click", function () {
  $('#khimji_msg').text('');
  var post_data = {
    //from_date : ($('#gen_fromdt').val()),
    payId: $("#payId").val()
  };
  console.log(post_data);
  $.ajax({
    type: 'post',
    url: base_url + 'index.php/khimji_services/generateAcNoOrReceiptNoById',
    dataType: 'json',
    data: post_data,
    success: function (data) {
      location.reload(true);
      console.log(data);
      //$('#khimji_post').text(data.post);
    }
  });
});
//Code added by Durga starts here 13-06-2023
//Function to get  scheme account number based on account number settings 
function getSchAccNumber_Format(is_lucky_draw = '', scheme_group_code = '', scheme_acc_number = '', schemeaccNo_displayFrmt = '', scheme_wise_acc_no = '', acc_branch = '', start_year = '', code = '') {
  console.log(code);
  var scheme_acc_number;
  if (is_lucky_draw == 1) {
    scheme_acc_number = scheme_group_code + ' ' + scheme_acc_number;
  }
  else {
    if (schemeaccNo_displayFrmt == 0) {   //only acc num
      scheme_acc_number = scheme_acc_number;
    }
    else if (schemeaccNo_displayFrmt == 1) { //based on acc number generation setting
      if (scheme_wise_acc_no == 0) {
        scheme_acc_number = scheme_acc_number;
      } else if (scheme_wise_acc_no == 1) {
        scheme_acc_number = acc_branch + '-' + scheme_acc_number;
      } else if (scheme_wise_acc_no == 2) {
        scheme_acc_number = code + '-' + scheme_acc_number;
      } else if (scheme_wise_acc_no == 3) {
        scheme_acc_number = code + '' + acc_branch + '-' + scheme_acc_number;
      } else if (scheme_wise_acc_no == 4) {
        scheme_acc_number = start_year + '-' + scheme_acc_number;
      } else if (scheme_wise_acc_no == 5) {
        scheme_acc_number = start_year + '' + code + '-' + scheme_acc_number;
      } else if (scheme_wise_acc_no == 6) {
        scheme_acc_number = start_year + '' + code + '' + acc_branch + '-' + scheme_acc_number;
      }
    }
    else if (schemeaccNo_displayFrmt == 2) {  //customised
      scheme_acc_number = scheme_acc_number;
    }
  }
  return scheme_acc_number;
}
//Function to get receipt number based on receipt number settings 
function getReceiptNumber_Format(receiptNo_displayFrmt = '', scheme_wise_receipt = '', acc_branch = '', start_year = '', code = '', receipt_no = '') {
  var receipt_no;
  if (receiptNo_displayFrmt == 0) {   //only acc num
    receipt_no = receipt_no;
  }
  else if (receiptNo_displayFrmt == 1) { //based on acc number generation setting
    if (scheme_wise_receipt == 1) {
      receipt_no = receipt_no;
    }
    else if (scheme_wise_receipt == 2) {
      receipt_no = acc_branch + '-' + receipt_no;
    }
    else if (scheme_wise_receipt == 3) {
      receipt_no = code + '-' + receipt_no;
    }
    else if (scheme_wise_receipt == 4) {
      receipt_no = code + '' + acc_branch + '-' + receipt_no;
    }
    else if (scheme_wise_receipt == 5) {
      receipt_no = start_year + '-' + receipt_no;
    }
    else if (scheme_wise_receipt == 6) {
      receipt_no = start_year + '' + code + '' + acc_branch + '-' + receipt_no;
    }
  }
  else if (receiptNo_displayFrmt == 2) {  //customised
    receipt_no = receipt_no;
  }
  return receipt_no;
}
//Code added by Durga ends here 13-06-2023
//Scheme wise mode wise report starts here
$('#search_mode_list').on('click', function () {
  var from_date = $('#rpts_payments1').text();
  var to_date = $('#rpts_payments2').text();
  var id_scheme = $('#scheme_select').val();
  var id_branch = $('#id_branch').val();
  generate_paymode_groupwise_list(from_date, to_date, id_scheme, id_branch);
});
function generate_paymode_groupwise_list(
  from_date = "",
  to_date = "",
  id = "",
  id_branch = ""
) {
  var branch_name = getBranchTitle();
  title = get_title(
    from_date,
    to_date,
    "Scheme Wise Mode Wise Report - " + branch_name
  );
  $("#mode_wise_daterange").text(from_date + " to " + to_date);
  var trHTML = "";
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    url: base_url + "index.php/reports/payment_modeandgroupwise_datalist",
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          id: id,
          id_branch: id_branch,
        }
        : "",
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var online_line_total = 0;
      var offline_line_total = 0;
      var cash_subtotal = 0;
      var cash_grandtotal = 0;
      var card_subtotal = 0;
      var card_grandtotal = 0;
      var nb_subtotal = 0;
      var nb_grandtotal = 0;
      var upi_subtotal = 0;
      var upi_grandtotal = 0;
      var wallet_subtotal = 0;
      var wallet_grandtotal = 0;
      var chq_subtotal = 0;
      var chq_grandtotal = 0;
      var amount_subtotal = 0;
      var amount_total = 0;
      var amount_grand_total = 0;
      var count = 0;
      //	console.log(data);
      $("div.overlay").css("display", "none");
      $("#payment_mode_group_wise_list > tbody > tr").remove();
      $("#payment_mode_group_wise_list").dataTable().fnClearTable();
      $("#payment_mode_group_wise_list").dataTable().fnDestroy();
      $.each(data, function (key, payment) {
        count = count + 1;
        trHTML +=
          "<tr>" +
          '<td style="text-align:left;" class="report-key"><strong>' +
          key +
          "</strong></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "</tr>";
        amount_total = 0;
        cash_subtotal = 0;
        card_subtotal = 0;
        upi_subtotal = 0;
        nb_subtotal = 0;
        chq_subtotal = 0;
        wallet_subtotal = 0;
        $.each(payment, function (key, items) {
          //checking for NAN value online data
          items.online_cash = isNaN(items.online_cash) ? 0 : items.online_cash;
          items.online_card = isNaN(items.online_card) ? 0 : items.online_card;
          items.online_nb = isNaN(items.online_nb) ? 0 : items.online_nb;
          items.online_upi = isNaN(items.online_upi) ? 0 : items.online_upi;
          items.online_cheque = isNaN(items.online_cheque)
            ? 0
            : items.online_cheque;
          items.online_wallet = isNaN(items.online_wallet)
            ? 0
            : items.online_wallet;
          //checking for NAN value offline data
          items.offline_cash = isNaN(items.offline_cash)
            ? 0
            : items.offline_cash;
          items.offline_card = isNaN(items.offline_card)
            ? 0
            : items.offline_card;
          items.offline_nb = isNaN(items.offline_nb) ? 0 : items.offline_nb;
          items.offline_upi = isNaN(items.offline_upi) ? 0 : items.offline_upi;
          items.offline_cheque = isNaN(items.offline_cheque)
            ? 0
            : items.offline_cheque;
          items.offline_wallet = isNaN(items.offline_wallet)
            ? 0
            : items.offline_wallet;
          online_line_total =
            parseFloat(items.online_cash) +
            parseFloat(items.online_card) +
            parseFloat(items.online_nb) +
            parseFloat(items.online_upi) +
            parseFloat(items.online_cheque) +
            parseFloat(items.online_wallet);
          offline_line_total =
            parseFloat(items.offline_cash) +
            parseFloat(items.offline_card) +
            parseFloat(items.offline_nb) +
            parseFloat(items.offline_upi) +
            parseFloat(items.offline_cheque) +
            parseFloat(items.offline_wallet);
          if (
            items.online_cash != 0 ||
            items.online_card != 0 ||
            items.online_nb != 0 ||
            items.online_upi != 0 ||
            items.online_cheque != 0 ||
            items.online_wallet != 0
          ) {
            trHTML +=
              "<tr>" +
              "<td>" +
              items.date_payment +
              "</td>" +
              '<td style="color:blue;font-weight:bold;">Online</td>' +
              "<td>" +
              (items.online_cash != 0
                ? formatCurrency.format(items.online_cash)
                : "") +
              "</td>" +
              "<td>" +
              (items.online_card != 0
                ? formatCurrency.format(items.online_card)
                : "") +
              "</td>" +
              "<td>" +
              (items.online_nb != 0
                ? formatCurrency.format(items.online_nb)
                : "") +
              "</td>" +
              "<td>" +
              (items.online_upi != 0
                ? formatCurrency.format(items.online_upi)
                : "") +
              "</td>" +
              "<td>" +
              (items.online_cheque != 0
                ? formatCurrency.format(items.online_cheque)
                : "") +
              "</td>" +
              "<td>" +
              (items.online_wallet != 0
                ? formatCurrency.format(items.online_wallet)
                : "") +
              "</td>" +
              "<td>" +
              (online_line_total != 0
                ? formatCurrency.format(online_line_total)
                : "") +
              "</td>" +
              "</tr>";
          }
          if (
            items.offline_cash != 0 ||
            items.offline_card != 0 ||
            items.offline_nb != 0 ||
            items.offline_nb != 0 ||
            items.offline_cheque != 0 ||
            items.offline_wallet != 0
          ) {
            trHTML +=
              "<tr>" +
              "<td>" +
              items.date_payment +
              "</td>" +
              '<td style="color:orange;font-weight:bold;">Offline</td>' +
              "<td>" +
              (items.offline_cash != 0
                ? formatCurrency.format(items.offline_cash)
                : "") +
              "</td>" +
              "<td>" +
              (items.offline_card != 0
                ? formatCurrency.format(items.offline_card)
                : "") +
              "</td>" +
              "<td>" +
              (items.offline_nb != 0
                ? formatCurrency.format(items.offline_nb)
                : "") +
              "</td>" +
              "<td>" +
              (items.offline_upi != 0
                ? formatCurrency.format(items.offline_upi)
                : "") +
              "</td>" +
              "<td>" +
              (items.offline_cheque != 0
                ? formatCurrency.format(items.offline_cheque)
                : "") +
              "</td>" +
              "<td>" +
              (items.offline_wallet != 0
                ? formatCurrency.format(items.offline_wallet)
                : "") +
              "</td>" +
              "<td>" +
              (offline_line_total != 0
                ? formatCurrency.format(offline_line_total)
                : "") +
              "</td>" +
              "</tr>";
          }
          amount_subtotal = online_line_total + offline_line_total;
          cash_subtotal +=
            parseInt(items.offline_cash) + parseInt(items.online_cash);
          card_subtotal +=
            parseInt(items.offline_card) + parseInt(items.online_card);
          nb_subtotal += parseInt(items.offline_nb) + parseInt(items.online_nb);
          upi_subtotal +=
            parseInt(items.offline_upi) + parseInt(items.online_upi);
          chq_subtotal +=
            parseInt(items.offline_cheque) + parseInt(items.online_cheque);
          wallet_subtotal +=
            parseInt(items.offline_wallet) + parseInt(items.online_wallet);
          amount_total += amount_subtotal;
        });
        amount_grand_total += amount_total;
        cash_grandtotal += cash_subtotal;
        card_grandtotal += card_subtotal;
        upi_grandtotal += upi_subtotal;
        chq_grandtotal += chq_subtotal;
        nb_grandtotal += nb_subtotal;
        wallet_grandtotal += wallet_subtotal;
        trHTML +=
          "<tr>" +
          '<td class="report-sub-total">SUB TOTAL</td>' +
          "<td></td>" +
          '<td class="report-sub-total">' +
          (cash_subtotal != 0 ? formatCurrency.format(cash_subtotal) : "") +
          "</td>" +
          '<td class="report-sub-total">' +
          (card_subtotal != 0 ? formatCurrency.format(card_subtotal) : "") +
          "</td>" +
          '<td class="report-sub-total">' +
          (nb_subtotal != 0 ? formatCurrency.format(nb_subtotal) : "") +
          "</td>" +
          '<td class="report-sub-total">' +
          (upi_subtotal != 0 ? formatCurrency.format(upi_subtotal) : "") +
          "</td>" +
          '<td class="report-sub-total">' +
          (chq_subtotal != 0 ? formatCurrency.format(chq_subtotal) : "") +
          "</td>" +
          '<td class="report-sub-total">' +
          (wallet_subtotal != 0 ? formatCurrency.format(wallet_subtotal) : "") +
          "</td>" +
          '<td class="report-sub-total">' +
          (amount_total != 0 ? formatCurrency.format(amount_total) : "") +
          "</td>" +
          "</tr>";
      });
      trHTML +=
        "<tr>" +
        '<td class="report-grand-total">GRAND TOTAL</td>' +
        "<td></td>" +
        '<td class="report-grand-total">' +
        (cash_grandtotal != 0 ? formatCurrency.format(cash_grandtotal) : "") +
        "</td>" +
        '<td class="report-grand-total">' +
        (card_grandtotal != 0 ? formatCurrency.format(card_grandtotal) : "") +
        "</td>" +
        '<td class="report-grand-total">' +
        (nb_grandtotal != 0 ? formatCurrency.format(nb_grandtotal) : "") +
        "</td>" +
        '<td class="report-grand-total">' +
        (upi_grandtotal != 0 ? formatCurrency.format(upi_grandtotal) : "") +
        "</td>" +
        '<td class="report-grand-total">' +
        (chq_grandtotal != 0 ? formatCurrency.format(chq_grandtotal) : "") +
        "</td>" +
        '<td class="report-grand-total">' +
        (wallet_grandtotal != 0
          ? formatCurrency.format(wallet_grandtotal)
          : "") +
        "</td>" +
        '<td class="report-grand-total">' +
        (amount_grand_total != 0
          ? formatCurrency.format(amount_grand_total)
          : "") +
        "</td>" +
        "</tr>";
      $("#payment_mode_group_wise_list > tbody").html(trHTML);
      if (!$.fn.DataTable.isDataTable("#payment_mode_group_wise_list")) {
        if (count != 0) {
          oTable = $("#payment_mode_group_wise_list").dataTable({
            bSort: false,
            bInfo: false,
            scrollX: "100%",
            dom: "lBfrtip",
            pageLength: 25,
            lengthMenu: [
              [-1, 25, 50, 100, 250],
              ["All", 25, 50, 100, 250],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: "",
                messageTop: title,
                orientation: "landscape",
                customize: function (win) {
                  $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', '10px')
                    .css('font-family', 'sans-serif');

                  // Force body to not generate extra pages
                  $(win.document.body).css({
                    'height': 'auto',
                    'overflow': 'visible',
                    'margin': '0',
                    'padding': '0'
                  });
                  $(win.document.documentElement).css({
                    'height': 'auto',
                    'overflow': 'visible'
                  });

                  // Inject CSS for borders, layout, and page control
                  var css = 'table.dataTable { ' +
                    'border-collapse: collapse !important; ' +
                    'width: 99% !important; ' +
                    'margin: 0 auto !important; ' +
                    'border: 1px solid black !important; ' +
                    'box-sizing: border-box !important; } ' +
                    'table.dataTable th, table.dataTable td { ' +
                    'border: 1px solid black !important; } ' +
                    '@page { size: landscape; margin: 10mm; } ' +
                    'html, body { height: auto !important; min-height: 0 !important; overflow: visible !important; }';
                  var head = win.document.head || win.document.getElementsByTagName('head')[0];
                  var style = win.document.createElement('style');
                  style.type = 'text/css';
                  if (style.styleSheet) {
                    style.styleSheet.cssText = css;
                  } else {
                    style.appendChild(win.document.createTextNode(css));
                  }
                  head.appendChild(style);
                },
              },
              {
                extend: "excel",
                footer: true,
                title:
                  "Scheme Wise Mode Wise Report - " +
                  branch_name +
                  " " +
                  from_date +
                  " to " +
                  to_date,
              },
              {
                extend: "colvis",
                collectionLayout: "fixed columns",
                collectionTitle: "Column visibility control",
              },
            ],
            columnDefs: [
              {
                targets: [2, 3, 4, 5, 6, 7, 8],
                className: "dt-right",
              },
              {
                targets: [0, 1],
                className: "dt-left",
              },
            ],
          });
        } else {
          var brHTML = "";
          brHTML +=
            '<tr><td colspan=9 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
          $("#payment_mode_group_wise_list > tbody").html(brHTML);
        }
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
//Scheme wise mode wise report ends here
//member report starts here 
function get_account_type() {
  $('#account_type_select').append(
    $("<option></option>")
      .attr("value", 0)
      .text("All")
  );
  if (ctrl_page[1] == 'member_report') {
    $('#account_type_select').append(
      $("<option></option>")
        .attr("value", 1)
        .text("Active")
    );
    $('#account_type_select').append(
      $("<option></option>")
        .attr("value", 2)
        .text("Closed")
    );
    $('#account_type_select').select2("val", '');
  }
  else {
    $('#account_type_select').append(
      $("<option></option>")
        .attr("value", 1)
        .text("Pre Closed Accounts")
    );
    $('#account_type_select').append(
      $("<option></option>")
        .attr("value", 2)
        .text("Full Matured Accounts")
    );
    $('#account_type_select').select2("val", 0);
  }
}
$("#account_type_select").select2({
  allowClear: true,
  placeholder: "Select Account Type"
});
$('#member_report_type_select').on('select2:unselecting', function (e) {
  $("#account_type_div").css("display", "none");
  $("#report_type_div").css("margin-left", 450 + "px");
  $("#member_date_div").css("display", "none");
});
$('#member_report_type_select').select2().on("change", function (e) {
  //alert(this.value);
  if (this.value) {
    if (this.value == 2) {
      $("#account_type_select").empty();
      get_account_type();
      $("#account_type_div").css("display", "block");
      $("#report_type_div").css("margin-left", 380 + "px");
      $("#member_date_div").css("display", "block");
    }
    else {
      $("#account_type_div").css("display", "none");
      $("#report_type_div").css("margin-left", 450 + "px");
      $("#member_date_div").css("display", "none");
    }
  }
});
//member_report_type_select filter for member report added manually(static)  starts here
function get_member_account_type() {
  $('#member_report_type_select').append(
    $("<option></option>")
      .attr("value", 1)
      .text("Live Member Report")
  );
  $('#member_report_type_select').append(
    $("<option></option>")
      .attr("value", 2)
      .text("New Member Report")
  );
  $('#member_report_type_select').select2("val", 1);
}
$("#member_report_type_select").select2({
  allowClear: true,
  placeholder: "Select Account Type"
});
//member_report_type_select filter for member report added manually(static) ends here
//load area select starts here
// Area select — AJAX search (lazy, avoids full-table-scan hang on large village tables)
$("#area_select").select2({
  allowClear: true,
  placeholder: "Select Area",
  minimumInputLength: 0,
  ajax: {
    url: base_url + 'index.php/admin_reports/get_area',
    dataType: 'json',
    delay: 300,
    data: function (params) {
      return { term: params.term };
    },
    processResults: function (data) {
      var results = [{ id: 0, text: 'All' }];
      if (data && data.length > 0) {
        $.each(data, function (i, item) {
          results.push({ id: item.id_village, text: item.village_name });
        });
      }
      return { results: results };
    },
    cache: true
  }
});
//load city select ends here
//joined through select box upload  static starts here
function get_joined_through_list() {
  $(".overlay").css('display', 'block');
  $.ajax({
    type: 'GET',
    url: base_url + 'index.php/admin_reports/get_joined_through',
    dataType: 'json',
    success: function (data) {
      if (data.length != 0 && data.length > 0) {
        $.each(data, function (key, item) {
          $('#joined_through_select').append(
            $("<option></option>")
              .attr("value", item.value)
              .text(item.text)
          );
        });
      }
      $('#joined_through_select').select2("val", '');
    }
  });
}
$("#joined_through_select").select2({
  allowClear: true,
  placeholder: "Select Joined Through"
});
//joined through select box upload  static ends here
$('#search_member_list').on('click', function () {
  get_member_report();
});
function get_member_report() {
  $("div.overlay").css("display", "block");
  var from_date = $("#rpts_payments1").text();
  var to_date = $("#rpts_payments2").text();
  $("#member_date_range").text(from_date + " To " + to_date);
  var post_data = {
    from_date: from_date,
    to_date: to_date,
    id_scheme: $("#scheme_select").val(),
    id_branch: $("#branch_select").val(),
    id_village: $("#area_select").val(),
    added_by: $("#joined_through_select").val(),
    emp_code: $("#employee_select").val(),
    report_type: $("#member_report_type_select").val(),
    account_type: $("#account_type_select").val()
  };
  $.ajax({
    type: 'post',
    url: base_url + 'index.php/admin_reports/getMemberReport',
    dataType: 'json',
    data: post_data,
    success: function (data) {
      $("div.overlay").css("display", "none");
      member_data = data.member_data;
      set_member_report(data);
    }
  });
}
function set_member_report(data) {
  var from_date = $("#rpts_payments1").text();
  var to_date = $("#rpts_payments2").text();
  var report_type = $("#member_report_type_select").val();
  var title;
  var amount_subtotal;
  var amount_grandtotal = 0;
  var branch_name = getBranchTitle();
  if (report_type == 2) {
    title = get_title(from_date, to_date, "New Member Report - " + branch_name);
  } else {
    title = get_title("", "", "Live Member Report - " + branch_name);
  }
  //the data for datatable (detailed report )is stored in member_data
  summarytitleHTML = "";
  var summaryhtml = get_member_summary_html(
    data.joined_through_count,
    data.scheme_count_data
  );
  //title=title+summarytitleHTML;
  $("#member_summary_details").html(summaryhtml);
  member_data = data.detailed_data;
  $("div.overlay").css("display", "none");
  $("#member_report_table > tbody > tr").remove();
  $("#member_report_table").dataTable().fnClearTable();
  $("#member_report_table").dataTable().fnDestroy();
  if (member_data != null) {
    var sno,
      count = 0;
    //temporary variable trHTML to format data
    var trHTML = "";
    $.each(member_data, function (key, mem_data) {
      amount_subtotal = 0;
      trHTML +=
        "<tr>" +
        '<td class="report-key"><strong>' +
        key +
        "</strong></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        //'<td></td>'+
        //'<td></td>'+
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>";
      sno = 0;
      $.each(mem_data, function (index, items) {
        sno++;
        count++;
        var added_by = "";
        switch (items.added_by) {
          case "0":
            added_by = "WebApp";
            break;
          case "1":
            added_by = "Admin";
            break;
          case "2":
            added_by = "MobileApp";
            break;
          case "3":
            added_by = "Collection App";
            break;
          /*case '4':
            added_by='Retail App';
            break;
          case '5':
            added_by='Sync';
            break;
          case '6':
            added_by='Import';
            break;*/
          case "4":
            added_by = "Admin";
            break;
          case "5":
            added_by = "Admin";
            break;
          case "6":
            added_by = "Admin";
            break;
        }
        amount_subtotal +=
          items.first_installment_amount != ""
            ? parseFloat(items.first_installment_amount)
            : 0;
        amount_grandtotal +=
          items.first_installment_amount != ""
            ? parseFloat(items.first_installment_amount)
            : 0;
        var member_address = getAddress(
          items.address1,
          items.address2,
          items.address3,
          items.area,
          items.city_name,
          items.state,
          items.country,
          items.state,
          items.pincode
        );
        trHTML +=
          "<tr>" +
          "<td>" +
          sno +
          "</td>" +
          "<td>" +
          items.code +
          "</td>" +
          "<td>" +
          items.scheme_acc_number +
          "</td>" +
          "<td>" +
          items.acc_name +
          "</td>" +
          "<td>" +
          items.mobile +
          "</td>" +
          "<td>" +
          items.cus_name +
          "</td>" +
          "<td>" +
          items.cus_reg_date +
          "</td>" +
          "<td>" +
          items.start_date +
          "</td>" +
          "<td>" +
          (items.first_installment_amount != 0 &&
            items.first_installment_amount != ""
            ? formatCurrency.format(items.first_installment_amount)
            : "") +
          "</td>" +
          "<td>" +
          member_address +
          "</td>" +
          // '<td>'+items.address+'</td>'+
          // '<td>'+items.area+'</td>'+
          // '<td>'+items.city_name+'</td>'+
          "<td>" +
          added_by +
          "</td>" +
          "<td>" +
          items.joined_branch +
          "</td>" +
          "<td>" +
          items.login_employee +
          "</td>" +
          "<td>" +
          items.referred_employee +
          "</td>" +
          "</tr>";
      });
      trHTML +=
        "<tr>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        '<td class="report-sub-total">Sub Total</td>' +
        '<td class="report-sub-total">' +
        (amount_subtotal != 0
          ? formatCurrency.format(parseFloat(amount_subtotal.toFixed(3)))
          : "") +
        "</td>" +
        //'<td></td>'+
        //'<td></td>'+
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>";
    });
    trHTML +=
      "<tr>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      '<td class="report-grand-total">Grand Total</td>' +
      '<td class="report-grand-total">' +
      (amount_grandtotal != 0
        ? formatCurrency.format(parseFloat(amount_grandtotal.toFixed(3)))
        : "") +
      "</td>" +
      //'<td></td>'+
      //'<td></td>'+
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "<td></td>" +
      "</tr>";
    $("#member_account_count").text(count);
    $("#member_report_table > tbody").html(trHTML);
    if (!$.fn.DataTable.isDataTable("#member_report_table")) {
      oTable = $("#member_report_table").dataTable({
        bSort: false,
        bInfo: false,
        scrollX: "100%",
        dom: "lBfrtip",
        pageLength: 25,
        lengthMenu: [
          [-1, 25, 50, 100, 250],
          ["All", 25, 50, 100, 250],
        ],
        buttons: [
          {
            extend: "print",
            footer: true,
            title: "",
            messageTop: title,
            orientation: "landscape",
            customize: function (win) {
              $(win.document.body).find('table')
                .addClass('compact');
              $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', '10px')
                .css('font-family', 'sans-serif');

              // Inject refined CSS for borders and page layout
              var css = 'table.dataTable { ' +
                'border-collapse: collapse !important; ' +
                'width: 99% !important; ' +
                'margin: 0 auto !important; ' +
                'border: 1px solid black !important; ' +
                'box-sizing: border-box !important; } ' +
                'table.dataTable th, table.dataTable td { ' +
                'border: 1px solid black !important; } ' +
                '@page { size: landscape; margin: 10mm; }';
              var head = win.document.head || win.document.getElementsByTagName('head')[0];
              var style = win.document.createElement('style');
              style.type = 'text/css';
              style.media = 'print';
              if (style.styleSheet) {
                style.styleSheet.cssText = css;
              } else {
                style.appendChild(win.document.createTextNode(css));
              }
              head.appendChild(style);
            },
            exportOptions: {
              columns: ":visible",
            },
          },
          {
            extend: "excel",
            footer: true,
            title:
              "Member Report -" +
              branch_name +
              " " +
              from_date +
              " - " +
              to_date,
          },
          {
            extend: "colvis",
            collectionLayout: "fixed columns",
            collectionTitle: "Column visibility control",
          },
        ],
        columnDefs: [
          {
            targets: [1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13],
            className: "dt-left",
          },
          {
            targets: [0, 8],
            className: "dt-right",
          },
        ],
      });
    }
  } else {
    $("#member_account_count").text("");
    var brHTML = "";
    brHTML +=
      '<tr><td colspan=14 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
    $("#member_report_table > tbody").html(brHTML);
  }
}
function get_member_summary_html(joined_through, scheme_count) {
  //console.log(scheme_count);
  var summaryHTML = '';
  if ($("#member_report_type_select").val() == 2) {
    $("#member_summary").text("New Member Summary");
    summarytitleHTML += "<div class='row'><div class='col-md-6' style='margin-left:60px;'><strong>New Member Summary</strong></div>";
  }
  else {
    $("#member_summary").text("Live Member Summary");
    summarytitleHTML += "<br/><br/><div class='row'><div class='col-md-6' style='margin-left:60px;'><strong>LIVE MEMBER SUMMARY</strong></div>";
  }
  var length = Object.keys(scheme_count).length;
  if (length > 0) {
    $("#print_member_summary").css('display', 'block');
    //if scheme account data not null create a table with scheme name and its count
    summaryHTML += "<div class='row'>" +
      "<div class='col-md-6'><table style='width: 75%;margin-left:100px;'>" +
      "<th style='text-align:left;padding-left: 20px;'>SCHEME NAME</th><th style='text-align:right;padding-left: 20px;'>ACCOUNT COUNT</th>" +
      "<tbody>";
    var total_acc_count = 0;
    $.each(scheme_count, function (key, sum_data) {
      summaryHTML += "<tr style='font-weight:bold;align:center;'><td colspan='2' style='padding-left: 20px;'>" + key + "</td></tr>";
      console.log(sum_data);
      var sccount;
      var sccode;
      var sub_sccount = 0;
      $.each(sum_data, function (sch, sum) {
        sccount = sum.length;
        sccode = sum[0].code;
        console.log(sccount);
        console.log(sccode);
        total_acc_count += parseInt(sccount);
        sub_sccount += parseInt(sccount);
        summaryHTML += "<tr><td style='padding-left: 40px;'>" + sccode + "</td><td style='text-align:right;padding-right: 20px;'>" + sccount + "</td></tr>";
      });
      summaryHTML += '<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>';
      summaryHTML += "<tr style='font-weight:bold;'><td class='highlighted-row' style='padding-left: 20px;'>Sub Total </td><td class='highlighted-row' style='text-align:right; padding-right: 20px;'>" + sub_sccount + "</td></tr>";
      summaryHTML += '<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>';
    });
    summaryHTML += "<tr style='font-weight:bold;'><td style='padding-left: 20px;'>Grand Total </td><td style='text-align:right; padding-right: 20px;'>" + total_acc_count + "</td></tr></tbody></table></div>";
    summarytitleHTML += summaryHTML;
  }
  else {
    $("#print_member_summary").css('display', 'none');
    summaryHTML += "<div class='row'>" +
      "<div class='col-md-6' style='text-align:center;color:red;'><strong>No Data Available</strong></div>";
  }
  summarytitleHTML += "<br/><br/>"
  $("#join_summary").text("Join Through Summary");
  summarytitleHTML += "<div class='col-md-6' style='margin-left:60px;'><strong>JOINED THROUGH SUMMARY</strong></div></div>";
  var temp = 0;
  var mode_details = joined_through[0];
  $.each(mode_details, function (key, mode_data) {
    if (temp == 0) {
      if (mode_data > 0) {
        temp = 1;
      }
    }
  });
  if (temp == 0) {
    summaryHTML += "<div class='col-md-6' style='text-align:center;color:red;'><strong>No Data Available</strong></div>" +
      "</div>";
  }
  else {
    //if scheme account data not null create a table with scheme name and its count
    var tempHTML = '';
    tempHTML += "<div class='col-md-6'><table style='width: 75%;margin-left:100px;'>" +
      "<th style='padding-left: 20px;text-align:left;'>MODE NAME</th><th style='padding-left: 20px;text-align:right;'>ACCOUNT COUNT</th>" +
      "<tbody>";
    total_acc_count = 0;
    $.each(mode_details, function (key, sum_data) {
      total_acc_count += parseInt(sum_data);
      tempHTML += "<tr><td style='padding-left: 20px;'>" + key + "</td><td style='text-align:right;margin-right:30px;padding-right: 20px;'>" + (sum_data != 0 ? sum_data : '') + "</td></tr>";
    });
    tempHTML += "<tfoot><tr style='font-weight:bold;'><td style='padding-left: 20px;'>Total :</td><td style='text-align:right;margin-right:30px;padding-right: 20px;'>" + total_acc_count + "</td></tr></tfoot></tbody></table></div>";
    var styleHtml = '<style>td.highlighted-row {' +
      'padding-right:30px;' +
      'border-top: 1px dashed black;' +
      'border-bottom: 1px dashed black;' +
      '}</style>';
    summaryHTML += tempHTML + styleHtml;
    summarytitleHTML += tempHTML + styleHtml;
  }
  //console.log(summaryHTML);
  return summaryHTML;
}
$('#print_member_summary').on('click', function () {
  const printWindow = window.open('', '_blank');
  var print_member_summary;
  var from_date = $("#rpts_payments1").text();
  var to_date = $("#rpts_payments2").text();
  var report_type = $("#member_report_type_select").val();
  var branch_name;
  branch_name = getBranchTitle();
  if (report_type == 2) {
    print_member_summary = get_title(from_date, to_date, "New Member Report - " + branch_name);
  }
  else {
    print_member_summary = get_title('', '', 'Live Member Report - ' + branch_name);
  }
  // console.log(summarytitleHTML);
  print_member_summary += summarytitleHTML;
  var htmlToPrint = '' +
    '<style type="text/css">' +
    'table th, table td {' +
    'border:1px solid #000;' +
    'padding:0.5em;' +
    '}' +
    '' + '.rightstyle{float: right;}'
  '</style>';
  //print_member_summary+=htmlToPrint;
  printWindow.document.write(print_member_summary);
  printWindow.document.close();
  printWindow.print();
  printWindow.close();
});
//member report ends here 
//emp refferral report starts here
function set_referral_table(from_date, to_date, emp_code) {
  my_Date = new Date();
  $("#referral_date_range").html(from_date + " To " + to_date);
  var acc_type = $("#credit_select").val();
  var company_name = $("#company_name").val();
  var print_title = get_title(
    from_date,
    to_date,
    "Employee Reffered Customer Report"
  );
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/get_referral_code_byId?nocache=" +
      my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "POST",
    //data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date}: ''),
    data:
      from_date != "" && to_date != ""
        ? {
          from_date: from_date,
          to_date: to_date,
          emp_code: emp_code,
          acc_type: acc_type,
        }
        : {
          from_date: "",
          to_date: "",
          emp_code: emp_code,
          acc_type: acc_type,
        },
    success: function (data) {
      //alert();
      $("#total_referrals").text(data.accounts.length);
      var accounts = data.accounts;
      console.log(from_date);
      console.log(to_date);
      console.log(accounts);
      var oTable = $("#reff_report").DataTable();
      oTable.clear().draw();
      if (accounts != null && accounts.length > 0) {
        oTable = $("#reff_report").DataTable({
          bDestroy: true,
          bInfo: true,
          bFilter: true,
          bSort: true,
          dom: "lBfrtip",
          pageLength: 25,
          lengthMenu: [
            [-1, 25, 50, 100, 250],
            ["All", 25, 50, 100, 250],
          ],
          buttons: [
            {
              extend: "print",
              footer: true,
              title: "",
              messageTop: print_title,
              customize: function (win) {
                $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "inherit");
              },
            },
            {
              extend: "excel",
              footer: true,
              title:
                "Employee Reffered Customer Report " +
                from_date +
                " To " +
                to_date,
            },
            /*	{
                extend:'pdf',
                footer: true,
                title:'Employee Reffered Customer Report'
              }  */
          ],
          aaData: accounts,
          columnDefs: [
            {
              targets: [3, 4, 5],
              className: "dt-right",
            },
            {
              targets: [0, 1, 2, 6, 7, 8, 9, 10],
              className: "dt-left",
            },
          ],
          aoColumns: [
            { mDataProp: "id_customer" },
            { mDataProp: "cus_name" },
            { mDataProp: "code" },
            { mDataProp: "payment_amount" },
            // 	{"mDataProp":"benefit"},
            {
              mDataProp: function (row, type, val, meta) {
                if (isValid(row.credit_amount) && row.credit_amount != 0) {
                  return row.credit_amount;
                } else {
                  return "";
                }
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                if (isValid(row.debit_amount) && row.debit_amount != 0) {
                  return row.debit_amount;
                } else {
                  return "";
                }
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return (
                  "<p style='font-weight:bold;color:" +
                  (row.issue_type == "Credit" ? "green" : "red") +
                  "'>" +
                  row.issue_type +
                  "</p>"
                );
              },
            },
            { mDataProp: "credit_for" },
            { mDataProp: "scheme_acc_number" },
            { mDataProp: "receipt_no" },
            { mDataProp: "date_transaction" },
          ],
          footerCallback: function (row, data, start, end, display) {
            var api = this.api(),
              data;
            var length = data.length;
            // Remove the formatting to get integer data for summation   /// for total amt footer
            var intVal = function (i) {
              return typeof i === "string"
                ? i.replace(/[\$,]/g, "") * 1
                : typeof i === "number"
                  ? i
                  : 0;
            };
            // Total over all pages
            total = api
              .column(3)
              .data()
              .reduce(function (a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            total_credit_amount = api
              .column(4)
              .data()
              .reduce(function (a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            total_debit_amount = api
              .column(5)
              .data()
              .reduce(function (a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            // Total over this page
            // Update footer
            $(api.column(3).footer()).html(parseFloat(total).toFixed(2));
            $(api.column(4).footer()).html(
              parseFloat(total_credit_amount).toFixed(2)
            );
            $(api.column(5).footer()).html(
              parseFloat(total_debit_amount).toFixed(2)
            );
          },
        });
      }
    },
  });
}
//emp refferral report ends here
function groupBy(list, keyGetter) {
  const map = new Map();
  list.forEach((item) => {
    const key = keyGetter(item);
    const collection = map.get(key);
    if (!collection) {
      map.set(key, [item]);
    } else {
      collection.push(item);
    }
  });
  return map;
}
//to check a value is valid or not 
function isValid(data) {
  if (data == '') {
    return false;
  }
  else if (data == null) {
    return false;
  }
  else if (data == 'undefined') {
    return false;
  }
  else {
    return true;
  }
}
//function to get branch name for title
function getBranchTitle() {
  var login_branch = $("#branch_filter").val();
  var branch_name;
  var selected_branch
  if (isValid(login_branch)) {
    branch_name = $("#login_branch_name").val();
  }
  else {
    if (ctrl_page[1] == 'closed_acc_report') {
      selected_branch = $('#close_branch_select option:selected').toArray().map(item => item.text).join();
    }
    else {
      selected_branch = $('#branch_select option:selected').toArray().map(item => item.text).join();
    }
    if (isValid(selected_branch)) {
      branch_name = selected_branch;
    }
    else {
      branch_name = "All Branch"
    }
  }
  return branch_name;
}
function getAddress(address1, address2, address3, area, city_name, state, country, state, pincode) {
  var address = '';
  if (!isValid(address1) && !isValid(address2) && !isValid(address3) && !isValid(area) && !isValid(city_name) && !isValid(state) && !isValid(country) && !isValid(pincode)) {
    return '-';
  }
  else {
    if (isValid(address1)) {
      address += convert(address1) + ",<br>";
    }
    if (isValid(address2)) {
      address += convert(address2) + ",<br>";
    }
    if (isValid(address3)) {
      address += convert(address3) + ",<br>";
    }
    if (isValid(area)) {
      address += convert(area) + ",<br>";
    }
    if (isValid(city_name)) {
      address += convert(city_name) + ",";
    }
    if (isValid(state)) {
      address += convert(state) + ",";
    }
    if (isValid(country)) {
      address += convert(country) + ",<br>";
    }
    if (isValid(pincode)) {
      address += pincode + ".<br>";
    }
    return address;
  }
}
function convert(sentence) {
  return sentence.charAt(0).toUpperCase() + sentence.slice(1);
}
//Advance payment report starts		
$('#search_gen_pay_list').on('click', function () {
  get_general_advance_list();
});
function get_general_advance_list() {
  var from_date = $("#rpts_payments1").text();
  var to_date = $("#rpts_payments2").text();
  var branch_name = getBranchTitle();
  var title = get_title(
    from_date,
    to_date,
    "General Advance Report - " + branch_name
  );
  $("#gen_adv_report_date_range").text(from_date + " To " + to_date);
  var post_data = {
    from_date: $("#rpts_payments1").text(),
    to_date: $("#rpts_payments2").text(),
    id_branch: $("#branch_select").val(),
    id_scheme: $("#scheme_select").val(),
    mode: $("#mode_select").val(),
    added_by: $("#select_pay_mode").val(),
  };
  $.ajax({
    url:
      base_url +
      "index.php/reports/general_advance_list?nocache=" +
      my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "POST",
    data: post_data,
    success: function (data) {
      var accounts = data.accounts;
      var oTable = $("#gen_adv_rpt_table").DataTable();
      oTable.clear().draw();
      if (accounts != null && accounts.length > 0) {
        oTable = $("#gen_adv_rpt_table").DataTable({
          bDestroy: true,
          bInfo: true,
          bFilter: true,
          bSort: true,
          dom: "lBfrtip",
          pageLength: 25,
          lengthMenu: [
            [-1, 25, 50, 100, 250],
            ["All", 25, 50, 100, 250],
          ],
          aaData: accounts,
          buttons: [
            {
              extend: "print",
              footer: true,
              title: "",
              messageTop: title,
              orientation: "landscape",
              customize: function (win) {
                $(win.document.body).find("table").addClass("compact");
                $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "10px")
                  .css("font-family", "sans-serif");
              },
              exportOptions: {
                columns: ":visible",
              },
            },
            {
              extend: "excel",
              footer: true,
              title:
                "General Advance Report -" +
                branch_name +
                " " +
                from_date +
                " - " +
                to_date,
            },
            {
              extend: "colvis",
              collectionLayout: "fixed columns",
              collectionTitle: "Column visibility control",
            },
          ],
          aoColumns: [
            { mDataProp: "sno" },
            { mDataProp: "date_payment" },
            { mDataProp: "custom_entry_date" },
            { mDataProp: "sch_code" },
            {
              mDataProp: function (row, type, val, meta) {
                return row.group_code != "" ? row.group_code : "-";
              },
            },
            { mDataProp: "scheme_acc_number" },
            { mDataProp: "name" },
            { mDataProp: "mobile" },
            { mDataProp: "receipt_no" },
            { mDataProp: "installment" },
            { mDataProp: "mode" },
            {
              mDataProp: function (row, type, val, meta) {
                return formatCurrency.format(parseFloat(row.payment_amount));
              },
            },
            { mDataProp: "metal_rate" },
            { mDataProp: "metal_weight" },
            { mDataProp: "emp_name" },
            { mDataProp: "transcation_id" },
            { mDataProp: "payment_ref_number" },
            { mDataProp: "pay_type" },
            { mDataProp: "payment_status" },
            { mDataProp: "branch" },
            {
              mDataProp: function (row, type, val, meta) {
                switch (row.paid_through) {
                  case "0":
                    return "Admin";
                    break;
                  case "1":
                    return "Web App";
                    break;
                  case "2":
                    return "Mobile App";
                    break;
                  case "3":
                    return "Collection App";
                    break;
                  case "4":
                    return "Cash Free";
                    break;
                  case "5":
                    return "Sync";
                    break;
                  default:
                    return "-";
                    break;
                }
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.status;
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.remark != "" ? row.remark : "-";
              },
            },
          ],
          footerCallback: function (row, data, start, end, display) {
            if (data.length > 0) {
              var api = this.api(),
                data;
              var intVal = function (i) {
                return typeof i === "string"
                  ? i.replace(/[\$,]/g, "") * 1
                  : typeof i === "number"
                    ? i
                    : 0;
              };
              // Amount Total over this page
              amttotal = api
                .column(11, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(11).footer()).html(formatCurrency.format(amttotal));
            }
          },
          columnDefs: [
            {
              targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15],
              className: "dt-left",
            },
            {
              targets: [11],
              className: "dt-right",
            },
          ],
        });
      }
    },
  });
}
//Advance payment report ends	
//Account ledger - show Advance payment list - starts
$('#gen_adv_btn').on('click', function () {
  get_adv_list_byid();
});
$('.hide_table').on('click', function () {
  $(".adv_table").css('display', 'none');
  $("#gen_adv_btn").css('display', 'block');
  $(".hide_table").css('display', 'none');
});
function get_adv_list_byid() {
  var id = ctrl_page[3];
  console.log(id);
  $.ajax({
    url: base_url + "index.php/admin_reports/general_advance_list_byid?nocache=" + my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "POST",
    data: { 'id': id },
    success: function (data) {
      console.log(data.accounts);
      var accounts = data.accounts;
      if (accounts != null && accounts.length > 0) {
        $(".adv_table").css('display', 'block');
        $("#gen_adv_btn").css('display', 'none');
        $(".hide_table").css('display', 'block');
        var trHTML = '';
        var amt_total = 0, wgt_total = 0;
        $.each(accounts, function (idx, item) {
          amt_total += parseFloat(item.payment_amount);
          wgt_total += parseFloat(item.metal_weight);
          trHTML += '<tr>' +
            '<td>' + item.sno + '</td>' +
            '<td>' + item.date_payment + '</td>' +
            '<td>' + item.payment_mode + '</td>' +
            '<td style="text-align:right;">' + formatCurrency.format(parseFloat(item.payment_amount)) + '</td>' +
            '<td style="text-align:right;">' + formatCurrency.format(parseFloat(item.metal_rate)) + '</td>' +
            '<td style="text-align:right;">' + (formatMetalWeight(item.metal_weight)) + '</td>' +
            '<td>' + item.payment_status + '</td>' +
            '<td>' + item.receipt_no + '</td>' +
            '</tr>';
        });
        trHTML += '<tr style="font-weight:bold;">' +
          '<td></td>' +
          '<td></td>' +
          '<td>Total</td>' +
          '<td style="text-align:right;">' + formatCurrency.format(parseFloat(amt_total)) + '</td>' +
          '<td></td>' +
          '<td style="text-align:right;">' + (formatMetalWeight(wgt_total)) + '</td>' +
          '<td></td>' +
          '<td></td>' +
          '</tr>';
        $('#gen_adv_table_byid > tbody').html(trHTML);
        get_adv_benefitlist_byid();
      }
      else {
        $(".adv_table").css('display', 'block');
        $("#gen_adv_btn").css('display', 'none');
        $(".hide_table").css('display', 'block');
        var trHTML = '';
        trHTML += '<tr><td colspan="7" style="text-align:center;font-weight:bold;"><p>No Advance Data Available</p></td></tr>';
        $('#gen_adv_table_byid > tbody').html(trHTML);
      }
    }
  });
}
function get_adv_benefitlist_byid() {
  var id = ctrl_page[3];
  $.ajax({
    url:
      app_url +
      "index.php/mobile_api/getSchemeDetail?id_scheme_account=" +
      id +
      "&source_type=MOB&nocache=" +
      my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "GET",
    success: function (data) {
      console.log(data.chit);
      var ga_data = data.chit.gen_adv_bonus;
      var trHTML = "";
      $.each(ga_data, function (idx, item) {
        if (item.bonus > 0) {
          trHTML +=
            '<tr style="font-weight:bold;">' +
            "<td></td>" +
            "<td>Saved Benefits </td>" +
            "<td>" +
            item.range +
            "</td>" +
            '<td style="text-align:right;">' +
            item.bonus +
            "</td>" +
            '<td style="text-align:right;">' +
            item.bonus_wgt +
            "</td>" +
            "<td></td>" +
            "<td></td>" +
            "<td></td>" +
            "</tr>";
        }
      });
      trHTML +=
        '<tr style="font-weight:bold;color:blue">' +
        "<td></td>" +
        "<td></td>" +
        "<td>Total GA Benefits</td>" +
        '<td style="text-align:right;">' +
        data.chit.tot_gen_adv_bonus +
        "</td>" +
        '<td style="text-align:right;">' +
        (formatMetalWeight(data.chit.tot_gen_adv_bonus_wgt)) +
        "</td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>";
      var grand_total_ga_amt =
        parseInt(data.chit.tot_bonus_paid_amt) +
        parseInt(data.chit.tot_gen_adv_bonus);
      var grand_total_ga_wgt = formatMetalWeight(parseFloat(data.chit.tot_bonus_paid_wgt) + parseFloat(data.chit.tot_gen_adv_bonus_wgt));
      trHTML +=
        '<tr style="font-weight:bold;color:red;">' +
        "<td></td>" +
        "<td></td>" +
        "<td>Grand GA Total</td>" +
        '<td style="text-align:right;">' +
        grand_total_ga_amt +
        "</td>" +
        '<td style="text-align:right;">' +
        grand_total_ga_wgt +
        "</td>" +
        "<td></td>" +
        "<td></td>" +
        "<td></td>" +
        "</tr>";
      $("#gen_adv_table_byid > tbody").append(trHTML);
    },
  });
}
//Account ledger - show Advance payment list - ends
//monthly report starts
function get_months() {
  var currentDate = new Date();
  // Get the current month (0-based index, so you may want to add 1)
  var currentMonth = currentDate.getMonth() + 1;
  var currentYear = currentDate.getFullYear();
  console.log("currentYear: " + currentYear);
  var month_array = [
    { month: 'January', val: 1 },
    { month: 'February', val: 2 },
    { month: 'March', val: 3 },
    { month: 'April', val: 4 },
    { month: 'May', val: 5 },
    { month: 'June', val: 6 },
    { month: 'July', val: 7 },
    { month: 'August', val: 8 },
    { month: 'September', val: 9 },
    { month: 'October', val: 10 },
    { month: 'November', val: 11 },
    { month: 'December', val: 12 },
  ];
  $.each(month_array, function (i, item) {
    $('#month_select').append(
      $("<option></option>")
        .attr("value", item.val)
        .text(item.month)
    );
  });
  $("#month_select").select2("val", currentMonth);
  $("#id_year").val(currentYear);
}
$("#month_select").select2({
  placeholder: "Select Month",
  allowClear: true
});
function get_month_report_data() {
  $("div.overlay").css("display", "block");
  var branch_name = getBranchTitle();
  var selectedmonth = $("#month_select option:selected").text();
  var title = get_title(
    "",
    "",
    "Monthly Chit Report - " + selectedmonth + " - " + branch_name
  );
  var postdata = {
    month: $("#month_select").val(),
    year: $("#id_year").val(),
    id_scheme: $("#scheme_select").val(),
    id_branch: $("#branch_select").val(),
  };
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/monthly_report_data?nocache=" +
      my_Date.getUTCSeconds(),
    dataType: "JSON",
    type: "POST",
    data: postdata,
    success: function (data) {
      $("div.overlay").css("display", "none");
      //console.log(data.accounts);
      var accounts = data.accounts;
      var oTable = $("#monthly_chit_report_table").DataTable();
      oTable.clear().draw();
      //console.log(accounts.length);
      if (accounts != null && accounts.length > 0) {
        var sno = 1;
        oTable = $("#monthly_chit_report_table").DataTable({
          bDestroy: true,
          bInfo: true,
          bFilter: true,
          bSort: true,
          dom: "lBfrtip",
          pageLength: 25,
          lengthMenu: [
            [-1, 25, 50, 100, 250],
            ["All", 25, 50, 100, 250],
          ],
          aaData: accounts,
          buttons: [
            {
              extend: "print",
              footer: true,
              title: "",
              messageTop: title,
              orientation: "landscape",
              customize: function (win) {
                $(win.document.body).find("table").addClass("compact");
                $(win.document.body)
                  .find("table")
                  .addClass("compact")
                  .css("font-size", "10px")
                  .css("font-family", "sans-serif");
              },
              exportOptions: {
                columns: ":visible",
              },
            },
            {
              extend: "excel",
              footer: true,
              title:
                "Monthly Chit Report -" + selectedmonth + " " + branch_name,
            },
            {
              extend: "colvis",
              collectionLayout: "fixed columns",
              collectionTitle: "Column visibility control",
            },
          ],
          aoColumns: [
            {
              mDataProp: function (row, type, val, meta) {
                return sno++;
              },
            },
            { mDataProp: "date_payment" },
            {
              mDataProp: function (row, type, val, meta) {
                return row.cash != 0 ? formatCurrency.format(row.cash) : "";
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.card != 0 ? formatCurrency.format(row.card) : "";
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.cheque != 0 ? formatCurrency.format(row.cheque) : "";
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.nb != 0 ? formatCurrency.format(row.nb) : "";
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.upi != 0 ? formatCurrency.format(row.upi) : "";
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.wallet != 0 ? formatCurrency.format(row.wallet) : "";
              },
            },
            {
              mDataProp: function (row, type, val, meta) {
                return row.payment_amounts != 0
                  ? formatCurrency.format(row.payment_amounts)
                  : "";
              },
            },
          ],
          footerCallback: function (row, data, start, end, display) {
            if (data.length > 0) {
              var api = this.api(),
                data;
              var intVal = function (i) {
                return typeof i === "string"
                  ? i.replace(/[\$,]/g, "") * 1
                  : typeof i === "number"
                    ? i
                    : 0;
              };
              // Amount Total over this page
              amttotal = api
                .column(8, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(8).footer()).html(formatCurrency.format(amttotal));
              cashtotal = api
                .column(2, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(2).footer()).html(formatCurrency.format(cashtotal));
              cashtotal = api
                .column(2, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(2).footer()).html(formatCurrency.format(cashtotal));
              cardtotal = api
                .column(3, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(3).footer()).html(formatCurrency.format(cardtotal));
              chequetotal = api
                .column(4, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(4).footer()).html(
                formatCurrency.format(chequetotal)
              );
              nbtotal = api
                .column(5, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(5).footer()).html(formatCurrency.format(nbtotal));
              upitotal = api
                .column(6, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(6).footer()).html(formatCurrency.format(upitotal));
              wallettotal = api
                .column(7, { page: "current" })
                .data()
                .reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0);
              $(api.column(7).footer()).html(
                formatCurrency.format(wallettotal)
              );
              $(api.column(1).footer()).html("Total ");
            }
          },
          columnDefs: [
            {
              targets: [0, 1],
              className: "dt-left",
            },
            {
              targets: [2, 3, 4, 5, 6, 7, 8],
              className: "dt-right",
            },
          ],
          order: [[1, "asc"]],
        });
      } else {
        $("#monthly_chit_report_table tfoot td").html(""); // Clear the footer row
      }
    },
  });
}
$('#search_monthly_list').on('click', function () {
  get_month_report_data();
});
//monthly report ends
//out standing report excel export ----start
$("#excel_export").on("click", function () {
  const excelData = [];
  selectedValue = $('input[name="tableview"]:checked').val();
  var offHTML = "";
  var onHTML = "";
  var scheme_count = 0;
  var scheme_ins = 0;
  var scheme_amt = 0;
  var closed_amt = 0;
  var collection_amt = 0;
  var balace_amt = 0;
  var sub_scheme_amt = 0;
  var scheme_metal_wgt = 0;
  var sub_gold_metal_wgt = 0;
  var grand_gold_metal_wgt = 0;
  var sub_silver_metal_wgt = 0;
  var grand_silver_metal_wgt = 0;
  var grp = 0;
  var grp_ins = 0;
  var grp_paid = 0;
  var grp_metal_wgt = 0;
  var grp_gold_wgt = 0;
  var grp_silver_wgt = 0;
  var scheme_wgt = 0;
  var balance_wgt = 0;
  //scheme wise summary starts here
  var title = "";
  var branch_name = getBranchTitle();
  title = get_title(
    "",
    "",
    "Customer Outstanding Payment Report - Summary - " + branch_name
  );
  my_Date = new Date();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/scheme_summary?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      id_scheme: $("#scheme_select").val(),
      id_group: $("#id_group").val(),
      id_branch:
        $("#branch_filter").val() != "" &&
          $("#branch_filter").val() != undefined
          ? $("#branch_filter").val()
          : $("#id_branch").val(),
      from_date:
        selectedValue != 0
          ? $("#rpt_payments1").html() != undefined
            ? $("#rpt_payments1").html()
            : ""
          : "",
      to_date:
        selectedValue != 0
          ? $("#rpt_payments2").html() != undefined
            ? $("#rpt_payments2").html()
            : ""
          : "",
      singlefilter:
        selectedValue != 0
          ? $("#datesingle_search").val() != undefined
            ? $("#datesingle_search").val()
            : ""
          : "",
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      // console.log(data);
      if (data.scheme_summary != null) {
        var company_name = $("#company_name").val();
        excelData.push({
          "Main Heading": "Customer Summary Report",
        });
        excelData.push({
          "Main Heading": company_name,
        });
        //  excelData.push({});
        excelData.push({
          "Scheme Name": "Scheme Name ",
          "Acc count": "No Of Account ",
          "O.P.Amt": "Opening Amount",
          "O.p.Wgt": "Opening Weight",
          "coll.Amt": "Collection Amount",
          "clos.Amt": " Closing Amount",
          "B.Amt": "Balance Amount ",
          "B.Wgt": "Balance Weight",
          "Gold.Wgt": "Gold Weight",
          "Silver.Wgt": "Silver WEight ",
        });
        // Loop through scheme summary data
        $.each(data.scheme_summary, function (key, classification) {
          sub_gold_metal_wgt = 0;
          sub_silver_metal_wgt = 0;
          sub_closing_wt = 0;
          sub_bal_amt = 0;
          sub_bal_wgt = 0;
          sub_collection_amt = 0;
          sub_scheme_count = 0;
          sub_scheme_amt = 0;
          sub_scheme_wgt = 0;
          let section_scheme_count = 0;
          $.each(classification, function (scheme, val) {
            // console.log(val);
            sub_gold_metal_wgt +=
              val.id_metal == 1 && isValid(val.metal_weight)
                ? formatMetalWeight(val.metal_weight)
                : 0;
            grand_gold_metal_wgt +=
              val.id_metal == 1 && isValid(val.metal_weight)
                ? formatMetalWeight(val.metal_weight)
                : 0;
            sub_silver_metal_wgt +=
              val.id_metal == 2 && isValid(val.metal_weight)
                ? formatMetalWeight(val.metal_weight)
                : 0;
            grand_silver_metal_wgt +=
              val.id_metal == 2 && isValid(val.metal_weight)
                ? formatMetalWeight(val.metal_weight)
                : 0;
            // ... Existing code ...
            excelData.push({
              "Scheme Name": val.code.toUpperCase(),
              "Acc count": val.scheme_count,
              "O.P.Amt":
                val.opening_amount != null
                  ? parseFloat(val.opening_amount).toFixed(2)
                  : 0,
              "O.P.Wgt":
                val.opening_wgt != null
                  // ? parseFloat(val.opening_wgt).toFixed(3)
                  ? (formatMetalWeight((val.opening_wgt)))
                  : 0,
              "coll.Amt":
                val.current_collection_amt != null
                  ? parseFloat(val.current_collection_amt).toFixed(2)
                  : 0,
              "clos.Amt":
                val.current_closed_amt != null
                  ? parseFloat(val.current_closed_amt).toFixed(2)
                  : 0,
              "B.Amt":
                val.balance_amount != null
                  ? parseFloat(val.balance_amount).toFixed(2)
                  : 0,
              "B.Wgt":
                val.balance_weight != null
                  // ? parseFloat(val.balance_weight).toFixed(3)
                  ? (formatMetalWeight((val.balance_weight)))
                  : 0,
              "Gold.Wgt":
                val.id_metal == 1 && isValid(val.metal_weight)
                  // ? parseFloat(val.metal_weight).toFixed(3)
                  ? (formatMetalWeight((val.metal_weight)))
                  : "",
              "Silver.Wgt":
                val.id_metal == 2 && isValid(val.metal_weight)
                  // ? parseFloat(val.metal_weight).toFixed(3)
                  ? (formatMetalWeight((val.metal_weight)))
                  : "",
            });
            // const columnWidths = {
            // 	'Scheme Name': 150,
            // 	'Acc count': 120,
            // 	'O.Amt': 120,
            // 	'coll.Amt': 150,
            // 	'clos.Amt': 150,
            // 	'B.Amt': 150,
            // 	'Gold.Wgt': 120,
            // 	'Silver.Wgt': 120
            // };
            // excelData.push({ _columnWidths: columnWidths });
            // Calculate subtotals
            scheme_count += parseInt(val.scheme_count);
            sub_scheme_count += parseInt(val.scheme_count);
            scheme_ins += parseInt(
              val.paid_installments != null ? val.paid_installments : 0
            );
            scheme_amt += parseInt(
              val.opening_amount != null ? val.opening_amount : 0
            );
            scheme_wgt += parseFloat(
              val.opening_wgt != null ? formatMetalWeight(val.opening_wgt) : 0
              // ).toFixed(3);
            );
            collection_amt += parseInt(
              val.current_collection_amt != null
                ? val.current_collection_amt
                : 0
            );
            closed_amt += parseInt(
              val.current_closed_amt != null ? val.current_closed_amt : 0
            );
            balace_amt += parseInt(
              val.balance_amount != null ? val.balance_amount : 0
            );
            balance_wgt += parseFloat(
              val.balance_weight != null ? formatMetalWeight(val.balance_weight) : 0
              // ).toFixed(3);
            );
            sub_closing_wt += parseInt(
              val.current_closed_amt != null ? val.current_closed_amt : 0
            );
            sub_bal_amt += parseInt(
              val.balance_amount != null ? val.balance_amount : 0
            );
            sub_bal_wgt += parseFloat(
              val.balance_weight != null ? formatMetalWeight(val.balance_weight) : 0
              // ).toFixed(3);
            );
            scheme_metal_wgt += parseFloat(
              val.metal_weight != null ? formatMetalWeight(val.metal_weight) : 0
            );
            sub_scheme_amt += parseInt(
              val.opening_amount != null ? val.opening_amount : 0
            );
            sub_scheme_wgt += parseFloat(
              val.opening_wgt != null ? formatMetalWeight(val.opening_wgt) : 0
              // ).toFixed(3);
            );
            sub_collection_amt += parseInt(
              val.current_collection_amt != null
                ? val.current_collection_amt
                : 0
            );
          });
          // ... Existing code ...
          excelData.push({
            "Scheme Name": "SUB TOTAL",
            "Acc count": sub_scheme_count,
            "O.P.Amt": parseFloat(sub_scheme_amt).toFixed(2),
            "O.P.Wgt": (formatMetalWeight((sub_scheme_wgt))),
            "coll.Amt": parseFloat(sub_collection_amt).toFixed(2),
            "clos.Amt": parseFloat(sub_closing_wt).toFixed(2),
            "B.Amt": parseFloat(sub_bal_amt).toFixed(2),
            "B.Wgt": (formatMetalWeight((sub_bal_wgt))),
            "Gold.Wgt":
              sub_gold_metal_wgt !== 0
                ? (formatMetalWeight((sub_gold_metal_wgt)))
                : "",
            "Silver.Wgt":
              sub_silver_metal_wgt !== 0
                ? (formatMetalWeight((sub_silver_metal_wgt)))
                : "",
          });
          // Reset subtotals
          // sub_scheme_count = 0;
          // paid_ins_count = 0;
          // amt_total = 0;
          // collectio_amt = 0;
          // closing_amt = 0;
          // bal_amt = 0;
          // sub_amt_total = 0;
          // wgt_total = 0;
          // sub_gold_metal_wgt = 0;
          // sub_silver_metal_wgt = 0;
        });
        // ... Existing code ...
        excelData.push({
          "Scheme Name": "GRAND TOTAL",
          "Acc count": scheme_count,
          "O.P.Amt": parseFloat(scheme_amt).toFixed(2),
          "O.P.Wgt": (session_data.metal_wgt_roundoff == 0 ? trimDecimal((scheme_wgt), session_data.metal_wgt_decimal) : scheme_wgt.toFixed(session_data.metal_wgt_decimal)),
          "coll.Amt": parseFloat(collection_amt).toFixed(2),
          "clos.Amt": parseFloat(closed_amt).toFixed(2),
          "B.Amt": parseFloat(balace_amt).toFixed(2),
          "B.Wgt": (session_data.metal_wgt_roundoff == 0 ? trimDecimal((balance_wgt),session_data.metal_wgt_decimal): balance_wgt.toFixed(session_data.metal_wgt_decimal) ),
          "Gold.Wgt":
            grand_gold_metal_wgt !== 0
              ? (session_data.metal_wgt_roundoff == 0 ? trimDecimal((grand_gold_metal_wgt),session_data.metal_wgt_decimal): grand_gold_metal_wgt.toFixed(session_data.metal_wgt_decimal) )
              : "",
          "Silver.Wgt":
            grand_silver_metal_wgt !== 0
              ? (session_data.metal_wgt_roundoff == 0 ? trimDecimal((grand_silver_metal_wgt),session_data.metal_wgt_decimal): grand_silver_metal_wgt.toFixed(session_data.metal_wgt_decimal) )
              : "",
        });
        // Generate Excel file
        exportToExcel(excelData, "Customer_Outstanding_Report");
      }
    },
  });
});
function exportToExcel(data, filename) {
  const csvContent = 'data:text/csv;charset=utf-8,' + data.map(row => Object.values(row).join(',')).join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', filename + '.csv');
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
function isValid(value) {
  return value !== null && value !== undefined && value !== '' && !(typeof value === 'number' && isNaN(value));
}
// Out stading report excel export --->end 
//maturity report starts here
$('#search_maturity_list').on('click', function () {
  get_maturity_report();
});
function get_maturity_report() {
  $("div.overlay").css("display", "block");
  var from_date = $("#rpts_payments1").text();
  var to_date = $("#rpts_payments2").text();
  $("#maturity_date_range").text(from_date + " To " + to_date);
  var post_data = {
    from_date: from_date,
    to_date: to_date,
    id_scheme: $("#scheme_select").val(),
    id_branch: $("#branch_select").val(),
    emp_code: $("#employee_select").val(),
  };
  $.ajax({
    type: 'post',
    url: base_url + 'index.php/admin_reports/maturity_report_data',
    dataType: 'json',
    data: post_data,
    success: function (data) {
      $("div.overlay").css("display", "none");
      set_maturity_report(data);
    }
  });
}
function set_maturity_report(data) {
  var from_date = $("#rpts_payments1").text();
  var to_date = $("#rpts_payments2").text();
  var title;
  var amount_subtotal;
  var amount_grandtotal = 0;
  var branch_name = getBranchTitle();
  title = get_title(from_date, to_date, "Maturity Report - " + branch_name);
  var data = [].concat.apply([], data.accounts);
  var oTable = $("#maturity_report_table").DataTable();
  oTable.clear().draw();
  if (data != null && data.length > 0) {
    oTable = $("#maturity_report_table").dataTable({
      bDestroy: true,
      bInfo: true,
      scrollX: "100%",
      bSort: true,
      dom: "lBfrtip",
      pageLength: 25,
      lengthMenu: [
        [-1, 25, 50, 100, 250],
        ["All", 25, 50, 100, 250],
      ],
      order: [[0, "desc"]],
      buttons: [
        {
          extend: "print",
          footer: true,
          title: "",
          messageTop: title,
          customize: function (win) {
            // $(win.document.body)
            //   .find("table")
            //   .addClass("compact")
            //   .css("font-size", "inherit");
            $(win.document.body).find('table')
              .addClass('compact');
            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', '10px')
              .css('font-family', 'sans-serif');
              
              // Inject refined CSS for borders and page layout
              var css = 'table.dataTable { ' +
                    'border-collapse: collapse !important; ' +
                    'width: 99% !important; ' +
                    'margin: 0 auto !important; ' +
                    'border: 1px solid black !important; ' +
                    'box-sizing: border-box !important; } ' +
                    'table.dataTable th, table.dataTable td { ' +
                    'border: 1px solid black !important; } ' +
                    '@page { size: landscape; margin: 10mm; }';
              var head = win.document.head || win.document.getElementsByTagName('head')[0];
              var style = win.document.createElement('style');
              style.type = 'text/css';
              style.media = 'print';
              if (style.styleSheet) {
                style.styleSheet.cssText = css;
              } else {
                style.appendChild(win.document.createTextNode(css));
              }
              head.appendChild(style);
          },
        },
        {
          extend: "excel",
          footer: true,
          title:
            "Maturity Report -" +
            branch_name +
            " " +
            from_date +
            " - " +
            to_date,
        },
        {
          extend: "colvis",
          collectionLayout: "fixed columns",
          collectionTitle: "Column visibility control",
        },
      ],
      aaData: data,
      aoColumns: [
        { mDataProp: "s_no" },
        {
          mDataProp: function (row, type, val, meta) {
            return row.firstname + " " + row.lastname;
          },
        },
        { mDataProp: "mobile" },
        { mDataProp: "id_scheme_account" },
        { mDataProp: "scheme_acc_number" },
        { mDataProp: "account_name" },
        { mDataProp: "scheme_code" },
        { mDataProp: "start_date" },
        { mDataProp: "maturity_date" },
        {
          mDataProp: function (row, type, val, meta) {
            return formatCurrency.format(row.payment_amount);
          },
        },
        {
          mDataProp: function (row, type, val, meta) {
            return formatMetalWeight(row.payment_weight);
          }
        },
        { mDataProp: "paid_installments" },
        { mDataProp: "branch" },
        {
          mDataProp: function (row, type, val, meta) {
            return get_added_by(row.added_by);
          },
        },
        // 			{ "mDataProp": "employee" },
      ],
      columnDefs: [
        {
          targets: [1, 4, 5, 6, 7, 8, 12, 13],
          className: "dt-left",
        },
        {
          targets: [0, 2, 3, 9, 10, 11],
          className: "dt-right",
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        if (data.length > 0) {
          var api = this.api(),
            data;
          for (var i = 0; i <= data.length - 1; i++) {
            var intVal = function (i) {
              return typeof i === "string"
                ? i.replace(/[\$,]/g, "") * 1
                : typeof i === "number"
                  ? i
                  : 0;
            };
            // paid Total
            paid = api
              .column(9, { page: "current" })
              .data()
              .reduce(function (a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            var paid_frm = formatCurrency.format(paid);
            paid_frm = paid_frm.replace("", " ");
            $(api.column(9).footer())
              .html(paid_frm)
              .css("text-align", "right")
              .addClass("report-grand-total");
            // paid weight
            paid_weight = api
              .column(10, { page: "current" })
              .data()
              .reduce(function (a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            $(api.column(10).footer())
              .html(formatMetalWeight(paid_weight))
              .css("text-align", "right")
              .addClass("report-grand-total");
            $(api.column(8).footer())
              .html("Total")
              .addClass("report-grand-total");
          }
        } else {
          var data = 0;
          var api = this.api(),
            data;
          $(api.column(8).footer()).html("");
          $(api.column(9).footer()).html("");
          $(api.column(10).footer()).html("");
        }
      },
    });
  } else {
    var brHTML = "";
    brHTML +=
      '<tr><td colspan=14 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
    $("#maturity_report_table > tbody").html(brHTML);
  }
}
function get_added_by(data) {
  var added_by;
  switch (data) {
    case "0":
      added_by = "WebApp";
      break;
    case "1":
      added_by = "Admin";
      break;
    case "2":
      added_by = "MobileApp";
      break;
    case "3":
      added_by = "Collection App";
      break;
    case "4":
      added_by = "Retail App";
      break;
    case "5":
      added_by = "Sync";
      break;
    case "6":
      added_by = "Import";
      break;
    /*	case '4':
        added_by='Admin';
        break;
      case '5':
        added_by='Admin';
        break;
      case '6':
        added_by='Admin';
        break;*/
  }
  return added_by;
}
//maturity report ends here
if (ctrl_page[1] == 'get_yet_to_issue') {
  get_gift_name();
  get_schemename();
  get_gift_report();
}
function get_gift_report() {
  my_Date = new Date();
  $("div.overlay").css("display", "block");
  $.ajax({
    data: ({ 'from_date': $('#gift_from_date').html(), 'to_date': $('#gift_to_date').html(), 'scheme': $('#scheme_select').val(), 'id_branch': $('#branch_select').val(), 'gift': $('#gift_select').val(), 'report_type': $('#report_type').val() }),
    url: base_url + "index.php/admin_reports/get_online_gift_report?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      console.log(data);
      gift_summary(data);
      get_gift_report_list(data);
      $("div.overlay").css("display", "none");
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    }
  });
}
function gift_summary(data) {
  let tableData = '<table  style="width:27%" class="table table-bordered">';
  tableData += `
		<thead>
			<tr>
				<th>Schemewise/Giftwise</th>
				<th>Count</th>
			</tr>
		</thead>
		<tbody>
	`;
  let totalCount = 0;
  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      const rowData = data[key];
      let count = 0;
      for (const item of rowData) {
        count += parseInt(item.yet_to_issue_gift_qty);
      }
      tableData += `
				<tr>
					<td>${key}</td>
					<td>${count}</td>
				</tr>
			`;
      totalCount += count;
    }
  }
  tableData += `
		</tbody>
		<tfoot>
		   <tr>
			</tr>
			<tr>
				<td style="font-weight:bold">Total</td>
				<td style="font-weight:bold">${totalCount}</td>
			</tr>
		</tfoot>
	</table>
	`;
  // Append the table to the container
  const tableContainer = document.getElementById('table-container');
  tableContainer.innerHTML = tableData;
  // return tableData;
}
function gift_summary_print(data) {
  console.log(data)
  let tableData = '<div style="text-align: center; font-size: 16px;margin:auto;"> <div style="display: flex; font-weight: bold;margin-right: 148px;">';
  tableData += `
			<span style="flex-basis: 50%;border-top:1px solid #CBBBB7;border-left:1px solid #CBBBB7;border-right:1px solid #CBBBB7;margin-left: 199px;">Schemewise/Giftwise</span>
			<span style="flex-basis: 50%;border-top:1px solid #CBBBB7;border-left:1px solid #CBBBB7;border-right:1px solid #CBBBB7;">Count</span>
		</div>
	`;
  let totalCount = 0;
  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      const rowData = data[key];
      let count = 0;
      for (const item of rowData) {
        count += parseInt(item.yet_to_issue_gift_qty);
      }
      tableData += `
				<div style="display: flex;margin-right: 148px; ">
					<span style="flex-basis: 50%;border-top:1px solid #CBBBB7 ;margin-left:200px;border-left:1px solid #CBBBB7;border-right:1px solid #CBBBB7;">${key}</span>
					<span style="flex-basis: 50%;border-top:1px solid  #CBBBB7;border-left:1px solid #CBBBB7;border-right:1px solid #CBBBB7;">${count}</span>
				</div>
			`;
      totalCount += count;
    }
  }
  tableData += `
		<div style="display: flex;  font-weight: bold;margin-right: 148px;">
			<span style="flex-basis: 50%;border-bottom:1px solid #CBBBB7;border-left:1px solid #CBBBB7;border-top:1px solid #CBBBB7;border-right:1px solid #CBBBB7;margin-left:200px;">Total</span>
			<span style="flex-basis: 50%;border-bottom:1px solid #CBBBB7;border-left:1px solid #CBBBB7;border-top:1px solid #CBBBB7;border-right:1px solid #CBBBB7;">${totalCount}</span>
		</div>
	</div>
	`;
  return tableData;
}
function get_gift_report_list(data) {
  var title = '';
  $("div.overlay").css("display", "block");
  $("#gift_issue_report > tbody > tr").remove();
  $('#gift_issue_report').dataTable().fnClearTable();
  $('#gift_issue_report').dataTable().fnDestroy();
  trHtml = '';
  gft_qty = 0;
  current_stock = 0;
  $.each(data, function (sche_gft, payment) {
    console.log(sche_gft);
    console.log(payment);
    var sub_yetToIssue = 0;
    var sub_avail = 0
    trHtml += '<tr style="font-weight:bold;">'
      + '<td  colspan="2">' + sche_gft + '</td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '</tr>';
    $.each(payment, function (key, items) {
      key = Number(key + 1);
      trHtml += '<tr>'
        + '<td>' + key + '</td>'
        + '<td><a  target ="_blank"  href="' + base_url + 'index.php/reports/payment/account/' + items.id_scheme_account + '">' + items.id_scheme_account + '</a></td>'
        + '<td>' + items.mobile + '</td>'
        + '<td>' + items.cus_name + '</td>'
        + '<td>' + items.start_date + '</td>'
        + '<td>' + items.joined_branch_name + '</td>'
        + '<td>' + items.scheme_acc_number + '</td>'
        + '<td>' + items.account_name + '</td>'
        + '<td>' + items.code + '</td>'
        + '<td>' + items.paid_installments + '/' + items.total_installments + '</td>'
        + '<td>' + items.yet_to_issue_gift_name + '</td>'
        + '<td>' + items.yet_to_issue_gift_qty + '</td>'
        + '<td>' + items.available_qty_from_stock + '</td>'
        + '<td>' + items.total_issued_gift_qty + '/' + items.total_assigned_qty + '</td>'
        + '<td>' + items.referred_by + '</td>'
        + '</tr>';
      sub_yetToIssue += Number(items.yet_to_issue_gift_qty);
      sub_avail += Number(items.available_qty_from_stock);
      gft_quantity = Number(items.yet_to_issue_gift_qty);
      curr_stock = Number(items.available_qty_from_stock);
    });
    trHtml += '<tr style="font-weight:bold;">'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '<td>Sub Total</td>'
      + '<td style="text-align:right; font-weight: bold;">' + sub_yetToIssue + '</td>'
      // +'<td>'+sub_avail+'</td>'
      + '<td></td>'
      + '<td></td>'
      + '<td></td>'
      + '</tr>';
    current_stock += Number(sub_avail);
    gft_qty += Number(sub_yetToIssue);
  });
  trHtml += '<tr style="font-weight:bold;">'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '<td> </td>'
    + '<td></td>'
    + '<td></td>'
    + '<td>Grand Total</td>'
    + '<td style="text-align:right; font-weight: bold;">' + gft_qty + '</td>'
    //  +'<td>'+current_stock+'</td>'
    + '<td></td>'
    + '<td></td>'
    + '<td></td>'
    + '</tr>';
  $('#gift_issue_report > tbody').html(trHtml);
  if (!$.fn.DataTable.isDataTable('#gift_issue_report')) {
    var branch_name = getBranchTitle();
    // const tableHtmls = document.getElementById('gift_summary_table')
    // tableHtmls.style.fontSize = '23px';
    //  const tableHtml = document.getElementById('gift_summary_table').outerHTML;
    tableHtml = gift_summary_print(data)
    console.log(tableHtml)
    title += get_title('', '', 'Gift - Yet To Issue');
    title += '<br>' + tableHtml + '<br><br>';
    var excel_title = '';
    excel_title = get_excel_title('', '', 'Gift - Yet To Issue');
    oTable = $('#gift_issue_report').dataTable({
      "bSort": false,
      "footer": true, // Enable footer
      "bInfo": false,
      "scrollX": '100%',
      "dom": 'lBfrtip',
      "lengthMenu": [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
      "width": "120px", "targets": 1,
      "buttons": [
        {
          extend: 'print',
          footer: true,
          messageTop: title,
          orientation: 'landscape',
          customize: function (win) {
            $(win.document.body).find('table')
              .addClass('compact');
            $(win.document.body).find('table')
              .addClass('compact')
              .css('font-size', '10px')
              .css('font-family', 'sans-serif');
          },
          exportOptions: { columns: ':visible' },
        },
        {
          extend: 'excel',
          footer: true,
          title: 'Gift Yet to issue Report',
          messageTop: excel_title,
        },
        { extend: 'colvis', collectionLayout: 'fixed columns', collectionTitle: 'Column visibility control' },
      ],
      "columnDefs":
        [{
          targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 14],
          className: 'dt-left'
        },
        {
          targets: [9, 11, 12, 13],
          className: 'dt-right'
        },
        { "width": "120px", "targets": 1 },
        ],
    });
  }
  $("div.overlay").css("display", "none");
}
function get_excel_title(from_date, to_date, title) {
  var company_name = $("#company_name").val();
  var company_code = $("#company_code").val();
  var company_address1 = $("#company_address1").val();
  var company_address2 = $("#company_address2").val();
  var company_city = $("#company_city").val();
  var pincode = $("#pincode").val();
  var company_email = $("#company_email").val();
  var company_gst_number = $("#company_gst_number").val();
  var phone = $("#phone").val();
  var select_date = "";
  var string = "";
  select_date += company_code;
  select_date += company_address1;
  select_date += company_address2 + company_city + "-" + pincode;
  select_date += "GSTIN:" + company_gst_number + ", EMAIL:" + company_email;
  select_date += "Contact :" + phone;
  select_date += title;
  select_date +=
    from_date !== "" && to_date != ""
      ? "Details From Date " + from_date + " To Date " + to_date
      : "";
  select_date +=
    "Print Taken On : " + moment().format("dddd, MMMM Do YYYY, h:mm:ss a");
  select_date +=
    "Print Taken By :" +
    $(".hidden-xs").html() +
    " Login Branch : " +
    $("#branch_name").html();
  return select_date;
}
$("#print_summary").on("click", function () {
  selectedValue = $('input[name="datepick"]:checked').val();
  const printWindow = window.open("", "_blank");
  //for printing outstanding summary(data that are printed when print clicked) starts here
  var scheme_count = 0;
  var scheme_ins = 0;
  var scheme_amt = 0;
  var closed_amt = 0;
  var collection_amt = 0;
  var balace_amt = 0;
  var sub_scheme_amt = 0;
  var scheme_metal_wgt = 0;
  var sub_gold_metal_wgt = 0;
  var grand_gold_metal_wgt = 0;
  var sub_silver_metal_wgt = 0;
  var grand_silver_metal_wgt = 0;
  var grp = 0;
  var grp_ins = 0;
  var grp_paid = 0;
  var grp_metal_wgt = 0;
  var grp_gold_wgt = 0;
  var grp_silver_wgt = 0;
  //scheme wise summary starts here
  var title = "";
  var branch_name = getBranchTitle();
  title = get_title(
    "",
    "",
    "Customer Outstanding Payment Report - Summary - " + branch_name
  );
  my_Date = new Date();
  selectedValue = $('input[name="datepick"]:checked').val();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/scheme_summary?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      id_scheme: $("#scheme_select").val(),
      id_group: $("#id_group").val(),
      id_branch:
        $("#branch_filter").val() != "" &&
          $("#branch_filter").val() != undefined
          ? $("#branch_filter").val()
          : $("#id_branch").val(),
      ...(selectedValue != 0
        ? {
          singlefilter:
            $("#datesingle_search").val() != undefined
              ? $("#datesingle_search").val()
              : "",
          from_date:
            $("#rpt_payments1").html() != undefined &&
              $("#rpt_payments1").html() != ""
              ? $("#rpt_payments1").html()
              : "",
          to_date:
            $("#rpt_payments2").html() != undefined &&
              $("#rpt_payments2").html() != ""
              ? $("#rpt_payments2").html()
              : "",
        }
        : {}),
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      console.log(data, "print");
      if (data.scheme_summary != null) {
        //	title+='<div class="row" style="text-align:center;">OUTSTANDING SCHEME SUMMARY</div>'
        //	title+='<div class="row" style="text-align:center;"><div class="col-md-12"><strong><p style="margin:0;">Schemewise Accounts</p></strong><table class="table table-bordered table-striped" style="width:80%;margin-left:30px;"><thead ><tr><th>Scheme Name</th><th>Acc count</th>&nbsp;&nbsp;<th>Amount</th><th>G.Weight</th><th>S.Weight</th></tr></thead><tbody><tr>';
        // 		title+='<div class="row" style="text-align:center;"><div class="col-md-12"><strong><p style="margin:0;">Schemewise Accounts</p></strong>
        // 		<table class="table table-bordered table-striped" style="width:80%;margin-left:30px;"><thead ><tr><th>Scheme Name</th>
        // 		<th style="text-align:right;">Acc count</th>&nbsp;&nbsp;<th style="text-align:right;">O.P.Amt</th><th style="text-align:right;">coll.Amt</th>
        // 		<th style="text-align:right;">clos.Amt</th><th style="text-align:right;">B.Amt</th><th style="text-align:right;">Gold.Wgt</th>
        // 		<th style="text-align:right;">Silver.Wgt</th></tr></thead><tbody><tr>';
        title +=
          '<div class="row" style="text-align:center;">' +
          '<div class="col-md-12">' +
          '<strong><p style="margin:0;">Schemewise Accounts</p></strong>' +
          '<table class="table table-bordered table-striped" style="width:80%;margin-left:30px;">' +
          "<thead>" +
          "<tr>" +
          '<th style="text-align:center;">Scheme Name</th>' +
          '<th style="text-align:center;">Account Count</th>' +
          '<th style="text-align:right;">Opening Amt</th>';
        if (selectedValue != 0) {
          title +=
            '<th style="text-align:right;">Collection Amt</th>' +
            '<th style="text-align:right;">Closed Amt</th>' +
            '<th style="text-align:right;">Balance Amt</th>';
        }
        title += "</tr></thead><tbody><tr>";
        $.each(data.scheme_summary, function (key, classification) {
          // title+='<td><strong>'+key.toUpperCase()+'</td><td></td><td></td><td></td></tr>';
          sub_gold_metal_wgt = 0;
          sub_silver_metal_wgt = 0;
          sub_closing_wt = 0;
          sub_bal_amt = 0;
          sub_collection_amt = 0;
          sub_scheme_count = 0;
          sub_scheme_amt = 0;
          $.each(classification, function (scheme, val) {
            sub_gold_metal_wgt +=
              val.id_metal == 1 && isValid(val.metal_weight)
                ? parseFloat(val.metal_weight)
                : 0;
            grand_gold_metal_wgt +=
              val.id_metal == 1 && isValid(val.metal_weight)
                ? parseFloat(val.metal_weight)
                : 0;
            sub_silver_metal_wgt +=
              val.id_metal == 2 && isValid(val.metal_weight)
                ? parseFloat(val.metal_weight)
                : 0;
            grand_silver_metal_wgt +=
              val.id_metal == 2 && isValid(val.metal_weight)
                ? parseFloat(val.metal_weight)
                : 0;
            // 	title+='<tr>'+
            // 	'<td>'+val.code.toUpperCase()+'</td>'+
            // 	'<td style="text-align: right;">'+val.scheme_count+'</td>&nbsp;&nbsp;'+
            // // 	'<td style="text-align: right;">'+(val.paid_installments!=null?val.paid_installments:0)+'</td>'+
            // 	'<td style="text-align: right;">'+(val.paid_amount!=null?formatCurrency.format(parseFloat(val.paid_amount).toFixed(2)):0)+'</td>'+
            // 	'<td style="text-align: right;">'+(val.collection_amount!=null?formatCurrency.format(parseFloat(val.collection_amount).toFixed(2)):0)+'</td>'+
            // 	'<td style="text-align: right;">'+(val.closed_amount!=null?formatCurrency.format(parseFloat(val.closed_amount).toFixed(2)):0)+'</td>'+
            // 	'<td style="text-align: right;">'+(val.balance_amount!=null?formatCurrency.format(parseFloat(val.balance_amount).toFixed(2)):0)+'</td>'+
            // 	'<td style="text-align: right;">'+(val.id_metal==1 && isValid(val.metal_weight) && val.metal_weight!=0 ?parseFloat(val.metal_weight).toFixed(3):"")+'</td>'+
            // 	'<td style="text-align: right;">'+(val.id_metal==2 && isValid(val.metal_weight) && val.metal_weight!=0 ?parseFloat(val.metal_weight).toFixed(3):"")+'</td>'+
            // 	'</tr>';
            title +=
              "<tr>" +
              "<td>" +
              val.code.toUpperCase() +
              "</td>" +
              '<td style="text-align: right;">' +
              val.scheme_count +
              "</td>&nbsp;&nbsp;" +
              '<td style="text-align: right;">' +
              (val.opening_amount != null
                ? formatCurrency.format(
                  parseFloat(val.opening_amount).toFixed(2)
                )
                : "0") +
              "</td>";
            if (selectedValue != 0) {
              title +=
                '<td style="text-align: right;">' +
                (val.current_collection_amt != null
                  ? formatCurrency.format(
                    parseFloat(val.current_collection_amt).toFixed(2)
                  )
                  : "0") +
                "</td>" +
                '<td style="text-align: right;">' +
                (val.current_closed_amt != null
                  ? formatCurrency.format(
                    parseFloat(val.current_closed_amt).toFixed(2)
                  )
                  : "0") +
                "</td>" +
                '<td style="text-align: right;">' +
                (val.balance_amount != null
                  ? formatCurrency.format(
                    parseFloat(val.balance_amount).toFixed(2)
                  )
                  : "0") +
                "</td>";
            }
            title +=
              '<td style="text-align: right;">' +
              (val.id_metal == 1 &&
                isValid(val.metal_weight) &&
                val.metal_weight != 0
                ? parseFloat(val.metal_weight).toFixed(3)
                : "") +
              "</td>" +
              '<td style="text-align: right;">' +
              (val.id_metal == 2 &&
                isValid(val.metal_weight) &&
                val.metal_weight != 0
                ? parseFloat(val.metal_weight).toFixed(3)
                : "") +
              "</td>" +
              "</tr>";
            scheme_count += parseInt(val.scheme_count);
            sub_scheme_count += parseInt(val.scheme_count);
            scheme_ins += parseInt(
              val.paid_installments != null ? val.paid_installments : 0
            );
            scheme_amt += parseInt(
              val.opening_amount != null ? val.opening_amount : 0
            );
            collection_amt += parseInt(
              val.current_collection_amt != null
                ? val.current_collection_amt
                : 0
            );
            closed_amt += parseInt(
              val.current_closed_amt != null ? val.current_closed_amt : 0
            );
            balace_amt += parseInt(
              val.balance_amount != null ? val.balance_amount : 0
            );
            sub_closing_wt += parseInt(
              val.closed_amount != null ? val.closed_amount : 0
            );
            sub_bal_amt += parseInt(
              val.balance_amount != null ? val.balance_amount : 0
            );
            scheme_metal_wgt += parseFloat(
              val.metal_weight != null ? val.metal_weight : 0
            );
            sub_scheme_amt += parseInt(
              val.paid_amount != null ? val.paid_amount : 0
            );
            sub_collection_amt += parseInt(
              val.collection_amount != null ? val.collection_amount : 0
            );
          });
          title +=
            "<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>";
          // title+='<tr  style=font-weight:bold>'+
          // 	'<td class="highlighted-row">SUB TOTAL  </td>'+
          // 	'<td class="highlighted-row" style="text-align: right;">'+sub_scheme_count+'</td>&nbsp;&nbsp;'+
          // // 	'<td class="highlighted-row" style="text-align: right;">'+paid_ins_count+'</td>'+
          // 	'<td class="highlighted-row" style="text-align: right;">'+formatCurrency.format(parseFloat(sub_scheme_amt).toFixed(2))+'</td>'+
          // 	'<td class="highlighted-row" style="text-align: right;">'+formatCurrency.format(parseFloat(sub_collection_amt).toFixed(2))+'</td>'+
          // 	'<td class="highlighted-row" style="text-align: right;">'+formatCurrency.format(parseFloat(sub_closing_wt).toFixed(2))+'</td>'+
          // 	'<td class="highlighted-row" style="text-align: right;">'+formatCurrency.format(parseFloat(Math.abs(sub_bal_amt)).toFixed(2))+'</td>'+
          // 	'<td class="highlighted-row" style="text-align: right;">'+(sub_gold_metal_wgt!=0 ? parseFloat(sub_gold_metal_wgt).toFixed(3):"")+'</td>'+
          // 	'<td class="highlighted-row" style="text-align: right;">'+(sub_silver_metal_wgt!=0 ? parseFloat(sub_silver_metal_wgt).toFixed(3):"")+'</td>'+
          // 	'</tr>';
          title +=
            "<tr><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td><td>&nbsp;&nbsp; </td></tr>";
        });
        // 			title+='<tr  style=font-weight:bold>'+
        // 					'<td>GRAND TOTAL  </td>'+
        // 					'<td style="text-align: right;">'+scheme_count+'</td>&nbsp;&nbsp;'+
        // 				// 	'<td style="text-align: right;">'+paid_ins_count+'</td>'+
        // 					'<td style="text-align: right;">'+formatCurrency.format(parseFloat(scheme_amt).toFixed(2))+'</td>'+
        // 					'<td style="text-align: right;">'+formatCurrency.format(parseFloat(collection_amt).toFixed(2))+'</td>'+
        // 					'<td style="text-align: right;">'+formatCurrency.format(parseFloat(closed_amt).toFixed(2))+'</td>'+
        // 					'<td style="text-align: right;">'+formatCurrency.format(parseFloat(balace_amt).toFixed(2))+'</td>'+
        // 					'<td style="text-align: right;">'+(grand_gold_metal_wgt!=0 ? parseFloat(grand_gold_metal_wgt).toFixed(3):"")+'</td>'+
        // 					'<td style="text-align: right;">'+(grand_silver_metal_wgt!=0 ? parseFloat(grand_silver_metal_wgt).toFixed(3):"")+'</td>'+
        // 					'</tr>';
        // 			title+='</tbody></table></div></div>';
        let grandTotalRow =
          '<tr style="font-weight:bold">' +
          "<td>GRAND TOTAL</td>" +
          '<td style="text-align: right;">' +
          scheme_count +
          "</td>&nbsp;&nbsp;" +
          '<td style="text-align: right;">' +
          formatCurrency.format(parseFloat(scheme_amt).toFixed(2)) +
          "</td>";
        // '<td style="text-align: right;">' + formatCurrency.format(parseFloat(collection_amt).toFixed(2)) + '</td>' +
        // '<td style="text-align: right;">' + formatCurrency.format(parseFloat(closed_amt).toFixed(2)) + '</td>';
        if (selectedValue != 0) {
          grandTotalRow +=
            '<td style="text-align:right;">' +
            formatCurrency.format(parseFloat(collection_amt).toFixed(2)) +
            "</td>" +
            '<td style="text-align:right;">' +
            formatCurrency.format(parseFloat(closed_amt).toFixed(2)) +
            "</td>" +
            '<td style="text-align:right;">' +
            formatCurrency.format(parseFloat(balace_amt).toFixed(2)) +
            "</td>";
        }
        grandTotalRow +=
          '<td style="text-align: right;">' +
          (grand_gold_metal_wgt != 0
            ? parseFloat(grand_gold_metal_wgt).toFixed(3)
            : "") +
          "</td>" +
          '<td style="text-align: right;">' +
          (grand_silver_metal_wgt != 0
            ? parseFloat(grand_silver_metal_wgt).toFixed(3)
            : "") +
          "</td>" +
          "</tr>";
        title += grandTotalRow + "</tbody></table></div></div>";
        //scheme wise summary ends here
        //Group wise summary starts here
        $.each(data.scheme_summary, function (key, classification) {
          $.each(classification, function (scheme, val) {
            if (val.is_lucky_draw == 1) {
              title +=
                '<div class="row" style="text-align:center;"><div class="col-md-12"><strong><p style="margin:0;">Groupwise Accounts  -- ' +
                val.scheme_name.toUpperCase() +
                "</p></strong>";
              var grp_count = 0;
              var grp_ins_count = 0;
              var grp_paid_count = 0;
              var grp_gold_wgt_count = 0;
              var grp_silver_wgt_count = 0;
              if (val.group_scheme != null) {
                title +=
                  '<table class="table table-bordered table-striped" style="width:80%;margin-left:30px;"><thead><tr><th>Scheme Name</th><th>Acc count</th>&nbsp;&nbsp;<th>Amount</th><th>G.Weight</th><th>S.Weight</th></tr></thead><tbody>';
                $.each(val.group_scheme, function (keys, vals) {
                  title +=
                    "<tr>" +
                    '<td style="text-align: left;">' +
                    vals.group_code +
                    "</td>" +
                    '<td style="text-align: right;">' +
                    vals.count +
                    "</td>&nbsp;&nbsp;" +
                    // 			'<td style="text-align: right;">'+(vals.paid_installments!=null?vals.paid_installments:0)+'</td>'+
                    '<td style="text-align: right;">' +
                    (vals.paid_amount != null
                      ? formatCurrency.format(
                        parseFloat(vals.paid_amount).toFixed(2)
                      )
                      : 0) +
                    "</td>" +
                    '<td style="text-align: right;">' +
                    (vals.id_metal == 1 &&
                      isValid(vals.metal_weight) &&
                      vals.metal_weight != 0
                      ? parseFloat(vals.metal_weight).toFixed(3)
                      : "") +
                    "</td>" +
                    '<td style="text-align: right;">' +
                    (vals.id_metal == 2 &&
                      isValid(vals.metal_weight) &&
                      vals.metal_weight != 0
                      ? parseFloat(vals.metal_weight).toFixed(3)
                      : "") +
                    "</td>" +
                    "</tr>";
                  grp_count += parseInt(vals.count);
                  grp_ins_count += parseInt(
                    vals.paid_installments != null ? vals.paid_installments : 0
                  );
                  grp_paid_count += parseInt(
                    vals.paid_amount != null ? vals.paid_amount : 0
                  );
                  grp_gold_wgt_count +=
                    vals.id_metal == 1 && isValid(vals.metal_weight)
                      ? parseFloat(vals.metal_weight)
                      : 0;
                  grp_silver_wgt_count +=
                    vals.id_metal == 2 && isValid(vals.metal_weight)
                      ? parseFloat(vals.metal_weight)
                      : 0;
                });
                title +=
                  "<tr style=font-weight:bold>" +
                  '<td style="text-align: left;">Total  </td>' +
                  '<td style="text-align: right;">' +
                  grp_count +
                  "</td>&nbsp;&nbsp;" +
                  // 			'<td style="text-align: right;">'+grp_ins_count+'</td>'+
                  '<td style="text-align: right;">' +
                  formatCurrency.format(parseFloat(grp_paid_count).toFixed(2)) +
                  "</td>" +
                  '<td style="text-align: right;">' +
                  parseFloat(grp_gold_wgt_count).toFixed(3) +
                  "</td>" +
                  '<td style="text-align: right;">' +
                  parseFloat(grp_silver_wgt_count).toFixed(3) +
                  "</td>" +
                  "</tr>";
                title += "</tbody></table></div></div>";
              }
            }
          });
        });
        var htmlToPrint =
          "" +
          '<style type="text/css">' +
          "table th, table td {" +
          "border:1px solid #000;" +
          "padding:0.5em;" +
          "}" +
          "</style>";
        var styleHtml =
          "<style>td.highlighted-row {" +
          "border-top: 1px dashed black;" +
          "border-bottom: 1px dashed black;" +
          "}</style>";
        title += styleHtml;
        printWindow.document.write(title);
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
        //Group wise summary ends here
      }
    },
  });
  //for printing outstanding summary(data that are printed when print clicked) ends here
});
/*Renewal report starts...*/
/*Actual + Renewal + Live chit details based on referred employee starts....*/
if (ctrl_page[0] == "reports" && ctrl_page[1] == "renewal_live_report") {
  $("#renew_payments1").empty();
  $("#renew_payments2").empty();
  $("#renew_payments1").text(moment().startOf("month").format("YYYY-MM-DD"));
  $("#renew_payments2").text(moment().endOf("month").format("YYYY-MM-DD"));
  $("#renewal_livereport_date_range").html(
    moment().startOf("month").format("DD-MM-YYYY") +
    " to " +
    moment().endOf("month").format("DD-MM-YYYY")
  );
  getRenewalLiveList();
  get_schemename();
  $("#renew_payment_date").daterangepicker(
    {
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
      },
      startDate: moment().subtract(29, "days"),
      endDate: moment(),
    },
    function (start, end) {
      $("#renewal_livereport_date_range").html(
        start.format("DD-MM-YYYY") + " to " + end.format("DD-MM-YYYY")
      );
      //getRenewalLiveList();
      $("#renew_payments1").text(start.format("YYYY-MM-DD"));
      $("#renew_payments2").text(end.format("YYYY-MM-DD"));
    }
  );
  get_employee_list();
}
$("#search_renewal_list").on("click", function () {
  getRenewalLiveList();
});
function getRenewalLiveList() {
  $("div.overlay").css("display", "block");
  my_Date = new Date();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/getRenewalLive_arlData?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      from_date: $("#renew_payments1").html(),
      to_date: $("#renew_payments2").html(),
      ref_employee: $("#employee_select").val(),
      renew_type: $("#renew_type").val(),
      group_by: $("#renew_groupBy").val(),
      branch: $("#branch_select").val(),
      scheme: $("#scheme_select").val(),
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var branch_name = getBranchTitle();
      // $("#renewal_livereport_date_range").text($('#renew_payments1').html().format('DD-MM-YYYY') + " to " + $('#renew_payments2').html().format('DD-MM-YYYY'));
      var title = "";
      title += get_title(
        $("#renew_payments1").html(),
        $("#renew_payments2").html(),
        "Renewal/Live Report - " + branch_name
      );
      title += "</tbody></table></div></br>";
      $("div.overlay").css("display", "none");
      $("#renewal_live_report > tbody > tr").remove();
      $("#renewal_live_report").dataTable().fnClearTable();
      $("#renewal_live_report").dataTable().fnDestroy();
      trHTML = "";
      var total_actual_chits = 0;
      var total_renewal_chits = 0;
      var total_live_chits = 0;
      $.each(data.arlData, function (key, chits) {
        var actual_chits = 0;
        var renewal_chits = 0;
        var live_chits = 0;
        trHTML +=
          "<tr>" +
          "<td></td>" +
          '<th style="text-align:left;" class="bold-on-print report-key">' +
          key +
          "</th>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "<td></td>" +
          "</tr>";
        $.each(chits, function (key, items) {
          actual_chits += parseInt(items.actual_ct);
          renewal_chits += parseInt(items.renewal_ct);
          live_chits += parseInt(items.live_ct);
          //rows
          trHTML +=
            "<tr>" +
            '<td style="text-align:right;">' +
            parseInt(key + 1) +
            "</td>" +
            '<td style="text-align:left;">' +
            items.cus_name +
            "</td>" +
            '<td style="text-align:left;">' +
            items.mobile +
            "</td>" +
            '<td style="text-align:right;">' +
            (items.actual_ct == 0 ? "" : items.actual_ct) +
            "</td>" +
            '<td style="text-align:left;">' +
            items.actual_chits +
            "</td>" +
            '<td style="text-align:right;">' +
            (items.renewal_ct == 0 ? "" : items.renewal_ct) +
            "</td>" +
            '<td style="text-align:left;">' +
            items.renewal_chits +
            "</td>" +
            '<td style="text-align:right;">' +
            (items.live_ct == 0 ? "" : items.live_ct) +
            "</td>" +
            '<td style="text-align:left;">' +
            items.live_chits +
            "</td>" +
            "</tr>";
        });
        //sub total
        trHTML +=
          '<tr style="font-size:15px;">' +
          "<td></td>" +
          "<td></td>" +
          '<td style="text-align:left;"><strong class="report-sub-total">SUB TOTAL</strong></td>' +
          '<td style="text-align:right;" class="report-sub-total"><strong>' +
          (actual_chits == 0 ? "" : actual_chits) +
          "</strong></td>" +
          "<td></td>" +
          '<td style="text-align:right;" class="report-sub-total"><strong>' +
          (renewal_chits == 0 ? "" : renewal_chits) +
          "</strong></td>" +
          "<td></td>" +
          '<td style="text-align:right;" class="report-sub-total"><strong>' +
          (live_chits == 0 ? "" : live_chits) +
          "</strong></td>" +
          "<td></td>" +
          "</tr>";
        total_actual_chits += actual_chits;
        total_renewal_chits += renewal_chits;
        total_live_chits += live_chits;
      });
      // grand total
      trHTML +=
        '<tr style="font-size:15px;">' +
        "<td></td>" +
        "<td></td>" +
        '<td style="text-align:left;"><strong class="report-grand-total">GRAND TOTAL</strong></td>' +
        '<td style="text-align:right;" class="report-grand-total"><strong>' +
        (total_actual_chits == 0 ? "" : total_actual_chits) +
        "</strong></td>" +
        "<td></td>" +
        '<td style="text-align:right;" class="report-grand-total"><strong>' +
        (total_renewal_chits == 0 ? "" : total_renewal_chits) +
        "</strong></td>" +
        "<td></td>" +
        '<td style="text-align:right;" class="report-grand-total"><strong>' +
        (total_live_chits == 0 ? "" : total_live_chits) +
        "</strong></td>" +
        "<td></td>" +
        "</tr>";
      $("#renewal_live_report > tbody").html(trHTML);
      if (!$.fn.DataTable.isDataTable("#renewal_live_report")) {
        if (data.arlData.length !== 0) {
          oTable = $("#renewal_live_report").dataTable({
            bSort: false,
            bInfo: false,
            scrollX: "100%",
            dom: "lBfrtip",
            lengthMenu: [
              [-1, 25, 50, 100, 250],
              ["All", 25, 50, 100, 250],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: "",
                messageTop: title,
                orientation: "landscape",
                customize: function (win) {

                    // 🔹 Add the grouped header rows
                    var firstHeader = `
                        <tr style="text-transform:uppercase;">
                            <th colspan="3" style="color:#177ec5;">Customer</th>
                            <th colspan="2" style="color:#E74C3C;">Closed Accounts</th>
                            <th colspan="2" style="color:#7D3C98;">Renewal Accounts</th>
                            <th colspan="2" style="color:#28B463;">Active Accounts</th>
                        </tr>`;

                    var secondHeader = `
                        <tr style="text-transform:uppercase;">
                            <th width="2%" style="color:#177ec5;">S.No</th>
                            <th style="color:#177ec5;">Cus Name</th>
                            <th width="5%" style="color:#177ec5;">Mobile</th>
                            <th style="color:#E74C3C;">Count</th>
                            <th style="color:#E74C3C;">Acc</th>
                            <th style="color:#7D3C98;">Count</th>
                            <th style="color:#7D3C98;">Acc</th>
                            <th style="color:#28B463;">Count</th>
                            <th style="color:#28B463;">Acc</th>
                        </tr>`;

                    $(win.document.body).find('thead').html(firstHeader + secondHeader);

                    // 🔹 Table formatting
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '10px')
                        .css('font-family', 'sans-serif');

                    // 🔹 Bold specific cells when printing
                    $(win.document.body)
                        .find('td.bold-on-print')
                        .css('font-weight', 'bold');

                    // 🔹 Inject refined CSS for borders + landscape layout
                    var css = 'table.dataTable { ' +
                        'border-collapse: collapse !important; ' +
                        'width: 99% !important; ' +
                        'margin: 0 auto !important; ' +
                        'border: 1px solid black !important; ' +
                        'box-sizing: border-box !important; } ' +
                        'table.dataTable th, table.dataTable td { ' +
                        'border: 1px solid black !important; } ' +
                        '@page { size: landscape; margin: 10mm; }';

                    var head = win.document.head || win.document.getElementsByTagName('head')[0];
                    var style = win.document.createElement('style');
                    style.type = 'text/css';
                    style.media = 'print';

                    if (style.styleSheet) {
                        style.styleSheet.cssText = css;
                    } else {
                        style.appendChild(win.document.createTextNode(css));
                    }

                    head.appendChild(style);
                },
                exportOptions: { columns: ":visible" },
              },
              {
                extend: "excel",
                footer: true,
                title:
                  "Renewal/Live Report - " +
                  branch_name +
                  " " +
                  $("#renew_payments1").html() +
                  " - " +
                  $("#renew_payments2").html(),
                customize: function (xlsx) {
                  try {
                    // Parse the XML content of the sheet
                    var sheet = xlsx.xl.worksheets["sheet1.xml"];
                    var parser = new DOMParser();
                    var xmlDoc = parser.parseFromString(
                      sheet,
                      "application/xml"
                    );
                    // Check if sheetData element exists
                    var sheetData = xmlDoc.getElementsByTagName("sheetData")[0];
                    if (!sheetData) {
                      throw new Error("sheetData element not found");
                    }
                    // Define the headers as XML
                    var headers = `
                <row r="1">
                    <c t="inlineStr" s="2" r="A1"><is><t>Customer</t></is></c>
                    <c t="inlineStr" s="2" r="B1"></c>
                    <c t="inlineStr" s="2" r="C1"></c>
                    <c t="inlineStr" s="2" r="D1"><is><t>Closed Accounts</t></is></c>
                    <c t="inlineStr" s="2" r="E1"></c>
                    <c t="inlineStr" s="2" r="F1"><is><t>Renewal Accounts</t></is></c>
                    <c t="inlineStr" s="2" r="G1"></c>
                    <c t="inlineStr" s="2" r="H1"><is><t>Active Accounts</t></is></c>
                    <c t="inlineStr" s="2" r="I1"></c>
                </row>
                <row r="2">
                    <c t="inlineStr" s="2" r="A2"><is><t>S.No</t></is></c>
                    <c t="inlineStr" s="2" r="B2"><is><t>Cus Name</t></is></c>
                    <c t="inlineStr" s="2" r="C2"><is><t>Mobile</t></is></c>
                    <c t="inlineStr" s="2" r="D2"><is><t>Count</t></is></c>
                    <c t="inlineStr" s="2" r="E2"><is><t>Acc</t></is></c>
                    <c t="inlineStr" s="2" r="F2"><is><t>Count</t></is></c>
                    <c t="inlineStr" s="2" r="G2"><is><t>Acc</t></is></c>
                    <c t="inlineStr" s="2" r="H2"><is><t>Count</t></is></c>
                    <c t="inlineStr" s="2" r="I2"><is><t>Acc</t></is></c>
                </row>`;
                    // Parse headers to XML
                    var headersXml = parser
                      .parseFromString(headers, "application/xml")
                      .getElementsByTagName("row");
                    // Insert header rows at the beginning of sheetData
                    for (var i = headersXml.length - 1; i >= 0; i--) {
                      sheetData.insertBefore(
                        headersXml[i],
                        sheetData.firstChild
                      );
                    }
                    // Serialize XML back to string
                    var serializer = new XMLSerializer();
                    xlsx.xl.worksheets["sheet1.xml"] =
                      serializer.serializeToString(xmlDoc);
                  } catch (error) {
                    console.error("Error customizing Excel export:", error);
                  }
                },
              },
              {
                extend: "colvis",
                collectionLayout: "fixed columns",
                collectionTitle: "Column visibility control",
              },
            ],
          });
        } else {
          var brHTML = "";
          brHTML +=
            '<tr><td colspan=9 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
          $("#renewal_live_report > tbody").html(brHTML);
        }
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
/*renewal report ends*/
if (ctrl_page[0] == "reports" && ctrl_page[1] == "customer_wishes") {
  // 26-11
  $("#celeb_date1").empty();
  $("#celeb_date2").empty();
  if (ctrl_page[2] == 1) {
    $("#celeb_date1").text(moment().format("DD-MM-YYYY"));
    $("#celeb_date2").text(moment().format("DD-MM-YYYY"));
  } else if (ctrl_page[2] == 2) {
    $("#celeb_date1").text(moment().add(1, "days").format("DD-MM-YYYY"));
    $("#celeb_date2").text(moment().add(1, "days").format("DD-MM-YYYY"));
  } else if (ctrl_page[2] == 3) {
    $("#celeb_date1").text(moment().startOf("week").format("DD-MM-YYYY"));
    $("#celeb_date2").text(moment().endOf("week").format("DD-MM-YYYY"));
  } else {
    $("#celeb_date1").text(moment().startOf("month").format("DD-MM-YYYY"));
    $("#celeb_date2").text(moment().endOf("month").format("DD-MM-YYYY"));
  }
  getCelebDates();
  const ranges = {
    Today: [moment(), moment()],
    Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
    Tomorrow: [moment().add(1, "days"), moment().add(1, "days")],
    "Last 7 Days": [moment().subtract(6, "days"), moment()],
    "Last 30 Days": [moment().subtract(29, "days"), moment()],
    "This Month": [moment().startOf("month"), moment().endOf("month")],
    "This Week": [moment().startOf("week"), moment().endOf("week")],
    "Last Month": [
      moment().subtract(1, "month").startOf("month"),
      moment().subtract(1, "month").endOf("month"),
    ],
  };
  // Define startDate and endDate based on ctrl_page[2]
  let startDate, endDate;
  if (ctrl_page[2] == 1) {
    startDate = moment();
    endDate = moment();
  } else if (ctrl_page[2] == 2) {
    startDate = moment().add(1, "days");
    endDate = moment().add(1, "days");
  } else if (ctrl_page[2] == 3) {
    startDate = moment().startOf("week");
    endDate = moment().endOf("week");
  } else {
    startDate = moment().subtract(29, "days");
    endDate = moment();
  }
  $("#celeb_date").daterangepicker(
    {
      ranges,
      startDate,
      endDate,
    },
    function (start, end) {
      $("#celeb_date span").html(
        start.format("MMMM D, YYYY") + " to " + end.format("MMMM D, YYYY")
      );
      $("#celeb_date1").text(start.format("DD-MM-YYYY"));
      $("#celeb_date2").text(end.format("DD-MM-YYYY"));
      getCelebDates();
    }
  );
}
function getCelebDates() {
  $("div.overlay").css("display", "block");
  var onHTML = "";
  onHTML +=
    '<tr><td colspan=8 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
  $("#celebration_list > tbody").html(onHTML);
  my_Date = new Date();
  $.ajax({
    url:
      base_url +
      "index.php/admin_reports/cus_celeb_dates?nocache=" +
      my_Date.getUTCSeconds(),
    data: {
      from_date: $("#celeb_date1").html(),
      to_date: $("#celeb_date2").html(),
    },
    dataType: "JSON",
    type: "POST",
    success: function (data) {
      var data = data.data;
      console.log(data.length);
      console.log(data);
      $("#celeb_report_date_range").text(
        $("#celeb_date1").html() + " to " + $("#celeb_date2").html()
      );
      var title = "";
      title += get_title(
        $("#celeb_date1").html(),
        $("#celeb_date2").html(),
        "Birthday/Wedding Day Report"
      );
      $("div.overlay").css("display", "none");
      $("#celebration_list > tbody > tr").remove();
      $("#celebration_list").dataTable().fnClearTable();
      $("#celebration_list").dataTable().fnDestroy();
      trHTML = "";
      $.each(data, function (key, items) {
        trHTML +=
          "<tr>" +
          "<td>" +
          parseInt(key + 1) +
          "</td>" +
          "<td>" +
          items.firstname +
          "</td>" +
          "<td>" +
          items.mobile +
          "</td>" +
          "<td>" +
          items.city_name +
          "</td>" +
          "<td>" +
          items.birthday +
          "</td>" +
          "<td>" +
          items.wedday +
          "</td>" +
          "<td>" +
          items.active_acc +
          "</td>" +
          "<td>" +
          items.closed_acc +
          "</td>" +
          "</tr>";
      });
      $("#celebration_list > tbody").html(trHTML);
      if (!$.fn.DataTable.isDataTable("#celebration_list")) {
        if (data.length > 0) {
          oTable = $("#celebration_list").dataTable({
            bSort: false,
            bInfo: false,
            // 		"scrollX": '100%',
            dom: "lBfrtip",
            pageLength: 25,
            lengthMenu: [
              [-1, 25, 50, 100, 250],
              ["All", 25, 50, 100, 250],
            ],
            buttons: [
              {
                extend: "print",
                footer: true,
                title: title,
                orientation: "landscape",
                exportOptions: { columns: ":visible" },
              },
              {
                extend: 'excel',
                footer: true,
                title: 'Birthday/Wedding Day Report' + $('#celeb_date1').html() + ' - ' + $('#celeb_date2').html(),
              },
              {
                extend: "colvis",
                collectionLayout: "fixed columns",
                collectionTitle: "Column visibility control",
              },
            ],
            columnDefs: [
              {
                targets: [0, 6, 7],
                className: "dt-right",
              },
              {
                targets: [1, 2, 3, 4, 5],
                className: "dt-left",
              },
            ],
          });
        } else {
          var onHTML = "";
          onHTML +=
            '<tr><td colspan=8 style="color:red;font-weight:bold;text-align:center;">No Data Available</td></tr>';
          $("#celebration_list > tbody").html(onHTML);
        }
      }
    },
    error: function (error) {
      $("div.overlay").css("display", "none");
    },
  });
}
function formatDate(date) {
  const parts = date.split('-');
  return parts[2] + '-' + parts[1] + '-' + parts[0]; // Convert DD-MM-YYYY to YYYY-MM-DD
}

