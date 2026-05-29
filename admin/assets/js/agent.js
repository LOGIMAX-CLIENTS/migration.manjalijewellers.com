var path =  url_params();
var ctrl_page = path.route.split('/');
var page = ctrl_page[ctrl_page.length - 1]; 
$(document).ready(function() {
   get_agent_name();
    $("#payments .table td a").popover();
	if(ctrl_page[0] == 'agent' && (ctrl_page[1] == 'add'||ctrl_page[1]=='edit')){
            get_branchnames();
    }
    $('#agent_reff_report').dataTable({

          "bPaginate": true,

          "bLengthChange": true,

          "bFilter": true,

          "bSort": true,

          "bInfo": true,

          "bAutoWidth": false,

		  "order": [[ 0, "desc" ]],

		  "dom": 'T<"clear">lfrtip', "tableTools": { "aButtons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] }

        });
	if(ctrl_page[1]=='approval')
	{
	  
            	  var date = new Date();
            		    var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 6, 1); 
            			var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
            			var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
            			var id_branch=$('#id_branch').val();
            			 var settle =$("#id_settled").val((this).value);
            			get_approval_list(from_date,to_date,id_branch,settle);
            	 var settle =$("#id_settled").val((this).value);
            	 //date picker
            	$('#settle_list1').empty();
            	$('#settle_list2').empty();
            	$('#settle_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
            	$('#settle_list2').text(moment().endOf('month').format('YYYY-MM-DD'));	
            	$('#settlement-dt-btn').daterangepicker(
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
            			 
                         get_approval_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),id_branch,settle);
            			  $('#onlinePayment_list1').text(start.format('YYYY-MM-DD'));
            			  $('#onlinePayment_list2').text(end.format('YYYY-MM-DD')); 
                      }
                    ); 
			//get_approval_list('','','',settle);
	}else if(ctrl_page[1] == "agent_settlement" && page == "list"){
            	    var date = new Date();
                var firstDay    =   new Date(date.getFullYear(), date.getMonth(),date.getDate() - 30, 1);
                var from_date   =   firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
            	var to_date     =   (date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());
                setAgent_SettlementList(from_date,to_date,'','');
                $('#agent_date1').empty();
                $('#agent_date2').empty();
                $('#agent_date1').text(moment().startOf('month').format('YYYY-MM-DD'));
                $('#agent_date2').text(moment().endOf('month').format('YYYY-MM-DD'));
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
                
                setAgent_SettlementList(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),'',$("#agent_select").val())
                $('#agent_date1').text(start.format('YYYY-MM-DD'));
                $('#agent_date2').text(end.format('YYYY-MM-DD'));
                }
                );
            	    
            		
            	
            		$("#branch_select").on("change",function(e){ 
            			setAgent_SettlementList(from_date,to_date,id_branch,'');
            		});
            		
            		$('#agent_select').select2().on("change", function(e){
                		if(this.value!='')
                		{
                		    agent = this.value;
                		    setAgent_SettlementList(from_date,to_date,'',agent);
                		}
            		});
	} else if(ctrl_page[1] == 'agent_report'){
	    if(ctrl_page[2] == 'list'){
	       //set_agentreport_table();
	       
	       var date = new Date();
                var firstDay    =   new Date(date.getFullYear(), date.getMonth(),date.getDate() - 30, 1);
                var from_date   =   firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
            	var to_date     =   (date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());
                set_agentreport_table(from_date,to_date,$('#agent_select').val());
                $('#agent_select').select2().on("change", function(e){
                		if(this.value!='')
                		{
                		    agent = this.value;
                		    set_agentreport_table(from_date,to_date,agent);
                		}
            		});
                $('#referral_list1').empty();
                $('#referral_list2').empty();
                $('#referral_list1').text(moment().startOf('month').format('YYYY-MM-DD'));
                $('#referral_list2').text(moment().endOf('month').format('YYYY-MM-DD'));
                $('#settlement-dt-btn').daterangepicker(
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
                set_agentreport_table(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'),$('#agent_select').val())
                $('#referral_list1').text(start.format('YYYY-MM-DD'));
                $('#referral_list2').text(end.format('YYYY-MM-DD'));
                }
                );
	    }
	    
		
	
		if(ctrl_page[2] == 'summary'){
		    var date = new Date();
                var firstDay    =   new Date(date.getFullYear(), date.getMonth(),date.getDate() - 30, 1);
                var from_date   =   firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
            	var to_date     =   (date.getFullYear()+"-"+(date.getMonth() + 1)+"-"+date.getDate());
            	
            	if(ctrl_page[3]){
                	       if(ctrl_page[3].length == 10)
                		    {
                		        $('#mobilenumber').val(ctrl_page[3]);
                		        get_agent_details(from_date,to_date);
                		    }
                	    }
                		$('#cus_search').on('click',function(){
                		    var mobile_num=$('#mobilenumber').val();
                		    if(mobile_num.length!=10)
                		    {
                		        alert('Please Enter The Valid Number..');
                		        $('#mobilenumber').val('');
                		        $('#mobilenumber').focus();
                		    }else{
                		        get_agent_details(from_date,to_date);
                		    }
            });
            	
            	var mobile_num=$('#mobilenumber').val();
        		    if(mobile_num.length!=10)
        		    {
        		        //alert('Please Enter The Valid Number..');
        		        $('#mobilenumber').val('');
        		        $('#mobilenumber').focus();
        		    }else{
        		        get_agent_details(from_date,to_date);
        		    }
                
                $('#summary_date1').empty();
                $('#summary_date2').empty();
                $('#summary_date1').text(moment().startOf('month').format('YYYY-MM-DD'));
                $('#summary_date2').text(moment().endOf('month').format('YYYY-MM-DD'));
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
                var mobile_num=$('#mobilenumber').val();
                		    if(mobile_num.length!=10)
                		    {
                		        alert('Please Enter The Valid Number..');
                		        $('#mobilenumber').val('');
                		        $('#mobilenumber').focus();
                		    }else{
                		        get_agent_details(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))
                		    }
                $('#summary_date1').text(start.format('YYYY-MM-DD'));
                $('#summary_date2').text(end.format('YYYY-MM-DD'));
                }
                );
		}
		
	}

    
	  
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
			settle_status=$("input[name='pay_status']:checked").val();
			pay_id=selected;
			update_status(settle_status,pay_id);
		}
   });

