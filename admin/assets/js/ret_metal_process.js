var path =  url_params();
var ctrl_page = path.route.split('/');
var oldMetalDetail=[];
var pocketDetails=[];
var PolishpocketDetails=[];
var metal_process=[];
var category_details=[];
var prod_details=[];
var design_details=[];
var sub_design_details=[];
var purity_details=[];
var available_metal_stock=[];
var melting_process_details =[];
var section_details =[];
$(document).ready(function() 
{   
     
    $(document).on('keypress',"input[type='number']",function (event){
         if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&
    	  ((event.which < 48 || event.which > 57) &&
    		(event.which != 0 && event.which != 8))) {
    	  event.preventDefault();
    	}
     });
     
     $(document).on('keydown',"input[type='number']",function (event){
         if(event.which == 40 || event.which == 38 || event.which == 37 || event.which == 39 )
         {
             event.preventDefault();
         }
     });
     
	switch(ctrl_page[1])
	{
	    
	    case 'metal_process':
	         switch(ctrl_page[2])
	         {
	              case 'list':
	                  get_metal_process();
	              break;
	         }
	    break;
	    
		case 'metal_pocket':
              
              switch(ctrl_page[2])
              {
                    case 'add':
                        var date = new Date();
            		    var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1); 
            			var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());
            			var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
        		        $('#rpt_payments1').html(from_date);
                        $('#rpt_payments2').html(to_date);
        			  		$('#rpt_payment_date').daterangepicker(
        		            {
        		              ranges: {
        		                'Today': [moment(), moment()],
        		                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        		                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        		                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        		                'This Month': [moment().startOf('month'), moment().endOf('month')],
        		                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        		              },
        		              startDate: moment().subtract(0, 'days'),
        		              endDate: moment()
        		            },
        		          function (start, end) {
        		          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        		      
        						$('#rpt_payments1').text(start.format('YYYY-MM-DD'));
        						$('#rpt_payments2').text(end.format('YYYY-MM-DD'));		            
        		          }
        			    ); 
                        get_ActiveMetals();
                        
                        get_old_metal_type();
                        get_ActiveProducts();
                        get_available_metal_stock_details();
                        get_karigar_pending_orders();
                        $('#select_product').select2({
                    	    placeholder: "Product",
                    	    allowClear: true
                    	  });
                    	   $('#select_design').select2({
                    	    placeholder: "Design",
                    	    allowClear: true
                    	  });
                    	   $('#select_sub_design').select2({
                    	    placeholder: "Sub Design",
                    	    allowClear: true
                    	  });
                        
                        //get_metal_stock_list();
                    break;
                    
                    case 'list':
                        var date = new Date();
            		    var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1); 
            			var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());
            			var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
        		        $('#rpt_payments1').html(from_date);
                        $('#rpt_payments2').html(to_date);
                        get_pocket_list();
        			  		$('#date_range_picker').daterangepicker(
        		            {
        		              ranges: {
        		                'Today': [moment(), moment()],
        		                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        		                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        		                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        		                'This Month': [moment().startOf('month'), moment().endOf('month')],
        		                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        		              },
        		              startDate: moment().subtract(0, 'days'),
        		              endDate: moment()
        		            },
        		          function (start, end) {
        		          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        						$('#rpt_payments1').text(start.format('YYYY-MM-DD'));
        						$('#rpt_payments2').text(end.format('YYYY-MM-DD'));	
        						get_pocket_list();
        		          }
        			    );
                    break;
              }
	 	break;
	 	
	 	case 'metal_process_issue':
	 	     switch(ctrl_page[2])
	 	     {
	 	          case 'add':
	 	              get_Activesections();
	 	              get_ActiveMetalProcess();
	 	              get_ActiveKarigars();
	 	              get_ActiveCategory();
	 	              get_pocket_details();
	 	              get_polish_pocket_details();
	 	              get_ActiveProducts();
	 	              get_ActiveDesigns();
	 	              get_ActiveSubDesign();
                      get_Active_Refining_Process();
                      get_melting_issue_details();
	 	              get_ActiveCategoryPurity();
	 	              get_category(); 
	 	          break;
	 	          case 'list':
	                  get_old_metal_process();
	              break;
	 	     }
	 	break;
	 	
	 	case 'metal_process_receipt':
	 	     switch(ctrl_page[2])
	 	     {
	 	          case 'add':
	 	               
	 	              get_ActiveKarigars();
	 	              get_pocket_details();
	 	              
	 	          break;
	 	     }
	 	break;
	 	
	 	case 'process_report':
	 	    get_ActiveMetalProcess();
	 	    get_ActiveKarigars();
	 	    
            var date = new Date();
            var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 30, 1); 
            var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());
            var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
            $('#rpt_payments1').html(from_date);
            $('#rpt_payments2').html(to_date);
            if(ctrl_page[2]=='list')
            {
                get_category();
                get_old_metal_type();
                //get_metal_process_report();
            }else
            {
                 get_metal_process_detailed_report();
            }
            
            $('#rpt_payment_date').daterangepicker(
            {
            ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(0, 'days'),
            endDate: moment()
            },
            function (start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#rpt_payments1').text(start.format('YYYY-MM-DD'));
            $('#rpt_payments2').text(end.format('YYYY-MM-DD'));	
            get_pocket_list();
            }
            );
        			    
	 	    
	 	break;
	 	
	 	case 'old_metal_pnl_report':
	 	    var pnl_from = moment().subtract(365, 'days').format('YYYY-MM-DD');
	 	    var pnl_to = moment().format('YYYY-MM-DD');
	 	    $('#rpt_payments1').html(pnl_from);
	 	    $('#rpt_payments2').html(pnl_to);

	 	    $('#rpt_payment_date').daterangepicker(
	 	    {
	 	        ranges: {
	 	            'Today': [moment(), moment()],
	 	            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	 	            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	 	            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	 	            'This Month': [moment().startOf('month'), moment().endOf('month')],
	 	            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
	 	            'This Year': [moment().startOf('year'), moment()],
	 	            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
	 	        },
	 	        startDate: moment().subtract(365, 'days'),
	 	        endDate: moment(),
	 	        locale: {
	 	            format: 'DD/MM/YYYY'
	 	        }
	 	    },
	 	    function (start, end) {
	 	        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	 	        $('#rpt_payments1').text(start.format('YYYY-MM-DD'));
	 	        $('#rpt_payments2').text(end.format('YYYY-MM-DD'));
	 	    }
	 	    );

	 	    get_old_metal_pnl_report();
	 	break;
	}
	
	
	    if($('#process_type:checked').val()==1)
        {
            $('.pocketing_details').css("display","block");
            $('.refining_details').css("display","none");
            $('.testing_details').css("display","none");
        }
        else if($('#process_type:checked').val()==2)
        {
            $('.pocketing_details').css("display","none");
            $('.refining_details').css("display","none");
            $('.testing_details').css("display","block");
        }
        else if($('#process_type:checked').val()==3)
        {
            $('.pocketing_details').css("display","none");
            $('.testing_details').css("display","none");
            $('.refining_details').css("display","block");
        }

});

$('#metal_search').on('click',function(){

    if($('#metal').val()=='' || $('#metal').val()==null)
    {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Metal'});
    }else{
         var trans_type =  $("input[name='transfer_item_type']:checked").val();
         var is_against_opening =  $("input[name='is_against_opening']:checked").val();
         if(trans_type==1)
         {
            if(is_against_opening==0)
            {
                get_metal_stock_list(); // From Branch
            }else{
                get_opening_metal_stock_list(); // From Company stock opening
            }
         }else if(trans_type==2){
            if($('#tag_no').val()=='' && $('#old_tag_no').val()==''){
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter tag No..'});
            }else{

                get_tag_search_list();
            }
         }
         else if(trans_type==3){
             if($('#select_product').val()=='' || $('#select_product').val()==null){
                 $.toaster({ priority : 'danger', title : 'Warning!', message : '' + "</br>" + 'Please Select Product' });
                 return;
             }
             if($('#select_design').val()=='' || $('#select_design').val()==null){
                 $.toaster({ priority : 'danger', title : 'Warning!', message : '' + "</br>" + 'Please Select Design' });
                 return;
             }
             if($('#select_sub_design').val()=='' || $('#select_sub_design').val()==null){
                 $.toaster({ priority : 'danger', title : 'Warning!', message : '' + "</br>" + 'Please Select Sub Design' });
                 return;
             }
             var nt_pcs = parseFloat($('#issue_pcs').val());
             var nt_wt = parseFloat($('#issue_weight').val());
             if(isNaN(nt_pcs) || nt_pcs <= 0){
                 $.toaster({ priority : 'danger', title : 'Warning!', message : '' + "</br>" + 'Please Enter Valid Piece' });
                 return;
             }
             if(isNaN(nt_wt) || nt_wt <= 0){
                 $.toaster({ priority : 'danger', title : 'Warning!', message : '' + "</br>" + 'Please Enter Valid Weight' });
                 return;
             }
             var html = '';
             html= 
            '<tr>'+
            '<td><input type="hidden" name="id_product[]" class="id_product" value='+$('#select_product').val()+'>'+$('#select_product option:selected').html()+'</td>'+
            '<td><input type="hidden" name="id_design[]" class="id_design" value='+$('#select_design').val()+'>'+$('#select_design option:selected').html()+'</td>'+
            '<td><input type="hidden" name="id_sub_design[]" class="id_sub_design" value='+$('#select_sub_design').val()+'>'+$('#select_sub_design option:selected').html()+'</td>'+
            '<td><input type="hidden" name="piece[]" class="piece" value='+$('#issue_pcs').val()+'>'+$('#issue_pcs').val()+'</td>'+
            '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value='+$('#issue_weight').val()+'>'+$('#issue_weight').val()+'</td>'+
            '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
            '</tr>';
             if($('#non_tag_list  > tbody > tr').length > 0 )
            {
                $('#non_tag_list > tbody > tr:first').before(html);
            }
            else
            {
                $('#non_tag_list > tbody').append(html);
            }
            calculate_non_tag_list();
            $('#select_product').select2("val","");
            $('#select_design').select2("val","");
            $('#select_sub_design').select2("val","");
            $('#issue_pcs').val('');
            $('#issue_weight').val('');
         }
         
    }
});

function get_old_metal_type()
{
    $('#old_metal_type option').remove();
    $.ajax({
        type: 'GET',
        url: base_url+'index.php/admin_ret_reports/get_old_metal_type',
        dataType:'json',
        success:function(data){
        var id_karigar =  $("#karigar").val();
        $("#old_metal_type").select2(
        {
            placeholder:"Select Category",
            allowClear: true		    
        });
        $.each(data,function (key, item) {
            $('#old_metal_type').append(
            $("<option></option>")
            .attr("value", item.id_metal_type)
            .text(item.metal_type)
            );
        }); 
        if($("#old_metal_type").length > 0)
        {
            $("#old_metal_type").select2("val",'');	
        }
             
        }
    });
}


function get_ActiveProduct()
{
	$.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_reports/get_ActiveProduct',
	dataType:'json',
	success:function(data){
            prod_details=data;
		}
	});
}



function get_ActiveDesigns()
{
    $(".overlay").css('display','block');
    $.ajax({
        type: 'GET',
        url: base_url+'index.php/admin_ret_metal_process/get_active_design_products',
        dataType:'json',
        success:function(data){
           design_details = data;
        }
    });
}

function get_ActiveSubDesign()
{
    $(".overlay").css('display','block');
    $.ajax({
        type: 'GET',
        url: base_url+'index.php/admin_ret_metal_process/get_active_sub_design_products',
        dataType:'json',
        success:function(data){
           sub_design_details = data;
        }
    });
}




function get_ActiveSubDesigns(curRow,id_design)
{
    
    $(".overlay").css('display','block');
    $.ajax({
        type: 'POST',
        url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',
        dataType:'json',
        data:{'id_product':curRow.find('.id_product').val(),'design_no':curRow.find('.id_design').val()},
        success:function(data){
            sub_design_details = data;
            curRow.find(".id_sub_design option").remove();
           
           $.each(data, function (key, item) {   
                curRow.find(".id_sub_design").append(
                    $("<option></option>")
                    .attr("value", item.id_sub_design)    
                    .text(item.sub_design_name)  
                );
            });         
            $(".id_sub_design").select2({
                placeholder: "Sub Design",
                allowClear: true
            });
            curRow.find(".id_sub_design").select2("val","");
            $(".overlay").css("display", "none");   
           
        }
    });
}


function get_ActiveCategoryPurity()
{
	$.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_metal_process/get_ActiveCategoryPurity',
	dataType:'json',
	success:function(data){
            purity_details=data;
		}
	});
}

//Process Master
function get_metal_process()
{
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/metal_process/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		cache:false,
		success:function(data){
		    var list=data.list;
		    var access=data.access;
			var oTable = $('#process_list').DataTable();
			oTable.clear().draw();
			
			if (list!= null && list.length > 0)
			{  	
				oTable = $('#process_list').dataTable({
					"bDestroy": true,
					"bDestroy": true,
					"bInfo": true,
					"bFilter": true,
					"bSort": false,
					"order": [[ 0, "desc" ]],
					"aaData"  : list,
					"aoColumns": [	
									{ "mDataProp": "id_metal_process" },
									{ "mDataProp": "process_name" },
									{ "mDataProp": function ( row, type, val, meta ){
                                		active_url =base_url+"index.php/admin_customer/customer_status/"+(row.status==1?0:1)+"/"+row.id_metal_process; 
                                		return "<a href='"+active_url+"'><i class='fa "+(row.status==1?'fa-check':'fa-remove')+"' style='color:"+(row.status==1?'green':'red')+"'></i></a>"
                                		}
                            		},
									{ "mDataProp": function ( row, type, val, meta ) {
                            		id= row.id_metal_process;
                            		edit_url=(access.edit=='1' ? base_url+'index.php/admin_ret_metal_process/metal_process/edit/'+id : '#' );
                            		action_content='<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button><ul class="dropdown-menu">'+
                            		(access.edit=='1'?'<li><a href="'+edit_url+'" class="btn-edit"><i class="fa fa-edit" ></i> Edit</a></li>':'')+'</ul></div>';
                            		return action_content;
                            		}
                            		}
								  ],
				});

			}
		}
	});
}
//Process Master


//Pocketing Entry

function get_pocket_list()
{
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/metal_pocket/ajax?nocache=" + my_Date.getUTCSeconds(),
		data:{'from_date':$('#rpt_payments1').html(),'to_date' :$('#rpt_payments2').html()},
		type:"POST",
		dataType:"JSON",
		cache:false,
		success:function(data){
		    var list=data.list;
			var oTable = $('#pocket_list').DataTable();
			oTable.clear().draw();
			
			if (list!= null && list.length > 0)
			{  	
				oTable = $('#pocket_list').dataTable({
					"bDestroy": true,
					"bDestroy": true,
					"bInfo": true,
					"bFilter": true,
					"bSort": false,
					"order": [[ 0, "desc" ]],
					"aaData"  : list,
					"aoColumns": [	
									{ "mDataProp": "pocket_no" },
									{ "mDataProp": "date" },
									{ "mDataProp": "piece" },
									{ "mDataProp": "gross_wt" },
									{ "mDataProp": "net_wt" },
									{ "mDataProp": "avg_purity" },
								  ],
				});

			}
		}
	});
}

$('#old_metal_select_all').click(function(event) {
	$("#metal_list tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
	event.stopPropagation();
	if($(this).is(":checked"))
	{
			$.each(oldMetalDetail,function(key,items){
			  oldMetalDetail[key].is_checked=1;
			});
	}else{
		$.each(oldMetalDetail,function(key,items){
			  oldMetalDetail[key].is_checked=0;
			});
	}
		calculate_old_metal();
});




function calculate_old_metal()
{
    var is_against_opening =  $("input[name='is_against_opening']:checked").val();
    if(is_against_opening==0)
    {

    
  var total_pcs=0;
  var total_gwt=0;
  var total_nwt=0;
  var total_diawt=0;
  var total_amt=0;
  var total_dust=0;
  var total_stone=0;
  var total_purity=0;
  var total_wastage=0;
  var total_final_wt=0;
  var total_length=0;
  var total_item_purity=0;
  $.each(oldMetalDetail,function(key,items){
	  if(items.is_checked==1)
	  {
		  total_pcs+=parseFloat(items.piece);
		  total_gwt+=parseFloat(items.gross_wt);
		  total_nwt+=parseFloat(items.net_wt);
          total_diawt+=parseFloat(items.diawt);
		  total_dust+=parseFloat(items.dust_wt);
		  total_stone+=parseFloat(items.stone_wt);
		  total_final_wt+=parseFloat(items.pure_wt);
		  total_purity+=parseFloat(items.touch*items.piece);
		  total_wastage+=parseFloat(items.wast_wt);
		  total_amt+=parseFloat(items.item_cost);
		  total_item_purity+=parseFloat(items.touch*items.piece);
		  total_length++;
	  }
  });
  
  $(".total_pcs").val(parseFloat(total_pcs).toFixed(0));
  $(".total_gross_wt").val(parseFloat(total_gwt).toFixed(3));
  $(".total_stone_wt").val(parseFloat(total_stone).toFixed(3));
  $(".total_dust_wt").val(parseFloat(total_dust).toFixed(3));
  $(".total_final_wt").val(parseFloat(total_final_wt).toFixed(3));
  $(".total_wastage_wt").val(parseFloat(total_wastage).toFixed(3));
  $(".total_net_wt").val(parseFloat(total_nwt).toFixed(3));
  $(".total_dia_wt").val(parseFloat(total_diawt).toFixed(3));
  $(".total_amount").val(parseFloat(total_amt).toFixed(3));
  $(".total_item_purity").val(parseFloat(total_item_purity).toFixed(3));
  if(total_length>0)
  {
	$(".avg_purity_per").val(isNaN(parseFloat(total_purity/total_pcs).toFixed(2))? 0 : parseFloat(total_purity/total_pcs).toFixed(2));
  }else{
	$(".avg_purity_per").val(0);
  }
}
  
}

function get_metal_stock_list()
{
    $("div.overlay").css("display", "block"); 
	oldMetalDetail=[];
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/metal_pocket/metal_list?nocache=" + my_Date.getUTCSeconds(),
		data:{'from_date':$('#rpt_payments1').html(),'to_date' :$('#rpt_payments2').html(),'id_branch':$('#branch_select').val(),'id_metal':$('#metal').val(),'from_branch':$('.branch_filter').val(),'tag_code':$('#tag_no').val(),'old_tag_id':$('#old_tag_no').val(),'trasnfer_item_type':$("input[name='transfer_item_type']:checked").val()},
		type:"POST",
		dataType:"JSON",
		cache:false,
		success:function(data){
		    var list=data;
			var oTable = $('#metal_list').DataTable();
			oTable.clear().draw();
			
			if (list!= null && list.length > 0)
			{  	

				$.each(list,function(key,val){
					$.each(val.bill_det,function(k,items){
						oldMetalDetail.push(items);
					});
				});

				oTable = $('#metal_list').dataTable({
					"bDestroy": true,
					"bDestroy": true,
					"bInfo": true,
					"bFilter": true,
					"bSort": false,
					"order": [[ 0, "desc" ]],
					"aaData"  : list,
					"aoColumns": [	
									{ "mDataProp": function ( row, type, val, meta )
									{ 
										return '<input type="checkbox" class="'+row.type+'" name="type[]" value="'+row.type+'"/>'+row.metal_type;
									}},
									{ "mDataProp": "piece" },
									{ "mDataProp": "gross_wt" },
									{ "mDataProp": "net_wt" },
                                    { "mDataProp": "dia_wt" },
									{ "mDataProp": "purity_per" },
									{ "mDataProp": "rate" },
									{
										"mDataProp": null,
										"sClass": "control center", 
										"sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
									},
								  ],
				});

				var anOpen =[]; 
				$(document).on('click',"#metal_list .control", function(){ 
					var nTr = this.parentNode;
					var i = $.inArray( nTr, anOpen );
					
					if ( i === -1 ) { 
						$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>'); 
						oTable.fnOpen( nTr, FormatRowBillDetails(oTable, nTr), 'details' );
						anOpen.push( nTr ); 
					}
					else { 
						$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
						oTable.fnClose( nTr );
						anOpen.splice( i, 1 );
					}
				} );

				calculate_old_metal();
			}
			$("div.overlay").css("display", "none"); 
		}
	});
}

function FormatRowBillDetails( oTable, nTr )
{
  var oData = oTable.fnGetData( nTr );
  var rowDetail = '';
  var prodTable = 
     '<div class="innerDetails">'+
      '<table class="table table-responsive table-bordered text-center table-sm">'+ 
        '<tr class="bg-teal">'+
        '<th>S.No</th>'+ 
        '<th>Bill Date</th>'+
        '<th>Bill No</th>'+
        '<th>Category</th>'+
        '<th>Pcs</th>'+
        '<th>G.Wgt</th>'+
		'<th>Net Wgt</th>'+
        '<th>Dia Wgt</th>'+
		'<th>Purity %</th>'+
        '<th>Rate per Gram</th>'+
        '<th>Amount</th>'+
        '</tr>';
    var bill_det = oData.bill_det; 
	var total_gwt=0;
    var total_nwt=0;
    var total_dia_wt=0;
    var total_amt=0;
    var total_pcs=0;
  $.each(bill_det, function (idx, val) {
	var is_checked=0;
	$.each(oldMetalDetail,function(key,items){
		if(val.type==items.type)
		{
			is_checked=items.is_checked;
			total_pcs+=parseFloat(items.piece);
			total_gwt+=parseFloat(items.gross_wt);
			total_nwt+=parseFloat(items.net_wt);
            total_dia_wt+=parseFloat(items.diawt)
			total_amt+=parseFloat(items.item_cost);
		}
	});
  	prodTable += 
        '<tr class="prod_det_btn">'+
        '<td><input type="checkbox" class="'+val.type+'" name="old_metal_sale_id[]" value="'+val.trans_id+'" '+(is_checked==1 ? 'checked' :'')+' >'+val.trans_id+'</td>'+
        '<td>'+val.bill_date+'</td>'+
        '<td>'+val.bill_no+'</td>'+
        '<td>'+val.old_metal_cat+'</td>'+
        '<td>'+val.piece+'</td>'+
        '<td>'+val.gross_wt+'</td>'+
        '<td>'+val.net_wt+'</td>'+
        '<td>'+val.diawt+'</td>'+
		'<td>'+val.touch+'</td>'+
        '<td>'+val.rate_per_gram+'</td>'+
		'<td>'+val.item_cost+'</td>'+
        '</tr>'; 
  }); 
  rowDetail = prodTable+'</table></div>';
  return rowDetail;
}


