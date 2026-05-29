var path =  url_params();

var ctrl_page = path.route.split('/');
$(document).ready(function() {
    
    
$(".close").click(function(){
    location.reload(true);
});

console.log(ctrl_page[0]);
if(ctrl_page[2]=='payment'){
			
		get_gateway();
			
		}

  


				$('#verifypayment_list1').empty();
				$('#verifypayment_list2').empty();
				$('#verifypayment_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
				$('#verifypayment_list2').text(moment().endOf('month').format('YYYY-MM-DD'));	
        $('#payment-dt-btn').daterangepicker(



            {



              ranges: {



                'Today': [moment(), moment()],



                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],



                'Last 7 Days': [moment().subtract(6, 'days'), moment()],



                'Last 30 Days': [moment().subtract(29, 'days'), moment()],



                'This Month': [moment().startOf('month'), moment().endOf('month')],



                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]



              },



              startDate: moment().subtract(29, 'days'),



              endDate: moment()



            },



        function (start, end) {



          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));


				var id_gateway=$("#id_gateway").val();
				var id_branch=$("#id_branch").val();
			
                    get_payment_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_branch,id_gateway);
				
			 $('#verifypayment_list1').text(start.format('YYYY-MM-DD'));
			 $('#verifypayment_list2').text(end.format('YYYY-MM-DD')); 



          }



        ); 







//select all transactions        



$('#sel_failed_all').click(function(event) {							



	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));



    event.stopPropagation();					     



});



  	



//verify all selected



   $("#check_transaction").click(function(){


     var id_gateway=$("#id_gateway").val();
     if(id_gateway == '')
     {
         alert("Select Payment Gateway");
     }
     else{
   	 // $('form#failed_txn').submit();

      $("div.overlay").css("display", "block");

	  var data = { 'txn_ids[]' : [],'transData' : []};
	  
	  
		var pg_code=$("#id_gateway").val();
		var id_branch=$("#id_branch").val();

		if(pg_code == 3){
			$("input[name='txnid[]']:checked").each(function() {			  
			   transData = {   'txn_id' : $(this).val() , 
							   'name' :$("input[name='name']").val(),
							   'amount':$("input[name='amount']").val(),
							   'mobile':$("input[name='mobile']").val(),
							   'payu_id':$("input[name='payu_id']").val(),
							   'date_payment':$("input[name='date_payment']").val(),
		   					};
			  console.log(transData);
			  data['transData'].push(transData);
			});	
		}else{
			$("input[name='txnid[]']:checked").each(function() {
			  data['txn_ids[]'].push($(this).val());
			});	
		 } 

		 data['pg_code']=pg_code;
		data['id_branch']=id_branch;

		console.log(data);
		



				$.ajax({



					      type: "POST",



						  url: base_url+"index.php/payment/verify/transaction",



						  data: data,



						  sync:false,



						  success: function(response){



							  	 $('#alert_msg').html(response);



							  	 $(".alert").css("display","block"); 



							  	 var id_gateway=$("#id_gateway").val();
								var from_date = $('#verifypayment_list1').text();
								var to_date  = $('#verifypayment_list2').text();
								 var id_branch=$('#id_branch').val();
								get_payment_list(from_date,to_date,id_branch,id_gateway);




							 	/* setTimeout(function(){



							



							         window.location.reload();



							        



							   



							}, 5000);*/



						 }				 



			   });

   }

   });  	



});	







function get_payment_list(from_date="",to_date="",id_branch="",pg_code="")



{



	my_Date = new Date();



	



	$.ajax({



			  url:base_url+ "index.php/get/online/payment?nocache=" + my_Date.getUTCSeconds(),



			 data: ((from_date !='' && to_date !='') || id_gateway!='' ? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch,'pg_code':pg_code}: ''),



			 dataType:"JSON",



			 type:"POST",



			 success:function(data){



			 	



			   			set_payment_list(data);

                   $("div.overlay").css("display", "none");

					  },



					  error:function(error)  



					  {



						 $("div.overlay").css("display", "none"); 



					  }	 



		  });



}


function set_payment_list(data)

