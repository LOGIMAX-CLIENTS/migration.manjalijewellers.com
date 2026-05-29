var path =  url_params();



var ctrl_page = path.route.split('/');



var img_resource=[];



var total_files=[];



var tax_details=[];



var pre_img_files=[];



var pre_img_resource=[];



var cat_product_details = [];



var stockIssueDetails = [];



var receipt_details =[];



$(document).ready(function() {



 



	 var path =  url_params();



	 $('#status').bootstrapSwitch();	



	 $('.date').datepicker({ dateFormat: 'yyyy-mm-dd', })



	 $('body').addClass("sidebar-collapse");	



     



     switch(ctrl_page[1])



	 {



	 	case 'stock_issue':



	 		    switch(ctrl_page[2]){				 	



				 	case 'list':	



				 	    set_stock_issue_list();



				 	break;



				 	case 'add':



				 	    get_ActiveMetals();



					    get_all_employee();



						get_metal_rates_by_branch();



					    get_stock_issue_type();



						get_ActiveKarigars();



						get_ActiveSections();

     

					    $('#issue_type').select2();



						$('#issue_to').select2();



					    $('#repair_type').select2();



				        if($('#description').length > 0)



						{



						 	CKEDITOR.replace('description');



						} 



						get_StockIssueItems();	



							



				 	break;



	 		    }



	 		break;



	 		



	 		



	}



	



});







function get_stock_issue_type()



{



    $.ajax({



	type: 'GET',



	url: base_url+'index.php/admin_ret_stock_issue/get_stock_issue_type',



	dataType:'json',



	success:function(data){



	    console.log(data);



	       $.each(data, function (key, item) {   



    		    $("#issue_type").append(



    		    $("<option></option>")



    		    .attr("value", item.id_stock_issue_type) 



				.attr("issue_to_cus",item.issue_to_cus)    



    		    .text(item.name)  



    		    );



    		}); 



    		$("#issue_type").select2(



    		{



    			placeholder:"Select Issue Type",



    			closeOnSelect: true		    



    		});



    		$("#issue_type").select2("val",'');







		}



	});



}







function get_all_karigar()



{



	$.ajax({



	type: 'GET',



	url: base_url+'index.php/admin_ret_catalog/karigar/active_list',



	dataType:'json',



	success:function(data){



	    if($('#repair_assign_karigar').length == 0){



    		var id =  $("#karigar").val();



    		var filter_karigar =  $("#filter_karigar").val();



    		$.each(data, function (key, item) {   



    		    $("#karigar_sel,#karigar_filter").append(



    		    $("<option></option>")



    		    .attr("value", item.id_karigar)    



    		    .text(item.karigar)  



    		    );



    		}); 



    		$("#karigar_sel").select2(



    		{



    			placeholder:"Assign To Karigar",



    			closeOnSelect: true		    



    		});



    		$("#karigar_filter").select2(



    		{



    			placeholder:"Karigar Filter",



    			closeOnSelect: true		    



    		});



    		    $("#karigar_sel").select2("val",(id!='' && id>0?id:''));



    		    $("#karigar_filter").select2("val",(filter_karigar!='' && filter_karigar>0?filter_karigar:''));



    		    $(".overlay").css("display", "none");



    		    



	    }



		    if($('#repair_assign_karigar').length > 0){



		        $.each(data, function (key, item) {   



        		    $("#repair_assign_karigar").append(



        		    $("<option></option>")



        		    .attr("value", item.id_karigar)    



        		    .text(item.karigar)  



        		    );



        		}); 



        		$("#repair_assign_karigar").select2(



        		{



        			placeholder:"Select Karigar",



        			closeOnSelect: true		    



        		});



        		$("#repair_assign_karigar").select2("val",'');



		    }



		    



		}



	});



}







function get_all_employee()







	{







	    $('#issue_employee option').remove();



	    $('#sel_emp option').remove();







		my_Date = new Date();







		$.ajax({ 







		url:base_url+ "index.php/admin_ret_estimation/get_employee?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),







        data: {'id_branch' : ($('#branch_select').val() || $('#id_branch').val())},







        type:"POST",







        dataType:"JSON",







        success:function(data)







        {







           emp_details = data;







           $.each(data, function (key, item) {					  				  			   		







                	 	$("#issue_employee").append(						







                	 	$("<option></option>")						







                	 	.attr("value", item.id_employee)						  						  







                	 	.text(item.emp_name)						  					







                	 	);			   											

                	 	$("#sel_emp").append(						







                	 	$("<option></option>")						







                	 	.attr("value", item.id_employee)						  						  







                	 	.text(item.emp_name)						  					







                	 	);			   											







                 	});						







             	$("#issue_employee").select2({			    







            	 	placeholder: "Select Employee",			    







            	 	allowClear: true		    







             	});					

             	$("#sel_emp").select2({			    







            	 	placeholder: "Select Employee",			    







            	 	allowClear: true		    







             	});					







         	    $("#issue_employee").select2("val",(''));	 

         	    $("#sel_emp").select2("val",(''));	 







         	    $(".overlay").css("display", "none");	







        },







        error:function(error)  







        {	







        } 







    	});







	}





$('#stock_issue_submit').on('click',function(){



    var issue_type = $('#issue_type').val(); 



	var issued_to = $('#issued_to').val();  



	var issued_no = $('#select_issue_no').val();  



	var stock_type = $('#stock_type').val();



	var customer =$('#est_cus_name').val();



	var employee =$('#issue_employee').val();



	var karigar =$('#karigar').val();



    var issue_receipt_type = $("input[name='order[issue_receipt_type]']:checked").val();



	var issue_to_cus = $("#issue_type :selected").attr('issue_to_cus');  



    var allow_submit=true;





	if(issue_receipt_type == 1)



	{



   var branch_val = $('#branch_select').val() || $('#id_branch').val();

   if(branch_val=='' || branch_val==null)



   {



       $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch..'});



       allow_submit=false;



   }



   

   else if((issue_type=='' || issue_type==null) && (issue_receipt_type==1))



   {



       $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue Type..'});



       allow_submit=false;



   }



   else if((stock_type=='' || stock_type==null))



   {



       $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Stock Type..'});



       allow_submit=false;



   }



   else if(issued_to=='' || issued_to==null)



   {



       $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue To..'});



       allow_submit=false;



   }



   else if($("input[name='nt_item_sel[]']:checked").length==0 && (stock_type==2)){



	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Issue..'});

	

	allow_submit=false;

	

	}



	}

	



   if(issue_receipt_type == 2)	



    {



		if(issued_no== '' || issued_no== null){



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue No.'});



	    }



		else if($("input[name='nt_item_sel[]']:checked").length==0 && (stock_type==2)){



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Receipt..'});

			

			allow_submit=false;

			

			}

	

    }



	else if(issued_to==1 && (customer=='' || customer==null))



	{

 

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter Customer name..'});

 

		allow_submit=false;

 

	}



	else if(issued_to==2 && (employee=='' || employee==null))



	{

 

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Employee..'});

 

		allow_submit=false;

 

	}

	else if(issued_to==3 && (karigar=='' || karigar==null))



	{

 

		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar..'});

 

		allow_submit=false;

 

	}



   else if(($('#tagissue_item_detail > tbody  > tr').length==0) && (issue_receipt_type==1 ) && (stock_type==1))



   {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});



        allow_submit=false;



   }



   else if(($('#tag_receipt_item_detail > tbody  > tr').length==0) && (issue_receipt_type==2 )&& (stock_type==1))



   {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});



        allow_submit=false;



   }



    else if(($('#nontagissue_item_detail > tbody  > tr').length==0) && (issue_receipt_type==1 ) && (stock_type==2))



   	{

   

   		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});

   

   		allow_submit=false;

   

   	}

   

   	else if(($('#nontag_receipt_item_detail > tbody  > tr').length==0) && (issue_receipt_type==2)&& (stock_type==2))

   

   	{

   

   		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});

   

   		allow_submit=false;

   

   	}



   else if ( $('#otp_required').val() == 1 && $('#is_otp_verfied').val() != 1) {



	$('#stock_otp_modal').modal({ backdrop: "static", keyboard: false }).modal("show");

				

	stock_at_send_otp();



	allow_submit=false;



   }



    if(allow_submit == true)



   {

		non_tagged = [];



		$("input[name='nt_item_sel[]']:checked").each(function () {

			var row = $(this).closest('tr');

			var data = {

				'id_nontag_item': row.find('.id_nontag_item').val(),

				'pieces': row.find('.ntpcs').val(),

				'id_section': row.find('.id_section').val(),

				'id_product': row.find('.id_product').val(),

				'id_design': row.find('.id_design').val(),

				'id_sub_design': row.find('.id_sub_design').val(),

				'grs_wt': row.find('.nt_gross_wt').val(),

				'net_wt': row.find('.nt_net_wt').val(),

				'id_stock_issue_detail': row.find('.id_stock_issue_detail').val()

			};

			non_tagged.push(data);

		});

		

		console.log(non_tagged);



		var additional_data =""



		//  additional_data = $('#stock_issue_form').serializeArray(); // Use serializeArray for form data



		if(stock_type == 1)

		{

		additional_data=$('#stock_issue_form').serialize();

		}

		else if(stock_type == 2)

		{

		 additional_data = {

			'nt_data': non_tagged, // Convert non_tagged to JSON

			'branch_select': ($('#branch_select').val() || $('#id_branch').val()),

			'issue_type': $('#issue_type').val(),

			'stock_type': $('#stock_type').val(),

			'issued_to': $('#issued_to').val(),

			'cus_id': $('#cus_id').val(),

			'rate_per_gram': $('#rate_per_gram').val(),

			'remark': $('#remark').val(),

			'id_employee': $('#issue_employee').val(),

			'karigar': $('#karigar').val(),

			'form_secret': $('#form_secret').val(),

			'type_issue': $("input[name='order[issue_receipt_type]']:checked").val(),

			'issued_type': $('#issued_type').val(),

			'issued_branch': $('#issued_branch').val()

		};

	  }



		// Combine form data and additional parameters

		// var combined_data = form_data.concat($.map(additional_data, function (value, key) {

		// 	return { name: key, value: value };

		// }));





		$('#stock_issue_submit').prop('disabled',true);



		var url=base_url+ "index.php/admin_ret_stock_issue/stock_issue/save?nocache=" + my_Date.getUTCSeconds();



	    $.ajax({ 



	        url:url,



	        data: additional_data,



	        type:"POST",



	        dataType:"JSON",



	        success:function(data){





				console.log(data);





				if(data.status)



				{



				    $("div.overlay").css("display", "none"); 



				    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});



				    var issue_receipt_type = $("input[name='order[issue_receipt_type]']:checked").val();  



				    if(issue_receipt_type==1)



				    {



				        window.open( base_url+'index.php/admin_ret_stock_issue/stock_issue/issue_print/'+data['id_stock_issue'],'_blank');



				        



				    }



				    location.href=base_url+'index.php/admin_ret_stock_issue/stock_issue/list';



				}



				else



				{



				    // window.location.reload();



				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



				    $("div.overlay").css("display", "none"); 



				}



				



	        },



	        error:function(error)  



	        {	



	        $("div.overlay").css("display", "none"); 



	        } 



	    });



   }

       



});





//Order delievery otp starts



$('.submit_stock_issue').on('click', function () {





	if ($('#otp_required').val() == 1 && $('#is_otp_verfied').val() != 1) {



		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>OTP is Not Verified Yet.." });



		return false;



	} else {



		$('#stock_issue_submit').trigger('click');



	}





});





function stock_at_send_otp(){



	my_Date = new Date();



	var mobile = $('#selected_cus_mobile').val();



	var send_resend = $('#send_resend').val();



	$.ajax({



		url: base_url + "index.php/admin_ret_stock_issue/stock_issue_sendotp?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		type: "POST",



		data: { 'mobile': mobile, 'send_resend': send_resend },



		dataType: "json",



		async: false,



		success: function (data) {



			if (data.status) {



				$("#stock_otp_modal").modal({



					backdrop: 'static',



					keyboard: false



				}).modal("show");



				var fewSeconds = 3;



				$("#resend_stock_otp").prop('disabled', true);



				timer = setTimeout(function () {



					$("#resend_stock_otp").prop('disabled', false);



				}, fewSeconds * 1000);



			}



			else {



				$("#stock_otp_modal").modal({



					backdrop: 'static',



					keyboard: false



				}).modal("show");



				var fewSeconds = 3;



				$("#resend_stock_otp").prop('disabled', true);



				timer = setTimeout(function () {



					$("#resend_stock_otp").prop('disabled', false);



				}, fewSeconds * 1000);



			}



		},



	});



}



