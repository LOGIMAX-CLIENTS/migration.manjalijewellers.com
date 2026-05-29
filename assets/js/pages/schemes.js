var path =  url_params();
var ctrl_page = path.route.split('/');
$(document).ready(function() {
	
	// Metal Tab
	$(document).on('click', '.metal_btn', function(){
    	$(".metal_btn").removeClass("theme-btn-bg");
    	$(this).addClass("theme-btn-bg");
    	$(".classify_tab").css("display","none");
    	$(".classify_tab"+this.value).css("display","revert");
    	$(".tab-pane").removeClass("active");
    	$("#sch_clsfy_tabs li").removeClass("active");
    	$(".classify_tab"+this.value).eq(0).addClass("active");
    	var activate_tab_content = $(".classify_tab"+this.value+" a").eq(0).attr("href");
    	$(activate_tab_content).addClass("active");
    })
    
    // Branch Tab
	$(document).on('click', '.branch_btn', function(){
    	$(".branch_btn").removeClass("theme-btn-bg");
    	$(this).addClass("theme-btn-bg");
    	$(".classify_tab").css("display","none");
    	$(".classify_tab"+this.value).css("display","revert");
    	$(".tab-pane").removeClass("active");
    	$("#sch_clsfy_tabs li").removeClass("active");
    	$(".classify_tab"+this.value).eq(0).addClass("active");
    	var activate_tab_content = $(".classify_tab"+this.value+" a").eq(0).attr("href");
    	$(activate_tab_content).addClass("active");
    })
    
	schemeData = {}; 
	schemes =  []; 
	schCodes = [];
	$("#schemeType").val(0);
	scheme.getSchemeDts(0);
	$('#from_date').datepicker({ 
       startDate: '-4y',
       endDate: '0d'
    });
    $('#to_date').datepicker({ 
       startDate: '-4y',
       endDate: '0d'
    });
	//exit scheme reg
	get_branchname();
 // get_cusname();
	$('#exis_pan_no').css('display','none');
	$('#pan').css('display','none');
	//exit scheme reg
	//get_schemename();
    //exit scheme reg 
	$("#schemeType").change(function() {
		$("#schemeTable").empty();
		scheme.getSchemeDts($(this).val());
	});
	//GG
	if(ctrl_page[1]=='ratehistory'){
		submit_ratehis();
			}
//GG
	 $("#account_name").on('keypress', function (event) {
          var regex = new RegExp("^[a-zA-Z ]*$");
          var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
          if (!regex.test(key)) {
             event.preventDefault();
             return false;
          }
       });
       $("#account_name").bind("cut copy paste",function(e) {
            e.preventDefault();
       });
	// exit scheme reg
	$('#reg_sub').click(function(event) {
		if($('#branch_set').val()==1){
		if($("#group_name").val().length > 0 && $("#scheme_acc_number").val().length > 0 && $("#branch_select").val().length > 0)
		{
			event.preventDefault();
			gen_OTP($("#branch_select").val());
		}
		}else {
		if($("#group_name").val().length > 0 && $("#scheme_acc_number").val().length > 0)
		{
			event.preventDefault();
			gen_OTP();
		}	
		}		
	});
	// exit scheme reg
	$('#reg_sub__bymobile').click(function(event) {
		if($('#branch_set').val()==1){
		if($("#scheme_mob_number").val().length > 0 && $("#branch_select1").val().length > 0)
		{
			event.preventDefault();
			gen_mobilereg_OTP($("#branch_select1").val());
		}
		}else {
			if($("#scheme_mob_number").val().length > 0)
			{
				event.preventDefault();
				gen_mobilereg_OTP();
			}	
		}
	});
	// exit scheme reg
	/* $('#reg_sub').click(function(event) {
		if($("#group_name").val().length > 0 && $("#scheme_acc_number").val().length > 0 ){
			event.preventDefault();
			gen_OTP();
		}
	}); */
	/* $('#reg_sub__bymobile').click(function(event) {
		if($("#scheme_mob_number").val().length > 0){
			event.preventDefault();
			gen_mobilereg_OTP();
		}
	}); */
	/*$('#register_sub').click(function(event) {
		 var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;
		if($("#id_scheme").val().length > 0  && $("#scheme_acc_number").val().length > 0 &&  $("#account_name").val().length >0)
		{
			var  branch_select= (typeof $('#branch_select').val() == 'undefined' ? '' :$('#branch_select').val());	
			if($('#branch_set').val()==1 && $('#branch_select').val()>0)
			{
			 $("#scheme_acc_number").attr('readonly','true');
			 $("#account_name").attr('readonly','true');		 
			 $("#register_sub").prop('disabled',true);
			 $("#branch_select").prop('disabled',true);
			 $("#secheme_select").prop('disabled',true);
			 if($('#regExistingReqOtp').val() == 1){
			 	gen_OTP();
			 }else{
			 	$("#join_existing").submit();
			 }
			}else if($('#branch_set').val()==0 && branch_select=='') {
			 $("#scheme_acc_number").attr('readonly','true');
			 $("#account_name").attr('readonly','true');		 
			 $("#register_sub").prop('disabled',true);			 
			 $("#secheme_select").prop('disabled',true);
			 if($('#regExistingReqOtp').val() == 1)
			 {
			 	gen_OTP();
			 }
			            if($('#exis_pan_no').val() != ''){
			                if(!regexp.test($("#exis_pan_no").val()))
			            	{
			            		 $("#exis_pan_no").val("");
			            		 alert("Not a valid PAN No.");
			            		 $("#pan_no").focus();
			            		 return false;
			            	}else{
			            	    $('#join_existing').submit();
			            	}
			            }else{
			                $('#join_existing').submit();
			            }
			}else{
				alert('Please fill out this feild');
			}			 
		}else{
			alert('Please fill out this feild');
		}
	});*/
	//existing reg sub//
$('#register_sub').click(function(event) {
		if($("#id_scheme").val().length > 0  && $("#scheme_acc_number").val().length > 0 &&  $("#account_name").val().length >0 )
		{
			var  branch_select= (typeof $('#branch_select').val() == 'undefined' ? '' :$('#branch_select').val());	
			if(($('#branch_set').val()==1 && $('#branch_select').val()>0)||$('#is_branchwise_cus_reg').val()==1)
			{
			 $("#scheme_acc_number").attr('readonly','true');
			 $("#account_name").attr('readonly','true');		 
			 $("#register_sub").prop('disabled',true);
			 $("#branch_select").prop('disabled',true);
			 $("#secheme_select").prop('disabled',true);
			 if($('#regExistingReqOtp').val() == 1){
			 	gen_OTP();
			 }else{
			 	if($("#exis_pan_no").prop('required') == true){
					if($("#exis_pan_no").val() == ""){
						 $("#scheme_acc_number").attr('readonly',false);
						 $("#account_name").attr('readonly',false);		 
						 $("#register_sub").prop('disabled',false);
						 $("#branch_select").prop('disabled',false);
						 $("#secheme_select").prop('disabled',false);
						alert("PAN No. is required");
					}else{
						verify_pan();
						//$("#join_existing").submit();
					}
				}else{
					$("#join_existing").submit();
				}
			 }
			}else if($('#branch_set').val()==0 && branch_select=='') {
    			 $("#scheme_acc_number").attr('readonly','true');
    			 $("#account_name").attr('readonly','true');		 
    			 $("#register_sub").prop('disabled',true);			 
    			 $("#secheme_select").prop('disabled',true);
    			 if($('#regExistingReqOtp').val() == 1){
    			 	gen_OTP();
    			 }else{
    			 	if($("#exis_pan_no").prop('required') == true){
    					if($("#exis_pan_no").val() == ""){
    						$("#scheme_acc_number").attr('readonly',false);
    						 $("#account_name").attr('readonly',false);		 
    						 $("#register_sub").prop('disabled',false);
    						 $("#branch_select").prop('disabled',false);
    						 $("#secheme_select").prop('disabled',false);
    						alert("PAN No. is required");
    					}else{
    						verify_pan();
						    //$("#join_existing").submit();
    					}
    				}else{
    					$("#join_existing").submit();
    				}
    			 }
		    }else{
				alert('Please fill all fields.');
			}			 
		}else
		{
			alert('Please fill all fields.. ');
		}
	});
//existing reg sub//
	$(' #schemeJoin_modal').on('shown.bs.modal',function(){
			$("#acc_name").on('keypress', function (event) {
			    var theEvent = event || window.event;
				 var tab= theEvent.keyCode || theEvent.which;
				 if (tab === 9 ) { //TAB was pressed
					return true;
				 }
				  var regex = new RegExp("^[a-zA-Z _ \r\s]+$");
				  var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				  if (!regex.test(key)) {
					 event.preventDefault();
					 alert('Account name must contain alphabets only')
				  }
			   });
	});
	$('#schemeJoin_modal #confirm').click(function(event) {
		event.preventDefault();	
		isValidAcName = false;	
		isValidPan = true;	
		isValidRefCode = false;
		validAmt=true;
		var min_amount=parseInt($("#min_amount").val());
		var max_amount=parseInt($("#max_amount").val());
		var pay_amt=parseInt($("#pay_amt").val());
		var flx_denomintion=parseInt($("#flx_denomintion").val());
		var get_amt_in_schjoin=parseInt($("#get_amt_in_schjoin").val());
		var scheme_type=parseInt($("#scheme_type").val());
		var flexible_sch_type=parseInt($("#flexible_sch_type").val());
		var one_time_premium=parseInt($("#oneTime_premium").val());
		var is_enquiry = parseInt($("#is_Enquiry").val());
        if(is_enquiry==0)
		{
			if($("#acc_name").val() != "")
			{ 
				isValidAcName = true;	
			}
			else{
				alert('Please fill account name');	
				isValidAcName = false;		
			}
			if(flx_denomintion!='' && get_amt_in_schjoin==1 && scheme_type==3 && flexible_sch_type!=3)
			{
				if((pay_amt>=min_amount) && (pay_amt<=max_amount))
				{
					//alert(pay_amt%flx_denomintion);
					validAmt=true;
					if((pay_amt%flx_denomintion)!=0)
					{
						validAmt=false;
						alert('Please Enter a Amount in Multiples of '+flx_denomintion+'');
					}else
					{
						validAmt=true;
					}
				}
				else
				{
					alert('Your Amount Shold be Minimum'+min_amount+' and Maximum '+max_amount+'');
					validAmt=false;
					$("#pay_amt").val('');
				}
			}
			if($("#pan_no").val() != undefined){
				if($("#pan_no").val() != ""){
				var regexp = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
				if(!regexp.test($("#pan_no").val()))
				{
					$("#pan_no").val("");
					alert("Not a valid PAN No.");
					$("#pan_no").focus();
					isValidPan = false;	
				}
				}else{
					alert("Enter valid PAN No.");
					isValidPan = false;
				}
			}
			if($("#referal_code").val() != "" && $("#referal_code").val() != null)
			{
				//checkreferalcode($("#referal_code").val());
				$('.overlay').css('display','block');
				$.ajax({
					type: 'POST',
					data:{'referal_code':$("#referal_code").val()},
					url:  baseURL+'index.php/chitscheme/referralcode_check',
					dataType: 'json',
					success: function(data) {
						if(data.status==1){
							isValidRefCode = true;	
							if(isValidRefCode && isValidPan && isValidAcName && validAmt){
								$("#schemeForm").submit(); 
							}
						}
						else
						{     
							alert(data.msg);
							$('#referal_code').val('');
							$('#referal_code').attr("placeholder",data.msg);	
							isValidRefCode = false;	
						}	
					},error:function(error)  
					{
						isValidRefCode = false;	
					} 
				});
			}else{
				isValidRefCode = true;
				if(isValidRefCode && isValidPan && isValidAcName && validAmt){
					$('#schemeJoin_modal #confirm').attr('disabled', 'disabled');
					if($("#pan_no").val() != undefined){
    					if(($("#pan_no").val()).length == 10){
    					    verify_pan();
    					}else{
    					    $("#schemeForm").submit();
    					}
					}else{
					    $("#schemeForm").submit();
					}
				}	
			} 
		}
		else
		{
			$interseted_amount=true;
			$interseted_weight=true;
			if($('#interseted_amount').val()!=undefined)
			{
				if($('#interseted_amount').val()=='' || ($('#interseted_amount').val().length>=8))
				{
					$interseted_amount=false;
					alert('Please Enter a Valid Amount');
				}else{
					$interseted_amount=true;
				}
			}
			if($('#interseted_weight').val()!=undefined)
			{
				if($('#interseted_weight').val()!='' && ($('#interseted_weight').val().length<=5))
				{
					$interseted_weight=true;
				}else{
					$interseted_weight=false;
					alert('Please Enter a Vaild Weight');
				}
			}
			if($interseted_amount==true && $interseted_weight==true)
			{
				$('#schemeJoin_modal #confirm').attr('disabled', 'disabled');
				$("#schemeForm").submit(); 
			}
		}
	});
	$("#schemes").select2({ 
		placeholder:'' 
	});	
	$("#chit_number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
	 $('#existing_tab').click(function(ev)
	 {
	 	ev.preventDefault();
	 	$('chit_number').val('');
	 	$('account_name').val('');
	 });
});
function gen_OTP()
{
		 var chit = $("#join_existing").serialize();
		$.ajax({
		   url:baseURL+"index.php/chitscheme/join_existing_byacc",
		   data:chit,
		   type : "POST",
		   dataType: "json",
		   success : function(result) {
		// $("#spinner").css('display','none');
		 	 if(result.success)
			  {
				 $('#otp_modal').modal({
					backdrop: 'static',
					keyboard: false
				 });
				 //$("#otp_modal #otp").focus();
				 setTimeout(enableBtn, 60000); 
				//  alert(result.success);
			  }
			  else
			  {
				 $("#scheme_acc_number").attr('readonly',false);
				 $("#account_name").attr('readonly',false);		 
				 $("#register_sub").prop('disabled',false);			 
				 $("#secheme_select").prop('disabled',false);
				 $("#branch_select").prop('disabled',false);
				 alert(result.message);
			  }
		   },
		   error : function(error){
			  $("#spinner").css('display','none');
			   console.log(error);
		   }
		});
}
function gen_mobilereg_OTP(id_branch=""){
		var scheme_mob_number = $.trim($("#scheme_mob_number").val());
		$("#scheme_mob_number").attr("readonly");
		$.ajax({
		   url:baseURL+"index.php/chitscheme/join_existing_bymob/"+scheme_mob_number,
		   type : "POST",
		   dataType: "json",
		   data:{'id_branch':id_branch},
		   success : function(result) {
		   $("#spinner").css('display','none');
		 	 if(result.success)
			  {
				 $('#otp_mob_modal').modal({
					backdrop: 'static',
					keyboard: false
				 });
				 setTimeout(enableBtn, 60000); 
			  }
			  else
			  {
				 $("#scheme_mob_number").removeAttr("readonly");
				 alert(result.message);
			  }
		   },
		   error : function(error){
			  $("#spinner").css('display','none');
			   console.log(error);
		   }
		});
}
function enableBtn() {
		$("#resendOTP").css("pointer-events", "auto");
		$("#resendOTP").css("display","inline-block");
	}
function schemeDetails(){
	var SelectedId = arguments[0];
		$.each(schemes,function(index,value) {
				if(value.id_scheme == SelectedId)
				{
					if(value.scheme_type == 0)
					{
						var totalAmt = parseFloat(value.amount)*parseFloat(value.tot_ins);
						var row = $("<p class='schemeName'>"+value.name+"</p><table id='scheme-amount' class='table table-bordered table-responsive'><thead><tr><th>Monthly Installment</th><th>Total Amount Payable</th><th>Incentive</th><th>Value of Gold Jewellery</th></tr></thead><tbody><tr><td>"+value.amount+"</td><td>"+parseFloat(totalAmt).toFixed(2)+"</td><td>"+value.incentive+"</td><td>"+parseFloat(parseFloat(value.incentive)+parseFloat(totalAmt)).toFixed(2)+"</td></tr></tbody></table><div><div class='heading'>Description:</div><div class='description'>"+value.description+"</div></div>");
						$("#scheme_modal .modal-body").empty().append(row);
						$('#scheme_modal').modal('show');
					}
					else
					{
						var row = $("<p class='schemeName'>"+value.scheme_name+"</p><div><div class='heading'>Description:</div><div class='description'>"+value.description+"</div></div>");
						$("#scheme_modal .modal-body").empty().append(row);
						$('#scheme_modal').modal('show');
					}
					return false;
				}
			});
	}
function schemeTermsModal(id)
{
	$(".overlayy").css("display","block");
	$.ajax({
			url:baseURL+"index.php/chitscheme/get_classification/"+id+"?nocache=" + my_Date.getUTCSeconds(),
				   type : "GET",
				   async:false,
				   dataType: "json",
				   success : function(result) {
				   		$(".overlayy").css("display","none");
						var modal_desc = $("<p class='schemeName theme-txt'>"+result.classification_name+"</p><div><div style='font-size: 13px;'>"+result.description+"</div></div>");
						$("#terms_modal .modal-body").empty().append(modal_desc);
						$('#terms_modal').modal('show');
					}
			});
	}
/*function confirm_scheme(schemeId,schemeName,companyName,referal_code,newSchjoinonline,isReferal,is_pan_required,e)
{
	e.preventDefault();
	$('input[name*="schemeID"]').remove();
	$('.modal-footer').show();
	var selected_value = schemeId;
	 var isReferal=isReferal;
	var input = $("<input>").attr("type", "hidden").attr("name", "schemeID").val(selected_value);
	$('#schemeForm').append($(input));
	if(newSchjoinonline == 1)
	{
	// branch_name list
	if($('#branch_set').val()==1)
	{
		get_branch();
		if(isReferal==1 && is_pan_required==1)
		{	
		var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p><p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' class='form-control width50'></select></p><p><p>PAN No"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;'  name='pan_no' id='pan_no' placeholder='PAN No' required/></p></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
		}
		else if(isReferal==0 && is_pan_required==1)
		{
				var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' class='form-control width50'></select></p><p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='pan_no' id='pan_no' style='text-transform: uppercase !important;'  placeholder='PAN NO' required/></p></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
		}
		else if(isReferal==1 && is_pan_required==0)
		{
				var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' class='form-control width50'></select></p><p><p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
		}
		else
		{
		var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
		}
	}	
	else
	{
		 var referal_code=(referal_code!=''?referal_code:'');
		 if(referal_code!='null' && is_pan_required==1)
		 {
		var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p><p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p><input type='hidden' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code' value="+referal_code+" /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
		 }
		 else
		 {
		if(isReferal== 1&& is_pan_required==1)
			{
				var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p><p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='pan_no' style='text-transform: uppercase !important;' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
			}
			else if(isReferal== 0 && is_pan_required==1	)
			{
			var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required='true'/></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='pan_no' id='pan_no' style='text-transform: uppercase !important;' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
			}
			else if(isReferal== 1 && is_pan_required==0	)
			{
				var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p><p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
			}	
			else
			{
			var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
			}
		 }
	  }
	}else{		
		var confirmText = "<p><center> Kindly visit our showroom for new scheme enrollment....<center></p>";
		$('.modal-footer').hide();
	  }	
		$('#schemeJoin_modal .modal-body').html(confirmText);
		$('#schemeJoin_modal').modal('show');
}*/
function confirm_scheme(schemeId,id_metal,schemeName,companyName,newSchjoinonline,isReferal,is_pan_required,cus_single,emp_single,cus_ref_code,emp_ref_code,is_branchwise_cus_reg,cusName_edit,allow_join,get_amt_in_schjoin,min_amount,max_amount,flx_denomintion,scheme_type,flexible_sch_type,one_time_premium,is_multi_commodity,askbranch,id_branch,is_enquiry,rate_fix_by,rate_select,otp_price_fixing,otp_price_fix_type,goldrate_22ct,agent_refferal,e){
	e.preventDefault();
	$('input[name*="schemeID"]').remove();
/*	$('input[name*="min_amount"]').remove();
	$('input[name*="max_amount"]').remove();
	$('#flx_denomintion').remove();*/
	$('.modal-footer').show();
	console.log(agent_refferal);
	var agent_refferal = agent_refferal;
	var selected_value = schemeId;
	 var isReferal=isReferal;
	 var cus_single=cus_single;
	 var emp_single=emp_single;
	 var cus_ref_code=cus_ref_code;
	 var emp_ref_code=emp_ref_code;
	 var is_branchwise_cus_reg=is_branchwise_cus_reg;
	 var schjoin_amt=parseInt(get_amt_in_schjoin);
	 var sch_type=parseInt(scheme_type);
	 var flx_sch_type=parseInt(flexible_sch_type);
	 var one_time_premium=parseInt(one_time_premium);
	 var is_multi_commodity=is_multi_commodity;
	  var askbranch=askbranch;
	 var cusName_edit=cusName_edit;
	  var id_branch=id_branch;
	 var id_metal=id_metal;
	 var otp_price_fixing = otp_price_fixing;
	 var otp_price_fix_type = otp_price_fix_type;
	var input = $("<input>").attr("type", "hidden").attr("name", "schemeID").val(selected_value);
	var min_amount = $("<input>").attr("type", "hidden").attr("id", "min_amount").val(min_amount);
	var max_amount = $("<input>").attr("type", "hidden").attr("id", "max_amount").val(max_amount);
	var flx_denomintion = $("<input>").attr("type", "hidden").attr("id", "flx_denomintion").val(flx_denomintion);
	var get_amt_in_schjoin = $("<input>").attr("type", "hidden").attr("id", "get_amt_in_schjoin").val(get_amt_in_schjoin);
	var scheme_type = $("<input>").attr("type", "hidden").attr("id", "scheme_type").val(scheme_type);
	var flexible_sch_type = $("<input>").attr("type", "hidden").attr("id", "flexible_sch_type").val(flexible_sch_type);
	var oneTime_premium = $("<input>").attr("type", "hidden").attr("id", "oneTime_premium").val(one_time_premium);
	var isEnquiry = $("<input>").attr("type", "hidden").attr("id", "is_Enquiry").val(parseInt(is_enquiry));
	//var isRateFixbyjoin = $("<input>").attr("type", "hidden").attr("id", "isRateFixbyjoin").val(parseInt(rate_fix_by));
   // var goldrate = $("<input>").attr("type", "hidden").attr("id", "goldrate").val(parseInt(goldrate_22ct));

	$('#schemeForm').append($(input));
	$('#schemeForm').append($(min_amount));
	$('#schemeForm').append($(max_amount));
	$('#schemeForm').append($(flx_denomintion));
	$('#schemeForm').append($(get_amt_in_schjoin));
	$('#schemeForm').append($(scheme_type));
	$('#schemeForm').append($(flexible_sch_type));
	$('#schemeForm').append($(oneTime_premium));
	$('#schemeForm').append($(isEnquiry));
	if(newSchjoinonline == 1)
	{
	 // if(flx_sch_type!=1 && flx_sch_type!=4 )
	 console.log(cusName_edit);
	  if(is_enquiry == 0 && cusName_edit != 1 )
	  {
	    if(schjoin_amt == 0 || sch_type!=3 || flx_sch_type==3)  //Getting Payment Amount From Customer
        {
        if(($('#branch_set').val()==1 && askbranch==1 && is_branchwise_cus_reg!=1 )) //Disable Branch When Branchwise customer registration is Enabled
        {
            get_branch();
          //  get_cusname();   //cus name by default in acc_name while join sch//HH
          if(agent_refferal == 1)
          {
              var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"<p>AGENT CODE"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='Agent Referal Number' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
               
          }else
                {
                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                }
           if(isReferal==0)
            {
                if(is_pan_required==1)
                {
                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                }
                else
                {
                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                }
            }
            else
            {
                if(cus_single==0 && emp_single==0)
                {
                        if(cus_ref_code!='' && emp_ref_code!='')
                        {
                            if(is_pan_required==1)
                            {
                                 var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"</p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                            else
                            {	
                                 var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                        }
                        else if(cus_ref_code=='' && emp_ref_code=='')
                        {
                            if(is_pan_required==1)
                            {
                                  var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                            else
                            {
                                  var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                        }
                        else if(emp_ref_code!='' || cus_ref_code!='')
                        {
                            if(is_pan_required==0)
                            {
                                    var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                            else
                            {
                                  var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                        }
                }
                else if(cus_single==1|| emp_single==1)
                {
                        if(is_pan_required==1)
                        {
                                  var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                        else
                        {
                                    var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                }
            }
        }	
        else
        {	
            
            if(agent_refferal == 1)
          {
              var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"<p>AGENT CODE"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='Agent Referal Number' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
               
          }else
                {
                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                }
                if(isReferal==0)
                {
                        if(is_pan_required==1)
                        {
                                var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                        else
                        {
                                var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                }
                else
                {	
                        if(cus_single==0 && emp_single==0)
                        {
                            if(cus_ref_code!='' && emp_ref_code!='')
                            {	
                                    if(is_pan_required==1)
                                    {
                                         var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {
                                        var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                            }
                            else if(cus_ref_code=='' && emp_ref_code=='')
                            {
                                if(is_pan_required==1)
                                {
                                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                            }
                            else if(emp_ref_code!='' || cus_ref_code!='')
                            {
                                if(is_pan_required==1)
                                {
                                        var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                        var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                            }
                        }
                        else if(cus_single==1|| emp_single==1)
                        {
                                if(is_pan_required==1)
                                {
                                    var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                        }
                }
        }
        }
        else
        {
                if(($('#branch_set').val()==1 && askbranch==1 && is_branchwise_cus_reg!=1 ))
                {
                    get_branch();
                    // get_cusname();  //cus name by default in acc_name while join sch//
                    if(agent_refferal == 1)
                      {
                          var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"<p>AGENT CODE"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='Agent Referal Number' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                           
                      }else
                            {
                                 var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                    if(isReferal==0)
                    {
                        if(is_pan_required==1)
                        {
                             var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                        else
                        {
                             var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                    }
                    else
                    {
                        
                        if(agent_refferal == 1)
                          {
                              var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"<p>AGENT CODE"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='Agent Referal Number' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                               
                          }else
                                {
                                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                        if(cus_single==0 && emp_single==0)
                        {
                                if(cus_ref_code!='' && emp_ref_code!='')
                                {
                                    if(is_pan_required==1)
                                    {
                                         var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {	
                                         var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                }
                                else if(cus_ref_code=='' && emp_ref_code=='')
                                {
                                    if(is_pan_required==1)
                                    {
                                          var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {
                                          var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                }
                                else if(emp_ref_code!='' || cus_ref_code!='')
                                {
                                    if(is_pan_required==0)
                                    {
                                            var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {
                                          var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                }
                        }
                        else if(cus_single==1|| emp_single==1)
                        {
                                if(is_pan_required==1)
                                {
                                          var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                            var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                        }
                    }
                }	
                else
                {	
                    
                    if(agent_refferal == 1)
                      {
                          var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"<p>AGENT CODE"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='Agent Referal Number' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                           
                      }else
                            {
                                 var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                        if(isReferal==0)
                        {
                                if(is_pan_required==1)
                                {
                                        var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                        var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                        }
                        else
                        {	
                            
                            console.log(111);
                                if(cus_single==0 && emp_single==0)
                                {
                                    if(cus_ref_code!='' && emp_ref_code!='')
                                    {	
                                            if(is_pan_required==1)
                                            {
                                                 var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                            }
                                            else
                                            {
                                                var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                            }
                                    }
                                    else if(cus_ref_code=='' && emp_ref_code=='')
                                    {
                                        if(is_pan_required==1)
                                        {
                                             var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                        else
                                        {
                                             var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                    }
                                    else if(emp_ref_code!='' || cus_ref_code!='')
                                    {
                                        if(is_pan_required==1)
                                        {
                                                var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                        else
                                        {
                                                var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p><p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                    }
                                }
                                else if(cus_single==1|| emp_single==1)
                                {
                                        if(is_pan_required==1)
                                        {
                                            var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                        else
                                        {
                                             var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'   class='form-control width50' readonly='readonly' /></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                }
                        }
                }
        }
	 }
	     else if(is_enquiry == 0 && cusName_edit == 1)
	  {
	      console.log(33);
	      if(agent_refferal == 1)
          {
              var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"<p>AGENT CODE"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='Agent Referal Number' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
               
          }else
                {
                     var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50' readonly='readonly' /></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                }
	      
	       if(sch_type == 3 && one_time_premium == 1 && otp_price_fixing == 1 && otp_price_fix_type == 1 && rate_fix_by ==0)
	        {
                    var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p>"+"<p><input type='hidden' class='form-control width50' name='goldrate'  id='goldrate' value="+goldrate_22ct+" /></p>"+"<p>Current Rate <strong>"+goldrate_22ct+"</strong>&nbsp;will be fixed till end of the scheme</p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
            }
	     
	     else if(schjoin_amt == 0 || sch_type!=3 || flx_sch_type==3)  //Getting Payment Amount From Customer
        {
        if(($('#branch_set').val()==1 && askbranch==1 && is_branchwise_cus_reg!=1 )) //Disable Branch When Branchwise customer registration is Enabled
        {
            getSchJoinBranches(schemeId);
          //  get_cusname();   //cus name by default in acc_name while join sch//
	     if(isReferal==0)
            {
                if(is_pan_required==1)
                {
                     var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                }
                else
                {
                     var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"</p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                }
            }
            else
            {
                
                if(cus_single==0 && emp_single==0)
                {
                        if(cus_ref_code!='' && emp_ref_code!='')
                        {
                            if(is_pan_required==1)
                            {
                                 var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"</p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                            else
                            {	
                                 var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                        }
                        else if(cus_ref_code=='' && emp_ref_code=='')
                        {
                            if(is_pan_required==1)
                            {
                                  var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                            else
                            {
                                  var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                        }
                        else if(emp_ref_code!='' || cus_ref_code!='')
                        {
                            if(is_pan_required==0)
                            {
                                    var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                            else
                            {
                                  var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                            }
                        }
                }
                else if(cus_single==1|| emp_single==1)
                {
                        if(is_pan_required==1)
                        {
                                  var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                        else
                        {
                                    var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                }
            }
        }	
        else
        {	
           
                if(isReferal==0)
                {
                        if(is_pan_required==1)
                        {
                                var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                        else
                        {
                                var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                }
                else
                {	
                     console.log(agent_refferal);
                        if(cus_single==0 && emp_single==0)
                        {
                            if(cus_ref_code!='' && emp_ref_code!='')
                            {	
                                    if(is_pan_required==1)
                                    {
                                         var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {
                                        var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                            }
                            else if(cus_ref_code=='' && emp_ref_code=='')
                            {
                                if(is_pan_required==1)
                                {
                                     var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                     var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                            }
                            else if(emp_ref_code!='' || cus_ref_code!='')
                            {
                                if(is_pan_required==1)
                                {
                                        var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                        var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                            }
                        }
                        else if(cus_single==1|| emp_single==1)
                        {
                            
                           
                                if(is_pan_required==1)
                                {
                                    var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else if(agent_refferal == 1)
                                {
                                      var confirmText = "<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='name' id='acc_name' name='id_customer'  class='form-control width50'/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p>"+"<p>Agent Code"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='agent_code' id='agent_code' placeholder='Agent Code (Optional)' required/></p>"+"<p>By joining the scheme you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                     var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                        }
                }
        }
        }
        else
        {
            console.log(222);
                if(($('#branch_set').val()==1 && askbranch==1 && is_branchwise_cus_reg!=1 ))
                {
                    get_branch();
                    // get_cusname();  //cus name by default in acc_name while join sch//
                    if(isReferal==0)
                    {
                        if(is_pan_required==1)
                        {
                             var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                        else
                        {
                             var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch' placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                        }
                    }
                    else
                    {
                        if(cus_single==0 && emp_single==0)
                        {
                                if(cus_ref_code!='' && emp_ref_code!='')
                                {
                                    if(is_pan_required==1)
                                    {
                                         var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {	
                                         var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                }
                                else if(cus_ref_code=='' && emp_ref_code=='')
                                {
                                    if(is_pan_required==1)
                                    {
                                          var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {
                                          var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                }
                                else if(emp_ref_code!='' || cus_ref_code!='')
                                {
                                    if(is_pan_required==0)
                                    {
                                            var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                    else
                                    {
                                          var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                    }
                                }
                        }
                        else if(cus_single==1|| emp_single==1)
                        {
                                if(is_pan_required==1)
                                {
                                          var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs'  placeholder='Select branch' name='id_branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                            var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Select Branch &nbsp;&nbsp;&nbsp; :&nbsp;"+" <select id='branchs' name='id_branch'  placeholder='Select branch' class='form-control width50'></select></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                        }
                    }
                }	
                else
                {	
                        if(isReferal==0)
                        {
                                if(is_pan_required==1)
                                {
                                        var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                                else
                                {
                                        var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                }
                        }
                        else
                        {	
                                if(cus_single==0 && emp_single==0)
                                {
                                    if(cus_ref_code!='' && emp_ref_code!='')
                                    {	
                                            if(is_pan_required==1)
                                            {
                                                 var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                            }
                                            else
                                            {
                                                var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                            }
                                    }
                                    else if(cus_ref_code=='' && emp_ref_code=='')
                                    {
                                        if(is_pan_required==1)
                                        {
                                             var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                        else
                                        {
                                             var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                    }
                                    else if(emp_ref_code!='' || cus_ref_code!='')
                                    {
                                        if(is_pan_required==1)
                                        {
                                                var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                        else
                                        {
                                                var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p><p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                    }
                                }
                                else if(cus_single==1|| emp_single==1)
                                {
                                        if(is_pan_required==1)
                                        {
                                            var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>PAN NO"+"&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' style='text-transform: uppercase !important;' name='pan_no' id='pan_no' placeholder='PAN NO' required/></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                        else
                                        {
                                             var confirmText = "<p>Are you sure you want to join the Purchase plan <strong>"+schemeName+"</strong> ?</p><p>Account Name"+"&nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='text' class='form-control width50' name='account_name' id='acc_name' placeholder='Name' required/></p>"+"<p><input type='hidden' class='form-control width50' name='id_branch'  id='id_branch' value="+id_branch+" /></p>"+"<p>Referal Code"+"&nbsp; &nbsp;&nbsp;&nbsp;"+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='referal_code' placeholder='Code(optional)' id='referal_code'   /></p><p>Payment Amount"+""+"  :"+"&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='payment_amount' id='pay_amt' placeholder='Enter Payment Amount' required/></p>"+"<p>By joining the Purchase plan you agree with the Terms & Conditions of "+'<strong>'+companyName+'</strong>'+"";
                                        }
                                }
                        }
                }
        }
	 }
	
	 else
	 { 
	    if(flx_sch_type==1)
	           {
	               var confirmText ="<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Interseted Amount"+"&nbsp;&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='interseted_amount' id='interseted_amount' placeholder='Interested Amount'></p><p>Description"+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+"<textarea name='message'  class='form-control width50' placeholder='Enter Your Message' style='height: 37px;'/></p>";
	           }else{
	               var confirmText ="<p>Are you sure you want to join the scheme <strong>"+schemeName+"</strong> ?</p><p>Interseted Weight"+"&nbsp;&nbsp;&nbsp;"+"<input type='number' class='form-control width50' name='interseted_weight' id='interseted_weight' placeholder='Interested Weight in Grams'></p><p>Description"+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+"<textarea name='message'  class='form-control width50' placeholder='Enter Your Message' style='height: 37px;'/></p>";
	           }
	 }
	}
	else{		
		var confirmText = "<p><center> Kindly visit our showroom for new scheme enrollment....<center></p>";
		$('.modal-footer').hide();
	  }	
	if(allow_join==true && cusName_edit != 1)
		{
			$('#schemeJoin_modal .modal-body').html(confirmText);
			$('#schemeJoin_modal').modal('show');
		    var cus_name=$('#name').val();    //assigned cus _name from session//HH
	        $('#acc_name').val(cus_name);   //customer name by default in account name while joining scheme//
		}
		
	else if(allow_join==true && cusName_edit == 1){
	    console.log(confirmText);
	    $('#schemeJoin_modal .modal-body').html(confirmText);
			$('#schemeJoin_modal').modal('show');
		   /* var cus_name=$('#name').val();    //assigned cus _name from session//
	        $('#acc_name').val(cus_name); */  //customer name by default in account name while joining scheme//
	}
		else
		{
			alert('maximum scheme group limit reached');
		}
}
function clsfyTermsbtn(sch_clsfy_id)
	{
		$('#clsfy_tc_block button').remove();
		terms_btn = "<button class='theme-txt btn btn-xs' onclick='javascript:schemeTermsModal("+sch_clsfy_id+");'>Terms & Conditions</button>";
		$("#clsfy_tc_block").append(terms_btn); 
	}
scheme ={
	getSchemeDts : function(schemeType) {
	 $("#spinner").css('display','block');
		my_Date = new Date();
		$.ajax({
			url:baseURL+"index.php/chitscheme/get_classifications/"+"?nocache=" + my_Date.getUTCSeconds(),
			type : "GET",
			async:false,
			dataType: "json",
			success : function(result) {
		   		var active_tab_name = '';			   		
		   		var active_tab = '';			   		
		   		var active = '';			   		
		   		var filter = '';			   		
		   		var c = 0;	
				/*$.each( result.commodities, function( i, v ) {
					if( c == 0 ){
						active_tab_name = i;
					}
					filter += "<button type='button' class='"+(c == 0 ? 'theme-btn-bg':'')+" metal_btn btn btn-sm' id='metal_btn"+v.id_metal+"' value='"+v.id_metal+"'>"+v.metal+"</button>";
					c++;
				});
				if(filter != ''){
					$("#metal_filter").append(filter+"<hr style='margin:5px'>");
				}*/
				$.each( result.branches, function( i, v ) {
					if( c == 0 ){
						active_tab_name = i;
					}
					filter += "<button type='button' class='"+(c == 0 ? 'theme-btn-bg':'')+" branch_btn btn btn-sm' id='branch_btn"+v.id_branch+"' value='"+v.id_branch+"'>"+v.name+"</button>";
					c++;
				});
				if(filter != ''){
					$("#branch_filter").append(filter+"<hr style='margin:5px'>");
				}
				$.each( result.classification, function( i, v ) {
					if( active_tab == ''){
						active_tab = (active_tab_name != '' ? (active_tab_name == v.id_branch) : i == 0);
						if(active_tab){
							var setActive =  true;	
						}						
					}else{
						var setActive =  false;
					}
					if(setActive == true) {
						active='active';
						terms_btn = "<button class='theme-txt btn btn-xs' onclick='javascript:schemeTermsModal("+v.id_classification+");'>Terms & Conditions</button>";
						$("#clsfy_tc_block").append(terms_btn); 
					}
					else{
						active='';
					}
					var tab_header = $("<li class='"+active+" classify_tab classify_tab"+v.id_branch+"' style='"+(active_tab_name != '' ? (active_tab_name != v.id_branch ? 'display:none': '' ) : '' )+"'><a href='#tab_"+v.id_classification+"' data-toggle='pill' onclick='javascript:clsfyTermsbtn("+v.id_classification+");' >"+v.classification_name+"</a></li>");
					$("#sch_clsfy_tabs").append(tab_header);
					var tab_div = $("<div class='tab-pane overflow "+active+"' id='tab_"+v.id_classification+"'>");
					$("#tab_sch_content").append(tab_div);
					var table_header = $("<table id='table_header_"+v.id_classification+"' class='table table-bordered table-striped table-responsive table_header'><thead><tr><th>#</th><th>Purchase plan Name</th><th>Monthly Payable</th><th>Months</th><th>Maximum Payable</th><th>Action</th></tr></thead><tbody></tbody></table>"); 
					$("#tab_"+v.id_classification).append(table_header);
				});
	   		}	
		});
		my_Date = new Date();
		$.ajax({
			url:baseURL+"index.php/chitscheme/get_avail_schemes/"+"?nocache=" + my_Date.getUTCSeconds(),
				type : "GET",
				async:false,
				dataType: "json",
				success : function(result) {		
					var regExistingReqOtp = 0;
					$.each(result.sch_list,function(key, value){
						$('#secheme_select').append('<option value=' + value.id_scheme + '>' + value.scheme_name + '</option>');						
					});	
					schCodes=result.sch_list;
					//console.log(result.schemes);
					$.each( result.schemes, function( key, value ) {						
						regExistingReqOtp = value.regExistingReqOtp;						
						var schName = '"'+value.scheme_name+'"';
						var cmpName = '"'+value.company_name+'"';
						var cus_single ='"'+value.cusbenefitscrt_type+'"';
						var emp_single ='"'+value.empbenefitscrt_type+'"';
						var cus_ref_code ='"'+value.cus_ref_code+'"';
						var emp_ref_code ='"'+value.emp_ref_code+'"';
						var newSchjoinonline ='"'+value.newSchjoinonline+'"';
						var isReferal ='"'+value.isReferal+'"';
						var is_pan_required ='"'+value.is_pan_required+'"';
						var is_branchwise_cus_reg ='"'+value.is_branchwise_cus_reg+'"';
						var cusName_edit ='"'+value.cusName_edit+'"';
						var is_multi_commodity ='"'+value.is_multi_commodity+'"';
						var id_metal ='"'+value.id_metal+'"';
						var askBranch ='"'+value.askBranch+'"';
						var id_branch ='"'+value.id_branch+'"';
						var sch_limit =value.sch_limit;
						var sch_limit_value =value.sch_limit_value;
						var accounts =value.accounts; 
						var cus_kyc_status = value.cus_kyc_status;
						var get_amt_in_schjoin = value.get_amt_in_schjoin;
						var min_amount =value.min_amount;
						var max_amount =value.max_amount;
						var flx_denomintion =value.flx_denomintion;
						var scheme_type =value.scheme_type;
						var flexible_sch_type =value.flexible_sch_type;
						var one_time_premium =value.one_time_premium;
						var agent_refferal = value.agent_refferal;
						var allow_join=true;
						if(sch_limit==1&&sch_limit_value!=null)
						{
							if(sch_limit_value>accounts)
							{
								 allow_join=true;
							}
							else
							{
								allow_join=false;
							}
						}
                        if(cus_kyc_status == 1)
                        {
                            if(value.is_enquiry==0)
                            {
                                  join_btn = "<button class='btn-primary btn btn-sm join-btn' onclick='javascript:confirm_scheme("+value.id_scheme+","+value.id_metal+","+schName+","+cmpName+","+newSchjoinonline+","+isReferal+","+is_pan_required+","+cus_single+","+emp_single+","+cus_ref_code+","+emp_ref_code+","+is_branchwise_cus_reg+","+cusName_edit+","+allow_join+","+get_amt_in_schjoin+","+min_amount+","+max_amount+","+flx_denomintion+","+scheme_type+","+flexible_sch_type+","+one_time_premium+","+value.is_multi_commodity+","+value.askBranch+","+value.id_branch+","+value.is_enquiry+","+value.rate_fix_by+","+value.rate_select+","+value.otp_price_fixing+","+value.otp_price_fix_type+","+result.goldrate_22ct+","+value.agent_refferal+",event);'> Join</button> ";
                            }else{
                                  join_btn = "<button style='font-size: 9px;width: 64px;' class='btn-primary btn btn-sm join-btn' onclick='javascript:confirm_scheme("+value.id_scheme+","+schName+","+cmpName+","+newSchjoinonline+","+isReferal+","+is_pan_required+","+cus_single+","+emp_single+","+cus_ref_code+","+emp_ref_code+","+is_branchwise_cus_reg+","+cusName_edit+","+allow_join+","+get_amt_in_schjoin+","+min_amount+","+max_amount+","+flx_denomintion+","+scheme_type+","+flexible_sch_type+","+one_time_premium+","+value.is_enquiry+","+value.rate_fix_by+","+value.rate_select+","+value.otp_price_fixing+","+value.otp_price_fix_type+","+result.goldrate_22ct+","+value.agent_refferal+",event);'> Enquiry</button> ";
                            }
                        }
                        else{
                            var kycURL = baseURL+"index.php/chitscheme/kyc_form";
                            join_btn = "<a href="+kycURL+" class='btn-primary btn btn-sm join-btn'>Join</a>"
                        }
						max_wgt_payable = (value.max_weight * value.total_installments);
						//console.log(max_wgt_payable);
						if(value.scheme_type == 1)
						{
							var payable = "Min "+value.min_weight+" gm  Max "+value.max_weight+" gm";
							var tot_payable = max_wgt_payable+" gm";
						}
						else if(value.scheme_type == 3)        // based in the sch type to shown payable //HH
						{
							var payable = value.currency_symbol+" Min "+value.min_amount+"  Max "+value.max_amount;
							var tot_payable = value.currency_symbol+" "+value.total_payable;
						}
						else
						{
							var payable = value.currency_symbol+" "+value.payable;
							var tot_payable = value.currency_symbol+" "+value.total_payable;
						}
						var arow = $("<tr><td>"+(key+1)+"</td><td>"+value.scheme_name+"</td><td>"+payable+"</td><td>"+value.total_installments+"</td><td>"+tot_payable+"</td><td>"+join_btn+"</td></tr>");
							//console.log(arow);
						$("#table_header_"+ value.id_classification +" tbody").append(arow);
			   		});
			   $('#regExistingReqOtp').val(regExistingReqOtp);
			   		if(result.allow_join.status == false)
							{
								scheme.disableJoin(result.allow_join.msg);
							}
							else
							{
								scheme.enableJoin();
							}
				}		
			});
			$("#spinner").css('display','none');
  
		},
		disableJoin : function(msg){
		$('.join-btn').prop('disabled', true);
     var alert_msg ='<div class="alert alert-danger" align="center"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>'+msg+'</strong></div>'; 
		$('.alert_msg').html(alert_msg);
	},
	enableJoin : function(){
		$('.join-btn').prop('disabled', false);
							$('.join-tooltip').prop('title', '');
							$('.join-tooltip').hide();
	},
}
$('#scheme').on('change', function() {  
	    var scheme = schCodes.filter(function(schCode) { 
          return schCode.id_scheme == $('#scheme').val() ;
        }); 
        console.log(scheme); 
        if(scheme[0].is_pan_required == '1'){ 
            $('#pan_no').prop('required',true);
            $('#pan_no').prop('minlength',10);
            $('#pan_no').css('display','');
              $('#pan_no').css('display','');
        }else{ 
           $('#pan_no').prop('required',false);  
           $('#pan_no').prop('minlength',0);
           $('#pan_no').css('display','none');
           $('#pan_no').css('display','none');
        } 
	});	
	$("#pan_no").on('change',function(e){
			var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;
			if(!regexp.test($("#pan_no").val()))
        	{
        		 $("#pan_no").val("");
        		 alert("Not a valid PAN No.");
        		 $("#pan_no").focus();
        		 return false;
        	}
});
$("#exis_pan_no").on('change',function(e){
	var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;
	if(!regexp.test($("#exis_pan_no").val()))
	{
		 $("#exis_pan_no").val("");
		 alert("Not a valid PAN Number");
		 $("#exis_pan_no").focus();
		 return false;        		
	}
});
function get_branch()
{
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/chitscheme/get_branch/',
		dataType: 'json',
		success: function(data){
			console.log(data);
		$.each(data, function (key, data) {	
			$('#branchs').append(
			 $('<option></option>')
			   .attr('value',data.id_branch)
			   .text(data.name)	
				);
		 });
		}
	});
}

function getSchJoinBranches(schemeId)
{
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/chitscheme/getSchJoinBranches/'+schemeId,
		dataType: 'json',
		success: function(data){
			console.log(data);
		$.each(data, function (key, data) {	
			$('#branchs').append(
			 $('<option></option>')
			   .attr('value',data.id_branch)
			   .text(data.name)	
				);
		 });
		}
	});
}

//exit scheme reg
function get_branchname()
{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/chitscheme/get_branch/',
		dataType:'json',
		success:function(data){
		 var scheme_val =  $('#id_branch').val();
		 var scheme_val1 =  $('#id_branch1').val();
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
			 $("#branch_select1").select2("val",(scheme_val1!='' && scheme_val1>0?scheme_val1:''));
			 $(".overlay").css("display", "none");	
		}
	});
}
$('#branch_select').select2().on("change", function(e) {
			if(this.value!='')
			{	 
				$("#id_branch").val($(this).val());
			}
		});
//exit scheme reg
/* // scheme name for existing // */
$('#secheme_select').select2().on("change", function(e){				
	if(this.value!='')
	{	 
		var id_scheme = this.value; 
		get_groupename(id_scheme);
		$("#id_scheme").val($(this).val()); 
		// Check and set PAN required
		var scheme = schCodes.filter(function(schCode) { 
			return schCode.id_scheme == id_scheme ;
		});  
		var code=scheme[0].code;
		$("#scheme_code").val(code); 
		if(scheme[0].is_pan_required == '1'){
			$('#exis_pan_no').prop('required',true);
			$('#exis_pan_no').prop('minlength',10);
			$('#exis_pan_no').css('display','');
			$('#pan').css('display','');
		}else{
			$('#exis_pan_no').prop('required',false);  
			$('#exis_pan_no').prop('minlength',0);
			$('#exis_pan_no').css('display','none');
			$('#pan').css('display','none');
		} 
	}
	else
	{
		$('#group_select').empty();
	}
});
$('#group_select').select2().on("change", function(e){	
  $("#id_scheme_group").val($(this).val()); 
  $("#group_code").val($("#group_select option:selected").text());
});
function get_schemename()
{
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/chitscheme/get_avail_schemes/',
		dataType:'json',
		success:function(data){
			$("#spinner").css('display','none');
			$.each(data.schemes, function (key, item) {					  	
				$('#secheme_select').append(
				$("<option></option>")
				.attr("value", item.id_scheme)						  
				.text(item.scheme_name )
				);
			});
			$("#secheme_select").select2({
				placeholder: "Select Name ",
				allowClear: true,
			});
			$("#secheme_select").select2("val", ($('#id_scheme').val()!=null?$('#id_scheme').val():''));
			$(".overlay").css("display", "none");	
		}
	});
}
/* // scheme name for existing// */
function get_groupename(id_scheme)
{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'POST',
		data:{'id_scheme':id_scheme},
		url: baseURL+'index.php/chitscheme/get_groups/',
		dataType:'json',
		success:function(data){ 
			$("#spinner").css('display','none');	 
			$('#group_select').empty();
			$.each(data, function (key, item) {
				$('#group_select').append(
				$("<option></option>")
				.attr("value", item.id_scheme_group)						  
				.text(item.group_code)
				);			   				
			});
			$("#group_select").select2({
				placeholder: "Select group code",
				allowClear: true
			});
			$("#group_select").select2("val", ($('#id_group').val()!=null?$('#id_group').val():''));
			$(".overlay").css("display", "none");	
		}
	});
}
// scheme_group//

//rate history
function submit_ratehis(id_branch="",from_date="",To_date="")
{
	var from_date = $('#from_date').val();
	var To_date =  $('#To_date').val();
	var id_branch= $('#id_branch').val();
	var branch_settings= $('#branch_settings').val();
    if(branch_settings==1)
    {
        if(id_branch!='')
        {
           $('.overlayy').css("display", "block");
           $.ajax({
	            type: "POST",	
	            url:baseURL+ "index.php/chitscheme/rates_history?nocache=" + my_Date.getUTCSeconds(),
	            data: {'id_branch':id_branch,'from_date':from_date,'To_date':To_date},			 
	            dataType: 'json',			
	            success:function(data)
	            {
                    $('.overlayy').css("display", "none");
                    oTable = $('#rate_history').dataTable({
	                    "bDestroy": true,
	                    "columnDefs": [{"className": "dt-center", "targets": "_all"}],
	                    "aaData": data,
	                     "order": [],
	                    "aoColumns": [{ "mDataProp": "updatetime" },
	                    { "mDataProp": "goldrate_22ct" },
	                    { "mDataProp": "silverrate_1gm" },
	                    { "mDataProp": function ( row, type, val, meta ){
		                    var platinum_rate = row.platinum_1g>0?row.platinum_1g:'-';
		                    return (platinum_rate);
	                    }},
                    ]}); 
	            }
            });
         }
    }
    else
    {
      $.ajax({
            type: "POST",	
            url:baseURL+ "index.php/chitscheme/rates_history?nocache=" + my_Date.getUTCSeconds(),
            data: {'id_branch':id_branch,'from_date':from_date,'To_date':To_date},			 
            dataType: 'json',			
            success:function(data)
            {
                oTable = $('#rate_history').dataTable({
	                "bDestroy": true,
	                "columnDefs": [{"className": "dt-center", "targets": "_all"}],
	                "aaData": data,
	                "aoColumns": [{ "mDataProp": "updatetime" },
	                { "mDataProp": "goldrate_22ct" },
	                { "mDataProp": "silverrate_1gm" },
	                { "mDataProp": function ( row, type, val, meta ){
		                var platinum_rate = row.platinum_1g>0?row.platinum_1g:'-';
		                return (platinum_rate);
	                }},
                ]}); 
            }
	    });
    }
}
//rate history


function verify_pan(){
    $("#schemeJoin_modal #spinner").css('display','block'); 
	var my_Date = new Date();
	$.ajax({
	   url:baseURL+"index.php/chitscheme/verify_pan?nocache=" + my_Date.getUTCSeconds(),
	   type : "POST",
	   dataType: "json",
	   data:{'pan_no':$("#schemeJoin_modal #pan_no").val()},
	   success : function(result) {	 
	       $("#schemeJoin_modal #spinner").css('display','none'); 
		   if(result.status){
		       $("#schemeForm").submit();
		   }else{
		       alert(result.msg);
		       $("#confirm").prop("disabled",false);
		   }
	   },
	   error : function(error){
			$("#schemeJoin_modal #spinner").css('display','none'); 
	   }
	});
}
 