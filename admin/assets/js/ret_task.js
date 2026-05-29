var path =  url_params();
var ctrl_page = path.route.split('/');

$(document).ready(function() {
	
     $('.date').datepicker({ dateFormat: 'yyyy-mm-dd', })
     switch(ctrl_page[1])
	 {
	 	case 'task':
				 switch(ctrl_page[2]){				 	
				 	case 'list':				 	
				 			set_task_list();
				 			get_ActiveProfile();
				 			get_ActiveEmployee();

				 	break;
				 	}
		 break;
		 case 'notice_board':
		 		switch(ctrl_page[2]){
		 			case 'list':
		 				get_ActiveProfile();
		 				set_notice_board_list();
		 			break;
		 		}
		 break;
	}
	
});


function get_ActiveProfile()
{
	  	$.ajax({		
             	type: 'GET',		
             	url: base_url+'index.php/admin_ret_task/get_ActiveProfile',		
             	dataType:'json',		
             	success:function(data){				 
            	 	
				var profile=$('#select_profile').val();
				var profile_select=$('#profile_select').val();
				var ar = $('#sel_pro').data('sel_pro');
				$('#profile_select').append(
            	 	$("<option></option>").attr("value",0).text("All")	
            	);


            	  $.each(data, function (key, item) {
                	 	$("#select_profile,#ed_select_profile,#profile_select").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_profile)						  						  
                	 	.text(item.profile_name )						  					
                	 	);			   											
                 	});						
                 	$("#select_profile,#ed_select_profile,#profile_select").select2({			    
                	 	placeholder: "Select Profile",			    
                	 	allowClear: true		    
                 	});	

                    if(profile_select!=undefined)
                    {
                        $("#profile_select").select2("val",(profile_select!='' && profile_select!=null?profile_select:''));	 
                    }else{
                    	$("#profile_select").select2("val","");	 
                    }
                    if(profile!=undefined)
                    {
                        $("#select_profile").select2("val",(profile!='' && profile!=null?profile:''));	 
                    }
				    if(ar!='' && ar!=null?ar:''!=undefined)
                    {
                        $("#ed_select_profile").select2("val",(ar!='' && ar!=null?ar:''));
                    }

                 	$(".overlay").css("display", "none");			
             	}	
            }); 
}

$('#select_profile').on('change',function(){
	if(ctrl_page[1]=='task')
	{
		$('#select_emp option').remove();
		get_ActiveTaskEmployee(this.value);
	}
	
});

$('#profile_select').on('change',function(){
    if(this.value!='' && ctrl_page[1]=='task')
    {
        	$('#select_emp option').remove();
	        get_ActiveTaskEmployee(this.value);
    }
    else{
        get_ActiveEmployee(this.value);
    }
});


function get_ActiveTaskEmployee(id_profile="")
{
	$('#select_emp option').remove();
	$('#ed_select_emp option').remove();
	  	$.ajax({		
             	type: 'POST',		
             	url: base_url+'index.php/admin_ret_task/get_ActiveEmployee',		
             	dataType:'json',	
             	data:{'id_profile':id_profile},	
             	success:function(data){				 
            	 var employee=$('#select_emp').val();
            	 var id_employee=$('#ed_select_emp').val();
            	 $('#select_emp,#ed_select_emp').append(
            	 	$("<option></option>").attr("value",0).text("All")	
            	  );
            	  $.each(data, function (key, item) {
                	 	$("#select_emp,#ed_select_emp").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_employee)						  						  
                	 	.text(item.emp_name )						  					
                	 	);			   											
                 	});						
                 	$("#select_emp,#ed_select_emp").select2({			    
                	 	placeholder: "Select Employee",			    
                	 	allowClear: true		    
                 	});				
					$("#select_emp").select2("val",(employee!='' && employee!=null?employee:''));	 
					$("#ed_select_emp").select2("val",(id_employee!='' && id_employee!=null?id_employee:''));	 
                 	$(".overlay").css("display", "none");			
             	}	
            }); 
}

