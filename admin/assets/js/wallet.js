var path =  url_params();
var ctrl_page = path.route.split('/');
$(document).ready(function() {
	
	if(ctrl_page[1]=='master' && (ctrl_page[2]=='add' || ctrl_page[2]=='edit'))
	{
		//for expanding sidebar
		$('body').removeClass("sidebar-collapse");
		
		    var dateToday = new Date();	
		    
			$('#effective_date').datepicker({
               format: 'dd-mm-yyyy',
                  "setValue": new Date(),
                  "autoclose": true
              
            }).datepicker("setDate", dateToday);
            
             $("input[name='wallet[type]']:radio").change(function () {
            	  if(this.value ==0)
            	  {    
				  	$('#value').prop('readonly',true);
				  	$('#value').val($('#currency').val());
				  	 $('#currency').focus();
				  }
				  else
				  {
				   	$('#value').prop('readonly',false);
				    $('#value').val('');
				    $('#value').focus();
				  }
				  
            });   
                     
    
            
          
          
            $('#currency').bind('change keyup', function(e){
            	var type = parseInt($("input[name='wallet[type]']:checked", '#wallet_master').val());
            
                if(type == 0)
                {
					$('#value').val(this.value);
					
				}	
				
			 });
           
		
	}
	else if(ctrl_page[1]=='master' && ctrl_page[2]=='list')
	{ 
    	
		//for collapse sidebar
		$('body').addClass("sidebar-collapse");
		//for loading list
	    load_setting_list();
	}

    if(ctrl_page[1]=='account')
	{ 
		if(ctrl_page[2]=='add' || ctrl_page[2]=='edit')
		{
			//for expanding sidebar
			$('body').removeClass("sidebar-collapse");
			if(ctrl_page[2]=='add')
			{
				get_customers('add');
				get_employee('add');
			}
			else
			{
				get_customers('edit');
				get_employee('edit');
			}

			var dateToday = new Date();	
		    
			$('#issue_date').datepicker({
               format: 'dd-mm-yyyy',
                  "setValue": new Date(),
                  "autoclose": true
              
            }).datepicker("setDate", dateToday);
            
            
          $('#customer_name').select2().on("change", function(e) {
		          		         
		          if(this.value!='')
		          {
		          	 $("#id_customer").val(this.value);
				  	 
				  }
				
				  
		   });
		   
		   $('#employee_name').select2().on("change", function(e) {
		          		       

							   console.log(this.value);
		          if(this.value!='')
		          {
		          	 $("#idemployee").val(this.value);
				  	 
				  }
				
				  
		   });
		   
		   
		}
		else if(ctrl_page[2]=='list')
		{
				
			//for collapse sidebar
			$('body').addClass("sidebar-collapse");
			//for loading list
			load_account_list();
		}
	}	
	
		$('#trans_type').on("change", function(e) {
			 
			       if((this).value!='')
					{  
						var from_date =$('#account_list1').text();
						var to_date  = $('#account_list2').text();
						$('#id_trans_type').val(this.value);
						load_transaction_list(from_date,to_date,(this).value);
					}
		 })
		 
	if(ctrl_page[1]=='transaction')
	{
		if(ctrl_page[2]=='add' || ctrl_page[2]=='edit')
		{
			//for expanding sidebar
			$('body').removeClass("sidebar-collapse");
			get_accounts();
		//	get_customers();
			var dateToday = new Date();	
		    
			$('#date_transaction').datepicker({
               format: 'dd-mm-yyyy',
                  "setValue": new Date(),
                  "autoclose": true
              
            }).datepicker("setDate", dateToday);
            
           $('#wallet_account').select2().on("change", function(e) {
	          if(this.value!='')
	          {
	          	 $("#id_wallet_account").val(this.value);
	          	  get_account(this.value);
			  	 
			  }
			  else
			  {
			  	 $('#cus_name').val('');
	     	    $('#balance').val('');
			  }
		   });
		   
		    $("input[name='wallet[transaction_type]']:radio").change(function () {
		    	 $('#value').val('');
                 $('#value').focus();
            });
            
            $('#value').bind('change keyup', function(e){
            	var type = parseInt($("input[name='wallet[transaction_type]']:checked", '#wallet_transaction').val());
             
                if(type == 1)
                {
					 if(parseFloat(this.value) > parseFloat($('#balance').val()))
				  	 {
					 	$('#value').val($('#balance').val());
					 }
				  
					
				}	
				
			 });
		   
		}
		else if(ctrl_page[2]=='list')
		{
			
			/*for collapse sidebar
			$('body').addClass("sidebar-collapse");
			for loading list
			load_transaction_list();*/
			/*var d = new Date();
		var from_date=(d.getDate()+"-"+(d.getMonth() + 1)+"-"+d.getFullYear());
		var to_date=((d.getDate()-6)+"-"+(d.getMonth() + 1)+"-"+d.getFullYear());*/
		
					   var date = new Date();
					   var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1); 
						var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
						var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());


		load_transaction_list(from_date,to_date,$('#id_trans_type').val());

			$('#account_list1').empty();
			$('#account_list2').empty();
		
			
		$('#account_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
		$('#account_list2').text(moment().endOf('month').format('YYYY-MM-DD'));	
		$('#account-dt-btn').daterangepicker(

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


              load_transaction_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),$('#id_trans_type').val())			 
			  $('#account_list1').text(start.format('YYYY-MM-DD'));
			  $('#account_list2').text(end.format('YYYY-MM-DD')); 

          }

        ); 
			
		}
		
	}


	
	
	if(ctrl_page[1]=='category' && ctrl_page[2]=='list')
	{ 
		
		//for collapse sidebar
		$('body').addClass("sidebar-collapse");
		//for loading list
		 wallet_category();
	}
	
	if(ctrl_page[1]=='category' && ctrl_page[2]=='setting')
	{ 
		
		//for collapse sidebar
		$('body').addClass("sidebar-collapse");
		//for loading list
		  wallet_category_setting();
	}
	if(ctrl_page[1]=='category' && ctrl_page[2]=='add')
	{ 
			 var dateToday = new Date();	
		    
			$('#date_add').datepicker({
               format: 'dd-mm-yyyy',
                  "setValue": new Date(),
                  "autoclose": true
              
            }).datepicker("setDate", dateToday);
		 
		 
	}
	
	
	
	
	   
	



	
});



