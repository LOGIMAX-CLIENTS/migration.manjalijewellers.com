var path =  url_params();
var ctrl_page = path.route.split('/');
var gift_approved;
var rows_added=0;

$(document).ready(function(){ 
   
    
    
    
    $('#gift_issue_form').on('keydown', function(event) {
        // Check if the Enter key was pressed (key code 13)
        if (event.keyCode === 13) {
            // Prevent the default form submission action
            console.log("Keydown")
            event.preventDefault();
            // Optionally, you can add additional code here to handle the Enter key press
        }
    });

    $("#mobile_number").autocomplete({
            source: function( request, response ){
                var mobile=$( "#mobile_number" ).val();
                $.ajax({
                    url:  base_url+'index.php/admin_customer/ajax_get_customers_list',
                    dataType: "json",
                    type: 'POST',
                    data:{'mobile':mobile},
                    success: function( data ) 
                    {
                        var data = JSON.stringify(data);
                        data = JSON.parse(data);
                        var cus_list = new Array(data.length);
                        var i = 0;
                        data.forEach(function (entry) {
                            console.log(entry.mobile);
                            var customer= {
                                label: entry.mobile+'  '+entry.firstname,
                                value:entry.id_customer,
                                mobile:entry.mobile
                            };
                            cus_list[i] = customer;
                            i++;
                        });
                        response(cus_list);
                    }
                });
            },
            minLength: 4,
        	delay: 300, // this is in milliseconds
        	select: function(e, i){
            	e.preventDefault();
            	$("#mobile_number" ).val(i.item.label);
            	$("#id_customer").val(i.item.value);
            	$("#cus_mobile").val(i.item.mobile);
            	$('.overlay').css('display','block');
            	$('#scheme_account').empty();
            	my_Date = new Date();
            	var id_customer=$('#id_customer').val();
            	var cus_mobile = $('#cus_mobile').val();
            	
            	if($('#id_customer').val()!='')
            	{
            	    $('#txt_mobile').val(cus_mobile);
                    set_accounts();
            	}else{
            	    $("#scheme_account").select2("val",'');
            		$('#scheme_account').empty();
            		$('#scheme-detail-box').addClass('box-default');
            		$('#mobile_number').val('');
            		$('#id_customer').val('');
            		$('#id_scheme_account').val('');
            		alert('Invalid Details');
            	}
        	},
        	response: function(e, i) {
                // ui.content is the array that's about to be sent to the response callback.
                if (i.content.length === 0) {
                   alert('Please Enter a valid Number');
                   $('#mobile_number').val('');
                } 
            },
        	focus: function(e, i) {
                e.preventDefault();
                $("#mobile_number").val(i.item.label);
        		}
        });
        
        
    $("#Scheme_account_no").autocomplete({
    	source: function( request, response ) 
    	{
    		var account=$( "#Scheme_account_no" ).val();
    		var Split=account.split('-');
    		var scheme_code=Split[0];
    		var acc_no=Split[1];
    	    $.ajax({
        		url:  base_url+'index.php/admin_customer/ajax_get_scheme_account_list',
        		dataType: "json",
        		type: 'POST',
        	    data:{'acc_no':acc_no,'scheme_code':scheme_code },
        		success: function( data ) 
        		{
        			var data = JSON.stringify(data);
        			data = JSON.parse(data);
        			  var cus_list = new Array(data.length);
        			  var i = 0;
        			  data.forEach(function (entry) {
        				  console.log(entry.mobile);
        				  var customer= {
        					  label: entry.name+' '+entry.scheme_acc_number,
        					  id_scheme_account:entry.id_scheme_account,
        					  mobile:entry.mobile,
        					  id_customer:entry.id_customer
        				  };
        				  cus_list[i] = customer;
        				  i++;
        			  });
        			  response(cus_list);
        		}
    	   });
    	},
    	minLength: 3,
    	delay: 300, // this is in milliseconds
    	select: function(e, i)
    	{
            e.preventDefault();
            $('#Scheme_account_no').prop('disabled', true);
            $('#mobile_number').prop('disabled', true);
            $("#mobile_number" ).val(i.item.mobile);
            $("#id_customer").val(i.item.id_customer);
            $("#cus_mobile").val(i.item.mobile);
            $("#id_scheme_account").val(i.item.id_scheme_account);
            $('.overlay').css('display','block');
            $('#scheme_account').empty();
            my_Date = new Date();
            var id_customer=$('#id_customer').val();
            var id_scheme_account=$('#id_scheme_account').val();
            var cus_mobile = $('#customer_mobile').val();
    	    if($('#id_customer').val()!='')
    	    {
                set_accounts();
            }
    	    else
    	    {
                $("#scheme_account").select2("val",'');
                $('#scheme_account').empty();
                $('#mobile_number').val('');
                $('#id_customer').val('');
                $('#id_scheme_account').val('');
                clear_account_detail();
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Invalid Details"});
    	    }
    	},
    	response: function(e, i) {
            // ui.content is the array that's about to be sent to the response callback.
            if (i.content.length === 0) {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter a valid Acc.no"});
            // alert('Please Enter a valid Acc.no');
             $('#Scheme_account_no').val('');
            } 
        },
        focus: function(e, i) {
            e.preventDefault();
            $("#Scheme_account_no").val(i.item.label);
        }
    });
    
    
    $('#verify_otp_gift').click(function(event) {
    		$("div.overlay").css("display", "block");
    		var otp=$("#otp_data").val();
    		console.log(otp);
    		$.ajax({
    			url:base_url+"index.php/admin_manage/verifyotp_gift",
    			type : "POST",
    			data : {otp:otp},
    			dataType:'json',
    			success : function(data) {	
    				//alert();
    				console.log(data.result);
    				if(data.result==1)
    				{
    					{
    						$.toaster({ priority : 'success',
    						 title : 'Success', message : "OTP verified successfully" });
    						var filterval="blur(0px)";
    						$("#gift_otp_btns").hide();
    						$('#txt_mobile').attr("disabled",true);
    						$('#otp_data').attr("disabled",true);
    						$('#suc_msg_otp').html('');
    						$("#isVerified").val('1');
    						//$('#verify_otp_modal').modal('toggle'); 
    						/*$('#gift_articles').css('filter',filterval);
    					//	$("#gift_list").attr("disabled",false);
    					//	$("#prize_details").attr("disabled",false);
    						$('#gift_articles').css('pointer-events','auto');
    						$("#button_otp").hide();*/
    						}
    				}
    				else if(data.result==5)
    				{
    					$.toaster({ priority : 'warning',
    						 title : 'warning', message : data.msg });
    				}
    				else if(data.result==6)
    				{
    					$.toaster({ priority : 'warning',
    						 title : 'warning', message : data.msg });
    				}
    			}
    		});
     });
     //RAHULL 10_11_25 Save button double clicked issue
    $('#save_gift').on('click', function() 
    {
		var $btn = $(this); // Store button reference
   		$btn.prop('disabled', true); // Disable the button immediately
    	 
            var content = $('#gift_issue_form').serializeArray();
    
    		var ref_gift=$("#ref_gift").val();
    		
    		
    		var isOTPReqToGift = $("#isOTPReqToGift").val();
    		var otp_approved = true;
    		
    		if(isOTPReqToGift == 1 && $("#isVerified").val() == 0){
    		    otp_approved = false;
    		}
    
    		if(gift_approved && otp_approved)
    		{
    			/*post_data={
    				'id_gift'          : $("#gift_id").val(),
    				'item_ref_no'      : $("#ref_gift").val(),
    				'gift_amount'      : $("#gift_amount").val(),
    				
    				'id_scheme_account': $('#gift_account').val(),
    				'gift_name':$("#gift_name").val(),
    				};
    				console.log(post_data);*/
    				$(".overlay").css('display','block');
    				$.ajax({
    					type: 'post',
    					url: base_url+'index.php/admin_manage/save_giftissued',
    					dataType:'json',
    					data : content,
    					success:function(data){
    						$(".overlay").css('display','none');
    						console.log(data);
    						if(data)
    						{
    						    
    						    
    						    setTimeout(function(){
                                    window.location.reload(true);
                                }, 5*1000);
    
    							$.toaster({ 
    								priority : 'success', 
    								title : 'Gift Details Updated', 
    								message : ''+"</br>Gift has been successfully saved",
    								settings: {
    									timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
    								}
    							});
    							$('#gift_issue_modal').modal('hide');
    							
    						}
    						else
    						{
    							$("#ref_gift").val('');
    								$("#gift_selected_details").text('');
    							$.toaster({ 
    								priority : 'warning', 
    								title : 'Data Warning', 
    								message : ''+"</br>Please Select Gifts to Issue for this accounts ",
    								settings: {
    									timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
    								}
    							});
    							
    							
    
    						}
    					},
						error: function() {
							$(".overlay").css('display', 'none');
							$.toaster({
								priority: 'danger',
								title: 'Error',
								message: 'Something went wrong while saving the gift.',
								settings: { timeout: 4000 }
							});
						},
						complete: function() {
							// Re-enable the button after AJAX completes (success or error)
							$btn.prop('disabled', false);
						}
    				});
    		}
    		else
    		{
    			$("#ref_gift").val('');
    			$("#gift_selected_details").text('');
    			if(!gift_approved || gift_approved == undefined){
        		    $.toaster({ 
    				priority : 'warning', 
    				title : 'Data Warning', 
    				message : ''+"</br>Please Select Gifts to Issue for this accounts ",
    				settings: {
    					timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
    				}
    			    });
        		}else if(!otp_approved || otp_approved == undefined){
        		    $.toaster({ 
    				priority : 'warning', 
    				title : 'Data Warning', 
    				message : ''+"</br>Please verify gift otp",
    				settings: {
    					timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
    				}
    			    });
        		}
				// Re-enable the button since the validation failed
        		$btn.prop('disabled', false); 			
    
    		}
    		
    
    }
	);
    	
    	
    $('#ref_gift').on("change", function(e) 
    {
    	var ref_gift=$(this).val();
    	
        getGiftId(ref_gift);
    	
    	
    });
    
    
    function getGiftId(ref_gift){
	    $.ajax({
                type: 'POST',
                url:  base_url+"index.php/admin_manage/getGiftByRef",
                dataType: 'json',
                data : {'ref_gift':ref_gift},
                success: function(data) 
                {
                    var id_gift = data;
                    validate_gift('',id_gift);
                    
                },
		});
	}
    
    
    $('#gift_select_inv').on('change', function() 
    {
    	var id_gift=$(this).val();
    	
    	validate_gift('',id_gift);
    });
    
    $('#scheme_account').on("change", function(e) {
        var id_customer = $('#id_customer').val();
        var mobile = $('#cus_mobile').val();
        
        

        if(this.value!=''){
            clear_gift_form();
            $("#id_scheme_account").val(this.value);
            show_gift_modal(this.value,id_customer,mobile)
        }else{
            clear_gift_form();
        }
    });
    
    
    $('#branch_select').on("change", function(e) {
       
        if(this.value!=''){
            clear_gift_form();
            $("#branch_sel").val(this.value);
            var cus_mobile = $('#cus_mobile').val();
            $('#txt_mobile').val(cus_mobile);
            set_gift_select()
        }else{
            clear_gift_form();
        }
    });
    
    $('#cancel_form').on("click", function(e) {
       
        window.location.reload();
        
    });
    
    
    $('#deduct_gift').on("click", function(e) {
       
        cancel_gift();
        
    });
   
	  
	  
$('#send_otp_gift').click(function(event) {
    var fewSecondsgift = $('#otp_exp').val();
    
    
    var extra = parseInt(fewSecondsgift + 1);
    
    
    $("#send_otp_gift").attr('disabled', true);
    $("#verify_otp_gift").attr('disabled', false);
    
    var i = fewSecondsgift;  //set the countdown
    (function timer(){
        if (--i < 0) 
        return;
        setTimeout(function(){
            $("#send_otp_gift").val('Resend OTP in '+i+'s');
            console.log(i + ' secs');  //do stuff here
            timer();
        }, 1000);
       
    })();
    
    setTimeout(function(){
        $("#send_otp_gift").attr('disabled', false);
        $("#verify_otp_gift").attr('disabled', true);
         $("#otp_txt_box").val('');
        $("#send_otp_gift").val('Resend OTP');
    }, 121*1000);
    
   
    $("#otp_txt_box").show();
    gift_send_otp();
});
  
});  //document.ready ends