$(document).on('change',".old_metal", function(){
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='old_metal_items')
                {
                    console.log(oldMetalDetail);
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        console.log(oldMetalDetail);
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='old_metal_items')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    
    calculate_old_metal();
});


$(document).on('change',".old_metal_items", function(){
    var trans_id=this.value;
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(trans_id==items.old_metal_sale_id)
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(trans_id==items.old_metal_sale_id)
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
});


$(document).on('change',".sales_return", function(){
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='sales_return_items')
                {
                    console.log(oldMetalDetail);
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        console.log(oldMetalDetail);
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='sales_return_items')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    
    calculate_old_metal();
});


$(document).on('change',".sales_return_items", function(){
    var trans_id=this.value;
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(trans_id==items.trans_id && (items.pocket_type==2))
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(trans_id==items.trans_id && (items.pocket_type==2))
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
});



$(document).on('change',".partly_sales",function()
{
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
            if(items.type=='partly_sales_items')
            {
                oldMetalDetail[key].is_checked=1;
                console.log('partlyItems',items);
            }
                
        });
    }
    else
    {
        console.log(oldMetalDetail);
        $.each(oldMetalDetail,function(key,items){
            if(items.type=='partly_sales_items')
            {
                oldMetalDetail[key].is_checked=0;
            }
        });
    }

    console.log('oldMetalDetail',oldMetalDetail);
    
    calculate_old_metal();
})


$(document).on('change',".partly_sales_items", function(){
    var trans_id=this.value;
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(trans_id==items.trans_id && (items.pocket_type==3))
                {
                    oldMetalDetail[key].is_checked=1;
                    console.log(items);
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(trans_id==items.trans_id && (items.pocket_type==3))
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
});


$('#pocket_save').on('click',function(){
            var trans_type =  $("input[name='transfer_item_type']:checked").val();
            $('#pocket_save').prop('disabled',true);
            var selected = [];
            if(trans_type==1)
            {
                var is_against_opening =  $("input[name='is_against_opening']:checked").val();
                if(is_against_opening==0)
                { 
                    $.each(oldMetalDetail,function(key,items){
                        if(items.is_checked==1)
                        {
                            selected.push(items);
                        }
                    });
                }
                else{

                    $("#metal_list tbody tr").each(function(index, value){

                         if($(value).find(".op_stk_metal_type").is(":checked"))

                         {

                             transData = { 

            					'id_old_metal_type'         : $(value).find(".op_stk_metal_type").val(),

            					'piece'   	                : $(value).find(".piece").val(),

            					'gross_wt'                  : $(value).find(".op_blc_gross_wt").val(),

            					'net_wt'                   : $(value).find(".op_blc_net_wt").val(),

            					'purity_per'                : $(value).find(".purity_per").val(),

            					'item_cost'                 : $(value).find(".amount").val(),

            				}

            				selected.push(transData);

                         }

                     });

                }
               
            }else if(trans_type==2){
                 if($("input[name='tag_id[]']:checked").val())
                 {
                     $("#tag_list tbody tr").each(function(index, value){
                         if($(value).find(".tag_id").is(":checked"))
                         {
                             transData = { 
            					'tag_id'        : $(value).find(".tag_id").val(),
            					'piece'   	    : $(value).find(".piece").val(),
            					'gross_wt'      : $(value).find(".gross_wt").val(),
            					'net_wgt'       : $(value).find(".net_wgt").val(),
                                'dia_wgt'       : $(value).find(".dia_wgt").val(),
            					'purity_per'    : $(value).find(".purity_value").val(),
            					'item_cost'     : $(value).find(".tag_purchase_cost").val(),
            				}
            				selected.push(transData);
                         }
                     });
                 }
            }
            else if(trans_type==3){
                     $("#non_tag_list tbody tr").each(function(index, value){
                             transData = { 
            					'id_product'    : $(value).find(".id_product").val(),
            					'id_design'    : $(value).find(".id_design").val(),
            					'id_sub_design'    : $(value).find(".id_sub_design").val(),
            					'piece'   	    : $(value).find(".piece").val(),
            					'gross_wt'      : $(value).find(".gross_wt").val(),
            				}
            				selected.push(transData);
                     });
            }
			
            req_data = selected;
            console.log(req_data);
            if(selected.length>0)
            {
               var total_pcs_val = parseFloat(trans_type == 1 ? $(".total_pcs").val() : (trans_type == 2 ? $(".tag_total_pcs").val() : $(".non_tag_total_pcs").val()));
               var total_gwt_val = parseFloat(trans_type == 1 ? $(".total_gross_wt").val() : (trans_type == 2 ? $(".tag_total_gross_wt").val() : $(".non_tag_total_gross_wt").val()));

               if (isNaN(total_pcs_val) || total_pcs_val <= 0) {
                   $.toaster({ priority : 'danger', title : 'Warning!', message : '' + "</br>" + 'Total Pcs must be greater than 0.' });
                   $('#pocket_save').prop('disabled', false);
                   return;
               }
               if (isNaN(total_gwt_val) || total_gwt_val <= 0) {
                   $.toaster({ priority : 'danger', title : 'Warning!', message : '' + "</br>" + 'Total Gross Weight must be greater than 0.' });
                   $('#pocket_save').prop('disabled', false);
                   return;
               }

               pocket_save(req_data);
            }else
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Items..'});
            }
      
});

function pocket_save(req_data)
{
    var trans_type =  $("input[name='transfer_item_type']:checked").val();
	$(".overlay").css("display", "block");	 
	my_Date = new Date();
	$.ajax({		
	 	type: 'POST',		
		url: base_url+"index.php/admin_ret_metal_process/metal_pocket/save?nocache=" + my_Date.getUTCSeconds(),
	 	dataType : 'json',		
	 	data : {'is_against_opening':$("input[name='is_against_opening']:checked").val(),'id_branch':$('#branch_select').val(),'req_data':req_data,'trans_type':trans_type,'total_gross_wt':(trans_type==1 ? $(".total_gross_wt").val():(trans_type==2 ? $(".tag_total_gross_wt").val():$(".non_tag_total_gross_wt").val())),'total_pcs':(trans_type==1 ? $(".total_pcs").val():(trans_type==2 ? $(".tag_total_pcs").val(): $(".non_tag_total_pcs").val())),'total_stone_wt':$(".total_stone_wt").val(),'total_dust_wt':$(".total_dust_wt").val(),'total_final_wt':$(".total_final_wt").val(),'total_wastage_wt':$(".total_wastage_wt").val(),'total_net_wt':(trans_type==1 ? $(".total_net_wt").val():(trans_type==2 ? $(".tag_total_net_wt").val():$(".non_tag_total_net_wt").val())),'total_dia_wt':(trans_type==1 ? $('.total_dia_wt').val():(trans_type==2 ? $('.tag_total_dia_wt').val() : 0)),'total_item_purity':$(".total_item_purity").val(),'avg_purity_per':(trans_type==1 ? $(".avg_purity_per").val() : (trans_type==2 ? $(".tag_total_avg_purity").val() : 0) ),'total_amount':(trans_type==1 ? $(".total_amount").val():(trans_type==2 ?  $(".tag_total_value").val():0))},
	 	success:function(data)
		 {
			 if(data.status)
			 {
				$.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
				window.location.reload();
			 }else{
				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
			 }
			 
			 //get_metal_stock_list();
			 //calculate_old_metal();
	     	$(".overlay").css("display", "none");
	     	$('#pocket_save').prop('disabled',false);
	 	 }	
	});
}

//Pocketing Entry




//Metal Process

$("input[name='process[process_type]']:radio").on('change',function(){
     $('#pocket_details tbody').empty();
     $('#melting_receipt tbody').empty();
    if(this.value==1)
    {
        $('.pocketing_details').css("display","block");
        $('.refining_details').css("display","none");
        $('.testing_details').css("display","none");
    }
    else if(this.value==2)
    {
        $('.pocketing_details').css("display","none");
        $('.refining_details').css("display","none");
        $('.testing_details').css("display","block");
    }
    else if(this.value==3)
    {
        $('.pocketing_details').css("display","none");
        $('.refining_details').css("display","block");
        $('.testing_details').css("display","none");
    }
});

function get_ActiveKarigars()
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_catalog/karigar/active_list',
		dataType:'json',
		success:function(data){
			var id =  $("#karigar").val();
		
		   $.each(data,function (key, item) {
			   		$('#karigar').append(
						$("<option></option>")
						  .attr("value", item.id_karigar)
						  .text(item.karigar)
					);
			}); 
			
			$("#karigar").select2(
			{
				placeholder:"Select Vendor",
				allowClear: true		    
			});  
			
			  $('#karigar').select2("val",(id!='' && id>0?id:''));
		}
	});

}

function get_ActiveCategory()
{
    $.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_catalog/category/active_category',
	dataType:'json',
	success:function(data){
            category_details=data;
            console.log(data);
		}
	});
}


function get_ActiveMetalProcess()
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_metal_process/get_ActiveMetalProcess',
		dataType:'json',
		success:function(data){
		    metal_process=data;
			var id =  $("#select_process").val();
		
		   $.each(data,function (key, item) {
			   		$('#select_process').append(
						$("<option></option>")
						  .attr("value", item.id_metal_process)
						  .text(item.process_name)
					);
			}); 
			$("#select_process").select2(
			{
				placeholder:"Select Process",
				allowClear: true		    
			});  
			
			  $('#select_process').select2("val",(id!='' && id>0?id:''));
		}
	});

}
function get_melting_issue_details()
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_metal_process/get_melting_issue_details',
		dataType:'json',
		success:function(data){
            melting_process_details = data;
			var id =  $("#select_metal_process").val();
		   $.each(data,function (key, item) {
			   		$('#select_metal_process').append(
						$("<option></option>")
						  .attr("value", item.id_old_metal_process)
						  .text(item.process_no)
					);
			}); 
			$("#select_metal_process").select2(
			{
				placeholder:"Select Process",
				allowClear: true		    
			});  
			
			  $('#select_metal_process').select2("val",(id!='' && id>0?id:''));
		}
	});

}

$('#select_metal_process').on('change',function(){

    var id_old_metal_process=this.value;
	
    if(this.value!='')
    {
        var rowExist = false;
        if($('#against_melting_receipt tbody tr').length>0)
        {
            	$('#against_melting_receipt > tbody tr').each(function(bidx, brow){ 
            	  
                    curRow = $(this);

            	    if(curRow.find('.id_old_metal_process').val()==id_old_metal_process)
            	    {
            	        rowExist = true;
            	    }
            	});
        }
       
    	if(rowExist)
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Aganst Melting Already Added..'});
    	     $("#select_polish_pocket").select2("val",'');	 
    	}
    	else
    	{
    	    get_against_melting_Details(this.value);
            $("#select_polish_pocket").select2("val",'');	 
    	}
    }
});


$(document).on('input',".purity", function(){
   var row = $(this).closest('tr');
   var purity = row.find('.purity').val();
   if(parseFloat(purity)>100){
       $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Purity'});
       row.find('.purity').val('');
   }
});


function get_against_melting_Details(id_old_metal_process)
{
    var trHtml='';
    var testing_details='';
    $('#against_melting_receipt tbody').empty();
    $('#testing_issue_details tbody').empty();
    if(id_old_metal_process!='')
    {
        $.each(melting_process_details,function(k,items){
		
                    if(items.id_old_metal_process==id_old_metal_process)
                    {
                        trHtml+='<tr>'
                                    +'<td><input type="checkbox" name="testing_receipt_melting[]" class="testing_receipt_melting"><input type="hidden" name="testing_receipt[id_melting_recd][]" class="id_melting_recd" value="'+items.id_melting_recd+'"><input type="hidden" class="is_melting_select" name="testing_receipt[is_melting_select][]" value="0"><input type="hidden" class="id_old_metal_process" name="testing_receipt[id_old_metal_process][]" value="'+items.id_old_metal_process+'">'+items.process_no+'</td>'
                                    +'<td>'+items.karigar_name+'</td>'
                                    +'<td>'+items.category_name+'<input type="hidden" name="testing_receipt[received_category][]" class="received_category" value="'+items.received_category+'"><input type="hidden" name="testing_receipt[id_section][]" class="id_section" value="'+items.id_section+'"><input type="hidden" name="testing_receipt[id_product][]" class="id_product" value="'+items.id_product+'"><input type="hidden" name="testing_receipt[id_sub_design][]" class="id_sub_design" value="'+items.id_sub_design+'"><input type="hidden" name="testing_receipt[id_design][]" class="id_design" value="'+items.id_design+'"></td>'
                                    +'<td>'+items.received_wt+'</td>'
                                    +'<td><input type="hidden" name="testing_receipt[recd_gwt][]" class="actual_weight" value="'+items.actual_weight+'" readonly><input type="hidden" name="testing_receipt[recd_pcs][]" class="recd_pcs" value="'+items.recd_pcs+'">'+items.actual_weight+'</td>'
                                    +'<<td style="width: 100px;"><input type="number"  name="testing_receipt[purity][]" class="form-control purity" value="" max="100"></td>'
                                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                                +'</tr>';
                           $.each(items.testing_details,function(key,val){
                               testing_details+='<tr>'
                                    +'<td>'+val.process_no+'</td>'
                                    +'<td>'+val.karigar_name+'</td>'
                                    +'<td>'+val.category_name+'</td>'
                                    +'<td>'+val.issue_wt+'</td>'
                                    +'<td>'+val.received_wt+'</td>'
                                    +'<td>'+val.production_loss+'</td>'
                                    +'<td>'+val.received_purity+'</td>'
                                    
                                +'</tr>';
                           });   
                            
                    }

        });
        
        $('#against_melting_receipt tbody').append(trHtml);
        $('#testing_issue_details tbody').append(testing_details);
    }
}


function get_Active_Refining_Process()
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_metal_process/get_Active_Refining',
		dataType:'json',
		success:function(data){
            refining_process_details = data;
			var id =  $("#select_refining_process").val();
		   $.each(data,function (key, item) {
			   		$('#select_refining_process').append(
						$("<option></option>")
						  .attr("value", item.id_old_metal_process)
						  .text(item.process_no)
					);
			}); 
			$("#select_refining_process").select2(
			{
				placeholder:"Select Process",
				allowClear: true		    
			});  
			
			  $('#select_refining_process').select2("val",(id!='' && id>0?id:''));
		}
	});

}


$('#select_metal_process').on('change',function(){

    var id_old_metal_process=this.value;
	
    if(this.value!='')
    {
        var rowExist = false;
        if($('#against_melting_receipt tbody tr').length>0)
        {
            	$('#against_melting_receipt > tbody tr').each(function(bidx, brow){ 
            	  
                    curRow = $(this);

            	    if(curRow.find('.id_old_metal_process').val()==id_old_metal_process)
            	    {
            	        rowExist = true;
            	    }
            	});
        }
       
    	if(rowExist)
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Aganst Melting Already Added..'});
    	     $("#select_polish_pocket").select2("val",'');	 
    	}
    	else
    	{
    	    get_against_melting_Details(this.value);
            $("#select_polish_pocket").select2("val",'');	 
    	}
    }
});





function get_pocket_details()
{
    pocketDetails=[];
    $('#select_pocket option').remove();
    $.ajax({
        type: 'POST',
        url: base_url+'index.php/admin_ret_metal_process/get_pocket_details',
        dataType:'json',
        data:{'trans_type':$('#melting_trans_type').val()},
        success:function(data){
        pocketDetails=data;
        var id_karigar =  $("#select_pocket").val();
        $("#select_pocket").select2(
        {
            placeholder:"Select Pocket",
            allowClear: true		    
        });
        $.each(data,function (key, item) {
            $('#select_pocket').append(
            $("<option></option>")
            .attr("value", item.id_metal_pocket)
            .text(item.pocket_no)
            );
        }); 
            
            if($("#select_pocket").length)
            {
                $("#select_pocket").select2("val",'');
            }
        }
    });
}

function get_polish_pocket_details()
{
    PolishpocketDetails=[];
    
    $('#select_polish_pocket option').remove();
    $.ajax({
        type: 'GET',
        url: base_url+'index.php/admin_ret_metal_process/get_polish_pocket_details',
        dataType:'json',
        success:function(data){
        PolishpocketDetails=data;
        var id_karigar =  $("#select_polish_pocket").val();
        $("#select_polish_pocket").select2(
        {
            placeholder:"Select Pocket",
            allowClear: true		    
        });
        $.each(data,function (key, item) {
            $('#select_polish_pocket').append(
            $("<option></option>")
            .attr("value", item.id_metal_pocket)
            .text(item.pocket_no)
            );
        }); 
        
            if($("#select_polish_pocket").length)
            {
                $("#select_polish_pocket").select2("val",'');
            }
            	 
        }
    });
}

$('#select_pocket').on('change',function(){
    var id_metal_pocket=this.value;
    if(this.value!='')
    {
        var rowExist = false;
        if($('#melting_trans_type').val()==1)
        {
            if($('#pocket_details tbody tr').length>0)
            {
                	$('#pocket_details > tbody tr').each(function(bidx, brow){ 

                	    curRow = $(this);

                	    if(curRow.find('.id_metal_pocket').val()==id_metal_pocket)
                	    {
                	        rowExist = true;
                            
                	    }
                	});
            }
        }else if($('#melting_trans_type').val()==2)
        {
            if($('#tagged_pocket_details tbody tr').length>0)
            {
                	$('#tagged_pocket_details > tbody tr').each(function(bidx, brow){ 
                	    curRow = $(this);
                	    if(curRow.find('.id_metal_pocket').val()==id_metal_pocket)
                	    {
                	        rowExist = true;
                	    }
                	});
            }
        }
        else if($('#melting_trans_type').val()==3)
        {
            if($('#non_tagged_pocket_details tbody tr').length>0)
            {
                	$('#non_tagged_pocket_details > tbody tr').each(function(bidx, brow){ 

                	    curRow = $(this);

                	    if(curRow.find('.id_metal_pocket').val()==id_metal_pocket)
                	    {
                	        rowExist = true;
                	    }
                	});
            }
        }
        
       
    	if(rowExist)
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Pocket Already Added..'});
    	     $("#select_pocket").select2("val",'');	 
    	}
    	else
    	{
    	    get_PocketingDetails(this.value);

            $("#select_pocket").select2("val",'');	 
    	}
    }
});

$('#select_polish_pocket').on('change',function(){
    var id_metal_pocket=this.value;
    if(this.value!='')
    {
        var rowExist = false;
        if($('#polish_pocket_details tbody tr').length>0)
        {
            	$('#polish_pocket_details > tbody tr').each(function(bidx, brow){ 
            	    curRow = $(this);
            	    if(curRow.find('.id_metal_pocket').val()==id_metal_pocket)
            	    {
            	        rowExist = true;
            	    }
            	});
        }
       
    	if(rowExist)
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Pocket Already Added..'});
    	     $("#select_polish_pocket").select2("val",'');	 
    	}
    	else
    	{
    	    get_PolishPocketingDetails(this.value);
            $("#select_polish_pocket").select2("val",'');	 
    	}
    }
});


$('#melting_trans_type').on('change',function(){
    $('#pocket_details tbody').empty();
    $('#pocket_details tfoot').empty();
    get_pocket_details();
    $('.old_metal_melting_details').css("display","none");
    $('.tagged_item_melting_details').css("display","none");
    $('.non_tagged_item_melting_details').css("display","none");
    if(this.value==1)
    {
        $('.old_metal_melting_details').css("display","block");
    }
    else if(this.value==2)
    {
        $('.tagged_item_melting_details').css("display","block");
    }
    else if(this.value==3)
    {
        $('.non_tagged_item_melting_details').css("display","block");
    }
});

