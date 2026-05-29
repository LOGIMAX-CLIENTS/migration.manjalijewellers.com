function url_params()
	{
		var url = window.location.href;
		var path=window.location.pathname;
		var params = path.split( 'php/' );
		
		return {'url':url,'pathname':path,'route':params[1]};
	}

var path =  url_params();

var ctrl_page = path.route.split('/');
$(document).ready(function() {
	$(".metal_btn").on("click",function(ev){
    	$(".metal_btn").removeClass("theme-btn-bg");
    	$(this).addClass("theme-btn-bg");
    	$(".sch_card").css("display","none");
    	$(".sch_ac_"+this.value).css("display","revert");
    })
    
    $(".branch_btn").on("click",function(ev){
    	$(".branch_btn").removeClass("theme-btn-bg");
    	$(this).addClass("theme-btn-bg");
    	$(".sch_card").css("display","none");
    	$(".sch_ac_"+this.value).css("display","revert");
    })
    
    $('.open-modal').click(function(e) 
	{
		e.preventDefault();
		var link = $(this).data('href');
	    $('.modal').find('.btn-confirm').attr('href',link);
	});
	
	/*get_metalname(); 
	get_metatwise_acc();*/
	
	/*$(document).ready(function(){
	  $("#demo").on("hide.bs.collapse", function(){
	    $(".btn").html('<span class="glyphicon glyphicon-collapse-down"></span> Open');
	  });
	  $("#demo").on("show.bs.collapse", function(){
	    $(".btn").html('<span class="glyphicon glyphicon-collapse-up"></span> Close');
	  });
	});*/
	
	
// closed accounts based on tab[branches] selection  //HH
 	$(".brn_btn").on("click",function(ev){
    	$(".brn_btn").removeClass("theme-btn-bg");
    	$(this).addClass("theme-btn-bg");
    	$(".pay_card").css("display","none");
    	$(".closed_ac_"+this.value).css("display","revert");
    })



/*	$('#closed_acc').DataTable( {


		"oLanguage": { sLengthMenu:"Show Entries: _MENU_" },


		"order"	   : [[3,'desc']],


		 fixedColumns: true,


		 "sScrollX": "100%"


	} ); */
	
	$('.progress .progress-bar').css("width",
                function() {
                    return $(this).attr("aria-valuenow") + "%";
                }
        )

       $('#fix_rate').on('click',function(){
           var rate_select = $('#rate_select').val();
           if(rate_select == 1)
           {
                $.ajax({
                    url:baseURL+"index.php/chitscheme/rateFixing_otp/",
                    type : "get",
                    dataType: "json",
                    success:function(data)
                    {
                        console.log(data.success);
                        if(data.success==true)
                        {
                                $('#otp_modal').modal({
    								backdrop: 'static',
    								keyboard: false
    							 });
                        }
                        
                    }
                });
           }
           else if(rate_select == 2)
           {
               var start_date = $('#start_date').val();
               $.ajax({
                    data : {'start_date':start_date},
                    url:baseURL+"index.php/chitscheme/getRatesByJoin/",
                    type : "POST",
                    dataType: "json",
                    success:function(data)
                    {
                        console.log(data);
                 var content ="<div class='pay-content'><div id='error-msg' style='color:red;'></div></div>"
			    
                var metal_rates='<div class="rate-table"><table class="table table-bordered table-striped table-responsive text-center">'+
			       '<tr><th colspan="3" style="text-align:center" ><h3 > Gold 22k 1gm rate </h3></th></tr>'+
			        '<tr><td><div style="float:left">Select prefered Metal Rate</div></td><td></td></tr>'+ 
			                           '<tr><th>Gold Rate</th><th>Date</th></tr>';
							   
			$.each(data, function() {
				        console.log(this.goldrate_22ct);
					 	metal_rates +="<tr><td><input type='radio' name='goldrate' value='"+this.goldrate_22ct+"' />	 "+this.goldrate_22ct+" </td><td>"+this.add_date+" </td></tr>";
					 
				});	   
				metal_rates +='<table></div>';
	            $("#ratefixByHistory").prop('disabled',true);
				$('#rateFixModal .modal-body .pay-content').remove();
				$('#rateFixModal .modal-body').append(content);
				$('#rateFixModal .modal-body .pay-content').append(metal_rates);
                $('#rateFixModal').modal('show', {backdrop: 'static'});
                    }
               });
           }
       });
       
       
       //selected gold rate
	
	    $(document).on('change', '[type=radio][name=goldrate]', function(ev)
	    {
	        ev.preventDefault();
	        var selected_rate= $(this).val();
	        if(selected_rate != undefined)
	        {
	            $("#ratefixByHistory").prop('disabled',false);
	        }
        });
        
         $('#ratefixByHistory').on('click',function(){
             //ev.preventDefault();
             var selected_rate = $('input[name="goldrate"]:checked').val();
              console.log(selected_rate);
              if(selected_rate == undefined)
              {
                  $('#error-msg').html('Select metal rate to proceed');
              }
              else
              {
                  $("#ratefixByHistory").prop('disabled',false);
                  $('#rateFixModal').on('hidden', function () {
                    });
                  $.ajax({
                    url:baseURL+"index.php/chitscheme/rateFixing_otp/",
                    type : "get",
                    dataType: "json",
                    success:function(data)
                    {
                        console.log(data.success);
                        $('#metal_rate').val(selected_rate);
                        if(data.success==true)
                        {
                                $('#otp_modal').modal({
    								backdrop: 'static',
    								keyboard: false
    							 });
                        }
                        
                    }
                });
              }
              
	        });
       
       $('#submit').on('click',function(){
           var otp=$('#otp').val();
            var id_scheme_account=$('#id_scheme_account').val();
            var metal_rate = $('#metal_rate').val();
            console.log(metal_rate);
             var amount=$('#amount').val();
              $.ajax({
                url:baseURL+"index.php/chitscheme/submit_ratefix/",
                type : "post",
                data :{'otp':otp, 'sch_ac_no' : $('#scheme_acc_number').val() , 'id_branch' : $('#id_branch').val(),'id_scheme_account':id_scheme_account,'amount':amount,'metal_rate':metal_rate},
                dataType: "json",
               
                success:function(data)
                {
                    //console.log(data.success);
                       if(data.success==false)
                       {
                           alert(data.msg);
                           $('#otp').val('');
                       }
                       else
                       {
                            alert(data.msg);
                          location.reload(true);
                       }
                    
                }
            });
       });
       
       $('#resendOTP').on('click',function(){
             $.ajax({
                url:baseURL+"index.php/chitscheme/rateFixing_otp/",
                type : "get",
                dataType: "json",
                success:function(data)
                {
                    console.log(data.success);
                    if(data.success==true)
                    {
                            alert(data.msg);
                            $('#otp_modal').modal({
								backdrop: 'static',
								keyboard: false
							 });
                    }
                    
                }
            });
           
       });
       
       function RateFixing_Data()
       {
             var metal_rate=$('#metal_rate').val();
             var scheme_acc_number=$('#scheme_acc_number').val();
             var id_scheme_account=$('#id_scheme_account').val();
              $.ajax({
                url:"https://121.200.48.187/EJAPIS/RateFixing",
               beforeSend: function (xhr) {
                   xhr.setRequestHeader('Authorization', make_base_auth('re0625@ejindia.com','karthik014'));
                },
                
                type : "post",
                data :{'scheme_acc_number':scheme_acc_number,'metal_rate':metal_rate,'id_scheme_account':id_scheme_account},
                dataType: 'json',
                success:function(data)
                {
                    if(data.success==true)
                    {
                        alert(data.msg);
                        location.reload(true);
                    }
                }
            });
       }
       
function make_base_auth(user, password) {
    var tok = user + ':' + password;
    var hash = btoa(tok);
    return 'Basic ' + hash;
}
     
     
     
     function get_metalname()
{ 
	$(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: baseURL+'index.php/chitscheme/get_metal/',
		dataType:'json',
		success:function(data){
		 var scheme_val =  $('#id_metal').val();
		
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
				get_metatwise_acc(id_metal);
			}
		});
		
		
	function get_metatwise_acc(id_metal="")
    { 
   
           $('.overlayy').css("display", "block");
           $.ajax({
	            type: "POST",	
	            url:baseURL+ "index.php/chitscheme/metal_report?nocache=" + my_Date.getUTCSeconds(),
	            data: {'id_metal':id_metal},			 
	            dataType: 'json',			
	            success:function(data)
	            {
                    $('.overlayy').css("display", "none");
                    oTable = $('#myschemes_list').dataTable({
	                    "bDestroy": true,
	                    "columnDefs": [{"className": "dt-center", "targets": "_all"}],
	                    "aaData": data,
	                     "order": [],
	                    "aoColumns": [
		                    { "mDataProp": "id_scheme_account" },
		                    { "mDataProp": function ( row, type, val, meta ){
			                    var branch = row.branch_name!=''?row.branch_name:'-';
			                    return (branch);
		                    }},	   
		                    { "mDataProp": "scheme_acc_number" },
		                    { "mDataProp": "account_name" },
		                    { "mDataProp": "start_date" },
		                    { "mDataProp": "payable" },
		                    { "mDataProp": function ( row, type, val, meta ){
			                    var install = row.one_time_premium==1?'<span class="badge bg-green">'+row.total_installments+'</span>':'<span class="badge bg-green">'+row.paid_installments+'/'+row.total_installments+'</span>';
			                    return (install);
		                    }},
		                   { "mDataProp": function ( row, type, val, meta ){
			                    if(row.one_time_premium==1){		        						
		        					return	(row.scheme_type=='Flexible' || row.scheme_type=='Amount to Weight' ? row.currency_symbol+ ' ' +number_format(row.payable,'2','.','') :  row.payable +'g/month');		        							
		        							 }
		        				else if(row.scheme_type=='Flexible'){ 		        				
			        				return (row.currency_symbol+ ' ' +row.total_paid_amount - row.paid_gst);
			        			}else{
			        			     return (row.currency_symbol+ ' ' +row.total_paid_amount);
			        			}
		        			}},
		                    { "mDataProp": function ( row, type, val, meta ){
			                    var wgt = row.total_paid_weight==0?'-':row.total_paid_weight +'g';
			                    return (wgt);
		                    }},
		                    { "mDataProp": function ( row, type, val, meta ){
			                    var rate_fix = row.one_time_premium==1?row.rate_fixed_in:row.rate_fixed_in ?'-' :row.rate_fixed_in;
			                    return (rate_fix);
		                    }},	
		                    { "mDataProp": function ( row, type, val, meta ){
			                    var status = row.is_closed==1?'<span class="label">Closed</span>':'<span class="label label-success">Active</span>';
			                    return (status);
		                    }},
		                    { "mDataProp": function ( row, type, val, meta ){
			                    return ((row.one_time_premium==0 && row.paid_installments>0) ?('<a href="'+baseURL+'index.php/chitscheme/scheme_account_report/'+row.id_scheme_account+'" target="_blank"  class="btn btn-primary btn-xs">View</a>'): '<a href="#confirm-delete" data-href="'+baseURL+'index.php/chitscheme/delete_account/'+row.id_scheme_account+'" class="btn btn-xs btn-del btn-danger" data-toggle="modal"><i class="fa fa-trash"></i></a>');
		                    }},
		                    { "mDataProp": function ( row, type, val, meta ) {
						   		return ((row.one_time_premium==0 && row.paid_installments>0) ?('<a href="'+baseURL+'index.php/chitscheme/scheme_account_report/'+row.id_scheme_account+'" target="_blank"  class="btn btn-primary btn-xs">View</a>'): '<a href="#confirm-delete" data-href="'+baseURL+'index.php/chitscheme/delete_account/'+row.id_scheme_account+'" class="btn btn-xs btn-del btn-danger" data-toggle="modal"><i class="fa fa-trash"></i></a>');
                        	}}, 
                    ]}); 
	            }
            });
       
		} 
 
});	


