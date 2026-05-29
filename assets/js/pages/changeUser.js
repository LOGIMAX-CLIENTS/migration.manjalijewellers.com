$(document).ready(function() {


	if(page == 'changeMob')


		$("#mobile").focus();


	else


		$("#old_passwd").focus();


		


	$("#IDSubmit").click(function(event) {


		event.preventDefault();


		changeUser.validatePasswd()


	});


	$("#IDMobSubmit").click(function(event) {


		event.preventDefault();


		changeUser.generateOTP()


	});


	$('#resendOTP').click(function(event) {


		event.preventDefault();


		changeUser.resendOTP();


	});


});									   


changeUser = {	


		validatePasswd : function() {


			if($.trim($("#passwd").val()).length < 8)


			{


				$("#passwd").val("");


				$("#confirm_passwd").val("");


				alert('Password must be of minimum 8 characters.');


				return false;


			}


		/*	else if($.trim($("#passwd").val()) == $.trim($("#old_passwd").val()) && $.trim($("#old_passwd").val()) == $.trim($("#confirm_passwd").val()))


			{


				$("#passwd").val("");


				$("#confirm_passwd").val("");


				alert('Current and New password are same.');


				return false;


			}*/


			else if($.trim($("#passwd").val()) != $.trim($("#confirm_passwd").val()))


			{


				$("#passwd").val("");


				$("#confirm_passwd").val("");


				alert('Password and Confirm password should be same.');


				return false;


			}


			else


			{


				$("#reset_passwd").submit();


			}


		},


	  	generateOTP : function() {


		 	if($.trim($("#mobile").val()).length != 10)


			{


				alert('Not a valid mobile number');


				return false;


			}


			else


			{


				$("#spinner").css('display','block');


				$("#IDMobSubmit").attr('disabled','disabled');


				$("#mobile").attr('readonly','true');


				var mobile = $.trim($("#mobile").val());


				$.ajax({


					   url:baseURL+"index.php/user/generateOTP/"+mobile+"/1",


					   type : "POST",


					   success : function(result) {	


					   $("#spinner").css('display','none');


						  if(result == 1)


						  {


							   $('#otp_modal').modal({


								backdrop: 'static',


								keyboard: false


							 });


							   setTimeout(changeUser.enableBtn, 90000);


						  }


						  else if(result == 0)


						  {


							 $("#IDMobSubmit").removeAttr("disabled");;


							 $("#mobile").removeAttr("readonly");


							 alert("Mobile number already exists.Please provide your number correctly...");


						  }


						  else if(result == 2)


						  {


							 $("#IDMobSubmit").removeAttr("disabled");;


							 $("#mobile").removeAttr("readonly");


							 alert("Not a valid mobile number...");


						  }


						  else


						  {


							 $("#IDMobSubmit").attr('disabled','false');


							 $("#mobile").removeAttr("readonly");


							 alert("Error in registration. Please try again later...");


						  }


					   },


					   error : function(error){


						  $("#spinner").css('display','none');


						   console.log(error);


					   }


					});


				


			}


	},


	resendOTP : function() {


		$("#OTPloader").css('display','inline-block');


		$("#resendOTP").css("pointer-events", "none");


		var mobile = $.trim($("#mobile").val());


		$.ajax({


		   url:baseURL+"index.php/user/generateOTP/"+mobile+"/1",


		   type : "POST",


		   success : function(result) {	


		   $("#OTPloader").css("display","none");


				if(result == 1)


				{


					$("#resendOTP").css("display","none");


					setTimeout(changeUser.enableBtn, 90000);


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