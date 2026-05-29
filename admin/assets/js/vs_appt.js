var path =  url_params();
var ctrl_page = path.route.split('/');
$('body').addClass("sidebar-collapse"); 
$(document).ready(function() {  
	if(ctrl_page[1] == "appt_approval_list"){ 
		get_apptApproval_list();
	}
	else if(ctrl_page[1] == "appt_slots"){
		editSlot = [];
		set_available_slots_tbl();
	}
	else if(ctrl_page[1] == "get_appt_request"){
		get_appt_request(); 
		
		$('#filtered_status').select2().on("change", function(e) { 
			$('#filtered_status').val(this.value);  
			get_appt_request(); 
		});
	} 
	
	$('#appt_req_list1').empty();
	$('#appt_req_list2').empty();
	$('#appt_req_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
	$('#appt_req_list2').text(moment().endOf('month').format('YYYY-MM-DD'));

	$('#appt_req-dt-btn').daterangepicker(
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
	            
               get_appt_request()
          } 
    ); 

	$("#proced").on("click",function(){ 
		$(".close_actionBtns").css("display", 'none');
		$("#save_slot").prop('disabled', false); 
		createTable();
	});  
	
	$( "#save_slot" ).on( "click", function(event) {
        $(this).prop('disabled',true);
        var postData = [];
        $('#slot_creation_tbl > tbody tr').each(function(idx, row){
        	curRow = $(this);
        	var data = [];
        	var slot_no = (isNaN(curRow.find('.slot_no').val()) || curRow.find('.slot_no').val() == '')  ? 0 : curRow.find('.slot_no').val();
        	var slot_date = (curRow.find('.slot_date').val() == '')  ? 0 : curRow.find('.slot_date').val();
        	var slot_time_from = (curRow.find('.slot_time_from').val() == '')  ? 0 : curRow.find('.slot_time_from').val();
        	var slot_time_to = (curRow.find('.slot_time_to').val() == '')  ? 0 : curRow.find('.slot_time_to').val();
        	var allowed_booking = (isNaN(curRow.find('.allowed_booking').val()) || curRow.find('.allowed_booking').val() == '')  ? 0 : curRow.find('.allowed_booking').val();
            if(slot_no > 0 && slot_date != 0 && slot_time_from != 0 && slot_time_to != 0 && allowed_booking > 0){
                postData.push({"slot_no":slot_no,"slot_date":slot_date,"slot_time_from":slot_time_from,"slot_time_to":slot_time_to,"allowed_booking":allowed_booking}); 
            }  
        })
        if(postData.length > 0 && postData.length == $("#no_of_slots").val()){
            createSlot(postData);
        }else{
            alert("Fill all required fields");
        } 
    }); 
    
    $('#feedback_status').on('keyup',function(){
	    if(this.value.length>1)
	    {        
	          $('#add_Feedback_status').prop('disabled',false);
	    }
	    else
	    {
	        $('#add_Feedback_status').prop('disabled',true);
	    }
    })
    
    $('#reject_reason').on('keyup',function(){
	    if(this.value.length>1)
	    {        
	          $('#add_reject_reason').prop('disabled',false);
	    }
	    else
	    {
	        $('#add_reject_reason').prop('disabled',true);
	    }
    })
    
	$('#add_Feedback_status').click(function(event) {
	    $(this).prop('disabled',true); 
	    var id_appt_request   = $("#id_appt_request").val();
	    var customer_feedback = $("#feedback_status").val();
	    $.ajax({
		    url:base_url+ "index.php/videoshopping_appt/appt_feedback_added",
		    data: {'customer_feedback':customer_feedback,'id_appt_request':id_appt_request},
		    type:"POST",
		    dataType:"JSON",
		    success:function(data)
		    {
		    	$("div.overlay").css("display", "none");
		    	$("#add_Feedback_status").attr("disabled", false); 
		    	$('.modal.in').modal().hide();  
		  		$('.modal-backdrop').remove();
		    	if(data == 1){
		    		msg = '<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Feedback updated successfully.</div>'; 
					$("#alert_message").html(msg); 
					get_apptApproval_list();
				}else{
					msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Failed!</strong>Unable to update feedback..</div>'; 
					$("#alert_message").html(msg); 
				} 
		    }
	    });
	});    
	//Appt Request Reject Reason For Customers -modal // 

	$('#add_reject_reason').click(function(event) {
	    $(this).prop('disabled',true);
	    $.ajax({
		    url:base_url+ "index.php/videoshopping_appt/appt_req_reject",
		    data: {'reject_reason':$("#reject_reason").val(),'id_appt_request':$("#id_appt_request_rj").val()},
		    type:"POST",
		    dataType:"JSON",
		    success:function(data)
		    {
		    	$("div.overlay").css("display", "none");
		    	$("#add_reject_reason").attr("disabled", false); 
		    	$('#update_reject_reason').modal('toggle');
		    	if(data == 1){
		    		msg = '<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Appointment rejected successfully.</div>'; 
					$("#alert_message").html(msg); 
					get_apptApproval_list();
				}else{
					msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Failed!</strong>Unable to rejected..</div>'; 
					$("#alert_message").html(msg); 
				} 
		    }
	    });
	});
	
	$('#upd_slot').click(function(event) {
		if($("#slot_no").val() != '' && $("#slot_date").val() != '' && $("#slot_time_from").val() != '' && $("#slot_time_to").val() != '' && $("#allowed_booking").val()  != ''){
			$(this).prop('disabled',true);
			update_slot();
		}else{
			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Failed!</strong>All fields are required..</div>'; 
			$("#chit_alert").html(msg); 
		} 
	    
	});
	  
	//$(document).on("click", ".req_id", function(event){
	$('#completed').click(function(event) {
		var postData = [];
		$("input[name='id_appt_request']:checked").each(function() {	
		  postData.push( $(this).val());
		}); 
		if(postData.length > 0){ 
			updateAsCompleted(postData); 
		}else{
			alert("Select atleast 1 appointment");
		} 
	}); 
	
	$(document).on("change", ".available_emps", function(event){
		var postData = [];
		$("input[name='id_employee']:checked").each(function() {			  
			var data = {
				'id_employee' : $(this).val()
			}; 
		  postData.push(data);
		}); 
		if(postData.length > 0){
			$(".tot_emp_sel").html(postData.length);
			$("#req_allocate").attr("disabled", false); 
		}else{
			$("#req_allocate").attr("disabled", true); 
		}
	}); 
	
	$(document).on("change", ".slot_time_from", function(event){
		var id = $(this).attr("id"); 
		var res = id.split("_"); 
		var fromDate = $(this).val();
		if(fromDate != ''){
			var toDate = moment.utc(fromDate,'HH:mm').add(1,'hour').format('HH:mm');   
			$("#timeTo_"+res[1]).val(toDate); 
		} 
	}); 
	
	$(document).on("change", ".ed_slot_time_from", function(event){
		var fromDate = $(this).val();
		if(fromDate != ''){
			var toDate = moment.utc(fromDate,'HH:mm').add(1,'hour').format('HH:mm');   
			$(".ed_slot_time_to").val(toDate); 
		} 
	}); 
	
	$(document).on("change", ".ed_allowed_booking", function(event){
		if($(this).val() < $(".ed_userbookings").val()){
			$(this).val($(".ed_userbookings").val());
			msg = '<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Having '+$(".ed_userbookings").val()+' user bookings</div>';
			$("#chit_alert").html(msg); 
		} 
	}); 
	
	$('#req_allocate').click(function(event) {
		$(this).prop('disabled',true); 
	    var postData = [];
		$("input[name='id_employee']:checked").each(function() {	 
		  postData.push($(this).val());
		});
		if(postData.length > 0){
			allocate_emp(postData,$("#id_appt_request_ae").val(),$("#preferred_slot_ae").val());
		}else{ 
			   	 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Select atleast 1 employee</div>';
			   	 $("#appt_req_model #chit_alert").html(msg);
		} 
	}); 
	
	$('#cancel').on('click',function(){ 
	    $("#req_allocate").attr("disabled", false); 
	});
    
});

