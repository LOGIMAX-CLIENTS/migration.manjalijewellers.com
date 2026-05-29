$(document).ready(function() {

	var path =  url_params();

	

	

    var ctrl_page = path.route.split('/');



    if(ctrl_page[1]=='payment_entry')

    {

		initialize_payment_entry("ppayment_status")

	}



	 if(ctrl_page[0]=='postdated' && (ctrl_page[2]=='add' || ctrl_page[2]=='edit'))

     {

     	load_customer_select();

		load_postpay_select();

		$('#pay_date').datepicker("setDate", new Date());

	 }

	

	

	 //for list view

	  if(ctrl_page[0]=='postdated' && ctrl_page[2]=='list')

	  {
			
	  	  initialize_payment_entry("sel_payment_status");

	  	  initialize_postpay_list();

				$('#ppayment_list1').empty();
				$('#ppayment_list2').empty();
				$('#ppayment_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
				$('#ppayment_list2').text(moment().endOf('month').format('YYYY-MM-DD'));	

	  	     $('#ppayment-dt-btn').daterangepicker(

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
		  
             get_payment_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))	
			 
			 $('#ppayment_list1').text(start.format('YYYY-MM-DD'));
			 $('#ppayment_list2').text(end.format('YYYY-MM-DD'));
          }

        ); 

	  	  

	 	

	  	//for select all

	  	$('#select_all').click(function(e){

	  		

	  		 if (e.stopPropagation !== undefined) {

		        e.stopPropagation();

		         $('input[name="id_payment"]').prop('checked', $(this).prop('checked'));

		       

		    } else {

		        e.cancelBubble = true;

		    }

	  	});

	  	

	  	 $('#update_status').click(function(){

	  	 	

	  	 

	  	 	  getSeletedRows();

	  	 });

	  	

		  	//for get all selected values

		  	function getSeletedRows()

			{		

				var table_data = [];

			    var values = {};

			    $("#post_payment_list > tbody > tr").each(function(i){

			        values = new Object;

			

			       if( $(this).find('input[type="checkbox"]').is(':checked') && $('#payment_status_select').val()!=null){ 

			       	   

			       	   //update status for selected row

				   	   $('input[name="payment_status"]').val($('#payment_status_select').val());

				      

				       //fetch values

				        $('input', this).each(function(){

				        	  if($(this).attr('type') == 'checkbox')

							  {

							  	 values[$(this).attr('name')] =($(this).is(':checked')?$(this).val():0);

							  }

							  else

							  {

							  	 values[$(this).attr('name')]=$(this).val();

							  }	

				           

				        });

				        

			        	table_data.push(values);

			        	

			        }

			    });



			    //table_data.shift(); 

			   if ( table_data.length != 0 ) {

			   				    

			     $("div.overlay").css("display", "block"); 

					var postData ={'postpay_data':JSON.stringify(table_data)};

					var my_Date = new Date();

						$.ajax({

							url : base_url+"index.php/postdated/payment/update",

							type : "POST",

							data : postData,

							//dataType: 'json',

							success: function(result)

							{

							   console.log(result);

							   	$("div.overlay").css("display", "none"); 

							   	 	 $('#pdp-alert').delay(500).fadeIn('normal', function() {

								   	 	  $(this).find("p").html(result);

								   	 	  $(this).addClass("alert-success ");

									      $(this).delay(1000).fadeOut();

									 });

									window.location.reload();

												   

							},

							error:function(error)

							{

								console.log(error);

									$("div.overlay").css("display", "none"); 

										$('#pdp-alert').delay(500).fadeIn('normal', function() {

									   	 	  $(this).find("p").html("Unable to proceed request");

									   	 	  $(this).addClass("alert-danger ");

										      $(this).delay(2500).fadeOut();

										 });

										 		

														

							}

						});

					

				}

	  }
	  
//branch_name 	  
	 
$('#branch_select').select2().on("change", function(e) {
	
		if(this.value!='')
		{  
			
			var from_date = $('#ppayment_list1').text();
			var to_date  = $('#ppayment_list2').text();
			var id=$(this).val();	
				
			console.log(from_date);
			console.log(to_date);
			console.log(id);
			get_payment_list(from_date,to_date,id);
		}
   });

//branch_name 
	  
	  

	  }

 });

 

 $('input[name="paytype[payment_type]"]').change(function(){

 	if($(this).val()=='ECS')

 	{

 		    $('#chq_start_no').val('');

			$('#chq_start_no').prop('disabled', true); 

			

	}

	else

	{

		 $('#chq_start_no').val('');

		$('#chq_start_no').prop('disabled', false);

	}

 

 	

 });

  function load_customer_select()

