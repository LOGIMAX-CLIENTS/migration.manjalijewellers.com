$(document).ready(function() {



	/*$('#useralert_modal').modal({



		backdrop: 'static',



		keyboard: false



	 });



	$('#useralert_modal').on('hidden.bs.modal', function () {



		$("#firstname").focus();



	}); */
	
	

     $("#firstname,#lastname").on('keypress', function (event) {
      var regex = new RegExp("^[a-zA-Z ]*$");
      var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
      if (!regex.test(key)) {
         event.preventDefault();
         return false;
      }
   });
   
   $("#firstname,#lastname").bind("cut copy paste",function(e) {
        e.preventDefault();
   });
  
	if(is_branchwise_cus_reg==1)
	 {
	 	get_branchname();
	 }
   
   
// country and state //HH
	if($("#custom_fields_country").val() == 1 || $("#custom_fields_country").val() == 2){	
	if($('#id_country').length>0)
	{
		
		signup.getCountry();

	}
	
	$("#id_country").select2({
		
		placeholder: "Select Country"
		
	});
}

 if($("#custom_fields_country").val() == 0) {
		if($('#id_country').length =101)
	{
		
		signup.getState(this.value);	

	}
}
	if($("#custom_fields_state").val() == 1 || $("#custom_fields_state").val() == 2){	
	$("#id_state").select2({

		placeholder: "Select State"

	  });
	  
	}
	
		if($("#custom_fields_city").val() == 1 || $("#custom_fields_city").val() == 2){
	$("#id_city").select2({

				  placeholder: "Select City",

				  allowClear:true

	});	
		}
	
	
	 

// country and state //
	
	
	
	
	
	
	
	$("#generate_otp").click(function(event) {



		event.preventDefault();

	

		signup.validatePasswd();



	});



	$("#email").change(function() {



		signup.validateEmail($(this));	



	});



	$("#submit").click(function(event) {



		if(!this.checkValidity())



        {



            event.preventDefault();



        }



		else



		{



			$('#signupForm').submit();	



		}



	});



	$('#resendOTP').click(function(event) {



		event.preventDefault();



		signup.resendOTP();



	});



});									   


//country and state //HH
if($("#custom_fields_country").val() == 1 || $("#custom_fields_country").val() == 2){	
	
$("#id_country").select2().on('change', function() {

		signup.getState(this.value);	

	});
	}
	
//country and state

//state and city  //HH
if($("#custom_fields_city").val() == 1 || $("#custom_fields_city").val() == 2){	
$("#id_state").select2().on('change', function() {

		signup.getCity(this.value);	

	}); 
}
//state and city  //