$(document).on('input', '#stock_trns_otp', function (e) {



	if (this.value.length == 6) {



		$('#verify_stock_otp').prop('disabled', false);



		$('#stock_trns_otp').val(this.value);



	}



	else {



		$('#verify_stock_otp').prop('disabled', true);



	}



});







$('#verify_stock_otp').on('click', function () {



	stock_order_otp();



});



function stock_order_otp() {



	var transfered_amount_Details= [];



	my_Date = new Date();



	$.ajax({



		url: base_url + "index.php/admin_ret_stock_issue/stock_issue_verify_otp?nocache=" + my_Date.getUTCSeconds() + '' + my_Date.getUTCMinutes() + '' + my_Date.getUTCHours(),



		data: { "otp": $("#stock_trns_otp").val() },



		type: "POST",



		dataType: "json",



		async: false,



		success: function (data) {



			$('.otp_alert').css('display', 'block');



			if (data.status) {



				// alert("1");



				$('#stock_trns_otp').prop('disabled', true);



				$('#verify_stock_otp').prop('disabled', true);



				$('.submit_stock_issue').prop('disabled', false);



				$(".otp_alert").append('<p style="color:green">' + data.msg + '</p>');



				$('#resend_stock_otp').prop('disabled', true);



				$('#is_otp_verfied').val(1);

      



				// $('#adv_trans_otp').val($('#order_trns_otp').val());



				setTimeout(function () {



					$('.otp_alert').css('display', 'none');



					$(".otp_alert").empty();



				}, 3000);



			}



			else {



				$('#verify_stock_otp').prop('disabled', false);



				$('.submit_stock_issue').prop('disabled', true);



				$(".otp_alert").append('<p style="color:red">' + data.msg + '</p>');



				$('#is_otp_verfied').val(0);



				setTimeout(function () {



					$('.otp_alert').css('display', 'none');



					$(".otp_alert").empty();



					$('#stock_trns_otp').prop('disabled', false);



					$("#resend_stock_otp").prop('disabled', false);



				}, 3000);



				$('#resend_stock_otp').prop('disabled', false);



			}



		},



		error: function (error) {



			$(".overlay").css('display', "none");



		}



	});



}



$('#resend_stock_otp').on('click', function () {



	$('#send_resend').val(1);



	$("#stock_trns_otp").val('');



	$('.submit_stock_issue').prop('disabled', true);



	stock_at_send_otp();



});



$('#close').on('click', function () {



	clearTimeout(timer); //clears the previous timer.



	$("#stock_trns_otp").val('');



	$("#resend_stock_otp").attr("disabled", true);



	$(".submit_stock_issue").attr("disabled", true);



	$('#is_otp_verfied').val(0);



});

//Order delievery otp ends











// $('#stock_issue_submit').on('click',function(){



//     var issue_type = $('#issue_type').val(); 



// 	var issued_to = $('#issued_to').val(); 



// 	var issued_no = $('#select_issue_no').val();



// 	var stock_type = $('#stock_type').val();







 



//     var issue_receipt_type = $("input[name='order[issue_receipt_type]']:checked").val();



// 	var issue_to_cus = $("#issue_type :selected").attr('issue_to_cus');  



//     var allow_submit=true;







// 	if(stock_type==1){



// 		if(issue_receipt_type == 1)



// 		{



// 			if($('#branch_select').val()=='' || $('#branch_select').val()==null)



// 			{



// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch..'});



// 				allow_submit=false;



// 			}



// 			else if((issue_type=='' || issue_type==null) && (issue_receipt_type==1))



// 			{



// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue Type..'});



// 				allow_submit=false;



// 			}



// 			else if(issued_to=='' || issued_to==null)



// 			{



// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue To..'});



// 				allow_submit=false;



// 			}



// 		}



// 		if(issue_receipt_type == 2)	



// 		{



// 			if(issued_no== '' || issued_no== null){



// 			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue No.'});



// 			}



// 		}



// 		else if(($('#tagissue_item_detail > tbody  > tr').length==0) && (issue_receipt_type==1 ))



// 		{



// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});



// 				allow_submit=false;



// 		}



// 		else if(($('#tag_receipt_item_detail > tbody  > tr').length==0) && (issue_receipt_type==2 ))



// 		{



// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});



// 				allow_submit=false;



// 		}



// 		if(allow_submit)



// 		{



// 				var form_data=$('#stock_issue_form').serialize();



// 				$('#stock_issue_submit').prop('disabled',true);



// 				var url=base_url+ "index.php/admin_ret_stock_issue/stock_issue/save?nocache=" + my_Date.getUTCSeconds();



// 				$.ajax({ 



// 					url:url,



// 					data: form_data,



// 					type:"POST",



// 					dataType:"JSON",



// 					success:function(data){







// 						console.log(data);







// 						if(data.status)



// 						{



// 							$("div.overlay").css("display", "none"); 



// 							$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});



// 							var issue_receipt_type = $("input[name='order[issue_receipt_type]']:checked").val();  



// 							if(issue_receipt_type==1)



// 							{



// 								window.open( base_url+'index.php/admin_ret_stock_issue/stock_issue/issue_print/'+data['id_stock_issue'],'_blank');



								



// 							}



// 							location.href=base_url+'index.php/admin_ret_stock_issue/stock_issue/list';



// 						}



// 						else



// 						{



// 							window.location.reload();



// 							$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



// 							$("div.overlay").css("display", "none"); 



// 						}



						



// 					},



// 					error:function(error)  



// 					{	



// 					$("div.overlay").css("display", "none"); 



// 					} 



// 				});



// 			}



// 		}else{



// 			if(issue_receipt_type == 1)



// 			{



// 				if($('#branch_select').val()=='' || $('#branch_select').val()==null)



// 				{



// 					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Branch..'});



// 					allow_submit=false;



// 				}



// 				else if((issue_type=='' || issue_type==null) && (issue_receipt_type==1))



// 				{



// 					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue Type..'});



// 					allow_submit=false;



// 				}



// 				else if(issued_to=='' || issued_to==null)



// 				{



// 					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue To..'});



// 					allow_submit=false;



// 				}



// 				else if($("input[name='nt_item_sel[]']:checked").length==0){



// 					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Issue..'});



// 					allow_submit=false;



// 				}



// 			}



// 			if(issue_receipt_type == 2)	



// 			{



// 				if(issued_no== '' || issued_no== null){



// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue No.'});



// 				}



// 			}



// 			else if(($('#nontagissue_item_detail > tbody  > tr').length==0) && (issue_receipt_type==1 ))



// 			{



// 					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});



// 					allow_submit=false;



// 			}



// 			else if(($('#nontag_receipt_item_detail > tbody  > tr').length==0) && (issue_receipt_type==2 ))



// 			{



// 					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records to Update..'});



// 					allow_submit=false;



// 			}



// 			if($("input[name='nt_item_sel[]']:checked").length==0){



// 				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Records to Receipt..'});



// 				allow_submit=false;



// 			}







// 			if(allow_submit)



// 			{



// 				non_tagged=[];



// 				$("input[name='nt_item_sel[]']:checked").each(function() {



// 					var row = $(this).closest('tr');



					



// 					var data = {



// 								'id_nontag_item' : row.find('.id_nontag_item').val(),



// 								'pieces' : row.find('.ntpcs').val() ,



// 								'id_section' : row.find('.id_section').val(),



// 								'id_product' : row.find('.id_product').val(),



// 								'id_design' : row.find('.id_design').val(),



// 								'id_sub_design' : row.find('.id_sub_design').val(),



// 								'grs_wt' : row.find('.nt_gross_wt').val(),



// 								'net_wt' : row.find('.nt_net_wt').val(),



// 								'id_stock_issue_detail':row.find('.id_stock_issue_detail').val()



// 							   };



// 					non_tagged.push(data); 



// 				});







// 				console.log(non_tagged);







// 				// var form_data=$('#stock_issue_form').serialize();







// 				$('#stock_issue_submit').prop('disabled',true);



// 				var url=base_url+ "index.php/admin_ret_stock_issue/stock_issue/save?nocache=" + my_Date.getUTCSeconds();



// 				$.ajax({



// 					url:url,



// 					data: {"nt_data":non_tagged,'branch_select':$('#branch_select').val(),'issue_type':$('#issue_type').val(),'stock_type':$('#stock_type').val(),'issued_to':$('#issued_to').val(),'cus_id':$('#cus_id').val(),'rate_per_gram':$('#rate_per_gram').val(),'remark':$('#remark').val(),'id_employee':$('#issue_employee').val(),'karigar':$('#karigar').val(),'form_secret':$('#form_secret').val(),'type_issue':$("input[name='order[issue_receipt_type]']:checked").val(),'issued_type':$('#issued_type').val(),'issued_branch':$('#issued_branch').val()},



// 					type:"POST",



// 					dataType:"JSON",



// 					success:function(data){







// 						if(data.status)



// 						{



// 							$("div.overlay").css("display", "none"); 



// 							$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});



// 							var issue_receipt_type = $("input[name='order[issue_receipt_type]']:checked").val();  



// 							if(issue_receipt_type==1)



// 							{



// 								window.open( base_url+'index.php/admin_ret_stock_issue/stock_issue/issue_print/'+data['id_stock_issue'],'_blank');



								



// 							}



// 							location.href=base_url+'index.php/admin_ret_stock_issue/stock_issue/list';



// 						}



// 						else



// 						{



// 							window.location.reload();



// 							$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



// 							$("div.overlay").css("display", "none"); 



// 						}



						



// 					},



// 					error:function(error)  



// 					{	



// 						$("div.overlay").css("display", "none"); 



// 					} 



// 				});



// 			}



// 		}



	



       



// });





function get_ActiveSections()

{

	$("#section_select option").remove();

    my_Date = new Date();

    $.ajax({

        type: 'POST',

        url: base_url+"index.php/admin_ret_catalog/get_sectionBranchwise?nocache=" + my_Date.getUTCSeconds(),

        data:{'id_branch':($('#branch_select').val() || $('#id_branch').val())},

        dataType:'json',

        success:function(data){

		 	console.log(data);

            var id=$("#id_section").val();

            $.each(data,function(key, item){

                $("#section_select").append(

                    $("<option></option>")

                    .attr("value",item.id_section)

                    .text(item.section_name)

                );

            });

			$('#section_select').select2({

				placeholder:"Select Section",

				allowClear: true

			});



			$("#section_select").select2("val",(id!='' && id>0?id:''));



			$(".overlay").css("display","none");

        }

    })

}



function set_stock_issue_list()