function set_accounts()
{
    var id_scheme_account = $('#id_scheme_account').val();
    var id_customer = $('#id_customer').val();
    var mobile = $('#cus_mobile').val();
                
    $.ajax({
        type: 'GET',
        url:  base_url+'index.php/payment/get/ajax/customer/account/'+id_customer+'?nocache=' + my_Date.getUTCSeconds(),
        dataType: 'json',
        cache:false,
        success: function(data) {
            console.log(data.accounts); 
            $('#scheme_account').empty();
            
            if($('#scheme_account').length>0)
            {
                $.each(data.accounts, function (key, acc) 
                {
                    
                    /*if(acc.has_gift == 1 || (acc.has_gift == 1  && id_scheme_account > 0 && id_scheme_account == acc.id_scheme_account)){*/
                    if(acc.has_gift == 1){
                        $('#scheme_account').append(
                        $("<option></option>")
                            .attr("value", acc.id_scheme_account)
                            .text(acc.scheme_acc_number)
                        );

                        $("#scheme_account").select2({
                            placeholder: "Select scheme account",
                            allowClear: true
                        });	
                        $("#scheme_account").select2("val", ($('#id_scheme_account').val()!=null?$('#id_scheme_account').val():''));
                    }
                });
                
                if(id_scheme_account > 0){
                    show_gift_modal(id_scheme_account,id_customer,mobile)
                }
            }
        //disable spinner
        $('.overlay').css('display','none');
        },
        error:function(error){
            console.log(error);
            //disable spinner
            $('.overlay').css('display','none');
        }	
    });
}    

