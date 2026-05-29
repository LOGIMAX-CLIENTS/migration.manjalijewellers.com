$(document).ready(function() {


jQuery("#referral").animate({right:"-282px"});toggle=0;

	$("#ref_mbi").click(function( event ) {
	
		
	if($("#referral_value").val()!=''){
			
		     $("#ref_mbi").prop("disabled",true);		
		     $("#ref_mail").prop("disabled",true);
			 $("#referral_value").prop("disabled",true);
		     $("#referral_by").val(1);
	 		 var referral_value=$("#referral_value").val();
	 		 var referral_by=$("#referral_by").val();
			 var status= referral.validateMobile();				 
			 if(status){				 
				referral.validate_form(referral_value,referral_by);					
			   }
		}else{
			$(".errText").html("Please Enter the mobile number");
			$('.errText').prop("color","RED");
			$("#referral_value").val('');
		 }
		
		
	  });
	
$( "#ref_mail").click(function( event ) {

	if($("#referral_value").val()!='') 
	{
		$("#ref_mbi").prop("disabled",true);		
		$("#ref_mail").prop("disabled",true);
		$("#referral_value").prop("disabled",true);
		$("#referral_by").val(2);
		var referral_value=$("#referral_value").val();
	    var referral_by=$("#referral_by").val();
			 var status= referral.validateEmail();				 
			 if(status){
				referral.validate_form(referral_value,referral_by);			
			   }
		}else{
			$(".errText").html("Please Enter the mail id");
			$('.errText').prop("color","RED");
			
		}

	});
	

	/* -- Coded by ARVK -- */
var toggle=0;

$(".refpull_feedback").click(function(){
		   
           if(toggle==0){
		   	
		   	jQuery("#referral").animate({right:"0px"});
		   	toggle=1;
			$('.errText').html("");									
			$('.success_Text').html("");	
			$("#referral_by").val("");
			$("#referral_value").val("");
		   	
		   }else{
			jQuery("#referral").animate({right:"-282px"});
		   	toggle=0;
			$('.errText').html("");									
			$('.success_Text').html("");	
			$("#referral_by").val("");
			$("#referral_value").val("");

		   	
		   }
           
        });
/* --/ Coded by ARVK -- */

});	


referral = {	


		validate_form : function(referral_value,referral_by){

			   if((referral_value!='' && referral_by!=''))
			   {
				
				     $.ajax({
						 
						  type: "POST",	
						  url: baseURL+"index.php/user/referral_linkshare?nocache=" + my_Date.getUTCSeconds(),
						   data:{"referral_id":referral_value,"referral_by":referral_by},
						   success: function(data){
							   
									if(data==1)
									{
										$(".success_Text").html("Referral link share success");
										$('.success_Text').css("color","GREEN");
										$("#ref_mbi").prop("disabled",false);		
										$("#ref_mail").prop("disabled",false);
										$("#referral_value").prop("disabled",false);
										jQuery("#referral").animate({right:"-282px"});toggle=0;
									
									 }

									    


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


		validateMobile : function() {


			if($.trim($("#referral_value").val())!= '')
			
			{
				if($.trim($("#referral_value").val().length) == 10) 


				{
					return true;


				}


				else


				{


					$(".errText").html("Mobile number should be 10 digits in length");


					$('.errText').css("color","RED");


					$("#referral_value").val("");
					
					$("#referral_value").prop("disabled",false);
					
					$("#ref_mbi").prop("disabled",false);
					
					$("#ref_mail").prop("disabled",false);

					return false;


				}


			}


		},
		
		validateEmail : function() {




		 var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;



		 if ($("#referral_value").val().search(emailRegEx) == -1) {			    

				$(".errText").html("Please enter a valid email address");

				$('.errText').css("color","RED");
				
				$("#referral_value").val("");
				
			    $("#referral_value").prop("disabled",false);
			   
				$("#ref_mbi").prop("disabled",false);
				
				$("#ref_mail").prop("disabled",false);

				
				return false;

		 }else{
			 
			 return true;
		    }
		}
}

$("#passwd").on('change',function(event){
			event.preventDefault();
			var old_pwd=$('#old_passwd').val();
			var new_pwd=$('#passwd').val();
			if(old_pwd == new_pwd)
			{
				alert('Old password and New Password should not be same');
				$('#passwd').val('')
			}
});