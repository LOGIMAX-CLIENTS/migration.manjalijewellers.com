var path =  url_params();



var ctrl_page = path.route.split('/');



let curr_symbol = "&#x20B9; ";



var break_even_config ={};



const colorCodes = [

    '#3366cc', '#109618', '#990099', '#ff9900', '#dc3912',

	'#673AB7', '#F44336', '#009688', '#FF9800', '#3F51B5',

	'#FFEB3B', '#795548', '#9C27B0', '#FF5722', '#607D8B',

	'#00BCD4', '#8BC34A', '#FFEB3B', '#FFC107', '#CDDC39',

    "#FF0000", "#00FF00", "#0000FF", "#FFFF00", "#00FFFF",

    "#FF00FF", "#800000", "#008000", "#000080", "#808000",

    "#800080", "#008080", "#808080", "#C0C0C0", "#FF9999",

    "#99FF99", "#9999FF", "#FFFF99", "#99FFFF", "#FF99FF",

    "#FF6666", "#66FF66", "#6666FF", "#FFFF66", "#66FFFF",

    "#FF66FF", "#FF3333", "#33FF33", "#3333FF", "#FFFF33",

    "#33FFFF", "#FF33FF", "#FF0000", "#00FF00", "#0000FF",

    "#FFFF00", "#00FFFF", "#FF00FF", "#800000", "#008000",

    "#000080", "#808000", "#800080", "#008080", "#808080",

    "#C0C0C0", "#FF9999", "#99FF99", "#9999FF", "#FFFF99",

    "#99FFFF", "#FF99FF", "#FF6666", "#66FF66", "#6666FF",

    "#FFFF66", "#66FFFF", "#FF66FF", "#FF3333", "#33FF33",

    "#3333FF", "#FFFF33", "#33FFFF", "#FF33FF", "#FF0000",

    "#00FF00", "#0000FF", "#FFFF00", "#00FFFF", "#FF00FF",

    "#800000", "#008000", "#000080", "#808000", "#800080",

    "#008080", "#808080", "#C0C0C0", "#FF9999", "#99FF99",

    "#9999FF", "#FFFF99", "#99FFFF", "#FF99FF", "#FF6666",

    "#66FF66", "#6666FF", "#FFFF66", "#66FFFF", "#FF66FF",

    "#FF3333", "#33FF33", "#3333FF", "#FFFF33", "#33FFFF",

    "#FF33FF"

     ];



$(document).ready(function()



{



	switch(ctrl_page[1])



	{







		case  'dashboard':







				//get_lot_data();



				//get_tagging_data();



				get_branch_order();



				get_average_bill_value();



                // get_branchname();



				var date = new Date();



			    var from_date=(date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());



			    var to_date=(date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());



			    $('#payment_list1').text(from_date);



				$('#payment_list2').text(to_date);



			    $("body").on("click","#tab_livecockpit", function(){



					//retail_dashboard_details(from_date,to_date);



				    get_live_cockpit_dashboard_details();



				});



				get_ActiveMetals_dashboard();


				check_LedgerBalanceAlert();





				$("body").on("click","#tab_order_management", function(){



				    get_order_management_details();



				});



				$("body").on("click","#tab_sales", function(){



					let from_date =  $('#payment_list1').text();



					let to_date =  $('#payment_list2').text();



					sales_dashboard_data(from_date,to_date);



				});







				$("body").on("click","#tab_sale_gchart", function(){



					// Load the Visualization API and the piechart package.



				// 	google.charts.load('current', {'packages':['corechart', 'bar']});







				// 	// Set a callback to run when the Google Visualization API is loaded.



				// 	google.charts.setOnLoadCallback(get_salesDetails);



				//     //get_salesDetails();



				set_sale_dashboard();







				});







				$("body").on("click","#tab_stock_gchart", function(){



					// Load the Visualization API and the piechart package.



					google.charts.load('current', {'packages':['corechart', 'bar']});







					// Set a callback to run when the Google Visualization API is loaded.



					google.charts.setOnLoadCallback(get_stockDetails);



				});







				$("body").on("click","#tab_contract_pricing", function(){



					get_contract_pricing();



				});











			     sales_details(from_date,to_date);



			    /*window.setInterval(function(){



				 if($('.tab-pane.active').attr('id') == "live_cockpit") {



					let from_date =  $('#payment_list1').text();



					let to_date =  $('#payment_list2').text();



					//retail_dashboard_details(from_date,to_date);



					get_live_cockpit_dashboard_details();



				 }



				 if($('.tab-pane.active').attr('id') == "sales") {



					let from_date =  $('#payment_list1').text();



					let to_date =  $('#payment_list2').text();



					sales_dashboard_data(from_date,to_date);



				 }







				  if($('.tab-pane.active').attr('id') == "tab_sale_gchart")



				 {



				     let from_date =  $('#payment_list1').text();



				     let to_date   =  $('#payment_list2').text();



				     // Load the Visualization API and the piechart package.



					google.charts.load('current', {'packages':['corechart', 'bar']});







					// Set a callback to run when the Google Visualization API is loaded.



					google.charts.setOnLoadCallback(get_salesDetails);



				 }







				 if($('.tab-pane.active').attr('id') == "tab_stock_gchart")



				 {



				     let from_date =  $('#payment_list1').text();



				     let to_date   =  $('#payment_list2').text();



				     // Load the Visualization API and the piechart package.



					google.charts.load('current', {'packages':['corechart', 'bar']});







					// Set a callback to run when the Google Visualization API is loaded.



					google.charts.setOnLoadCallback(get_stockDetails);



				 }



				 sales_details(from_date,to_date);



				},60*1000*5);*/



		break;



	 	case 'get_estimation':



	 			var date = new Date();



				var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1);



				var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();



				var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());



			    var id_filter=$('#id_filter').val();



			    set_estimation_table(from_date,to_date,id_filter);



			    $('#estimation1').text(from_date);



			    $('#estimation2').text(to_date);



	 	break;



	}







	// -------------



  // - PIE CHART -



  // -------------



if($('#pieChart').get(0) !== undefined){



  // Get context with jQuery - using jQuery's .get() method.



  var pieChartCanvas = $('#pieChart').get(0).getContext('2d');



  var pieChart       = new Chart(pieChartCanvas);



  var PieData        = [



    {



      value    : 5,



      color    : '#f56954',



      highlight: '#f56954',



      label    : 'Repair'



    },



    /*{



      value    : 500,



      color    : '#00a65a',



      highlight: '#00a65a',



      label    : 'IE'



    },



    {



      value    : 400,



      color    : '#f39c12',



      highlight: '#f39c12',



      label    : 'FireFox'



    },*/



    {



      value    : 10,



      color    : '#00c0ef',



      highlight: '#00c0ef',



      label    : 'Custom'



    },



    /*{



      value    : 300,



      color    : '#3c8dbc',



      highlight: '#3c8dbc',



      label    : 'Opera'



    },*/



    {



      value    : 12,



      color    : '#f39c12',



      highlight: '#f39c12',



      label    : 'Catalog'



    }



  ];



  var pieOptions     = {



    // Boolean - Whether we should show a stroke on each segment



    segmentShowStroke    : true,



    // String - The colour of each segment stroke



    segmentStrokeColor   : '#fff',



    // Number - The width of each segment stroke



    segmentStrokeWidth   : 1,



    // Number - The percentage of the chart that we cut out of the middle



    percentageInnerCutout: 50, // This is 0 for Pie charts



    // Number - Amount of animation steps



    animationSteps       : 100,



    // String - Animation easing effect



    animationEasing      : 'easeOutBounce',



    // Boolean - Whether we animate the rotation of the Doughnut



    animateRotate        : true,



    // Boolean - Whether we animate scaling the Doughnut from the centre



    animateScale         : false,



    // Boolean - whether to make the chart responsive to window resizing



    responsive           : true,



    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container



    maintainAspectRatio  : false,



    // String - A legend template



    legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',



    // String - A tooltip template



    tooltipTemplate      : '<%=value %> <%=label%> Order'



  };



  // Create pie or douhnut chart



  // You can switch between pie and douhnut using the method below.



  pieChart.Doughnut(PieData, pieOptions);



  // -----------------



  // - END PIE CHART -



  // -----------------



}



	$('#payment_list1').text(moment().format('YYYY-MM-DD'));



	$('#payment_list2').text(moment().format('YYYY-MM-DD'));



    $('#payment-dt-btn').daterangepicker(



        {



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



          get_lot_data(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));



		  get_tagging_data(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));



		  get_branch_order(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));



		  retail_dashboard_details(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));







		  get_order_management_details(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));











		  if($('.tab-pane.active').attr('id') == "sale_gchart") {



		    // Load the Visualization API and the piechart package.



// 			google.charts.load('current', {'packages':['corechart', 'bar']});







// 			// Set a callback to run when the Google Visualization API is loaded.



// 			google.charts.setOnLoadCallback(get_salesDetails);



		 }







	  $('#payment_list1').text(start.format('YYYY-MM-DD'));



	  $('#payment_list2').text(end.format('YYYY-MM-DD'));



	  if($('.tab-pane.active').attr('id') == "live_cockpit") {



	  	get_live_cockpit_dashboard_details();



	  } else if($('.tab-pane.active').attr('id') == "sales") {



			let from_date =  $('#payment_list1').text();



			let to_date   =  $('#payment_list2').text();



			sales_dashboard_data(from_date,to_date);



	  }



      }



    );







	$('#estimation_date').daterangepicker(



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



	function (start, end)



	{



		$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));



		var id_filter=$('#id_filter').val();



		set_estimation_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_filter)



		$('#estimation1').text(start.format('YYYY-MM-DD'));



		$('#estimation2').text(end.format('YYYY-MM-DD'));



	}



	);



$("#filter_type").select2({



	placeholder:"Select type",



	allowClear: true



});







});



$('#filter_type').on('change',function(){



	if(this.value!='')



	{



		var from_date=$('#estimation1').text();



		var to_date  =$('#estimation2').text();



		var id_branch=$('#id_branch').val();



		$('#id_filter').val(this.value);



		set_estimation_table(from_date,to_date,this.value,id_branch);



	}



	else



	{



		$('#id_filter').val('');



	}



});



$('#branch_select').on('change',function(){



	if(this.value!='')



	{



		var from_date=$('#estimation1').text();



		var to_date  =$('#estimation2').text();



		var id_filter  =$('#id_filter').val();



		$('#id_branch').val(this.value);



		//set_estimation_table(from_date,to_date,id_filter,this.value);







    	if($('.tab-pane.active').attr('id') == "live_cockpit") {



			let from_date =  $('#payment_list1').text();



			let to_date =  $('#payment_list2').text();



			//retail_dashboard_details(from_date,to_date);







			get_live_cockpit_dashboard_details();



		 }















		 if($('.tab-pane.active').attr('id') == "sale_gchart") {



			branch = this.value;



			$('.branch_filter').each(function () {



					$(this).select2("val",branch);



			});

		 }







		    if($('.tab-pane.active').attr('id') == "stock_and_branch_transfer")



            {



            	let from_date =  $('#payment_list1').text();



            	let to_date   =  $('#payment_list2').text();



            	branch_transfer_details_dashboard_data(from_date,to_date);

            	sales_transfer_intransit_dashboard_data();

            }



			if($('.tab-pane.active').attr('id') == "gross_profit")



            {



            	if($('#metal_select').val()!='' && $('#metal_select').val()!=null)

				{



					get_gross_profit_report();



				}else

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Metal ..'});

				}



            }



	}



	else



	{



		$('#id_branch').val('');



	}



});



function retail_dashboard_details(from_date,to_date)



{



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_dashboard/get_retail_dashboard_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date},



			 type:"POST",



			 cache:false,



			 success:function(data){



				var estimation 	    = data.estimation;



				$('#live_estimation').text(estimation.estimation);



		 	 },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function set_estimation_table(from_date,to_date,filter_type,id_branch)