{

	my_Date = new Date();

	//show spinner

		$("div.overlay").css("display", "block"); 

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/customer/get_customers?nocache=' + my_Date.getUTCSeconds(),

	  dataType: 'json',

	  cache:false,

		success: function(data) {

		      if($('#customer').length>0)

		     {

			 	$.each(data, function (key, cus) {

						  	

				   

					$('#customer').append(

						$("<option></option>")

						  .attr("value", cus.id)

						  .text(cus.mobile+" "+cus.name)						  

					);

					

				});

				

				$("#customer").select2({

				  placeholder: "Enter Mobile Number",

				    allowClear: true

				});		

				

				$("#customer").select2("val", ($('#id_customer').val()!=null?$('#id_customer').val():''));

			 }

			 //disable spinner

			$("div.overlay").css("display", "none"); 

		},

	  	error:function(error)

		{

			console.log(error);

			//disable spinner

				$("div.overlay").css("display", "none"); 

		}	

	 }); 	

}



 $('#customer').select2().on("change", function(e) {

          //console.log("change val=" + this.value);

         

      if(this.value!='')

      {

      	 $("#id_customer").val(this.value);

      	 my_Date = new Date();

      	 //load customer schemes

		//show spinner

		$("div.overlay").css("display", "block"); 

		$.ajax({

		  type: 'GET',

		  url:  base_url+'index.php/payment/get/ajax/customer/account_amount/'+this.value+'?nocache=' + my_Date.getUTCSeconds(),

		  dataType: 'json',

		  cache:false,

			success: function(data) {

			        if($('#scheme_account').length>0)

				     {

				     	$('#scheme_account').empty();

					 	$.each(data, function (key, acc) {

								  	

						   

							$('#scheme_account').append(

								$("<option></option>")

								  .attr("value", acc.id_scheme_account)

								  .text(acc.code+' - '+acc.scheme_acc_number)

								  

							);

							

						});

						

						$("#scheme_account").select2({

						  placeholder: "Select scheme account",

						    allowClear: true

						});		

						

						$("#scheme_account").select2("val", ($('#id_scheme_account').val()!=null?$('#id_scheme_account').val():''));

					 

					 

					 }

				 //disable spinner

					$("div.overlay").css("display", "none"); 

				

			},

		  	error:function(error)

			{

				console.log(error);

				//disable spinner

				$("div.overlay").css("display", "none"); 

			}	

		 }); 

	  }

	  else

	  {

	  	$("#scheme_account").select2("val",'');

	  	$('#scheme_account').empty();

	  }

	

		  

   });

 function load_postpay_select()