function get_PocketingDetails(id_metal_pocket)
{
    $('#pocket_details tfoot >tr').empty();

    $('#tagged_pocket_details tbody >tr').empty();

    var trHtml='';
   
    if(id_metal_pocket!='')
    {
        
        $.each(pocketDetails,function(key,val){

            $.each(val.item_details,function(k,items){

                if(items.id_metal_pocket==id_metal_pocket)
                {
                    if(items.trans_type==1) //old metal items
                    {
                        var gross_wt = parseFloat(parseFloat(items.gross_wt)-parseFloat(items.issue_gwt)).toFixed(3);
                        var net_wt = parseFloat(parseFloat(items.net_wt)-parseFloat(items.issue_nwt)).toFixed(3);
                        var piece = parseFloat(parseFloat(items.piece)-parseFloat(items.issue_pcs)).toFixed(3);
                        var dia_wt = parseFloat(parseFloat(items.dia_wt)-parseFloat(items.issue_dia_wt)).toFixed(3);
                        var bal_purity  = parseFloat(parseFloat(items.tot_purity)).toFixed(3);
                        var avg_rate_per_grm = parseFloat(parseFloat(items.avg_rate)).toFixed(2);

                        var total_amt = 0 ;

                        total_amt = parseFloat(parseFloat(parseFloat(net_wt)*parseFloat(bal_purity)/100)*parseFloat(avg_rate_per_grm)).toFixed(2);
                       
                        trHtml+='<tr>'
                        +'<td><input type="checkbox" name="pocket[trans_type][]" class="trans_type" value="'+items.trans_type+'" checked><input type="hidden" name="pocket[id_metal_pocket][]" class="id_metal_pocket" value="'+items.id_metal_pocket+'">'+items.pocket_no+'</td>'
                        +'<td><input type="hidden" name="pocket[id_metal_type][]" class="id_metal_type" value="'+items.id_metal_type+'">'+items.metal_type+'</td>'
                        +'<td><input type="hidden" name="pocket[id_category][]" class="id_category" value=""></td>'
                        +'<td><input type="number" name="pocket[piece][]" class="piece col-md-6" value="'+piece+'"><input type="hidden" class="blc_pieces " value="'+piece+'"> of &nbsp;'+piece+'<br/><span style="font-size:10px;color:red;"class="aerr"></span></td>'
                        +'<td><input type="number" name="pocket[gross_wt][]" class="gross_wt col-md-6" value="'+gross_wt+'"><input type="hidden" class="blc_gross_wt " value="'+gross_wt+'"> of &nbsp;'+gross_wt+'<br/><span style="font-size:10px;color:red;" class="berr"></span></td>'
                        +'<td><input type="number" name="pocket[net_wt][]" class="net_wt col-md-6" value="'+net_wt+'" ><input type="hidden" class="blc_net_wt " value="'+net_wt+'"> of &nbsp;'+net_wt+'<br/><span style="font-size:10px;color:red;" class="cerr"></span></td>'
                        +'<td><input type="number" name="pocket[dia_wt][]" class="dia_wt col-md-6" value="'+dia_wt+'" ><input type="hidden" class="blc_dia_wt " value="'+dia_wt+'"> of &nbsp;'+dia_wt+'<br/><span style="font-size:10px;color:red;" class="daerr"></span></td>'
                        +'<td><input type="number" name="pocket[avg_purity][]" class="avg_purity col-md-6" value="'+(isNaN(bal_purity)?0:bal_purity)+'" ><input type="hidden" class="blc_purity " value="'+(isNaN(bal_purity)?0:bal_purity)+'"> of &nbsp;'+(isNaN(bal_purity)?0:bal_purity)+'<br/><span style="font-size:10px;color:red;" class="derr"></span></td>'
                        +'<td><input type="number" name="pocket[avg_rate_per_gram][]" class="avg_rate_per_gram col-md-6" value="'+(isNaN(avg_rate_per_grm) ? 0 : avg_rate_per_grm)+'" style="width:100px;" ><input type="hidden" class="blc_rate_per_gram " value="'+(isNaN(avg_rate_per_grm) ? 0 : avg_rate_per_grm)+'"> of &nbsp;'+(isNaN(avg_rate_per_grm) ? 0 : avg_rate_per_grm)+'<br/><span style="font-size:10px;color:red;" class="rerr"></span></td>'
                        +'<td><input type="number" name="pocket[amount][]" class="amount col-md-6" value="'+total_amt+'" style="width:100px;" readonly><input type="hidden" class="blcitem_cost " value="'+total_amt+'"> of &nbsp;'+total_amt+'<br/><span style="font-size:10px;color:red;" class="eerr"></span></td>'
                        +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                        +'</tr>';
                    }else if(items.trans_type==2)
                    {
                        trHtml+='<tr>'
                        +'<td><input type="checkbox" name="pocket[trans_type][]" class="trans_type" value="'+items.trans_type+'" checked><input type="hidden" name="pocket[id_pocket_details][]" class="id_pocket_details" value="'+items.id_pocket_details+'"><input type="hidden" name="pocket[id_metal_pocket][]" class="id_metal_pocket" value="'+items.id_metal_pocket+'">'+items.pocket_no+'</td>'
                        +'<td>'+items.tag_code+'</td>'
                        +'<td><span><input type="number" name="pocket[piece][]" class="piece col-md-6" value="'+items.piece+'"><input type="hidden" class="blc_pieces " value="'+items.piece+'"> of &nbsp;'+items.piece+'<br/><span style="font-size:10px;color:red;" class="aerr"></span></span></td>'
                        +'<td><span><input type="number" name="pocket[gross_wt][]" class="gross_wt col-md-6" value="'+items.gross_wt+'"><input type="hidden" class="blc_gross_wt " value="'+items.gross_wt+'"> of &nbsp;'+items.gross_wt+'<br/><span style="font-size:10px;color:red;" class="berr"></span></span></td>'
                        +'<td><span><input type="number" name="pocket[net_wt][]" class="net_wt col-md-6" value="'+items.net_wt+'"><input type="hidden" class="blc_net_wt " value="'+items.net_wt+'"> of &nbsp;'+items.net_wt+'<br/><span style="font-size:10px;color:red;" class="cerr"></span></span></td>'
                        +'<td></td>'+
                        +'<td><span><input type="number" name="pocket[avg_purity][]" class="purity col-md-6" value="'+items.purity+'"><input type="hidden" class="blc_purity " value="'+items.purity+'"> of &nbsp;'+items.purity+'<br/><span style="font-size:10px;color:red;" class="derr"></span></span></td>'
                        +'<td><span><input type="number" name="pocket[amount][]" class="item_cost col-md-6" value="'+items.item_cost+'"><input type="hidden" class="blcitem_cost " value="'+items.item_cost+'"> of &nbsp;'+items.item_cost+'<br/><span style="font-size:10px;color:red;" class="eerr"></span></span></td>'
                        +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                        +'</tr>';
                    }
                    else if(items.trans_type==3)
                    {
                        trHtml+='<tr>'
                        +'<td><input type="checkbox" name="pocket[trans_type][]" class="trans_type" value="'+items.trans_type+'" ><input type="hidden" name="pocket[id_pocket_details][]" class="id_pocket_details" value="'+items.id_pocket_details+'"><input type="hidden" name="pocket[id_metal_pocket][]" class="id_metal_pocket" value="'+items.id_metal_pocket+'">'+items.pocket_no+'</td>'
                        +'<td>'+items.product_name+'</td>'
                        +'<td><span><input type="number" name="pocket[piece][]" class="piece col-md-6" value="'+items.piece+'"><input type="hidden" class="blc_pieces " value="'+items.piece+'"> of &nbsp;'+items.piece+'<br/><span style="font-size:10px;color:red;" class="aerr"></span></span></td>'
                        +'<td><span><input type="number" name="pocket[gross_wt][]" class="gross_wt col-md-6" value="'+items.gross_wt+'"><input type="hidden" class="blc_gross_wt " value="'+items.gross_wt+'"> of &nbsp;'+items.gross_wt+'<br/><span style="font-size:10px;color:red;" class="berr"></span></span></td>'
                        +'<td><span><input type="number" name="pocket[net_wt][]" class="net_wt col-md-6" value="'+items.net_wt+'"><input type="hidden" class="blc_net_wt " value="'+items.net_wt+'"> of &nbsp;'+items.net_wt+'<br/><span style="font-size:10px;color:red;" class="cerr"></span></span></td>'
                        +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                        +'</tr>';
                    }
                   
                }
            });


            $.each(val.sales_item_details,function(key,items)
            {
                if(items.id_metal_pocket==id_metal_pocket)
                {
                    if(items.trans_type==1)
                    {
                        var gross_wt = parseFloat(parseFloat(items.gross_wt)-parseFloat(items.issue_gwt)).toFixed(3);
                        var net_wt = parseFloat(parseFloat(items.net_wt)-parseFloat(items.issue_nwt)).toFixed(3);
                        var dia_wt = parseFloat(parseFloat(items.dia_wt)-parseFloat(items.issue_dia_wt)).toFixed(3);
                        var piece = parseFloat(parseFloat(items.piece)-parseFloat(items.issue_pcs)).toFixed(3);
                        var bal_purity  = parseFloat(parseFloat(parseFloat(items.tot_purity)-parseFloat(items.issue_purity))/parseFloat(piece)).toFixed(3);
                        var avg_rate_per_grm = parseFloat(parseFloat(items.avg_rate)).toFixed(2);

                        trHtml+='<tr>'
                        +'<td><input type="checkbox" name="pocket[trans_type][]" class="trans_type" value="'+items.trans_type+'" checked><input type="hidden" name="pocket[id_metal_pocket][]" class="id_metal_pocket" value="'+items.id_metal_pocket+'">'+items.pocket_no+'</td>'
                        +'<td><input type="hidden" name="pocket[id_metal_type][]" class="id_metal_type" value="'+items.id_metal_type+'">'+items.metal_type+'</td>'
                        +'<td><input type="hidden" name="pocket[id_category][]" class="id_category" value="'+items.cat_id+'">'+(items.category!=null ? items.category:'-')+'</td>'
                        +'<td><input type="number" name="pocket[piece][]" class="piece col-md-6" value="'+piece+'"><input type="hidden" class="blc_pieces " value="'+piece+'"> of &nbsp;'+piece+'<br/><span style="font-size:10px;color:red;"class="aerr"></span></td>'
                        +'<td><input type="number" name="pocket[gross_wt][]" class="gross_wt col-md-6" value="'+gross_wt+'"><input type="hidden" class="blc_gross_wt " value="'+gross_wt+'"> of &nbsp;'+gross_wt+'<br/><span style="font-size:10px;color:red;" class="berr"></span></td>'
                        +'<td><input type="number" name="pocket[net_wt][]" class="net_wt col-md-6" value="'+net_wt+'"><input type="hidden" class="blc_net_wt " value="'+net_wt+'"> of &nbsp;'+net_wt+'<br/><span style="font-size:10px;color:red;" class="cerr"></span></td>'
                        +'<td><input type="number" name="pocket[dia_wt][]" class="dia_wt col-md-6" value="'+dia_wt+'" ><input type="hidden" class="blc_dia_wt " value="'+dia_wt+'"> of &nbsp;'+dia_wt+'<br/><span style="font-size:10px;color:red;" class="daerr"></span></td>'
                        +'<td><input type="number" name="pocket[avg_purity][]" class="avg_purity col-md-6" value="'+(isNaN(bal_purity)?0:bal_purity)+'"><input type="hidden" class="blc_purity " value="'+(isNaN(bal_purity)?0:bal_purity)+'"> of &nbsp;'+(isNaN(bal_purity)?0:bal_purity)+'<br/><span style="font-size:10px;color:red;" class="derr"></span></td>'
                        +'<td><input type="number" name="pocket[avg_rate_per_gram][]" class="avg_rate_per_gram col-md-6" value="'+(isNaN(avg_rate_per_grm) ? 0 : avg_rate_per_grm)+'" style="width:100px;" ><input type="hidden" class="blc_rate_per_gram " value="'+(isNaN(avg_rate_per_grm) ? 0 : avg_rate_per_grm)+'"> of &nbsp;'+(isNaN(avg_rate_per_grm) ? 0 : avg_rate_per_grm)+'<br/><span style="font-size:10px;color:red;" class="rerr"></span></td>'
                        +'<td><input type="number" name="pocket[amount][]" class="amount col-md-6" value="'+items.amount+'" style="width:100px;"><input type="hidden" class="blcitem_cost " value="'+items.amount+'"> of &nbsp;'+items.amount+'<br/><span style="font-size:10px;color:red;" class="eerr"></span></td>'
                        +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                        +'</tr>';
                    }
                }
            });
            
        });

        if($('#melting_trans_type').val()==1)
        {
            $('#pocket_details tbody').append(trHtml); 

            calculate_pocketing_details();
        }
        else if($('#melting_trans_type').val()==2){

            $('#tagged_pocket_details tbody').append(trHtml);


            calculate_tag_pocketing_details();
        }
        else{
            $('#non_tagged_pocket_details tbody').append(trHtml);
           
           var ab =  $('#non_tagged_pocket_details tbody').append(trHtml);

           console.log(ab);

            calculate_non_tag_pocketing_details();
        }
        
        
    }
}

$('#old_metal_select_all').click(function(event) {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
    event.stopPropagation(); 
    calculate_pocketing_details();
});

$('#tag_select_all').click(function(event) {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
    event.stopPropagation(); 
    calculate_tag_pocketing_details();

});
$('#non_tag_select_all').click(function(event) {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
    event.stopPropagation(); 
    calculate_non_tag_pocketing_details();
});

$(document).on('click',".trans_type", function(){

    if($('#melting_trans_type').val()==1)
     {
        calculate_pocketing_details();
    
    }
     else if($('#melting_trans_type').val()==2){

        calculate_tag_pocketing_details();
     }
     else 
     {
        calculate_non_tag_pocketing_details();
     }
});	

$(document).on('input',".piece", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blc_pieces').val());
    if($(this).val() > blc){
        row.find('.aerr').text('Invalid');
        row.find('.piece').val(blc);
    }else{
        row.find('td:eq(2) .aerr').text('');
    }
    if($('#melting_trans_type').val()==1)
    {
       calculate_pocketing_details();

    }
    else if($('#melting_trans_type').val()==2){

       calculate_tag_pocketing_details();
    }
    else 
    {
       calculate_non_tag_pocketing_details();
    }

});

$(document).on('input',".gross_wt", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blc_gross_wt').val());
    if($(this).val() > blc){
        row.find('.berr').text('Invalid');
        row.find('.gross_wt').val(blc);
    }else{
        row.find('td:eq(3) .berr').text('');
    }
    if($('#melting_trans_type').val()==1)
    {
        calculate_OldMetal_value(row);
       calculate_pocketing_details();

    }
    else if($('#melting_trans_type').val()==2){

       calculate_tag_pocketing_details();
    }
    else 
    {
       calculate_non_tag_pocketing_details();
    }
});

$(document).on('input',".net_wt", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blc_net_wt').val());
    if($(this).val() > blc){
        row.find('.cerr').text('Invalid');
        row.find('.net_wt').val(blc);
    }else{
        row.find('td:eq(4) .cerr').text('');
    }
    if($('#melting_trans_type').val()==1)
    {
       calculate_pocketing_details();

    }
    else if($('#melting_trans_type').val()==2){

       calculate_tag_pocketing_details();
    }
    else 
    {
       calculate_non_tag_pocketing_details();
    }
});

$(document).on('input',".dia_wt",function()
{
    var row = $(this).closest('tr');
    blc = parseFloat(row.find('.blc_dia_wt').val());
    if($(this).val() > blc)
    {
        row.find('.daerr').text('Invalid');
        row.find('.dia_wt').val(blc);
    }
    else
    {
        row.find('td:eq(5) .daerr').text('');
    }
    if($('#melting_trans_type').val()==1)
    {
       calculate_pocketing_details();

    }
    else if($('#melting_trans_type').val()==2){

       calculate_tag_pocketing_details();
    }
    else 
    {
       calculate_non_tag_pocketing_details();
    }
})

$(document).on('input',".purity", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blc_purity').val());
    if($(this).val() > blc){
        row.find('.derr').text('Invalid');
        row.find('.purity').val(blc);
    }else{
        row.find('td:eq(6) .derr').text('');
    }
    if($('#melting_trans_type').val()==1)
    {
       calculate_pocketing_details();

    }
    else if($('#melting_trans_type').val()==2){

       calculate_tag_pocketing_details();
    }
});

$(document).on('input',".avg_purity", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blc_purity').val());
    if($(this).val() > blc){
        row.find('.derr').text('Invalid');
        row.find('.avg_purity').val(blc);
    }else{
        row.find('td:eq(6) .derr').text('');
    }
    calculate_OldMetal_value(row);
    calculate_pocketing_details();  
});

$(document).on('input',".avg_rate_per_gram",function()
{
    var row = $(this).closest('tr');
    blc = parseFloat(row.find('.blc_rate_per_gram').val());
    if($(this).val() > blc){
        row.find('.rerr').text('Invalid');
        row.find('.avg_rate_per_gram').val(blc);
    }else{
        row.find('td:eq(7) .rerr').text('');
    }
    calculate_OldMetal_value(row);
    calculate_pocketing_details(); 
})

$(document).on('input',".amount", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blcitem_cost ').val());
    if($(this).val() > blc){
        row.find('.eerr').text('Invalid');
        row.find('.amount').val(blc);
    }else{
        row.find('td:eq(7) .eerr').text('');
    }
    calculate_pocketing_details();  
});

$(document).on('input',".item_cost", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blcitem_cost ').val());
    if($(this).val() > blc){
        row.find('.eerr').text('Invalid');
        row.find('.item_cost').val(blc);
    }else{
        row.find('td:eq(7) .eerr').text('');
    }
    if($('#melting_trans_type').val()==1)
    {
       calculate_pocketing_details();

    }
    else if($('#melting_trans_type').val()==2){

       calculate_tag_pocketing_details();
    }
    else 
    {
       calculate_non_tag_pocketing_details();
    }});


function get_PolishPocketingDetails(id_metal_pocket)
{
    $('#polish_pocket_details tfoot >tr').empty();
    var trHtml='';
   
    if(id_metal_pocket!='')
    {
        $.each(PolishpocketDetails,function(k,val){
            $.each(val.pocket_details,function(key,items){
                    if(items.id_metal_pocket==id_metal_pocket)
                    {
                        console.log('tot_purity:'+items.tot_purity);
                        console.log('total_issue_purity:'+items.total_issue_purity);
                       var pcs         = parseFloat(items.piece-items.issue_pcs).toFixed(3);
                       var gross_wt    = parseFloat(items.gross_wt-items.issue_gwt).toFixed(3);
                       var net_wt      = parseFloat(items.net_wt-items.issue_nwt).toFixed(3);
                       var bal_purity  = parseFloat(parseFloat(parseFloat(items.tot_purity)-parseFloat(items.total_issue_purity))).toFixed(3);
                       var bal_avg_purity  = parseFloat(parseFloat(parseFloat(items.tot_purity)-parseFloat(items.total_issue_purity))/parseFloat(pcs)).toFixed(3);
                        trHtml+='<tr>'
                                    +'<td><input type="hidden" name="pocket[id_metal_pocket][]" class="id_metal_pocket" value="'+items.id_metal_pocket+'">'+items.pocket_no+'</td>'
                                    +'<td><input type="hidden" name="pocket[id_metal_type][]" class="id_metal_type" value="'+items.id_metal_type+'">'+items.old_metal_type+'</td>'
                                    +'<td><input type="hidden" name="pocket[piece][]" class="piece" value="'+pcs+'">'+pcs+'</td>'
                                    +'<td><input type="hidden" name="pocket[gross_wt][]" class="gross_wt" value="'+gross_wt+'">'+gross_wt+'</td>'
                                    +'<td><input type="hidden" name="pocket[net_wt][]" class="net_wt" value="'+net_wt+'">'+net_wt+'</td>'
                                    +'<td><input type="hidden" name="pocket[diawt][]" class="diawt" value="'+items.diawt+'">'+items.diawt+'</td>'
                                    +'<td><input type="hidden" name="pocket[tot_purity][]" class="tot_purity" value="'+bal_purity+'"><input type="hidden" name="pocket[avg_purity][]" class="avg_purity" value="'+bal_avg_purity+'">'+bal_avg_purity+'</td>'
                                    +'<td><input type="hidden" name="pocket[amount][]" class="amount" value="'+items.amount+'">'+items.amount+'</td>'
                                    +'<td style="width: 85px;" ><input type="number"   name="pocket[issue_pcs][]" class="form-control issue_pcs" value="" ></td>'
                                    +'<td style="width: 100px;"><input type="number"  name="pocket[issue_gwt][]" class="form-control issue_gwt" value="" ></td>'
                                    +'<td style="width: 100px;"><input type="number" name="pocket[issue_nwt][]" class="form-control issue_nwt" value="" ></td>'
                                    +'<td style="width: 100px;"><input type="number"  name="pocket[issue_purity][]" class="form-control issue_purity" value="" max="100"></td>'
                                    +'<td style="width: 100px;"><input type="number"  name="pocket[blc_avg_purity][]" class="form-control blc_avg_purity" value="" readonly></td>'
                                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                                +'</tr>';
                    }
            });
           
        });
        
        $('#polish_pocket_details tbody').append(trHtml);
        calculate_polishing_pocketing_details();
    }
}

$(document).on('keyup',".issue_purity", function(){
    var row = $(this).closest('tr'); 
    var issue_purity = row.find('.issue_purity').val();
    if(parseFloat(issue_purity)>100)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Vaild Weight..'});
        row.find('.issue_purity').focus();
        row.find('.issue_purity').val('');
    }
    calculate_polishing_pocketing_details();
});

$(document).on('keyup',".issue_gwt", function(){
    var row = $(this).closest('tr'); 
    var gross_wt = row.find('.gross_wt').val();
    var issue_gwt = row.find('.issue_gwt').val();
    if(parseFloat(gross_wt)<parseFloat(issue_gwt))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Vaild Weight..'});
        row.find('.issue_gwt').focus();
        row.find('.issue_gwt').val(gross_wt);
    }
    calculate_polishing_pocketing_details();
});


$(document).on('keyup',".issue_nwt", function(){
    var row = $(this).closest('tr'); 
    var net_wt = row.find('.net_wt').val();
    var issue_nwt = row.find('.issue_nwt').val();
    if(parseFloat(net_wt)<parseFloat(issue_nwt))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Vaild Weight..'});
        row.find('.issue_nwt').focus();
        row.find('.issue_nwt').val(net_wt);
    }
    calculate_polishing_pocketing_details();
});


$(document).on('keyup',".issue_pcs,.issue_purity", function(){
    var row = $(this).closest('tr'); 
    var piece = row.find('.piece').val();
    var issue_pcs = row.find('.issue_pcs').val();
    if(parseFloat(piece)<parseFloat(issue_pcs))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Vaild Pcs'});
        row.find('.issue_pcs').focus();
        row.find('.issue_pcs').val(piece);
    }
    calculate_polishing_pocketing_details();
});

function calculate_polishing_pocketing_details()
{
    $('#polish_pocket_details tfoot').empty();
    var trHtml='';
    var tot_gwt=0;
    var tot_nwt=0;
    var tot_amt=0;
    var tot_pcs=0;
    var tot_purity=0;
    var total_avg_purity=0;
    var total_isssue_pcs = 0;
    var total_isssue_gwt = 0;
    var total_isssue_nwt = 0;
    var total_pocket_avg_purity = 0;
    var total_balance_pocket_pcs = 0;
    var avg_purity = 0;
    if( $('#polish_pocket_details > tbody tr').length>0)
    {
        $('#polish_pocket_details > tbody tr').each(function(idx, row){
            curRow = $(this);
            var blc_avg_purity = 0;
            tot_pcs+=parseFloat(curRow.find('.piece').val());
            tot_gwt+=parseFloat(curRow.find('.gross_wt').val());
            tot_nwt+=parseFloat(curRow.find('.net_wt').val());
            tot_purity+=parseFloat(curRow.find('.avg_purity').val());
            tot_amt+=parseFloat(curRow.find('.amount').val());
            total_isssue_pcs+=parseFloat(curRow.find('.issue_pcs').val()!='' ? curRow.find('.issue_pcs').val():0);
            total_isssue_gwt+=parseFloat(curRow.find('.issue_gwt').val()!='' ? curRow.find('.issue_gwt').val():0);
            total_isssue_nwt+=parseFloat(curRow.find('.issue_nwt').val()!='' ? curRow.find('.issue_nwt').val():0);
            
            total_pocket_pcs    = (curRow.find('.piece').val()!='' ? curRow.find('.piece').val():0);
            issue_pcs           = (curRow.find('.issue_pcs').val()!='' ? curRow.find('.issue_pcs').val():0);
            total_purity        = (curRow.find('.tot_purity').val()!='' ? curRow.find('.tot_purity').val():0);
            issue_purity        = (curRow.find('.issue_purity').val()!='' ? curRow.find('.issue_purity').val():0);
            
            total_issue_purity  = parseFloat(issue_purity)*parseFloat(issue_pcs).toFixed(2);
            
            var balance_pocket_pur = parseFloat(total_purity)-parseFloat(total_issue_purity);
            
            var balance_pocket_pcs = parseFloat(total_pocket_pcs)-parseFloat(issue_pcs);
            
            total_balance_pocket_pcs+= parseFloat(total_pocket_pcs)-parseFloat(issue_pcs);
            
            var pocket_avg_purity = parseFloat(balance_pocket_pur);
            
            total_pocket_avg_purity+=parseFloat(balance_pocket_pur);
            
            
            if(balance_pocket_pcs>0)
            {
                curRow.find('.blc_avg_purity').val(parseFloat(balance_pocket_pur/balance_pocket_pcs).toFixed(2));
            }else
            {
                curRow.find('.blc_avg_purity').val(0);
            }
            
            
        });
        if(total_balance_pocket_pcs>0)
        {
            avg_purity=parseFloat(parseFloat(total_pocket_avg_purity)/total_balance_pocket_pcs).toFixed(2);
        }
        
        console.log('avg_purity:'+avg_purity);
    }
   
     trHtml+='<tr style="font-weight:bold;">'
                +'<td colspan="7">TOTAL</td>'
                +'<td>'+parseFloat(total_isssue_pcs).toFixed(0)+'<input type="hidden" name="process[piece]" value="'+parseFloat(total_isssue_pcs).toFixed(0)+'"></td>'
                +'<td>'+parseFloat(total_isssue_gwt).toFixed(3)+'<input type="hidden" name="process[gross_wt]" value="'+parseFloat(total_isssue_gwt).toFixed(3)+'"></td>'
                +'<td>'+parseFloat(total_isssue_nwt).toFixed(3)+'<input type="hidden" name="process[net_wt]" value="'+parseFloat(total_isssue_nwt).toFixed(3)+'"></td>'
                +'<td></td>'
                +'<td></td>'
                +'<td>'+parseFloat(avg_purity).toFixed(3)+'<input type="hidden" name="process[total_issue_purity]" value="'+parseFloat(total_issue_purity).toFixed(3)+'"><input type="hidden" name="process[purity]" value="'+parseFloat(avg_purity).toFixed(3)+'"></td>'
                +'<td></td>'
            +'</tr>';
    $('#polish_pocket_details tfoot').append(trHtml);
}

