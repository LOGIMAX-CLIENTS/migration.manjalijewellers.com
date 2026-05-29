//general setings Functions here

var path =  url_params();

var ctrl_page = path.route.split('/');

console.log(ctrl_page);

 $(document).ready(function(){
     
     
     
     	switch(ctrl_page[1])
    	{
    	    case 'gift_voucher_settings':
    	        switch(ctrl_page[2])
    	        {
    	            case 'add':
    	                getActiveMetal();
    	                
    	            break;
    	            case 'edit':
    	                getActiveMetal();
    	                 var oTable = $('#issue_product').DataTable();
    	                 var oTable = $('#utilize_product').DataTable();
	                 	if ( ! $.fn.DataTable.isDataTable( '#issue_product,#utilize_product' ) ) 
	                 	{ 
    						oTable = $('#utilize_product,#issue_product').dataTable({ 
    						"bSort": false, 
    						"bDestroy": true,
    						"bInfo": true, 
    						"scrollX":'100%',
    						"paging": false,  
    						"dom": 'Bfrtip',
    						 "lengthMenu": [ [10, 25, 50, -1], ["All", 25, 50] ],
    						"bAutoWidth": false,
    						"responsive": true,
    						});
    					}
    	            break;
    	        }
    	         if($('#description').length > 0)
                {
                CKEDITOR.replace('description');
                }
    	        get_gift_voucher_settings();
    	    break;
    	    
	 	    case 'gift_issue':
				switch(ctrl_page[2])
				
				{				 	
				 	case 'list':				 	
				 			get_billing_list();
				 		break;
				 	case 'add':
				 	        get_free_giftvoucher_list();
				 		break;
				}
	 		break; 
	 		
	 		case 'gift_master':
	 		            getActiveMetal();
	 		            
                        if($('#description').length > 0)
                        {
                        CKEDITOR.replace('description');
                        }

	 		            switch(ctrl_page[2])
	 		            {
	 		                case 'list':
	 		                    get_gift_voucher_details();
	 		                break;
	 		            }
		    break;
		
    	}
     
});


//Gift Voucher Settings

$('#utilize_select').click(function(event) {
	  $("#utilize_product tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
      event.stopPropagation();
});

$('#issue_select').click(function(event) {
	  $("#issue_product tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
      event.stopPropagation();
});


$('#utilized_for').on('change',function(){
    if(this.value!='' && this.value==3)
    {
        get_ActiveProduct();
    }else{
        //$('#utilized').css("display", "none"); 
    }
  
});

$('#issue_for').on('change',function(){
    if(this.value!='' && this.value==3)
    {
        //$('#issue').css("display", "block"); 
        get_issueProduct();
    }else{
        //('#issue').css("display", "none"); 
    }
});


$('#metal_select').on('change',function(){
    if(this.value!='' && this.value==3)
    {
        //$('#issue').css("display", "block"); 
        get_master_Product();
    }else{
        //('#issue').css("display", "none"); 
    }
});

function get_master_Product()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_gift_vocuher/get_Activeproduct/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
		   	
			 var oTable = $('#utilize_product').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#utilize_product').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": false,
						"order": [[ 0, "desc" ]],
						"lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],
						"aaData"  : data,
						"aoColumns": [
						{ "mDataProp": function ( row, type, val, meta ){ 
		                	chekbox='<input type="checkbox" class="pro_id" name="utilized_pro[]"  value="'+row.pro_id+'"/>'+row.pro_id; 
		                	return chekbox;
		                }},
						{ "mDataProp": "name" },
						{ "mDataProp": "product_name" },
						]
					});			  	 	
				}
				$("div.overlay").css("display", "none"); 
		},
		 error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	
	});
}

function get_ActiveProduct()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_gift_vocuher/get_Activeproduct/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
		   	
			 var oTable = $('#utilize_product').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#utilize_product').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": false,
						"order": [[ 0, "desc" ]],
						"lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],
						"aaData"  : data,
						"aoColumns": [
						{ "mDataProp": function ( row, type, val, meta ){ 
		                	chekbox='<input type="checkbox" class="pro_id" name="utilized_pro[]"  value="'+row.pro_id+'"/>'+row.pro_id; 
		                	return chekbox;
		                }},
						{ "mDataProp": "name" },
						{ "mDataProp": "product_name" },
						]
					});			  	 	
				}
				$("div.overlay").css("display", "none"); 
		},
		 error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	
	});
}


