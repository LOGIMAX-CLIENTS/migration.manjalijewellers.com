var path =  url_params();
var ctrl_page = path.route.split('/');
$(document).ready(function() {
	if(ctrl_page[1]=='service' && ctrl_page[2]=='list')
	{
	set_smsServices_list();	
	}
		$("#sms,#email").bootstrapSwitch();	
		
		$('#select_all').click(function(event) {
			
		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
			event.stopPropagation();
		});
  
   
		$('#confirm-delete .btn-cancel').on('click', function(e) {
			$('.btn-confirm').attr('href',"#");
		}); 
		
		$('#sms_msg').on('blur onchange',function(e){
			 if(this.value.length >200)
   	 	  {
   	   		   $(this).val('');
	   		   $(this).attr('placeholder', 'Enter characters below 200 ')
	  	  }
		});
		$('#sms_footer').on('blur onchange',function(e){
			 if(this.value.length >7)
   	 	  {
   	   		   $(this).val('');
	   		   $(this).attr('placeholder', 'Enter characters below 7 ')
	  	  }
		});
		if($('#scheme').length>0)
		{
			get_schemes();
		}
			
		if($('#compose-textarea').length>0)
		{
			 $("#compose-textarea").wysihtml5();
		}
		
		$('#scheme').select2()
        .on("change", function(e) {
          if(this.value != "")
		  {
			  $("#scheme_val").val(this.value);
		  	 
			  get_customer_list(this.value);
		  }
		  
        });
        
        
		
		
		
		
		
		
      /* $("#btn-send").click(function(){

      	   // var selected_customers = get_selected_tablerows("customer_lists");
      	   	 var selected = [];
				$("input[name='mobile[]']:checked").each(function() {
				  selected.push($(this).val());
				});
      	    var msg = $("#message").val();
      	    if( msg!=""){
		      	  	if(selected.length != '0')	{
      	    my_Date = new Date();
			 $("div.overlay").css("display", "block"); 
			$.ajax({
					  url:base_url+ "index.php/sms/send/group_message?nocache=" + my_Date.getUTCSeconds(),
					 data: ({'customer':selected,'message':msg}),
					 type:"POST",
					 success:function(data){
					 	 $("div.overlay").css("display", "none"); 
					 	 if(data=='success'){
						 	 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Group sms sent successfully.</div>';
						 }
						else{
						 	 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
						 }
						 $('#chit_alert').html(msg);
						  },
					  error:function(error){
							 $("div.overlay").css("display", "none"); 
						  }	 
						 
				  });
				   }else{
						  	
						   msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please select customers.</div>';
							$('#chit_alert').html(msg);	
							}
						  }
						  else{
						  	 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please enter message.</div>';
							$('#chit_alert').html(msg);
						  }

      });  */
	  
             
      /* $("#btn-send-email").click(function(){

      	   // var selected_customers = get_selected_tablerows("customer_lists");
      	    var selected = [];
				$("input[name='email[]']:checked").each(function() {
				  selected.push($(this).val());
				});
			
			 var subject = $("#subject").val();
      	    var msg = $("#compose-textarea").val();
		      	  if(subject!="" && msg!=""){
		      	  	if(selected.length != '0')	{
		      	    my_Date = new Date();
						 $("div.overlay").css("display", "block"); 
					$.ajax({
							  url:base_url+ "index.php/email/send/group_message?nocache=" + my_Date.getUTCSeconds(),
							 data: ({'customer':selected,"subject":subject,'message':msg}),
							 type:"POST",
							 success:function(data){
							 	 $("div.overlay").css("display", "none"); 
							 	 if(data=='success'){
								 	 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Group Mail sent successfully.</div>';
								 }
								else{
								 	 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
								 }
									
								$('#chit_alert').html(msg);
									   			
									  },
								  error:function(error){
									   $("div.overlay").css("display", "none"); 
									  }	 
						  });
						  }else{
						  	
						   msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please select customers.</div>';
							$('#chit_alert').html(msg);	
							}
						  }
						  else{
						  	 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong> Subject and Message fields should not be empty.</div>';
							$('#chit_alert').html(msg);
						  }
						  
      }); */
	  
//selected bassed to group_message

	$('#send_sms_to').on('change', function() {
		 var val1='all_cus';
		 var val2='sel_cus';
		 var val3='sch_cus';		 
		 var val4=$(this).val();
		 if(val1 ==val4){	
		 	var oTable = $('#customer_lists').DataTable();
			oTable.clear().draw();
			$('#scheme').prop('disabled',true);
			get_allcustomer_list();
		 }else if(val2 ==val4){	
		 	var oTable = $('#customer_lists').DataTable();
			oTable.clear().draw();
			$('#scheme').prop('disabled',true);
			get_allcustomer_list();
		 }else{
			 var oTable = $('#customer_lists').DataTable();
			oTable.clear().draw();
			 $('#scheme').prop('disabled',false);
			 }
	})
//selected bassed to group_message

//selected bassed to group_mail

	$('#send_email_to').on('change', function() {
		 var val1='all_cus';
		 var val2='sel_cus';
		 var val3='sch_cus';		 
		 var val4=$(this).val();
		 if(val1 ==val4){	
		 	var oTable = $('#customer_lists').DataTable();
			oTable.clear().draw();
			$('#scheme').prop('disabled',true);
			get_allcustomer_list();
		 }else if(val2 ==val4){	
			var oTable = $('#customer_lists').DataTable();
			oTable.clear().draw();
			$('#scheme').prop('disabled',true);
			get_allcustomer_list();
		 }else{
		 	var oTable = $('#customer_lists').DataTable();
			oTable.clear().draw();
			 $('#scheme').prop('disabled',false);
		}
	})
//selected bassed to group_mail
});