function calculate_pocketing_details()
{
    
    $('#pocket_details tfoot').empty();

    var trHtml='';
    var tot_pcs=0;
    var tot_gwt=0;
    var tot_nwt=0;
    var tot_diawt =0 ;
    var tot_amt=0;
    var tot_purity=0;
    var avg_purity=0;

    var tot_rate_per_grm = 0;
    var avg_rate_per_grm = 0;

  
    $("#pocket_details input[type=checkbox]:checked").each(function () 
    { 

        var curRow = $(this).closest('tr');           
        tot_pcs+= (isNaN(curRow.find('.piece').val() ) ? 0 : parseFloat(curRow.find('.piece').val())); 
        tot_gwt+= (isNaN(curRow.find('.gross_wt').val() ) ? 0 : parseFloat(curRow.find('.gross_wt').val())); 
        tot_nwt+= (isNaN(curRow.find('.net_wt').val() ) ? 0 : parseFloat(curRow.find('.net_wt').val())); 
        tot_diawt+=(isNaN(curRow.find('.dia_wt').val()) ? 0 : parseFloat(curRow.find('.dia_wt').val()));
        tot_purity+= (isNaN(curRow.find('.avg_purity').val() ) ? 0 : parseFloat(curRow.find('.avg_purity').val())); 
        tot_rate_per_grm+=(isNaN(curRow.find('.avg_rate_per_gram ').val()) ? 0 : parseFloat(curRow.find('.avg_rate_per_gram').val()));
        tot_amt+= (isNaN(curRow.find('.amount').val() ) ? 0 : parseFloat(curRow.find('.amount').val())); 

    });
       
    avg_purity+=parseFloat(tot_purity/$('#pocket_details > tbody tr').length);
    avg_rate_per_grm+=parseFloat(tot_rate_per_grm/$('#pocket_details > tbody tr').length);

        
    trHtml='<tr style="font-weight:bold;">'
        +'<td>TOTAL</td>'
        +'<td></td>'
        +'<td></td>'
        +'<td width="10%"><input type="text" disabled="true" placeholder="Pieces" value="'+parseFloat(tot_pcs).toFixed(3)+'"/><input type="hidden" name="process[piece]" value="'+parseFloat(tot_pcs).toFixed(3)+'"></td>'
        +'<td width="10%"><input type="text" disabled="true" placeholder="Gross wt" value="'+parseFloat(tot_gwt).toFixed(3)+'"/><input type="hidden" name="process[gross_wt]" value="'+parseFloat(tot_gwt).toFixed(3)+'"></td>'
        +'<td width="10%"><input type="text" disabled="true" placeholder="Net wt" value="'+parseFloat(tot_nwt).toFixed(3)+'"/><input type="hidden" name="process[net_wt]" value="'+parseFloat(tot_nwt).toFixed(3)+'"></td>'
        +'<td width="10%"><input type="text" disabled="true" placeholder="Dia wt" value="'+parseFloat(tot_diawt).toFixed(3)+'"/><input type="hidden" name="process[dia_wt]" value="'+parseFloat(tot_diawt).toFixed(3)+'"></td>'
        +'<td width="10%"><input type="text" disabled="true" placeholder="Purity" value="'+parseFloat(avg_purity).toFixed(3)+'"/><input type="hidden" name="process[purity]" value="'+parseFloat(avg_purity).toFixed(2)+'"></td>'
        +'<td width="10%"><input type="text" disabled="true" placeholder="Rate" value="'+parseFloat(avg_rate_per_grm).toFixed(2)+'"/><input type="hidden" name="process[rate]" value="'+parseFloat(avg_rate_per_grm).toFixed(2)+'"></td>'
        +'<td width="10%"><input type="text" disabled="true" placeholder="Total Amount" value="'+parseFloat(tot_amt).toFixed(3)+'"/><input type="hidden" name="process[amount]" value="'+parseFloat(tot_amt).toFixed(3)+'"></td>'
        +'<td></td>'
    +'</tr>';

    $('#pocket_details tfoot').append(trHtml);
}

function calculate_tag_pocketing_details()
{
    var trHtml='';
    var tot_pcs=0;
    var tot_gwt=0;
    var tot_nwt=0;
    var item_cost=0;
    var tot_purity=0;
    
        $("#tagged_pocket_details input[type=checkbox]:checked").each(function () { 
            var curRow = $(this).closest('tr'); 
            tot_pcs+= (isNaN(curRow.find('.piece').val() ) ? 0 : parseFloat(curRow.find('.piece').val())); 
            tot_gwt+= (isNaN(curRow.find('.gross_wt').val() ) ? 0 : parseFloat(curRow.find('.gross_wt').val())); 
            tot_nwt+= (isNaN(curRow.find('.net_wt').val() ) ? 0 : parseFloat(curRow.find('.net_wt').val())); 
            tot_purity+= (isNaN(curRow.find('.purity').val() ) ? 0 : parseFloat(curRow.find('.purity').val())); 
            item_cost+= (isNaN(curRow.find('.item_cost').val() ) ? 0 : parseFloat(curRow.find('.item_cost').val())); 
            console.log(tot_pcs);
            console.log(tot_gwt);

        });
   

   $('.tot_pcs').val(parseFloat(tot_pcs));
   $('.total_pcs').val(parseFloat(tot_pcs));
   $('.tot_gwt').val(parseFloat(tot_gwt).toFixed(3));
   $('.tot_gross_wt').val(parseFloat(tot_gwt).toFixed(3));
   $('.tot_nwt').val(parseFloat(tot_nwt).toFixed(3));
   $('.tot_net_wt').val(parseFloat(tot_nwt).toFixed(3));
   $('.tot_purity').val(parseFloat(tot_purity/parseFloat($('#tagged_pocket_details > tbody tr').length)).toFixed(3));
   $('.tot_avg_purity').val(parseFloat(tot_purity/parseFloat($('#tagged_pocket_details > tbody tr').length)).toFixed(3));
   $('.tag_tot_amount').val(parseFloat(item_cost).toFixed(2));
   $('.tot_value').val(parseFloat(item_cost).toFixed(2));
   
}


function calculate_non_tag_pocketing_details()
{
    var trHtml='';
    var tot_pcs=0;
    var tot_gwt=0;
    var tot_nwt=0;
   
    $("#non_tagged_pocket_details input[type=checkbox]:checked").each(function () { 
        var curRow = $(this).closest('tr'); 
        tot_pcs+= (isNaN(curRow.find('.piece').val() ) ? 0 : parseFloat(curRow.find('.piece').val())); 
        tot_gwt+= (isNaN(curRow.find('.gross_wt').val() ) ? 0 : parseFloat(curRow.find('.gross_wt').val())); 
        tot_nwt+= (isNaN(curRow.find('.net_wt').val() ) ? 0 : parseFloat(curRow.find('.net_wt').val())); 
        });
   
   $('.tot_pcs').val(parseFloat(tot_pcs));
   $('.total_pcs').val(parseFloat(tot_pcs));
   $('.tot_gwt').val(parseFloat(tot_gwt).toFixed(3));
   $('.tot_gross_wt').val(parseFloat(tot_gwt).toFixed(3));
   $('.tot_nwt').val(parseFloat(tot_nwt).toFixed(3));
   $('.tot_net_wt').val(parseFloat(tot_nwt).toFixed(3));
}


$('#issue_submit').on('click',function(){
    var process_for = $("input[name='process[process_for]']:checked").val();//1-Issue,2-Receipt
    var against_melting = $("input[name='testing_receipt[against_melting]']:checked").val();//1-Issue,2-Receipt
    var process_type = $('#select_process').val();
    var id_karigar   = $('#karigar').val();
    var allow_submit=false;
    
        if(process_type==1) // melting
        {
            if(process_for==1) //isssue
            {
                 if($('#pocket_details tbody tr').length==0 && ($('#melting_trans_type').val()==1))
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Pocket..'});
                 }
                 else if($('#tagged_pocket_details tbody tr').length==0 && ($('#melting_trans_type').val()==2))
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Pocket..'});
                 }
                 else if($('#non_tagged_pocket_details tbody tr').length==0 && ($('#melting_trans_type').val()==3))
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Pocket..'});
                 }
                 else
                 {
                     allow_submit=true;
                 }
            }
            else if(process_for==2) // receipt
            {
                if($("input[name='receipt[is_melting_select_row][]']:checked").val())
                {
                    if(!validateMeltingReceiptRow())
                    {
                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
                    }
                    else
                    {
                        allow_submit=true;
                    }
                }
                else
                {
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Record.'});
                }
            }
        }
        else if(process_type==2) //Testing
        {
            if(process_for==1) //isssue
            {
                 if($('#testing_process_details tbody tr').length==0)
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Pocket..'});
                 }
                 else
                 {
                     allow_submit=true;
                 }
                 /*else
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
                 }*/
            }else if(process_for==2)//receipt
            {
               var against_melting = $("input[name='testing_receipt[against_melting]']:checked").val();
               if(against_melting==1)
               {
                   if(!$("input[name='testing_receipt_melting[]']:checked").val())
                   {
                            allow_submit=false;
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Item..'});
                   }
                   else if(!validateTestingReceiptRow())
                   {
                       $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
                   }
                    else
                    {
                        allow_submit=true;
                    }
               }else{
                   if(!$("input[name='testing_receipt_melting[]']:checked").val())
                   {
                            allow_submit=false;
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Item..'});
                   }else{
                       allow_submit=true;
                   }
               }
               
            }
        }
        else if(process_type==3) //Refining
        {
            if(process_for==1) //isssue
            {
                 if($('#refining_issue_details tbody tr').length==0)
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Process No..'});
                 }
                 else
                 {
                     allow_submit=true;
                 }
                
            }else if(process_for==2)//receipt
            {
                if($("input[name='testing_receipt_melting[]']:checked").val()) 
                {
                       if(!validateRefiningReceiptRow())
                       {
                           $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
                       }
                        else
                        {
                            allow_submit=true;
                        }
                }else{
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
                }
               
            }
        }
        else if(process_type==4) //Polishing
        {
            if(process_for==1) //isssue
            {
                 if($('#polish_pocket_details tbody tr').length==0)
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Process No..'});
                 }
                 else  if(!valiDatePolishingIssue())
                 {
                     allow_submit=false;
                     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
                 }
                 else
                 {
                     allow_submit=true;
                 }
                
            }else if(process_for==2)//receipt
            {
               if(!$("input[name='polishing_receipt_select[]']:checked").val())
               {
                        allow_submit=false;
                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Item..'});
               }else if(!validatePolishingReceiptRow())
               {
                   allow_submit=false;
                   $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
               }
               else
               {
                   allow_submit=true;
               }
            }
        }
        if((id_karigar=='' || id_karigar==null) && (process_for==1 ||(process_for!=1 && against_melting!=2)))
        {
            allow_submit=false;
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Karigar..'});
        }
        if(allow_submit)
        {   
                my_Date = new Date();
                $("div.overlay").css("display", "block");
                 $('#issue_submit').prop('disabled',true);
                 var form_data=$('#process_issue').serialize();
                    $.ajax({ 
        		        url:base_url+ "index.php/admin_ret_metal_process/metal_process_issue/save?nocache=" + my_Date.getUTCSeconds(),
        		        data: form_data,
        		        type:"POST",
        		        dataType:"JSON",
        		        success:function(data){
        					if(data.status)
        					{
        					    
        					}
        					window.location.reload();
        					$("div.overlay").css("display", "none"); 
        		        },
        		        error:function(error)  
        		        {	
        		        $("div.overlay").css("display", "none"); 
        		        } 
        		    });
        }
});


function valiDatePolishingIssue(){
	var row_validate = true;
	$('#polish_pocket_details > tbody  > tr').each(function(index, tr) {
		if($(this).find('.issue_pcs').val() == "" || $(this).find('.issue_pcs').val() == 0 || $(this).find('.issue_gwt').val() == "" || $(this).find('.issue_gwt').val() == 0 || $(this).find('.issue_nwt').val() == "" || $(this).find('.issue_purity').val() == "" || $(this).find('.issue_purity').val() == 0 ){
			row_validate = false;
		}
	});
	return row_validate;
}

function get_ActiveMetals()
{
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_catalog/ret_product/active_metal',
		dataType:'json',
		success:function(data){
			var id =  $("#metal").val();
			
		    $.each(data,function (key, item) {
			   		$('#metal').append(
						$("<option></option>")
						  .attr("value", item.id_metal)
						  .text(item.metal)
					);
			});
			$("#metal").select2(
			{
				placeholder:"Select Metal",
				allowClear: true		    
			}); 
			
			if($("#metal").length)
			{
			    $("#metal").select2("val","");
			}
		}
	});
}

function get_Activesections()
{
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_catalog/get_sectionBranchwise',
		dataType:'json',
		data:{'id_branch':$('#id_branch').val()},
		success:function(data){
		    section_details = data;
		}
	});
}



$('#process_filter').on('click',function(){
    
    var process_for = $("input[name='process[process_for]']:checked").val();//1-Issue,2-Receipt
    var against_melting = $("input[name='testing_receipt[against_melting]']:checked").val();//1-Issue,2-Receipt
    var id_process = $('#select_process').val();
    $('.melting_issue').css("display","none");
    $('.melting_receipt').css("display","none");
    $('#receipt_det').css("display","none");
    
    $('.melting_issue').css("display","none");
    $('.melting_receipt').css("display","none");
    $('#receipt_det').css("display","none");
    
    $('.testing_issue').css("display","none");
    $('#receipt_det').css("display","none");
    $('.testing_receipt').css("display","none");
    
     $('.testing_issue').css("display","none");
    $('#receipt_det').css("display","none");
    $('.testing_receipt').css("display","none");
    
    $('.refining_issue').css("display","none");
    $('#receipt_det').css("display","none");
    $('.refining_receipt').css("display","none");
    
    $('.refining_issue').css("display","none");
    $('#receipt_det').css("display","none");
    $('.polishing_receipt').css("display","none");
    
    $('.polish_issue').css("display","none");
    $('.melting_receipt').css("display","none");
            
    if($('#select_process').val()=='' || $('#select_process').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Process Type'});
    }else if(($('#karigar').val()=='' || $('#karigar').val()==null) && (process_for==2 && id_process!=2))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});
    }
    else if(($('#karigar').val()=='' || $('#karigar').val()==null) && (process_for==2 && id_process==2 && against_melting==2))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Karigar'});
    }
    else
    {
        var process_code='';
        var process_for = $("input[name='process[process_for]']:checked").val();//1-Issue,2-Receipt
        $.each(metal_process,function(key,items){
            if(items.id_metal_process==$('#select_process').val())
            {
                process_code=items.process_code;
            }
        });
    }
    if(process_code=='MELTING')
    {
        if(process_for==1) // issue
        {
            $('.melting_issue').css("display","block");
            $('.melting_receipt').css("display","none");
            $('#receipt_det').css("display","none");
        }
        else if(process_for==2) // Receipt
        {
            $('.melting_issue').css("display","none");
            $('.melting_receipt').css("display","block");
            $('#receipt_det').css("display","block");
            get_melting_process_details();
        }
    }
    
    if(process_code=='TESTING')
    {
        if(process_for==1) // issue
        {
            $('.testing_issue').css("display","block");
            $('#receipt_det').css("display","none");
            $('.testing_receipt').css("display","none");
            $('.vendor').css("display","block");
            $('.melting').css("display","none");
            
            get_testing_issue_details();
        }
        else if(process_for==2) // Receipt
        {
            $('.testing_issue').css("display","none");
            $('#receipt_det').css("display","block");
            $('.testing_receipt').css("display","block");
            $('.melting').css("display","block");


            if(against_melting == 1)
            {
                $('.vendor').css("display","none");
                $('.testing_receipt').css("display","none");
                $('.against_melting_receipt').css("display","block");  
            }
            else
            {
                $('.vendor').css("display","block");
                $('.testing_receipt').css("display","block");
                $('.against_melting_receipt').css("display","none"); 
                get_testing_receipt_details();
            }

            
        }
    }
    
    
    if(process_code=='REFINING')
    {
        if(process_for==1) // issue
        {
            $('.refining_issue').css("display","block");
            $('#receipt_det').css("display","none");
            $('.refining_receipt').css("display","none");
            
            get_RefiningIssueDetails();
        }
        else if(process_for==2) // Receipt
        {
            $('.refining_issue').css("display","none");
            $('#receipt_det').css("display","block");
            $('.refining_receipt').css("display","block");

            get_refining_receipt_details();
        }
    }
    
     if(process_code=='POLISHING')
    {
        if(process_for==1) // issue
        {
            $('.polish_issue').css("display","block");
            $('#receipt_det').css("display","none");
            $('.polishing_receipt').css("display","none");
            
            //get_RefiningIssueDetails();
        }
        else if(process_for==2) // Receipt
        {
            $('.polish_issue').css("display","none");
            $('#receipt_det').css("display","block");
            $('.polishing_receipt').css("display","block");
            
            get_polishing_receipt_details();
        }
    }
    
});


function get_old_metal_process()
{
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/metal_process_issue/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		dataType:"JSON",
		cache:false,
		success:function(data){
		    var list=data.list;
		    var access=data.access;
			var oTable = $('#process_list').DataTable();
			oTable.clear().draw();
			
			if (list!= null && list.length > 0)
			{  	
				oTable = $('#process_list').dataTable({
					"bDestroy": true,
					"bDestroy": true,
					"bInfo": true,
					"bFilter": true,
					"bSort": false,
					"order": [[ 0, "desc" ]],
					"aaData"  : list,
					"aoColumns": [	
									{ "mDataProp": "id_old_metal_process" },
									{ "mDataProp": "process_no" },
									{ "mDataProp": "date_add" },
									{ "mDataProp": "process_for" },
									{ "mDataProp": "process_name" },
									{ "mDataProp": "karigar_name" },
									{ "mDataProp": "emp_name" },
									{ "mDataProp": function ( row, type, val, meta ) {
                                    id= row.id_old_metal_process;
                                    print_url=base_url+'index.php/admin_ret_metal_process/process_acknowladgement/'+id;
                                    action_content='<a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Acknowladgement"><i class="fa fa-print" ></i></a>';
                                    return action_content;
                                    }
                                    }
								  ],
				});

			}
		}
	});
}

//Metal Process


//Melting Receipt
function get_melting_process_details()
{
    $('#melting_receipt tbody').empty();
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/get_KarigarMeltingIssueDetilas?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'id_karigar':$('#karigar').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    if(data.length>0)
		    {
		        trHtml='';
		        var category = "<option value=''>- Select Category-</option>";
		        $.each(category_details, function (mkey, mitem) {
            		category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
            	});	
	
		        $.each(data,function(key,items){
                    trHtml+='<tr id="'+parseFloat(key+1)+'" >'
                    +'<td><input type="checkbox" class="id_melting" name="receipt[is_melting_select_row][]" ><input type="hidden" class="is_melting_select" name="receipt[is_melting_select][]" value="0"><input type="hidden" name="receipt[id_melting][]" value="'+items.id_melting+'"><input type="hidden" name="receipt[id_old_metal_process][]" value="'+items.id_old_metal_process+'">'+items.process_no+'</td>'
                    +'<td><input type="hidden" name="receipt[gross_wt][]" class="gross_wt" value="'+items.gross_wt+'">'+items.gross_wt+'</td>'
                    +'<td><input type="hidden" name="receipt[net_wt][]" class="net_wt" value="'+items.net_wt+'">'+items.net_wt+'</td>'
                    +'<td><input type="hidden" name="receipt[avg_purity][]" class="avg_purity" value="'+items.purity+'">'+items.purity+'</td>'
                    +'<td><input type="hidden" name="receipt[amount][]" class="amount" value="'+items.amount+'" readonly>'+items.amount+'</td>'
                    /*+'<td><select class="id_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'*/
                    +'<td><a href="#" class="btn btn-success btn-sm add_category" id = "add_category"  data-toggle="modal"><i class="fa fa-plus"></i></a><input type="hidden" class="cat_details" name="receipt[category_details][]" value=""></td>'
                    +'<td><input type="number" name="receipt[recd_gwt][]" class="form-control recd_gwt" value="" readonly></td>'
                    +'<td><input type="number" name="receipt[received_less_wt][]" class="form-control received_less_wt" readonly></td>'
                    +'<td><input type="number" name="receipt[received_amount][]" class="form-control received_amount"value="'+items.amount+'" readonly></td>'
                    +'<td><input type="number" name="receipt[charge][]" class="form-control charge" value=""></td>'
                    +'<td><input type="text" name="receipt[ref_no][]" class="form-control ref_no" value=""></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                    +'</tr>';
                });
                $('#melting_receipt tbody').append(trHtml);
                $('#melting_receipt > tbody').find('.id_ret_category').select2();
                $("div.overlay").css("display", "none"); 
		    }else
		    {
		        $("div.overlay").css("display", "none"); 
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
		    }
		}
	});
}

