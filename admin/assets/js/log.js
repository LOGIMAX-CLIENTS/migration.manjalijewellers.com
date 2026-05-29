var path =  url_params();

var ctrl_page = path.route.split('/');

$(document).ready(function() {

	switch(ctrl_page[0])

	{
		case 'log':

			switch (ctrl_page[1]) {

				case 'list':

					get_log_list();

					$('#log-dt-btn').daterangepicker({

						ranges: {

						'Today': [moment(), moment()],

						'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],

						'Last 7 Days': [moment().subtract(6, 'days'), moment()],

						'Last 30 Days': [moment().subtract(29, 'days'), moment()],

						'This Month': [moment().startOf('month'), moment().endOf('month')],

						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month'									)]

						},

						startDate: moment().subtract(29, 'days'),

						endDate: moment()

					},

					function (start, end) {

						$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

						get_log_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))

					}

					);   

				break;

				case 'detail':

					get_logDetail_list();

					$('#log-dt-btn').daterangepicker({

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

					get_logDetail_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))

					}

					); 

				break;

				default:

				break;

			}
		
		break;

		case 'form_logger':

			$('#form_log_date1').text(moment().clone().subtract(30, 'days').format('YYYY-MM-DD'));

			$('#form_log_date2').text(moment().format('YYYY-MM-DD'));

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

			function (start, end) {

				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

				$('#form_log_date1').text(start.format('YYYY-MM-DD'));

				$('#form_log_date2').text(end.format('YYYY-MM-DD')); 

			}

			); 

			get_employee("");

			get_formlogger_list();

			$(document).on("click", "#search", function() {

				get_formlogger_list();

			});

			$(document).on("click", ".clear_date", function() {

				$('#form_log_date1, #form_log_date2').html("");
			
			});

		break;

		default:

		break;

	} 

});



//functions

function get_log_list(from_date="",to_date="")

{

	my_Date = new Date();

	 $("div.overlay").css("display", "block"); 

	$.ajax({

			  url:base_url+"index.php/log/ajax_list?nocache=" + my_Date.getUTCSeconds(),

			 data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date}: ''),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

			   			set_log_list(data);

			   			 $("div.overlay").css("display", "none"); 

					  },

					  error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

						alert(1);

					  }	 

			      });

}

function set_log_list(data)	

{

   var logs = data.logs;

   console.log(logs);

   var access = data.access;

   

   var oTable = $('#log_list').DataTable();

   $("#total_logs").text(logs.length);

   oTable.clear().draw();

   	 if (logs!= null && logs.length > 0)

	 {

	 	oTable = $('#log_list').dataTable({

				                "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,

				                "aaData": logs,

				                "order": [[ 0, "desc" ]],

				                "aoColumns": [{ "mDataProp": "id_log" },

					                { "mDataProp": "emp_name" },

					                { "mDataProp": "login_on" },

					                { "mDataProp": "logout_on" },

					                { "mDataProp": function ( row, type, val, meta ) {

					                	 id= row.id_log;

					                	 url=(access.edit=='1' ? base_url+'index.php/log/detail/'+id : '#' );

					                	        	        return "<a class='btn btn-primary' href='"+url+"'><i class='fa fa-search'></i> View</a>";

					                	}

					               

					            }] 



				            });	

	 }  

	 

}

function get_logDetail_list(from_date="",to_date="")

{

	my_Date = new Date();

	 $("div.overlay").css("display", "block"); 

	$.ajax({

			  url:base_url+"index.php/log/ajax_list_detail?nocache=" + my_Date.getUTCSeconds(),

			 data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date}: ''),

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

			   			set_logDetai_list(data);

			   			 $("div.overlay").css("display", "none"); 

					  },

					  error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

						alert(1);

					  }	 

			      });

}

function set_logDetai_list(data)	

{

   var logs = data.logs;

   var oTable = $('#logDetail_list').DataTable();

   $("#total_logs").text(logs.length);

   oTable.clear().draw();

   	 if (logs!= null && logs.length > 0)

	 {

	 	oTable = $('#logDetail_list').dataTable({

				                 "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,

				                "aaData": logs,

				                "order": [[ 0, "desc" ]],

				                "aoColumns": [{ "mDataProp": "id_log_detail" },

					                { "mDataProp": "event_date" },

					                { "mDataProp": "module" },

					                { "mDataProp": "operation" },

					                { "mDataProp": "record" },

					                { "mDataProp": "remark" }           

					            ] 



				            });	

	 }  

}

