$(document).ready(function() {
	$("#pull-out").css("display","none");
	$('.date').datepicker({
		maxDate : 'now',
		format : 'dd-mm-yyyy'
    });
	$("#btnsubmit").on('click',function(event) {
		event.preventDefault();
		if($("#payment_amount").val() > 0)
		{
			$("#save_payment").submit();
		}
		else
		{
			alert("Please fill out required fields...");
		}
	});
	var maximum_weight = 0;
	get_rate();
	scheme_account_load('','');
	$("#scheme_acccounts").on('change',function() {
		scheme_account_load(this.value,'');
	});
	$("#id_customer").on('change',function() {
		scheme_account_load('',this.value);
	});
	$("#id_scheme_account").on('change',function() {
		schemedata_post();
	});
	$("#weight").on('keyup',function(){
		calculate_total();
	});
	//for popover in 
	 $('[data-toggle="popover"]').popover({ html : true});
	//payment_list select all
		$('#select_all').click(function(event) {
		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
      event.stopPropagation();
    });
   /* $("tbody tr td input[type='checkbox']").click(function(event) {
		$("#select_all").prop('checked', $(this).prop('checked'));
      event.stopPropagation();
    });
	*/
	$('a.payscheme').click(function(e){
		 //e.preventDefault();
		 var id=$(this).data('id');		
		get_account(id);
	});
	
	$("input[name='pay_status']:radio").change(function(){
		if($("input[name='pay_id[]']:checked").val())
		{
			$('#pay_status_form').submit();
		}
	
   });
   
 	$('#payments').dataTable({
          "bPaginate": true,
          "bLengthChange": true,
          "bFilter": true,
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": true,
		  "order": [[ 1, "desc" ]],
		  "columnDefs": [
            {
                "targets": [ 1 ],
                "visible": false,
                "searchable": false
            }]
		  
        });
	
	$('#pay_amount').click(function(e){
		
		  var info=[];
		    info['id_scheme_account']=$('#acc_id').val();
		  	info['date_payment']=$('#pay_date').val();
		  	info['payment']=$('#sch_amount').val();
		  	info['payment_mode']=$('#pay_mode').val();
		  	info['remark']=$('#pay_remark').val();
		  	
		    
		 
		 $.ajax({
			type	:'POST',
			url		:base_url+"index.php/payment/scheme_account/",
			 data: { 
			          
        			 'info': info 
    				},
			dataType:"json",
			success	: function(data){
				console.log(data);
				 
				 
				 
			}
		});
	});

});

