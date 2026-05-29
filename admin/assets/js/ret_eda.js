var path =  url_params();

var ctrl_page 		= path.route.split('/');

$(document).ready(function() {

	var path =  url_params();

	$('#status').bootstrapSwitch();

    $(window).scroll(function() {    // this will work when your window scrolled.

		var height = $(window).scrollTop();  //getting the scrolling height of window

		if(height  > 300) {

			$(".stickyBlk").css({"position": "fixed"});

		} else{

			$(".stickyBlk").css({"position": "static"});

		}

	}); 

	switch(ctrl_page[1]) {

	 	case 'eda':

			switch(ctrl_page[2]) {

				case 'list':				 	

					get_eda_list();

					$('#branch_select').on('change',function(){

						get_eda_list();

					});

				break;

		}
		break;

		case 'sales_report':
			switch(ctrl_page[2]) {
				case 'list':
				    get_ActiveMetals();
					get_sales_list();
				break;
		    }
		break;

		case 'purchase_report':
        	switch(ctrl_page[2])
        	{
        		case 'list':
        			get_ActiveMetals();
        			get_eda_purcahse_list();
        		break;
        	}
        break;

        case 'partly_sold_report':
        	switch(ctrl_page[2])
        	{
        		case 'list':
        			get_ActiveMetals();
        			get_eda_partlysold();
        		break;
        	}
        break;
	}

	

});

$(document).on("click", ".approve_eda", function() {

	let estimation_id 	= $(this).closest('tr').find(".estimation_id").val();

	let estimate_final_amt 	= $(this).closest('tr').find(".estimate_final_amt").val();

	console.log("estimation_id",estimation_id);

	console.log("estimate_final_amt",estimate_final_amt);

	if(parseFloat(estimation_id) > 0) {

		$("#esti_id").val(estimation_id);

		$("#estimate_final_amt").val(estimate_final_amt);

		$("#confirm-approve").modal('show');

	}

});

$(document).on("click", ".reject_eda", function() {

	let estimation_id 	= $(this).closest('tr').find(".estimation_id").val();

	console.log("estimation_id",estimation_id);

	if(parseFloat(estimation_id) > 0) {

		$("#esti_reject_id").val(estimation_id);

		$("#confirm-reject").modal('show');

	}

});

$(document).on("click", ".btn-approve", function() {

	let esti_id = $("#esti_id").val();

	let estimate_final_amt = $.trim($("#estimate_final_amt").val()) == '' ? 0 : $("#estimate_final_amt").val();

	if(parseFloat(esti_id) > 0) {

		$("#confirm-approve").modal('hide');

		my_Date = new Date();

		$("div.overlay").css("display", "block"); 

		$.ajax({

			url:base_url+"index.php/admin_ret_eda/eda/update?nocache=" + my_Date.getUTCSeconds(),

			dataType:"JSON",

			type:"POST",

			data:{'esti_id': esti_id, 'estimate_final_amt': estimate_final_amt, 'type' : 1},

			success:function(data){

				get_eda_list();

				$("div.overlay").css("display", "none"); 

				if(data.status == true) {

					$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+'Estimation approved successfully...'});

				}

				else

				{

				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

				}

			},

			error:function(error)  {

				$("div.overlay").css("display", "none"); 

			}	 

		});

	}

});

$(document).on("click", ".btn-reject", function() {

	let esti_id = $("#esti_reject_id").val();

	if(parseFloat(esti_id) > 0) {

		$("#confirm-reject").modal('hide');

		my_Date = new Date();

		$("div.overlay").css("display", "block"); 

		$.ajax({

			url:base_url+"index.php/admin_ret_eda/eda/update?nocache=" + my_Date.getUTCSeconds(),

			dataType:"JSON",

			type:"POST",

			data:{'esti_id': esti_id, 'type' : 2},

			success:function(data){

				get_eda_list();

				$("div.overlay").css("display", "none"); 

				if(data.status == true) {

					$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+'Estimation rejected successfully...'});

				}else

				{

				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

				}

			},

			error:function(error)  {

				$("div.overlay").css("display", "none"); 

			}	 

		});

	}

});