{



	my_Date = new Date();



	$.ajax({



			 url:base_url+ "index.php/admin_ret_stock_issue/stock_issue?nocache=" + my_Date.getUTCSeconds(),



			 data:{'status':$('#issue_status').val()},



			 dataType:"JSON",



			 type:"POST",



			 success:function(data){	 



				 var list 	= data.list;



				 var access		= data.access;	



				 $('#total_count').text(list.length);



		



			 	var oTable = $('#issue_list').DataTable();



				 oTable.clear().draw();



				  



				 if (list!= null && list.length > 0)



				 {  	



					oTable = $('#issue_list').dataTable({



						"bDestroy": true,



		                "bInfo": true,



		                "bFilter": true,



		                "scrollX":'100%',

				"bAutoWidth": false,



		                "bSort": true,



		                "dom": 'lBfrtip',



		                "order": [[ 0, "desc" ]],



		                "buttons" : ['excel','print'],



				        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },



						"aaData": list,



						"aoColumns": [	{ "mDataProp": "id_stock_issue", "sWidth": "40px" },

										{ "mDataProp": "issue_no", "sWidth": "100px" },

										{ "mDataProp": "branch_name", "sWidth": "110px" },

										{ "mDataProp": "tag_code", "sWidth": "120px" },

										{ "mDataProp": "cat_name", "sWidth": "120px" },

										{ "mDataProp": "issue_date", "sWidth": "90px" },

										{ "mDataProp": "issue_type", "sWidth": "110px" },  

										{ "mDataProp": "emp_name", "sWidth": "100px" }, 

								{ "sWidth": "140px", "mDataProp": function ( row, type, val, meta ) {

									var status = row.issue_status || '';

									var badgeClass = '';
									var icon = '';

									if (status === 'Issued') {
										badgeClass = 'background:#27a243;color:#fff;';
										icon = '<i class="fa fa-check-circle"></i> ';
									} else if (status === 'Received') {
										badgeClass = 'background:#1a7eb8;color:#fff;';
										icon = '<i class="fa fa-inbox"></i> ';
									} else if (status === 'Partially Received') {
										badgeClass = 'background:#e07b00;color:#fff;';
										icon = '<i class="fa fa-adjust"></i> ';
									} else if (status === 'Approval Pending') {
										badgeClass = 'background:#d4ac0d;color:#fff;';
										icon = '<i class="fa fa-clock-o"></i> ';
									} else if (status === 'Rejected') {
										badgeClass = 'background:#c0392b;color:#fff;';
										icon = '<i class="fa fa-times-circle"></i> ';
									} else {
										badgeClass = 'background:#888;color:#fff;';
									}

									return '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:600;white-space:nowrap;' + badgeClass + '">' + icon + status + '</span>';
								}
								},

										{

											"mDataProp": null,
											"sWidth": "30px",

											"sClass": "control center", 

											"sDefaultContent": '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'

											},

									

										{ "sWidth": "80px", "mDataProp": function ( row, type, val, meta ) {

                                            id= row.id_stock_issue;

                                            print_url=base_url+'index.php/admin_ret_stock_issue/stock_issue/issue_print/'+id;



                                            print_url_det=base_url+'index.php/admin_ret_stock_issue/stock_issue/issue_print_detail/'+id;



                                            action_content='<a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" ><i class="fa fa-print" ></i></a>'+'<a href="'+ print_url_det+'" target="_blank" class="btn btn-primary btn-print" data-toggle="tooltip" ><i class="fa fa-print" ></i></a>';



                                            return action_content;



                                            }



                                        }



										



									 ]



						});			  	 	



					



						var anOpen = [];



						$(document).on('click', "#issue_list .control", function () {



							var nTr = this.parentNode;



							var i = $.inArray(nTr, anOpen);



							if (i === -1) {



								$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');



								oTable.fnOpen(nTr, fnFormatRowDetails(oTable, nTr), 'details');



								anOpen.push(nTr);



							}



							else {



								$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');



								oTable.fnClose(nTr);



								anOpen.splice(i, 1);



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







function fnFormatRowDetails(oTable, nTr) {



	var oData = oTable.fnGetData(nTr);



	var rowDetail = '';



	var prodTable =



		'<div class="innerDetails">' +	



		'<table class="table table-responsive table-bordered text-center table-sm">' +



		'<tr class="bg-teal">' +



		'<th>S.No</th>'+ 



        '<th>Received by </th>'+



        '<th>Received date</th>'+



        '<th>LWT </th>'+



        '<th>GWT</th>'+



        '<th>NWT</th>'+



		'<th>Action</th>'+



		'</tr>';



	var summary = oData.summary;



	$.each(summary, function (idx, val) {



		id = val.id_stock_issue;



		date = val.received_date;



		print_url = base_url+'index.php/admin_ret_stock_issue/stock_issue/issue_print/'+id+'/'+val.received_time;



		prodTable +=



			'<tr class="prod_det_btn">' +



			'<td>' + parseFloat(idx + 1) + '</td>' +



			'<td>'+val.emp_name+'</td>'+



			'<td>'+val.received_date+'</td>'+



			'<td>'+val.less_wt+'</td>'+



			'<td>'+val.gross_wt+'</td>'+



			'<td>'+val.net_wt+'</td>'+



			'<td>'+'<a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" ><i class="fa fa-print" ></i></a>'+'</td>'+



			'</tr>';



	});



	rowDetail = prodTable + '</table></div>';



	return rowDetail;



}







$('#repair_tag_search').on('click',function(){



    var tag_code=$('#repair_tag_code').val();



    tag_search=true;



     $('#stockrepair_item_detail > tbody tr').each(function(idx, row){



         curRow = $(this);



         if(curRow.find('.tag_code').val()==tag_code)



         {



             $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists.'});



             tag_search=false;



             return false;



         }



     });



     if(tag_search)



     {



          get_tag_details(tag_code);



     }



   



});























function calculate_tag_issue_details()



{



    var total_pcs=0;



    var total_gwt=0;



    var total_nwt=0;



    var total_amount=0;



    var total_tax_amount=0;



    var total_taxable_amount=0;



    var total_stone_amount=0;



    var total_othermetal_amount=0;







    $('#tagissue_item_detail > tbody tr').each(function(idx, row){



         curRow = $(this);



         total_pcs+=parseFloat(curRow.find('.piece').val());



         total_gwt+=parseFloat(curRow.find('.gross_wt').val());



         total_nwt+=parseFloat(curRow.find('.net_wt').val());



         



         



         



		 var rate_per_gram          = (curRow.find('.rate_per_gram').val()!='' ? curRow.find('.rate_per_gram').val():0);



		 var stone_price            = (curRow.find('.stone_price').val()!='' ? curRow.find('.stone_price').val():0);



		 var othermetal_amount      = (curRow.find('.othermetal_amount').val()!='' ? curRow.find('.othermetal_amount').val():0);



		 var amount                 = parseFloat((parseFloat(rate_per_gram)*parseFloat(curRow.find('.net_wt').val()))+parseFloat(stone_price)+parseFloat(othermetal_amount)).toFixed(2);



		 var tax_amount             = parseFloat(parseFloat(amount*3)/100).toFixed(2);



		



		 curRow.find('.taxable_amount').html(money_format_india(parseFloat(amount).toFixed(2)));



		 curRow.find('.tax_amount').html(money_format_india(parseFloat(tax_amount).toFixed(2)));



		 curRow.find('.tamount').html(money_format_india(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2)));



		 



		 total_amount+= parseFloat(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2));



		 total_taxable_amount+=parseFloat(amount);



		 total_tax_amount+=parseFloat(tax_amount);



		 total_stone_amount+=parseFloat(stone_price);



		 total_othermetal_amount+=parseFloat(othermetal_amount);



		 



    });







	



    $('.total_pieces').css("text-align","right").html(total_pcs);



    $('.total_gross_wt').css("text-align","right").html(money_format_india(parseFloat(total_gwt).toFixed(3)));



    $('.total_nwt').css("text-align","right").html(money_format_india(parseFloat(total_nwt).toFixed(3)));



	$('.total_amount').css("text-align","right").html(money_format_india(parseFloat(total_amount).toFixed(2)));



	$('.total_tax_amount').css("text-align","right").html(money_format_india(parseFloat(total_tax_amount).toFixed(2)));



	$('.total_taxable_amount').css("text-align","right").html(money_format_india(parseFloat(total_taxable_amount).toFixed(2)));



	$('.total_stone_amount').css("text-align","right").html(money_format_india(parseFloat(total_stone_amount).toFixed(2)));



	$('.total_othermetal_amount').css("text-align","right").html(money_format_india(parseFloat(total_othermetal_amount).toFixed(2)));



	







}











$("input[name='order[zissue_receipt_type]']:radio").change(function()



    {



        $('#stockrepair_item_detail > tbody').empty();



        $('#tagissue_item_detail > tbody').empty();



        var ordertype = $("input[name='order[issue_receipt_type]']:checked").val();  



        if(ordertype == 1){



            $('.type_issue').css("display", "block");



            $('.type_receipt').css("display", "none");



			$('.issued_to').css("display", "block");



			$('.cus_select').css("display", "block");



			$('.branch').css("display", "block");



        }else{



            $('.type_issue').css("display", "none");



            $('.type_receipt').css("display", "block");



			$('.issued_to').css("display", "none");



			$('.cus_select').css("display", "none");



			$('.branch').css("display", "none");







        }



    });



    



	$("input[name='order[issue_receipt_type]']:radio").change(function()



    {



        $('#stockrepair_item_detail > tbody').empty();



        $('#tagissue_item_detail > tbody').empty();



		let stock_type = $('#stock_type').val();



		var ordertype = $("input[name='order[issue_receipt_type]']:checked").val(); 



		var issue_to_cus = $("#issued_to").val();





		if(stock_type==1){



			if(ordertype == 1){



				$('.type_issue').css("display", "block");



				$('.type_receipt').css("display", "none");



				$('.issued_to').css("display", "block");



				$('.cus_select').css("display", "block");



				$('.branch').css("display", "block");



				if(issue_to_cus == 2)

				{

				$('.employee').css("display", "block");

				}

				if(issue_to_cus == 1)

				{

				$('.customer').css("display", "block");

				}

				if(issue_to_cus == 3)

				{

				$('.karigar').css("display", "block");

				}



			}else{



				$('.type_issue').css("display", "none");



				$('.type_receipt').css("display", "block");



				$('.issued_to').css("display", "none");



				$('.cus_select').css("display", "none");



				$('.branch').css("display", "block");



				$('.employee').css("display", "none");



				$('.customer').css("display", "none");



				$('.karigar').css("display", "none");





			}	



		}else if(stock_type==2){



			if(ordertype == 1){



				$('.receipttag').css("display","block");



				if(issue_to_cus == 2)

				{

				$('.employee').css("display", "block");

				}

				if(issue_to_cus == 1)

				{

				$('.customer').css("display", "block");

				}

				if(issue_to_cus == 3)

				{

				$('.karigar').css("display", "block");

				}



			}else{



				$('.receipttag').css("display","none");





				$('.employee').css("display", "none");



				$('.customer').css("display", "none");



				$('.karigar').css("display", "none");



			}







		}



    });





function get_StockIssueItems()



{



        $('#select_issue_no option').remove();



		my_Date = new Date();



		let stock_type = $('#stock_type').val();



		$.ajax({ 



		url:base_url+ "index.php/admin_ret_stock_issue/get_StockIssuedItems?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



        type:"post",



        data : {'stock_type':stock_type},



        dataType:"JSON",



        success:function(data)



        {   



        	    var id=$('#select_issue_no').val();



        	    stockIssueDetails=data;    



                $.each(data, function (key, item) {					  				  			   		



                    $("#select_issue_no").append(						



                    $("<option></option>")						



                    .attr("value", item.id_stock_issue)						  						  



                    .text(item.issue_no)						  					



                    );			   											



                });	



            



             	$("#select_issue_no").select2({			    



            	 	placeholder: "Select Issue No",			    



            	 	allowClear: true		    



             	});	



             	



             



             	$("#select_issue_no").select2("val","");







         	    $(".overlay").css("display", "none");	



        },



        error:function(error)  



        {	



        } 



    	});



}















$('#receipt_tag_search').on('click',function(){



    var allow_search=false;



    var tag_code=$('#receipt_tag_code').val();



    if($('#select_issue_no').val()!='' && $('#select_issue_no').val()!=null)



    {



        if(tag_code!='')



        {



            $.each(stockIssueDetails,function(k,items){



                if(items.id_stock_issue==$('#select_issue_no').val())



                {



                    $.each(items.tag_details,function(key,tags){



                       if(tags.tag_code==tag_code)



                       {



                           allow_search=true;



                       }



                    });



                }



            });



            if(allow_search)



            {



                tag_search=true;



                $('#tag_receipt_item_detail > tbody tr').each(function(idx, row){



                    curRow = $(this);



                    if(curRow.find('.tag_code').val()==tag_code)



                    {



                        $('#receipt_tag_code').val('');



                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists.'});



                        tag_search=false;



                        return false;



                    }



                });



                if(tag_search)



                {



                    get_receipt_tag_details(tag_code);



                }



            }



            else



            {



                $('#receipt_tag_code').val('');



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Tag Code.'});



            }



        }



        else



        {



             $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter The Tag Code.'});



        }



    }



    else



    {



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue No.'});



    }



});







$("#receipt_tag_code").on('paste', function() {







	setTimeout(function() {



		var allow_search=false;



		var tag_code=$('#receipt_tag_code').val();



		if($('#select_issue_no').val()!='' && $('#select_issue_no').val()!=null)



		{



			if(tag_code!='')



			{



				$.each(stockIssueDetails,function(k,items){



					if(items.id_stock_issue==$('#select_issue_no').val())



					{



						$.each(items.tag_details,function(key,tags){



						   if(tags.tag_code==tag_code)



						   {



							   allow_search=true;



						   }



						});



					}



				});



				if(allow_search)



				{



					tag_search=true;



					$('#tag_receipt_item_detail > tbody tr').each(function(idx, row){



						curRow = $(this);



						if(curRow.find('.tag_code').val()==tag_code)



						{



							$('#receipt_tag_code').val('');



							$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists.'});



							tag_search=false;



							return false;



						}



					});



					if(tag_search)



					{



						get_receipt_tag_details(tag_code);



					}



				}



				else



				{



					$('#receipt_tag_code').val('');



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Tag Code.'});



				}



			}



			else



			{



				 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter The Tag Code.'});



			}



		}



		else



		{



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue No.'});



		}







	}, 10);







});







$('#receipt_old_tag_search').on('click',function(){



	var allow_search=false;



	var old_tag_code=$('#receipt_old_tag_code').val();



	if($('#select_issue_no').val()!='' && $('#select_issue_no').val()!=null)



	{



		if(old_tag_code!='')



		{



			$.each(stockIssueDetails,function(k,items){



				if(items.id_stock_issue==$('#select_issue_no').val())



				{



					$.each(items.tag_details,function(key,tags){



					   if(tags.old_tag_id==old_tag_code)



					   {



						   allow_search=true;



					   }



					});



				}



			});



			if(allow_search)



			{



				tag_search=true;



				$('#tag_receipt_item_detail > tbody tr').each(function(idx, row){



					curRow = $(this);



					if(curRow.find('.old_tag_code').val()== old_tag_code)



					{



						$('#receipt_tag_code').val('');



						$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Old Tag Already Exists.'});



						tag_search=false;



						return false;



					}



				});



				if(tag_search)



				{



					get_receipt_tag_details('',old_tag_code);



				}



			}



			else



			{



				$('#receipt_old_tag_code').val('');



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Old Tag Code.'});



			}



		}



		else



		{



			 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter The Old Tag Code.'});



		}



	}



	else



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue No.'});



	}



   



});







$("#receipt_old_tag_code").on('paste', function() {



	setTimeout(function() {



		var allow_search=false;



		var old_tag_code=$('#receipt_old_tag_code').val();



		if($('#select_issue_no').val()!='' && $('#select_issue_no').val()!=null)



		{



			if(old_tag_code!='')



			{



				$.each(stockIssueDetails,function(k,items){



					if(items.id_stock_issue==$('#select_issue_no').val())



					{



						$.each(items.tag_details,function(key,tags){



						   if(tags.old_tag_id==old_tag_code)



						   {



							   allow_search=true;



						   }



						});



					}



				});



				if(allow_search)



				{



					tag_search=true;



					$('#tag_receipt_item_detail > tbody tr').each(function(idx, row){



						curRow = $(this);



						if(curRow.find('.old_tag_code').val()== old_tag_code)



						{



							$('#old_receipt_tag_code').val('');



							$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Old Tag Already Exists.'});



							tag_search=false;



							return false;



						}



					});



					if(tag_search)



					{



						get_receipt_tag_details('',old_tag_code);



					}



				}



				else



				{



					$('#receipt_tag_code').val('');



					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Old Tag Code.'});



				}



			}



			else



			{



				 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter The Old Tag Code.'});



			}



		}



		else



		{



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Issue No.'});



		}



	}, 10);







});











function get_receipt_tag_details(tag_code,old_tag_code)



{



    var issue_type = $('#issue_type').val();  



    my_Date = new Date();



	$.ajax({



		type:"POST",



		url: base_url+"index.php/admin_ret_stock_issue/get_receipt_tag_scan_details?nocache=" + my_Date.getUTCSeconds(),



		cache:false,		



		dataType:"JSON",



		data:{'tag_code' : tag_code,'old_tag_code':old_tag_code,'id_branch': ($("#branch_select").val() || $("#id_branch").val())},



		success:function(data){



                  



			receipt_details = data;







		    if(data.length>0)



		    {



		        var html = "";



        	    $.each(data,function(key,items){



    		        html+='<tr>'



    		                +'<td><input type="hidden" class="tag_id" name="tag_id[]" value="'+items.value+'"><input type="hidden" class="old_tag_code" name="old_tag_code[]" value="'+items.old_tag_id+'"><input type="hidden" class="tag_code" name="tag_code[]" value="'+items.label+'">'+items.label+'</td>'



    		                +'<td>'+items.catname+'</td>'



    		                +'<td>'+items.purname+'</td>'



    		                +'<td>'+items.product_name+'</td>'



    		                +'<td>'+items.design_name+'</td>'



    		                +'<td>'+items.sub_design_name+'</td>'



    		                +'<td><input type="hidden" class="piece" value="'+items.piece+'">'+money_format_india(items.piece)+'</td>'



    		                +'<td><input type="hidden" class="gross_wt" value="'+items.gross_wt+'">'+money_format_india(items.gross_wt)+'</td>'



							+'<td><input type="hidden" class="net_wt" value="'+items.net_wt+'">'+money_format_india(items.net_wt)+'</td>'



							+'<td class="rate">'+items.rate_per_gram+'</td>'



							+'<td><input type="hidden" class="stone_price" name="stone_price" value="'+items.stone_price+'">'+money_format_india(items.stone_price)+'</td>'



							+'<td><input type="hidden" class="othermetal_amount" name="othermetal_amount" value="'+items.othermetal_amount+'">'+money_format_india(items.othermetal_amount)+'</td>'



							+'<td class="taxable_amount"></td>'



							+'<td class="tax">3%</td>'



							+'<td class="tax_amount"></td>'



							+'<td class="tamount"></td>'



    		                +'<td><a href="#" onClick="remove_rec_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



    		               +'</tr>';



    		        });



	            if($('#tag_receipt_item_detail > tbody  > tr').length>0)



            	{



            	    $('#tag_receipt_item_detail > tbody > tr:first').before(html);



            	}else{



            	    $('#tag_receipt_item_detail tbody').append(html);



            	}



            	calculate_tag_receipt_details();



            	$('#receipt_tag_code').val('');



            	$('#receipt_tag_code').focus();



				$('#receipt_old_tag_code').val('');



            	$('#receipt_old_tag_code').focus();



		    }



		    else



		    {



		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found.'});



		    }



		}



	});



}



















$(document).on('change','#gold_rate,#silver_rate',function(e) {



 



	calculateSaleValue();







});







$("#est_cus_name").on("keyup",function(e){ 



	var customer = $("#est_cus_name").val();



	if(customer.length >= 1) { 



		getSearchCustomers(customer);



	}



}); 







function getSearchCustomers(searchTxt){



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_estimation/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),             



        dataType: "json", 



        method: "POST", 



        data: {'searchTxt': searchTxt,'esti_for':1}, 



        success: function (data) {



			//console.log(data);



			$( "#est_cus_name" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{ 



					e.preventDefault();



					$("#selected_cus_mobile").val(i.item.mobile);



					$("#est_cus_name").val(i.item.label);



					$("#cus_id").val(i.item.value);



				},



				change: function (event, ui) {



					if (ui.item === null) {



						$(this).val('');



						$('#est_cus_name').val('');



						$("#cus_id").val("");



	



					}



			    },



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            if(searchTxt != ""){



						if (i.content.length === 0) {



						    var mobile=$('#est_cus_name').val();







						    if(mobile.length==10)



						    {



						         $("#customerAlert").html('');



						    }else{



						        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter a valid customer name / mobile'});



						    }



						}



						else{



						   $("#customerAlert").html('');



						} 



					}else{



					}



		        },



				 minLength: 1,



			});



        }



     });



}