//selected bassed to group_message

function get_allcustomer_list()
{ 
	my_Date = new Date();
	 $("div.overlay").css("display", "block"); 
	$.ajax({
			type:"GET",
			url:base_url+'index.php/sms/send/get_selectcustomer/'+'?nocache='+my_Date.getUTCSeconds(),
			dataType:"json",
			success:function(data){	
			   			 	 if(ctrl_page[0]=="sms")
						  	 {
							 	set_mobile_list(data);
							 }
							 else if(ctrl_page[0]=="email")
							 {
							 	set_email_list(data);
							 }
			   			 $("div.overlay").css("display", "none"); 
					  },
					  error:function(error)  
					  {
						 $("div.overlay").css("display", "none"); 
					  }	 
			      });
}

 //selected bassed to group_message
 
 
//scheme dropdown	
function get_schemes()
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/sms/get_schemes',
		dataType:'json',
		success:function(data){
				var selectid=$('#scheme_val').val();
					$.each(data, function (key, data) {
							$('#scheme').append(
								$("<option></option>")
								  .attr("value", data.id_scheme)
								  .text(data.name)

						);
						
					});
					
			$("#scheme").select2({
			    placeholder: "Select scheme",
			    allowClear: true
			});
				
			$("#scheme").select2("val",(selectid!=null && selectid>0?selectid:''));
		
			
		}
		
		
	});
	
}


function get_customer_list(id)
{ 
	my_Date = new Date();
	 $("div.overlay").css("display", "block"); 
	$.ajax({
			type:"GET",
			url:base_url+'index.php/sms/get_scheme/'+id+'?nocache='+my_Date.getUTCSeconds(),
			dataType:"json",
			success:function(data){
				
				console.log(data);
			   			 	 if(ctrl_page[0]=="sms")
						  	 {
							 	set_mobile_list(data);
							 }
							 else if(ctrl_page[0]=="email")
							 {
							 	set_email_list(data);
							 }
			   			 $("div.overlay").css("display", "none"); 
					  },
					  error:function(error)  
					  {
						 $("div.overlay").css("display", "none"); 
					  }	 
			      });
}