function createTable(){
    var a;
    a = $("#no_of_slots").val();
    if (a == "") {
        alert("Please enter SLOTS");
    } else {
        var rows = "<thead><th>Slot No</th><th>Date</th><th>Slot Time From</th><th>Slot Time To</th><th>Allowed Booking</th></thead>";
        var d = new Date(); 
		var month = d.getMonth()+1;
		var day = d.getDate(); 
		var minDate = d.getFullYear() + '-' +
		    (month<10 ? '0' : '') + month + '-' +
		    (day<10 ? '0' : '') + day;
		    
        for (var i = 0; i < a; i++) {
            rows += "<tr id='" + (i+1) + "'><td><input type='number' class='slot_no' value='" + (i+1)+"' name='" + "slot".concat(i+1) + "' style='width: 100px;'></td><td><input type='date' min='"+minDate+"' class='form-control slot_date' name='" + "date".concat(i+1) + "' style='width: 200px;'></td><td><input type='time'  class='form-control slot_time_from' name='" + "timeFrom_".concat(i+1) + "' id='" + "timeFrom_".concat(i+1) + "' style='width: 200px;'></td><td><input type='time'  class='form-control slot_time_to' name='" + "timeTo_".concat(i+1) + "'  id='" + "timeTo_".concat(i+1) + "' style='width: 200px;'></td><td><input type='number' value=1 class='form-control allowed_booking' style='width: 100px;' id='" + "allowed_booking".concat(i+1) + "' name='" + "allowed_booking".concat(i+1) + "' style='width: 200px;'></td></tr>";
        }
        document.getElementById("slot_creation_tbl").innerHTML = rows;
        $("#save_blk").css("display","block");
    }
}