function get_metal_rates_by_branch(id_branch)







{







	my_Date = new Date();







	$.ajax({







			 url:base_url+ "index.php/admin_ret_tagging/get_metal_rates_by_branch?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),







			 data:  {'id_branch':id_branch},







			 type:"POST",







			 dataType: "json", 







			 async:false,







			 	  success:function(data){







			 	    metal_rates = data; 







			 	  	$('.per-grm-sale-value').html(data.goldrate_22ct);







			 	  	$('.silver_per-grm-sale-value').html(data.silverrate_1gm);







			 	  	$('.mjdmagoldrate_22ct').html(data.mjdmagoldrate_22ct);







			 	  	$('.mjdmasilverrate_1gm').html(data.mjdmasilverrate_1gm);







			 	  	$('.goldrate_18ct').html(data.goldrate_18ct);







			 	  	$('.goldrate_22ct').html(data.goldrate_22ct);







			 	  	$('.silverrate_1gm').html(data.silverrate_1gm);







			 	  	$('#goldrate_22ct').val(data.goldrate_22ct);







			 	  	$('#silverrate_1gm').val(data.silverrate_1gm);







			 	  	







				  },







				  error:function(error)  







				  {







					 $("div.overlay").css("display", "none"); 







				  }	 







		  });







}



$('#branch_select').on('change',function(){







	if(this.value!='')







	{







		$('#id_branch').val(this.value);







		if(ctrl_page[2]!='list')











		{



			get_metal_rates_by_branch(this.value);

			get_ActiveSections(this.value);



		}







	}







	else







	{







		$('#id_branch').val('');







	}







});























$('#issue_tag_search').on('click',function(){



    var tag_code=$('#issue_tag_code').val();





    if(tag_code!='')



    {

        tag_search=true;



         $('#tagissue_item_detail > tbody tr').each(function(idx, row){



             curRow = $(this);



             console.log(curRow.find('.tag_code').val());



             console.log(tag_code);



             if(curRow.find('.tag_code').val()==tag_code)



             {



                 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists.'});



                 tag_search=false;



                 return false;



             }

			 





         });



         if(tag_search)



         {



             if($('#metal').val()!='' && $('#metal').val()!=null && $('#section_select').val()!='' && $('#section_select').val()!=null)



             {



                 get_tag_details(tag_code);
				  $('#issue_tag_code').val('');

				 $('#issue_tag_code').focus();


             }else{



				if($('#metal').val()=='')

				{



                 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Metal.'});

				}

				else 

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Section.'});

				}

             }





         }



    }else{



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Tag Code.'});



    }



});







$("#issue_tag_code").on('paste', function() {



	



	setTimeout(function() {



		var tag_code=$('#issue_tag_code').val();







		if(tag_code!='')



		{



		    tag_search=true;



    		 $('#tagissue_item_detail > tbody tr').each(function(idx, row){



    			 curRow = $(this);



    			 console.log(curRow.find('.tag_code').val());



    			 console.log(tag_code);



    			 if(curRow.find('.tag_code').val()==tag_code)



    			 {



    				 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists.'});



    				 tag_search=false;



    				 return false;



    			 }

			



    		 });



    		 if(tag_search)



    		 {



    		    if($('#metal').val()!='' && $('#metal').val()!=null && $('#section_select').val()!='' && $('#section_select').val()!=null)



             {



                 get_tag_details(tag_code);
				$('#issue_tag_code').val('');

				 $('#issue_tag_code').focus();

             }else{



				if($('#metal').val()=='')

				{



                 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Metal.'});

				}

				else 

				{

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Section.'});

				}

             }



    			  



    		 }



		}



		else{



		    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Tag Code.'});



		}



	}, 10);







});



$('#issue_old_tag_search').on('click',function(){



    var old_tag_code=$('#issue_old_tag_code').val();







    if(old_tag_code!='')



    {



        tag_search=true;



         $('#tagissue_item_detail > tbody tr').each(function(idx, row){



             curRow = $(this);



             console.log(curRow.find('.old_tag_code').val());



             console.log(old_tag_code);



             if(curRow.find('.old_tag_code').val()==old_tag_code)



             {



                 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists.'});



                 tag_search=false;



                 return false;



             }

		



         });



         if(tag_search)



         {



			if($('#metal').val()!='' && $('#metal').val()!=null && $('#section_select').val()!='' && $('#section_select').val()!=null)



			{



				get_tag_details('',old_tag_code);
				$('#issue_old_tag_code').val('');

				$('#issue_old_tag_code').focus();


			}else{



			   if($('#metal').val()=='')

			   {



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Metal.'});

			   }

			   else 

			   {

				   $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Section.'});

			   }

			}



         }



    }



    else{



        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Tag Code.'});



    }



   



});







$("#issue_old_tag_code").on('paste', function() {



	setTimeout(function() {



		var old_tag_code=$('#issue_old_tag_code').val();







		if(old_tag_code!='')



		{



		    tag_search=true;



    		 $('#tagissue_item_detail > tbody tr').each(function(idx, row){



    			 curRow = $(this);



    			 console.log(curRow.find('.old_tag_code').val());



    			 console.log(old_tag_code);



    			 if(curRow.find('.old_tag_code').val()==old_tag_code)



    			 {



    				 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists.'});



    				 tag_search=false;



    				 return false;



    			 }



			



    		 });



    		 if(tag_search)



    		 {



				if($('#metal').val()!='' && $('#metal').val()!=null && $('#section_select').val()!='' && $('#section_select').val()!=null)



				{

   

					get_tag_details('',old_tag_code);
					$('#issue_old_tag_code').val('');

					$('#issue_old_tag_code').focus();

				}else{

   

				   if($('#metal').val()=='')

				   {

   

					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Metal.'});

				   }

				   else 

				   {

					   $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Section.'});

				   }

				}

    			  



    		 }



		}



		else{



		    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Tag Code.'});



		}







	}, 10);







});







function get_tag_details(tag_code,old_tag_code="")



