$(document).ready(function() {

	$('#success').css('display','none');
	if($('#ifsc').val()=='' && $('#bank_acc_no').val()=='' && $('#con_acc_no').val()==''){	
	//&& $('#bank_name').val()==''&& $('#bank_branch').val()==''		
		$('span[id^="bank_rmsat"]').remove();
			
	} 
	
	$('#success').delay(5000).fadeOut('slow');
	//hide error/success mgs

	//kyc status text color 
	($('#bank_color').val()=="Pending"? $('#bank_clrrm').css('color', 'blue'):
	($('#bank_color').val()=="In Progress"?$('#bank_clrrm').css('color', 'orange'):($('#bank_color').val()=="Verified"?$('#bank_clrrm').css('color', 'Green'):$('#bank_clrrm').css('color', 'red'))));

	($('#pan_color').val()=="Pending"? $('#pan_clrrm').css('color', 'blue'):
	($('#pan_color').val()=="In Progress"?$('#pan_clrrm').css('color', 'orange'):($('#pan_color').val()=="Verified"?$('#pan_clrrm').css('color', 'Green'):$('#pan_clrrm').css('color', 'red'))));

	($('#aadhar_color').val()=="Pending"? $('#aadhar_clrrm').css('color', 'blue'):
	($('#aadhar_color').val()=="In Progress"?$('#aadhar_clrrm').css('color', 'orange'):($('#aadhar_color').val()=="Verified"?$('#aadhar_clrrm').css('color', 'Green'):$('#aadhar_clrrm').css('color', 'red'))));
	
	($('#dl_color').val()=="Pending"? $('#dl_clrrm').css('color', 'blue'):
	($('#dl_color').val()=="In Progress"?$('#dl_clrrm').css('color', 'orange'):($('#dl_color').val()=="Verified"?$('#dl_clrrm').css('color', 'Green'):$('#dl_clrrm').css('color', 'red'))));
		
		
	if($('#pan_no').val()=='' && $('#pan_card_name').val()==''){
		$('#pan_rmsat').remove();
	}

	if($('#aadhar_cardname').val()=='' && $('#aadhar_number').val()=='' && $('#dob').val()==''){
		$('#a_rmsat').remove();
	}

});

$("#pan_no").on('change',function(e){

	var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;
	if(!regexp.test($("#pan_no").val()))
	{
		 $("#pan_no").val("");
		 alert("Not a valid PAN Number");
		 $("#pan_no").focus();
		 return false;        		
	}

});