function get_eda_list() {

	my_Date = new Date();

	$("div.overlay").css("display", "block"); 

	$.ajax({

		url:base_url+"index.php/admin_ret_eda/eda/ajax?nocache=" + my_Date.getUTCSeconds(),

		dataType:"JSON",

		type:"POST",

		data:{'id_branch':(($('#branch_select').val()!='' && $('#branch_select').val()!='' && $('#branch_select').val()!=undefined) ? $('#branch_select').val():$('#branch_filter').val())},

		success:function(data){

			set_eda_list(data);

			$("div.overlay").css("display", "none"); 

		},

		error:function(error)  {

			$("div.overlay").css("display", "none"); 

		}	 

	});

}

/**

 * 

 * Updated By : Vivek, Updated On : 07-09-22

 * Added excel and print buttons. Given discount field

 */

function set_eda_list(data)	{

   $("div.overlay").css("display", "none"); 

   var estimation = data.list;

   var access = data.access;

   var oTable = $('#eda_list').DataTable();

   $("#total_estimation").text(estimation.length);

    if(access.add == '0')

	 {

		$('#add_estimation').attr('disabled','disabled');

	 }

	 oTable.clear().draw();

   	 if (estimation!= null && estimation.length > 0)

	 {

	 	oTable = $('#eda_list').dataTable({

			"bDestroy": true,

			"bInfo": true,

			"bFilter": true, 

			"bSort": true,

			"order": [[ 0, "desc" ]],

			"dom": 'lBfrtip',

			"buttons": [

				{

				  extend: 'print',

				  footer: true,

				  title: "EDA List",

				},

				{

				   extend:'excel',

				   footer: true,

				   title: "EDA List",

				 }

			],

			"aaData": estimation,

			"aoColumns": [{ "mDataProp": "esti_no" },

						{ "mDataProp": "estimation_datetime" },		

						{ "mDataProp": function ( row, type, val, meta ){

							return row.firstname;

						},

						},

						{ "mDataProp": "mobile" },

						

						{ "mDataProp": function ( row, type, val, meta ){

						    if($('#id_branch').val()==0 || $('#id_branch').val()=='')

						    {

						         return row.product_name;

						    }else{

						        return '-';

						    }

						},

						},

						{ "mDataProp": function ( row, type, val, meta ) {

						   	return "<span class='estimate_total_amt'>"+row.total_cost+"</span>";

					   	}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							if(row.is_eda_approved == 0) {

								action_content	=	'<input type="number" class="estimate_final_amt form-control" />';

							} else {

								action_content	=	row.estimate_final_amt;

								

							}

						   	return action_content;

					   	}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							let discount_amt = "-";

							if(row.is_eda_approved == 1) {

								let final_amt = isNaN(row.estimate_final_amt) || row.estimate_final_amt == "" || row.estimate_final_amt == null ? 0 : row.estimate_final_amt;

								discount_amt = parseFloat(parseFloat(row.total_cost) - parseFloat(final_amt)).toFixed(2);

							}

							return "<span class='estimate_discount_amt'>"+discount_amt+"</span>";

					   	}

						},

						{ "mDataProp": function ( row, type, val, meta ) {

							if(row.is_eda_approved == 0) {

								id	= row.estimation_id

								let approve_url		=	(access.add=='1' ? base_url+'index.php/admin_ret_eda/eda/approve/'+id : '#' );

								let reject_url		=	(access.add=='1' ? base_url+'index.php/admin_ret_eda/eda/reject/'+id : '#' );

								let approve_confirm	= 	(access.add=='1' ?'#confirm-approve':'');

								action_content 	= 	'<a href="#" class="btn btn-success approve_eda" data-href='+approve_url+' data-toggle="modal" >Approve</a><input type="hidden" class="estimation_id" value="'+id+'" /> &nbsp; <a href="#" class="btn btn-danger reject_eda" data-href='+reject_url+' data-toggle="modal" >Reject</a>';

							} else {

								action_content = 'Approved';

							}

							return action_content;

						}

					}] 

		});	

	}

}

$(document).on("keyup",".estimate_final_amt", function() {

	let curRow = $(this).closest("tr");

	let estimate_total_amt = curRow.find('.estimate_total_amt').html();

	let estimate_final_amt = $.trim(curRow.find('.estimate_final_amt').val());

	estimate_total_amt = isNaN(estimate_total_amt) || estimate_total_amt == "" ? 0 : estimate_total_amt;

	estimate_final_amt = isNaN(estimate_final_amt) || estimate_final_amt == "" ? 0 : estimate_final_amt;

	let estimate_discount_amt = (parseFloat(estimate_total_amt) - parseFloat(estimate_final_amt)).toFixed(2);

	curRow.find('.estimate_discount_amt').html(estimate_discount_amt);

});

