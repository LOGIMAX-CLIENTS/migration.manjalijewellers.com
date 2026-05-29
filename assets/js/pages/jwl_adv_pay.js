$(document).ready(function() {
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
	
    $('#branch_select').select2().on("change", function(e) { 
    	if(this.value!='')
    	{	  
    		$("#id_branch").val(this.value);    
    		var id_branch=$("#id_branch").val(); 
    		get_rateByBranch(id_branch); 
    	}
    	else
    	{   
    		$("#id_branch").val('');       
    	}
    	
    	if(this.value==1)
    	{
    	    $("#wastage").css("display", "none");
    	}
    	else
    	{
    	    $("#wastage").css("display", "block");
    	}
    	
    });
	   
   $("#alter_mobile").on('blur onchange',function(event) {
        if(this.value != ''){
           if($.trim($("#alter_mobile").val()).length != 10)
    		{
    			$("#altermobErr").html('Not a valid mobile number');
    		}else{
				$("#altermobErr").html('');
			} 
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
   
    $("#email").on('blur onchange',function(event) {
	    if(this.value != ''){
            validateEmail($(this));	
	    }
    });
    
	
	$("#generate_otp").click(function(event) { 
		if(($("#jap_mobile").val()).length == 10 )
		{ 
			$("#err").html('');
			$("#spinner").css('display','block');
			$("#generate_otp").attr('disabled','disabled');
			$("#jap_mobile").attr('readonly','true');
			var mobile = $.trim($("#jap_mobile").val());
			$.ajax({
			   url:baseURL+"index.php/jwl_adv_pay/generateOTP/"+mobile,
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
					 $("#jap_mobile").removeAttr("readonly");
					 alert("Not a valid mobile number...");
				  }
				  else
				  {
					 $("#generate_otp").attr('disabled','false');
					 $("#jap_mobile").removeAttr("readonly");
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
	});
	
	$("#pay_now").on("click", function(event){	
		event.preventDefault(); 
		var offers  =  $('#offers').data('offerarr'); 
		if($("#a_terms").is(":checked") && $("#id_branch").val() != '' && $("#firstname").val() != '' && $("#jap_weight").val() >= 1 && $("#no_of_month").val() > 0 && $("#adv_percent").val() > 0 && $("#amount").val() > 0){
			$('#type').val(this.value);
			$('#JAP_Form').submit();
		}else{
			if( parseFloat($("#jap_weight").val())  < offers.min_offer_wgt){
			    $("#grmErr").css("color","red");
			}
			else if($("#no_of_month").val() == ''){
				alert('No. Of Months required.');
			}
			else if($("#adv_percent").val() == ''){
				alert('Advance % is required.');
			}
			else if($("#firstname").val() == ''){
				alert('Customer name is required.');
			}
			else if($("#id_branch").val() == ''){
				alert('Choose Delivery location.');
			}
			else if(!$("#a_terms").is(":checked")){
				alert('Please Accept Term & Conditions.');
			}
			else if($("#amount").val() == ''){
				alert('Invalid advance amount.');
			}else{
				alert($("#a_terms").is(":checked") && $("#id_branch").val() != '' && $("#firstname").val() != '' && $("#jap_weight").val() > offers.min_offer_wgt && $("#no_of_month").val() > 0 && $("#adv_percent").val() > 0 && $("#amount").val() > 0);
			}
		}
	});
	
	$("#adv_percent").on("change", function(){	
		calcAmount();
	});
	
	$("#no_of_month").on("change", function(){		
		setAdvPercent();
	});
	
	jQuery("#jap_weight").on("input", function(){
		var offers =  $('#offers').data('offerarr'); 
    	calcAmount();
	     if($('#id_branch').val()!='' && $('#id_branch').val()!=null)
	     {
    		if( parseFloat($("#jap_weight").val())  < 1){
    			$("#grmErr").html("Minimum weight 1g");
    			$('#pay_now').prop('disabled',true);
    		}
    		else if( parseFloat($("#jap_weight").val())  < offers.min_offer_wgt){
    			$("#grmErr").css("color","gray");
    			$('#pay_now').prop('disabled',false);
    			setAdvPercent();
    		}
    		else if( parseFloat($("#jap_weight").val())  >= offers.min_offer_wgt ){
    			$("#grmErr").css("color","gray");
    			$('#pay_now').prop('disabled',false);
    			setAdvPercent();
    		}
	    }else
	    {
	       $('#amount').val("");
	       $('#pay_now').html('Pay Now');
	       $('#jap_weight').val(offers.min_offer_wgt);
	       alert('Please Select The Delivery Location.');
	    }
	});
	
	if($("#id_branch").val() == ""){
		get_branchname();
	}else{
		calcAmount(); 
	} 
	
		
});

function setAdvPercent(){
    // SET ADVANCE OPTIONS
	var options 	= "";
	var weight 		=  parseFloat($("#jap_weight").val());  
	var no_of_month = parseFloat($("#no_of_month").val()); 
	var offers  	= $('#offers').data('offerarr'); 
	var i 			= 0;
	$.each(offers.data[no_of_month], function (key, item) {
		if( weight >=  item['min_wgt'] && weight <=  item['max_wgt'] ){
    		options += "<option value="+item['adv']+" "+(i == 0 ? 'selected="true"':'')+">"+item['adv']+"%</option>"; 
        	i++;	
		}
	});
	$("#adv_percent").html(options);
	calcAmount();
}

function calcAmount(){
	var offers 			=  $('#offers').data('offerarr'); 
	var no_of_month 	=  parseFloat($("#no_of_month").val()); 
	var adv_percent 	=  parseFloat($("#adv_percent").val()); 
	var mc_disc_percent =  0;
	var rate 			=  parseFloat($("#rate").val()); 
	var weight 			=  parseFloat($("#jap_weight").val());  
	var offer_name 		=  "";  
	
	if($("#id_branch").val() > 0 && weight >= 1){
		var purchase_amount	= (weight*rate).toFixed(2);
		var payment_amount	=  (purchase_amount*(adv_percent/100)).toFixed(2);
	    $.each(offers.data[no_of_month], function (key, item) { 
	        if(adv_percent == item['adv'] && ( weight >=  item['min_wgt'] && weight <=  item['max_wgt'] )){
	            offer_name = item['name'];
    			$.each(item['disc'], function (ky, value) {
					if(weight < 40){ // < 40 g
						mc_disc_percent = item.disc[0];
					}
					else if(weight < 80){ // < 80 g
						mc_disc_percent = item.disc[0];
					}
					else if(weight >= 80){ // < 80 g
						mc_disc_percent = item.disc[1];
					}
    			}) 
		    }	
		});		
		
		jQuery("#offer_name").val(offers.data[no_of_month].name);		
		if(mc_disc_percent > 0){
		    jQuery("#disp_mc_disc_percent").html("<b>DISCOUNT ON MC :</b> "+mc_disc_percent+"%");    
		}else{
		    jQuery("#disp_mc_disc_percent").html("");    
		}
		jQuery("#offer_name").val(offer_name);
		jQuery("#mc_disc_percent").val(mc_disc_percent);		
		jQuery("#purchase_amount").val(purchase_amount); // Actual amount of purchase weight 
		jQuery("#amount").val(payment_amount); // Advance Payment amount
		jQuery("#pay_now").html("Pay &#8377; "+payment_amount);
		if( payment_amount > 0 )
		{  
	       $("#amtErr").html("");
	       $('#pay_now').prop('disabled',false);
		}
	}else{
		jQuery("#purchase_amount").val(0); // Actual amount of purchase weight 
		jQuery("#amount").val(0); // Advance Payment amount
		jQuery("#pay_now").html("Pay Now");
	}
		
}	

function validateForm(){
	var proceed = true;
	if(($("#name").val()).length == 0 ){
		$("#nameErr").html('Name Required'); 
		proceed = false;
	}
	if(($("#branch_select").val()).length == 0 ){
		$("#mobErr").html('Branch Required'); 
		proceed = false;
	}
	if(($("#category").val()).length == 0 ){
		$("#catErr").html('Category Required'); 
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
	var mobile = $.trim($("#jap_mobile").val()); 
	$.ajax({
	   url:baseURL+"index.php/jwl_adv_pay/generateOTP/"+mobile,
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
	   url:baseURL+"index.php/jwl_adv_pay/verifyOTP",
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

function validateEmail(elementValue) {
	 var status = false;     
	 var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
	 if (elementValue.val().search(emailRegEx) == -1) {
		  elementValue.val("");
		  alert("Please enter a valid email address");
	 }
}

function get_branchname()
{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/jwl_adv_pay/get_branch/',
		dataType:'json',
		success:function(data){
		 var scheme_val =  $('#id_branch').val();
		var html='';
		   $.each(data, function (key, item) {
		  		var disabled = "";
		        if(item.active==0)
		        {
		            disabled = "disabled='disabled'";
		        }
				html = "<option value='"+item.id_branch+"' "+disabled+">"+item.name+"</option>";
				$('#branch_select').append(html);
				$('#branch_select1').append(
					$("<option></option>")
					.attr("value", item.id_branch)						  
					.text(item.name )
				);		   				
				
			});
			$("#branch_select").select2({
			    placeholder: "Select Delivery Location",
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

function get_rateByBranch(id_branch){
	$("#spinner").css('display','block');
	var _date = new Date();
	$.ajax({
		   url:baseURL+"index.php/jwl_adv_pay/metalrateByBranch?recach="+new Date().getTime()+"&_nocache="+new Date().getTime(),
		   type : "POST",
		   dataType: "json",
		   data: {'id_branch': id_branch},
		   cache: false,
		   success : function(result) {	 
		      $("#spinner").css('display','none'); 
		      $("#pay_now").attr('disabled',false); 
			  var data = result.metal_rates;
			  $("#branch_rate").html(data.goldrate_22ct);
			  $("#rate").val(data.goldrate_22ct);
			  //$("#today_gold_rate").html(data.goldrate_22ct);
			  //$("#discounted_per_gram").html(parseFloat(data.goldrate_22ct)-(parseFloat($('#discount_amt').val())));
			  $("#branch_rate_silver").html(data.silverrate_1gm);
			  $("#silver_1g").val(data.silverrate_1gm);
    		  calcAmount(); 
		   },
		   error : function(error){
			  $("#spinner").css('display','none');
			   console.log(error);
		   }
		});
}