function get_ActiveEmployee(id_profile="")
{
	    $('#ed_select_emp option').remove();
	    $('#select_emp option').remove();
	  	$.ajax({		
             	type: 'POST',		
             	url: base_url+'index.php/admin_ret_task/get_ActiveEmployee',		
             	dataType:'json',	
             	data:{'id_profile':id_profile,'id_branch':$('#branch_select').val()},	
             	success:function(data){				 
            	 var employee=$('#select_emp').val();
            	 var id_employee=$('#sel_emp').data('sel_emp');
            	 console.log(id_employee);
            	 $('#select_emp').append(
            	 	$("<option></option>").attr("value",0).text("All")	
            	  );
            	  $.each(data, function (key, item) {
                	 	$("#select_emp,#ed_select_emp").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_employee)						  						  
                	 	.text(item.emp_name )						  					
                	 	);			   											
                 	});						
                 	$("#select_emp,#ed_select_emp").select2({			    
                	 	placeholder: "Select Employee",			    
                	 	allowClear: true		    
                 	});				
					$("#select_emp").select2("val",(employee!='' && employee!=null?employee:''));	 
					$("#ed_select_emp").select2('val',id_employee);
                 	$(".overlay").css("display", "none");			
             	}	
            }); 
}

$('#select_employee').on('change',function(){
	if(this.value!='')
	{
		set_task_list();
	}
});

function set_task_list()
{
	$("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		 url:base_url+ "index.php/admin_ret_task/task/ajax?nocache=" + my_Date.getUTCSeconds(),
		  dataType:"JSON",
		  type:"POST",
		  data:{'id_employee':$('#select_employee').val()},
			 success:function(data){	 
				 var list 	= data.list;
				 var access		= data.access;	
				 if(access.add==1)
				 {
				 	$('#add_task').css("display","block");
				 }else{
				 	$('#add_task').css("display","none");
				 }
				 $('#total_count').text(list.length);
		
			 	var oTable = $('#task_list').DataTable();
				oTable.clear().draw();
				  
				 if (list!= null && list.length > 0)
				 {  	
					oTable = $('#task_list').dataTable({
						"bDestroy": true,
		                "bInfo": true,
		                "bFilter": true,
		                "scrollX":'100%',
		                "bSort": true,
		                "dom": 'lBfrtip',
		                "order": [[ 0, "desc" ]],
		                "buttons" : ['excel','print'],
				        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						"aaData": list,
						"aoColumns": [	
										{ "mDataProp": "id_task" },
										{ "mDataProp": "created_dt" },
										{ "mDataProp": "task_name" },
										{ "mDataProp": "emp_name" },
										{ "mDataProp": function ( row, type, val, meta ){
											if(row.task_status==0)
											{
											     return '<span class="badge bg-blue">'+row.status+'</span>';
											}
											else if(row.task_status==1)
											{
											    return '<span class="badge bg-yellow">'+row.status+'</span>';
											}
											else if(row.task_status==2)
											{
											    return '<span class="badge bg-green">'+row.status+'</span>';
											}else{
												 return '<span class="badge bg-red">'+row.status+'</span>';
											}
										},
										},
										{ "mDataProp": "completed_date" },  
										{ "mDataProp": "created_by" },  
										{ "mDataProp": function ( row, type, val, meta ) {
												id= row.id_task;
												print_url=base_url+'index.php/admin_ret_order/vendor_acknowladgement/'+id;
												delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_task/task/delete/'+id : '#' );
												delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
												action_content=(access.edit==1 && row.task_status==1 ?'<a class="btn btn-primary btn-edit" role="button" data-id='+id+' ><i class="fa fa-edit" ></i></a> &nbsp;' :'')+'<a  class="btn btn-primary btn-view" id="edit" data-toggle="modal" data-id='+id+' ><i class="fa fa-eye" ></i></a>&nbsp;'+(row.task_status!=2 && row.task_status!=3 && access.delete=='1'? '<button class="btn btn-warning" onclick="confirm_delete('+id+')"><i class="fa fa-close" ></i></button>' :'')
												return action_content;
											 }	
										}
										
									 ]
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


$('#create_task').on('click',function(){
	var error_msg='';
	if($('#select_emp').val()==null || $('#select_emp').val()=='')
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please Select Employee .</div>'
	}else if($('#task_name').val()=='' || $('#task_name').val()==null)
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter The Task Name .</div>'
	}else{
		create_new_task();
	}
	$('#error_msg').html(error_msg);
});


$('#save_and_new_task').on('click',function(){
	var error_msg='';
	if($('#select_emp').val()==null || $('#select_emp').val()=='')
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please Select Employee .</div>'
	}else if($('#task_name').val()=='' || $('#task_name').val()==null)
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter The Task Name .</div>'
	}else{
		save_and_new_task();
	}
	console.log(error_msg);
	$('#error_msg').html(error_msg);
});


function create_new_task()
{
	my_Date = new Date();
	var form_data = new FormData();  
	form_data.append('task_name', $('#task_name').val());
	form_data.append('id_employee', $('#select_emp').val());
	form_data.append('id_profile', $('#select_profile').val());

	/*var task_list_attachement =$("#task_list_attachement")[0].files.length;
	for (var i = 0; i < task_list_attachement; i++) {
	form_data.append("task_attachement[]", $("#task_list_attachement")[0].files[i]);
	}*/

	var prechecklist_attachement =$("#prechecklist_attachement")[0].files.length;
	for (var i = 0; i < prechecklist_attachement; i++) {
	form_data.append("pre_attachement[]", $("#prechecklist_attachement")[0].files[i]);
	}

	/*var postchecklist_attachement =$("#postchecklist_attachement")[0].files.length;
	for (var i = 0; i < postchecklist_attachement; i++) {
	form_data.append("post_attachement[]", $("#postchecklist_attachement")[0].files[i]);
	}*/

	$.ajax({
		data:form_data,
		url: base_url+"index.php/admin_ret_task/task/save?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		cache:false,
		enctype: 'multipart/form-data',
		contentType : false,
		processData : false,
		success:function(data){
			window.location.reload();
		}
	});
}


function save_and_new_task()
{
	my_Date = new Date();
	var form_data=$('#task_form').serialize();
	$.ajax({
		data:form_data,
		url: base_url+"index.php/admin_ret_task/task/save?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		success:function(data){
			if(data.status)
			{
				response_msg='<div class ="alert alert-success"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong></strong>'+data.message+'</div>'
			}else{
				response_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong></strong>'+data.message+'</div>'
			}
			$('#error_msg').html(response_msg);
			$('#task_name').val('');
		    $('#select_emp').select2("val",'');
		}
	});
}

$(document).on('click', "#task_list a.btn-edit", function(e) {
	e.preventDefault();
	//$('#confirm-edit').modal('show'); //display something
	$('#task_edit').modal({backdrop:'static', keyboard:false});
	id=$(this).data('id');
	$("#edit-id").val(id); 
	getTaskDetails(id);
	  
});

function getTaskDetails(id)
{
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_task/task/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
			$('#ed_task_name').val(data.task_name);
		    $('#ed_select_emp').select2("val",data.task_assign_to);
		   	 get_ActiveEmployee();
		}

	});
}

