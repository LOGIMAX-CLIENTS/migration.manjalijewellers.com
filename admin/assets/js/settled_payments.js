var pathArray = window.location.pathname.split( 'php/' );
var ctrl_page = pathArray[1].split('/');
$(document).ready(function() {
	$('body').addClass("sidebar-collapse");
	  $('#fetch_settlement').click(function(e){
	  	    form_data = $('#sync_settled_payments').serialize();
	  		if(($('#request_date').val() == "")){
				 msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong> Enter date for which Settlement Details are required</div>';	
					
				$("div.overlay").css("display", "none"); 				      
				        //stop the form from submitting
				         $('#error-msg').html(msg);			         

		 	  return false;	
			}else{
				fetchSettled_payment(form_data);
			}
			
	  });
	  
	  $('#sync_settled_pay').click(function(e){
	  	    updateSettled_payment();
	  });
	
});

function fetchSettled_payment(form_data)
{
	 my_Date = new Date();
	 $("div.overlay").css("display", "block"); 
	 $.ajax({
			  url:base_url+ "index.php/admin_payment/fetch_settled_payments?nocache=" + my_Date.getUTCSeconds(),
			  data: (form_data),
			  type:"POST",
			  dataType:"JSON",
			  success:function(data){
				 		  msg='<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Message!</strong>  '+data.msg+'</div>';	
						 $('#error-msg').html(msg);	
						 $('#total_settlement').text(data.transactions.lenght);	
			   			 $("div.overlay").css("display", "none"); 
			   			 fetchedSettlement_list(data.transactions)
					  },
					  error:function(error)  
					  {  console.log(2);
						 $("div.overlay").css("display", "none"); 
					  }	 
		   });
}

function fetchedSettlement_list(payment)
{
	 var oTable = $('#settled_txns').DataTable();	 	 
     oTable.clear().draw();
  	 if (payment!= null && payment.length > 0)
  	 {  	
		  	oTable = $('#settled_txns').dataTable({
		                "bDestroy": true,
		                "bInfo": true,
		                "bFilter": true,
		                "bSort": true,
		                "order": [[ 0, "desc" ]],
		                "aaData": payment,
		                "aoColumns": [   { "mDataProp": "txnid" },
			               				 { "mDataProp": "gateway_id" },
			               				 { "mDataProp": "amount" },
			               				 { "mDataProp": "requestdate" },
			               				 { "mDataProp": function ( row, type, val, meta ) {	
							                	 content = (row.is_settled == 1 ?'Settled':'Not Settled');
							                	return content;
							                }}],
						/*"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull )
		                  switch(aData['payment_status'])
						  {
						     case 'Approved':
								$(nRow).css('color', 'green');
							   break;
							 case 'Rejected':
							     $(nRow).css('color', 'red');
							   break;
						  }
							
						}*/
		            });			  	 	
		}	
}  

function updateSettled_payment()
{
	 my_Date = new Date();
	 $("div.overlay").css("display", "block"); 
	 $.ajax({
			  url:base_url+ "index.php/admin_payment/updateGtwaySettlement?nocache=" + my_Date.getUTCSeconds(),
			  type:"GET",
			  dataType:"JSON",
			  success:function(data){
			  			 if(data.pending_avail){
							 updateSettled_payment();
						 }else{
						 	 /* var oTable = $('#settled_txns').DataTable();	 	 
    						  oTable.clear().draw();*/
						 	  $("div.overlay").css("display", "none");
						 	  // location.reload(true);
						 }
				 		 msg = '<div class = "alert alert-success"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Message!</strong>  '+data.msg+'</div>';	
						 $('#alert-msg').html(msg);	
					  },
					  error:function(error)  
					  {  console.log(2);
						 $("div.overlay").css("display", "none"); 
					  }	 
		   });
}