function createSlot(postData){
    $("div.overlay").css("display", "block");
    $.ajax({
        url:base_url+ "index.php/videoshopping_appt/create_slot",
        data:{"slot_data":postData},
        type:"POST",
        dataType:"JSON",
        async:false,
		 	  success:function(data){
		            location.reload(false);
		   			$("div.overlay").css("display", "none"); 
			  },
			  error:function(error)  
			  {
				 $("div.overlay").css("display", "none"); 
			  }
    });
} 

function set_available_slots_tbl()
{ 
	$("div.overlay").css("display", "block"); 
	my_Date = new Date(); 
	$.ajax({
		url: base_url+'index.php/videoshopping_appt/ajax_available_slots/?nocache=' + my_Date.getUTCSeconds(), 
		dataType:"JSON",
		type:"POST",
		success:function(data){ 
			$("div.overlay").css("display", "none");  
			var oTable = $('#available_slots').DataTable(); 
			if((data.slots).length > 0){
				editSlot = data.slots;
				access = data.access;
				oTable.clear().draw();
				oTable = $('#available_slots').dataTable({
				    "bDestroy": true,
				    "bInfo": true,
				    "bFilter": true,
				    "bSort": true,				                
				    "dom": 'lBfrtip',				                
				    "buttons" : ['print'],
				    "aaData": data.slots,
				    "order": [[ 0, "desc" ]],
				    "pageLength":25,
					"aoColumns": [			 
						{ "mDataProp": "id_appointment_slot" },			                
						{ "mDataProp": "slot_no" },			                
						{ "mDataProp": "slot_date" },		
						{ "mDataProp": "slot_time_from" },
						{ "mDataProp": "slot_time_to" },
						{ "mDataProp": "allowed_booking" },
						{ "mDataProp": "date_add" },
						{ "mDataProp": function ( row, type, val, meta ){ 
							delete_url = (access.delete=='1' ? base_url+'index.php/videoshopping_appt/delete_slot/'+row.id_appointment_slot : '#' );  
		                	//return '<button type="button"  class="btn btn-primary" onClick="open_slot_edit('+row.id_appointment_slot+',"'+row.slot_date+'",'+row.slot_no+',"'+row.slot_time_from+'","'+row.slot_time_to+'",'+row.allowed_booking+')"></button>'; 
		                	return '<button type="button"  class="btn btn-primary" onClick="open_slot_edit('+row.id_appointment_slot+')"><i class="fa fa-edit" ></i> Edit</li></button>'+(row.userbookings == null || row.userbookings == 0  ?'<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a>':''); 
						}},
					] 
				});
			} 
		},
		error:function(error)  
		{
			$("div.overlay").css("display", "none"); 
		}	 
	});
}

