$(document).ready(function() {

	/*$('#useralert_modal').modal({

		backdrop: 'static',

		keyboard: false

	 });

	$('#useralert_modal').on('hidden.bs.modal', function () {

		$("#firstname").focus();

	}); */

	$("#generate_otp").click(function(event) {

		event.preventDefault();

		 $("div.overlay").css("display", "block");

		forgot.genOTP();

	});

	

	/*$("#submit").click(function(event) {

		if(!this.checkValidity())

        {

            event.preventDefault();

        }

		else

		{

			$('#forgotForm').submit();	

		}

	});*/

	$('#resendOTP').click(function(event) {

		event.preventDefault();

		forgot.resendOTP();

	});

	$('#ID_pwd_Submit').click(function(event) {

		event.preventDefault();

		forgot.validatePasswd();

	});

	

});		

						   

forgot = {	



    

		genOTP : function() {
 //$("#overlay").css('display','block');
						var mobile = $.trim($("#mobile").val());

						var email = $.trim($("#email").val());

						if (mobile === "" && email === "") {
							alert("Please provide either a mobile number or an email address!");
							$("div.overlay").css("display", "none"); 
							return;
						}
					
						
						var data = {
							'mobile': mobile,
							'email': email
						}
						   

			$.ajax({

					   url:base_url+"index.php/chit_admin/forgetUser_OTP",

					   type : "POST",

					   dataType: "json",

					   data : data,

					   success : function(result) {	

						$("div.overlay").css("display", "none");
					   

						  if(result == 1)

						  {
						  	 $('#otp_modal').modal({

								backdrop: 'static',

								keyboard: false

							 });

							 $("#resendOTP").css("display","none");
							 
							 setTimeout(forgot.enableBtn, 90000); 

						  }

						  else if(result == 2)

						  {

							 $("#generate_otp").removeAttr("disabled");

							 $("#mobile").removeAttr("readonly");

							 $("#email").removeAttr("readonly");

							 alert("Not a valid mobile number or email.Please provide your details correctly...");

						  }

						  else

						  {

							 $("#generate_otp").attr('disabled','false');

							 $("#mobile").removeAttr("readonly");

							  $("#email").removeAttr("readonly");

							 alert("Error in reset password. Please try again later...");
							 

						  }

					   },

					   error : function(error){

					   

						 $("div.overlay").css("display", "block");

						   console.log(error);

					   }

					});

					},

	// 	validatePasswd : function() {

	// 		if( $.trim($("#rst_passwd").val()) == '' || $.trim($("#rst_confirm_passwd").val()) == '')

	// 		{	

	// 			alert('Please fill the required fields');

	// 			return false;

	// 		}

	// 		if($.trim($("#rst_passwd").val()).length < 8)

	// 		{

	// 			$("#rst_passwd").val("");

	// 			$("#rst_confirm_passwd").val("");

	// 			alert('Password must be of minimum 8 characters.');

	// 			return false;

	// 		}

			

	// 		if($.trim($('#rst_passwd').val()) != $.trim($('#rst_confirm_passwd').val()))

	// 		{

				

	// 			alert('Password and Confirm password should be same');

	// 			$('#rst_passwd').val("");

	// 			$('#rst_confirm_passwd').val("");

	// 			$('#rst_passwd').focus();

	// 		}

			

	// 		else

	// 		{

	// 			$("#forgot_pswd").submit();

	// 		}

	// },
	validatePasswd: function () {
		const password = $.trim($("#rst_passwd").val());
		const confirmPassword = $.trim($("#rst_confirm_passwd").val());
		const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;
	
		if (password === '' || confirmPassword === '') {
			toastr.error('Please fill the required fields');
			return false;
		}
	
		if (password.length < 8) {
			$("#rst_passwd").val("");
			$("#rst_confirm_passwd").val("");
			toastr.error('Password must be at least 8 characters long.');
			return false;
		}
	
		if (!passwordRegex.test(password)) {
			$("#rst_passwd").val("");
			$("#rst_confirm_passwd").val("");
			toastr.error(
				'Password must contain at least:<br>' +
				'- 1 uppercase letter<br>' +
				'- 1 lowercase letter<br>' +
				'- 1 number<br>' +
				'- 1 special character (@$!%*?&#)<br>' +
				'- Minimum length: 8 characters'
			);
			return false;
		}
			
		if (password !== confirmPassword) {
			toastr.error('Password and Confirm password should be the same');
			$('#rst_passwd').val("");
			$('#rst_confirm_passwd').val("");
			$('#rst_passwd').focus();
			return false;
		}
	
		toastr.success('Validation successful! Submitting the form...');
		$("#forgot_pswd").submit();
	},	

	resendOTP : function() {

		$("#resendOTP").css("pointer-events", "none");

		$("#OTPloader").css("display","inline-block");

		var mobile = $.trim($("#mobile").val());

		var email = $.trim($("#email").val());		
		
		var data = {
			'mobile': mobile,
			'email': email
		}

		$.ajax({

		   url:base_url+"index.php/chit_admin/forgetUser_OTP",

		   type : "POST",

		   dataType: "json",

		   data : data,

		   success : function(result) {	

		   $("#OTPloader").css("display","none");

				if(result == 1)

				{

					$("#resendOTP").css("display","none");

					setTimeout(forgot.enableBtn, 90000);

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