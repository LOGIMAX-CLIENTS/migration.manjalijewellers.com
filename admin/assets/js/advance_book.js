var pathArray = window.location.pathname.split( 'php/' );
var ctrl_page = pathArray[1].split('/');
var payment_device_details=[];
var payment_bank_details=[];
var nb_values=[];
var nbpayment=[];
var count =0;
let indianCurrency = Intl.NumberFormat('en-IN');
/* -- generalized api folder path -- */
    var path = pathArray[0].split('/');
    var api_url = window.location.origin + '/' + path[1] +'/api/' ;
/* -- generalized api folder path -- */

function swapDivs() {
            // Get references to the two div elements
            var box1 = document.getElementById('book_amt');
            var box2 = document.getElementById('book_wt');


            // Get their parent node
            var parent = box1.parentNode;

            // Get the reference node (next sibling of box2)
            var referenceNode = box2.nextSibling;

            // Swap the positions of the two div elements
            parent.insertBefore(box2, box1);
            parent.insertBefore(box1, referenceNode);
}


$(document).ready(function(){
    
    $('#swapDivs').on('click',function(e){
       swapDivs(); 
    });
    
   

     // code added by Durga 08-12-2023 starts
    switch(ctrl_page[1])
    {
        // booking list starts 
        case 'bookings_list':
             
                         var date = new Date();
        		    var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1); 
                    var from_date =  firstDay.getDate()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getFullYear();
        			var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
			        $('#account_list1').html(from_date);
                    $('#account_list2').html(to_date);
                    pre_booking_payment_list();
        	         $('#prebook_pay-dt-btn').daterangepicker( {
        
                      ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                      },
                      startDate: moment(),
                      endDate: moment()
                    },
        
                function (start, end) {
        
                  $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        		 
        		   $('#account_list1').html(start.format('DD-MM-YYYY'));
                    $('#account_list2').html(end.format('DD-MM-YYYY'));	
                     $("#mobile_number").val('');
                     pre_booking_payment_list(start.format('DD-MM-YYYY'),end.format('DD-MM-YYYY'))
                  }
                ); 
        break;
        // booking list ends 
        case 'plan_set':
            get_branchnames();
            break;
            //booked account list starts  
        case 'booked_acc_list':
                    var date = new Date();
        		    var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1); 
                    var from_date =  firstDay.getDate()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getFullYear();
        			var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
			        $('#account_list1').html(from_date);
                    $('#account_list2').html(to_date);
                    //pre_booking_account_list(from_date,to_date);
                    pre_booking_account_list();
        	         $('#prebook_pay-dt-btn').daterangepicker( {
        
                      ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                      },
                      startDate: moment(),
                      endDate: moment()
                    },
        
                function (start, end) {
        
                  $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        		  //pre_booking_payment_list(start.format('DD-MM-YYYY'),end.format('DD-MM-YYYY'))
        		 
        		   $('#account_list1').html(start.format('DD-MM-YYYY'));
                    $('#account_list2').html(end.format('DD-MM-YYYY'));	
                    $("#mobile_number").val('');
                     pre_booking_account_list();
                  }
                ); 
        break;
        //booked account list ends  
    }
       
    
     // code added by Durga 08-12-2023 ends