function get_ActiveMetals()
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_catalog/ret_product/active_metal',
		dataType:'json',
		success:function(data){
			var id =  $("#metal").val();
			$("#metal").append(
				$("<option></option>")
				.attr("value", 0)
				.text('All' )
				);
		    $.each(data,function (key, item) {
			   		$('#metal').append(
						$("<option></option>")
						  .attr("value", item.id_metal)
						  .text(item.metal)
					);
			});
			$("#metal").select2(
			{
				placeholder:"Select Metal",
				allowClear: true
			});
		}
	});
}
$('#eda_sales_search').on('click',function(){
    get_sales_list();
});

function get_sales_list()
{
    $("div.overlay").css("display", "block");

    var branch_name=($('#branch_name').val()!='' && $('#branch_name').val()!=undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());
	var company_name=$('#company_name').val();
	var title="<b><span style='font-size:15pt;margin-left:30%;'>"+company_name+"</span></b><b><span style='font-size:12pt;'></span></b></br>"+"<span style=font-size:13pt;margin-left:20%;>&nbsp;&nbsp;EDA Item Details "+($("#branch_select").val()!='' ? "Cost Center:"+branch_name+" ":'')+"</span>";

	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_ret_eda/sales_report/ajax?nocache=" + my_Date.getUTCSeconds(),
		     data: ( {'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#branch_select").val()),'id_metal':$("#metal").val()}),
			 dataType:"JSON",
			 type:"POST",
			 success:function(data){

			    $("#eda_sales_list > tbody > tr").remove();
	 		    $('#eda_sales_list').dataTable().fnClearTable();
    		    $('#eda_sales_list').dataTable().fnDestroy();

			     var list = data.list;
			 	$("div.overlay").css("display", "none");
			 	if(list.length > 0)
			 	{
			 	    var trHtml = '';
			 	    $.each(list,function(key,items){
			 	        var sales_details = items.sales_details;
			 	        var purchase_details = items.purchase_details;
			 	        var e_url = base_url+'index.php/admin_ret_estimation/generate_invoice/'+items.estimation_id;



			 	        if(sales_details.length > 0)
			 	        {
			 	            var total_pcs = 0;
			 	            var total_gwt = 0;
			 	            var total_lwt = 0;
			 	            var total_nwt = 0;
			 	            var total_amt = 0;
			 	            $.each(sales_details,function(k,sval){
			 	                if(k==0)
			 	                {
			 	                    var est_url = '<a href='+e_url+' target="_blank">'+items.esti_no+'</a>';
			 	                    var cus_name = items.cus_name;
			 	                    var mobile  =   items.mobile;
			 	                }else
			 	                {
			 	                    var est_url='';
			 	                    var cus_name='';
			 	                    var mobile='';
			 	                }

			 	                total_pcs+=parseFloat(sval.piece);
			 	                total_gwt+=parseFloat(sval.gross_wt);
			 	                total_lwt+=parseFloat(sval.less_wt);
			 	                total_nwt+=parseFloat(sval.net_wt);
			 	                total_amt+=parseFloat(sval.item_cost);
			 	                trHtml+='<tr>'
			 	                +'<td>'+est_url+'</td>'
			 	                +'<td>'+cus_name+'</td>'
			 	                +'<td>'+mobile+'</td>'
			 	                +'<td>'+sval.tag_code+'</td>'
			 	                +'<td>'+sval.metal_name+'</td>'
			 	                +'<td>'+sval.category_name+'</td>'
			 	                +'<td>'+sval.product_name+'</td>'
			 	                +'<td>'+sval.design_name+'</td>'
			 	                +'<td>'+sval.sub_design_name+'</td>'
			 	                +'<td>'+sval.piece+'</td>'
			 	                +'<td>'+sval.gross_wt+'</td>'
			 	                +'<td>'+sval.less_wt+'</td>'
			 	                +'<td>'+sval.net_wt+'</td>'
			 	                +'<td>'+sval.discount+'</td>'
			 	                +'<td>'+sval.item_cost+'</td>'
								 +'<td></td>'
			 	                +'</tr>';
			 	            });
			 	            trHtml+='<tr style="font-weight:bold;" >'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td style="text-align:center;">'+(purchase_details.length==0 ?'GRAND TOTAL' :'SUB TOTAL')+'</td>'
			 	                +'<td>'+parseFloat(total_pcs).toFixed(0)+'</td>'
			 	                +'<td>'+parseFloat(total_gwt).toFixed(3)+'</td>'
			 	                +'<td>'+parseFloat(total_lwt).toFixed(3)+'</td>'
			 	                +'<td>'+parseFloat(total_nwt).toFixed(3)+'</td>'
			 	                +'<td></td>'
			 	                +'<td>'+parseFloat(total_amt).toFixed(3)+'</td>'
								 +'<td></td>'
			 	                +'</tr>';

			 	        }

			 	        if(purchase_details.length > 0)
			 	        {

			 	            var total_pcs = 0;
			 	            var total_gwt = 0;
			 	            var total_lwt = 0;
			 	            var total_nwt = 0;
			 	            var total_amt = 0;
			 	            $.each(purchase_details,function(k,sval){

			 	                if(sales_details.length==0 && k==0)
    		 	                {
    		 	                    var est_url = '<a href='+e_url+' target="_blank">'+items.esti_no+'</a>';
    		 	                    var cus_name = items.cus_name;
    		 	                    var mobile  =   items.mobile;
    		 	                }else
    		 	                {
    		 	                    var est_url='';
    		 	                    var cus_name='';
    		 	                    var mobile='';
    		 	                }

			 	                total_pcs+=parseFloat(sval.piece);
			 	                total_gwt+=parseFloat(sval.gross_wt);
			 	                total_lwt+=parseFloat(sval.less_wt);
			 	                total_nwt+=parseFloat(sval.net_wt);
			 	                total_amt+=parseFloat(sval.amount);
			 	                trHtml+='<tr>'
			 	                +'<td>'+est_url+'</td>'
			 	                +'<td>'+cus_name+'</td>'
			 	                +'<td>'+mobile+'</td>'
			 	                +'<td></td>'
			 	                +'<td>'+sval.metal_name+'</td>'
			 	                +'<td>'+sval.old_metal_cat+'</td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td>'+sval.piece+'</td>'
			 	                +'<td>'+sval.gross_wt+'</td>'
			 	                +'<td>'+sval.less_wt+'</td>'
			 	                +'<td>'+sval.net_wt+'</td>'
			 	                +'<td></td>'
			 	                +'<td>'+sval.amount+'</td>'
								 +'<td></td>'
			 	                +'</tr>';
			 	            });
			 	            trHtml+='<tr style="font-weight:bold;" >'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td style="text-align:center;">'+(sales_details.length==0 ?'GRAND TOTAL' :'SUB TOTAL')+'</td>'
			 	                +'<td>'+parseFloat(total_pcs).toFixed(0)+'</td>'
			 	                +'<td>'+parseFloat(total_gwt).toFixed(3)+'</td>'
			 	                +'<td>'+parseFloat(total_lwt).toFixed(3)+'</td>'
			 	                +'<td>'+parseFloat(total_nwt).toFixed(3)+'</td>'
			 	                +'<td></td>'
			 	                +'<td>'+parseFloat(total_amt).toFixed(2)+'</td>'
								 +'<td></td>'
			 	                +'</tr>';

			 	        }

			 	        trHtml+='<tr style="font-weight:bold;" >'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td style="text-align:center;">GRAND TOTAL</td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td></td>'
			 	                +'<td>'+parseFloat(items.total_cost).toFixed(2)+'</td>'
								 +'<td>'+parseFloat(items.mc_avg).toFixed(2)+'</td>'
			 	                +'</tr>';


			 	    });
			 	    $('#eda_sales_list > tbody').html(trHtml);

			 	    if ( ! $.fn.DataTable.isDataTable( '#eda_sales_list' ) ) {
						oTable = $('#eda_sales_list').dataTable({
						"bSort": false,
						"bDestroy": true,
						"bInfo": true,
						"scrollX":'100%',
						"paging": false,
						"dom": 'Bfrtip',
						"bAutoWidth": false,
						"responsive": true,
						"buttons": [
						{
							extend: 'print',
							footer: true,
							title: '',
							messageTop: title,
							customize: function ( win ) {
							    $(win.document.body).find('table')
                                .addClass('compact');

								$(win.document.body).find( 'table' )
									.addClass('compact')
									.css('font-size','10px')
									.css('font-family','sans-serif');

							},
						    exportOptions: {
        	                    columns: ':visible',
                          		stripHtml: false
        	                }
						},
						{
							extend:'excel',
							footer: true,
						    title: '',
						}
						],

						"columnDefs": [
                            {

                                targets: [1,2,3,4,5,6,7,8,9],
                                className: 'dt-body-right'
                            }
                        ],


						});
					}

			 	}
			  },
			  error:function(error)
			  {
				 $("div.overlay").css("display", "none");
			  }
	      });
}
//reports

