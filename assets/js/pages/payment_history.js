var path =  url_params();
var ctrl_page = path.route.split('/');
$(document).ready(function() {
/*$('#historyTable').DataTable( {
"oLanguage": { sLengthMenu:"Show Entries: _MENU_" },
fixedColumns: true,
       "bFilter": false,
       "bSort": false,
       "aaSorting": [[ 0, "desc" ]],
       "sScrollX": "100%",
       responsive:true
} ); */

 get_metalname(); 
giftcard_payment()
get_metatwise_pay();

// payment history based on tab[branches] selection  //HH
 	$(".brn_btn").on("click",function(ev){
    	$(".brn_btn").removeClass("theme-btn-bg");
    	$(this).addClass("theme-btn-bg");
    	$(".pay_card").css("display","none");
    	$(".pay_ac_"+this.value).css("display","revert");
    })

});


payhistory = {	
	getHistory : function() {
		
	$.ajax({
		type: "GET",
		url: baseURL+"paymt/payment_history",
		dataType: "json",
		success: function(data) {
		
			
			
		  
		}
	});
  }
}

function giftcard_payment(){
	
	
	$.ajax({
		type: "GET",
		url: baseURL+"index.php/paymt/ajax_giftdetails",
		dataType: "json",
		success: function(data) {
			console.log(data[0]);
			$('#Giftcard_pay').DataTable( {
				"oLanguage"       : { sLengthMenu:"Show Entries: _MENU_" },
				fixedColumns      : true,
					   "bFilter"  : false,
					   "bSort"    : false,
					   "aaSorting": [[ 0, "desc" ]],
					   "sScrollX" : "100%",
					   "columnDefs": [{"className": "dt-center", "targets": "_all"} ],
					   responsive :true,
					   "aaData"   : data,	
					   "aoColumns": [{
									"data": "id",
									render: function (data, type, row, meta) {
									return meta.row + meta.settings._iDisplayStart + 1;
									}
},
					   
												{ "mDataProp": "payment_mode" },

												{ "mDataProp": "amount" },
												
												{ "mDataProp":  function ( row, type, val, meta ) {				                	 
										return (row.payment_status==1?'Success':
												(row.payment_status==2?'Awaiting':
												(row.payment_status==3?'Failed':
												(row.payment_status==4?'Cancelled':
												(row.payment_status==5?'Return':
												(row.payment_status==6?'Refund':'Pending'
												))))));
							
											

												}},
												
		]} );
		  
		}
	});
	
}

function get_metalname()
{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/paymt/get_metal/',
		dataType:'json',
		success:function(data){
		 var scheme_val =  $('#id_metal').val();
		 
		 	$('#metal_select').append(						
            	 	$("<option></option>")						
            	 	.attr("value", 0)						  						  
            	 	.text('All' )
						
            	 	);
		
		   $.each(data, function (key, item) {					  	
			   		$('#metal_select').append(
						$("<option></option>")
						.attr("value", item.id_metal)						  
						  .text(item.metal )
					);
							   				
			});
			$("#metal_select").select2({
			    placeholder: "Select metal name",
			    allowClear: true
		    });
			
			 $("#metal_select").select2("val",(scheme_val!='' && scheme_val>0?scheme_val:''));
			 
			 $(".overlay").css("display", "none");	
		}
	});
}

$('#metal_select').select2().on("change", function(e) {
			if(this.value!='')
			{	 
				$("#id_metal").val($(this).val());
				var id_metal=$('#id_metal').val();
				get_metatwise_pay(id_metal);
			}
		});
		
		// tab[branches] used so metal file commended and tabl id:  historyTables in view //HH
function get_metatwise_pay(id_metal="")
    { 
   
           $('.overlayy').css("display", "block");
           $.ajax({
	            type: "POST",	
	            url:baseURL+ "index.php/paymt/metal_report?nocache=" + my_Date.getUTCSeconds(),
	            data: {'id_metal':id_metal},			 
	            dataType: 'json',			
	            success:function(data)
	            {
                    $('.overlayy').css("display", "none");
                    oTable = $('#historyTable').dataTable({
	                    "bDestroy": true,
	                    "columnDefs": [{"className": "dt-center", "targets": "_all"}],
	                    "aaData": data,
	                     "order": [],
	                    "aoColumns": [
		                    { "mDataProp": "id_payment" },
		                    { "mDataProp": function ( row, type, val, meta ){
			                    var branch = row.branch_name!=''?row.branch_name:'-';
			                    return (branch);
		                    }},	 
		                    { "mDataProp": "code" },
		                    
		                    
		                    { "mDataProp": function ( row, type, val, meta ){
		                    
		                   
		                     return (row.has_lucky_draw==1 ? row.scheme_group_code : row.code)+' '+(row.scheme_acc_number ==null ? 'Not Allocated': row.scheme_acc_number);
			                    
                             }},
		                    { "mDataProp": function ( row, type, val, meta ){
			               
			                 var wgt = row.scheme_type='Weight Scheme'?row.metal_weight+'g':'-';
			                    return (wgt);
		        			}},
		                    { "mDataProp": "payment_amount" },
		                    { "mDataProp": "add_charges" },
		                    
		                    
		                    { "mDataProp": function ( row, type, val, meta ){
		                    
		                     	row.gst_calc = 0;
								row.gst_amt = 0;
								if(row.gst > 0){
									if(row.gst_type == 1 ){
										var gst_calc = row.payment_amount*(row.gst/100);
										return number_format(row.gst_calc,'2','.','');
										var gst_amt = row.gst_calc;
									}
									else{
										var gst_calc = row.payment_amount-(row.payment_amount*(100/(100+row.gst)));
										return number_format(row.gst_calc,'2','.','');
									}
								}
								else{
								return ' - '
								}
								 
		                    }},
		                    { "mDataProp": "payment_amount" },
		                    { "mDataProp": "payment_mode" },
		                   
		                    { "mDataProp": "date_payment" },
		                    { "mDataProp": "payment_status" },
		                    { "mDataProp": function ( row, type, val, meta ){
			                    var receipt = row.receipt_no!=''?row.receipt_no:'-';
			                    return (receipt);
		                    }},
		                    { "mDataProp": function ( row, type, val, meta ){
			                 if(row.id_pay_status == 1 ){
			                   	    return ('<a href="'+baseURL+'index.php/paymt/generateInvoice/'+row.id_payment+'" target="_blank"  class="btn btn-primary btn-xs">Print</a>');
			                 } else {
			                     return '-'
			                 }
		                    }},

		                 
		                  
                    ]}); 
	            }
            });
       
		}		