$('#update_task').on('click',function(){
	$("#confirm-edit").modal({backdrop: true});	
	var error_msg='';
	if($('#ed_select_emp').val()==null || $('#ed_select_emp').val()=='')
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please Select Employee .</div>'
	}else if($('#ed_task_name').val()=='' || $('#ed_task_name').val()==null)
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter The Task Name .</div>'
	}else{
		update_task();
	}
	$('#ed_error_msg').html(error_msg);
});

function update_task()
{
	my_Date = new Date();
	var form_data=$('#ed_task_form').serialize();
	$.ajax({
		data:form_data,
		url: base_url+"index.php/admin_ret_task/task/update/"+$("#edit-id").val()+"?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		success:function(data){
			//window.location.reload();
		}
	});
}

$(':radio[name="task_attachement"]').on('change',function(){
	if(this.value==1)
	{
		$('#task_checklist_attchement').css("display","block");
	}else{
		$('#task_checklist_attchement').css("display","none");
	}
});


$(':radio[name="pre_attachement"]').on('change',function(){
	if(this.value==1)
	{

		$('#pre_checklist_attchement').css("display","block");
	}else{
		$('#pre_checklist_attchement').css("display","none");
	}
});


$(':radio[name="task_pre_attachement"]').on('change',function(){
	if(this.value==1)
	{
		$('#task_pre_checklist_attchement').css("display","block");
	}else{
		$('#task_pre_checklist_attchement').css("display","none");
	}
});


$(':radio[name="post_attachement"]').on('change',function(){
	if(this.value==1)
	{

		$('#post_checklist_attchement').css("display","block");
	}else{
		$('#post_checklist_attchement').css("display","none");
	}
});

$(':radio[name="task_post_attachement"]').on('change',function(){
	if(this.value==1)
	{

		$('#task_post_checklist_attchement').css("display","block");
	}else{
		$('#task_post_checklist_attchement').css("display","none");
	}
});


$("#task_pre_checklist_attachments").change( function(){
		event.preventDefault();
		validateFile(this);
}); 


