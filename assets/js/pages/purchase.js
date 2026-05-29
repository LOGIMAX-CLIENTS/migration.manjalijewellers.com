$(document).ready(function() {

    $('#amt').html(1000);
    $('#planA_amt').val(1000);
	$('#amount_select').on('change',function(){
	   if(this.value!='')
	   {
	        $('#amt').html(this.value);
	        $('#planA_amt').val(this.value);
	   }
	   else
	   {
	       $('#amt').html(1000);
	       $('#planA_amt').val(1000);
	   }
	});
	
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
 
	$("#generate_otp").click(function(event) {
		event.preventDefault();	
		signup.validateFields();
	});
	 
	
	jQuery("#amount").on("input", function(){ 
		if( parseFloat($("#amount").val())  < 500 ){
			$("#amtErr").html("Minimum 500");
		}
		else if( parseFloat($("#amount").val())  >= 500 ){
			$("#amtErr").html("");
		}
		var rate = parseFloat($("#rate").val()); 
		var gms = this.value/rate;
		jQuery("#weight").val(gms.toFixed(3));
	});
	
	jQuery("#weight").on("input", function(){ 
		if( parseFloat($("#weight").val())  < 0){
			$("#grmErr").html("Must be greater than 0");
		}
		else if( parseFloat($("#weight").val())  < 500 ){
			$("#grmErr").html("");
		}
		var rate = parseFloat($("#rate").val()); 
		var amt = this.value*rate;
		jQuery("#amount").val(amt.toFixed(2));
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
	
	$("#a_terms:checkbox").on("change",function(){
	    
	    if($("#a_terms").is(":checked"))
	    {
	         $('#terms').css('display','block');
	         $('#plana').css('display','block');
                $('html, body').animate({
                scrollTop: $("#plana").offset().top
                }, 1000);
	    }
	    else
	    {
	         $('#plana').css('display','none');
	    }
	    
	    if(!$("#w_terms").is(":checked") && !$("#a_terms").is(":checked"))
	   {
	       $('#terms').css('display','none');
	   }
	  
	});
	
	$("#w_terms:checkbox").on("change",function(){
	    
	    if($("#w_terms").is(":checked"))
	    {
	         $('#terms').css('display','block');
	         $('#planb').css('display','block');
                $('html, body').animate({
                scrollTop: $("#planb").offset().top
                }, 1000);
	    }
	    else
	    {
	         $('#planb').css('display','none');
	    }
	    
	   if(!$("#w_terms").is(":checked") && !$("#a_terms").is(":checked"))
	   {
	       $('#terms').css('display','none');
	   }
	  
	});
	
	$("#pay_by_amt,#buy_in_grams").click(function(event) { 
		event.preventDefault(); 
		if(this.id == 'pay_by_amt'){
			if($("#a_terms").is(":checked") && $("#firstname").val() != '' ){
				$('#type').val(this.value);	
				$('#purchaseForm').submit();
			}else{
				if(!$("#a_terms").is(":checked")){
					alert('Please Accept Term & Conditions.');
				}else{
					alert('Customer name is required.');
				}
				
			}
		}
		else if(this.id == 'buy_in_grams'){
			if($("#w_terms").is(":checked")){
				if(parseFloat($("#weight").val())  > 0 && parseFloat($("#amount").val())  > 0  && $("#firstname").val() != '' ){
					if( parseFloat($("#amount").val())  < 500 ){
						$("#amtErr").html("Minimum 500");
						return false;
					}else{
						$('#type').val(this.value);	
						$('#purchaseForm').submit();
					} 
				}else{  
					if( parseFloat($("#amount").val())  <= 0 || $("#amount").val()  == ''){ 
						$("#amtErr").html("Minimum 500"); 
					}
					if( parseFloat($("#weight").val())  <= 0 || $("#amount").val()  == ''){
						$("#grmErr").html("Must be greater than 0"); 
					}
					if($("#firstname").val() == ''  ){
						alert('Customer name is required.');
					}
				}
			}else{
				alert('Please Accept Term & Conditions.');
			}  
		} 
	}); 
	
	$('#resendOTP').click(function(event) {
		event.preventDefault();
		signup.resendOTP();
	});
});									   
 
//country and state
signup = {	
		validateFields : function() { 
			if($.trim($("#mobile").val()).length != 10)
			{
				alert('Not a valid mobile number');
				return false;
			}
			
			/*if($("#terms").is(":checked"))
			{*/	
			else {
				$("#spinner").css('display','block');
				$("#generate_otp").attr('disabled','disabled');  
				$("#mobile").attr('readonly','true');
				var mobile = $.trim($("#mobile").val()); 
				var name = '';
				
					$.ajax({
					   url:baseURL+"index.php/purchase/generateOTP/"+mobile+"/0/"+name,
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
							 setTimeout(signup.enableBtn, 30000); 
						  } 
						  else if(result == 2)
						  {
							 $("#generate_otp").removeAttr("disabled"); 
							 $("#mobile").removeAttr("readonly"); 
							 alert("Not a valid mobile number...");
						  }
						  else
						  {
							 $("#generate_otp").attr('disabled','false'); 
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
				/*else{
					alert('Please accept terms and conditions');
				}*/
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
				url: baseURL+"index.php/purchase/check_email",
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
		var name = '';
		$.ajax({
		   url:baseURL+"index.php/purchase/generateOTP/"+mobile+"/0/"+name,
		   type : "POST",
		   success : function(result) {	
		   $("#OTPloader").css("display","none");
				if(result == 1)
				{
					$("#resendOTP").css("display","none");
					setTimeout(signup.enableBtn, 30000);
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
				   url:baseURL+"index.php/purchase/get_country_list",
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
				   url:baseURL+"index.php/purchase/get_state/"+id,
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
			}
}
function get_branchname()
{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/purchase/get_branch/',
		dataType:'json',
		success:function(data){
		console.log(data);
		 var scheme_val =  $('#id_branch').val();
		
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