{



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_dashboard/get_estimation_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'type':filter_type,'id_branch':id_branch},



			 type:"POST",



			 cache:false,



			 success:function(data){



				var estimation 	    = data;



			 var oTable = $('#estimation_list').DataTable();



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



						"aaData"  : estimation,



						"aoColumns": [	{ "mDataProp": "estimation_id" },



										{ "mDataProp": "cus_name" },



										{ "mDataProp": "item_type" },



										{ "mDataProp": "sales_wt" },



										{ "mDataProp": "sales_amt" },



										{ "mDataProp": "pur_wt" },



										{ "mDataProp": "item_cost" },



										{ "mDataProp": "chit_amt" },



										{ "mDataProp": "gift_voucher_amt" },



										{ "mDataProp": "discount" },



										{ "mDataProp": "total_cost" },



										],



										"footerCallback": function ( row, data, start, end, display ) {



										var api = this.api(), data;



										// Remove the formatting to get integer data for summation



										var intVal = function ( i ) {



										return typeof i === 'string' ?



										i.replace(/[\$,]/g, '')*1 :



										typeof i === 'number' ?



										i : 0;



										};



										// Total over all pages



										/*total = api



										.column( 10 )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );*/



										// Total over this page



										sales_wt = api



										.column( 3, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										sales_amt = api



										.column( 4, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										pur_wt = api



										.column( 5, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										pur_amt = api



										.column( 6, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										chit_amt = api



										.column( 7, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										gift_amt = api



										.column( 8, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										dis_amt = api



										.column( 9, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										net_amt = api



										.column( 10, { page: 'current'} )



										.data()



										.reduce( function (a, b) {



										return intVal(a) + intVal(b);



										}, 0 );



										// Update footer



										$( api.column( 3 ).footer() ).html(



										'INR '+parseFloat(sales_wt).toFixed(2)



										);



										$( api.column( 4 ).footer() ).html(



										'INR '+parseFloat(sales_amt).toFixed(2)



										);



										$( api.column( 5 ).footer() ).html(



										'INR '+parseFloat(pur_wt).toFixed(2)



										);



										$( api.column( 6 ).footer() ).html(



										'INR '+parseFloat(pur_amt).toFixed(2)



										);



										$( api.column( 7 ).footer() ).html(



										'INR '+parseFloat(chit_amt).toFixed(2)



										);



										$( api.column( 8 ).footer() ).html(



										'INR '+parseFloat(gift_amt).toFixed(2)



										);



										$( api.column( 9 ).footer() ).html(



										'INR '+parseFloat(dis_amt).toFixed(2)



										);



										$( api.column( 10 ).footer() ).html(



										//'$'+pageTotal +' ( $'+ total +')'



										'INR '+parseFloat(net_amt).toFixed(2)



										);



										}



					});



				}



		  },



		  error:function(error)



		  {



			 $("div.overlay").css("display", "none");



		  }



	});







}



function get_lot_data(from_date="",to_date="")



{



	my_Date = new Date();



	$.ajax({



	  	  url:base_url+ "index.php/admin_ret_dashboard/ajax_lot_data?nocache=" + my_Date.getUTCSeconds(),



    	 data:{'from_date':from_date,'to_date':to_date},



    	 dataType:"JSON",



    	 type:"POST",



    	 success:function(data){



		    console.log(data);



    	 	set_lot_data(data,from_date,to_date);



    	  },



    	  error:function(error)



    	  {



    	  $("div.overlay").css("display", "none");



    	  }



	  });



}



function set_lot_data(data,from_date,to_date)



{



	$('#lot-data tbody').remove();



	$('#grs_wt').text(data.gross_wt);



	$('#net_wt').text(data.net_wt);



    $.each(data['lot'], function (index, element)



	{



		        id_branch = element.id_branch;







    			$('#lot-data').append(



                  $('<tbody><tr>')



				  .append($('<td style="text-align: centre;padding: 5px;">').append(element.branch_name))



                  .append($('<td style="text-align: centre;padding: 5px;">').append(element.lots))



                  .append($('<td style="text-align: centre;padding: 5px;">').append(element.net_weight))



				  .append($('<td style="text-align: centre;padding: 5px;">').append(element.grs_wt))







                );







		 });



}



function get_tagging_data(from_date="",to_date="")



{



	my_Date = new Date();



	$.ajax({



	  	  url:base_url+ "index.php/admin_ret_dashboard/ajax_tag_data?nocache=" + my_Date.getUTCSeconds(),



    	 data:{'from_date':from_date,'to_date':to_date},



    	 dataType:"JSON",



    	 type:"POST",



    	 success:function(data){



		    console.log(data);



    	 	set_tagging_data(data,from_date,to_date);



    	  },



    	  error:function(error)



    	  {



    	  $("div.overlay").css("display", "none");



    	  }



	  });



}



function set_tagging_data(data,from_date,to_date)



{



	$('#tag-data tbody').remove();



	$('#gt').text(data.grs_wt);



	$('#nt').text(data.nt_wt);



    $.each(data['tag'], function (index, element)



	{







    			$('#tag-data').append(



                  $('<tbody><tr>')



				  .append($('<td style="text-align: centre;padding: 5px;">').append(element.branch_name))



                  .append($('<td style="text-align: centre;padding: 5px;">').append(element.tags))



                  .append($('<td style="text-align: centre;padding: 5px;">').append(element.nt))



				  .append($('<td style="text-align: centre;padding: 5px;">').append(element.gt))







                );







		 });



}



function get_live_cockpit_dashboard_details()



{



        let from_date =  $('#payment_list1').text();



		let to_date =  $('#payment_list2').text();



        get_EstimationStatus(from_date,to_date);


		get_deposit_data(from_date, to_date);



		get_BillingStatus(from_date,to_date);



		get_GreentagSalesDetails(from_date,to_date);



		get_metal_purchase_status(from_date,to_date);



		get_CreditSalesDetails(from_date,to_date);



		get_GiftVoucherDetails(from_date,to_date);



		get_BillClassficationDetails(from_date,to_date);



		get_VitrualTagStatus(from_date,to_date);



		get_SalesreturnDetails(from_date,to_date);



		get_BranchTransferDetails(from_date,to_date);



		get_lot_tag_details(from_date,to_date);



		get_OrderDetails(from_date,to_date);



		get_StockDetails(from_date,to_date);



		get_silver_StockDetails(from_date,to_date);



		get_ReorderDetails(from_date,to_date);



		get_KarigarOrderDetails();



		get_CustomerOrderDetails(from_date,to_date);



		get_MetalStockDetails();



		get_RecentBillDetails(from_date,to_date);



		get_cash_abstract_details(from_date,to_date);



		getEstimationDetails(from_date,to_date);



		get_CustomerDetails(from_date,to_date);



		get_stock_details(from_date,to_date);



		get_approval_type();



}



function get_order_management_details()



{



    let from_date =  $('#payment_list1').text();



	let to_date =  $('#payment_list2').text();



	get_customer_order_details(from_date,to_date);

	get_pendingorderDetails(from_date,to_date);

	get_wiporderDetails(from_date,to_date);

	get_dreadyorderDetails(from_date,to_date);

	get_deliveredorderDetails(from_date,to_date);

	get_karigarreminderDetails(from_date,to_date);

	get_karigaroverdueDetails(from_date,to_date);



}



function get_EstimationStatus(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_EstimationStatus?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



				if(data != null &&  data.dash_estmation.created !== undefined){



				var estimate_create_url = base_url+'index.php/admin_ret_reports/dashboard_estimation/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



				var estimate_convert_url = base_url+'index.php/admin_ret_reports/dashboard_estimation/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



			        $("#cp_estimation_created").html('<a href='+estimate_create_url+' target="_blank">'+data.dash_estmation.created+'</a>');



				    $("#cp_estimation_converted").html('<a href='+estimate_convert_url+' target="_blank">'+data.dash_estmation.sold+'</a>');



				}



				    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



/*function get_BillingStatus(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_BillingStatus?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){







				    var dashboard_goldlist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



				    var dashboard_silverlist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



				    var dashboard_mrplist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/3/' + $('#id_branch').val();



			            $("#cp_sales_bills").html('<a href='+dashboard_goldlist+' target="_blank">' + data.dash_billing.gold_wt+ " g" + '</a>');







				        $("#cp_sales_amount").html('<a href='+dashboard_silverlist+' target="_blank">' + data.silver_wt.silver_wt+ " g" + '</a>');







					    $("#cp_sales_amount_mrp").html('<a href='+dashboard_mrplist +' target="_blank">' + curr_symbol + data.mrp.mrp + '</a>');







				    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}*/



function get_BillingStatus(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_BillingStatus?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



			sales_html=' <tr style="border-bottom: 15px solid transparent;" ><th style=" text-align:left;"  >Metal</th><th style=" text-align:right;"> Pcs</th><th style=" text-align:right;">Weight</th><th style=" text-align:right;">Amount</th> <th style="width:4%"></th></tr>';



				var dashboard_mrplist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/mrp/' + $('#id_branch').val();



                var dashboard_dialist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/daimond/'+ $('#id_branch').val();



			if(data.dash_billing){











			$.each(data.dash_billing, function (index, element)  {



				if(element.amt !=0){



					var dashboard_goldlist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/'+element.id_metal+'/' + $('#id_branch').val();



					sales_html +=  '<tr style="border-bottom: 15px solid transparent">'+



					'<td style=" text-align:left;"><b>'+element.metal_code+'  <span class="badge bg-green" id="cp_dia_count" style="font-size:10px">'+element.count+'</span></b></td>'+



					'<td style=" text-align:right;" ><b>'+'<a href='+dashboard_goldlist +' target="_blank">'+element.piece+ "" + '</a>'+'</b></td>'+



					'<td style=" text-align:right;"><b>'+'<a href='+dashboard_goldlist+' target="_blank">' + element.wt+ " g" + '</a>'+'</b></td>'+



					'<td style=" text-align:right;"><b>'+'<a href='+dashboard_goldlist +' target="_blank">' + curr_symbol +money_format_india(element.amt) + '</b></a>'+'</td>'+



					'<td></td>'+



				'</tr> ';



			}



					});



				}



				if(data.diamond.stone_amt != 0){



					sales_html +=  '<tr style="border-bottom: 15px solid transparent" >'+



					'<td style=" text-align:left;"><b>'+data.diamond.stone_name+'  <span class="badge bg-green" id="cp_dia_count" style="font-size:10px">'+data.diamond.count+'</span></b></th>'+



					'<td style=" text-align:right;"><b>'+'<a href='+dashboard_dialist +' target="_blank">'+data.diamond.stone_pieces+ "" + '</a>'+' </b></td>'+



					'<td style=" text-align:right;"><b>'+'<a href='+dashboard_dialist +' target="_blank">' + data.diamond.stone_wt+ " CT" + '</a>'+' </b></td>'+



					'<td style=" text-align:right; "><b>'+'<a href='+dashboard_dialist +' target="_blank">' + curr_symbol + money_format_india(data.diamond.stone_amt) + '</a>'+'</b></td>'+



					'<td></td>'+



				'</tr> ';



				}



				if(data.mrp.mrp != 0 ){



				sales_html +=  '<tr style="border-bottom: 15px solid transparent">'+



					'<td style=" text-align:left;"><b> MRP  <span class="badge bg-green" id="cp_dia_count" style="font-size:10px">'+data.mrp.mrp_count+'</span></b></td>'+



					'<td style=" text-align:right;"><b>'+'<a href='+dashboard_mrplist +' target="_blank">'+data.mrp.mrp_piece+ "" + '</a>'+'</b></td>'+



					'<td style=" text-align:right;"><b>'+'<a href='+dashboard_mrplist+' target="_blank">' + data.mrp.mrp_wt+ " g" + '</a>'+'</b></td>'+



					'<td style=" text-align:right;"><b>'+'<a href='+dashboard_mrplist +' target="_blank">' + curr_symbol + money_format_india(data.mrp.mrp) + '</a>'+'</b></td>'+



					'<td></td>'+



				'</tr> ';







				}



				$('#metal_sales').html(sales_html);



			    //    if(data != null &&  data.dash_billing.gold_wt !== undefined){



			    //     /*$("#cp_sales_bills").html(curr_symbol+data.dash_billing.bills);



				//     $("#cp_sales_amount").html(curr_symbol+data.dash_billing.billamount);*/



				//     var dashboard_goldlist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



				//     var dashboard_silverlist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



				//     var dashboard_mrplist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/3/' + $('#id_branch').val();



                //     		    var dashboard_dialist = base_url+'index.php/admin_ret_reports/dashboard_sales/'+from_date+'/'+to_date+'/4/'+ $('#id_branch').val();



			    //         $("#cp_gold_wt").html('<a href='+dashboard_goldlist+' target="_blank">' + data.dash_billing.gold_wt+ " g" + '</a>');



				// 		$("#cp_gold_amt").html('<a href='+dashboard_goldlist +' target="_blank">' + curr_symbol + data.dash_billing.gold_amt + '</a>');







				// 		$("#cp_gold_pcs").html('<a href='+dashboard_goldlist +' target="_blank">'+data.dash_billing.gold_piece+ " Pcs" + '</a>');







				// 		$("#cp_gold_count").html(data.dash_billing.gold_count);















				// 		$("#cp_silver_wt").html('<a href='+dashboard_silverlist+' target="_blank">' + data.silver_wt.silver_wt+ " g" + '</a>');







				// 		$("#cp_silver_amt").html('<a href='+dashboard_silverlist +' target="_blank">' + curr_symbol + data.silver_wt.silver_amt + '</a>');







				// 		$("#cp_silver_pcs").html('<a href='+dashboard_silverlist +' target="_blank">'+data.silver_wt.silver_piece+ " Pcs" + '</a>');







				// 		$("#cp_silver_count").html(data.silver_wt.silver_count);



				// 		$("#cp_dia_wt").html('<a href='+dashboard_dialist +' target="_blank">' + data.diamond.stone_wt+ " CT" + '</a>');



				// 		$("#cp_dia_amt").html('<a href='+dashboard_dialist +' target="_blank">' + curr_symbol + data.diamond.stone_amt + '</a>');



				// 		$("#cp_dia_pcs").html('<a href='+dashboard_dialist +' target="_blank">'+data.diamond.stone_pieces+ " Pcs" + '</a>');



				// 		$("#cp_dia_count").html(data.diamond.count);











				// 		$("#cp_mrp_amt").html('<a href='+dashboard_mrplist +' target="_blank">' + curr_symbol + data.mrp.mrp + '</a>');







				// 		$("#cp_mrp_wt").html('<a href='+dashboard_mrplist+' target="_blank">' + data.mrp.mrp_wt+ " g" + '</a>');



				// 		$("#cp_mrp_pcs").html('<a href='+dashboard_mrplist +' target="_blank">'+data.mrp.mrp_piece+ " Pcs" + '</a>');







				// 		$("#cp_mrp_count").html(data.mrp.mrp_count);



			    //    }



				    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_GreentagSalesDetails(from_date,to_date)



{



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     	url:base_url+ "index.php/admin_ret_dashboard/get_GreentagSalesDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){ console.log(data);



			        if(data != null &&  data.dash_greentag.tot_sales_wt !== undefined){



    				    var dashboard_greentag = base_url+'index.php/admin_ret_reports/dashboard_greentag/'+from_date+'/'+to_date+'/' + $('#id_branch').val();







    			        $("#cp_greentag_sales").html('<a href='+dashboard_greentag +' target="_blank">'+data.dash_greentag.tot_sales_wt + " g" + '</a>');



    				    $("#cp_greentag_count").html('<a href='+dashboard_greentag +' target="_blank">'+data.dash_greentag.tot_piece + " Pcs" + '</a>');



    				    $("#cp_greentag_rs").html('<a href='+dashboard_greentag +' target="_blank">'+ curr_symbol +money_format_india(parseFloat(data.dash_greentag.incentive).toFixed(2))+ '</a>');



			        }







				    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_SalesreturnDetails(from_date,to_date)



{



	$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     	url:base_url+ "index.php/admin_ret_dashboard/get_SalesReturnDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){







				    var dashboard_salesreturn = base_url+'index.php/admin_ret_reports/dashboard_salereturn/'+from_date+'/'+to_date+'/' + $('#id_branch').val();



				    if(data != null && data.dash_salesreturn_details.tot_wt !== undefined){



    			        $("#cp_salesreturn_wt").html('<a href='+dashboard_salesreturn +' target="_blank">'+data.dash_salesreturn_details.tot_wt + " g" + '</a>');



    				    $("#cp_salesreturn_pcs").html('<a href='+dashboard_salesreturn +' target="_blank">'+data.dash_salesreturn_details.tot_pcs + " Pcs" + '</a>');



    			      }







				    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_VitrualTagStatus(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_VitrualTagStatus?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){

				var	trHtml = '';



			      if(data != null && data.dash_virturaltag_details.homesale_wt !== undefined){



				    var dashboard_home = base_url+'index.php/admin_ret_reports/dashboard_virtualsales/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



				    var dashboard_partly = base_url+'index.php/admin_ret_reports/dashboard_virtualsales/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



			        $("#cp_virtual_tag_homesale_pcs").html(data.dash_virturaltag_details.homesale_pcs);



				    $("#cp_virtual_tag_homesale_wt").html('<a href='+dashboard_home +' target="_blank">'+data.dash_virturaltag_details.homesale_wt+" g" + '</a>');



				    $("#cp_virtual_tag_tagsplit_pcs").html(data.dash_virturaltag_details.tagsplit_pcs);



				    $("#cp_virtual_tag_tagsplit_wt").html('<a href='+dashboard_partly +' target="_blank">'+data.dash_virturaltag_details.tagsplit_wt+" g" + '</a>');



					trHtml +='<tr>'

						+'<td  style=" text-align:Right;"> '+data.dash_virturaltag_details.tagsplit_tag_wt+'</td>'

						+'<td  style=" text-align:Right;"> '+data.dash_virturaltag_details.tagsplit_wt+'</td>'

						+'<td  style=" text-align:Right;"> '+parseFloat(data.dash_virturaltag_details.tagsplit_tag_wt-data.dash_virturaltag_details.tagsplit_wt).toFixed(3)+'</td>'

						+'</tr>';

						$("#vir_partly_sale tbody ").html(trHtml);



			      }



				    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_metal_purchase_status(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_old_metal_purchase?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                    // $("#old_metal_purchase_gold").html("-");



                    // $("#old_metal_purchase_silver").html("-");



					trHtml='<td colspan="3">No Records found</td>';



                    if(data != null && data.dash_old_metal_purchase !== undefined){



						trHtml='';



                        $.each(data.dash_old_metal_purchase, function (index, element)  {



							let metal_type = element.metal_type;





					     var dashboard_oldmetal = base_url+'index.php/admin_ret_reports/dashboard_oldmetal/'+from_date+'/'+to_date+'/'+metal_type+'/' + $('#id_branch').val();

                        trHtml +='<tr>'

						+'<td  style=" text-align:left;"> '+element.type+'</td>'

						+'<td style=" text-align:right;" ><a href='+dashboard_oldmetal +' target="_blank">'+element.gross_wt+ " g" + '</a></td>'

						+'<td style=" text-align:right;" ><a href='+dashboard_oldmetal +' target="_blank">'+element.weight+ " g" + '</a></td></tr>';







                        //



                        // let weight = element.weight;



    					 var dashboard_oldmetal_gold = base_url+'index.php/admin_ret_reports/dashboard_oldmetal/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



    					// var dashboard_oldmetal_silver = base_url+'index.php/admin_ret_reports/dashboard_oldmetal/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



                        // if(metal_type == 1)



                        // $("#old_metal_purchase_gold").html('<a href='+dashboard_oldmetal_gold +' target="_blank">'+weight+ " g" + '</a>');



                        // if(metal_type == 2)



                        // $("#old_metal_purchase_silver").html('<a href='+dashboard_oldmetal_silver +' target="_blank">'+weight+ " g" + '</a>');



                        });



						$("#old_metal_purchase tbody ").html(trHtml);



                    }



                    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_CreditSalesDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_CreditSalesDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){







			    	var dashboard_creditbill = base_url+'index.php/admin_ret_reports/dashboard_creditsales/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



				    var dashboard_received = base_url+'index.php/admin_ret_reports/dashboard_creditsales/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



				    if(data != null && data.dash_credeit_sales.tot_due_amount !== undefined){



                        $("#tot_bill_amt").html('<a href='+dashboard_creditbill +' target="_blank">'+curr_symbol+money_format_india(data.dash_credeit_sales.tot_due_amount) + '</a>');



    				    $("#creditreceived").html('<a href='+dashboard_received +' target="_blank">'+curr_symbol+money_format_india(data.dash_credeit_sales.creditreceived) + '</a>');



    			      }



				    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_GiftVoucherDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_GiftVoucherDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



			      if(data != null && data.dash_gift_vouchers.tot_utlized !== undefined){



        				var dashboard_gift_utlized = base_url+'index.php/admin_ret_reports/dashboard_giftcard/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



        				var dashboard_gift_issue = base_url+'index.php/admin_ret_reports/dashboard_giftcard/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



        				var dashboard_gift_sold = base_url+'index.php/admin_ret_reports/dashboard_giftcard/'+from_date+'/'+to_date+'/3/' + $('#id_branch').val();



                            $("#gift_tot_utlized").html('<a href='+dashboard_gift_utlized +' target="_blank">'+curr_symbol+money_format_india(data.dash_gift_vouchers.tot_utlized) + '</a>');



        				    $("#gift_tot_issued").html('<a href='+dashboard_gift_issue +' target="_blank">'+curr_symbol+money_format_india(data.dash_gift_vouchers.tot_issued) + '</a>');



        			    	$("#gift_tot_sold").html('<a href='+dashboard_gift_sold +' target="_blank">'+curr_symbol+money_format_india(data.dash_gift_vouchers.tot_sold) + '</a>');



			      }



			    	$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_BillClassficationDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_BillClassficationDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



			        if(data != null && data.dash_bills_clasfication.newcusbillsale !== undefined){



                        $("#totalnewcusbill").html(data.dash_bills_clasfication.totalnewcusbill);



        				$("#newcisbillsalewt").html(data.dash_bills_clasfication.newcisbillsalewt+" g");



        				$("#newcusbillsale").html(curr_symbol+money_format_india(data.dash_bills_clasfication.newcusbillsale));



        				$("#totaloldcusbill").html(data.dash_bills_clasfication.totaloldcusbill);



        				$("#oldcusbillsalewt").html(data.dash_bills_clasfication.oldcusbillsalewt+" g");



        				$("#oldcusbillsale").html(curr_symbol+money_format_india(data.dash_bills_clasfication.oldcusbillsale));



			        }



    				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_BranchTransferDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_BranchTransferDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                    $("#branch_transfer").html("");



                    if(data != null && data.dash_approval_pendings.approvalpending !== undefined){



                        let approval_pending = data.dash_approval_pendings.approvalpending;



                        let download_pending = data.dash_approval_pendings.downloadpending;



                        if(approval_pending > 0 && download_pending > 0) {



                        Morris.Donut({



                            element: 'branch_transfer',



                            data: [



                            {label: "Approval Pending (Pcs)", value: data.dash_approval_pendings.approvalpending},



                            {label: "Download Pending (Pcs)", value: data.dash_approval_pendings.downloadpending}



                            ],



                            colors: [



                            "#ec5550", "#61b15a"



                            ]



                        });



                        }



                        else



                        {



                            $("#branch_transfer").html('<div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Approval Pending</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+approval_pending+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Download Pending</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+download_pending+'</div></div>');



                        }



                    }



                    $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_lot_tag_details(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_lot_tag_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



				var dashboard_lot = base_url+'index.php/admin_ret_reports/dashboard_lot/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



				var dashboard_tag = base_url+'index.php/admin_ret_reports/dashboard_tag/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



				var dashboard_non_tag = base_url+'index.php/admin_ret_reports/dashboard_lot/'+from_date+'/'+to_date+'/3/' + $('#id_branch').val();



				if(data != null && data.dash_lot_tag_details.lot_wt !== undefined){



                    $("#lot_pcs").html(data.dash_lot_tag_details.lot_pcs);



    				$("#lot_wt").html('<a href='+dashboard_lot +' target="_blank">'+data.dash_lot_tag_details.lot_wt+" g" + '</a>');



    				$("#tagged_pcs").html(data.dash_lot_tag_details.tagged_pcs);



    				$("#tagged_wt").html('<a href='+dashboard_tag +' target="_blank">'+data.dash_lot_tag_details.tagged_wt+" g" + '</a>');



					$("#non_tagged_pcs").html(data.dash_lot_tag_details.non_tag_pcs);



    				$("#non_tagged_wt").html('<a href='+dashboard_non_tag +' target="_blank">'+data.dash_lot_tag_details.non_tag_wt+" g" + '</a>');



					$("#pending_pcs").html( parseFloat(data.dash_lot_tag_details.lot_pcs) - parseFloat(data.dash_lot_tag_details.tagged_pcs) + parseFloat(data.dash_lot_tag_details.non_tag_pcs));



    				$("#pending_wt").html('<a href='+dashboard_tag +' target="_blank">'+ parseFloat(parseFloat(data.dash_lot_tag_details.lot_wt) - parseFloat(data.dash_lot_tag_details.tagged_wt) + parseFloat(data.dash_lot_tag_details.non_tag_wt) ).toFixed(3)+" g" + '</a>');







				}



    				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_OrderDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_OrderDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                    	$("#orders").html("");



                    	if(data != null && data.dash_orders_details.orderplaced !== undefined){



        				let orderplaced = data.dash_orders_details.orderplaced;



        				let orderreceived = data.dash_orders_details.orderreceived;



        				let ordersincart = data.dash_orders_details.ordersincart;



        				if(orderplaced > 0 && orderreceived > 0 && ordersincart > 0)



        				{



        					Morris.Donut({



        						element: 'orders',



        						data: [



        						  {label: "Placed", value: orderplaced},



        						  {label: "Received", value: orderreceived},



        						  {label: "Cart", value: ordersincart}



        						],



        						colors: [



        							"#f37121", "#8dc688", "#7b4b7d"



        						]



        					});



        				}



        				else



        				{



        					$("#orders").html('<div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Placed</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+orderplaced+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Received</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+orderreceived+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Cart</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+ordersincart+'</div></div>');



        				}



			            }







        				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_StockDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_StockDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                    	let g_opening_grosswt = parseFloat(data.dash_stock_details.g_opening_gwt).toFixed(2);



        				let g_sales_grosswt = parseFloat(data.dash_stock_details.g_tot_sales_gwt).toFixed(2);



        				let g_avail_grosswt = parseFloat(data.dash_stock_details.g_available_gwt).toFixed(2);



						let g_inward = parseFloat(data.dash_stock_details.g_inward_gwt).toFixed(2);







						let g_opening_pcs = data.dash_stock_details.g_opening_pcs;



        				let g_tot_sales_pcs = data.dash_stock_details.g_tot_sales_pcs;



        				let g_available_pcs = data.dash_stock_details.g_available_pcs;



						let g_inward_pcs = data.dash_stock_details.g_inward_pcs;







        				let g_total_wt = parseFloat(g_opening_grosswt) + parseFloat(g_sales_grosswt) + parseFloat(g_avail_grosswt) + parseFloat(g_inward);







        				$("#opening_gwt").html(g_opening_grosswt + " GM / " + g_opening_pcs + " Pcs");



        				$("#tot_sales_gwt").html(g_sales_grosswt + " GM / " + g_tot_sales_pcs + " Pcs");



        				$("#available_gwt").html(g_avail_grosswt + " GM / " + g_available_pcs + " Pcs");



        				$("#tot_inward_gwt").html(g_inward + " GM / " + g_inward_pcs + " Pcs");











        				let g_progress_openingwt = 0;



        				let g_progress_saleswt = 0;



        				let g_progress_availwt = 0;



						let g_progress_inward = 0;











        				if(g_total_wt > 0) {



        					g_progress_openingwt = Math.round((g_opening_grosswt / g_total_wt) * 100);



        					g_progress_saleswt = Math.round((g_sales_grosswt / g_total_wt) * 100);



        					g_progress_availwt = Math.round((g_avail_grosswt / g_total_wt) * 100);



							g_progress_inward = Math.round((g_inward / g_total_wt) * 100);



        				}







        				$("#progress_opening_gwt").css("width",g_progress_openingwt+"%");



        				$("#progress_tot_sales_gwt").css("width",g_progress_saleswt+"%");



        				$("#progress_available_gwt").css("width",g_progress_availwt+"%");



						$("#progress_tot_inward_gwt").css("width",g_progress_inward+"%");







        				$("#stock_pie").html("");







        				if(g_opening_pcs > 0  && g_available_pcs > 0)



        				{



							Morris.Donut({



								element: 'stock_pie',



								data: [



									{label: "Opening (Pcs)", value: g_opening_pcs},



									{label: "Inward (Pcs)", value: g_inward_pcs},



									{label: "Sales (Pcs)", value: g_tot_sales_pcs},



									{label: "Closing (Pcs)", value: g_available_pcs}



								],



								colors: [



									"#3C8DBC", "#DD4B39", "#f39c12" , "#03A65A"



								]



							});



        				}



        				else



        				{



        					$("#stock_pie").html('</br><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Opening Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+g_opening_pcs+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Sales Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+g_tot_sales_pcs+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Available Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+g_available_pcs+'</div></div>');



        				}



                        $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_silver_StockDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_silver_StockDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                    	let s_opening_grosswt = parseFloat(data.dash_stock_details.s_opening_gwt).toFixed(2);



        				let s_sales_grosswt = parseFloat(data.dash_stock_details.s_tot_sales_gwt).toFixed(2);



        				let s_avail_grosswt = parseFloat(data.dash_stock_details.s_available_gwt).toFixed(2);



						let s_inward = parseFloat(data.dash_stock_details.s_inward_gwt).toFixed(2);







						let s_opening_pcs = data.dash_stock_details.s_opening_pcs;



        				let s_tot_sales_pcs = data.dash_stock_details.s_tot_sales_pcs;



        				let s_available_pcs = data.dash_stock_details.s_available_pcs;



						let s_inward_pcs = data.dash_stock_details.s_inward_pcs;



        				let s_total_wt = parseFloat(s_opening_grosswt) + parseFloat(s_sales_grosswt) + parseFloat(s_avail_grosswt) + parseFloat(s_inward);







        				$("#s_opening_gwt").html(s_opening_grosswt + " GM / " + s_opening_pcs + " Pcs");



        				$("#s_tot_sales_gwt").html(s_sales_grosswt + " GM / " + s_tot_sales_pcs + " Pcs");



        				$("#s_available_gwt").html(s_avail_grosswt + " GM / " + s_available_pcs + " Pcs");



        				$("#s_tot_inward_gwt").html(s_inward + " GM / " +  s_inward_pcs + " Pcs");











        				let s_progress_openingwt = 0;



        				let s_progress_saleswt = 0;



        				let s_progress_availwt = 0;



						let s_progress_inward = 0;











        				if(s_total_wt > 0) {



        					s_progress_openingwt = Math.round((s_opening_grosswt / s_total_wt) * 100);



        					s_progress_saleswt = Math.round((s_sales_grosswt / s_total_wt) * 100);



        					s_progress_availwt = Math.round((s_avail_grosswt / s_total_wt) * 100);



							s_progress_inward = Math.round((s_inward / s_total_wt) * 100);







        				}











        				$("#s_progress_opening_gwt").css("width",s_progress_openingwt+"%");



        				$("#s_progress_tot_sales_gwt").css("width",s_progress_saleswt+"%");



        				$("#s_progress_available_gwt").css("width",s_progress_availwt+"%");



						$("#s_progress_tot_inward_gwt").css("width",s_progress_inward+"%");







        				$("#s_stock_pie").html("");







        				if(s_opening_pcs > 0  && s_available_pcs > 0)



        				{



							Morris.Donut({



								element: 's_stock_pie',



								data: [



									{label: "Opening (Pcs)", value: s_opening_pcs},



									{label: "Inward (Pcs)", value: s_inward_pcs},



									{label: "Sales (Pcs)", value: s_tot_sales_pcs},



									{label: "Closing (Pcs)", value: s_available_pcs}



								],



								colors: [



									"#3C8DBC", "#DD4B39", "#f39c12" , "#03A65A"



								]



							});







        				}



        				else



        				{



        					$("#s_stock_pie").html('</br><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Opening Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+s_opening_pcs+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Sales Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+s_tot_sales_pcs+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Available Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+s_available_pcs+'</div></div>');



        				}



                        $("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_ReorderDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_ReorderDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                    	$("#reorder_items_table tbody").empty();



        				let reorder_items = data.dash_reorder_items;







        				let table_value = "";



        				$.each(reorder_items, function (index, element)  {



        					table_value = table_value+'<tr>'+



        					'<td>'+element.product_name+'</td>'+



        					'<td>'+element.design_name+'</td>'+



        					'<td>'+element.weight_name+'</td>'+



        					'<td><label class="label label-success">'+element.available_pcs+'</label></td>'+



        					'<td><label class="label label-info">'+parseFloat(element.min_pcs-element.available_pcs)+'</label></td>'+



        					'</tr>';



        				});







        				if(table_value != "") {



        					$("#reorder_items_table tbody").append(table_value);



        				} else {



        					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



        					$("#reorder_items_table tbody").append(table_value);



        				}







        				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_stock_details(from_date,to_date)



{



	//$("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		 url:base_url+ "index.php/admin_ret_dashboard/get_stock_details?nocache=" + my_Date.getUTCSeconds(),



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 dataType:"JSON",



			 type:"POST",







			 cache:false,



			  success:function(data){



				$("#metal_stock_items_table tbody").empty();



				let metal = data.stock_details_dashboard;



                var id_branch=$('#id_branch').val();







				var table_value="";



				var op_blc_gwt=0;



				var	inw_gwt=0;



				var	br_out_gwt=0;



				var	sold_gwt=0;



				var	closing_gwt=0;



				$.each(metal, function (index, element)  {



					$.each(element, function (ind, val)  {



						let dashboard_stock = base_url+ "index.php/admin_ret_reports/get_stock_detail_list/list"+"/"+from_date+"/"+to_date+"/"+id_branch+"/"+val.id_metal;

						let close_gwt = (parseFloat(val.op_blc_gwt) + parseFloat(val.inw_gwt)) - (parseFloat(val.br_out_gwt) + parseFloat(val.sold_gwt));


					table_value += '<tr>'+



					'<td  style=" text-align:left;" ><b>'+val.metal_name+'</b></td>'+



					'<td style="text-align:right;" ><b><a href='+dashboard_stock+' target="_blank">'+val.op_blc_gwt+'</a></b></td>'+



					'<td style="text-align:right;" ><b><a href='+dashboard_stock+' target="_blank">'+val.inw_gwt+'</a></b></td>'+



					'<td style="text-align:right;" ><b><a href='+dashboard_stock+' target="_blank">'+val.br_out_gwt+'</a></b></td>'+



					'<td style="text-align:right;" ><b><a href='+dashboard_stock+' target="_blank">'+val.sold_gwt+'</a></b></td>'+



					// '<td style="text-align:right;" ><b><a href='+dashboard_stock+' target="_blank">'+val.closing_gwt+'</a></b></td>'+

					'<td style="text-align:right;" ><b><a href='+dashboard_stock+' target="_blank">'+(parseFloat(close_gwt).toFixed(3))+'</a></b></td>'+



					'</tr>';



					op_blc_gwt+=parseFloat(val.op_blc_gwt);



					inw_gwt+=parseFloat(val.inw_gwt);



					br_out_gwt+=parseFloat(val.br_out_gwt);



					sold_gwt+=parseFloat(val.sold_gwt);



					// closing_gwt+=parseFloat(val.closing_gwt);

					closing_gwt+=parseFloat(close_gwt);



				});



				});



				table_value_total = '<tr>'+



					'<td  style=" text-align:left;" ><b>Total </b></td>'+



					'<td style="text-align:right;" ><b><a href="" target="_blank">'+parseFloat(op_blc_gwt).toFixed(3)+'</a></b></td>'+



					'<td style="text-align:right;" ><b><a href="" target="_blank">'+parseFloat(inw_gwt).toFixed(3)+'</a></b></td>'+



					'<td style="text-align:right;" ><b><a href="" target="_blank">'+parseFloat(br_out_gwt).toFixed(3)+'</a></b></td>'+



					'<td style="text-align:right;" ><b><a href="" target="_blank">'+parseFloat(sold_gwt).toFixed(3)+'</a></b></td>'+



					'<td style="text-align:right;" ><b><a href="" target="_blank">'+parseFloat(closing_gwt).toFixed(3)+'</a></b></td>'+



					'</tr>';



				if(table_value != "") {



					$("#metal_stock_items_table tbody").html(table_value);



					$("#metal_stock_items_table tfoot").html(table_value_total);



				} else {



					table_value = "<tr><td colspan='6'>No Records found</td></tr>";



					$("#metal_stock_items_table tbody").html(table_value);



				}







				//		$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



			//	 $("div.overlay").css("display", "none");



			table_value = "<tr><td colspan='6'>No Records found</td></tr>";



					$("#metal_stock_items_table tbody").html(table_value);



			  }



	});



}


function get_deposit_data(from_date, to_date) {

	my_Date = new Date();

	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard/get_cash_deposit_details_branchwise?nocache=" + my_Date.getUTCSeconds(),

		dataType: "JSON",

		data: { 'from_date': from_date, 'to_date': to_date },

		type: "POST",

		cache: false,

		success: function (data) {

			let table_value = "";

			let tot_collection = 0;

			let tot_dep_amount = 0;

			let opening_amt = 0;
			let deposit = 0;
			let closing_amt = 0;


			$.each(data, function (key, element) {

				if (Object.keys(element).length > 0) {

					/* tot_collection = parseFloat(element[0].tot_bill) + parseFloat(element[0].tot_rec); //total colection in cash

					tot_dep_amount = parseFloat(element[0].tot_dep_amount); //previous date deposit

					opening_amt = tot_collection - tot_dep_amount; */

					opening_amt = element.opening;

					inward = parseFloat(element[0].inward); //selected date inward

					deposit = parseFloat(element[0].deposit); //selected date deposit

					closing_amt = opening_amt + inward - deposit;

					table_value = table_value + '<tr>';

					table_value += '<td>' + element.branch_name + '</td>' +

						'<td class="numbers">' + formatNumber(opening_amt, 2) + '</td>' +

						'<td class="numbers">' + formatNumber(inward, 2) + '</td>' +

						'<td class="numbers">' + formatNumber(deposit, 2) + '</td>' +

						'<td class="numbers">' + formatNumber(closing_amt, 2) + '</td>';

					table_value += '</tr>';


				}

			});

			$("#branch_wise_bank_deposit tbody").empty();

			$('#branch_wise_bank_deposit tbody').append(table_value);
		},

		error: function (error) {

			$("div.overlay").css("display", "none");

		}

	});

}



function get_KarigarOrderDetails()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_KarigarOrderDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 type:"POST",



			 cache:false,



			  success:function(data){



                    	$("#karigar_today_delivered").html(data.karigar_orders.today_delivered);



        				$("#karigar_today_pending").html(data.karigar_orders.today_pending);



        				$("#karigar_tomm_delivered").html(data.karigar_orders.tm_delivery);







        				$("#karigar_pending_delivery").html(data.karigar_orders.over_due_orders);



        				$("#karigar_yet_to_delivery").html(data.karigar_orders.work_in_progress);







        				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_CustomerOrderDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_customerOrderDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



					var dashboard_received = base_url+'index.php/admin_ret_reports/dashboard_customerorder/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();



					var dashboard_allocated = base_url+'index.php/admin_ret_reports/dashboard_customerorder/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();



					var dashboard_pending = base_url+'index.php/admin_ret_reports/dashboard_customerorder/'+from_date+'/'+to_date+'/3/' + $('#id_branch').val();



					var dashboard_ready = base_url+'index.php/admin_ret_reports/dashboard_customerorder/'+from_date+'/'+to_date+'/4/' + $('#id_branch').val();



					var dashboard_delivered = base_url+'index.php/admin_ret_reports/dashboard_customerorder/'+from_date+'/'+to_date+'/5/' + $('#id_branch').val();



					if(data != null && data.customer_order.received_piece !== undefined){







                    		$("#customer_today_received").html('<a href='+  dashboard_received +' target="_blank" style="color:#ffffff;">'+  data.customer_order.received_piece + '</a>');



            				$("#customer_today_allocated").html('<a href='+  dashboard_allocated +' target="_blank" style="color:#ffffff;">'+ data.customer_order.allocated_piece+ '</a>');



            				$("#customer_today_pending").html('<a href='+  dashboard_pending +' target="_blank" style="color:#ffffff;">'+ data.customer_order.pending_piece+ '</a>');



            				$("#customer_today_ready").html('<a href='+  dashboard_ready +' target="_blank" style="color:#ffffff;">'+ data.customer_order.ready_piece+ '</a>');



            				$("#customer_today_delivered").html('<a href='+  dashboard_delivered +' target="_blank" style="color:#ffffff;">'+ data.customer_order.delivery_piece+ '</a>');



					}







            				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_MetalStockDetails()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_MetalStockDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 type:"POST",

			 
			 data: {'id_branch':$('#id_branch').val()},



			 cache:false,



			  
			  success:function(data){

							var gl_tag_weight = 0;
							
							var gl_nt_weight = 0;

							var sl_tag_weight = 0;
							
							var sl_nt_weight = 0;

							var gl_weight = 0;

							var sl_weight = 0;

                    		$("#stock_total_gold_weight").html("-");

            				$("#stock_total_silver_weight").html("-");

							$.each(data.tag_details, function (index, element)  {

								if(element.metal == 'GOLD') {

            					var metal = element.metal;

            					gl_tag_weight += parseFloat(element.total_gwt != undefined ? element.total_gwt : 0 );
							}
								else if(element.metal == 'SILVER') {

									var metal = element.metal;

									sl_tag_weight += parseFloat(element.total_gwt == undefined ? 0 :element.total_gwt);

									// var gl_weight = (parseFloat(tag_weight) || 0) + (parseFloat(nt_weight) || 0);
								}

							});
							$.each(data.Nontag_details, function (index, element)  {

								if(element.metal == 'GOLD') {

									var metal = element.metal;

									gl_nt_weight += parseFloat(element.nt_total_gwt != undefined ? element.nt_total_gwt : 0 );
								
							}
								else if(element.metal == 'SILVER') {

									var metal = element.metal;

									sl_nt_weight += parseFloat(element.nt_total_gwt == undefined ? 0 : element.nt_total_gwt);

									// var gl_weight = (parseFloat(tag_weight) || 0) + (parseFloat(nt_weight) || 0);
								}

							});

								sl_weight = parseFloat(parseFloat(sl_tag_weight)+parseFloat(sl_nt_weight)).toFixed(3);

								gl_weight = parseFloat(parseFloat(gl_tag_weight) + parseFloat(gl_nt_weight)).toFixed(3);


            					// if(metal == 'GOLD')
									
            						$("#stock_total_gold_weight").html(gl_weight+ " GM");
									
								// if(metal == 'SILVER')
									
									$("#stock_total_silver_weight").html(sl_weight+ " GM");
										
										
										
										
										$("div.overlay").css("display", "none");
										
									},
									



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_CustomerDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_CustomerDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                        $("#new_customer_table tbody").empty();



        				let customer_details = data.dash_customer_details;







        				table_value = "";



        				$.each(customer_details, function (index, element)  {



        					table_value = table_value+'<tr>'+



        					'<td><div>'+element.firstname+'</div><div>'+element.mobile+'</div></td>'+



        					'<td>'+element.branchname+'</td>'+



        					'<td>'+element.jointhrough+'</td>'+



        					'</tr>';



        				});







        				if(table_value != "") {



        					$("#new_customer_table tbody").append(table_value);



        				} else {



        					table_value = "<tr><td colspan='3'>No Records found</td></tr>";



        					$("#new_customer_table tbody").append(table_value);



        				}







        				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function get_RecentBillDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_RecentBillDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                        	$("#recent_bills_table tbody").empty();



            				let bill_details = data.dash_bills_details;



            				bill_details = group_by(bill_details, "branchname");



            				table_value = "";







            				$.each(bill_details, function (key, element)  {







            					let subArrLength = element.length;



            					table_value = table_value+'<tr>'+



            					'<td rowspan='+subArrLength+'>'+key+'</td>';







            					$.each(element, function (index, values)  {



            						table_value = table_value+



            						'<td>'+values.bill_no+'</td>'+



            						'<td>'+values.cusname+'</td>'+



            						'<td>'+values.billtype+'</td>'+



            						'<td>'+values.billamount+'</td>'+



            						'</tr>';



            					});







            				});







            				if(table_value != "") {



            					$("#recent_bills_table tbody").append(table_value);



            				} else {



            					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



            					$("#recent_bills_table tbody").append(table_value);



            				}







            				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}

/*

function get_cash_abstract_details(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_cash_abstract_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                        	let cash_abs = data.dash_cash_abstarct_details;



            				$("#sales_amount").html(money_format_india(cash_abs.sales_amount));



            				$("#sales_total_tax_amount").html(money_format_india(cash_abs.sales_total_tax_amount));



            				$("#sales_return").html(money_format_india(cash_abs.sales_return));



            				$("#sales_return_total_tax_amount").html(money_format_india(cash_abs.sales_return_total_tax_amount));



            				$("#purchase_amount").html(money_format_india(cash_abs.purchase_amount));



            				$("#advance_receipt").html(money_format_india(cash_abs.advance_receipt));



            				$("#credit_sale").html(money_format_india(cash_abs.credit_sale));



            				$("#credit_receipt").html(money_format_india(cash_abs.credit_receipt));



            				$("#handling_charge").html(money_format_india(cash_abs.handling_charge));



            				$("#trans_total").html(money_format_india(cash_abs.trans_total));



            				$("#cash").html(money_format_india(cash_abs.cash));



            				// $("#chq").html(money_format_india(cash_abs.chq));



							$("#chq_recd").html(money_format_india(parseFloat(cash_abs.chq_recd).toFixed(2)));



							$("#chq_issued").html(money_format_india(parseFloat(cash_abs.chq_issue).toFixed(2)));



            				$("#card").html(money_format_india(cash_abs.card));



            				$("#nb").html(money_format_india(cash_abs.nb));



            				$("#advadj").html(money_format_india(cash_abs.advadj));



            				$("#chituti").html(money_format_india(cash_abs.chituti));



            				$("#handlingcharge").html(money_format_india(cash_abs.handlingcharge));



            				$("#orderadj").html(money_format_india(cash_abs.orderadj));



            				$("#giftvoucher").html(money_format_india(cash_abs.giftvoucher));



            				$("#roundoff").html(money_format_india(cash_abs.roundoff));



            				$("#paymodes_total").html(money_format_india(cash_abs.paymodes_total));



							$("#advance_deposit").html(money_format_india(cash_abs.advance_deposit));



							$("#other_expenses").html(money_format_india(cash_abs.other_expenses));



            				$(".chit_payments").remove();



							if(cash_abs.chit_payment_details.length>0){



								let heading = `<div class="col-md-12 col-xs-12 item-heading chit_payments">



													CHIT PAYMENT



												</div>`;



								$(".cash_abs_details").append(heading);



			 			        $.each(cash_abs.chit_payment_details,function(key,item){



									let trHTML = `<div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd chit_payments">



													<div class="col-md-6 col-xs-6 text">`+item.payment_mode+`</div>



													<div class="col-md-1 col-xs-1"></div>



													<div class="col-md-5 col-xs-5 values color">`+item.payment_amount+`</div>



												</div>`;



									$(".cash_abs_details").append(trHTML);



			 			        });



			 			    }



            				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}*/



function getEstimationDetails(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/getEstimationDetails?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                        	$("#estimation_details_table tbody").empty();



            				let estimation_details = data.dash_estimation_details;



            				estimation_details = group_by(estimation_details, "branchname");



            				table_value = "";







            				$.each(estimation_details, function (key, element)  {







            					let subArrLength = element.length;



            					table_value = table_value+'<tr>'+



            					'<td rowspan='+subArrLength+'>'+key+'</td>';







            					$.each(element, function (index, values)  {



            						table_value = table_value+



            						'<td>'+values.esti_no+'</td>'+



            						'<td>'+values.cusname+'</td>'+



            						'<td>'+values.estamount+'</td>'+



            						'<td>'+values.purchase_status+'</td>'+



            						'</tr>';



            					});



            				});







            				if(table_value != "") {



            					$("#estimation_details_table tbody").append(table_value);



            				} else {



            					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



            					$("#estimation_details_table tbody").append(table_value);



            				}







            				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



function retail_dashboard_details_old(from_date,to_date)  // this is old function dont call in any where



{



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_dashboard/get_retail_dashboard_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date},



			 type:"POST",



			 cache:false,



			 success:function(data){







			     if(data != null && data.estimation !== undefined){



				var estimation 	    = data.estimation;



				var billing 	    = data.billing;



				$('#live_estimation').text(estimation.estimation);



				$('#billing').text(billing.billing);



				$("#cp_estimation_created").html(data.dash_estmation.created);







				$("#cp_estimation_converted").html(data.dash_estmation.sold);



				$("#cp_sales_bills").html(data.dash_billing.bills);







				// $("#cp_sales_amount").html(data.dash_billing.billamount);



				$("#cp_virtual_tag_homesale_pcs").html(data.dash_virturaltag_details.homesale_pcs);







				$("#cp_virtual_tag_homesale_wt").html(data.dash_virturaltag_details.homesale_wt+" g");



				$("#cp_virtual_tag_tagsplit_pcs").html(data.dash_virturaltag_details.tagsplit_pcs);







				$("#cp_virtual_tag_tagsplit_wt").html(data.dash_virturaltag_details.tagsplit_wt+" g");



				$("#old_metal_purchase_gold").html("-");



				$("#old_metal_purchase_silver").html("-");



				$.each(data.dash_old_metal_purchase, function (index, element)  {



					let metal_type = element.metal_type;



					let weight = element.weight;



					if(metal_type == 1)



						$("#old_metal_purchase_gold").html(weight+ " g");



					if(metal_type == 2)



						$("#old_metal_purchase_silver").html(weight+ " g");



				});



				$("#tot_bill_amt").html(curr_symbol+data.dash_credeit_sales.tot_bill_amt);







				$("#creditreceived").html(curr_symbol+data.dash_credeit_sales.creditreceived);



				$("#gift_tot_utlized").html(curr_symbol+data.dash_gift_vouchers.tot_utlized);







				$("#gift_tot_issued").html(curr_symbol+data.dash_gift_vouchers.tot_issued);



				$("#gift_tot_sold").html(curr_symbol+data.dash_gift_vouchers.tot_sold);



				$("#totalnewcusbill").html(data.dash_bills_clasfication.totalnewcusbill);







				$("#newcisbillsalewt").html(data.dash_bills_clasfication.newcisbillsalewt+" g");



				$("#newcusbillsale").html(curr_symbol+data.dash_bills_clasfication.newcusbillsale);



				$("#totaloldcusbill").html(data.dash_bills_clasfication.totaloldcusbill);







				$("#oldcusbillsalewt").html(data.dash_bills_clasfication.oldcusbillsalewt+" g");



				$("#oldcusbillsale").html(curr_symbol+data.dash_bills_clasfication.oldcusbillsale);



				$("#branch_transfer").html("");



				let approval_pending = data.dash_approval_pendings.approvalpending;



				let download_pending = data.dash_approval_pendings.downloadpending;



				if(approval_pending > 0 && download_pending > 0) {



					Morris.Donut({



						element: 'branch_transfer',



						data: [



						{label: "Approval Pending (Pcs)", value: data.dash_approval_pendings.approvalpending},



						{label: "Download Pending (Pcs)", value: data.dash_approval_pendings.downloadpending}



						],



						colors: [



							"#ec5550", "#61b15a"



						]



					});



				} else {



					$("#branch_transfer").html('<div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Approval Pending</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+approval_pending+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Download Pending</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+download_pending+'</div></div>');



				}



				$("#lot_pcs").html(data.dash_lot_tag_details.lot_pcs);







				$("#lot_wt").html(data.dash_lot_tag_details.lot_wt+" g");



				$("#tagged_pcs").html(data.dash_lot_tag_details.tagged_pcs);







				$("#tagged_wt").html(data.dash_lot_tag_details.tagged_wt+" g");



				$("#orders").html("");



				let orderplaced = data.dash_orders_details.orderplaced;



				let orderreceived = data.dash_orders_details.orderreceived;



				let ordersincart = data.dash_orders_details.ordersincart;



				if(orderplaced > 0 && orderreceived > 0 && ordersincart > 0)



				{



					Morris.Donut({



						element: 'orders',



						data: [



						  {label: "Placed", value: orderplaced},



						  {label: "Received", value: orderreceived},



						  {label: "Cart", value: ordersincart}



						],



						colors: [



							"#f37121", "#8dc688", "#7b4b7d"



						]



					});



				}



				else



				{



					$("#orders").html('<div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Placed</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+orderplaced+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Received</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+orderreceived+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Cart</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+ordersincart+'</div></div>');



				}



				let opening_grosswt = data.dash_stock_details.opening_gwt;



				let sales_grosswt = data.dash_stock_details.tot_sales_gwt;



				let avail_grosswt = data.dash_stock_details.available_gwt;



				let total_wt = parseFloat(opening_grosswt) + parseFloat(sales_grosswt) + parseFloat(avail_grosswt);



				$("#opening_gwt").html(opening_grosswt);



				$("#tot_sales_gwt").html(sales_grosswt);



				$("#available_gwt").html(avail_grosswt);



				let progress_openingwt = 0;



				let progress_saleswt = 0;



				let progress_availwt = 0;



				if(total_wt > 0) {



					progress_openingwt = Math.round((opening_grosswt / total_wt) * 100);



					progress_saleswt = Math.round((sales_grosswt / total_wt) * 100);



					progress_availwt = Math.round((avail_grosswt / total_wt) * 100);



				}



				$("#opening_gwt").html(opening_grosswt);



				$("#tot_sales_gwt").html(sales_grosswt);



				$("#available_gwt").html(avail_grosswt);



				$("#progress_opening_gwt").css("width",progress_openingwt+"%");



				$("#progress_tot_sales_gwt").css("width",progress_saleswt+"%");



				$("#progress_available_gwt").css("width",progress_availwt+"%");



				let opening_pcs = data.dash_stock_details.opening_pcs;



				let tot_sales_pcs = data.dash_stock_details.tot_sales_pcs;



				let available_pcs = data.dash_stock_details.available_pcs;



				$("#stock_pie").html("");



				if(opening_pcs > 0 && tot_sales_pcs > 0 && available_pcs > 0)



				{



					Morris.Donut({



						element: 'stock_pie',



						data: [



						  {label: "Opening", value: opening_pcs},



						  {label: "Sales", value: tot_sales_pcs},



						  {label: "Available", value: available_pcs}



						],



						colors: [



							"#3C8DBC", "#DD4B39", "#00A65A"



						]



					});



				}



				else



				{



					$("#stock_pie").html('<div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Opening Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+opening_pcs+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Sales Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+tot_sales_pcs+'</div></div><div class="col-md-12 col-xs-12 no-paddingwidth inside-items"><div class="col-md-7 col-xs-7 no-paddingwidth label-text">Available Pcs</div><div class="col-md-5 col-xs-5 no-paddingwidth label-value">'+available_pcs+'</div></div>');



				}



				$("#reorder_items_table tbody").empty();



				let reorder_items = data.dash_reorder_items;



				let table_value = "";



				$.each(reorder_items, function (index, element)  {



					table_value = table_value+'<tr>'+



					'<td>'+element.product_name+'</td>'+



					'<td>'+element.design_name+'</td>'+



					'<td>'+element.weight_name+'</td>'+



					'<td><label class="label label-success">'+element.available_pcs+'</label></td>'+



					'<td><label class="label label-info">'+element.min_pcs+'</label></td>'+



					'</tr>';



				});



				if(table_value != "") {



					$("#reorder_items_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



					$("#reorder_items_table tbody").append(table_value);



				}



				$("#karigar_today_delivered").html(data.karigar_orders.today_delivered);



				$("#karigar_today_pending").html(data.karigar_orders.today_pending);



				$("#karigar_tomm_delivered").html(data.karigar_orders.tm_delivery);



				$("#karigar_pending_delivery").html(data.karigar_orders.over_due_orders);



				$("#karigar_yet_to_delivery").html(data.karigar_orders.work_in_progress);



				$("#customer_today_delivered").html(data.customer_orders.today_delivered);



				$("#customer_today_pending").html(data.customer_orders.today_pending);



				$("#customer_tomm_delivery_ready").html(data.customer_orders.tm_ready_for_delivery);



				$("#customer_tomm_pending").html(data.customer_orders.today_delivered);



				$("#customer_pending_delivery").html(data.customer_orders.over_due_orders);



				$("#customer_yet_to_delivery").html(data.customer_orders.work_in_progress);



				$("#stock_total_gold_weight").html("-");



				$("#stock_total_silver_weight").html("-");



				$.each(data.stock_metal_details, function (index, element)  {



					let metal = element.metal;



					let weight = element.total_gwt;



					if(metal == 'GOLD')



						$("#stock_total_gold_weight").html(weight+ " GM");



					if(metal == 'SILVER')



						$("#stock_total_silver_weight").html(weight+ " GM");



				});



				$("#new_customer_table tbody").empty();



				let customer_details = data.dash_customer_details;



				table_value = "";



				$.each(customer_details, function (index, element)  {



					table_value = table_value+'<tr>'+



					'<td><div>'+element.firstname+'</div><div>'+element.mobile+'</div></td>'+



					'<td>'+element.branchname+'</td>'+



					'<td>'+element.jointhrough+'</td>'+



					'</tr>';



				});



				if(table_value != "") {



					$("#new_customer_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='3'>No Records found</td></tr>";



					$("#new_customer_table tbody").append(table_value);



				}







				$("#recent_bills_table tbody").empty();



				let bill_details = data.dash_bills_details;



				bill_details = group_by(bill_details, "branchname");



				table_value = "";



				$.each(bill_details, function (key, element)  {



					let subArrLength = element.length;



					table_value = table_value+'<tr>'+



					'<td rowspan='+subArrLength+'>'+key+'</td>';



					$.each(element, function (index, values)  {



						table_value = table_value+



						'<td>'+values.bill_no+'</td>'+



						'<td>'+values.cusname+'</td>'+



						'<td>'+values.billtype+'</td>'+



						'<td>'+values.billamount+'</td>'+



						'</tr>';



					});



				});



				if(table_value != "") {



					$("#recent_bills_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



					$("#recent_bills_table tbody").append(table_value);



				}



				let cash_abs = data.dash_cash_abstarct_details;



				$("#sales_amount").html(cash_abs.sales_amount);



				$("#sales_total_tax_amount").html(cash_abs.sales_total_tax_amount);



				$("#sales_return").html(cash_abs.sales_return);



				$("#sales_return_total_tax_amount").html(cash_abs.sales_return_total_tax_amount);



				$("#purchase_amount").html(cash_abs.purchase_amount);



				$("#advance_receipt").html(cash_abs.advance_receipt);



				$("#credit_sale").html(cash_abs.credit_sale);



				$("#credit_receipt").html(cash_abs.credit_receipt);



				$("#handling_charge").html(cash_abs.handling_charge);



				$("#trans_total").html(cash_abs.trans_total);



				$("#cash").html(cash_abs.cash);



				$("#chq").html(cash_abs.chq);



				$("#card").html(cash_abs.card);



				$("#nb").html(cash_abs.nb);



				$("#advadj").html(cash_abs.advadj);



				$("#chituti").html(cash_abs.chituti);



				$("#handlingcharge").html(cash_abs.handlingcharge);



				$("#orderadj").html(cash_abs.orderadj);



				$("#giftvoucher").html(cash_abs.giftvoucher);



				$("#roundoff").html(cash_abs.roundoff);



				$("#paymodes_total").html(cash_abs.paymodes_total);



				$("#estimation_details_table tbody").empty();



				let estimation_details = data.dash_estimation_details;



				estimation_details = group_by(estimation_details, "branchname");



				table_value = "";



				$.each(estimation_details, function (key, element)  {



					let subArrLength = element.length;



					table_value = table_value+'<tr>'+



					'<td rowspan='+subArrLength+'>'+key+'</td>';



					$.each(element, function (index, values)  {



						table_value = table_value+



						'<td>'+values.esti_no+'</td>'+



						'<td>'+values.cusname+'</td>'+



						'<td>'+values.estamount+'</td>'+



						'<td>'+values.purchase_status+'</td>'+



						'</tr>';



					});



				});







				if(table_value != "") {



					$("#estimation_details_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



					$("#estimation_details_table tbody").append(table_value);



				}



			     }



		 	 },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}

/*

function sales_dashboard_data(from_date, to_date)



{



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_dashboard/get_SaleBill_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date},



			 type:"POST",



			 cache:false,



			 success:function(data){



				let table_value = "";



				let cw_records = data.categorywise_records;



				let mw_records = data.metalwise_records;



				let pw_records = data.paymentwise_records;



				let category_wise = Array();



				$("#sales_table tbody").empty();



				var tag = 0;



				var sold_weight = 0;



				$.each(cw_records, function (key1, element1)  {



					let branch_name = element1.branch_name;



					let br_wise_details = element1.branchwise_sales_details;



					let br_wise_length = br_wise_details.length;



					if(br_wise_length > 0) {



						let subArrLength = br_wise_length == 0 ? 1 : br_wise_length;



						table_value = table_value+'<tr>'+



						'<td rowspan='+subArrLength+'>'+branch_name+'</td>';



						$.each(br_wise_details, function (bwd_key, bwd_element)  {



							category_wise.push(bwd_element);



							table_value = table_value+



							'<td>'+bwd_element.category_name+'</td>'+



							'<td class="numbers">'+money_format_india(bwd_element.sold_weight)+'</td>'+



							'<td class="numbers">'+(bwd_element.green_tag == null ? 0 : bwd_element.green_tag)+'</td>'+



							'</tr>';



							sold_weight += parseFloat(bwd_element.sold_weight);



                            tag += parseFloat(bwd_element.green_tag == null ? 0 : bwd_element.green_tag);



						});



					}



				});



				if(table_value != "") {



					table_value = table_value+



					'<td> GRAND TOTAL </td>'+



					'<td></td>'+



					'<td class="numbers">'+money_format_india(parseFloat(sold_weight).toFixed(2))+'</td>'+



					'<td class="numbers">'+tag+'</td>'+



					'</tr>';





				}



				if(table_value != "") {



					$("#sales_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='4'>No Records found</td></tr>";



					$("#sales_table tbody").append(table_value);



				}







				category_wise = group_by(category_wise, "category_name");



				console.log("category_wise",category_wise);



				table_value = "";



				pw_amount =0



				$("#payment_wise_table tbody").empty();



				$.each(pw_records, function (key2, element2)  {



					let branch_name = element2.branch_name;



					let pr_wise_details = element2.paymentwise_sales_details;



					let pr_wise_length = pr_wise_details.length;



					if(pr_wise_length > 0) {



						let subArrLength = pr_wise_length == 0 ? 1 : pr_wise_length;



						table_value = table_value+'<tr>'+



						'<td rowspan='+subArrLength+'>'+branch_name+'</td>';



						let group_pr_wise_details=[];



						pr_wise_details.map((ele,ind)=>{

						    const paymentMode = ele.payment_mode;

							const amount = parseFloat(ele.amount);

							const branch_name = ele.branch_name;



                               const existingEntryIndex = group_pr_wise_details.findIndex((entry) => entry.payment_mode === paymentMode);



                              if (existingEntryIndex !== -1) {

                                group_pr_wise_details[existingEntryIndex].amount += amount;

                              } else {

                                group_pr_wise_details.push({ payment_mode: paymentMode, amount: amount, branch_name: branch_name });

                              }

						})



						$.each(group_pr_wise_details, function (pwd_key, pwd_element)  {



							table_value = table_value+



							'<td>'+pwd_element.payment_mode+'</td>'+



							'<td class="numbers">'+money_format_india(pwd_element.amount)+'</td>'+



							'</tr>';



							pw_amount+=parseFloat(pwd_element.amount)



						});



					}



				});



				if(table_value != "") {



					table_value = table_value+



					'<td> GRAND TOTAL </td>'+



					'<td></td>'+



					'<td class="numbers">'+money_format_india(parseFloat(pw_amount).toFixed(2))+'</td>'+



					'</tr>';





				}



				if(table_value != "") {



					$("#payment_wise_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='3'>No Records found</td></tr>";



					$("#payment_wise_table tbody").append(table_value);



				}



				table_value = "";



				metalt_wt = 0;



				$("#metal_wise_table tbody").empty();



				$.each(mw_records, function (key3, element3)  {



					let branch_name = element3.branch_name;



					let mt_wise_details = element3.metalwise_sales_details;



					let mt_wise_length = mt_wise_details.length;



					if(mt_wise_length > 0) {



						let subArrLength = mt_wise_length == 0 ? 1 : mt_wise_length;



						table_value = table_value+'<tr>'+



						'<td rowspan='+subArrLength+'>'+branch_name+'</td>';



						$.each(mt_wise_details, function (pwd_key, mwd_element)  {



							table_value = table_value+



							'<td>'+mwd_element.metal+'</td>'+



							'<td class="numbers">'+money_format_india(mwd_element.sold_weight)+'</td>'+



							'</tr>';



							metalt_wt += parseFloat(mwd_element.sold_weight);

						});



					}



				});



				if(table_value != "") {



					table_value = table_value+



					'<td> GRAND TOTAL </td>'+



					'<td></td>'+



					'<td class="numbers">'+money_format_india(parseFloat(metalt_wt).toFixed(3))+'</td>'+



					'</tr>';





				}



				if(table_value != "") {



					$("#metal_wise_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='3'>No Records found</td></tr>";



					$("#metal_wise_table tbody").append(table_value);



				}



			},



			error:function(error)



			{



			   $("div.overlay").css("display", "none");



			}



  });



}

*/

function sales_dashboard_data(from_date, to_date,id_branch)



{



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_dashboard/get_SaleBill_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			 success:function(data){



				let table_value = "";



				let cw_records = data.categorywise_records;



				let mw_records = data.metalwise_records;



				let pw_records = data.paymentwise_records;



				let pw_summary_records = data.payment_summary;



				let mw_summary = data.metal_wise_summary;



				let cw_summary = data.category_wise_summary;



				let category_wise = Array();







				//NEW





				$("#sales_category_table tbody").empty();



				cat_table ='';



				cattag = 0;



				catsold_weight = 0;



				$.each(cw_summary, function (bwd_key, bwd_element)  {



					cat_table +=



					'<tr> <td>'+bwd_element.category_name+'</td>'+



					'<td class="numbers">'+money_format_india(bwd_element.sold_weight)+'</td>'+



					'<td class="numbers">'+(bwd_element.green_tag == null ? 0 : bwd_element.green_tag)+'</td>'+



					'</tr>';



					catsold_weight += parseFloat(bwd_element.sold_weight);



					cattag += parseFloat(bwd_element.green_tag == null ? 0 : bwd_element.green_tag);



				});





				if(cat_table != "") {



					cat_table = cat_table+



					'<tr> <td> <b>GRAND TOTAL</b> </td>'+



					'<td class="numbers">'+money_format_india(parseFloat(catsold_weight).toFixed(3))+'</td>'+



					'<td class="numbers">'+cattag+'</td>'+



					'</tr>';





				}



				if(cat_table != "") {



					$("#sales_category_table tbody").append(cat_table);



				} else {



					cat_table = "<tr><td colspan='3'>No Records found</td></tr>";



					$("#sales_category_table tbody").append(cat_table);



				}





				$("#metal_wise_sumarry_table tbody").empty();



				metal_table ='';



				metaltag = 0;



				metalsold_weight = 0;



				$.each(mw_summary, function (pwd_key, mwd_element)  {



					metal_table = metal_table+



					'<tr><td>'+mwd_element.metal+'</td>'+



					'<td class="numbers">'+money_format_india(mwd_element.sold_weight)+'</td>'+



					'</tr>';



					metalsold_weight += parseFloat(mwd_element.sold_weight);

				});



				if(metal_table != "") {



					metal_table = metal_table+



					'<tr><td><b> GRAND TOTAL </b></td>'+



					'<td class="numbers">'+money_format_india(parseFloat(metalsold_weight).toFixed(3))+'</td>'+



					'</tr>';





				}



				if(metal_table != "") {



					$("#metal_wise_sumarry_table tbody").append(metal_table);



				} else {



					metal_table = "<tr><td colspan='3'>No Records found</td></tr>";



					$("#metal_wise_sumarry_table tbody").append(metal_table);



				}











				//NEW



				$("#sales_table tbody").empty();



				var tag = 0;



				var sold_weight = 0;



				$.each(cw_records, function (key1, element1)  {



					let branch_name = element1.branch_name;



					let br_wise_details = element1.branchwise_sales_details;



					let br_wise_length = br_wise_details.length;



					if(br_wise_length > 0) {



						let subArrLength = br_wise_length == 0 ? 1 : br_wise_length;



						table_value = table_value+'<tr>'+



						'<td rowspan='+subArrLength+'>'+branch_name+'</td>';



						let cw_sub_weight = 0;

						let cw_sub_green_tag = 0;



						$.each(br_wise_details, function (bwd_key, bwd_element)  {



							cw_sub_weight+=parseFloat(bwd_element.sold_weight)

							cw_sub_green_tag+=parseFloat(bwd_element.green_tag ? bwd_element.green_tag : 0)



							category_wise.push(bwd_element);



							table_value = table_value+



							'<td>'+bwd_element.category_name+'</td>'+



							'<td class="numbers">'+money_format_india(bwd_element.sold_weight)+'</td>'+



							'<td class="numbers">'+(bwd_element.green_tag == null ? 0 : bwd_element.green_tag)+'</td>'+



							'</tr>';



							sold_weight += parseFloat(bwd_element.sold_weight);



                            tag += parseFloat(bwd_element.green_tag == null ? 0 : bwd_element.green_tag);



						});





						if(cw_sub_weight || cw_sub_green_tag) {



							table_value = table_value+



							'<td> SUB TOTAL </td>'+



							'<td></td>'+



							'<td class="numbers">'+money_format_india(parseFloat(cw_sub_weight).toFixed(3))+'</td>'+



							'<td class="numbers">'+cw_sub_green_tag+'</td>'+



							'</tr>';





						}



					}



				});



				if(table_value != "") {



					table_value = table_value+



					'<td> <b>GRAND TOTAL</b> </td>'+



					'<td></td>'+



					'<td class="numbers">'+money_format_india(parseFloat(sold_weight).toFixed(3))+'</td>'+



					'<td class="numbers">'+tag+'</td>'+



					'</tr>';





				}



				if(table_value != "") {



					$("#sales_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='4'>No Records found</td></tr>";



					$("#sales_table tbody").append(table_value);



				}







				category_wise = group_by(category_wise, "category_name");



				console.log("category_wise",category_wise);





				$("#payment_wise_summary_table tbody").empty();



				table_value="";



				let tot_summarywise_payment=0;



				$.each(pw_summary_records,function(key5,element5){





					if(pw_summary_records.length>0){



						tot_summarywise_payment+=parseFloat(element5.amount);



						table_value = table_value+'<tr>'+



						'<td>'+element5.payment_mode+'</td>'+



						'<td class="numbers">'+money_format_india(element5.amount)+'</td>'+



						'</tr>';

					}

				})



				if(tot_summarywise_payment>0){

					table_value+= '<tr>'+

					'<td><b>Grand Total</b></td>'+

					'<td class="numbers">'+money_format_india(parseFloat(tot_summarywise_payment).toFixed(2))+'</td>'+

					'<tr>'

				}



				if(table_value != "") {



					$("#payment_wise_summary_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='2'>No Records found</td></tr>";



					$("#payment_wise_summary_table tbody").append(table_value);



				}







				table_value = "";



				pw_amount =0



				$("#payment_wise_table tbody").empty();



				$.each(pw_records, function (key2, element2)  {



					let branch_name = element2.branch_name;



					let pr_wise_details = element2.paymentwise_sales_details;



					let pr_wise_length = pr_wise_details.length;



					if(pr_wise_length > 0) {



						let subArrLength = pr_wise_length == 0 ? 1 : pr_wise_length;



						table_value = table_value+'<tr>'+



						'<td rowspan='+subArrLength+'>'+branch_name+'</td>';



						let pw_total = 0;



						$.each(pr_wise_details, function (pwd_key, pwd_element)  {



							pw_total+=parseFloat(pwd_element.amount);



							table_value = table_value+



							'<td>'+pwd_element.payment_mode+'</td>'+



							'<td class="numbers">'+money_format_india(pwd_element.amount)+'</td>'+



							'</tr>';



							pw_amount+=parseFloat(pwd_element.amount)



						});



						if(pw_total){



							table_value = table_value+



							'<td> SUB TOTAL </td>'+



							'<td></td>'+



							'<td class="numbers">'+money_format_india(parseFloat(pw_total).toFixed(2))+'</td>'+



							'</tr>';

						}



					}



				});



				if(table_value != "") {



					table_value = table_value+



					'<td> <b>GRAND TOTAL</b> </td>'+



					'<td></td>'+



					'<td class="numbers">'+money_format_india(parseFloat(pw_amount).toFixed(2))+'</td>'+



					'</tr>';





				}



				if(table_value != "") {



					$("#payment_wise_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='3'>No Records found</td></tr>";



					$("#payment_wise_table tbody").append(table_value);



				}



				table_value = "";



				metalt_wt = 0;



				$("#metal_wise_table tbody").empty();



				$.each(mw_records, function (key3, element3)  {



					let branch_name = element3.branch_name;



					let mt_wise_details = element3.metalwise_sales_details;



					let mt_wise_length = mt_wise_details.length;



					if(mt_wise_length > 0) {



						let subArrLength = mt_wise_length == 0 ? 1 : mt_wise_length;



						table_value = table_value+'<tr>'+



						'<td rowspan='+subArrLength+'>'+branch_name+'</td>';



						$.each(mt_wise_details, function (pwd_key, mwd_element)  {



							table_value = table_value+



							'<td>'+mwd_element.metal+'</td>'+



							'<td class="numbers">'+money_format_india(mwd_element.sold_weight)+'</td>'+



							'</tr>';



							metalt_wt += parseFloat(mwd_element.sold_weight);

						});



					}



				});



				if(table_value != "") {



					table_value = table_value+



					'<td><b> GRAND TOTAL </b></td>'+



					'<td></td>'+



					'<td class="numbers">'+money_format_india(parseFloat(metalt_wt).toFixed(3))+'</td>'+



					'</tr>';





				}



				if(table_value != "") {



					$("#metal_wise_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='3'>No Records found</td></tr>";



					$("#metal_wise_table tbody").append(table_value);



				}



			},



			error:function(error)



			{



			   $("div.overlay").css("display", "none");



			}



  });



}



function group_by(objArr, keyname)



{



	let result = objArr.reduce(function (r, a) {



		r[a[keyname]] = r[a[keyname]] || [];



		r[a[keyname]].push(a);



		return r;



	}, Object.create(null));



	return result;



}



function get_branch_order(from_date="",to_date="")



{



	my_Date = new Date();



	$.ajax({



	  	  url:base_url+ "index.php/admin_ret_dashboard/get_order_data?nocache=" + my_Date.getUTCSeconds(),



		  data:{'from_date':from_date,'to_date':to_date},



    	 dataType:"JSON",



    	 type:"POST",



    	 success:function(data){



		    // console.log(data);



            if(data != null && data.order !== undefined){



    	 	    set_branch_order(data,from_date,to_date);



            }



    	  },



    	  error:function(error)



    	  {



    	  $("div.overlay").css("display", "none");



    	  }



	  });



}



function set_branch_order(data,from_date,to_date)



{



	$('#order-data tbody').remove();



	$('#catalog').text(data.tot_catalog);



	$('#custom').text(data.tot_custom);



	$('#repair').text(data.tot_repair);



    $.each(data['order'], function (index, element)



	{



		        id_branch = element.id_branch;







				  $('#order-data').append(



                  $('<tbody><tr>')



				  .append($('<td style="text-align: centre;padding: 5px;">').append(element.branch_name))



                  .append($('<td style="text-align: centre;padding: 5px;">').append(element.catalog))



                  .append($('<td style="text-align: centre;padding: 5px;">').append(element.custom))



				  .append($('<td style="text-align: centre;padding: 5px;">').append(element.repair))



				  );



		 });



}



function sales_details(from_date,to_date)



{



	$('#sale_details tbody').remove();



	$('#tot_bills').text('');



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_dashboard/get_sales_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date},



			 type:"POST",



			 cache:false,



			 success:function(data){







			     if(data.sales_details != undefined){



        				if(data.sales_details.length>0)







        				{







        					var total_bills=0;







        					$.each(data.sales_details, function (index, element)







        					{







        						total_bills+=parseFloat(element.billing);







        						$('#sale_details').append(







        						$('<tbody><tr>')







        						.append($('<td style="text-align: centre;padding: 5px;">').append(element.branch_name))







        						.append($('<td style="text-align: centre;padding: 5px;">').append(element.billing))







        						);







        					});







        					$('#tot_bills').html(total_bills);







        				}







			     }



		 	 },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});







}



function get_average_bill_value()



{



	$('#avg_details tbody').remove();



	$('#tot_bills').text('');



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_dashboard/get_MetalBill_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 type:"POST",



			 cache:false,



			 success:function(data){



				if(data.sales_details.length>0)



				{



					var total_bills=0;



					var html='';



					$.each(data.sales_details, function (index, element)



					{



						total_bills+=parseFloat(element.billing);



						var avg_amt=parseFloat(element.sale_amount/element.billing).toFixed(2);



						$('#avg_details').append(



						$('<tbody><tr>')



						.append($('<td style="text-align: centre;padding: 5px;">').append(element.branch_name))



						.append($('<td style="text-align: centre;padding: 5px;">').append(element.metal))



						.append($('<td style="text-align: centre;padding: 5px;">').append(avg_amt))



						);



					});



					$('#tot_bills').html(total_bills);



				}



		 	 },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



 function get_branchname()



 {



    $(".overlay").css('display','block');



    $.ajax({



    type: 'GET',



    url: base_url+'index.php/branch/branchname_list',



    dataType:'json',



    success:function(data){



    var id_branch =  $('#id_branch').val();







     $("#branch_select,.branch_filter").append(



        $("<option></option>")



        .attr("value", 0)



        .text('All' )



    );







    $.each(data.branch, function (key, item) {



        $("#branch_select,.branch_filter").append(



            $("<option></option>")



            .attr("value", item.id_branch)



            .text(item.name )



        );



    });



    $("#branch_select,.branch_filter").select2({



        placeholder: "Select Branch",



        allowClear: true



    });



    if($("#branch_select").length || $(".ret_branch").length){



     $("#branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));



    }



    if($(".branch_filter").length){



     $(".branch_filter").select2("val",'');



    }



    $(".overlay").css("display", "none");



    }



    });



}



//Order Managent Details



function get_customer_order_details(from_date,to_date)



{



    $("#customer_order_table tbody").empty();



    $("#cus_order_details tbody").empty();



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_customer_order_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



            var table_value='';



            var cus_details='';



            var total_pcs=0;



            var total_wt=0;



                $.each(data.cus_orders_details,function(key,items){



                    total_pcs+=parseFloat(items.totalitems);



                    total_wt+=parseFloat(items.weight);



                    cus_details+='<tr>'+



                                        '<td>'+items.branch_name+'</td>'+



                                        '<td>'+items.cus_name+'</td>'+



                                        '<td>'+items.product_name+'</td>'+



                                        '<td>'+(items.weight+'/'+items.totalitems)+'</td>'+



                                        '<td>'+items.order_status+'</td>'+



                                 '</tr>'



                });



                $('.total_pcs').html(parseFloat(total_wt).toFixed(3)+'/'+total_pcs);



                $("#cus_order_details tbody ").append(cus_details);







        		$.each(data.cus_orders, function (bwd_key, bwd_element)  {



        		    var pending_url = base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=0&filter_type=1&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;



        		    var order_placed_url = base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=1&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;







        		    var karigar_pending_url = base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=0&filter_type=4&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;



        		    var karigar_delivered_url = base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=4&filter_type=7&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;



        		    var karigar_over_due_url = base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=6&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;







        		    var customer_pending_url = base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=3&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;



        		    var customer_delivery_ready_url= base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=4&filter_type=7&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;



        		    var customer_delivered_url= base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=5&filter_type=2&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;



        		    var customer_over_due_url= base_url+'index.php/admin_ret_reports/order_status/list/?order_staus=3&filter_type=5&id_branch='+bwd_element.order_from+'&from_date='+from_date+'&to_date='+to_date;







					table_value +='<tr>'+



    					'<td>'+bwd_element.branch_name+'</td>'+



    					'<td class="numbers"><a href='+pending_url+' target="_blank">'+(bwd_element.allocation_pending_wt+'/'+bwd_element.allocation_pending_pcs)+'</td>'+



    					'<td class="numbers"><a href='+order_placed_url+' target="_blank">'+(bwd_element.allocation_done_wt+'/'+bwd_element.allocation_done_pcs)+'</td>'+



    					'<td class="numbers"><a href='+karigar_pending_url+' target="_blank">'+(bwd_element.karigar_pending_wt+'/'+bwd_element.karigar_pending_pcs)+'</a></td>'+



    					'<td class="numbers"><a href='+karigar_delivered_url+' target="_blank">'+(bwd_element.karigar_delivered_wt+'/'+bwd_element.karigar_delivered_pcs)+'</a></td>'+



    					'<td class="numbers"><a href='+karigar_over_due_url+' target="_blank" '+(bwd_element.karigar_over_due_pcs>0 ? 'style="color:red;"' :'')+' >'+(bwd_element.karigar_over_due_wt+'/'+bwd_element.karigar_over_due_pcs)+'</a></td>'+



    					'<td class="numbers"><a href='+customer_pending_url+' target="_blank">'+(bwd_element.cus_pending_wt+'/'+bwd_element.cus_pending_pcs)+'</a></td>'+



    					'<td class="numbers"><a href='+customer_delivery_ready_url+' target="_blank" >'+(bwd_element.cus_delivery_ready_wt+'/'+bwd_element.cus_delivery_ready_pcs)+'</a></td>'+



    					'<td class="numbers"><a href='+customer_delivered_url+' target="_blank">'+(bwd_element.cus_delivered_wt+'/'+bwd_element.cus_delivered_pcs)+'</a></td>'+



    					'<td class="numbers"><a href='+customer_over_due_url+' target="_blank" '+(bwd_element.cus_over_due_pcs>0 ? 'style="color:red;"' :'')+'>'+(bwd_element.cus_over_due_wt+'/'+bwd_element.cus_over_due_pcs)+'</a></td>'+



    					'</tr>';



					});



				$("#customer_order_table tbody").append(table_value);



        		$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



//chart details



function get_salesDetails()



{



    $("div.overlay").css("display", "block");



    let from_date =  $('#payment_list1').text();



	let to_date   =  $('#payment_list2').text();



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_saleschart_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			 async: false,



			  success:function(saledata){



				var currencySymbol = "<i class='fa fa-inr'></i>";



				$('#sale_total_value').html(currencySymbol + saledata.sales_summary.total_sales_amount);



				$('#sales_estimated').html(saledata.sales_summary.total_est);



				$('#sales_returned').html(saledata.sales_summary.total_sales_ret);



				$('#sales_credit').html(saledata.sales_summary.total_credit_issued);



				$('#sales_green_tag').html(saledata.sales_summary.total_green_tag_amt);



				$('#sales_gross_profit').html(currencySymbol + saledata.sales_summary.total_profit_amt);







				var data = google.visualization.arrayToDataTable([



					  ['Type', 'No.of.Tags'],



					  ['Billed',  parseInt(saledata.sales_summary.total_est_billed)],



					  ['Non Billed',  parseInt(saledata.sales_summary.total_est) - parseInt(saledata.sales_summary.total_est_billed)],



					]);



					var options = {



					  pieHole: 0.5,



					  pieSliceTextStyle: {



						color: 'black',



					  },



					  legend: 'none',



					  title: 'Estimate Vs Billed',



					  is3D: true



					};



					var chart = new google.visualization.PieChart(document.getElementById('sales_converted'));



					chart.draw(data, options);











					var sales_branch_data = new google.visualization.DataTable();



					sales_branch_data.addColumn('string', 'Branch');



					sales_branch_data.addColumn('number', 'Sales');



					var row_sale_branch = [];



					$.each( saledata.sales_details.sales_by_branch, function( key, value ) {



						var branch_val = [];



						branch_val[0] = value.branch_name;



						branch_val[1] = parseFloat(value.amount);



						row_sale_branch[key] = branch_val;



					});







					sales_branch_data.addRows(row_sale_branch);







					var sales_branch_options = {



					  legend: 'none'



					};







					var sales_branch_chart = new google.charts.Bar(document.getElementById("sales_by_branch"));



					sales_branch_chart.draw(sales_branch_data, google.charts.Bar.convertOptions(sales_branch_options));



















					var sales_mode_data = new google.visualization.DataTable();



					sales_mode_data.addColumn('string', 'Mode');



					sales_mode_data.addColumn('number', 'Amount');



					var row_mode_coll = [];



					$.each( saledata.sales_details.sales_by_pay_mode, function( key, value ) {



						var coll_mod_val = [];



						coll_mod_val[0] = value.payment_mode;



						coll_mod_val[1] = parseFloat(value.amount);



						row_mode_coll[key] = coll_mod_val;



					});







					sales_mode_data.addRows(row_mode_coll);







					var sales_mod_options = {



					  legend: 'none'



					};







					var sales_mod_chart = new google.visualization.BarChart(document.getElementById("sales_pay_mode"));



					sales_mod_chart.draw(sales_mode_data, sales_mod_options);











					var sales_pro_data = new google.visualization.DataTable();



					sales_pro_data.addColumn('string', 'product');



					sales_pro_data.addColumn('number', 'Sales');



					var row_sale_pro = [];



				/*	$.each( saledata.sales_details.sales_by_product, function( key, value ) {



						var pro_val = [];



						pro_val[0] = value.pro_short_name;



						pro_val[1] = parseFloat(value.amount);



						row_sale_pro[key] = pro_val;



					});



					*/



					sales_pro_data.addRows(row_sale_pro);







					var sales_pro_options = {



					  legend: 'none',



					  colors: ['red','blue'],



					};







					var sales_pro_chart = new google.charts.Bar(document.getElementById("sales_by_product"));



					sales_pro_chart.draw(sales_pro_data, google.charts.Bar.convertOptions(sales_pro_options));







					var cus_data = google.visualization.arrayToDataTable([



					  ['Type', 'Visit'],



					  ['New',  parseInt(saledata.sales_summary.total_new_cus)],



					  ['Old',  parseInt(saledata.sales_summary.total_old_cus)],



					]);



					var cus_options = {



					  pieHole: 0.5,



					  pieSliceTextStyle: {



						color: 'black',



					  },



					  legend: 'none',



					  is3D: true,



					  width: '100%',



					  height: 200



					};



					var chart = new google.visualization.PieChart(document.getElementById('sales_customer_visit'));



					chart.draw(cus_data, cus_options);



















        		$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



//chart details



//stock chart details



function get_stockDetails()



{



    $("div.overlay").css("display", "block");



    let from_date =  $('#payment_list1').text();



	let to_date   =  $('#payment_list2').text();



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_stockchart_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			 async: false,



			  success:function(stockdata){



				var branch_stock_data = new google.visualization.DataTable();



					branch_stock_data.addColumn('string', 'Branch');



					branch_stock_data.addColumn('number', 'Opening');



					branch_stock_data.addColumn('number', 'Inward');



					branch_stock_data.addColumn('number', 'Sales');



					branch_stock_data.addColumn('number', 'Closing');



					var row_stock_coll = [];



			/*		$.each( stockdata.stock_by_branch, function( key, value ) {



						var stock_val = [];



						stock_val[0] = value.branch_name;



						stock_val[1] = parseInt(value.opening_pcs);



						stock_val[2] = parseInt(value.inw_pcs);



						stock_val[3] = parseInt(value.tot_sales_pcs);



						stock_val[4] = parseInt(value.available_pcs);



						row_stock_coll[key] = stock_val;



					});







					branch_stock_data.addRows(row_stock_coll);







					var brch_stock_options = {



					  legend: 'none'



					};







					var branch_stock_chart = new google.charts.Bar(document.getElementById("stock_by_branch"));



					branch_stock_chart.draw(branch_stock_data, google.charts.Bar.convertOptions(brch_stock_options));



					*/















			/*	var stock_pro_data = new google.visualization.DataTable();



					stock_pro_data.addColumn('string', 'Product');



					stock_pro_data.addColumn('number', 'Stock(Pcs)');



					var row_stock_pro = [];



					$.each( stockdata.stock_by_product, function( key, value ) {



						var pro_val = [];



						pro_val[0] = value.pro_short_name;



						pro_val[1] = parseInt(value.pcs);



						row_stock_pro[key] = pro_val;



					});







					stock_pro_data.addRows(row_stock_pro);







					var stock_pro_options = {



					  legend: 'none',



					  colors: ['red','blue'],



					};







					var stock_pro_chart = new google.charts.Bar(document.getElementById("stock_by_product"));



					stock_pro_chart.draw(stock_pro_data, google.charts.Bar.convertOptions(stock_pro_options));*/







				var bt_approval_data = new google.visualization.DataTable();



					bt_approval_data.addColumn('string', 'Branch');



					bt_approval_data.addColumn('number', 'Intransit(Pcs)');



					bt_approval_data.addColumn('number', 'Yet to Approve(Pcs)');



					var row_bt_app = [];



					$.each( stockdata.branch_transfer_details, function( key, value ) {



						var bt_appr_val = [];



						bt_appr_val[0] = value.branch_name;



						bt_appr_val[1] = parseInt(value.intransit_pcs);



						bt_appr_val[2] = parseInt(value.yet_to_approve_pcs);



						row_bt_app[key] = bt_appr_val;



					});







					bt_approval_data.addRows(row_bt_app);







					var bt_app_options = {



					  legend: 'none'



					};







					var bt_appr_chart = new google.visualization.BarChart(document.getElementById("bt_approval_pending"));



					bt_appr_chart.draw(bt_approval_data, bt_app_options);







        		$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



//stock chart details



function branch_transfer_details_dashboard_data(from_date,to_date)



{



    my_Date = new Date();



    $.ajax({



    url:base_url+ "index.php/admin_ret_dashboard/get_branch_transfer_details?nocache=" + my_Date.getUTCSeconds(),



    dataType:"JSON",



    data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



    type:"POST",



    cache:false,



    success:function(data){



        var download_pending=data.branch_transfer_details.download_pending;



        var approved_pending=data.branch_transfer_details.approved_pending;



        if(download_pending.length>0)



        {



            var trHtml='';



            var total_pcs=0;



            var total_gwt=0;



            var total_nwt=0;



            $.each(download_pending,function(key,items){



            total_pcs+=parseFloat(items.tot_pcs);



            total_gwt+=parseFloat(items.tot_gwt);



            var down_branch=$('#id_branch').val();



            var dashboard_bt_download=base_url+'index.php/admin_ret_reports/dashboard_branchtransfer/'+from_date+'/'+to_date+'/2/'+down_branch+'/'+items.product_id;



            trHtml+='<tr>'



            +'<td><a href='+dashboard_bt_download+' target="_blank">'+items.product_name+'</td>'



            +'<td>'+items.from_branch_name+'</td>'



            +'<td>'+items.to_branch_name+'</td>'



            +'<td>'+items.tot_pcs+'</td>'



            +'<td>'+items.tot_gwt+'</td>'



            +'</tr>';



            });



            trHtml+='<tr style="font-weight:bold;">'



            +'<td>TOTAL</td>'



            +'<td></td>'



            +'<td></td>'



            +'<td>'+parseFloat(total_pcs).toFixed(2)+'</td>'



            +'<td>'+parseFloat(total_gwt).toFixed(3)+'</td>'



            +'</tr>';



            $('#branch_transfer_table_download_pending > tbody').html(trHtml);



        }







        if(approved_pending.length>0)



        {



            var total_pcs=0;



            var total_gwt=0;



            var total_nwt=0;



            var trHtml='';







            $.each(approved_pending,function(key,items){



            var app_branch=$('#id_branch').val();



            var dashboard_bt_approval=base_url+'index.php/admin_ret_reports/dashboard_branchtransfer/'+from_date+'/'+to_date+'/1/'+app_branch+'/'+items.product_id;



            total_pcs+=parseFloat(items.tot_pcs);



            total_gwt+=parseFloat(items.tot_gwt);



            trHtml+='<tr>'



            +'<td><a href='+dashboard_bt_approval+' target="_blank">'+items.product_name+'</td>'



            +'<td>'+items.from_branch_name+'</td>'



            +'<td>'+items.to_branch_name+'</td>'



            +'<td>'+items.tot_pcs+'</td>'



            +'<td>'+items.tot_gwt+'</td>'



            +'</tr>';



            });



            trHtml+='<tr style="font-weight:bold;">'



            +'<td>TOTAL</td>'



            +'<td></td>'



            +'<td></td>'



            +'<td>'+parseFloat(total_pcs).toFixed(2)+'</td>'



            +'<td>'+parseFloat(total_gwt).toFixed(3)+'</td>'



            +'</tr>';



            $('#branch_transfer_table_approved_pending > tbody').html(trHtml);



        }



        $("div.overlay").css("display", "none");



    },



    error:function(error)



    {



    $("div.overlay").css("display", "none");



    }



    });







}



// Sales Transfer In-Transit Dashboard Data
function sales_transfer_intransit_dashboard_data()
{
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_dashboard/get_sales_transfer_in_transit?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        cache: false,
        success: function(data) {
            var in_transit = data.in_transit;
            var kpi = data.kpi;

            // Update count badge
            if (kpi && kpi.total_bills > 0) {
                $('#st_intransit_count').text(kpi.total_bills + ' Bills');
            } else {
                $('#st_intransit_count').text('0');
            }

            if (in_transit && in_transit.length > 0) {
                var trHtml = '';
                var total_value = 0;
                var total_items = 0;

                $.each(in_transit, function(key, item) {
                    total_value += parseFloat(item.tot_bill_amount || 0);
                    total_items += parseInt(item.item_count || 0);

                    // Color code days in transit
                    var days = parseInt(item.days_in_transit || 0);
                    var badgeClass = 'bg-green';
                    if (days > 3) {
                        badgeClass = 'bg-red';
                    } else if (days > 1) {
                        badgeClass = 'bg-yellow';
                    }

                    // Status label
                    var statusLabel = '<span class="label ' + badgeClass + '">' + days + ' day' + (days !== 1 ? 's' : '') + '</span>';

                    // Link to sales transfer list
                    var stLink = base_url + 'index.php/admin_ret_sales_transfer/sales_transfer/in_transit';

                    trHtml += '<tr>'
                        + '<td><a href="' + stLink + '" target="_blank">' + item.bill_no + '</a></td>'
                        + '<td>' + item.bill_date + '</td>'
                        + '<td>' + item.from_branch_name + '</td>'
                        + '<td>' + item.to_branch_name + '</td>'
                        + '<td style="text-align:center;">' + item.item_count + '</td>'
                        + '<td style="text-align:right;">' + parseFloat(item.tot_bill_amount).toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td>'
                        + '<td style="text-align:center;">' + statusLabel + '</td>'
                        + '<td>' + (item.bill_type == 13 ? 'Dispatch' : 'Download') + '</td>'
                        + '</tr>';
                });

                trHtml += '<tr style="font-weight:bold;">'
                    + '<td>TOTAL</td>'
                    + '<td></td>'
                    + '<td></td>'
                    + '<td></td>'
                    + '<td style="text-align:center;">' + total_items + '</td>'
                    + '<td style="text-align:right;">' + total_value.toLocaleString('en-IN', {minimumFractionDigits: 2}) + '</td>'
                    + '<td></td>'
                    + '<td></td>'
                    + '</tr>';

                $('#sales_transfer_intransit_table > tbody').html(trHtml);
            } else {
                $('#sales_transfer_intransit_table > tbody').html('<tr><td colspan="8" style="text-align:center; color:#999;">No in-transit sales transfers</td></tr>');
            }
        },
        error: function(error) {
            $('#sales_transfer_intransit_table > tbody').html('<tr><td colspan="8" style="text-align:center; color:#999;">Could not load data</td></tr>');
        }
    });
}


function get_approval_type()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_approval?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 type:"POST",



			 cache:false,



			  success:function(data){



						 console.log(data);



						 var dashboard_contract = base_url+'index.php/admin_ret_catalog/karigar_approval/list';



						 var bt_transfer = base_url+'index.php/admin_ret_brntransfer/branch_transfer/approval_list';



                    		 $("#contact_price").html("-");



							 $("#bt_approve").html("-");



							 $("#bt_download").html("-");



            					let price = data.status.contract_price_count;



								let branch= data.branch_status.branch_transfer_count;



								let download=data.download_status.branch_download_count;



								//alert(download);



            					$("#contact_price").html('<a href='+dashboard_contract +' target="_blank">'+price+ '</a>');



								$("#bt_approve").html('<a href='+bt_transfer +' target="_blank">'+branch+'</a>');



								$("#bt_download").html(download);







            				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}







function get_contract_pricing()



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



		      url:base_url+ "index.php/admin_ret_dashboard/get_contract_approval?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 type:"POST",



			 cache:false,



			  success:function(data){







				console.log(data);



				if(data.approval_status!=null && data.approval_status.length>0){



				$.each(data.approval_status, function (key,item){







				var dashboard_approved = base_url+'index.php/admin_ret_reports/dashboard_contractprice/list/1';



				var dashboard_yet_to_approve = base_url+'index.php/admin_ret_catalog/karigar_approval/list/0';



				var dashboard_rejected = base_url+'index.php/admin_ret_reports/dashboard_contractprice/list/2';



				var dashboard_hold = base_url+'index.php/admin_ret_catalog/karigar_approval/list/3';



					$("#contact_approved").html("-");



					$("#contract_yet_to_approve").html("-");



					$("#contract_hold").html("-");



					$("#contract_rejected").html("-");





					$("#contract_yet_to_approve").html('<a href='+dashboard_yet_to_approve +' target="_blank">'+item.yet_to_approve+'</a>');



					$("#contract_hold").html('<a href='+dashboard_hold +' target="_blank">'+item.hold+'</a>');



					$("#contact_approved").html('<a href='+dashboard_approved +' target="_blank">'+item.approved+ '</a>');



					$("#contract_rejected").html('<a href='+dashboard_rejected +' target="_blank">'+item.rejected+'</a>');



					});



				}







				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }







			});











}





function calculate_gross_profit(){



	var sale_wt = parseFloat((isNaN($('#sale_wt_h').val()) || $('#sale_wt_h').val() == '')  ? 0 : $('#sale_wt_h').val());



	var sale_amount = parseFloat((isNaN($('#sale_amount_h').val()) || $('#sale_amount_h').val() == '')  ? 0 : $('#sale_amount_h').val());



	var purchase_wt =  parseFloat((isNaN($('#purchase_wt_h').val()) || $('#purchase_wt_h').val() == '')  ? 0 : $('#purchase_wt_h').val());



	var purchase_purity =  parseFloat((isNaN($('#purchase_purity').val()) || $('#purchase_purity').val() == '')  ? 0 : $('#purchase_purity').val());



	var purchase_rate =  parseFloat((isNaN($('#purchase_rate').val()) || $('#purchase_rate').val() == '')  ? 0 : $('#purchase_rate').val());



	var discount_amount = parseFloat((isNaN($('#discount_amount_h').val()) || $('#discount_amount_h').val() == '')  ? 0 : $('#discount_amount_h').val());



	var discount_wt = parseFloat((isNaN($('#discount_wt_h').val()) || $('#discount_wt_h').val() == '')  ? 0 : $('#discount_wt_h').val());



	var sale_rate = 0;



	var purchase_amount = 0;



	var net_rate = 0;



	var net_amount = 0;



	var purchase_net =0;



	var gp_ratio = 0;



	var discount_rate = 0;



	var total_gross_profit =0;



	var total_profit_per = 0;



	sale_rate = sale_amount == 0 ? 0 : parseFloat( sale_amount / sale_wt ) ;



	purchase_amount = parseFloat( purchase_wt * purchase_purity * purchase_rate / 100 );



	net_rate =  parseFloat( sale_rate - purchase_rate * purchase_purity / 100 );



	net_amount = sale_amount - purchase_amount;



	purchase_net = purchase_wt * net_rate;



	gp_ratio = sale_amount == 0 ? 0 : parseFloat( net_amount / sale_amount * 100 ) ;



    discount_rate = discount_amount == 0 ? 0 : parseFloat( discount_amount  / discount_wt);



	total_gross_profit =  sale_amount == 0 ? 0 : parseFloat( discount_amount / sale_amount * 100 ) ;



	total_profit_per = gp_ratio - total_gross_profit;



	$('#purchase_amount_gross').val(money_format_india(parseFloat(purchase_amount).toFixed(2)));



	$('#sale_rate').val(money_format_india(parseFloat(sale_rate).toFixed(2)));



	$('#net_rate').val(money_format_india(parseFloat(net_rate).toFixed(2)));



	$('#net_amount').val(money_format_india(parseFloat(net_amount).toFixed(2)));



	$('#purchase_net').val(money_format_india(parseFloat(purchase_net).toFixed(2)));



	$('#discount_rate').val(money_format_india(parseFloat(discount_rate).toFixed(2)));



	//$('#purchase_amount').val(money_format_india(parseFloat(purchase_amount).toFixed(2)));



	$('#gp_ratio').val(parseFloat(gp_ratio).toFixed(2));



	$('#gp_ratio_per').val(parseFloat(total_gross_profit).toFixed(2));



	$('#purchase_net_h').val(purchase_net);



	$('#discount_rate_h').val(discount_rate);



	$('#net_rate_h').val(net_rate);



	$('#net_amount_h').val(net_amount);



	$('#purchase_amount_h').val(purchase_amount);



	$('#sale_rate_h').val(sale_rate);



	$('#total_gross_profit').val(money_format_india(parseFloat(total_profit_per).toFixed(2)));



	// $('#total_profit_per_h').val(total_profit_per);



	if(gp_ratio > 0 ){

		$("#gp_ratio").css("background-color","#39FF14;");



	}else{

		$("#gp_ratio").css("background-color","#fa3205");

	}



	if(discount_rate > 0 ){

		$("#gp_ratio_per").css("background-color","#39FF14;");



	}else{

		$("#gp_ratio_per").css("background-color","#fa3205");

	}



	if(total_profit_per > 0 ){

		$("#total_gross_profit").css("background-color","#39FF14;");



	}else{

		$("#total_gross_profit").css("background-color","#fa3205");

	}







}



$('#metal_select').on('select2:select', function(e) {



	if($('#metal_select').val()!='' && $('#metal_select').val()!=null)

	{

		get_gross_profit_report();

	}

	else

	{

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Metal ..'});

	}

});







$('#purchase_rate,#purchase_purity').on('change',function()

{

	calculate_gross_profit();

});



function get_gross_profit_report(){



	var my_Date = new Date();



	let from_date =  $('#payment_list1').text();



	let to_date   =  $('#payment_list2').text();



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard/gross_profit_report/ajax?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select').val()},

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{

			var list = data.list;



			$('#sale_wt').val(money_format_india(parseFloat(list.sale_wt).toFixed(3)));



			$('#sale_amount').val(money_format_india(parseFloat(list.sale_amount).toFixed(2)));



			$('#discount_wt').val(money_format_india(parseFloat(list.sale_wt).toFixed(3)));



			$('#purchase_wt').val(money_format_india(parseFloat(list.sale_wt).toFixed(3)));



			$('#discount_amount').val(money_format_india(parseFloat(list.discount_amount).toFixed(2)));



			$('#sale_wt_h').val(list.sale_wt);



			$('#sale_amount_h').val(list.sale_amount);



			$('#discount_wt_h').val(list.sale_wt);



			$('#purchase_wt_h').val(list.sale_wt);



			$('#discount_amount_h').val(list.discount_amount);





			calculate_gross_profit();



		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});

}





/* Sales Chart Start Here



Plugin Used : Google Chart



For referance : https://developers.google.com/chart/interactive/docs



*/





/*



IntoString Convert Number into Short Form Of Number



eg: 10,000 = 10K



*/













function money_formater(num){

    const formatter = new Intl.NumberFormat('en-IN',{style: 'currency',currency: 'INR', minimumFractionDigits: 0,maximumFractionDigits: 2,notation: 'compact',compactDisplay: 'long'});

     return formatter.format(num);

}



const intToString = num => {

    num = num.toString().replace(/[^0-9.]/g, '');

    if (num < 1000) {

        return num;

    }

    let si = [

      {v: 1E3, s: "K"},

      {v: 1E6, s: "M"},

      {v: 1E9, s: "B"},

      {v: 1E12, s: "T"},

      {v: 1E15, s: "P"},

      {v: 1E18, s: "E"}

      ];

    let index;

    for (index = si.length - 1; index > 0; index--) {

        if (num >= si[index].v) {

            break;

        }

    }

    return (num / si[index].v).toFixed(2).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, "$1") + si[index].s;

};





const gramsToOtherUnits = (grams) => {

    if (grams < 1000) {

        return grams + ' G';

    }



    let si = [

        { v: 1E3, s: 'KG' },

        { v: 1E6, s: 'Mg' },

        { v: 1E9, s: 'Tg' },

        { v: 1E12, s: 'Pg' },

        { v: 1E15, s: 'Eg' },

    ];



    let index;

    for (index = si.length - 1; index > 0; index--) {

        if (grams >= si[index].v) {

            break;

        }

    }



    return (grams / si[index].v).toFixed(2).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, '$1') + ' ' + si[index].s;

};





function get_sales_glance(){



	$("#sales_glance .loader-wrapper").css("display", "block");

	var my_Date = new Date();



	let from_date =  $('#dt_sales_glance').find('.payment_list3').text();



	let to_date   =  $('#dt_sales_glance').find('.payment_list4').text();



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_Sales_glance?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#sales_branch_glance').val(),'id_metal':$('#mt_sales_glance').val() },

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{



			var list = data.responsedata;



			$('#sale_amt').text(intToString(list.sale_amount));



			$('#sale_bills').text(intToString(list.sales_bill_count)+' Bills');



			$('#sale_net_wt').text(gramsToOtherUnits(list.sale_nwt));



			$('#sale_gross_wt').text(gramsToOtherUnits(list.sale_gwt));



			$('#return_amt').text(intToString(list.sales_return_amt));



			$('#return_qty').text(intToString(list.sales_return_count)+' Quantity');



			$('#sale_discount').text(intToString(list.sale_discount));



			$('#sale_bill_cnt').text(intToString(list.sales_bill_count));



			$('#dia_wt').text(parseFloat(list.sale_diawt).toFixed(3));



			$("#sales_glance .loader-wrapper").css("display", "none");

			//calculate_gross_profit();



		},



		error: function (error) {



			$("#sales_glance .loader-wrapper").css("display", "none");



		}



	});

}



function set_sale_dashboard(){





	// var date = new Date();

	// var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate()-29, 1);

	// var from_date=(firstDay.getFullYear()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getDate());

	// var to_date=(date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());





	var from_date = $('#payment_list1').text();

	var to_date = $('#payment_list2').text();

	var default_date = from_date+'/'+to_date ;



	$('.payment_list3').text(from_date);

	$('.payment_list4').text(to_date);

	$('.show_date').html(default_date);



	// branch = $('#id_branch').val();

	// $('.branch_filter').each(function () {

	// 	$(this).select2('val',branch);

	// });



	get_Financial_year();



	get_ActiveMetals_dashboard();



	$('.sale_dt_range').each(function () {

		var elementId = this.id; // Capture the ID before entering the callback function

        var that = $(this);

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

					startDate: new Date(from_date),

					endDate: new Date(to_date)

			},

			function (start, end) {

				console.log(elementId); // Use the captured element ID here



				that.find('.show_date').text(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));

				that.find('.payment_list3').text(start.format('YYYY-MM-DD'));

				that.find('.payment_list4').text(end.format('YYYY-MM-DD'));



				switch (elementId)

				{

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

				  case 'dt_break_even':

					get_FinancialStatus();

				  break;

				}



			}

		);

	});









	$('.branch_filter').on("change", function(e) {

	if(this.value!='')

	{

		console.log(this.id)

      switch (this.id)

	  {

		case 'sales_branch_glance':

			get_sales_glance();

		break;

		case 'branch_top_selling':

			get_top_selling();

		break;

		case 'branch_top_sellers':

			get_top_sellers();

		break;

		case 'branch_month_sales':

			get_monthly_sales_details();

		break;

		case 'branch_branch_compare':

			get_branch_comparison_details();

		break;

		case 'branch_store_sales':

			get_store_wise_sales();

		break;

		case 'branch_branch_avg_va':

			get_branch_avg_va_details();

		break;

		case 'branch_product_sales':

			get_product_sales();

		break;

		case 'branch_emp_sales':

			get_employee_sales();

		break;

		case 'branch_section_sales':

			get_section_sales();

		break;

		case 'branch_karigar_sales':

			get_karigar_sales();

		break;

		case 'branch_custome_wise_sale':

			get_custome_wise_sale();

		break;

		case 'branch_break_even':

			get_FinancialStatus();

		break;

	  }

	}

    });



	$('.metal_filter').on("change", function(e) {

		if(this.value!='')

		{

			console.log(this.id)

		  switch (this.id)

		  {

			case 'mt_sales_glance':

				get_sales_glance();

			break;

			case 'mt_top_selling':

				get_top_selling();

			break;

			case 'mt_top_sellers':

				get_top_sellers();

			break;

			case 'mt_month_sales':

				get_monthly_sales_details();

			break;

			case 'mt_branch_compare':

				get_branch_comparison_details();

			break;

			case 'mt_store_sales':

				get_store_wise_sales();

			break;

			case 'mt_branch_avg_va':

				get_branch_avg_va_details();

			break;

			case 'mt_product_sales':

				get_product_sales();

			break;

			case 'mt_emp_sales':

				get_employee_sales();

			break;

			case 'mt_section_sales':

				get_section_sales();

			break;

			case 'mt_karigar_sales':

				get_karigar_sales();

			break;

			case 'mt_custome_wise_sale':

				get_custome_wise_sale();

			break;

			case 'mt_break_even':

				get_FinancialStatus();

			break;

		  }

		}

		});



	google.charts.load('current', {'packages':['corechart']});

	// google.charts.load('current', {'packages':['bar']});

	google.charts.setOnLoadCallback(get_top_selling);

	google.charts.setOnLoadCallback(get_top_sellers);

	google.charts.setOnLoadCallback(get_monthly_sales_details);

	google.charts.setOnLoadCallback(get_branch_comparison_details);

	google.charts.setOnLoadCallback(get_branch_avg_va_details);

	google.charts.setOnLoadCallback(get_product_sales);

	google.charts.setOnLoadCallback(get_section_sales);

	google.charts.setOnLoadCallback(get_employee_sales);

	google.charts.setOnLoadCallback(get_karigar_sales);

	google.charts.setOnLoadCallback(get_custome_wise_sale);

	get_sales_glance();

	get_store_wise_sales();

	get_FinancialStatus();

	//get_top_selling();



	$('#group_by').select2({

        placeholder:"Group By",

        allowClear:true

    });



    $('#group_by').select2('val','');



    $('#group_by').on("change", function(e) {

        if(this.value!='')

        {

            get_branch_avg_va_details();

        }

    });





}





function get_ActiveMetals_dashboard() {



	$(".metal_filter option").remove();

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_catalog/ret_product/active_metal',

		dataType: 'json',

		success: function (data) {

			var id = $("#metal").val();

			$(".metal_filter").append(

				$("<option></option>")

					.attr("value", 0)

					.text('All')

			);

			$.each(data, function (key, item) {

				$('.metal_filter').append(

					$("<option></option>")

						.attr("value", item.id_metal)

						.text(item.metal)

				);

			});

			$(".metal_filter").select2(

				{

					placeholder: "Select Metal",

					allowClear: true

				});



			$('.metal_filter').select2("val", (id != '' && id > 0 ? id : ''));

		}

	});

}





function get_FinancialStatus(){



	let from_date =  $('#dt_break_even').find('.payment_list3').text();



	let to_date   =  $('#dt_break_even').find('.payment_list4').text();



	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_FinancialStatus?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#branch_break_even').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				value = data.response_data;



				break_even_config = {

					type: 'gauge',

					data: {

					  //labels: ['Success', 'Warning', 'Error'],

					  datasets: [{

						data: [35,70,100],

						value: value,

						backgroundColor: ['red', 'yellow', 'green'],

						borderWidth: 2

					  }]

					},

					options: {

					  responsive: true,

					  title: {

						display: true,

						text: 'Gauge chart'

					  },

					  layout: {

						padding: {

						  bottom: 30

						}

					  },

					  needle: {

						// Needle circle radius as the percentage of the chart area width

						radiusPercentage: 2,

						// Needle width as the percentage of the chart area width

						widthPercentage: 3.2,

						// Needle length as the percentage of the interval between inner radius (0%) and outer radius (100%) of the arc

						lengthPercentage: 80,

						// The color of the needle

						color: 'rgba(0, 0, 0, 1)'

					  },

					  valueLabel: {

						formatter: Math.round

					  }

					}

				  };



				  var ctx = document.getElementById('break_even').getContext('2d');

				  window.myGauge = new Chart(ctx, break_even_config);



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}









function get_top_selling(){



	var my_Date = new Date();



	let from_date =  $('#dt_top_selling').find('.payment_list3').text();



	let to_date   =  $('#dt_top_selling').find('.payment_list4').text();





	$("#sales_top_selling .loader-wrapper").css("display", "block");





	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_top_selling?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_top_selling').val(),'id_metal':$('#mt_top_selling').val() },

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{

			//Get Response as [['Chain',300],['Ring',300]]

            var lable_data = data.data;



			var chartdata = data.responsedata;



			var data = new google.visualization.DataTable();



			//Set Chart Column With type And name

			data.addColumn('string', 'Product');

			data.addColumn('number', 'Bill Count');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0,



						   colors:colorCodes

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('top_selling'));

			chart.draw(data, options);





			sum_tot=0;

			$.each(chartdata, function (index, element)

			{

			sum_tot+=element[1];

			});



			label='';



            // Labels Button

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+gramsToOtherUnits(lable_data[index].wt)+' '+lable_data[index].pcs+' Pcs</button>'

			});



			$('#top_selling_lable').html(label);



			$("#sales_top_selling .loader-wrapper").css("display", "none");

		},



		error: function (error) {



			$("div.overlay").css("display", "none");

			$("#sales_top_selling .loader-wrapper").css("display", "none");

		}



	});

}





function get_top_sellers(){



	var my_Date = new Date();



	let from_date =  $('#dt_top_sellers').find('.payment_list3').text();



	let to_date   =  $('#dt_top_sellers').find('.payment_list4').text();



	$("#sales_top_sellers .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_top_sellers?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_top_sellers').val(),'id_metal':$('#mt_top_sellers').val()  },

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{

			var chartdata = data.responsedata;

			var lable_data = data.data;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Karigar Name');

			data.addColumn('number', 'Bill Count');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0,

							legend: {

								position: 'labeled'

								},

							colors:colorCodes,

						   };



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('top_sellers'));

			chart.draw(data, options);



			sum_tot=0;

			$.each(chartdata, function (index, element)

			{

			sum_tot+=element[1];

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+gramsToOtherUnits(lable_data[index].wt)+' '+lable_data[index].pcs+' PCs</button>';

				//label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#top_sellers_lable').html(label);

			$("#sales_top_sellers .loader-wrapper").css("display", "none");

		},



		error: function (error) {



			$("div.overlay").css("display", "none");

			$("#sales_top_sellers .loader-wrapper").css("display", "none");

		}



	});

}



function get_monthly_sales_details(){



	var my_Date = new Date();



	// let from_date =  $('#dt_month_sales').find('.payment_list3').text();



	// let to_date   =  $('#dt_month_sales').find('.payment_list4').text();  'from_date': from_date, 'to_date': to_date,

    $("#sales_month_on_month .loader-wrapper").css("display", "block");

	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_monthly_sales?nocache=" + my_Date.getUTCSeconds(),

		data: { 'fy_code':$('#finalial_year_month_sales').val(), 'id_branch':$('#branch_month_sales').val(),'id_metal':$('#mt_month_sales').val() },

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{

			var list = data.responsedata.data;



			var branch = data.responsedata.branch;



			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Month');

			$.each(branch, function (index, element)

			{

				data.addColumn('number', element.short_name);

			});





			data.addRows(list);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0,

						   title: 'Month On Month Sale Comparision ',

						   hAxis: {title: 'Months'},

						   vAxis:{

							title: 'Net Wt In Gram ',

							format: 'short'

						   },

							legend: {

								position: 'labeled'

								},

							colors:colorCodes,

                            };



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.LineChart(document.getElementById('month_on_month'));

			chart.draw(data, options);

            $("#sales_month_on_month .loader-wrapper").css("display", "none");



		},



		error: function (error) {



			 $("#sales_month_on_month .loader-wrapper").css("display", "none");



		}



	});

}

