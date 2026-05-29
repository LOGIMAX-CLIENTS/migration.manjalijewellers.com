var path = url_params();
var ctrl_page = path.route.split('/');
let indianCurrency = Intl.NumberFormat('en-IN');
var f_date = "";
var t_date = "";
$(document).ready(function () {
	if (ctrl_page[0] == 'admin' && ctrl_page[1] == 'dashboard' && ctrl_page[2] == 'collection_App') {
		var from_date = ctrl_page[3];
		var to_date = ctrl_page[4];
		var id_branch = ctrl_page[6];
		var id_employee = ctrl_page[5];
		$.ajax({
			type: 'POST',
			url: base_url + 'index.php/admin_dashboard/ajax_get_collection_list',
			dataType: 'json',
			async: false,
			data: { 'id_branch': id_branch, 'from_date': from_date, 'to_date': to_date, 'id_employee': id_employee },
			success: function (data) {
				set_collection_app_list(data);
			}
		});
	}
	if (ctrl_page[0] == 'admin' && ctrl_page[1] == 'dashboard') {
		var date = new Date();
		var from_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		$('#payment_list1').text(from_date);
		$('#payment_list2').text(to_date);
		if ($('.tab-pane.active').attr('id') == "crm") {
			getindex();
			$(".metalselect").css("display", "none");
		}
		get_live_cockpit_dashboard_details();
		$("body").on("click", "#tab_livecockpit", function () {
			//retail_dashboard_details(from_date,to_date);
			get_live_cockpit_dashboard_details();
			$(".metalselect").css("display", "none");
		});
		var date_type = $("#date_type").val();
		var id_branch = $("#id_branch").val();
		get_payment_summary(from_date, to_date, id_branch, date_type);
		$("body").on("click", "#tab_order_management", function () {
			get_order_management_details();
			$(".metalselect").css("display", "none");
		});
		$("body").on("click", "#tab_sales", function () {
			let from_date = $('#payment_list1').text();
			let to_date = $('#payment_list2').text();
			sales_dashboard_data(from_date, to_date);
			$(".metalselect").css("display", "none");
		});
		$("body").on("click", "#tab_sale_gchart", function () {
			set_sale_dashboard();
			$(".metalselect").css("display", "none");
		});
		$("body").on("click", "#tab_stock_gchart", function () {
			// Load the Visualization API and the piechart package.
			set_stock_dashboard();
			$(".metalselect").css("display", "none");
		});
		$("body").on("click", "#tab_purchase", function () {
			// Load the Visualization API and the piechart package.
			$(".metalselect").css("display", "block");
			set_purchase_dashboard();
		});
		$("body").on("click", "#tab_contract_pricing", function () {
			get_contract_pricing();
			$(".metalselect").css("display", "none");
		});
		sales_details(from_date, to_date);
		check_LedgerBalanceAlert();
	}
	if (ctrl_page[0] == 'admin' && ctrl_page[1] == 'dashboard' && $('#branch_set').val() == 0) {
		var date = new Date();
		var from_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		var date_type = $("#date_type").val();
		getindex(from_date, to_date, date_type);
		$("#customer_count_span").empty();
		$("#without_acc_cus_count").empty();
		$("#without_pay_acc_count").empty();
		set_customer_link(from_date, to_date);
		get_collection_app_details(from_date, to_date);
		get_collection_summary(from_date, to_date);
		// schWise_accounts is already called inside getindex() above
		//$(".overlay").css('display','none');
	}
	if (ctrl_page[0] == 'customer_detail') {
		get_customer_data();
	}
	//dashboard new work....
	console.log(moment().format('YYYY-MM-DD'));
	$('#branch_select').on("change", function (e) {
		var from_date = $('#payment_list1').text();
		var to_date = $('#payment_list2').text();
		//alert(from_date);
		if (this.value != '') {
			$("#customer_count_span").empty();
			$("#without_acc_cus_count").empty();
			$("#without_pay_acc_count").empty();
			$("#id_branch").val(this.value);
			var id_branch = $("#id_branch").val();
			var date_type = $("#date_type").val();
			set_customer_link(from_date, to_date, date_type);
			get_collection_summary(from_date, to_date, id_branch);	// esakki 28-09
			get_accounts_bydate(from_date, to_date, id_branch, date_type);
			get_payment_summary(from_date, to_date, id_branch, date_type);
			if ($('.tab-pane.active').attr('id') == "crm") {
				getindex('', '', date_type);
			}
			if ($('.tab-pane.active').attr('id') == "sales") {
				let from_date = $('#payment_list1').text();
				let to_date = $('#payment_list2').text();
				sales_dashboard_data(from_date, to_date);
			}
		}
		else {
			$("#id_branch").val('');
		}
	});
	// date_type filter change handler
	$('#date_type').on("change", function() {
		var from_date = $('#payment_list1').text();
		var to_date = $('#payment_list2').text();
		var id_branch = $('#branch_select').val();
		var date_type = $(this).val();
		get_payment_summary(from_date, to_date, id_branch, date_type);
		if ($('.tab-pane.active').attr('id') == "crm") {
			getindex(from_date, to_date, date_type);
			set_customer_link(from_date, to_date, date_type);
			get_collection_app_details(from_date, to_date, date_type);
			get_accounts_bydate(from_date, to_date, id_branch, date_type);
		}
	});
	$('#payment_list1').text(moment().format('YYYY-MM-DD'));
	$('#payment_list2').text(moment().format('YYYY-MM-DD'));
	f_date = $('#payment_list1').text();
	$("#cus_from_date").val(f_date);
	t_date = $('#payment_list2').text();
	//set_date_customer(f_date,t_date);
	// alert(ff);
	//$('#payment_show1').text(moment().format('DD-MM-YYYY'));
	//$('#payment_show2').text(moment().format('DD-MM-YYYY'));
	$('#payment-dt-btn').daterangepicker(
		{
			ranges:
			{
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
		function (start, end) {
			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			$('#payment_list1').text(start.format('YYYY-MM-DD'));
			$('#payment_list2').text(end.format('YYYY-MM-DD'));
			//  $('#payment-dt-btn').text(start.format('D-MM-YYYY') + ' - ' + end.format('D-MM-YYYY'));
			$("#customer_count_span").empty();
			$("#without_acc_cus_count").empty();
			$("#without_pay_acc_count").empty();
			var id_branch = $('#branch_select').val();
			var date_type = $("#date_type").val();
			get_collection_summary(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), id_branch)
			//			get_accounts_bydate(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_branch);
			//					get_customer_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_branch);
			get_payment_summary(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), id_branch, date_type);
			if ($('.tab-pane.active').attr('id') == "crm") {
				getindex('', '', date_type);
				set_customer_link(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), date_type);
				get_collection_app_details(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), date_type);
			}
			if ($('.tab-pane.active').attr('id') == "purchase") {
				set_purchase_dashboard();
			}
			if ($('.tab-pane.active').attr('id') == "sales") {
				let from_date = $('#payment_list1').text();
				let to_date = $('#payment_list2').text();
				sales_dashboard_data(from_date, to_date);
			}
			if ($('.tab-pane.active').attr('id') == "live_cockpit") {
				retail_dashboard_details(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_EstimationStatus(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_BillingStatus(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_GreentagSalesDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_VitrualTagStatus(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_SalesreturnDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_metal_purchase_status(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_CreditSalesDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_GiftVoucherDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_BillClassficationDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_BranchTransferDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_lot_tag_details(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_OrderDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_StockDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_ReorderDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_KarigarOrderDetails();
				get_CustomerOrderDetails();
				get_MetalStockDetails();
				get_RecentBillDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_cash_abstract_details(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				getEstimationDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_CustomerDetails(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
				get_stock_details(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
			}
			if ($('.tab-pane.active').attr('id') == "sale_gchart") {
				frm_date = start;
				to_date = end;
				$('.sale_dt_range').each(function () {
					var elementId = this.id; // Capture the ID before entering the callback function
					var that = $(this);
					that.find('.show_date').text(frm_date.format('DD-MM-YYYY') + ' - ' + to_date.format('DD-MM-YYYY'));
					that.find('.payment_list3').text(frm_date.format('YYYY-MM-DD'));
					that.find('.payment_list4').text(to_date.format('YYYY-MM-DD'));
					$(this).daterangepicker(
						{
							ranges:
							{
								'Today': [moment(), moment()],
								'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
								'Last 7 Days': [moment().subtract(6, 'days'), moment()],
								'Last 30 Days': [moment().subtract(29, 'days'), moment()],
								'This Month': [moment().startOf('month'), moment().endOf('month')],
								'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
							},
							startDate: frm_date,
							endDate: to_date
						},
						function (start, end) {
							console.log(elementId); // Use the captured element ID here
							that.find('.show_date').text(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
							that.find('.payment_list3').text(start.format('YYYY-MM-DD'));
							that.find('.payment_list4').text(end.format('YYYY-MM-DD'));
							switch (elementId) {
								case 'dt_sales_glance':
									get_sales_glance();
									break;
								case 'dt_top_selling':
									get_top_selling();
									break;
								case 'dt_top_sellers':
									get_top_sellers();
									break;
								case 'dt_branch_compare':
									get_branch_comparison_details();
									break;
								case 'dt_store_sales':
									get_store_wise_sales();
									break;
								case 'dt_branch_avg_va':
									get_branch_avg_va_details();
									break;
								case 'dt_product_sales':
									get_product_sales();
									break;
								case 'dt_emp_sales':
									get_employee_sales();
									break;
								case 'dt_section_sales':
									get_section_sales();
									break;
								case 'dt_karigar_sales':
									get_karigar_sales();
									break;
								case 'dt_custome_wise_sale':
									get_custome_wise_sale();
									break;
							}
						}
					);
				});
				get_sales_glance();
				get_top_selling();
				get_top_sellers();
				get_branch_comparison_details();
				get_store_wise_sales();
				get_branch_avg_va_details();
				get_product_sales();
				get_employee_sales();
				get_section_sales();
				get_karigar_sales();
				get_custome_wise_sale();
			}
		}
	);
	// ends..
	$('#emp_select').select2().on("change", function (e) {
		if (this.value != '') {
			var id_emp = this.value;
			if (ctrl_page[0] == 'admin_dashboard' && ctrl_page[1] == 'get_account') {
				get_scheme_account_list_bybranch(id_emp, ctrl_page[2], ctrl_page[3], ctrl_page[4]);
			} else if (ctrl_page[0] == 'admin_dashboard' && ctrl_page[1] == 'get_account_joined') {
				get_scheme_account_list_bysource(id_emp, ctrl_page[4], ctrl_page[2], ctrl_page[3]);
			}
		}
	});
	if (ctrl_page[0] == 'admin_dashboard' && ctrl_page[1] == 'get_account') {
		var id_branch = ctrl_page[2];
		var from_date = ctrl_page[3];
		var to_date = ctrl_page[4];
		if (id_branch != '' && from_date != '' && to_date != '') {
			get_scheme_account_list_bybranch("", id_branch, from_date, to_date);
			get_employee_name(id_branch);
		}
	}
	if (ctrl_page[0] == 'admin_dashboard' && ctrl_page[1] == 'get_account_joined') {
		var added_by = ctrl_page[4];
		var from_date = ctrl_page[2];
		var to_date = ctrl_page[3];
		if (added_by != '' && from_date != '' && to_date != '') {
			get_scheme_account_list_bysource("", added_by, from_date, to_date);
			get_employee_name('');
		}
	}
	if (ctrl_page[0] == 'admin_dashboard' && ctrl_page[1] == 'get_payment_joined') {
		var added_by = ctrl_page[4];
		var from_date = ctrl_page[2];
		var to_date = ctrl_page[3];
		if (added_by != '' && from_date != '' && to_date != '') {
			$.ajax({
				type: 'POST',
				url: base_url + 'index.php/admin_dashboard/ajax_get_payment_joined',
				dataType: 'json',
				async: false,
				data: { 'added_by': added_by, 'from_date': from_date, 'to_date': to_date },
				success: function (data) {
					set_scheme_payment_list(data);
				}
			});
		}
	}
	// Created by RK - 15/12/2022
	if (ctrl_page[0] == 'admin_dashboard' && ctrl_page[1] == 'customer_wishes') {
		if (ctrl_page[3] == 'T') {
			var type = ctrl_page[2];
			var byfilter = ctrl_page[3];
			$.ajax({
				type: 'POST',
				url: base_url + 'index.php/admin_dashboard/ajax_customer_wishes',
				dataType: 'json',
				async: false,
				data: { 'type': type, 'byfilter': byfilter },
				success: function (data) {
					console.log(data);
					set_customer_wish_list(data);
				}
			});
		}
	}
	function set_customer_wish_list(data) {
		//alert();
		var accounts = data.accounts;
		console.log(accounts);
		var oTable = $('#customer_list').dataTable();
		if (accounts != null && accounts.length > 0) {
			oTable = $('#customer_list').dataTable({
				"bDestroy": true,
				"bInfo": true,
				"bFilter": true,
				"bSort": true,
				"dom": 'lBfrtip',
				"buttons": ['excel', 'print'],
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
				]
			});
		}
	}
	//Created by RK - 15/12/2022--- ends here
	function get_scheme_account_list_bysource(id_employee = "", added_by, from_date, to_date) {
		$.ajax({
			type: 'POST',
			url: base_url + 'index.php/admin_dashboard/ajax_get_account_joined',
			dataType: 'json',
			async: false,
			data: { 'added_by': added_by, 'from_date': from_date, 'to_date': to_date, 'id_employee': id_employee },
			success: function (data) {
				set_scheme_account_list(data);
			}
		});
	}
	function get_scheme_account_list_bybranch(id_employee = "", id_branch, from_date, to_date) {
		$.ajax({
			type: 'POST',
			url: base_url + 'index.php/admin_dashboard/ajax_get_account',
			dataType: 'json',
			async: false,
			data: { 'id_branch': id_branch, 'from_date': from_date, 'to_date': to_date, 'id_employee': id_employee },
			success: function (data) {
				set_scheme_account_list(data);
			}
		});
	}
	$('#mobilenumber').val('');
	$('body').addClass("sidebar-collapse");
	$("#print").val('1');
	$('#mob_submit').on('click', function () {
		var mobile_number = $('#mobilenumber').val();
		console.log(mobile_number);
		$.ajax({
			type: 'POST',
			url: base_url + 'index.php/admin_customer/get_customer_by_mobile',
			dataType: 'json',
			async: false,
			data: { 'mobile': mobile_number },
			success: function (data) {
				console.log(data);
				if (data.result == true) {
					if ($('#mobilenumber').val().length == 10 && data.cus.id_customer > 0) {
						window.location.href = base_url + 'index.php/customer/edit/' + data.cus.id_customer;
					} else {
						alert('Customer not found..');
						$('#mobilenumber').val('');
					}
				}
				else {
					alert('please Enter a Valid Number');
					$('#mobilenumber').val('')
				}
			}
		});
	});
	if ($('#branch_set').val() == 0) {
		var date = new Date();
		var from_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		get_accounts_bydate(from_date, to_date);
	}
	if ($('#branch_set').val() == 1) {
		//get_branchname();
		var date = new Date();
		var from_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());
		get_accounts_bydate(from_date, to_date);
		set_customer_link(from_date, to_date);
		get_payment_list(from_date, to_date);
		get_account_list(from_date, to_date);
		get_customer_list(from_date, to_date);
		get_collection_app_details(from_date, to_date);
		// schWise_accounts(from_date,to_date);
		get_collection_list();
		get_collection_summary(from_date, to_date);
		inter_wallet_list(from_date, to_date);
		get_register_list();
		//$('#payment_list1').empty();
		//$('#payment_list2').empty();
		$("#detailed").on('click', function () {
			$("#collDetail").css('display', 'block');
			$("#collSummary").css('display', 'none');
			$("#print").val('2');
		});
		$("#summary").on('click', function () {
			$("#collDetail").css('display', 'none');
			$("#collSummary").css('display', 'block');
			$("#print").val('1');
		});
		$("#rtl").on('click', function () {
			$("#retail").css('display', 'block');
			$("#crm").css('display', 'none');
		})
		$("#crmBtn").on('click', function () {
			$("#retail").css('display', 'none');
			$("#crm").css('display', 'block');
		})
		$("#print").on('click', function () {
			if ($("#print").val() == 1) {
				newWin = window.open("");
				var divToPrint = document.getElementById("collSummary");
				newWin.document.write(divToPrint.outerHTML);
				newWin.document.title = 'Summary Collection Report';
				newWin.print();
				newWin.close();
			}
			else {
				newWin = window.open("");
				var divToPrint = document.getElementById("collDetail");
				newWin.document.write(divToPrint.outerHTML);
				newWin.document.title = 'Detailed Collection Report';
				newWin.print();
				newWin.close();
			}
		});
		//23-12-2022
		/*console.log($('.tab-pane.active').attr('id'));
			$('#payment_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
			$('#payment_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
				$('#payment-dt-btn').daterangepicker({
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
				function (start, end) {
					$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
					$('#payment_list1').text(start.format('YYYY-MM-DD'));
					$('#payment_list2').text(end.format('YYYY-MM-DD'));
					if($('.tab-pane.active').attr('id') == "crm") {
						$('#payment_list tbody').remove();
						$('#account_list tbody').remove();
						$('#interWalList tbody').remove();
						getindex();
					}
					if($('.tab-pane.active').attr('id') == "live_cockpit") {
						retail_dashboard_details(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_EstimationStatus(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_BillingStatus(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_GreentagSalesDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_VitrualTagStatus(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_SalesreturnDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_metal_purchase_status(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_CreditSalesDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_GiftVoucherDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_BillClassficationDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_BranchTransferDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_lot_tag_details(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_OrderDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_StockDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_ReorderDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_KarigarOrderDetails();
						get_CustomerOrderDetails();
						get_MetalStockDetails();
						get_RecentBillDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_cash_abstract_details(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						getEstimationDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
						get_CustomerDetails(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
					}
					  if($('.tab-pane.active').attr('id') == "sales") {
						sales_dashboard_data(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
					  }
					  if($('.tab-pane.active').attr('id') == "order_management") {
							get_order_management_details();
					  }
						$('#mob').empty();
						$('#web').empty();
						$('#admin').empty();
						$('#collection').empty();
						$('#thr_m').empty();
						$('#thr_w').empty();
						$('#thr_a').empty();
						$('#test').empty();
						$('#all_reg').empty();
						$('#registered').empty();
						$('#not_registered').empty();
						$('#cus_web').empty(); //date wise filter cus reg  by hh//
						$('#cus_mob').empty();
						$('#cus_admin').empty();
						$('#thr_c').empty();
						$('#cus_collection').empty();
				  }
				);
		*/
	}
	var line = new Morris.Line
		({
			element: 'rate-chart',
			resize: true,
			data: getRateData(),
			xkey: 'updatetime',
			ykeys: ['rate'],
			labels: ['Gold 22ct'],
			lineColors: ['#efefef'],
			lineWidth: 2,
			hideHover: 'auto',
			gridTextColor: "#fff",
			gridStrokeWidth: 0.4,
			pointSize: 4,
			pointStrokeColors: ["#efefef"],
			gridLineColor: "#efefef",
			gridTextFamily: "Open Sans",
			gridTextSize: 10
		});
});
function getRateData() {
	var data = "";
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/rate/ajax/weekstat',
		dataType: 'json',
		async: false,
		data: {},
		success: function (result) {
			data = result;
		},
		error: function (xhr, status, error) {
			console.log(error);
		}
	});
	return data;
}
function get_branchname() {
	//alert();
	$(".overlay").css('display', 'block');
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/branch/branchname_list',
		dataType: 'json',
		success: function (data) {
			console.log(data);
			$('#branch_select').append(
				$("<option></option>")
					.attr("value", 0)
					.text('All')
			);
			$.each(data, function (key, item) {
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
			$("#branch_select").select2("val", (branch_id != '' ? branch_id : ''));
			$(".overlay").css("display", "none");
		}
	});
}
/*$('#branch_select').select2().on("change", function(e) {
	if(this.value!='')
	{
		$("#id_branch").val(this.value);
		var id_branch=$("#id_branch").val();
			if($('.tab-pane.active').attr('id') == "crm")
			 {
					getindex();
			}
	}
	else
	{
	$("#id_branch").val('');
	}
}); */
function getindex(from_date = "", to_date = "", date_type = "") {
	$('#payment_list tbody').remove();
	$('#account_list tbody').remove();
	$('#interWalList tbody').remove();
	$('#mob').empty();
	$('#web').empty();
	$('#admin').empty();
	$('#collection').empty();
	$('#thr_m').empty();
	$('#thr_w').empty();
	$('#thr_a').empty();
	$('#test').empty();
	$('#all_reg').empty();
	$('#registered').empty();
	$('#not_registered').empty();
	$('#cus_web').empty(); //date wise filter cus reg  by hh//
	$('#cus_mob').empty();
	$('#cus_admin').empty();
	$('#thr_c').empty();
	$('#cus_collection').empty();
	$('#mob_paid').empty();
	$('#web_paid').empty();
	$('#admin_paid').empty();
	$('#collection_paid').empty();
	if (from_date == '' && to_date == '') {
		var from_date = $('#payment_list1').html();
		var to_date = $('#payment_list2').html();
	}
	if (date_type == '') {
		date_type = $("#date_type").val();
	}
	$("div.overlay").css("display", "block");
	var id_branch = $("#id_branch").val();
	schWise_accounts(from_date, to_date, date_type);
	$.ajax({
		type: "POST",
		url: base_url + "index.php/admin_dashboard/dashboard",
		data: { 'id_branch': id_branch, 'from_date': from_date, 'to_date': to_date, 'date_type': date_type },
		sync: false,
		dataType: 'json',
		success: function (response) {
			//12-12-2022,AB
			get_account_list(from_date, to_date, date_type);
			get_payment_list(from_date, to_date, date_type);
			get_customer_list(from_date, to_date, date_type);
			get_collection_app_details(from_date, to_date, date_type);
			console.log(response);
			//closed
			$("#closed").text(response.closed);
			$("#renewal").text(response.renewal);
			$("#two_pending").text(response.two_pending);
			$("#one_pending").text(response.one_pending);
			//existing
			$("#e_all_reg").text(response.existing_request.all_reg);
			$("#total_request").text(response.existing_request.total_request);
			$("#exiting_processing").text(response.existing_request.exiting_processing);
			$("#exiting_approved").text(response.existing_request.exiting_approved);
			$("#exiting_rejected").text(response.existing_request.exiting_rejected);
			/* 18-10-2023 #AB ....donot uncomment this part....
			   get_collection_list(from_date,to_date);
			   get_collection_summary(from_date,to_date);
			   $("#test").text(response.payment.today.paid);
			   $("#tot_pay").text(indianCurrency.format(response.payment.today.paid));
			   $("#t_paid").text(response.payment.today.paid);
			   $("#y_paid").text(response.payment.yesterday.paid);
			   $("#tw_paid").text(response.payment.week.paid);
			   $("#tm_paid").text(response.payment.month.paid);
			   $("#awaiting").text(response.payment.awaiting.paid);
			   $("#thr_a").text(response.payment.admin_paid.joined_thro);
			   $("#thr_w").text(response.payment.web_paid.joined_thro);
			   $("#thr_m").text(response.payment.mob_paid.joined_thro);
			   $("#thr_c").text(response.payment.collection_paid.joined_thro);
			   //acounts
			   $("#wk_reg").text(response.account.wk_reg);
			   $("#yes_reg").text(response.account.yes_reg);
			   $("#m_reg").text(response.account.m_reg);
			   $("#today_reg").text(response.account.today_reg);
			  // $("#all_acc").text(response.account.all_reg);
			   $("#all_reg").text(response.account.all_reg);
			   $("#mob_reg").text(response.account.mob.joined_thro);
			   $("#w_reg").text(response.account.web.joined_thro);
			   $("#a_reg").text(response.account.admin.joined_thro);
			   $("#acc_wo_pay").text(response.account.acc_wo_pay);
			   $("#c_reg").text(response.account.collection.joined_thro);  */
			//inter wallet
			/*
					   $("#t_trans").text(response.inter_wallet.t_trans);
					   $("#t_redeem_trans").text(response.inter_wallet.t_redeem_trans);
					   $("#y_trans").text(response.inter_wallet.y_trans);
					   $("#y_redeem_trans").text(response.inter_wallet.y_redeem_trans);
					   $("#tw_trans").text(response.inter_wallet.tw_trans);
					   $("#tw_redeem_trans").text(response.inter_wallet.tw_redeem_trans);
					   $("#tm_trans").text(response.inter_wallet.tm_trans);
					   $("#tm_redeem_trans").text(response.inter_wallet.tm_redeem_trans);  */
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function get_payment_list(from_date = "", to_date = "", date_type = "") {
	var id_branch = $('#branch_select').val();
	var log_branch = $('#id_branch').val();
	if(log_branch != 0){
		id_branch = log_branch;
	}
	my_Date = new Date();
	// $("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_dashboard/payment_status?nocache=" + my_Date.getUTCSeconds(),
		//data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch}: ''),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			set_payment_list(data, from_date, to_date);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_payment_list(data, from_date, to_date)   // Sch Pay & Act Paid Split n Showed in Dashboard//HH
{
	//23-12-2022,AB
	$('#payment_list').empty();
	$('#thr_a').empty();
	$('#thr_w').empty();
	$('#thr_m').empty();
	$('#thr_c').empty();
	$('#thr_aw').empty();
	$('#thr_ww').empty();
	$('#thr_mw').empty();
	$('#thr_cw').empty();
	var sum = 0;
	var divide = 0;
	$.each(data.payment_status, function (index, element) {
		id_branch = element.id_branch;
		sum += Number(element.paid);
		// sum += Number(element.act_paid);
		var num_parts = element.paid.toString().split(".");
		num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		var paid = num_parts.join(".");
		$("#test").text(sum);
		$("#tot_pay").text(indianCurrency.format(sum));
		list_url = base_url + 'index.php/admin_dashboard/get_payment/' + id_branch + "/" + from_date + "/" + to_date;
		$('#payment_list').append(
			$('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.short_name))
				// .append($('<td style="padding: 5px;">').append(element.currency_symbol+' '+element.paid))
				.append($('<td>').append('<a href=' + list_url + '><span class="badge bg-green">' + element.id_payment + '</span></a>')).append($('<td style="padding: 5px; text-align:right;">').append('<span> &#8377;</span>' + ' ' + indianCurrency.format(element.paid)))
		);
		var rowCount = $('#payment_list td').length;
	});
	/* online_list_url=base_url+'index.php/admin_dashboard/get_online_payment/'+from_date +'/'+to_date;
	 $('#payment_list').append(
	 $('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(data.online.branch_name))
	 .append($('<td style="padding: 5px;">').append("INR"+' '+data.online.paid))
	 .append($('<td style="padding: 5px;">').append("INR"+' '+data.online.paid))
	 .append($('<td>').append('<a href='+online_list_url+'><span class="badge bg-green">'+data.online.count+'</span></a>'))
	 ); */
	admin_url = base_url + 'index.php/admin_dashboard/get_payment_joined/' + from_date + "/" + to_date + "/" + 0;
	web_url = base_url + 'index.php/admin_dashboard/get_payment_joined/' + from_date + "/" + to_date + "/" + 1;
	app_url = base_url + 'index.php/admin_dashboard/get_payment_joined/' + from_date + "/" + to_date + "/" + 2;
	collection_url = base_url + 'index.php/admin_dashboard/get_payment_joined/' + from_date + "/" + to_date + "/" + 3;
	// $("#").text(data.mob_paid.joined_thro);
	// $("#web_paid").text(data.web_paid.joined_thro);
	// $("#admin_paid").text(data.admin_paid.joined_thro);
	$("#thr_a").append('<a href=' + admin_url + '><span class="badge bg-green">' + data.admin_paid.joined_thro + '</span></a>')
	$("#thr_aw").append('<span style="margin-left: 50px;">' + '&#8377;' + ' ' + indianCurrency.format(data.admin_paid.amount) + '</span>');
	$("#thr_w").append('<a href=' + web_url + '><span class="badge bg-green">' + data.web_paid.joined_thro + '</span></a>')
	$("#thr_ww").append('<span style="margin-left: 50px;">' + '&#8377;' + ' ' + indianCurrency.format(data.web_paid.amount) + '</span>');
	$("#thr_m").append('<a href=' + app_url + '><span class="badge bg-green">' + data.mob_paid.joined_thro + '</span></a>')
	$("#thr_mw").append('<span style="margin-left: 50px;">' + '&#8377;' + ' ' + indianCurrency.format(data.mob_paid.amount) + '</)span>');
	$("#thr_c").append('<a href=' + collection_url + '><span class="badge bg-green">' + data.collection_paid.joined_thro + '</span></a>')
	$("#thr_cw").append('<span style="margin-left: 50px;">' + '&#8377;' + ' ' + indianCurrency.format(data.collection_paid.amount) + '</span>');
}
function get_account_list(from_date = "", to_date = "", date_type = "") {
	var id_branch = $('#branch_select').val();
	var log_branch = $('#id_branch').val();
	if(log_branch != 0){
		id_branch = log_branch;
	}
	my_Date = new Date();
	// $("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_dashboard/account_status?nocache=" + my_Date.getUTCSeconds(),
		//data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch}: ''),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			console.log(data);
			set_account_list(data, from_date, to_date);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_account_list(data, from_date, to_date) {
	//alert("1");
	//23-12-2022,AB
	$('#account_list tbody').remove();
	$('#mob').empty();
	$('#mob_c').empty();
	$('#admin').empty();
	$('#admin_c').empty();
	$('#web').empty();
	$('#web_c').empty();
	$('#collection').empty();
	$('#collection_c').empty();
	var sum = 0;
	$.each(data.account_stat, function (index, element) {
		sum += Number(element.total);
		$("#all_acc").text(sum);
		//   $("#all_reg").text(sum);
		id_branch = element.id_branch;
		$.each(data.acc_wo_pay, function (index, element) {
			acc_wo_pay = element.count;
		});
		list_url = base_url + 'index.php/admin_dashboard/get_account/' + id_branch + "/" + from_date + "/" + to_date;
		$('#account_list').append(
			$('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.short_name))
				.append($('<td>').append('<a href=' + list_url + '><span class="badge bg-green">' + element.total + '</span></a>'))
				.append($('<td style="text-align: right;padding: 5px;">').append('&#8377; ' + indianCurrency.format(element.amount)))
				.append($('<td style="text-align: right;padding: 5px;">').append(formatMetalWeight(element.weight)))
		);
	});
	admin_url = base_url + 'index.php/admin_dashboard/get_account_joined/' + from_date + "/" + to_date + "/" + 1;
	web_url = base_url + 'index.php/admin_dashboard/get_account_joined/' + from_date + "/" + to_date + "/" + 0;
	app_url = base_url + 'index.php/admin_dashboard/get_account_joined/' + from_date + "/" + to_date + "/" + 2;
	collection_url = base_url + 'index.php/admin_dashboard/get_account_joined/' + from_date + "/" + to_date + "/" + 3;
	$("#mob").append('<a href=' + app_url + '><span class="badge bg-aqua">' + data.mob.joined_thro + '</span></a>')
	$("#mob_c").append('<span style="margin-left: 40px;">' + '&#8377;' + ' ' + indianCurrency.format(data.mob.amount) + '</span>');
	$("#web").append('<a href=' + web_url + '><span class="badge bg-aqua">' + data.web.joined_thro + '</span></a>')
	$("#web_c").append('<span style="margin-left: 40px;">' + '&#8377;' + ' ' + indianCurrency.format(data.web.amount) + '</span>');
	$("#admin").append('<a href=' + admin_url + '><span class="badge bg-aqua">' + data.admin.joined_thro + '</span></a>')
	$("#admin_c").append('<span style="margin-left: 40px;">' + '&#8377;' + ' ' + indianCurrency.format(data.admin.amount) + '</span>');
	$("#collection").append('<a href=' + collection_url + '><span class="badge bg-aqua">' + data.collection.joined_thro + '</span></a>')
	$("#collection_c").append('<span style="margin-left: 40px;">' + '&#8377;' + ' ' + indianCurrency.format(data.collection.amount) + '</span>');
}
function inter_wallet_list(from_date = "", to_date = "") {
	my_Date = new Date();
	// $("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_dashboard/inter_wallet_status?nocache=" + my_Date.getUTCSeconds(),
		data: { 'from_date': from_date, 'to_date': to_date },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			set_interwallet_list(data, from_date, to_date);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_interwallet_list(data, from_date, to_date) {
	$.each(data, function (index, element) {
		if (parseFloat(element.credit) > 0 || parseFloat(element.debit) > 0) {
			id_branch = element.id_branch;
			credit_url = base_url + 'index.php/admin_dashboard/inter_wallet_transcation_details/' + id_branch + '/' + from_date + "/" + to_date + "/" + 1;
			redeem_url = base_url + 'index.php/admin_dashboard/inter_wallet_transcation_details/' + id_branch + '/' + from_date + "/" + to_date + "/" + 2;
			$('#interWalList').append(
				$('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.branch_name))
					.append($('<td>').append('<a href=' + credit_url + '><span class="badge bg-purple">' + element.currency_symbol + ' ' + element.credit + '</span></a>'))
					.append($('<td>').append('<a href=' + redeem_url + '><span class="badge bg-purple">' + element.currency_symbol + ' ' + element.debit + '</span></a>'))
			);
		}
	});
	/* $.each(data.inter_wallet_redeem, function (index, element)
	{
		 id_branch=element.id_branch;
		 redeem_url=base_url+'index.php/admin_dashboard/inter_wallet_transcation_details/'+id_branch+'/'+from_date+"/"+to_date+"/"+2;
	   $('#redeem_list').append(
			 $('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.branch_name))
			 .append($('<td>').append('<a href='+redeem_url+'><span class="badge bg-purple">'+element.total+'</span></a>'))
			 );
	});*/
}
function get_collection_list(from_date = "", to_date = "") {
	my_Date = new Date();
	// $("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_dashboard/ajax_collectionData?nocache=" + my_Date.getUTCSeconds(),
		data: { 'date': from_date, 'to_date': to_date },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			set_collection_list(data, from_date, to_date);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_collection_list(data, from_date, to_date) {
	/*collection_url= base_url+'index.php/admin_dashboard/get_payment/'+id_branch+'/'+from_date+"/"+to_date;
	closed_collection =base_url+'index.php/admin_manage/ajax_close_account_list/'+id_branch+'/'+from_date+"/"+to_date;
	cancelled_list =base_url+'index.php/admin_dashboard/get_cancelled_payment/'+id_branch+'/'+from_date+"/"+to_date;
	*/
	$('#collection_list tbody').remove();
	$.each(data.pay_collection, function (index, element) {
		var closing_balance = 0;
		closing_balance = (parseFloat(element.opening_bal) + parseFloat(element.collection) - parseFloat(element.closing));
		id_branch = element.id_branch;
		/*var opening_bal = element.opening_bal.toString().split(".");
			opening_bal[0] = opening_bal[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			var opening_bal =opening_bal.join(".");*/
		//list_url=base_url+'index.php/admin_dashboard/get_payment/'+id_branch +"/"+from_date +"/"+to_date;
		$('#collection_list').append(
			$('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.branch_name))
				.append($('<td style="padding: 5px;text-align: right;">').append(element.currency_symbol + ' ' + element.opening_bal))
				.append($('<td style="padding: 5px;text-align: right;">').append(element.currency_symbol + ' ' + element.collection))
				.append($('<td style="padding: 5px;text-align: right;">').append(element.currency_symbol + ' ' + element.closing))
				.append($('<td style="padding: 5px;text-align: right;">').append(element.currency_symbol + ' ' + closing_balance))
		);
	});
}
function get_register_list() {
	my_Date = new Date();
	// $("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_dashboard/regisert_list?nocache=" + my_Date.getUTCSeconds(),
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			set_registered_list(data);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_registered_list(data) {
	registered = base_url + 'index.php/admin_dashboard/inter_wallet_accounts_detail/';
	not_registered = base_url + 'index.php/admin_dashboard/inter_wallet_accounts__woc/';
	$("#registered").append($('<td>').append('<a href=' + registered + '><span class="badge bg-aqua">' + data.inter_wallet_accounts + '</span></a>'));
	$("#not_registered").append($('<td>').append('<a href=' + not_registered + '><span class="badge bg-aqua">' + data.inter_wallet_accounts_woc + '</span></a>'));
}
// Coded by KVP for daily collection summary
// function get_collection_summary(from_date="",to_date="")
// {
// 	my_Date = new Date();
// 	$.ajax({
// 	  	  url:base_url+ "index.php/admin_dashboard/ajax_daily_collection?nocache=" + my_Date.getUTCSeconds(),
//     	 data:{'date':from_date,'to_date':to_date},
//     	 dataType:"JSON",
//     	 type:"POST",
//     	 success:function(data){
//     	     console.log(data);
//     	 	set_collection_summary(data,from_date,to_date);
//     	  },
//     	  error:function(error)
//     	  {
//     	  $("div.overlay").css("display", "none");
//     	  }
// 	  });
// }
function get_collection_summary(from_date = "", to_date = "", id_branch = "", date_type = "") {
	if (date_type == '') { date_type = $("#date_type").val(); }
	my_Date = new Date();
	$.ajax({
		url: base_url + "index.php/admin_dashboard/getsource_wiserrecord?nocache=" + my_Date.getUTCSeconds(),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			console.log(data);
			set_collection_summary(data, from_date, to_date);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
/*  function set_collection_summary(data,from_date,to_date)
{
		$('#collDetail tbody').remove();
		$('#collSummary tbody').remove();
		 var closing_total=0;
		 var op_total=0;
		 var closed_total=0;
		 var cancelled_total=0;
		 var closing_balance_total=0;
		 var op_total_amt=0;
		 var op_total_wt=0;
		 var collection_tot_amt=0;
		 var op_total_wt_scheme=0;
		 var collection_tot_wt=0;
		 var today_collection_wgt=0;
		 var closed_total_amt=0;
		 var closed_total_wt=0;
		 var closed_total_wt_scheme=0;
		 var cancelled_total_amt=0;
		 var cancelled_total_wt=0;
		 var cancelled_total_wt_scheme=0;
		 var closing_balance_total_amt=0;
		 var closing_balance_total_wt=0;
			   var   collection_total = 0 ;
		 var closing_balance_total_wt_scheme=0;
		//if(data.type == 1){
			   console.log(data.collection)
			$.each(data.collection,  function (index, element) {  // Summary
			   id_branch = element.id_branch; 
			//    from_date = element.date;
			//    to_date = element.date
			collection_url= base_url+'index.php/admin_dashboard/get_payment/'+id_branch+'/'+from_date+"/"+to_date;
			closed_collection =base_url+'index.php/admin_manage/ajax_close_account_list/'+id_branch+'/'+from_date+"/"+to_date;
			cancelled_list =base_url+'index.php/admin_dashboard/get_cancelled_payment/'+id_branch+'/'+from_date+"/"+to_date;
				 total_collection=parseFloat(element.collectionamt);
				 total_collection+=Number(total_collection);	
				 opening_total=parseFloat(element.opening_bal);
				 op_total+=Number(opening_total);
			 closed_amts=parseFloat(element.closed_amt);
				 console.log(closed_amts)
				 closed_total+=Number(closed_amts);
				//  cancelled=parseFloat(element.today_cancelled_amt)+parseFloat(element.today_cancelled_wgt);
				//  cancelled_total+=Number(cancelled);
				closing_bal=parseFloat(element.balanceamt);
				 closing_balance_total+=Number(closing_bal);
				 detailed_op=parseFloat(element.balance_amount);
				 op_total_amt+=Number(detailed_op);
				//  opening_blc_wgt=parseFloat(element.opening_blc_wgt);
				//  op_total_wt+=Number(opening_blc_wgt);
				//   opening_weight=parseFloat(element.opening_weight);
				//  op_total_wt_scheme+=Number(opening_weight);
				//   today_collection_amt=parseFloat(element.collection_amount);
				//  collection_tot_amt+=Number(today_collection_amt);
				  total_collection=parseFloat(element.collectionamt);
				 collection_total+=Number(total_collection);	
				 amtSchClosedAmt=parseFloat(element.closed_amt);
				 closed_total_amt+=Number(amtSchClosedAmt);
				  wgtSchClosedAmt=parseFloat(element.wgtSchClosedAmt);
					closed_total_wt+=Number(wgtSchClosedAmt);
				closing_balance_amt=parseFloat(element.balance_amount);
				 closing_balance_total_amt+=Number(closing_balance_amt);
				 collection_op_balance=parseFloat(element.opening_bal);
				$('#collection_total').text(get_convert(collection_total.toFixed(2)));
				$('#op_total').text(get_convert(op_total.toFixed(2)));
				$('#closed_total').text(get_convert(closed_total.toFixed(2)));
				$('#cancelled_total').text(get_convert(cancelled_total.toFixed(2)));
				$('#closing_balance_total').text(get_convert(closing_balance_total.toFixed(2)));
				$('#collection_tot_amt').text(get_convert(collection_tot_amt.toFixed(2)));
				$('#closed_total_amt').text(get_convert((closed_total_amt).toFixed(2)));
				$('#cancelled_total_amt').text(get_convert((cancelled_total_amt).toFixed(2)));
				$('#closing_balance_total_amt').text(get_convert(closing_balance_total_amt.toFixed(2)));
if (element.branch_name != 'HEAD OFFICE') {
	$('#collSummary').append(
		$('<tbody>').append(
			$('<tr>').append(
				$('<td class="branch" style="text-align: left;padding: 5px;">').text(element.branch_name),
				$('<td class="price" style="padding:5px;text-align: right;color:#605ca8 ;">').append(get_convert(parseFloat(element.opening_bal).toFixed(2))),
				$('<td style="padding: 5px;text-align: right;color:#00a65a;">').append(get_convert(parseFloat(element.collectionamt).toFixed(2))),
				$('<td style="padding: 5px;text-align: right;">').append(get_convert(parseFloat(element.closed_amt).toFixed(2))),
				$('<td style="padding: 5px;text-align: right;color:#0073b7 ;">').append(get_convert(parseFloat(element.balanceamt).toFixed(2))))
			)
		);
}
			}); 
		   $.each(data.collection,  function (index, element) {  // Detailed report 
			   from_date = element.date;
			   to_date = element.date
			   id_branch = element.id_branch; 
				amt_scheme =base_url+'index.php/admin_dashboard/get_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'0';
				wt_scheme =base_url+'index.php/admin_dashboard/get_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'1';
				wt_scheme_closed =base_url+'index.php/admin_manage/ajax_close_account_list/'+id_branch+'/'+from_date+"/"+to_date+"/"+'1';
				amt_scheme_closed =base_url+'index.php/admin_manage/ajax_close_account_list/'+id_branch+'/'+from_date+"/"+to_date+"/"+'0';
				amt_scheme_canceled =base_url+'index.php/admin_dashboard/get_cancelled_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'0';
				wt_scheme_canceled =base_url+'index.php/admin_dashboard/get_cancelled_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'1';
				if(element.branch_name != 'HEAD OFFICE'){
					$('#collDetail').append(
					$('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.branch_name))
									.append($('<td style="padding: 5px;text-align: right;color:#605ca8 ;">').
							append(get_convert(element.paid_amount)))
		//               		.append($('<td style="padding: 5px;text-align: right;color:#605ca8 ;">').append
					// 		(get_convert(element.opening_blc_wgt)+'<br/>'+element.opening_weight+' g'))
									.append($('<td style="padding: 5px;text-align: right;">').append('<a style="color:#00a65a;text-decoration:none !important;" href='+amt_scheme+'>'
							 +(get_convert(parseFloat(element.today_collection_amt).toFixed(2)))+'</a><br/>&nbsp;'))
									.append($('<td style="padding: 5px;text-align: right;color:#00a65a ;">').append('<a style="color:#00a65a;text-decoration:none !important;" href='+wt_scheme+'>'+(
							get_convert(parseFloat(element.today_collection_wgt).toFixed(2)))+'<br/>'+
							element.today_weight+'</a> g'))
									.append($('<td style="padding: 5px;text-align: right;color:#ff851b;">').append('<a style="color:#ff851b;text-decoration:none !important;" href='+amt_scheme_closed+'>'+
							(get_convert(parseFloat(element.amtSchClosedAmt).toFixed(2)))+'</a><br/>&nbsp;'))
							.append($('<td style="padding: 5px;text-align: right;color:#ff851b;">').append('<a style="color:#ff851b;text-decoration:none !important;" href='+wt_scheme_closed+'>'+
							(get_convert(parseFloat(element.wgtSchClosedAmt).toFixed(2)))+'<br/>'+element.wgtSchClosedWgt+'</a> g'))
									.append($('<td style="padding: 5px;text-align: right;color:#0073b7;">').append(
							get_convert(element.closing_balance_amt)+'<br/>&nbsp;'))
					); 
				}
			}); 
		}  */
// new esakki 28-09
function set_collection_summary(data, from_date, to_date) {
	$('#collDetail tbody').remove();
	$('#collSummary tbody').remove();

	// Handle branch vs non-branch display
	var isBranchMode = (data.branch_settings == 1);

	var closing_total = 0;
	var op_total = 0;
	var closed_total = 0;
	var cancelled_total = 0;
	var closing_balance_total = 0;
	var op_total_amt = 0; var op_total_wt = 0; var collection_tot_amt = 0;
	var op_total_wt_scheme = 0; var collection_tot_wt = 0; var today_collection_wgt = 0; var closed_total_amt = 0;
	var closed_total_wt = 0; var closed_total_wt_scheme = 0; var cancelled_total_amt = 0; var cancelled_total_wt = 0;
	var cancelled_total_wt_scheme = 0;
	var closing_balance_total_amt = 0;
	var closing_balance_total_wt = 0;
	var collection_total = 0;
	var closing_balance_total_wt_scheme = 0;
	//if(data.type == 1){
	console.log(data.collection)
	$.each(data.collection, function (index, element) {  // Summary
		id_branch = element.id_branch;
		//    from_date = element.date;
		//    to_date = element.date
		collection_url = base_url + 'index.php/admin_dashboard/get_payment/' + id_branch + '/' + from_date + "/" + to_date;
		closed_collection = base_url + 'index.php/admin_manage/ajax_close_account_list/' + id_branch + '/' + from_date + "/" + to_date;
		cancelled_list = base_url + 'index.php/admin_dashboard/get_cancelled_payment/' + id_branch + '/' + from_date + "/" + to_date;
		total_collection = parseFloat(element.collectionamt);
		total_collection += Number(total_collection);
		opening_total = parseFloat(element.opening_bal);
		console.log("opening", opening_total);
		op_total += Number(opening_total);
		closed_amts = parseFloat(element.closed_amt);
		console.log(closed_amts)
		closed_total += Number(closed_amts);
		//  cancelled=parseFloat(element.today_cancelled_amt)+parseFloat(element.today_cancelled_wgt);
		//  cancelled_total+=Number(cancelled);
		closing_bal = parseFloat(element.balanceamt);
		closing_balance_total += Number(closing_bal);
		detailed_op = parseFloat(element.balance_amount);
		op_total_amt += Number(detailed_op);
		//  opening_blc_wgt=parseFloat(element.opening_blc_wgt);
		//  op_total_wt+=Number(opening_blc_wgt);
		//   opening_weight=parseFloat(element.opening_weight);
		//  op_total_wt_scheme+=Number(opening_weight);
		//   today_collection_amt=parseFloat(element.collection_amount);
		//  collection_tot_amt+=Number(today_collection_amt);
		total_collection = parseFloat(element.collectionamt);
		collection_total += Number(total_collection);
		amtSchClosedAmt = parseFloat(element.closed_amt);
		closed_total_amt += Number(amtSchClosedAmt);
		wgtSchClosedAmt = parseFloat(element.wgtSchClosedAmt);
		closed_total_wt += Number(wgtSchClosedAmt);
		closing_balance_amt = parseFloat(element.balance_amount);
		closing_balance_total_amt += Number(closing_balance_amt);
		collection_op_balance = parseFloat(element.opening_bal);
		$('#collection_total').text(get_convert(collection_total.toFixed(2)));
		$('#op_total').text(get_convert(op_total.toFixed(2)));
		$('#closed_total').text(get_convert(closed_total.toFixed(2)));
		$('#cancelled_total').text(get_convert(cancelled_total.toFixed(2)));
		$('#closing_balance_total').text(get_convert(closing_balance_total.toFixed(2)));
		$('#collection_tot_amt').text(get_convert(collection_tot_amt.toFixed(2)));
		$('#closed_total_amt').text(get_convert((closed_total_amt).toFixed(2)));
		$('#cancelled_total_amt').text(get_convert((cancelled_total_amt).toFixed(2)));
		$('#closing_balance_total_amt').text(get_convert(closing_balance_total_amt.toFixed(2)));
		// if (element.branch_name != 'HEAD OFFICE') {
		var $row = $('<tr>');
		if (isBranchMode) {
			$row.append($('<td class="branch" style="text-align: left;padding: 5px;">').text(element.branch_name));
		}
		$row.append(
				$('<td class="price" style="padding:5px;text-align: right;color:#605ca8 ;">').append(get_convert(parseFloat(element.opening_bal).toFixed(2))),
				$('<td style="padding: 5px;text-align: right;color:#00a65a;">').append(get_convert(parseFloat(element.collectionamt).toFixed(2))),
				$('<td style="padding: 5px;text-align: right;">').append(get_convert(parseFloat(element.closed_amt).toFixed(2))),
				$('<td style="padding: 5px;text-align: right;color:#0073b7 ;">').append(get_convert(parseFloat(element.balanceamt).toFixed(2))));
		$('#collSummary').append(
			$('<tbody>').append($row)
		);
		// }
	});

	$.each(data.collection, function (index, element) {  // Detailed report 
		from_date = element.date;
		to_date = element.date
		id_branch = element.id_branch;
		amt_scheme = base_url + 'index.php/admin_dashboard/get_payment/' + id_branch + '/' + from_date + "/" + to_date + "/" + '0';
		wt_scheme = base_url + 'index.php/admin_dashboard/get_payment/' + id_branch + '/' + from_date + "/" + to_date + "/" + '1';
		wt_scheme_closed = base_url + 'index.php/admin_manage/ajax_close_account_list/' + id_branch + '/' + from_date + "/" + to_date + "/" + '1';
		amt_scheme_closed = base_url + 'index.php/admin_manage/ajax_close_account_list/' + id_branch + '/' + from_date + "/" + to_date + "/" + '0';
		amt_scheme_canceled = base_url + 'index.php/admin_dashboard/get_cancelled_payment/' + id_branch + '/' + from_date + "/" + to_date + "/" + '0';
		wt_scheme_canceled = base_url + 'index.php/admin_dashboard/get_cancelled_payment/' + id_branch + '/' + from_date + "/" + to_date + "/" + '1';
		// if(element.branch_name != 'HEAD OFFICE'){
		$('#collDetail').append(
			$('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.branch_name))
				.append($('<td style="padding: 5px;text-align: right;color:#605ca8 ;">').
					append(get_convert(element.paid_amount)))
				//               		.append($('<td style="padding: 5px;text-align: right;color:#605ca8 ;">').append
				// 		(get_convert(element.opening_blc_wgt)+'<br/>'+element.opening_weight+' g'))
				.append($('<td style="padding: 5px;text-align: right;">').append('<a style="color:#00a65a;text-decoration:none !important;" href=' + amt_scheme + '>'
					+ (get_convert(parseFloat(element.today_collection_amt).toFixed(2))) + '</a><br/>&nbsp;'))
				.append($('<td style="padding: 5px;text-align: right;color:#00a65a ;">').append('<a style="color:#00a65a;text-decoration:none !important;" href=' + wt_scheme + '>' + (
					get_convert(parseFloat(element.today_collection_wgt).toFixed(2))) + '<br/>' +
					element.today_weight + '</a> g'))
				.append($('<td style="padding: 5px;text-align: right;color:#ff851b;">').append('<a style="color:#ff851b;text-decoration:none !important;" href=' + amt_scheme_closed + '>' +
					(get_convert(parseFloat(element.amtSchClosedAmt).toFixed(2))) + '</a><br/>&nbsp;'))
				.append($('<td style="padding: 5px;text-align: right;color:#ff851b;">').append('<a style="color:#ff851b;text-decoration:none !important;" href=' + wt_scheme_closed + '>' +
					(get_convert(parseFloat(element.wgtSchClosedAmt).toFixed(2))) + '<br/>' + element.wgtSchClosedWgt + '</a> g'))
				.append($('<td style="padding: 5px;text-align: right;color:#0073b7;">').append(
					get_convert(element.closing_balance_amt) + '<br/>&nbsp;'))
		);
		// }
	});
}
/* function set_collection_summary(data,from_date,to_date)
{
		$('#collDetail tbody').remove();
		$('#collSummary tbody').remove();
		 var closing_total=0;
		 var op_total=0;
		 var closed_total=0;
		 var cancelled_total=0;
		 var closing_balance_total=0;
		 var op_total_amt=0;var op_total_wt=0;var collection_tot_amt=0;
		 var op_total_wt_scheme=0;var collection_tot_wt=0;var today_collection_wgt=0;var closed_total_amt=0;
		 var closed_total_wt=0;var closed_total_wt_scheme=0;var cancelled_total_amt=0;var cancelled_total_wt=0;
		 var cancelled_total_wt_scheme=0;
		 var closing_balance_total_amt=0;
		 var closing_balance_total_wt=0;
		 var closing_balance_total_wt_scheme=0;
		//if(data.type == 1){
			$.each(data.collection,  function (index, element) {  // Summary
			   id_branch = element.id_branch;
			   from_date = element.date;
			   to_date = element.date
			collection_url= base_url+'index.php/admin_dashboard/get_payment/'+id_branch+'/'+from_date+"/"+to_date;
			closed_collection =base_url+'index.php/admin_manage/ajax_close_account_list/'+id_branch+'/'+from_date+"/"+to_date;
			cancelled_list =base_url+'index.php/admin_dashboard/get_cancelled_payment/'+id_branch+'/'+from_date+"/"+to_date;
				 total_collection=parseFloat(element.today_collection_amt)+parseFloat(element.today_collection_wgt);
				 closing_total+=Number(total_collection);
				 opening_total=parseFloat(element.opening_blc_amt)+parseFloat(element.opening_blc_wgt);
				 op_total+=Number(opening_total);
				 closed=parseFloat(element.amtSchClosedAmt)+parseFloat(element.wgtSchClosedAmt);
				 closed_total+=Number(closed);
				 cancelled=parseFloat(element.today_cancelled_amt)+parseFloat(element.today_cancelled_wgt);
				 cancelled_total+=Number(cancelled);
				closing_bal=parseFloat(element.closing_balance_amt)+parseFloat(element.closing_balance_wgt);
				 closing_balance_total+=Number(closing_bal);
				 detailed_op=parseFloat(element.opening_blc_amt);
				 op_total_amt+=Number(detailed_op);
				 opening_blc_wgt=parseFloat(element.opening_blc_wgt);
				 op_total_wt+=Number(opening_blc_wgt);
				  opening_weight=parseFloat(element.opening_weight);
				 op_total_wt_scheme+=Number(opening_weight);
				  today_collection_amt=parseFloat(element.today_collection_amt);
				 collection_tot_amt+=Number(today_collection_amt);
				  today_collection=parseFloat(element.today_collection_wgt);
				 today_collection_wgt+=Number(today_collection);
				today_weight=parseFloat(element.today_weight);
				 collection_tot_wt+=Number(today_weight);
				 amtSchClosedAmt=parseFloat(element.amtSchClosedAmt);
				 closed_total_amt+=Number(amtSchClosedAmt);
				  wgtSchClosedAmt=parseFloat(element.wgtSchClosedAmt);
					closed_total_wt+=Number(wgtSchClosedAmt);
				 wgtSchClosedWgt=parseFloat(element.wgtSchClosedWgt);
				 closed_total_wt_scheme+=Number(wgtSchClosedWgt);
				 today_cancelled_amt=parseFloat(element.today_cancelled_amt);
				 cancelled_total_amt+=Number(today_cancelled_amt);
				  today_cancelled_wgt=parseFloat(element.today_cancelled_wgt);
				 cancelled_total_wt+=Number(today_cancelled_wgt);
				 weight_cancelled=parseFloat(element.weight_cancelled);
				 cancelled_total_wt_scheme+=Number(weight_cancelled);
				closing_balance_amt=parseFloat(element.closing_balance_amt);
				 closing_balance_total_amt+=Number(closing_balance_amt);
				  closing_balance_wgt=parseFloat(element.closing_balance_wgt);
				 closing_balance_total_wt+=Number(closing_balance_wgt);
				 closing_weight=parseFloat(element.closing_weight);
				 closing_balance_total_wt_scheme+=Number(closing_weight);
				 collection_op_balance=((parseFloat(element.opening_blc_amt)+parseFloat(element.opening_blc_wgt)).toFixed(2));
				$('#closing_total').text(get_convert((closing_total).toFixed(2)));
				$('#op_total').text(get_convert(op_total.toFixed(2)));
				$('#closed_total').text(get_convert(closed_total.toFixed(2)));
				$('#cancelled_total').text(get_convert(cancelled_total.toFixed(2)));
				$('#closing_balance_total').text(get_convert(closing_balance_total.toFixed(2)));
				$('#op_total_amt').text(get_convert(op_total_amt.toFixed(2)));
				$('#op_total_wt').text(get_convert(op_total_wt.toFixed(2)));
				$('#op_total_wt_scheme').text((op_total_wt_scheme).toFixed(3)+'g');
				$('#collection_tot_amt').text(get_convert(collection_tot_amt.toFixed(2)));
				$('#today_collection_wgt').text(get_convert((today_collection_wgt).toFixed(2)));
				$('#collection_tot_wt').text((collection_tot_wt).toFixed(3)+'g');
				$('#closed_total_amt').text(get_convert((closed_total_amt).toFixed(2)));
				$('#closed_total_wt').text(get_convert(closed_total_wt.toFixed(2)));
				$('#closed_total_wt_scheme').text((closed_total_wt_scheme).toFixed(3)+'g');
				$('#cancelled_total_amt').text(get_convert((cancelled_total_amt).toFixed(2)));
				$('#cancelled_total_wt').text((cancelled_total_wt).toFixed(2));
				$('#cancelled_total_wt_scheme').text((cancelled_total_wt_scheme).toFixed(3)+'g');
				$('#closing_balance_total_amt').text(get_convert(closing_balance_total_amt.toFixed(2)));
				$('#closing_balance_total_wt').text(get_convert((closing_balance_total_wt).toFixed(2)));
				$('#closing_balance_total_wt_scheme').text((closing_balance_total_wt_scheme).toFixed(3)+'g');
				if(element.branch_name != 'HEAD OFFICE'){
					$('#collSummary').append(
					$('<tbody><tr>').append($('<td class="branch" style="text-align: left;padding: 5px;">').append(element.branch_name))
									.append($('<td class="price" style="padding:5px;text-align: right;color:#605ca8 ;">').append(get_convert(collection_op_balance)))
							.append($('<td style="padding: 5px;text-align: right;color:#00a65a ;text-decoration:none !important;">').append('<a style="text-decoration:none !important"; href='+collection_url+'>'
							+get_convert((total_collection.toFixed(2)))+'</a>'))
									.append($('<td style="padding: 5px;text-align: right;">').append('<a style="text-decoration:none !important"; href='+closed_collection+'>'+get_convert(((parseFloat(element.amtSchClosedAmt)+parseFloat(element.wgtSchClosedAmt)).toFixed(2)))+'</a>'))
									.append($('<td style="padding: 5px;text-align: right;">').append('<a style="text-decoration:none !important"; href='+cancelled_list+'>'+get_convert(((parseFloat(element.today_cancelled_amt)+parseFloat(element.today_cancelled_wgt)).toFixed(2)))+'</a>'))
									.append($('<td style="padding: 5px;text-align: right;color:#0073b7 ;">').append(
							get_convert((parseFloat(element.closing_balance_amt)+parseFloat(element.closing_balance_wgt)).toFixed(2))))
					);
				}
			});
		   $.each(data.collection,  function (index, element) {  // Detailed report
			   from_date = element.date;
			   to_date = element.date
			   id_branch = element.id_branch;
				amt_scheme =base_url+'index.php/admin_dashboard/get_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'0';
				wt_scheme =base_url+'index.php/admin_dashboard/get_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'1';
				wt_scheme_closed =base_url+'index.php/admin_manage/ajax_close_account_list/'+id_branch+'/'+from_date+"/"+to_date+"/"+'1';
				amt_scheme_closed =base_url+'index.php/admin_manage/ajax_close_account_list/'+id_branch+'/'+from_date+"/"+to_date+"/"+'0';
				amt_scheme_canceled =base_url+'index.php/admin_dashboard/get_cancelled_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'0';
				wt_scheme_canceled =base_url+'index.php/admin_dashboard/get_cancelled_payment/'+id_branch+'/'+from_date+"/"+to_date+"/"+'1';
				if(element.branch_name != 'HEAD OFFICE'){
					$('#collDetail').append(
					$('<tbody><tr>').append($('<td style="text-align: left;padding: 5px;">').append(element.branch_name))
									.append($('<td style="padding: 5px;text-align: right;color:#605ca8 ;">').
							append(get_convert(element.opening_blc_amt)))
									.append($('<td style="padding: 5px;text-align: right;color:#605ca8 ;">').append
							(get_convert(element.opening_blc_wgt)+'<br/>'+element.opening_weight+' g'))
									.append($('<td style="padding: 5px;text-align: right;">').append('<a style="color:#00a65a;text-decoration:none !important;" href='+amt_scheme+'>'
							 +(get_convert(parseFloat(element.today_collection_amt).toFixed(2)))+'</a><br/>&nbsp;'))
									.append($('<td style="padding: 5px;text-align: right;color:#00a65a ;">').append('<a style="color:#00a65a;text-decoration:none !important;" href='+wt_scheme+'>'+(
							get_convert(parseFloat(element.today_collection_wgt).toFixed(2)))+'<br/>'+
							element.today_weight+'</a> g'))
									.append($('<td style="padding: 5px;text-align: right;color:#ff851b;">').append('<a style="color:#ff851b;text-decoration:none !important;" href='+amt_scheme_closed+'>'+
							(get_convert(parseFloat(element.amtSchClosedAmt).toFixed(2)))+'</a><br/>&nbsp;'))
							.append($('<td style="padding: 5px;text-align: right;color:#ff851b;">').append('<a style="color:#ff851b;text-decoration:none !important;" href='+wt_scheme_closed+'>'+
							(get_convert(parseFloat(element.wgtSchClosedAmt).toFixed(2)))+'<br/>'+element.wgtSchClosedWgt+'</a> g'))
									.append($('<td style="padding: 5px;text-align: right;color:#dd4b39;">').append('<a style="color:#dd4b39;text-decoration:none !important;" href='+amt_scheme_canceled+'>'+
							(get_convert(parseFloat(element.today_cancelled_amt).toFixed(2)))+'</a><br/>&nbsp;'))
									.append($('<td style="padding: 5px;text-align: right;color:#dd4b39;">').append('<a style="color:#dd4b39;text-decoration:none !important;" href='+wt_scheme_canceled+'>'+
							(get_convert(parseFloat(element.today_cancelled_wgt).toFixed(2)))+'<br/>'+element.weight_cancelled+'</a> g'))
									.append($('<td style="padding: 5px;text-align: right;color:#0073b7;">').append(
							get_convert(element.closing_balance_amt)+'<br/>&nbsp;'))
									.append($('<td style="padding: 5px;text-align: right;color:#0073b7;">').append(
							get_convert(element.closing_balance_wgt)+'<br/>'+element.closing_weight+' g'))
					);
				}
			});
		//}
} */
function get_convert(num) {
	var str = num;
	var format = /[.]/;
	var x = num;
	if (format.test && (typeof str != 'number')) {
		var number = str.split(".");
		var x = number[0];
	}
	if (x.length >= 4) {
		x = x.toString();
		var lastThree = x.substring(x.length - 3);
		var otherNumbers = x.substring(0, x.length - 3);
		if (otherNumbers != '') {
			lastThree = ',' + lastThree;
			var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
			var total = res.concat('.', number[1]);
			return total;
		}
		else {
			return '-';
		}
	} else {
		return num == 0 ? '-' : num;
	}
}
// Coded by KVP ends for daily collection summary
//Customer Order Management
function get_order_management_details() {
	let from_date = $('#payment_list1').text();
	let to_date = $('#payment_list2').text();
	get_customer_order_details(from_date, to_date);
}
function get_customer_order_details(from_date, to_date) {
	$("#customer_order_table tbody").empty();
	$("#cus_order_details tbody").empty();
	$("div.overlay").css("display", "block");
	my_Date = new Date();
	$.ajax({
		url: base_url + "index.php/admin_ret_dashboard/get_customer_order_details?nocache=" + my_Date.getUTCSeconds(),
		dataType: "JSON",
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': $('#id_branch').val() },
		type: "POST",
		cache: false,
		success: function (data) {
			var table_value = '';
			var cus_details = '';
			var total_pcs = 0;
			var total_wt = 0;
			$.each(data.cus_orders_details, function (key, items) {
				total_pcs += parseFloat(items.totalitems);
				total_wt += parseFloat(items.weight);
				cus_details += '<tr>' +
					'<td>' + items.branch_name + '</td>' +
					'<td>' + items.cus_name + '</td>' +
					'<td>' + items.product_name + '</td>' +
					'<td>' + (items.weight + '/' + items.totalitems) + '</td>' +
					'<td>' + items.order_status + '</td>' +
					'</tr>'
			});
			$('.total_pcs').html(parseFloat(total_wt).toFixed(3) + '/' + total_pcs);
			$("#cus_order_details tbody ").append(cus_details);
			$.each(data.cus_orders, function (bwd_key, bwd_element) {
				var pending_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=0&filter_type=1&id_branch=' + bwd_element.order_from;
				var order_placed_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=1&id_branch=' + bwd_element.order_from;
				var karigar_pending_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=0&filter_type=4&id_branch=' + bwd_element.order_from;
				var karigar_delivered_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=4&filter_type=7&id_branch=' + bwd_element.order_from;
				var karigar_over_due_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=6&id_branch=' + bwd_element.order_from;
				var customer_pending_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=3&id_branch=' + bwd_element.order_from;
				var customer_delivery_ready_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=4&filter_type=7&id_branch=' + bwd_element.order_from;
				var customer_delivered_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=5&filter_type=2&id_branch=' + bwd_element.order_from;
				var customer_over_due_url = base_url + 'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=5&id_branch=' + bwd_element.order_from;
				table_value += '<tr>' +
					'<td>' + bwd_element.branch_name + '</td>' +
					'<td class="numbers"><a href=' + pending_url + ' target="_blank">' + (bwd_element.allocation_pending_wt + '/' + bwd_element.allocation_pending_pcs) + '</td>' +
					'<td class="numbers"><a href=' + order_placed_url + ' target="_blank">' + (bwd_element.allocation_done_wt + '/' + bwd_element.allocation_done_pcs) + '</td>' +
					'<td class="numbers"><a href=' + karigar_pending_url + ' target="_blank">' + (bwd_element.karigar_pending_wt + '/' + bwd_element.karigar_pending_pcs) + '</a></td>' +
					'<td class="numbers"><a href=' + karigar_delivered_url + ' target="_blank">' + (bwd_element.karigar_delivered_wt + '/' + bwd_element.karigar_delivered_pcs) + '</a></td>' +
					'<td class="numbers"><a href=' + karigar_over_due_url + ' target="_blank" ' + (bwd_element.karigar_over_due_pcs == 0 ? 'style="text-color:red;"' : '') + ' >' + (bwd_element.karigar_over_due_wt + '/' + bwd_element.karigar_over_due_pcs) + '</a></td>' +
					'<td class="numbers"><a href=' + customer_pending_url + ' target="_blank">' + (bwd_element.cus_pending_wt + '/' + bwd_element.cus_pending_pcs) + '</a></td>' +
					'<td class="numbers"><a href=' + customer_delivery_ready_url + ' target="_blank" >' + (bwd_element.cus_delivery_ready_wt + '/' + bwd_element.cus_delivery_ready_pcs) + '</a></td>' +
					'<td class="numbers"><a href=' + customer_delivered_url + ' target="_blank">' + (bwd_element.cus_delivered_wt + '/' + bwd_element.cus_delivered_pcs) + '</a></td>' +
					'<td class="numbers"><a href=' + customer_over_due_url + ' target="_blank" ' + (bwd_element.cus_over_due_pcs > 0 ? 'style="color:red;"' : '') + '>' + (bwd_element.cus_over_due_wt + '/' + bwd_element.cus_over_due_pcs) + '</a></td>' +
					'</tr>';
			});
			$("#customer_order_table tbody").append(table_value);
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
//Customer Order Management
function set_scheme_account_list(data) {
	var accounts = data.accounts;
	console.log(accounts.length);
	var oTable = $('#sch_acc_list').dataTable();
	if (accounts != null && accounts.length > 0) {
		oTable = $('#sch_acc_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true,
			"bSort": true,
			"dom": 'lBfrtip',
			"buttons": ['excel', 'print'],
			"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
			"aaData": accounts,
			"order": [[0, "desc"]],
			"aoColumns": [
				{ "mDataProp": "id_scheme_account" },
				{
					"mDataProp": function (row, type, val, meta) {
						if (row.has_lucky_draw == 1) {
							return row.group_code + ' ' + row.scheme_acc_number;
						} else {
							return row.code + ' ' + row.scheme_acc_number;
						}
					}
				},
				{ "mDataProp": "name" },
				{ "mDataProp": "mobile" },
				{ "mDataProp": "code" },
				{ "mDataProp": "scheme_type" },
				{ "mDataProp": "start_date" },
				{ "mDataProp": "added_by" },
				{
					"mDataProp": function (row, type, val, meta) {
						if (row.emp_name != null) {
							return row.emp_name + ' / ' + row.emp_code;
						} else {
							return '-';
						}
					}
				},
				{ "mDataProp": "payment_amount" },
				{ "mDataProp": "total_installments" },
				{
					"mDataProp": function (row, type, val, meta) {
						if (row.scheme_type == 1) {
							return 'max' + row.max_weight + 'g/month';
						} else {
							return row.amount;
						}
					}
				}
			],
			"footerCallback": function (row, data, start, end, display) {
				if (data.length > 0) {
					var api = this.api(), data;
					var intVal = function (i) {
						return typeof i === 'string' ?
							i.replace(/[\$,]/g, '') * 1 :
							typeof i === 'number' ? i :
								0;
					};
					// Amount Total over this page
					amttotal = api
						.column(9, { page: 'current' })
						.data()
						.reduce(function (a, b) {
							return intVal(a) + intVal(b);
						}, 0);
					$(api.column(9).footer()).html(parseFloat(amttotal).toFixed(2));
				}
			}
		});
	}
}
function set_scheme_payment_list(data) {
	console.log(data);
	var payments = data.payments;
	var oTable = $('#sch_payment_list').dataTable();
	if (payments != null && payments.length > 0) {
		oTable = $('#sch_payment_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true,
			"bSort": true,
			"dom": 'lBfrtip',
			"buttons": ['excel', 'print'],
			"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
			"aaData": payments,
			"order": [[0, "desc"]],
			"aoColumns": [
				{ "mDataProp": "id_payment" },
				{ "mDataProp": "name" },
				{ "mDataProp": "mobile" },
				{ "mDataProp": "ref_no" },
				{ "mDataProp": "code" },
				{
					"mDataProp": function (row, type, val, meta) {
						if (row.has_lucky_draw == 1) {
							return row.group_code + ' ' + row.scheme_acc_number;
						} else {
							return row.code + ' ' + row.scheme_acc_number;
						}
					}
				},
				{ "mDataProp": "scheme_type" },
				{ "mDataProp": "date_payment" },
				{ "mDataProp": "id_transaction" },
				{ "mDataProp": "payment_mode" },
				{ "mDataProp": "payment_amount" },
				{
					"mDataProp": function (row, type, val, meta) {
						return '<span class= "badge bg-' + row.color + '">' + row.payment_status + '</span>';
					}
				}
			],
			"footerCallback": function (row, data, start, end, display) {
				if (data.length > 0) {
					var api = this.api(), data;
					var intVal = function (i) {
						return typeof i === 'string' ?
							i.replace(/[\$,]/g, '') * 1 :
							typeof i === 'number' ? i :
								0;
					};
					// Amount Total over this page
					amttotal = api
						.column(10, { page: 'current' })
						.data()
						.reduce(function (a, b) {
							return intVal(a) + intVal(b);
						}, 0);
					$(api.column(10).footer()).html(parseFloat(amttotal).toFixed(2));
				}
			}
		});
	}
}
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
				console.log(item.id_employee);
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
function get_customer_details_data() {
	//alert(f_date);
	// window.location.href= base_url+'index.php/admin_dashboard/customer_detail_view';
	get_customer_data(f_date, t_date);
}
function get_customer_data(f_date, t_date) {
	alert(f_date);
	$.ajax({
		type: 'POST',
		url: base_url + 'index.php/admin_dashboard/customer_detail_bydate',
		//url: base_url+'index.php/reports/detail/registration/all',
		dataType: 'json',
		async: false,
		data: { 'from_date': f_date, 'to_date': t_date },
		success: function (data) {
			console.log("data " + data);
			alert(data);
			//set_customer_table(data);
			//window.location.href= base_url+'index.php/reports/detail/registration/all';
			//window.location.href= base_url+'index.php/admin_dashboard/customer_detail_view';
			set_customer_table(data);
		}
	});
}
function set_customer_table(data) {
	window.location.href = base_url + 'index.php/admin_dashboard/customer_detail_view';
	var customer_data = data.customer;
	console.log(customer_data);
	//alert();
	var oTable = $('#customer_data_list').dataTable();
	if (customer_data != null && customer_data.length > 0) {
		oTable = $('#customer_data_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true,
			"bSort": true,
			"dom": 'lBfrtip',
			"buttons": ['excel', 'print'],
			"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },
			"aaData": accounts,
			"order": [[0, "desc"]],
			"aoColumns": [
				{ "mDataProp": "name" },
				{ "mDataProp": "name" },
				{ "mDataProp": "mobile" },
				{ "mDataProp": "date_add" },
				{ "mDataProp": "added_by" },
				{ "mDataProp": "profile_complete" },
				{ "mDataProp": "active" },
			]
		});
	}
}
function set_collection_app_list(data) {
	console.log(data);
	//alert();
	var accounts = data.accounts;
	var oTable = $('#collection_app_list').DataTable();
	oTable.clear().draw();
	if (accounts != null && accounts.length > 0) {
		oTable = $('#collection_app_list').DataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true,
			"bSort": true,
			"aaData": accounts,
			"aoColumns": [
				{ "mDataProp": "sno" },
				{ "mDataProp": "name" },
				{ "mDataProp": "mobile" },
				{ "mDataProp": "ref_no" },
				{ "mDataProp": "code" },
				{ "mDataProp": "scheme_acc_number" },
				{ "mDataProp": "scheme_type" },
				{ "mDataProp": "date_payment" },
				{ "mDataProp": "id_transaction" },
				{ "mDataProp": "payment_mode" },
				{ "mDataProp": "payment_status" },
				{ "mDataProp": "payment_amount" },
			],
			"footerCallback": function (row, data, start, end, display) {
				if (data.length > 0) {
					var api = this.api(), data;
					for (var i = 0; i <= data.length - 1; i++) {
						var intVal = function (i) {
							return typeof i === 'string' ?
								i.replace(/[\$,]/g, '') * 1 :
								typeof i === 'number' ?
									i : 0;
						};
						// paid Total
						paid = api
							.column(11, { page: 'current' })
							.data()
							.reduce(function (a, b) {
								return intVal(a) + intVal(b);
							}, 0);
						$(api.column(11).footer()).html(parseFloat(paid));
					}
				}
				else {
					var data = 0;
					var api = this.api(), data;
					$(api.column(11).footer()).html('');
				}
			}
		});
	}
}
//employee wise collection ends
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
// 	payment summary end
function indianCurrencyFormat(amount) {
	return '₹' + (+amount).toLocaleString('en-IN');
}
//Scheme wise join ---start
function schWise_accounts(from_date, to_date, date_type = "") {
	$('#schWise_account_list > tbody').empty();
	$('#schWise_account_list > tfoot').empty();
	var id_branch = $('#branch_select').val();
	var log_branch = $('#id_branch').val();
	if(log_branch != 0){
		id_branch = log_branch;
	}
	my_Date = new Date();
	$.ajax({
		url: base_url + "index.php/admin_dashboard/schWise_accounts_list?nocache=" + my_Date.getUTCSeconds(),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			list_url = '';
			console.log(data);
			let grandTotal = 0;
			let grandWeight = 0;
			$.each(data, function (index, element) {
				let amount = parseFloat(element.amount);
				// Extract numeric part of weight and convert to float for calculation
				let weight = parseFloat(element.weight.match(/[\d.]+/)[0]);
				// list_url=base_url+'index.php/admin_dashboard/get_account/'+id_branch +"/"+from_date +"/"+to_date;
				$('#schWise_account_list').append(
					$('<tbody><tr>').append($('<td style="text-align: left;">').append(element.code))
						.append($('<td>').append('<span class="badge bg-green">' + element.total + '</span>'))
						.append($('<td style="text-align: right;;">').append('&#8377; ' + indianCurrency.format(element.amount)))
						.append($('<td style="text-align: right;">').append(formatMetalWeight(element.weight)))
				);
				// Update grand total and grand weight
				grandTotal += amount;
				grandWeight += weight;
			});
			// Add footer row to the table
			$('#schWise_account_list').append(
				$('<tfoot>').append(
					$('<tr style="color:red">').append(
						$('<td colspan="2" style="text-align: left;">').append('Grand Total:'),
						$('<td style="text-align: right;padding: 5px;">').append('&#8377; ' + indianCurrency.format(grandTotal)),
						$('<td style="text-align: right;">').append(formatMetalWeight(grandWeight) + ' g')
					)
				)
			);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
// filter Date wise cus reg in dashboard//hh
function get_customer_list(from_date = "", to_date = "", date_type = "") {
	var id_branch = $('#branch_select').val();
	//alert("1");
	my_Date = new Date();
	// $("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_dashboard/customer_status?nocache=" + my_Date.getUTCSeconds(),
		//data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch}: ''),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			set_customer_list(data, from_date, to_date, id_branch, date_type);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_customer_list(data, from_date, to_date, id_branch, date_type) {
	$('#cus_web').empty(); //date wise filter cus reg  by hh//
	$('#cus_mob').empty();
	$('#cus_admin').empty();
	$('#cus_collection').empty();
	var sum = 0;
	var dt_param = (date_type != '' && date_type != undefined && date_type != null ? '?date_type=' + date_type : '');
	if (id_branch != '' && id_branch != 0 && id_branch != 'undefined' && id_branch != null) {
		admin_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 1 + "/" + id_branch + dt_param;
		web_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 0 + "/" + id_branch + dt_param;
		app_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 2 + "/" + id_branch + dt_param;
		collection_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 3 + "/" + id_branch + dt_param;
	}
	else {
		admin_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 1 + "/0" + dt_param;
		web_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 0 + "/0" + dt_param;
		app_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 2 + "/0" + dt_param;
		collection_url = base_url + 'index.php/admin_dashboard/get_customer_joined/' + from_date + "/" + to_date + "/" + 3 + "/0" + dt_param;
	}
	console.log(data.admin.joined_thro);
	$("#cus_mob").append('<a href=' + app_url + '><span class="badge bg-aqua">' + data.mob.joined_thro + '</span></a>');
	$("#cus_web").append('<a href=' + web_url + '><span class="badge bg-aqua">' + data.web.joined_thro + '</span></a>');
	$("#cus_admin").append('<a href=' + admin_url + '><span class="badge bg-aqua">' + data.admin.joined_thro + '</span></a>');
	$("#cus_collection").append('<a href=' + collection_url + '><span class="badge bg-aqua">' + data.collection.joined_thro + '</span></a>');
}
// filter Date wise cus reg in dashboard//hh
//employee wise collection starts
function get_collection_app_details(from_date = "", to_date = "", date_type = "") {
	var id_branch = $('#branch_select').val();
	//alert("1");
	my_Date = new Date();
	$.ajax({
		url: base_url + "index.php/admin_dashboard/get_collection_app_details?nocache=" + my_Date.getUTCSeconds(),
		//data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch}: ''),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			console.log(data.collection_data);
			console.log(data.collection_data.length);
			var trHTML = '';
			var total_amount = 0, total_count = 0;
			var collection_data = data.collection_data;
			if (collection_data.length > 0) {
				var emp_url;
				for (var i = 0; i < collection_data.length; i++) {
					total_amount += parseFloat(collection_data[i].total_amount);
					total_count += parseInt(collection_data[i].count);
					if (isValid(id_branch)) {
						emp_url = base_url + 'index.php/admin/dashboard/collection_App/' + from_date + "/" + to_date + "/" + collection_data[i].id_employee + "/" + id_branch;
					}
					else {
						emp_url = base_url + 'index.php/admin/dashboard/collection_App/' + from_date + "/" + to_date + "/" + collection_data[i].id_employee + "/0";
					}
					trHTML += '<tr>';
					if (isValid(collection_data[i].id_employee)) {
						trHTML += '<td>' + collection_data[i].employee + '</td>';
					}
					else {
						trHTML += '<td>No Employee Data</td>';
					}
					//trHTML+='<td>'+collection_data[i].count+'</td>'
					trHTML += '<td style="text-align:right"><a href=' + emp_url + '><span class="badge bg-aqua">' + collection_data[i].count + '</span></a></td>'
						+ '<td style="text-align:right">' + collection_data[i].total_amount + '</td>'
						+ '</tr>';
				}
				$('#dash_coll_app > tbody').html(trHTML);
				$('#coll_tot').html("Total Amount");
				$('#total_coll_amount').text(indianCurrency.format(total_amount));
			}
			else {
				trHTML = '';
				$('#dash_coll_app > tbody').html(trHTML);
			}
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
// payment _summary-start
function get_payment_summary(from_date = "", to_date = "", id_branch = "", date_type = "") {
	// var branch_name=getBranchTitle();
	// const printWindow = window.open('', '_blank');
	const offlineDiv = document.getElementById("offline");
	const onlineDiv = document.getElementById("online_payment");
	const adminDiv = document.getElementById("admin_payment");
	offlineDiv.innerHTML = '';
	onlineDiv.innerHTML = '';
	adminDiv.innerHTML = '';
	// var title='';
	// title+=get_title($('#rpt_payments1').html(),$('#rpt_payments2').html(),'Summary - Source Wise Report - '+branch_name);
	var id_branch = $('#branch_select').val(); 
	var log_branch = $('#id_branch').val();
	if(log_branch != 0){
		id_branch = log_branch;
	}
	my_Date = new Date();
	$.ajax({
		url: base_url + "index.php/admin_reports/payment_summary_modewise?nocache=" + my_Date.getUTCSeconds(),
		data: ({ 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type }),
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			const subtotals = [];
			createTable(data.mode_wise.offline, "offline", data.offline_auth_total);
			createTable(data.mode_wise.admin_app, "admin_payment", data.admin_app_auth_total);
			createTable(data.mode_wise.online, "online_payment", data.online_auth_total);
			// const grandTotal = subtotals.reduce((acc, subtotal) => acc + subtotal, 0);
			const grandTotal = subtotals.reduce((acc, subtotal) => acc + subtotal, 0);
			// Append the grand total at the end of the last table
			const lastTableDiv = document.getElementById("online_payment"); // Use the ID of the last table
			// Create grand total row
			const grandTotalRow = document.createElement("tr");
			const grandTotalLabelCell = grandTotalRow.insertCell(0);
			grandTotalLabelCell.style.textAlign = "left";
			const grandTotalAmtCell = grandTotalRow.insertCell(1);
			grandTotalAmtCell.style.textAlign = "right";
			grandTotalLabelCell.innerHTML = "Grand Total";
			grandTotalLabelCell.style.color = "green";
			grandTotalAmtCell.innerHTML = indianCurrencyFormat(grandTotal);
			grandTotalAmtCell.style.color = "green";
			// Append grand total row to the last table
			const lastTable = lastTableDiv.querySelector("table");
			lastTable.tFoot.appendChild(grandTotalRow);
			function createTable(data, divId, authTotal) {
				const div = document.getElementById(divId);
				const table = document.createElement("table");
				table.style.width = "100%";
				table.classList.add("table", "table-condensed");
				const headerRow = table.insertRow();
				const modeHeader = headerRow.insertCell(0);
				modeHeader.textContent = "Mode";
				const amtHeader = headerRow.insertCell(1);
				amtHeader.style.textAlign = "right";
				amtHeader.textContent = "Amount";
				let subtotal = 0;
				data.forEach(item => {
					const row = table.insertRow();
					const modeCell = row.insertCell(0);
					if (item.mode_name != '' && item.mode_name != null) {
						modeCell.textContent = item.mode_name;
					} else {
						modeCell.textContent = item.payment_mode;
					}
					const amtCell = row.insertCell(1);
					amtCell.style.textAlign = "right";
					const amount = Number(item.offline_amt || item.admin_app_amt || item.online_amt);
					amtCell.textContent = indianCurrencyFormat(amount);
					subtotal += amount;
				});
				const displaySubtotal = (authTotal !== undefined && authTotal !== null)
					? Number(authTotal)
					: subtotal;
				subtotals.push(displaySubtotal);
				const tfoot = table.createTFoot();
				const subtotalRow = tfoot.insertRow();
				const subtotalLabelCell = subtotalRow.insertCell(0);
				const subtotalAmtCell = subtotalRow.insertCell(1);
				subtotalAmtCell.style.textAlign = "right";
				subtotalLabelCell.innerHTML = "Subtotal";
				subtotalLabelCell.style.color = "red";
				subtotalAmtCell.innerHTML = indianCurrencyFormat(displaySubtotal);
				subtotalAmtCell.style.color = "red";
				// Append the table to the div
				div.appendChild(table);
			}
		}
	});
}
function set_customer_link(from_date = "", to_date = "", date_type = "") {
	var id_branch = $('#branch_select').val();
	if ($('#branch_set').val() == 0 || (id_branch == '' || id_branch == 'undefined' || id_branch == null)) {
		var customer_url = base_url + 'index.php/reports/detail/registration_bydate/' + from_date + "/" + to_date + "/0";
		var customer_without_acc_url = base_url + 'index.php/customer/withoutAccount_bydate/' + from_date + "/" + to_date + "/0";
		var account_without_pay_url = base_url + 'index.php/account/withoutPayment_bydate/' + from_date + "/" + to_date + "/0";
	}
	else {
		var customer_url = base_url + 'index.php/reports/detail/registration_bydate/' + from_date + "/" + to_date + "/" + id_branch;
		var customer_without_acc_url = base_url + 'index.php/customer/withoutAccount_bydate/' + from_date + "/" + to_date + "/" + id_branch;
		var account_without_pay_url = base_url + 'index.php/account/withoutPayment_bydate/' + from_date + "/" + to_date + "/" + id_branch;
	}
	my_Date = new Date();
	//alert();
	$.ajax({
		url: base_url + "index.php/admin_dashboard/customer_count?nocache=" + my_Date.getUTCSeconds(),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		//	data:{'from_date':from_date,'to_date':to_date},
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			//alert();
			console.log(data);
			$('#date_list').text(from_date + " to " + to_date);
			$("#customer_count_span").append('<a href=' + customer_url + '><span class="badge bg-aqua">' + data.cust_count + '</span></a>');
			$("#without_acc_cus_count").append('<a href=' + customer_without_acc_url + '><span class="badge bg-aqua">' + data.without_acc_count + '</span></a>');
			$("#without_pay_acc_count").append('<a href=' + account_without_pay_url + '><span class="badge bg-aqua">' + data.acc_wo_pay_count + '</span></a>');
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function get_accounts_bydate(from_date = "", to_date = "", id_branch = "", date_type = "") {
	var id_branch = $('#branch_select').val();
	my_Date = new Date();
	// $("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_dashboard/account_bydate?nocache=" + my_Date.getUTCSeconds(),
		//data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch}: ''),
		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'date_type': date_type },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			console.log(data);
			$("#all_acc").text(data);
			//set_account_list(data,from_date,to_date);
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