$('#pur_eda_search').on('click',function()
{
	get_eda_purcahse_list();

});








function get_eda_purcahse_list()
{

	$("div.overlay").css("display", "block");

    var branch_name=($('#branch_name').val()!='' && $('#branch_name').val()!=undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());
	var company_name=$('#company_name').val();
	var title="<b><span style='font-size:15pt;margin-left:30%;'>"+company_name+"</span></b><b><span style='font-size:12pt;'></span></b></br>"+"<span style=font-size:13pt;margin-left:20%;>&nbsp;&nbsp;EDA PURCHASE Details "+($("#branch_select").val()!='' ? "Cost Center:"+branch_name+" ":'')+"</span>";

	my_Date = new Date();

	$.ajax({
		url:base_url+ "index.php/admin_ret_eda/purchase_report/ajax?nocache=" + my_Date.getUTCSeconds(),
		data: ( {'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#branch_select").val()),'id_metal':$("#metal").val()}),
		dataType:"JSON",
		type:"POST",
		success:function(data){

			$("div.overlay").css("display", "none");
		 		var list = data.list;

				 $("#eda_pur_list > tbody > tr").remove();
				 $('#eda_pur_list').dataTable().fnClearTable();
				 $('#eda_pur_list').dataTable().fnDestroy();

				if (list!= null )
				{

					var trHTML = '';

					tot_pur_pcs=0;

					tot_pur_gwt=0;

					tot_pur_lwt=0;

					tot_pur_nwt=0;

					tot_pur_va=0;

					tot_pur_rate=0;

					tot_pur_amt=0;


					$.each(list, function (i, metal)
					{
						var pur_pcs=0;

						var pur_gwt=0;

						var pur_lwt=0;

						var pur_nwt=0;

						var pur_va=0;

						var pur_rate=0;

						var pur_amt=0;

						trHTML += '<tr style="font-weight:bold;">' +
							'<td style="text-align: left; text-transform:uppercase;">'+i+'</td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
						'</tr>';

						$.each(metal, function (key, item)
						{

							pur_pcs+=parseFloat(item.piece);

							pur_gwt+=parseFloat(item.gross_wt);

							pur_lwt+=parseFloat(item.less_wt);

							pur_nwt+=parseFloat(item.net_wt);

							pur_va+=parseFloat(item.wastage_wt);

							pur_rate+=parseFloat(item.rate_per_gram);

							pur_amt+=parseFloat(item.pur_amt);

							var url = base_url+'index.php/admin_ret_estimation/generate_invoice/'+item.estimation_id;

							trHTML += '<tr>' +
								'<td>' + item.branch + '</td>' +
								'<td>' + item.est_date + '</td>' +
								'<td><a href='+url+' target="_blank">'+item.esti_no+'</a></td>'+
								'<td>' + item.old_metal_cat + '</td>' +
								'<td>' + item.piece + '</td>' +
								'<td>' + item.gross_wt + '</td>' +
								'<td>' + item.less_wt + '</td>' +
								'<td>' + item.net_wt + '</td>' +
								'<td>' + item.wastage_wt + '</td>' +
								'<td>' + item.rate_per_gram + '</td>' +
								'<td>' + item.pur_amt + '</td>' +
							'</tr>';

						});

						trHTML += '<tr style="font-weight:bold;">' +

							'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td><b>Sub Total</td>'

							+'<td><b>'+pur_pcs+'</td>'

							+'<td><b>'+parseFloat(pur_gwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(pur_lwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(pur_nwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(pur_va).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(pur_rate).toFixed(2)+'</td>'

							+'<td><b>'+parseFloat(pur_amt).toFixed(2)+'</td>'

						'</tr>';

						tot_pur_pcs+=parseFloat(pur_pcs);

						tot_pur_gwt+=parseFloat(pur_gwt);

						tot_pur_lwt+=parseFloat(pur_lwt);

						tot_pur_nwt+=parseFloat(pur_nwt);

						tot_pur_va+=parseFloat(pur_va);

						tot_pur_rate+=parseFloat(pur_rate);

						tot_pur_amt+=parseFloat(pur_amt);


					});

					trHTML += '<tr style="font-weight:bold;">' +

							'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td><b>Grand Total</td>'

							+'<td><b>'+tot_pur_pcs+'</td>'

							+'<td><b>'+parseFloat(tot_pur_gwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(tot_pur_lwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(tot_pur_nwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(tot_pur_va).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(tot_pur_rate).toFixed(2)+'</td>'

							+'<td><b>'+parseFloat(tot_pur_amt).toFixed(2)+'</td>'

						'</tr>';


					$('#eda_pur_list > tbody').html(trHTML);

				}


				if ( ! $.fn.DataTable.isDataTable( '#eda_pur_list' ) ) {
				   oTable = $('#eda_pur_list').dataTable({
				   "bSort": false,
				   "bDestroy": true,
				   "bInfo": true,
				   "scrollX":'100%',
				   "paging": false,
				   "dom": 'Bfrtip',
				   "bAutoWidth": false,
				   "responsive": true,
				   "buttons": [
				   {
					   extend: 'print',
					   footer: true,
					   title: '',
					   messageTop: title,
					   customize: function ( win ) {
						   $(win.document.body).find('table')
						   .addClass('compact');

						   $(win.document.body).find( 'table' )
							   .addClass('compact')
							   .css('font-size','10px')
							   .css('font-family','sans-serif');

					   },
					   exportOptions: {
						   columns: ':visible',
							 stripHtml: false
					   }
				   },
				   {
					   extend:'excel',
					   footer: true,
					   title: '',
				   }
				   ],

				   "columnDefs": [
					   {

						   targets: [1,2,3,4,5,6,7,8,9],
						   className: 'dt-body-right'
					   }
				   ],


				   });
			   }

		 	},
		 error:function(error)
		 {
			$("div.overlay").css("display", "none");
		 }
	});
}

$('#partly_eda_search').on('click',function()
{
	get_eda_partlysold();
});







function get_eda_partlysold()
{


	$("div.overlay").css("display", "block");

	var branch_name=($('#branch_name').val()!='' && $('#branch_name').val()!=undefined ? $('#branch_name').val() : $("#branch_select option:selected").text());
	var company_name=$('#company_name').val();
	var title="<b><span style='font-size:15pt;margin-left:30%;'>"+company_name+"</span></b><b><span style='font-size:12pt;'></span></b></br>"+"<span style=font-size:13pt;margin-left:20%;>&nbsp;&nbsp;EDA PARTLY SOLD Details "+($("#branch_select").val()!='' ? "Cost Center:"+branch_name+" ":'')+"</span>";

	my_Date = new Date();

	$.ajax({
			url:base_url+ "index.php/admin_ret_eda/partly_sold_report/ajax?nocache=" + my_Date.getUTCSeconds(),
			data: ( {'id_branch':($('#branch_filter').val()!='' && $('#branch_filter').val()!=undefined ? $('#branch_filter').val(): $("#branch_select").val()),'id_metal':$("#metal").val()}),
			dataType:"JSON",
			type:"POST",
			success:function(data)
			{

				$("div.overlay").css("display", "none");
		 		var list = data.list;

				$("#eda_partly_list > tbody > tr").remove();
				$('#eda_partly_list').dataTable().fnClearTable();
				$('#eda_partly_list').dataTable().fnDestroy();

				if(list!=null)
				{
					var trHTML = '';

					tot_act_pcs=0;

					tot_act_gwt=0;

					tot_act_nwt=0;

					tot_sold_pcs=0;

					tot_sold_gwt=0;

					tot_sold_nwt=0;

					$.each(list,function(i,metal)
					{
						var act_pcs=0;

						var act_gwt=0;

						var act_nwt=0;

						var sold_pcs=0;

						var sold_gwt=0;

						var sold_nwt=0;

						trHTML += '<tr style="font-weight:bold;">' +
							'<td style="text-align: left; text-transform:uppercase;">'+i+'</td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
							'<td></td>'+
						'</tr>';

						$.each(metal,function(key,item)
						{

							act_pcs+=parseFloat(item.act_piece);

							act_gwt+=parseFloat(item.act_grs_wt);

							act_nwt+=parseFloat(item.act_net_wt);

							sold_pcs+=parseFloat(item.sold_piece);

							sold_gwt+=parseFloat(item.sold_gwt);

							sold_nwt+=parseFloat(item.sold_nwt);


							var url = base_url+'index.php/admin_ret_estimation/generate_invoice/'+item.estimation_id;

							trHTML += '<tr>' +
								'<td>' + item.branch + '</td>' +
								'<td>' + item.est_date + '</td>' +
								'<td><a href='+url+' target="_blank">'+item.esti_no+'</a></td>'+
								'<td>' + item.tag_code + '</td>' +
								'<td>' + item.product_name + '</td>' +
								'<td>' + item.design_name + '</td>' +
								'<td>' + item.act_piece + '</td>' +
								'<td>' + item.act_grs_wt + '</td>' +
								'<td>' + item.act_net_wt + '</td>' +
								'<td>' + item.sold_piece + '</td>' +
								'<td>' + item.sold_gwt + '</td>' +
								'<td>' + item.sold_nwt + '</td>' +
							'</tr>';

						});

						trHTML += '<tr style="font-weight:bold;">' +

							'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td><b>Sub Total</td>'

							+'<td><b>'+act_pcs+'</td>'

							+'<td><b>'+parseFloat(act_gwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(act_nwt).toFixed(3)+'</td>'

							+'<td><b>'+sold_pcs+'</td>'

							+'<td><b>'+parseFloat(sold_gwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(sold_nwt).toFixed(3)+'</td>'

						'</tr>';

						tot_act_pcs+=parseFloat(act_pcs);

						tot_act_gwt+=parseFloat(act_gwt);

						tot_act_nwt+=parseFloat(act_nwt);

						tot_sold_pcs+=parseFloat(sold_pcs);

						tot_sold_gwt+=parseFloat(sold_gwt);

						tot_sold_nwt+=parseFloat(sold_nwt);

					});

					trHTML += '<tr style="font-weight:bold;">' +

							'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td></td>'

							+'<td><b>Grand Total</b></td>'

							+'<td><b>'+tot_act_pcs+'</td>'

							+'<td><b>'+parseFloat(tot_act_gwt).toFixed(3)+'</td>'

							+'<td><b>'+parseFloat(tot_act_nwt).toFixed(3)+'</td>'

							+'<td><b>'+tot_sold_pcs+'</td>'

							+'<td><b>'+parseFloat(tot_sold_gwt).toFixed(3)+'</b></td>'

							+'<td><b>'+parseFloat(tot_sold_nwt).toFixed(2)+'</b></td>'


						'</tr>';

					$('#eda_partly_list > tbody').html(trHTML);

				}
				if ( ! $.fn.DataTable.isDataTable( '#eda_partly_list' ) ) {
					oTable = $('#eda_partly_list').dataTable({
					"bSort": false,
					"bDestroy": true,
					"bInfo": true,
					"scrollX":'100%',
					"paging": false,
					"dom": 'Bfrtip',
					"bAutoWidth": false,
					"responsive": true,
					"buttons": [
					{
						extend: 'print',
						footer: true,
						title: '',
						messageTop: title,
						customize: function ( win ) {
							$(win.document.body).find('table')
							.addClass('compact');

							$(win.document.body).find( 'table' )
								.addClass('compact')
								.css('font-size','10px')
								.css('font-family','sans-serif');

						},
						exportOptions: {
							columns: ':visible',
							  stripHtml: false
						}
					},
					{
						extend:'excel',
						footer: true,
						title: '',
					}
					],


					});
				}



			},
			error:function(error)
			{
				$("div.overlay").css("display", "none");
			}
	});

}

// ADDED FOR REPORT

