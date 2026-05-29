var path =  url_params();

var ctrl_page = path.route.split('/');

$(document).ready(function() {

    $("#payments .table td a").popover();

	if(ctrl_page[1]=='payment')

	{

	 var settle =$("#id_settled").val((this).value);

			get_onlinepayment_list('','','',settle);

	}

//	$("input[name='pay_id[]']").change(function(ev)

   //payment_list showing selected payment amount in the page //

	$(document).on('click', "input[name='pay_id[]']", function(e) {

	    var tot_amt = 0;

	    $("#tot_sel_amt").html("");

		$("#online_payments tbody tr").each(function(index, value) 

		{

    		 if($(value).find("input[name='pay_id[]']").is(":checked"))

    		 {

    		     console.log($(value).find(".payment_amt").val());

    		     tot_amt = tot_amt+parseFloat($(value).find(".payment_amt").val()); 

    		 }

		})

		$("#tot_sel_amt").html(tot_amt);

	})

    

	//date picker

	$('#onlinePayment_list1').empty();

	$('#onlinePayment_list2').empty();

	$('#onlinePayment_list1').text(moment().startOf('month').format('YYYY-MM-DD'));

	$('#onlinePayment_list2').text(moment().endOf('month').format('YYYY-MM-DD'));	

	$('#onlinePayment-dt-btn').daterangepicker(

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

			 var id_branch=$('#id_branch').val();

			 var settle =$("#id_settled").val((this).value);

             get_onlinepayment_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_branch,settle)

			  $('#onlinePayment_list1').text(start.format('YYYY-MM-DD'));

			  $('#onlinePayment_list2').text(end.format('YYYY-MM-DD')); 

          }

        );   

	//for popover in 

	 $('[data-toggle="popover"]').popover({ html : true});

	//payment_list select all showing - total payment amount in the page //HH

	$('#select_all').click(function(event) {

		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

		var tot_amt = 0;

		$("#sel_all_amt").html(""); 

		$("#online_payments tbody tr").each(function(index, value) 

		{

		    

		        if($(value).find("input[name='pay_id[]']:checked").is(":checked")){	

		            console.log($(value).find(".payment_amt").val());

    		        tot_amt = tot_amt+parseFloat($(value).find(".payment_amt").val()); 

		        }

		 

		})

		$("#sel_all_amt").html(tot_amt);

        event.stopPropagation();

    });

    //to show transaction detail

	$(document).on('click', "a.btn-det", function(e) {

       e.preventDefault();

		$('.trans-det').html(transaction_detail($(this).data('id')));

		  $('#pay_detail').modal('show', {backdrop: 'static'});

	});

	$("input[name='pay_status']:radio").change(function(){

		if($("input[name='pay_id[]']:checked").val())

		{

		 var selected = [];

				$("input[name='pay_id[]']:checked").each(function() {

				  selected.push($(this).val());

				});

			pay_status=$("input[name='pay_status']:checked").val();

			pay_id=selected;

			update_paystatus(pay_status,pay_id);

		}

   });

 function update_paystatus(pay_status="",pay_id="")

{

	my_Date = new Date();

	 $("div.overlay").css("display", "block"); 

	$.ajax({

			  url:base_url+ "index.php/online/payment/update_status?nocache=" + my_Date.getUTCSeconds(),

			 data:  {'pay_status':pay_status,'pay_id':pay_id},

			 type:"POST",

			 async:false,

			 	 success:function(data){

						 $("div.overlay").css("display", "none"); 

		location.reload(true);

					  },

					  error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }	 

		  });

}

// settled pay show in payment apprval page with filter//HH

$('#settle_Select').select2().on("change", function(e) 

{           if(this.value!='')

			{  

			 var settle =$("#id_settled").val((this).value);

				get_onlinepayment_list('','','',settle);

			}

});

// settled pay show in payment apprval page with filter//

 function get_onlinepayment_list(from_date="",to_date="",id_branch="",settle="")

{

	my_Date = new Date();

	var type=$('#date_Select').find(":selected").val();

     var settle=$('#settle_Select').find(":selected").val();

	 $("div.overlay").css("display", "block"); 

	$.ajax({

			  url:base_url+ "index.php/ajax/online/payment?nocache=" + my_Date.getUTCSeconds(),

			 data:  {'from_date':from_date,'to_date':to_date,'id_branch':id_branch,'date_type':type,'settle':settle},

			 dataType:"JSON",

			 type:"POST",

			 success:function(data){

			 	$('#total_payments').text(data.data.length);

			   			set_onlinepayment_list(data.data);

			   			 $("div.overlay").css("display", "none"); 

					  },

					  error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }	 

		  });

}

function set_onlinepayment_list(data)

