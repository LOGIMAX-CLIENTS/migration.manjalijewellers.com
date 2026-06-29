var pathArray = window.location.pathname.split('php/');
var ctrl_page = pathArray[1].split('/');
$(document).ready(function () {
	//RHR starts...
	var b = $('#total_installments').val();
	$('#installment_no').append(
		$("<option></option>")
			.attr("value", '0')
			.text('0')
	);
	for (j = 1; j <= b; j++) {
		if ($("#bonus_ins").val() == j) {
			$('#installment_no').append(
				$("<option></option>")
					.attr("value", j)
					.text(j)
					.attr("selected", "selected")
			);
		} else {
			$('#installment_no').append(
				$("<option></option>")
					.attr("value", j)
					.text(j)
			);
		}
	}
	var i = $('#bonus_ins').val();
	$('#installment_no').val(i);
	$("#installment_cycle").on('change', function () {
		var ins_cycle = $('#installment_cycle').val();
		if (ins_cycle == 2) {
			$('#ins_days_duration').css('display', 'block');
		} else {
			$('#ins_days_duration').css('display', 'none');
			$('#ins_days_duration').val('');
		}
	});
	// esakki restrict dues above total installments #14-12
	$('#advance_months,#unpaid_months,#preclose_months').on('input', function () {
		var tot_ins = $('#total_installments').val();
		var dues = $(this).val();
		if (parseInt(dues) >= parseInt(tot_ins)) {
			$(this).val('');
			$.toaster({ priority: 'danger', title: 'Warning', message: '' + "</br>Please enter less than total installments!" });
		}
	})
	/* $("#purity").select2({
		 placeholder: "Select Metal Purity",
		 allowClear: true
	 }); */
	getActiveMetalPurities();
	$('#metal').on('change', function () {
		$('#purity').empty();
		getActiveMetalPurities();
		let id_metal = $(this).val();
		$.ajax({
			type: 'GET',
			url: base_url + 'index.php/admin_scheme/checkDigiAvailability',
			data: { id_metal: id_metal },
			dataType: 'json',
			success: function (data) {
				if (data.is_digi_gold == 1) {
					$("#digiBox").hide();
					$("#digiWarning").show().text(
						(id_metal == 1 ? "Digi Gold" : "Digi Silver") + " scheme already exists."
					);
					$("#is_digi").prop("checked", false);
				} else {
					$("#digiBox").show();
					$("#digiWarning").hide();
				}
			}
		});
	});
	//RHR ends...   
	$("#is_digi").on('change', function () {
		if ($('#is_digi').is(':checked')) {
			$('#ap_installment,#ap_maturity,#tot_payday_div,#daily_pay_limit').prop('disabled', false);
			$('#metal').focus();
		} else {
			$('#ap_installment,#ap_maturity,#tot_payday_div,#daily_pay_limit').prop('disabled', true);
		}
	});
	if (ctrl_page[0] == 'scheme') {
		get_scheme_list();
		get_weights_list();
	}
	$('#scheme_create').submit(function (e) {
		$('#submit').prop('disabled', true); // preclose interest deduction base on scheme chart //HH
		/* var postData = [];
		 $('#chart_creation_tbl > tbody tr').each(function(idx, row){
			 curRow = $(this);
			 var data = [];
			 //var id_debit_settings = (isNaN(curRow.find('.id_debit_settings').val()) || curRow.find('.id_debit_settings').val() == '')  ? 0 : curRow.find('.id_debit_settings').val();
			 var installment_from = (curRow.find('.installment_from').val() == '')  ? 0 : curRow.find('.installment_from').val();
			 var installment_to = (curRow.find('.installment_to').val() == '')  ? 0 : curRow.find('.installment_to').val();
			 var deduction_type = (curRow.find('.deduction_type').val() == '')  ? 0 : curRow.find('.deduction_type').val();
			 var deduction_value = (isNaN(curRow.find('.deduction_value').val()) || curRow.find('.deduction_value').val() == '')  ? 0 : curRow.find('.deduction_value').val();
			//console.log(deduction_value);
			 if(installment_from != 0 && installment_to != 0 && (deduction_type == 0 || deduction_type==1)&& deduction_value > 0){
				 postData.push({"installment_from":installment_from,"installment_to":installment_to,"deduction_type":deduction_type,"deduction_value":deduction_value}); 
			 }
			console.log(postData);
			 if(postData.length > 0 && postData.length){
			 createChart(postData);
			   //console.log(createChart(postData));
		 }
		 })*/
	});
	//DGS-DCNM 
	var totPayDay = $("input[name='sch[restrict_payment]']:checked").val();
	if (totPayDay == 1) {
		$('#tot_payday_div').css('display', 'block');
	} else {
		$('#tot_payday_div').css('display', 'none');
	}
	$("input[name='sch[restrict_payment]']:radio").change(function () {
		if ($(this).val() == 1) {
			$('#tot_payday_div').css('display', 'block');
		}
		else {
			$('#tot_payday_div').css('display', 'none');
		}
	});
	//DGS-DCNM    	
	$("#discount").change(function () {
		if ($("input[name='sch[discount]']:checked").val() == 1) {
			$("input[name='sch[firstPayDisc_by]']").prop('disabled', false);
			$("#firstPayDisc_value").prop('disabled', false);
		} else {
			$("input[name='sch[firstPayDisc_by]']").prop('disabled', true);
			$("#firstPayDisc_value").prop('disabled', true);
		}
	});
	$("#all_pay_disc").change(function () {
		if ($("input[name='sch[all_pay_disc]']:checked").val() == 1) {
			$("input[name='sch[allpay_disc_by]']").prop('disabled', false);
			$("#allpay_disc_value").prop('disabled', false);
		} else {
			$("input[name='sch[allpay_disc_by]']").prop('disabled', true);
			$("#allpay_disc_value").prop('disabled', true);
		}
	});
	if (ctrl_page[1] == 'add' || ctrl_page[1] == 'edit') {
		$("#allow_advance_in").select2({
			placeholder: "Select Source",
			allowClear: true
		});
		$("#allow_unpaid_in").select2({
			placeholder: "Select Source",
			allowClear: true
		});
		$('#disable_pay').on('change click', function () {
			$("#disable_pay_amt").prop("disabled", !$(this).is(':checked'));
			(!$(this).is(':checked') ? $("#disable_pay_amt").val(0) : '');
		});
		$('#avg_installments').empty();
		$('#interest_ins_sel,#avg_calc_ins').select2({
			placeholder: 'Select Ins'
		});
		var b = $('#total_installments').val();
		for (j = 1; j <= b; j++) {
			$('#interest_ins_sel,#avg_calc_ins').append(
				$("<option></option>")
					.attr("value", j)
					.text(j)
			);
			$('#apply_benefit_min_ins').append(
				$("<option></option>")
					.attr("value", j)
					.text(j)
			);
		}
		var selectedValue = "";
		var sch_check;
		if (($('#discount').is(':checked'))) {
			$("input[name='sch[discount_type]']:radio").attr('disabled', false);
			$('#disc_select').prop('disabled', false);
		}
		else {
			$("input[name='sch[discount_type]']:radio").attr('disabled', true);
			$('#disc_select').prop('disabled', true);
		}
		$('#discount').change(function () {
			if (($('#discount').is(':checked'))) {
				$("input[name='sch[discount_type]']:radio").attr('disabled', false);
				$('#disc_select').prop('disabled', false);
			}
			else {
				$("input[name='sch[discount_type]']:radio").attr('disabled', true);
				$('#disc_select').prop('disabled', true);
			}
		});
		$("input[name='sch[discount_type]']:radio").on('change', function () {
			if ($(this).val() == 1) {
				$('#disc_select').prop('disabled', false);
			}
			else {
				$('#disc_select').prop('disabled', true);
			}
		});
		$('#free_payment').change(
			function () {
				if ($('#free_payment').is(':checked')) {
					$("#allowSecondPay,#approvalReqForFP").prop('disabled', false);
				}
				else {
					$("#allowSecondPay,#approvalReqForFP").prop('disabled', true);
					$("#allowSecondPay,#approvalReqForFP").prop("checked", false);
				}
			});
		if ($('#free_payment').is(':checked')) {
			$('#allowSecondPay').prop("disabled", false);
		} else {
			$('#allowSecondPay').prop('disabled', true);
		}
		if ($('#has_free_ins').is(':checked')) {
			$('#free_instalments').prop("disabled", false);
		}
		$("#free_instalments").change(function () {
			var data = $("#free_instalments").select2('data');
			selectedValue = $(this).val();
			$(".free_payInstallments").val(selectedValue);
		})
		$("#avg_installments").change(function () {
			var data = $("#avg_installments").select2('data');
			selectedValue = $(this).val();
			$(".avg_installments").val(selectedValue);
		});
		$("#branches").change(function () {
			var data = $("#branches").select2('data');
			// console.log(data);
			selectedValue = $(this).val();
			$("#id_branch").val(selectedValue);
		});
		//Stop payment //HH
		$("#select_intall_to_stop").change(function () {
			var data = $("#select_intall_to_stop").select2('data');
			//console.log(data);
			selectedValue = $(this).val();
			$(".select_intall_to_stop").val(selectedValue);
		});
		//referrals installments//
		$("#install_select").change(function () {
			var data = $("#install_select").select2('data');
			// console.log(data);
			selectedValue = $(this).val();
			$(".installs_select").val(selectedValue);
		});
		//avg calc installments//
		$("#avg_calc_ins").change(function () {
			var data = $("#avg_calc_ins").select2('data');
			selectedValue = $(this).val();
			$(".avg_calc_select").val(selectedValue);
		});
		//min & max amt installment
		$("#set_as_min_from").change(function () {
			var data = $("#set_as_min_from").select2('data');
			selectedValue = $(this).val();
			$(".min_select").val(selectedValue);
		});
		$("#set_as_max_from").change(function () {
			var data = $("#set_as_max_from").select2('data');
			selectedValue = $(this).val();
			$(".max_select").val(selectedValue);
		});
		//interest installment installment
		$("#interest_ins_sel").change(function () {
			var data = $("#interest_ins_sel").select2('data');
			selectedValue = $(this).val();
			$(".interest_ins").val(selectedValue);
		});
		//
		$("#disc_select").change(function () {
			var data = $("#disc_select").select2('data');
			// console.log(data);
			selectedValue = $(this).val();
			$(".disc_select").val(selectedValue);
		});
		$("input[name='sch[ref_benifitadd_ins_type]']:radio").on('change click', function () {
			$('#install_select').empty();
			if ($(this).val() == 0) {
				$('#install_select').prop('disabled', true);
			} else {
				$('#install_select').prop('disabled', false);
				var i = $('#total_installments').val();
				for (i = 1; i <= b; i++) {
					$("#install_select").append(
						$("<option></option>")
							.attr("value", i)
							.text(i)
					);
				}
			}
		});
		$("input[name='sch[discount_type]']:radio").on('change click', function () {
			if ($(this).val() == 0) {
				$('#dis_select').prop('disabled', true);
			} else {
				$('#dis_select').prop('disabled', false);
			}
		});
		$("input[name='sch[emp_refferal]']").on('change', function () {
			if ($(this).is(':checked')) {
				$("#emp_refferal_value").prop("disabled", false);
				$("input[name='sch[emp_refferal_by]']").prop('disabled', false);
			} else {
				$("#emp_refferal_value").prop("disabled", true);
				$("#emp_refferal_value").val(0);
				$("input[name='sch[emp_refferal_by]']").prop('disabled', true);
			}
		});
		$("input[name='sch[cus_refferal]']").on("change click", function () {
			if ($(this).is(':checked')) {
				$("#cus_refferal_value").prop("disabled", false);
				$("input[name='sch[cus_refferal_by]']").prop('disabled', false);
			} else {
				$("#cus_refferal_value").prop("disabled", true);
				$("#cus_refferal_value").val(0);
				$("input[name='sch[cus_refferal_by]']").prop('disabled', true);
			}
		})
		if ($("#cus_refferal").val() != 1) {
			$("#cus_refferal_value").prop("disabled", true);
		}
		if ($("#emp_refferal").val() != 1) {
			$("#emp_refferal_value").prop("disabled", true);
		}
		if ($("input[name='sch[interest]']:checked").val() == 1) {
			$('#interest_value').prop('disabled', false);
			$('.interest_block').prop('disabled', false);
		} else {
			$('#interest_value').prop('disabled', true);
			$('.interest_block').prop('disabled', true);
		}
		if ($("input[name='sch[is_lucky_draw]']:checked").val() == 1) //lucky draw settings In scheme master HH //
		{
			$('#max_members').prop('disabled', false);
			$('.isLuckyDraw_block').prop('disabled', false);
		} else {
			$('#max_members').prop('disabled', true);
			$('.isLuckyDraw_block').prop('disabled', true);
		}
		if ($("input[name='sch[tax]']:checked").val() == 1) {
			$('#tax_value').prop('disabled', false);
			$('.tax_block').prop('disabled', false);
		} else {
			$('#tax_value').prop('disabled', true);
			$('.tax_block').prop('disabled', true);
		}
		if ($("input[name='sch[discount]']:checked").val() == 1) {
			$('#firstPayDisc_value').prop('disabled', false);
			$("input[name='sch[firstPayDisc_by]']:radio").prop('disabled', false);
		} else {
			$('#firstPayDisc_value').prop('disabled', true);
			$("input[name='sch[firstPayDisc_by]']:radio").prop('disabled', true);
		}
		if ($("input[name='sch[all_pay_disc]']:checked").val() == 1) {
			$('#allpay_disc_value').prop('disabled', false);
			$("input[name='sch[allpay_disc_by]']:radio").prop('disabled', false);
		} else {
			$('#allpay_disc_value').prop('disabled', true);
			$("input[name='sch[allpay_disc_by]']:radio").prop('disabled', true);
		}
		if ($("input[name='sch[emp_incentive_closing]']").is(':checked')) {
			$(this).val(1);
			$("input[name='sch[closing_incentive_based_on]']").prop('disabled', false);
			$('#proced_closing').prop('disabled', false);
		} else {
			$("input[name='sch[closing_incentive_based_on]']").prop('disabled', true);
			$('#proced_closing').prop('disabled', true);
			$(this).val(0);
		}
		//branches
		//free ins options
		if (ctrl_page[1] == 'edit') {
			var selectedcommodity = $("#metal_val").val();
			if (selectedcommodity != null && selectedcommodity != '') {
				$("#metal").val(selectedcommodity);
			}
			var id = ctrl_page[2];
			$.ajax({
				type: 'GET',
				url: base_url + 'index.php/admin_scheme/getFreeInsBySchId/' + id,
				dataType: 'json',
				success: function (data) {
					console.log(data);
					//referrals installments//
					var ref = data.ref_benifitadd_ins;
					var dis = data.discount_installment;
					var ins = data.stop_payment_installment;
					var avg_cal_ins = data.avg_calc_ins;
					var min_ins = data.set_as_min_from;
					var max_ins = data.set_as_max_from;
					var interest_ins = data.interest_ins;
					console.log(min_ins);
					//	$('#install_select').empty();
					var ref_data = [];
					if (data.avg_calc_ins != null && data.avg_calc_ins != '') {
						$('#avg_calc_ins').val(data.avg_calc_ins);
					} else {
						$('#avg_calc_ins').val('');
					}
					if (data.apply_benefit_min_ins != null && data.apply_benefit_min_ins != '') {
						$('#apply_benefit_min_ins').val(data.apply_benefit_min_ins);
					} else {
						$('#apply_benefit_min_ins').val('');
					}
					var total_ins = $('#total_installments').val();
					//referrals installments//
					$('#free_instalments').empty();
					//	$('#disc_select').empty();
					var str = data.free_payInstallments;
					if (str != null && str != '') {
						var res = str.split(",");
						var dt = [];
						for (var i = 2; i <= total_ins; i++) {
							dt.push({ id: i, text: i });
						}
						$("#free_instalments").select2({
							data: dt
						}).select2('val', res);
						$("#avg_installments").select2({
							data: dt
						});
					}
					$("#free_instalments").select2({
						data: dt
					}).select2('val', res);
					$("#avg_installments").select2({
						data: dt
					});
					for (var i = 1; i <= total_ins; i++) {
						ref_data.push({ id: i, text: i });
					}
					/*$("#install_select").select2({
					   data: ref_data			 	
					 }).select2('val', ref);*/
					$("#branches").select2();
					$("#avg_calc_ins").select2({
						data: ref_data
					}).select2('val', avg_cal_ins);
					$("#set_as_min_from").select2({
						data: ref_data
					}).select2('val', min_ins);
					$("#set_as_max_from").select2({
						data: ref_data
					}).select2('val', max_ins);
					$("#disc_select").select2({
						data: ref_data
					}).select2('val', ref);
					$("#branches").select2();
					$("#disc_select").select2({
						data: ref_data
					}).select2('val', dis);
					$("#select_intall_to_stop").select2({
						data: ref_data
					}).select2('val', ins);
				}
			});
			if ($("input[name='sch[cus_refferal]']").is(':checked')) {
				$("#cus_refferal_value").prop('disabled', false);
			} else {
				$("#cus_refferal_value").prop('disabled', true);
				$("#cus_refferal_value").val(0);
			}
			if ($("input[name='sch[emp_refferal]']").is(':checked')) {
				$("#emp_refferal_value").prop('disabled', false);
			} else {
				$("#emp_refferal_value").prop('disabled', true);
				$("#emp_refferal_value").val(0);
			}
			if ($("input[name='sch[ref_benifitadd_ins_type]']:checked").val() == 1) {
				$("#install_select").prop('disabled', false);
			} else {
				$("#install_select").prop('disabled', true);
			}
			if ($("input[name='sch[cus_refferal]']").is(':checked')) {
				$("input[name='sch[cus_refferal_by]']").prop('disabled', false);
			} else {
				$("input[name='sch[cus_refferal_by]']").prop('disabled', true);
			}
			if ($("input[name='sch[emp_refferal]']").is(':checked')) {
				$("input[name='sch[emp_refferal_by]']").prop('disabled', false);
			} else {
				$("input[name='sch[emp_refferal_by]']").prop('disabled', true);
			}
		}
		else if (ctrl_page[1] == 'add') {
			$("#free_instalments").select2();
			$("#avg_installments").select2();
			$("#branches").select2();
			$("#install_select").select2();
			$("#disc_select").select2();
			$("#select_intall_to_stop").select2();
		}
		if ($("input[name='sch[scheme_type]']:checked").val() == 1 && $('#min_weight').val() != $('#max_weight').val()) {
			$('#allow_unpaid').prop('disabled', 'disabled');
			$('#allow_advance').prop('disabled', 'disabled');
			$('#allow_preclose').prop('disabled', 'disabled');
		}
		$('#allow_unpaid').on('change click', function () {
			//$("input.unpaid_block").prop("disabled", !$(this).is(':checked'));
			$('#unpaid_months').prop("disabled", !$(this).is(':checked'))
			if ($(this).is(':checked')) {
				$('#unpaid_weight_limit').prop("disabled", !$("#opt_weight").is(':checked'))
			}
			else {
				$('#unpaid_weight_limit').prop("disabled", true);
				$('#unpaid_months').val(0);
			}
		});
		$('#total_installments').on('keyup', function () {
			$('#maturity_installment').val(this.value);
			$('#avg_installments').empty();
			var b = $('#total_installments').val();
			for (j = 1; j <= b; j++) {
				$('#avg_installments').append(
					$("<option></option>")
						.attr("value", j)
						.text(j)
				);
			}
			// esakki dues adjust based on total installments #14-12
			var tot_ins = $(this).val();
			var ad_dues = $('#advance_months').val();
			var unpaid_dues = $('#unpaid_months').val();
			var preclose_due = $('#preclose_months').val();
			if (parseInt(ad_dues) != '' && parseInt(ad_dues) > 0 && parseInt(ad_dues) >= parseInt(tot_ins)) {
				$('#advance_months').val('');
			}
			if (parseInt(unpaid_dues) != '' && parseInt(unpaid_dues) > 0 && parseInt(unpaid_dues) >= parseInt(tot_ins)) {
				$('#unpaid_months').val('');
			}
			if (parseInt(preclose_due) != '' && parseInt(preclose_due) > 0 && parseInt(preclose_due) >= parseInt(tot_ins)) {
				$('#preclose_months').val('');
			}
		});
		$('#min_installments').on('change', function () {
			var mat_ins = $('#maturity_installment').val();
			var tot_ins = $('#total_installments').val();
			var min_ins = this.value;
			if (mat_ins != '' || tot_ins != '') {
				if (min_ins > mat_ins && min_ins > tot_ins) {
					alert("Minimum installment cannot be greater than maturity / total installment...");
				} else if (min_ins > tot_ins) {
					alert("Minimum installment cannot be greater than total installment...");
				} else if (min_ins > mat_ins) {
					alert("Minimum installment cannot be greater than maturity installment...");
				}
			}
		});
		$('#total_installments').on('keyup', function () {
			$('#free_instalments').empty();
			var b = $('#total_installments').val();
			for (i = 2; i <= b; i++) {
				$('#free_instalments').append(
					$("<option></option>")
						.attr("value", i)
						.text(i)
				);
			}
		});
		//stop payment installments //HH
		$('#total_installments').on('keyup', function () {
			$('#select_intall_to_stop').empty();
			var b = $('#total_installments').val();
			for (i = 1; i <= b; i++) {
				$('#select_intall_to_stop').append(
					$("<option></option>")
						.attr("value", i)
						.text(i)
				);
			}
		});
		//referrals installments// 
		$('#total_installments').on('keyup', function () {
			$('#disc_select').empty();
			$('#install_select').empty();
			$('#avg_calc_ins').empty();
			$('#apply_benefit_min_ins').empty();
			$('#set_as_min_from').empty();
			$('#set_as_max_from').empty();
			$('#interest_ins_sel').empty();
			var b = $('#total_installments').val();
			$("#disc_select,#install_select,#avg_calc_ins,#apply_benefit_min_ins,#set_as_min_from,#set_as_max_from,#interest_ins_sel").append(
				$("<option></option>")
					.attr("value", '')
					.text('-- Select --')
			);
			for (i = 1; i <= b; i++) {
				$("#disc_select,#install_select,#avg_calc_ins,#apply_benefit_min_ins,#set_as_min_from,#set_as_max_from,#interest_ins_sel").append(
					$("<option></option>")
						.attr("value", i)
						.text(i)
				);
			}
		});
		$('#has_free_ins').on('change', function () {
			if ($(this).is(':checked')) {
				$('#free_instalments').prop("disabled", false);
			}
			else {
				$('#free_instalments').prop("disabled", true);
			}
		});
		//	$("[name='sch[free_payment]']").bootstrapSwitch();
		$('#allow_advance').on('change', function () {
			$("#advance_months").prop("disabled", !$(this).is(':checked'));
			if ($(this).is(':checked')) {
				$('#advance_weight_limit').prop("disabled", !$("#opt_weight").is(':checked'))
			}
			else {
				$('#advance_weight_limit').prop("disabled", true);
				$('#advance_months').val(0);
			}
		});
		//RAHUL 24_11_25
		/* $('#allow_preclose').on('change click',function(){		
			 $("#preclose_months").prop("disabled", !$(this).is(':checked'));
			 (!$(this).is(':checked')? $("#preclose_months").val(0):'');
			 $("[name='sch[preclose_benefits]']").bootstrapSwitch('disabled',!$(this).is(':checked'));
		}); */
		$('#allow_preclose').on('change click', function () {
			let checked = $(this).is(':checked');
			// Enable/Disable months input
			$("#preclose_months").prop("disabled", !checked);
			if (!checked) {
				$("#preclose_months").val(0);
			}
			// If unchecked, also turn OFF the switch (set to NO)
			if (!checked) {
				$("[name='sch[preclose_benefits]']").bootstrapSwitch('state', false, true);
			}
			// Enable/Disable switch
			$("[name='sch[preclose_benefits]']").bootstrapSwitch('disabled', !checked);
			// Show/Hide preclose_benefits box
			if (checked) {
				$("#preclose_benefits_box").show();
				$("#preclose_type_box").show();
			} else {
				$("#preclose_benefits_box").hide();
				$("#preclose_type_box").hide();
			}
		});
		$('#maturity_type').on('change', function () {  //  Maturity Days showed based on the maturity type-2//HH 
			var maturity_type = $(this).val();
			if (maturity_type == 1 || maturity_type == 3) {
				$('#maturity_setting').css('display', 'none');
			}
			else {
				$('#maturity_setting').css('display', 'block');
			}
		});
		$('#maturity_type').on('change', function () {  //  Closing Maturity Days showed based on the maturity type-2//HH 
			var maturity_type = $(this).val();
			if (maturity_type == 1 || maturity_type == 3) {
				$('#close_maturity_setting').css('display', 'none');
			}
			else {
				$('#close_maturity_setting').css('display', 'block');
			}
		});
		if ($("input[name='sch[get_amt_in_schjoin]']:checked").val() == 1) //Get Payment Amount in scheme join and apply as//
		{
			$('#firstPayamt_as_payamt').prop('disabled', false);
			$('#firstPayamt_maxpayable').prop('disabled', false);
		}
		else {
			$('#firstPayamt_as_payamt').prop('disabled', true);
			$('#firstPayamt_maxpayable').prop('disabled', true);
		}
		$('#get_amt_in_schjoin').change(function ()//Get Payment Amount in scheme join settings In scheme master HH //
		{
			if ($("input[name='sch[get_amt_in_schjoin]']:checked").val() == 1) {
				$('#firstPayamt_as_payamt').prop('disabled', false);
				$('#firstPayamt_maxpayable').prop('disabled', false);
			}
			else {
				$("input[name='sch[firstPayamt_maxpayable]']:checked").prop('checked', false);
				$("input[name='sch[firstPayamt_as_payamt]']:checked").prop('checked', false);
				$('#firstPayamt_as_payamt').prop('disabled', true);
				$('#firstPayamt_maxpayable').prop('disabled', true);
			}
		});
		$('#firstPayamt_as_payamt').change(function () {
			if ($("input[name='sch[firstPayamt_as_payamt]']:checked").val() == 1) {
				$("input[name='sch[firstPayamt_maxpayable]']:checked").prop('checked', false);
			}
		});
		$('#firstPayamt_maxpayable').change(function () {
			if ($("input[name='sch[firstPayamt_as_payamt]']:checked").val() == 1) {
				$("input[name='sch[firstPayamt_as_payamt]']:checked").prop('checked', false);
			}
		});
		$("#submit").on('click', function () {
			if ($("input[name='sch[get_amt_in_schjoin]']:checked").val() == 1) {
				if (($("input[name='sch[firstPayamt_as_payamt]']:checked").val() != 1) && $("input[name='sch[firstPayamt_maxpayable]']:checked").val() != 1) {
					$.toaster({ priority: 'warning', title: 'Closing Warning', message: '' + "</br>please select any settings for payable settings" });
					return false;
				}
			}
		});
	}
	/* -- Coded by ARVK -- */
	if (ctrl_page[1] == 'edit' || ctrl_page[1] == 'add') {
		if ($("input[name='sch[scheme_type]']:checked").val() == 0) {
			$("#paymenttype_settings").css("display", "none");
			$("#price_settings").css("display", "none");
			$('#amount').prop('disabled', false);
			$('#min_weight').prop('disabled', 'disabled');
			$('#max_weight').prop('disabled', 'disabled');
			$('input.amtsch_block').prop('disabled', false);
			//$('#min_chance').prop('disabled','disabled');
			//$('#max_chance').prop('disabled','disabled');
			$("#payment_setting").css("display", "block");
			$("#free_pay_settings").css("display", "block");
			$("div#settlement_settingsamt").css("display", "block");
			$("div#payment_type").css("display", "none");
			$('#pay_type').val('');
		}
		else if ($("input[name='sch[scheme_type]']:checked").val() == 2) {
			$("#payment_setting").css("display", "block");
			$("#price_settings").css("display", "block");
			$("div#paymenttype_settings").css("display", "none");
			$("div#settlement_settingsamt").css("display", "block");
			$('#amount').prop('disabled', false);
			$('#min_weight').prop('disabled', 'disabled');
			$('#max_weight').prop('disabled', 'disabled');
			//$('#min_amount').prop('disabled','disabled');
			//$('#max_amount').prop('disabled','disabled');
			//	$('#min_chance').prop('disabled','disabled');
			//	$('#max_chance').prop('disabled','disabled');
			$('#pay_type').val('');
		}
		else if ($("input[name='sch[scheme_type]']:checked").val() == 3) {
			$("#payment_type").css("display", "block");
			$("div#paymenttype_settings").css("display", "none");
			$("div#paymentamount_limit").css("display", "block");
			$("#price_settings").css("display", "none");
			$("div#weighttye_settings").css("display", "block");
			$("div#settlement_settingsamt").css("display", "block");
			$('#amount').prop('disabled', false);
			$("#free_pay_settings").css("display", "none");
			//	$('#min_chance').prop('disabled','disabled');
			//	$('#max_chance').prop('disabled','disabled');
			//	$('#min_amount').prop('disabled','false');
			//	$('#max_amount').prop('disabled','false');
			if ($("input[name='sch[one_time_premium]']:checked").val() == 1) {
				$('#price_settings').css('display', 'block');
				$('#enquiry_settings').css('display', 'block');
				$('#paymenttype_settings').css('display', 'none');
				$('#paymentmethod_settings').css('display', 'none');
				$('#weight_convert').css('display', 'none');
				$('#weighttye_settings').css('display', 'none');
				//	$('#total_installments').prop('disabled',true);
				$('#amount').prop('disabled', true);
				$('#rate_selection').css('display', 'block');
			}
			else {
				$('#price_settings').css('display', 'none');
				$('#enquiry_settings').css('display', 'none');
				$('#paymenttype_settings').css('display', 'block');
				$('#paymentmethod_settings').css('display', 'block');
				$('#weight_convert').css('display', 'none');
				$('#weighttye_settings').css('display', 'none');
				//	$('#total_installments').prop('disabled',false);
				$('#amount').prop('disabled', false);
				$('#rate_selection').css('display', 'none');
			}
			/*  if($('#pay_type').val()==1)
			  {
				  $('#flx_denomintions').css('display','block');   // enabled based on the schem type- flx sch type 1,2//HH 
				 $('#premium_settings').css('display','block');
			  }
			  else if($('#pay_type').val()==2)
			  {
				  $('#flx_denomintions').css('display','block'); // enabled based on the schem type- flx sch type 1,2//HH
				   $('#premium_settings').css('display','none');
				   $('#price_settings').css('display','none');
				   $('#total_installments').prop('disabled',false);
				   $('#amount').prop('disabled',false);
				   $('#paymenttype_settings').css('display','block');
				   $('#weight_convert').css('display','none');
				   $('#weighttye_settings').css('display','none');
				   $('#paymentamount_limit').css('display','block');
				   $('#paymentmethod_settings').css('display','block');
				   $('#min_chance').prop('disabled',false);
				   $('#max_chance').prop('disabled',false);
			  }
			  else if($('#pay_type').val()==3)
			  {		
				   $('#premium_settings').css('display','none');
				   $('#price_settings').css('display','none');
				   $('#total_installments').prop('disabled',false);
				   $('#amount').prop('disabled',false);
				   $('#paymenttype_settings').css('display','block');
				   $('#weight_convert').css('display','block');
				   $('#weighttye_settings').css('display','block');
				   $('#paymentamount_limit').css('display','block');
				   $('#paymentmethod_settings').css('display','block');
			  }
			  else if($('#pay_type').val()==4){
				   $('#premium_settings').css('display','block');
				   $('#price_settings').css('display','none');
				   $('#total_installments').prop('disabled',false);
				   $('#amount').prop('disabled',false);
				   $('#paymenttype_settings').css('display','none');
				   $('#weight_convert').css('display','none');
				   $('#weighttye_settings').css('display','none');
				   $('#paymentamount_limit').css('display','none');
				   $('#paymentmethod_settings').css('display','none');
			  }*/
			if ($('#pay_type').val() == 1) {
				$('#premium_settings').css('display', 'block');
				$('#paymenttype_settings').css('display', 'block');
				$('#paymentamount_limit').css('display', 'block');
				$('#weighttye_settings').css('display', 'none');
				$('#weight_convert').css('display', 'none');
				$('#weight_store').css('display', 'none');
				if ($('#one_time_premium').val() == 1) {
					//	$('#total_installments').prop('disabled',true);
					$('#amount').prop('disabled', true);
				}
			}
			else if ($('#pay_type').val() == 2) {
				$('#premium_settings').css('display', 'none');
				$('#enquiry_settings').css('display', 'none');
				$('#price_settings').css('display', 'none');
				$('#total_installments').prop('disabled', false);
				$('#amount').prop('disabled', false);
				$('#paymenttype_settings').css('display', 'block');
				$('#weight_convert').css('display', 'block');
				$('#weighttye_settings').css('display', 'none');
				$('#paymentamount_limit').css('display', 'block');
				$('#paymentmethod_settings').css('display', 'block');
				$('#weight_store').css('display', 'none');
			}
			else if ($('#pay_type').val() == 3) {
				$('#premium_settings').css('display', 'none');
				$('#price_settings').css('display', 'none');
				$('#total_installments').prop('disabled', false);
				$('#amount').prop('disabled', false);
				$('#paymenttype_settings').css('display', 'block');
				$('#weight_convert').css('display', 'block');
				$('#weighttye_settings').css('display', 'block');
				$('#paymentamount_limit').css('display', 'none');
				$('#paymentmethod_settings').css('display', 'block');
				$('#weight_store').css('display', 'none');
			}
			else if ($('#pay_type').val() == 4 || $('#pay_type').val() == 8) {
				$('#premium_settings').css('display', 'block');
				$('#paymenttype_settings').css('display', 'block');
				$('#weighttye_settings').css('display', 'block');
				$('#paymentamount_limit').css('display', 'none');
				$('#weight_convert').css('display', 'none');
				$('#weight_store').css('display', 'block');
				//	$('#total_installments').prop('disabled',true);
				$('#amount').prop('disabled', true);
				if ($('#pay_type').val() == 8) {
					$('#flx_denomintions').css('display', 'block');
					$('#weight_convert').css('display', 'block');
				}
			}
			else if ($('#pay_type').val() == 5) {
				$('#premium_settings').css('display', 'block');
				$('#paymenttype_settings').css('display', 'block');
				$('#weighttye_settings').css('display', 'none');
				$('#paymentamount_limit').css('display', 'block');
				$('#weight_convert').css('display', 'none');
				$('#weight_store').css('display', 'block');
				//	$('#total_installments').prop('disabled',true);
				$('#amount').prop('disabled', true);
			}
			else if ($('#pay_type').val() == 6 || $('#pay_type').val() == 7) {
				$('#paymenttype_settings').css('display', 'block');
				$('#flexi_installments').css('display', 'block');
				$('#premium_settings').css('display', 'none');
				$('#paymentamount_limit').css('display', 'none');
				$('#weighttye_settings').css('display', 'none');
				$('#allow_advance').prop('disabled', true);
				$('#allow_unpaid').prop('disabled', true);
				$('#weight_convert').css('display', 'block');
				$('#flx_denomintions').css('display', 'block');
			}
			if ($("input[name='sch[otp_price_fixing]']:checked").val() == 1) {
				$('#otp_price_fix_single').prop('disabled', false);
				$('#otp_price_fix_multiple').prop('disabled', false);
			}
			else {
				$('#otp_price_fix_single').prop('disabled', true);
				$('#otp_price_fix_multiple').prop('disabled', true);
			}
		}
		else {
			$("#payment_setting").css("display", "block");
			$("#free_pay_settings").css("display", "block");
			$("#price_settings").css("display", "block");
			$("div#paymenttype_settings").css("display", "block");
			$("div#paymentamount_limit").css("display", "none");
			$("div#paymentmethod_settings").css("display", "none");
			$("div#weighttye_settings").css("display", "block");
			$("div#settlement_settingswgt").css("display", "block");
			$('#amount').prop('disabled', 'disabled');
			$('#amount').val('0.00');
			$('#min_weight').prop('disabled', false);
			$('#max_weight').prop('disabled', false);
			//	$('#min_chance').prop('disabled',false);
			//	$('#max_chance').prop('disabled',false); 
			$('#pay_type').val('');
		}
		//Scheme Type Change functions
		$("input[name='sch[scheme_type]']:radio").change(function () {
			if ($(this).val() == 0) {
				$("div#paymenttype_settings").css("display", "none");
				$('#amount').prop('disabled', false);
				$("#price_settings").css("display", "none");
				$("#payment_setting").css("display", "block");
				$("#free_pay_settings").css("display", "block");
				$('#min_weight').prop('disabled', 'disabled');
				$('#max_weight').prop('disabled', 'disabled');
				$('input.amtsch_block').prop('disabled', false);
				$("input[name='sch[payment_chances]']").prop('disabled', 'disabled');
				//	$('#min_chance').prop('disabled','disabled');
				//	$('#max_chance').prop('disabled','disabled');
				$('#min_amount').prop('disabled', 'disabled');
				$('#max_amount').prop('disabled', 'disabled');
				$("div#payment_type").css("display", "none");
				$('#pay_type').val('');
			}
			else if ($(this).val() == 2) {
				$("div#paymenttype_settings").css("display", "none");
				$("#payment_setting").css("display", "block");
				$("#price_settings").css("display", "block");
				$('#amount').prop('disabled', false);
				$('#min_weight').prop('disabled', 'disabled');
				$('#max_weight').prop('disabled', 'disabled');
				$('input.amtsch_block').prop('disabled', false);
				$("input[name='sch[payment_chances]']").prop('disabled', 'disabled');
				//	$('#min_chance').prop('disabled','disabled');
				//	$('#max_chance').prop('disabled','disabled');
				$('#min_amount').prop('disabled', 'disabled');
				$('#max_amount').prop('disabled', 'disabled');
				$("div#payment_type").css("display", "none");
				$('#pay_type').val('');
			}
			else if ($(this).val() == 3) {
				$("#price_settings").css("display", "none");
				$("#payment_type").css("display", "block");
				$("div#paymenttype_settings").css("display", "none"); //Limit settings,Convert settings
				$("div#paymentamount_limit").css("display", "block");
				$("div#weighttye_settings").css("display", "block");
				$("input[name='sch[payment_chances]']").prop('disabled', false);
				$('#amount').prop('disabled', 'disabled');
				$('#min_weight').prop('disabled', false);
				$('#max_weight').prop('disabled', false);
				$('#max_amt_chance').prop('disabled', false);
				$('#min_amt_chance').prop('disabled', false);
				$("#payment_setting").css("display", "block");
				$("#free_pay_settings").css("display", "none");
				$('input.amtsch_block').prop('disabled', false);
				//	$('#min_chance').prop('disabled','disabled');
				//$('#min_amount').prop('disabled',false);
				//	$('#max_amount').prop('disabled',false);
				$('#max_chance').prop('disabled', 'disabled');
			}
			else {
				$("#price_settings").css("display", "none");
				$("div#paymentmethod_settings").css("display", "none");
				$("div#paymenttype_settings").css("display", "block");
				$("div#paymentamount_limit").css("display", "none");
				$("div#weighttye_settings").css("display", "block");
				$("#payment_setting").css("display", "block");
				$("#free_pay_settings").css("display", "block");
				$('#minimal').hide();
				$('#wgt_cnvrt').hide();
				$('#amount').prop('disabled', 'disabled');
				$('#amount').val('0.00');
				$('#min_weight').prop('disabled', false);
				$('#max_weight').prop('disabled', false);
				//	$('#min_amount').prop('disabled','disabled');
				//	$('#max_amount').prop('disabled','disabled');
				$("input[name='sch[payment_chances]']").prop('disabled', false);
				$("div#payment_type").css("display", "none");
			}
		});
		//Scheme Type Change functions
		$('#pay_type').on('change', function () {
			//	$('#min_amount').prop('disabled',false);
			//	$('#max_amount').prop('disabled',false);
			var pay_type = $(this).val();
			if (pay_type != '') {
				$('#flexible_sch_type').val(pay_type);
				if (pay_type == 1) {
					$('#flexi_installments').css('display', 'none');
					$('#premium_settings').css('display', 'block');
					$('#paymentmethod_settings').css('display', 'block');
					$('#paymenttype_settings').css('display', 'block');
					$('#paymentamount_limit').css('display', 'block');
					$('#weighttye_settings').css('display', 'none');
					$('#weight_convert').css('display', 'none');
					$('#weight_store').css('display', 'none');
					if ($('#one_time_premium').val() == 1) {
						//	$('#total_installments').prop('disabled',true);
						$('#amount').prop('disabled', true);
					}
					$('#price_settings').css('display', 'none');
					$('#total_installments').prop('disabled', false);
					$('#amount').prop('disabled', false);
					$('#weight_convert').css('display', 'none');
					$('#weighttye_settings').css('display', 'none');
					$('#paymentamount_limit').css('display', 'block');
					$('#flexi_installments').css('display', 'none');
				}
				else if (pay_type == 2) {
					$('#premium_settings').css('display', 'none');
					$('#price_settings').css('display', 'none');
					$('#enquiry_settings').css('display', 'none');
					$('#total_installments').prop('disabled', false);
					$('#amount').prop('disabled', false);
					$('#paymenttype_settings').css('display', 'block');
					$('#weight_convert').css('display', 'block');
					$('#weighttye_settings').css('display', 'none');
					$('#paymentamount_limit').css('display', 'block');
					$('#paymentmethod_settings').css('display', 'block');
					$('#weight_store').css('display', 'none');
					$('#flexi_installments').css('display', 'none');
				}
				else if (pay_type == 3) {
					$('#premium_settings').css('display', 'none');
					$('#price_settings').css('display', 'none');
					$('#enquiry_settings').css('display', 'none');
					$('#total_installments').prop('disabled', false);
					$('#amount').prop('disabled', false);
					$('#paymenttype_settings').css('display', 'block');
					$('#weight_convert').css('display', 'block');
					$('#weighttye_settings').css('display', 'block');
					$('#paymentamount_limit').css('display', 'none');
					$('#paymentmethod_settings').css('display', 'block');
					$('#weight_store').css('display', 'none');
					$('#flexi_installments').css('display', 'none');
				}
				else if (pay_type == 4 || pay_type == 8) {
					$('#premium_settings').css('display', 'block');
					$('#price_settings').css('display', 'none');
					$('#total_installments').prop('disabled', false);
					$('#paymenttype_settings').css('display', 'block');
					$('#weight_convert').css('display', 'block');
					$('#weighttye_settings').css('display', 'block');
					$('#paymentamount_limit').css('display', 'none');
					$('#weight_convert').css('display', 'none');
					$('#weight_store').css('display', 'block');
					//	$('#total_installments').prop('disabled',true);
					$('#amount').prop('disabled', true);
					$('#flexi_installments').css('display', 'none');
					if (pay_type == 8) {
						$('#flx_denomintions').css('display', 'block');
						$('#weight_convert').css('display', 'block');
					}
				}
				else if ($('#pay_type').val() == 5) {
					$('#premium_settings').css('display', 'block');
					$('#paymenttype_settings').css('display', 'block');
					$('#weighttye_settings').css('display', 'none');
					$('#paymentamount_limit').css('display', 'block');
					$('#weight_convert').css('display', 'none');
					$('#weight_store').css('display', 'block');
					//	$('#total_installments').prop('disabled',true);
					$('#amount').prop('disabled', true);
				}
				else if ($('#pay_type').val() == 6 || $('#pay_type').val() == 7) {
					$('#paymenttype_settings').css('display', 'block');
					$('#flexi_installments').css('display', 'block');
					$('#premium_settings').css('display', 'none');
					$('#paymentamount_limit').css('display', 'none');
					$('#weighttye_settings').css('display', 'none');
					$('#allow_advance').prop('disabled', true);
					$('#allow_unpaid').prop('disabled', true);
					$('#weight_convert').css('display', 'block');
					$('#flx_denomintions').css('display', 'block');
				}
			}
			else {
				$('#flexible_sch_type').val('');
			}
		});
		$("input[name='sch[one_time_premium]']:radio").on('change', function () {
			var one_time_premium = $(this).val();
			if (one_time_premium == 1) {
				$('#rate_selection').css('display', 'block');
				$('#price_settings').css('display', 'block');
				$('#enquiry_settings').css('display', 'block');
				/*$('#paymenttype_settings').css('display','none');
				$('#paymentmethod_settings').css('display','none');
				$('#weight_convert').css('display','none');
				$('#weighttye_settings').css('display','none');
				$('#amount').prop('disabled',true);*/
			}
			else {
				if ($('#pay_type').val() == 4 || $('#pay_type').val() == 8) {
					$('#weight_convert').css('display', 'none');
					$('#weighttye_settings').css('display', 'none');
					//	$('#total_installments').prop('disabled',true);
					$('#amount').prop('disabled', true);
					$('#rate_selection').css('display', 'none');
					if ($('#pay_type').val() == 8) {
						$('#flx_denomintions').css('display', 'block');
						$('#weight_convert').css('display', 'block');
					}
				}
				else {
					$('#price_settings').css('display', 'none');
					$('#enquiry_settings').css('display', 'none');
					$('#paymenttype_settings').css('display', 'block');
					$('#paymentmethod_settings').css('display', 'block');
					$('#weight_convert').css('display', 'none');
					$('#weighttye_settings').css('display', 'none');
					$('#total_installments').prop('disabled', false);
					$('#amount').prop('disabled', false);
					$('#rate_selection').css('display', 'none');
				}
			}
		});
		$("input[name='sch[otp_price_fixing]']:radio").on('change', function () {
			var otp_price_fixing = $(this).val();
			if (otp_price_fixing == 0) {
				$('#otp_price_fix_single').prop('disabled', true);
				$('#otp_price_fix_multiple').prop('disabled', true);
			}
			else {
				$('#otp_price_fix_single').prop('disabled', false);
				$('#otp_price_fix_multiple').prop('disabled', false);
			}
		});
		$("input[name='sch[otp_price_fix_type]']:radio").on('change', function () {
			var otp_price_fix_type = $(this).val();
			if (otp_price_fix_type == 2) {
				//$('#rate_fix_sch_join').prop('disabled',true);
				//$('#rate_fix_sch_close').prop('disabled',true);
				//$('#rate_fix_anytime').prop('disabled',true);
			}
			else {
				$('#rate_fix_sch_join').prop('disabled', false);
				$('#rate_fix_sch_close').prop('disabled', false);
				$('#rate_fix_anytime').prop('disabled', false);
			}
		});
		// For Top up scheme settings 
		if ($("input[name='sch[is_topup_scheme]']:checked").length > 0) {
			$('#topUpSchemeDiv').show();
		} else {
			$('#topUpSchemeDiv').hide();
		}
		//RAHUL 24_11_25
		let allowPreclose = $("input[name='sch[allow_preclose]']").is(":checked");
		if (allowPreclose) {
			// enable the switch
			$("[name='sch[preclose_benefits]']").bootstrapSwitch('disabled', false);
			$("#preclose_benefits_box").show();
			$("#preclose_type_box").show();
		} else {
			// disable + set NO
			$("[name='sch[preclose_benefits]']").bootstrapSwitch('state', false, true);
			$("[name='sch[preclose_benefits]']").bootstrapSwitch('disabled', true);
			$("#preclose_benefits_box").hide();
			$("#preclose_type_box").hide();
		}
		$('#allow_preclose').on('change click', function () {
			let checked = $(this).is(':checked');
			// Enable/Disable months input
			$("#preclose_months").prop("disabled", !checked);
			if (!checked) {
				$("#preclose_months").val(0);
			}
			// If unchecked, also turn OFF the switch (set to NO)
			if (!checked) {
				$("[name='sch[preclose_benefits]']").bootstrapSwitch('state', false, true);
				$("[name='sch[preclose_benefits]']").bootstrapSwitch('disabled', !checked);
			}
			// Enable/Disable switch
			// $("[name='sch[preclose_benefits]']").bootstrapSwitch('disabled', !checked);
			// Show/Hide preclose_benefits box
			if (checked) {
				$("#preclose_benefits_box").show();
				$("#preclose_type_box").show();
			} else {
				$("#preclose_benefits_box").hide();
				$("#preclose_type_box").hide();
			}
		});
	}
	$("#min_weight,#max_weight").on('blur keyup', function () {
		if ($("input[name='sch[scheme_type]']:checked").val() == 1 && $('#min_weight').val() == $('#max_weight').val()) {
			//	alert($("input[name='sch[scheme_type]']:checked").val());
			$('#allow_unpaid').prop('disabled', false);
			$('#allow_advance').prop('disabled', false);
			$('#allow_preclose').prop('disabled', false);
		}
		else if ($("input[name='sch[scheme_type]']:checked").val() == 1 && $('#min_weight').val() != $('#max_weight').val()) {
			//alert($("input[name='sch[scheme_type]']:checked").val());
			$('#allow_unpaid').prop('disabled', 'disabled');
			$('#allow_advance').prop('disabled', 'disabled');
			$('#allow_preclose').prop('disabled', 'disabled');
		}
	})
	/* -- / Coded by ARVK -- */
	//For editor
	if ($('#description').length > 0) {
		CKEDITOR.replace('description');
	}
	if ($('#key_benifits_description').length > 0) {
		CKEDITOR.replace('key_benifits_description');
	}
	$('#tax_value').on('keypress keyup blur change click', function () {
		$('#total_tax').val(calc_tax());
	});
	$("input:radio[name='sch[tax_by]']").on('change click', function () {
		$('#total_tax').val(calc_tax());
	});
	$('#interest_value').on('keypress keyup keydown blur change click', function () {
		$('#total_interest').val(calc_interest());
	});
	$("input:radio[name='sch[interest_by]']").on('change click', function () {
		$('#total_interest').val(calc_interest());
	});
	//to get metals
	//	get_metal();	
	//to get Classifications
	get_classifications();
	// to get branches
	get_branches();
	// to get branches
	$("input[name='sch[payment_chances]']:radio").change(function () {
		if ($(this).val() == 1) {
			// $("#price_settings").css("display", "none");
			$('#min_chance').prop('readonly', false);
			$('#max_chance').prop('readonly', false);
		}
		else {
			$('#min_chance').prop('readonly', 'true');
			$('#max_chance').prop('readonly', 'true');
		}
	});
	$('#isInterest').change(function () {
		$("input.interest_block").prop("disabled", !$(this).is(':checked'));
		$("#interest_value").prop("disabled", !$(this).is(':checked'));
		$("#interest_value").val('0');
		$("#total_interest").val('0.00');
	});
	$('#isLuckyDraw').change(function () {  //lucky draw settings In scheme master HH //
		$("input.isLuckyDraw_block").prop("disabled", !$(this).is(':checked'));
		$("#has_prize").prop("disabled", !$(this).is(':checked'));
		$("#max_members").prop("disabled", !$(this).is(':checked'));
		$("#has_prize").val('0');
		$("#max_members").val('');
	});
	$('#isTaxable').change(function () {
		$("input.tax_block").prop("disabled", !$(this).is(':checked'));
		$("#tax_value").prop("disabled", !$(this).is(':checked'));
		$("#tax_value").val('0');
		$("#total_tax").val('0.00');
	});
	//  For Top up scheme 
	$('#topUpMinvalue, #topUpMaxvalue, #topupDenomination').on('input', function (e) {
		// console.log($(this).attr('id') + " => " + $(this).val());
		let value = $(this).val();
		value = value.replace(/[^0-9]/g, '');
		$(this).val(value);
	});
	$('#topUpSchemeChartAddBtn').on('click', function () {
		createChartRowForTopUpScheme();
	})
});
function get_metal() {
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/scheme/get_metals',
		dataType: 'json',
		success: function (data) {
			//console.log(data);
			$.each(data, function (key, data) {
				$('#metal').append(
					$('<option></option>')
						.attr('value', data.id)
						.text(data.name)
				);
				selectid = $('#metal_val').val();
				$('#metal').val(selectid);              //rhr
			});
		}
	});
}
function get_classifications() {
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/scheme/get_classifications',
		dataType: 'json',
		success: function (data) {
			//console.log(data);
			$.each(data, function (key, data) {
				$('#classify').append(
					$('<option></option>')
						.attr('value', data.id)
						.text(data.name)
				);
				selectid = $('#classify_val').val();
				$('#classify').val((selectid != null ? selectid : 0));
			});
		}
	});
}
function get_branches() {
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/scheme/get_branches',
		dataType: 'json',
		success: function (data) {
			//	console.log(data);
			$.each(data, function (key, data) {
				$('#branches').append(
					$('<option></option>')
						.attr('value', data.id_branch)
						.text(data.name)
				);
				var ar = $('#sel_br').data('sel_br');
				console.log(data);
				console.log(ar);
				$('#branches').select2('val', ar);
				console.log($('#branches').val());
			});
		}
	});
}
//calculate tax
function calc_tax() {
	var selected = $("input[name='sch[tax_by]']:checked").val();
	var duration = parseFloat($('#total_installments').val()).toFixed(2);
	var amount = (parseFloat($('#amount').val()) * parseFloat(duration)).toFixed(2);
	var tax_val = parseFloat($("#tax_value").val()).toFixed(2);
	var total = 0;
	if (selected == 0) {
		total = (parseFloat(amount) * (parseFloat(tax_val) / 100)).toFixed(2);
	}
	else {
		total = parseFloat(tax_val).toFixed(2);
	}
	if (isNaN(total)) {
		total = 0;
	}
	return total;
}
//calculate interest
function calc_interest() {
	var selected = $("input[name='sch[interest_by]']:checked").val();
	var duration = parseFloat($('#total_installments').val()).toFixed(2);
	var amount = (parseFloat($('#amount').val()) * parseFloat(duration)).toFixed(2);
	var interest_val = parseFloat($("#interest_value").val()).toFixed(2);
	var total = 0;
	if (selected == 0) {
		total = (parseFloat(amount) * (parseFloat(interest_val) / 100)).toFixed(2);
	}
	else {
		total = parseFloat(interest_val).toFixed(2);
	}
	if (isNaN(total)) {
		total = 0;
	}
	return total;
}
function get_scheme_list() {
	my_Date = new Date();
	$("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/scheme/ajax_scheme_list?nocache=" + my_Date.getUTCSeconds(),
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			set_scheme_list(data);
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_scheme_list(data) {
	var scheme = data.data;
	var access = data.access;
	console.log(scheme);
	$('#total_schemes').text(scheme.length);
	if (access.add == '0') {
		$('#add_scheme').attr('disabled', 'disabled');
	}
	var oTable = $('#scheme_list').DataTable();
	oTable.clear().draw();
	if (scheme != null && scheme.length > 0) {
		oTable = $('#scheme_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true,
			"bSort": true,
			"aaData": scheme,
			"order": [[0, 'desc']],
			"aoColumns": [{ "mDataProp": "id_scheme" },
			{ "mDataProp": "scheme_name" },
			{ "mDataProp": "code" },
			{
				"mDataProp": function (row, type, val, meta) {
					var type = (row.scheme_type == '0' ? "Amount" : (row.scheme_type == '1' ? "Weight" : row.scheme_type == '3' ? "FLXIBLE_AMOUNT" : "Amount to Weight"));
					return type;
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					var amount = ((row.scheme_type == '0' || row.scheme_type == '2') ? row.amount : "-");
					return amount;
				}
			},
			{ "mDataProp": "total_installments" },
			{
				"mDataProp": function (row, type, val, meta) {
					return row.allow_advance == '0' ? "No" : "Yes";
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					return row.advance_months == '1' ? row.advance_months : "-";
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					//active_url =base_url+"index.php/admin_scheme/customer_status/"+(row.active==1?0:1)+"/"+row.id_customer; 
					return "<i class='fa " + (row.active == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.active == 1 ? 'green' : 'red') + "'></i></a>"
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					id = row.id_scheme;
					edit_url = (access.edit == '1' ? base_url + 'index.php/scheme/edit/' + id : '#');
					delete_url = (access.delete == '1' ? base_url + 'index.php/scheme/delete/' + id : '#');
					delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
					action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
						(access.edit == '1' ? '<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' : '') +
						(access.delete == '1' ? '<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li>' : '') + '</ul></div>';
					return action_content;
				}
			}]
		});
	}
}
//GG
$("#edit_sch_img").change(function (e) {
	e.preventDefault();
	//alert(asd);
	validate_Image(this);
});
function validate_Image() {
	console.log(arguments);
	if (arguments[0].id == 'edit_sch_img') {
		var preview = $('#edit_sch_img_preview');
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
//GG update
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
// preclose interest deduction based on scheme chart //HH
//tkv Chit General Advance settings (GA) block with separate benefit settings .... Dt Added : 06-11-2023, By: #AB
$("#apply_adv_benefit").on("click", function () {    // show button based on the checkbox click//
	$(".adv_answer").toggle(this.checked);
});
$("#adv_proceed").on("click", function () {
	adv_benefit_chart_table();
});
function adv_benefit_chart_table() {
	var i = 1;
	var table_rows = $('#adv_benefit_chart tbody tr').length;
	var row = "<tr rowID='" + i + "'>"
		+ "<td><select class='interest_by' name='adv_chart[" + table_rows + "][interest_by]'><option value='0'>Month Wise</option></select></td>"
		+ "<td>From<input type='number' class='installment_from' name='adv_chart[" + table_rows + "][installment_from]' value='1' style='width: 50px;'>To<input type='number' name='adv_chart[" + table_rows + "][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td>"
		+ "<td><input type='radio' name='adv_chart[" + table_rows + "][interest_type]' class='interest_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='adv_chart[" + table_rows + "][interest_type]' class='interest_type' checked  value='0'  style='width: 50px;'>%</td>"     //DGS-DCNM
		+ "<td><input type='any' name='adv_chart[" + table_rows + "][interest_value]' value='10' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>"
		+ "<td><div><button id='" + i + "' class='delete btn btn-danger' onClick='remove_row($(this).closest(\'tr\'));' name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>"
		+ "</tr>";
	$('#adv_benefit_chart tbody').append(row);
	$("#save_blk").css("display", "block");
}
//tkv Chit GA ends
// Toggle required on commodity selects based on chart section visibility
function toggleChartCommodityRequired() {
	var chartVisible = $("#interest_ins_block").is(':visible') && $("#isapply_benefit_by_chart").is(':checked');
	$("select[name*='commodityIdForChart']").prop('required', chartVisible);
}
$("#isapply_benefit_by_chart").on("click", function () {    // show button based on the checkbox click//
	$(".answer").toggle(this.checked);
	toggleChartCommodityRequired();
});
$("#proced").on("click", function () {
	createchart_table();
});
function old_createchart_table() {
	var i = 1;
	var table_rows = $('#chart_creation_tbl tbody tr').length;
	var row = "<tr rowID='" + i + "'>"
		+ "<td><select class='interest_by' name='installmentchart[" + table_rows + "][interest_by]'><option value='0'>By Installments</option><option value='1'>By Days</option></select></td>"      //DGS-DCNM
		+ "<td>From<input type='number' class='installment_from' name='installmentchart[" + table_rows + "][installment_from]' value='1' style='width: 50px;'>To<input type='number' name='installmentchart[" + table_rows + "][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td>"
		// +"<td><select class='form-control'  name='installmentchart["+table_rows+"][interest_mode]' ><option value='1'>Amount</option><option value='2'>Discount on Wastage in purchase</option><option value='3'>Discount on Wastage & GST</option></td>"
		+ "<td><input type='radio' name='installmentchart[" + table_rows + "][interest_type]' class='interest_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentchart[" + table_rows + "][interest_type]' class='interest_type' checked  value='0'  style='width: 50px;'>%</td>"     //DGS-DCNM
		+ "<td><input type='any' name='installmentchart[" + table_rows + "][interest_value]' value='10' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>"
		+ "<td><div><button id='" + i + "' class='delete btn btn-danger' onClick='remove_row($(this).closest(\'tr\'));' name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>"
		+ "</tr>";
	$('#chart_creation_tbl tbody').append(row);
	$("#save_blk").css("display", "block");
}
function createchart_table() {  //RHR
	var i = 1;
	var table_rows = $('#chart_creation_tbl tbody tr').length;
	if (table_rows == 0) {
		var row = "<tr rowID='0'>"
			+ "<td><b>By Installment</b><input type='hidden' name='installmentchart[" + table_rows + "][int_calc_on]'  value='2'></td>"
			+ "<td>-</td>"
			+ "<td>-</td>"     //DGS-DCNM
			+ "<td>-</td>"
			+ "<td>-</td>"
			+ "<td><select id='installment_no' class='installment_no' name='installmentchart[" + table_rows + "][installment_no]'>"
			+ "</select></td>"
			+ "<td>-</td>"
			+ "<td>-</td>"
			+ "</td>"
			+ "</tr>"
			+ "<tr rowID='" + i + "'>"
			+ "<td><b>By Value</b><input type='hidden' name='installmentchart[" + i + "][int_calc_on]'  value='1'></td>"
			+ "<td><select class='interest_by' name='installmentchart[" + i + "][interest_by]'><option value='0'>By Installments</option><option value='1'>By Days</option><option value='2'>Purchase VA&MC Discount</option></select></td>"      //DGS-DCNM
			+ "<td>From<input type='number' class='installment_from' name='installmentchart[" + i + "][installment_from]' value='1' style='width: 50px;'>To<input type='number' name='installmentchart[" + i + "][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td>"
			+ "<td><input type='radio' name='installmentchart[" + i + "][interest_type]' class='interest_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentchart[" + i + "][interest_type]' class='interest_type' checked  value='0'  style='width: 50px;'>%</td>"     //DGS-DCNM
			+ "<td><input type='any' name='installmentchart[" + i + "][interest_value]' value='10' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>"
			+ "<td>-</td>"
			+ "<td><select name='installmentchart[" + i + "][commodityIdForChart]' class='form-control' required='true'><option value='' disabled selected hidden>Please Select Commodity</option>"
			+ "<option value='1' selected>GOLD</option>"
			+ "<option value='2'>SILVER</option>"
			+ "</select>"
			+ "</td>"
			+ "<td><div><button id='" + i + "' class='delete btn btn-danger' onClick='remove_row($(this).closest(\'tr\'));' name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>"
			+ "</tr>";
	} else {
		var row = "<tr rowID='" + i + "'>"
			+ "<td><b>By Value</b><input type='hidden' name='installmentchart[" + table_rows + "][int_calc_on]'  value='1'></td>"
			+ "<td><select class='interest_by' name='installmentchart[" + i + "][interest_by]'><option value='0'>By Installments</option><option value='1'>By Days</option><option value='2'>Purchase VA&MC Discount</option></select></td>"      //DGS-DCNM      //DGS-DCNM
			+ "<td>From<input type='number' class='installment_from' name='installmentchart[" + table_rows + "][installment_from]' value='1' style='width: 50px;'>To<input type='number' name='installmentchart[" + table_rows + "][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td>"
			+ "<td><input type='radio' name='installmentchart[" + table_rows + "][interest_type]' class='interest_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentchart[" + table_rows + "][interest_type]' class='interest_type' checked  value='0'  style='width: 50px;'>%</td>"     //DGS-DCNM
			+ "<td><input type='any' name='installmentchart[" + table_rows + "][interest_value]' value='10' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>"
			+ "<td>-</td>"
			+ "<td><select name='installmentchart[" + table_rows + "][commodityIdForChart]' class='form-control' required='true'><option value='' disabled selected hidden>Please Select Commodity</option>"
			+ "<option value='1' selected>GOLD</option>"
			+ "<option value='2'>SILVER</option>"
			+ "</select>"
			+ "</td>"
			+ "<td><div><button id='" + i + "' class='delete btn btn-danger' onClick='remove_row($(this).closest(\'tr\'));' name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>"
			+ "</tr>";
	}
	$('#chart_creation_tbl tbody').append(row);
	var b = $('#total_installments').val();
	$('#installment_no').append(
		$("<option></option>")
			.attr("value", '0')
			.text('0')
	);
	for (j = 1; j <= b; j++) {
		$('#installment_no').append(
			$("<option></option>")
				.attr("value", j)
				.text(j)
		);
	}
	$("#save_blk").css("display", "block");
}
function createpreclose_chart_table() {
	var i = 1;
	var table_rows = $('#preclose_chart_creation_tbl tbody tr').length;
	var row = "<tr rowID='" + i + "'>"
		+ "<td><select class='deduction_by' name='installmentchart[" + table_rows + "][deduction_by]'><option value='0'>By Installments</option><option value='1'>By Days</option></select></td>"      //DGS-DCNM
		+ "<td>From<input type='number' name='installmentpreclosechart[" + table_rows + "][installment_from]' class='installment_from' value='1' style='width: 50px;'>To<input type='number' name='installmentpreclosechart[" + table_rows + "][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td><td><input type='radio' name='installmentpreclosechart[" + table_rows + "][deduction_type]' class='deduction_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentpreclosechart[" + table_rows + "][deduction_type]' class='deduction_type' checked  value='0'  style='width: 50px;'>%</td><td><input type='any' name='installmentpreclosechart[" + table_rows + "][deduction_value]' value='10' class='form-control deduction_value' style='width: 100px;'   style='width: 100px;'></td><td><div><button id='" + i + "' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
	$('#preclose_chart_creation_tbl tbody').append(row);
	$("#save_blk").css("display", "block");
}
function remove_row(curRow) {
	curRow.remove();
}
//Scheme Benefits
$("#isapply_benefit_by_chart").on("click", function () {    // show button based on the checkbox click//
	$(".answer").toggle(this.checked);
});
/*function createchart_table()
{
	var i = 1;
	var table_rows = $('#chart_creation_tbl tbody tr').length;
	var row = "<tr rowID='" + i + "'>"
				+"<td>From<input type='number' class='installment_from' name='installmentchart["+table_rows+"][installment_from]' value='1' style='width: 50px;'>To<input type='number' name='installmentchart["+table_rows+"][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td>"
				+"<td><select class='form-control'  name='installmentchart["+table_rows+"][interest_mode]' ><option value='1'>Amount</option><option value='2'>Discount on Wastage in purchase</option><option value='3'>Discount on Wastage & GST</option></td>"
				+"<td><input type='radio' name='installmentchart["+table_rows+"][interest_type]' class='interest_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentchart["+table_rows+"][interest_type]' class='interest_type' checked  value='0'  style='width: 50px;'>%</td>"
				+"<td><input type='number' name='installmentchart["+table_rows+"][interest_value]' value='10' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>"
				+"<td><div><button id='" + i + "' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>"
				+"</tr>";
	$('#chart_creation_tbl tbody').append(row);
	$("#save_blk").css("display","block");
}*/
$(document).on("click", '.delete', function () {  // remove row
	$(this).closest('tr').remove();
});  // remove row
//scheme closing benefits
$("input[name='sch[emp_incentive_closing]']").on('change', function () {
	if ($(this).is(':checked')) {
		$(this).val(1);
		$("input[name='sch[closing_incentive_based_on]']").prop('disabled', false);
		$('#proced_closing').prop('disabled', false);
	}
	else {
		$(this).val(0);
		$("input[name='sch[closing_incentive_based_on]']").prop('disabled', true);
		$('#proced_closing').prop('disabled', true);
	}
});
$("#proced_closing").on("click", function () {
	createchart_closing_table();
});
function createchart_closing_table() {
	//$('#chart_creation_tbl_closing').show();
	var j = 1;
	var table_rows_closing = $('#chart_creation_tbl_closing tbody tr').length;
	var row_closing = "<tr rowID='" + j + "'>"
		+ "<td>From<input type='number' class='incentive_from' name='installmentchart_closing[" + table_rows_closing + "][incentive_from]' value='1' style='width: 50px;' step='any'>To<input type='number' name='installmentchart_closing[" + table_rows_closing + "][incentive_to]' class='installment_to' value='1' step='any' style='width: 50px;'></td>"
		+ "<td><input type='radio' name='installmentchart_closing[" + table_rows_closing + "][type]' class='interest_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentchart_closing[" + table_rows_closing + "][type]' class='interest_type' checked  value='2'  style='width: 50px;'>%</td>"
		+ "<td><input type='number' step='any' name='installmentchart_closing[" + table_rows_closing + "][value]' value='10' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>"
		+ "<td><div><button id='" + j + "' class='delete btn btn-danger'   name='delete' type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>"
		+ "</tr>";
	$('#chart_creation_tbl_closing tbody').append(row_closing);
	$("#save_blk").css("display", "block");
}
$("#chart_creation_tbl_closing").on('click', '.delete', function () {
	$(this).closest('tr').remove();
});
/*function createchart_table(){
	  var i = 1;
   var table_rows = $('#chart_creation_tbl tbody tr').length;
   var row = "<tr rowID='" + i + "'>"+"<td>From<input type='number' name='installmentchart["+table_rows+"][installment_from]' class='installment_from' value='1' style='width: 50px;'>To<input type='number' name='installmentchart["+table_rows+"][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td><td><input type='radio' name='installmentchart["+table_rows+"][interest_type]' class='interest_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentchart["+table_rows+"][interest_type]' class='interest_type' checked  value='0'  style='width: 50px;'>%</td><td><input type='number' name='installmentchart["+table_rows+"][interest_value]' value='10' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td><td><div><button id='" + i + "' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
		 $('#chart_creation_tbl tbody').append(row);
		$("#save_blk").css("display","block");
}*/
function createChart(postData) {
	console.log(postData);
	$("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_scheme/sch_post/" + type,
		data: { 'installment_from': installment_from, 'installment_to': installment_to, 'interest_type': interest_type, 'interest_value': interest_value },
		type: "POST",
		dataType: "JSON",
		async: false,
		success: function (data) {
			console.log(data);
			//   location.reload(false);
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
// Pre-close settings benefit deduction based on scheme chart //HH
$("#proceds").on("click", function () {
	createpreclose_chart_table();
});
// start of agent benefit js 
$("#apply_agent_benefit").on("click", function () {    // show button based on the checkbox click// 
	$(".agentblock").toggle(this.checked);
	//$("#agent_credit").show();
});
$("#agent_add_row").on("click", function () {
	createagentbenefit_chart_table();
});
function createagentbenefit_chart_table() {
	var i = 1;
	var table_rows = $('#agent_chart_creation_tbl tbody tr').length;
	var row = "<tr rowID='" + i + "'>" + "<td>From<input type='number' name='agent_benefit_chart[" + table_rows + "][installment_from]' class='installment_from' value='1' style='width: 50px;'>To<input type='number' name='agent_benefit_chart[" + table_rows + "][installment_to]' class='installment_to' value='1'  style='width: 50px;'></td><td><input type='radio' name='agent_benefit_chart[" + table_rows + "][benefit_type]' class='benefit_type' value='1'  style='width: 50px;'>Amt<input type='radio' name='agent_benefit_chart[" + table_rows + "][benefit_type]' class='benefit_type' checked  value='0'  style='width: 50px;'>%</td><td><input type='number' name='agent_benefit_chart[" + table_rows + "][benefit_value]' value='10' class='form-control benefit_value' style='width: 100px;'   style='width: 100px;'></td><td><div><button id='" + i + "' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
	$('#agent_chart_creation_tbl tbody').append(row);
	$("#save_blk").css("display", "block");
}
function createagentbenefit_chart(post_Data) {
	console.log(post_Data);
	$("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_scheme/sch_post/" + type,
		data: { 'installment_from': installment_from, 'installment_to': installment_to, 'benefit_type': benefit_type, 'benefit_value': benefit_value },
		type: "POST",
		dataType: "JSON",
		async: false,
		success: function (data) {
			console.log(data);
			//   location.reload(false);
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
// end of agent benefit js 
// start of employee & agent incentive benefit js 
$("#emp_credit_incentive").on("click", function () {    // show button based on the checkbox click// 
	$(".emp_incentive_block").toggle(this.checked);
});
$("#agent_credit_incentive").on("click", function () {    // show button based on the checkbox click// 
	$(".agent_incentive_block").toggle(this.checked);
});
$("#add_emp_incentive_row").on("click", function () {
	var table_rows = $('#emp_incentive_chart_table tbody tr').length;
	addrow_incentive_chart_table('emp', table_rows);
});
$("#add_agent_incentive_row").on("click", function () {
	var table_rows = $('#agent_incentive_chart_table tbody tr').length;
	addrow_incentive_chart_table('age', table_rows);
});
function addrow_incentive_chart_table(type, table_rows) {
	var i = table_rows++;
	if (type == 'emp') {
		var credit_for_val = 1
		var credit_for_label = 'Employee';
		var credit_options = "<option value='0'>New scheme joining</option>" +
			"<option value='1'>customer intro scheme join</option>" +
			"<option value='2'>Payment based on day</option>";
		var credit_type = 'emp_credit_type';
		var credit_value = 'emp_credit_value';
	} else if (type == 'age') {
		var credit_for_val = 2
		var credit_for_label = 'Agent';
		var credit_type = 'age_credit_type';
		var credit_value = 'age_credit_value';
		var credit_options = "<option value='0'>New scheme joining</option>" +
			"<option value='3'>Payment based on date</option>";
	}
	var row = "<tr rowID='" + i + "'>" + "<td><input type='hidden' name='incentive_chart[" + type + "][" + table_rows + "][credit_to]' value='" + credit_for_val + "'><span><b>" + credit_for_label + "</b></span></td>" +
		"<td><select class='credit_for' name='incentive_chart[" + type + "][" + table_rows + "][credit_for]' onchange='load_credit_range(this.value," + credit_for_val + "," + i + ");'>" + credit_options + "</select></td>" +
		"<td><select class='" + credit_for_val + "_credit_range_" + i + "' name='incentive_chart[" + type + "][" + table_rows + "][credit_from_range]'></select></td>" +
		"<td><select class='" + credit_for_val + "_credit_range_" + i + "' name='incentive_chart[" + type + "][" + table_rows + "][credit_to_range]'></select></td>" +
		"<td><input type='radio' name='incentive_chart[" + type + "][" + table_rows + "][credit_type]' value='0'  style='width: 50px;'>Amt<input type='radio' name='incentive_chart[" + type + "][" + table_rows + "][credit_type]' checked  value='1'  style='width: 50px;'>%</td>" +
		"<td><input type='number' step='any' name='incentive_chart[" + type + "][" + table_rows + "][credit_value]' value='' class='form-control' style='width: 100px;'></td>" +
		"<td><div><button id='" + i + "' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
	if (type == 'emp') {
		$('#emp_incentive_chart_table tbody').append(row);
		load_credit_range(0, 1, i);
	} else if (type == 'age') {
		$('#agent_incentive_chart_table tbody').append(row);
		load_credit_range(0, 2, i);
	}
	$("#save_blk").css("display", "block");
}
function load_credit_range(sel, type, i) {
	$("." + type + "_credit_range_" + i + " option").remove();
	if (sel == 0 || sel == 1) {
		var ins = $('#total_installments').val();
		if (ins != '' || ins != 0) {
			for (j = 1; j <= ins; j++) {
				$('.' + type + '_credit_range_' + i + '').append(
					$("<option></option>")
						.attr("value", j)
						.text(j)
				);
			}
		} else {
			alert("Please fill installment to use this option...");
		}
	} else if (sel == 3) {
		for (j = 1; j <= 31; j++) {
			$('.' + type + '_credit_range_' + i + '').append(
				$("<option></option>")
					.attr("value", j)
					.text(j)
			);
		}
	} else if (sel == 2) {
		var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		for (j = 0; j <= 6; j++) {
			$('.' + type + '_credit_range_' + i + '').append(
				$("<option></option>")
					.attr("value", days[j])
					.text(days[j])
			);
		}
	}
}
// End of employee & agent incentive benefit js 
$("#apply_debit_on_preclose").on("click", function () {    // show button based on the checkbox click//
	$(".precloseblock").toggle(this.checked);
});
$("#interest_ins_block").hide();
if ($("#isInterest").is(":checked")) {
	$("#interest_ins_block").show();
}
toggleChartCommodityRequired();
$("#isInterest").on("click", function () {    // show & Hide checkbox based on the checkbox click//
	if ($(this).is(":checked")) {
		$(".precloseblock_open").hide();
		$("#interest_ins_block").show();
	} else {
		$(".precloseblock_open").show();
		$("#interest_ins_block").hide();
	}
	toggleChartCommodityRequired();
});
// flexible scheme installment settings
$("#add_sch").on("click", function () {
	create_schins_table();
});
function create_schins_table() {
	var i = 1;
	var table_rows = $('#scheme_setting_tbl tbody tr').length;
	var row = "<tr rowID='" + i + "'>" + "<td>From<input type='number' name='scheme_flexible[" + table_rows + "][ins_from]' class='ins_from' value='1' style='width: 50px;'>To<input type='number' name='scheme_flexible[" + table_rows + "][ins_to]' class='ins_to' value='1'  style='width: 50px;'></td><td><input type='number' name='scheme_flexible[" + table_rows + "][min_value]' value='' step='any' class='form-control min_value' style='width: 100px;'   style='width: 100px;'></td><td><input type='number' step='any' name='scheme_flexible[" + table_rows + "][max_value]' step='any' value='' class='form-control max_value' style='width: 100px;'   style='width: 100px;'></td><td><div><button id='" + i + "' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
	$('#scheme_setting_tbl tbody').append(row);
	$("#save_blk").css("display", "block");
}
function createpreclose_Chart(post_Data) {
	console.log(post_Data);
	$("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_scheme/sch_post/" + type,
		data: { 'installment_from': installment_from, 'installment_to': installment_to, 'deduction_type': deduction_type, 'deduction_value': deduction_value },
		type: "POST",
		dataType: "JSON",
		async: false,
		success: function (data) {
			console.log(data);
			//   location.reload(false);
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
/*var installment_to = (curRow.find('.installment_to').val() == '')  ? 0 : curRow.find('.installment_to').val();
			var deduction_type = (curRow.find('.deduction_type').val() == '')  ? 0 : curRow.find('.deduction_type').val();
			var deduction_value = (isNaN(curRow.find('.deduction_value').val()) || curRow.find('.deduction_value').val() == '')  ? 0 : curRow.find('.deduction_value').val();
		   //console.log(deduction_value);
			if(installment_from != 0 && installment_to != 0 && (deduction_type == 0 || deduction_type==1)&& deduction_value > 0){
				postData.push({"installment_from":installment_from,"installment_to":installment_to,"deduction_type":deduction_type,"deduction_value":deduction_value}); 
			}
		   console.log(postData);
			if(postData.length > 0 && postData.length){
			createChart(postData);
			  //console.log(createChart(postData));
		}*/
$("#discount").change(function () {
	if ($("input[name='sch[discount]']:checked").val() == 1) {
		$("input[name='sch[firstPayDisc_by]']").prop('disabled', false);
		$("#firstPayDisc_value").prop('disabled', false);
	} else {
		$("input[name='sch[firstPayDisc_by]']").prop('disabled', true);
		$("#firstPayDisc_value").prop('disabled', true);
	}
});
$("#all_pay_disc").change(function () {
	if ($("input[name='sch[all_pay_disc]']:checked").val() == 1) {
		$("input[name='sch[allpay_disc_by]']").prop('disabled', false);
		$("#allpay_disc_value").prop('disabled', false);
	} else {
		$("input[name='sch[allpay_disc_by]']").prop('disabled', true);
		$("#allpay_disc_value").prop('disabled', true);
	}
});
function get_classifications() {
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/scheme/get_classifications',
		dataType: 'json',
		success: function (data) {
			//console.log(data);
			$.each(data, function (key, data) {
				$('#classify').append(
					$('<option></option>')
						.attr('value', data.id)
						.text(data.name)
				);
				selectid = $('#classify_val').val();
				$('#classify').val((selectid != null ? selectid : 0));
			});
		}
	});
}
function get_branches() {
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/scheme/get_branches',
		dataType: 'json',
		success: function (data) {
			//	console.log(data);
			$.each(data, function (key, data) {
				$('#branches').append(
					$('<option></option>')
						.attr('value', data.id_branch)
						.text(data.name)
				);
				var ar = $('#sel_br').data('sel_br');
				console.log(data);
				console.log(ar);
				$('#branches').select2('val', ar);
				console.log($('#branches').val());
			});
		}
	});
}
//calculate tax
function calc_tax() {
	var selected = $("input[name='sch[tax_by]']:checked").val();
	var duration = parseFloat($('#total_installments').val()).toFixed(2);
	var amount = (parseFloat($('#amount').val()) * parseFloat(duration)).toFixed(2);
	var tax_val = parseFloat($("#tax_value").val()).toFixed(2);
	var total = 0;
	if (selected == 0) {
		total = (parseFloat(amount) * (parseFloat(tax_val) / 100)).toFixed(2);
	}
	else {
		total = parseFloat(tax_val).toFixed(2);
	}
	if (isNaN(total)) {
		total = 0;
	}
	return total;
}
//calculate interest
function calc_interest() {
	var selected = $("input[name='sch[interest_by]']:checked").val();
	var duration = parseFloat($('#total_installments').val()).toFixed(2);
	var amount = (parseFloat($('#amount').val()) * parseFloat(duration)).toFixed(2);
	var interest_val = parseFloat($("#interest_value").val()).toFixed(2);
	var total = 0;
	if (selected == 0) {
		total = (parseFloat(amount) * (parseFloat(interest_val) / 100)).toFixed(2);
	}
	else {
		total = parseFloat(interest_val).toFixed(2);
	}
	if (isNaN(total)) {
		total = 0;
	}
	return total;
}
function get_scheme_list() {
	my_Date = new Date();
	$("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/scheme/ajax_scheme_list?nocache=" + my_Date.getUTCSeconds(),
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			set_scheme_list(data);
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
function set_scheme_list(data) {
	var scheme = data.data;
	var access = data.access;
	$('#total_schemes').text(scheme.length);
	if (access.add == '0') {
		$('#add_scheme').attr('disabled', 'disabled');
	}
	var oTable = $('#scheme_list').DataTable();
	oTable.clear().draw();
	if (scheme != null && scheme.length > 0) {
		oTable = $('#scheme_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true,
			"bSort": true,
			"aaData": scheme,
			"order": [[0, 'desc']],
			"aoColumns": [{ "mDataProp": "id_scheme" },
			{ "mDataProp": "scheme_name" },
			{ "mDataProp": "code" },
			{
				"mDataProp": function (row, type, val, meta) {
					var type = (row.scheme_type == '0' ? "Amount" : (row.scheme_type == '1' ? "Weight" : row.scheme_type == '3' ? "FLXIBLE_AMOUNT" : "Amount to Weight"));
					return type;
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					var amount = ((row.scheme_type == '0' || row.scheme_type == '2') ? row.amount : "-");
					return amount;
				}
			},
			{ "mDataProp": "total_installments" },
			{
				"mDataProp": function (row, type, val, meta) {
					return row.allow_advance == '0' ? "No" : "Yes";
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					return row.advance_months != '' || row.advance_months != null ? row.advance_months : "-";
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					//active_url =base_url+"index.php/admin_scheme/customer_status/"+(row.active==1?0:1)+"/"+row.id_customer; 
					return "<i class='fa " + (row.active == 1 ? 'fa-check' : 'fa-remove') + "' style='color:" + (row.active == 1 ? 'green' : 'red') + "'></i></a>"
				}
			},
			{
				"mDataProp": function (row, type, val, meta) {
					id = row.id_scheme;
					edit_url = (access.edit == '1' ? base_url + 'index.php/scheme/edit/' + id : '#');
					delete_url = (access.delete == '1' ? base_url + 'index.php/scheme/delete/' + id : '#');
					delete_confirm = (access.delete == '1' ? '#confirm-delete' : '');
					action_content = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">' +
						'<li><a href="' + edit_url + '" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' +
						'<li><a href="#" class="btn-del" data-href="' + delete_url + '" data-toggle="modal" data-target="' + delete_confirm + '"  ><i class="fa fa-trash"></i> Delete</a></li></ul></div>';
					return action_content;
				}
			}]
		});
	}
}
//GG
$("#edit_sch_img").change(function (e) {
	e.preventDefault();
	//alert(asd);
	validate_Image(this);
});
function validate_Image() {
	console.log(arguments);
	if (arguments[0].id == 'edit_sch_img') {
		var preview = $('#edit_sch_img_preview');
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
//GG update
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
function getActiveMetalPurities() {
	my_Date = new Date();
	$("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/admin_scheme/getActivePuritiesByMetal?nocache=" + my_Date.getUTCSeconds(),
		data: { "id_metal": $("#metal").val() },
		dataType: "JSON",
		type: "POST",
		success: function (data) {
			$.each(data, function (key, purity) {
				$('#purity').append(
					$("<option></option>")
						.attr("value", purity.id_purity)
						.text(purity.purity)
				);
			});
			selectid = $('#purity_val').val();
			$('#purity').val(selectid);
			$("div.overlay").css("display", "none");
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
// 	allow general advance ---14-12-23 ---start --santhosh
$("#allow_general_advance").change(function () {
	updateRequiredState();
});
function updateRequiredState() {
	// Get the checkbox state
	var isChecked = $("#allow_general_advance").prop("checked");
	// Set the required attribute based on the checkbox state
	if (isChecked != true) {
		$("#adv_min_amt,#adv_max_amt,#adv_denomination").prop("required", isChecked);
	} else {
		$("#adv_min_amt,#adv_max_amt,#adv_denomination").prop("required", isChecked);
	}
}
// 	allow general advance ---14-12-23 ---end --santhosh
// Weight slabs scheme master 07-02-2024---start
function enableWeightslab(element) {
	console.log(element.id + 'id')
	console.log($('#is_lumpSum').prop("checked") + 'checked');
	console.log($('#pay_type').val() + 'value 4')
	console.log($('#pay_type').length);
	if (element.id == 'is_lumpSum' || element.id == 'opt_flexible' || element.id == 'pay_type') {
		if ($('#is_lumpSum').prop("checked") == true && $('#pay_type').val() == 4 && $('#pay_type').length > 0) {
			console.log('1')
			$('#joinTime_weight_slabs').prop('required', true)
		} else {
			console.log('2')
			$('#joinTime_weight_slabs').prop('required', false)
		}
	}
}
function get_weights_list() {
	my_Date = new Date();
	$("div.overlay").css("display", "block");
	$.ajax({
		url: base_url + "index.php/scheme/ajax_weight_list?nocache=" + my_Date.getUTCSeconds(),
		dataType: "JSON",
		type: "POST",
		data: { 'wgt_min': $('#min_weight').val(), 'wgt_max': $('#max_weight').val() },
		success: function (data) {
			console.log(data)
			if (ctrl_page[1] == 'add') {
				$('#joinTime_weight_slabs').select2({
					placeholder: "select Weight "
				})
			} else {
				$('#joinTime_weight_slabs').select2();
			}
			if ($('#is_lumpSum').prop('checked')) {
				$('#joinTime_weight_slabs').prop('required', true)
			}
			// $('#joinTime_weight_slabs').prop('selectedIndex', -1);
			$('#joinTime_weight_slabs').empty();
			$.each(data, function (key, data) {
				$('#joinTime_weight_slabs').append(
					$('<option></option>')
						.attr('value', data.id_weight)
						.text(data.weight)
				);
			});
			var selectedValues = $('#selected_wgt').val().split(',');
			$('#joinTime_weight_slabs option').each(function () {
				var optionValue = $(this).val();
				// Check if the option value is in the selectedValues array
				if ($.inArray(optionValue, selectedValues) !== -1) {
					// If it is, set the selected attribute of the option
					$(this).prop('selected', true);
				}
			});
			// Trigger change event to reflect the selected options visually if needed
			$('#joinTime_weight_slabs').trigger('change');
			// 							 var select2Dropdown = $('#joinTime_weight_slabs');
			//                             // Clear existing selections
			//                             select2Dropdown.val(null);
			//                             // Loop through the selected values and trigger the select2 'select' event for each value
			//                             selectedValues.forEach(function(value) {
			//     var option = new Option(value, value, true, true);
			//     select2Dropdown.append(option).trigger('change');
			// });
			// 			  if(selectedValues.length !=1) {	
			// 				$('#joinTime_weight_slabs').removeAttr('data-placeholder');
			// 			 // Loop through the selected values and set the corresponding options as selected
			// 			 $.each(selectedValues, function (key, value) {
			// 				 $('#joinTime_weight_slabs option[value="' + value + '"]').prop('selected', true);
			// 			 });
			// 			}
			console.log($('#joinTime_weight_slabs').val()); // ["2", "3", "4"]
		},
		error: function (error) {
			$("div.overlay").css("display", "none");
		}
	});
}
$('#min_weight,#max_weight').on('input', function () {
	console.log($('#min_weight').val())
	console.log($('#max_weight').val())
	if ($('#min_weight').val() != 0 && $('#min_weight').val() != '' && $('#max_weight').val() != '' && $('#max_weight').val() != 0) {
		get_weights_list();
	}
});
//Weight slabs scheme master 07-02-2024---End
// function for top up scheme 
function enableTopupScheme(element) {
	if (element.checked) {
		// console.log("Checked");
		$('#topUpSchemeDiv').show();
		$('#topUpSchemeDiv input:first').focus();
	} else {
		$('#topUpSchemeDiv').hide();
		$('#topUpMinvalue').val('');
		$('#topUpMaxvalue').val('');
		$('#topupDenomination').val('');
		$("input[name='sch[topup_booking_type]']").prop("checked", false);
		$("#topup_booking_type_amt").prop("checked", true);
		$("#topupStoreSlab").val(1).trigger("change");
	}
}
function createChartRowForTopUpScheme() {
	var rowCount, row;
	rowCount = $('#topUpSchemeChartTable tbody tr').length;
	var row = "<tr id='topUpChartRow_" + rowCount + "'>";
	row += "<td><select name='topUpSchemeChart[" + rowCount + "][payable_by]' class='locked'><option value='1' selected>Days</option></select></td>";
	row += "<td>From&nbsp;&nbsp;<input type='number' name='topUpSchemeChart[" + rowCount + "][range_from]' class='range_from' value='1' style='width: 50px;'>&nbsp;To&nbsp;<input type='number' name='topUpSchemeChart[" + rowCount + "][range_to]' class='range_to' value='1'  style='width: 50px;'></td>";
	row += "<td><select name='topUpSchemeChart[" + rowCount + "][payable_type]'> <option value='0'>Amount</option> <option value='1'>Percentage</option></select></td>";
	row += "<td><input type='text' name='topUpSchemeChart[" + rowCount + "][payable_value]'></td>";
	row += "<td><button id='topUpSchemeRowDeleteBtn_" + rowCount + "' class='delete btn btn-danger' name='delete' type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
	$('#topUpSchemeChartTable tbody').append(row);
	$("select.locked").on("mousedown", function (e) {
		e.preventDefault(); // prevent dropdown from opening
	});
}
$(document).on("mousedown keydown keypress", "select.locked", function (e) {
	e.preventDefault(); // prevent dropdown from opening
});
