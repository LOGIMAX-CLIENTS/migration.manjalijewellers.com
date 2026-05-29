$(document).ready(function() {
	$("#name").focus();
	$('[data-toggle="popover"]').popover(); 
	$(".accountnumbers").keyup(function() {
		enquiry.validateAccNum($(this));
	});
	$("#generate_otp").click(function(event) {
		event.preventDefault();
		enquiry.validatePasswd();
	});
	$("#email").change(function() {
		enquiry.validateEmail($(this));	
	});
	$("#submit").click(function(event) {
		if(!this.checkValidity())
        {
            event.preventDefault();
        }
		else
		{
			$('#existEnqForm').submit();	
		}
	});
	$('#resendOTP').click(function(event) {
		event.preventDefault();
		enquiry.resendOTP();
	});
	
/*	$( "#btnsubmit" ).click(function( event ) {
		event.preventDefault();
		enquiry.validate_form();
	});
	$( "#refCaptcha" ).click(function(event) {
		event.preventDefault();
		enquiry.refCaptcha();
	});
	$("#chit-desc").click(function(event) {
		event.preventDefault();
	});*/
});
enquiry = {
		validateAccNum: function(obj,curVal) {
			if(!isNaN($.trim(obj.val())) && $.trim(obj.val()) != '' && parseFloat($.trim(obj.val().length)) == 1)
			{
					var accNumID = obj.prop('id');
					var NewID = parseFloat(accNumID.replace(/\D/g,''))+1;
					if(parseInt(NewID) <=6)
						$("#acc"+parseFloat(NewID)).focus();
					else
						$("#mobile").focus();
			}
			else
			{
				obj.val('');
				obj.focus();
			}
		},
		validatePasswd : function() {
		//	if($.trim($("#firstname").val()) == '' || $.trim($("#scheme_code").val()) == -1 || $.trim($("#acc1").val()) == '' || $.trim($("#acc2").val()) == '' || $.trim($("#acc3").val()) == '' || $.trim($("#acc4").val()) == '' || $.trim($("#acc5").val()) == '' || $.trim($("#acc6").val()) == '' || $.trim($("#mobile").val()) == '' || $.trim($("#email").val()) == '' || $.trim($("#passwd").val()) == '' || $.trim($("#confirm_password").val()) == '')
		   if($.trim($("#firstname").val()) == '' || $.trim($("#scheme_code").val()) == -1 || $.trim($("#acc1").val()) == '' || $.trim($("#mobile").val()) == '' || $.trim($("#email").val()) == '' || $.trim($("#passwd").val()) == '' || $.trim($("#confirm_password").val()) == '')
			{	
				alert('Please fill all the required fields');
				return false;
			}
			if($.trim($("#mobile").val()).length != 10)
			{
				alert('Not a valid mobile number');
				return false;
			}
			if($.trim($("#passwd").val()).length < 8)
			{
				$("#passwd").val("");
				$("#confirm_password").val("");
				alert('Password should have minimum 8 charecters');
				return false;
			}
			if($.trim($('#passwd').val()) == $.trim($('#confirm_password').val()))
			{
					 $("#spinner").css('display','block');
					 $("#generate_otp").attr('disabled','disabled');
					 $("#scheme_code").attr('readonly','true');
					 $("#acc1").attr('readonly','true');
					 $("#acc2").attr('readonly','true');
					 $("#acc3").attr('readonly','true');
					 $("#acc4").attr('readonly','true');
					 $("#acc5").attr('readonly','true');
					 $("#acc6").attr('readonly','true');
					  $("#firstname").attr('readonly','true');
					 $("#mobile").attr('readonly','true');
					 $("#email").attr('readonly','true');
					 $("#passwd").attr('readonly','true');
					 $("#confirm_password").attr('readonly','true');
					 
					var mobile = $.trim($("#mobile").val());
					var name = $("#firstname").val();
					$.ajax({
					   url:baseURL+"index.php/user/generateOTP/"+mobile+"/0/"+name,
					   type : "POST",
					   success : function(result) {	
					   $("#spinner").css('display','none');
						  if(result == 1)
						  {
							 $('#otp_modal').modal({
								backdrop: 'static',
								keyboard: false
							 });
							 setTimeout(enquiry.enableBtn, 90000); 
						  }
						  else
						  {
							 $("#generate_otp").removeAttr("disabled");
							 $("#scheme_code").removeAttr("readonly");
							 $("#acc1").removeAttr("readonly");
							 $("#acc2").removeAttr("readonly");
							 $("#acc3").removeAttr("readonly");
							 $("#acc4").removeAttr("readonly");
							 $("#acc5").removeAttr("readonly");
							 $("#acc6").removeAttr("readonly");
							 $("#firstname").removeAttr("readonly");
							 $("#mobile").removeAttr("readonly");
							 $("#email").removeAttr("readonly");
							 $("#passwd").removeAttr("readonly");
							 $("#confirm_password").removeAttr("readonly");
							 if(result == 0)
							 	alert("Mobile number already exists.Please provide your number correctly...");
							 else if(result == 2)
							 	 alert("Not a valid mobile number...");
							 else
							 	 alert("Error occurred. Please try again later...");
						  }
					   },
					   error : function(error){
						  $("#spinner").css('display','none');
						   console.log(error);
					   }
					});
			}
			else
			{
				alert('Password and Confirm password should be same');
				$('#passwd').val("");
				$('#confirm_password').val("");
				$('#passwd').focus();
			}
	},
	validateEmail : function(elementValue) {
		 var status = false;     
		 var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
		 if (elementValue.val().search(emailRegEx) == -1) {
			  elementValue.val("");
			  alert("Please enter a valid email address");
		 }
		 else
		 {
			  $.ajax({						
				type: "POST",					   		
				url: baseURL+"index.php/user/check_email",
				data: "email="+elementValue.val(),
				success: function(data) {
					//client_sendsms(data);
					if(data==1)
					{
						document.getElementById('email').value="";
						alert("E mail already exits.Please provide correct email Id...");
					}
					else if(data == 0)
					{}
					else
					{
						document.getElementById('email').value="";
						console.log("Error occured while validating E Mail ID");
					}
				},
				error: function(request,error) {
					alert(error);
				}
			});
		 }

	},
	resendOTP : function() {
		$("#resendOTP").css("pointer-events", "none");
		$("#OTPloader").css("display","inline-block");
		var mobile = $.trim($("#mobile").val());
		var name = $("#firstname").val();
		$.ajax({
		   url:baseURL+"index.php/user/generateOTP/"+mobile+"/0/"+name,
		   type : "POST",
		   success : function(result) {	
		   $("#OTPloader").css("display","none");
				if(result == 1)
				{
					$("#resendOTP").css("display","none");
					setTimeout(enquiry.enableBtn, 90000);
				}
				else if(result == 0)
			    {
				 $("#resendOTP").css("pointer-events", "auto");
				 alert("Mobile number already exists.Please provide your number correctly...");
			    }
			    else if(result == 2)
			    {
				 $("#resendOTP").css("pointer-events", "auto");
				 alert("Not a valid mobile number...");
			    }
			    else
			    {
				 $("#resendOTP").css("pointer-events", "auto");
				 alert("Error in sending sms. Please try again later...");
			    }
			},
			error: function(request,error) {
				console.log(error);
				$("#resendOTP").css("pointer-events", "auto");
				$("#OTPloader").css("display","none");
				alert("Error in sending sms.Please try again later");
			}
		});
	},
	enableBtn : function() {
		$("#resendOTP").css("pointer-events", "auto");
		$("#resendOTP").css("display","inline-block");
	}
}