function url_params()
	{
		var url = window.location.href;
		var path=window.location.pathname;
		var params = path.split( 'php/' );
		
		return {'url':url,'pathname':path,'route':params[1]};
	}


$(document).ready(function() {
    
    var path =  url_params();
    var ctrl_page = path.route.split('/');
    var date = new Date(); 
   // var from_date=(date.getFullYear()+"-"+(date.getMonth() - 1)+"-"+date.getDate());
    var from_date=((date.getFullYear() - 1)+"-"+(date.getMonth())+"-"+date.getDate());
    var to_date = date.toISOString().slice(0, 10);
    $('#ranges_to').val(to_date);
    
    


    var d = new Date();
    let lastOneYr = d.setDate(d.getDate() - 360);
    lastOneYr = new Date(lastOneYr).toISOString().slice(0, 10);
    $('#ranges_from').val(lastOneYr);
    //$('#spin').css('display','block');
  
   // console.log(to_date);
    if(ctrl_page[0] == 'dashboard')
    {
        get_dashboard(from_date,to_date);
        get_transaction_details(from_date,to_date);
    }
    var from = $('#ranges_from').val();
    var to = $('#ranges_to').val();
       if(from != '' && to != '')
        {
            fromDate = from;
            toDate = to;
        }else{
            fromDate = from_date;
            toDate = to_date;
        }
    $('#ref').on('click',function(){
        var from = $('#ranges_from').val();
        var to = $('#ranges_to').val();
        get_referal_details(from,to);
    });
    $('#settle').on('click',function(){
        get_settlement_details(fromDate,toDate);
        get_summary_details(fromDate,toDate);
    });
   
   $('#send_date').on('click',function(){
        
       var from = $('#ranges_from').val();
       var to = $('#ranges_to').val();
       var cur_date =  new Date();
       if(from != '' && to != '')
       {
           if(from > cur_date || from > to){
               alert("Enter valid from date");
              // $.toaster({ priority : 'danger', title : 'Alert!', message : ''+"</br> Enter valid from date"});
           }else if(to > cur_date){
               alert("Enter valid to date");
              // $.toaster({ priority : 'danger', title : 'Alert!', message : ''+"</br> Enter valid to date"});
           }else{
           
           $('.overlay').css('display','block');
           setTimeout(function(){
            $('.overlay').css('display','none');
           }, 5000); 
           get_dashboard(from,to);
           get_transaction_details(from,to);
           get_referal_details(from,to);
           get_settlement_details(from,to);
           get_summary_details(from,to);
           
           }
       }
   });
   
   if(ctrl_page[1] == 'conversionList' && ctrl_page[0] == 'dashboard'){
       getConversion(from_date,to_date);
   }
   if(ctrl_page[1] == 'referralList' && ctrl_page[0] == 'dashboard'){
       getReferrals(from_date,to_date);
   }
   if(ctrl_page[1] == 'unpaidList' && ctrl_page[0] == 'dashboard'){
       getunpaid(from_date,to_date);
   }
   if(ctrl_page[1] == 'settlementList' && ctrl_page[0] == 'dashboard'){
       getsettlement(from_date,to_date);
   }
   
    
});

