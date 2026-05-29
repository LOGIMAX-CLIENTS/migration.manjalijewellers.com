var path =  url_params();
var ctrl_page = path.route.split('/');

$(document).ready(function() {
     
	   if(ctrl_page[2] == 'add')
      {
      	$('#select_all').prop('checked', false)
          	get_schemes();
         	get_month_rate(1);
          
        $('#select_all').on('change',function()
        {
        	get_payment_list();
          
        })
      }	
      else if(ctrl_page[2] == 'list')
      {
      	  
	  	get_settlement_list();
	  }
	  else if(ctrl_page[2]=='detail')
	  {
	  	sett_det_list(ctrl_page[4]);
	  }
	 
	 if($("input:radio[name='sch[type]']:checked").val()==3)
	 {
	 	$(".adjust_block").css("display", "none");
	 }
  
            //for displaying monthly rate 
         	
         $("input[name='sch[type]']:radio").on('change', function () {
         	console.log(this.value);
	    	 if(this.value == 1)
	    	 {
	    	     $(".adjust_block").css("display", "block");
	    	     
			 }	 
	    	 else if(this.value == 2)
	    	 {
	    	    $(".adjust_block").css("display", "none");
	    	    alert('Under Construction...');
	    	    $("input[name='sch[type]'][value=3]").prop('checked','checked');
	    	   
			 }	
			 else if(this.value == 3)
	    	 {
	    	 	$(".adjust_block").css("display", "none");
			 }
        });
        
        //for select all
	  	   
		    $('#select_all').click(function(e){
	  		
	  		 if (e.stopPropagation !== undefined) {
		        e.stopPropagation();
		         $('input[name="scheme[]"]').prop('checked', $(this).prop('checked'));
		       
		    } else {
		        e.cancelBubble = true;
		    }
		    		    
		    
	  	});	
		 
});	
//var settlementData=[];	
function get_schemes()
{
	var oTable = $('#scheme_list').DataTable();
	$("div.overlay").css("display", "block");
	$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/scheme/get/fix_schemes',
				  dataType: 'json',
				  success: function(data) {
				  	oTable = $('#scheme_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": false,
				                "aaData": data,
				                "aoColumns": [
							                    { "mDataProp": function (row,type,val,meta){  
							                        return (row.payments>0 ? "<input type='checkbox' class='settlement_data' onchange='get_payment_list()' name='scheme[]' value="+row.id_scheme+" /> ":"")+row.id_scheme;							                       	
							                       } 
							                    },
							                    { "mDataProp": "scheme_name" },
								                { "mDataProp": "code" },
								                { "mDataProp": "accounts" },
								                { "mDataProp": "payments" },
								                { "mDataProp": function (row,type,val,meta){  
							                        return ("<input type='hidden' name='type[]' value='"+row.setlmnt_type+"'/> ")+row.type}},
								                { "mDataProp": function (row,type,val,meta){  
							                        return ("<input type='hidden' name='adjust_by[]' value='"+row.setlmnt_adjust_by+"'/> ")+row.adjust_by}},
								                { "mDataProp": function (row,type,val,meta){  
							                        return (row.adjust_by=='Manual'? "<input type='text' id='setlmnt_rate' class='' onchange='' name='setlmnt_rate[]' placeholder='Enter settlement rate' value='' required='true' /> ": "<input type='text' id='setlmnt_rate' class='' onchange='' name='setlmnt_rate[]' placeholder='Enter settlement rate' value='"+row.rate+"' required='true' disabled='true' /> ");} }
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


function get_payment_list()
{ 
	 
				
	 var id = [];
	$("input[name='scheme[]']:checked").each(function() {
	  id.push($(this).val());
	  console.log(id);
	});
	
	if ($('.settlement_data').is(":checked")){ 
	  	
	  	my_Date = new Date();
	  	var oTable = $('#settlement_list').DataTable();
	 
		$("div.overlay").css("display", "block");
		$.ajax({
				  type: 'POST',
				  url:  base_url+'index.php/settlement/get_scheme/?nocache='+my_Date.getUTCSeconds(),
				  data:{ id_scheme: id},
				  dataType: 'json',
				  success: function(data) {
				  	  console.log(data);
				  	oTable = $('#settlement_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": data,
				                "aoColumns": [
							                    { "mDataProp": "id_payment" },
							                    { "mDataProp": "date_payment" },
							                    { "mDataProp": "name" },
								                { "mDataProp": "scheme_acc_number" },
								                { "mDataProp": "mobile" },
								                { "mDataProp": "payment_amount" }
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
	  	 else
	  	 {
	  	 	var oTable = $('#settlement_list').DataTable();
		 	 oTable.clear().draw();
		 }
		
}

function get_month_rate(by)
{
	 $("div.overlay").css("display", "block"); 
	$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/ajax/monthly_rate/'+by,
				  dataType: 'json',
				  success: function(data) {
				  	
				  	 $("div.overlay").css("display", "none"); 
				  	 $("#rate").val(data.rate);
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	       });
}


 $('#btn_settlement').click(function(){
 	
 	var scheme = [];
 	var flag = true;
	$('#scheme_list tbody').find('tr').each(function (index, element)
	{
		var eachrow = $(this);
		if (eachrow.find('input[name="scheme[]"]').is(':checked'))
		{
			if($('input[name="setlmnt_rate[]"]', element).val()!='')
			{
				scheme.push({
							'id' : $('input[name="scheme[]"]', element).val(),
						 	'type' : $('input[name="type[]"]', element).val(),
						 	'adjust_by' : $('input[name="adjust_by[]"]', element).val(),
						 	'rate' : $('input[name="setlmnt_rate[]"]', element).val()
							});
			}
			else
			{
				flag = false;
			}
						
		}

		});

		if(flag==true)
		{	
			//console.log(scheme);
			$("div.overlay").css("display", "block"); 
			if (scheme.length>0) 
			{
			 	 $.ajax({
							  type: 'POST',
							  url:  base_url+'index.php/settlement/update/account',
							  data: {'scheme':scheme},
							  success: function(data) {
							  	
							  	 $("div.overlay").css("display", "none"); 
							  	  window.location.reload();
							  	
							  },
						  	  error:function(error)  
							  {
							  	
								 $("div.overlay").css("display", "none"); 
							  }	 
				       });
			}
			else
			{
		 		$("div.overlay").css("display", "none"); 
		 	 	msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Please select scheme.</div>';
				$('#chit_alert').html(msg);	
		 	}
		}
		else
		{
			$("div.overlay").css("display", "none"); 
			msg='<div class = "alert alert-warning"><i class="icon fa fa-warning"></i><a href = "#" class = "close" data-dismiss = "alert">&times;</a><strong></strong>Rate field should not be empty.</div>';
			$('#chit_alert').html(msg);	
		}

 	 
 });	 

function get_settlement_list()
{
	  $('body').addClass("sidebar-collapse");
	var oTable = $('#settle_list').DataTable();
	$("div.overlay").css("display", "block");
	$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/settlement/weight/ajax_list',
				  dataType: 'json',
				  success: function(data) {
				  	oTable = $('#settle_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": data.set,
				                "order"	   : [[0,'desc']],
				                "aoColumns": [
							                    { "mDataProp": "id_settlement"},
							                    { "mDataProp": "date_upd" },
							                    { "mDataProp": "employee" },
								                { "mDataProp": "schemes" },
								                { "mDataProp": "acc_count" },
								                { "mDataProp": function(row,type,val,meta){
	                                                return "<span class='label "+(row.success==1? 'bg-green':'bg-red')+"'>"+(row.success==1? 'Success':'Failed')+"</span>";  							                
								                  } 
								                },
								                { "mDataProp": function (row,type,val,meta){ 
								                   return "<a class='btn btn-primary' href='"+base_url+'index.php/settlement/weight/detail/list/'+row.id_settlement+"'><i class='fa fa-search'></i> View</a>"
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

function sett_det_list(id)
{
	  $('body').addClass("sidebar-collapse");
	var oTable = $('#sett_det_list').DataTable();
	$("div.overlay").css("display", "block");
	$.ajax({
				  type: 'GET',
				  url:  base_url+'index.php/settlement/weight/detail/ajax_list/'+id,
				  dataType: 'json',
				  success: function(data) {
				  	oTable = $('#sett_det_list').dataTable({
				                "bDestroy": true,
				                "bInfo": true,
				                "bFilter": true,
				                "bSort": true,
				                "aaData": data.set,
				            "aoColumns": [{ "mDataProp": "id_payment" },
					                { "mDataProp": "name" },
					                { "mDataProp": "account_name" },
					                { "mDataProp": "scheme_acc_number" },
					                { "mDataProp": "mobile" },
					                { "mDataProp": "set_type" },
					                { "mDataProp": "adjust_by" },
					                { "mDataProp": "metal_rate" },
					                { "mDataProp": "metal_weight" },
					                { "mDataProp": "payment_amount" },
					                { "mDataProp": function(row,type,val,meta)
					                	{return "<span class='label bg-"+row.status_color+"-active'>"+row.payment_status+"</span>";	}
					                
					               }] 
					            });
				  	
				  	 $("div.overlay").css("display", "none"); 
				  },
			  	  error:function(error)  
				  {
					 $("div.overlay").css("display", "none"); 
				  }	 
	        });	
}