function get_Financial_year()

{

    $('.financial_year option').remove();

    $.ajax({

        type: 'GET',

        url:  base_url+'index.php/admin_ret_dashboard_api/get_financial_year',

        dataType: 'json',

        success: function(data) {

			set_default='';

        var id_village='';

        $.each(data.responsedata, function (key, val) {

		if(val.status == '1'){

			set_default = val.fin_year_code;

		}

	//	set_default=val.status == '1'&& set_default != '1'? val.fin_year_code	:'';

        $('.financial_year').append(

        $("<option></option>")

        .attr("value", val.fin_year_code)

        .text(val.fin_year_name)

        );



        $('.financial_year').select2({

            placeholder:"Select Year",

            allowClear:true

        });

        $(".financial_year").select2("val",set_default);

        });



            $('.overlay').css('display','none');

        },

        error:function(error)

        {

            $("div.overlay").css("display", "none");

        }

    });

}





$('.financial_year').on('change',function(){



	if(this.value != ''){

		get_monthly_sales_details();



	}

})





function get_branch_comparison_details(){



	var my_Date = new Date();



	let from_date =  $('#dt_branch_compare').find('.payment_list3').text();



	let to_date   =  $('#dt_branch_compare').find('.payment_list4').text();



	$("#sales_branch_wise_compare .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_branch_comparison?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_branch_compare').val(),'id_metal':$('#mt_branch_compare').val() },

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{

			var list = data.responsedata;



			var branch = data.responsedata.branch;



			var colour = ['#45cafc', '#303f9f', '#9b72e8','#45cffc', '#306f9f', '#9b76e8'];



			var chartdata =[];



			$.each(list, function (index, element)

			{

				chartdata.push([element.branch_name,parseFloat(element.branch_sales),colorCodes[index]])

			});



			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Branch');

			data.addColumn('number', 'Sales');

			data.addColumn({ type: 'string',role: 'style' });



			data.addRows(chartdata);



			var options = {

				legend: { position: 'none' },

			  title: '',

			  hAxis: {

				title: 'Branch',

			  },

			  vAxis: {

				title: 'Sales Amount',

				format: 'short'

			  },

			  colors:colorCodes

			};



             label='';



			$.each(chartdata, function (index, element)

			{

			label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+element[2]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#branch_comparison_lable').html(label);



			// Set chart options

			// var options = {

			// 	           sliceVisibilityThreshold: 0,

			// 				legend: {

			// 					position: 'labeled'

			// 					},

			// 			   'width':500,

			// 			   'height':330};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.ColumnChart(document.getElementById('branch_comparison'));

			chart.draw(data, options);



         $("#sales_branch_wise_compare .loader-wrapper").css("display", "none");





		},



		error: function (error) {



			$("div.overlay").css("display", "none");



			$("#sales_branch_wise_compare .loader-wrapper").css("display", "none");



		}



	});

}



function getActiveSections(){

    my_Date = new Date();

    $.ajax({

        type: 'GET',

        url: base_url+"index.php/admin_ret_catalog/get_section?nocache=" + my_Date.getUTCSeconds(),

        dataType:'json',

        success:function(data){

            $("#select_to_section option").remove();

            $("#select_frm_section option").remove();

            $.each(data,function(key, item){

                $("#select_frm_section,#select_to_section").append(

                    $("<option></option>")

                    .attr("value",item.id_section)

                    .text(item.section_name)

                );

            });

				$('#select_frm_section').select2({

				placeholder: 'From Section',

				allowClear: true

			});

			$('#select_to_section').select2({

				placeholder: 'To Section',

				allowClear: true

			})

			$("#select_frm_section").select2("val",'');

			$("#select_to_section").select2("val",'');

            $(".overlay").css("display","none");

        }

    })

}





function get_store_wise_sales() {



	let from_date =  $('#dt_store_sales').find('.payment_list3').text();

	let to_date   =  $('#dt_store_sales').find('.payment_list4').text();

	my_Date = new Date();

	$("#sales_store_wise_sales .loader-wrapper").css("display", "block");

	$.ajax({

		type: 'POST',

		url: base_url + 'index.php/admin_ret_dashboard_api/get_store_sales',

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch': ($('#branch_store_sales').val() != '' && $('#branch_store_sales').val() != undefined ? $('#branch_store_sales').val() : $("#branch_select").val()),'id_metal':$('#mt_store_sales').val() },

		dataType: 'JSON',

		success: function (data) {

			console.log(data)

			var list = data.responsedata;

			const totalSales = list.reduce((sum, current) => {

				const sales = parseFloat(current.branch_sales); // Convert to float



				if (!isNaN(sales)) {

				  sum += sales;

				}



				return sum;

			  }, 0);

			var oTable = $('#store_wise_sales').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#store_wise_sales').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					"bSort": true,

				    "order": [[ 2, "desc" ]],

					paging: false,

					"scrollX": false,

					scrollCollapse: true,

					scrollY: '50vh',

					columnDefs: [

						{

							targets: [1,2],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "branch_name" },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.branch_sales/totalSales * 100).toFixed(2);

							return '<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-info  active" role="progressbar" style="width: '+percentage+'%" aria-valuenow="'+percentage+'" aria-valuemin="0" aria-valuemax="100"><span style="color: #444 !important;">'+percentage+'%</div></div>';

						  }},

						{ "mDataProp": function(row){

							return money_format_india(row.branch_sales)

						} },

					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_amount = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						$( api.column(0).footer() ).html(count+' Store');

						$( api.column(2).footer() ).html(money_format_india(parseFloat(total_amount).toFixed(2)));





					}

				});

			}



			$("#sales_store_wise_sales .loader-wrapper").css("display", "none");

		},

		error: function (error) {

			$("#sales_store_wise_sales .loader-wrapper").css("display", "none");

		}

	});

}