{



    var issue_type = $('#issue_type').val();  



    my_Date = new Date();



	$.ajax({



		type:"POST",



		url: base_url+"index.php/admin_ret_stock_issue/get_tag_scan_details?nocache=" + my_Date.getUTCSeconds(),



		cache:false,		



		dataType:"JSON",



		data:{'tag_code':tag_code,'old_tag_code':old_tag_code,'id_branch': ($("#branch_select").val() || $("#id_branch").val()),'id_metal': $("#metal").val(),

		

		'id_section': $("#section_select").val()},



		success:function(data){



			console.log(data);



		    if(data.length>0)           



		    {



             var section="";



			 var html="";



        	 $.each(data,function(key,items){



			

                 section =items.id_section;



		    	 rate_field=items.rate_field;



		        var rate_per_gram  = (metal_rates[rate_field]!='' && metal_rates[rate_field]!='undefined' ? metal_rates[rate_field]:0);



    		        html+='<tr>'



					     +'<td><input type="hidden" class="tag_id" name="tag_id[]" value="'+items.value+'"><input type="hidden" class="old_tag_code" name="old_tag_code[]" value="'+items.old_tag_id+'"><input type="hidden" class="tag_code" name="tag_code[]" value="'+items.label+'"><input type="hidden" class="section" name="id_section[]" value="'+items.id_section+'"><input type="hidden" class="id_metal" name="id_metal" value="'+items.id_metal+'"><input type="hidden" class="rate_field" name="rate_field[]" value="'+items.rate_field+'"><input type="hidden" class="rate_per_gram" name="rate_per_gram" value="'+rate_per_gram+'">'+items.label+'</td>'



    		                +'<td>'+items.catname+'</td>'



    		                +'<td>'+items.purname+'</td>'



							+'<td>'+items.section_name+'</td>'



    		                +'<td>'+items.product_name+'</td>'



    		                +'<td>'+items.design_name+'</td>'



    		                +'<td>'+items.sub_design_name+'</td>'



    		                +'<td style="text-align:right;"><input type="hidden" class="piece" value="'+items.piece+'">'+items.piece+'</td>'



    		                +'<td style="text-align:right;"><input type="hidden" class="gross_wt" value="'+items.gross_wt+'">'+money_format_india(items.gross_wt)+'</td>'



    		                +'<td style="text-align:right;"><input type="hidden" class="net_wt" value="'+items.net_wt+'">'+money_format_india(items.net_wt)+'</td>'



							+'<td style="text-align:right;" class="rate">'+rate_per_gram+'</td>'



							+'<td style="text-align:right;"><input type="hidden" class="stone_price" name="stone_price" value="'+items.stone_price+'">'+money_format_india(items.stone_price)+'</td>'



							+'<td style="text-align:right;" ><input type="hidden" class="othermetal_amount" name="othermetal_amount" value="'+items.othermetal_amount+'">'+money_format_india(items.othermetal_amount)+'</td>'



							+'<td style="text-align:right;" class="taxable_amount"></td>'



							+'<td style="text-align:right;" class="tax">3%</td>'



							+'<td style="text-align:right;" class="tax_amount"></td>'



							+'<td style="text-align:right;" class="tamount"></td>'



    		                +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



    		               +'</tr>';



					



    		        });



					











	            if($('#tagissue_item_detail > tbody  > tr').length>0)



            	{



            	    $('#tagissue_item_detail > tbody > tr:first').before(html);



            	}else{



            	    $('#tagissue_item_detail tbody').append(html);



            	}



            	calculate_tag_issue_details();



            	// ($('#issue_tag_code').val('');



            	// $('#issue_tag_code').focus();



				// $('#issue_old_tag_code').val('');



            	// $'#issue_old_tag_code').focus();







				



		    }



			else if(section != $("#section_select").val())



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Valid Section.'});



			}



		    else



		    {

			

		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found.'});



		    }



		}



	});



}







$(document).on('change','#rate_per_gram',function(e) {



    if($('#metal').val()!='' && $('#metal').val()!=null)



    {



        var rate_per_gram = $('#rate_per_gram').val();



        var id_metal      = $('#metal').val();



        var current_rate  = 0;



        var min_tol       = 50;



        var max_tol       = 50;



        if(id_metal==1)



        {



            current_rate = metal_rates['goldrate_22ct'];



        }else if(id_metal==2)



        {



            current_rate = metal_rates['silverrate_1gm'];



        }



        if(current_rate>0)



        {



            var max_tol_value = (parseFloat(current_rate) + (parseFloat(current_rate) * parseFloat(max_tol)/100)).toFixed(2);



		    var min_tol_value = (parseFloat(current_rate) - (parseFloat(current_rate) * parseFloat(min_tol)/100)).toFixed(2);



		    if(parseFloat(rate_per_gram) < parseFloat(min_tol_value)) 



		    {



                $('#rate_per_gram').val(current_rate);



                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Rate! Cannot be less than'+min_tol_value});



    



    		} else if(parseFloat(rate_per_gram) > parseFloat(max_tol_value)) {



    		    $('#rate_per_gram').val(current_rate);



    			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Invalid Rate! Cannot be more than'+min_tol_value});



    		}



        }



    	calculateSaleValue();



    }



});







function calculateSaleValue()



{



    $('#tagissue_item_detail > tbody tr').each(function(idx, row)



    {



        curRow = $(this);



		if($('#rate_per_gram').val()!='')



		{



			rate_per_gram = $('#rate_per_gram').val(); 



			curRow.find('.rate_per_gram').val(rate_per_gram);



			curRow.find('.rate').html(rate_per_gram);



			



		}



		calculate_tag_issue_details();



	});



}











function get_ActiveKarigars() {



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_catalog/karigar/active_list',



		dataType: 'json',



		success: function (data) {



		



		        $.each(data, function (key, item) {   



        		    $("#karigar").append(



        		    $("<option></option>")



        		    .attr("value", item.id_karigar)    



        		    .text(item.karigar)  



        		    );



        		}); 



        		$("#karigar").select2(



        		{



        			placeholder:"Select Karigar",



        			closeOnSelect: true		    



        		});



        		$("#karigar").select2("val",'');



		 



		}



	});



}







$("#issued_to").on('change',function(){







   if(this.value!='')



   {



       var issue_to_cus = $("#issued_to").val();



       if(issue_to_cus==1)



       {



           $('.customer').css("display","block");



		   $('.employee').css("display","none");



		   $('.karigar').css("display","none");



       }



	   else if(issue_to_cus==2)



       {



           $('.employee').css("display","block");



		   $('.customer').css("display","none");



		   $('.karigar').css("display","none");



       }



	   else



	   {



		   $('.karigar').css("display","block");



		   $('.employee').css("display","none");



		   $('.customer').css("display","none");



		  



	   }







   }



});











function get_ActiveMetals() 



{



	$("#metal option").remove();



	$.ajax({



		type: 'GET',



		url: base_url + 'index.php/admin_ret_catalog/ret_product/active_metal',



		dataType: 'json',



		success: function (data) {



			console.log(data);



			var id = $("#metal").val();



			



			$.each(data, function (key, item) {



				$('#metal').append(



					$("<option></option>")



						.attr("value", item.id_metal)



						.text(item.metal)



				);



			});



			$("#metal").select2(



				{



					placeholder: "Select Metal",



					allowClear: true



				});



		}



	});



}



//issue no change function 







$('#select_issue_no').on('change',function()



{



	let stock_type = $('#stock_type').val();







	if(stock_type==1){



		if(this.value!='')



		{



			$('#tag_receipt_item_detail tbody').empty();



			



			$.each(stockIssueDetails,function(k,items)



			{



				console.log(items);







				if(items.id_stock_issue==$('#select_issue_no').val())



				{



					var html = "";







					$.each(items.tag_details,function(key,tags)



					{



						html+='<tr>'



								+'<td><input type="hidden" class="tag_id" name="tag_id[]" value="'+tags.tag_id+'"><input type="hidden" class="tag_code" name="tag_code[]" value="'+tags.tag_code+'">'+tags.tag_code+'</td>'



								// +'<td><input type="hidden" class="old_tag_code" name="old_tag_code[]" value="'+tags.old_tag_id+'">'+tags.old_tag_id+'</td>'



								+'<td>'+tags.catname+'</td>'



								+'<td>'+tags.purname+'</td>'



								+'<td>'+tags.product_name+'</td>'



								+'<td>'+tags.design_name+'</td>'



								+'<td>'+tags.sub_design_name+'</td>'



								+'<td><input type="hidden" class="piece" value="'+tags.piece+'">'+tags.piece+'</td>'



								+'<td><input type="hidden" class="gross_wt" value="'+tags.gross_wt+'">'+tags.gross_wt+'</td>'



								+'<td><input type="hidden" class="net_wt" value="'+tags.net_wt+'">'+tags.net_wt+'</td>'



								+'<td><input type="hidden" class="rate_per_gram" value="'+tags.rate_per_gram+'">'+tags.rate_per_gram+'</td>'



								+'<td><input type="hidden" class="stone_price" name="stone_price" value="'+tags.stone_price+'">'+tags.stone_price+'</td>'



								+'<td><input type="hidden" class="othermetal_amount" name="othermetal_amount" value="'+tags.othermetal_amount+'">'+tags.othermetal_amount+'</td>'



								+'<td class="taxable_amount"></td>'



								+'<td class="tax">3%</td>'



								+'<td class="tax_amount"></td>'



								+'<td class="tamount"></td>'



								+'<td><a href="#" onClick="remove_rec_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



							+'</tr>';



					});



					



					if($('#tag_receipt_item_detail > tbody  > tr').length>0)



					{



						$('#tag_receipt_item_detail > tbody > tr:first').before(html);



					}else{



						$('#tag_receipt_item_detail tbody').append(html);



					}



					



					calculate_tag_receipt_details();



					



				}



			});



		}



	}else if(stock_type==2){







		if(this.value!='')



		{



			$('#nontag_receipt_item_detail tbody').empty();







			



			$.each(stockIssueDetails,function(k,item)



			{







				if(item.id_stock_issue==$('#select_issue_no').val())



				{



					var html = "";







					console.log(item.nontag_details);







					$.each(item.nontag_details,function(key,items)



					{



						$('#issued_branch').val(items.issued_branch);



						$('#issued_type').val(items.issued_type);







						html+='<tr>'



						+'<td>'+'<input type="checkbox" name="nt_item_sel[]" class="nt_item_sel"><input type="hidden" class="id_nontag_item" name="id_nontag_item[]" value='+items.id_nontag_item+'>'+'</td>'



						   +'<td><input type="hidden" name="id_stock_issue_detail[]" class="id_stock_issue_detail" value='+items.id_stock_issue_detail+'><input type="hidden" class="rate_per_gram" value='+items.rate_per_gram+'><input type="hidden" name="id_section[]" class="id_section" value='+items.id_section+'>'+items.section_name+'</td>'



						   +'<td><input type="hidden" name="id_product[]" class="id_product" value='+items.product+'>'+items.product_name+'</td>'



						   +'<td><input type="hidden" name="id_design[]" class="id_design" value='+items.design+'><input name="id_sub_design[]" class="id_sub_design" type="hidden" value='+items.id_sub_design+'>'+items.design_name+'</td>'







						   +'<td style="text-align:right;"><span>'+'<input class="bal_pcs" type="hidden" value='+items.no_of_piece+'><input type="number" readonly class="ntpcs" name="ntpcs[]" value='+items.no_of_piece+'><br><span class="err"></span></span></td>'







						   +'<td style="text-align:right;"><span>'+'<input class="bal_gwt" type="hidden" value='+items.gross_wt+'><input type="number" readonly class=" nt_gross_wt" name="nt_gross_wt[]" value='+money_format_india(items.gross_wt)+'><br><span class="err"></span></span></td>'







						   +'<td style="text-align:right;"><span><input type="hidden" class="bal_nwt value='+items.net_wt+'"><input type="number" readonly class=" nt_net_wt" name="nt_net_wt[]" value='+items.net_wt+'><br><span class="err"></span></span></td>'







						   +'<td style="text-align:right;" class="nttaxable_amount"></td>'



						   +'<td style="text-align:right;" class="nttax">3%</td>'



						   +'<td style="text-align:right;" class="nttax_amount"></td>'



						   +'<td style="text-align:right;" class="nttamount"></td>'



						   +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



						  +'</tr>';



					});



					



					// if($('#nontag_receipt_item_detail > tbody  > tr').length>0)



					// {



					// 	$('#nontag_receipt_item_detail > tbody > tr:first').before(html);



					// }else{



					// 	$('#nontag_receipt_item_detail tbody').append(html);



					// }







					$('#nontag_receipt_item_detail tbody').append(html);



					calculate_nontag_receipt_details();



					



				}



			});



		}



	}



	







});







function calculate_tag_receipt_details()



