var path =  window.location.pathname.split( 'php/' );
var ctrl_page = path[1].split('/');
$(document).ready(function() {
	if(ctrl_page[1]=='list')
	{
	set_notiServices_list();	
	}
	$("#notification_img").change( function(){
		
	event.preventDefault();
	validateImage(this);
	}); 
		
		/*Coded by ARVK - Start*/
		
		if($('#noti_switch').is(':checked'))
		{
			$('#notibox').fadeIn('slow');
		}else{
			$('#notibox').fadeOut('slow');
		}
		
		$('input[name="noti_on_off"]').on('switchChange.bootstrapSwitch', function(event, state) {
			

  			if(state==true)

	     {
	     		my_Date = new Date();
	     		$("div.overlay").css("display", "block");
				$.ajax({
                
                url:base_url+ "index.php/notification/on_off/1?nocache=" + my_Date.getUTCSeconds(),
                type:"POST",
				success:function(data){
					 	 $("div.overlay").css("display", "none");
					 	 }
            });
            $('#notibox').fadeIn('slow');
	     }

		 else{
		 		my_Date = new Date();
	     		$("div.overlay").css("display", "block");
				$.ajax({
                
                url:base_url+ "index.php/notification/on_off/0?nocache=" + my_Date.getUTCSeconds(),
                type:"POST",
				success:function(data){
					 	 $("div.overlay").css("display", "none");
					 	 }
            			});
            	$('#notibox').fadeOut('slow');
		 	}
  
		});
		
		$('#noti_switch').bootstrapSwitch();
			
	/*Coded by ARVK - End*/
	
		$('#select_all').click(function(event) {
			
		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
			event.stopPropagation();
		});
  
   
		$('#confirm-delete .btn-cancel').on('click', function(e) {
			$('.btn-confirm').attr('href',"#");
		}); 
		
	/*	$('#noti_msg').on('blur onchange',function(e){
			 if(this.value.length >180)
   	 	  {
   	   		   $(this).val('');
	   		   $(this).attr('placeholder', 'Enter characters below 180 ')
	  	  }
		});*/
		$('#noti_footer').on('blur onchange',function(e){
			 if(this.value.length >7)
   	 	  {
   	   		   $(this).val('');
	   		   $(this).attr('placeholder', 'Enter characters below 7 ')
	  	  }
		});
		$('#send_notif_on').on('blur keyup',function(e){
			 if(this.value.length >14)
   	 	  {
   	   		   $(this).val('');
	   		   $(this).attr('placeholder', 'Enter "dd" only  ')
	  	  }
		});
		$('#send_daily_from').on('blur keyup',function(e){
			 if(this.value.length >2)
   	 	  {
   	   		   $(this).val('');
	   		   $(this).attr('placeholder', 'Enter "dd" only  ')
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
        
      
	

});

 
// new arrivals 
function validateImage()
   {   	 	

	if(arguments[0].id == 'notification_img')
      {
		 var preview = $('#img_preview');
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
			console.log(fileName);
			var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
			ext = ext.toLowerCase();
			if(ext != "jpg" && ext != "png" && ext != "jpeg")
			{
				alert("Upload JPG or PNG Images only");
				arguments[0].value = "";
				preview.css('display','none');
			}
			else
			{
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
  }
function set_notiServices_list()
{
	my_Date = new Date();
	$("div.overlay").css('display','block');
	$.ajax({
			 url:base_url+ "index.php/notification/ajax?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 type:"POST",
			 success:function(data){
				 var sms 	= data.notification;//console.log(sms);				 	
				 $('#total_services').text(sms.length);
				  if(data.add == '0')
				 { 
			 			
					$('#add_services').attr('disabled','disabled');
				//	$('#add_services').style.visibility = 'hidden';
					
				 }
				 
			 var oTable = $('#notiService_list').DataTable();
				 oTable.clear().draw();
						 if (sms!= null && sms.length > 0)
							{  	
							oTable = $('#notiService_list').dataTable({
									"bDestroy": true,
									"bInfo": true,
									"bFilter": true,
									"bSort": false,
									"aaData": sms,
									"aoColumns": [	{ "mDataProp": "id_notification" },
													{ "mDataProp": "noti_name" },
													 { "mDataProp": function ( row, type, val, meta ){
					                	    noti_url =base_url+"index.php/admin_usersms/notification_status/"+(row.noti_sub==1?0:1)+"/"+row.id_notification; 
					                		return "<a href='"+noti_url+"'><i class='fa "+(row.noti_sub==1?'fa-check':'fa-remove')+"' style='color:"+(row.noti_sub==1?'green':'red')+"'></i></a>"
					                	}
					                },
													{ "mDataProp": function ( row, type, val, meta ) {
														 id= row.id_notification;
                	 									 edit_url=(data.edit!='1'?base_url+'index.php/notification/edit/'+id:'#');
														 delete_url=(data.delete!='1' ? base_url+'index.php/notification/delete/'+id : '#' );
														 delete_confirm= (data.delete!='1' ?'#confirm-delete':'');
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