function set_mobile_list(data)	
{
  var group = data.group;
  var oTable = $('#customer_lists').DataTable();
   oTable.clear().draw();
   	 if (group!= null && group.length > 0)
	 {
	 	oTable = $('#customer_lists').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": group,
				                "aoColumns": [ { "mDataProp":function (row,type,val,meta){
												id=row.id_customer;
												return "<label class='checkbox-inline'><input type='checkbox' class='flat-red' name='mobile[]' value='"+row.mobile+"' /> "+id+" </label>";
															} },
												{ "mDataProp": "name" },
												{ "mDataProp": "mobile" },
												{ "mDataProp": "email" }	
												
												]
										});	
	 }  
}

function set_email_list(data)	
{
  var group = data.group;
  console.log(group);
  var oTable = $('#customer_lists').DataTable();
  
   	 if (group!= null && group.length > 0)
	 {
	 	oTable = $('#customer_lists').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": group,
				                "aoColumns": [ { "mDataProp":function (row,type,val,meta){
												id=row.id_customer;
												return "<label class='checkbox-inline'><input type='checkbox' class='flat-red' name='email[]' value='"+row.email+"' /> "+id+" </label>";
															} },
												{ "mDataProp": "name" },
												{ "mDataProp": "mobile" },
												{ "mDataProp": "email" }	
												
												]
										});	
	 }  
}
function set_smsServices_list()
{
	my_Date = new Date();
	$("div.overlay").css('display','block');
	$.ajax({
			 url:base_url+ "index.php/sms/service/ajax?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 type:"POST",
			 success:function(data){
				 var sms 	= data.sms;
				 var access		= data.access;	
				 $('#total_services').text(sms.length);
				  if(access.add == '0')
				 { 
			 			
					$('#add_services').attr('disabled','disabled');
				//	$('#add_services').style.visibility = 'hidden';
					
				 }
				 
			 var oTable = $('#smsService_list').DataTable();
				 oTable.clear().draw();
						 if (sms!= null && sms.length > 0)
							{  	
							oTable = $('#smsService_list').dataTable({
									"bDestroy": true,
									"bInfo": true,
									"bFilter": true,
									"bSort": true,
									"aaData": sms,
									"aoColumns": [	{ "mDataProp": "id_services" },
													{ "mDataProp": "serv_name" },
													 { "mDataProp": function ( row, type, val, meta ){
					                	    email_url =base_url+"index.php/admin_usersms/services_status/email/"+(row.serv_email==1?0:1)+"/"+row.id_services; 
					                		return "<a href='"+email_url+"'><i class='fa "+(row.serv_email==1?'fa-check':'fa-remove')+"' style='color:"+(row.serv_email==1?'green':'red')+"'></i></a>"
					                	}
					                },
					                 { "mDataProp": function ( row, type, val, meta ){
					                	    sms_url =base_url+"index.php/admin_usersms/services_status/sms/"+(row.serv_sms==1?0:1)+"/"+row.id_services; 
					                		return "<a href='"+sms_url+"'><i class='fa "+(row.serv_sms==1?'fa-check':'fa-remove')+"' style='color:"+(row.serv_sms==1?'green':'red')+"'></i></a>"
					                	}
					                },
													{ "mDataProp": function ( row, type, val, meta ) {
														 id= row.id_services;
                	 									 edit_url=(access.edit=='1'?base_url+'index.php/sms/service/edit/'+id:'#');
														 delete_url=(access.delete=='1' ? base_url+'index.php/sms/service/delete/'+id : '#' );
														 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
														  action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
															'<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+
															'<li><a href="#" class=" btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i> Delete</a></li>';
														
														 return action_content;
														 }
													   
													}]
								});			  	 	
							}
								$("div.overlay").css('display','none');
					  },
					  error:function(error)  
					  {
						 $("div.overlay").css("display", "none"); 
					  }	 
			      });
	
	
}