function get_customers(type)
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/wallet/get/customers/'+type,
		dataType:'json',
		success:function(data){
		  var id_customer =  $('#id_customer').val();
		   $.each(data.customers, function (key, item) {
					  	
			  
			   		$('#customer_name').append(
						$("<option></option>")
						  .attr("value", item.id)
						  .text(item.mobile)
						  
					);
			   				
				
			});
			
			$("#customer_name").select2({
			    placeholder: "Enter mobile number",
			    allowClear: true
			});
				
				console.log(id_customer);
			$("#customer_name").select2("val",(id_customer!='' && id_customer>0?id_customer:''));
			id_customer!=''?'':$("#cusremark").val('');
			
		
				
		}
	});
}

// referral emp account //

function get_employee(type)
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/wallet/get/employee/'+type,
		dataType:'json',
		success:function(data){
		  var idemployee =  $('#idemployee').val();
		   $.each(data.employee, function (key, item) {		  	
			  
			   		$('#employee_name').append(
						$("<option></option>")
						  .attr("value", item.id)
						  .text(item.mobile)
						  
					);
			   				
				
			});
			
			$("#employee_name").select2({
			    placeholder: "Enter mobile number",
			    allowClear: true
			});
			$("#employee_name").select2("val",(idemployee!='' && idemployee>0?idemployee:''));
			idemployee!=''?'':$("#empremark").val('');
		
				
		}
	});
}


// referral emp account //