{

	my_Date = new Date();

	//show spinner

	$('.overlay').css('display','block');

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/payment/get/ajax_data?nocache=' + my_Date.getUTCSeconds(),

	  dataType: 'json',

	   cache:false,

	  success: function(data) {

	  //	console.log(country);

						

			$.each(data.bank, function (key, bank) {

					  	

			   

				$('#payee_bank').append(

					$("<option></option>")

					  .attr("value", bank.id_bank)

					  .text(bank.bank_name)

					  

				);

				

			

			});

			

			$("#payee_bank").select2({

			  placeholder: "Select payee bank",

			    allowClear: true

			});		

			

			$("#payee_bank").select2("val", ($('#id_payee_bank').val()!=null?$('#id_payee_bank').val():''));

			

			$.each(data.drawee, function (key, bank) {

					  	

			   

				$('#drawee_acc_no').append(

					$("<option></option>")

					  .attr("value", bank.id_drawee)

					  .text(bank.account_no)

					  

				);

				

			

			});

			

			$("#drawee_acc_no").select2({

			  placeholder: "Select account number",

			    allowClear: true

			});		

			

			$("#drawee_acc_no").select2("val", ($('#id_drawee_bank').val()!=null?$('#id_drawee_bank').val():''));

			

	



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



$('#generate_payments').click(function(){

	var html="";

	var scheme_account 		= $('#scheme_account').val();

	var pay_mode 			=  $('input[name="paytype[payment_type]"]:checked').val();

	var allowed_leaves 		=  $('#allowed_leaves').val();

	var total_leaves 		=  $('#chq_leaf').val();

	var installments 		=  $('#no_of_installment').val();

	

	

	var chq_start_no 		=  $('#chq_start_no').val();

	var exe_day 			=  $('#exe_date').val();

	

	var payee_bank 			=  $('#payee_bank').val();

	var payee_bank_branch 	=  $('#payee_bank_branch').val();

	var payee_ifsc 			=  $('#payee_ifsc').val();

	var payee_acc_no 		=  $('#payee_acc_no').val();

	var drawee_acc_no 		=  $('#drawee_acc_no').val();

	var installment_amount 	=  $('#installment_amount').val();

	var startDt             = $('#start_date').val().split("/");

	var start_date			= new Date( startDt[2],startDt[1] - 1,startDt[0]);

		var xdd 			= start_date.getDate();

		var xmm 			= start_date.getMonth()+1;

		var xyy 			= start_date.getFullYear();

	
	   var curDate = new Date();

        var curDay= curDate.getDate();

		var curMonth = curDate.getMonth()+1;

		var curYr= curDate.getFullYear();

        if(exe_day<curDay  ){

			exe_day = curDay;

		}


		if(xmm<curMonth ){

			xmm = xmm + 1;

		}

	
		if(xyy<curYr){ 

			xyy = curYr;

		}



		

		if((exe_day!=null || exe_day!='') && (start_date!=null || start_date!='') && (payee_bank!=null || payee_bank!='')&& (payee_acc_no!=null || payee_acc_no!=''))

		{

			for(i=1;i<=total_leaves;i++){

			

				

				var date_payment = exe_day+"/"+((xmm+'').length==1?'0'+xmm:xmm)+"/"+curYr;

				

				html+="<tr><td>"+i+"</td><td><input type='text' class='form-control' name='pay["+i+"][date_payment]' value='"+(date_payment!=''?date_payment:'')+"'/></td>"+

				"<td><input type='text' class='form-control' name='pay["+i+"][cheque_no]' value='"+(chq_start_no!=''?chq_start_no:'')+"'/></td>"+

				"<input type='hidden' name='pay["+i+"][id_scheme_account]' value='"+(scheme_account!=''?scheme_account:'')+"'/>"+

				"<input type='hidden' name='pay["+i+"][pay_mode]' value='"+(pay_mode!=''?pay_mode:'')+"'/>"+

				"<input type='hidden' name='pay["+i+"][payee_bank]' value='"+(payee_bank!=''?payee_bank:'')+"'/>"+

				"<input type='hidden' name='pay["+i+"][payee_acc_no]' value='"+(payee_acc_no!=''?payee_acc_no:'')+"'/>"+

				"<input type='hidden' name='pay["+i+"][payee_branch]' value='"+(payee_bank_branch!=''?payee_bank_branch:'')+"'/>"+

				"<input type='hidden' name='pay["+i+"][payee_ifsc]' value='"+(payee_ifsc!=''?payee_ifsc:'')+"'/>"+

				

				"<input type='hidden' name='pay["+i+"][id_drawee]' value='"+(drawee_acc_no!=''?drawee_acc_no:'')+"'/>"+

				

				"<td><input type='text' class='form-control' name='pay["+i+"][amount]' value='"+(installment_amount!=''?installment_amount:'')+"'/></td></tr>";

				

		  	  if(chq_start_no!='')

		  	  {

			  	chq_start_no=parseFloat(chq_start_no)+1;		  	

			  }

		  	  	xmm=parseFloat(xmm)+1;

		  	   	if(xmm > 12 )

		  	   	{

					xmm =1;

					curYr=parseFloat(curYr)+1 

				}

		  	  	

			}

	     

	        $('#post_payment_detail tbody').html(html);

	        

	       

				$('#btn-save').prop('disabled',false);

			

		}

		

	

	  	  //console.log(html);

});



function initialize_payment_entry(elementID)

{

	$('body').removeClass("sidebar-collapse");

    my_Date = new Date();

	$('.overlay').css('display','block');

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/postdated/payment/ajax_payment_status?nocache=' + my_Date.getUTCSeconds(),

	  dataType: 'json',

	   cache:false,

	  success: function(data) {

	  	      $.each(data.payment_status, function (key, item) {
	
				$('#'+elementID).append(

					$("<option></option>")

					  .attr("value", item.id_status_msg)

					  .text(item.payment_status)

				);

			});

			$("#"+elementID).select2({

			    placeholder: "Select status",

			    allowClear: true

			});		

				$("#"+elementID).select2("val", ($('#payment_status').val()!=null?$('#payment_status').val():''));

			//disable spinner

				$('.overlay').css('display','none');

	  	}

	  });	

}