{



    var total_pcs=0;



    var total_gwt=0;



    var total_nwt=0;



    var total_amount=0;



    var total_tax_amount=0;



    var total_taxable_amount=0;



    var total_stone_amount=0;



    var total_othermetal_amount=0;







    $('#tag_receipt_item_detail > tbody tr').each(function(idx, row){



         curRow = $(this);



         total_pcs+=parseFloat(curRow.find('.piece').val());



         total_gwt+=parseFloat(curRow.find('.gross_wt').val());



		 total_nwt+=parseFloat(curRow.find('.net_wt').val());







		 var rate_per_gram          = (curRow.find('.rate_per_gram').val()!='' ? curRow.find('.rate_per_gram').val():0);



		 var stone_price            = (curRow.find('.stone_price').val()!='' ? curRow.find('.stone_price').val():0);



		 var othermetal_amount      = (curRow.find('.othermetal_amount').val()!='' ? curRow.find('.othermetal_amount').val():0);



		 var amount                 = parseFloat((parseFloat(rate_per_gram)*parseFloat(curRow.find('.net_wt').val()))+parseFloat(stone_price)+parseFloat(othermetal_amount)).toFixed(2);



		 var tax_amount             = parseFloat(parseFloat(amount*3)/100).toFixed(2);







		 curRow.find('.taxable_amount').html(money_format_india(parseFloat(amount).toFixed(2)));



		 curRow.find('.tax_amount').html(money_format_india(parseFloat(tax_amount).toFixed(2)));



		 curRow.find('.tamount').html(money_format_india(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2)));







		 total_amount+= parseFloat(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2));



		 total_taxable_amount+=parseFloat(amount);



		 total_tax_amount+=parseFloat(tax_amount);



		 total_stone_amount+=parseFloat(stone_price);



		 total_othermetal_amount+=parseFloat(othermetal_amount);



    });



    $('.receipt_total_pieces').html(money_format_india(total_pcs));



    $('.receipt_total_gross_wt').html(money_format_india(parseFloat(total_gwt).toFixed(3)));



    $('.receipt_total_nwt').html(money_format_india(parseFloat(total_nwt).toFixed(3)));



	$('.receipt_total_amount').html(money_format_india(parseFloat(total_amount).toFixed(2)));



	$('.receipt_total_tax_amount').html(money_format_india(parseFloat(total_tax_amount).toFixed(2)));



	$('.receipt_total_taxable_amount').html(money_format_india(parseFloat(total_taxable_amount).toFixed(2)));



	$('.receipt_total_stone_amount').html(money_format_india(parseFloat(total_stone_amount).toFixed(2)));



	$('.receipt_total_othermetal_amount').html(money_format_india(parseFloat(total_othermetal_amount).toFixed(2)));



}







$('#issue_status_search').on('click',function(){







	set_stock_issue_list();







});  







function remove_rec_row(curRow)



{



	curRow.remove();



	calculate_tag_receipt_details();



}







$('#add_new_customer').on('click', function () {

    // #branch_select is disabled for branch-level users — their branch is pre-set in session (#id_branch).
    // Accept either: the visible select has a selection, OR the select is disabled (branch pre-set from session).
    var branch_selected = ($('#branch_select').val() != '' && $('#branch_select').val() != null);
    var branch_preset   = ($('#branch_select').prop('disabled') === true);

    if (branch_selected || branch_preset) {
        // open_customer_slider() is defined in ret_general.js — shared by all pages
        open_customer_slider();
    } else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select the Branch' });
    }

});





// function get_country()



// {    $('#country option').remove();



//     $('#ed_cus_country option').remove();



//     $.ajax({



//         type: 'GET',



//         url:  base_url+'index.php/settings/company/getcountry',



//         dataType: 'json',



//         success: function(country) {



            



//             $.each(country, function (key, country) 



//             {



//                 if(country.is_default==1)



//                 {



//                     $('#id_country').val(country.id);



//                 }



                



//                 $('#country,#ed_cus_country').append(



//                 $("<option></option>")



//                 .attr("value", country.id)



//                 .text(country.name)



//                 );



                



//             });



//             var id_country=$('#id_country').val();



//             var ed_id_country=$('#ed_id_country').val();



            



//            $("#country,#ed_cus_country").select2({



//             placeholder: "Enter Country",



//             allowClear: true



//             });	



//             if($("#country").length)



//             {



//                 $("#country").select2("val",(id_country!='' ? id_country:''));



//             }



            



//             if($("#ed_cus_country").length)



//             {



//                 $("#ed_cus_country").select2("val",(ed_id_country!=null &&  ed_id_country>0 ? ed_id_country :''));



//             }



             



	        



//         },



//         error:function(error)  



//         {



        



//          }



//     });



// }



// $('#country,#ed_cus_country').on('change',function(){



//     $('#id_country').val(this.value);



//     if(this.value!='')



//     {



//         get_state(this.value);



//     }



    



// });



// $('#state,#ed_cus_state').on('change',function(){



//     if(this.value!='')



//     {



//         get_city(this.value);



//     }



      



// });



// function get_state(id)



// {



//     $('#state option').remove();



//     $('#ed_cus_state option').remove();



//     $.ajax({



//         type: 'POST',



//         data:{'id_country':id },



//         url:  base_url+'index.php/settings/company/getstate',



//         dataType: 'json',



//         success: function(state) {



       



//         $.each(state, function (key, state) {



            



//              if(state.is_default==1)



//             {



//                 $('#id_state').val(state.id);



//             }



                



//             $('#state,#ed_cus_state').append(



//             $("<option></option>")



//             .attr("value", state.id)



//             .text(state.name)



//             );



//         });



//          var id_state=$('#id_state').val();



         



//          var ed_id_state=$('#ed_id_state').val();



//          $("#state,#ed_cus_state").select2({



//             placeholder: "Enter State",



//             allowClear: true



//         });



        



//         if($("#state").length)



//         {



//             $("#state").select2("val",(id_state!='' ? id_state:''));



//         }



        



//         if($("#ed_cus_state").length)



//         {



//             $("#ed_cus_state").select2("val",(ed_id_state!=null && ed_id_state>0 ? ed_id_state:''));



//         }



        



            



//         },



//         error:function(error)  



//         {



//         }



//     });



// }



// function get_city(id)



// {  



//     $('#city option').remove();



//     $('#ed_cus_city option').remove();



//     $.ajax({



//         type: 'POST',



//         data:{'id_state':id },



//         url:  base_url+'index.php/settings/company/getcity',



//         dataType: 'json',



//         success: function(city) {



			



//         $.each(city, function (key, city) {



			



// 			if(city.is_default==1)



// 			{



// 				$('#id_city').val(city.id);



// 			}



        



//             $('#city,#ed_cus_city').append(



//             $("<option></option>")



//             .attr("value", city.id)



//             .text(city.name)



//             );



//         });



// 		var id_city=$('#id_city').val();



//         var ed_id_city=$('#ed_id_city').val();



        



//         $('#city,#ed_cus_city').select2({



// 			placeholder: "Enter city",



// 			allowClear: true



// 		});



		



// 		if($("#city").length)



// 		{



// 		    $("#city").select2("val", (id_city!=null? id_city :''));



// 		}



		



// 		if($("#ed_cus_city").length)



// 		{



// 		    $("#ed_cus_city").select2("val",(ed_id_city!=null && ed_id_city>0 ? ed_id_city:''));



// 		}



        



//         },



//         error:function(error)  



//         {



            



//         }



//     });



// }



function get_village_list() {



	$('#sel_village option').remove();



	$('#id_village').val("");



	$('#ed_sel_village option').remove();



	my_Date = new Date();



	$.ajax({ 



	url:base_url+ "index.php/admin_ret_estimation/ajax_get_village?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),



	type:"GET",



	dataType:"JSON",



	success:function(data)



	{



	$.each(data, function (key, item) {		



		



			if(item.is_default==1) {



				$('#id_village').val(item.id_village);



			}



			$("#sel_village,#ed_sel_village").append(						



			$("<option></option>")						



			.attr("value", item.id_village)						  						  



			.text(item.village_name)						  					



			);			   											



		});						



		var id_village = $('#id_village').val();



		var ed_id_village = $('#ed_id_village').val();



		$("#sel_village,#ed_sel_village").select2({			    



			placeholder: "Select Area",			    



			allowClear: true		    



		});	



		if($("#sel_village").length) {



			$("#sel_village").select2("val", (id_village!=null? id_village :''));



		}



		if($("#ed_sel_village").length) {



			$("#ed_sel_village").select2("val",(ed_id_village!=null && ed_id_village>0 ? ed_id_village:''));



		}



		$(".overlay").css("display", "none");	



	},



	error:function(error)  



	{	



	} 



	});



}



$('#ed_sel_village').on('change',function(){



	if(this.value!='')



	{



		$('#ed_id_village').val(this.value);



	}



	else



	{



		$('#ed_id_village').val('');



	}



});



$('#sel_village').on('change',function(){



	if(this.value!='')



	{



		$('#id_village').val(this.value);



	}



	else



	{



		$('#id_village').val('');



	}



});



/* add_customer() function removed — now handled by ret_general.js
   which reads directly from the shared slider fields and calls
   admin_ret_billing/createNewCustomer for all pages including stock_issue */



function customer_detail_modal(id_customer)



{



	my_Date = new Date();



	$.ajax({



		url: base_url+'index.php/admin_ret_estimation/getCustomerDet/?nocache='+ my_Date.getUTCSeconds(),



		dataType:"json",



		method: "POST",



		data:{'id_customer':id_customer},



		success: function(data){



			



			$('#cus_pop').html(""); 



			$('#customer-popup').modal('show');



			



			$('#cus_del_country').val(data.cus_details[0].id_country);



			$('#cus_del_state').val(data.cus_details[0].id_state);



			



			$.each(data.cus_details,function(key,item){



				var cusRow="<div class='row'>"+



			"<div class='col-md-12 col-md-offset-1'>"+



				"<div class='row'>"+



					"<div class='col-md-4'>"+



						"<div class='form-group'>"+



							"<label>Customer name</label><br>"



							+ item.firstname +



						"<br></div>"+



						"<div class='form-group'>"+



							"<label>Rating</label><br>"+



							(item.is_vip == 0 ? "-" : "<span class='label bg-orange'><i class='fa fa-fw fa-star'></i> V I P</span>")						



						+"<br></div>"+



						"<div class='form-group'>"+



							"<label>Total Accounts</label><br><span class='badge bg-green'>"



							+ item.tot_account +



						"</span></div>"+



					"</div>"+



					"<div class='col-md-4'>"+



						"<div class='form-group'>"+



							"<label>Active Accounts</label><br><span class='badge bg-green'>"



							+ item.active_acc +



						"</span><br></div>"+



						"<div class='form-group'>"+



							"<label>Closed Accounts</label><br><span class='badge bg-green'>"



							+ item.closed_count +



						"</span><br></div>"+



						"<div class='form-group'>"+



							"<label>Inactive Accounts</label><br><span class='badge bg-green'>"



							+ item.inactive_acount +



						"</span><br></div>"+



					"</div>"+



					"<div class='col-md-4'>"+



						"<div class='form-group'>"+



							"<label>Gold (Grams)</label><br>"



							+ item.gold_wt +



						"<br></div>"+



						"<div class='form-group'>"+



							"<label>Silver (Grams)</label><br>"



							+ item.silver_wt +



						"<br></div>"+



						"<div class='form-group'>"+



							"<label>MRP (Rs)</label><br>"



							+ item.tot_fixed_rate +



						"<br></div>"+



					"</div>"+



			"</div>"+



			



		  "</div>";



		   $("#cus_pop").append(cusRow);



			});



			



			var tablehead="<div class='table-responsive'>"+



				"<table id='bill_list' class='table table-bordered table-striped text-center'>"+



				   "<thead>"+



					 "<tr>"+



					 "<th>Bill Date</th>"+



					   "<th>Bill No</th>"+



					   "<th>Branch</th>"+



					   "<th>Gold Wt</th>"+



					   "<th>Silver Wt</th>"+



					   "<th>MRP Amt</th>"+



					   "<th>Bill Amt</th>"+



					 "</tr>"+



				"</thead>"+



				"<tbody>";



			$.each(data.bill_details,function(key,item){



                tablehead+=



                "<tr>"+



                "<td>"+item.bill_date+"</td>"+



                "<td>"+item.bill_no+"</td>"+



                "<td>"+item.branch_name+"</td>"+



                "<td>"+item.gold_wt+"</td>"+



                "<td>"+item.silver_wt+"</td>"+



                "<td>"+item.mrp_amount+"</td>"+



                "<td>"+item.tot_bill_amount+"</td>"+



                "</tr>";



			});



			tablehead+="</tbody></table></div>";



			



			$('#cus_bill_details').html(tablehead);



			



		}



	});



}