//function open_slot_edit(id,date,slot_no,from,to,allowed_book){ 
function open_slot_edit(id_appointment_slot){ 
	$('#edit_slot').modal({
							backdrop: 'static',
							keyboard: false
						});
	var d = new Date(); 
	var month = d.getMonth()+1;
	var day = d.getDate(); 
	var minDate = d.getFullYear() + '-' +
	    (month<10 ? '0' : '') + month + '-' +
	    (day<10 ? '0' : '') + day;
	var data = [];
	    
	editSlot.forEach(function(e) {
	  if (e.id_appointment_slot == id_appointment_slot){
	  	data = e;
	  } 
	}); 
	
	var date = new Date(data.slot_date); 
	var m = date.getMonth()+1;
	var d = date.getDate(); 
	var slot_date = date.getFullYear() + '-' +
	    (d<10 ? '0' : '') + d + '-' +
	    (m<10 ? '0' : '') + m;
	    
	$("#id_appointment_slot").val(data.id_appointment_slot);  
	$("#slot_date").val(slot_date); 
	$("#slot_date").attr('min',minDate);
	$("#slot_time_from").val(data.time_from);  
	$("#slot_time_to").val(data.time_to);
	$("#slot_no").val(data.slot_no); 
	$("#allowed_booking").val(data.allowed_booking);
	if(data.userbookings != null && data.userbookings > 0 ){
		$("#slot_date").attr("readonly",true);  
		$("#slot_time_from").attr("readonly",true);  
		$("#slot_time_to").attr("readonly",true);
		$("#userbookings").val(data.userbookings);
	}else{
		$("#slot_date").attr("readonly",false);  
		$("#slot_time_from").attr("readonly",false);  
		$("#slot_time_to").attr("readonly",false);
		$("#userbookings").val(0);
	}  
}

function get_appt_request()
{
	var status = $('#filtered_status').val();
	var from_date=$('#appt_req_list1').text();
	var to_date  =$('#appt_req_list2').text();
	
	$("div.overlay").css("display", "block"); 
	my_Date = new Date();
	postData = (from_date !='' && to_date !='' ? {'from_date':from_date,'to_date':to_date,'status':status}:{'status':$('#filtered_status').val()});
	$.ajax({
		url: base_url+'index.php/videoshopping_appt/ajax_appt_requests/?nocache=' + my_Date.getUTCSeconds(), 
		data: (postData),
		dataType:"JSON",
		type:"POST",
		success:function(data){ 
			set_appt_request(data);
			$("div.overlay").css("display", "none"); 
		},
		error:function(error)  
		{
			$("div.overlay").css("display", "none"); 
		}	 
	});
}

function toTimestamp(strDate){
 var datum = Date.parse(strDate);
 return datum/1000;
}