function get_ActiveProduct()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_gift_vocuher/get_Activeproduct/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
		   	
			 var oTable = $('#utilize_product').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#utilize_product').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": false,
						"order": [[ 0, "desc" ]],
						"lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],
						"aaData"  : data,
						"aoColumns": [
						{ "mDataProp": function ( row, type, val, meta ){ 
		                	chekbox='<input type="checkbox" class="pro_id" name="utilized_pro[]"  value="'+row.pro_id+'"/>'+row.pro_id; 
		                	return chekbox;
		                }},
						{ "mDataProp": "name" },
						{ "mDataProp": "product_name" },
						]
					});			  	 	
				}
				$("div.overlay").css("display", "none"); 
		},
		 error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	
	});
}

function get_issueProduct()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_gift_vocuher/get_Activeproduct/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
		   	
			 var oTable = $('#issue_product').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#issue_product').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": false,
				    	"lengthMenu": [ [ 10, 25, 50, -1], [10, 25, 50, "All"] ],
						"order": [[ 0, "desc" ]],				
						"aaData"  : data,
						"aoColumns": [
						{ "mDataProp": function ( row, type, val, meta ){ 
		                	chekbox='<input type="checkbox" class="pro_id" name="issue_pro[]"  value="'+row.pro_id+'"/>'+row.pro_id; 
		                	return chekbox;
		                }},
						{ "mDataProp": "name" },
						{ "mDataProp": "product_name" },
						]
					});			  	 	
				}
				$("div.overlay").css("display", "none"); 
		},
		 error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	
	});
}

function getActiveMetal()
{
     $(".overlay").css("display", "block");
    $('#metal_select option').remove();
    my_Date = new Date();
		$.ajax({ 
		url:base_url+ "index.php/admin_gift_vocuher/getActiveMetal?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        type:"POST",
        data:{'id_branch':$('#id_branch').val()},
        dataType:"JSON",
        success:function(data)
        {
           var metal_type=$('#id_metal').val();
           var issue_for=$('#metal').val();
           
           console.log(metal_type);
          
           	$("#utilized_for,#issue_for,#metal_select").append(						
        	 	$("<option></option>")						
        	 	.attr("value", 0)						  						  
        	 	.text('All' ),
        	 	$("<option></option>")						
        	 	.attr("value", 1)						  						  
        	 	.text('Gold' ),
        	 	$("<option></option>")						
        	 	.attr("value", 2)						  						  
        	 	.text('Silver'),
        	 		$("<option></option>")						
        	 	.attr("value", 3)						  						  
        	 	.text('Product Based')
    	 	);
    	 	
         					
             	$("#utilized_for,#issue_for,#metal_select").select2({			    
            	 	placeholder: "Metal Type",			    
            	 	allowClear: true		    
             	});	
             	
             	if(ctrl_page[1]!='gift_master')
             	{
             	     $("#utilized_for").select2("val",(metal_type!='' ?metal_type:''));	 
         	          $("#issue_for").select2("val",(issue_for!='' ?issue_for:''));	 
             	}
             	else if(ctrl_page[1]=='gift_master')
             	{
             	    $("#metal_select").select2("val",(metal_type!='' ?metal_type:''));	 
             	}
         	   
         	    
         	    $(".overlay").css("display", "none");	
        },
        error:function(error)  
        {	
             $(".overlay").css("display", "none");
        } 
    	});
}