function show_gift_modal(id_Scheme_account,id_cus,mobile)
{
	$("#gift_chart_tbl_inv > tbody").empty();
	$('#ref_gift').val('');
	$("#gift_selected_details").text('');
//	$('#gift_scheme').val(id_scheme);
	$('#gift_account').val(id_Scheme_account);
	$('#txt_mobile').val(mobile);
	$('#id_cus').val(id_cus);
	get_gift_issued_byschemeid(id_Scheme_account);
	
	var logged_branch = $('#id_branch').val();
    
    var branch_select = $('#branch_sel').val();
    
    var id_branch = (logged_branch > 0 ? logged_branch : branch_select);
    
    if(id_branch > 0){
        set_gift_select();
    }
        
	
	
}

function get_gift_issued_byschemeid(id)
{
	$.ajax({
			type: 'GET',
			  url:  base_url+"index.php/admin_manage/get_gift_issued_byaccount?id_scheme_account="+id,
			 dataType: 'json',
			  success: function(data) 
			  {
				console.log(data);
				
				$('#paid_installments').val(data.paid_installments);
				
				var gifts = data.gifts;
				var gift_html='';
				$('#gift_table_byaccount tbody').empty();
				if(gifts.length>0)
				{
					var title_html="<h4>Gift Issued Details</h4>";
					$("#gift_table_issued_title").html(title_html);
					
					$("#gift_table_byaccount").css("display","inline-table");
					
					var gift_total=0;
					$.each(gifts, function(key, gift)
					{
						var sno=key+1;
						var gift_price=parseFloat(gift.gift_value)*parseFloat(gift.gift_count);
						gift_total+=parseFloat(gift_price);
						gift_html+='<tr>'+
						'<td>'+sno+'</td>'+
						'<td>'+gift.gift_name+'</td>'+
						'<td>'+gift.gift_value+'</td>'+
						'<td>'+gift.gift_count+'/'+gift.assigned_qty+'</td>'+
						'<td>'+gift_price+'</td>'+
						'<td>'+gift.date_issued+'</td>'+
						'<td>'+gift.issued_employee+'</td>'+
						'<td><button class="delete btn btn-danger" onclick="show_cancelgift_modal('+gift.id_gift_issued+')" name="delete" type="button"><i class="fa fa-times"></i></button></div></td>'
						'</tr>';
						
						
					});
					gift_html+='<tr style="font-weight:bold;">'+
						'<td></td>'+
						'<td></td>'+
						'<td></td>'+
						'<td>Total</td>'+
						'<td>'+gift_total+'</td>'+
						'<td></td>'+
						'<td></td>'+
						'<td></td>'+
						'</tr>';
				$("#total_gift_value").val(gift_total);
				$('#gift_table_byaccount tbody').append(gift_html);
				}
				else
				{
					$("#gift_table_byaccount").css("display","none");
						$("#gift_table_issued_title").html('');
				}
			  }
			  
			});
}




	
function validate_gift(ref_gift='',id_gift='')
{
	var gift_scheme=$('#gift_scheme').val();
	var gift_account=$('#gift_account').val();
	$(".overlay").css('display','block');
	
	var logged_branch = $('#id_branch').val();
    
    var branch_select = $('#branch_sel').val();
    
    var id_branch = (logged_branch > 0 ? logged_branch : branch_select);
	
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_manage/get_gift_validation',
		dataType:'json',
		data : {'ref_gift':ref_gift,'id_gift':id_gift,'id_scheme':gift_scheme,'id_scheme_account':gift_account,'id_branch':id_branch},
		success:function(data)
		{
			//console.log(data);

			const keys = Object.keys(data);
			const totalLength = keys.length;
			if(totalLength>0)
			{
				$("#gift_name").val(data.gift_name);
				$("#gift_id").val(data.id_gift);
				$("#gift_amount").val(data.gift_unit_price);
				if(data.status==0)
				{
                    var curIssuingGiftIdsArr = []; 
                    $('.cur_gifts').each(function() {
                      curIssuingGiftIdsArr.push($(this).val()); 
                      console.log(curIssuingGiftIdsArr);
                    });

                    var cur_gift_given = getOccurrence(curIssuingGiftIdsArr, data.id_gift);  

				    var yetToIssue = parseInt(data.item_issue_limit) - parseInt(data.gift_count);
	                var actual_eligible_gift_count = (parseInt(yetToIssue) > parseInt(data.available_item_from_stock) ? parseInt(data.available_item_from_stock) : parseInt(yetToIssue));
	                var further_eligible = parseInt(actual_eligible_gift_count) - parseInt(cur_gift_given);

				    var total_gift_count = parseInt(data.gift_count);
				    
				   
				    
				    
					//checking whether id_scheme_account limit reached starts
					if(total_gift_count < data.item_issue_limit && further_eligible > 0)   //2 < 20
					{
						//checking whether the gift already issued 
						
							//checking whether stock available starts
							if(data.total_quantity>0)
							{
							    var cur_gift_value = parseInt($('#cur_gift_value').val()) + parseInt(data.gift_unit_price);
                        	   
                        	                
								//checking gift value starts here 
								var total_gift_value=$("#total_gift_value").val();
								var current_gift_value=parseFloat(total_gift_value)+parseFloat(cur_gift_value);
								if(parseFloat(current_gift_value)<=parseFloat(data.payment_amount))
								{
									//Checking scheme mapped starts here
									if(parseInt(data.id_scheme)==parseInt(gift_scheme))
									{
									    
										gift_approved=1;
										

										$("#gift_selected_details").text("Selected Gift : "+data.gift_name+"   Value : INR "+data.gift_unit_price);
										
                    	                add_gift_issuing_row(data);
                    	                $('#cur_gift_value').val(cur_gift_value);
                    	                var yetToIssue = data.item_issue_limit - data.gift_count;

										/*$.toaster({ 
											priority : 'success', 
											title : 'Proceed', 
											message : ''+"</br>Selected Gift available you may proceed to save",
											settings: {
												timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
											}
										});*/
										//return true;
									}
									else
									{
										$.toaster({ 
											priority : 'warning', 
											title : 'No scheme Mapped', 
											message : ''+"</br>Selected Gift is not mapped to the scheme",
											settings: {
												timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
											}
										});
										gift_approved=false;
										$("#ref_gift").val('');
											$("#gift_selected_details").text('');
										//return false;
									}
									//Checking scheme mapped ends here

								}
								else
								{
									$.toaster({ 
										priority : 'warning', 
										title : 'Gift Value Limit', 
										message : ''+"</br>Selected Gift Value exceeds the total payment",
										settings: {
											timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
										}
									});
									gift_approved=false;
									$("#ref_gift").val('');
										$("#gift_selected_details").text('');
									//return false;
								}
								//checking gift value starts ends 
							}
							else
							{
								$.toaster({ 
									priority : 'warning',
									title : 'NO Stock',
									message : ''+"</br>Selected Gift not available currently", 
									settings: {
										timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
									}							
									});
									gift_approved=false;
									$("#ref_gift").val('');
										$("#gift_selected_details").text('');
								//return false;
							}
						//checking whether stock available ends
					}
					else
					{
						$.toaster({ 
							priority : 'warning', 
							title : 'Limit Reached', 
							message : ''+"</br>The limit allocated for this account reached already pls select any other gift",
							settings: {
								timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
							}
						});
						gift_approved=false;
						$("#ref_gift").val('');
							$("#gift_selected_details").text('');
						//return false;
					}
					//checking whether id_scheme_account limit reached ends
				}
				else
				
				{
				    
				    	
				    	
					$.toaster({ 
						priority : 'warning', 
						title : 'Gift Already Issued', 
						message : ''+"</br>Selected Gift is already issued ",
						settings: {
							timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
						}
					});
					gift_approved=false;
					$("#ref_gift").val('');
					$("#gift_selected_details").text('');

					//return false;
				}
				
			}
			else
			{
				$.toaster({ 
					priority : 'warning', 
					title : 'No scheme Mapped', 
					message : ''+"</br>Selected Gift is not mapped to the scheme",
					settings: {
						timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
					}
				});
				gift_approved=false;
				$("#ref_gift").val('');
					$("#gift_selected_details").text('');
				//return false;
			}
			
		
		},
		error:function(error)
    	{
        	$('.overlay').css('display','none');
    	}
	});
}
	