function get_dashboard(from_date,to_date)
{
    $.ajax({
		   url:baseURL+"index.php/dashboard/getDashboard",
		   type : "POST",
		   dataType: "json",
		   data:{'from_date':from_date,'to_date':to_date},
		   success : function(data) {
		       console.log(data);
		        var dashboard_data = '<table class="bold bold1" style="width:100%; border: 1px solid #8C1F48; font-size: 15px;border-radius:15px;border-collapse: separate;">';
              
                 dashboard_data += '<tr> <td style="text-align: center;border-right: 1px solid #8C1F48;border-bottom: 1px solid #8C1F48;">'+data.data.referrals+'</td>  <td style="text-align: center; border-right: 1px solid #8C1F48;border-bottom: 1px solid #8C1F48;">'+data.data.conversions+'</td><td style="text-align: center; border-right: 1px solid #8C1F48;border-bottom: 1px solid #8C1F48;">'+data.data.unpaid+'</td><td style="text-align: center; border-bottom: 1px solid #8C1F48;">₹'+data.data.tot_earned.tot_cash_point+'</td> </tr><tr style="border: 1px solid #8C1F48;"> <td style="text-align: center; border-right: 1px solid #8C1F48;" rowspan="3"><a href="'+baseURL+'index.php/dashboard/referralList">Referrals</a></td>  <td style="text-align: center; border-right: 1px solid #8C1F48;" rowspan="3" ><a href="'+baseURL+'index.php/dashboard/conversionList">Conversions</a></td><td style="text-align: center; border-right: 1px solid #8C1F48;" rowspan="3"><a href="'+baseURL+'index.php/dashboard/unpaidList">Unpaid</a></td><td style="text-align: center;" rowspan="3">Total Earnings</td> </tr>';
        
                 dashboard_data += '</table>';
                 
                $('#dashboard').html(dashboard_data);
                $('#tot_cash_point').html('₹'+data.data.tot_earned.outstanding);
		   },
		   error : function(error){
			 
			   console.log(error);
		   }
		});
}
   
 
function get_transaction_details(from_date,to_date){
		
		$.ajax({
		   url:baseURL+"index.php/dashboard/getCusLoyaltyTrans",
		   type : "POST",
		   dataType: "json",
		   data:{'from_date':from_date,'to_date':to_date,'type':'transactions'},
		   success : function(data) {
		       console.log(data);
		      set_transaction_data(data);
		   },
		   error : function(error){
			 
			   console.log(error);
		   }
		});
}	

function set_transaction_data(data)
{
    
    if(data.data.length > 0)
    {
       
        var accno;
        var trans_data = '<table style="width:100%; border: 1px solid #fff; font-size: 14px; border-collapse: collapse;">';
        $.each(data.data, function (key, val) {
            if(val.trans_type == 'Settled Commission')
            {
                accno = '-';
            }
          else{
              accno = val.scheme_acc_number;
          }
          if(key == (data.data).length -1 ){
              style='';
          }else{
              style="style='border-bottom:1.5px solid #8C1F48;'";
          }
         trans_data += '<tr '+style+'><td class="transaction_img"><img src="'+baseURL+'assets/img/transaction.png"></td> <td class="transact-text">'+accno+'<br/><b>'+val.trans_type+'</b></td><td class="transact-text">'+val.customer_name+'<br/><b>'+val.mobile+'</b></td><td class="transact-text"><b>'+val.trans_type_sign+' ₹ '+val.cash_point+'</b><br/>'+val.cr_date+'</td> </tr>';
        });
         trans_data += '</table>';
        $('#bill').html(trans_data);
        if(data.data.length >= 5)
         {
             var view_data = '<a href="'+baseURL+'index.php/dashboard/conversionList" class="col-md-2 view_more">View More</a>';
             $('#view_trans_data').html(view_data);
         }
    }
    else
    {
        
        $('#bill').html(data.message);
    }
    
}

function get_referal_details(from_date,to_date)
{
    $.ajax({
		   url:baseURL+"index.php/dashboard/getAgentReferralsList",
		   type : "POST",
		   dataType: "json",
		   data:{'from_date':from_date,'to_date':to_date,'type':'referral','last_id': 0},
		   success : function(data) {
		       
		      if(data.data.length > 0)
    {
        var referal_data = '<table style="width:100%; border: 1px solid #fff; font-size: 14px; border-collapse: collapse;">';
        $.each(data.data, function (key, val) {
         if(key == (data.data).length -1 ){
              style='';
          }else{
              style="style='border-bottom:1.5px solid #8C1F48;'";
          }
         referal_data += '<tr '+style+'><td class="transaction_img1"><img src="'+baseURL+'assets/img/transaction.png"></td> <td class="transact-text">'+val.scheme_acc_number+'<br/><b>'+val.account_name+'</b></td><td class="transact-text"><b>'+val.trans_type_sign+' ₹ '+val.cash_point+'</b><br/>'+val.cr_date+'</td> </tr>';
         
         //referal_data += '<tr><td class="col-md-3"><img src="'+baseURL+'assets/img/transaction.png"></td> <td>'+val.trans_type+'</td> <td>'+val.scheme_acc_number+'</td> <td>₹ '+val.cash_point+'</td><td>'+val.cr_date+'</td> </tr>';
        });
         referal_data += '</table>';
         
        $('#referals').html(referal_data);
        if(data.data.length >= 5)
         {
             var view_data = '<a href="'+baseURL+'index.php/dashboard/referralList" class="col-md-2 view_more">View More</a>';
             $('#view_ref_data').html(view_data);
         }
    }
    else
    {
        $('#referals').html(data.message);
    }
		   },
		   error : function(error){
			 
			   console.log(error);
		   }
		});
}