function get_gift_voucher_settings()
{
	$("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_gift_vocuher/gift_voucher_settings/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
		   		var list 	= data.list;
				var access	    = data.access;	
				if(access.add == '0')
				{ 	
					$('#add_size').attr('disabled','disabled');
				}
			 $("div.overlay").css("display","add_newstone");
			 var oTable = $('#gift_voucher_settings').DataTable();
			 oTable.clear().draw();
			 if (list!= null && list.length > 0)
			 {  	
				oTable = $('#gift_voucher_settings').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],				
						"aaData"  : list,
						"aoColumns": [
						{ "mDataProp": "id_set_gift_voucher"},
						{ "mDataProp": "branch_name"},
						{ "mDataProp": "gift_type" },
						{ "mDataProp": "sale_value" },
						{ "mDataProp": "credit_value" },
						{ "mDataProp": function ( row, type, val, meta ){
						    active_url =base_url+"index.php/admin_gift_vocuher/update_gift_settings_status/"+(row.status==0?1:0)+"/"+row.id_set_gift_voucher; 
						   
						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"
						}
						},
						{ "mDataProp": function ( row, type, val, meta ){
						    default_url =base_url+"index.php/admin_gift_vocuher/UpdateGiftSettings/"+(row.is_default==0?1:0)+"/"+row.id_set_gift_voucher+'/'+row.id_branch; 
						return "<a href='"+default_url+"'><i class='fa "+(row.is_default==1?'fa-check':'fa-remove')+"' style='color:"+(row.is_default==1?'green':'red')+"'></i></a>"
						}
						},
						{ "mDataProp": function ( row, type, val, meta ) {
							 id= row.id_set_gift_voucher;
							 edit_url=(access.edit=='1'?  base_url+'index.php/admin_gift_vocuher/gift_voucher_settings/edit/'+id : '#');
							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_gift_vocuher/gift_voucher_settings/delete/'+id : '#' );
							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
							 action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit" ><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'
							 return action_content;
							 }
						},
						]
					});			  	 	
				}
				$("div.overlay").css("display", "none"); 
		},
		 error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	
	});
}

$('#gift_type').on('change',function(){
    if(this.value==1)
    {
        $('#min_value').attr("placeholder","Enter The Amount");
        $('#credit_value').attr("placeholder",'Enter The Amount');
    }
    else if(this.value==2)
    {
        $('#min_value').attr("placeholder",'Enter The Amount');
        $('#credit_value').attr("placeholder",'Enter The Weight');
    }
    else if(this.value==3)
    {
        $('#min_value').attr("placeholder",'Enter The Weight');
        $('#credit_value').attr("placeholder",'Enter The Amount');
    }
    else if(this.value==4)
    {
        $('#min_value').attr("placeholder",'Enter The Weight');
        $('#credit_value').attr("placeholder",'Enter The Weight');
    }
});