//srinidhi script
function selectAll(){
	chbox=arguments[0];
	type=arguments[1];
	table=document.getElementById("com_table");
	
	if(type=='sel'){
		active_cell=1;
	}
	else{
		active_cell=2;
	}
	rowCount=table.rows.length-1;
	for(i=1;i<=rowCount;i++){
		chkBox=table.rows[i].cells[active_cell].childNodes[0];
		chkBox.checked=chbox.checked;
	}	
}


			
function resetDuration() {
	var table_id = document.getElementById("data_grid");
	var row = arguments[0].parentNode.parentNode.id;
	if(arguments[0].checked == false) {
		table_id.rows[row].cells[5].childNodes[0].value = "-1";
	}
}

//selected bassed to group_message 

$("#btn-send").click(function(){
	 var val1='all_cus';
		var val2='sel_cus';
		 var val3='sch_cus';
		 var val4=$('#send_sms_to').val();
		if(val1==val4){
				var msg = $("#message").val();
				if( msg!=""){
				my_Date = new Date();
				 $("div.overlay").css("display", "block"); 
				$.ajax({
						  url:base_url+ "index.php/sms/send/group_message_allcus?nocache=" + my_Date.getUTCSeconds(),
						 data: {'message':msg},
						 type:"POST",
						 success:function(data){
							 $("div.overlay").css("display", "none"); 
							 if(data=='success'){
								 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> All Customer sms sent successfully.</div>';
							 }
							else{
								 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
							 }
							 $('#chit_alert').html(msg);
							  },
						  error:function(error){
								 $("div.overlay").css("display", "none"); 
							  }	 
							 
					  });
					}
				  else{
					 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please enter message.</div>';
					$('#chit_alert').html(msg);
				  }
	
		}else if(val2==val4){
			var msg = $("#message").val();
			var selected = [];
					$("input[name='mobile[]']:checked").each(function() {
					  selected.push($(this).val());
					});
					console.log(selected);
					console.log(msg);
					
				if( msg!=""){
						if(selected.length != '0')	{
				my_Date = new Date();
				 $("div.overlay").css("display", "block"); 
				$.ajax({
						  url:base_url+ "index.php/sms/send/group_message_selectcus?nocache=" + my_Date.getUTCSeconds(),
						 data: ({'customer':selected,'message':msg}),
						 type:"POST",
						 success:function(data){
							 $("div.overlay").css("display", "none"); 
							 if(data=='success'){
								 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Selected Customer sms sent successfully.</div>';
							 }
							else{
								 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
							 }
							 $('#chit_alert').html(msg);
							  },
						  error:function(error){
								 $("div.overlay").css("display", "none"); 
							  }	 
							 
					  });
				   }else{
						  	
						   msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please select customers.</div>';
							$('#chit_alert').html(msg);	
							}
						  }
						  else{
						  	 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please enter message.</div>';
							$('#chit_alert').html(msg);
						  }
	
		}
		else{
      	   // var selected_customers = get_selected_tablerows("customer_lists");
      	   	 var selected = [];
				$("input[name='mobile[]']:checked").each(function() {
				  selected.push($(this).val());
				});
      	    var msg = $("#message").val();
      	    if( msg!=""){
		      	  	if(selected.length != '0')	{
      	    my_Date = new Date();
			 $("div.overlay").css("display", "block"); 
			$.ajax({
					  url:base_url+ "index.php/sms/send/group_message?nocache=" + my_Date.getUTCSeconds(),
					 data: ({'customer':selected,'message':msg}),
					 type:"POST",
					 success:function(data){
					 	 $("div.overlay").css("display", "none"); 
					 	 if(data=='success'){
						 	 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Group sms sent successfully.</div>';
						 }
						else{
						 	 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
						 }
						 $('#chit_alert').html(msg);
						  },
					  error:function(error){
							 $("div.overlay").css("display", "none"); 
						  }	 
						 
				  });
				   }else{
						  	
						   msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please select customers.</div>';
							$('#chit_alert').html(msg);	
							}
						  }
						  else{
						  	 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please enter message.</div>';
							$('#chit_alert').html(msg);
						}
					}

      }); 