$(document).on('keyup','.charge',function()
{
    var row = $(this).closest('tr');
    var charges  = (isNaN(row.find('.charge').val()) ? 0 : row.find('.charge').val());
    var amount = (isNaN(row.find('.amount').val()) ? 0 : row.find('.amount').val());
    var received_amount = (isNaN(row.find('.received_amount').val()) ? 0 : row.find('.received_amount').val());
    var total_amount = parseFloat(parseFloat(amount) + parseFloat(charges)).toFixed(3);

    row.find('.received_amount').val(isNaN(total_amount) ? amount : total_amount);
})

$(document).on('keyup','.recd_gwt', function(e){
    var row = $(this).closest('tr');
    var net_wt = row.find('.net_wt').val();
    var recd_gwt = row.find('.recd_gwt').val();
    if(parseFloat(net_wt)<parseFloat(recd_gwt))
    {
        row.find('.gross_wt').val(0);
        row.find('.received_less_wt').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight..'});
    }
    else
    {
        row.find('.received_less_wt').val(parseFloat(parseFloat(net_wt)-parseFloat(recd_gwt)).toFixed(3));
    }
});    

function validateMeltingReceiptRow(){
	var row_validate = true;
	$('#melting_receipt > tbody  > tr').each(function(index, tr) {
	    if($(this).find('.id_melting').is(":checked"))
        {
            var category_details=[];
            if($(this).find('.cat_details').val()!='')
            {
               category_details=JSON.parse($(this).find('.cat_details').val());
            }
                if(category_details.length ==0|| $(this).find('.recd_gwt').val()=='' || $(this).find('.charge').val()=='' ){
    			row_validate = false;
		    }
        }
	});
	return row_validate;
}


$(document).on('change',".id_melting",function(){
    if($(this).is(":checked"))
    {
        $(this).closest('tr').find('.is_melting_select').val(1);
    }else{
        $(this).closest('tr').find('.is_melting_select').val(0);
    }
});
//Melting Receipt


//receipt payment

$(document).on('keyup','.cash_amt,.net_bank_amt', function(e){
    calculate_receipt_payment();
});

function calculate_receipt_payment()
{
    
    var total_amount=0;
    var cash_amt=0;
    var net_bank_amt=0;
    cash_amt = (isNaN($('.cash_amt').val()) || $('.cash_amt').val()=='' ? 0:$('.cash_amt').val());
    net_bank_amt = (isNaN($('.net_bank_amt').val()) || $('.net_bank_amt').val()=='' ? 0:$('.net_bank_amt').val());
    total_amount=parseFloat(cash_amt)+parseFloat(net_bank_amt);
    $('.total_amount').html(parseFloat(total_amount).toFixed(2));
}

//receipt payment


//Testing Issue
function get_testing_issue_details()
{
    $('#testing_process_details tbody').empty();
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/get_testing_issue_details?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'id_karigar':$('#karigar').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    if(data.length>0)
		    {
		        trHtml='';
		        $.each(data,function(key,items){
                    trHtml+='<tr>'
                    +'<td><input type="checkbox" class="id_melting"><input type="hidden" class="is_melting_select" name="testing_issue[is_melting_select][]" value="0"><input type="hidden" name="testing_issue[id_melting_recd][]" value="'+items.id_melting_recd+'"><input type="hidden" name="testing_issue[purity][]" value="'+items.purity+'"><input type="hidden" name="testing_issue[amount][]" value="'+items.amount+'">'+items.process_no+'</td>'
                    +'<td>'+items.category_name+'</td>'
                    +'<td>'+items.net_wt+'</td>'
                    +'<td><span><input type="number" name="testing_issue[weight][]"class="weight col-md-6" value="'+items.received_wt+'"><input type="hidden" class="blc_weight" value="'+items.received_wt+'"> of &nbsp;'+items.received_wt+'<br/><span style="font-size:10px;color:red;"  class="aerr"></span></span></td>'
                    +'<td>'+items.purity+'</td>'
                    +'<td>'+items.rate+'</td>'
                    +'<td>'+items.amount+'</td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                    +'</tr>';
                });

                $('#testing_process_details tbody').append(trHtml);

                $("div.overlay").css("display", "none"); 
		    }else
		    {
		        $("div.overlay").css("display", "none"); 
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
		    }
		}
	});
}

$(document).on('input',".weight", function(){
    var row = $(this).closest('tr'); 
    blc = parseFloat(row.find('.blc_weight').val());
    if($(this).val() > blc){
        row.find('.aerr').text('Invalid');
        row.find('.weight').val(blc);
    }else{
        row.find('td:eq(4).aerr').text('');
    }
});
function get_testing_receipt_details()
{
    $('#testing_receipt tbody').empty();
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/get_testing_receipt_details?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'id_karigar':$('#karigar').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    if(data.length>0)
		    {
		        trHtml='';
		        $.each(data,function(key,items){
                    trHtml+='<tr>'
                    +'<td><input type="checkbox" name="testing_receipt_melting[]" class="testing_receipt_melting"><input type="hidden" class="is_melting_select" name="testing_receipt[is_melting_select][]" value="0"><input type="hidden" name="testing_receipt[id_metal_testing][]" value="'+items.id_metal_testing+'"><input type="hidden" name="testing_receipt[id_melting_recd][]" value="'+items.id_melting_recd+'"><input type="hidden" name="testing_receipt[received_category][]" value="'+items.received_category+'"><input type="hidden" name="testing_receipt[id_section][]" value="'+items.id_section+'"><input type="hidden" name="testing_receipt[id_product][]" value="'+items.id_product+'"><input type="hidden" name="testing_receipt[id_design][]" value="'+items.id_design+'"><input type="hidden" name="testing_receipt[id_sub_design][]" value="'+items.id_sub_design+'"><input type="hidden" name="testing_receipt[recd_pcs][]" value="'+items.recd_pcs+'">'+items.process_no+'</td>'
                    +'<td>'+items.category_name+'</td>'
                    +'<td><input type="hidden" class="form-control actual_wt" value="'+items.net_wt+'" >'+items.net_wt+'</td>'
                    +'<td>'+items.purity+'</td>'
                    +'<td>'+items.amount+'<input type="hidden" class="melting_act_charges" value="'+items.amount+'"></td>'
                    +'<td><input type="number" name="testing_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" ></td>'
                    +'<td><input type="number" name="testing_receipt[received_less_wt][]" class="form-control received_less_wt" readonly></td>'
                    +'<td><input type="number" name="testing_receipt[received_purity][]" class="form-control received_purity" value=""></td>'
                    +'<td><input type="number" name="testing_receipt[received_amount][]" class="form-control received_amount" value="'+items.amount+'" readonly ></td>'
                    +'<td><input type="number" name="testing_receipt[receipt_charges][]" class="form-control testing_receipt_charges"></td>'
                    +'<td><input type="text" name="testing_receipt[receipt_ref_no][]" class="form-control receipt_ref_no"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                    +'</tr>';
                });
                $('#testing_receipt tbody').append(trHtml);
                $("div.overlay").css("display", "none"); 
		    }else
		    {
		        $("div.overlay").css("display", "none"); 
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
		    }
		}
	});
}

$(document).on('change','.testing_receipt_charges', function(e){
    var row = $(this).closest('tr');
    var act_chrges = row.find('.melting_act_charges').val();
    var testing_receipt_charges = (row.find('.testing_receipt_charges').val()!='' ?row.find('.testing_receipt_charges').val() :0);
    var total_charges = parseFloat(parseFloat(testing_receipt_charges)+parseFloat(act_chrges)).toFixed(2);
    row.find('.received_amount').val(total_charges);
});

$(document).on('keyup','.recd_gross_wt', function(e){
    var row = $(this).closest('tr');
    var actual_wt = row.find('.actual_wt').val();
    var recd_gross_wt = row.find('.recd_gross_wt').val();
    if(parseFloat(actual_wt)<parseFloat(recd_gross_wt))
    {
        row.find('.recd_gross_wt').val('');
        row.find('.received_less_wt').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight..'});
    }
    else
    {
        row.find('.received_less_wt').val(parseFloat(parseFloat(actual_wt)-parseFloat(recd_gross_wt)).toFixed(3));
    }
});    

function validateTestingReceiptRow(){
	var row_validate = true;
	$('#testing_receipt > tbody  > tr').each(function(index, tr) {
	    if($(this).find('.testing_receipt_melting').is(":checked"))
        {
            if($(this).find('.recd_gross_wt').val()=='' || $(this).find('.recd_gross_wt').val()==0 || $(this).find('.received_purity').val()=='' || $(this).find('.received_purity').val()==0 || $(this).find('.receipt_charges').val()=='' ){
			row_validate = false;
		    }
        }
	});
	return row_validate;
}

function validateRefiningReceiptRow(){
	var row_validate = true;
	$('#refining_receipt > tbody  > tr').each(function(index, tr) {
	    if($(this).find('.testing_receipt_melting').is(":checked"))
        {
            if($(this).find('.recd_gross_wt').val()=='' || $(this).find('.recd_gross_wt').val()==0  || $(this).find('.receipt_charges').val()=='' ){
			row_validate = false;
		    }
        }
	});
	return row_validate;
}


function validateRefiningReceiptCategoryRow(){
	var row_validate = true;
	$('#refining_category_modal #category_row > tbody  > tr').each(function(index, tr) {
	    console.log($(this).find('.refining_ret_category').val());
        if($(this).find('.refining_ret_category').val()=='' || $(this).find('.refining_ret_category').val()==null || $(this).find('.id_section').val()=='' || $(this).find('.id_section').val()==null || $(this).find('.id_product').val()=='' || $(this).find('.id_product').val()==null || $(this).find('.id_design').val()=='' || $(this).find('.id_design').val()==null || $(this).find('.id_sub_design').val()=='' || $(this).find('.id_sub_design').val()==null || $(this).find('.purity').val()=='' || $(this).find('.purity').val()==null || $(this).find('.recd_gross_wt').val()=='' || $(this).find('.recd_gross_wt').val()==0 ){
		    row_validate = false;
	    }
	});
	return row_validate;
}




function validateTestingIssueRow(){
    
    row_validate = false;
	$('#testing_receipt > tbody  > tr').each(function(index, tr) {
	    if($(this).find('.is_melting_select').val()==1)
        {
			row_validate = true;
			return row_validate;
        }
	});

}



$(document).on('change',".testing_receipt_melting",function(){
    if($(this).is(":checked"))
    {
        $(this).closest('tr').find('.is_melting_select').val(1);
    }else{
        $(this).closest('tr').find('.is_melting_select').val(0);
    }
});

//Testing Issue

//Refining issue
function get_RefiningIssueDetails()
{
    $('#refining_issue_details tbody').empty();
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/get_RefiningIssueDetails?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'id_karigar':$('#karigar').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    if(data.length>0)
		    {
		        trHtml='';
		        $.each(data,function(key,items){
                    trHtml+='<tr>'
                    +'<td><input type="checkbox" class="id_melting"><input type="hidden" class="is_melting_select" name="refining_issue[is_melting_select][]" value="0"><input type="hidden" name="refining_issue[id_melting_recd][]" value="'+items.id_melting_recd+'">'+items.process_no+'</td>'
                    +'<td>'+items.category_name+'</td>'
                    +'<td><span><input type="number" name="refining_issue[weight][]"class="weight col-md-6" value="'+items.received_wt+'"><input type="hidden" class="blc_weight" value="'+items.received_wt+'"> of &nbsp;'+items.received_wt+'<br/><span style="font-size:10px;color:red;"  class="aerr"></span></span></td>'
                    +'<td>'+items.purity+'</td>'
                    +'<td>'+items.amount+'</td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                    +'</tr>';
                });
                $('#refining_issue_details tbody').append(trHtml);
                $("div.overlay").css("display", "none"); 
		    }else
		    {
		        $("div.overlay").css("display", "none"); 
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
		    }
		}
	});
}



function get_refining_receipt_details()
{
    $('#refining_receipt tbody').empty();
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/get_RefiningReceiptDetails?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'id_karigar':$('#karigar').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    if(data.length>0)
		    {
		        trHtml='';

                get_chg_tax_type($('#karigar').val());
		        var category = "<option value=''>- Select Category-</option>";
		        $.each(category_details, function (mkey, mitem) {
            		category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
            	});	
	
	
		        $.each(data,function(key,items){
                    trHtml+='<tr id="'+parseFloat(key+1)+'">'
                    +'<td><input type="checkbox" class="testing_receipt_melting" name="testing_receipt_melting[]"><input type="hidden" class="is_melting_select" name="refining_receipt[is_melting_select][]" value="0"><input type="hidden" name="refining_receipt[id_metal_refining][]" value="'+items.id_metal_refining+'"><input type="hidden" name="refining_receipt[id_melting_recd][]" value="'+items.id_melting_recd+'">'+items.process_no+'</td>'
                    +'<td>'+items.category_name+'</td>'
                    +'<td><input type="hidden" class="form-control actual_wt" value="'+items.net_wt+'" >'+items.net_wt+'</td>'
                    +'<td>'+items.purity+'</td>'
                    +'<td>'+items.amount+'<input type="hidden" class="actual_amount" value="'+items.amount+'" ></td>'
                    +'<td><a href="#" class="btn btn-success btn-sm add_refining_receipt_category" id = "add_category"  data-toggle="modal"><i class="fa fa-plus"></i></a><input type="hidden" class="cat_details" name="refining_receipt[category_details][]"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" readonly></td>'
                    +'<td><input type="number" name="refining_receipt[received_less_wt][]" class="form-control received_less_wt" readonly></td>'
                    +'<td><input type="number" name="refining_receipt[receipt_charges][]" class="form-control refining_receipt_charges"></td>'
                    +'<td><input type="number" name="refining_receipt[chg_tax_perc][]" class="form-control chg_tax_perc"></td>'
                    +'<td><input type="number" name="refining_receipt[chg_tax_value][]" class="form-control chg_tax_value" readonly></td>'
                    +'<td><input type="number" class="form-control final_amount" value="'+items.amount+'" readonly></td>'
                    +'<td><input type="text" name="refining_receipt[receipt_ref_no][]" class="form-control receipt_ref_no"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                    +'</tr>';
                });
                $('#refining_receipt tbody').append(trHtml);
                $('#refining_receipt > tbody').find('.id_ret_category').select2();
                $("div.overlay").css("display", "none"); 
		    }else
		    {
		        $("div.overlay").css("display", "none"); 
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
		    }
		}
	});
}


$(document).on('change','.refining_receipt_charges', function(e){
    var row = $(this).closest('tr');
    var act_chrges = row.find('.actual_amount').val();
    var refining_receipt_charges = (row.find('.refining_receipt_charges').val()!='' ?row.find('.refining_receipt_charges').val() :0);
    var total_charges = parseFloat(parseFloat(refining_receipt_charges)+parseFloat(act_chrges)).toFixed(2);
    row.find('.final_amount').val(total_charges);
});


$(document).on('click', ".add_category", function(e) {
	//$('#category_modal').modal('toggle');
	var curRow=$(this).closest('tr');
	create_new_category_row(curRow);
});


$(document).on('change',".id_ret_category",function(){
   var curRow=$(this).closest('tr');
   if(this.value!='')
   {    
       id_category=this.value;
       var product = "<option value=''>- Select Product-</option>";
       $.each(prod_details,function(key,items){
           if(items.cat_id==id_category)
           {
            	product += "<option value='"+items.pro_id+"'>"+items.product_name+"</option>";
           }
       });
       curRow.find('.id_product').html(product);
       curRow.find('.id_product').select2();
   }
});

$(document).on('change',".id_product",function(){
   var curRow=$(this).closest('tr');
   var id_product=curRow.find('.id_product').val();
   if(this.value!='')
   {    
       id_category=this.value;
       var design = "<option value=''>- Select Design-</option>";
       $.each(design_details,function(key,items){
           if(items.pro_id==id_product)
           {
            	design += "<option value='"+items.design_no+"'>"+items.design_name+"</option>";
           }
       });
       curRow.find('.id_design').html(design);
       curRow.find('.id_design').select2();
   }
});

$(document).on('change',".id_design",function(){
   var curRow=$(this).closest('tr');
   var id_design=curRow.find('.id_design').val();
   var id_product=curRow.find('.id_product').val();
   if(this.value!='')
   {    
       id_category=this.value;
       var sub_design = "<option value=''>- Select Design-</option>";
       $.each(sub_design_details,function(key,items){
           if(items.id_product==id_product && items.id_design==id_design)
           {
            	sub_design += "<option value='"+items.id_sub_design+"'>"+items.sub_design_name+"</option>";
           }
       });
       curRow.find('.id_sub_design').html(sub_design);
       curRow.find('.id_sub_design').select2();
   }
});

$('#add_new_category').on('click',function(){
    
    trHtml='';
    var section = "<option value=''>- Select Section-</option>";
    var category = "<option value=''>- Select Category-</option>";
    var product = "<option value=''>- Select Category-</option>";
    var design = "<option value=''>- Select Category-</option>";
    var sub_design = "<option value=''>- Select Category-</option>";
    $.each(category_details, function (mkey, mitem) {
    category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
    });	
     $.each(section_details, function (mkey, mitem) {
            section += "<option value='"+mitem.id_section+"'>"+mitem.section_name+"</option>";
    });
    trHtml+='<tr>'
    +'<td><select class="id_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'
    +'<td><select class="id_section" name="receipt[id_section][]" value="" style="width: 100px;">'+section+'</select></td>'
    +'<td><select class="id_product" name="receipt[id_product][]" value="" style="width: 100px;">'+product+'</select></td>'
    +'<td><select class="id_design" name="receipt[id_design][]" value="" style="width: 100px;" >'+design+'</select></td>'
    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="" style="width: 100px;" >'+sub_design+'</select></td>'
    +'<td><input type="number" name="refining_receipt[recd_pcs][]" class="form-control recd_pcs" value="" style="width: 100px;"></td>'
    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" style="width: 100px;"></td>'
    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
    +'</tr>';
    $('#category_row tbody').append(trHtml);
    $('#category_row > tbody').find('.id_ret_category').select2();
    $('#category_row > tbody').find('.id_product').select2();
    $('#category_row > tbody').find('.id_design').select2();
    $('#category_row > tbody').find('.id_sub_design').select2();
    $('#category_row > tbody').find('.id_section').select2();
});


function create_new_category_row(curRow)
{
    $('#category_modal').modal('toggle');
    $('#category_row tbody').empty();
    if(curRow!='' && curRow!=undefined)
    {
       trHtml='';
        console.log(curRow.find('.actual_wt').val());
        $('#active_id').val(curRow.closest('tr').attr('id'));
        var cat_details=curRow.find('.cat_details').val();

        console.log(cat_details);
        
        
        if(cat_details!='')
        {
            let categories=JSON.parse(cat_details);

            console.log(categories);

            $.each(categories,function(key,items){

                var section = "<option value=''>- Select Section-</option>";
                var category = "<option value=''>- Select Category-</option>";
                var product = "<option value=''>- Select Product-</option>";
                var design = "<option value=''>- Select Design-</option>";
                var sub_design = "<option value=''>- Select Sub Design-</option>";
                
                console.log(category_details);

                $.each(section_details, function (mkey, mitem) {
                     var section_slelected="";
                      if(items.id_section==mitem.id_section)
                      {
                          section_slelected="selected";
                      }
                      section += "<option value='"+mitem.id_section+"' "+section_slelected+" >"+mitem.section_name+"</option>";
                });
                
                $.each(category_details, function (mkey, mitem) {
                     var cat_slelected="";
                      if(items.id_ret_category==mitem.id_ret_category)
                      {
                          cat_slelected="selected";
                      }
                      category += "<option value='"+mitem.id_ret_category+"' "+cat_slelected+" >"+mitem.name+"</option>";
                });
                
                $.each(prod_details,function(key,pitems){
                   var prod_slelected="";
                   if(pitems.pro_id==items.id_product)
                   {
                    	prod_slelected="selected";
                   }
                   product += "<option value='"+pitems.pro_id+"' "+prod_slelected+">"+pitems.product_name+"</option>";
               });
               
               
               $.each(design_details,function(key,pitems){
                   var des_slelected="";
                   if(pitems.pro_id==items.id_product)
                   {
                           if(pitems.design_no==items.id_design)
                           {
                            	des_slelected="selected";
                           }
                    	design += "<option value='"+pitems.design_no+"' "+des_slelected+" >"+pitems.design_name+"</option>";
                   }
               });
               
               $.each(sub_design_details,function(key,pitems){
                    var sub_des_slelected="";
                   if(pitems.id_product==items.id_product && pitems.id_design==items.id_design)
                   {
                         if(pitems.id_sub_design==items.id_sub_design)
                           {
                            	sub_des_slelected="selected";
                           }
                    	sub_design += "<option value='"+pitems.id_sub_design+"' "+sub_des_slelected+" >"+pitems.sub_design_name+"</option>";
                   }
               });
           
                trHtml+='<tr>'
                    +'<td><select class="id_ret_category" name="receipt[id_ret_category][]" value=""  style="width: 80px;">'+category+'</select></td>'
                    +'<td><select class="id_section" name="receipt[id_section][]" value=""  style="width: 80px;">'+section+'</select></td>'
                    +'<td><select class="id_product" name="receipt[id_product][]" value="" style="width: 80px;" >'+product+'</select></td>'
                    +'<td><select class="id_design" name="receipt[id_design][]" value="" style="width: 80px;" >'+design+'</select></td>'
                    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="" style="width: 80px;" >'+sub_design+'</select></td>'
                    +'<td><input type="number" name="refining_receipt[recd_pcs][]" class="form-control recd_pcs" value="'+items.recd_pcs+'" ></td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="'+items.recd_gross_wt+'" ></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                +'</tr>';
            });
        }
        else
        {
            var section = "<option value=''>- Select Section-</option>";
            var category = "<option value=''>- Select Category-</option>";
            var product = "<option value=''>- Select Category-</option>";
            var design = "<option value=''>- Select Category-</option>";
            var sub_design = "<option value=''>- Select Category-</option>";
             $.each(category_details, function (mkey, mitem) {
                category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
            });	
            $.each(section_details, function (mkey, mitem) {
                      section += "<option value='"+mitem.id_section+"'>"+mitem.section_name+"</option>";
            });
            trHtml+='<tr>'
                    +'<td><select class="id_ret_category" name="receipt[id_ret_category][]" value="" style="width: 80px;">'+category+'</select></td>'
                    +'<td><select class="id_section" name="receipt[id_section][]" value="" style="width: 80px;">'+section+'</select></td>'
                    +'<td><select class="id_product" name="receipt[id_product][]" value="" style="width: 80px;" >'+product+'</select></td>'
                    +'<td><select class="id_design" name="receipt[id_design][]" value="" style="width: 80px;" >'+design+'</select></td>'
                    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="" style="width: 80px;" >'+sub_design+'</select></td>'
                    +'<td><input type="number" name="refining_receipt[recd_pcs][]" class="form-control recd_pcs" value="" ></td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" ></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                +'</tr>';
        }
        
    	$('#category_row tbody').append(trHtml);
        $('#category_row > tbody').find('.id_section').select2();
        $('#category_row > tbody').find('.id_ret_category').select2();
        $('#category_row > tbody').find('.id_product').select2();
        $('#category_row > tbody').find('.id_design').select2();
        $('#category_row > tbody').find('.id_sub_design').select2();
    }
}