{

	 var payment = data.data;

	 $('body').addClass("sidebar-collapse");

	 

	 var oTable = $('#payment_verification_list').DataTable();

	

	     oTable.clear().draw();

			  	 if (payment!= null && payment.length > 0)

			  	  {  	

					  	oTable = $('#payment_verification_list').dataTable({

				                "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,
				                
				                 "dom": 'lBfrtip',
           			             "buttons" : ['excel','print'],
						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },



				                "aaData": payment,

				                "aoColumns": [{ "mDataProp": function(row,type,val,meta){ 

				                     return "<label class='checkbox-inline'><input type='checkbox' name='txnid[]' value='"+row.ref_trans_id+"'/><input type='hidden' name='name' value='"+row.name+"'/><input type='hidden' name='payu_id' value='"+row.payu_id+"'/><input type='hidden' name='mobile' value='"+row.mobile+"'/><input type='hidden' name='amount' value='"+row.payment_amount+"'/> " + row.id_payment +  "</label>"

				                   }				                    

				                },

					                { "mDataProp": function(row,type,val,meta){ 

				                     return "<input type='hidden' name='mobile' value='"+row.date_payment+"'/><input type='hidden' name='date_payment' value='"+row.date_payment+"'/>"+row.date_payment;

				                   }				                    

				                }, 

					                { "mDataProp": "id_transaction" },

					                { "mDataProp": "name" },

					                { "mDataProp": "account_name" },

					                { "mDataProp": "code" },

					                { "mDataProp": function ( row, type, val, meta ){
					                	if(row.has_lucky_draw==0){
					                	return row.group_code+' '+row.scheme_acc_number;
					                	}
					                	else{
					                		return row.code+' '+row.scheme_acc_number;
					                	}
					                }},


					                { "mDataProp": "mobile" },

					                { "mDataProp": "payment_mode" },

					                { "mDataProp": "metal_rate" },

					                { "mDataProp": function ( row, type, val, meta ){
						                		return formatMetalWeight(row.metal_weight);
						                	}},
									 { "mDataProp": "bank_charges" },
									

					                { "mDataProp": "payment_amount" },

					                { "mDataProp": "payment_ref_number" },
					                
					                { "mDataProp": "remark" }
									

					                ] 



				            });			  	 	

					  	 }	

}


 
 
 $('#branch_select').select2().on("change", function(e) {
         
		 
			 switch(ctrl_page[2])

			{

				case 'payment':
				
					
					if(this.value!='')
					{  
						
						 $("#id_branch").val(this.value);
						var from_date = $('#verifypayment_list1').text();
						var to_date  = $('#verifypayment_list2').text();
						var id=$('#id_branch').val()
						var pg_code=$("#id_gateway").val();
						if(pg_code!='')
						{
							get_payment_list(from_date,to_date,id,pg_code);
						}
					}
					 break;		
			
			     } 
		  
   });

//branch_name



function get_gateway()
{ 

	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		
		  url: base_url+"index.php/admin_payment/PaymentGateway",
		dataType:'json',
		success:function(data){
		console.log(data);
		 var scheme_val =  $('#id_branch').val();

	
		   $.each(data, function (key, item) {					  	
			  
					$('#gateway_select').append(
						$("<option></option>")
						.attr("value", item.pg_code)						  
						  .text(item.pg_name)
						  
					);
					
			});
		  
		   
			
			$("#gateway_select").select2({
			    placeholder: "Select Gateway",
			    allowClear: true
		    });
			
			 $("#gateway_select").select2("val",(scheme_val!='' && scheme_val>0?scheme_val:''));
			 $(".overlay").css("display", "none");	
		}
	});
}

$('#gateway_select').on('change',function(e){
if(this.value!='')
{
    $("#branch").css('display','block');
    $("#verify_date").css('display','block');
	$("#id_gateway").val(this.value);
	var pg_code=$("#id_gateway").val();
	var from_date = $('#verifypayment_list1').text();
	var to_date  = $('#verifypayment_list2').text();
	 var id_branch=$('#id_branch').val();
	get_payment_list(from_date,to_date,id_branch,pg_code);


}
else
{
	$("#id_gateway").val('');
	$("#branch").css('display','none');
    $("#verify_date").css('display','none');
		
}
});