function initialize_postpay_list()

{

		my_Date = new Date();

	//show spinner

		$("div.overlay").css("display", "block"); 

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/postdated/payment/ajax_payment_status?nocache=' + my_Date.getUTCSeconds(),

	  dataType: 'json',

	   cache:false,

	  success: function(data) {



	      //loading list

	      get_payment_list();

	        

	        //loading data in select

			/*$.each(data.payment_status, function (key, item) {

					  	

			   

				$('#payment_status_select').append(

					$("<option></option>")

					  .attr("value", item.id_status_msg)

					  .text(item.payment_status)

					  

				);

				

			});

			

			$("#payment_status_select").select2({

			    placeholder: "Select status",

			    allowClear: true

			});		

			

			$("#payment_status_select").select2("val", '');*/

	

			//disable spinner

					$("div.overlay").css("display", "none"); 

		},

		error:function(error)

		{

			console.log(error);

			//disable spinner

				$('.overlay').css('display','none');

		}	

	  });

}



  $('#scheme_account').select2()

        .on("change", function(e) {

          //console.log("change val=" + this.value);

         

          if(this.value!='')

          {

          	 $("#id_scheme_account").val(this.value);

		  	 get_account_detail(this.value);

		  }

		  else

		  {

		  	  	$('#no_of_installment').val('');

	  	        $('#installment_amount').val('');	

		  }

		  

   });  

   

   //on selecting drawee account

   $('#drawee_acc_no').select2()

        .on("change", function(e) {

          //console.log("change val=" + this.value);

         

          if(this.value!='')

          {

          	 $("#id_drawee_bank").val(this.value);

		  	 get_drawee_detail(this.value);

		  }

		

		  

   });

  

  //to get drawee detail

  function get_drawee_detail(id)

  {

  	my_Date = new Date();

  		$.ajax({

				  type: 'GET',

				  url:  base_url+'index.php/settings/drawee/ajax_list/'+id+'?nocache=' + my_Date.getUTCSeconds(),

				  dataType: 'json',

				   cache:false,

				  success: function(data) {

				  	$('#drawee_bank').val(data.data.bank_name);

				  	$('#drawee_bank_branch').val(data.data.branch);

				  	$('#drawee_ifsc').val(data.data.ifsc_code);

				  	

				  }

				});  	

  } 

   

 //to get account detail         

 function get_account_detail(id)

 {

 	my_Date = new Date();

	//show spinner

	$('.overlay').css('display','block');

 		$.ajax({

				  type: 'GET',

				  url:  base_url+'index.php/payment/get/ajax/account/'+id+'?nocache=' + my_Date.getUTCSeconds(),

				  dataType: 'json',

				   cache:false,

				  success: function(data) {

				  	var account = data.account;

	  	           	var payable ='';

	  	           	

	  	           	payable = (account.scheme_type=='0'?parseFloat(account.payable).toFixed(2) :parseFloat(parseFloat(data.payable)-parseFloat(account.current_paid_weight)).toFixed(3) );

	  	           	$('#no_of_installment').val(account.total_installments);

	  	           	$('#paid_installments').val(account.paid_installments);

	  	            $('#allowed_leaves').val((parseInt(account.total_installments) - parseInt(account.paid_installments))-(account.total_pdc!=null && account.total_pdc!=''? parseInt(account.total_pdc):0));

	  	            $('#pending_chq').val((account.total_pdc!=null && account.total_pdc!=''?account.total_pdc!=parseInt(account.total_pdc):0));

	  	           	$('#installment_amount').val(payable);

	  	            

	  	            if($('#allowed_leaves').val()>0)

	  	            {

						$('#chq_leaf').val($('#allowed_leaves').val());

						$('#chq_leaf').prop('readonly',false);

					}

					else

					{

						$('#chq_leaf').prop('readonly',true);

					}

	  	   

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

 $('#chq_leaf').keyup(function(e){

 

 	if(parseInt(this.value) > parseInt($('#allowed_leaves').val()) )

 	{

 		this.value ='';

		this.value = $('#allowed_leaves').val();

		

	}

	

 });



function get_payment_list(from_date="",to_date="",id_branch="")

{



	my_Date = new Date();

		$("div.overlay").css("display", "block"); 

	$.ajax({

			  url:base_url+"index.php/postdated/payment/ajax_list?nocache=" + my_Date.getUTCSeconds(),

			 data: (from_date !='' && to_date !=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch}: ''),

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

	$('body').addClass("sidebar-collapse");

	 var payment = data.data;

	 var access = data.access;	

	 var oTable = $('#post_payment_list').DataTable();

	   $('#total_payments').text(payment.length);

	  if(access.add == '0')

			 {

				$('#add_post_payment').attr('disabled','disabled');

			 }	

	

	     oTable.clear().draw();

			  	 if (payment!= null && payment.length > 0)

			  	  {  	

			  	      

					  	oTable = $('#post_payment_list').dataTable({

				                "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,

				                "dom": 'T<"clear">lfrtip',

				                "tableTools": { "aButtons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

				                "aaData": payment,

				                "aoColumns": [{ "mDataProp":function (row,type,val,meta){

						id=row.id_post_payment;

						//return "<label class='checkbox-inline'><input type='checkbox' class='flat-red' name='id_payment' value='"+id+"' /> "+id+" </label>";

						return id;

									} },

					{ "mDataProp":"date_payment"},

					{ "mDataProp":"cus_name"},

					{ "mDataProp":"account_name"},

						{ "mDataProp":"code"},	

					  { "mDataProp": function ( row, type, val, meta ){
					                	if(row.has_lucky_draw==1){
					                	return row.scheme_group_code+' '+row.scheme_acc_number;
					                	}
					                	else{
					                		return row.code+'  '+row.scheme_acc_number;
					                	}
					                }},


					{ "mDataProp":"pay_mode"},

		      	 	  { "mDataProp":function (row,type,val,meta){

				    	 return "<input type='hidden' name='cheque_no' value='"+row.cheque_no+"' />"+row.cheque_no

			         }

				    },

				 	{ "mDataProp":"payee_short_code"},

					{ "mDataProp":"drawee_account_name"},

					{ "mDataProp":"drawee_acc_no"},

					{ "mDataProp":"drawee_short_code"},

					{ "mDataProp":"amount"},

				 /*   { "mDataProp":  function ( row, type, val, meta ) {				                	 

	                	 return "<input type='text' class='form-control input-sm' name='payment_ref_number' readonly='true' />";

	                   }

				    },*/

					{ "mDataProp":function(row,type,val,meta){

						

						action_content ="<input type='hidden' name='date_payment' value='"+row.date_payment+"' />"+

								"<input type='hidden' name='id_scheme_account' value='"+row.id_scheme_account+"' />"+

								"<input type='hidden' name='pay_mode' value='"+row.pay_mode+"' />"+

								"<input type='hidden' name='bank_acc_no' value='"+row.payee_acc_no+"' />"+

								"<input type='hidden' name='bank_name' value='"+row.payee_bank+"' />"+

								"<input type='hidden' class='pdc_amount' name='payment_amount' value='"+row.amount+"' />"+

								"<input type='hidden' name='payment_status'/>"+

								"<input type='hidden'  name='date_presented' />"+

								"<input type='hidden'  name='charges' />"+

								"<span class='label bg-"+row.status_color+"-active'>"+row.payment_status+"</span>";

							//"<select class='form-control pay_status' name='payment_status'>";

						return action_content;

					 },

				  }	 ,

					 { "mDataProp": function ( row, type, val, meta ) {

									 id= row.id_post_payment;

										 edit_url=(access.edit=='1' ? base_url+'index.php/postdated/payment_entry/edit/'+id : '#' );

					                	 status_url = base_url+'index.php/postdated/payment_entry/status/'+id ;

					                	

									 action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+

					'<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+

		

					'<li><a href="'+status_url+'" class="btn-edit"><i class="fa fa-search-plus" ></i> Status</a></li></ul></div>';

							  

							return action_content;

							}

					}],

					              "fnInitComplete": function (oSettings, json) {   

  		

  			               if($("#ftotal").length>0)

  			               {

						   	 var sum=0;

						   	  $("#rep_post_payment_list > tbody > tr ").each(function() {

									   

									    var row = $(this);

									     value=row.find('td:eq(10)').html();

									    // add only if the value is number

									    if(!isNaN(value) && value.length != 0 ) {

									        sum += parseFloat(value);

									        

									    }

									    $('#ftotal').html(parseFloat(sum).toFixed(2));

									});						   

						  }

  			}

				            });			  	 	

					  	 }	

}



  	 $('#update_status').click(function(){  	 	

  	     get_table_values();

  	 });



//for get all selected values

function get_table_values()

{		

	var table_data = [];

    var values = {};

    $("#post_payment_list > tbody > tr").each(function(i){

        values = new Object;



       if( $(this).find('input[type="checkbox"]').is(':checked') && $('#sel_payment_status').val()!=null){ 

       	   

       	   //update status for selected row

	   	   $('input[name="payment_status"]').val($('#sel_payment_status').val());

	   	   $('.pay_status').select2("val",$('#sel_payment_status').val());

	   	   $('input[name="charges"]').val($('#sub_charge').val());

	   	 

	   	    //fetch values

	        $('input', this).each(function(){

	        	if($(this).val()!='')

	        	{ 

	        	  if($(this).attr('type') == 'checkbox')

				  {

				  	 values[$(this).attr('name')] =($(this).is(':checked')?$(this).val():0);

				  }

				  else

				  {

				  	

				  		 values[$(this).attr('name')]=$(this).val();

				  }	

		         }

	        });

	        

        	table_data.push(values);

        }

        console.log(table_data);

    });

    

     $("#sel_payment_status").select2("val", '');

     //removes the first elemet

     //table_data.shift(); 

     update_postdata(table_data) 

}



function update_postdata(data)

{

	if (data.length != 0 ) {

     $("div.overlay").css("display", "block"); 

		var postData ={'postpay_data':JSON.stringify(data)};

		var my_Date = new Date();

			$.ajax({

				url : base_url+"index.php/postdated/payment/update",

				type : "POST",

				data : postData,

				dataType: 'json',

				success: function(result)

				{

				   console.log(result);

				   	$("div.overlay").css("display", "none"); 

				   	 	 $('#pdp-alert').delay(500).fadeIn('normal', function() {

					   	 	  $(this).find("p").html(result);

					   	 	  $(this).addClass("alert-success ");

						      $(this).delay(1000).fadeOut();

						 });

						  $('#post_payment_list').DataTable().ajax.reload();

						//window.location.reload();

									   

				},

				error:function(error)

				{

					console.log(error);

						$("div.overlay").css("display", "none"); 

							$('#pdp-alert').delay(500).fadeIn('normal', function() {

						   	 	  $(this).find("p").html("Unable to proceed request");

						   	 	  $(this).addClass("alert-danger ");

							      $(this).delay(2500).fadeOut();

							 });

							 		

											

				}

			});

		

	}	

}