function submit_kyc(form,form_type,type)
{
    
   // $('.overlayy').css('display','block');
	if(form_type == 1)
	{
		$('#success').css('display','none');
		 $(".overlayy").css("display","block");
	var ifsc         = $('#ifsc').val();
	var bank_acc_no  = $('#bank_acc_no').val();
	var con_acc_no   = $('#con_acc_no').val();
	var name   		 = $('#bank_name').val();
	var bank_branch  = $('#bank_branch').val();
	var bankid       = $('#submit_bnk_detail').val();
	
		if($('#ifsc').val().length != 11 ){
			alert('Enter valid IFSC code');
			 $(".overlayy").css("display","none");
		}
	
		else if(($.trim($('#bank_acc_no').val().length) != $.trim($('#con_acc_no').val().length)))
		{
			alert('Bank A/C number should be same');
			$(".overlayy").css("display","none");
		}else if(ifsc=='' || bankid=='' ){ // bank_name==''||
			alert('Fields should not be empty');
			$(".overlayy").css("display","none");
			
		}else if (!$('#bnk_chk').is(':checked')) {
		    alert('Check I agree to proceed further');
		    $(".overlayy").css("display","none");
		}
		else
		{
			var records      =  {'type':type,'form_type':form_type,'ifsc':ifsc,'bank_acc_no':bank_acc_no,'con_acc_no':con_acc_no,'acc_holder_name':name,'bank_name':bank_branch} //,'name':name,'bank_branch':bank_branch
		    $(".overlayy").css("display","block");
		 setTimeout(function(){ 
			$.ajax({
				url:baseURL+"index.php/user/kyc_details",
				type : "POST",
				data: records ,
				async:false,
				dataType: "json",
				success : function(data) {
					console.log(data);
					if(data.status==true){
						$('#success').css('display','block');
						jQuery('#success').addClass('alert alert-success').removeClass('alert alert-danger');
						$('#suc_mgs').text(data.msg);
						/* $("#tab_bank :input").attr("disabled", true); */
						
						$("#tab_bank").removeClass("active");
						$("#1").removeClass("active");
						$(".overlayy").css("display","none");
						$("#tab_pan").addClass("active");   
						$("#2").addClass("active");
						$("div.overlay").css("display", "block");
						$("#bank_btn").remove();
						if(data.kyc_status == 1 && data.approval_type == 'Auto')
						window.location.href= baseURL+"index.php/dashboard";
						 
					}else{
				//var msg = 'Error in updating the database.';
					$('#success').css('display','block');
					jQuery('#success').addClass('alert alert-danger').removeClass('alert alert-success');
					$('#suc_mgs').text(data.msg);
			        $(".overlayy").css("display","none");
					}
					
				}
			});
		  }, 1000);
		}
	}
	else if(form_type == 2)
	{
		$('#success').css('display','none');
	
	    
		if($('#pan_no').val()=="" || $('#pan_card_name').val()===''){
			alert('Fields should not be empty');
              $('.overlayy').css('display','none');
			
		}else if (!$('#pan_chk').is(':checked')) {
		    alert('Check I agree to proceed further');
		     $('.overlayy').css('display','none');
		   
		}else{
	
		var pannumber = $('#pan_no').val();
		var pancap = pannumber.toUpperCase();
	
		var records = {'type':type,'form_type':form_type,'pan_no':pancap,'pan_card_name':$('#pan_card_name').val()}
		
		$('.overlayy').css('display','block');
		
    setTimeout(function(){ 
            	$.ajax({
    			url:baseURL+"index.php/user/kyc_details",
    			type : "POST",
    			data: records ,
    			async:false,
    			dataType: "json",
    			success : function(data) {
    				console.log(data);
    				if(data.status==true){
    					$('#success').css('display','block');
    					
    					jQuery('#success').addClass('alert alert-success').removeClass('alert alert-danger');
    					$('#suc_mgs').text(data.msg);
    					//$("#tab_pan :input").attr("disabled", true);
    					
    					$('.overlayy').css('display','none');
    					
    					$("#tab_pan").removeClass("active");
    					$("#2").removeClass("active");
    					
    				
    					$("#tab_aadhar").addClass("active");
    					$("#3").addClass("active");
    					
    				   
    					$("#pan_btn").remove();
    					if(data.kyc_status == 1 && data.approval_type == 'Auto')
    					window.location.href= baseURL+"index.php/dashboard";
    		
    				}else{ 
    				   
    				    $('.overlayy').css('display','none');
    		           
    					jQuery('#success').addClass('alert alert-danger').removeClass('alert alert-success');
    					$('#success').css('display','block');
    				  
                
                	
    					$('#suc_mgs').text(data.msg);	
    				}
    		  }
    		});
    
    }, 1000);
		
	
	  }
		
	}
	else if(form_type == 4)
	{ 
		$('#success').css('display','none');
		
	    $(".overlayy").css("display", "block");
		
		if($('#dl_number').val().length < 15 || $('#dl_number').val().length> 16 ){
			alert('Enter valid driving licence number');
			$(".overlayy").css("display", "none");
		}
		
		else if($('#dl_number').val()=="" || $('#dl_dob').val()==""){
			alert('Fields should not be empty');
	    	$(".overlayy").css("display", "none");
		}
		else if (!$('#dl_chk').is(':checked')) {
		    alert('Check I agree to proceed further');
		    	$(".overlayy").css("display", "none");
		}
		  
		else{
			
			var dlnumber = $('#dl_number').val();
			var dlcap = dlnumber.toUpperCase();
			
		var records = {'type':type,'form_type':form_type,'dl_number':dlcap,'dob':$('#dl_dob').val()}
	
	setTimeout(function(){ 
		$.ajax({
			url:baseURL+"index.php/user/kyc_details",
			type : "POST",
			data: records ,
			async:false,
			dataType: "json",
			success : function(data) {
				
				if(data.status==true){
					$('#success').css('display','block');
					jQuery('#success').addClass('alert alert-success').removeClass('alert alert-danger');
					
					$("div.overlay").css("display", "none");
					$('#suc_mgs').text(data.msg);
					$('.overlayy').css('display','none');
					$("#tab_dl :input").attr("disabled", true);
					$("#dl_btn").remove();
					if(data.kyc_status == 1 && data.approval_type == 'Auto')
					window.location.href= baseURL+"index.php/dashboard";

				}else{ 
			//var msg = 'Error in updating the database.';
				$('#success').css('display','block');
				jQuery('#success').addClass('alert alert-danger').removeClass('alert alert-success');
				$('#suc_mgs').text(data.msg);
					$("div.overlay").css("display", "none");

				}
				
			}
			});
			
		}, 1000);
		}
	}
	else if(form_type == 3)
	{
	    
	    $(".overlayy").css("display", "block");
	    if($('#adhar_file').val()=="" || $('#aadhar_password').val()===''){
			alert('Fields should not be empty');
		 $(".overlayy").css("display", "none");
			
		}else{
		    var aadhar_number = $('#aadhar_number').val();
            var aadhar_cardname = $('#aadhar_cardname').val();
            var file = $('#file').val();
            var dob = $('#dob').val();
            
        if(aadhar_number=="" || aadhar_cardname=='' || dob==''){
			alert('Please fill all the mandatory fields');
              $('.overlayy').css('display','none');
        }else if (!$('#adhar_chk').is(':checked')) {
		    alert('Check I agree to proceed further');
		     $('.overlayy').css('display','none');
		}else{   
            
             $(".overlayy").css("display", "block");
            var records={'form_type':form_type,'aadhar_number':aadhar_number,'aadhar_cardname':aadhar_cardname,'dob':dob,'type':type,'file':file,'password':$('#aadhar_password').val()}
        setTimeout(function(){ 
        	$.ajax({
    			url:baseURL+"index.php/user/kyc_details",
    			type : "POST",
    			data: records ,
    			async:false,
    			dataType: "json",
    			success : function(data) {
    				if(data.status==true){
        		        $('#success').css('display','block');
        				jQuery('#success').addClass('alert alert-success').removeClass('alert alert-danger');
        				
        				$("div.overlay").css("display", "none");
        				$('#suc_mgs').text(data.msg);
        			    $(".overlayy").css("display", "none");
        				$("#tab_aadhar :input").attr("disabled", true);
        				$("#aadhar_btn").remove();
        				if(data.kyc_status == 1 && data.approval_type == 'Auto')
        				window.location.href= baseURL+"index.php/dashboard";
    				}else{  
        				$('#success').css('display','block');
        				jQuery('#success').addClass('alert alert-danger').removeClass('alert alert-success');
        				$('#suc_mgs').text(data.msg);
    				}
    			}
    	    });
        }, 1000);
        
		}
		}
        
	}
	
	
}

   function validateFile()
    {
             var fileName =arguments[0].value;

			var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

			ext = ext.toLowerCase();
			
			if(ext != "pdf")
			{
			    alert('Upload only PDF');
			}
			else
			{
			    var file    = arguments[0].files[0];

				var reader  = new FileReader();

				  reader.onloadend = function (e) {
				      
				      var binaryData = e.target.result;
                    
                      var base64String = window.btoa(binaryData);
                      
                      console.log(base64String);

				  }	

				  if (file)

				  {

				 	reader.readAsDataURL(file);

				  }
				  var file=$('file').val(file);
				 
                 
    }
    
    }

    $('#pdf').on('change',function(event){
        event.preventDefault();
        var file = event.target.files[0]; 
        var ext = file.name.substring(file.name.lastIndexOf('.') + 1);
		ext = ext.toLowerCase();
			if(ext == "pdf")
			{
			    var reader = new FileReader();
                reader.onload = (function(theFile) {
                return function(e) {
                var result = e.target.result;
                var base64 = window.btoa(result);
                $('#file').val(base64);
                };
                })(file);
                reader.readAsBinaryString(file);
			}
			else
			{
			    alert('Upload only PDF');
			    $('#pdf').val('');
			}
       
    });
    