function getOccurrence(array, value) 
{
    return array.filter((v) => (v === value)).length;
}
      
function add_gift_issuing_row(data)
{
    //rows_added variable is incremented every row added ,it holds total number of rows added including deleted rows
	rows_added++;
	var i = 1;
	var table_rows = $('#gift_chart_tbl_inv tbody tr').length;
		table_rows=rows_added;
		i=table_rows;
		
		console.log(data);
	  var row = "<tr>"
    				+"<td><input type='hidden' class = 'cur_gifts' name='gift["+i+"][id_gift]' value='"+data.id_gift+"'/><input type='hidden' name='gift["+i+"][ref_no]' value='"+data.ref_gift+"'/> "+ data.id_gift +  "<br/>"+data.ref_gift+"</label></td>"
    				+"<td><input type='hidden' name='gift["+i+"][gift_name]' value='"+data.gift_name+"'/>"+data.gift_name+"</td>"
    				+"<td>"+data.gift_count+'/'+data.item_issue_limit+"<br/><span>Stock avail : "+data.available_item_from_stock+"</span></td>"  //assigned qty
    				+"<td> 1</td>"  //issue qty
    				+"<td><input type='hidden' name='gift["+i+"][gift_amount]' value='"+data.gift_unit_price+"'/> INR "+data.gift_unit_price+"</td>"  //unit_price
    				+"<td><div><button class='delete btn btn-danger' onClick='remove_row($(this).closest(\"tr\"),"+data.gift_unit_price+");' name='delete' type='button'><i class='fa fa-trash'></i></button></div></td>"
                +"</tr>";
                $('#gift_chart_tbl_inv tbody').append(row);
	$("#gift_tbl_len").val(table_rows);
     //return true;
	
    
}