function get_branch_avg_va_details(){



	var my_Date = new Date();



	let from_date =  $('#dt_branch_avg_va').find('.payment_list3').text();



	let to_date   =  $('#dt_branch_avg_va').find('.payment_list4').text();



	$("#sales_avg_branch .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_branch_avg_va?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_branch_avg_va').val(),'id_metal':$('#mt_branch_avg_va').val(),'group_by':$('#group_by').val() },

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{

			var chartdata = data.responsedata;



			// var chartdata =[];



			// $.each(list, function (index, element)

			// {

			// 	chartdata.push([element.branch_name,parseFloat(element.branch_sales),colorCodes[index]])

			// });



			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Branch');

			data.addColumn('number', 'Avg Wastage');





			data.addRows(chartdata);



			var options = {

				legend: { position: 'none' },

			  title: '',

			  hAxis: {

				title: 'Branch',

			  },

			  vAxis: {

				title: 'Branch Avg Wastage',

				format: 'short'

			  },

			};



             label='';



			// $.each(chartdata, function (index, element)

			// {

			// label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+element[2]+';" >'+element[0]+' - '+element[1]+'% </button>'

			// });



			// $('#branch_avg_va_lable').html(label);



			// Set chart options

			// var options = {

			// 	           sliceVisibilityThreshold: 0,

			// 				legend: {

			// 					position: 'labeled'

			// 					},

			// 			   'width':500,

			// 			   'height':330};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.ColumnChart(document.getElementById('branch_avg_va'));

			chart.draw(data, options);



        	$("#sales_avg_branch .loader-wrapper").css("display", "none");





		},



		error: function (error) {





         	$("#sales_avg_branch .loader-wrapper").css("display", "none");

		}



	});

}