// settled pay show in payment apprval page with filter//HH
$('#settle_Select').select2().on("change", function(e) 
{           if(this.value!='')
			{  
			 var settle =$("#id_settled").val((this).value);
				get_approval_list('','','',settle);
			}
});
// settled pay show in payment apprval page with filter//





if(ctrl_page[0]=='agent'){
	var last_month = moment().subtract(29, 'days');
	var today = moment();
	get_agent_list(last_month.format('YYYY-MM-DD'),today.format('YYYY-MM-DD'));
}	

$('#village_select').select2().on("change", function(e) {
	console.log("village selected");
	if(this.value!=''){  
		var id=$(this).val();
		$('#id_village').val(id);
		get_agent_list('','','',id)
	}
});

$('#branch_select').select2().on("change", function(e) { 
	if(this.value!=''){
		$("#id_branch").val(this.value);
		var id_branch=$('#id_branch').val();
		get_agent_list('','',id_branch)
	}
});

$("#village_select").select2({
	placeholder: "Enter Village",
	allowClear: true
});	

if(path.route=='agent'){ 
	get_village();
	$('#customer-dt-btn').daterangepicker({
		ranges: {'Today': [moment(), moment()],
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
		get_agent_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))
    }
    ); 
} 

if($('#country').length>0)
{
    	get_country();	
    	
    	get_village();
}

$('#country').select2().on('change', function() {
      
        if(this.value!='')
        {
             get_state(this.value);
             
             $('#city').empty();
             
             $('#cityval').empty();
			 
			 
             $('#select2-city-container').empty();
			
             
             $("#city option:selected").text();
        }
}); 

$('#state').select2().on('change', function() {
        if(this.value!='')
        {
            get_city(this.value);
        }

});

$("#state").select2({
		placeholder: "Enter State",

		allowClear: true
});	

$("#city").select2({

	placeholder: "Enter City",
	allowClear: true

});			

$("#country").select2({
	placeholder: "Enter Country",
	allowClear: true
});	
	
$("#Village").select2({
	placeholder: "Enter Village",
	allowClear: true
});	
	
$("#village_select").select2({
	placeholder: "Enter Village",
	allowClear: true
});	


$("#pref_mode").on("change",function(e){ 
    var pref_mode = $('#pref_mode').find(":selected").val();
    if(pref_mode == 2){
        $('#utr_no').removeAttr('disabled');
    }
});

});        /////

 function update_status(settle_status="",settle_id="")
{
	my_Date = new Date();
	 $("div.overlay").css("display", "block"); 
	$.ajax({
			  url:base_url+ "index.php/admin_agent/update_status?nocache=" + my_Date.getUTCSeconds(),
			 data:  {'settle_status':settle_status,'settle_id':settle_id},
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

 function get_approval_list(from_date="",to_date="",id_branch="",settle="")
{
    console.log(from_date);
	my_Date = new Date();
	var type=$('#date_Select').find(":selected").val();
     var settle=$('#settle_Select').find(":selected").val();
	 $("div.overlay").css("display", "block"); 
	$.ajax({
			  url:base_url+ "index.php/admin_agent/ajax_onlineSettlements?nocache=" + my_Date.getUTCSeconds(),
			 data:  {'from_date':from_date,'to_date':to_date,'id_branch':id_branch,'date_type':type,'settle':settle},
			 dataType:"JSON",
			 type:"POST",
			 success:function(data){
			 	$('#total_payments').text(data.data.length);
			   			set_approval_list(data.data);
			   			 $("div.overlay").css("display", "none"); 
					  },
					  error:function(error)  
					  {
						 $("div.overlay").css("display", "none"); 
					  }	 
		  });
}
function set_approval_list(data)
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
				                	chekbox=' <input type="checkbox" class="pay_id" name="pay_id[]" value="'+row.id_transaction+'"  /> ';
				                	
				                	return chekbox+" "+row.id_transaction;
				                }},
					                { "mDataProp": "name" },
					                { "mDataProp": "agent_code" },
					                { "mDataProp": "cash_point" },
					                { "mDataProp": "request_date" },
					                { "mDataProp": "scheme_acc_number" },
					                { "mDataProp": "trans_type" },
					                { "mDataProp": function(row,type,val,meta){
					                if(row.status == 1)
					                {
	                	                 status = "<span class='label bg-green-active'>Active</span>";
					                }else if(row.status == 2)
					                {
					                     status = "<span class='label bg-orange-active'>Settled</span>";
					                }
					                else if(row.status == 0)
					                {
					                     status = "<span class='label bg-red-active'>Expired</span>";
					                }
					                else if(row.status == 4)
					                {
					                     status = "<span class='label bg-blue-active'>Partly Settled</span>";
					                }
					                return 	status;
	               }}
					                
					              ],
								
								
						
								
				            });			  	 	
					  	 }	
}



function get_agent_list(from_date="",to_date="",id_branch="",id_village="")
{

	my_Date = new Date();
	var type=$('#date_Select').find(":selected").val();
	$("div.overlay").css("display", "block"); 
	$.ajax({
		  url:base_url+"index.php/agent/ajax_list?nocache=" + my_Date.getUTCSeconds(),
		  data: (from_date !='' || id_branch!='' || to_date !='' || id_village!=''? {'from_date':from_date,'to_date':to_date,'id_branch':id_branch,'id_village':id_village,'date_type':type}: ''),
		  dataType:"JSON",
		  type:"POST",
		  success:function(data){
			 console.log(data);
			set_agent_list(data);
	   		$("div.overlay").css("display", "none"); 
		  },
		  error:function(error){
			$("div.overlay").css("display", "none"); 
		  }	 
	});
}