function validateFile()
{
	 if(arguments[0].id == 'task_pre_checklist_attachments')
      {
		 var preview = $('#offer_img_preview');
	  }
	  if(arguments[0].files[0].size > 1048576)
	  {
		 alert('File size cannot be greater than 1 MB');
		 arguments[0].value = "";
		 preview.css('display','none');
	  }
	  else
	  {
	  	var fileName =arguments[0].value;
	  	var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
		ext = ext.toLowerCase();
		
				var file    = arguments[0].files[0];
				var reader  = new FileReader();
				  reader.onloadend = function () {
					preview.prop('src',reader.result);
				  }	
				  if (file)
				  {
				 	reader.readAsDataURL(file);
					preview.css('display','');
				  }
				 else
				  {
				  	preview.prop('src','');
					preview.css('display','none');
				  }
	  }

}


$(document).on('click', "#task_list a.btn-view", function(e) {
		e.preventDefault();
		id=$(this).data('id');
	    $("#status_id").val(id); 
	   	getTaskViewDetails(id);
	   	$('#confirm-view').modal('show');
});

function getTaskViewDetails()
{
	$('#pre_check_list >tbody').empty();
	$('#post_check_list >tbody').empty();
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_task/task/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
			$('#task').val(data.task_name);
			$('#remarks').val(data.remarks);
			$('#task_pre_checklist_attachments').val(data.task_pre_checklist_attachments);
			$('#task_post_checklist_attachments').val(data.task_post_checklist_attachments);

			if(data.task_status==2)
			{
				$('#remarks').prop('readonly',true);
				$('#update_status').prop('disabled',true);
				$('#has_pre_attachement').css("display","none");
				$('#post_task_attachement').css("display","block");
				$('#user_attachement').css("display","none");
			}else{
				$('#update_status').prop('disabled',false);
				$('#remarks').prop('readonly',false);
				$('#has_pre_attachement').css("display","block");
				$('#post_task_attachement').css("display","none");
				$('#user_attachement').css("display","block");
			}
			var preHtml 			='';
			var postHtml 			='';
			var taskHtml 			='';
			var attachement 		=data.task_pre_checklist_attachments;
			var post_attachement 	=data.task_post_checklist_attachments;
			var task_attchhements 	=data.task_attachments;
				
				if(task_attchhements!=null && task_attchhements!='')
				{
					$('#task_attchhements').css("display","block");
					let tasks=task_attchhements.split('##');
					
					$.each(tasks,function(key,item){
					if(item!='')
					{
					taskHtml+='<tr>'+
					'<td><input type="hidden" class="id_task" value='+data.id_task+'>'+parseFloat(key+1)+'</td>'+
					'<td><input type="hidden" class="file_name" value='+item+'>'+item+'</td>'+
					'<td class="download"><a href='+base_url+'index.php/admin_ret_task/download_file/'+item+'/0>Download</a></td>'+
					'</tr>'
					}
					});

					$('#task_check_list >tbody').append(taskHtml);
				}
				else
				{
				    $('#task_attchhements').css("display","none");
				}

				if(attachement!=null && attachement!='')
				{
				    let pre_task_attachement=attachement.split('##');
					if(attachement!='' && attachement!=null)
					{
						$('#pre_task_attachement').css("display","block");
					}
					else
					{
						$('#pre_task_attachement').css("display","none");
					}
					$.each(pre_task_attachement,function(key,item){
					if(item!='')
					{
					preHtml+='<tr>'+
					'<td><input type="hidden" class="id_task" value='+data.id_task+'>'+parseFloat(key+1)+'</td>'+
					'<td><input type="hidden" class="file_name" value='+item+'>'+item+'</td>'+
					'<td class="download"><a href='+base_url+'index.php/admin_ret_task/download_file/'+data.id_task+'/'+item+'/1>Download</a></td>'+
					'</tr>'
					}
					});
					$('#pre_check_list >tbody').append(preHtml);
				}
				else
				{
				    $('#pre_task_attachement').css("display","none");
				}
				if(post_attachement!=null)
				{
					let post_task_attachement=post_attachement.split('##');
					if(post_attachement!='' && post_attachement!=null)
					{
						$('#post_task_attachement').css("display","block");
					}
					else
					{
						$('#post_task_attachement').css("display","none");
					}

					$.each(post_task_attachement,function(key,item){
					if(item!='')
					{
					postHtml+='<tr>'+
					'<td><input type="hidden" class="id_task" value='+data.id_task+'>'+parseFloat(key+1)+'</td>'+
					'<td><input type="hidden" class="file_name" value='+item+'>'+item+'</td>'+
					'<td class="download"><a href='+base_url+'index.php/admin_ret_task/download_file/'+data.id_task+'/'+item+'/2>Download</a></td>'+
					'</tr>'
					}
					});
					$('#post_check_list >tbody').append(postHtml);
				}
				
		}

	});
}