function get_custome_wise_sale(){



	var my_Date = new Date();



	let from_date =  $('#dt_custome_wise_sale').find('.payment_list3').text();



	let to_date   =  $('#dt_custome_wise_sale').find('.payment_list4').text();



	$("#sales_customer_wise_sale .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_custome_wise_sale?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_custome_wise_sale').val(),'id_metal':$('#mt_custome_wise_sale').val() },

		dataType: "JSON",

		type: "POST",

		success: function (data)

		{

			var chartdata = data.responsedata;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Customer Type');

			data.addColumn('number', 'Bill Count');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0,

							legend: {

								position: 'labeled'

								},

							colors:colorCodes,

						   };



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('custome_wise_sale'));

			chart.draw(data, options);



			sum_tot=0;

			$.each(chartdata, function (index, element)

			{

			sum_tot+=element[1];

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#custome_wise_sale_lable').html(label);

			$("#sales_customer_wise_sale .loader-wrapper").css("display", "none");

		},



		error: function (error) {



			$("div.overlay").css("display", "none");

			$("#sales_customer_wise_sale .loader-wrapper").css("display", "none");

		}



	});

}







function get_product_sales(){



	var my_Date = new Date();



	let from_date =  $('#dt_product_sales').find('.payment_list3').text();



	let to_date   =  $('#dt_product_sales').find('.payment_list4').text();



     $("#sales_product_wise_sale .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_product_sales?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_product_sales').val(),'id_metal':$('#mt_product_sales').val() },

		dataType: "JSON",

		type: "POST",

		success: function (responsedata)

		{

			var chartdata = responsedata.chartdata;

			console.log('char',chartdata);

			var list = responsedata.data;

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Product');

			data.addColumn('number', 'Sale Value');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0.00,

						   'width':550,

						   'height':330,

						   colors:colorCodes,

						   title: 'Top Ten Products',

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('product_sale'));

			chart.draw(data, options);



			totalSales_pro=0;

			$.each(list, function (index, element)

			{

				totalSales_pro+= parseFloat(element.product_sales);

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#product_sale_lable').html(label);





			//var list = responsedata.data;

			var oTable = $('#product_wise_sales').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#product_wise_sales').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"scrollX": false,

					"bSort": true,

					"order": [[ 2, "desc" ]],

					"columnDefs": [

						{

							targets: [1,2],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "product_name" },

						{ "mDataProp": function(row){

							return money_format_india(row.product_sales)

						} },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.product_sales/totalSales_pro * 100).toFixed(2);

							return percentage;

						  }},



					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_amount = api

							.column(1)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );

						total_sale_percentage = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						//console.log('haidb',total_sale_percentage,Math.round(total_sale_percentage));

						$( api.column(0).footer() ).html(count+' Product');

						$( api.column(1).footer() ).html(money_format_india(parseFloat(total_amount).toFixed(2)));

						$( api.column(2).footer() ).html(parseFloat(Math.round(total_sale_percentage)).toFixed(2)+' %');





					}

				});

			}



        	$("#sales_product_wise_sale .loader-wrapper").css("display", "none");



		},



		error: function (error) {



				$("#sales_product_wise_sale .loader-wrapper").css("display", "none");



		}



	});

}







function get_section_sales(){



	var my_Date = new Date();



	let from_date =  $('#dt_section_sales').find('.payment_list3').text();



	let to_date   =  $('#dt_section_sales').find('.payment_list4').text();



		$("#sales_section_wise_sale .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_section_sales?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_section_sales').val(),'id_metal':$('#mt_section_sales').val() },

		dataType: "JSON",

		type: "POST",

		success: function (responsedata)

		{

			var chartdata = responsedata.chartdata;

			var list = responsedata.data;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Section');

			data.addColumn('number', 'Sale Value');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0.01,

						   'width':550,

						   'height':330,

						   colors:colorCodes,

						   title: 'Top Ten Section',

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('section_sale'));

			chart.draw(data, options);



			var totalSales_sec=0;

			$.each(list, function (index, element)

			{

				totalSales_sec+=parseFloat(element.section_sales);

			});



			label='';

			//var list = responsedata.data;

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#section_sale_lable').html(label);





			//var list = responsedata.data;

			var oTable = $('#section_wise_sales').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#section_wise_sales').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"scrollX": false,

					"order": [[ 2, "desc" ]],

					"bSort": true,

						"columnDefs": [

						{

							targets: [1,2],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "section_name" },

						{ "mDataProp": function(row){

							return money_format_india(row.section_sales)

						} },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.section_sales/totalSales_sec * 100).toFixed(2);

							return percentage;

						  }},



					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_amount = api

							.column(1)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );

						total_sale_percentage = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						console.log('haidb',total_sale_percentage,Math.round(total_sale_percentage));

						$( api.column(0).footer() ).html(count+' Section ');

						$( api.column(1).footer() ).html(money_format_india(parseFloat(total_amount).toFixed(2)));

						$( api.column(2).footer() ).html(parseFloat(Math.round(total_sale_percentage)).toFixed(2)+' %');





					}

				});

			}



         $("#sales_section_wise_sale .loader-wrapper").css("display", "none");



		},



		error: function (error) {



			$("#sales_section_wise_sale .loader-wrapper").css("display", "none");



		}



	});

}