signup = {	



		validatePasswd : function() {



			if($.trim($("#firstname").val()) == '' || $.trim($("#mobile").val()) == '' ||  $.trim($("#email").val()) == '' ||   $.trim($("#passwd").val()) == '' || $.trim($("#confirm_password").val()) == '')



			{	



				alert('Please fill all the required fields');



				return false;



			}
		// Country, state, city mandatory//HH
			if($.trim($("#custom_fields_country").val()) == 2) 	
               {
                   
			if($.trim($("#id_country").val()) == '')
            	
            	{ 
                 
                 alert('please select the Country');

                    return false;
                   	}
               }
			
			
		 if($.trim($("#custom_fields_state").val()) == 2) 	
               {
                   
			if($.trim($("#id_state").val()) == '')
            	
            	{ 
                 
                 alert('please select the state');

                    return false;
                   	}
               }
              
               
               
        if($.trim($("#custom_fields_city").val()) == 2) 	
               {
                   
            if($.trim($("#id_city").val()) == '')
            	
            	{ 
                 
                 alert('please select the city');

                    return false;
                   	}
		        }


               // branch selection is  mandatory for new reg//HH
               if($.trim($("#is_branchwise_cus_reg").val()) == 1)
			   {
             if($.trim($("#id_branch").val()) == '')
            	
            	{ 
                 
                 alert('please select the branch');

                    return false;
                   	}
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



				alert('Password must be of minimum 8 characters.');



				return false;



			}



			if($.trim($('#passwd').val()) == $.trim($('#confirm_password').val()))



			{



				if($("#terms").is(":checked"))

				{
					
					 $("#spinner").css('display','block');



					 $("#generate_otp").attr('disabled','disabled');



					 $("#firstname").attr('readonly','true');



					 $("#mobile").attr('readonly','true');



					 $("#email").attr('readonly','true');
					 
					 $("#passwd").attr('readonly','true');
					 



					 $("#confirm_password").attr('readonly','true');



					 



					var mobile = $.trim($("#mobile").val());



					var email = $.trim($("#email").val());



					var name = $("#firstname").val();



					$.ajax({



					   url:baseURL+"index.php/user/generateOTP/"+mobile+"/0/"+email+"/"+name,



					   type : "POST",



					   success : function(result) {	

					  

					   $("#spinner").css('display','none');
					   


						  if(result == 1)



						  {

							 $('#otp_modal').modal({

								backdrop: 'static',

								keyboard: false

							 });

							 //$("#otp_modal #otp").focus();

							 setTimeout(signup.enableBtn, 60000); 

						  }



						  else if(result == 0)



						  {



							 $("#generate_otp").removeAttr("disabled");



							 $("#firstname").removeAttr("readonly");



							 $("#mobile").removeAttr("readonly");



							 $("#email").removeAttr("readonly");
							 

							 $("#passwd").removeAttr("readonly");



							 $("#confirm_password").removeAttr("readonly");
							 
							 



							 alert("Mobile number already exists.Please provide your number correctly...");



						  }



						  else if(result == 2)



						  {



							 $("#generate_otp").removeAttr("disabled");;



							 $("#firstname").removeAttr("readonly");



							 $("#mobile").removeAttr("readonly");



							 $("#email").removeAttr("readonly");
							 


							 $("#passwd").removeAttr("readonly");



							 $("#confirm_password").removeAttr("readonly");
							 
							 



							 alert("Not a valid mobile number...");



						  }



						  else



						  {



							 $("#generate_otp").attr('disabled','false');



			 				 $("#firstname").removeAttr("readonly");



							 $("#mobile").removeAttr("readonly");



							  $("#email").removeAttr("readonly");

							 $("#passwd").removeAttr("readonly");



							 $("#confirm_password").removeAttr("readonly");



							 alert("Error in registration. Please try again later...");



						  }



					   },



					   error : function(error){



						  $("#spinner").css('display','none');



						   console.log(error);



					   }



					});
				}
				else
					alert('Please accept terms and conditions');
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



					setTimeout(signup.enableBtn, 90000);



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



	},
	getCountry : function() {

			 $("#spinner").css('display','block');

			

			$.ajax({

				   url:baseURL+"index.php/user/get_country_list",

				   type : "GET",

				   dataType: "json",

				   success: function(country) {

		
		$.each(country, function (key, country) {

		   $("#spinner").css('display','none');

			$('#id_country').append(

				$("<option></option>")

				  .attr("value", country.id)

				  .text(country.name)

				  

			);

			

		});

		

		

			$("#id_country").select2("val", ($('#countryval').val()!=null?$('#countryval').val():''));

			var selectid=$('#countryval').val();

			if(selectid!=null && selectid > 0)

			{

				$('#id_country').val(selectid);

				$('.overlay').css('display','block');

				//register.getState(selectid);

			}

		$('.overlay').css('display','none');

		},

				   error : function(error){

					  $("#spinner").css('display','none');

				   }

				});

			},
		getState : function(id) {

			 $("#spinner").css('display','block');

			 $('#id_state option').remove();

			$.ajax({

				   url:baseURL+"index.php/user/get_state/"+id,

				   type : "POST",

				   dataType: "json",

				   success : function(state) {

				   		 $("#spinner").css('display','none');

				   $.each(state, function (key, state) {

						$('#id_state').append(

							$("<option></option>")

							  .attr("value", state.id)

							  .text(state.name)

						);

					});	

					$("#id_state").select2("val", ($('#stateval').val()!=null?$('#stateval').val():''));

					var selectid=$('#stateval').val();

					if(selectid!=null && selectid>0)

					{

					$('#id_state').val(selectid);

					  // register.getCity(selectid);

				    }

				

				  },

				   error : function(error){

					   	 $("#spinner").css('display','none');

				   }

				});

			},
			
				getCity : function(id) {    // Get city  //HH

				 	 $("#spinner").css('display','block');

				 	  $('#id_city option').remove();

			$.ajax({

				   url:baseURL+"index.php/user/get_city/"+id,

				   type : "POST",

				   dataType: "json",

				   success : function(city) {

				   	 $("#spinner").css('display','none');

					   $.each(city, function (key, city) {

		  

						$('#id_city').append(

							$("<option></option>")

							  .attr("value", city.id)

							  .text(city.name)

						);

					});

					$("#id_city").select2("val", ($('#cityval').val()!=null?$('#cityval').val():''));

					var selectid=$('#cityval').val();

					if(selectid!=null && selectid>0)

					{

						$('#id_city').val(selectid);

				    }

				  },

				  

				   error : function(error){

					   	 $("#spinner").css('display','none');

				   }

				});

			}



}
// Branch wise Cus Reg In User //HH
function get_branchname()
{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/user/get_branch/',
		dataType:'JSON',
		success:function(data){
		console.log(data);
		 var scheme_val =$('#id_branch').val();
		   $.each(data, function (key, item) {					  	
			  
			   		$('#branch_select').append(
						$("<option></option>")
						.attr("value", item.id_branch)						  
						  .text(item.name )
						  
					);
					
					$('#branch_select1').append(
						$("<option></option>")
						.attr("value", item.id_branch)						  
						  .text(item.name )
						  
					);			   				
				
			});
		  
		   
			
			$("#branch_select").select2({
			    placeholder: "Select branch name",
			    allowClear: true
		    });
			$("#branch_select1").select2({
			    placeholder: "Select branch name",
			    allowClear: true
		    });
				
			 $("#branch_select").select2("val",(scheme_val!='' && scheme_val>0?scheme_val:''));
			 $(".overlay").css("display", "none");	
		}
	});
}

$('#branch_select').select2().on("change", function(e) { 
	if(this.value!='')
	{   
		$("#id_branch").val(this.value);    
		var id_branch=$("#id_branch").val(); 
	}
	else
	{   
	$("#id_branch").val('');       
	}
});