//Gift Voucher Settings

    $('#branch_select').on('change',function(){
        if(this.value!='')
        {
            $('#id_branch').val(this.value);
                 //get_free_giftvoucher_list();
                if(ctrl_page[1]=='gift_master')
                {
                    var data = $("#branch_select").select2('data');		
                    selectedValue = $(this).val(); 		
                    $("#id_branch").val(selectedValue);
                }
                
        }else{
            $('#id_branch').val('');
        }
    });

	 $("input[name='gift[gift_type]']:radio").on('change',function(){
	        $('.free_type').css("display","none");
	        $('.promotional_type').css("display","none");
	        $('.payment_blk').css("display","none");
		   if($(this).val()==1)
		   {
		   		$('#select_gift').prop('disabled',false);
		   		
		   		$('.receive_amount').prop('disabled',true);
		   		$('.cash_pay').prop('disabled',true);
		   		
		   		$('#gift_receipts').css('display','none');
		   		
		   		$('#cus_transfer_to').css('display','block');
		   		$('#cus_select').css('display','block');

		   		$('#gift_receipts').css('display','none');
		   		
		   		$('.free_type').css("display","block");
		   }
		   else if($(this).val()==2)
		   {
		   		$('#select_gift').prop('disabled',true);
		   		
		   		$('.receive_amount').prop('disabled',false);
		   		$('.cash_pay').prop('disabled',false);
		   		
		   		$('#gift_receipts').css('display','none');
		   		
		   		$('#cus_transfer_to').css('display','block');
		        $('#cus_select').css('display','block');
		        
		        $('.payment_blk').css("display","block");
		   		
		   }
		   else if($(this).val()==3)
		   {
		        $('#gift_receipts').css('display','block');
		        
		        $('#select_gift').prop('disabled',false);
		   		$('.receive_amount').prop('disabled',true);
		   		$('.cash_pay').prop('disabled',true);
		   		
		   		$('.receive_amount').prop('disabled',true);
		   		$('.cash_pay').prop('disabled',true);
		   		
		   		$('#cus_transfer_to').css('display','none');
		   		$('#cus_select').css('display','none');
		   		
		   		$('.promotional_type').css("display","block");
		   		
		   }
	});
	
	$("input[name='gift[gift_for]']:radio").on('change',function(){
		   if($(this).val()==1)
		   {
		   		$('#emp_select').css('display','block');
		   		$('#cus_select').css('display','none');
		   		$('#cus_transfer_to').css('display','none');
		   		$('#gift_receipts').css('display','none');
		   }
		   else if($(this).val()==2)
		   {
		   		$('#gift_receipts').css('display','none');
		   		$('#emp_select').css('display','none');
		   		$('#cus_select').css('display','block');
		   		$('#cus_transfer_to').css('display','block');
		   }
	});
	
	$("#cus_name").on("keyup",function(e){ 
		var customer = $("#cus_name").val();
		if(customer.length >= 2) { 
			getSearchCustomers(customer);
		}
	}); 
	
	function getSearchCustomers(searchTxt){
	my_Date = new Date();
	$.ajax({
        url: base_url+'index.php/admin_gift_vocuher/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),             
        dataType: "json", 
        method: "POST", 
        data: {'searchTxt': searchTxt}, 
        success: function (data) {
			$( "#cus_name" ).autocomplete(
			{
				source: data,
				select: function(e, i)
				{ 
					e.preventDefault();
					$("#cus_name").val(i.item.label);
					$("#id_customer").val(i.item.value);
				},
				change: function (event, ui) {
					if (ui.item === null) {
						$(this).val('');
						$('#cus_name').val('');
						$("#id_customer").val("");
					}
			    },
				response: function(e, i) {
		            // ui.content is the array that's about to be sent to the response callback.
		            if(searchTxt != ""){
						if (i.content.length === 0) {
						   $("#customerAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');
						}else{
						   $("#customerAlert").html('');
						} 
					}else{
					}
		        },
				 minLength: 3,
			});
        }
     });
}

$("#purchase_cus_search").on("keyup",function(e){ 
		var customer = $("#purchase_cus_search").val();
		if(customer.length >= 2) { 
			purchase_cus_search(customer);
		}
	}); 
	
	function purchase_cus_search(searchTxt){
	my_Date = new Date();
	$.ajax({
        url: base_url+'index.php/admin_gift_vocuher/getCustomersBySearch/?nocache=' + my_Date.getUTCSeconds(),             
        dataType: "json", 
        method: "POST", 
        data: {'searchTxt': searchTxt}, 
        success: function (data) {
			$( "#purchase_cus_search" ).autocomplete(
			{
				source: data,
				select: function(e, i)
				{ 
					e.preventDefault();
					$("#purchase_cus_search").val(i.item.label);
					$("#purchase_to").val(i.item.value);
				},
				change: function (event, ui) {
					if (ui.item === null) {
						$(this).val('');
						$('#purchase_cus_search').val('');
						$("#purchase_to").val("");
					}
			    },
				response: function(e, i) {
		            // ui.content is the array that's about to be sent to the response callback.
		            if(searchTxt != ""){
						if (i.content.length === 0) {
						   $("#customerAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');
						}else{
						   $("#customerAlert").html('');
						} 
					}else{
					}
		        },
				 minLength: 3,
			});
        }
     });
}


	$("#emp_name").on("keyup",function(e){ 
		var employee = $("#emp_name").val();
		if(employee.length >= 2) { 
			getSearchEmployee(employee);
		}
	}); 
	
	function getSearchEmployee(searchTxt){
	my_Date = new Date();
	$.ajax({
        url: base_url+'index.php/admin_gift_vocuher/getEmployeeBySearch/?nocache=' + my_Date.getUTCSeconds(),             
        dataType: "json", 
        method: "POST", 
        data: {'searchTxt': searchTxt}, 
        success: function (data) {
			$( "#emp_name" ).autocomplete(
			{
				source: data,
				select: function(e, i)
				{ 
					e.preventDefault();
					$("#emp_name").val(i.item.label);
					$("#id_employee").val(i.item.value);
				},
				change: function (event, ui) {
					if (ui.item === null) {
						$(this).val('');
						$('#emp_name').val('');
						$("#id_employee").val("");
					}
			    },
				response: function(e, i) {
		            // ui.content is the array that's about to be sent to the response callback.
		            if(searchTxt != ""){
						if (i.content.length === 0) {
						   $("#employeeAlert").html('<p style="color:red">Enter a valid customer name / mobile</p>');
						}else{
						   $("#employeeAlert").html('');
						} 
					}else{
					}
		        },
				 minLength: 3,
			});
        }
     });
}

function get_free_giftvoucher_list()
{
    $('#select_gift option').remove();
    my_Date = new Date();
		$.ajax({ 
		url:base_url+ "index.php/admin_gift_vocuher/get_gift_voucher?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        type:"POST",
        data:{'id_branch':$('#id_branch').val()},
        dataType:"JSON",
        success:function(data)
        {
           var id_village=$('#select_gift').val();
           $.each(data, function (key, item) {					  				  			   		
                	 	$("#select_gift").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_gift_voucher)						  						  
                	 	.text(item.name)						  					
                	 	);			   											
                 	});						
             	$("#select_gift").select2({			    
            	 	placeholder: "Select Gift",			    
            	 	allowClear: true		    
             	});					
         	    $("#select_gift").select2("val",(id_village!='' && id_village>0?id_village:''));	 
         	    $(".overlay").css("display", "none");	
        },
        error:function(error)  
        {	
        } 
    	});
}

