$(document).ready(function() {
	
	//$('[data-toggle="popover"]').popover();   
	 	 
	$( "#verifyOTP" ).click(function( event ) {
		event.preventDefault();  
		var otp = $('#otp').val();
		if(otp.length == 6){ 
			$("#otpErr").html("");
			verifyOTP(otp);
		}else{
			$("#otpErr").html("Enter 6 digit OTP");
		}
	});	
	
	$( "#vs_appt_submit" ).click(function( event ) {
		event.preventDefault(); 
		var proceed = validateForm();
		if(proceed){
			$('#vs_appt_form').submit();	
		}else{
			$("#err").html("Please Fill required fields")
		}
	});	
	
	$("#generate_otp").click(function(event) { 
		if(($("#vs_mobile").val()).length == 10 )
		{ 
			$("#err").html('');
			$("#spinner").css('display','block');
			$("#generate_otp").attr('disabled','disabled');
			$("#vs_mobile").attr('readonly','true');
			var mobile = $.trim($("#vs_mobile").val());
			$.ajax({
			   url:baseURL+"index.php/vs_appt_book/generateOTP/"+mobile,
			   type : "POST",
			   success : function(result) {	
			   $("#spinner").css('display','none');
				  if(result == 1)
				  {  
				  	 $('#otp_modal').modal({
								backdrop: 'static',
								keyboard: false
							 });
					 $("#otp_modal #otp").focus();
					 setTimeout(enableBtn, 60000);
				  }
				  else if(result == 2)
				  {
					 $("#generate_otp").removeAttr("disabled");
					 $("#vs_mobile").removeAttr("readonly");
					 alert("Not a valid mobile number...");
				  }
				  else
				  {
					 $("#generate_otp").attr('disabled','false');
					 $("#vs_mobile").removeAttr("readonly");
					 alert("Error in sending OTP. Please try again later...");
				  }
			   },
			   error : function(error){
				  $("#spinner").css('display','none'); 
			   }
			});
		}
		else{
			$("#err").html('Enter 10 digit mobile number');
		}
	});	
		
	$( "#upd_feedback" ).click(function( event ) {
		event.preventDefault(); 
		if(($("#customer_feedback").val()).length == 0 ){
			$("#fErr").html('Please enter feedback');  
		}else{
			$("#upd_feedback").attr('disabled',true); 
			$("#fErr").html(''); 
			$.ajax({
				url:baseURL+"index.php/vs_appt_book/update_feedback",
				type : "POST",
				data: {'id_appt_request':$("#id_appt_request").val(),'customer_feedback':$("#customer_feedback").val()} ,
				async:false,
				dataType: "json",
				success : function(data) {
					window.location.reload();					
				}
			});
		}
	});
	
	jQuery("#name,#description,#mobile,#category,#description,#pdate,#pref_slot,#location,#email").on("input", function(){  
		var regex = new RegExp("^[a-zA-Z ]*$");
		var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		if(($("#name").val()).length > 0 ){
			if (!regex.test(key)) {
				event.preventDefault();
				//$("#name").val('');
				$("#nameErr").html('Only alphabets are allowed'); 
				return false;
			}else{
				alert(1);
				$("#nameErr").html(''); 
			}
		}
		if(($("#mobile").val()).length ==  10 ){
			$("#mobErr").html('');  
		}
		if(($("#category").val()).length > 0 ){
			$("#catErr").html('');  
		}
		if(($("#description").val()).length >= 300 ){
			$("#msgErr").html('Max. 300 characters');  
		}else{
			$("#msgErr").html('');  
		}
		/*if(($("#pdate").val()).length > 0 ){
			$("#pdateErr").html('');  
		}*/
		if(($("#pref_slot").val()).length > 0 ){
			('');  
		} 
		
		/*var email = $("#email").val();
		if(email.length > 0){
			var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
			if (email.search(emailRegEx) == -1) {
			  $("#email").val("");
			  $("#emailErr").html("Invalid email address");
			}
		} */
	}); 	 
	
	/*$(".slotDayBox").click(function(e){
	 	e.preventDefault(); 
	});*/
	
	$( ".date-col" ).click(function( event ) { 
		event.preventDefault();
		var id = $(this).attr("id"); 
		$(".date-col").css("background","#07b4ed"); 
		$(this).css("background","#fba017"); 
		var sel_date = $("#d_val"+id).val(); 
		$("#sel_date").html("<i class='icon-calendar'></i> "+sel_date);
		$("#sel_time").html(''); 
		$('#time_slot_modal').modal({
								backdrop: 'static',
								keyboard: false
							 }); 
		$("#slotTimeArea").html($("#slotData_"+this.id).html());
	}); 
	
	$( "#time_slot_sub" ).click(function( event ) { 
		$('#time_slot_modal').modal("toggle");
	})
	
	$(document).on('click',".time-col-btn", function(event){
		event.preventDefault();
		var id = $(this).attr("id");
		var slot_id = $(this).val();
		$("#pref_slot").val(slot_id);
		$(".time-col-btn").css("background","transparent");
		$(".time-col-btn").css("color","#786a6a");  
		$(this).css("background","#fba017"); 
		$(this).css("color","#fff");  
		$(this).css("border","none");  
		var sel_time = $("#tval"+slot_id).val();
		$("#sel_time").html("<i class='icon-time'></i> "+sel_time);
	});
    
});