function set_agent_list(data)	
{
	var customer = data.agent;
	var access = data.access;
	console.log(access.edit);
    var oTable = $('#agent_list').DataTable();
    $("#total_customers").text(customer.length);
    if(access.add == '0'){
		$('#add_agent').attr('disabled','disabled');
    }
	oTable.clear().draw();
   	if (customer!= null && customer.length > 0){
		oTable = $('#agent_list').dataTable({
			"bDestroy": true,
			"bInfo": true,
			"bFilter": true,
			"bSort": true,
			"dom": 'lBfrtip',
			"buttons" : ['excel','print'],
			"tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
			"aaData": customer,
			"order": [[ 0, "desc" ]],
			"aoColumns": [{ "mDataProp":function (row,type,val,meta){
											id=row.id_agent;
											return "<label class='checkbox-inline'><input type='checkbox' class='flat-red' name='id_agent[]' value='"+row.id_agent+"' /> "+id+" </label>";
										} 
						  },    //agent id

					      { "mDataProp":function (row,type,val,meta){
											var title = row.title!=null?row.title+". ":'';
											return title+""+row.name;
										} 
						  },    // name
						  
						  { "mDataProp": "agent_code" },   // ref code
						  
					      { "mDataProp": "mobile" },   // ph no

						  { "mDataProp": function ( row, type, val, meta ){
											
												return row.date_add;
											
												
											
	                                     }
						  },	 // date add			                

					      { "mDataProp": function ( row, type, val, meta ){
											active_url =base_url+"index.php/admin_agent/agent_status/"+(row.active==1?0:1)+"/"+row.id_agent; 
											return "<a href='"+active_url+"'><i class='fa "+(row.active==1?'fa-check':'fa-remove')+"' style='color:"+(row.active==1?'green':'red')+"'></i></a>"
					                	}
					      },   // status active or not

					      { "mDataProp": function ( row, type, val, meta ) {					              
											return (row.added_by=='0'?"Web":(row.added_by=='1'?"Admin":"Mobile"));
										 }
						  },     // added from web/ mobile/ app
 						
						  { "mDataProp": function ( row, type, val, meta ) {
											id= row.id_agent;
											edit_url=(access.edit=='1' ? base_url+'index.php/agent/edit/'+id : '#' );
											delete_url=(access.delete=='1' ? base_url+'index.php/agent/delete/'+id : '#' );
											delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
											action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+'<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>'+'<li><a href="#" class="btn-del" data-href="'+delete_url+'" data-toggle="modal" data-target="'+delete_confirm+'"  ><i class="fa fa-trash"></i> Delete</a></li></ul></div>';
											return action_content;
					                	}				           
					       }]      // edit/ del actions
		});	
	 }  
}

 function update_status(settle_status="",settle_id="")
{
	my_Date = new Date();
	 $("div.overlay").css("display", "block"); 
	$.ajax({
			  url:base_url+ "index.php/admin_agent/update_status?nocache=" + my_Date.getUTCSeconds(),
			 data:  {'settle_status':settle_status,'settle_id':settle_id},
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

function get_country()
{
$('.overlay').css('display','block');

	$.ajax({

	  type: 'GET',

	  url:  base_url+'index.php/settings/company/getcountry',

	  dataType: 'json',

	  success: function(country) {

		$.each(country, function (key, country) {

			$('#country').append(

				$("<option></option>")

				  .attr("value", country.id)

				  .text(country.name)
			);

		});


		$("#country").select2("val", ($('#countryval').val()!=null?$('#countryval').val():''));

			var selectid=$('#countryval').val();

			if(selectid!=null && selectid > 0)

			{

				$('#country').val(selectid);
				
				$('.overlay').css('display','block');

				get_state(selectid);

			}

		$('.overlay').css('display','none');

		},

	  	 error:function(error)  

					  {

						 $("div.overlay").css("display", "none"); 

				  }
	});

}




function get_village()
{
    $('.overlay').css('display','block');
	$.ajax({
	  type: 'GET',
	  url:  base_url+'index.php/admin_settings/ajax_village_list',
	  dataType: 'json',
	  success: function(data) {
			console.log("village : ",data.list);

var id_village=$('#id_village').val();
		$.each(data.list, function (key, data) {
			$('#Village').append(
				$("<option></option>")
        		  .attr("value", data.id_village)
				  .text(data.village_name)
			);
				$('#village_select').append(
				$("<option></option>")
        		  .attr("value", data.id_village)
				  .text(data.village_name)
			);
		});
	
			if(ctrl_page[1]=='edit'||ctrl_page[1]=='add')
		{
		    $("#Village").select2("val",(id_village!=''?id_village:''));
		}
	
		if(ctrl_page[0]=='customer')
		{
		    	$("#village_select").select2("val",(id_village!=''?id_village:''));
		}
		var selectid=$('#id_village').val();
    	if(selectid!=null && selectid > 0)
    	{
				$('#Village').val(selectid);
				$('.overlay').css('display','block');
		}
		$('.overlay').css('display','none');
		},
	  	 error:function(error)  
					  {
                        $("div.overlay").css("display", "none"); 

					  }
	});
	}

$('#Village').select2().on("change", function(e) {
if(this.value!='')
{  
var id=$(this).val();
$('#id_village').val(id);
get_village_list(id);
}
});

// $('#village_select').select2().on("change", function(e) {
// if(this.value!='')
// {  
// var id=$(this).val();
// $('#id_village').val(id);
// get_customer_list('','','',id)
// }
// });
	
	function get_village_list(id_village)
	{
	   $.ajax({
	  type: 'POST',
	  data:{'id_village':id_village},
	  url:  base_url+'index.php/admin_settings/ajax_village_list',
	  dataType: 'json',
	  success: function(data) {
	        $('#post_office').val(data.post_office);
	        $('#taluk').val(data.taluk);
	         $('#pincode').val(data.pincode);
	
		}
	       
	   });
	}


function get_state(id)

{
	$('.overlay').css('display','block');

	$('#state option').remove();

	$.ajax({

	  type: 'POST',

	   data:{'id_country':id },

	  url:  base_url+'index.php/settings/company/getstate',

	  dataType: 'json',

	  success: function(state) {
	
		$.each(state, function (key, state) {

			$('#state').append(

				$("<option></option>")

			  .attr("value", state.id)

				  .text(state.name)

			);

		});

		$("#state").select2("val", ($('#stateval').val()!=null?$('#stateval').val():''));

		var selectid=$('#stateval').val();

		    console.log(selectid);

		if(selectid!=null && selectid>0)

		{

			$('#state').val(selectid);
	    get_city(selectid);
	    }
		$('.overlay').css('display','none');
	  },
error:function(error)  
{
						 $("div.overlay").css("display", "none"); 
					  }
	});
}
function get_city(id)
{  
	$('.overlay').css('display','block');
	$('#city option').remove();		  	
	$("#city").css("display", "block");
	$.ajax({
	  type: 'POST',
	  data:{'id_state':id },
	  url:  base_url+'index.php/settings/company/getcity',
	  dataType: 'json',
	  success: function(city) {
		$.each(city, function (key, city) {
			$('#city').append(
				$("<option></option>")
				  .attr("value", city.id)
				  .text(city.name)
			);
		});
		$("#city").select2("val", ($('#cityval').val()!=null?$('#cityval').val():''));
		var selectid=$('#cityval').val();
		if(selectid!=null && selectid>0)
		{
			$('#city').val(selectid);		 
	    }
	    $('.overlay').css('display','none');
	  },
	  	 error:function(error)  
					  {
						 $("div.overlay").css("display", "none"); 
					  }
	});
}

function setAgent_SettlementList(from_date,to_date,id_branch='',id_agent='')
{
		my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_agent/agent_settlement/ajax?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 data : {"from_date": from_date,"to_date" :to_date,"id_branch" : id_branch,"id_agent":id_agent},
			 type:"POST",
			 cache:false,
			 success:function(data) {
                   	var list                     = data.data.list;
					var index                    = 1;
					var minium_settlement_amount = data.data.minium_settlement_amount;
					$("#settle_amount_limitaion").text(data.data.minium_settlement_amount);
					$("#settle_max_amount_limitaion").text(data.data.maximum_settlement_amount);
					var access   = data.data.access;
    				var oTable   =   $('#agent_settlement').DataTable();
    				oTable.clear().draw();				  
    				if (list!= null && list.length > 0)
    				{  	
    					oTable = $('#agent_settlement').dataTable({
    						"bDestroy": true,
    		                "bInfo": true,
    		                "bFilter": true,
    		                "scrollX":'100%',
    		                "bSort": true,
    		                "dom": 'lBfrtip',
    		                "order": [[ 0, "desc" ]],
    		                "lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
    						"aaData": list,
    						"aoColumns": [	
									{ "mDataProp": function ( row, type, val, meta ){
									 index  += parseFloat(meta.row);
									  var branch  = row.id_branch!=null ? row.id_branch:'';
									  if(parseFloat(row.cash_points) >= parseFloat(minium_settlement_amount))
									  {
									    return '<input type="checkbox" class="id_influencer_req" name="influencer_req[]" value="'+row.cus_id+'"/><input type="hidden" class="id_branch" value="'+branch+'">&nbsp;'+index;
									  }
									  else
									  {
										   return '<input type="hidden" class="id_orderdetails" name="id_orderdetails[]" value="'+row.cus_id+'" disabled/><input type="hidden" class="id_branch" value="'+branch+'">&nbsp;'+index;
									  }
									 },},
									{ "mDataProp": "agent_name" },
									{ "mDataProp": "mobile" },
									{ "mDataProp": "branch_name" },
									{ "mDataProp": "no_of_referrals" },
									{ "mDataProp":"cash_points"},
									{ "mDataProp": function ( row, type, val, meta ){
										if(parseFloat(row.cash_points) >= parseFloat(minium_settlement_amount)){ 
											var html = '<input  type="number"  id="settle_amt" class="settle_amt" value="'+minium_settlement_amount+'">';
											$('.settle_amt').val(minium_settlement_amount);
					                	 return html;	
										 }else{
										     return '-';
										 }
										 },},
										 {
										"mDataProp": null,
										"sClass": "control center", 
										"sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
									},
									{ "mDataProp": function (row, type, val, meta) { 
									var min_amt_settle = data.data.minium_settlement_amount;
									var max_amt_settle = data.data.maximum_settlement_amount;
									label = '<a href="#" class="btn btn-success" data-href="#" data-toggle="modal" data-target="#req-update-modal" onclick="get_agentsettlement_records('+row.cus_id+','+min_amt_settle+','+max_amt_settle+')"> Settlement</a>' ;
									return label;
		                            },},
									
    						],
    					});
    				    var anOpen =[]; 
                		$(document).on('click',"#agent_settlement .control", function(){ 
                		   var nTr = this.parentNode;
                		   var i = $.inArray( nTr, anOpen );
                		   if ( i === -1 ) { 
                				$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>');
                                 var oData = oTable.fnGetData( nTr );  var transcation_bill  = oData.bill_transcation;
								 if(transcation_bill.length>0){ 								
									oTable.fnOpen( nTr, fnFormatRowOrderDetails(oTable, nTr), 'details' );
									anOpen.push( nTr ); 
								 }
								 else { 
									$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
									oTable.fnClose( nTr );
									anOpen.splice( i, 1 );
								}
                		    }
                		    else { 
                				$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
                				oTable.fnClose( nTr );
                				anOpen.splice( i, 1 );
                		    }
                		} );
    				}
				$("div.overlay").css("display", "none"); 
		    },
		    error:function(error)  
		    {
			    $("div.overlay").css("display", "none"); 
		    }	 
	});
}

function get_settlement_records()
{
	var selected = [];
	var settleData;
	
    	$("#agent_settlement tbody tr").each(function(index, value){
    		 if($(this).find('td:first .id_influencer_req').prop('checked') == true){
    		     var row_amt = $(this).find("td:eq(6) .settle_amt").val();
    		     var min_amt = $("#settle_amount_limitaion").text();
    			 var max_amt = $("#settle_max_amount_limitaion").text();
    			
    		     /*if(row_amt < min_amt || row_amt > max_amt)
    		      
    		     {
    		         
    		         alert("Enter Amount Within limit");
    		     }*/
    		    // else {
        			settleData = { 
        						 'id_agent'         : $(this).find('td:first .id_influencer_req').val(),
        						  'settlement_pts'     : $(this).find("td:eq(6) .settle_amt").val(),
        						  'pts_type'           :'1',
        						  'settlement_branch'  : ($(this).find("td:eq(0) .id_branch").val() != "" ? ($(this).find("td:eq(0).id_branch").val()): ($("#branch_select").val() !='' ?$("#branch_select").val() :'')) 						
        			}
        			selected.push(settleData);
        			
    		     //}
    		}
    		
    	});
	
	
	if(selected.length >0)
	{
		update_settlementamount(selected);
	}
	else
	{
		alert("Kindly Select Any One Agent Records to Settle Cash Points");
	}
}
function update_settlementamount(data)
{
    //console.log(data);
	my_Date = new Date();
	$("div.overlay").css("display", "block"); 
	$.ajax({
    	 url:base_url+ "index.php/admin_agent/agent_settlement/bulk_settlement?nocache=" + my_Date.getUTCSeconds(),
    	 data:  {'settlement_records':data,'settlement_branch':$("#settlement_branch").val()},
    	 type:"POST",
    	 dataType:"JSON",
    	  cache:false,
    	 async:false,
    	 	  success:function(data){
    	            setAgent_SettlementList();
    	            
    	            console.log(data);
    	            if(data.status){
                $("#cus_search").trigger("click");
                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                    }
                    else{
                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                    }
            //location.reload(true);
    	   		    $("div.overlay").css("display", "none"); 
    		  },
    		  error:function(error)
    		  {
    		  		console.log(error);
    			 $("div.overlay").css("display", "none"); 
    		  }	 
      }); 
}

function updInfluencerSettlement()
{
	var selected = [];
	var settlement_pts = 0;
	$("#pending_settlmt_list tbody tr").each(function(index, value){
		 if($(this).find('td:first .cus_loyal_tran_id').prop('checked') == true){				
			settleData = {
						 'id_cus_loyal_tran'   : $(this).find('td:first .id_cus_loyal_tran').val(),
						  'settlement_pts'     : $(this).find('td:first .cash_point').val()
			}
			selected.push(settleData);
			settlement_pts = settlement_pts + parseFloat(settleData.settlement_pts);
		}
	});
	if(selected.length >0 &&  settlement_pts >= $('#influ_minimum_amt_required_to_settle').val() )
	{
		my_Date = new Date();
    	$("div.overlay").css("display", "block"); 
    	$.ajax({
        	 url:base_url+ "index.php/admin_ret_loyalty/influencer_settlement/settl_amt_by_bill?nocache=" + my_Date.getUTCSeconds(),
        	 data:  {'settlement_records':selected, 'id_customer' : $("#id_customer").val(), 'cus_cash_points' : $("#cus_cash_points").val(), 'settlement_pts' : settlement_pts},
        	 type:"POST",
        	 cache:false,
        	 async:false,
        	 	  success:function(data){
        	            $('#cus_search').trigger("click");
        	   		    $("div.overlay").css("display", "none"); 
        	   		    $.toaster({ priority : 'info', title : 'Influencer Settlement!', message : ''+"</br>"+data.message});
        		  },
        		  error:function(error)
        		  {
        		  		console.log(error);
        			 $("div.overlay").css("display", "none"); 
        		  }	 
          }); 
	}else{
	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Kindly select settlement record'});
	}
}

function fnFormatRowOrderDetails( oTable, nTr )
{
  var oData = oTable.fnGetData( nTr );
  var rowDetail = '';
  var prodTable = 
     '<div class="innerDetails">'+
      '<table class="table table-responsive table-bordered text-center table-sm">'+ 
        '<tr class="bg-teal">'+
        '<th>S.No</th>'+ 
        '<th>Cash Point</th>'+ 
        '<th>Scheme Acc</th>'+
        '<th>Issue Type</th>'+
        '<th>Credits for</th>'+
        '</tr>';
    var transcation_bill  = oData.bill_transcation; 
    $.each(transcation_bill, function (idx, val) {
        var transcation_bill_status ='';
        var cr_based_on ='';
        var credit_for = "<span class='badge bg-orange'>"+val.credit_for+"</span>";
        if(val.status==0)
        {
            transcation_bill_status="<span class='badge bg-red'>Expired</span>";
        }else if(val.status==1)
        {
            transcation_bill_status="<span class='badge bg-green'>Active</span>";
        }
        else if(val.status==2)
        {
            transcation_bill_status="<span class='badge bg-blue'>Settled</span>";
        }
		if(val.cr_based_on==1)
        {
            cr_based_on="<span class='badge bg-danger'>General settings</span>";
        }else if(val.cr_based_on==2)
        {
            cr_based_on="<span class='badge bg-orange'>Category settings</span>";
        }else if(val.cr_based_on==3)
        {
            cr_based_on="<span class='badge bg-orange'>Purchase Plan settings</span>";
        }
  	prodTable += 
        '<tr class="order_det_btn">'+
        '<td>'+parseFloat(idx+1)+'</td>'+
        '<td>'+val.cash_point+'</td>'+
        '<td>'+val.sch_acc_no+'</td>'+
        '<td>'+val.issue_type+'</td>'+
        '<td>'+credit_for+'</td>'+
        '</tr>'; 
  }); 
  rowDetail = prodTable+'</table></div>';
  return rowDetail;
}

function get_agentsettlement_records(agent_id,min_amt,max_amt)
{
    
    my_Date = new Date();
    	//$("div.overlay").css("display", "block"); 
    	$.ajax({
        	 url:base_url+ "index.php/admin_agent/getAgentBankDetails?nocache=" + my_Date.getUTCSeconds(),
        	 dataType:'json',
        	 data:  {'id_agent':agent_id},
        	 type:"GET",
        	  cache:false,
        	 async:false,
        	 	  success:function(data){
        	 	     
        	            $("#cus_id").val(agent_id);
   	                    $("#min_amt").val(min_amt);
	                    $("#max_amt").val(max_amt);
	                    $("#min_settle").text('₹ '+min_amt+' - ₹ '+max_amt);
	                    var bank_data = '<li class="list-group-item"><i class="fa fa-money "></i> <b>Payment Mode</b> <a class="" style="margin-left:180px;">'+data.agent.preferred_mode+'</a></li> <li class="list-group-item"><i class="fa fa-bank "></i> <b>Bank Name</b> <a class="" style="margin-left:200px;">'+data.agent.bank_name+'</a></li><li class="list-group-item"><i class="fa fa-bank "></i> <b>Bank IFSC</b> <a class="" style="margin-left:209px;">'+data.agent.ifsc_code+'</a></li> <li class="list-group-item"><i class="fa fa-bank "></i> <b>Account No.</b> <a class="" style="margin-left:198px;">'+data.agent.bank_account_number+'</a></li>';
        		        $('.profile-list').html(bank_data);
        		        
        		        $("#preferred_mode").val(data.agent.preferred_mode);
        		        $("#bank_name").val(data.agent.bank_name);
        		        $("#ifsc_code").val(data.agent.ifsc_code); 
        		        $("#bank_account_number").val(data.agent.bank_account_number);
        		        
	                    },
        		  error:function(error)
        		  {
        		  		console.log(error);
        		  }	 
          }); 
   	
}

function updateSettlement()
{
    
    var id_agent = $("#cus_id").val();
    var settle_pts = $("#settle_pts").val();
    
    var utr = $("#utr_no").val();
    var pref_mode = $("#pref_mode").val();
    var preferred_mode = $("#preferred_mode").val();
    var bank_name = $("#bank_name").val();
    var ifsc_code = $("#ifsc_code").val();
    var bank_account_number = $("#bank_account_number").val();

    if(settle_pts == '' || settle_pts == 0)
    {
        msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Settlement Amount</div>';
        $("#error").html(msg);
    }
    else if((settle_pts < $("#min_amt").val() || settle_pts > $("#max_amt").val()))
    {
       
        msg='<div class = "alert alert-danger"><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong>Warning!</strong>Enter Amount within limit</div>';
        $("#error").html(msg);
    }
    else{
        $("#error").hide();
        my_Date = new Date();
    	$("div.overlay").css("display", "block"); 
    	$.ajax({
        	 url:base_url+ "index.php/admin_agent/agent_settlement/settlement_amount?nocache=" + my_Date.getUTCSeconds(),
        	 data:  {'id_agent':id_agent,'settlement_branch':$("#settlement_branch").val(),'settlement_pts':settle_pts,'utr_no':utr,'pref_mode':pref_mode,'preferred_mode':preferred_mode,'bank_name':bank_name,'ifsc_code':ifsc_code,'bank_account_number':bank_account_number},
        	 type:"POST",
        	  cache:false,
        	 async:false,
        	 	  success:function(data){
        	 	      console.log(data);
        	            setAgent_SettlementList();
        	            location.reload(true);
        	   		    $("div.overlay").css("display", "none"); 
        		  },
        		  error:function(error)
        		  {
        		  		console.log(error);
        			 $("div.overlay").css("display", "none"); 
        		  }	 
          }); 
    }
}

function get_agent_name()
{
	//$("#spinner").css('display','none');
	//$(".overlay").css('display','block');
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/agent/ajax_getAgents',
		dataType:'json',
		success:function(data){
		console.log(data);
		 $("#spinner").css('display','none');
		   $.each(data.employee, function (key, item) {
		   console.log(item.id_agent);
			   		$('#agent_select').append(
						$("<option></option>")
						.attr("value", item.id_agent)
						  .text(item.agent_name )
					);
			});
			$("#agent_select").select2({
			    placeholder: "Select Agent Name ",
			    allowClear: true,
		    });

			 $("#agent_select").select2("val", ($('#id_agent').val()!=null?$('#id_agent').val():''));
			 $(".overlay").css("display", "none");
		}

	});
}