function set_appt_request(data)
{
    console.log(data);
    var appt_request = data;
	var oTable = $('#appt_request_list').DataTable();
	oTable.clear().draw();
	oTable = $('#appt_request_list').dataTable({
	    "bDestroy": true,
	    "bInfo": true,
	    "bFilter": true,
	    "bSort": true,				                
	    "dom": 'lBfrtip',				                
	    "buttons" : ['excel','print'],
	    "aaData": appt_request,
	    "order": [[ 0, "desc" ]],
	    "pageLength":25,
		"aoColumns": [
		/*	{ "mDataProp": function ( row, type, val, meta ){ 
				var showCheckbox = false;
				if(row.alloted_slot != null){ 
					var today = new Date().getTime();
					var slot_date = toTimestamp(row.alloted_slot);
					if(row.status == 'Alloted') { 
						var showCheckbox =  (slot_date <= today) 
					}
				}
				
				if(row.status == 'Alloted' && showCheckbox){  
					return '<input type="checkbox" name="id_appt_request" id="id_appt_request" class="req_id" value="'+row.id_appt_request+'"/> '+row.id_appt_request;
				}else{
					return row.id_appt_request;
				}
			}}, 	*/
			{ "mDataProp": function ( row, type, val, meta ){ 
			    if(row.status != 'Completed'){  
					return '<input type="checkbox" name="id_appt_request" id="id_appt_request" class="req_id" value="'+row.id_appt_request+'"/> '+row.id_appt_request;
				}else{
					return row.id_appt_request;
				}
			}},
			{ "mDataProp": "name" },				                
			{ "mDataProp": "mobile" },
			{ "mDataProp": "scheduled_time" },
			{ "mDataProp": "pref_category" }, 
			{ "mDataProp": "pref_item" }, 
			{ "mDataProp": "email" },
			{ "mDataProp": "location" },
			{ "mDataProp": "created_on" },
			{ "mDataProp": "prefered_lang" },
			{ "mDataProp": "status" },
			{ "mDataProp": "remarks" },
			{ "mDataProp": function ( row, type, val, meta ){
				console.log(row)
				edit = '<li><a href="#" class="btn-edit" onClick="update_app_req_status(' + row.id_appt_request + ', \'' + row.remarks + '\', \'' + row.sts_code + '\')"><i class="fa fa-edit"></i> Update Status</a></li>';
				action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
				edit+'<li></ul></div>';
				return action_content;
			}},
			
		/*	{ "mDataProp": "whats_app_no" },  
            { "mDataProp": "alloted_emp" },   
        	{ "mDataProp": "pref_slot" },    
	        { "mDataProp": "description" },   
			{ "mDataProp": "customer_feedback" },
			{ "mDataProp": "reject_reason" },   */
			
			/*{ "mDataProp": function ( row, type, val, meta ){  
                	return row.status+" "+(row.status == 'Completed' && row.customer_feedback == '' ? ' <button type="button"  class="btn btn-warning" onClick="update_feedback_status('+row.id_appt_request+')">Get Feedback</button>':''); 
			}},*/
		] 
	});	
}
function get_apptApproval_list()
{
	$("div.overlay").css("display", "block"); 
	my_Date = new Date();
	postData = {'status':0};
	$.ajax({
		url: base_url+'index.php/videoshopping_appt/approvalApptRequests/?nocache=' + my_Date.getUTCSeconds(), 
		data: (postData),
		dataType:"JSON",
		type:"POST",
		success:function(data){ 
			set_apptApproval_list(data);
			$("div.overlay").css("display", "none"); 
		},
		error:function(error)  
		{
			$("div.overlay").css("display", "none"); 
		}	 
	});
}
   