$(':radio[name="task_has_post_checklist"]').on('change',function(){
	if(this.value==1)
	{

		$('#post_checklist_attchement').css("display","block");
	}else{
		$('#post_checklist_attchement').css("display","none");
	}
});

$('#update_status').on('click',function(){
	my_Date = new Date();
	var form_data = new FormData();  
	var totalfiles =$("#attach_documents")[0].files.length;
	var totalfiles =$("#attach_documents")[0].files.length;
	for (var i = 0; i < totalfiles; i++) {
	form_data.append("post_attachement[]", $("#attach_documents")[0].files[i]);
	}
	form_data.append("id_task", $("#status_id").val());
	form_data.append("remarks", $("#remarks").val());
	$.ajax({
		data:form_data,
		url: base_url+"index.php/admin_ret_task/update_status?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		cache:false,
		enctype: 'multipart/form-data',
		contentType : false,
		processData : false,
		success:function(data){
			window.location.reload();
		}
	});
});


function confirm_delete(bill_id)
{

	$('#task_id').val(bill_id);
	$('#confirm-delete').modal('show');
}




$('#task_cancel').on('click',function(){
	my_Date = new Date();
	$.ajax({
		type: 'POST',
		url:base_url+ "index.php/admin_ret_task/task/delete?nocache=" + my_Date.getUTCSeconds(),
		dataType:'json',
		data:{'cancel_remark':$('#cancel_remark').val(),'id_task':$('#task_id').val()},
		success:function(data){
			
		    window.location.reload();
		}
	});
});

//Item Description



//Notice Board

$('#add_task').on('click',function(){
    get_ActiveEmployee();
});

function set_notice_board_list()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		 url:base_url+ "index.php/admin_ret_task/notice_board/ajax?nocache=" + my_Date.getUTCSeconds(),
		  dataType:"JSON",
			 type:"POST",
			 success:function(data){	 
				 var list 	= data.list;
				 var access		= data.access;	
				 if(access.add==1)
				 {
				 	$('#add_task').css("display","block");
				 }else{
				 	$('#add_task').css("display","none");
				 }
				 $('#total_count').text(list.length);
		
			 	var oTable = $('#task_list').DataTable();
				oTable.clear().draw();
				  
				 if (list!= null && list.length > 0)
				 {  	
					oTable = $('#notice_board_list').dataTable({
						"bDestroy": true,
		                "bInfo": true,
		                "bFilter": true,
		                "scrollX":'100%',
		                "bSort": true,
		                "dom": 'lBfrtip',
		                "order": [[ 0, "desc" ]],
		                "buttons" : ['excel','print'],
				        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						"aaData": list,
						"aoColumns": [	
										{ "mDataProp": "id_noticeboard" },
										{ "mDataProp": "date_add" },
										{ "mDataProp": "noticeboard_text" },
										{ "mDataProp": "reminder_on" },
										{ "mDataProp": "profile_name" },
										{ "mDataProp": "emp_name" },
                                        { "mDataProp": function ( row, type, val, meta ){
                                        active_url =(access.delete=='1' ? base_url+"index.php/admin_ret_task/noticeboard_status/"+(row.noticeboard_status==1?0:1)+"/"+row.id_noticeboard :''); 
                                        return "<a href='"+active_url+"'><i class='fa "+(row.noticeboard_status==1?'fa-check':'fa-remove')+"' style='color:"+(row.noticeboard_status==1?'green':'red')+"'></i></a>"
                                        }
                                        },
										{ "mDataProp": function ( row, type, val, meta ) {
												id= row.id_noticeboard;
												delete_url=(access.delete=='1' ? base_url+'index.php/admin_ret_task/notice_board/delete/'+id : '#' );
												delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
												action_content=(access.edit==1 ?'<a class="btn btn-primary btn-edit" role="button" data-id='+id+' ><i class="fa fa-edit" ></i></a> &nbsp;' :'')+''+(access.delete==1 ? '<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash">' :'')+'</i></a>'
												return action_content;
											 }	
										}
										
										
									 ]
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

$('#save_notice_board').on('click',function(){
	var error_msg='';
	if($('#id_profile').val()==null || $('#id_profile').val()=='')
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please Select Profile .</div>'
	}else if($('#noticeboard_text').val()=='' || $('#noticeboard_text').val()==null)
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter The Notification Content .</div>'
	}
	else if($('#reminder_date').val()=='' || $('#reminder_date').val()==null)
	{
	    error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please Select Validity Date .</div>'
	}
	else{
		create_notice_board();
	}
	$('#error_msg').html(error_msg);
});

$('#update_noticeboard').on('click',function(){
    
    var error_msg='';
	if($('#ed_profile_select').val()==null || $('#ed_profile_select').val()=='')
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please Select Profile .</div>'
	}else if($('#ed_noticeboard_text').val()=='' || $('#ed_noticeboard_text').val()==null)
	{
		error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter The Notification Content .</div>'
	}
	else if($('#ed_reminder_date').val()=='' || $('#ed_reminder_date').val()==null)
	{
	    error_msg='<div class ="alert alert-danger"><a href = "#" class= "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please Select Validity Date .</div>'
	}
	else{
		update_noticeboard();
	}
	$('#ed_error_msg').html(error_msg);
});

function update_noticeboard()
{
   my_Date = new Date();
	$.ajax({
		data:{'noticeboard_text':$('#ed_noticeboard_text').val(),'reminder_date':$('#ed_reminder_date').val(),'id_profile':$('#ed_profile_select').val(),'employee':$('#ed_select_emp').val()},
		url: base_url+"index.php/admin_ret_task/notice_board/update/"+$("#edit-id").val()+"?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		success:function(data){
			window.location.reload();
		}
	});
}