function validateForm(){
	var proceed = true;
	if(($("#name").val()).length == 0 ){
		$("#nameErr").html('Name Required'); 
		proceed = false;
	}
	if(($("#mobile").val()).length == 0 ){
		$("#mobErr").html('Mobile Required'); 
		proceed = false;
	}
	if(($("#category").val()).length == 0 ){
		$("#catErr").html('Category Required'); 
		proceed = false;
	}
	/*if(($("#pdate").val()).length == 0 ){
		$("#pdateErr").html('Preferred Date Required'); 
		proceed = false;
	}*/
	if(($("#pref_slot").val()).length == 0 ){
		$("#pslotErr").html('Preferred Slot Required'); 
		proceed = false;
	} 
	return proceed;
}

function enableBtn() {
	$("#resendOTP").css("pointer-events", "auto");
	$("#resendOTP").css("display","inline-block");
}

function resendOTP() {
	$("#resendOTP").css("pointer-events", "none");
	$("#OTPloader").css("display","inline-block");
	var mobile = $.trim($("#vs_mobile").val()); 
	$.ajax({
	   url:baseURL+"index.php/user/generateOTP/"+mobile,
	   type : "POST",
	   success : function(result) {	
	   		$("#OTPloader").css("display","none");
			if(result == 1)
			{
				$("#resendOTP").css("display","none");
				setTimeout(enableBtn, 90000);
			} 
		    else if(result == 2)
		    {
			 $("#resendOTP").css("pointer-events", "auto");
			 alert("Not a valid mobile number...");
		    }
		    else
		    {
			 $("#resendOTP").css("pointer-events", "auto");
			 alert("Error in sending OTP. Please try again later...");
		    }
		},
		error: function(request,error) { 
			$("#resendOTP").css("pointer-events", "auto");
			$("#OTPloader").css("display","none");
			alert("Error in sending OTP.Please try again later");
		}
	});
}

function verifyOTP(otp){
	$.ajax({
	   url:baseURL+"index.php/vs_appt_book/verifyOTP",
	   type : "POST",
	   data : {"otp":otp},
	   success : function(result) {	 
			if(result == 1)
			{
				$('#otp_modal').modal('toggle');
				window.location.reload();
			} 
			else if(result == 0)
		    {
			 	$("#otpErr").html("Invalid OTP...");
		    }  
		},
		error: function(request,error) {   
		}
	});
}