function set_apptApproval_list(data)
{
    var appt_request = data;
	var oTable = $('#new_appt_req').DataTable();
	oTable.clear().draw();
	oTable = $('#new_appt_req').dataTable({
	    "bDestroy": true,
	    "bInfo": true,
	    "bFilter": true,
	    "bSort": true,				                
	    "dom": 'lBfrtip',				                
	    "buttons" : ['excel','print'],
	    "aaData": appt_request,
	    "order": [[ 0, "desc" ]],
	    "pageLength":25,
		"aoColumns": [
			{ "mDataProp": "id_appt_request" },    
			{ "mDataProp": "name" },				                
			{ "mDataProp": "mobile" },
			{ "mDataProp": "whats_app_no" },
			{ "mDataProp": "location" },
			{ "mDataProp": "pref_category" },
			{ "mDataProp": "pref_item" }, 
			{ "mDataProp": function(row,type,val,meta)
        	{
				if(row.available_slots > 0 ){
					return row.pref_slot+" <br/><span class='text-green text-small'>Available</span>";	
				}else{
					return row.pref_slot+" <br/><span class='text-red'>Unavailable</span>";	
				}
			}}, 
			{ "mDataProp": "status" },
			{ "mDataProp": "created_on" },
			/*{ "mDataProp": "description" },
			{ "mDataProp": "customer_feedback" },
			{ "mDataProp": "reject_reason" },*/
			{ "mDataProp": function ( row, type, val, meta ){
    	    	if(row.status == 'Open' ){
    	    		if(row.available_slots > 0 ){ // 0 -> Not alloted
                		return '<button type="button" class="btn btn-success" style="background-color: #00a65a; border-color: #008d4c; padding: 5px;" onClick="appt_req_model('+ row.id_appt_request + ', ' + row.preferred_slot + ')">Fix</button>' + '  ' + '<button type="button" class="btn btn-danger" style="background-color: #dd4b39; border-color: #d73925; padding: 5px;"  onClick="update_reject_reason('+ row.id_appt_request + ')">Reject</button>';
                	}else{
						return '<button type="button" class="btn btn-danger" style="background-color: #dd4b39; border-color: #d73925; padding: 5px;"  onClick="update_reject_reason('+ row.id_appt_request + ')">Reject</button>';	
					}
    	    	}
    	    	else if(row.status == 'Completed' ){
                	return '<button type="button"  class="btn btn-warning" style="background-color: #f39c12; padding: 3px;" onClick="update_feedback_status('+row.id_appt_request+')">Cus Feedback</button>';
    	    	}
				else if( row.status == 'Closed' ){
					return ' Feedback Taken ';
				}
				else if( row.status == 'Rejected' ){
					return ' Rejected ';
				}
			}},
		] 
	});	
}
 
function appt_req_model(id_appt_request,preferred_slot){
	$("#id_appt_request_ae").val(id_appt_request); 
	$("#preferred_slot_ae").val(preferred_slot); 
	$('.available_emp').html(available_empData(id_appt_request,preferred_slot)); 
	$('#appt_req_model').modal({
						backdrop: 'static',
						keyboard: false
 						}); 
}

function update_reject_reason(id_appt_request){
	$('#update_reject_reason').modal({
	            backdrop: 'static',
	              keyboard: false
	                 });
	$("#id_appt_request_rj").val(id_appt_request); 
}


function update_feedback_status(id_appt_request){
	$('#update_feedback_status').modal({
									backdrop: 'static',
									keyboard: false
								});
	$("#id_appt_request_cf").val(id_appt_request); 
} 

function available_empData(id_appt_request,preferred_slot)
{
	var chekbox="";
    $("div.overlay").css("display", "block");
    $.ajax({
        url:base_url+ "index.php/videoshopping_appt/emp_available/"+id_appt_request+"/"+preferred_slot,
        data: {'id_appt_request':id_appt_request,'preferred_slot':preferred_slot},
        dataType:'JSON',
        type:'POST',
        async:false,
        success:function(data){ 
			$.each(data, function (type, val) {
				chekbox+='<tr><td><input type="checkbox" name="id_employee" id="id_employee" class="available_emps" value="'+val.id_employee+'"/> '+val.firstname+'</td></tr>'; 
			});
       	 	$("div.overlay").css("display", "none");
       	 	return chekbox;	 
        },
        error:function(error)  
        {
            $("div.overlay").css("display", "none"); 
        }	 
    }); 
     $("div.overlay").css("display", "none"); 
	 return chekbox;
} 