function set_bill_data(id_cus){



	my_Date = new Date();



	$.ajax({



		url: base_url+'index.php/admin_ret_estimation/getCustomerBill/?nocache='+ my_Date.getUTCSeconds(),



		dataType:"json",



		method: "POST",



		data:{'id_cus':id_cus},



		success: function(data){



			



				var tablehead="<div class='table-responsive'>"+



				"<table id='bill_list' class='table table-bordered table-striped text-center'>"+



				   "<thead>"+



					 "<tr>"+



					 "<th>Bill Date</th>"+



					   "<th>Bill No</th>"+



					   "<th>Branch</th>"+



					   "<th>Gold Wt</th>"+



					   "<th>Silver Wt</th>"+



					   "<th>MRP Amt</th>"+



					   "<th>Bill Amt</th>"+



					 "</tr>"+



				"</thead>"+



				"<tbody>";



				$('#cus_pop').append(tablehead);



				



					 $.each(data,function(key,item){ 



					var curRow=



						"<tr>"+



						"<td>"+item.bill_date+"</td>"+



						"<td>"+item.bill_no+"</td>"+



						"<td>"+item.branch_name+"</td>"+



						"<td>"+item.gold_wt+"</td>"+



						"<td>"+item.silver_wt+"</td>"+



						"<td>"+item.mrp_amount+"</td>"+



						"<td>"+item.tot_bill_amount+"</td>"+



						"</tr>";



						



						$('#cus_pop tbody').append(curRow);



			})



			$('#cus_pop').append("</tbody></table></div>");



		}



	});



}



function getSearchCustomers(searchTxt){



    var esti_for = $("input[name='estimation[esti_for]']:checked").val();



	my_Date = new Date();



	$.ajax({



        url: base_url+'index.php/admin_ret_estimation/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),             



        dataType: "json", 



        method: "POST", 



        data: {'searchTxt': searchTxt,'esti_for':esti_for}, 



        success: function (data) {



			$( "#est_cus_name" ).autocomplete(



			{



				source: data,



				select: function(e, i)



				{ 



					e.preventDefault();



					$("#selected_cus_mobile").val(i.item.mobile);



					$("#est_cus_name").val(i.item.label);



					$("#cus_id").val(i.item.value);



					$("#cus_village").html(i.item.village_name);



				    $('#id_country').val(i.item.id_country);



				    $('#id_city').val(i.item.id_city);



				    $('#id_state').val(i.item.id_state);



					$("#cus_info").append(i.item.vip == 'Yes' ? "<span class='label bg-orange'><i class='fa fa-fw fa-star'></i> V I P</span>":"");



					$("#cus_info").append(i.item.accounts > 0 ? "&nbsp;<span class='label label-info'>Chit Customer</span>":"");	



				

					



					// if($('#estimation_chit_details > tbody > tr').length > 0)



					// {



					//     $('#estimation_chit_details > tbody').empty();



					//     calculate_purchase_details();



	                //     calculate_sales_details();



					// }



					



					customer_detail_modal(i.item.value); // Customer Purchase and Account Details



				},



				change: function (event, ui) {



					if (ui.item === null) {



						$(this).val('');



						$('#est_cus_name').val('');



						$("#cus_id").val("");



						$("#cus_village").html("");



						$("#cus_info").html("");



						// if($('#estimation_chit_details > tbody > tr').length > 0)



    					// {



    					//     $('#estimation_chit_details > tbody').empty();



    					//     calculate_purchase_details();



    	                //     calculate_sales_details();



    					// }



						/*$("#chit_cus").html("");



						$("#vip_cus").html("");*/



					}



			    },



				response: function(e, i) {



		            // ui.content is the array that's about to be sent to the response callback.



		            if(searchTxt != ""){



						if (i.content.length === 0) {



						    var mobile=$('#est_cus_name').val();



						    if(mobile.length==10)



						    {



						        create_customer(mobile);



						         $("#customerAlert").html('');



						    }else{



						        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter a valid customer name / mobile'});



						    }



						}



						else{



						   $("#customerAlert").html('');



						} 



					}else{



					}



		        },



				 minLength: 1,



			});



        }



     });



}



	



/* create_customer() — open_customer_slider() is defined in ret_general.js */
function create_customer(mobile_no) {
    open_customer_slider({ mobile_no: mobile_no });
}



/* NOTE: #add_newcutomer submit handler is now handled by ret_general.js
   which reads from the shared common_customerslider.php fields and routes
   to add_customer() via ctrl_page detection. The old handler here used wrong
   field selectors (#cus_type:checked) for the new shared slider. */



$('#gst_no,#ed_gst_no').on('change',function(){



	var gst=$(this).val();



	var gstinformat = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){3}$');



	



	if(!gstinformat.test(gst))



	{



		$('#gst_no,#ed_gst_no').val("");



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter Valid GST NO'});



		$('#gst_no,#ed_gst_no').focus();



	}



	else



	{



		$('#gst_no,#ed_gst_no').val(gst);



	}



});



$('#ed_cus_pin_code_add,#pin_code_add').on('change',function(){



	if(this.value.length!=6)



	{



		$('#ed_cus_pin_code_add,#pin_code_add').val("");



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter Valid PIN Code'});



	}



});



/* Customer search. - Start */	



$("#est_cus_name").on("keyup",function(e){ 



	var customer = $("#est_cus_name").val();



	if(customer.length >= 1) { 



		getSearchCustomers(customer);



	}



}); 



/* Ends - Customer search. */