//form code starts....

    if(ctrl_page[0] == 'admin_adv_booking' && ctrl_page[1] == 'plan_set'){
      //  getActiveMetalPurities();
        get_payment_device_details();   
		get_payment_bank_details(); 
		

     //   if(ctrl_page[2] > 0){
            /*get data of the specified plan to edit and set in th form...
            else get default empty record and set in add form.. */

            get_form_data();

     //   }
    }
    
    if(ctrl_page[0] == 'admin_adv_booking' && ctrl_page[1] == 'lock_gold_view'){
        setMetalPlans();
         get_payment_device_details();   
		get_payment_bank_details(); 
    }
    
    

    function get_form_data(){
        my_Date = new Date();
        console.log(ctrl_page[2])
        $("div.overlay").css("display", "block");
        $.ajax({
            url:api_url+ "index.php/advance_booking/planFormData?id_plan="+ctrl_page[2]+"&nocache=" + my_Date.getUTCSeconds(),
            dataType:"JSON",
            type:"GET",
            success:function(data){

                console.log(data);

                set_form_data(data);//line altered by Santhosh
			
                $("div.overlay").css("display", "none");
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });
    }
    
    //function code added by santhosh starts

   function set_form_data(data){
        
        //set fromdata base on id_plan (ctrl_page segment 3rd)....
        console.log(data);
        
        
        $('#gst_setting').val(data.gst_setting);
        $('#gst_type').val(data.gst_type);
        $('#gst').val(data.gst);


        $('#form_type').val("update");
        $('#plan_name').val(data.plan_name);
        $('#plan_code').val(data.plan_code);
        $('#sync_plan_code').val(data.sync_plan_code);
        $('#maturity_value').val(data.maturity_value);
        $('#minimum_val').val(data.minimum_val);
        $('#maximum_val').val(data.maximum_val);
        $('#flx_denomintion').val(data.denomination);
        $('#total_adv_limit_value').val(data.total_adv_limit_value);
        $('#adv_limit_value_online').val(data.adv_limit_value_online);
        $("#login_branch").val(data.accessible_branches);
        
        // Convert the string to an array of integers
        if(data.accessible_branches!=null)
        {
            var selectedBranches = data.accessible_branches.split(",").map(Number);
         // Set pre-selected values
          $('#login_branch_select').val(selectedBranches).trigger('change');
        }
        
         $("#description").val(data.plan_description);
          
        
        var isVisibleValue = data.is_visible;
        //$('#id_plan').val(isVisibleValue);
        $('#id_plan').val(data.id_plan);
        $('input[name="is_visible"]').prop('checked', false);
        $('input[name="is_visible"][value="' + isVisibleValue + '"]').prop('checked', true);

        var payableBy = data.payable_by;
        $('#payable_by').val(payableBy);
        if(payableBy==0){
            $('input[name="payable_by"][value="0"]').prop('checked', true);
        }else{
            $('input[name="payable_by"][value="1"]').prop('checked', true);
        }
        var advLimitAvailable = data.is_adv_limit_available;
       // $('#is_adv_limit_available').val(advLimitAvailable);
        if(advLimitAvailable==0){
            $('input[name="is_adv_limit_available"][value="0"]').prop('checked', true);
        }else{
            $('input[name="is_adv_limit_available"][value="1"]').prop('checked', true);
        }
        //checking advance_limit_div 
        show_hide_advancelimit(advLimitAvailable);
        
        
        var advLimitType = data.adv_limit_type;
      //  $('#adv_limit_type').val(advLimitType);
        if(advLimitType==0){
            $('input[name="adv_limit_type"][value="0"]').prop('checked', true);
        }else{
            $('input[name="adv_limit_type"][value="1"]').prop('checked', true);
        }
        console.log(advLimitType);
        
        
        var commodity = data.id_metal;
        $('#metal_select').val(commodity);
        $('#metal_val').val(commodity);
        $('input[name="id_metal"]').prop('checked', false);
        $('input[name="id_metal"][value="' + commodity + '"]').prop('checked', true);
       
        
        var maturityType = data.maturity_type;
        $('#maturity_type').val(maturityType);
        $('#maturity_type').trigger('change');

        var commodityValue = data.id_metal;
        $('#metal_select').val(commodityValue);
        $('#metal_select').trigger('change');

        var val_purity = data.purity;
        $('#purity').val(val_purity);
        $('#purity_val').val(val_purity);
        $('input[name="purity"]').prop('checked', false);
        $('input[name="purity"][value="' + val_purity + '"]').prop('checked', true);
    
        $('#is_active').bootstrapSwitch();
        var isActiveValue = data.is_active;
        if (isActiveValue != 0) {
            // alert(1);
            $('#is_active').bootstrapSwitch('state', true, true);
        } else {
            // alert(2);
            $('#is_active').bootstrapSwitch('state', false, true);
        }


    }
	//function code added by santhosh ends
	
	
    $('#metal_select').on('change',function(){
	    $('#purity').empty();
	    if(this.value!='' && this.value!=null)
	    {
	         getActiveMetalPurities();
	    }
	   
	});	
	
	 function setMetalPlans(){

        my_Date = new Date();
        $("div.overlay").css("display", "block");
        $.ajax({
            url:api_url+ "index.php/advance_booking/selectMetalPlans?nocache=" + my_Date.getUTCSeconds(),
            dataType:"JSON",
            type:"GET",
            success:function(data){
              
                $.each(data, function (key, plan) {
                    $('#plan').append(
                        $("<option></option>")
                        .attr("value", plan.id_plan)
                        .text(plan.plan_name)
                    );
                });
                
	            selectid=$('#plan_val').val();
			    $('#plan').val(1);
			    
			    $('#plan_val').val(1);
			
                $("div.overlay").css("display", "none");
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });
        
	}
	
	
	
    function getActiveMetalPurities(){

        my_Date = new Date();
        $("div.overlay").css("display", "block");
        $.ajax({
            url:base_url+ "index.php/admin_scheme/getActivePuritiesByMetal?nocache=" + my_Date.getUTCSeconds(),
            data: {"id_metal":$("#metal_select").val()},
            dataType:"JSON",
            type:"POST",
            success:function(data){
                console.log(data);
                $.each(data, function (key, purity) {
                    $('#purity').append(
                        $("<option></option>")
                        .attr("value", purity.id_purity)
                        .text(purity.purity)
                    );
                });
                
	            selectid=$('#purity_val').val();
			    $('#purity').val(selectid);
			
                $("div.overlay").css("display", "none");
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });
	}
	
	$('#pay_submit').on('click',function(){
	    var bal=parseFloat($(".bal_amount").html());
	     
	     if(bal==0)
	     {
	         var form_data=$('#adv_payment_form').serialize();
            my_Date = new Date();
            $.ajax({
            url:api_url+ "index.php/advance_booking/book_byPayment?nocache=" + my_Date.getUTCSeconds(),
            data: form_data,
            dataType:"JSON",
            type:"POST",
            success:function(data){
                console.log(data);
                window.location = base_url+ "index.php/admin_adv_booking/bookings_list?nocache=" + my_Date.getUTCSeconds(),
                
                 $("div.overlay").css("display", "block");
                
                if(data.status == true){
                    $.toaster({ priority : 'success', title : 'Success!', message : data.message});
                }else{
                    $.toaster({ priority : 'danger', title : 'Warning!', message : data.message});
                }
                

                $("div.overlay").css("display", "none");
                },
                error:function(error)  
                {
                    $("div.overlay").css("display", "none"); 
                }	 
            });
	     }
	     else
	     {
	         alert("Amount must be equal to Payment Amount");
	     }
      
        
        
	});    

$('#pay_cancel').on('click',function(){
    $('#payment_form').css('display','none');
});


    $('#submit').on('click',function(){
        var form_type = $('#form_type').val();
        var form_data=$('#adv_plan_form').serialize();
        console.log(form_data);

        my_Date = new Date();
        $("div.overlay").css("display", "block");

        $.ajax({
            url:api_url+ "index.php/advance_booking/plan_submit?nocache=" + my_Date.getUTCSeconds(),
            data: form_data,
            dataType:"JSON",
            type:"POST",
            success:function(data){
                if(data > 0){
                    if(ctrl_page[2]>0)
                    {
                        $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>Plan updated successfully..."}); 
                    }
                    else
                    {
                         $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>Plan created successfully..."}); 
                    }
                   
                }else{
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Unable to proceed..."});
                }
                window.location = base_url+ "index.php/admin_adv_booking/plan_list?nocache=" + my_Date.getUTCSeconds(),

                $("div.overlay").css("display", "none");
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });

    });
//form code ends....      

//plans listing starts...  

    if(ctrl_page[0] == 'admin_adv_booking' && ctrl_page[1] == 'plan_list'){
        getActive_plans_list();
    } 

    function getActive_plans_list(){
        my_Date = new Date();
        $("div.overlay").css("display", "block");
        $.ajax({
            url:api_url+ "index.php/advance_booking/allActivePlans?nocache=" + my_Date.getUTCSeconds(),
            dataType:"JSON",
            type:"GET",
            success:function(data){
              
             /*   $.each(data, function(key,val){
                    $('#data_print').html('<p>'+val.id_plan+'</p>');
                });  */

                set_allActivePlansList(data);//line altered by Santhosh
			
                $("div.overlay").css("display", "none");
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });
    }

     // function code added by Santhosh starts
    function set_allActivePlansList(data){
       
        var plan 	= data;
	 
	   /* console.log(plan);
	    let plan_data = []; // Create a new array

        // Push individual objects into the new array
        plan_data.push(plan.GOLD);
        plan_data.push(plan.SILVER);
        
        console.log(plan_data);*/
	 
	    let plan_data = Object.values(plan);
	    console.log(plan_data);
	    $('#total_plan').text(plan_data.length);
	    if(plan_data.length < 2 )
	    {
	         $("#add_plan").css("display", "block");
	    }
	    else
	    {
	        $("#add_plan").css("display", "none");
	    }
	    
	    var oTable = $('#plan_list').DataTable();
	     oTable.clear().draw();
			  	 if (plan_data!= null && plan_data.length > 0)
			  	  {  	
					  	oTable = $('#plan_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": plan_data,
				                "order":[[0,'desc']],
				                "aoColumns": [{ "mDataProp": "id_plan" },
					                { "mDataProp": "plan_name" },
					                { "mDataProp": "plan_code" },
									{ "mDataProp": function ( row, type, val, meta ){
										  var pay=(row.payable_by == '0'?"Amount":"weight");
										  return pay;
									}
					                },
                                    { "mDataProp": function ( row, type, val, meta ){
                                        //active_url =base_url+"index.php/admin_scheme/customer_status/"+(row.active==1?0:1)+"/"+row.id_customer; 
                                        return "<i class='fa "+(row.is_active==1?'fa-check':'fa-remove')+"' style='color:"+(row.is_active==1?'green':'red')+"'></i></a>"
                                    }
                                    },
					                { "mDataProp": function ( row, type, val, meta ){
										  if(row.is_visible=='0'){
                                            return "Restrick to join";
                                          } else if(row.is_visible=='1'){
                                            return "Show to All";
                                          } else{
                                            return "Show in Admin";
                                          }
									}
					                },
					                						
									{ "mDataProp": function ( row, type, val, meta ){
										id= row.id_plan;
									
					                	    edit_url=( base_url+'/index.php/admin_adv_booking/plan_set/'+id);
											action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
					    '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li></ul></div>';
					                	return action_content;	
													}
									}] 
				            });			  	 	
					  	 }
    }
    
     // function code added by Santhosh ends
//plans listing ends...




   // Multi-mode payment :: STARTS
   //Credit card starts
    $('#new_card').on('click', function(){
    	$("#cardPayAlert span").remove();
    	if(validateCardDetailRow()){
    		create_new_empty_cardpay_row();
    	}else{
    		$("#cardPayAlert").append("<span>Please fill all fields in current row.</span>");
    		$('#cardPayAlert span').delay(20000).fadeOut(500);
    	}
    });
    function get_payment_device_details(){
    	$.ajax({		
    	 	type: 'GET',		
    	 	url : base_url + 'index.php/admin_ret_billing/get_payment_device_details',
    	 	dataType : 'json',		
    	 	success  : function(data){
    		 	payment_device_details = data;
    	 	}	
    	}); 
    }
    function get_payment_bank_details(){
    	$.ajax({		
    	 	type: 'GET',		
    	 	url : base_url + 'index.php/admin_ret_billing/get_bank_acc_details',
    	 	dataType : 'json',		
    	 	success  : function(data){
    		 	payment_bank_details = data;
    	 	}	
    	}); 
    }
    function validateCardDetailRow(){
    	var row_validate = true;
    	$('#card_details > tbody  > tr').each(function(index, tr) {
    		if($(this).find('.card_name').val() == "" || $(this).find('.card_type').val() == "" || $(this).find('.card_no').val() == "" || $(this).find('.card_amt').val() == "" || $(this).find('.ref_no').val() == "" || $(this).find('.id_device').val() == ""){
    			row_validate = false;
    		}
    	});
    	return row_validate;
    }
    function create_new_empty_cardpay_row()
    {
        var card_rows_added = $('#card_details tbody tr').length;
		var card_count=card_rows_added++;
    	var row = "";
    	var device_list='';
    	$.each(payment_device_details, function (pkey, item) 
    	{
    		device_list += "<option value='"+item.id_device+"'>"+item.device_name+"</option>";
    	});
    	console.log(device_list);
    	row += '<tr>'
    				+'<td><select name="card_details[card_name][]" class="card_name"><option value="1">RuPay</option><option value="2">VISA</option><option value="3">Mastro</option><option value="4">Master</option></select></td>'
    				+'<td><select name="card_details[card_type][]" class="card_type"><option value="1">CC</option><option value="2">DC</option></select></td>'
    				+'<td><select class="form-control id_device" name="card_details[id_device][]" style="width: 100px !important;">'+device_list+'</select></td> '
    				+'<td><input type="number" step="any" class="card_no" name="card_details[card_no][]"/></td>'
    				+'<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]"/></td>' 
    			
    				+'<td><input type="text" step="any" class="ref_no"  name="card_details[ref_no][]"/><span class="error" id="ref_span_'+card_count+'" ></span></td>'
    				+'<td><a href="#" onClick="removeCC_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' 
    			+'</tr>';
    	$('#card_details tbody').append(row);
    }
    $(document).on('keyup', '.card_amt', function(e){
    		if(e.which === 13)
    		{
    			e.preventDefault();
    			if(validateCardDetailRow()){
    				create_new_empty_cardpay_row();
    			}else{
    				alert("Please fill required fields");
    			}
    		}
    		calculate_creditCard_Amount();
    	});
    function removeCC_row(curRow)
    {
    	curRow.remove();
    	calculate_creditCard_Amount();
    }
    function calculate_creditCard_Amount()
    {
    	var total_amount=0;
    	var cc_amount=0;
    	var dc_amount=0;
    	card_payment=[];
    	$('#card-detail-modal .modal-body #card_details > tbody  > tr').each(function(index, tr) {
    				if($(this).find('.card_amt').val() != ""){
    					if($(this).find('.card_type').val()==1)
    					{
    						cc_amount+=parseFloat($(this).find('.card_amt').val());
    					}
    					else if($(this).find('.card_type').val()==2)
    					{
    						dc_amount+=parseFloat($(this).find('.card_amt').val());
    					}
    					card_payment.push({'card_name':$(this).find('.card_name').val(),'id_device':$('.id_device').val(),'card_type':$(this).find('.card_type').val(),'card_no':$(this).find('.card_no').val(),'card_amt':$(this).find('.card_amt').val()});
    				}
    		});
    		$('.cc_total_amt').html(parseFloat(cc_amount).toFixed(2));
    		$('.dc_total_amt').html(parseFloat(dc_amount).toFixed(2));
    		$('.cc_total_amount').html(parseFloat(parseFloat(cc_amount)+parseFloat(dc_amount)).toFixed(2));
    }
    $('#add_newcc').on('click',function(){
    		if(validateCardDetailRow()){
    		    
    		    card_payment=[];
    		    $('#card-detail-modal .modal-body #card_details > tbody  > tr').each(function(index, tr) {
    				if($(this).find('.card_amt').val() != ""){
    					card_payment.push({'card_name':$(this).find('.card_name').val(),'id_device':$('.id_device').val(),'card_type':$(this).find('.card_type').val(),'card_no':$(this).find('.card_no').val(),'card_amt':$(this).find('.card_amt').val(),'ref_no':$(this).find('.ref_no').val()});
    				}
    	    	});
    			$('#payment_modes > tbody >tr').each(function(bidx, brow){
    				bill_card_pay_row = $(this);
    				bill_card_pay_row.find('.CC').html($('.cc_total_amt').html());
    				bill_card_pay_row.find('.DC').html($('.dc_total_amt').html());
    				bill_card_pay_row.find('#card_payment').val(card_payment.length>0 ? JSON.stringify(card_payment):'');
    			});
    			$('#card-detail-modal').modal('toggle');
    			$('#edit_payment').css('overflow-y', 'auto');
    			calculatePaymentCost();
    		}else{
    			alert("Please fill required fields");
    		}
    });
    //Credit card ends
    //Chque starts
    $('#new_chq').on('click', function(){
    	$("#chqPayAlert span").remove();
    	if(validateChqDetailRow()){
    		create_new_empty_chqpay_row();
    	}else{
    		$("#chqPayAlert").append("<span>Please fill all fields in current row.</span>");
    		$('#chqPayAlert span').delay(20000).fadeOut(500);
    	}
    });
    function validateChqDetailRow(){
    	var row_validate = true;
    	$('#chq_details > tbody  > tr').each(function(index, tr) {
    		if($(this).find('.bank_name').val() == "" || $(this).find('.bank_branch').val() == "" || $(this).find('.cheque_no').val() == "" || $(this).find('.payment_amount').val() == ""){
    			row_validate = false;
    		}
    	});
    	return row_validate;
    }
    function create_new_empty_chqpay_row()
    {
    	var row = "";
    	row += '<tr>'
    				+'<td><input class="cheque_date" data-date-format="dd-mm-yyyy" name="cheque_details[cheque_date][]" type="text" placeholder="Cheque Date" /></td>'
    				+'<td><input name="cheque_details[bank_name][]" type="text" class="bank_name" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>'
    				+'<td><input name="cheque_details[bank_branch][]" type="text" class="bank_branch" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>'
    				+'<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td>' 
    				+'<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)" /></td>'
    				+'<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td>' 
    				+'<td><a href="#" onClick="removeChq_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
    			+'</tr>';
    	$('#chq_details tbody').append(row);
    	$('#chq_details > tbody').find('tr:last .cheque_date').focus();
    }
    $(document).on('keyup', '.payment_amount', function(e){
    		if(e.which === 13)
    		{
    			e.preventDefault();
    			if(validateChqDetailRow()){
    				create_new_empty_chqpay_row();
    			}else{
    				alert("Please fill required fields");
    			}
    		}
    		calculate_chq_Amount();
    	});
    function removeChq_row(curRow)
    {
    	curRow.remove();
    	calculate_chq_Amount();
    }
    function calculate_chq_Amount()
    {
    	var total_amount=0;
    	var chq_amount=0;
    	chq_payment=[];
    	$('#cheque-detail-modal .modal-body #chq_details > tbody  > tr').each(function(index, tr) {
    				if($(this).find('.payment_amount').val() != ""){
    				    chq_amount+=parseFloat($(this).find('.payment_amount').val());
    					chq_payment.push({'cheque_date':$(this).find('.cheque_date').val(),'cheque_no':$(this).find('.cheque_no').val(),'bank_branch':$(this).find('.bank_branch').val(),'bank_name':$(this).find('.bank_name').val(),'payment_amount':$(this).find('.payment_amount').val(),'bank_IFSC':$(this).find('.bank_IFSC').val()});
    				}
    		});
    		$('.chq_total_amount').html(parseFloat(chq_amount).toFixed(2));
    }
    $('#add_newchq').on('click',function(){
		//lines added by Durga starts here -12.04.2023
		var amount_limit=parseFloat($("#payment_amt").val());
	
		
		
		var payment_amt     =($('#payment_amt').val()!='' ? $('#payment_amt').val():0);
    	var make_pay_cash   =($('#make_pay_cash').val()!='' ? $('#make_pay_cash').val():0);
    	var cc              =($('.CC').html()!='' ? $('.CC').html():0);
    	var dc              =($('.DC').html()!='' ? $('.DC').html():0);
    	var chq             =($('.CHQ').html()!='' ? $('.CHQ').html():0);
    	var NB              =($('.NB').html()!='' ? $('.NB').html():0);
    	var adv_adj_amt     =($('#tot_adv_adj').html()!='' ? $('#tot_adv_adj').html():0);
		
		var total_amount=parseFloat(parseFloat(make_pay_cash)+parseFloat(cc)+parseFloat(dc)+parseFloat(NB)+parseFloat(adv_adj_amt)).toFixed(2);
		var can_pay=parseFloat(amount_limit)-parseFloat(total_amount);
		var total_sum=$(".chq_total_amount").html();
		//lines added by Durga ends here -12.04.2023
    		if(validateChqDetailRow())
			{
				if(total_sum>can_pay)
				{
					alert("Can Pay upto INR "+can_pay);
					$("#cheque_amount").val("");
					$('.chq_total_amount').html(""); 
					
				}
				else if(total_sum>amount_limit)
				{
					//$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Amount Should not exceed INR "+amount_limit});
					alert("Amount Should not exceed INR "+amount_limit);
					$("#cheque_amount").val("");
	
					$('.chq_total_amount').html(""); 
					
				}
				else
				{
					$('#payment_modes > tbody >tr').each(function(bidx, brow){
						bill_card_pay_row = $(this);
						bill_card_pay_row.find('.CHQ').html($('.chq_total_amount').html());
						bill_card_pay_row.find('#chq_payment').val(chq_payment.length>0 ? JSON.stringify(chq_payment):'');
					});
					$('#cheque-detail-modal').modal('toggle');
					calculatePaymentCost();
				}
    		}
			else
			{
    			alert("Please fill required fields");
    		}
    });
    $(document).on('focus', '.cheque_date', function(e){
			
            var row = $(this).closest('tr');
		
    		row.find('.cheque_date').datepicker(
        	{ 
        		format: 'dd-mm-yyyy'
        	});
			
				
    	});
		
	
    //Cheque ends
    //Net banking starts
    $('#new_net_bank').on('click', function(){
    	$("#NetBankAlert span").remove();
    	if(validateNBDetailRow()){
			create_new_empty_net_banking_row();
    	}else{
    		$("#NetBankAlert").append("<span>Please fill all fields in current row.</span>");
    		$('#NetBankAlert span').delay(20000).fadeOut(500);
    	}
    });
    function validateNBDetailRow(){
    	var row_validate = true;
    	$('#net_bank_details > tbody  > tr').each(function(index, tr) {
    		if($(this).find('.nb_type').val() == "" || $(this).find('.ref_no').val() == "" || $(this).find('.amount').val() == "" ){
    			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Please Fill The Required Fields.."});
                row_validate = false;
                return true;
    		}
    		if($(this).find('.nb_type').val()==3)
    		{
    		    if( $(this).find('.id_device').val() == "")
    		    {
    		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Select Device Type.."});
    		        row_validate = false;
    		        return true;
    		    }
    		}else if($(this).find('.nb_type').val()==1 || $(this).find('.nb_type').val()==2)
    		{
    		    if( $(this).find('.id_bank').val() == "")
    		    {
    		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Select The Bank"});
    		        row_validate = false;
    		        return true;
    		    }
    		}
    	});
    	return row_validate;
    }
    function create_new_empty_net_banking_row()
    {
            console.log("succes");
		//var i=nb_values.length-1;
		/*console.log(nb_values[i].nb_type);
		if(nb_values[i].nb_type!=undefined && nb_values[i].nb_type!="")
		{
				nbpayment.push(nb_values[i]);	
		}*/
		/*nb_values=[];
		count += 1;*/
		var devicelist='';
		var banklist='';
		rows_added = $('#net_bank_details tbody tr').length;
		var nb_row_count=rows_added++;
		$.each(payment_device_details, function (pkey, item) 
    	{
    		devicelist += "<option value='"+item.id_device+"'>"+item.device_name+"</option>";
    	});
		$.each(payment_bank_details, function (pkey, item) 
    	{
    		banklist += "<option value='"+item.id_bank+"'>"+item.acc_number+"</option>";
    	});
    	
		var row = "";
    	row += '<tr>'
    		//	+'<td><select name="nb_details[nb_type][]" class="nb_type" ><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>'
    		//	+'<td class="upi_type"><select name="nb_details[nb_bank][]" class="id_bank" style="width:150px;"><option value="">Select Bank</option>'+banklist+'</select></td>'
    			
    		//	+'<td class="device" style="display:none;"><select name="nb_details[nb_device][]" class="id_device" style="width:150px;"><option value="">Select Device</option>'+devicelist+'</select></td>'
	            +'<td><select name="nb_details[nb_type][]" class="nb_type" id="nb_type_'+nb_row_count+'" onchange="setdevice(this.value,'+nb_row_count+')"><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>'
    			+'<td class="upi_type" id="upi_type_'+nb_row_count+'"><select name="nb_details[nb_bank][]"  class="id_bank" style="width:150px;"><option value="">Select Bank</option>'+banklist+'</select></td>'
    			
    			+'<td class="device" id="device_'+nb_row_count+'"  style="display:none;"><select name="nb_details[nb_device][]" class="id_device" style="width:150px;"><option value="">Select Device</option>'+devicelist+'</select></td>'		
				+'<td><input class="form-control  datemask date nb_date" data-date-format="yyyy-mm-dd" name="nb_details[nb_date][]" type="text" placeholder="NB Date" style="width: 100px;" /></td>'
				//+'<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td>'
				+'<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]" /><span class="error" id="nb_ref_span_'+nb_row_count+'"></span></td>'
    			+'<td><input type="number" step="any" class="amount" name="nb_details[amount][]"/></td>'
    			+'<td><a href="#" onClick="removeNb_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
    			+'</tr>';
    	$('#net_bank_details tbody').append(row);
    	$('#net_bank_details > tbody').find('tr:last .cheque_date').focus();
    	var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());		
        $('.nb_date').datepicker({ dateFormat: 'yyyy-mm-dd',endDate:today });
    }
    $(document).on('keyup', '.amount', function(e){
    		if(e.which === 13)
    		{
    			e.preventDefault();
    			if(validateNBDetailRow()){
    				create_new_empty_net_banking_row();
    			}else{
    				alert("Please fill required fields");
    			}
    		}
			$('#net_banking_modal .modal-body #net_bank_details > tbody  > tr').each(function(index, tr) 
			{
						if($(this).find('.amount').val() != "")
						{
							console.log(count);
							nb_values.push({'nb_type':$(this).find('.nb_type'+count).val(),'ref_no':$(this).find('.ref_no').val(),'amount':$(this).find('.amount').val(),'bank':$(this).find('.nb_bank'+count).val(),'device':$(this).find('.nb_device'+count).val(),'acc_no':$('.nb_bank'+count).find(':selected').text()});
						}
			});
			var len = nb_values.length-1;
			console.log(nb_values[len]);
    		calculate_NB_Amount();
    	});
    function removeNb_row(curRow)
    {
		if(curRow.length>0)
		{
			if(nbpayment.length-1>=curRow[0].id)
			{
				nbpayment.splice(curRow[0].id);
			}
			console.log(nbpayment.length-1>=curRow[0].id);
		}
		console.log(nbpayment);
    	curRow.remove();
    	calculate_NB_Amount();
    }
    function calculate_NB_Amount()
    {
    	var total_amount=0;
    	var nb_amount=0;
    	nb_payment=[];
    	$('#net_banking_modal .modal-body #net_bank_details > tbody  > tr').each(function(index, tr) {
					if($(this).find('.amount').val() != ""){
    				    nb_amount+=parseFloat($(this).find('.amount').val());
    					nb_payment.push({'nb_type':$(this).find('.nb_type'+count).val(),'ref_no':$(this).find('.ref_no').val(),'amount':$(this).find('.amount').val(),'bank':$(this).find('.nb_bank'+count).val(),'device':$(this).find('.nb_device'+count).val(),'acc_no':$('.nb_bank'+count).find(':selected').text()});
    				}
    		});
    		$('.nb_total_amount').html(parseFloat(nb_amount).toFixed(2));
    }
    $('#add_newnb').on('click',function(){
    		if(validateNBDetailRow())
    		{
                 var nbpayment=[];
    			 $('#net_banking_modal .modal-body #net_bank_details > tbody  > tr').each(function(index, tr) {
                    if($(this).find('.amount').val() != ""){
                    nbpayment.push({
                                        'nb_type':$(this).find('.nb_type').val(),
                                        'id_bank':$(this).find('.id_bank').val(),
                                        'nb_date':$(this).find('.nb_date').val(),
                                        'id_device':$(this).find('.id_device').val(),
                                        'amount':$(this).find('.amount').val(),
                                        'ref_no':$(this).find('.ref_no').val()
                                    });
                    }
                });
    			$('#payment_modes > tbody >tr').each(function(bidx, brow){
    				bill_card_pay_row = $(this);
    				bill_card_pay_row.find('.NB').html($('.nb_total_amount').html());
    				bill_card_pay_row.find('#nb_payment').val(nbpayment.length>0 ? JSON.stringify(nbpayment):'');
					nb_values=[];
    			});
    			$('#net_banking_modal').modal('toggle');
    			calculatePaymentCost();
    		}else{
    			alert("Please fill required fields");
    		}
			console.log($('#nb_payment').val());
    });
    
 
    
  /*  $(document).on('change','.nb_type',function(e){
    	$('.device').hide();
    	$('.upi_type').hide();
    	if(this.value==3)
    	{
    		$('.device').show();
    	}
    	else if(this.value==2 || this.value==1)
    	{
    		$('.upi_type').show();
    	}
    });*/
    function setdevice(value,index)
	{
		//alert();
		if(value==3)
    	{
    		// $('.device').show();
			// $('.upi_type').hide();
    		$('#device_'+index).css("display", "block");
			$('#upi_type_'+index).css("display", "none");
    	}
    	else if(value==2 || value==1)
    	{
    		//$('#upi_type_'+index).show();
    		$('#upi_type_'+index).css("display", "block");
			$('#device_'+index).css("display", "none");
    		// $('.upi_type').show();
			// $('.device').hide();
    	}
	}
	function check_ref_no(inputelement,value,index)
	{
		var inputName = inputelement.name;
				var parts=inputName.split('_');
				var input_type=parts[0];
				
		if(isValid(value))
		{
			my_Date = new Date();
    	$.ajax({
            url: base_url+'index.php/admin_payment/get_ref_num/?nocache=' + my_Date.getUTCSeconds(),             
            dataType: "json", 
            method: "POST", 
            data: {'value':value},
            success: function (data) 
			{
				console.log(data);
				

				if(data>0)
				{
					if(input_type=='nb')
					{
						$("#nb_ref_span_"+index).text("Ref no already exist");
					}
					else
					{
						$("#ref_span_"+index).text("Ref no already exist");
					}
					
				}
				else
				{
					if(input_type=='nb')
					{
						$("#nb_ref_span_"+index).text("");
					}
					else
					{
						$("#ref_span_"+index).text("");
					}
				}
				
			}
		});      
		}
		 else
		 {
			if(input_type=='nb')
					{
						$("#nb_ref_span_"+index).text("");
					}
					else
					{
						$("#ref_span_"+index).text("");
					}
		 }    
			
		
		
	}
    $('.net_banking_modal').on('click',function()
	{
		if($('.nb_type'+count).val()==1 || $('.nb_type'+count).val()==2)
		{
			$('.nb_bank'+count).show();
			$('.nb_device'+count).hide();
		}
		else if($('#nb_type'+count).val()==3)
		{
			$('.nb_device'+count).show();
			$('.nb_bank'+count).hide();
		}
	});
	$('.nb_type'+count).on('change',function()
	{
		if(this.value==1 || this.value==2)
		{
			$('.nb_bank'+count).show();
			$('.nb_device'+count).hide();
		}
		else if(this.value==3)
		{
			$('.nb_device'+count).show();
			$('.nb_bank'+count).hide();
		}
	});
	function showhide(event,count) 
	{
		if(event.target.value==1 || event.target.value==2)
		{
			$('.nb_bank'+count).show();
			$('.nb_device'+count).hide();
		}
		else if(event.target.value==3)
		{
			$('.nb_device'+count).show();
			$('.nb_bank'+count).hide();
		}
	}
	$('#card_detail_modal').on('click',function()
	{
		
       if(validateCardDetailRow())
	   {
	    //	alert();
			
            if($('#card_details > tbody > tr').length==0)
            {
                create_new_empty_cardpay_row();
            }
    	}
	});
	$('#netbankmodal').on('click',function()
	{
       if(validateNBDetailRow()){
            if($('#net_bank_details > tbody > tr').length==0)
            {
                create_new_empty_net_banking_row();
            }
    	}
	
	});
    //Net banking ends
    
    
      //Advance starts     #EP
    function get_advance_details()
    {
    	$('#bill_adv_adj > tbody').empty();
    	my_Date = new Date();
    	$.ajax({
            url: base_url+'index.php/admin_ret_billing/get_advance_details/?nocache=' + my_Date.getUTCSeconds(),             
            dataType: "json", 
            method: "POST", 
           // data: {'bill_cus_id':$('#id_customer').val(),'id_payment':$('#id_payment').val()},
            data: {'bill_cus_id':1,'id_payment':1},
            success: function (data) {
                        
                        rec_id_ret_wallet = data[0].id_ret_wallet;
                        total_sum_adjusted_bill_amount = 0;
                        $.each(data,function(key,items){
                            total_sum_adjusted_bill_amount += parseInt((parseFloat(items.amount).toFixed(2)));
                        });
                        
                        console.log(total_sum_adjusted_bill_amount);
                        
                        if(total_sum_adjusted_bill_amount>0)
                        {
                            $('#adv-adj-confirm-add').modal('show');
                            var row="";
                            var html='';
                            var metal_rate=$('.per-grm-sale-value').html();
                            
                            var weight_amt=parseFloat(data.weight*data.rate_per_gram);
                            
                            $('#id_ret_wallet').val(data.id_ret_wallet);
                            
                            //onclick="handleCheckboxClick(this)" ---> added by Durga 19.05.2023
                            $.each(data,function(key,items){
                                html+='<tr>'
                                +'<td><input type="checkbox" class="id_issue_receipt" onclick="handleCheckboxClick(this)"  name="adv_adj[id_issue_receipt]" value="'+items.id_issue_receipt+'"><input type="hidden" class="id_ret_wallet" value="'+items.id_ret_wallet+'"></td>'
                                +'<td><div class="adv_bill_no" value="'+items.bill_no+'">'+items.bill_no+'</div></td>'
                                +'<td><div class="advance_amount" >'+items.amount+'</div></td>'
                                +'<td><input type="number" class="form-control adj_amount" name="adv_adj[adj_amount]" ></td>'
                                +'<td><input type="number" class="form-control blc_amount" name="adv_adj[blc_amount]" readonly></td>'
                                +'</tr>'; 
                            });
                            
                            $('.total_adv_amt').html(parseFloat(total_sum_adjusted_bill_amount).toFixed(2));
                            $('.total_bill_amt').html(parseFloat($('#payment_amt').val()).toFixed(2));
                            
                            $('#bill_adv_adj > tbody').append(html);
                            
                        //    $('#edit_bill_adv_adj > tbody').append(html);
                        }
                        else
                        {
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Your Wallet Amount is 0"});
                        }
    	        }
         });
    }
    
    function get_edit_advance_details()
    {
    	$('#bill_adv_adj > tbody').empty();
    	my_Date = new Date();
    	$.ajax({
            url: base_url+'index.php/admin_ret_billing/get_advance_details/?nocache=' + my_Date.getUTCSeconds(),             
            dataType: "json", 
            method: "POST", 
            data: {'bill_cus_id':$('#id_customer').val()},
            success: function (data) {
                
                       // $('#edit_bill_adv_adj > tr').remove();
                        
                        rec_id_ret_wallet = data[0].id_ret_wallet;
                        total_sum_adjusted_bill_amount = 0;
                        $.each(data,function(key,items){
                            total_sum_adjusted_bill_amount += parseInt((parseFloat(items.amount).toFixed(2)));
                        });
                        
                        console.log(total_sum_adjusted_bill_amount);
                        
                        
                        if(total_sum_adjusted_bill_amount>0)
                        {
                            $('#adv-adj-confirm-add').modal('show');
                            var row="";
                            var html='';
                            var metal_rate=$('.per-grm-sale-value').html();
                            
                            var weight_amt=parseFloat(data.weight*data.rate_per_gram);
                            
                            $('#id_ret_wallet').val(data.id_ret_wallet);
                            
                            $.each(data,function(key,items){
                                html+='<tr>'
                                +'<td><input type="checkbox" class="id_issue_receipt"  name="adv_adj[id_issue_receipt]" value="'+items.id_issue_receipt+'"><input type="hidden" class="id_ret_wallet" value="'+items.id_ret_wallet+'"></td>'
                                +'<td><div class="adv_bill_no" value="'+items.bill_no+'">'+items.bill_no+'</div></td>'
                                +'<td><div class="advance_amount" >'+items.total_amount+'</div></td>'
                                +'<td><input type="number" class="form-control adj_amount" name="adv_adj[adj_amount]" value="" ></td>'
                                +'<td><input type="number" class="form-control blc_amount" name="adv_adj[blc_amount]" value="'+parseFloat(total_sum_adjusted_bill_amount).toFixed(2)+'" readonly></td>'
                                +'</tr>'; 
                            });
                            
                            $('.total_adv_amt').html(parseFloat(total_sum_adjusted_bill_amount).toFixed(2));
                            $('.total_bill_amt').html(parseFloat($('#payment_amt').val()).toFixed(2));
                            
                            $('#edit_bill_adv_adj > tbody').append(html);
                        }
                        else
                        {
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Your Wallet Amount is 0"});
                        }
    	        }
         });
    }
     
    $(document).on('change',".id_issue_receipt", function(e){
    	 	var row = $(this).closest('tr'); 
    	 	var advance_amount=row.find('.advance_amount').html();
    	 	if(row.find('.id_issue_receipt').is(':checked'))
    	 	{
    	 	    row.find('.adj_amount').val(parseFloat(advance_amount).toFixed(2));
    	 	}else{
    	 	    row.find('.adj_amount').val(0);
    	 	}
            calculate_advance_adjust_amount();
    });
    function calculate_advance_adjust_amount()
    {
         adjusted_amt=0;
        balance_amt=0;
        $('#adv-adj-confirm-add .modal-body #bill_adv_adj > tbody  > tr').each(function(index, tr) {
            var row = $(this).closest('tr'); 
            if(row.find('.id_issue_receipt').is(':checked'))
            {
                adjusted_amt+=(isNaN(row.find('.adj_amount').val()) || (row.find('.adj_amount').val()=='') ? 0 :parseFloat(row.find('.adj_amount').val()));
                balance_amt+=(isNaN(row.find('.blc_amount').val()) || (row.find('.blc_amount').val()=='') ?0: parseFloat(row.find('.blc_amount').val()));
            }
        });
        $('.total_adj_adv_amt').html(parseFloat(adjusted_amt).toFixed(2));
        $('.total_blc_amt').html(parseFloat(balance_amt).toFixed(2));
    }
    $(document).on('keyup',".adj_amount", function(e){
        var row = $(this).closest('tr'); 
        var advance_amount=parseFloat(row.find('.advance_amount').html());
        if(row.find('.adj_amount').val()!='' && row.find('.adj_amount').val()>0)
        {
            row.find('.id_issue_receipt').prop('checked',true);
            if(parseFloat(advance_amount)<parseFloat(row.find('.adj_amount').val()))
            {
                row.find('.id_issue_receipt').prop('checked',false);
                row.find('.adj_amount').val(0);
                row.find('.blc_amount').val(0);
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Your Receipt Amount Exceed"});
            }else{
                row.find('.id_issue_receipt').prop('checked',true);
                row.find('.blc_amount').val(parseFloat(parseFloat(advance_amount)-parseFloat(row.find('.adj_amount').val())).toFixed(2));
            }
        }else{
           row.find('.id_issue_receipt').prop('checked',false);
           row.find('.adj_amount').val(0);
           row.find('.blc_amount').val(0);
        }
        
        calculate_advance_adjust_amount();
    });
    $('input[type=radio][name="receipt[receipt_as]"]').change(function(){
    	if(this.value==1)
    	{
    		$('#esti_no').prop('disabled',true);
    	}else{
    		$('#esti_no').prop('disabled',false);
    	}
    });
    $('input[type=radio][name="store_receipt_as"]').change(function() {
    	var metal_rate=$('.per-grm-sale-value').html();
    	if(adv_adj_details.length>0)
    	{
    		adv_adj_details[0].store_receipt_as=this.value;
    		if(this.value==1)
    		{
    			adv_adj_details[0].wallet_blc=$('.excess_amt').html();
    		}else{
    			adv_adj_details[0].wallet_blc=parseFloat($('.excess_amt').html()/metal_rate).toFixed(4);
    		}
    	}
    	console.log(adv_adj_details);
    });
  
    
    
       $('#save_receipt_adv_adj').on('click',function(e){
        
        var total_adj_adv_amt=$('.total_adj_adv_amt').html();
        var total_bill_amt=$('.total_bill_amt').html();
        if(parseFloat(total_bill_amt)<parseFloat(total_adj_adv_amt))
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+"Please Enter The Valid Adjusted Amount"});
        }else{
            var advance_adj=[];
        	$('#adv-adj-confirm-add .modal-body #bill_adv_adj > tbody  > tr').each(function(index, tr) {
        	if($(this).find('.id_issue_receipt').is(':checked')){
        		advance_adj.push({
        		    'id_issue_receipt':$(this).find('.id_issue_receipt').val(),
        		    'id_ret_wallet':$(this).find('.id_ret_wallet').val(),
        		    'adj_amount':$(this).find('.adj_amount').val(),
        		    'blc_amount':$(this).find('.blc_amount').val(),
        		});
        	}
            });
        
        	$('#payment_modes > tbody >tr').each(function(bidx, brow){
        		bill_card_pay_row = $(this);
        		bill_card_pay_row.find('#tot_adv_adj').html($('.total_adj_adv_amt').html());
        		$('#advance_muliple_receipt').val(advance_adj.length>0 ? JSON.stringify(advance_adj):'');
    			bal_excss_amt = parseInt(parseInt(total_sum_adjusted_bill_amount).toFixed(2) - parseInt(adjusted_amt).toFixed(2)).toFixed(2);
    			$('#excess_adv_amt').val(bal_excss_amt);
        	});
        	$('#adv-adj-confirm-add').modal('toggle');
        	calculatePaymentCost();
        }
    });
    
    $('#close_add_adj').on('click',function(e){
        $('#adv-adj-confirm-add .modal-body').find('#bill_adv_adj tbody').empty();
      $('.tot_bill_amt').html('');
      $('.adjusted_amt').html('');
      $('.excess_amt').html('');
    });
    
     $( "#proceed" ).on( "click", function(event) 
    {
        var amt = parseInt($('#payable').val());
        var max_payable_amount = parseInt($('input[name=max_payable_amount]').val());
        var min_payable_amount = parseInt($('input[name=min_payable_amount]').val());
  
        if((amt >= min_payable_amount) && (amt <= max_payable_amount)){   //50k >= 10k && 50k <= 3l
            $('#pay_error').html('');
            $('#payment_amt').val(amt);
            $('#payment_modes').css("pointer-events", 'all');
            $('#payment_modes').css("opacity", '0.9');
           
        }else{
            $('#pay_error').html('You can pay upto minimum INR '+min_payable_amount+' and maximum INR '+max_payable_amount+' only.');
            $('#payment_amt').val('');
            $('#payment_modes').css("pointer-events", 'none');
            $('#payment_modes').css("opacity", '0.4');
        }
        
        
        
    });
                
                
    //mobile number auto complete function starts here 
    
   $( "#mobile_number" ).autocomplete({
      source: function( request, response ) 
	  {
	     
      	var mobile=$( "#mobile_number" ).val();
		var id_scheme=$("#id_scheme").val(); 
        $.ajax({
	 	 url:  base_url+'index.php/admin_customer/ajax_get_customers_list',
          dataType: "json",
          type: 'POST',
         data:{'mobile':mobile,'id_scheme':id_scheme},
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
                        cus_name:entry.firstname,
                        phone : entry.mobile,
                        email : entry.email
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
		select: function(e, i)
		{
		    if(ctrl_page[1]=='lock_gold_view')
		    {
		        
		        console.log(i);
		        	e.preventDefault();
                		$("#mobile_number" ).val(i.item.label);
                		$("#id_customer").val(i.item.value);
                		$("#cus_name").val(i.item.cus_name);
                		$("#phone").val(i.item.phone);
                		$("#email").val(i.item.email);
                		//$("#id_scheme_account").val(i.item.id_scheme_account);
                		$('.overlay').css('display','block');
                		$('#booking_select').empty();
                		my_Date = new Date();
                		var id_customer=$('#id_customer').val();
                		
                		var cus_mobile = $('#customer_mobile').val();
                		//var scheme_acc_number=0;
                		if($('#id_customer').val()!='')
                		{
                		    $('#payment-detail-box').empty();
                            $('#payment-detail-box').css('display','none');
                            $('#ledger-box').empty();
                            $('#ledger-box').css('display','none');
                		     load_accounts();
                		}
                		else
                		{
                		$("#booking_select").select2("val",'');
                			$('#booking_select').empty();
                			
                			$('#mobile_number').val('');
                			$('#id_customer').val('');
                			$('#booking_id').val('');
                			$('#payment-detail-box').empty();
                            $('#payment-detail-box').css('display','none');
                            $('#ledger-box').empty();
                            $('#ledger-box').css('display','none');
                			alert('Invalid Details');
                		}
            }
            if(ctrl_page[1]=='bookings_list')
            {
                e.preventDefault();
            	$("#mobile_number" ).val(i.item.label);
            	$("#id_customer").val(i.item.value);
            	//$("#id_scheme_account").val(i.item.id_scheme_account);
            	$('.overlay').css('display','block');
            	
            	if($('#id_customer').val()!='')
            	{
            					var from_date = $('#account_list1').text();
            					var to_date  = $('#account_list2').text();
            					var id_customer=$('#id_customer').val();	
            					var id_branch=$('#id_branch').val();	
            					pre_booking_payment_list(from_date,to_date,id_branch,id_customer);
            	}
            	else
            	{
            	    $("#id_customer").val('');
            	}

            }
            if(ctrl_page[1]=='booked_acc_list')
            {
                e.preventDefault();
            	$("#mobile_number" ).val(i.item.label);
            	$("#id_customer").val(i.item.value);
            	//$("#id_scheme_account").val(i.item.id_scheme_account);
            	$('.overlay').css('display','block');
            	
            	if($('#id_customer').val()!='')
            	{
            					var from_date = $('#account_list1').text();
            					var to_date  = $('#account_list2').text();
            					var id_customer=$('#id_customer').val();	
            					var id_branch=$('#id_branch').val();	
            					$('#payment-detail-box').empty();
                                $('#payment-detail-box').css('display','none');
                                $('#ledger-box').empty();
                                $('#ledger-box').css('display','none');
            					pre_booking_account_list();
            	}
            	else
            	{
            	     $("#id_customer").val('');
            	}

            }
	
		},
		response: function(e, i) {
            // ui.content is the array that's about to be sent to the response callback.
            if (i.content.length === 0) {
               alert('Please Enter a valid Number');
               $('#mobile_number').val('');
                $("#id_customer").val('');
                $('#payment-detail-box').empty();
                $('#payment-detail-box').css('display','none');
                $('#ledger-box').empty();
                $('#ledger-box').css('display','none');
            } 
        },
		focus: function(e, i) {
        e.preventDefault();
        $("#mobile_number").val(i.item.label);
		}
		
    });
	
    //mobile number auto complete function ends here 
    
    //booking select change function starts
    
    $('#booking_select').on('change',function(e){
        if(this.value!='' && this.value!=null)
        {
            	$('.overlay').css('display','block');
            	    $('#payment-detail-box').empty();
                    $('#payment-detail-box').css('display','none');
                    $('#ledger-box').empty();
                    $('#ledger-box').css('display','none');
          select_booking(this.value);
        }
        else
        {
                    $('#payment-detail-box').empty();
                    $('#payment-detail-box').css('display','none');
                    $('#ledger-box').empty();
                    $('#ledger-box').css('display','none');
        }
    });
    //booking select change function ends
    
    
    
    $('#plan').on('change',function(e){
        if(this.value!='' && this.value!=null)
        {
            $('#plan_val').val(this.value);
            set_dataFor_Booking(this.value);
            load_accounts();
        }
    });
    
    function set_dataFor_Booking(id_plan){
        $.ajax({
		        data:{'id_plan':id_plan},
                url:api_url+ "index.php/advance_booking/planFormData?nocache=" + my_Date.getUTCSeconds(),
                dataType:"JSON",
                type:"GET",
                success:function(data){

                   console.log(data);
                   
                    $('#gst_setting').val(data.gst_setting);
                    $('#gst_type').val(data.gst_type);
                    $('#gst').val(data.gst);
        
                   $('#plan_val').val(data.id_plan);
                   
                   $('#booking_rate').val(data.metal_rate);
                   $('input[name=plan_minimum]').val(data.minimum_val);
                   $('input[name=plan_maximum]').val(data.maximum_val);
                   $('#booking_weight').val(''); 
                   $('#booking_amount').val(''); 
                   $('#create_acc_content').html('');
                   $('#plan_min_max').html('Min: '+data.minimum_val+' - Max: '+data.maximum_val);
        
                }
        });
    }
    
    $('#add_booking').on('click',function(e){
        set_dataFor_Booking($('#plan_val').val());
        $('#booking_form').css('display','block');
        $('#payment_form').css('display','none');
    });
    
    $('#create_booking').on('click',function(e){
        
        var booking = [];
        
        
        
        booking = {'id_customer' : $('#id_customer').val(), 
                    'id_plan' : $('#plan_val').val(), 
                     'id_branch' : $('#id_branch').val(),
                     'booking_amount' : $('#booking_amount').val(),
                     'booking_rate' :  $('#booking_rate').val(),
                     'booking_weight' : $('#booking_weight').val(),
                     'source_type' : 'ADMIN',
                     'cus_name' : $('#booking_name').val(),
                     'gst_setting':$('#gst_setting').val(),
                     'gst_type':$('#gst_type').val(),
                     'gst':$('#gst').val(),
            
        };
       
        my_Date = new Date();
        $("div.overlay").css("display", "block");
        $.ajax({
            url:api_url+ "index.php/advance_booking/create_booking?nocache=" + my_Date.getUTCSeconds(),
            data: booking,
            dataType:"JSON",
            type:"POST",
            success:function(data){
                select_booking(data.pay_content.booking_id);
			
                $("div.overlay").css("display", "none");
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });
        
    });
    
    $('#booking_amount').on('change',function(e){
        $('#booking_weight').prop('readonly', true);
        $('#create_acc_content').html('');
        var min = parseInt($('#plan_minimum').val());
        var max = parseInt($('#plan_maximum').val());
        var metal_rate =  $('#booking_rate').val();
        var amount = parseInt(this.value);
        var weight = parseFloat(amount/metal_rate).toFixed(3);
        var plan_val = $('#plan_val').val();

        //$('#create_acc_content').html(amount+' min '+min+' max '+max);
        if(plan_val > 0 && metal_rate > 0){
            if((amount >= min) && (amount <= max)){
               $('#booking_weight').val(weight); 
               $('#create_acc_content').html('');
               $('#create_booking').css('display','block');
            }else{
                $('#create_acc_content').html('You can book upto minimum INR '+min+' and maximum INR '+max+' only.');
                $('#booking_weight').val(''); 
                $('#booking_amount').val(''); 
                $('#create_booking').css('display','none');
            }
        }else{
            $('#create_acc_content').html('Select metal to create booking....');
            $('#booking_weight').val(''); 
            $('#booking_amount').val(''); 
            $('#create_booking').css('display','none');
        }
        
    });
    
    $('#booking_weight').on('change',function(e){
        $('#booking_amount').prop('readonly', true);
        $('#create_acc_content').html('');
        var min = parseInt($('#plan_minimum').val());
        var max = parseInt($('#plan_maximum').val());
        var metal_rate =  $('#booking_rate').val();
        var weight = parseFloat((this.value),3);
        var amount = parseInt((weight*metal_rate));
        
        
        var plan_val = $('#plan_val').val();

        //$('#create_acc_content').html(amount+' min '+min+' max '+max);
        if(plan_val > 0 && metal_rate > 0){
            if((amount >= min) && (amount <= max)){
               $('#booking_amount').val(amount); 
               $('#create_acc_content').html('');
               $('#create_booking').css('display','block');
            }else{
                $('#create_acc_content').html('You can book upto minimum INR '+min+' and maximum INR '+max+' only.');
                $('#booking_weight').val(''); 
                $('#booking_amount').val(''); 
                $('#create_booking').css('display','none');
            }
        }else{
            $('#create_acc_content').html('Select metal to create booking....');
            $('#booking_weight').val(''); 
            $('#booking_amount').val(''); 
            $('#create_booking').css('display','none');
        }
        
    });
    
    
    
    
    function empty_AllData()
    {
        $("#advance_paid").val('');
                   $("#advance_value").val('');
                   $("#balance_adv_amt").val('');
                   $("#content_box").text('');
                   $("#balance_amt").val('');
                   $("#balance_paid").val('');
                   $("#booking_amount").val('');
                   $("#booking_date").val('');
                   $("#booking_id").val('');
                   $("#booking_name").val('');
                   $("#booking_number").val('');
                   $("#booking_rate").val('');
                   $("#booking_status").val('');
                   $("#booking_weight").val('');
                   $("#can_pay").text('');
                   $("#eligible_on").val('');
                   $("#id_metal").val('');
                   $("#max_payable_amount").val('');
                   $("#max_payable_amount_span").text('');
                   $("#metal").val('');
                   $("#min_payable_amount").val('');
                   $("#min_payable_amount_span").text('');
                   $("#online_advance_amt").val('');
                   $("#payable").val('');
                   //$("#status").val(data.status);
                   $("#total_advance_amt").val('');
                   $("#total_paid_amount").val('');
                   $("#trans_type").text('');
    }
    


});


 //Branch select on change function starts 
    $('#branch_select').select2().on("change", function(e)
    { 
	    console.log(this.value);
		if(this.value!="")
		{   
		    switch(ctrl_page[1])
		    {
		        //booking list starts Gopal Code
		        case 'bookings_list':
		                 $("#mobile_number").val('');
		            	pre_booking_payment_list('','',this.value);
		            	break;
		           //booking list ends Gopal Code
		        case 'booked_acc_list':
		                $("#mobile_number").val('');
		            	pre_booking_account_list();
		            	break;
		     
		    }
		
		
		}

	});
	
	 //Branch select on change function ends 
	 
	//Gopal coded function starts
	 
	 function pre_booking_payment_list(from_date="",to_date="",id_branch="",id_customer="")
        {
            var mob=$("#mobile_number").val();
            
            if(mob!='')
            {
              var id_customer=$('#id_customer').val();  
              
              var from_date= $('#account_list1').html();
                var to_date=$('#account_list2').html();
            }
             else
             {
               var from_date= $('#account_list1').html();
                var to_date=$('#account_list2').html();
                var id_branch=$('#branch_select').val();
             
             }
             
            if(from_date!='' && to_date!='')
            {
                $("#bookings_range").text(from_date+" To "+to_date);
            }
            else
            {
                $("#bookings_range").text("Select Date")
            }
           
        	my_Date = new Date();
        	
        	 $("div.overlay").css("display", "block"); 
        	$.ajax({
        	            
        			  url:api_url+"index.php/advance_booking/pre_booking_payment?nocache=" + my_Date.getUTCSeconds(),
        			 data: (from_date !='' || id_branch!='' || to_date !=''  || id_customer? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch,'id_customer':id_customer}: ''),
        			 dataType:"JSON",
        			 type:"GET",
        			 success:function(data){
        			     console.log(data);
        			   			set_prebooking_pay_list(data);
        			   			 $("div.overlay").css("display", "none"); 
        					  },
        					  error:function(error)  
        					  {
        						 $("div.overlay").css("display", "none"); 
        					  }	 
        			      });
        }
        
        
        function set_prebooking_pay_list(data)	
            {
              
               var oTable = $('#pre_bookingpay_list').DataTable();
                //    $("#total_customers").text(customer.length);
               
            	 oTable.clear().draw();
               	 if (data!= null && data.length > 0)
            	 {
            		 $("#total_bookings").text(data.length);
            	 	oTable = $('#pre_bookingpay_list').dataTable({
            				                "bDestroy": true,
            				                "bInfo": true,
            				                "bFilter": true,
            				                "bSort": true,
            				                
            				                 "dom": 'lBfrtip',
                       		                "buttons" : ['excel','print','colvis'],
            						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            				                "aaData": data,
            				                "order": [[ 0, "desc" ]],
            				                 "columnDefs": 
															 [
																 {
																	 targets: [0,1,2,3,4,5,7,8,9,10,11,12,13,14], 
																	 className: 'dt-left'
																 },
																 {
																	 targets: [6], 
																	 className: 'dt-right'
																 },
							 
			 
															 ],
            				               "aoColumns": [
            				
            					               { "mDataProp": "id_payment" },
            					                { "mDataProp": "cus_name" },
            					                { "mDataProp": "mobile" },
            					                { "mDataProp": "receipt_number" },
            					                { "mDataProp": "booking_number" },
            					                
            					                { "mDataProp": "date_payment" },
            					                { "mDataProp": "payment_amount" },
            					                { "mDataProp": "payment_mode" },
            					                { "mDataProp": "payment_type" },
            					                { "mDataProp": "payment_status" },
            					                { "mDataProp": "payment_through" },
            					                { "mDataProp": "payment_ref_number" },
            					                { "mDataProp": "branch" },
            					                { "mDataProp": "emp_name" },
            					                { "mDataProp": "remarks" },
            					                { "mDataProp": function ( row, type, val, meta ) {
            					                    var action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>'+
            					                    '<ul class="dropdown-menu">'+
					    
                            					    '<li><a href="#" class="btn-edit"><i class="fa fa-print" ></i>Receipt</a></li>'+
                            					    '</ul></div>';
					                	            return action_content;
            					                }}
            					               ] 
            				            });	
            	 }  
            }
	 //Gopal coded function ends
	 
	 // function to load multi select branch dropdown 
	 function get_branchnames(){	
         	//$(".overlay").css('display','block');	
         	$.ajax({		
             	type: 'GET',		
             	url: base_url+'index.php/admin_employee/branchname_list',		
             	dataType:'json',		
             	success:function(data){				 
            	 	var id_branch =  $('#login_branch').val();	
            	 	
            	 	
        	 	        	$("#login_branch_select").append(						
                    	 	$("<option></option>")						
                    	 	.attr("value", 0)						  						  
                    	 	.text('All' )
                    	 	);
            	 	
            	 	$.each(data.branch, function (key, item) {					  				  			   		
                	 	$("#login_branch_select").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_branch)						  						  
                	 	.text(item.name )						  					
                	 	);			   											
                 	});						
                 	$("#login_branch_select").select2({			    
                	 	placeholder: "Select Branch",			    
                	 	allowClear: true		    
                 	});

					var ar = $('#sel_br').data('sel_br');
					console.log(ar);
					
					if($('#login_branch_select').length)
					{
					    $('#login_branch_select').select2('val',ar);				
					}
					
							
                 	//$("#login_branch_select").select2("val",(id_branch!='' && id_branch>0?id_branch:''));	 
                 	$(".overlay").css("display", "none");
            	 		
             	}	
            }); 
        }
        
         $("#login_branch_select").change(function() {
			 var data = $("#login_branch_select").select2('data');		
			 selectedValue = $(this).val(); 		
			 $("#login_branch").val(selectedValue);
		}) ;	
        
        //Advance limit radio change event  starts
        
        $('input[type="radio"][name="is_adv_limit_available"]').change(function() {
        if ($(this).is(':checked')) {
           var selected_value=$(this).val();
          show_hide_advancelimit(selected_value);
            // Perform actions based on the selected option
        }
        });
        //Advance limit radio change event  starts
        
        //showing or hideing Advance limit block based on option selected starts
        function show_hide_advancelimit(selected_value)
        {
            console.log(selected_value);
             if(selected_value==1)
           {
                $('#advance_limit_div').css("pointer-events", 'all');
                $('#advance_limit_div').css("opacity", '0.9');
           }
           else
           {
               $('#advance_limit_div').css("pointer-events", 'none');
                $('#advance_limit_div').css("opacity", '0.4');
           }
        }
         //showing or hiding Advance limit block based on option selected starts
         
         
         $('input[type="radio"][name="adv_limit_type"]').change(function() {
            
            if ($(this).is(':checked')) 
            {
                    var advLimitType=$('input[type="radio"][name="adv_limit_type"]:checked').val();
           
                    if(advLimitType==0){
                       
                    $('input[name="adv_limit_type"][value="0"]').prop('checked', true);
                    }else{
                        
                        $('input[name="adv_limit_type"][value="1"]').prop('checked', true);
                    }
            checkMax();
            checkMaxOnline();
            // Perform actions based on the selected option
            }
        });
         
         $('#total_adv_limit_value').keyup(function(event) {
            
            checkMax();
         });
         $('#adv_limit_value_online').keyup(function(event) {
            
            checkMaxOnline();
         });
         
         function checkMax()
         {
             var adv_limit_type=$('input[type="radio"][name="adv_limit_type"]:checked').val();
             var adv_limit_avail=parseFloat($("#is_adv_limit_available").val());
             var max_val=parseFloat($("#maximum_val").val());
             var given_value=parseFloat($("#total_adv_limit_value").val());
           
             if(adv_limit_avail==1)
             {
                 
                  if(adv_limit_type==0)//amount type
                  {
                      console.log(given_value);
                      if(given_value > max_val)
                      {
                          alert("Amount must be less than "+max_val);
                          $("#total_adv_limit_value").val('');
                           $("#adv_limit_value_online").val('');
                      }
                  }
                  else if(adv_limit_type==1)//percent type
                  {
                     var given_amt=parseFloat(max_val*given_value/100);
                    
                     if(given_amt > max_val)
                      {
                          alert("Amount must be less than "+max_val);
                          $("#total_adv_limit_value").val('');
                           $("#adv_limit_value_online").val('');
                      }
                     
                  }
             }
         }
         function checkMaxOnline()
         {
             var adv_limit_type=$('input[type="radio"][name="adv_limit_type"]:checked').val();
             var adv_limit_avail=parseFloat($("#is_adv_limit_available").val());
             var max_val=parseFloat($("#total_adv_limit_value").val());
             var given_value=parseFloat($("#adv_limit_value_online").val());
            var amt=parseFloat($("#maximum_val").val());
             if(adv_limit_avail==1)
             {
                 
                  if(adv_limit_type==0)//amount type
                  {
                      console.log(given_value);
                      if(given_value > max_val)
                      {
                          alert("Amount must be less than "+max_val);
                          $("#adv_limit_value_online").val('');
                      }
                  }
                    else if(adv_limit_type==1)//percent type
                  {
                      var max_amount=parseFloat(amt*max_val/100);//amount in total advance limit value        
                      console.log(max_amount);
                      
                    //  var max_amt_online=parseFloat(max_amount*given_value/100);// amount in advance limit online
                    //  console.log(max_amt_online);
                     
                    
                     if(given_value > max_amount)
                      {
                          alert("Amount must be less than "+max_amount);
                          $("#adv_limit_value_online").val('');
                      }
                     
                  }
             }
         }
        
        $('#make_pay_cash').on('keyup',function(){
    	calculatePaymentCost();
         });
        
        function calculatePaymentCost()
    {
    //	validate_max_cash();
    	var total_amount    =0;
    	var bal_amount      =0;
    	var wallet_blc      =0;
    	var payment_amt     =($('#payment_amt').val()!='' ? $('#payment_amt').val():0);
    	var make_pay_cash   =($('#make_pay_cash').val()!='' ? $('#make_pay_cash').val():0);
    	var cc              =($('.CC').html()!='' ? $('.CC').html():0);
    	var dc              =($('.DC').html()!='' ? $('.DC').html():0);
    	var chq             =($('.CHQ').html()!='' ? $('.CHQ').html():0);
    	var NB              =($('.NB').html()!='' ? $('.NB').html():0);
    	var adv_adj_amt     =($('#tot_adv_adj').html()!='' ? $('#tot_adv_adj').html():0);
    	
    	
    	total_amount=parseFloat(parseFloat(make_pay_cash)+parseFloat(cc)+parseFloat(dc)+parseFloat(chq)+parseFloat(NB)+parseFloat(adv_adj_amt)).toFixed(2);
    	bal_amount=parseFloat(parseFloat(payment_amt)-parseFloat(total_amount)).toFixed(2);
    	$('.sum_of_amt').html(total_amount);
    	$('.bal_amount').html(bal_amount);
    	if(($('#payment_amt').val()==0))
    	{
    	    $('#pay_submit').prop('disabled',false);
    	
    	}
    	
    }
    
    function select_booking(booking_id){
         var box_html='';
         var ledger_html='';
       // alert(booking_id);
         $.ajax({
		        data:{'id_customer':$('#id_customer').val(),'source_type':'ADMIN','booking_id':booking_id},
                url:api_url+ "index.php/advance_booking/paymentData?nocache=" + my_Date.getUTCSeconds(),
                dataType:"JSON",
                type:"GET",
                success:function(data){
                    
                    $('#payment_amt').val('');
                    $('#payment_modes').css("pointer-events", 'none');
                    $('#payment_modes').css("opacity", '0.4');
                    
                    $('.overlay').css('display','block');
                    box_html='';
                    ledger_html='';
                    $('#payment-detail-box').empty();
                    $('#payment-detail-box').css('display','none');
                    $('#ledger-box').empty();
                    $('#ledger-box').css('display','none');
                
                var data = data.chits;
                
                    $('.overlay').css('display','none');
                    $('#booking_form').css('display','none');
                    $('#payment_form').css('display','block');
                   
                    if(data.can_pay == 'N'){
                        $('#make_pay_block').css('display','none');
                    }else{
                        $('#make_pay_block').css('display','block');
                    }
                    
                    
                    //acc
                   $('input[name=advance_paid]').val(data.advance_paid);
                    $('input[name=advance_value]').val(data.advance_value);
                    $('input[name=balance_adv_amt]').val(data.balance_adv_amt);
                    $('input[name=balance_amt]').val(data.balance_amt);
                     $('input[name=balance_paid]').val(data.balance_paid);
                    $('input[name=booking_amount]').val(data.booking_amount);
                     $('input[name=booking_date]').val(data.booking_date);
                    $('input[name=booking_name]').val(data.booking_name);
                     $('input[name=booking_number]').val(data.booking_number);
                    $('input[name=booking_status]').val(data.booking_status);
                    $('input[name=booking_rate]').val(data.booking_rate);
                     $('input[name=id_adv_booking]').val(data.booking_id);
                    
                    $('input[name=eligible_on]').val(data.eligible_on);
                     $('input[name=max_payable_amount]').val(data.max_payable_amount);
                    $('input[name=metal]').val(data.metal);
                     $('input[name=min_payable_amount]').val(data.min_payable_amount);
                    $('input[name=online_advance_amt]').val(data.online_advance_amt);
                     $('input[name=status]').val(data.status);
                    $('input[name=total_advance_amt]').val(data.total_advance_amt);
                    $('input[name=total_paid_amount]').val(data.total_paid_amount);
                    
                    $("#payable").val(data.payable);
                    $("#min_payable_amount_span").text("Min : " + data.min_payable_amount);
                    $("#max_payable_amount_span").text("Max : " + data.max_payable_amount);
                    
                    
                    //create account details box div starts
                    
                    var gst_data = '';    
                   
                    if(data.gst_setting == 1){
                        if(data.gst_type == 0){
                            gst_data +=  '<th>Gst : '+data.gst+'% (Incl)</th>';
                        }else{
                            gst_data += '<th>Gst : '+data.gst+'% (Excl)</th>';
                        }
                        
                         gst_data += '<td>'+data.gst_amount+'</td>';
                    }else{
                         gst_data += '<th></th><td></td>';
                    }
                    
                    box_html+='<legend>Booking Details</legend><div  class="box box-solid box-default" >'+
                    '<div class="box-body" id="acc_box">'+
                    '<table class="table table-condensed">'+
                    '<tr>'+
                    '<th>Booking Number</th>'+
                    '<td>'+data.booking_number+'</td>'+
                    '<th>Act.Booked Amount</th>'+
                    '<td>'+data.booking_amount+'</td>'+
                    '<th>Total Paid Amount</th>'+
                    '<td>'+data.total_paid_amount+'</td>'+
                    
                    '</tr>'+
                    '<tr>'+
                    '<th>Booking Name</th>'+
                    '<td>'+data.booking_name+'</td>'+
                    '<th>Act.Booked Weight</th>'+
                    '<td>'+data.booking_weight+'</td>'+
                    '<th>Maturity Date</th>'+
                    '<td>'+data.maturity_date+'</td>'+
                    
                    '</tr>'+
                    '<tr>'+
                    '<th>Booking Status</th>'+
                    '<td>'+data.booking_status+'</td>'+
                    '<th>Booked Rate</th>'+
                    '<td>'+data.booking_rate+'</td>'+
                    '<th>Total Advance</th>'+
                    '<td>'+data.total_advance_amt+'</td>'+
                    
                    '</tr>'+
                    
                    '<tr>'+
                    '<th>Final Amount</th>'+
                    '<td>'+data.final_booking_amount+'</td>'+
                    '<th>Final Weight</th>'+
                    '<td>'+data.final_booking_weight+'</td>'
                    
                    +gst_data+
                    
                    '</tr>'+
                    
                    '</table>'+
                    '</div>'+
                    '</div>';
                    $('#payment-detail-box').css('display','block');
                    /* if(data.can_pay=='Y')
                    {
                        $('#payment-detail-box').css('background-color', 'rgba(0, 255, 0, 0.2)');
                        $('#acc_box').css('background-color', 'rgba(0, 255, 0, 0.2)');
                    }
                    else
                    {
                        $('#payment-detail-box').css('background-color', 'rgba(255, 0, 0, 0.2)');
                        $('#acc_box').css('background-color', 'rgba(255, 0, 0, 0.2)');
                    }*/
                    $('#payment-detail-box').append(box_html);
                    //create account details box div ends

                    
                     //load ledger
                    set_ledger(data.booking_id);
                     

                    
                    
                   


                    
                   /* $("#advance_paid").val(data.advance_paid);
                    $("#advance_value").val(data.advance_value);
                    $("#balance_adv_amt").val(data.balance_adv_amt);
                    $("#content_box").text(data.content_box);
                    $("#balance_amt").val(data.balance_amt);
                    $("#balance_paid").val(data.balance_paid);
                    
                    $("#booking_amount").html(data.booking_amount);
                    $("#booking_date").html(data.booking_date);
                    $("#booking_id").val(data.booking_id);
                    $("#booking_name").html(data.booking_name);
                    $("#booking_number").html(data.booking_number);
                    $("#booking_rate").html(data.booking_rate);
                    $("#booked_status").html(data.booking_status);
                    
                    
                    $("#booked_weight").html(data.booking_weight);
                    $("#booked_weight").prop('readonly', true);
                    
                    $("#can_pay").text(data.can_pay);
                    $("#eligible_on").val(data.eligible_on);
                    $("#id_metal").val(data.id_metal);
                    $("#max_payable_amount").val(data.max_payable_amount);
                    $("#max_payable_amount_span").text("Max : " + data.max_payable_amount);
                    $("#metal").val(data.metal);
                    $("#min_payable_amount").val(data.min_payable_amount);
                    $("#min_payable_amount_span").text("Min : " + data.min_payable_amount);
                    $("#online_advance_amt").val(data.online_advance_amt);
                    $("#payable").val(data.payable);
                    //$("#status").val(data.status);
                    $("#total_advance_amt").val(data.total_advance_amt);
                    $("#total_paid_amount").val(data.total_paid_amount);
                    $("#trans_type").text(data.trans_type);*/
        
            }
        });
    }
    
     function set_ledger(id_booking)
    {
        
         $.ajax({
		             url:api_url+"index.php/advance_booking/pre_booking_payment?nocache=" + my_Date.getUTCSeconds(),
        			 data: {'id_booking':id_booking},
        			 dataType:"JSON",
        			 type:"GET",
                    success:function(data){
                   
                     ledger_html='';
                     ledger_html+='<legend>Ledger</legend>';
                    if(data.length>0)
                    {
                        
                    ledger_html+='<div  class="box box-solid box-default" >'+
                    '<div class="box-body">'+
                    '<table class="table table-bordered table-striped">'+
                    '<thead>'+
                    '<tr>'+
                     '<th style="text-align:right;">Receipt</th>'+
                    '<th style="text-align:left;">Payment Date</th>'+
                   
                    '<th style="text-align:right;">Payment Amount</th>'+
                    '<th style="text-align:center;">Payment Mode</th>'+
                    '<th style="text-align:left;">Paid Through</th>'+
                    
                    '</tr>'+
                    '</thead>';
                        var tot_amt=0;
                    	$.each(data, function (key, item) 
                    	{
                    	    tot_amt+=parseFloat(item.payment_amount);
                    	    ledger_html+='<tr>'+
                    	   '<td style="text-align:right;">'+item.receipt_number+'</td>'+
                    	    '<td style="text-align:left;">'+item.date_payment+'</td>'+
                    	     
                    	    '<td style="text-align:right;">'+indianCurrency.format(item.payment_amount)+'</td>'+
                    	    '<td style="text-align:center;">'+item.payment_mode+'</td>'+
                    	     '<td style="text-align:left;">'+item.payment_through+'</td>'+
                    	   
                    	    '</tr>';
                    	});
                        ledger_html+='<tr style="font-weight:bold;">'+
                    	   '<td style="text-align:right;"></td>'+
                    	    '<td style="text-align:left;">Total </td>'+
                    	     
                    	    '<td style="text-align:right;">'+indianCurrency.format(tot_amt)+'</td>'+
                    	    '<td style="text-align:center;"></td>'+
                    	     '<td style="text-align:left;"></td>'+
                    	   
                    	    '</tr>';
                    
                    
                    ledger_html+='</table>'+
                    '</div>'+
                    '</div>';
                    $('#ledger-box').css('display','block');
                    $('#ledger-box').append(ledger_html);
                    }
                    else
                    {
                        ledger_html='';
                        $('#ledger-box').css('display','block');
                        ledger_html='<h4 style="text-align:center;">No Payment Data Available</h4>';
                        $('#ledger-box').append(ledger_html);
                        
                    }
                }
         });
    }
    function swapDiv(event, elem) {
        console.log(event);
        console.log(elem);
        console.log($('#book_amt').html());
        
        console.log($('#book_wt').html());
        
        wt = $('#book_wt').html();
        amt = $('#book_amt').html();

        elem.parentNode.insertBefore(elem, elem.parentNode.firstChild);
    }
    
     //booked account list starts  
    	 function pre_booking_account_list()
        {
            var mob=$("#mobile_number").val();
            
            if(mob!='')
            {
              var id_customer=$('#id_customer').val();  
              
              var from_date= $('#account_list1').html();
                var to_date=$('#account_list2').html();
            }
             else
             {
               var from_date= $('#account_list1').html();
                var to_date=$('#account_list2').html();
                var id_branch=$('#branch_select').val();
             
             }
             
            if(from_date!='' && to_date!='')
            {
                $("#bookings_acc_range").text(from_date+" To "+to_date);
            }
            else
            {
                $("#bookings_acc_range").text("All Accounts")
            }
            
        	my_Date = new Date();
        	
        	 $("div.overlay").css("display", "block"); 
        	$.ajax({
        	            
        			  url:api_url+"index.php/advance_booking/pre_booking_accounts?nocache=" + my_Date.getUTCSeconds(),
        			 data: (from_date !='' || id_branch!='' || to_date !='' || id_customer!=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch,'id_customer':id_customer}: ''),
        			 dataType:"JSON",
        			 type:"GET",
        			 success:function(data){
        			     console.log(data);
        			   			set_prebooking_acc_list(data);
        			   			 $("div.overlay").css("display", "none"); 
        					  },
        					  error:function(error)  
        					  {
        						 $("div.overlay").css("display", "none"); 
        					  }	 
        			      });
        }
        
        
        function set_prebooking_acc_list(data)	
            {
              
               var oTable = $('#pre_booking_acc_list').DataTable();
                
               
            	 oTable.clear().draw();
               	 if (data!= null && data.length > 0)
            	 {
            		 $("#booked_accounts_count").text(data.length);
            	 	oTable = $('#pre_booking_acc_list').DataTable({
            				                "bDestroy": true,
            				                "bInfo": true,
            				                "bFilter": true,
            				                "bSort": true,
            				                
            				                 "dom": 'lBfrtip',
                       		               "buttons": [
        									{  
        										extend: 'print',
        										footer: true,
        										title:'Reserve Booked Accounts',
        										
        										orientation: 'landscape',
        											 customize: function ( win ) {
        												 $(win.document.body).find( 'table' ).addClass('compact').css('font-size','10px').css('font-family','sans-serif');
        											 },
        											 exportOptions: {
        														columns: ':visible'
        														},
        										 },
        										 {
        											 extend:'excel',
        											 footer: true,
        											 title: 'Reserve Booked Accounts ',
        										 },
        										 { 
        											 extend: 'colvis',
        											 collectionLayout: 'fixed columns',collectionTitle: 'Column visibility control'
        											},
        									 ],
            						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            				                "aaData": data,
            				                "order": [[ 0, "desc" ]],
            				                 "columnDefs": 
															 [
																 {
																	 targets: [0,1,2,3,4,8,11,12,13,14,15], 
																	 className: 'dt-left'
																 },
																 {
																	 targets: [5,6,7,9,10], 
																	 className: 'dt-right'
																 },
							 
			 
															 ],
            				               "aoColumns": [
            				
            					                { "mDataProp": "booking_id" },
            					                { "mDataProp": "cus_name" },
            					                { "mDataProp": "mobile" },
            					                { "mDataProp": "booking_name" },
            					                { "mDataProp": "booking_number" },
            					                {"mDataProp":function ( row, type, val, meta ) {
            					                    if(row.booking_amount!='')
            					                    {
            					                        if(row.gst_setting == 1 && row.gst_amount > 0 && row.gst_type_name != ''){
            					                            return indianCurrency.format(row.booking_amount)+'</br><em>('+row.gst_type_name+': INR '+indianCurrency.format(row.gst_amount)+' )</em>';
            					                        }else{
            					                            return indianCurrency.format(row.booking_amount);
            					                        }
            					                        
            					                        
            					               
            					                    }
            					                    else
            					                    {
            					                        return '0.00';
            					                    }
            					                }},
            					                
            					                {"mDataProp":function ( row, type, val, meta ) {
            					                    if(row.booking_weight!='')
            					                    {
            					                        if(row.gst_setting == 1 && row.gst_weight > 0 && row.gst_type_name != ''){
            					                            return row.booking_weight+'</br><em>('+row.gst_type_name+': '+row.gst_weight+'g)</em>';
            					                        }else{
            					                            return row.booking_weight;
            					                        }
            					                        
            					                        
            					               
            					                    }
            					                    else
            					                    {
            					                        return '0.00';
            					                    }
            					                }},
            					                { "mDataProp": "booking_rate" },
            					                { "mDataProp": "booking_date" },
            					                {"mDataProp":function ( row, type, val, meta ) {
            					                    if(row.payment_amount!='')
            					                    {
            					                        return indianCurrency.format(row.payment_amount);
            					               
            					                    }
            					                    else
            					                    {
            					                        return '0.00';
            					                    }
            					                }},
            					                {"mDataProp":function ( row, type, val, meta ) {
            					                    var bal=parseFloat(row.booking_amount)-parseFloat(row.payment_amount);
            					                      if(bal!='')
            					                    {
            					                        return indianCurrency.format(bal);
            					               
            					                    }
            					                    else
            					                    {
            					                        return '0.00';
            					                    }
            					                }},
            					                { "mDataProp": "branch" },
            					                { "mDataProp": "employee" },
            					                 {"mDataProp":function ( row, type, val, meta ) {
            					                     switch(row.status)
            					                     {
            					                         case '1':
            					                             return 'Open';
            					                             break;
            					                         case '2':
            					                              return 'Advance Done';
            					                             break;
            					                         case '3':
            					                              return 'Paid';
            					                             break;
            					                         case '4':
            					                              return 'Closed';
            					                             break;
            					                       default:
            					                            return '-';
            					                            
            					                     }
            					                    
            					                }},
            					                 {"mDataProp":function ( row, type, val, meta ) {
            					                  switch(row.added_by)
            					                     {
            					                         case '0':
            					                             return 'Web App';
            					                             break;
            					                         case '1':
            					                              return 'Admin';
            					                             break;
            					                         case '2':
            					                              return 'Mobile App';
            					                             break;
            					                         case '3':
            					                              return 'Collection App';
            					                             break;
            					                         case '4':
            					                              return 'Retail App';
            					                             break;
            					                         case '5':
            					                              return 'Import';
            					                             break;
            					                       default:
            					                            return '-';
            					                            
            					                     }
            					                }},
            					                { "mDataProp": "remarks" },
            					                { "mDataProp": function(row,type,val,meta){
            					                     id= row.booking_id;
            					                     action_content = '';
            					                     if(row.status == 3){
            					                         action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu"><li><a href="#" class="btn-edit" onClick="close_booking('+id+')"><i class="fa fa-close" ></i> Close</a></li></ul></div>';
					                	                
            					                     }
            					                    
            					                     return action_content;   
            					                    
            					                    }
                            	                }
            					              ]
            				            });	
            	  
                }
                else
                {
                    $("#booked_accounts_count").text('');
                }
            }
    //booked account list ends  
    
    
    function close_booking(id){
        $('#close_booking').modal('show', {backdrop: 'static'});
        $('#id_booking_close').val(id);
    }
    
    $('#mark_close').on('click',function(){
        var id = $('#id_booking_close').val();
        mark_close(id);
    });
    
    function mark_close(id){
        
        my_Date = new Date();
        $("div.overlay").css("display","block");
        var cls_emp = $('#employee_closed').val();
        var cls_branch = $('#closing_id_branch').val();
        $.ajax({
            data : {'booking_id': id,'employee_closed' : cls_emp,'closing_id_branch' : cls_branch},
            url:api_url+"index.php/advance_booking/booking_close?nocache="+my_Date.getUTCSeconds(),
            dataType:"JSON",
            type:"POST",
            success:function(data){
                $("div.overlay").css("display","none");
                $('#close_booking').modal('toggle'); 
                $.toaster({ priority : 'success', title : 'Success!', message : data.msg});
                pre_booking_account_list();
                
            },
            error:function(data){
                $("div.overlay").css("display","none");
                $('#close_booking').modal('toggle'); 
                $.toaster({ priority : 'warning', title : 'Success!', message : data.msg});
                pre_booking_account_list();
            }
        });
    }
    
    function load_accounts(){
        
         my_Date = new Date();
        $("div.overlay").css("display", "block");
        
        $.ajax({
                		         data:{'id_customer':$('#id_customer').val(),'source_type':'ADMIN','id_plan':$('#plan_val').val()},
                         
                             url:api_url+ "index.php/advance_booking/paymentData?nocache=" + my_Date.getUTCSeconds(),
                            dataType:"JSON",
                            type:"GET",
                            success:function(data){
                
                              
                			   var data = data.chits;
                			   
                			   console.log(data);
                			   
                						$('#booking_select').empty();
                						var content = '';
                						if(data!=null)
                							 {
                							      $('.accounts').css('display','block');
                							     
                								var textToInsert = '';
                								
                								textToInsert += '<div class="grid-container" style="display: grid;grid-template-columns: repeat(7, 20fr);height:130px;grid-column-gap: 5px;grid-row-gap: 5px;padding: 10px;border-radius: 1px;">';
                                                $.each(data, function(key, value) {
                                                    var button = (value.can_pay == 'Y' ? 'Pay' : 'Details');
                                                  textToInsert += '<div class="grid-item" style="background-color: honeydew;color:#000;    font-weight: 700;font-size: 15px;height:170px;width:160px;text-align: center;border-radius:8px;"><h4 style="font-weight: 700;">' + value.booking_number + '</h4><p> Balance : INR ' + value.balance_amt+ '</p><p>(' + value.booking_status+ ')</p><input type="button" onclick="select_booking('+value.booking_id+')" value="'+button+'" style="color: white;background-color: green;"></input></div>';
                                                });
                                                textToInsert += '</div>';
                                                
                                                $('#list_accounts').html(textToInsert);
                								
                								$('#list_accounts').css('display','block');	
                						
                					
                							 }else
                							 {
                							     empty_AllData();
                							     $("#booking_amount").prop('readonly', false);
                							     $("#booking_weight").prop('readonly', false);
                							     $('#booking_select').empty();
                							     $("#booking_div").css('display','none');
                							 }
                						 //disable spinner
                						 			$('.overlay').css('display','none');
                						 			
                			},
                				error:function(error)
                			{
                			console.log(error);
                			//disable spinner
                			$('.overlay').css('display','none');
                			}	
                			 });
    }
    
    