function set_agentreport_table(from_date,to_date,agent)
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_agent/agent_report/list_ajax?nocache=" + my_Date.getUTCSeconds(),
			 data:{'from_date':from_date,'to_date':to_date,'id_agent':agent},
			 dataType:"JSON",
			 type:"POST",
			 cache:false,
			 success:function(data) {
			      $("div.overlay").css("display", "none"); 
                var recordList 	= data.list.records;
                var access  = data.access;
				//$('#record_count').text(recordList.length);
                var oTable = $('#agent_referral_list').DataTable();
                oTable.clear().draw();
                if (recordList != null && recordList.length > 0)
                {
                    oTable = $('#agent_referral_list').dataTable({
                            "bDestroy": true,
                            "bInfo": true,
                            "bFilter": true,
                            "bSort": true,
                            "order": [[ 0, "desc" ]],
                            "dom": 'lBfrtip',
                            "buttons" : ['excel','print'],
                            "tableTools": { "buttons": [ { "sExtends": "xls",
                            "oSelectorOpts": { page: 'current' 
                            } },
                            { "sExtends": "pdf", 
                            "oSelectorOpts": { page: 'current' 
                            } } ] },				
                            "aaData"  : recordList,
                            "aoColumns": [	
                                            { "mDataProp": function ( row, type, val, meta ) {
                                            action = '<a href="'+base_url+'index.php/admin_agent/agent_referral_account/'+row.agent_code+'" target="_blank">'+row.id_agent+'</a>';
                                            return action;
                                            }},
                                            { "mDataProp": "agent_name" },
                                            { "mDataProp": "earnings" },
                                            { "mDataProp": "referrals" },
                                            { "mDataProp": "conversions" },
                                            { "mDataProp": "unpaid" },
                                            { "mDataProp": "unpaid" },
                                            { "mDataProp": "revenue" },
                                            { "mDataProp": function ( row, type, val, meta ) {
                                                var  summary_url = base_url+ "index.php/agent/agent_report/summary/";
                                                return '<a href="'+summary_url+'" target="blank" class="btn btn-info btn-edit" id="edit" role="button" data-toggle="modal" data-id='+row.id_customer+'><i class="fa fa-eye" ></i></a>';
                                                }
                                            }
                                           
                                            ]
                    });
                }
		    },
		    error:function(error)  
		    {
			    $("div.overlay").css("display", "none"); 
		    }	 
	});
}