function get_profession(){



	$('.overlay').css('display','block');



	



		$.ajax({



	



		  type: 'GET',



		  url:  base_url+'index.php/admin_settings/get_profession',



	



		  dataType: 'json',



	



		  success: function(data) {



			$.each(data, function (key,data ) {



	



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



	        if($("#profession").length > 0)



	        {



	            $("#profession").select2("val", ($('#professionval').val()!=null?$('#professionval').val():''));



	        }



			if($("#ed_profession").length>0)



			{



			    $("#ed_profession").select2("val", ($('#ed_professionval').val()!='' ? $('#ed_professionval').val():''));



			}



			



			



			



	



			$('.overlay').css('display','none');



	



			},



	     error:function(error)  {



	



			$("div.overlay").css("display", "none"); 



	     }



		});



	



}



$('#profession').on('change',function()



{



	if(this.value!='')



	{



		$('#professionval').val(this.value);



	}



	else



	{



		$('#professionval').val('');



	}



});



$('#ed_id_profession').on('change',function()



{



	if(this.value!='')



	{



		$('#ed_professionval').val(this.value);



	}



	else



	{



		$('#ed_professionval').val('');



	}



});




$('#cus_mobile').on('blur', function () {
	// type="number" can render large values in scientific notation (e.g. 4562115436 → 4.562115436e9)
	// Count digits via regex instead of raw string .length to handle this correctly
	var digits = String(this.value).replace(/\D/g, '');
	if (digits.length == 10) {
		$('#cus_mobile').val(digits); // normalize to plain digits
	} else if (digits.length > 0) {
		$.toaster({ priority: 'danger', title: 'warning!', message: '' + "</br>" + 'Please enter 10 digit mobile number..' });
		$('#cus_mobile').val('');
	}
});

///Add customer ends




////Edit Customer Starts



$('#edit_customer').on('click',function(){

    if($('#branch_select').val() == "" || $('#branch_select').val() == null){

        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please Select the Branch' });

        return false;

    }

    if ($('#cus_id').val() != '' && $('#cus_id').val() != undefined) {
        $("#demo #add_newcutomer").text('Update');
        // Clear slider fields before population
        $('#demo #id_customer').val('');
        $('#demo #cus_first_name').val('');
        $('#demo #cus_mobile').val('');
        $('#demo #cus_email').val('');
        $('#demo #address1').val('');
        $('#demo #address2').val('');
        $('#demo #address3').val('');
        $('#demo #pin_code_add').val('');
        $('#demo #customer_img').val('');
        $("#demo #cus_img_preview").attr("src", base_url + "assets/img/default.png");

        get_customer();

    } else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Select Customer Name' });
        return false;
    }

});



 



 function get_customer()



 



 {



 



    my_Date = new Date();



 



     $.ajax({



 



         type:"POST",



 



         url: base_url+"index.php/admin_ret_estimation/get_customer?nocache=" + my_Date.getUTCSeconds(),



 



         cache:false,       



 



         dataType:"JSON",



 



         data:{'id_customer' : $('#cus_id').val()},



 



         success:function(data){
             $('#demo #id_customer').val(data.id_customer);
             $('#demo #cus_first_name').val(data.firstname);
             $('#demo #cus_mobile').val(data.mobile);
             $('#demo #cus_email').val(data.email);
             $('#demo #id_country').val(data.id_country);
             $('#demo #id_state').val(data.id_state);
             $('#demo #id_city').val(data.id_city);
             $('#demo #address1').val(data.address1);
             $('#demo #address2').val(data.address2);
             $('#demo #address3').val(data.address3);
             $('#demo #pin_code_add').val(data.pincode);
             $('#demo #gst_no').val(data.gst_number);
             $('#demo #cus_img_preview').attr("src",base_url+data.img_path);
             $('#demo #title').val(data.title);
             $('#demo #professionval').val(data.id_profession);
             $('#demo #date_of_birth').val(data.date_of_birth);
             $('#demo #date_of_wed').val(data.date_of_wed);

             if(data.cus_type==1)
             {
                 $('#demo #vip1').attr('checked', true);
                 $('.gst').hide();
             }else{
                 $('#demo #vip0').attr('checked', true);
                 $('.gst_no').show();
             }

             get_country();
             get_profession();

             var myOffcanvas = document.getElementById('demo');
             var bsOffcanvas = bootstrap.Offcanvas.getInstance(myOffcanvas);
             if (!bsOffcanvas) {
                 bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
             }
             bsOffcanvas.show();

         }



	 });



 



 }



////Edit Customer Ends



/////Update Customer Starts



/* $("#update_cutomer").on('click', function(){



	var esti_for = $("input[name='estimation[esti_for]']:checked").val();



	if($('#ed_cus_first_name').val() == '')



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the Firstname..'});



		return false;



	}



	else if($('#ed_cus_mobile').val() == '' || $('#ed_cus_mobile').val() == null )



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the Mobile Number..'});



		return false;



	}else if($('#ed_cus_country').val() == '' || $('#ed_cus_country').val() == null)



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select the Country..'});



		return false;



	}



	else if($('#ed_cus_state').val() == '' || $('#ed_cus_state').val() == null)



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select the State..'});



		return false;



	}else if($('#ed_cus_city').val() == '' || $('#ed_cus_city').val() == null)



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Select the City..'});



		return false;



	}



	else if($('#ed_cus_address1').val() == '')



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the Address..'});



		return false;



	}



	else if($('#ed_cus_pin_code_add').val() == '')



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the Pincode..'});



		return false;



	}



	else if($('#ed_cus_pin_code_add').val()!='' && ($('#ed_cus_pin_code_add').val().length!=6))



	{



		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the Valid Pincode..'});



		return false;



	}



	



	else if(esti_for == 3)



	{



		if($('#ed_gst_no').val() == '')



		{



			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the GST No..'});



			return false;



		}else



		{



			var reggst = new RegExp('^[0-9]{2}[a-zA-Z]{4}([1-9]|[a-zA-Z]){1}[0-9]{4}[a-zA-Z]{1}([1-9]|[a-zA-Z]){3}$');



			if(!reggst.test($('#ed_gst_no').val()))



			{



				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the Valid GST No..'});



				return false;



			}



		}



	}



	update_cutomer($('#ed_cus_first_name').val(),$('#ed_cus_mobile').val(),$('#id_village').val(),$('#ed_gst_no').val(),$("#ed_cus_image")[0].files[0]);



				$('#cus_first_name').val('');



				$('#cus_mobile').val('');



}); */



/* function update_cutomer(cus_name, cus_mobile,id_village,gst_no,img)



	 { 



		//, cus_address



	    var esti_for = $("input[name='estimation[esti_for]']:checked").val();



        var gender = $("input[name='customer[gender]']:checked").val();



		var form_data = new FormData();



		form_data.append('id_customer',$("#cus_id").val());



		form_data.append('cusName',cus_name);



		form_data.append('cusMobile',cus_mobile);



		form_data.append('cusBranch',$('#id_branch').val());



		form_data.append('gst_no',gst_no);



		form_data.append('cus_type',esti_for==1 ? 1 :2);



		form_data.append('id_country',$('#ed_cus_country').val());



		form_data.append('id_state',$('#ed_cus_state').val());



		form_data.append('id_city',$('#ed_cus_city').val());



		form_data.append('address1',$('#ed_cus_address1').val());



		form_data.append('address2',$('#ed_cus_address2').val());



		form_data.append('address3',$('#ed_cus_address3').val());



		form_data.append('pincode',$('#ed_cus_pin_code_add').val());



		form_data.append('mail',$('#ed_cus_email').val());



		form_data.append('cust_img',img);



        form_data.append('customer_img',$('#ed_customer_img').val());



        



        form_data.append('title',$('#ed_title').val());



        



        form_data.append('id_profession',$('#ed_profession').val());



        form_data.append('gender',gender);



    



        form_data.append('date_of_birth',$('#ed_date_of_birth').val());



        form_data.append('date_of_wed',$('#ed_date_of_wed').val());



        form_data.append('id_village',$('#ed_sel_village').val());



    	my_Date = new Date();



		//  data: {'id_customer':$("#cus_id").val(),'cusName': cus_name, 'cusMobile' : cus_mobile, 'cusBranch' : 1,'id_village':id_village,'cus_type':(esti_for==1 ? 1 :2),'gst_no':gst_no,'id_country':$('#ed_cus_country').val(),'id_state':$('#ed_cus_state').val(),'id_city':$('#ed_cus_city').val(),'address1':$('#ed_cus_address1').val(),'address2':$('#ed_cus_address2').val(),'address3':$('#ed_cus_address3').val(),'pincode':$('#ed_cus_pin_code_add').val(),'mail':$('#ed_cus_email').val()},



    	$.ajax({



            url: base_url+'index.php/admin_ret_estimation/updateCustomer/?nocache=' + my_Date.getUTCSeconds(),             



            dataType: "json", 



            method: "POST", 



			data:form_data,



	        cache : false,



	     	enctype: 'multipart/form-data',



	     	contentType : false,



	    	processData : false,



            //Need to update login branch id here from session



            success: function (data) { 



    			if(data.success == true){



    				$('#confirm-edit').modal('toggle');



    				$("#est_cus_name").val(data.response.firstname + " - " + data.response.mobile);



    				$("#cus_id").val(data.response.id_customer);



    				



    				$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});



    			}else{



    				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});



    			}



            }



         });



    } */



////Update Customer Ends











//non tag stock issue



$('#stock_type').on('change',function(){



	let ordertype = $("input[name='order[issue_receipt_type]']:checked").val(); 







	if(ordertype==1){



		if($('#stock_type').val()==1){



			$('.tagelement').css('display','block')



			$('.nontagelement').css('display','none')



		}else{



			$('.tagelement').css('display','none')



			$('.nontagelement').css('display','block')



		}



	}else{



		if($('#stock_type').val()==1){



			$('.receipttag').css("display","block");



			$('#tag_receipt_item_detail').css("display","block")



			$('#nontag_receipt_item_detail').css("display","none");



		}else{



			$('.receipttag').css("display","none");



			$('#tag_receipt_item_detail').css("display","none")



			$('#nontag_receipt_item_detail').css("display","block")



		}



	}



	



	get_StockIssueItems();



})







function get_nontag_details()





{



	$("div.overlay").css("display", "block");



	$('nontagissue_item_detail > tbody').empty();



	my_Date = new Date();



	$.ajax({



		type:"POST",



		url: base_url+"index.php/admin_ret_stock_issue/get_nontag_scan_details?nocache=" + my_Date.getUTCSeconds(),



		cache:false,		



		dataType:"JSON",



		data:{'id_branch': ($("#branch_select").val() || $("#id_branch").val()),'id_section':$("#section_select").val()},



		success:function(data){



			console.log(data);



			// debugger;



		    if(data.length>0)           



		    {



		        var html = "";



        	    $.each(data,function(key,items){



					



		    	rate_field=items.rate_field;



		        



    		        html+='<tr>'



					     +'<td>'+'<input type="checkbox" name="nt_item_sel[]" class="nt_item_sel"><input type="hidden" class="id_nontag_item" name="id_nontag_item[]" value='+items.id_nontag_item+'>'+'</td>'



    		                +'<td><input type="hidden" name="id_section[]" class="id_section" value='+items.id_section+'>'+items.section_name+'</td>'



    		                +'<td><input type="hidden" name="id_product[]" class="id_product" value='+items.product+'>'+items.product_name+'</td>'



    		                +'<td><input type="hidden" name="id_design[]" class="id_design" value='+items.design+'><input name="id_sub_design[]" class="id_sub_design" type="hidden" value='+items.id_sub_design+'>'+items.design_name+'</td>'







    		                +'<td style="text-align:right;"><span>'+'<input class="bal_pcs" type="hidden" value='+items.no_of_piece+'><input type="number" class="col-md-6 ntpcs" name="ntpcs[]" value='+items.no_of_piece+'> of &nbsp;'+items.no_of_piece+'<br><span class="err"></span></span></td>'







    		                +'<td style="text-align:right;"><span>'+'<input class="bal_gwt" type="hidden" value='+items.gross_wt+'><input type="number" class="col-md-6 nt_gross_wt" name="nt_gross_wt[]" value='+money_format_india(items.gross_wt)+'> of &nbsp;'+items.gross_wt+'<br><span class="err"></span></span></td>'







    		                +'<td style="text-align:right;"><span><input type="hidden" class="bal_nwt value='+items.net_wt+'"><input type="number" class="col-md-6 nt_net_wt" name="nt_net_wt[]" value='+items.net_wt+' disabled> of &nbsp;'+money_format_india(items.net_wt)+'<br><span class="err"></span></span></td>'





							+'<td style="text-align:right;" class="nttaxable_amount">0.00</td>'



							+'<td style="text-align:right;" class="nttax">3%</td>'



							+'<td style="text-align:right;" class="nttax_amount">0.00</td>'



							+'<td style="text-align:right;" class="nttamount">0.00</td>'



    		                +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'



    		               +'</tr>';



    		        });











	            if($('#nontagissue_item_detail > tbody  > tr').length>0)



            	{



            	    $('#nontagissue_item_detail > tbody > tr:first').before(html);



            	}else{



            	    $('#nontagissue_item_detail tbody').append(html);



            	}



            	calculate_nontag_issue_details();



		    }



		    else



		    {



		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found.'});



		    }



		}



	});





}







$('#search_non_tag').on('click',function(){



  var section = $("#section_select").val();



	// alert(section);



  if(section !='' && section != null)

  {



	get_nontag_details();

  }

  else{



	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select Section.'});



  }



})



// $('#section_select').on('change',function(){



// 	if(this.value!="")

// 	{

// 	get_nontag_details();

// 	}



// });



$('#select_all').click(function(event) {



	$("#nontagissue_item_detail tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



	event.stopPropagation();  



	// calculateNTtotal();



});







$('#rec_select_all').click(function(event) {



	$("#nontag_receipt_item_detail tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



	event.stopPropagation();  



	// calculateNTtotal();



});











function calculate_nontag_issue_details()



{



    let total_pcs=0;



    let total_gwt=0;



    let total_nwt=0;



    let total_amount=0;



    let total_tax_amount=0;



    let total_taxable_amount=0;







    $('#nontagissue_item_detail > tbody tr').each(function(idx, row){



		curRow = $(this);



		total_pcs+=parseFloat(curRow.find('.ntpcs').val());



		total_gwt+=parseFloat(curRow.find('.nt_gross_wt').val());



		total_nwt+=parseFloat(curRow.find('.nt_net_wt').val());



		



		let rate_per_gram          = $('#rate_per_gram').val();



		



		let amount                 = parseFloat((parseFloat(rate_per_gram)*parseFloat(curRow.find('.nt_net_wt').val()))).toFixed(2);



		let tax_amount             = parseFloat(parseFloat(amount*3)/100).toFixed(2);



	



		// curRow.find('.nttaxable_amount').html(money_format_india(parseFloat(amount).toFixed(2)));



		// curRow.find('.nttax_amount').html(money_format_india(parseFloat(tax_amount).toFixed(2)));



		// curRow.find('.nttamount').html(money_format_india(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2)));



		curRow.find('.nttaxable_amount').html(isNaN(amount) ? '0.00' : money_format_india(parseFloat(amount).toFixed(2)));

        curRow.find('.nttax_amount').html(isNaN(tax_amount) ? '0.00' : money_format_india(parseFloat(tax_amount).toFixed(2)));

        curRow.find('.nttamount').html(isNaN(amount) || isNaN(tax_amount) ? '0.00' : money_format_india(parseFloat(parseFloat(amount) + parseFloat(tax_amount)).toFixed(2)));



		total_amount+= parseFloat(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2));



		total_taxable_amount+=parseFloat(amount);



		total_tax_amount+=parseFloat(tax_amount);



		 



    });







    $('.nttotal_pieces').html(total_pcs);



    $('.nttotal_gross_wt').html(money_format_india(parseFloat(total_gwt).toFixed(3)));



    $('.nttotal_nwt').html(money_format_india(parseFloat(total_nwt).toFixed(3)));



	$('.nttotal_amount').html(money_format_india(parseFloat(total_amount).toFixed(2)));



	$('.nttotal_tax_amount').html(money_format_india(parseFloat(total_tax_amount).toFixed(2)));



	$('.nttotal_taxable_amount').html(money_format_india(parseFloat(total_taxable_amount).toFixed(2)));







}











$('#rate_per_gram').on('change',function(){



	calculate_nontag_issue_details();



});







$(document).on('input',".ntpcs", function(){



	var row = $(this).closest('tr'); 



	blc = parseFloat(row.find('.bal_pcs').val());



	if($(this).val() > blc){



		row.find('.err').text('Invalid');



		row.find('.nt_piece').val(blc);



	}else{



		row.find('td:eq(3) .err').text('');



	}



});







$(document).on('input',".nt_gross_wt", function(){



	var row = $(this).closest('tr'); 



	blc = parseFloat(row.find('.bal_gwt').val());



	if($(this).val() > blc){



		row.find('.err').text('Invalid');



		row.find('.nt_gross_wt').val(blc);



	}else{



		row.find('.err').text('');



	}



});







$(document).on('input',".nt_net_wt", function(){



	var row = $(this).closest('tr'); 



	blc = parseFloat(row.find('.bal_nwt').val());



	if($(this).val() > blc){



		row.find('.err').text('Invalid');



		row.find('.nt_net_wgt').val(blc);



	}else{



		row.find('.err').text('');



	}



}); 











// $(document).on('change','.nt_net_wt',function(){



// 	calculate_nontag_issue_details();



// })





$(document).on('change','.nt_gross_wt',function(){



	curRow = $(this);



	$('.nt_net_wt').val(this.value);



	calculate_nontag_issue_details();



})















function calculate_nontag_receipt_details()



{



    let total_pcs=0;



    let total_gwt=0;



    let total_nwt=0;



    let total_amount=0;



    let total_tax_amount=0;



    let total_taxable_amount=0;







    $('#nontag_receipt_item_detail > tbody tr').each(function(idx, row){



		curRow = $(this);



		total_pcs+=parseFloat(curRow.find('.ntpcs').val());



		total_gwt+=parseFloat(curRow.find('.nt_gross_wt').val());



		total_nwt+=parseFloat(curRow.find('.nt_net_wt').val());



		



		let rate_per_gram          = curRow.find('.rate_per_gram').val();



		



		let amount                 = parseFloat((parseFloat(rate_per_gram)*parseFloat(curRow.find('.nt_net_wt').val()))).toFixed(2);



		let tax_amount             = parseFloat(parseFloat(amount*3)/100).toFixed(2);



	



		curRow.find('.nttaxable_amount').html(money_format_india(parseFloat(amount).toFixed(2)));



		curRow.find('.nttax_amount').html(money_format_india(parseFloat(tax_amount).toFixed(2)));



		curRow.find('.nttamount').html(money_format_india(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2)));



		



		total_amount+= parseFloat(parseFloat(parseFloat(amount)+parseFloat(tax_amount)).toFixed(2));



		total_taxable_amount+=parseFloat(amount);



		total_tax_amount+=parseFloat(tax_amount);



		 



    });







    $('.nttotal_pieces').html(total_pcs);



    $('.nttotal_gross_wt').html(money_format_india(parseFloat(total_gwt).toFixed(3)));



    $('.nttotal_nwt').html(money_format_india(parseFloat(total_nwt).toFixed(3)));



	$('.nttotal_amount').html(money_format_india(parseFloat(total_amount).toFixed(2)));



	$('.nttotal_tax_amount').html(money_format_india(parseFloat(total_tax_amount).toFixed(2)));



	$('.nttotal_taxable_amount').html(money_format_india(parseFloat(total_taxable_amount).toFixed(2)));







}







//non tag stock issue