$('#make_pay_cash').on('keyup',function(){
    calculate_ReceiptAmount();
});

//Card Details

function removeCC_row(curRow)
{
	curRow.remove();
	calculate_creditCard_Amount();
}

$('#new_card').on('click', function(){
	$("#cardPayAlert span").remove();
	if(validateCardDetailRow()){
		create_new_empty_cardpay_row();
	}else{
		$("#cardPayAlert").append("<span>Please fill all fields in current row.</span>");
		$('#cardPayAlert span').delay(20000).fadeOut(500);
	}
});
function validateCardDetailRow(){
	var row_validate = true;
	$('#card_details > tbody  > tr').each(function(index, tr) {
		if($(this).find('.card_name').val() == "" || $(this).find('.card_type').val() == "" || $(this).find('.card_no').val() == "" || $(this).find('.card_amt').val() == ""){
			row_validate = false;
		}
	});
	return row_validate;
}

$('#add_card').on('click',function(){
		if(validateCardDetailRow()){
			$('#payment_modes > tbody >tr').each(function(bidx, brow){
				bill_card_pay_row = $(this);
				bill_card_pay_row.find('.CC').html($('.cc_total_amt').html());
				bill_card_pay_row.find('.DC').html($('.dc_total_amt').html());
				bill_card_pay_row.find('#card_payment').val(card_payment.length>0 ? JSON.stringify(card_payment):'');
			});
			$('#card-detail-modal').modal('toggle');
			calculate_ReceiptAmount();
		}else{
			alert("Please fill required fields");
		}
});

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

function create_new_empty_cardpay_row()
{
	var row = "";
	row += '<tr>'
				+'<td><select name="card_details[card_name][]" class="card_name"><option value=2>VISA</option><option value=2>RuPay</option><option value=3>Mastro</option><option value=4>Master</option></select></td>'
				+'<td><select name="card_details[card_type][]" class="card_type"><option value=1>CC</option><option value=2>DC</option></select></td>'
				+'<td><input type="number" step="any" class="card_no" name="card_details[card_no][]"/></td>'
				+'<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]"/></td>' 
				+'<td><a href="#" onClick="removeCC_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>' 
			+'</tr>';
	$('#card_details tbody').append(row);
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
					card_payment.push({'card_name':$(this).find('.card_name').val(),'card_type':$(this).find('.card_type').val(),'card_no':$(this).find('.card_no').val(),'card_amt':$(this).find('.card_amt').val()});
				}
		});
		$('.cc_total_amt').html(parseFloat(cc_amount).toFixed(2));
		$('.dc_total_amt').html(parseFloat(dc_amount).toFixed(2));
		$('.cc_total_amount').html(parseFloat(parseFloat(cc_amount)+parseFloat(dc_amount)).toFixed(2));
}