{

	 var payment = data;

	   $('body').addClass("sidebar-collapse");

	 var oTable = $('#online_payments').DataTable();

	     oTable.clear().draw();

			  	 if (payment!= null && payment.length > 0)

			  	  {  	

					  	oTable = $('#online_payments').dataTable({

				                "bDestroy": true,

				                "bInfo": true,

				                "bFilter": true,

				                "bSort": true,

				                 "dom": 'lBfrtip',

           			             "buttons" : ['excel','print'],

						        "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },

				                "aaData": payment,

				                "aoColumns": [    { "mDataProp": function ( row, type, val, meta ) {

				                	chekbox=' <input type="checkbox" class="pay_id" name="pay_id[]" value="'+row.id_payment+'"  /> ';

				                	element = (row.free_payment == 1 ? (row.payment_mode == 'FP'? chekbox:(row.client_id != null ? chekbox: (row.is_new == 'Y'?"":chekbox))): chekbox);

				                	return element+" "+row.id_payment;

				                }},

					                { "mDataProp": "ref_trans_id" },

					                { "mDataProp": "gateway_requestaction" },

					                { "mDataProp": "name" },

					                { "mDataProp": "account_name" },

					                { "mDataProp": function ( row, type, val, meta ){

					                	return row.code;

					                	}

					                },

					                  { "mDataProp": function ( row, type, val, meta ){

					                	if(row.has_lucky_draw==1){

					                	return row.scheme_group_code+' '+row.scheme_acc_number;

					                	}

					                	else{

					                		return row.code+' '+row.scheme_acc_number;

					                	}

					                }},

					                { "mDataProp": "paid_installments" },

					                { "mDataProp": "mobile" },

					                { "mDataProp": "date_payment" },

					                { "mDataProp": "payment_mode" },

					                { "mDataProp": "metal_rate" },

					                { "mDataProp": "metal_weight" },

					                { "mDataProp": function ( row, type, val, meta ) {

					                	return "<input type='hidden' class='payment_amt' value="+row.payment_amount+" />"+ (row.no_of_dues>1?row.act_amount:row.payment_amount);

					                	}},

					                { "mDataProp": "net_amt" },

					                { "mDataProp": "service_fee" },

					                { "mDataProp": "igst" },

					                { "mDataProp": "payment_status" },

					                { "mDataProp": function ( row, type, val, meta ) {

					                	 action_content='<a href="#" class="btn btn-warning btn-det" data-href="#" data-toggle="modal" data-id="'+row.id_payment+'" data-target="#pay_detail"><i class="fa fa-search"></i> Detail</a>';

					                	return action_content;

					                	}

					            }],

								/*"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

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

								

							 "footerCallback": function( row, data, start, end, display ) 

				{

					

					if(data.length>0){

					 var api = this.api(), data;

					for( var i=0; i<=data.length-1;i++){

						var intVal = function ( i ) {

							   return typeof i === 'string' ?

								   i.replace(/[\$,]/g, '')*1 :

								   typeof i === 'number' ?

									   i : 0;  };	

				

	// payment_amt 

            

             payment_amt = api

                .column(13,{ page: 'current'})

                .data()

                .reduce( function (a, b) {

                    return intVal(a) + intVal(b);

                }, 0 );

               

            $( api.column(13).footer() ).html(parseFloat(payment_amt).toFixed(2));

			

			}

			

		}

			else{

			 var payment = data;

					 var api = this.api(), data;

					 

					 $( api.column(13).footer() ).html('');

					

				  }

				

			  }

								

				            });			  	 	

					  	 }	

}  

function transaction_detail(id)

{

	var transaction="";

	$("div.overlay").css("display", "block");

		$.ajax({

			  url:base_url+ "index.php/online/get/ajax_payment/"+id,

			 dataType:"JSON",

			 type:"POST",

			 async:false,

			 success:function(data){

			 	payment	=	data;

				console.log(payment);

			 	var gst =(payment.gst >0 ?  payment.currency_symbol+' '+ (payment.gst_type == 1 ?(payment.payment_amount*(payment.gst/100)):Math.round(parseFloat(payment.payment_amount)-(parseFloat(payment.payment_amount)*(100/(100+parseFloat(payment.gst))))))+' '+(payment.gst_type == 0?"(Amount inclusive of GST)":"(Amount exclusive of GST)"):'0.00');

			  var discount = payment.discount > 0 ? "<tr ><th>Discount</th><td>"+payment.discount+"</td></tr></tr>" : '';

			 	transaction  = "<table class='table table-bordered trans'><tr><th>Account Name</th><td>"+data.account_name+"</td></tr><tr><th>Mobile</th><td>"+data.mobile+"</td></tr><tr><th>Account No.</th><td>"+(data.scheme_acc_number == null ? 'Not Allocated':data.scheme_acc_number)+"</td></tr><tr><th>Date</th><td>"+payment.date_payment+"</td></tr><tr><th>Transaction ID</th><td>"+payment.trans_id+"</td></tr><tr><th>PayU ID</th><td>"+payment.payu_id+"</td></tr><tr><th>Mode</th><td>"+payment.payment_mode+"</td></tr><tr><th>Bank</th><td>"+payment.bank_name+"</td></tr><tr><th>Card No</th><td>"+payment.card_no+"</td></tr><tr><th>Paid Amount</th><td> "+payment.currency_symbol+"  "+(payment.no_of_dues>1?payment.act_amount:payment.payment_amount)+' + Charge : '+payment.currency_symbol+' '+payment.bank_charges+"</td></tr><tr ><th>GST</th><td>"+gst+"</td></tr></tr>"+discount+"<tr ><th>Remark</th><td><span class='label bg-yellow'>"+payment.remark+"</span></td></tr></table>"

				return transaction;

					$("div.overlay").css("display", "none");

					  },

					  error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

					  }	 

		  });

		   $("div.overlay").css("display", "none"); 

		return transaction;	

}

 //branch_name

 $('#branch_select').select2().on("change", function(e) {

			 switch(ctrl_page[1])

			{

				case 'payment':

					if(this.value!='')

					{  

						var from_date = $('#onlinePayment_list1').text();

						var to_date   = $('#onlinePayment_list2').text();

						var id =$(this).val();

						var settle =$("#id_settled").val((this).value);

						get_onlinepayment_list(from_date,to_date,id,settle);

					}

					 break;		

			} 

   });

//branch_name

});