function load_account_list()
{
	
		my_Date = new Date();
	$("div.overlay").css("display", "block"); 
		var oTable = $('#wallet_acc_list').DataTable();
		$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/wallet/account/ajax_list?nocache='+my_Date.getUTCSeconds(),
				  dataType: 'json',
				  success: function(data) {
						var access=data.access;
						
						$('#total_wall_acc').text(data.wallet.length);
						 if(access.add == '0')
						 {
							$('#add_w_acc').attr('disabled','disabled');
						 }
				       oTable = $('#wallet_acc_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                 "order": [[ 0, "desc" ]],
				                "aaData": data.wallet,
				                "aoColumns": [
							                    { "mDataProp": "id_wallet_account" },
							                    { "mDataProp": "type" },
							                    { "mDataProp": "name" },
								                { "mDataProp": "mobile" },
								                { "mDataProp": "issued_date" },
								                { "mDataProp": "emp_name" },		
								                { "mDataProp": function ( row, type, val, meta ){
					
							                		return "<a href='#'><i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"
							                	} },
							                	  { "mDataProp": function ( row, type, val, meta ) {
							                	 id         = row.id_wallet_account;
							                	 edit_url   =(access.edit=='1'? base_url+'index.php/wallet/account/edit/'+id : "#");
							                	 delete_url = (access.delete=='1'?base_url+'index.php/wallet/account/delete/'+id :"#");
							                	 delete_confirm= '#confirm-delete';
							                	 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
							    '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+
							    '<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li></ul></div>';
							                	return action_content;
							                	}
							               
							            }
								             ]
					            });
					                
					    $("div.overlay").css("display", "none");           
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}

//wallet transaction

function get_accounts()
{
	$("div.overlay").css("display", "block"); 
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/wallet/account/ajax_list',
		dataType:'json',
		success:function(data){
		  var id_wallet_account =  $('#id_wallet_account').val();
		  if(id_wallet_account != ''){
			  $("#wallet_account").prop("disabled", true);
				}
		   $.each(data.wallet, function (key, item) {
					  	
			  
			   		$('#wallet_account').append(
						$("<option></option>")
						  .attr("value", item.id_wallet_account)
						  .text(item.mobile )
						  
					);
			   				
				
			});
			
			$("#wallet_account").select2({
			    placeholder: "Enter mobile number",
			    allowClear: true
			});
				
			$("#wallet_account").select2("val",(id_wallet_account!='' && id_wallet_account>0?id_wallet_account:''));
			var type = data.setting.type;
		
			$('#val_type').html((type==0? '(Rs.)':'Point'));
			
			$("div.overlay").css("display", "none"); 
		},
		error:function(data)
		{
			$("div.overlay").css("display", "none"); 
		}
	});
}

function get_account(id_account)
{
	$("div.overlay").css("display", "block"); 
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/wallet/account/ajax_list/'+id_account,
		dataType:'json',
		success:function(data){
			var wallet = data.wallet;
			 console.log(data.wallet);
	     	 $('#cus_name').val(wallet.name);
	     	 $('#balance').val(wallet.balance);
			
			$("div.overlay").css("display", "none"); 
		},
		error:function(data)
		{
			$("div.overlay").css("display", "none"); 
		}
	});
}