function validateMeltingReceiptCategoryRow(){
	var row_validate = true;
	$('#category_modal .modal-body #category_row > tbody  > tr').each(function(index, tr) {
            if($(this).find('.id_section').val()=='' || $(this).find('.id_section').val()==null || $(this).find('.id_ret_category').val()=='' || $(this).find('.id_ret_category').val()==null || $(this).find('.id_product').val()=='' || $(this).find('.id_product').val()==null || $(this).find('.recd_gross_wt').val()==0  || $(this).find('.recd_pcs').val()==0  || $(this).find('.id_design').val()=='' || $(this).find('.id_design').val()==null || $(this).find('.id_sub_design').val()=='' || $(this).find('.id_sub_design').val()==null){
			    row_validate = false;
		    }
	});
	return row_validate;
}


$('#category_modal  #update_category').on('click', function(){
    if(validateMeltingReceiptCategoryRow())
    {
    	var cat_details=[];
    	var recd_gross_wt=0;
    	var catRow=$('#active_id').val();
    	var gross_wt = 	$('#'+catRow).find('.gross_wt').val();
    	
    	$('#category_modal .modal-body #category_row> tbody  > tr').each(function(index, tr) {
    	    recd_gross_wt+=parseFloat($(this).find('.recd_gross_wt').val());
    		cat_details.push({
    		    'id_ret_category'   : $(this).find('.id_ret_category').val(),
    		    'recd_gross_wt'     : $(this).find('.recd_gross_wt').val(),
    		    'recd_pcs'          : $(this).find('.recd_pcs').val(),
    		    'id_section'        : $(this).find('.id_section').val(),
    		    'id_product'        : $(this).find('.id_product').val(),
    		    'id_design'         : $(this).find('.id_design').val(),
    		    'id_sub_design'     : $(this).find('.id_sub_design').val(),
    		});
    	});
    	
    	
        if(parseFloat(gross_wt)<parseFloat(recd_gross_wt))
        {
            $('#'+catRow).find('.recd_gwt').val(0);
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight..'});
        }
        else
        {
                $('#'+catRow).find('.recd_gwt').val(parseFloat(recd_gross_wt).toFixed(3));
                $('#'+catRow).find('.received_less_wt').val(parseFloat(parseFloat(gross_wt)-parseFloat(recd_gross_wt)).toFixed(3));
            	$('#category_modal').modal('toggle');
            	$('#'+catRow).find('.cat_details').val(cat_details.length>0 ? JSON.stringify(cat_details):'');
            	$('#'+catRow).find('.recd_gwt').val(parseFloat(recd_gross_wt).toFixed(3));
            	$('#category_modal .modal-body').find('#category_row tbody').empty();
        }
    }
    else
	{
	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
	}
});

//Refining issue


//Polishing receipt

function get_polishing_receipt_details()
{
    $('#polishing_receipt tbody').empty();
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/get_PolishingReceiptDetails?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'id_karigar':$('#karigar').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    if(data.length>0)
		    {
		       var trHtml='';
		        $.each(data,function(key,items){
                    trHtml+='<tr id="'+parseFloat(key+1)+'">'
                    +'<td><input type="checkbox" name="polishing_receipt_select[]" class="id_polishing" checked><input type="hidden" class="is_polishing_select" name="polishing_receipt[is_polishing_select][]" value="1"><input type="hidden" name="polishing_receipt[id_polishing_details][]" value="'+items.id_polishing_details+'"><input type="hidden" name="polishing_receipt[id_old_metal_type][]" value="'+items.id_old_metal_type+'"><input type="hidden" name="polishing_receipt[id_polishing][]" value="'+items.id_polishing+'">'+items.process_no+'</td>'
                    +'<td>'+items.old_metal_type+'</td>'
                    +'<td><input type="hidden" class="form-control gross_wt" value="'+items.gross_wt+'" ><input type="hidden" class="form-control no_of_piece" name="polishing_receipt[no_of_piece][]" value="'+items.no_of_piece+'" >'+items.gross_wt+'</td>'
                    +'<td><input type="hidden" class="form-control actual_wt" value="'+items.net_wt+'" >'+items.net_wt+'</td>'
                    +'<td><input type="hidden" class="form-control actual_issue_diawt" value="'+items.issue_diawt+'" >'+items.issue_diawt+'</td>'
                    +'<td><a href="#" class="btn btn-success btn-sm add_polishing_receipt_category" id = "add_polishing_receipt_category"  data-toggle="modal"><i class="fa fa-plus"></i></a><input type="hidden" class="cat_details" name="polishing_receipt[category_details][]" value=""></td>'
                    +'<td><input type="number" name="polishing_receipt[recd_pcs][]" class="form-control recd_pcs" value="" readonly ></td>'
                    +'<td><input type="number" name="polishing_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" readonly ></td>'
                    +'<td><input type="number" name="polishing_receipt[recd_nwt][]" class="form-control recd_nwt" value="" readonly ></td>'
                    +'<td style="display:none;"><input type="number" name="polishing_receipt[received_less_wt][]" class="form-control received_less_wt"  readonly></td>'
                    +'<td><input type="number" name="polishing_receipt[receipt_charges][]" class="form-control receipt_charges"></td>'
                    +'<td><input type="text" name="polishing_receipt[receipt_ref_no][]" class="form-control receipt_ref_no"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                    +'</tr>';
                });
                $('#polishing_receipt tbody').append(trHtml);
                $('#polishing_receipt > tbody').find('.id_ret_category').select2();
                $("div.overlay").css("display", "none"); 
		    }else
		    {
		        $("div.overlay").css("display", "none"); 
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
		    }
		}
	});
}


$(document).on('click', ".add_polishing_receipt_category", function(e) {
	//$('#category_modal').modal('toggle');
	var curRow=$(this).closest('tr');
	create_new_polishing_category_row(curRow);
});


$(document).on('change',".refining_ret_category",function(){
   var curRow=$(this).closest('tr');
   if(this.value!='')
   {    
       id_category=this.value;
       var product = "<option value=''>- Select Product-</option>";
       var purity = "<option value=''>- Select Purity-</option>";
       $.each(prod_details,function(key,items){
           if(items.cat_id==id_category)
           {
            	product += "<option value='"+items.pro_id+"'>"+items.product_name+"</option>";
           }
       });
       
       $.each(purity_details,function(key,items){
           if(items.id_category==id_category)
           {
            	purity += "<option value='"+items.id_purity+"'>"+items.purity+"</option>";
           }
       });
       
       curRow.find('.id_product').html(product);
       curRow.find('.purity').html(purity);
       curRow.find('.id_product').select2();
       curRow.find('.purity').select2();
   }
});



function create_new_polishing_category_row(curRow)
{
    $('#polishing_category_modal').modal('toggle');
    $('#category_row tbody').empty();
    if(curRow!='' && curRow!=undefined)
    {
       trHtml='';
        console.log(curRow.find('.actual_wt').val());
        $('#active_id').val(curRow.closest('tr').attr('id'));
        var cat_details=curRow.find('.cat_details').val();
        
        
        if(cat_details!='')
        {
            
            let categories=JSON.parse(cat_details);
            $.each(categories,function(key,items){
                var category = "<option value=''>- Select Category-</option>";
                var section = "<option value=''>- Select Section-</option>";
                var product = "<option value=''>- Select Product-</option>";
                var design = "<option value=''>- Select Design-</option>";
                var sub_design = "<option value=''>- Select Sub Design-</option>";
                var purity = "<option value=''>- Select Purity-</option>";
                $.each(category_details, function (mkey, mitem) {
                     var cat_slelected="";
                      if(items.id_ret_category==mitem.id_ret_category)
                      {
                          cat_slelected="selected";
                      }
                      category += "<option value='"+mitem.id_ret_category+"' "+cat_slelected+" >"+mitem.name+"</option>";
                });	
                
                $.each(section_details, function (mkey, mitem) {
                     var section_slelected="";
                      if(items.id_section==mitem.id_section)
                      {
                          section_slelected="selected";
                      }
                      section += "<option value='"+mitem.id_section+"' "+section_slelected+" >"+mitem.section_name+"</option>";
                });
                
                $.each(prod_details,function(key,pitems){
                   var prod_slelected="";
                   
                   if(pitems.pro_id==items.id_product)
                   {
                    	prod_slelected="selected";
                   }
                   if(items.is_non_tag==1)
                   {
                       if(pitems.stock_type==2)
                       {
                           product += "<option value='"+pitems.pro_id+"' "+prod_slelected+">"+pitems.product_name+"</option>";
                       }
                   }
                   else
                   {
                       product += "<option value='"+pitems.pro_id+"' "+prod_slelected+">"+pitems.product_name+"</option>";
                   }
                   
               });
               

               
               $.each(design_details,function(key,pitems){
                   var des_slelected="";
                   if(pitems.pro_id==items.id_product)
                   {
                           if(pitems.design_no==items.id_design)
                           {
                            	des_slelected="selected";
                           }
                    	design += "<option value='"+pitems.design_no+"' "+des_slelected+" >"+pitems.design_name+"</option>";
                   }
               });
               
               $.each(sub_design_details,function(key,pitems){
                    var sub_des_slelected="";
                   if(pitems.id_product==items.id_product && pitems.id_design==items.id_design)
                   {
                         if(pitems.id_sub_design==items.id_sub_design)
                           {
                            	sub_des_slelected="selected";
                           }
                    	sub_design += "<option value='"+pitems.id_sub_design+"' "+sub_des_slelected+" >"+pitems.sub_design_name+"</option>";
                   }
               });
               
                    $.each(purity_details,function(key,pitems){
                       var purity_slelected="";
                       if(pitems.id_category==items.id_ret_category)
                       {
                           if(pitems.id_purity==items.id_purity)
                           {
                            	purity_slelected="selected";
                           }
                           purity += "<option value='"+pitems.id_purity+"' "+purity_slelected+">"+pitems.purity+"</option>";
                       }
                      
                   });
               
               
           
                trHtml+='<tr>'
                    +'<td><input type="checkbox" class="is_non_tag" name="receipt[is_non_tag][]"  '+(items.is_non_tag==1 ? "checked value='1' " : "value='0'" )+' ></td>'
                    +'<td><select class="polishing_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'
                    +'<td><select class="polishing_id_section" name="receipt[id_section][]" value="">'+section+'</select></td>'
                    +'<td><select class="id_product" name="receipt[id_product][]" value="" style="width: 100px;">'+product+'</select></td>'
                    +'<td><select class="id_design" name="receipt[id_design][]" value="" style="width: 100px;" >'+design+'</select></td>'
                    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="" style="width: 100px;">'+sub_design+'</select></td>'
                    +'<td><select class="purity" name="receipt[purity][]" value="">'+purity+'</td>'
                    +'<td><input type="number" name="refining_receipt[recd_pcs][]" class="form-control recd_pcs" value="'+items.recd_pcs+'" style="width: 100px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="'+items.recd_gross_wt+'" style="width: 100px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_nwt][]" class="form-control recd_nwt" value="'+items.recd_nwt+'" style="width: 100px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_diawt][]" class="form-control recd_diawt" value="'+items.recd_diawt+'" style="width: 100px;"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                +'</tr>';
            });
        }
        else
        {
            
            var category = "<option value=''>- Select Category-</option>";
            var section = "<option value=''>- Select Section-</option>";
            var product = "<option value=''>- Select Product-</option>";
            var design = "<option value=''>- Select Design-</option>";
            var sub_design = "<option value=''>- Select Sub Design-</option>";
            var purity = "<option value=''>- Select Purity-</option>";
             $.each(category_details, function (mkey, mitem) {
                category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
            });	
            
            $.each(section_details, function (mkey, mitem) {
                  section += "<option value='"+mitem.id_section+"' >"+mitem.section_name+"</option>";
            });
                
            trHtml+='<tr>'
                    +'<td><input type="checkbox" class="is_non_tag" name="receipt[is_non_tag][]" value="0"></td>'
                    +'<td><select class="polishing_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'
                    +'<td><select class="polishing_id_section" name="receipt[id_section][]" value="">'+section+'</select></td>'
                    +'<td><select class="id_product" name="receipt[id_product][]" value="" style="width: 100px;" >'+product+'</select></td>'
                    +'<td><select class="id_design" name="receipt[id_design][]" value=""  style="width: 100px;" >'+design+'</select></td>'
                    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value=""  style="width: 100px;" >'+sub_design+'</select></td>'
                    +'<td><select class="purity" name="receipt[purity][]" value="">'+purity+'</td>'
                    +'<td><input type="number" name="refining_receipt[recd_pcs][]" class="form-control recd_pcs" value="" style="width: 100px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" style="width: 100px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_nwt][]" class="form-control recd_nwt" value="" style="width: 100px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_diawt][]" class="form-control recd_diawt" value="" style="width: 100px;"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                +'</tr>';
        }
        
    	$('#category_row tbody').append(trHtml);
        $('#category_row > tbody').find('.polishing_ret_category').select2();
        $('#category_row > tbody').find('.polishing_id_section').select2();
        $('#category_row > tbody').find('.id_product').select2();
        $('#category_row > tbody').find('.id_design').select2();
        $('#category_row > tbody').find('.id_sub_design').select2();
    }
}

$(document).on('change',".is_non_tag",function()
{
    $(this).closest('tr').find('.id_design').prop("disabled",true);
	$(this).closest('tr').find('.id_sub_design').prop("disabled",true);
	if($(this).is(":checked"))
	{
	    	$(this).closest('tr').find('.is_non_tag').val(1);
	    	$(this).closest('tr').find('.id_design').prop("disabled",false);
	    	$(this).closest('tr').find('.id_sub_design').prop("disabled",false);
	}else{
	    $(this).closest('tr').find('.is_non_tag').val(0);
	}
});
	

$(document).on('change',".polishing_ret_category",function(){
   var curRow=$(this).closest('tr');
   
   if(this.value!='')
   {    
       id_category=this.value;
       var product = "<option value=''>- Select Product-</option>";
       var purity = "<option value=''>- Select Purity-</option>";
       is_non_tag = curRow.find('.is_non_tag').val();
       console.log(is_non_tag);
       $.each(prod_details,function(key,items){
           if(items.cat_id==id_category)
           {
                if(is_non_tag == 1)
                {
                    if(items.stock_type == 2)
                    {
                        product += "<option value='"+items.pro_id+"'>"+items.product_name+"</option>";
                    }
                }else
                {
                    product += "<option value='"+items.pro_id+"'>"+items.product_name+"</option>";
                }
            	
           }
       });
       
       $.each(purity_details,function(key,items){
           if(items.id_category==id_category)
           {
            	purity += "<option value='"+items.id_purity+"'>"+items.purity+"</option>";
           }
       });
       
       curRow.find('.id_product').html(product);
       curRow.find('.purity').html(purity);
       curRow.find('.id_product').select2();
       curRow.find('.purity').select2();
   }
});


$('#polishing_category_modal  #update_polishing_category').on('click', function(){
     if(validatePolishingReceiptCategoryRow())
     {
    	var cat_details=[];
    
    	var recd_gross_wt=0;
    	var recd_nwt=0;
    	var recd_pcs=0;
    	var recd_diawt=0;
    	var catRow=$('#active_id').val();
    	var gross_wt = 	$('#'+catRow).find('.gross_wt').val();
    	var actual_wt = 	$('#'+catRow).find('.actual_wt').val();
    	var actual_wt = 	$('#'+catRow).find('.actual_wt').val();
    	var actual_issue_diawt = 	$('#'+catRow).find('.actual_issue_diawt').val();
    	
    	$('#polishing_category_modal .modal-body #category_row> tbody  > tr').each(function(index, tr) {
    	    recd_gross_wt+=parseFloat($(this).find('.recd_gross_wt').val());
    	    recd_nwt+=parseFloat($(this).find('.recd_nwt').val());
    	    recd_pcs+=parseFloat($(this).find('.recd_pcs').val());
    	    recd_diawt+=parseFloat($(this).find('.recd_diawt').val());
    		cat_details.push({
    		    'is_non_tag':$(this).find('.is_non_tag').val(),
    		    'id_ret_category' : $(this).find('.polishing_ret_category').val(),
    		    'id_section' : $(this).find('.polishing_id_section').val(),
    		    'recd_pcs':$(this).find('.recd_pcs').val(),
    		    'recd_gross_wt' :$(this).find('.recd_gross_wt').val(),
    		    'recd_nwt' :$(this).find('.recd_nwt').val(),
    		    'id_product' :$(this).find('.id_product').val(),
    		    'id_design' :$(this).find('.id_design').val(),
    		    'id_sub_design' :$(this).find('.id_sub_design').val(),
    		    'id_purity' :$(this).find('.purity ').val(),
    		    'purity' :$(this).find('.purity option:selected').text(),
    		    'recd_diawt':$(this).find('.recd_diawt').val()
    		    
    		});
    	});
    	
    	if(parseFloat(parseFloat(gross_wt).toFixed(3)) < parseFloat(parseFloat(recd_gross_wt).toFixed(3)))
        {
            $('#'+catRow).find('.recd_gwt').val(0);
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Gross Weight..'});
        }
        else if(parseFloat(parseFloat(actual_wt).toFixed(3))<parseFloat(parseFloat(recd_nwt).toFixed(3)))
        {
            $('#'+catRow).find('.recd_gwt').val(0);
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Net Weight..'});
        }
        else if(parseFloat(parseFloat(actual_issue_diawt).toFixed(3))<parseFloat(parseFloat(recd_diawt).toFixed(3)))
        {
            $('#'+catRow).find('.recd_gwt').val(0);
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Dia Weight..'});
        }
        else
        {
            $('#'+catRow).find('.recd_pcs').val(parseFloat(recd_pcs).toFixed(0));
            $('#'+catRow).find('.recd_gross_wt').val(parseFloat(recd_gross_wt).toFixed(3));
            $('#'+catRow).find('.recd_nwt').val(parseFloat(recd_nwt).toFixed(3));
            $('#'+catRow).find('.received_less_wt').val(parseFloat(parseFloat(actual_wt)-parseFloat(recd_gross_wt)).toFixed(3));
            $('#polishing_category_modal').modal('toggle');
    	    $('#'+catRow).find('.cat_details').val(cat_details.length>0 ? JSON.stringify(cat_details):'');
    	    $('#polishing_category_modal .modal-body').find('#category_row tbody').empty();
        }
    }
	else
	{
	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
	}
});

$('#add_new_polishing_category').on('click',function(){
    if(validatePolishingReceiptCategoryRow())
    {
        trHtml='';
        var section = "<option value=''>- Select Category-</option>";
        var category = "<option value=''>- Select Category-</option>";
        var product = "<option value=''>- Select Product-</option>";
        var purity = "<option value=''>- Select Purity-</option>";
        var category_exists = false;
        $.each(category_details, function (mkey, mitem) {
            category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
        });	
            var product = "<option value=''>- Select Product-</option>";
            var design = "<option value=''>- Select Design-</option>";
            var sub_design = "<option value=''>- Select Sub Design-</option>";
            var purity = "<option value=''>- Select Purity-</option>";
             $.each(category_details, function (mkey, mitem) {
                category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
            });	
            
            $.each(section_details, function (mkey, mitem) {
             section += "<option value='"+mitem.id_section+"' >"+mitem.section_name+"</option>";
            });
        
            trHtml+='<tr>'
                    +'<td><input type="checkbox" class="is_non_tag" name="receipt[is_non_tag][]" value="0"></td>'
                    +'<td><select class="polishing_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'
                    +'<td><select class="polishing_id_section" name="receipt[id_section][]" value="">'+section+'</select></td>'
                    +'<td><select class="id_product" name="receipt[id_product][]" value="" style="width: 80px;">'+product+'</select></td>'
                    +'<td><select class="id_design" name="receipt[id_design][]" value="" disabled style="width: 80px;">'+design+'</select></td>'
                    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="" disabled style="width: 80px;">'+sub_design+'</select></td>'
                    +'<td><select class="purity" name="receipt[purity][]" value="">'+purity+'</td>'
                    +'<td><input type="number" name="refining_receipt[recd_pcs][]" class="form-control recd_pcs" value="" style="width: 80px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" style="width: 80px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_nwt][]" class="form-control recd_nwt" value="" style="width: 80px;"></td>'
                    +'<td><input type="number" name="refining_receipt[recd_diawt][]" class="form-control recd_diawt" value="" style="width: 80px;"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                +'</tr>';
        $('#polishing_category_modal .modal-body #category_row tbody').append(trHtml);
        $('#polishing_category_modal .modal-body #category_row > tbody').find('.polishing_ret_category').select2();
    }
    else
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill The Required Fields..'});
    }
});

function validatePolishingReceiptCategoryRow(){
	var row_validate = true;
	$('#polishing_category_modal .modal-body #category_row > tbody  > tr').each(function(index, tr) {
            if($(this).find('.polishing_id_section').val()=='' || $(this).find('.polishing_id_section').val()==null || $(this).find('.polishing_ret_category').val()=='' || $(this).find('.polishing_ret_category').val()==null || $(this).find('.id_product').val()=='' || $(this).find('.id_product').val()==null || $(this).find('.purity').val()==null || $(this).find('.purity').val()=='' || $(this).find('.recd_gross_wt').val()==0 || $(this).find('.recd_nwt').val()==0  || $(this).find('.recd_pcs').val()==0 || $(this).find('.id_product').val()=='' || $(this).find('.id_product').val()==null || $(this).find('.id_design').val()=='' || $(this).find('.id_design').val()==null || $(this).find('.id_sub_design').val()=='' || $(this).find('.id_sub_design').val()==null ){
			    row_validate = false;
		    }
	});
	return row_validate;
}


function get_category()
{
	$(".overlay").css('display','block');
	$('#select_category option').remove();
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_catalog/category/active_category',
		dataType:'json',
		success:function(data){
		  var id_category =  $('#select_category').val();
		   $.each(data, function (key, item) {
			   		$('#select_category').append(
						$("<option></option>")
						  .attr("value", item.id_ret_category)
						  .text(item.name)
					);
			});
			$("#select_category").select2({
			    placeholder: "Select Category",
			    allowClear: true
			});
			$("#select_category").select2("val",(id_category!='' && id_category>0?id_category:''));
			 $(".overlay").css("display", "none");	
		}
	});
}

$('#select_category').select2().on("change", function(e) {
    if(this.value!='')
    {
        get_cat_purity(); 
        get_category_product(); 
    }
});

