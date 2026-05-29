var path = url_params();

var ctrl_page = path.route.split('/');

var lot_details = [];

var tax_details = [];

var purities = [];

var cur_search_tags = [];

var matel_types = [];

var metal_master = []; 

var stones = [];

var materials = [];

var stone_details = [];

var material_details = [];

var emp_details = [];

var order_adv_details = [];

var other_charges_details = [];

var item_list = [];

var stone_types = [];

var uom_details = [];

var chit_details = [];

var wast_settings_details = [];

var metal_rates = [];

var purity_rate = [];

var prod_details = [];

var cur_cat_Row;

var img_resource = [];

var pre_img_files = [];

var cus_product_details = [];

var cus_design_details = [];

var cus_sub_design_details = [];

var cat_product_details = [];

var section_details = [];

var cat_design_details = [];

var cat_sub_design_details = [];

var branch_details = [];

var cus_country = [];

var loose_product_rate = [];

var profile=[];

var tag_split_details = [];
 
var custom_split_details = [];

var tag_details = [];

var stone_rate_settings = [];

let branch_id_city = '';

let quality_code = [];

let mc_readonly = $("#allow_mc_edit").val() == 1 ? "" : "readonly";

let mc_type_selection = $("#allow_mc_edit").val() == 1 ? "" : "pointer-events : none";

let va_readonly = $("#allow_va_edit").val() == 1 ? "" : "readonly";

var manualRates = {};

var items = [];

var cht_count = 0;

var sr_count = 0;

var fin_year = [];

$(document).ready(function () {

	if (ctrl_page[2] == 'add') {

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

	var path = url_params();

	$('#status').bootstrapSwitch();

	prod_info = [];

	get_all_old_metal_rates();

	get_old_metal_categories();

	get_metal_master(); // Load metal master data from database

	$(window).scroll(function () {    // this will work when your window scrolled.

		var height = $(window).scrollTop();  //getting the scrolling height of window

		if (height > 300) {

			$(".stickyBlk").css({ "position": "fixed" });

		} else {

			$(".stickyBlk").css({ "position": "static" });

		}

	});

	switch (ctrl_page[1]) {

		case 'estimation':

			switch (ctrl_page[2]) {

				case 'list':

					get_estimation_list();

					$('#est_list1').text(moment().startOf('month').format('YYYY-MM-DD'));

					$('#est_list2').text(moment().endOf('month').format('YYYY-MM-DD'));

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

							get_estimation_list(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))

							$('#est_list1').text(start.format('YYYY-MM-DD'));

							$('#est_list2').text(end.format('YYYY-MM-DD'));

						}

					);

					$('#branch_select').on('change', function () {

						get_estimation_list();

					});

					break;

				case 'edit':

					var id_branch = $('#id_branch').val();

					get_metal_rates_by_branch(id_branch);

					get_taxgroup_items();

					get_ActiveUOM();

					get_ActiveProduct();

					// getBranchName();

					get_profile();

					getStoneRateSettings();

					getStoneRateSettings();

					getActive_quality_code();

					getFinancialYr();

					var is_eda = $('#iseda').val();

					if(is_eda == 1) {

						$('#is_eda').prop('checked', true);

					} else {

						$('#is_eda').prop('checked', false);

					}

					var metal = $('.metal').val();

					var cat_id = $('.cat_id').val();

					getestPurityDetails(metal,cat_id);

					setTimeout(function () {

						get_tag_purities();

						get_employee(id_branch);

						calculate_purchase_details();

						calculate_sales_details();

						get_wastage_settings_details();

						getCusproducts();

						getNonTagproducts();

						get_Activesections();

						homebill_design();

						homebill_sub_design();

						getOtherChargesDetails();

						get_purity_rate();

						non_tag_design();

						non_tag_sub_design();

					}, 1000);

					setTimeout(function () {

						estimation_tag_data();

					}, 2000);

					get_old_metal_Product();

					$('#branch_select').on('select2:opening', function (e) {
						if ($(this).prop('disabled')) {
							e.preventDefault(); // Prevent dropdown from opening
						}
					});

					$("#branch_select").prop('disabled', true); // To prevent changing branch 

					$('#branch_select').next('.select2-container').css('pointer-events', 'none');

					get_tag_matels();

					break;

				case 'add':

				get_tag_purities();

					getCusproducts();

					get_Activesections();

					getNonTagproducts();

					// get_branches();

					getStoneRateSettings();

					getLooseStoneProductRateSettings();

					getestPurityDetails();

					get_profile();

					getActive_quality_code();

					getFinancialYr();

					// getBranchName();

					$("#date_of_birth,#ed_date_of_birth").datepicker({ format: 'yyyy-mm-dd' });

					$("#date_of_wed,#ed_date_of_wed").datepicker({ format: 'yyyy-mm-dd' });

					// $("#country").select2({

					// 	placeholder: "Enter Country",

					// 	allowClear: true

					// });

					// $("#sel_village").select2({

					// 	placeholder: "Select Area",

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

					$("#profession").select2({

						placeholder: "Select Profession",

						allowClear: true

					});

					$("#ed_profession").select2({

						placeholder: "Select Profession",

						allowClear: true

					});

					get_profession();

					get_country();

					get_ActiveProduct();

					get_taxgroup_items();

					hide_page_open_details();

					$('#branch_select').trigger("change");

					var id_branch = $('#id_branch').val();

					get_employee(id_branch);

					get_wastage_settings_details();

					get_old_metal_Product();

					get_tag_matels();

					// --- Partly Sale: auto-prefill from URL params ---
					var urlParams = new URLSearchParams(window.location.search);
					var prefillTagCode = urlParams.get('tag_code');
					var prefillTagCodes = urlParams.get('tag_codes'); // comma-separated multi-tag
					var prefillBranchId = urlParams.get('branch_id');
					var prefillCusId = urlParams.get('cus_id');
					var prefillCusName = urlParams.get('cus_name');

					// Build tag queue: tag_codes (batch) takes priority, fallback to tag_code (single)
					var tagQueue = [];
					if (prefillTagCodes && prefillTagCodes.length > 0) {
						tagQueue = prefillTagCodes.split(',').map(function(c) { return c.trim(); }).filter(function(c) { return c !== ''; });
					} else if (prefillTagCode && prefillTagCode.length > 0) {
						tagQueue = [prefillTagCode];
					}

					// Show existing overlay spinner when coming from Partly Sale
					if (urlParams.get('from_split') == '1' && tagQueue.length > 0) {
						$('.overlay').show();
					}

					// Branch prefill (for all-branch / HO users)
					if (prefillBranchId && prefillBranchId.length > 0) {
						setTimeout(function() {
							var $branchSel = $('#branch_select');
							if (!$branchSel.prop('disabled')) {
								// All-branch user: select the branch and trigger change
								if ($branchSel.find('option[value="' + prefillBranchId + '"]').length > 0) {
									$branchSel.val(prefillBranchId).trigger('change');
								} else {
									// select2 might not have the option yet — use select2 val
									$branchSel.select2('val', prefillBranchId);
								}
								$('#id_branch').val(prefillBranchId);
							}
						}, 800);
					}

					// Customer prefill
					if (prefillCusId && prefillCusId.length > 0) {
						setTimeout(function() {
							$('#cus_id').val(prefillCusId);
							$('#est_cus_name').val(decodeURIComponent(prefillCusName || ''));
						}, 500);
					}

					// Auto-check tag details checkbox to open tagging row
					if (urlParams.get('select_tag_details') == '1') {
						setTimeout(function() {
							if (!$('#select_tag_details').is(':checked')) {
								$('#select_tag_details').prop('checked', true).trigger('change');
							}
						}, 600);
					}

					// Sequential tag scan — chains get_tag_data() calls one after another
					// so all tags end up in the same estimation form's tag details table
					function scanNextTag(queue, index) {
						if (index >= queue.length) {
							// All done — hide overlay and focus scan input
							$('.overlay').hide();
							$('#est_tag_scan').val('').focus();
							return;
						}
						$('#est_tag_scan').val(queue[index]);
						isTagPaste = true;
						setTimeout(function() {
							isTagPaste = false;
							get_tag_data(function() {
								$('#est_tag_scan').val('');
								// Small delay between scans to let DOM settle
								setTimeout(function() {
									scanNextTag(queue, index + 1);
								}, 300);
							});
						}, 200);
					}

					// Kick off scan queue (after branch & settings are loaded)
					if (tagQueue.length > 0) {
						setTimeout(function() {
							scanNextTag(tagQueue, 0);
						}, 1500);
					}

					break;

			}

			break;

	}

	if (ctrl_page[2] != "list") {

		//get_customer_list();

		get_tag_purities();

		//get_tag_matels();

		get_stones();

		get_purity_rate();

		getOtherChargesDetails();

		get_materials();

		get_stone_types();

		get_ActiveUOM();

	}

	$('input[type=radio][name="estimation[esti_for]"]').change(function () {

		$('#est_cus_name').val('');

		$('#cus_id').val('');

		if (this.value == 1) {

			$("#cus_req").css('display', 'inline');

			$("#select_catalog_details").attr('disabled', false);

			$("#select_custom_details").attr('disabled', false);

			$("#select_oldmatel_details").attr('disabled', false);

			$('#add_new_customer').prop('disabled', false);

			$('#edit_customer').prop('disabled', false);

		}

		else if (this.value == 2) {

			$("#cus_req").css('display', 'none');

			$("#select_catalog_details").attr('disabled', true);

			$("#select_custom_details").attr('disabled', true);

			$("#select_oldmatel_details").attr('disabled', true);

			$('#add_new_customer').prop('disabled', false);

			$('#edit_customer').prop('disabled', false);

		} else if (this.value == 3) {

	        $('.gst').css('display','block');

			//$('#add_new_customer').prop('disabled',true);

			//$('#edit_customer').prop('disabled',true);

		}

	});

	/* NOTE: #add_new_customer click handler with branch validation is defined below near line 17477 */



	$('#select_order_details').change(function () {

		if (this.checked) {

			$(".order_details").show();

			if ($('#estimation_order_details tbody tr').length == 0) {

				create_new_empty_est_order_row();//Create new empty est empty row

			}

		} else {

			$(".order_details").hide();

		}

	});

	$(document).on('keyup', ".orderno", function (e) {

		var orderno = this.value;					//Coaded by karthik

		var row = $(this).closest('tr');

		if ($("#branch_settings").val() == 1) {

			if ($("#id_branch").val() != "") {

				getSearchOrders(orderno, row);

			} else {

				alert("Select Branch");

			}

		} else {

			getSearchOrders(orderno, row);

		}

	});

 function getSearchOrders(searchTxt, curRow) {

		my_Date = new Date();

		$.ajax({

			url: base_url + 'index.php/admin_ret_estimation/getOrderBySearch/?nocache=' + my_Date.getUTCSeconds(),

			dataType: "json",

			method: "POST",

			data: { 'searchTxt': searchTxt, 'id_branch': $("#id_branch").val() },

			success: function (data) {

				cur_search_tags = data;

				$.each(data, function (key, item) {

					$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

						let records = data.filter(tag => tag.tag_id == $(this).find('.est_tag_id').val());

						if (records.length > 0) {

							console.log($(this).find('.est_tag_id').val());

							let index = data.indexOf(records[0]);

							if (index !== -1) {

								data.splice(index, 1);

							}

						}

					});

				});

				$(".orderno").autocomplete(

					{

						source: data,

						select: function (e, i) {

							e.preventDefault();

							var curRowItem = i.item;

							if (curRowItem.sales_mode == 2) { // 1 - Fixed Rate, 2 - Flexible

								get_metal_rates_by_branch(i.item.lot_received_at);

							}

							curRow.find('.orderno').val(i.item.label);

							curRow.find('.order_id').val(i.item.value);

							curRow.find('.prodct_name').html(curRowItem.product_name);

							curRow.find('.id_product').val(curRowItem.id_product);

							curRow.find('.design_no').val(curRowItem.design_no);

							curRow.find('.design_name').html(curRowItem.design_name);

							curRow.find('.purity').html(curRowItem.purname);

							curRow.find('.id_purity').val(curRowItem.id_purity);

							curRow.find('.sizes').html(curRowItem.size_name);

							curRow.find('.size').val(curRowItem.size);

							curRow.find('.pieces').html(curRowItem.totalitems);

							curRow.find('.totalitems').val(curRowItem.totalitems);

							curRow.find('.weight').val(curRowItem.weight);

							curRow.find('.net_weight').val(curRowItem.weight);

							curRow.find('.wast_percent').html(curRowItem.wast_percent);

							curRow.find(".wastage_max_per").val(curRowItem.wast_percent);

							curRow.find('.mc').html(curRowItem.mc);

							curRow.find('.mc_value').val(curRowItem.mc);

							curRow.find('.cost').html(curRowItem.rate);

							curRow.find(".tax_percentage").val(curRowItem.tax_percentage);

							curRow.find(".tgi_calculation").val(curRowItem.tgi_calculation);

							curRow.find(".metal_type").val(curRowItem.metal_type);

							curRow.find(".tax_group").val(curRowItem.tax_group_id);

							curRow.find(".stn_amt").val(curRowItem.stn_amt);

							curRow.find(".sales_mode").val(curRowItem.sales_mode);

							curRow.find(".purchase_cost").val(curRowItem.tag_purchase_cost);

							let pur_mc = typeof curRowItem.po_details != 'undefined' && curRowItem.po_details != null && curRowItem.po_details.length != 0 ? (!(curRowItem.po_details[0].mc_value >= 0) || curRowItem.po_details[0].mc_value == null ? 0 : curRowItem.po_details[0].mc_value) : 0;

							let pur_va = typeof curRowItem.po_details != 'undefined' && curRowItem.po_details != null && curRowItem.po_details.length != 0 ? (!(curRowItem.po_details[0].item_wastage >= 0) || curRowItem.po_details[0].item_wastage == null ? 0 : curRowItem.po_details[0].item_wastage) : 0;

							curRow.find(".pur_mc").val(pur_mc);

							curRow.find(".pur_va").val(pur_va);

							calculate_order_SaleValue();

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

								curRow.find('.order_no').val("");

								curRow.find('.order_id').val("");

							}

						},

						minLength: 1,

					});

			}

		});

	}

	$('#select_tag_details').change(function () {

		if (this.checked) {

			$(".tag_details").show();

			$("#est_tag_scan").focus();

			if ($('#estimation_tag_details tbody tr').length == 0) {

				//create_new_empty_est_tag_row();//Create new empty est empty row

			}

		}

		else {

			$(".tag_details").hide();

			$('#estimation_tag_details tbody').empty();

		}

	});

	$('#create_tag_details').on('click', function () {

		if (validateTagDetailRow()) {

			create_new_empty_est_tag_row();

		}

	});

	$('#create_order_details').on('click', function () {

		if (validateOrderDetailRow()) {

			create_new_empty_est_order_row();

		} else {

			alert("Please fill required fields");

		}

	});

	$('#tag_search').on('click', function () {

		get_tag_data(function () {

			$('#est_tag_scan').val('');

			$("#est_tag_scan").focus();

		});

	});

	$("#est_tag_scan").on('paste', function() {

		setTimeout(function() {

			$("#est_tag_scan").val($("#est_tag_scan").val().trim());

			get_tag_data(function() {

				$('#est_tag_scan').val('');

				$("#est_tag_scan").focus();

			});

		}, 100);

	});

	let isTagPaste = false;

	$("#est_tag_scan").on('keypress', function (e) {

		if (e.which == 13) {

			if ($('#est_tag_scan').val() != '' && $('#est_tag_scan').val().length > 6) {

				console.log("Paste triggered");

				isTagPaste = true;

				setTimeout(function () {

					isTagPaste = false;

					get_tag_data(function () {

						$('#est_tag_scan').val('');

						$("#est_tag_scan").focus();

					});

				}, 100);

			}

		}

	});

	$('#tag_barcode_search').on('click', function () {

		get_tag_data(function () {

			$('#est_tag_barcode_scan').val('');

			$("#est_tag_barcode_scan").focus();

		});

	});

	$("#est_tag_barcode_scan").on('paste', function() {

		setTimeout(function() {

			$("#est_tag_barcode_scan").val($("#est_tag_barcode_scan").val().trim());

			get_tag_data(function() {

				$('#est_tag_barcode_scan').val('');

				$("#est_tag_barcode_scan").focus();

			});

		}, 100);

	});

	$("#est_tag_barcode_scan").on('keypress', function (e) {

		if (e.which == 13) {

			if ($('#est_tag_barcode_scan').val() != '' && $('#est_tag_barcode_scan').val().length > 6) {

				setTimeout(function () {

					get_tag_data(function () {

						$('#est_tag_barcode_scan').val('');

						$("#est_tag_barcode_scan").focus();

					});

				}, 100);

			}

		}

	});

	$('#tag_merge_srch,#tag_mrg_barcode_srch').on('click', function () {

		get_tag_merge_data();

	});

	function get_tag_data_29_09(callback) {

		$(".overlay").css('display', 'block');

		var tagData = $.trim($('#est_tag_scan').val());

		var old_tag_no = $.trim($('#est_tag_barcode_scan').val());

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

				searchTxt = tagData;

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

		}

		else if(old_tag_no!='') {

			var tagData = $.trim($('#est_tag_barcode_scan').val().replaceAll(' ', ''));

			var istagId = (tagData.search("/") > 0 ? true : false);

			//var isTagCode = (tagData.search("-") > 0 ? true : false);

			var isTagCode = true;

			if (istagId) {

				var tId = tagData.split("/");

				searchTxt = (tId.length >= 2 ? tId[0] : "");

				type = "tag_id";

			}

			else if (isTagCode) {

				searchTxt = tagData;

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

		else if($('#est_order').val()!='')

		{

		    tag_search = true;

		}

		if (tag_search) {

			my_Date = new Date();

			$.ajax({

				type: 'POST',

				url: base_url + 'index.php/admin_ret_estimation/getTaggingScanBySearch/?nocache=' + my_Date.getUTCSeconds(),

				dataType: 'json',

				data: { 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val(),'order_no':$('#est_order').val()},

				success: function (data) {

					if (data != null && data.length > 0) {

						let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

						let emp_select = $("#emp_select").val();

						rowExist = false;

						if ($('#estimation_tag_details > tbody').length > 0) {

							$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

								//rowExist=false;

								if (data[0].tag_id == $(this).find('.est_tag_id').val()) {

									$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists..' });

									rowExist = true;

								}

								if (data[0].metal_type != $(this).find('.metal_type').val()) {

									$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Add The Same Metal Items ..' });

									rowExist = true;

								}

							});

						}

						if (!rowExist) {

							var row = "";

							var stone_details = [];

							var other_metal_details = [];

							var tag_other_itm_amount = 0;

							$.each(data[0].stone_details, function (skey, sitem)

							{



								stone_details.push({

									"show_in_lwt": sitem.is_apply_in_lwt,

									"stone_id": sitem.stone_id,

									"stones_type": sitem.stone_type,

									"stone_pcs": sitem.pieces,

									"stone_wt": sitem.wt,

									"stone_price": sitem.amount,

									"stone_rate": sitem.rate_per_gram,

									"stone_uom_id": sitem.uom_id,

									"stone_cal_type": sitem.stone_cal_type

								});

							});



							$.each(data[0].other_metal_details, function (skey, sitem) {

								tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

								other_metal_details.push({

									"tag_other_itm_id": sitem.tag_other_itm_id,

									"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

									"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

									"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

									"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

									"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

									"tag_other_itm_uom": sitem.tag_other_itm_uom,

									"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

									"tag_other_itm_mc": sitem.tag_other_itm_mc,

									"tag_other_itm_rate": sitem.tag_other_itm_rate,

									"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

									"tag_other_itm_amount": sitem.tag_other_itm_amount,

									"purname": sitem.purname,

				                    "metal_name": sitem.metal_name,



								});

							});



							let pur_mc = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].mc_value >= 0) || data[0].po_details[0].mc_value == null ? 0 : data[0].po_details[0].mc_value) : 0;

							let pur_va = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].item_wastage >= 0) || data[0].po_details[0].item_wastage == null ? 0 : data[0].po_details[0].item_wastage) : 0;

							let rate_field = data[0].rate_field;

							let rate_per_grm = 0;

							if(rate_field != '' && data[0].stone_type==0) // For Ornaments Product

							{

								rate_per_grm = (isNaN($('.'+rate_field).html()) || $('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();

							}

							else

							{

								$.each(loose_product_rate,function(key,items)

								{

									console.log('looseproduct',items);

									var product_centwt = 0;

									if(data[0].stone_type==2) // for diamond products

									{

										var pcs  = (isNaN(data[0].piece) || data[0].piece == '')  ? 0 : parseInt(data[0].piece);

										var grs_wt  = (isNaN(data[0].gross_wt) || data[0].gross_wt == '')  ? 0 : parseFloat(data[0].gross_wt);

										product_centwt = parseFloat(((grs_wt)/(pcs))*100).toFixed(3);

									}

									if($('#branch_select').val() == items.id_branch && data[0].lot_product == items.id_product && data[0].design_id == items.id_design && data[0].id_sub_design == items.id_sub_design && data[0].quality_id == items.quality_id && data[0].uom_id==items.uom_id)

									{

										if(product_centwt > 0)

										{

											if(product_centwt >= parseFloat(items.from_cent) && product_centwt <= parseFloat(items.to_cent))

											{

												rate_per_grm = items.max_rate;

											}

										}

										else

										{

											rate_per_grm = items.max_rate;

										}

									}

								});

							}

							var wast_wgt = parseFloat(parseFloat(data[0].net_wt) * parseFloat(data[0].retail_max_wastage_percent / 100)).toFixed(3);

							var select_emp = "<option value=''>-Select Employee-</option>";

							$.each(emp_details, function (pkey, emp) {

								console.log(emp_details);

								let emp_selected = "";

								if (emp.id_employee == emp_select) {

									emp_selected = "selected = 'selected'";

								}

								select_emp += "<option " + emp_selected + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

							});

							var mc_min_limit = parseFloat(data[0].mc_va_limit.mc_min);

							var va_min_limit = parseFloat(data[0].mc_va_limit.wastag_min);

							var va_wt_min_limit = parseFloat(parseFloat(data[0].net_wt) * parseFloat(data[0].mc_va_limit.wastag_min / 100)).toFixed(3);

							var tag_image = base_url + 'assets/img/no_image.png';

							if (data[0].tag_images.length > 0) {

								$.each(data[0].tag_images, function (key, val) {

									if (val.is_default == 1) {

										tag_image = base_url + 'assets/img/tag/' + val.image;

									}

								});

							}

							var child_tag_details = [];

							var rowId = new Date().getTime();

							row += '<tr id="' + rowId + '">'

								+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + data[0].label + ' placeholder="Enter tag code" required autocomplete="off"/><input class="id_mapping_details" type="hidden" name="est_tag[id_mapping_details][]" value="' + data[0].id_mapping_details + '" /><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + data[0].tag_id + ' /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + data[0].id_orderdetails + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + data[0].order_no + '"/><input class="rate_field" type="hidden"  value="' + data[0].rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + data[0].market_rate_field + '"/></td>'

								+ '<td><a href="#" onClick="create_new_empty_est_tag_merge_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" id="it_has_childtag" value= "" ><input type="hidden" name="est_tag[child_tag_details][]" id="child_tag_details" value="' + JSON.stringify(child_tag_details, null, 5) + '"/></td>'

								+ '<td><img src="' + tag_image + '" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="' + data[0].tag_id + '" id="edit"><i class="fa fa-eye" ></i></a></td>'

								+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_tag[item_emp_id][]" >' + select_emp + '</select></td>'

								+ '<td><input type="checkbox" class="partial" ' + (data[0].calculation_based_on == 3 ? 'disabled' : '') + ' ><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"></td>'

								+ '<td><div class="prodct_name">' + data[0].product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + data[0].lot_product + ' /><input type="hidden" class="metal_type" value=' + data[0].metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + data[0].scheme_closure_benefit + '></td>'

								+ '<td><div class="design_name">' + data[0].design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + data[0].design_id + ' /></td>'

								+ '<td><div class="sub_design_name">' + data[0].sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value=' + data[0].id_sub_design + ' /><input type="hidden" class="quality_id" name="est_tag[quality_id][]" value='+data[0].quality_id+'><input type="hidden" class="stone_type" value='+data[0].stone_type+'><input type="hidden" class="uom_id" value='+data[0].uom_id+'><input type="hidden" class="stone_calculation_based_on" value='+data[0].stone_calculation_based_on+'></td>'

								+ '<td><div class="section_name">' + data[0].section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + data[0].id_section + ' /></td>'

								+ '<td><div class="huid">' + data[0].huid + '</div></td>'

								+ '<td><div class="order_no"></td>'

								+ '<td><div class="purity">' + data[0].purname + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + data[0].purity + ' /></td>'

								+ '<td><div class="sizes">' + data[0].size_name + '</div><input type="hidden" class="size" name="est_tag[size][]"  value="' + data[0].size + '" /></td>'

								//	+'<td><div class="pieces">'+data[0].piece+'</div><input type="hidden" class="piece" name="est_tag[piece][]" value='+data[0].piece+' /></td>'

								+ '<td><input type="number" class="form-control piece" name="est_tag[piece][]"  style="width:100px;" value=' + data[0].piece + ' readonly /><input type="hidden" class="act_piece" name="est_tag[act_piece][]" value=' + data[0].piece + ' readonly /></td>'

								+ '<td><input type="text" class="form-control gwt" name="est_tag[gwt][]" step="any" value=' + data[0].gross_wt + ' readonly style="width:100px;" /><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + data[0].gross_wt + ' /><input type="hidden" class="act_gwt" value=' + data[0].gross_wt + ' /></td>'

								//+'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" value='+data[0].less_wt+' readonly/></td>'

								+ '<td><div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + data[0].less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

								+ '<td><div class="nwt">' + data[0].net_wt + '</div><input type="hidden" class="form-control tot_nwt" name="est_tag[nwt][]" value=' + data[0].net_wt + ' /><input type="hidden" class="tot_tag_nwt"  value=' + data[0].net_wt + ' style="width:100px;" /></td>'

								+ '<td><input type="text" class="form-control market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + (rate_per_grm > 0 ? rate_per_grm : data[0].sales_value) + '" style="width:100px;" /></td>'

								+ '<td><div class="wastage" style="display:none">' + data[0].retail_max_wastage_percent + '</div><input type="number" name="est_tag[wastage][]" class="form-control wastage_max_per" value=' + data[0].retail_max_wastage_percent + ' style="width:100px;" ' + va_readonly + '/><input type="hidden" class="va_min_limit" value="' + va_min_limit + '"></td>'

								+ '<td><div class="act_wast_wt" style="display:none">' + wast_wgt + '</div><input type="number" ' + va_readonly + ' name="est_tag[est_wastage_wt][]" class="form-control est_wastage_wt" value="" style="width:100px;" /><input type="hidden" class="va_wt_min_limit" value="' + va_wt_min_limit + '"></td>'

								//+'<td><div class="mc">'+data[0].tag_mc_value+'</div></td>'

								+ '<td><select class="form-control est_mc_type" '+mc_readonly+' style="width:80px;'+mc_type_selection+'"><option value="2" ' + (data[0].tag_mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (data[0].tag_mc_type == 1 || data[0].tag_mc_type == null ? 'selected' : '') + '>Piece</option></select></td>'

								+ '<td><div class="mc_val" style="display:none">' + data[0].tag_mc_value + '</div><input class="form-control act_mc_value"  name="est_tag[mc][]" type="number" '+mc_readonly+' value=' + data[0].tag_mc_value + ' style="width:100px;" /><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" />Tot Mc:<span class="tot_mc" ></span><input type="hidden" class="mc_min_limit" value="' + mc_min_limit + '"></td>'

								//	+'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value='+JSON.stringify(stone_details)+'><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

								+ '<td><div class="cost">' + (data[0].sales_value) + '</div> <input type="hidden" name="est_tag[total_cgst][]" class="sale_cgst"/><input type="hidden" class="sale_sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="sale_igst" name="est_tag[total_igst][]"/> <input type="hidden" class="sale_taxable_amount" name="est_tag[sale_taxable_amount][]"/> <input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (data[0].sales_value) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + data[0].item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + data[0].calculation_based_on + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + data[0].tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + data[0].tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + data[0].tax_percentage + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + data[0].stone_price + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + data[0].certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + data[0].tag_mc_type + ' /><input class="mc_value" type="hidden"  value=' + data[0].tag_mc_value + ' /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="act_sales_value" value=' + (rate_per_grm >0 && data[0].stone_type!=0 ? rate_per_grm  : data[0].sales_value) + '><input type="hidden" class="charge_value" value=' + data[0].charge_value + ' name="est_tag[charge_value][]"><input type="hidden" class="sales_mode" value=' + data[0].sales_mode + ' ><input type="hidden" class="purchase_cost" value=' + data[0].tag_purchase_cost + ' name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_tag[pur_va][]" ><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value=' + JSON.stringify(other_metal_details) + '><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" value="' + tag_other_itm_amount + '"></td>'

								+ '<td><a href="#" onClick="remove_tag_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

								+ '</tr>';

							$('#estimation_tag_details tbody').append(row);

							$('#estimation_tag_details > tbody').find('.item_emp_id').select2();

							if (data[0].id_tag_mapping != '') {

								var collection_details = data[0].collection_details;

								if (collection_details.length > 0) {

									$.each(collection_details, function (key, items) {

										$('#estimation_tag_details > tbody').each(function (idx, row) {

											if (items.tag_id != $(this).find('.est_tag_id').val()) {

												$('#id_tag_mapping').val(data[0].id_tag_mapping);

												$('#collection_confirm').modal('show');

											}

										});

									});

								}

							}

							calculatetag_SaleValue();

						}

						// Call the callback function after successful completion

						if (callback) {

							callback();

						}

					}

					if (data.length <= 0) {

						get_tag_status_details({ 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val() });

					}

				}

			});

		}

		$(".overlay").css('display', 'none');

	}

	function get_tag_data(callback) {

		$(".overlay").css('display', 'block');

		var tagData = $.trim($('#est_tag_scan').val());

		var old_tag_no = $.trim($('#est_tag_barcode_scan').val());

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

				searchTxt = tagData;

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

		}

		else if(old_tag_no!='') {

			var tagData = $.trim($('#est_tag_barcode_scan').val().replaceAll(' ', ''));

			var istagId = (tagData.search("/") > 0 ? true : false);

			//var isTagCode = (tagData.search("-") > 0 ? true : false);

			var isTagCode = true;

			if (istagId) {

				var tId = tagData.split("/");

				searchTxt = (tId.length >= 2 ? tId[0] : "");

				type = "tag_id";

			}

			else if (isTagCode) {

				searchTxt = tagData;

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

		else if($('#est_order').val()!='')

		{

		    tag_search = true;

		}

		if (tag_search) {

			my_Date = new Date();

			$.ajax({

				type: 'POST',

				url: base_url + 'index.php/admin_ret_estimation/getTaggingScanBySearch/?nocache=' + my_Date.getUTCSeconds(),

				dataType: 'json',

				data: { 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val(),'order_no':$('#est_order').val(),'fin_year':$('#fin_year').val()},

				success: function (result) {

				    var data = result.responseData;

					var tag_data = result.tag_reserve;

					if(tag_data.length > 0){

						if(tag_data[0].reserved != null && tag_data[0].status == 0){

						$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Reserved.' });

						}

						else if(tag_data[0].reserved != null && tag_data[0].status == 1){

							$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Sold out.' });

						}

						if (callback) {

							callback();

						}

						return false;

					}
					if (data[0].Is_green_tag == "1") {
						$.toaster({ priority: 'success', title: 'Green Tag!', message: data[0].label + ' is Green Tag...' });
					}



					if (data != null && data.length > 0) {

						if(data[0].id_customer  != null ){

							$("#est_cus_name").val(data[0].name);

							$("#cus_id").val(data[0].id_customer);

							customer_detail_modal(data[0].id_customer)

						}

						$.each(data,function(key,val){

							let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

							let emp_select = $("#emp_select").val();

							rowExist = false;

							if ($('#estimation_tag_details > tbody').length > 0) {

								$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

									//rowExist=false;

									if (val.tag_id == $(this).find('.est_tag_id').val()) {

										$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists..' });

										rowExist = true;

									}

								});

							}

							if (!rowExist) {

								/* var row = "";

								var stone_details = [];

								var other_metal_details = [];

								var tag_other_itm_amount = 0;

								$.each(val.stone_details, function (skey, sitem) {

									stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });

								});

								$.each(val.other_metal_details, function (skey, sitem) {

									tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

									other_metal_details.push({

										"tag_other_itm_id": sitem.tag_other_itm_id,

										"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

										"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

										"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

										"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

										"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

										"tag_other_itm_uom": sitem.tag_other_itm_uom,

										"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

										"tag_other_itm_mc": sitem.tag_other_itm_mc,

										"tag_other_itm_rate": sitem.tag_other_itm_rate,

										"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

										"tag_other_itm_amount": sitem.tag_other_itm_amount,

									});

								});

								let pur_mc = typeof val.po_details != 'undefined' && val.po_details != null && val.po_details.length != 0 ? (!(val.po_details[0].mc_value >= 0) || val.po_details[0].mc_value == null ? 0 : val.po_details[0].mc_value) : 0;

								let pur_va = typeof val.po_details != 'undefined' && val.po_details != null && val.po_details.length != 0 ? (!(val.po_details[0].item_wastage >= 0) || val.po_details[0].item_wastage == null ? 0 : val.po_details[0].item_wastage) : 0;

								let rate_field = val.rate_field;

								let rate_per_grm = 0;

								if(val.ord_rate_type==1 && val.order_rate_per_grm!=''){

									rate_per_grm = val.order_rate_per_grm;

								}

								else if (rate_field != '' && val.stone_type==0) // For Ornaments Product

								{

									rate_per_grm = (isNaN($('.'+rate_field).html()) || $('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();

								}

								else

								{

									$.each(loose_product_rate,function(key,items)

									{

										console.log('looseproduct',items);

										var product_centwt = 0;

										if(val.stone_type==2) // for diamond products

										{

											var pcs  = (isNaN(val.piece) || val.piece == '')  ? 0 : parseInt(val.piece);

											var grs_wt  = (isNaN(val.gross_wt) || val.gross_wt == '')  ? 0 : parseFloat(val.gross_wt);

											product_centwt = parseFloat(((grs_wt)/(pcs))*100).toFixed(3);

										}

										if($('#branch_select').val() == items.id_branch && val.lot_product == items.id_product && val.design_id == items.id_design && val.id_sub_design == items.id_sub_design && val.quality_id == items.quality_id && val.uom_id==items.uom_id)

										{

											if(product_centwt > 0)

											{

												if(product_centwt >= parseFloat(items.from_cent) && product_centwt <= parseFloat(items.to_cent))

												{

													rate_per_grm = items.max_rate;

												}

											}

											else

											{

												rate_per_grm = items.max_rate;

											}

										}

									});

								}

								var wast_wgt = parseFloat(parseFloat(val.net_wt) * parseFloat(val.retail_max_wastage_percent / 100)).toFixed(3);

								var select_emp = "<option value=''>-Select Employee-</option>";

								$.each(emp_details, function (pkey, emp) {

									let emp_selected = "";

									if (emp.id_employee == emp_select) {

										emp_selected = "selected = 'selected'";

									}

									select_emp += "<option " + emp_selected + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

								});

								var select_purity = "";

								$.each(purities, function (pkey, sitem) {

									var selected = "";

									if (val.purity == sitem.id_purity) {

										selected = "selected";

									}

									select_purity += "<option value='" + sitem.id_purity + "'" + selected + ">" + sitem.purity + "</option>";

								});

								var mc_min_limit = parseFloat(val.mc_va_limit.mc_min);

								var va_min_limit = parseFloat(val.mc_va_limit.wastag_min);

								var va_wt_min_limit = parseFloat(parseFloat(val.net_wt) * parseFloat(val.mc_va_limit.wastag_min / 100)).toFixed(3);

								var tag_image = base_url + 'assets/img/no_image.png';

								if (val.tag_images.length > 0) {

									$.each(val.tag_images, function (key, val) {

										if (val.is_default == 1) {

											tag_image = base_url + 'assets/img/tag/' + val.image;

										}

									});

								}

								var child_tag_details = [];

								var rowId = new Date().getTime();

								row += '<tr id="' + rowId + '">'

									+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + val.label + ' placeholder="Enter tag code" required autocomplete="off"/><input class="id_mapping_details" type="hidden" name="est_tag[id_mapping_details][]" value="' + val.id_mapping_details + '" /><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + val.tag_id + ' /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + val.id_orderdetails + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + val.order_no + '"/><input class="rate_field" type="hidden"  value="' + val.rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + val.market_rate_field + '"/></td>'

									+ '<td><a href="#" onClick="create_new_empty_est_tag_merge_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" id="it_has_childtag" value= "" ><input type="hidden" name="est_tag[child_tag_details][]" id="child_tag_details" value="' + JSON.stringify(child_tag_details, null, 5) + '"/></td>'

									+ '<td><img src="' + tag_image + '" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="' + val.tag_id + '" id="edit"><i class="fa fa-eye" ></i></a></td>'

									+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_tag[item_emp_id][]" >' + select_emp + '</select></td>'

									+ '<td><input type="checkbox" class="partial" ' + (val.calculation_based_on == 3 ? 'disabled' : '') + ' ><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"></td>'

									+ '<td><div class="prodct_name">' + val.product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + val.lot_product + ' /><input type="hidden" class="metal_type" value=' + val.metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + val.scheme_closure_benefit + '></td>'

									+ '<td><div class="design_name">' + val.design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + val.design_id + ' /></td>'

									+ '<td><div class="sub_design_name">' + val.sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value=' + val.id_sub_design + ' /><input type="hidden" class="quality_id" name="est_tag[quality_id][]" value='+val.quality_id+'><input type="hidden" class="stone_type" value='+val.stone_type+'><input type="hidden" class="uom_id" value='+val.uom_id+'><input type="hidden" class="stone_calculation_based_on" value='+val.stone_calculation_based_on+'></td>'

									+ '<td><div class="section_name">' + val.section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + val.id_section + ' /></td>'

									+ '<td><div class="huid">' + val.huid + '</div></td>'

									+ '<td><div class="order_no"></td>'

									// + '<td><div class="purity">' + val.purname + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + val.purity + ' /></td>'

									// + '<td><select class="form-control tag_purity" id="select_purity" name="est_tag[purity][]" style="width:100px;"' + (profile.est_purity_edit == 1 ? '' : 'disabled') + '></select><input type="hidden" class="id_purity" value='+val.purity+'></td>'

									+ '<td><select class="form-control tag_purity" name="est_tag[purity][]" style="width:100px;"' + (profile.est_purity_edit == 1 ? '' : 'disabled') + '>' + select_purity + '</select></td>'

									+ '<td><div class="sizes">' + val.size_name + '</div><input type="hidden" class="size" name="est_tag[size][]"  value="' + val.size + '" /></td>'

									//	+'<td><div class="pieces">'+val.piece+'</div><input type="hidden" class="piece" name="est_tag[piece][]" value='+val.piece+' /></td>'

									+ '<td><input type="number" class="form-control piece" name="est_tag[piece][]"  style="width:100px;" value=' + val.piece + ' readonly /><input type="hidden" class="act_piece" name="est_tag[act_piece][]" value=' + val.piece + ' readonly /></td>'

									+ '<td><input type="text" class="form-control gwt" name="est_tag[gwt][]" step="any" value=' + val.gross_wt + ' readonly style="width:100px;" /><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + val.gross_wt + ' /><input type="hidden" class="act_gwt" value=' + val.gross_wt + ' /></td>'

									//+'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" value='+val.less_wt+' readonly/></td>'

									+ '<td><div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + val.less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

									+ '<td><div class="nwt">' + val.net_wt + '</div><input type="hidden" class="form-control tot_nwt" name="est_tag[nwt][]" value=' + val.net_wt + ' /><input type="hidden" class="tot_tag_nwt"  value=' + val.net_wt + ' style="width:100px;" /></td>'

									+ '<td><input type="text" class="form-control market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + (rate_per_grm > 0 ? rate_per_grm : val.sales_value) + '" style="width:100px;" /></td>'

									+ '<td><div class="wastage" style="display:none">' + val.retail_max_wastage_percent + '</div><input type="number" name="est_tag[wastage][]" class="form-control wastage_max_per" value=' + val.retail_max_wastage_percent + ' style="width:100px;" /><input type="hidden" class="va_min_limit" value="' + va_min_limit + '"></td>'

									+ '<td><div class="act_wast_wt" style="display:none">' + wast_wgt + '</div><input type="number" name="est_tag[est_wastage_wt][]" class="form-control est_wastage_wt" value="" style="width:100px;" /><input type="hidden" class="va_wt_min_limit" value="' + va_wt_min_limit + '"></td>'

									//+'<td><div class="mc">'+val.tag_mc_value+'</div></td>'

									+ '<td><select class="form-control est_mc_type" style="width:80px;"><option value="2" ' + (val.tag_mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (val.tag_mc_type == 1 || val.tag_mc_type == null ? 'selected' : '') + '>Piece</option></select></td>'

									+ '<td><div class="mc_val" style="display:none">' + val.tag_mc_value + '</div><input class="form-control act_mc_value"  name="est_tag[mc][]" type="number" value=' + val.tag_mc_value + ' style="width:100px;" /><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" />Tot Mc:<span class="tot_mc" ></span><input type="hidden" class="mc_min_limit" value="' + mc_min_limit + '"></td>'

									//	+'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value='+JSON.stringify(stone_details)+'><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

									+ '<td><div class="cost">' + (val.sales_value) + '</div> <input type="hidden" name="est_tag[total_cgst][]" class="sale_cgst"/><input type="hidden" class="sale_sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="sale_igst" name="est_tag[total_igst][]"/> <input type="hidden" class="sale_taxable_amount" name="est_tag[sale_taxable_amount][]"/> <input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (val.sales_value) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + val.item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + val.calculation_based_on + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + val.tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + val.tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + val.tax_percentage + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + val.stone_price + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + val.certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + val.tag_mc_type + ' /><input class="mc_value" type="hidden"  value=' + val.tag_mc_value + ' /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="act_sales_value" value=' + (rate_per_grm >0 && val.stone_type!=0 ? rate_per_grm  : val.sales_value) + '><input type="hidden" class="charge_value" value=' + val.charge_value + ' name="est_tag[charge_value][]"><input type="hidden" class="sales_mode" value=' + val.sales_mode + ' ><input type="hidden" class="purchase_cost" value=' + val.tag_purchase_cost + ' name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_tag[pur_va][]" ><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value=' + JSON.stringify(other_metal_details) + '><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" value="' + tag_other_itm_amount + '"><input type="hidden" class="ord_rate_per_grm" value='+val.order_rate_per_grm+'><input type="hidden" class="ord_rate_type" value='+val.ord_rate_type+'></td>'

									+ '<td><a href="#" onClick="remove_tag_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

									+ '</tr>';

								$('#estimation_tag_details tbody').append(row);

								$('#estimation_tag_details > tbody').find('.item_emp_id').select2();

								$('#estimation_tag_details > tbody').find('.tag_purity').select2();

								getestPurityDetails(val.id_metal,val.cat_id);

								if (val.id_tag_mapping != '') {

									var collection_details = val.collection_details;

									if (collection_details.length > 0) {

										$.each(collection_details, function (key, items) {

											$('#estimation_tag_details > tbody').each(function (idx, row) {

												if (items.tag_id != $(this).find('.est_tag_id').val()) {

													$('#id_tag_mapping').val(val.id_tag_mapping);

													$('#collection_confirm').modal('show');

												}

											});

										});

									}

								} */

							   val.balance_weight =  val.gross_wt;



								append_tag_details(val); // Append the tag details in table

                             

								tag_split_details.push(val);  //Tag Split



							}

							// Call the callback function after successful completion

							if (callback) {

								callback();

							}

						});

						var paid_advance = 0;

        				var paid_weight = 0;

        				var wt_amt = 0;

        				order_adv_details = result.adv_details;

        				if (order_adv_details.length > 0) {

        					$.each(order_adv_details, function (key, item) {

        						paid_advance += parseFloat(item.paid_advance);

        						paid_weight += parseFloat(item.paid_weight);

        						wt_amt += parseFloat(item.paid_weight * item.rate_per_gram);

        					});

							$('.summary_adv_paid_amt').html(parseFloat(paid_advance) + parseFloat(wt_amt));

							$('.summary_adv_paid_weight').html(parseFloat(paid_weight).toFixed(3));



        					calculateOrderTag();

        				}else{

        				    calculatetag_SaleValue();

        				}



					}

					if (data.length <= 0) {

						get_tag_status_details({ 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val() });

					}

				}

			});

		}

		$(".overlay").css('display', 'none');

	}

	$(document).on('change', '.tag_purity', function (e) {

		var row = $(this).closest('tr');

		get_search_tag_metal_rates(row);

	});

	function get_search_tag_metal_rates(curRow) {

		var id_purity = curRow.find('.tag_purity').val();

		var id_metal = curRow.find('.metal_type').val();

		if (id_purity == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Purity..' });

		}

		else if (id_metal == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Metal..' });

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

					curRow.find('.rate_field').val(data.rate_field);

					curRow.find('.market_rate_field').val(data.market_rate_field);

					let rate_field = data.rate_field;

					let rate_per_grm = 0;

					if (rate_field != '') {

						rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

					}

					curRow.find('.market_rate_value').val(rate_per_grm);

					calculatetag_SaleValue();

				}

			});

		}

	}

	$('#set_collection_tags').on('click', function () {

		my_Date = new Date();

		$.ajax({

			type: 'POST',

			url: base_url + 'index.php/admin_ret_estimation/getTaggingSearchByCollection/?nocache=' + my_Date.getUTCSeconds(),

			dataType: 'json',

			data: { 'id_branch': $("#id_branch").val(), 'id_tag_mapping': $('#id_tag_mapping').val() },

			success: function (data) {

				if (data != null && data.length > 0) {

					let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

                    let emp_select = $("#emp_select").val();

					$.each(data, function (key, items) {

						var rowExist = false;

						$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

							if (items.tag_id == $(this).find('.est_tag_id').val()) {

								$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists ..' });

								rowExist = true;

							}

							/*if(items.tax_group_id!=$(this).find('.tax_group_id').val())

							  {

								 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Add The Same Tax Items ..'});

								 rowExist=true;

							  }*/

						});

						if (!rowExist) {

							var row = "";

							var stone_details = [];

							var other_metal_details = [];

							var tag_other_itm_amount = 0;

							$.each(items.stone_details, function (skey, sitem) {

								stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });

							});

							$.each(data[0].other_metal_details, function (skey, sitem) {

								tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

								other_metal_details.push({

									"tag_other_itm_id": sitem.tag_other_itm_id,

									"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

									"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

									"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

									"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

									"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

									"tag_other_itm_uom": sitem.tag_other_itm_uom,

									"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

									"tag_other_itm_mc": sitem.tag_other_itm_mc,

									"tag_other_itm_rate": sitem.tag_other_itm_rate,

									"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

									"tag_other_itm_amount": sitem.tag_other_itm_amount,

									"purname": sitem.purname,

				                     "metal_name": sitem.metal_name,



								});

							});

							var wast_wgt = parseFloat(parseFloat(data[0].net_wt) * parseFloat(data[0].retail_max_wastage_percent / 100)).toFixed(3);

							var select_emp = "<option value=''>-Select Employee-</option>";

							$.each(emp_details, function (pkey, emp) {

								let emp_selected = "";

								if (emp.id_employee == emp_select) {

									emp_selected = "selected = 'selected'";

								}

								select_emp += "<option " + emp_selected + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

							});

							var va_wt_min_limit = parseFloat(parseFloat(data[0].net_wt) * parseFloat(data[0].mc_va_limit.wastag_min / 100)).toFixed(3);

							var mc_min_limit = parseFloat(data[0].mc_va_limit.mc_min);

							var tag_image = base_url + 'assets/img/no_image.png';

							if (data[0].tag_images.length > 0) {

								$.each(data[0].tag_images, function (key, val) {

									if (val.is_default == 1) {

										tag_image = base_url + 'assets/img/tag/' + val.image;

									}

								});

							}

							var child_tag_details=[];

							var rowId = new Date().getTime();

							row += '<tr id="' + rowId + '">'

								+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + items.label + ' placeholder="Enter tag code" required autocomplete="off"/><input class="id_mapping_details" type="hidden" name="est_tag[id_mapping_details][]" value=' + items.id_mapping_details + ' /><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + items.tag_id + ' placeholder="Enter tag code" required /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + items.id_orderdetails + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + items.order_no + '"/><input class="rate_field" type="hidden"  value="' + items.rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + items.market_rate_field + '"/></td>'

								+ '<td><a href="#" onClick="create_new_empty_est_tag_merge_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" id="it_has_childtag" value= "" ><input type="hidden" name="est_tag[child_tag_details][]" id="child_tag_details" value="' + JSON.stringify(child_tag_details, null, 5) + '"/></td>'

								+ '<td><img src="' + tag_image + '" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="' + data[0].tag_id + '" id="edit"><i class="fa fa-eye" ></i></a></td>'

+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_tag[item_emp_id][]" >' + select_emp + '</select></td>'

								+ '<td><input type="checkbox" class="partial" "' + (items.calculation_based_on == 3 ? 'disabled' : '') + '" ><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"></td>'

								+ '<td><div class="prodct_name">' + items.product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + items.lot_product + ' /><input type="hidden" class="metal_type" value=' + items.metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + items.scheme_closure_benefit + '></td>'

								+ '<td><div class="design_name">' + items.design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + items.design_id + ' /></td>'

								+ '<td><div class="sub_design_name">' + items.sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value=' + items.id_sub_design + ' /></td>'

								+ '<td><div class="section_name">' + data[0].section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + data[0].id_section + ' /></td>'

+ '<td><div class="huid">' + data[0].huid + '</div></td>'

								+ '<td><div class="order_no"></td>'

								+ '<td><div class="purity">' + items.purname + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + items.purity + ' /></td>'

								+ '<td><div class="sizes">' + (items.size_name ? items.size_name : '-' ) + '</div><input type="hidden" class="size" name="est_tag[size][]"  value="' + items.size + '" /></td>'

								+ '<td><div class="pieces">' + items.piece + '</div><input type="hidden" class="piece" name="est_tag[piece][]" value=' + items.piece + ' /></td>'

								+ '<td><input type="text" class="gwt form-control" name="est_tag[gwt][]" step="any" value=' + items.gross_wt + ' readonly/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + items.gross_wt + ' /><input type="hidden" class="act_gwt" value=' + items.gross_wt + ' /></td>'

								//+'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" value='+items.less_wt+' readonly/></td>'

								+ '<td><div class="form-group"><div class="input-group "><input class="lwt  form-control"  value=' + items.less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'

								+ '<td><div class="nwt">' + items.net_wt + '</div><input type="hidden" class="tot_nwt" name="est_tag[nwt][]" value=' + items.net_wt + ' /><input type="hidden" class="tot_tag_nwt"  value=' + items.net_wt + ' /></td>'

								+ '<td><input type="text" class="form-control market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + rate_per_grm + '" style="width:100px;" /></td>'

								+ '<td><div class="wastage" style="display:none">' + items.retail_max_wastage_percent + '</div><input type="number" name="est_tag[wastage][]" class="wastage_max_per  form-control" value=' + items.retail_max_wastage_percent + ' /></td>'

								+ '<td><div class="act_wast_wt" style="display:none">' + wast_wgt + '</div><input type="number" name="est_tag[est_wastage_wt][]" class="form-control est_wastage_wt" value="" style="width:100px;" /><input type="hidden" class="va_wt_min_limit" value="' + va_wt_min_limit + '"></td>'

								//+'<td><div class="mc">'+data[0].tag_mc_value+'</div></td>'

								+ '<td><select class="form-control est_mc_type" style="width:80px;"><option value="2" ' + (data[0].tag_mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (data[0].tag_mc_type == 1 || data[0].tag_mc_type == null ? 'selected' : '') + '>Piece</option></select></td>'

								+ '<td><div class="mc_val" style="display:none">' + data[0].tag_mc_value + '</div><input class="form-control act_mc_value" name="est_tag[mc][]" type="number" value=' + data[0].tag_mc_value + ' style="width:100px;" /><input class="discount_amount" name="est_tag[discount_amount][]" type="hidden" value="" />Tot Mc:<span class="tot_mc" ></span><input type="hidden" class="mc_min_limit" value="' + mc_min_limit + '"></td>'

								//+ '<td><div class="mc_val" style="display:none">' + data[0].tag_mc_value + '</div><input class="act_mc_value"  name="est_tag[mc][]" type="number" value=' + items.tag_mc_value + ' /><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" /></td>'

								//+ '<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

								+ '<td><div class="cost">' + (items.sales_value) + '</div><input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (items.sales_value) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + items.item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + items.calculation_based_on + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + items.tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + items.tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + items.tax_percentage + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + items.stone_price + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + items.certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + items.tag_mc_type + ' /><input class="mc_value" type="hidden"  value=' + items.tag_mc_value + ' /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="act_sales_value" value=' + (items.sales_value) + '><input type="hidden" class="charge_value" value=' + items.charge_value + ' name="est_tag[charge_value][]"><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value=' + JSON.stringify(other_metal_details) + '><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" value="' + tag_other_itm_amount + '"></td>'

								+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

								+ '</tr>';

							$('#estimation_tag_details tbody').append(row);

							calculatetag_SaleValue();

						}

					});

				}

				$('#collection_confirm').modal('toggle');

			}

		});

	});

	function get_tag_barcode_data() {

		console.log("get_tag_data");

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

		}

		if (type == "tag_code") {

			tag_search = false;

		}

		if (tag_search) {

			my_Date = new Date();

			$.ajax({

				type: 'POST',

				url: base_url + 'index.php/admin_ret_estimation/getTaggingBySearch/?nocache=' + my_Date.getUTCSeconds(),

				dataType: 'json',

				data: { 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val() },

				success: function (data) {

					if (data != null && data.length > 0) {

						let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

						if ($('#estimation_tag_details >tbody > tr').length > 0) {

							var rowExist = false;

							$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

								if (items.tag_id == $(this).find('.est_tag_id').val()) {

									$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists ..' });

									rowExist = true;

								}

								/*if(items.tax_group_id!=$(this).find('.tax_group_id').val())

								  {

									 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Add The Same Tax Items ..'});

									 rowExist=true;

								  }*/

							});

							if (!rowExist) {

								let pur_mc = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].mc_value >= 0) || data[0].po_details[0].mc_value == null ? 0 : data[0].po_details[0].mc_value) : 0;

								let pur_va = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].item_wastage >= 0) || data[0].po_details[0].item_wastage == null ? 0 : data[0].po_details[0].item_wastage) : 0;

								var row = "";

								row += '<tr>'

									+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + data[0].label + ' placeholder="Enter tag code" required autocomplete="off"/><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + data[0].tag_id + ' placeholder="Enter tag code" required /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + data[0].id_orderdetails + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + data[0].order_no + '"/><input class="rate_field" type="hidden"  value="' + data[0].rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + data[0].market_rate_field + '"/></td>'

									+ '<td><img src="" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="" id="edit"><i class="fa fa-eye" ></i></a></td>'

									+ '<td><input type="checkbox" class="partial"><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"></td>'

									+ '<td><div class="prodct_name">' + data[0].product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + data[0].lot_product + ' /><input type="hidden" class="metal_type" value=' + data[0].metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + data[0].scheme_closure_benefit + '></td>'

									+ '<td><div class="design_name">' + data[0].design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + data[0].design_id + ' /></td>'

									+ '<td><div class="order_no"></td>'

									+ '<td><div class="purity">' + data[0].purity + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + data[0].purity + ' /></td>'

									+ '<td><div class="sizes">' + data[0].size_name + '</div><input type="hidden" class="size" name="est_tag[size][]"  value="' + data[0].size + '" /></td>'

									+ '<td><div class="pieces">' + data[0].piece + '</div><input type="hidden" class="piece" name="est_tag[piece][]" value=' + data[0].piece + ' /></td>'

									+ '<td><input type="text" class="gwt" name="est_tag[gwt][]" step="any" value=' + data[0].gross_wt + ' disabled/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + data[0].gross_wt + ' /><input type="hidden" class="act_gwt" value=' + data[0].gross_wt + ' /></td>'

									//+'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" value='+data[0].less_wt+' disabled/></td>'

									+ '<td><div class="form-group"><div class="input-group "><input class="lwt"  value=' + data[0].less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly disabled style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" style="height: 26px;padding-left: 12px;" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'

									+ '<td><div class="nwt">' + data[0].net_wt + '</div><input type="hidden" class="tot_nwt" name="est_tag[nwt][]" value=' + data[0].net_wt + ' /><input type="hidden" class="tot_tag_nwt"  value=' + data[0].net_wt + ' /></td>'

									+ '<td><input type="text" class="market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + rate_per_grm + '" /></td>'

									+ '<td><div class="wastage" style="display:none">' + data[0].retail_max_wastage_percent + '</div><input type="number" name="est_tag[wastage][]" class="wastage_max_per" value=' + data[0].retail_max_wastage_percent + ' /></td>'

									+ '<td><div class="mc">' + data[0].tag_mc_value + '</div></td>'

									+ '<td><div class="cost">' + (data[0].sales_value) + '</div><input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (data[0].sales_value) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + data[0].item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + data[0].calculation_based_on + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + data[0].tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + data[0].tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + data[0].tax_percentage + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + data[0].stone_price + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + data[0].certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + data[0].tag_mc_type + ' /><input class="mc_value" type="hidden" name="est_tag[mc][]" value=' + data[0].tag_mc_value + ' /><input class="act_mc_value" type="hidden" value=' + data[0].tag_mc_value + ' /><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="sales_mode" value=' + data[0].sales_mode + ' ><input type="hidden" class="purchase_cost" name="est_tag[purchase_cost][]" value=' + data[0].tag_purchase_cost + ' ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_tag[pur_va][]" ></td>'

									+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

									+ '</tr>';

								$('#estimation_tag_details tbody').append(row);

								calculatetag_SaleValue();

							}

						} else {

							let pur_mc = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].mc_value >= 0) || data[0].po_details[0].mc_value == null ? 0 : data[0].po_details[0].mc_value) : 0;

							let pur_va = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].item_wastage >= 0) || data[0].po_details[0].item_wastage == null ? 0 : data[0].po_details[0].item_wastage) : 0;

							var row = "";

							row += '<tr>'

								+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + data[0].label + ' placeholder="Enter tag code" required autocomplete="off"/><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + data[0].tag_id + ' placeholder="Enter tag code" required /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + data[0].id_orderdetails + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + data[0].order_no + '"/><input class="rate_field" type="hidden"  value="' + data[0].rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + data[0].market_rate_field + '"/></td>'

								+ '<td><input type="checkbox" class="partial"><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"></td>'

								+ '<td><div class="prodct_name">' + data[0].product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + data[0].lot_product + ' /><input type="hidden" class="metal_type" value=' + data[0].metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + data[0].scheme_closure_benefit + '></td>'

								+ '<td><div class="design_name">' + data[0].design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + data[0].design_id + ' /></td>'

								+ '<td><div class="order_no"></td>'

								+ '<td><div class="purity">' + data[0].purity + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + data[0].purity + ' /></td>'

								+ '<td><div class="sizes">' + data[0].size_name + '</div><input type="hidden" class="size" name="est_tag[size][]" value="' + data[0].size + '" /></td>'

								+ '<td><div class="pieces">' + data[0].piece + '</div><input type="hidden" class="piece" name="est_tag[piece][]" value=' + data[0].piece + ' /></td>'

								+ '<td><input type="text" class="gwt" name="est_tag[gwt][]" step="any" value=' + data[0].gross_wt + ' disabled/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + data[0].gross_wt + ' /><input type="hidden" class="act_gwt" value=' + data[0].gross_wt + ' /></td>'

								//+'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" value='+data[0].less_wt+' disabled/></td>'

								+ '<td><div class="form-group"><div class="input-group "><input class="lwt"  value=' + data[0].less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly disabled style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" style="height: 26px;padding-left: 12px;" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'

								+ '<td><div class="nwt">' + data[0].net_wt + '</div><input type="hidden" class="tot_nwt" name="est_tag[nwt][]" value=' + data[0].net_wt + ' /><input type="hidden" class="tot_tag_nwt"  value=' + data[0].net_wt + ' /></td>'

								+ '<td><input type="text" class="market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + rate_per_grm + '" /></td>'

								+ '<td><div class="wastage" style="display:none">' + data[0].retail_max_wastage_percent + '</div><input type="number" name="est_tag[wastage][]" class="wastage_max_per" min=' + data[0].retail_max_wastage_percent + ' value=' + data[0].retail_max_wastage_percent + ' /></td>'

								+ '<td><div class="mc">' + data[0].tag_mc_value + '</div></td>'

								+ '<td><div class="cost">' + (data[0].sales_value) + '</div><input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (data[0].sales_value) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + data[0].item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + data[0].calculation_based_on + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + data[0].tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + data[0].tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + data[0].tax_percentage + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + data[0].stone_price + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + data[0].certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + data[0].tag_mc_type + ' /><input class="mc_value" type="hidden" name="est_tag[mc][]" value=' + data[0].tag_mc_value + ' /><input class="act_mc_value" type="hidden" value=' + data[0].tag_mc_value + ' /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="sales_mode" value=' + data[0].sales_mode + ' ><input type="hidden" class="purchase_cost" name="est_tag[purchase_cost][]" value=' + data[0].tag_purchase_cost + ' ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_tag[pur_va][]" ></td>'

								+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

								+ '</tr>';

							$('#estimation_tag_details tbody').append(row);

							calculatetag_SaleValue();

						}

					}

					$('#est_tag_scan').val('');

				}

			});

		}

		$(".overlay").css('display', 'none');

	}

	$('#select_catalog_details').change(function () {

		if (this.checked) {

			if ($('#estimation_catalog_details tbody tr').length == 0) {

				var id_branch = $('#id_branch').val();

				if (id_branch != '') {

					$(".catalog_details").show();

					create_new_empty_est_catalog_row();

				}

				else {

					alert('Please Select Branch');

					$('#select_catalog_details').prop('checked', false);

				}

			}

		} else {

			$(".catalog_details").hide();

			$('#estimation_catalog_details tbody').empty();

		}

	});

	$('#create_catalog_details').on('click', function () {

		if (validateCatalogDetailRow()) {

			create_new_empty_est_catalog_row();

		} else {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Required Fields..' });

		}

	});

	$('#create_custom_details').on('click', function () {

		if (validateCustomDetailRow()) {

			create_new_empty_est_custom_row();

		}

	});

	$('#select_custom_details').change(function () {

		if (this.checked) {

			if ($('#estimation_custom_details tbody tr').length == 0) {

				var id_branch = $('#id_branch').val();

				if (id_branch != '') {

					$(".custom_details").show();

					create_new_empty_est_custom_row();

				} else {

					alert('Please Select Branch');

					$('#select_custom_details').prop('checked', false);

				}

			}

		} else {

			$('#estimation_custom_details tbody').empty();

			$(".custom_details").hide();

		}

	});

	$('#select_oldmatel_details').change(function () {

		if (this.checked) {

			if ($('#estimation_old_matel_details tbody tr').length == 0) {

				var id_employee = $('#id_employee').val();

				var id_branch = $('#id_branch').val();

				if (id_employee != '' && id_branch != '') {

					$(".old_matel_details").show();

					create_new_empty_est_oldmatel_row();

				}

				else {

					alert('Please Select Branch and Employee');

					$('#select_oldmatel_details').prop('checked', false);

				}

			}

		}

		else {

			$(".old_matel_details").hide();

			$('#estimation_old_matel_details tbody').empty();

		}

	});

	$('#create_old_matel_details').on('click', function () {

		if (validateOldMatelDetailRow()) {

			create_new_empty_est_oldmatel_row();

		} else {

			alert("Please fill required fields");

		}

	});

	$('#select_has_stn_details').change(function () {

		if (this.checked) {

			$(".stone_details").show();

			if ($('#estimation_stone_details tbody tr').length == 0) {

				create_new_empty_est_stone_row();

			}

		} else {

			$(".stone_details").hide();

		}

	});

	$('#create_stone_details').on('click', function () {

		if (validateStoneDetailRow()) {

			create_new_empty_est_stone_row();

		} else {

			alert("Please fill required fields");

		}

	});

	$('#select_materials_details').change(function () {

		if (this.checked) {

			$(".material_details").show();

			if ($('#estimation_material_details tbody tr').length == 0) {

				create_new_empty_est_material_row();

			}

		} else {

			$(".material_details").hide();

		}

	});

	$('#create_material_details').on('click', function () {

		if (validateMaterialDetailRow()) {

			create_new_empty_est_material_row();

		} else {

			alert("Please fill required fields");

		}

	});

	$('#select_voucher_details').change(function () {

		if (this.checked) {

			$(".gift_voucher_details").show();

			if ($('#estimation_gift_voucher_details tbody tr').length == 0) {

				create_new_empty_est_voucher_row();

			}

		} else {

			$(".gift_voucher_details").hide();

		}

	});

	$('#create_gift_voucher_details').on('click', function () {

		if (validateVoucherDetailRow()) {

			create_new_empty_est_voucher_row();

		} else {

			alert("Please fill required fields");

		}

	});

	$('#select_chit_details').change(function () {

		if (this.checked) {

			$(".chit_details").show();

			if ($('#estimation_chit_details tbody tr').length == 0) {

				create_new_empty_est_chit_row();

			}

		} else {

			$(".chit_details").hide();

		}

	});

	$('#create_chit_details').on('click', function () {

		if ($('#cus_id').val() == '') {

			$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please Select The Customer..' });

		}

		else if ($('#estimation_tag_details > tbody >tr').length == 0 && $('#estimation_custom_details > tbody >tr').length == 0 && $('#estimation_catalog_details > tbody >tr').length == 0) {

			$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Item Details Not Found..' });

		}

		else {

			if (validateChitDetailRow()) {

				create_new_empty_est_chit_row();

			} else {

				$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please fill required fields..' });

			}

		}

	});

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

	/* $('#add_newcutomer').click(function (event) {

		var esti_for = $("input[name='estimation[esti_for]']:checked").val();

		if ($('#cus_first_name').val() == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Firstname..' });

			return false;

		}

		else if ($('#cus_mobile').val() == '' || $('#cus_mobile').val() == null) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Mobile Number..' });

			return false;

		}

		else if ($('#country').val() == '' || $('#country').val() == null) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Country..' });

			return false;

		}

		else if ($('#state').val() == '' || $('#state').val() == null) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the State..' });

			return false;

		} else if ($('#city').val() == '' || $('#city').val() == null) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the City..' });

			return false;

		}

		else if ($('#address1').val() == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Address..' });

			return false;

		}

		else if ($('#pin_code_add').val() == '' && $('#country').val() == 101) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });

			return false;

		}

		else if ($('#pin_code_add').val() != '' && ($('#pin_code_add').val().length != 6)) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid Pincode..' });

			return false;

		}

		else if ($('#id_village').val() == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Area..' });

			return false;

		}

		else if ($('#country').val() != 101) {

			if ($('#pp').val() == '') {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Passport No..' });

				return false;

			}

		}

		else if (esti_for == 3) {

			if ($('#gst_no').val() == '') {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the GST No..' });

				return false;

			} else {

				var reggst = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){3}$');

				if (!reggst.test($('#gst_no').val())) {

					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid GST No..' });

					return false;

				}

			}

		}

		add_customer($('#cus_first_name').val(), $('#cus_mobile').val(), $('#id_village').val(), $('#cus_type:checked').val(), $('#gst_no').val(), $("#cus_image")[0].files[0]);

		$('#cus_first_name').val('');

		$('#cus_mobile').val('');

	}); */

	$('#gst_no,#ed_gst_no').on('change', function () {

		var gst = $(this).val();

		var gstinformat = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){2}([0-9]|[a-zA-Z]){1}$');

		if (!gstinformat.test(gst)) {

			$('#gst_no,#ed_gst_no').val("");

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid GST NO' });

			$('#gst_no,#ed_gst_no').focus();

		}

		else {

			$('#gst_no,#ed_gst_no').val(gst);

		}

	});

	$('#ed_cus_pin_code_add,#pin_code_add').on('change', function () {

		if (this.value.length != 6) {

			$('#ed_cus_pin_code_add,#pin_code_add').val("");

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid PIN Code' });

		}

	});

	/* Customer search. - Start */

	$("#est_cus_name").on("keyup", function (e) {

		var customer = $("#est_cus_name").val();

		if (customer.length >= 1) {

			getSearchCustomers(customer);

		}

	});

	/* Ends - Customer search. */

	$(document).on('keyup', "#estimation_tag_details input[type='text'], #estimation_catalog_details input[type='text'], #estimation_custom_details input[type='text'], #estimation_stone_details input[type='number'], #estimation_material_details input[type='text']", function (e) {

		calculate_purchase_details();

	});

	$(document).on('change', "#estimation_tag_details input[type='text'], #estimation_catalog_details input[type='text'], #estimation_custom_details input[type='text'], #estimation_stone_details input[type='number'], #estimation_material_details input[type='text']", function (e) {

		calculate_purchase_details();

	});

	$(document).on('change', "#estimation_old_matel_details input[type='text'], #estimation_gift_voucher_details input[type='text'], #estimation_chit_details input[type='text'], .summary_discount_amt, .old_item_type", function (e) {

		calculate_sales_details();

	});

	$(document).on('change', ".purpose", function (e) {

		var row = $(this).closest('tr');

		if (this.value != '') {

			row.find('.id_purpose').val(this.value);

		}

		else {

			row.find('.id_purpose').val(2);

		}

	});

	/* Chit number Search */

	/*$(document).on('keyup','.scheme_account_id',function(){

		var searchTxt=this.value;

		var row = $(this).closest('tr');

		get_scheme_acc_number(searchTxt,row);

	});*/

	$(document).on('click', '#scheme_search', function () {

		var row = $(this).closest('tr');

		var id_scheme_acc = row.find('.scheme_account_id').val();

		get_SchemeAcc_number('', row, id_scheme_acc);

	});

	/* Chit number Search*/

	/* Tag id search. - Start */

	$(document).bind('paste', ".est_tag_name", function (e) {

		var row = $(this).closest('tr');

		var tagData = $(this).val();

		var type = "";

		var searchTxt = "";

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

		} else {

			var tagData = $.trim($(this).val().replaceAll(' ', ''));

			var istagId = (tagData.search("/") > 0 ? true : false);

			var isTagCode = (tagData.search("-") > 0 ? true : false);

			if (istagId) {

				var tId = tagData.split("/");

				searchTxt = (tId.length >= 2 ? tId[0] : "");

				type = "tag_id";

			}

			else if (isTagCode) {

				searchTxt = $(this).val().replaceAll(' ', '');

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

					getSearchTags(searchTxt, type, row);

				} else {

					tag_search = true;

					getSearchTags(searchTxt, type, row);

				}

			}

		}

	});

	$(document).on('keyup', ".est_tag_name", function (e) {

		console.log(2);

		var row = $(this).closest('tr');

		var tagData = this.value;

		var type = "";

		var searchTxt = "";

		if (tagData != "") {

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

	});

	$(document).on('keyup', ".cus_tag_name", function (e) {

		var row = $(this).closest('tr');

		var tagData = this.value;

		var type = "";

		var searchTxt = "";

		if (tagData != "") {

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

						getCusSearchTags(searchTxt, type, row, 'cus_tag_name');

					} else {

						alert("Select Branch");

						$(this).val("");

					}

				} else {

					getCusSearchTags(searchTxt, type, row, 'cus_tag_name');

				}

			}

		}

	});

	$(document).on('keyup', ".cus_old_tag_code", function (e) {

		var row = $(this).closest('tr');

		var tagData = this.value;

		var type = "";

		var searchTxt = "";

		if (tagData != "") {

			var istagId = (tagData.search("/") > 0 ? true : false);

			var isTagCode = (tagData.search("-") > 0 ? true : false);

			type = "old_tag_id";

			searchTxt = this.value;

			/*if(istagId){

				var tId   = tagData.split("/");

				searchTxt = (tId.length >= 2 ? tId[0] : "");

				type  = "tag_id";

			}

			else if(isTagCode){

				searchTxt = this.value;

				type  = "tag_code";

			}*/

			if (searchTxt != "") {

				if ($("#branch_settings").val() == 1) {

					if ($("#id_branch").val() != "") {

						getCusSearchTags(searchTxt, type, row, 'cus_old_tag_code');

					} else {

						alert("Select Branch");

						$(this).val("");

					}

				} else {

					getCusSearchTags(searchTxt, type, row, 'cus_old_tag_code');

				}

			}

		}

	});

	/* Ends - tag id search. */

	/* Product id search. - Start */

	$(document).on('keyup', ".cat_product", function (e) {

		var row = $(this).closest('tr');

		var product = row.find(".cat_product").val();

		if (product.length >= 2) {

			getSearchProducts(product, row);

		} else {

			row.find(".cat_pro_id").val('');

		}

	});

	/* Ends - product id search. */

	/* Design id search. - Start */

	$(document).on('keyup', ".cat_design", function (e) {

		//var row = $(this).parent().parent();

		var row = $(this).closest('tr');

		var design = row.find(".cat_design").val();

		if (design.length >= 2) {

			getSearchDesign(design, row);

		} else {

			row.find(".cat_des_id").val('');

		}

	});

	$(document).on('keyup', ".cus_design", function (e) {

		//var row = $(this).parent().parent();

		var row = $(this).closest('tr');

		var design = row.find(".cus_design").val();

		getSearchCusDesign(design, row);

	});

	/* Ends - design id search. */

	//Catalog Details

	$(document).on('keyup', ".lot_no", function (e) {

		var lot_no = this.value;

		var row = $(this).closest('tr');

		if (lot_no.length > 0) {

			getSearch_lot(lot_no, row);

		}

	});

	$(document).on('keyup', '.cat_gwt, .cat_lwt, .cat_wastage, .cat_mcm ,.cat_taxable_amt,.cat_pcs ,.cat_mc,.cat_market_rate_value', function (e) {

		var row = $(this).closest('tr');

		var id_purity = row.find('.cat_purity').val();

		if (id_purity == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Purity..' });

		}

		else {

			var gross_wt = (isNaN(row.find('.cat_gwt').val()) || row.find('.cat_gwt').val() == '') ? 0 : row.find('.cat_gwt').val();

			var less_wt = (isNaN(row.find('.cat_lwt').val()) || row.find('.cat_lwt').val() == '') ? 0 : row.find('.cat_lwt').val();

			var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt)).toFixed(3);

			row.find('.cat_nwt').val(net_wt);

			calculateSaleValue();

		}

	});

	$(document).on('keyup', '.cat_dis', function (e) {

		var row = $(this).closest('tr');

		var disc_limit = parseFloat($('#disc_limit').val());

		if (parseFloat(this.value) > disc_limit) {

			row.find('.cat_dis').val('');

			row.find('.cat_dis').focus();

			alert('Your Maximum Discount Limit is' + disc_limit);

		}

		calculateSaleValue();

	});

	$(document).on('change', '.mc_type', function (e) {

		var row = $(this).closest('tr');

		if (this.value != '') {

			row.find('.id_mc_type').val(this.value);

		}

		else {

			row.find('.id_mc_type').val(this.value);

		}

		calculateSaleValue();

	});

	$(document).on('keypress', '.cat_mc, .cat_amt', function (e) {

		if (e.which === 13) {

			e.preventDefault();

			if (validateCatalogDetailRow()) {

				create_new_empty_est_catalog_row();

			} else {

				alert("Please fill required fields");

			}

		}

	});

	$(document).on('keyup', '.cat_wastage', function () {

		let row_change = $(this).closest('tr');

		calc_cat_wastage_wt(row_change);

		calculateSaleValue();

	});

	$(document).on('keyup change', '.cat_gwt, .cat_lwt', function () {

		let row_change = $(this).closest('tr');

		update_catalog_wastage_mc(row_change);

		calculateSaleValue();

	});

	$(document).on('change', '.cat_wastage_wt', function (event) {

		//if ((event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode == 38 || event.keyCode == 40 || event.keyCode == 8 || event.keyCode == 46) {

		let row_change = $(this).closest('tr');

		let cat_nwt = row_change.find('.cat_nwt').val() > 0 ? row_change.find('.cat_nwt').val() : 0;

		let cat_wastage_wt = row_change.find('.cat_wastage_wt').val() > 0 ? row_change.find('.cat_wastage_wt').val() : 0;

		let wast_perc = parseFloat(parseFloat(cat_wastage_wt * 100) / parseFloat(cat_nwt)).toFixed(2);

		row_change.find('.cat_wastage').val(wast_perc);

		calculateSaleValue();

		//}

	});

	// Piece

	$(document).on('change', '.cat_pcs', function (e) {

		var curr_used_pcs = 0;

		var tot_blc_pcs = 0;

		var row = $(this).closest('tr');

		var cat_des_id = $(this).closest('tr').find('.cat_des_id').val();

		var is_non_tag = $(this).closest('tr').find('.is_non_tag').val();

		if (is_non_tag == 1) {

			$('#estimation_catalog_details> tbody  > tr').each(function (index, tr) {

				var id_design = $(this).find('.cat_des_id').val();

				if (id_design == cat_des_id) {

					tot_blc_pcs = $(this).find('.tot_blc_pcs').val();

					curr_used_pcs = parseFloat(($(this).find('.cat_pcs').val() == '' ? 0 : $(this).find('.cat_pcs').val()));

				}

			});

			if (tot_blc_pcs < curr_used_pcs) {

				row.find('.cat_pcs').val(0);

				row.find('.cat_pcs').focus();

				alert("Entered pieces greater than available pieces.");

			} else {

				row.find('.blc_pcs').html(tot_blc_pcs - curr_used_pcs);

			}

		}

	});

	//pieces

	//gross wt

	$(document).on('change', '.cat_gwt', function (e) {

		var curr_used_gwt = 0;

		var tot_blc_gwt = 0;

		var row = $(this).closest('tr');

		var cat_des_id = $(this).closest('tr').find('.cat_des_id').val();

		var is_non_tag = $(this).closest('tr').find('.is_non_tag').val();

		if (is_non_tag == 1) {

			$('#estimation_catalog_details> tbody  > tr').each(function (index, tr) {

				var id_design = $(this).find('.cat_des_id').val();

				var is_non_tag = $(this).find('.is_non_tag').val();

				if ((id_design == cat_des_id) && (is_non_tag == 1)) {

					tot_blc_gwt = $(this).find('.tot_blc_gwt').val();

					curr_used_gwt = parseFloat(($(this).find('.cat_gwt').val() == '' ? 0 : $(this).find('.cat_gwt').val()));

				}

			});

			if (tot_blc_gwt < curr_used_gwt) {

				row.find('.cat_gwt').val(0);

				row.find('.cat_nwt').val(0);

				row.find('.cat_gwt').focus();

				alert("Entered weight greater than available weight.");

			} else {

				row.find('.blc_gwt').html(parseFloat(tot_blc_gwt - curr_used_gwt).toFixed(3));

			}

		}

	});

	//gross wt

	//Catalog Details

	/* custom Product id search. - Start */

	$(document).on('keyup', ".cus_product", function (e) {

		var row = $(this).closest('tr');

		var product = row.find(".cus_product").val();

		getSearchCustomProducts(product, row);

	});

	/* Ends - product id search. */

	$(document).on('keyup', '.cus_gwt, .cus_lwt, .cus_wastage, .cus_mc,.cus_taxable_amt,.cus_market_rate_value', function (e) {

		var row = $(this).closest('tr');

		var gross_wt = (isNaN(row.find('.cus_gwt').val()) || row.find('.cus_gwt').val() == '') ? 0 : row.find('.cus_gwt').val();

		var blc_tag_gwt = (isNaN(row.find('.blc_tag_gwt').val()) || row.find('.blc_tag_gwt').val() == '') ? 0 : row.find('.blc_tag_gwt').val();

		if ((parseFloat(blc_tag_gwt) < parseFloat(gross_wt)) && (row.find('.cus_tag_name').val() != '')) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Valid Gross Wt..' });

			row.find('.cus_gwt').val(blc_tag_gwt);

		} else {

			var less_wt = (isNaN(row.find('.cus_lwt').val()) || row.find('.cus_lwt').val() == '') ? 0 : row.find('.cus_lwt').val();

			var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt)).toFixed(3);

			row.find('.cus_nwt').val(net_wt);

			calculateCustomItemSaleValue();

		}

	});

	$(document).on('keyup', '.cus_dis', function (e) {

		var row = $(this).closest('tr');

		var disc_limit = parseFloat($('#disc_limit').val());

		if (parseFloat(this.value) > disc_limit) {

			row.find('.cus_dis').val('');

			row.find('.cus_dis').focus();

			alert('Your Maximum Discount Limit is' + disc_limit);

		}

		calculateCustomItemSaleValue();

	});

	$(document).on('change', '.cus_mc_type ', function (e) {

		var row = $(this).closest('tr');

		if (this.value != '') {

			row.find('.id_mc_type').val(this.value);

		}

		else {

			row.find('.id_mc_type').val(this.value);

		}

		calculateCustomItemSaleValue();

	});

	$(document).on('keypress', '.cus_mc, .cus_amt', function (e) {

		if (e.which === 13) {

			e.preventDefault();

			if (validateCustomDetailRow()) {

				create_new_empty_est_custom_row();

			}

		}

	});

	$(document).on('keyup', '.cus_wastage', function () {

		let row_change = $(this).closest('tr');

		update_custom_wastage_wt(row_change);

		calculateCustomItemSaleValue();

	});

	$(document).on('keyup change', '.cus_gwt, .cus_lwt', function () {

		let row_change = $(this).closest('tr');

		update_custom_wastage_mc(row_change);

	});

	$(document).on('change', '.cus_wastage_wt', function (event) {

		//if ((event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode == 38 || event.keyCode == 40 || event.keyCode == 8 || event.keyCode == 46) {

		let row_change = $(this).closest('tr');

		let cus_nwt = row_change.find('.cus_nwt').val() > 0 ? row_change.find('.cus_nwt').val() : 0;

		let cus_wastage_wt = row_change.find('.cus_wastage_wt').val() > 0 ? row_change.find('.cus_wastage_wt').val() : 0;

		let wast_perc = parseFloat(parseFloat(cus_wastage_wt * 100) / parseFloat(cus_nwt)).toFixed(2);

		row_change.find('.cus_wastage').val(wast_perc);

		calculateCustomItemSaleValue();

		//}

	});

	//Old Gold

	$(document).on('change', '.old_amount', function (e) {

		var row = $(this).closest('tr');

		var old_rate = parseFloat(parseFloat(row.find('.old_amount').val() / parseFloat(row.find('.old_nwt').val())));

		row.find('.old_rate').val(old_rate);

		old_metalrate(row);

		calculateOldMatelItemSaleValue(row);

	})

	$(document).on('change', '.old_rate', function (e) {

		var row = $(this).closest('tr');

		old_metalrate(row);

		calculateOldMatelItemSaleValue(row);

	});

	function old_metalrate(curRow) {

		let min_old_gold_rate = $('#min_old_gold_rate').val();

		let min_old_silver_rate = $('#min_old_silver_rate').val();

		let max_old_gold_rate = $('#max_old_gold_rate').val();

		let max_old_silver_rate = $('#max_old_silver_rate').val();

		var id_metal = curRow.find('.old_id_category').val();

		var oldrate = curRow.find('.old_rate').val();

		var row = $(this).closest('tr');

		// condition to check minimum and maximum gold rates

		if (id_metal == 1 ? (oldrate < parseFloat(min_old_gold_rate)) || (oldrate > parseFloat(max_old_gold_rate)) : false) {

			$.toaster({ priority: 'danger', title: 'Warning!', settings: { 'timeout': 4000 }, message: '' + "</br>" + 'Enter valid Gold rate..' });

			curRow.find('.old_rate').val('');

			curRow.find('.old_rate').focus();

		}

		else if (id_metal == 2 ? (oldrate < parseFloat(min_old_silver_rate)) || (oldrate > parseFloat(max_old_silver_rate)) : false) {

			$.toaster({ priority: 'danger', title: 'Warning!', settings: { 'timeout': 4000 }, message: '' + "</br>" + 'Enter valid Silver rate..', timeOut: 9500 });

			curRow.find('.old_rate').val('');

			curRow.find('.old_rate').focus();

		}

	}

	/*$(document).on('change', '.old_gwt, .old_dwt,.old_swt', function(e){

		var row = $(this).closest('tr');

		var gross_wt = (isNaN(row.find('.old_gwt').val()) || row.find('.old_gwt').val() == '')  ? 0 : row.find('.old_gwt').val();

		var dust_wt  = (isNaN(row.find('.old_dwt').val()) || row.find('.old_dwt').val() == '')  ? 0 : row.find('.old_dwt').val();

		var stone_wt  = (isNaN(row.find('.old_swt').val()) || row.find('.old_swt').val() == '')  ? 0 : row.find('.old_swt').val();

		var old_wastage  = (isNaN(row.find('.old_wastage').val()) || row.find('.old_wastage').val() == '')  ? 0 : row.find('.old_wastage').val();

		var wastage_wt  = 0;

		var other_stone_wt  = (isNaN(row.find('.stone_wt').val()) || row.find('.stone_wt').val() == '')  ? 0 : row.find('.stone_wt').val();

		var net_wt = parseFloat(parseFloat(gross_wt) -(parseFloat(dust_wt)+parseFloat(stone_wt)+parseFloat(other_stone_wt)+parseFloat(wastage_wt))).toFixed(3);

		wastage_wt = parseFloat((net_wt * (old_wastage / 100))).toFixed(3);

		net_wt = parseFloat(parseFloat(gross_wt) -parseFloat(dust_wt)-parseFloat(stone_wt)-parseFloat(other_stone_wt)-parseFloat(wastage_wt)).toFixed(3);

		row.find('.old_wastage_wt').val(wastage_wt);

		row.find('.old_nwt').val(net_wt);

		calculateOldMatelItemSaleValue(row);

	});*/

	$(document).on('change', '.old_gwt, .old_dwt,.old_swt', function (e) {

		var row = $(this).closest('tr');

		calculateOldMatelItemSaleValue(row);

	});

	/*$(document).on('change', '.old_wastage', function(e){

	var row = $(this).closest('tr');

	var gross_wt = (isNaN(row.find('.old_gwt').val()) || row.find('.old_gwt').val() == '')  ? 0 : row.find('.old_gwt').val();

	var dust_wt  = (isNaN(row.find('.old_dwt').val()) || row.find('.old_dwt').val() == '')  ? 0 : row.find('.old_dwt').val();

	var stone_wt  = (isNaN(row.find('.old_swt').val()) || row.find('.old_swt').val() == '')  ? 0 : row.find('.old_swt').val();

	var old_wastage  = (isNaN(row.find('.old_wastage').val()) || row.find('.old_wastage').val() == '')  ? 0 : row.find('.old_wastage').val();

	var wastage_wt  = 0;

	var other_stone_wt  = (isNaN(row.find('.stone_wt').val()) || row.find('.stone_wt').val() == '')  ? 0 : row.find('.stone_wt').val();

	var net_wt = parseFloat(parseFloat(gross_wt) -(parseFloat(dust_wt)+parseFloat(stone_wt)+parseFloat(other_stone_wt)+parseFloat(wastage_wt))).toFixed(3);

	wastage_wt = parseFloat((net_wt * (old_wastage / 100))).toFixed(3);

	net_wt = parseFloat(parseFloat(gross_wt) -parseFloat(dust_wt)-parseFloat(stone_wt)-parseFloat(other_stone_wt)-parseFloat(wastage_wt)).toFixed(3);

	row.find('.old_wastage_wt').val(wastage_wt);

	row.find('.old_nwt').val(net_wt);

	calculateOldMatelItemSaleValue(row);

});*/

	$(document).on('change', '.old_wastage', function (e) {

		var row = $(this).closest('tr');

		var gross_wt = (isNaN(row.find('.old_gwt').val()) || row.find('.old_gwt').val() == '') ? 0 : row.find('.old_gwt').val();

		var dust_wt = (isNaN(row.find('.old_dwt').val()) || row.find('.old_dwt').val() == '') ? 0 : row.find('.old_dwt').val();

		var stone_wt = (isNaN(row.find('.old_swt').val()) || row.find('.old_swt').val() == '') ? 0 : row.find('.old_swt').val();

		var old_wastage = (isNaN(row.find('.old_wastage').val()) || row.find('.old_wastage').val() == '') ? 0 : row.find('.old_wastage').val();

		var wastage_wt = 0;

		var other_stone_wt = (isNaN(row.find('.stone_wt').val()) || row.find('.stone_wt').val() == '') ? 0 : row.find('.stone_wt').val();

		var net_wt = parseFloat(parseFloat(gross_wt) - (parseFloat(dust_wt) + parseFloat(stone_wt) + parseFloat(other_stone_wt) + parseFloat(wastage_wt))).toFixed(3);

		// Condition to check wastage % must be less than 100%

		if (parseFloat(old_wastage) > 100) {

			row.find('.old_wastage').val('');

			row.find('.old_wastage_wt').val('');

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Wastage % must be within 100%' })

			row.find('.old_wastage').focus();

			row.find('.old_nwt').val(net_wt);

			calculateOldMatelItemSaleValue(row);

		}

		else {

			wastage_wt = parseFloat((net_wt * (old_wastage / 100))).toFixed(5);

			net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(dust_wt) - parseFloat(stone_wt) - parseFloat(other_stone_wt) - parseFloat(wastage_wt)).toFixed(3);

			row.find('.old_wastage_wt').val(parseFloat(wastage_wt).toFixed(3));

			row.find('.old_nwt').val(net_wt);

			calculateOldMatelItemSaleValue(row);

		}

	});

	$(document).on('change', '.old_purity', function (e) {

		var row = $(this).closest('tr');

		var old_purity = (isNaN(row.find('.old_purity').val()) || row.find('.old_purity').val() == '') ? 0 : row.find('.old_purity').val();

		if (parseFloat(old_purity) > 100 || parseFloat(old_purity) < 10) {

			row.find('.old_purity').val('');

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please enter The Valid Purity..' })

			row.find('.old_purity').focus();

			// Reset category on invalid purity
			row.find('.old_metal_category').prop('disabled', false);
			row.find('.old_metal_category').select2("val", "");
			row.find('.old_metal_category').prop('disabled', true);

			calculateOldMatelItemSaleValue(row);

			return;

		}

		// Auto-select Old Metal Category based on purity/touch range
		var id_old_metal = row.find('.old_metal_type').val();
		var purityVal = parseFloat(old_purity);
		var matched_cat_id = null;

		if (id_old_metal > 0 && purityVal > 0) {

			$.each(old_metal_category, function (catkey, catitem) {

				if (catitem.id_old_metal_type == id_old_metal) {

					var touch_from = parseFloat(catitem.touch_from) || 0;
					var touch_to = parseFloat(catitem.touch_to) || 0;

					// Match: touch_from <= purity < touch_to (both must be > 0 to participate)
					if (touch_from > 0 && touch_to > 0 && purityVal >= touch_from && purityVal <= touch_to) {

						matched_cat_id = catitem.id_old_metal_cat;

						return false; // break loop

					}

				}

			});

			if (matched_cat_id != null) {

				// Auto-select the matched category and disable dropdown
				row.find('.old_metal_category').prop('disabled', false);
				row.find('.old_metal_category').select2("val", matched_cat_id);
				row.find('.old_metal_category').prop('disabled', true);

				// Add hidden input to carry value since disabled selects don't submit
				row.find('.old_metal_cat_hidden').remove();
				row.find('.old_metal_category').after('<input type="hidden" class="old_metal_cat_hidden" name="est_oldmatel[id_old_metal_category][]" value="' + matched_cat_id + '">');

				// Trigger the category change logic to calculate rate
				row.find('.old_metal_category').trigger('change');

			} else {

				// No matching range found — show error, clear purity
				row.find('.old_purity').val('');

				row.find('.old_metal_category').prop('disabled', false);
				row.find('.old_metal_category').select2("val", "");
				row.find('.old_metal_category').prop('disabled', true);
				row.find('.old_metal_cat_hidden').remove();

				row.find('.old_rate').val('');

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered purity does not match any category range. Please check the master settings.' })

				row.find('.old_purity').focus();

			}

		}

		calculateOldMatelItemSaleValue(row);

	});

	/*$(document).on('change', '.old_wastage_wt', function(e){

		var row = $(this).closest('tr');

		var gross_wt = (isNaN(row.find('.old_gwt').val()) || row.find('.old_gwt').val() == '')  ? 0 : row.find('.old_gwt').val();

		var dust_wt  = (isNaN(row.find('.old_dwt').val()) || row.find('.old_dwt').val() == '')  ? 0 : row.find('.old_dwt').val();

		var stone_wt  = (isNaN(row.find('.old_swt').val()) || row.find('.old_swt').val() == '')  ? 0 : row.find('.old_swt').val();

		var old_wastage_wt  = 0;

		var other_stone_wt  = (isNaN(row.find('.stone_wt').val()) || row.find('.stone_wt').val() == '')  ? 0 : row.find('.stone_wt').val();

		var old_wastage  = (isNaN(row.find('.old_wastage').val()) || row.find('.old_wastage').val() == '')  ? 0 : row.find('.old_wastage').val();

		var old_nwt  = (isNaN(row.find('.old_nwt').val()) || row.find('.old_nwt').val() == '')  ? 0 : row.find('.old_nwt').val();

		var net_wt = parseFloat(parseFloat(gross_wt) -(parseFloat(dust_wt)+parseFloat(stone_wt)+parseFloat(other_stone_wt)+parseFloat(old_wastage_wt))).toFixed(3);

		var wsatage_per =((parseFloat(old_wastage_wt)*100)/parseFloat(row.find('.old_nwt').val()));

		row.find('.old_nwt').val(net_wt);

		row.find('.old_wastage').val(wsatage_per);

		calculateOldMatelItemSaleValue(row);

	});*/

	$(document).on('keypress', '.old_dwt,.old_swt,.old_wastage_wt', function (event) {

		if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&

			((event.which < 48 || event.which > 57) &&

				(event.which != 0 && event.which != 8))) {

			event.preventDefault();

		}

		var text = $(this).val();

		if ((text.indexOf('.') != -1) &&

			(text.substring(text.indexOf('.')).length > 3) &&

			(event.which != 0 && event.which != 8) &&

			($(this)[0].selectionStart >= text.length - 3)) {

			event.preventDefault();

		}

	});

	$(document).on('change', '.old_wastage_wt', function (e) {

		var row = $(this).closest('tr');

		var gross_wt = (isNaN(row.find('.old_gwt').val()) || row.find('.old_gwt').val() == '') ? 0 : row.find('.old_gwt').val();

		var dust_wt = (isNaN(row.find('.old_dwt').val()) || row.find('.old_dwt').val() == '') ? 0 : row.find('.old_dwt').val();

		var stone_wt = (isNaN(row.find('.old_swt').val()) || row.find('.old_swt').val() == '') ? 0 : row.find('.old_swt').val();

		var wsatage_per = 0;

		var wastage_wt = 0;

		var other_stone_wt = (isNaN(row.find('.stone_wt').val()) || row.find('.stone_wt').val() == '') ? 0 : row.find('.stone_wt').val();

		var old_wastage_wt = (isNaN(row.find('.old_wastage_wt').val()) || row.find('.old_wastage_wt').val() == '') ? 0 : row.find('.old_wastage_wt').val();

		var old_nwt = (isNaN(row.find('.old_nwt').val()) || row.find('.old_nwt').val() == '') ? 0 : row.find('.old_nwt').val();

		var net_wt = parseFloat(parseFloat(gross_wt) - (parseFloat(dust_wt) + parseFloat(stone_wt) + parseFloat(other_stone_wt) + parseFloat(wastage_wt))).toFixed(3);

		// Condition to check whether Wastage wt is greater than NWT

		if (parseFloat(old_wastage_wt) > parseFloat(old_nwt)) {

			row.find('.old_wastage_wt').val('');

			row.find('.old_wastage').val('');

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Wastage WT is greater than Nwt' })

			row.find('.old_wastage_wt').focus();

			row.find('.old_nwt').val(net_wt);

			calculateOldMatelItemSaleValue(row);

		}

		else {

			wsatage_per = (((parseFloat(old_wastage_wt) * 100) / parseFloat(net_wt)).toFixed(2));

			net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(dust_wt) - parseFloat(stone_wt) - parseFloat(other_stone_wt) - parseFloat(old_wastage_wt)).toFixed(3);

			row.find('.old_nwt').val(net_wt);

			row.find('.old_wastage').val(wsatage_per);

			calculateOldMatelItemSaleValue(row);

		}

	});

	$(document).on('keypress', '.old_use_type, .old_amount', function (e) {

		if (e.which === 13) {

			e.preventDefault();

			if (validateOldMatelDetailRow()) {

				create_new_empty_est_old_row();

			} else {

				alert("Please fill required fields");

			}

		}

	});

	$(document).on('keypress', '.stone_price', function (e) {

		if (e.which === 13) {

			e.preventDefault();

			if (validateStoneDetailRow()) {

				create_new_empty_est_stone_row();

			} else {

				alert("Please fill required fields");

			}

		}

	});

	$(document).on('keypress', '.material_price', function (e) {

		if (e.which === 13) {

			e.preventDefault();

			if (validateMaterialDetailRow()) {

				create_new_empty_est_material_row();

			} else {

				alert("Please fill required fields");

			}

		}

	});

	//Old gold

	$(document).on('keypress', '.gift_voucher_amt', function (e) {

		if (e.which === 13) {

			e.preventDefault();

			if (validateVoucherDetailRow()) {

				create_new_empty_est_voucher_row();

			} else {

				alert("Please fill required fields");

			}

		}

	});

	$(document).on('keypress', '.chit_amt', function (e) {

		if (e.which === 13) {

			e.preventDefault();

			if (validateChitDetailRow()) {

				create_new_empty_est_chit_row();

			} else {

				alert("Please fill required fields");

			}

		}

	});

	old_metal_rate = [];

	old_metal_category = [];

	function get_all_old_metal_rates() {

		my_Date = new Date();

		$.ajax({

			url: base_url + "index.php/admin_ret_estimation/get_all_old_metal_rates?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

			type: "POST",

			dataType: "JSON",

			success: function (data) {

				old_metal_rate = data;

				//row.find('.old_rate').val(data.rate);

			},

			error: function (error) {

			}

		});

	}

	function get_old_metal_categories() {

		my_Date = new Date();

		$.ajax({

			url: base_url + "index.php/admin_ret_estimation/get_old_metal_category?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

			type: "POST",

			dataType: "JSON",

			success: function (data) {

				old_metal_category = data;

				//row.find('.old_rate').val(data.rate);

			},

			error: function (error) {

			}

		});

	}


	/* Fetches metal master data from the database. */
	function get_metal_master() {

		my_Date = new Date();

		$.ajax({

			url: base_url + "index.php/admin_ret_estimation/getMetalTypes?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

			type: "POST",

			dataType: "JSON",

			success: function (data) {

				metal_master = data;

				console.log('Metal Master loaded from database:', metal_master);

			},

			error: function (error) {

				console.error('Error loading metal master:', error);

			}

		});

	}

	$(document).on('change', '.old_id_category', function (e) {

		var row = $(this).closest('tr');

		row.find('.old_metal_type option:gt(0)').remove();

		row.find('.old_metal_category option:gt(0)').remove();

		row.find('.old_rate').val("");

		row.find('.old_metal_type ').select2("val", "");

		// Reset category (keep disabled — auto-selected by purity)
		row.find('.old_metal_category').prop('disabled', false);
		row.find('.old_metal_category').select2("val", "");
		row.find('.old_metal_category').prop('disabled', true);
		row.find('.old_metal_cat_hidden').remove();

		if (this.value > 0)

			get_old_metal_type(this.value, row);

		calculateOldMatelItemSaleValue(row);

	});

	function get_old_metal_type(id_metal, curRow) {

		curRow.find('.old_metal_type option:gt(0)').remove();

		$(".overlay").css('display', 'block');

		$.ajax({

			type: 'POST',

			url: base_url + 'index.php/admin_ret_estimation/get_old_metal_type',

			dataType: 'json',

			data: { 'id_metal': id_metal },

			success: function (data) {

				var metal_type = "";

				$.each(data, function (pkey, pitem) {

					metal_type += "<option value='" + pitem.id_metal_type + "'>" + pitem.metal_type + "</option>";

				});

				curRow.find('.old_metal_type').append(metal_type);

				$(".overlay").css('display', 'none');

			}

		});

	}

	$(document).on('change', '.old_metal_type', function (e) {

		var row = $(this).closest('tr');

		row.find('.old_metal_category option:gt(0)').remove();

		row.find('.old_rate').val("");

		// Reset category (keep disabled — auto-selected by purity)
		row.find('.old_metal_category').prop('disabled', false);
		row.find('.old_metal_category').select2("val", "");
		row.find('.old_metal_category').prop('disabled', true);
		row.find('.old_metal_cat_hidden').remove();

		var id_old_metal = this.value;

		if (id_old_metal > 0) {

			var metal_cat = "";

			$.each(old_metal_category, function (pkey, pitem) {

				if (pitem.id_old_metal_type == id_old_metal) {

					metal_cat += "<option value='" + pitem.id_old_metal_cat + "'>" + pitem.old_metal_cat + "</option>";

				}

			});

			//Old Metal Product 

			var metal_prod = "";

			var selectedprod = 0;

			$.each(old_metal_prod_details, function (pkey, pitem) {

				if (pitem.id_metal == id_old_metal) {

					metal_prod += "<option value='" + pitem.pro_id + "'>" + pitem.product_name + "</option>";

				}

			});

			row.find('.old_metal_product').append(metal_prod);

			row.find('.old_metal_category').append(metal_cat);

		}

	});

	$(document).on('change', '.old_metal_category', function (e) {

		var row = $(this).closest('tr');

		row.find('.old_rate').val("");

		var id_old_metal_cat = this.value;

		var id_old_metal = row.find('.old_metal_type').val();

		var id_metal = row.find('.old_id_category').val();

		if (id_old_metal_cat > 0 && id_old_metal > 0 && id_metal > 0) {

			let metal_perc = 0;

			let metal_rate = 0;

			let metal_discount = 0;

			$.each(old_metal_category, function (catkey, catitem) {

				if (catitem.id_old_metal_type == id_old_metal) {

					if (catitem.id_old_metal_cat == id_old_metal_cat) {

						metal_perc = catitem.old_metal_perc;

						metal_discount = catitem.old_metal_discount;

						return;

					}

				}

			});

			$.each(old_metal_rate, function (orkey, oritem) {

				if (oritem.id_metal == id_metal) {

					metal_rate = oritem.rate;

				}

			});

			metal_rate = parseFloat((metal_rate - metal_discount)) - (metal_rate * metal_perc / 100);

			row.find('.old_rate').val(metal_rate);

			calculateOldMatelItemSaleValue(row);

		}

	});

	$('#branch_select').on('change', function () {

		if (this.value != '') {

			$('#id_branch').val(this.value);

			if (ctrl_page[2] != 'list') {

				get_metal_rates_by_branch(this.value);

				get_invnetory_item();

				get_employee(this.value);

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

	/* Lot Received At */

	$('#lt_rcvd_branch_sel').on('change', function (e) {

		if (this.value != '') {

			$("#id_branch").val(this.value);

		}

		else {

			$("#id_branch").val('');

		}

	});

	/*$('#estimation_datetime').datetimepicker(

	{

		format: 'dd-mm-yyyy H:m:s'

	});*/

	$('#gross_wt, #less_wt').on('keyup', function (e) {

		var gross_wt = (isNaN($('#gross_wt').val()) || $('#gross_wt').val() == '') ? 0 : $('#gross_wt').val();

		var less_wt = (isNaN($('#less_wt').val()) || $('#less_wt').val() == '') ? 0 : $('#less_wt').val();

		var net_wt = parseFloat(gross_wt) - parseFloat(less_wt);

		$('#net_wt').val(net_wt);

		calculateWastage();

		calculateSaleValue();

	});

	$('input[type=checkbox][name="tagging[calculation_based_on]"]').change(function () {

		calculateWastage();

		calculateSaleValue();

	});

	$("#est_cus_id").select2({

		placeholder: "Select Customer",

		allowClear: true

	});

	$("#select_tax_group_id").on('change', function (e) {

		if (this.value != '') {

			var taxgroupid = this.value;

			my_Date = new Date();

			$.ajax({

				url: base_url + 'index.php/admin_ret_tagging/getAvailableTaxGroupItems/?nocache=' + my_Date.getUTCSeconds(),

				dataType: "json",

				method: "POST",

				data: { 'taxGroupId': taxgroupid },

				success: function (data) {

					tax_details = data;

					calculateSaleValue();

				}

			});

		}

	});

	$("input[name='product[product_stone]']").on('change', function () {

		if ($("input[name='product[product_stone]'][value='2']").prop("checked")) {

			$('#tot_stone_diamond').attr('disabled', 'disabled');

			$('#stone_name').prop("disabled", 'disabled');

			$("#diamond_block").hide();

		} else {

			$('#tot_stone_diamond').prop("disabled", false);

			$("#diamond_block").show();

			if ($("input[name='product[product_stone]'][value='0']").prop("checked")) {

				$('#stone_name').prop("disabled", 'disabled');

			}

			else {

				$('#stone_name').prop("disabled", false);

			}

		}

	});

	$("#_create_stn_details").on('click', function () {

		if (validateStoneDetailRow()) {

			var temp_tag_stones = tag_stones;

			if ($("#design_id").val() != "") {

				temp_tag_stones = tag_design_stones;

			}

			var html = "";

			var row_id = $('#tagging_stone_details tbody tr').length;

			var select_op = '<select class="form-control select_stn_det" id="tagstone_' + row_id + '" name="tagstone[stone_id][]"><option value=""> - Select Stone - </option>';

			var selected_stones = [];

			var op_length = 0;

			$('#tagging_stone_details > tbody  > tr').each(function (index, tr) {

				selected_stones.push({ "st_id": $(this).find('td:first   .select_stn_det').val() });

			});

			$.each(tag_stones, function (key, item) {

				var $exist_flag = false;

				$.each(selected_stones, function (stkey, stval) {

					if (stval.st_id == item.stone_id) {

						$exist_flag = true;

					}

				});

				if (!$exist_flag) {

					select_op += '<option value="' + item.stone_id + '">' + item.stone_name + '</option>';

					op_length++;

				}

			});

			select_op += '</select>';

			if (op_length > 0) {

				html += '<tr><td>' + select_op + '</td><td><div class="input-group"> <input class="form-control tagstone_pcs" type="number" step="any" name="tagstone[pcs][]" value="" required /></div></td><td><div class="input-group"><input type="number" class="form-control tagstone_wt" step="any" name="tagstone[weight][]" value="" required /></div></td><td><input type="hidden" name="tagstone[uom_id][]" value="" /><div class="stn_uom"></div></td><td><div class="input-group"> <input class="form-control tagstone_amt" type="number" step="any" name="tagstone[amount][]" value="" required /></div></td></tr>';

				$('#tagging_stone_details tbody').append(html);

			} else {

				alert("There is no more stone details are available");

			}

		} else {

			alert("Please fill required fields");

		}

	});

	$("#create_material_details").on('click', function () {

		if (validateMaterialDetailRow()) {

			var html = "";

			var row_id = $('#tagging_material_details tbody tr').length;

			var select_op = '<select class="form-control select_mat_det" id="tagmat_' + row_id + '" name="tagmaterials[material_id][]"><option value=""> - Select Material - </option>';

			var selected_materials = [];

			var op_length = 0;

			$('#tagging_material_details > tbody  > tr').each(function (index, tr) {

				selected_materials.push({ "mat_id": $(this).find('td:first .select_mat_det').val() });

			});

			$.each(tag_materials, function (key, item) {

				var $exist_flag = false;

				$.each(selected_materials, function (stkey, stval) {

					if (stval.mat_id == item.material_id) {

						$exist_flag = true;

					}

				});

				if (!$exist_flag) {

					select_op += '<option value="' + item.material_id + '">' + item.material_name + '</option>';

					op_length++;

				}

			});

			select_op += '</select>';

			if (op_length > 0) {

				html += '<tr><td>' + select_op + '</td><td><div class="input-group"> <input class="form-control tagmat_wt" type="number" step="any" name="tagmaterials[weight]" value="" required /></div></td><td><input type="hidden" name="tagmaterials[uom_id]" value="" /><div class="stn_uom"></div></td><td><div class="input-group"> <input class="form-control tagmat_amt" type="number" step="any" name="tagmaterials[amount][]" value="" required /></div></td></tr>';

				$('#tagging_material_details tbody').append(html);

			} else {

				alert("There is no more materials details are available");

			}

		} else {

			alert("Please fill required fields");

		}

	});

	$(document).on('change', ".select_stn_det", function () {

		//alert(this.value);

		var selectId = this.value;

		//var row = $(this).parent().parent();

		var row = $(this).closest('tr');

		if (selectId != "") {

			$.each(tag_stones, function (key, item) {

				if (item.stone_id == selectId) {

					row.find('td:eq(3) .stn_uom').html(item.uom_short_code);

					//row.find('td eq(3) .stn_uom').html(item.uom_short_code);

					$(row).find("td:eq(3) input[type='hidden']").val(item.uom_id);

				}

			});

		} else {

			row.find('td:eq(3) .stn_uom').html("");

			$(row).find("td:eq(3) input[type='hidden']").val("");

		}

	});

	$(document).on('change', ".select_mat_det", function () {

		var selectedId = this.value;

		var row = $(this).closest('tr');

		if (selectedId != "") {

			$.each(tag_materials, function (key, item) {

				if (item.material_id == selectedId) {

					row.find('td:eq(2) .stn_uom').html(item.uom_short_code);

					$(row).find("td:eq(2) input[type='hidden']").val(item.uom_id);

				}

			});

		} else {

			row.find('td:eq(2) .stn_uom').html("");

			$(row).find("td:eq(2) input[type='hidden']").val("");

		}

	});

	$('#emp_select').on('change', function () {

		if (this.value != '') {

			let emp_id = this.value;

			$('#id_employee').val(emp_id);

			if ($('.item_emp_id').length) {

				$('.item_emp_id').each(function () {

					$(this).select2("val", emp_id);

				});

			}

			$.each(emp_details, function (key, item) {

				var id_employee = $('#id_employee').val();

				if (item.id_employee == id_employee) {

					$('#disc_limit_type').val(item.disc_limit_type);

					$('#disc_limit').val(item.disc_limit);

					$('#allowed_old_met_pur').val(item.allowed_old_met_pur);

					if (item.allow_branch_transfer == 1) {

						$('#type2').prop('disabled', false);

					} else {

						$('#type2').prop('disabled', true);

					}

				}

			});

		}

		else {

			$('#id_employee').val('');

			$('#disc_limit_type').val('');

			$('#disc_limit').val('');

			$('#allowed_old_met_pur').val('');

		}

	});

	//Employee Filter

	$("#est_print").click(function () {

		var form_validate_tag = false;

		var form_validate_nontag = false;

		var form_validate_custom = false;

		var form_validate_old = false;

		var esti_for = $("input[name='estimation[esti_for]']:checked").val();

		var ask_cus_data = (esti_for == 1 ? ($('#cus_id').val() == '' ? false : true) : true);

		var ask_branch = ($('#id_branch').val() == '' ? false : true);

		var ask_emp_data = ($('#id_employee').val() == '' ? false : true);

		// $("#iseda").val(0);

		var ask_cus_addr1 = ($('#cus_del_address1').val() == '' ? false : true);

		var ask_cus_pincode = ($('#cus_del_pincode').val() == '' ? false : true);

		var ask_cus_country = ($('#cus_del_country').val() == '' ? false : true);

		var ask_cus_state = ($('#cus_del_state').val() == '' ? false : true);

		if (ask_cus_data && esti_for == 1){

			if(!ask_cus_addr1){

				alert('Customer Address Not Available..!');

			}

			if(!ask_cus_pincode){

				alert('Customer Pincode Not Available..!');

			}

			if(!ask_cus_country){

				alert('Customer Country Not Available..!');

			}

			if(!ask_cus_state){

				alert('Customer State Not Available..!');

			}

		} else {

			ask_cus_addr1 = true;

			ask_cus_pincode = true;

			ask_cus_country = true;

			ask_cus_state = true;

		}

		let tag_checked= $('#select_tag_details').is(':checked');

		let nontag_checked= $('#select_catalog_details').is(':checked');

		let home_checked= $('#select_custom_details').is(':checked');

		let oldmetal_checked= $('#select_oldmatel_details').is(':checked');

		let est_checked = false;

		if (!ask_branch) {

			alert('Please Select Branch');

		}

		else if (!ask_emp_data) {

			alert('Please Select Employee');

		}

		else if (!ask_cus_data && esti_for!=2) {

			alert('Please Select Customer');

		}else if(tag_checked || nontag_checked || home_checked || oldmetal_checked){

		     est_checked = true;

		}else if(!tag_checked || !nontag_checked || !home_checked || !oldmetal_checked){

		    alert("Select any Estimation Items");

		}

		if ($('#select_tag_details').is(":checked")) {

			if ($('#estimation_tag_details tbody tr').length > 0) {

				if (validateTagDetailRow()) {

					form_validate_tag = true;

				} else {

					form_validate_tag = false;

					//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The All Required Fields in Tag Detail Row..'});

				}

			} else {

				form_validate_tag = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Tagging Details..' });

			}

		}

		else {

			form_validate_tag = true;

			$('#estimation_tag_details tbody').empty();

		}

		if ($('#select_catalog_details').is(":checked")) {

			if ($('#estimation_catalog_details tbody tr').length > 0) {

				if (validateCatalogDetailRow()) {

					form_validate_nontag = true;

				} else {

					form_validate_nontag = false;

					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The All Required Fields in Catalog Bill Detail Row..' });

				}

			} else {

				form_validate_nontag = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Catalog Details..' });

			}

		}

		else {

			form_validate_nontag = true;

			$('#estimation_catalog_details tbody').empty();

		}

		if ($('#select_custom_details').is(":checked")) {

			if ($('#estimation_custom_details tbody tr').length > 0) {

				if (validateCustomDetailRow()) {

					form_validate_custom = true;

				} else {

					form_validate_custom = false;

				}

			} else {

				form_validate_custom = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Home Bill Details..' });

			}

		}

		else {

			form_validate_custom = true;

			$('#estimation_custom_details tbody').empty();

		}

		if ($('#select_oldmatel_details').is(":checked")) {

			if ($('#estimation_old_matel_details tbody tr').length > 0) {

				if (validateOldMatelDetailRow()) {

					form_validate_old = true;

				} else {

					form_validate_old = false;

					//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The All Required Fields in Old Metal Row..'});

				}

			} else {

				form_validate_old = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The All Required Fields in Old Metal Row..' });

			}

		}

		else {

			form_validate_old = true;

			$('#estimation_old_matel_details tbody').empty();

		}

		if (ask_cus_data && ask_branch && ask_emp_data && form_validate_tag && form_validate_nontag && form_validate_custom && form_validate_old && est_checked && ask_cus_addr1 && ask_cus_pincode && ask_cus_country && ask_cus_state) {

			my_Date = new Date();

			$("div.overlay").css("display", "block");

			$("#pay_print").attr("disabled", true);

			$("#pay_save").attr("disabled", true);

			var post_data = $('#est_form').serialize();

			if (ctrl_page[2] == 'add') {

				var url = base_url + "index.php/admin_ret_estimation/estimation/save?nocache=" + my_Date.getUTCSeconds();

			}

			else {

				var url = base_url + 'index.php/admin_ret_estimation/estimation/update/' + ctrl_page[3] + '?nocache=' + my_Date.getUTCSeconds();

			}

			$.ajax({

				url: url,

				data: post_data,

				type: "POST",

				dataType: "JSON",

				success: function (data) {

					$("#est_print").attr("disabled", false);

					if (data.type == 2) {

						//window.location.href= base_url+'index.php/admin_ret_estimation/estimation/list';

					}

					else {

						if (data.status) {

							window.open(base_url + 'index.php/admin_ret_estimation/generate_invoice/' + data['id'], '_blank');

							//window.open( base_url+'index.php/admin_ret_estimation/generate_brief_copy/'+data['id'],'_blank');

						}

						//window.location.href= base_url+'index.php/admin_ret_estimation/estimation/list';

					}

					window.location.href = base_url + 'index.php/admin_ret_estimation/estimation/list';

					$("div.overlay").css("display", "none");

				},

				error: function (error) {

					$("#est_print").attr("disabled", false);

					$("div.overlay").css("display", "none");

				}

			});

		}

	});

	$("#est_eda_print").click(function () {

		var form_validate = false;

		var esti_for = $("input[name='estimation[esti_for]']:checked").val();

		var ask_cus_data = (esti_for == 1 ? ($('#cus_id').val() == '' ? false : true) : true);

		var ask_branch = ($('#id_branch').val() == '' ? false : true);

		var ask_emp_data = ($('#id_employee').val() == '' ? false : true);

		$("#iseda").val(1);

		if (!ask_branch) {

			alert('Please Select Branch');

		}

		else if (!ask_emp_data) {

			alert('Please Select Employee');

		}

		else if (!ask_cus_data) {

			alert('Please Select Customer');

		}

		if ($('#select_tag_details').is(":checked")) {

			if ($('#estimation_tag_details').length >= 0) {

				if (validateTagDetailRow()) {

					form_validate = true;

				} else {

					form_validate = false;

					//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The All Required Fields in Tag Detail Row..'});

				}

			} else {

				form_validate = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Tagging Details..' });

				alert('Please Fill The Tagging Details..');

			}

		}

		else {

			$('#estimation_tag_details tbody').empty();

		}

		if ($('#select_catalog_details').is(":checked")) {

			if ($('#estimation_catalog_details').length >= 0) {

				if (validateCatalogDetailRow()) {

					form_validate = true;

				} else {

					form_validate = false;

					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The All Required Fields in Catalog Bill Detail Row..' });

				}

			} else {

				form_validate = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Catalog Details..' });

			}

		}

		if ($('#select_custom_details').is(":checked")) {

			if ($('#estimation_custom_details').length >= 0) {

				if (validateCatalogDetailRow()) {

					form_validate = true;

				} else {

					form_validate = false;

					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The All Required Fields in Home Bill Detail Row..' });

				}

			} else {

				form_validate = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Home Bill Details..' });

			}

		}

		else {

			$('#estimation_custom_details tbody').empty();

		}

		if ($('#select_oldmatel_details').is(":checked")) {

			if ($('#estimation_old_matel_details').length >= 0) {

				if (validateOldMatelDetailRow()) {

					form_validate = true;

				} else {

					form_validate = false;

					//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The All Required Fields in Old Metal Row..'});

				}

			} else {

				form_validate = false;

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The All Required Fields in Old Metal Row..' });

			}

		}

		else {

			$('#estimation_old_matel_details tbody').empty();

		}

		if (ask_cus_data && ask_branch && ask_emp_data && form_validate) {

			my_Date = new Date();

			$("div.overlay").css("display", "block");

			$("#pay_print").attr("disabled", true);

			$("#pay_save").attr("disabled", true);

			var post_data = $('#est_form').serialize();

			if (ctrl_page[2] == 'add') {

				var url = base_url + "index.php/admin_ret_estimation/estimation/save?nocache=" + my_Date.getUTCSeconds();

			}

			else {

				var url = base_url + 'index.php/admin_ret_estimation/estimation/update/' + ctrl_page[3] + '?nocache=' + my_Date.getUTCSeconds();

			}

			$.ajax({

				url: url,

				data: post_data,

				type: "POST",

				dataType: "JSON",

				success: function (data) {

					$("#est_print").attr("disabled", false);

					if (data.type == 2) {

						//window.location.href= base_url+'index.php/admin_ret_estimation/estimation/list';

						window.location.reload();

					}

					else {

						if (data.status) {

							window.open(base_url + 'index.php/admin_ret_estimation/generate_invoice/' + data['id'], '_blank');

							//window.open( base_url+'index.php/admin_ret_estimation/generate_brief_copy/'+data['id'],'_blank');

						}

						//window.location.href= base_url+'index.php/admin_ret_estimation/estimation/list';

						window.location.reload();

					}

					$("div.overlay").css("display", "none");

				},

				error: function (error) {

					$("#est_print").attr("disabled", false);

					$("div.overlay").css("display", "none");

				}

			});

		}

	});

});

function deleteEstimation(id) {

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/estimation/delete/" + id + "?nocache=" + my_Date.getUTCSeconds(),

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

/* function add_customer(cus_name, cus_mobile, id_village, cus_type, gst_no, img) {

	var esti_for = $("input[name='estimation[esti_for]']:checked").val();

	var gender = $("input[name='customer[gender]']:checked").val();

	var form_data = new FormData();

	form_data.append('cusName', cus_name);

	form_data.append('cusMobile', cus_mobile);

	form_data.append('cusBranch', $('#id_branch').val());

	form_data.append('id_village', id_village);

	form_data.append('gst_no', gst_no);

	form_data.append('cus_type', esti_for == 1 ? 1 : 2);

	form_data.append('id_country', $('#country').val());

	form_data.append('id_state', $('#state').val());

	form_data.append('id_city', $('#city').val());

	form_data.append('address1', $('#address1').val());

	form_data.append('address2', $('#address2').val());

	form_data.append('address3', $('#address3').val());

	form_data.append('pincode', $('#pin_code_add').val());

	form_data.append('mail', $('#cus_email').val());

	form_data.append('cust_img', img);

	form_data.append('customer_img', $('#customer_img').val());

	form_data.append('title', $('#title').val());

	form_data.append('gender', gender);

	form_data.append('id_profession', $('#professionval').val());

	form_data.append('date_of_birth', $('#date_of_birth').val());

	form_data.append('date_of_wed', $('#date_of_wed').val());

	form_data.append('pan_no', $('#pan').val());

	form_data.append('aadharid', $('#aadharid').val());

	form_data.append('dl_no', $('#dl').val());

	form_data.append('pp_no', $('#pp').val());

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/createNewCustomer/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: form_data,

		cache: false,

		enctype: 'multipart/form-data',

		contentType: false,

		processData: false,

		//Need to update login branch id here from session

		success: function (data) {

			if (data.success == true) {

				$('#confirm-add').modal('toggle');

				$('#cus_first_name').val('');

				$('#pin_code_add').val('');

				$('#address3').val('');

				$('#address2').val('');

				$('#address1').val('');

				$('#cus_email').val('');

				$('#cus_mobile').val('');

				$('#cus_image').val(null);

				$("#cus_img_preview").attr("src", base_url + "assets/img/default.png");

				$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);

				$("#cus_id").val(data.response.id_customer);

				$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + 'Customer Created SuccessFully.' });

			} else {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });

			}

		}

	});

} */

function getSearchCustomers(searchTxt) {

	var esti_for = $("input[name='estimation[esti_for]']:checked").val();

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'esti_for': esti_for },

		success: function (data) {

			$("#est_cus_name").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						$("#est_cus_name").val(i.item.label);

						$("#cus_id").val(i.item.value);

						$("#cus_village").html(i.item.village_name);

						$('#id_country').val(i.item.id_country);

						$('#id_city').val(i.item.id_city);

						$('#id_state').val(i.item.id_state);
						$('#address1').val(i.item.address1);
						$('#pin_code_add').val(i.item.pincode);

						$("#cus_info").append(i.item.vip == 'Yes' ? "<span class='label bg-orange'><i class='fa fa-fw fa-star'></i> V I P</span>" : "");

						$("#cus_info").append(i.item.accounts > 0 ? "&nbsp;<span class='label label-info'>Chit Customer</span>" : "");

						if ($('#estimation_chit_details > tbody > tr').length > 0) {

							$('#estimation_chit_details > tbody').empty();

							calculate_purchase_details();

							calculate_sales_details();

						}

						if (i.item.cus_type == 2 && esti_for != 3) {

							$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Mobile Number Already registered in Business Profile' });

							$("#est_cus_name").val('');

						}

						else if (i.item.cus_type == 1 && esti_for == 3) {

							$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Mobile Number Already registered in  Individual Profile' });

							$("#est_cus_name").val('');

						}

						else {

							customer_detail_modal(i.item.value); // Customer Purchase and Account Details 

						}

						getCustomerSalesDetails(i.item.value);

					},

					change: function (event, ui) {

						if (ui.item === null) {

							$(this).val('');

							$('#est_cus_name').val('');

							$("#cus_id").val("");

							$("#cus_village").html("");

							$("#cus_info").html("");

							if ($('#estimation_chit_details > tbody > tr').length > 0) {

								$('#estimation_chit_details > tbody').empty();

								calculate_purchase_details();

								calculate_sales_details();

							}

							/*$("#chit_cus").html("");

							$("#vip_cus").html("");*/

						}

					},

					response: function (e, i) {

						// ui.content is the array that's about to be sent to the response callback.

						if (searchTxt != "") {

							if (i.content.length === 0) {

								var mobile = $('#est_cus_name').val();

								if (mobile.length == 10) {

									create_customer(mobile);

									$("#customerAlert").html('');

								} else {

									$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter a valid customer name / mobile' });

								}

							}

							else {

								$("#customerAlert").html('');

							}

						} else {

						}

					},

					minLength: 1,

				});

		}

	});

}

/* reset_customer_slider() and open_customer_slider() are defined in ret_general.js
 * and shared across all pages that use the #demo offcanvas slider. */

function create_customer(mobile_no) {
	// open_customer_slider() is defined in ret_general.js — shared by all pages
	open_customer_slider({ mobile_no: mobile_no });
}

function getSearchTags(searchTxt, searchField, curRow) {

	console.log("getSearchTags");

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getTaggingBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'searchField': searchField, 'id_branch': $("#id_branch").val() },

		success: function (data) {

			cur_search_tags = data;

			$.each(data, function (key, item) {

				/*$('#estimation_tag_details > tbody tr').each(function(idx, row){

					if(item != undefined){

						if($(this).find('.est_tag_id').val() == item.value){

							data.splice(key, 1);

						}

					}

				});*/

				$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

					let records = data.filter(tag => tag.tag_id == $(this).find('.est_tag_id').val());

					if (records.length > 0) {

						console.log($(this).find('.est_tag_id').val());

						let index = data.indexOf(records[0]);

						if (index !== -1) {

							data.splice(index, 1);

						}

					}

					if ($(this).find('.tax_group_id').val() != '') {

						let tag_details = data.filter(tag => tag.tax_group_id != $(this).find('.tax_group_id').val());

						if (tag_details.length > 0) {

							let index = data.indexOf(tag_details[0]);

							if (index !== -1) {

								data.splice(index, 1);

							}

						}

					}

				});

			});

			$(".est_tag_name").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						var curRowItem = i.item;

						let rate_field = curRowItem.rate_field;

						let rate_per_grm = 0;

						let emp_select = $("#emp_select").val();

						if (rate_field != '') {

							rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

						}

						if (curRowItem.sales_mode == 2) { // 1 - Fixed Rate, 2 - Flexible

							get_metal_rates_by_branch(i.item.current_branch);

						}

						if (curRowItem.tag_mark == 1) {

							$(".est_tag_name").css("background-color", "#43e143");

						}

						var stone_details = [];

						var other_metal_details = [];

						var tag_other_itm_amount = 0;

						$.each(i.item.stone_details, function (skey, sitem) {

							stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });

						});

						$.each(i.item.other_metal_details, function (skey, sitem) {

							tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

							other_metal_details.push({

								"tag_other_itm_id": sitem.tag_other_itm_id,

								"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

								"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

								"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

								"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

								"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

								"tag_other_itm_uom": sitem.tag_other_itm_uom,

								"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

								"tag_other_itm_mc": sitem.tag_other_itm_mc,

								"tag_other_itm_rate": sitem.tag_other_itm_rate,

								"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

								"tag_other_itm_amount": sitem.tag_other_itm_amount,

								"purname": sitem.purname,

                                 "metal_name": sitem.metal_name,



							});

						});

						curRow.find('.est_tag_name').val(i.item.label);

						curRow.find('.item_emp_id').select2("val", emp_select);

						curRow.find('.est_tag_id').val(i.item.value);

						curRow.find('.is_partial').val(0);

						curRow.find('.prodct_name').html(curRowItem.product_name);

						curRow.find('.design_name').html(curRowItem.design_name);

						curRow.find('.design_id').val(curRowItem.design_id);

						curRow.find('.sub_design_name').html(curRowItem.sub_design_name);

						curRow.find('.id_sub_design').val(curRowItem.id_sub_design);

						curRow.find('.pro_id').val(curRowItem.lot_product);

						curRow.find('.purity').html(curRowItem.purname);

						curRow.find('.purity').val(curRowItem.purity);

						curRow.find('.sizes').html(curRowItem.size_name);

						curRow.find('.size').val(curRowItem.size);

						curRow.find('.pieces').html(curRowItem.piece);

						curRow.find('.piece').val(curRowItem.piece);

						curRow.find('.gwt').val(curRowItem.gross_wt);

						curRow.find('.cur_gwt').val(curRowItem.gross_wt);

						curRow.find('.lwt').val(curRowItem.less_wt);

						curRow.find('.nwt').html(curRowItem.net_wt);

						curRow.find('.tot_tag_nwt').val(curRowItem.net_wt);

						curRow.find('.tot_nwt').val(curRowItem.net_wt);

						curRow.find('.tot_nwt').val(curRowItem.net_wt);

						curRow.find('.wastage').html(curRowItem.retail_max_wastage_percent);

						curRow.find(".wastage_max_per").val(curRowItem.retail_max_wastage_percent);

						curRow.find(".tag_other_itm_amount").val(curRowItem.tag_other_itm_amount);

						//curRow.find('.mc').html(curRowItem.tag_mc_value);

						//curRow.find('.mc_value').val(curRowItem.tag_mc_value);

						curRow.find('.cost').html(curRowItem.sales_value);

						curRow.find(".sales_value").val(curRowItem.sales_value);

						curRow.find(".act_sales_value").val(curRowItem.sales_value);

						curRow.find(".caltype").val(curRowItem.calculation_based_on);

						curRow.find(".tax_percentage").val(curRowItem.tax_percentage);

						curRow.find(".tgi_calculation").val(curRowItem.tgi_calculation);

						curRow.find(".stone_price").val(curRowItem.stone_price);

						curRow.find(".stone_details").val(JSON.stringify(stone_details));

						curRow.find(".other_metal_details").val(JSON.stringify(other_metal_details));

						curRow.find(".certification_price").val(curRowItem.certification_cost);

						curRow.find(".id_mc_type").val(curRowItem.tag_mc_type);

						curRow.find(".tag_item_rate").val(curRowItem.item_rate);

						curRow.find(".metal_type").val(curRowItem.metal_type);

						curRow.find(".tax_group_id").val(curRowItem.tax_group_id);

						curRow.find(".act_gwt").val(curRowItem.gross_wt);

						curRow.find(".act_mc_value").val(curRowItem.tag_mc_value);

						curRow.find(".id_orderdetails").val(curRowItem.id_orderdetails);

						curRow.find(".order_no").html(curRowItem.order_no);

						curRow.find(".rate_field").val(curRowItem.rate_field);

						curRow.find(".market_rate_field").val(curRowItem.market_rate_field);

						curRow.find(".id_mapping_details").val(curRowItem.id_mapping_details);

						curRow.find('.market_rate_value').val(rate_per_grm);

						curRow.find(".charge_value").val(curRowItem.charge_value);

						curRow.find(".charge").html(curRowItem.charge_value);

						curRow.find(".scheme_closure_benefit").val(curRowItem.scheme_closure_benefit);

						if (curRowItem.calculation_based_on == 3 || curRowItem.calculation_based_on == 4) {

							curRow.find(".partial").prop("disabled", true);

						} else {

							curRow.find(".partial").prop("disabled", false);

						}

						curRow.find(".sales_mode").val(curRowItem.sales_mode);

						curRow.find(".purchase_cost").val(curRowItem.tag_purchase_cost);

						let pur_mc = typeof curRowItem.po_details != 'undefined' && curRowItem.po_details != null && curRowItem.po_details.length != 0 ? (!(curRowItem.po_details[0].mc_value >= 0) || curRowItem.po_details[0].mc_value == null ? 0 : curRowItem.po_details[0].mc_value) : 0;

						let pur_va = typeof curRowItem.po_details != 'undefined' && curRowItem.po_details != null && curRowItem.po_details.length != 0 ? (!(curRowItem.po_details[0].item_wastage >= 0) || curRowItem.po_details[0].item_wastage == null ? 0 : curRowItem.po_details[0].item_wastage) : 0;

						curRow.find(".pur_mc").val(pur_mc);

						curRow.find(".pur_va").val(pur_va);

						/*if(curRowItem.order_no!='')

						{

							get_order_items(curRowItem.order_no);

						}*/

						calculatetag_SaleValue();

						/*if(validateTagDetailRow()){

							create_new_empty_est_tag_row();

							$('#estimation_tag_details > tbody').find('tr:last td:eq(0) .est_tag_name').focus();

						}*/

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

							curRow.find('.est_tag_name').val("");

							curRow.find('.est_tag_id').val("");

						}

					},

					minLength: 1,

				});

			/*if(cur_search_tags.length <= 0 )

			{

				get_tag_status_details({'searchTxt': searchTxt, 'searchField': searchField, 'id_branch': $("#id_branch").val()});

			}*/

		}

	});

}

function getSearch_lot(searchTxt, curRow) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getNonTagLots/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'id_branch': $("#id_branch").val() },

		success: function (data) {

			$(".lot_no").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						curRow.find('.lot_no').val(i.item.value);

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

							curRow.find('.lot_no').val("");

						}

					},

					minLength: 1,

				});

		}

	});

}

$(document).on('change', '.cat_purity', function (e) {

	var row = $(this).closest('tr');

	get_search_catalog_metal_rates(row);

});

function get_search_catalog_metal_rates(curRow) {

	var id_purity = curRow.find('.cat_purity').val();

	var id_metal = curRow.find('.metal_type').val();

	if (id_purity == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Purity..' });

		curRow.find('.cat_rate_field').val('');

		curRow.find('.cat_market_rate_field').val('');

	}

	else if (id_metal == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Metal..' });

		curRow.find('.cat_rate_field').val('');

		curRow.find('.cat_market_rate_field').val('');

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

				curRow.find('.cat_rate_field').val(data.rate_field);

				curRow.find('.cat_market_rate_field').val(data.market_rate_field);

				let rate_field = data.rate_field;

				let rate_per_grm = 0;

				if (rate_field != '') {

					rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

				}

				curRow.find('.cat_market_rate_value').val(rate_per_grm);

				calculateSaleValue();

			}

		});

	}

}

function getSearchProducts(searchTxt, curRow) {

	if (searchTxt.length >= 2) {

		my_Date = new Date();

		$.ajax({

			url: base_url + 'index.php/admin_ret_estimation/getProductBySearch/?nocache=' + my_Date.getUTCSeconds(),

			dataType: "json",

			method: "POST",

			data: { 'searchTxt': searchTxt, 'is_non_tag': curRow.find('.is_non_tag').val(), 'id_branch': $('#id_branch').val() },

			success: function (data) {

				$(".cat_product").autocomplete(

					{

						source: data,

						select: function (e, i) {

							e.preventDefault();

							//var tax_percentage=[];

							curRow.find('.cat_product').val(i.item.label);

							curRow.find('.cat_pro_id').val(i.item.value);

							curRow.find('.tax_percentage').val(i.item.tax_percentage);

							curRow.find('.tgi_calculation').val(i.item.tgi_calculation);

							curRow.find('.tax_group_id').val(i.item.tax_group_id);

							curRow.find('.cat_pcs').val(i.item.no_of_pieces);

							curRow.find('.cat_calculation_based_on').val(i.item.calculation_based_on);

							curRow.find('.metal_type').val(i.item.id_metal);

							curRow.find('.cat_design').val("");

							curRow.find('.cat_des_id').val("");

							curRow.find('.cat_sales_mode').val(i.item.sales_mode);

							curRow.find('.scheme_closure_benefit').val(i.item.scheme_closure_benefit);

							if (i.item.sales_mode == 1) {

								curRow.find('.cat_gwt').attr("readonly", true);

								curRow.find('.cat_lwt').attr("readonly", true);

								curRow.find('.cat_mc').attr("readonly", true);

								curRow.find('.cat_wastage').attr("readonly", true);

								curRow.find('.cat_wastage_wt').attr("readonly", true);

								curRow.find('.cat_taxable_amt').attr("readonly", false);

							} else {

								curRow.find('.cat_gwt').attr("readonly", false);

								curRow.find('.cat_lwt').attr("readonly", false);

								curRow.find('.cat_mc').attr("readonly", false);

								curRow.find('.cat_wastage').attr("readonly", false);

								curRow.find('.cat_wastage_wt').attr("readonly", false);

								curRow.find('.cat_taxable_amt').attr("readonly", true);

							}

							var curRowItem = i.item;

							var curr_used_gross = 0;

							var curr_used_pcs = 0;

							if (curRow.find('.is_non_tag').val() == 1) {

								$('#estimation_catalog_details> tbody  > tr').each(function (index, tr) {

									if ($(this).find('.cat_des_id').val() == i.item.value && ($(this).find('.is_non_tag').val() == 1)) {

										curr_used_gross += parseFloat(($(this).find('.cat_gwt').val() == '' ? 0 : $(this).find('.cat_gwt').val()));

										curr_used_pcs += parseFloat(($(this).find('.cat_pcs').val() == '' ? 0 : $(this).find('.cat_pcs').val()));

									}

								});

								curRow.find('.tot_blc_pcs').val(i.item.no_of_piece - curr_used_pcs);

								curRow.find('.tot_blc_gwt').val(i.item.gross_wt - curr_used_gross);

								curRow.find('.blc_pcs').html(i.item.no_of_piece - curr_used_pcs);

								curRow.find('.blc_gwt').html(i.item.gross_wt - curr_used_gross);

							}

							console.log(curRowItem);

							$('#estimation_catalog_details > tbody').find('.cat_design').focus();

						},

						change: function (event, ui) {

							if (curRow.find('.cat_pro_id').val() == '') {

								curRow.find('.cat_product').focus();

								curRow.find('.cat_product').val('');

								curRow.find('.cat_sales_mode').val('');

								curRow.find('.cat_gwt').attr("readonly", false);

								curRow.find('.cat_lwt').attr("readonly", false);

								curRow.find('.cat_mc').attr("readonly", false);

								curRow.find('.cat_wastage').attr("readonly", false);

								curRow.find('.cat_wastage_wt').attr("readonly", false);

								curRow.find('.cat_taxable_amt').attr("readonly", true);

							}

						},

						response: function (e, i) {

							// ui.content is the array that's about to be sent to the response callback.

							if (searchTxt != "") {

								if (i.content.length !== 0) {

									console.log("content : ", i.content);

								}

							} else {

								curRow.find('.cat_product').val("");

								curRow.find('.cat_pro_id').val("");

								curRow.find('.tax_percentage').val("");

								curRow.find('.tgi_calculation').val("");

								curRow.find('.cat_pcs').val("");

								curRow.find('.cat_calculation_based_on').val("");

								curRow.find('.metal_type').val("");

							}

						},

						minLength: 2,

					});

			}

		});

	}

}

$(document).on('change', '.cus_purity', function (e) {

	var row = $(this).closest('tr');

	get_search_custom_metal_rates(row);

});

function get_search_custom_metal_rates(curRow) {

	var id_purity = curRow.find('.cus_purity').val();

	var id_metal = curRow.find('.metal_type').val();

	if (id_purity == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Purity..' });

	}

	else if (id_metal == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Metal..' });

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

				curRow.find('.cus_rate_field').val(data.rate_field);

				curRow.find('.cus_market_rate_field').val(data.market_rate_field);

				let rate_field = data.rate_field;

				let rate_per_grm = 0;

				if (rate_field != '') {

					rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

				}

				curRow.find('.cus_market_rate_value').val(rate_per_grm);

				calculateCustomItemSaleValue();

			}

		});

	}

}

function getSearchCustomProducts(searchTxt, curRow) {

	if (searchTxt.length >= 2) {

		my_Date = new Date();

		$.ajax({

			url: base_url + 'index.php/admin_ret_estimation/getCustomProductBySearch/?nocache=' + my_Date.getUTCSeconds(),

			dataType: "json",

			method: "POST",

			data: { 'searchTxt': searchTxt },

			success: function (data) {

				$(".cus_product").autocomplete(

					{

						source: data,

						select: function (e, i) {

							e.preventDefault();

							curRow.find('.cus_product').val(i.item.label);

							curRow.find('.cus_product_id').val(i.item.value);

							curRow.find('.tax_percentage').val(i.item.tax_percentage);

							curRow.find('.tgi_calculation').val(i.item.tgi_calculation);

							curRow.find('.cus_pcs').val(i.item.no_of_pieces);

							curRow.find('.cus_calculation_based_on').val(i.item.calculation_based_on);

							curRow.find('.metal_type').val(i.item.metal_type);

							curRow.find('.tax_group_id').val(i.item.tax_group_id);

							curRow.find('.cus_sales_mode').val(i.item.sales_mode);

							curRow.find('.scheme_closure_benefit').val(i.item.scheme_closure_benefit);

							if (i.item.sales_mode == 1) {

								if (i.item.calculation_based_on == 4) {

									curRow.find('.cus_gwt').attr("readonly", false);

									curRow.find('.cus_market_rate_value').attr("readonly", false);

								}

								else {

									curRow.find('.cus_gwt').attr("readonly", true);

									curRow.find('.cus_lwt').attr("readonly", true);

									curRow.find('.cus_mc').attr("readonly", true);

									curRow.find('.cus_wastage').attr("readonly", true);

									curRow.find('.cus_wastage_wt').attr("readonly", true);

									curRow.find('.cus_taxable_amt').attr("readonly", false);

								}

							} else {

								curRow.find('.cus_gwt').attr("readonly", false);

								curRow.find('.cus_lwt').attr("readonly", false);

								curRow.find('.cus_mc').attr("readonly", false);

								curRow.find('.cus_wastage').attr("readonly", false);

								curRow.find('.cus_wastage_wt').attr("readonly", false);

								curRow.find('.cus_taxable_amt').attr("readonly", true);

							}

							var curRowItem = i.item;

							$('#estimation_custom_details > tbody').find('tr:last td:eq(1) .cus_qty').focus();

						},

						change: function (event, ui) {

							if (ui.item === null) {

							} else {

							}

						},

						response: function (e, i) {

							// ui.content is the array that's about to be sent to the response callback.

							if (searchTxt != "") {

								if (i.content.length !== 0) {

									//console.log("content : ", i.content);

								}

							} else {

								curRow.find('td:eq(0) .cus_product').val("");

								curRow.find('td:eq(0) .cus_product_id').val("");

								curRow.find('.tax_percentage').val("");

								curRow.find('.tgi_calculation').val("");

								curRow.find('.metal_type').val("");

							}

						},

						minLength: 1,

					});

			}

		});

	}

}

function getSearchDesign(searchTxt, curRow) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getProductDesignBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'ProCode': curRow.find('.cat_pro_id').val(), 'id_branch': $('#id_branch').val(), 'is_non_tag': (curRow.find('.is_non_tag').val() == undefined ? 0 : curRow.find('.is_non_tag').val()) },

		success: function (data) {

			$(".cat_design").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						curRow.find('.cat_design').val(i.item.label);

						curRow.find('.cat_des_id').val(i.item.value);

						var curRowItem = i.item;

						var curr_used_gross = 0;

						var curr_used_pcs = 0;

						/*if(curRow.find('.is_non_tag').val()==1)

						{

							$('#estimation_catalog_details> tbody  > tr').each(function(index, tr) {

								if($(this).find('.cat_des_id').val()==i.item.value && ($(this).find('.is_non_tag').val()==1))

								{

									curr_used_gross+=parseFloat(($(this).find('.cat_gwt').val()=='' ?0 :$(this).find('.cat_gwt').val()));

									curr_used_pcs+=parseFloat(($(this).find('.cat_pcs').val()=='' ?0 :$(this).find('.cat_pcs').val()));

								}

							});

								curRow.find('.tot_blc_pcs').val(i.item.no_of_piece-curr_used_pcs);

								curRow.find('.tot_blc_gwt').val(i.item.gross_wt-curr_used_gross);

								curRow.find('.blc_pcs').html(i.item.no_of_piece-curr_used_pcs);

								curRow.find('.blc_gwt').html(i.item.gross_wt-curr_used_gross);

						}*/

						$('#estimation_catalog_details > tbody').find('tr:last td:eq(2) .cat_qty').focus();

					},

					change: function (event, ui) {

						if (curRow.find('.cat_des_id').val() == '') {

							alert('Please Enter Valid Design..');

							curRow.find('.cat_design').focus();

							curRow.find('.cat_design').val('');

						}

					},

					response: function (e, i) {

						// ui.content is the array that's about to be sent to the response callback.

						if (searchTxt != "") {

							if (i.content.length !== 0) {

								//console.log("content : ", i.content);

							}

						} else {

							curRow.find('.cat_design').val("");

							curRow.find('.cat_des_id').val("");

						}

					},

					minLength: 1,

				});

		}

	});

}

function getSearchCatSubDesign(searchTxt, curRow) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getProductSubDesignBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'ProCode': curRow.find('.cat_pro_id').val(), 'id_branch': $('#id_branch').val(), 'is_non_tag': (curRow.find('.is_non_tag').val() == undefined ? 0 : curRow.find('.is_non_tag').val()) },

		success: function (data) {

			$(".cat_sub_design").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						curRow.find('.cat_sub_design').val(i.item.label);

						curRow.find('.cat_id_sub_design').val(i.item.value);

					},

					change: function (event, ui) {

						if (ui.item === null) {

						} else {

						}

					},

					response: function (e, i) {

						// ui.content is the array that's about to be sent to the response callback.

						if (searchTxt != "") {

							if (i.content.length !== 0) {

							}

						} else {

							curRow.find('.cat_sub_design').val("");

							curRow.find('.cat_id_sub_design').val("");

						}

					},

					minLength: 1,

				});

		}

	});

}

function getSearchCusDesign(searchTxt, curRow) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getProductDesignBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'ProCode': curRow.find('.cus_product_id').val(), 'id_branch': $('#id_branch').val(), 'is_non_tag': (curRow.find('.is_non_tag').val() == undefined ? 0 : curRow.find('.is_non_tag').val()) },

		success: function (data) {

			$(".cus_design").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						curRow.find('.cus_design').val(i.item.label);

						curRow.find('.cus_des_id').val(i.item.value);

					},

					change: function (event, ui) {

						if (ui.item === null) {

						} else {

						}

					},

					response: function (e, i) {

						// ui.content is the array that's about to be sent to the response callback.

						if (searchTxt != "") {

							if (i.content.length !== 0) {

								//console.log("content : ", i.content);

							}

						} else {

							curRow.find('.cus_design').val("");

							curRow.find('.cus_des_id').val("");

						}

					},

					minLength: 1,

				});

		}

	});

}

function hide_page_open_details() {

	$(".tag_details").hide();

	$(".catalog_details").hide();

	$(".custom_details").hide();

	$(".old_matel_details").hide();

	$(".stone_details").hide();

	$(".material_details").hide();

	$(".gift_voucher_details").hide();

	//$(".chit_details").hide();

}

function create_new_empty_est_tag_row() {

	var row = "";

	let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

	var rowId = new Date().getTime();

	var select_emp = "<option value=''>-Select Employee-</option>";

	$.each(emp_details, function (pkey, emp) {

		console.log(emp_details);

		select_emp += "<option value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

	});

	row += '<tr id="' + rowId + '">'

		+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value="" placeholder="Enter tag code" required autocomplete="off"/><input class="id_mapping_details" type="hidden" name="est_tag[id_mapping_details][]" value="" /><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value="" placeholder="Enter tag code" required /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value=""/><input class="orderno" type="hidden" name="est_tag[orderno][]" value=""/><input class="rate_field" type="hidden"  value=""/><input class="market_rate_field" type="hidden"  value=""/></td>'

		+ '<td><a href="#" onClick="create_new_empty_est_tag_merge_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a></td>'

		+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_tag[item_emp_id]">' + select_emp + '</select></td>'

		+ '<td><input type="checkbox" class="partial"><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"></td>'

		+ '<td><div class="prodct_name"></div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value="" /><input type="hidden" class="metal_type"><input type="hidden" class="scheme_closure_benefit" value=""></td>'

		+ '<td><div class="design_name"></div><input type="hidden" class="design_id" name="est_tag[design_id][]" value="" /></td>'

		+ '<td><div class="sub_design_name"></div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value="" /></td>'

		+ '<td><div class="section_name"></div><input type="hidden" class="id_section" name="est_tag[id_section][]" value="" /></td>'

		+ '<td><div class="huid"></div></td>'

		+ '<td><div class="order_no"></td>'

		+ '<td><div class="purity"></div><input type="hidden" class="purity" name="est_tag[purity][]" value="" /></td>'

		+ '<td><div class="sizes"></div><input type="hidden" class="size" name="est_tag[size][]" value="" /></td>'

		+ '<td><div class="pieces"></div><input type="hidden" class="piece" name="est_tag[piece][]" value="" /></td>'

		+ '<td><input type="text" class="gwt" name="est_tag[gwt][]" value="" step="any" readonly/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=""/><input type="hidden" class="act_gwt" value=""/></td>'

		+ '<td><input type="text" class="lwt"  value="" step="any" readonly/><input type="hidden" class="lwt" name="est_tag[lwt][]" value=""/></td>'

		+ '<td><div class="nwt"></div><input type="hidden" class="tot_nwt" name="est_tag[nwt][]" value="" /><input type="hidden" class="tot_tag_nwt" /></td>'

		+ '<td><input type="text" class="market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + '  value="0" /></td>'

		+ '<td><div class="wastage" style="display:none"></div><input type="number" ' + va_readonly + ' name="est_tag[wastage][]" class="wastage_max_per" value="" min="" /></td>'

		+ '<td><div class="act_wast_wt" style="display:none"></div><input type="number" ' + va_readonly + ' name="est_tag[est_wastage_wt][]" class="est_wastage_wt" value="" min="" /></td>'

		/*	+'<td><div class="mc"></div></td>'*/

		+ '<td><select class="form-control est_mc_type" '+mc_readonly+' style="width:80px; '+mc_type_selection+'"><option value="2">Gram</option><option value="1">Piece</option></select></td>'

		+ '<td><div class="mc_val" style="display:none"></div><input class="act_mc_value"  name="est_tag[mc][]" type="number" value="" '+mc_readonly+'/><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" /></td>'

		+ '<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=""><input type="hidden" class="stone_price" name="est_tag[stone_price][]"><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" ></td></td>'

		+ '<td><div class="charge"></div></td>'

		+ '<td><div class="other_metal"></div></td>'

		+ '<td><div class="cost"></div><input type="hidden" name="est_tag[total_cgst][]" class="sale_cgst"/><input type="hidden" class="sale_sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="sale_igst" name="est_tag[total_igst][]"/><input type="hidden" class="sale_taxable_amount" name="est_tag[sale_taxable_amount][]"/><input  class="sales_value" type="hidden" name="est_tag[cost][]" value="" /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value="" /><input class="caltype" type="hidden" name="est_tag[caltype][]" value="" /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value="" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value="" /><input class="tax_group_id" type="hidden" name="est_tag[tax_percentage][]" value="" /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value="" /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value="" /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value="" /><input class="mc_value" type="hidden"   value="" /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="act_sales_value" ><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="charge_value" name="est_tag[charge_value][]"><input type="hidden" class="mc_min" ><input type="hidden" class="wastag_min" ><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" ><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" ><input type="hidden" class="sales_mode" ><input type="hidden" class="purchase_cost" name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc"  name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" name="est_tag[pur_va][]" ></td>'

		+ '<td><a href="#"onClick="remove_tag_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

		+ '</tr>';

	if ($('#estimation_tag_details > tbody  > tr').length > 0) {

		$('#estimation_tag_details > tbody > tr:first').before(row);

	} else {

		$('#estimation_tag_details tbody').append(row);

	}

	$('#estimation_tag_details > tbody').find('.item_emp_id').select2();

}

function remove_tag_row(curRow) {

	var weight_scheme_closure_type = $('#weight_scheme_closure_type').val(); //1-General,2-Based on M.C & V.A

	var chit_rate_cal_type = $('#chit_rate_cal_type').val(); 

	if(chit_rate_cal_type==2){

		if(weight_scheme_closure_type==3)

		{

			$.each(tag_split_details,function(k,tval)

			{

				if(tval.value==curRow.find('.est_tag_id').val())

				{

					tag_split_details.splice(k,1);

				}

			});

			set_tag_split_details();
			set_custom_split_details();

		}
	}

	remove_child_row(curRow);
	
	let removedId = curRow.attr('id');

	if(items.length > 0){

	items = items.filter(function (item) {
		return item.RowId != removedId;
	});

}

	curRow.remove();

	calculatetag_SaleValue();

	$('#add_new_inv').trigger('click');

}

function remove_child_row(curRow) {

	var tag_id = curRow.find('.est_tag_id').val();

	$('#estimation_tag_details > tbody  > tr').each(function (index, tr) {

		if (tag_id == $(this).find('.parent_tag_id').val()) {

			var cur_tr = $(this).closest('tr');

			cur_tr.remove();

		}

	});

}

function create_new_empty_est_order_row() {

	var row = "";

	row += '<tr>'

		+ '<td><input class="orderno" type="text"  value="" placeholder="Enter Order No" required autocomplete="off"/><input class="order_id" type="hidden" name="order[orderno][]" value="" placeholder="Enter Order No" required /></td>'

		+ '<td><div class="prodct_name"></div><input type="hidden" class="id_product" name="order[id_product][]" value="" /><input type="hidden" class="metal_type"></td>'

		+ '<td><div class="design_name"></div><input type="hidden" class="design_no" name="order[design_no][]" value="" /></td>'

		+ '<td><div class="purity"></div><input type="hidden" class="id_purity" name="order[id_purity][]" value="" /></td>'

		+ '<td><div class="sizes"></div><input type="hidden" class="size" name="order[size][]" value="" /></td>'

		+ '<td><div class="pieces"></div><input type="hidden" class="totalitems" name="order[totalitems][]" value="" /></td>'

		+ '<td><input type="text" class="weight" name="order[gwt][]" value="" disabled/><input type="hidden" class="weight" name="order[weight][]" value=""/></td>'

		+ '<td><div class="wast_percent" style="display:none"></div><input type="number" name="order[wast_percent][]" class="wastage_max_per" value="" min="" /></td>'

		+ '<td><div class="mc"></div><input type="hidden" name="order[mc][]" class="mc_value" value="" /></td>'

		+ '<td><div class="cost"></div><input type="hidden" name="order[total_cgst][]" class="cgst"/><input type="hidden" class="sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="igst" name="est_tag[total_igst][]"/><input type="hidden" class="taxable_amount" name="est_tag[taxable_amount][]"/><input type="hidden" class="stn_amt"><input type="hidden" class="item_cost" name="order[item_cost][]" ><input class="tgi_calculation" type="hidden" name="order[tgi_calculation][]" value="" /><input class="tax_percentage" type="hidden" name="order[tax_percentage][]" value="" /><input class="tax_price" type="hidden" name="order[tax_price][]" value="" /><input class="market_rate_tax" type="hidden" name="order[market_rate_tax][]" value="" /><input class="market_rate_cost" type="hidden" name="order[market_rate_cost][]" value="" /><input class="tax_group" type="hidden"  value="" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

		+ '</tr>';

	$('#estimation_order_details tbody').append(row);

}

function calc_dia_tot_amt(){
	var dia_amt = 0;
	$('#estimation_tag_details > tbody > tr').each(function(index, tr) {
		var stone_details = $(this).find('.stone_details').val();

		if (stone_details != '') {

			var st_details = JSON.parse(stone_details);
			$.each(st_details, function (k, v) {
				if (v.stones_type == 1) { // Diamond
					let dia_amt_field = v.stone_price;
					dia_amt += parseFloat(dia_amt_field) || 0;
				}
			});
		}
	});
	$('.dia_amt').text(dia_amt.toFixed(2));
}

function create_new_empty_est_catalog_row() {

	let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

	var purity = "<option value=''>-Select Purity-</option>";

	$.each(purities, function (pkey, pitem) {

		purity += "<option "+(pitem.is_default == '1' ? 'selected': '')+" value='" + pitem.id_purity + "'>" + pitem.purity + "</option>";

	});

	var row = "";

	var a = $("#catRow").val();

	var i = ++a;

	var header_emp_id = $('#id_employee').val();
	var select_emp = "<option value=''>-Select Employee-</option>";

	$.each(emp_details, function (pkey, emp) {

		console.log(emp_details);

		var emp_selected = (header_emp_id != '' && emp.id_employee == header_emp_id) ? "selected='selected'" : "";
		select_emp += "<option " + emp_selected + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

	});

	$("#catRow").val(i);

	row += '<tr id=' + i + '>'

		+ '<td><input type="checkbox" class="non_tag" checked disabled><input type="hidden" class="is_non_tag" name="est_catalog[is_non_tag][]" value="1" ><input type="hidden" class="cat_rate_field" name="est_catalog[rate_field][]"><input type="hidden" class="cat_market_rate_field" name="est_catalog[market_rate_field][]"><input type="hidden" class="is_chttopup_applied" value="0"><input type="hidden" class="is_sr_applied" value="0"><input type="hidden" class="ChitUtilizedDetails"/><input type="hidden" class="SRUtilizedDetails"/><input type="hidden" class="partialy_utilized" value="0"/></td>'

		+ '<td><select class="form-control cat_section" name="est_catalog[id_section][]" value="" placeholder="Search Section" style="width:150px;"><input type="hidden" class="cat_section_id"  value="" /></td>'

		/*+'<td><input type="text" class="form-control cat_product" name="est_catalog[product][]" value="" placeholder="Search Product" autocomplete="off" required style="width:80px;"/><input type="hidden" class="cat_pro_id" name="est_catalog[pro_id][]" value="" /><input type="hidden" class="tax_percentage" value=""><input type="hidden" class="tax_group_id" value=""><input type="hidden" class="tgi_calculation" value=""><input type="hidden" class="metal_type" value=""><input type="hidden" class="scheme_closure_benefit" value=""><input type="hidden" class="cat_sales_mode" value=""></td>'

		+'<td><input type="text" class="form-control cat_design" name="est_catalog[design][]" value="" placeholder="Search Design" autocomplete="off" required style="width:80px;"/><input type="hidden" class="cat_des_id" name="est_catalog[des_id][]" value="" /></td>'*/

		+ '<td><select class="form-control cat_product" name="est_catalog[product][]" value="" placeholder="Search Product" style="width:150px;"><input type="hidden" class="cat_pro_id" name="est_catalog[pro_id][]" value="" /><input type="hidden" class="tax_percentage" value=""><input type="hidden" class="tax_group_id" value=""><input type="hidden" class="tgi_calculation" value=""><input type="hidden" class="metal_type" value=""><input type="hidden" class="scheme_closure_benefit" value=""><input type="hidden" class="cat_sales_mode" value=""></td>'

		+ '<td><select class="form-control cat_design" name="est_catalog[design][]" value="" placeholder="Search Design" style="width:150px;"><input type="hidden" class="cat_des_id" name="est_catalog[des_id][]" value="" /></td>'

		+ '<td><select class="form-control cat_sub_design" name="est_catalog[id_sub_design][]" value="" placeholder="Search SubDesign" required style="width:150px;"></select><input type="hidden" class="cat_id_sub_design"  value="" /></td>'

		+ '<td><select class=" form-control item_emp_id" style="width:100px !important" name="est_catalog[item_emp_id][]" >' + select_emp + '</select></td>'

		+ '<td><select class="form-control cat_purity" name="est_catalog[purity][]" style="width:100px;">' + purity + '</select></td>'

		+ '<td><input type="number" class="form-control cat_size" name="est_catalog[size][]" value="" style="width:80px;" placeholder="Enter Size"/></td>'

		+ '<td><input type="number" class="form-control cat_pcs" name="est_catalog[pcs][]" value="1" autocomplete="off" style="width:80px;"  placeholder="Enter Pcs"/>Stock :<span class="blc_pcs" ></span><input type="hidden" class="tot_blc_pcs"></td>'

		+ '<td><input type="number"  class="form-control cat_gwt" name="est_catalog[gwt][]" value="" autocomplete="off" style="width:80px;  placeholder="Enter GWT""/><span class="blc_gwt" ></span><input type="hidden" class="tot_blc_gwt"></td>'

		+ '<td><div class="form-group"><div class="input-group "><input class="form-control cat_lwt" onclick="create_new_empty_est_cat_stone_item($(this).closest(\'tr\'));" type="number" name="est_catalog[lwt][]" step="any" readonly="" style="width:100px;"><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cat_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'

		+ '<td><input type="number" class="form-control tot_stn_amt" name="est_catalog[stone_amt][]" value="0.00" readonly style="width:100px;" /></td>'

		+ '<td><input type="number" class="form-control tot_dia_amt" name="est_catalog[diamond_amt][]" value="0.00" readonly style="width:100px;" /></td>'

		//+'<td><input type="number" class="form-control cat_lwt" name="est_catalog[lwt][]" value="" autocomplete="off" style="width:80px;"  placeholder="Enter LWT"/></td>'

		+ '<td><input type="number" class="form-control cat_nwt" name="est_catalog[nwt][]" value="" autocomplete="off" readonly style="width:80px;"></td>'

		+ '<td><input class="form-control cat_market_rate_value" name="est_catalog[est_rate_per_grm][]" type="text" value="" style="width:80px;" ' + rate_readonly + '  /></td>'

		+ '<td><select class="form-control mc_type" '+mc_readonly+' style="width:80px;'+mc_type_selection+'"><option value="2">Gram</option><option value="1">Piece</option><option value="3">% on Price</option></select></select><input type="hidden" value="2" name="est_catalog[id_mc_type][]" class="id_mc_type"></td>'

		+ '<td><input type="number" '+mc_readonly+' class="form-control cat_mc" name="est_catalog[mc][]" value="" style="width:80px;"  placeholder="Enter MC"/><input type="hidden" value="" name="est_catalog[max_mc][]" class="nn_cat_mc"></td>'

		+ '<td><input type="number"  class="form-control cat_act_va_per"  value="" name="est_catalog[max_va_per][]" style="width:80px;" readonly placeholder="VA in %"/><input type="hidden" class="cat_pro_va_per"></td>'

		+ '<td><input type="number" ' + va_readonly + ' class="form-control cat_wastage" name="est_catalog[wastage][]" value="" style="width:80px;"  placeholder="VA in %"/><input type="hidden" class="id_wastage_slab" name="est_catalog[id_wastage_slab][]" ><input type="hidden" class="act_va_per" name="est_catalog[act_va_per][]"><input type="hidden" class="slab_details"><input type="hidden" class="wast_slb_val" name="est_catalog[wast_slab_val][]"></td>'

		+ '<td><input type="number" ' + va_readonly + ' class="form-control cat_wastage_wt" value="" style="width:80px;"  placeholder="Wt"/></td>'

		+ '<td><a href="#" onClick="create_new_empty_est_cus_charges_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="charges_details" name="est_catalog[charges_details][]"><input type="hidden" class="value_charge" name="est_catalog[value_charge][]"></td>'

		+ '<td><input type="number" class="form-control cat_taxable_amt" name="est_catalog[taxable_amt][]" readonly style="width:100px;"></td>'

		+ '<td><input type="number"  class="form-control cat_tax_per" name="est_catalog[tax_per][]" value="" readonly style="width:80px;"/></td>'

		+ '<td><input type="number"  class="form-control cat_tax_price" name="est_catalog[tax_price][]" value="" readonly style="width:80px;"/><input type="hidden" name="est_catalog[total_cgst][]" class="cat_cgst"/><input type="hidden" class="cat_sgst" name="est_catalog[total_sgst][]"/><input type="hidden" class="cat_igst" name="est_catalog[total_igst][]"/><input type="hidden" class="cat_taxable_amount" name="est_catalog[sale_taxable_amount][]"/></td>'

		//+'<td><a href="#" onClick="create_new_empty_est_cat_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a></td>'

		+ '<td><input type="number" class="form-control cat_amt" name="est_catalog[amount][]" value="" readonly style="width:100px;" /><input type="hidden" class="cat_calculation_based_on" name="est_catalog[calculation_based_on][]" value="" /><input type="hidden" id="stone_details" class="stone_details" name="est_catalog[stone_details][]"><input type="hidden" id="material_details" class="material_details" name="est_catalog[material_details][]"><input type="hidden" value="0" class="stone_price" id="stone_price"><input type="hidden" value="0" class="material_price" id="material_price"><input type="hidden" class="market_rate_cost" name="est_catalog[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_catalog[market_rate_tax][]"><input type="hidden" class="sales_mode" ><input type="hidden" class="purchase_cost" name="est_catalog[purchase_cost][]" ><input type="hidden" class="pur_mc"  name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" name="est_tag[pur_va][]" ></td>'

		+ '<td><a href="#" onClick="removeCat_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'

		+ '</tr>';

	$('#estimation_catalog_details tbody').append(row);

	$('#estimation_catalog_details > tbody').find('tr:last td:eq(0) .cat_product').focus();

	$('.cat_product').append(

		$("<option></option>")

			.attr("value", "")

			.text('-Choose-')

	);

	$('.cat_section').append(

		$("<option></option>")

			.attr("value", "")

			.text('Section')

	);

	$.each(section_details, function (key, item) {

		if ($('#id_branch').val() == item.id_branch && item.is_home_bill_counter != 1) {

			$(".cat_section").append(

				$("<option></option>")

					.attr("value", item.id_section)

					.text(item.section_name)

			);

		}

	});

	$.each(cat_product_details, function (key, item) {

		$('.cat_product').append(

			$("<option></option>")

				.attr("value", item.pro_id)

				.text(item.product_name)

		);

	});

	$('#estimation_catalog_details > tbody').find('.cat_section').select2();

	$('#estimation_catalog_details > tbody').find('.cat_product').select2();

	$('#estimation_catalog_details > tbody').find('.cat_design').select2();

	$('#estimation_catalog_details > tbody').find('.item_emp_id').select2();

	$('#estimation_catalog_details > tbody').find('.cat_sub_design').select2();

	$('#estimation_catalog_details > tbody').find('.cat_section').select2({

		placeholder: "Section",

		allowClear: false

	});

	$('#estimation_catalog_details > tbody').find('.cat_product').select2({

		placeholder: "Product",

		allowClear: false

	});

	$('#estimation_catalog_details > tbody').find('.cat_design').select2({

		placeholder: "Design",

		allowClear: false

	});

	$('#estimation_catalog_details > tbody').find('.cat_sub_design').select2({

		placeholder: "Sub Design",

		allowClear: true

	});

	var disc_limit = $('#disc_limit').val();

	if (disc_limit == '') {

		$('#estimation_catalog_details > tbody').find('.cat_dis').attr('disabled', 'disabled');

	}

}

function create_new_empty_est_custom_row() {

	let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

	var purity = "<option value=''>-Select Purity-</option>";

	$.each(purities, function (pkey, pitem) {

		purity +="<option "+(pitem.is_default == '1' ? 'selected': '')+" value='" + pitem.id_purity + "'>" + pitem.purity + "</option>";

	});

	var row = "";

	var a = $("#cusRow").val();

	var i = ++a;

	var header_emp_id = $('#id_employee').val();
	var lastrowemp = $('#estimation_custom_details > tbody').find('tr:last td .item_emp_id').val();
	var default_emp_id = (lastrowemp && lastrowemp != '') ? lastrowemp : header_emp_id;
	var select_emp = "<option value=''> Select Employee </option>";

	$.each(emp_details, function (pkey, emp) {

		console.log(emp_details);

		var emp_selected = (default_emp_id != '' && emp.id_employee == default_emp_id) ? "selected='selected'" : "";
		select_emp += "<option " + emp_selected + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

	});

	$("#cusRow").val(i);

	row += '<tr id=cus' + i + '>'

		+ '<td><input class="form-control cus_tag_name" type="text" name="est_custom[tag_name][]" value="" placeholder="Enter tag code" required autocomplete="off" style="width:80px"/><input class="est_tag_id" type="hidden" name="est_custom[tag_id][]" value="" placeholder="Enter tag code" required /><input type="hidden" class="is_partial"  name="est_custom[is_partial][]" value="0"><input type="hidden" class="cus_rate_field"  name="est_custom[rate_field][]" ><input type="hidden" class="cus_market_rate_field"  name="est_custom[market_rate_field][]" ><input type="hidden" class="is_chttopup_applied" value="0"><input type="hidden" class="is_sr_applied" value="0"><input type="hidden" class="ChitUtilizedDetails"/><input type="hidden" class="SRUtilizedDetails"/><input type="hidden" class="partialy_utilized" value="0"/></td>'

		+ '<td><input class="form-control cus_old_tag_code" type="text" name="est_custom[old_tag_code][]" value="" placeholder="Enter Old tag code" required autocomplete="off" style="width:80px"/></td>'

		/*+'<td><input type="text" name="est_custom[product][]" value="" class="form-control cus_product" placeholder="Search Product" required style="width:80px;" autocomplete="off"/><input class="cus_product_id" type="hidden" name="est_custom[pro_id][]" value="" /><input type="hidden" class="tax_group_id" value=""><input type="hidden" class="tax_percentage" value=""><input type="hidden" class="tgi_calculation" value=""><input type="hidden" class="metal_type" value=""><input type="hidden" class="scheme_closure_benefit" value=""><input type="hidden" class="cus_sales_mode" value=""></td>'

		+'<td><input type="text" class="form-control cus_design" name="est_custom[design][]" value="" placeholder="Search Design" required style="width:80px;" autocomplete="off"/><input type="hidden" class="cus_des_id" name="est_custom[des_id][]" value="" /></td>'

		+'<td><input type="text" class="form-control cus_sub_design" name="est_custom[sub_design][]" value="" placeholder="Search SubDesign" required style="width:80px;" autocomplete="off"/><input type="hidden" class="cus_id_sub_design" name="est_custom[id_sub_design][]" value="" /></td>'*/

		+ '<td><select class="form-control cus_section" name="est_custom[id_section][]" value="" placeholder="Search Section" style="width:150px;"><input type="hidden" class="cus_section_id"  value="" /></td>'

		+ '<td><select class="form-control cus_product"name="est_custom[product][]" style="width:150px; placeholder="Search Product""></select><input class="cus_product_id" type="hidden" name="est_custom[pro_id][]" value="" /><input type="hidden" class="tax_group_id" value=""><input type="hidden" class="tax_percentage" value=""><input type="hidden" class="tgi_calculation" value=""><input type="hidden" class="metal_type" value=""><input type="hidden" class="cat_id" value=""><input type="hidden" class="scheme_closure_benefit" value=""><input type="hidden" class="cus_sales_mode" value=""></td>'

		+ '<td><select class="form-control cus_design" name="est_custom[design][]" value="" placeholder="Search Design" required style="width:150px;"></select><input type="hidden" class="cus_des_id" name="est_custom[des_id][]" value="" /></td>'

		+ '<td><select class="form-control cus_sub_design" name="est_custom[sub_design][]" value="" placeholder="Search SubDesign" required style="width:150px;"></select><input type="hidden" class="cus_id_sub_design" name="est_custom[id_sub_design][]" value="" /></td>'

		+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_custom[item_emp_id][]" >' + select_emp + '</select></td>'

		+ '<td><select class="form-control cus_purity" name="est_custom[purity][]" style="width:80px;">' + purity + '</select></td>'

		+ '<td><input type="number" class="form-control cus_size" name="est_custom[size][]" placeholder="Enter Size" value="" style="width:80px;"/></td>'

		+ '<td><input class="form-control cus_pcs" type="number" name="est_custom[pcs][]" placeholder="Enter Pcs" value="1" style="width:80px;"/></td>'

		+ '<td><input type="number" class="form-control cus_gwt" name="est_custom[gwt][]"  placeholder="Enter GWT"style="width:80px;"/><input type="hidden" class="form-control blc_tag_gwt"/></td>'

		//	+'<td><input class="form-control cus_lwt" type="number" name="est_custom[lwt][]"  placeholder="Enter NWT" style="width:80px;"/></td>'

		+ '<td><div class="form-group"><div class="input-group "><input class="form-control cus_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="est_custom[lwt][]" step="any" readonly="" style="width:100px;"><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" name="est_custom[stone_details][]" value=""><input type="hidden" class="stone_price" name="est_custom[stone_price][]"></div></div></td>'

		+ '<td><input type="number" class="form-control tot_stn_amt" name="est_custom[stone_amt][]" value="" readonly style="width:100px;" /></td>'

		+ '<td><input type="number" class="form-control tot_dia_amt" name="est_custom[diamond_amt][]" value="" readonly style="width:100px;" /></td>'

		+ '<td><input type="number" class="form-control cus_nwt" name="est_custom[nwt][]"  readonly style="width:80px;"/></td>'

		+ '<td><input class="form-control cus_market_rate_value" name="est_custom[est_rate_per_grm][]" type="text" value="" style="width:80px;" ' + rate_readonly + '  /></td>'

		+ '<td><select class="form-control cus_mc_type" '+mc_readonly+' style="width:80px; '+mc_type_selection+'"><option value="2">Gram</option><option value="1">Piece</option><option value="3">% on Price</option></select><input type="hidden" value="2" name="est_custom[id_mc_type][]" class="id_mc_type"></td>'

		+ '<td><input type="number" '+mc_readonly+' class="form-control cus_mc" name="est_custom[mc][]" value="" placeholder="MC" style="width:80px;"/><input type="hidden" value="" name="est_custom[max_mc][]" class="HB_cus_mc"></td>'

		+ '<td><input class="form-control cus_act_va_per" type="number"readonly  placeholder="VA in %" name="est_custom[max_va_per][]" value="" style="width:80px;"/><input type="hidden" class="cus_pro_va_per"></td>'

		+ '<td><input class="form-control cus_wastage" ' + va_readonly + ' type="number" name="est_custom[wastage][]" placeholder="VA in %" value="" style="width:80px;"/><input type="hidden" class="id_wastage_slab" name="est_custom[id_wastage_slab][]" ><input type="hidden" class="act_va_per" name="est_custom[act_va_per][]"><input type="hidden" class="wast_slb_val" name="est_custom[wast_slab_val][]"><input type="hidden" class="slab_details"></td>'

		+ '<td><input class="form-control cus_wastage_wt" ' + va_readonly + ' type="number" placeholder="Wt" value="" style="width:80px;"/></td>'

		+ '<td><a href="#" onClick="create_new_empty_est_cus_charges_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="charges_details" name="est_custom[charges_details][]"><input type="hidden" class="value_charge" name="est_custom[value_charge][]"></td>'

		+ '<td><input type="number" class="form-control cus_taxable_amt" name="est_custom[taxable_amt][]" readonly="" value="" style="width:80px;"></td>'

		+ '<td><input type="number" class="form-control cus_tax_price" name="est_custom[tax_price][]" value="" readonly="" style="width:80px;">  <input type="hidden" name="est_custom[total_cgst][]" class="cus_cgst"/><input type="hidden" class="cus_sgst" name="est_custom[total_sgst][]"/><input type="hidden" class="cus_igst" name="est_custom[total_igst][]"/><input type="hidden" class="cus_taxable_amount" name="est_custom[sale_taxable_amount][]"/></td>'

		//+'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_custom[stone_details][]" value=""><input type="hidden" class="stone_price" name="est_custom[stone_price][]"></td>'

		+ '<td><input class="form-control cus_amount" type="number" name="est_custom[amount][]" value="" readonly style="width:100px;"/><input type="hidden" class="cus_calculation_based_on" name="est_custom[calculation_based_on][]" value="" /><input type="hidden" id="material_details" class="material_details" name="est_custom[material_details][]"><input type="hidden" value="0" class="stone_price" id="stone_price"><input type="hidden" value="0" class="material_price" id="material_price"><input type="hidden" class="market_rate_cost" name="est_custom[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_custom[market_rate_tax][]"></td>'

		+ '<td><a href="#" onClick="removeCat_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'

		+ '</tr>';

	$('#estimation_custom_details tbody').append(row);

	$('.cus_product').append(

		$("<option></option>")

			.attr("value", "")

			.text('-Choose-')

	);

	$.each(section_details, function (key, pitem) {

		if ($('#id_branch').val() == pitem.id_branch) {

			if (pitem.is_home_bill_counter == 1) {

				$('.cus_section').append(

					$("<option></option>")

						.attr("value", pitem.id_section)

						.text(pitem.section_name)

				);

			}

		}

	});

	$.each(cus_product_details, function (key, item) {

		$('.cus_product').append(

			$("<option></option>")

				.attr("value", item.pro_id)

				.text(item.product_name)

		);

	});

	$('#estimation_custom_details > tbody').find('.cus_section').select2();

	$('#estimation_custom_details > tbody tr:last').find('.cus_section').select2("val", "");

	$('#estimation_custom_details > tbody').find('.cus_product').select2();

	$('#estimation_custom_details > tbody').find('.item_emp_id').select2();

	$('#estimation_custom_details > tbody').find('.cus_design').select2();

	$('#estimation_custom_details > tbody').find('.cus_sub_design').select2();

	$('#estimation_custom_details > tbody').find('.cus_section').select2({

		placeholder: "Section",

		allowClear: true

	});

	$('#estimation_custom_details > tbody').find('.cus_product').select2({

		placeholder: "Product",

		allowClear: true

	});

	$('#estimation_custom_details > tbody').find('.cus_design').select2({

		placeholder: "Design",

		allowClear: true

	});

	$('#estimation_custom_details > tbody').find('.cus_sub_design').select2({

		placeholder: "Sub Design",

		allowClear: true

	});

	$('#estimation_custom_details > tbody').find('.cus_product').focus();

	var disc_limit = $('#disc_limit').val();

	if (disc_limit == '') {

		$('#estimation_custom_details > tbody').find('.cus_dis').attr('disabled', 'disabled');

	}

}

function create_new_empty_est_oldmatel_row() {

	var purity = "<option value=''>-Select Purity-</option>";

	$.each(purities, function (pkey, pitem) {

		purity += "<option value='" + pitem.id_purity + "'>" + pitem.purity + "</option>";

	});

	var matelTupes = "<option value=''>- Select Metal-</option>";

	var allowed_old_met_pur = $('#allowed_old_met_pur').val();

	/* Dynamic Metal Loading from Database*/
	if (metal_master && metal_master.length > 0) {

		if (allowed_old_met_pur == 1) {	// All Metals
			matel_types = metal_master;

		} else if (allowed_old_met_pur == 2) {	// Only Gold 
			matel_types = metal_master.filter(function(m) {
				return m.metal && m.metal.toLowerCase() === 'gold';
			});
		} else if (allowed_old_met_pur == 3) {	// Only Silver 
			matel_types = metal_master.filter(function(m) {
				return m.metal && m.metal.toLowerCase() === 'silver';
			});
		} else {	// Default: All metals
			matel_types = metal_master;
		}
	} else {
		// console.warn('Metal master data not loaded. Old metal dropdown may be empty.');
		matel_types = [];
	}

	$.each(matel_types, function (mkey, mitem) {

		matelTupes += "<option value='" + mitem.id_metal + "'>" + mitem.metal + "</option>";

	});

	var metal_types = "<option value=''>- Select Metal Type-</option>";

	var old_metal_cat = "<option value=''>- Select -</option>";

	var row = "";

	var a = $("#oldMRow").val();

	var i = ++a;

	$("#oldMRow").val(i);

	row += '<tr id=oldM' + i + '>'

		+ '<td><select class="old_id_category"  name="est_oldmatel[id_category][]" value="">' + matelTupes + '</select></td>'

		+ '<td><select class="old_metal_type"  name="est_oldmatel[id_old_metal_type][]" value="">' + metal_types + '</select></td>'

		+ '<td><select class="old_metal_category"  name="est_oldmatel[id_old_metal_category][]" value="" disabled>' + old_metal_cat + '</select></td>'

		+ '<td><select class="old_metal_product" name="est_oldmatel[old_metal_product][]" style="width:180px;" value=""></select><input type="hidden" class="old_metal_prod_id"  name="est_oldmatel[old_metal_prod_id][]"></td>'

		+ '<td><input type="number" class="form-control old_pcs" name="est_oldmatel[pcs][]" value="1" style="width: 100px;" min="0"/></td>'

		+ '<td><input type="number" class="form-control old_gwt" name="est_oldmatel[gwt][]" value="" style="width: 86px;" min="0"/></td>'

		+ '<td><input class="form-control old_dwt" type="number" name="est_oldmatel[dwt][]" value="" style="width: 86px;"/></td>'

		+ '<td><input type="number" class="form-control old_swt" name="est_oldmatel[swt][]" value="" style="width: 86px;" readonly /></td>'

		+ '<td><input class="form-control old_wastage" type="number" name="est_oldmatel[wastage][]" step="any" value="" style="width: 86px;" min="0"/></td>'

		+ '<td><input class="form-control old_wastage_wt" type="number" name="est_oldmatel[wastage_wt][]" step="any" value="" style="width: 86px;"/></td>'

		+ '<td><input type="number" class="form-control old_nwt" name="est_oldmatel[nwt][]" value="" readonly style="width: 86px;"/></td>'
		
		+ '<td><input type="number" class="form-control old_purity" name="est_oldmatel[purity][]" value="" style="width: 86px;"/></td>'


		+ '<td><input type="number" class="form-control old_rate" name="est_oldmatel[rate][]" value="" style="width: 100px;"/></td>'

		+ '<td><input type="number" class="form-control old_touch" name="est_oldmatel[touch][]" value="100" style="width: 86px;"/></td>'

		+ '<td><a href="#" onClick="create_new_empty_est_old_metal_stone($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a></td>'

		+ '<td><select class="purpose"><option value="1">Cash</option><option value="2" selected>Exchange</option></select><input type="hidden" value="" name="est_oldmatel[id_purpose][]" class="id_purpose"></td>'

		+ '<td><textarea class="form-control old_metal_remarks" style="width:217px;height:35px;" name="est_oldmatel[old_metal_remarks][]" placeholder="Enter Remarks"  rows="5" cols="20" value=""></textarea></td>'

		+ '<td><input class="form-control old_amount" type="number" name="est_oldmatel[amount][]" value="" style="width: 100px;"/><input type="hidden" id="stone_details" class="stone_details" name="est_oldmatel[stone_details][]" value=""><input type="hidden" value="0" class="stone_price" id="stone_price"><input type="hidden" id="stone_wt" class="stone_wt" value="0"></td>'

		+ '<td><a href="#" onClick="remove_old_metal_row($(this).closest(\'tr\').remove());" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

		+ '</tr>';

	$('#estimation_old_matel_details tbody').append(row);

	$('#estimation_old_matel_details > tbody').find('.old_id_category').focus();

	$('#estimation_old_matel_details > tbody').find('.purpose').select2();

	$('#estimation_old_matel_details > tbody').find('.old_metal_category').select2();

	$('#estimation_old_matel_details > tbody').find('.old_metal_type').select2();

	$('#estimation_old_matel_details > tbody').find('.old_id_category').select2();

	$('#estimation_old_matel_details > tbody').find('.purpose').select2({

		placeholder: "Purpose",

		allowClear: true

	});

	$('#estimation_old_matel_details > tbody').find('.old_metal_category').select2({

		placeholder: "Category",

		allowClear: true

	}).prop('disabled', true);

	$('#estimation_old_matel_details > tbody').find('.old_metal_type').select2({

		placeholder: "Type",

		allowClear: true

	});

		$('#estimation_old_matel_details > tbody').find('.old_metal_product').select2({

		placeholder: "Select Product",

		allowClear: true

	});

	/*	$('#estimation_old_matel_details > tbody').find('.old_id_category').select2({

			placeholder: "Metal",

			allowClear: true

		});*/

	var id_purpose = 2;

	var old_id_category = $('#estimation_old_matel_details > tbody').find('.old_id_category').val();

	$('#estimation_old_matel_details > tbody').find('.purpose').select2("val", (id_purpose != '' && id_purpose > 0 ? id_purpose : ''));

	//$('#estimation_old_matel_details > tbody').find('.old_id_category').select2("val","");

}

function remove_old_metal_row(curRow) {

	curRow.remove();

	calculate_purchase_details();

	calculate_sales_details();

}

function create_new_empty_est_stone_row() {

	var stones_list = "<option value=''>-Select Stone-</option>";

	$.each(stones, function (pkey, pitem) {

		stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

	});

	var row = "";

	row += '<tr><td><select class="stone_id" name="est_stones[stone_id][]">' + stones_list + '</select></td><td><input type="number" class="stone_pcs" name="est_stones[stone_pcs][]" value="" /></td><td><input class="stone_wt" type="number" name="est_stones[stone_wt][]" value="" /></td><td><input type="number" class="stone_price" name="est_stones[stone_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

	$('#estimation_stone_details tbody').append(row);

	$('#estimation_stone_details > tbody').find('tr:last td:eq(0) .stone_id').focus();

}

function create_new_empty_est_material_row() {

	var material_list = "<option value=''> - Select Material - </option>";

	$.each(materials, function (pkey, pitem) {

		material_list += "<option value='" + pitem.material_id + "'>" + pitem.material_name + "</option>";

	});

	var row = "";

	row += '<tr><td><select class="material_id" name="est_materials[material_id][]">' + material_list + '</select></td><td><input class="material_wt" type="number" name="est_materials[material_wt][]" value="" /></td><td><input type="number" class="material_price" name="est_materials[material_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

	$('#estimation_material_details tbody').append(row);

	$('#estimation_material_details > tbody').find('tr:last td:eq(0) .material_id').focus();

}

function create_new_empty_est_voucher_row() {

	var row = "";

	row += '<tr><td><input class="voucher_no" type="number" name="gift_voucher[voucher_no][]" value="" /></td><td></td><td><input type="number" class="gift_voucher_amt" name="gift_voucher[gift_voucher_amt][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

	$('#estimation_gift_voucher_details tbody').append(row);

	$('#estimation_gift_voucher_details > tbody').find('tr:last td:eq(0) .voucher_no').focus();

}

function create_new_empty_est_chit_row() {

	var row = "";

	row += '<tr>'

		+ '<td><div class="input-group"><input class="form-control scheme_account_id" type="number" name="chit_uti[scheme_account_id][]" value="" /><span class="input-group-btn"><button type="button" id="scheme_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button></span></div><input type="hidden" name="chit_uti[id_scheme_account][]" class="form-control id_scheme_account" id="id_scheme_account"><input type="hidden" name="chit_uti[bonus_percent][]" class="form-control bonus_percent" id="bonus_percent"><input type="hidden" class="form-control scheme_type" id="scheme_type"><input type="hidden" class="form-control closing_amount"><input type="hidden" class="form-control closing_weight"><input type="hidden" name="chit_uti[paid_installments][]" class="form-control paid_installments" ><input type="hidden" name="chit_uti[total_installments][]" class="form-control total_installments" ><input type="hidden" name="chit_uti[closing_add_chgs][]" class="form-control closing_add_chgs" ><input type="hidden" name="chit_uti[additional_benefits][]" class="form-control additional_benefits"></td>' + '<td><input type="number" class="form-control chit_amt" name="chit_uti[chit_amt][]" value=""  readonly /><input type="hidden" name="chit_uti[wastage_per][]" class="form-control wastage_per"><input type="hidden" name="chit_uti[mc_value][]" class="form-control mc_value"><input type="hidden" name="chit_uti[savings_in_wastage][]" class="form-control savings_in_wastage"><input type="hidden" name="chit_uti[savings_in_mcvalue][]" class="form-control savings_in_mcvalue"><input type="hidden" name="chit_uti[closing_weight][]" class="form-control closing_weight"><input type="hidden" name="chit_uti[is_wast_and_mc_benefit_apply][]" class="form-control is_wast_and_mc_benefit_apply"><input type="hidden" name="chit_uti[rate_per_gram][]" class="form-control rate_per_gram"><input type="hidden" class="form-control topup_scheme"><input type="hidden" name="chit_uti[rate_type][]" class="form-control rate_type" id="rate_type"></td>'

		+ '<td><span class="saved_weight"></span><input type="hidden" class="form-control chit_rate"/></td>'

		+ '<td><a href="#" onClick="remove_chit_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

	$('#estimation_chit_details tbody').append(row);
	
	var lastRow = $('#estimation_chit_details > tbody > tr:last');

	var uniqueId = 'chit_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

    lastRow.attr('data-chit-id', uniqueId);

	$('#estimation_chit_details > tbody').find('tr:last td:eq(0) .scheme_account_id').focus();

}

$(document).on('change','.item_emp_id',function(){

	// if(this.value!='')

	// {

	// 	$('#emp_select').select2("val",this.value);

	// }

	let curRow = $(this).closest("tr");

	let cur_emp_id = this.value;

	if (curRow.find('#child_tag_details').val() != '' && curRow.find('#child_tag_details').val() != undefined) {

		console.log(curRow.find('#child_tag_details').val());

		var child_tag_details = JSON.parse(curRow.find('#child_tag_details').val())

		$.each(child_tag_details, function (idx, obj) {

			var child_tag_id = obj.tag_id;

			$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

				if ($(this).find('.est_tag_id').val() == child_tag_id) {

					console.log($(this).find('.item_emp_id').val());

					$(this).find('.item_emp_id').select2();

					$(this).find('.item_emp_id').select2("val", (cur_emp_id != '' ? cur_emp_id : ''));

				}

			});

		});

	}

});

function validateTagDetailRow() {

	var row_validate = true;

	let is_weight_scheme = check_is_weight_scheme();

	$('#estimation_tag_details > tbody  > tr').each(function (index, tr) {

		let curRow = $(this).closest("tr");

		let tag_code = $(this).find('td:first .est_tag_name').val();

		let id_orderdetails = curRow.find('.id_orderdetails').val();

		let is_order_rate = $('input[name="order_rate_type"]:checked').val();

		let pro_id = $(this).find('.pro_id').val();

		let design_id = $(this).find('.design_id').val();

		let id_sub_design = $(this).find('.id_sub_design').val();

		let rate = isNaN(curRow.find(".market_rate_value").val()) || $.trim(curRow.find(".market_rate_value").val()) == "" ? 0 : parseFloat(curRow.find(".market_rate_value").val());

		let calculation_type = (isNaN(curRow.find('.caltype').val()) || curRow.find('.caltype').val() == '') ? 0 : curRow.find('.caltype').val();

		let gwt = $(this).find('.gwt').val();

		let nwt = $(this).find('.tot_nwt').val();

		let wastage_max_per = $(this).find('.wastage_max_per').val();

		let mc_cal_type = $(this).find('.est_mc_type').val();

		let act_mc_value = $(this).find('.act_mc_value').val();

		let sales_mode = $(this).find('.sales_mode').val();



		let pur_mc = $(this).find('.pur_mc').val();

		pur_mc = isNaN(pur_mc) || pur_mc == "" ? 0 : pur_mc;

		let pur_va = $(this).find('.pur_va').val();

		pur_va = isNaN(pur_va) || pur_va == "" ? 0 : pur_va;



		let mc_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt, rate, calculation_type);



		console.log("mc_va_limit", mc_va_limit);



		let min_mc = mc_va_limit.mc_min;

		let min_va = mc_va_limit.va_min;

		min_mc = parseFloat(min_mc) > parseFloat(pur_mc) ? min_mc : pur_mc;

		min_va = parseFloat(min_va) > parseFloat(pur_va) ? min_va : pur_va;



		let min_mc_cal_type = mc_va_limit.mc_cal_type;



		let margin_mrp = mc_va_limit.margin_mrp;



		let rate_field = curRow.find(".rate_field").val();

		let metal_type = curRow.find(".metal_type").val();

		let cal_type = curRow.find(".caltype").val();



		let pcs_tag = isNaN($(this).find('.pcs').val()) || $(this).find('.pcs').val() == "" ? 1 : $(this).find('.pcs').val();

		let total_mc_va = calculate_total_mc_va(rate, cal_type, gwt, nwt, wastage_max_per, mc_cal_type, act_mc_value, pcs_tag);

		let total_min_mc_va = calculate_total_mc_va(rate, cal_type, gwt, nwt, min_va, min_mc_cal_type, min_mc, pcs_tag);

		var is_eda_est = $('#iseda').val();

		if(curRow.find('.stone_type').val()==0) // for Ornaments Product

		{

			if(id_orderdetails || (curRow.find('.is_sr_applied').val() == 1 || curRow.find('.is_chttopup_applied').val() == 1) || ((curRow.find('.is_sr_applied').val() == 0 || curRow.find('.is_chttopup_applied').val() == 0) && (curRow.find('.partialy_utilized').val() == 1))){



				var returnStatus            = { status :true ,message:'' ,rate_per_grm: rate };



			}else{

				var returnStatus            = check_rate_is_valid(rate, rate_field, metal_type, cal_type);



			}

		}

		else

		{

			var returnStatus = check_min_max_rate(curRow);

		}

		if (tag_code == "") {

			row_validate = false;

		}

		else if ($(this).find('.gwt').val() < 0) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Gross Weight' });

			row_validate = false;

			return false;

		}

		else if ($(this).find('.tot_nwt').val() < 0 && (tag_details.length == 0)) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Net Weight' });

			row_validate = false;

			return false;

		}

		else if (parseFloat($(this).find('.tot_nwt').val()) > parseFloat($(this).find('.gwt').val()) && (tag_details.length == 0)) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Net Weight' });

			row_validate = false;

			return false;

		}

		else if (parseFloat($(this).find('.tot_nwt').val()) > parseFloat($(this).find('.tot_tag_nwt').val()) && (tag_details.length == 0)) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Net Weight' });

			row_validate = false;

			return false;

		}

		else if ((parseFloat($(this).find('.tot_nwt').val()) != parseFloat($(this).find('.tot_tag_nwt').val())) && $(this).find('.is_partial').val() == 0 && (tag_details.length ==0 ) ) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid Net Weight' });

			row_validate = false;

			return false;

		}

		else if ((($(this).find('.tax_price').val())==0 || $(this).find('.tax_price').val()<0) && is_eda_est == 0 ) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid GST' });

			row_validate = false;

			return false;

		}

		else if (($(this).find('.is_partial').val() == 1) && (tag_details.length==0)&& $(this).find('.isTagsplitted').val() != 1 ) {

			if ($(this).find('.gwt').val() == $(this).find('.act_gwt').val()) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Weight Must be Less Than the Actual Weight for Partly Sale..' });

				row_validate = false;

				return false;

			}

		}

		else if (id_orderdetails > 0 && is_order_rate == 2 ? false : !returnStatus.status) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + returnStatus.message });

			row_validate = false;

			return false;

		}

		if ((calculation_type == 1 || calculation_type == 2 || calculation_type ==3)&&(tag_details.length==0)) {

			if (parseFloat(total_mc_va) < parseFloat(total_min_mc_va)) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Total MC and Wastage cannot be less than ' + total_min_mc_va +' for tag code <b>'+tag_code +'</b>' });

				row_validate = false;

				return false;

			}

		}

		if (($(this).find('.item_emp_id').val() == "" || $(this).find('.item_emp_id').val() == null) && ($('#est_emp_select_req').val() == 1)) {

			row_validate = false;

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select Employee Name' });

			return false;

		}

		if(($(this).find('.sale_sgst').val()==0 && $(this).find('.sale_cgst').val()==0) && ($(this).find('.sale_igst').val()==0))

		{

			row_validate = false;

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Items Tax Not Calculated'});

		    return false;

		}

	});

	return row_validate;

}

function validateOrderDetailRow() {

	var row_validate = true;

	$('#estimation_order_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .orderno').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

/*function validateCatalogDetailRow(){

	var row_validate = true;

	$('#estimation_catalog_details > tbody  > tr').each(function(index, tr) {

		if($(this).find('.cat_pro_id').val() == "" || $(this).find('.cat_pcs').val() == "" || $(this).find('.cat_des_id').val() == "" || $(this).find('.cat_gwt').val() == "" || $(this).find('.cat_purity').val() == ""){

			row_validate = false;

		}

	});

	return row_validate;

}*/

/*function getNonTagDesignDetails(curRow)

{

	my_Date = new Date();

	$.ajax({

		url: base_url+'index.php/admin_ret_estimation/get_non_tag_stock/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: {'id_branch':$('#id_branch').val(),'id_product':curRow.find('.cat_product').val(),'id_design':curRow.find('.cat_design').val(),'id_sub_design':curRow.find('.cat_sub_design').val()},

		success: function (data)

		{

			console.log(data);

			var curr_used_gross = 0;

			var curr_used_pcs = 0;

			if(curRow.find('.is_non_tag').val()==1)

			{

				$('#estimation_catalog_details> tbody  > tr').each(function(index, tr) {

						curr_used_gross+=parseFloat(($(this).find('.cat_gwt').val()=='' ? 0 :$(this).find('.cat_gwt').val()));

						curr_used_pcs+=parseFloat(($(this).find('.cat_pcs').val()=='' ? 0 :$(this).find('.cat_pcs').val()));

				});

				if(data!=""){

					curRow.find('.tot_blc_pcs').val(data.no_of_piece-curr_used_pcs);

					curRow.find('.tot_blc_gwt').val(data.gross_wt-curr_used_gross);

					curRow.find('.blc_pcs').html(data.no_of_piece-curr_used_pcs);

					curRow.find('.blc_gwt').html(data.gross_wt-curr_used_gross);

				}

				else{

					curRow.find('.tot_blc_pcs').val(0);

					curRow.find('.tot_blc_gwt').val(0);

					curRow.find('.blc_pcs').html(0);

					curRow.find('.blc_gwt').html(0);

				}

			}

		}

	});

} */

function validateCatalogDetailRow() {

	var row_validate = true;

	let is_weight_scheme = check_is_weight_scheme();

	$('#estimation_catalog_details > tbody  > tr').each(function (index, tr) {

		let curRow = $(this).closest("tr");

		let rate = isNaN(curRow.find(".cat_market_rate_value").val()) || $.trim(curRow.find(".cat_market_rate_value").val()) == "" ? 0 : parseFloat(curRow.find(".cat_market_rate_value").val());

		let cal_type = curRow.find(".cat_calculation_based_on").val();



		let pro_id = isNaN($(this).find('.cat_pro_id').val()) || $(this).find('.cat_pro_id').val() == "" ? 0 : $(this).find('.cat_pro_id').val();

		let design_id = isNaN($(this).find('.cat_des_id').val()) || $(this).find('.cat_des_id').val() == "" ? 0 : $(this).find('.cat_des_id').val();

		let sub_design_id = isNaN($(this).find('.cat_id_sub_design').val()) || $(this).find('.cat_id_sub_design').val() == "" ? 0 : $(this).find('.cat_id_sub_design').val();

		let id_section = isNaN($(this).find('.cat_section').val()) || $(this).find('.cat_section').val() == "" ? 0 : $(this).find('.cat_section').val();



		let gwt = isNaN($(this).find('.cat_gwt').val()) || $(this).find('.cat_gwt').val() == "" ? 0 : $(this).find('.cat_gwt').val();

		let nwt = isNaN($(this).find('.cat_nwt').val()) || $(this).find('.cat_nwt').val() == "" ? 0 : $(this).find('.cat_nwt').val();

		let pcs = isNaN($(this).find('.cat_pcs').val()) || $(this).find('.cat_pcs').val() == "" ? 0 : $(this).find('.cat_pcs').val();



		let purity = isNaN($(this).find('.cat_purity').val()) || $(this).find('.cat_purity').val() == "" ? 0 : $(this).find('.cat_purity').val();

		let wastage_max_per = isNaN($(this).find('.cat_wastage').val()) || $(this).find('.cat_wastage').val() == "" ? 0 : $(this).find('.cat_wastage').val();

		let mc_cal_type = $(this).find('.id_mc_type').val();

		let act_mc_value = isNaN($(this).find('.cat_mc').val()) || $(this).find('.cat_mc').val() == "" ? 0 : $(this).find('.cat_mc').val();

		let sales_mode = isNaN($(this).find('.sales_mode').val()) || $(this).find('.sales_mode').val() == "" ? 0 : $(this).find('.sales_mode').val();



		let mc_va_limit = get_mc_va_limit(pro_id, design_id, 0, gwt, 2, nwt);

		let min_mc = mc_va_limit.mc_min;

		let min_va = mc_va_limit.va_min;

		let min_mc_cal_type = mc_va_limit.mc_cal_type;

		let margin_mrp = mc_va_limit.margin_mrp;

		let rate_field = curRow.find(".cat_rate_field").val();

		let metal_type = curRow.find(".metal_type").val();

		let total_mc_va = calculate_total_mc_va(rate, cal_type, gwt, nwt, wastage_max_per, mc_cal_type, act_mc_value, pcs);

		let total_min_mc_va = calculate_total_mc_va(rate, cal_type, gwt, nwt, min_va, min_mc_cal_type, min_mc, pcs);

		var is_eda_est = $('#iseda').val();
		
		if((curRow.find('.is_sr_applied').val() == 1 || curRow.find('.is_chttopup_applied').val() == 1) || ((curRow.find('.is_sr_applied').val() == 0 || curRow.find('.is_chttopup_applied').val() == 0) && (curRow.find('.partialy_utilized').val() == 1))){

			var returnStatus  = { status :true ,message:'' ,rate_per_grm: rate };

		}

		else{

		var  returnStatus = check_rate_is_valid(rate, rate_field, metal_type, cal_type);

		}


		if(wastage_max_per>100){

		   $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastage Should be below 100% ' });

			row_validate = false;

			return false;

		}

		if( ((curRow.find('.cat_tax_price').val())==0) && is_eda_est==0 ){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid GST' });

			 row_validate = false;

			 return false;

		 }

		if (curRow.find('.cat_pro_id').val() == "" || (curRow.find('.cat_sales_mode').val() == 1 ? (curRow.find('.cat_taxable_amt').val() == "" || curRow.find('.cat_amt').val() == "") : (curRow.find('.cat_pcs').val() == "" || curRow.find('.cat_des_id').val() == "" || curRow.find('.cat_id_sub_design').val() == "" || curRow.find('.cat_gwt').val() == "" || curRow.find('.cat_purity').val() == "" || curRow.find('.cat_amt').val() == "" || curRow.find('.cat_amt').val() == 0)) || returnStatus.status == false) {

			row_validate = false;

		} else if (cal_type == 1 || cal_type == 2 || cal_type == 3) {

			if (parseFloat(total_mc_va) < parseFloat(total_min_mc_va)) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Total MC and Wastage cannot be less than ' + total_min_mc_va });

				row_validate = false;

				return false;

			}

		}

		else if (id_section == '') {

			row_validate = false;

		}

		else if (sales_mode == 2) {

			//if(!is_weight_scheme) {

			if (parseFloat(wastage_max_per) < parseFloat(min_va)) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastage cannot be less than ' + min_va });

				row_validate = false;

				return false;

			} else if (parseFloat(act_mc_value) < parseFloat(min_mc)) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'MC cannot be less than ' + min_mc });

				row_validate = false;

				return false;

			}

			//}

		}

		if(($(this).find('.cat_cgst').val()==0 && $(this).find('.cat_sgst').val()==0) && ($(this).find('.cat_igst').val()==0))

		{

			row_validate = false;

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Non Tag Items Tax Not Calculated'});

		    return false;

		}
		if(($(this).find('.cat_market_rate_value').val()<=0 || $(this).find('.cat_market_rate_value').val()=='') || ($(this).find('.cat_taxable_amt').val()<=0 || $(this).find('.cat_taxable_amt').val()==''))
			{
				row_validate = false;
				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Metal Rate should not be zero.'});
				return false;
			}

		if((curRow.find('.nn_cat_mc').val() == '' || curRow.find('.nn_cat_mc').val() == undefined || parseFloat(curRow.find('.nn_cat_mc').val()) == null || curRow.find('.nn_cat_mc').val() == 0)) {
			if (curRow.find('.cat_mc').val() != '' && curRow.find('.cat_mc').val() != undefined && parseFloat(curRow.find('.cat_mc').val()) != null && parseFloat(curRow.find('.cat_mc').val()) > 0) {
				// If nn_cat_mc is not present, copy the value of cat_mc to nn_cat_mc
				curRow.find('.nn_cat_mc').val(curRow.find('.cat_mc').val());
			}
		}

		if((curRow.find('.act_va_per').val() == '' || curRow.find('.act_va_per').val() == undefined || parseFloat(curRow.find('.act_va_per').val()) == null || curRow.find('.act_va_per').val() == 0)) {
			if (curRow.find('.cat_wastage').val() != '' && curRow.find('.cat_wastage').val() != undefined && parseFloat(curRow.find('.cat_wastage').val()) != null && parseFloat(curRow.find('.cat_wastage').val()) > 0) {
				// If act_va_per is not present, copy the value of cat_wastage to act_va_per
				curRow.find('.act_va_per').val(curRow.find('.cat_wastage').val());
			}
			}
	});

	return row_validate;

}

/*function validateCustomDetailRow(){

	var row_validate = true;

	$('#estimation_custom_details > tbody  > tr').each(function(index, tr) {

		if($(this).find('.cus_product').val() == "" || $(this).find('.cus_gwt').val() == "" || $(this).find('.cus_purity').val() == "" || $(this).find('.cus_pcs').val() == "" ){

			row_validate = false;

		}

	});

	return row_validate;

}*/

function validateCustomDetailRow() {

	var row_validate = true;

	$('#estimation_custom_details > tbody  > tr').each(function (index, tr) {



		let curRow = $(this).closest("tr");



		let rate = isNaN(curRow.find(".cus_market_rate_value").val()) || $.trim(curRow.find(".cus_market_rate_value").val()) == "" ? 0 : parseFloat(curRow.find(".cus_market_rate_value").val());

		let rate_field = curRow.find(".cus_rate_field").val();



		let metal_type = curRow.find(".metal_type").val();

		let cal_type = curRow.find(".cus_calculation_based_on").val();



		let pro_id = $(this).find('.cus_product').val();

		let design_id = $(this).find('.cus_design').val();

		let id_sub_design = $(this).find('.cus_sub_design').val();



		let gwt = $(this).find('.cus_gwt').val();

		let nwt = $(this).find('.cus_nwt').val();



		let wastage_max_per = $(this).find('.cus_wastage').val();

		let mc_cal_type = $(this).find('.id_mc_type').val();

		let act_mc_value = $(this).find('.cus_mc').val();

		let sales_mode = $(this).find('.cus_sales_mode').val();



		let mc_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt);

		var is_eda_est = $('#iseda').val();

		console.log("mc_va_limit", mc_va_limit);

		if(wastage_max_per>100){

		    curRow.find('.cus_wastage').val('');

		    curRow.find('.cus_wastage').focus();

		    $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastage Should be less than 100% ' });

			row_validate = false;

		}



		let min_mc = mc_va_limit.mc_min;

		let min_va = mc_va_limit.va_min;

		let min_mc_cal_type = mc_va_limit.mc_cal_type;



		let margin_mrp = mc_va_limit.margin_mrp;

		let pcs_home = isNaN($(this).find('.cus_pcs').val()) || $(this).find('.cus_pcs').val() == "" ? 1 : $(this).find('.cus_pcs').val();

		let total_mc_va = calculate_total_mc_va(rate, cal_type, gwt, nwt, wastage_max_per, mc_cal_type, act_mc_value, pcs_home);

		let total_min_mc_va = calculate_total_mc_va(rate, cal_type, gwt, nwt, min_va, min_mc_cal_type, min_mc, pcs_home);


		if((curRow.find('.is_sr_applied').val() == 1 || curRow.find('.is_chttopup_applied').val() == 1) || ((curRow.find('.is_sr_applied').val() == 0 || curRow.find('.is_chttopup_applied').val() == 0) && (curRow.find('.partialy_utilized').val() == 1))){

			var returnStatus  = { status :true ,message:'' ,rate_per_grm: rate };

		}

		else{

		var  returnStatus = check_rate_is_valid(rate, rate_field, metal_type, cal_type);

		}


		if (pro_id == "" || (curRow.find('.cus_sales_mode').val() == 1 ? (curRow.find('.cus_taxable_amt').val() == "" || curRow.find('.cus_amt').val() == "") : (gwt == "" || curRow.find('.cus_purity').val() == "" || curRow.find('.cus_pcs').val() == "")) || returnStatus.status == false) {



			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The All Required Fields in Home Bill Detail Row..' });

			row_validate = false;



		} /* else if (cal_type == 1 || cal_type == 2 || cal_type == 3) {

			if (parseFloat(total_mc_va) < parseFloat(total_min_mc_va)) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Total MC and Wastage cannot be less than ' + total_min_mc_va });

				row_validate = false;

			}

		} */ else if((curRow.find('.cus_tax_price').val())==0 && is_eda_est==0){

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Invalid GST ' });

			row_validate = false;

		}
		// if(($(this).find('.cus_cgst').val()==0 && $(this).find('.cus_sgst').val()==0) && ($(this).find('.cus_igst').val()==0) && is_eda_est==0)
		// {
		// 	row_validate = false;
		// 	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Home Bill Tax Not Calculated'});
		//     return false;
		// }
		if(($(this).find('.cus_market_rate_value').val()<=0 || $(this).find('.cus_market_rate_value').val()=='') ||($(this).find('.cus_taxable_amt').val()<=0 || $(this).find('.cus_taxable_amt').val()==''))
			{
				row_validate = false;
				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Metal Rate should not be zero.'});
				return false;
			}
		if((curRow.find('.HB_cus_mc').val() == '' || curRow.find('.HB_cus_mc').val() == undefined || parseFloat(curRow.find('.HB_cus_mc').val()) == null || curRow.find('.HB_cus_mc').val() == 0)){
			if (curRow.find('.cus_mc').val() != '' && curRow.find('.cus_mc').val() != undefined && parseFloat(curRow.find('.cus_mc').val()) != null && curRow.find('.cus_mc').val() != 0) {
				// If HB_cus_mc is not present, copy the value of cus_mc to HB_cus_mc
				curRow.find('.HB_cus_mc').val(curRow.find('.cus_mc').val());
			}
		}

		if((curRow.find('.act_va_per').val() == '' || curRow.find('.act_va_per').val() == undefined || parseFloat(curRow.find('.act_va_per').val()) == null || curRow.find('.act_va_per').val() == 0)){
			if (curRow.find('.cus_wastage').val() != '' && curRow.find('.cus_wastage').val() != undefined && parseFloat(curRow.find('.cus_wastage').val()) != null && curRow.find('.cus_wastage').val() != 0) {
				// If act_va_per is not present, copy the value of cus_wastage to act_va_per
				curRow.find('.act_va_per').val(curRow.find('.cus_wastage').val());
			}
			}
	});

	return row_validate;

}

/*function validateOldMatelDetailRow(){

	var row_validate = true;

	$('#estimation_old_matel_details > tbody  > tr').each(function(index, tr) {

		if($(this).find('.old_item_type').val() == "" || $(this).find('.old_id_category').val() == "" || $(this).find('.old_metal_category').val() == "" || $(this).find('.old_gwt').val() == "" || $(this).find('.old_pcs').val() == "" || $(this).find('.old_metal_type').val() == "" || $(this).find('.old_purity').val() == "" ){

			row_validate = false;

		}

	});

	return row_validate;

}*/

function validateOldMatelDetailRow() {

	let row_validate = true;

	let max_cash_allowed = $('#max_cash_allowed').val();

	let total_cash = 0;

	$('#estimation_old_matel_details > tbody  > tr').each(function (index, tr) {

		let old_amount = $(this).find('.old_amount').val();

		let old_wastage = $(this).find('.old_wastage').val();

		let purpose = $(this).find('.purpose').val();

		// For old_metal_category: since it's always disabled, read value from hidden input mirror
		var old_metal_cat_val = $(this).find('.old_metal_cat_hidden').val() || $(this).find('.old_metal_category').val();
		var old_purity_val = $(this).find('.old_purity').val();

		if ($(this).find('.old_item_type').val() == "" || $(this).find('.old_id_category').val() == "" || $(this).find('.old_id_category').val() == null || old_metal_cat_val == "" || old_metal_cat_val == null || old_metal_cat_val == undefined || $(this).find('.old_gwt').val() == "" || $(this).find('.old_pcs').val() == "" || $(this).find('.old_metal_type').val() == "" || $(this).find('.old_metal_type').val() == null || old_purity_val == "" || old_purity_val == 0 || old_purity_val > 100) {

			row_validate = false;

			// Specific messages for purity and category
			if (old_purity_val == "" || old_purity_val == 0 || old_purity_val == null) {
				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Purity is required. Please enter a valid purity value.' });
			} else if (old_metal_cat_val == "" || old_metal_cat_val == null || old_metal_cat_val == undefined) {
				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Old Metal Category is required. Please enter a valid purity to auto-select category.' });
			} else {
				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The All Required Fields in Old Metal Row..' });
			}

		}

		if (($(this).find('.old_metal_remarks').val() == "" || $(this).find('.old_metal_remarks').val() == undefined) && ($('#est_old_metal_remarks_req').val() == 1)) {

			row_validate = false;

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Enter The Remarks..' });

		}

		if (purpose == 1) {

			total_cash = total_cash + parseFloat(old_amount);

		}
		if(($(this).find('.old_rate').val()<=0 && $(this).find('.old_rate').val()==''))
			{
				row_validate = false;
				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Old Metal Rate should not be zero.'});
				return false;
			}
	});

	// checks whether old gold return amount is greater than 10000

	if (parseFloat(total_cash) > parseFloat(max_cash_allowed)) {

		$.toaster({ priority: 'danger', title: 'Warning!', settings: { 'timeout': 4000 }, message: '' + "</br>" + 'Maximum cash allowed for amount is ' + max_cash_allowed });

		row_validate = false;

	}

	return row_validate;

}

function validateStoneDetailRow() {

	var row_validate = true;

	$('#estimation_stone_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .stone_id').val() == "" || $(this).find('td:eq(1) .stone_pcs').val() == "" || $(this).find('td:eq(2) .stone_wt').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

function validateMaterialDetailRow() {

	var row_validate = true;

	$('#estimation_material_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .material_id').val() == "" || $(this).find('td:eq(1) .material_wt').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

function validateVoucherDetailRow() {

	var row_validate = true;

	$('#estimation_gift_voucher_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .voucher_no').val() == "" || $(this).find('td:eq(2) .gift_voucher_amt').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

 $(document).on('change',".is_eda",function(){

    if($(this).is(":checked"))

    {

        $('#iseda').val(1);

    }else{

        $('#iseda').val(0);

    }

    calculatetag_SaleValue();

    calculateCustomItemSaleValue();

    calculateSaleValue();

});

function validateChitDetailRow() {

	var row_validate = true;

	$('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .scheme_account_id').val() == "" || $(this).find('td:eq(1) .chit_amt').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

function calculateSaleValue() {

	$('#estimation_catalog_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var disc_limit_type = $('#disc_limit_type').val();

		var total_price = 0;

		var total_tax_rate = 0;

		var market_total_tax_rate = 0;

		var total_tax_per = 0;

		var base_value_tax = 0;

		var arrived_value_tax = 0;

		var base_value_amt = 0;

		var arrived_value_amt = 0;

		var market_base_value_tax = 0;

		var market_arrived_value_tax = 0;

		var market_base_value_amt = 0;

		var market_arrived_value_amt = 0;

		var cus_del_country = $('#cus_del_country').val();

		var cmp_country = $('#cmp_country').val();

		var cus_del_state = $('#cus_del_state').val();

		var cmp_state = $('#cmp_state').val();

		var cgst = 0;

		var igst = 0;

		var sgst = 0;

		var board_rate = 0;

        var is_eda_est = $('#iseda').val();

		var gross_wt = (isNaN(curRow.find('.cat_gwt').val()) || curRow.find('.cat_gwt').val() == '') ? 0 : curRow.find('.cat_gwt').val();

		var less_wt = (isNaN(curRow.find('.cat_lwt').val()) || curRow.find('.cat_lwt').val() == '') ? 0 : curRow.find('.cat_lwt').val();

		var stone_price = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '') ? 0 : curRow.find('.stone_price').val();

		var material_price = (isNaN(curRow.find('.material_price').val()) || curRow.find('.material_price').val() == '') ? 0 : curRow.find('.material_price').val();

		var value_charge = (isNaN(curRow.find('.value_charge').val()) || curRow.find('.value_charge').val() == '') ? 0 : curRow.find('.value_charge').val();

		var net_wt = (isNaN(curRow.find('.cat_nwt').val()) || curRow.find('.cat_nwt').val() == '') ? 0 : curRow.find('.cat_nwt').val();

		var calculation_type = (isNaN(curRow.find('.cat_calculation_based_on').val()) || curRow.find('.cat_calculation_based_on').val() == '') ? 0 : curRow.find('.cat_calculation_based_on').val();

		var discount = (isNaN(curRow.find('.cat_dis').val()) || curRow.find('.cat_dis').val() == '') ? 0 : curRow.find('.cat_dis').val();

		var metal_type = (isNaN(curRow.find('.metal_type').val()) || curRow.find('.metal_type').val() == '') ? 1 : curRow.find('.metal_type').val();

		var tax_group = curRow.find('.tax_group_id').val();

		var sales_mode = curRow.find('.cat_sales_mode').val();

		var disc_amt = $('#summary_discount_amt').val();

		rate_field = curRow.find('.cat_rate_field').val();

		var rate_field_value = (isNaN($('.'+rate_field).html()) ||$('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();

		market_rate_field = curRow.find('.cat_market_rate_field').val();

		var market_rate_per_grm = 0;

		/*if(rate_field!='')

		{

			rate_per_grm = (isNaN($('.'+rate_field).html()) ||$('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();

		}

		if(market_rate_field!='')

		{

			market_rate_per_grm = (isNaN($('.'+market_rate_field).html()) ||$('.'+market_rate_field).html() == '')  ? 0 : parseFloat($('.'+market_rate_field).html());

		}*/

		if($('#wastage_rate_type').val() == 2 && !($("#manual_rate").is(":checked")) && rate_field_value == curRow.find('.cat_market_rate_value').val()){

			board_rate =  parseFloat(curRow.find('.cat_market_rate_value').val());
		}
		
		else {
			
			board_rate = rate_field_value

		}

		rate_per_grm = curRow.find('.cat_market_rate_value').val();

		if (market_rate_field != '') {

			market_rate_per_grm = (isNaN($('.' + market_rate_field).html()) || $('.' + market_rate_field).html() == '') ? 0 : parseFloat($('.' + market_rate_field).html());

		}

		var retail_max_mc = (isNaN(curRow.find('.cat_mc').val()) || curRow.find('.cat_mc').val() == '') ? 0 : curRow.find('.cat_mc').val();

		var cat_pcs = (isNaN(curRow.find('.cat_pcs').val()) || curRow.find('.cat_pcs').val() == '' || curRow.find('.cat_pcs').val() == 0) ? 1 : curRow.find('.cat_pcs').val();

		var tot_wastage = (isNaN(curRow.find('.cat_wastage').val()) || curRow.find('.cat_wastage').val() == '') ? 0 : curRow.find('.cat_wastage').val();

		/**

		*	Amount calculation based on settings (without discount and tax )

		*   0 - Wastage on Gross weight And MC on Gross weight

		*   1 - Wastage on Net weight And MC on Net weight

		*   2 - Wastage On Netwt And MC On Grwt

		*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC

		*/

		if (calculation_type == 0) {

			var wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			var wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

			var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) :  (curRow.find('.id_mc_type').val() == 3 ? parseFloat((retail_max_mc / 100) * (parseFloat(rate_per_grm) *(parseFloat(wast_wgt) + parseFloat(net_wt)))) : parseFloat(retail_max_mc * cat_pcs)));

			// Metal Rate + Stone + OM + Wastage + MC

			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(material_price) +parseFloat(value_charge) ).toFixed(3);

			market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(material_price));

		}

		else if (calculation_type == 1) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			var wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

			var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) :  (curRow.find('.id_mc_type').val() == 3 ? parseFloat
			((retail_max_mc / 100) * (parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))) : parseFloat(retail_max_mc * cat_pcs)));

			// Metal Rate + Stone + OM + Wastage + MC

			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(material_price) +parseFloat(value_charge));

			market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(material_price));

		}

		else if (calculation_type == 2) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			var wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

			var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) :  (curRow.find('.id_mc_type').val() == 3 ? parseFloat
			((retail_max_mc / 100) * (parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))) : parseFloat(retail_max_mc * cat_pcs)));

			// Metal Rate + Stone + OM + Wastage + MC

			rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(material_price) +parseFloat(value_charge);

			market_rate_with_mc = parseFloat((parseFloat(market_rate_per_grm) * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(material_price);

		}

		if (sales_mode == 1) {

			if(calculation_type == 4)

			{

				var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * cat_pcs));

				rate_with_mc = parseFloat((isNaN(rate_per_grm) || rate_per_grm == '') ? 0 : rate_per_grm * gross_wt);

				rate_with_mc = parseFloat(parseFloat(rate_with_mc) + parseFloat(mc_type)).toFixed(2);

				market_rate_with_mc = parseFloat((isNaN(rate_per_grm) || rate_per_grm == '') ? 0 : rate_per_grm * gross_wt);

				curRow.find('.cat_taxable_amt').val(rate_with_mc);

			}

			else

			{

				rate_with_mc = curRow.find('.cat_taxable_amt').val();

				market_rate_with_mc = curRow.find('.cat_taxable_amt').val();

			}

		}

		// Discount calculation based on employee eligiblity

		if (disc_limit_type != '' && (disc_amt > 0)) {

			if (disc_limit_type == 1) {

				rate_with_mc = parseFloat(rate_with_mc - discount);

				market_rate_with_mc = parseFloat(market_rate_with_mc - discount);

			}

			else {

				rate_with_mc = parseFloat(rate_with_mc + parseFloat(rate_with_mc * parseFloat(discount / 100)))

				market_rate_with_mc = parseFloat(market_rate_with_mc + parseFloat(market_rate_with_mc * parseFloat(discount / 100)))

			}

		}

		console.log('Rate with MC + Discount : ' + rate_with_mc);

		// Tax Calculation

		var tax_type = '';

		$.each(prod_details, function (k, val) {

			if (val.pro_id == curRow.find('.cat_pro_id').val()) {

				tax_type = val.tax_type;

			}

		});

		if(is_eda_est==0)

    	{

		if (tax_details.length > 0) {

			// Tax Calculation

			var base_value_tax = parseFloat(calculate_base_value_tax(rate_with_mc, tax_group)).toFixed(2);

			var base_value_amt = parseFloat(parseFloat(rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);

			var arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

			var market_base_value_tax = parseFloat(calculate_base_value_tax(market_rate_with_mc, tax_group)).toFixed(2);

			var market_base_value_amt = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);

			var market_arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var market_arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			market_total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

			$.each(tax_details, function (idx, taxitem) {

				if (taxitem.tgi_tgrpcode == tax_group) {

					if (taxitem.tgi_calculation == 1) {

						console.log(1);

						$('.cat_tax_per').val(taxitem.tax_percentage);

					}

				}

			});

		}

	}

		total_price = parseFloat(parseFloat(rate_with_mc) + parseFloat(total_tax_rate)).toFixed(2);

		market_total_price = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(market_total_tax_rate)).toFixed(2);

		total_tax_per = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

		if ((calculation_type == 3 || calculation_type == 4) && (tax_type == 1)) //tax_type 1-Inclusive ,2-Exclusive

		{

			total_price = rate_with_mc;

			var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_mc, tax_group)).toFixed(2);

		}

		// cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		// sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		 if (cus_del_country == cmp_country) {

			if (cus_del_state == cmp_state) {

				cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

				sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			}

			else {

				cgst = 0; sgst = 0;

				igst = total_tax_rate;

			}

		}

		else if (cus_del_country == '') {

			cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		}

		else // NO GST

		{

			cgst = 0; sgst = 0; igst = 0;

			inclusive_tax_rate = rate_with_mc;

		}

		curRow.find('.cat_cgst').val(cgst);

		curRow.find('.cat_sgst').val((sgst));

		curRow.find('.cat_igst').val(igst);

		curRow.find('.cat_taxable_amount').val((rate_with_mc));

		curRow.find('.cat_tax_price').val(total_tax_rate);

		curRow.find('.cat_amt').val((total_price));

		curRow.find('.market_rate_tax').val(market_total_tax_rate);

		curRow.find('.market_rate_cost').val((market_total_price));

		curRow.find('.cat_wastage_wt').val(parseFloat(wast_wgt).toFixed(3));

		if (sales_mode != 1)

			curRow.find('.cat_taxable_amt').val(parseFloat(rate_with_mc).toFixed(2));

		console.log('Taxable Amount : ' + rate_with_mc);

		console.log('Amount : ' + total_price);

		console.log('*************************');

	});

	calculate_purchase_details();

	calculate_sales_details();

}

function calculateCustomItemSaleValue(curRow) {

	$('#estimation_custom_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var disc_limit_type = $('#disc_limit_type').val();

		var total_price = 0;

		var arrived_rate_tax = 0;

		var total_tax_per = 0;

		var total_tax_rate = 0;

		var market_total_tax_rate = 0;

		var base_value_tax = 0;

		var arrived_value_tax = 0;

		var base_value_amt = 0;

		var arrived_value_amt = 0;

		var market_base_value_tax = 0;

		var market_arrived_value_tax = 0;

		var market_base_value_amt = 0;

		var market_arrived_value_amt = 0;

		var value_charge = 0;

		var stone_price = 0; // Not worked

		var tot_stone_wt = 0; // Not worked

		var certification_price = 0; // Not worked

		var stone_wt = 0;

		var cus_del_country = $('#cus_del_country').val();

		var cmp_country = $('#cmp_country').val();

		var cus_del_state = $('#cus_del_state').val();

		var cmp_state = $('#cmp_state').val();

		var cgst = 0;

		var igst = 0;

		var sgst = 0;

		var board_rate = 0;

		var is_eda_est = $('#iseda').val();

		var disc_amt = $('#summary_discount_amt').val();

		var stone_details = curRow.find('.stone_details').val();

		var stone_amt = 0;

		var dia_amt = 0;

		if (stone_details != '') {

			var st_details = JSON.parse(stone_details);

			if (st_details.length > 0) {

				$.each(st_details, function (pkey, pitem) {

					$.each(uom_details, function (key, item) {

						if (item.uom_id == pitem.stone_uom_id) {

							if (pitem.show_in_lwt == 1) {

								if ((item.uom_short_code == 'CT') && (item.divided_by_value != null && item.divided_by_value != '')) //For Carat Need to convert into gram

								{

									stone_wt = parseFloat(parseFloat(pitem.stone_wt) / parseFloat(item.divided_by_value));

								} else {

									stone_wt = pitem.stone_wt;

								}

								tot_stone_wt += parseFloat(stone_wt);

							}

							stone_price += parseFloat(pitem.stone_price);

							if(pitem.stones_type == 1){

								dia_amt += parseFloat(pitem.stone_price);

							}

							else{

								stone_amt += parseFloat(pitem.stone_price);

							}

						}

					});

				});

			}

		}

		curRow.find('.cus_lwt').val(parseFloat(tot_stone_wt).toFixed(3));

		var gross_wt = (isNaN(curRow.find('.cus_gwt').val()) || curRow.find('.cus_gwt').val() == '') ? 0 : curRow.find('.cus_gwt').val();

		curRow.find('.cus_nwt').val(parseFloat(parseFloat(gross_wt) - tot_stone_wt).toFixed(3));

		var net_wt = (isNaN(curRow.find('.cus_nwt').val()) || curRow.find('.cus_nwt').val() == '') ? 0 : curRow.find('.cus_nwt').val();

		var value_charge = (isNaN(curRow.find('.value_charge').val()) || curRow.find('.value_charge').val() == '') ? 0 : curRow.find('.value_charge').val();

		var material_price = (isNaN(curRow.find('.material_price').val()) || curRow.find('.material_price').val() == '') ? 0 : curRow.find('.material_price').val();

		var calculation_type = (isNaN(curRow.find('.cus_calculation_based_on').val()) || curRow.find('.cus_calculation_based_on').val() == '') ? 0 : curRow.find('.cus_calculation_based_on').val();

		var discount = (isNaN(curRow.find('.cus_dis').val()) || curRow.find('.cus_dis').val() == '') ? 0 : curRow.find('.cus_dis').val();

		var mjdmagoldrate_22ct = (isNaN($('.mjdmagoldrate_22ct').html()) || $('.mjdmagoldrate_22ct').html() == '') ? 0 : parseFloat($('.mjdmagoldrate_22ct').html());

		var metal_type = (isNaN(curRow.find('.metal_type').val()) || curRow.find('.metal_type').val() == '') ? 1 : curRow.find('.metal_type').val();

		var tax_group = curRow.find('.tax_group_id').val();

		var sales_mode = curRow.find('.cus_sales_mode').val();

		rate_field = curRow.find('.cus_rate_field').val();

		var rate_field_value = (isNaN($('.'+rate_field).html()) ||$('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();

		market_rate_field = curRow.find('.cus_market_rate_field').val();

		/*if(rate_field!='')

		{

			rate_per_grm = (isNaN($('.'+rate_field).html()) ||$('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();

		}*/

		if($('#wastage_rate_type').val() == 2 && !($("#manual_rate").is(":checked")) && rate_field_value == curRow.find('.cus_market_rate_value').val()){

			board_rate =  parseFloat(curRow.find('.cus_market_rate_value').val());
		}
		
		else {
			
			board_rate = rate_field_value

		}

		rate_per_grm = curRow.find('.cus_market_rate_value').val();

		if (market_rate_field != '') {

			market_rate_per_grm = (isNaN($('.' + market_rate_field).html()) || $('.' + market_rate_field).html() == '') ? 0 : parseFloat($('.' + market_rate_field).html());

		}

		var retail_max_mc = (isNaN(curRow.find('.cus_mc').val()) || curRow.find('.cus_mc').val() == '') ? 0 : curRow.find('.cus_mc').val();

		var tot_wastage = (isNaN(curRow.find('.cus_wastage').val()) || curRow.find('.cus_wastage').val() == '') ? 0 : curRow.find('.cus_wastage').val();

		if (calculation_type == 0) {

			var wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			var wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);		//rate_per_grm means either board rate or manual rate

			var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) :  (curRow.find('.id_mc_type').val() == 3 ? parseFloat((retail_max_mc / 100) * (parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))) :  parseFloat(retail_max_mc * 1)));

			// Metal Rate + Stone + OM + Wastage + MC

			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(material_price) + parseFloat(value_charge));

			market_rate_with_mc = parseFloat(parseFloat(mjdmagoldrate_22ct * (parseFloat(net_wt))) + parseFloat(mc_type) + parseFloat(wast_amt) + parseFloat(stone_price) + parseFloat(material_price));

		}

		else if (calculation_type == 1) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			var wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);		//rate_per_grm means either board rate or manual rate

			var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) :  (curRow.find('.id_mc_type').val() == 3 ? parseFloat((retail_max_mc / 100) *(parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))) :  parseFloat(retail_max_mc * 1)));

			// Metal Rate + Stone + OM + Wastage + MC

			rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(material_price) + parseFloat(value_charge));

			market_rate_with_mc = parseFloat(parseFloat(mjdmagoldrate_22ct * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(material_price));

		}

		else if (calculation_type == 2) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			var wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);		//rate_per_grm means either board rate or manual rate

			var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) :  (curRow.find('.id_mc_type').val() == 3 ? parseFloat((retail_max_mc / 100) * (parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt)))) :  parseFloat(retail_max_mc * 1)));

			// Metal Rate + Stone + OM + Wastage + MC

			rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(material_price) + parseFloat(value_charge);

			market_rate_with_mc = parseFloat((parseFloat(mjdmagoldrate_22ct) * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(material_price);

		}

		if (sales_mode == 1) {

			if (calculation_type == 4) {

				if (curRow.find('.id_mc_type').val() != 3) {

					mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * 1));

				} else {

					mc_type = retail_max_mc;

				}

				rate_with_mc = parseFloat((isNaN(curRow.find('.cus_market_rate_value').val()) || curRow.find('.cus_market_rate_value').val() == '') ? 0 : curRow.find('.cus_market_rate_value').val() * gross_wt);

				rate_with_mc = parseFloat(parseFloat(rate_with_mc) + parseFloat(mc_type)).toFixed(2);

				market_rate_with_mc = parseFloat((isNaN(curRow.find('.cus_market_rate_value').val()) || curRow.find('.cus_market_rate_value').val() == '') ? 0 : curRow.find('.cus_market_rate_value').val() * gross_wt);

				curRow.find('.cus_taxable_amt').val(rate_with_mc);

			}

			else {

				rate_with_mc = curRow.find('.cus_taxable_amt').val();

				market_rate_with_mc = curRow.find('.cus_taxable_amt').val();

			}

		}

		if (disc_limit_type != '' && (disc_amt > 0)) {

			if (disc_limit_type == 1) {

				rate_with_mc = parseFloat(rate_with_mc - discount);

				market_rate_with_mc = parseFloat(market_rate_with_mc - discount);

			}

			else {

				rate_with_mc = parseFloat(rate_with_mc + parseFloat(rate_with_mc * parseFloat(discount / 100)))

				market_rate_with_mc = parseFloat(market_rate_with_mc + parseFloat(market_rate_with_mc * parseFloat(discount / 100)))

			}

		}

		var tax_type = '';

		$.each(prod_details, function (k, val) {

			if (val.pro_id == curRow.find('.cus_product_id').val()) {

				tax_type = val.tax_type;

			}

		});

         if(is_eda_est==0)

		 {

		if (tax_details.length > 0) {

			// Tax Calculation

			var base_value_tax = parseFloat(calculate_base_value_tax(rate_with_mc, tax_group)).toFixed(2);

			var base_value_amt = parseFloat(parseFloat(rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);

			var arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

			var market_base_value_tax = parseFloat(calculate_base_value_tax(market_rate_with_mc, tax_group)).toFixed(2);

			var market_base_value_amt = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);

			var market_arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var market_arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			market_total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

		}

	}

		total_price = parseFloat(parseFloat(rate_with_mc) + parseFloat(total_tax_rate)).toFixed(2);

		market_total_price = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(market_total_tax_rate)).toFixed(2);

		/* if((calculation_type==3 || calculation_type==4) && (tax_type==1) ) //tax_type 1-Inclusive ,2-Exclusive

		 {

			 total_price=rate_with_mc;

			 var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_mc,tax_group)).toFixed(2);

		 }*/

		total_tax_per = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

		curRow.find('.market_rate_tax').val(market_total_tax_rate);

		curRow.find('.market_rate_cost').val(market_total_price);

		curRow.find('.cus_tax_price').val(total_tax_rate);

		//curRow.find('.cus_tax_per').val(total_tax_per);

		if (sales_mode != 1) {

			curRow.find('.cus_taxable_amt').val(parseFloat(rate_with_mc).toFixed(2));

		}

		// cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		// sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		 if (cus_del_country == cmp_country) {

			if (cus_del_state == cmp_state) {

				cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

				sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			}

			else {

				cgst = 0; sgst = 0;

				igst = total_tax_rate;

			}

		}

		else if (cus_del_country == '') {

			cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		}

		else // NO GST

		{

			cgst = 0; sgst = 0; igst = 0;

			inclusive_tax_rate = rate_with_mc;

		} 

		curRow.find('.cus_cgst').val(cgst);

		curRow.find('.cus_sgst').val(sgst);

		curRow.find('.cus_igst').val(igst);

		curRow.find('.cus_taxable_amount').val(rate_with_mc);

		curRow.find('.cus_amount').val((total_price));

		curRow.find('.cus_wastage_wt').val((parseFloat(wast_wgt).toFixed(3)));

		curRow.find('.tot_stn_amt').val(parseFloat(stone_amt).toFixed(2));

		curRow.find('.tot_dia_amt').val(parseFloat(dia_amt).toFixed(2));

		console.log('Calculation : ' + calculation_type);

		console.log('Wastage : ' + wast_wgt);

		console.log('MC : ' + mc_type);

		console.log('Rate with MC : ' + rate_with_mc);

		console.log('Rate per Gram : ' + rate_per_grm);

	});

	calculate_purchase_details();

	calculate_sales_details();

}

$(document).on('change', '.old_touch', function (e) {

	var row = $(this).closest('tr');

	var touch = row.find('.old_touch').val();

	// Condition to check wastage % must be less than 100%

	if (parseFloat(touch) > 100 || parseFloat(touch) <= 0 || touch == '') {

		row.find('.old_touch').val('100');

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Touch  must be within 100' })

	}

	calculateOldMatelItemSaleValue(row);

});

function calculateOldMatelItemSaleValue(curRow) {

	let tot_stone_wt = 0;

	var stone_details = curRow.find('.stone_details').val();

	if (stone_details != '' && stone_details !== undefined) {

		var st_details = JSON.parse(stone_details);

		if (st_details.length > 0) {

			$.each(st_details, function (pkey, pitem) {

				$.each(uom_details, function (key, item) {

					if (item.uom_id == pitem.stone_uom_id) {

						if (pitem.show_in_lwt == 1) {

							if ((item.uom_short_code == 'CT') && (item.divided_by_value != null && item.divided_by_value != '')) //For Carat Need to convert into gram

							{

								stone_wt = parseFloat(parseFloat(pitem.stone_wt) / parseFloat(item.divided_by_value));

							} else {

								stone_wt = pitem.stone_wt;

							}

							tot_stone_wt += parseFloat(stone_wt);

						}

						stone_price += parseFloat(pitem.stone_price);

					}

				});

			});

		}

	}

	tot_stone_wt = parseFloat(tot_stone_wt).toFixed(3);

	curRow.find('.old_swt').val(tot_stone_wt);

	validate_oldmetal_weight(curRow);

	var gross_wt = (isNaN(curRow.find('.old_gwt').val()) || curRow.find('.old_gwt').val() == '') ? 0 : curRow.find('.old_gwt').val();

	var dust_wt = (isNaN(curRow.find('.old_dwt').val()) || curRow.find('.old_dwt').val() == '') ? 0 : curRow.find('.old_dwt').val();

	var stone_wt = (isNaN(curRow.find('.old_swt').val()) || curRow.find('.old_swt').val() == '') ? 0 : curRow.find('.old_swt').val();

	var other_stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();

	var other_stone_price = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '') ? 0 : curRow.find('.stone_price').val();

	var touch = (isNaN(curRow.find('.old_touch').val()) || curRow.find('.old_touch').val() == '' || curRow.find('.old_touch').val() > 100) ? 100 : curRow.find('.old_touch').val();

	var purity = (isNaN(curRow.find('.old_purity').val()) || curRow.find('.old_purity').val() == '' || curRow.find('.old_purity').val() > 100) ? 0 : curRow.find('.old_purity').val();

	var total_price = 0;

	var tax_rate = 0;

	var rate_per_grm = (isNaN(curRow.find('.old_rate').val()) || curRow.find('.old_rate').val() == '') ? 0 : curRow.find('.old_rate').val();

	var cal_weight = (isNaN(curRow.find('.old_wastage_wt').val()) || curRow.find('.old_wastage_wt').val() == '') ? 0 : curRow.find('.old_wastage_wt').val();

	var wastage = (isNaN(curRow.find('.old_wastage').val()) || curRow.find('.old_wastage').val() == '') ? 0 : curRow.find('.old_wastage').val();

	var net_wt = (isNaN(curRow.find('.old_nwt').val()) || curRow.find('.old_nwt').val() == '') ? 0 : curRow.find('.old_nwt').val();

	var net_weight = parseFloat(parseFloat(gross_wt) - parseFloat(dust_wt) - parseFloat(stone_wt) - parseFloat(other_stone_wt) - parseFloat(cal_weight)).toFixed(3);

	curRow.find('.old_nwt').val(net_weight);

	//total_price =parseFloat(parseFloat(net_weight)*parseFloat(rate_per_grm));

	total_price = parseFloat(parseFloat((parseFloat(net_weight) * parseFloat(touch)) / 100) * parseFloat(rate_per_grm)).toFixed(2);

	total_price = Math.round(parseFloat(total_price) + parseFloat(other_stone_price));

	$(".summary_sale_amt").html(total_price);

	curRow.find('.old_amount').val(parseFloat(total_price).toFixed(2));

	calculate_purchase_details();

	calculate_sales_details();

	console.log('total_price:' + total_price);

	console.log('rate_per_grm:' + rate_per_grm);

	console.log('tot_wastage:' + wastage);

	console.log('cal_weight:' + cal_weight);

	console.log('net_wt:' + net_weight);

	console.log('other_stone_wt:' + other_stone_wt);

	console.log('--------------');

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

function get_purity_rate() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_estimation/get_purity_rate',

		dataType: 'json',

		success: function (data) {

			purity_rate = data;

		}

	});

}

function get_profile() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_estimation/estimation/profile',

		dataType: 'json',

		success: function (data) {

			profile = data;

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

		}

	});

}

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

function get_materials() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_tagging/getAvailableMaterials',

		dataType: 'json',

		success: function (data) {

			materials = data;

		}

	});

}

function calculate_purchase_details() {

	var purchase_weight = 0;

	var purchase_rate = 0;

	var stone_rate = 0;

	var material_rate = 0;

	var purchase_piece = 0;

	var cgst = 0;

	var sgst = 0;

	var igst = 0;

	var sale_taxable_amount = 0;

	$('#estimation_tag_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('.est_tag_name').val() != "" && $(this).find('.sales_value').val() != "") {

			purchase_weight += parseFloat((isNaN($(this).find('.gwt').val()) || $(this).find('.gwt').val() == '') ? 0 : $(this).find('.gwt').val());

			purchase_rate += parseFloat((isNaN($(this).find('.sales_value').val()) || $(this).find('.sales_value').val() == '') ? 0 : $(this).find('.sales_value').val());

			purchase_piece += parseFloat((isNaN($(this).find('.piece').val()) || $(this).find('.piece').val() == '') ? 0 : $(this).find('.piece').val());

			cgst += parseFloat((isNaN($(this).find('.sale_cgst').val()) || $(this).find('.sale_cgst').val() == '') ? 0 : $(this).find('.sale_cgst').val());

			sgst += parseFloat((isNaN($(this).find('.sale_sgst').val()) || $(this).find('.sale_sgst').val() == '') ? 0 : $(this).find('.sale_sgst').val());

			igst += parseFloat((isNaN($(this).find('.sale_igst').val()) || $(this).find('.sale_igst').val() == '') ? 0 : $(this).find('.sale_igst').val());

			sale_taxable_amount += parseFloat((isNaN($(this).find('.sale_taxable_amount').val()) || $(this).find('.sale_taxable_amount').val() == '') ? 0 : $(this).find('.sale_taxable_amount').val());

		}

	});

	$('#estimation_order_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('.order_id').val() != "") {

			purchase_weight += parseFloat((isNaN($(this).find('.weight').val()) || $(this).find('.weight').val() == '') ? 0 : $(this).find('.weight').val());

			purchase_rate += parseFloat((isNaN($(this).find('.item_cost').val()) || $(this).find('.item_cost').val() == '') ? 0 : $(this).find('.item_cost').val());

			cgst += parseFloat((isNaN($(this).find('.cgst').val()) || $(this).find('.cgst').val() == '') ? 0 : $(this).find('.cgst').val());

			sgst += parseFloat((isNaN($(this).find('.sgst').val()) || $(this).find('.sgst').val() == '') ? 0 : $(this).find('.sgst').val());

			igst += parseFloat((isNaN($(this).find('.igst').val()) || $(this).find('.igst').val() == '') ? 0 : $(this).find('.igst').val());

			sale_taxable_amount += parseFloat((isNaN($(this).find('.taxable_amount').val()) || $(this).find('.taxable_amount').val() == '') ? 0 : $(this).find('.taxable_amount').val());

		}

	});

	$('#estimation_catalog_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .cat_product').val() != "" && ($(this).find('.cat_gwt').val() != "" || $(this).find('.cat_amt').val() != "")) {

			purchase_weight += parseFloat((isNaN($(this).find('.cat_gwt').val()) || $(this).find('.cat_gwt').val() == '') ? 0 : $(this).find('.cat_gwt').val());

			purchase_rate += parseFloat((isNaN($(this).find('.cat_amt').val()) || $(this).find('.cat_amt').val() == '') ? 0 : $(this).find('.cat_amt').val());

			var id_mc_type = $(this).find('.id_mc_type').val();

			cgst += parseFloat((isNaN($(this).find('.cat_cgst').val()) || $(this).find('.cat_cgst').val() == '') ? 0 : $(this).find('.cat_cgst').val());

			sgst += parseFloat((isNaN($(this).find('.cat_sgst').val()) || $(this).find('.cat_sgst').val() == '') ? 0 : $(this).find('.cat_sgst').val());

			igst += parseFloat((isNaN($(this).find('.cat_igst').val()) || $(this).find('.cat_igst').val() == '') ? 0 : $(this).find('.cat_igst').val());

			sale_taxable_amount += parseFloat((isNaN($(this).find('.cat_taxable_amount').val()) || $(this).find('.cat_taxable_amount').val() == '') ? 0 : $(this).find('.cat_taxable_amount').val());

			//$(this).find('.mc_type ').select2();

			/*$(this).find('.mc_type ').select2({

				placeholder: "Type",

				allowClear: true

			});*/

			//$(this).find('.mc_type ').select2("val",(id_mc_type!='' && id_mc_type>0?id_mc_type:''));

		}

	});

	$('#estimation_custom_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('.cus_product_id').val() != "" && ($(this).find('.cus_gwt').val() != "" || $(this).find('.cus_amount').val() != "")) {

			purchase_weight += parseFloat((isNaN($(this).find('.cus_gwt').val()) || $(this).find('.cus_gwt').val() == '') ? 0 : $(this).find('.cus_gwt').val());

			purchase_rate += parseFloat((isNaN($(this).find('.cus_amount').val()) || $(this).find('.cus_amount').val() == '') ? 0 : $(this).find('.cus_amount').val());

			var id_mc_type = $(this).find('.id_mc_type').val();

			cgst += parseFloat((isNaN($(this).find('.cus_cgst').val()) || $(this).find('.cus_cgst').val() == '') ? 0 : $(this).find('.cus_cgst').val());

			sgst += parseFloat((isNaN($(this).find('.cus_sgst').val()) || $(this).find('.cus_sgst').val() == '') ? 0 : $(this).find('.cus_sgst').val());

			igst += parseFloat((isNaN($(this).find('.cus_igst').val()) || $(this).find('.cus_igst').val() == '') ? 0 : $(this).find('.cus_igst').val());

			sale_taxable_amount += parseFloat((isNaN($(this).find('.cus_taxable_amount').val()) || $(this).find('.cus_taxable_amount').val() == '') ? 0 : $(this).find('.cus_taxable_amount').val());

			/*$(this).find('.cus_mc_type ').select2();

			$(this).find('.cus_mc_type ').select2({

				placeholder: "Type",

				allowClear: true

			});

			$(this).find('.cus_mc_type ').select2("val",(id_mc_type!='' && id_mc_type>0?id_mc_type:''));*/

		}

	});

	/*$('#estimation_stone_details > tbody  > tr').each(function(index, tr) {

		if($(this).find('td:first .stone_id').val() != "" && $(this).find('td:eq(1) .stone_pcs').val() != "" && $(this).find('td:eq(3) .stone_price').val() != ""){

			stone_rate += parseFloat((isNaN($(this).find('td:eq(3) .stone_price').val()) || $(this).find('td:eq(3) .stone_price').val() == '')  ? 0 : $(this).find('td:eq(3) .stone_price').val());

		}

	});*/

	$('#estimation_material_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .material_id').val() != "" && $(this).find('td:eq(2) .material_price').val() != "") {

			material_rate += parseFloat((isNaN($(this).find('td:eq(2) .material_price').val()) || $(this).find('td:eq(2) .material_price').val() == '') ? 0 : $(this).find('td:eq(2) .material_price').val());

		}

	});

	//$(".summary_pur_weight").html(parseFloat(purchase_weight).toFixed(3));

	cgst = parseFloat(cgst).toFixed(2);

	sgst = parseFloat(sgst).toFixed(2);

	igst = parseFloat(igst).toFixed(2);

	sale_taxable_amount = parseFloat(sale_taxable_amount).toFixed(2);

	$(".tax_cgst").html(cgst);

	$(".tax_sgst").html(sgst);

	$(".tax_igst").html(igst);

	$(".taxable_sale_smt").html(sale_taxable_amount);

	$(".summary_pur_weight").html(parseFloat(purchase_weight).toFixed(3) + "/" + purchase_piece);

	$(".summary_pur_amt").html(parseFloat(purchase_rate).toFixed(2));

	$(".summary_stone_amt").html(stone_rate);

	$(".summary_material_amt").html(material_rate);

	calculateFinalCost();

}

function calculate_sales_details() {

	var sale_weight = 0;

	var sale_rate = 0;

	var gift_voucher = 0;

	var chit_amt = 0;

	var saved_weight = 0;

	var sr_weight = 0;

	var sr_amt = 0;	

	$('#estimation_old_matel_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .old_item_type').val() != "" && $(this).find('.old_gwt').val() != "" && $(this).find('.old_nwt').val() != "" && $(this).find('.old_amount').val() != "") {

			sale_weight += parseFloat((isNaN($(this).find('.old_gwt').val()) || $(this).find('.old_gwt').val() == '') ? 0 : $(this).find('.old_gwt').val());

			sale_rate += parseFloat((isNaN($(this).find('.old_amount').val()) || $(this).find('.old_amount').val() == '') ? 0 : $(this).find('.old_amount').val());

		}

	});

	$('#estimation_gift_voucher_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .voucher_no').val() != "" && $(this).find('td:eq(2) .gift_voucher_amt').val() != "") {

			gift_voucher += parseFloat((isNaN($(this).find('td:eq(2) .gift_voucher_amt').val()) || $(this).find('td:eq(2) .gift_voucher_amt').val() == '') ? 0 : $(this).find('td:eq(2) .gift_voucher_amt').val());

		}

	});

	$('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .id_scheme_account').val() != "" && $(this).find('td:eq(1) .chit_amt').val() != "") {

			saved_weight += parseFloat((isNaN($(this).find('.saved_weight').html()) || $(this).find('.saved_weight').html() == '') ? 0 : $(this).find('.saved_weight').html());

			chit_amt += parseFloat((isNaN($(this).find('td:eq(1) .chit_amt').val()) || $(this).find('td:eq(1) .chit_amt').val() == '') ? 0 : $(this).find('td:eq(1) .chit_amt').val());

		}

	});

	$('#estimation_sr_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('#filter_bill_no').val() != "" && $(this).find('.sr_amount').val() != "") {

			sr_weight += parseFloat((isNaN($(this).find('.sr_weight').val()) || $(this).find('.sr_weight').val() == '') ? 0 : $(this).find('.sr_weight').val());

			sr_amt += parseFloat((isNaN($(this).find('.sr_amount').val()) || $(this).find('.sr_amount').val() == '') ? 0 : $(this).find('.sr_amount').val());

		}

	});

	$(".summary_sale_weight").html(parseFloat(sale_weight).toFixed(3));

	$(".summary_sale_amt").html(parseFloat(sale_rate).toFixed(2));

	//	$(".summary_gift_voucher_amt").html(gift_voucher);

	//$(".summary_gift_amt").val(gift_voucher);

	//$(".summary_chit_amt").html(chit_amt);

	$(".summary_chit_weight").html(parseFloat(saved_weight).toFixed(3));

	$(".summary_chit_amt").html(parseFloat(chit_amt).toFixed(2));

	$(".summary_sr_weight").html(parseFloat(sr_weight).toFixed(3));

	$(".summary_sr_amt").html(parseFloat(sr_amt).toFixed(2));

	calculateFinalCost();

}

function calculateFinalCost() {

	var purchase_amt = 0;

	var sales_amt = 0;

	var stone_amt = 0;

	var material_amt = 0;

	var gift_voucher_amt = 0;

	var chit_amt = 0;

	var discount = 0;

	var adv_paid_amt = 0;

	var chit_paid_amt = 0;

	var sales_ret_Amt = 0;

	purchase_amt = parseFloat((isNaN($('.summary_pur_amt').html()) || $('.summary_pur_amt').html() == '') ? 0 : parseFloat($('.summary_pur_amt').html()));

	sales_amt = parseFloat((isNaN($('.summary_sale_amt').html()) || $('.summary_sale_amt').html() == '') ? 0 : parseFloat($('.summary_sale_amt').html()));

	stone_amt = parseFloat((isNaN($('.summary_stone_amt').html()) || $('.summary_stone_amt').html() == '') ? 0 : parseFloat($('.summary_stone_amt').html()));

	adv_paid_amt = ($('.summary_adv_paid_amt').html() != '' || isNaN($('.summary_adv_paid_amt').html()) ? parseFloat($('.summary_adv_paid_amt').html()) : 0);

	chit_paid_amt = ($('.summary_chit_amt').html() != '' || isNaN($('.summary_chit_amt').html()) ? parseFloat($('.summary_chit_amt').html()) : 0);

	sales_ret_Amt = ($('.summary_sr_amt').html() != '' || isNaN($('.summary_sr_amt').html()) ? parseFloat($('.summary_sr_amt').html()) : 0);

	tot_purchase = parseFloat(purchase_amt + stone_amt + material_amt);

	tot_sale = parseFloat(sales_amt + gift_voucher_amt + chit_paid_amt + sales_ret_Amt);

	tot_cost = parseFloat(tot_purchase - tot_sale - discount - adv_paid_amt ).toFixed(2)

	$(".total_cost").val(tot_cost);

	$("#est_actual_final_cost").val(tot_cost);

	if (tot_cost > 0) {

		$(".total_cost").attr("readonly", false);

	}

}

$('#summary_discount_amt').on('change', function () {

	var discount_amt = $('#summary_discount_amt').val();

	var total_cost = $(".total_cost").val();

	if (parseFloat(total_cost) < parseFloat(discount_amt)) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Discount Amount is Greater Than The Final Amount' });

		$('#summary_discount_amt').val(0);

	}

	calculatetag_SaleValue();

});

$(".total_cost").on('change', function (key, items) {

	var discount_amt = $('#summary_discount_amt').val();

	var total_cost = $(".total_cost").val();

	var est_actual_final_cost = $("#est_actual_final_cost").val();

	var discount_amount = 0;

	if (parseFloat(est_actual_final_cost) < parseFloat(total_cost)) {

		$(".total_cost").val(est_actual_final_cost);

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'The Final Amount Grater Than The Actual Cost..' });

		$('#summary_discount_amt').val(0);

	}

	else {

		if (parseFloat(total_cost) < parseFloat(discount_amt)) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Discount Amount is Greater Than The Final Amount' });

			$('#summary_discount_amt').val(0);

		}

		else {

			discount_amount = parseFloat(parseFloat(est_actual_final_cost) - parseFloat(total_cost)).toFixed(2);

			$('#summary_discount_amt').val(discount_amount);

		}

	}

	calculatetag_SaleValue();

});

function get_estimation_list(from_date, to_date) {

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/estimation/ajax?nocache=" + my_Date.getUTCSeconds(),

		dataType: "JSON",

		type: "POST",

		data: { 'id_branch': (($('#branch_select').val() != '' && $('#branch_select').val() != '' && $('#branch_select').val() != undefined) ? $('#branch_select').val() : $('#branch_filter').val()), 'from_date': from_date, 'to_date': to_date },

		success: function (data) {

			set_estimation_list(data);

			$("div.overlay").css("display", "none");

		},

		error: function (error) {

			$("div.overlay").css("display", "none");

		}

	});

}

/*function set_estimation_list(data)

{

   $("div.overlay").css("display", "none");

   var estimation = data.list;

   var access = data.access;

   var oTable = $('#estimation_list').DataTable();

   $("#total_estimation").text(estimation.length);

	if(access.add == '0')

	 {

		$('#add_estimation').attr('disabled','disabled');

	 }

	 oTable.clear().draw();

		   if (estimation!= null && estimation.length > 0)

	 {

		  oTable = $('#estimation_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true,

			"bSort": true,

			"order": [[ 0, "desc" ]],

			"dom": 'lBfrtip',

			"buttons" : ['excel','print'],

			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

			"aaData": estimation,

			"aoColumns": [{ "mDataProp": "estimation_id" },

						{ "mDataProp": "estimation_datetime" },

						{ "mDataProp": "firstname" },

						{ "mDataProp": "esti_for" },

						{ "mDataProp": "total_cost" },

						{ "mDataProp": "item_type" },

						{ "mDataProp": function ( row, type, val, meta ) {

							 id= row.estimation_id

							 edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_estimation/estimation/edit/'+id : '#' );

							 print_url=base_url+'index.php/admin_ret_estimation/generate_invoice/'+id;

							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_estimation/estimation/delete/'+id : '#' );

							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');

							 action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a><a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a><a href="'+print_url+'" target="_blank"  class="btn btn-info btn-print" data-toggle="tooltip" title="Customer Copy"><i class="fa fa-print" ></i></a>';

							return action_content;

						}

					}]

		});

	}

}*/

function set_estimation_list(data) {

	$("div.overlay").css("display", "none");

	var estimation = data.list;

	var access = data.access;

	var oTable = $('#estimation_list').DataTable();

	$("#total_estimation").text(estimation.length);

	if (access.add == '0') {

		$('#add_estimation').attr('disabled', 'disabled');

	}

	oTable.clear().draw();

	if (estimation != null && estimation.length > 0) {

		oTable = $('#estimation_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true,

			"bSort": true,

			"order": [[0, "desc"]],

			"dom": 'lBfrtip',

			"buttons": ['excel', 'print'],

			"tableTools": { "buttons": [{ "sExtends": "xls", "oSelectorOpts": { page: 'current' } }, { "sExtends": "pdf", "oSelectorOpts": { page: 'current' } }] },

			"aaData": estimation,

			"aoColumns": [

				{ "mDataProp": "esti_no" },

				{ "mDataProp": "emp_name" },

				{ "mDataProp": "emp_code" },

				{ "mDataProp": "estimation_datetime" },

				{

					"mDataProp": function (row, type, val, meta) {

						return row.firstname;

					},

				},

				{ "mDataProp": "mobile" },

				{ "mDataProp": "total_cost" },

				{

					"mDataProp": function (row, type, val, meta) {

						return row.product_name;

					},

				},

				{

					"mDataProp": function (row, type, val, meta) {

						if (row.bill_no != null &&  row.bill_id != null) {

							var url = base_url + 'index.php/admin_ret_billing/billing_invoice/' + row.bill_id;

							return '<a href=' + url + ' target="_blank">' + row.bill_no + '</a>';

						} else {

							return '-';

						}

					},

				},

				{ "mDataProp": "added_through" },

				{ "mDataProp": "rating" },

				{ "mDataProp": "review" },

				{ "mDataProp": "suggestion" },

				{

					"mDataProp": function (row, type, val, meta) {

						id = row.estimation_id

						edit_url = (access.edit == '1' ? base_url + 'index.php/admin_ret_estimation/estimation/edit/' + id : '#');

						print_url = base_url + 'index.php/admin_ret_estimation/generate_invoice/' + id;

						brief_copy = base_url + 'index.php/admin_ret_estimation/generate_brief_copy/' + id;

						delete_url = (access.delete == '1' ? base_url + 'index.php/admin_ret_estimation/estimation/delete/' + id : '#');

						delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');

						action_content = ((row.bill_id == null || row.bill_no == null ) &&  row.chit_util_id == '' &&  access.edit == '1' ? '<a href="' + edit_url + '" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>' : '') + '<a href="' + print_url + '" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Customer Copy"><i class="fa fa-print" ></i></a>';

						// <a href="#" class="btn btn-danger btn-del" data-href=' + delete_url + ' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';

						return action_content;

					}

				}]

		});

	}

}

function get_metal() {

	$(".overlay").css('display', 'block');

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/get/active_metals',

		dataType: 'json',

		success: function (data) {

			var id_metal = $('#id_metal').val();

			$.each(data, function (key, item) {

				$('#metal_select').append(

					$("<option></option>")

						.attr("value", item.id_metal)

						.text(item.metal)

				);

			});

			$("#metal_select").select2({

				placeholder: "Select metal",

				allowClear: true

			});

			$("#metal_select").select2("val", (id_metal != '' && id_metal > 0 ? id_metal : ''));

			$(".overlay").css("display", "none");

		}

	});

}

//on selecting subcategory

$('#metal_select').select2()

	.on("change", function (e) {

		if (this.value != '') {

			$("#id_metal").val(this.value);

		}

	});

function get_received_lots() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_tagging/get_lot_ids',

		dataType: 'json',

		success: function (data) {

			lot_details = data;

			var id = $('#tag_lot_id').val();

			$.each(data, function (key, item) {

				$("#tag_lot_received_id").append(

					$("<option></option>")

						.attr("value", item.lot_no)

						.text(item.lot_no)

				);

			});

			$("#tag_lot_received_id").select2("val", (id != '' && id > 0 ? id : ''));

			$(".overlay").css("display", "none");

		}

	});

}

function get_tag_types() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_tagging/get_tag_types',

		dataType: 'json',

		success: function (data) {

			var id = $('#tag_type').val();

			$.each(data, function (key, item) {

				$("#select_tag_type").append(

					$("<option></option>")

						.attr("value", item.tag_id)

						.text(item.tag_name)

				);

			});

			$("#select_tag_type").select2("val", (id != '' && id > 0 ? id : ''));

			$(".overlay").css("display", "none");

		}

	});

}

function get_tag_purities() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_catalog/purity/active_purities',

		dataType: 'json',

		success: function (data) {

			purities = data;

		}

	});

}

function get_tag_matels() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_estimation/getMetalTypes',

		dataType: 'json',

		success: function (data) {

			matel_types = data;

		}

	});

}

function get_tag_taxgroups() {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_tagging/getAvailableTaxGroups',

		dataType: 'json',

		success: function (data) {

			var id = $('#tax_group_id').val();

			$.each(data, function (key, item) {

				$("#select_tax_group_id").append(

					$("<option></option>")

						.attr("value", item.tgrp_id)

						.text(item.tgrp_name)

				);

			});

			$("#select_tax_group_id").select2("val", (id != '' && id > 0 ? id : ''));

			$(".overlay").css("display", "none");

		}

	});

}

function getDesignPurityDetails(designId) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_tagging/getDesignPurityByDesignId/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'designId': designId },

		success: function (data) {

			var id = $('#purity').val();

			if (data.length > 0) {

				$("#select_purity").html('');

				$.each(data, function (key, item) {

					$("#select_purity").append(

						$("<option></option>")

							.attr("value", item.des_pur_id)

							.text(item.purity)

					);

				});

			} else {

				$("#select_purity").html('');

			}

			$("#select_purity").select2("val", (id != '' && id > 0 ? id : ''));

			$(".overlay").css("display", "none");

		}

	});

}

function getDesignStoneDetails(designId) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_tagging/getDesignStonesByDesignId/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'designId': designId },

		success: function (data) {

			tag_design_stones = data;

			/* console.log(data);

			var html = '';

			$.each(data, function(key, item){

				html += '<tr><td><input type="hidden" name="tagstone[stone_id][]" value="'+item.stone_id+'" />'+item.stone_name+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagstone[pcs][]" value="'+item.stone_pcs+'" required /></div></td><td><div class="input-group"><input type="number" class="form-control" step="any" name="tagstone[weight][]" value="" required /></div></td><td><input type="hidden" name="tagstone[uom_id][]" value="'+item.uom_id+'" />'+item.uom_short_code+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagstone[amount][]" value="" required /></div></td></tr>';

			});

			$('#tagging_stone_details tbody').append(html); */

		}

	});

}

function getDesignMaterialsByDesignId(designId) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_tagging/getDesignMaterialsByDesignId/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'designId': designId },

		success: function (data) {

			tag_design_materials = data;

			/* console.log(data);

			var html = '';

			$.each(data, function(key, item){

				html += '<tr><td><input type="hidden" name="tagmaterials[material_id][]" value="'+item.material_id+'" required />'+item.material_name+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagmaterials[weight][]" value="" required /></div></td><td><input type="hidden" name="tagmaterials[uom_id][]" value="'+item.uom_id+'" />'+item.uom_short_code+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagmaterials[amount][]" value="" required /></div></td><td></td></tr>';

			});

			$('#tagging_material_details tbody').append(html); */

		}

	});

}

function load_tag_stone_list_on_edit() {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_tagging/getTagStoneByTagId/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'tagId': $("#tag_id").val() },

		success: function (data) {

			var html = '';

			$.each(data, function (key, item) {

				html += '<tr><td><input type="hidden" name="tagmaterials[material_id][]" value="' + item.material_id + '" required />' + item.material_name + '</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagmaterials[weight][]" value="" required /></div></td><td><input type="hidden" name="tagmaterials[uom_id][]" value="' + item.uom_id + '" />' + item.uom_short_code + '</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagmaterials[amount][]" value="" required /></div></td><td></td></tr>';

			});

			$('#tagging_material_details tbody').append(html);

		}

	});

}

//26.03.2020

$(document).on('change', ".partial", function () {

	if ($(this).is(":checked")) {

		var act_mc_value = $(this).closest('tr').find('.act_mc_value').val();

		$(this).closest('tr').find('.is_partial').val(1);

		$(this).closest('tr').find('.gwt').prop('readonly', false);

		//$(this).closest('tr').find('.lwt').prop('readonly',false);

		$(this).closest('tr').find('.piece').prop('readonly', false);

		$(this).closest('tr').find('.mc_value').val(act_mc_value);

	}

	else {

		var act_gwt = $(this).closest('tr').find('.act_gwt').val();

		var act_mc_value = $(this).closest('tr').find('.act_mc_value').val();

		var less_wt = $(this).closest('tr').find('.lwt').val();

		$(this).closest('tr').find('.is_partial').val(0);

		$(this).closest('tr').find('.gwt').prop('readonly', true);

		$(this).closest('tr').find('.lwt').prop('readonly', true);

		$(this).closest('tr').find('.gwt').val(act_gwt);

		$(this).closest('tr').find('.cur_gwt').val(act_gwt);

		$(this).closest('tr').find('.mc_value').val(act_mc_value);

		$(this).closest('tr').find('.mc').html(act_mc_value);

		$(this).closest('tr').find('.nwt').html(parseFloat(act_gwt - less_wt).toFixed(3));

		$(this).closest('tr').find('.tot_nwt').val(parseFloat(act_gwt - less_wt).toFixed(3));

		$(this).closest('tr').find('.piece').prop('readonly', true);



		var row = $(this).closest('tr');



		if(tag_split_details){

    	    $.each(tag_split_details,function(key,val){

    	        if(val.tag_id ==  row.find('.est_tag_id').val()){

    	            val.gross_wt = act_gwt;

    	            val.less_wt = less_wt;

    	            val.net_wt = parseFloat(act_gwt - less_wt).toFixed(3);

    	            stone_details = [];

    	            $.each(val.stone_details,function(key,val){

    	                val.stone_wt = val.act_stone_wt;

    	                val.pieces = val.act_stn_pcs;

    	                stone_details.push(val);

    	            });

    	            val.stone_details = stone_details;

    	        }

    	    });



    	    if(row.find('.stone_details').val()!='')

            {

                var tag_stone_details = JSON.parse(row.find('.stone_details').val());

	            if(tag_stone_details.length > 0){

	                var acttagstnDetails = [];

	                $.each(tag_stone_details,function(key,sitem){

                		acttagstnDetails.push({ "show_in_lwt": sitem.show_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.act_stn_pcs, "stone_wt": sitem.act_stone_wt,"act_stn_pcs": sitem.act_stn_pcs, "act_stone_wt": sitem.act_stone_wt, "stone_price": sitem.stone_price, "stone_rate": sitem.stone_rate, "stone_uom_id": sitem.stone_uom_id, "stone_cal_type": sitem.stone_cal_type });

	                });

	            }

	            row.find('.stone_details').val(JSON.stringify(acttagstnDetails));

            }



	    }



	}

	calculatetag_SaleValue();

});

$(document).on('change', ".non_tag", function () {

	if ($(this).is(":checked")) {

		$(this).closest('tr').find('.is_non_tag').val(1);

		$(this).closest('tr').find('.lot_no').prop('disabled', false);

	}

	else {

		$(this).closest('tr').find('.is_non_tag').val(0);

		$(this).closest('tr').find('.lot_no').prop('disabled', true);

	}

});

$(document).on('change', ".gwt", function () {

	var act_gwt = parseFloat($(this).closest('tr').find('.act_gwt').val());

	var gwt = parseFloat($(this).closest('tr').find('.gwt').val());

	if (parseFloat(act_gwt) > parseFloat(gwt)) {

		var less_wt = $(this).closest('tr').find('.lwt').val();

		var net_wt = parseFloat(gwt - (isNaN(less_wt) ? 0 : less_wt)).toFixed(3);

		$(this).closest('tr').find('.nwt').html(net_wt);

		$(this).closest('tr').find(".tot_nwt").val(net_wt);

		$(this).closest('tr').find(".cur_gwt").val(gwt);

		var row = $(this).closest('tr');



			if(tag_split_details){

        	    $.each(tag_split_details,function(key,val){

        	        if(val.tag_id ==  row.find('.est_tag_id').val()){

        	            val.gross_wt =  gwt;

        	            val.less_wt  =  less_wt;

    	                val.net_wt   =  net_wt;

        	        }

        	    });

	        }



	}

	else {

		alert('Maximum Gross weight Exceed');

		var less_wt = $(this).closest('tr').find('.lwt').val();

		var net_wt = parseFloat(gwt - (isNaN(less_wt) ? 0 : less_wt)).toFixed(3);

		$(this).closest('tr').find('.nwt').html(net_wt);

		$(this).closest('tr').find(".cur_gwt").val(parseFloat(act_gwt).toFixed(3));

		$(this).closest('tr').find(".gwt").val(parseFloat(act_gwt).toFixed(3));

		$(this).closest('tr').find(".gwt").focus();

		var row = $(this).closest('tr');

		if(tag_split_details){

        	    $.each(tag_split_details,function(key,val){

        	        if(val.tag_id ==  row.find('.est_tag_id').val()){

        	            val.gross_wt = act_gwt;

        	            val.less_wt  =  less_wt;

    	                val.net_wt   =  net_wt;

        	        }

        	    });

	        }

	}

	calculatetag_SaleValue();

});

$(document).on('change', ".lwt", function () {

	var gwt = $(this).closest('tr').find('.gwt').val();

	var less_wt = parseInt($(this).closest('tr').find('.lwt').val());

	if (less_wt < gwt) {

		var net_wt = parseFloat(gwt - less_wt).toFixed(4);

		$(this).closest('tr').find('.nwt').html(net_wt);

		$(this).closest('tr').find(".tot_nwt").val(net_wt);

		var row = $(this).closest('tr');

		calculatetag_SaleValue();

	}

	else {

		$(this).closest('tr').find('.lwt').focus();

		$(this).closest('tr').find('.lwt').val('');

	}

});

$(document).on('change', ".act_mc_value", function () {
	var currentRow = $(this).closest('tr');

	let mc = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val());

	let act_mc = isNaN(parseFloat($(this).closest('tr').find(".mc_val").html())) ? 0 : parseFloat($(this).closest('tr').find(".mc_val").html());

	let mc_min = isNaN(parseFloat($(this).closest('tr').find(".mc_min_limit").val())) ? 0 : parseFloat($(this).closest('tr').find(".mc_min_limit").val());

	/*if (mc < mc_min) {

		$(this).val(act_mc);

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Minimum MC is ' + mc_min + '' });

	}*/

	let id_orderdetails = isNaN(parseFloat($(this).closest('tr').find('.id_orderdetails').val())) ? '' : parseFloat($(this).closest('tr').find('.id_orderdetails').val());

	console.log(id_orderdetails);

	if (id_orderdetails != '') {

		calculateOrderTag();

	}

	else {

		calculatetag_SaleValue();

	}

	var tagId = currentRow.find('.est_tag_id').val(); 
	var tagDetail = tag_split_details.find(function(detail) {
		return detail.tag_id === tagId;
	  });

	  if (tagDetail) {
		var newMcValue = parseFloat($(this).val());
		tagDetail.tag_mc_value = newMcValue;
  
		var retailMcValue = tagDetail.tag_mc_value;
  
		console.log('Retail Max Wastage Percent:', retailMcValue);
	  } else {
		console.log('Tag not found in tag_split_details.');
	  }

	if ($('#estimation_chit_details > tbody >tr').length > 0) {

		calculate_chit_closing_balance();

	}

});

function calculatetag_SaleValue() {

	$('#estimation_tag_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		if (curRow.find('.est_tag_id').val() != '') {

			var mc_type = 0;

			var wast_wgt = 0;

			var wast_amt = 0;

			var rate_with_mc = 0;

			var market_rate_with_mc = 0;

			var market_total_price = 0;

			var total_price = 0;

			var stone_price = 0;

			var material_price = 0;

			var tax_rate = 0;

			var total_tax_rate = 0;

			var market_total_tax_rate = 0;

			var base_value_tax = 0;

			var arrived_value_tax = 0;

			var base_value_amt = 0;

			var arrived_value_amt = 0;

			var market_base_value_tax = 0;

			var market_arrived_value_tax = 0;

			var market_base_value_amt = 0;

			var market_arrived_value_amt = 0;

			var stone_price = 0; // Not worked

			var tot_stone_wt = 0; // Not worked

			var certification_price = 0; // Not worked

			var stone_wt = 0;

			var tag_other_itm_amount = 0;

			var tag_other_itm_weight = 0;

			var disc_amt = $('#summary_discount_amt').val();

			var total_sales_amt = $('.summary_pur_amt').html();

			var discount = 0;

			var cus_del_country = $('#cus_del_country').val();

			var cmp_country = $('#cmp_country').val();

			var cus_del_state = $('#cus_del_state').val();

			var cmp_state = $('#cmp_state').val();

			var cgst = 0;

			var igst = 0;

			var sgst = 0;

			var is_eda_est = $('#iseda').val();

			var stn_price = 0;

			var stone_amt = 0;

			var dia_amt = 0;

			var board_rate = 0;

			var stone_details = curRow.find('.stone_details').val();

			if (stone_details) {

				var st_details = JSON.parse(stone_details);

				if (st_details.length > 0) {

					$.each(st_details, function (pkey, pitem) {

						$.each(uom_details, function (key, item) {

							if (item.uom_id == pitem.stone_uom_id) {

								if (pitem.show_in_lwt == 1) {

									if ((item.uom_short_code == 'CT') && (item.divided_by_value != null && item.divided_by_value != '')) //For Carat Need to convert into gram

									{

										stone_wt = parseFloat(parseFloat(pitem.stone_wt) / parseFloat(item.divided_by_value));

									} else {

										stone_wt = pitem.stone_wt;

									}

									tot_stone_wt += parseFloat(stone_wt);

								}

								if(pitem.stone_cal_type==1) // by wt

								{

									stone_price += (parseFloat(pitem.stone_wt) * parseFloat(pitem.stone_rate));

									stn_price = (parseFloat(pitem.stone_wt) * parseFloat(pitem.stone_rate));

									if(pitem.stones_type == 1){
										dia_amt += stn_price;
									}
									else{
										stone_amt += stn_price;
									}

								}

								else

								{

									stone_price += (parseFloat(pitem.stone_pcs) * parseFloat(pitem.stone_rate));

									stn_price = (parseFloat(pitem.stone_pcs) * parseFloat(pitem.stone_rate));

									if(pitem.stones_type == 1){
										dia_amt += stn_price;
									}
									else{
										stone_amt += stn_price;
									}

								}

							}

						});

					});

				}

			}

			curRow.find('.lwt').val(parseFloat(tot_stone_wt).toFixed(3));

			var other_metal_details_str = curRow.find('.other_metal_details').val();

			if (other_metal_details_str != '') {
				try {
					var parsedData;
					
					// Smart detection: Check if it's Base64 or regular JSON
					if (isLikelyBase64(other_metal_details_str)) {
						// Edit screen - Base64 encoded data
						parsedData = JSON.parse(atob(other_metal_details_str));
					} else {
						// Add screen - Regular JSON data
						parsedData = JSON.parse(other_metal_details_str);
					}
					
					other_metal_details = parsedData;
				console.log(other_metal_details.length);

				if (other_metal_details.length > 0) {

					$.each(other_metal_details, function (key, items) {

						tag_other_itm_weight += parseFloat(items.tag_other_itm_grs_weight);

					});

					tag_other_itm_amount = calculate_other_metal_amount(other_metal_details, curRow);

				}
				} catch (e) {
					console.error('JSON parsing error:', e);
					console.error('Problematic string:', other_metal_details_str);
				}
			}

			var piece = (isNaN(curRow.find('.piece').val()) || curRow.find('.piece').val()=='') ? 0 :curRow.find('.piece').val();

			var gross_wt = (isNaN(curRow.find('.gwt').val()) || curRow.find('.gwt').val() == '') ? 0 : curRow.find('.gwt').val();

			var less_wt = (isNaN(curRow.find('.lwt').val()) || curRow.find('.lwt').val() == '') ? 0 : curRow.find('.lwt').val();

			var net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(less_wt) - parseFloat(tag_other_itm_weight)).toFixed(3);

			curRow.find('.tot_nwt').val(net_wt);

			curRow.find('.nwt').html(net_wt);

			var net_wt = (isNaN(curRow.find('.tot_nwt').val()) || curRow.find('.tot_nwt').val() == '') ? 0 : parseFloat(curRow.find('.tot_nwt').val()).toFixed(3);

			var calculation_type = (isNaN(curRow.find('.caltype').val()) || curRow.find('.caltype').val() == '') ? 0 : curRow.find('.caltype').val();

			var piece = (isNaN(curRow.find('.piece').val()) || curRow.find('.piece').val() == '') ? 1 : curRow.find('.piece').val();

			var metal_type = (isNaN(curRow.find('.metal_type').val()) || curRow.find('.metal_type').val() == '') ? 1 : curRow.find('.metal_type').val();

			var tax_group = curRow.find('.tax_group_id').val();

			var product_type = curRow.find('.stone_type').val();  // 0 -> ornaments, 1-> stone , 2 -> Diamond

			var stone_calc_type		= (!isNaN($('.stone_calculation_based_on').val()) && $('.stone_calculation_based_on').val() != '' && $('.stone_calculation_based_on').val() > 0)  ? $('.stone_calculation_based_on').val() : 0;  // 1 -> wt , 2 -> pcs

			let loose_prod_stone_rate = 0;

			if(product_type!=0)

			{

				var prod_uom_id = (curRow.find('.uom_id').val()!='' ? curRow.find('.uom_id').val() : 1);

				let prod_st_wt = 0;

				if(stone_calc_type == 2) // pcs

				{

					loose_prod_stone_rate = parseFloat(curRow.find('.act_sales_value').val()) * parseFloat(piece);

				}

				else

				{

					/*$.each(uom_details,function(key,item)

					{

						if(item.uom_id==prod_uom_id)

						{

							let divided_by_value = 1;

							if(item.divided_by_value > 0) {

								divided_by_value = item.divided_by_value;

							}

							prod_st_wt = parseFloat(net_wt) / parseFloat(divided_by_value);

							return false;

						}

					});*/

					loose_prod_stone_rate = parseFloat(curRow.find('.act_sales_value').val()) * parseFloat(net_wt);

				}

			}

			market_rate_field = curRow.find('.market_rate_field').val();

			var rate_field = curRow.find('.rate_field').val();

			var rate_field_value = (rate_field != null && rate_field != '' && rate_field != 'null' && !isNaN($('.'+rate_field).html()) && $('.'+rate_field).html() != '')  ? $('.'+rate_field).html() : 0;

			rate_per_grm = isNaN(parseFloat(curRow.find('.market_rate_value').val())) ? 0 : parseFloat(curRow.find('.market_rate_value').val());
			
			if(($('#wastage_rate_type').val() == 2 && !($("#manual_rate").is(":checked")) && rate_field_value == curRow.find('.market_rate_value').val())){
				
				board_rate =  parseFloat(curRow.find('.market_rate_value').val());
				
			}
			else {
			
				board_rate = rate_field_value
			}



			if (market_rate_field != '') {

				market_rate_per_grm = (isNaN($('.' + market_rate_field).html()) || $('.' + market_rate_field).html() == '') ? 0 : parseFloat($('.' + market_rate_field).html());

			}

			var tot_wastage = (isNaN(curRow.find('.wastage_max_per').val()) || curRow.find('.wastage_max_per').val() == '') ? 0 : curRow.find('.wastage_max_per').val();

			var retail_max_mc = (isNaN(curRow.find('.act_mc_value').val()) || curRow.find('.act_mc_value').val() == '') ? 0 : curRow.find('.act_mc_value').val();

			var certification_price = (isNaN(curRow.find('.certification_price').val()) || curRow.find('.certification_price').val() == '') ? 0 : curRow.find('.certification_price').val();

			var cur_id_mc_type = (isNaN(curRow.find('.id_mc_type').val()) || curRow.find('.id_mc_type').val() == '') ? 0 : curRow.find('.id_mc_type').val();

			if (curRow.find('.parent_tag_id').val() != '' && curRow.find('.parent_tag_id').val() != undefined) { //Tag Merge

				if (curRow.find('.parent_tag_id').val() != '') {

					var parent_tag_id = curRow.find('.parent_tag_id').val();

					var child_item_emp_id = '';

					$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

						if ($(this).find('.est_tag_id').val() != '') {

							if ($(this).find('.est_tag_id').val() == parent_tag_id) {

								var current_row = $(this)

								tot_wastage = (isNaN(current_row.find('.wastage_max_per').val()) || current_row.find('.wastage_max_per').val() == '') ? 0 : current_row.find('.wastage_max_per').val();

								retail_max_mc = (isNaN(current_row.find('.act_mc_value').val()) || current_row.find('.act_mc_value').val() == '') ? 0 : current_row.find('.act_mc_value').val();

								child_item_emp_id = (isNaN(current_row.find('.item_emp_id').val()) || current_row.find('.item_emp_id').val() == '') ? 0 : current_row.find('.item_emp_id').val();

							}

						}

					});

				}

			}

			/**

			*	Amount calculation based on settings (without discount and tax )

			*   0 - Wastage on Gross weight And MC on Gross weight

			*   1 - Wastage on Net weight And MC on Net weight

			*   2 - Wastage On Netwt And MC On Grwt

			*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC

			*/

			console.log('gross_wt:' + gross_wt);

			console.log('net_wt:' + net_wt);

			console.log('retail_max_mc:' + retail_max_mc);

			if (calculation_type == 0) {

				wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

				wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

				if (curRow.find('.id_mc_type').val() != 3) {

					mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) : parseFloat(retail_max_mc * 1));

					// Metal Rate + Stone + OM + Wastage + MC

					rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

					market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

				} else {

					mc_type = retail_max_mc;

					rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

					market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_amt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				}

			}

			else if (calculation_type == 1) {

				var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

				wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

				if (curRow.find('.id_mc_type').val() != 3) {

					var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * 1));

					// Metal Rate + Stone + OM + Wastage + MC

					rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

					market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

				} else {

					mc_type = retail_max_mc;

					rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

					market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				}

			}

			else if (calculation_type == 2) {

				var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

				wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

				if (curRow.find('.id_mc_type').val() != 3) {

					var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) : parseFloat(retail_max_mc * 1));

					// Metal Rate + Stone + OM + Wastage + MC

					rate_with_mc = parseFloat(parseFloat((parseFloat(rate_per_grm) * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(certification_price)).toFixed(2);

					market_rate_with_mc = parseFloat((parseFloat(market_rate_per_grm) * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(certification_price);

				} else {

					mc_type = retail_max_mc;

					rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

					market_rate_with_mc = parseFloat((parseFloat(market_rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				}

			}

			else if (calculation_type == 3) {

				rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val());

				market_rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val());

			 }

			else if (calculation_type == 4)

			{

				if (curRow.find('.id_mc_type').val() != 3) {

					mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * 1));



				} else {

					mc_type = retail_max_mc;

				}

				if(product_type==0)

				{

					rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val() *  gross_wt);

					market_rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val() *  gross_wt);

				}

				else

				{

					rate_with_mc = parseFloat(loose_prod_stone_rate);

					market_rate_with_mc = parseFloat(loose_prod_stone_rate);

				}

				rate_with_mc = parseFloat(parseFloat(rate_with_mc) + parseFloat(mc_type)).toFixed(2);

			}

			let charge_value = isNaN(parseFloat(curRow.find('.charge_value').val())) ? 0 : curRow.find('.charge_value').val();

			let rate_with_charges = parseFloat(rate_with_mc) + parseFloat(charge_value) + parseFloat(tag_other_itm_amount.tot_amount || 0);

			if (disc_amt > 0 && disc_amt != '') {

				var disc_per = parseFloat((disc_amt / total_sales_amt) * 100);

				discount = parseFloat((rate_with_charges * disc_per) / 100);

				rate_with_charges = parseFloat(rate_with_charges - discount).toFixed(2);

			}

			var tax_type = '';

			$.each(prod_details, function (k, val) {

				if (val.pro_id == curRow.find('.pro_id').val()) {

					tax_type = val.tax_type;

				}

			});

            if(is_eda_est==0)

        	{

					if (tax_details.length > 0) {

						// Tax Calculation

						var base_value_tax = parseFloat(calculate_base_value_tax(rate_with_charges, tax_group)).toFixed(2);

						var base_value_amt = parseFloat(parseFloat(rate_with_charges) + parseFloat(base_value_tax)).toFixed(2);

						var arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

						var arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

						total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

						var market_base_value_tax = parseFloat(calculate_base_value_tax(market_rate_with_mc, tax_group)).toFixed(2);

						var market_base_value_amt = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);

						var market_arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

						var market_arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

						market_total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

					}

					total_price = parseFloat(parseFloat(rate_with_charges) + parseFloat(total_tax_rate)).toFixed(2);

					market_total_price = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(market_total_tax_rate)).toFixed(2);

			}

			else

			{

				total_price=rate_with_charges;

				market_total_price=rate_with_charges;

				total_tax_rate=0;

			}

			if ((calculation_type == 3 || calculation_type == 4) && (tax_type == 1)) //tax_type 1-Inclusive ,2-Exclusive

			{

				total_price = rate_with_charges;

				var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_charges, tax_group)).toFixed(2);

			}

			// cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			// sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			if (cus_del_country == cmp_country) {

				if (cus_del_state == cmp_state) {

					cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

					sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

				}

				else {

					cgst = 0; sgst = 0;

					igst = total_tax_rate;

				}

			}

			else if (cus_del_country == '') {

				cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

				sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			}

			else // NO GST

			{

				cgst = 0; sgst = 0; igst = 0;

				inclusive_tax_rate = rate_with_mc;

			} 

			if (curRow.find('.parent_tag_id').val() != ''  && curRow.find('.parent_tag_id').val()!= undefined) { //Tag Merge

				curRow.find('.wastage_max_per').val(tot_wastage);

				curRow.find('.act_mc_value').val(retail_max_mc);

				curRow.find('.tax_price').val(total_tax_rate);

				curRow.find('.market_rate_tax').val(market_total_tax_rate);

				curRow.find('.est_wastage_wt').val(parseFloat(wast_wgt).toFixed(3));

				curRow.find('.item_emp_id').val(child_item_emp_id);

				var parent_tag_id = curRow.find('.parent_tag_id').val();

				$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

					if ($(this).find('.est_tag_id').val() != '') {

						if ($(this).find('.est_tag_id').val() == parent_tag_id) {

							var cur_row = $(this);

							var child_data = JSON.parse(cur_row.find('#child_tag_details').val());

							$.each(child_data, function (idx, rowObj) {

								if (rowObj.tag_id == curRow.find('.est_tag_id').val()) {

									rowObj.cost = total_price;

									rowObj.wastage = tot_wastage;

									rowObj.mc = retail_max_mc;

									rowObj.tax_price = total_tax_rate;

									rowObj.item_emp_id = child_item_emp_id;

									rowObj.id_mc_type = cur_id_mc_type;

								}

							});

							cur_row.find('#child_tag_details').val(JSON.stringify(child_data));

						}

					}

				});

			}

			curRow.find('.sale_cgst').val(cgst);

			curRow.find('.sale_sgst').val(sgst);

			curRow.find('.sale_igst').val(igst);

			curRow.find('.sale_taxable_amount').val(rate_with_charges);

			curRow.find('.tax_price').val(total_tax_rate);

			curRow.find('.market_rate_tax').val(market_total_tax_rate);

			curRow.find('.discount_amount').val(parseFloat(discount).toFixed(2));

			curRow.find('.cost').html(total_price);

			curRow.find('.sales_value').val(total_price);

			curRow.find('.sales_value').val(total_price);

			curRow.find('.est_wastage_wt').val(parseFloat(wast_wgt).toFixed(3));

			curRow.find('.tot_mc').html(parseFloat(mc_type).toFixed(2));

			curRow.find('.tot_stn_amt').val(parseFloat(stone_amt).toFixed(2));

			curRow.find('.tot_dia_amt').val(parseFloat(dia_amt).toFixed(2));

			//curRow.find('.mc').html(parseFloat(mc_type).toFixed(2));

			//curRow.find('.act_mc_value').val(parseFloat(mc_type).toFixed(2));

			console.log('Total Price :' + total_price);

			console.log('rate_with_mc :' + rate_with_mc);

			console.log('tag_other_itm_amount :' + tag_other_itm_amount);

			console.log('rate_with_charges :' + rate_with_charges);

			console.log('wast_wgt :' + wast_wgt);

			console.log('charge_value :' + charge_value);

			console.log('tot_wastage :' + tot_wastage);

			console.log('calculation_type :' + calculation_type);

			console.log('gross_wt :' + gross_wt);

			console.log('gross_wt :' + gross_wt);

			console.log('stone_price :' + stone_price);

			console.log('mc_type :' + mc_type);

			console.log('rate_per_grm :' + rate_per_grm);

			console.log('retail_max_mc :' + retail_max_mc);

			console.log('total_tax_rate :' + total_tax_rate);

			console.log('---------------');

		}

	});

	calculate_purchase_details();

	calculate_sales_details();

	calculate_chit_closing_balance();

}

function calculate_other_metal_amount(other_metal_details, curRow) {

	var tot_amount = 0;

	var rate_per_gram = 0;

	var tag_other_metal_details = [];

	$.each(other_metal_details, function (key, items) {

		$.each(purity_rate, function (key, item) {

			var rate_field = '';

			if (item.id_metal == items.tag_other_itm_metal_id && item.id_purity == items.tag_other_itm_pur_id) {

				rate_field = item.rate_field;

				rate_per_gram = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

			}

		});

		var net_wt = items.tag_other_itm_grs_weight;

		var wastage_perc = items.tag_other_itm_wastage;

		var wast_wt = parseFloat((net_wt * wastage_perc) / 100);

		var mc_type = items.tag_other_itm_cal_type;

		var making_charge = items.tag_other_itm_mc;

		var mc_value = (mc_type == 1 ? parseFloat(net_wt * making_charge) : (mc_type == 2 ? parseFloat(making_charge) : 0));

		var total_amount = parseFloat(parseFloat(rate_per_gram) * parseFloat(parseFloat(net_wt) + parseFloat(wast_wt)) + parseFloat(mc_value)).toFixed(2);

		tot_amount += parseFloat(total_amount);

		console.log('wast_wt:' + wast_wt);

		console.log('mc_value:' + mc_value);

		console.log('total_amount:' + total_amount);

		console.log('oher_metal_rate_per_gram:' + rate_per_gram);

		tag_other_metal_details.push({

			"tag_other_itm_id": items.tag_other_itm_id,

			"tag_other_itm_tag_id": items.tag_other_itm_tag_id,

			"tag_other_itm_metal_id": items.tag_other_itm_metal_id,

			"tag_other_itm_pur_id": items.tag_other_itm_pur_id,

			"tag_other_itm_grs_weight": items.tag_other_itm_grs_weight,

			"tag_other_itm_wastage": items.tag_other_itm_wastage,

			"tag_other_itm_uom": items.tag_other_itm_uom,

			"tag_other_itm_cal_type": items.tag_other_itm_cal_type,

			"tag_other_itm_mc": items.tag_other_itm_mc,

			"tag_other_itm_rate": rate_per_gram,

			"tag_other_itm_pcs": items.tag_other_itm_pcs,

			"tag_other_itm_amount": total_amount,

			"purname": items.purname,

         "metal_name": items.metal_name,

		});

	});

	curRow.find('.other_metal_details').val(JSON.stringify(tag_other_metal_details));

	return {tot_amount:tot_amount,
		rate_per_gram : rate_per_gram
	};

}

function calculateOrderTag() {

	var adv_paid_amt = 0;

	var adv_paid_wt = 0;

	var total_weight = 0;

	var balance_weight = 0;

	var balance_pay_amt = 0;

	var average_rate = 0;

	var cus_del_country = $('#cus_del_country').val();

	var cmp_country = $('#cmp_country').val();

	var cus_del_state = $('#cus_del_state').val();

	var cmp_state = $('#cmp_state').val();

	$.each(order_adv_details, function (key, items) {

		if (items.store_as == 2) //Stored as Weight

		{

			adv_paid_amt += parseFloat(items.paid_weight) * parseFloat(items.rate_per_gram); // Convert Weight into Amount

			adv_paid_wt += parseFloat(items.paid_weight);

		} else if (items.store_as == 1) //Stored as Amount

		{

			adv_paid_amt += parseFloat(items.paid_advance);

			adv_paid_wt += parseFloat(items.paid_advance) / parseFloat(items.rate_per_gram); // Convert Amount into Amount

		}

	});

	adv_paid_wt = parseFloat(adv_paid_wt).toFixed(3);

	var order_rate_per_grm = 0;

	$('#estimation_tag_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var stone_details = curRow.find('.stone_details').val();

    	var stone_price = 0;

    	var tot_stone_wt = 0;

    	var stone_wt = 0;

		var cgst = 0;

		var igst = 0;

		var sgst = 0;

    	if (stone_details != '') {

    		var st_details = JSON.parse(stone_details);

    		if (st_details.length > 0) {

    			$.each(st_details, function (pkey, pitem) {

    				$.each(uom_details, function (key, item) {

    					if (item.uom_id == pitem.stone_uom_id) {

    						if (pitem.show_in_lwt == 1) {

    							if ((item.uom_short_code == 'CT') && (item.divided_by_value != null && item.divided_by_value != '')) //For Carat Need to convert into gram

    							{

    								stone_wt = parseFloat(parseFloat(pitem.stone_wt) / parseFloat(item.divided_by_value));

    							} else {

    								stone_wt = pitem.stone_wt;

    							}

    							tot_stone_wt += parseFloat(stone_wt);

    						}

    						stone_price += parseFloat(pitem.stone_price);

    					}

    				});

    			});

    		}

    	}

    	curRow.find('.lwt').val(parseFloat(tot_stone_wt).toFixed(3));

		rate_field = curRow.find('.rate_field').val();

		if(curRow.find('.ord_rate_type').val()==1 && curRow.find('.ord_rate_per_grm').val()!='')

		{

			order_rate_per_grm = curRow.find('.ord_rate_per_grm').val();

		}

		else if(rate_field != '') {

			order_rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

		}

		var gross_wt = (isNaN(curRow.find('.gwt').val()) || curRow.find('.gwt').val() == '') ? 0 : curRow.find('.gwt').val();

		var less_wt = (isNaN(curRow.find('.lwt').val()) || curRow.find('.lwt').val() == '') ? 0 : curRow.find('.lwt').val();

		var stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();

		var net_wt = parseFloat(gross_wt) - parseFloat(less_wt);

		var tot_wastage = (isNaN(curRow.find('.wastage_max_per').val()) || curRow.find('.wastage_max_per').val() == '') ? 0 : curRow.find('.wastage_max_per').val();

		var calculation_type = (isNaN(curRow.find('.caltype').val()) || curRow.find('.caltype').val() == '') ? 0 : curRow.find('.caltype').val();

		/**

		*	Amount calculation based on settings (without discount and tax )

		*   0 - Wastage on Gross weight And MC on Gross weight

		*   1 - Wastage on Net weight And MC on Net weight

		*   2 - Wastage On Netwt And MC On Grwt

		*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC

		*/

		if (calculation_type == 0) {

			total_weight += parseFloat((parseFloat(gross_wt) * parseFloat(tot_wastage / 100)) + parseFloat(gross_wt));

		}

		else if (calculation_type == 1) {

			total_weight += parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100) + parseFloat(net_wt));

		}

		else if (calculation_type == 2) {

			total_weight += parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100) + parseFloat(net_wt))

		}

	});

	balance_weight = parseFloat(parseFloat(total_weight) - parseFloat(adv_paid_wt)).toFixed(3);

	if (adv_paid_wt < total_weight) {

		var balance_pay_amt = parseFloat(parseFloat(order_rate_per_grm) * parseFloat(balance_weight)).toFixed(2);

	}

	average_rate = parseFloat(parseFloat(parseFloat(adv_paid_amt) + parseFloat(balance_pay_amt)) / parseFloat(total_weight)).toFixed(2);

	console.log('adv_paid_amt:' + adv_paid_amt);

	console.log('adv_paid_wt:' + adv_paid_wt);

	console.log('total_weight:' + total_weight);

	console.log('balance_weight:' + balance_weight);

	console.log('average_rate:' + average_rate);

	console.log('balance_pay_amt:' + balance_pay_amt);

	$('#estimation_tag_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var mc_type = 0;

		var wast_wgt = 0;

		var rate_with_mc = 0;

		var market_rate_with_mc = 0;

		var total_price = 0;

		var stone_price = 0;

		var material_price = 0;

		var tax_rate = 0;

		var total_tax_rate = 0;

		var market_total_tax_rate = 0;

		var base_value_tax = 0;

		var arrived_value_tax = 0;

		var base_value_amt = 0;

		var arrived_value_amt = 0;

		var market_base_value_tax = 0;

		var market_arrived_value_tax = 0;

		var market_base_value_amt = 0;

		var market_arrived_value_amt = 0;

		var rate_per_grm = 0;

		var market_rate_per_grm = 0;

		var igst = 0;

		var wast_amt = 0;

		var gross_wt = (isNaN(curRow.find('.gwt').val()) || curRow.find('.gwt').val() == '') ? 0 : curRow.find('.gwt').val();

		var less_wt = (isNaN(curRow.find('.lwt').val()) || curRow.find('.lwt').val() == '') ? 0 : curRow.find('.lwt').val();

		var stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();

		var net_wt = parseFloat(gross_wt) - parseFloat(less_wt);

		var calculation_type = (isNaN(curRow.find('.caltype').val()) || curRow.find('.caltype').val() == '') ? 0 : curRow.find('.caltype').val();

		var piece = (isNaN(curRow.find('.piece').val()) || curRow.find('.piece').val() == '') ? 1 : curRow.find('.piece').val();

		var metal_type = (isNaN(curRow.find('.metal_type').val()) || curRow.find('.metal_type').val() == '') ? 1 : curRow.find('.metal_type').val();

		var tax_group = curRow.find('.tax_group_id').val();

		//  loose product

		var product_type = curRow.find('.stone_type').val();  // 0 -> ornaments, 1-> stone , 2 -> Diamond

		var stone_calc_type		= (!isNaN($('.stone_calculation_based_on').val()) && $('.stone_calculation_based_on').val() != '' && $('.stone_calculation_based_on').val() > 0)  ? $('.stone_calculation_based_on').val() : 0;  // 1 -> wt , 2 -> pcs

		let loose_prod_stone_rate = 0;

		if(product_type!=0)

		{

			var prod_uom_id = curRow.find('.uom_id').val();

			let prod_st_wt = 0;

			if(stone_calc_type==1)

			{

				$.each(uom_details,function(key,item)

				{

					if(item.uom_id==prod_uom_id)

					{

						let divided_by_value = 1;

						if(item.divided_by_value > 0) {

							divided_by_value = item.divided_by_value;

						}

						prod_st_wt = parseFloat(net_wt) / parseFloat(divided_by_value);

						return false;

					}

				});

				loose_prod_stone_rate = parseFloat(curRow.find('.act_sales_value').val()) * parseFloat(prod_st_wt);

			}

			else if(stone_calc_type == 2)

			{

				loose_prod_stone_rate = parseFloat(curRow.find('.act_sales_value').val()) * parseFloat(piece);

			}

		}

		//  loose product

		var stone_details = curRow.find('.stone_details').val();

    	var tot_stone_wt = 0;

    	var stone_wt = 0;

    	if (stone_details != '') {

    		var st_details = JSON.parse(stone_details);

    		if (st_details.length > 0) {

    			$.each(st_details, function (pkey, pitem) {

    				$.each(uom_details, function (key, item) {

    					if (item.uom_id == pitem.stone_uom_id) {

    						if (pitem.show_in_lwt == 1) {

    							if ((item.uom_short_code == 'CT') && (item.divided_by_value != null && item.divided_by_value != '')) //For Carat Need to convert into gram

    							{

    								stone_wt = parseFloat(parseFloat(pitem.stone_wt) / parseFloat(item.divided_by_value));

    							} else {

    								stone_wt = pitem.stone_wt;

    							}

    							tot_stone_wt += parseFloat(stone_wt);

    						}

    						stone_price += parseFloat(pitem.stone_price);

    					}

    				});

    			});

    		}

    	}

		rate_field = curRow.find('.rate_field').val();

		market_rate_field = curRow.find('.market_rate_field').val();

		if(curRow.find('.ord_rate_type').val()==1 && curRow.find('.ord_rate_per_grm').val()!=''){

			rate_per_grm = curRow.find('.ord_rate_per_grm').val();

		}else{

			rate_per_grm = curRow.find('.market_rate_value').val();

		}

		if (market_rate_field != '') {

			market_rate_per_grm = average_rate

			//market_rate_per_grm = (isNaN($('.'+market_rate_field).html()) ||$('.'+market_rate_field).html() == '')  ? 0 : parseFloat($('.'+market_rate_field).html());

		}

		var tot_wastage = (isNaN(curRow.find('.wastage_max_per').val()) || curRow.find('.wastage_max_per').val() == '') ? 0 : curRow.find('.wastage_max_per').val();

		var retail_max_mc = (isNaN(curRow.find('.act_mc_value').val()) || curRow.find('.act_mc_value').val() == '') ? 0 : curRow.find('.act_mc_value').val();

		//var stone_price = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '')  ? 0 : curRow.find('.stone_price').val();

		var certification_price = (isNaN(curRow.find('.certification_price').val()) || curRow.find('.certification_price').val() == '') ? 0 : curRow.find('.certification_price').val();

		/**

		*	Amount calculation based on settings (without discount and tax )

		*   0 - Wastage on Gross weight And MC on Gross weight

		*   1 - Wastage on Net weight And MC on Net weight

		*   2 - Wastage On Netwt And MC On Grwt

		*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC

		*/

		console.log('gross_wt:' + gross_wt);

		console.log('net_wt:' + net_wt);

		console.log('retail_max_mc:' + retail_max_mc);

		if (calculation_type == 0) {

			wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

			if (curRow.find('.id_mc_type').val() != 3) {

				mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) : parseFloat(retail_max_mc * 1));

				// Metal Rate + Stone + OM + Wastage + MC

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) +  parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

			} else {

				mc_type = retail_max_mc;

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * ( parseFloat(net_wt)) + parseFloat(wast_amt)) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(net_wt))+ parseFloat(wast_amt))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(net_wt)) + parseFloat(wast_amt)) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(net_wt)) + parseFloat(wast_amt))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

			}

		}

		else if (calculation_type == 1) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

			if (curRow.find('.id_mc_type').val() != 3) {

				var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * 1));

				// Metal Rate + Stone + OM + Wastage + MC

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(net_wt)) + parseFloat(wast_amt)) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

			} else {

				mc_type = retail_max_mc;

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(net_wt)) + parseFloat(wast_amt)) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(net_wt)) + parseFloat(wast_amt))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

			}

		}

		else if (calculation_type == 2) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(5);

			wast_amt = parseFloat(parseFloat(wast_wgt) * parseFloat(rate_per_grm)).toFixed(2);

			if (curRow.find('.id_mc_type').val() != 3) {

				var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) : parseFloat(retail_max_mc * 1));

				// Metal Rate + Stone + OM + Wastage + MC

				rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(net_wt))) + parseFloat(wast_amt) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(certification_price);

				market_rate_with_mc = parseFloat((parseFloat(market_rate_per_grm) * (parseFloat(net_wt))+ parseFloat(wast_amt)) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(certification_price);

			} else {

				mc_type = retail_max_mc;

				rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(net_wt)) + parseFloat(wast_amt)) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(net_wt)) + parseFloat(wast_amt))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat((parseFloat(market_rate_per_grm) * (parseFloat(net_wt)) + parseFloat(wast_amt)) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(net_wt)) + parseFloat(wast_amt))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

			}

		}

		else if (calculation_type == 3)

		{

			rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val());

			market_rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val());

		}

		else if (calculation_type == 4)

		{

			if (curRow.find('.id_mc_type').val() != 3) {

				mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * 1));

			} else {

				mc_type = retail_max_mc;

			}

			if(product_type==0)

			{

				rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val() *  gross_wt);

				market_rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val() *  gross_wt);

			}

			else

			{

				rate_with_mc = parseFloat(loose_prod_stone_rate);

				market_rate_with_mc = parseFloat(loose_prod_stone_rate);

			}

			rate_with_mc = parseFloat(parseFloat(rate_with_mc) + parseFloat(mc_type)).toFixed(2);

		}

		let charge_value = isNaN(parseFloat(curRow.find('.charge_value').val())) ? 0 : curRow.find('.charge_value').val();

		let rate_with_charges = parseFloat(rate_with_mc) + parseFloat(charge_value);

		var tax_type = '';

		$.each(prod_details, function (k, val) {

			if (val.pro_id == curRow.find('.pro_id').val()) {

				tax_type = val.tax_type;

			}

		});

		if (tax_details.length > 0) {

			// Tax Calculation

			var base_value_tax = parseFloat(calculate_base_value_tax(rate_with_charges, tax_group)).toFixed(2);

			var base_value_amt = parseFloat(parseFloat(rate_with_charges) + parseFloat(base_value_tax)).toFixed(2);

			var arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

			var market_base_value_tax = parseFloat(calculate_base_value_tax(market_rate_with_mc, tax_group)).toFixed(2);

			var market_base_value_amt = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);

			var market_arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var market_arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			market_total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

		}

		total_price = parseFloat(parseFloat(rate_with_charges) + parseFloat(total_tax_rate)).toFixed(2);

		market_total_price = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(market_total_tax_rate)).toFixed(2);

		if ((calculation_type == 3 || calculation_type == 4) && (tax_type == 1)) //tax_type 1-Inclusive ,2-Exclusive

		{

			total_price = rate_with_charges;

			var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_charges, tax_group)).toFixed(2);

		}

		if (cus_del_country == cmp_country) {

			if (cus_del_state == cmp_state) {

				cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

				sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			}

			else {

				cgst = 0; sgst = 0;

				igst = total_tax_rate;

			}

		}

		else if (cus_del_country == '') {

			cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		}

		else // NO GST

		{

			cgst = 0; sgst = 0; igst = 0;

			inclusive_tax_rate = rate_with_mc;

		}

		curRow.find('.sale_cgst').val(cgst);

		curRow.find('.sale_sgst').val(sgst);

		curRow.find('.sale_igst').val(igst);

		curRow.find('.sale_taxable_amount').val(rate_with_charges);

		curRow.find('.tax_price').val(total_tax_rate);

		curRow.find('.market_rate_tax').val(market_total_tax_rate);

		curRow.find('.market_rate_cost').val(market_total_price);

		curRow.find('.cost').html(total_price);

		curRow.find('.mc').html(parseFloat(mc_type).toFixed(2));

		curRow.find('.mc_value').val(parseFloat(mc_type).toFixed(2));

		curRow.find('.sales_value').val(total_price);

		curRow.find('.est_wastage_wt').val(parseFloat(wast_wgt).toFixed(3));

		curRow.find('.tot_mc').html(parseFloat(mc_type).toFixed(2));

		console.log('Total Price :' + total_price);

		console.log('rate_with_mc :' + rate_with_charges);

		console.log('rate_with_mc :' + rate_with_mc);

		console.log('charge_value :' + charge_value);

		console.log('wast_wgt :' + wast_wgt);

		console.log('tot_wastage :' + tot_wastage);

		console.log('calculation_type :' + calculation_type);

		console.log('gross_wt :' + gross_wt);

		console.log('gross_wt :' + gross_wt);

		console.log('stone_price :' + stone_price);

		console.log('mc_type :' + mc_type);

		console.log('rate_per_grm :' + rate_per_grm);

		console.log('retail_max_mc :' + retail_max_mc);

		console.log('total_tax_rate :' + total_tax_rate);

		console.log('---------------');

	});

	calculate_purchase_details();

	calculate_sales_details();

}

function calculate_chit_details() {

	var total_chit_paid_amt = 0;

	var total_chit_weight = 0;

	var chit_weight = 0;

	var total_weight = 0;

	var balance_weight = 0;

	var balance_pay_amt = 0;

	var average_rate = 0;

	console.log(chit_details);

	$.each(chit_details, function (key, items) {

		if (items.scheme_type == 2 || items.scheme_type == 3) {

			chit_weight += parseFloat(items.closing_balance);

			total_chit_paid_amt += parseFloat(items.paid_amount - items.closing_add_chgs);

		}

		else if (items.scheme_type == 0) {

			total_chit_paid_amt += parseFloat(items.closing_balance);

		}

	});

	$('.summary_chit_amt').html(parseFloat(total_chit_paid_amt));

	$('.summary_chit_weight').html(parseFloat(chit_weight).toFixed(3));

	total_chit_weight = parseFloat(chit_weight).toFixed(3);

	$('#estimation_tag_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var gross_wt = (isNaN(curRow.find('.gwt').val()) || curRow.find('.gwt').val() == '') ? 0 : curRow.find('.gwt').val();

		var less_wt = (isNaN(curRow.find('.lwt').val()) || curRow.find('.lwt').val() == '') ? 0 : curRow.find('.lwt').val();

		var stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();

		var net_wt = parseFloat(gross_wt) - parseFloat(less_wt);

		var tot_wastage = (isNaN(curRow.find('.wastage_max_per').val()) || curRow.find('.wastage_max_per').val() == '') ? 0 : curRow.find('.wastage_max_per').val();

		var calculation_type = (isNaN(curRow.find('.caltype').val()) || curRow.find('.caltype').val() == '') ? 0 : curRow.find('.caltype').val();

		/**

		*	Amount calculation based on settings (without discount and tax )

		*   0 - Wastage on Gross weight And MC on Gross weight

		*   1 - Wastage on Net weight And MC on Net weight

		*   2 - Wastage On Netwt And MC On Grwt

		*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC

		*/

		if (calculation_type == 0) {

			total_weight += parseFloat((parseFloat(gross_wt) * parseFloat(tot_wastage / 100)) + parseFloat(gross_wt));

		}

		else if (calculation_type == 1) {

			total_weight += parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100) + parseFloat(net_wt));

		}

		else if (calculation_type == 2) {

			total_weight += parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100) + parseFloat(net_wt))

		}

	});

	balance_weight = parseFloat(parseFloat(total_weight) - parseFloat(total_chit_weight)).toFixed(3);

	if (total_chit_weight < total_weight) {

		var balance_pay_amt = parseFloat(parseFloat($('.goldrate_22ct').html()) * parseFloat(balance_weight)).toFixed(2);

	}

	average_rate = parseFloat(parseFloat(parseFloat(total_chit_paid_amt) + parseFloat(balance_pay_amt)) / parseFloat(total_weight)).toFixed(2);

	console.log('adv_paid_amt:' + total_chit_paid_amt);

	console.log('adv_paid_wt:' + total_chit_weight);

	console.log('total_weight:' + total_weight);

	console.log('balance_weight:' + balance_weight);

	console.log('average_rate:' + average_rate);

	console.log('balance_pay_amt:' + balance_pay_amt);

	$('#estimation_tag_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var mc_type = 0;

		var wast_wgt = 0;

		var rate_with_mc = 0;

		var market_rate_with_mc = 0;

		var total_price = 0;

		var stone_price = 0;

		var material_price = 0;

		var tax_rate = 0;

		var total_tax_rate = 0;

		var market_total_tax_rate = 0;

		var base_value_tax = 0;

		var arrived_value_tax = 0;

		var base_value_amt = 0;

		var arrived_value_amt = 0;

		var market_base_value_tax = 0;

		var market_arrived_value_tax = 0;

		var market_base_value_amt = 0;

		var market_arrived_value_amt = 0;

		var rate_per_grm = 0;

		var market_rate_per_grm = 0;

		var gross_wt = (isNaN(curRow.find('.gwt').val()) || curRow.find('.gwt').val() == '') ? 0 : curRow.find('.gwt').val();

		var less_wt = (isNaN(curRow.find('.lwt').val()) || curRow.find('.lwt').val() == '') ? 0 : curRow.find('.lwt').val();

		var stone_wt = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '') ? 0 : curRow.find('.stone_wt').val();

		var net_wt = parseFloat(gross_wt) - parseFloat(less_wt);

		var calculation_type = (isNaN(curRow.find('.caltype').val()) || curRow.find('.caltype').val() == '') ? 0 : curRow.find('.caltype').val();

		var piece = (isNaN(curRow.find('.piece').val()) || curRow.find('.piece').val() == '') ? 1 : curRow.find('.piece').val();

		var metal_type = (isNaN(curRow.find('.metal_type').val()) || curRow.find('.metal_type').val() == '') ? 1 : curRow.find('.metal_type').val();

		var tax_group = curRow.find('.tax_group_id').val();

		rate_field = curRow.find('.rate_field').val();

		market_rate_field = curRow.find('.market_rate_field').val();

		if (rate_field != '') {

			//rate_per_grm = (isNaN($('.'+rate_field).html()) ||$('.'+rate_field).html() == '')  ? 0 : $('.'+rate_field).html();

			if (balance_pay_amt > 0) {

				rate_per_grm = average_rate;

			} else {

				rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

			}

		}

		if (market_rate_field != '') {

			market_rate_per_grm = average_rate

			//market_rate_per_grm = (isNaN($('.'+market_rate_field).html()) ||$('.'+market_rate_field).html() == '')  ? 0 : parseFloat($('.'+market_rate_field).html());

		}

		var tot_wastage = (isNaN(curRow.find('.wastage_max_per').val()) || curRow.find('.wastage_max_per').val() == '') ? 0 : curRow.find('.wastage_max_per').val();

		var retail_max_mc = (isNaN(curRow.find('.act_mc_value').val()) || curRow.find('.act_mc_value').val() == '') ? 0 : curRow.find('.act_mc_value').val();

		var stone_price = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '') ? 0 : curRow.find('.stone_price').val();

		var certification_price = (isNaN(curRow.find('.certification_price').val()) || curRow.find('.certification_price').val() == '') ? 0 : curRow.find('.certification_price').val();

		/**

		*	Amount calculation based on settings (without discount and tax )

		*   0 - Wastage on Gross weight And MC on Gross weight

		*   1 - Wastage on Net weight And MC on Net weight

		*   2 - Wastage On Netwt And MC On Grwt

		*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC

		*/

		console.log('gross_wt:' + gross_wt);

		console.log('net_wt:' + net_wt);

		console.log('retail_max_mc:' + retail_max_mc);

		if (calculation_type == 0) {

			wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100)).toFixed(3);

			if (curRow.find('.id_mc_type').val() != 3) {

				mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) : parseFloat(retail_max_mc * 1));

				// Metal Rate + Stone + OM + Wastage + MC

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

			} else {

				mc_type = retail_max_mc;

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

			}

		}

		else if (calculation_type == 1) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(3);

			if (curRow.find('.id_mc_type').val() != 3) {

				var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * net_wt) : parseFloat(retail_max_mc * 1));

				// Metal Rate + Stone + OM + Wastage + MC

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type) + parseFloat(stone_price) + parseFloat(certification_price));

			} else {

				mc_type = retail_max_mc;

				rate_with_mc = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

			}

		}

		else if (calculation_type == 2) {

			var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(3);

			if (curRow.find('.id_mc_type').val() != 3) {

				var mc_type = parseFloat(curRow.find('.id_mc_type').val() == 2 ? parseFloat(retail_max_mc * gross_wt) : parseFloat(retail_max_mc * 1));

				// Metal Rate + Stone + OM + Wastage + MC

				rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(certification_price);

				market_rate_with_mc = parseFloat((parseFloat(market_rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(mc_type)) + parseFloat(stone_price) + parseFloat(certification_price);

			} else {

				mc_type = retail_max_mc;

				rate_with_mc = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

				market_rate_with_mc = parseFloat((parseFloat(market_rate_per_grm) * (parseFloat(wast_wgt) + parseFloat(net_wt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(net_wt)))) * (mc_type / 100))) + parseFloat(stone_price) + parseFloat(certification_price));

			}

		}

		else if (calculation_type == 3 || calculation_type == 4) {

			rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val());

			market_rate_with_mc = parseFloat((isNaN(curRow.find('.act_sales_value').val()) || curRow.find('.act_sales_value').val() == '') ? 0 : curRow.find('.act_sales_value').val());

		}

		let charge_value = isNaN(parseFloat(curRow.find('.charge_value').val())) ? 0 : curRow.find('.charge_value').val();

		let rate_with_charges = parseFloat(rate_with_mc) + parseFloat(charge_value);

		var tax_type = '';

		$.each(prod_details, function (k, val) {

			if (val.pro_id == curRow.find('.pro_id').val()) {

				tax_type = val.tax_type;

			}

		});

		if (tax_details.length > 0) {

			// Tax Calculation

			var base_value_tax = parseFloat(calculate_base_value_tax(rate_with_charges, tax_group)).toFixed(2);

			var base_value_amt = parseFloat(parseFloat(rate_with_charges) + parseFloat(base_value_tax)).toFixed(2);

			var arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

			var market_base_value_tax = parseFloat(calculate_base_value_tax(market_rate_with_mc, tax_group)).toFixed(2);

			var market_base_value_amt = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(base_value_tax)).toFixed(2);

			var market_arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var market_arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			market_total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

		}

		total_price = parseFloat(parseFloat(rate_with_charges) + parseFloat(total_tax_rate)).toFixed(2);

		market_total_price = parseFloat(parseFloat(market_rate_with_mc) + parseFloat(market_total_tax_rate)).toFixed(2);

		if ((calculation_type == 3 || calculation_type == 4) && (tax_type == 1)) //tax_type 1-Inclusive ,2-Exclusive

		{

			total_price = rate_with_charges;

			var total_tax_rate = parseFloat(calculate_inclusiveGST(rate_with_charges, tax_group)).toFixed(2);

		}

		curRow.find('.tax_price').val(total_tax_rate);

		curRow.find('.market_rate_tax').val(market_total_tax_rate);

		curRow.find('.market_rate_cost').val(market_total_price);

		curRow.find('.cost').html(total_price);

		curRow.find('.mc').html(parseFloat(mc_type).toFixed(2));

		curRow.find('.mc_value').val(parseFloat(mc_type).toFixed(2));

		curRow.find('.sales_value').val(total_price);

		console.log('Total Price :' + total_price);

		console.log('rate_with_mc :' + rate_with_charges);

		console.log('rate_with_mc :' + rate_with_mc);

		console.log('charge_value :' + charge_value);

		console.log('wast_wgt :' + wast_wgt);

		console.log('tot_wastage :' + tot_wastage);

		console.log('calculation_type :' + calculation_type);

		console.log('gross_wt :' + gross_wt);

		console.log('gross_wt :' + gross_wt);

		console.log('stone_price :' + stone_price);

		console.log('mc_type :' + mc_type);

		console.log('rate_per_grm :' + rate_per_grm);

		console.log('retail_max_mc :' + retail_max_mc);

		console.log('total_tax_rate :' + total_tax_rate);

		console.log('---------------');

	});

	calculate_purchase_details();

	calculate_sales_details();

}

function calculate_order_SaleValue() {

	$('#estimation_order_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var total_price = 0;

		var market_total_price = 0;

		var material_price = 0;

		var tax_rate = 0;

		var base_value_tax = 0;

		var arrived_value_price = 0;

		var base_value_price = 0;

		var arrived_value_tax = 0;

		var total_tax_rate = 0;

		var market_base_rate_tax = 0;

		var market_base_value_price = 0;

		var market_arrived_rate_tax = 0;

		var market_arrived_value_price = 0;

		var market_total_tax_rate = 0;

		var arrived_rate_tax = 0;

		var market_taxable = 0;

		var cus_del_country = $('#cus_del_country').val();

		var cmp_country = $('#cmp_country').val();

		var cus_del_state = $('#cus_del_state').val();

		var cmp_state = $('#cmp_state').val();

		var cgst = 0;

		var igst = 0;

		var sgst = 0;

		var weight = (isNaN(curRow.find('.weight').val()) || curRow.find('.weight').val() == '') ? 0 : curRow.find('.weight').val();

		var piece = (isNaN(curRow.find('.piece').val()) || curRow.find('.piece').val() == '') ? 1 : curRow.find('.piece').val();

		var metal_type = (isNaN(curRow.find('.metal_type').val()) || curRow.find('.metal_type').val() == '') ? 1 : curRow.find('.metal_type').val();

		if (metal_type == 1) {

			var rate_per_grm = (isNaN($('.per-grm-sale-value').html()) || $('.per-grm-sale-value').html() == '') ? 0 : parseFloat($('.per-grm-sale-value').html());

		} else {

			var rate_per_grm = (isNaN($('.silver_per-grm-sale-value').html()) || $('.silver_per-grm-sale-value').html() == '') ? 0 : parseFloat($('.silver_per-grm-sale-value').html());

		}

		if (metal_type == 1) {

			var market_rate_per_grm = (isNaN($('.mjdmagoldrate_22ct').html()) || $('.mjdmagoldrate_22ct').html() == '') ? 0 : parseFloat($('.mjdmagoldrate_22ct').html());

		} else {

			var market_rate_per_grm = (isNaN($('.mjdmasilverrate_1gm').html()) || $('.mjdmasilverrate_1gm').html() == '') ? 0 : parseFloat($('.mjdmasilverrate_1gm').html());

		}

		var tot_wastage = (isNaN(curRow.find('.wastage_max_per').val()) || curRow.find('.wastage_max_per').val() == '') ? 0 : curRow.find('.wastage_max_per').val();

		var retail_max_mc = (isNaN(curRow.find('.mc_value').val()) || curRow.find('.mc_value').val() == '') ? 0 : curRow.find('.mc_value').val();

		var stn_amt = (isNaN(curRow.find('.stn_amt').val()) || curRow.find('.stn_amt').val() == '') ? 0 : curRow.find('.stn_amt').val();

		var tax_group = curRow.find('.tax_group').val();

		var tgi_calculation_type = curRow.find('.tgi_calculation').val().split(",");

		var tax_percentage = curRow.find('.tax_percentage').val().split(",");

		/**

		*	Amount calculation based on settings (without discount and tax )

		*   0 - Wastage on Gross weight And MC on Gross weight

		*   1 - Wastage on Net weight And MC on Net weight

		*   2 - Wastage On Netwt And MC On Grwt

		*   rate_with_mc = Metal Rate + Stone + OM + Wastage + MC

		*/

		var wast_wgt = parseFloat(parseFloat(weight) * parseFloat(tot_wastage / 100)).toFixed(3);

		taxable = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt) + parseFloat(weight))) + parseFloat(retail_max_mc) + parseFloat(stn_amt));

		market_taxable = parseFloat(parseFloat(market_rate_per_grm * (parseFloat(wast_wgt) + parseFloat(weight))) + parseFloat(retail_max_mc) + parseFloat(stn_amt));

		//sale value

		var tax_type = '';

		$.each(prod_details, function (k, val) {

			if (val.pro_id == curRow.find('.pro_id').val()) {

				tax_type = val.tax_type;

			}

		});

		if (tax_details.length > 0) {

			// Tax Calculation

			var base_value_tax = parseFloat(calculate_base_value_tax(taxable, tax_group)).toFixed(2);

			var base_value_amt = parseFloat(parseFloat(taxable) + parseFloat(base_value_tax)).toFixed(2);

			var arrived_value_tax = parseFloat(calculate_arrived_value_tax(base_value_amt, tax_group)).toFixed(2);

			var arrived_value_amt = parseFloat(parseFloat(base_value_amt) + parseFloat(arrived_value_tax)).toFixed(2);

			total_tax_rate = parseFloat(parseFloat(base_value_tax) + parseFloat(arrived_value_tax)).toFixed(2);

			var market_base_value_tax = parseFloat(calculate_base_value_tax(market_taxable, tax_group)).toFixed(2);

			var market_base_value_amt = parseFloat(parseFloat(market_taxable) + parseFloat(market_base_value_tax)).toFixed(2);

			var market_arrived_value_tax = parseFloat(calculate_arrived_value_tax(market_base_value_amt, tax_group)).toFixed(2);

			var market_arrived_value_amt = parseFloat(parseFloat(market_base_value_amt) + parseFloat(market_arrived_value_tax)).toFixed(2);

			market_total_tax_rate = parseFloat(parseFloat(market_base_value_tax) + parseFloat(market_arrived_value_tax)).toFixed(2);

		}

		total_price = parseFloat(parseFloat(taxable) + parseFloat(total_tax_rate)).toFixed(2);

		market_total_price = parseFloat(parseFloat(market_taxable) + parseFloat(market_total_tax_rate)).toFixed(2);

		if ((calculation_type == 3 || calculation_type == 4) && (tax_type == 1)) //tax_type 1-Inclusive ,2-Exclusive

		{

			total_price = taxable;

			var total_tax_rate = parseFloat(calculate_inclusiveGST(taxable, tax_group)).toFixed(2);

		}

		if (cus_del_country == cmp_country) {

			if (cus_del_state == cmp_state) {

				cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

				sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			}

			else {

				cgst = 0; sgst = 0;

				igst = total_tax_rate;

			}

		}

		else if (cus_del_country == '') {

			cgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

			sgst = parseFloat(parseFloat(total_tax_rate) / 2).toFixed(2);

		}

		else // NO GST

		{

			cgst = 0; sgst = 0; igst = 0;

			inclusive_tax_rate = rate_with_mc;

		}

		curRow.find('.cgst').val(cgst);

		curRow.find('.sgst').val(sgst);

		curRow.find('.igst').val(igst);

		curRow.find('.taxable_amount').val(market_taxable);

		curRow.find('.tax_price').val(total_tax_rate);

		curRow.find('.market_rate_cost').val(market_total_price);

		curRow.find('.market_rate_tax').val(market_total_tax_rate);

		curRow.find('.cost').html(total_price);

		curRow.find('.item_cost').val(total_price);

		curRow.find('.sales_value').val(total_price);

		console.log('Total Price :' + total_price);

		console.log('wast_wgt :' + wast_wgt);

		console.log('tot_wastage :' + tot_wastage);

		console.log('rate_per_grm :' + rate_per_grm);

		console.log('---------------');

	});

	calculate_purchase_details();

	calculate_sales_details();

}

function calculate_inclusiveGST(taxcallrate, taxgroup) {

	var totaltax = 0;

	console.log(tax_details);

	$.each(tax_details, function (idx, taxitem) {

		if (taxitem.tgi_tgrpcode == taxgroup) {

			//	Remove GST = 490*100/(100+3) = 475.7281553398058

			//	GST 3% = 490 - 475.7281553398058 = 14.2718446601942

			amt_without_gst = (parseFloat(taxcallrate) * 100) / (100 + parseFloat(taxitem.tax_percentage));

			totaltax += parseFloat(taxcallrate) - parseFloat(amt_without_gst);

		}

	});

	return totaltax;

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

function get_metal_rates_by_branch(id_branch) {

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_tagging/get_metal_rates_by_branch?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		data: { 'id_branch': id_branch },

		type: "POST",

		dataType: "json",

		async: false,

		success: function (data) {

			metal_rates = data;

			$('.per-grm-sale-value').html(data.goldrate_22ct);

			$('.silver_per-grm-sale-value').html(data.silverrate_1gm);

			$('.mjdmagoldrate_22ct').html(data.mjdmagoldrate_22ct);

			$('.mjdmasilverrate_1gm').html(data.mjdmasilverrate_1gm);

			$('.goldrate_14ct').html(data.goldrate_14ct);

			$('.goldrate_9ct').html(data.goldrate_9ct);

			$('.goldrate_18ct').html(data.goldrate_18ct);

			$('.goldrate_22ct').html(data.goldrate_22ct);

			$('.silverrate_1gm').html(data.silverrate_1gm);

			$('#goldrate_22ct').val(data.goldrate_22ct);

			$('#silverrate_1gm').val(data.silverrate_1gm);

			$('#goldrate_14ct').val(data.goldrate_14ct);

			$('#goldrate_18ct').val(data.goldrate_18ct);

			$('#goldrate_9ct').val(data.goldrate_9ct);

			$('#mjdmasilverrate_999').val(data.mjdmasilverrate_999);
			
			$('.mjdmasilverrate_999').html(data.mjdmasilverrate_999);
		},

		error: function (error) {

			$("div.overlay").css("display", "none");

		}

	});

}

function validateChitDetailRow() {

	var row_validate = true;

	$('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .scheme_account_id').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

function get_SchemeAcc_number(searchTxt, curRow, id_scheme_acc) {

	var scheme_shortcode = '';

	var scheme_rate_type = '';

	var scheme_shortcode_flag = true;

	var weight_scheme_closure_type = $('#weight_scheme_closure_type').val(); //1-General,2-Based on M.C & V.A

	var chit_rate_cal_type = $('#chit_rate_cal_type').val(); //1-Based on Average rate calculation ,2-Based on weight scheme closure type

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_billing/get_scheme_accounts/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'id_customer': $('#cus_id').val(), 'id_scheme_account': id_scheme_acc ,'id_branch':$('#id_branch').val() },

		success: function (data) {

			if (data != '') {

				$.each(data, function (key, item) {

					scheme_shortcode = item.scheme_short_code;

					scheme_rate_type = item.rate_type;

					$('#estimation_chit_details > tbody tr').each(function (idx, row) {

						if (item != undefined) {

							if ($(this).find('td:first .id_scheme_account').val() == item.value) {

								data.splice(key, 1);

							}

						}

					});

				});

				const index = chit_details.findIndex(object => object.value === data[0].value);

				if (index === -1) {

					chit_details.push(data[0]);

				}

				curRow.find('.scheme_account_id').val(data[0].label);

				curRow.find('.id_scheme_account').val(data[0].value);

				curRow.find('.scheme_type').val(data[0].scheme_type);

				curRow.find('.closing_amount').val(data[0].closing_amount);

				curRow.find('.rate_type').val(data[0].rate_type);

				curRow.find('.paid_installments').val(data[0].paid_installments);

				curRow.find('.total_installments').val(data[0].total_installments);

				curRow.find('.is_wast_and_mc_benefit_apply').val(data[0].is_wast_and_mc_benefit_apply);

				curRow.find('.closing_add_chgs').val(data[0].closing_add_chgs);

				curRow.find('.additional_benefits').val(data[0].additional_benefits);

				curRow.find('.bonus_percent').val(data[0].bonus_percent);

				if (data[0].scheme_type != 0 && (data[0].paid_installments == data[0].total_installments)) {

					curRow.find('.closing_weight').val(data[0].closing_weight);

				}

				var curRowItem = data[0];

				if(chit_rate_cal_type == 1 && data[0].rate_type == 2){

					var add = false;

					// var closing_weight = parseFloat(data[0].closing_amount / data[0].topup_rate).toFixed(3);

					var closing_weight = (data[0].one_time_premium == 1 ? data[0].fixed_wgt : (data[0].is_topup_scheme === 1 ? data[0].topup_weight : data[0].closing_weight));

					var rate = (data[0].one_time_premium == 1 ? data[0].fixed_metal_rate : (data[0].is_topup_scheme === 1 ? data[0].topup_rate : parseFloat(data[0].closing_amount / data[0].closing_weight).toFixed(2)));

					var closing_weight = parseFloat(closing_weight ).toFixed(3);

					curRow.find('.closing_amount').val(data[0].closing_amount);

					curRow.find('.chit_amt').val(data[0].closing_amount);

					curRow.find('.closing_weight').val(closing_weight);

					curRow.find('.saved_weight').val(closing_weight);

					curRow.find('.chit_rate').val(rate);

					curRow.find('.topup_scheme').val(data[0].is_topup_scheme);

					get_topup_chit_closing_balance('',add = 'cht_add');

				}

				else{

					if(chit_rate_cal_type != 1){  
						if(weight_scheme_closure_type!=3){

							calculate_chit_closing_balance();

						}else{

							

							var rate = parseFloat(data[0].closing_amount / data[0].closing_weight).toFixed(2);

							curRow.find('.closing_amount').val(data[0].closing_amount);

							curRow.find('.chit_amt').val(data[0].closing_amount);

							curRow.find('.closing_weight').val(data[0].closing_weight);

							curRow.find('.chit_rate').val(rate);

							set_tag_split_details();
							set_custom_split_details();

						}
					}
				}

				if ($('#estimation_tag_details > tbody >tr').length > 0 || $('#estimation_custom_details > tbody >tr').length > 0 || $('#estimation_catalog_details > tbody >tr').length > 0) {

					calculate_sales_details();

					calculate_purchase_details();

					//calculate_chit_details();

				}

			}

			else {

				curRow.find('.scheme_account_id').val("");

				curRow.find('.id_scheme_account').val("");

				curRow.find('.scheme_type').val("");

				curRow.find('.closing_amount').val("");

				curRow.find('.paid_installments').val("");

				curRow.find('.total_installments').val("");

				curRow.find('.is_wast_and_mc_benefit_apply').val("");

				curRow.find('.closing_add_chgs').val("");

				curRow.find('.additional_benefits').val("");

				curRow.find('.bonus_percent').val("");

				calculate_chit_closing_balance();

				if ($('#estimation_tag_details > tbody >tr').length > 0 || $('#estimation_custom_details > tbody >tr').length > 0 || $('#estimation_catalog_details > tbody >tr').length > 0) {

					calculate_sales_details();

					calculate_purchase_details();

					//calculate_chit_details();

				}

			}

		}

	})

}

function get_scheme_acc_number(searchTxt, curRow) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_billing/get_scheme_accounts/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'id_customer': $('#cus_id').val() },

		success: function (data) {

			$.each(data, function (key, item) {

				$('#estimation_chit_details > tbody tr').each(function (idx, row) {

					if (item != undefined) {

						if ($(this).find('td:first .id_scheme_account').val() == item.value) {

							data.splice(key, 1);

						}

					}

				});

			});

			curRow.find(".scheme_account_id").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						const index = chit_details.findIndex(object => object.value === i.item.value);

						if (index === -1) {

							if (i.item.scheme_type != 0) {

								i.item.closing_amount = parseFloat(i.item.closing_balance * parseFloat($('.mjdmagoldrate_22ct').html())).toFixed(2);

							}

							chit_details.push(i.item);

						}

						curRow.find('.scheme_account_id').val(i.item.label);

						curRow.find('.id_scheme_account').val(i.item.value);

						curRow.find('.scheme_type').val(i.item.scheme_type);

						curRow.find('.closing_amount').val(i.item.closing_amount);

						curRow.find('.paid_installments').val(i.item.paid_installments);

						curRow.find('.total_installments').val(i.item.total_installments);

						curRow.find('.is_wast_and_mc_benefit_apply').val(i.item.is_wast_and_mc_benefit_apply);

						curRow.find('.closing_add_chgs').val(i.item.closing_add_chgs);

						curRow.find('.additional_benefits').val(i.item.additional_benefits);

						if (i.item.scheme_type != 0) {

							curRow.find('.closing_weight').val(i.item.closing_balance);

						}

						var curRowItem = i.item;

						calculate_chit_closing_balance();

						if ($('#estimation_tag_details > tbody >tr').length > 0 || $('#estimation_custom_details > tbody >tr').length > 0 || $('#estimation_catalog_details > tbody >tr').length > 0) {

							calculate_sales_details();

							calculate_purchase_details();

							//calculate_chit_details();

						}

					},

					change: function (event, ui) {

						if (ui.item === null) {

							//$(this).val('');

						}

					},

					response: function (e, i) {

						// ui.content is the array that's about to be sent to the response callback.

						if (searchTxt != "") {

							if (i.content.length === 0) {

								$("#customerAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');

							} else {

								$("#customerAlert").html('');

							}

						} else {

						}

					},

					minLength: 1,

				});

		}

	});

}

$('#wastage_per,#mc_value').on('change', function () {

	calculate_chit_closing_balance();

});

function calculate_chit_closing_balance() {

	var weight_scheme_closure_type = $('#weight_scheme_closure_type').val(); //1-General,2-Based on M.C & V.A

	var weightschemecaltype = $('#weightschemecaltype').val(); //1->Based On Manual Va & MC  2->Based on the Highest Va & MC 3->Based On the Lowest Va & MC

	if (weightschemecaltype == 1 && weight_scheme_closure_type!=3) {

		$('#wastage_per').attr('readonly', false);

		$('#mc_value').attr('readonly', false);

	} else {

		$('#wastage_per').attr('readonly', true);

		$('#mc_value').attr('readonly', true);

		$('#wastage_per').val("");

		$('#mc_value').attr("");

	}

	$('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

		var curRow = $(this).closest("tr");

		var chit_amount = 0;

		var saving_weight = 0;

		var goldrate_22ct = (isNaN($('.goldrate_22ct').html()) || $('.goldrate_22ct').html() == '') ? 0 : parseFloat($('.goldrate_22ct').html());

		var additional_benefits = curRow.find('.additional_benefits').val();

		var closing_add_chgs = curRow.find('.closing_add_chgs').val();

		var paid_installments = curRow.find('.paid_installments').val();

		var total_installments = curRow.find('.total_installments').val();

		var is_wast_and_mc_benefit_apply = curRow.find('.is_wast_and_mc_benefit_apply').val();

		var rate_type = curRow.find('#rate_type').val();

		var saving_weight = (curRow.find('.closing_weight').val() != '' ? curRow.find('.closing_weight').val() : 0);

		var sales_details = [];

		var board_rate = 0;

		var rate_field = '';

		var is_topup_scheme = curRow.find('.topup_scheme').val();

		var chit_rate_cal_type = $('#chit_rate_cal_type').val();

		$('#estimation_tag_details > tbody tr').each(function (bidx, brow) {

			tagcurRow = $(this);

			var rate_field = tagcurRow.find('.rate_field').val();

			rate_field_value = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : parseFloat($('.' + rate_field).html());
			
			if($('#wastage_rate_type').val() == 1 && $("#manual_rate").is(":checked") && rate_field_value != tagcurRow.find('.market_rate_value').val()){

				board_rate = rate_field_value;
			}

			else {

				board_rate =  parseFloat(tagcurRow.find('.market_rate_value').val());
				
			}

			// board_rate = (isNaN($('.' + market_rate_field).html()) || $('.' + market_rate_field).html() == '') ? 0 : parseFloat($('.' + market_rate_field).html());

			if (tagcurRow.find('.scheme_closure_benefit').val() == 1 && tagcurRow.find('.metal_type').val()==1) {

			    goldrate_22ct = (isNaN(tagcurRow.find('.market_rate_value').val()) || tagcurRow.find('.market_rate_value').val() == '') ? 0 : parseFloat(tagcurRow.find('.market_rate_value').val());

				//var goldrate_22ct = (isNaN($('.market_rate_value').val()) || $('.market_rate_value').val() == '') ? 0 : parseFloat($('.market_rate_value').val());

				sales_details.push({ 'wastage_per': tagcurRow.find('.wastage_max_per').val(), 'mc_value': (tagcurRow.find('.id_mc_type').val() == 1 ? tagcurRow.find('.act_mc_value').val() : parseFloat(parseFloat(tagcurRow.find('.act_mc_value').val()) * parseFloat(tagcurRow.find('.gwt').val())).toFixed(2)), 'item_gross_wt': tagcurRow.find('.gwt').val() });

			}else if(tagcurRow.find('.metal_type').val()==1){

			    goldrate_22ct = (isNaN(tagcurRow.find('.market_rate_value').val()) || tagcurRow.find('.market_rate_value').val() == '') ? 0 : parseFloat(tagcurRow.find('.market_rate_value').val());



			}

		});

		$('#estimation_catalog_details > tbody tr').each(function (bidx, brow) {

			NontagcurRow = $(this);

			if (NontagcurRow.find('.scheme_closure_benefit').val() == 1 && NontagcurRow.find('.metal_type').val()==1) {

				sales_details.push({ 'wastage_per': NontagcurRow.find('.cat_wastage').val(), 'mc_value': (NontagcurRow.find('.mc_type').val() == 1 ? NontagcurRow.find('.cat_mc').val() : parseFloat(parseFloat(NontagcurRow.find('.cat_mc').val()) * parseFloat(NontagcurRow.find('.cat_gwt').val())).toFixed(2)), 'item_gross_wt': NontagcurRow.find('.cat_gwt').val() });

			}

		});

		$('#estimation_custom_details > tbody tr').each(function (bidx, brow) {

			homebillcurRow = $(this);

			if (homebillcurRow.find('.scheme_closure_benefit').val() == 1 && homebillcurRow.find('.metal_type').val()==1) {

				sales_details.push({ 'wastage_per': homebillcurRow.find('.cus_wastage').val(), 'mc_value': (homebillcurRow.find('.id_mc_type').val() == 1 ? homebillcurRow.find('.cus_mc').val() : parseFloat(parseFloat(homebillcurRow.find('.cus_mc').val()) * parseFloat(homebillcurRow.find('.cus_gwt').val())).toFixed(2)), 'item_gross_wt': homebillcurRow.find('.cus_gwt').val() });

			}

		});





		console.log(sales_details);

		if(chit_rate_cal_type == 1 && rate_type == 2){

			var chit_rate_per_gram =  curRow.find('.chit_rate').val();

			curRow.find('.rate_per_gram').val(parseFloat(chit_rate_per_gram).toFixed(2));

			curRow.find('.saved_weight').html(parseFloat(saving_weight).toFixed(3));

			curRow.find('.closing_weight').val(parseFloat(saving_weight).toFixed(3));

			chit_amount = curRow.find('.closing_amount').val();
		}

		else{

			if (weight_scheme_closure_type == 2 && saving_weight > 0 && (paid_installments == total_installments) && (sales_details.length > 0) && is_topup_scheme == 0) {

				var savings_in_wast_amt = 0;

				var savings_in_wast_wt = 0;

				var savings_in_mcvalue = 0;

				var wastage_wt = 0;

				var wastage_per = 0;

				var mc_value = 0;

				var item_gross_wt = 0;

				if (weightschemecaltype == 2)// Taking Highest V.A & M.C

				{

					if ($('#wastage_per').val() == '') {

						wastage_per = Math.max.apply(null, sales_details.map(item => item.wastage_per));

					}

					else {

						wastage_per = ($('#wastage_per').val() != '' ? $('#wastage_per').val() : 0);

					}

					if ($('#mc_value').val() == '') {

						$.each(sales_details, function (key, val) {

							if (parseFloat(val.mc_value) > parseFloat(mc_value)) {

								console.log(val.mc_value);

								mc_value = val.mc_value;

								item_gross_wt = val.item_gross_wt;

							}

						});

					}

					else {

						mc_value = ($('#mc_value').val() != '' ? $('#mc_value').val() : 0);

					}

				}

				else if (weightschemecaltype == 3)//Taking Lowest V.A & M.C

				{

					if ($('#wastage_per').val() == '') {

						wastage_per = Math.min.apply(null, sales_details.map(item => item.wastage_per));

					}

					else {

						wastage_per = ($('#wastage_per').val() != '' ? $('#wastage_per').val() : 0);

					}

					if ($('#mc_value').val() == '') {

						mc_value =  sales_details[0].mc_value;

						for (let i = 0; i < sales_details.length; i++)

						{

								if(parseFloat(sales_details[i].mc_value) < parseFloat(mc_value))

								{

									mc_value = sales_details[i].mc_value;

									item_gross_wt = sales_details[i].item_gross_wt;

								}

							}

					}

					else {

						mc_value = ($('#mc_value').val() != '' ? $('#mc_value').val() : 0);

					}

				}

				else if (weightschemecaltype == 4)//Taking Average V.A & M.C 

				{

					var sum_mc = 0;

					var sum_va_weight = 0;

					var total_sales_wt = 0;

					var total_mc_value = 0;

					$.each(sales_details, function (key, val) {

						total_sales_wt += parseFloat(val.item_gross_wt);

						total_mc_value += parseFloat(val.mc_value);

					});

					if ($('#wastage_per').val() == '') {

						sum_va_weight = sales_details.reduce((acc, item) => acc + parseFloat(item.item_wastage_wt), 0);

						// wastage_per = sum_wastage_per / sales_details.length; //average wastege percentage

						// wastage_per = sum_wastage_per / sales_details.length;

						wastage_per = (parseFloat(sum_va_weight) / parseFloat(total_sales_wt)) * 100;

					}

					else {

						wastage_per = ($('#wastage_per').val() != '' ? $('#wastage_per').val() : 0);

					}

					if ($('#mc_value').val() == '') {

						sales_details.forEach((item, index) => {

							sum_mc += parseFloat(item.mc_value);

							item_gross_wt += parseFloat(item.item_gross_wt);

						});

						mc_value = sum_mc / sales_details.length; //average mc



					}

					else {

						mc_value = ($('#mc_value').val() != '' ? $('#mc_value').val() : 0);

					}

				}

				else {

					wastage_per = ($('#wastage_per').val() != '' ? $('#wastage_per').val() : 0);

					mc_value = ($('#mc_value').val() != '' ? $('#mc_value').val() : 0);

				}

				var total_sales_wt = 0;

				var total_mc_value = 0;

				$.each(sales_details, function (key, val) {

					total_sales_wt += parseFloat(val.item_gross_wt);

					total_mc_value += parseFloat(val.mc_value);

				});

				if (wastage_per > 0) {

					if (parseFloat(total_sales_wt) > parseFloat(saving_weight)) {

						var wastage_wt = parseFloat(((parseFloat(saving_weight) * parseFloat(wastage_per)) / 100)).toFixed(3);

						var savings_in_wast_wt = parseFloat(parseFloat(wastage_wt) + parseFloat(saving_weight)).toFixed(3);

					} else {

						var wastage_wt = parseFloat(((parseFloat(total_sales_wt) * parseFloat(wastage_per)) / 100));

						var savings_in_wast_wt = parseFloat(((parseFloat(total_sales_wt) * parseFloat(wastage_per)) / 100) + parseFloat(saving_weight)).toFixed(3);

					}

				}

				else {

					var savings_in_wast_wt = parseFloat(saving_weight).toFixed(3);

				}

				console.log(mc_value);

				console.log(savings_in_wast_wt);

				console.log(item_gross_wt);

				if (mc_value > 0 && item_gross_wt > 0) {

					if (parseFloat(total_sales_wt) > parseFloat(saving_weight)) {

						var savings_in_mcvalue = parseFloat((parseFloat(mc_value) / parseFloat(item_gross_wt)) * parseFloat(saving_weight)).toFixed(2);

					} else {

						var savings_in_mcvalue = parseFloat(total_mc_value).toFixed(2);

					}

				}

				else if (mc_value != '') {

					savings_in_mcvalue = mc_value;

				}

				else {

					var savings_in_mcvalue = 0;

				}
				if($('#wastage_rate_type').val() == 1 && $("#manual_rate").is(":checked")){

					savings_in_wast_amt = parseFloat(parseFloat(savings_in_wast_wt) * parseFloat(board_rate)).toFixed(2);
				}
				
				else {

				savings_in_wast_amt = parseFloat(parseFloat(savings_in_wast_wt) * parseFloat(goldrate_22ct)).toFixed(2);

				}


				var amount = parseFloat(savings_in_wast_amt) + parseFloat(savings_in_mcvalue) - parseFloat(closing_add_chgs) + parseFloat(additional_benefits);

				console.log(savings_in_wast_amt);

				if (amount > 0) {

					chit_amount = amount;

				} else {

					chit_amount = curRow.find('.closing_amount').val();

				}

				curRow.find('.wastage_per').val(parseFloat(wastage_per).toFixed(2));

				curRow.find('.savings_in_wastage').val(parseFloat(parseFloat(wastage_wt)).toFixed(3));

				curRow.find('.mc_value').val(parseFloat(mc_value).toFixed(2));

				curRow.find('.savings_in_mcvalue').val(parseFloat(savings_in_mcvalue).toFixed(2));

				curRow.find('.closing_weight').val(parseFloat(saving_weight).toFixed(3));

				curRow.find('.rate_per_gram').val(parseFloat(goldrate_22ct).toFixed(2));

				curRow.find('.saved_weight').html(parseFloat(saving_weight).toFixed(3));

				$('.applied_wast_per').html(parseFloat(wastage_per).toFixed(2));

				$('.applied_mc').html(parseFloat(mc_value).toFixed(2));

			} else {

				if(weight_scheme_closure_type==3 && saving_weight > 0 ){

					var chit_rate_per_gram = rate_type == 1 ? goldrate_22ct : curRow.find('.chit_rate').val();
					
					curRow.find('.rate_per_gram').val(parseFloat(chit_rate_per_gram).toFixed(2));

					curRow.find('.saved_weight').html(parseFloat(saving_weight).toFixed(3));

					chit_amount = parseFloat(parseFloat(saving_weight)*parseFloat(chit_rate_per_gram)).toFixed(2);

				}else{

					saving_weight= 0;

					curRow.find('.saved_weight').html(parseFloat(saving_weight).toFixed(3));

					curRow.find('.closing_weight').val(parseFloat(saving_weight).toFixed(3));

					chit_amount = curRow.find('.closing_amount').val();

				}

			}
		}
		curRow.find('.chit_amt').val(parseFloat(Math.round(chit_amount)).toFixed(2));

	});

	calculate_purchase_details();

	calculate_sales_details();

}

function remove_chit_row(curRow) {

	// var topup = curRow.find('.topup_scheme').val();

	var del = false;

	var rate_type = curRow.find('#rate_type').val();

	var weight_scheme_closure_type = $('#weight_scheme_closure_type').val(); //1-General,2-Based on M.C & V.A
	
	var chit_rate_cal_type = $('#chit_rate_cal_type').val(); //1-Based on Average rate calculation ,2-Based on weight scheme closure type

	curRow.remove();
	
	if(chit_rate_cal_type == 1 && rate_type == 2){

       get_topup_chit_closing_balance(del = 'cht_delete','');	

	}
	
	
	else if(chit_rate_cal_type == 2 ){

		if(weight_scheme_closure_type==3)

		/* {

			set_tag_split_details();

			} */
 		{
 			set_tag_split_details();
 			set_custom_split_details();
 		}

	}

	calculate_sales_details();

	calculate_purchase_details();

}

function removeCat_row(curRow) {

	let removedId = curRow.attr('id');

	curRow.remove();

	if(items.length > 0){

		items = items.filter(function (item) {
			return item.rowId != removedId;
		});

	}

	calculate_sales_details();

	calculate_purchase_details();

}

//Catalog stone and material

function create_new_empty_est_cat_stone_item(curRow, id) {

	if (curRow != undefined) {

		$('#active_id').val(curRow.closest('tr').attr('id'));

	}

	var row = "";

	if (id != undefined) {

		my_Date = new Date();

		$.ajax({

			url: base_url + "index.php/admin_ret_estimation/get_stone_details?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

			data: { 'est_item_id': id },

			type: "POST",

			dataType: "json",

			async: false,

			success: function (data) {

				if (data.length > 0) {

					$.each(data, function (pkey, pitem) {

						var stones_list = '';

						$.each(materials, function (pkey, item) {

							var selected = "";

							if (item.stone_id == pitem.stone_id) {

								selected = "selected='selected'";

							}

							stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

						});

						row += '<tr><td><select class="form-control stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select><input type="hidden" class="stone_id" name="est_stones[stone_id][]" value="' + pitem['stone_id'] + '" /></td><td><input type="number" class="form-control stone_pcs" name="est_stones[stone_pcs][]" value="' + pitem['pieces'] + '" /></td><td><input class="form-control stone_wt" type="number" name="est_stones[stone_wt][]" value="' + pitem['wt'] + '" /></td><td><input type="number" class="form-control stone_price" name="est_stones[stone_price][]" value="' + pitem['price'] + '" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

					});

				}

				else {

					var stones_list = "<option value=''>-Select Stone-</option>";

					$.each(stones, function (pkey, pitem) {

						stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

					});

					row += '<tr><td><select class="form-control stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select></td><td><input type="number" class="form-control stone_pcs" name="est_stones_item[stone_pcs][]" value="" /></td><td><input class="form-control stone_wt" type="number" name="est_stones_item[stone_wt][]" value="" /></td><td><input type="number" class="form-control stone_price" name="est_stones_item[stone_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

				}

			},

		});

	}

	else {

		var catRow = $('#active_id').val();

		var row_st_details = $('#' + catRow).find('.stone_details').val();

		if (row_st_details != '' && row_st_details != [] && row_st_details != "[]" ) {

			var stone_details = (function(data) {
				if (!data) return [];
				try {
					// Check if it looks like base64 (no JSON characters)
					const isEncoded = !/[{}\[\]"]/.test(data) && data.length % 4 === 0;
					const jsonStr = isEncoded ? atob(data) : data;
					return JSON.parse(jsonStr);
				} catch (e) {
					console.error('Stone details parse error:', e);
					return [];
				}
			})(row_st_details);

			$.each(stone_details, function (pkey, pitem) {

				var stones_list = '';

				$.each(stones, function (pkey, item) {

					var selected = "";

					if (item.stone_id == pitem.stone_id) {

						selected = "selected='selected'";

					}

					stones_list += "<option value='" + pitem.stone_id + "'>" + item.stone_name + "</option>";

				});

				row += '<tr><td><select class="form-control stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select><input type="hidden" class="form-control stone_id" name="est_stones[stone_id][]" value="' + pitem['stone_id'] + '" /></td><td><input type="number" class="form-control stone_pcs" name="est_stones[stone_pcs][]" value="' + pitem['stone_pcs'] + '" /></td><td><input class="form-control stone_wt" type="number" name="est_stones[stone_wt][]" value="' + pitem['stone_wt'] + '" /></td><td><input type="number" class="form-control stone_price" name="est_stones[stone_price][]" value="' + pitem['stone_price'] + '" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

			});

		}

		else {

			var stones_list = "<option value=''>-Select Stone-</option>";

			$.each(stones, function (pkey, pitem) {

				stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

			});

			row += '<tr><td><select class="form-control stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select></td><td><input type="number" class="form-control stone_pcs" name="est_stones_item[stone_pcs][]" value="" /></td><td><input class="form-control stone_wt" type="number" name="est_stones_item[stone_wt][]" value="" /></td><td><input type="number" class="form-control stone_price" name="est_stones_item[stone_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

		}

	}

	$('#stoneModal .modal-body').find('#estimation_stone_item_details tbody').append(row);

	$('#stoneModal').modal('show');

}

$('#stoneModal .modal-body #create_stone_item_details').on('click', function () {

	if (validateStoneItemDetailRow()) {

		create_new_empty_est_cat_stone_item();

	} else {

		alert("Please fill required fields");

	}

});

$(document).on('keyup', ".stone_pcs,.stone_wt", function () {

	calculate_stone_amount();

});

$(document).on('change',".stone_rate,.stone_price",function()

{

	var row = $(this).closest('tr');

	check_min_max_stone_rate(row);

})

$('#estimation_stone_cus_item_details > tbody tr input[type=radio]').on('change', function () {

	console.log("LMX");

	calculate_stone_amount();

});

$(document).on('change', ".stone_cal_type", function () {

	$('#estimation_stone_cus_item_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		if (curRow.find('input[type=radio]:checked').val() == 1) {

			curRow.find('.stone_wt').attr('readonly', false);

			curRow.find('.stone_uom_id').attr('disabled', false);

		} else {

			//curRow.find('.stone_wt').val(0);

			curRow.find('.stone_wt').attr('readonly', true);

			curRow.find('.stone_uom_id').attr('disabled', true);

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

		var st_rate = 0;

		//stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);

		var stonePrice = (isNaN(curRow.find('.stone_price').val()) || curRow.find('.stone_price').val() == '') ? 0 : curRow.find('.stone_price').val();

		var stonePrice_focus = curRow.find(".stone_price").is(':focus');

		console.log(curRow.find('input[type=radio]:checked').val());

		if (curRow.find('input[type=radio]:checked').val() == 1) {

			if (stonePrice_focus == true) {

				st_rate = parseFloat(parseFloat(stonePrice) / parseFloat(stone_wt)).toFixed(2);

			} else {

				stone_amt = parseFloat(parseFloat(stone_wt) * parseFloat(stone_rate)).toFixed(2);

			}

		} else {

			if (stonePrice_focus == true) {

				st_rate = parseFloat(parseFloat(stonePrice) / parseFloat(stone_pcs)).toFixed(2);

			} else {

				stone_amt = parseFloat(parseFloat(stone_pcs) * parseFloat(stone_rate)).toFixed(2);

			}

		}

		if (stonePrice_focus == true) {

			curRow.find('.stone_rate').val(st_rate);
			curRow.find('.max_stn_price').val(stonePrice);

		} else {

			curRow.find('.stone_price').val(stone_amt);
			curRow.find('.max_stn_price').val(stone_amt);
		}

	});

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

$('#stoneModal').on('hidden.bs.modal', function () {

	$('#stoneModal .modal-body').find('#estimation_stone_item_details tbody').empty();

});

function validateStoneItemDetailRow() {

	var row_validate = true;

	$('#stoneModal .modal-body #estimation_stone_item_details> tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .stone_id').val() == "" || $(this).find('td:eq(1) .stone_pcs').val() == "" || $(this).find('td:eq(2) .stone_wt').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

$('#cus_stoneModal  #update_stone_details').on('click', function () {

	if (validateStoneCusItemDetailRow()) {

		var stone_details = [];

		var stone_price = 0;

		var stone_wt = 0;

		var certification_price = 0;

		var max_stn_amt = 0;

		$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details > tbody  > tr').each(function (index, tr) {

		    if($(this).find('.show_in_lwt').val()==1){

		        if(($(this).find('.stone_uom_id').val() == 6)) //For Carat Need to convert into gram

    			{

    				stone_wt+= parseFloat(parseFloat($(this).find('.stone_wt').val()/5));

    			} else {

    				stone_wt+=parseFloat($(this).find('.stone_wt').val());

    			}

		    }





			stone_price += parseFloat($(this).find('.stone_price').val());

			max_stn_amt = parseFloat($(this).find('.max_stn_price').val());

			stone_details.push({

				'show_in_lwt'           : $(this).find('.show_in_lwt').val(),

				'stone_id'              : $(this).find('.stone_id').val(),

				'stones_type'           : $(this).find('.stones_type').val(),

				'stone_pcs'             : $(this).find('.stone_pcs').val(),

				'act_stn_pcs'           : ($(this).find('.act_stn_pcs').val()!=undefined ? $(this).find('.act_stn_pcs').val() :0),

				'stone_wt'              : $(this).find('.stone_wt').val(),

				'act_stone_wt'          : ($(this).find('.act_stone_wt').val()!=undefined ? $(this).find('.act_stone_wt').val() : 0),

				'stone_cal_type'        : $(this).find('input[type=radio]:checked').val(),

				'stone_price'           : $(this).find('.stone_price').val(),

				'stone_rate'            : $(this).find('.stone_rate').val(),

				'stone_uom_id'          : $(this).find('.stone_uom_id').val(),

				'quality_id'			: $(this).find('.quality_id').val(),

				"max_stn_price" 		: parseFloat(max_stn_amt).toFixed(2)

			});

		});

		console.log(stone_details);

		$('#cus_stoneModal').modal('toggle');

		var catRow = $('#custom_active_id').val();

		$('#' + catRow).find('.stone_details').val(JSON.stringify(stone_details));

		$('#' + catRow).find('.stone_price').val(stone_price);

		$('#' + catRow).find('.price').val(certification_price);



		//Tag split

		var est_tag_id = $('#' + catRow).find('.est_tag_id').val();

		var gross_wt = $('#' + catRow).find('.gwt').val();

		if(tag_split_details){

    	    $.each(tag_split_details,function(key,val){

    	        if(val.tag_id ==  est_tag_id){

    	           val.less_wt = parseFloat(stone_wt).toFixed(3);

    	           val.net_wt  = parseFloat(parseFloat(gross_wt)-parseFloat(stone_wt)).toFixed(3);

    	           val.stone_details = stone_details;

    	        }

    	    });

        }

	    //Tag split





		var row = $('.' + catRow).closest('tr');

		update_custom_wastage_mc(row);

		calculatetag_SaleValue();

		calculateCustomItemSaleValue(row);

		$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();

	}

	else {

		alert('Please Fill The Required Details');

	}

});

$('#oldstoneModal  #update_stone_details').on('click', function () {

	var stone_details = [];

	var stone_price = 0;

	$('#stoneModal .modal-body #estimation_stone_item_details> tbody  > tr').each(function (index, tr) {

		stone_price += parseFloat($(this).find('td:eq(3) .stone_price').val());

		stone_details.push({ 'stone_id': $(this).find('td:first .stone_id').val(), 'stone_pcs': $(this).find('td:eq(1) .stone_pcs').val(), 'stone_wt': $(this).find('td:eq(2) .stone_wt').val(), 'stone_price': $(this).find('td:eq(3) .stone_price').val() });

	});

	$('#stoneModal').modal('toggle');

	var catRow = $('#active_id').val();

	$('#' + catRow).find('.stone_details').val(stone_details.length > 0 ? JSON.stringify(stone_details) : '');

	$('#' + catRow).find('.stone_price').val(stone_price);

	var row = $('#' + catRow).closest('tr');

	calculateSaleValue(row);

	$('#stoneModal .modal-body').find('#estimation_stone_item_details tbody').empty();

});

//other materials

function create_new_empty_est_cat_other_material(curRow, id) {

	if (curRow != undefined) {

		$('#active_id').val(curRow.closest('tr').attr('id'));

	}

	var row = "";

	console.log(id);

	if (id != undefined) {

		my_Date = new Date();

		$.ajax({

			url: base_url + "index.php/admin_ret_estimation/get_other_material_details?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

			data: { 'est_item_id': id },

			type: "POST",

			dataType: "json",

			async: false,

			success: function (data) {

				if (data.length > 0) {

					$.each(data, function (pkey, pitem) {

						var material_list = "";

						$.each(materials, function (pkey, item) {

							var selected = "";

							if (item.material_id == pitem.material_id) {

								selected = "selected='selected'";

							}

							material_list += "<option value='" + item.material_id + "' " + selected + ">" + item.material_name + "</option>";

						});

						row += '<tr><td><select class="material_id" name="est_materials[material_id][]">' + material_list + '</select><input type="hidden" class="material_id" name="est_materials[material_id][]" value="' + pitem['material_id'] + '" /></td><td><input class="material_wt" type="number" name="est_materials[material_wt][]" value="' + pitem['wt'] + '" /></td><td><input type="number" class="material_price" name="est_materials[material_price][]" value="' + pitem['price'] + '" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

					});

				}

				else {

					var material_list = "<option value=''> - Select Material - </option>";

					$.each(materials, function (pkey, pitem) {

						material_list += "<option value='" + pitem.material_id + "'>" + pitem.material_name + "</option>";

					});

					row += '<tr><td><select class="material_id" name="est_materials[material_id][]">' + material_list + '</select></td><td><input class="material_wt" type="number" name="est_materials[material_wt][]" value="" /></td><td><input type="number" class="material_price" name="est_materials[material_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

				}

			},

		});

	}

	else {

		var catRow = $('#active_id').val();

		var row_mt_details = $('#' + catRow).find('td:eq(13) .material_details').val();

		if (row_mt_details != '') {

			var material_details = JSON.parse(row_mt_details);

			$.each(material_details, function (pkey, pitem) {

				var material_list = "";

				$.each(materials, function (pkey, item) {

					var selected = "";

					if (item.material_id == pitem.material_id) {

						selected = "selected='selected'";

					}

					material_list += "<option value='" + item.material_id + "' " + selected + ">" + item.material_name + "</option>";

				});

				row += '<tr><td><select class="material_id" name="est_materials[material_id][]">' + material_list + '</select><input type="hidden" class="material_id" name="est_materials[material_id][]" value="' + pitem['material_id'] + '" /></td><td><input class="material_wt" type="number" name="est_materials[material_wt][]" value="' + pitem['material_wt'] + '" /></td><td><input type="number" class="material_price" name="est_materials[material_price][]" value="' + pitem['material_price'] + '" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

			});

		}

		else {

			var material_list = "<option value=''> - Select Material - </option>";

			$.each(materials, function (pkey, pitem) {

				material_list += "<option value='" + pitem.material_id + "'>" + pitem.material_name + "</option>";

			});

			row += '<tr><td><select class="material_id" name="est_materials[material_id][]">' + material_list + '</select></td><td><input class="material_wt" type="number" name="est_materials[material_wt][]" value="" /></td><td><input type="number" class="material_price" name="est_materials[material_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

		}

	}

	$('#other_material_modal .modal-body').find('#estimation_other_material_details tbody').append(row);

	$('#other_material_modal').modal('show');

}

$('#other_material_modal .modal-body #create_material_item_details').on('click', function () {

	if (validateMaterialItemDetailRow()) {

		create_new_empty_est_cat_other_material();

	} else {

		alert("Please fill required fields");

	}

});

function validateMaterialItemDetailRow() {

	var row_validate = true;

	$('#other_material_modal .modal-body #estimation_other_material_details> tbody  > tr').each(function (index, tr) {

		if ($(this).find('td:first .material_id').val() == "" || $(this).find('td:eq(1) .material_wt').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

$('#other_material_modal  #close_material_details').on('click', function () {

	$('#other_material_modal .modal-body').find('#estimation_other_material_details tbody').empty();

});

$('#other_material_modal  #update_material_details').on('click', function () {

	var material_details = [];

	var material_price = 0;

	$('#other_material_modal .modal-body #estimation_other_material_details> tbody  > tr').each(function (index, tr) {

		material_price += parseFloat($(this).find('td:eq(2) .material_price').val());

		material_details.push({ 'material_id': $(this).find('td:first .material_id').val(), 'material_wt': $(this).find('td:eq(1) .material_wt').val(), 'material_price': $(this).find('td:eq(2) .material_price').val() });

	});

	$('#other_material_modal').modal('toggle');

	var catRow = $('#active_id').val();

	$('#' + catRow).find('td:eq(13) .material_details').val(material_details.length > 0 ? JSON.stringify(material_details) : '');

	$('#' + catRow).find('td:eq(13) .material_price').val(material_price);

	var row = $('#' + catRow).closest('tr');

	calculateSaleValue(row);

	$('#other_material_modal .modal-body').find('#estimation_other_material_details tbody').empty();

});

//other materials

//catalog

//custom

function create_new_empty_est_cus_stone_item(curRow, id) {//estimation_tag_details

	//$('#estimation_stone_cus_item_details tbody').empty();

	// Check if the row is from a tagged item (tag code loaded)
	var isTaggedItem = false;
	if (curRow != undefined) {
		var parentTable = curRow.closest('table').attr('id');
		if (parentTable == 'estimation_tag_details') {
			// Check if this row has a tag_id (indicating it was loaded via Tag Code)
			var tagId = curRow.find('.est_tag_id').val();
			if (tagId && tagId != '' && tagId != '0') {
				isTaggedItem = true;
			}
		}
	}

	if (curRow != undefined) {

		$('#custom_active_id').val(curRow.closest('tr').attr('id'));

		console.log("TABLE ID : ", curRow.closest('table').attr('id'));

		$('#custom_active_table').val(curRow.closest('table').attr('id'));

	}

	var row = "";

	var ispartly_sale = $('#' + $('#custom_active_id').val()).find('.is_partial').val();

	if (ispartly_sale == undefined || ispartly_sale == 0 && curRow != undefined) {

		if (curRow.closest('table').attr('id') == 'estimation_custom_details') {

			ispartly_sale = 1;

		}

	} else if (curRow == undefined && $('#custom_active_table').val() == 'estimation_custom_details') {

		ispartly_sale = 1;

	}

	if (id != undefined) {

		my_Date = new Date();

		$.ajax({

			url: base_url + "index.php/admin_ret_estimation/get_stone_details?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

			data: { 'est_item_id': id },

			type: "POST",

			dataType: "json",

			async: false,

			success: function (data) {

				if (data.length > 0) {

					$.each(data, function (pkey, pitem) {

						var stones_list = '';

						$.each(materials, function (pkey, item) {

							var selected = "";

							if (item.stone_id == pitem.stone_id) {

								selected = "selected='selected'";

							}

							stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

						});

						row += '<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select><input type="hidden" class="stone_id" name="est_stones[stone_id][]" value="' + pitem['stone_id'] + '" /></td><td><input type="number" class="stone_pcs" name="est_stones[stone_pcs][]" value="' + pitem['pieces'] + '" /></td><td><input class="stone_wt" type="number" name="est_stones[stone_wt][]" value="' + pitem['wt'] + '" /></td><td><input type="number" class="stone_price" name="est_stones[stone_price][]" value="' + pitem['price'] + '" /><input type="number" class="max_stn_price" name="est_stones[max_stn_price][]" value="' + pitem['max_stn_price'] + '" /></td><td>' + (isTaggedItem ? '<span class="text-muted"><i class="fa fa-lock"></i></span>' : '<a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>') + '</td></tr>';

					});

				}

				else {

					var stones_list = "<option value=''>-Select Stone-</option>";

					$.each(stones, function (pkey, pitem) {

						stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

					});

					row += '<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select></td><td><input type="number" class="stone_pcs" name="est_stones_item[stone_pcs][]" value="" /></td><td><input class="stone_wt" type="number" name="est_stones_item[stone_wt][]" value="" /></td><td><input type="number" class="stone_price" name="est_stones_item[stone_price][]" value=""  /></td><input type="number" class="max_stn_price" name="est_stones[max_stn_price][]" value="" /><td>' + (isTaggedItem ? '<span class="text-muted"><i class="fa fa-lock"></i></span>' : '<a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>') + '</td></tr>';

				}

			},

		});

	}

	else {

		var catRow = $('#custom_active_id').val();

		console.log(catRow);

		var row_st_details = $('#' + catRow).find('.stone_details').val();

		if (row_st_details != '' && row_st_details != '[]' && curRow != undefined) {

			/*var stone_details=JSON.parse(row_st_details);

			 $.each(stone_details, function (pkey, pitem) {

					 var stones_list='';

				 $.each(stones, function (pkey, item)

				 {

					 var selected = "";

					 if(item.stone_id == pitem.stone_id)

					 {

						 selected = "selected='selected'";

					 }

					 stones_list += "<option value='"+pitem.stone_id+"'>"+item.stone_name+"</option>";

				 });

				 row+='<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">'+stones_list+'</select><input type="hidden" class="stone_id" name="est_stones[stone_id][]" value="'+pitem['stone_id']+'" /></td><td><input type="number" class="stone_pcs" name="est_stones[stone_pcs][]" value="'+pitem['stone_pcs']+'" /></td><td><input class="stone_wt" type="number" name="est_stones[stone_wt][]" value="'+pitem['stone_wt']+'" /></td><td><input type="number" class="stone_price" name="est_stones[stone_price][]" value="'+pitem['stone_price']+'" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

			 });*/

			var stone_details = JSON.parse(row_st_details);

			console.log(stone_details);

			$.each(stone_details, function (pkey, pitem) {

				var stones_list = '';

				var stones_type_list = '';

				var uom_list = '';

				var html = '';

				var cal_type = pitem.stone_cal_type;

				$.each(stones, function (pkey, item) {

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

				});

				var empty_quality_id = false;

				$.each(quality_code,function(pkey, item){

					var quality_selected = "";

					if (item.quality_id == pitem.quality_id) {

						quality_selected = "selected='selected'";

						empty_quality_id = true;
					}

					quality_list += "<option value='" + item.quality_id + "' " + quality_selected + ">" + item.code + "</option>";
				})

				if (!empty_quality_id) {

    				quality_list = "<option value='' selected disabled>- Select Quality -</option>" + quality_list;
					
				}

				/*row += '<tr>'

					+'<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]" disabled value="'+(pitem.show_in_lwt==1 ? 1:0)+'" '+(pitem.show_in_lwt==1 ? 'checked' :'' )+' ></td>'

					+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" disabled >'+stones_type_list+'</select></td>'

					+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" disabled >'+stones_list+'</select></td>'

					+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" readonly value="'+pitem['stone_pcs']+'" style="width: 100%;"/></td>'

					+'<td><div class="input-group"><input class="stone_wt form-control" type="number" disabled name="est_stones_item[stone_wt][]" value="'+pitem['stone_wt']+'" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" '+(ispartly_sale == 0 ? 'disabled' : '')+' >'+uom_list+'</select></span></div></td>'

					+'<td><div class="form-group"><input class="stone_cal_type" type="radio" disabled name="est_stones_item[cal_type]['+pkey+']" value="1" '+(cal_type == 1 ? 'checked' : '')+'> By Wt&nbsp;<input type="radio" '+(ispartly_sale == 0 ? 'disabled' : '')+' name="est_stones_item[cal_type]['+pkey+']" '+(cal_type == 2 ? 'checked' : '')+' class="stone_cal_type" value="2">By Pcs</div></td>'

					+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" readonly value="'+pitem['stone_rate']+'"  style="width:100%;"/></td>'

					+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+pitem['stone_price']+'"  style="width:100%;" /></td>'

					+'<td></td></tr>';*/

				row += '<tr>'

					+ '<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]"  disabled value="' + (pitem.show_in_lwt == 1 ? 1 : 0) + '" ' + (pitem.show_in_lwt == 1 ? 'checked' : '') + ' ><input type="hidden" class="quality_id" name="est_stones_item[quality_id][]" value="'+pitem.quality_id+'"></td>'

					+ '<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" disabled >' + stones_type_list + '</select></td>'

					+ '<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"  disabled>' + stones_list + '</select></td>'

					+'<td><select class="quality_id form-control" name="est_stones_item[quality_id][]" style="width:120px;" disabled>'+quality_list+'</select><input type="hidden" id="id_quality"></td>'

					+ '<td><input type="hidden" class="act_stn_pcs" value="' + pitem['stone_pcs'] + '"><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]"  value="' + pitem['stone_pcs'] + '" style="width: 100%;" ' + (ispartly_sale == 0 ? 'readonly' : '') + '/></td>'

					+ '<td><div class="input-group"><input type="hidden" class="act_stone_wt" value="' + pitem['stone_wt'] + '"><input class="stone_wt form-control" type="number"  name="est_stones_item[stone_wt][]" value="' + pitem['stone_wt'] + '" style="width:100%;" ' + (ispartly_sale == 0 ? 'disabled' : '') + '/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" ' + (ispartly_sale == 0 ? 'disabled' : '') + ' >' + uom_list + '</select></span></div></td>'

					+ '<td><div class="form-group"><input class="stone_cal_type" type="radio"  name="est_stones_item[cal_type][' + pkey + ']" value="1" ' + (cal_type == 1 ? 'checked' : '') + '> By Wt&nbsp;<input type="radio" ' + (ispartly_sale == 0 ? 'disabled' : '') + ' name="est_stones_item[cal_type][' + pkey + ']" ' + (cal_type == 2 ? 'checked' : '') + ' class="stone_cal_type" value="2">By Pcs</div></td>'

					+ '<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]"  value="' + pitem['stone_rate'] + '"  style="width:100%;"/></td>'

					+ '<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="' + pitem['stone_price'] + '"  style="width:100%;" /><input type="hidden" name="est_stones_item[max_stn_price][]" class="max_stn_price" value="' + pitem['max_stn_price'] + '"></td>'

					+ '<td>' + (isTaggedItem ? '<span class="text-muted"><i class="fa fa-lock"></i></span>' : '<a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>') + '</td>'

					+ '</tr>';

			});

		}

		else {

			if (ispartly_sale == 1) {

				var stones_list = "<option value=''>-Select Stone-</option>";

				var stones_type = "<option value=''>-Stone Type-</option>";

				var uom_list = "<option value=''>-Stone UOM-</option>";

				var quality_list = "<option value=''>-Stone Quality-</option>";

				$.each(stones, function (pkey, pitem) {

					stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

				});

				$.each(stone_types, function (pkey, pitem) {

					stones_type += "<option value='" + pitem.id_stone_type + "'>" + pitem.stone_type + "</option>";

				});

				$.each(uom_details, function (pkey, pitem) {

					var selected = pitem.is_default == 1 ? "selected='selected'" : "";

					uom_list += "<option value='" + pitem.uom_id + "' " + selected + ">" + pitem.uom_name + "</option>";

				});
				$.each(quality_code, function (pkey, pitem) {

					var selected = pitem.is_default == 1 ? "selected='selected'" : "";

					quality_list += "<option value='" + pitem.quality_id + "' " + selected + ">" + pitem.code + "</option>";

				});

				var rowId = $('#estimation_stone_cus_item_details tbody tr').length;

				var active_row = new Date().getTime();

				row += '<tr id="' + active_row + '">'

					+ '<td><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="1" checked></td>'

					+ '<td><select class="stones_type form-control" name="est_stones_item[stones_type][]">' + stones_type + '</select></td>'

					+ '<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"></select></td>'

					+'<td><select class="quality_id form-control" name="est_stones_item[quality_id][]" style="width:120px;">'+quality_list+'</select></td>'

					+ '<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="" style="width: 100%;"/></td>'

					+ '<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">' + uom_list + '</select></span></div></td>'

					+ '<td><div class="form-group"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type][' + rowId + ']" value="1" checked="true"> By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + rowId + ']" class="stone_cal_type" value="2">By Pcs</div></td>'

					+ '<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value=""  style="width:100%;"/></td>'

					+ '<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value=""  style="width:100%;" /><input type="hidden" name="est_stones_item[max_stn_price][]" class="max_stn_price" ></td>'

					+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

				/*var stones_list = "<option value=''>-Select Stone-</option>";

				$.each(stones, function (pkey, pitem) {

					stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";

				});

				row += '<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">'+stones_list+'</select></td><td><input type="number" class="stone_pcs" name="est_stones_item[stone_pcs][]" value="" /></td><td><input class="stone_wt" type="number" name="est_stones_item[stone_wt][]" value="" /></td><td><input type="number" class="stone_price" name="est_stones_item[stone_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

				*/

			}

		}

	}

	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);

	$('#cus_stoneModal').modal('show');

}

$('#cus_stoneModal .modal-body #create_stone_item_details').on('click', function () {

	if (validateStoneCusItemDetailRow()) {

		create_new_empty_est_cus_stone_item();

	} else {

		alert("Please fill required fields");

	}

});

$('#cus_stoneModal').on('hidden.bs.modal', function () {

	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();

});

function validateStoneCusItemDetailRow() {

	var row_validate = true;

	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function (index, tr) {

		if ($(this).find('.stone_id').val() == "" || $(this).find('.stone_pcs').val() == "" || $(this).find('.stone_wt').val() == "" || $(this).find('.stone_rate').val() == "" || $(this).find('.stone_price').val() == "" || $(this).find('.stone_uom_id').val() == "") {

			row_validate = false;

		}

		if ($(this).find('.max_stn_price').length == 0 || $(this).find('.max_stn_price').val() == "" || $(this).find('.max_stn_price').val() == undefined || $(this).find('.max_stn_price').val() == null) {

			if($(this).find('.stone_price').val() != "" && $(this).find('.stone_price').val() > 0) {
				$(this).find('.max_stn_price').val($(this).find('.stone_price').val());
			}

		}

	});

	return row_validate;

}

function create_new_empty_est_old_metal_stone(curRow, id) {

	$('#estimation_stone_old_metal_details tbody').empty();

	if (curRow != undefined) {

		$('#old_metal_active_id').val(curRow.closest('tr').attr('id'));

	}

	var row = "";

	if (id != undefined) {

		my_Date = new Date();

		$.ajax({

			url: base_url + "index.php/admin_ret_estimation/get_old_metal_stone_details?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

			data: { 'est_old_metal_sale_id': id },

			type: "POST",

			dataType: "json",

			async: false,

			success: function (data) {

				if (data.length > 0) {

					$.each(data, function (pkey, pitem) {

						var stones_list = '';

						$.each(materials, function (pkey, item) {

							var selected = "";

							if (item.stone_id == pitem.stone_id) {

								selected = "selected='selected'";

							}

							stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

						});

						row += '<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select><input type="hidden" class="stone_id" name="est_stones[stone_id][]" value="' + pitem['stone_id'] + '" /></td><td><input type="number" class="stone_pcs" name="est_stones[stone_pcs][]" value="' + pitem['pieces'] + '" /></td><td><input class="stone_wt" type="number" name="est_stones[stone_wt][]" value="' + pitem['wt'] + '" /></td><td><input type="number" class="stone_price" name="est_stones[stone_price][]" value="' + pitem['price'] + '" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

					});

				}

				else {

					var stones_list = "<option value=''>-Select Stone-</option>";

					$.each(stones, function (pkey, pitem) {

						stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

					});

					row += '<tr><td><select class="stone_id" name="est_stones_item[stone_id][]">' + stones_list + '</select></td><td><input type="number" class="stone_pcs" name="est_stones_item[stone_pcs][]" value="" /></td><td><input class="stone_wt" type="number" name="est_stones_item[stone_wt][]" value="" /></td><td><input type="number" class="stone_price" name="est_stones_item[stone_price][]" value=""  /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

				}

			},

		});

	}

	else {

		var catRow = $('#old_metal_active_id').val();

		var row_st_details = $('#' + catRow).find('.stone_details').val();

		if (row_st_details.length > 0) {

			var stone_details = JSON.parse(row_st_details);

			$.each(stone_details, function (pkey, pitem) {

				var stones_list = '';

				var stones_type_list = '';

				var uom_list = '';

				var html = '';

				$.each(stones, function (key, item) {

					console.log(item);

					var selected = "";

					if (item.stone_id == pitem.stone_id) {

						selected = "selected='selected'";

					}

					stones_list += "<option value='" + pitem.stone_id + "'>" + item.stone_name + "</option>";

				});

				$.each(stone_types, function (key, item) {

					var st_type_selected = "";

					if (item.id_stone_type == pitem.stones_type) {

						st_type_selected = "selected='selected'";

					}

					stones_type_list += "<option value='" + item.id_stone_type + "' " + st_type_selected + ">" + item.stone_type + "</option>";

				});

				$.each(uom_details, function (key, item) {

					var uom_selected = "";

					if (item.uom_id == pitem.stone_uom_id) {

						uom_selected = "selected='selected'";

					}

					uom_list += "<option value='" + item.uom_id + "' " + uom_selected + ">" + item.uom_name + "</option>";

				});

				row += '<tr data-rowno="' + (pkey + 1) + '">'

					+ '<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]" value="' + (pitem.show_in_lwt == 1 ? 1 : 0) + '" ' + (pitem.show_in_lwt == 1 ? 'checked' : '') + ' ></td>'

					+ '<td><select class="stones_type form-control" name="est_stones_item[stones_type][]" >' + stones_type_list + '</select></td>'

					+ '<td><select class="stone_id form-control" name="est_stones_item[stone_id][]" >' + stones_list + ' </select></td>'

					+ '<td><input type="number" class="old_stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="' + pitem['stone_pcs'] + '" style="width: 100%;"/></td>'

					+ '<td><div class="input-group"><input class="old_stone_wt form-control" type="number" name="est_stones_item[old_stone_wt][]" value="' + pitem['stone_wt'] + '" style="width:100px;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" >' + uom_list + '</select></span></div></td>'

					+ '<td><div class="form-group" style="width: 100px;"><input class="old_stone_cal_type" type="radio" name="est_stones_item[cal_type][' + pkey + ']" value="1" ' + (pitem.old_stone_cal_type == 1 ? 'checked' : '') + ' > By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + pkey + ']" class="old_stone_cal_type" value="2" ' + (pitem.old_stone_cal_type == 2 ? 'checked' : '') + ' > By Pcs</div></td>'

					+ '<td><input type="number" class="old_stone_rate form-control" name="est_stones_item[old_stone_rate][]" value="' + pitem['old_stone_rate'] + '"  style="width:80%;"/></td>'

					+ '<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="' + pitem['stone_price'] + '"  style="width:100%;"/></td>'

					+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

			});

		}

		else {

			var stones_list = "<option value=''>-Select Stone-</option>";

			var stones_type = "<option value=''>-Stone Type-</option>";

			var uom_list = "<option value=''>-UOM-</option>";

			$.each(stones, function (pkey, pitem) {

				//console.log(pitem);

				stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

			});

			$.each(stone_types, function (pkey, pitem) {

				stones_type += "<option value='" + pitem.id_stone_type + "'>" + pitem.stone_type + "</option>";

			});

			$.each(uom_details, function (pkey, pitem) {

				uom_list += "<option value='" + pitem.uom_id + "'>" + pitem.uom_name + "</option>";

			});

			let rowNo = $('#old_stoneModal .modal-body').find('#estimation_stone_old_metal_details tbody tr:last').attr('data-rowno');

			if (/^\d+$/.test(rowNo)) {

				rowNo = parseInt(rowNo) + 1;

			} else {

				rowNo = 1;

			}

			row += '<tr data-rowno="' + rowNo + '">'

				+ '<td style="width:8%;"><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="1" checked></td>'

				+ '<td style="width:15%;"><select class="stones_type form-control" name="est_stones_item[stones_type][]" >' + stones_type + '</select></td>'

				+ '<td style="width:15%;"><select class="stone_id form-control" name="est_stones_item[stone_id][]">' + stones_list + '</select></td>'

				+ '<td style="width:10%;"><input type="number" class="old_stone_pcs form-control" name="est_stones_item[old_stone_pcs][]" value="" style="width: 100%;"/></td>'

				+ '<td><div class="input-group"><input class="old_stone_wt form-control" type="number" name="est_stones_item[old_stone_wt][]" value="" style="width:100px;"/><span class="input-group-btn" style="width: 70px;" ><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" style="width: 70px;">' + uom_list + '</select></span></div></td>'

				+ '<td ><div class="form-group" style="width: 100px;"><input class="old_stone_cal_type" type="radio" name="est_stones_item[cal_type][' + (rowNo - 1) + ']" value="1" checked="true"> By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + (rowNo - 1) + ']" class="old_stone_cal_type" value="2"> By Pcs</div></td>'

				+ '<td style="width:15%;"><input type="number" class="old_stone_rate form-control" name="est_stones_item[old_stone_rate][]" value=""  style="width:80%;"/></td>'

				+ '<td><input type="number" class="stone_price" name="est_stones_item[stone_price][]" value=""  /></td>'

				+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

		}

	}

	$('#old_stoneModal .modal-body').find('#estimation_stone_old_metal_details tbody').append(row);

	$('#old_stoneModal').modal('show');

}

$('#old_stoneModal .modal-body #create_stone_old').on('click', function () {

	if (validateStoneoldMetalDetailRow()) {

		create_empty_est_old_metal_stone();

	} else {

		alert("Please fill required fields");

	}

});

function create_empty_est_old_metal_stone(curRow, id) {

	if (curRow != undefined) {

		$('#old_metal_active_id').val(curRow.closest('tr').attr('id'));

	}

	var row = "";

	var catRow = $('#old_metal_active_id').val();

	var stones_list = "<option value=''>-Select Stone-</option>";

	var stones_type = "<option value=''>-Stone Type-</option>";

	var uom_list = "<option value=''>-UOM-</option>";

	$.each(stones, function (pkey, pitem) {

		stones_list += "<option value='" + pitem.stone_id + "'>" + pitem.stone_name + "</option>";

	});

	$.each(stone_types, function (pkey, pitem) {

		stones_type += "<option value='" + pitem.id_stone_type + "'>" + pitem.stone_type + "</option>";

	});

	$.each(uom_details, function (pkey, pitem) {

		uom_list += "<option value='" + pitem.uom_id + "'>" + pitem.uom_name + "</option>";

	});

	$.each(stone_types, function (pkey, pitem) {

		stones_type += "<option value='" + pitem.id_stone_type + "'>" + pitem.stone_type + "</option>";

	});

	let rowNo = $('#old_stoneModal .modal-body').find('#estimation_stone_old_metal_details tbody tr:last').attr('data-rowno');

	if (/^\d+$/.test(rowNo)) {

		rowNo = parseInt(rowNo) + 1;

	} else {

		rowNo = 1;

	}

	row += '<tr data-rowno="' + rowNo + '">'

		+ '<td style="width:8%;"><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="1" checked></td>'

		+ '<td style="width:15%;"><select class="stones_type form-control" name="est_stones_item[stones_type][]" >' + stones_type + '</select></td>'

		+ '<td style="width:15%;"><select class="stone_id form-control" name="est_stones_item[stone_id][]"></select></td>'

		+ '<td style="width:10%;"><input type="number" class="old_stone_pcs form-control" name="est_stones_item[old_stone_pcs][]" value="" style="width: 100%;"/></td>'

		+ '<td><div class="input-group"><input class="old_stone_wt form-control" type="number" name="est_stones_item[old_stone_wt][]" value="" style="width:100px;"/><span class="input-group-btn" style="width: 70px;" ><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]" style="width: 70px;">' + uom_list + '</select></span></div></td>'

		+ '<td ><div class="form-group" style="width: 100px;"><input class="old_stone_cal_type" type="radio" name="est_stones_item[cal_type][' + (rowNo - 1) + ']" value="1" checked="true">By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type][' + (rowNo - 1) + ']" class="old_stone_cal_type" value="2"> By Pcs</div></td>'

		+ '<td style="width:15%;"><input type="number" class="old_stone_rate form-control" name="est_stones_item[old_stone_rate][]" value=""  style="width:80%;"/></td>'

		+ '<td><input type="number" class="stone_price" name="est_stones_item[stone_price][]" value=""  /></td>'

		+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

	$('#old_stoneModal .modal-body').find('#estimation_stone_old_metal_details tbody').append(row);

	$('#old_stoneModal').modal('show');

}

$('#old_stoneModal  #close_stone_details').on('click', function () {

	$('#old_stoneModal .modal-body').find('#estimation_stone_old_metal_details tbody').empty();

});

function validateStoneoldMetalDetailRow() {

	var row_validate = true;

	$('#old_stoneModal .modal-body #estimation_stone_old_metal_details> tbody  > tr').each(function (index, tr) {

		console.log(tr);

		let calc_type = parseInt($(this).find('input[type=radio]:checked').val());

		if ($(this).find('.stone_id').val() == "" || $(this).find('.old_stone_pcs').val() == "" || (calc_type != 1 && calc_type != 2) || (calc_type == 1 ? $(this).find('.old_stone_wt').val() == "" || $(this).find('.stone_uom_id').val() == "" : false) || $(this).find('.old_stone_rate').val() == "" || $(this).find('.stone_price').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

$('#old_stoneModal  #update_stone_details').on('click', function () {

	var stone_details = [];

	var stone_price = 0;

	var tag_less_wgt = 0;

	if (validateStoneoldMetalDetailRow()) {

		$('#old_stoneModal .modal-body #estimation_stone_old_metal_details> tbody  > tr').each(function (index, tr) {

			if ($(this).find('.show_in_lwt').val() == 1) {

				tag_less_wgt += parseFloat($(this).find('.old_stone_wt').val());

			}

			stone_id = $(this).find('.stone_id').val();

			console.log(stone_id);

			stone_price += parseFloat($(this).find('.stone_price').val());

			stone_details.push({

				'show_in_lwt': $(this).find('.show_in_lwt').val(),

				'stone_id': $(this).find('.stone_id').val(),

				'stones_type': $(this).find('.stones_type').val(),

				'stone_pcs': $(this).find('.old_stone_pcs').val(),

				'stone_wt': $(this).find('.old_stone_wt').val(),

				'old_stone_cal_type': $(this).find('input[type=radio]:checked').val(),

				'stone_price': $(this).find('.stone_price').val(),

				'old_stone_rate': $(this).find('.old_stone_rate').val(),

				'stone_uom_id': $(this).find('.stone_uom_id').val()

			});

		});

		console.log(stone_details);

		if (stone_id != '') {

			$('#old_stoneModal').modal('hide');

			var catRow = $('#old_metal_active_id').val();

			$('#' + catRow).find('.stone_details').val(stone_details.length > 0 ? JSON.stringify(stone_details) : '');

			$('#' + catRow).find('.stone_price').val(stone_price);

			var row = $('#' + catRow).closest('tr');

			calculateOldMatelItemSaleValue(row);

			$('#old_stoneModal .modal-body').find('#estimation_stone_item_details tbody').empty();

		}

	}

	else {

		alert('Please Fill The Required Details');

	}

});

$(document).on('keyup', ".old_stone_pcs,.old_stone_wt,.old_stone_rate", function () {

	calculate_old_stone_amount();

});

$(document).on('change', ".old_stone_cal_type", function () {

	$('#estimation_stone_old_metal_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		console.log((curRow.find('input[type=radio]:checked').val() == 1));

		if (curRow.find('input[type=radio]:checked').val() == 1) {

			curRow.find('.old_stone_wt').attr('readonly', false);

			curRow.find('.stone_uom_id').attr('disabled', false);

		} else {

			//curRow.find('.stone_wt').val(0);

			curRow.find('.old_stone_wt').attr('readonly', true);

			curRow.find('.stone_uom_id').attr('disabled', true);

		}

	});

	calculate_old_stone_amount();

});

function calculate_old_stone_amount() {

	$('#estimation_stone_old_metal_details > tbody tr').each(function (idx, row) {

		curRow = $(this);

		var stone_amt = 0;

		var old_stone_pcs = (isNaN(curRow.find('.old_stone_pcs').val()) || curRow.find('.old_stone_pcs').val() == '') ? 0 : curRow.find('.old_stone_pcs').val();

		var old_stone_wt = (isNaN(curRow.find('.old_stone_wt').val()) || curRow.find('.old_stone_wt').val() == '') ? 0 : curRow.find('.old_stone_wt').val();

		var old_stone_rate = (isNaN(curRow.find('.old_stone_rate').val()) || curRow.find('.old_stone_rate').val() == '') ? 0 : curRow.find('.old_stone_rate').val();

		//stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);

		console.log(curRow.find('input[type=radio]:checked').val());

		if (curRow.find('input[type=radio]:checked').val() == 1) {

			stone_amt = parseFloat(parseFloat(old_stone_wt) * parseFloat(old_stone_rate)).toFixed(2);

		} else {

			stone_amt = parseFloat(parseFloat(old_stone_pcs) * parseFloat(old_stone_rate)).toFixed(2);

		}

		curRow.find('.stone_price').val(stone_amt);

	});

}

function get_old_metal_rate(id_metal, row) {

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/get_old_metal_rate?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		data: { 'id_metal': id_metal },

		type: "POST",

		dataType: "JSON",

		success: function (data) {

			row.find('.old_rate').val(data.rate);

		},

		error: function (error) {

		}

	});

}

//old metal

function get_village_list() {

	var id_village = $('#id_village').val();

	var ed_id_village = $('#ed_id_village').val();

	$('#sel_village option').remove();

	$('#id_village').val("");

	$('#ed_sel_village option').remove();

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/ajax_get_village?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		type: "GET",

		dataType: "JSON",

		success: function (data) {

			$.each(data, function (key, item) {

				if (item.is_default == 1) {

					$('#id_village').val(item.id_village);

				}

				$("#sel_village,#ed_sel_village").append(

					$("<option></option>")

						.attr("value", item.id_village)

						.text(item.village_name)

				);

			});

			/* var id_village = $('#id_village').val();

			var ed_id_village = $('#ed_id_village').val(); */

			$("#sel_village,#ed_sel_village").select2({

				placeholder: "Select Area",

				allowClear: true

			});

			if ($("#sel_village").length) {

				$("#sel_village").select2("val", (id_village != null ? id_village : ''));

			}

			if ($("#ed_sel_village").length) {

				$("#ed_sel_village").select2("val", (ed_id_village != null && ed_id_village > 0 ? ed_id_village : ''));

			}

			$(".overlay").css("display", "none");

		},

		error: function (error) {

		}

	});

}

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

//Employee Filter

function get_employee(id_branch) {

	$('#emp_select option').remove();

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/get_employee?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		data: { 'id_branch': id_branch },

		type: "POST",

		dataType: "JSON",

		success: function (data) {

			var id_employee = $('#id_employee').val();

			emp_details = data;

			$.each(data, function (key, item) {

				$("#emp_select").append(

					$("<option></option>")

						.attr("value", item.id_employee)

						.text(item.emp_name)

				);

			});

			$("#emp_select").select2({

				placeholder: "Sales Employee",

				allowClear: true

			});

			$("#emp_select").select2("val", (id_employee != '' && id_employee > 0 ? id_employee : ''));

			$(".overlay").css("display", "none");

		},

		error: function (error) {

		}

	});

}

function get_taxgroup_items() {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_billing/getAllTaxgroupItems/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "GET",

		success: function (data) {

			tax_details = data;

			console.log("tax_details", tax_details);

		}

	});

}

$('#order_search').on('click', function () {

	var order_no = $('#est_order').val();

	//getTagOrder_details(order_no);

	$('#tag_search').trigger('click');

});

function get_order_items(order_no) {

	my_Date = new Date();

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_estimation/get_order_details/?nocache=' + my_Date.getUTCSeconds(),

		dataType: 'json',

		data: { 'searchTxt': order_no, 'id_branch': $('#id_branch').val() },

		success: function (data) {

			if (data.order_no != null && data.tot_pcs > 0) {

				var tagged_pcs = 0;

				$('#estimation_tag_details > tbody tr').each(function (bidx, brow) {

					tag_row = $(this);

					if (data.order_no == tag_row.find('.order_no').html()) {

						console.log(tag_row.find('.piece').val());

						tagged_pcs += parseFloat(tag_row.find('.piece').val());

					}

				});

				if (tagged_pcs != data.tot_pcs) {

					$('#est_print').prop('disabled', true);

					alert('Please Select All Order Items');

				} else {

					$('#est_print').prop('disabled', false);

				}

			}

		}

	});

}

function getTagOrder_details(order_no) {

	$('#searchEstiOrderAlert').html('');

	my_Date = new Date();

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_estimation/getOrderBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: 'json',

		data: { 'searchTxt': order_no, 'id_branch': $("#id_branch").val(), 'fin_year': $('#fin_year').val() },

		success: function (data) {

			if (data.responseData != null && data.responseData.length > 0) {

				var row = "";

				var rowExist = false;

				var paid_advance = 0;

				var paid_weight = 0;

				var wt_amt = 0;

				order_adv_details = data.adv_details;

				if (data.adv_details.length > 0) {

					$.each(data.adv_details, function (key, item) {

						paid_advance += parseFloat(item.paid_advance);

						paid_weight += parseFloat(item.paid_weight);

						wt_amt += parseFloat(item.paid_weight * item.rate_per_gram);

					});

				}

				$('.summary_adv_paid_amt').html(parseFloat(paid_advance) + parseFloat(wt_amt));

				$('.summary_adv_paid_weight').html(parseFloat(paid_weight).toFixed(3));

				let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

				$.each(data.responseData, function (key, item) {

					$('#est_cus_name').val(item.customer);

					$('#cus_id').val(item.id_customer);

					$('#estimation_tag_details > tbody tr').each(function (bidx, brow) {

						tag_row = $(this);

						if (item.tag_id == tag_row.find('.est_tag_id').val()) {

							rowExist = true;

							alert('Tag Already Exists');

							return false;

						}

					});

					if (!rowExist) {

						var stone_details = [];

						var other_metal_details = [];

						var stone_price = 0;

						var tag_other_itm_amount = 0;

						$.each(item.stone_details, function (skey, sitem) {

							if (sitem.pieces > 0) {

								stone_price += parseFloat(sitem.amount);

								stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type, "max_stn_price": sitem.amount });

							}

						});

						$.each(item.other_metal_details, function (skey, sitem) {

							tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

							other_metal_details.push({

								"tag_other_itm_id": sitem.tag_other_itm_id,

								"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

								"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

								"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

								"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

								"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

								"tag_other_itm_uom": sitem.tag_other_itm_uom,

								"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

								"tag_other_itm_mc": sitem.tag_other_itm_mc,

								"tag_other_itm_rate": sitem.tag_other_itm_rate,

								"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

								"tag_other_itm_amount": sitem.tag_other_itm_amount,

								"purname": sitem.purname,

								"metal_name": sitem.metal_name,

							});

						});

						let pur_mc = typeof item.po_details != 'undefined' && item.po_details != null && item.po_details.length != 0 ? (!(item.po_details[0].mc_value >= 0) || item.po_details[0].mc_value == null ? 0 : item.po_details[0].mc_value) : 0;

						let pur_va = typeof item.po_details != 'undefined' && item.po_details != null && item.po_details.length != 0 ? (!(item.po_details[0].item_wastage >= 0) || item.po_details[0].item_wastage == null ? 0 : item.po_details[0].item_wastage) : 0;

						let rate_per_grm = 0;

						if (item.rate_field != '' && item.rate_type == 2)  // Based on Delivery date

						{

							rate_per_grm = (isNaN($('.' + item.rate_field).html()) || $('.' + item.rate_field).html() == '') ? 0 : $('.' + item.rate_field).html();

						}

						else // Based on order rate

						{

							rate_per_grm = parseFloat(item.order_rate).toFixed(2);

						}

						var tag_image = base_url + 'assets/img/no_image.png';

						if (item.tag_images.length > 0) {

							$.each(item.tag_images, function (key, val) {

								if (val.is_default == 1) {

									tag_image = base_url + 'assets/img/tag/' + val.image;

								}

							});

						}

						var rowId = new Date().getTime();

						var select_emp = "<option value=''>-Select Employee-</option>";

						$.each(emp_details, function (pkey, emp) {

							console.log(emp_details);

							select_emp += "<option value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

						});

						row += '<tr id="' + rowId + '">'

							+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + item.label + ' placeholder="Enter tag code" required autocomplete="off"/><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + item.tag_id + ' placeholder="Enter tag code" required /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + item.id_orderdetails + '"/><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + item.order_no + '"/><input class="rate_field" type="hidden"  value="' + item.rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + item.market_rate_field + '"/></td>'

							+ '<td><img src="' + tag_image + '" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="' + item.tag_id + '" id="edit"><i class="fa fa-eye" ></i></a></td>'

							+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_tag[item_emp_id][]" >' + select_emp + '</select></td>'

							+ '<td><input type="checkbox" class="partial"><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"></td>'

							+ '<td><div class="prodct_name">' + item.product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + item.product_id + ' /><input type="hidden" class="metal_type" value=' + item.id_metal + '><input type="hidden" class="scheme_closure_benefit" value=' + item.scheme_closure_benefit + '></td>'

							+ '<td><div class="design_name">' + item.design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + item.design_id + ' /></td>'

							+ '<td><div class="sub_design_name">' + item.sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value="' + item.id_sub_design + '" /></td>'

							+ '<td><div class="section_name">' + item.section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + item.id_section + ' /></td>'

							+ '<td><div class="huid">' + item.huid + '</div></td>'

							+ '<td><div class="order_no">' + order_no + '</td>'

							+ '<td><div class="purity">' + item.purname + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + item.purity + ' /></td>'

							+ '<td><div class="sizes">' + item.size_name + '</div><input type="hidden" class="size" name="est_tag[size][]" value="' + item.size + '"/></td>'

							+ '<td><div class="pieces">' + item.piece + '</div><input type="hidden" class="piece" name="est_tag[piece][]" value=' + item.piece + ' /></td>'

							+ '<td><input type="text" class="gwt" name="est_tag[gwt][]" step="any" value=' + item.gross_wt + ' readonly/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value="' + item.gross_wt + '"/><input type="hidden" class="act_gwt" value="' + item.gross_wt + '"/></td>'

							// +'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" value='+item.less_wt+'></td>'

							+ '<td><div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + item.less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

							+ '<td><div class="nwt">' + item.net_wt + '</div><input type="hidden" class="tot_nwt" name="est_tag[nwt][]" value=' + item.net_wt + ' /><input type="hidden" class="tot_tag_nwt" value=' + item.net_wt + ' /></td>'

							+ '<td><input type="text" class="market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + rate_per_grm + '" /></td>'

							+ '<td><div class="wastage" style="display:none">' + item.retail_max_wastage_percent + '</div><input type="number" ' + va_readonly + ' name="est_tag[wastage][]" class="wastage_max_per" value=' + item.retail_max_wastage_percent + ' /></td>'

							+ '<td><div class="est_wastage_wt" style="display:none"></div><input type="number" ' + va_readonly + ' name="est_tag[est_wastage_wt][]" class="est_wastage_wt" value="" /></td>'

							/*+'<td><div class="mc">'+item.tag_mc_value+'</div></td>'*/

							+ '<td><select class="form-control est_mc_type" '+mc_readonly+' style="width:80px; '+mc_type_selection+'"><option value="2" ' + (item.tag_mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (item.tag_mc_type == 1 ? 'selected' : '') + '>Piece</option></select></td>'

							+ '<td><div class="mc_val" style="display:none">' + item.tag_mc_value + '</div><input class="act_mc_value"  name="est_tag[mc][]" type="number" '+mc_readonly+' value="' + item.tag_mc_value + '"  /><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" />Tot Mc:<span class="tot_mc" ></span></td>'

							//+'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value='+JSON.stringify(stone_details)+'><input type="hidden" class="stone_price" name="est_tag[stone_price][]" value="'+stone_price+'"></td>'

							+ '<td><input type="text" class="form-control tag_remark" placeholder="Partly Remark" name="est_tag[tag_remark][]" step="any"  readonly style="width:100px;" /></td>'

							+ '<td><div class="cost">' + item.sales_value + '</div><input type="hidden" name="est_tag[total_cgst][]" class="sale_cgst"/><input type="hidden" class="sale_sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="sale_taxable_amount" name="est_tag[sale_taxable_amount][]"/><input type="hidden" class="sale_igst" name="est_tag[total_igst][]"/><input type="hidden" class="act_sales_value" value="' + item.sales_value + '"><input class="sales_value" type="hidden" name="est_tag[cost][]" value="' + item.sales_value + '" /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value="" /><input class="caltype" type="hidden" name="est_tag[caltype][]" value="' + item.calculation_based_on + '" /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value="" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value="" /><input class="tax_group_id" type="hidden" name="est_tag[tax_percentage][]" value="' + item.tax_group_id + '" /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value="" /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value="' + item.tag_mc_type + '" /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="charge_value" name="est_tag[charge_value][]"  value="' + item.charge_value + '"><input type="hidden" class="sales_mode" value=' + item.sales_mode + ' ><input type="hidden" class="purchase_cost" value=' + item.tag_purchase_cost + ' name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_tag[pur_va][]" ><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value=' + JSON.stringify(other_metal_details) + '><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" value="' + tag_other_itm_amount + '"><input type="hidden" class="order_rate" name="est_tag[order_rate][]"  value="' + item.order_rate + '"></td>'

							+ '<td><a href="#" onClick="remove_order_tag($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a><a href="#" onClick="confirm_cancel(' + item.id_orderdetails + ')" class="btn btn-warning"><i class="fa fa-close"></a></td>'

							+ '</tr>';

					}

				});

				$('#estimation_tag_details tbody').append(row);

				$('#estimation_tag_details > tbody').find('.item_emp_id').select2();

				//calculatetag_SaleValue();

				calculateOrderTag();

				setTimeout(function () {
					calc_dia_tot_amt();
				}, 2000);

			} else {

				alert(data.message);

			}

		}

	});

}

function remove_order_tag(curRow) {

	curRow.remove();

	calculateOrderTag();

}

function getCusSearchTags(searchTxt, searchField, curRow, searchtab) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getPartialTagSearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'searchField': searchField, 'id_branch': $("#id_branch").val() },

		success: function (data) {

			cur_search_tags = data;

			$.each(data, function (key, item) {

				$('#estimation_custom_details > tbody tr').each(function (idx, row) {

					if (item != undefined) {

						if ($(this).find('.est_tag_id').val() == item.value) {

							data.splice(key, 1);

						}

					}

				});

			});

			$("." + searchtab).autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						var curRowItem = i.item;

						if (curRowItem.sales_mode == 2) { // 1 - Fixed Rate, 2 - Flexible

							get_metal_rates_by_branch(i.item.current_branch);

						}

						var stone_details = [];

						var stone_price = 0;

						$.each(i.item.stone_details, function (skey, sitem) {

							if (sitem.pieces > 0) {

								stone_price += parseFloat(sitem.amount);

								stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });

							}

						});

						curRow.find('.cus_tag_name').val(i.item.tag_code);

						curRow.find('.cus_old_tag_code').val(i.item.old_tag_id);

						//	curRow.find('.cus_tag_name').val(i.item.label);

						curRow.find('.est_tag_id').val(i.item.value);

						curRow.find('.is_partial').val(1);

						curRow.find('.cus_product').select2("val", curRowItem.product_id);

						curRow.find('.cus_product_id').val(curRowItem.product_id);

						curRow.find('.cus_des_id').val(curRowItem.design_id);

						//curRow.find('.cus_des_id').val(curRowItem.design_id);

						curRow.find('.cus_id_sub_design').val(curRowItem.id_sub_design);

						//curRow.find('.cus_id_sub_design').val(curRowItem.id_sub_design);

						curRow.find('.cus_purity').val(curRowItem.purity);

						curRow.find('.cus_old_tag').val(curRowItem.old_tag_id);

						//curRow.find('.size').val(curRowItem.size);

						curRow.find('.cus_pcs').val(1);

						curRow.find('.cus_gwt').val(curRowItem.gross_wt);

						curRow.find('.blc_tag_gwt').val(curRowItem.gross_wt);

						curRow.find('.cus_lwt').val(curRowItem.less_wt);

						curRow.find('.cus_nwt').val(curRowItem.net_wt);

						curRow.find('.cus_mc').val(curRowItem.tag_mc_value);

						curRow.find('.HB_cus_mc').val(curRowItem.tag_mc_value);

						curRow.find('.cus_mc_type').val(curRowItem.tag_mc_type);

						curRow.find('.id_mc_type').val(curRowItem.tag_mc_type);

						curRow.find('.cus_wastage').val(curRowItem.retail_max_wastage_percent);

						curRow.find('.act_va_per').val(curRowItem.retail_max_wastage_percent);

						curRow.find(".cus_calculation_based_on").val(curRowItem.calculation_based_on);

						curRow.find(".metal_type").val(curRowItem.metal_type);

						curRow.find(".tax_group_id").val(curRowItem.tax_group_id);

						curRow.find(".act_gwt").val(curRowItem.gross_wt);

						curRow.find(".act_mc_value").val(curRowItem.tag_mc_value);

						curRow.find(".id_orderdetails").val(curRowItem.id_orderdetails);

						curRow.find(".cus_rate_field").val(curRowItem.rate_field);

						curRow.find(".stone_price").val(stone_price);

						curRow.find(".stone_details").val(JSON.stringify(stone_details));

						curRow.find(".order_no").html(curRowItem.order_no);

						if (curRowItem.calculation_based_on == 3 || curRowItem.calculation_based_on == 4) {

							curRow.find(".cus_partial").prop("disabled", true);

						} else {

							curRow.find(".cus_partial").prop("disabled", false);

						}

						if (curRowItem.mc_va_limit != undefined) {

							curRow.find(".wastag_min").val(curRowItem.mc_va_limit.wastag_min);

						}

						if (curRowItem.mc_va_limit != undefined) {

							curRow.find(".mc_min").val(curRowItem.mc_va_limit.mc_min);

						}

						get_search_custom_metal_rates(curRow);

						calculateCustomItemSaleValue();

						/*if(validateTagDetailRow()){

							create_new_empty_est_tag_row();

							$('#estimation_tag_details > tbody').find('tr:last td:eq(0) .est_tag_name').focus();

						}*/

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

							curRow.find('.est_tag_name').val("");

							curRow.find('.est_tag_id').val("");

						}

					},

					minLength: 1,

				});

		}

	});

}

$('#ed_cus_mobile').on('keyup', function () {

	if (this.value.length > 10) {

		$('#ed_cus_mobile').val('');

		$('#ed_cus_mobile').focus();

	}

	else {

		$('#ed_cus_mobile').prop('disabled', false);

	}

});

/* $("#update_cutomer").on('click', function () {

	var esti_for = $("input[name='estimation[esti_for]']:checked").val();

	if ($('#ed_cus_first_name').val() == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Firstname..' });

		return false;

	}

	else if ($('#ed_cus_mobile').val() == '' || $('#ed_cus_mobile').val() == null) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Mobile Number..' });

		return false;

	} else if ($('#ed_cus_country').val() == '' || $('#ed_cus_country').val() == null) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Country..' });

		return false;

	}

	else if ($('#ed_cus_state').val() == '' || $('#ed_cus_state').val() == null) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the State..' });

		return false;

	} else if ($('#ed_cus_city').val() == '' || $('#ed_cus_city').val() == null) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the City..' });

		return false;

	}

	else if ($('#ed_cus_address1').val() == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Address..' });

		return false;

	}

	else if ($('#ed_cus_pin_code_add').val() == '' && $('#ed_cus_country').val() == 101) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });

		return false;

	}

	else if ($('#ed_cus_pin_code_add').val() != '' && ($('#ed_cus_pin_code_add').val().length != 6)) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid Pincode..' });

		return false;

	}

	else if ($('#ed_id_village').val() == '' ) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select the Area..' });

		return false;

	}

	else if (esti_for == 3) {

		if ($('#ed_gst_no').val() == '') {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the GST No..' });

			return false;

		} else {

			var reggst = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){3}$');

			if (!reggst.test($('#ed_gst_no').val())) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid GST No..' });

				return false;

			}

		}

	}

	update_cutomer($('#ed_cus_first_name').val(), $('#ed_cus_mobile').val(), $('#id_village').val(), $('#ed_gst_no').val(), $("#ed_cus_image")[0].files[0]);

	$('#cus_first_name').val('');

	$('#cus_mobile').val('');

}); */

/* function update_cutomer(cus_name, cus_mobile, id_village, gst_no, img) { //, cus_address

	var esti_for = $("input[name='estimation[esti_for]']:checked").val();

	var gender = $("input[name='customer[gender]']:checked").val();

	var form_data = new FormData();

	form_data.append('id_customer', $("#cus_id").val());

	form_data.append('cusName', cus_name);

	form_data.append('cusMobile', cus_mobile);

	form_data.append('cusBranch', $('#id_branch').val());

	form_data.append('gst_no', gst_no);

	form_data.append('cus_type', esti_for == 1 ? 1 : 2);

	form_data.append('id_country', $('#ed_cus_country').val());

	form_data.append('id_state', $('#ed_cus_state').val());

	form_data.append('id_city', $('#ed_cus_city').val());

	form_data.append('address1', $('#ed_cus_address1').val());

	form_data.append('address2', $('#ed_cus_address2').val());

	form_data.append('address3', $('#ed_cus_address3').val());

	form_data.append('pincode', $('#ed_cus_pin_code_add').val());

	form_data.append('mail', $('#ed_cus_email').val());

	form_data.append('cust_img', img);

	form_data.append('customer_img', $('#ed_customer_img').val());

	form_data.append('title', $('#ed_title').val());

	form_data.append('id_profession', $('#ed_profession').val());

	form_data.append('gender', gender);

	form_data.append('date_of_birth', $('#ed_date_of_birth').val());

	form_data.append('date_of_wed', $('#ed_date_of_wed').val());

	form_data.append('id_village', $('#ed_sel_village').val());

	form_data.append('pan_no', $('#ed_pan_no').val());

	form_data.append('aadharid', $('#ed_aadharid').val());

	form_data.append('dl_no', $('#ed_dl_no').val());

	form_data.append('pp_no', $('#ed_pp_no').val());

	my_Date = new Date();

	//  data: {'id_customer':$("#cus_id").val(),'cusName': cus_name, 'cusMobile' : cus_mobile, 'cusBranch' : 1,'id_village':id_village,'cus_type':(esti_for==1 ? 1 :2),'gst_no':gst_no,'id_country':$('#ed_cus_country').val(),'id_state':$('#ed_cus_state').val(),'id_city':$('#ed_cus_city').val(),'address1':$('#ed_cus_address1').val(),'address2':$('#ed_cus_address2').val(),'address3':$('#ed_cus_address3').val(),'pincode':$('#ed_cus_pin_code_add').val(),'mail':$('#ed_cus_email').val()},

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/updateCustomer/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: form_data,

		cache: false,

		enctype: 'multipart/form-data',

		contentType: false,

		processData: false,

		//Need to update login branch id here from session

		success: function (data) {

			if (data.success == true) {

				$('#confirm-edit').modal('toggle');

				$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);

				$("#cus_id").val(data.response.id_customer);

				$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>" + data.message });

			} else {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });

			}

		}

	});

} */

$('#edit_customer').on('click', function () {

	if ($('#cus_id').val() != '' && $('#cus_id').val() != undefined) {

		get_customer();

	}

});

function get_customer_25_12_2023() {

	my_Date = new Date();

	$.ajax({

		type: "POST",

		url: base_url + "index.php/admin_ret_estimation/get_customer?nocache=" + my_Date.getUTCSeconds(),

		cache: false,

		dataType: "JSON",

		data: { 'id_customer': $('#cus_id').val() },

		success: function (data) {

			$('#ed_id_village').val(data.id_village);

			$('#ed_cus_first_name').val(data.firstname);

			$('#ed_cus_mobile').val(data.mobile);

			$('#ed_cus_email').val(data.email);

			$('#ed_cus_country').val(data.id_country);

			$('#ed_cus_state').val(data.id_city);

			$('#ed_cus_city').val(data.id_state);

			$('#ed_cus_address1').val(data.address1);

			$('#ed_cus_address2').val(data.address2);

			$('#ed_cus_address3').val(data.address3);

			$('#ed_cus_pin_code_add').val(data.pincode);

			$('#ed_gst_no').val(data.gst_number);

			$('#ed_id_country').val(data.id_country);

			$('#ed_id_state').val(data.id_state);

			$('#ed_id_city').val(data.id_city);

			$("#ed_cus_img_preview").attr("src", data.img_path);

			$('#ed_title').val(data.title);

			$('#ed_pan_no').val(data.pan_no);

			$('#ed_aadharid').val(data.aadharid);

			$('#ed_dl_no').val(data.dl_no);

			$('#ed_pp_no').val(data.pp_no);

			$('#ed_professionval').val(data.id_profession);

			$('#ed_date_of_birth').val(data.date_of_birth);

			$('#ed_date_of_wed').val(data.date_of_wed);

			$('#ed_title').val(data.title);

			if (data.cus_type == 1) {

				$('#ed_cus_type1').attr('checked', true);

				$('.gst').hide();

			} else {

				$('#ed_cus_type2').attr('checked', true);

				$('.gst_no').show();

			}

			get_country();

			// get_village_list();

			get_villages_by_pincode(data.pincode);

			get_profession();

			$('#confirm-edit').modal('show');

		}

	});

}

function get_customer() {

	my_Date = new Date();

	$.ajax({

		type: "POST",

		url: base_url + "index.php/admin_ret_estimation/get_customer?nocache=" + my_Date.getUTCSeconds(),

		cache: false,

		dataType: "JSON",

		data: { 'id_customer': $('#cus_id').val() },

		success: function (data) {

			$('#id_customer').val($('#cus_id').val());

			// $('#id_village').val(data.id_village);

			// $('#sel_village').val(data.id_village);

			$('#cus_first_name').val(data.firstname);

			$('#cus_mobile').val(data.mobile);

			$("#cus_mobile").prop("readonly",true);

			$('#cus_email').val(data.email);

			$('#country').val(data.id_country);

			$('#state').val(data.id_city);

			$('#city').val(data.id_state);

			$('#address1').val(data.address1);

			$('#address2').val(data.address2);

			$('#address3').val(data.address3);

			$('#pin_code_add').val(data.pincode);

			get_villages_by_pincode(data.pincode);

			$('#gst_no').val(data.gst_number);

			$('#id_country').val(data.id_country);

			$('#id_state').val(data.id_state);

			$('#id_city').val(data.id_city);

			$("#cus_img_preview").attr("src", data.img_path);

			$('#title').val(data.title);

			$('#pan_no').val(data.pan_no);

			$('#aadharid').val(data.aadharid);

			$('#dl_no').val(data.dl_no);

			$('#pp_no').val(data.pp_no);

			$('#professionval').val(data.id_profession);

			$('#date_of_birth').val(data.date_of_birth);

			$('#date_of_wed').val(data.date_of_wed);

			$('#title').val(data.title);

			if (data.cus_type == 1) {

				$('#cus_type1').attr('checked', true);

				$('.gst').hide();

			} else {

				$('#cus_type2').attr('checked', true);

				$('.gst_no').show();

			}

			if (data.is_vip == 1) {

				$('#vip1').attr('checked', true);

			} else {

				$('#vip0').attr('checked', true);

			}

			get_country();

			// get_village_list();

			get_profession();

			$('#confirm-add').modal('show');

		}

	});

}


$('#add_new_customer').on('click', function () {
	if ($('#branch_select').val() != '' && $('#branch_select').val() != null) {
		// open_customer_slider() is defined in ret_general.js — shared by all pages
		open_customer_slider();
	} else {
		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select the Branch' });
		setTimeout(function () { $('.toast').fadeOut(); }, 2000);
	}
});

//Customer Purchase and Accounts

function customer_detail_modal(id_customer) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getCustomerDet/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'id_customer': id_customer },

		success: function (data) {

			$('#cus_pop').html("");

			$('#customer-popup').modal('show');

			$('#cus_del_country').val(data.cus_details[0].id_country);

			$('#cus_del_state').val(data.cus_details[0].id_state);

			$('#cus_del_address1').val(data.cus_details[0].address1);

			$('#cus_del_pincode').val(data.cus_details[0].pincode);

			$.each(data.cus_details, function (key, item) {

				var cusRow = "<div class='row'>" +

					"<div class='col-md-12 col-md-offset-1'>" +

					"<div class='row'>" +

					"<div class='col-md-4'>" +

					"<div class='form-group'>" +

					"<label>Customer Name</label><br>"

					+ item.firstname +

					"<br></div>" +

					"<div class='form-group'>" +

					"<label>Rating</label><br>" +

					(item.is_vip == 0 ? "-" : "<span class='label bg-orange'><i class='fa fa-fw fa-star'></i> V I P</span>")

					+ "<br></div>" +

					"<div class='form-group'>" +

					"<label>Total Accounts</label><br><span class='badge bg-green'>"

					+ item.tot_account +

					"</span></div>" +

					"</div>" +

					"<div class='col-md-4'>" +

					"<div class='form-group'>" +

					"<label>Active Accounts</label><br><span class='badge bg-green'>"

					+ item.active_acc +

					"</span><br></div>" +

					"<div class='form-group'>" +

					"<label>Closed Accounts</label><br><span class='badge bg-green'>"

					+ item.closed_count +

					"</span><br></div>" +

					"<div class='form-group'>" +

					"<label>Inactive Accounts</label><br><span class='badge bg-green'>"

					+ item.inactive_acount +

					"</span><br></div>" +

					"</div>" +

					"<div class='col-md-4'>" +

					"<div class='form-group'>" +

					"<label>Gold (Grams)</label><br>"

					+ item.gold_wt +

					"<br></div>" +

					"<div class='form-group'>" +

					"<label>Silver (Grams)</label><br>"

					+ item.silver_wt +

					"<br></div>" +

					"<div class='form-group'>" +

					"<label>MRP (Rs)</label><br>"

					+ item.tot_fixed_rate +

					"<br></div>" +

					"</div>" +

					"</div>" +

					"</div>";

				$("#cus_pop").append(cusRow);

			});

			var tablehead = "<div class='table-responsive'>" +

				"<table id='bill_list' class='table table-bordered table-striped text-center'>" +

				"<thead>" +

				"<tr>" +

				"<th>Bill Date</th>" +

				"<th>Bill No</th>" +

				"<th>Branch</th>" +

				"<th>Gold Wt</th>" +

				"<th>Silver Wt</th>" +

				"<th>MRP Amt</th>" +

				"<th>Bill Amt</th>" +

				"</tr>" +

				"</thead>" +

				"<tbody>";

			$.each(data.bill_details, function (key, item) {

				tablehead +=

					"<tr>" +

					"<td>" + item.bill_date + "</td>" +

					"<td>" + item.bill_no + "</td>" +

					"<td>" + item.branch_name + "</td>" +

					"<td>" + item.gold_wt + "</td>" +

					"<td>" + item.silver_wt + "</td>" +

					"<td>" + item.mrp_amount + "</td>" +

					"<td>" + item.tot_bill_amount + "</td>" +

					"</tr>";

			});

			tablehead += "</tbody></table></div>";

			var credit_bill_details = '';

			var tot_outstanding = '';

			if (data.outstanding.length > 0 || data.receipt_details  > 0 || data.order_advance >0) {

				var credit_bill_details = "<div class='table-responsive'>" +

					"<table id='bill_list' class='table table-bordered table-striped text-center'>" +

					"<thead style='background-color: LightSkyBlue '>" +

					"<tr style='text-align: right'>" +

					"<th>Bill Date</th>" +

					"<th>Bill No</th>" +

					"<th>Bill Amt</th>" +

					"<th>Bill Paid Amount</th>" +

					"<th>Due Amount</th>" +

					"<th>Credit Collection</th>" +

					"<th>Balance</th>" +

					"</tr>" +

					"</thead>";

				"<tbody>";

				var total_pending_amt = 0;

				$.each(data.outstanding, function (key, items) {

					total_pending_amt += (!isNaN(items.bal_amt) && (items.bal_amt)!=null && (items.bal_amt)!=undefined && (items.bal_amt)!='' && (items.bal_amt)!='-'? parseFloat(items.bal_amt): 0);

					var paid_amount =

						credit_bill_details +=

						"<tr>" +

						"<td>" + items.bill_date + "</td>" +

						"<td>" + items.bill_no + "</td>" +

						"<td>" + money_format_india(items.tot_bill_amount) + "</td>" +

						"<td>" + money_format_india(items.tot_amt_received) + "</td>" +

						"<td>" + money_format_india(items.due_amount) + "</td>" +

						"<td>" + money_format_india(items.paid_amount) + "</td>" +

						"<td>" + money_format_india(items.bal_amt) + "</td>" +

						"</tr>";

				});

				var tot_outstanding = "<div class='table-responsive'>" +

					"<table id='bill_list' class='table table-bordered table-striped text-center'>" +

					"<tfoot >" +

					"<tr style='text-align: right'>" +

					"<th style='text-align: left'>Outstanding Amount : " + money_format_india(total_pending_amt) + "</th>" +

					"<th style='text-align: left'>Advance Amount : " + money_format_india(data.receipt_details) + "</th>" +

					"<th style='text-align: left'>Order Advance : " + money_format_india(data.order_advance) + "</th>" +



					"</tr>";

				credit_bill_details += "</tbody></tfoot></table></div>";

			}

			$('#cus_bill_details').html(tablehead + credit_bill_details + tot_outstanding);

		}

	});

}

$('#customer-popup').on('hidden.bs.modal', function () {

	$('#select_tag_details').focus();

})

function set_bill_data(id_cus) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getCustomerBill/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'id_cus': id_cus },

		success: function (data) {

			var tablehead = "<div class='table-responsive'>" +

				"<table id='bill_list' class='table table-bordered table-striped text-center'>" +

				"<thead>" +

				"<tr>" +

				"<th>Bill Date</th>" +

				"<th>Bill No</th>" +

				"<th>Branch</th>" +

				"<th>Gold Wt</th>" +

				"<th>Silver Wt</th>" +

				"<th>MRP Amt</th>" +

				"<th>Bill Amt</th>" +

				"</tr>" +

				"</thead>" +

				"<tbody>";

			$('#cus_pop').append(tablehead);

			$.each(data, function (key, item) {

				var curRow =

					"<tr>" +

					"<td>" + item.bill_date + "</td>" +

					"<td>" + item.bill_no + "</td>" +

					"<td>" + item.branch_name + "</td>" +

					"<td>" + item.gold_wt + "</td>" +

					"<td>" + item.silver_wt + "</td>" +

					"<td>" + item.mrp_amount + "</td>" +

					"<td>" + item.tot_bill_amount + "</td>" +

					"</tr>";

				$('#cus_pop tbody').append(curRow);

			})

			$('#cus_pop').append("</tbody></table></div>");

		}

	});

}

//Customer Purchase and Accounts

//customer charges

function create_new_empty_est_cus_charges_item(curRow, id) {

	if (curRow != undefined) {

		$('#custom_active_id').val(curRow.closest('tr').attr('id'));

	}

	var row = "";

	console.log(id);

	if (id != undefined) {

		var charge_list = "<option value=''>-Select Charge Type-</option>";

		$.each(other_charges_details, function (pkey, pitem) {

			charge_list += "<option value='" + pitem.id_charge + "'>" + pitem.name_charge + "</option>";

		});

		row += '<tr><td><select class="id_charge" name="est_stones_item[id_charge][]">' + charge_list + '</select></td><td><input type="number" class="value_charge" name="est_stones_item[value_charge][]" value="" /></td></tr>';

	}

	else {

		var catRow = $('#custom_active_id').val();

		var row_charges_details_details = $('#' + catRow).find('.charges_details').val();

		console.log(row_charges_details_details);

		if (row_charges_details_details != '') {

			var cus_charges_details = JSON.parse(row_charges_details_details);

			console.log(cus_charges_details);

			$.each(cus_charges_details, function (pkey, pitem) {

				var charge_list = '';

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

			var charge_list = "<option value=''>-Select Charge Type-</option>";

			$.each(other_charges_details, function (pkey, pitem) {

				charge_list += "<option value='" + pitem.id_charge + "'>" + pitem.name_charge + "</option>";

			});

			row += '<tr><td><select class="id_charge" name="est_stones_item[id_charge][]">' + charge_list + '</select></td><td><input type="number" class="value_charge" name="est_stones_item[value_charge][]" value="" /></td><td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

		}

	}

	$('#cus_other_charges_modal .modal-body').find('#estimation_other_charge_cus_item_details tbody').append(row);

	$('#cus_other_charges_modal').modal('show');

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

		create_new_empty_est_cus_charges_item();

	} else {

		alert("Please fill required fields");

	}

});

function validatecusOtherChargeDetailRow() {

	var row_validate = true;

	$('#cus_other_charges_modal .modal-body #estimation_other_charge_cus_item_details> tbody  > tr').each(function (index, tr) {

		if ($(this).find('.id_charge').val() == "" || $(this).find('.value_charge').val() == 0 || $(this).find('.value_charge').val() == '') {

			row_validate = false;

		}

	});

	return row_validate;

}

$('#cus_other_charges_modal  #update_charge_details').on('click', function () {

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

	calculateCustomItemSaleValue(row);

	calculateSaleValue();

	$('#cus_other_charges_modal .modal-body').find('#estimation_other_charge_cus_item_details tbody').empty();

});

//customer charges

function get_invnetory_item() {

	$('#select_item option').remove();

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_other_inventory/get_productMappedDetails',

		dataType: 'json',

		data: { "id_branch": $('#branch_select').val() },

		success: function (data) {

			other_inventory_item = data;

		}

	});

}

$('#add_new_inv').on('click', function () {

	if (validateEstOtherInvDetailRow()) {

		if ($('#estimation_tag_details > tbody >tr').length > 0) {

			create_new_empty_est_other_inv_row();

		}

		else {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select The Tag Details..' });

		}

	} else {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Fill The Required Fields..' });

	}

});

function validateEstOtherInvDetailRow() {

	var row_validate = true;

	$('#estimation_other_inv_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('.id_other_item').val() == "" || $(this).find('.no_of_pcs').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}

function create_new_empty_est_other_inv_row() {

	if (other_inventory_item.length > 0) {

		var item = "<option value=''>-Select Item</option>";

		$('#estimation_tag_details > tbody tr').each(function (idx, row) {

			curRow = $(this);

			$.each(other_inventory_item, function (key, val) {

				if (val.pro_id == curRow.find('.pro_id').val()) {

					$.each(val.item_details, function (k, items) {

						var filtered = item_list.filter(item => item.id_other_item == items.id_other_item);

						if (filtered.length == 0) {

							item_list.push({ 'id_other_item': items.id_other_item, 'item_name': items.item_name, 'tot_pcs': items.tot_pcs, 'item_image': items.item_image, 'sku_id': items.sku_id });

						}

					});

				}

			});

		});

		$.each(item_list, function (i, val) {

			item += "<option value='" + val.id_other_item + "'>" + val.item_name + "</option>";

		});

		var trHtml = '';

		var img_src = base_url + 'assets/img/no_image.png';

		trHtml += '<tr>'

			+ '<td><select class="form-control id_other_item" name="est_oth_inv[id_other_item][]" value="">' + item + '</select></td>'

			+ '<td><input type="number" name="est_oth_inv[no_of_pcs][]" class="form-control no_of_pcs"></td>'

			+ '<td><img class="img_src" src="' + img_src + '" width="30" height="30"></td>'

			+ '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

			+ '</tr>';

		if ($('#estimation_other_inv_details > tbody  > tr').length > 0) {

			$('#estimation_other_inv_details > tbody > tr:first').before(trHtml);

		} else {

			$('#estimation_other_inv_details tbody').append(trHtml);

		}

		$('#estimation_old_matel_details > tbody').find('.id_other_item').select2();

	}

	else {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Records Found..' });

	}

}

$(document).on('keyup', ".no_of_pcs", function (e) {

	var row = $(this).closest('tr');

	var id_other_item = row.find('.id_other_item').val();

	var no_of_pcs = row.find('.no_of_pcs').val();

	$.each(item_list, function (key, items) {

		if (items.id_other_item == id_other_item) {

			var available_pcs = items.tot_pcs;

			if (parseFloat(available_pcs) < no_of_pcs) {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Available Pieces is " + items.tot_pcs });

				row.find('.no_of_pcs').val(0);

			}

		}

	});

});

$(document).on('change', ".id_other_item", function (e) {

	var row = $(this).closest('tr');

	var id_other_item = row.find('.id_other_item').val();

	$.each(item_list, function (key, items) {

		if (items.id_other_item == id_other_item && (items.item_image != '')) {

			var img_src = base_url + 'assets/img/other_inventory/' + items.sku_id + '/' + items.item_image;

			row.find('.img_src').attr('src', img_src);

		}

	});

});

$(document).on('change', ".wastage_max_per", function (e) {
	var currentRow = $(this).closest('tr');
	let wastage = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val());
	var tagId = currentRow.find('.est_tag_id').val();

	let act_tag_wast = isNaN(parseFloat($(this).closest('tr').find(".wastage").html())) ? 0 : parseFloat($(this).closest('tr').find(".wastage").html());

	let wastage_min = isNaN(parseFloat($(this).closest('tr').find(".va_min_limit").val())) ? 0 : parseFloat($(this).closest('tr').find(".va_min_limit").val());

	var allow_mc_va_edit = false;

	$.each(chit_details, function (key, items) {

		if (items.scheme_type != 0) {

			allow_mc_va_edit = true;

		}

	});

	if(wastage>100){

	    $(this).val(act_tag_wast);

	    $(this).focus();

	    $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastage Should be below 100% ' });

	}

	if (wastage < wastage_min && !allow_mc_va_edit) {

		$(this).val(act_tag_wast);

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Minimum Wastage Percentage is ' + wastage_min + ' %' });

	}

	let id_orderdetails = isNaN(parseFloat($(this).closest('tr').find('.id_orderdetails').val())) ? '' : parseFloat($(this).closest('tr').find('.id_orderdetails').val());
	var tagDetail = tag_split_details.find(function(detail) {
			return detail.tag_id === tagId;
		});
		if (tagDetail) {
			
			var newWastageMaxPer = parseFloat($(this).val()); 
			tagDetail.retail_max_wastage_percent = newWastageMaxPer;
			
			var retailMaxWastagePercent = tagDetail.retail_max_wastage_percent;
	
			console.log('Retail Max Wastage Percent:', retailMaxWastagePercent);
		} else {
			console.log('Tag not found in tag_split_details.');
		}
	console.log(id_orderdetails);

	if (id_orderdetails != '') {

		calculateOrderTag();

	}

	else {

		calculatetag_SaleValue();

		if ($('#estimation_chit_details > tbody >tr').length > 0) {

			calculate_chit_closing_balance();

		}

	}

});

$(document).on('change', '.est_wastage_wt', function (e) {

	let wastage_wt = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val());

	let act_wastage_wt = isNaN(parseFloat($(this).closest('tr').find(".act_wast_wt").html())) ? 0 : parseFloat($(this).closest('tr').find(".act_wast_wt").html());

	let wastage_wt_min = isNaN(parseFloat($(this).closest('tr').find(".va_wt_min_limit").val())) ? 0 : parseFloat($(this).closest('tr').find(".va_wt_min_limit").val());

	var allow_mc_va_edit = false;

	$.each(chit_details, function (key, items) {

		if (items.scheme_type != 0) {

			allow_mc_va_edit = true;

		}

	});

	console.log(wastage_wt);

	console.log(wastage_wt_min);

	console.log((wastage_wt < wastage_wt_min && !allow_mc_va_edit));

	if (wastage_wt < wastage_wt_min && !allow_mc_va_edit) {

		$(this).val(act_wastage_wt);

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Minimum Wastage Weigth is ' + wastage_wt_min + '' });

	}

	else {

		let wast_per = parseFloat(parseFloat(wastage_wt * 100) / parseFloat($(this).closest('tr').find('.tot_nwt').val())).toFixed(2);

		$(this).closest('tr').find('.wastage_max_per').val(wast_per);

	}

	let id_orderdetails = isNaN(parseFloat($(this).closest('tr').find('.id_orderdetails').val())) ? '' : parseFloat($(this).closest('tr').find('.id_orderdetails').val());

	console.log(id_orderdetails);

	if (id_orderdetails != '') {

		calculateOrderTag();

	}

	else {

		calculatetag_SaleValue();

		if ($('#estimation_chit_details > tbody >tr').length > 0) {

			calculate_chit_closing_balance();

		}

	}

});

$(document).on('change', '.est_mc_type', function (e) {

	let id_orderdetails = isNaN(parseFloat($(this).closest('tr').find('.id_orderdetails').val())) ? '' : parseFloat($(this).closest('tr').find('.id_orderdetails').val());

	console.log(id_orderdetails);

	$(this).closest('tr').find('.id_mc_type').val(this.value);

	let curRow = $(this).closest("tr");

	var cur_mc_type =this.value;

	if (curRow.find('#child_tag_details').val() != '' && curRow.find('#child_tag_details').val() != undefined) {

		console.log(curRow.find('#child_tag_details').val());

		var child_tag_details = JSON.parse(curRow.find('#child_tag_details').val())

		$.each(child_tag_details, function (idx, obj) {

			var child_tag_id = obj.tag_id;

			$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

				if ($(this).find('.est_tag_id').val() == child_tag_id) {

					$(this).find('.est_mc_type').val((cur_mc_type != '' ? cur_mc_type : ''));

					$(this).find('.id_mc_type').val((cur_mc_type != '' ? cur_mc_type : ''));

				}

			});

		});

	}

	if (id_orderdetails != '') {

		calculateOrderTag();

	}

	else {

		calculatetag_SaleValue();

	}

});

$(document).on('keyup', ".cus_sub_design", function (e) {

	var row = $(this).closest('tr');

	var sub_design = row.find(".cus_sub_design").val();

	getSearchCusSubDesign(sub_design, row);

});

function getSearchCusSubDesign(searchTxt, curRow) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getProductSubDesignBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': searchTxt, 'ProCode': curRow.find('.cus_product_id').val(), 'id_branch': $('#id_branch').val(), 'is_non_tag': (curRow.find('.is_non_tag').val() == undefined ? 0 : curRow.find('.is_non_tag').val()) },

		success: function (data) {

			$(".cus_sub_design").autocomplete(

				{

					source: data,

					select: function (e, i) {

						e.preventDefault();

						curRow.find('.cus_sub_design').val(i.item.label);

						curRow.find('.cus_id_sub_design').val(i.item.value);

					},

					change: function (event, ui) {

						if (ui.item === null) {

						} else {

						}

					},

					response: function (e, i) {

						// ui.content is the array that's about to be sent to the response callback.

						if (searchTxt != "") {

							if (i.content.length !== 0) {

							}

						} else {

							curRow.find('.cus_sub_design').val("");

							curRow.find('.cus_id_sub_design').val("");

						}

					},

					minLength: 1,

				});

		}

	});

}

$("input[name$='cus[cus_type]']").click(function () {

	var test = $(this).val();

	if (test == 1) {

		$(".gst").hide();

	}

	else {

		$(".gst").show();

	}

});

$("#manual_rate").change(function () {

	if (this.checked) {

		$(".market_rate_value").prop("readonly", false);

		$(".cus_market_rate_value").prop("readonly", false);

		$(".cat_market_rate_value").prop("readonly", false);

	} else {

		$(".market_rate_value").prop("readonly", true);

		$(".cus_market_rate_value").prop("readonly", true);

		$(".cat_market_rate_value").prop("readonly", true);

	}

});

function update_po_details(product_id, design_id, subdesign_id) {

	$.ajax({

		url: base_url + 'index.php/admin_ret_tagging/get_po_details/?nocache=' + my_Date.getUTCSeconds(),

		type: 'POST',

		dataType: "json",

		data: { 'product_id': product_id, 'design_id': design_id, 'subdesign_id': subdesign_id, 'lot_no': $('#tag_lot_received_id').val(), 'lot_from': $('#tag_lot_received_id option:selected').attr('data-lotfrom') },

		success: function (data) {

			current_po_details = data;

		},

		error: function (error) {

			$("div.overlay").css("display", "none");

			console.log("Error on : ", error);

		},

	});

}

function get_wastage_settings_details() {

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_tagging/get_wastage_settings_details',

		dataType: 'json',

		success: function (data) {

			wast_settings_details = data;

		}

	});

}

function get_mc_va_limit(product_id, design_id, subdesign_id, gwt, tag_type = 1, nwt) {

	let mc_min = 0;

	let va_min = 0;

	let va_max = 0;

	let va_min_wt = 0;

	let va_method = 0;

	let margin_mrp = 0;

	let mc_cal_type = 0;

	$.each(wast_settings_details, function (key, items) {

		if ((items.id_product == product_id) && (items.id_design == design_id) && (tag_type == 1 ? (items.id_sub_design == subdesign_id) : true)) {

			margin_mrp = isNaN(items.margin_mrp) || items.margin_mrp == '' || items.margin_mrp == null ? 0 : items.margin_mrp;

			mc_cal_type = isNaN(items.mc_cal_type) || items.mc_cal_type == '' || items.mc_cal_type == null ? 0 : items.mc_cal_type;

			if (items.wastage_type == 1) { //Fixed

				mc_min = isNaN(items.mc_min) || items.mc_min == '' || items.mc_min == null ? 0 : items.mc_min;

				va_min = isNaN(items.wastag_min) || items.wastag_min == '' || items.wastag_min == null ? 0 : items.wastag_min;

				va_max = isNaN(items.wastage_max) || items.wastage_max == '' || items.wastage_max == null ? 0 : items.wastage_max;

				va_min_wt = isNaN(items.wastag_min_wt) || items.wastag_min_wt == '' || items.wastag_min_wt == null ? 0 : items.wastag_min_wt;

				va_method = isNaN(items.wastag_method) || items.wastag_method == '' || items.wastag_method == null ? 0 : items.wastag_method;

			}

			else if (items.wastage_type == 2) { //Flexiable

				$.each(items.weight_range_det, function (i, result) {

					if ((parseFloat(result.wc_from_weight) <= parseFloat(gwt)) && (parseFloat(gwt) <= parseFloat(result.wc_to_weight))) {

						mc_min = isNaN(result.mcrg_min) || result.mcrg_min == '' || result.mcrg_min == null ? 0 : result.mcrg_min;

						va_min = isNaN(result.wc_min) || result.wc_min == '' || result.wc_min == null ? 0 : result.wc_min;

						va_max = isNaN(result.wc_max) || result.wc_max == '' || result.wc_max == null ? 0 : result.wc_max;

						va_min_wt = isNaN(result.wc_min_wt) || result.wc_min_wt == '' || result.wc_min_wt == null ? 0 : result.wc_min_wt;

						va_method = isNaN(result.wc_method) || result.wc_method == '' || result.wc_method == null ? 0 : result.wc_method;

					}

				});

			}

			return false;

		}

	});

	if (va_method == 2) {

		let wast_perc  = parseFloat(nwt) > 0 ? parseFloat(parseFloat(va_min_wt * 100) / parseFloat(nwt)).toFixed(2) : 0;

		va_min = wast_perc;

	}

	let return_data = {

		"mc_min": mc_min,

		"va_min": va_min,

		"va_max": va_max,

		"margin_mrp": margin_mrp,

		"mc_cal_type": mc_cal_type

	}

	return return_data;

}

function calculate_total_mc_va(rate_per_grm, calculation_type, gross_wt, net_wt, tot_wastage, id_mc_type, mc, pieces) {

	let total_mc_va = 0

	pieces = isNaN(pieces) || pieces == '' || pieces == 0 ? 1 : parseFloat(pieces);



	if (calculation_type == 0) {

		let wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100)).toFixed(3);

		if (id_mc_type != 3) {

			mc = parseFloat(id_mc_type) == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * pieces);

			total_mc_va = parseFloat(parseFloat(rate_per_grm * parseFloat(wast_wgt)) + parseFloat(mc));

		} else {

			total_mc_va = parseFloat(parseFloat(rate_per_grm * parseFloat(wast_wgt)) + parseFloat(((parseFloat(rate_per_grm * parseFloat(wast_wgt))) * (mc / 100))));

		}

	}

	else if (calculation_type == 1) {

		let wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(3);

		if (id_mc_type != 3) {

			mc = parseFloat(id_mc_type) == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * pieces);

			total_mc_va = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt))) + parseFloat(mc));

		} else {

			total_mc_va = parseFloat(parseFloat(rate_per_grm * (parseFloat(wast_wgt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt)))) * (mc / 100))));

		}

	}

	else if (calculation_type == 2) {

		let wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100)).toFixed(3);

		if (id_mc_type != 3) {

			mc = parseFloat(id_mc_type) == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * pieces);

			total_mc_va = parseFloat(parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt))) + parseFloat(mc))).toFixed(2);

		} else {

			total_mc_va = parseFloat((parseFloat(rate_per_grm) * (parseFloat(wast_wgt))) + parseFloat(((parseFloat(rate_per_grm * (parseFloat(wast_wgt) ))) * (mc / 100))));

		}

	}

	return parseFloat(total_mc_va).toFixed(2);

}

function check_is_weight_scheme() {

	let IsWeightScheme = false;

	if ($('#estimation_chit_details > tbody').length > 0) {

		$('#estimation_chit_details > tbody > tr').each(function (idx, row) {

			if (parseFloat($(this).find('.scheme_type').val()) > 0) {

				IsWeightScheme = true;

				return false;

			}

		});

	}

	return IsWeightScheme;

}

$(document).on("change", ".market_rate_value", function () {

	let curRow = $(this).closest("tr");

	let rate = isNaN($(this).val()) || $.trim($(this).val()) == "" ? 0 : parseFloat($(this).val());

	let rate_field = curRow.find(".rate_field").val();

	let metal_type = curRow.find(".metal_type").val();

	let cal_type = curRow.find(".caltype").val();

	let id_orderdetails = curRow.find('.id_orderdetails').val();

	let is_order_rate = $('input[name="order_rate_type"]:checked').val();

	let stone_type = curRow.find('.stone_type').val();

	if(stone_type == 0) // for Ornaments Product

	{

		var  returnStatus = check_rate_is_valid(rate, rate_field, metal_type, cal_type);

	}

	else

	{

		var  returnStatus = check_min_max_rate(curRow);

	}

	if (id_orderdetails > 0 && is_order_rate == 2 ? false : (returnStatus.status == false)) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + returnStatus.message, settings: { 'timeout': 10000 } });

		curRow.find(".market_rate_value").val(returnStatus.rate_per_grm);

		if(stone_type!=0)

		{

			curRow.find(".act_sales_value").val(returnStatus.rate_per_grm);

		}

		curRow.find(".market_rate_value").focus();

	}

	else

	{

		if(stone_type!=0)

		{

			curRow.find(".act_sales_value").val(curRow.find(".market_rate_value").val());

		}

	}

	calculatetag_SaleValue();

	if(id_orderdetails > 0){

	calculateOrderTag();

	}

});

$(document).on("change", ".cus_market_rate_value", function () {

	let curRow = $(this).closest("tr");

	let rate = isNaN($(this).val()) || $.trim($(this).val()) == "" ? 0 : parseFloat($(this).val());

	let rate_field = curRow.find(".cus_rate_field").val();

	let metal_type = curRow.find(".metal_type").val();

	let cal_type = curRow.find(".cus_calculation_based_on").val();

	let returnStatus = check_rate_is_valid(rate, rate_field, metal_type, cal_type);

	if (returnStatus.status == false) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + returnStatus.message, settings: { 'timeout': 10000 } });

		curRow.find(".cus_market_rate_value").val(returnStatus.rate_per_grm);

		curRow.find(".cus_market_rate_value").focus();

		calculateCustomItemSaleValue();

	}

});

$(document).on("change", ".cat_market_rate_value", function () {

	let curRow = $(this).closest("tr");

	let rate = isNaN($(this).val()) || $.trim($(this).val()) == "" ? 0 : parseFloat($(this).val());

	let rate_field = curRow.find(".cat_rate_field").val();

	let metal_type = curRow.find(".metal_type").val();

	let cal_type = curRow.find(".cat_calculation_based_on").val();

	let returnStatus = check_rate_is_valid(rate, rate_field, metal_type, cal_type);

	if (returnStatus.status == false) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + returnStatus.message, settings: { 'timeout': 10000 } });

		curRow.find(".cat_market_rate_value").val(returnStatus.rate_per_grm);

		curRow.find(".cat_market_rate_value").focus();

		calculateSaleValue();

	}

});

function check_rate_is_valid(rate, rate_field, metal_type, cal_type) {

	let status = true;

	let message = "";

	let rate_per_grm = 0;

	let min_tol = 0;

	let max_tol = 0;

	if (cal_type != 3 && cal_type != 4) {

		if (rate_field != '') {

			rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : parseFloat($('.' + rate_field).html());

		}

		if (metal_type == $("#gold_metal_id").val()) {

			min_tol = $("#min_gold_tol").val();

			max_tol = $("#max_gold_tol").val();

		} else if (metal_type == $("#silver_metal_id").val()) {

			min_tol = $("#min_silver_tol").val();

			max_tol = $("#max_silver_tol").val();

		}

		let max_tol_value = (parseFloat(rate_per_grm) + (parseFloat(rate_per_grm) * parseFloat(max_tol) / 100)).toFixed(2);

		let min_tol_value = (parseFloat(rate_per_grm) - (parseFloat(rate_per_grm) * parseFloat(min_tol) / 100)).toFixed(2);

		if (rate < min_tol_value) {

			status = false;

			message = "Invalid Rate! Cannot be less than " + min_tol_value;

		} else if (rate > max_tol_value) {

			status = false;

			message = "Invalid Rate! Cannot be more than " + max_tol_value;

		}

	}

	return { status, message, rate_per_grm };

}

/*$(document).on('keyup', '.market_rate_value', function (e) {

	calculatetag_SaleValue();

	calculateOrderTag();

});*/

$('#cus_image,#ed_cus_image').on('change', function () {

	validateImage(this);

});

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

$('#ed_snap_shots').on('click', function () {

	Webcam.snap(function (data_uri) {

		$(".ed_image-tag").val(data_uri);

		img_resource.push({ 'src': data_uri, 'name': (Math.floor(100000 + Math.random() * 900000)) + 'jpg' });

		pre_img_files.push(data_uri);

		alert("Your Webcam Images Take Snap Shot Successfullys.");

		console.log(data_uri);

		var preview = $('#ed_cus_img_preview');

		preview.prop('src', data_uri);

	});

	$('#ed_customer_img').val(JSON.stringify(img_resource));

});

function validateImage() {

	if (arguments[0].id == 'cus_image') {

		var preview = $('#cus_img_preview');

	}

	else if (arguments[0].id == 'ed_cus_image') {

		var preview = $('#ed_cus_img_preview');

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

		} else {

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

function get_ActiveProduct() {

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_ActiveProduct',

		data: ({ 'id_category': '' }),

		dataType: "JSON",

		type: "POST",

		success: function (data) {

			prod_details = data;

		}

	});

}

$("input[name='order_rate_type']:radio").change(function () {

	let order_type = $(this).val();

	$('#estimation_tag_details > tbody tr').each(function (idx, row) {

		let rate_per_gram = 0;

		let curRow = $(this);

		let orderno = curRow.find('.orderno').val();

		if ($.trim(orderno) != "") {

			if (order_type == 1) {

				let rate_field = curRow.find('.rate_field').val();

				if (rate_field != '') {

					rate_per_gram = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

				}

				curRow.find('.market_rate_value').val(rate_per_gram);

			} else if (order_type == 2) {

				rate_per_gram = curRow.find('.order_rate').val();

				curRow.find('.market_rate_value').val(rate_per_gram);

			}

		}

	});

	calculateOrderTag();

});

function confirm_cancel(id_orderdetails) {

	$('#id_orderdetails').val(id_orderdetails);

	$('#confirm-ordercancell').modal('show');

}

$('#od_cancel_remark').on('keypress', function () {

	if (this.value.length > 6) {

		$('#od_cancell_delete').prop('disabled', false);

	} else {

		$('#od_cancell_delete').prop('disabled', true);

	}

});

$('#od_cancell_delete').on('click', function () {

	my_Date = new Date();

	$.ajax({

		type: 'POST',

		url: base_url + "index.php/admin_ret_estimation/cancel_order_tag/ajax?nocache=" + my_Date.getUTCSeconds(),

		dataType: 'json',

		data: { 'remarks': $('#od_cancel_remark').val(), 'id_order': $('#id_orderdetails').val() },

		success: function (data) {

			$('#confirm-ordercancell').modal('hide');

			remove_order_tag(curRow);

		}

	});

});

/* function get_profession() {

	$('.overlay').css('display', 'block');

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_settings/get_profession',

		dataType: 'json',

		success: function (data) {

			$.each(data, function (key, data) {

				$('#profession').append(

					$("<option></option>")

						.attr("value", data.id_profession)

						.text(data.name)

				);

				$('#ed_profession').append(

					$("<option></option>")

						.attr("value", data.id_profession)

						.text(data.name)

				);

			});

			if ($("#profession").length > 0) {

				$("#profession").select2("val", ($('#professionval').val() != null ? $('#professionval').val() : ''));

			}

			if ($("#ed_profession").length > 0) {

				$("#ed_profession").select2("val", ($('#ed_professionval').val() != '' ? $('#ed_professionval').val() : ''));

			}

			$('.overlay').css('display', 'none');

		},

		error: function (error) {

			$("div.overlay").css("display", "none");

		}

	});

}

$('#profession').on('change', function () {

	if (this.value != '') {

		$('#professionval').val(this.value);

	}

	else {

		$('#professionval').val('');

	}

});

$('#ed_id_profession').on('change', function () {

	if (this.value != '') {

		$('#ed_professionval').val(this.value);

	}

	else {

		$('#ed_professionval').val('');

	}

}); */

/*Home Bill product,design,subdesign Dropdown*/

function getCusproducts() {

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_catalog/ret_product/active_list/?nocache=" + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		success: function (data) {

			console.log(data);

			cus_product_details = data;

		}

	});

}

$(document).on('change', '.cus_product', function () {

	var row = $(this).closest('tr');

	if (this.value != '') {

		row.find('.cus_product_id').val(this.value);

		getProductDetails(row, this.value);

		get_Activedesign(row, this.value);

	}

	else {

		row.find('.tax_percentage').val('');

		row.find('.tgi_calculation').val('');

		//row.find('.cus_pcs').val('');

		row.find('.cus_calculation_based_on').val('');

		row.find('.metal_type').val('');

		row.find('.tax_group_id').val('');

		row.find('.cus_sales_mode').val('');

		row.find('.scheme_closure_benefit').val('');

	}

});

function getProductDetails(curRow, pro_id) {

	console.log(pro_id);

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getCustomProductBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': '', 'pro_id': pro_id },

		success: function (data) {

			console.log(data);
			
			getestPurityDetails_currow(data[0].metal_type, data[0].cat_id, curRow);

			curRow.find('.tax_percentage').val(data[0].tax_percentage);

			curRow.find('.tgi_calculation').val(data[0].tgi_calculation);

			curRow.find('.cus_pcs').val(data[0].no_of_pieces);

			curRow.find('.cus_calculation_based_on').val(data[0].calculation_based_on);

			curRow.find('.metal_type').val(data[0].metal_type);

			curRow.find('.cat_id').val(data[0].cat_id);

			curRow.find('.tax_group_id').val(data[0].tax_group_id);

			curRow.find('.cus_sales_mode').val(data[0].sales_mode);

			curRow.find('.scheme_closure_benefit').val(data[0].scheme_closure_benefit);

		// 	if(curRow.find('.cus_purity').val() != ''){

		// 		get_search_custom_metal_rates(curRow);

		//    }

			if (data[0].sales_mode == 1) {

				if (data[0].calculation_based_on == 4) {

					curRow.find('.cus_gwt').attr("readonly", false);

					curRow.find('.cus_market_rate_value').attr("readonly", false);

				}

				else {

					curRow.find('.cus_gwt').attr("readonly", true);

					curRow.find('.cus_lwt').attr("readonly", true);

					curRow.find('.cus_mc').attr("readonly", true);

					curRow.find('.cus_wastage').attr("readonly", true);

					curRow.find('.cus_wastage_per').attr("readonly", true);

					curRow.find('.cus_wastage_wt').attr("readonly", true);

					curRow.find('.cus_taxable_amt').attr("readonly", false);

				}

			} else {

				curRow.find('.cus_gwt').attr("readonly", false);

				curRow.find('.cus_lwt').attr("readonly", false);

				curRow.find('.cus_mc').attr("readonly", false);

				curRow.find('.cus_wastage').attr("readonly", false);

				curRow.find('.cus_wastage_per').attr("readonly", false);

				curRow.find('.cus_wastage_wt').attr("readonly", false);

				curRow.find('.cus_taxable_amt').attr("readonly", true);

			}

				if(curRow.find('.cus_purity').val() != '' && curRow.find('.metal_type').val() !=  ''){

				get_search_custom_metal_rates(curRow);

		   }

		}

	});

}

function get_Activedesign(curRow, pro_id) {

	curRow.find('.cus_design option').remove();

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_active_design_products',

		dataType: "json",

		method: "POST",

		data: { 'id_product': pro_id },

		success: function (data) {

			var design_id = curRow.find('.cus_des_id').val()

			$.each(data, function (key, item) {

				$(curRow.find('.cus_design')).append(

					$("<option></option>")

						.attr("value", item.design_no)

						.text(item.design_name)

				);

			});

			$(curRow.find('.cus_design')).select2(

				{

					placeholder: "Select Design",

					allowClear: true

				});

			curRow.find('.cus_design').select2("val", (design_id != '' && design_id > 0 ? design_id : ""));

		}

	});

}

$(document).on('change', '.cus_design', function () {

	console.log((this.value != ''));

	if (this.value != '') {

		var row = $(this).closest('tr');

		row.find('.cus_des_id').val(this.value);

		get_ActiveSubDesigns(row, this.value);

	}

})

function get_ActiveSubDesigns(curRow, des_id) {

	curRow.find('.cus_sub_design option').remove();

	var pro_id = curRow.find('.cus_product_id').val();

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_ActiveSubDesigns',

		dataType: "json",

		method: "POST",

		data: { 'id_product': pro_id, 'design_no': des_id },

		success: function (data) {

			var sub_design_id = curRow.find('.cus_id_sub_design').val()

			$.each(data, function (key, item) {

				$(curRow.find('.cus_sub_design')).append(

					$("<option></option>")

						.attr("value", item.id_sub_design)

						.text(item.sub_design_name)

				);

			});

			$(curRow.find('.cus_sub_design')).select2(

				{

					placeholder: "Select Sub Design",

					allowClear: true

				});

			curRow.find('.cus_sub_design').select2("val", (sub_design_id != '' && sub_design_id > 0 ? sub_design_id : ""));

		}

	});

}

$(document).on('change', '.cus_sub_design', function () {

	console.log((this.value != ''));

	if (this.value) {

		var row = $(this).closest('tr');

		row.find('.cus_id_sub_design').val(this.value);

	}

});

/*Home Bill product,design,subdesign Dropdown*/

/*Non-Tag Product , Design Dropdown*/

function get_Activesections() {

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_sectionBranchwise',

		data: ({ 'id_section': '' }),

		dataType: "JSON",

		type: "POST",

		success: function (data) {

			section_details = data;

		}

	});

}

function getNonTagproducts() {

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/getNonTagproducts/?nocache=" + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		success: function (data) {

			console.log(data);

			cat_product_details = data;

		}

	});

}

$(document).on('change', '.cat_section ', function () {

	var row = $(this).closest('tr');

	getNonTagDesignDetails(row);

});

$(document).on('change', '.cat_product', function () {

	var row = $(this).closest('tr');

	row.find('.cat_design').select2("val", '');

	row.find('.cat_sub_design').select2("val", '');

	row.find('.cat_design option').remove();

	row.find('.cat_sub_design option').remove();

	if (this.value != '') {

		row.find('.cat_pro_id').val(this.value);

		getNonTagProductDetails(row, this.value);

		getNontagDesigns(row, this.value);

	}

	getNonTagDesignDetails(row);

});

function getNonTagProductDetails(curRow, pro_id) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/getProductBySearch/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'searchTxt': '', 'is_non_tag': curRow.find('.is_non_tag').val(), 'id_branch': $('#id_branch').val(), 'pro_id': pro_id },

		success: function (data) {

			if (!(data.length == 0)) {

				curRow.find('.tax_percentage').val(data[0].tax_percentage);

				curRow.find('.tgi_calculation').val(data[0].tgi_calculation);

				curRow.find('.tax_group_id').val(data[0].tax_group_id);

				curRow.find('.cat_pcs').val(data[0].no_of_pieces);

				curRow.find('.cat_calculation_based_on').val(data[0].calculation_based_on);

				curRow.find('.metal_type').val(data[0].id_metal);

				curRow.find('.cat_design').val("");

				curRow.find('.cat_des_id').val("");

				curRow.find('.cus_id_sub_design').val("");

				curRow.find('.cat_sales_mode').val(data[0].sales_mode);

				curRow.find('.scheme_closure_benefit').val(data[0].scheme_closure_benefit);

				getestPurityDetailsCurrow(data[0].id_metal, data[0].cat_id, curRow);

				// if(curRow.find('.cat_purity').val() != ''){

				// 	get_search_catalog_metal_rates(curRow);

			 	//   }

				if (data[0].sales_mode == 1 && data[0].calculation_based_on == 3) {

					curRow.find('.cat_gwt').attr("readonly", true);

					curRow.find('.cat_lwt').attr("readonly", true);

					curRow.find('.cat_mc').attr("readonly", true);

					curRow.find('.cat_wastage').attr("readonly", true);

					curRow.find('.cat_wastage_per').attr("readonly", true);

					curRow.find('.cat_wastage_wt').attr("readonly", true);

					curRow.find('.cat_taxable_amt').attr("readonly", false);

				} else {

					curRow.find('.cat_gwt').attr("readonly", false);

					curRow.find('.cat_lwt').attr("readonly", false);

					curRow.find('.cat_mc').attr("readonly", false);

					curRow.find('.cat_wastage').attr("readonly", false);

					curRow.find('.cat_wastage_per').attr("readonly", false);

					curRow.find('.cat_wastage_wt').attr("readonly", false);

					curRow.find('.cat_taxable_amt').attr("readonly", true);

				}

			} else {

				if (pro_id > 0) {

					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Non Tag lot found for this product in this branch' });

				}

			}

			if(curRow.find('.cat_purity').val() != '' && curRow.find('.metal_type').val() !=  ''){

				get_search_catalog_metal_rates(curRow);

		   }
			

			// var curr_used_gross = 0;

			// var curr_used_pcs = 0;

			// if(curRow.find('.is_non_tag').val()==1)

			// {

			// 	$('#estimation_catalog_details> tbody  > tr').each(function(index, tr) {

			// 		if($(this).find('.cat_des_id').val()==data[0].value && ($(this).find('.is_non_tag').val()==1))

			// 		{

			// 			curr_used_gross+=parseFloat(($(this).find('.cat_gwt').val()=='' ?0 :$(this).find('.cat_gwt').val()));

			// 			curr_used_pcs+=parseFloat(($(this).find('.cat_pcs').val()=='' ?0 :$(this).find('.cat_pcs').val()));

			// 		}

			// 	});

			// 		curRow.find('.tot_blc_pcs').val(data[0].no_of_piece-curr_used_pcs);

			// 		curRow.find('.tot_blc_gwt').val(data[0].gross_wt-curr_used_gross);

			// 		curRow.find('.blc_pcs').html(data[0].no_of_piece-curr_used_pcs);

			// 		curRow.find('.blc_gwt').html(data[0].gross_wt-curr_used_gross);

			// }

		}

	});

}

function getNonTagDesignDetails(curRow) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_non_tag_stock/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'id_branch': $('#id_branch').val(), 'id_section': curRow.find('.cat_section').val(), 'id_product': curRow.find('.cat_product').val(), 'id_design': curRow.find('.cat_design').val(), 'id_sub_design': curRow.find('.cat_sub_design').val() },

		success: function (data) {

			//alert(data);

			console.log(data);

			var curr_used_gross = 0;

			var curr_used_pcs = 0;

			if (curRow.find('.is_non_tag').val() == 1) {

				

				



					$('#estimation_catalog_details> tbody  > tr').each(function (index, tr) {



	

						if (curRow.find('.cat_pro_id').val() == $(this).find('.cat_pro_id').val() && curRow.find('.cat_des_id').val() == $(this).find('.cat_des_id').val() && curRow.find('.cat_id_sub_design').val() == $(this).find('.cat_id_sub_design').val()) {



							curr_used_gross += parseFloat(($(this).find('.cat_gwt').val() == '' ? 0 : $(this).find('.cat_gwt').val()));

							curr_used_pcs += parseFloat(($(this).find('.cat_pcs').val() == '' ? 0 : $(this).find('.cat_pcs').val()));

						}



					});

			

				if (data != "") {

					curRow.find('.tot_blc_pcs').val(data.no_of_piece - curr_used_pcs);

					curRow.find('.tot_blc_gwt').val(data.gross_wt - curr_used_gross);

					curRow.find('.blc_pcs').html(data.no_of_piece - curr_used_pcs);

					curRow.find('.blc_gwt').html(data.gross_wt - curr_used_gross);

				}

				else {

					curRow.find('.tot_blc_pcs').val(0);

					curRow.find('.tot_blc_gwt').val(0);

					curRow.find('.blc_pcs').html(0);

					curRow.find('.blc_gwt').html(0);

				}

			}

		}

	});

}

function getNontagDesigns(curRow, pro_id, des_id) {

	curRow.find('.cat_design option').remove();

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_active_design_products',

		dataType: "json",

		method: "POST",

		data: { 'id_product': pro_id, 'searchTxt': '', 'is_non_tag': curRow.find('.is_non_tag').val(), 'id_branch': $('#id_branch').val(), 'design_no': des_id },

		success: function (data) {

			var design_id = curRow.find('.cat_des_id').val()

			$.each(data, function (key, item) {

				$(curRow.find('.cat_design')).append(

					$("<option></option>")

						.attr("value", item.design_no)

						.text(item.design_name)

				);

			});

			$(curRow.find('.cat_design')).select2(

				{

					placeholder: "Select Design",

					allowClear: true

				});

			curRow.find('.cat_design').select2("val", (design_id != '' && design_id > 0 ? design_id : ""));

		}

	});

}

$(document).on('change', '.cat_design', function () {

	var row = $(this).closest('tr');

	row.find('.cat_sub_design option').remove();

	row.find('.cat_sub_design').select2("val", '');

	if (this.value != '') {

		row.find('.cat_des_id').val(this.value);

		get_ActiveCatSubDesigns(row, this.value);

	}

	getNonTagDesignDetails(row);

});

$(document).on('change', '.cat_sub_design', function () {

	if (this.value != '') {

		var row = $(this).closest('tr');

		row.find('.cat_id_sub_design').val(this.value);

		getNonTagDesignDetails(row);

	}

});

function get_ActiveCatSubDesigns(curRow, des_id) {

	curRow.find('.cat_sub_design option').remove();

	var pro_id = curRow.find('.cat_product').val();

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_ActiveSubDesigns',

		dataType: "json",

		method: "POST",

		data: { 'id_product': pro_id, 'design_no': des_id },

		success: function (data) {

			var sub_design_id = curRow.find('.cat_id_sub_design').val()

			$.each(data, function (key, item) {

				$(curRow.find('.cat_sub_design')).append(

					$("<option></option>")

						.attr("value", item.id_sub_design)

						.text(item.sub_design_name)

				);

			});

			$(curRow.find('.cat_sub_design')).select2(

				{

					placeholder: "Select Sub Design",

					allowClear: false

				});

			curRow.find('.cat_sub_design').select2("val", (sub_design_id != '' && sub_design_id > 0 ? sub_design_id : ""));

		}

	});

}

/*Non-Tag Product , Design Dropdown*/

function validate_oldmetal_weight(row) {

	var gross_wt = (isNaN(row.find('.old_gwt').val()) || row.find('.old_gwt').val() == '') ? 0 : row.find('.old_gwt').val();

	var dust_wt = (isNaN(row.find('.old_dwt').val()) || row.find('.old_dwt').val() == '') ? 0 : row.find('.old_dwt').val();

	var stone_wt = (isNaN(row.find('.old_swt').val()) || row.find('.old_swt').val() == '') ? 0 : row.find('.old_swt').val();

	var old_wastage = (isNaN(row.find('.old_wastage').val()) || row.find('.old_wastage').val() == '') ? 0 : row.find('.old_wastage').val();

	var wastage_wt = 0;

	var other_stone_wt = (isNaN(row.find('.stone_wt').val()) || row.find('.stone_wt').val() == '') ? 0 : row.find('.stone_wt').val();

	var net_wt = parseFloat(parseFloat(gross_wt) - (parseFloat(dust_wt) + parseFloat(stone_wt) + parseFloat(other_stone_wt) + parseFloat(wastage_wt))).toFixed(3);

	// Condition to check whether Dust wt is greater than Gwt

	if (parseFloat(dust_wt) > parseFloat(gross_wt)) {

		row.find('.old_dwt').val(0);

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Dust WT is greater than Gwt' })

		row.find('.old_dwt').focus();

	}

	// Condition to check whether Stone wt is greater than Gwt

	else if (parseFloat(stone_wt) > parseFloat(gross_wt)) {

		row.find('.old_swt').val(0);

		row.find('.stone_details').val("");

		row.find('.stone_price').val(0);

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Stone WT is greater than Gwt' })

		row.find('.old_swt').focus();

	}

	// Condition to check whether Dust wt and Stone Wt is greater than Gwt

	else if (parseFloat(stone_wt) + parseFloat(dust_wt) > parseFloat(gross_wt)) {

		row.find('.old_swt').val(0);

		row.find('.stone_details').val("");

		row.find('.stone_price').val(0);

		row.find('.old_dwt').val(0);

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Dust WT & Stone WT is greater than Gwt' })

		row.find('.old_dwt').focus();

		row.find('.old_nwt').val(gross_wt);

	} else {

		wastage_wt = parseFloat((net_wt * (old_wastage / 100))).toFixed(5);

		net_wt = parseFloat(parseFloat(gross_wt) - parseFloat(dust_wt) - parseFloat(stone_wt) - parseFloat(other_stone_wt) - parseFloat(wastage_wt)).toFixed(3);

		row.find('.old_wastage_wt').val(parseFloat(wastage_wt).toFixed(3));

		row.find('.old_nwt').val(net_wt);

	}

}

function non_tag_design(pro_id) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_active_design_products',

		dataType: "json",

		method: "POST",

		data: { 'id_product': pro_id },

		success: function (data) {

			console.log(data);

			cat_design_details = data;

		}

	});

}

function homebill_design() {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_active_design_products',

		dataType: "json",

		method: "POST",

		success: function (data) {

			cus_design_details = data;

		}

	});

}

function non_tag_sub_design() {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_ActiveSubDesigns',

		dataType: "json",

		method: "POST",

		success: function (data) {

			cat_sub_design_details = data;

		}

	});

}

function homebill_sub_design() {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_catalog/get_ActiveSubDesigns',

		dataType: "json",

		method: "POST",

		success: function (data) {

			cus_sub_design_details = data;

		}

	});

}

//ESTIMATION EDIT FUNCTION STARTS

function estimation_tag_data() {

	$(".overlay").css('display', 'block');

	let estimation_id = $("#estimation_id").val();

	var weight_scheme_closure_type = $('#weight_scheme_closure_type').val(); //1-General,2-Based on M.C & V.A

	let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

	if (estimation_id > 0) {

		my_Date = new Date();

		$.ajax({

			type: 'POST',

			url: base_url + 'index.php/admin_ret_estimation/estimation/est_edit/' + estimation_id + '?nocache=' + my_Date.getUTCSeconds(),

			dataType: 'json',

			success: function (data) {

				//  console.log(data.old_metal);

				$('#cus_del_address1').val(data.estimation.address1);

				$('#cus_del_pincode').val(data.estimation.pincode);

				$('#cus_del_state').val(data.estimation.id_state);

				$('#cus_del_country').val(data.estimation.id_country);

				$('#summary_stn_dis_per').val(data.estimation.disc_per);

				$('#summary_stn_dis_per').prop('readonly',true);

				$('#summary_blk_dis_per').val(data.estimation.bulk_was_disc_per);

				$('#summary_blk_dis_per').prop('readonly',true);

				// if(data.estimation.bulk_was_disc_per != 0){

				// 	$('.add_slab').prop("disabled",true);
				// }

				// $('#cmp_state').val(data.estimation.id_state);

				// $('#cmp_country').val(data.estimation.id_country);

				var tag_itm_details = data.tag_details.item_details ;

				var nt_itm_details = data.non_tag_details.item_details ;

				var cus_itm_details = data.est_home_bill.item_details ;
				
				var topup_scheme = false;
				
				if(data.chit_details.length > 0){

					$.each(data.chit_details, function (index, itm) {

						if(itm.is_topup_scheme == 1){
							topup_scheme = true;
						}

					});
				}

				// CHIT DETAILS ENDS

				//TAG DETAILS STARTS

				if (data.tag_details.item_details != null && data.tag_details.item_details.length > 0) {

					$.each(data.tag_details.item_details, function (key, item) {

						var row = "";

						//  ('est_edit', items);

						var paid_advance = 0;

						var paid_weight = 0;

						var wt_amt = 0;

						var slab_details = [];

						order_adv_details = item.advance_details;

						if (item.advance_details.length > 0) {

							$.each(item.advance_details, function (key, item) {

								paid_advance += parseFloat(item.paid_advance);

								paid_weight += parseFloat(item.paid_weight);

								wt_amt += parseFloat(item.paid_weight * item.rate_per_gram);

							});

						}

						$('.summary_adv_paid_amt').html(parseFloat(paid_advance) + parseFloat(wt_amt));

						$('.summary_adv_paid_weight').html(parseFloat(paid_weight).toFixed(3));

						var stone_details = [];

						var other_metal_details = [];

						var child_tag_details = [];

						var rowId = new Date().getTime();

						var select_emp = "";

						$.each(emp_details, function (pkey, emp) {

							var selected = "";

							if (item.item_emp_id == emp.id_employee) {

								selected = "selected";

							}

							select_emp += "<option value='" + emp.id_employee + "'" + selected + " >" + emp.emp_name + "</option>";

						});

						// var select_purity = "";

						// $.each(purities, function (pkey, sitem) {

						// 	var selected = "";

						// 	if (item.purity == sitem.id_purity) {

						// 		selected = "selected";

						// 	}

						// 	select_purity += "<option value='" + sitem.id_purity + "'" + selected + ">" + sitem.purity + "</option>";

						// });

						var stone_details = [];

						var other_metal_details = [];

						var stone_price = 0;

						var tag_other_itm_amount = 0;

						$.each(item.stone_details, function (skey, sitem) {

							stone_price += parseFloat(sitem.price);

							stone_details.push({

								"show_in_lwt": sitem.is_apply_in_lwt,

								"stone_id": sitem.stone_id,

								"stones_type": sitem.stone_type,

								"stone_pcs": sitem.pieces,

								"stone_wt": sitem.wt,

								"stone_price": sitem.price,

								"stone_rate": sitem.rate_per_gram,

								"stone_uom_id": sitem.uom_id,

								"stone_cal_type": sitem.stone_cal_type,

								'max_stn_price'   : sitem.max_stn_amt,

								'quality_id'   : sitem.quality_id,

							});

						});

						$.each(item.other_metal_details, function (skey, sitem) {

							tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

							other_metal_details.push({

								"tag_other_itm_id": sitem.tag_other_itm_id,

								"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

								"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

								"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

								"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

								"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

								"tag_other_itm_uom": sitem.tag_other_itm_uom,

								"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

								"tag_other_itm_mc": sitem.tag_other_itm_mc,

								"tag_other_itm_rate": sitem.tag_other_itm_rate,

								"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

								"tag_other_itm_amount": sitem.tag_other_itm_amount,

								"purname": sitem.purname,

								"metal_name": sitem.metal_name,

							});

						});



						// charges 

						var charges_details = [];

						if(item.charges.length > 0){

							$.each(item.charges, function (skey, sitem) {

								charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.amount, "code_charge": sitem.code_charge });

							});

						}

						// $.each(adv_details, function(skey, sitem){

						// });

						$.each(item.child_tag_details, function (idx, citem) {

							var child_stone_data = [];

							if(citem.stone_details!='' && citem.stone_details!= null && citem.stone_details != undefined ){

								$.each(citem.stone_details, function (idx, stn) {

									child_stone_data.push({

										"show_in_lwt"		: stn.is_apply_in_lwt,

										"stone_id"			: stn.stone_id,

										"stones_type"		: stn.stone_type,

										"stone_pcs"			: stn.pieces,

										"stone_wt"			: stn.wt,

										"stone_price"		: stn.price,

										"stone_rate"		: stn.rate_per_gram,

										"stone_uom_id"		: stn.uom_id,

										"stone_cal_type"	: stn.stone_cal_type

									});

								})

							}

							child_tag_details.push({

								"act_gwt"                  : citem.gross_wt,

								"act_sales_value"          : citem.item_cost,

								"caltype"                  : citem.calculation_based_on,

								"child_tag_id"             : citem.tag_id,

								"cur_gwt"                  : citem.gross_wt,

								"design_id"                : citem.design_id,

								"design_name"              : citem.design_name,

								"discount_amount"          : citem.discount,

								"est_mc_type"              : citem.mc_type,

								"est_rate_per_grm"         : citem.est_rate_per_grm,

								"tag_id"                   : citem.tag_id,

								"gwt"                      : citem.gross_wt,

								"id_mc_type"               : citem.mc_type,

								"id_orderdetails"          : citem.id_orderdetails,

								"id_sub_design"            : citem.id_sub_design,

								"lwt"                      : citem.less_wt,

								"label"                    : citem.label,

								"market_rate_cost"         : citem.market_rate_cost,

								"market_rate_tax"          : citem.market_rate_tax,

								"mc"                       : citem.mc_value,

								"nwt"                      : citem.net_wt,

								"parent_tag_id"            : citem.parent_tag_id,

								"partial"                  : citem.is_partial,

								"piece"                    : citem.piece,

								"pro_id"                   : citem.product_id,

								"prodct_name"              : citem.product_name,

								"pur_name"                 : citem.pur_name,

								"purname"                  : citem.pur_name,

								"purity"                   : citem.purity,

								"cost"                     : citem.item_cost,

								"size"                     : citem.size,

								"size_name"                : citem.size,

								"sub_des_name"             : citem.sub_design_name,

								"sub_design_name"          : citem.sub_design_name,

								"tax_group_id"             : citem.tax_group_id,

								"tot_nwt"                  : citem.net_wt,

								"tot_tag_nwt"              : citem.net_wt,

								"wastage"                  : citem.wastage_percent,

								"retail_max_wastage_percent": citem.wastage_percent,

								"item_emp_id"              : citem.item_emp_id,

								"stone_details"            : child_stone_data,

								"sales_mode"               : citem.sales_mode,

								"rate_field"               : citem.rate_field,

								"id_section"               : citem.id_section,

								"section_name"             : citem.section_name,

								"huid"        		       : citem.huid,

							});

							console.log(JSON.stringify(child_tag_details, null, 4));

						});

						var mc_min_limit = parseFloat(item.mc_va_limit.mc_min);

						var va_min_limit = parseFloat(item.mc_va_limit.wastag_min);

						var va_wt_min_limit = parseFloat(parseFloat(item.net_wt) * parseFloat(item.mc_va_limit.wastag_min / 100)).toFixed(3);

						var tag_image = base_url + 'assets/img/no_image.png';

						if (item.tag_images.length > 0) {

							$.each(item.tag_images, function (key, val) {

								if (val.is_default == 1) {

									tag_image = base_url + 'assets/img/tag/' + val.image;

								}

							});

						}

						console.log(data.tag_details.stone_details);

						if(item.id_wastage != ''){

						var detail = {

							id_wastage: item.id_wastage,
							act_va_per: item.max_va_per,
							
						};

						// $("#blk_disc_apply").prop("disabled",true);

						// $("#blk_disc_reset").prop("disabled",true);

						slab_details.push(detail);

					}

						
					if(!topup_scheme) {

						row += '<tr id="' + rowId + '">'

							+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + item.tag_code + ' placeholder="Enter tag code" required autocomplete="off"/><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]"  value=' + item.tag_id + ' placeholder="Enter tag code" required /><input class="rate_field" type="hidden"  value="' + item.rate_field + '"/><input class="metal" type="hidden"  value="' + item.id_metal + '"/><input class="cat_id" type="hidden"  value="' + item.cat_id + '"/><input class="is_split_row" type="hidden" name="est_tag[is_split_row][]" value="' + (item.is_split_row!=undefined ? item.is_split_row :0) +'"/></td>'

							+ '<td><a href="#" onClick="create_new_empty_est_tag_merge_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" id="it_has_childtag" value= "" ><input type="hidden" name="est_tag[child_tag_details][]" id="child_tag_details" value=\'' + JSON.stringify(child_tag_details) + '\'></td>'

							+ '<td><img src="' + tag_image + '" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="' + item.tag_id + '" id="edit"><i class="fa fa-eye" ></i></a></td>'

							+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_tag[item_emp_id][]" >' + select_emp + '</select></td>'

							// +'<td><input type="checkbox" class="partial"><input type="hidden" class="is_partial"  name="est_tag[is_partial][]"value='+item.is_partial+'></td>'

							+ '<td><input type="checkbox" class="partial" ' + (item.is_partial == 1 ? 'checked' : '') + ' ' + (item.sales_mode == 1 ? 'disabled' : '') + '><input type="hidden" class="is_partial" name="est_tag[is_partial][]" value=' + item.is_partial + '></td>'

							+ '<td><div class="prodct_name">' + item.product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + item.product_id + ' /><input type="hidden" class="metal_type" value=' + item.metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + item.scheme_closure_benefit + '></td>'

							+ '<td><div class="design_name">' + item.design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + item.design_id + ' /></td>'

							+ '<td><div class="sub_design_name">' + item.sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value="' + item.id_sub_design + '" /><input type="hidden" class="quality_id" value='+item.quality_id+'><input type="hidden" class="stone_type" value='+item.stone_type+'><input type="hidden" class="uom_id" value='+item.uom_id+'></td>'

							+ '<td><div class="section_name">' + item.section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + item.id_section + ' /></td>'

							+ '<td><div class="huid">' + item.huid + '</div></td>'

							+ '<td><div class="order_no">' + item.order_no + '<input type="hidden" class="id_orderdetails" name="est_tag[id_orderdetails][]" value="' + item.id_orderdetails + '" /></td>'

							// + '<td><select class="form-control tag_purity" id="select_purity" name="est_tag[purity][]" style="width:100px;"' + (profile.est_purity_edit == 1 ? '' : 'disabled') + '></select><input type="hidden" class="id_purity" value='+item.purity+'></td>'

							// + '<td><select class="form-control tag_purity" name="est_tag[purity][]" style="width:100px;"' + (profile.est_purity_edit == 1 ? '' : 'disabled') + '>' + select_purity + '</select></td>'

							+ '<td><span style="width:100px;">' + (Number(item.purname).toFixed(2)) + '</span><input class="est_tag_id_purity" type="hidden" name="est_tag[purity][]" value="' + item.purity +'"/></td>'

							// + '<td><div class="purity">' + item.purname + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + item.purity + ' /></td>'

							+ '<td><div class="sizes">' + item.size + '</div><input type="hidden" class="size" name="est_tag[size][]" value="' + item.id_size + '"/></td>'

							// + '<td><div class="pieces">' + item.piece + '</div><input type="hidden" class="piece" name="est_tag[piece][]" value=' + item.piece + ' /></td>'

							+ '<td><input type="number" class="form-control piece" name="est_tag[piece][]"  style="width:100px;" value=' + item.piece + ' readonly /><input type="hidden" class="act_piece" name="est_tag[act_piece][]" value=' + item.piece + ' readonly /></td>'

							+ '<td><input type="text" class="form-control gwt" name="est_tag[gwt][]" step="any" value=' + item.gross_wt + ' style="width:100px;" readonly/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value="' + item.gross_wt + '"/><input type="hidden" class="act_gwt" value="' + item.tag_gross_wt + '"/></td>'

							//+'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" readonly></td>'

							+ '<td><div class="form-group"><div class="input-group "><input class="form-control lwt"  value=' + item.less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" class="stone_price" name="est_tag[stone_price][]" value="' + stone_price + '"></td>'

							+ '<td><input type="number" class="form-control tot_stn_amt" name="est_tag[stone_amt][]" value=' + item.stn_amt + ' readonly style="width:100px;" /></td>'

							+ '<td><input type="number" class="form-control tot_dia_amt" name="est_tag[diamond_amt][]" value=' + item.dia_amt + ' readonly style="width:100px;" /></td>'

							+ '<td><div class="nwt">' + item.net_wt + '</div><input type="hidden" class="form-control tot_nwt" name="est_tag[nwt][]" value=' + item.net_wt + ' style="width:100px;" /><input type="hidden" class="tot_tag_nwt" value=' + item.tag_net_wt + ' /></td>'

							+ '<td><input type="text" class="form-control market_rate_value" ' + rate_readonly + ' name="est_tag[est_rate_per_grm][]" value="' + item.est_rate_per_grm + '"style="width:100px;" /></td>'

							+ '<td><div class="act_wast_per" style="display:none">'+ item.max_va_per +'</div><input type="number"  name="est_tag[max_va_per][]" class="form-control tag_act_va_per" value="'+ item.max_va_per+'" style="width:100px;" readonly/><input type="hidden" class="tag_va_per" value="' + item.max_va_per + '"></td>'

							+ '<td><div class="wastage" style="display:none">' + item.wastage_percent + '</div><input type="number" name="est_tag[wastage][]" class="form-control wastage_max_per" ' + va_readonly + ' value=' + item.wastage_percent + ' style="width:100px;"/><input type="hidden" class="va_min_limit" value="' + va_min_limit + '"><input type="hidden" class="act_va_per" name="est_tag[act_va_per][]" value=' + item.act_wast_per + '><input type="hidden" class="id_wastage_slab" name="est_tag[id_wastage_slab][]"  value=' + item.id_wastage + '><input type="hidden" class="slab_details" value=' + JSON.stringify(slab_details) + '><input type="hidden" class="blk_disc_apld" name="est_tag[blk_disc_apld][]" value=' + item.tag_blk_disc + '><input type="hidden" class="wast_slb_val" name="est_tag[wast_slab_val][]" value="' + item.slb_val + '"></td>'

							+ '<td><div class="est_wastage_wt" style="display:none"></div><input type="number" ' + va_readonly + ' name="est_tag[est_wastage_wt][]" class="form-control est_wastage_wt" value=' + item.wastage_percent + ' style="width:100px;" /><input type="hidden" class="va_wt_min_limit" value="' + va_wt_min_limit + '"><input type="hidden" class="act_va_per" value="' + item.max_va_per + '" name="est_tag[act_va_per][]"></td>'

							+ '<td><select class="form-control est_mc_type" '+mc_readonly+' name="est_tag[id_mc_type][]"style="width:80px; '+mc_type_selection+'"><option value="2" ' + (item.mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (item.mc_type == 1 ? 'selected' : '') + '>Piece</option></select></td>'

							+ '<td><div class="mc_val" style="display:none">' + item.mc_value + '</div><input class="form-control act_mc_value" type="number" '+mc_readonly+' value="' + item.mc_value + '"  name="est_tag[mc][]" /><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" />Tot Mc:<span class="tot_mc" ></span><input type="hidden" class="mc_min_limit" value="' + mc_min_limit + '"><input type="hidden" name="est_tag[max_mc][]" class="tag_max_mc" value="' + item.max_mc + '"></td>'

							//+'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value='+JSON.stringify(stone_details)+'><input type="hidden" class="stone_price" name="est_tag[stone_price][]" value="'+stone_price+'"></td>'

							+'<td>'+(item.charge_value > 0 ? '<a href="#" onClick="showTagCharges($(this).closest(\'tr\'));">'+item.charge_value+'</a><input type="hidden" class="charges_details" name="est_tag[charges_details][]" value=' + JSON.stringify(charges_details) + '>' :(item.charge_value == null ? '0.00' : item.charge_value ))+'</td>'

							+'<td>'+(item.tag_other_itm_amount > 0 ? '<a href="#" class="tag_other_itm_amount_link" onClick="showTagothermetals($(this).closest(\'tr\'));">'+item.tag_other_itm_amount+'</a><input type="hidden" class="tag_other_metal_details" value=' + JSON.stringify(other_metal_details) + '>' :item.tag_other_itm_amount)+'</td>'

							+ '<td><div class="cost">' + item.sales_value + '</div><input type="hidden" name="est_tag[total_cgst][]" class="sale_cgst"/><input type="hidden" class="sale_sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="sale_igst" name="est_tag[total_igst][]"/> <input type="hidden" class="sale_taxable_amount" name="est_tag[sale_taxable_amount][]"/> <input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value="'  + btoa(JSON.stringify(other_metal_details)) + '"><input type="hidden" class="act_sales_value" value="' + item.sales_value + '"><input class="sales_value" type="hidden" name="est_tag[cost][]" value="' + item.sales_value + '" /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value="" /><input class="caltype" type="hidden" name="est_tag[caltype][]" value="' + item.calculation_based_on + '" /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value="" /><input class="tax_percentage" type="hidden" value="" /><input class="tax_group_id" type="hidden" name="est_tag[tax_percentage][]" value="' + item.tax_group_id + '" /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value="' + item.stn_amt + '" />	<input class="certification_price" type="hidden" name="est_tag[certification_price][]" value="" /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value="' + item.mc_type + '" /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="charge_value" name="est_tag[charge_value][]"  value="' + item.charge_value + '"><input type="hidden" class="sales_mode" value=' + item.sales_mode + ' ><input type="hidden" class="purchase_cost" value=' + item.tag_purchase_cost + ' name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" value=' + item.pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + item.pur_va + ' name="est_tag[pur_va][]" ><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" value="' + item.tag_other_itm_amount + '"><input type="hidden" class="order_rate" name="est_tag[order_rate][]"  value="' + item.order_rate + '"> </td>'

							+ '<td><a href="#"onClick="remove_order_tag($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

							+ '</tr>';

						$('#estimation_tag_details tbody').append(row);

						$('#estimation_tag_details > tbody').find('.item_emp_id').select2();

						$('#estimation_tag_details > tbody').find('.tag_purity').select2();

						// getestPurityDetails(item.id_metal,item.cat_id);

						if (child_tag_details != '' && child_tag_details != undefined && child_tag_details != '[]') {

							set_tag_merge_data(rowId);

						}

						tag_split_details.push(item); 

					}

					else{

						$('#est_tag_scan').val(item.tag_code);

						$('#tag_search').trigger('click');
					}

					});

					calculatetag_SaleValue();
					calc_dia_tot_amt();

				}
				if(tag_itm_details != undefined){
				edit_wastage_slab(tag_itm_details);
				}

				//TAG DETAILS ENDS

				//NON-TAG DETAILS STARTS

				if (data.non_tag_details.item_details != null && data.non_tag_details.item_details.length > 0) {

					//    console.log(data.non_tag_details);

					$.each(data.non_tag_details.item_details, function (key, item) {

						var slab_details = [];

						var row = "";

						var rate_field = '';

						var market_rate_field = '';

						var a = $("#catRow").val();

						var i = ++a;

						$("#catRow").val(i);

						$.each(item.stone_details, function (skey, sitem) {

							stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type, "max_stn_price": sitem.max_stn_amt });

						});

						// cat_product_details=[];

						var select_section = "";

						$.each(section_details, function (key, sec_item) {

							var selected = "";

							if($('#id_branch').val()==sec_item.id_branch && sec_item.is_home_bill_counter != 1)

							{

								if (sec_item.id_section == item.id_section) {

									selected = "selected";

								}

								select_section += "<option value='" + sec_item.id_section + "' " + selected + ">" + sec_item.section_name + "</option>";

							}

						});

						var select_product = "";

						$.each(cat_product_details, function (key, pitem) {

							var selected = "";

							if (item.product_id == pitem.pro_id) {

								selected = "selected";

							}

							select_product += "<option value='" + pitem.pro_id + "' " + selected + ">" + pitem.product_name + "</option>";

						});

						var select_design = "";

						$.each(cat_design_details, function (key, ditem) {

							var selected = "";

							console.log(ditem);

							if (ditem.pro_id == item.product_id) {

								if (item.design_id == ditem.design_no) {

									selected = "selected";

								}

								select_design += "<option value='" + ditem.design_no + "' " + selected + ">" + ditem.design_name + "</option>";

							}

						});

						var select_sub_design = "";

						$.each(cat_sub_design_details, function (key, sitem) {

							var selected = "";

							if (sitem.id_product == item.product_id && sitem.id_design == item.design_id) {

								if (item.id_sub_design == sitem.id_sub_design) {

									selected = "selected";

								}

								select_sub_design += "<option value='" + sitem.id_sub_design + "' " + selected + ">" + sitem.sub_design_name + "</option>";

							}

						});

						var select_purity = "";

						$.each(purities, function (pkey, sitem) {

							var selected = "";

							if (item.purid == sitem.id_purity) {

								selected = "selected";

							}

							select_purity += "<option value='" + sitem.id_purity + "'" + selected + ">" + sitem.purity + "</option>";

						});

						$.each(purity_rate, function (k, purval) {

							if (purval.id_purity == item.purid && purval.id_metal == item.metal_type) {

								rate_field = purval.rate_field;

								market_rate_field = purval.market_rate_field;

							}

						});

						var charges = [];

						var amount = 0;

						console.log(item.charges);

						$.each(item.charges, function (pkey, pitem) {

							amount += parseFloat(pitem.amount);

							charges.push({ "id_charge": pitem.id_charge, "code_charge": pitem.name_charge, "value_charge": pitem.amount });

						});

						var select_emp = "";

						$.each(emp_details, function (pkey, emp) {

							var selected = "";

							if (item.item_emp_id == emp.id_employee) {

								selected = "selected";

							}

							select_emp += "<option value='" + emp.id_employee + "'" + selected + " >" + emp.emp_name + "</option>";

						});

						if(item.id_wastage != ''){

						var detail = {

							id_wastage: item.id_wastage,
							act_va_per: item.max_va_per,
							
						};

						slab_details.push(detail);

					}						

						row += '<tr id=' + i + '>'

							+ '<td><input type="checkbox" class="non_tag" checked ><input type="hidden" class="is_non_tag" name="est_catalog[is_non_tag][]" value=' + item.is_non_tag + '><input type="hidden" class="cat_rate_field" name="est_catalog[rate_field][]" value="' + rate_field + '" ><input type="hidden" class="cat_market_rate_field" name="est_catalog[market_rate_field][]" value="' + market_rate_field + '"></td>'

							+ '<td><select class="form-control cat_section" name="est_catalog[id_section][]" value="" placeholder="Search Section" style="width:150px;">' + select_section + '</select></td>'

							+ '<td><select class="form-control cat_product" name="est_catalog[product][]" value=""  placeholder="Search Product" style="width:150px;">' + select_product + '</select><input type="hidden" class="cat_pro_id" name="est_catalog[pro_id][]"  value=' + item.product_id + ' /><input type="hidden" class="tax_percentage" value=' + item.tax_percentage + '><input type="hidden" class="tax_group_id" value=' + item.tgrp_id + '><input type="hidden" class="tgi_calculation" value=' + item.tgi_calculation + '><input type="hidden" class="metal_type" value=' + item.metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + item.scheme_closure_benefit + '"><input type="hidden" class="cat_sales_mode" value="'+item.sales_mode+'"></td>'

							+ '<td><select class="form-control cat_design" name="est_catalog[design][]" value=""  placeholder="Search Design" style="width:150px;">' + select_design + '</select><input type="hidden" class="cat_des_id" name="est_catalog[des_id][]"  value=' + item.design_id + '  /></td>'

							+ '<td><select class="form-control cat_sub_design" name="est_catalog[sub_design][]" value=""placeholder="Search SubDesign" required style="width:150px;">' + select_sub_design + '</select><input type="hidden" class="cat_id_sub_design" name="est_catalog[id_sub_design][]"  value="' + item.id_sub_design + '" /></td>'

							+ '<td><select class="form-control item_emp_id" style="width:100px !important" name="est_catalog[item_emp_id][]" value=' + item.item_emp_id + ' >' + select_emp + '</select></td>'

							+ '<td><select class="form-control cat_purity" name="est_catalog[purity][]" style="width:100px;">' + select_purity + '</select></td>'

							+ '<td><input type="number" class="form-control cat_size" name="est_catalog[size][]"value=' + item.size + ' style="width:80px;" placeholder="Enter Size"/></td>'

							+ '<td><input type="number" class="form-control cat_pcs" name="est_catalog[pcs][]" value=' + item.piece + ' autocomplete="off" style="width:80px;"  placeholder="Enter Pcs"/>Stock :<span class="blc_pcs" ></span><input type="hidden" class="tot_blc_pcs"></td>'

							+ '<td><input type="number"  class="form-control cat_gwt" name="est_catalog[gwt][]" value=' + item.gross_wt + ' autocomplete="off" style="width:80px;  placeholder="Enter GWT""/><span class="blc_gwt" ></span><input type="hidden" class="tot_blc_gwt"></td>'

							//+'<td><input type="number" class="form-control cat_lwt" name="est_catalog[lwt][]" value='+item.less_wt+' autocomplete="off" style="width:80px;"  placeholder="Enter LWT"/></td>'

							+ '<td><div class="form-group"><div class="input-group "><input class="form-control cat_lwt" onclick="create_new_empty_est_cat_stone_item($(this).closest(\'tr\'));" type="number" name="est_catalog[lwt][]" value=' + item.less_wt + ' step="any" readonly="" style="width:100px;"><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cat_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'

							+ '<td><input type="number" class="form-control tot_stn_amt" name="est_catalog[stone_amt][]" value="0.00" readonly style="width:100px;" /></td>'

							+ '<td><input type="number" class="form-control tot_dia_amt" name="est_catalog[diamond_amt][]" value="0.00" readonly style="width:100px;" /></td>'


							+ '<td><input type="number" class="form-control cat_nwt" name="est_catalog[nwt][]"value=' + item.net_wt + ' autocomplete="off" readonly style="width:80px;"></td>'

							+ '<td><input class="form-control cat_market_rate_value" name="est_catalog[est_rate_per_grm][]" ' + rate_readonly + ' type="text" value=' + item.est_rate_per_grm + ' style="width:80px;"/></td>'

							+ '<td><select class="form-control mc_type" style="width:80px;"><option value="2" ' + (item.mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (item.mc_type == 1 ? 'selected' : '') + '>Piece</option><option value="3" '+(item.mc_type == 3 ? 'selected' : '') + '>% on Price</option></select><input type="hidden" value="' + item.mc_type + '" name="est_catalog[id_mc_type][]" class="id_mc_type"></td>'

							+ '<td><input type="number"  class="form-control cat_mc" name="est_catalog[mc][]" value=' + item.mc_value + ' style="width:80px;"  placeholder="Enter MC"/><input type="hidden" value="' + item.max_mc + '" name="est_catalog[max_mc][]" class="nn_cat_mc"></td>'

							+ '<td><input type="number" class="form-control cat_act_va_per"  value='+item.max_va_per+' name="est_catalog[max_va_per][]" style="width:80px;" readonly  placeholder="VA in %"/><input type="hidden" class="cat_pro_va_per"></td>'

							+ '<td><input type="number" class="form-control cat_wastage" name="est_catalog[wastage][]" value=' + item.wastage_percent + ' style="width:80px;"  placeholder="VA in %"/><input type="hidden" class="act_va_per" value=' + item.act_wast_per + ' name="est_catalog[act_va_per][]" ><input type="hidden" class="id_wastage_slab"  name="est_catalog[id_wastage_slab][]" value=' + item.id_wastage + '><input type="hidden" class="slab_details" value=' + JSON.stringify(slab_details) + '><input type="hidden" class="wast_slb_val" name="est_catalog[wast_slab_val][]" value=' + item.slb_val + '></td>'

							+ '<td><input type="number" class="form-control cat_wastage_wt" value=' + item.wastage_percent + ' style="width:80px;"  placeholder="Wt"/></td>'

							+ '<td><a href="#" onClick="create_new_empty_est_cus_charges_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="charges_details" name="est_catalog[charges_details][]" value=' + JSON.stringify(charges) + '><input class="value_charge" type="hidden" name="est_catalog[value_charge][]"  value=' + amount + ' ></td>'

							+ '<td><input type="number" class="form-control cat_taxable_amount" name="est_catalog[taxable_amt][]"value=""readonly style="width:100px;"><input type="hidden" name="est_tag[total_cgst][]" class="cat_cgst"/><input type="hidden" class="cat_sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="cat_igst" name="est_tag[total_igst][]"/></td>'

							+ '<td><input type="number"  class="form-control cat_tax_per" name="est_catalog[tax_per][]" value=' + item.tax_percentage + ' readonly style="width:80px;"/></td>'

							+ '<td><input type="number"  class="form-control cat_tax_price" name="est_catalog[tax_price][]" value=' + item.item_total_tax + ' readonly style="width:80px;"/><input type="hidden" name="est_catalog[total_cgst][]" class="cat_cgst"/><input type="hidden" class="cat_sgst" name="est_catalog[total_sgst][]"/><input type="hidden" class="cat_igst" name="est_catalog[total_igst][]"/></td>'

							// + '<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=""><input type="hidden" class="stone_price" name="est_tag[stone_price][]"><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" ></td></td>'

							// +'<td><a href="#" onClick="create_new_empty_est_cat_stone_item($(this).closest(\'tr\'));"value="" class="btn btn-success"><i class="fa fa-plus"></i></a></td>'

							+ '<td><input type="number" class="form-control cat_amt" name="est_catalog[amount][]" value=' + JSON.stringify(stone_details) + ' readonly style="width:100px;" /><input type="hidden" class="cat_calculation_based_on" name="est_catalog[calculation_based_on][]"value=' + item.calculation_based_on + ' /><input type="hidden" id="stone_details" class="stone_details" name="est_catalog[stone_details][]value=' + stone_details + '"><input type="hidden" id="material_details" class="material_details" name="est_catalog[material_details][]"><input type="hidden" value="0" class="stone_price" id="stone_price"><input type="hidden" class="material_price" id="material_price"><input type="hidden" class="market_rate_cost" name="est_catalog[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_catalog[market_rate_tax][]"><input type="hidden" class="sales_mode" ><input type="hidden" class="purchase_cost" name="est_catalog[purchase_cost][]" ><input type="hidden" class="pur_mc" name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" name="est_tag[pur_va][]" ></td>'

							+ '<td><a href="#" onClick="removeCat_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'

							+ '</tr>';

						$('#estimation_catalog_details tbody').append(row);

						$('#estimation_catalog_details > tbody').find('.cat_product,.cat_design,.cat_sub_design,.cat_purity,.cat_section,.item_emp_id').select2();

						let lastRow = $('#estimation_catalog_details > tbody tr:last');

						calc_cat_wastage_wt(lastRow);

						getNonTagDesignDetails(lastRow);

					});

					calculateSaleValue();

				}
				if(nt_itm_details != undefined){
				edit_wastage_slab(nt_itm_details);
				}

				//NON-TAG DETAILS ENDS

				//HOME BILL DETAILS STARTS

				if (data.est_home_bill.item_details != null && data.est_home_bill.item_details.length > 0) {

					console.log(data.est_home_bill);

					$.each(data.est_home_bill.item_details, function (key, item) {
						custom_split_details.push(item);
						append_custom_details(item);
					});

					calculateCustomItemSaleValue();

				}
				if(cus_itm_details != undefined){
				edit_wastage_slab(cus_itm_details);
				}

				//HOME BILL DETAILS ENDS

				//OLD METAL STARTS

				if (data.old_metal.old_matel_details != null && data.old_metal.old_matel_details.length > 0) {

					console.log(data.old_metal);

					$.each(data.old_metal.old_matel_details, function (key, item) {

						var purity = "<option value=''>-Select Purity-</option>";

						$.each(purities, function (pkey, pitem) {

							purity += "<option value='" + pitem.id_purity + "'>" + pitem.purity + "</option>";

						});

						// var matelTupes = "<option value=''>- Select Metal-</option>";

						var allowed_old_met_pur = $('#allowed_old_met_pur').val();

						
						if (metal_master && metal_master.length > 0) {
							if (allowed_old_met_pur == 1) {
								matel_types = metal_master;
							} else if (allowed_old_met_pur == 2) {
								matel_types = metal_master.filter(function(m) {
									return m.metal && m.metal.toLowerCase() === 'gold';
								});
							} else if (allowed_old_met_pur == 3) {
								matel_types = metal_master.filter(function(m) {
									return m.metal && m.metal.toLowerCase() === 'silver';
								});
							} else {
								matel_types = metal_master;
							}
						} else {
							matel_types = [];
						}

						var matelTupes = [];

						var matelTupes = "";

						// alert(matel_types);

						$.each(matel_types, function (key, mitem) {

							var selected = "";

							if (item.id_category == mitem.id_metal) {

								selected = "selected";

							}

							matelTupes += "<option value='" + mitem.id_metal + "' " + selected + ">" + mitem.metal + "</option>";

						});

						var metal_types = "";

						// var old_metal_types=[];

						$.each(item.old_metal_types, function (key, mitem) {

							var selected = "";

							if (item.id_old_metal_type == mitem.id_metal_type) {

								selected = "selected";

							}

							metal_types += "<option value='" + mitem.id_metal_type + "' " + selected + ">" + mitem.metal_type + "</option>";

						});

						var old_metal_cat = "";

						// var old_metal_types=[];

						$.each(item.old_metal_category, function (key, citem) {

							var selected = "";

							if (item.id_old_metal_category == citem.id_old_metal_cat) {

								selected = "selected";

							}

							old_metal_cat += "<option value='" + citem.id_old_metal_cat + "' " + selected + ">" + citem.old_metal_cat + "</option>";

						});

						var stone_details = [];

						var other_metal_details = [];

						var stone_price = 0;

						var tag_other_itm_amount = 0;

						$.each(item.stone_details, function (skey, sitem) {

							if (sitem.pieces > 0) {

								stone_price += parseFloat(sitem.price);

								stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.price, "old_stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "old_stone_cal_type": sitem.stone_cal_type, "max_stn_price": sitem.max_stn_amt });

							}

						});

						var old_metal_product = "";

						$.each(old_metal_prod_details, function (pkey, pitem) {

							if (item.id_old_metal_type == pitem.id_metal) {

								var selected = "";

								if (item.old_metal_prod_id == pitem.pro_id) {

									selected = "selected='selected'";

								}

								old_metal_product += "<option value='" + pitem.pro_id + "' " + selected + ">" + pitem.product_name + "</option>";

							}



						});

						var row = "";

						var a = $("#oldMRow").val();

						var i = ++a;

						$("#oldMRow").val(i);

						row += '<tr id=oldM' + i + '>'

							+ '<td><select class="old_id_category" name="est_oldmatel[id_category][]" value="">' + matelTupes + '</select></td>'

							+ '<td><select class="old_metal_type" name="est_oldmatel[id_old_metal_type][]" value="">' + metal_types + '</select></td>'

							+ '<td><select class="old_metal_category" name="est_oldmatel[id_old_metal_category][]" value="">' + old_metal_cat + '</select></td>'

							+ '<td><select style="width:200px" class="old_metal_product form-control" name="est_oldmatel[old_metal_product][]" value="">' + old_metal_product + '</select><input type="hidden" class="old_metal_prod_id"  name="est_oldmatel[old_metal_prod_id][]" value="' + item.old_metal_prod_id + '"></td>'

							+ '<td><input type="number" class="form-control old_purity" name="est_oldmatel[purity][]"value=' + item.purid + ' value="" style="width: 86px;"/></td>'

							+ '<td><input type="number" class="form-control old_pcs" name="est_oldmatel[pcs][]"value=' + item.piece + '	style="width: 100px;"/></td>'

							+ '<td><input type="number" class="form-control old_gwt" name="est_oldmatel[gwt][]"value=' + item.gross_wt + ' style="width: 86px;"/></td>'

							+ '<td><input class="form-control old_dwt" type="number" name="est_oldmatel[dwt][]"value=' + item.dust_wt + ' style="width: 86px;"/></td>'

							+ '<td><input type="number" class="form-control old_swt" name="est_oldmatel[swt][]" value=' + item.stone_wt + ' style="width: 86px;"/></td>'

							+ '<td><input class="form-control old_wastage" type="number" name="est_oldmatel[wastage][]" step="any" value=' + item.wastage_percent + ' style="width: 86px;"/></td>'

							+ '<td><input class="form-control old_wastage_wt" type="number" name="est_oldmatel[wastage_wt][]" step="any" value=' + item.wastage_wt + ' style="width: 86px;"/></td>'

							+ '<td><input type="number" class="form-control old_nwt" name="est_oldmatel[nwt][]" value=' + item.net_wt + ' readonly style="width: 86px;"/></td>'

							+ '<td><input type="number" class="form-control old_rate" name="est_oldmatel[rate][]" value=' + item.rate_per_gram + ' style="width: 100px;"/></td>'

							+ '<td><input type="number" class="form-control old_touch" name="est_oldmatel[touch][]" value=' + item.touch + ' style="width: 86px;"/></td>'

							+ '<td><a href="#" onClick="create_new_empty_est_old_metal_stone($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_custom[stone_details][]" value=' + JSON.stringify(stone_details) + '></td>'

							+ '<td><select class="purpose"><option value="1">Cash</option><option value="2" selected>Exchange</option></select><input type="hidden" value=' + item.purpose + ' name="est_oldmatel[id_purpose][]" class="id_purpose"></td>'

							+ '<td><input textarea class="form-control old_metal_remarks" style="width:217px;height:35px;" name="est_oldmatel[old_metal_remarks][]" placeholder="Enter Remarks"  rows="5" cols="20" value="' + item.remark + '"></textarea></td>'

							+ '<td><input class="form-control old_amount" type="number" name="est_oldmatel[amount][]" value=' + item.amount + ' style="width: 100px;"/><input type="hidden" id="stone_details" class="stone_details" name="est_oldmatel[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" value=' + stone_price + ' class="stone_price" id="stone_price"><input type="hidden" id="stone_wt" class="stone_wt" value=' + item.stone_wt + '></td>'

							+ '<td><a href="#" onClick="remove_old_metal_row($(this).closest(\'tr\').remove());" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

							+ '</tr>';

						$('#estimation_old_matel_details tbody').append(row);

						$('#estimation_old_matel_details > tbody').find('.old_id_category').focus();

						$('#estimation_old_matel_details > tbody').find('.purpose').select2();

						$('#estimation_old_matel_details > tbody').find('.old_metal_category').select2();

						$('#estimation_old_matel_details > tbody').find('.old_metal_type').select2();

						$('#estimation_old_matel_details > tbody').find('.old_id_category').select2();

						$('#estimation_old_matel_details > tbody').find('.old_id_product').select2();

						$('#estimation_old_matel_details > tbody').find('.purpose').select2({

							placeholder: "Purpose",

							allowClear: true

						});

					});

					calculate_purchase_details();

					calculate_sales_details();

				}

				//OLD METAL ENDS



				// CHIT DETAILS STARTS

				if (data.chit_details != null && data.chit_details.length > 0 && ctrl_page[1] == 'estimation' && ctrl_page[2] == 'add') {

					$.each(data.chit_details, function (key, item) {

						var row = "";

						row += '<tr>'

							+ '<td><input class="form-control scheme_account_id" type="number" name="chit_uti[scheme_account_id][]"  value=' + item.scheme_account_id + ' /><input type="hidden" name="chit_uti[id_scheme_account][]" class="form-control id_scheme_account" id="id_scheme_account" value=' + item.scheme_account_id + '><input type="hidden" class="form-control scheme_type" id="scheme_type" value=' + item.scheme_type + '><input type="hidden" class="form-control closing_amount" value=' + item.closing_amount + '><input type="hidden" class="form-control closing_weight"value=' + item.closing_weight + '><input type="hidden" name="chit_uti[paid_installments][]" class="form-control paid_installments"value=' + item.paid_installments + ' ><input type="hidden" name="chit_uti[total_installments][]" class="form-control total_installments" value=' + item.total_installments + ' ><input type="hidden" name="chit_uti[closing_add_chgs][]" class="form-control closing_add_chgs" value=' + item.closing_add_chgs + '><input type="hidden" name="chit_uti[additional_benefits][]" class="form-control additional_benefits"value=' + item.additional_benefits + ' ></td>'

							+ '<td><input type="hidden" name="chit_uti[bonus_percent][]" class="form-control bonus_percent"value=' + item.bonus_percent + '><input type="number" class="form-control chit_amt" name="chit_uti[chit_amt][]" value=' + item.closing_amount + ' readonly /><input type="hidden" name="chit_uti[wastage_per][]" class="form-control wastage_per"value=' + item.wastage_per + '><input type="hidden" name="chit_uti[mc_value][]" class="form-control mc_value"value=' + item.mc_value + '><input type="hidden" name="chit_uti[savings_in_wastage][]" class="form-control savings_in_wastage"value=' + item.savings_in_wastage + '><input type="hidden" name="chit_uti[savings_in_mcvalue][]" class="form-control savings_in_mcvalue"value=' + item.savings_in_making_charge + '><input type="hidden" name="chit_uti[closing_weight][]" class="form-control closing_weight"value=' + item.closing_weight + '><input type="hidden" name="chit_uti[is_wast_and_mc_benefit_apply][]" class="form-control is_wast_and_mc_benefit_apply"value=' + item.is_wast_and_mc_benefit_apply + '></td>'

							+ '<td><span class="saved_weight"></span></td>'

							+ '<td><a href="#" onClick="remove_chit_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';

						$('#estimation_chit_details tbody').append(row);

						$('#estimation_chit_details > tbody').find('tr:last td:eq(0) .scheme_account_id').focus();

					});

				//	calculate_chit_closing_balance();

					if(weight_scheme_closure_type!=3){

						calculate_chit_closing_balance();

					}else{

						set_tag_split_details();
						set_custom_split_details();

					}

				}





				if ($('#estimation_tag_details > tbody >tr').length > 0 || $('#estimation_custom_details > tbody >tr').length > 0 || $('#estimation_catalog_details > tbody >tr').length > 0) {

					calculate_sales_details();

					calculate_purchase_details();

					//calculate_chit_details();

				}

			}

		});

	}

	$(".overlay").css('display', 'none');

}

//ESTIMATION EDIT FUNCTION ENDS

function get_tag_status_details(Tagdata) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_tag_status_details?nocache=' + my_Date.getUTCSeconds(),

		method: "post",

		dataType: "json",

		data: Tagdata,

		success: function (data) {

			console.log(data);

			var message = data.message;

			$.toaster({ priority: 'danger', title: 'Warning!', message: data.message });

		}

	});

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

$(document).on('click', "#estimation_tag_details a.tag_images", function (e) {

	e.preventDefault();

	id = $(this).data('id');

	$("#edit-id").val(id);

	view_taging_imgs(id);

});

function view_taging_imgs(tag_id) {

	$('#order_images').empty();

	$('#imageModal_tag').modal('show');

	data = [];

	var tag_image1 = base_url + 'assets/img/tag/';

	$(".overlay").css('display', "none");

	$.ajax({

		data: ({ 'tag_id': tag_id }),

		url: base_url + "index.php/admin_ret_estimation/get_tag_img_by_id?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		dataType: "JSON",

		type: "POST",

		success: function (data) {

			for (i = 0; i < data.length; i++) {

				img_src = data[i].image;

				var preview = $('#order_images');

				var img = tag_image1 + '/' + img_src;

				console.log(img);

				if (img_src) {

					div = document.createElement("div");

					div.setAttribute('class', 'col-md-3 images');

					div.setAttribute('id', 'order_img_edit_' + [i]);

					$('.images').css('margin-right', '25px');

					key = [i];

					param = img_src;

					console.log(param);

					div.innerHTML += "<div class='form-group'><div class='image-input image-input-outline' id='kt_image_'><div class='image-input-wrapper'><img class='thumbnail' src='" + img + "'" + "style='width: 200px;height: 200px;'/><a href=" + img + " download=" + img_src + "></div></div></a>";

					preview.append(div);

				}

			}

		},

		error: function (error) {

			$("div.overlay").css("display", "none");

		}

	});

}

/*Customer Kyc*/

$('.pan_no,.ed_pan_no').on('change', function () {

	if (this.value != '') {

		var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;

		if (!regexp.test(this.value)) {

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid PAN No.." });

			$("#pan").focus();

			$("#pan").val("");

			$("#ed_pan_no").focus();

			$("#ed_pan_no").val("");

		}

	}

});

$('.dl_no,.ed_dl_no').on('change', function () {

	var value = $(this).val();

	var maxLength = $(this).attr("maxLength");

	if (value.length > maxLength) {

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

$("#aadharid,#ed_aadharid,#aadhar_no").keyup(function () {

	var value = $(this).val();

	value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join("-");

	$(this).val(value);

});

$('#aadharid,#ed_aadharid').on('change', function () {

	var value = $(this).val();

	var maxLength = $(this).attr("maxLength");

	if (value.length != maxLength) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Enter The Valid Aadhar No.." });

		$("#aadharid").focus();

		$("#aadharid").val("");

		$("#ed_aadharid").focus();

		$("#ed_aadharid").val("");

	}

});

/*Customer Kyc*/

$(document).on('change', '.cat_gwt', function (e) {

	let curRow = $(this).closest("tr");

	set_tagging_wastage_and_mc(curRow);

});

function set_tagging_wastage_and_mc(curRow) {

	curRow.find('.cat_wastage').val(0);

	curRow.find('.cat_mc').val(0);

	//	curRow.find('.mc_type').val(0);

	if (curRow.find('.cat_pro_id').val() == '' || curRow.find('.cat_pro_id').val() == null) {

		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});

	} else if (curRow.find('.cat_des_id ').val() == '' || curRow.find('.cat_des_id ').val() == null) {

		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Design"});

	} else if (curRow.find('.cat_sub_design  ').val() == '' || curRow.find('.cat_sub_design  ').val() == null) {

		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});

	}

	else {

		$.each(wast_settings_details, function (key, items) {

			if ((items.id_product == curRow.find('.cat_pro_id').val()) && (items.id_design == curRow.find('.cat_des_id ').val()) && (items.id_sub_design == curRow.find('.cat_sub_design  ').val())) {

				if (items.wastage_type == 1)//Fixed

				{

					curRow.find('.cat_wastage').val(items.wastag_value);

					curRow.find('.cat_act_va_per').val(items.wastag_value);

					curRow.find('.cat_mc').val(items.mc_cal_value);

					curRow.find('.nn_cat_mc').val(items.mc_cal_value);

					curRow.find('.mc_type').val(items.mc_cal_type);

					curRow.find('.id_mc_type').val(items.mc_cal_type);

					calculateSaleValue();

				}

				else if (items.wastage_type == 2)//Flexiable

				{

					$.each(items.weight_range_det, function (i, result) {

						if ((parseFloat(result.wc_from_weight) <= parseFloat(curRow.find('.cat_gwt ').val())) && (parseFloat(curRow.find('.cat_gwt ').val()) <= parseFloat(result.wc_to_weight))) {

							curRow.find('.cat_wastage').val(result.wc_percent);

							curRow.find('.cat_act_va_per').val(result.wc_percent);

							curRow.find('.cat_mc').val(result.mc);

							curRow.find('.nn_cat_mc').val(result.mc);

							curRow.find('.mc_type').val(items.mc_cal_type);

							curRow.find('.id_mc_type').val(items.mc_cal_type);

							calculateSaleValue();

						}

					});

				}

			}

		});

	}

}

$(document).on('change', '.cus_gwt', function (e) {

	let curRow = $(this).closest("tr");

	set_tagging_wastage_and_mc_home_bill(curRow);

});

function set_tagging_wastage_and_mc_home_bill(curRow) {

	curRow.find('.cus_wastage').val(0);

	curRow.find('.cus_mc').val(0);

	//	curRow.find('.mc_type').val(0);

	if (curRow.find('.cus_product ').val() == '' || curRow.find('.cus_product ').val() == null) {

		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});

	} else if (curRow.find('.cus_design  ').val() == '' || curRow.find('.cus_design  ').val() == null) {

		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Design"});

	} else if (curRow.find('.cus_sub_design  ').val() == '' || curRow.find('.cus_sub_design  ').val() == null) {

		//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});

	}

	else {

		$.each(wast_settings_details, function (key, items) {

			if ((items.id_product == curRow.find('.cus_product ').val()) && (items.id_design == curRow.find('.cus_design  ').val()) && (items.id_sub_design == curRow.find('.cus_sub_design  ').val())) {

				if (items.wastage_type == 1)//Fixed

				{

					curRow.find('.cus_wastage').val(items.wastag_value);

					curRow.find('.cus_act_va_per').val(items.wastag_value);

					curRow.find('.cus_mc').val(items.mc_cal_value);

					curRow.find('.HB_cus_mc').val(items.mc_cal_value);

					curRow.find('.cus_mc_type').val(items.mc_cal_type);

					curRow.find('.id_mc_type').val(items.mc_cal_type);

					calculateCustomItemSaleValue();

				}

				else if (items.wastage_type == 2)//Flexiable

				{

					$.each(items.weight_range_det, function (i, result) {

						if ((parseFloat(result.wc_from_weight) <= parseFloat(curRow.find('.cus_gwt ').val())) && (parseFloat(curRow.find('.cus_gwt ').val()) <= parseFloat(result.wc_to_weight))) {

							curRow.find('.cus_wastage').val(result.wc_percent);

							curRow.find('.cus_act_va_per').val(result.wc_percent);

							curRow.find('.cus_mc').val(result.mc);

							curRow.find('.HB_cus_mc').val(result.mc);

							curRow.find('.cus_mc_type').val(items.mc_cal_type);

							curRow.find('.id_mc_type').val(items.mc_cal_type);

							calculateCustomItemSaleValue();

						}

					});

				}

			}

		});

	}

}

$(document).on('change', ".piece", function () {

	var act_piece = parseFloat($(this).closest('tr').find('.act_piece').val());

	var piece = parseFloat($(this).closest('tr').find('.piece').val());

	if (parseFloat(act_piece) > parseFloat(piece)) {

		var row = $(this).closest('tr');

	}

	else {

		alert('Maximum Piece Exceed');

		$(this).closest('tr').find('.piece').val(act_piece);

		$(this).closest('tr').find(".piece").focus();

	}

	calculatetag_SaleValue();

});

/*Loose Stone Product rate Settings*/

function getLooseStoneProductRateSettings()

{

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/admin_ret_catalog/getLooseStoneProductRateSettings',

		dataType:'json',

		success:function(data)

			{

				loose_product_rate =data;

				$(".overlay").css("display", "none");

			}

		});

}

function check_min_max_rate(row)

{

	let status = true;

	let message = "";

	let rate_per_grm = 0;

	var rate = row.find('.market_rate_value').val();

	$.each(loose_product_rate,function(key,items)

	{

		var product_centwt = 0;

		if(row.find('.stone_type').val()==2) // for diamond products

		{

			var pcs  = (isNaN(row.find('.piece').val()) || row.find('.piece').val() == '')  ? 0 : parseInt(row.find('.piece').val());

			var grs_wt  = (isNaN(row.find('.gwt').val()) || row.find('.gwt').val() == '')  ? 0 : parseFloat(row.find('.gwt').val());

			product_centwt = parseFloat(((grs_wt)/(pcs))*100).toFixed(3);

		}

		if($('#branch_select').val() == items.id_branch && row.find('.pro_id').val() == items.id_product && row.find('.design_id').val() == items.id_design && row.find('.id_sub_design').val()==items.id_sub_design && row.find('.quality_id').val()==items.quality_id && row.find('.uom_id').val()==items.uom_id)

		{

			if(product_centwt > 0)

			{

				if(product_centwt >= parseFloat(items.from_cent) && product_centwt <= parseFloat(items.to_cent))

				{

					if(rate>=parseFloat(items.min_rate) && rate<=parseFloat(items.max_rate))

					{

						rate_per_grm = rate;

					}

					else

					{

						rate_per_grm = items.max_rate;

						status = false;

						message = 'Entered Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !';

					}

				}

			}

			else if(rate>=parseFloat(items.min_rate) && rate<=parseFloat(items.max_rate))

			{

				rate_per_grm = rate;

			}

			else

			{

				rate_per_grm = items.max_rate;

				status = false;

				message = 'Entered Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !';

			}

		}

	});

	return { status, message, rate_per_grm };

}

//Tag Merge Modal

function create_new_empty_est_tag_merge_item(curRow, id) {

	if (curRow.find('.item_emp_id ').val() == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select Employee' });

	} else {

		$('#tag_merge_modal .modal-body').find('#estimation_tag_merge_item_details tbody').empty();

		if (curRow != undefined) {

			$('#active_id').val(curRow.closest('tr').attr('id'));//parent row id

		}

		var catRow = $('#active_id').val();

		var child_tag_details = $('#' + catRow).find('#child_tag_details').val();

		var row = "";

		var rowId = new Date().getTime();

		if (child_tag_details != '' && child_tag_details != '[]' && curRow != undefined) {

			var data = JSON.parse(child_tag_details);

			console.log(data);

			$.each(data, function (pkey, pitem) {

				var stone_details = [];

				if (pitem.stone_details != '' && pitem.stone_details != undefined) {

					$.each(pitem.stone_details, function (skey, sitem) {

						stone_details.push({

							"show_in_lwt": sitem.show_in_lwt,

							"stone_id": sitem.stone_id,

							"stones_type": sitem.stones_type,

							"stone_pcs": sitem.stone_pcs,

							"stone_wt": sitem.stone_wt,

							"stone_price": sitem.stone_price,

							"stone_rate": sitem.stone_rate,

							"stone_uom_id": sitem.stone_uom_id,

							"stone_cal_type": sitem.stone_cal_type

						});

					});

				}

				console.log(pitem);

				row += '<tr id="' + rowId + '">'

					+ '<td style="width:150px"><input class="est_tag_name form-control" type="text" name="est_tag[tag_name][]" value="' + pitem['label'] + '" placeholder="Enter tag code" required autocomplete="off" readonly/><input class="id_mapping_details" type="hidden" name="est_tag[id_mapping_details][]" value="' + pitem['id_mapping_details'] + '" /><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + pitem['tag_id'] + ' /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + pitem['id_orderdetails'] + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + pitem['order_no'] + '"/><input class="rate_field" type="hidden"  value="' + pitem['rate_field'] + '"/><input class="market_rate_field" type="hidden"  value="' + pitem['market_rate_field'] + '"/><input class="parent_tag_id" type="hidden" name="est_tag[product_tag_id][]" value="' + pitem['parent_tag_id'] + '"/></td>'

					+ '<input type="checkbox" class="partial" ><input type="hidden" class="is_partial"  name="est_tag[is_partial][]" value="' + pitem['is_partial'] + '" >'

					+ '<td><div class="prodct_name" >' + pitem['prodct_name'] + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + pitem['pro_id'] + ' /><input type="hidden" class="metal_type" value=' + pitem['metal_type'] + '><input type="hidden" class="scheme_closure_benefit" value=' + pitem['scheme_closure_benefit'] + '><input type="hidden" class="prod_name" value="' + pitem['prodct_name'] + '"/></td>'

					+ '<td><div class="design_name">' + pitem['design_name'] + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + pitem['design_id'] + ' /><input type="hidden" class="des_name" value="' + pitem['design_name'] + '"/></td>'

					+ '<td><div class="sub_design_name">' + pitem['sub_des_name'] + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value=' + pitem['id_sub_design'] + ' /><input type="hidden" class="sub_des_name" value="' + pitem['sub_des_name'] + '"/></td>'

					+ '<div class="section_name">' + pitem.section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + pitem.id_section + ' /><input type="hidden" class="section_name" value="'+pitem.section_name+'"/>'

					+ '<div class="huid">' + pitem.huid + '</div><input type="hidden" class="huid" value="'+pitem.huid+'">'

					+ '<div class="purity">' + pitem['purname'] + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + pitem['purity'] + ' /><input type="hidden" class="purname" value=' + pitem['purname'] + '>'

					+ 'div class="sizes">' + pitem['size_name'] + '</div><input type="hidden" class="size" name="est_tag[size][]"  value="' + pitem['size'] + '" /><input type="hidden" class="size_name" name="est_tag[size_name][]"  value="' + pitem['size_name'] + '" />'

					+ '<td><div class="pieces">' + pitem['piece'] + '</div><input type="hidden" class="piece" name="est_tag[piece][]" value=' + pitem['piece'] + ' /></td>'

					+ '<td><input type="text" class="gwt form-control" name="est_tag[gwt][]" step="any" value=' + pitem['gwt'] + ' readonly/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + pitem['cur_gwt'] + ' /><input type="hidden" class="act_gwt" value=' + pitem['act_gwt'] + ' /></td>'

					+ '<td><input type="text" class="lwt form-control" name="est_tag[lwt][]" step="any" value=' + pitem['lwt'] + ' readonly/></td>'

					+ '<td><div>' + pitem['tot_nwt'] + '</div><input type="hidden" class="tot_nwt" name="est_tag[nwt][]" value=' + pitem['tot_nwt'] + ' /><input type="hidden" class="tot_tag_nwt"  value=' + pitem['tot_tag_nwt'] + ' /><input type="hidden" class="nwt" name="est_tag[nwt][]"  value=' + pitem['tot_tag_nwt'] + '/></td>'

					+ '<input type="hidden" class="market_rate_value" name="est_tag[est_rate_per_grm][]" value="' + pitem['est_rate_per_grm'] + '" />'

					+ '<input class="wastage" type="hidden" value="' + pitem['wastage'] + '"/>'

					+ '<input type="number" name="est_tag[est_wastage_wt][]" class="est_wastage_wt" value="" /><input type="hidden" class="act_wast_wt" value="' + pitem['act_wast_wt'] + '">'

					//+'<td><div class="mc">'+pitem.tag_mc_value+'</div></td>'

					+ '<input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value=' + pitem['discount_amount'] + ' ><input type="hidden" class="mc_val" value="' + pitem['mc'] + '"/>'

					+ '<a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" class="stone_price" name="est_tag[stone_price][]" value=' + pitem['stone_price'] + '>'

					+ '<div class="cost">' + (pitem['cost']) + '</div><input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (pitem['cost']) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + pitem.item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + pitem['caltype'] + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + pitem['tgi_calculation'] + ' /><input type="hidden" class="tax_group_id" value="' + pitem['tax_group_id'] + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + pitem['tax_percentage'] + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + pitem['stone_price'] + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + pitem['certification_cost'] + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + pitem['id_mc_type'] + ' /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value=' + pitem['tax_price'] + ' ><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]" value=' + pitem['market_rate_cost'] + '><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]" value=' + pitem['market_rate_tax'] + '><input type="hidden" class="act_sales_value" value=' + (pitem['act_sales_value']) + '><input type="hidden" class="charge_value" value=' + pitem['charge_value'] + ' name="est_tag[charge_value][]"><input type="hidden" class="sales_mode" value=' + pitem['sales_mode'] + ' ><input type="hidden" class="purchase_cost" value=' + pitem['tag_purchase_cost'] + ' name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" name="est_tag[pur_mc][]" value=' + pitem['pur_mc'] + '><input type="hidden" class="pur_va"  name="est_tag[pur_va][]" value=' + pitem['pur_va'] + ' ><input type="hidden" id="child_tag_id"/><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value=' + pitem['other_metal_details'] + '><input type="hidden" class="rowId" value="' + rowId + '"/><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" value=' + pitem['tag_other_itm_amount'] + '>'

					+ '<td><a href="#" onClick="remove_tag_merge_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

					+ '</tr>';

			});

		}

		$('#tag_merge_modal .modal-body').find('#estimation_tag_merge_item_details tbody').append(row);

		$('#tag_merge_modal').modal('show');

	}

}

$('#tag_merge_modal .modal-body #create_tag_merge_details').on('click', function () {

	create_new_empty_est_tag_merge_item();

});

function get_tag_merge_data() {

	// $(".overlay").css('display', 'block');

	var tagData = $('#est_tag_merge_scan').val();

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

			searchTxt = $('#est_tag_merge_scan').val();

			type = "tag_code";

		}

		if (searchTxt != "") {

			if ($("#branch_settings").val() == 1) {

				if ($("#id_branch").val() != "") {

					tag_search = true;

				} else {

					tag_search = false;

					$('#est_tag_merge_scan').val("");

				}

			} else {

				tag_search = true;

			}

		}

	} else {

		var tagData = $.trim($('#est_tag_merge_barcode_scan').val().replaceAll(' ', ''));

		var istagId = (tagData.search("/") > 0 ? true : false);

		//var isTagCode = (tagData.search("-") > 0 ? true : false);

		var isTagCode = true;

		if (istagId) {

			var tId = tagData.split("/");

			searchTxt = (tId.length >= 2 ? tId[0] : "");

			type = "tag_id";

		}

		else if (isTagCode) {

			searchTxt = $('#est_tag_merge_barcode_scan').val().replaceAll(' ', '');

			type = "old_tag_id";

		}

		if (searchTxt != "") {

			if ($("#branch_settings").val() == 1) {

				if ($("#id_branch").val() != "") {

					tag_search = true;

				} else {

					tag_search = false;

					$('#est_tag_merge_barcode_scan').val("");

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

			url: base_url + 'index.php/admin_ret_estimation/getTaggingScanBySearch/?nocache=' + my_Date.getUTCSeconds(),

			dataType: 'json',

			data: { 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val() },

			success: function (data) {

				var catRow = $('#active_id').val();

				var parent_tag_id = $('#' + catRow).find('.est_tag_id').val();

				var child_item_emp_id = $('#' + catRow).find('.item_emp_id').val();

				var data=data.responseData;

				if (data.length <= 0) {

					get_tag_status_details({ 'searchTxt': searchTxt, 'searchField': type, 'id_branch': $("#id_branch").val() });

				}

				if (data != null && data.length > 0) {

					rowExist = false;

					if ($('#estimation_tag_merge_item_details > tbody').length > 0) {

						$('#estimation_tag_merge_item_details > tbody > tr').each(function (idx, row) {

							//rowExist=false;

							if (data[0].tag_id == $(this).find('.est_tag_id').val()) {

								$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists..' });

								rowExist = true;

							}

							if (data[0].tax_group_id != $(this).find('.tax_group_id').val()) {

								$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Add The Same Tax Items ..' });

								rowExist = true;

							}

							if (data[0].metal_type != $(this).find('.metal_type').val()) {

								$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Add The Same Metal Items ..' });

								rowExist = true;

							}

						});

					}

					if ($('#estimation_tag_details > tbody').length > 0) {

						$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

							//rowExist=false;

							if (data[0].tag_id == $(this).find('.est_tag_id').val()) {

								$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Tag Already Exists in Primary Tag..' });

								rowExist = true;

							}

							if (data[0].tax_group_id != $(this).find('.tax_group_id').val()) {

								$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Add The Same Tax Items ..' });

								rowExist = true;

							}

							if (data[0].metal_type != $(this).find('.metal_type').val()) {

								$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Add The Same Metal Items ..' });

								rowExist = true;

							}

						});

					}

					if (!rowExist) {

						var row = "";

						var stone_details = [];

						var other_metal_details = [];

						var tag_other_itm_amount = 0;

						let pur_mc = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].mc_value >= 0) || data[0].po_details[0].mc_value == null ? 0 : data[0].po_details[0].mc_value) : 0;

						let pur_va = typeof data[0].po_details != 'undefined' && data[0].po_details != null && data[0].po_details.length != 0 ? (!(data[0].po_details[0].item_wastage >= 0) || data[0].po_details[0].item_wastage == null ? 0 : data[0].po_details[0].item_wastage) : 0;

						let rate_field = data[0].rate_field;

						let rate_per_grm = 0;

						if (rate_field != '') {

							rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

						}

						var wast_wgt = parseFloat(parseFloat(data[0].net_wt) * parseFloat(data[0].retail_max_wastage_percent / 100)).toFixed(3);

						$.each(data[0].stone_details, function (skey, sitem) {

							stone_details.push({ "show_in_lwt": sitem.is_apply_in_lwt, "stone_id": sitem.stone_id, "stones_type": sitem.stone_type, "stone_pcs": sitem.pieces, "stone_wt": sitem.wt, "stone_price": sitem.amount, "stone_rate": sitem.rate_per_gram, "stone_uom_id": sitem.uom_id, "stone_cal_type": sitem.stone_cal_type });

						});

						var total = 0;

						var rowId = new Date().getTime();

						row += '<tr id="' + rowId + '">'

							+ '<td style="width:150px"><input class="est_tag_name form-control" type="text" name="est_tag[tag_name][]" value=' + data[0].label + ' placeholder="Enter tag code" required autocomplete="off" readonly/><input class="id_mapping_details" type="hidden" name="est_tag[id_mapping_details][]" value="' + data[0].id_mapping_details + '" /><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + data[0].tag_id + ' /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + data[0].id_orderdetails + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + data[0].order_no + '"/><input class="rate_field" type="hidden"  value="' + data[0].rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + data[0].market_rate_field + '"/><input  name="est_tag[parent_tag_id][]"type="hidden" class="parent_tag_id" value="' + parent_tag_id + '"/></td>'

							+ '<input type="hidden" class="child_item_emp_id" value="' + child_item_emp_id + '">'

							+ '<input type="checkbox" class="partial" ' + (data[0].calculation_based_on == 3 ? 'disabled' : '') + ' ><input type="hidden" class="is_partial"  name="est_tag[is_partial][]">'

							+ '<td><div class="prodct_name">' + data[0].product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + data[0].lot_product + ' /><input type="hidden" class="metal_type" value=' + data[0].metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + data[0].scheme_closure_benefit + '><input type="hidden" class="prod_name" value="' + data[0].product_name + '"/></td>'

							+ '<td><div class="design_name">' + data[0].design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + data[0].design_id + ' /><input type="hidden" class="des_name" value="' + data[0].design_name + '"/></td>'

							+ '<td><div class="sub_design_name">' + data[0].sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value=' + data[0].id_sub_design + ' /><input type="hidden" class="sub_des_name" value="' + data[0].sub_design_name + '"/></td>'

							+ '<div class="section_name">' + data[0].section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + data[0].id_section + ' /><input type="hidden" class="section_name" value="'+data[0].section_name+'"/>'

							+ '<div class="huid">' + data[0].huid + '</div><input type="hidden" class="huid" value="'+data[0].huid+'"/>'

							+ '<div class="purity">' + data[0].purname + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + data[0].purity + ' /><input class="purname" type="hidden" value=' + data[0].purname + '>'

							+ 'div class="sizes">' + data[0].size_name + '</div><input type="hidden" class="size" name="est_tag[size][]"  value="' + data[0].size + '" /><input type="hidden" class="size_name" name="est_tag[size_name][]"  value="' + data[0].size_name + '" />'

							+ '<td><div class="pieces">' + data[0].piece + '</div><input type="hidden" class="piece" name="est_tag[piece][]" value=' + data[0].piece + ' /></td>'

							+ '<td><input type="text" class="gwt form-control" name="est_tag[gwt][]" step="any" value=' + data[0].gross_wt + ' readonly/><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + data[0].gross_wt + ' /><input type="hidden" class="act_gwt" value=' + data[0].gross_wt + ' /></td>'

							+ '<td><input type="text" class="lwt form-control" name="est_tag[lwt][]" step="any" value=' + data[0].less_wt + ' readonly/></td>'

							+ '<td><div class="nwt">' + data[0].net_wt + '</div><input type="hidden" class="tot_nwt" name="est_tag[nwt][]" value=' + data[0].net_wt + ' /><input type="hidden" class="tot_tag_nwt"  value=' + data[0].net_wt + ' /></td><input type="hidden" class="market_rate_value" name="est_tag[market_rate_value][]" value="' + rate_per_grm + '" /><input type="hidden" name="est_tag[wastage][]" class="wastage" value=' + data[0].retail_max_wastage_percent + ' /><input type="hidden" name="est_tag[est_wastage_wt][]" class="act_wast_wt" value=' + wast_wgt + ' /><input type="hidden" class="est_mc_type" value="' + data[0].tag_mc_type + '"><input type="hidden" name="est_tag[wastage][]" class="wastage_max_per" value=' + data[0].retail_max_wastage_percent + ' />'

							+ '<div class="act_wast_wt" style="display:none">' + wast_wgt + '</div><input type="hidden" name="est_tag[est_wastage_wt][]" class="est_wastage_wt" value="" />'

							// + '<select class="form-control est_mc_type" style="width:80px;"><option value="2" ' + (data[0].tag_mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (data[0].tag_mc_type == 1 ? 'selected' : '') + '>Piece</option></select>'

							+ '<input type="hidden" class="mc_val" name="est_tag[mc][]"  value=' + data[0].tag_mc_value + ' /><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" />'

							+ '<a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '>'

							+ '<div class="cost">' + (data[0].sales_value) + '</div><input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (data[0].sales_value) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + data[0].item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + data[0].calculation_based_on + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + data[0].tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + data[0].tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + data[0].tax_percentage + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + data[0].stone_price + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + data[0].certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + data[0].tag_mc_type + ' /><input class="mc_value" type="hidden"  value=' + data[0].tag_mc_value + ' /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="act_sales_value" value=' + (data[0].sales_value) + '><input type="hidden" class="charge_value" value=' + data[0].charge_value + ' name="est_tag[charge_value][]"><input type="hidden" class="sales_mode" value=' + data[0].sales_mode + ' ><input type="hidden" class="purchase_cost" value=' + data[0].tag_purchase_cost + ' name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_tag[pur_va][]" ><input type="hidden" id="child_tag_id"/><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value=' + JSON.stringify(other_metal_details) + '><input type="hidden" class="tag_other_itm_amount" name="est_tag[tag_other_itm_amount][]" value="' + tag_other_itm_amount + '"><input type="hidden" class="rowId" value="' + rowId + '"/>'

							+ '<td><a href="#" onClick="remove_tag_merge_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

							+ '</tr>';

						$('#estimation_tag_merge_item_details tbody').append(row);

					}

					$('#est_tag_merge_scan').val('');

					$('#est_tag_merge_barcode_scan').val('');

					calculate_tag_merge_data();

					calculatetag_SaleValue();

				}

			}

		});

	}

}

function remove_tag_merge_row(curRow) {

	curRow.remove();

	calculate_tag_merge_data();

}

function calculate_tag_merge_data() {

	$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

		var total_grs_wt = 0;

		var total_less_wt = 0;

		var total_net_wt = 0;

		$('#estimation_tag_merge_item_details > tbody > tr').each(function (idx, row) {

			total_grs_wt += parseFloat((isNaN($(this).find('.gwt').val()) || $(this).find('.gwt').val() == '') ? 0 : $(this).find('.gwt').val());

			total_less_wt += parseFloat((isNaN($(this).find('.lwt').val()) || $(this).find('.lwt').val() == '') ? 0 : $(this).find('.lwt').val());

			total_net_wt += parseFloat((isNaN($(this).find('.tot_nwt').val()) || $(this).find('.tot_nwt').val() == '') ? 0 : $(this).find('.tot_nwt').val());

			$('.tot_tag_merge_gwt').html(parseFloat(total_grs_wt).toFixed(3));

			$('.tot_tag_merge_lwt').html(parseFloat(total_less_wt).toFixed(3));

			$('.tot_tag_merge_nwt').html(parseFloat(total_net_wt).toFixed(3));

		});

	});

}

$('#add_tag_merged_data').on('click', function () {

	var total_grs_wt = 0;

	var total_net_wt = 0;

	var total_less_wt = 0;

	var child_tag_id = 0;

	var child_tag_details = [];

	var stone_data = '';

	var catRow = $('#active_id').val();

	var parent_tag_id = $('#' + catRow).find('.est_tag_id').val();

	var item_emp_id = $('#' + catRow).find('.item_emp_id').val();

	$('#estimation_tag_details > tbody  > tr').each(function (index, tr) {

		curRow = $(this);

		if (parent_tag_id == curRow.find('.parent_tag_id').val()) {

			var cur_id = curRow.attr('id');

			$('#estimation_tag_details > tbody  > #' + cur_id + '').remove();

		}

	});

	var stone_data = [];

	if ($('#estimation_tag_merge_item_details > tbody  > tr').length > 0) {

		$('#estimation_tag_merge_item_details > tbody  > tr').each(function (index, tr) {

			if ($(this).find(".stone_details").val() != '') {

				stone_data = $(this).find(".stone_details").val();

				var stone_details = JSON.parse(stone_data);

			}

			child_tag_details.push({

				"act_gwt": $(this).find(".act_gwt").val(),

				"act_sales_value": $(this).find(".act_sales_value").val(),

				"act_wast_wt": $(this).find(".act_wast_wt").val(),

				"caltype": $(this).find(".caltype").val(),

				"certification_cost": $(this).find(".certification_price").val(),

				"charge_value": $(this).find(".charge_value").val(),

				"child_tag_id": $(this).find(".child_tag_id").val(),

				"cur_gwt": $(this).find(".cur_gwt").val(),

				"design_id": $(this).find(".design_id").val(),

				"design_name": $(this).find(".des_name").val(),

				"discount_amount": $(this).find(".discount_amount").val(),

				"est_mc_type": $(this).find(".est_mc_type").val(),

				"est_rate_per_grm": $(this).find(".market_rate_value").val(),

				"tag_id": $(this).find(".est_tag_id").val(),

				"label": $(this).find(".est_tag_name").val(),

				"est_wastage_wt": $(this).find(".est_wastage_wt").val(),

				"gwt": $(this).find(".gwt").val(),

				"id_mapping_details": $(this).find(".id_mapping_details").val(),

				"id_mc_type": $(this).find(".id_mc_type").val(),

				"id_orderdetails": $(this).find(".id_orderdetails").val(),

				"id_sub_design": $(this).find(".id_sub_design").val(),

				"item_rate": $(this).find(".tag_item_rate").val(),

				"lwt": $(this).find(".lwt").val(),

				"market_rate_cost": $(this).find(".market_rate_cost").val(),

				"market_rate_field": $(this).find(".market_rate_field").val(),

				"market_rate_tax": $(this).find(".market_rate_tax").val(),

				"market_rate_value": $(this).find(".market_rate_value").val(),

				"mc": $(this).find(".mc_val").val(),

				"metal_type": $(this).find(".metal_type").val(),

				"nwt": $(this).find(".tot_nwt").val(),

				"order_no": $(this).find(".orderno").val(),

				"other_metal_details": $(this).find(".other_metal_details").val(),

				"parent_tag_id": $(this).find(".parent_tag_id").val(),

				"pur_name": $(this).find(".pur_name").val(),

				"purname": $(this).find(".purname").val(),

				"partial": $(this).find(".partial").val(),

				"piece": $(this).find(".piece").val(),

				"pro_id": $(this).find(".pro_id").val(),

				"prodct_name": $(this).find(".prod_name").val(),

				"purity": $(this).find(".purity").val(),

				"tag_purchase_cost": $(this).find(".purchase_cost").val(),

				"pur_mc": $(this).find(".pur_mc").val(),

				"pur_va": $(this).find(".pur_va").val(),

				"rowId": $(this).find(".rowId").val(),

				"rate_field": $(this).find(".rate_field").val(),

				"sales_mode": $(this).find(".sales_mode").val(),

				"cost": $(this).find(".sales_value").val(),

				"scheme_closure_benefit": $(this).find(".scheme_closure_benefit").val(),

				"size": $(this).find(".size").val(),

				"size_name": $(this).find(".size_name").val(),

				"stone_details": stone_details,

				"stone_price": $(this).find(".stone_price").val(),

				"sub_des_name": $(this).find(".sub_des_name").val(),

				"sub_design_name": $(this).find(".sub_des_name").val(),

				"tag_other_itm_amount": $(this).find(".tag_other_itm_amount").val(),

				"tax_group_id": $(this).find(".tax_group_id").val(),

				"tax_percentage": $(this).find(".tax_percentage").val(),

				"tax_price": $(this).find(".tax_price").val(),

				"tgi_calculation": $(this).find(".tgi_calculation").val(),

				"tot_nwt": $(this).find(".tot_nwt").val(),

				"tot_tag_nwt": $(this).find(".tot_tag_nwt").val(),

				"wastage": $(this).find(".wastage").val(),

				"retail_max_wastage_percent": $(this).find(".wastage_max_per").val(),

				"id_section": $(this).find(".id_section").val(),

				"section_name": $(this).find(".section_name").val(),

				"huid": $(this).find(".huid").val(),

				"item_emp_id": item_emp_id,

			});

		});

	}

	var catRow = $('#active_id').val();

	$('#' + catRow).find('#child_tag_details').val(JSON.stringify(child_tag_details));

	set_tag_merge_data();

	$('#tag_merge_modal').modal('toggle');

	calculatetag_SaleValue();

});

function set_tag_merge_data(catRow = "") {

	if (catRow == '') {

		var catRow = $('#active_id').val();

	}

	if (catRow != '') {

		var child_detail = JSON.parse($('#' + catRow).find('#child_tag_details').val());

		console.log(child_detail);

	}

	if (child_detail != '' && child_detail != undefined && child_detail != '[]') {

		$('#it_has_childtag').val(1);

	} else {

		$('#it_has_childtag').val(0);

	}

	var row_merge_data = $('#' + catRow).find('#child_tag_details').val();

	if (row_merge_data != '' && row_merge_data != '[]' && row_merge_data != undefined) {

		var tag_details = JSON.parse(row_merge_data);

		$.each(tag_details, function (pkey, pitem) {

			rowExist = false;

			var wast_percent = 0;

			var act_mc_value = 0;

			$('#estimation_tag_details > tbody  > tr').each(function (index, tr) {

				curRow = $(this);

				if (pitem.est_tag_id == curRow.find('.est_tag_id').val()) {

					rowExist = true;

				}

				if (pitem.parent_tag_id == curRow.find('.est_tag_id').val()) {

					wast_percent = (curRow.find('.wastage_max_per ').val());

					act_mc_value = (curRow.find('.act_mc_value ').val());

					est_mc_type = (curRow.find('.est_mc_type ').val());

					$.each(child_detail, function (idx, row) {

						row.mc = curRow.find('.act_mc_value ').val();

						row.wastage = (curRow.find('.wastage_max_per ').val())

					});

					curRow.find('#child_tag_details').val(JSON.stringify(child_detail));

				}

			});

			let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

			var stones_list = '';

			var stones_type_list = '';

			var uom_list = '';

			var html = '';

			let rate_field = pitem.rate_field;

			let rate_per_grm = 0;

			var stone_details = [];

			var other_metal_details = [];

			var tag_other_itm_amount = 0;

			$.each(pitem.stone_details, function (skey, sitem) {

				stone_details.push({

					"show_in_lwt": sitem.show_in_lwt,

					"stone_id": sitem.stone_id,

					"stones_type": sitem.stones_type,

					"stone_pcs": sitem.stone_pcs,

					"stone_wt": sitem.stone_wt,

					"stone_price": sitem.stone_price,

					"stone_rate": sitem.stone_rate,

					"stone_uom_id": sitem.stone_uom_id,

					"stone_cal_type": sitem.stone_cal_type

				});

			});

			if (rate_field != '') {

				rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

			}

			let pur_mc = typeof pitem.po_details != 'undefined' && pitem.po_details != null && pitem.po_details.length != 0 ? (!(pitem.po_details[0].mc_value >= 0) || pitem.po_details[0].mc_value == null ? 0 : pitem.po_details[0].mc_value) : 0;

			let pur_va = typeof pitem.po_details != 'undefined' && pitem.po_details != null && pitem.po_details.length != 0 ? (!(pitem.po_details[0].item_wastage >= 0) || pitem.po_details[0].item_wastage == null ? 0 : pitem.po_details[0].item_wastage) : 0;

			var wast_wgt = parseFloat(parseFloat(pitem.net_wt) * parseFloat(pitem.retail_max_wastage_percent / 100)).toFixed(3);

			var select_emp = "<option value=''>-Employee-</option>";

			$.each(emp_details, function (pkey, emp) {

				let selectedMetal = "";

				if (pitem.item_emp_id == emp.id_employee) {

					selectedMetal = "selected = 'selected'";

				}

				select_emp += "<option  " + selectedMetal + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

			});

			if (other_metal_details != '' && other_metal_details != '[]' && other_metal_details != undefined) {

				$.each(pitem.other_metal_details, function (skey, sitem) {

					tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

					other_metal_details.push({

						"tag_other_itm_id": sitem.tag_other_itm_id,

						"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

						"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

						"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

						"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

						"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

						"tag_other_itm_uom": sitem.tag_other_itm_uom,

						"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

						"tag_other_itm_mc": sitem.tag_other_itm_mc,

						"tag_other_itm_rate": sitem.tag_other_itm_rate,

						"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

						"tag_other_itm_amount": sitem.tag_other_itm_amount,

						"purname": sitem.purname,

                        "metal_name": sitem.metal_name,

					});

				});

			}

			var tag_image = base_url + 'assets/img/no_image.png';

			// if (pitem.tag_images.length > 0) {

			// 	$.each(data[0].tag_images, function (key, val) {

			// 		if (val.is_default == 1) {

			// 			tag_image = base_url + 'assets/img/tag/' + val.image;

			// 		}

			// 	});

			// }

			if (!rowExist) {

				var row = "";

				var rowId = new Date().getTime();

				row += '<tr id="' + rowId + '">'

					+ '<td><input class="est_tag_name" type="text" name="est_child_tag[tag_name][]" value=' + pitem.label + ' placeholder="Enter tag code" required autocomplete="off" disabled /><input class="id_mapping_details" type="hidden" name="est_child_tag[id_mapping_details][]" value="' + pitem.id_mapping_details + '" /><input class="est_tag_id" type="hidden" name="est_child_tag[tag_id][]" value=' + pitem.tag_id + ' /><input class="id_orderdetails" type="hidden" name="est_child_tag[id_orderdetails][]" value="' + pitem.id_orderdetails + '" /><input class="orderno" type="hidden" name="est_child_tag[orderno][]" value="' + pitem.order_no + '"/><input class="rate_field" type="hidden"  value="' + pitem.rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + pitem.market_rate_field + '"/><input  name="est_child_tag[parent_tag_id][]"type="hidden" class="parent_tag_id" value="' + pitem.parent_tag_id + '"/></td>'

					+ '<td><input type="hidden" id="it_has_childtag" value= "" ></td>'

					+ '<td><img src="' + tag_image + '" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="' + pitem.tag_id + '" id="edit"><i class="fa fa-eye" ></i></a></td>'

					+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_child_tag[item_emp_id][]" disabled>' + select_emp + '</select></td>'

					+ '<td><input type="checkbox" class="partial" ' + (pitem.caltype == 3 ? 'disabled' : '') + ' disabled ><input type="hidden" class="is_partial"  name="est_child_tag[is_partial][]"></td>'

					+ '<td><div class="prodct_name" disabled>' + pitem.prodct_name + '</div><input type="hidden" class="pro_id" name="est_child_tag[pro_id][]" value=' + pitem.pro_id + ' /><input type="hidden" class="metal_type" value=' + pitem.metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + pitem.scheme_closure_benefit + '><input type="hidden" class="prod_name" value="' + pitem.prodct_name + '"/></td>'

					+ '<td><div class="design_name" disabled>' + pitem.design_name + '</div><input type="hidden" class="design_id" name="est_child_tag[design_id][]" value=' + pitem.design_id + ' /></td>'

					+ '<td><div class="sub_design_name" disabled>' + pitem.sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_child_tag[id_sub_design][]" value=' + pitem.id_sub_design + ' /></td>'

					+ '<td><div class="section_name">' + pitem.section_name + '</div><input type="hidden" class="id_section" name="est_child_tag[id_section][]" value=' + pitem.id_section + ' /></td>'

					+ '<td><div class="huid">' + pitem.huid + '</div></td>'

					+ '<td><div class="order_no"></td>'

					+ '<td><div class="purity" disabled>' + pitem.purname + '</div><input type="hidden" class="purity" name="est_child_tag[purity][]" value=' + pitem.purity + ' /></td>'

					+ '<td><div class="sizes" disabled>' + pitem.size_name + '</div><input type="hidden" class="size" name="est_child_tag[size][]"  value="' + pitem.size + '" /></td>'

					+ '<td><div class="pieces" disabled>' + pitem.piece + '</div><input type="hidden" class="piece" name="est_child_tag[piece][]" value=' + pitem.piece + ' /></td>'

					+ '<td><div class="tagmerge_gwt" style="display:none">' + pitem.gwt + '</div><input type="text" class="gwt form-control" name="est_child_tag[gwt][]" step="any" value=' + pitem.gwt + ' readonly/><input type="hidden" class="cur_gwt" name="est_child_tag[cur_gwt][]" value=' + pitem.gwt + ' /><input type="hidden" class="act_gwt" value=' + pitem.gwt + ' /></td>'

					+ '<td><div class="form-group"><div class="input-group "><input type="text" class="lwt form-control" name="est_child_tag[lwt][]" step="any" value=' + pitem.lwt + '   onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" readonly/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" name="est_child_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '></div></div></td>'

					// + '<td><div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + data[0].less_wt + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'

					+ '<td><div class="nwt">' + pitem.nwt + '</div><div class="tagmerge_nwt" style="display:none">' + pitem.nwt + '</div><input type="hidden" class="tot_nwt" name="est_child_tag[nwt][]" value=' + pitem.nwt + ' /><input type="hidden" class="tot_tag_nwt"  value=' + pitem.nwt + ' /></td>'

					+ '<td><input type="text" class="market_rate_value form-control" name="est_child_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + pitem.est_rate_per_grm + '" /></td>'

					+ '<td><div class="wastage" style="display:none">' + wast_percent + '</div><input type="number" name="est_child_tag[wastage][]" class="wastage_max_per form-control" value=' + wast_percent + ' disabled /></td>'

					+ '<td><div class="act_wast_wt" style="display:none">' + wast_wgt + '</div><input type="number" name="est_child_tag[est_wastage_wt][]" class="est_wastage_wt form-control" value="" disabled /></td>'

					+ '<td><select class="form-control est_mc_type" style="width:80px;" disabled><option value="2" ' + (est_mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (est_mc_type == 1 ? 'selected' : '') + '>Piece</option></select></td>'

					+ '<td><div class="mc_val" style="display:none">' + act_mc_value + '</div><input class="act_mc_value"  name="est_child_tag[mc][]" type="number" value=' + act_mc_value + ' disabled/><input class="discount_amount"  name="est_child_tag[discount_amount][]" type="hidden" value="" />Tot Mc:<span class="tot_mc" ></span></td>'

					+ '<a href="#" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_child_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '>'

					+ '<td><div class="cost">' + (pitem.cost) + '</div><input class="sales_value" type="hidden" name="est_child_tag[cost][]" value=' + (pitem.cost) + ' /><input class="tag_item_rate" type="hidden" name="est_child_tag[item_rate][]" value=' + pitem.item_rate + ' /><input class="caltype" type="hidden" name="est_child_tag[caltype][]" value=' + pitem.caltype + ' /><input class="tgi_calculation" type="hidden" name="est_child_tag[tgi_calculation][]" value=' + pitem.tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + pitem.tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_child_tag[tax_percentage][]" value=' + pitem.tax_percentage + ' /><input class="stone_price" type="hidden" name="est_child_tag[stone_price][]" value=' + pitem.stone_price + ' /><input class="certification_price" type="hidden" name="est_child_tag[certification_price][]" value=' + pitem.certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_child_tag[id_mc_type][]" value=' + pitem.id_mc_type + ' /><input class="mc_value" type="hidden"  value=' + pitem.tag_mc_value + ' /><input class="tax_price" type="hidden" name="est_child_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_child_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_child_tag[market_rate_tax][]"><input type="hidden" class="act_sales_value" value=' + (pitem.cost) + '><input type="hidden" class="charge_value" value=' + pitem.charge_value + ' name="est_child_tag[charge_value][]"><input type="hidden" class="sales_mode" value=' + pitem.sales_mode + ' ><input type="hidden" class="purchase_cost" value=' + pitem.tag_purchase_cost + ' name="est_child_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_child_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_child_tag[pur_va][]" ><input type="hidden" class="other_metal_details" name="est_child_tag[other_metal_details][]" value=' + JSON.stringify(other_metal_details) + '><input type="hidden" class="tag_other_itm_amount" name="est_child_tag[tag_other_itm_amount][]" value="' + tag_other_itm_amount + '"><input type="hidden" class="sale_taxable_amount" name="est_child_tag[sale_taxable_amount][]"/></td>'

					+ '<td></td>'

					+ '</tr>';

				if (catRow != '') {

					$('#estimation_tag_details #' + catRow + '').after(row); //child details Will append after Parent Row

				} else {

					$('#estimation_tag_details tbody').append(row);

				}

				if (pitem.item_emp_id != '') {

					$('.item_emp_id').select2("val", (pitem.item_emp_id != '' ? pitem.item_emp_id : ''));

				}

				$('#estimation_tag_details > tbody').find('.item_emp_id').select2();

				$('#' + catRow).find('#child_tag_details').val();

			}

		});

	}

}

//Pincode Wise Area

/* $('#add_new_area').click(function (event) {

	if ($('#village').val() == '' || $('#village').val() == null) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Village..' });

		return false;

	}

	else if ($('#new_pincode').val() == '') {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Pincode..' });

		return false;

	}

	else if ($('#new_pincode').val() != '' && ($('#new_pincode').val().length != 6)) {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter the Valid Pincode..' });

		return false;

	}

	add_new_village($('#village').val(), $('#new_pincode').val());

	$('#village').val('');

	$('#new_pincode').val('');

}); */

$('#new_pincode').on('change', function () {

	if (this.value.length != 6) {

		$('#new_pincode').val("");

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid PIN Code' });

	}

});

/* function add_new_village(village, pincode) {

	my_Date = new Date();

	$("div.overlay").css("display", "block");

	$.ajax({

		url: base_url + "index.php/admin_ret_estimation/get_village?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),

		data: { 'village_name': village, 'pincode': pincode },

		type: "POST",

		dataType: "JSON",

		async: false,

		success: function (data) {

			console.log(data);

			if (data.status) {

				var ins_id = data.ins_id;

				var newVillage = village;

				var $newOption = $('<option>', {

					value: ins_id,

					text: newVillage

				});

				$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + data.message });

				if(ins_id!='')

				{

					$('#ed_id_village').val(ins_id);

					$('#id_village').val(ins_id);

				}

				$('#confirm-area').modal('hide');

				$("div.overlay").css("display", "none");

				if($('#ed_cus_pin_code_add').val().length ==6){

					get_villages_by_pincode($('#ed_cus_pin_code_add').val())

				}else if($('#pin_code_add').val().length==6){

					get_villages_by_pincode($('#pin_code_add').val())

				}

			} else {

				$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });

			}

		},

		error: function (error) {

			console.log(error);

			$("div.overlay").css("display", "none");

		}

	});

} */

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

					$('#sel_village').select2("val", (id_village != '' ? id_village : $("#branch_id_village").val()));

				}

				if ($('#ed_sel_village').val().length > 0 ) {

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : $("#branch_id_village").val()));

				}

				if(id_village!=''){

					$('#sel_village').select2("val", (id_village != '' ? id_village : $("#branch_id_village").val()));

				}

				if(ed_id_village!=''){

					$('#ed_sel_village').select2("val", (ed_id_village != '' ? ed_id_village : $("#branch_id_village").val()));

				}

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

$('.new_village_close,.add_new_area').on('click', function () {

	$("body").on("hidden.bs.modal", function () { // to use multiple model in one page

		if ($(".modal.in").length > 0) {

			$("body").addClass("modal-open")

		}

	});

});

/* $(document).on('keyup', '#pin_code_add,#ed_cus_pin_code_add', function () {

	var pin_code = $("#pin_code_add").val() || $('#ed_cus_pin_code_add').val();

	if (pin_code.length == 6) {

		get_villages_by_pincode(pin_code)

	}else{

		$('#id_village').val('');

		$('#ed_id_village').val('');

		$('#sel_village').select2("val",'');

		$('#ed_sel_village').select2("val",'');

		$('#sel_village option').remove();

		$('#ed_sel_village option').remove();

	}

}); */

function getestPurityDetails(metal,cat_id) {

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_purities?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: {'id_metal': metal,'cat_id':cat_id},

		success: function (data) {

			var id = $('.id_purity').val();

				$.each(data, function (key, item) {

					$("#select_purity").append(

						$("<option></option>")

							.attr("value", item.id_purity)

							.text(item.purity)

					);

				});

			$("#select_purity").select2({

				placeholder: "Select Purity",

				allowClear: true

			});

			console.log("id:", id);

			$("#select_purity").select2("val",(id != '' && id > 0 ? id : ''));

			$(".overlay").css("display", "none");

		}

	});

}

// To get purities automatically starts here - By Kanaga Sundar

function getestPurityDetailsCurrow(metal, cat_id, currow) {

	currow.find('.cat_purity option').remove();

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_purities?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		async: false,

		data: { 'id_metal': metal, 'cat_id': cat_id },

		success: function (data) {

			var id = $('.id_purity').val();

			$.each(data, function (key, item) {

				currow.find(".cat_purity").append(

					$("<option></option>")

						.attr("value", item.id_purity)

						.text(item.purity)

				);

			});

			currow.find(".cat_purity").select2({

				placeholder: "Select Purity",

				allowClear: true

			});



		}

	});

}

function getestPurityDetails_currow(metal, cat_id, currow) {

	currow.find('.cus_purity option').remove();

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_purities?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		async: false,

		data: { 'id_metal': metal, 'cat_id': cat_id },

		success: function (data) {

			var id = $('.id_purity').val();

			$.each(data, function (key, item) {

				currow.find(".cus_purity").append(

					$("<option></option>")

						.attr("value", item.id_purity)

						.text(item.purity)

				);

			});

			currow.find(".cus_purity").select2({

				placeholder: "Select Purity",

				allowClear: true

			});



		}

	});

}

// To get purities automatically ends here

/* $('.add_new_village').on('click', function () {

	if ($('#pin_code_add').val().length == 6) {

		$('#confirm-area').modal('show');

		var pin_code = $('#pin_code_add').val();

		$('#new_pincode').val(pin_code);

	} else if ($('#ed_cus_pin_code_add').val().length == 6) {

		$('#confirm-area').modal('show');

		var pin_code = $('#ed_cus_pin_code_add').val();

		$('#new_pincode').val(pin_code);

	} else {

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Enter Valid Pin Code' });

	}

}); */

//Tag Split

function set_tag_split_details_old()

{

	tag_details = [];

	var total_closing_weight = 0;

	$('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

		var curRow = $(this).closest("tr");

		total_closing_weight+=parseFloat((parseFloat(curRow.find('.closing_weight').val())*parseFloat(curRow.find('.bonus_percent').val()))/100);

	});

		var applied_closing_weight = 0;

		var total_balance_applied_weight = 0;



			$.each(tag_split_details,function(key,val){

				var isTagsplitted = false;

				if(tag_details.length> 0)

				{

					$.each(tag_details,function(idx, tag){

						if(tag.value != undefined){

							if(tag.value == val.value){

								isTagsplitted = true;

							}

						}

					});

				}

				if(total_closing_weight >0 && (!isTagsplitted) && (val.id_metal == 1))

				{

					var item_weight = val.gross_wt;

					total_balance_applied_weight = parseFloat(parseFloat(total_closing_weight)-parseFloat(applied_closing_weight)).toFixed(3);

					if((parseFloat(total_balance_applied_weight)<parseFloat(item_weight)) && (total_balance_applied_weight > 0) && (val.calculation_based_on!=3) )

					{

						var item_balance_weight = parseFloat(parseFloat(item_weight)-parseFloat(total_balance_applied_weight)).toFixed(3);

						var split_stn_details = [];

						var split_other_metal_details = [];

						var charges_details	= [];

						var stone_details = val.stone_details;

						var other_metal_details = val.other_metal_details;

						if(stone_details.length>0){

							$.each(stone_details,function(k,val){

								var stn_weight = val.stone_wt;

								if(stn_weight > 0)

								{

									var stn_weight_per = parseFloat((parseFloat(total_balance_applied_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

									stn_weight 	   = parseFloat((parseFloat(stn_weight*stn_weight_per))/100).toFixed(3);

								}



								split_stn_details.push({

									'show_in_lwt':val.show_in_lwt,

									'stone_pcs':val.stone_pcs,

									'stone_rate':val.stone_rate,

									'stone_cal_type':val.stone_cal_type,

									'stone_id':val.stone_id,

									'tag_id':val.tag_id,

									'stone_uom_id':val.stone_uom_id,

									'stone_wt'	:stn_weight,

									"act_stn_pcs": val.act_stn_pcs,

									"act_stone_wt": val.act_stone_wt,

									'stone_price':(val.stone_cal_type ==2 ? parseFloat(parseFloat(val.stone_rate)*parseFloat(val.stone_pcs).toFixed(2)) :parseFloat(parseFloat(stn_weight)*parseFloat(val.stone_rate).toFixed(2))),

								});

							});

							console.log(split_stn_details);

						}

						if(val.charges_details.length > 0){

							$.each(val.charges_details, function (skey, sitem) {

								charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display,"tag_id": sitem.tag_id });

							});

						}



						if(other_metal_details.length > 0)

						{

							$.each(other_metal_details,function(k,val){

								var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

								if(tag_other_itm_grs_weight > 0)

								{

									var other_metal_weight_per = parseFloat((parseFloat(total_balance_applied_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

									tag_other_itm_grs_weight 	   = parseFloat((parseFloat(tag_other_itm_grs_weight)*parseFloat(other_metal_weight_per))/100).toFixed(3);

								}

								split_other_metal_details.push({

									'id_metal':val.id_metal,

									'tag_other_itm_amount':val.tag_other_itm_amount,

									'tag_other_itm_cal_type':val.tag_other_itm_cal_type,

									'tag_other_itm_grs_weight':tag_other_itm_grs_weight,

									'tag_other_itm_id':val.tag_other_itm_id,

									'tag_other_itm_mc':0,

									'tag_other_itm_metal_id':val.tag_other_itm_metal_id,

									'tag_other_itm_pcs':val.tag_other_itm_pcs,

									'tag_other_itm_pur_id':val.tag_other_itm_pur_id,

									'tag_other_itm_rate':val.tag_other_itm_rate,

									'tag_other_itm_tag_id':val.tag_other_itm_tag_id,

									'tag_other_itm_uom':val.tag_other_itm_uom,

									'tag_other_itm_wastage':0,

									"purname": val.purname,

                                    "metal_name": val.metal_name,

								});

							});

						}

						tag_details.push({

							'isTagsplitted'				    :(total_balance_applied_weight > 0 ? 1 :0),

							'gross_wt'						:total_balance_applied_weight,

							'value'							:val.tag_id,

							'item_type'						:0,

							'calculation_based_on'			:val.calculation_based_on,

							'cat_id'						:val.cat_id,

							'certification_cost'			:val.certification_cost,

							'charge_value'					:(key==0 ? val.charge_value : 0 ),

							'charges_details'				:(key==0 ? charges_details : 0 ),

							'cost_center'					:val.cost_center,

							'current_branch'				:val.current_branch,

							'design_id'						:val.design_id,

							'design_name'					:val.design_name,

							'halmarking'					:val.halmarking,

							'huid'							:val.huid,

							'id_mapping_details'			:val.id_mapping_details,

							'id_metal'						:val.id_metal,

							'id_orderdetails'				:val.id_orderdetails,

							'id_section'					:val.id_section,

							'id_sub_design'					:val.id_sub_design,

							'id_tag_mapping'				:val.id_tag_mapping,

							'item_rate'						:val.item_rate,

							'id_sub_design'					:val.id_sub_design,

							'label'							:val.label,

							'less_wt'						:val.less_wt,

							'lot_product'					:val.lot_product,

							'lot_received_at'				:val.lot_received_at,

							'market_rate_field'				:val.market_rate_field,

							'metal_type'					:val.metal_type,

							'net_wt'						:val.net_wt,

							'ord_rate_type'					:val.ord_rate_type,

							'order_no'						:val.order_no,

							'order_rate_per_grm'			:val.order_rate_per_grm,

							'piece'							:val.piece,

							'product_name'					:val.product_name,

							'product_short_code'			:val.product_short_code,

							'purity'						:val.purity,

							'purname'						:val.purname,

							'quality_id'					:val.quality_id,

							'rate_field'					:val.rate_field,

							//'retail_max_wastage_percent'    :val.retail_max_wastage_percent,

							'retail_max_wastage_percent'    :0,

							'sales_mode'					:val.sales_mode,

							'sales_value'					:val.sales_value,

							'scheme_closure_benefit'		:val.scheme_closure_benefit,

							'section_name'					:val.section_name,

							'size'							:val.size,

							'size_name'						:val.size_name,

							'stone_price'					:val.stone_price,

							'stone_type'					:val.stone_type,

							'sub_design_name'				:val.sub_design_name,

							'tag_id'						:val.tag_id,

							'tag_mc_type'					:val.tag_mc_type,

							'tag_mc_value'					:0,

							'tag_other_itm_amount'			:val.tag_other_itm_amount,

							'tag_purchase_cost'				:val.tag_purchase_cost,

							'tag_status'					:val.tag_status,

							'tax_group_id'					:val.tax_group_id,

							'tax_percentage'				:val.tax_percentage,

							'tgi_calculation'				:val.tgi_calculation,

							'uom'							:val.uom,

							'uom_id'						:val.uom_id,

							'stone_details'					:split_stn_details,

							'other_metal_details'			:split_other_metal_details,

							'tag_images'					:[],

						});

						applied_closing_weight+=parseFloat(item_weight);

						if(item_balance_weight>0)

						{

							var split_stn_details = [];

							var split_other_metal_details = [];

							var stone_details = val.stone_details;

							var other_metal_details = val.other_metal_details;

							if(stone_details.length>0){

								$.each(stone_details,function(k,val){

									var stn_weight = val.stone_wt;

									if(stn_weight > 0)

									{

										var stn_weight_per = parseFloat((parseFloat(item_balance_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

										stn_weight 	   = parseFloat((parseFloat(stn_weight)*parseFloat(stn_weight_per))/100).toFixed(3);

									}

									split_stn_details.push({

										'show_in_lwt':val.show_in_lwt,

										'stone_pcs':0,

										'stone_rate':val.stone_rate,

										'stone_cal_type':val.stone_cal_type,

										'stone_id':val.stone_id,

										'tag_id':val.tag_id,

										'stone_uom_id':val.stone_uom_id,

										'stone_wt'	:stn_weight,

										"act_stn_pcs": val.act_stn_pcs,

									    "act_stone_wt": val.act_stone_wt,

										'stone_price':(val.stone_cal_type ==2 ? 0 :parseFloat(parseFloat(stn_weight)*parseFloat(val.stone_rate).toFixed(2))),

									});

								});

								console.log(split_stn_details);

							}

							if(other_metal_details.length > 0)

							{

								$.each(other_metal_details,function(k,val){

									var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

									if(tag_other_itm_grs_weight > 0)

									{

										var other_metal_weight_per = parseFloat((parseFloat(item_balance_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

										tag_other_itm_grs_weight 	   = parseFloat((parseFloat(tag_other_itm_grs_weight)*parseFloat(other_metal_weight_per))/100).toFixed(3);

									}

									split_other_metal_details.push({

										'id_metal':val.id_metal,

										'tag_other_itm_amount':val.tag_other_itm_amount,

										'tag_other_itm_cal_type':val.tag_other_itm_cal_type,

										'tag_other_itm_grs_weight':tag_other_itm_grs_weight,

										'tag_other_itm_id':val.tag_other_itm_id,

										'tag_other_itm_mc':val.tag_other_itm_mc,

										'tag_other_itm_metal_id':val.tag_other_itm_metal_id,

										'tag_other_itm_pcs':val.tag_other_itm_pcs,

										'tag_other_itm_pur_id':val.tag_other_itm_pur_id,

										'tag_other_itm_rate':val.tag_other_itm_rate,

										'tag_other_itm_tag_id':val.tag_other_itm_tag_id,

										'tag_other_itm_uom':val.tag_other_itm_uom,

										'tag_other_itm_wastage':val.tag_other_itm_wastage,

										"purname": val.purname,

                                        "metal_name": val.metal_name,

									});

								});

							}

							tag_details.push({

							'isTagsplitted'				    :(item_balance_weight > 0 ? 1 :0),

							'item_type'						:2,

							'gross_wt'						:item_balance_weight,

							'value'							:val.tag_id,

							'calculation_based_on'			:val.calculation_based_on,

							'cat_id'						:val.cat_id,

							'certification_cost'			:val.certification_cost,

							'charge_value'					:0,

							'charges_details'				:[],

							'cost_center'					:val.cost_center,

							'current_branch'				:val.current_branch,

							'design_id'						:val.design_id,

							'design_name'					:val.design_name,

							'halmarking'					:val.halmarking,

							'huid'							:val.huid,

							'id_mapping_details'			:val.id_mapping_details,

							'id_metal'						:val.id_metal,

							'id_orderdetails'				:val.id_orderdetails,

							'id_section'					:val.id_section,

							'id_sub_design'					:val.id_sub_design,

							'id_tag_mapping'				:val.id_tag_mapping,

							'item_rate'						:val.item_rate,

							'id_sub_design'					:val.id_sub_design,

							'label'							:val.label,

							'less_wt'						:val.less_wt,

							'lot_product'					:val.lot_product,

							'lot_received_at'				:val.lot_received_at,

							'market_rate_field'				:val.market_rate_field,

							'metal_type'					:val.metal_type,

							'net_wt'						:val.net_wt,

							'ord_rate_type'					:val.ord_rate_type,

							'order_no'						:val.order_no,

							'order_rate_per_grm'			:val.order_rate_per_grm,

							'piece'							:val.piece,

							'product_name'					:val.product_name,

							'product_short_code'			:val.product_short_code,

							'purity'						:val.purity,

							'purname'						:val.purname,

							'quality_id'					:val.quality_id,

							'rate_field'					:val.rate_field,

							'retail_max_wastage_percent'    :val.retail_max_wastage_percent,

							'sales_mode'					:val.sales_mode,

							'sales_value'					:val.sales_value,

							'scheme_closure_benefit'		:val.scheme_closure_benefit,

							'section_name'					:val.section_name,

							'size'							:val.size,

							'size_name'						:val.size_name,

							'stone_price'					:val.stone_price,

							'stone_type'					:val.stone_type,

							'sub_design_name'				:val.sub_design_name,

							'tag_id'						:val.tag_id,

							'tag_mc_type'					:val.tag_mc_type,

							'tag_mc_value'					:val.tag_mc_value,

							'tag_other_itm_amount'			:val.tag_other_itm_amount,

							'tag_purchase_cost'				:val.tag_purchase_cost,

							'tag_status'					:val.tag_status,

							'tax_group_id'					:val.tax_group_id,

							'tax_percentage'				:val.tax_percentage,

							'tgi_calculation'				:val.tgi_calculation,

							'uom'							:val.uom,

							'uom_id'						:val.uom_id,

							'stone_details'					:split_stn_details,

							'other_metal_details'			:split_other_metal_details,

							'tag_images'					:[],

							});

							applied_closing_weight+=parseFloat(item_balance_weight);

						}

					}else{

							var split_stn_details = [];

							var stone_details = val.stone_details;

							var other_metal_details = val.other_metal_details;

							var split_other_metal_details = [];

							var charges_details = [];

							if(stone_details.length>0){

								$.each(stone_details,function(k,val){

									var stn_weight = val.stone_wt;



									split_stn_details.push({

										'show_in_lwt':val.show_in_lwt,

										'stone_pcs':val.stone_pcs,

										'stone_rate':val.stone_rate,

										'stone_cal_type':val.stone_cal_type,

										'stone_id':val.stone_id,

										'tag_id':val.tag_id,

										'stone_uom_id':val.stone_uom_id,

										'stone_wt'	:stn_weight,

										"act_stn_pcs": val.act_stn_pcs,

									    "act_stone_wt": val.act_stone_wt,

										'stone_price':(val.stone_cal_type ==2 ? parseFloat(parseFloat(val.stone_rate)*parseFloat(val.stone_pcs).toFixed(2)) :parseFloat(parseFloat(stn_weight)*parseFloat(val.stone_rate).toFixed(2))),

									});

								});

								console.log(split_stn_details);

							}

							if(val.charges_details.length > 0){

								$.each(val.charges_details, function (skey, sitem) {

									charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display,"tag_id": sitem.tag_id });

								});

							}

							if(other_metal_details.length > 0)

							{

								$.each(other_metal_details,function(k,val){

									var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

									split_other_metal_details.push({

										'id_metal':val.id_metal,

										'tag_other_itm_amount':val.tag_other_itm_amount,

										'tag_other_itm_cal_type':val.tag_other_itm_cal_type,

										'tag_other_itm_grs_weight':tag_other_itm_grs_weight,

										'tag_other_itm_id':val.tag_other_itm_id,

										'tag_other_itm_mc':val.tag_other_itm_mc,

										'tag_other_itm_metal_id':val.tag_other_itm_metal_id,

										'tag_other_itm_pcs':val.tag_other_itm_pcs,

										'tag_other_itm_pur_id':val.tag_other_itm_pur_id,

										'tag_other_itm_rate':val.tag_other_itm_rate,

										'tag_other_itm_tag_id':val.tag_other_itm_tag_id,

										'tag_other_itm_uom':val.tag_other_itm_uom,

										'tag_other_itm_wastage':val.tag_other_itm_wastage,

										"purname": val.purname,

										"metal_name": val.metal_name,

									});

								});

							}

						tag_details.push({

							'isTagsplitted'				    :(total_balance_applied_weight > 0 && val.calculation_based_on!=3 ? 1 :0),

							'item_type'						:0,

							'gross_wt'						:val.gross_wt,

							'value'							:val.tag_id,

							'calculation_based_on'			:val.calculation_based_on,

							'cat_id'						:val.cat_id,

							'certification_cost'			:val.certification_cost,

							'charge_value'					:val.charge_value,

							'charges_details'				:charges_details,

							'cost_center'					:val.cost_center,

							'current_branch'				:val.current_branch,

							'design_id'						:val.design_id,

							'design_name'					:val.design_name,

							'halmarking'					:val.halmarking,

							'huid'							:val.huid,

							'id_mapping_details'			:val.id_mapping_details,

							'id_metal'						:val.id_metal,

							'id_orderdetails'				:val.id_orderdetails,

							'id_section'					:val.id_section,

							'id_sub_design'					:val.id_sub_design,

							'id_tag_mapping'				:val.id_tag_mapping,

							'item_rate'						:val.item_rate,

							'id_sub_design'					:val.id_sub_design,

							'label'							:val.label,

							'less_wt'						:val.less_wt,

							'lot_product'					:val.lot_product,

							'lot_received_at'				:val.lot_received_at,

							'market_rate_field'				:val.market_rate_field,

							'metal_type'					:val.metal_type,

							'net_wt'						:val.net_wt,

							'ord_rate_type'					:val.ord_rate_type,

							'order_no'						:val.order_no,

							'order_rate_per_grm'			:val.order_rate_per_grm,

							'piece'							:val.piece,

							'product_name'					:val.product_name,

							'product_short_code'			:val.product_short_code,

							'purity'						:val.purity,

							'purname'						:val.purname,

							'quality_id'					:val.quality_id,

							'rate_field'					:val.rate_field,

							'retail_max_wastage_percent'    :(total_balance_applied_weight > 0 && val.calculation_based_on!=3 ? 0 :val.retail_max_wastage_percent),

							'sales_mode'					:val.sales_mode,

							'sales_value'					:val.sales_value,

							'scheme_closure_benefit'		:val.scheme_closure_benefit,

							'section_name'					:val.section_name,

							'size'							:val.size,

							'size_name'						:val.size_name,

							'stone_price'					:val.stone_price,

							'stone_type'					:val.stone_type,

							'sub_design_name'				:val.sub_design_name,

							'tag_id'						:val.tag_id,

							'tag_mc_type'					:val.tag_mc_type,

							'tag_mc_value'					:(total_balance_applied_weight > 0 && val.calculation_based_on!=3 ? 0 :val.tag_mc_value),

							'tag_other_itm_amount'			:val.tag_other_itm_amount,

							'tag_purchase_cost'				:val.tag_purchase_cost,

							'tag_status'					:val.tag_status,

							'tax_group_id'					:val.tax_group_id,

							'tax_percentage'				:val.tax_percentage,

							'tgi_calculation'				:val.tgi_calculation,

							'uom'							:val.uom,

							'uom_id'						:val.uom_id,

							'stone_details'					:split_stn_details,

							'other_metal_details'			:split_other_metal_details,

							'tag_images'					:[],

						});

						if(val.calculation_based_on!=3){

						    applied_closing_weight+=parseFloat(val.gross_wt);

						}





					}

				}

		});

	if(tag_details.length>0)

	{

		$('#estimation_tag_details > tbody').empty();

		$.each(tag_details,function(key,ival){

			append_tag_details(ival);

		});

	}else{

		$('#estimation_tag_details > tbody').empty();

		$.each(tag_split_details,function(key,ival){

			append_tag_details(ival);

		});

	}

	setTimeout(function(){

		calculatetag_SaleValue();

	},1000)



	console.log(tag_details);

}





// function set_tag_split_details() {

//     tag_details = [];

//     var total_closing_weight = 0;

// 	var retail_max_wastage_percent = 0;

// 	var max_va_per = 0;

// 	var id_wast_slab = 0;

// 	var blk_dic = 0;

// 	var wastArr = [];
	
// 	// Capture manually edited rates before processing
	

// 	$('#estimation_tag_details > tbody > tr').each(function() {

// 		const tagId = $(this).find('.est_tag_id').val();

// 		const rate = $(this).find('.market_rate_value').val();

// 		retail_max_wastage_percent = $(this).find('.wastage_max_per').val();

// 		max_va_per = $(this).find('.tag_act_va_per').val();

// 		id_wast_slab = $(this).find('.id_wastage_slab').val();

// 		blk_dic = $(this).find('.blk_disc_apld').val();

// 		act_wast_per = $(this).find('.act_va_per').val();
		
// 		wast_slb_value = $(this).find('.wast_slb_val').val();

// 		if (tagId && rate) {

// 			manualRates[tagId] = rate;

// 		}

// 		wastArr.push({
//         tagId: tagId,
//         retail_max_wastage_percent: retail_max_wastage_percent,
//         max_va_per: max_va_per,
// 		id_wast_slab : id_wast_slab,
// 		blk_dic_per : blk_dic,
// 		act_wast_per : act_wast_per,
// 		wast_slb_value : wast_slb_value
//     	});

// 	});

// 	$.each(tag_split_details, function (key, ival) {

// 		ival.balance_weight = ival.gross_wt;

// 		$.each(wastArr, function(item,val){

// 			if(ival.tag_id == val.tagId){
		
// 				ival.retail_max_wastage_percent = val.retail_max_wastage_percent;
				
// 				ival.max_va_per = val.max_va_per;

// 				 if (ival.id_wast_slab === undefined || ival.blk_dic_per === undefined || ival.wast_slb_value === undefined) {

// 					ival.id_wast_slab = val.id_wast_slab;
// 					ival.blk_dic_per = val.blk_dic_per;
// 					ival.wast_slb_value = val.wast_slb_value;
// 					ival.act_wast_per = val.act_wast_per;

// 				}

// 			}

// 		});

// 	});

//     $('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

//         var curRow = $(this).closest("tr");

//         //total_closing_weight+=parseFloat((parseFloat(curRow.find('.closing_weight').val())*parseFloat(curRow.find('.bonus_percent').val()))/100);

//         total_closing_weight = parseFloat(curRow.find('.closing_weight').val());



//         var applied_closing_weight = 0;

//         var total_balance_applied_weight = 0;



// 		var savings_in_making_charge = 0;



// 		var savings_in_wastage = 0;



//         $.each(tag_split_details, function (key, val) {



//             var isTagsplitted = false;

//             if (tag_details.length > 0) {

//                 $.each(tag_details, function (idx, tag) {

//                     if (tag.value != undefined) {

//                         if (tag.value == val.value) {



//                             if(val.balance_weight <= 0){

//                                 isTagsplitted = true;

//                             }

                            

//                         }

//                     }

//                 });

//             }

//             if (total_closing_weight > 0 && (!isTagsplitted)) {



//                 var item_weight = val.balance_weight;

//                 total_balance_applied_weight = parseFloat(parseFloat(total_closing_weight) - parseFloat(applied_closing_weight)).toFixed(3);

//                 if ( (parseFloat(total_balance_applied_weight) < parseFloat(item_weight)) && (total_balance_applied_weight > 0) && (val.calculation_based_on != 3) && (val.id_metal == 1) && (parseFloat(curRow.find('.bonus_percent').val()) > 0) ) { //&& (val.id_metal == 1) (parseFloat(total_balance_applied_weight) < parseFloat(item_weight)) &&

//                     var item_balance_weight = parseFloat(parseFloat(item_weight) - parseFloat(total_balance_applied_weight)).toFixed(3);

// 				//	val.balance_weight = item_balance_weight;

//                     var split_stn_details = [];

//                     var split_other_metal_details = [];

//                     var charges_details = [];

//                     var stone_details = val.stone_details;

//                     var other_metal_details = val.other_metal_details;

//                     var tag_stn_weight = 0;

//                     var other_wt = 0;

// 					var tag_wt = val.gross_wt;



//                     if (stone_details.length > 0) {

//                         $.each(stone_details, function (k, val) {

//                             var stn_weight = val.stone_wt;

// 							var stone_wt =0;

//                             if (stn_weight > 0) {

//                                 var stn_weight_per = parseFloat((parseFloat(total_balance_applied_weight) / parseFloat(tag_wt)) * parseFloat(100)).toFixed(2);

//                                 stn_weight = parseFloat((parseFloat(stn_weight * stn_weight_per)) / 100).toFixed(3);

//                             }

                        

//                             var stone_pcs = (tag_wt == item_weight)?val.stone_pcs:0;



// 							$.each(uom_details, function (key, itemk) {



// 								if (itemk.uom_id == val.stone_uom_id) {



// 									if (val.show_in_lwt == 1) {



// 										if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram

	

// 										{

	

// 											stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(itemk.divided_by_value));

	

// 										} else {

	

// 											stone_wt = stn_weight;

	

// 										}

	

// 										tag_stn_weight += parseFloat(stone_wt);

	

// 									}

// 								}})



//                             split_stn_details.push({

//                                 'show_in_lwt': val.show_in_lwt,

//                                 'stone_pcs': stone_pcs,

//                                 'stone_rate': val.stone_rate,

//                                 'stone_cal_type': val.stone_cal_type,

//                                 'stone_id': val.stone_id,

//                                 'tag_id': val.tag_id,

//                                 'stone_uom_id': val.stone_uom_id,

//                                 'stone_wt': stn_weight,

//                                 "act_stn_pcs": val.act_stn_pcs,

//                                 "act_stone_wt": val.act_stone_wt,

//                                 'stone_price': val.amount

									

//                             });

//                         });

//                         console.log(split_stn_details);

//                     }

					

//                     if (val.charges_details.length > 0) {

//                         $.each(val.charges_details, function (skey, sitem) {

//                             charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display, "tag_id": sitem.tag_id });

//                         });

//                     }

					



//                     if (other_metal_details.length > 0) {

//                         $.each(other_metal_details, function (k, val) {

//                             var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

//                             if (tag_other_itm_grs_weight > 0) {

//                                 var other_metal_weight_per = parseFloat((parseFloat(total_balance_applied_weight) / parseFloat(item_weight)) * parseFloat(100)).toFixed(2);

//                                 tag_other_itm_grs_weight = parseFloat((parseFloat(tag_other_itm_grs_weight) * parseFloat(other_metal_weight_per)) / 100).toFixed(3);

//                             }



//                             other_wt += parseFloat(tag_other_itm_grs_weight);

//                             split_other_metal_details.push({

//                                 'id_metal': val.id_metal,

//                                 'tag_other_itm_amount': val.tag_other_itm_amount,

//                                 'tag_other_itm_cal_type': val.tag_other_itm_cal_type,

//                                 'tag_other_itm_grs_weight': tag_other_itm_grs_weight,

//                                 'tag_other_itm_id': val.tag_other_itm_id,

//                                 'tag_other_itm_mc': 0,

//                                 'tag_other_itm_metal_id': val.tag_other_itm_metal_id,

//                                 'tag_other_itm_pcs': val.tag_other_itm_pcs,

//                                 'tag_other_itm_pur_id': val.tag_other_itm_pur_id,

//                                 'tag_other_itm_rate': val.tag_other_itm_rate,

//                                 'tag_other_itm_tag_id': val.tag_other_itm_tag_id,

//                                 'tag_other_itm_uom': val.tag_other_itm_uom,

//                                 'tag_other_itm_wastage': 0,

//                                 "purname": val.purname,

//                                 "metal_name": val.metal_name,

//                             });

//                         });

//                     }



//                     var va_per = 0;

//                     var calculation_type = val.calculation_based_on;

//                     var gross_wt = total_balance_applied_weight;

//                     var net_wt = parseFloat(total_balance_applied_weight) - parseFloat(other_wt) - parseFloat(tag_stn_weight);

// 					var mc_type = val.tag_mc_type;

// 					var mc  = ( val.gross_wt == val.balance_weight && val.tag_mc_type == 1 ? val.tag_mc_value : (val.tag_mc_type == 2 ? val.tag_mc_value : 0));

// 					var tot_wastage = val.retail_max_wastage_percent;

// 					var wast_wt_discount =0;

// 					var mc_discount =0;

                    

// 					if (calculation_type == 0) {



// 						var wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100));



// 						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val())/100));



// 						var wast_gt =  wast_wgt - va_dis_wt;



// 						var va_per = parseFloat((wast_gt * 100) /gross_wt);



// 						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * 1));



// 						var mc_val = parseFloat(parseFloat(mc) - parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ))).toFixed(2);



// 						wast_wt_discount = va_dis_wt;



// 						mc_discount = parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ));



// 					}else if(calculation_type == 1){



// 						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



// 						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val())/100));



// 						var wast_gt =  wast_wgt - va_dis_wt;



// 						var va_per = parseFloat((wast_gt * 100) /net_wt);





// 						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



// 						var mc_val = parseFloat(parseFloat(mc) - parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ))).toFixed(2);



// 						wast_wt_discount = va_dis_wt;



// 						mc_discount = parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ));





// 					}else if(calculation_type == 2){



// 						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



// 						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val())/100));



// 						var wast_gt =  wast_wgt - va_dis_wt;



// 						var va_per = parseFloat((wast_gt * 100) /net_wt);



// 						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



// 						var mc_val = parseFloat(parseFloat(mc) - parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ))).toFixed(2);



// 						wast_wt_discount = va_dis_wt;



// 						mc_discount = parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ));

					



// 					}else{



// 						var va_per = 0;



// 						var tot_mc =0;



// 						var mc_val =0;



// 						wast_wt_discount = 0;



// 						mc_discount = 0;



// 					}



// 					savings_in_wastage += parseFloat( isNaN(wast_wt_discount) ? 0 : wast_wt_discount);



// 					savings_in_making_charge +=	parseFloat(isNaN(mc_discount) ? 0 : mc_discount);





// 					 va_per = parseFloat( isNaN(va_per) ? tot_wastage : va_per).toFixed(2);



// 					 mc_val = parseFloat(   isNaN(mc_val) ? mc : mc_val).toFixed(2);



// 				//	var va_per = (parseFloat(wast_wgt / (parseFloat(curRow.find('.bonus_percent').val())/100)) * 100)





//                     tag_details.push({

//                         'isTagsplitted': (total_balance_applied_weight > 0 ? 1 : 0),

//                         'gross_wt': total_balance_applied_weight,

//                         'value': val.tag_id,

//                         'item_type': ( val.gross_wt == val.balance_weight ? 0:2),

//                         'calculation_based_on': val.calculation_based_on,

//                         'cat_id': val.cat_id,

//                         'certification_cost': val.certification_cost,

//                         'charge_value': (val.gross_wt == val.balance_weight ? val.charge_value : 0),

//                         'charges_details': (val.gross_wt == val.balance_weight ? charges_details : []),

//                         'cost_center': val.cost_center,

//                         'current_branch': val.current_branch,

//                         'design_id': val.design_id,

//                         'design_name': val.design_name,

//                         'halmarking': val.halmarking,

//                         'huid': val.huid,

//                         'id_mapping_details': val.id_mapping_details,

//                         'id_metal': val.id_metal,

//                         'id_orderdetails': val.id_orderdetails,

//                         'id_section': val.id_section,

//                         'id_sub_design': val.id_sub_design,

//                         'id_tag_mapping': val.id_tag_mapping,

//                         'item_rate': val.item_rate,

//                         'id_sub_design': val.id_sub_design,

//                         'label': val.label,

//                         'less_wt': val.less_wt,

//                         'lot_product': val.lot_product,

//                         'lot_received_at': val.lot_received_at,

//                         'market_rate_field': val.market_rate_field,

//                         'metal_type': val.metal_type,

//                         'net_wt': val.net_wt,

//                         'ord_rate_type': val.ord_rate_type,

//                         'order_no': val.order_no,

//                         'order_rate_per_grm': val.order_rate_per_grm,

// 						'piece':(parseFloat(val.balance_weight) == parseFloat(val.gross_wt)  ? val.piece : 0),

//                       //  'piece': val.piece,

//                         'product_name': val.product_name,

//                         'product_short_code': val.product_short_code,

//                         'purity': val.purity,

//                         'purname': val.purname,

//                         'quality_id': val.quality_id,

//                         'rate_field': val.rate_field,

//                         //'retail_max_wastage_percent'    :val.retail_max_wastage_percent,

//                         'retail_max_wastage_percent': va_per,

// 						'max_va_per': val.max_va_per,

// 						'id_wast_slab': val.id_wast_slab,
						
// 						'blk_dic_per': val.blk_dic_per,

// 						'act_wast_per': val.act_wast_per,
						
// 						'wast_slb_value': val.wast_slb_value,

//                         'sales_mode': val.sales_mode,

//                         'sales_value': val.sales_value,

//                         'scheme_closure_benefit': val.scheme_closure_benefit,

//                         'section_name': val.section_name,

//                         'size': val.size,

//                         'size_name': val.size_name,

//                         'stone_price': val.stone_price,

//                         'stone_type': val.stone_type,

//                         'sub_design_name': val.sub_design_name,

//                         'tag_id': val.tag_id,

//                         'tag_mc_type': val.tag_mc_type,

//                         'tag_mc_value': mc_val,

//                         'tag_other_itm_amount': val.tag_other_itm_amount,

//                         'tag_purchase_cost': val.tag_purchase_cost,

//                         'tag_status': val.tag_status,

//                         'tax_group_id': val.tax_group_id,

//                         'tax_percentage': val.tax_percentage,

//                         'tgi_calculation': val.tgi_calculation,

//                         'uom': val.uom,

//                         'uom_id': val.uom_id,

// 						'stone_details': split_stn_details,

//                         'other_metal_details': split_other_metal_details,

// 						//'stone_details': ( parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? stone_details:[]),

// 						//'other_metal_details': (  parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? other_metal_details:[]),

//                         'tag_images': [],

// 						'is_split_row'	: 1,

//                     });

//                     applied_closing_weight += parseFloat(item_weight);

// 					if (item_balance_weight > 0) {

// 						val.balance_weight =item_balance_weight;

// 						return false; 

// 					}



//                 } 

// 				else if((parseFloat(total_balance_applied_weight) > parseFloat(item_weight)) && (val.calculation_based_on != 3) && (val.id_metal == 1) && (parseFloat(curRow.find('.bonus_percent').val()) > 0) ){



// 					var split_stn_details = [];

// 					var stone_details = val.stone_details;

// 					var other_metal_details = val.other_metal_details;

// 					var split_other_metal_details = [];

// 					var charges_details = [];

// 					var tag_stn_weight = 0;

//                     var other_wt = 0;

// 					/*

// 					if(stone_details.length>0){

// 						$.each(stone_details,function(k,val){

// 							var stn_weight = val.stone_wt;

//                             var stone_pcs = (val.gross_wt == val.balance_weight)?val.stone_pcs:0;



// 							$.each(uom_details, function (key, itemk) {



// 								if (itemk.uom_id == val.stone_uom_id) {



// 									if (val.show_in_lwt == 1) {



// 										if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram

	

// 										{

	

// 											stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(val.divided_by_value));

	

// 										} else {

	

// 											stone_wt = stn_weight;

	

// 										}

	

// 										tag_stn_weight += parseFloat(stone_wt);

	

// 									}

// 							}})

// 							split_stn_details.push({

// 								'show_in_lwt':val.show_in_lwt,

// 								'stone_pcs':stone_pcs,

// 								'stone_rate':val.stone_rate,

// 								'stone_cal_type':val.stone_cal_type,

// 								'stone_id':val.stone_id,

// 								'tag_id':val.tag_id,

// 								'stone_uom_id':val.stone_uom_id,

// 								'stone_wt'	:stn_weight,

// 								"act_stn_pcs": val.act_stn_pcs,

// 								"act_stone_wt": val.act_stone_wt,

// 								'stone_price': val.amount

// 							//	'stone_price':(val.stone_cal_type ==2 ? parseFloat(parseFloat(val.stone_rate)*parseFloat(val.stone_pcs).toFixed(2)) :parseFloat(parseFloat(stn_weight)*parseFloat(val.stone_rate).toFixed(2))),

// 							});

// 						});

// 						console.log(split_stn_details);

// 					}

// 					*/



// 					var tag_wt = val.gross_wt;



// 					if (stone_details.length > 0) {

//                         $.each(stone_details, function (k, val) {

//                             var stn_weight = val.stone_wt;

// 							var stone_wt =0;

//                             if (stn_weight > 0) {

//                                 var stn_weight_per = parseFloat((parseFloat(item_weight) / parseFloat(tag_wt)) * parseFloat(100)).toFixed(2);

//                                 stn_weight = parseFloat((parseFloat(stn_weight * stn_weight_per)) / 100).toFixed(3);

//                             }

                        

//                             var stone_pcs = (tag_wt == item_weight)?val.stone_pcs:0;



// 							$.each(uom_details, function (key, itemk) {



// 								if (itemk.uom_id == val.stone_uom_id) {



// 									if (val.show_in_lwt == 1) {



// 										if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram

	

// 										{

	

// 											stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(itemk.divided_by_value));

	

// 										} else {

	

// 											stone_wt = stn_weight;

	

// 										}

	

// 										tag_stn_weight += parseFloat(stone_wt);

	

// 									}

// 								}})



//                             split_stn_details.push({

//                                 'show_in_lwt': val.show_in_lwt,

//                                 'stone_pcs': stone_pcs,

//                                 'stone_rate': val.stone_rate,

//                                 'stone_cal_type': val.stone_cal_type,

//                                 'stone_id': val.stone_id,

//                                 'tag_id': val.tag_id,

//                                 'stone_uom_id': val.stone_uom_id,

//                                 'stone_wt': stn_weight,

//                                 "act_stn_pcs": val.act_stn_pcs,

//                                 "act_stone_wt": val.act_stone_wt,

//                                 'stone_price': val.amount

									

//                             });

//                         });

//                         console.log(split_stn_details);

//                     }

					

// 					if(val.charges_details.length > 0){

// 						$.each(val.charges_details, function (skey, sitem) {

// 							charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display,"tag_id": sitem.tag_id });

// 						});

// 					}

// 					/*

// 					if(other_metal_details.length > 0)

// 					{

// 						$.each(other_metal_details,function(k,val){

// 							var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

// 							split_other_metal_details.push({

// 								'id_metal':val.id_metal,

// 								'tag_other_itm_amount':val.tag_other_itm_amount,

// 								'tag_other_itm_cal_type':val.tag_other_itm_cal_type,

// 								'tag_other_itm_grs_weight':tag_other_itm_grs_weight,

// 								'tag_other_itm_id':val.tag_other_itm_id,

// 								'tag_other_itm_mc':val.tag_other_itm_mc,

// 								'tag_other_itm_metal_id':val.tag_other_itm_metal_id,

// 								'tag_other_itm_pcs':val.tag_other_itm_pcs,

// 								'tag_other_itm_pur_id':val.tag_other_itm_pur_id,

// 								'tag_other_itm_rate':val.tag_other_itm_rate,

// 								'tag_other_itm_tag_id':val.tag_other_itm_tag_id,

// 								'tag_other_itm_uom':val.tag_other_itm_uom,

// 								'tag_other_itm_wastage':val.tag_other_itm_wastage,

// 								"purname": val.purname,

// 								"metal_name": val.metal_name,

// 							});

// 							other_wt += parseFloat(tag_other_itm_grs_weight);



// 						});

// 					}

// 					*/



// 					if (other_metal_details.length > 0) {

//                         $.each(other_metal_details, function (k, val) {

//                             var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

//                             if (tag_other_itm_grs_weight > 0) {

//                                 var other_metal_weight_per = parseFloat((parseFloat(item_weight) / parseFloat(tag_wt)) * parseFloat(100)).toFixed(2);

//                                 tag_other_itm_grs_weight = parseFloat((parseFloat(tag_other_itm_grs_weight) * parseFloat(other_metal_weight_per)) / 100).toFixed(3);

//                             }



//                             other_wt += parseFloat(tag_other_itm_grs_weight);

//                             split_other_metal_details.push({

//                                 'id_metal': val.id_metal,

//                                 'tag_other_itm_amount': val.tag_other_itm_amount,

//                                 'tag_other_itm_cal_type': val.tag_other_itm_cal_type,

//                                 'tag_other_itm_grs_weight': tag_other_itm_grs_weight,

//                                 'tag_other_itm_id': val.tag_other_itm_id,

//                                 'tag_other_itm_mc': 0,

//                                 'tag_other_itm_metal_id': val.tag_other_itm_metal_id,

//                                 'tag_other_itm_pcs': val.tag_other_itm_pcs,

//                                 'tag_other_itm_pur_id': val.tag_other_itm_pur_id,

//                                 'tag_other_itm_rate': val.tag_other_itm_rate,

//                                 'tag_other_itm_tag_id': val.tag_other_itm_tag_id,

//                                 'tag_other_itm_uom': val.tag_other_itm_uom,

//                                 'tag_other_itm_wastage': 0,

//                                 "purname": val.purname,

//                                 "metal_name": val.metal_name,

//                             });

//                         });

//                     }



// 					var va_per = 0;

//                     var calculation_type = val.calculation_based_on;

//                     var gross_wt = val.gross_wt;

//                     var net_wt = parseFloat(val.gross_wt) - parseFloat(other_wt) - parseFloat(tag_stn_weight);

// 					var mc_type = val.tag_mc_type;

// 					var mc  = ( val.gross_wt == val.balance_weight && val.tag_mc_type == 1 ? val.tag_mc_value : (val.tag_mc_type == 2 ? val.tag_mc_value : 0));

// 					var tot_wastage = val.retail_max_wastage_percent;



// 					wast_wt_discount =0



// 					mc_discount =0



                    

// 					if (calculation_type == 0) {



// 						var wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100));



// 						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val())/100));



// 						var wast_gt =  wast_wgt - va_dis_wt;



// 						var va_per = parseFloat((wast_gt * 100) /gross_wt);



// 						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * 1));



// 						var mc_val = parseFloat(parseFloat(mc) - parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ))).toFixed(2);



// 						wast_wt_discount = va_dis_wt;



// 						mc_discount = parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ));



// 					}else if(calculation_type == 1){



// 						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



// 						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val())/100));



// 						var wast_gt =  wast_wgt - va_dis_wt;



// 						var va_per = parseFloat((wast_gt * 100) /net_wt);





// 						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



// 						var mc_val = parseFloat(parseFloat(mc) - parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ))).toFixed(2);



// 						wast_wt_discount = va_dis_wt;



// 						mc_discount = parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ));





// 					}else if(calculation_type == 2){



// 						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



// 						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val())/100));



// 						var wast_gt =  wast_wgt - va_dis_wt;



// 						var va_per = parseFloat((wast_gt * 100) /net_wt);



// 						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



// 						var mc_val = parseFloat(parseFloat(mc) - parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ))).toFixed(2);



// 						wast_wt_discount = va_dis_wt;



// 						mc_discount = parseFloat( mc * ( parseFloat(curRow.find('.bonus_percent').val())/100 ));

					



// 					}else{



// 						var va_per = 0;



// 						var tot_mc =0;



// 						var mc_val =0;



// 						wast_wt_discount = 0;



// 						mc_discount = 0;



// 					}



// 					savings_in_wastage += parseFloat( isNaN(wast_wt_discount) ? 0 : wast_wt_discount);



// 					savings_in_making_charge +=	parseFloat(isNaN(mc_discount) ? 0 : mc_discount);



// 					 va_per = parseFloat( isNaN(va_per) ? tot_wastage : va_per).toFixed(2);



// 					 mc_val = parseFloat(   isNaN(mc_val) ? mc : mc_val).toFixed(2);





// 					tag_details.push({

// 						'isTagsplitted'				    :(total_balance_applied_weight > 0 && val.calculation_based_on!=3 ? 1 :0),

// 						'item_type'						: (val.gross_wt == val.balance_weight ? 0:2),

// 						'gross_wt'						:val.balance_weight,

// 						'value'							:val.tag_id,

// 						'calculation_based_on'			:val.calculation_based_on,

// 						'cat_id'						:val.cat_id,

// 						'certification_cost'			:val.certification_cost,

//                         'charge_value'                  : (val.gross_wt == val.balance_weight ? val.charge_value : 0),

//                         'charges_details'               : (val.gross_wt == val.balance_weight ? charges_details : []),

// 						'cost_center'					:val.cost_center,

// 						'current_branch'				:val.current_branch,

// 						'design_id'						:val.design_id,

// 						'design_name'					:val.design_name,

// 						'halmarking'					:val.halmarking,

// 						'huid'							:val.huid,

// 						'id_mapping_details'			:val.id_mapping_details,

// 						'id_metal'						:val.id_metal,

// 						'id_orderdetails'				:val.id_orderdetails,

// 						'id_section'					:val.id_section,

// 						'id_sub_design'					:val.id_sub_design,

// 						'id_tag_mapping'				:val.id_tag_mapping,

// 						'item_rate'						:val.item_rate,

// 						'id_sub_design'					:val.id_sub_design,

// 						'label'							:val.label,

// 						'less_wt'						:val.less_wt,

// 						'lot_product'					:val.lot_product,

// 						'lot_received_at'				:val.lot_received_at,

// 						'market_rate_field'				:val.market_rate_field,

// 						'metal_type'					:val.metal_type,

// 						'net_wt'						:val.net_wt,

// 						'ord_rate_type'					:val.ord_rate_type,

// 						'order_no'						:val.order_no,

// 						'order_rate_per_grm'			:val.order_rate_per_grm,

// 						'piece'							:(parseFloat(val.balance_weight) == parseFloat(val.gross_wt)  ? val.piece : 0),

// 						//'piece'							:val.piece,

// 						'product_name'					:val.product_name,

// 						'product_short_code'			:val.product_short_code,

// 						'purity'						:val.purity,

// 						'purname'						:val.purname,

// 						'quality_id'					:val.quality_id,

// 						'rate_field'					:val.rate_field,

// 						'retail_max_wastage_percent'    : va_per,

// 						'max_va_per'					: val.max_va_per,

// 						'id_wast_slab'					: val.id_wast_slab,
						
// 						'blk_dic_per'					: val.blk_dic_per,

// 						'act_wast_per'					: val.act_wast_per,

// 						'wast_slb_value'					: val.wast_slb_value,

// 						'sales_mode'					:val.sales_mode,

// 						'sales_value'					:val.sales_value,

// 						'scheme_closure_benefit'		:val.scheme_closure_benefit,

// 						'section_name'					:val.section_name,

// 						'size'							:val.size,

// 						'size_name'						:val.size_name,

// 						'stone_price'					:val.stone_price,

// 						'stone_type'					:val.stone_type,

// 						'sub_design_name'				:val.sub_design_name,

// 						'tag_id'						:val.tag_id,

// 						'tag_mc_type'					:val.tag_mc_type,

// 						'tag_mc_value'					:mc_val,

// 						'tag_other_itm_amount'			:val.tag_other_itm_amount,

// 						'tag_purchase_cost'				:val.tag_purchase_cost,

// 						'tag_status'					:val.tag_status,

// 						'tax_group_id'					:val.tax_group_id,

// 						'tax_percentage'				:val.tax_percentage,

// 						'tgi_calculation'				:val.tgi_calculation,

// 						'uom'							:val.uom,

// 						'uom_id'						:val.uom_id,

// 						//'stone_details': ( parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? stone_details:[]),

// 						//'other_metal_details': (  parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? other_metal_details:[]),

// 						'stone_details': split_stn_details,

//                         'other_metal_details': split_other_metal_details,

// 						'tag_images'					:[],

// 						'is_split_row'	: 1,


// 					});

// 					if(val.calculation_based_on!=3){

// 						applied_closing_weight+=parseFloat(val.balance_weight);

// 					}



// 					val.balance_weight = 0; 







// 				}

//             }

//         });





// 		curRow.find('.savings_in_wastage').val(savings_in_wastage);

// 		curRow.find('.savings_in_mcvalue').val(savings_in_making_charge)

//     });



// 	if (tag_details.length > 0) {



// 		$.each(tag_split_details, function (key, val) {



// 			if(val.balance_weight > 0 ){

// 				//append_tag_details(ival);

// 				var item_weight = val.balance_weight;

// 				var item_balance_weight = val.balance_weight;

// 				var split_stn_details = [];

// 				var split_other_metal_details = [];

// 				var stone_details = val.stone_details;

// 				var other_metal_details = val.other_metal_details;

// 				var charges_details = [];

// 				var tag_stn_weight =0;



// 				if(val.charges_details.length > 0){

// 					$.each(val.charges_details, function (skey, sitem) {

// 						charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display,"tag_id": sitem.tag_id });

// 					});

// 				}

// 				/*

// 				if(stone_details.length>0){

// 					$.each(stone_details,function(k,val){

// 						var stn_weight = val.stone_wt;

// 						// if(stn_weight > 0)

// 						// {

// 						// 	var stn_weight_per = parseFloat((parseFloat(item_balance_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

// 						// 	stn_weight 	   = parseFloat((parseFloat(stn_weight)*parseFloat(stn_weight_per))/100).toFixed(3);

// 						// }



// 						var stone_pcs = (val.gross_wt == val.balance_weight) ? val.stone_pcs:0;



// 						split_stn_details.push({

// 							'show_in_lwt':val.show_in_lwt,

// 							'stone_pcs':stone_pcs,

// 							'stone_rate':val.stone_rate,

// 							'stone_cal_type':val.stone_cal_type,

// 							'stone_id':val.stone_id,

// 							'tag_id':val.tag_id,

// 							'stone_uom_id':val.stone_uom_id,

// 							'stone_wt'	:stn_weight,

// 							"act_stn_pcs": val.act_stn_pcs,

// 							"act_stone_wt": val.act_stone_wt,

// 							"stone_price": val.amount,

// 							"quality_id": val.quality_id

// 							//'stone_price':(val.stone_cal_type ==2 ? 0 :parseFloat(parseFloat(stn_weight)*parseFloat(val.stone_rate).toFixed(2))),

// 						});

// 					});

// 					console.log(split_stn_details);

// 				}

// 				if(other_metal_details.length > 0)

// 				{

// 					$.each(other_metal_details,function(k,val){

// 						var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

// 						// if(tag_other_itm_grs_weight > 0)

// 						// {

// 						// 	var other_metal_weight_per = parseFloat((parseFloat(item_balance_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

// 						// 	tag_other_itm_grs_weight 	   = parseFloat((parseFloat(tag_other_itm_grs_weight)*parseFloat(other_metal_weight_per))/100).toFixed(3);

// 						// }

// 						split_other_metal_details.push({

// 							'id_metal':val.id_metal,

// 							'tag_other_itm_amount':val.tag_other_itm_amount,

// 							'tag_other_itm_cal_type':val.tag_other_itm_cal_type,

// 							'tag_other_itm_grs_weight':tag_other_itm_grs_weight,

// 							'tag_other_itm_id':val.tag_other_itm_id,

// 							'tag_other_itm_mc':val.tag_other_itm_mc,

// 							'tag_other_itm_metal_id':val.tag_other_itm_metal_id,

// 							'tag_other_itm_pcs':val.tag_other_itm_pcs,

// 							'tag_other_itm_pur_id':val.tag_other_itm_pur_id,

// 							'tag_other_itm_rate':val.tag_other_itm_rate,

// 							'tag_other_itm_tag_id':val.tag_other_itm_tag_id,

// 							'tag_other_itm_uom':val.tag_other_itm_uom,

// 							'tag_other_itm_wastage':val.tag_other_itm_wastage,

// 							"purname": val.purname,

// 							"metal_name": val.metal_name,

// 						});

// 					});

// 				}

// 				*/	



// 				var tag_wt = val.gross_wt;





// 				if (stone_details.length > 0) {

// 					$.each(stone_details, function (k, val) {

// 						var stn_weight = val.stone_wt;

// 						var stone_wt =0;

// 						if (stn_weight > 0) {

// 							var stn_weight_per = parseFloat((parseFloat(item_weight) / parseFloat(tag_wt)) * parseFloat(100)).toFixed(2);

// 							stn_weight = parseFloat((parseFloat(stn_weight * stn_weight_per)) / 100).toFixed(3);

// 						}

					

// 						var stone_pcs = (tag_wt == item_weight)?val.stone_pcs:0;



// 						$.each(uom_details, function (key, itemk) {



// 							if (itemk.uom_id == val.stone_uom_id) {



// 								if (val.show_in_lwt == 1) {



// 									if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram



// 									{



// 										stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(itemk.divided_by_value));



// 									} else {



// 										stone_wt = stn_weight;



// 									}



// 									tag_stn_weight += parseFloat(stone_wt);



// 								}

// 							}})



// 						split_stn_details.push({

// 							'show_in_lwt': val.show_in_lwt,

// 							'stone_pcs': stone_pcs,

// 							'stone_rate': val.stone_rate,

// 							'stone_cal_type': val.stone_cal_type,

// 							'stone_id': val.stone_id,

// 							'tag_id': val.tag_id,

// 							'stone_uom_id': val.stone_uom_id,

// 							'stone_wt': stn_weight,

// 							"act_stn_pcs": val.act_stn_pcs,

// 							"act_stone_wt": val.act_stone_wt,

// 							'stone_price': val.amount

								

// 						});

// 					});

// 					console.log(split_stn_details);

// 				}



// 				if (other_metal_details.length > 0) {

// 					$.each(other_metal_details, function (k, val) {

// 						var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

// 						if (tag_other_itm_grs_weight > 0) {

// 							var other_metal_weight_per = parseFloat((parseFloat(tag_wt) / parseFloat(item_weight)) * parseFloat(100)).toFixed(2);

// 							tag_other_itm_grs_weight = parseFloat((parseFloat(tag_other_itm_grs_weight) * parseFloat(other_metal_weight_per)) / 100).toFixed(3);

// 						}



// 						other_wt += parseFloat(tag_other_itm_grs_weight);

// 						split_other_metal_details.push({

// 							'id_metal': val.id_metal,

// 							'tag_other_itm_amount': val.tag_other_itm_amount,

// 							'tag_other_itm_cal_type': val.tag_other_itm_cal_type,

// 							'tag_other_itm_grs_weight': tag_other_itm_grs_weight,

// 							'tag_other_itm_id': val.tag_other_itm_id,

// 							'tag_other_itm_mc': 0,

// 							'tag_other_itm_metal_id': val.tag_other_itm_metal_id,

// 							'tag_other_itm_pcs': val.tag_other_itm_pcs,

// 							'tag_other_itm_pur_id': val.tag_other_itm_pur_id,

// 							'tag_other_itm_rate': val.tag_other_itm_rate,

// 							'tag_other_itm_tag_id': val.tag_other_itm_tag_id,

// 							'tag_other_itm_uom': val.tag_other_itm_uom,

// 							'tag_other_itm_wastage': 0,

// 							"purname": val.purname,

// 							"metal_name": val.metal_name,

// 						});

// 					});

// 				}

// 				var mc  = ( val.gross_wt == val.balance_weight && val.tag_mc_type == 1 ? val.tag_mc_value : (val.tag_mc_type == 2 ? val.tag_mc_value : 0));



				

// 				tag_details.push({

// 				'isTagsplitted'					: (parseFloat(item_balance_weight) == parseFloat(val.gross_wt)  ? 0 :1 ),

// 				'item_type'						:(parseFloat(item_balance_weight) == parseFloat(val.gross_wt)  ? 0 :2 ),

// 				'gross_wt'						:item_balance_weight,

// 				'value'							:val.tag_id,

// 				'calculation_based_on'			:val.calculation_based_on,

// 				'cat_id'						:val.cat_id,

// 				'certification_cost'			:val.certification_cost,

// 				'charge_value'					: (parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? val.charge_value : 0),

// 				'charges_details'				: (parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? charges_details : []),

// 				'cost_center'					:val.cost_center,

// 				'current_branch'				:val.current_branch,

// 				'design_id'						:val.design_id,

// 				'design_name'					:val.design_name,

// 				'halmarking'					:val.halmarking,

// 				'huid'							:val.huid,

// 				'id_mapping_details'			:val.id_mapping_details,

// 				'id_metal'						:val.id_metal,

// 				'id_orderdetails'				:val.id_orderdetails,

// 				'id_section'					:val.id_section,

// 				'id_sub_design'					:val.id_sub_design,

// 				'id_tag_mapping'				:val.id_tag_mapping,

// 				'item_rate'						:val.item_rate,

// 				'id_sub_design'					:val.id_sub_design,

// 				'label'							:val.label,

// 				'less_wt'						:val.less_wt,

// 				'lot_product'					:val.lot_product,

// 				'lot_received_at'				:val.lot_received_at,

// 				'market_rate_field'				:val.market_rate_field,

// 				'metal_type'					:val.metal_type,

// 				'net_wt'						:val.net_wt,

// 				'ord_rate_type'					:val.ord_rate_type,

// 				'order_no'						:val.order_no,

// 				'order_rate_per_grm'			:val.order_rate_per_grm,

// 				'piece'							:(parseFloat(item_balance_weight) == parseFloat(val.gross_wt)  ? val.piece : 0),

// 				'product_name'					:val.product_name,

// 				'product_short_code'			:val.product_short_code,

// 				'purity'						:val.purity,

// 				'purname'						:val.purname,

// 				'quality_id'					:val.quality_id,

// 				'rate_field'					:val.rate_field,

// 				'retail_max_wastage_percent'    :val.retail_max_wastage_percent,
				
// 				'max_va_per'   					:val.max_va_per,
				
// 				'act_wast_per'   				:val.act_wast_per,
				
// 				'wast_slb_value'   				:val.wast_slb_value,

// 				'sales_mode'					:val.sales_mode,

// 				'sales_value'					:val.sales_value,

// 				'scheme_closure_benefit'		:val.scheme_closure_benefit,

// 				'section_name'					:val.section_name,

// 				'size'							:val.size,

// 				'size_name'						:val.size_name,

// 				'stone_price'					:val.stone_price,

// 				'stone_type'					:val.stone_type,

// 				'sub_design_name'				:val.sub_design_name,

// 				'tag_id'						:val.tag_id,

// 				'tag_mc_type'					:val.tag_mc_type,

// 				'tag_mc_value'					:mc,

// 				'tag_other_itm_amount'			:val.tag_other_itm_amount,

// 				'tag_purchase_cost'				:val.tag_purchase_cost,

// 				'tag_status'					:val.tag_status,

// 				'tax_group_id'					:val.tax_group_id,

// 				'tax_percentage'				:val.tax_percentage,

// 				'tgi_calculation'				:val.tgi_calculation,

// 				'uom'							:val.uom,

// 				'uom_id'						:val.uom_id,

// 				'stone_details': split_stn_details,

// 				'other_metal_details': split_other_metal_details,

// 				'tag_images'					:[],

// 				'is_split_row'					: (parseFloat(item_balance_weight) <= parseFloat(val.gross_wt)  ? 0 :1 ),

// 				});

// 				//applied_closing_weight+=parseFloat(item_balance_weight);

// 			}

		   

// 		});

// 		$('#estimation_tag_details > tbody').empty();

// 		$.each(tag_details, function (key, ival) {

// 			append_tag_details(ival);

// 		});

// 	} else {

// 		$('#estimation_tag_details > tbody').empty();

// 		$.each(tag_split_details, function (key, ival) {

// 			append_tag_details(ival);

// 		});

// 	}

//     setTimeout(function () {

//         calculatetag_SaleValue();

//     }, 1000)



//     console.log(tag_details);

// }

function set_tag_split_details() {

	tag_details = [];

	var total_closing_amount = 0;

	var total_closing_weight = 0;

	var retail_max_wastage_percent = 0;

	var max_va_per = 0;

	var id_wast_slab = 0;

	var blk_dic = 0;

	var wastArr = [];

	var scheme_avg_rate = 0;

	var rate_type = 0;

	var split_wt_type = $('#tag_split_wt').val();



	// Capture manually edited rates before processing


	$('#estimation_tag_details > tbody > tr').each(function () {

		const tagId = $(this).find('.est_tag_id').val();

		const rate = $(this).find('.market_rate_value').val();

		retail_max_wastage_percent = $(this).find('.wastage_max_per').val();

		max_va_per = $(this).find('.tag_act_va_per').val();

		id_wast_slab = $(this).find('.id_wastage_slab').val();

		blk_dic = $(this).find('.blk_disc_apld').val();

		act_wast_per = $(this).find('.act_va_per').val();

		wast_slb_value = $(this).find('.wast_slb_val').val();

		if (tagId && rate) {

			manualRates[tagId] = rate;

		}

		wastArr.push({
			tagId: tagId,
			retail_max_wastage_percent: retail_max_wastage_percent,
			max_va_per: max_va_per,
			id_wast_slab: id_wast_slab,
			blk_dic_per: blk_dic,
			act_wast_per: act_wast_per,
			wast_slb_value: wast_slb_value
		});

	});

	$.each(tag_split_details, function (key, ival) {

		// Sanitization - Ensure base weights are numbers
		ival.gross_wt = parseFloat(ival.gross_wt) || 0;
		ival.net_wt = parseFloat(ival.net_wt) || 0;
		ival.less_wt = parseFloat(ival.less_wt) || 0;

		if(split_wt_type == 1){
			ival.balance_weight = ival.gross_wt;
		}
		else{
			ival.balance_weight = ival.net_wt;
		}

		$.each(wastArr, function (item, val) {

			if (ival.tag_id == val.tagId) {

				ival.retail_max_wastage_percent = val.retail_max_wastage_percent;

				ival.max_va_per = val.max_va_per;

				if (ival.id_wast_slab === undefined || ival.blk_dic_per === undefined || ival.wast_slb_value === undefined) {

					ival.id_wast_slab = val.id_wast_slab;
					ival.blk_dic_per = val.blk_dic_per;
					ival.wast_slb_value = val.wast_slb_value;
					ival.act_wast_per = val.act_wast_per;

				}

			}

		});

	});

	var scheme_closing_weight = 0;

	$('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

		var curRow = $(this).closest("tr");

		var closing_weight = parseFloat(curRow.find(".closing_weight").val()) || 0;
		var closing_amount = parseFloat(curRow.find(".closing_amount").val()) || 0;
		var bonus_percent = parseFloat(curRow.find(".bonus_percent").val()) || 0;


		// Sum up total closing weight and amount
		// if (bonus_percent > 0) {
			scheme_closing_weight += closing_weight;
			total_closing_weight += closing_weight;
			total_closing_amount += closing_amount;
		// }

		rate_type = parseFloat(curRow.find('.rate_type').val());
	});

	// Calculate scheme_avg_rate (prevent division by zero)
	var scheme_avg_rate = (total_closing_amount > 0 && scheme_closing_weight > 0)
		? (total_closing_amount / scheme_closing_weight).toFixed(2)
		: 0;

	console.log("Scheme Average Rate: ", scheme_avg_rate);

	
	var applied_closing_weight = 0;
	
	var total_balance_applied_weight = 0;
	
	
	
	var savings_in_making_charge = 0;
	
	
	
	var savings_in_wastage = 0;
	
	$('#estimation_chit_details > tbody  > tr').each(function (index, tr) {

		var curRow = $(this).closest("tr");


		$.each(tag_split_details, function (key, val) {



			var isTagsplitted = false;

			if (tag_details.length > 0) {

				$.each(tag_details, function (idx, tag) {

					if (tag.value != undefined) {

						if (tag.value == val.value) {



							if (val.balance_weight <= 0) {

								isTagsplitted = true;

							}



						}

					}

				});

			}

			if (total_closing_weight > 0 && (!isTagsplitted)) {



				var item_weight = parseFloat(val.balance_weight) || 0;

				total_balance_applied_weight = parseFloat(total_closing_weight || 0) - parseFloat(applied_closing_weight || 0);

				if ((parseFloat(total_balance_applied_weight) < parseFloat(item_weight)) && (total_balance_applied_weight > 0) && (val.calculation_based_on != 3) && (val.id_metal == 1) && (parseFloat(curRow.find('.bonus_percent').val()) > 0)) { //&& (val.id_metal == 1) (parseFloat(total_balance_applied_weight) < parseFloat(item_weight)) &&

					var item_balance_weight = parseFloat(item_weight - total_balance_applied_weight);

					//	val.balance_weight = item_balance_weight;

					var split_stn_details = [];

					var split_other_metal_details = [];

					var charges_details = [];

					var stone_details = val.stone_details;

					var other_metal_details = val.other_metal_details;

					var tag_stn_weight = 0;

					var other_wt = 0;

					var tag_wt = (split_wt_type == 1 ? val.gross_wt : val.net_wt);



					if (stone_details.length > 0) {

						$.each(stone_details, function (k, val) {

							var stn_weight = val.stone_wt;

							var stone_wt = 0;

							if (stn_weight > 0 && tag_wt > 0) {

								var stn_weight_per = ((parseFloat(total_balance_applied_weight) / parseFloat(tag_wt)) * 100);

								stn_weight = parseFloat((stn_weight * stn_weight_per) / 100).toFixed(3);

							} else {
								stn_weight = 0;
							}



							var stone_pcs = (tag_wt == item_weight) ? val.stone_pcs : 0;



							$.each(uom_details, function (key, itemk) {



								if (itemk.uom_id == val.stone_uom_id) {



									if (val.show_in_lwt == 1) {



										if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram



										{



											stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(itemk.divided_by_value));



										} else {



											stone_wt = stn_weight;



										}



										tag_stn_weight += parseFloat(stone_wt);



									}

								}
							})



							split_stn_details.push({

								'show_in_lwt': val.show_in_lwt,

								'stone_pcs': stone_pcs,

								'stone_rate': val.stone_rate,

								'stone_cal_type': val.stone_cal_type,

								'stone_id': val.stone_id,

								'tag_id': val.tag_id,

								'stone_uom_id': val.stone_uom_id,

								'stone_wt': stn_weight,

								"act_stn_pcs": val.act_stn_pcs,

								"act_stone_wt": val.act_stone_wt,

								'stone_price': val.amount



							});

						});

						console.log(split_stn_details);

					}



					if (val.charges_details.length > 0) {

						$.each(val.charges_details, function (skey, sitem) {

							charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display, "tag_id": sitem.tag_id });

						});

					}





					if (other_metal_details.length > 0) {

						$.each(other_metal_details, function (k, val) {

							var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

							if (tag_other_itm_grs_weight > 0 && item_weight > 0) {

								var other_metal_weight_per = parseFloat((parseFloat(total_balance_applied_weight) / parseFloat(item_weight)) * parseFloat(100)).toFixed(2);

								tag_other_itm_grs_weight = parseFloat((parseFloat(tag_other_itm_grs_weight) * parseFloat(other_metal_weight_per)) / 100).toFixed(3);

							} else {
								tag_other_itm_grs_weight = 0;
							}



							other_wt += parseFloat(tag_other_itm_grs_weight);

							split_other_metal_details.push({

								'id_metal': val.id_metal,

								'tag_other_itm_amount': val.tag_other_itm_amount,

								'tag_other_itm_cal_type': val.tag_other_itm_cal_type,

								'tag_other_itm_grs_weight': tag_other_itm_grs_weight,

								'tag_other_itm_id': val.tag_other_itm_id,

								'tag_other_itm_mc': 0,

								'tag_other_itm_metal_id': val.tag_other_itm_metal_id,

								'tag_other_itm_pcs': val.tag_other_itm_pcs,

								'tag_other_itm_pur_id': val.tag_other_itm_pur_id,

								'tag_other_itm_rate': val.tag_other_itm_rate,

								'tag_other_itm_tag_id': val.tag_other_itm_tag_id,

								'tag_other_itm_uom': val.tag_other_itm_uom,

								'tag_other_itm_wastage': 0,

								"purname": val.purname,

								"metal_name": val.metal_name,

							});

						});

					}



					var va_per = 0;

					var calculation_type = val.calculation_based_on;

					var gross_wt = (split_wt_type == 1 ? total_balance_applied_weight : parseFloat(parseFloat(total_balance_applied_weight || 0) + parseFloat(tag_stn_weight || 0) + parseFloat(other_wt || 0)).toFixed(3));

                    var net_wt = (split_wt_type == 1 ? (parseFloat(total_balance_applied_weight || 0) - parseFloat(other_wt || 0) - parseFloat(tag_stn_weight || 0)) :(parseFloat(total_balance_applied_weight || 0)));

					var mc_type = val.tag_mc_type;
					
					var mc  = ( (split_wt_type == 1 ? val.gross_wt == val.balance_weight : val.net_wt == val.balance_weight) && val.tag_mc_type == 1 ? val.tag_mc_value : (val.tag_mc_type == 2 ? val.tag_mc_value : 0));

					var tot_wastage = val.retail_max_wastage_percent;

					var wast_wt_discount = 0;

					var mc_discount = 0;



					if (calculation_type == 0) {



						var wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100));



						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



						var wast_gt = wast_wgt - va_dis_wt;



						var va_per = parseFloat((wast_gt * 100) / gross_wt);



						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * 1));



						var mc_val = parseFloat(parseFloat(mc) - parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100))).toFixed(2);



						wast_wt_discount = va_dis_wt;



						mc_discount = parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



					} else if (calculation_type == 1) {



						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



						var wast_gt = wast_wgt - va_dis_wt;



						var va_per = parseFloat((wast_gt * 100) / net_wt);





						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



						var mc_val = parseFloat(parseFloat(mc) - parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100))).toFixed(2);



						wast_wt_discount = va_dis_wt;



						mc_discount = parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100));





					} else if (calculation_type == 2) {



						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



						var wast_gt = wast_wgt - va_dis_wt;



						var va_per = parseFloat((wast_gt * 100) / net_wt);



						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



						var mc_val = parseFloat(parseFloat(mc) - parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100))).toFixed(2);



						wast_wt_discount = va_dis_wt;



						mc_discount = parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100));





					} else {



						var va_per = 0;



						var tot_mc = 0;



						var mc_val = 0;



						wast_wt_discount = 0;



						mc_discount = 0;



					}



					savings_in_wastage += parseFloat(isNaN(wast_wt_discount) ? 0 : wast_wt_discount);



					savings_in_making_charge += parseFloat(isNaN(mc_discount) ? 0 : mc_discount);





					va_per = parseFloat(isNaN(va_per) ? tot_wastage : va_per).toFixed(2);



					mc_val = parseFloat(isNaN(mc_val) ? mc : mc_val).toFixed(2);



					//	var va_per = (parseFloat(wast_wgt / (parseFloat(curRow.find('.bonus_percent').val())/100)) * 100)





					tag_details.push({

						'isTagsplitted': (total_balance_applied_weight > 0 ? 1 : 0),

                        'gross_wt': gross_wt,

						'value': val.tag_id,

						'item_type': ((split_wt_type == 1 ? val.gross_wt == val.balance_weight : val.net_wt == val.balance_weight ) ? 0:2),

						'calculation_based_on': val.calculation_based_on,

						'cat_id': val.cat_id,

						'certification_cost': val.certification_cost,

						'charge_value': ((split_wt_type == 1 ? val.gross_wt == val.balance_weight : val.net_wt == val.balance_weight ) ? val.charge_value : 0),

                        'charges_details': ((split_wt_type == 1 ? val.gross_wt == val.balance_weight : val.net_wt == val.balance_weight ) ? charges_details : []),

						'cost_center': val.cost_center,

						'current_branch': val.current_branch,

						'design_id': val.design_id,

						'design_name': val.design_name,

						'halmarking': val.halmarking,

						'huid': val.huid,

						'id_mapping_details': val.id_mapping_details,

						'id_metal': val.id_metal,

						'id_orderdetails': val.id_orderdetails,

						'id_section': val.id_section,

						'id_sub_design': val.id_sub_design,

						'id_tag_mapping': val.id_tag_mapping,

						'item_rate': val.item_rate,

						'id_sub_design': val.id_sub_design,

						'label': val.label,

						'less_wt': val.less_wt,

						'lot_product': val.lot_product,

						'lot_received_at': val.lot_received_at,

						'market_rate_field': val.market_rate_field,

						'metal_type': val.metal_type,

						'net_wt': net_wt,

						'ord_rate_type': val.ord_rate_type,

						'order_no': val.order_no,

						'order_rate_per_grm': val.order_rate_per_grm,

						'piece':((split_wt_type == 1 ? (parseFloat(val.balance_weight) == parseFloat(val.gross_wt)) : (parseFloat(val.balance_weight) == parseFloat(val.net_wt)))  ? val.piece : 0),

						//  'piece': val.piece,

						'product_name': val.product_name,

						'product_short_code': val.product_short_code,

						'purity': val.purity,

						'purname': val.purname,

						'quality_id': val.quality_id,

						'rate_field': val.rate_field,

						'chit_avg_rate': scheme_avg_rate,

						'chit_rate_type': rate_type,


						//'retail_max_wastage_percent'    :val.retail_max_wastage_percent,

						'retail_max_wastage_percent': va_per,

						'max_va_per': val.max_va_per,

						'id_wast_slab': val.id_wast_slab,

						'blk_dic_per': val.blk_dic_per,

						'act_wast_per': val.act_wast_per,

						'wast_slb_value': val.wast_slb_value,

						'sales_mode': val.sales_mode,

						'sales_value': val.sales_value,

						'scheme_closure_benefit': val.scheme_closure_benefit,

						'section_name': val.section_name,

						'size': val.size,

						'size_name': val.size_name,

						'stone_price': val.stone_price,

						'stone_type': val.stone_type,

						'sub_design_name': val.sub_design_name,

						'tag_id': val.tag_id,

						'tag_mc_type': val.tag_mc_type,

						'tag_mc_value': mc_val,

						'tag_other_itm_amount': val.tag_other_itm_amount,

						'tag_purchase_cost': val.tag_purchase_cost,

						'tag_status': val.tag_status,

						'tax_group_id': val.tax_group_id,

						'tax_percentage': val.tax_percentage,

						'tgi_calculation': val.tgi_calculation,

						'uom': val.uom,

						'uom_id': val.uom_id,

						'stone_details': split_stn_details,

						'other_metal_details': split_other_metal_details,

						//'stone_details': ( parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? stone_details:[]),

						//'other_metal_details': (  parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? other_metal_details:[]),

						'tag_images': [],

						'is_split_row': 1,

					});

					applied_closing_weight += parseFloat(item_weight);

					if (item_balance_weight > 0) {

						val.balance_weight = item_balance_weight;

						return false;

					}



				}

				else if ((parseFloat(total_balance_applied_weight) > parseFloat(item_weight)) && (val.calculation_based_on != 3) && (val.id_metal == 1) && (parseFloat(curRow.find('.bonus_percent').val()) > 0)) {



					var split_stn_details = [];

					var stone_details = val.stone_details;

					var other_metal_details = val.other_metal_details;

					var split_other_metal_details = [];

					var charges_details = [];

					var tag_stn_weight = 0;

					var other_wt = 0;

					/*

					if(stone_details.length>0){

						$.each(stone_details,function(k,val){

							var stn_weight = val.stone_wt;

							var stone_pcs = (val.gross_wt == val.balance_weight)?val.stone_pcs:0;



							$.each(uom_details, function (key, itemk) {



								if (itemk.uom_id == val.stone_uom_id) {



									if (val.show_in_lwt == 1) {



										if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram

	

										{

	

											stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(val.divided_by_value));

	

										} else {

	

											stone_wt = stn_weight;

	

										}

	

										tag_stn_weight += parseFloat(stone_wt);

	

									}

							}})

							split_stn_details.push({

								'show_in_lwt':val.show_in_lwt,

								'stone_pcs':stone_pcs,

								'stone_rate':val.stone_rate,

								'stone_cal_type':val.stone_cal_type,

								'stone_id':val.stone_id,

								'tag_id':val.tag_id,

								'stone_uom_id':val.stone_uom_id,

								'stone_wt'	:stn_weight,

								"act_stn_pcs": val.act_stn_pcs,

								"act_stone_wt": val.act_stone_wt,

								'stone_price': val.amount

							//	'stone_price':(val.stone_cal_type ==2 ? parseFloat(parseFloat(val.stone_rate)*parseFloat(val.stone_pcs).toFixed(2)) :parseFloat(parseFloat(stn_weight)*parseFloat(val.stone_rate).toFixed(2))),

							});

						});

						console.log(split_stn_details);

					}

					*/



					var tag_wt = (split_wt_type == 1 ? val.gross_wt : val.net_wt);



					if (stone_details.length > 0) {

						$.each(stone_details, function (k, val) {

							var stn_weight = val.stone_wt;

							var stone_wt = 0;

							if (stn_weight > 0) {

								var stn_weight_per = parseFloat((parseFloat(item_weight) / parseFloat(tag_wt)) * parseFloat(100)).toFixed(2);

								stn_weight = parseFloat((parseFloat(stn_weight * stn_weight_per)) / 100).toFixed(3);

							}



							var stone_pcs = (tag_wt == item_weight) ? val.stone_pcs : 0;



							$.each(uom_details, function (key, itemk) {



								if (itemk.uom_id == val.stone_uom_id) {



									if (val.show_in_lwt == 1) {



										if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram



										{



											stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(itemk.divided_by_value));



										} else {



											stone_wt = stn_weight;



										}



										tag_stn_weight += parseFloat(stone_wt);



									}

								}
							})



							split_stn_details.push({

								'show_in_lwt': val.show_in_lwt,

								'stone_pcs': stone_pcs,

								'stone_rate': val.stone_rate,

								'stone_cal_type': val.stone_cal_type,

								'stone_id': val.stone_id,

								'tag_id': val.tag_id,

								'stone_uom_id': val.stone_uom_id,

								'stone_wt': stn_weight,

								"act_stn_pcs": val.act_stn_pcs,

								"act_stone_wt": val.act_stone_wt,

								'stone_price': val.amount



							});

						});

						console.log(split_stn_details);

					}



					if (val.charges_details.length > 0) {

						$.each(val.charges_details, function (skey, sitem) {

							charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display, "tag_id": sitem.tag_id });

						});

					}

					/*

					if(other_metal_details.length > 0)

					{

						$.each(other_metal_details,function(k,val){

							var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

							split_other_metal_details.push({

								'id_metal':val.id_metal,

								'tag_other_itm_amount':val.tag_other_itm_amount,

								'tag_other_itm_cal_type':val.tag_other_itm_cal_type,

								'tag_other_itm_grs_weight':tag_other_itm_grs_weight,

								'tag_other_itm_id':val.tag_other_itm_id,

								'tag_other_itm_mc':val.tag_other_itm_mc,

								'tag_other_itm_metal_id':val.tag_other_itm_metal_id,

								'tag_other_itm_pcs':val.tag_other_itm_pcs,

								'tag_other_itm_pur_id':val.tag_other_itm_pur_id,

								'tag_other_itm_rate':val.tag_other_itm_rate,

								'tag_other_itm_tag_id':val.tag_other_itm_tag_id,

								'tag_other_itm_uom':val.tag_other_itm_uom,

								'tag_other_itm_wastage':val.tag_other_itm_wastage,

								"purname": val.purname,

								"metal_name": val.metal_name,

							});

							other_wt += parseFloat(tag_other_itm_grs_weight);



						});

					}

					*/



					if (other_metal_details.length > 0) {

						$.each(other_metal_details, function (k, val) {

							var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

							if (tag_other_itm_grs_weight > 0) {

								var other_metal_weight_per = parseFloat((parseFloat(item_weight) / parseFloat(tag_wt)) * parseFloat(100)).toFixed(2);

								tag_other_itm_grs_weight = parseFloat((parseFloat(tag_other_itm_grs_weight) * parseFloat(other_metal_weight_per)) / 100).toFixed(3);

							}



							other_wt += parseFloat(tag_other_itm_grs_weight);

							split_other_metal_details.push({

								'id_metal': val.id_metal,

								'tag_other_itm_amount': val.tag_other_itm_amount,

								'tag_other_itm_cal_type': val.tag_other_itm_cal_type,

								'tag_other_itm_grs_weight': tag_other_itm_grs_weight,

								'tag_other_itm_id': val.tag_other_itm_id,

								'tag_other_itm_mc': 0,

								'tag_other_itm_metal_id': val.tag_other_itm_metal_id,

								'tag_other_itm_pcs': val.tag_other_itm_pcs,

								'tag_other_itm_pur_id': val.tag_other_itm_pur_id,

								'tag_other_itm_rate': val.tag_other_itm_rate,

								'tag_other_itm_tag_id': val.tag_other_itm_tag_id,

								'tag_other_itm_uom': val.tag_other_itm_uom,

								'tag_other_itm_wastage': 0,

								"purname": val.purname,

								"metal_name": val.metal_name,

							});

						});

					}



					var va_per = 0;

					var calculation_type = val.calculation_based_on;

					var gross_wt = val.gross_wt;

					var net_wt = (split_wt_type == 1 ? (parseFloat(val.gross_wt) - parseFloat(other_wt) - parseFloat(tag_stn_weight)) : val.net_wt);

					var mc_type = val.tag_mc_type;

					var mc  = ( (split_wt_type == 1 ? val.gross_wt == val.balance_weight : val.net_wt == val.balance_weight) && val.tag_mc_type == 1 ? val.tag_mc_value : (val.tag_mc_type == 2 ? val.tag_mc_value : 0));

					var tot_wastage = val.retail_max_wastage_percent;



					wast_wt_discount = 0



					mc_discount = 0





					if (calculation_type == 0) {



						var wast_wgt = parseFloat(parseFloat(gross_wt) * parseFloat(tot_wastage / 100));



						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



						var wast_gt = wast_wgt - va_dis_wt;



						var va_per = parseFloat((wast_gt * 100) / gross_wt);



						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * gross_wt) : parseFloat(mc * 1));



						var mc_val = parseFloat(parseFloat(mc) - parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100))).toFixed(2);



						wast_wt_discount = va_dis_wt;



						mc_discount = parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



					} else if (calculation_type == 1) {



						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



						var wast_gt = wast_wgt - va_dis_wt;



						var va_per = parseFloat((wast_gt * 100) / net_wt);





						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



						var mc_val = parseFloat(parseFloat(mc) - parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100))).toFixed(2);



						wast_wt_discount = va_dis_wt;



						mc_discount = parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100));





					} else if (calculation_type == 2) {



						var wast_wgt = parseFloat(parseFloat(net_wt) * parseFloat(tot_wastage / 100));



						var va_dis_wt = parseFloat(wast_wgt * (parseFloat(curRow.find('.bonus_percent').val()) / 100));



						var wast_gt = wast_wgt - va_dis_wt;



						var va_per = parseFloat((wast_gt * 100) / net_wt);



						var tot_mc = parseFloat(mc_type == 2 ? parseFloat(mc * net_wt) : parseFloat(mc * 1));



						var mc_val = parseFloat(parseFloat(mc) - parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100))).toFixed(2);



						wast_wt_discount = va_dis_wt;



						mc_discount = parseFloat(mc * (parseFloat(curRow.find('.bonus_percent').val()) / 100));





					} else {



						var va_per = 0;



						var tot_mc = 0;



						var mc_val = 0;



						wast_wt_discount = 0;



						mc_discount = 0;



					}



					savings_in_wastage += parseFloat(isNaN(wast_wt_discount) ? 0 : wast_wt_discount);



					savings_in_making_charge += parseFloat(isNaN(mc_discount) ? 0 : mc_discount);



					va_per = parseFloat(isNaN(va_per) ? tot_wastage : va_per).toFixed(2);



					mc_val = parseFloat(isNaN(mc_val) ? mc : mc_val).toFixed(2);





					tag_details.push({

						'isTagsplitted': (total_balance_applied_weight > 0 && val.calculation_based_on != 3 ? 1 : 0),

						'item_type'	:((split_wt_type == 1 ? val.gross_wt == val.balance_weight : val.net_wt == val.balance_weight) ? 0:2),
						'gross_wt'	:(split_wt_type == 1 ? val.balance_weight : parseFloat((parseFloat(val.balance_weight) + parseFloat(tag_stn_weight) + parseFloat(other_wt))).toFixed(3)) ,

						'value': val.tag_id,

						'calculation_based_on': val.calculation_based_on,

						'cat_id': val.cat_id,

						'certification_cost': val.certification_cost,

						'charge_value': (val.gross_wt == val.balance_weight ? val.charge_value : 0),

						'charges_details': (val.gross_wt == val.balance_weight ? charges_details : []),

						'cost_center': val.cost_center,

						'current_branch': val.current_branch,

						'design_id': val.design_id,

						'design_name': val.design_name,

						'halmarking': val.halmarking,

						'huid': val.huid,

						'id_mapping_details': val.id_mapping_details,

						'id_metal': val.id_metal,

						'id_orderdetails': val.id_orderdetails,

						'id_section': val.id_section,

						'id_sub_design': val.id_sub_design,

						'id_tag_mapping': val.id_tag_mapping,

						'item_rate': val.item_rate,

						'id_sub_design': val.id_sub_design,

						'label': val.label,

						'less_wt': val.less_wt,

						'lot_product': val.lot_product,

						'lot_received_at': val.lot_received_at,

						'market_rate_field': val.market_rate_field,

						'metal_type': val.metal_type,

						'net_wt':(split_wt_type != 1 ? val.net_wt : parseFloat((parseFloat(val.balance_weight) + parseFloat(tag_stn_weight) + parseFloat(other_wt))).toFixed(3)) ,

						'ord_rate_type': val.ord_rate_type,

						'order_no': val.order_no,

						'order_rate_per_grm': val.order_rate_per_grm,

						'piece'	:((split_wt_type == 1 ? (parseFloat(val.balance_weight) == parseFloat(val.gross_wt)) : (parseFloat(val.balance_weight) == parseFloat(val.net_wt)))  ? val.piece : 0),

						//'piece'							:val.piece,

						'product_name': val.product_name,

						'product_short_code': val.product_short_code,

						'purity': val.purity,

						'purname': val.purname,

						'quality_id': val.quality_id,

						'rate_field': val.rate_field,

						'chit_avg_rate': scheme_avg_rate,

						'chit_rate_type': rate_type,

						'retail_max_wastage_percent': va_per,

						'max_va_per': val.max_va_per,

						'id_wast_slab': val.id_wast_slab,

						'blk_dic_per': val.blk_dic_per,

						'act_wast_per': val.act_wast_per,

						'wast_slb_value': val.wast_slb_value,

						'sales_mode': val.sales_mode,

						'sales_value': val.sales_value,

						'scheme_closure_benefit': val.scheme_closure_benefit,

						'section_name': val.section_name,

						'size': val.size,

						'size_name': val.size_name,

						'stone_price': val.stone_price,

						'stone_type': val.stone_type,

						'sub_design_name': val.sub_design_name,

						'tag_id': val.tag_id,

						'tag_mc_type': val.tag_mc_type,

						'tag_mc_value': mc_val,

						'tag_other_itm_amount': val.tag_other_itm_amount,

						'tag_purchase_cost': val.tag_purchase_cost,

						'tag_status': val.tag_status,

						'tax_group_id': val.tax_group_id,

						'tax_percentage': val.tax_percentage,

						'tgi_calculation': val.tgi_calculation,

						'uom': val.uom,

						'uom_id': val.uom_id,

						//'stone_details': ( parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? stone_details:[]),

						//'other_metal_details': (  parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? other_metal_details:[]),

						'stone_details': split_stn_details,

						'other_metal_details': split_other_metal_details,

						'tag_images': [],

						'is_split_row': 1,


					});

					if (val.calculation_based_on != 3) {

						applied_closing_weight += parseFloat(val.balance_weight);

					}



					val.balance_weight = 0;







				}

			}







			curRow.find('.savings_in_wastage').val(savings_in_wastage);

			curRow.find('.savings_in_mcvalue').val(savings_in_making_charge)

		});

	});



	if (tag_details.length > 0) {

		$.each(tag_split_details, function (key, val) {

			if (val.balance_weight > 0) {

				//append_tag_details(ival);

				var item_weight = val.balance_weight;

				var item_balance_weight = val.balance_weight;

				var split_stn_details = [];

				var split_other_metal_details = [];

				var stone_details = val.stone_details;

				var other_metal_details = val.other_metal_details;

				var charges_details = [];

				var tag_stn_weight = 0;

				var other_wt = 0;

				if (val.charges_details.length > 0) {

					$.each(val.charges_details, function (skey, sitem) {

						charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display, "tag_id": sitem.tag_id });

					});

				}

				/*

				if(stone_details.length>0){

					$.each(stone_details,function(k,val){

						var stn_weight = val.stone_wt;

						// if(stn_weight > 0)

						// {

						// 	var stn_weight_per = parseFloat((parseFloat(item_balance_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

						// 	stn_weight 	   = parseFloat((parseFloat(stn_weight)*parseFloat(stn_weight_per))/100).toFixed(3);

						// }



						var stone_pcs = (val.gross_wt == val.balance_weight) ? val.stone_pcs:0;



						split_stn_details.push({

							'show_in_lwt':val.show_in_lwt,

							'stone_pcs':stone_pcs,

							'stone_rate':val.stone_rate,

							'stone_cal_type':val.stone_cal_type,

							'stone_id':val.stone_id,

							'tag_id':val.tag_id,

							'stone_uom_id':val.stone_uom_id,

							'stone_wt'	:stn_weight,

							"act_stn_pcs": val.act_stn_pcs,

							"act_stone_wt": val.act_stone_wt,

							"stone_price": val.amount,

							"quality_id": val.quality_id

							//'stone_price':(val.stone_cal_type ==2 ? 0 :parseFloat(parseFloat(stn_weight)*parseFloat(val.stone_rate).toFixed(2))),

						});

					});

					console.log(split_stn_details);

				}

				if(other_metal_details.length > 0)

				{

					$.each(other_metal_details,function(k,val){

						var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

						// if(tag_other_itm_grs_weight > 0)

						// {

						// 	var other_metal_weight_per = parseFloat((parseFloat(item_balance_weight)/parseFloat(item_weight))*parseFloat(100)).toFixed(2);

						// 	tag_other_itm_grs_weight 	   = parseFloat((parseFloat(tag_other_itm_grs_weight)*parseFloat(other_metal_weight_per))/100).toFixed(3);

						// }

						split_other_metal_details.push({

							'id_metal':val.id_metal,

							'tag_other_itm_amount':val.tag_other_itm_amount,

							'tag_other_itm_cal_type':val.tag_other_itm_cal_type,

							'tag_other_itm_grs_weight':tag_other_itm_grs_weight,

							'tag_other_itm_id':val.tag_other_itm_id,

							'tag_other_itm_mc':val.tag_other_itm_mc,

							'tag_other_itm_metal_id':val.tag_other_itm_metal_id,

							'tag_other_itm_pcs':val.tag_other_itm_pcs,

							'tag_other_itm_pur_id':val.tag_other_itm_pur_id,

							'tag_other_itm_rate':val.tag_other_itm_rate,

							'tag_other_itm_tag_id':val.tag_other_itm_tag_id,

							'tag_other_itm_uom':val.tag_other_itm_uom,

							'tag_other_itm_wastage':val.tag_other_itm_wastage,

							"purname": val.purname,

							"metal_name": val.metal_name,

						});

					});

				}

				*/



				var tag_wt = (split_wt_type == 1 ? val.gross_wt : val.net_wt);





				if (stone_details.length > 0) {

					$.each(stone_details, function (k, val) {

						var stn_weight = val.stone_wt;

						var stone_wt = 0;

						if (stn_weight > 0 && tag_wt > 0) {

							var stn_weight_per = parseFloat((parseFloat(item_weight) / parseFloat(tag_wt)) * parseFloat(100)).toFixed(2);

							stn_weight = parseFloat((parseFloat(stn_weight * stn_weight_per)) / 100).toFixed(3);

						} else {
							stn_weight = 0;
						}



						var stone_pcs = (tag_wt == item_weight) ? val.stone_pcs : 0;



						$.each(uom_details, function (key, itemk) {



							if (itemk.uom_id == val.stone_uom_id) {



								if (val.show_in_lwt == 1) {



									if ((itemk.uom_short_code == 'CT') && (itemk.divided_by_value != null && itemk.divided_by_value != '')) //For Carat Need to convert into gram



									{



										stone_wt = parseFloat(parseFloat(stn_weight) / parseFloat(itemk.divided_by_value));



									} else {



										stone_wt = stn_weight;



									}



									tag_stn_weight += parseFloat(stone_wt);



								}

							}
						})



						split_stn_details.push({

							'show_in_lwt': val.show_in_lwt,

							'stone_pcs': stone_pcs,

							'stone_rate': val.stone_rate,

							'stone_cal_type': val.stone_cal_type,

							'stone_id': val.stone_id,

							'tag_id': val.tag_id,

							'stone_uom_id': val.stone_uom_id,

							'stone_wt': stn_weight,

							"act_stn_pcs": val.act_stn_pcs,

							"act_stone_wt": val.act_stone_wt,

							'stone_price': val.amount



						});

					});

					console.log(split_stn_details);

				}



				if (other_metal_details.length > 0) {

					$.each(other_metal_details, function (k, val) {

						var tag_other_itm_grs_weight = val.tag_other_itm_grs_weight;

						if (tag_other_itm_grs_weight > 0 && item_weight > 0 && tag_wt > 0) {

							var other_metal_weight_per = parseFloat((parseFloat(tag_wt) / parseFloat(item_weight)) * parseFloat(100)).toFixed(2);

							tag_other_itm_grs_weight = parseFloat((parseFloat(tag_other_itm_grs_weight) * parseFloat(other_metal_weight_per)) / 100).toFixed(3);

						} else {
							tag_other_itm_grs_weight = 0;
						}



						other_wt += parseFloat(tag_other_itm_grs_weight);

						split_other_metal_details.push({

							'id_metal': val.id_metal,

							'tag_other_itm_amount': val.tag_other_itm_amount,

							'tag_other_itm_cal_type': val.tag_other_itm_cal_type,

							'tag_other_itm_grs_weight': tag_other_itm_grs_weight,

							'tag_other_itm_id': val.tag_other_itm_id,

							'tag_other_itm_mc': 0,

							'tag_other_itm_metal_id': val.tag_other_itm_metal_id,

							'tag_other_itm_pcs': val.tag_other_itm_pcs,

							'tag_other_itm_pur_id': val.tag_other_itm_pur_id,

							'tag_other_itm_rate': val.tag_other_itm_rate,

							'tag_other_itm_tag_id': val.tag_other_itm_tag_id,

							'tag_other_itm_uom': val.tag_other_itm_uom,

							'tag_other_itm_wastage': 0,

							"purname": val.purname,

							"metal_name": val.metal_name,

						});

					});

				}

				var mc  = ( (split_wt_type == 1 ? val.gross_wt == val.balance_weight : val.net_wt == val.balance_weight ) && val.tag_mc_type == 1 ? val.tag_mc_value : (val.tag_mc_type == 2 ? val.tag_mc_value : 0));





				tag_details.push({

					'isTagsplitted'	:(split_wt_type == 1 ? (parseFloat(item_balance_weight) == parseFloat(val.gross_wt)): (parseFloat(item_balance_weight) == parseFloat(val.net_wt))  ? 0 :1 ),

					'item_type': (parseFloat(item_balance_weight) == parseFloat(val.gross_wt) ? 0 : 2),

					'gross_wt'						:(split_wt_type == 1 ? parseFloat(item_balance_weight || 0).toFixed(3) : parseFloat(parseFloat(item_balance_weight || 0) + parseFloat(other_wt || 0) + parseFloat(tag_stn_weight || 0)).toFixed(3) ),

					'value': val.tag_id,

					'calculation_based_on': val.calculation_based_on,

					'cat_id': val.cat_id,

					'certification_cost': val.certification_cost,

					'charge_value': (parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? val.charge_value : 0),

					'charges_details': (parseFloat(val.gross_wt) == parseFloat(val.balance_weight) ? charges_details : []),

					'cost_center': val.cost_center,

					'current_branch': val.current_branch,

					'design_id': val.design_id,

					'design_name': val.design_name,

					'halmarking': val.halmarking,

					'huid': val.huid,

					'id_mapping_details': val.id_mapping_details,

					'id_metal': val.id_metal,

					'id_orderdetails': val.id_orderdetails,

					'id_section': val.id_section,

					'id_sub_design': val.id_sub_design,

					'id_tag_mapping': val.id_tag_mapping,

					'item_rate': val.item_rate,

					'id_sub_design': val.id_sub_design,

					'label': val.label,

					'less_wt': val.less_wt,

					'lot_product': val.lot_product,

					'lot_received_at': val.lot_received_at,

					'market_rate_field': val.market_rate_field,

					'chit_avg_rate': scheme_avg_rate,

					'chit_rate_type': rate_type,

					'metal_type': val.metal_type,

					'net_wt': val.net_wt,

					'ord_rate_type': val.ord_rate_type,

					'order_no': val.order_no,

					'order_rate_per_grm': val.order_rate_per_grm,

					'piece': (parseFloat(item_balance_weight) == parseFloat(val.gross_wt) ? val.piece : 0),

					'product_name': val.product_name,

					'product_short_code': val.product_short_code,

					'purity': val.purity,

					'purname': val.purname,

					'quality_id': val.quality_id,

					'rate_field': val.rate_field,

					'retail_max_wastage_percent': val.retail_max_wastage_percent,

					'max_va_per': val.max_va_per,

					'act_wast_per': val.act_wast_per,

					'wast_slb_value': val.wast_slb_value,

					'sales_mode': val.sales_mode,

					'sales_value': val.sales_value,

					'scheme_closure_benefit': val.scheme_closure_benefit,

					'section_name': val.section_name,

					'size': val.size,

					'size_name': val.size_name,

					'stone_price': val.stone_price,

					'stone_type': val.stone_type,

					'sub_design_name': val.sub_design_name,

					'tag_id': val.tag_id,

					'tag_mc_type': val.tag_mc_type,

					'tag_mc_value': mc,

					'tag_other_itm_amount': val.tag_other_itm_amount,

					'tag_purchase_cost': val.tag_purchase_cost,

					'tag_status': val.tag_status,

					'tax_group_id': val.tax_group_id,

					'tax_percentage': val.tax_percentage,

					'tgi_calculation': val.tgi_calculation,

					'uom': val.uom,

					'uom_id': val.uom_id,

					'stone_details': split_stn_details,

					'other_metal_details': split_other_metal_details,

					'tag_images': [],

					'is_split_row': (parseFloat(item_balance_weight) <= parseFloat(val.gross_wt) ? 0 : 1),

				});

				if (val.calculation_based_on != 3) {

					applied_closing_weight += parseFloat(val.gross_wt);

				}

			}

		});

		$('#estimation_tag_details > tbody').empty();

		$.each(tag_details, function (key, ival) {

			append_tag_details(ival);

		});


	} else {

		$('#estimation_tag_details > tbody').empty();

		$.each(tag_split_details, function (key, ival) {

			append_tag_details(ival);

		});

	}

	setTimeout(function () {

		calculatetag_SaleValue();

	}, 1000)



	console.log(tag_details);

}

//Tag Split

//Set Tag Details in Table

function append_tag_details(val)

	{

        let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";

		let emp_select = $("#emp_select").val();

		var row = "";

		var stone_details = [];

		var other_metal_details = [];

		var charges_details = [];

		var tag_other_itm_amount = 0;

		var stone_amt = 0;

		var dia_amt = 0;

		var stone_amt = 0;

		var dia_amt = 0;
		var tag_other_full_amt= calculate_other_metal_amount(val.other_metal_details, $('<tr>'));

		$.each(val.stone_details, function (skey, sitem)

		{

			var max_stone_rate = sitem.stone_rate;

				$.each(stone_rate_settings,function(key,items)

				{
					var stone_pcs  = (isNaN(sitem.stone_pcs) || sitem.stone_pcs == '')  ? 0 : parseInt(sitem.stone_pcs);

					var stone_wt  = (isNaN(sitem.stone_wt) || sitem.stone_wt == '')  ? 0 : parseFloat(sitem.stone_wt);

				var stone_centwt = (stone_pcs > 0) ? parseFloat(((stone_wt) / (stone_pcs)) * 100).toFixed(3) : 0;



					if($('#branch_select').val() == items.id_branch && sitem.stone_type==items.stone_type && sitem.stone_id==items.stone_id && sitem.quality_id == items.quality_id && sitem.stone_uom_id==items.uom_id)

					{

						if(stone_centwt >= 0)

						{

							if(stone_centwt >= parseFloat(items.from_cent) && stone_centwt <= parseFloat(items.to_cent))



							{



								max_stone_rate = items.max_rate;



							}

						}

					}

				});

			var stone_price = 0;

			var tag_stn_price = 0;

			if (sitem.stone_cal_type == 1) // by wt

			{

				stone_price += (parseFloat(sitem.stone_wt) * parseFloat(max_stone_rate));

				if(sitem.stone_type == 1){
					dia_amt += stone_price;
				}
				else{
					stone_amt += stone_price;
				}

			}

			else

			{

				stone_price += (parseFloat(sitem.stone_pcs) * parseFloat(max_stone_rate));

				if(sitem.stone_type == 1){
					dia_amt += stone_price;
				}
				else{
					stone_amt += stone_price;
				}

			}

		tag_stn_price = stone_price;

			stone_details.push({

				"show_in_lwt": sitem.show_in_lwt,

				"stone_id": sitem.stone_id,

				"stones_type": sitem.stone_type,

				"stone_pcs": sitem.stone_pcs,

				"stone_wt": sitem.stone_wt,

				"act_stn_pcs": sitem.act_stn_pcs,

				"act_stone_wt": sitem.act_stone_wt,

				"stone_price": parseFloat(stone_price).toFixed(2),

				"stone_rate": max_stone_rate,

				"stone_uom_id": sitem.stone_uom_id,

				"stone_cal_type": sitem.stone_cal_type,

				"quality_id": sitem.quality_id,

				"max_stn_price" : parseFloat(tag_stn_price).toFixed(2)

			});

		});

		$.each(val.charges_details, function (skey, sitem) {

			charges_details.push({ "charge_id": sitem.charge_id, "charge_value": sitem.charge_value, "code_charge": sitem.code_charge, "tag_charge_id": sitem.tag_charge_id, "tag_display": sitem.tag_display,"tag_id": sitem.tag_id });

		});



		$.each(val.other_metal_details, function (skey, sitem) {

			tag_other_itm_amount += parseFloat(sitem.tag_other_itm_amount);

			other_metal_details.push({

				"tag_other_itm_id": sitem.tag_other_itm_id,

				"tag_other_itm_tag_id": sitem.tag_other_itm_tag_id,

				"tag_other_itm_metal_id": sitem.tag_other_itm_metal_id,

				"tag_other_itm_pur_id": sitem.tag_other_itm_pur_id,

				"tag_other_itm_grs_weight": sitem.tag_other_itm_grs_weight,

				"tag_other_itm_wastage": sitem.tag_other_itm_wastage,

				"tag_other_itm_uom": sitem.tag_other_itm_uom,

				"tag_other_itm_cal_type": sitem.tag_other_itm_cal_type,

				"tag_other_itm_mc": sitem.tag_other_itm_mc,

				"tag_other_itm_rate": sitem.tag_other_itm_rate,

				"purname": sitem.purname,

				"metal_name": sitem.metal_name,

				"tag_other_itm_pcs": sitem.tag_other_itm_pcs,

				"tag_other_itm_amount": sitem.tag_other_itm_amount,

			});

		});



		console.log(JSON.stringify(other_metal_details));



		let pur_mc = typeof val.po_details != 'undefined' && val.po_details != null && val.po_details.length != 0 ? (!(val.po_details[0].mc_value >= 0) || val.po_details[0].mc_value == null ? 0 : val.po_details[0].mc_value) : 0;

		let pur_va = typeof val.po_details != 'undefined' && val.po_details != null && val.po_details.length != 0 ? (!(val.po_details[0].item_wastage >= 0) || val.po_details[0].item_wastage == null ? 0 : val.po_details[0].item_wastage) : 0;

		let rate_field = val.rate_field;

		let rate_per_grm = 0;

		let act_wast_per = ((val.act_wast_per == '' || val.act_wast_per === undefined ) ? val.retail_max_wastage_percent  : val.act_wast_per);

		let wast_slb_value = ((val.wast_slb_value == '' || val.wast_slb_value === undefined ) ? '' : val.wast_slb_value);

		let id_va_slab = ((val.id_wast_slab == '' || val.id_wast_slab === undefined ) ? '' : val.id_wast_slab);

		let blk_disc = ((val.blk_dic_per == '' || val.blk_dic_per === undefined ) ? 0 : val.blk_dic_per);

		if(val.ord_rate_type==1 && val.order_rate_per_grm!=''){

			rate_per_grm = val.order_rate_per_grm;

		}

		else if (rate_field != null && rate_field != '' && rate_field != 'null' && val.stone_type==0) // For Ornaments Product

		{

			 if (manualRates[val.tag_id] && manualRates[val.tag_id] != '') {

				rate_per_grm = manualRates[val.tag_id]; // Use stored manual rate
				
			} else {

				rate_per_grm = (isNaN($('.' + rate_field).html()) || $('.' + rate_field).html() == '') ? 0 : $('.' + rate_field).html();

			}

			// Fallback: if DOM lookup returned 0, try purity_rate array (same as Home Bill)
			if (rate_per_grm == 0 && typeof purity_rate !== 'undefined' && purity_rate.length > 0) {
				$.each(purity_rate, function (k, purval) {
					if (purval.id_purity == val.purity && purval.id_metal == val.id_metal) {
						let fallback_rate_field = purval.rate_field;
						if (fallback_rate_field != null && fallback_rate_field != '') {
							let fallback_rate = (isNaN($('.' + fallback_rate_field).html()) || $('.' + fallback_rate_field).html() == '') ? 0 : $('.' + fallback_rate_field).html();
							if (fallback_rate > 0) {
								rate_per_grm = fallback_rate;
							}
						}
					}
				});
			}

		}

		else

		{

			$.each(loose_product_rate,function(key,items)

			{

				console.log('looseproduct',items);

				var product_centwt = 0;

				if(val.stone_type==2) // for diamond products

				{

					var pcs  = (isNaN(val.piece) || val.piece == '')  ? 0 : parseInt(val.piece);

					var grs_wt  = (isNaN(val.gross_wt) || val.gross_wt == '')  ? 0 : parseFloat(val.gross_wt);

					product_centwt = (pcs > 0) ? parseFloat(((grs_wt) / (pcs)) * 100).toFixed(3) : 0;

				}

				if($('#branch_select').val() == items.id_branch && val.lot_product == items.id_product && val.design_id == items.id_design && val.id_sub_design == items.id_sub_design && val.quality_id == items.quality_id && val.uom_id==items.uom_id)

				{

					if(product_centwt > 0)

					{

						if(product_centwt >= parseFloat(items.from_cent) && product_centwt <= parseFloat(items.to_cent))

						{

							rate_per_grm = items.max_rate;

						}

					}

					else

					{

						rate_per_grm = items.max_rate;

					}

				}

			});

		}
		
		if (val.chit_rate_type == 2 && val.is_split_row == 1 && val.isTagsplitted == 1) {
		rate_per_grm = val.chit_avg_rate;

		}

		var wast_wgt = parseFloat(parseFloat(val.net_wt) * parseFloat(val.retail_max_wastage_percent / 100)).toFixed(3);

		var select_emp = "<option value=''>-Select Employee-</option>";

		$.each(emp_details, function (pkey, emp) {

			let emp_selected = "";

			if (emp.id_employee == emp_select) {

				emp_selected = "selected = 'selected'";

			}

			select_emp += "<option " + emp_selected + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";

		});

		// var select_purity = "";

		// $.each(purities, function (pkey, sitem) {

		// 	var selected = "";

		// 	if (val.purity == sitem.id_purity) {

		// 		selected = "selected";

		// 	}

		// 	select_purity += "<option value='" + sitem.id_purity + "'" + selected + ">" + sitem.purity + "</option>";

		// });

		var mc_min_limit = 0;

		var va_min_limit = 0;

		if(val.mc_va_limit!=undefined){

			var mc_min_limit = parseFloat(val.mc_va_limit.mc_min);

			var va_min_limit = parseFloat(val.mc_va_limit.wastag_min);

		}

		var va_wt_min_limit = parseFloat(parseFloat(val.net_wt) * parseFloat(va_min_limit / 100)).toFixed(3);

		var tag_image = base_url + 'assets/img/no_image.png';

		if (val.tag_images.length > 0) {

			$.each(val.tag_images, function (key, val) {

				if (val.is_default == 1) {

					tag_image = base_url + 'assets/img/tag/' + val.image;

				}

			});

		}

		var split_tag =[];

		if(tag_details){

			split_tag = tag_details.filter((row)=>{



				return row.tag_code ==""



			});

		}

		let tagVaReadonly = '';

		let tagMCReadonly = '';

		let tagMCSelection = '';

		if (val.calculation_based_on == 3 || val.calculation_based_on == 4) {

			tagMCReadonly = 'readonly';

			tagVaReadonly = 'readonly';

			tagMCSelection = 'pointer-events : none;';

		} 

		else 

			{

			tagMCReadonly = mc_readonly; 

			tagVaReadonly = va_readonly;

			tagMCSelection = mc_type_selection;

			

		}



		var child_tag_details = [];

		var rowId = new Date().getTime();

		row += '<tr id="' + rowId + '">'

			+ '<td><input class="est_tag_name" type="text" name="est_tag[tag_name][]" value=' + val.label + ' placeholder="Enter tag code" required autocomplete="off"/><input class="isTagsplitted" type="hidden" name="est_tag[isTagsplitted][]" value="' + (val.isTagsplitted!=undefined ? val.isTagsplitted :0) + '" /><input class="item_type" type="hidden" name="est_tag[item_type][]" value="' + (val.item_type!=undefined ? val.item_type :0) + '" /><input class="id_mapping_details" type="hidden" name="est_tag[id_mapping_details][]" value="' + val.id_mapping_details + '" /><input class="est_tag_id" type="hidden" name="est_tag[tag_id][]" value=' + val.tag_id + ' /><input class="id_orderdetails" type="hidden" name="est_tag[id_orderdetails][]" value="' + val.id_orderdetails + '" /><input class="orderno" type="hidden" name="est_tag[orderno][]" value="' + val.order_no + '"/><input class="rate_field" type="hidden"  value="' + val.rate_field + '"/><input class="market_rate_field" type="hidden"  value="' + val.market_rate_field + '"/>	'+'<input class="is_split_row" type="hidden" name="est_tag[is_split_row][]" value="' + (val.is_split_row!=undefined ? val.is_split_row :0) +'"/><input type="hidden" class="is_chttopup_applied" value="0"/><input type="hidden" class="is_sr_applied" value="0"><input type="hidden" class="ChitUtilizedDetails"/><input type="hidden" class="SRUtilizedDetails"/><input type="hidden" class="partialy_utilized" value="0"/></td>'

			+ '<td><a href="#" onClick="create_new_empty_est_tag_merge_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" id="it_has_childtag" value= "" ><input type="hidden" name="est_tag[child_tag_details][]" id="child_tag_details" value="' + JSON.stringify(child_tag_details, null, 5) + '"/></td>'

			+ '<td><img src="' + tag_image + '" width="50" height="55"><a class="btn btn-secondary tag_images" data-toggle="modal" data-id="' + val.tag_id + '" id="edit"><i class="fa fa-eye" ></i></a></td>'

			+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_tag[item_emp_id][]" >' + select_emp + '</select></td>'

			+ '<td><input type="checkbox" class="partial" ' + (val.calculation_based_on == 3 || tag_details.length > 0 ? 'disabled' : '') + ' ><input type="hidden" class="is_partial"  name="est_tag[is_partial][]" value="'+((val.item_type==0 && val.isTagsplitted==1 )? 1 :0)+'"></td>'

			+ '<td><div class="prodct_name">' + val.product_name + '</div><input type="hidden" class="pro_id" name="est_tag[pro_id][]" value=' + val.lot_product + ' /><input type="hidden" class="metal_type" value=' + val.metal_type + '><input type="hidden" class="scheme_closure_benefit" value=' + val.scheme_closure_benefit + '></td>'

			+ '<td><div class="design_name">' + val.design_name + '</div><input type="hidden" class="design_id" name="est_tag[design_id][]" value=' + val.design_id + ' /></td>'

			+ '<td><div class="sub_design_name">' + val.sub_design_name + '</div><input type="hidden" class="id_sub_design" name="est_tag[id_sub_design][]" value=' + val.id_sub_design + ' /><input type="hidden" class="quality_id" name="est_tag[quality_id][]" value='+val.quality_id+'><input type="hidden" class="stone_type" value='+val.stone_type+'><input type="hidden" class="uom_id" value='+val.uom_id+'><input type="hidden" class="stone_calculation_based_on" value='+val.stone_calculation_based_on+'></td>'

			+ '<td><div class="section_name">' + val.section_name + '</div><input type="hidden" class="id_section" name="est_tag[id_section][]" value=' + val.id_section + ' /></td>'

			+ '<td><div class="huid">' + val.huid + '</div></td>'

			+ '<td><div class="order_no">' + val.order_no + '</td>'

			// + '<td><div class="purity">' + val.purname + '</div><input type="hidden" class="purity" name="est_tag[purity][]" value=' + val.purity + ' /></td>'

			// + '<td><select class="form-control tag_purity" id="select_purity" name="est_tag[purity][]" style="width:100px;"' + (profile.est_purity_edit == 1 ? '' : 'disabled') + '></select><input type="hidden" class="id_purity" value='+val.purity+'></td>'

			// + '<td><select class="form-control tag_purity" name="est_tag[purity][]" style="width:100px;"' + (profile.est_purity_edit == 1 ? '' : 'disabled') + '>' + select_purity + '</select></td>'

			+ '<td><span class="tag_purity_name" style="width:100px;">' + (isNaN(parseFloat(val.purname)) ? val.purname : parseFloat(val.purname).toFixed(2)) + '</span><input class="tag_id_purity" type="hidden" name="est_tag[purity][]" value="' + val.purity +'"/></td>'

			+ '<td><div class="sizes">' + val.size_name + '</div><input type="hidden" class="size" name="est_tag[size][]"  value="' + val.size + '" /></td>'

			//	+'<td><div class="pieces">'+val.piece+'</div><input type="hidden" class="piece" name="est_tag[piece][]" value='+val.piece+' /></td>'

			+ '<td><input type="number" class="form-control piece" name="est_tag[piece][]"  style="width:80px;" value=' + val.piece + ' readonly /><input type="hidden" class="act_piece" name="est_tag[act_piece][]" value=' + val.piece + ' readonly /></td>'

			+ '<td><input type="text" class="form-control gwt" name="est_tag[gwt][]" step="any" value=' + (isNaN(parseFloat(val.gross_wt)) ? '0.000' : parseFloat(val.gross_wt).toFixed(3)) + ' readonly style="width:80px;" /><input type="hidden" class="cur_gwt" name="est_tag[cur_gwt][]" value=' + (isNaN(parseFloat(val.gross_wt)) ? '0.000' : parseFloat(val.gross_wt).toFixed(3)) + ' /><input type="hidden" class="act_gwt" value=' + (isNaN(parseFloat(val.gross_wt)) ? '0.000' : parseFloat(val.gross_wt).toFixed(3)) + ' /></td>'

			//+'<td><input type="text" class="lwt" name="est_tag[lwt][]" step="any" value='+val.less_wt+' readonly/></td>'

			+ '<td><div class="form-group"><div class="input-group "><input class="form-control lwt" value=' + (isNaN(parseFloat(val.less_wt)) ? '0.000' : parseFloat(val.less_wt).toFixed(3)) + ' onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="est_tag[lwt][]" step="any" readonly style="width:80px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value=' + JSON.stringify(stone_details) + '><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

			+ '<td><input type="number" class="form-control tot_stn_amt" name="est_tag[stone_amt][]" value=' + parseFloat(stone_amt).toFixed(2) + ' readonly style="width:100px;" /></td>'

			+ '<td><input type="number" class="form-control tot_dia_amt" name="est_tag[diamond_amt][]" value=' + parseFloat(dia_amt).toFixed(2) + ' readonly style="width:100px;" /></td>'

			+ '<td><div class="nwt">' + (isNaN(parseFloat(val.net_wt)) ? '0.000' : parseFloat(val.net_wt).toFixed(3)) + '</div><input type="hidden" class="form-control tot_nwt" name="est_tag[nwt][]" value=' + (isNaN(parseFloat(val.net_wt)) ? '0.000' : parseFloat(val.net_wt).toFixed(3)) + ' /><input type="hidden" class="tot_tag_nwt"  value=' + (isNaN(parseFloat(val.net_wt)) ? '0.000' : parseFloat(val.net_wt).toFixed(3)) + ' style="width:100px;" /></td>'

			+ '<td><input type="text" class="form-control market_rate_value" name="est_tag[est_rate_per_grm][]" ' + rate_readonly + ' value="' + (rate_per_grm > 0 ? rate_per_grm : val.sales_value) + '" style="width:100px;" /></td>'

			
			+ '<td><input type="number"  name="est_tag[max_va_per][]" class="form-control tag_act_va_per" value="'+ val.max_va_per +'" style="width:100px;" readonly /><input type="hidden" class="tag_va_per" value="' + val.max_va_per + '"></td>'
			
			+ '<td><input type="number"  name="est_tag[wastage][]" class="form-control wastage_max_per" value="' + val.retail_max_wastage_percent + '"style="width:100px;" /><input type="hidden" class="va_min_limit" value="' + va_min_limit + '"><input type="hidden" class="id_wastage_slab" name = "est_tag[id_wastage_slab][]" value="'+ id_va_slab +'"><input type="hidden" class="slab_details"><input type="hidden" class="blk_disc_apld" value="'+ blk_disc +'" name="est_tag[blk_disc_apld][]"><input type="hidden" class="act_va_per" name="est_tag[act_va_per][]" value="' + act_wast_per + '"><input type="hidden" class="wast_slb_val" name="est_tag[wast_slab_val][]" value="' + wast_slb_value + '"></td>'


			+ '<td><div class="act_wast_wt" style="display:none">' + wast_wgt + '</div><input type="number"  name="est_tag[est_wastage_wt][]" class="form-control est_wastage_wt" value="" style="width:100px;" '+ tagVaReadonly +'/><input type="hidden" class="va_wt_min_limit" value="' + va_wt_min_limit + '"></td>'

			//+'<td><div class="mc">'+val.tag_mc_value+'</div></td>'

			+ '<td><select class="form-control est_mc_type" '+ tagMCReadonly +' style="width:80px;  '+tagMCSelection+' "><option value="2" ' + (val.tag_mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (val.tag_mc_type == 1 || val.tag_mc_type == null ? 'selected' : '') + '>Piece</option><option value="3" ' + (val.tag_mc_type == 3 ? 'selected' : '') + '>% on Price</option></select></td>'



			+ '<td><div class="mc_val" style="display:none">' + val.tag_mc_value + '</div><input class="form-control act_mc_value"  name="est_tag[mc][]" type="number"  value="' + val.tag_mc_value + '"style="width:100px;" '+ tagMCReadonly +'/><input class="discount_amount"  name="est_tag[discount_amount][]" type="hidden" value="" />Tot Mc:<span class="tot_mc" ></span><input type="hidden" class="mc_min_limit" value="' + mc_min_limit + '"><input type="hidden" name="est_tag[max_mc][]" class="tag_max_mc" value="' + val.tag_mc_value + '"></td>'



			//	+'<td><a href="#" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details" name="est_tag[stone_details][]" value='+JSON.stringify(stone_details)+'><input type="hidden" class="stone_price" name="est_tag[stone_price][]"></td>'

			+'<td>'+(val.charge_value > 0 ? '<a href="#" onClick="showTagCharges($(this).closest(\'tr\'));">'+val.charge_value+'</a><input type="hidden" class="charges_details" name="est_tag[charges_details][]" value=' + JSON.stringify(charges_details) + '>' :val.charge_value)+'</td>'

			+'<td>'+(val.tag_other_itm_amount > 0 ? '<a href="#" class="tag_other_itm_amount_link" onClick="showTagothermetals($(this).closest(\'tr\'));">'+ parseFloat(tag_other_full_amt.tot_amount).toFixed(2) +'</a>' :val.tag_other_itm_amount)+'<input type="hidden" class="tag_other_metal_details" value=' + JSON.stringify(other_metal_details) + '></td>'

			+ '<td><span class="cost">'+ val.sales_value +'</span><input type="hidden" name="est_tag[total_cgst][]" class="sale_cgst"/><input type="hidden" class="sale_sgst" name="est_tag[total_sgst][]"/><input type="hidden" class="sale_igst" name="est_tag[total_igst][]"/> <input type="hidden" class="sale_taxable_amount" name="est_tag[sale_taxable_amount][]"/> <input class="sales_value" type="hidden" name="est_tag[cost][]" value=' + (val.sales_value) + ' /><input class="tag_item_rate" type="hidden" name="est_tag[item_rate][]" value=' + val.item_rate + ' /><input class="caltype" type="hidden" name="est_tag[caltype][]" value=' + val.calculation_based_on + ' /><input class="tgi_calculation" type="hidden" name="est_tag[tgi_calculation][]" value=' + val.tgi_calculation + ' /><input type="hidden" class="tax_group_id" value="' + val.tax_group_id + '" /><input class="tax_percentage" type="hidden" name="est_tag[tax_percentage][]" value=' + val.tax_percentage + ' /><input class="stone_price" type="hidden" name="est_tag[stone_price][]" value=' + val.stone_price + ' /><input class="certification_price" type="hidden" name="est_tag[certification_price][]" value=' + val.certification_cost + ' /><input class="id_mc_type" type="hidden" name="est_tag[id_mc_type][]" value=' + val.tag_mc_type + ' /><input class="mc_value" type="hidden"  value=' + val.tag_mc_value + ' /><input class="tax_price" type="hidden" name="est_tag[tax_price][]" value="" /><input type="hidden" class="market_rate_cost" name="est_tag[market_rate_cost][]"><input type="hidden" class="market_rate_tax" name="est_tag[market_rate_tax][]"><input type="hidden" class="act_sales_value" value=' + (rate_per_grm >0 && val.stone_type!=0 ? rate_per_grm  : val.sales_value) + '><input type="hidden" class="charge_value" value=' + val.charge_value + ' name="est_tag[charge_value][]"><input type="hidden" class="sales_mode" value=' + val.sales_mode + ' ><input type="hidden" class="purchase_cost" value=' + val.tag_purchase_cost + ' name="est_tag[purchase_cost][]" ><input type="hidden" class="pur_mc" value=' + pur_mc + ' name="est_tag[pur_mc][]" ><input type="hidden" class="pur_va" value=' + pur_va + ' name="est_tag[pur_va][]" ><input type="hidden" class="ord_rate_per_grm" value='+val.order_rate_per_grm+'><input type="hidden" class="other_metal_details" name="est_tag[other_metal_details][]" value=' + JSON.stringify(other_metal_details) + '><input type="hidden" class="tag_other_itm_amount"  value="' + tag_other_itm_amount + '"></td><input type="hidden" class="ord_rate_type" value='+val.ord_rate_type+'></td>'

			+ (val.isTagsplitted !=1 && val.isTagsplitted!='undefined' ? '<td><a href="#" onClick="remove_tag_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' :'<td></td>')

			+ '</tr>';

			// if (calculation_based_on == 3 || calculation_based_on == 4) {

			// 	curRow.find('.act_sales_value').prop('readonly', true);

			// 	curRow.find('.id_mc_type').prop('readonly', true);

			// }

			

		if($('.is_split_row').val() == 1){
			$('#estimation_tag_details tbody').append(row);
		}

		else{
		$('#estimation_tag_details tbody').prepend(row);
		}

		let appendedRow = $('#estimation_tag_details tbody tr#' + rowId);

		let tagId = appendedRow.find('.est_tag_id').val();

		let tagAlreadyExists = items.some(item => item.row.find('.est_tag_id').val() === tagId);

		if (appendedRow.find('.is_chttopup_applied').val() == 0 && appendedRow.find('.is_sr_applied').val() == 0 && !tagAlreadyExists) {
			items.push({
				type: 'tag',
				RowId: rowId,
				tag_id: tagId,
				row: appendedRow,
				purity: parseFloat(appendedRow.find('.tag_purity_name').html() || 0),
				weight: parseFloat(appendedRow.find('.tot_nwt').val() || 0),
				chtapplied: parseInt(appendedRow.find('.is_chttopup_applied').val() || 0),
				srapplied: parseInt(appendedRow.find('.is_sr_applied').val() || 0),
				originalRatePerGram: parseFloat(appendedRow.find('.market_rate_value').val() || 0),
				ratefield: '.market_rate_value',
				metal_type: appendedRow.find('.metal_type').val(),
				utilizedchitdetails: appendedRow.find('.ChitUtilizedDetails').val(),
				utilizedSRdetails: appendedRow.find('.SRUtilizedDetails').val(),
				tag_partialy_utilized: parseInt(appendedRow.find('.partialy_utilized').val() || 0),
			});
		}

		calc_dia_tot_amt();

		$('#estimation_tag_details > tbody').find('.item_emp_id').select2();

		$('#estimation_tag_details > tbody').find('.tag_purity').select2();



		// getestPurityDetails(val.id_metal,val.cat_id);

		if (val.id_tag_mapping != '') {

			var collection_details = val.collection_details;

			if (collection_details?.length > 0) {

				$.each(collection_details, function (key, items) {

					$('#estimation_tag_details > tbody').each(function (idx, row) {

						if (items.tag_id != $(this).find('.est_tag_id').val()) {

							$('#id_tag_mapping').val(val.id_tag_mapping);

							$('#collection_confirm').modal('show');

						}

					});

				});

			}

		}



	}





function showTagCharges(curRow)

{

	$('#tag_other_charges_modal').modal('toggle');

	if (curRow != undefined) {

		$('#custom_active_id').val(curRow.closest('tr').attr('id'));

	}

	var catRow = $('#custom_active_id').val();

	var charges_details = JSON.parse($('#' + catRow).find('.charges_details').val());

	if(charges_details.length > 0)

	{

		var row = '';

		var charge_value = 0;

		$.each(charges_details,function(key,val){

			charge_value+=parseFloat(val.charge_value);

			row+='<tr>'

				 +'<td>'+val.code_charge+'</td>'

				 +'<td>'+val.charge_value+'</td>'

				 +'</tr>';

		});

		$('.tag_charges_total').html(parseFloat(money_format_india(charge_value)).toFixed(2));

		$('#tag_other_charges_modal tbody').append(row);

	}

}



function showTagothermetals(curRow)

{

	$('#other_metalmodal').modal('toggle');

	if (curRow != undefined) {

		$('#custom_active_id').val(curRow.closest('tr').attr('id'));

	}

	var catRow = $('#custom_active_id').val();

	var other_metal_details = JSON.parse($('#' + catRow).find('.tag_other_metal_details').val());

	console.log(other_metal_details);
	$('#other_metalmodal tbody').empty();

	if(other_metal_details.length > 0)

	{

		var row = '';

		var pcs = 0;

		var wt = 0;

		var amount = 0;

		var calcData = calculate_other_metal_amount(other_metal_details, $('<tr>'));


		$.each(other_metal_details,function(key,val){



			 pcs+=parseFloat(val.tag_other_itm_pcs);

		     wt+=parseFloat(val.tag_other_itm_grs_weight);

			 amount+=parseFloat(calcData.tot_amount);



			row+='<tr data-index="'+key+'">'

				 +'<td>'+val.metal_name+'</td>'

				 +'<td>'+val.purname+'</td>'

				 +'<td>'+val.tag_other_itm_pcs+'</td>'

				 +'<td>'+val.tag_other_itm_grs_weight+'</td>'

				 +'<td><input type="number" class="form-control om_wastage" value="'+val.tag_other_itm_wastage+'" step="any" style="width:80px;" /></td>'

				 +'<td><select class="form-control om_cal_type" style="width:100px;"><option value="1" '+(val.tag_other_itm_cal_type==1 ? 'selected':'')+'>Per Gram</option><option value="2" '+(val.tag_other_itm_cal_type==2 ? 'selected':'')+'>Per Piece</option></select></td>'

				 +'<td><input type="number" class="form-control om_mc" value="'+val.tag_other_itm_mc+'" step="any" style="width:80px;" /></td>'

				 +'<td><input type="number" class="form-control om_rate" value="'+ parseFloat(calcData.rate_per_gram).toFixed(2)+'" step="any" style="width:100px;" /></td>'

				 +'<td class="om_amount">'+ parseFloat(calcData.tot_amount).toFixed(2)+'</td>'

				 +'</tr>';

		});



		$('.total_pcs').html(parseFloat(money_format_india(pcs)).toFixed(0));

		$('.total_wt').html(parseFloat(money_format_india(wt)).toFixed(3));

	 	$('.total_amount').html(parseFloat(amount).toFixed(2));



		$('#other_metalmodal tbody').append(row);

	}

}



$('#tag_close_charge_details').on('click',function(){

	$('#tag_other_charges_modal tbody').empty();

	$('.tag_charges_total').html(0);

	$('#tag_other_charges_modal').modal('toggle');

});



$('#tag_other_metal_details').on('click',function(){

	$('#other_metal_table tbody').empty();

	$('.total_pcs').html(0);

	$('.total_wt').html(0);

	$('.total_amount').html(0);

	$('#other_metalmodal').modal('toggle');

});

// Save edited Other Metal details (VA, MC Type, MC, Rate)
$('#save_tag_other_metal_details').on('click', function(){
	var catRow = $('#custom_active_id').val();
	var other_metal_details = JSON.parse($('#' + catRow).find('.tag_other_metal_details').val());
	var total_amount = 0;
	$('#other_metalmodal #other_metal_table > tbody > tr').each(function(){
		var idx = $(this).data('index');
		if(other_metal_details[idx] !== undefined){
			other_metal_details[idx].tag_other_itm_wastage = parseFloat($(this).find('.om_wastage').val()) || 0;
			other_metal_details[idx].tag_other_itm_cal_type = parseInt($(this).find('.om_cal_type').val());
			other_metal_details[idx].tag_other_itm_mc = parseFloat($(this).find('.om_mc').val()) || 0;
			// Recalculate row amount
			var net_wt = parseFloat(other_metal_details[idx].tag_other_itm_grs_weight) || 0;
			var wastage_perc = other_metal_details[idx].tag_other_itm_wastage;
			var wast_wt = parseFloat((net_wt * wastage_perc) / 100);
			var mc_type = other_metal_details[idx].tag_other_itm_cal_type;
			var making_charge = other_metal_details[idx].tag_other_itm_mc;
			var rate_per_gram = parseFloat($(this).find('.om_rate').val()) || 0;
			var mc_value = (mc_type == 1 ? parseFloat(net_wt * making_charge) : (mc_type == 2 ? parseFloat(making_charge) : 0));
			var row_amount = parseFloat(parseFloat(rate_per_gram) * parseFloat(parseFloat(net_wt) + parseFloat(wast_wt)) + parseFloat(mc_value)).toFixed(2);
			$(this).find('.om_amount').html(parseFloat(row_amount).toFixed(2));
			total_amount += parseFloat(row_amount);
		}
	});
	// Update stored JSON on the parent tag row
	$('#' + catRow).find('.tag_other_metal_details').val(JSON.stringify(other_metal_details));
	$('#' + catRow).find('.other_metal_details').val(JSON.stringify(other_metal_details));
	// Recalculate other metal amount
	var recalc = calculate_other_metal_amount(other_metal_details, $('#' + catRow));
	$('.total_amount').html(parseFloat(recalc.tot_amount).toFixed(2));
	$('#' + catRow).find('.tag_other_itm_amount').val(parseFloat(recalc.tot_amount).toFixed(2));
	// Update visible link text on the tag row
	$('#' + catRow).find('.tag_other_itm_amount_link').text(parseFloat(recalc.tot_amount).toFixed(2));
	// Update totals in modal footer
	$('.total_amount').html(parseFloat(total_amount).toFixed(2));
	// Recalculate the tag sale value
	calculatetag_SaleValue();
	$('#other_metalmodal').modal('hide');
	$.toaster({ priority: 'success', title: 'Success!', message: 'Other Metal details updated successfully.' });
});
$(document).on('change keyup', '#other_metalmodal .om_wastage, #other_metalmodal .om_cal_type, #other_metalmodal .om_mc, #other_metalmodal .om_rate', function() {
    var tr = $(this).closest('tr');
    var idx = tr.data('index');
    var catRow = $('#custom_active_id').val();
    var other_metal_details = JSON.parse($('#' + catRow).find('.tag_other_metal_details').val()); 
    if(other_metal_details[idx] !== undefined){
        var net_wt = parseFloat(other_metal_details[idx].tag_other_itm_grs_weight) || 0;
        var wastage_perc = parseFloat(tr.find('.om_wastage').val()) || 0;
        var wast_wt = parseFloat((net_wt * wastage_perc) / 100);
        var mc_type = parseInt(tr.find('.om_cal_type').val());
        var making_charge = parseFloat(tr.find('.om_mc').val()) || 0;
        var rate_per_gram = parseFloat(tr.find('.om_rate').val()) || 0;
        var mc_value = (mc_type == 1 ? parseFloat(net_wt * making_charge) : (mc_type == 2 ? parseFloat(making_charge) : 0));
        var row_amount = parseFloat(parseFloat(rate_per_gram) * parseFloat(parseFloat(net_wt) + parseFloat(wast_wt)) + parseFloat(mc_value)).toFixed(2);
        tr.find('.om_amount').html(row_amount);
        // Update total amount in footer
        var total_amount = 0;
        $('#other_metalmodal #other_metal_table > tbody > tr').each(function(){
            total_amount += parseFloat($(this).find('.om_amount').text()) || 0;
        });
        $('.total_amount').html(parseFloat(total_amount).toFixed(2));
    }
});




//Set Tag Details in Table

// Get MC and VA for Home bill and Non Tag

function get_mc_va(product_id, design_id, subdesign_id, gwt, tag_type = 1, nwt, calculation_type) {

	let mc_cal_value = 0;

	let wastag_value = 0;

	let wastag_value_wt = 0;

	let va_method = 0;

	let mc_cal_type = 0;

	$.each(wast_settings_details, function (key, items) {

		if ((items.id_product == product_id) && (items.id_design == design_id) && (tag_type == 1 ? (items.id_sub_design == subdesign_id) : true)) {

			mc_cal_type = isNaN(items.mc_cal_type) || items.mc_cal_type == '' || items.mc_cal_type == null ? 0 : items.mc_cal_type;

			if (items.wastage_type == 1) { //Fixed

				mc_cal_value = isNaN(items.mc_cal_value) || items.mc_cal_value == '' || items.mc_cal_value == null ? 0 : items.mc_cal_value;

				wastag_value = isNaN(items.wastag_value) || items.wastag_value == '' || items.wastag_value == null ? 0 : items.wastag_value;

				wastag_value_wt = isNaN(items.wastag_value_wt) || items.wastag_value_wt == '' || items.wastag_value_wt == null ? 0 : items.wastag_value_wt;

				va_method = isNaN(items.wastag_method) || items.wastag_method == '' || items.wastag_method == null ? 0 : items.wastag_method;

			}

			else if (items.wastage_type == 2) { //Flexiable

				$.each(items.weight_range_det, function (i, result) {

					if ((parseFloat(result.wc_from_weight) <= parseFloat(gwt)) && (parseFloat(gwt) <= parseFloat(result.wc_to_weight))) {

						mc_cal_value = isNaN(result.mc) || result.mc == '' || result.mc == null ? 0 : result.mc;

						wastag_value = isNaN(result.wc_percent) || result.wc_percent == '' || result.wc_percent == null ? 0 : result.wc_percent;

						wastag_value_wt = isNaN(result.wc_wt) || result.wc_wt == '' || result.wc_wt == null ? 0 : result.wc_wt;

						va_method = isNaN(result.wc_method) || result.wc_method == '' || result.wc_method == null ? 0 : result.wc_method;

					}

				});

			}

			return false;

		}

	});

	let wt = 0;

	if(calculation_type == 0){



		wt = gwt;



	} else if(calculation_type == 1 || calculation_type == 2){



		wt = nwt;



	}

	if (va_method == 2) {

		wastag_value  = parseFloat(wt) > 0 ? parseFloat(parseFloat(wastag_value_wt / parseFloat(wt)) * 100).toFixed(2) : 0;

	} else {

		wastag_value_wt = parseFloat(parseFloat(wastag_value / 100) * parseFloat(wt)).toFixed(3);

	}

	let return_data = {

		"mc_cal_value": mc_cal_value,

		"mc_cal_type": mc_cal_type,

		"wastag_value": wastag_value,

		"wastag_value_wt": wastag_value_wt,

		"va_method"	: va_method

	}

	return return_data;

}

function update_custom_wastage_mc(row_change){

	let product_id = row_change.find('.cus_product').val();

	let design_id = row_change.find('.cus_design').val();

	let sub_design_id = row_change.find('.cus_sub_design').val();

	let cus_gwt = row_change.find('.cus_gwt').val();

	let cus_nwt = row_change.find('.cus_nwt').val() > 0 ? row_change.find('.cus_nwt').val() : 0;

	let cus_calculation_based_on = row_change.find('.cus_calculation_based_on').val();

	//let cus_wastage = row_change.find('.cus_wastage').val() > 0 ? row_change.find('.cus_wastage').val() : 0;

	let va_mc = get_mc_va(product_id, design_id, sub_design_id, cus_gwt, 1, cus_nwt, cus_calculation_based_on);

	console.log("va_mc",va_mc);

	row_change.find('.cus_wastage_wt').val(va_mc.wastag_value_wt);

	row_change.find('.cus_wastage').val(va_mc.wastag_value);

	row_change.find('.cus_act_va_per').val(va_mc.wastag_value);

	row_change.find('.cus_pro_va_per').val(va_mc.wastag_value);

	row_change.find('.act_va_per').val(va_mc.wastag_value);

	row_change.find('.cus_mc_type').val(va_mc.mc_cal_type);

	row_change.find('.id_mc_type').val(va_mc.mc_cal_type);

	row_change.find('.cus_mc').val(va_mc.mc_cal_value);

	row_change.find('.HB_cus_mc').val(va_mc.mc_cal_value);

	calculateCustomItemSaleValue();

}

function update_custom_wastage_wt(row_change) {

	let cus_wastage = row_change.find('.cus_wastage').val() > 0 ? row_change.find('.cus_wastage').val() : 0;

	if(parseFloat(cus_wastage) <= 100) {

		let cus_nwt = row_change.find('.cus_nwt').val() > 0 ? row_change.find('.cus_nwt').val() : 0;

		let wast_wgt = parseFloat(parseFloat(cus_nwt) * parseFloat(cus_wastage / 100)).toFixed(3);

		row_change.find('.cus_wastage_wt').val(wast_wgt);

	} else {

		row_change.find('.cus_wastage').val('');



		row_change.find('.cus_wastage').focus();

		$.toaster({ priority: 'danger', title: 'Warning!', settings: { 'timeout': 4000 }, message: '' + "</br>" + 'Wastage Shold below 100%..', timeOut: 9500 });

	}

}

function update_catalog_wastage_mc(row_change) {

	let product_id = row_change.find('.cat_product').val();

	let design_id = row_change.find('.cat_design').val();

	let sub_design_id = row_change.find('.cat_sub_design').val();

	let cat_gwt = row_change.find('.cat_gwt').val();

	let cat_nwt = row_change.find('.cat_nwt').val() > 0 ? row_change.find('.cat_nwt').val() : 0;

	let cat_calculation_based_on = row_change.find('.cat_calculation_based_on').val();

	let va_mc = get_mc_va(product_id, design_id, sub_design_id, cat_gwt, 1, cat_nwt, cat_calculation_based_on);

	console.log("va_mc",va_mc);

	row_change.find('.cat_wastage_wt').val(va_mc.wastag_value_wt);

	row_change.find('.cat_wastage').val(va_mc.wastag_value);

	row_change.find('.cat_act_va_per').val(va_mc.wastag_value);
	
	row_change.find('.cat_pro_va_per').val(va_mc.wastag_value);

	row_change.find('.act_va_per').val(va_mc.wastag_value);

	row_change.find('.mc_type').val(va_mc.mc_cal_type);

	row_change.find('.id_mc_type').val(va_mc.mc_cal_type);

	row_change.find('.cat_mc').val(va_mc.mc_cal_value);

	row_change.find('.nn_cat_mc').val(va_mc.mc_cal_value);

}

function calc_cat_wastage_wt(row_change) {

	let cat_wastage = row_change.find('.cat_wastage').val() > 0 ? row_change.find('.cat_wastage').val() : 0;

	if(parseFloat(cat_wastage) <= 100) {

		let cat_nwt = row_change.find('.cat_nwt').val() > 0 ? row_change.find('.cat_nwt').val() : 0;

		let wast_wgt = parseFloat(parseFloat(cat_nwt) * parseFloat(cat_wastage / 100)).toFixed(3);

		row_change.find('.cat_wastage_wt').val(wast_wgt);

	} else {

		row_change.find('.cat_wastage').val('');

		row_change.find('.cat_wastage_wt').val('');



		row_change.find('.cat_wastage').focus();



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Wastage Shold be below 100%..' });

	}

}



function getStoneRateSettings()

{

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/admin_ret_catalog/getStoneRateSettings',

		dataType:'json',

		success:function(data)

		{

			stone_rate_settings =data;

			$(".overlay").css("display", "none");

		}

	});

}



function check_min_max_stone_rate(curRow)

{

	var stone_rate = curRow.find('.stone_rate').val();

	$.each(stone_rate_settings,function(key,items)

		{

			var stone_pcs  = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : parseInt(curRow.find('.stone_pcs').val());

			var stone_wt  = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : parseFloat(curRow.find('.stone_wt').val());

		var stone_centwt = parseFloat(((stone_wt)/(stone_pcs))*100).toFixed(3);


		if($('#branch_select').val() == items.id_branch && curRow.find('.stones_type').val()==items.stone_type && curRow.find('.stone_id').val()==items.stone_id && curRow.find('.quality_id').val() == items.quality_id && curRow.find('.stone_uom_id').val()==items.uom_id)

		{

			if(stone_centwt > 0)

			{

				if(stone_centwt >= parseFloat(items.from_cent) && stone_centwt <= parseFloat(items.to_cent))

				{

					if(curRow.find('.stone_rate').val()>=parseFloat(items.min_rate) && curRow.find('.stone_rate').val()<=parseFloat(items.max_rate))

					{

						curRow.find('.stone_rate').val(stone_rate);

					}

					else

					{

						curRow.find('.stone_rate').val(items.max_rate);

						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Stone Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !'});

					}

				}

			}

			else if(curRow.find('.stone_rate').val()>=parseFloat(items.min_rate) && curRow.find('.stone_rate').val()<=parseFloat(items.max_rate))

			{

				curRow.find('.stone_rate').val(stone_rate);

			}

			else

			{

				curRow.find('.stone_rate').val(items.max_rate);

				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Entered Stone Rate Must be Within '+items.min_rate+' and '+items.max_rate+' !'});

			}

		}

	});

	calculate_stone_amount();

}

$(document).on('keyup', ".stone_price", function () {

	$(this).closest('tr').find('.stone_rate').val(0);

	calculate_stone_amount();

});

$(document).on('keyup', ".stone_rate", function () {

	$(this).on('change', ".stone_cal_type", function () {

		$(this).closest('tr').find('.stone_price').val(0);

	});

	calculate_stone_amount();

});
$(document).on('change', ".old_metal_product", function (e) {

	var row = $(this).closest('tr');


	if (this.value != '') {

		row.find('.old_metal_prod_id').val(this.value);


	}
	else {

		row.find('.old_metal_prod_id').val('');

	}
});
function get_old_metal_Product(id_metal = '', row = '') {
	$.ajax({
		url: base_url + 'index.php/admin_ret_estimation/get_old_metal_Product',
		data: ({ 'id_metal': id_metal }),
		dataType: "JSON",
		type: "POST",
		success: function (data) {


			old_metal_prod_details = data;


			$(".overlay").css("display", "none");
		}
	});
}
$('#disc_reset').on('click', function () {

	if($('#dia_disc_lmt').val() != 0){

		$("#summary_stn_dis_per").prop("readonly", false);
		$('#summary_stn_dis_per').val('').html('').focus();
		if ($('#estimation_tag_details > tbody > tr').length > 0) {
			$('#estimation_tag_details > tbody > tr').each(function () {
				var curRow = $(this);
				if (ctrl_page[2] == 'add') {
					var old_st_details = [];
					old_st_details = JSON.parse(curRow.find('.stone_details_backup').val());
					if (old_st_details) {
						curRow.find('.stone_details').val(JSON.stringify(old_st_details));
						// calculatetag_SaleValue();
					}
				}
				else if (ctrl_page[2] == 'edit') {

					var stone_details_new = [];

					var stone_details = JSON.parse(curRow.find('.stone_details').val());

					$.each(stone_details, function (index, value) {

						if (value.stones_type == 1) {

							value.stone_price = value.max_stn_price;

							var stn_wt = value.stone_wt;

							var stn_pcs = value.stone_pcs;

							if (value.stone_cal_type == 1) {

								finalrate = parseFloat(value.stone_price / stn_wt).toFixed(2);

								value.stone_rate = finalrate;

							}

							else {

								finalrate = parseFloat(value.stone_price / stn_pcs).toFixed(2);

								value.stone_rate = finalrate;

							}

							stone_details_new.push(value)
						}
						else {
							stone_details_new.push(value)
						}
					});
					curRow.find('.stone_details').val(JSON.stringify(stone_details_new));
				}
				// curRow.find('.stone_details').val(''); 
				calculatetag_SaleValue();
			});
		}
		if ($('#estimation_custom_details > tbody > tr').length > 0) {
			$('#estimation_custom_details > tbody > tr').each(function () {
				var curRow = $(this);
				var old_st_details = [];
				if (ctrl_page[2] == 'add') {
					old_st_details = JSON.parse(curRow.find('.stone_details_backup').val());
					if (old_st_details) {
						curRow.find('.stone_details').val(JSON.stringify(old_st_details));
						// calculateCustomItemSaleValue();
					}
				}
				else if (ctrl_page[2] == 'edit') {

					var stone_details_new = [];

					var stone_details = JSON.parse(curRow.find('.stone_details').val());

					$.each(stone_details, function (index, value) {

						if (value.stones_type == 1) {

							value.stone_price = value.max_stn_price;

							var stn_wt = value.stone_wt;

							var stn_pcs = value.stone_pcs;

							if (value.stone_cal_type == 1) {

								finalrate = parseFloat(value.stone_price / stn_wt).toFixed(2);

								value.stone_rate = finalrate;

							}

							else {

								finalrate = parseFloat(value.stone_price / stn_pcs).toFixed(2);

								value.stone_rate = finalrate;

							}

							stone_details_new.push(value)
						}
						else {
							stone_details_new.push(value)
						}
					});
					curRow.find('.stone_details').val(JSON.stringify(stone_details_new));
				}
				// curRow.find('.stone_details').val(''); 
				calculateCustomItemSaleValue();
			});
		}

	}

});

$("#summary_stn_dis_per").on("change", function () {
	if (this.value != "") {
		$("#summary_stn_dis_per").prop("readonly", true);

		$("#total_cost").prop("readonly", true);

		$("#disc_apply").trigger("click");
	}
});

$('#disc_apply').on('click', function () {

	var min_disc_per = $('#min_stn_disc_limit').val();

	var max_disc_per = $('#max_stn_disc_limit').val();

	var stn_disc_per = parseFloat($('#summary_stn_dis_per').val());

	var emp_disc_per = $('#dia_disc_lmt').val();

	if ((stn_disc_per > max_disc_per) || (stn_disc_per < min_disc_per)) {

		$.toaster({ priority: 'danger', title: 'Warning!', settings: { 'timeout': 4000 }, message: '' + "</br>" + 'Enter the discount percentage between ' + min_disc_per + ' to ' + max_disc_per + '.' });

		$('#summary_stn_dis_per').val('').focus();

		$("#summary_stn_dis_per").prop("readonly", false);
	}
	else if ((stn_disc_per > emp_disc_per) || (stn_disc_per < min_disc_per)) {

		$.toaster({ priority: 'danger', title: 'Warning!', settings: { 'timeout': 4000 }, message: '' + "</br>" + 'Enter the discount percentage between ' + min_disc_per + ' to ' + emp_disc_per + '.' });

		$('#summary_stn_dis_per').val('').focus();

		$("#summary_stn_dis_per").prop("readonly", false);
	}
	else {

		if ($('#estimation_tag_details > tbody > tr ').length > 0 && stn_disc_per > 0) {

			$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

				curRow = $(this);

				var stone_details_new = [];

				var stone_details = JSON.parse(curRow.find('.stone_details').val());

				if (!curRow.find('.stone_details_backup').length) {
					curRow.append('<input type="hidden" class="stone_details_backup" value= ' + curRow.find('.stone_details').val() + ' />');
				}

				$.each(stone_details, function (index, value) {

					if (value.stones_type == 1) {

						stn_wt = value.stone_wt;

						stn_pcs = value.stone_pcs;

						// if (ctrl_page[2] == 'add') {

							finalRate = parseFloat(value.stone_rate - (value.stone_rate * stn_disc_per / 100)).toFixed(2);

							value.stone_rate = parseFloat(finalRate).toFixed(2);

							check_disc_min_max_stone_rate(value);

							if (value.stone_cal_type == 1) {

								finalamt = parseFloat(value.stone_rate * stn_wt).toFixed(2);

								value.stone_price = finalamt;

							}

							else {

								finalamt = parseFloat(value.stone_rate * stn_pcs).toFixed(2);

								value.stone_price = finalamt;

							}

							stone_details_new.push(value);

						// }

					}
					else {
						stone_details_new.push(value)
					}
				})



				curRow.find('.stone_details').val(JSON.stringify(stone_details_new));

				calculatetag_SaleValue();
			});
		}

		if ($('#estimation_custom_details > tbody > tr').length > 0 && stn_disc_per > 0) {

			$('#estimation_custom_details > tbody > tr').each(function (idx, row) {

				curRow = $(this);

				var stone_details_new = [];

				var stone_details = JSON.parse(curRow.find('.stone_details').val());


				if (!curRow.find('.stone_details_backup').length) {
					curRow.append('<input type="hidden" class="stone_details_backup" value= ' + curRow.find('.stone_details').val() + ' />');
				}

				$.each(stone_details, function (index, value) {

					if (value.stones_type == 1) {

						stn_wt = value.stone_wt;

						stn_pcs = value.stone_pcs;

						// if (ctrl_page[2] == 'add') {

							finalRate = parseFloat(value.stone_rate - (value.stone_rate * stn_disc_per / 100)).toFixed(2);

							value.stone_rate = parseFloat(finalRate).toFixed(2);

							check_disc_min_max_stone_rate(value);

							if (value.stone_cal_type == 1) {

								finalamt = parseFloat(value.stone_rate * stn_wt).toFixed(2);

								value.stone_price = finalamt;

							}

							else {

								finalamt = parseFloat(value.stone_rate * stn_pcs).toFixed(2);

								value.stone_price = finalamt;

							}

							stone_details_new.push(value);

						}

					// }
					else {
						stone_details_new.push(value)
					}

				})
				curRow.find('.stone_details').val(JSON.stringify(stone_details_new));

				calculateCustomItemSaleValue();
			});
		}
	}
});
$('#create_va_slab_details').on('click', function () {

		if ($('#estimation_tag_details > tbody >tr').length == 0 && $('#estimation_custom_details > tbody >tr').length == 0 && $('#estimation_catalog_details > tbody >tr').length == 0) {

			$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Item Details Not Found..' });

		}

		else {

			if (validateVASlabDetailRow()) {

								create_new_empty_est_va_slab_row();
								
								// $("#summary_blk_dis_per").prop("readonly", true);

								// $("#blk_disc_apply").prop("disabled",true);

								// $("#blk_disc_reset").prop("disabled",true);

			} else {

				$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please fill required fields..' });

			}

		}

	});
	function validateVASlabDetailRow() {

	var row_validate = true;

	$('#estimation_va_slab_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('.select_metal').val() == "" || $(this).find('.va_range').val() == "" || $(this).find('.disc_value').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;

}
function create_new_empty_est_va_slab_row() {

	var row = "";

	row += '<tr>'

		+ '<td style="width:30%;"><select class="select_metal" style="width:100%;"></select></td>'

		+ '<td style="width:30%;"><select class="va_range" style="width:100%;"></select><input type="hidden" class="va_disc_range"></td>'

		+ '<td style="width:20%;"><input type="number" class="disc_value" style="width:100%; text-align:left;" onchange="onDiscValueChange($(this).closest(\'tr\'))"><input type="hidden" class="wastage_id"></td>'

		+ '<td style="width:20%;">' + '<button type="button" onClick="apply_va_slab_row($(this).closest(\'tr\'));" class="btn btn-primary btn-apply va_apply"  style="font-size: 0px;padding:8px;" disabled>' +
			'<i class="fa fa-check fa-sm" style="font-size: 12px;"></i>' +
			'</button>' + '<button type="button" onClick="remove_va_slab_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del" style="font-size: 0px;padding:8px;margin-left:3px;">'+ '<i class="fa fa-trash fa-sm" style="font-size: 12px;"></i > '+
			'</button>' +
		  '</td></tr>';

	$('#estimation_va_slab_details tbody').append(row);

	$.each(matel_types, function (key, item) {

				$('.select_metal').append(

					// $('<option></option>'),

					$("<option></option>")

						.attr("value", item.id_metal)

						.text(item.metal)

				);

			});

			$(".select_metal").select2({

				placeholder: "Select metal",

				allowClear: true,

				width: '100%'

			});

			var row = $('#estimation_va_slab_details tbody tr:last');

			var goldMetal = matel_types.find(m => m.metal.toLowerCase() === "gold");

			if (goldMetal) {
				row.find('.select_metal').val(goldMetal.id_metal).trigger('change');
			}

			$(".overlay").css("display", "none");


}
$(document).on('change', '.select_metal', function () {

	var row = $(this).closest('tr');
	
	var id_metal = row.find('.select_metal').val();

	var va_range = row.find('.va_range');

	my_Date = new Date();

	$.ajax({

		url: base_url + 'index.php/admin_ret_estimation/get_va_range/?nocache=' + my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: { 'id_metal': id_metal },

		success: function (data) {

			var range = data;

			var selectedSlabs = [];

			va_range.empty(); 

         
            $('#estimation_va_slab_details tbody tr').each(function () {
				
                var metal_id = $(this).find('.select_metal').val();
                var slabVal = $(this).find('.va_range').val();
                if (metal_id == id_metal && slabVal) {
                    selectedSlabs.push(slabVal); 
                }
				
            });

			$.each(range, function (key, item) {

				 if (!selectedSlabs.includes(item.value)){

				va_range.append(

					$('<option></option>'),

					$("<option></option>")

						.attr("value", item.value)

						.attr("data-from", item.from_range)  

                   		.attr("data-to", item.to_range)
						    
                   		.attr("data-id", item.id_wastage)    

						.text(item.text_range)
						
					);

				}
					
					$(".va_range").select2({
	
						placeholder: "Select Slab",
	
						allowClear: true,

						width: 'resolve'
	
					});

			});


		}

	})

})
$(document).on('select2:select', '.va_range', function (e) {
	 
	var row = $(this).closest('tr');

	row.find('.select_metal').prop('disabled', true);

	row.find('.va_range').prop('disabled', true);

});
function apply_va_slab_row(currow){


	var min_va_value = parseFloat(currow.find('.va_range').val());

	var value = parseFloat(currow.find('.disc_value').val());

	var selectedslab =  currow.find('.va_range option:selected');
	
	var from = parseFloat(selectedslab.data("from"));
	
	var to = parseFloat(selectedslab.data("to"));
	
	var id_wastage = selectedslab.data("id");
	
	currow.find('.wastage_id').val(id_wastage);

	if(value < min_va_value){

		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'The wastage percentage should not be less than ' + min_va_value + ' %.' });

		currow.find('.disc_value').val('').focus();

	}

	else {
	
	wastage_slab_value(currow,id_wastage,value,'',from,to);

	currow.find('.va_apply').prop('disabled',true);
	}

}
function onDiscValueChange(row){

	var disc_value = row.find('.disc_value').val()

	if(disc_value != ''){

	row.find('.va_apply').prop('disabled',false);

	 row.find('.va_apply').focus();

	}

	else{

		row.find('.va_apply').prop('disabled',true);

	}
}
function remove_va_slab_row(curRow) {

	var del_trg = true;

	var value = curRow.find('.disc_value').val();

	var wastage_id = curRow.find('.wastage_id').val();

	wastage_slab_value(curRow,wastage_id,value,del_trg);
	
	curRow.remove();

	// if($('#estimation_va_slab_details tbody tr').length === 0){

	// 	$("#summary_blk_dis_per").prop("readonly", false);

	// 	$("#blk_disc_apply").prop("disabled",false);

	// 	$("#blk_disc_reset").prop("disabled",false);
	// }

}
function wastage_slab_value(row, id_wastage, value, del_trg, from, to) {

	var id_metal = row.find('.select_metal').val()

	var disc_value = row.find('.disc_value').val()

	$('#estimation_tag_details > tbody >tr').each(function () {

		let tagrow = $(this);

		let slab_details = [];

		let pro_id = $(this).find('.pro_id').val();

		let design_id = $(this).find('.design_id').val();

		let id_sub_design = $(this).find('.id_sub_design').val();

		let gwt = $(this).find('.gwt').val();

		let nwt = $(this).find('.tot_nwt').val();

		let wastage_max_per = $(this).find('.wastage_max_per').val();

		if (tagrow.find('.metal_type').val() == id_metal) {

			if ((tagrow.find('.wastage_max_per').val() >= from && (tagrow.find('.wastage_max_per').val() <= to) && (tagrow.find('.id_wastage_slab').val() == '') && (tagrow.find('.blk_disc_apld').val() == 0))) {

				let act_va_per = tagrow.find('.act_va_per').val();

				let tag_act_va_per = tagrow.find('.tag_act_va_per').val();

				let min_max_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt)

				let va_per = ((value < min_max_va_limit.va_min || (min_max_va_limit.va_max > 0 ? value > min_max_va_limit.va_max : false) || value > tag_act_va_per) ? act_va_per : value);

				if (va_per != act_va_per) {


					tagrow.find('.id_wastage_slab').val(id_wastage);

					tagrow.find('.wast_slb_val').val(parseFloat(value).toFixed(2));

					// if (tagrow.find('.act_va_per').val() == '') {
					// 	tagrow.find('.act_va_per').val(parseFloat(act_va_per).toFixed(2));
					// }


					let detail = {

						id_wastage: id_wastage,
						va_value: value,
						act_va_per: act_va_per,
						from_range: from,
						to_range: to

					};

					slab_details.push(detail);

					tagrow.find('.slab_details').val(JSON.stringify(slab_details));

					tagrow.find('.wastage_max_per').val(parseFloat(value).toFixed(2));

				}

			}

			else if (tagrow.find('.id_wastage_slab').val() == id_wastage && del_trg == '') {

				let act_va_per = tagrow.find('.act_va_per').val();

				let tag_act_va_per = tagrow.find('.tag_act_va_per').val();

				let min_max_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt)

				let va_per = ((value < min_max_va_limit.va_min || (min_max_va_limit.va_max > 0 ? value > min_max_va_limit.va_max : false) || value > tag_act_va_per) ? act_va_per : value);

				if (va_per != act_va_per) {

					let detail = {

						id_wastage: id_wastage,
						va_value: value,
						act_va_per: act_va_per,
						from_range: from,
						to_range: to

					};
					tagrow.find('.wastage_max_per').val(parseFloat(value).toFixed(2));

					tagrow.find('.wast_slb_val').val(parseFloat(value).toFixed(2));

					slab_details.push(detail);

					if (detail != undefined) {

						tagrow.find('.slab_details').val(JSON.stringify(slab_details));

					}

				}

				else {

					tagrow.find('.wastage_max_per').val(parseFloat(va_per).toFixed(2));

					tagrow.find('.id_wastage_slab').val('');

					tagrow.find('.slab_details').val('');

					tagrow.find('.wast_slb_val').val('');
				}
			}

			else {

				if (del_trg) {

					let tag_slab = (tagrow.find('.slab_details').val() != '' ? JSON.parse(tagrow.find('.slab_details').val()) : '');

					if (tag_slab != '') {

						$.each(tag_slab, function (key, item) {

							if (item.id_wastage == id_wastage) {

								tagrow.find('.wastage_max_per').val(parseFloat(item.act_va_per).toFixed(2));

								tagrow.find('.id_wastage_slab').val('');

								tagrow.find('.slab_details').val('');

								tagrow.find('.wast_slb_val').val('');

							}




							// else if ((tagrow.find('.id_wastage_slab').val() != '') && tagrow.find('.metal_type').val() == id_metal && (disc_value == tagrow.find('.wastage_max_per').val())) {

							// 	tagrow.find('.wastage_max_per').val(parseFloat(tagrow.find('.act_va_per').val()).toFixed(2));

							// 	tagrow.find('.id_wastage_slab').val('');

							// 	tagrow.find('.slab_details').val('');

							// 	tagrow.find('.act_va_per').val('');
							// }

						});

					}

				}

			}

		}
		// else if ((tagrow.find('.id_wastage_slab').val() != '') && tagrow.find('.metal_type').val() != id_metal && (disc_value == tagrow.find('.wastage_max_per').val())) {

		// 	tagrow.find('.wastage_max_per').val(parseFloat(tagrow.find('.act_va_per').val()).toFixed(2));

		// 	tagrow.find('.id_wastage_slab').val('');

		// 	tagrow.find('.slab_details').val('');

		// 	tagrow.find('.act_va_per').val('');
		// }

		calculatetag_SaleValue();

	});
	$('#estimation_custom_details > tbody >tr').each(function () {

		let customrow = $(this);

		let pro_id = customrow.find('.cus_product_id').val();

		let design_id = customrow.find('.cus_des_id').val();

		let id_sub_design = customrow.find('.cus_id_sub_design').val();

		let gwt = customrow.find('.cus_gwt').val();

		let nwt = customrow.find('.cus_nwt').val();

		let wastage_max_per = customrow.find('.cus_wastage').val();

		let slab_details = [];

		if (customrow.find('.metal_type').val() == id_metal) {

			if (((customrow.find('.cus_wastage').val() >= from) && (customrow.find('.cus_wastage').val() <= to) && (customrow.find('.id_wastage_slab').val() == ''))) {

				// if(customrow.find('.id_wastage_slab').val() == ''){

				var act_va_per = customrow.find('.act_va_per').val();

				let min_max_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt)

				let va_per = ((value < min_max_va_limit.va_min || (min_max_va_limit.va_max > 0 ? value > min_max_va_limit.va_max : false)) ? act_va_per : value);

				if (va_per != act_va_per) {

					customrow.find('.id_wastage_slab').val(id_wastage);

					customrow.find('.wast_slb_val').val(parseFloat(value).toFixed(2));

					// if (customrow.find('.act_va_per').val() == '') {
					// 	customrow.find('.act_va_per').val(parseFloat(act_va_per).toFixed(2));
					// }

					let detail = {

						id_wastage: id_wastage,
						va_value: value,
						act_va_per: act_va_per,
						from_range: from,
						to_range: to

					};


					customrow.find('.cus_wastage').val(parseFloat(value).toFixed(2));

					// }

					slab_details.push(detail);

					if (detail != undefined) {

						customrow.find('.slab_details').val(JSON.stringify(slab_details));

					}

				}

			}

			else if (customrow.find('.id_wastage_slab').val() == id_wastage && del_trg == '') {

				act_va_per = customrow.find('.act_va_per').val();

				let min_max_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt)

				let va_per = ((value < min_max_va_limit.va_min || (min_max_va_limit.va_max > 0 ? value > min_max_va_limit.va_max : false)) ? act_va_per : value);

				if (va_per != act_va_per) {

					let detail = {

						id_wastage: id_wastage,
						va_value: value,
						act_va_per: act_va_per,
						from_range: from,
						to_range: to

					};
					customrow.find('.cus_wastage').val(parseFloat(value).toFixed(2));

					customrow.find('.wast_slb_val').val(parseFloat(value).toFixed(2));

					slab_details.push(detail);

					if (detail != undefined) {

						customrow.find('.slab_details').val(JSON.stringify(slab_details));

					}
				}

				else {

					customrow.find('.cus_wastage').val(parseFloat(va_per).toFixed(2));

					customrow.find('.id_wastage_slab').val('');

					customrow.find('.slab_details').val('');

					customrow.find('.wast_slb_value').val('');

				}
			}
			else {

				if (del_trg) {

					let cus_slab = (customrow.find('.slab_details').val() != '' ? JSON.parse(customrow.find('.slab_details').val()) : '');

					if (cus_slab != '') {

						$.each(cus_slab, function (key, item) {

							if (item.id_wastage == id_wastage) {

								customrow.find('.cus_wastage').val(parseFloat(item.act_va_per).toFixed(2));

								customrow.find('.id_wastage_slab').val('');

								customrow.find('.slab_details').val('');

								customrow.find('.wast_slb_val').val('');
							}

							// else if ((customrow.find('.id_wastage_slab').val() != '') && customrow.find('.metal_type').val() == id_metal && (disc_value == customrow.find('.cus_wastage').val())) {

							// 	customrow.find('.cus_wastage').val(item.act_va_per);

							// 	customrow.find('.id_wastage_slab').val('');

							// 	customrow.find('.slab_details').val('');

							// 	customrow.find('.act_va_per').val('');
							// }
						});

					}

				}


			}
		}
		// else if ((customrow.find('.id_wastage_slab').val() != '') && customrow.find('.metal_type').val() != id_metal && (disc_value == customrow.find('.cus_wastage').val())) {

		// 	customrow.find('.cus_wastage').val(customrow.find('.act_va_per').val());

		// 	customrow.find('.id_wastage_slab').val('');

		// 	customrow.find('.slab_details').val('');

		// 	customrow.find('.act_va_per').val('');
		// }

		calculateCustomItemSaleValue();

	});
	$('#estimation_catalog_details > tbody >tr').each(function () {

		var catrow = $(this);

		var slab_details = [];

		let pro_id = $(this).find('.cat_pro_id').val();

		let design_id = $(this).find('.cat_des_id').val();

		let id_sub_design = $(this).find('.cat_id_sub_design').val();

		let gwt = $(this).find('.cat_gwt').val();

		let nwt = $(this).find('.cat_nwt').val();

		let wastage_max_per = $(this).find('.cat_wastage').val();

		if (catrow.find('.metal_type').val() == id_metal) {

			if ((catrow.find('.cat_wastage').val() >= from && (catrow.find('.cat_wastage').val() <= to) && catrow.find('.id_wastage_slab').val() == '')) {

				var act_va_per = catrow.find('.act_va_per').val();

				let min_max_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt)

				let va_per = ((value < min_max_va_limit.va_min || (min_max_va_limit.va_max > 0 ? value > min_max_va_limit.va_max : false)) ? act_va_per : value);

				if (va_per != act_va_per) {

					catrow.find('.id_wastage_slab').val(id_wastage);

					catrow.find('.wast_slb_val').val(parseFloat(value).toFixed(2));

					// if (catrow.find('.act_va_per').val() == '') {
					// 	catrow.find('.act_va_per').val(parseFloat(act_va_per).toFixed(2));
					// }

					var detail = {

						id_wastage: id_wastage,
						va_value: value,
						act_va_per: act_va_per,
						from_range: from,
						to_range: to

					};

					slab_details.push(detail);

					catrow.find('.slab_details').val(JSON.stringify(slab_details));

					catrow.find('.cat_wastage').val(value);

				}

			}

			else if (catrow.find('.id_wastage_slab').val() == id_wastage && del_trg == '') {

				act_va_per = catrow.find('.act_va_per').val();

				let min_max_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt)

				let va_per = ((value < min_max_va_limit.va_min || (min_max_va_limit.va_max > 0 ? value > min_max_va_limit.va_max : false)) ? act_va_per : value);

				if (va_per != act_va_per) {

					let detail = {

						id_wastage: id_wastage,
						va_value: value,
						act_va_per: act_va_per,
						from_range: from,
						to_range: to

					};
					catrow.find('.cat_wastage').val(parseFloat(value).toFixed(2));

					catrow.find('.wast_slb_val').val(parseFloat(value).toFixed(2));

					slab_details.push(detail);

					if (detail != undefined) {

						catrow.find('.slab_details').val(JSON.stringify(slab_details));

					}
				}

				else {

					catrow.find('.cat_wastage').val(parseFloat(va_per).toFixed(2));

					catrow.find('.id_wastage_slab').val('');

					catrow.find('.slab_details').val('');

					catrow.find('.wast_slb_val').val('');

				}
			}

			else {

				if (del_trg) {

					var cat_slab = (catrow.find('.slab_details').val() != '' ? JSON.parse(catrow.find('.slab_details').val()) : '');

					if (cat_slab != '') {

						$.each(cat_slab, function (key, item) {

							if (item.id_wastage == id_wastage) {

								catrow.find('.cat_wastage').val(parseFloat(item.act_va_per).toFixed(2));

								catrow.find('.id_wastage_slab').val('');

								catrow.find('.slab_details').val('');

								catrow.find('.wast_slb_val').val('');
							}

							// else if ((catrow.find('.id_wastage_slab').val() != '') && catrow.find('.metal_type').val() == id_metal && (disc_value == catrow.find('.cat_wastage').val())) {

							// 	catrow.find('.cat_wastage').val(parseFloat(catrow.find('.act_va_per').val()).toFixed(2));

							// 	catrow.find('.id_wastage_slab').val('');

							// 	catrow.find('.slab_details').val('');

							// 	catrow.find('.act_va_per').val('');
							// }
						});
					}


				}

			}
		}

		// else if ((catrow.find('.id_wastage_slab').val() != '') && catrow.find('.metal_type').val() != id_metal && (disc_value == catrow.find('.cat_wastage').val())) {

		// 	catrow.find('.cat_wastage').val(parseFloat(catrow.find('.act_va_per').val()).toFixed(2));

		// 	catrow.find('.id_wastage_slab').val('');

		// 	catrow.find('.slab_details').val('');

		// 	catrow.find('.act_va_per').val('');
		// }
		calculateSaleValue();
	});

}
function edit_wastage_slab(item_details) {

	$.each(item_details, function (key, item) {
		if (item.id_wastage) {
			let exists = false;

			$('#estimation_va_slab_details > tbody > tr').each(function () {
				var row = $(this).closest('tr');
				if (row.find('.wastage_id').val() == item.id_wastage) {
					exists = true;
					// return false;
				}
			});

			if (!exists) {

				create_new_empty_est_va_slab_row();

				const lastRow = $('#estimation_va_slab_details > tbody > tr:last');

				lastRow.find('.select_metal').val(item.id_metal).trigger('change');

				setTimeout(() => {
				const vaRangeSelect = lastRow.find('.va_range');

				const matchingOption = vaRangeSelect.find('option').filter(function () {
					return $(this).text().trim() === item.slab_range.trim();
				});

				if (matchingOption.length > 0) {
					const matchedValue = matchingOption.val();

					vaRangeSelect.val(matchedValue).trigger('change'); 
				} 
				}, 300);
				// lastRow.find('.disc_value').text(item.value);
				lastRow.find('.disc_value').val(parseFloat(item.slb_val).toFixed(2));
				lastRow.find('.wastage_id').val(item.id_wastage);
				lastRow.find('.select_metal').prop('disabled', true);
				lastRow.find('.va_range').prop('disabled', true);
				
			}
		}
	});
}
function check_disc_min_max_stone_rate(value) {

	var stn_type = value.stones_type;

	var stn_id = value.stone_id;

	var stn_uom_id = value.stone_uom_id;

	var quality_id = value.quality_id;

	var stone_rate = value.stone_rate;

	var price = 0;

	$.each(stone_rate_settings, function (key, items) {

		var stone_pcs = value.stone_pcs;

		var stone_wt = parseFloat(value.stone_wt).toFixed(2);

		var stone_centwt = parseFloat(((stone_wt) / (stone_pcs)) * 100).toFixed(3);

		if ($('#branch_select').val() == items.id_branch && stn_type == items.stone_type && stn_id == items.stone_id && quality_id == items.quality_id && stn_uom_id == items.uom_id) {

			if (stone_centwt > 0) {

				if (stone_centwt >= parseFloat(items.from_cent) && stone_centwt <= parseFloat(items.to_cent)) {

					if (stone_rate >= parseFloat(items.min_rate) && stone_rate <= parseFloat(items.max_rate)) {

						value.stone_rate = stone_rate;
						
					}
					
					else {
						
						Rate = value.stone_cal_type == 1 ? parseFloat(value.max_stn_price / value.stone_wt) : parseFloat(value.max_stn_price / value.stone_pcs);
						
						value.stone_rate = parseFloat(Rate).toFixed(2);
						
						$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Entered Stone Rate Must be Within ' + items.min_rate + ' and ' + items.max_rate + '.' });
						
					}

				}

			}

			else if (stone_rate >= parseFloat(items.min_rate) && stone_rate <= parseFloat(items.max_rate)) {

				value.stone_rate = stone_rate;

			}

			else {

				value.stone_rate = items.max_rate;

				$.toaster({ priority: 'danger', title: 'Warning!', settings: { 'timeout': 8000 }, message: '' + "</br>" + 'Entered Stone Rate Must be Within ' + items.min_rate + ' and ' + items.max_rate + ' !' });

			}

		}

	});
	
	return value;

}

$("#summary_blk_dis_per").on("keyup", function () {

	if(parseFloat(this.value) == 0 || parseFloat(this.value) > parseFloat($('#bulk_wast').val())){

			var blk_wast_per = parseFloat($('#bulk_wast').val()).toFixed(2);

			$("#summary_blk_dis_per").val('').focus();

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Bulk wastage discount should not exceed the value ' + blk_wast_per + '.' });

	}
	else if(parseFloat(this.value) == 0 || parseFloat(this.value) > parseFloat($('#blk_wast_disc_lmt').val())){

			var blk_wast_per = parseFloat($('#blk_wast_disc_lmt').val()).toFixed(2);

			$("#summary_blk_dis_per").val('').focus();

			$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Bulk wastage discount should not exceed the value ' + blk_wast_per + '.' });

	}
	
});

$("#summary_blk_dis_per").on("change", function () {

	if (this.value != "" ) {

		$("#summary_blk_dis_per").prop("readonly", true);

		$("#total_cost").prop("readonly", true);

		$("#blk_disc_apply").trigger("click");

	}

});

$('#blk_disc_apply').on('click', function () {

	var blk_disc_per = parseFloat($('#summary_blk_dis_per').val());

		if ($('#estimation_tag_details > tbody > tr ').length > 0 && blk_disc_per > 0) {

			$('#estimation_tag_details > tbody > tr').each(function (idx, row) {

				curRow = $(this);

				if(curRow.find('.id_wastage_slab').val() == '' && curRow.find('.wastage_max_per').val() != 0 && (curRow.find('.is_split_row').val() == 0 || curRow.find('.is_split_row').val() == undefined)){

					let pro_id = $(this).find('.pro_id').val();

					let design_id = $(this).find('.design_id').val();

					let id_sub_design = $(this).find('.id_sub_design').val();

					let gwt = $(this).find('.gwt').val();

					let nwt = $(this).find('.tot_nwt').val();		

					let act_va_per = parseFloat(curRow.find('.wastage_max_per').val()).toFixed(2);

					let disc_was_val = parseFloat((act_va_per - parseFloat(blk_disc_per)).toFixed(2)); 
					
					let min_max_va_limit = get_mc_va_limit(pro_id, design_id, id_sub_design, gwt, 1, nwt)
					
					let va_min = parseFloat(min_max_va_limit.va_min);  
					
					let va_max = parseFloat(min_max_va_limit.va_max);
					// let blk_was_per = ((disc_was_val < min_max_va_limit.va_min || (min_max_va_limit.va_max > 0 ? disc_was_val > min_max_va_limit.va_max : false)) ? (min_max_va_limit.va_min < 2 ? 2 : min_max_va_limit.va_min) : (disc_was_val == 0 ? ( min_max_va_limit.va_min != 0 ? min_max_va_limit.va_min : 2 ) : disc_was_val < 2 ? 2 : disc_was_val));
					let blk_was_per = ((disc_was_val < va_min || (va_max > 0 ? disc_was_val > va_max : false)) ? act_va_per : disc_was_val);
					

					if (blk_was_per != act_va_per) {

						curRow.find('.wastage_max_per').val(parseFloat(blk_was_per).toFixed(2));

						curRow.find('.act_va_per').val(parseFloat(act_va_per).toFixed(2));

						curRow.find('.blk_disc_apld').val(1);

					}

					calculatetag_SaleValue();

				}
			});
			
		}
	
});
$('#blk_disc_reset').on('click', function () {

	if($('#blk_wast_disc_lmt').val() > 0){

		$("#summary_blk_dis_per").prop("readonly", false);

		$('#summary_blk_dis_per').val('').html('').focus();

		if ($('#estimation_tag_details > tbody > tr').length > 0) {

			$('#estimation_tag_details > tbody > tr').each(function () {

				var curRow = $(this);

				act_va_per = parseFloat(curRow.find('.tag_act_va_per').val()).toFixed(2);

				if(curRow.find('.blk_disc_apld').val() == 1){

					curRow.find('.wastage_max_per').val(act_va_per);

					curRow.find('.blk_disc_apld').val(0);
				}

				calculatetag_SaleValue();
			})
		}

	}

});
$(document).on('change','.cat_wastage,.cus_wastage,.wastage_max_per',function(){

	var row = $(this).closest('tr');

	var wast_per = $(this).val();

	if(wast_per != ''){
		$(this).val(parseFloat(wast_per).toFixed(2));
		if ($(this).hasClass('cat_wastage')) {
			if(row.find('.cat_pro_va_per').val() == 0 && row.find('.cat_pro_va_per').val() != ''){
				row.find('.cat_act_va_per').val(parseFloat(wast_per).toFixed(2));
			}
			row.find('.act_va_per').val(parseFloat(wast_per).toFixed(2));
        }
		
        if ($(this).hasClass('cus_wastage')) {
			if(row.find('.cus_pro_va_per').val() == 0 && row.find('.cus_pro_va_per').val() != ''){
				row.find('.cus_act_va_per').val(parseFloat(wast_per).toFixed(2));
			}
			row.find('.act_va_per').val(parseFloat(wast_per).toFixed(2));
        }
        if ($(this).hasClass('wastage_max_per')) {
			if(row.find('.tag_va_per').val() == 0 ){
				row.find('.tag_act_va_per').val(parseFloat(wast_per).toFixed(2));
			}
			row.find('.act_va_per').val(parseFloat(wast_per).toFixed(2));
        }
		
	}
});

function isLikelyBase64(str) {
    // Base64 strings don't contain brackets, braces, or quotes at the start
    // Regular JSON will start with [ or {
    if (str.charAt(0) === '[' || str.charAt(0) === '{') {
        return false; // Likely regular JSON
    }
    
    // Base64 only contains these characters
    var base64Pattern = /^[A-Za-z0-9+/]+=*$/;
    return base64Pattern.test(str);
}



function get_topup_chit_closing_balance(del = '', add = '') {
	if (del != '' || add != '') {
		console.log(items)
		items.forEach(function (item) {
			var currow = item.row;
			var rate_field = item.ratefield;
			if (del == 'cht_delete' || add == 'cht_add') {
				currow.find('.is_chttopup_applied').val(0);
				currow.find(rate_field).val(parseFloat(item.originalRatePerGram).toFixed(2));
				// currow.find('.utilized_rate').val(0);
				currow.find('.ChitUtilizedDetails').val('');
				item.chtapplied = 0;
				item.utilizedchitdetails = [];
			}
			else if (del == 'sr_delete' || add == 'sr_add') {
				currow.find('.is_sr_applied').val(0);
				currow.find(rate_field).val(parseFloat(item.originalRatePerGram).toFixed(2));
				item.srapplied = 0;
				currow.find('.SRUtilizedDetails').val('');
				item.utilizedSRdetails = [];
			}
			item.tag_partialy_utilized = 0;
			currow.find('.partialy_utilized').val(0);
			cht_count = ($('#estimation_chit_details > tbody > tr').length == 0 ? 0 : 1);
			sr_count = ($('#estimation_sr_details > tbody > tr').length == 0 ? 0 : 1);
		});
		if ($('#estimation_chit_details > tbody > tr').length == 0 && $('#estimation_sr_details > tbody > tr').length == 0) {
				$('#est_cus_name').prop('readonly', false);
		}
	}

	items.sort(function (a, b) {
		const typePriority = {
			'tag': 0,
			'non-tag': 1,
			'homebill': 2
		};
		if (a.purity === b.purity) {
			return typePriority[a.type] - typePriority[b.type];
		}
		return b.purity - a.purity;
	});
	var table_name = (add == 'cht_add' || del == 'cht_delete' ? '#estimation_chit_details > tbody > tr' : '#estimation_sr_details  > tbody > tr');
	var bal_wt = 0;
	var utilized_topup_wt = 0;
	var pre_tag_pur = 0;
	var nxt_cht_utilized = false;
	$(table_name).each(function () {
		let utilizedData = [];
		var chitRow = $(this);
		let chitId = chitRow.attr('data-chit-id');
		var topup_weight = (add == 'cht_add' || del == 'cht_delete' ? parseFloat(chitRow.find('.closing_weight').val() || 0) : parseFloat(chitRow.find('.sr_weight').val() || 0));
		var chitRate = (add == 'cht_add' || del == 'cht_delete' ? parseFloat(chitRow.find('.chit_rate').val() || 0) : parseFloat(chitRow.find('.rate').val() || 0));
		var nextRow = chitRow.next('tr');
		var nextChitId = nextRow.data('chit-id');
		var nxt_rw_wt = (add == 'cht_add' || del == 'cht_delete' ? parseFloat(nextRow.find('.closing_weight').val() || 0) : parseFloat(nextRow.find('.sr_weight').val() || 0));
		var nxt_rw_rate = (add == 'cht_add' || del == 'cht_delete' ? parseFloat(nextRow.find('.chit_rate').val() || 0) : parseFloat(nextRow.find('.rate').val() || 0));
		var cht_amt = parseFloat(chitRow.find('.chit_amt').val());
		var tot_wt = 0;
		items.forEach(function (item, index) {
			if (topup_weight <= 0) return;
			prevItem = items[index - 1];
			prev_tag_purity = (prevItem == undefined ? 0 : prevItem.purity);
			var row = item.row;
			var excess_rate = 0;
			var avg_rate = 0;
			var excess_wt = 0;
			var utilized_rate = 0;
			var prev_utld_cht = 0;
			var tag_utld_wt = 0;
			var tag_utld_rate = 0;
			var rate_field = item.ratefield;
			utilizedData = (add == 'cht_add' || del == 'cht_delete' ? item.utilizedchitdetails || [] : item.utilizedSRdetails || []);
			var karat = Math.round((item.purity / 100) * 24);
			var board_rate = (karat == 18 ? parseFloat($('.goldrate_18ct').html()) : parseFloat($('.goldrate_22ct').html()));
			var converted_cht_wt = (prev_tag_purity == item.purity ? parseFloat(topup_weight).toFixed(3) : parseFloat(topup_weight * (22 / karat)).toFixed(3)) ;
			var converted_cht_rate  = parseFloat(chitRate * (karat / 22)).toFixed(2);
			var nxt_rw_cnvtd_wt = parseFloat(nxt_rw_wt * (22 / karat)).toFixed(3);
			var nxt_rw_converted_rate =   parseFloat(nxt_rw_rate * (karat / 22)).toFixed(2) ;
			var remaining_cht_wt = converted_cht_wt;
			converted_cht_rate = (( parseFloat(converted_cht_rate) > board_rate ) ? board_rate : converted_cht_rate ) ;
			nxt_rw_converted_rate = (( parseFloat(nxt_rw_converted_rate) > board_rate ) ? board_rate : nxt_rw_converted_rate ) ;
			if (utilizedData.length > 0) {
				utilizedData.forEach(function (chit) {
					tag_utld_wt += parseFloat(chit.utilized_wt);
					tag_utld_rate += parseFloat(chit.utilized_wt) * parseFloat(chit.utilized_rate);
				})
			}
			var chit_wt = parseFloat(parseFloat(converted_cht_wt) - (remaining_cht_wt != converted_cht_wt ? parseFloat(remaining_cht_wt) : 0)).toFixed(3);
			tot_wt = ((item.chtapplied == 0 && item.srapplied == 0 && item.metal_type == 1) ? parseFloat(item.weight - tag_utld_wt) : 0);
			if (tot_wt <= chit_wt && tot_wt != 0 && item.chtapplied == 0 && item.srapplied == 0 && item.metal_type == 1) {
				if(tag_utld_wt == 0){
				var utilized_weight = item.weight;
				var rate = (parseFloat(converted_cht_rate));
				row.find(rate_field).val(parseFloat(converted_cht_rate).toFixed(2));
				}
				else{
					var utilized_weight = tot_wt;
					var rate =  parseFloat((parseFloat(tag_utld_rate) + (parseFloat(tot_wt) * parseFloat(converted_cht_rate)))/parseFloat(item.weight)).toFixed(2);
					row.find(rate_field).val(parseFloat(rate).toFixed(2));
				}
				utilizedData.push({
					chit_id: chitId,
					utilized_wt: parseFloat(item.weight).toFixed(3),
					utilized_rate: parseFloat(rate).toFixed(2),
					purity: karat
				});
				if (add == 'cht_add' || del == 'cht_delete') {
					row.find('.is_chttopup_applied').val(1);
					row.find('.ChitUtilizedDetails').val(JSON.stringify(utilizedData));
					item.utilizedchitdetails = utilizedData;
					item.chtapplied = 1;
				}
				else {
					row.find('.is_sr_applied').val(1);
					item.srapplied = 1;
					row.find('.SRUtilizedDetails').val(JSON.stringify(utilizedData));
					item.utilizedSRdetails = utilizedData;
				}
				row.find('.partialy_utilized').val(0);
				item.tag_partialy_utilized = 0;
				pre_tag_pur = karat;
				topup_weight = parseFloat(remaining_cht_wt - utilized_weight);
			}
			else if (tot_wt > chit_wt && tot_wt != 0 && item.chtapplied == 0 && item.srapplied == 0 && item.metal_type == 1) {
				utilized_rate = Math.round(parseFloat(chit_wt * converted_cht_rate) + parseFloat(tag_utld_rate)).toFixed(3);
				bal_wt = parseFloat(parseFloat(item.weight) - parseFloat(tag_utld_wt) - parseFloat(chit_wt)).toFixed(3);
				if (bal_wt > 0 && nxt_rw_converted_rate > 0) {					
					excess_rate = parseFloat(bal_wt * nxt_rw_converted_rate).toFixed(2);
				}
				else {
					excess_rate = parseFloat(bal_wt * item.originalRatePerGram).toFixed(2);
					row.find('.partialy_utilized').val(1);
					row.find('.is_chttopup_applied').val(0);
					item.chtapplied = 0;
					item.tag_partialy_utilized = 1;
				}
				avg_rate = parseFloat((parseFloat(utilized_rate) + parseFloat(excess_rate)) / parseFloat(item.weight)).toFixed(2);
				row.find(rate_field).val(parseFloat(avg_rate).toFixed(2));
				utilizedData.push({
					chit_id: chitId,
					utilized_wt: parseFloat(chit_wt).toFixed(3),
					utilized_rate: parseFloat(converted_cht_rate).toFixed(2),
					purity: karat
				});
				if (add == 'cht_add' || del == 'cht_delete') {
					if (bal_wt == 0) {
						row.find('.partialy_utilized').val(0);
						row.find('.is_chttopup_applied').val(1);
						item.chtapplied = 1;
						item.tag_partialy_utilized = 0;
					}
					row.find('.ChitUtilizedDetails').val(JSON.stringify(utilizedData));
					item.utilizedchitdetails = utilizedData;
				}
				else {
					if (bal_wt == 0) {
						row.find('.partialy_utilized').val(0);
						row.find('.is_sr_applied').val(1);
						item.srapplied = 1;
						item.tag_partialy_utilized = 0;
					}
					row.find('.SRUtilizedDetails').val(JSON.stringify(utilizedData));
					item.utilizedSRdetails = utilizedData;
				}
				pre_tag_pur = karat;
				topup_weight = parseFloat(remaining_cht_wt - chit_wt);
			}
		});
	});
	calculatetag_SaleValue();
	calculateSaleValue();
	calculateCustomItemSaleValue();
}

$('#create_sr_details').on('click', function () {

	if ($('#est_cus_name').val() == '') {

			$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please Select The Customer..' });

		}
	else if ($('#branch_select').val() == '') {

			$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please Select The Branch..' });

		}

	else if ($('#estimation_tag_details > tbody >tr').length == 0 && $('#estimation_custom_details > tbody >tr').length == 0 && $('#estimation_catalog_details > tbody >tr').length == 0) {

			$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Item Details Not Found..' });

		}

	else {
		
		if (validateSRDetailRow()) {

				create_new_empty_est_sr_row();

    			} else {

				$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please fill required fields..' });

    			}

	}

});	

function validateSRDetailRow() {

	var row_validate = true;

	$('#estimation_sr_details > tbody  > tr').each(function (index, tr) {

		if ($(this).find('.ret_bill_id').val() == "" || $(this).find('.sr_amount').val() == "") {

			row_validate = false;

		}

	});

	return row_validate;
}


function create_new_empty_est_sr_row() {

	var cus_id = $('#cus_id').val();
	let row = '<tr>';

	row += '<td><div class="input-group" style="display: flex; ">'
	row += '<select class="form-control bill_fin_year_select" name="bill_fin_year_select[]" style="width: 70px;">';
	console.log(fin_year);
	fin_year.forEach(function (year) {
		row += `<option value="${year.fin_year_code}">${year.fin_year_name}</option>`;
	});
	row += '</select>';

	row += '<select class="form-control" id="filter_bill_no" name="sales_ret_uti[filter_bill_no][]" style="width: 70px;">'
		+ '</select>'
		+ '<input type="hidden" class="ret_bill_id" name="sales_ret_uti[ret_bill_id][]" value="">'
		+ '<input type="hidden" class="ret_bill_det_id" name="sales_ret_uti[ret_bill_det_id][]" value="">'
		+ '<button type="button" class="btn btn-default search_bill_no" onclick="getBillDetails($(this).closest(\'tr\'));">'
		+ '<i class="fa fa-search"></i>'
		+ '</button>'
		+ '</div></td>'
		+ '<td><input type="text" name="amount[]" class="form-control sr_amount" readonly/><input type="hidden" class="form-control rate"/></td>'
		+ '<td><input type="text" name="weight[]" class="form-control sr_weight" readonly/></td>'
		+ '<td><a href="#" onClick="remove_sr_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
		+ '</tr>';


	// Append the row to the table body
	$('#estimation_sr_details tbody').append(row);

	let lastRow = $('#estimation_sr_details tbody tr:last');

    var uniqueId = 'sr_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

    lastRow.attr('data-sr-id', uniqueId);

	getCustomerSalesDetails(cus_id, lastRow);

}
 function getFinancialYr() {

		my_Date = new Date();

		$.ajax({

			url: base_url + 'index.php/admin_ret_estimation/getFinancialYr/?nocache=' + my_Date.getUTCSeconds(),

			dataType: "json",

			method: "POST",

			data: { 'id_branch': $("#id_branch").val() },

			success: function (data) {

				fin_year = data;

			},

		});

	}


function getBillDetails(row) {

	$("#ret_bill_id").val("");

	var fin_year = row.find('.bill_fin_year_select').val();

	var billNo = row.find('#filter_bill_no').val();

	var bill_id = row.find('.ret_bill_id').val();

	var billType = 7;

	var selectedDetIds = [];

	$('#estimation_sr_details > tbody > tr').each(function () {
		let $tr = $(this);

		if ($tr.is(row)) return;

		let tr_bill_id = $tr.find('.ret_bill_id').val();

		if (tr_bill_id === bill_id) {
			let detIds = $tr.find('.ret_bill_det_id').val();
			if (detIds) {
				selectedDetIds = selectedDetIds.concat(detIds.split(',').filter(Boolean));
			}
		}
	});


	my_Date = new Date();

	$.ajax({
		url:
			base_url +
			"index.php/admin_ret_billing/getBillDetails/?nocache=" +
			my_Date.getUTCSeconds(),

		dataType: "json",

		method: "POST",

		data: {
			billNo: billNo,
			billType: billType,
			id_branch: $("#id_branch").val(),
			fin_year: fin_year,
			is_eda: 1,
		},

		success: function (data) {
			if (data.success == true) {
				$("#bill-detail-popup").modal("toggle");
				$("#bill-detail-popup").modal("show");
				$("#bill_items_tbl_for_return tbody").empty();
				$("#bill_items_for_return").show();

				// BILL SOLD ITEMS

				if (data.responsedata.item_details.length > 0) {

					$("#bill_items_tbl_for_return tbody").empty();

					// $(".ret_bill_id").val(data.responsedata.item_details[0].bill_id);

					row.find(".rate").val(data.responsedata.item_details[0].board_rate);

					$("#est_cus_name").attr("readonly", true);

					$.each(data.responsedata.item_details, function (estkey, estval) {
						let isChecked = selectedDetIds.length > 0 ? selectedDetIds.includes(estval.bill_det_id.toString()) : '';
						var row =
							"<tr>" +
							"<td>" +
							(estval.status == 2
								? '<span style="color:red">Returned</span>'
								: `<input type="checkbox" class="select_est_details" value="1" ${isChecked ? 'checked disabled' : ''} />`) +
							'<input type="hidden" class="bill_id" value="' +
							estval.bill_id +
							'" /><input type="hidden" class="bill_det_id" value="' +
							estval.bill_det_id +
							'" /><input type="hidden" class="bill_det_id" value="' +
							estval.bill_det_id +
							'" /><input type="hidden" class="est_id" value="' +
							estval.esti_id +
							'" /><input type="hidden" class="order_no" value="' +
							estval.order_no +
							'" /><input type="hidden" class="est_itm_id" value="' +
							estval.esti_item_id +
							'" /><input type="hidden" class="est_tag_id" value="' +
							estval.tag_id +
							'" /><input type="hidden" class="est_hsn" value="' +
							estval.hsn_code +
							'" /><input type="hidden" class="est_item_type" value="' +
							estval.item_type +
							'" /><input type="hidden" class="est_cal_type" value="' +
							estval.calculation_based_on +
							'" /><input type="hidden" class="est_metal_type" value="' +
							estval.metal_type +
							'" /><input type="hidden" class="est_uom" value="' +
							estval.uom +
							'"  /><input type="hidden" class="ret_bill_cash_paid" value="' +
							data.responsedata.cash_paid_amount +
							'"  /></td>' +
							'<td><span class="est_product_name">' +
							estval.product_name +
							'</span><input class="est_product_id" type="hidden" value="' +
							estval.product_id +
							'" /></td>' +
							'<td><span class="est_design_code">' +
							estval.design_name +
							'</span><input type="hidden" class="est_design_id" value="' +
							estval.design_id +
							'"  /></td>' +
							'<td><span class="est_piece">' +
							estval.piece +
							'</span><input type="hidden" class="est_pcs" value="' +
							estval.piece +
							'"  /></td>' +
							'<td><span class="est_purname">' +
							estval.purname +
							'</span><input type="hidden" class="est_purid" value="' +
							estval.purname +
							'"  /></td>' +
							'<td><span class="est_size">' +
							estval.size +
							'</span><input type="hidden" class="est_size_val" value="' +
							estval.size +
							'"  /></td>' +
							'<td><span class="est_gross_wt">' +
							estval.gross_wt +
							'</span><input type="hidden" class="est_gross_val" value="' +
							estval.gross_wt +
							'"  /></td>' +
							'<td><span class="est_less_wt">' +
							estval.less_wt +
							'</span><input type="hidden" class="est_less_val" value="' +
							estval.less_wt +
							'"  /></td>' +
							'<td><span class="est_net_wt">' +
							estval.net_wt +
							'</span><input type="hidden" class="est_net_val" value="' +
							estval.net_wt +
							'"  /></td>' +
							'<td><span class="est_wastage">' +
							estval.wastage_percent +
							'</span><input type="hidden" class="est_wastage_percent" value="' +
							estval.wastage_percent +
							'"  /></td>' +
							'<td><span class="est_mc">' +
							estval.mc_value +
							" " +
							(estval.mc_type == 1 ? " per gm" : " per pc") +
							'</span><input type="hidden" class="est_mc_value" value="' +
							estval.mc_value +
							'"  /><input type="hidden" class="est_mc_type" value="' +
							estval.mc_type +
							'"  /></td>' +
							'<td><input type="hidden" class="est_discount" value="' +
							estval.discount +
							'"/>' +
							estval.discount +
							"</td>" +
							'<td><span class="est_tgrp_name">' +
							estval.tgrp_name +
							'</span><input type="hidden" class="est_tax_id" value="' +
							estval.tax_group_id +
							'" /><input type="hidden" class="est_tax_val" value="' +
							estval.item_total_tax +
							'" /></td><td>' +
							estval.item_total_tax +
							"</td>" +
							'<td><span class="est_item_cost">' +
							estval.item_cost +
							'</span><input type="hidden" class="est_material_price" value="' +
							estval.othermat_amount +
							'"  /><input type="hidden" class="est_stone_price" value="' +
							estval.stone_price +
							'"  /><input type="hidden" class="est_item_cost_val" value="' +
							estval.item_cost +
							'"  /></td>' +
							"<td>" +
							(estval.is_partial == 1
								? "<input type='checkbox' class='select_est_partial' value='1' />"
								: "-") +
							"</td>" +
							'<td><span class="est_tag_no">' +
							estval.tag_id +
							"</span></td>" +
							"</tr>";

						$("#bill_items_tbl_for_return tbody").append(row);
					});

					$("#bill_items_for_return").show();



				} else {
					$("#bill_items_tbl_for_return tbody").empty();


					$("#bill_items_for_return").hide();

				}

			} else {
				alert(data.message);
			}
		},
	});
}

$('#update_bill_return').on('click', function () {
	var totalAmount = 0;
	var totalWeight = 0;
	var bill_id = 0;
	var selectedDetIds = [];

	$('#bill_items_tbl_for_return tbody tr').each(function () {
		var $checkbox = $(this).find('.select_est_details');
		if ($checkbox.is(':checked') && !$checkbox.is(':disabled')) {
			var amount = parseFloat($(this).find('.est_item_cost_val').val()) || 0;
			var netWeight = parseFloat($(this).find('.est_net_val').val()) || 0;
			var purity = parseFloat($(this).find('.est_purid').val()) || 0;
			var detId = $(this).find('.bill_det_id').val();
			var b_id = $(this).find('.bill_id').val();
			var karat = Math.round((purity / 100) * 24);
			if(karat != '22')
			{
				var netWeight = netWeight * (karat / 22);
			}

			selectedDetIds.push(detId);
			totalAmount += amount;
			totalWeight += netWeight;
			bill_id = b_id;
		}
	});

	$('#estimation_sr_details tbody tr').each(function () {
		const $curRow = $(this);
		if ($curRow.find('.ret_bill_id').val() == bill_id &&
			$curRow.find('.sr_amount').val() == '' &&
			$curRow.find('.sr_weight').val() == '') {

			$curRow.find('.sr_amount').val(totalAmount.toFixed(2));
			$curRow.find('.sr_weight').val(totalWeight.toFixed(3));
			// $curRow.find('.ret_bill_id').val(bill_id);
			$curRow.find('.ret_bill_det_id').val(selectedDetIds.join(','));
		}
	});

	$("#bill-detail-popup").modal("hide");

	get_topup_chit_closing_balance('','sr_add');
});


function remove_sr_row(curRow) {



	var del = false;

	curRow.remove();

    get_topup_chit_closing_balance(del = 'sr_delete','');	


	// calculate_sales_details();

	// calculate_purchase_details();

}

$(document).on('change', '#filter_bill_no', function () {
   
    let selectedOption = $(this).find('option:selected');

   
    let billId = selectedOption.data('bill-id');

 
    $(this).closest('tr').find('.ret_bill_id').val(billId);
});

$(document).on('blur', '.cat_gwt', function (e) {

		var row = $(this).closest('tr');

		row.find('.cat_nwt').trigger('change');

});

$(document).on('change', '.cat_nwt', function () {
	var row = $(this).closest('tr');
	var net_wt = row.find('.cat_nwt').val();
	let rowId = row.attr("id");
	let existingItem = items.find(it => it.rowId === rowId);
	let rowExists = !!existingItem;
	if (rowExists) {
		let prevWt = parseFloat(existingItem.weight || 0);
		netWtChanged = prevWt !== net_wt;
		if(netWtChanged){
			existingItem.weight = parseFloat(net_wt || 0);
		}
	}
	if (row.find('.is_chttopup_applied').val() == 0 && row.find('.is_sr_applied').val() == 0 && !rowExists && row.find('.cat_nwt').val() != 0) {

		items.push({
			type: 'non-tag',
			rowId: rowId,
			row: row,
			purity: parseFloat(row.find('.cat_purity option:selected').text() || 0),
			weight: parseFloat(row.find('.cat_nwt').val() || 0),
			chtapplied: parseInt(row.find('.is_chttopup_applied').val() || 0),
			srapplied: parseInt(row.find('.is_sr_applied').val() || 0),
			originalRatePerGram: parseFloat(row.find('.cat_market_rate_value').val() || 0),
			ratefield: '.cat_market_rate_value',
			metal_type: row.find('.metal_type').val(),
			utilizedchitdetails: row.find('.ChitUtilizedDetails').val(),
			utilizedSRdetails: row.find('.SRUtilizedDetails').val(),
			tag_partialy_utilized: parseInt(row.find('.partialy_utilized').val() || 0)
		});

	}

});

function append_custom_details(val) {
	let rate_readonly = $("#manual_rate").is(":checked") ? "" : "readonly";
	var purity_options = "";
	$.each(purities, function (pkey, pitem) {
		purity_options += "<option " + (val.purid == pitem.id_purity ? 'selected' : '') + " value='" + pitem.id_purity + "'>" + pitem.purity + "</option>";
	});

	var select_emp = "<option value=''> Select Employee </option>";
	$.each(emp_details, function (pkey, emp) {
		var selected = (val.item_emp_id == emp.id_employee) ? "selected" : "";
		select_emp += "<option " + selected + " value='" + emp.id_employee + "'>" + emp.emp_name + "</option>";
	});

	var select_section = "<option value=''>Select Section</option>";
	$.each(section_details, function (key, sec_item) {
		if ($('#id_branch').val() == sec_item.id_branch && sec_item.is_home_bill_counter == 1) {
			var selected = (sec_item.id_section == val.id_section) ? "selected" : "";
			select_section += "<option value='" + sec_item.id_section + "' " + selected + ">" + sec_item.section_name + "</option>";
		}
	});

	var select_product = "<option value=''>Select Product</option>";
	$.each(cus_product_details, function (key, pitem) {
		var selected = (val.product_id == pitem.pro_id) ? "selected" : "";
		select_product += "<option value='" + pitem.pro_id + "' " + selected + ">" + pitem.product_name + "</option>";
	});

	// Support for API stone details format
	var ui_stone_details = [];
	var total_stone_price = parseFloat(val.stone_price) || 0;
	if (val.stone_details && val.stone_details.length > 0) {
		$.each(val.stone_details, function (skey, sitem) {
			if (sitem.stone_pcs || sitem.pieces > 0) {
				// Map API keys to UI keys if necessary
				var formatted_stone = {
					"show_in_lwt": sitem.show_in_lwt || sitem.is_apply_in_lwt,
					"stone_id": sitem.stone_id,
					"stones_type": sitem.stones_type || sitem.stone_type,
					"stone_pcs": sitem.stone_pcs || sitem.pieces,
					"stone_wt": sitem.stone_wt || sitem.wt,
					"stone_price": sitem.stone_price || sitem.price,
					"stone_rate": sitem.stone_rate || sitem.rate_per_gram,
					"stone_uom_id": sitem.stone_uom_id || sitem.uom_id,
					"stone_cal_type": sitem.stone_cal_type,
					'max_stn_price': sitem.max_stn_price || sitem.max_stn_amt,
					'quality_id': sitem.quality_id
				};
				ui_stone_details.push(formatted_stone);
				if (!val.stone_price) total_stone_price += parseFloat(formatted_stone.stone_price || 0);
			}
		});
	}

	// Support for API charges format
	var ui_charges = [];
	var total_charge_amount = parseFloat(val.charge_amount) || 0;
	if (val.charges && val.charges.length > 0) {
		$.each(val.charges, function (pkey, pitem) {
			ui_charges.push({ 
				"id_charge": pitem.id_charge, 
				"code_charge": pitem.code_charge || pitem.name_charge, 
				"value_charge": pitem.value_charge || pitem.amount 
			});
			if (!val.charge_amount) total_charge_amount += parseFloat(pitem.value_charge || pitem.amount || 0);
		});
	}

	// Handle Slab Details
	var ui_slab_details = val.slab_details || [];
	if (ui_slab_details.length === 0 && val.id_wastage) {
		ui_slab_details.push({
			id_wastage: val.id_wastage,
			act_va_per: val.max_va_per || val.act_wast_per
		});
	}

	let customVaReadonly = (val.isCustomSplitted == 1) ? 'readonly' : va_readonly;
	let customMCReadonly = (val.isCustomSplitted == 1) ? 'readonly' : mc_readonly;
	let customMCSelection = (val.isCustomSplitted == 1) ? 'pointer-events : none;' : mc_type_selection;
	let deleteBtn = (val.isCustomSplitted == 1) ? '<td></td>' : '<td><a href="#" onClick="removeCat_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>';

	var a = $("#cusRow").val();
	var i = ++a;
	$("#cusRow").val(i);

	var rowId = 'cus' + i;
	var row = '<tr id="' + rowId + '">'
		+ '<td><input class="form-control cus_tag_name" type="text" name="est_custom[tag_name][]" value="' + (val.tag_code || val.label || "") + '" placeholder="Enter tag code" required autocomplete="off" style="width:80px"/><input class="est_tag_id" type="hidden" name="est_custom[tag_id][]" value="' + (val.tag_id || "") + '"/><input type="hidden" class="is_partial" name="est_custom[is_partial][]" value="0"><input type="hidden" class="cus_rate_field" name="est_custom[rate_field][]" value="' + (val.rate_field || "") + '"><input type="hidden" class="cus_market_rate_field" name="est_custom[market_rate_field][]" value="' + (val.market_rate_field || "") + '"><input type="hidden" class="is_chttopup_applied" value="0"><input type="hidden" class="is_sr_applied" value="0"><input type="hidden" class="ChitUtilizedDetails"/><input type="hidden" class="SRUtilizedDetails"/><input type="hidden" class="partialy_utilized" value="0"/><input type="hidden" class="isCustomSplitted" value="' + (val.isCustomSplitted || 0) + '"><input type="hidden" class="is_split_row" name="est_custom[is_split_row][]" value="' + (val.is_split_row || 0) + '"></td>'
		+ '<td><input class="form-control cus_old_tag_code" type="text" name="est_custom[old_tag_code][]" value="' + (val.old_tag_code || "") + '" placeholder="Enter Old tag code" required autocomplete="off" style="width:80px"/></td>'
		+ '<td><select class="form-control cus_section" name="est_custom[id_section][]" style="width:150px;">' + select_section + '</select><input type="hidden" class="cus_section_id" value="' + (val.id_section || "") + '" /></td>'
		+ '<td><select class="form-control cus_product" name="est_custom[product][]" style="width:150px;">' + select_product + '</select><input class="cus_product_id" type="hidden" name="est_custom[pro_id][]" value="' + (val.product_id || "") + '"/><input type="hidden" class="tax_group_id" value="' + (val.tax_group_id || val.tgrp_id || "") + '"><input type="hidden" class="tax_percentage" value="' + (val.tax_percentage || "") + '"><input type="hidden" class="tgi_calculation" value="' + (val.tgi_calculation || "") + '"><input type="hidden" class="metal_type" value="' + (val.metal_type || "") + '"><input type="hidden" class="scheme_closure_benefit" value="' + (val.scheme_closure_benefit || "") + '"><input type="hidden" class="cus_sales_mode" value="' + (val.sales_mode || "") + '"></td>'
		+ '<td><select class="form-control cus_design" name="est_custom[design][]" style="width:150px;"></select><input type="hidden" class="cus_des_id" name="est_custom[des_id][]" value="' + (val.design_id || "") + '" /></td>'
		+ '<td><select class="form-control cus_sub_design" name="est_custom[sub_design][]" style="width:150px;"></select><input type="hidden" class="cus_id_sub_design" name="est_custom[id_sub_design][]" value="' + (val.id_sub_design || "") + '" /></td>'
		+ '<td><select class="item_emp_id form-control" style="width:100px !important" name="est_custom[item_emp_id][]" >' + select_emp + '</select></td>'
		+ '<td><select class="form-control cus_purity" name="est_custom[purity][]" style="width:80px;">' + purity_options + '</select></td>'
		+ '<td><input type="number" class="form-control cus_size" name="est_custom[size][]" value="' + (val.size || "") + '" style="width:80px;"/></td>'
		+ '<td><input class="form-control cus_pcs" type="number" name="est_custom[pcs][]" value="' + (val.piece || "1") + '" style="width:80px;"/></td>'
		+ '<td><input type="number" class="form-control cus_gwt" name="est_custom[gwt][]" value="' + (val.gross_wt || "") + '" style="width:80px;" ' + (val.isCustomSplitted == 1 ? 'readonly' : '') + '/><input type="hidden" class="form-control blc_tag_gwt" value="' + (val.gross_wt || "") + '"/></td>'
		+ '<td><div class="form-group"><div class="input-group "><input class="form-control cus_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));" type="number" name="est_custom[lwt][]" value="' + (val.less_wt || "0") + '" step="any" readonly style="width:100px;"><span class="input-group-addon input-sm add_tag_lwt" onclick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span><input type="hidden" class="stone_details" name="est_custom[stone_details][]" value=\'' + JSON.stringify(ui_stone_details) + '\'><input type="hidden" class="stone_price" name="est_custom[stone_price][]" value="' + total_stone_price + '"></div></div></td>'
		+ '<td><input type="number" class="form-control tot_stn_amt" name="est_custom[stone_amt][]" value="' + (val.stn_amt || 0) + '" readonly style="width:100px;" /></td>'
		+ '<td><input type="number" class="form-control tot_dia_amt" name="est_custom[diamond_amt][]" value="' + (val.dia_amt || 0) + '" readonly style="width:100px;" /></td>'
		+ '<td><input type="number" class="form-control cus_nwt" name="est_custom[nwt][]" value="' + (isNaN(parseFloat(val.net_wt)) ? '0.000' : parseFloat(val.net_wt).toFixed(3)) + '" readonly style="width:80px;"/></td>'
		+ '<td><input class="form-control cus_market_rate_value" name="est_custom[est_rate_per_grm][]" type="text" value="' + (val.chit_rate_type == 2 && val.isCustomSplitted == 1 ? val.chit_avg_rate : (val.est_rate_per_grm || "")) + '" style="width:80px;" ' + rate_readonly + ' /></td>'
		+ '<td><select class="form-control cus_mc_type" ' + customMCReadonly + ' style="width:80px; ' + customMCSelection + '"><option value="2" ' + (val.mc_type == 2 ? 'selected' : '') + '>Gram</option><option value="1" ' + (val.mc_type == 1 ? 'selected' : '') + '>Piece</option><option value="3" ' + (val.mc_type == 3 ? 'selected' : '') + '>% on Price</option></select><input type="hidden" value="' + (val.mc_type || 2) + '" name="est_custom[id_mc_type][]" class="id_mc_type"></td>'
		+ '<td><input type="number" ' + customMCReadonly + ' class="form-control cus_mc" name="est_custom[mc][]" value="' + (val.mc_value || "") + '" style="width:80px;"/><input type="hidden" value="' + (val.max_mc || "") + '" name="est_custom[max_mc][]" class="HB_cus_mc"></td>'
		+ '<td><input class="form-control cus_act_va_per" type="number" readonly value="' + (val.max_va_per || "") + '" name="est_custom[max_va_per][]" style="width:80px;"/><input type="hidden" class="cus_pro_va_per"></td>'
		+ '<td><input class="form-control cus_wastage" ' + customVaReadonly + ' type="number" name="est_custom[wastage][]" value="' + (val.wastage_percent || "") + '" style="width:80px;"/><input type="hidden" class="id_wastage_slab" name="est_custom[id_wastage_slab][]" value="' + (val.id_wastage || "") + '"><input type="hidden" class="act_va_per" name="est_custom[act_va_per][]" value="' + (val.act_wast_per || "") + '"><input type="hidden" class="wast_slb_val" name="est_custom[wast_slab_val][]" value="' + (val.slb_val || "") + '"><input type="hidden" class="slab_details" value=\'' + JSON.stringify(ui_slab_details) + '\'></td>'
		+ '<td><input class="form-control cus_wastage_wt" ' + customVaReadonly + ' type="number" value="' + (val.wastage_percent || "") + '" style="width:80px;"/></td>'
		+ '<td><a href="#" onClick="create_new_empty_est_cus_charges_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="charges_details" name="est_custom[charges_details][]" value=\'' + JSON.stringify(ui_charges) + '\'><input type="hidden" class="value_charge" name="est_custom[value_charge][]" value="' + total_charge_amount + '"><input type="hidden" name="est_stones_item[value_charge][]" value="' + total_charge_amount + '"></td>'
		+ '<td><input type="number" class="form-control cus_taxable_amt" name="est_custom[taxable_amt][]" value="' + (val.item_total_tax || "") + '" readonly style="width:80px;"></td>'
		+ '<td><input type="number" class="form-control cus_tax_price" name="est_custom[tax_price][]" value="' + (val.tax_price || "") + '" readonly style="width:80px;"> <input type="hidden" name="est_custom[total_cgst][]" class="cus_cgst" value="' + (val.cgst || "") + '"/><input type="hidden" class="cus_sgst" name="est_custom[total_sgst][]" value="' + (val.sgst || "") + '"/><input type="hidden" class="cus_igst" name="est_custom[total_igst][]" value="' + (val.igst || "") + '"/><input type="hidden" class="cus_taxable_amount" name="est_custom[sale_taxable_amount][]" value="' + (val.item_total_tax || "") + '"/></td>'
		+ '<td><input class="form-control cus_amount" type="number" name="est_custom[amount][]" value="' + (val.item_cost || "") + '" readonly style="width:100px;"/><input type="hidden" class="cus_calculation_based_on" name="est_custom[calculation_based_on][]" value="' + (val.calculation_based_on || "") + '" /><input type="hidden" id="material_details" class="material_details" name="est_custom[material_details][]"><input type="hidden" value="' + total_stone_price + '" class="stone_price" id="stone_price"><input type="hidden" value="0" class="material_price" id="material_price"><input type="hidden" class="market_rate_cost" name="est_custom[market_rate_cost][]" value="' + (val.market_rate_cost || "") + '"><input type="hidden" class="market_rate_tax" name="est_custom[market_rate_tax][]"><input type="hidden" class="cus_dis" value="' + (val.discount || 0) + '"></td>'
		+ deleteBtn
		+ '</tr>';

	$('#estimation_custom_details tbody').append(row);
	let lastRow = $('#estimation_custom_details tbody tr#' + rowId);
	lastRow.find('.cus_section, .cus_product, .item_emp_id, .cus_purity').select2();
	
	if (val.design_options) {
		lastRow.find('.cus_design').html(val.design_options).val(val.design_id).select2({ placeholder: "Design", allowClear: true });
	} else if (val.product_id) {
		get_Activedesign(lastRow, val.product_id);
	}

	if (val.sub_design_options) {
		lastRow.find('.cus_sub_design').html(val.sub_design_options).val(val.id_sub_design).select2({ placeholder: "Sub Design", allowClear: true });
	} else if (val.design_id) {
		get_ActiveSubDesigns(lastRow, val.design_id);
	}

	update_custom_wastage_wt(lastRow);
}

function set_custom_split_details() {
	var total_closing_weight = 0;
	var split_wt_type = $('#tag_split_wt').val() || 1;

	var total_closing_amount = 0;
	var scheme_closing_weight = 0;
	var rate_type = 0;

	$('#estimation_chit_details > tbody > tr').each(function () {
		var curRow = $(this).closest("tr");
		var closing_weight = parseFloat(curRow.find(".closing_weight").val()) || 0;
		var closing_amount = parseFloat(curRow.find(".closing_amount").val()) || 0;
		
		total_closing_weight += closing_weight;
		scheme_closing_weight += closing_weight;
		total_closing_amount += closing_amount;
		rate_type = parseFloat(curRow.find('.rate_type').val()) || 0;
	});

	// Calculate scheme_avg_rate (prevent division by zero)
	var scheme_avg_rate = (total_closing_amount > 0 && scheme_closing_weight > 0)
		? (total_closing_amount / scheme_closing_weight).toFixed(2)
		: 0;

	var tagged_applied_weight = 0;
	$('#estimation_tag_details > tbody > tr').each(function () {
		if ($(this).find('.isTagsplitted').val() == 1) {
			tagged_applied_weight += parseFloat(split_wt_type == 1 ? $(this).find('.gwt').val() : $(this).find('.tot_nwt').val()) || 0;
		}
	});

	var remaining_scheme_weight = Math.max(0, total_closing_weight - tagged_applied_weight);
	var items_to_process = [];

	$('#estimation_custom_details > tbody > tr').each(function () {
		var row = $(this);
		if (row.find('.is_split_row').val() != 1) {
			var item = {
				tag_id: row.find('.est_tag_id').val(),
				label: row.find('.cus_tag_name').val(),
				tag_code: row.find('.cus_tag_name').val(),
				old_tag_code: row.find('.cus_old_tag_code').val(),
				id_section: row.find('.cus_section').val(),
				product_id: row.find('.cus_product_id').val(),
				design_id: row.find('.cus_des_id').val(),
				design_options: row.find('.cus_design').html(),
				id_sub_design: row.find('.cus_id_sub_design').val(),
				sub_design_options: row.find('.cus_sub_design').html(),
				item_emp_id: row.find('.item_emp_id').val(),
				purid: row.find('.cus_purity').val(),
				size: row.find('.cus_size').val(),
				piece: row.find('.cus_pcs').val(),
				gross_wt: parseFloat(row.find('.cus_gwt').val()) || 0,
				net_wt: parseFloat(row.find('.cus_nwt').val()) || 0,
				less_wt: parseFloat(row.find('.cus_lwt').val()) || 0,
				est_rate_per_grm: row.find('.cus_market_rate_value').val(),
				mc_type: row.find('.cus_mc_type').val(),
				mc_value: row.find('.cus_mc').val(),
				max_mc: row.find('.HB_cus_mc').val(),
				max_va_per: row.find('.cus_act_va_per').val(),
				wastage_percent: row.find('.cus_wastage').val(),
				id_wastage: row.find('.id_wastage_slab').val(),
				act_wast_per: row.find('.act_va_per').val(),
				slb_val: row.find('.wast_slb_val').val(),
				item_total_tax: row.find('.cus_taxable_amt').val(),
				item_cost: row.find('.cus_amount').val(),
				calculation_based_on: row.find('.cus_calculation_based_on').val(),
				market_rate_cost: row.find('.market_rate_cost').val(),
				stone_details: JSON.parse(row.find('.stone_details').val() || "[]"),
				charges: JSON.parse(row.find('.charges_details').val() || "[]"),
				sales_mode: row.find('.cus_sales_mode').val(),
				tax_group_id: row.find('.tax_group_id').val(),
				tax_percentage: row.find('.tax_percentage').val(),
				tgi_calculation: row.find('.tgi_calculation').val(),
				metal_type: row.find('.metal_type').val(),
				scheme_closure_benefit: row.find('.scheme_closure_benefit').val(),
				rate_field: row.find('.cus_rate_field').val(),
				market_rate_field: row.find('.cus_market_rate_field').val(),
				discount: row.find('.cus_dis').val()
			};
			items_to_process.push(item);
		}
	});

	$('#estimation_custom_details > tbody').empty();

	var bonus_percent = 0;
	$('#estimation_chit_details > tbody > tr').each(function () {
		bonus_percent = parseFloat($(this).find('.bonus_percent').val()) || 0;
	});

	$.each(items_to_process, function (key, val) {
		var item_weight = (split_wt_type == 1 ? val.gross_wt : val.net_wt);

		if (remaining_scheme_weight > 0 && item_weight > 0) {
			if (remaining_scheme_weight >= item_weight) {
				val.isCustomSplitted = 1;
				val.is_split_row = 0;
				val.chit_avg_rate = scheme_avg_rate;
				val.chit_rate_type = rate_type;
				apply_custom_scheme_benefits(val, bonus_percent);
				append_custom_details(val);
				remaining_scheme_weight -= item_weight;
			} else {
				var benefit_item = $.extend(true, {}, val);
				var regular_item = $.extend(true, {}, val);
				var benefit_weight = remaining_scheme_weight;
				var regular_weight = item_weight - benefit_weight;

				benefit_item.isCustomSplitted = 1;
				benefit_item.is_split_row = 0;
				benefit_item.chit_avg_rate = scheme_avg_rate;
				benefit_item.chit_rate_type = rate_type;
				if (split_wt_type == 1) {
					benefit_item.gross_wt = benefit_weight.toFixed(3);
					benefit_item.net_wt = Math.max(0, benefit_weight - val.less_wt).toFixed(3);
				} else {
					benefit_item.net_wt = benefit_weight.toFixed(3);
					benefit_item.gross_wt = (benefit_weight + val.less_wt).toFixed(3);
				}
				
				// Split monetary values proportionally
				if (parseFloat(val.item_total_tax) > 0 && item_weight > 0) {
					benefit_item.item_total_tax = (parseFloat(val.item_total_tax) * (benefit_weight / item_weight)).toFixed(2);
					regular_item.item_total_tax = (parseFloat(val.item_total_tax) - parseFloat(benefit_item.item_total_tax)).toFixed(2);
				}
				if (parseFloat(val.item_cost) > 0 && item_weight > 0) {
					benefit_item.item_cost = (parseFloat(val.item_cost) * (benefit_weight / item_weight)).toFixed(2);
					regular_item.item_cost = (parseFloat(val.item_cost) - parseFloat(benefit_item.item_cost)).toFixed(2);
				}
				
				apply_custom_scheme_benefits(benefit_item, bonus_percent);
				append_custom_details(benefit_item);

				regular_item.isCustomSplitted = 0;
				regular_item.is_split_row = 1;
				if (split_wt_type == 1) {
					regular_item.gross_wt = regular_weight.toFixed(3);
					regular_item.net_wt = regular_weight.toFixed(3);
				} else {
					regular_item.net_wt = regular_weight.toFixed(3);
					regular_item.gross_wt = regular_weight.toFixed(3);
				}
				regular_item.piece = 0;
				regular_item.less_wt = 0;
				regular_item.stone_details = [];
				append_custom_details(regular_item);
				remaining_scheme_weight = 0;
			}
		} else {
			val.isCustomSplitted = 0;
			val.is_split_row = 0;
			append_custom_details(val);
		}
	});

	calculateCustomItemSaleValue();
}

function apply_custom_scheme_benefits(item, bonus_percent) {
	if (bonus_percent > 0) {
		var original_wastage = parseFloat(item.wastage_percent) || 0;
		item.wastage_percent = (original_wastage - (original_wastage * (bonus_percent / 100))).toFixed(2);
		var original_mc = parseFloat(item.mc_value) || 0;
		item.mc_value = (original_mc - (original_mc * (bonus_percent / 100))).toFixed(2);
	}
}

// =============================================================================
// Hidden Trigger: Triple-click "ESTIMATION ITEMS" label → open Tag Split module
// =============================================================================
(function(){
    var _tsClicks = 0, _tsTimer = null;
    $(document).on('click', '.stickyBlk label[for="Offer"]', function(){
        _tsClicks++;
        clearTimeout(_tsTimer);
        _tsTimer = setTimeout(function(){ _tsClicks = 0; }, 500);
        if (_tsClicks === 3) {
            _tsClicks = 0;
            var href = window.location.href;
            var baseUrl = href.substring(0, href.indexOf('index.php'));
            window.open(baseUrl + 'index.php/admin_ret_tag_parts/form', '_blank');
        }
    });
})();