$("#profile_select").change(function() {
       var data = $("#profile_select").select2('data');
       selectedValue = $(this).val();
       $("#id_profile").val(selectedValue);
 }); 

$("#ed_purity_sel").change(function() {
	var data = $("#ed_purity_sel").select2('data');
	selectedValue = $(this).val();
	$("#ed_pur_id").val(selectedValue);
}); 

function create_notice_board()
{
	my_Date = new Date();
	$.ajax({
		data:{'noticeboard_text':$('#noticeboard_text').val(),'reminder_date':$('#reminder_date').val(),'id_profile':$('#id_profile').val(),'employee':$('#select_emp').val(),'id_branch':$('#branch_select').val()},
		url: base_url+"index.php/admin_ret_task/notice_board/save?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		success:function(data){
			window.location.reload();
		}
	});
}

$(document).on('click', "#notice_board_list a.btn-edit", function(e) {
	e.preventDefault();
	//$('#confirm-edit').modal('show'); //display something
	$('#noticeboard_edit').modal({backdrop:'static', keyboard:false});
	id=$(this).data('id');
	$("#edit-id").val(id); 
	getNoticeBoardDetails(id);
	  
});

function getNoticeBoardDetails()
{
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_task/notice_board/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
		   
			$('#ed_noticeboard_text').val(data.noticeboard_text);
			$('#sel_emp').attr("data-sel_emp",data.id_employee);
			$('#ed_id_profile').val(data.visible_to);
			$('#ed_reminder_date').val(data.reminder_on);
			
			get_Notice_board_profile();
			get_ActiveEmployee();
		}

	});
}


function get_Notice_board_profile()
{
	  	$.ajax({		
             	type: 'GET',		
             	url: base_url+'index.php/admin_ret_task/get_ActiveProfile',		
             	dataType:'json',		
             	success:function(data){				 
            	 	
            	 var profile_select=$('#ed_id_profile').val();
            	 
            	 $('#ed_profile_select').append(
            	 	$("<option></option>").attr("value",0).text("All")	
            	);
            	
            	  $.each(data, function (key, item) {
                	 	$("#ed_profile_select").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_profile)						  						  
                	 	.text(item.profile_name )						  					
                	 	);			   											
                 	});						
                 	$("#ed_profile_select").select2({			    
                	 	placeholder: "Select Profile",			    
                	 	allowClear: true		    
                 	});	
                 	$('#ed_profile_select').select2("val",profile_select);
                 	$(".overlay").css("display", "none");			
             	}	
            }); 
}

$('.close_modal').on('click',function(){
    window.location.reload();
});

$('#branch_select').on('change',function(){
    if(this.value!='')
    {
        get_ActiveEmployee();
    }
});


//Notice Board