function allocate_emp(data,id_appt_req,preferred_slot){
	$("div.overlay").css("display", "block");
	$.ajax({
	    url:base_url+ "index.php/videoshopping_appt/allocate_appt",
	    data: {"data":data,"id_appt_request":id_appt_req,"slot":preferred_slot},
	    type:"POST",
	    dataType:"JSON",
	    success:function(data)
	    {
	    	$("div.overlay").css("display", "none");
	    	$("#req_allocate").attr("disabled", false);  
	  		$('#appt_req_model').modal('toggle');
	    	if(data == 1){
	    		msg = '<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Appointment fixed and employee allocated successfully.</div>'; 
				$("#alert_message").html(msg);  
			}else{
				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Failed!</strong>Unable to fix appointment & allot employee..</div>'; 
				$("#alert_message").html(msg); 
			}
			get_apptApproval_list();
	    }
    });
}

function updateAsCompleted(data){
	$("#completed").attr("disabled", true); 
	$("div.overlay").css("display", "block");
	$.ajax({
	    url:base_url+ "index.php/videoshopping_appt/appt_completed",
	    data: {"data":data},
	    type:"POST",
	    dataType:"JSON",
	    success:function(data)
	    {
	    	$("div.overlay").css("display", "none");
	    	$("#completed").attr("disabled", false); 
	    	if(data > 0){
	    		msg = '<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>'+ data +' Appointment(s) completed successfully.</div>'; 
				$("#alert_message").html(msg); 				
			}else{
				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Failed!</strong>Unable to update appointment..</div>'; 
				$("#alert_message").html(msg); 
			}
			get_appt_request();
	    }
    });
}
function update_app_req_status(id,remarks,status){
	$('#update_appreq_status').modal('show', {backdrop: 'static'});
	$("#id_app_req").val(id);
	$("#app_reqremarks").val(remarks); 
	$("#filtered_req_status").val(status);
} 
function update_slot(){
	$("div.overlay").css("display", "block");
    $.ajax({
	    url:base_url+ "index.php/videoshopping_appt/update_slot",
	    data: {'slot_no':$("#slot_no").val(),'slot_date':$("#slot_date").val(),'slot_time_from':$("#slot_time_from").val(),'slot_time_to':$("#slot_time_to").val(),'allowed_booking':$("#allowed_booking").val(),'id_appointment_slot':$("#id_appointment_slot").val()},
	    type:"POST",
	    dataType:"JSON",
	    success:function(data)
	    {
	    	$("div.overlay").css("display", "none");
	    	$("#upd_slot").attr("disabled", false); 
	    	$('#edit_slot').modal('toggle');
	    	if(data == 1){
	    		msg = '<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Success!</strong>Slot Updated successfully.</div>'; 
				$("#alert_message").html(msg); 
				set_available_slots_tbl();
			}else{
				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Failed!</strong>Unable to update slot..</div>'; 
				$("#alert_message").html(msg); 
			} 
	    }
    });
}
 

$("#add_app_req_status").click(function(){    
	$("div.overlay").css("display", "block"); 
    $("#add_enq_status").prop('disabled',true);
    var postData = {"id_app_req":$("#id_app_req").val(),"filtered_req_status":$("#filtered_req_status").val(),"app_reqremarks":$("#app_reqremarks").val(),"id_app_req":$("#id_app_req").val()};
	console.log(postData)
    $.ajax({
        url:base_url+ "index.php/videoshopping_appt/updateappreq_status/UpdateStatus",
        dataType:"JSON",
        data : postData,
        type:"POST",
        async:false,
        success:function(data){
           
            window.location.reload();
        },
        error:function(error)  
        {
            $("div.overlay").css("display", "none"); 
        }	 
    }); 
 });