function remove_row(curRow,unit_price)
{
    curRow.remove();
    var cur_gift_value = parseInt($('#cur_gift_value').val()) - parseInt(unit_price);
    $('#cur_gift_value').val(cur_gift_value);
}

function set_gift_select()
{
    $('#gift_select_inv').empty();
  
    var id_scheme_account = $('#gift_account').val();
    
    var logged_branch = $('#logged_branch').val();
    
    var branch_select = $('#branch_sel').val();
    
    var id_branch = (logged_branch > 0 ? logged_branch : branch_select);
    
    var id_customer = $('#id_customer').val();
    
   // var ref_gift = $('#ref_gift').val();
    
    if(id_scheme_account > 0 ){
        $.ajax({
        type: 'POST',
		url: base_url+'index.php/admin_manage/get_gifts_from_inv',
		dataType:'json',
		data : {'id_scheme_account':id_scheme_account,'id_branch':id_branch},
		success:function(data)
		{
		    
		    
		 //   alert(data.length);
		 
		 var gifts = data.gifts;
		 
		 console.log(data);
		    
		      $("#gift_select_inv").select2({
    			    placeholder: "Select Gift",
    			    allowClear: true
    			});
                $('#gift_select_inv').append(
                    $("<option></option>")
                        .attr("value", '')						  
                        .text('select gift')
                        .attr("type","hidden")
                );
                    
            if(gifts.length > 0){
                
                
                $('#newGiftIssueDiv, .newGiftIssueDiv,#gift_table_byaccount').css('display','block');
                $('#no_gifts_avail').css('display','none');
                    
    	        $.each(gifts, function (key, data) {	
                    $('#gift_select_inv').append(
                        $("<option></option>")
                            .attr("value", data.id_other_item)						  
                            .text(data.name )
                    );
                    $('#gift_scheme').val(data.id_scheme);
                });
                
                $("#gift_select_inv").select2({
    			    placeholder: "Select Gift",
    			    allowClear: true
    			});
    		  
            }else{
                    //$('#gift_table_byaccount,#gift_table_issued_title').css('display','none');
                    $('#no_gifts_avail').html(data.msg);
                    $('#no_gifts_avail').css('display','block');
                    
                    
                $.toaster({ 
					priority : 'danger', 
					title : 'Warning!', 
					message : data.msg,
					settings: {
						timeout: 5000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
					}
				});
            }
          
	
           
		}
        
    });
    }else{
        
        if($('#branch_select option').length > 1){
            $.toaster({ 
					priority : 'danger', 
					title : 'Warning!', 
					message : ''+"</br>Please fill customer and account data to issue gift...",
					settings: {
						timeout: 5000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
					}
				});
        }
        
    }
 
    
    
}