//Card Details



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
		if($(this).find('.nb_type').val() == "" || $(this).find('.ref_no').val() == "" || $(this).find('.amount').val() == ""){
			row_validate = false;
		}
	});
	return row_validate;
}
function create_new_empty_net_banking_row()
{
	var row = "";
	row += '<tr>'
			+'<td><select name="nb_details[nb_type][]" class="nb_type"><option value=1>RTGS</option><option value=2>IMPS</option></select></td>'
			+'<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td>'
			+'<td><input type="number" step="any" class="amount" name="nb_details[amount][]"/></td>'
			+'<td><a href="#" onClick="removeNb_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
			+'</tr>';
	$('#net_bank_details tbody').append(row);
	$('#net_bank_details > tbody').find('tr:last .cheque_date').focus();
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
		calculate_NB_Amount();
	});
function removeNb_row(curRow)
{
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
					nb_payment.push({'nb_type':$(this).find('.nb_type').val(),'ref_no':$(this).find('.ref_no').val(),'amount':$(this).find('.amount').val()});
				}
		});
		$('.nb_total_amount').html(parseFloat(nb_amount).toFixed(2));
}
$('#add_newnb').on('click',function(){
		if(validateNBDetailRow()){
			$('#payment_modes > tbody >tr').each(function(bidx, brow){
				bill_card_pay_row = $(this);
				bill_card_pay_row.find('.NB').html($('.nb_total_amount').html());
				bill_card_pay_row.find('#nb_payment').val(nb_payment.length>0 ? JSON.stringify(nb_payment):'');
			});
			$('#net_banking_modal').modal('toggle');
			calculatePaymentCost();
		}else{
			alert("Please fill required fields");
		}
});

$('#save_net_banking').on('click',function(){
		if(validateNBDetailRow()){
			$('#payment_modes > tbody >tr').each(function(bidx, brow){
				bill_card_pay_row = $(this);
				bill_card_pay_row.find('.NB').html($('.nb_total_amount').html());
				bill_card_pay_row.find('#nb_payment').val(nb_payment.length>0 ? JSON.stringify(nb_payment):'');
			});
			$('#net_banking_modal').modal('toggle');
			calculate_ReceiptAmount();
		}else{
			alert("Please fill required fields");
		}
});

//Net banking ends

function calculate_ReceiptAmount()
{
	var receipt_type=$("input:radio[name='receipt[receipt_type]']:checked").val();
	var receive_amount=parseFloat(($('.receive_amount').val()!='' || $('.receive_amount').val()>0 ?$('.receive_amount').val() :0));
	var cc=($('.CC').html()!='' ? $('.CC').html():0);
	var dc=($('.DC').html()!='' ? $('.DC').html():0);
	var chq=($('.CHQ').html()!='' ? $('.CHQ').html():0);
	var NB=($('.NB').html()!='' ? $('.NB').html():0);
	var cash=($('#make_pay_cash').val()!='' ? $('#make_pay_cash').val():0);
	var pay_amount=parseFloat(cash)+parseFloat(cc)+parseFloat(dc)+parseFloat(chq)+parseFloat(NB);
	$('.receipt_total_amount').html(pay_amount);
	$('.receipt_bal_amount').html(parseFloat(parseFloat(receive_amount)-parseFloat(pay_amount)).toFixed(2));
	
	if($('.receipt_bal_amount').html()==0)
	{
	    $('#save_receipt').prop('disabled',false);		
	}else{
	    $('#save_receipt').prop('disabled',true);	
	}
}