function get_category_product()
{
	$('#prod_select option').remove();  
	$("div.overlay").css("display", "block"); 
	$.ajax({
	type: 'POST',
	url: base_url+'index.php/admin_ret_reports/get_ActiveProduct',
	dataType:'json',
	data: {
		'id_category' :$('#select_category').val()
	},
	success:function(data){
		var id =  $("#prod_select").val();
		$.each(data, function (key, item) {   
		    $("#prod_select").append(
		    $("<option></option>")
		    .attr("value", item.pro_id)    
		    .text(item.product_name)  
		    );
		});
		$("#prod_select").select2(
		{
			placeholder:"Select Product",
			allowClear: true		    
		});
		if($("#prod_select").length)
		{
		    $("#prod_select").select2("val",(id!='' && id>0?id:''));
		}
		    
		}
	});
	$("div.overlay").css("display", "none"); 
}

function get_cat_purity()
{
	$(".overlay").css('display','block');
	$('#select_purity option').remove();
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_catalog/category/cat_purity',
		dataType:'json',
		data: {
			'id_category' :$('#select_category').val()
		},
		success:function(data){
		  var id_purity =  $('#id_purity').val();
		   $.each(data, function (key, item) {
			   		$('#select_purity').append(
						$("<option></option>")
						  .attr("value", item.id_purity)
						  .text(item.purity)
					);
			});
			$("#select_purity").select2({
			    placeholder: "Select Purity",
			    allowClear: true
			});
			$("#select_purity").select2("val",(id_purity!='' && id_purity>0?id_purity:''));
			 $(".overlay").css("display", "none");	
		}
	});
}

function validatePolishingReceiptRow(){
	var row_validate = true;
	$('#polishing_receipt > tbody  > tr').each(function(index, tr) {
	    if($(this).find('.id_polishing').is(":checked"))
        {
            if($(this).find('.recd_gross_wt').val()=='' || $(this).find('.recd_gross_wt').val()==0  || $(this).find('.receipt_charges').val()=='' ){
			row_validate = false;
		    }
        }
	});
	return row_validate;
}

$(document).on('change',".id_polishing",function(){
    if($(this).is(":checked"))
    {
        $(this).closest('tr').find('.is_polishing_select').val(1);
    }else{
        $(this).closest('tr').find('.is_polishing_select').val(0);
    }
});

//Polishing receipt


$(document).on('click', ".add_refining_receipt_category", function(e) {
	//$('#category_modal').modal('toggle');
	var curRow=$(this).closest('tr');
	create_new_refining_category_row(curRow);
});

$(document).on('change',".refining_ret_category",function(){
   var curRow=$(this).closest('tr');
   if(this.value!='')
   {    
       id_category=this.value;
       var product = "<option value=''>- Select Product-</option>";
       var purity = "<option value=''>- Select Purity-</option>";
       $.each(prod_details,function(key,items){
           if(items.cat_id==id_category)
           {
            	product += "<option value='"+items.pro_id+"'>"+items.product_name+"</option>";
           }
       });
       
       $.each(purity_details,function(key,items){
           if(items.id_category==id_category)
           {
            	purity += "<option value='"+items.id_purity+"'>"+items.purity+"</option>";
           }
       });
       
       curRow.find('.id_product').html(product);
       curRow.find('.purity').html(purity);
       curRow.find('.id_product').select2();
       curRow.find('.purity').select2();
   }
});



function create_new_refining_category_row(curRow)
{
    $('#refining_category_modal').modal('toggle');
    $('#category_row tbody').empty();
    if(curRow!='' && curRow!=undefined)
    {
       trHtml='';
        console.log(curRow.find('.actual_wt').val());
        $('#active_id').val(curRow.closest('tr').attr('id'));
        var cat_details=curRow.find('.cat_details').val();
        
        
        if(cat_details!='')
        {
            let categories=JSON.parse(cat_details);
            $.each(categories,function(key,items){
                var category = "<option value=''>- Select Category-</option>";
                var section = "<option value=''>- Select Section-</option>";
                var product = "<option value=''>- Select Product-</option>";
                var design = "<option value=''>- Select Design-</option>";
                var sub_design = "<option value=''>- Select Sub Design-</option>";
                var purity = "<option value=''>- Select Purity-</option>";
                $.each(category_details, function (mkey, mitem) {
                     var cat_slelected="";
                      if(items.id_ret_category==mitem.id_ret_category)
                      {
                          cat_slelected="selected";
                      }
                      category += "<option value='"+mitem.id_ret_category+"' "+cat_slelected+" >"+mitem.name+"</option>";
                });	
                
                $.each(prod_details,function(key,pitems){
                   var prod_slelected="";
                   if(pitems.pro_id==items.id_product)
                   {
                    	prod_slelected="selected";
                   }
                   product += "<option value='"+pitems.pro_id+"' "+prod_slelected+">"+pitems.product_name+"</option>";
               });
               
               $.each(section_details, function (mkey, mitem) {
                     var section_slelected="";
                      if(items.id_section==mitem.id_section)
                      {
                          section_slelected="selected";
                      }
                      section += "<option value='"+mitem.id_section+"' "+section_slelected+" >"+mitem.section_name+"</option>";
                });

               $.each(design_details,function(key,pitems){
                var des_slelected="";
                if(pitems.pro_id==items.id_product)
                {
                        if(pitems.design_no==items.id_design)
                        {
                             des_slelected="selected";
                        }
                     design += "<option value='"+pitems.design_no+"' "+des_slelected+" >"+pitems.design_name+"</option>";
                }
            });
            
            $.each(sub_design_details,function(key,pitems){
                 var sub_des_slelected="";
                if(pitems.id_product==items.id_product && pitems.id_design==items.id_design)
                {
                      if(pitems.id_sub_design==items.id_sub_design)
                        {
                             sub_des_slelected="selected";
                        }
                     sub_design += "<option value='"+pitems.id_sub_design+"' "+sub_des_slelected+" >"+pitems.sub_design_name+"</option>";
                }
            });
               
                    $.each(purity_details,function(key,pitems){
                       var purity_slelected="";
                       if(pitems.id_category==items.id_ret_category)
                       {
                           if(pitems.id_purity==items.id_purity)
                           {
                            	purity_slelected="selected";
                           }
                           purity += "<option value='"+pitems.id_purity+"' "+purity_slelected+">"+pitems.purity+"</option>";
                       }
                      
                   });
               
               
           
                trHtml+='<tr>'
                    +'<td><select class="refining_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'
                    +'<td><select class="id_section" name="receipt[id_section][]" value="">'+section+'</select></td>'
                    +'<td><select class="id_product" name="receipt[id_product][]" value="">'+product+'</select></td>'
                    +'<td><select class="id_design" name="receipt[id_design][]" value="">'+design+'</select></td>'
                    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="">'+sub_design+'</select></td>'
                    +'<td><select class="purity" name="receipt[purity][]" value="">'+purity+'</td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="'+items.recd_gross_wt+'" style="width:100px;"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                +'</tr>';
            });
        }
        else
        {
            var section = "<option value=''>- Select Section-</option>";
            var category = "<option value=''>- Select Category-</option>";
            var product = "<option value=''>- Select Product-</option>";
            var design = "<option value=''>- Select Design-</option>";
            var sub_design = "<option value=''>- Select Sub Design-</option>";
            var purity = "<option value=''>- Select Purity-</option>";
             $.each(category_details, function (mkey, mitem) {
                category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
            });	
            $.each(section_details, function (mkey, mitem) {
                  section += "<option value='"+mitem.id_section+"' >"+mitem.section_name+"</option>";
            });
            trHtml+='<tr>'
                    +'<td><select class="refining_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'
                    +'<td><select class="id_section" name="receipt[id_section][]" value="">'+section+'</select></td>'
                    +'<td><select class="id_product" name="receipt[id_product][]" value="">'+product+'</select></td>'
                    +'<td><select class="id_design" name="receipt[id_design][]" value="">'+design+'</select></td>'
                    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="">'+sub_design+'</select></td>'
                    +'<td><select class="purity" name="receipt[purity][]" value="">'+purity+'</td>'
                    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" style="width:100px;"></td>'
                    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                +'</tr>';
        }
        
    	$('#category_row tbody').append(trHtml);
        $('#category_row > tbody > td').find('.id_ret_category').select2();
        $('#category_row > tbody > td').find('.id_section').select2(); 
    }
}

$('#refining_category_modal  #update_refining_category').on('click', function(){
    if(validateRefiningReceiptCategoryRow())
    {
        	var cat_details=[];

    	var recd_gross_wt=0;
    	var catRow=$('#active_id').val();
    	var actual_wt = 	$('#'+catRow).find('.actual_wt').val();
    	
    	$('#refining_category_modal .modal-body #category_row> tbody  > tr').each(function(index, tr) {
    	    recd_gross_wt+=parseFloat($(this).find('.recd_gross_wt').val());
    		cat_details.push({'id_section' : $(this).find('.id_section').val(),'id_ret_category' : $(this).find('.refining_ret_category').val(),'recd_gross_wt' :$(this).find('.recd_gross_wt').val(),'id_product' :$(this).find('.id_product').val(),'id_design' :$(this).find('.id_design').val(),'id_sub_design' :$(this).find('.id_sub_design').val(),'id_purity' :$(this).find('.purity').val(),'purity' :$(this).find('.purity option:selected').text()});
    	});
    	recd_gross_wt = parseFloat(recd_gross_wt).toFixed(3);
    	if(parseFloat(actual_wt)<parseFloat(recd_gross_wt))
        {
            $('#'+catRow).find('.recd_gwt').val(0);
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Weight..'});
        }
        else
        {
            $('#'+catRow).find('.recd_gross_wt').val(parseFloat(recd_gross_wt).toFixed(3));
            $('#'+catRow).find('.received_less_wt').val(parseFloat(parseFloat(actual_wt)-parseFloat(recd_gross_wt)).toFixed(3));
            $('#refining_category_modal').modal('toggle');
    	    $('#'+catRow).find('.cat_details').val(cat_details.length>0 ? JSON.stringify(cat_details):'');
    	    $('#refining_category_modal .modal-body').find('#category_row tbody').empty();
        }
    }
    else{
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill the  required fields..'});
    }
    
	
});

$('#add_new_ref_category').on('click',function(){
    trHtml='';
    var category = "<option value=''>- Select Category-</option>";
    var section = "<option value=''>- Select Section-</option>";
    var product = "<option value=''>- Select Product-</option>";
    var design = "<option value=''>- Select Design-</option>";
    var sub_design = "<option value=''>- Select Sub Design-</option>";
    var purity = "<option value=''>- Select Purity-</option>";
    $.each(category_details, function (mkey, mitem) {
    category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
    });	
    $.each(section_details, function (mkey, mitem) {
          section += "<option value='"+mitem.id_section+"' >"+mitem.section_name+"</option>";
    });
    trHtml+='<tr>'
    +'<td><select class="refining_ret_category" name="receipt[id_ret_category][]" value="">'+category+'</select></td>'
    +'<td><select class="id_section" name="receipt[id_section][]" value="">'+section+'</select></td>'
    +'<td><select class="id_product" name="receipt[id_product][]" value="">'+product+'</select></td>'
    +'<td><select class="id_design" name="receipt[id_design][]" value="">'+design+'</select></td>'
    +'<td><select class="id_sub_design" name="receipt[id_sub_design][]" value="">'+sub_design+'</select></td>'
    +'<td><select class="purity" name="receipt[purity][]" value="">'+purity+'</td>'
    +'<td><input type="number" name="refining_receipt[recd_gwt][]" class="form-control recd_gross_wt" value="" ></td>'
    +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
    +'</tr>';
    $('#refining_category_modal .modal-body #category_row tbody').append(trHtml);
    $('#refining_category_modal .modal-body #category_row > tbody').find('.refining_ret_category').select2();
    $('#refining_category_modal .modal-body #category_row > tbody').find('.id_section').select2();
});

$('#metal_process_search').on('click',function(){
    if($('#select_process').val()=='' || $('#select_process').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Any One Process'});
    }else
    {
        get_metal_process_report();
    }
    
});

$('#select_process,#report_type').on('change',function(){
    var process_for = $("input[name='process[process_for]']:checked").val();//1-Issue,2-Receipt
    $('.old_metal').css("display","none");
    $('.general_category').css("display","none");
    $('.melting').css("display","none");
    var id_process = this.value;
    if(this.value!='')
    {
       set_filter_details();
       if(id_process==2 && process_for==2){
           $('.melting').css("display","block");
       }
    }
});

function set_filter_details()
{
    if($('#select_process').val()==1 && $('#report_type').val()==1)
       {
           $('.old_metal').css("display","block");
           $('.vendor').css("display","none");
       }
       else if($('#select_process').val()==1 && $('#report_type').val()==2)
       {
           $('.general_category').css("display","block");
           $('.vendor').css("display","block");

       }
       else if($('#select_process').val()==2 && ($('#report_type').val()==2 || $('#report_type').val()==1))
       {
           $('.general_category').css("display","block");
       }
       else if($('#select_process').val()==4 && $('#report_type').val()==1)
       {
           $('.old_metal').css("display","block");
       }
       else if($('#select_process').val()==4 && $('#report_type').val()==2)
       {
           $('.general_category').css("display","block");
       }
}


function get_metal_process_report()
{
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/process_report/ajax?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'from_date':$('#rpt_payments1').html(),'to_date':$('#rpt_payments2').html(),'id_karigar':$('#karigar').val(),'id_metal_process':$('#select_process').val(),'process_for':$('#report_type').val(),'id_metal_type':$('#old_metal_type').val(),'id_category':$('#select_category').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    var list=data.list;
		    var access=data.access;
			var oTable = $('#process_details').DataTable();
			oTable.clear().draw();
			
			if (list!= null && list.length > 0)
			{  	
				oTable = $('#process_details').dataTable({
					"bDestroy": true,
					"bDestroy": true,
					"bInfo": true,
					"bFilter": true,
					"bSort": true,
					"order": [[ 0, "desc" ]],
					"dom": 'lBfrtip',
					"buttons": [
							 {
							   extend: 'print',
							   footer: true,
							   title: "Metal Process Report",
							   customize: function ( win ) {
									$(win.document.body).find( 'table' )
										.addClass( 'compact' )
										.css( 'font-size', 'inherit' );
								},
							 },
							 {
								extend:'excel',
								footer: true,
							    title: "Metal Process Report",
							  }
							 ],
					"aaData"  : list,
					"aoColumns": [	
									{ "mDataProp": "id_old_metal_process" },
									{ "mDataProp": "process_name" },
									{ "mDataProp": "process_no" },
									{ "mDataProp": "date_add" },
									{ "mDataProp": "item_name" },
									{ "mDataProp": "karigar_name" },
									{ "mDataProp": "gross_wt" },
									{ "mDataProp": "net_wt" },
									{ "mDataProp": "diawt" },
								  ],
								  "footerCallback": function( row, data, start, end, display )
            						{ 
    									if(list.length>0){
    										var api = this.api(), data;
    
    										for( var i=0; i<=data.length-1;i++){
    
    											var intVal = function ( i ) {
    												return typeof i === 'string' ?
    												i.replace(/[\$,]/g, '')*1 :
    												typeof i === 'number' ?
    												i : 0;
    											};	
    											
    											$(api.column(0).footer() ).html('Total');	
    
    											gross_wgt = api
    											.column(6)
    											.data()
    											.reduce( function (a, b) {
    												return intVal(a) + intVal(b);
    											}, 0 );
    											$(api.column(6).footer()).html(parseFloat(gross_wgt).toFixed(3));
    											
    											net_wgt = api
    											.column(7)
    											.data()
    											.reduce( function (a, b) {
    												return intVal(a) + intVal(b);
    											}, 0 );
    											$(api.column(7).footer()).html(parseFloat(net_wgt).toFixed(3));
    											
    											diawt = api
    											.column(8)
    											.data()
    											.reduce( function (a, b) {
    												return intVal(a) + intVal(b);
    											}, 0 );
    											$(api.column(8).footer()).html(parseFloat(diawt).toFixed(3));
    											
    											
    									} 
    									}else{
    										 var api = this.api(), data; 
    										 $(api.column(6).footer()).html('');
    										 $(api.column(7).footer()).html('');
    									}
        							} 
    							   
				});

			}
			$("div.overlay").css("display", "none"); 
		}
	});
}

$('#metal_process_detailed_search').on('click',function(){
    get_metal_process_detailed_report();
});

function get_metal_process_detailed_report()
{
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url: base_url+"index.php/admin_ret_metal_process/process_report/detail_porcess_report?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'from_date':$('#rpt_payments1').html(),'to_date':$('#rpt_payments2').html(),'id_karigar':$('#karigar').val(),'id_metal_process':$('#select_process').val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    var list=data;
		    
			var oTable = $('#process_details').DataTable();
			oTable.clear().draw();
			
			if (list!= null && list.length > 0)
			{  	
				oTable = $('#process_details').dataTable({
					"bDestroy": true,
					"bDestroy": true,
					"bInfo": true,
					"bFilter": true,
					"bSort": true,
					"order": [[ 0, "desc" ]],
					"aaData"  : list,
					"aoColumns": [	
									{ "mDataProp": "pocket_no" },
									{
                                        "mDataProp": null,
                                        "sClass": "control center", 
                                        "sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
                                    },
									{ "mDataProp": "date_add" },
									{ "mDataProp": "piece" },
									{ "mDataProp": "gross_wt" },
									{ "mDataProp": "net_wt" },
								  ],
    					    
				});
				
				var anOpen =[]; 
                    		$(document).on('click',"#process_details .control", function(){ 
                    		   var nTr = this.parentNode;
                    		   var i = $.inArray( nTr, anOpen );
                    		 
                    		   if ( i === -1 ) { 
                    				$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>'); 
                    				oTable.fnOpen( nTr, fnFormatRowProcessDetails(oTable, nTr), 'details' );
                    				anOpen.push( nTr ); 
                    		    }
                    		    else { 
                    				$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
                    				oTable.fnClose( nTr );
                    				anOpen.splice( i, 1 );
                    		    }
                    		} );

			}
			$("div.overlay").css("display", "none"); 
		}
	});
}


function fnFormatRowProcessDetails( oTable, nTr )
{
  var oData = oTable.fnGetData( nTr );
  var rowDetail = '';
  var prodTable = 
     '<div class="innerDetails">'+
      '<table class="table table-responsive table-bordered text-center table-sm">'+ 
        '<tr class="bg-teal">'+
            '<th colspan="4"></th>'+ 
            '<th colspan="4">ISSUE</th>'+
            '<th colspan="3">RECEIVED</th>'+
        '</tr>'+
        '<tr>'+
            '<th colspan="1"></th>'+ 
            '<th>PROCESS</th>'+ 
            '<th>PROCESS NO</th>'+ 
            '<th>DATE</th>'+ 
            '<th>ITEM</th>'+ 
            '<th>PCS</th>'+
            '<th>GWT</th>'+
            '<th>NWT</th>'+
            '<th>PCS</th>'+
            '<th>GWT</th>'+
            '<th>NWT</th>'+
        '</tr>';
        
    var polish_details = oData.polish_details; 
    var melting_details = oData.melting_details;
    var testing_details = oData.testing_details;
    var s_no = 1;
      $.each(melting_details, function (idx, val) {
      	 if(val.process_no!='')
      	 {
      	     var received_wt = 0;
      	     if(val.recd_details.length > 0)
      	     {
      	         
      	         $.each(val.recd_details,function(k,i){
      	             received_wt+=parseFloat(i.received_wt);
      	         });
      	     }
             print_url=base_url+'index.php/admin_ret_metal_process/process_acknowladgement/'+val.id_old_metal_process;
      	     prodTable += 
            '<tr class="prod_det_btn">'+
            '<td>'+parseFloat(s_no)+'</td>'+
            '<td>'+val.process_name+'</td>'+
            '<td><a href='+print_url+' target="_blank">'+val.process_no+'</a></td>'+
            '<td>'+val.date_add+'</td>'+
            '<td>'+val.metal_type+'</td>'+
            '<td>'+val.issue_pcs+'</td>'+
            '<td>'+val.issue_gwt+'</td>'+
            '<td>'+val.issue_nwt+'</td>'+
            '<td></td>'+
            '<td>'+received_wt+'</td>'+
            '<td>'+received_wt+'</td>'+
            '</tr>';
      	 }
      s_no++;
      });
      
      $.each(polish_details, function (idx, val) {
      	 if(val.process_no!='')
      	 {
      	     print_url=base_url+'index.php/admin_ret_metal_process/process_acknowladgement/'+val.id_old_metal_process;
      	     prodTable += 
            '<tr class="prod_det_btn">'+
            '<td>'+parseFloat(s_no)+'</td>'+
            '<td>'+val.process_name+'</td>'+
            '<td><a href='+print_url+' target="_blank">'+val.process_no+'</a></td>'+
            '<td>'+val.date_add+'</td>'+
            '<td>'+val.metal_type+'</td>'+
            '<td>'+val.issue_pcs+'</td>'+
            '<td>'+val.issue_gwt+'</td>'+
            '<td>'+val.issue_nwt+'</td>'+
            '<td>'+val.received_pcs+'</td>'+
            '<td>'+val.received_gwt+'</td>'+
            '<td>'+val.received_nwt+'</td>'+
            '</tr>';
      	 }
      s_no++;
      }); 
      
       $.each(testing_details, function (idx, val) {
      	 if(val.process_no!='')
      	 {
      	     print_url=base_url+'index.php/admin_ret_metal_process/process_acknowladgement/'+val.id_old_metal_process;
      	     prodTable += 
            '<tr class="prod_det_btn">'+
            '<td>'+parseFloat(s_no)+'</td>'+
            '<td>'+val.process_name+'</td>'+
            '<td><a href='+print_url+' target="_blank">'+val.process_no+'</a></td>'+
            '<td>'+val.date_add+'</td>'+
            '<td></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td>'+val.issue_nwt+'</td>'+
            '<td></td>'+
            '<td></td>'+
            '<td>'+val.received_wt+'</td>'+
            '</tr>';
      	 }
      s_no++;
      });
      
  rowDetail = prodTable+'</table></div>';
  return rowDetail;
}


$('input[type=radio][name="transfer_item_type"]').change(function() {
    $('.tagged_items').css("display","none");
    $('.non_tagged_items').css("display","none");
    $('.old_metal_items').css("display","none");
    $('.stock_type').css("display","block");
    if(this.value == 2){
        $('.tagged_items').css("display","block");
        $('.stock_type').css("display","none");
    }
    else if(this.value == 3){
        $('.non_tagged_items').css("display","block");
        $('.stock_type').css("display","none");
    }
    else{
        $('.old_metal_items').css("display","block");
    }
});


