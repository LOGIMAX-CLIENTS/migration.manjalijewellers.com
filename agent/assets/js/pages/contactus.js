$(document).ready(function() {    

    $("#cfName").on('keypress',function(event){    
    var regex= new RegExp("^[a-zA-Z ]*$");    
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }    
    });
    $("#cfMobile").keypress(function (e){
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) 
        {
            return false;
        }
    });
    $('#cfMobile').on('blur onchange',function(){
        if(this.value.length != 10)
        {
            $(this).val('');
            $(this).attr('placeholder', 'Enter valid mobile number');
        }
    });  

	$( "#cfrefreshCaptcha" ).click(function(event) {
		event.preventDefault();
		refreshCaptcha();
	});
	
	$( "#submit" ).click(function( event ) {
		
		//event.preventDefault();
		validate();
	});
	
/* -- Coded by ARVK -- */
var toggle=0;
$(".pull_feedback").click(function(){
           if(toggle==0){
		   	jQuery("#contact").animate({left:"0px"});
		   	toggle=1;
		   }else{
		   	refreshCaptcha();
			jQuery("#contact").animate({left:"-282px"});
		   	toggle=0;
		   }
        });
/* --/ Coded by ARVK -- */

		
 function validate(){
	 	
			if($.trim($("#cfName").val()) == '' || $.trim($("#cfMessage").val()) == '' || $.trim($("#cfMobile").val()) == '' || $.trim($("#cfcaptchaAns").val()) == '')
			{   
			    $('.errText').fadeIn();
				$(".errText").html("Please fill all the fields");
				$('.errText').css("color","RED");
				$('.errText').delay(10000).fadeOut(500); // fade updtd for alert ms//hh
				//return false;	
			}
			else
			{
				if($.trim($("#cfMobile").val().length) == 10) 
				{
					contact_form_validate();
				}
				else
				{
					$(".errText").html("Mobile number should be 10 digits in length");
					$('.errText').css("color","RED");
					$("#cfMobile").val('');
					$("#cfMobile").focus();
					//return false;
				}
			}
		}
		
	
function contact_form_validate(){
var cfcaptchaAns = $('#cfcaptchaAns').val();
var cfName = $('#cfName').val();
var cfMobile = $('#cfMobile').val();
var cfMessage = $('#cfMessage').val();
var cfreg = $('#cfreg').val();
				   $.ajax({                                                
						   type: "POST",                                                           
						   url: baseURL+"index.php/user/contactForm",
						   data: {'cfcaptchaAns':cfcaptchaAns,'cfName':cfName,'cfMobile':cfMobile,'cfMessage':cfMessage,'cfreg':cfreg},
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
											$("#cfName").val("");
											$("#cfMobile").val("");
											$("#cfMessage").val("");
											$("#cfcaptchaAns").val("");
											jQuery("#contact").animate({left:"-282px"});toggle=0;	
											 alert("Thanks for contacting us...");
									   }
									      document.getElementById("submit").disabled=false;
						   },
						   error: function(request,error) {
								   console.log(error);
								      document.getElementById("submit").disabled=false;
						   }
				   });
			
		}	
function refreshCaptcha() {
			var img = document.images['captcha_img'];
			img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
		}



 });