function load_transaction_list(from_date,to_date,types="")
{

	my_Date = new Date();
	$("div.overlay").css("display", "block"); 
		var oTable = $('#wallet_trans_list').DataTable();
		$.ajax({
				  type: 'POST',
				  url:  base_url+'index.php/wallet/transaction/ajax_list?nocache='+my_Date.getUTCSeconds(),
				  data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'type':types}: ''),

				  dataType: 'json',
				  success: function(data) {
						var access=data.access;
						
						 if(access.add == '0')
						 {
							$('#add_wall_trans').attr('disabled','disabled');
						 }
				       oTable = $('#wallet_trans_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
								 "order"	 : [[0,'desc']],
				                "aaData": data.wallet,
				                "aoColumns": [
							                    { "mDataProp": "id_wallet_transaction" },
							                    { "mDataProp": "date_transaction" },
							                    { "mDataProp":function ( row, type, val, meta ) {
							                    	var type = row.transaction_type;
							                    	return "<span class='badge "+(type==0?'bg-green':'bg-red')+"'>"+(type==0?'Issue':'Redeem')+"</span>";
							                      } 							                    
							                    },
							                    { "mDataProp": "name" },
								                { "mDataProp": "mobile" },
								                { "mDataProp": "value" },
								                { "mDataProp": "description" },
								                { "mDataProp": "type" },
								                { "mDataProp": "emp_name" },		
								                { "mDataProp": function ( row, type, val, meta ) {
							                	 id         = row.id_wallet_transaction;
												 edit_url   =(access.edit=='1'? base_url+'index.php/wallet/transaction/edit/'+id : "#");
							                	 delete_url = (access.delete=='1'?base_url+'index.php/wallet/transaction/delete/'+id :"#");
												print_url  = (base_url+'index.php/payment/thermal_invoice/'+id+'/'+'WalletTransaction/'+row.date_transaction);												

							                	 delete_confirm= '#confirm-delete';
							                	 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
							    '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+
							    '<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li><li><a href="'+print_url+'" target="_blank" class="btn-print"><i class="fa fa-print" ></i>Thermal_Print</a></li></ul></div>';
							                	return action_content;
							                	}
							               
							            }
								             ],
											 				 
									"footerCallback": function( row, data, start, end, display ) 
									{
										if(data.length>0){
											
										var totlength=data.length;
										
										 var api = this.api(), data;

												 var intVal = function ( i ) {
													return typeof i === 'string' ?
														i.replace(/[\$,]/g, '')*1 :
														typeof i === 'number' ?i : 
														0;
												};
										
										$( api.column(0).footer() ).html(totlength);		   
										// Amount Total over this page
										
										
										/* amttotal = api
											.column(5,{ page: 'current'})
											.data()
											.reduce( function (a, b) {
												return intVal(a) + intVal(b);
											}, 0 ); */
											var Isum = 0;
											var Rsum = 0;
											
											$.each( data, function( key, value) {
											
											if(value.transaction_type == 0 ){
												var sumValue = (parseFloat(Isum)+parseFloat(value.value)).toFixed(2);
												Isum =sumValue;
												//console.log(value.value);
												
											} 
										 
										});
										 $.each( data, function( key, value) {
											
											if(value.transaction_type == 1 ){
												var sumValue = parseFloat(parseFloat(Rsum)+parseFloat(value.value)).toFixed(2);
												Rsum =sumValue;
										
											} 
										  
										});	
										

												var  amttotal = parseFloat(parseFloat(Isum)-parseFloat(Rsum)).toFixed(2);
												
										$( api.column(5).footer() ).html('Total'+'&nbsp;&nbsp;'+parseFloat(amttotal).toFixed(2));

									  }	
									else{
										var data=0;
										var api = this.api(), data;
										$( api.column(0).footer() ).html(""); 
										$( api.column(5).footer() ).html("");           
									}
								}			 
								
					            });
					                
					    $("div.overlay").css("display", "none");           
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}

function load_setting_list()
{
	$("div.overlay").css("display", "block"); 
		var oTable = $('#wallet_list').DataTable();
		$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/wallet/master/ajax_list',
				  dataType: 'json',
				  success: function(data) {
						var access=data.access;
						
						$('#total_plans').text(data.wallet.length);
						 if(access.add == '0')
						 {
							$('#add_plan').attr('disabled','disabled');
						 }
				       oTable = $('#wallet_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": data.wallet,
				                "columnDefs": 
										[
															
											{
												targets: [0,1,2,3,4,5], 
												className: 'dt-left'
											},
										
											
										],
				                "aoColumns": [
							                    { "mDataProp": "id_wallet" },
							                    { "mDataProp": "effective_date" },
								                { "mDataProp": "name" },
								                { "mDataProp": "type" },
								                { "mDataProp": "currency" },
								                { "mDataProp": "value" },		
								                { "mDataProp": function ( row, type, val, meta ){
					
							                		return "<i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"
							                	} },
							                	  { "mDataProp": function ( row, type, val, meta ) {
							                	 id         = row.id_wallet;
							                	 edit_url   = (access.edit=='1'?base_url+'index.php/wallet/master/edit/'+id:"#");
												
												  delete_url = (access.delete=='1'?base_url+'index.php/wallet/master/delete/'+id:"#");
							                	// delete_confirm= '#confirm-delete';
							                	 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
							    /* '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+
							    '<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li></ul></div>'; */
								(access.edit == '1' ? '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>':"")+'</ul></div>';
								
							                	return action_content;
							                	}
							               
							            }
								             ]
					            });
					                
					    $("div.overlay").css("display", "none");           
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}



// wallet Category //



