$(document).ready(function() {    
    $("#custName").on('keypress',function(event){    
    var regex= new RegExp("^[a-zA-Z ]*$");    
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }    
    });
    $("#custMobile").keypress(function (e){
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) 
        {
            return false;
        }
    });
    $('#custMobile').on('blur onchange',function(){
        if(this.value.length != 10)
        {
            $(this).val('');
            $(this).attr('placeholder', 'Enter valid mobile number');
        }
    });  
	$( "#contact_submit" ).click(function( event ) {
		event.preventDefault();
		contact.validate_form();
	});
	$( "#refreshCaptcha" ).click(function(event) {
		event.preventDefault();
		contact.refreshCaptcha();
	});
/* -- Coded by ARVK -- */
var toggle=0;
$(".pull_feedback").click(function(){
           if(toggle==0){
		   	jQuery("#contact").animate({left:"0px"});
		   	toggle=1;
		   }else{
		   	contact.refreshCaptcha();
			jQuery("#contact").animate({left:"-282px"});
		   	toggle=0;
		   }
        });
/* --/ Coded by ARVK -- */
});	
contact = {	
		validate_form : function() {
			   document.getElementById("contact_submit").disabled=true;
			   if(contact.validate())
			   {
				   $.ajax({                                                
						   type: "POST",                                                           
						   url: baseURL+"index.php/user/contactSubmit",
						   data:"captchaAns="+$('#captchaAns').val()+ "&custName=" + $('#custName').val() + "&custMobile=" + $('#custMobile').val() + "&custMessage=" + $('#custMessage').val()+ "&reg=" + $('#reg').val(),
						   success: function(data){
						       console.log(data);
									   if(data==0)
									   {     
									       	$('.errText').fadeIn();
											 $("#captchaAns").val("");
											 $("#captchaAns").focus();	   
											 $('.errText').html("Enter valid captcha");
											 $('.errText').css("color","RED");
											 $('.errText').delay(10000).fadeOut(500); // fade updtd for alert ms//hh
									   }
									   else if(data==2)
									   {
										    $('.errText').html("Error in sending mail.Please try again later");
											$('.errText').css("color","RED");
									   }
									   else
									   {
											$("#custName").val("");
											$("#custMobile").val("");
											$("#custMessage").val("");
											$("#captchaAns").val("");
											jQuery("#contact").animate({left:"-282px"});toggle=0;	
											 alert("Thanks for contacting us...");
									   }
									      document.getElementById("contact_submit").disabled=false;
						   },
						   error: function(request,error) {
								   console.log(error);
								      document.getElementById("contact_submit").disabled=false;
						   }
				   });
			 }
			 else
			 {
				 document.getElementById("contact_submit").disabled=false;
			 }
		},
		validate : function() {
			if($.trim($("#custName").val()) == '' || $.trim($("#custMessage").val()) == '' || $.trim($("#custMobile").val()) == '' || $.trim($("#captchaAns").val()) == '')
			{   
			    $('.errText').fadeIn();
				$(".errText").html("Please fill all the fields");
				$('.errText').css("color","RED");
				$('.errText').delay(10000).fadeOut(500); // fade updtd for alert ms//hh
				return false;	
			}
			else
			{
				if($.trim($("#custMobile").val().length) == 10) 
				{
					return true;
				}
				else
				{
					$(".errText").html("Mobile number should be 10 digits in length");
					$('.errText').css("color","RED");
					$("#custMobile").val('');
					$("#custMobile").focus();
					return false;
				}
			}
		},
		refreshCaptcha : function() {
			var img = document.images['captcha_img'];
			img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
		}
}