function clear_gift_form(){
    $('#newGiftIssueDiv').css('display','none');
  /*  $('#no_gifts_avail').css('display','none');
    $('#gift_table_byaccount').css('display','none');*/
    $('#gift_issue_form').trigger("reset");
    $('form#gift_issue_form').trigger("reset"); 
    $("#gift_chart_tbl_inv > tbody").empty();
    $('#otp_data').prop('disabled',false);
    $('#send_otp_gift').prop('disabled',false);
    $('#verify_otp_gift').prop('disabled',true);
    $('#isVerified').val('0');
    $('#gift_otp_btns').css('display','block');
}

function gift_send_otp()
{
    
    var fewSecondsgift = $('#otp_exp').val();
$("div.overlay").css("display", "block");
    var cust_id=$("#id_cus").val();
    var mobile = $.trim($("#txt_mobile").val());
    
    if(mobile.length == 10){
        $.ajax({
        	url:base_url+"index.php/admin_manage/sendotp_gift",
        	type : "POST",
        	data : {mobile:mobile,id_cust:cust_id},
        	//dataType:'json',
        	success : function(result) {	
        	    $("#verify_otp_gift").attr('disabled', false);
        	    
        	    $('#suc_msg_otp').html('OTP sent successfully... Kindly verify within '+fewSecondsgift+' seconds before its expiry...')
        	
                
                setTimeout(function(){
                    $("#suc_msg_otp").html('');
                   
                }, fewSecondsgift*1000);
            }
        });
    }else{
        alert('Enter valid mobile number...');
        $("#txt_mobile").val('');
        $('#send_otp_gift').prop('disabled',false);
    }    
}