//selected bassed to group_message

//selected bassed to group_mail

$("#btn-send-email").click(function(){

      	var val1='all_cus';
		 var val2='sel_cus';
		 var val3='sch_cus';
		 var val4=$('#send_email_to').val();
		 if(val1==val4){
			 var subject = $("#subject").val();
      	     var msg = $("#compose-textarea").val();
		      	  if(subject!="" && msg!=""){
		      	    my_Date = new Date();
						 $("div.overlay").css("display", "block"); 
					$.ajax({
							  url:base_url+ "index.php/sms/send/group_email_allcus?nocache=" + my_Date.getUTCSeconds(),
							 data: {"subject":subject,'message':msg},
							 type:"POST",
							 success:function(data){
							 	 $("div.overlay").css("display", "none"); 
							 	 if(data=='success'){
								 	 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Group Mail sent successfully.</div>';
								 }
								else{
								 	 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
								 }
									
								$('#chit_alert').html(msg);
									   			
									  },
								  error:function(error){
									   $("div.overlay").css("display", "none"); 
									  }	 
						  });
						  }
						  else{
						  	 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong> Subject and Message fields should not be empty.</div>';
							$('#chit_alert').html(msg);
						  }
					}else if(val2==val4){ 
						  
				var selected = [];
				$("input[name='email[]']:checked").each(function() {
				  selected.push($(this).val());
				});
			
			    var subject = $("#subject").val();
      	         var msg = $("#compose-textarea").val();
		      	  if(subject!="" && msg!=""){
		      	  	if(selected.length != '0')	{
		      	    my_Date = new Date();
						 $("div.overlay").css("display", "block"); 
					$.ajax({
							  url:base_url+ "index.php/sms/send/group_email_cus?nocache=" + my_Date.getUTCSeconds(),
							 data: ({'customer':selected,"subject":subject,'message':msg}),
							 type:"POST",
							 success:function(data){
							 	 $("div.overlay").css("display", "none"); 
							 	 if(data=='success'){
								 	 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Group Mail sent successfully.</div>';
								 }
								else{
								 	 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
								 }
									
								$('#chit_alert').html(msg);
									   			
									  },
								  error:function(error){
									   $("div.overlay").css("display", "none"); 
									  }	 
						  });
						  }else{
						  	
						   msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please select customers.</div>';
							$('#chit_alert').html(msg);	
							}
						  }
						  else{
						  	 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong> Subject and Message fields should not be empty.</div>';
							$('#chit_alert').html(msg);
						  }
					}
					else{
						var selected = [];
					$("input[name='email[]']:checked").each(function() {
					  selected.push($(this).val());
					});
				
					   var subject = $("#subject").val();
					   var msg = $("#compose-textarea").val();
					  if(subject!="" && msg!=""){
						if(selected.length != '0')	{
						my_Date = new Date();
							 $("div.overlay").css("display", "block"); 
						$.ajax({
								  url:base_url+ "index.php/email/send/group_message?nocache=" + my_Date.getUTCSeconds(),
								 data: ({'customer':selected,"subject":subject,'message':msg}),
								 type:"POST",
								 success:function(data){
									 $("div.overlay").css("display", "none"); 
									 if(data=='success'){
										 msg='<div class = "alert alert-success"><i class="icon fa fa-check"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a> Group Mail sent successfully.</div>';
									 }
									else{
										 msg='<div class = "alert alert-danger"><i class="icon fa fa-remove"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Error!</strong> Unable to proceed your request!</div>';
									 }
										
									$('#chit_alert').html(msg);
													
										  },
									  error:function(error){
										   $("div.overlay").css("display", "none"); 
										  }	 
							  });
							  }else{
								
							   msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please select customers.</div>';
								$('#chit_alert').html(msg);	
								}
							  }
							  else{
								 msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong> Subject and Message fields should not be empty.</div>';
								$('#chit_alert').html(msg);
							  }
						}
						  
				});




//selected bassed to group_mail 