$('#issue_submit').on('click',function(){
        var allow_submit=false;
        var gift_type = $("input[name='gift[gift_type]']:checked").val();
        var gift_for = $("input[name='gift[gift_for]']:checked").val();
        var id_customer=$("#id_customer").val();
        var id_employee=$("#id_employee").val();
        var select_gift=$("#select_gift").val();
        var vocuher_amount=$("#vocuher_amount").val();
        if(gift_type==1)
        {
            if(gift_for==1 && id_employee=='')
            {
                allow_submit=false;
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Employee"});
            }else if(gift_for==2 && id_customer=='')
            {
                allow_submit=false;
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Customer"});
            }
            else if(vocuher_amount=='' || vocuher_amount==0 )
            {
                allow_submit=false;
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Amount"});
            }
            else{
                allow_submit=true;
            }
        }else if(gift_type==2)
        {
            if($('.receive_amount').val()!='' && $('.receive_amount').val()!=0)
            {
                    var receive_amount=$('.receive_amount').val();
                    var receipt_total_amount=($('.receipt_total_amount').html()=='' || $('.receipt_total_amount').html()==0 ? 0 :$('.receipt_total_amount').html());
                    if(receive_amount==receipt_total_amount)
                    {
                         allow_submit=true;
                    }else{
                         allow_submit=false;
                    }
            }else{
                alert('Please Enter The Received Amount');
            }
            
        }else if(gift_type==3)
        {
            if(select_gift=='' || select_gift==null || select_gift==undefined)
            {
                allow_submit=false;
                alert('Please Select Gift Vocuher');
            }
            else{
                allow_submit=true;
            }
        }
        if(allow_submit)
        {
            var form_data=$('#gift_issue').serialize();
            console.log(form_data);
            $('#issue_submit').prop('disabled',true);
            var url=base_url+ "index.php/admin_gift_vocuher/gift_issue/save?nocache=" + my_Date.getUTCSeconds();
            $.ajax({ 
                url:url,
                data: form_data,
                type:"POST",
                dataType:"JSON",
                success:function(data){
                if(data.status)
                {
                    if(data['gift_type']==3)
                    {
                        window.open( base_url+'index.php/admin_gift_vocuher/get_issue_receipt/?id_gift='+data['insIds'],'_blank');
                    }else{
                        window.open( base_url+'index.php/admin_gift_vocuher/gift_issue/receipt_print/'+data['id'],'_blank');
                    }
                     
                }
                 window.location.reload();
                $("div.overlay").css("display", "none"); 
                },
                error:function(error)  
                {	
                $("div.overlay").css("display", "none"); 
                } 
            });
        }
	
});



//Gift Voucer

function get_gift_voucher_details()
{
	$(".overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_gift_vocuher/gift_master/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"GET",
		dataType: 'json',
		cache:false,
		success:function(data){
		   		var list 	= data.gift_det;
				var access	    = data.access;	
			 var oTable = $('#gift_master').DataTable();
			 oTable.clear().draw();
			 if (list!= null && list.length > 0)
			 {  	
				oTable = $('#gift_master').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],				
						"aaData"  : list,
						"aoColumns": [
						{ "mDataProp": "id_gift_voucher"},
						{ "mDataProp": "name" },
					    { "mDataProp": function ( row, type, val, meta ){
						    if(row.voucher_type==1)
						    {
						        return 'Amount';
						    }else{
						        return 'Weight';
						    }
						}
						},
						{ "mDataProp": "sale_value" },
						{ "mDataProp": "credit_value" },
						{ "mDataProp": function ( row, type, val, meta ){
						active_url =base_url+"index.php/admin_gift_vocuher/update_gift_status/"+(row.status==0?1:0)+"/"+row.id_gift_voucher; 
						return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"
						}
						},
						{ "mDataProp": function ( row, type, val, meta ) {
							 id= row.id_gift_voucher;
							 edit_url=(access.edit=='1'?  base_url+'index.php/admin_gift_vocuher/gift_master/edit/'+id : '#');
							 delete_url=(access.delete=='1' ? base_url+'index.php/admin_gift_vocuher/gift_master/Delete/'+id : '#' );
							 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
							 action_content='<a href="'+edit_url+'" class="btn btn-primary btn-edit" ><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>'
							 return action_content;
							 }
						},
						]
					});			  	 	
				}
			$(".overlay").css("display", "none"); 	
		},
		 error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	
	});
}

 


//Gift Voucer