function cancel_gift(){
  //  alert(id_gift_issued);
    var content = $('#gift_cancel_form').serializeArray();
    
    console.log(content);
    
    $.ajax({
		type: 'post',
		url: base_url+'index.php/admin_manage/cancel_giftissued',
		dataType:'json',
		data : content,
		success:function(data){
			$(".overlay").css('display','none');
		$('#gift_issue_modal').modal('hide');
			if(data)
			{
			   
			    
			    setTimeout(function(){
                    window.location.reload(true);
                }, 5*1000);

				$.toaster({ 
					priority : 'success', 
					title : 'Gift Cancelled', 
					message : ''+"</br>Gift has been  successfully cancelled",
					settings: {
						timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
					}
				});

			}
			else
			{
				
				$.toaster({ 
					priority : 'warning', 
					title : 'Data Warning', 
					message : ''+"</br>Unable to proceed.... ",
					settings: {
						timeout: 4000 // Time in milliseconds (e.g., 3000ms = 3 seconds)
					}
				});
				
				

			}
		}
	});
}

function show_cancelgift_modal(id_gift_issued)
{
    
	$('#cancel_issued_gift').modal('show');
	$('#cancel_issued_gift')
        .find("input,textarea,select")
        .empty()
        .end();
    $('#deduct_id_gift_issued').val(id_gift_issued);    
        
}           