function get_employee_sales(){



	var my_Date = new Date();



	let from_date =  $('#dt_emp_sales').find('.payment_list3').text();



	let to_date   =  $('#dt_emp_sales').find('.payment_list4').text();



		$("#sales_employee_wise_sale .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_employee_sales?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_emp_sales').val(),'id_metal':$('#mt_emp_sales').val() },

		dataType: "JSON",

		type: "POST",

		success: function (responsedata)

		{

			var chartdata = responsedata.chartdata;

			var list = responsedata.data;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Product');

			data.addColumn('number', 'Sale Value');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0.01,

						   'width':550,

						   'height':330,

						   colors:colorCodes

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('emp_sale'));

			chart.draw(data, options);



			totalSales_emp=0;

			$.each(list, function (index, element)

			{

				totalSales_emp+=parseFloat(element.emp_sales);

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#emp_sale_lable').html(label);





			var list = responsedata.data;

			var oTable = $('#emp_wise_sales').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#emp_wise_sales').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"order": [[ 2, "desc" ]],

					"bSort": true,

					"scrollX": false,

					"columnDefs": [

						{

							targets: [1,2],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "emp_name" },

						{ "mDataProp": function(row){

							return money_format_india(row.emp_sales)

						} },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.emp_sales/totalSales_emp * 100).toFixed(2);

							return percentage;

						  }},



					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_amount = api

							.column(1)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );

						total_sale_percentage = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						console.log('haidb',total_sale_percentage,Math.round(total_sale_percentage));

						$( api.column(0).footer() ).html(count+' Employee');

						$( api.column(1).footer() ).html(money_format_india(parseFloat(total_amount).toFixed(2)));

						$( api.column(2).footer() ).html(parseFloat(Math.round(total_sale_percentage)).toFixed(2)+' %');





					}

				});

			}



             $("#sales_employee_wise_sale .loader-wrapper").css("display", "none");



		},



		error: function (error) {



			$("#sales_employee_wise_sale .loader-wrapper").css("display", "none");



		}



	});

}





function get_karigar_sales(){



	var my_Date = new Date();



	let from_date =  $('#dt_karigar_sales').find('.payment_list3').text();



	let to_date   =  $('#dt_karigar_sales').find('.payment_list4').text();



	$("#sales_karigar_wise_sale .loader-wrapper").css("display", "block");



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_karigar_sales?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_karigar_sales').val(),'id_metal':$('#mt_karigar_sales').val(), },

		dataType: "JSON",

		type: "POST",

		success: function (responsedata)

		{

			var chartdata = responsedata.chartdata;

			var list = responsedata.data;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Karigar');

			data.addColumn('number', 'Sale Value');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0.01,

						   'width':550,

						   'height':330,

						   colors:colorCodes

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('karigar_sale'));

			chart.draw(data, options);



			totalSales_kar=0;

			$.each(list, function (index, element)

			{

				totalSales_kar+=parseFloat(element.karigar_sales);

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#karigar_sale_lable').html(label);





			//var list = responsedata.data;

			var oTable = $('#karigar_wise_sales').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#karigar_wise_sales').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"order": [[ 1, "desc" ]],

					"bSort": true,

					"scrollX": false,

					"columnDefs": [

						{

							targets: [1,2],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "karigar_name" },

						{ "mDataProp": function(row){

							return money_format_india(row.karigar_sales)

						} },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.karigar_sales/totalSales_kar * 100).toFixed(2);

							return percentage;

						  }},



					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_amount = api

							.column(1)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );

						total_sale_percentage = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						console.log('haidb',total_sale_percentage,Math.round(total_sale_percentage));

						$( api.column(0).footer() ).html(count+' Karigar');

						$( api.column(1).footer() ).html(money_format_india(parseFloat(total_amount).toFixed(2)));

						$( api.column(2).footer() ).html(parseFloat(Math.round(total_sale_percentage)).toFixed(2)+' %');





					}

				});

			}



         	$("#sales_karigar_wise_sale .loader-wrapper").css("display", "none");



		},



		error: function (error) {



			$("#sales_karigar_wise_sale .loader-wrapper").css("display", "none");



		}



	});

}













//STOCK CHART



function set_stock_dashboard(){





	var date = new Date();

	var firstDay = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 240, 1);

	var from_date=(firstDay.getFullYear()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getDate());

	var to_date=(date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());

	var default_date =(firstDay.getDate()+"/"+(firstDay.getMonth() + 1)+"/"+firstDay.getFullYear())+' - '+(date.getDate()+"/"+(date.getMonth() + 1)+"/"+date.getFullYear())



	$('.payment_list3').text(from_date);

	$('.payment_list4').text(to_date);

	$('.show_date').html(default_date);



	get_Financial_year();



	get_ActiveMetals_dashboard();



	$('#mt_karigar_groupby').select2(

		{

			placeholder: "Select Group By",

			allowClear: true

		});

	$('#mt_karigar_groupby').select2("val", "1");



	get_ActiveKarigars();



	$('.stock_dt_range').each(function () {

		var elementId = this.id; // Capture the ID before entering the callback function

        var that = $(this);

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

					startDate: moment(),

					endDate: moment()

			},

			function (start, end) {

				console.log(elementId); // Use the captured element ID here



				that.find('.show_date').text(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));

				that.find('.payment_list3').text(start.format('YYYY-MM-DD'));

				that.find('.payment_list4').text(end.format('YYYY-MM-DD'));



				switch (elementId)

				{

				  case 'dt_product_stock':

					get_product_stock();

				  break;

				  case 'dt_section_stock':

					get_section_stock();

				  break;

				  case 'dt_karigar_stock':

					get_karigar_stock();

				  break;

				}



			}

		);

	});







	$('.branch_filter').on("change", function(e) {

		if(this.value!='')

		{

			console.log(this.id)

		switch (this.id)

		{

			case 'branch_product_stock':

				get_product_stock();

			break;

			case 'branch_section_stock':

				get_section_stock();

			break;

			case 'branch_karigar_stock':

				get_karigar_stock();

			break;



		}

		}

	});



	$('.metal_filter').on("change", function(e) {

		if(this.value!='')

		{

			console.log(this.id)

		  switch (this.id)

		  {

			case 'mt_product_stock':

				get_product_stock();

			break;

			case 'mt_section_stock':

				get_section_stock();

			break;

			case 'mt_karigar_stock':

				get_karigar_stock();

			break;



		  }

		}

		});





	google.charts.load('current', {'packages':['corechart']});



	google.charts.setOnLoadCallback(get_product_stock);

	google.charts.setOnLoadCallback(get_section_stock);

	google.charts.setOnLoadCallback(get_karigar_stock);



}







function get_product_stock(){





	$("#stock_product .loader-wrapper").css("display", "block");



	var my_Date = new Date();



	let from_date =  $('#dt_product_stock').find('.payment_list3').text();



	let to_date   =  $('#dt_product_stock').find('.payment_list4').text();



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_product_stock?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_product_stock').val(),'id_metal':$('#mt_product_stock').val() },

		dataType: "JSON",

		type: "POST",

		success: function (responsedata)

		{

			var chartdata = responsedata.chartdata;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Product');

			data.addColumn('number', 'Sale Value');

			data.addRows(chartdata);

			var list = responsedata.data;



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0,

						   title: ' Top Five Stocks',

						   colors:colorCodes

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('product_stock'));

			chart.draw(data, options);



			product_tot_stock=0;

			$.each(list, function (index, element)

			{

				product_tot_stock+=parseFloat(element.stock_wt);

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#product_stock_lable').html(label);





			//var list = responsedata.data;

			var oTable = $('#product_wise_stock').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#product_wise_stock').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"bSort": false,

					"columnDefs": [

						{

							targets: [1,2,3,4,5],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "product_name" },

						{ "mDataProp": "stock_pcs" },

						{ "mDataProp": function(row){

							return money_format_india(row.stock_wt)

						} },

						{ "mDataProp": function(row){

							return money_format_india(row.net_wt)

						} },

						{ "mDataProp": function(row){

							return money_format_india(row.dia_wt)

						} },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.stock_wt/product_tot_stock * 100).toFixed(2);

							return percentage;

						  }},



					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_pcs = api

							.column(1)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );

						total_net_wt = api

							.column(3)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						total_wt = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



							tot_dia = api

							.column(4)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



							total_stock_percentage = api

							.column(5)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						// console.log('haidb',total_sale_percentage,Math.round(total_sale_percentage));

						$( api.column(0).footer() ).html(count+' Product');

						$( api.column(1).footer() ).html(money_format_india(parseFloat(total_pcs).toFixed(0)));

						$( api.column(2).footer() ).html(money_format_india(parseFloat(total_wt).toFixed(3)));

						$( api.column(3).footer() ).html(money_format_india(parseFloat(total_net_wt).toFixed(3)));

						$( api.column(4).footer() ).html(money_format_india(parseFloat(tot_dia).toFixed(3)));

						$( api.column(5).footer() ).html(parseFloat(Math.round(total_stock_percentage)).toFixed(2)+' %');





					}

				});

			}



			$("#stock_product .loader-wrapper").css("display", "none");



		},



		error: function (error) {



			$("#stock_product .loader-wrapper").css("display", "none");



		}



	});

}







function get_section_stock(){



	var my_Date = new Date();



	let from_date =  $('#dt_section_stock').find('.payment_list3').text();



	let to_date   =  $('#dt_section_stock').find('.payment_list4').text();



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_section_stock?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_section_stock').val(),'id_metal':$('#mt_section_stock').val() },

		dataType: "JSON",

		type: "POST",

		success: function (responsedata)

		{

			var chartdata = responsedata.chartdata;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Section');

			data.addColumn('number', 'Stock');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0,

						   title: ' Top Five Stocks',

						   colors:colorCodes

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('section_stock'));

			chart.draw(data, options);

            var list = responsedata.data;

			section_stock_wt=0;

			$.each(list, function (index, element)

			{

				section_stock_wt+=parseFloat(element.stock_wt);

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#section_stock_lable').html(label);







			var oTable = $('#section_wise_stock').DataTable();

			oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#section_wise_stock').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"bSort": false,

					"columnDefs": [

						{

							targets: [1,2,3,4,5],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "section_name" },

						{ "mDataProp": "stock_pcs" },

						{ "mDataProp": function(row){

							return money_format_india(row.stock_wt)

						} },

						{ "mDataProp": function(row){

							return money_format_india(row.net_wt)

						} },

						{ "mDataProp": function(row){

							return money_format_india(row.dia_wt)

						} },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.stock_wt/section_stock_wt * 100).toFixed(2);

							return percentage;

						  }},



					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_pcs = api

							.column(1)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );

						total_net_wt = api

							.column(3)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						total_wt = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



							tot_dia = api

							.column(4)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



							total_stock_percentage = api

							.column(5)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						// console.log('haidb',total_sale_percentage,Math.round(total_sale_percentage));

						$( api.column(0).footer() ).html(count+' Product');

						$( api.column(1).footer() ).html(money_format_india(parseFloat(total_pcs).toFixed(0)));

						$( api.column(2).footer() ).html(money_format_india(parseFloat(total_wt).toFixed(3)));

						$( api.column(3).footer() ).html(money_format_india(parseFloat(total_net_wt).toFixed(3)));

						$( api.column(4).footer() ).html(money_format_india(parseFloat(tot_dia).toFixed(3)));

						$( api.column(5).footer() ).html(parseFloat(Math.round(total_stock_percentage)).toFixed(2)+' %');





					}

				});

			}







		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});

}







function get_karigar_stock(){



	var my_Date = new Date();



	let from_date =  $('#dt_karigar_stock').find('.payment_list3').text();



	let to_date   =  $('#dt_karigar_stock').find('.payment_list4').text();



	$.ajax({

		url: base_url + "index.php/admin_ret_dashboard_api/get_karigar_stock?nocache=" + my_Date.getUTCSeconds(),

		data: { 'from_date': from_date, 'to_date': to_date, 'id_branch':$('#branch_karigar_stock').val(),'id_metal':$('#mt_karigar_stock').val(),'id_karigar':$('#id_karigar_stock').val(),'group_by':$('#mt_karigar_groupby').val(), },

		dataType: "JSON",

		type: "POST",

		success: function (responsedata)

		{

			var chartdata = responsedata.chartdata;

			var list = responsedata.data;

			console.log('char',chartdata);

			// console.log('asda',othedda)

			var data = new google.visualization.DataTable();

			data.addColumn('string', 'Karigar');

			data.addColumn('number', 'Sale Value');

			data.addRows(chartdata);



			// Set chart options

			var options = {

				           sliceVisibilityThreshold: 0,

						   title: ' Top Five Stocks',

						   colors:colorCodes

						};



			// Instantiate and draw our chart, passing in some options.

			var chart = new google.visualization.PieChart(document.getElementById('karigar_stock'));

			chart.draw(data, options);



			karigar_stock_wt=0;

			$.each(list, function (index, element)

			{

				karigar_stock_wt+=parseFloat(element.stock_wt);

			});



			label='';

			$.each(chartdata, function (index, element)

			{

				label+='<button type="button" class="btn " style="margin-left: 5px;color: white;background-color :'+colorCodes[index]+';" >'+element[0]+' - '+intToString(element[1])+'</button>'

			});



			$('#karigar_stock_lable').html(label);









			if($('#mt_karigar_groupby').val()==1){

				var oTable = $('#karigar_wise_stock').DataTable();

				oTable.clear().draw();

			if (list != null && list.length > 0) {

				oTable = $('#karigar_wise_stock').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"order": [[ 2, "desc" ]],

					"bSort": true,

					"columnDefs": [

						{

							targets: [1,2,3,4,5],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

					"aaData": list,

					"aoColumns": [

						{ "mDataProp": "karigar_name" },

						{ "mDataProp": "stock_pcs" },

						{ "mDataProp": function(row){

							return money_format_india(row.stock_wt)

						} },

						{ "mDataProp": function(row){

							return money_format_india(row.net_wt)

						} },

						{ "mDataProp": function(row){

							return money_format_india(row.dia_wt)

						} },

						{ "mDataProp": function ( row, type, val, meta ) {

                            percentage = parseFloat(row.stock_wt/karigar_stock_wt * 100).toFixed(2);

							return percentage;

						  }},



					],

					"footerCallback": function ( row, data, start, end, display ) {

						var api = this.api(), data;

						// Remove the formatting to get integer data for summation

						var intVal = function ( i ) {

							return typeof i === 'string' ?

								i.replace(/[\$,]/g, '')*1 :

								typeof i === 'number' ?

									i : 0;

						};



						total_pcs = api

							.column(1)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );

						total_net_wt = api

							.column(3)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						total_wt = api

							.column(2)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



							tot_dia = api

							.column(4)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



							total_stock_percentage = api

							.column(5)

							.data()

							.reduce( function (a, b) {

								return intVal(a) + intVal(b);

							}, 0 );



						// Total over all pages



						// Update footer

						// $( api.column(5).footer() ).html(money_format_india(parseFloat(purchase_cost).toFixed(2)));

						count=list.length;

						// console.log('haidb',total_sale_percentage,Math.round(total_sale_percentage));

						$( api.column(0).footer() ).html(count+' Product');

						$( api.column(1).footer() ).html(money_format_india(parseFloat(total_pcs).toFixed(0)));

						$( api.column(2).footer() ).html(money_format_india(parseFloat(total_wt).toFixed(3)));

						$( api.column(3).footer() ).html(money_format_india(parseFloat(total_net_wt).toFixed(3)));

						$( api.column(4).footer() ).html(money_format_india(parseFloat(tot_dia).toFixed(3)));

						$( api.column(5).footer() ).html(parseFloat(Math.round(total_stock_percentage)).toFixed(2)+' %');





					}

				});

			}



		}else{



			$("#karigar_wise_stock > tbody > tr ").remove();

			$('#karigar_wise_stock').dataTable().fnClearTable();

			$('#karigar_wise_stock').dataTable().fnDestroy();



			trHTML='';



			if (list != null) {



				$.each(list, function (idx, karigar) {



					trHTML += '<tr><td style="text-align: left;color: red;font-weight: bold;" colspan=4>' + idx + '</td></tr>';



					karigar_total_stock_wt = 0;

					karigar_total_stock_pcs = 0;

					$.each(karigar, function (id,branch) {

						$.each(branch, function (id,product) {



								karigar_total_stock_wt+= parseFloat(product.stock_wt);

								karigar_total_stock_pcs+= parseFloat(product.stock_pcs);



						});

					});

					$.each(karigar, function (sidx, branch) {



						trHTML += '<tr><td style="text-align: left;color: blue;font-weight: bold;" colspan=4>' + sidx + '</td></tr>';

						total_stock_wt = 0;

						total_stock_pcs = 0;



						$.each(branch, function (id,product) {

							total_stock_wt+= parseFloat(product.stock_wt);

							total_stock_pcs+= parseFloat(product.stock_pcs);

						});

						stock_percentage = (total_stock_wt/karigar_total_stock_wt)*100;

						$.each(branch, function (id,product) {



							stock_percentage = (product.stock_wt/total_stock_wt) * 100;



						trHTML += '<tr>'

							+ '<td style="text-align: left;">' + product.product_name + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(product.stock_pcs).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(product.stock_wt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(stock_percentage).toFixed(2)) + '</td>'

						'</tr>';



						});



						trHTML +=  '<tr style="color: #d905ff;">'

						+ '<td style="text-align: left;font-weight:bold;">SUB TOTAL</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(total_stock_pcs).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(total_stock_wt).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;"></td></tr>';



					});

				});



			}

			$('#karigar_wise_stock > tbody').html(trHTML);



			if ( ! $.fn.DataTable.isDataTable( '#karigar_wise_stock' ) ) {



				oTable = $('#karigar_wise_stock').dataTable({

					"bDestroy": true,

					"bInfo": false,

					"bFilter": false,

					paging: false,

					scrollCollapse: true,

					scrollY: '50vh',

					"order": [[ 2, "desc" ]],

					"bSort": true,

					"columnDefs": [

						{

							targets: [1,2,3],

							className: 'dt-right'

						},

						{

							targets: [0],

							className: 'dt-left'

						}

					],

				});



			}





		}







		},



		error: function (error) {



			$("div.overlay").css("display", "none");



		}



	});

}









// set_stock_dashboard();







//Order Management