function get_settlement_details(from_date,to_date)
{
    $.ajax({
	   url:baseURL+"index.php/dashboard/getInfSettledData",
	   type : "POST",
	   dataType: "json",
	   data:{'from_date':from_date,'to_date':to_date,'type':'list','last_id': 0},
	   success : function(data) {
	        if(data.data.length > 0)
            {  
                var settlement_data = '<table style="width:100%; border: 1px solid #fff; font-size: 14px; border-collapse: collapse;">';
                $.each(data.data, function (key, val) {
                    settlement_data += '<tr><td class="transaction_img2"><img src="'+baseURL+'assets/img/transaction.png"></td> <td class="transact-text"><b>'+val.status+'</b></td>  <td class="transact-text"><b>₹ '+val.settlement_pts+'</b></td><td class="transact-text"><b>'+val.settlement_date+'</b></td> </tr>';
                });
                settlement_data += '</table>';
                $('#settlements').html(settlement_data);
                if(data.data.length >= 5)
         {
             var view_data = '<a href="'+baseURL+'index.php/dashboard/conversionList" class="col-md-2 view_more">View More</a>';
             $('#view_settle_data').html(view_data);
         }
            }
            else
            {
                $('#settlements').html(data.message);
            }
	   },
	   error : function(error){
		   console.log(error);
	   }
	});
}

function get_summary_details(from_date,to_date)
{
    $.ajax({
	   url:baseURL+"index.php/dashboard/getInfSetlSummmary",
	   type : "POST",
	   dataType: "json",
	   data:{'from_date':from_date,'to_date':to_date,'type':'list','last_id': 0},
	   success : function(data) {
	        if(data)
            {
                var settled;
                if(data.data.settled === null)
                {
                    settled = '-';
                }else{
                    settled = '₹ '+data.data.settled;
                }
                var summary_data = '<table style="width:100%; border: 1px solid #fff; font-size: 16px; border-collapse: collapse;text-align:center">';
                summary_data += '<tr> <td><b>₹ '+data.data.earned+'</b></td>  <td><b>'+settled+'</b></td><td><b>₹ '+data.data.outstanding+'</b></td> </tr><tr> <td>Total Earnings</td>  <td>Received Settlement</td><td>Balance Settlement</td> </tr>';
                summary_data += '</table>';
                $('#settlement_summary').html(summary_data);
            }
            else
            {
                $('#settlement_summary').html(data.message);
            }
	   },
	   error : function(error){
		   console.log(error);
	   }
	});
}




function getConversion(from_date,to_date){

     $.ajax({
	   url:baseURL+"index.php/dashboard/getConversionData",
	    data:{'from_date':from_date,'to_date':to_date},
	   type : "POST",
	   dataType: "json",
	  
	   success : function(data) {
	        if(data)
            {
                setConversionList(data);
            }
            else
            {
                 alert("0");
            }
	   },
	   error : function(error){
		   console.log(error);
	   }
	});
 
} 

function setConversionList(data)	
{
    
   var conversion = data.conversion;
   //var access = data.access;
   var oTable = $('#conversion_table').DataTable();


	 oTable.clear().draw();
   	 if (conversion!= null && conversion.length > 0)
	 {
	 	oTable = $('#conversion_table').dataTable({
						
						"bDestroy": true,
						
						"bInfo": true,
						
						"bFilter": true,
						
						"bSort": true,
						
						"dom": 'lBfrtip',
						
           		        "buttons" : ['excel','print'],
						
						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						
				        "aaData": conversion,
						
			            "order": [[ 0, "desc" ]],
						
				        "aoColumns": [{"mDataProp": "cus_name"},
				                      {"mDataProp": "mobile"},
				                      {"mDataProp": "scheme_acc_number"},
				                      {"mDataProp": "account_name"},
				                      {"mDataProp": "date_add"},
				                      {"mDataProp": "payment_amount"},
				                      {"mDataProp": "date_payment"},
				                      {"mDataProp": "cash_point"},
				                      {"mDataProp": "unsettled_cash_pts"},
				                      {"mDataProp": "status"}

					                  
					
 									] 
				            });	
	 }  
}	