$('#tag_select_all').click(function(event) {
	$("#tag_list tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
});

function get_tag_search_list()
{
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
	$.ajax({
		url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getTagsByFilter?nocache=" + my_Date.getUTCSeconds(),
		type:"POST",
		data:{'tag_no':$('#tag_no').val(),'from_brn':$("#branch_select").val()},
		dataType:"JSON",
		cache:false,
		success:function(data){
		    var list=data;
		    if(list.length==0)
		    {
		        $('#tag_no').val('');
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No records Found..'});
		    }else{
    		    html='';
                rowExist=false;
                if($('#tag_list > tbody >tr').length > 0)
                {
                    $('#tag_list > tbody tr').each(function(bidx, brow){
                        bt_tagid = $(this);
                        // CHECK DUPLICATES - TAG
                        if(bt_tagid.find('.tag_id').val() != '')
                        {
                            if(data[0].tag_id == bt_tagid.find('.tag_id').val()){
                            rowExist = true;
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});
                            } 
                        }
                    });
                }
                
                if(!rowExist)
                {
                    $.each(data, function (key, val) {
                        if(val.id_metal==$('#metal').val())
                        {
                            html += 
                            '<tr>'+
                            '<td><input type="checkbox" name="tag_id[]" class="tag_id" value='+val.tag_id+' checked>'+val.tag_id+'</td>'+
                            '<td><input type="hidden" name="tag_code[]" class="tag_code" value='+val.tag_code+'>'+val.tag_code+'</td>'+
                            '<td>'+val.product+'</td>'+
                            '<td><input type="hidden" name="piece[]" class="piece" value='+val.piece+'>'+val.piece+'</td>'+
                            '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value='+val.gross_wt+'>'+val.gross_wt+'</td>'+
                            '<td><input type="hidden" name="less_wt[]" class="less_wt" value='+val.less_wt+'>'+val.less_wt+'</td>'+
                            '<td><input type="hidden" name="net_wgt[]" class="net_wgt" value='+val.net_wt+'>'+val.net_wt+'</td>'+
                            '<td><input type="hidden" name="dia_wgt[]" class="dia_wgt" value='+val.tag_dia_wt+'>'+val.tag_dia_wt+'</td>'+
                            '<td><input type="hidden" name="purity_value[]" class="purity_value" value='+val.purity_value+'>'+val.purity_value+'</td>'+
                            '<td><input type="hidden" name="tag_purchase_cost[]" class="tag_purchase_cost" value='+val.tag_purchase_cost+'>'+val.tag_purchase_cost+'</td>'+
                            '<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                            '</tr>';
                        }else{
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The Metal..'});
                        }
                        
                    });
                    if($('#tag_list  > tbody > tr').length > 0 )
                    {
                        $('#tag_list > tbody > tr:first').before(html);
                    }
                    else
                    {
                        $('#tag_list > tbody').append(html);
                    }
                    calculate_tag_list();
                    $('#tag_no').val('');
                }
		    }
			$("div.overlay").css("display", "none"); 
		}
	});
}


function calculate_tag_list()
{
    var total_pcs = 0;
    var total_gwt = 0;
    var total_nwt = 0;
    var total_lwt = 0;
    var total_diawt = 0;
    var purity_value = 0;
    var tag_purchase_cost = 0;
    $('#tag_list > tbody > tr').each(function(bidx, brow){
        curRow = $(this);
        console.log(curRow.find('.piece').val());
        total_pcs+= parseFloat(curRow.find('.piece').val());
        total_gwt+= parseFloat(curRow.find('.gross_wt').val());
        total_nwt+= parseFloat(curRow.find('.net_wgt').val());
        total_lwt+= parseFloat(curRow.find('.less_wt').val());
        total_diawt+=parseFloat(curRow.find('.dia_wgt').val());
        purity_value+= parseFloat(curRow.find('.purity_value').val());
        tag_purchase_cost+= parseFloat(curRow.find('.tag_purchase_cost').val());
    });
    console.log('total_pcs:'+total_pcs);
    $('.tag_total_pcs').val(parseFloat(total_pcs));
    $('.tag_total_gross_wt').val(parseFloat(total_gwt).toFixed(3));
    $('.tag_total_net_wt').val(parseFloat(total_nwt).toFixed(3));
    $('.tag_total_lesswt').val(parseFloat(total_lwt).toFixed(3));
    $('.tag_total_dia_wt').val(parseFloat(total_diawt).toFixed(3));
    $('.tag_total_value').val(parseFloat(tag_purchase_cost).toFixed(2));
    $('.tag_total_avg_purity').val(parseFloat(parseFloat(purity_value)/parseFloat($('#tag_list > tbody > tr').length)).toFixed(3));
}

function calculate_non_tag_list()
{
    var total_pcs = 0;
    var total_gwt = 0;
    var total_nwt = 0;
    var total_lwt = 0;
    $('#non_tag_list > tbody > tr').each(function(bidx, brow){
        curRow = $(this);
        console.log(curRow.find('.piece').val());
        total_pcs+= parseFloat(curRow.find('.piece').val());
        total_gwt+= parseFloat(curRow.find('.gross_wt').val());
        total_nwt+= parseFloat(curRow.find('.net_wgt').val());
    });
    console.log('total_pcs:'+total_pcs);
    $('.non_tag_total_pcs').val(parseFloat(total_pcs));
    $('.non_tag_total_gross_wt').val(parseFloat(total_gwt).toFixed(3));
    $('.non_tag_total_net_wt').val(parseFloat(total_nwt).toFixed(3));
}


var prod_details = [];

function get_ActiveProducts()
{
    $('#select_product option').remove();
    $(".overlay").css("display", "block");
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_ret_category':''},
	url: base_url+"index.php/admin_ret_catalog/get_ActiveProducts/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	        	    var id_product = $('#select_product').val();
        	        $.each(data, function (key, item) {  
	                    if(item.stock_type == 2)
        	            {
	                    // add to dropdown
                            $('#select_product').append(
                                $("<option></option>")
                                .attr("value", item.pro_id)    
                                .text(item.product_name)  
                                .attr("data-purmode", item.purchase_mode)
                            );

                            // also push into prod_details only if ctrl_page condition matches
                            if (typeof ctrl_page !== "undefined" && ctrl_page[1] === "metal_process_issue") {
                                prod_details.push(item);
                            }
                                }
                            }); 
                            
                            $('#select_product').select2({
                                placeholder: "Product",
                                allowClear: true
                            });
                    
                            if($('#select_product').length)
                            {
                                $('#select_product').select2("val", (id_product != '' ? id_product : ''));
                            }
	        
        	        $("div.overlay").css("display", "none"); 
		}
	});
}

$('#select_product').on('change',function(){
    $('#select_design option').remove();
    $('#select_sub_design option').remove();
    get_ProductDesign();
    set_availabe_purity();
});


function get_ProductDesign()
{
    $('#select_design option').remove();
	$(".overlay").css('display','block');
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_catalog/get_active_design_products',
		dataType:'json',
		data:{'id_product':$('#select_product').val()},
		success:function(data){
            
            var id =  $('#select_design').val();

            $.each(data, function (key, item) {
                $('#select_design').append(
                $("<option></option>")
                .attr("value", item.design_no)
                .text(item.design_name)
                );
            });
            
            $("#select_design").select2({
                placeholder: "Design",
                allowClear: true
            });
            
            $("#select_design").select2("val",(id!='' && id>0?id:''));
            $(".overlay").css("display", "none");	
		}
	});
}

$('#select_design').on('change',function(){
    get_ActiveSubDesigns();
    set_availabe_purity();
});

function get_ActiveSubDesigns()
{
    $('#select_sub_design option').remove();
	$(".overlay").css('display','block');
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_catalog/get_ActiveSubDesigns',
		dataType:'json',
		data:{'id_product':$('#select_product').val(),'design_no':$('#select_design').val()},
		success:function(data){
            
            	var id =  $('#select_sub_design').val();

            $.each(data, function (key, item) {
                $('#select_sub_design').append(
                $("<option></option>")
                .attr("value", item.id_sub_design)
                .text(item.sub_design_name)
                );
            });
            
            $("#select_sub_design").select2({
                placeholder: "Sub Design",
                allowClear: true
            });
            
            $("#select_sub_design").select2("val",(id!='' && id>0?id:''));
            $(".overlay").css("display", "none");	
		}
	});
}

$('#select_sub_design').on('change',function(){
    set_availabe_purity();
});

function set_availabe_purity()
{
       $.each(available_metal_stock,function (key, item){  
           if($('#select_product').val()==item.id_product && $('#select_design').val()==item.design && $('#select_sub_design').val()==item.id_sub_design)
           {
               if(item.net_wt>0)
               {
                   $('.available_pcs').html(item.no_of_piece);
                   $('.available_weight').html(item.net_wt);
               }
               else
               {
                   $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Stock Not Available.."});
               }
           }
    	});
}

function get_available_metal_stock_details()
{
    my_Date = new Date();
    $("div.overlay").css("display", "block");
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/karigarmetalissue/available_stock_details?nocache=" + my_Date.getUTCSeconds(),
        dataType:"JSON",
        type:"POST",
        data:{'id_product':$('#select_product').val()},
        success:function(data){
            available_metal_stock=data;
            $("div.overlay").css("display", "none"); 
        },
        error:function(error)  
        {
            $("div.overlay").css("display", "none"); 
        }	 
    });
}


$('#issue_pcs').on('change',function(){
    $.each(available_metal_stock,function(key,items){
        if($('#select_product').val()==items.id_product && $('#select_design').val()==items.design && $('#select_sub_design').val()==items.id_sub_design)
        {
            if(parseFloat(items.no_of_piece)<$('#issue_pcs').val())
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Available Pieces is "+items.no_of_piece});
                $('#issue_pcs').val('');
                $('#issue_pcs').focus();
            }
        }
    });
});

$('#issue_weight').on('change',function(){
    $.each(available_metal_stock,function(key,items){
        if($('#select_product').val()==items.id_product && $('#select_design').val()==items.design && $('#select_sub_design').val()==items.id_sub_design)
        {
            if(parseFloat(items.net_wt)<$('#issue_weight').val())
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Available Weight is "+items.gross_wt});
                $('#issue_weight').val('');
                $('#issue_weight').focus();
            }
        }
    });
});



function get_opening_metal_stock_list()

{

    $("div.overlay").css("display", "block"); 

	oldMetalDetail=[];

    my_Date = new Date();

	$.ajax({

		url: base_url+"index.php/admin_ret_metal_process/get_opening_metal_stock_list?nocache=" + my_Date.getUTCSeconds(),

		data:{'id_metal':$('#metal').val()},

		type:"POST",

		dataType:"JSON",

		cache:false,

		success:function(data){

		    var list=data;

			if (list!= null && list.length > 0)

			{  	

			    $('#metal_list > tbody').empty();

			    var trhtml = '';

                $.each(list,function(key,val){

                    trhtml+='<tr>'

                          +'<td><input type="checkbox" class="op_stk_metal_type" name="id_metal_type[]" value="'+val.id_metal_type+'"/>'+val.metal_type+'</td>'

                          +'<td><input type="number" class="form-control piece" placeholder="Enter The Pcs"></td>'

                          +'<td><input type="number" class="form-control op_blc_gross_wt" placeholder="Enter The Weight" value="'+val.blc_weight+'"><input type="hidden" class="available_gwt" value="'+val.blc_weight+'"></td>'

                          +'<td><input type="number" class="form-control op_blc_net_wt" placeholder="Enter The Weight" value="'+val.blc_weight+'" readonly></td>'

                          +'<td></td>'

                          +'<td><input type="number" class="form-control purity_per"></td>'

                          +'<td><input type="number" class="form-control amount"></td>'

                          +'<td></td>'

                          +'</tr>';

                });

                $('#metal_list > tbody').append(trhtml);

				calculate_opening_stock_old_metal();

			}else{

			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});

			}

			$("div.overlay").css("display", "none"); 

		}

	});

}



$(document).on('change','.op_blc_gross_wt,.op_blc_net_wt,.piece,.amount',function(){

    var row = $(this).closest('tr');

    var gross_wt = row.find('.op_blc_gross_wt').val();

    var available_gwt = row.find('.available_gwt').val();

    if(parseFloat(available_gwt) < parseFloat(gross_wt)){

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Available Weight Is '+available_gwt});

        row.find('.op_blc_gross_wt').val(available_gwt);

        row.find('.op_blc_net_wt').val(available_gwt);

    }else{

        row.find('.op_blc_net_wt').val(gross_wt);

        calculate_opening_stock_old_metal();

    }

    

});



$(document).on('keyup','.purity_per',function(){

    var row = $(this).closest('tr');

    var purity_per = row.find('.purity_per').val();

    if(purity_per>100){

        row.find('.purity_per').val(0);

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Purity..'});

    }

});



$(document).on('change',".op_stk_metal_type",function(){

    if($(this).is(":checked"))

    {

        calculate_opening_stock_old_metal();

    }

});




function calculate_opening_stock_old_metal()

{

    var total_pcs = 0;

    var total_gwt = 0;

    var total_nwt = 0;

    var total_amt = 0;

    $('#metal_list > tbody > tr').each(function(bidx, brow){ 

	        curRow = $(this);

	        if(curRow.find(".op_stk_metal_type").is(":checked"))

	        {

	            total_pcs+=parseFloat((curRow.find('.piece').val()!='' ? curRow.find('.piece').val() :0));

    	        total_gwt+=parseFloat((curRow.find('.op_blc_gross_wt').val()!='' ? curRow.find('.op_blc_gross_wt').val() :0));

    	        total_nwt+=parseFloat((curRow.find('.op_blc_net_wt').val()!='' ? curRow.find('.op_blc_net_wt').val() :0));

    	        total_amt+=parseFloat((curRow.find('.amount').val()!='' ? curRow.find('.amount').val() :0));

	        }

	        

	});

   $('.total_pcs').html(parseFloat(total_pcs).toFixed(0));

   $('.total_gross_wt').html(parseFloat(total_gwt).toFixed(3));

   $('.total_net_wt').html(parseFloat(total_nwt).toFixed(3));

   $('.total_amount').html(parseFloat(total_amt).toFixed(3));

}   


/*Calculate Old Metal Value*/

function calculate_OldMetal_value(curRow)
{
    var piece = (isNaN(curRow.find('.piece').val()) ? 0 : parseFloat(curRow.find('.piece').val()));
    var grs_wt = (isNaN(curRow.find('.gross_wt').val())  ? 0 : parseFloat(curRow.find('.gross_wt').val()));
    var purity = (isNaN(curRow.find('.avg_purity').val() ) ? 0 : parseFloat(curRow.find('.avg_purity').val())); 
    var rate_per_grm = (isNaN(curRow.find('.avg_rate_per_gram').val()) ? 0 : parseFloat(curRow.find('.avg_rate_per_gram').val()));

    var total_amt = 0 ;

    total_amt = parseFloat(parseFloat(parseFloat(grs_wt)*parseFloat(purity)/100)*parseFloat(rate_per_grm)).toFixed(2);

    //curRow.find('.net_wt').val(parseFloat(grs_wt).toFixed(3));
    curRow.find('.amount').val(isNaN(total_amt) ? 0 : total_amt);
}

$(document).on('change', ".chg_tax_perc", function(e) {
	//$('#category_modal').modal('toggle');
	var curRow=$(this).closest('tr');
	calculate_charges_tax(curRow);
});

function calculate_charges_tax(curRow){
    var tax_percentage = parseFloat(curRow.find(".chg_tax_perc").val());
    var charge_amt = parseFloat(curRow.find(".refining_receipt_charges").val());

    // var taxAmount = (tax_percentage / 100) * charge_amt;
    var taxableAmount = (charge_amt*(100/(100+tax_percentage)));

    var taxAmount = parseFloat(charge_amt-taxableAmount)

    // Original Cost – (Original Cost * (100 / (100 + GST% ) ) )

    curRow.find('.chg_tax_value').val(parseFloat(taxAmount).toFixed(2));
    

}

function get_chg_tax_type(karigar){

    $.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_metal_process/get_chg_tax_type',
		dataType:'json',
        data: {'karigar':karigar}, 
		success:function(data){

            console.log(data);

            $("#chg_tax_type").val(data);

        }
    });
    
}

function get_karigar_pending_orders()

{

    $("div.overlay").css("display", "block");

    $('#select_po_no option').remove();

    $.ajax({

	type: 'POST',

	data:{'id_karigar':$('#select_karigar').val(),'po_id':$('#po_id').val()},

	url: base_url+'index.php/admin_ret_metal_process/get_repair_pending_order',

	dataType:'json',

	success:function(data){

	    karigar_pending_order_details=data;

	    var id=$('#select_po_no').val();

		$.each(data, function (key, item) {

		    $("#select_po_no").append(

		    $("<option></option>")

		    .attr("value", item.id_customerorder)

		    .text(item.order_no)
            .attr("tag_id",item.tag_id)
            .attr("tag_no",item.tag_code)


		    );

		});

		$("#select_po_no").select2(

		{

			placeholder:"Select PO NO",

			closeOnSelect: true

		});



		if($("#select_po_no").length)

		{

		    $("#select_po_no").select2("val",(id!='' && id>0?id:''));

		}

		    $(".overlay").css("display", "none");

		}

	});

}







$('#select_po_no').on('change',function(){

    is_metal_issue=0;
 
    $('#is_stock_repair_order').val(0);
 
    if(this.value!='')
 
    {
        $('#tag_no').val($(this).find('option:selected').attr('tag_no'));
        get_tag_search_list();
    }
 
 });


// =====================================================
// OLD METAL PnL REPORT
// =====================================================

$('#old_metal_pnl_search').on('click', function(){
    get_old_metal_pnl_report();
});

function get_old_metal_pnl_report()
{
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
    $.ajax({
        url: base_url+"index.php/admin_ret_metal_process/old_metal_pnl_report/ajax?nocache=" + my_Date.getUTCSeconds(),
        type: "POST",
        data: {
            'from_date': $('#rpt_payments1').html(),
            'to_date': $('#rpt_payments2').html()
        },
        dataType: "JSON",
        cache: false,
        success: function(data){
            var list = data.list;
            var access = data.access;

            // Destroy existing DataTable if exists
            if($.fn.DataTable.isDataTable('#old_metal_pnl_table')){
                $('#old_metal_pnl_table').DataTable().clear().destroy();
            }
            $('#old_metal_pnl_table tbody').empty();

            if(list != null && list.length > 0)
            {
                var html = '';
                var total_old_gold_wt = 0;
                var total_expected_pure_wt = 0;
                var total_actual_refined_wt = 0;
                var total_difference = 0;
                var total_purity_sum = 0;
                var purity_count = 0;

                $.each(list, function(idx, row){
                    var sno = idx + 1;
                    var old_gold_wt = parseFloat(row.total_old_gold_wt) || 0;
                    var avg_purity = parseFloat(row.avg_purity_pct) || 0;
                    var expected_pure = parseFloat(row.expected_pure_wt) || 0;
                    var actual_refined = parseFloat(row.actual_refined_pure_wt) || 0;
                    var diff = parseFloat(row.difference) || 0;
                    var status = row.status;

                    total_old_gold_wt += old_gold_wt;
                    total_expected_pure_wt += expected_pure;
                    total_actual_refined_wt += actual_refined;
                    total_difference += diff;
                    total_purity_sum += avg_purity;
                    purity_count++;

                    var statusLabel = '';
                    if(status === 'PROFIT'){
                        statusLabel = '<span class="label label-success">PROFIT</span>';
                    } else if(status === 'LOSS'){
                        statusLabel = '<span class="label label-danger">LOSS</span>';
                    } else if(status === 'PENDING'){
                        statusLabel = '<span class="label label-default">PENDING</span>';
                    } else {
                        statusLabel = '<span class="label label-info">BREAK-EVEN</span>';
                    }

                    html += '<tr>'
                        + '<td>' + sno + '</td>'
                        + '<td>' + row.pocket_no + '</td>'
                        + '<td>' + row.pocket_date + '</td>'
                        + '<td style="text-align:right;">' + old_gold_wt.toFixed(3) + '</td>'
                        + '<td style="text-align:right;">' + avg_purity.toFixed(2) + '</td>'
                        + '<td style="text-align:right;">' + expected_pure.toFixed(3) + '</td>'
                        + '<td style="text-align:right;">' + actual_refined.toFixed(3) + '</td>'
                        + '<td style="text-align:right;">' + diff.toFixed(3) + '</td>'
                        + '<td>' + statusLabel + '</td>'
                        + '</tr>';
                });

                $('#old_metal_pnl_table tbody').html(html);

                // Footer totals
                var avg_purity_val = purity_count > 0 ? (total_purity_sum / purity_count) : 0;
                $('#pnl_total_old_gold_wt').html(total_old_gold_wt.toFixed(3)).css('text-align','right');
                $('#pnl_total_avg_purity').html(avg_purity_val.toFixed(2)).css('text-align','right');
                $('#pnl_total_expected_pure_wt').html(total_expected_pure_wt.toFixed(3)).css('text-align','right');
                $('#pnl_total_actual_refined_wt').html(total_actual_refined_wt.toFixed(3)).css('text-align','right');
                $('#pnl_total_difference').html(total_difference.toFixed(3)).css('text-align','right');

                var netStatusLabel = '';
                if(total_difference > 0){
                    netStatusLabel = '<span class="label label-success">NET PROFIT</span>';
                } else if(total_difference < 0){
                    netStatusLabel = '<span class="label label-danger">NET LOSS</span>';
                } else {
                    netStatusLabel = '<span class="label label-info">BREAK-EVEN</span>';
                }
                $('#pnl_net_status').html(netStatusLabel);

                // Init DataTable
                $('#old_metal_pnl_table').DataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "paging": false,
                    "order": [[ 0, "asc" ]],
                    "dom": 'lBfrtip',
                    "buttons": [
                        {
                            extend: 'print',
                            footer: true,
                            title: "Old Metal PnL Report",
                            customize: function(win){
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size','inherit');
                            }
                        },
                        {
                            extend:'excel',
                            footer: true,
                            title: "Old Metal PnL Report",
                        }
                    ]
                });
            }
            $("div.overlay").css("display", "none"); 
        }
    });
}

// Print handler
$('#old_metal_pnl_print').on('click', function(){
    if($.fn.DataTable.isDataTable('#old_metal_pnl_table')){
        $('#old_metal_pnl_table').DataTable().button('.buttons-print').trigger();
    }
});

// Export handler
$('#old_metal_pnl_export').on('click', function(){
    if($.fn.DataTable.isDataTable('#old_metal_pnl_table')){
        $('#old_metal_pnl_table').DataTable().button('.buttons-excel').trigger();
    }
});