function get_pendingorderDetails(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard/get_pendingorderDetails?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){

				if(data != null &&  data.dash_pendingorder.pending_orders !== undefined){

				var pendingorder_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/1/' + $('#id_branch').val();

				var stock_order_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/2/' + $('#id_branch').val();

			        $("#om_order_pending").html('<a href='+pendingorder_url+' target="_blank">'+data.dash_pendingorder.pending_orders+'</a>');

				    $("#om_stock_pending").html('<a href='+stock_order_url+' target="_blank">'+data.dash_pendingorder.stock_orders+'</a>');

				}

				    $("div.overlay").css("display", "none");

			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_wiporderDetails(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard/get_wiporderDetails?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){

				if(data != null &&  data.dash_wiporder.wipcusorders !== undefined){

				var wiporder_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/3/' + $('#id_branch').val();

				var wipstock_order_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/4/' + $('#id_branch').val();

			        $("#om_cusorder_wip").html('<a href='+wiporder_url+' target="_blank">'+data.dash_wiporder.wipcusorders+'</a>');

				    $("#om_stkorder_wip").html('<a href='+wipstock_order_url+' target="_blank">'+data.dash_wiporder.wipstockorders+'</a>');

				}

				    $("div.overlay").css("display", "none");

			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_dreadyorderDetails(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard/get_dreadyorderDetails?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){

				if(data != null &&  data.dash_drorder.drcusorders !== undefined){

				var drorder_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/5/' + $('#id_branch').val();

				var drstock_order_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/6/' + $('#id_branch').val();

			        $("#om_cusorder_dready").html('<a href='+drorder_url+' target="_blank">'+data.dash_drorder.drcusorders+'</a>');

				    $("#om_stockorder_dready").html('<a href='+drstock_order_url+' target="_blank">'+data.dash_drorder.drstockorders+'</a>');

				}

				    $("div.overlay").css("display", "none");

			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_deliveredorderDetails(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard/get_deliveredorderDetails?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){

				if(data != null &&  data.dash_deliveredorder.deliveredcusorders !== undefined){

				var deliveredorder_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/7/' + $('#id_branch').val();

				var deliveredstock_order_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/8/' + $('#id_branch').val();

			        $("#om_cusorder_delivered").html('<a href='+deliveredorder_url+' target="_blank">'+data.dash_deliveredorder.deliveredcusorders+'</a>');

				    $("#om_stkorder_delivered").html('<a href='+deliveredstock_order_url+' target="_blank">'+data.dash_deliveredorder.deliveredstockorders+'</a>');

				}

				    $("div.overlay").css("display", "none");

			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_karigarreminderDetails(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard/get_karigarreminderDetails?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){

				if(data != null &&  data.dash_karreminderdetails.karigarremindcusorders !== undefined){

				var kar_cus_reminder_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/9/' + $('#id_branch').val();

				var kar_stk_reminder_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/10/' + $('#id_branch').val();

			        $("#om_cusorder_kar_reminder").html('<a href='+kar_cus_reminder_url+' target="_blank">'+data.dash_karreminderdetails.karigarremindcusorders+'</a>');

				    $("#om_stkorder_kar_reminder").html('<a href='+kar_stk_reminder_url+' target="_blank">'+data.dash_karreminderdetails.karigarremindstockorders+'</a>');

				}

				    $("div.overlay").css("display", "none");

			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_karigaroverdueDetails(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard/get_karigaroverdueDetails?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){

				if(data != null &&  data.dash_kar_overdue_details.karoverduecusorders !== undefined){

				var kar_cus_overdue_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/11/' + $('#id_branch').val();

				var kar_stk_overdue_url = base_url+'index.php/admin_ret_reports/dashboard_ordermanagement/'+from_date+'/'+to_date+'/12/' + $('#id_branch').val();

			        $("#om_cusorder_kar_overdue").html('<a href='+kar_cus_overdue_url+' target="_blank">'+data.dash_kar_overdue_details.karoverduecusorders+'</a>');

				    $("#om_stkorder_kar_overdue").html('<a href='+kar_stk_overdue_url+' target="_blank">'+data.dash_kar_overdue_details.kar_overdue_stockorders+'</a>');

				}

				    $("div.overlay").css("display", "none");

			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_cash_abstract_details(from_date,to_date)



{



    $("div.overlay").css("display", "block");



	my_Date = new Date();



	$.ajax({



	     url:base_url+ "index.php/admin_ret_dashboard/get_cash_abstract_details?nocache=" + my_Date.getUTCSeconds(),



			 dataType:"JSON",



			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val()},



			 type:"POST",



			 cache:false,



			  success:function(data){



                        	let cash_abs = data.dash_cash_abstarct_details;





							$("#sales_amount").html(money_format_india(parseFloat(cash_abs.sales_amount).toFixed(2)));



							$("#sales_total_tax_amount").html(money_format_india(parseFloat(cash_abs.sales_total_tax_amount).toFixed(2)));



							$("#sales_return").html(money_format_india(parseFloat(cash_abs.sales_return).toFixed(2)));



							$("#sales_return_total_tax_amount").html(money_format_india(parseFloat(cash_abs.sales_return_total_tax_amount).toFixed(2)));



							$("#purchase_amount").html(money_format_india(parseFloat(cash_abs.purchase_amount).toFixed(2)));



							$("#advance_receipt").html(money_format_india(parseFloat(cash_abs.advance_receipt).toFixed(2)));



							$("#ca_advance_refund").html(money_format_india(parseFloat(cash_abs.adv_refund).toFixed(2)));



							$("#credit_sale").html(money_format_india(parseFloat(cash_abs.credit_sale).toFixed(2)));



							$("#credit_receipt").html(money_format_india(parseFloat(cash_abs.credit_receipt).toFixed(2)));



							$("#handling_charge").html(money_format_india(parseFloat(cash_abs.handling_charge).toFixed(2)));



							$("#trans_total").html(money_format_india(parseFloat(cash_abs.trans_total).toFixed(2)));



							$("#cash").html(money_format_india(parseFloat(cash_abs.cash).toFixed(2)));



							$("#chq").html(money_format_india(parseFloat(cash_abs.chq).toFixed(2)));



							$("#card").html(money_format_india(parseFloat(cash_abs.card).toFixed(2)));



							$("#nb").html(money_format_india(parseFloat(cash_abs.nb).toFixed(2)));



							$("#advadj").html(money_format_india(parseFloat(cash_abs.advadj).toFixed(2)));



						//	$("#chituti").html(money_format_india(parseFloat(cash_abs.chituti).toFixed(2)));



							$("#handlingcharge").html(money_format_india(parseFloat(cash_abs.handlingcharge).toFixed(2)));



							$("#orderadj").html(money_format_india(parseFloat(cash_abs.orderadj).toFixed(2)));



							$("#giftvoucher").html(money_format_india(parseFloat(cash_abs.giftvoucher).toFixed(2)));



							$("#roundoff").html(money_format_india(parseFloat(cash_abs.roundoff).toFixed(2)));



							$("#paymodes_total").html(money_format_india( parseFloat(cash_abs.paymodes_total).toFixed(2)));



							$("#advance_deposit").html(money_format_india( parseFloat(cash_abs.advance_deposit).toFixed(2)));



							$("#other_expenses").html(money_format_india(parseFloat(cash_abs.other_expense).toFixed(2)));



							$("#chq_recd").html(money_format_india(parseFloat(cash_abs.chq_recd).toFixed(2)));



							$("#chq_issued").html(money_format_india(parseFloat(cash_abs.chq_issue).toFixed(2)));



							$("#chitben").html(money_format_india(parseFloat(cash_abs.chitben).toFixed(2)));



							$("#chitpaid").html(money_format_india(parseFloat(cash_abs.chitcuspaid).toFixed(2)));



							$(".chit_payments").remove();



							if(cash_abs.chit_payment_details.length>0){



								let heading = `<div class="col-md-12 col-xs-12 item-heading chit_payments even">



													CHIT PAYMENT



												</div>`;



								$(".cash_abs_details").append(heading);



			 			        $.each(cash_abs.chit_payment_details,function(key,item){



									if(key%2 == 1){

										class_='odd'

									}else{

										class_='even'

									}



									let trHTML = `<div class="col-md-12 col-xs-12 no-paddingwidth `+class_+` row_ca  chit_payments">



													<div class="col-md-6 col-xs-6 text">`+money_format_india(parseFloat(item.payment_mode).toFixed(2))+`</div>



													<div class="col-md-1 col-xs-1"></div>



													<div class="col-md-5 col-xs-5 values color">`+money_format_india(parseFloat(item.payment_amount).toFixed(2))+`</div>

												</div>`;



									$(".cash_abs_details").append(trHTML);



			 			        });



			 			    }







            				$("div.overlay").css("display", "none");



			  },



			  error:function(error)



			  {



				 $("div.overlay").css("display", "none");



			  }



	});



}



//PURCHASE CHARTS



function get_purchase_inwards(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	         url:base_url+ "index.php/admin_ret_dashboard_api/get_purchase_inwards?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				data = data.response_data;



				$("#pur_gross_wt").html(money_format_india(parseFloat(data.gross_wt).toFixed(3)));



				$("#pur_net_wt").html(money_format_india(parseFloat(data.net_wt).toFixed(3)));



				$("#pur_pure_wt").html(money_format_india(parseFloat(data.pure_wt).toFixed(3)));



				$("#pur_dia_wt").html(money_format_india(parseFloat(data.dia_wt).toFixed(3)));



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}





function get_vendor_payment(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_vendor_payment?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				$("#vendor_payment_table tbody").empty();



				let vandor_payments = data.response_data;







				let table_value = "";

				let total_cash =0;

				let total_nb =0;

				let total =0;



				$.each(vandor_payments, function (index, element)  {



					total_cash += parseFloat(element.cash);



					total_nb += parseFloat(element.NB);



					total += parseFloat(element.total);



					table_value = table_value+'<tr>'+



					'<td class="alignleft">'+element.suppliername+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(element.cash).toFixed(2))+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(element.NB).toFixed(2))+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(element.total).toFixed(2))+'</td>'+



					'</tr>';



				});







				if(table_value != "") {



					table_value += '<tr class="footer" >'+



					'<td class="alignleft" >TOTAL</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(total_cash).toFixed(2))+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(total_nb).toFixed(2))+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(total).toFixed(2))+'</td>'+



					'</tr>';



					$("#vendor_payment_table tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



					$("#vendor_payment_table tbody").append(table_value);



				}



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}





function get_outwards_details(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_outward_details?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				$("#outward_details tbody").empty();



				let response_data = data.response_data;







				let table_value = "";

				let total_gwt =0;

				let total_nwt =0;

				let total_diawt =0;

				let total_purewt =0;





				$.each(response_data, function (index, element)  {



					total_gwt += parseFloat(element.gross_wt);



					total_nwt += parseFloat(element.net_wt);



					total_diawt += parseFloat(element.diawt);



					total_purewt += parseFloat(element.purewt);



					table_value = table_value+'<tr>'+



					'<td class="alignleft" >'+element.type+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(element.gross_wt).toFixed(3))+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(element.net_wt).toFixed(3))+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(element.diawt).toFixed(3))+'</td>'+



					'<td class="alignright" >'+money_format_india(parseFloat(element.purewt).toFixed(3))+'</td>'+



					'</tr>';



				});







				if(table_value != "") {



					table_value += '<tr class="footer" >'+



					'<td class="alignleft" >TOTAL</td>'+



					'<td  class="alignright" >'+money_format_india(parseFloat(total_gwt).toFixed(3))+'</td>'+



					'<td  class="alignright" >'+money_format_india(parseFloat(total_nwt).toFixed(3))+'</td>'+



					'<td  class="alignright" >'+money_format_india(parseFloat(total_diawt).toFixed(3))+'</td>'+



					'<td  class="alignright" >'+money_format_india(parseFloat(total_purewt).toFixed(3))+'</td>'+



					'</tr>';



					$("#outward_details tbody").append(table_value);



				} else {



					table_value = "<tr><td colspan='5'>No Records found</td></tr>";



					$("#outward_details tbody").append(table_value);



				}



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_profit_loss(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_weight_gain_loss?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				data = data.response_data.summary;



				$("#gain_gross_wt").html(money_format_india(parseFloat(isNaN(data.blc_gwt) ? 0 : data.blc_gwt).toFixed(3)));



				$("#gain_net_wt").html(money_format_india(parseFloat(data.blc_nwt).toFixed(3)));



				$("#gain_pcs").html(money_format_india(parseFloat(data.blc_pcs).toFixed(0)));



				$("#gain_dia_wt").html(money_format_india(parseFloat(data.blc_diawt).toFixed(3)));



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_SupplierTransactions(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/getMetalwiseApprovalTransaction?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				//$("#vendor_payment_table tbody").empty();



				var list = data.response_data;



				var opening_value = "";

				var closing_value = "";

				var opening_wt = "";

				var closing_wt = "";



				var trHTML = '';

				var gold_value = 0;

				var silver_value = 0;

				var platinum_wt = 0;

				var amount = 0;

				var tFtrHTML = "";





				$.each(list, function (key, item) {

					gold_value += parseFloat(item.goldwt);

					silver_value += parseFloat(item.silverwt);

					platinum_wt += parseFloat(item.platinumwt);

					amount += parseFloat(item.balanceamt);

				});

				if (list != null) {

					var smithFilter = $('#smith_balance_filter').val() || 'all';



					$.each(list, function (idx, item) {

						if (smithFilter === 'exclude_zero' && parseFloat(item.balanceamt) === 0 && parseFloat(item.goldwt) === 0 && parseFloat(item.silverwt) === 0 && parseFloat(item.platinumwt) === 0) { return true; }



						trHTML += '<tr>'

							+ '<td style="text-align: left;">' + item.firstname + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.goldwt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.silverwt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.platinumwt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.balanceamt).toFixed(2)) + '</td>'

						'</tr>';

					});

					tFtrHTML = '<tr>'

						+ '<td style="text-align: left;font-weight:bold;">Total : </td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(gold_value).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(silver_value).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(platinum_wt).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(amount).toFixed(3)) + '</td>'



					'</tr>';

				}

				$('#smith_ledgere_metal_list > tbody').html(trHTML);

				$('#smith_ledgere_metal_list > tfoot').html(tFtrHTML);







				// if(table_value != "") {



				// 	table_value += '<tr>'+



				// 	'<td>TOTAL</td>'+



				// 	'<td>'+total_cash+'</td>'+



				// 	'<td>'+total_nb+'</td>'+



				// 	'<td>'+total+'</td>'+



				// 	'</tr>';



				// 	$("#vendor_payment_table tbody").append(table_value);



				// } else {



				// 	table_value = "<tr><td colspan='5'>No Records found</td></tr>";



				// 	$("#vendor_payment_table tbody").append(table_value);



				// }



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_creditdebit(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_crdr_details?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				data = data.response_data;



				$("#pur_balance").html(money_format_india(parseFloat(data.credit_amount- data.debit_amount).toFixed(2)));



				$("#pur_debit").html(money_format_india(parseFloat(data.debit_amount).toFixed(2)));



				//$("#gain_pure_wt").html(money_format_india(parseFloat(data.pure_wt).toFixed(3)));



				$("#pur_credit").html(money_format_india(parseFloat(data.credit_amount).toFixed(2)));



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_qc_details(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_qc_details?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				data = data.response_data;



				$("#qc_gross_wt").html(money_format_india(parseFloat(data.qc_failed_gwt).toFixed(3)));



				$("#qc_net_wt").html(money_format_india(parseFloat(data.qc_failed_nwt).toFixed(3)));



				//$("#gain_pure_wt").html(money_format_india(parseFloat(data.pure_wt).toFixed(3)));



				$("#qc_less_wt").html(money_format_india(parseFloat(data.qc_failed_lwt).toFixed(3)));



				$("#qc_pcs").html(money_format_india(parseFloat(data.qc_failed_pcs).toFixed(0)));



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function set_purchase_dashboard (){

	let from_date =  $('#payment_list1').text();

	let to_date =  $('#payment_list2').text();



	get_purchase_inwards(from_date,to_date);

	get_vendor_payment(from_date,to_date);

	get_outwards_details(from_date,to_date);

	get_profit_loss(from_date,to_date);

	get_SupplierTransactions(from_date,to_date);

	get_creditdebit(from_date,to_date);

	get_qc_details(from_date,to_date);

	get_rate_fixed(from_date,to_date);

	get_rate_unfixed(from_date,to_date);

	get_supplier_crde(from_date,to_date);

	get_accountstock_inwards(from_date,to_date);

	get_supplier_transcation(from_date,to_date);

	let id_branch = $('#id_branch').val() || '';
	get_delayed_purchase_orders(from_date,to_date, id_branch);
	get_delayed_po_payments(from_date,to_date, id_branch);
	get_today_delivery_po_payments(from_date,to_date, id_branch);
	get_rate_cut_profit_loss(from_date, to_date, id_branch);
	get_md_pending_orders();

}

function get_delayed_po_payments(from_date, to_date, id_branch) {
    if (!id_branch) id_branch = $('#id_branch').val() || '';
    let id_metalArr = $('#metal_select_dash').val() || [];
    let id_metal = Array.isArray(id_metalArr) ? id_metalArr.join(',') : id_metalArr;

    $.ajax({
        url: base_url + "index.php/admin_ret_dashboard_api/get_delayed_po_payments",
        type: "POST",
        data: { from_date: from_date, to_date: to_date, id_branch: id_branch, id_metal: id_metal },
        dataType: 'json',
        success: function (data) {
            $("#delayed_po_payment_table tbody").empty();
            $("#total_delayed_bill_amt").text('0');
            $("#total_delayed_pay_amt").text('0');

            if (data.status) {
                let payments = data.response_data;
                let totalBillAmt = 0;
                let totalPaidAmt = 0;

                $.each(payments, function (index, element) {
                    let delay = Math.abs(parseInt(element.delay_days)) || 0;
                    let billAmt = parseFloat(element.bill_amount) || 0;
                    totalBillAmt += billAmt;

                    let signalColor = "dot-red";
                    let signalBlink = "blink";
                    if (delay >= 15) {
                        signalColor = "dot-dark-red";
                        signalBlink = "blink-fast";
                    } else if (delay >= 8) {
                        signalColor = "dot-red";
                    } else if (delay >= 3) {
                        signalColor = "dot-orange";
                    } else {
                        signalColor = "dot-yellow";
                    }

                    let commentText = element.dashboard_comment || '';
                    let escapedComment = $('<div>').text(commentText).html();

                    let table_value = '<tr>' +
                        '<td><div class="signal-dot ' + signalColor + ' ' + (element.status === 'Paid' ? '' : signalBlink) + '"></div></td>' +
                        '<td><a href="' + base_url + 'index.php/admin_ret_purchase/purchase/job_receipt/' + element.po_id + '" target="_blank">' + element.po_number + '</a></td>' +
                        '<td>' + element.po_date + '</td>' +
                        '<td>' + element.supplier_name + '</td>' +
                        '<td>' + element.due_date + '</td>' +
                        '<td class="alignright text-danger" style="font-weight:bold;">' + (delay == 0 ? '' : delay) + '</td>' +
                        '<td class="alignright">' + (billAmt == 0 ? '' : money_format_india(billAmt.toFixed(2))) + '</td>' +
                        '<td style="font-weight:bold; color:' + (element.payment_status === 'Paid' ? 'green' : 'red') + '">' + element.payment_status + '</td>' +
                        '<td class="po-comment-cell" data-po-id="' + element.po_id + '">' +
                            '<span class="comment-display">' + escapedComment + '</span>' +
                            '<i class="fa fa-pencil comment-edit-btn" style="cursor:pointer; margin-left:5px; color:#337ab7; font-size:12px;" title="Edit Comment"></i>' +
                            '<div class="comment-edit-box" style="display:none;">' +
                                '<textarea class="form-control comment-textarea" rows="2" style="font-size:11px; margin-bottom:4px;">' + escapedComment + '</textarea>' +
                                '<button class="btn btn-primary btn-xs comment-save-btn" style="margin-right:4px;"><i class="fa fa-check"></i> Save</button>' +
                                '<button class="btn btn-default btn-xs comment-cancel-btn"><i class="fa fa-times"></i></button>' +
                            '</div>' +
                        '</td>' +
                        '</tr>';
                    $("#delayed_po_payment_table tbody").append(table_value);
                });

                $("#total_delayed_bill_amt").text(money_format_india(totalBillAmt.toFixed(2)));
                $("#total_delayed_pay_amt").text(money_format_india(totalPaidAmt.toFixed(2)));
            }
        }
    });
}

function get_today_delivery_po_payments(from_date, to_date, id_branch) {
    if (!id_branch) id_branch = $('#id_branch').val() || '';
    let id_metalArr = $('#metal_select_dash').val() || [];
    let id_metal = Array.isArray(id_metalArr) ? id_metalArr.join(',') : id_metalArr;

    $.ajax({
        url: base_url + "index.php/admin_ret_dashboard_api/get_today_delivery_po_payments",
        type: "POST",
        data: { from_date: from_date, to_date: to_date, id_branch: id_branch, id_metal: id_metal },
        dataType: 'json',
        success: function (data) {
            $("#today_po_delivery_table tbody").empty();
            let totalBillAmt = 0;

            if (data.status) {
                let payments = data.response_data;
                
                $.each(payments, function (index, element) {
                    let billAmt = parseFloat(element.bill_amount) || 0;
                    totalBillAmt += billAmt;
                    
                    let signalColor = "dot-green"; 

                    let commentText = element.dashboard_comment || '';
                    let escapedComment = $('<div>').text(commentText).html();

                    let table_value = '<tr>' +
                        '<td><div class="signal-dot ' + signalColor + '"></div></td>' +
                        '<td><a href="' + base_url + 'index.php/admin_ret_purchase/purchase/job_receipt/' + element.po_id + '" target="_blank">' + element.po_number + '</a></td>' +
                        '<td>' + element.po_date + '</td>' +
                        '<td>' + element.supplier_name + '</td>' +
                        '<td>' + element.due_date + '</td>' +
                        '<td class="alignright">' + (billAmt == 0 ? '' : money_format_india(billAmt.toFixed(2))) + '</td>' +
                        '<td style="font-weight:bold; color:black">' + element.status + '</td>' +
                        '<td class="po-comment-cell" data-po-id="' + element.po_id + '">' +
                            '<span class="comment-display">' + escapedComment + '</span>' +
                            '<i class="fa fa-pencil comment-edit-btn" style="cursor:pointer; margin-left:5px; color:#337ab7; font-size:12px;" title="Edit Comment"></i>' +
                            '<div class="comment-edit-box" style="display:none;">' +
                                '<textarea class="form-control comment-textarea" rows="2" style="font-size:11px; margin-bottom:4px;">' + escapedComment + '</textarea>' +
                                '<button class="btn btn-primary btn-xs comment-save-btn" style="margin-right:4px;"><i class="fa fa-check"></i> Save</button>' +
                                '<button class="btn btn-default btn-xs comment-cancel-btn"><i class="fa fa-times"></i></button>' +
                            '</div>' +
                        '</td>' +
                        '</tr>';
                    $("#today_po_delivery_table tbody").append(table_value);
                });

                $("#total_today_bill_amt").text(money_format_india(totalBillAmt.toFixed(2)));
            }
        }
    });
}

// ===== Dashboard Comment Box Handlers =====
$(document).on('click', '.comment-edit-btn', function () {
    let $cell = $(this).closest('.po-comment-cell');
    $cell.find('.comment-display, .comment-edit-btn').hide();
    $cell.find('.comment-edit-box').show();
    $cell.find('.comment-textarea').focus();
});

$(document).on('click', '.comment-cancel-btn', function () {
    let $cell = $(this).closest('.po-comment-cell');
    let originalText = $cell.find('.comment-display').text();
    $cell.find('.comment-textarea').val(originalText);
    $cell.find('.comment-edit-box').hide();
    $cell.find('.comment-display, .comment-edit-btn').show();
});

$(document).on('click', '.comment-save-btn', function () {
    let $btn = $(this);
    let $cell = $btn.closest('.po-comment-cell');
    let po_id = $cell.data('po-id');
    let comment = $cell.find('.comment-textarea').val();

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
        url: base_url + 'index.php/admin_ret_dashboard_api/save_po_dashboard_comment',
        type: 'POST',
        data: { po_id: po_id, comment: comment },
        dataType: 'json',
        success: function (data) {
            if (data.status) {
                $cell.find('.comment-display').text(comment);
                $cell.find('.comment-edit-box').hide();
                $cell.find('.comment-display, .comment-edit-btn').show();
            } else {
                alert('Error: ' + (data.msg || 'Failed to save comment'));
            }
        },
        error: function () {
            alert('Network error. Please try again.');
        },
        complete: function () {
            $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Save');
        }
    });
});
// ===== End Dashboard Comment Box Handlers =====

function get_delayed_purchase_orders(from_date, to_date, id_branch) {
    if (!id_branch) id_branch = $('#id_branch').val() || '';
    let id_metalArr = $('#metal_select_dash').val() || [];
    let id_metal = Array.isArray(id_metalArr) ? id_metalArr.join(',') : id_metalArr;

    $.ajax({
        url: base_url + "index.php/admin_ret_dashboard_api/get_delayed_purchase_orders",
        type: "POST",
        data: { from_date: from_date, to_date: to_date, id_branch: id_branch, id_metal: id_metal },
        dataType: 'json',
        success: function (data) {
            $("#delayed_po_table tbody").empty();
            $("#karigar_delay_analysis_table tbody").empty();
            
            // Reset footer totals
            $("#total_3_7, #total_8_10, #total_11_14, #total_15_plus, #total_sum_delayed, #total_qty_ordered, #total_sum_po, #total_sum_arriving_po, #total_sum_delivered_po, #total_sum_delayed_delivered, #total_del_3_7, #total_del_8_10, #total_del_11_14, #total_del_15_plus").text('0');

            if (data.status) {
                let pos = data.response_data;
                let karigarSummary = {};
                let grandTotals = {
                    '3-7': 0, '8-10': 0, '11-14': 0, '15+': 0, 
                    'del-3-7': 0, 'del-8-10': 0, 'del-11-14': 0, 'del-15+': 0,
                    total: 0, qty: 0, received: 0, balance: 0, 
                    extra: 0, shortage: 0
                };

                const emptyIfZero = (val) => (parseFloat(val) === 0 || val === '0' || val === '' || val == null) ? '' : val;

                let reportView = $('input[name="report_view"]:checked').val() || 'pending';

                $.each(pos, function (index, element) {
                    let qty = parseFloat(element.quantity_ordered) || 0;
                    let recd = parseFloat(element.received_pcs) || 0;
                    let isItemDelivered = (recd >= qty && qty > 0);
                    
                    let delay = parseInt(element.delayed_days);
                    let isFullyDelivered = isItemDelivered;
                    let diffDays = parseInt(element.delivery_diff_days) || 0;
                    
                    let signalColor = "";
                    let signalBlink = "";

                    if (!karigarSummary[element.karigar_name]) {
                        karigarSummary[element.karigar_name] = { 
                            '3-7': 0, '8-10': 0, '11-14': 0, '15+': 0, 
                            'del-3-7': 0, 'del-8-10': 0, 'del-11-14': 0, 'del-15+': 0,
                            totalDelayed: 0, maxDelay: 0,
                            totalPOs: 0, deliveredPOs: 0, arrivingPOs: 0,
                            delayedDelivered: 0
                        };
                    }

                    karigarSummary[element.karigar_name].totalPOs++;

                    if (isItemDelivered) {
                        karigarSummary[element.karigar_name].deliveredPOs++;
                    } else if (delay <= 2) {
                        karigarSummary[element.karigar_name].arrivingPOs++;
                    }


                    grandTotals.qty += qty;
                    grandTotals.received += recd;
                    let rowBalance = qty - recd;
                    grandTotals.balance += rowBalance;

                    if (isFullyDelivered) {
                        // Delivered (Received): Solid Colors
                        if (diffDays < 0) {
                            let delDelay = Math.abs(diffDays);
                            karigarSummary[element.karigar_name].delayedDelivered++;
                            
                            if (delDelay >= 15) { 
                                signalColor = "dot-red";
                                karigarSummary[element.karigar_name]['del-15+']++; grandTotals['del-15+']++; 
                            }
                            else if (delDelay >= 11) { 
                                signalColor = "dot-orangered";
                                karigarSummary[element.karigar_name]['del-11-14']++; grandTotals['del-11-14']++; 
                            }
                            else if (delDelay >= 8) { 
                                signalColor = "dot-orange";
                                karigarSummary[element.karigar_name]['del-8-10']++; grandTotals['del-8-10']++; 
                            }
                            else if (delDelay >= 3) { 
                                signalColor = "dot-yellow";
                                karigarSummary[element.karigar_name]['del-3-7']++; grandTotals['del-3-7']++; 
                            }
                            else {
                                signalColor = "dot-green";
                            }
                        } else {
                            signalColor = "dot-green";
                        }
                        signalBlink = ""; // No blink for delivered
                    } else {
                        // Pending (Yet to deliver): Blinking Colors
                        if (delay >= 15) {
                            signalColor = "dot-red";
                            signalBlink = "blink-fast";
                            karigarSummary[element.karigar_name]['15+']++;
                            grandTotals['15+']++;
                        } else if (delay >= 11) {
                            signalColor = "dot-orangered";
                            signalBlink = "blink";
                            karigarSummary[element.karigar_name]['11-14']++;
                            grandTotals['11-14']++;
                        } else if (delay >= 8) {
                            signalColor = "dot-orange";
                            signalBlink = "blink";
                            karigarSummary[element.karigar_name]['8-10']++;
                            grandTotals['8-10']++;
                        } else if (delay >= 3) {
                            signalColor = "dot-yellow";
                            signalBlink = "blink";
                            karigarSummary[element.karigar_name]['3-7']++;
                            grandTotals['3-7']++;
                        }

                        if (delay > 2) {
                            karigarSummary[element.karigar_name].totalDelayed++;
                            grandTotals.total++;
                        }
                        if (delay > karigarSummary[element.karigar_name].maxDelay) {
                            karigarSummary[element.karigar_name].maxDelay = delay;
                        }
                    }

                    let delayDisplay = delay;
                    if (!isFullyDelivered) {
                        if (delay < 0) {
                            delayDisplay = 'In ' + Math.abs(delay) + ' days';
                            if (signalColor === "") { 
                                signalColor = "dot-green";
                                signalBlink = "blink";
                            }
                        } else if (delay >= 0 && delay <= 2) {
                            if (delay === 0) delayDisplay = 'Arriving today';
                            if (signalColor === "") {
                                signalColor = "dot-green";
                                signalBlink = "blink";
                            }
                        }
                    }

                    let finalDiff = isFullyDelivered ? -diffDays : delay;
                    let diffColor = (finalDiff < 0) ? 'text-green' : 'text-danger';
                    
                    let showRow = true;
                    if (reportView === 'pending' && isFullyDelivered) showRow = false;
                    if (reportView === 'delivered' && !isFullyDelivered) showRow = false;
                    
                    let hideStyle = showRow ? '' : 'style="display:none;"';
                    let rowClass = isFullyDelivered ? 'delivered-row' : 'pending-row';

                    let balance = qty - recd;
					let extra_txt = '';
					let shortage_txt = '';
					if(balance > 0)
					{
						extra_txt = '(bal) <span style="color:red;">('+Math.abs(balance)+')</span> ';
						grandTotals.extra += Math.abs(balance);
					}
					else if(balance < 0)
					{
						shortage_txt = '(etc) <span style="color:green;">('+Math.abs(balance)+')</span> ';
						grandTotals.shortage += Math.abs(balance);
					}
					let balance_txt = extra_txt+shortage_txt;
                    let diffDaysVal = element.delivery_diff_days != null ? element.delivery_diff_days : 0;
                    let table_value = '<tr class="' + rowClass + '" ' + hideStyle + '>' +
                        '<td><div class="signal-dot ' + signalColor + ' ' + (isFullyDelivered ? '' : signalBlink) + '"></div></td>' +
                        '<td><a href="' + base_url + 'index.php/admin_ret_purchase/get_karigar_acknowladgement/' + element.orderid + '" target="_blank">' + element.po_number + '</a></td>' +
                        '<td>' + element.po_date + '</td>' +
                        '<td data-delay="' + delay + '">' + element.expected_delivery_date + '</td>' +
                        '<td>' + element.karigar_name + '</td>' +
                        '<td>' + element.order_item + '</td>' +
                        '<td class="alignright">' + emptyIfZero(element.quantity_ordered) + '</td>' +
                        '<td class="alignright">' + emptyIfZero(recd) + '</td>' +
                        '<td class="alignright">' + (recd ? balance_txt : '') + '</td>' +
                        '<td>' + (element.actual_received_date || '') + '</td>' +
                        '<td class="alignright ' + diffColor + '" data-diff-days="' + diffDaysVal + '">' + Math.abs(finalDiff) + '</td>' +
                        '</tr>';
                    $("#delayed_po_table tbody").append(table_value);
                });

                let summaryGrand = { dly: 0, tpo: 0, dpo: 0, apo: 0, ddly: 0 };
                $.each(karigarSummary, function (name, s) {
                    summaryGrand.dly += s.totalDelayed;
                    summaryGrand.tpo += s.totalPOs;
                    summaryGrand.dpo += s.deliveredPOs;
                    summaryGrand.apo += s.arrivingPOs;
                    summaryGrand.ddly += s.delayedDelivered;

                    let row = '<tr class="karigar-row">' +
                        '<td>' + name + '</td>' +
                        '<td class="alignright">' + emptyIfZero(s.totalPOs) + '</td>' +
                        '<td class="alignright">' + emptyIfZero(s.deliveredPOs) + '</td>' +
                        '<td class="alignright link-row" data-karigar="' + name + '" data-bracket="arriving">' + emptyIfZero(s.arrivingPOs) + '</td>' +
                        // Delivered Brackets
                        '<td class="alignright link-row col-delivered" data-karigar="' + name + '" data-bracket="delayed-del">' + emptyIfZero(s.delayedDelivered) + '</td>' +
                        '<td class="alignright link-row col-delivered" data-karigar="' + name + '" data-bracket="del-3-7">' + emptyIfZero(s['del-3-7']) + '</td>' +
                        '<td class="alignright link-row col-delivered" data-karigar="' + name + '" data-bracket="del-8-10">' + emptyIfZero(s['del-8-10']) + '</td>' +
                        '<td class="alignright link-row col-delivered" data-karigar="' + name + '" data-bracket="del-11-14">' + emptyIfZero(s['del-11-14']) + '</td>' +
                        '<td class="alignright link-row col-delivered" data-karigar="' + name + '" data-bracket="del-15+">' + emptyIfZero(s['del-15+']) + '</td>' +
                                                 
                        // Pending Brackets
                        '<td class="alignright link-row col-pending" data-karigar="' + name + '" data-bracket="all">' + emptyIfZero(s.totalDelayed) + '</td>' +
                        '<td class="alignright link-row col-pending" data-karigar="' + name + '" data-bracket="3-7">' + emptyIfZero(s['3-7']) + '</td>' +
                        '<td class="alignright link-row col-pending" data-karigar="' + name + '" data-bracket="8-10">' + emptyIfZero(s['8-10']) + '</td>' +
                        '<td class="alignright link-row col-pending" data-karigar="' + name + '" data-bracket="11-14">' + emptyIfZero(s['11-14']) + '</td>' +
                        '<td class="alignright link-row col-pending" data-karigar="' + name + '" data-bracket="15+">' + emptyIfZero(s['15+']) + '</td>' +
                        '</tr>';
                    $("#karigar_delay_analysis_table tbody").append(row);
                });

                // Update Footers
                $("#total_sum_po").text(emptyIfZero(summaryGrand.tpo));
                $("#total_sum_delivered_po_summary").text(emptyIfZero(summaryGrand.dpo));
                $("#total_sum_arriving_po").text(emptyIfZero(summaryGrand.apo));
                $("#total_sum_delayed_delivered").text(emptyIfZero(summaryGrand.ddly));
                $("#total_del_3_7").text(emptyIfZero(grandTotals['del-3-7']));
                $("#total_del_8_10").text(emptyIfZero(grandTotals['del-8-10']));
                $("#total_del_11_14").text(emptyIfZero(grandTotals['del-11-14']));
                $("#total_del_15_plus").text(emptyIfZero(grandTotals['del-15+']));

                $("#total_sum_delayed").text(emptyIfZero(summaryGrand.dly));
                $("#total_3_7").text(emptyIfZero(grandTotals['3-7']));
                $("#total_8_10").text(emptyIfZero(grandTotals['8-10']));
                $("#total_11_14").text(emptyIfZero(grandTotals['11-14']));
                $("#total_15_plus").text(emptyIfZero(grandTotals['15+']));
				let extra_txt = '';
				let shortage_txt = '';
				if(grandTotals.shortage != 0)
					extra_txt = '(bal) <span style="color:red;">('+Math.abs(grandTotals.shortage)+')</span>';
				else if(grandTotals.extra != 0)
					shortage_txt = '(etc) <span style="color:green;">('+Math.abs(grandTotals.extra)+')</span>';
				let balance_txt = extra_txt+" | "+shortage_txt;
                // Update detailed table footer
                $("#total_qty_ordered").text(emptyIfZero(grandTotals.qty));
                $("#total_qty_received").text(emptyIfZero(grandTotals.received));
                $("#total_qty_balance").text(emptyIfZero(grandTotals.received ? balance_txt : ''));
                // Trigger visibility refresh
                $('input[name="report_view"]:checked').trigger('change');
            }
        }
    });
}

$(document).on('change', 'input[name="report_view"]', function() {
    let view = $(this).val();
    
    // Toggle Second Table (Detailed POs) Rows
    if (view === 'pending') {
        $('.pending-row').show();
        $('.delivered-row').hide();
        $('.group-delivered, .col-delivered').hide();
        $('.group-pending, .col-pending').show();
    } else if (view === 'delivered') {
        $('.pending-row').hide();
        $('.delivered-row').show();
        $('.group-delivered, .col-delivered').show();
        $('.group-pending, .col-pending').hide();
    } else {
        $('.pending-row, .delivered-row').show();
        $('.group-delivered, .col-delivered, .group-pending, .col-pending').show();
    }

    // Toggle Summary Table Rows based on visibility of their data
    $('#karigar_delay_analysis_table tbody tr.karigar-row').each(function() {
        let hasPending = parseInt($(this).find('.col-pending[data-bracket="all"]').text()) > 0;
        let hasDelivered = parseInt($(this).find('.col-delivered[data-bracket="delayed-del"]').text()) > 0;
        
        if (view === 'pending') {
            if (hasPending) $(this).show(); else $(this).hide();
        } else if (view === 'delivered') {
            if (hasDelivered) $(this).show(); else $(this).hide();
        } else {
            $(this).show();
        }
    });

    recalculate_delayed_report_footers();
});

function recalculate_delayed_report_footers() {
    const emptyIfZero = (val) => (parseFloat(val) === 0 || val === '0' || val === '' || val == null) ? '' : val;
    let view = $('input[name="report_view"]:checked').val() || 'both';

    // Detailed Table Totals
    let dt_qty = 0, dt_recd = 0, dt_bal = 0, dt_extra = 0, dt_shortage = 0;
    $("#delayed_po_table tbody tr:visible").each(function() {
        let q = parseFloat($(this).find('td:eq(6)').text()) || 0;
        let r = parseFloat($(this).find('td:eq(7)').text()) || 0;
        dt_qty += q;
        dt_recd += r;
        dt_bal += (q - r);   
		let balance = (q - r);        
		let extra = 0;
		let shortage = 0;
		if(balance > 0 && r != 0)
			dt_extra = Math.abs(balance);
		else if(balance < 0 && r != 0)
			dt_shortage = Math.abs(balance);         
    });
	let extra_txt = '';
	let shortage_txt = '';
	if(dt_extra!=0)
	extra_txt = '(bal) <span style="color:red;">('+dt_extra+')</span> ';
	if(dt_shortage!=0)
	shortage_txt = '(etc) <span style="color:green;">('+dt_shortage+')</span> ';
	let balance_txt = extra_txt+shortage_txt;
    $("#total_qty_ordered").text(emptyIfZero(dt_qty));
    $("#total_qty_received").text(emptyIfZero(dt_recd));
    $("#total_qty_balance").html(emptyIfZero((dt_recd ? balance_txt : '')));

    // Summary Table Totals
    let s_po = 0, s_dpo = 0, s_apo = 0, s_dd = 0, s_d3 = 0, s_d8 = 0, s_d11 = 0, s_d15 = 0;
    let s_p = 0, s_p3 = 0, s_p8 = 0, s_p11 = 0, s_p15 = 0;
    
    $("#karigar_delay_analysis_table tbody tr:visible").each(function() {
        s_po += parseFloat($(this).find('td:eq(1)').text()) || 0;
        s_dpo += parseFloat($(this).find('td:eq(2)').text()) || 0;
        s_apo += parseFloat($(this).find('td:eq(3)').text()) || 0;
        
        if (view === 'both' || view === 'delivered') {
            s_dd += parseFloat($(this).find('td:eq(4)').text()) || 0;
            s_d3 += parseFloat($(this).find('td:eq(5)').text()) || 0;
            s_d8 += parseFloat($(this).find('td:eq(6)').text()) || 0;
            s_d11 += parseFloat($(this).find('td:eq(7)').text()) || 0;
            s_d15 += parseFloat($(this).find('td:eq(8)').text()) || 0;
        }
        
        if (view === 'both' || view === 'pending') {
            s_p += parseFloat($(this).find('td:eq(9)').text()) || 0;
            s_p3 += parseFloat($(this).find('td:eq(10)').text()) || 0;
            s_p8 += parseFloat($(this).find('td:eq(11)').text()) || 0;
            s_p11 += parseFloat($(this).find('td:eq(12)').text()) || 0;
            s_p15 += parseFloat($(this).find('td:eq(13)').text()) || 0;
        }
    });

    $("#total_sum_po").text(emptyIfZero(s_po));
    $("#total_sum_delivered_po_summary").text(emptyIfZero(s_dpo));
    $("#total_sum_arriving_po").text(emptyIfZero(s_apo));
    $("#total_sum_delayed_delivered").text(emptyIfZero(s_dd));
    $("#total_del_3_7").text(emptyIfZero(s_d3));
    $("#total_del_8_10").text(emptyIfZero(s_d8));
    $("#total_del_11_14").text(emptyIfZero(s_d11));
    $("#total_del_15_plus").text(emptyIfZero(s_d15));

    $("#total_sum_delayed").text(emptyIfZero(s_p));
    $("#total_3_7").text(emptyIfZero(s_p3));
    $("#total_8_10").text(emptyIfZero(s_p8));
    $("#total_11_14").text(emptyIfZero(s_p11));
    $("#total_15_plus").text(emptyIfZero(s_p15));
}

// Unified Click Handler for filtering Detailed Table by Karigar & Bracket
$(document).on('click', '#karigar_delay_analysis_table .link-row', function() {
    $('#karigar_delay_analysis_table .link-row, #karigar_delay_analysis_table .link-header').removeClass('clicked-cell');
    $(this).addClass('clicked-cell');
    
    let karigar = $(this).data('karigar');
    let bracket = $(this).data('bracket');
    
    $("#delayed_po_table tbody tr").each(function() {
        let rowKarigar = $(this).find('td:eq(4)').text();
        let rowDelay = parseInt($(this).find('td:eq(3)').attr('data-delay')) || 0;
        let diffDays = parseInt($(this).find('td:eq(10)').attr('data-diff-days')) || 0;
        
        let matchKarigar = (rowKarigar === karigar);
        let matchBracket = false;
        
        if (bracket === 'all') matchBracket = !$(this).hasClass('delivered-row') && rowDelay > 2;
        else if (bracket === 'arriving') matchBracket = !$(this).hasClass('delivered-row') && rowDelay <= 2;
        else if (bracket === 'delayed-del') matchBracket = $(this).hasClass('delivered-row') && (diffDays < 0);
        else if (bracket === 'del-3-7') matchBracket = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 3 && Math.abs(diffDays) <= 7;
        else if (bracket === 'del-8-10') matchBracket = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 8 && Math.abs(diffDays) <= 10;
        else if (bracket === 'del-11-14') matchBracket = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 11 && Math.abs(diffDays) <= 14;
        else if (bracket === 'del-15+') matchBracket = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 15;
        else if (bracket === '3-7' && rowDelay >= 3 && rowDelay <= 7) matchBracket = true;
        else if (bracket === '8-10' && rowDelay >= 8 && rowDelay <= 10) matchBracket = true;
        else if (bracket === '11-14' && rowDelay >= 11 && rowDelay <= 14) matchBracket = true;
        else if (bracket === '15+' && rowDelay >= 15) matchBracket = true;
        
        if (matchKarigar && matchBracket) $(this).show();
        else $(this).hide();
    });

    recalculate_delayed_report_footers();

    $('html, body').animate({
        scrollTop: $("#details-section").offset().top - 20
    }, 500);
});

// Click Handler for filtering Detailed Table by Bracket Header (All Karigars)
$(document).on('click', '#karigar_delay_analysis_table .link-header', function() {
    $('#karigar_delay_analysis_table .link-row, #karigar_delay_analysis_table .link-header').removeClass('clicked-cell');
    $(this).addClass('clicked-cell');

    let bracket = $(this).data('bracket');
    
    $("#delayed_po_table tbody tr").each(function() {
        let rowDelay = parseInt($(this).find('td:eq(3)').attr('data-delay')) || 0;
        let diffDays = parseInt($(this).find('td:eq(10)').attr('data-diff-days')) || 0;
        let show = false;
        
        if (bracket === 'all') show = !$(this).hasClass('delivered-row') && rowDelay > 2;
        else if (bracket === 'delayed-del') show = $(this).hasClass('delivered-row') && (diffDays < 0);
        else if (bracket === 'del-3-7') show = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 3 && Math.abs(diffDays) <= 7;
        else if (bracket === 'del-8-10') show = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 8 && Math.abs(diffDays) <= 10;
        else if (bracket === 'del-11-14') show = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 11 && Math.abs(diffDays) <= 14;
        else if (bracket === 'del-15+') show = $(this).hasClass('delivered-row') && Math.abs(diffDays) >= 15;
        else if (bracket === '3-7' && rowDelay >= 3 && rowDelay <= 7) show = true;
        else if (bracket === '8-10' && rowDelay >= 8 && rowDelay <= 10) show = true;
        else if (bracket === '11-14' && rowDelay >= 11 && rowDelay <= 14) show = true;
        else if (bracket === '15+' && rowDelay >= 15) show = true;
        
        if (show) $(this).show();
        else $(this).hide();
    });

    recalculate_delayed_report_footers();

    $('html, body').animate({
        scrollTop: $("#details-section").offset().top - 20
    }, 500);
});

$(document).on('click', '#scroll_to_summary', function() {
    $('html, body').animate({
        scrollTop: $("#summary-section").offset().top - 20
    }, 500);
});

function get_ActiveKarigars(kar_type='') {

	$.ajax({

		type: 'GET',

		url: base_url + 'index.php/admin_ret_catalog/karigar/active_list/'+kar_type,

		dataType: 'json',

		success: function (data) {

			var id = $("#karigar").val();

			// $("#id_karigar_stock").append(

			// 	$("<option></option>")

			// 		.attr("value", 0)

			// 		.text('All')

			// );

			$.each(data, function (key, item) {

				$('#id_karigar_stock').append(

					$("<option></option>")

						.attr("value", item.id_karigar)

						.text(item.karigar+' - '+ item.code)

				);

			});



			$("#id_karigar_stock").select2(

				{

					placeholder: "Select Karigar",

					allowClear: true

				});



			$('#id_karigar_stock').select2("val", "");

		}

	});

}



function get_rate_fixed(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_rate_fixed?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				data = data.response_data;



				$("#fixed_wt").html(money_format_india(parseFloat(isNaN(data.rate_fix_wt) ? 0 : data.rate_fix_wt).toFixed(3)));



				$("#fixed_avg_rate").html(money_format_india(parseFloat(data.rate_fix_rate).toFixed(2)));



				$("#fixed_amt").html(money_format_india(parseFloat(data.fixed_amount).toFixed(2)));



				//$("#gain_pure_wt").html(money_format_india(parseFloat(data.pure_wt).toFixed(3)));



				$("#fixed_bal_amt").html(money_format_india(parseFloat(data.grn_purchase_amt - data.fixed_amount ).toFixed(2)));



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}





function get_rate_unfixed(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_rate_unfixed?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				var list = data.response_data;



				var opening_value = "";

				var closing_value = "";

				var opening_wt = "";

				var closing_wt = "";



				var trHTML = '';

				var pure_wt = 0;

				var pur_ret_pur_wt = 0;

				var ratefixwt = 0;

				var balance_weight = 0;

				var tFtrHTML = "";



				if (list != null) {



					$.each(list, function (idx, item) {



						pure_wt += parseFloat(item.pure_wt);

						pur_ret_pur_wt += parseFloat(item.pur_ret_pur_wt);

						ratefixwt += parseFloat(item.ratefixwt);

						balance_weight += parseFloat(item.balance_weight);



						trHTML += '<tr>'

							+ '<td style="text-align: left;">' + item.supplier_name + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.pure_wt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.ratefixwt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.pur_ret_pur_wt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.balance_weight).toFixed(2)) + '</td>'

						'</tr>';

					});

					tFtrHTML = '<tr>'

						+ '<td style="text-align: left;font-weight:bold;">Total : </td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(pure_wt).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(ratefixwt).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(pur_ret_pur_wt).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(balance_weight).toFixed(3)) + '</td>'



					'</tr>';

				}

				$('#vendor_unfix_table > tbody').html(trHTML);

				$('#vendor_unfix_table > tfoot').html(tFtrHTML);



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}