function get_rate()
{
	my_Date = new Date();
	var baseURL = base_url.replace('admin/','');
	$.ajax({
		type: "GET",
		url: baseURL+"api/rate.txt"+"?nocache=" + my_Date.getUTCSeconds(),
		dataType: "json",
		cache: false,
		success: function(data) {
		   var currentRate = data.goldrate_22ct;
 		   $("#metal_rate").val(currentRate);
		}
	});
}
function scheme_account_load(scheme_id,cus_id)
{
	$("#pull-out").css("display","none");
	$("#pull-out i").removeClass("fa-minus").addClass("fa-plus");
	$("#collapsed_box").addClass("collapsed-box");
	$("#scheme-details").css("display","none");
	$("#eligiblity").html("");
	$("#amount").html("");
	$("#totalPaidAmount").html("");
	$("#totalWeight").html("");
	$("#eligible_qty").html("");
	$("#scheme_name").html("");
	$("#no_install").html("");
	$("#paid_install").html("");
	$("#start_date").html("");
	
	$('#id_scheme_account').find("option:gt(0)").remove();
	$('#customerName').html("");
	if(scheme_id == '' && cus_id == '')
	{
		$('#scheme_acccounts').find("option:gt(0)").remove();
		$('#id_customer').find("option:gt(0)").remove();
		$.each(schemes,function(index,value) {
			$('#scheme_acccounts').append($('<option></option>').val(value.id_scheme_account).html(value.id_scheme_account));    
		});

		var customers = [];
        $.each(schemes,function(i,val) {
			if ($.inArray(val.id_customer, customers) == -1) {
				$('#id_customer').append($('<option></option>').val(val.id_customer).html(val.cus_name));
				customers.push(val.id_customer);
			}  
		});
		$("#selected_scheme").css("display","none");
	}
	else if(scheme_id != '' && cus_id == '')
	{
		$('#id_customer').val("");
		$('#id_scheme_account').append($('<option selected="selected"></option>').val(scheme_id).html(scheme_id));
		$.each(schemes,function(index,value) {
			if(value.id_scheme_account == scheme_id)
			{
				$('#customerName').html(value.cus_name);
				return false;
			}
		});
		schemedata_post();
		$("#selected_scheme").css("display","block");
	}
	else if(scheme_id == '' && cus_id != '')
	{
		var i = 0;
		$('#scheme_acccounts').val("");
		$.each(schemes,function(index,value) {
			if(value.id_customer == cus_id)
			{
				i = i+1;
				$('#customerName').html(value.cus_name);
				$('#id_scheme_account').append($('<option></option>').val(value.id_scheme_account).html(value.id_scheme_account));
			}
		});
		if(i == 1)
		{
			var selected = $('#id_scheme_account').find("option:gt(0)").val();
			$('#id_scheme_account').val(selected);
			schemedata_post();
		}
		$("#selected_scheme").css("display","block");
	}
}
function schemedata_post() {
		$("#pull-out").css("display","none");
		$("#pull-out i").removeClass("fa-minus").addClass("fa-plus");
		$("#collapsed_box").addClass("collapsed-box");
		$("#scheme-details").css("display","none");
		$("#eligiblity").html("");
		$("#amount").html("");
		$("#totalPaidAmount").html("");
		$("#totalWeight").html("");
		$("#eligible_qty").html("");
		$("#scheme_name").html("");
		$("#scheme_type").html("");
		$("#no_install").html("");
		$("#paid_install").html("");
		$("#start_date").html("");
		$("#payment_amount").val(0);
		$("#weight").val(0);
		maximum_weight = 0;
		 var schID = $("#id_scheme_account").val();
	  	 $.each(schemes,function(index,value) {
		 if(schID == value.id_scheme_account)
		 {
		 	$("#pull-out").css("display","block");
			$("#pull-out i").removeClass("fa-plus").addClass("fa-minus");
			$("#collapsed_box").removeClass("collapsed-box");
			$("#scheme-details").css("display","block");
			$("#pull-out").css("display","block");
				if(value.scheme_type == 'Amount')
				{
					$(".metalqty_container").css("display","none");
					$(".amount_container").css("display","block");
					if((parseFloat(value.no_install) - parseFloat(value.totalpay_install)) <= 0 || value.curpay_install > 0)
					{
						$("#amount").html(0);
						$("#payment_amount").val(0);
						$("#eligiblity").html("<span class='label label-danger'>No</span>");
					}
					else
					{
						$("#amount").html(parseFloat(value.amount).toFixed(2));
						$("#payment_amount").val(parseFloat(value.amount).toFixed(2));
						$("#eligiblity").html("<span class='label label-success'>Yes</span>");
					}
					$("#totalPaidAmount").html(parseFloat(value.curpay_amount).toFixed(2));
					
				}
				else if(value.scheme_type == 'Weight')
				{
					$(".metalqty_container").css("display","block");
					$(".amount_container").css("display","none");
					maximum_weight = value.max_weight;
					if((parseFloat(value.no_install) - parseFloat(value.totalpay_install)) <= 0 || parseFloat(value.curpay_weight) >= parseFloat(value.max_weight) ||  parseFloat(value.chances_used) >= parseFloat(value.max_chance))
						$("#eligiblity").html("<span class='label label-danger'>No</span>");
					else
						$("#eligiblity").html("<span class='label label-success'>Yes</span>");
					$("#totalWeight").html(parseFloat(value.curpay_weight).toFixed(2));
					$("#totalPaidAmount").html(parseFloat(value.curpay_amount).toFixed(2));
					$("#eligible_qty").html(parseFloat(parseFloat(value.max_weight)-parseFloat(value.curpay_weight)).toFixed(2));
				}
				
				$("#scheme_name").html(value.scheme_name);
				$("#scheme_type").html(value.scheme_type);
				$("#scheme_acc_number").html(value.scheme_acc_number);
				$("#no_install").html(value.no_install);
				$("#paid_install").html(value.totalpay_install);
				$("#start_date").html(value.start_date);
				return false;
			}
		});
}
function calculate_total()
{
	$("#payment_amount").val(0);
	if ($("#scheme_type").html() == 'Weight') {		
		var eligibleQty = isNaN($("#eligible_qty").html()) || $("#eligible_qty").html() == '' ? 0 :$("#eligible_qty").html();
		var weight =  isNaN($("#weight").val()) || $("#weight").val() == '' ? 0 :$("#weight").val();
		console.log(weight);
		if(parseFloat(weight) <= parseFloat(eligibleQty))
		{
			totalAmt = parseFloat($("#weight").val()) * parseFloat($("#metal_rate").val());
			$("#payment_amount").val(parseFloat(isNaN(totalAmt)?0.00:totalAmt).toFixed(2));
		}
		else
		{
			$("#payment_amount").val(0);
			$("#weight").val(0);
			alert('Maximum weight per month : ' +parseFloat(maximum_weight)+' gm');
		}
	}
}
function get_account(id)
{
	$.ajax({
		type	:'GET',
		url		:base_url+"index.php/account/payment_detail/"+id,
		dataType:"json",
		success	: function(data){
			//console.log(data);
			 	/*$('#pay-confirm').modal({
    backdrop: 'static',
    keyboard: false
});*/
			 	$('#acc_id').val(data.id_scheme_account);
			 	$('#acc_name').val(data.account_name);
			 	$('#sch_amount').val(data.amount);
			 	$('#pay_mode').val(1);
			 	$('#pay_date').datepicker({               
	                 "setDate": new Date(),
	       			 "autoclose": true
                });
			 
		}
	});
}