function wallet_category()
{	
$("div.overlay").css("display", "block"); 
		var oTable = $('#walletcategory_list').DataTable();
		$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/wallet/category/ajax_list',
				  dataType: 'json',
				  success: function(data) {
						var access=data.access;
						
						$('#total_plans').text(data.wallet.length);
						 if(access.add == '0')
						 {
							$('#add_plan').attr('disabled','disabled');
						 }
				       oTable = $('#walletcategory_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": data.wallet,
				                "columnDefs": 
										[
															
											{
												targets: [0,1,2], 
												className: 'dt-left'
											},
										
											
										],	
				                "aoColumns": [
							                    { "mDataProp": "id_wallet_category"},						                  
								                { "mDataProp": "name" },
								                { "mDataProp": "code" },
								                { "mDataProp": function ( row, type, val, meta ){

					                	        active_url =base_url+"index.php/admin_wallet/walletcategory_status/"+(row.active==1?0:1)+"/"+row.id_wallet_category; 
					                		     return "<a href='"+active_url+"'><i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"}
												},
												{ "mDataProp": function ( row, type, val, meta ){

													 id= row.id_wallet_category;

													 edit_url=(access.edit=='1' ? base_url+'index.php/wallet/category/edit/'+id : '#' );
													 
													 delete_url=(access.delete=='1' ? base_url+'index.php/wallet/category/delete/'+id : '#' );
													 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
													 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
													(access.edit == '1' ? '<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>' :'') +
												(access.delete == '1' ? '<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li>':'')+'</ul></div>';
													return action_content;
												}}
								             ]
					            });
					                
					    $("div.overlay").css("display", "none");           
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}


function wallet_category_setting()
{	
$("div.overlay").css("display", "block"); 
		var oTable = $('#walletcatesett_list').DataTable();
		my_Date = new Date();
		$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/wallet/category/setting/ajax_list?nocache=' + my_Date.getUTCSeconds(),
				  dataType: 'json',
				  success: function(data) {
						var access=data.access;
						
						console.log(data);
						
						$('#total_plans').text(data.wallet.length);
						 if(access.add == '0')
						 {
							$('#add_plan').attr('disabled','disabled');
						 }
				       oTable = $('#walletcatesett_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
								'columnDefs': [{
										 'targets': 0,
										 'searchable':false,
										 'orderable':false,
										 "bSort": true,
										 'className': 'dt-body-center',
									  }],
				                "aaData": data.wallet,
				                "aoColumns": [
							                    /* { "mDataProp": "id_wcat_settings"}, */
												{ "mDataProp":function ( row, type, val, meta ){												   
												  return '<input type="checkbox" id="walletcatset_id" class="walletcatset_id"  value="'+row.id_wcat_settings+'">';
												 }},
								                { "mDataProp": "name" },
								                { "mDataProp": function ( row, type, val, meta ){
												   
												return '<input  type="text"   class="walletcs_point"  style="width: 15%;" disabled="true" value="'+row.point+'">'+'&nbsp; Point Per &nbsp; '+'<input  type="text"   class="walletcs_value" style="width: 30%;" disabled="true" value="'+row.value+'">  '+row.currency_symbol;  
												   }
												},
								                 { "mDataProp": function ( row, type, val, meta ){
												   
												return '<input  type="text"  id="walletcs_redeem" class="walletcs_redeem" style="width: 30%;" disabled="true" value="'+(row.redeem_percent!='null'?row.redeem_percent:'')+'">';  
												   }
												},
								                { "mDataProp": "date_add" },								               
								                { "mDataProp": function ( row, type, val, meta ){

					                	        active_url =base_url+"index.php/admin_wallet/wallet_categorysett_status/"+(row.active==1?0:1)+"/"+row.id_wcat_settings; 
					                		     return "<a href='"+active_url+"'><i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"}
												},
												{ "mDataProp": function ( row, type, val, meta ){														
												return '<input  type="text"  id="id_remark" class="walletcs_remark"  disabled="true" value="'+(row.remark!=''?row.remark:'')+'">';	
													  
												   }
												}
								             ]
					            });
					                
					    $("div.overlay").css("display", "none");           
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}