function get_supplier_crde(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_supplier_crde?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				data = data.response_data;



				$("#credit_supp").html(money_format_india(parseFloat(data.Credit).toFixed(2)));



				$("#debit_supp").html(money_format_india(parseFloat(data.Debit).toFixed(2)));



				$("#bal_supp").html(money_format_india(parseFloat(data.Balance).toFixed(2)));



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}





function get_accountstock_inwards(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_accountstock_inwards?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				var list = data.response_data;



				var opening_value = "";

				var closing_value = "";

				var opening_wt = "";

				var closing_wt = "";



				var trHTML = '';

				var inw_gwt = 0;

				var inw_nwt = 0;

				var inw_diawt = 0;

				var balance_weight = 0;

				var tFtrHTML = "";



				if (list != null) {



					$.each(list, function (idx, item) {



						inw_gwt += parseFloat(item.inw_gwt);

						inw_nwt += parseFloat(item.inw_nwt);

						inw_diawt += parseFloat(item.inw_diawt);

						// balance_weight += parseFloat(item.balance_weight);



						trHTML += '<tr>'

							+ '<td style="text-align: left;">' + item.type + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.inw_gwt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.inw_nwt).toFixed(3)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.inw_diawt).toFixed(3)) + '</td>'

						'</tr>';

					});

					tFtrHTML = '<tr>'

						+ '<td style="text-align: left;font-weight:bold;">Total : </td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(inw_gwt).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(inw_nwt).toFixed(3)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(inw_diawt).toFixed(3)) + '</td>'

					'</tr>';

				}

				$('#inwards_details > tbody').html(trHTML);

				$('#inwards_details > tfoot').html(tFtrHTML);



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}



function get_supplier_transcation(from_date,to_date){

	$("div.overlay").css("display", "block");

	my_Date = new Date();

	$.ajax({

	     url:base_url+ "index.php/admin_ret_dashboard_api/get_supplier_transcation?nocache=" + my_Date.getUTCSeconds(),

			 dataType:"JSON",

			 data:{'from_date':from_date,'to_date':to_date,'id_branch':$('#id_branch').val(),'id_metal':$('#metal_select_dash').val()},

			 type:"POST",

			 cache:false,

			  success:function(data){



				$("div.overlay").css("display", "none");



				var list = data.response_data;



				var trHTML = '';

				var Balance = 0;

				var Debit = 0;

				var Credit = 0;

				var balance_weight = 0;

				var tFtrHTML = "";



				if (list != null) {

					var sundryFilter = $('#sundry_balance_filter').val() || 'all';



					$.each(list, function (idx, item) {

						if (sundryFilter === 'exclude_zero' && (parseFloat(item.Credit) - parseFloat(item.Debit)) === 0) { return true; }



						Credit += parseFloat(item.Credit);

						Debit += parseFloat(item.Debit);

						// Balance += parseFloat(item.Balance);

						Balance += parseFloat(parseFloat(item.Credit)-parseFloat(item.Debit));





						trHTML += '<tr>'

							+ '<td style="text-align: left;">' + item.Supplier + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.Credit).toFixed(2)) + '</td>'

							+ '<td style="text-align: right;">' + money_format_india(parseFloat(item.Debit).toFixed(2)) + '</td>'

							// + '<td style="text-align: right;">' + money_format_india(parseFloat(item.Balance).toFixed(2)) + '</td>'
							+ '<td style="text-align: right;">' + money_format_india(parseFloat(parseFloat(item.Credit)-parseFloat(item.Debit)).toFixed(2)) + '</td>'

						'</tr>';

					});

					tFtrHTML = '<tr>'

						+ '<td style="text-align: left;font-weight:bold;">Total : </td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(Credit).toFixed(2)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(Debit).toFixed(2)) + '</td>'

						+ '<td style="text-align: right;font-weight:bold;">' + money_format_india(parseFloat(Balance).toFixed(2)) + '</td>'

					'</tr>';

				}

				$('#smith_ledgere > tbody').html(trHTML);

				$('#smith_ledgere > tfoot').html(tFtrHTML);



			  },

			  error:function(error)

			  {

				 $("div.overlay").css("display", "none");

			  }

	});

}







$('#metal_select_dash').on("change", function(e) {

	if(this.value!='')

	{



		set_purchase_dashboard();



	}

	});

// Initialize Select2 for balance filters on Purchase Dashboard
$(document).ready(function () {
	if ($('#smith_balance_filter').length) {
		$('#smith_balance_filter').select2({ minimumResultsForSearch: Infinity });
		$('#smith_balance_filter').on('change', function () { set_purchase_dashboard(); });
	}
	if ($('#sundry_balance_filter').length) {
		$('#sundry_balance_filter').select2({ minimumResultsForSearch: Infinity });
		$('#sundry_balance_filter').on('change', function () { set_purchase_dashboard(); });
	}
});

	function formatNumber(numberToFormat, digit = 3, locale = 'en-IN') {
	return new Intl.NumberFormat(locale, { minimumFractionDigits: digit, maximumFractionDigits: digit }).format(numberToFormat);
}

// Profit & Loss - Rate Cut Analysis
function get_rate_cut_profit_loss(from_date, to_date, id_branch) {
    if (!id_branch) id_branch = $('#id_branch').val() || '';
    
    $.ajax({
        url: base_url + 'index.php/admin_ret_dashboard_api/get_rate_cut_profit_loss',
        method: 'POST',
        data: {
            from_date: from_date,
            to_date: to_date,
            id_branch: id_branch
        },
        dataType: 'json',
        success: function(response) {
            if(response.status && response.response_data) {
                if($.fn.DataTable.isDataTable('#rate_cut_profit_loss_table')) {
                    $('#rate_cut_profit_loss_table').DataTable().destroy();
                }
                
                $('#rate_cut_profit_loss_table').DataTable({
                    data: response.response_data,
                    columns: [
                        { data: 'rate_cut_date' },
                        // { data: 'rate_cut_ref' },
                        // { data: 'po_ref_no' },
                        // { data: 'supplier_name' },
                        // { 
                        //     data: 'pure_weight',
                        //     className: 'alignright',
                        //     render: function(data) {
                        //         return formatNumber(parseFloat(data), 3);
                        //     }
                        // },
                        { 
                            data: 'rate_cut_rate',
                            className: 'alignright',
                            render: function(data) {
                                if (parseFloat(data) == 0) return '';
                                return '<span style="color: #ff6b35; font-weight: bold;">₹' + formatNumber(parseFloat(data), 2) + '</span>';
                            }
                        },
                        { 
                            data: 'current_bullion_rate',
                            className: 'alignright',
                            render: function(data) {
                                if (parseFloat(data) == 0) return '';
                                return '<span style="color: #4a90e2; font-weight: bold;">₹' + formatNumber(parseFloat(data), 2) + '</span>';
                            }
                        },
                        { 
                            data: 'rate_deviation',
                            className: 'alignright',
                            render: function(data) {
                                if (parseFloat(data) == 0) return '';
                                var color = data > 0 ? '#28a745' : (data < 0 ? '#dc3545' : '#6c757d');
                                var sign = data > 0 ? '+' : '';
                                return '<span style="color: ' + color + '; font-weight: bold;">' + sign + formatNumber(parseFloat(data), 2) + '</span>';
                            }
                        },
                        { 
                            data: 'profit_percentage',
                            className: 'alignright',
                            render: function(data) {
                                if (parseFloat(data) == 0) return '';
                                var color = data > 0 ? '#28a745' : (data < 0 ? '#dc3545' : '#6c757d');
                                var sign = data > 0 ? '+' : '';
                                return '<span style="color: ' + color + '; font-weight: bold;">' + sign + formatNumber(parseFloat(data), 2) + '%</span>';
                            }
                        },
                        { 
                            data: 'profit_loss_status',
                            render: function(data) {
                                if(data === 'profit') {
                                    return '<span class="badge badge-success">Profit</span>';
                                } else if(data === 'loss') {
                                    return '<span class="badge badge-danger">Loss</span>';
                                } else {
                                    return '<span class="badge badge-secondary">Neutral</span>';
                                }
                            }
                        }
                    ],
                    order: [[0, 'desc']],
                    pageLength: 10,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: ['excel', 'pdf', 'print']
                });
            }
        },
        error: function() {
            alert('Error loading profit/loss data');
        }
    });
}


function check_LedgerBalanceAlert() {
	var my_Date = new Date();
	$.ajax({
		url: base_url + "index.php/admin_ret_dashboard/get_LedgerBalanceAlert?nocache=" + my_Date.getUTCSeconds(),
		dataType: "JSON",
		data: { 'id_branch': $('#id_branch').val() },
		type: "POST",
		cache: false,
		success: function (data) {
			if (data.ledger_alerts && data.ledger_alerts.length > 0) {
				var low_balance_ledgers = [];
				$.each(data.ledger_alerts, function (idx, item) {
					var current_bal = parseFloat(item.current_balance);
					var min_bal = parseFloat(item.min_balance);
					
					if (current_bal < min_bal) {
						low_balance_ledgers.push(
							"Ledger: " + item.ledger_name + "\n" +
							"Current Balance: " + formatNumber(current_bal, 2) + "\n" +
							"Minimum Balance: " + formatNumber(min_bal, 2)
						);
					}
				});
				
				if (low_balance_ledgers.length > 0) {
					var message = "Expense ledger balance is below the minimum balance.";
					
					// Use persistent toaster message as per requirement
					$.toaster({
						settings: {
							donotdismiss: ['danger']
						}
					});
					$.toaster({ priority: 'danger', title: 'Attention required', message: message });
				}
			}
		}
	});
}

// ========== MD APPROVAL PENDING ORDERS ==========
function get_md_pending_orders() {
    $.ajax({
        url: base_url + "index.php/admin_ret_dashboard_api/get_md_pending_orders",
        type: "POST",
        dataType: "json",
        success: function (data) {
            $("#md_approval_dash_table tbody").empty();
            var count = 0;
            if (data.status && data.response_data && data.response_data.length > 0) {
                count = data.response_data.length;
                $.each(data.response_data, function (i, row) {
                    var sourceClass = (row.po_source == 'Reorder') ? 'label-info' : 'label-primary';
                    var tr = '<tr>' +
                        '<td><input type="checkbox" class="md_row_chk" value="' + row.id_customerorder + '"></td>' +
                        '<td><a href="' + base_url + 'index.php/admin_ret_purchase/get_karigar_acknowladgement/' + row.id_customerorder + '" target="_blank" style="color:#3c8dbc; text-decoration:underline;">' + row.pur_no + '</a></td>' +
                        '<td>' + row.order_date + '</td>' +
                        '<td>' + (row.due_date || '') + '</td>' +
                        '<td>' + row.karigar_name + '</td>' +
                        '<td>' + (row.products || '') + '</td>' +
                        '<td class="alignright">' + (row.order_pcs || '0') + '</td>' +
                        '<td class="alignright">' + (row.order_approx_wt || '0') + '</td>' +
                        '<td>' + (row.weight_range || '') + '</td>' +
                        '<td>' + (row.order_type_label || '') + '</td>' +
                        '<td><span class="label ' + sourceClass + '">' + (row.po_source || '') + '</span></td>' +
                        '<td>' + (row.created_by_name || '') + '</td>' +
                        '<td>' +
                            '<button class="btn btn-success btn-xs md_btn_approve" data-id="' + row.id_customerorder + '" data-mobile="' + (row.karigar_mobile || '') + '" title="Approve"><i class="fa fa-check"></i></button> ' +
                            '<button class="btn btn-danger btn-xs md_btn_reject" data-id="' + row.id_customerorder + '" title="Reject"><i class="fa fa-times"></i></button>' +
                        '</td>' +
                    '</tr>';
                    $("#md_approval_dash_table tbody").append(tr);
                });
            } else {
                $("#md_approval_dash_table tbody").html('<tr><td colspan="13" class="text-center" style="color:#999;">No pending orders for approval</td></tr>');
            }
            $('#md_pending_count').text(count > 0 ? count + ' Pending' : '');
        },
        error: function (xhr) {
            console.log('MD Approval AJAX Error:', xhr.status);
            $("#md_approval_dash_table tbody").html('<tr><td colspan="13" class="text-center text-danger">Failed to load data</td></tr>');
        }
    });
}

// MD Select All
$(document).on('change', '#md_select_all_chk', function () {
    $('.md_row_chk').prop('checked', this.checked);
    mdToggleBulkBar();
});
$(document).on('change', '.md_row_chk', function () {
    $('#md_select_all_chk').prop('checked', $('.md_row_chk').length === $('.md_row_chk:checked').length);
    mdToggleBulkBar();
});
function mdToggleBulkBar() {
    $('#md_bulk_action_bar').toggle($('.md_row_chk:checked').length > 0);
}

// MD Overlay — block user actions during AJAX
function mdShowOverlay() {
    if ($('#md_approval_overlay').length === 0) {
        $('#md_approval_dash_table').closest('.box, .panel, .card, div').css('position', 'relative');
        $('<div id="md_approval_overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:999; display:flex; align-items:center; justify-content:center;">' +
            '<i class="fa fa-spinner fa-spin fa-2x" style="color:#ff9800;"></i>&nbsp; <b style="color:#333;">Processing...</b>' +
        '</div>').appendTo($('#md_approval_dash_table').closest('.box, .panel, .card, div'));
    }
}
function mdHideOverlay() {
    $('#md_approval_overlay').remove();
}


// MD Single Approve
$(document).on('click', '.md_btn_approve', function () {
    var id = $(this).data('id');
    if (!confirm('Approve this Purchase Order?')) return;
    mdShowOverlay();
    $.post(base_url + "index.php/admin_ret_purchase_approval/md_dashboard/approve", { id_customerorder: id }, function (res) {
        mdHideOverlay();
        if (res.status) {
            $.toaster({ priority: 'success', title: 'Success', message: res.msg });
            get_md_pending_orders();
        }
        else { $.toaster({ priority: 'danger', title: 'Error', message: res.msg }); }
     }, 'json').fail(function(xhr) {
        mdHideOverlay();
        $.toaster({ priority: 'danger', title: 'Error', message: 'Server error while approving order. Please try again.' });
    });
});

// MD Single Reject — Open Modal
$(document).on('click', '.md_btn_reject', function () {
    $('#md_reject_order_id').val($(this).data('id'));
    $('#md_reject_reason').val('');
    $('#md_reject_modal').modal('show');
});
$(document).on('click', '#md_btn_confirm_reject', function () {
    var idVal = $('#md_reject_order_id').val();
    var reason = $.trim($('#md_reject_reason').val());
    if (!reason) { $.toaster({ priority: 'warning', title: 'Warning', message: 'Please enter a rejection reason.' }); return; }
    $('#md_reject_modal').modal('hide');
	mdShowOverlay();

    // Check if bulk reject (comma-separated IDs)
    if (idVal.indexOf(',') !== -1) {
        var ids = idVal.split(',');
        var completed = 0;
        $.each(ids, function(i, id) {
           $.post(base_url + "index.php/admin_ret_purchase_approval/md_dashboard/reject", { id_customerorder: id, reason: reason }, function (res) {
                completed++;
                if (completed === ids.length) {
					mdHideOverlay();
                    $.toaster({ priority: 'success', title: 'Success', message: ids.length + ' order(s) rejected' });
                    get_md_pending_orders();
                    $('#md_select_all_chk').prop('checked', false);
                    mdToggleBulkBar();
                }
             }, 'json').fail(function(xhr) {
                completed++;
                if (completed === ids.length) { mdHideOverlay(); }
                $.toaster({ priority: 'danger', title: 'Error', message: 'Server error while rejecting order.' });
            });
        });
    } else {
        $.post(base_url + "index.php/admin_ret_purchase_approval/md_dashboard/reject", { id_customerorder: idVal, reason: reason }, function (res) {
            mdHideOverlay();
            if (res.status) { $.toaster({ priority: 'success', title: 'Success', message: res.msg }); get_md_pending_orders(); }
            else { $.toaster({ priority: 'danger', title: 'Error', message: res.msg }); }
        }, 'json').fail(function(xhr) {
            mdHideOverlay();
            $.toaster({ priority: 'danger', title: 'Error', message: 'Server error while rejecting order.' });
        });
    }
});

// MD Bulk Approve
$(document).on('click', '#md_btn_bulk_approve', function () {
    var selected = [];
    $('.md_row_chk:checked').each(function () { selected.push($(this).val()); });
    if (!selected.length) { $.toaster({ priority: 'warning', title: 'Warning', message: 'Select at least one order.' }); return; }
    if (!confirm('Approve ' + selected.length + ' Purchase Order(s)?')) return;
    mdShowOverlay();
    $.post(base_url + "index.php/admin_ret_purchase_approval/md_dashboard/bulk_approve", { order_ids: selected }, function (res) {
        mdHideOverlay();
        if (res.status) {
            $.toaster({ priority: 'success', title: 'Success', message: res.msg });
            get_md_pending_orders();
            $('#md_select_all_chk').prop('checked', false);
            mdToggleBulkBar();
        }
        else { $.toaster({ priority: 'danger', title: 'Error', message: res.msg }); }
     }, 'json').fail(function(xhr) {
        mdHideOverlay();
        $.toaster({ priority: 'danger', title: 'Error', message: 'Server error while approving orders. Please try again.' });
    });
});

// MD Bulk Reject — Open modal, reject all selected with same reason
$(document).on('click', '#md_btn_bulk_reject', function () {
    var selected = [];
    $('.md_row_chk:checked').each(function () { selected.push($(this).val()); });
    if (!selected.length) { $.toaster({ priority: 'warning', title: 'Warning', message: 'Select at least one order.' }); return; }
    $('#md_reject_order_id').val(selected.join(','));
    $('#md_reject_reason').val('');
    $('#md_reject_modal').modal('show');
});

