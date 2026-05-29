var path = url_params();



var ctrl_page = path.route.split('/');



var img_resource = [];



var total_files = [];



var tax_details = [];



var pre_img_files = [];



var pre_img_resource = [];



var cat_product_details = [];



var metal_rates = [];



var stone_details = [];



var stones = [];



var stone_types = [];



var uom_details = [];



var other_charge_details = [];



var modalStoneDetail = [];



var OrderProducts = [];



var section_details = [];


var branch_details = [];



$(document).ready(function () {







	var path = url_params();



	$('#status').bootstrapSwitch();



	$('.date').datepicker({ dateFormat: 'yyyy-mm-dd', })



	$('body').addClass("sidebar-collapse");



	$(document).on('keydown', "input[type='number']", function (event) {



		if (event.which == 40 || event.which == 38 || event.which == 37 || event.which == 39 || event.which == 189 || event.which == 109) {



			event.preventDefault();



		}



	});

	// $(document).on('input', "input[type='number']", function () {

	// 	if ($(this).val() < 0) {

	// 		$(this).val(0);

	// 	}

	// });





	if (ctrl_page[1] == "repair_order" && ctrl_page[2] == 'add') {

		Webcam.set({

			width: 290,

			height: 190,

			image_format: 'jpg',

			jpeg_quality: 90

		});

		Webcam.attach('#my_camera');

		//Webcam.attach('#ed_my_camera');

		Webcam.on('error', function (err) {

			console.log('Error accessing webcam:', err);

		});

	}



	if ($('.smith_due_dt').length > 0) {



		$('.smith_due_dt').datepicker({



			startDate: '+1d',



			format: 'yyyy-mm-dd',







		})



			.on('changeDate', function (ev) {



				$(this).datepicker('hide');



			});



	}













	switch (ctrl_page[1]) {



		case 'order':



			switch (ctrl_page[2]) {



				case 'list':



					var date = new Date();



					var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 29, 1);



					var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());



					var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());



					$('#rpt_payments1').html(from_date);



					$('#rpt_payments2').html(to_date);



					setOrderList();



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



							startDate: moment().subtract(29, 'days'),



							endDate: moment()



						},



						function (start, end) {



							$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));







							$('#rpt_payments1').text(start.format('YYYY-MM-DD'));



							$('#rpt_payments2').text(end.format('YYYY-MM-DD'));



						}



					);



					break;







				case 'edit':



					//setEstiData(ctrl_page[3]);



					document.getElementById('toggle-webcam_button').addEventListener('click', function () {











						toggle(document.querySelectorAll('.target_preview_webcam'));







						web_cam_order_images();



					});



					get_orderedit_data();



					getCusproducts();







					$(document).on('keyup', ".product", function (e) {



						var prod = this.value;



						if (prod.length >= 3) {



							console.log(prod.length);



							var row = $(this).closest('tr');



							getSearchProd(prod, this.id, row);



						}



					});



					$(document).on('keyup', ".design", function (e) {



						var val = this.value;



						if (val.length >= 1) {



							getSearchDesign(val, this.id);



						}



					});



					$(document).on('keyup', "#esti_no", function (e) {



						var val = this.value;







						searchEsti(val);



					});



					$(document).on('keyup', ".sub_design", function (e) {



						let curRow = $(this).closest('tr');



						var val = this.value;



						if (val.length >= 1) {



							getSearchSubDesign(val, this.id, curRow);



						}



					});



					$('input[type=radio][name="order[order_type]"]').change(function () {



						let checked_radio = $('input[name="order[order_type]"]:checked').val();



						if (checked_radio == 2) {



							$(".tag_scanning").css("display", "none");



							$(".add_item").css("display", "block");



							$(".add_home_bill").css("display", "none");



							$("#item_detail tbody").empty();



						} else if (checked_radio == 5) {



							$(".tag_scanning").css("display", "block");



							$(".add_item").css("display", "none");







							$(".add_home_bill").css("display", "none");



							$("#item_detail tbody").empty();



						} else if (checked_radio == 6) {



							$(".tag_scanning").css("display", "none");



							$(".add_item").css("display", "none");



							$(".add_home_bill").css("display", "block");



							$("#item_detail tbody").empty();



						}







					});



					get_stones();



					get_stone_types();



					if($('#id_branch').val() !=''){
					get_cmp_state();
					}



					get_ActiveUOM();



					getOtherChargesDetails();



					get_metal_rates_by_branch();



					get_all_employee();


					get_branches();


					break;



				case 'add':





					document.getElementById('toggle-webcam_button').addEventListener('click', function () {





						toggle(document.querySelectorAll('.target_preview_webcam'));







						web_cam_order_images();



					});



					get_metal_rates_by_branch();



					getOtherChargesDetails();



					get_profession();





					$("#date_of_birth,#ed_date_of_birth").datepicker({ format: 'yyyy-mm-dd' });



					$("#date_of_wed,#ed_date_of_wed").datepicker({ format: 'yyyy-mm-dd' });





					$("#profession").select2({



						placeholder: "Select Profession",



						allowClear: true



					});



					$(document).on('keyup', ".product", function (e) {



						var prod = this.value;



						if (prod.length >= 3) {



							console.log(prod.length);



							var row = $(this).closest('tr');



							getSearchProd(prod, this.id, row);



						}



					});







					$(document).on('keyup', ".design", function (e) {



						var val = this.value;



						if (val.length >= 3) {



							getSearchDesign(val, this.id);



						}



					});



					$(document).on('keyup', "#esti_no", function (e) {



						var val = this.value;



						searchEsti(val);



					});







					$(document).on('keyup', ".sub_design", function (e) {



						curRow = $(this);



						var val = this.value;



						if (val.length >= 1) {



							getSearchSubDesign(val, this.id, curRow);



						}



					});







					$('input[type=radio][name="order[order_type]"]').change(function () {



						$("#item_detail tbody").empty();



						let checked_radio = $('input[name="order[order_type]"]:checked').val();



						if (checked_radio == 2) {



							$(".tag_scanning").css("display", "none");



							$(".add_item").css("display", "block");



						} else if (checked_radio == 5) {



							$(".tag_scanning").css("display", "block");



							$(".add_item").css("display", "none");



						} else if (checked_radio == 6) {



							$(".tag_scanning").css("display", "none");



							$(".add_item").css("display", "none");



						}



					});





					if ($('#allow_bill_type').val() == 3) {

						$(document).on('keypress', function (e) {

							if (e.keyCode == 10) //ctrl+enter

							{

								if ($('#is_eda').val() == 1) //IF is_eda is 1 Normal order,2-No2 order

								{

									$('#is_eda').val(2);



									$('#cus_name').css("background-color", "red");



									$('.eda_tax_calc').css('display', 'block');

								} else {

									$('#is_eda').val(1);



									$('#cus_name').css("background-color", "");



									$('.eda_tax_calc').css('display', 'none');

								}



								calculate_orderSale_value();

							}

						})

					} else if ($('#allow_bill_type').val() == 1) // Only Normal Order

					{

						$('#is_eda').val(1);



						$('#cus_name').css("background-color", "");



						$('.eda_tax_calc').css('display', 'none');

					} else {



						$('#is_eda').val(2);



						$('.eda_tax_calc').css('display', 'block');



						$('#bill_cus_name').css("background-color", "red");

					}







					/*if($('#description').length > 0)



					{



						  CKEDITOR.replace('description');



					}*/







					get_stones();



					if($('#id_branch').val() !=''){
					get_cmp_state();
					}



					get_stone_types();



					get_ActiveUOM();



					get_all_employee();



					getCusproducts();







					$(document).on('change', ".show_in_lwt", function () {



						if ($(this).is(":checked")) {



							$(this).closest('tr').find('.show_in_lwt').val(1);



						} else {



							$(this).closest('tr').find('.show_in_lwt').val(0);



						}



					});



					get_branches();



					break;



				case 'repair_add':



					get_all_karigar();



					get_all_employee();



					get_all_master_data();



					if ($('#description').length > 0) {



						CKEDITOR.replace('description');



					}



					break;



				case 'repair_item_details':



					get_category();



					get_ActiveProduct();



					get_edit_repair_details(ctrl_page[3]);





					$("#purity").select2({



						placeholder: "Select Purity",



						allowClear: true



					});



					get_stones();



					get_stone_types();



					get_ActiveUOM();





					$("#des_select").select2({



						placeholder: "Select Design",



						allowClear: true



					});



					$("#sub_design_select").select2({



						placeholder: "Select Sub Design",



						allowClear: true



					});





					$('#des_select').on('change', function () {



						get_ActiveSubDesign();



					});





					$('#prod_select').on('change', function () {



						get_Activedesigns(this.value);



					});





					$("#item_type").select2({



						placeholder: "Select Item Type",



						allowClear: true



					});



					$("#section").select2({



						placeholder: "Select Section",



						allowClear: true



					});



					get_Activesection();



					$("#item_type").select2("val", '1');



					break;



			}







		case 'customer_neworders':



			get_all_karigar();



			// get_all_branches();



			var date = new Date();



			var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 29, 1);



			var from_date = firstDay.getFullYear() + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();



			var to_date = (date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate());





			$('#new_list1').text(from_date);



			$('#new_list2').text(to_date);



			get_new_orderlist(from_date, to_date);



			$('#account_list1').text(moment().startOf('month').format('YYYY-MM-DD'));



			$('#account_list2').text(moment().endOf('month').format('YYYY-MM-DD'));



			$('#account-dt-btn').daterangepicker(



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



					get_new_orderlist(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))



					$('#new_list1').text(start.format('YYYY-MM-DD'));



					$('#new_list2').text(end.format('YYYY-MM-DD'));



				}



			);



			break;



		case 'neworders':



			//get_new_orderlist();



			get_all_karigar();



			get_all_branches();



			get_all_employee();



			get_ActiveCounters();



			var date = new Date();



			var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 6, 1);



			var from_date = firstDay.getFullYear() + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();



			var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());



			$('#new_list1').text(from_date);



			$('#new_list2').text(to_date);



			get_new_orderlist(from_date, to_date);



			$('#account_list1').text(moment().startOf('month').format('YYYY-MM-DD'));



			$('#account_list2').text(moment().endOf('month').format('YYYY-MM-DD'));



			$('#account-dt-btn').daterangepicker(



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



					$('#account-dt-btn').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));



					get_new_orderlist(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))



					$('#new_list1').text(start.format('YYYY-MM-DD'));



					$('#new_list2').text(end.format('YYYY-MM-DD'));



				}



			);



			break;







		case 'stock_issue':



			switch (ctrl_page[2]) {



				case 'list':



					set_repair_order_list();



					break;



				case 'add':



					get_all_karigar();



					get_all_employee();



					get_all_master_data();



					$('#issue_type').select2();



					$('#repair_type').select2();



					if ($('#description').length > 0) {



						CKEDITOR.replace('description');



					}



					get_StockIssueItems();







					break;



			}



			break;







		case 'repair_order':



			switch (ctrl_page[2]) {



				case 'list':





					// get_all_branches();



					get_all_employee();



					get_ActiveCounters();



					var date = new Date();

					var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 0, 1);

					var from_date = (firstDay.getDate() + "-" + (firstDay.getMonth() + 1) + "-" + firstDay.getFullYear());

					var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());



					$('#rpt_payments1').html(from_date);



					$('#rpt_payments2').html(to_date);



					set_repair_order_list(from_date, to_date);



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

							// $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));



							$('#rpt_payment_date').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));



							$('#rpt_payments1').text(start.format('YYYY-MM-DD'));

							$('#rpt_payments2').text(end.format('YYYY-MM-DD'));

						}

					);



					break;



				case 'add':



					get_stones();



					get_stone_types();



					get_ActiveUOM();



					get_profession();





					$("#date_of_birth,#ed_date_of_birth").datepicker({ format: 'yyyy-mm-dd' });



					$("#date_of_wed,#ed_date_of_wed").datepicker({ format: 'yyyy-mm-dd' });





					$("#profession").select2({



						placeholder: "Select Profession",



						allowClear: true



					});



					document.getElementById('toggle-webcam_button').addEventListener('click', function () {





						toggle(document.querySelectorAll('.target_preview_webcam'));



						web_cam_order_images();



					});



					$("#cus_name").on("keyup", function (e) {



						var customer = $("#cus_name").val();



						if (customer.length >= 2) {



							getSearchCustomers(customer);



						}



					});





					break;





				case 'repair_order_status':



					var date = new Date();



					var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 6, 1);



					var from_date = firstDay.getFullYear() + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();



					var to_date = (date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear());



					$('#rpt_payments1').text(from_date);



					$('#rpt_payments2').text(to_date);



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



							startDate: moment().subtract(6, 'days'),



							endDate: moment()



						},



						function (start, end) {



							// $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));



							$('#rpt_payment_date').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));



							$('#rpt_payments1').text(start.format('YYYY-MM-DD'));



							$('#rpt_payments2').text(end.format('YYYY-MM-DD'));



						}



					);





					$('#order_status').select2({



						placeholder: "Select Order Status",



						allowClear: true



					});



					$('#order_status').select2("val", '');



					get_order_status();



					setRepairOrderStatus();



					get_ActiveCounters();



					break;



				case 'edit':



					get_repair_order_edit_data();



					get_stones();



					get_stone_types();



					get_ActiveUOM();





					$("#cus_name").on("keyup", function (e) {



						var customer = $("#cus_name").val();



						if (customer.length >= 2) {



							getSearchCustomers(customer);



						}



					});



					document.getElementById('toggle-webcam_button').addEventListener('click', function () {





						toggle(document.querySelectorAll('.target_preview_webcam'));



						web_cam_order_images();



					});



					break;







			}



			break;







		case 'cart':



			get_ActiveKaigar();



			get_order_cart();



			get_ActiveProduct();



			get_Activedesign('');



			get_weight_range('');



			$(document).on('keyup', ".karigar_search", function (e) {



				var karigar = this.value;



				if (karigar.length >= 3) {



					var row = $(this).closest('tr');



					getSearchKarigar(karigar, row);



				}



			});



			if (ctrl_page[2] == 'cart_status') {



				set_cart_status();



			}



			break;



	}







	$('.order_from').select2().on('change', function () {



		if (this.value != '') {



			$('#id_branch').val(this.value);



			get_metal_rates_by_branch(this.value);



			calculate_orderSale_value();



			let checked_radio = $('input[name="order[order_type]"]:checked').val();



			if (checked_radio == 4) {



				$("#tagissue_item_detail tbody").html('');



			}







		}



		else {



			$('#id_branch').val('');



		}



	});







	$('#order_to_br').on('change', function (e) {



		$('#id_order_to_br').val(this.value);



	})







	$(document).on('change', ".purity", function () {



		$('#id_' + this.id).val(this.value);



	})



	/*	$(document).on('change',".order_type", function(){



			if(this.value == 2 || this.value == 3 ){



				var i = this.id;



				if(! $('#1_img'+i).length){



					var newRow = $("<tr>");



					html2 = "<td colspan='3'><textarea name='o_item["+i+"][smith_remainder_date]' rows='3' cols='50'></textarea></td><td colspan='3'> <input type='file' name='o_item["+i+"][img_1]' /><img src='' alt='Upload Sample 1' class='img-thumbnail uploadImg' id='1_img"+i+"' alt='Product Sample' width='150' height='75'></td><td colspan='3'><input type='file' name='o_item["+i+"][img_2]' /><img src='' alt='Upload Sample 2' class='img-thumbnail uploadImg' id='2_img"+i+"' alt='Product Sample' width='150' height='75'> </td>";



						newRow.append(html2);



						newRow.insertAfter($(this).parents().closest('tr'));



					}



			}







		})*/



	$(document).on('change', ".order_type", function () {



		if (this.value == 2 || this.value == 3) {



			var i = this.id;



			console.log($('#1_img' + i).length);



			if (!$('#1_img' + i).length) {



				var newRow = $("<tr id='" + i + "' class='imgrow" + i + "'>");



				html2 = "<td colspan='3'><textarea name='o_item[" + i + "][smith_remainder_date]' rows='3' cols='50'></textarea></td><td colspan='12'><input type='hidden' id='image_name_" + i + "' name='o_item[" + i + "][image]' value=''> <input type='file' id='img" + i + "' class='img1' name='o_item[" + i + "][img_1][]' multiple /><div alt='Upload Sample 1' class='col-md-12' id='1_img" + i + "'><input type='hidden' name='o_item[" + i + "][remove_files]' id='files" + i + "'></div><button id='img_upload' type='button' class='btn btn-success pull-right'><i class='fa fa-plus'></i>Upload</button></td>";



				newRow.append(html2);



				newRow.insertAfter($(this).parents().closest('tr'));



			}



		}



		else {



			id = $(this).closest("tr").next('tr').attr('id');



			var rowid = "imgrow" + id;



			$('.' + rowid + '').remove();



		}







	})







	$('input[type=radio][name="order[order_for]"]').change(function () {



		var order_for = $("input[name='order[order_for]']:checked").val();



		if (order_for == 2) { // Customer



			$('.brn').css('display', 'none');



			$('.cus').css('display', 'block');



		}



		else if (order_for == 1) { // Branch



			$('.brn').css('display', 'block');



			$('.cus').css('display', 'none');



		}



	});







	/* Customer search. - Start */



	$("#cus_name").on("keyup", function (e) {



		var customer = $("#cus_name").val();



		if (customer.length >= 2) {



			getSearchCustomers(customer);



		}



	});



	/* Ends - Customer search. */







	//Image validation



	$(".uploadImg").on('change', function () {



		validateImage(this);



	});



	function getOtherChargesDetails() {



		$.ajax({



			type: 'GET',



			url: base_url + 'index.php/admin_ret_tagging/getOtherCharges',



			dataType: 'json',



			success: function (data) {



				other_charges_details = data;



			}



		});



	}



	$("#create_order").on('click', function (e) {



		e.preventDefault();

		if ($('#item_detail > tbody  > tr').length > 0) {

			if (validateOrderDetailRow()) {



				var order_for = $("input[name='order[order_for]']:checked").val();



				if (($('#cus_id').val() == null || $('#cus_id').val() == '') && order_for == 2) {



					$("#customerAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');



					return false;



				}



				else if (($('#order_to_br').val() == null || $('#order_to_br').val() == '') && order_for == 1) {



					alert("Select Order For Branch");



					return false;



				}



				else if ($('.order_from').val() == null || $('.order_from').val() == '') {



					alert("Select Order Branch");



					return false;



				}



				else {



					create_customer_order();



				}



			} else {



				alert('Please Fill The Required Fields..');



			}

		} else {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Order Details..' });

		}







	});



	function create_customer_order() {



		$("div.overlay").css("display", "block");



		$('#create_stock_order').prop('disabled', true);



		var form_data = $('#order_submit').serialize();



		if (ctrl_page[3] != '' && ctrl_page[2] == 'edit') {



			var url = base_url + "index.php/admin_ret_order/order/update?nocache=" + my_Date.getUTCSeconds();



		} else {



			var url = base_url + "index.php/admin_ret_order/order/save?nocache=" + my_Date.getUTCSeconds();



		}







		$.ajax({



			url: url,



			data: form_data,



			type: "POST",



			dataType: "JSON",



			success: function (data) {



				if (ctrl_page[3] != '' && ctrl_page[2] == 'edit') {



					window.location.replace(base_url + 'index.php/admin_ret_order/order/list');



				}



				else {



					window.location.replace(base_url + 'index.php/admin_ret_order/order/list');



				}



				if (data.id_customerorder != '' && data.order_for == 1) {



					window.open(base_url + 'index.php/admin_ret_order/vendor_acknowladgement/' + data.id_customerorder, '_blank');



				}



				location.href = base_url + 'index.php/admin_ret_order/order/list';



				$("div.overlay").css("display", "none");



			},



			error: function (error) {



				$("div.overlay").css("display", "none");



			}



		});



		$('#create_stock_order').prop('disabled', false);



	}







	$(document).on('change', ".img1", function () {



		id = $(this).closest("tr").attr('id');



		$('#cur_id').val(id);



		item_validateImage();



	});



	$(document).on('change', ".category", function () {







		var row = $(this).closest('tr');



		let cat_id = $(this).val();



		// let id_purity = (row.find('id_purity').val() ? row.find('id_purity').val() : 0);



		category_change(row, cat_id);



	});



	function get_ActiveCusorderProduct(curRow, id_category) {







		$(".overlay").css('display', 'block');



		$.ajax({



			type: 'POST',



			data: { 'id_category': id_category },



			url: base_url + 'index.php/admin_ret_order/get_ActiveProducts',



			dataType: 'json',



			success: function (data) {



				get_products = data;



				curRow.find(".product option").remove();



				$('.product').append(



					$("<option></option>")



						.attr("value", "")



						.text('-Choose-')



				);



				$.each(data, function (key, item) {



					curRow.find(".product").append(



						$("<option></option>")



							.attr("value", item.pro_id)



							.text(item.product_name)



					);



				});



				$(".product").select2({



					placeholder: "product",



					allowClear: true



				});



				curRow.find(".pro_id").val(curRow.find(".product").val());



				$(".overlay").css("display", "none");







			}



		});



	}



	/*$(document).on('change',".product",function(){



		var row = $(this).closest('tr');



		row.find('.design option').remove();







		if(this.value!='')



		{



			get_product_size(row,this.value);



			get_ActiveDesigns(row,this.value);



		}



	});*/















	function get_ActiveDesigns(curRow) {







		$(".overlay").css('display', 'block');



		$.ajax({



			type: 'POST',



			url: base_url + 'index.php/admin_ret_catalog/get_active_design_products',



			dataType: 'json',



			data: { 'id_product': curRow.find('.product').val() },



			success: function (data) {



				curRow.find(".design option").remove();



				$('.design').append(



					$("<option></option>")



						.attr("value", "")



						.text('-Choose-')



				);



				$.each(data, function (key, item) {



					curRow.find(".design").append(



						$("<option></option>")



							.attr("value", item.design_no)



							.text(item.design_name)



					);



				});



				$(".design").select2({



					placeholder: "Design",



					allowClear: true



				});



				curRow.find(".design_no").val(curRow.find(".design").val());



				$(".overlay").css("display", "none");











			}



		});



	}



	/*$(document).on('change',".design",function(){



		var row = $(this).closest('tr');



		row.find('.sub_design option').remove();



		if(this.value!='')



		{



			get_ActiveSubDesigns(row,this.value);



		}



	});*/



	/*function get_ActiveSubDesigns(curRow,id_design)



	{







		$(".overlay").css('display','block');



		$.ajax({



			type: 'POST',



			url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',



			dataType:'json',



			data:{'id_product':curRow.find('.product').val(),'design_no':curRow.find('.design').val()},



			success:function(data){



				curRow.find(".sub_design option").remove();



				$('.sub_design').append(



					$("<option></option>")



					.attr("value", "")



					.text('-Choose-')



				);



			   $.each(data, function (key, item) {



					curRow.find(".sub_design").append(



						$("<option></option>")



						.attr("value", item.id_sub_design)



						.text(item.sub_design_name)



					);



				});



				$(".sub_design").select2({



					placeholder: "Sub Design",



					allowClear: true



				});



				curRow.find(".id_sub_design").val(curRow.find(".sub_design").val());



				$(".overlay").css("display", "none");







			}



		});



	}*/







	$(document).on('keyup change', ".order_from,.category,.weight,.mc,.stn_amt,.mc_type,.order_rate", function () {



		calculate_orderSale_value();



	});







	$(document).on('change', ".wast_percent", function () {



		var curRow = $(this).closest('tr');



		if (this.value > 100) {



			curRow.find('.wast_percent').val('');



			$.toaster({ priority: 'warning', title: 'Warning!', message: '' + "</br>Please Enter The Valid Percentage" });



		}



		calculate_orderSale_value();



	});



	Date.prototype.toInputFormat = function () {



		var yyyy = this.getFullYear().toString();



		var mm = (this.getMonth() + 1).toString();



		var dd = this.getDate().toString();



		return (dd[1] ? dd : "0" + dd[0]) + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + yyyy; // padding



	};







	$('#issue_type').on('change', function () {



		$('#stockrepair_item_detail > tbody').empty();



		$('#tagissue_item_detail > tbody').empty();



		var ordertype = this.value;



		if (ordertype == 1) {



			$('#repair_item_type').css("display", "block");



			$('.issuerepair').css("display", "block");



			$('.issueothers').css("display", "none");



		} else {



			$('.issueothers').css("display", "block");



			$('.issuerepair').css("display", "none");



			$('#repair_item_type').css("display", "none");



		}



	});







	$('#repair_type').on('change', function () {



		var repairtype = this.value;



		if (repairtype == 1) {



			$('.stock_repair_det').css("display", "block");



			$('.customer_repair_det').css("display", "none");



			$('.repair_delivered_btn').css("display", "block");



			$('.repair_order_status_btn').css("display", "none");



		} else {



			$('.customer_repair_det').css("display", "block");



			$('.stock_repair_det').css("display", "none");





			$('.repair_order_status_btn').css("display", "block");



			$('.repair_delivered_btn').css("display", "none");

		}



	});



















	function create_new_empty_repair_order_row() {



		var html = "";



		var a = $("#cus_i_increment").val();



		var i = ++a;



		$("#cus_i_increment").val(i);



		var cus_due_date = $('#cus_due_date').val();



		var smith_due_date = $('#smith_due_date').val();



		var smith_rem_date = $('#smith_remainder_date').val();



		var collections_required = $('#collections_required').val();



		var subproduct_required = $('#subproduct_required').val();











		html += "<tr id='st_detail" + i + "' class='" + i + "'>" +



			"<td><input type='text' id='o_tag_code_" + i + "' name='o_item[" + i + "][tag_code]'class='tag_code' required='true' /> <input type='hidden' id='o_item_repair_tag_id_" + i + "' name='o_item[" + i + "][tag_id]'class='repair_tag_id'  />" +



			"<td><span class='tag_id_cat'></span><input type='hidden' id='o_item_id_cat_" + i + "' name='o_item[" + i + "][id_cat]'class='id_cat' required='true' /></td>" +



			"<td><span class='tag_purity'></span><input type='hidden' name='o_item[" + i + "][pure_wt]' class='pure_wt' required='true' /></td>" +



			"<td><span class='tag_id_prod'></span><input type='hidden' id='o_item_id_prod_" + i + "' name='o_item[" + i + "][id_product]'class='id_product' required='true' /><input type='hidden' name='o_item[" + i + "][orter_type]' id='ortertype" + i + "' value='3' required='true'/><input type='hidden' name='o_item[" + i + "][id_purity]' id='id_purity" + i + "' value='1' required='true'/></td>" +



			"<td><span class='tag_id_des'></span><input type='hidden' id='o_item_id_des_" + i + "' name='o_item[" + i + "][id_des]' class='id_des' required='true' /></td>" +



			"<td><span class='tag_id_sub_des'></span><input type='hidden' id='o_item_id_sub_des_" + i + "' name='o_item[" + i + "][id_sub_des]' class='id_sub_des' required='true' /></td>" +



			"<td><input type='number' class='form-control gweight' name='o_item[" + i + "][gweight]' id='gweight_" + i + "' autocomplete='off' readonly /></td>" +



			"<td><input type='number' class='form-control nweight' name='o_item[" + i + "][nweight]' id='nweight_" + i + "' autocomplete='off' readonly /></td>" +



			"<td><input type='number' class='form-control qty' placeholder='Pcs' name='o_item[" + i + "][totalitems]' required='true'/></td>" +



			'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" name="o_item[' + i + '][order_img]""></td>' +



			'<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" name="o_item[' + i + '][description]"></td>' +



			"<td><input class='form-control datemask date cus_due_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][cus_due_date]' value=" + cus_due_date + " type='text' required='true' placeholder='Cus Due Date' readonly />" +



			'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



		"</tr>";



		$('#stockrepair_item_detail tbody').append(html);







		$('.date').datepicker({ dateFormat: 'yyyy-mm-dd', })







		$('#stockrepair_item_detail > tbody').find('.category').focus();



	}















});



// add new metal information



$("#add_order_item").on('click', function () {



	debugger



	pre_img_resource = [];



	var order_for = $("input[name='order[order_for]']:checked").val();



	if (($('#cus_id').val() == null || $('#cus_id').val() == '') && order_for == 2) {



		//$("#customerAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');



		$.toaster({ priority: 'danger', title: 'Warning!', message: 'Enter a valid customer name / mobile' });



		return false;



	}



	else if (($('#order_to_br').val() == null || $('#order_to_br').val() == '') && order_for == 1) {



		alert("Select Order For Branch");



		return false;



	}



	else if ($('.order_from').val() == null || $('.order_from').val() == '') {



		alert("Select Order Branch");



		return false;



	}



	else {



		if (validateOrderDetailRow()) {



			create_new_empty_cus_order_row('add', []);



		}



		else {



			alert('Please Fill The Required Fields..');



		}







	}



});







function validateOrderDetailRow() {



	var validate = true;



	$('#item_detail > tbody  > tr').each(function (index, tr) {



		console.log("category", $(this).find('.category').val());



		console.log("id_metal", $(this).find('.id_metal').val());



		console.log("purity", $(this).find('.purity').val());



		console.log("id_product", $(this).find('.id_product').val());



		console.log("id_design", $(this).find('.id_design').val());



		console.log("id_sub_design", $(this).find('.id_sub_design').val());



		console.log("weight", $(this).find('.weight').val());



		console.log("net_wt", $(this).find('.net_wt').val());



		console.log("wast_wgt", $(this).find('.wast_wgt').val());



		var balance_type = $("input[name='order[balance_type]']:checked").val();



		var description = $(this).find('.order_des').val().trim();



		if ($(this).find('.category').val() == "" || $(this).find('.id_metal').val() == "" || $(this).find('.purity').val() == "" || $(this).find('.id_product').val() == "" || $(this).find('.id_design').val() == "" || $(this).find('.id_sub_design').val() == "" || $(this).find('.weight').val() == "" || $(this).find('.net_wt').val() == "" || $(this).find('.wast_wgt').val() == "") {



			validate = false;



			alert('Please Fill The Required Fields..');



		}



		else if ($('.due_date').val() == null || $('.due_date').val() == '') {



			alert("Select Due Date");



			validate = false;



		}



		else if ($('#order_img').val() === '') {



			alert('Please select an image.');



			validate = false;



		}







		else if (description === '' && $('#customer_order_description_req').val() == 1) {



			alert('Please enter a description.');



			validate = false;



		}



		else if ((!is_samepurity()) && balance_type == 1) {



			validate = false;



			alert('Order cannot be created with different purities!');



		}



	});



	return validate;



}



function is_samepurity() {



	let is_samepurity = true;







	let first_purity = '';







	$('#item_detail > tbody  > tr').each(function (index, tr) {







		if (index == 0) {







			first_purity = $(this).find('.purity').val();







		} else {







			if (!(parseFloat(first_purity) == parseFloat($(this).find('.purity').val()))) {







				is_samepurity = false;







				return false;







			}







		}







	});







	return is_samepurity;







}







function calculate_orderSale_value() {



	$('#item_detail > tbody tr').each(function (idx, row) {







		var row = $(this);







		let taxable = 0;







		let mc = 0;







		let rate_per_grm = (isNaN(parseFloat(row.find('.order_rate').val())) || !(parseFloat(row.find('.order_rate').val()) > 0)) ? 0 : parseFloat(row.find('.order_rate').val());







		var weight = (isNaN(row.find('.weight').val()) || row.find('.weight').val() == '') ? 0 : row.find('.weight').val();







		var stn_amt = (isNaN(row.find('.stn_amt').val()) || row.find('.stn_amt').val() == '') ? 0 : row.find('.stn_amt').val();







		var retail_max_mc = (isNaN(row.find('.mc').val()) || row.find('.mc').val() == '') ? 0 : row.find('.mc').val();







		var wast_percent = (isNaN(row.find('.wast_percent').val()) || row.find('.wast_percent').val() == '') ? 0 : row.find('.wast_percent').val();







		var wast_wgt = (isNaN(row.find('.wast_wgt').val()) || row.find('.wast_wgt').val() == '') ? 0 : row.find('.wast_wgt').val();







		var tax_group = (isNaN(row.find('.tax_group').val()) || row.find('.tax_group').val() == '') ? 0 : row.find('.tax_group').val();







		var less_wt = (isNaN(row.find('.less_wt').val()) || row.find('.less_wt').val() == '') ? 0 : row.find('.less_wt').val();







		let calculation_type = (isNaN(row.find('.calculation_type').val()) || row.find('.calculation_type').val() == '') ? 2 : row.find('.calculation_type').val();







		let value_charge = isNaN(parseFloat(row.find('.value_charge').val())) ? 0 : row.find('.value_charge').val();







		row.find('.net_wt').val((parseFloat(weight) - parseFloat(less_wt)).toFixed(3));







		let net_wt = row.find('.net_wt').val();







		//taxable = parseFloat(parseFloat(parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) +parseFloat(mc)+parseFloat(stn_amt) + parseFloat(value_charge)).toFixed(2);







		if (calculation_type == 0) {







			let wast_wgt = parseFloat(parseFloat(weight) * parseFloat(wast_percent / 100)).toFixed(3);







			row.find('.wast_wgt').val(wast_wgt);







			if (row.find('.mc_type').val() != 3) {







				mc = parseFloat(row.find('.mc_type').val() == 2 ? parseFloat(retail_max_mc * weight) : parseFloat(retail_max_mc * 1));







				// Metal Rate + Stone + OM + Wastage + MC







				taxable = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc) + parseFloat(stn_amt) + parseFloat(value_charge));







			} else {







				mc = retail_max_mc;







				taxable = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc / 100))) + parseFloat(stn_amt) + parseFloat(value_charge));







			}







		}







		else if (calculation_type == 1) {







			let wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(wast_percent / 100)).toFixed(3);







			row.find('.wast_wgt').val(wast_wgt);







			if (row.find('.mc_type').val() != 3) {







				mc = parseFloat(row.find('.mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * 1));







				// Metal Rate + Stone + OM + Wastage + MC







				taxable = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc) + parseFloat(stn_amt) + parseFloat(value_charge));







			} else {







				mc = retail_max_mc;







				taxable = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc / 100))) + parseFloat(stn_amt) + parseFloat(value_charge));







			}







		}







		else if (calculation_type == 2) {







			let wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(wast_percent / 100)).toFixed(3);







			row.find('.wast_wgt').val(wast_wgt);







			if (row.find('.mc_type').val() != 3) {







				mc = parseFloat(row.find('.mc_type').val() == 2 ? parseFloat(retail_max_mc * weight) : parseFloat(retail_max_mc * 1));







				// Metal Rate + Stone + OM + Wastage + MC







				taxable = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc)) + parseFloat(stn_amt) + parseFloat(value_charge);







			} else {







				mc = retail_max_mc;







				taxable = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc / 100))) + parseFloat(stn_amt) + parseFloat(value_charge));







			}







		}







		else if (calculation_type == 3 || calculation_type == 4) {







			row.find('.wast_wgt').val(0.000);







			console.log("ACTUAL SALES VALUE", row.find('.act_sales_value').val());







			taxable = parseFloat((isNaN(row.find('.act_sales_value').val()) || row.find('.act_sales_value').val() == '') ? 0 : row.find('.act_sales_value').val());







		}







		row.find('.taxable').val(parseFloat(taxable).toFixed(2));







		var total_tax_rate = 0;







		var cus_state = $('#cus_state').val();







		var cmp_state = $('#cmp_state').val();







		var cgst = 0;







		var igst = 0;







		var sgst = 0;



		var is_eda = $('#is_eda').val();





		if (is_eda == 1 || ($('#is_eda').val() == 2 && $('#is_eda_tax_calc').val() == 1)) {



			if (tax_details.length > 0) {



				// Tax Calculation



				var base_value_tax = parseFloat(calculate_base_value_tax(taxable, tax_group)).toFixed(2);







				var base_value_amt = parseFloat(parseFloat(taxable) + parseFloat(base_value_tax)).toFixed(2);







				var arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);







				var arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);







				total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);







				if (cus_state == cmp_state) {







					cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(3);







					sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(3);







				} else {







					igst = total_tax_rate;







				}







			}

		}

		else {

			total_tax_rate = 0;

		}











		console.log("gross weight " + weight);







		console.log("stn_amt " + stn_amt);







		console.log("wast_wgt " + wast_wgt);







		console.log("tax_group " + tax_group);







		console.log("mc " + mc);







		console.log("rate_per_grm " + rate_per_grm);







		console.log("Taxable " + taxable);







		console.log("base_value_tax " + base_value_tax);







		console.log("Tax Rate" + total_tax_rate);







		console.log("--------");











		row.find('.tax').val(total_tax_rate);







		row.find('.o_cgst').val(cgst);







		row.find('.o_sgst').val(sgst);







		row.find('.o_igst').val(igst);







		row.find('.order_amt').val(parseFloat(parseFloat(taxable) + parseFloat(total_tax_rate)).toFixed(2));







	});



}







function calculate_base_value_tax(taxcallrate, taxgroup) {



	var totaltax = 0;



	console.log(tax_details);



	$.each(tax_details, function (idx, taxitem) {



		if (taxitem.tgi_tgrpcode == taxgroup) {



			if (taxitem.tgi_calculation == 1) {



				console.log(1);



				if (taxitem.tgi_type == 1) {



					totaltax += parseFloat(taxcallrate) * (parseFloat(taxitem.tax_percentage) / 100);



				} else {



					totaltax -= parseFloat(taxcallrate) * (parseFloat(taxitem.tax_percentage) / 100);



				}



			}



		}



	});



	return totaltax;



}



function calculate_arrived_value_tax(taxcallrate, taxgroup) {



	var totaltax = 0;



	$.each(tax_details, function (idx, taxitem) {



		if (taxitem.tgi_tgrpcode == taxgroup) {



			if (taxitem.tgi_calculation == 2) {



				if (taxitem.tgi_type == 1) {



					totaltax += parseFloat(taxcallrate) * (parseFloat(taxitem.tax_percentage) / 100);



				} else {



					totaltax -= parseFloat(taxcallrate) * (parseFloat(taxitem.tax_percentage) / 100);



				}



			}



		}



	});



	return totaltax;



}







$(document).on('change', '.is_set_items', function () {



	if ($(this).is(':checked')) {



		$(this).closest('tr').find('.is_set_items').val(1);



	} else {



		$(this).closest('tr').find('.is_set_items').val(0);



	}



});



function create_new_empty_cus_order_row(type, data) {



	if (validateOrderDetailRow()) {



		var html = "";







		var a = $("#i_increment").val();







		var i = ++a;







		$("#i_increment").val(i);







		var cus_due_date = $('#cus_due_date').val();







		var smith_due_date = $('#smith_due_date').val();







		var smith_rem_date = $('#smith_remainder_date').val();







		var collections_required = $('#collections_required').val();







		var subproduct_required = $('#subproduct_required').val();



		let pre_img_resource = [];



		let order_type = "";



		let order_type_name = "";



		let tag_code = "";



		let tag_id = "";



		let cat_id = "";



		let metal_type = "";



		let purity = "";



		let product_name = "";



		let product_id = "";



		let design_name = "";



		let design_id = "";



		let sub_design_name = "";



		let sub_design_id = "";



		let gross_wt = "";



		let less_wt = "";



		let net_wt = "";



		let size = "";







		let pcs = "";



		let wast_perc = "";



		let wast_wt = "";



		let tag_mc_type = "";



		let tag_mc_value = 0;



		let calculation_based_on = "";



		let sales_mode = "";



		let charge_value = 0;







		let stone_details = [];



		let charges_details = [];



		let stone_price = 0;



		let amount = 0;



		let order_rate = 0;



		let tax_amount = 0;



		let id_orderdetails = "";

		let totalStoneWt = 0;







		if (type == 'tag') {



			order_type = 5;



			order_type_name = "Tagged Order";



			$('#item_detail > tbody > tr').each(function (idx, row) {



				let records = data.filter(tag => tag.tag_id == $(this).find('.order_tag_id').val());



				if (records.length > 0) {



					let index = data.indexOf(records[0]);



					if (index !== -1) {



						data.splice(index, 1);



					}



				}



			});



			tag_code = data[0].label;



			tag_id = data[0].tag_id;



			cat_id = data[0].id_ret_category;



			metal_type = data[0].metal_type;



			purity = data[0].purity;



			product_name = data[0].product_name;



			product_id = data[0].lot_product;



			design_name = data[0].design_name;



			design_id = data[0].design_id;



			sub_design_name = data[0].sub_design_name;



			sub_design_id = data[0].id_sub_design;



			gross_wt = data[0].gross_wt;



			less_wt = data[0].less_wt;



			net_wt = data[0].net_wt;



			size = parseInt(data[0].size);



			pcs = data[0].piece;



			calculation_based_on = data[0].calculation_based_on;



			sales_mode = data[0].sales_mode;



			wast_perc = data[0].retail_max_wastage_percent;



			wast_wt = parseFloat(parseFloat(net_wt) * parseFloat(wast_perc / 100)).toFixed(3);



			tag_mc_type = data[0].tag_mc_type;



			tag_mc_value = data[0].tag_mc_value;



			/*if(calculation_based_on == 0 || calculation_based_on == 2) {







				if(tag_mc_type != 3) {



					retail_max_mc = parseFloat(parseFloat(tag_mc_type) == 2 ? parseFloat(parseFloat(tag_mc_value) / parseFloat(gross_wt) ) : parseFloat(parseFloat(tag_mc_value) / 1)).toFixed(2);







				} else {



					retail_max_mc = tag_mc_value;







				}







			}



			else if(calculation_based_on == 1) {







				if(tag_mc_type != 3) {







					retail_max_mc = parseFloat(parseFloat(tag_mc_type) == 2 ? parseFloat(parseFloat(tag_mc_value) / parseFloat(net_wt) ) : parseFloat(parseFloat(tag_mc_value) / 1)).toFixed(2);







				}else {







					retail_max_mc = tag_mc_value;







				}







			}



			else if(calculation_based_on == 3 || calculation_based_on == 4){







				retail_max_mc = tag_mc_value;







			}*/



			$.each(data[0].charges_details, function (ckey, citem) {



				charges_details.push({ 'value_charge': citem.charge_value, 'id_charge': citem.charge_id });



			});



			charge_value = data[0].charge_value == null ? 0 : data[0].charge_value;



			$.each(data[0].stone_details, function (skey, sitem) {



				stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });



				stone_price = parseFloat(stone_price) + parseFloat(sitem.amount);

				totalStoneWt = parseFloat(totalStoneWt) + parseFloat(sitem.wt);



			});



		} else if (type == 'edit') {



			console.log("editdata", data);



			order_type = data.ortertype;



			if (order_type == 5) {







				$(".tag_scanning").css("display", "block");



				$(".add_item").css("display", "none");



				$('#tag_order').attr('checked', true);



				order_type_name = "Tagged Order";



			}



			tag_code = data.label;



			tag_id = data.tag_id;



			cat_id = data.id_ret_category;



			metal_type = data.metal_type;



			purity = data.id_purity;



			product_name = data.product_name;



			product_id = data.id_product;



			design_name = data.design_name;



			design_id = data.design_id;



			sub_design_name = data.sub_design_name;



			sub_design_id = data.id_sub_design;



			gross_wt = data.gross_wt;



			less_wt = data.less_wt;



			net_wt = data.net_wt;



			size = parseInt(data.id_size);



			pcs = data.piece;



			calculation_based_on = data.calculation_based_on;



			sales_mode = data.sales_mode;



			wast_perc = data.wast_percent;



			wast_wt = parseFloat(parseFloat(net_wt) * parseFloat(wast_perc / 100)).toFixed(3);



			tag_mc_type = data.mc_type;



			tag_mc_value = data.mc;



			$('#smith_due_date').val(data.s_due_date);



			smith_due_date = data.s_due_date;



			$('#smith_remainder_date').val(data.s_remainder_date);







			smith_rem_date = data.s_remainder_date;



			cus_due_date = data.cus_due_date;



			order_rate = data.rate_per_gram;



			amount = data.amount;



			tax_amount = data.rate_per_gram * data.gross_wt;



			id_orderdetails = data.id_orderdetails;



			description = data.description;



			days_difference = data.days_difference;









			$.each(data.image_details, function (skey, sitem) {







				pre_img_resource.push({ 'src': sitem.img_src, 'name': sitem.image, 'id': sitem.id_order_img });







			});







			$('#cus_name').val(data.cus_name);



			$('#cus_id').val(data.id_customer);



			$('#cus_state').val(data.id_state);



			$('#cus_order').val(data.order_no);



			$('#branch_select').select2("val", data.id_branch);



			$('#id_branch').val(data.id_branch);









			if (data.balance_type == 1) {



				$('#metal_bal_type').attr('checked', true);



			}



			else {



				$('#cash_bal_type').attr('checked', true);



			}



			if (data.rate_type == 1) {



				$('#order_rate').attr('checked', true);



			}



			else {



				$('#delivery_rate').attr('checked', true);



			}



			//tag_mc_value = data.tag_mc_value;



			$.each(data.charges_details, function (ckey, citem) {



				charges_details.push({ 'value_charge': citem.charge_value, 'id_charge': citem.charge_id });



			});



			charge_value = data.charge_value == null ? 0 : data.charge_value;



			var tot_stone_wt = 0;



			$.each(data.stone_details, function (skey, sitem) {



				if (sitem.is_apply_in_lwt == 1) {



					if (sitem.stones_type == 1) {



						var stone_weight = parseFloat(sitem.wt / 5);



					} else {



						var stone_weight = parseFloat(sitem.wt);



					}



					tot_stone_wt += parseFloat(stone_weight);



				}



				stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stones_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });



				stone_price = parseFloat(stone_price) + parseFloat(sitem.amount);



			});



		} else if (type == 'add') {



			order_type = 2;



			order_type_name = "Customized Order";



		} else if (type == 'home_bill') {



			order_type = 6;



			order_type_name = "Home Bill Order";



		}



		html += "<tr id='detail" + i + "' class='" + i + "'>" +



			"<td style='display:none'>" + order_type_name + "<input type='hidden' name='o_item[" + i + "][order_type]' id='" + i + "' class='form-control order_type' value='" + order_type + "'></td>" +



			"<td><input class='form-control tag_name' type='text' name='o_item[" + i + "][tag_name]' placeholder='Enter tag code' required autocomplete='off' value='" + tag_code + "' style='width: 100px;' readonly /><input class='order_tag_id' type='hidden' name='o_item[" + i + "][tag_id]' placeholder='Enter tag Id' required value='" + tag_id + "' /></td>" +



			"<td style='display:none'><select class='form-control category' name='o_item[" + i + "][category]' id='category" + i + "' required='true' style='width: 100px;' value='" + cat_id + "'/><input type='hidden' class='id_category' name='o_item[" + i + "][id_category]' id='id_category" + i + "' required='true' value='" + cat_id + "'/><input type='hidden' class='id_metal' name='o_item[" + i + "][id_metal]' id='id_metal" + i + "' required='true' value='" + metal_type + "' /><input type='hidden' class='tax_group' name='o_item[" + i + "][tax_group]' id='tax_group" + i + "' required='true'/><input type='hidden' name='o_item[" + i + "][cgst]' class='o_cgst' /><input type='hidden' class='o_sgst' name='o_item[" + i + "][sgst]' /><input type='hidden' class='o_igst' name='o_item[" + i + "][igst]' /><input type='hidden' class='calculation_based_on' name='o_item[" + i + "][calculation_based_on]' value='" + calculation_based_on + "' /><input type='hidden' class='sales_mode' name='o_item[" + i + "][sales_mode]' value='" + sales_mode + "' /><input type='hidden' class='id_orderdetails' name='o_item[" + i + "][id_orderdetails]' value='" + id_orderdetails + "'/></td>" +



			//"<td><input type='text' class='form-control product' placeholder='Product' name='o_item["+i+"][product]' required id='prod_"+i+"' autocomplete='off' style='width: 100px;' value='"+product_name+"' /><input type='hidden' id='o_item_id_prod_"+i+"' name='o_item["+i+"][id_product]' class='id_product' required='true' value='"+product_id+"' /></td>"+



			"<td><select class='form-control product' name='o_item[" + i + "][product]' required id='prod_" + i + "' style='width: 150px;' value='" + product_name + "'><input type='hidden' id='o_item_id_prod_" + i + "' name='o_item[" + i + "][id_product]' class='id_product' required='true' value='" + product_id + "' /></td>" +



			//"<td><input type='text' class='form-control design' placeholder='Design' id='dsgn_"+i+"' name='o_item["+i+"][design]' required autocomplete='off' style='width: 100px;'  value='"+design_name+"' /><input type='hidden' id='o_item_id_dsgn_"+i+"' name='o_item["+i+"][id_design]' class='id_design'  value='"+design_id+"' /></td>"+



			"<td><select class='form-control design'  id='dsgn_" + i + "' name='o_item[" + i + "][design]' style='width: 150px;' value='" + design_name + "'><input type='hidden' id='o_item_id_dsgn_" + i + "' name='o_item[" + i + "][id_design]' class='id_design'  value='" + design_id + "' /></td>" +



			//"<td><input type='text' id='sub_design_"+i+"' name='o_item["+i+"][sub_design]' class='form-control sub_design' placeholder='Sub Design' style='width: 100px;'  value='"+sub_design_name+"' /><input type='hidden' class='id_sub_design' name='o_item["+i+"][id_sub_design]' id='o_item_sub_design_"+i+"' required autocomplete='off' value='"+sub_design_id+"' /></td>"+



			"<td><select class='form-control sub_design'  id='sub_design_" + i + "' name='o_item[" + i + "][sub_design]' style='width: 150px;'  value='" + sub_design_name + "'><input type='hidden' class='id_sub_design' name='o_item[" + i + "][id_sub_design]' id='o_item_sub_design_" + i + "' value='" + sub_design_id + "' /></td>" +



			"<td><select style='width: 100px;' class='form-control purity' name='o_item[" + i + "][purity]' id='purity" + i + "' required='true' /><input type='hidden' name='o_item[" + i + "][id_purity]' class='id_purity' id='id_purity" + i + "' required='true'/></td>" +



			"<td><input type='number' class='form-control weight' name='o_item[" + i + "][weight]' placeholder='Enter Gross Weight'  required id='weight_" + i + "' autocomplete='off' style='width: 75px;'  value='" + gross_wt + "' /></td>" +



			//"<td><input type='text' class='form-control less_wt' name='o_item["+i+"][less_wt]' placeholder='Enter Less Weight'  required id='less_wt_"+i+"' autocomplete='off' style='width: 75px;' value='"+less_wt+"' readonly /></td>"+



			'<td><div class="form-group"><div class="input-group"><input class="form-control less_wt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="o_item[' + i + '][less_wt]" step="any" value="' + (type == 'tag' ? parseFloat(totalStoneWt).toFixed(2) : tot_stone_wt) + '" readonly="" style="width:100px;"><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" name="o_item[' + i + '][stone_details]" value=' + JSON.stringify(stone_details) + '></div></div></td>' +



			"<td><input type='text' class='form-control net_wt' name='o_item[" + i + "][net_wt]' placeholder='Enter Net Weight'  required id='net_wt_" + i + "' autocomplete='off' style='width: 75px;' value='" + net_wt + "' readonly /></td>" +



			"<td><select  class='form-control size' placeholder='Size' name='o_item[" + i + "][size]' required='true' style='width: 100px;'/></td>" +



			"<td><input type='number' class='form-control qty' placeholder='Pcs' name='o_item[" + i + "][totalitems]' required='true' style='width: 70px;'  value='" + pcs + "' id='qty_" + i + "'/></td>" +



			"<td><input type='number' class='form-control wast_percent' placeholder='Wast %' name='o_item[" + i + "][wast_percent]' required='true' style='width: 70px;'  value='" + wast_perc + "' /></td>" +



			"<td><input type='number' class='form-control wast_wgt' placeholder='Wast Weight' name='o_item[" + i + "][wast_wgt]' required='true' style='width: 100px;'  value='" + wast_wt + "' /></td>" +



			"<td><select class='mc_type' class='form-control' name='o_item[" + i + "][id_mc_type]' style='width: 100px;'><option value='1'>Piece</option><option value='2' selected>Gram</option></select></td>" +



			"<td><input type='number' step='any' class='form-control mc' placeholder='Total MC' name='o_item[" + i + "][mc]' style='width: 100px;' value='" + tag_mc_value + "' /></td>" +



			"<td><a href='#' onClick='create_new_empty_est_cus_charges_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='charges_details' name='o_item[" + i + "][charges_details]' value=" + JSON.stringify(charges_details) + "></td>" +



			"<td><input style='width:100px' type='text' class='form-control value_charge' name='o_item[" + i + "][value_charge]' value='" + charge_value + "' readonly /></td>" +



			//"<td><a href='#' onClick='create_new_empty_est_cus_stone_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='stone_details' name='o_item["+i+"][stone_details]' value="+JSON.stringify(stone_details)+"></td>"+



			"<td><input type='number' step='any' class='form-control stn_amt' placeholder='Amount' name='o_item[" + i + "][stn_amt]' step='any' style='width: 100px;' readonly value='" + stone_price + "'/></td>" +



			"<td><input type='number' class='form-control order_rate' placeholder='Order Rate' name='o_item[" + i + "][order_rate]' value='" + order_rate + "' required='true'  style='width: 100px;'/></td>" +



			"<td><input type='text' class='form-control taxable' placeholder='Amount' name='o_item[" + i + "][taxable]' value='" + tax_amount + "' 	required='true' readonly='true' style='width: 100px;'/></td>" +



			"<td><input type='text' class='form-control order_amt' placeholder='Amount' name='o_item[" + i + "][rate]' value='" + amount + "' required='true' readonly='true' style='width: 100px;'/></td>" +



			'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden"  class="order_img" name=o_item[' + i + '][order_img]" value=' + JSON.stringify(pre_img_resource) + ' ></td>' +



			'<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" value="' + (ctrl_page[2] == 'edit' && id_orderdetails != '' ? description : '') + '" name="o_item[' + i + '][description]"></td>' +



			//"<td><input type='number' step='any' class='form-control due_date' placeholder='Due Days'  name='o_item["+i+"][due_date]' value="+(ctrl_page[2]=='edit' && id_orderdetails!='' ? days_difference :'' )+" step='any' style='width: 100px;'/></td>"+



			"<td><input type='text' class='form-control datemask date due_date' readonly placeholder='Due Date' data-date-format='dd-mm-yyyy' value='" + cus_due_date + "' name='o_item[" + i + "][due_date]' style='width: 100px;'/></td>" +



			"<td style='display:none;'><input class='form-control datemask date smith_due_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_due_date]' type='text' value='" + smith_due_date + "' required='true' placeholder='Smith Due Date' readonly style='width: 100px;'/></td>" +



			"<td style='display:none;'><input class='form-control datemask date smith_rem_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_remainder_date]' type='text' value='" + smith_rem_date + "' required='true' placeholder='Smith Reminder Date'   readonly style='width: 100px;'/>" +



			"</td>" +



			//'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



			'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>' + (type != 'tag' ? '<a href="#" onClick="copy_cus_order_row($(this).closest(\'tr\'))" class="btn btn-warning btn-del"><i class="fa fa-copy"></i></a>' : '') + '</td>'



		"</tr>";



		$('#item_detail tbody').append(html);







		$('.date').datepicker({ dateFormat: 'yyyy-mm-dd', })



		// Category



		$('#category' + i).append(



			$("<option></option>")



				.attr("value", "")



				.text('-Choose-')



		);



		$.each(CategorysArr, function (key, item) {



			$('#category' + i).append(



				$("<option></option>")



					.attr("value", item.id_ret_category)



					.text(item.name)



			);



		});



		if (product_id == 0) {



			$('#prod_' + i).append(



				$("<option></option>")



					.attr("value", "")



					.text('-Choose-')



			);



		}







		console.log('OrderProducts', OrderProducts);



		$.each(OrderProducts, function (key, item) {



			console.log((product_id > 0));



			if (product_id > 0) {



				if (product_id == item.pro_id) {



					$('#prod_' + i).append(



						$("<option></option>")



							.attr("value", item.pro_id)



							.text(item.product_name)



					);



				}



			}



			else {



				$('#prod_' + i).append(



					$("<option></option>")



						.attr("value", item.pro_id)



						.text(item.product_name)



				);



			}







		});



		if (type == 'edit') {







			$.each(data.purity, function (pkey, pitem) {



				$("#purity" + i).append(



					$("<option></option>").attr("value", pitem.id_purity).text(pitem.purity)



				);



			});







			$('#purity' + i).val(data.id_purity);



			$.each(data.products, function (pkey, pitem) {



				$('#prod_' + i).append(



					$("<option></option>").attr("value", pitem.pro_id).text(pitem.product_name)



				);



			});



			$('#prod_' + i).val(data.id_product);



		}



		$('#category' + i).val(cat_id);



		let row_lastObj = $('#item_detail tbody #detail' + i);







		if (cat_id > 0) {



			get_cat_purity(row_lastObj, cat_id, purity);



			var CatData = filterByCatId('id_ret_category', cat_id);



			if (CatData) {



				row_lastObj.find(".id_metal").val(CatData.id_metal);



				row_lastObj.find(".tax_group").val(CatData.tgrp_id);



				getTaxGroupDetail(row_lastObj, CatData.tgrp_id);



			}



			if (product_id > 0) {



				get_Activedesign(row_lastObj, product_id);



				get_product_size(product_id, row_lastObj, size);







			}



			if (tag_mc_type > 0) {



				row_lastObj.find(".mc_type").val(tag_mc_type);



			}



		}



		if (order_type == 2 || order_type == 6) {



			row_lastObj.find(".product").prop("readonly", false);



			row_lastObj.find(".design").prop("readonly", false);



			row_lastObj.find(".sub_design").prop("readonly", false);



			row_lastObj.find(".purity").prop("disabled", false);



			//row_lastObj.find(".weight").prop("readonly", false);



			row_lastObj.find(".size").prop("disabled", false);



			row_lastObj.find(".qty").prop("readonly", false);



			row_lastObj.find(".wast_percent").prop("readonly", false);







			row_lastObj.find(".wast_wgt").prop("readonly", false);



			//row_lastObj.find(".mc_type").prop("disabled", false);



			//row_lastObj.find(".mc").prop("readonly", false);



		} else if (order_type == 5) {



			row_lastObj.find(".product").prop("disabled", true);



			row_lastObj.find(".design").prop("disabled", true);



			row_lastObj.find(".sub_design").prop("disabled", true);



			row_lastObj.find(".purity").prop("disabled", true);



			//row_lastObj.find(".weight").prop("readonly", true);



			row_lastObj.find(".size").prop("disabled", true);



			row_lastObj.find(".qty").prop("readonly", true);



			row_lastObj.find(".wast_percent").prop("readonly", true);







			row_lastObj.find(".wast_wgt").prop("readonly", true);



			//row_lastObj.find(".mc_type").prop("disabled", true);



			//row_lastObj.find(".mc").prop("readonly", true);



		}



		$('#item_detail > tbody').find('.product').select2();



		$('#item_detail > tbody').find('.product').select2({



			placeholder: "product",



			allowClear: true



		});







		$('#item_detail > tbody').find('.design').select2();



		$('#item_detail > tbody').find('.design').select2({



			placeholder: "Design",



			allowClear: true



		});



		$('#item_detail > tbody').find('.sub_design').select2();



		$('#item_detail > tbody').find('.sub_design').select2({



			placeholder: "Sub design",



			allowClear: true



		});



		$('#item_detail > tbody').find('.mc_type').select2();



		$('#item_detail > tbody').find('.mc_type').select2({



			placeholder: "MC Type",



			allowClear: true



		});



		$('#item_detail > tbody').find('.category').focus();







		calculate_orderSale_value();



	}



}











function dia_remove(i, id = "") {



	var rowId = "d_detail" + i;



	$('#' + rowId + '').remove();



	if (id) {



		deleteProdDetail(id);



	}



}



function m_remove(i, id = "") {



	var rowId = "detail" + i;



	$('#' + rowId + '').remove();



	if (id) {



		deleteOrderItem(id);



	}



}



function deleteOrderItem(id) {



	my_Date = new Date();



	$("div.overlay").css("display", "block");



	$.ajax({



		url: base_url + "index.php/admin_ret_order/" + id + "?nocache=" + my_Date.getUTCSeconds(),



		type: "POST",



		success: function (data) {



			//	window.location.reload();



			$("div.overlay").css("display", "none");



		},



		error: function (error) {



			alert('error');



			$("div.overlay").css("display", "none");



		}



	});



}







function remove_img(file) {



	var id = $('#id_product').val();



	$("div.overlay").css("display", "block");



	$.ajax({



		url: base_url + "index.php/admin_catalog/remove_img/" + file + "/" + id,



		type: "POST",



		success: function (result) {



			$("div.overlay").css("display", "none");



			window.location.reload();



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



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











function getSearchProd(searchTxt, inputId, curRow) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_estimation/getProductBySearch/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': searchTxt }, //,'cat_id' : curRow.find(".category").val()



		success: function (data) {



			$(".product").autocomplete(



				{



					source: data,



					select: function (e, i) {



						e.preventDefault();



						$("#o_item_id_" + inputId).val(i.item.value);



						$("#" + inputId).val(i.item.label);



						curRow.find('.category').val(i.item.cat_id);



						curRow.find('.id_category').val(i.item.cat_id);



						curRow.find('.calculation_based_on').val(i.item.calculation_based_on);



						curRow.find('.sales_mode').val(i.item.sales_mode);



						get_product_size(i.item.value, curRow, 0);



						category_change(curRow, i.item.cat_id);



					},



					response: function (e, i) {



						// ui.content is the array that's about to be sent to the response callback.



						/*console.log(i);



						if (i.content.length === 0) {



						   $("#prodAlert").html('<p style="color:red">Enter a valid Product</p>');



						   $('#lt_product').val('');



						}else{



							$("#prodAlert").html('');



						} */



					},



					minLength: 0,



				});



		}



	});



}



function get_product_size(id_product, curRow, size = 0) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/get_product_size/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'id_product': id_product },



		success: function (data) {



			curRow.find(".size option").remove();



			$('.size').append(



				$("<option></option>")



					.attr("value", "")



					.text('-Choose-')



			);



			$.each(data, function (key, item) {



				curRow.find(".size").append(



					$("<option></option>")



						.attr("value", item.id_size)



						.text(item.value + ' ' + item.name)



				);



			});



			curRow.find(".size").select2({



				placeholder: "Select Size",



				allowClear: true



			});



			if (size > 0) {



				curRow.find(".size").select2("val", size);



			}



		}



	});



}



function getSearchDesign(searchTxt, inputId) {



	var str = inputId.split("_");  // Sample : prod_1 => split and pass the id



	var prod_id = $("#o_item_id_prod_" + str[1]).val();



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/get_ActiveDesingns/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': searchTxt, 'product_id': prod_id },



		success: function (data) {



			$(".design").autocomplete(



				{



					source: data,



					select: function (e, i) {



						e.preventDefault();



						$("#o_item_id_" + inputId).val(i.item.value);



						$("#" + inputId).val(i.item.label);



						// Design sizes



						$.ajax({



							url: base_url + 'index.php/admin_ret_catalog/get_Activesize/?nocache=' + my_Date.getUTCSeconds(),



							dataType: "json",



							method: "POST",



							data: { 'id_product': prod_id },



							success: function (data) {







							}



						});



						/*// Design purities



						$.ajax({



							url: base_url+'index.php/admin_ret_catalog/design/d_purities/?nocache=' + my_Date.getUTCSeconds(),



							dataType: "json",



							method: "POST",



							data: {'design_no': i.item.value},



							success: function (data) {







							}



						});*/



					},



					response: function (e, i) {



						// ui.content is the array that's about to be sent to the response callback.



						/*console.log(i);



						if (i.content.length === 0) {



						   $("#prodAlert").html('<p style="color:red">Enter a valid Design</p>');



						   $('#lt_product').val('');



						}else{



							$("#prodAlert").html('');



						} */



					},



					minLength: 0,



				});



		}



	});



}



function get_product_list() {



	my_Date = new Date();



	$("div.overlay").css("display", "block");



	$.ajax({



		url: base_url + "index.php/product/ajax?nocache=" + my_Date.getUTCSeconds(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			set_product_list(data);



			$("div.overlay").css("display", "none");



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



function set_product_list(data) {



	var product = data.product;



	var access = data.access;



	var oTable = $('#product_list').DataTable();



	$("#total_product").text(product.length);



	if (access.add == '0') {



		$('#add_product').attr('disabled', 'disabled');



	}



	oTable.clear().draw();



	if (product != null && product.length > 0) {



		oTable = $('#product_list').dataTable({



			"bDestroy": true,



			"bInfo": true,



			"bFilter": true,



			"bSort": true,



			"aaData": product,



			"order": [[0, "desc"]],



			"aoColumns": [{ "mDataProp": "id_product" },



			{ "mDataProp": "category_name" },



			{ "mDataProp": "subcategory_name" },



			{ "mDataProp": "name" },



			{

				"mDataProp": function (row, type, val, meta) {



					status_url = base_url + "index.php/product/status/" + (row.status == 1 ? 0 : 1) + "/" + row.id_product;



					return "<a href='" + status_url + "'><i class='fa " + (row.status == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.status == 1 ? 'green' : 'red') + "'></i></a>"



				}



			},



			{

				"mDataProp": function (row, type, val, meta) {



					id = row.id_product



					edit_url = (access.edit == '1' ? base_url + 'index.php/product/edit/' + id : '#');



					delete_url = (access.delete == '1' ? base_url + 'index.php/product/delete/' + id : '#');



					delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');



					action_content = '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i> Edit</a><a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>';



					return action_content;



				}



			}]



		});



	}



}



function searchEsti(searchTxt) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/estimation/getEstiBySearch/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': searchTxt },



		success: function (data) {



			$("#esti_no").autocomplete(



				{



					source: data,



					select: function (e, i) {



						e.preventDefault();



						$("#esti_no").val(i.item.value);



						my_Date = new Date();



						$.ajax({



							url: base_url + 'index.php/admin_ret_order/estimation/getEstiDetails/?nocache=' + my_Date.getUTCSeconds(),



							dataType: "json",



							method: "POST",



							data: { 'esti_id': i.item.value },



							success: function (data) {



								$("#item_detail tbody tr").empty();



								$("#aw_detail tbody tr").empty();







								$("#i_increment").val(0);



								var a = 0;



								$.each(data.esti_items, function (key, item) {



									var html_1 = "";



									var i = ++a;



									$("#i_increment").val(i);







									html_1 = "<tr id='detail" + i + "'><td>" + i + "</td><td><select style='width:100%;' name='o_item[" + i + "][orter_type]' id='" + i + "' class='form-control order_type'><option value='1' " + (item.item_type == 1 ? 'selected' : '') + ">Catalog order</option><option value='2'  " + (item.item_type == 2 ? 'selected' : '') + ">Customer order</option><option value='3'  " + (item.item_type == 3 ? 'selected' : '') + ">Repair order</option><option value='4'  " + (item.item_type == 4 ? 'selected' : '') + ">Catalog Admin order</option></select></td><td><input type='text' class='form-control product' placeholder='Product Name' name='o_item[" + i + "][product]' required='true' value='" + item.product_name + "' id='prod_" + i + "'/><input value='" + item.product_id + "' type='hidden' id='o_item_id_prod_" + i + "' name='o_item[" + i + "][id_product]' required='true'/></td>" + "<td><input type='text' class='form-control design' placeholder='Design Name' value='" + item.itemname + "' id='dsgn_" + i + "' name='o_item[" + i + "][design]' " + (item.item_type != 2 ? 'required' : '') + "/><input type='hidden' id='o_item_id_dsgn_" + i + "' value='" + item.design_no + "' name='o_item[" + i + "][design_no]' /></td>" + "<td><input value='" + item.net_wt + "''  type='text' class='form-control' placeholder='Weight' name='o_item[" + i + "][weight]' required='true'/></td>" + "<td><input value='" + item.piece + "' type='text' class='form-control' placeholder='Pcs' name='o_item[" + i + "][totalitems]' required='true'/></td>" + "<td><input type='text' class='form-control'  value='" + item.size + "'  placeholder='Size' name='o_item[" + i + "][size]' required='true'/></td>" + "<td><input type='text' class='form-control purity1' name='o_item[" + i + "][purity]' id='purity" + i + "' value='" + item.purity + "' required='true'/><input value='" + item.id_purity + "' type='hidden' name='o_item[" + i + "][id_purity]' id='id_purity" + i + "' required='true'/> </td>" + "<td><input  value='" + item.item_cost + "'  type='text' class='form-control' placeholder='Amount' name='o_item[" + i + "][rate]' required='true' readonly='true'/></td><td><input class='form-control datemask date' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_remainder_date]' type='text' required='true' placeholder='Smith Remainder Date'   readonly /></td><td><input class='form-control datemask date' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_due_date]' type='text' required='true' placeholder='Smith Due Date'   readonly /></td><td><input class='form-control datemask date' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][cus_due_date]' type='text' required='true' placeholder='Cus Due Date' readonly /></td>" + "<td><button type='button' class='btn btn-danger' onclick='m_remove(" + i + ")'><i class='fa fa-trash'></i></button></td>";



									$('#item_detail tbody').append(html_1);



									$('.date').datepicker({ dateFormat: 'yyyy-mm-dd', })



								});







								// ADVANCE WEIGHT



								var tot_amt = 0;



								var html_3 = "";



								var a = 0;



								$.each(data.esti_old_gold, function (key, item) {



									var html_2 = "";



									$("#aw_increment").val(0);



									var i = ++a;



									$("#aw_increment").val(i);



									html_2 += "<tr id='detail" + i + "'><td>" + i + "</td><td>" + item.category + "</td><td>" + item.purpose + "</td><td>" + item.gross_wt + "</td><td>" + item.stone_wt + "</td><td>" + item.dust_wt + "</td><td>" + item.net_wt + "</td><td>" + item.wastage_percent + "</td><td>" + item.wastage_wt + "</td><td>" + item.rate_per_gram + "</td><td>" + item.amount + "</td></tr>";



									$('#aw_detail tbody').append(html_2);



									tot_amt = parseFloat(tot_amt) + parseFloat(item.amount);



								})



								if (tot_amt > 0) {



									html_3 += "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>Total Amt : </td><td>" + tot_amt + "</td></tr>";



									$('#aw_detail tbody').append(html_3);



								}



								// EST DETAIL



								$('#est_date').text(data.esti.esti_date);



								$('#tot_cost').text(data.esti.total_cost);



								$('#g_voucher').text(data.esti.gift_voucher_amt);



								$('#disc').text(data.esti.discount);



								$('#cus').text(data.esti.customer);



								$('#mobile').text(data.esti.mobile);



								$('#id_customer').val(data.esti.id_customer);



							}



						});







					},



					response: function (e, i) {



						// ui.content is the array that's about to be sent to the response callback.



						console.log(i);



						/*if (i.content.length === 0) {



						   $("#subprodAlert").html('<p style="color:red">Enter a valid Sub Product</p>');



						   //$('#lt_product').val('');



						}else{



							$("#subprodAlert").html('');



						}*/



					},



					minLength: 0,



				});



		}



	});



}



$('#cus_order_search').on('click', function () {



	setOrderList();



});



function setOrderList() {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/order?nocache=" + my_Date.getUTCSeconds(),



		data: ({ 'from_date': $('#rpt_payments1').html(), 'to_date': $('#rpt_payments2').html(), 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val()) }),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			var order = data.orders;



			var access = data.access;



			var profile = data.profile;





			$('#total_count').text(order.length);







			var oTable = $('#order_list').DataTable();



			oTable.clear().draw();







			if (order != null && order.length > 0) {



				oTable = $('#order_list').dataTable({



					"bDestroy": true,



					"bInfo": true,



					"bFilter": true,



					"scrollX": '100%',



					"bSort": true,



					"dom": 'lBfrtip',



					"order": [[0, "desc"]],



					"buttons": ['excel', 'print'],



					"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },



					"aaData": order,



					"aoColumns": [{ "mDataProp": "id_customerorder" },



					{ "mDataProp": "order_no" },



					{ "mDataProp": "order_for" },



					{ "mDataProp": "ordertype_name" },



					{ "mDataProp": "order_to" },



					//										{ "mDataProp": "est_no" },



					{ "mDataProp": "order_date" },



					{ "mDataProp": "order_items" },



					{ "mDataProp": "tag_code" },



					{



						"mDataProp": null,



						"sClass": "control center",



						"sDefaultContent": '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'



					},



					{

						"mDataProp": function (row, type, val, meta) {



							id = row.id_customerorder;







							edit_url = (access.edit == '1' ? base_url + 'index.php/admin_ret_order/order/edit/' + id : '#');



							delete_url = (access.delete == '1' ? base_url + 'index.php/admin_ret_order/order/delete/' + id : '#');



							detailed_url = base_url + "index.php/admin_ret_order/customer_order_acknowladgement/" + id;



							delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');



							action_content =  '<a href="' + detailed_url + '" target="_blank" class="btn btn-primary btn-print" data-toggle="tooltip" title="Detailed Print"><i class="fa fa-print" ></i></a>' +(access.edit==1 ? '<a style="'+(row.order_status == '4' ? 'pointer-events: none;opacity: 0.6;' : "" )  +'" href=' + edit_url + ' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '><i class="fa fa-edit" ></i></a>' :'')  + (profile.allow_order_cancel == 1 && row.order_status <= 4 && access.delete == '1' ? '<button class="btn btn-warning" onclick="confirm_order_cancel(' + id + ',' + profile.order_cancel_otp + ')"><i class="fa fa-close" ></i></button>' : '')



							return action_content;



						}



	

	



					},





					],





					

					// $.each(order, function (key, item) {



					

					// 	if (item.order_status == 4) {

					// 		$('#edit').prop('disabled', true);  

					// 	  } 

					// 	  else {

					// 		$('#edit').prop('disabled', false); 

					// 	  }

				});

		







				var anOpen = [];



				$(document).on('click', "#order_list .control", function () {



					var nTr = this.parentNode;



					var i = $.inArray(nTr, anOpen);







					if (i === -1) {



						$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');



						oTable.fnOpen(nTr, fnFormatRowDetails(oTable, nTr), 'details');



						anOpen.push(nTr);



					}



					else {



						$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');



						oTable.fnClose(nTr);



						anOpen.splice(i, 1);



					}



				});







			}



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});





	// $.each(order, function (key, item) {

	// 	if (item.order_status == 4) {

	// 	  $('#edit').prop('disabled', true);  

	// 	} else {

	// 	  $('#edit').prop('disabled', false); 

	// 	}

	//   });

	  

		

			





}







function fnFormatRowDetails(oTable, nTr) {



	var oData = oTable.fnGetData(nTr);



	var rowDetail = '';



	var order_details = oData.order_details;



	var profile = oData.profile;





	var prodTable =



		'<div class="innerDetails">' +



		'<table class="table table-responsive table-bordered text-center table-sm">' +



		'<tr class="bg-teal">' +



		'<th>S.No</th>' +



		'<th>Image</th>' +



		'<th>Product Name</th>' +



		'<th>Design</th>' +



		'<th>Sub Design</th>' +



		'<th>Size</th>' +



		'<th>Pcs</th>' +



		'<th>Gwt</th>' +



		'<th>Less Wt</th>' +



		'<th>Net Wt</th>' +



		'<th>Stone Value</th>' +



		'<th>MC value</th>' +



		'<th>Wastage %</th>' +



		'<th>Remarks </th>' +



		'<th>Customer due date</th>' +



		'<th>Status</th>' +



		'<th>Action</th>' +



		'</tr>';







	var total_amount = 0;



	$.each(order_details, function (idx, val) {



		if (val.image_details[0] != '' && val.image_details[0] != null) {







			img_src = base_url + 'assets/img/orders/' + val.image_details[0].image;



		}



		else {



			img_src = base_url + 'assets/img/no_image.png';



		}



		prodTable +=



			'<tr class="prod_det_btn">' +



			'<td>' + val.id_orderdetails + '</td>' +



			'<td>' + '<img src=' + img_src + ' width="50" height="55"><br><a  class="btn btn-secondary order_img"  id="edit" data-toggle="modal" data-id=' + val.id_orderdetails + '><i class="fa fa-eye" ></i></a>' + '</td>' +



			'<td>' + val.product_name + '</td>' +



			'<td>' + val.design_name + '</td>' +



			'<td>' + val.sub_design_name + '</td>' +



			'<td>' + val.size + '</td>' +



			'<td>' + val.totalitems + '</td>' +



			'<td>' + val.weight + '</td>' +



			'<td>' + val.less_wt + '</td>' +



			'<td>' + val.net_wt + '</td>' +



			'<td>' + val.stn_amt + '</td>' +



			'<td>' + val.mc + '</td>' +



			'<td>' + val.wast_percent + '</td>' +



			'<td>' + val.description + '</td>' +



			'<td>' + val.cus_duedate + '</td>' +



			'<td>' + val.status + '</td>' +



			'<td>' + (val.orderstatus <= 4 && profile.allow_order_cancel == 1 ? '<button class="btn btn-danger" onclick="confirm_order_item_cancel(' + val.id_orderdetails + ')"><i class="fa fa-close" ></i></button>' : (val.reject_reason != '' ? val.reject_reason : '')) + '</td>' +



			'</tr>';



	});



	rowDetail = prodTable + '</table></div>';



	return rowDetail;



}







$(document).on('click', "#order_list a.order_img", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	view_dup_tag_history_imgs(id);



});



function view_dup_tag_history_imgs(order_id_img1) {



	update_tag_img_id1 = order_id_img1;



	data = [];



	var tag_codeimage1 = base_url + 'assets/img/orders';



	$('#imageModal_bulk_edit').modal('show');



	$(".overlay").css('display', "none");



	$.ajax({



		data: ({ 'order_id': order_id_img1 }),



		url: base_url + "index.php/admin_ret_order/get_img_by_order_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			retrive_img = data;



			for (i = 0; i < data.length; i++) {



				img_src = data[i].image;



				var preview = $('#order_images');



				var img = tag_codeimage1 + '/' + img_src;



				if (img_src) {



					div = document.createElement("div");



					div.setAttribute('class', 'col-md-3 images');



					div.setAttribute('id', 'order_img_edit_' + [i]);







					$('.images').css('margin-right', '25px');



					key = [i];



					param = img_src;







					div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><img class='thumbnail' src='" + img + "'" + "style='width: 300px;height: 250px;'/><a href=" + img + " download=" + img_src + "><i class='fa fa-download btn btn-success'>Download</i></div></div></a>";



					preview.append(div);







				}



			}







		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



$("#imageModal_bulk_edit").on("hidden.bs.modal", function () {



	$('#order_images').empty();



});



function confirm_order_cancel(order_id, order_cancel_otp) {



	$('#order_id').val(order_id);



	$('#confirm-ordercancell').modal('show');



	if (order_cancel_otp == 1) {

		$('.cancel_otp').css("display", "none");

		$('.order_remarks').css("display", "none");

		$('.cancel_otp_confirmation').css("display", "block");

		$('.verify_otp').css("display", "none");





	} else {

		$('.cancel_otp').css("display", "none");

		$('.order_remarks').css("display", "block");

		$('.cancel_otp_confirmation').css("display", "none");



	}



}



function confirm_order_item_cancel(order_id) {



	$('#id_orderdetails').val(order_id);

	$('#order_id').val('');



	$('#confirm-ordercancell').modal('show');



}





function confirm_repair_order_cancel(order_id) {

	$('#id_orderdetails').val('');

	$('#order_id').val(order_id);

	$('#confirm-ordercancell').modal('show');

}



$('#order_cancel_remark').on('keypress', function () {



	if (this.value.length > 6) {



		$('#cancell_delete').prop('disabled', false);



	} else {



		$('#cancell_delete').prop('disabled', true);



	}



});



$('#cancell_delete').on('click', function () {



	$('#cancell_delete').prop('disabled', true);



	if ($('#order_id').val() != '') {



		var baseurl = base_url + "index.php/admin_ret_order/order/ajax_order_cancel?nocache=" + my_Date.getUTCSeconds();



	}



	else if ($('#id_orderdetails').val() != '') {



		var baseurl = base_url + "index.php/admin_ret_order/order/cancel_order_item?nocache=" + my_Date.getUTCSeconds();



	}



	my_Date = new Date();



	$.ajax({



		type: 'POST',



		url: baseurl,



		dataType: 'json',



		data: { 'remarks': $('#order_cancel_remark').val(), 'order_id': $('#order_id').val(), 'id_orderdetails': $('#id_orderdetails').val() },



		success: function (data) {



			if (data.status) {



				$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });



				window.location.reload();



			} else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



				window.location.reload();



			}







		}



	});



});



function setEstiData(id) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/order/estiData/' + id + '?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		success: function (data) {



			$("#item_detail tbody tr").empty();



			$("#aw_detail tbody tr").empty();



			$("#i_increment").val(0);



			var a = 0;



			$.each(data.order_det, function (key, item) {



				var html_1 = "";



				var i = ++a;



				$("#i_increment").val(i);



				var id_orderdetails = item.id_orderdetails;







				var stone_details = [];



				var other_charge_details = [];



				$.each(item.stone_details, function (skey, sitem) {



					stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });



				});







				$.each(item.other_charge_details, function (ckey, citem) {



					other_charge_details.push({ 'value_charge': citem.charge_value, 'id_charge': citem.charge_id });







				});











				html_1 += "<tr id='detail" + i + "' class='" + i + "'>" +







					"<td><input class='weight' type='hidden'  name='o_item[" + i + "][id_orderdetails]' required='true' value='" + item.id_orderdetails + "' id='id_orderdetails" + i + "'/><select class='category form-control' placeholder='category Name' name='o_item[" + i + "][category]' required='true' value='" + item.id_ret_category + "' id='category" + i + "' style=width:150px/></td>" +



					"<td><select class='purity form-control' placeholder='purity Name' name='o_item[" + i + "][purity]' required='true' value='" + item.purity + "' id='purity" + i + "' style=width:70px/></td>" +



					"<td><select class='product form-control' placeholder='product Name' name='o_item[" + i + "][product]' required='true' value='" + item.product + "' id='product" + i + "' style=width:150px/></td>" +



					"<td><select class='design form-control' placeholder='design Name' name='o_item[" + i + "][design]' required='true' value='" + item.design + "' id='design" + i + "'  style=width:150px/></td>" +



					"<td><select class='sub_design form-control' placeholder='sub_design Name' name='o_item[" + i + "][sub_design]' required='true' value='" + item.sub_design + "' id='sub_design" + i + "' style=width:150px/></td>" +



					"<td><input  class='form-control weight' placeholder='weight Name' name='o_item[" + i + "][weight]' required='true' value='" + item.weight + "' id='weight" + i + "' style='width:70px'/></td>" +



					"<td><select class='size form-control' placeholder='size ' name='o_item[" + i + "][size]' required='true' value='" + item.size + "' id='size" + i + "'/style=width:70px></td>" +



					"<td><input class='pieces form-control' placeholder='piecs ' name='o_item[" + i + "][piecs]' required='true' value='" + item.piecs + "' id='piecs" + i + "' style='width:70px'/></td>" +



					'<td><input class="cus_due_date form-control" placeholder="Due Days" style=width:70px name="o_item[' + i + '][cus_due_date]" required="true" value=' + item.cus_due_date + ' id="cus_due_date"+i+" /></td>' +



					"<td><input class='form-control wast_percent' placeholder='wastages ' name='o_item[" + i + "][wastage]' required='true' value='" + item.wastage + "' id='wastage" + i + "' style='width:70px'/></td>" +



					"<td><input type='number' class='form-control wast_wgt' placeholder='Wast Weight' name='o_item[" + i + "][wast_wgt]' required='true' style='width: 100px;' readonly/></td>" +



					"<td><select class='mc_type' class='form-control' name='o_item[" + i + "][id_mc_type]' style='width: 100px;'><option value='1' " + (item.mc_type == 1 ? 'selected' : '') + ">Gram</option><option value='2' " + (item.mc_type == 2 ? 'selected' : '') + ">Piece</option></select></td>" +







					"<td><input class='mc form-control' placeholder='mc ' name='o_item[" + i + "][mc]' required='true' value='" + item.mc + "' id='mc" + i + "' style='width:70px'/></td>" +









					"<td><a href='#' onClick='create_new_empty_est_cus_charges_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='charges_details' name='o_item[" + i + "][charges_details]' value=" + JSON.stringify(other_charge_details) + "></td>" +



					"<td><input type='number' step='any' class='form-control value_charge' placeholder='Amount' name='o_item[" + i + "][value_charge]' step='any' value='" + item.charge_value + "' id='value_charge" + i + "' style='width: 100px;'style='width:70px' readonly/></td>" +



					"<td><a href='#' onClick='create_new_empty_est_cus_stone_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='stone_details' name='o_item[" + i + "][stone_details]' value=" + JSON.stringify(stone_details) + "></td>" +



					"<td><input type='number' step='any' class='form-control stn_amt' placeholder='Amount' name='o_item[" + i + "][stn_amt]' step='any' value='" + item.stn_amt + "' id='stn_amt" + i + "' style='width: 100px;'style='width:70px' readonly/></td>" +



					"<td><input class='rate form-control' placeholder='rate ' name='o_item[" + i + "][rate]' required='true' value='" + item.rate + "' id='rate" + i + "'readonly style='width:70px'/></td>" +



					'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" name="o_item[' + i + '][order_img]""></td>' +



					'<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" name="o_item[' + i + '][description]"></td>' +



					'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



				"</tr>";



				"</tr>";







				$('#item_detail tbody').append(html_1);



				$.each(data.categories, function (key, cate) {



					$('#category' + i).append(



						$("<option></option>").attr("value", cate.id_ret_category).text(cate.name)



					);







				});



				$('#category' + i).val(item.id_ret_category);



				data.products.filter(product => product.cat_id == item.id_ret_category).forEach((productDet) => {



					$('#product' + i).append(



						$("<option></option>").attr("value", productDet.pro_id).text(productDet.product_name)



					);



				});



				$('#product' + i).val(item.id_product);



				item.sizes.forEach(sizeDet => {



					$("#size" + i).append(



						$("<option></option>").attr("value", sizeDet.id_size).text(sizeDet.value + '-' + sizeDet.name)



					);



				});



				$('#size' + i).val(item.id_size)



				data.designs.filter(design => design.pro_id == item.id_product).forEach((designDet) => {



					$('#design' + i).append(



						$("<option></option>").attr("value", designDet.design_no).text(designDet.design_name)



					);



				});



				$('#design' + i).val(item.design_no);



				data.subdesigns.filter(sub_design => sub_design.id_design == item.design_no).forEach((subdesignDet) => {



					$('#sub_design' + i).append(



						$("<option></option>").attr("value", subdesignDet.id_sub_design).text(subdesignDet.sub_design_name)



					);



				});



				$('#sub_design' + i).val(item.id_sub_design);



				item.purity.forEach(purityDet => {



					$("#purity" + i).append(



						$("<option></option>").attr("value", purityDet.id_purity).text(purityDet.purity)



					);



				});







				$('#purity' + i).val(item.id_purity);



				$('#item_detail > tbody').find('.category').select2();



				$('#item_detail > tbody').find('.category').select2({



					placeholder: "category",



					allowClear: true



				});



				$('#item_detail > tbody').find('.product').select2();



				$('#item_detail > tbody').find('.product').select2({



					placeholder: "product",



					allowClear: true



				});



				$('#item_detail > tbody').find('.purity').select2();



				$('#item_detail > tbody').find('.purity').select2({



					placeholder: "purity",



					allowClear: true



				});



				$('#item_detail > tbody').find('.design').select2();



				$('#item_detail > tbody').find('.design').select2({



					placeholder: "design",



					allowClear: true



				});



				$('#item_detail > tbody').find('.sub_design').select2();



				$('#item_detail > tbody').find('.sub_design').select2({



					placeholder: "sub_design",



					allowClear: true



				});







				$('#item_detail > tbody').find('.size').select2();



				$('#item_detail > tbody').find('.size').select2({



					placeholder: "size",



					allowClear: true



				});







				$('#item_detail > tbody').find('.mc_type').select2();



				$('#item_detail > tbody').find('.mc_type').select2({



					placeholder: "MC Type",



					allowClear: true



				});







				$('#item_detail > tbody').find('.category').focus();



			});



			calculate_orderSale_value();







		}



	});



}



function get_all_karigar() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_catalog/karigar/active_list',



		dataType: 'json',



		success: function (data) {



			if ($('#repair_assign_karigar').length == 0) {



				var id = $("#karigar").val();



				var filter_karigar = $("#filter_karigar").val();



				$.each(data, function (key, item) {



					$("#karigar_sel,#karigar_filter").append(



						$("<option></option>")



							.attr("value", item.id_karigar)



							.text(item.karigar)



					);



				});



				$("#karigar_sel").select2(



					{



						placeholder: "Assign To Karigar",



						closeOnSelect: true



					});



				$("#karigar_filter").select2(



					{



						placeholder: "Karigar Filter",



						closeOnSelect: true



					});



				if ($('#karigar_sel').length) {



					$("#karigar_sel").select2("val", (id != '' && id > 0 ? id : ''));



				}







				if ($('#karigar_filter').length) {



					$("#karigar_filter").select2("val", (filter_karigar != '' && filter_karigar > 0 ? filter_karigar : ''));



				}











				$(".overlay").css("display", "none");







			}



			if ($('#repair_assign_karigar').length > 0) {



				$.each(data, function (key, item) {



					$("#repair_assign_karigar").append(



						$("<option></option>")



							.attr("value", item.id_karigar)



							.text(item.karigar)



					);



				});



				$("#repair_assign_karigar").select2(



					{



						placeholder: "Select Karigar",



						closeOnSelect: true



					});



				$("#repair_assign_karigar").select2("val", '');



			}







		}



	});



}



function get_all_employee() {



	$('#issue_employee option').remove();



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_estimation/get_employee?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		data: { 'id_branch': $('#branch_select').val() },



		type: "POST",



		dataType: "JSON",



		success: function (data) {



			emp_details = data;



			var id_employee = $('#id_employee').val();



			$.each(data, function (key, item) {



				$("#issue_employee,#employee_sel").append(



					$("<option></option>")



						.attr("value", item.id_employee)



						.text(item.emp_name)



				);



			});



			$("#issue_employee,#employee_sel").select2({



				placeholder: "Select Employee",



				allowClear: true



			});



			//$("#issue_employee").select2("val",(id_employee!='' && id_employee>0?id_employee:''));



			if (id_employee != '') {

				$("#employee_sel").select2("val", (id_employee != '' && id_employee > 0 ? id_employee : ''));

			}



			$(".overlay").css("display", "none");



		},



		error: function (error) {



		}



	});



}



$('#issue_employee').on('change', function () {



	if (this.value != '') {



		$('#id_employee').val(this.value);



	}



	else {



		$('#id_employee').val('');



	}



});



function get_all_master_data() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_order/active_cat_product_list',



		dataType: 'json',



		success: function (data) {



			console.log(data);



			cat_product_details = data;



		}



	});



}



$('#karigar_sel').on('change', function (e) {



	if (this.value != '') {



		$('#karigar').val(this.value);



	}



	else {



		$('#karigar').val('');



	}



});



$('#karigar_filter').on('change', function (e) {



	if (this.value != '') {



		$('#filter_karigar').val(this.value);



		var from_date = $('#new_list1').text();



		var to_date = $('#new_list2').text();



		var id_branch = $('#filter_branch').val();



		// get_new_orderlist(from_date, to_date, id_branch, this.value);



	}



	else {



		$('#filter_karigar').val('');



	}



});



$('#branch_filter').on('change', function (e) {



	if (this.value != '') {



		$('#filter_branch').val(this.value);



		var from_date = $('#new_list1').text();



		var to_date = $('#new_list2').text();



		var id_karigar = $('#filter_karigar').val();



		// get_new_orderlist(from_date, to_date, this.value, id_karigar);



	}



	else {



		$('#filter_branch').val('');



	}



});



$('#employee_sel,#repair_type,#work_type').on('change', function (e) {



	var from_date = $('#new_list1').text();



	var to_date = $('#new_list2').text();



	var id_karigar = $('#filter_karigar').val();



	var id_branch = $('#filter_branch').val();



	// get_new_orderlist(from_date, to_date, id_branch, id_karigar);

})



$('#select_all').click(function (event) {



	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



	event.stopPropagation();



});



$('#search_new_order_list').click(function () {

	var from_date = $('#new_list1').text();



	var to_date = $('#new_list2').text();

	get_new_orderlist(from_date, to_date);

});

/*

function get_new_orderlist(from_date, to_date, id_branch, id_karigar) {





	if (ctrl_page[1] == 'customer_neworders') // customer order



	{



		var order_type = 2;



	}



	else if (ctrl_page[1] == 'neworders')  // repair order



	{



		var order_type = '3,4';



	}



	else if (ctrl_page[1] == 'repair_order') {



		var order_type = 3;



	}









	var id_branch = ($('#filter_branch').val() != '' && $('#filter_branch').val() != null ? $('#filter_branch').val() : $('#id_branch').val())



	$(".overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/ajax_get_neworder?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: ({ 'from_date': from_date, 'to_date': to_date, 'id_branch': $('#filter_branch').val(), 'id_karigar': $('filter_karigar').val(), 'order_type': order_type, 'id_employee': $('#issue_employee').val(), 'repair_type': $('#repair_type').val(), 'work_type': $('#work_type').val(), 'id_counter': $('#counter_sel').val() }),



		success: function (data) {



			set_new_orderlist(data);



			$(".overlay").css("display", "none");



		}



	});



}

*/

function get_new_orderlist(from_date, to_date, id_branch, id_karigar) {



	if (ctrl_page[1] == 'customer_neworders') // customer order



	{



		var order_type = 2;



	}



	else if (ctrl_page[1] == 'neworders')  // repair order



	{



		var order_type = '3,4';



	}



	var id_branch = ($('#filter_branch').val() != '' && $('#filter_branch').val() != null ? $('#filter_branch').val() : $('#id_branch').val())



	$(".overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/ajax_get_neworder?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: ({ 'from_date': from_date, 'to_date': to_date, 'id_branch': id_branch, 'id_karigar': $('filter_karigar').val(), 'order_type': order_type, 'id_employee': $('#issue_employee').val(), 'repair_type': $('#repair_type').val(), 'work_type': $('#work_type').val(), 'id_counter': $('#counter_sel').val() }),



		success: function (data) {



			if (ctrl_page[1] == 'customer_neworders') // customer order



			{



				set_new_orderlist(data);



			}



			else if (ctrl_page[1] == 'neworders')  // repair order



			{



				set_repair_orderlist(data);



			}







			$(".overlay").css("display", "none");



		}



	});



}



function getCurrentDate() {

	var today = new Date();

	var dd = String(today.getDate()).padStart(2, '0');

	var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!

	var yyyy = today.getFullYear();

	return dd + '-' + mm + '-' + yyyy;

}

function set_new_orderlist(order) {



	var oTable = $('#neworder_list').DataTable();



	oTable.clear().draw();



	var company_name = $('#company_name').val();

	var from_date = $("#new_list1").html();

	var to_date = $("#new_list2").html();

	var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());

	var report_name = "Repair Order Karigar Allotment"

	var optional = '';



	var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"

		+ "<span>" + report_print(branch_name, report_name, from_date, to_date, optional) + "</span>";





	$("div.overlay").css("display", "block");



	if (order.length > 0) {



		data = [



			{

				"mDataProp": function (row, type, val, meta) {



					if (row.cus_ord_status <= 3 && (row.id_karigar == '' || row.id_karigar == null)) {



						chekbox = '<input type="checkbox" class="id_orderdetails" name="id_orderdetails[]" value="' + row.id_orderdetails + '"/> '



						return chekbox + " " + row.id_orderdetails + '<input type="hidden" class="id_product" value="' + row.id_product + '"><input type="hidden" class="id_category" value="' + row.id_category + '"><input type="hidden" class="product_name" value="' + row.product_name + '">';



					} else {



						return row.id_orderdetails + '<input type="hidden" class="id_product" value="' + row.id_product + '"><input type="hidden" class="id_category" value="' + row.id_category + '"><input type="hidden" class="product_name" value="' + row.product_name + '">';



					}







				}

			},



			{

				"mDataProp": function (row, type, val, meta) {



					id = row.id_orderdetails;



					edit_target = ("#imageModal_new");



					content = '<a href="#" class="btn btn-success btn-sm" id = "img_upload_order" value=0 data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-plus"></i></a>';



					return content;



				}

			},



			{

				"mDataProp": function (row, type, val, meta) {



					id = row.id_orderdetails;



					edit_target = ("#order_des_new");



					content = '<a href="#" class="btn btn-default btn-sm" value=0 data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-plus"></i></a>';



					return content;



				}

			},



			{ "mDataProp": "orderno" },



			{ "mDataProp": "pur_no" },



			{ "mDataProp": "karigar_name" },



			{

				"mDataProp": function (row, type, val, meta) {



					var defaultDate = row.smith_due_date ? row.smith_due_date : '';



					if (row.cus_ord_status <= 3) {



						return '<input class="form-control smith_due_dt" data-date-format="dd-mm-yyyy" name="o_item["+i+"][smith_due_date]" value="' + defaultDate + '" type="text" placeholder="Smith Due Date" style="width: 100px;"/><input type="hidden" class="order_date" value="' + row.order_date + '"><input type="hidden" class="cus_ord_status" value="' + row.cus_ord_status + '">';



					} else {



						return row.defaultDate;



					}



				}



			},



			{ "mDataProp": "orter_type" },



			{ "mDataProp": "cus_name" },



			{ "mDataProp": "cus_mobile" },



			{ "mDataProp": "emp_name" },



			{ "mDataProp": "product_name" },



			{ "mDataProp": "design_name" },



			{ "mDataProp": "sub_design_name" },



			{ "mDataProp": "totalitems" },



			{ "mDataProp": "weight" },



			{ "mDataProp": "tag_code" },



			{ "mDataProp": "repair_type" },



			{ "mDataProp": "work_at" },



			{ "mDataProp": "size" },



			{

				"mDataProp": function (row, type, val, meta) { return "<span class='label bg-" + row.color + "'>" + row.orderstatus + "</span>"; }







			},



			{ "mDataProp": "customer_ref_no" },



			{

				"mDataProp": "order_date",

				"render": function (data, type, row) {

					return '<div style="width: 80px;">' + data + '</div>';

				}

			},

			{

				"mDataProp": "cus_due_date",

				"render": function (data, type, row) {

					return '<div style="width: 80px;">' + data + '</div>';

				}

			},





			{ "mDataProp": "branch_name" },



			{

				"mDataProp": function (row, type, val, meta) {





					if (row.cus_ord_status == 3) {

						return '<input class="form-control kar_remark" name="o_item["+i+"][karigar_remarks]" value="" type="text" placeholder="Karigar change Reason" style="width: 200px;"/>';

					} else {

						return '-';

					}





				}



			},

			{ "mDataProp": "kar_remark" },











			{

				"mDataProp": function (row, type, val, meta) {



					id = row.id_orderdetails;



					view_confirm = '#confirm-view';



					action_content = '<a  class="btn btn-primary btn-edit" id="edit" data-toggle="modal" data-id=' + id + ' ><label>View Image</label></a>'



					return action_content;



				}



			}





		];



		if ($('#branch_user').val() == 1) {

			data.splice(4, 1);

		}



		oTable = $('#neworder_list').dataTable({



			"bDestroy": true,



			"bInfo": true,



			"bFilter": true,



			"scrollX": '100%',



			"bSort": false,



			"dom": 'lBfrtip',



			"buttons": ['excel', 'print'],



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

					title: company_name + '-' + (from_date && to_date != '' ? from_date + '- ' + to_date : '') + ' - ',

				},





			],





			"aaData": order,



			"aoColumns": data,



			"footerCallback": function (row, data, start, end, display) {

				if (order.length > 0) {

					var api = this.api(), data;

					for (var i = 0; i <= data.length - 1; i++) {

						var intVal = function (i) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '') * 1 :

								typeof i === 'number' ?

									i : 0;

						};

						$(api.column(11).footer()).html('Total');

						piece = api

							.column(14)

							.data()

							.reduce(function (a, b) {

								return intVal(a) + intVal(b);

							}, 0);

						$(api.column(14).footer()).html(formatNumber(piece, 0));

						gross_wt = api

							.column(15)

							.data()

							.reduce(function (a, b) {

								return intVal(a) + intVal(b);

							}, 0);

						$(api.column(15).footer()).html(formatNumber(gross_wt));



					}

				} else {

					var api = this.api(), data;



					$(api.column(12).footer()).html('');

					$(api.column(13).footer()).html('');



				}

			}



		});



	}



	// $('.smith_due_dt').datepicker({ dateFormat: 'yyyy-mm-dd' });

	$(".smith_due_dt").datepicker({ startDate: new Date() });



}



$("#order_des_new").on("hidden.bs.modal", function () {



	CKEDITOR.instances.description_new.destroy();



});



$(document).on('click', "#neworder_list a.btn-success", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	get_order_img(id);



});



$(document).on('click', "#neworder_list a.btn-default", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	get_order_des(id);



});



$(document).on('click', "#neworder_list a.btn-edit", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	get_order_details(id);



});



$(document).on('click', "#neworder_list img.order_img", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	get_order_image(id);



});



function get_order_img(id) {



	$('#order_images').html('');



	img_order_id = id;



	$.ajax({



		data: ({ 'order_id': img_order_id }),



		url: base_url + "index.php/admin_ret_order/get_img_by_order_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			$.each(data, function (key, img) {



				var preview = $('#order_images');



				div = document.createElement("div");



				div.setAttribute('class', 'col-md-3 images');



				div.setAttribute('id', 'order_img_' + [key]);



				if (img.image != '' && img.image != undefined && img.image != null) {

					img_src = base_url + 'assets/img/orders/' + img.image;

				} else {

					img_src = base_url + 'assets/img/no_image.png';

				}



				console.log(img_src);





				div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><a onclick='remove_order_images_new(" + img.id_orderdetails + "," + img.img_name + "," + key + ")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + img_src + "'" + "style='width: 115px;height: 115px;'/></div></div>";



				preview.append(div);



			});



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



$("#update_img_new").on('click', function () {



	var final_file = [];



	var retrive_file = [];



	let image_details = localStorage['img_details'];



	if (image_details) {



		img_final = JSON.parse(image_details);



	}



	localStorage.removeItem("img_details");



	$('#imageModal_new').modal('toggle');



	if (image_details == '' || image_details == null) {



		for (i = 0; i < retrive_img.length; i++) {



			retrive_file.push(retrive_img[i]);



		}



		$.ajax({



			data: ({ 'id_orderdetails': img_order_id, 'image': retrive_file }),



			url: base_url + "index.php/admin_ret_order/insert_retrive_img?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



			dataType: "JSON",



			type: "POST",



			success: function (data) {



			},



			error: function (error) {



				$("div.overlay").css("display", "none");



			}



		});



	}



	else if (image_details != '' || image_details != null) {



		for (i = 0; i < img_final.length; i++) {



			final_file.push(img_final[i].src);



		}



		$.ajax({



			data: ({ 'id_orderdetails': img_order_id, 'image': final_file }),



			url: base_url + "index.php/admin_ret_order/update_order_image?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



			dataType: "JSON",



			type: "POST",



			success: function (data) {



				if (data.status) {



					$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });



				} else {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



				}



			},



			error: function (error) {



				$("div.overlay").css("display", "none");



			}



		});



	}



	else if ((image_details != '' || image_details != null) && (retrive_img != null || retrive_img != '')) {



		for (i = 0; i < retrive_img.length; i++) {



			retrive_file.push(retrive_img[i]);



		}



		for (i = 0; i < img_final.length; i++) {



			final_file.push(img_final[i].src);



		}



		$.ajax({



			data: ({ 'id_orderdetails': img_order_id, 'new_image': final_file, 'retrive_image': retrive_file }),



			url: base_url + "index.php/admin_ret_order/update_and_retrive_order_image?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



			dataType: "JSON",



			type: "POST",



			success: function (data) {







			},



			error: function (error) {



				$("div.overlay").css("display", "none");



			}



		});



	}



});



function insert_retrive_img(retrive_file) {



	$.ajax({



		data: ({ 'id_orderdetails': img_order_id, 'image': retrive_file }),



		url: base_url + "index.php/admin_ret_order/insert_retrive_img?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {







		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



function remove_order_images_new(id_orderdetails, img_name, key) {



	console.log(img_name);



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/delete_order_img?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		dataType: "JSON",



		data: ({ 'image': img_name, 'id_orderdetails': id_orderdetails }),



		type: "POST",



		success: function (data) {



			if (data.status) {



				$('#order_img_' + key).remove();



				$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });



			} else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



			}



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



function get_order_des(id) {



	des_order_id = id;



	$.ajax({



		data: ({ 'id_orderdetails': des_order_id }),



		url: base_url + "index.php/admin_ret_order/get_dec_by_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			description = data[0].description;



			CKEDITOR.replace('description_new');



			CKEDITOR.instances.description_new.setData(description);



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



$("#add_desc_new").on('click', function () {



	$('#order_des_new').modal('toggle');



	description = CKEDITOR.instances.description_new.getData();



	my_Date = new Date();



	var form_data = new FormData();



	form_data.append('description', description);



	form_data.append('id_orderdetails', des_order_id);



	$.ajax({



		data: form_data,



		url: base_url + "index.php/admin_ret_order/update_order_des?nocache=" + my_Date.getUTCSeconds(),



		type: "POST",



		dataType: 'json',



		cache: false,



		enctype: 'multipart/form-data',



		contentType: false,



		processData: false,



		success: function (data) {



			if (data.status) {



				$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });



			} else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



			}



		}



	});



});



/*$(document).on('change', ".smith_due_dt", function(e) {



		var row = $(this).closest('tr');



		var order_date=dateToTimeStamp(row.find('.order_date').val());



		var karigar_due_date=dateToTimeStamp(this.value);



		if(karigar_due_date<order_date)



		{



			row.find('.smith_due_dt').val('');



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Valid Date..'});



		}







});*/



function dateToTimeStamp(date) {



	new_date = date.split("-");



	new_date = new_date[1] + "/" + new_date[0] + "/" + new_date[2];



	time_stamp = new Date(new_date).getTime();







	return time_stamp;



}



$(document).on('click', "#neworder_list a.btn-edit", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	get_order_details(id);



});



$(document).on('click', "#neworder_list img.order_img", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	get_order_image(id);



});



$(document).on('change', '.smith_due_dt', function () {

	var value = $(this).val();

	$(this).attr('value', value);

});

$("input[name='upd_status_btn']:radio").on('change click', function () {



	if ($("input[name='id_orderdetails[]']:checked").val()) {



		var selected = [];



		var approve = false;



		var is_remark_entered = true;



		var smith_due_date_entered = true;





		$("#neworder_list tbody tr").each(function (index, value) {



			if ($(value).find("input[name='id_orderdetails[]']:checked").is(":checked")) {





				if ($(value).find(".id_orderdetails").val() != '' && $(value).find(".kar_remark").val() == '' && $(value).find(".cus_ord_status").val() == 3) {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Remark' });



					$(value).find(".kar_remark").focus();



					$('input[name=upd_status_btn]').removeAttr('checked');



					is_remark_entered = false;



				}



				if ($(value).find(".id_orderdetails").val() != '' && $(value).find(".smith_due_dt").val() == '' && $(value).find(".cus_ord_status").val() == 0) {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Smith Due Date' });



					$(value).find(".smith_due_dt").focus();



					$('input[name=upd_status_btn]').removeAttr('checked');



					smith_due_date_entered = false;



				}

				if (is_remark_entered == true && smith_due_date_entered == true) {



					transData = {



						'id_orderdetails': $(value).find(".id_orderdetails").val(),



						'id_category': $(value).find(".id_category").val(),



						'smith_due_dt': $(value).find(".smith_due_dt").val(),



						'kar_remark': $(value).find(".kar_remark").val()



					}



					selected.push(transData);

				}



			}



		});

		var smith_due_date = transData.smith_due_dt;



		var assign_to = $("input[name='order[assign_to]']:checked").val();



		var id_vendor = $('#karigar').val();



		var id_branch = $('#id_branch').val();



		var id_employee = $('#employee_sel').val();



		req_status = $("input[name='upd_status_btn']:checked").val();



		req_data = selected;



		if (req_status == 1) {



			if (assign_to == 1) {



				if (smith_due_date == "") {

					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Smith Due Date' });



				}

				if (id_vendor != '') {



					if (is_remark_entered == true) {



						update_request_data(req_status, req_data);

					}



				} else {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Karigar' });



				}



			}



			else if (assign_to == 2) {



				if (smith_due_date == "") {

					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Smith Due Date' });



				}



				if (id_employee != '' && id_employee != null) {



					update_request_data(req_status, req_data);



				} else {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Employee' });



				}



			}



		}



		else if (req_status == 2 && is_remark_entered == true) {



			update_request_data(req_status, req_data);



		}



	}



	else {



		alert('Please select any Order...!');



		$('input[name=upd_status_btn]').removeAttr('checked');



	}



});









function update_request_data(req_status, req_data, id_vendor, id_branch) {



	$(".overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/assign_customer_order?nocache=' + my_Date.getUTCSeconds(),



		method: "post",



		async: false,



		dataType: "json",



		data: ({ 'req_status': req_status, 'req_data': req_data, 'id_vendor': $('#karigar').val(), 'id_branch': $('#id_branch').val(), 'assign_to': $("input[name='order[assign_to]']:checked").val(), 'id_employee': $('#employee_sel').val() }),



		success: function (data) {



			if (req_status == 1) {



				console.log(data.id_customerorder);



				//window.open( base_url+'index.php/admin_ret_order/get_karigar_acknowladgement/?id_order='+data.id_customerorder,'_blank');



			}



			window.location.reload()



		}



	});



}



function get_all_branches() {



	my_Date = new Date();

	var branch_data = [];

	$.ajax({



		url: base_url + 'index.php/admin_ret_order/get_all_branch?nocache=' + my_Date.getUTCSeconds(),



		method: "get",



		dataType: "json",



		success: function (data) {



			var id = $("#select_branch").val();



			var filter_branch = $("#filter_branch").val();

			

			$("#branch_select").empty();



			$.each(data, function (key, item) {

				

					$("#select_branch").append(



						$("<option></option>")



							.attr("value", item.id_branch)



							.text(item.branch_name)



					);

					$("#branch_select").append(



						$("<option></option>")



							.attr("value", item.id_branch)



							.text(item.branch_name)



					);



					$("#branch_filter").append(



						$("<option></option>")



							.attr("value", item.id_branch)



							.text(item.branch_name)



					);

				



			});







			$("#select_branch").select2(



				{



					placeholder: "Assign To Branch",



					closeOnSelect: true



				});



			$("#branch_filter").select2(



				{



					placeholder: "Branch Filter",



					closeOnSelect: true



				});

			$("#select_branch").select2(



				{



					placeholder: "Select Branch",



					closeOnSelect: true



				});



			if ($("#select_branch").length) {



				$("#select_branch").select2("val", (id != '' && id > 0 ? id : ''));



			}







			if ($("#branch_filter").length) {



				$("#branch_filter").select2("val", (filter_branch != '' && filter_branch > 0 ? filter_branch : ''));



			}











			$(".overlay").css("display", "none");

			branch_data = data;


		}



	});
	
	return branch_data;
}



$('#select_branch').on('change', function (e) {



	if (this.value != '') {



		$('#id_branch').val(this.value);



	}



	else {



		$('#id_branch').val('');



	}



});



function get_order_details(id) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/get_ordersby_id/' + id + '/?nocache=' + my_Date.getUTCSeconds(),



		method: "get",



		dataType: "json",



		async: false,



		success: function (data) {



			$('#id_orderdetails').val(data.id_orderdetails);



			var content = '<div class="col-md-6"><label>Order No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.orderno + '</div><div class="col-md-6"><label>Customer Name &nbsp;: </label> &nbsp;' + data.cus_name + '</div><br><div class="col-md-6"><label>Mobile&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.mobile + '</div><div class="col-md-6"><label>Karigar Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.karigar_name + '</div><div class="col-md-6"><label>Type &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.orter_type + '</div><br><div class="col-md-6"><label>Product Name &nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.product_name + '</div><br><br><div class="col-md-6"><label>OrderDate &nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.order_date + '</div><div class="col-md-6"><label>Product Code &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.product_short_code + '</div><br><div class="col-md-6"><label>Weight &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.weight + '</div><div class="col-md-6"><label>Size &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.size + '</div><br><div class="col-md-6"><label>Pcs &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </label> &nbsp;' + data.totalitems + '</div><div class="col-md-6"><label>Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;</label> ' + data.orderstatus + '</div></br><div class="col-md-12"><label>Purity &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</label>&nbsp;' + data.purity + '</div></br><div class="col-md-12"> <textarea id="reject_reason"  placeholder="Enter Reject Reason" style="width: 46%; "/></div>';



			$('#confirm-view .modal-body').html(content);



			$('#confirm-view').modal('show');



		}



	});



}



function get_order_image(id) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/get_ordersby_id/' + id + '/?nocache=' + my_Date.getUTCSeconds(),



		method: "get",



		dataType: "json",



		async: false,



		success: function (data) {



			$('#imagePreview').empty();



			for (var i = 0; i < data.order_image.length; i++) {



				$("#imagePreview").append($('<img>', { src: data.order_image[i], style: "width:100px;" },));



			}



			$('#image-view').modal('show');



		}



	});



}



$('#reason_submit').on('click', function () {







	var reject_reason = $('#reject_reason').val();



	var id_orderdetails = $('#id_orderdetails').val();



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/updatereject_reason/?nocache=' + my_Date.getUTCSeconds(),



		method: "post",



		data: { 'id_orderdetails': id_orderdetails, 'reject_reason': reject_reason },



		dataType: "json",



		async: false,



		success: function (data) {



			window.location.reload()



		}



	});



});



function item_validateImage() {



	var files = event.target.files;



	var a = $('#cur_id').val();



	var preview = $('#1_img' + a);



	var html_1 = "";











	for (var i = 0; i < files.length; i++) {



		var file = files[i];



		total_files.push(file);



		if (file.size > 1048576) {



			alert('File size cannot be greater than 1 MB');



			files[i] = "";



			return false;







		}



		else {



			var fileName = file.name;



			var ext = fileName.substring(fileName.lastIndexOf('.') + 1);



			ext = ext.toLowerCase();



			if (ext != "jpg" && ext != "png" && ext != "jpeg") {



				alert("Upload JPG or PNG Images only");



				files[i] = "";







			}



			else {







				var reader = new FileReader();



				var id = i;



				reader.onload = function (event) {



					img_resource.push({ 'src': event.target.result, 'name': fileName });



				}



				if (file) {



					reader.readAsDataURL(file);



				}



				else {



					preview.prop('src', '');







				}



			}



		}







	}



	setTimeout(function () {



		console.log(img_resource);



		$.each(img_resource, function (key, item) {



			if (item) {



				var div = document.createElement("div");



				div.setAttribute('class', 'col-md-3');



				div.setAttribute('id', +a + '_id' + key);



				div.innerHTML += "<a onclick='img_remove(" + key + ")'><i class='fa fa-trash'></i></a><img class='thumbnail' src='" + item.src + "'" +



					"style='width: 100px;height: 100px;'/>";



				preview.append(div);



			}



		});



	}, 3000);



}







function img_remove(id) {



	var a = $('#cur_id').val();



	$('#' + a + '_id' + id).remove();



	const index = total_files.indexOf(img_resource[id]);



	total_files.splice(index, 1);



}



function remove_images(id = "", file = "") {



	var a = $('#cur_id').val();



	$('#' + a + '_id' + id).remove();



	$("div.overlay").css("display", "block");



	$.ajax({



		url: base_url + "index.php/admin_ret_order/remove_img",



		type: "POST",



		data: { 'file': file, 'id': id },



		success: function (result) {



			$("div.overlay").css("display", "none");



			window.location.reload();



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



$(document).on('click', "#img_upload", function () {



	var formData = new FormData();



	var current = $('#cur_id').val();



	for (var i = 0; i < total_files.length; i++) {



		formData.append("file[]", total_files[i]);



	}



	var my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/upload_orderimg/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		cache: false,



		contentType: false,



		processData: false,



		data: formData,



		success: function (data) {



			total_files = [];







			$('#image_name_' + current).val(data.name);







		}



	});



});



function get_metal_rates_by_branch() {



	var id_branch = $('#branch_select').val();



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_tagging/get_metal_rates_by_branch?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		data: { 'id_branch': id_branch },



		type: "POST",



		dataType: "json",



		async: false,



		success: function (data) {



			metal_rates = data;



			/*if(data.goldrate_22ct>0)



			{



				$('.per-grm-sale-value').html(data.goldrate_22ct);



				$('.silver_per-grm-sale-value').html(data.silverrate_1gm);



			}else{



				$('.per-grm-sale-value').html(0);



				$('.silver_per-grm-sale-value').html(0);



			}*/



			$('.per-grm-sale-value').html(data.goldrate_22ct);



			$('.silver_per-grm-sale-value').html(data.silverrate_1gm);



			$('.mjdmagoldrate_22ct').html(data.mjdmagoldrate_22ct);



			$('.mjdmasilverrate_1gm').html(data.mjdmasilverrate_1gm);



			$('.goldrate_18ct').html(data.goldrate_18ct);



			$('.goldrate_22ct').html(data.goldrate_22ct);



			$('.silverrate_1gm').html(data.silverrate_1gm);



			$('.silverrate_999').html(data.silverrate_999);











		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



function getSearchCustomers(searchTxt) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_estimation/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': searchTxt },



		success: function (data) {



			$("#cus_name").autocomplete(



				{



					source: data,



					select: function (e, i) {



						e.preventDefault();



						$("#cus_name").val(i.item.label);



						$("#cus_id").val(i.item.value);



						$("#cus_village").html(i.item.village_name);



						$("#chit_cus").html((i.item.accounts == 0 ? 'No' : 'Yes'));



						$("#vip_cus").html(i.item.vip);



						$("#cus_state").val(i.item.id_state);



					},



					change: function (event, ui) {



						if (ui.item === null) {



							$(this).val('');



							$('#cus_name').val('');



							$("#cus_id").val("");



							$("#cus_village").html("");



							$("#chit_cus").html("");



							$("#vip_cus").html("");



						}



					},



					response: function (e, i) {



						// ui.content is the array that's about to be sent to the response callback.



						if (searchTxt != "") {



							if (i.content.length === 0) {



								var mobile = $('#cus_name').val();

								if (mobile.length == 10) {

									create_customer(mobile);

									$("#customerAlert").html('');

								} else {

									$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter a valid customer name / mobile' });

								}



								//    $("#customerAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');



							} else {



								$("#customerAlert").html('');



							}



						} else {



						}



					},



					minLength: 3,



				});



		}



	});



}





function create_customer(mobile_no) {

	get_country();

	$('.offcanvas').offcanvas('show');

	get_village_list();

	$('#cus_mobile').val(mobile_no);



}



function get_cat_purity(curRow, id_category, curr_purity = 0) {



	// alert(curr_purity);



	$(".overlay").css('display', 'block');



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_catalog/category/cat_purity',



		dataType: 'json',



		data: {



			'id_category': id_category



		},



		success: function (data) {



			curRow.find(".purity option").remove();



			$('.purity').append(



				$("<option></option>")



					.attr("value", "")



					.text('-Choose-')



			);



			$.each(data, function (key, item) {



				curRow.find(".purity").append(



					$("<option></option>")



						.attr("value", item.id_purity)



						.text(item.purity)



				);



			});



			/*curRow.find(".purity").select2({



				placeholder: "Select Purity",



				allowClear: true



			});*/



			if (!(curr_purity > 0)) {



				curRow.find('.order_rate').val(0);



				curRow.find(".id_purity").val(curRow.find(".purity").val());



				calculate_orderSale_value();



			} else {



				curRow.find(".purity").val(curr_purity);



				curRow.find(".id_purity").val(curRow.find(".purity").val());



				get_search_custom_metal_rates(curRow);



			}



			$(".overlay").css("display", "none");



		}



	});



}







function getTaxGroupDetail(curRow, tgrp_id) {



	$(".overlay").css('display', 'block');



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_order/taxGroupItems',



		dataType: 'json',



		data: {



			'tgrp_id': tgrp_id



		},



		success: function (data) {



			tax_details = data;



		}



	});



}

$('#cus_mobile').on('blur', function () {

	var min_mob_len = $('#country option:selected').attr('min_mob_len');

	var max_mob_len = $('#country option:selected').attr('max_mob_len');

	console.log('min_mob_len', min_mob_len);

	console.log('max_mob_len', min_mob_len);

	if (min_mob_len != null && max_mob_len != null) {

		if ((this.value.length >= min_mob_len) && (this.value.length <= max_mob_len)) {

			$('#cus_mobile').val(this.value);

		}

		else {

			$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please enter between ' + min_mob_len + ' and ' + max_mob_len + ' digit mobile number..' });

			$('#cus_mobile').val('');

			$('#cus_mobile').prop('disabled', false);

		}

	}

	else {

		$('#cus_mobile').val(this.value);

	}

});







/* $("#add_newcutomer").on('click', function(){



		if($('#cus_first_name').val() != "")



		{



			if($('#cus_mobile').val() == "" || $('#cus_mobile').val() == null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please enter customer mobile"});







			}



			else if($('#country').val() == "" || $('#country').val() == null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Country"});



			}



			else if($('#state').val() == "" || $('#state').val() == null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select State"});



			}



			else if($('#address1').val() == "" || $('#address1').val() == null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please enter customer Address"});



			}



			else if($('#id_branch').val() == "" || $('#id_branch').val() == null)



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Branch"});



			}



			else if($('#country').val()!=101)

			{

				if($('#pp').val()=='')

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the PP No..'});



					return false;

				}

			}





			else



			{



				  if($('#cus_id').val() == "")



				  {



						add_cutomer();



				  }



				  else



				  {



					  update_cutomer();



				  }



			}



		}else{



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The First Name.."});



		}



	}); */



function add_cutomer_25_12_2023(cus_name, cus_mobile, id_village, cus_type) { //, cus_address



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_billing/createNewCustomer/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'cus_type': $("input[name='cus[cus_type]']:checked").val(), 'pan_no': $('#pan').val(), 'aadharid': $('#aadharid').val(), 'cusName': $('#cus_first_name').val(), 'cusMobile': $('#cus_mobile').val(), 'cusBranch': $('#id_branch').val(), 'id_village': $('#sel_village').val(), 'id_country': $('#country').val(), 'id_state': $('#state').val(), 'id_city': $('#city').val(), 'address1': $('#address1').val(), 'address2': $('#address2').val(), 'address3': $('#address3').val(), 'pincode': $('#pin_code_add').val(), 'mail': $('#cus_email').val(), 'gst_no': $('#gst_no').val(), 'dl_no': $('#dl').val(), 'pp_no': $('#pp').val() }, //Need to update login branch id here from session



		success: function (data) {



			if (data.success == true) {



				$('#confirm-add').modal('toggle');



				$("#cus_name").val(data.response.firstname + " - " + data.response.mobile);



				$("#cus_id").val(data.response.id_customer);



				$("#cus_state").val(data[0].id_state);



			} else {



				alert(data.message);



			}



		}



	});



}



$('#add_new_customer').on('click', function (e) {



	//get_village_list();



});



function get_village_list() {





	$('#sel_village option').remove();



	$('#ed_sel_village option').remove();



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_estimation/ajax_get_village?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "GET",



		dataType: "JSON",



		success: function (data) {







			$.each(data, function (key, item) {



				$("#sel_village,#ed_sel_village").append(



					$("<option></option>")



						.attr("value", item.id_village)



						.text(item.village_name)



				);



			});



			$("#sel_village,#ed_sel_village").select2({



				placeholder: "Select Village",



				allowClear: true



			});





			var id_village = $('#id_village').val();



			var ed_id_village = $('#ed_id_village').val();



			if (id_village != '' && id_village != null && id_village != undefined) {



				$("#sel_village").select2("val", (id_village != '' && id_village > 0 ? id_village : ''));



			}







			if (ed_id_village != '') {



				console.log(ed_id_village);



				$("#ed_sel_village").select2("val", (ed_id_village != '' && ed_id_village != null ? ed_id_village : ''));



			}







			$(".overlay").css("display", "none");



		},



		error: function (error) {



		}



	});



}







$("#search_bill_no").on('click', function () {



	getBillDetails($('#filter_bill_no').val());







});







// $(document).on('change', '#branch_select', function (e) {



// 	get_all_employee();



// });







$(document).on('keyup', ".tag_code", function (e) {



	var row = $(this).closest('tr');



	var tagData = this.value;



	var type = "";



	var searchTxt = "";



	if (tagData != "") {



		//Tab Not Active



		// $('ul#tabs > li').not('.active').addClass('disabled disabledTab');



		var istagId = (tagData.search("/") > 0 ? true : false);



		var isTagCode = (tagData.search("-") > 0 ? true : false);



		if (istagId) {







			var tId = tagData.split("/");



			searchTxt = (tId.length >= 2 ? tId[0] : "");



			type = "tag_id";



		}



		else if (isTagCode) {



			searchTxt = this.value;



			type = "tag_code";



		}



		if (searchTxt != "") {



			if ($("#branch_settings").val() == 1) {



				if ($("#id_branch").val() != "") {



					getSearchTags(searchTxt, type, row);



				} else {



					alert("Select Branch");



					$(this).val("");



				}



			} else {



				getSearchTags(searchTxt, type, row);



			}



		}



	}



	else {



		//Tab Not Active



		if ($(row).find('td:eq(1) .pro_id').val() != "") {



			$(row).find('td:eq(0) .est_tag_id').val("");



		}



	}



});







function getSearchTags(searchTxt, searchField, curRow) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/getIssueTaggingBySearch/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': searchTxt, 'searchField': searchField, 'id_branch': $("#branch_select").val() },



		success: function (data) {



			cur_search_tags = data;



			var ordertype = $('#issue_type').val();



			if (ordertype == 1) {



				$.each(data, function (key, item) {



					$('#stockrepair_item_detail > tbody tr').each(function (idx, row) {



						if (item != undefined) {



							if ($(this).find('.repair_tag_id').val() == item.value) {



								data.splice(key, 1);



							}



						}



					});



				});



				$(".tag_code").autocomplete(



					{



						source: data,



						select: function (e, i) {



							e.preventDefault();



							var curRowItem = i.item;







							var purewt = parseFloat((parseFloat(curRowItem.net_wt) * (parseFloat(curRowItem.purname))) / 100).toFixed(3);











							curRow.find('.tag_code').val(i.item.label);



							curRow.find('.repair_tag_id').val(i.item.value);



							curRow.find('.tag_id_cat').html(curRowItem.catname);



							curRow.find('.id_cat').val(i.item.catid);



							curRow.find('.tag_id_prod').html(curRowItem.product_name);



							curRow.find('.id_product').val(i.item.lot_product);



							curRow.find('.tag_id_des').html(curRowItem.design_name);



							curRow.find('.id_des').val(i.item.design_id);



							curRow.find('.tag_id_sub_des').html(curRowItem.sub_design_name);



							curRow.find('.id_sub_des').val(i.item.subdesignid);



							curRow.find('.gweight').val(curRowItem.gross_wt);



							curRow.find('.nweight').val(curRowItem.net_wt);



							curRow.find('.qty').val(curRowItem.piece);



							curRow.find('.tag_purity').html(curRowItem.purname);



							curRow.find('.pure_wt').val(purewt);







							$("#repaid_order_items").trigger('click');



						},



						change: function (event, ui) {



							if (ui.item === null) {



								/* $(this).val('');



								curRow.find('td:eq(0) .est_tag_name').val("");



								curRow.find('td:eq(0) .est_tag_id').val(""); */



							} else {



								//$('#estimation_tag_details > tbody').find('tr:last td:eq(0) .est_tag_name').focus();



							}



						},



						response: function (e, i) {



							// ui.content is the array that's about to be sent to the response callback.



							if (searchTxt != "") {



								if (i.content.length !== 0) {



									//console.log("content : ", i.content);



								}



							} else {



								curRow.find('.tag_code').val("");



								curRow.find('.repair_tag_id').val("");



							}



						},



						minLength: 1,



					});



			} else {



				$.each(data, function (key, item) {



					$('#tagissue_item_detail > tbody tr').each(function (idx, row) {



						if (item != undefined) {



							if ($(this).find('.issue_tag_id').val() == item.value) {



								data.splice(key, 1);



							}



						}



					});



				});



				$(".tag_code").autocomplete(



					{



						source: data,



						select: function (e, i) {



							e.preventDefault();



							var curRowItem = i.item;



							curRow.find('.tag_code').val(i.item.label);



							curRow.find('.issue_tag_id').val(i.item.value);



							curRow.find('.tag_id_cat').html(curRowItem.catname);



							curRow.find('.id_cat').val(i.item.catid);



							curRow.find('.tag_id_prod').html(curRowItem.product_name);



							curRow.find('.id_product').val(i.item.lot_product);



							curRow.find('.tag_id_des').html(curRowItem.design_name);



							curRow.find('.id_des').val(i.item.design_id);



							curRow.find('.tag_id_sub_des').html(curRowItem.sub_design_name);



							curRow.find('.id_sub_des').val(i.item.subdesignid);



							curRow.find('.gweight').val(curRowItem.gross_wt);



							curRow.find('.nweight').val(curRowItem.net_wt);



							curRow.find('.qty').val(curRowItem.piece);



							$("#add_issue_items").trigger('click');



						},



						change: function (event, ui) {



							if (ui.item === null) {



								/* $(this).val('');



								curRow.find('td:eq(0) .est_tag_name').val("");



								curRow.find('td:eq(0) .est_tag_id').val(""); */



							} else {



								//$('#estimation_tag_details > tbody').find('tr:last td:eq(0) .est_tag_name').focus();



							}



						},



						response: function (e, i) {



							// ui.content is the array that's about to be sent to the response callback.



							if (searchTxt != "") {



								if (i.content.length !== 0) {



									//console.log("content : ", i.content);



								}



							} else {



								curRow.find('.tag_code').val("");



								curRow.find('.repair_tag_id').val("");



							}



						},



						minLength: 1,



					});



			}



		}



	});



}











$(document).on('change', '.cat_select', function (e) {



	if ($(this).closest('tr').find('option:selected')) {



		$(this).closest('tr').find('.pro_select').val(null).trigger('change');



		var row = $(this).closest('tr');



		var pro_append = "<option value=''>- Select Product-</option>";



		$(this).closest('tr').find('.pro_select option').remove();



		var cat_select = row.find('.cat_select').val();







		if (cat_select != '') {



			$.each(cat_product_details, function (catkey, catval) {



				if (catval.id_ret_category == cat_select) {



					$.each(catval.products, function (mkey, mitem) {



						pro_append += "<option value='" + mitem.pro_id + "'>" + mitem.product_name + "</option>";



					});



					row.find('.pro_select').append(pro_append);



				}



			});



		}



	}



});



function getBillDetails(billNo) {



	$("#search_bill_no").val("");



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/get_bill_details/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'bill_no': billNo, 'id_branch': $('#branch_select').val() },



		success: function (data) {



			if (data.length > 0) {



				$('#cus_name').val(data[0].cus_name);



				$('#cus_id').val(data[0].id_customer);



				var subproduct_required = $('#subproduct_required').val();



				$('#bill_items_tbl_for_return tbody').empty();



				$.each(data, function (estkey, estval) {



					var row = '<tr>'



						+ '<td>' + (estval.status == 2 ? '<span style="color:red">Returned</span>' : '<input type="checkbox" class="select_est_details" value="1" />') + '<input type="hidden" class="bill_id" value="' + estval.bill_id + '" /><input type="hidden" class="bill_det_id" value="' + estval.bill_det_id + '" /><input type="hidden" class="bill_det_id" value="' + estval.bill_det_id + '" /><input type="hidden" class="est_id" value="' + estval.esti_id + '" /><input type="hidden" class="est_itm_id" value="' + estval.esti_item_id + '" /><input type="hidden" class="category_name" value="' + estval.category_name + '"><input type="hidden" class="cat_id" value="' + estval.cat_id + '"><input type="hidden" class="collection_name" value="' + estval.collection_name + '" /><input type="hidden" class="id_collection" value="' + estval.id_collection + '" /><input type="hidden" class="collection_name" value="' + estval.collection_name + '"></td>'



						+ '<td><span class="est_product_name">' + (subproduct_required == 1 ? estval.parent_prods_name + '-' + estval.product_name : estval.product_name) + '</span><input class="est_product_id" type="hidden" value="' + estval.product_id + '" /></td>'



						+ '<td><span class="est_design_code">' + estval.design_name + '</span><input type="hidden" class="est_design_id" value="' + estval.design_id + '"  /></td>'



						+ '<td><span class="est_piece">' + estval.piece + '</span><input type="hidden" class="est_pcs" value="' + estval.piece + '"  /></td>'



						+ '<td><span class="est_purname">' + estval.pur_name + '</span><input type="hidden" class="est_purid" value="' + estval.id_purity + '"  /></td>'



						+ '<td><span class="est_size">' + estval.size + '</span><input type="hidden" class="est_size_val" value="' + estval.size + '"  /></td>'



						+ '<td><span class="est_gross_wt">' + estval.gross_wt + '</span><input type="hidden" class="est_gross_val" value="' + estval.gross_wt + '"  /></td>'



						+ '<td><span class="est_less_wt">' + estval.less_wt + '</span><input type="hidden" class="est_less_val" value="' + estval.less_wt + '"  /></td>'



						+ '<td><span class="est_net_wt">' + estval.net_wt + '</span><input type="hidden" class="est_net_val" value="' + estval.net_wt + '"  /></td>'



						+ '<td><span class="est_item_cost">' + estval.item_cost + '</span><input type="hidden" class="est_material_price" value="' + estval.othermat_amount + '"  /><input type="hidden" class="est_stone_price" value="' + estval.stone_price + '"  /><input type="hidden" class="est_item_cost_val" value="' + estval.item_cost + '"  /></td>'



						+ '</tr>';



					$('#bill_items_tbl_for_return tbody').append(row);



				});



				$('#bill_items_for_return').show();



				$('#BillModal').modal('show');



			} else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Recoed Found' });



			}



		}



	});



}



$('#update_bill_details').on('click', function () {



	$('#bill_items_tbl_for_return > tbody tr').each(function (idx, row) {



		sold_items_row = $(this);



		var collections_required = $('#collections_required').val();



		var subproduct_required = $('#subproduct_required').val();



		var cus_due_date = $('#cus_due_date').val();



		var smith_due_date = $('#smith_due_date').val();



		var smith_rem_date = $('#smith_remainder_date').val();



		var rowExist = false;



		var html = '';



		if (sold_items_row.find('td:first .select_est_details').is(':checked')) {



			$('#repair_item_detail > tbody tr').each(function (bidx, brow) {



				return_items_row = $(this);



				if (sold_items_row.find('.bill_det_id').val() == return_items_row.find('.bill_det_id').val()) {



					rowExist = true;



				}



			});



			if (!rowExist) {



				if (sold_items_row.find('.select_est_details').is(':checked')) {



					var a = $("#i_increment").val();



					var i = ++a;



					$("#i_increment").val(i);



					html += "<tr id='detail" + i + "' class='" + i + "'>" +



						"<td>" + sold_items_row.find('.category_name').val() + "<input type='hidden' name='o_item[" + i + "][id_category]' id='id_category" + i + "' value=" + sold_items_row.find('.cat_id').val() + " required='true'/><input type='hidden' class='bill_det_id' name='o_item[" + i + "][bill_det_id]' id='bill_det_id" + i + "' value=" + sold_items_row.find('.bill_det_id').val() + " /><input type='hidden' name='o_item[" + i + "][orter_type]' id='ortertype" + i + "' value='3' required='true'/></td>" +



						(collections_required == 1 ? "<td>" + sold_items_row.find('.collection_name').val() + "</td>" : '') +



						"<td>" + sold_items_row.find('.est_purname').html() + " <input type='hidden' class='id_purity' name='o_item[" + i + "][id_purity]' id='id_purity" + i + "' value=" + sold_items_row.find('.est_purid').val() + " /></td>" +



						"<td>" + sold_items_row.find('.est_product_name').html() + "<input type='hidden' id='o_item_id_prod_" + i + "' name='o_item[" + i + "][id_product]'class='id_product' required='true' value=" + sold_items_row.find('.est_product_id').val() + " /></td>" +



						"<td>" + sold_items_row.find('.est_design_code').html() + "<input type='hidden' id='o_item_id_dsgn_" + i + "' name='o_item[" + i + "][design_no]' class='id_design' value=" + sold_items_row.find('.est_design_id').val() + " /></td>" +



						"<td><input type='number' class='form-control weight' placeholder='Enter Net Weight'  required id='weight_" + i + "' autocomplete='off'   name='o_item[" + i + "][weight]' value=" + sold_items_row.find('.est_net_val').val() + " /></td>" +



						"<td><input type='number' class='form-control qty' placeholder='Pcs' name='o_item[" + i + "][totalitems]' value='1' required='true'/></td>" +



						'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" name="o_item[' + i + '][order_img]""></td>' +



						'<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" name="o_item[' + i + '][description]"></td>' +



						"<td><input class='form-control datemask date cus_due_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][cus_due_date]' value=" + cus_due_date + " type='text' required='true' placeholder='Cus Due Date' readonly />" +



						"<td><input class='form-control datemask date smith_due_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_due_date]' type='text' value=" + smith_due_date + " required='true' placeholder='Smith Due Date' readonly style='width: 100px;'/></td>" +



						"<td><input class='form-control datemask date smith_rem_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_remainder_date]' type='text' value=" + smith_rem_date + " required='true' placeholder='Smith Reminder Date'   readonly style='width: 100px;'/>" +



						"</td>" +



						'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



					"</tr>";



				}







			}



			$('#repair_item_detail tbody').append(html);



		}



	});



	$('#BillModal').modal('toggle');



});











$('#ed_sel_village').on('change', function () {



	if (this.value != '') {



		$('#ed_id_village').val(this.value);



	}



	else {



		$('#ed_id_village').val('');



	}



});







$('#sel_village').on('change', function () {



	if (this.value != '') {



		$('#id_village').val(this.value);



	}



	else {



		$('#id_village').val('');



	}



});















$('#ed_cus_mobile').on('keyup', function () {



	if (this.value.length > 10) {



		$('#ed_cus_mobile').val('');



		$('#ed_cus_mobile').focus();



	}



	else {



		$('#ed_cus_mobile').prop('disabled', false);



	}



});







$("#update_cutomer").on('click', function () {



	if ($('#ed_cus_first_name').val() != "") {



		$(".ed_cus_first_name").html("");



		if ($('#ed_cus_mobile').val() != "") {



			$(".ed_cus_mobile").html("");



			var cus_type = $("input[name='ed_cus[cus_type]']:checked").val();



			update_cutomer($('#ed_cus_first_name').val(), $('#ed_cus_mobile').val(), $('#ed_id_village').val(), cus_type);



			$('#ed_cus_first_name').val('');



			$('#ed_cus_mobile').val('');



		} else {



			$(".ed_cus_mobile").html("Please enter customer mobile");



		}



	} else {



		$(".ed_cus_first_name").html("Please enter customer first name");



	}



});







function update_cutomer(cus_name, cus_mobile, id_village, cus_type) { //, cus_address



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_billing/updateNewCustomer/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'cus_type': $("input[name='cus[cus_type]']:checked").val(), 'id_customer': $('#cus_id').val(), 'pan_no': $('#pan').val(), 'aadharid': $('#aadharid').val(), 'cusName': $('#cus_first_name').val(), 'cusMobile': $('#cus_mobile').val(), 'cusBranch': $('#id_branch').val(), 'id_village': $('#sel_village').val(), 'id_country': $('#country').val(), 'id_state': $('#state').val(), 'id_city': $('#city').val(), 'address1': $('#address1').val(), 'address2': $('#address2').val(), 'address3': $('#address3').val(), 'pincode': $('#pin_code_add').val(), 'mail': $('#cus_email').val(), 'gst_no': $('#gst_no').val(), 'dl_no': $('#dl').val(), 'pp_no': $('#pp').val() }, //Need to update login branch id here from session



		success: function (data) {



			if (data.success == true) {



				$('#confirm-add').modal('toggle');



				$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);



				$("#cus_id").val(data.response.id_customer);



				$("#cus_state").val(data[0].id_state);



			} else {



				alert(data.message);



			}



		}



	});



}







function get_customer_old() {







	my_Date = new Date();



	$.ajax({



		type: "POST",



		url: base_url + "index.php/admin_ret_estimation/get_customer?nocache=" + my_Date.getUTCSeconds(),



		cache: false,



		dataType: "JSON",



		data: { 'id_customer': $('#cus_id').val() },



		success: function (data) {



			console.log(data);



			setTimeout(() => {

				$('#ed_id_village').val(data.id_village);



				$('#ed_cus_first_name').val(data.firstname);



				$('#ed_cus_mobile').val(data.mobile);

			}, 2000)







			if (data.cus_type == 1) {



				$('#ed_cus_type1').attr('checked', true);



			} else {



				$('#ed_cus_type2').attr('checked', true);



			}







			get_village_list();



			$('#confirm-edit').modal('show');



		}



	});



}



//Image Upload



function update_image_upload(curRow, id) {



	pre_img_resource = [];



	$('#active_row').val(curRow.closest('tr').attr('class'));



	$('#uploadArea_p_stn').empty();



	if (curRow != undefined) {



		var preview = 'uploadArea_p_stn';



		var order_img = curRow.find('.order_img').val();



		console.log(order_img)



		if (order_img != '') {



			var img_details = JSON.parse(order_img);







			$.each(img_details, function (key, item) {



				if (item) {



					pre_img_resource.push({ 'src': item.src, 'name': item.name });







					var div = document.createElement("div");



					div.setAttribute('class', 'col-md-4');



					div.setAttribute('id', +key);



					param = { "key": key, "preview": preview, "stone_type": "order_images" };



					div.innerHTML += "<a onclick='remove_stn_img(" + JSON.stringify(param) + ")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



						"style='width: 100px;height: 100px;'/>";







					$('#' + preview).append(div);



				}



				$('#lot_img_upload').css('display', '');



			});



		}







		console.log(pre_img_resource)



		$('#cus_i_increment').val(curRow.closest('tr').attr('class'));



	}



	$('#imageModal').modal('show');



}





$("#order_images").on('change', function () {



	if (this.value != '') {



		validateCertifImg(this.id);



	}



});



function validateCertifImg(type) {



	if (type == 'order_images') {



		preview = 'uploadArea_p_stn';



	}



	var files = event.target.files;



	var html_1 = "";



	for (var i = 0; i < files.length; i++) {



		var file = files[i];



		if (file.size > 1048576) {



			alert('File size cannot be greater than 1 MB');



			files[i] = "";



			return false;



		}



		else {



			var fileName = file.name;



			var ext = fileName.substring(fileName.lastIndexOf('.') + 1);



			ext = ext.toLowerCase();



			if (ext != "jpg" && ext != "png" && ext != "jpeg") {



				alert("Upload JPG or PNG Images only");



				files[i] = "";



			}



			else {



				var reader = new FileReader();



				reader.onload = function (event) {



					if (type == 'order_images') {



						pre_img_resource.push({ 'src': event.target.result, 'name': fileName });



						pre_img_files.push(file);



					}



				}



				if (file) {



					reader.readAsDataURL(file);



				}







			}



		}



	}



	setTimeout(function () {



		var resource = [];



		$('#' + preview + ' div').remove();



		if (type == 'order_images') {



			resource = pre_img_resource;



		}



		console.log(resource);



		$.each(resource, function (key, item) {



			if (item) {



				var div = document.createElement("div");



				div.setAttribute('class', 'col-md-4');



				div.setAttribute('id', +key);



				param = { "key": key, "preview": preview, "stone_type": type };



				div.innerHTML += "<a onclick='remove_stn_img(" + JSON.stringify(param) + ")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



					"style='width: 100px;height: 100px;'/>";



				$('#' + preview).append(div);



			}



			$('#lot_img_upload').css('display', '');



		});



	}, 1000);



	//pre_img_resource=[];



}



function remove_stn_img(param) {







	$('#' + param.preview + ' #' + param.key).remove();







	if (param.stone_type == 'order_images') {



		pre_img_resource.splice(param.key, 1);







	}



	var preview = 'uploadArea_p_stn';



	$('#' + preview + ' div').remove();



	$.each(pre_img_resource, function (key, item) {



		if (item) {







			var div = document.createElement("div");



			div.setAttribute('class', 'col-md-4');



			div.setAttribute('id', +key);



			param = { "key": key, "preview": preview, "stone_type": "order_images" };



			div.innerHTML += "<a onclick='remove_stn_img(" + JSON.stringify(param) + ")'><i class='fa fa-trash'></i>Delete</a><img class='thumbnail' src='" + item.src + "'" +



				"style='width: 100px;height: 100px;'/>";



			$('#' + preview).append(div);



		}



		$('#lot_img_upload').css('display', '');



	});







}







$('#imageModal  #update_img').on('click', function () {



	$('#imageModal').modal('toggle');



	var curRow = $("#active_row").val();



	$('.' + curRow).find('.order_img').val(JSON.stringify(pre_img_resource));



	console.log(JSON.stringify(pre_img_resource));



});



function update_order_description(curRow, id) {



	$('#i_increment').val(curRow.closest('tr').attr('class'));



	$('#description').val(curRow.closest('tr').find('.order_des').val());



	$('#order_des').modal('show');



}



$(document).on('click', ".add_order_desc", function () {



	var curRow = $("#i_increment").val(); // For customer order



	var content = $("#order_des #description").val();



	$('.' + curRow).find('.order_des').val(content);



	$('#order_des').modal('toggle');



});



function getSearchSubDesign(searchTxt, inputId, curRow) {



	var str = inputId.split("_");  // Sample : prod_1 => split and pass the id



	var prod_id = $("#o_item_id_prod_" + str[3]).val();



	var design_id = $("#o_item_id_dsgn_" + str[3]).val();



	console.log(str);



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/get_ActiveSubDesingns/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': searchTxt, 'product_id': prod_id, 'design_no': design_id },



		success: function (data) {



			$(".sub_design").autocomplete(



				{



					source: data,



					select: function (e, i) {



						e.preventDefault();



						console.log(inputId);



						$("#o_item_" + inputId).val(i.item.value);



						$("#" + inputId).val(i.item.label);



					},



					response: function (e, i) {



						// ui.content is the array that's about to be sent to the response callback.



						/*console.log(i);



						if (i.content.length === 0) {



						   $("#prodAlert").html('<p style="color:red">Enter a valid Design</p>');



						   $('#lt_product').val('');



						}else{



							$("#prodAlert").html('');



						} */



					},



					minLength: 0,



				});



		}



	});



}



$('#stock_issue_submit').on('click', function () {



	var ordertype = $('#issue_type').val();



	var allow_submit = true;



	if ($('#branch_select').val() == '' || $('#branch_select').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Branch..' });



		allow_submit = false;



	}



	else if (ordertype == 1)// REPAIR



	{



		var repair_type = $('#repair_type').val();



		if (repair_type == 1) {



			if ($('#stockrepair_item_detail > tbody  > tr').length == 0) {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Records to Update..' });



				allow_submit = false;



			}



		}



		else if (repair_type == 2) {



			if ($('#custrepair_item_detail > tbody  > tr').length == 0) {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Records to Update..' });



				allow_submit = false;



			}



		}



	}



	else if (ordertype == 2 || ordertype == 3) {



		if ($('#issue_employee').val() == '' || $('#issue_employee').val() == null) {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Employee..' });



			allow_submit = false;



		}



		else if ($('#tagissue_item_detail > tbody  > tr').length == 0) {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Records to Update..' });



			allow_submit = false;



		}



	}





	if (allow_submit) {



		var form_data = $('#stock_issue_form').serialize();



		$('#stock_issue_submit').prop('disabled', true);



		var url = base_url + "index.php/admin_ret_order/stock_issue/save?nocache=" + my_Date.getUTCSeconds();



		$.ajax({



			url: url,



			data: form_data,



			type: "POST",



			dataType: "JSON",



			success: function (data) {



				if (data.status) {



					$("div.overlay").css("display", "none");



					$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });



					window.open(base_url + 'index.php/admin_ret_order/stock_issue/issue_print/' + data['id_stock_issue'], '_blank');



					location.href = base_url + 'index.php/admin_ret_order/stock_issue/list';



				}



				else {



					window.location.reload();



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



					$("div.overlay").css("display", "none");



				}







			},



			error: function (error) {



				$("div.overlay").css("display", "none");



			}



		});



	}







});



function set_stock_issue_list() {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/stock_issue?nocache=" + my_Date.getUTCSeconds(),



		data: {},



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			var list = data.list;



			var access = data.access;



			$('#total_count').text(list.length);







			var oTable = $('#issue_list').DataTable();



			oTable.clear().draw();







			if (list != null && list.length > 0) {



				oTable = $('#issue_list').dataTable({



					"bDestroy": true,



					"bInfo": true,



					"bFilter": true,



					"scrollX": '100%',



					"bSort": true,



					"dom": 'lBfrtip',



					"order": [[0, "desc"]],



					"buttons": ['excel', 'print'],



					"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },



					"aaData": list,



					"aoColumns": [{ "mDataProp": "id_stock_issue" },



					{ "mDataProp": "issue_no" },



					{

						"mDataProp": function (row, type, val, meta) {



							if (row.status == 0 || row.status == 2) {



								return "<span class='label bg-red'>" + row.issue_status + "</span>";



							} else if (row.status == 1) {



								return "<span class='label bg-orange'>" + row.issue_status + "</span>";



							}



							else if (row.status == 3) {



								return "<span class='label bg-green'>" + row.issue_status + "</span>";



							}



						}

					},



					{ "mDataProp": "branch_name" },



					{ "mDataProp": "issue_date" },



					{ "mDataProp": "issue_type" },



					{ "mDataProp": "emp_name" },



					{ "mDataProp": "order_no" },



					{ "mDataProp": "repair_type" },



					{

						"mDataProp": function (row, type, val, meta) {



							id = row.id_stock_issue;



							print_url = base_url + 'index.php/admin_ret_order/stock_issue/issue_print/' + id;



							action_content = '<a href="' + print_url + '" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" ><i class="fa fa-print" ></i></a>';



							return action_content;



						}



					}







					]



				});



			}



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



$('#repair_tag_search').on('click', function () {



	var tag_code = $('#repair_tag_code').val();



	tag_search = true;



	$('#stockrepair_item_detail > tbody tr').each(function (idx, row) {



		curRow = $(this);



		if (curRow.find('.tag_code').val() == tag_code) {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists.' });



			tag_search = false;



			return false;



		}



	});



	if (tag_search) {



		get_tag_details(tag_code);



	}







});



$("input[name='order[issue_receipt_type]']:radio").change(function () {



	$('#stockrepair_item_detail > tbody').empty();



	$('#tagissue_item_detail > tbody').empty();



	var ordertype = $("input[name='order[issue_receipt_type]']:checked").val();



	if (ordertype == 1) {



		$('.type_issue').css("display", "block");



		$('.type_receipt').css("display", "none");



	} else {



		$('.type_issue').css("display", "none");



		$('.type_receipt').css("display", "block");



	}



});











function get_StockIssueItems() {



	$('#select_issue_no option').remove();



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/get_StockIssueItems?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "GET",



		dataType: "JSON",



		success: function (data) {



			var id = $('#select_issue_no').val();



			$.each(data, function (key, item) {



				$("#select_issue_no").append(



					$("<option></option>")



						.attr("value", item.id_village)



						.text(item.village_name)



				);



			});







			$("#select_issue_no").select2({



				placeholder: "Select Issue No",



				allowClear: true



			});







			if (id != '' && id != null && id != undefined) {



				$("#select_issue_no").select2("val", (id_village != '' && id_village > 0 ? id_village : ''));



			}







			$(".overlay").css("display", "none");



		},



		error: function (error) {



		}



	});



}



//Repair  Order



$("input[name='order[order_type]']:radio").change(function () {



	$('#stockrepair_item_detail > tbody').empty();



	$('#cusrepair_item_detail > tbody').empty();



	var ordertype = $("input[name='order[order_type]']:checked").val();



	if (ordertype == 3) {



		$('.cus_repair').css("display", "block");



		$('.stock_repair').css("display", "none");



	} else {



		$('.cus_repair').css("display", "none");



		$('.stock_repair').css("display", "block");



	}



});



$('#add_new_customer_repair').on('click', function (e) {



	//$('#confirm-add-customer').modal('toggle');



	$('#confirm-add').modal('toggle');



	get_village_list();



	$("#myModalLabel").text('Add Customer');







	$("#add_newcutomer").text('Add');



	$("#cus_first_name").val('');



	$("#cus_mobile").val('');



	$("#id_village").val('');



	$("#sel_village option").remove();



	$("#id_customer").val('');



	$('#id_country').val('');



	$('#id_state').val('');



	$('#state option').remove();



	$('#id_city').val('');



	$('#city option').remove();



	$("#address1").val('');



	$("#address2").val('');



	$("#address3").val('');



	$("#pincode").val('');



	$("#cus_email").val('');







	// $("#country").select2({



	// 	placeholder: "Enter Country",



	// 	allowClear: true



	// });







	// $("#state").select2({



	// 	placeholder: "Enter State",



	// 	allowClear: true



	// });







	// $("#city").select2({



	// 	placeholder: "Enter City",



	// 	allowClear: true



	// });



	get_country();



});







// function get_country() {



// 	$('#country option').remove();



// 	$.ajax({



// 		type: 'GET',



// 		url: base_url + 'index.php/settings/company/getcountry',



// 		dataType: 'json',



// 		success: function (country) {



// 			var cus_id_country = $('#id_country').val();

// 			var id_branch = $('#branch_select').val();

// 			var branch = get_all_branches();

// 			$.each(country, function (key, country) {

// 				// if (country.is_default == 1) {
// 				// 	$('#id_country').val(country.id);
// 				// }

// 				$('#country').append(



// 					$("<option></option>")



// 						.attr("value", country.id)

// 						.attr("min_mob_len", country.min_mob_len)

// 						.attr("max_mob_len", country.max_mob_len)

// 						.text(country.name)



// 				);



// 			});





// 			var id_country = $('#id_country').val();



// 			$("#country").select2({



// 				placeholder: "Enter Country",



// 				allowClear: true



// 			});







// 			$("#country").select2("val", (cus_id_country != '' ? cus_id_country : (id_country != null && id_country != '' ? id_country : '')));



// 		},



// 		error: function (error) {







// 		}



// 	});



// }





// $(document).ready(function () {

// 	$('#country').on('change', function () {

// 		if (this.value) {



// 			get_state(this.value);



// 		}



// 	});







	// $('#state').on('change', function () {



	// 	if (this.value) {



	// 		get_city(this.value);



	// 	}







	// });

// })















// function get_state(id) {



// 	$('#state option').remove();



// 	$.ajax({



// 		type: 'POST',



// 		data: { 'id_country': id },



// 		url: base_url + 'index.php/settings/company/getstate',



// 		dataType: 'json',



// 		success: function (state) {







// 			$.each(state, function (key, state) {
// 				// if (state.is_default == 1) {
// 				// 	$('#id_state').val(state.id);
// 				// }

// 				$('#state').append(



// 					$("<option></option>")



// 						.attr("value", state.id)



// 						.text(state.name)



// 				);



// 			});





// 			var id_state = $('#id_state').val();



// 			$("#state").select2({



// 				placeholder: "Enter State",



// 				allowClear: true



// 			});







// 			$("#state").select2("val", (id_state != null && id_state != '' ? id_state : ''));



// 		},



// 		error: function (error) {







// 		}



// 	});



// }











// function get_city(id) {







// 	$('#city option').remove();



// 	$.ajax({



// 		type: 'POST',



// 		data: { 'id_state': id },



// 		url: base_url + 'index.php/settings/company/getcity',



// 		dataType: 'json',



// 		success: function (city) {











// 			$.each(city, function (key, city) {

// 				// if (city.is_default == 1) {

// 				// 	$('#id_city').val(city.id);

// 				// }

// 				$('#city').append(



// 					$("<option></option>")



// 						.attr("value", city.id)



// 						.text(city.name)



// 				);



// 			});



// 			$("#city").select2({



// 				placeholder: "Enter City",



// 				allowClear: true



// 			});



// 			var id_city = $('#id_city').val();



// 			if ($("#city").length) {



// 				$("#city").select2("val", (id_city != null ? id_city : ''));



// 			}



// 			//  $("#city").val( id_city!=null && id_city!='' ? id_city :'' ).change();





// 		},



// 		error: function (error) {







// 		}



// 	});



// }











$('#add_newcutomer_repair').click(function (event) {







	if ($('#cus_first_name').val() == '') {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Firstname..' });



		return false;



	}



	else if ($('#cus_mobile').val() == '' || $('#cus_mobile').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Mobile Number..' });



		return false;



	} else if ($('#country').val() == '' || $('#country').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Country..' });



		return false;



	}



	else if ($('#state').val() == '' || $('#state').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the State..' });



		return false;



	} else if ($('#city').val() == '' || $('#city').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the City..' });



		return false;



	} else if ($('#address1').val() == '') {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Address..' });



		return false;



	} else if ($('#pin_code_add').val() == '' && $('#country').val() == 101) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });



		return false;



	}



	else if ($('#country').val() != 101) {

		if ($('#dl').val() == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the DL No..' });



			return false;

		}

		else if ($('#pp').val() == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the PP No..' });



			return false;

		}

	}







	add_cutomer_repair($('#cus_first_name').val(), $('#cus_mobile').val(), $('#id_village').val(), $('#cus_type:checked').val(), $('#gst_no').val());



	$('#cus_first_name').val('');



	$('#cus_mobile').val('');



});



function add_cutomer_repair(cus_name, cus_mobile, id_village, cus_type, gst_no) { //, cus_address



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_billing/createNewCustomer/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'cusName': cus_name, 'cusMobile': cus_mobile, 'cusBranch': $('#id_branch').val(), 'id_village': id_village, 'cus_type': cus_type, 'gst_no': gst_no, 'id_country': $('#country').val(), 'id_state': $('#state').val(), 'id_city': $('#city').val(), 'address1': $('#address1').val(), 'address2': $('#address2').val(), 'address3': $('#address3').val(), 'pincode': $('#pin_code_add').val(), 'mail': $('#cus_email').val(), 'dl_no': $('#dl').val(), 'pp_no': $('#pp').val() }, //Need to update login branch id here from session



		success: function (data) {



			if (data.success == true) {



				$('#confirm-add').modal('toggle');



				$("#cus_name").val(data.response.firstname + " - " + data.response.mobile);



				$("#cus_id").val(data.response.id_customer);



				// Loyalty module







				$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + 'Customer Created SuccessFully.' });







				// ./Loyalty module



			} else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



			}



		}



	});



}



function create_new_empty_cus_repair_order_row() {







	var trHtml = '';



	var a = $("#i_increment").val();



	var i = ++a;



	$("#i_increment").val(i);





	$.each(stone_details, function (skey, sitem) {





		stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });





		stone_price = parseFloat(stone_price) + parseFloat(sitem.amount);





	});





	trHtml += "<tr id='detail" + i + "' class='" + i + "'>"



		+ "<td><input type='hidden' name='order_item[id_orderdetails][]' /><select class='metal'  class='form-control' name='order_item[metal][]'  id='metal' required='true' style='width: 150px;' /><input type='hidden' name='[metal]' id='metal' required='true'/></td>"



		+ "<td><select class='form-control product'  name='order_item[id_product][]' id='product' required='true' style='width: 150px;'/><input type='hidden' id='id_product' required='true'/></td>"



		+ '<td><select class="form-control design" name="order_item[id_design][]" value="" placeholder="Search Design" required style="width:150px;"></select><input type="hidden" class="id_design" value="" /></td>'



		+ '<td><select class="form-control sub_design" name="order_item[id_sub_design][]" value="" placeholder="Search SubDesign" required style="width:150px;"></select><input type="hidden" class="id_sub_design"  value="" /></td>'



		+ '<td><input type="number" class="form-control weight" value="" name="order_item[weight][]" style="width:100px;"></td>'



		+ '<td><div class="form-group"><div class="input-group"><input class="form-control less_wt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="order_item[less_wt][]" step="any" readonly="" style="width:100px;"><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" name="order_item[stone_details][]" value="' + JSON.stringify(stone_details) + '"></div></div></td>'



		+ '<td><input type="number" class="form-control net_wt" value="" name="order_item[net_wt][]" style="width:100px;" readonly></td>'



		+ '<td><input type="number" class="form-control pcs" name="order_item[piece][]" style="width:100px;"></td>'



		+ '<td><input type="text" class="form-control cus_due_days"  name="order_item[cus_due_days][]" data-date-format="dd-mm-yyyy" style="width:100px;"></td>'



		+ "<td><select class='form-control repair' name='order_item[repair][]' id='repair' required='true' style='width: 150px;' /></td>"



		+ '<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" name="order_item[order_img][]"></td>'



		+ '<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" name="order_item[description][]"></td>'



		+ '<td><a href="#" id="create_order_item" class="btn btn-success btn-plus"><i class="fa fa-plus"></i></a><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



		+ '</tr>';





	get_metal();



	get_damage_type_master();



	if ($('#custrepair_item_detail > tbody  > tr').length > 0) {



		$('#custrepair_item_detail > tbody > tr:first').before(trHtml);



	} else {



		$('#custrepair_item_detail tbody').append(trHtml);



	}



	$(".cus_due_days").datepicker({ startDate: new Date() });



}



$(document).on("keyup", '.pcs', function (e) {







	if (e.which === 13) {



		e.preventDefault();



		if (validateNBDetailRow()) {



			create_new_empty_cus_repair_order_row();



		} else {



			alert("Please fill required fields");



		}







	}



	calculate_cus_pcs();



});



function calculate_cus_pcs() {



	var total_amount = 0;



	var order_pcs = 0;



	order_payment = [];



	$('#custrepair_item_detail > tbody  > tr').each(function (index, tr) {



		if ($(this).find('.pcs').val() != "") {



			order_pcs += parseFloat($(this).find('.pcs').val());



			order_payment.push({ 'pcs': $(this).find('.pcs').val() });



		}



	});



	console.log(order_pcs);



	$('.cus_tot_pcs').html((order_pcs));



}



$(document).on("keyup", '.weight', function (e) {







	if (e.which === 13) {



		e.preventDefault();



		if (validateNBDetailRow()) {



			create_new_empty_cus_repair_order_row();



		} else {



			alert("Please fill required fields");



		}







	}



	calculate_cus_weight();



});



function calculate_cus_weight() {



	var total_amount = 0;



	var order_wgt = 0;



	order_weight = [];



	$('#custrepair_item_detail > tbody  > tr').each(function (index, tr) {



		if ($(this).find('.weight').val() != "") {



			order_wgt += parseFloat($(this).find('.weight').val());



			order_weight.push({ 'weight': $(this).find('.weight').val() });



		}



	});







	$('.cus_tot_wgt').html(parseFloat(order_wgt).toFixed(2));



}



function remove_repair(curRow) {



	curRow.remove();



	calculate_cus_pcs();



	calculate_cus_weight();



}



function get_metal() {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/get_metal?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "POST",



		dataType: "JSON",



		success: function (data) {



			var id = $(".metal").val();



			$.each(data, function (key, item) {



				$(".metal").append(



					$("<option></option>")



						.attr("value", item.id_metal)



						.text(item.metal)



				);



			});







			$(".metal").select2(



				{



					placeholder: "Select metal",



					allowClear: true



				});



			// $(".metal").select2("val",(id!='' && id>0?id:''));



			// $(".metal").select2("val",(id!='' && id>0?id:''));











		},



		error: function (error) {



		}



	});



}



function get_damage_type_master() {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/get_repair_damage_master?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "POST",



		dataType: "JSON",



		success: function (data) {



			var id = $(".repair").val();



			$.each(data, function (key, item) {



				$(".repair").append(



					$("<option></option>")



						.attr("value", item.id_repair_master)



						.text(item.name)



				);



			});







			$(".repair").select2(



				{



					placeholder: "Select Type",



					allowClear: true



				});



			$(".repair").select2("val", (id != '' && id > 0 ? id : ''));



			$(".repair").select2("val", (id != '' && id > 0 ? id : ''));







		},



		error: function (error) {



		}



	});



}



$(document).on('change', "#metal", function () {



	var row = $(this).closest('tr');



	row.find('.product option').remove();



	if (this.value != '') {



		get_cus_product(row, this.value);



	}



});



function get_cus_product(curRow) {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/get_cus_product?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "POST",



		data: { 'id_metal': curRow.find('.metal').val() },



		dataType: "JSON",



		success: function (data) {



			var id = $(".product").val();



			$.each(data, function (key, item) {



				$(".product").append(



					$("<option></option>")



						.attr("value", item.pro_id)



						.text(item.product_name)



				);



			});







			$(".product").select2(



				{



					placeholder: "Select product",



					allowClear: true



				});



			$(".product").select2("val", (id != '' && id > 0 ? id : ''));



			$(".product").select2("val", (id != '' && id > 0 ? id : ''));







		},



		error: function (error) {



		}



	});



}



$(document).on("keyup change", '.weight,.less_wt,.pcs', function (e) {



	calcluate_cur_repair_total();



});





function calcluate_cur_repair_total() {



	var total_amount = 0;



	var order_pcs = 0;



	var order_weight = 0;



	var less_weight = 0;



	var net_wt = 0;



	$('#custrepair_item_detail > tbody  > tr').each(function (index, tr) {



		if ($(this).find('.weight').val() != "") {



			var row = $(this);



			var weight = (isNaN(row.find('.weight').val()) || row.find('.weight').val() == '') ? 0 : row.find('.weight').val();



			var less_wt = (isNaN(row.find('.less_wt').val()) || row.find('.less_wt').val() == '') ? 0 : row.find('.less_wt').val();



			var pcs = (row.find('.pcs').val() ? row.find('.pcs').val() : 0)



			row.find('.net_wt').val((parseFloat(weight) - parseFloat(less_wt)).toFixed(3));



			if (pcs != "") {

				if (pcs <= 0) {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Valid Pcs..' });



					row.find('.pcs').val('');

				}

			}



			if (parseFloat(weight) < parseFloat(less_wt)) {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Valid Gross Wt..' });



				row.find('.weight').val('');



			}

			else {



				net_wt += parseFloat($(this).find('.net_wt').val());



				order_weight += parseFloat($(this).find('.weight').val());



				less_weight += (isNaN($(this).find('.less_wt').val()) || $(this).find('.less_wt').val() == undefined ? '' : parseFloat($(this).find('.less_wt').val()));





				if ($(this).find('.pcs').val() != "") {



					order_pcs += parseFloat($(this).find('.pcs').val());



				}



			}

		}



	});



	$('.cus_tot_wgt').html(parseFloat(order_weight).toFixed(3));



	$('.cus_tot_net_wgt').html(parseFloat(net_wt).toFixed(3));



	$('.cus_tot_less_wgt').html(parseFloat((isNaN(less_weight) ? 0 : less_weight)).toFixed(3));



	$('.cus_tot_pcs').html((order_pcs));



}





function get_metal() {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/get_metal?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "POST",



		dataType: "JSON",



		success: function (data) {



			var id = $(".metal").val();



			$.each(data, function (key, item) {



				$(".metal").append(



					$("<option></option>")



						.attr("value", item.id_metal)



						.text(item.metal)



				);



			});







			$(".metal").select2(



				{



					placeholder: "Select metal",



					allowClear: true



				});



			$(".metal").select2("val", (id != '' && id > 0 ? id : ''));



			$(".metal").select2("val", (id != '' && id > 0 ? id : ''));











		},



		error: function (error) {



		}



	});



}



$(document).on('change', "#metal", function () {



	var row = $(this).closest('tr');



	row.find('.product option').remove();



	if (this.value != '') {



		get_cus_product(row, this.value);



	}



});



function get_cus_product(curRow) {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/get_cus_product?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "POST",



		data: { 'id_metal': curRow.find('.metal').val() },



		dataType: "JSON",



		success: function (data) {



			var id = $(".product").val();



			$.each(data, function (key, item) {



				$(".product").append(



					$("<option></option>")



						.attr("value", item.pro_id)



						.text(item.product_name)



				);



			});







			$(".product").select2(



				{



					placeholder: "Select product",



					allowClear: true



				});



			$(".product").select2("val", (id != '' && id > 0 ? id : ''));



			$(".product").select2("val", (id != '' && id > 0 ? id : ''));







		},



		error: function (error) {



		}



	});



}



$(document).on('keyup', ".cus_product", function (e) {



	curRow = $(this).closest('tr');



	var product = curRow.find(".cus_product").val();



	getSearchProducts(product, curRow);



});



function getSearchProducts(searchTxt, curRow) {



	if (searchTxt.length >= 3) {



		my_Date = new Date();



		$.ajax({



			url: base_url + 'index.php/admin_ret_estimation/getProductBySearch/?nocache=' + my_Date.getUTCSeconds(),



			dataType: "json",



			method: "POST",



			data: { 'searchTxt': searchTxt, 'is_non_tag': '' },



			success: function (data) {



				$(".cus_product").autocomplete(



					{



						source: data,



						select: function (e, i) {



							e.preventDefault();



							curRow.find('.cus_product').val(i.item.label);



							curRow.find('.id_product').val(i.item.value);



							console.log(curRow.find('.cus_product').val());



						},



						change: function (event, ui) {











						},



						response: function (e, i) {



							// ui.content is the array that's about to be sent to the response callback.



							if (searchTxt != "") {



								if (i.content.length !== 0) {



									console.log("content : ", i.content);



								}



							} else {



								curRow.find('.cus_product').val("");



								curRow.find('.id_product').val("");



							}



						},



						minLength: 3,



					});



			}



		});



	}



}



$('#repaid_order_items').on('click', function () {



	if (validateRepairOrderDetailRow()) {



		$('#create_order').prop('disabled', false);



		var repairtype = $("input[name='order[order_type]']:checked").val();



		if (repairtype == 4) {



			create_new_empty_repair_order_row();



		} else {



			create_new_empty_cus_repair_order_row();



		}







	}

	else {



		// $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Required FIelds..' });



		$('#create_order').prop('disabled', true);



	}



});



function validateRepairOrderDetailRow() {

	var validate = true;



	var repairtype = $('input[name="order[order_type]"]:checked').val();



	if (repairtype == 4) {

		$('#stockrepair_item_detail > tbody  > tr').each(function (index, tr) {

			if ($(this).find('.tag_code').val() == "") {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please  Enter the Tag Code' });

				validate = false;

				return false; // Stop the loop

			}

			if ($(this).find('.pro_select').val() == "") {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Product' });

				validate = false;

				return false; // Stop the loop

			}

			if ($(this).find('.qty').val() == "") {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Quantity' });

				validate = false;

				return false; // Stop the loop

			}

			if ($(this).find('.grsweight').val() == "") {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Gross weight' });

				validate = false;

				return false; // Stop the loop

			}

			if ($(this).find('.order_des').val() == "" || $(this).find('.order_des').val() == null) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Description' });

				validate = false;

				return false; // Stop the loop

			}

		});



		$('#tagissue_item_detail > tbody  > tr').each(function (index, tr) {



			if ($(this).find('.order_des').val() == "" || $(this).find('.order_des').val() == null) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Description ' });

				validate = false;

				return false;

			}

		});

	} else {

		$('#custrepair_item_detail > tbody  > tr').each(function (index, tr) {

			if ($(this).find('.product').val() == "" || $(this).find('.product').val() == null) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please select the Product' });

				validate = false;

				return false;

			}

			if ($(this).find('.pcs').val() == "") {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Pcs ' });

				validate = false;

				return false;

			}

			if ($(this).find('.weight').val() == "") {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Gross Weight ' });

				validate = false;

				return false;

			}

			if ($(this).find('.metal').val() == "" || $(this).find('.metal').val() == null) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please select the Metal' });

				validate = false;

				return false;

			}

			if ($(this).find('.cus_due_days').val() == "") {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please select Customer due days' });

				validate = false;

				return false;

			}

			if ($(this).find('.repair').val() == "" || $(this).find('.repair').val() == null) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please select the Repair' });

				validate = false;

				return false;

			}

			if ($(this).find('.order_des').val() == "" || $(this).find('.order_des').val() == null) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: 'Please Enter the Description ' });

				validate = false;

				return false;

			}

		});

	}



	return validate;

}









// function validateRepairOrderDetailRow() {



// 	var validate = true;



// 	var repairtype = $('input[name="order[order_type]"]:checked').val();



// 	if (repairtype == 4) {



// 		$('#stockrepair_item_detail > tbody  > tr').each(function (index, tr) {



// 			if ($(this).find('.tag_code').val() == "" || $(this).find('.pro_select').val() == "" || $(this).find('.qty').val() == "" || $(this).find('.grsweight').val() == "") {



// 				validate = false;



// 			}

// 		});



// 	} else {



// 		$('#custrepair_item_detail > tbody  > tr').each(function (index, tr) {



// 			if ($(this).find('.product ').val() == "" || $(this).find('.product ').val() == null || $(this).find('.pcs').val() == "" || $(this).find('.weight').val() == "" || $(this).find('.metal').val() == "" || $(this).find('.metal').val() == null || $(this).find('.cus_due_days').val() == "" || $(this).find('.repair').val() == "" || $(this).find('.repair').val() == null) {



// 				validate = false;



// 			}

// 		});

// 	}



// 	return validate;

// }





$('#create_repair_order').on('click', function () {



	if (validateRepairOrderDetailRow()) {



		var repairtype = $("input[name='order[order_type]']:checked").val();



		var order_for = $("input[name='order[order_for]']:checked").val();





		if (($('#cus_id').val() == null || $('#cus_id').val() == '' && (repairtype == 3))) {



			alert("Enter a valid customer");



			return false;



		}



		else if (($('#branch_select').val() == null || $('#branch_select').val() == '')) {



			alert("Select Order For Branch");



			return false;



		}

		else if (($('#id_employee').val() == null || $('#id_employee').val() == '') && $('#employee_sel').val() == '') {



			alert("Select Employee");



			return false;



		}



		else if ($('#custrepair_item_detail > tbody > tr').length == 0 && (repairtype == 3)) {



			alert("No Records Found..");



			return false;



		}

		else if ($('#tagissue_item_detail > tbody > tr').length == 0 && (repairtype == 4)) {



			alert("No Records Found..");



			return false;



		}

		else {



			$("div.overlay").css("display", "block");



			$('#create_repair_order').prop('disabled', true);



			var form_data = $('#order_submit').serialize();

			console.log(form_data);



			if (ctrl_page[3] != '' && ctrl_page[2] == 'edit') {

				var url = base_url + "index.php/admin_ret_order/repair_order/update?nocache=" + my_Date.getUTCSeconds();



			} else {



				var url = base_url + "index.php/admin_ret_order/repair_order/save?nocache=" + my_Date.getUTCSeconds();



			}



			$.ajax({



				url: url,



				data: form_data,



				type: "POST",



				dataType: "JSON",



				success: function (data) {



					if (data.status) {



						$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });



						window.open(base_url + 'index.php/admin_ret_order/repair_acknowledgement/' + data.id_customerorder, '_blank');



						window.location.replace(base_url + 'index.php/admin_ret_order/repair_order/list');



					}



					else {



						$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



					}







					$("div.overlay").css("display", "none");



				},



				error: function (error) {



					$("div.overlay").css("display", "none");



				}



			});



			$('#create_repair_order').prop('disabled', false);



		}







	}



	// else {



	// 	$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Fill The Required Fields.." });



	// }



});





function report_print(branch_name, report_name, from_date, to_date, optional) {

	if (branch_name == '') {

		branch_name = "ALL"

	}

	var data = "<span>" + report_name + " - " + branch_name + "</span></br>"

		+ "<span>" + (optional != '' ? optional : '') + "</span>"

		+ (from_date != '' && to_date != '' ? "<span>FROM&nbsp;:&nbsp;" + from_date + " &nbsp;&nbsp;TO&nbsp;&nbsp; " + to_date + "</span></br> " : '')

		+ $('.hidden-xs').html() + " &nbsp; - &nbsp;" + "</span>" + "<span style='font-size:11pt;'>" + getDisplayDateTime() + "</span></br>";

	return data;

}



function getDisplayDateTime() {

	var today = new Date();

	var dispdate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();

	var disptime = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();

	return dispdate + " " + disptime;

}



$('#reorder_search').click(function (event) {



	set_repair_order_list();



});

function set_repair_order_list() {



	var company_name = $('#company_name').val();

	var from_date = $("#rpt_payments1").html();

	var to_date = $("#rpt_payments2").html();

	var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());

	var report_name = "Repair Order Report"

	var optional = '';



	var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"

		+ "<span>" + report_print(branch_name, report_name, from_date, to_date, optional) + "</span>";



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/repair_order/ajax?nocache=" + my_Date.getUTCSeconds(),



		dataType: "JSON",



		type: "POST",



		data: ({ 'from_date': $("#rpt_payments1").html(), 'to_date': $("#rpt_payments2").html(), 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val()), 'id_employee': $('#employee_sel').val(), 'id_counter': $('#counter_sel').val() }),



		success: function (data) {



			var list = data.orders;



			var access = data.access;





			var oTable = $('#repair_order_list').DataTable();



			oTable.clear().draw();







			if (list != null && list.length > 0) {



				oTable = $('#repair_order_list').dataTable({



					"bDestroy": true,



					"bInfo": true,



					"bFilter": true,



					"scrollX": '100%',



					"bSort": true,



					"dom": 'lBfrtip',



					"order": [[0, "desc"]],



					"aaData": list,



					"columnDefs": [

						{

							targets: [6, 7],

							className: 'dt-right'

						},



					],



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

							exportOptions: {

								columns: ':visible',

								stripHtml: false

							}

						},

						{

							extend: 'excel',

							footer: true,

							title: company_name + '-' + (branch_name != '' ? branch_name : '') + ' - ' + 'Repair Order Report' + from_date + ' - ' + to_date,

							exportOptions: {

								columns: ':visible',

								stripHtml: false

							}

						},

						{

							extend: 'colvis',

							collectionLayout: 'fixed columns',

							collectionTitle: 'Column visibility control'

						},



					],



					"aoColumns": [



						{ "mDataProp": "id_customerorder" },



						{ "mDataProp": "order_no" },



						{ "mDataProp": "branch_name" },



						{ "mDataProp": "ordertype" },



						{ "mDataProp": "order_date" },



						{ "mDataProp": "cus_name" },



						{ "mDataProp": "order_pcs" },



						{ "mDataProp": "order_approx_wt" },



						{ "mDataProp": "firstname" },



						{



							"mDataProp": null,



							"sClass": "control center",



							"sDefaultContent": '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'



						},



						{

							"mDataProp": function (row, type, val, meta) {



								id = row.id_customerorder;



								order_no = row.order_no;



								edit_url = (access.edit == '1' ? base_url + 'index.php/admin_ret_order/repair_order/edit/' + id : '#');



								detailed_url = base_url + "index.php/admin_ret_order/repair_acknowledgement/" + id;



								delete_url = (row.allow_cancel == '1' ? 'onclick=confirm_repair_order_cancel(' + id + ')' : '#');





								action_content = '<a href="' + detailed_url + '" target="_blank" class="btn btn-primary btn-print" data-toggle="tooltip" title="Detailed Print"><i class="fa fa-print" ></i></a>	<a href=' + edit_url + ' class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id=' + id + '><i class="fa fa-edit" ></i></a> ' + (row.allow_cancel == 1 ? ' <a href="#" ' + delete_url + ' class="btn btn-danger" data-toggle="tooltip" title="Cancel Order"><i class="fa fa-close" ></i></a>' : '');



								return action_content;



							}

						}



					],



					"footerCallback": function (row, data, start, end, display) {

						if (list.length > 0) {

							var api = this.api(), data;

							for (var i = 0; i <= data.length - 1; i++) {

								var intVal = function (i) {

									return typeof i === 'string' ?

										i.replace(/[\$,]/g, '') * 1 :

										typeof i === 'number' ?

											i : 0;

								};

								$(api.column(5).footer()).html('Total');

								piece = api

									.column(6)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(6).footer()).html(formatNumber(piece, 0));

								gross_wt = api

									.column(7)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(7).footer()).html(formatNumber(gross_wt));



							}

						} else {

							var api = this.api(), data;



							$(api.column(5).footer()).html('');

							$(api.column(6).footer()).html('');



						}

					}







				});







				var anOpen = [];



				$(document).on('click', "#repair_order_list .control", function () {



					var nTr = this.parentNode;



					var i = $.inArray(nTr, anOpen);







					if (i === -1) {



						$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');



						oTable.fnOpen(nTr, fnFormatRowRepairDetails(oTable, nTr), 'details');



						anOpen.push(nTr);



					}



					else {



						$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');



						oTable.fnClose(nTr);



						anOpen.splice(i, 1);



					}



				});







			}



			$("div.overlay").css("display", "none");



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



function fnFormatRowRepairDetails(oTable, nTr) {



	var oData = oTable.fnGetData(nTr);



	var rowDetail = '';



	var prodTable =



		'<div class="innerDetails">' +



		'<table class="table table-responsive table-bordered text-center table-sm">' +



		'<tr class="bg-teal">' +



		'<th>S.No</th>' +



		'<th>Image</th>' +



		'<th>Product </th>' +



		'<th>Design </th>' +



		'<th>Sub Design </th>' +



		'<th>Pcs</th>' +



		'<th>Gross wt</th>' +



		'<th>Less wt</th>' +



		'<th>Net wt</th>' +



		'<th>Repair Type</th>' +



		'<th>Status</th>' +



		'<th>Remarks</th>' +



		'<th>Reject Reason</th>' +

		'<th>Repair Type</th>' +

		'<th>Tag Code</th>' +

		'<th>C.Due</th>' +



		'<th>Action</th>' +



		'</tr>';



	var order_details = oData.order_details;



	var total_amount = 0;



	$.each(order_details, function (idx, val) {



		if (val.image_details[0] != '' && val.image_details[0] != null) {







			img_src = base_url + 'assets/img/orders/' + val.image_details[0].image;



		}



		else {



			img_src = base_url + 'assets/img/no_image.png';



		}



		prodTable +=



			'<tr class="prod_det_btn">' +



			'<td>' + val.id_orderdetails + '</td>' +



			'<td>' + '<img src=' + img_src + ' width="50" height="55"><br><a  class="btn btn-secondary order_img"  id="edit" data-toggle="modal" data-id=' + val.id_orderdetails + '><i class="fa fa-eye" ></i></a>' + '</td>' +



			'<td>' + val.product_name + '</td>' +



			'<td>' + val.design_name + '</td>' +



			'<td>' + val.sub_design_name + '</td>' +



			'<td style="text-align:right;">' + val.totalitems + '</td>' +



			'<td style="text-align:right;">' + val.weight + '</td>' +



			'<td style="text-align:right;">' + val.less_wt + '</td>' +



			'<td style="text-align:right;">' + val.net_wt + '</td>' +



			'<td>' + val.repair_type + '</td>' +



			'<td>' + val.status + '</td>' +



			'<td>' + val.description + '</td>' +



			'<td>' + (val.reject_reason ? val.reject_reason : ' - ') + '</td>' +



			'<td>' + (val.repair_type) + '</td>' +

			'<td>' + (val.tag_code ? val.tag_code : '-') + '</td>' +

			'<td>' + (val.cus_due_date_date ? val.cus_due_date_date : '-') + '</td>' +



			'<td>' + (val.orderstatus < 3 ? '<button class="btn btn-danger" onclick="confirm_order_item_cancel(' + val.id_orderdetails + ')"><i class="fa fa-close" ></i></button>' : '-') + '</td>' +



			'</tr>';

	});







	rowDetail = prodTable + '</table></div>';



	return rowDetail;



}



$('input[type=radio][name="order[assign_to]"]').change(function () {



	if (this.value == 1) {



		$('#karigar_assign').css("display", "block");



		$('#emp_assign').css("display", "none");



	} else {



		get_all_employee();



		$('#karigar_assign').css("display", "none");



		$('#emp_assign').css("display", "block");



	}



});



/*function get_all_employee()



{



	$("#employee_sel option").remove();



	$.ajax({



	type: 'POST',



	url: base_url+'index.php/admin_ret_estimation/get_employee',



	dataType:'json',



	data:{'id_branch':''},



	success:function(data){



		var id =  $("#karigar").val();



		var id_employee =  $("#employee_sel").val();



		$.each(data, function (key, item) {



			$("#employee_sel").append(



			$("<option></option>")



			.attr("value", item.id_employee)



			.text(item.emp_name)



			);



		});



		$("#employee_sel").select2(



		{



			placeholder:"Assign To Employee",



			closeOnSelect: true



		});







			$("#employee_sel").select2("val",(id_employee!='' && id_employee>0?id_employee:''));



			$(".overlay").css("display", "none");



		}



	});



}*/



$(document).on('click', "#repair_order_list a.order_img", function (e) {



	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	view_dup_tag_history_imgs(id);



});



$(document).on('keyup', '.completed_weight', function (e) {



	var row = $(this).closest('tr');



	if (!(row.find('.id_orderdetails').is(':checked'))) {



		row.find('.id_orderdetails').prop('checked', true);



	}



});



$("#repair_order_status").on('click', function () {



	$("div.overlay").css("display", "block");



	if ($("input[name='id_orderdetails[]']:checked").val()) {



		var selected = [];



		var approve = false;



		var is_remark_entered = true;



		$("#repair_order_list tbody tr").each(function (index, value) {



			if ($(value).find("input[name='id_orderdetails[]']:checked").is(":checked")) {



				if ($(value).find(".id_orderdetails").val() != '' && $(value).find(".kar_remark").val() == '') {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Remark' });



					$(value).find(".kar_remark").focus();



					$('input[name=upd_status_btn]').removeAttr('checked');



					is_remark_entered = false;

				}



				if (is_remark_entered == true) {



					transData = {



						'id_orderdetails': $(value).find(".id_orderdetails").val(),



						'completed_weight': $(value).find(".completed_weight").val(),



						'final_amount': $(value).find(".final_amount").val(),



						'kar_remark': $(value).find(".kar_remark").val()





					}



					selected.push(transData);

				}



			}







		});



		req_data = selected;



		if (is_remark_entered) {

			update_repair_order_status(req_data);

		}



	}



	else {



		$("div.overlay").css("display", "none");



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Order' });



	}



});



function update_repair_order_status(req_data) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/repair_order_status?nocache=' + my_Date.getUTCSeconds(),



		method: "post",



		async: false,



		data: ({ 'req_data': req_data }),



		success: function (data) {



			window.location.reload()



		}



	});



}



function get_order_status() {



	my_Date = new Date();



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_reports/order_status/order_status?nocache=' + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		dataType: 'json',



		success: function (data) {



			var id = $("#order_status").val();







			$.each(data, function (key, item) {



				$('#order_status').append(



					$("<option></option>")



						.attr("value", item.id_order_msg)



						.text(item.order_status)



				);



			});







			$('#order_status').select2("val", '');



		}



	});



}



$('#repair_order_search').on('click', function () {



	setRepairOrderStatus();



});



function setRepairOrderStatus() {



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/order/repair_order_list?nocache=" + my_Date.getUTCSeconds(),



		dataType: "JSON",



		type: "POST",



		data: { 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val()), 'order_status': $('#order_status').val(), 'from_date': $('#rpt_payments1').html(), 'to_date': $('#rpt_payments2').html(), 'id_employee': $('#employee_sel').val(), 'repair_type': $('#repair_type').val(), 'work_type': $('#work_type').val(), 'id_counter': $('#counter_sel').val() },



		success: function (data) {







			var oTable = $('#repair_order_list').DataTable();



			oTable.clear().draw();



			TableData = [



				{

					"mDataProp": function (row, type, val, meta) {



						if (row.orderstatus > 0 && row.orderstatus <= 4) {



							chekbox = '<input type="checkbox" class="id_orderdetails" name="id_orderdetails[]" value="' + row.id_orderdetails + '"/><input type="hidden" class="orderstatus" value="' + row.orderstatus + '"/> <input type="hidden" class="order_type" value="' + row.order_type + '"/>'



							return chekbox + " " + row.id_orderdetails + '';



						} else {



							return row.id_orderdetails;



						}







					}

				},



				{ "mDataProp": "from_branch" },



				{ "mDataProp": "branch_name" },



				{ "mDataProp": "orderno" },

				{ "mDataProp": "po_no" },

				{ "mDataProp": "tag_code" },





				{ "mDataProp": "order_date" },



				{ "mDataProp": "cus_due_date" },





				{ "mDataProp": "cus_name" },







				{

					"mDataProp": function (row, type, val, meta) {



						var url = base_url + 'index.php/admin_ret_billing/billing_invoice/' + row.bill_id;



						return '<a href=' + url + ' target="_blank">' + row.bill_no + '</a>';



					},



				},



				{ "mDataProp": "totalitems" },

				{ "mDataProp": "weight" },



				{ "mDataProp": "less_wt" },

				{ "mDataProp": "net_wt" },

				{ "mDataProp": "product_name" },





				{ "mDataProp": "design_name" },

				{ "mDataProp": "sub_design_name" },





				{

					"mDataProp": function (row, type, val, meta) {



						console.log(row.order_img);



						if (row.order_img != '') {



							// var rep_order_image = base_url + 'assets/img/repair_order';



							var rep_order_image = base_url + 'assets/img/orders';





							// order_image = row.order_img.split('#');







							if (row.order_img != '' && row.order_img != null && row.order_img != undefined) {

								var type = rep_order_image + '/' + row.order_img;

							} else {

								var type = base_url + 'assets/img/no_image.png';



							}



							//return '<img src='+type+' width="60" height="65">';



							return '<img src=' + type + ' width="50" height="55"><br><a  class="btn btn-secondary rpr_ord_img"  id="edit" data-toggle="modal" data-id=' + row.id_orderdetails + '><i class="fa fa-eye" ></i></a>';



						} else {







							return '-';



						}



					},



				},









				{

					"mDataProp": function (row, type, val, meta) {



						var action_url = base_url + "index.php/admin_ret_order/order/repair_item_details/" + row.id_orderdetails + "/" + row.id_customerorder;



						return (row.work_at == 1 && (row.orderstatus == 3 || row.orderstatus == 4) ? '<a href=' + action_url + ' class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="other_metal_details"  value="">' : '-');



					}

				},







				{

					"mDataProp": function (row, type, val, meta) {



						if (row.orderstatus == 3 || row.orderstatus == 4) {



							return '<input type="number" class="completed_weight form-control" name="completed_weight[]" placeholder="Enter Weight" value="' + row.completed_weight + '"/> ';



						} else {



							return row.completed_weight;



						}







					}

				},



				{

					"mDataProp": function (row, type, val, meta) {



						if (row.orderstatus == 3 || row.orderstatus == 4) {



							return '<input type="number" class="final_amount form-control" name="final_amount[]" placeholder="Enter Amount"  value="' + row.amount + '"/> ';



						} else {



							return row.amount;



						}







					}



				},



				{ "mDataProp": "emp_name" },

				{ "mDataProp": "repair_type" },

				{ "mDataProp": "work_type" },











				{

					"mDataProp": function (row, type, val, meta) {

						return "<span class='badge bg-" + row.color + "'>" + row.order_status + "</span>";

					}



				},







				{ "mDataProp": "kar_name" },



				{ "mDataProp": "kar_mobile" },





				{ "mDataProp": "smith_due_date" },



				{ "mDataProp": "deliverydate" },





				{ "mDataProp": "description" },



				{ "mDataProp": "emp_name" },









				{

					"mDataProp": function (row, type, val, meta) {



						action_url = base_url + "index.php/admin_ret_order/repair_acknowledgement/" + row.id_customerorder;



						return "<a href='" + action_url + "' target='_blank' class='btn btn-info btn-print' data-toggle='tooltip' title='Order Print'><i class='fa fa-print'></i></a>";







					}

				}







			];



			columnDefs = [

				{

					targets: [4, 9, 10, 11, 12, 13, 17, 18, 19, 25],

					className: 'dt-right'

				},

				{

					targets: [0, 1, 2, 3, 5, 6, 7, 8, 12, 14, 15, 16, 20, 21, 22, 23, 24, 26, 27, 28],

					className: 'dt-left'

				},



			];



			tol_index = 9;



			pcs_index = 10;



			gross_wt_index = 11;



			less_wt_index = 12;



			net_wt_index = 13;





			if ($('#branch_user').val() == 1) {



				// TableData.remove(4);



				// TableData.remove(27);



				// TableData.remove(26);



				// TableData.remove(25);



				// delete TableData[4];



				// delete TableData[27];



				// delete TableData[26];



				// delete TableData[26];



				TableData.splice(27, 1);



				TableData.splice(26, 1);



				TableData.splice(25, 1);



				TableData.splice(4, 1);



				columnDefs = [

					{

						targets: [4, 9, 10, 11, 12, 13, 17, 18, 19],

						className: 'dt-right'

					},

					{

						targets: [0, 1, 2, 3, 5, 6, 7, 8, 12, 14, 15, 16, 20, 21, 22, 23, 24],

						className: 'dt-left'

					},



				]



				//columnDefs = [];

				console.log(TableData);



				console.log(columnDefs);



				tol_index = 8;



				pcs_index = 9;



				gross_wt_index = 10;



				less_wt_index = 11;



				net_wt_index = 12;



			}







			if (data != null && data.length > 0) {



				oTable = $('#repair_order_list').dataTable({



					"bDestroy": true,



					"bInfo": true,



					"bFilter": true,



					"scrollX": '100%',



					"bSort": true,



					"dom": 'lBfrtip',



					"order": [[0, "desc"]],



					"columnDefs": columnDefs,



					"buttons": [



						{



							extend: 'print',



							footer: true,



							title: "Repair Order Status Report",



							customize: function (win) {



								$(win.document.body).find('table')



									.addClass('compact')



									.css('font-size', 'inherit');



							},

							exportOptions: {

								columns: ':visible',

								stripHtml: false

							}



						},



						{



							extend: 'excel',



							footer: true,



							title: "Repair Order Status Report",



						}



					],



					"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },



					"aaData": data,



					"aoColumns": TableData,







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







								$(api.column(tol_index).footer()).html('Total');



								val_10 = api

									.column(pcs_index)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(pcs_index).footer()).html(formatNumber(val_10, 0));

								gross_wt = api

									.column(gross_wt_index)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(gross_wt_index).footer()).html(formatNumber(gross_wt, 3));





								less_wt = api

									.column(less_wt_index)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(less_wt_index).footer()).html(formatNumber(less_wt, 3));



								net_wt = api

									.column(net_wt_index)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(net_wt_index).footer()).html(formatNumber(net_wt, 3));



								// c_wt = api

								// 	.column(18)

								// 	.data()

								// 	.reduce(function (a, b) {

								// 		return intVal(a) + intVal(b);

								// 	}, 0);

								// $(api.column(18).footer()).html(formatNumber(c_wt,3));



								// amt = api

								// 	.column(19)

								// 	.data()

								// 	.reduce(function (a, b) {

								// 		return intVal(a) + intVal(b);

								// 	}, 0);

								// $(api.column(19).footer()).html(formatNumber(amt,3));



								/* val_11 = api

									.column(11)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(11).footer()).html(formatNumber(val_11,3)); */



								/* piece = api

									.column(12)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(12).footer()).html(formatNumber(piece,2));

								gross_wt = api

									.column(13)

									.data()

									.reduce(function (a, b) {

										return intVal(a) + intVal(b);

									}, 0);

								$(api.column(13).footer()).html(formatNumber(gross_wt)); */



							}

						} else {

							var api = this.api(), data;

							$(api.column(10).footer()).html('');

							$(api.column(11).footer()).html('');

							$(api.column(12).footer()).html('');

							$(api.column(13).footer()).html('');





						}

					}



				});



			}



			$("div.overlay").css("display", "none");



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}



$('#issue_tag_search,#issue_old_tag_search').on('click', function () {



	var tag_code = $('#issue_tag_code').val();



	var old_tag_code = $('#old_issue_tag_code').val();



	tag_search = true;



	$('#tagissue_item_detail > tbody tr').each(function (idx, row) {



		curRow = $(this);



		if (curRow.find('.tag_code').val() == tag_code || curRow.find('.old_tag_id').val() == old_tag_code) {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists.' });



			tag_search = false;



			return false;



		}



	});



	if ($('.order_from').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select Branch' });



		tag_search = false;



		return false;



	}



	if (tag_search) {



		get_tag_details(tag_code, old_tag_code);



	}



});



function get_tag_details(tag_code, old_tag_code) {



	var issue_type = $('#issue_type').val();



	var tag_search = true;





	my_Date = new Date();



	$.ajax({



		type: "POST",



		url: base_url + "index.php/admin_ret_stock_issue/get_tag_scan_details?nocache=" + my_Date.getUTCSeconds(),



		cache: false,



		dataType: "JSON",



		data: { 'tag_code': tag_code, 'old_tag_code': old_tag_code, 'id_branch': $("#branch_select").val() },



		success: function (data) {



			if (data.length > 0) {



				var html = "";



				var a = $("#cus_i_increment").val();



				var i = ++a;



				$("#cus_i_increment").val(i);





				$('#tagissue_item_detail > tbody tr').each(function (idx, row) {



					curRow = $(this);



					if (curRow.find('.tag_code').val() == tag_code || curRow.find('.old_tag_id').val() == old_tag_code) {



						$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists.' });



						tag_search = false;



						return false;



					}



				});



				if (tag_search) {



					$.each(data, function (key, items) {



						var purewt = parseFloat((parseFloat(items.net_wt) * (parseFloat(items.purname) + parseFloat(items.wastage_percent))) / 100).toFixed(3);



						if (items.tag_img != null && items.tag_img != undefined && items.tag_img != '') {



							var img_src = base_url + 'assets/img/tag/' + items.tag_img;

						}

						else {

							var img_src = base_url + 'assets/img/no_image.png';

						}



						stone_details = [];



						var stone_price = "";



						$.each(items.stone_details, function (skey, sitem) {



							stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });



							stone_price = parseFloat(stone_price) + parseFloat(sitem.amount);



						});









						html += "<tr id='detail" + i + "' class='" + i + "'>"



							+ '<td><img class="img_src"  id="img_prev" src="' + img_src + '" width="35" height="35" onclick="tag_imgprev($(this).closest(\'tr\'))"></td>'



							+ '<td><input type="hidden" class="tag_id" name="order_item[tag_id][]" value="' + items.value + '"><input type="hidden" class="tag_code" name="tag_code[]" value="' + items.label + '">' + items.label + '</td>'



							+ '<td><input type="hidden" class="old_tag_code" name="order_item[old_tag_code][]" value="' + items.value + '">' + (items.old_label ? '<input type="hidden" class="old_tag_code" name="old_tag_code[]" value="' + items.old_label + '">' + items.old_label : '') + '</td>'



							+ '<td>' + items.catname + '</td>'



							+ '<td>' + items.purname + '</td>'



							+ '<td><input type="hidden" class="id_product" name="order_item[id_product][]" value="' + items.lot_product + '">' + items.product_name + '</td>'



							+ '<td><input type="hidden" class="id_design" name="order_item[id_design][]" value="' + items.design_id + '">' + items.design_name + '</td>'



							+ '<td><input type="hidden" class="id_sub_design" name="order_item[id_sub_design][]" value="' + items.subdesignid + '">' + items.sub_design_name + '</td>'



							+ '<td><input type="hidden" class="piece" name="order_item[piece][]" value="' + items.piece + '">' + items.piece + '</td>'



							+ '<td><input type="hidden" class="purewt" name="order_item[purewt][]" value="' + purewt + '"><input type="hidden" class="gross_wt" name="order_item[weight][]" value="' + items.gross_wt + '">' + items.gross_wt + '</td>'



							+ '<td><input type="hidden" class="net_wt" name="order_item[net_wt][]" value="' + items.net_wt + '">' + items.net_wt + '</td>'



							+ '<td><div class="form-group"><div class="input-group"><input class="form-control less_wt"  value="' + items.less_wt + '"onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="order_item[less_wt][]" step="any" readonly="" style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" value=' + JSON.stringify(stone_details) + ' name="order_item[stone_details][]" ><input type="hidden" class="stone_price" name="order_item[stone_price][]"value="' + stone_price + '"></div></div></td>'



							+ "<td><select class='form-control repair' name='order_item[repair][]' id='repair' required='true' style='width: 150px;' /></td>"



							+ '<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" name="order_item[order_img][]"></td>'



							+ '<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" name="order_item[description][]" value="" ></td>'



							// + "<td><select class='form-control repair' name='order_item[repair][]' id='repair' required='true' style='width: 150px;' /></td>"



							+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



							+ '</tr>';



					});



					get_damage_type_master();





					if ($('#tagissue_item_detail > tbody  > tr').length > 0) {



						$('#tagissue_item_detail > tbody > tr:first').before(html);



					} else {



						$('#tagissue_item_detail tbody').append(html);



					}



				}



				calculate_tag_issue_details();



				$('#issue_tag_code').val('');



				$('#old_issue_tag_code').val('');



				$('#issue_tag_code').focus();



			}



			else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Records Found.' });



			}



		}



	});



}



function tag_imgprev(curRow) {



	$('#imagepreview').attr('src', curRow.find('#img_prev').attr('src'));



	$('#imagemodal').modal('show');

}



function calculate_tag_issue_details() {



	var total_pcs = 0;



	var total_gwt = 0;



	var total_nwt = 0;





	$('#tagissue_item_detail > tbody tr').each(function (idx, row) {



		curRow = $(this);



		total_pcs += parseFloat(curRow.find('.piece').val());



		total_gwt += parseFloat(curRow.find('.gross_wt').val());



		total_nwt += parseFloat(curRow.find('.net_wt').val());





	});



	$('.total_pieces').html(total_pcs);



	$('.total_gross_wt').html(parseFloat(total_gwt).toFixed(3));



	$('.total_net_wt').html(parseFloat(total_gwt).toFixed(3));





}



//Repair  Order



//Order Cart



$(document).on('change', ".totalitems", function (e) {



	var row = $(this).closest('tr');







	var order_pcs = row.find('.order_pcs').val();



	var max_pcs = row.find('.max_pcs').val();



	var totalitems = row.find('.totalitems').val();







	if (parseFloat(totalitems) > parseFloat(max_pcs)) {



		alert('Entered Pieces Are Greater than the Available Pieces..');



		row.find('.totalitems').val(order_pcs);



	}



});



function getSearchKarigar(searchTxt, curRow) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/karigar_search/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': searchTxt },



		success: function (data) {



			$(".karigar_search").autocomplete(



				{



					source: data,



					select: function (e, i) {



						e.preventDefault();



						curRow.find(".karigar_search").val(i.item.label);



						curRow.find(".id_karigar").val(i.item.value);







					},



					change: function (event, ui) {



						if (ui.item === null) {



							curRow.find(".karigar_search").val('');



							curRow.find(".id_karigar").val('');



						}



					},







					response: function (e, i) {



						// ui.content is the array that's about to be sent to the response callback.



						/*console.log(i);



						if (i.content.length === 0) {



						   $("#prodAlert").html('<p style="color:red">Enter a valid Product</p>');



						   $('#lt_product').val('');



						}else{



							$("#prodAlert").html('');



						} */



					},



					minLength: 0,



				});



		}



	});



}



function get_order_cart() {



	$(".overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/cart?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'id_product': $('#prod_select').val(), 'id_design': $('#des_select').val(), 'id_wt_range': $('#wt_select').val() },



		success: function (data) {



			get_order_cart_list(data.orders);



			$(".overlay").css("display", "none");



		}



	});



}



function get_order_cart_list(order) {



	var row = '';



	$("#cart_list > tbody > tr").remove();



	$('#cart_list').dataTable().fnClearTable();



	$('#cart_list').dataTable().fnDestroy();



	$.each(order, function (key, item) {



		row += '<tr>'



			+ '<td><input type="checkbox" class="id_cart_order" name="cart[id_cart_order][]" value="' + item.id_cart_order + '"/>' + item.id_cart_order + '<input type="hidden" class="ortertype" name="cart[ortertype][]" value=' + item.ortertype + ' /><input type="hidden" class="smith_remainder_date" name="cart[smith_remainder_date][]" value=' + item.smith_remainder_date + ' /><input type="hidden" class="smith_due_date" name="cart[smith_due_date][]" value=' + item.smith_due_date + ' /><input type="hidden" class="id_purity" name="cart[id_purity][]" value=' + item.id_purity + ' /><input type="hidden" class="id_branch" name="cart[id_branch][]" value=' + item.id_branch + ' /></td>'



			+ '<td><span>' + item.order_date + '</div><input type="hidden" class="order_date" name="cart[order_date][]" value=' + item.order_date + ' /></td>'



			+ '<td><span>' + item.product_name + '</span><input type="hidden" class="id_product" name="cart[id_product][]" value=' + item.id_product + ' /></td>'



			+ '<td><span>' + item.design_name + '</span><input type="hidden" class="design_no" name="cart[design_no][]" value=' + item.design_no + ' /></td>'



			+ '<td><span>' + item.sub_design_name + '</span><input type="hidden" class="id_sub_design" name="cart[id_sub_design][]" value=' + item.id_sub_design + ' /></td>'



			+ '<td><span>' + item.weight_range + '</span><input type="hidden" class="weight_range_value" name="cart[weight_range_value][]" value=' + item.weight_range_value + ' /><input type="hidden" class="id_wt_range" name="cart[id_wt_range][]" value=' + item.id_wt_range + ' /></td>'



			+ '<td><input type="number" class="form-control totalitems" name="cart[totalitems][]" value=' + item.totalitems + ' /><input type="hidden" class="max_pcs" value=' + item.max_pcs + ' /><input type="hidden" class="order_pcs" value=' + item.totalitems + ' /></td>'



			+ '<td><span>' + item.size_name + '</span><input type="hidden" class="size" name="cart[size][]" value=' + item.id_size + '></td>'



			+ '<td><span>' + item.emp_name + '</span></td>'



			+ '</tr>';



	});







	$('#cart_list tbody').append(row);



	if (!$.fn.DataTable.isDataTable('#cart_list')) {



		oTable = $('#cart_list').dataTable({



			"bSort": true,



			"bInfo": true,



			"scrollX": '100%',



			"dom": 'lBfrtip',



			"paging": false,



			"buttons": [



				{



					extend: 'print',



					footer: true,



					title: '',



					customize: function (win) {



						$(win.document.body).find('table')



							.addClass('compact')



							.css('font-size', 'inherit');



					},



				},



				{



					extend: 'excel',



					footer: true,



				}



			],



		});



	}



	// $('.smith_due_dt').datepicker({ dateFormat: 'yyyy-mm-dd' });

	$(".smith_due_dt").datepicker({ startDate: new Date() });



}



$("input[name='order_status_btn']:radio").change(function () {



	req_status = $("input[name='order_status_btn']:checked").val();



	var allow_submit = true;



	if (req_status == 1) {



		if ($('#select_karigar').val() == '' || $('#select_karigar').val() == null) {



			$('input[name=order_status_btn]').removeAttr('checked');



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Karigar' });



			allow_submit = false;



		} else if ($('.smith_due_dt').val() == '' || $('.smith_due_dt').val() == null) {



			$('input[name=order_status_btn]').removeAttr('checked');



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Due Date' });



			allow_submit = false;



		}



	}



	if (allow_submit) {



		if ($("input[name='cart[id_cart_order][]']:checked").val()) {



			$(".overlay").css("display", "block");



			var selected = [];







			var deleteids_arr = [];



			$("#cart_list tbody tr").each(function (index, value) {



				if ($(value).find("input:checkbox[class=id_cart_order]:checked").is(":checked")) {



					transData = {



						'id_cart_order': $(value).find(".id_cart_order").val(),



						'id_branch': $(value).find(".id_branch").val(),



						'totalitems': $(value).find(".totalitems").val(),



						'weight_range_value': $(value).find(".weight_range_value").val(),



						'id_product': $(value).find(".id_product").val(),



						'design_no': $(value).find(".design_no").val(),



						'id_sub_design': $(value).find(".id_sub_design").val(),



						'id_wt_range': $(value).find(".id_wt_range").val(),



						'id_karigar': $(value).find(".id_karigar").val(),



						'size': $(value).find(".size").val(),



						'smith_due_dt': $(value).find(".smith_due_dt").val(),



					}



					selected.push(transData);



				}



			});



			if (allow_submit) {



				req_status = $("input[name='order_status_btn']:checked").val();



				req_data = selected;



				order_place(req_status, req_data);



			}



		}



		else {



			$('input[name=order_status_btn]').removeAttr('checked');



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Any One Item' });



		}



	}







});



function order_place(req_status, data) {



	my_Date = new Date();



	$(".overlay").css("display", "block");



	$.ajax({



		url: base_url + "index.php/admin_ret_order/cart/order_place?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		data: { 'status': req_status, 'req_data': data, 'id_karigar': $('#select_karigar').val(), 'smith_due_dt': $('.smith_due_dt').val() },



		type: "POST",



		dataType: "json",



		async: false,



		success: function (data) {



			location.reload(true);



			$(".overlay").css("display", "none");



		},



		error: function (error) {



			console.log(error);



			$(".overlay").css("display", "none");



		}



	});



}



function get_ActiveProduct() {



	$('#prod_select option').remove();



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_reports/get_ActiveProduct',



		data: ({ 'id_category': $('#category').length > 0 ? $('#category').val() : 0, 'stock_type': $('#item_type').length > 0 ? ($('#item_type').val() == 1 ? 2 : 1) : '' }),



		dataType: 'json',



		success: function (data) {



			var id = $("#prod_select").val();



			$.each(data, function (key, item) {



				$("#prod_select").append(



					$("<option></option>")



						.attr("value", item.pro_id)



						.attr("data-stock-type", item.stock_type)



						.text(item.product_name)



				);



			});







			$("#prod_select").select2(



				{



					placeholder: "Select Product",



					allowClear: true



				});



			$("#prod_select").select2("val", (id != '' && id > 0 ? id : ''));



		}



	});



}



$('#prod_select').on('change', function () {

	var curRow = $(this).closest('tr');

	if (this.value != '') {



		if (ctrl_page[1] == 'cart') {



			get_Activedesign(curRow,this.value);



			get_weight_range(this.value);



		}



	} else {



		get_order_cart();



	}







});



$('#des_select').on('change', function () {



	if (this.value != '') {



		if (ctrl_page[1] == 'cart') {



			get_order_cart();



		}







	}



	else {



		get_order_cart();



	}



});



$("#wt_select").on('change', function () {



	if (this.value != '') {



		get_order_cart();



	} else {



		get_order_cart();



	}



});





function get_Activedesigns(id_product) {



	$("div.overlay").css("display", "block");



	$('#des_select option').remove();



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_reports/get_Activedesign',



		dataType: 'json',



		data: { 'id_product': id_product },



		success: function (data) {



			var id = $("#id_design").val();



			$.each(data, function (key, item) {



				$("#des_select").append(



					$("<option></option>")



						.attr("value", item.design_no)



						.text(item.design_name)



				);



			});







			$("#des_select").select2(



				{



					placeholder: "Select Design",



					allowClear: true



				});



			$("#des_select").select2("val", (id != '' && id > 0 ? id : ''));



			$("div.overlay").css("display", "none");



		}



	});







}



function get_weight_range(id_product) {



	$('#wt_select option').remove();



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_reports/get_weight_range',



		dataType: 'json',



		data: { 'id_product': id_product },



		success: function (data) {



			var id = $("#wt_range").val();



			$.each(data, function (key, item) {



				$("#wt_select").append(



					$("<option></option>")



						.attr("value", item.id_weight)



						.text(item.name)



				);



			});







			$("#wt_select").select2(



				{



					placeholder: "Weight Range",



					allowClear: true



				});



			$("#wt_select").select2("val", (id != '' && id > 0 ? id : ''));



		}



	});



}



function get_purchase_order_list() {



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/order/purchase_order?nocache=" + my_Date.getUTCSeconds(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			var list = data;



			var oTable = $('#order_list').DataTable();



			oTable.clear().draw();



			if (list != null && list.length > 0) {



				oTable = $('#order_list').dataTable({



					"bDestroy": true,



					"bInfo": true,



					"bFilter": true,



					"bSort": true,



					"order": [[0, "desc"]],



					"dom": 'lBfrtip',



					"buttons": ['excel', 'print'],



					"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },



					"aaData": list,



					"aoColumns": [



						{ "mDataProp": "id_customerorder" },



						{ "mDataProp": "pur_no" },



						{ "mDataProp": "order_date" },



						{

							"mDataProp": function (row, type, val, meta) {



								return '<span class="badge bg-' + row.color + '">' + row.order_status_msg + '</span>';



							},



						},



						{ "mDataProp": "karigar_name" },



						{ "mDataProp": "mobile" },



						{ "mDataProp": "order_pcs" },











						{

							"mDataProp": function (row, type, val, meta) {



								return parseFloat(row.delivered_qty);



							}



						},











						{

							"mDataProp": function (row, type, val, meta) {



								id = row.id_customerorder;



								print_url = base_url + 'index.php/admin_ret_order/get_karigar_receipt/' + id;



								action_content = '<a href="#" onclick="send_karigar_sms(' + id + ',' + row.mobile + ')"  class="btn btn-success" data-toggle="tooltip" title="Send WhatsApp / Email"><i class="fa fa-whatsapp" aria-hidden="true"></i><a href="' + print_url + '" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Vendor acknowledgement "><i class="fa fa-print" ></i></a>';



								return action_content;



							}



						},







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



function get_ActiveKaigar() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_catalog/karigar/active_list',



		dataType: 'json',



		success: function (data) {



			var id = $('#select_karigar').val();



			$.each(data, function (key, item) {



				$("#select_karigar").append(



					$("<option></option>")



						.attr("value", item.id_karigar)



						.text(item.karigar)



				);



			});



			$("#select_karigar").select2(



				{



					placeholder: "Select Karigar",



					allowClear: true



				});







			if ($("#select_karigar").length) {



				$("#select_karigar").select2("val", (id != '' && id > 0 ? id : ''));



			}



			$(".overlay").css("display", "none");



		}



	});



}



function set_cart_status() {



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/cart/order_status?nocache=" + my_Date.getUTCSeconds(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			var list = data;



			var oTable = $('#order_list').DataTable();



			oTable.clear().draw();



			if (list != null && list.length > 0) {



				oTable = $('#order_list').dataTable({



					"bDestroy": true,



					"bInfo": true,



					"bFilter": true,



					"bSort": true,



					"order": [[0, "desc"]],



					"dom": 'lBfrtip',



					"buttons": ['excel', 'print'],



					"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },



					"aaData": list,



					"aoColumns": [



						{ "mDataProp": "id_cart_order" },



						{ "mDataProp": "pur_no" },



						{ "mDataProp": "date_add" },



						{ "mDataProp": "product_name" },



						{ "mDataProp": "design_name" },



						{ "mDataProp": "sub_design_name" },



						{ "mDataProp": "cart_status" },



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



//Order Cart



$('#add_new_customer').on('click', function (e) {



	//$('#confirm-add-customer').modal('toggle');
	if($('#branch_select').val() != '' && $('#branch_select').val() != null){

	$('#demo').offcanvas('show');

	$('#confirm-add').modal('toggle');







	$("#myModalLabel").text('Add Customer');







	$("#add_newcutomer").text('Add');



	$("#cus_first_name").val('');



	$("#cus_mobile").val('');



	$("#cus_mobile").prop("readonly", false);



	$("#id_village").val('');



	$("#sel_village option").remove();



	$("#id_customer").val('');



	$('#id_country').val('');



	$('#id_state').val('');



	$('#state option').remove();



	$('#id_city').val('');



	$('#city option').remove();



	$("#address1").val('');



	$("#address2").val('');



	$("#address3").val('');



	$("#pincode").val('');



	$("#cus_email").val('');



	$('#dl').val('');



	$('#pp').val('');







	// $("#country").select2({



	// 	placeholder: "Enter Country",



	// 	allowClear: true



	// });







	// $("#state").select2({



	// 	placeholder: "Enter State",



	// 	allowClear: true



	// });







	// $("#city").select2({



	// 	placeholder: "Enter City",



	// 	allowClear: true



	// });

	$('#id_country').val($('#branch_id_country').val());
	$('#id_state').val($('#branch_id_state').val());
	$('#id_city').val($('#branch_id_city').val());

	get_country();

	// get_village_list();
}
else {

	$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select the Branch' });

	setTimeout(function () {
		$('.toast').fadeOut();
	}, 2000);
}

});



$("input[name$='cus[cus_type]']").click(function () {



	var cus_type = $(this).val();



	if (cus_type == 1) {



		$(".customer").css("display", "block");



		$(".gst").css("display", "none");



		$(".company").css("display", "none");







	}



	else {



		$(".customer").css("display", "none");



		$(".gst").css("display", "block");



		$(".company").css("display", "block");







	}



});



$("#aadharid,#ed_cus_aadhar").keyup(function () {



	var value = $(this).val();



	value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join("-");



	$(this).val(value);



});







$("#aadharid,#ed_cus_aadhar").on('blur onchange', function (event) {



	event.preventDefault();



	var value = $(this).val();



	var maxLength = $(this).attr("maxLength");



	if (value.length != maxLength) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Aadhar No.." });



		$('#aadharid,#ed_cus_aadhar').val('');



	}



});





$("#cus_first_name").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validFirstName = /^[a-zA-Z][a-zA-Z ]*$/;



	if (!(validFirstName.test(value))) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Firstname.." });



		$('#cus_first_name').val('');

		$('#cus_first_name').focus();



	}



});



$("#cus_email").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validEmail = /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/;



	if (!validEmail.test(value)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Email.." });



		$('#cus_email').val('');

		$('#cus_email').focus();



	}



});



$("#pin_code_add").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validPinCode = /^[1-9]{1}[0-9]{2}[0-9]{3}$/gm;



	if (!validPinCode.test(value)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Pincode.." });



		$('#pin_code_add').val('');

		$('#pin_code_add').focus();



	}



});



$("#pp").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validPassport = /^[A-PR-WY][1-9]\d\s?\d{4}[1-9]$/;



	if (!validPassport.test(value)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Passport No.." });



		$('#pp').val('');

		$('#pp').focus();



	}



});





$("#address1").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validAddress = /^[#.0-9a-zA-Z\s,/-]+$/;



	if (!validAddress.test(value)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Address.." });



		$('#address1').val('');

		$('#address1').focus();



	}



});



$("#address2").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validAddress = /^[#.0-9a-zA-Z\s,/-]+$/;



	if (!validAddress.test(value)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Address.." });



		$('#address2').val('');

		$('#address2').focus();



	}



});



$("#address3").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validAddress = /^[#.0-9a-zA-Z\s,/-]+$/;



	if (!validAddress.test(value)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Address.." });



		$('#address3').val('');

		$('#address3').focus();



	}



});



$("#dl").on('change', function (event) {



	event.preventDefault();



	var value = $(this).val();



	validDrivinglicence = /^[a-zA-Z0-9-_\!\@\#\$\%\^&\*]{5,16}$/;



	if (!validDrivinglicence.test(value)) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Driving Licence.." });



		$('#dl').val('');

		$('#dl').focus();



	}



});



$('.pan_no').on('change', function () {



	if (this.value != '') {



		var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;



		if (!regexp.test(this.value)) {



			$("#pan").val("");



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid PAN No.." });



			$("#pan").focus();



		}



	}



});



$('#edit_customer').on('click', function () {



	if ($('#id_customer').val() != '' && $('#id_customer').val() != undefined) {



		get_customer();



	}



	else if ($('#cus_id').val() != '' && $('#cus_id').val() != undefined) {



		get_customer();



	}



});



function get_customer() {





	// setTimeout(function(){

	my_Date = new Date();



	$.ajax({



		type: "POST",



		url: base_url + "index.php/admin_ret_estimation/get_customer?nocache=" + my_Date.getUTCSeconds(),



		cache: false,



		dataType: "JSON",



		data: { 'id_customer': $('#cus_id').val() },



		success: function (data) {







			$('#id_village').val(data.id_village);



			$('#cus_first_name').val(data.firstname);



			$('#cus_mobile').val(data.mobile);



			$('#cus_email').val(data.email);



			$('#country').val(data.id_country);



			$('#city').val(data.id_city);



			$('#state').val(data.id_state);





			$('#id_country').val(data.id_country);



			$('#id_city').val(data.id_city);



			$('#id_state').val(data.id_state);



			$('#address1').val(data.address1);



			$('#address2').val(data.address2);



			$('#address3').val(data.address3);



			$('#pin_code_add').val(data.pincode);



			$('#gst_no').val(data.gst_number);



			$('#pan').val(data.pan_no);



			$('#aadharid').val(data.aadharid);



			$('#professionval').val(data.id_profession);



			$("#dl").val(data.dl_no);



			$('#pp').val(data.pp_no);



			$('#date_of_birth').val(data.date_of_birth);



			$('#date_of_wed').val(data.date_of_wed);



			if (data.cus_type == 1) {



				$('#cus_type1').attr('checked', true);



			} else {



				$('#cus_type2').attr('checked', true);



				$('.gst_no').show();



			}





			if (data.is_vip == 1) {

				$('#vip1').attr('checked', true);

			} else {

				$('#vip0').attr('checked', true);

			}









			setTimeout(function () {

				if (data.pincode != '') {

					get_villages_by_pincode(data.pincode)

				}

				get_profession();

			}, 1000)



		}



	});



	get_country();











	// $('#confirm-add-customer').modal('show');

	$('#confirm-add').modal('show');

	// })













}



function get_stones() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_tagging/getStoneItems',



		dataType: 'json',



		success: function (data) {



			stones = data;



		}







	});



}



function get_stone_types() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_tagging/getStoneTypes',



		dataType: 'json',



		success: function (data) {



			stone_types = data;



		}



	});



}



function get_ActiveUOM() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_tagging/get_ActiveUOM',



		dataType: 'json',



		success: function (data) {



			uom_details = data;



			console.log(uom_details);



		}



	});



}



function category_change(row, cat_id, id_purity = 0) {



	if (cat_id > 0) {



		get_cat_purity(row, cat_id, id_purity);



		get_metal_type(row, cat_id);



		var CatData = filterByCatId('id_ret_category', cat_id);



		if (CatData) {



			row.find(".id_metal").val(CatData.id_metal);



			row.find(".tax_group").val(CatData.tgrp_id);



			getTaxGroupDetail(row, CatData.tgrp_id);



		}







	} else {



		row.find(".id_metal").val("");



		row.find(".purity option").remove();



		row.find(".id_purity").val("");



		row.find(".order_rate").val(0);



		calculate_orderSale_value();







	}



}



function filterByCatId(prop, value) {



	var filtered = [];



	for (var i = 0; i < CategorysArr.length; i++) {



		var obj = CategorysArr[i];



		for (var key in obj) {



			if (key == prop && obj[key] == value) {



				return obj;



			}



		}



	}



}



function get_orderedit_data() {



	$(".overlay").css('display', 'block');



	let order_id = $("#order_id").val();



	if (order_id > 0) {



		my_Date = new Date();



		$.ajax({



			type: 'POST',



			url: base_url + 'index.php/admin_ret_order/order/ordered_list/' + order_id + '?nocache=' + my_Date.getUTCSeconds(),



			dataType: 'json',



			success: function (data) {



				$(data).each(function (index, odetails) {



					create_new_empty_cus_order_row('edit', odetails);



				});



			}



		});



	}



	$(".overlay").css('display', 'none');



}



/* Stone modal function*/



function create_new_empty_est_cus_stone_item(curRow, id) {



	if (curRow != undefined) {



		$('#custom_active_id').val(curRow.closest('tr').attr('id'));



		console.log("TABLE ID : ", curRow.closest('table').attr('id'));



		$('#custom_active_table').val(curRow.closest('table').attr('id'));



	}



	console.log(curRow);



	var row = '';







	var catRow = $('#custom_active_id').val();



	console.log(catRow);



	var row_st_details = $('#' + catRow).find('.stone_details').val();



	console.log(row_st_details);



	if (row_st_details != '' && row_st_details != '[]' && curRow != undefined) {



		$('#estimation_stone_cus_item_details tbody').empty();



		var stone_details = JSON.parse(row_st_details);



		console.log(stone_details);



		$.each(stone_details, function (pkey, pitem) {



			var stones_list = '';



			var stones_type_list = '';



			var uom_list = '';



			var html = '';



			var cal_type = pitem.stone_cal_type;



			$.each(stones, function (key, item) {



				var selected = "";



				if (item.stone_id == pitem.stone_id) {



					selected = "selected='selected'";



				}



				stones_list += "<option value='" + item.stone_id + "' " + selected + ">" + item.stone_name + "</option>";



			});







			$.each(stone_types, function (pkey, item) {



				var st_type_selected = "";







				if (item.id_stone_type == pitem.stones_type) {



					st_type_selected = "selected='selected'";



				}



				stones_type_list += "<option value='" + item.id_stone_type + "' " + st_type_selected + ">" + item.stone_type + "</option>";



			});



			$.each(uom_details, function (pkey, item) {



				var uom_selected = "";







				if (item.uom_id == pitem.stone_uom_id) {



					uom_selected = "selected='selected'";



				}



				uom_list += "<option value='" + item.uom_id + "' " + uom_selected + ">" + item.uom_name + "</option>";



				console.log(uom_list);



			});





			let option_delete = '<a href="#" onClick="remove_row($(this).closest(\'tr\').remove());" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>';



			row += '<tr>'



				+ '<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]"  value="' + (pitem.show_in_lwt == 1 ? 1 : 0) + '" ' + (pitem.show_in_lwt == 1 ? 'checked' : '') + ' ></td>'



				+ '<td><select class="stones_type form-control" name="est_stones_item[stones_type][]"  >' + stones_type_list + '</select></td>'



				+ '<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"  >' + stones_list + '</select></td>'



				+ '<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]"  value="' + pitem['stone_pcs'] + '" style="width: 100%;"/></td>'



				+ '<td><div class="input-group"><input class="stone_wt form-control" type="number"  name="est_stones_item[stone_wt][]" value="' + pitem['stone_wt'] + '" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[stone_uom_id][]"  >' + uom_list + '</select></span></div></td>'



				+ '<td><div class="form-group"><input class="stone_cal_type" type="radio"  name="est_stones_item[cal_type][' + pkey + ']" value="1" ' + (cal_type == 1 ? 'checked' : '') + '> By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + pkey + ']" ' + (cal_type == 2 ? 'checked' : '') + ' class="stone_cal_type" value="2">By Pcs</div></td>'



				+ '<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]"  value="' + pitem['stone_rate'] + '"  style="width:100%;"/></td>'



				+ '<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="' + pitem['stone_price'] + '"  style="width:100%;" readonly/></td>'



				+ '<td>' + option_delete + '</td></tr>';



		});







	}



	else {



		var stones_list = "<option value=''>--Select Stone--</option>";



		var stones_type = "<option value=''>--Stone Type--</option>";



		var uom_list = "";



		$.each(stones, function (pkey, pitem) {



			stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";



		});



		$.each(stone_types, function (pkey, pitem) {



			stones_type += "<option value='" + pitem.id_stone_type + "'>" + pitem.stone_type + "</option>";



		});



		$.each(uom_details, function (pkey, pitem) {



			uom_list += "<option value='" + pitem.uom_id + "'>" + pitem.uom_name + "</option>";



		});



		var rowId = $('#estimation_stone_cus_item_details tbody tr').length;



		var active_row = new Date().getTime();



		row += '<tr id="' + active_row + '">'







			+ '<td><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="1" checked></td>'



			+ '<td><select class="stones_type form-control" name="est_stones_item[stones_type][]">' + stones_type + '</select></td>'



			+ '<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"></select></td>'



			+ '<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="" style="width: 100%;"/></td>'



			+ '<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[stone_uom_id][]">' + uom_list + '</select></span></div></td>'



			+ '<td><div class="form-group"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type][' + rowId + ']" value="1" checked="true"> By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + rowId + ']" class="stone_cal_type" value="2">By Pcs</div></td>'



			+ '<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value=""  style="width:80%;"/></td>'



			+ '<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value=""  style="width:100%;" /></td>'



			+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	}



	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);



	$('#cus_stoneModal').modal('show');



}



$(document).on('change', ".show_in_lwt", function () {



	if ($(this).is(":checked")) {



		$(this).closest('tr').find('.show_in_lwt').val(1);



	} else {



		$(this).closest('tr').find('.show_in_lwt').val(0);



	}



});



$(document).on('change', ".stones_type", function () {



	var row = $(this).closest('tr');







	var stone_type = this.value;







	row.find('.stone_id').html('');







	var stones_list = "<option value=''>-Select Stone-</option>";







	$.each(stones, function (pkey, pitem) {







		if (pitem.stone_type == stone_type) {







			stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";







		}







	});







	row.find('.stone_id').append(stones_list);







});



$(document).on('keyup', ".stone_pcs,.stone_wt,.stone_rate", function () {



	calculate_stone_amount();



});



$(document).on('change', ".stone_price", function () {



	$('#estimation_stone_cus_item_details > tbody tr').each(function (idx, row) {



		curRow = $(this);



		var stone_rate = 0;



		var stone_amt = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '') ? 0 : curRow.find('.stone_price').val();



		var stone_pcs = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '') ? 0 : curRow.find('.stone_pcs').val();



		var stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();



		if (curRow.find('input[type=radio]:checked').val() == 2) {



			stone_rate = parseFloat(parseFloat(stone_amt) / parseFloat(stone_pcs)).toFixed(2);



		}



		curRow.find('.stone_rate').val(stone_rate);



	});



	calculate_stone_amount();



});



$('#estimation_stone_cus_item_details > tbody tr input[type=radio]').on('change', function () {



	calculate_stone_amount();



});



$(document).on('change', ".stone_cal_type", function () {



	$('#estimation_stone_cus_item_details > tbody tr').each(function (idx, row) {



		curRow = $(this);



		if (curRow.find('input[type=radio]:checked').val() == 1) {



			curRow.find('.stone_wt').attr('readonly', false);



			curRow.find('.stone_uom_id').attr('disabled', false);



			curRow.find('.stone_price').attr('readonly', true);



		} else {



			//curRow.find('.stone_wt').val(0);



			curRow.find('.stone_wt').attr('readonly', true);



			curRow.find('.stone_uom_id').attr('disabled', true);



			curRow.find('.stone_price').attr('readonly', false);



		}



	});



	calculate_stone_amount();



});



function calculate_stone_amount() {



	$('#estimation_stone_cus_item_details > tbody tr').each(function (idx, row) {



		curRow = $(this);



		var stone_amt = 0;



		var stone_pcs = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '') ? 0 : curRow.find('.stone_pcs').val();



		var stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();



		var stone_rate = (isNaN(curRow.find('.stone_rate').val()) || curRow.find('.stone_rate').val() == '') ? 0 : curRow.find('.stone_rate').val();



		//stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);



		console.log(curRow.find('input[type=radio]:checked').val());



		if (curRow.find('input[type=radio]:checked').val() == 1) {



			stone_amt = parseFloat(parseFloat(stone_wt) * parseFloat(stone_rate)).toFixed(2);



		} else {



			stone_amt = parseFloat(parseFloat(stone_pcs) * parseFloat(stone_rate)).toFixed(2);



		}



		curRow.find('.stone_price').val(stone_amt);



	});



}



$('#cus_stoneModal .modal-body #create_stone_item_details').on('click', function () {



	if (validateStoneCusItemDetailRow()) {







		create_new_empty_est_cus_stone_item();







	} else {







		alert("Please fill required fields");







	}







});





$(document).on('keyup', '.stone_pcs,.stone_wt,.stone_rate ,.stone_price ', function (e) {



	calculate_est_stone_Amount();



});

function calculate_est_stone_Amount() {





	var stn_pcs = 0;



	var stn_wt = 0;



	var stn_amt = 0;





	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details > tbody > tr').each(function (index, tr) {

		stn_pcs += (isNaN($(this).find('.stone_pcs').val()) || $(this).find('.stone_pcs').val() == '' ? 0 : parseFloat($(this).find('.stone_pcs').val()));

		stn_wt += (isNaN($(this).find('.stone_wt').val()) || $(this).find('.stone_wt').val() == '' ? 0 : parseFloat($(this).find('.stone_wt').val()));

		stn_amt += (isNaN($(this).find('.stone_price').val()) || $(this).find('.stone_price').val() == '' ? 0 : parseFloat($(this).find('.stone_price').val()));

	});





	$('.stn_pcs').html(parseFloat(stn_pcs).toFixed(0));



	$('.stn_wt').html(parseFloat(stn_wt).toFixed(2));



	$('.stn_amt').html(parseFloat(stn_amt).toFixed(2));



}



$('#cus_stoneModal  #close_stone_details').on('click', function () {







	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();







});







function validateStoneCusItemDetailRow() {



	var row_validate = true;



	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function (index, tr) {



		if ($(this).find('.stone_id').val() == "" || $(this).find('.stone_pcs').val() == "" || $(this).find('.stone_wt').val() == "" || $(this).find('.stone_rate').val() == "" || $(this).find('.stone_price').val() == "" || $(this).find('.stone_uom_id').val() == "") {



			row_validate = false;



		}



	});



	return row_validate;



}



$('#cus_stoneModal  #update_stone_details').on('click', function () {



	if (validateStoneCusItemDetailRow()) {



		var stone_details = [];



		var stone_price = 0;



		var stone_weight = 0;



		var certification_price = 0;



		$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details > tbody  > tr').each(function (index, tr) {



			stone_price += parseFloat($(this).find('.stone_price').val());



			let stone_id = $(this).find('.stone_uom_id').val();



			let st_weight = $(this).find('.stone_wt').val();



			var stone_type = $(this).find('.stones_type').val();



			if ($(this).find('.show_in_lwt').is(":checked")) {



				// if ((stone_type == 1)) //For Carat Need to convert into gram

				if(($(this).find('.stone_uom_id').val() == 6)) //For Carat Need to convert into gram

				{



					stone_weight = parseFloat(parseFloat(stone_weight) + parseFloat(parseFloat(st_weight) / parseFloat(5))).toFixed(3);



				}



				else {



					stone_weight = parseFloat(parseFloat(stone_weight) + parseFloat(st_weight)).toFixed(3);



				}



			}



			stone_details.push({



				'show_in_lwt': $(this).find('.show_in_lwt').val(),



				'stone_id': $(this).find('.stone_id').val(),



				'stones_type': $(this).find('.stones_type').val(),



				'stone_pcs': $(this).find('.stone_pcs').val(),



				'stone_wt': $(this).find('.stone_wt').val(),



				'stone_cal_type': $(this).find('input[type=radio]:checked').val(),



				'stone_price': $(this).find('.stone_price').val(),



				'stone_rate': $(this).find('.stone_rate').val(),



				'stone_type': $(this).find('.stone_type').val(),



				'stone_uom_id': $(this).find('.stone_uom_id').val()



			});



		});



		console.log(stone_details);



		$('#cus_stoneModal').modal('toggle');



		var catRow = $('#custom_active_id').val();



		$('#' + catRow).find('.stone_details').val(JSON.stringify(stone_details));



		$('#' + catRow).find('.stn_amt').val(stone_price);



		$('#' + catRow).find('.less_wt').val(stone_weight);



		//$('#'+catRow).find('.price').val(certification_price);



		var row = $('.' + catRow).closest('tr');



		calculate_orderSale_value();



		calcluate_cur_repair_total();



		$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



	}



	else {



		alert('Please Fill The Required Details');



	}



});



function create_new_empty_est_cus_charges_item(curRow, id) {



	if (curRow != undefined) {



		$('#custom_active_id').val(curRow.closest('tr').attr('id'));



	}



	var row = "";







	var catRow = $('#custom_active_id').val();







	var row_charges_details_details = $('#' + catRow).find('.charges_details').val();



	console.log(row_charges_details_details);



	$('#cus_other_charges_modal .modal-body').find('#estimation_other_charge_cus_item_details tbody').empty();







	if (row_charges_details_details != '' && row_charges_details_details != '[]' && curRow != undefined) {



		var cus_charges_details = JSON.parse(row_charges_details_details);



		console.log(cus_charges_details);



		$.each(cus_charges_details, function (pkey, pitem) {



			var charge_list = "";



			$.each(other_charges_details, function (pkey, item) {



				var selected = "";



				if (item.id_charge == pitem.id_charge) {



					selected = "selected='selected'";



				}



				charge_list += "<option value='" + pitem.id_charge + "'  " + selected + ">" + item.name_charge + "</option>";



			});



			row += '<tr><td><select class="id_charge" name="est_stones_item[id_charge][]">' + charge_list + '</select></td><td><input class="value_charge" type="number" name="est_stones_item[value_charge][]" value="' + pitem['value_charge'] + '" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



		});



	}



	else {



		add_new_empty_est_cus_charges_item();



	}



	$('#cus_other_charges_modal .modal-body').find('#estimation_other_charge_cus_item_details tbody').append(row);



	$('#cus_other_charges_modal').modal('show');



}



function add_new_empty_est_cus_charges_item() {



	let row = "";



	var charge_list = "<option value=''>-Select Charge Type-</option>";



	$.each(other_charges_details, function (pkey, pitem) {



		charge_list += "<option value='" + pitem.id_charge + "'>" + pitem.name_charge + "</option>";



	});



	row += '<tr><td><select class="id_charge" name="est_stones_item[id_charge][]">' + charge_list + '</select></td><td><input type="number" class="value_charge" name="est_stones_item[value_charge][]" value="" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	$('#cus_other_charges_modal .modal-body').find('#estimation_other_charge_cus_item_details tbody').append(row);



}



$(document).on('change', ".id_charge", function (e) {



	var id_charge = this.value;



	var row = $(this).closest('tr');



	$.each(other_charges_details, function (pkey, pitem) {



		if (id_charge == pitem.id_charge) {



			row.find('.value_charge').val(pitem.value_charge);;



		}



	});



});



$('#cus_other_charges_modal .modal-body #add_new_charge').on('click', function () {



	if (validatecusOtherChargeDetailRow()) {



		add_new_empty_est_cus_charges_item();



	} else {



		alert("Please fill required fields");



	}



});



function validatecusOtherChargeDetailRow() {



	var row_validate = true;



	$('#cus_other_charges_modal .modal-body #estimation_other_charge_cus_item_details> tbody  > tr').each(function (index, tr) {



		if ($(this).find('.id_charge').val() == "" || $(this).find('.value_charge').val() == '') {



			row_validate = false;



		}



	});



	return row_validate;



}



$('#cus_other_charges_modal  #update_charge_details').on('click', function () {







	if (validatecusOtherChargeDetailRow) {



		var charge_details = [];



		var value_charge = 0;



		$('#cus_other_charges_modal .modal-body #estimation_other_charge_cus_item_details> tbody  > tr').each(function (index, tr) {



			value_charge += parseFloat($(this).find('.value_charge').val());



			charge_details.push({ 'value_charge': $(this).find('.value_charge').val(), 'id_charge': $(this).find('.id_charge').val() });



		});



		$('#cus_other_charges_modal').modal('toggle');



		var catRow = $('#custom_active_id').val();



		$('#' + catRow).find('.charges_details').val(charge_details.length > 0 ? JSON.stringify(charge_details) : '');



		$('#' + catRow).find('.value_charge').val(value_charge);



		var row = $('#' + catRow).closest('tr');



		calculate_orderSale_value();



		$('#cus_other_charges_modal .modal-body').find('#estimation_other_charge_cus_item_details tbody').empty();



	}







	else {



		alert("Please fill all required fields");



	}



});



//Repair Other Item Details



function get_category() {



	$("div.overlay").css("display", "block");



	$("#category option").remove();



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_catalog/category/active_category',



		dataType: 'json',



		data: { 'id_metal': $('#metal').val() },



		success: function (data) {







			$.each(data, function (key, item) {



				$('#category').append(



					$("<option></option>")



						.attr("value", item.id_ret_category)



						.text(item.name)



				);



			});



			$("#category").select2({



				placeholder: "Select Category",



				allowClear: true



			});



			$("#category").select2("val", "");



		}



	});



	$("div.overlay").css("display", "none");



}



$('#category').on('change', function () {



	if (this.value != '') {



		get_ActiveProduct();



		get_category_purity();



	}



});



function get_category_purity() {



	$('#purity option').remove();



	$(".overlay").css('display', 'block');



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_catalog/category/cat_purity',



		dataType: 'json',



		data: {



			'id_category': $('#category').val()



		},



		success: function (data) {



			var id_purity = $('#category').val();



			$.each(data, function (key, item) {



				$('#purity').append(



					$("<option></option>")



						.attr("value", item.id_purity)



						.text(item.purity)



				);



			});



			$("#purity").select2({



				placeholder: "Select Purity",



				allowClear: true



			});



			$("#purity").select2("val", (id_purity != '' && id_purity > 0 ? id_purity : ''));



			$(".overlay").css("display", "none");



		}



	});



}



$('#gross_wt').on('keyup', function () {



	calculate_item_row_details();



});



$('#wast_per').on('keyup', function () {

	if ($(this).val() < 0) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Entry valid VA%.' });



		$('#wast_per').val('');

		$('#wast_per').focus();

	}



	if ($(this).val() > 100) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Entry valid VA%.' });



		$('#wast_per').val('');

		$('#wast_per').focus();

	}

})

$('#mc_value').on('keyup', function () {

	if ($(this).val() < 0) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Entry Mc.' });



		$('#mc_value').val('');

		$('#mc_value').focus();

	}

})

$('#service_charge').on('keyup', function () {

	if ($(this).val() < 0) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Entry Valid Amount.' });



		$('#service_charge').val('');

		$('#service_charge').focus();

	}

})



function calculate_item_row_details() {



	var gross_wt = (isNaN($('#gross_wt').val()) || $('#gross_wt').val() == '' ? 0 : $('#gross_wt').val());



	if (gross_wt < 0) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Entry valid Weight.' });



		$('#gross_wt').val('');

		$('#gross_wt').focus();

	}



	var less_wt = (isNaN($('#less_wt').val()) || $('#less_wt').val() == '' ? 0 : $('#less_wt').val());



	var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt)).toFixed(3);



	$('#net_wt').val(net_wt);



	$('#less_wt').val(less_wt);



}



$('#add_repair_item').on('click', function () {



	if ($('#category').val() == '' || $('#category').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select The Category.' });



	} else if ($('#purity').val() == '' || $('#purity').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select The Purity.' });



	}



	else if ($('#prod_select').val() == '' || $('#prod_select').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select The Product.' });



	}



	else if ($('#des_select').val() == '' || $('#des_select').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select The Product.' });



	}



	else if ($('#sub_design_select').val() == '' || $('#sub_design_select').val() == null) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select The Product.' });



	}



	else if ($('#gross_wt').val() < 0 || $('#gross_wt').val() == '') {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Weight.' });



	}



	else {



		add_repair_other_items();



	}



});



function add_repair_other_items() {



	var row = '';



	row += '<tr>'



		+ '<td><input type="hidden" class="item_type" name="order_item[item_type][]" value="' + $('#item_type').val() + '">' + $('#item_type option:selected').text() + '</td>'



		+ '<td><input type="hidden" class="id_section" name="order_item[id_section][]" value="' + $('#section').val() + '">' + $('#section option:selected').text() + '</td>'



		+ '<td><input type="hidden" class="category" name="order_item[id_ret_category][]" value="' + $('#category').val() + '">' + $('#category option:selected').text() + '</td>'



		+ '<td><input type="hidden" class="purity" name="order_item[id_purity][]" value="' + $('#purity').val() + '">' + $('#purity option:selected').text() + '</td>'



		+ '<td><input type="hidden" class="id_product" name="order_item[id_product][]" value="' + $('#prod_select').val() + '">' + $('#prod_select option:selected').text() + '</td>'



		+ '<td><input type="hidden" class="id_design" name="order_item[id_design][]" value="' + $('#des_select').val() + '">' + $('#des_select option:selected').text() + '</td>'



		+ '<td><input type="hidden" class="id_sub_design" name="order_item[id_sub_design][]" value="' + $('#sub_design_select').val() + '">' + $('#sub_design_select option:selected').text() + '</td>'



		+ '<td><input type="hidden" class="pcs" name="order_item[pcs][]" value="' + $('#pcs').val() + '">' + parseFloat($('#pcs').val()).toFixed(0) + '</td>'



		+ '<td><input type="hidden" class="gross_wt" name="order_item[gross_wt][]" value="' + $('#gross_wt').val() + '">' + parseFloat($('#gross_wt').val()).toFixed(3) + '</td>'



		+ '<td><input type="hidden" class="less_wt" name="order_item[less_wt][]" value="' + $('#less_wt').val() + '"><input type="hidden" class="stone_details" name="order_item[stone_details][]" value=' + $('#stone_details').val() + '>' + parseFloat($('#less_wt').val()).toFixed(3) + '</td>'



		+ '<td><input type="hidden" class="net_wt" name="order_item[net_wt][]" value="' + $('#net_wt').val() + '">' + parseFloat($('#net_wt').val()).toFixed(3) + '</td>'



		+ '<td><input type="hidden" class="wast_per" name="order_item[wast_per][]" value="' + $('#wast_per').val() + '">' + $('#wast_per').val() + '</td>'



		+ '<td><input type="hidden" class="mc_type" name="order_item[mc_type][]" value="' + $('#mc_type').val() + '"><input type="hidden" class="mc_value" name="order_item[mc_value][]" value="' + $('#mc_value').val() + '">' + $('#mc_value').val() + '</td>'



		+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



		+ '</tr>';



	$('#custrepair_item_detail tbody').append(row);



	empty_repair_other_items();



}



function empty_repair_other_items() {



	if ($('#branch_select').length > 0) {



		$('#branch_select').select2("val", "");



	}



	$('#section').select2("val", "");



	$('#category').select2("val", "");



	$('#purity').select2("val", "");



	$('#prod_select').select2("val", "");



	$('#des_select').select2("val", "")



	$('#sub_design_select').select2("val", "")



	$('#weight').val("");



	$('#pcs').val("");



	$('#wast_per').val("");



	$('#mc_value').val("");



	$('#gross_wt').val("");



	$('#less_wt').val("");



	$('#service_charge').val("");



	$('#net_wt').val("");



	$('.tot_blc_pcs').val(0);

	$('.tot_blc_gwt').val(0);

	$('.blc_pcs').html(0);

	$('.blc_gwt').html(0);









}



$('#save_repair_item').on('click', function () {



	if ($('#custrepair_item_detail tbody >tr').length > 0) {



		$('#id_orderdetails').val(ctrl_page[3]);



		$("div.overlay").css("display", "block");



		$('#save_repair_item').prop('disabled', true);



		var form_data = $('#order_submit').serialize();



		var url = base_url + "index.php/admin_ret_order/update_repair_order_other_details?nocache=" + my_Date.getUTCSeconds();



		$.ajax({



			url: url,



			data: form_data,



			type: "POST",



			dataType: "JSON",



			success: function (data) {



				if (data.status) {



					$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });



					window.location.replace(base_url + 'index.php/admin_ret_order/repair_order/repair_order_status');



				}



				else {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });



				}







				$("div.overlay").css("display", "none");



			},



			error: function (error) {



				$("div.overlay").css("display", "none");



			}



		});



		$('#create_repair_order').prop('disabled', false);



	} else {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Fill The Required Fields.." });



	}



});



$('.add_lwt').on('click', function () {



	openStoneModal();



});



function openStoneModal() {



	console.log(modalStoneDetail.length);



	if (modalStoneDetail.length > 0) {



		$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



		$('#cus_stoneModal').modal('show');



		$.each(modalStoneDetail, function (key, item) {



			console.log(item);



			if (item) {



				create_new_empty_stone_item(item);



			}



		})



	}



	else {



		$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



		$('#cus_stoneModal').modal('show');



		if ($('#cus_stoneModal tbody >tr').length == 0) {



			create_new_empty_stone_item();



		}



	}



}



function create_new_empty_stone_item(stn_data = []) {



	var row = '';







	var stones_list = "<option value=''> -Select Stone- </option>";



	var stones_type = "<option value=''>-Stone Type-</option>";



	var uom_list = "<option value=''>-UOM-</option>";



	$.each(stones, function (pkey, pitem) {



		stones_list += "<option value='" + pitem.stone_id + "' " + (stn_data ? (pitem.stone_id == stn_data.stone_id ? 'selected' : '') : '') + ">" + pitem.stone_name + "</option>";



	});



	$.each(uom_details, function (pkey, pitem) {



		uom_list += "<option value='" + pitem.uom_id + "' " + (stn_data ? (pitem.uom_id == stn_data.stone_uom_id ? 'selected' : '') : '') + ">" + pitem.uom_name + "</option>";



	});



	$.each(stone_types, function (pkey, pitem) {



		stones_type += "<option value='" + pitem.id_stone_type + "' " + (stn_data ? (pitem.id_stone_type == stn_data.stones_type ? 'selected' : '') : '') + ">" + pitem.stone_type + "</option>";



	});



	var show_in_lwt = (stn_data ? stn_data.show_in_lwt : '');





	var stone_pcs = (stn_data ? (stn_data.stone_pcs == undefined ? '' : stn_data.stone_pcs) : '');



	var stone_wt = (stn_data ? (stn_data.stone_wt == undefined ? '' : stn_data.stone_wt) : '');



	var rate = (stn_data ? (stn_data.stone_rate == undefined ? 0 : stn_data.stone_rate) : 0);



	var price = (stn_data ? (stn_data.stone_price == undefined ? 0 : stn_data.stone_price) : 0);







	var cal_type = (stn_data ? (stn_data.stone_cal_type == undefined ? 1 : stn_data.stone_cal_type) : 1);



	var row_cls = $('#estimation_stone_cus_item_details tbody tr').length;







	row = '<tr id="' + $('#estimation_stone_cus_item_details tbody tr').length + '" class="st_' + $('#estimation_stone_cus_item_details tbody tr').length + '">'



		// + '<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:100px;"><option value="">-Select-</option><option value=1 ' + (show_in_lwt == 1 ? 'selected' : '') + '>Yes</option><option value=0 ' + (show_in_lwt == 0 ? 'selected' : '') + '>No</option></select></td>'



		+ '<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]"  value="' + (show_in_lwt == 1 ? 1 : 0) + '" ' + (show_in_lwt == 1 || show_in_lwt == undefined ? 'checked' : '') + ' ></td>'



		+ '<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" style="width:100px;">' + stones_type + '</select></td>'



		+ '<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" style="width:100px;">' + stones_list + '</select><input type="hidden" class="stone_type" value=""></td>'



		+ '<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="' + stone_pcs + '" style="width: 60px;"/></td>'



		+ '<td><div class="input-group" style="width:159px;"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="' + stone_wt + '" style="width: 78px;"/><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">' + uom_list + '</select></span></div></td>'



		+ '<td><div class="form-group" style="width: 100px;"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type][' + parseFloat(row_cls + 1) + ']" value="1" ' + (cal_type == 1 ? 'checked' : '') + '>Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + parseFloat(row_cls + 1) + ']" class="stone_cal_type" value="2" ' + (cal_type == 2 ? 'checked' : '') + '>Pcs</div></td>'



		+ '<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="' + rate + '" /></td>'



		+ '<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="' + price + '" "/></td>'



		+ '<td style="width: 100px;"><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';







	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);







	$("#cus_stoneModal").on('shown.bs.modal', function () {



		$(this).find('.show_in_lwt').focus();



	});







}



function remove_row(curRow) {



	curRow.remove();



	calculate_est_stone_Amount();



}





$(document).on('change', ".stone_id", function () {



	let stone_id_val = $(this).val();



	var row = $(this).closest('tr');



	$.each(stones, function (pkey, stItem) {



		if (stone_id_val == stItem.stone_id) {



			if (stItem.st_id == 0) {



				console.log("Diamond");



				row.find('.show_in_lwt').prop('checked', true);



				row.find('.show_in_lwt').val(1);



			} else if (stItem.st_id == 1) {



				console.log("Others");



				row.find('.show_in_lwt').prop('checked', false);



				row.find('.show_in_lwt').val(0);



			}



		}



	});



});



$(document).on('input', ".stone_pcs,.stone_wt,.stone_rate", function () {



	calculate_stone_amount();



});





$(document).on('change', '.stone_pcs,.stone_wt', function (e) {

	// curRow = $(this);



	var curRow = $(this).closest('tr');



	var stn_pcs = (curRow.find('.stone_pcs').val() ? curRow.find('.stone_pcs').val() : 0);

	var stn_wt = (curRow.find('.stone_wt').val() ? curRow.find('.stone_wt').val() : 0);



	if (stn_wt < 0) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Valid Weight.' });



		curRow.find('.stone_wt').val('');



		curRow.find('.stone_wt').focus();



		return false;

	} else if (stn_pcs < 0) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Valid Pcs.' });



		curRow.find('.stone_pcs').val('');



		curRow.find('.stone_pcs').focus();



		return false;

	}

});



function calculate_stone_amount() {



	$('#estimation_stone_cus_item_details > tbody tr').each(function (idx, row) {



		curRow = $(this);



		var stone_amt = 0;



		var stone_pcs = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '') ? 0 : curRow.find('.stone_pcs').val();



		var stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();



		var stone_rate = (isNaN(curRow.find('.stone_rate').val()) || curRow.find('.stone_rate').val() == '') ? 0 : curRow.find('.stone_rate').val();





		if (curRow.find('input[type=radio]:checked').val() == 1) {



			stone_amt = parseFloat(parseFloat(stone_wt) * parseFloat(stone_rate)).toFixed(2);



		} else {



			stone_amt = parseFloat(parseInt(stone_pcs) * parseFloat(stone_rate)).toFixed(2);



		}



		//stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);



		curRow.find('.stone_price').val(stone_amt);



	});



}



$(document).on('click', '.create_stone_item_details', function (e) {



	if (validateStoneCusItemDetailRow()) {



		create_new_stone_row();



	} else {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Please Fill The Required Fields.." });



	}



});



function validateStoneCusItemDetailRow() {



	var row_validate = true;



	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function (index, tr) {



		if ($(this).find('.stone_id').val() == "" || $(this).find('.stone_pcs').val() == "" || $(this).find('.stone_wt').val() == "" || $(this).find('.stone_rate').val() == "" || $(this).find('.stone_price').val() == "" || $(this).find('.stone_uom_id').val() == "") {



			row_validate = false;



		}



	});



	return row_validate;



}



function create_new_stone_row() {



	var stones_list = "<option value=''>-Select Stone-</option>";



	var stones_type = "<option value=''>-Stone Type-</option>";



	var uom_list = "<option value=''>-UOM-</option>";







	var length = (($('#estimation_stone_cus_item_details tbody tr').length) + 1);







	console.log('length:' + length);







	$.each(stones, function (pkey, pitem) {



		stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";



	});



	$.each(stone_types, function (pkey, pitem) {



		stones_type += "<option value='" + pitem.id_stone_type + "'>" + pitem.stone_type + "</option>";



	});



	$.each(uom_details, function (pkey, pitem) {



		uom_list += "<option value='" + pitem.uom_id + "'>" + pitem.uom_name + "</option>";



	});



	var row = '';







	row += '<tr>'



		+ '<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:100px;"><option value="">-Select-</option><option value=1>Yes</option><option value=0>No</option></select></td>'



		+ '<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" style="width:100px;">' + stones_type + '</select></td>'



		+ '<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" style="width:100px;">' + stones_list + '</select><input type="hidden" class="stone_type" value=""></td>'



		+ '<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="1" style="width: 60px;"/></td>'



		+ '<td><div class="input-group" style="width:159px;"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width: 78px;"/><span class="input-group-btn" style="width: 138px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">' + uom_list + '</select></span></div></td>'



		+ '<td><div class="form-group" style="width: 100px;"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type][' + length + ']" value="1" checked>Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + length + ']" class="stone_cal_type" value="2" >Pcs</div></td>'







		+ '<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="" /></td>'



		+ '<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="" "/></td>'



		+ '<td style="width: 100px;"><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';



	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);







}



$('#cus_stoneModal  #update_repair_item_stone_details').on('click', function () {



	if (validateStoneCusItemDetailRow()) {



		var stone_details = [];



		var stone_price = 0;



		var certification_price = 0;



		var tag_less_wgt = 0;



		var gross_wt = $('#gross_wt').val();



		modalStoneDetail = []; // Reset Old Value of stone modal



		$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function (index, tr) {



			stone_price += parseFloat($(this).find('.stone_price').val());



			// if ($(this).find('.show_in_lwt :selected').val() == 1) {

			if ($(this).find('.show_in_lwt').is(":checked")) {



				tag_less_wgt += parseFloat($(this).find('.stone_wt').val());



			}



			console.log('tag_less_wgt:' + tag_less_wgt);



			stone_details.push({



				'show_in_lwt': $(this).find('.show_in_lwt').val(),



				'stone_id': $(this).find('.stone_id').val(),



				'stones_type': $(this).find('.stones_type').val(),



				'stone_pcs': $(this).find('.stone_pcs').val(),



				'stone_wt': $(this).find('.stone_wt').val(),







				'stone_cal_type': $(this).find('input[type=radio]:checked').val(),



				'stone_price': $(this).find('.stone_price').val(),



				'stone_rate': $(this).find('.stone_rate').val(),



				'stone_type': $(this).find('.stone_type').val(),



				'stone_uom_id': $(this).find('.stone_uom_id').val(),







				'stone_uom_name': $(this).find('.stone_uom_id :selected').text(),



				'stone_name': $(this).find('.stone_id :selected').text()



			});



		});







		if (gross_wt < tag_less_wgt) {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Valid Weight.' });



		} else {



			modalStoneDetail = stone_details;



			$("#less_wt").val(tag_less_wgt);



			$("#stone_details").val(JSON.stringify(stone_details));



			$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();



			$('#cus_stoneModal').modal('hide');



			calculate_item_row_details();



		}







	}



	else {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Required Details.' });



	}



});



//Repair Other Item Details



$(document).on('change', '.purity', function (e) {



	var row = $(this).closest('tr');



	get_search_custom_metal_rates(row);



});



function get_metal_type(curRow, id_category) {



	$(".overlay").css('display', 'block');







	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_order/get_metaltype',



		dataType: 'json',



		data: {



			'id_category': id_category



		},



		success: function (data) {



			console.log(data);



			curRow.find(".id_metal").val(data.id_metal);



			$(".overlay").css("display", "none");



		}



	});



}



function get_search_custom_metal_rates(curRow) {



	var id_purity = curRow.find('.purity').val();



	var id_metal = curRow.find('.id_metal').val();



	if (id_purity == '' || id_metal == '') {



		if (id_purity == '') {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Purity..' });







		} else if (id_metal == '') {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Metal..' });



		}



		curRow.find('.order_rate').val(0);



		calculate_orderSale_value();



	}



	else {



		$("div.overlay").css("display", "block");



		my_Date = new Date();



		$.ajax({



			url: base_url + 'index.php/admin_ret_estimation/get_metal_purity_rate?nocache=' + my_Date.getUTCSeconds(),



			dataType: "json",



			method: "POST",



			data: { 'id_purity': id_purity, 'id_metal': id_metal },



			success: function (data) {



				$("div.overlay").css("display", "none");



				let rate = isNaN(parseFloat($('.' + data.rate_field).html())) ? 0 : parseFloat($('.' + data.rate_field).html());



				console.log(parseFloat($('.' + data.rate_field).html()));



				console.log(rate);



				curRow.find('.order_rate').val(rate);



				calculate_orderSale_value();



			}



		});



	}



}



$('#tag_search').on('click', function () {

	if ($('#branch_select').val()!= ''){

		get_tag_data();

	} else {

		$.toaster({
			priority: "danger",
			title: "Warning!",
			message: "" + "</br>Please select branch",
			settings: {'timeout': 5000}
		});

	}

});



$('#tag_barcode_search').on('click', function () {

	if ($('#branch_select').val()!= ''){

		get_tag_data();

	} else {

		$.toaster({
			priority: "danger",
			title: "Warning!",
			message: "" + "</br>Please select branch",
			settings: {'timeout': 5000}
		});

	}



});



function get_tag_data() {



	$(".overlay").css('display', 'block');



	var tagData = $('#est_tag_scan').val();



	var type = "";



	var searchTxt = "";



	var tag_search = false;



	console.log(tagData);



	if (tagData != "") {



		var istagId = (tagData.search("/") > 0 ? true : false);



		var isTagCode = (tagData.search("-") > 0 ? true : false);



		if (istagId) {



			var tId = tagData.split("/");



			searchTxt = (tId.length >= 2 ? tId[0] : "");



			type = "tag_id";



		}



		else if (isTagCode) {



			searchTxt = $('#est_tag_scan').val();



			type = "tag_code";



		}



		if (searchTxt != "") {



			if ($("#branch_settings").val() == 1) {



				if ($("#id_branch").val() != "") {



					tag_search = true;



				} else {



					tag_search = false;



					$('#est_tag_scan').val("");



				}



			} else {



				tag_search = true;



			}



		}



	} else {



		//var tagData = $.trim($('#est_tag_barcode_scan').val().replaceAll(' ',''));



		var tagData = $.trim($('#est_tag_barcode_scan').val());



		var istagId = (tagData.search("/") > 0 ? true : false);



		//var isTagCode = (tagData.search("-") > 0 ? true : false);



		var isTagCode = true;



		if (istagId) {



			var tId = tagData.split("/");



			searchTxt = (tId.length >= 2 ? tId[0] : "");



			type = "tag_id";



		}



		else if (isTagCode) {



			//searchTxt = $('#est_tag_barcode_scan').val().replaceAll(' ','');



			searchTxt = $.trim($('#est_tag_barcode_scan').val());



			type = "old_tag_id";



		}



		if (searchTxt != "") {



			if ($("#branch_settings").val() == 1) {



				if ($("#id_branch").val() != "") {



					tag_search = true;



				} else {



					tag_search = false;



					$('#est_tag_barcode_scan').val("");



				}



			} else {



				tag_search = true;



			}



		}



	}



	if (tag_search) {



		my_Date = new Date();



		$.ajax({



			type: 'POST',



			url: base_url + 'index.php/admin_ret_estimation/getTaggingBySearch/?nocache=' + my_Date.getUTCSeconds(),



			dataType: 'json',



			data: { 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val() },



			success: function (data) {



				create_new_empty_cus_order_row('tag', data);







			}



		});



	}



	$(".overlay").css('display', 'none');



}



function get_cmp_state() {



	$.ajax({



		type: 'POST',



		url: base_url + 'index.php/admin_ret_order/get_cmp_state',



		data: { 'id_branch': $('#id_branch').val() },



		dataType: 'json',



		success: function (data) {



			$('#cmp_state').val(data[0].id_state);



		}



	})



}



/*Product,Design,Subdesign Dropdown*/



function getCusproducts() {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		success: function (data) {



			console.log('product', data);



			OrderProducts = data;



		}



	});



}



$(document).on('change', '.product', function () {



	if (this.value != '') {



		var row = $(this).closest('tr');



		$("#o_item_id_" + this.id).val(this.value);



		getOrderSearchProducts(row, this.value);



	}



})



function getOrderSearchProducts(curRow, pro_id) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_estimation/getProductBySearch/?nocache=' + my_Date.getUTCSeconds(),



		dataType: "json",



		method: "POST",



		data: { 'searchTxt': '', 'pro_id': pro_id }, //,'cat_id' : curRow.find(".category").val()



		success: function (data) {



			console.log(data);



			curRow.find('.category').val(data[0].cat_id);



			curRow.find('.id_category').val(data[0].cat_id);



			curRow.find('.calculation_based_on').val(data[0].calculation_based_on);



			curRow.find('.sales_mode').val(data[0].sales_mode);



			get_product_size(data[0].value, curRow, 0);



			category_change(curRow, data[0].cat_id, curRow.find('.id_purity').val());



			get_Activedesign(curRow, data[0].value);



		}



	});



}



$(document).on('change', "#product", function () {





	var row = $(this).closest('tr');



	row.find('.id_design option').remove();



	if (this.value != '') {



		get_Activedesign(row, this.value);



	}



});





function get_Activedesign(curRow, pro_id) {



	curRow.find('.design option').remove();



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_catalog/get_active_design_products',



		dataType: "json",



		method: "POST",



		data: { 'id_product': pro_id },



		success: function (data) {



			var design_id = curRow.find('.id_design').val()



			$.each(data, function (key, item) {



				$(curRow.find('.design')).append(



					$("<option></option>")



						.attr("value", item.design_no)



						.text(item.design_name)



				);



			});



			$(curRow.find('.design')).select2(



				{



					placeholder: "Select Design",



					allowClear: true



				});



			curRow.find('.design').select2("val", (design_id != '' && design_id > 0 ? design_id : ""));



		}



	});



}



$(document).on('change', '.design', function () {



	console.log((this.value != ''));



	if (this.value != '') {



		$("#o_item_id_" + this.id).val(this.value);



		var row = $(this).closest('tr');



		get_ActiveSubDesigns(row, this.value);



	}



})



$(document).on('change', "#id_design", function () {





	var row = $(this).closest('tr');



	row.find('.id_sub_design option').remove();



	if (this.value != '') {



		get_ActiveSubDesigns(row, this.value);



	}



});





function get_ActiveSubDesigns(curRow, des_id) {



	curRow.find('.sub_design option').remove();



	var pro_id = curRow.find('.product').val();



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_catalog/get_ActiveSubDesigns',



		dataType: "json",



		method: "POST",



		data: { 'id_product': pro_id, 'design_no': des_id },



		success: function (data) {



			var sub_design_id = curRow.find('.id_sub_design').val()



			$.each(data, function (key, item) {



				$(curRow.find('.sub_design')).append(



					$("<option></option>")



						.attr("value", item.id_sub_design)



						.text(item.sub_design_name)



				);



			});



			$(curRow.find('.sub_design')).select2(



				{



					placeholder: "Select Sub Design",



					allowClear: true



				});



			curRow.find('.sub_design').select2("val", (sub_design_id != '' && sub_design_id > 0 ? sub_design_id : ""));



		}



	});



}



$(document).on('change', '.sub_design', function () {



	if (this.value != '') {



		var row = $(this).closest('tr');



		row.find(".id_sub_design").val(this.value);



	}



})



/*Product,Design,Subdesign Dropdown*/



function web_cam_order_images() {



	//webcam upload, #AD On:31-03-2023,Cd:Gopalakrishnan



	Webcam.set({



		width: 290,



		height: 190,



		image_format: 'jpg',



		jpeg_quality: 90



	});







	Webcam.attach('#my_camera');



	Webcam.on('error', function (err) {



		console.log('Error accessing webcam:', err);



	});



	$("#upload_img").setShortcutKey(17, 73, function () {



		take_snapshot('pre_images');



	});



	//webcam upload ends...











}















function toggle(elements, specifiedDisplay) {







	var element, index;







	elements = elements.length ? elements : [elements];



	for (index = 0; index < elements.length; index++) {



		element = elements[index];







		if (isElementHidden(element)) {



			element.style.display = '';







			// If the element is still hidden after removing the inline display



			if (isElementHidden(element)) {



				element.style.display = specifiedDisplay || 'block';



			}



		} else {



			element.style.display = 'none';



		}



	}



	function isElementHidden(element) {



		return window.getComputedStyle(element, null).getPropertyValue('display') === 'none';



	}



}







function take_snapshot(type) {







	var pre_img_files = [];



	$('#snap_shots').prop('disabled', true);



	if (type == 'pre_images') {



		var preview = 'uploadArea_p_stn';











	}



	Webcam.snap(function (data_uri) {



		$(".image-cust").val(data_uri);



		pre_img_resource.push({ 'src': data_uri, 'name': (Math.floor(100000 + Math.random() * 900000)) + 'jpg' });



		pre_img_files.push(data_uri);







	});



	$('#customer_images').val(encodeURIComponent(JSON.stringify(pre_img_resource)));



	console.log(pre_img_resource);



	var show = $('#cus_img_preview');



	show.prop('src', pre_img_resource[0].src);



	show.css('display', 'block');







	setTimeout(function () {



		var resource = [];



		$('#' + preview + ' div').remove();







		if (type == 'pre_images') {



			resource = pre_img_resource;



		}







		$.each(resource, function (key, item) {



			console.log(key)



			if (item) {



				var div = document.createElement("div");



				div.setAttribute('class', ' col-md-4 images');



				div.setAttribute('id', +key);



				param = { "key": key, "preview": preview, "stone_type": type };



				div.innerHTML += "<a onclick='remove_stn_img(" + JSON.stringify(param) + ")' style='cursor:pointer;color:inherit;' data-toggle='tooltip' data-placement='bottom' title='Delete This Images'>&nbsp;<i class='fa fa-trash'></i></a>&nbsp;&nbsp;<img class='thumbnail' src='" + item.src + "'" + "style='width: 100px;height: 100px;'/>";



				$('#' + preview).append(div);

			}



			$('#lot_img_upload').css('display', '');











		});











		$('#snap_shots').prop('disabled', false);







	}, 100);



}







$(document).on('change', '.wast_wgt', function (e) {



	let wastage_wt = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val());



	let wast_per = parseFloat(parseFloat(wastage_wt * 100) / parseFloat($(this).closest('tr').find('.net_wt').val())).toFixed(2);



	$(this).closest('tr').find('.wast_percent').val(wast_per);



	console.log((parseFloat(wast_per) > 100));



	if (wast_per > 100) {



		$(this).closest('tr').find('.wast_percent').val('');



		$.toaster({ priority: 'warning', title: 'Warning!', message: '' + "</br>Please Enter The Valid Percentage" });



	}



	calculate_orderSale_value();



});



/*Customer Kyc*/



$('.dl_no,.ed_dl_no').on('change', function () {



	var value = $(this).val();

	var maxLength = $(this).attr("maxLength");

	if (value.length != maxLength) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Driving License No.." });

		$("#dl").focus();

		$("#dl").val("");



		$("#ed_dl_no").focus();

		$("#ed_dl_no").val("");

	}

});



$('.pp_no,.ed_pp_no').on('change', function () {

	var value = $(this).val();

	var maxLength = $(this).attr("maxLength");

	if (value.length > maxLength) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Driving License No.." });

		$("#pp").val("");

		$("#pp").focus();



		$("#ed_pp").focus();

		$("#ed_pp").val("");

	}

})



/*Customer Kyc*/



function get_repair_order_edit_data() {



	$(".overlay").css('display', 'block');







	let id_customerorder = $("#id_customerorder").val();







	if (id_customerorder > 0) {



		my_Date = new Date();





		$.ajax({





			type: 'POST',





			url: base_url + 'index.php/admin_ret_order/repair_order/repair_ordered_list/' + id_customerorder + '?nocache=' + my_Date.getUTCSeconds(),





			dataType: 'json',





			success: function (order) {





				let pre_img_resource = [];



				let metal_type = "";



				let metal_name = "";



				let orderno = "";



				let product_name = "";







				let product_id = "";







				let design_name = "";







				let design_id = "";







				let sub_design_name = "";







				let sub_design_id = "";







				let gross_wt = "";







				let less_wt = "";







				let net_wt = "";





				let pcs = "";





				let stone_details = [];





				let id_orderdetails = "";





				$(order).each(function (index, data) {



					if (data.order_type == 3) {



						$('input[name="order[order_type]"][value="' + data.order_type + '"]').prop('checked', true);



						$('.cus_repair').css("display", "block");



						$('.stock_repair').css("display", "none");



						var trHtml = "";



						var a = $("#i_increment").val() != '' ? $("#i_increment").val() : 0;



						var i = ++a;



						$("#i_increment").val(i);



						$('#cus_name').val(data.cus_name);



						$('#cus_id').val(data.id_customer);



						$('#id_branch').val(data.id_branch);



						$('#id_employee').val(data.order_taken_by);







						$('#branch_select').select2('val', data.id_branch);



						// $('#branch_select').append(



						// 	$("<option></option>").attr("value", data.id_branch).text(data.branch_name)



						// );



						// $('#branch_select').text(data.branch_name);





						metal_type = data.metal_type;



						metal_name = data.metal_name;



						product_name = data.product_name;





						product_id = data.id_product;





						design_name = data.design_name;





						design_id = data.design_id;





						sub_design_name = data.sub_design_name;



						sub_design_id = data.id_sub_design;



						gross_wt = data.gross_wt;



						less_wt = data.less_wt;



						net_wt = data.net_wt;



						pcs = data.piece;



						cus_due_date = data.cus_due_date;



						id_orderdetails = data.id_orderdetails;



						description = data.description;



						id_repair_master = data.id_repair_master;



						days_difference = data.days_difference;



						id_customerorder = data.id_customerorder;



						orderno = data.orderno;





						var stone_price = 0;



						var stone_details = [];





						$.each(data.stone_details, function (skey, sitem) {





							stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });



							stone_price = parseFloat(stone_price) + parseFloat(sitem.amount);







						});



						pre_img_resource = [];



						$.each(data.image_details, function (skey, sitem) {





							pre_img_resource.push({ 'src': sitem.img_src, 'name': sitem.image, 'id': sitem.id_order_img });







						});









						var select_metal = "";



						$.each(data.metal, function (key, pitem) {



							var selected = "";



							if (metal_type == pitem.id_metal) {



								selected = "selected";



							}

							select_metal += "<option value='" + pitem.id_metal + "' " + selected + ">" + pitem.metal + "</option>";



						});





						var select_repair = "";



						$.each(data.damage_repair, function (key, pitem) {



							var selected = "";



							if (id_repair_master == pitem.id_repair_master) {





								selected = "selected";



							}

							select_repair += "<option value='" + pitem.id_repair_master + "' " + selected + ">" + pitem.name + "</option>";



						});





						var select_product = "";



						$.each(data.products, function (key, pitem) {



							var selected = "";



							if (product_id == pitem.pro_id) {





								selected = "selected";





							}

							select_product += "<option value='" + pitem.pro_id + "' " + selected + ">" + pitem.product_name + "</option>";



						});



						var select_design = "";



						$.each(data.design, function (key, pitem) {



							var selected = "";



							if (design_id == pitem.design_no) {



								selected = "selected";



							}

							select_design += "<option value='" + pitem.design_no + "' " + selected + ">" + pitem.design_name + "</option>";



						});



						var select_sub_design = "";



						$.each(data.sub_design, function (key, pitem) {



							var selected = "";



							if (sub_design_id == pitem.id_sub_design) {



								selected = "selected";

							}

							select_sub_design += "<option value='" + pitem.id_sub_design + "' " + selected + ">" + pitem.sub_design_name + "</option>";



						});





						trHtml += "<tr id='detail" + i + "' class='" + i + "'>"



							+ "<td><select class='metal' class='form-control' name='order_item[metal][]' id='metal' required='true' style='width: 150px;'>" + select_metal + "</select><input type='hidden' id='metal' value= '" + metal_type + "'required='true'><input type='hidden' class='id_orderdetails'name='order_item[id_orderdetails][]' value=" + id_orderdetails + "><input type='hidden' class='id_customerorder'name='order[id_customerorder]' value=" + id_customerorder + "><input type='hidden' name='order[order_no]' value=" + orderno + " ></td>"



							+ "<td><select class='product' class='form-control' name='order_item[id_product][]' id='product' required='true' style='width: 150px;'value='" + product_name + "' >" + select_product + "</select><input type='hidden' name='o_item[id_product]' id='id_product required='true' value='" + product_id + "'/></td>"



							+ '<td><select class="form-control design" name="order_item[id_design][]" value="" placeholder="Search Design" required style="width:150px;">' + select_design + '</select><input type="hidden" class="id_design" name="o_item[id_design][]" value="' + design_id + '" /></td>'



							+ '<td><select class="form-control sub_design" name="order_item[id_sub_design][]" value="' + sub_design_name + '" placeholder="Search SubDesign" required style="width:150px;">' + select_sub_design + '</select><input type="hidden" class="id_sub_design" name="o_item[id_sub_design][]" value="' + sub_design_id + '" /></td>'



							+ '<td><input type="number" class="form-control weight" value="' + gross_wt + '" name="order_item[weight][]"  style="width:100px;"></td>'



							+ '<td><div class="form-group"><div class="input-group"><input class="form-control less_wt" value=' + less_wt + '  onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="order_item[less_wt][]" step="any" readonly="" style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" value=' + JSON.stringify(stone_details) + ' name="order_item[stone_details][]" ><input type="hidden" class="stone_price" name="order_item[stone_price][]"value="' + stone_price + '"></div></div></td>'



							+ '<td><input type="number" class="form-control net_wt"  value="' + net_wt + '" name="order_item[net_wt][]" style="width:100px;" readonly></td>'



							+ '<td><input type="number" class="form-control pcs" name="order_item[piece][]" style="width:100px;"  value="' + pcs + '" ></td>'



							+ '<td><input type="text" data-date-format="dd-mm-yyyy"  class="form-control cus_due_days" value="' + days_difference + '" name="order_item[cus_due_days][]" style="width:100px;" ></td>'



							+ "<td><select class='form-control repair' name='order_item[repair][]' id='repair' required='true' style='width: 150px;'value=" + id_repair_master + ">'" + select_repair + "'</select></td>"



							+ '<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" name="order_item[order_img][]" value=' + JSON.stringify(pre_img_resource) + ' ></td>'



							+ '<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" name="order_item[description][]" value="' + description + '" ></td>'



							+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



							+ '</tr>';





						if ($('#custrepair_item_detail > tbody  > tr').length > 0) {



							$('#custrepair_item_detail > tbody > tr:first').before(trHtml);



						} else {



							$('#custrepair_item_detail tbody').append(trHtml);



						}

						calcluate_cur_repair_total();



						$('#custrepair_item_detail > tbody').find('.product').select2();



						$('#custrepair_item_detail > tbody').find('.design').select2();



						$('#custrepair_item_detail > tbody').find('.sub_design').select2();



						$('#custrepair_item_detail > tbody').find('.metal').select2();



						$('#custrepair_item_detail > tbody').find('.repair').select2();



						$('.cus_due_days').datepicker();



					}



					else if (data.order_type == 4) {





						$('input[name="order[order_type]"][value="' + data.order_type + '"]').prop('checked', true);



						$('.cus_repair').css("display", "none");



						$('.stock_repair').css("display", "block");





						$('#id_branch').val(data.id_branch);









						console.log(data.tag_details);



						id_orderdetails = data.id_orderdetails;



						id_customerorder = data.id_customerorder;



						orderno = data.orderno;



						if (data.tag_details.length > 0) {



							var html = "";



							var old_tag = "";



							var tag_code = "";



							$.each(data.tag_details, function (key, items) {



								//var purewt = parseFloat((parseFloat(items.net_wt) * (parseFloat(items.purname) + parseFloat(items.wastage_percent))) / 100).toFixed(3);



								old_tag = items.old_label;



								tag_code = items.label;



								var purewt = parseFloat((parseFloat(items.net_wt) * (parseFloat(items.purname) + parseFloat(items.wastage_percent))) / 100).toFixed(3);



								if (items.tag_img != null && items.tag_img != undefined && items.tag_img != '') {



									var img_src = base_url + 'assets/img/tag/' + items.tag_img;

								}

								else {

									var img_src = base_url + 'assets/img/no_image.png';

								}



								stone_details = [];



								var stone_price = "";



								$.each(items.stone_details, function (skey, sitem) {



									stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });



									stone_price = parseFloat(stone_price) + parseFloat(sitem.amount);



								});



								pre_img_resource = [];



								$.each(data.image_details, function (skey, sitem) {





									pre_img_resource.push({ 'src': sitem.img_src, 'name': sitem.image, 'id': sitem.id_order_img });







								});



								id_repair_master = data.id_repair_master;



								i = new Date().valueOf();



								$.each(data.damage_repair, function (key, pitem) {



									var selected = "";



									if (id_repair_master == pitem.id_repair_master) {





										selected = "selected";



									}

									select_repair += "<option value='" + pitem.id_repair_master + "' " + selected + ">" + pitem.name + "</option>";



								});



								html += "<tr id='detail" + i + "' class='" + i + "'>"



									+ '<td><img class="img_src"  id="img_prev" src="' + img_src + '" width="35" height="35" onclick="tag_imgprev($(this).closest(\'tr\'))"></td>'



									+ '<td><input type="hidden" class="tag_id" name="order_item[tag_id][]" value="' + items.value + '"><input type="hidden" class="tag_code" name="tag_code[]" value="' + items.label + '">' + items.label + "<input type='hidden' class='id_orderdetails'name='order_item[id_orderdetails][]' value=" + id_orderdetails + "><input type='hidden' class='id_customerorder'name='order[id_customerorder]' value=" + id_customerorder + "><input type='hidden' name='order[order_no]' value=" + orderno + " ></td>"



									+ '<td><input type="hidden" class="old_tag_code" name="order_item[old_tag_code][]" value="' + items.value + '">' + (items.old_label ? '<input type="hidden" class="old_tag_code" name="old_tag_code[]" value="' + items.old_label + '">' + items.old_label : '') + '</td>'



									+ '<td>' + items.catname + '</td>'



									+ '<td>' + items.purname + '</td>'



									+ '<td><input type="hidden" class="id_product" name="order_item[id_product][]" value="' + items.lot_product + '">' + items.product_name + '</td>'



									+ '<td><input type="hidden" class="id_design" name="order_item[id_design][]" value="' + items.design_id + '">' + items.design_name + '</td>'



									+ '<td><input type="hidden" class="id_sub_design" name="order_item[id_sub_design][]" value="' + items.subdesignid + '">' + items.sub_design_name + '</td>'



									+ '<td><input type="hidden" class="piece" name="order_item[piece][]" value="' + items.piece + '">' + items.piece + '</td>'



									+ '<td><input type="hidden" class="purewt" name="order_item[purewt][]" value="' + purewt + '"><input type="hidden" class="gross_wt" name="order_item[weight][]" value="' + items.gross_wt + '">' + items.gross_wt + '</td>'



									+ '<td><input type="hidden" class="net_wt" name="order_item[net_wt][]" value="' + items.net_wt + '">' + items.net_wt + '</td>'



									+ '<td><div class="form-group"><div class="input-group"><input class="form-control less_wt"  value="' + items.less_wt + '"onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="order_item[less_wt][]" step="any" readonly="" style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" value=' + JSON.stringify(stone_details) + ' name="order_item[stone_details][]" ><input type="hidden" class="stone_price" name="order_item[stone_price][]"value="' + stone_price + '"></div></div></td>'



									+ "<td><select class='form-control repair' name='order_item[repair][]' id='repair' required='true' style='width: 150px;' > " + select_repair + "</select></td>"



									+ '<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" name="order_item[order_img][]"   value=' + JSON.stringify(pre_img_resource) + '></td>'



									+ '<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" name="order_item[description][]" value="' + data.description + '" ></td>'



									// + "<td><select class='form-control repair' name='order_item[repair][]' id='repair' required='true' style='width: 150px;' /></td>"



									+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



									+ '</tr>';







								/*	html += '<tr>'

	

										+ '<td><input type="hidden" class="tag_id" name="order_item[tag_id][]" value="' + items.value + '"><input type="hidden" class="tag_code" name="tag_code[]" value="' + tag_code + '">' + tag_code + '</td>'

	

										+ '<td><input type="hidden" class="old_tag_code" name="order_item[old_tag_code][]" value="' + items.value + '"><input type="hidden" class="old_tag_code" name="old_tag_code[]" value="' + old_tag + '">' + old_tag + '</td>'

	

										+ '<td>' + items.catname + '</td>'

	

										+ '<td>' + items.purname + '</td>'

	

										+ '<td><input type="hidden" class="id_product" name="order_item[id_product][]" value="' + items.lot_product + '">' + items.product_name + '</td>'

	

										+ '<td><input type="hidden" class="id_design" name="order_item[id_design][]" value="' + items.design_id + '">' + items.design_name + '</td>'

	

										+ '<td><input type="hidden" class="id_sub_design" name="order_item[id_sub_design][]" value="' + items.subdesignid + '">' + items.sub_design_name + '</td>'

	

										+ '<td><input type="hidden" class="piece" name="order_item[piece][]" value="' + items.piece + '">' + items.piece + '</td>'

	

										+ '<td><input type="hidden" class="purewt" name="order_item[purewt][]" value="' + purewt + '"><input type="hidden" class="gross_wt" name="order_item[weight][]" value="' + items.gross_wt + '">' + items.gross_wt + '</td>'

	

										+ '<td>input type="hidden" class="net_wt" name="order_item[net_wt][]" value="' + items.net_wt + '">' + items.net_wt + '</td>'

	

										+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

	

										+ '</tr>';*/



							});



							get_branchname();



							if ($('#tagissue_item_detail > tbody  > tr').length > 0) {



								$('#tagissue_item_detail > tbody > tr:first').before(html);



							} else {



								$('#tagissue_item_detail tbody').append(html);



							}



							$('#custrepair_item_detail > tbody').find('.repair').select2();



							calculate_tag_issue_details();



							$('#issue_tag_code').val('');



							$('#old_issue_tag_code').val('');



							$('#issue_tag_code').focus();



						}

					}





				});



			}







		});





	}







	$(".overlay").css('display', 'none');



}











$(document).on('click', '#resend_cancel_otp', function (e) {

	ordercancel_otp();



});



//Order Cancel OTP

function ordercancel_otp() {

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_order/send_order_cancel_otp/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'order_id': $('#order_id').val() },

		success: function (data) {

			console.log(data.status);

			if (data.status == true) {

				$("div.overlay").css("display", "none");

				$('#otp_by_emp').val('');

				$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + data.msg });



				var fewSeconds = 60;

				$("#resend_cancel_otp").prop('disabled', true);

				timer = setTimeout(function () {

					$("#resend_cancel_otp").prop('disabled', false);

				}, fewSeconds * 1000);





			}

			else {

				$("div.overlay").css("display", "none");

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br> Unable to Send The OTP..." });

			}

		},

		error: function (error) {

			$("div.overlay").css("display", "none");

		}

	});

}



//OTP FOR Order Cancel

$('#send_order_cancel_otp_yes').on('click', function () {

	allow_discount_otp = true;

	$('.cancel_otp').css('display', 'block');

	$('.cancel_otp_confirmation').css('display', 'none');

	$('.order_remarks').css('display', 'none');

	$('.verify_otp').css('display', 'block');

	ordercancel_otp()



});



$(document).on('click', '#send_order_cancel_otp_no', function () {

	$('#confirm-ordercancell').modal('toggle');

});



$('#order_cancel_close_modal').on('click', function () {

	$('#confirm-ordercancell').modal('toggle');

});



$('#verfiy_ord_can_otp').on('click', function () {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_order/verify_order_cancel_otp/?nocache=' + my_Date.getUTCSeconds(),

		data: { 'otp': $('#cancel_otp').val() },

		dataType: "json",

		method: "POST",

		success: function (data) {

			if (data.status == true) {

				$('.cancel_otp_confirmation').css('display', 'none');

				$('.cancel_otp').css('display', 'none');

				$('.order_remarks').css('display', 'block');

				$('.verify_otp').css('display', 'none');



				allow_discount_otp = false

				$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + data.msg });

			} else {

				$('#cancel_otp').val('');

				$.toaster({ priority: 'danger', title: 'Success!', message: '' + "</br>" + data.msg });

			}

		},

		error: function (error) {

		}

	});

});









function get_ActiveSubDesign() {



	$('#sub_design_select option').remove();



	var pro_id = $('#prod_select').val();



	var des_id = $('#des_select').val();



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_catalog/get_ActiveSubDesigns',



		dataType: "json",



		method: "POST",



		data: { 'id_product': pro_id, 'design_no': des_id },



		success: function (data) {



			// var sub_design_id = curRow.find('.id_sub_design').val()



			$.each(data, function (key, item) {



				$('#sub_design_select').append(



					$("<option></option>")



						.attr("value", item.id_sub_design)



						.text(item.sub_design_name)



				);



			});



			$("#sub_design_select").select2(



				{



					placeholder: "Select Sub Design",



					allowClear: true



				});



			$("#sub_design_select").select2("val", '');



		}



	});



}





$(document).on('change', '#employee_sel', function () {



	if (this.value != '') {



		$('#id_employee').val(this.value);



	} else {

		$('#id_employee').val('');



	}



});





$(document).on('keyup', '.stone_wt', function () {



	var row = $(this).closest('tr');

	var stone_wt = row.find('.stone_wt').val();

	var stn_wt = $('.stn_wt').html();



	var stone_uom_id = row.find('.stone_uom_id ').val();

	var catRow = $('#custom_active_id').val();



	var gross_wt = $('#' + catRow).find('.weight').val();



	if ((parseFloat(stn_wt) > parseFloat(gross_wt)) && stone_uom_id == 1) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Stone Weight is Greater Than Gross Weight' });

		row.find('.stone_wt ').val('');



	}



	if (stone_uom_id == 6) {

		var dia_wt = parseFloat(stn_wt / 5);

		if ((parseFloat(dia_wt) > parseFloat(gross_wt))) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Stone Weight is Greater Than Gross Weight' });

			row.find('.stone_wt ').val('');

		}





	}





	calculate_est_stone_Amount();





});



$(document).on('change', '.stone_uom_id', function () {

	var row = $(this).closest('tr');

	var uom_id = row.find('.stone_uom_id').val();

	var stone_wt = row.find('.stone_wt').val();

	var catRow = $('#custom_active_id').val();

	var gross_wt = $('#' + catRow).find('.weight').val();



	if ((parseFloat(stone_wt) > parseFloat(gross_wt)) && uom_id == 1) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Stone Weight is Greater Than Gross Weight' });

		row.find('.stone_wt ').val('');

	}





});



$(document).on('click', '#create_order_item', function (e) {

	e.preventDefault();



	$('#repaid_order_items').trigger('click');



})





function formatNumber(numberToFormat, digit = 3, locale = 'en-IN') {

	return new Intl.NumberFormat(locale, { minimumFractionDigits: digit, maximumFractionDigits: digit }).format(numberToFormat);

}



function get_ActiveSections() {

	$("#section_sel option").remove();

	my_Date = new Date();

	$.ajax({

		type: 'POST',

		url: base_url + "index.php/admin_ret_catalog/get_sectionBranchwise?nocache=" + my_Date.getUTCSeconds(),

		data: { 'id_branch': $('#branch_select').val() },

		dataType: 'json',

		success: function (data) {

			var id = $("#id_section").val();

			$.each(data, function (key, item) {

				$("#section_sel").append(

					$("<option></option>")

						.attr("value", item.id_section)

						.text(item.section_name)

				);

			});

			$('#section_sel').select2({

				placeholder: "Select Section",

				allowClear: true

			});



			$("#section_sel").select2("val", (id != '' && id > 0 ? id : ''));



			$(".overlay").css("display", "none");

		}

	})

}





function get_ActiveCounters() {

	$('#counter_sel option').remove();

	$("div.overlay").css("display", "block");

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_catalog/get_ActiveCounter',

		dataType: 'json',

		data: { 'id_branch': ($('#branch_filter').val() != '' && $('#branch_filter').val() != undefined ? $('#branch_filter').val() : $("#branch_select").val()), 'id_floor': $("#floor_sel").val() },

		success: function (data) {

			var id = $("#counter").val();

			$("#counter_sel").append(

				$("<option></option>")

					.attr("value", 0)

					.text('All')

			);

			$.each(data, function (key, item) {

				$('#counter_sel').append(

					$("<option></option>")

						.attr("value", item.counter_id)

						.text(item.counter_name)

				);

			});

			$("#counter_sel").select2(

				{

					placeholder: "Select Counter",

					allowClear: true

				});

			$('#counter_sel').select2("val", (id != '' && id > 0 ? id : ''));

			$(".overlay").css("display", "none");

		}

	});

}





$(document).on('click', '.rpr_ord_img', function (e) {

	e.preventDefault();



	id = $(this).data('id');



	$("#edit-id").val(id);



	view_repair_order_img(id);





});









//Repair Order Image Preview



function view_repair_order_img(order_id_img1) {



	update_tag_img_id1 = order_id_img1;



	data = [];



	var tag_codeimage1 = base_url + 'assets/img/orders';



	$('#imageModal_bulk_edit').modal('show');



	$(".overlay").css('display', "none");



	$.ajax({



		data: ({ 'order_id': order_id_img1 }),



		url: base_url + "index.php/admin_ret_order/get_img_by_order_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			retrive_img = data;



			for (i = 0; i < data.length; i++) {



				img_src = data[i].image;



				var preview = $('#order_images');



				var img = tag_codeimage1 + '/' + img_src;



				if (img_src) {



					div = document.createElement("div");



					div.setAttribute('class', 'col-md-3 images');



					div.setAttribute('id', 'order_img_edit_' + [i]);







					$('.images').css('margin-right', '25px');



					key = [i];



					param = img_src;







					div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><img class='thumbnail' src='" + img + "'" + "style='width: 300px;height: 250px;'/><a href=" + img + " download=" + img_src + "><i class='fa fa-download btn btn-success'>Download</i></div></div></a>";



					preview.append(div);







				}



			}







		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}

//Repair Order Image Preview



function get_villages_by_pincode(pincode) {



	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/get_village_by_pincode?nocache=" + my_Date.getUTCSeconds(),

		dataType: "json",

		type: 'POST',

		data: { 'pin_code': pincode },

		success: function (data) {



			if (data.length > 0) {

				var id_village = $('#id_village').val();

				var ed_id_village = $('#ed_id_village').val();

				$('#sel_village option').remove();

				$('#ed_sel_village option').remove();



				$("#sel_village,#ed_sel_village").select2({

					placeholder: "Select Area",

					allowClear: true

				});

				$.each(data, function (key, item) {

					$("#sel_village,#ed_sel_village").append(

						$("<option></option>")

							.attr("value", item.id_village)

							.text(item.village_name)

					);

				});



				if ($('#sel_village').length > 0) {

					$('#sel_village').select2("val", (id_village != '' ? id_village : ''));

				}

				/* if ($('#ed_sel_village').val().length > 0 ) {

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : ''));

				} */

				if (id_village != '') {

					$('#sel_village').select2("val", (id_village != '' ? id_village : ''));

				}

				/* if(ed_id_village!=''){

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : ''));



				} */

				$("body").on("hidden.bs.modal", function () { // to use multiple model in one page

					if ($(".modal.in").length > 0) {

						$("body").addClass("modal-open")

					}

				});

			} else {



				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Area Found For this Pincode' });

				$('#sel_village option').remove();

				// $('#ed_sel_village option').remove();





				$('#id_village').val('');

				$('#ed_id_village').val('');



				$("#sel_village,#ed_sel_village").select2({

					placeholder: "Select Area",

					allowClear: true

				});





			}



		}

	});



}



$('#snap_shots').on('click', function () {

	Webcam.snap(function (data_uri) {

		$(".image-tag").val(data_uri);

		img_resource.push({ 'src': data_uri, 'name': (Math.floor(100000 + Math.random() * 900000)) + 'jpg' });

		pre_img_files.push(data_uri);

		alert("Your Webcam Images Take Snap Shot Successfullys.");

		console.log(data_uri);

		var preview = $('#cus_img_preview');

		preview.prop('src', data_uri);

	});

	$('#customer_img').val(JSON.stringify(img_resource));

});







function get_edit_repair_details(id) {



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_order/order/repair_details?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		data: { 'id_orderdetails': id },



		dataType: "JSON",



		type: "POST",



		success: function (data) {



			var row = '';



			$.each(data, function (key, item) {



				row += '<tr>'



					+ '<td><input type="hidden" class="item_type" name="order_item[item_type][]" value="' + item.item_type + '">' + item.item_type_name + '</td>'



					+ '<td><input type="hidden" class="id_section" name="order_item[id_section][]" value="' + item.id_section + '">' + item.section + '</td>'



					+ '<td><input type="hidden" class="category" name="order_item[id_ret_category][]" value="' + item.id_ret_category + '">' + item.category_name + '</td>'



					+ '<td><input type="hidden" class="purity" name="order_item[id_purity][]" value="' + item.id_purity + '">' + item.purity + '</td>'



					+ '<td><input type="hidden" class="id_product" name="order_item[id_product][]" value="' + item.id_product + '">' + item.product_name + '</td>'



					+ '<td><input type="hidden" class="id_design" name="order_item[id_design][]" value="' + item.id_design + '">' + item.design_name + '</td>'



					+ '<td><input type="hidden" class="id_sub_design" name="order_item[id_sub_design][]" value="' + item.id_sub_design + '">' + item.sub_design_name + '</td>'



					+ '<td><input type="hidden" class="pcs" name="order_item[pcs][]" value="' + item.pcs + '">' + parseFloat(item.pcs).toFixed(0) + '</td>'



					+ '<td><input type="hidden" class="gross_wt" name="order_item[gross_wt][]" value="' + item.gross_wt + '">' + item.gross_wt + '</td>'



					+ '<td><input type="hidden" class="less_wt" name="order_item[less_wt][]" value="' + item.less_wt + '"><input type="hidden" class="stone_details" name="order_item[stone_details][]" >' + item.less_wt + '</td>'



					+ '<td><input type="hidden" class="net_wt" name="order_item[net_wt][]" value="' + item.net_wt + '">' + item.net_wt + '</td>'



					+ '<td><input type="hidden" class="wast_per" name="order_item[wast_per][]" value="' + item.wast_percent + '">' + item.wast_percent + '</td>'



					+ '<td><input type="hidden" class="mc_type" name="order_item[mc_type][]" value="' + item.mc_type + '"><input type="hidden" class="mc_value" name="order_item[mc_value][]" value="' + item.mc_value + '">' + item.mc_value + '</td>'



					+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



					+ '</tr>';



				$('#custrepair_item_detail tbody').append(row);



			});



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});



}







function copy_cus_order_row(cuRrow) {



	if (validateOrderDetailRow()) {



		get_category();



		var html = "";







		var a = $("#i_increment").val();







		var i = ++a;







		$("#i_increment").val(i);







		var cus_due_date = $('#cus_due_date').val();







		var smith_due_date = $('#smith_due_date').val();







		var smith_rem_date = $('#smith_remainder_date').val();







		var collections_required = $('#collections_required').val();







		var subproduct_required = $('#subproduct_required').val();







		let order_type = cuRrow.find('.order_type').val();







		let order_type_name = '';







		let tag_code = cuRrow.find('.tag_name').val();







		let tag_id = cuRrow.find('.order_tag_id').val();



		let mc_type = cuRrow.find('.mc_type').val();







		let cat_id = cuRrow.find('.category').val();







		let metal_type = cuRrow.find('.id_metal').val();







		let purity = cuRrow.find('.purity').val();







		let product_name = cuRrow.find('.product').val();







		let product_id = cuRrow.find('.id_product').val();







		let design_name = cuRrow.find('.design').val();







		let design_id = cuRrow.find('.id_design').val();







		let sub_design_name = cuRrow.find('.sub_design').val();







		let sub_design_id = cuRrow.find('.id_sub_design').val();







		let gross_wt = cuRrow.find('.weight').val();







		let less_wt = cuRrow.find('.less_wt').val();







		let net_wt = cuRrow.find('.net_wt').val();







		let size = cuRrow.find('.size').val();







		let pcs = cuRrow.find('.qty').val();







		let wast_perc = cuRrow.find('.wast_percent').val();







		let wast_wt = cuRrow.find('.wast_wt').val();







		let tag_mc_type = '';







		let tag_mc_value = cuRrow.find('.mc').val();





		let description = cuRrow.find('.order_des').val();







		let calculation_based_on = cuRrow.find('.calculation_based_on').val();







		let sales_mode = cuRrow.find('.sales_mode').val();



		let due_date = cuRrow.find('.due_date').val();







		let charge_value = cuRrow.find('.value_charge').val();







		let stone_details_array = cuRrow.find('.stone_details').val();



		let stone_details = JSON.parse(stone_details_array);





		let order_img = cuRrow.find('.order_img').val();



		// let order_img = JSON.parse(order_img_array);







		let charges_details_array = cuRrow.find('.charges_details').val();



		let charges_details = JSON.parse(charges_details_array);





		console.log(JSON.parse(charges_details_array));







		let stone_price = cuRrow.find('.stn_amt').val();







		let amount = cuRrow.find('.order_amt').val();







		let order_rate = cuRrow.find('.order_rate').val();



		let id_purity = cuRrow.find('.id_purity').val();









		let tax_amount = cuRrow.find('.taxable').val();



		let tot_stone_wt = cuRrow.find('.less_wt').val();







		let id_orderdetails = cuRrow.find('.id_orderdetails').val();



		let tax_group = cuRrow.find('.tax_group').val();









		html += "<tr id='detail" + i + "' class='" + i + "'>" +







			"<td style='display:none'>" + order_type_name + "<input type='hidden' name='o_item[" + i + "][order_type]' id='" + i + "' class='form-control order_type' value='" + order_type + "'></td>" +







			"<td><input class='form-control tag_name' type='text' name='o_item[" + i + "][tag_name]' placeholder='Enter tag code' required autocomplete='off' value='" + tag_code + "' style='width: 100px;' readonly /><input class='order_tag_id' type='hidden' name='o_item[" + i + "][tag_id]' placeholder='Enter tag Id' required value='" + tag_id + "' /></td>" +







			"<td style='display:none'><select class='form-control category' name='o_item[" + i + "][category]' id='category" + i + "' required='true' style='width: 100px;'/><input type='hidden' class='id_category' name='o_item[" + i + "][id_category]' id='id_category" + i + "' required='true' value='" + cat_id + "'/><input type='hidden' class='id_metal' name='o_item[" + i + "][id_metal]' id='id_metal" + i + "' required='true' value='" + metal_type + "' /><input type='hidden' class='tax_group' name='o_item[" + i + "][tax_group]' id='tax_group" + i + "' required='true' value='" + tax_group + "' /><input type='hidden' name='o_item[" + i + "][cgst]' class='o_cgst' /><input type='hidden' class='o_sgst' name='o_item[" + i + "][sgst]' /><input type='hidden' class='o_igst' name='o_item[" + i + "][igst]' /><input type='hidden' class='calculation_based_on' name='o_item[" + i + "][calculation_based_on]' value='" + calculation_based_on + "' /><input type='hidden' class='sales_mode' name='o_item[" + i + "][sales_mode]' value='" + sales_mode + "' /><input type='hidden' class='id_orderdetails' name='o_item[" + i + "][id_orderdetails]' value='" + id_orderdetails + "'/></td>" +







			"<td><select class='form-control product' placeholder='Product' style='width: 150px;' name='o_item[" + i + "][product]' required id='prod_" + i + "' autocomplete='off' value='" + product_name + "' ></select><input type='hidden' id='o_item_id_prod_" + i + "' name='o_item[" + i + "][id_product]' class='id_product' required='true' value='" + product_id + "' /></td>" +







			"<td><select class='form-control design' placeholder='Design' id='dsgn_" + i + "' name='o_item[" + i + "][design]' required autocomplete='off' style='width: 150px;'  value='" + design_name + "' ></select><input type='hidden' id='o_item_id_dsgn_" + i + "' name='o_item[" + i + "][id_design]' class='id_design'  value='" + design_id + "' /></td>" +







			"<td><select id='sub_design_" + i + "' name='o_item[" + i + "][sub_design]' class='form-control sub_design' placeholder='Sub Design' style='width: 150px;'  value='" + sub_design_name + "' ></select><input type='hidden' class='id_sub_design' name='o_item[" + i + "][id_sub_design]' id='o_item_sub_design_" + i + "' required autocomplete='off' value='" + sub_design_id + "' /></td>" +







			"<td><select style='width: 100px;' class='form-control purity' name='o_item[" + i + "][purity]' id='purity" + i + "' required='true' /><input type='hidden' name='o_item[" + i + "][id_purity]'  class='id_purity'  value='" + id_purity + "'  id='id_purity" + i + "' required='true'/></td>" +







			"<td><input type='text' class='form-control weight' name='o_item[" + i + "][weight]' placeholder='Enter Gross Weight'  required id='weight_" + i + "' autocomplete='off' style='width: 75px;'  value='" + gross_wt + "' /></td>" +







			//"<td><input type='text' class='form-control less_wt' name='o_item[" + i + "][less_wt]' placeholder='Enter Less Weight'  required id='less_wt_" + i + "' autocomplete='off' style='width: 75px;' value='" + less_wt + "' readonly /></td>" +





			'<td><div class="form-group"><div class="input-group"><input class="form-control less_wt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="o_item[' + i + '][less_wt]" step="any" value="' + tot_stone_wt + '" readonly="" style="width:100px;"><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" name="o_item[' + i + '][stone_details]" value=' + JSON.stringify(stone_details) + '></div></div></td>' +







			"<td><input type='text' class='form-control net_wt' name='o_item[" + i + "][net_wt]' placeholder='Enter Net Weight'  required id='net_wt_" + i + "' autocomplete='off' style='width: 75px;' value='" + net_wt + "' readonly /></td>" +







			"<td><select  class='form-control size' placeholder='Size' name='o_item[" + i + "][size]' required='true' style='width: 100px;'/></td>" +







			"<td><input type='number' class='form-control qty' placeholder='Pcs' name='o_item[" + i + "][totalitems]' required='true' style='width: 70px;'  value='" + pcs + "' id='qty_" + i + "'/></td>" +







			"<td><input type='number' class='form-control wast_percent' placeholder='Wast %' name='o_item[" + i + "][wast_percent]' required='true' style='width: 70px;'  value='" + wast_perc + "' /></td>" +







			"<td><input type='number' class='form-control wast_wgt' placeholder='Wast Weight' name='o_item[" + i + "][wast_wgt]' required='true' style='width: 100px;'  value='" + wast_wt + "' /></td>" +







			"<td><select class='mc_type' class='form-control' name='o_item[" + i + "][id_mc_type]' style='width: 100px;'value=''><option value='1'>Piece</option><option value='2' selected>Gram</option></select></td>" +







			"<td><input type='number' step='any' class='form-control mc' placeholder='Total MC' name='o_item[" + i + "][mc]' style='width: 100px;' value='" + tag_mc_value + "' /></td>" +







			"<td><a href='#' onClick='create_new_empty_est_cus_charges_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='charges_details' name='o_item[" + i + "][charges_details]' value=" + JSON.stringify(charges_details) + "></td>" +







			"<td><input style='width:100px' type='text' class='form-control value_charge' name='o_item[" + i + "][value_charge]' value='" + charge_value + "' readonly /></td>" +







			"<td style='display:none;'><a href='#' onClick='create_new_empty_est_cus_stone_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='stone_details' name='o_item[" + i + "][stone_details]' value=" + JSON.stringify(stone_details) + "></td>" +







			"<td><input type='number' step='any' class='form-control stn_amt' placeholder='Amount' name='o_item[" + i + "][stn_amt]' step='any' style='width: 100px;' readonly value='" + stone_price + "'/></td>" +







			"<td><input type='text' class='form-control order_rate' placeholder='Order Rate' name='o_item[" + i + "][order_rate]' value='" + order_rate + "' required='true'  style='width: 100px;'/></td>" +







			"<td><input type='text' class='form-control taxable' placeholder='Amount' name='o_item[" + i + "][taxable]' value='" + tax_amount + "' 	required='true' readonly='true' style='width: 100px;'/></td>" +







			"<td><input type='text' class='form-control order_amt' placeholder='Amount' name='o_item[" + i + "][rate]' value='" + amount + "' required='true' readonly='true' style='width: 100px;'/></td>" +







			'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_img" value=\'' + order_img + '\' name="o_item[' + i + '][order_img]"></td>' +







			'<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" value="' + description + '" name="o_item[' + i + '][description]"></td>' +







			"<td><input class='form-control datemask date due_date' data-date-format='dd-mm-yyyy' placeholder='Due Days' value=" + due_date + " name='o_item[" + i + "][due_date]' required='true' readonly style='width: 100px;'/></td>" +







			"<td style='display:none;'><input class='form-control datemask date smith_due_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_due_date]' type='text' value='" + smith_due_date + "' required='true' placeholder='Smith Due Date' readonly style='width: 100px;'/></td>" +







			"<td style='display:none;'><input class='form-control datemask date smith_rem_dt' data-date-format='dd-mm-yyyy' name='o_item[" + i + "][smith_remainder_date]' type='text' value='" + smith_rem_date + "' required='true' placeholder='Smith Reminder Date'   readonly style='width: 100px;'/>" +



			"</td>" +







			'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a><a href="#" onClick="copy_cus_order_row($(this).closest(\'tr\'))" class="btn btn-warning btn-del"><i class="fa fa-copy"></i></a></td>'







		"</tr>";







		$('#item_detail tbody').append(html);



		$('.date').datepicker({ dateFormat: 'yyyy-mm-dd' })



		let row = $('#item_detail tbody tr:last');



		row.find('.mc_type').val(mc_type);



		console.log(row);





		// Category







		$('#category' + i).append(



			$("<option></option>")



				.attr("value", "")



				.text('-Choose-')



		);







		$.each(CategorysArr, function (key, item) {



			$('#category' + i).append(



				$("<option></option>")



					.attr("value", item.id_ret_category)



					.text(item.name)



			);



		});



		$('#category' + i).val(cat_id);



		let row_lastObj = $('#item_detail tbody #detail' + i);







		if (cat_id > 0) {







			get_cat_purity(row_lastObj, cat_id, purity);











			var CatData = filterByCatId('id_ret_category', cat_id);







			if (CatData) {







				row_lastObj.find(".id_metal").val(CatData.id_metal);







				row_lastObj.find(".tax_group").val(CatData.tgrp_id);







				getTaxGroupDetail(row_lastObj, CatData.tgrp_id);



			}



			$.each(OrderProducts, function (key, item) {



				$('#prod_' + i).append(



					$("<option></option>")



						.attr("value", item.pro_id)



						.text(item.product_name)



				);



			});





			$(".product").select2(



				{



					placeholder: "Select product",



				});







			if (product_id > 0) {



				$('#prod_' + i).select2("val", product_id);



				get_product_size(product_id, row_lastObj, size);



				get_Activedesign(row_lastObj, product_id);







			}







			if (tag_mc_type > 0) {







				row_lastObj.find(".mc_type").val(tag_mc_type);







			}





			setTimeout(() => {



				if (purity > 0) {



					$('#purity' + i).val(purity);



				}

			}, 1000);



		}





	}

}











function get_Activesection() {

	$("#section option").remove();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_sectionBranchwise',

		data: ({ 'id_section': '' }),

		dataType: "JSON",

		type: "POST",

		success: function (data) {

			section_details = data;

			var id = $("#id_section").val();

			$.each(section_details, function (key, item) {

				if ($('#id_branch').val() == item.id_branch && item.is_home_bill_counter != 1) {

					$("#section").append(

						$("<option></option>")

							.attr("value", item.id_section)

							.text(item.section_name)

					);

				}

			});



			$("#section").select2("val", (id != '' && id > 0 ? id : ''));

		}

	});

}





$(document).on('change', "#branch_select", function (e) {



	var val = this.value;



	if (val.length >= 1) {



		// getSearchDesign(val, this.id);



		var item_type = $("#item_type").val();



		if (item_type == 1) {



			$("#section option").remove();



			$.each(section_details, function (key, item) {

				if (val == item.id_branch && item.is_home_bill_counter != 1) {

					$("#section").append(

						$("<option></option>")

							.attr("value", item.id_section)

							.text(item.section_name)

					);

				}

			});



		}







	}



});



$(document).on('change', "#sub_design_select", function (e) {



	var val = this.value;



	if (val.length >= 1) {



		// getSearchDesign(val, this.id);



		var item_type = $("#item_type").val();



		if (item_type == 1) {



			getNonTagDesignDetails();



		}







	}



});





function getNonTagDesignDetails() {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_non_tag_stock/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'id_branch': $('#id_branch').val(), 'id_section': $('#section').val(), 'id_product': $('#prod_select').val(), 'id_design': $('#des_select').val(), 'id_sub_design': $('#sub_design_select').val() },

		success: function (data) {

			//alert(data);

			console.log(data);

			var curr_used_gross = 0;

			var curr_used_pcs = 0;

			if ($("#item_type").val() == 1) {

				$('#custrepair_item_detail> tbody  > tr').each(function (index, tr) {

					if ($(this).find('.item_type').val() == 2) {

						curr_used_gross += parseFloat(($(this).find('.gross_wt').val() == '' ? 0 : $(this).find('.gross_wt').val()));

						curr_used_pcs += parseFloat(($(this).find('.pcs').val() == '' ? 0 : $(this).find('.pcs').val()));

					}

				});

				if (data != "") {

					$('.tot_blc_pcs').val(data.no_of_piece - curr_used_pcs);

					$('.tot_blc_gwt').val(data.gross_wt - curr_used_gross);

					$('.blc_pcs').html(data.no_of_piece - curr_used_pcs);

					$('.blc_gwt').html(data.gross_wt - curr_used_gross);

				}

				else {

					$('.tot_blc_pcs').val(0);

					$('.tot_blc_gwt').val(0);

					$('.blc_pcs').html(0);

					$('.blc_gwt').html(0);

				}

			}

		}

	});

}







$(document).on('change', "#pcs", function (e) {



	var val = this.value;



	var item_type = $("#item_type").val();



	if (val != '' && item_type == 1) {



		balance_pcs = isNaN(parseFloat($('.tot_blc_pcs').val())) ? 0 : parseFloat($('.tot_blc_pcs').val());



		pcs = isNaN(parseFloat(val)) ? 0 : parseFloat(val);



		blc = balance_pcs - pcs;



		if (blc < 0) {

			this.value = balance_pcs;

		} else {

			//$('.tot_blc_pcs').val(blc);



			$('.blc_pcs').html(parseFloat(blc).toFixed(3));

		}





	}



});







$(document).on('change', "#gross_wt", function (e) {



	var val = this.value;



	var item_type = $("#item_type").val();



	if (val != '' && item_type == 1) {



		balance_wt = isNaN(parseFloat($('.tot_blc_gwt').val())) ? 0 : parseFloat($('.tot_blc_gwt').val());



		wt = isNaN(parseFloat(val)) ? 0 : parseFloat(val);



		blc_wt = balance_wt - wt;



		if (blc_wt < 0) {

			this.value = balance_wt;



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered weight greater than available weight.' });



		} else {

			//	$('.tot_blc_gwt').val(blc_wt);



			$('.blc_gwt').html(parseFloat(blc_wt).toFixed(3));

		}





	}



});





$("#repair_delivered").on('click', function () {



	$("div.overlay").css("display", "block");



	if ($("input[name='id_orderdetails[]']:checked").val()) {



		var selected = [];



		var approve = false;



		var is_remark_entered = true;



		$("#repair_order_list tbody tr").each(function (index, value) {



			if ($(value).find("input[name='id_orderdetails[]']:checked").is(":checked")) {



				// if ($(value).find(".id_orderdetails").val() != '' && $(value).find(".kar_remark").val() == '') {



				// 	$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter Remark' });



				// 	$(value).find(".kar_remark").focus();



				// 	$('input[name=upd_status_btn]').removeAttr('checked');



				// 	is_remark_entered = false;

				// }



				if ($(value).find(".order_type").val() == 4 && $(value).find(".orderstatus").val() == 4) {



					transData = {



						'id_orderdetails': $(value).find(".id_orderdetails").val(),

						'order_type': $(value).find(".order_type").val(),

					}



					selected.push(transData);

				} else if ($(value).find(".order_type").val() != 4) {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Stock Repair Order Item Can Only Be Marked As Delivered' });



				} else if ($(value).find(".orderstatus").val() != 4) {



					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Delivery Ready Status can Only be Updated as Delivered' });



				}



			}







		});



		req_data = selected;



		if (req_data.length) {

			update_repair_deliver_order_status(req_data);

		}



	}



	else {



		$("div.overlay").css("display", "none");



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Order' });



	}



});



function update_repair_deliver_order_status(req_data) {



	my_Date = new Date();



	$.ajax({



		url: base_url + 'index.php/admin_ret_order/repair_deliver_order_status?nocache=' + my_Date.getUTCSeconds(),



		method: "post",



		async: false,



		data: ({ 'req_data': req_data }),



		success: function (data) {



			window.location.reload()



		}



	});



}



/* estimation no fetch tags */



$('#esti_search').on('click', function () {

	if ($('#branch_select').val()!= ''){

		get_estimation_data();

	} else {

		$.toaster({
			priority: "danger",
			title: "Warning!",
			message: "" + "</br>Please select branch",
			settings: {'timeout': 5000}
		});

	}


})





function get_estimation_data() {



	let searchTxt = $('#est_no').val();



	$.ajax({



		type: 'POST',

		url: base_url + 'index.php/admin_ret_order/get_estimation_tags/?nocache=' + my_Date.getUTCSeconds(),



		dataType: 'json',



		data: { 'estiNo': searchTxt, 'id_branch': $("#id_branch").val() },



		success: function (data) {



			console.log(data);



			if (data[0].id_customer != '' && data[0].id_customer != undefined) {





				$("#cus_name").val(data[0].cus_name);



				$("#cus_id").val(data[0].id_customer);



				$("#cus_state").val(data[0].id_state);



			}







			create_new_empty_tag_reserve(data);



		}



	});

}







function create_new_empty_tag_reserve(data) {





	order_type = 5;



	order_type_name = "Tag Reserve";



	$('#item_detail > tbody > tr').each(function (idx, row) {



		let records = data.filter(tag => tag.tag_id == $(this).find('.order_tag_id').val());



		if (records.length > 0) {



			let index = data.indexOf(records[0]);



			if (index !== -1) {



				data.splice(index, 1);



			}



		}



	});





	let html = "";











	$.each(data, function (key, item) {



		var a = $("#i_increment").val();



		var i = ++a;



		$("#i_increment").val(i);





		tag_code = item.tag_code;



		tag_id = item.tag_id;



		cat_id = item.id_ret_category;



		metal_type = item.metal_type;



		purity = item.purity;



		amount = item.item_cost;



		tax_amount = item.item_total_tax;



		order_rate = item.est_rate_per_grm;



		product_name = item.product_name;



		product_id = item.product_id;



		design_name = item.design_name;



		design_id = item.design_id;



		sub_design_name = item.sub_design_name;



		sub_design_id = item.id_sub_design;



		gross_wt = item.gross_wt;



		less_wt = item.less_wt;



		net_wt = item.net_wt;



		size = parseInt(item.size);



		pcs = item.piece;



		calculation_based_on = item.calculation_based_on;



		sales_mode = item.sales_mode;



		wast_perc = item.wastage_percent;



		wast_wt = parseFloat(parseFloat(net_wt) * parseFloat(wast_perc / 100)).toFixed(3);



		tag_mc_type = item.mc_type;



		mc_value = item.mc_value;



		id_orderdetails = item.id_orderdetails;



		sales_value = item.sales_value;



		description = item.description;



		cat_id = item.cat_id;



		let stone_details = [];



		let charges_details = [];



		let pre_img_resource = [];



		let stone_price = 0;



		var purity = item.purid;





		var cus_due_date = $('#cus_due_date').val();



		var smith_due_date = $('#smith_due_date').val();



		var smith_rem_date = $('#smith_remainder_date').val();





		$.each(item.charges_details, function (ckey, citem) {



			charges_details.push({ 'value_charge': citem.charge_value, 'id_charge': citem.charge_id });



		});



		charge_value = item.charge_value == null ? 0 : item.charge_value;



		$.each(item.stone_details, function (skey, sitem) {



			stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });



			stone_price = parseFloat(stone_price) + parseFloat(sitem.amount);



		});





		html += "<tr id='detail" + i + "' class='" + i + "'>" +



			"<td style='display:none'>" + order_type_name + "<input type='hidden' name='o_item[" + i + "][order_type]' id='" + i + "' class='form-control order_type' value='" + order_type + "'></td>" +







			"<td><input class='form-control tag_name' type='text' name='o_item[" + i + "][tag_name]' placeholder='Enter tag code' required autocomplete='off' value='" + tag_code + "' style='width: 100px;' readonly /><input class='order_tag_id' type='hidden' name='o_item[" + i + "][tag_id]' placeholder='Enter tag Id' required value='" + tag_id + "' /></td>" +



			"<td style='display:none'><select class='form-control category' name='o_item[" + i + "][category]' id='category" + i + "' required='true' style='width: 100px;' value='" + cat_id + "'/><input type='hidden' class='id_category' name='o_item[" + i + "][id_category]' id='id_category" + i + "' required='true' value='" + cat_id + "'/><input type='hidden' class='id_metal' name='o_item[" + i + "][id_metal]' id='id_metal" + i + "' required='true' value='" + metal_type + "' /><input type='hidden' class='tax_group' name='o_item[" + i + "][tax_group]' id='tax_group" + i + "' required='true'/><input type='hidden' name='o_item[" + i + "][cgst]' class='o_cgst' /><input type='hidden' class='o_sgst' name='o_item[" + i + "][sgst]' /><input type='hidden' class='o_igst' name='o_item[" + i + "][igst]' /><input type='hidden' class='calculation_based_on' name='o_item[" + i + "][calculation_based_on]' value='" + calculation_based_on + "' /><input type='hidden' class='sales_mode' name='o_item[" + i + "][sales_mode]' value='" + sales_mode + "' /><input type='hidden' class='id_orderdetails' name='o_item[" + i + "][id_orderdetails]' value='" + id_orderdetails + "'/><input type='hidden' class='act_sales_value' name='o_item[" + i + "][act_sales_value]' value='" + sales_value + "'/></td>" +



			"<td><select class='form-control product' name='o_item[" + i + "][product]' required id='prod_" + i + "' style='width: 150px;' value='" + product_name + "'> <option value='" + product_id + "'>" + product_name + "</option> </select> <input type='hidden' id='o_item_id_prod_" + i + "' name='o_item[" + i + "][id_product]' class='id_product' required='true' value='" + product_id + "' /></td>" +





			"<td><select class='form-control design'  id='dsgn_" + i + "' name='o_item[" + i + "][design]' style='width: 150px;' value='" + design_name + "'>  <option value='" + design_id + "'>" + design_name + "</option></select><input type='hidden' id='o_item_id_dsgn_" + i + "' name='o_item[" + i + "][id_design]' class='id_design'  value='" + design_id + "' /></td>" +



			"<td><select class='form-control sub_design'  id='sub_design_" + i + "' name='o_item[" + i + "][sub_design]' style='width: 150px;'  value='" + sub_design_name + "'><option value='" + sub_design_id + "'>" + sub_design_name + "</option> </select><input type='hidden' class='id_sub_design' name='o_item[" + i + "][id_sub_design]' id='o_item_sub_design_" + i + "' value='" + sub_design_id + "' /></td>" +



			"<td><select style='width: 100px;' class='form-control purity' name='o_item[" + i + "][purity]' id='purity" + i + "' required='true' > <option value='" + item.purid + "'>" + item.purname + "</option> </select> <input type='hidden' name='o_item[" + i + "][id_purity]' class='id_purity' id='id_purity" + i + "' value=" + item.purid + " required='true'/></td>" +



			"<td><input type='text' class='form-control weight' name='o_item[" + i + "][weight]' placeholder='Enter Gross Weight'  required id='weight_" + i + "' autocomplete='off' style='width: 75px;'  value='" + gross_wt + "' /></td>" +







			"<td><input type='text' class='form-control less_wt' name='o_item[" + i + "][less_wt]' placeholder='Enter Less Weight'  required id='less_wt_" + i + "' autocomplete='off' style='width: 75px;' value='" + less_wt + "' readonly /></td>" +







			"<td><input type='text' class='form-control net_wt' name='o_item[" + i + "][net_wt]' placeholder='Enter Net Weight'  required id='net_wt_" + i + "' autocomplete='off' style='width: 75px;' value='" + net_wt + "' readonly /></td>" +



			"<td><select  class='form-control size' placeholder='Size' name='o_item[" + i + "][size]' required='true' style='width: 100px;'/></td>" +



			"<td><input type='number' class='form-control qty' placeholder='Pcs' name='o_item[" + i + "][totalitems]' required='true' style='width: 70px;'  value='" + pcs + "' id='qty_" + i + "'/></td>" +



			"<td><input type='number' class='form-control wast_percent' placeholder='Wast %' name='o_item[" + i + "][wast_percent]' required='true' style='width: 70px;'  value='" + wast_perc + "' /></td>" +



			"<td><input type='number' class='form-control wast_wgt' placeholder='Wast Weight' name='o_item[" + i + "][wast_wgt]' required='true' style='width: 100px;'  value='" + wast_wt + "' /></td>" +







			"<td><select class='mc_type' class='form-control' name='o_item[" + i + "][id_mc_type]' style='width: 100px;'><option value='1'>Piece</option><option value='2' selected>Gram</option></select></td>" +







			"<td><input type='number' step='any' class='form-control mc' placeholder='Total MC' name='o_item[" + i + "][mc]' style='width: 100px;' value='" + mc_value + "' /></td>" +







			"<td><a href='#' onClick='create_new_empty_est_cus_charges_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='charges_details' name='o_item[" + i + "][charges_details]' value=" + JSON.stringify(charges_details) + "></td>" +







			"<td><input style='width:100px' type='text' class='form-control value_charge' name='o_item[" + i + "][value_charge]' value='" + charge_value + "' readonly /></td>" +







			"<td><a href='#' onClick='create_new_empty_est_cus_stone_item($(this).closest(\"tr\"));' class='btn btn-success'><i class='fa fa-plus'></i></a><input type='hidden' class='stone_details' name='o_item[" + i + "][stone_details]' value=" + JSON.stringify(stone_details) + "></td>" +







			"<td><input type='text' class='form-control order_rate' placeholder='Order Rate' name='o_item[" + i + "][order_rate]' value='" + order_rate + "' required='true'  style='width: 100px;'/></td>" +







			"<td><input type='text' class='form-control taxable' placeholder='Amount' name='o_item[" + i + "][taxable]' value='" + tax_amount + "' 	required='true' readonly='true' style='width: 100px;'/></td>" +







			"<td><input type='text' class='form-control order_amt' placeholder='Amount' name='o_item[" + i + "][rate]' value='" + amount + "' required='true' readonly='true' style='width: 100px;'/></td>" +







			'<td><a href="#" onClick="update_image_upload($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden"  class="order_img" name=o_item[' + i + '][order_img]" value=' + JSON.stringify(pre_img_resource) + ' ></td>' +







			'<td><a href="#" onClick="update_order_description($(this).closest(\'tr\'));" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a><input type="hidden" class="order_des" value="' + (ctrl_page[2] == 'edit' && id_orderdetails != '' ? description : '') + '" name="o_item[' + i + '][description]"></td>' +







			"<td><input type='number' step='any' class='form-control due_date' placeholder='Due Days' value=" + cus_due_date + " name='o_item[" + i + "][due_date]' step='any' style='width: 100px;'/></td>" +









			"</td>" +



			'<td><a href="#" onClick="remove_order_row($(this).closest(\'tr\').remove());" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'







		"</tr>";







	})





	$('#item_detail tbody').append(html);



	$('.date').datepicker({ dateFormat: 'yyyy-mm-dd' })





	$('#item_detail > tbody').find('.sub_design').select2();

	$('#item_detail > tbody').find('.mc_type').select2();

	$('#item_detail > tbody').find('.product').select2();

	$('#item_detail > tbody').find('.design').select2();







	$('#item_detail > tbody').find(".product").prop("disabled", true);



	$('#item_detail > tbody').find(".design").prop("disabled", true);



	$('#item_detail > tbody').find(".sub_design").prop("disabled", true);



	// $('#item_detail > tbody').find(".purity").prop("disabled", true);



	$('#item_detail > tbody').find(".size").prop("disabled", true);



	$('#item_detail > tbody').find(".qty").prop("readonly", true);



	$('#item_detail > tbody').find(".wast_percent").prop("readonly", true);



	$('#item_detail > tbody').find(".wast_wgt").prop("readonly", true);



	load_tax_group();



	setTimeout(() => {

		calculate_orderSale_value();

	}, 1000);















}







function load_tax_group() {







	$("#item_detail tbody tr").each(function (index, value) {



		var curRow = $(this);



		var cat_id = curRow.find('.id_category').val();



		var purity = curRow.find('.id_purity').val();



		get_cat_purity(curRow, cat_id, purity);





		if (cat_id > 0) {





			var CatData = filterByCatId('id_ret_category', cat_id);



			if (CatData) {



				curRow.find(".id_metal").val(CatData.id_metal);



				curRow.find(".tax_group").val(CatData.tgrp_id);



				getTaxGroupDetail(curRow, CatData.tgrp_id);



			}



			if (product_id > 0) {



				get_Activedesign(curRow, product_id);



				get_product_size(product_id, curRow, size);



			}



			if (tag_mc_type > 0) {



				curRow.find(".mc_type").val(tag_mc_type);



			}



		}



	});









}



/* estimation no fetch tags */





function set_repair_orderlist(order) {



	var oTable = $('#neworder_list').DataTable();



	oTable.clear().draw();



	var company_name = $('#company_name').val();

	var from_date = $("#new_list1").html();

	var to_date = $("#new_list2").html();

	var branch_name = ($('#branch_name').val() != '' && $('#branch_name').val() != undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());

	var report_name = "Repair Order Karigar Allotment"

	var optional = '';



	var title = "<div style='text-align: center;'><b><span style='font-size:15pt;'>" + company_name + "</span></b><b><span style='font-size:12pt;'></span></b></br>"

		+ "<span>" + report_print(branch_name, report_name, from_date, to_date, optional) + "</span>";





	$("div.overlay").css("display", "block");



	if (order.length > 0) {



		data = [



			{

				"mDataProp": function (row, type, val, meta) {



					if (row.cus_ord_status <= 3 && (row.id_karigar == '' || row.id_karigar == null)) {



						chekbox = '<input type="checkbox" class="id_orderdetails" name="id_orderdetails[]" value="' + row.id_orderdetails + '"/> '



						return chekbox + " " + row.id_orderdetails + '<input type="hidden" class="id_product" value="' + row.id_product + '"><input type="hidden" class="id_category" value="' + row.id_category + '"><input type="hidden" class="product_name" value="' + row.product_name + '">';



					} else {



						return row.id_orderdetails + '<input type="hidden" class="id_product" value="' + row.id_product + '"><input type="hidden" class="id_category" value="' + row.id_category + '"><input type="hidden" class="product_name" value="' + row.product_name + '">';



					}







				}

			},



			{

				"mDataProp": function (row, type, val, meta) {



					id = row.id_orderdetails;



					edit_target = ("#imageModal_new");



					content = '<a href="#" class="btn btn-success btn-sm" id = "img_upload_order" value=0 data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-plus"></i></a>';



					return content;



				}

			},



			{

				"mDataProp": function (row, type, val, meta) {



					id = row.id_orderdetails;



					edit_target = ("#order_des_new");



					content = '<a href="#" class="btn btn-default btn-sm" value=0 data-toggle="modal" data-id=' + id + '  data-target=' + edit_target + '><i class="fa fa-plus"></i></a>';



					return content;



				}

			},



			{ "mDataProp": "orderno" },



			{ "mDataProp": "pur_no" },



			{ "mDataProp": "employee_name" },



			{ "mDataProp": "karigar_name" },



			{

				"mDataProp": function (row, type, val, meta) {



					var defaultDate = row.smith_due_date ? row.smith_due_date : '';



					if (row.cus_ord_status <= 3) {



						return '<input class="form-control smith_due_dt" data-date-format="dd-mm-yyyy" name="o_item["+i+"][smith_due_date]" value="' + defaultDate + '" type="text" placeholder="Smith Due Date" style="width: 100px;"/><input type="hidden" class="order_date" value="' + row.order_date + '"><input type="hidden" class="cus_ord_status" value="' + row.cus_ord_status + '">';



					} else {



						return row.defaultDate;



					}



				}



			},



			{ "mDataProp": "orter_type" },



			{ "mDataProp": "cus_name" },



			{ "mDataProp": "cus_mobile" },



			{ "mDataProp": "emp_name" },



			{ "mDataProp": "product_name" },



			{ "mDataProp": "design_name" },



			{ "mDataProp": "sub_design_name" },



			{ "mDataProp": "totalitems" },



			{ "mDataProp": "weight" },



			{ "mDataProp": "tag_code" },



			{ "mDataProp": "repair_type" },



			{ "mDataProp": "work_at" },



			{ "mDataProp": "size" },



			{

				"mDataProp": function (row, type, val, meta) { return "<span class='label bg-" + row.color + "'>" + row.orderstatus + "</span>"; }







			},



			{ "mDataProp": "customer_ref_no" },



			{

				"mDataProp": "order_date",

				"render": function (data, type, row) {

					return '<div style="width: 80px;">' + data + '</div>';

				}

			},

			{

				"mDataProp": "cus_due_date",

				"render": function (data, type, row) {

					return '<div style="width: 80px;">' + data + '</div>';

				}

			},





			{ "mDataProp": "branch_name" },



			{

				"mDataProp": function (row, type, val, meta) {





					if (row.cus_ord_status == 3) {

						return '<input class="form-control kar_remark" name="o_item["+i+"][karigar_remarks]" value="" type="text" placeholder="Karigar change Reason" style="width: 200px;"/>';

					} else {

						return '-';

					}





				}



			},

			{ "mDataProp": "kar_remark" },











			{

				"mDataProp": function (row, type, val, meta) {



					id = row.id_orderdetails;



					view_confirm = '#confirm-view';



					action_content = '<a  class="btn btn-primary btn-edit" id="edit" data-toggle="modal" data-id=' + id + ' ><label>View Image</label></a>'



					return action_content;



				}



			}





		];



		if ($('#branch_user').val() == 1) {

			data.splice(4, 1);

		}



		oTable = $('#neworder_list').dataTable({



			"bDestroy": true,



			"bInfo": true,



			"bFilter": true,



			"scrollX": '100%',



			"bSort": false,



			"dom": 'lBfrtip',



			"buttons": ['excel', 'print'],



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

					title: company_name + '-' + (from_date && to_date != '' ? from_date + '- ' + to_date : '') + ' - ',

				},





			],





			"aaData": order,



			"aoColumns": data,



			"footerCallback": function (row, data, start, end, display) {

				if (order.length > 0) {

					var api = this.api(), data;

					for (var i = 0; i <= data.length - 1; i++) {

						var intVal = function (i) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '') * 1 :

								typeof i === 'number' ?

									i : 0;

						};

						$(api.column(11).footer()).html('Total');



						if ($('#branch_user').val() == 1) {

							piece = api

								.column(15)

								.data()

								.reduce(function (a, b) {

									return intVal(a) + intVal(b);

								}, 0);

							$(api.column(15).footer()).html(formatNumber(piece, 0));

							gross_wt = api

								.column(16)

								.data()

								.reduce(function (a, b) {

									return intVal(a) + intVal(b);

								}, 0);

							$(api.column(16).footer()).html(formatNumber(gross_wt));

						} else {



							piece = api

								.column(14)

								.data()

								.reduce(function (a, b) {

									return intVal(a) + intVal(b);

								}, 0);

							$(api.column(14).footer()).html(formatNumber(piece, 0));

							gross_wt = api

								.column(15)

								.data()

								.reduce(function (a, b) {

									return intVal(a) + intVal(b);

								}, 0);

							$(api.column(15).footer()).html(formatNumber(gross_wt));



						}

					}

				} else {

					var api = this.api(), data;



					$(api.column(12).footer()).html('');

					$(api.column(13).footer()).html('');



				}

			}



		});



	}



	// $('.smith_due_dt').datepicker({ dateFormat: 'yyyy-mm-dd' });

	$(".smith_due_dt").datepicker({ startDate: new Date() });



}
function get_branches() {
	//$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/branch/branchname_list',
		dataType: 'json',
		success: function (data) {
			branch_details = data.branch;
		}
	});
}
$(document).ready(function () {
	$('#branch_select').on('change', function () {
		if (this.value != '') {
			$('#id_branch').val(this.value);
			get_all_employee();
			if (ctrl_page[2] != 'list') {
				var id_branch = this.value;
				$.each(branch_details, function (key, items) {
					if (id_branch == items.id_branch) {
						$('#cmp_state').val(items.id_state);
						$('#cmp_country').val(items.id_country);
						branch_id_city = items.id_city;
					}
				});
			}
		}
		else {
			$('#id_branch').val('');
		}
	});
});