function getReferrals(from_date,to_date){

     $.ajax({
	   url:baseURL+"index.php/dashboard/getReferralData",
	   data:{'from_date':from_date,'to_date':to_date},
	   type : "POST",
	   dataType: "json",
	  
	   success : function(data) {
	        if(data)
            {
                
                setReferralList(data);
            }
            else
            {
                 alert("0");
            }
	   },
	   error : function(error){
		   console.log(error);
	   }
	});
 
} 

function setReferralList(data)	
{
   
   var referrals = data.referrals;
   var oTable = $('#referral_list').DataTable();


	 oTable.clear().draw();
   	 if (referrals!= null && referrals.length > 0)
	 {
	 	oTable = $('#referral_list').dataTable({
						
						"bDestroy": true,
						
						"bInfo": true,
						
						"bFilter": true,
						
						"bSort": true,
						
						"dom": 'lBfrtip',
						
           		        "buttons" : ['excel','print'],
						
						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						
				        "aaData": referrals,
						
			            "order": [[ 0, "desc" ]],
						
				        "aoColumns": [{"mDataProp": "cus_name"},
				                      {"mDataProp": "mobile"},
				                      {"mDataProp": "scheme_acc_number"},
				                      {"mDataProp": "account_name"},
				                      {"mDataProp": "date_add"},
				                     

					                  
					
 									] 
				            });	
	 }  
}	



function getunpaid(from_date,to_date){

     $.ajax({
	   url:baseURL+"index.php/dashboard/getUnpaidData",
	    data:{'from_date':from_date,'to_date':to_date},
	   type : "POST",
	   dataType: "json",
	  
	   success : function(data) {
	        if(data)
            {
                setUnpaidData(data);
            }
            else
            {
                 alert("0");
            }
	   },
	   error : function(error){
		   console.log(error);
	   }
	});
 
} 



function setUnpaidData(data)	
{
    
   var unpaid = data.unpaid;
   //var access = data.access;
   var oTable = $('#unpaid_list').DataTable();


	 oTable.clear().draw();
   	 if (unpaid!= null && unpaid.length > 0)
	 {
	 	oTable = $('#unpaid_list').dataTable({
						
						"bDestroy": true,
						
						"bInfo": true,
						
						"bFilter": true,
						
						"bSort": true,
						
						"dom": 'lBfrtip',
						
           		        "buttons" : ['excel','print'],
						
						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						
				        "aaData": unpaid,
						
			            "order": [[ 0, "desc" ]],
						
				        "aoColumns": [{"mDataProp": "cus_name"},
				                      {"mDataProp": "mobile"},
				                      {"mDataProp": "scheme_acc_number"},
				                      {"mDataProp": "account_name"},
				                      {"mDataProp": "date_add"},
				                      

					                  
					
 									] 
				            });	
	 }  
}

   
   function getsettlement(from_date,to_date){

     $.ajax({
	   url:baseURL+"index.php/dashboard/getsettlementData",
	    data:{'from_date':from_date,'to_date':to_date},
	   type : "POST",
	   dataType: "json",
	  
	   success : function(data) {
	        if(data)
            {
                setSettlementData(data);
            }
            else
            {
                 alert("0");
            }
	   },
	   error : function(error){
		   console.log(error);
	   }
	});
 
}   
       
       
   function setSettlementData(data)	
{
   
   var settlements = data.settlements;
   var oTable = $('#settlement_list').DataTable();


	 oTable.clear().draw();
   	 if (settlements!= null && settlements.length > 0)
	 {
	 	oTable = $('#settlement_list').dataTable({
						
						"bDestroy": true,
						
						"bInfo": true,
						
						"bFilter": true,
						
						"bSort": true,
						
						"dom": 'lBfrtip',
						
           		        "buttons" : ['excel','print'],
						
						"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
						
				        "aaData": settlements,
						
			            "order": [[ 0, "desc" ]],
						
				        "aoColumns": [{"mDataProp": "settlement_pts"},
				                      {"mDataProp": "settlement_date"},
				                      {"mDataProp": "status"}
 									] 
				            });	
	 }  
}	
   
       
      
       
      
      
       

     
     
     
  