$(document).on('click', '#select_walletdata', function(e){	
	
	 if($(this).prop("checked") == true){
		 
                $("tbody tr td input[type='checkbox']").prop('checked',true);
				$(".walletcs_point").attr('disabled', false);
				$(".walletcs_remark").attr('disabled', false);
				$(".walletcs_redeem").attr('disabled', false);
				$(".walletcs_value").attr('disabled', false);
				
            }
            else if($(this).prop("checked") == false)
			{
               
				$(".walletcs_remark").val('');
				$(".walletcs_remark").attr('disabled', true);
				$(".walletcs_redeem").val('');
				$(".walletcs_redeem").attr('disabled', true);
				$(".walletcs_point").val('');
				$(".walletcs_point").attr('disabled', true);
				$(".walletcs_value").val('');
				$(".walletcs_value").attr('disabled', true);
				$("tbody tr td input[type='checkbox']").prop('checked', false);
            }
	
 
});


$(document).on('click', '.wallet_category', function(e){
 var walletset = [];
 var data = [];
 var table='';
 var i=0;
	  
		
	   $("#walletcatesett_list tbody tr").each(function(index, value) 
		{
		if(!$(value).find(".walletcatset_id").is(":checked"))
			 { 
				$(value).find(".walletcatset_id").empty();	
			 }
			else if(($(value).find(".walletcatset_id").is(":checked"))){
					   var creditpoint=$(value).find(".walletcs_point").val();
					   var creditvalue=$(value).find(".walletcs_value").val();
					   var walletcsredeem=$(value).find(".walletcs_redeem").val();
					   var walletcsremark=$(value).find(".walletcs_remark").val();					   
					  var data ={'index':i,'value':creditvalue,'point':creditpoint,
					          'redeem_percent':walletcsredeem,'remark':walletcsremark}; 
					 walletset.push(data);
					table+='<input type="hidden" name=wallet_categoryset['+i+'][id_wcat_settings] value='+$(value).find(".walletcatset_id").val()+'>'+
					'<input type="hidden" name=wallet_categoryset['+i+'][value] value='+$(value).find(".walletcs_value").val()+'>'+
				   '<input type="hidden" name=wallet_categoryset['+i+'][point] value='+$(value).find(".walletcs_point").val()+'>'+
				   '<input type="hidden" name=wallet_categoryset['+i+'][redeem_percent] value='+$(value).find(".walletcs_redeem").val()+'>'+
				   '<input type="hidden" name=wallet_categoryset['+i+'][remark] value='+$(value).find(".walletcs_remark").val()+'>';
				   i++;
					   
				   }
				else{
				  
				  msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Select to proceed</div>';
						
						$("div.overlay").css("display", "none"); 
								
								//stop the form from submitting
								 $('#error-msg').html(msg);
				return false;
				  
			  }
		
	  });
	  
	  if(walletset.length>0 && table!=''){		  
		 $('.walletcategory').append(table);
		 $('.wallet_category').attr('disabled',true);
		 $('#walletsetting').submit();
	  }else{
		  
		  msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>  Select to proceed</div>';
		  $("div.overlay").css("display", "none"); 
		  $('#error-msg').html(msg);
		  return false;
		  
	  } 
});

$(document).on('click', '.walletcatset_id', function(e){
	
 $("#walletcatesett_list tbody tr").each(function(index, value) 
	{
			 if(!$(value).find(".walletcatset_id").is(":checked"))
			 { 
				$(value).find(".walletcs_remark").attr('disabled', true);
				$(value).find(".walletcs_redeem").attr('disabled', true);
				$(value).find(".walletcs_point").attr('disabled', true);
				$(value).find(".walletcs_value").attr('disabled', true);
			}
			else if($(value).find(".walletcatset_id").is(":checked"))
			 { 
				$(value).find(".walletcs_remark").attr('disabled', false);
				$(value).find(".walletcs_redeem").attr('disabled', false);
				$(value).find(".walletcs_point").attr('disabled', false);
				$(value).find(".walletcs_value").attr('disabled', false);	
			}
		

      });
});

$('#wallet_name,#wallet_code,#wallet_name').bind("cut copy paste",function(e) {
		e.preventDefault();
});