function get_employee(id_branch)

	{

		$('#emp_select option').remove();

		my_Date = new Date();

		$.ajax({

		url:base_url+ "index.php/admin_ret_estimation/get_employee?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

        data: {'id_branch':id_branch},

        type:"POST",

        dataType:"JSON",

        success:function(data)

        {

           var id_employee=$('#id_employee').val();

           emp_details=data;

           $.each(data, function (key, item) {

                	 	$("#emp_select").append(

                	 	$("<option></option>")

                	 	.attr("value", item.id_employee)

                	 	.text(item.emp_name)

                	 	);

                 	});

             	$("#emp_select").select2({

            	 	placeholder: "Select Employee",

            	 	allowClear: true

             	});

         	    $("#emp_select").select2("val",(id_employee!='' && id_employee>0?id_employee:''));

         	    $(".overlay").css("display", "none");

        },

        error:function(error)

        {

        }

    	});

	}

	function get_formlogger_list() {
	
		let from_date = $('#form_log_date1').html() != "" ? $('#form_log_date1').html() : "-";
		
		let to_date = $('#form_log_date2').html() != "" ? $('#form_log_date2').html() : "-";
	
		let emp_id = $('#emp_select').val() > 0 ? $('#emp_select').val() : 0;
		
		$("div.overlay").css("display", "block");
		
		my_Date = new Date();
		
		$.ajax({
			
			url: base_url+"index.php/form_logger/ajax/"+emp_id+"/"+from_date+"/"+to_date+"?nocache=" + my_Date.getUTCSeconds(),
			
			type:"GET",
			
			dataType: 'json',
			
			cache:false,
			
			success:function(data)
			{
				data = data.list;
			
				$("div.overlay").css("display","add_newstone");
	
				var oTable = $('#form_logger_list').DataTable();
				
				oTable.clear().draw();
				
				if (data!= null && data.length > 0) {
				
					oTable = $('#form_logger_list').dataTable({
					
							"bDestroy": true,
						
							"bInfo": true,
						
							"bFilter": true,
						
							"bSort": true,
						
							"order": [[ 0, "desc" ]],
						
							"aaData"  : data,
						
							"aoColumns": [

								{ "mDataProp":function(row,type,val,meta){
	
									return "<span class='log_row_id'>"+row.log_id+"</span>";
								
								}},
	
								{ "mDataProp": "log_datetime" },
	
								{ "mDataProp": "emp_name" },

								{ "mDataProp":function(row,type,val,meta){
									
									return $.trim(row.log_form) != "" ? row.log_form.replace(/(^\w|\s\w)/g, function(match) { return match.toUpperCase(); }) : "";
								
								}},

								{ "mDataProp":function(row,type,val,meta){
									
									return $.trim(row.log_operation) != "" ? row.log_operation.replace(/(^\w|\s\w)/g, function(match) { return match.toUpperCase(); }) : "";
								
								}},
	
								{ "mDataProp": "log_url" },
	
								{ "mDataProp": "log_ip" },
	
								{ "mDataProp":function(row,type,val,meta){
	
									return "<span class='view_log_details'>View Details</span>";
								
								}},
	
							   ],
					   });
	
				   }
	
				   $("div.overlay").css("display", "none");
	
		   },
	
			error:function(error) {
	
				$("div.overlay").css("display", "none");
	
			}
		});
	
	}

	$(document).on("click",".view_log_details", function() {

		let log_id = $(this).closest("tr").find(".log_row_id").text();

		$("div.overlay").css("display", "block");

		my_Date = new Date();
		
		$.ajax({
			
			url: base_url+"index.php/form_logger/get_form_log_data/"+log_id+"?nocache=" + my_Date.getUTCSeconds(),
			
			type:"GET",
			
			dataType: 'json',
			
			cache:false,
			
			success:function(data)
			{

				let parsedJson = formatJsonString(JSON.parse(data.log_data));

				$("#log_info").html(parsedJson);
				
				$('#log-info-modal').modal('show');

				$("div.overlay").css("display", "none");

		   	},
	
			error:function(error) {
	
				$("div.overlay").css("display", "none");
	
			}
		});
	

	});


	function formatJsonString(data) {

		var valueString = "";
	
		$.each(data, function(key, item) {

			let itemData = item;

			let isValidJson = true;

			if (typeof itemData === 'string') {

				if(isStringifiedJSON(itemData)) {

					itemData = JSON.parse(itemData);

				} else {

					isValidJson = false;

				}

			}

			if(isValidJson) {

				var keysArray = itemData !== null ? Object.keys(itemData) : 0;
	
				if (keysArray.length > 0) {

					itemData = formatJsonString(itemData);

				}

			}

			valueString += '<span class="style-key">'+key+'</span>' + ": " + itemData + "<br>";
	
		});
	
		return valueString;
	}

	function isStringifiedJSON(str) {
		
		try {
		
			JSON.parse(str);
		
			return true;
		
		} catch (e) {
		
			return false;
		
		}
	  
	}