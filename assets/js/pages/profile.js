$(document).ready(function() {

		
		$('#pan_no').keyup(function(){
		 this.value=this.value.toUpperCase();
		});

	schemeData = {}; 

	schemes =  []; 

	$('#date_of_birth').datepicker({

    format: 'dd-mm-yyyy',

    startDate: '01/01/1900',

	endDate:"0d" ,

	 "autoclose": true

	})

	 .on('changeDate', function(ev){                 

	    $('#date_of_birth').datepicker('hide');

	});

	$('#date_of_wed').datepicker({

	    format: 'dd-mm-yyyy',

	    startDate: '01/01/1900',

		endDate:"0d" ,

		 "autoclose": true

	})

	 .on('changeDate', function(ev){                 

	    $('#date_of_wed').datepicker('hide');

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
   
	if($('#id_country').length>0)

	{

    	register.getCountry();

	}

	

	$("#id_country").select2({

					  placeholder: "Select Country"

					

	});	

	$("#id_state").select2({

				  placeholder: "Select State"

					});

	

	

	$("#id_city").select2({

				  placeholder: "Select City",

				  allowClear:true

	});	

	$("#proof_list").select2({

	

	});	

	

	$("#id_country").select2().on('change', function() {

		register.getState(this.value);	

	});

	

	$("#id_state").select2().on('change', function() {

		register.getCity(this.value);	

	});					

	

	$("#pan_proof").on('change',function() {

		register.validateImage(this);	

	});

	$("#cus_img").on('change',function() {

		register.validateImage(this);	

	});

	$("#voterid_proof").on('change',function() {

		register.validateImage(this);	

	});

	$("#rationcard_proof").on('change',function() {

		register.validateImage(this);	

	});

	$("#registerSubmit").on('click',function(event) {

		event.preventDefault();

		register.validate(this);	

	});

	

	//to remove uploaded image

	$("#customer").on('click',function(event) {

		event.preventDefault();

	  register.remove_img(this.id);

	});

	$("#pan").on('click',function(event) {

		event.preventDefault();

	  register.remove_img(this.id);

	});

	$("#rationcard").on('click',function(event) {

		event.preventDefault();

	  register.remove_img(this.id);

	});

	$("#voterid").on('click',function(event) {

		event.preventDefault();

	  register.remove_img(this.id);

	});

	

	//dynamic field add

	   $("#proof_list").change(function () {

		  var proof = $(this).val();

		  if (proof == 1 && proof <=3  ){

		  	$("#uploadArea").append('<b>Attach PanCard Proof</b><input class="span3" type="file" id="pan_proof" name="pan_proof" /><br/>');

		  	$("#pan_proof").on('change',function() {

				register.validateImage(this);	

			});

			$('option:selected', this).remove();

		  }

		  else if (proof == 2 && proof <=3  ){

		  	$("#uploadArea").append('<b>Attach VoterID Proof</b><input class="span3" type="file" id="voterid_proof" name="voterid_proof" /><br/>');

		  		$("#voterid_proof").on('change',function() {

				register.validateImage(this);	

			});

		     $('option:selected', this).remove();

		  }

		  else if (proof == 3 && proof <=3  ){

		  	$("#uploadArea").append('<b>Attach RationCard Proof</b><input type="file" class="span3" id="rationcard_proof" name="rationcard_proof" /><br/>');

		  	$("#rationcard_proof").on('change',function() {

				register.validateImage(this);	

			});

		    $('option:selected', this).remove();

		  }

		    

		});

			

});	
/* Referal link  send */		
$("#referallinksend").on('click',function(event) 
		{
		var mobile=null;	
		var email=null;	
		
		if($("#mobileno").val()>=10){
				mobile = $.trim($("#mobileno").val());
			}
			if($("#emailid").val()!='')
			{		
			  email=$.trim($("#emailid").val());
			}
			if(($("#mobileno").val()>=10 || email!=null)){								 				 
			$("#spinner").css('display','block');
			$("#mobileno").prop( "disabled",true);
			$("#emailid").prop( "disabled",true);
				$.ajax({				
			url:baseURL+"index.php/user/referral_linksend?nocache=" + my_Date.getUTCSeconds(),				   				 
			data: ((mobile!=''|| email!='')?{'mobile':mobile,'email':email}:''),				  
			type : "POST",	
			dataType: "json",
			success : function(data) {
				if(data.status == false){
					$("#spinner").css('display','none');	
				msg='<div class = "alert alert-danger"><a href = "#" class = "Failed " data-dismiss = "alert">&times;</a>Not allowed to send</div>';
				$("#mobileno").prop('disabled', false);
				$("#emailid").prop('disabled', false);
				$("#mobileno").val('');
				$("#emailid").val('');
				$('#error-msg').html(msg);
				}
				
				else if(data.status == true){
								
				$("#spinner").css('display','none');	
				msg='<div class = "alert alert-success"><a href = "#" class = "success " data-dismiss = "alert">&times;</a>Code Shared successfully</div>';
				$("#mobileno").prop('disabled', false);
				$("#emailid").prop('disabled', false);
				$("#mobileno").val('');
				$("#emailid").val('');
				$('#error-msg').html(msg);	
				}
				else{	
				
				msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please enter the Mobile number and Mail id</div>';			
				$('#error-msg').html(msg);	
				}			    
			}					   					   		
		});						
	   }else{	
			msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Please enter the Mobile number and Mail id</div>';				
		$('#error-msg').html(msg);			
		}
	});
/* Referal link  send */ 
							   

register = {	

		remove_img : function(id) {

			 $("#spinner").css('display','block');

			$.ajax({

				   url:baseURL+"index.php/user/remove_cus_img/"+id,

				   type : "POST",

				   success : function(result) {

				  

					   $("#spinner").css('display','none');

					   if(id=='customer')

					   $('#cusImg_preview').attr('src',baseURL+'admin/assets/img/default.png');

					   else if(id=='pan')

					   $('#panImg_preview').attr('src',baseURL+'admin/assets/img/no_image.png');

					   else if(id=='voterid')

					   $('#VIImg_preview').attr('src',baseURL+'admin/assets/img/no_image.png');

					   else

					   $('#RCImg_preview').attr('src',baseURL+'admin/assets/img/no_image.png');

					   $('#alert').html(result);

					   $("#alert").css("display","block");

				   },

				   error : function(error){

				   }

				});

			},

			

		getCountry : function() {

			 $("#spinner").css('display','block');

			

			$.ajax({

				   url:baseURL+"index.php/user/get_country",

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

			

			getCity : function(id) {

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

			},

			validateImage : function(){

				if(arguments[0].id == 'cus_img')

				{

					var preview = $('#cusImg_preview');

					var imgText = $('#cusImg_text');

				}

				else if(arguments[0].id == 'pan_proof')

				{

					var preview = $('#panImg_preview');

					var imgText = $('#pan_Img_text');

				}

				else if(arguments[0].id == 'voterid_proof')

				{

					var preview = $('#VIImg_preview');

					var imgText = $('#VI_Img_text');

				}

				else

				{

					var preview = $('#RCImg_preview');

					var imgText = $('#RC_Img_text');

				}

				if(arguments[0].files[0].size > 1048576)

				{

				  alert('File size cannot be greater than 1 MB');

				  arguments[0].value = "";

				  preview.css('display','none');

				  imgText.css('display','none');

				}

				else

				{

					var fileName =arguments[0].value;

					var ext = fileName.substring(fileName.lastIndexOf('.') + 1);

					ext = ext.toLowerCase();

					if(ext != "jpg" && ext != "png" && ext != "jpeg")

					{

						alert("Upload JPG or PNG Images only");

						arguments[0].value = "";

						preview.css('display','none');

						imgText.css('display','none');

					}

					else

					{

						var file    = arguments[0].files[0];

						var reader  = new FileReader();

						  reader.onloadend = function () {

							preview.prop('src',reader.result);

						  }					

						  if (file)

						  {

						 	reader.readAsDataURL(file);

							preview.css('display','');

							imgText.css('display','');

						  }

						  else

						  {

						  	preview.prop('src','');

							preview.css('display','');

							imgText.css('display','');

						  }

						  

  

					}

				}

			},

		validate : function(){

		  if($("#pan_no").val() != '')

		  {

			var regexp = /^[A-Z]{5}\d{4}[A-Z]{1}$/;

			if(!regexp.test($("#pan_no").val()))

			{

				 $("#pan_no").val("");

				 alert("Not a valid PAN No.");

				 $("#pan_no").focus();

				 return false;

			}

		  }

		 if(ISpanreq == '1' ){

				if($("#pan_no").val() != ''){

					return true;

				}

				else{

					 alert("Please enter pan number.");

					 return false;

				}

		  }	

		  if($("#pincode").val() != '')

		  {

			 var regexp = /^([0-9]{6})?$/;

			if(!regexp.test($("#pincode").val()))

			{

				 $("#pincode").val("");

				 alert("Not a valid Pincode.");

				 $("#pincode").focus();

				 return false;

			}

		  }

		  if($("#lastname").val() == '')

		  {

			 $("#lastname").focus();

			 	 return false;

		  }

		  

		  if($("#firstname").val() == '')

		  {

			 $("#firstname").focus();

			 	 return false;

		  }

		 

			  var elementValue = $("#email");

			  if(IDEmail != elementValue.val())

			  {

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

								if(data==0)

								{

									$("#registerForm").submit();

								}

								else

								{

									 document.getElementById('email').value="";

									 alert("E mail already exist.Please provide correct email Id...");

								}

							},

							error: function(request,error) {

								alert(error);

							}

						});

					 }

			   }

			   else

			   {

				   $("#registerForm").submit();

			   }

		}

	}