$("#update_Infprofile").on('click',function(){
	var data = {
	            "id_agent"  : $("#edit-id-cus").val(),
	            "payment_mode" : $("#ed_pay_mode").val(),
	            "bank_name" : $("#ed_bank_name").val(),
	            "bank_ifsc" : $("#ed_bank_ifsc").val(),
	            "bank_acc_no" : $("#ed_bank_acc_no").val()
	            };
	update_infProfile(data);
});

function update_infProfile(postData)
{
    $("div.overlay").css("display", "block");
    my_Date = new Date();
	$.ajax({
        url:base_url+ "index.php/admin_agent/agent_report/updprofile?nocache=" + my_Date.getUTCSeconds(),
        data:{'data':postData},
        dataType:"JSON",
        type:"POST",
        cache:false,
        success:function(data) {
            $("div.overlay").css("display", "none");
            if(data.status){
                $("#cus_search").trigger("click");
                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
            }
            else{
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
            }
        },
        error:function(error)
        {
            $("div.overlay").css("display", "none"); 
        }	 
	});
}


function get_agent_details(from_date,to_date)
{
     $('.overlay').css('display','block');
     my_Date = new Date();
	 $.ajax({
		url:base_url+ "index.php/admin_agent/agent_report/ajax?nocache=" + my_Date.getUTCSeconds(),
        type: 'POST',
        dataType: 'json',
        data:{'from_date' : from_date,'to_date': to_date, 'mobile':(ctrl_page[4]!='' && ctrl_page[4]!=undefined ? ctrl_page[4] :$('#mobilenumber').val())},
        success: function(data) { 
            $('.overlay').css('display','none');
            if(data.list.count == 0)
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.list.msg});
            }
            else{
                	$('.profile-list').html('');
                    var agent = data.list.agent;
        			var transHtml = "";
        			var refHtml = "";
        			var ordersHtml = "";
        			var referralHtml = "";
        			var pendingSettlmtHtml = "";
        			$('#transactions_list > tbody').html('');
        			$('#referrals_list > tbody').html('');
        			$('#pending_settlmt_list > tbody').html('');
        			
        			    $("#editInfProf").css("display","block");
        			    
        			    $('#earnings').html("&#8377; "+data.list.earnings);
        			    $('#cus_cash_points').val(agent.cus_cash_pts);
        			    $('#influ_minimum_amt_required_to_settle').val(agent.influ_minimum_amt_required_to_settle);
        				$(".influencer-blk").css("display","block");
        			    $(".influencer-blk,#orders").removeClass("active");
        			    $("#transactions,.my_trans").addClass("active");
        				$(".orders-blk").css("display","none");
        				$(".expiring-blk").css("display","none");	
        				$(".customer-blk").css("display","none");	
        				$('.profile-list').append('<li class="list-group-item"><i class="fa fa-money "></i> <b>Payment Mode</b> <a class="pull-right">'+agent.pref_pay_mode+'</a></li>');				
        				$('.profile-list').append('<li class="list-group-item"><i class="fa fa-bank "></i> <b>Bank Name</b> <a class="pull-right">'+agent.bank_name+'</a></li>');				
        				$('.profile-list').append('<li class="list-group-item"><i class="fa fa-bank "></i> <b>Bank IFSC</b> <a class="pull-right">'+agent.bank_ifsc+'</a></li>');				
        				$('.profile-list').append('<li class="list-group-item"><i class="fa fa-bank "></i> <b>Account No.</b> <a class="pull-right">'+agent.bank_acc_number+'</a></li>');				
        			    $(".modal-body #edit-id-cus").val(agent.id_agent);
        			    $(".modal-body #ed_pay_mode").val(agent.preferred_mode);
        			    $(".modal-body #ed_bank_name").val(agent.bank_name);
        			    $(".modal-body #ed_bank_ifsc").val(agent.bank_ifsc);
        			    $(".modal-body #ed_bank_acc_no").val(agent.bank_acc_number);
        			    $('#invites_count').html(data.list.total_referrals);
                        $('#conversions_count').html(data.list.conversions);
                        $('#sales_count').html(data.list.unpaid);
                        if(data.access.edit == 1){
                            $(".upd_settlmt_blk").css("display","block");
                        }else{
                            $(".upd_settlmt_blk").css("display","none");
                        }
        			
        		
                    $('#cus_active').html('<span class="label bg-'+(agent.active == 1 ?'green' :'red')+'">'+(agent.active == 1 ?'ACTIVE' :'INACTIVE')+'</span>');
                    $('#loyalty_cus_type').html('<span class="label bg-purple">AGENT</span>');
                    
                    $('#cus_name').html(agent.cus_name);
                    $('#id_customer').val(agent.id_customer);
                    $('#cus_mobile').html(agent.mobile);
        			if(agent.date_of_birth != '' && agent.date_of_birth != null){
        				$('.profile-list').append('<li class="list-group-item"><b><i class="fa fa-birthday-cake "></i> Date of Birth</b> <a class="pull-right">'+agent.date_of_birth+'</a></li>');
        			}
        			if(agent.date_of_wed != '' && agent.date_of_wed != null){
        				$('.profile-list').append('<li class="list-group-item"><b><i class="fa fa-calendar "></i> Date of Wed</b> <a class="pull-right">'+agent.date_of_wed+'</a></li>');
        			}
                    var address = "";
                    if(agent.address1 != ''){
        				address = agent.address1;
        			}
        			if(agent.address2 != ''){
        				address = address+"<br/>"+agent.address2;
        			}
        			if(agent.address3 != ''){
        				address = address+"<br/>"+agent.address2;
        			}
        			if(agent.city_name != ''){
        				address = address+"<br/>"+agent.city_name;
        			}
        			if(agent.state_name != ''){
        				address = address+"<br/>"+agent.state_name;
        			}
        			if(agent.county_name != ''){
        				address = address+"<br/>"+agent.county_name;
        			}
                    $('#address').html(address);
                    var social_media = "";
                    if(agent.email != ''){
        				social_media = "<p><i class='fa fa-google text-red'></i> "+agent.email+"</p>";
        			}
        			if(agent.facebook != ''){
        				social_media = social_media+"<p> <i class='fa fa-facebook text-light-blue'></i> &nbsp;"+agent.facebook+"</p>";
        			}
        			if(agent.instagram != ''){
        				social_media = social_media+"<p> <i class='fa fa-instagram text-maroon'></i> "+agent.instagram+"</p>";
        			}
        			if(agent.twitter != ''){
        				agent = social_media+"<p> <i class='fa fa-twitter text-aqua'></i> "+agent.twitter+"</p>";
        			}
        			if(agent.youtube != ''){
        				social_media = social_media+"<p> <i class='fa fa-youtube text-red'></i> "+agent.youtube+"</p>";
        			}
        			if(agent.website != ''){
        				social_media = social_media+"<p> <i class='fa fa-globe'></i> "+agent.website+"</p>";
        			}
                    
                    $('#social_media').html(social_media);
                    if(agent.cus_img != null && agent.cus_img!='')
                    {
                    	$("#cus_img").prop("src",agent.cus_img);
                    }else{
                    	$("#cus_img").prop("src",base_url+'assets/img/default.png');
                    }
                    
        			
        			if(data.list.transactions.length > 0){
        			    console.log(data.list.transactions);
        				$.each(data.list.transactions,function(key,item){
        	                transHtml+= '<tr>' + 
        	                                '<td>'+(key+1)+'</td>' +
        	                                '<td>'+item.cr_date+'</td>' +
        	                                '<td>'+item.customer_name+'</td>' +
        	                                '<td>'+item.mobile+'</td>' +
        	                                '<td>'+item.receipt_no+'</td>' +
        	                                '<td>'+item.payment_amount+'</td>' +
        	                                '<td>&#8377; '+item.cash_point+'</td>' +	
        	                                '<td>'+item.status+'</td>' +	                                
        	                            '</tr>';
        	            });
        	            $('#transactions_list > tbody').html(transHtml);
        			} 
        			
        			 
        			if(data.list.referral_list.length > 0){
        				$.each(data.list.referral_list,function(key,item){
        	                referralHtml+= '<tr>' + 
        	                                '<td>'+(key+1)+'</td>' +
        	                                '<td>'+item.cr_date+'</td>' +
        	                                '<td>'+item.customer_name+'</td>' +
        	                                '<td>'+item.mobile+'</td>' +
        	                                '<td>'+item.cash_point+'</td>' +	                                
        	                                '<td>'+item.status+'</td>' +	                                
        	                            '</tr>';
        	            });
        	            $('#referrals_list > tbody').html(referralHtml);
        			}   
        			
        			if(data.list.pend_settlmnt.length > 0){
        				$.each(data.list.pend_settlmnt,function(key,item){
        	                pendingSettlmtHtml+= '<tr>' + 
        	                                '<td><input type="hidden" class="cus_loyal_tran_id" name="cus_loyal_tran_id[]" value="'+item.id_cus_loyal_tran+'"/> '+(key+1)+'<input type="hidden" class="id_cus_loyal_tran" value="'+item.id_cus_loyal_tran+'"/><input type="hidden" class="cash_point" value="'+item.cash_point+'"></td>' +
        	                                '<td>'+item.cr_date+'</td>' +
        	                                '<td>'+item.unsettled_cash_pts+'</td>' +
        	                                '<td>'+item.sch_acc_no+'</td>' +
        	                               	                            
        	                            '</tr>';
        	            });
        	            $('#pending_settlmt_list > tbody').html(pendingSettlmtHtml);
        			}
			
            }
            
        },
        error:function(error)  
        {
            $("div.overlay").css("display", "none"); 
        }
    });
}

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