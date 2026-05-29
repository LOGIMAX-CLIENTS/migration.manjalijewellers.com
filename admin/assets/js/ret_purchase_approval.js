var path =  url_params();
var ctrl_page = path.route.split('/');
var categoryDetails  =[];
var karigarOrderDetails=[];
var metalDetails    =[];
var purityDetails   =[];
var stones 			=[];
var stone_types 	=[];
var modalStoneDetail=[];
var uom_details 	= [];
var po_itemdetails 	= [];
var hm_itemdetails 	= [];
var karigar_wastage_details 	= [];
var net_banking_details 	= [];
var sales_details 	= [];
var charges_list = [];
var weight_range_details = [];
var img_resource=[];
var total_files=[];
var available_metal_stock=[];
var karigar_pending_order_details=[];
var karigar_pending_po_details = [];
var karigar_details = [];
var category_lists = [];
var is_metal_issue = 0;
var returnitemlist = [];
var purchasereturnitemlist = [];
var returntaggeditemlist = [];
var nontagreturnitemlist = [];
var insertedcatdetails = [];
var tax_details = [];
var non_tag_category = [];
var return_item_type = 1;
var activeGRNS = [];
var grncatdetails = [];
var ratefix_po_detail = [];
var bank_details=[];
const format = (num, decimals) => num.toLocaleString('en-US', {
   minimumFractionDigits: 3,      
   maximumFractionDigits: 3,
});
$(document).ready(function() {
 
    
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
     
    $('#sbe-dt-btn').daterangepicker(
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
			$('#sbe_date1').text(start.format('YYYY-MM-DD'));
			$('#sbe_date2').text(end.format('YYYY-MM-DD'));		
		    get_purchase_entry(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));            
		}
	);
	
	$('#grn-dt-btn').daterangepicker(
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
			$('#grn_date1').text(start.format('YYYY-MM-DD'));
			$('#grn_date2').text(end.format('YYYY-MM-DD'));		
		    get_grn_entry_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));            
		}
	);
	$('#rf-dt-btn').daterangepicker(
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
			$('#rf_date1').text(start.format('YYYY-MM-DD'));
			$('#rf_date2').text(end.format('YYYY-MM-DD'));		
		    get_sbe_rate_fix_entry_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));            
		}
	);
    
    $('#sp-dt-btn').daterangepicker(
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
			$('#sp_date1').text(start.format('YYYY-MM-DD'));
			$('#sp_date2').text(end.format('YYYY-MM-DD'));		
		    get_purchase_order_payment_list(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));            
		}
	);
        	         
	 var path =  url_params();
	 switch(ctrl_page[1])
	 {
	     case 'approvalstock':
	         switch(ctrl_page[2])
        	 {
        	     case 'add':
        	         get_stones();
        	         get_stone_types();
        	         get_ActiveUOM();
        	         get_ActiveKaigar();
        	         get_ActiveCategories();
        	         get_ActivePurity();
        	         //get_CategoryProducts();
        	         $("#select_purity").select2({
                            placeholder: "Select Purity",
                            allowClear: true
                        });
        	          $("#select_product").select2({
                            placeholder: "Select Product",
                            allowClear: true
                        });
                        $("#select_design").select2({
                            placeholder: "Select Design",
                            allowClear: true
                        });
                        $("#select_sub_design").select2({
                            placeholder: "Select Sub Design",
                            allowClear: true
                        });
                       
                        $("#select_weight_range").select2({
                            placeholder: "Select Weight Range",
                            allowClear: true
                        });
                        $("#select_size").select2({
                            placeholder: "Select Size",
                            allowClear: true
                        });
                        $('.add_stone_details').on('click',function(){
                            openStoneModal();
                        });
                        
                        $(document).on('click', '.create_stone_item_details', function (e) {
                             if(validateStoneCusItemDetailRow()){
                        			create_new_stone_row();
                        		}else{
                        			alert("Please fill required stone fields");
                        		}
                         });
                         
                         calculate_purchase_order_item_total();
                        
                        
                        $('.smith_due_dt').datepicker({ dateFormat: 'yyyy-mm-dd'});
                        
        	     break;
        	     case 'purchase_add':
        	         get_ActiveGRNS();
        	         get_ActiveKaigar();
        	         get_ActiveMetals();
        	         get_ActivePurity();
        	         get_ActiveCategories();
        	         get_stones();
        	         get_stone_types();
        	         get_ActiveUOM();
        	         get_taxgroup_items();
        	         get_karigar_wise_wastage();
        	         get_charges();
        	         	$('#despatch_through').select2({
                    	    placeholder: "Dispatch Through",
                    	    allowClear: true
                    	});
                    	$("#select_metal").select2({
                            placeholder: "Select Metal",
                            allowClear: true
                        });
                        $("#select_category").select2({
                            placeholder: "Select Category",
                            allowClear: true
                        });
                        $("#select_product").select2({
                            placeholder: "Select Product",
                            allowClear: true
                        });
                        $("#select_design").select2({
                            placeholder: "Select Design",
                            allowClear: true
                        });
                        $("#select_sub_design").select2({
                            placeholder: "Select Sub Design",
                            allowClear: true
                        });
                        $("#select_purity").select2({
                            placeholder: "Select Purity",
                            allowClear: true
                        });
                        
                        $("#select_po_no").select2({			    
                    	 	placeholder: "Select PO No",			    
                    	 	allowClear: true		    
                     	});	
                        
                        $(document).keyup(function(e) {
                            if(e.keyCode == 9) {    // TAB KEY
                                if($('#other_metal_charges').is(':focus')){
                                    open_other_metal_modal();
                                } 
                                else  if($('#tot_lwt').is(':focus')){
                                    openStoneModal();
                                }
                            }
                        });
                        
                        $('.add_stone_details').on('click',function(){
                            openStoneModal();
                        });
                        
                        $(document).on('click', '.create_stone_item_details', function (e) {
                             if(validateStoneCusItemDetailRow()){
                        			create_new_stone_row();
                        		}else{
                        			alert("Please fill required stone fields");
                        		}
                         });
                         
                         calculate_purchase_order_item_total();
                         
                         $('#tab_items').on('click',function(){
                             if($('#select_grn').val()=='' || $('#select_grn').val()==null)
                             {
                                 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select GRN No."});
                             }
                         });
        
        	     break;
        	     case 'pur_order':
        	                var date = new Date();
                            var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 29, 1); 
                            var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());
                            var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
                            $('#rpt_from_date').html(from_date);
                            $('#rpt_to_date').html(to_date);
                            $('#rpt_date_picker').html(from_date + ' - ' + to_date);
                            get_pur_order_Details();
                            $('#rpt_date_picker').daterangepicker(
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
                            $('#rpt_date_picker').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
                            $('#rpt_from_date').text(start.format('DD-MM-YYYY'));
                            $('#rpt_to_date').text(end.format('DD-MM-YYYY'));		            
                            }
                            ); 
        	         get_purchase_order_list();
        	     break;
        	     case 'order_status':
        	            get_ActiveCategories();
        	           $("#select_pur_ord_no").select2(
                		{
                			placeholder:"Select PO NO",
                			closeOnSelect: true		    
                		});
                		
                		$("#report_type").select2(
                		{
                			placeholder:"Select Type",
                			closeOnSelect: true		    
                		});
                		
                		$("#select_category").select2({
                            placeholder: "Select Category",
                            allowClear: true
                        });
                        
                        $("#select_product").select2({
                            placeholder: "Select Product",
                            allowClear: true
                        });
                        
                        $("#select_design").select2({
                            placeholder: "Select Design",
                            allowClear: true
                        });
                        $("#select_sub_design").select2({
                            placeholder: "Select Sub Design",
                            allowClear: true
                        });
                        
                        
        	            
        	            if(ctrl_page[2]=='order_status')
        	            {
        	                get_ActiveKaigar();
        	                
        	                var date = new Date();
                            var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 29, 1); 
                            var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());
                            var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
                            $('#rpt_from_date').html(from_date);
                            $('#rpt_to_date').html(to_date);
                            $('#rpt_date_picker').html(from_date + ' - ' + to_date);
                            get_pur_order_Details();
                            $('#rpt_date_picker').daterangepicker(
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
                            $('#rpt_date_picker').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
                            $('#rpt_from_date').text(start.format('DD-MM-YYYY'));
                            $('#rpt_to_date').text(end.format('DD-MM-YYYY'));		            
                            }
                            ); 
            
        	            }
        	     break;
        	     
        	     case 'order_delivery':
        	         get_ActiveKaigar();
        	         get_CategoryProducts();
        	     break;
        	     
        	     case 'list':
        	         var date = new Date();
                        var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 6, 1); 
                        var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
                        var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
                        $('#sbe_date1').empty();
                        $('#sbe_date2').empty();
			 			get_purchase_entry(from_date,to_date);
        	        
        	     break;
        	     
        	     case 'qc_status':
        	         get_qc_status_details();
        	     break;
        	 }
	     break;
	     
	     case 'grnentry':
	         switch(ctrl_page[2])
	         {
	             case 'add':
	                 get_cat_details();
        	         get_ActiveKaigar();
        	         get_stones();
        	         get_stone_types();
        	         get_ActiveUOM();
        	         get_charges();
        	         get_taxgroup_items();
        	         get_ActiveMetals();
        	         get_ActivePurity();
        	         get_ActiveCategories();
        	         get_country();
        	     break;
        	     case 'edit':
	                 get_cat_details();
        	         get_ActiveKaigar();
        	         get_stones();
        	         get_stone_types();
        	         get_ActiveUOM();
        	         get_charges();
        	         get_taxgroup_items();
        	         get_ActiveMetals();
        	         get_ActivePurity();
        	         get_ActiveCategories();
        	         calculate_grnItem_details();
        	     break;
        	     case 'list':
        	         var date = new Date();
                        var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 6, 1); 
                        var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
                        var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
                        $('#grn_date1').empty();
                        $('#grn_date2').empty();
			 			get_grn_entry_list(from_date,to_date);
	             break;
	         }
	     break;
	     
	     case 'rate_fixing':
	         switch(ctrl_page[2])
	         {
	             case 'add':
	                
        	         get_ActiveKaigar();
        	        $("#select_po_ref_no").select2(
            		{
            			placeholder:"Select PO NO",
            			closeOnSelect: true		    
            		});
        	     break;
        	     case 'list':
        	         var date = new Date();
                            var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 6, 1); 
                            var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
                            var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
                            $('#rf_date1').empty();
                            $('#rf_date2').empty();
				 			get_sbe_rate_fix_entry_list(from_date,to_date);
	             break;
	         }
	     break;
	     
	     case 'approvalstock':
	         switch(ctrl_page[2])
	         {
	             case 'list':
        	         get_approval_purchase_entry();
        	     break;
	         }
	     break;
	     
	     case 'qc_issue_receipt':
	         switch(ctrl_page[2])
        	 {
        	     case 'add':
        	         get_qc_issue_purchase_orders();
        	         get_ActiveEmployee();
        	     break;
        	     case 'list':
        	         get_qc_issue_details();
        	     break;
        	     case 'qc_entry':
        	         get_qc_receipt_purchase_orders();
        	     break;
        	 }
	     break;
	     case 'halmarking_issue_receipt':
	         switch(ctrl_page[2])
        	 {
        	     case 'add':
        	         get_pending_halmarking_items();
        	         get_ActiveKaigar();
        	     break;
        	     case 'list':
        	         get_halmarking_issue_details();
        	     break;
        	     case 'hm_receipt':
        	          get_halmarking_issue_orders();
        	     break;
        	 }
	     break;
	     case 'purchase_payment':
	         
	         $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
              var target = $(e.target).attr("href") // activated tab
              if(target == '#tot_summary'){
                  $(".received_amount").focus();
              }
            });
                        
	         switch(ctrl_page[2])
        	 {
        	     
        	     case 'list':
        	         get_purchase_order_payment_list();
        	     break;
        	     case 'add':
        	          get_ActiveKaigar();
        	     break;
        	 }
	     break;
	     case 'supplier_po_payment':
	         
	         $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
              var target = $(e.target).attr("href") // activated tab
              if(target == '#tot_summary'){
                  $(".received_amount").focus();
              }
            });
                        
	         switch(ctrl_page[2])
        	 {
        	     
        	     case 'list':
        	          var date = new Date();
                        var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 6, 1); 
                        var from_date =  firstDay.getFullYear()+'-'+(firstDay.getMonth()+1)+'-'+firstDay.getDate();
                        var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
                        $('#rf_date1').empty();
                        $('#rf_date2').empty();
			 			get_purchase_order_payment_list(from_date,to_date);
        	     break;
        	     case 'add':
        	          get_ActiveKaigar();
        	          get_bank_details();
        	     break;
        	 }
	     break;
	     case 'karigarmetalissue':
	                 
	         switch(ctrl_page[2])
        	 {
        	     
        	     case 'list':
        	         get_karigar_metal_issue_list();
        	     break;
        	     case 'add':
        	          get_ActiveMetals();
        	          get_ActiveKaigar();
        	          get_ActiveCategories();
        	          get_available_metal_stock_details();
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
                	  $('#select_purity').select2({
                	    placeholder: "Purity",
                	    allowClear: true
                	  });
        	     break;
        	 }
	     break;
	      case 'purchasereturn':
			switch(ctrl_page[2])
			{
				case 'list':
        	        get_returned_po_details();
				break;
				case 'add':
					get_ActiveKaigar();
					get_ActiveCategories();
					get_ActivePurity();
					get_return_item_po_ref_nos();
					get_stones();
        	        get_stone_types();
        	        get_ActiveUOM();
        	        get_charges();
        	        get_taxgroup_items();
        	        get_non_tag_category();
        	        get_available_metal_stock_details();
        	        $(document).on('keypress',"input[type='number']",function (event){
        	             if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&
                    	  ((event.which < 48 || event.which > 57) &&
                    		(event.which != 0 && event.which != 8))) {
                    	  event.preventDefault();
                    	}
        	         });
        	         $('#select_design').select2({
                	    placeholder: "Design",
                	    allowClear: true
                	  });
                	   $('#select_sub_design').select2({
                	    placeholder: "Sub Design",
                	    allowClear: true
                	  });
				break;
			}
		    break;
		    
		    
		    case 'order_description':
	         if($('#order_des').length > 0)
             {
             	CKEDITOR.replace('order_des');
             }
             get_order_description();
	     break;
	     
	 }

});


    function report_print(branch_name,report_name,from_date,to_date,optional)
    {
        if(branch_name==''){
        branch_name = "ALL"
        }
        var data = "<span>"+report_name+" - "+branch_name+"</span></br>"
        +"<span>"+(optional!='' ? optional: '' )+"</span>"
        +(from_date!='' && to_date != '' ? "<span>FROM&nbsp;:&nbsp;"+from_date+" &nbsp;&nbsp;TO&nbsp;&nbsp; "+to_date+ "</span></br> " : '')
        + $('.hidden-xs').html()+" &nbsp; - &nbsp;"+ "</span>"+"<span style='font-size:11pt;'>"+getDisplayDateTime()  +"</span></br>";
        return data;
    }


    function date_format(dt_range)
    {
    	var date = dt_range.split('/');
    	return date[2] + '-' + date[1] + '-' + date[0];
    }

    function formatMoney(number) {
      return number.toLocaleString('en-IN', { style: 'currency', currency: 'USD' });
    }

   function getDisplayDateTime()
   {
        var today = new Date();
        var dispdate = today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear();
        var disptime = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        return dispdate + " " + disptime;
    }

    function get_karigar_wise_wastage()
    {
        $.ajax({
    	type: 'GET',
    	url: base_url+'index.php/admin_ret_catalog/get_karigar_wise_wastage',
    	dataType:'json',
    	success:function(data){
    	        karigar_wastage_details=data;
    		}
    	});
    }
    
    $('#select_all').click(function(event) {
    	  $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
          event.stopPropagation();
    });

$(document).on('change', '.return_item_cat_id', function(event) {
    var row     = $(this).parent().closest('tr');
    var catId   = $(row).find('.return_item_cat_id').val();
    var karId   = $(row).find('.return_item_kar_id').val();
    
    if($(this).prop('checked')){
        $('#item_detail > tbody tr').each(function(bidx, brow){
		    curRow = $(this);
			if(curRow.find('.po_kar_id').val() == karId && curRow.find('.po_cat_id').val() == catId)
			{
				 curRow.find('input[type="checkbox"]').prop('checked', true);
			} 
	   });
    }else{
        $('#item_detail > tbody tr').each(function(bidx, brow){
		    curRow = $(this);
			if(curRow.find('.po_kar_id').val() == karId && curRow.find('.po_cat_id').val() == catId)
			{
				curRow.find('input[type="checkbox"]').prop('checked', false);
			} 
	   });
    }
    get_purchase_return_total();
    event.stopPropagation();
});

function get_ActiveCategory()
{
    $.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_catalog/category/active_category',
	dataType:'json',
	success:function(data){
	        categoryDetails = data;
		}
	});
}




function get_ActiveKaigar()
{
	$.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_catalog/karigar/active_list',
	dataType:'json',
	success:function(data){
	    var id=$('#select_karigar').val();
	    var id_karigar= $('#id_karigar').val();
	    console.log('id:'+id);
	    karigar_details = data;
		$.each(data, function (key, item) {   
		    $("#select_karigar").append(
		    $("<option></option>")
		    .attr("value", item.id_karigar) 
		    .attr("data-karigartpe", item.karigartpe)
		    .text(item.karigar)  
		    );
		}); 
		$("#select_karigar").select2(
		{
			placeholder:"Select Karigar",
			closeOnSelect: true		    
		});
		
		if($("#select_karigar").length)
		{
		    $("#select_karigar").select2("val",(id!='' && id>0? id:(id_karigar!='' && id_karigar!=undefined ? id_karigar :'')));
		}
		    $(".overlay").css("display", "none");
		}
	});
}

$("input[name='order[order_for]']:radio").on('change',function(){
    $('#item_detail tbody').empty();
    $('.stock_order').css("display","none");
    $('.customer_order').css("display","none");
    $('.stock_and_cus_ord').css("display","block");
    $('.stock_repair').css("display","none");
    if(this.value==1)
    {
        $('#select_order_no').prop('disabled',true);
        $('.stock_order').css("display","block");
        $('.customer_order').css("display","none");
    }
    else if(this.value==2)
    {
        get_ProductDesign();
        get_customer_order_pending_details(); 
        $('#select_order_no').prop('disabled',false);
        $('.stock_order').css("display","none");
        $('.customer_order').css("display","block");
    }
    else if(this.value==3)
    {
        $('.stock_and_cus_ord').css("display","none");
        $('.stock_repair').css("display","block");
        $('#select_order_no').prop('disabled',false);
        get_stock_repair_order_details(); 
    }
});

function get_customer_order_pending_details()
{
    $("#select_order_no option").remove();
    $.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_purchase/get_customer_order_pending_details',
	dataType:'json',
	success:function(data){
	    cus_order_details=data;
	    var id=$('#select_order_no').val();
		$.each(data, function (key, item) {   
		    $("#select_order_no").append(
		    $("<option></option>")
		    .attr("value", item.id_customerorder)    
		    .text(item.order_no)  
		    );
		}); 
		$("#select_order_no").select2(
		{
			placeholder:"Select Order No",
			 allowClear: true	    
		});
		
		if($("#select_order_no").length)
		{
		    $("#select_order_no").select2("val",(id!='' && id>0?id:''));
		}
		    $(".overlay").css("display", "none");
		}
	});
}


$('#branch_select').on('change',function(){
    if(this.value!='')
    {
        get_stock_repair_order_details();
    }
});

function get_stock_repair_order_details()
{
    $("#select_order_no option").remove();
    $.ajax({
	type: 'POST',
	url: base_url+'index.php/admin_ret_purchase/get_stock_repair_order_details',
	data:{'id_branch':$('#branch_select').val()},
	dataType:'json',
	success:function(data){
	    cus_order_details=data;
	    var id=$('#select_order_no').val();
		$.each(data, function (key, item) {   
		    $("#select_order_no").append(
		    $("<option></option>")
		    .attr("value", item.id_customerorder)    
		    .text(item.order_no)  
		    );
		}); 
		$("#select_order_no").select2(
		{
			placeholder:"Select Order No",
			 allowClear: true	    
		});
		
		if($("#select_order_no").length)
		{
		    $("#select_order_no").select2("val",(id!='' && id>0?id:''));
		}
		    $(".overlay").css("display", "none");
		}
	});
}


$('#select_order_no').on('change',function(){
    
    var order_for = $("input[name='order[order_for]']:checked").val();
    let id_order=this.value;
    if(this.value!='')
    {
        $('#item_detail tbody').empty();
       
        $("#select_product option").remove();
        $.each(cus_order_details,function(key,items){
            if(items.id_customerorder==id_order)
            {
                $.each(items.item_details,function(key,val){
                        $('#select_product').append(
                        $("<option></option>")
                        .attr("value", val.id_product)    
                        .text(val.product_name)  
                        .attr("data-purmode", val.purchase_mode)
                        );
                });
                
                $("#select_product").select2({
                    placeholder: "Select Product",
                    allowClear: true
                });
                
                $("#select_product").select2("val","");
            }
        });
    }
    
    if(ctrl_page[1]=='purchase' && ctrl_page[2]=='add' && this.value!='')
    {
        let order_search=true;
        if(order_for==3)
        {
            if($('#branch_select').val()=='' || $('#branch_select').val()==null)
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Branch"});
                order_search=false;
                 $("#select_order_no").select2("val","");
            }
        }
        if(order_search)
        {
            var trHtml='';
            $.each(cus_order_details,function(key,val){
                if(val.id_customerorder==id_order)
                {
                    $.each(val.order_details,function(k,items){
                            trHtml+='<tr>'
                            +'<td><input type="hidden" name="order_details[id_orderdetails][]" value="'+items.id_orderdetails+'"><input type="hidden" value="" name="order_details[order_images][]" /><input type="hidden" class="id_customer_order"  name="order_details[id_customer_order][]" value="'+$('#select_order_no').val()+'"><input type="hidden" class="id_product"  name="order_details[product][]" value="'+items.id_product+'">'+items.product_name+'</td>'
                            +'<td><input type="hidden" class="design"  name="order_details[design][]" value="'+items.design_no+'">'+items.design_name+'</td>'
                            +'<td><input type="hidden" class="sub_design"  name="order_details[sub_design][]" value="'+items.id_sub_design+'">'+items.sub_design_name+'</td>'
                            +'<td><input type="hidden" class="size"  name="order_details[size][]" value="'+items.id_size+'">'+items.size+'</td>'
                            +'<td><input type="hidden" class="weight_range"  name="order_details[weight_range][]" value="" ></td>'
                            +'<td><input type="hidden" class="approx_wt"  name="order_details[order_wt][]" value="'+items.weight+'" >'+items.weight+'</td>'
                            +'<td><input type="hidden" class="piece"  name="order_details[piece][]" value="'+items.totalitems+'" >'+items.totalitems+'</td>'
                            +'<td><input type="hidden" class="description"  name="order_details[description][]" value="" ><input type="hidden" class="due_date"  name="order_details[due_date][]" value="'+$('.smith_due_dt').val()+'" >'+$(".smith_due_dt").val()+'</td>'
                             +'<td><a href="#" class="btn btn-danger btn-del btn-xs" style="padding:5px;" onClick="remove_purchase_order_row($(this).closest(\'tr\'));"><i class="fa fa-trash"></i></a></td>'
                            +'</tr>';
                    });
                }
            });
            
            if($('#item_detail > tbody  > tr').length>0)
            {
                $('#item_detail > tbody > tr:first').before(trHtml);
            }else{
                $('#item_detail tbody').append(trHtml);
            }
            calculate_order_item_details();
            reset_purchase_order_form();
        }
        
    }
    
});


function remove_purchase_order_row(curRow)
{
    curRow.remove();
    calculate_order_item_details();
}

$("#add_order_item").on('click',function(){
    var order_for = $("input[name='order[order_for]']:checked").val();
    
	if($('#select_karigar').val() == null || $('#select_karigar').val() == '')   
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Karigar"});
	}
	else if($('#select_product').val() == null || $('#select_product').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Product"});
	}
	else if($('#select_design').val() == null || $('#select_design').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Design"});
	}
	else if($('#select_sub_design').val() == null || $('#select_sub_design').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Sub Design"});
	}
	else if(($('#select_weight_range').val() == null || $('#select_weight_range').val() == '') && (order_for==1))
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Weight Range"});
	}
	else if(($('#order_weight').val() == 0 || $('#order_weight').val() == '') && (order_for==2))
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Weight"});
	}
	else if($('#tot_pcs').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Pcs"});
	}
	else if($('.smith_due_dt').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Due Date"});
	}
	else if(order_for==2 && ($('#select_order_no').val() == null || $('#select_order_no').val() == ''))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Customer Order No"});
    }
	else
	{       
	    set_purchase_order_preview();    
	}
});

function set_purchase_order_preview()
{
    var images=$('#order_iamges').val();
    var trHtml='';
    var approx_wt=0;
    var order_for = $("input[name='order[order_for]']:checked").val();
    
    if(order_for==1)
    {
        $.each(weight_range_details,function(key,items){
            if(items.id_weight==$('#select_weight_range').val())
            {
                approx_wt=parseFloat(items.value*$("#tot_pcs").val());
            }
        });
    }
    else
    {
        approx_wt=$('#order_weight').val();
    }
    
    trHtml+='<tr>'
                +'<td><input type="hidden" value='+images+' name="order_details[order_images][]" /><input type="hidden" class="id_customer_order"  name="order_details[id_customer_order][]" value="'+$('#select_order_no').val()+'"><input type="hidden" class="id_product"  name="order_details[product][]" value="'+$('#select_product').val()+'">'+$("#select_product option:selected").text()+'</td>'
                +'<td><input type="hidden" class="design"  name="order_details[design][]" value="'+$('#select_design').val()+'">'+$("#select_design option:selected").text()+'</td>'
                +'<td><input type="hidden" class="sub_design"  name="order_details[sub_design][]" value="'+$('#select_sub_design').val()+'">'+$("#select_sub_design option:selected").text()+'</td>'
                +'<td><input type="hidden" class="size"  name="order_details[size][]" value="'+$('#select_size').val()+'">'+$("#select_size option:selected").text()+'</td>'
                +'<td><input type="hidden" class="weight_range"  name="order_details[weight_range][]" value="'+$('#select_weight_range').val()+'" >'+$("#select_weight_range option:selected").text()+'</td>'
                +'<td><input type="hidden" class="approx_wt" name="order_details[order_wt][]" value="'+approx_wt+'">'+parseFloat(approx_wt).toFixed(2)+'</td>'
                +'<td><input type="hidden" class="piece"  name="order_details[piece][]" value="'+$('#tot_pcs').val()+'" >'+$("#tot_pcs").val()+'</td>'
                +'<td><input type="hidden" class="description"  name="order_details[description][]" value="'+$('#remark').val()+'" ><input type="hidden" class="due_date"  name="order_details[due_date][]" value="'+$('.smith_due_dt').val()+'" >'+$(".smith_due_dt").val()+'</td>'
                 +'<td><a href="#" class="btn btn-danger btn-del btn-xs" style="padding:5px;" onClick="remove_row($(this).closest(\'tr\'));"><i class="fa fa-trash"></i></a></td>'
            +'</tr>';
    if($('#item_detail > tbody  > tr').length>0)
    {
        $('#item_detail > tbody > tr:first').before(trHtml);
    }else{
        $('#item_detail tbody').append(trHtml);
    }
    calculate_order_item_details();
    reset_purchase_order_form();
}

function calculate_order_item_details()
{
    var total_pcs=0;
    var total_wt=0;
    var trHtml='';
    $('#item_detail > tbody  > tr').each(function(index, tr) {
        total_pcs+=parseFloat($(this).find('.piece').val());
        total_wt+=parseFloat($(this).find('.approx_wt').val());
    });
    $('.tot_pcs').html(total_pcs);
    $('.tot_wt').html(parseFloat(total_wt).toFixed(3));
    $('.order_pcs').val(total_pcs);
    $('.order_wt').val(parseFloat(total_wt).toFixed(3));
}

$('.smith_due_dt').on('change',function(){
        var order_date=dateToTimeStamp($('.smith_due_dt').val());
        var current_date =Date.now();
        if(current_date>order_date)
        {
            $('.smith_due_dt').val('');
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Valid Date..'});
        }
});

function dateToTimeStamp(date)
{
    new_date=date.split("/");
    new_date = new_date[1]+"/"+new_date[0]+"/"+new_date[2];
    time_stamp=new Date(new_date).getTime();
    return time_stamp;
}

function reset_purchase_order_form()
{
    $('#select_size').select2("val","");
    $('#select_weight_range').select2("val","");
    $('#tot_pcs').val('');
    $('#remark').val('');
}

function create_new_empty_pur_order_row()
{
    var category='<option value="">Select Category</option>';
    $.each(categoryDetails, function (mkey, mitem) {
		category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
	});
	
    var trHtml='';
    trHtml+='<tr>'
                +'<td><select class="form-control select_category" name="order_details[category][]" value="">'+category+'</td>'
                +'<td><select class="form-control select_product" name="order_details[product][]"></td>'
                +'<td><select class="form-control select_design" name="order_details[design][]"></td>'
                +'<td><select class="form-control select_sub_design" name="order_details[sub_design][]"></td>'
                +'<td><select class="form-control select_size" name="order_details[size][]"></td>'
                +'<td><select class="form-control select_weight_range" name="order_details[weight_range][]"></td>'
                +'<td><input type="number" class="form-control piece" name="order_details[piece][]"></td>'
                +'<td><input class="form-control datemask date smith_due_dt" name="order_details[due_date][]" data-date-format="dd-mm-yyyy" type="text"/></td>'
                +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
            '</tr>';
            
    $('#item_detail tbody').append(trHtml);
    $('#item_detail > tbody').find('.select_category').select2();
    $('#item_detail > tbody').find('.select_product').select2();
    $('#item_detail > tbody').find('.select_design').select2();
    $('#item_detail > tbody').find('.select_sub_design').select2();
    $('#item_detail > tbody').find('.select_size').select2();
    $('#item_detail > tbody').find('.select_weight_range').select2();
    
	$('#item_detail > tbody').find('.select_category').select2({
	    placeholder: "Category",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_product').select2({
	    placeholder: "Product",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_design').select2({
	    placeholder: "Design",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_sub_design').select2({
	    placeholder: "Sub Design",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_size').select2({
	    placeholder: "Sub Design",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_weight_range').select2({
	    placeholder: "Select Weight Range",
	    allowClear: true
	});
	

	
	$('.smith_due_dt').datepicker({ dateFormat: 'yyyy-mm-dd'});
	
}

$(document).on('change',".select_category",function(){
     
    var row = $(this).closest('tr');
    row.find('.select_product option').remove();
    row.find('.select_product').select2("val","");
    if(this.value!='')
    {
        get_ActiveProduct(this.value,row);
    }else{
         get_ActiveProduct(this.value,row);
    }
});



function get_ActiveProduct(id_ret_category,curRow)
{
    
    $(".overlay").css("display", "block");
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_ret_category':id_ret_category},
	url: base_url+"index.php/admin_ret_catalog/get_ActiveProducts/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	        	$.each(data, function (key, item) {   
        		   curRow.find('.select_product').append(
        		    $("<option></option>")
        		    .attr("value", item.pro_id)    
        		    .text(item.product_name)  
        		    .attr("data-purmode", item.purchase_mode)
        		    );
        		}); 
	         $(".overlay").css("display", "none");
		}
	});
}


$(document).on('change',".select_product",function(){
     
    var row = $(this).closest('tr');
    row.find('.select_size option').remove();
    row.find('.select_design option').remove();
    row.find('.select_weight_range option').remove();
    row.find('.select_design').select2("val","");
    row.find('.select_size').select2("val","");
    row.find('.select_weight_range').select2("val","");
    if(this.value!='')
    {
        get_ActiveDesigns(this.value,row);
        get_ActiveSize(this.value,row);
        get_ActiveWeightRange(this.value,row);
    }
    var pur_type = $('#select_product option:selected').attr('data-purmode');
    if(pur_type == 1){
        $('#item_cost').prop('readonly',false);
    }else{
        $('#item_cost').prop('readonly',true);
    }
});


function get_ActiveWeightRange()
{
    $(".overlay").css("display", "block");
    $('#select_weight_range option').remove();
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_product':$('#select_product').val()},
	url: base_url+"index.php/admin_ret_purchase/get_ActiveWeightRange/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	    
	            weight_range_details=data;
	    
	        	$.each(data, function (key, item) {   
        		   $('#select_weight_range').append(
        		    $("<option></option>")
        		    .attr("value", item.id_weight)    
		            .text(item.name)  
        		    );
        		}); 
        		
        		$("#select_weight_range").select2(
        		{
        			placeholder:"Select Weight Range",
        			allowClear: true   
        		});
        		
        		if($("#select_weight_range").length)
        		{
        		    $("#select_weight_range").select2("val",'');
        		}
		
		
	         $(".overlay").css("display", "none");
		}
	});
}

function get_ActiveSize()
{
    $(".overlay").css("display", "block");
    $('#select_size option').remove();
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_product':$('#select_product').val()},
	url: base_url+"index.php/admin_ret_tagging/get_ActiveSize/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	        	$.each(data, function (key, item) {   
        		   $('#select_size').append(
        		    $("<option></option>")
        		    .attr("value", item.id_size)    
		            .text(item.value+'-'+item.name)  
        		    );
        		}); 
	         $(".overlay").css("display", "none");
		}
	});
}


function get_ActiveDesigns(id_product,curRow)
{
    $(".overlay").css("display", "block");
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_product':id_product},
	url: base_url+"index.php/admin_ret_catalog/get_active_design_products/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	        	$.each(data, function (key, item) {   
        		   curRow.find('.select_design').append(
        		    $("<option></option>")
        		    .attr("value", item.design_no)    
        		    .text(item.design_name)  
        		    );
        		}); 
	         $(".overlay").css("display", "none");
		}
	});
}




$(document).on('change',".select_design",function(){
     
    var row = $(this).closest('tr');
    row.find('.select_sub_design option').remove();
    row.find('.select_sub_design').select2("val","");
    if(this.value!='')
    {
        get_ActiveSubDesigns(this.value,row);
    }
});



function get_ActiveSubDesigns(id_product,curRow)
{
    $(".overlay").css("display", "block");
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_product':id_product},
	url: base_url+"index.php/admin_ret_catalog/get_ActiveSubDesigns/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	        	$.each(data, function (key, item) {   
        		   curRow.find('.select_sub_design').append(
        		    $("<option></option>")
        		    .attr("value", item.id_sub_design)    
        		    .text(item.sub_design_name)  
        		    );
        		}); 
	         $(".overlay").css("display", "none");
		}
	});
}


function validateOrderDetailRow()
{
    var validate = true;
	$('#item_detail > tbody  > tr').each(function(index, tr) {
		if($(this).find('.select_product').val() == "" || $(this).find('.select_design').val() == "" || $(this).find('.select_sub_design').val() == "" || $(this).find('.select_size').val() == ""  || $(this).find('.select_weight_range').val() == "" || $(this).find('.piece').val() == "" ){
			validate = false;
		}
	});
	return validate;
}


$("#create_order").on('click',function(e) {
	e.preventDefault();
	create_customer_order();
});

function create_customer_order()
{
    var order_for = $("input[name='order[order_for]']:checked").val();
    if($('#select_karigar').val() == null || $('#select_karigar').val() == '')   
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Karigar"});
	}
	else if($('.smith_due_dt').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Due Date"});
	}
	else if((order_for==2 || order_for==3) && ($('#select_order_no').val() == null || $('#select_order_no').val() == ''))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Order No"});
    }
    else if($('#item_detail > tbody  > tr').length==0)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
    }
    else
    {
        $("div.overlay").css("display", "block"); 
    		$('#create_stock_order').prop('disabled',true);
    		var form_data=$('#order_submit').serialize();
    			var url=base_url+ "index.php/admin_ret_purchase/purchase/save?nocache=" + my_Date.getUTCSeconds();
    		    $.ajax({ 
    		        url:url,
    		        data: form_data,
    		        type:"POST",
    		        dataType:"JSON",
    		        success:function(data){
    		            if(data.status)
    		            {
    		                location.href=base_url+'index.php/admin_ret_purchase/purchase/pur_order';
    		                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.msg});
    		                $("div.overlay").css("display", "none"); 
    		            }else
    		            {
    		                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.msg});
    		                $("div.overlay").css("display", "none"); 
    		            }
    					
    		        },
    		        error:function(error)  
    		        {	
    		            $("div.overlay").css("display", "none"); 
    		        } 
    		    });
    		$('#create_stock_order').prop('disabled',false);
    }
}

$('#pur_ord_search').on('click',function(){
    get_purchase_order_list();
});


function get_purchase_order_list()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_ret_purchase/purchase/purchase_order?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 type:"POST",
			 data:{'from_date':$('#rpt_from_date').html(),'to_date':$('#rpt_to_date').html()},
			 success:function(data){
			 	var list=data.list;
				var oTable = $('#order_list').DataTable();
				oTable.clear().draw();				  
				if (list!= null && list.length > 0)
				{  	
					oTable = $('#order_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "order": [[ 0, "desc" ]],
                    "dom": 'lBfrtip',
                    "buttons" : ['excel','print'],
                    "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
                    "aaData": list,
                    "aoColumns": [
                    { "mDataProp": function ( row, type, val, meta ){
                        if(row.order_status<=3)
                        {
                            return '<input type="checkbox" class="id_customerorder" name="id_customerorder[]" value="'+row.id_customerorder+'"/>'+row.id_customerorder; 
                        }else{
                            return row.id_customerorder;
                        }
        			},
        			},
                    { "mDataProp": "pur_no" },
                    { "mDataProp": "order_date" },
                    { "mDataProp": function ( row, type, val, meta ){
                        return '<span class="badge bg-'+row.color+'">'+row.order_status_msg+'</span>';
        			},
        			},
                    { "mDataProp": "karigar_name" },
                    { "mDataProp": "mobile" },
                    { "mDataProp": "order_for" },
                    { "mDataProp": "order_pcs" },

                    { "mDataProp": function ( row, type, val, meta ) {
                    return parseFloat(row.order_approx_wt).toFixed(3);
                    }
                    },
                    
                    { "mDataProp": function ( row, type, val, meta ) {
                    return parseFloat(row.delivered_qty);
                    }
                    },
                    
                    { "mDataProp": function ( row, type, val, meta ) {
                    return parseFloat(row.delivered_wt).toFixed(3);
                    }
                    },
                    
                    { "mDataProp": "order_no" },
                    { "mDataProp": "cus_order_branch" },
                    
                    
                    { "mDataProp": function ( row, type, val, meta ) {
                    id= row.id_customerorder;
                    print_url=base_url+'index.php/admin_ret_purchase/get_karigar_acknowladgement/'+id;
                    action_content='<a href="#" onclick="send_karigar_sms('+id+','+row.mobile+')"  class="btn btn-success" data-toggle="tooltip" title="Send WhatsApp / Email"><i class="fa fa-whatsapp" aria-hidden="true"></i><a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Vendor acknowledgement "><i class="fa fa-print" ></i></a>';
                    return action_content;
                    }
                    },
                    
                    ]
                    });
				
				}
				$("div.overlay").css("display", "none"); 
			  },
			  error:function(error)  
			  {
				 $("div.overlay").css("display", "none"); 
			  }	 
	      });
}


function send_karigar_sms(id_customer_order,mobile)
{
    if(mobile!='')
    {
         my_Date = new Date();
            $("div.overlay").css("display", "block"); 
            $.ajax({
                url:base_url+ "index.php/admin_ret_purchase/send_karigar_sms?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
                data:  {'mobile':mobile,'id_customer_order':id_customer_order},
                type:"POST",
                async:false,
                dataType:'json',
                success:function(data){
                        if(data.status)
                        {
                            $('#order_delviery').prop('disabled',false);
                            $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                            $("div.overlay").css("display", "none"); 
                            window.location.reload();
                        }else
                        {
                            $('#order_delviery').prop('disabled',false);
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                        }
                        
                },
                error:function(error)  
                {
                $("div.overlay").css("display", "none"); 
                }	 
            });
    }
    else
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Mobile Number Not Found"});
    }
}

//PUrchase Entry


/*$('#select_po_no').on('change',function(){
   if(this.value!='')
   {
        var id_customerorder=this.value;
        $.each(karigar_pending_order_details,function(key,items){
            if(items.id_customerorder==id_customerorder)
            {
                if(items.ref_order_type==3) // Customer Repair
                {
                    $('#is_cus_repair_order').val(1);
                    $('#select_design').prop('disabled',true);
                    $('#select_sub_design').prop('disabled',true);
                    $('.cus_repair').css('display','block');
                    $('.oranmentspo').css('display','none');
                    $('#tot_wastage_perc').prop('readonly',false);
                }
                else
                {
                    $('#select_design').prop('disabled',false);
                    $('#select_sub_design').prop('disabled',false);
                    $('#is_cus_repair_order').val(0);
                    $('.cus_repair').css('display','none');
                    $('.oranmentspo').css('display','block');
                    $('#tot_wastage_perc').prop('readonly',true);
                }
            }
        });
   }
});*/


$('#select_po_no').on('change',function(){
   is_metal_issue=0;
   if(this.value!='')
   {
        var id_customerorder = this.value;
        var ordered_categories = [];
        var selectable_categories = [];
        var order_details = [];
        var selected_order_details = [];
        $.each(karigar_pending_order_details, function(key,items){
            if(items.id_customerorder == id_customerorder)
            {
                if(items.ref_order_type == 3) // Customer Repair
                {
                    is_metal_issue=1;
                    $('#is_cus_repair_order').val(1);
                    $('#select_design').prop('disabled',true);
                    $('#select_sub_design').prop('disabled',true);
                    $('.cus_repair').css('display','block');
                    $('.oranmentspo').css('display','none');
                    //$('#tot_wastage_perc').prop('readonly',false);
                }
                else
                {
                    $('#select_design').prop('disabled',false);
                    $('#select_sub_design').prop('disabled',false);
                    $('#is_cus_repair_order').val(0);
                    $('.cus_repair').css('display','none');
                    $('.oranmentspo').css('display','block');
                    //$('#tot_wastage_perc').prop('readonly',true);
                }
                var totalitems = 0;
                var weight = 0;
                var receivedpcs = 0;
                var receivedwt = 0;
                $.each(items.order_details, function(okey, oitems){
                    totalitems+=parseFloat(oitems.totalitems);
                    weight+=parseFloat(oitems.weight);
                    receivedpcs+=parseFloat(oitems.receivedpcs);
                    receivedwt+=parseFloat(oitems.receivedwt);
                    ordered_categories.push({'catid' : oitems.cat_id});
                });
                selected_order_details = items;
                order_details = items.order_details;
                
                $("#orderedpcs").html(parseFloat(totalitems).toFixed(0));
                $("#orderedwt").html(parseFloat(weight).toFixed(3));
                $("#receivedpcs").html(parseFloat(receivedpcs).toFixed(0));
                $("#receivedwt").html(parseFloat(receivedwt).toFixed(3));
            }
        });
        
        /*var uniqueSites = ordered_categories.filter(function(item, i, ordered_categories) {

            return i == ordered_categories.indexOf(item);
    
        });*/
        
        const key = 'catid';

        const orderarrayUniqueByKey = [...new Map(ordered_categories.map(item =>
          [item[key], item])).values()];
        
        //ordered_categories.map(item => item.catId).filter((value, index, self) => self.indexOf(value) === index)
        
        //Update respected order category into category list to avoid loading all the category
        //$("#select_category").empty();
        
        $('#select_category option').remove();
         $.each(orderarrayUniqueByKey, function(okey, oval){
            $.each(category_lists, function(ckey, cval){
                if(oval.catid == cval.id_ret_category){
                    selectable_categories.push({"catId" : oval.catid, "catName" : cval.name,"cat_type" : cval.cat_type});
                }
            });
         });
        
        
        $.each(selectable_categories, function (key, item) { 
            $('#select_category').append(
            $("<option></option>")
            .attr("value", item.catId)    
            .attr("data-cattype", item.cat_type) 
            .text(item.catName)  
            );
    	});
    	
    	$('#select_category').select2({
    	    placeholder: "Select Category",
    	    allowClear: true
    	});
    	
    	
    	if($('#select_category').length)
        {
            $('#select_category').select2("val", '');
        }
        
        
    $('#cus_orderdetailsModal').modal('show');
    $('#order_items_details tbody').empty();

    var trHtml='';
    $.each(order_details,function(key,i){
        trHtml+='<tr>'
                    +'<td>'+i.catname+'</td>'
                    +'<td>'+i.product_name+'</td>'
                    +'<td>'+i.design_name+'</td>'
                    +'<td>'+i.sub_design_name+'</td>'
                    +'<td>'+i.totalitems+'</td>'
                    +'<td>'+i.weight_description+'</td>'
                    +'<td>'+i.receivedpcs+'</td>'
                    +'<td>'+i.receivedwt+'</td>'
                +'</tr>';
    });
    $('#order_items_details tbody').append(trHtml);
   }
});


function get_karigar_pending_orders()
{
    $("div.overlay").css("display", "block"); 
    $('#select_po_no option').remove();
    $.ajax({
	type: 'POST',
	data:{'id_karigar':$('#select_karigar').val()},
	url: base_url+'index.php/admin_ret_purchase/get_karigar_pending_order_details',
	dataType:'json',
	success:function(data){
	    karigar_pending_order_details=data;
	    var id=$('#select_po_no').val();
		$.each(data, function (key, item) {   
		    $("#select_po_no").append(
		    $("<option></option>")
		    .attr("value", item.id_customerorder)    
		    .text(item.pur_no)  
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

$("input[name='billing[bill_type]']:radio").on('change',function(){
    $('.balance_amount').val(0);
    $('.receive_amount').val(0);
    calculatePaymentCost();
});

function get_supplier_pay_details(sId)
{
    $("div.overlay").css("display", "block"); 
    $('.balance_amount').val(0);
    $('.receive_amount').val(0);
    $.ajax({
        type: 'POST',
        data:{'id_karigar':$('#select_karigar').val()},
        url: base_url+'index.php/admin_ret_purchase/supplier_po_payment/get_supplier_pay_details',
        dataType:'json',
        success:function(data){
            if (data.balance_amount > 0)
            {  	
                $('.balance_amount').val(data.balance_amount);
                $('.receive_amount').val(data.balance_amount);
            }else
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Not Found"});
            }
            $(".overlay").css("display", "none");
            calculatePaymentCost();
        }
    });
}

$('.receive_amount').on('keyup',function(){
    var bill_type = $("input[name='billing[bill_type]']:checked").val();
    if(bill_type ==1)
    {
        var balance_amount = ($('.balance_amount').val()!='' ? $('.balance_amount').val():0);
        var receive_amount = ($('.receive_amount').val()!='' ? $('.receive_amount').val():0);
        if(parseFloat(balance_amount) < parseFloat(receive_amount))
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Received Amount is Grater Than The Out Standing Amount.."});
            $('.receive_amount').val(0);
        }
    }
    calculatePaymentCost();
});



function get_payhistory_by_supid(sId){
    
    $("div.overlay").css("display", "block"); 
   
    $.ajax({
    	type: 'POST',
    	data:{'id_karigar':$('#select_karigar').val()},
    	url: base_url+'index.php/admin_ret_purchase/supplier_po_payment/paymenthistory',
    	dataType:'json',
    	success:function(data){
    	    console.log(data);
    	    
    	var oTable = $('#pay_history').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.payhistory.length > 0)
			 {  	
				oTable = $('#pay_history').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "asc" ]],
						"aLengthMenu": [
                            [25, 50, 100, 200, -1],
                            [25, 50, 100, 200, "All"]
                        ],
                        "iDisplayLength": -1,
						"dom": 'lBfrtip',
						"aaData"  : data.payhistory,
						"aoColumns": [	{ "mDataProp":function ( row, type, val, meta ){ 
											return '<span class="">'+row.billtype+'</span>';
										}},   
										{ "mDataProp":function ( row, type, val, meta ){
											return '<span class="">'+row.pay_refno+'</span>';
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<span class="">'+row.pay_date+'</span>';
										}},  
										{ "mDataProp":function ( row, type, val, meta ){
											return '<span class="">'+row.po_ref_no+'</span>';
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<span class="">'+row.tot_cash_pay+'</span>';
										}}
									]
					});			  	 	
				} 
    		$(".overlay").css("display", "none");
    	}
    });
}

    $('.selectAllPO').click(function(event) {
		$("#po_pay_details tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
		event.stopPropagation();  
		calculateSelectedPosCost();
	});
	


    $(document).on('change', '.po_item_pay', function() {
        
        calculateSelectedPosCost();
       
    });
    
    
    function calculateSelectedPosCost(){
        var pototalcost     = 0;
        var popaidcost      = 0;
        var popayablecost   = 0;
        $('#po_pay_details tbody').find('tr').each(function () {
            var row = $(this);
            if (row.find('input[type="checkbox"]').is(':checked')) {
                pototalcost     += parseFloat(row.find('.item_cost').html());
                popaidcost      += parseFloat(row.find('.paidamt').html());
                popayablecost   += parseFloat(row.find('.balanceamt').html());
                row.find('.curpayable').val(parseFloat(row.find('.balanceamt').html()));
            }
        });
        
        $('.payable_amt').html(parseFloat(pototalcost).toFixed(2));
        $('.paid_amt').html(parseFloat(popaidcost).toFixed(2));
        $('.balance_amt').html(parseFloat(parseFloat(pototalcost) - parseFloat(popaidcost)).toFixed(2));
        $('.received_amount').val(parseFloat(popayablecost).toFixed(2));
        calculate_received_amount();
        
    }

$("input[name='order[purchase_type]']:radio").on('change',function(){
    $('#item_detail > tbody').empty();
    $('#select_category').select2("val","");
    $('#select_po_no').select2("val","");
    $('#approval_stock_yes').prop('disabled',false);
    $('#approval_stock_no').prop('disabled',false);
    if(this.value==2)
    {
        $('#select_po_no').prop('disabled',true);
    }else if(this.value==1)
    {
        if($('#select_karigar').val()!='' && $('#select_karigar').val()!=null)
        {
            get_karigar_pending_orders(); 
        }
        
        $('#select_po_no').prop('disabled',false);
        $('#approval_stock_yes').prop('disabled',true);
        $('#approval_stock_no').prop('disabled',true);
        $('#approval_stock_no').prop('checked',true);
    }
    
   var po_type = $("input[type='radio'][name='order[po_type]']:checked").val(); 
    
    if(po_type==1)
    {
        $('.oranments').css('display','block');
        $('.bullion_purchase').css('display','none');
        $('.stone_purchase').css('display','none');
        $('.oranments-bullion').css('display','block');
    }
    else if(po_type ==2)
    {
        $('.bullion_purchase').css('display','block');
        $('.oranments').css('display','none');
        $('.stone_purchase').css('display','none');
        $('.aganist_order').prop('disabled',true);
        $('.oranments-bullion').css('display','block');
    }else if(po_type == 3){ // For stone purchase
        $('.stone_purchase').css('display','block');
        $('.bullion_purchase').css('display','none');
        $('.oranments-bullion').css('display','none');
        $('.oranments').css('display','none');
        $('.aganist_order').prop('disabled',true);
        $('#select_purity').select2("val","");
        $('#select_purity').prop('disabled',true);
        
        var uom_list = "<option value=''> - UOM - </option>";
        $.each(uom_details, function (pkey, pitem) {
    		uom_list += "<option value='"+pitem.uom_id+"'>"+pitem.uom_name+"</option>";
    	});
    	//$('.stone_uom_id').append(uom_list);
    	$('.stone_uom_id')
            .find('option')
            .remove()
            .end()
            .append(uom_list);
        
    }
    get_ActiveCategories();
    
    
    
});



$('.stone_uom_id').on('change',function(){
    if(this.value != ''){
        $(".call_type_label").html("(" + $('.stone_uom_id :selected').text() + ")");
    }
});
    
$("input[name='cal_type']:radio").on('change', function(){
    if(this.value==1){
        $(".call_type_label").html("(" + $('.stone_uom_id :selected').text() + ")" );
    }else{
        $(".call_type_label").html("(" + "Per Pc" + ")");
    }
});

$("input[name='order[po_type]']:radio").on('change',function(){
    $('#item_detail > tbody').empty();
    $('.oranments').css('display','none');
    $('.bullion_purchase').css('display','none');
    $('#select_po_no').select2("val","");
    $('.aganist_order').prop('disabled',false);
    if(this.value==1)
    {
        $('.oranments').css('display','block');
        $('.bullion_purchase').css('display','none');
        $('.stone_purchase').css('display','none');
        $('.oranments-bullion').css('display','block');
    }
    else if(this.value==2)
    {
        $('.bullion_purchase').css('display','block');
        $('.oranments').css('display','none');
        $('.stone_purchase').css('display','none');
        $('.aganist_order').prop('disabled',true);
        $('.oranments-bullion').css('display','block');
    }else if(this.value == 3){ // For stone purchase
        $('.stone_purchase').css('display','block');
        $('.bullion_purchase').css('display','none');
        $('.oranments-bullion').css('display','none');
        $('.oranments').css('display','none');
        $('.aganist_order').prop('disabled',true);
        $('#select_purity').select2("val","");
        $('#select_purity').prop('disabled',true);
        
        var uom_list = "<option value=''> - UOM - </option>";
        $.each(uom_details, function (pkey, pitem) {
    		uom_list += "<option value='"+pitem.uom_id+"'>"+pitem.uom_name+"</option>";
    	});
    	//$('.stone_uom_id').append(uom_list);
    	$('.stone_uom_id')
            .find('option')
            .remove()
            .end()
            .append(uom_list);
        
    }
    get_ActiveCategories();
});


$("#add_pur_entry_item").on('click',function(){
	if($('#select_karigar').val() == null || $('#select_karigar').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Karigar"});
	}
	else
	{       if(validatePurOrderDetailRow())
        	{
        	    create_new_empty_pur_order_entry_row();
        	}
        	else{
        	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Fill The Required Fields.."});
            }
	        
	}
});


function get_KarigarOrders()
{
    $.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_purchase/get_KarigarOrders',
	dataType:'json',
	success:function(data){
	        karigarOrderDetails=data;
		}
	});
}


function validatePurOrderDetailRow()
{
    var validate = true;
	$('#item_detail > tbody  > tr').each(function(index, tr) {
		if($(this).find('.select_product').val() == "" || $(this).find('.select_design').val() == "" || $(this).find('.select_sub_design').val() == ""  || $(this).find('.piece').val() == "" || $(this).find('.weight').val() == "" ){
			validate = false;
		}
	});
	return validate;
}


function create_new_empty_pur_order_entry_row()
{
   var trHtml='';
    trHtml+='<tr>'
                +'<td></td>'
                +'<td><select class="form-control select_product" name="order_details[product][]"></td>'
                +'<td><select class="form-control select_design" name="order_details[design][]"></td>'
                +'<td><select class="form-control select_sub_design" name="order_details[sub_design][]"></td>'
                +'<td><select class="form-control select_weight_range" name="order_details[weight][]"></td>'
                +'<td><input type="number" class="form-control piece" name="order_details[piece][]"></td>'
                +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
            '</tr>';
            
    $('#item_detail tbody').append(trHtml);
    $('#item_detail > tbody').find('.select_category').select2();
    $('#item_detail > tbody').find('.select_product').select2();
    $('#item_detail > tbody').find('.select_design').select2();
    $('#item_detail > tbody').find('.select_sub_design').select2();
   
	$('#item_detail > tbody').find('.select_category').select2({
	    placeholder: "Category",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_product').select2({
	    placeholder: "Product",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_design').select2({
	    placeholder: "Design",
	    allowClear: true
	});
	
	$('#item_detail > tbody').find('.select_sub_design').select2({
	    placeholder: "Sub Design",
	    allowClear: true
	});
	
}



/*function set_KarigarPendingOrders()
{
    
    $.ajax({
	type: 'POST',
	url: base_url+'index.php/admin_ret_purchase/get_KarigarOrders',
	dataType:'json',
	data:{'id_karigar':$('#select_karigar').val()},
	success:function(data){
	         if(data.length>0)
               {
                   var trHtml='';
                   $.each(data,function(key,items){
                       trHtml+='<tr>'
                            +'<td>'+items.pur_no+'<input type="hidden" class="id_orderdetails" value="'+items.id_orderdetails+'"></td>'
                            +'<td>'+items.product_name+'</td>'
                            +'<td>'+items.design_name+'</td>'
                            +'<td>'+items.sub_design_name+'</td>'
                            +'<td><input type="number" class="form-control piece" name="order_details[piece][]"></td>'
                            +'<td><input type="number" class="form-control weight" name="order_details[weight][]"></td>'
                            +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                        '</tr>';
                   });
               } 
             $('#item_detail tbody').append(trHtml);
		}
	});
	
   
}*/

//PUrchase Entry




//Purchase Order Status
$('#ordet_status_search').on('click',function(){
    get_pur_order_Details();
});

function get_pur_order_Details()
{
    var company_name    = $('#company_name').val();
    var from_date = $('#rpt_from_date').html();
    var to_date = $('#rpt_to_date').html();
    var branch_name = '';
    var report_name = "PUCHASE ORDER REPORT";
    var optional = ($('#select_karigar').val()!='' && $('#select_karigar').val()!=null ? $('#select_karigar option:selected').text() :'');
    var title="<div style='text-align: center;'><b><span style='font-size:15pt;'>"+company_name+"</span></b><b><span style='font-size:12pt;'></span></b></br>"
    +"<span>"+report_print(branch_name,report_name,from_date,to_date,optional)+"</span>";
    $("div.overlay").css("display","block");
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/get_pur_order_Details?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 data:{'from_date':$('#rpt_from_date').html(),'to_date':$('#rpt_to_date').html(),'id_karigar':$('#select_karigar').val(),'date_group_by':$('#date_group_by').val(),'report_type':$('#report_type').val(),'id_product':$('#select_product').val(),'id_design':$('#select_design').val(),'id_sub_design':$('#select_sub_design').val()},
	 type:"POST",
	 success:function(data){
	 	var list=data;
		var oTable = $('#order_details_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#order_details_list').dataTable({
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
			   title: '',
			   messageTop: title,
			   	customize: function ( win ) {
				$(win.document.body).find( 'table' )
				.addClass( 'compact' )
				.css( 'font-size', '10px' );
				},
			 },
			 {
				extend:'excel',
				footer: true,
			    title: 'Purchase Order Report', 
			  }
			 ],
            "aaData": list,
            "aoColumns": [
            { "mDataProp": function ( row, type, val, meta ){
			    var url = base_url+'index.php/admin_ret_purchase/get_karigar_acknowladgement/'+row.id_customerorder;
				return '<a href='+url+' target="_blank">'+row.pur_no+'</a>';
			},
			},
			
		
			
			{ "mDataProp": "po_ref_no" },
			
			{ "mDataProp": function ( row, type, val, meta ){
			    var url = base_url+'index.php/admin_ret_order/customer_order_acknowladgement/'+row.cusOrdid;
				return '<a href='+url+' target="_blank">'+row.cus_ord_ref+'</a>';
			},
			},
            { "mDataProp": "karigar_name" },
            { "mDataProp": "orderdate" },
            { "mDataProp": "product_name" },
            { "mDataProp": "design_name" },
            { "mDataProp": "sub_design_name" },
            { "mDataProp": "orderpcs" },
            { "mDataProp": "received_pcs" },
            ],
            "footerCallback": function( row, data, start, end, display )
			{ 
				if(data.length>0){
					var api = this.api(), data;
					for( var i=0; i<=data.length-1;i++){
						var intVal = function ( i ) {
							return typeof i === 'string' ?
							i.replace(/[\$,]/g, '')*1 :
							typeof i === 'number' ?
							i : 0;
						};	
						
						$(api.column(0).footer() ).html('Total');	
						orderpcs = api
						.column(8)
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );
						$(api.column(8).footer()).html(parseFloat(orderpcs));
						
						deliveredpcs = api
						.column(9)
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );
						$(api.column(9).footer()).html(parseFloat(deliveredpcs));
						
				} 
				}else{
					 var api = this.api(), data; 
					 $(api.column(8).footer()).html('');
					 $(api.column(9).footer()).html('');
				}
			} 
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}
//Purchase Order Status

//Order cancel or close

$('#order_close').on('click',function(){
    $('#order_close').prop('disabled',true);
    var req_data=[];
     if($("input[name='id_customerorder[]']:checked").val())
     {
        var selected = [];
        var approve=false;
        $("#order_list tbody tr").each(function(index, value)
        {
            if($(value).find("input[name='id_customerorder[]']:checked").is(":checked"))
            {
                transData = { 
                'id_customerorder'   : $(value).find(".id_customerorder").val(),
                }
                selected.push(transData);	
            }
        });
        req_data = selected;
        update_order_close(req_data);
     }else
     {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Any One Items."});
         $('#order_delviery').prop('disabled',false);
     }
});


function update_order_close(req_data)
{
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/update_order_close?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'req_data':req_data},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
                if(data.status)
                {
                    $('#order_close').prop('disabled',false);
                    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                    $("div.overlay").css("display", "none"); 
                    window.location.reload();
                }else
                {
                    $('#order_close').prop('disabled',false);
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                }
                
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}


$('#order_cancel').on('click',function(){
    $('#order_close').prop('disabled',true);
    var req_data=[];
     if($("input[name='id_customerorder[]']:checked").val())
     {
        var selected = [];
        var approve=false;
        $("#order_list tbody tr").each(function(index, value)
        {
            if($(value).find("input[name='id_customerorder[]']:checked").is(":checked"))
            {
                transData = { 
                'id_customerorder'   : $(value).find(".id_customerorder").val(),
                }
                selected.push(transData);	
            }
        });
        req_data = selected;
        update_order_cancel(req_data);
     }else
     {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Any One Items."});
         $('#order_delviery').prop('disabled',false);
     }
});


function update_order_cancel(req_data)
{
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/update_order_cancel?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'req_data':req_data},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
                if(data.status)
                {
                    $('#order_cancel').prop('disabled',false);
                    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                    $("div.overlay").css("display", "none"); 
                    window.location.reload();
                }else
                {
                    $('#order_cancel').prop('disabled',false);
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                }
                
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}

//Order cancel or close



//Purchase Order Delivery





$('#search_pur_order').on('click',function(){
    if($('#select_karigar').val() == null || $('#select_karigar').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Karigar"});
	}
	else if($('#select_pur_ord_no').val() == null || $('#select_pur_ord_no').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select PO REF No"});
	}
	else
	{
	    get_karigar_pending_order_details();
	}
});

function get_karigar_pending_order_details()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/get_karigar_pending_order_details?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 data:{'id_customerorder':$('#select_pur_ord_no').val()},
	 type:"POST",
	 success:function(data){
	 	if(data.length>0)
	 	{
	 	    set_order_item_details(data);
	 	}
	 	else
	 	{
	 	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
	 	}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}

function set_order_item_details(data)
{
    $('#item_detail tbody').empty();
    var trHtml='';
    $.each(data,function(key,items){
        trHtml+='<tr>'
                +'<td><input type="checkbox" class="id_orderdetails" name="id_orderdetails[]" value="'+items.id_orderdetails+'"/>'+items.pur_no+'</td>'
                +'<td>'+items.product_name+'</td>'
                +'<td>'+items.design_name+'</td>'
                +'<td>'+items.sub_design_name+'</td>'
                +'<td>'+items.size+'</td>'
                +'<td>'+items.weight_range+'</td>'
                +'<td><input type="hidden" class="form-control tot_delivered_pcs" value="'+items.delivered_pcs+'"><input type="hidden" class="form-control order_pcs" value="'+items.tot_items+'"><input type="hidden" class="form-control id_joborder" value="'+items.id_joborder+'">'+items.tot_items+'</td>'
                +'<td>'+items.delivered_pcs+'</td>'
                +'<td><input type="number" class="form-control delivered_pcs"></td>'
                +'<td><input type="number" class="form-control delivered_wt"></td>'
                +'</tr>';
    });
    if($('#item_detail > tbody  > tr').length>0)
    {
        $('#item_detail > tbody > tr:first').before(trHtml);
    }else{
        $('#item_detail tbody').append(trHtml);
    }
}


$('#order_delviery').on('click',function(){
    $('#order_delviery').prop('disabled',true);
    var req_data=[];
     if($("input[name='id_orderdetails[]']:checked").val())
     {
        var selected = [];
        var approve=false;
        $("#item_detail tbody tr").each(function(index, value)
        {
            if($(value).find("input[name='id_orderdetails[]']:checked").is(":checked"))
            {
                transData = { 
                'id_orderdetails'   : $(value).find(".id_orderdetails").val(),
                'tot_delivered_pcs' : $(value).find(".tot_delivered_pcs").val(),
                'delivered_pcs'     : $(value).find(".delivered_pcs").val(),
                'delivered_wt'      : $(value).find(".delivered_wt").val(),
                'order_pcs'         : $(value).find(".order_pcs").val(),
                'id_joborder'       : $(value).find(".id_joborder").val(),
                }
                selected.push(transData);	
            }
        });
        req_data = selected;
        update_order_delivery(req_data);
     }else
     {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Any One Items."});
         $('#order_delviery').prop('disabled',false);
     }
});


function update_order_delivery(req_data)
{
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/update_order_delivery?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'req_data':req_data},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
                if(data.status)
                {
                    $('#order_delviery').prop('disabled',false);
                    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                    $("div.overlay").css("display", "none"); 
                    window.location.reload();
                }else
                {
                    $('#order_delviery').prop('disabled',false);
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                }
                
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}

//Purchase Order Delivery


//Purchase Entry

function get_purchase_entry(from_date,to_date)
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase_approval/approvalstock/ajax_approval_list?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 type:"POST",
	 data:{'from_date':from_date,'to_date':to_date},
	 success:function(data){
	 	var access=data.access;
	 	var list=data.list;
		var oTable = $('#approval_entry_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#approval_entry_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [[ 0, "desc" ]],
            "dom": 'lBfrtip',
            "buttons" : ['excel','print'],
            "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            "aaData": list,
            "aoColumns": [
            { "mDataProp": "po_ref_no" },
            { "mDataProp": "po_date" },
            { "mDataProp": "karigar" },
            { "mDataProp": "category_name" },
            { "mDataProp": "tot_pcs" },
            { "mDataProp": "gross_wt" },
            { "mDataProp": "tot_lwt" },
            { "mDataProp": "tot_nwt" },
            { "mDataProp": "tot_purchase_amt" },
            { "mDataProp": "tot_purchase_wt" },
            { "mDataProp": function ( row, type, val, meta ) {
					 id= row.po_id;
					 let print_url= '';


                    //  edit_url=(access.edit=='1' ? '<a href="'+base_url+'index.php/admin_ret_purchase/supplier_po_payment/edit/'+id+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>' :'');

                    //  delete_url=(access.delete=='1' ? '<button onclick="confirm_bill_cancel('+id+')" class=""> <i class="fa fa-close" ></i></button>' :'');
                    
					 edit_url=(access.edit=='1' ? '<a href="'+base_url+' index.php/admin_ret_purchase/purchase/purchase_add/'+id+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>': '' );
                     delete_url=(access.delete=='1' ? '<a href="#" class="btn btn-danger btn-del"> <i class="fa fa-trash" data-href="#" data-toggle="modal" data-target="#confirm-delete"></i></a>' :'');
				
                    //  delete_url=(access.delete=='1' ? '#' : '#' );
					 delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
					  //if(row.purchase_type == 1) {
                        print_url= '<a href="'+base_url+'index.php/admin_ret_purchase/purchase/job_receipt/'+id+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="" data-original-title="Customer Copy"><i class="fa fa-print"></i></a>';
                     //}
					 action_content=edit_url+print_url+delete_url;
					return action_content;
				}

			}
    ]
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}


function get_approval_purchase_entry()
{
    
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/approvalstock/ajax_purchase_list?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 type:"POST",
	 success:function(data){
	 	var access=data.access;
	 	var list=data.list;
		var oTable = $('#approval_entry_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#approval_entry_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [[ 0, "desc" ]],
            "dom": 'lBfrtip',
            "buttons" : ['excel','print'],
            "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            "aaData": list,
            "aoColumns": [
                { "mDataProp": "po_id" },
                { "mDataProp": "po_type" },
                { "mDataProp": "po_date" },
                { "mDataProp": "po_ref_no" },
                { "mDataProp": "karigar" },
                { "mDataProp": "category_name" },
                { "mDataProp": "purity" },
                { "mDataProp": "gross_wt" },
                { "mDataProp": "tot_lwt" },
                { "mDataProp": "tot_nwt" },
                { "mDataProp": "total_payable_amt" },
                { "mDataProp": "total_payable_wt" },
                { "mDataProp": function ( row, type, val, meta ) {
    					 id = row.po_id
    					 delete_url = (access.delete=='1' ? base_url+'index.php/admin_ret_estimation/estimation/delete/'+id : '#' );
    					 delete_confirm = (access.delete=='1' ?'#confirm-delete':'');
    					 action_content ='<a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';
    					 return action_content;
    				}
    
    			}
        ]
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });

}
function get_ActiveMetals()
{
    $("div.overlay").css("display", "block"); 
    $.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_catalog/active_metals',
	dataType:'json',
	success:function(data){
	        metalDetails=data;
	        var id=$('#select_metal').val();
	        $.each(data, function (key, item) {   
                $('#select_metal').append(
                $("<option></option>")
                .attr("value", item.id_metal)    
                .text(item.metal)  
                );
        	}); 
        	
        	$('#select_metal').select2({
        	    placeholder: "Metal",
        	    allowClear: true
        	});
	        if($('#select_metal').length)
	        {
	            $('#select_metal').select2("val",(id!='' ? id:''));
	        }
	        
	    	$("div.overlay").css("display", "none"); 
		}
	});
}

$('#select_purity').on('change', function(){
    var selectedpurity = $( "#select_purity option:selected" ).text();
    if(selectedpurity != ""){
        $("#purity").val(selectedpurity);
    }
});

$('#select_metal').on('change',function(){
    
    if(ctrl_page[1]!='karigarmetalissue')
    {
        $('#select_category option').remove();
        $('#select_purity option').remove();
        $('#select_product option').remove();
        $('#select_design option').remove();
        $('#select_sub_design option').remove();
        
        $('#select_category').select2("val",'');
        $('#select_purity').select2("val",'');
        $('#select_product').select2("val",'');
        $('#select_design').select2("val",'');
        $('#select_sub_design').select2("val",'');
    }
    else if(ctrl_page[1]=='karigarmetalissue')
    {
        $('#select_category option').remove();
        $('#select_product option').remove();
        set_available_stock_details();
    }
    
    
   if(this.value!='')
   {
       get_ActiveCategories();
   }
});

function get_ActiveCategories()
{
    $("div.overlay").css("display", "block"); 
    $('#select_category option').remove();
    $.ajax({
	type: 'POST',
	//data:{'id_metal':$('#select_metal').val(), 'id_cat_type' : $("input[type='radio'][name='order[po_type]']:checked").val()},
	data:{'id_metal':$('#select_metal').val(), 'id_cat_type' : ''},
	url: base_url+'index.php/admin_ret_catalog/get_MetalCategory',
	dataType:'json',
	success:function(data){
	    category_lists = data;
	    var id=$('#select_category').val();
	    var id_category=$('#id_category').val();
	            if(ctrl_page[1]=='karigarmetalissue')
	            {
	                $.each(data, function (key, item) { 
	                    if(item.cat_type==2 || item.cat_type==3 || item.cat_type==4)
	                    {
                            $('#select_category').append(
                            $("<option></option>")
                            .attr("value", item.id_ret_category) 
                            .attr("data-cattype", item.cat_type)
                            .text(item.name)  
                            );
	                    }
                	});
	            }
	            else
	            {
	                $.each(data, function (key, item) { 
                        $('#select_category').append(
                        $("<option></option>")
                        .attr("value", item.id_ret_category)  
                        .attr("data-cattype", item.cat_type)
                        .text(item.name)  
                        );
                	});
	            }
	         
        	
        	$('#select_category').select2({
        	    placeholder: "Category",
        	    allowClear: true
        	});
        	
        	console.log(id);
        	
	        if($('#select_category').length)
	        {
	            $('#select_category').select2("val",(id_category!='' ? id_category:''));
	        }
	        $("div.overlay").css("display", "none"); 
		}
	});
}

$('#select_category').on('change',function(){
    
    if(ctrl_page[2]=='add')
    {
        $('#select_purity option').remove();
        $('#select_product option').remove();
        $('#select_design option').remove();
        $('#select_sub_design option').remove();
        
        $('#select_purity').select2("val",'');
        $('#select_product').select2("val",'');
        $('#select_design').select2("val",'');
        $('#select_sub_design').select2("val",'');
        
       if(this.value!='')
       {
           var purchase_type = $("input[name='order[purchase_type]']:checked").val();
           if(purchase_type==1)
           {
               if($('#select_po_no').val()=='' || $('#select_po_no').val()==null)
               {
                   $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select PO No"});
               }
               else
               {
                   //get_OrderProducts();
                   get_CategoryProducts();
                   get_cat_purity();
               }
           }
           else
           {
               get_CategoryProducts();
               get_cat_purity();
           }
           
       }
    }
    else if(ctrl_page[1]=='karigarmetalissue')
    {
        $('#select_product option').remove();
         if(this.value!='')
         {
             get_cat_purity();
             get_CategoryProducts();
         }
        
    }
    else if(ctrl_page[2]=='order_status')
    {
        $('#select_product option').remove();
         if(this.value!='')
         {
             get_CategoryProducts();
         }
    }

});

function get_charges() {

	my_Date = new Date();

	$.ajax({

		url: base_url+'index.php/admin_ret_catalog/charges/getActiveChargesList/?nocache=' + my_Date.getUTCSeconds(),   

		dataType: "json", 

		method: "GET", 

		success: function ( data ) { 

			charges_details = data;

			console.log("charges_details", charges_details);

			charges_list = charges_details;

		}

	 });

}

function get_CategoryProducts()
{
    $('#select_product option').remove();
    $(".overlay").css("display", "block");
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_ret_category':$('#select_category').val()},
	url: base_url+"index.php/admin_ret_catalog/get_ActiveProducts/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	        	    var id=$('#select_product').val();
        	        $.each(data, function (key, item) {   
                        $('#select_product').append(
                        $("<option></option>")
                        .attr("value", item.pro_id)    
                        .text(item.product_name)  
                         .attr("data-purmode", item.purchase_mode)
                        );
                	}); 
                	
                	$('#select_product').select2({
                	    placeholder: "Product",
                	    allowClear: true
                	});
        	        if($('#select_product').length)
        	        {
        	            $('#select_product').select2("val",(id!='' ? id:''));
        	        }
        	        $("div.overlay").css("display", "none"); 
		}
	});
}


function get_OrderProducts()
{
    $('#select_product option').remove();
    $(".overlay").css("display", "block");
    my_Date = new Date();
	$.ajax({
	type: 'POST',
	data:{'id_customerorder':$('#select_po_no').val(),'id_ret_category':$('#select_category').val()},
	url: base_url+"index.php/admin_ret_purchase/get_OrderProducts/?nocache=" + my_Date.getUTCSeconds(),
	dataType:'json',
	success:function(data){
	        	    var id=$('#select_product').val();
        	        $.each(data, function (key, item) {   
                        $('#select_product').append(
                        $("<option></option>")
                        .attr("value", item.pro_id)    
                        .text(item.product_name)
                        .attr("data-purmode", item.purchase_mode)
                        );
                	}); 
                	
                	$('#select_product').select2({
                	    placeholder: "Product",
                	    allowClear: true
                	});
        	        if($('#select_product').length)
        	        {
        	            $('#select_product').select2("val",(id!='' ? id:''));
        	        }
        	        $("div.overlay").css("display", "none"); 
		}
	});
}

function get_OrderProductsDesign()
{
    $('#select_design option').remove();
	$(".overlay").css('display','block');
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_purchase/get_OrderProductsDesign',
		dataType:'json',
		data:{'id_product':$('#select_product').val(),'id_customerorder':$('#select_po_no').val()},
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


$('#select_product').on('change',function(){
    if(this.value!='' && ctrl_page[1]=='approvalstock')
    {
        $('#select_design option').remove();
        $('#select_sub_design option').remove();
        $('#tot_wastage_perc').val(0);
        
        $('#select_design').select2("val",'');
        $('#select_sub_design').select2("val",'');
        
        
         if(this.value!='')
         {
             if(ctrl_page[1]=='approvalstock' && ctrl_page[2]=='add')
               {
                   get_ActiveSize();
                   get_ActiveWeightRange();
                   get_ProductDesign();
        
               }
         }
    }
    else if(ctrl_page[1]=='karigarmetalissue' && this.value!='')
    {
        set_availabe_purity();
        $('#select_design option').remove();
        $('#select_sub_design option').remove();
        $('#select_design').select2("val",'');
        $('#select_sub_design').select2("val",'');
        get_ProductDesign();
    }
   else if(ctrl_page[1]=='purchasereturn' && this.value!='')
   {
        $('#select_design option').remove();
        $('#select_sub_design option').remove();
        $('#select_design').select2("val",'');
        $('#select_sub_design').select2("val",'');
        get_ProductDesign();
   }
   
});


function set_karigar_wise_wastage()
{
    if($('#select_karigar').val()!='' && $('#select_karigar').val()!=null && $('#select_product').val()!='' && $('#select_product').val()!=null && $('#select_design').val()!='' && $('#select_design').val()!=null && $('#select_sub_design').val()!='' && $('#select_sub_design').val()!=null)
    {
        $.each(karigar_wastage_details,function(key,items){
            if((items.id_karikar==$('#select_karigar').val()) && ($('#select_product').val()==items.id_product) && ($('#select_sub_design').val()==items.id_sub_design) && ($('#select_design').val()==items.id_design) )
            {
                $('#tot_wastage_perc').val(items.wastage_per);
            }
        });
    }
}


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
    
    $('#select_sub_design option').remove();
    $('#tot_wastage_perc').val(0);
    $('#select_sub_design').select2("val",'');
    
   if(this.value!='')
   {
       if(ctrl_page[1]=='approvalstock' && ctrl_page[2]=='add')
       {
           get_ActiveSubDesigns();
           
       }
   }
});

$('#select_sub_design').on('change',function(){
    $('#tot_wastage_perc').val(0);
    if(this.value!='')
    {
           if(ctrl_page[1]=='approvalstock' && ctrl_page[2]=='add')
           {
               set_availabe_purity();
           }
    }
});

function get_OrderSubDesigns()
{
    $('#select_sub_design option').remove();
	$(".overlay").css('display','block');
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_purchase/get_OrderSubDesigns',
		dataType:'json',
		data:{'id_product':$('#select_product').val(),'design_no':$('#select_design').val(),'id_customerorder':$('#select_po_no').val()},
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


function get_cat_purity()
{
    $('#select_purity option').remove();
	$(".overlay").css('display','block');
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_catalog/category/cat_purity',
		dataType:'json',
		data:{'id_category':$('#select_category').val()},
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
                placeholder: "Purity",
                allowClear: true
            });
            
            if($("#select_purity").length)
            {
                $("#select_purity").select2("val",(id_purity!='' && id_purity>0?id_purity:''));
            }
            
            $(".overlay").css("display", "none");	
		}
	});
}

$("#purchase_order_no").on("keyup",function(e){ 

	var orderno = $("#purchase_order_no").val(); 

	if(orderno != "") { 

		get_purchase_orderno(orderno);

    }

});

$('#tot_gwt,#tot_wastage_perc,#rate_per_gram,#tot_pcs,#tot_wastage_perc,#mc_value,#purchase_touch').on('keyup',function(){
    var tot_gwt=(isNaN($('#tot_gwt').val()) || $('#tot_gwt').val()=='' ? 0:$('#tot_gwt').val());
    var tot_lwt=(isNaN($('#tot_lwt').val()) || $('#tot_lwt').val()=='' ? 0:$('#tot_lwt').val());
    var other_metal_wt = (isNaN($('#other_metal_wt').val()) || $('#other_metal_wt').val()=='' ? 0:$('#other_metal_wt').val());
    $('#tot_nwt').val(parseFloat(tot_gwt-tot_lwt).toFixed(3));
    
    var tot_netwt = parseFloat(tot_gwt-(tot_lwt + other_metal_wt)).toFixed(3);
    //var purity = parseFloat($("#select_purity option:selected").text());
    var purity = (isNaN($('#purchase_touch').val()) || $('#purchase_touch').val()=='' ? 0:$('#purchase_touch').val());
    var wastage = (isNaN($('#tot_wastage_perc').val()) || $('#tot_wastage_perc').val()=='' ? 0:$('#tot_wastage_perc').val());
    if(wastage > 100)
    {
        wastage = 0;
        $('#tot_wastage_perc').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Wastage Percentage Should Not Exceed More Than 100"});
    }
    var purewt = parseFloat((parseFloat(tot_netwt) * (parseFloat(purity) + parseFloat(wastage))) / 100).toFixed(3);
   
    $('#tot_purewt').val(format(purewt).replace(/,/g, ''));
    
    calculate_purchase_item_cost();
});

$('#ratecaltype,#mc_type').on('change',function(){
    calculate_purchase_item_cost();
});

$('.is_rate_fixed').on('change',function(){
    if($('.is_rate_fixed:checked').val()==1)
    {
        //$('#rate_per_gram').prop('readonly',false);
    }else{
        //$('#rate_per_gram').prop('readonly',true);
    }
});


$("#add_po_order_items").on('click',function(e){
    //var pur_order_type = $("input[type='radio'][name='order[po_type]']:checked").val();
    
    var pur_order_type = $('#select_category option:selected').attr('data-cattype');
    
    console.log('pur_order_type:'+pur_order_type);
    
    var purchase_type = $("input[name='order[purchase_type]']:checked").val();
    
    if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Karigar"});
    }
    else if($('#select_category').val()=='' || $('#select_category').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Category"});
    }
    else if($('#select_purity').val()=='' || $('#select_purity').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Purity"});
    }
    else if($('#select_product').val()=='' || $('#select_product').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Product"});
    }
    else if(($('#select_design').val()=='' || $('#select_design').val()==null))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Design"});
    }
    else if(($('#select_sub_design').val()=='' || $('#select_sub_design').val()==null))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Sub Design"});
    }
    else if($('#tot_pcs').val()=='' || $('#tot_pcs').val()==0)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Pcs"});
    }
    else if($('#tot_gwt').val()=='' || $('#tot_gwt').val()==0 && $('#select_product option:selected').attr('data-purmode') == 2)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Weight"});
    }
    else if($('#purchase_touch').val()=='' || $('#purchase_touch').val()==0 && $('#select_product option:selected').attr('data-purmode') == 2)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Purchase Touch"});
    }
    else if($('#rate_per_gram').val()=='' && $('#select_product option:selected').attr('data-purmode') == 2)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Purchase Rate"});
    }
    else
    {
        set_po_order_item_preview();
    }
    
});



function set_po_order_item_preview()
{
    var is_halmerked = $("input[type='radio'][name='is_halmerked']:checked").val();
    var is_rate_fixed = $("input[type='radio'][name='is_rate_fixed']:checked").val();
    //var pur_order_type = $("input[type='radio'][name='order[po_type]']:checked").val();
    
    var pur_order_type = $('#select_category option:selected').attr('data-cattype');
    
    var purmode = $('#select_product option:selected').attr('data-purmode');
    
    var is_cus_repair_order=$('#is_cus_repair_order').val();
    var cus_repair_amt = 0;
    
    if(is_cus_repair_order == 1){
        cus_repair_amt = $("#cus_repair_amt").val();
    }
    
    var tot_pcs=0;
    var tot_gwt=0;
    var tot_lwt=0;
    var tot_nwt=0;
    var cal_type = 1;
    var gwt_uom = 0;
    
    tot_pcs = $("#tot_pcs").val();
    tot_gwt = $("#tot_gwt").val();
    tot_lwt = $("#tot_lwt").val();
    tot_nwt = $("#tot_nwt").val();
    
    /*if(pur_order_type == 3){
      tot_gwt   = $(".stone_wt").val();
      cal_type  = $("input[type='radio'][name='cal_type']:checked").val();
      gwt_uom   = $(".stone_uom_id").val();
    }*/
    var tax_group = '';
    var total_tax_rate = 0;
    var igst_cost = 0;
    var sgst_cost = 0;
    var cgst_cost = 0;
    $.each(category_lists,function(key,val){
        if(val.id_ret_category == $('#select_category').val())
        {
            tax_group = val.tgrp_id;
        }
    });
    if(tax_group!='')
    {
        var supplier_country = '';
        var supplier_state = '';
        var cmp_country = $('#cmp_country').val();
        var cmp_state = $('#cmp_state').val();
        var supplier_country = $('#supplier_country').val();
        var supplier_state = $('#supplier_state').val();
        
    
        total_tax_rate = parseFloat(calculate_inclusiveGST($("#item_cost").val(),tax_group)).toFixed(2);
        
        if(cmp_country=='' || cmp_state=='')
		{
		    cgst_cost = parseFloat(parseFloat(total_tax_rate)/2).toFixed(2);
		    sgst_cost = parseFloat(parseFloat(total_tax_rate)/2).toFixed(2);
		}
		else
		{
		    if(total_tax_rate > 0)
		    {
		        if(cmp_country==supplier_country)
    		    {
    		        if(cmp_state==supplier_state)
    		        {
    		            sgst_cost = parseFloat(parseFloat(total_tax_rate)/2).toFixed(2);
    		            cgst_cost = parseFloat(parseFloat(total_tax_rate)/2).toFixed(2);
    		        }else
    		        {
    		            igst_cost = total_tax_rate;
    		        }
    		    }else
    		    {
    		        sgst_cost = parseFloat(parseFloat(total_tax_rate)/2).toFixed(2);
    		        cgst_cost = parseFloat(parseFloat(total_tax_rate)/2).toFixed(2);
    		    }
		    }
		}
    }
    
    
    var stn_details = $('#stone_details').val();
    var dia_weight = 0;
    if(stn_details!='')
    {
        stone_details = JSON.parse(stn_details);
        $.each(stone_details,function(key,val){
            if(val.stone_uom_id==6)
            {
                dia_weight+=parseFloat(val.stone_wt);
            }
        });
    }

    var trHtml='<tr>'
                +'<td><input type="hidden" class="id_category" name="order_item[id_category][]" value="'+$('#select_category').val()+'"><input type="hidden" class="id_order_no" name="order_item[po_order_no][]" value="'+$('#select_po_no').val()+'">'+$("#select_category option:selected").text()+'</td>'
                +'<td><input type="hidden" class="id_product" name="order_item[id_product][]" value="'+$('#select_product').val()+'">'+$("#select_product option:selected").text()+'<input type="hidden" name="order_item[po_purchase_mode][]" value='+purmode+'></td>'
                +'<td><input type="hidden" class="id_design" name="order_item[id_design][]" value="'+$('#select_design').val()+'">'+$("#select_design option:selected").text()+'</td>'
                +'<td><input type="hidden" class="id_sub_design" name="order_item[id_sub_design][]" value="'+$('#select_sub_design').val()+'">'+$("#select_sub_design option:selected").text()+'</td>'
                +'<td><input type="hidden" class="id_purity" name="order_item[id_purity][]" value="'+$('#select_purity').val()+'">'+$("#select_purity option:selected").text()+'</td>'
                +'<td><input type="hidden" class="tot_pcs" name="order_item[tot_pcs][]" value="'+tot_pcs+'">'+tot_pcs+'</td>'
                +'<td><input type="hidden" class="tot_gwt" name="order_item[tot_gwt][]" value="'+tot_gwt+'"><input type="hidden" class="cal_type" name="order_item[cal_type][]" value="'+cal_type+'"><input type="hidden" class="gwt_uom" name="order_item[gwt_uom][]" value="'+gwt_uom+'">'+tot_gwt+'</td>'
                +'<td><input type="hidden" class="tot_lwt" name="order_item[tot_lwt][]" value="'+tot_lwt+'">'+tot_lwt+'</td>'
                +'<td><input type="hidden" class="tot_nwt" name="order_item[tot_nwt][]" value="'+tot_nwt+'">'+tot_nwt+'</td>'
                +'<td><span class="total_diawt">'+parseFloat(dia_weight).toFixed(3)+'</span></td>'
                +'<td><input type="hidden" class="tot_purewt" name="order_item[tot_purewt][]" value="'+$('#tot_purewt').val()+'">'+$('#tot_purewt').val()+'</td>'
                +'<td><input type="hidden" class="purchase_touch" name="order_item[purchase_touch][]" value="'+$('#purchase_touch').val()+'"><input type="hidden" name="order_item[tot_wastage_perc][]" value="'+$('#tot_wastage_perc').val()+'">'+$("#tot_wastage_perc").val()+'</td>'
                +'<td><input type="hidden" class="mc_value" name="order_item[mc_value][]" value="'+$('#mc_value').val()+'"><input type="hidden" class="mc_type" name="order_item[mc_type][]" value="'+$('#mc_type').val()+'">'+$("#mc_value").val()+'-'+$("#mc_type option:selected").text()+'</td>'
                +'<td><input type="hidden" class="cus_repair_amt" name="order_item[cus_repair_amt][]" value="'+$('#cus_repair_amt').val()+'"><input type="hidden" class="stone_price" name="order_item[stone_price][]" value="'+$('#stone_price').val()+'">'+$('#stone_price').val()+'<input type="hidden" class="stone_details" name="order_item[stone_details][]" value=\''+$('#stone_details').val()+'\'></td>'
                +'<td>'+$('#other_metal_amount').val()+'<input type="hidden" class="other_metal_amt" value='+$('#other_metal_amount').val()+'><input type="hidden" name="order_item[other_metal_details][]" value='+$('#other_metal_details').val()+'></td>'
                +'<td><input type="hidden" class="total_payable_amt" name="order_item[total_payable_amt]" value="'+$("#item_cost").val()+'"><input type="hidden" class="cgst_cost" name="order_item[cgst_cost]" value="'+cgst_cost+'"><input type="hidden" class="sgst_cost" name="order_item[sgst_cost]" value="'+sgst_cost+'"><input type="hidden" class="igst_cost" name="order_item[igst_cost]" value="'+igst_cost+'"><input type="hidden" class="total_tax_rate" name="order_item[total_tax_rate]" value="'+total_tax_rate+'"><input type="hidden" name="order_item[ratecaltype][]" value="'+purmode+'"><input type="hidden" class="rate_per_gram" name="order_item[rate_per_gram][]" value="'+$('#rate_per_gram').val()+'"><input type="hidden" name="order_item[item_cost][]" value="'+$('#item_cost').val()+'"><input type="hidden" name="order_item[is_halmerked][]" value="'+is_halmerked+'"><input type="hidden" name="order_item[is_rate_fixed][]" value="'+is_rate_fixed+'">'+$("#item_cost").val()+'</td>'
                +'<td><input type="hidden" class="remark" name="order_item[remark][]" value="'+$('#remark').val()+'"><span><a href="#" onclick="remove_po_entry_row($(this).closest(\'tr\'));" class="btn-del label label-danger" style="padding:5px;" data-toggle="tooltip" title="Delete!"><i class="fa fa-trash"></i></a></span><span><a href="#" onclick="edit_po_entry_row($(this).closest(\'tr\'));" class="btn-del label label-success" style="padding:5px;" data-toggle="tooltip" title="Edit!"><i class="fa fa-edit"></i></a></span></td>'
            '</tr>';
    if($('#item_detail > tbody  > tr').length>0)
	{
	    $('#item_detail > tbody > tr:first').before(trHtml);
	}else{
	    $('#item_detail tbody').append(trHtml);
	}
    reset_order_entry_form(); 
    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>Item Added Successfully."});
    //calculate_total_total_purchase_order_items();
    calculate_purchase_order_item_total();
}


function calculate_inclusiveGST(taxcallrate, taxgroup){
	var totaltax = 0;
	console.log(tax_details);
	$.each(tax_details, function(idx, taxitem){
		if(taxitem.tgi_tgrpcode == taxgroup){
		//	Remove GST = 490*100/(100+3) = 475.7281553398058
        //	GST 3% = 490 - 475.7281553398058 = 14.2718446601942
			amt_without_gst = (parseFloat(taxcallrate)*100)/(100+parseFloat(taxitem.tax_percentage));
			totaltax += parseFloat(taxcallrate)	- parseFloat(amt_without_gst);
		}
	});
	return totaltax;
}

function remove_po_entry_row(curRow)
{
    curRow.remove();
    calculate_purchase_order_item_total();
}

function edit_po_entry_row(curRow)
{
    $('#select_category').select2("val",curRow.find('.id_category').val());
    $('#select_purity').select2("val",curRow.find('.id_purity').val());
    $('#select_product').select2("val",curRow.find('.id_product').val());
    $('#select_design').select2("val",curRow.find('.id_design').val());
    $('#select_sub_design').select2("val",curRow.find('.id_sub_design').val());
    $('#tot_pcs').val(curRow.find('.tot_pcs').val());
    $('#tot_gwt').val(curRow.find('.tot_gwt').val());
    $('#stone_details').val(curRow.find('.stone_details').val());
    $('#mc_type').val(curRow.find('.mc_type').val());
    $('#mc_value').val(curRow.find('.mc_value').val());
    $('#rate_per_gram').val(curRow.find('.rate_per_gram').val());
    curRow.remove();
    calculate_purchase_order_item_total();
}


function calculate_purchase_order_item_total()
{
    var total_pcs=0;
    var total_wt=0;
    var total_lwt=0;
    var total_nwt=0;
    var other_metal_amt=0;
    var total_payable_amt=0;
    var tot_purewt=0;
    var total_item_cost=0;
    var total_cgst_cost=0;
    var total_sgst_cost=0;
    var total_igst_cost=0;
    var total_diawt=0;
    var tot_stone_price=0;
     $('#item_detail > tbody tr').each(function(idx, idx){
         row = $(this);
        total_pcs+=parseFloat((row.find('.tot_pcs').val()!='' ? row.find('.tot_pcs').val():0));
        total_wt+=parseFloat((row.find('.tot_gwt').val()!='' ? row.find('.tot_gwt').val():0));
        total_lwt+=parseFloat((row.find('.tot_lwt').val()!='' ? row.find('.tot_lwt').val():0));
        total_nwt+=parseFloat((row.find('.tot_nwt').val()!='' ? row.find('.tot_nwt').val():0));
        total_payable_amt+=parseFloat(row.find('.total_payable_amt').val()-row.find('.total_tax_rate').val());
        total_item_cost+=parseFloat(row.find('.total_payable_amt').val());
        other_metal_amt+=parseFloat((row.find('.other_metal_amt').val()!='' ? row.find('.other_metal_amt').val():0));
        tot_stone_price+=parseFloat((row.find('.stone_price').val()!='' ? row.find('.stone_price').val():0));
        tot_purewt+=parseFloat(row.find('.tot_purewt').val());
        total_cgst_cost+=parseFloat(row.find('.cgst_cost').val());
        total_sgst_cost+=parseFloat(row.find('.sgst_cost').val());
        total_igst_cost+=parseFloat(row.find('.igst_cost').val());
        total_diawt+=parseFloat(row.find('.total_diawt').html());
    });
    
    var tcsval = 0;
    var tdsval = 0;
    var other_charges_tdsval = 0;
    var tcspercent =(isNaN($('#po_tcs_percent').val()) || $('#po_tcs_percent').val()=='' ? 0:$('#po_tcs_percent').val()); 
    var tdspercent =(isNaN($('#po_tds_percent').val()) || $('#po_tds_percent').val()=='' ? 0:$('#po_tds_percent').val());
    var other_charges_taxable_amount =(isNaN($('#other_charges_taxable_amount').val()) || $('#other_charges_taxable_amount').val()=='' ? 0:$('#other_charges_taxable_amount').val());
    var charges_tds_percent =(isNaN($('#charges_tds_percent').val()) || $('#charges_tds_percent').val()=='' ? 0:$('#charges_tds_percent').val());
    
    if(tdspercent > 0){
        tdsval = parseFloat(parseFloat(total_payable_amt) * (tdspercent / 100)).toFixed(2);
    }
    console.log('tdsval:'+tdsval);
    $("#item_tds_tax_value").val(tdsval);
    
    if(tcspercent > 0){
        tcsval = parseFloat(parseFloat(total_item_cost) * (tcspercent / 100)).toFixed(2);
    }
    $("#item_tcs_tax_value").val(tcsval);
    
    if(charges_tds_percent > 0)
    {
        other_charges_tdsval = parseFloat(parseFloat(other_charges_taxable_amount) * (charges_tds_percent / 100)).toFixed(2);
    }
    $("#other_charges_tds_tax_value").val(other_charges_tdsval);

    $('.total_pcs').html(total_pcs);
    $('.total_gwt').html(parseFloat(total_wt).toFixed(3));
    $('.total_lwt').html(parseFloat(total_lwt).toFixed(3));
    $('.total_nwt').html(parseFloat(total_nwt).toFixed(3));
    $('.total_diawt').html(parseFloat(total_diawt).toFixed(3));
    $('.total_pure_wt').html(parseFloat(tot_purewt).toFixed(3));
    $('.total_amt').html(parseFloat(total_item_cost).toFixed(2));
    $('.other_metal_amt').html(parseFloat(other_metal_amt).toFixed(2));
    $('.tot_paybale_purewt').html(parseFloat(tot_purewt).toFixed(3));
    $('.tot_stone_price').html(parseFloat(tot_stone_price).toFixed(2));
    $('.tot_purchase_wt').val(parseFloat(tot_purewt).toFixed(3));
    $('.total_summary_taxable_amt').val(parseFloat(total_payable_amt).toFixed(2));
    $('.total_summary_cgst_amount').val(parseFloat(total_cgst_cost).toFixed(2));
    $('.total_summary_sgst_amount').val(parseFloat(total_sgst_cost).toFixed(2));
    $('.total_summary_igst_amount').val(parseFloat(total_igst_cost).toFixed(2));
    
    $('#received_pcs').val(total_pcs);
    $('#received_wt').val(parseFloat(total_wt).toFixed(3));
    
    calculate_FinalCost();
    
}

$(document).on('keyup','.tot_discount',function(){
    calculate_FinalCost();
});

$(document).on('keyup', '.tcs_percent, .tds_percent,.charges_tds_percent', function (){
    if(ctrl_page[1]=='grnentry')
    {
        calculate_grn_final_cost();
    }
    if(ctrl_page[1]=='purchasereturn')
    {
        calculate_purchase_return_final_cost();
    }
    else
    {
        calculate_FinalCost();
    }
    
});


function calculate_FinalCost()
{
    /*var total_cost          =0;
    var total_payable_amt   =(isNaN($('.total_summary_payable_amt').val()) || $('.total_summary_payable_amt').val()=='' ? 0:$('.total_summary_payable_amt').val());
    var other_charges_amount=(isNaN($('#other_charges_amount').val()) || $('#other_charges_amount').val()=='' ? 0:$('#other_charges_amount').val());
   
    var tot_discount        = (isNaN($('#tot_discount').val()) || $('#tot_discount').val()=='' ? 0:$('#tot_discount').val());
    var total_cost          = parseFloat(parseFloat(total_payable_amt)+parseFloat(other_charges_amount)-parseFloat(tot_discount)).toFixed(2);
    var tcsval = 0;
    var tdsval = 0;
    var tcspercent =(isNaN($('#tcs_percent').val()) || $('#tcs_percent').val()=='' ? 0:$('#tcs_percent').val()); 
    var tdspercent =(isNaN($('#tds_percent').val()) || $('#tds_percent').val()=='' ? 0:$('#tds_percent').val()); 
    if(tcspercent > 0){
        tcsval = parseFloat(parseFloat(total_cost) * (tcspercent / 100)).toFixed(2);
    }
    $("#tcs_tax_value").val(tcsval);
    
    if(tdspercent > 0){
        tdsval = parseFloat(parseFloat(total_cost) * (tdspercent / 100)).toFixed(2);
    }
    $("#tds_tax_value").val(tdsval);
    
    
    $('.total_cost').val(parseFloat(parseFloat(total_cost) + parseFloat(tcsval) - parseFloat(tdsval)).toFixed(0));*/
    
    
    
    var total_summary_taxable_amt       = (isNaN($('.total_summary_taxable_amt').val()) || $('.total_summary_taxable_amt').val()=='' ? 0:$('.total_summary_taxable_amt').val());
    var tds_tax_value                   = (isNaN($('.item_tds_tax_value').val()) || $('.item_tds_tax_value').val()=='' ? 0:$('.item_tds_tax_value').val());
    var total_summary_cgst_amount       = (isNaN($('.total_summary_cgst_amount').val()) || $('.total_summary_cgst_amount').val()=='' ? 0:$('.total_summary_cgst_amount').val());
    var total_summary_sgst_amount       = (isNaN($('.total_summary_sgst_amount').val()) || $('.total_summary_sgst_amount').val()=='' ? 0:$('.total_summary_sgst_amount').val());
    var total_summary_igst_amount       = (isNaN($('.total_summary_igst_amount').val()) || $('.total_summary_igst_amount').val()=='' ? 0:$('.total_summary_igst_amount').val());
    var tcs_tax_value                   = (isNaN($('.item_tcs_tax_value').val()) || $('.item_tcs_tax_value').val()=='' ? 0:$('.item_tcs_tax_value').val());
    var other_charges_tds_tax_value     = (isNaN($('.other_charges_tds_tax_value').val()) || $('.other_charges_tds_tax_value').val()=='' ? 0:$('.other_charges_tds_tax_value').val());
    var other_charges_taxable_amount    = (isNaN($('#other_charges_taxable_amount').val()) || $('#other_charges_taxable_amount').val()=='' ? 0:$('#other_charges_taxable_amount').val());
    var other_charges_tax               = (isNaN($('#other_charges_tax').val()) || $('#other_charges_tax').val()=='' ? 0:$('#other_charges_tax').val());
    var grn_discount                    = (isNaN($('.grn_discount').val()) || $('.grn_discount').val()=='' ? 0:$('.grn_discount').val());
    var final_cost = parseFloat(parseFloat(total_summary_taxable_amt)-parseFloat(tds_tax_value)+parseFloat(total_summary_cgst_amount)+parseFloat(total_summary_sgst_amount)+parseFloat(total_summary_igst_amount)+parseFloat(tcs_tax_value)+parseFloat(other_charges_taxable_amount)-parseFloat(other_charges_tds_tax_value)+parseFloat(other_charges_tax)-parseFloat(grn_discount)).toFixed(2)
    console.log('final_cost:'+final_cost);
    round_of_val               = final_cost;
    tot_cost 			        = parseFloat(Math.round(final_cost));
    
	 round_of_amt               = parseFloat(tot_cost-round_of_val).toFixed(2);
	 console.log(round_of_amt);
	 $('.grn_round_off').val(round_of_amt<0.50 ? round_of_amt : round_of_amt);
     $('.total_cost').val(parseFloat(tot_cost).toFixed(2));
    
}





function reset_order_entry_form()
{
    $('#select_category').select2("val","");
    $('#select_product').select2("val","");
    $('#select_design').select2("val","");
    $('#select_sub_design').select2("val","");
    $('#select_sub_design').select2("val","");
    $('#tot_pcs').val('');
    $('#tot_gwt').val('');
    $('#tot_lwt').val('');
    $('#tot_nwt').val('');
    $('#tot_wastage_perc').val('');
    $('#mc_value').val('');
    $('#rate_per_gram').val(0);
    $('#tot_purewt').val(0);
    $('#item_cost').val(0);
    $('#purchase_touch').val();
    $('#stone_details').val('');
    $('#remark').val('');
    $('#stone_price').val(0);
    
    $('#other_metal_details').val('');
    $('#other_metal_amount').val(0);
    $('#other_metal_wt').val(0);
    $('#other_metal_wast_wt').val(0);
    $('#other_metal_mc_amount').val(0);
    
   /*$('#other_charges_details').val('');
    $('#other_charges_amount').val(0);*/
    
    modalStoneDetail=[];
    metal_details=[];
}

function calculate_purchase_item_cost()
{
    var item_cost=0;
    var rate_per_gram           = (isNaN($('#rate_per_gram').val()) || $('#rate_per_gram').val()=='' ? 0:$('#rate_per_gram').val());
    var tot_pcs                 = (isNaN($('#tot_pcs').val()) || $('#tot_pcs').val()=='' ? 0:$('#tot_pcs').val());
    var tot_gwt                 = (isNaN($('#tot_gwt').val()) || $('#tot_gwt').val()=='' ? 0:$('#tot_gwt').val());
    var tot_lwt                 = (isNaN($('#tot_lwt').val()) || $('#tot_lwt').val()=='' ? 0:$('#tot_lwt').val());
    var wastage_per             = (isNaN($('#tot_wastage_perc').val()) || $('#tot_wastage_perc').val()=='' ? 0:$('#tot_wastage_perc').val());
    var mc_value                = (isNaN($('#mc_value').val()) || $('#mc_value').val()=='' ? 0:$('#mc_value').val());
    var other_metal_amount      = (isNaN($('#other_metal_amount').val()) || $('#other_metal_amount').val()=='' ? 0:$('#other_metal_amount').val());
    var other_metal_wt          = (isNaN($('#other_metal_wt').val()) || $('#other_metal_wt').val()=='' ? 0:$('#other_metal_wt').val());
    var other_metal_wast_wt     = (isNaN($('#other_metal_wast_wt').val()) || $('#other_metal_wast_wt').val()=='' ? 0:$('#other_metal_wast_wt').val());
    var other_metal_mc_amount   = (isNaN($('#other_metal_mc_amount').val()) || $('#other_metal_mc_amount').val()=='' ? 0:$('#other_metal_mc_amount').val());
    var stone_price             = (isNaN($('#stone_price').val()) || $('#stone_price').val()=='' ? 0:$('#stone_price').val());
    var order_div_gwt               = 0;
    
    var karigar_type = $('#select_karigar option:selected').attr('data-karigartpe');
    
    var ratecaltype = $('#select_product option:selected').attr('data-purmode'); //2-Flexible,2-Fixed
    
    var purchase_type = $("input[name='order[purchase_type]']:checked").val();
    
    var grn_type    ='';
   
    if(stone_price > 0){
        stone_price = parseFloat(parseFloat(stone_price) * ((100 + 3) / 100)).toFixed(2);
    }
    
    var other_charges_amount = 0;
    
    /*var other_charges_amount      = (isNaN($('#other_charges_amount').val()) || $('#other_charges_amount').val()=='' ? 0:$('#other_charges_amount').val());
    
    if(other_charges_amount > 0){
        other_charges_amount = parseFloat(parseFloat(other_charges_amount) * ((100 + 3) / 100)).toFixed(2);
    }*/
    
   
   

    var net_wt                  = parseFloat(tot_gwt)-parseFloat(other_metal_wt)-parseFloat(tot_lwt); //Removing Other Metal Weight and Stone Weight
    
    $('#tot_nwt').val(parseFloat(net_wt).toFixed(3));
    
    if($('#is_cus_repair_order').val() == 1){
       if(tot_gwt > 0){
           var tot_ord_issue_wt = (isNaN($('#order_gwt').val()) || $('#order_gwt').val()=='' ? 0:$('#order_gwt').val());
           order_div_gwt = parseFloat(net_wt) - parseFloat(tot_ord_issue_wt);
       }
   }
    
    
    var purity = (isNaN($('#purchase_touch').val()) || $('#purchase_touch').val()=='' ? 0:$('#purchase_touch').val());
    var wastage = (isNaN($('#tot_wastage_perc').val()) || $('#tot_wastage_perc').val()=='' ? 0:$('#tot_wastage_perc').val());
    
   /* if(purchase_type == 2){ // If normal purchase
        var purewt = format(parseFloat((parseFloat(net_wt) * (parseFloat(purity) + parseFloat(wastage))) / 100)).replace(/,/g, '');
    }else{ // If againist order
        if($('#is_cus_repair_order').val() == 1){ // For repair
            var purewt = format(parseFloat((parseFloat(order_div_gwt) * (parseFloat(purity))) / 100)).replace(/,/g, '');
        }else{ // For job work
            if(karigar_type == 1){ // If GST karigar
                var purewt = format(parseFloat((parseFloat(net_wt) * (parseFloat(purity))) / 100)).replace(/,/g, '');
            }else{ // If local karigar
                var purewt = format(parseFloat((parseFloat(net_wt) * parseFloat(purity) / 100) + ((parseFloat(net_wt) * parseFloat(wastage)) / 100) )).replace(/,/g, '');
            }
        }
    }*/

    var mc_type                 = $('#mc_type').val();
    
    var wast_wt                 = parseFloat((net_wt*$('#tot_wastage_perc').val())/100).toFixed(3);
    
    var total_weight            = parseFloat(wast_wt)+parseFloat(net_wt);
    
    //$('#is_cus_repair_order').val(1);
  
    
    //var mctax                   = purchase_type == 2 ? 3 : $('#is_cus_repair_order').val() == 1 ? 18 : 5;
    
    
   /* var mctax                   = purchase_type == 2 ? 3 : $('#is_cus_repair_order').val() == 1 ? 18 : (is_metal_issue==1 ? 5 : 3);
    
    if(purchase_type == 2){
    
        var item_cost                   = parseFloat(((parseFloat(pure_wt))*parseFloat(rate_per_gram))+parseFloat(mc_type == 1 ? ((mc_value*tot_gwt) * ((100 + mctax) / 100)) : ((mc_value*tot_pcs) * ((100 + mctax) / 100)))+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);
    }else{
        if($('#is_cus_repair_order').val() == 1){
            wast_wt                 = parseFloat((order_div_gwt * $('#tot_wastage_perc').val()) / 100).toFixed(3);
            if(karigar_type == 1){
                var item_cost  = parseFloat(((parseFloat(pure_wt))*parseFloat(rate_per_gram))+parseFloat(mc_type == 1 ? ((mc_value) * ((100 + mctax) / 100)) : ((mc_value*tot_pcs) * ((100 + mctax) / 100)))+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price) + ((wast_wt * rate_per_gram)* ((100 + mctax) / 100))).toFixed(2);
            }else{
               var item_cost  = parseFloat(((parseFloat(pure_wt))*parseFloat(rate_per_gram))+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);
            }
            
        }else{
            if(karigar_type == 1){
                var item_cost  = parseFloat(((parseFloat(pure_wt))*parseFloat(rate_per_gram))+parseFloat(mc_type == 1 ? ((mc_value*tot_gwt) * ((100 + mctax) / 100)) : ((mc_value*tot_pcs) * ((100 + mctax) / 100)))+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price) + ((wast_wt * rate_per_gram)* ((100 + mctax) / 100))).toFixed(2);
            }else{
               var item_cost  = parseFloat(((parseFloat(pure_wt))*parseFloat(rate_per_gram))+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);
            }
        }
    }
    
    if($("input[type='radio'][name='order[po_type]']:checked").val() == 3){ // For stone purchase
        var tot_pcs = (isNaN($('#tot_pcs').val()) || $('#tot_pcs').val()=='' ? 0:$('#tot_pcs').val());
        var stone_wt = (isNaN($('.stone_wt').val()) || $('.stone_wt').val()=='' ? 0:$('.stone_wt').val());
        var stone_uom = (isNaN($('.stone_uom_id').val()) || $('.stone_uom_id').val()=='' ? 0:$('.stone_uom_id').val());
        var stone_cal_type = $("input[type='radio'][name='cal_type']:checked").val();
        
        if(stone_cal_type == 1){
            var item_cost  = parseFloat((((parseFloat(stone_wt))*parseFloat(rate_per_gram)) * ((100 + 0.25) / 100))).toFixed(2);
        }else{
            var item_cost  = parseFloat((((parseFloat(tot_pcs))*parseFloat(rate_per_gram)) * ((100 + 0.25) / 100))).toFixed(2);
        }
        
    }else if($("input[type='radio'][name='order[po_type]']:checked").val() == 2){ // For bullion purchase
        var item_cost  = parseFloat((((parseFloat(tot_gwt))*parseFloat(rate_per_gram)) )).toFixed(2);
    }*/
    
    $.each(activeGRNS,function(g,val){
        if(val.grn_id==$('#select_grn').val())
        {
            grn_type=val.grn_type;
        }
    });
    if(grn_type==1) //1-Bill,2-Receipt
    {
        var purewt = format(parseFloat((parseFloat(net_wt) * (parseFloat(purity) + parseFloat(wastage))) / 100)).replace(/,/g, '');
        var total_mc_value  = (mc_type==1 ? parseFloat(parseFloat(mc_value)*parseFloat(tot_gwt)).toFixed(2) :parseFloat(parseFloat(mc_value)*parseFloat(tot_pcs)).toFixed(2));
        if(karigar_type == 1)
        {
            total_mc_value = parseFloat(parseFloat(total_mc_value)+parseFloat(parseFloat(parseFloat(total_mc_value*3))/100)).toFixed(2);
        }
        if(ratecaltype==2)
        {
            item_cost   = parseFloat((parseFloat(purewt)*parseFloat(rate_per_gram))+parseFloat(total_mc_value)+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);
        }else
        {
            item_cost   = parseFloat((parseFloat(tot_pcs)*parseFloat(rate_per_gram))+parseFloat(total_mc_value)+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);
        }
        
    }
    else
    {
        //var purewt = format(parseFloat((parseFloat(net_wt) * (parseFloat(purity))) / 100)).replace(/,/g, '');
        
        var purewt = format(parseFloat(((parseFloat(net_wt) * (parseFloat(purity) / 100)) * (parseFloat(wastage) + 100) / 100))).replace(/,/g, '');
        
        
        var total_mc_value  = (mc_type==1 ? parseFloat(parseFloat(mc_value)*parseFloat(tot_gwt)).toFixed(2) :parseFloat(parseFloat(mc_value)*parseFloat(tot_pcs)).toFixed(2));
        console.log(purewt);
        var total_wt = parseFloat(parseFloat(parseFloat(net_wt)*parseFloat(wastage)/100)).toFixed(3);
        if(karigar_type == 1)
        {
            total_mc_value = parseFloat(parseFloat(total_mc_value)+parseFloat(parseFloat(parseFloat(total_mc_value*3))/100)).toFixed(2);
        }
        console.log(total_mc_value);
        if(ratecaltype==2)
        {
            item_cost   = parseFloat((parseFloat(parseFloat(purewt)+parseFloat(total_wt))*parseFloat(rate_per_gram))+parseFloat(total_mc_value)+parseFloat(other_metal_amount) + parseFloat(other_charges_amount)+parseFloat(stone_price)).toFixed(2);
        }else
        {
            item_cost   = parseFloat((parseFloat(parseFloat(tot_pcs)+parseFloat(total_wt))*parseFloat(rate_per_gram))).toFixed(2);
        }
        
    }
    $('#tot_purewt').val(parseFloat(purewt).toFixed(3));
    $('#item_cost').val(item_cost);
}

function validateBillEntryForm()
{
    var row_validate=true;
   /* if($('#select_category').val()=='' || $('#select_category').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Category."});
        row_validate=false;
    }
    else if($("input[type='radio'][name='order[po_type]']:checked").val() != 3 && ($('#select_purity').val()=='' || $('#select_purity').val()==null))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Purity."});
        row_validate=false;
    }
    else
    { */
        if($('#item_detail > tbody tr').length>0)
        {
            $('#item_detail > tbody tr').each(function(bidx, brow){
                curRow = $(this);
                // || curRow.find('.id_design').val()=='' || curRow.find('.id_sub_design').val()==''
                if(curRow.find('.tot_pcs').val()=='' || curRow.find('.tot_purewt').val()=='' || curRow.find('.id_product').val()=='')
                {
                    row_validate=false;
                }
           });
        }
        else
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Fill The Required Fields."});
            row_validate=false;
        }
        
    /* } */
    
    return row_validate;
}



$('#submit_pur_entry').on('click',function(){
    
    if(validateBillEntryForm())
    {
        $('#submit_pur_entry').prop('disabled',true);
        $("div.overlay").css("display", "block"); 
        var form_data=$('#purhcase_entry_form').serialize();
    	$('#pay_submit').prop('disabled',true);
    	if($('#po_id').val()!='' && $('#po_id').val() !=  undefined)
    	{
    	    var url=base_url+ "index.php/admin_ret_purchase_approval/approvalstock/approval_update?nocache=" + my_Date.getUTCSeconds();
    	}else
    	{
    	    var url=base_url+ "index.php/admin_ret_purchase_approval/approvalstock/approval_save?nocache=" + my_Date.getUTCSeconds();
    	}
    	my_Date = new Date();
        $.ajax({ 
            url:url,
            data: form_data,
            type:"POST",
            dataType:"JSON",
            success:function(data){
    			if(data.status)
    			{
    			    $("div.overlay").css("display", "none"); 
    			}
    			window.location.href= base_url+'index.php/admin_ret_purchase_approval/approvalstock/list';
            },
            error:function(error)  
            {	
                $("div.overlay").css("display", "none"); 
            } 
        });
    }
    
    
});

$('#po_item_search').on('click',function(){
    $('#item_detail tbody').empty();
    if($('#select_po_ref_no').val()=='')
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Ref No."});
    }else
    {
        get_purchase_issue_entry_items();
    }
});

function get_purchase_issue_entry_items()
{
    $("div.overlay").css("display", "block"); 
     	my_Date = new Date();
        $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/get_purchase_issue_entry_items?nocache=" + my_Date.getUTCSeconds(),
        data:{'po_id':$('#select_po_ref_no').val()},
        dataType:"JSON",
        type:"POST",
        success:function(data){
			if(data.length>0)
			{
			    var trHtml='';
			     var rowExists=false;
			    $.each(data,function(key,items){
			        
			         $('#item_detail > tbody tr').each(function(bidx, brow){
                    curRow = $(this);
                        if(curRow.find('.po_item_id').val()==items.po_item_id)
    					{
    					    rowExists=true;
    					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
    					} 
                   });
                   if(!rowExists)
                   {
                       let stone_details=JSON.stringify(items.stone_details);
			           trHtml+='<tr class="'+items.po_item_id+'">'
			              +'<td><input type="hidden" class="po_item_id"  name="order_items[po_item_id][]" value="'+items.po_item_id+'"/><input type="hidden" name="order_items[no_of_pcs][]" class="no_of_pcs" value="'+items.no_of_pcs+'"><input type="hidden" name="order_items[gross_wt][]" class="gross_wt" value="'+items.gross_wt+'"><input type="hidden" name="order_items[net_wt][]" class="net_wt" value="'+items.net_wt+'"><input type="hidden" name="order_items[less_wt][]" class="less_wt" value="'+items.less_wt+'">'+items.po_item_id+'</td>'
			             /* +'<td>'+items.category_name+'</td>'*/
			              +'<td>'+items.product_name+'</td>'
			              +'<td>'+items.design_name+'</td>'
			              +'<td>'+items.sub_design_name+'</td>'
			              +'<td>'+items.no_of_pcs+'</td>'
			              +'<td>'+items.gross_wt+'</td>'
			              +'<td>'+items.less_wt+'</td>'
			              +'<td>'+items.net_wt+'</td>'
			              +'<td><input type="number" name="order_items[failed_pcs][]" class="form-control failed_pcs" value="0"></td>'
			              +'<td><input type="number" name="order_items[failed_gwt][]" class="form-control failed_wt" value="0"></td>'
			              +'<td><input type="hidden" class="stone_details" name="order_items[stone_details][]" value=\''+stone_details+'\'>'+((items.stone_details).length>0 ? '<a href="#" onClick="create_new_empty_po_stone_items($(this).closest(\'tr\'));" class="btn btn-success  btn-sm"><i class="fa fa-plus"></i></a>' : '-')+'</td>'
			              +'<td><input type="number" name="order_items[qc_acc_pcs][]" class="form-control qc_acc_pcs" value="'+items.no_of_pcs+'" readonly></td>'
			              +'<td><input type="number" name="order_items[qc_acc_gwt][]" class="form-control qc_acc_gwt" value="'+items.gross_wt+'" readonly></td>'
			              +'<td><input type="number" name="order_items[qc_acc_lwt][]" class="form-control qc_acc_lwt" value="'+items.less_wt+'" readonly></td>'
			              +'<td><input type="number" name="order_items[qc_acc_nwt][]" class="form-control qc_acc_nwt" value="'+items.net_wt+'" readonly></td>'
			              +'</tr>';
                   }
			        
			    });
			    $('#item_detail tbody').append(trHtml);
			}else
			{
			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Record Found."});
			}
			$("div.overlay").css("display", "none"); 
        },
        error:function(error)  
        {	
            $("div.overlay").css("display", "none"); 
        } 
    });
}


function create_new_empty_po_stone_items(curRow)
{
    $('#cus_stoneModal').modal('show');
     $('#estimation_stone_cus_item_details tbody').empty();
    $('#activeRow').val(curRow.closest('tr').attr('class'));
    var stone_details=JSON.parse(curRow.find('.stone_details').val());
    var trHtml='';
    $.each(stone_details,function(key,i){
        trHtml+='<tr>'
                    +'<td><input type="hidden" class="po_st_id" value="'+i.po_st_id+'">'+i.po_st_id+'</td>'
                    +'<td><input type="hidden" class="stone_name" value="'+i.stone_name+'">'+i.stone_name+'</td>'
                    +'<td><input type="hidden" class="form-control po_stone_pcs" value="'+i.po_stone_pcs+'">'+i.po_stone_pcs+'</td>'
                    +'<td><input type="hidden" class="form-control po_stone_wt" value="'+i.po_stone_wt+'">'+i.po_stone_wt+'</td>'
                    +'<td><input type="number" class="form-control po_stone_rejected_pcs" value="'+i.po_stone_rejected_pcs+'" style="width:80px;"></td>'
                    +'<td><input type="number" class="form-control po_stone_rejected_wt" value="'+i.po_stone_rejected_wt+'" style="width:80px;"></td>'
                    +'<td><input type="number" class="form-control po_stone_accepted_pcs" value="'+i.po_stone_pcs+'" style="width:80px;" readonly></td>'
                    +'<td><input type="number" class="form-control po_stone_accepted_wt" value="'+i.po_stone_wt+'" style="width:80px;" readonly></td>'
                    +'<td><input type="number" class="form-control stone_rate" value="'+i.stone_rate+'" style="width:80px;" style="width:80px;" readonly></td>'
                    +'<td><input type="number" class="form-control po_stone_accepted_amount" value="'+i.po_stone_amount+'" style="width:80px;" readonly></td>'
                +'</tr>';
    });
    $('#estimation_stone_cus_item_details tbody').append(trHtml);
}

$(document).on('keyup','.po_stone_rejected_pcs',function(e){
    var row = $(this).closest('tr'); 
    var tot_pcs=row.find('.po_stone_pcs').val();
    var failed_pcs=row.find('.po_stone_rejected_pcs').val();
    if(parseFloat(tot_pcs)<parseFloat(failed_pcs))
    {
        row.find('.po_stone_rejected_pcs').val('');
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Pieces."});
    }
    calculate_stone_accepted_details();
});

$(document).on('keyup','.po_stone_rejected_wt',function(e){
    var row = $(this).closest('tr'); 
    var gross_wt=row.find('.po_stone_wt').val();
    var failed_wt=row.find('.po_stone_rejected_wt').val();
    if(parseFloat(gross_wt)<parseFloat(failed_wt))
    {
        row.find('.po_stone_rejected_wt').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Weight."});
    }
    calculate_stone_accepted_details();
});


function calculate_stone_accepted_details()
{
     
    $('#estimation_stone_cus_item_details > tbody tr').each(function(bidx, brow){
        curRow = $(this);
        
        var total_po_stone_pcs=0;
        var total_po_stone_wt=0;
        var total_rejected_pcs=0;
        var total_rejected_gwt=0;
        var accepted_wt=0;
   
   
        total_issue_pcs=parseFloat(isNaN(curRow.find('.po_stone_pcs').val()) || curRow.find('.po_stone_pcs')=='' ? 0 :curRow.find('.po_stone_pcs').val());
        total_issue_gwt=parseFloat(isNaN(curRow.find('.po_stone_wt').val()) || curRow.find('.po_stone_wt')=='' ? 0 :curRow.find('.po_stone_wt').val());
         
        total_rejected_pcs=parseFloat(isNaN(curRow.find('.po_stone_rejected_pcs').val()) || curRow.find('.po_stone_rejected_pcs')=='' ? 0 :curRow.find('.po_stone_rejected_pcs').val());
        total_rejected_gwt=parseFloat(isNaN(curRow.find('.po_stone_rejected_wt').val()) || curRow.find('.po_stone_rejected_wt')=='' ? 0 :curRow.find('.po_stone_rejected_wt').val());
       
        accepted_wt=parseFloat(parseFloat(total_issue_gwt)-parseFloat(total_rejected_gwt)).toFixed(3);
        
        var stone_rate=parseFloat(isNaN(curRow.find('.stone_rate').val()) || curRow.find('.stone_rate')=='' ? 0 :curRow.find('.stone_rate').val());
        
        curRow.find('.po_stone_accepted_pcs').val(parseFloat(parseFloat(total_issue_pcs)-parseFloat(total_rejected_pcs)).toFixed(3));
        curRow.find('.po_stone_accepted_wt').val(accepted_wt);
        
        curRow.find('.po_stone_accepted_amount').val(parseFloat(parseFloat(accepted_wt)*parseFloat(stone_rate)).toFixed(2));
        
    });
}


$('#cus_stoneModal  #remove_stone_details').on('click', function(){
    var stone_details=[];
    var activeRow=$('#activeRow').val();
      $("#estimation_stone_cus_item_details tbody tr").each(function(index, value){
              stone_details.push({
                        'po_in_lwt'                 : $(this).find('.po_in_lwt').val(),
    		            'po_st_id'                  : $(this).find('.po_st_id').val(),
    		            'stone_name'                : $(this).find('.stone_name').val(),
    		            'po_stone_pcs'              : $(this).find('.po_stone_pcs').val(),
    		            'po_stone_wt'               : $(this).find('.po_stone_wt').val(),
    		            'stone_rate'                : $(this).find('.stone_rate').val(),
    		            'po_stone_amount'           : $(this).find('.po_stone_amount').val(),
    		            'po_stone_rejected_pcs'     : $(this).find('.po_stone_rejected_pcs').val(),
    		            'po_stone_rejected_wt'      : $(this).find('.po_stone_rejected_wt').val(),
    		            'po_stone_accepted_pcs'     : $(this).find('.po_stone_accepted_pcs').val(),
    		            'po_stone_accepted_wt'      : $(this).find('.po_stone_accepted_wt').val(),
    		});
      });
      $("#estimation_stone_cus_item_details tbody").empty();
      $('#cus_stoneModal').modal('toggle');
      $('.'+activeRow).find('.stone_details').val(JSON.stringify(stone_details));
      console.log(stone_details);
      if(ctrl_page[2]=='hm_receipt')
      {
          calculate_halmarking_receipt_row_details();
      }else
      {
          qcAccepted_items();
      }
      
});




$(document).on('keyup','.failed_pcs',function(e){
    var row = $(this).closest('tr'); 
    var tot_pcs=row.find('.no_of_pcs').val();
    var failed_pcs=row.find('.failed_pcs').val();
    if(parseFloat(tot_pcs)<parseFloat(failed_pcs))
    {
        row.find('.failed_pcs').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Pieces."});
    }
    qcAccepted_items();
});


$(document).on('keyup','.failed_wt',function(e){
    var row = $(this).closest('tr'); 
    var gross_wt=row.find('.gross_wt').val();
    var failed_wt=row.find('.failed_wt').val();
    if(parseFloat(gross_wt)<parseFloat(failed_wt))
    {
        row.find('.failed_wt').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Weight."});
    }
    qcAccepted_items();
});


function qcAccepted_items()
{
    $('#item_detail > tbody tr').each(function(idx, row){
         curRow = $(this); 
         var stone_Details  = JSON.parse(curRow.find('.stone_details').val());
         var rejected_stn_wt =0;
         $.each(stone_Details,function(k,stn){
            rejected_stn_wt+=parseFloat(stn.po_stone_rejected_wt);
         });
         var total_pcs      = (isNaN(curRow.find('.no_of_pcs').val()) || curRow.find('.no_of_pcs').val()=='' ? 0 :curRow.find('.no_of_pcs').val());
         var total_gross_wt = (isNaN(curRow.find('.gross_wt').val()) || curRow.find('.gross_wt').val()=='' ? 0 :curRow.find('.gross_wt').val());
         var total_less_wt  = (isNaN(curRow.find('.less_wt').val()) || curRow.find('.less_wt').val()=='' ? 0 :curRow.find('.less_wt').val());
         var total_nwt      = (isNaN(curRow.find('.less_wt').val()) || curRow.find('.net_wt').val()=='' ? 0 :curRow.find('.net_wt').val());
         
         var rejected_pcs   = (isNaN(curRow.find('.net_wt').val()) || curRow.find('.failed_pcs').val()=='' ? 0 :curRow.find('.failed_pcs').val());
         var rejected_gwt   = (isNaN(curRow.find('.failed_wt').val()) || curRow.find('.failed_wt').val()=='' ? 0 :curRow.find('.failed_wt').val());
         
         var qc_accepted_pcs = parseFloat(parseFloat(total_pcs)-parseFloat(rejected_pcs)).toFixed(2);
         var qc_accepted_gwt = parseFloat(parseFloat(total_gross_wt)-parseFloat(rejected_gwt)).toFixed(3);
         var qc_accepted_lwt = parseFloat(parseFloat(total_less_wt)-parseFloat(rejected_stn_wt)).toFixed(3);
         var qc_accepted_nwt = parseFloat(parseFloat(qc_accepted_gwt)-parseFloat(qc_accepted_lwt)).toFixed(3);
         
         curRow.find('.qc_acc_pcs').val(qc_accepted_pcs);
         curRow.find('.qc_acc_gwt').val(qc_accepted_gwt);
         curRow.find('.qc_acc_lwt').val(qc_accepted_lwt);
         curRow.find('.qc_acc_nwt').val(qc_accepted_nwt);
    });
}


$('#update_qc_status').on('click',function(){
        if($('#select_po_ref_no').val()=='' || $('#select_po_ref_no').val()==null)
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select PO Ref No"});
        }
        else if($('#item_detail > tbody > tr').length==0)
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
        }
        else
        {
            $("div.overlay").css("display", "block"); 
            $('#update_qc_status').prop('disabled',true);
            var form_data=$('#qc_entry_form').serialize();
        	$('#pay_submit').prop('disabled',true);
        	var url=base_url+ "index.php/admin_ret_purchase/update_qc_status?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours();
            $.ajax({ 
                url:url,
                data: form_data,
                type:"POST",
                dataType:"JSON",
                success:function(data){
        			if(data.status)
        			{
        			    $("div.overlay").css("display", "none"); 
        			    window.location.href= base_url+'index.php/admin_ret_purchase/qc_issue_receipt/list';
        			}
        			
                },
                error:function(error)  
                {	
                    $("div.overlay").css("display", "none"); 
                } 
            });
        }
});

function update_qc_status(req_data)
{
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/update_qc_status?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'req_data':req_data,'po_id':$('#select_po_ref_no').val()},
        type:"POST",
        async:false,
        dataType:"JSON",
        success:function(data){
            if(data.status)
            {
                 $("div.overlay").css("display", "none");
                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                location.reload(false);
            }else
            {
                 $("div.overlay").css("display", "none");
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
            }
            $('#set_green_tag').prop('disabled',false);
            
            
        },
        error:function(error)  
        {
            console.log(error);
            $("div.overlay").css("display", "none"); 
        }	 
    });
}

//Purchase Entry



//Other Charges

function get_ActivePurity()
{
    $("div.overlay").css("display", "block"); 
    $.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_catalog/ajax_getPurity',
	dataType:'json',
	success:function(data){
	        purityDetails=data;
	    	$("div.overlay").css("display", "none"); 
		}
	});
}

$(".add_other_metals").on('click',function(){
    open_other_metal_modal();
});

function open_other_metal_modal(){
    $('#other_metalmodal').modal('show');
    if( $('#other_metal_table tbody > tr').length==0)
    {
         create_new_empty_other_metal_item();     
    }
   
}

$(".add_other_charges").on('click',function(){
    open_other_charges_modal();
});

$(document).on('click', '#table_charges tbody tr .create_charge_item_details, .add_charges', function(){

    if(validate_charges_row()){

		create_new_empty_other_charges_item();

	}else{

		alert("Please fill required charge fields");

	}

});

$('#cus_chargeModal  #update_charge_details').on('click', function(){

	if(validate_charges_row())

    {

		var charge_details=[];

		var charge_value=0;
		
		var charge_value_with_tax = 0;

		modalChargeDetail = []; // Reset Old Value of charge modal

		$('#cus_chargeModal .modal-body #table_charges> tbody  > tr').each(function(index, tr) {

			charge_value += parseFloat($(this).find('.chargesValue').val());
			
			var char_tax        = 0;
			var char_tax_value  = 0;
			var selected_charge = $(this).find('.chargesType').val();
			$.each(charges_details, function (ckey, citem) {
			    if(parseInt(selected_charge) == parseInt(citem.id_charge)){
			        
			        char_tax= parseFloat(citem.charge_tax);
			        
			    }
        		
        	});
			
			var char_with_tax = parseFloat(parseFloat($(this).find('.chargesValue').val()) * ((100 + char_tax) / 100)).toFixed(2);
			
			char_tax_value     = parseFloat(parseFloat($(this).find('.chargesValue').val()) * ((char_tax) / 100)).toFixed(2);
			
			charge_value_with_tax += parseFloat(char_with_tax);
            
			charge_details.push({

						'charge_value'       : $(this).find('.chargesValue').val(),

						'charge_id'          : $(this).find('.chargesType').val(),

						'name_charge'        : $(this).find('.chargesType :selected').text(),
						
						'char_with_tax'      : char_with_tax,
						
						'charge_tax'         :  char_tax,
						
						'charge_tax_value'   :  char_tax_value,

						});

		});

		modalChargeDetail = charge_details;

		console.log(modalChargeDetail);

		// Update charge Summary

        $("#other_charges_amount").val(parseFloat(charge_value_with_tax).toFixed(2));

		$("#other_charges_details").val(JSON.stringify(charge_details));

		

        //$('#cus_chargeModal .modal-body').find('#table_charges tbody').empty();

        $('#cus_chargeModal').modal('hide');
        
        calculate_purchase_item_cost();
        
        calculate_FinalCost();
        
        calculate_purchase_return_final_cost();
        
        calculate_grn_final_cost();

    }

    else

    {

    	alert('Please Fill The Required charge Details');

    }

});

function open_other_charges_modal(){
    $('#cus_chargeModal').modal('show');
    if( $('#table_charges tbody > tr').length==0)
    {
         create_new_empty_other_charges_item();     
    }
   
}


function create_new_empty_other_metal_item()
{
   
    var trHtml='';
    var metal='<option value="">Select Metal</option>';
    var purity='<option value="">Select Purity</option>';
    $.each(category_lists, function (mkey, mitem) {
	    metal += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
	});

	$.each(purityDetails, function (k, p) {
		purity += "<option value='"+p.id_purity+"'>"+p.purity+"</option>";
	});
    
    
   
       trHtml+='<tr>'
          +'<td><select class="form-control select_metal">'+metal+'</td>'
          +'<td><select class="form-control select_purity">'+purity+'</td>'
          +'<td><input type="number" class="form-control pcs"></td>'
          +'<td><input type="number" class="form-control gwt"></td>'
          +'<td ><input type="number" class="form-control wastage_perc" value="0"><input type="hidden" class="wast_wt" value="0"></td>'
          +'<td ><select class="form-control calc_type"><option value="">Mc Type</option><option value="1" selected>Per Gram</option><option value="2">Per Piece</option></select></td>'
          +'<td ><input type="number" class="form-control making_charge" value="0"><input type="hidden" class="mc_value" value="0"></td>'
          +'<td><input type="number" class="form-control rate_per_gram"></td>'
          +'<td><input type="number" class="form-control amount"></td>'
           +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
          +'</tr>';
   
   
     $('#other_metal_table tbody').append(trHtml);
}


function create_new_empty_other_charges_item(selData = [])

{

    console.log(selData);

	let charges_validated = validate_charges_row();

	if(charges_validated) {

		let options = '';

		$(charges_list).each(function(idx, charges){

			options += "<option value='"+charges.id_charge+"' "+(selData ? (charges.id_charge == selData.charge_id ? 'selected' : '') : '')+">"+charges.name_charge+"</option>";

		});

	    let row_cls = $('#table_charges tbody tr').length;

		let _row_last = $('#table_charges tbody tr:last');

		let sno = (_row_last.length > 0 ? parseInt(_row_last.find('.sno').text()) : 0)+1;

		let new_row = "<tr class='ch_"+row_cls+"'><td class='sno'>"+sno+"</td><td><select class='form-control chargesType'><option value=''>--Select--</option>"+options+"</select></td><td><input type='text' value='"+(selData ? (selData.charge_value == undefined ? 0 : selData.charge_value) : '')+"' class='form-control chargesValue' /></td><td class='chargeModal_buttons'></td></tr>";

		$('#cus_chargeModal .modal-body').find('#table_charges tbody').append(new_row);

		$('#cus_chargeModal .modal-body').find('#table_charges tbody ch_'+row_cls+ '.chargesType').focus();

		addChargeModal_buttons();

		$("#cus_chargeModal").on('shown.bs.modal', function(){

            $(this).find('.chargesType').focus();

        });

	}

}

function remove_charge(obj)

{

	$(obj).closest('tr').remove();

	addChargeModal_buttons();

}

function addChargeModal_buttons()

{

	let charge_rows = $("#table_charges tbody");

	$(charge_rows).find('.create_charge_item_details').remove();

	$(charge_rows).find('.remove_charge_item_details').remove();

	$(charge_rows).find(".chargeModal_buttons:last").prepend('<button type="button" class="btn btn-success btn-xs create_charge_item_details"><i class="fa fa-plus"></i></button>');

	$(charge_rows).find(".chargeModal_buttons").append('<button type="button" class="btn btn-danger btn-xs remove_charge_item_details" onclick="remove_charge(this)"><i class="fa fa-trash"></i></button>');

}

function validate_charges_row() {

	let row_validated = true;

	let charges_row = $("#table_charges tbody tr");

	$(charges_row).each(function(idx, row){

		let rowChargeType = $('.row').find('.chargesType').val();

		let rowChargesValue = $('.row').find('.chargesValue').val();

		if(!(rowChargeType > 0) || !(rowChargesValue > 0)) {

			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please fill the previous row charges!"});

			row_validated = false;

			return false;

		}

	});

	return row_validated;

}

$('#create_other_metal_item_details').on('click',function(){
    if(validate_other_metal_row())
    {
        create_new_empty_other_metal_item();
    }else
    {
        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>Please Fill The Required charge Details"});
    }
});


$('#other_metalmodal  #update_other_metal_details').on('click', function(){
	if(validate_other_metal_row())
    {
    	var metal_details=[];
    	var tot_amount=0;
    	var tot_weight=0;
    	var tot_wast_wt=0;
    	var tot_mc_value=0;
    	$('#other_metalmodal .modal-body #other_metal_table> tbody  > tr').each(function(index, tr) {
    		tot_amount+=parseFloat($(this).find('.amount').val());
    		tot_weight+=parseFloat($(this).find('.gwt').val());
    		tot_wast_wt+=parseFloat($(this).find('.wast_wt').val());
    		tot_mc_value+=parseFloat($(this).find('.mc_value').val());
    		metal_details.push({
    		            'id_metal'      : $(this).find('.select_metal').val(),
    		            'id_purity'     : $(this).find('.select_purity').val(),
    		            'pcs'           : $(this).find('.pcs').val(),
    		            'gwt'           : $(this).find('.gwt').val(),
    		            'wastage_perc'  : $(this).find('.wastage_perc').val(),
    		            'calc_type'     : $(this).find('.calc_type').val(),
    		            'making_charge' : $(this).find('.making_charge').val(),
    		            'rate_per_gram' : $(this).find('.rate_per_gram').val(),
    		            'amount'        : $(this).find('.amount').val(),
    		            'wast_wt'       : $(this).find('.wast_wt').val(),
    		            'mc_value'      : $(this).find('.mc_value').val(),
    		            });
    	});
    	$('#other_metal_details').val(JSON.stringify(metal_details));
    	$('#other_metal_amount').val(tot_amount);
    	$('#other_metal_wt').val(tot_weight);
    	$('#other_metal_wast_wt').val(tot_wast_wt);
    	$('#other_metal_mc_amount').val(tot_mc_value);
        $('#other_metalmodal').modal('hide');
        calculate_purchase_item_cost();
    }
    else
    {
        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>Please Fill The Required Details"});
    }
});


function validate_other_metal_row()
{
    var row_validate = true;
	$('#other_metal_table > tbody  > tr').each(function(index, tr) {
		if($(this).find('.select_metal').val() == "" || $(this).find('.select_purity').val() == "" || $(this).find('.pcs').val() == "" || $(this).find('.gwt').val() == "" || $(this).find('.rate_per_gram').val() == ""){
			row_validate = false;
		}
	});
	return row_validate;
}

$(document).on('change','.calc_type',function(){
    calculate_other_metal_amount();
});


$(document).on('keyup','.rate_per_gram,.gwt,.wastage_perc,.making_charge',function(){
    calculate_other_metal_amount();
});

function calculate_other_metal_amount()
{
    var tot_pcs=0;
    var tot_gwt=0;
    var tot_amount=0;
    $('#other_metal_table > tbody  > tr').each(function(index, tr) {
        row = $(this);
        var piece           = (isNaN(row.find('.pcs').val()) || row.find('.pcs').val()=='' ? 0:row.find('.pcs').val());
        var gross_wt        = (isNaN(row.find('.gwt').val()) || row.find('.gwt').val()=='' ? 0:row.find('.gwt').val());
        var wastage_perc    = (isNaN(row.find('.wastage_perc').val()) || row.find('.wastage_perc').val()=='' ? 0:row.find('.wastage_perc').val());
        var rate_per_gram   = (isNaN(row.find('.rate_per_gram').val()) || row.find('.rate_per_gram').val()=='' ? 0:row.find('.rate_per_gram').val());
        var calc_type       = (isNaN(row.find('.calc_type').val()) || row.find('.calc_type').val()=='' ? 0:row.find('.calc_type').val());
        var wast_wt         = parseFloat((gross_wt*wastage_perc)/100);
        var mc_type         = (row.find('.calc_type').val()=='' ? 0:row.find('.calc_type').val());
        var making_charge   = (row.find('.making_charge').val()=='' ? 0 : row.find('.making_charge').val());
        var mc_value        = (mc_type==1 ? parseFloat(gross_wt*making_charge) : (mc_type==2 ? parseFloat(making_charge*piece) :0));
        var total_amount    = parseFloat(parseFloat(rate_per_gram)*parseFloat(parseFloat(gross_wt)+parseFloat(wast_wt))+parseFloat(mc_value));
        row.find('.amount').val(parseFloat(total_amount).toFixed(2));
        row.find('.wast_wt').val(parseFloat(wast_wt).toFixed(3));
        row.find('.mc_value').val(parseFloat(mc_value).toFixed(3));
        
        tot_pcs+=parseFloat(piece);
        tot_gwt+=parseFloat(gross_wt);
        tot_amount+=parseFloat(total_amount);
        
        console.log('wast_wt:'+wast_wt);
        console.log('total_amount:'+total_amount);
        console.log('mc_value:'+mc_value);
        
    });
    
    $('.total_pcs').html(parseFloat(tot_pcs));
    $('.total_wt').html(parseFloat(tot_gwt).toFixed(3));
    $('.total_amount').html(parseFloat(tot_amount).toFixed(2));
}

$(document).on('change',".chargesType",function(){
   var row = $(this).closest('tr');
   var charge_type=this.value;
   $(charges_list).each(function(idx, charges){
        if(charges.id_charge == charge_type)
        {
            row.find('.chargesValue').val(charges.value_charge);
        }
	});
});

$(document).on('change',".stones_type",function(){
   var row = $(this).closest('tr');
   var stone_type=this.value;
   row.find('.stone_id').html('');
   	var stones_list = "<option value=''>-Select Stone-</option>";
   $.each(stones, function (pkey, pitem) {
        if(pitem.stone_type==stone_type)
        {
            stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";
        }
	});
	 row.find('.stone_id').append(stones_list);
});

function create_new_stone_row()
{
	var stones_list = "<option value=''>-Select Stone-</option>";
	var stones_type = "<option value=''>-Stone Type-</option>";
	var uom_list = "<option value=''>-UOM-</option>";
	$.each(stones, function (pkey, pitem) {
		stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";
	});
	$.each(stone_types, function (pkey, pitem) {
		stones_type += "<option value='"+pitem.id_stone_type+"'>"+pitem.stone_type+"</option>";
	});
	$.each(uom_details, function (pkey, pitem) {
		uom_list += "<option value='"+pitem.uom_id+"'>"+pitem.uom_name+"</option>";
	});
	
	var length=(($('#cus_stoneModal tbody tr').length)+1);
	


	var row='';
        row += '<tr>'
            +'<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]" value="1" checked ></td>'
            //+'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:100px;"><option value="">-Select-</option><option value=1>Yes</option><option value=0>No</option></select></td>' 
        	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]">'+stones_type+'</select></td>'
			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"></select></td>'
			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="" style="width: 100%;"/></td>'
			+'<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'
			+'<td><div class="form-group"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+length+']" value="1" checked="true">Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+length+']" class="stone_cal_type" value="2">Pcs</div></td>'
			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value=""  style="width:100%;"/></td>'
			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value=""  style="width:100%;" /></td>'
			+'<td><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';
	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);
}

function get_stones(stone_type=""){
    stones=[];
	$.ajax({		
	 	type: 'POST',		
	 	url : base_url + 'index.php/admin_ret_tagging/getStoneItems',
	 	dataType : 'json',
	 	data:{'stone_type':stone_type},
	 	success  : function(data){
		 	stones = data;
		 	console.log("stones",stones);
	 	}	
	}); 
}
function get_stone_types(){
	$.ajax({		
	 	type: 'GET',		
	 	url : base_url + 'index.php/admin_ret_tagging/getStoneTypes',
	 	dataType : 'json',		
	 	success  : function(data){
		 	stone_types = data;
	 	}	
	}); 
}

function get_ActiveUOM(){
	$.ajax({		
	 	type: 'GET',		
	 	url : base_url + 'index.php/admin_ret_tagging/get_ActiveUOM',
	 	dataType : 'json',		
	 	success  : function(data){
		 	uom_details = data;
	 	}	
	}); 
}

$(".add_po_lwt").on('click',function(){
	 openStoneModal();
});

function openStoneModal(){
    $('#cus_stoneModal').modal('show');
    $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();
    if(modalStoneDetail.length > 0){
        $.each(modalStoneDetail, function (key, item) {
	        if(item){
                create_new_empty_stone_item(item);  
	        }
        })
    }else{
        create_new_empty_stone_item();     
    }
}


function create_new_empty_stone_item(stn_data=[])
{
    console.log(stn_data);
    /*if(stn_data)
    {
        $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();
    }*/
	var stones_list = "<option value=''> -Select Stone- </option>";
	var stones_type = "<option value=''>-Stone Type-</option>";
	var uom_list = "<option value=''>-UOM-</option>";
	$.each(stones, function (pkey, pitem) {
		stones_list += "<option value='"+pitem.stone_id+"' "+(stn_data ? (pitem.stone_id == stn_data.stone_id ? 'selected' : '') : '')+">"+pitem.stone_name+"</option>";
	});
	$.each(uom_details, function (pkey, pitem) {
		uom_list += "<option value='"+pitem.uom_id+"' "+(stn_data ? (pitem.uom_id == stn_data.stone_uom_id ? 'selected' : '') : '')+">"+pitem.uom_name+"</option>";
	});
	$.each(stone_types, function (pkey, pitem) {
		stones_type += "<option value='"+pitem.id_stone_type+"' "+(stn_data ? (pitem.id_stone_type == stn_data.stones_type ? 'selected' : '') : '')+">"+pitem.stone_type+"</option>";
	});
	var show_in_lwt = (stn_data ? stn_data.show_in_lwt : '');
	var stone_pcs = (stn_data ? (stn_data.stone_pcs == undefined ? '':stn_data.stone_pcs) : '');
	var stone_wt = (stn_data ? (stn_data.stone_wt == undefined ? '':stn_data.stone_wt) : '');
	var rate = (stn_data ? (stn_data.stone_rate == undefined ? 0:stn_data.stone_rate): 0);
	var price = (stn_data ? (stn_data.stone_price == undefined ? 0:stn_data.stone_price) : 0);
	var cal_type = (stn_data ? (stn_data.stone_cal_type == undefined ? 1:stn_data.stone_cal_type) : 1);
		
	var row_cls = $('#estimation_stone_cus_item_details tbody tr').length;
        row = '<tr id="'+$('#estimation_stone_cus_item_details tbody tr').length+'" class="st_'+$('#estimation_stone_cus_item_details tbody tr').length+'">' 
            +'<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]" value="'+(show_in_lwt==1 ? 1:0)+'" '+(show_in_lwt==1 ? 'checked' :'')+' ></td>'
            //+'<td><select class="show_in_lwt form-control" name="est_stones_item[show_in_lwt][]" style="width:100px;"><option value="">-Select-</option><option value=1 '+(show_in_lwt==1 ? 'selected' :'')+'>Yes</option><option value=0 '+(show_in_lwt==0 ? 'selected' :'')+'>No</option></select></td>'
        	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]">'+stones_type+'</select></td>'
			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]">'+stones_list+'</select><input type="hidden" class="stone_type" value=""></td>'
			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="'+stone_pcs+'" style="width: 60px;"/></td>'
			+'<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="'+stone_wt+'" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'
			+'<td><div class="form-group"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" value="1" '+(cal_type == 1 ? 'checked' : '')+'>Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+parseFloat(row_cls+1)+']" class="stone_cal_type" value="2" '+(cal_type == 2 ? 'checked' : '')+'>Pcs</div></td>'
			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="'+rate+'" /></td>'
			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+price+'" "/></td>'
			+'<td><button type="button" class="btn btn-success btn-xs create_stone_item_details"><i class="fa fa-plus"></i></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-xs btn-del"><i class="fa fa-trash"></i></a></td></tr>';
	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);
	//$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody .st_'+row_cls+ '.show_in_lwt').focus();
	$("#cus_stoneModal").on('shown.bs.modal', function(){
    });
	$('#custom_active_id').val("st_"+row_cls);
}


function validateStoneCusItemDetailRow(){
	var row_validate = true;
	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {
		if($(this).find('.stone_id').val() == "" || $(this).find('.stone_pcs').val() == "" || $(this).find('.stone_wt').val() == "" || $(this).find('.stone_rate').val() == "" || $(this).find('.stone_price').val() == "" || $(this).find('.stone_uom_id').val() == "" ){
			row_validate = false;
		}
	});
	return row_validate;
}

$(document).on('input',".stone_pcs,.stone_wt,.stone_rate",function(){
    calculate_stone_amount();
});

$(document).on('#pur_return_stone_item_details tbody input',".stone_pcs,.stone_wt,.stone_rate",function(){
    calculate_return_stone_amount();
});

$(document).on('change',".stone_cal_type",function(){
     $('#estimation_stone_cus_item_details > tbody tr').each(function(idx, row){
        curRow = $(this);   
        if(curRow.find('input[type=radio]:checked').val() == 1){ 
            // curRow.find('.stone_wt').attr('readonly', false);
            // curRow.find('.stone_uom_id').attr('disabled', false);
        }else{
            // curRow.find('.stone_wt').val(0);
            // curRow.find('.stone_wt').attr('readonly', true);
            //  curRow.find('.stone_uom_id').attr('disabled', true);
        }
     });
    calculate_stone_amount();
});

function calculate_stone_amount()
{
     $('#estimation_stone_cus_item_details > tbody tr').each(function(idx, row){
         curRow = $(this);   
         var stone_amt=0;
         var stone_pcs  = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();
         var stone_wt  = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();
         var stone_rate  = (isNaN(curRow.find('.stone_rate').val()) || curRow.find('.stone_rate').val() == '')  ? 0 : curRow.find('.stone_rate').val();
         //stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);
         
         if(curRow.find('input[type=radio]:checked').val() == 1){
            stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);
         }else{
           stone_amt = parseFloat(parseInt(stone_pcs)*parseFloat(stone_rate)).toFixed(2); 
         }
         
         curRow.find('.stone_price').val(stone_amt);
     });
}

function calculate_return_stone_amount()
{
     $('#pur_return_stone_item_details > tbody tr').each(function(idx, row){
         curRow = $(this);   
         var stone_amt=0;
         var stone_pcs  = (isNaN(curRow.find('.stone_pcs').val()) || curRow.find('.stone_pcs').val() == '')  ? 0 : curRow.find('.stone_pcs').val();
         var stone_wt  = (isNaN(curRow.find('.stone_wt').val()) || curRow.find('.stone_wt').val() == '')  ? 0 : curRow.find('.stone_wt').val();
         var stone_rate  = (isNaN(curRow.find('.stone_rate').val()) || curRow.find('.stone_rate').val() == '')  ? 0 : curRow.find('.stone_rate').val();
         //stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);
         
         if(curRow.find('input[type=radio]:checked').val() == 1){
            stone_amt = parseFloat(parseFloat(stone_wt)*parseFloat(stone_rate)).toFixed(2);
         }else{
           stone_amt = parseFloat(parseInt(stone_pcs)*parseFloat(stone_rate)).toFixed(2); 
         }
         
         curRow.find('.stone_price').val(stone_amt);
     });
}

$('#cus_stoneModal .modal-body #create_stone_item_details').on('click', function(){
if(validateStoneCusItemDetailRow()){
			create_new_empty_stone_entry_row();
		}else{
			alert("Please fill required fields");
		}
});
function create_new_empty_stone_entry_row()
{  
    var row = "";
	var stones_list = "<option value=''>-Select Stone-</option>";
	var stones_type = "<option value=''>-Stone Type-</option>";
	var uom_list = "<option value=''>-Stone UOM-</option>";
	$.each(stones, function (pkey, pitem) {
		stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";
	});
	
	$.each(stone_types, function (pkey, pitem) {
		stones_type += "<option value='"+pitem.id_stone_type+"'>"+pitem.stone_type+"</option>";
	});
	
	$.each(uom_details, function (pkey, pitem) {
		var selected = pitem.is_default == 1 ? "selected='selected'" : "";
		uom_list += "<option value='"+pitem.uom_id+"' "+selected+">"+pitem.uom_name+"</option>";
	});
	var rowId = $('#estimation_stone_cus_item_details tbody tr').length;
	var active_row = new Date().getTime();
        row += '<tr id="'+active_row+'">'
        	+'<td><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="1" checked></td>'
        	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]">'+stones_type+'</select></td>'
			+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"></select></td>'
			+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="" style="width: 100%;"/></td>'
			+'<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'
			+'<td><div class="form-group"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+rowId+']" value="1" checked="true"> By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+rowId+']" class="stone_cal_type" value="2">By Pcs</div></td>'
			+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value=""  style="width:100%;"/></td>'
			+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value=""  style="width:100%;"/></td>'
			+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';
	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);
}

$('#cus_stoneModal  #update_return_stn_details').on('click', function(){
	if(validateStoneCusItemDetailRow())
	{
    	var stone_details       =[];
    	var stone_price         =0;
    	var stone_wt            =0;
    	var certification_price =0;
    	var catRow              =$('#custom_active_id').val();
    	var gross_wt            =$('#'+catRow).find('.gross_wt').val();
    	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details > tbody  > tr').each(function(index, tr) {
    		stone_price+=parseFloat($(this).find('.stone_price').val());
    		stone_wt+=parseFloat($(this).find('.stone_wt').val());
        		stone_details.push({
        		            'show_in_lwt'       : $(this).find('.show_in_lwt').val(),
        		            'stone_id'          : $(this).find('.stone_id').val(),
        		            'stones_type'       : $(this).find('.stones_type').val(),
        		            'stone_pcs'         : $(this).find('.stone_pcs').val(),
        		            'stone_wt'          : $(this).find('.stone_wt').val(),
        		            'stone_cal_type'    : $(this).find('input[type=radio]:checked').val(),
        		            'stone_price'       : $(this).find('.stone_price').val(),
        		            'stone_rate'        : $(this).find('.stone_rate').val(),
        		            'stone_type'        : $(this).find('.stone_type').val(),
        		            'stone_uom_id'      : $(this).find('.stone_uom_id').val()
        		});
    	});
    	if(parseFloat(gross_wt)<parseFloat(stone_wt))
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Entered Weight is Grater Than The Gross Wt."});
    	}
    	else
    	{
            $('#cus_stoneModal').modal('toggle');
            $('#'+catRow).find('.ret_add_stone_wt').val(stone_price);
            $('#'+catRow).find('.stone_details').val(JSON.stringify(stone_details));
            var row = $('.'+catRow).closest('tr');
            $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();
    	}
    	if(ctrl_page[1] == 'purchasereturn' && ctrl_page[2] == 'add')
        {
           //calculate_returnItem_details($('#'+catRow));
           calculate_purchase_return_final_cost();
        }
    }
    else
    {
    	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Fill The Required Fields.."});
    }
});


$('#cus_stoneModal  #update_stone_details').on('click', function(){
	if(validateStoneCusItemDetailRow())
    {
    	var stone_details=[];
    	var stone_price=0;
    	var certification_price=0;
    	var tag_less_wgt = 0;
    	var tot_stone_wt  			= 0;
    	modalStoneDetail = []; // Reset Old Value of stone modal
    	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details> tbody  > tr').each(function(index, tr) {
    		stone_price+=parseFloat($(this).find('.stone_price').val());
    		
    		stone_details.push({
    		            'show_in_lwt'       : $(this).find('.show_in_lwt').val(),
    		            'stone_id'          : $(this).find('.stone_id').val(),
    		            'stones_type'       : $(this).find('.stones_type').val(),
    		            'stone_pcs'         : $(this).find('.stone_pcs').val(),
    		            'stone_wt'          : $(this).find('.stone_wt').val(),
    		            'stone_cal_type'    : $(this).find('input[type=radio]:checked').val(),
    		            'stone_price'       : $(this).find('.stone_price').val(),
    		            'stone_rate'        : $(this).find('.stone_rate').val(),
    		            'stone_type'        : $(this).find('.stone_type').val(),
    		            'stone_uom_id'      : $(this).find('.stone_uom_id').val(),
    		            'stone_name'        : $(this).find('.stone_id :selected').text()
    		});
    	});
    	modalStoneDetail = stone_details;
    	
    	
    	
            if(stone_details.length>0)
            {
                 $.each(stone_details, function (pkey, pitem) {
                     $.each(uom_details,function(key,item){
                         var stone_wt = 0;
                         if(item.uom_id==pitem.stone_uom_id)
                         {
                             
                             if(((item.uom_short_code).toLowerCase() =='ct') && (item.divided_by_value!=null && item.divided_by_value!='')) //For Carat Need to convert into gram
                             {
                                 stone_wt=parseFloat(parseFloat(pitem.stone_wt)/parseFloat(item.divided_by_value));
                             }else{
                                 stone_wt=pitem.stone_wt;
                             }
                             tot_stone_wt+=parseFloat(stone_wt);
                            if(parseInt(pitem.show_in_lwt) == 1){
                                tag_less_wgt+=parseFloat(stone_wt);    
                        	}
                         }
                     });
                     
                    
                 });
            }
       
    	
    	$("#tot_lwt").val(tag_less_wgt);
    	$("#stone_price").val(stone_price);
    	$("#stone_details").val(JSON.stringify(stone_details));
    	calculate_purchase_item_cost();
        $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();
        $('#cus_stoneModal').modal('hide');
        $('#tot_wastage_perc').focus();
    }
    else
    {
    	alert('Please Fill The Required Details');
    }
});


//Other Charges



//Quality Control Details



function get_qc_issue_purchase_orders(status)
{
    $(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_purchase/qc_issue_receipt/purchase_issue',
		dataType:'json',
		success:function(data){
            var po_id =  $('#select_po_ref_no').val();
            po_itemdetails=data;
            $.each(data, function (key, item) {
                $('#select_po_ref_no').append(
                $("<option></option>")
                .attr("value", item.po_id)
                .text(item.po_ref_no)
                );
            });
            
            $("#select_po_ref_no").select2({
                placeholder: "Select PO Ref No",
                allowClear: true
            });
            
            $("#select_po_ref_no").select2("val",(po_id!='' && po_id>0?po_id:''));
            $(".overlay").css("display", "none");	
		}
	});
}


function get_qc_receipt_purchase_orders()
{
    $(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_purchase/qc_issue_receipt/purchase_receipt',
		dataType:'json',
		success:function(data){
            var po_id =  $('#select_po_ref_no').val();
            po_itemdetails=data;
            $.each(data, function (key, item) {
                $('#select_po_ref_no').append(
                $("<option></option>")
                .attr("value", item.po_id)
                .text(item.po_ref_no)
                );
            });
            
            $("#select_po_ref_no").select2({
                placeholder: "Select PO Ref No",
                allowClear: true
            });
            
            $("#select_po_ref_no").select2("val",(po_id!='' && po_id>0?po_id:''));
            $(".overlay").css("display", "none");	
		}
	});
}



$('#select_po_ref_no').on('change',function(){
   if(this.value!='')
   {
       if(ctrl_page[1]=='qc_issue_receipt' && ctrl_page[2]=='add')
       {
           set_qc_issue_preview_detaails(this.value);
       }
       else if(ctrl_page[1]=='qc_issue_receipt' && ctrl_page[2]=='qc_entry')
       {
           $('#po_item_search').trigger('click');
       }
       else if(ctrl_page[1]=='halmarking_issue_receipt' && ctrl_page[2]=='add')
       {
           set_purchase_order_item_detaails(this.value);
       }
       else if(ctrl_page[1] == 'purchasereturn' && ctrl_page[2] == 'add')
       {
           get_qc_rejected_details_by_poid(this.value);
       }
       else if(ctrl_page[1] == 'rate_fixing' && ctrl_page[2] == 'add')
       {
           set_po_rate_fix_details(this.value);
       }
   }
});


$('#select_karigar').on('change',function(){
	if(this.value!='')
    {
		if(ctrl_page[1] == 'purchasereturn' && ctrl_page[2] == 'add')
		{
		    $('#select_po_ref_no').prop('disabled',false);
		    $('#tag_number').prop('disabled',false);
		    $('#old_tag_number').prop('disabled',false);
		    $('#select_product').prop('disabled',false);
		    $('#tag_history_search').prop('disabled',false);
		    $('#return_item_detail > tbody').empty();
		    $('#item_detail > tbody').empty();
		    returnitemlist = [];
		    updatereturncategory();
		    get_return_item_po_ref_nos();
		    get_purchase_return_total();
		    calculate_purchase_return_final_cost();
            //get_qc_rejected_details_by_supid(this.value);
		}
		else if(ctrl_page[2]=='order_delivery')
		{
		    get_karigar_pending_orders();
		}
		else if(ctrl_page[2]=='purchase_add')
		{
		    if($("input[type='radio'][name='order[purchase_type]']:checked").val() == 1){
		        get_karigar_pending_orders();
		    }
		    get_karigar_details(this.value);
		}else if(ctrl_page[1] == 'supplier_po_payment' && ctrl_page[2] == 'add')
		{
           get_supplier_pay_details(this.value);
		}
		else if(ctrl_page[1] == 'rate_fixing' && ctrl_page[2] == 'add')
		{
		    get_rate_fixing_po_no();
		}
	}
	   
});


function get_karigar_details(karId){
     $(".overlay").css('display','block');
    	my_Date = new Date();
		$.ajax({ 
    		url:base_url+ "index.php/admin_ret_purchase/get_karigar_details?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
            data: {'karId': karId},
            type:"POST",
            dataType:"JSON",
            success:function(data)
            {
                karigar_details = data; 
                console.log("karigar_details", karigar_details);
                $("#tcs_percent").val(data.tcs_tax);
                $("#tds_percent").val(data.tds_tax);
                $("#supplier_country").val(data.id_country);
                $("#supplier_state").val(data.id_state);
             	$(".overlay").css("display", "none");	
            },
            error:function(error)  
            {	
            } 
    	});
}

function get_ActiveEmployee()
{
    $('#emp_select option').remove();
		my_Date = new Date();
		$.ajax({ 
		url:base_url+ "index.php/admin_ret_estimation/get_employee?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data: {'id_branch':''},
        type:"POST",
        dataType:"JSON",
        success:function(data)
        {
           var id_employee=$('#emp_select').val();
           $.each(data, function (key, item) {					  				  			   		
                	 	$("#emp_select").append(						
                	 	$("<option></option>")						
                	 	.attr("value", item.id_employee)						  						  
                	 	.text(item.emp_name)						  					
                	 	);			   											
                 	});						
             	$("#emp_select").select2({			    
            	 	placeholder: "Select Employee",			    
            	 	allowClear: true		    
             	});					
         	    $("#emp_select").select2("val",(id_employee!='' && id_employee>0?id_employee:''));	 
         	    $(".overlay").css("display", "none");	
        },
        error:function(error)  
        {	
        } 
    	});
}

function set_qc_issue_preview_detaails(po_id)
{
    $(".overlay").css("display", "block");
   
    $.each(po_itemdetails,function(key,val){
       if(val.po_id==po_id)
       {
           var rowExists=false;
           var trHtml='';
			$.each(val.item_details,function(key,items){
			        $('#item_detail > tbody tr').each(function(bidx, brow){
                    curRow = $(this);
                        if(curRow.find('.po_item_id').val()==items.po_item_id)
    					{
    					    rowExists=true;
    					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
    					} 
                   });
               if(!rowExists)
               {
                  
    				trHtml+='<tr>'
                    +'<td><input type="hidden" class="po_item_id" name="po_item_id[]" value="'+items.po_item_id+'" />'+items.po_ref_no+'</td>'
                    +'<td>'+items.karigar_name+'</td>'
                    +'<td>'+items.product_name+'</td>'
                    +'<td>'+items.design_name+'</td>'
                    +'<td>'+items.sub_design_name+'</td>'
                    +'<td><input type="hidden" class="no_of_pcs" value="'+items.no_of_pcs+'">'+items.no_of_pcs+'</td>'
                    +'<td><input type="hidden" class="gross_wt" value="'+items.gross_wt+'">'+items.gross_wt+'</td>'
                    +'<td><input type="hidden" class="less_wt" value="'+items.less_wt+'">'+items.less_wt+'</td>'
                    +'<td><input type="hidden" class="net_wt" value="'+items.net_wt+'">'+items.net_wt+'</td>'
                    +'</tr>';
			    }
			});

            if($('#item_detail > tbody  > tr').length>0)
        	{
        	    $('#item_detail > tbody > tr:first').before(trHtml);
        	}else{
        	    $('#item_detail tbody').append(trHtml);
        	}
        	calculate_qu_issue_row_details();
        	$('#select_po_ref_no').val("");
          
       }
    });
    $(".overlay").css("display", "none");
}

function calculate_qu_issue_row_details()
{
    var total_pcs=0;
    var total_gwt=0;
    var total_lwt=0;
    var total_nwt=0;
    $('#item_detail > tbody tr').each(function(bidx, brow){
        curRow = $(this);
        total_pcs+=parseFloat(isNaN(curRow.find('.no_of_pcs').val()) || curRow.find('.no_of_pcs')=='' ? 0 :curRow.find('.no_of_pcs').val());
        total_gwt+=parseFloat(isNaN(curRow.find('.gross_wt').val()) || curRow.find('.gross_wt')=='' ? 0 :curRow.find('.gross_wt').val());
        total_lwt+=parseFloat(isNaN(curRow.find('.less_wt').val()) || curRow.find('.less_wt')=='' ? 0 :curRow.find('.less_wt').val());
        total_nwt+=parseFloat(isNaN(curRow.find('.net_wt').val()) || curRow.find('.net_wt')=='' ? 0 :curRow.find('.net_wt').val());
    });
    $('.total_pcs').html(parseFloat(total_pcs));
    $('.total_gwt').html(parseFloat(total_gwt).toFixed(3));
    $('.total_lwt').html(parseFloat(total_lwt).toFixed(3));
    $('.total_nwt').html(parseFloat(total_nwt).toFixed(3));
}

$('#qc_issue_submit').on('click',function(){
    $("div.overlay").css("display", "block"); 
    if($('#emp_select').val()=='' || $('#emp_select').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Employee."});
        $("div.overlay").css("display", "none"); 
    }
    else if($('#item_detail > tbody >tr').length==0)
    {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found."});
         $("div.overlay").css("display", "none"); 
    }
    else
    {
        $('#qc_issue_submit').prop('disabled',true);
        var req_data=[];
          var selected = [];
            var approve=false;
            $("#item_detail tbody tr").each(function(index, value)
            {
                   transData = { 
                    'po_item_id'   : $(value).find(".po_item_id").val(),
                    }
                    selected.push(transData);	
            });
            req_data = selected;
            update_qc_issue(req_data);
    }
});

function update_qc_issue(req_data)
{
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/update_qc_issue?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'req_data':req_data,'id_employee':$('#emp_select').val(),'total_pcs':$('.total_pcs').html(),'total_gwt':$('.total_gwt').html(),'total_lwt':$('.total_lwt').html(),'total_nwt':$('.total_nwt').html()},
        type:"POST",
        async:false,
        success:function(data){
                $('#update_qc_status').prop('disabled',false);
                window.location.href= base_url+'index.php/admin_ret_purchase/qc_issue_receipt/list';
                $("div.overlay").css("display", "none"); 
        },
        error:function(error)  
        {
            $("div.overlay").css("display", "none"); 
        }	 
    });
}


function get_qc_issue_details()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/qc_issue_receipt/ajax?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 type:"POST",
	 success:function(data){
	 	var list=data.list;
		var oTable = $('#item_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#item_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [[ 0, "desc" ]],
            "dom": 'lBfrtip',
            "buttons" : ['excel','print'],
            "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            "aaData": list,
            "aoColumns": [
                { "mDataProp": "po_ref_no" },
                { "mDataProp": "date_add" },
                { "mDataProp": "emp_name" },
                { "mDataProp": "product_name" },
                { "mDataProp": "design_name" },
                { "mDataProp": "sub_design_name" },
                { "mDataProp": "total_pcs" },
                { "mDataProp": "gross_wt" },
                { "mDataProp": "less_wt" },
                { "mDataProp": "net_wt" },
                { "mDataProp": function ( row, type, val, meta ){
                return '<span class="badge bg-'+(row.status==1 ? 'red' :'green')+'">'+row.qc_status+'</span>';
                },
                },
                { "mDataProp": "qc_passed_pcs" },
                { "mDataProp": "qc_passed_gwt" },
                { "mDataProp": "qc_passed_lwt" },
                { "mDataProp": "qc_passed_nwt" },
              ]
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}



//Halmarking Issue / Receipt
function get_pending_halmarking_items(status)
{
    $(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_purchase/halmarking_issue_receipt/get_pending_halmarking_items',
		dataType:'json',
		success:function(data){
            var po_id =  $('#select_po_ref_no').val();
            po_itemdetails=data;
            $.each(data, function (key, item) {
                $('#select_po_ref_no').append(
                $("<option></option>")
                .attr("value", item.po_id)
                .text(item.po_ref_no)
                );
            });
            
            $("#select_po_ref_no").select2({
                placeholder: "Select PO Ref No",
                allowClear: true
            });
            
            $("#select_po_ref_no").select2("val",(po_id!='' && po_id>0?po_id:''));
            $(".overlay").css("display", "none");	
		}
	});
}

function set_purchase_order_item_detaails(po_id)
{
    $(".overlay").css("display", "block");
   
    $.each(po_itemdetails,function(key,val){
       if(val.po_id==po_id)
       {
           var rowExists=false;
           var trHtml='';
			$.each(val.item_details,function(key,items){
			        $('#item_detail > tbody tr').each(function(bidx, brow){
                    curRow = $(this);
                        if(curRow.find('.po_item_id').val()==items.po_item_id)
    					{
    					    rowExists=true;
    					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
    					} 
                   });
               if(!rowExists)
               {
                  
    				trHtml+='<tr>'
                    +'<td><input type="checkbox" class="po_item_id" name="po_item_id[]" value="'+items.po_item_id+'" checked/><input type="hidden" class="po_id" name="po_id[]" value="'+val.po_id+'" />'+items.po_ref_no+'</td>'
                    +'<td>'+items.karigar_name+'</td>'
                    +'<td>'+items.product_name+'</td>'
                    +'<td>'+items.design_name+'</td>'
                    +'<td>'+items.sub_design_name+'</td>'
                    +'<td><input type="hidden" class="no_of_pcs" value="'+items.no_of_pcs+'">'+items.no_of_pcs+'</td>'
                    +'<td><input type="hidden" class="gross_wt" value="'+items.gross_wt+'">'+items.gross_wt+'</td>'
                    +'<td><input type="hidden" class="less_wt" value="'+items.less_wt+'">'+items.less_wt+'</td>'
                    +'<td><input type="hidden" class="net_wt" value="'+items.net_wt+'">'+items.net_wt+'</td>'
                    +'</tr>';
			    }
			});

            if($('#item_detail > tbody  > tr').length>0)
        	{
        	    $('#item_detail > tbody > tr:first').before(trHtml);
        	}else{
        	    $('#item_detail tbody').append(trHtml);
        	}
        	calculate_halmarking_issue_row_details();
        	$('#select_po_ref_no').val("");
          
       }
    });
    $(".overlay").css("display", "none");
}


function calculate_halmarking_issue_row_details()
{
    var total_pcs=0;
    var total_gwt=0;
    var total_lwt=0;
    var total_nwt=0;
    $('#item_detail > tbody tr').each(function(bidx, brow){
        curRow = $(this);
        total_pcs+=parseFloat(isNaN(curRow.find('.no_of_pcs').val()) || curRow.find('.no_of_pcs')=='' ? 0 :curRow.find('.no_of_pcs').val());
        total_gwt+=parseFloat(isNaN(curRow.find('.gross_wt').val()) || curRow.find('.gross_wt')=='' ? 0 :curRow.find('.gross_wt').val());
        total_lwt+=parseFloat(isNaN(curRow.find('.less_wt').val()) || curRow.find('.less_wt')=='' ? 0 :curRow.find('.less_wt').val());
        total_nwt+=parseFloat(isNaN(curRow.find('.net_wt').val()) || curRow.find('.net_wt')=='' ? 0 :curRow.find('.net_wt').val());
    });
    $('.total_pcs').html(parseFloat(total_pcs));
    $('.total_gwt').html(parseFloat(total_gwt).toFixed(3));
    $('.total_lwt').html(parseFloat(total_lwt).toFixed(3));
    $('.total_nwt').html(parseFloat(total_nwt).toFixed(3));
}


$('#halmarking_issue').on('click',function(){
    if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Karigar."});
    }
    else
    {
        $('#halmarking_issue').prop('disabled',true);
        var req_data=[];
         if($("input[name='po_item_id[]']:checked").val())
         {
            var selected = [];
            var approve=false;
            $("#item_detail tbody tr").each(function(index, value)
            {
                if($(value).find("input[name='po_item_id[]']:checked").is(":checked"))
                {
                    transData = { 
                    'po_item_id'   : $(value).find(".po_item_id").val(),
                    'po_id'        : $(value).find(".po_id").val(),
                    }
                    selected.push(transData);	
                }
            });
            req_data = selected;
            update_halmarking_issue(req_data);
         }else
         {
             $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Any One Items."});
         }
    }
});

function update_halmarking_issue(req_data)
{
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/update_halmarking_issue?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'req_data':req_data,'id_karigar':$('#select_karigar').val(),'total_pcs':$('.total_pcs').html(),'total_gwt':$('.total_gwt').html(),'total_lwt':$('.total_lwt').html(),'total_nwt':$('.total_nwt').html()},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
                if(data.status)
                {
                    $('#halmarking_issue').prop('disabled',false);
                    window.location.href= base_url+'index.php/admin_ret_purchase/halmarking_issue_receipt/list';
                    $("div.overlay").css("display", "none"); 
                }else
                {
                    $('#halmarking_issue').prop('disabled',false);
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                }
                
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}


function get_halmarking_issue_details()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/halmarking_issue_receipt/ajax?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 type:"POST",
	 success:function(data){
	 	var list=data.list;
		var oTable = $('#item_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#item_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [[ 0, "desc" ]],
            "dom": 'lBfrtip',
            "buttons" : ['excel','print'],
            "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            "aaData": list,
            "aoColumns": [
                { "mDataProp": "hm_ref_no" },
                { "mDataProp": "issue_date" },
                { "mDataProp": "karigar_name" },
                { "mDataProp": "hm_process_pcs" },
                { "mDataProp": "hm_process_gwt" },
                { "mDataProp": "hm_process_lwt" },
                { "mDataProp": "hm_process_nwt" },
                { "mDataProp": "total_hm_charges" },
                { "mDataProp": function ( row, type, val, meta ){
                return '<span class="badge bg-'+(row.status==1 ? 'red' :'green')+'">'+row.hm_status+'</span>';
                },
                },
              ]
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}


function get_halmarking_issue_orders()
{
     $(".overlay").css('display','block');
	$.ajax({
		type: 'GET',
		url: base_url+'index.php/admin_ret_purchase/halmarking_issue_receipt/get_halmarking_issue_orders',
		dataType:'json',
		success:function(data){
            var po_id =  $('#select_hm_ref_no').val();
            hm_itemdetails=data;
            $.each(data, function (key, item) {
                $('#select_hm_ref_no').append(
                $("<option></option>")
                .attr("value", item.hm_process_id)
                .text(item.hm_ref_no)
                );
            });
            
            $("#select_hm_ref_no").select2({
                placeholder: "Select HM Ref No",
                allowClear: true
            });
            
            $("#select_hm_ref_no").select2("val",(po_id!='' && po_id>0?po_id:''));
            $(".overlay").css("display", "none");	
		}
	});
}

$('#select_hm_ref_no').on('change',function(){
    if(this.value!='')
    {
         if(ctrl_page[2]=='hm_receipt')
        {
            set_halmarking_issue_details();
        }
    }
   
});

function set_halmarking_issue_details()
{
    var trHtml='';
    $.each(hm_itemdetails,function(key,val){
        if(val.hm_process_id==$('#select_hm_ref_no').val())
        {
            $.each(val.item_details,function(key,items){
                let stone_details=JSON.stringify(items.stone_details);
                    trHtml+='<tr class="'+items.po_item_id+'">'
                        +'<td><input type="hidden" class="po_item_id" value="'+items.po_item_id+'">'+items.po_item_id+'</td>'
                        +'<td>'+items.product_name+'</td>'
                        +'<td>'+items.design_name+'</td>'
                        +'<td>'+items.sub_design_name+'</td>'
                        +'<td><input type="hidden" class="hm_issue_pcs" value="'+items.pcs+'">'+items.pcs+'</td>'
                        +'<td><input type="hidden" class="hm_issue_gwt" value="'+items.gross_wt+'">'+items.gross_wt+'</td>'
                        +'<td><input type="hidden" class="hm_issue_lwt" value="'+items.less_wt+'">'+items.less_wt+'</td>'
                        +'<td><input type="hidden" class="hm_issue_nwt" value="'+items.net_wt+'">'+items.net_wt+'</td>'
                        +'<td><input type="number" name="order_items[hm_rejected_pcs][]" class="form-control hm_rejected_pcs" value="0" style="width: 80px;"></td>'
        	            +'<td><input type="number" name="order_items[hm_rejected_gwt][]" class="form-control hm_rejected_gwt" value="0" style="width: 80px;"></td>'
			            +'<td><input type="hidden" class="stone_details" name="order_items[stone_details][]" value='+stone_details+'>'+((items.stone_details).length>0 ? '<a href="#" onClick="create_new_empty_po_stone_items($(this).closest(\'tr\'));" class="btn btn-success  btn-sm"><i class="fa fa-plus"></i></a>' : '-')+'</td>'
        	            +'<td><input type="number" name="order_items[hm_rejected_nwt][]" class="form-control hm_rejected_nwt" value="0" readonly style="width: 80px;"></td>'
    			        +'<td><input type="number" name="order_items[hm_received_pcs][]" class="form-control hm_received_pcs" value="'+items.pcs+'"  style="width: 80px;" readonly></td>'
    			        +'<td><input type="number" name="order_items[hm_received_gwt][]" class="form-control hm_received_gwt" value="'+items.gross_wt+'"  style="width: 80px;" readonly></td>'
    			        +'<td><input type="number" name="order_items[hm_received_lwt][]" class="form-control hm_received_lwt" value="'+items.less_wt+'"  style="width: 80px;" readonly></td>'
    			        +'<td><input type="number" name="order_items[hm_received_nwt][]" class="form-control hm_received_nwt" value="'+items.net_wt+'"  style="width: 80px;" readonly></td>'
    			        +'<td><input type="number" name="order_items[hm_charges][]" class="form-control hm_charges" value="0" style="width: 80px;"></td>'
    			        +'<td><input type="number" name="order_items[hm_total_amount][]" class="form-control total_amount" value="0" style="width: 80px;" readonly></td>'
                    '</tr>';
            });
        }
    });
    if($('#item_detail > tbody  > tr').length>0)
	{
	    $('#item_detail > tbody > tr:first').before(trHtml);
	}else{
	    $('#item_detail tbody').append(trHtml);
	}
	calculate_halmarking_receipt_row_details();
}

$(document).on('keyup','.hm_rejected_pcs',function(e){
    var row = $(this).closest('tr'); 
    var tot_pcs=row.find('.hm_issue_pcs').val();
    var failed_pcs=row.find('.hm_rejected_pcs').val();
    if(parseFloat(tot_pcs)<parseFloat(failed_pcs))
    {
        row.find('.hm_rejected_pcs').val('');
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Pieces."});
    }
    calculate_halmarking_receipt_row_details();
});

$(document).on('keyup','.hm_rejected_gwt',function(e){
    var row = $(this).closest('tr'); 
    var gross_wt=row.find('.hm_issue_gwt').val();
    var failed_wt=row.find('.hm_rejected_gwt').val();
    if(parseFloat(gross_wt)<parseFloat(failed_wt))
    {
        row.find('.hm_rejected_gwt').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Weight."});
    }
    calculate_halmarking_receipt_row_details();
});

$(document).on('keyup','.hm_charges',function(e){
    calculate_halmarking_receipt_row_details();
});


function calculate_halmarking_receipt_row_details()
{
    var total_issue_pcs=0;
    var total_issue_gwt=0;
    var total_issue_lwt=0;
    var total_issue_nwt=0;
    
    var total_rejected_pcs=0;
    var total_rejected_gwt=0;
    var total_rejected_lwt=0;
    var total_rejected_nwt=0;
    
    $('#item_detail > tbody tr').each(function(bidx, brow){
        curRow = $(this);
        
        var stone_Details  = JSON.parse(curRow.find('.stone_details').val());
        var rejected_stn_wt =0;
         $.each(stone_Details,function(k,stn){
            rejected_stn_wt+=parseFloat(stn.po_stone_rejected_wt);
         });
         
        var hm_charges=parseFloat(isNaN(curRow.find('.hm_charges').val()) || curRow.find('.hm_charges')=='' ? 0 :curRow.find('.hm_charges').val());
        
        
        
        total_issue_pcs=parseFloat(isNaN(curRow.find('.hm_issue_pcs').val()) || curRow.find('.hm_issue_pcs')=='' ? 0 :curRow.find('.hm_issue_pcs').val());
        total_issue_gwt=parseFloat(isNaN(curRow.find('.hm_issue_gwt').val()) || curRow.find('.hm_issue_gwt')=='' ? 0 :curRow.find('.hm_issue_gwt').val());
        total_issue_lwt=parseFloat(isNaN(curRow.find('.hm_issue_lwt').val()) || curRow.find('.hm_issue_lwt')=='' ? 0 :curRow.find('.hm_issue_lwt').val());
        total_issue_nwt=parseFloat(isNaN(curRow.find('.hm_issue_nwt').val()) || curRow.find('.hm_issue_nwt')=='' ? 0 :curRow.find('.hm_issue_nwt').val());
        
        total_rejected_pcs=parseFloat(isNaN(curRow.find('.hm_rejected_pcs').val()) || curRow.find('.hm_rejected_pcs')=='' ? 0 :curRow.find('.hm_rejected_pcs').val());
        total_rejected_gwt=parseFloat(isNaN(curRow.find('.hm_rejected_gwt').val()) || curRow.find('.hm_rejected_gwt')=='' ? 0 :curRow.find('.hm_rejected_gwt').val());
        total_rejected_lwt=parseFloat(isNaN(curRow.find('.hm_rejected_lwt').val()) || curRow.find('.hm_rejected_lwt')=='' ? 0 :curRow.find('.hm_rejected_lwt').val());
        total_rejected_nwt=parseFloat(isNaN(curRow.find('.hm_rejected_nwt').val()) || curRow.find('.hm_rejected_nwt')=='' ? 0 :curRow.find('.hm_rejected_nwt').val());
        
        var hm_accepted_pcs=parseFloat(total_issue_pcs)-parseFloat(total_rejected_pcs);
        
        curRow.find('.hm_received_pcs').val(hm_accepted_pcs);
        curRow.find('.hm_received_gwt').val(parseFloat(total_issue_gwt)-parseFloat(total_rejected_gwt));
        curRow.find('.hm_received_lwt').val(parseFloat(total_issue_lwt)-parseFloat(rejected_stn_wt));
        curRow.find('.hm_received_nwt').val(parseFloat(curRow.find('.hm_received_gwt').val())-parseFloat(curRow.find('.hm_received_lwt').val()));
        
        
        curRow.find('.total_amount').val(parseFloat(parseFloat(hm_accepted_pcs)*parseFloat(hm_charges)).toFixed(2));
        
        console.log('hm_charges:'+hm_charges);
        
    });
   /* $('.hm_received_pcs').val(parseFloat(total_issue_pcs)-parseFloat(total_rejected_pcs));
    $('.hm_received_gwt').val(parseFloat(parseFloat(total_issue_gwt)-parseFloat(total_rejected_gwt)).toFixed(3));
    $('.hm_received_lwt').val(parseFloat(parseFloat(total_issue_lwt)-parseFloat(total_rejected_lwt)).toFixed(3));
    $('.hm_received_nwt').val(parseFloat(parseFloat(total_rejected_nwt)-parseFloat(total_rejected_nwt)).toFixed(3));*/
}

$('#hm_receipt_submit').on('click',function(){
    if($('#select_hm_ref_no').val=='')
    {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Ref No."});
    }
    else
    {
        $('#hm_receipt_submit').prop('disabled',true);
        var req_data=[];
        var selected = [];
        var approve=false;
        $("#item_detail tbody tr").each(function(index, value)
        {
           
                transData = { 
                'po_item_id'        : $(value).find(".po_item_id").val(),
                'hm_rejected_pcs'   : $(value).find(".hm_rejected_pcs").val(),
                'hm_rejected_gwt'   : $(value).find(".hm_rejected_gwt").val(),
                'hm_rejected_lwt'   : $(value).find(".hm_rejected_lwt").val(),
                'hm_rejected_nwt'   : $(value).find(".hm_rejected_nwt").val(),
                'halmarking_charges': $(value).find(".total_amount").val(),
                }
                selected.push(transData);	
        });
        req_data = selected;
        update_halmarking_receipt(req_data);
    }
});

function update_halmarking_receipt(req_data)
{
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/update_halmarking_receipt?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'req_data':req_data,'hm_process_id':$('#select_hm_ref_no').val(), 'hm_vendor_ref_id' : $('#hm_vendor_ref_id').val()},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
                if(data.status)
                {
                    $('#hm_receipt_submit').prop('disabled',false);
                    window.location.href= base_url+'index.php/admin_ret_purchase/halmarking_issue_receipt/list';
                    $("div.overlay").css("display", "none"); 
                }else
                {
                    $('#hm_receipt_submit').prop('disabled',false);
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                }
                
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}


//Halmarking Issue / Receipt




//PO Payment
$('#search_po_items').on('click',function(){
    if($('#po_ref_no').val()=='')
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter THE PO Ref No"});
    }else
    {
        get_purchase_order_items();
    }
});

function get_purchase_order_items()
{
    $('#item_details tbody').empty();
    $('#pay_history tbody').empty();
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/purchase_payment/purchase_payment_details?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data: {'po_ref_no':$('#po_ref_no').val()},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
               var item_details=data.item_details;
               if(item_details.length==0)
               {
                   $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found."});
                   $("div.overlay").css("display", "none"); 
               }
               else
               {
                    var total_payable_amount = item_details[0].total_payable_amt;
                    var pay_history_details = data.pay_history;
                    var tot_cash_amt=data.pay_details['tot_pay_amt'];
                    var tot_pay_wt=data.pay_details['tot_pay_wt'];
                    $('#karigar_name').html(item_details[0].karigar_name);
                    $('#mobile_no').html(item_details[0].mobile);
            
                   var trHtml='';
                   var historyHtml = '';
                   
                   $.each(item_details,function(key,items){
                       if(items.mc_type==1)
                       {
                           var mc_value=parseFloat(parseFloat(items.mc_value)*parseFloat(items.total_gwt));
                       }else
                       {
                           var mc_value=parseFloat(parseFloat(items.mc_value)*parseFloat(items.total_pcs));
                       }
                       trHtml+='<tr>'
                                 +'<td>'+items.category_name+'</td>'
                                 +'<td>'+items.product_name+'</td>'
                                 +'<td>'+items.design_name+'</td>'
                                 +'<td>'+items.sub_design_name+'</td>'
                                 +'<td>'+items.total_pcs+'</td>'
                                 +'<td>'+items.total_gwt+'</td>'
                                 +'<td>'+items.total_lwt+'</td>'
                                 +'<td>'+items.total_nwt+'</td>'
                                 +'<td>'+items.item_wastage+'</td>'
                                 +'<td>'+parseFloat(mc_value).toFixed(2)+'</td>'
                                 +'<td>'+items.item_pure_wt+'</td>'
                               +'</tr>';
                   
                        if(items.rate_fixing_det.length>0)
                        {
                            var total_amount=0;
                            var total_taxable_amount=0;
                            var total_tax_amount=0;
                            trHtml+='<tr style="font-weight:bold;">'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td>#</td>'
                                 +'<td>Rate Fixed Date</td>'
                                 +'<td>Description</td>'
                                 +'<td>Amount/Weight</td>'
                                 +'<td>Rate Per Gram</td>'
                                 +'<td>Taxable Amount</td>'
                                 +'<td>Tax Amount</td>'
                                 +'<td>Total Amount</td>'
                               +'</tr>';
                              
                              $.each(items.rate_fixing_det,function(k,val){
                                total_amount+=parseFloat(val.total_amount);
                                total_tax_amount+=parseFloat(val.total_tax_amount);
                                total_taxable_amount+=parseFloat(val.total_amount-val.total_tax_amount);
                                  trHtml+='<tr>'
                                         +'<td></td>'
                                         +'<td></td>'
                                         +'<td></td>'
                                         +'<td>'+parseFloat(k+1)+'</td>'
                                         +'<td>'+val.rate_fix_created_on+'</td>'
                                         +'<td>'+(val.rate_fix_type==2 ? 'MC & Stone & Other ' :'Metal')+'</td>'
                                         +'<td>'+(val.rate_fix_type==1 ? val.rate_fix_wt : val.rate_fix_amt)+'</td>'
                                         +'<td>'+val.rate_fix_rate+'</td>'
                                         +'<td>'+parseFloat(val.total_amount-val.total_tax_amount).toFixed(2)+'</td>'
                                         +'<td>'+parseFloat(val.total_tax_amount).toFixed(2)+'</td>'
                                         +'<td>'+parseFloat(val.total_amount).toFixed(2)+'</td>'
                                       +'</tr>';
                              });
                              
                              trHtml+='<tr style="font-weight:bold;">'
                                 +'<td>TOTAL</td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td>'+parseFloat(total_taxable_amount).toFixed(2)+'</td>'
                                 +'<td>'+parseFloat(total_tax_amount).toFixed(2)+'</td>'
                                 +'<td>'+parseFloat(total_amount).toFixed(2)+'</td>'
                               +'</tr>';
                               
                        }
                        
                   });
                   
                   $.each(pay_history_details, function(key, items){
                       historyHtml+='<tr>'
                                 +'<td>'+items.pay_refno+'</td>'
                                 +'<td>'+items.pay_date+'</td>'
                                 +'<td>'+items.tot_cash_pay+'</td>'
                               +'</tr>';
                   });

                    $('.payable_amt').html(parseFloat(total_payable_amount).toFixed(2));
                    $('.paid_amt').html(parseFloat(tot_cash_amt).toFixed(2));
                    $('.paid_wt').html(parseFloat(tot_pay_wt).toFixed(2));
                    $('.balance_amt').html(parseFloat(total_payable_amount-tot_cash_amt).toFixed(2));

                    $('#item_details tbody').append(trHtml);
                    
                    $('#pay_history tbody').append(historyHtml);
                    
                    
                    $("div.overlay").css("display", "none"); 
               }
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}


/*function get_purchase_order_items()
{
    $('#item_details tbody').empty();
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/purchase_payment/purchase_payment_details?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data: {'po_ref_no':$('#po_ref_no').val()},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
            var item_details=data.item_details;
            var pay_history_details=data.pay_history;
            var tot_cash_amt=data.pay_details['tot_pay_amt'];
            var tot_pay_wt=data.pay_details['tot_pay_wt'];
               if(item_details.length==0)
               {
                   $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found."});
               }
               else
               {
                   var trHtml='';
                   var total_mc_value=0;
                   var total_pur_wt=0;
                   $.each(item_details,function(key,items){
                       total_pur_wt+=parseFloat(items.item_pure_wt);
                       if(items.mc_type==1)
                       {
                           var mc_value=parseFloat(parseFloat(items.mc_value)*parseFloat(items.total_gwt));
                       }else
                       {
                           var mc_value=parseFloat(parseFloat(items.mc_value)*parseFloat(items.total_pcs));
                       }
                       trHtml+='<tr style="font-weight:bold;">'
                                 +'<td>'+items.category_name+'</td>'
                                 +'<td>'+items.product_name+'</td>'
                                 +'<td>'+items.design_name+'</td>'
                                 +'<td>'+items.sub_design_name+'</td>'
                                 +'<td>'+items.total_pcs+'</td>'
                                 +'<td>'+items.total_gwt+'</td>'
                                 +'<td>'+items.total_lwt+'</td>'
                                 +'<td>'+items.total_nwt+'</td>'
                                 +'<td>'+items.item_wastage+'</td>'
                                 +'<td>'+parseFloat(mc_value).toFixed(2)+'</td>'
                                 +'<td>'+items.item_pure_wt+'</td>'
                               +'</tr>';
                    total_mc_value+=parseFloat(mc_value);
                        
                        if(items.rate_fixing_det.length>0)
                        {
                            trHtml+='<tr style="font-weight:bold;">'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td>#</td>'
                                 +'<td>Description</td>'
                                 +'<td>Amount/Weight</td>'
                                 +'<td>Rate Per Gram</td>'
                                 +'<td>Taxable Amount</td>'
                                 +'<td>Tax Amount</td>'
                                 +'<td>Total Amount</td>'
                                 +'<td>Paid Amount</td>'
                                 +'<td>Balance Amount</td>'
                               +'</tr>';
                              
                              $.each(items.rate_fixing_det,function(k,val){
    
                                  trHtml+='<tr>'
                                         +'<td></td>'
                                         +'<td></td>'
                                         +'<td>'+val.rate_fix_id+'</td>'
                                         +'<td>'+(val.rate_fix_type==2 ? 'MC & Stone & Other ' :'Metal')+'</td>'
                                         +'<td>'+(val.rate_fix_type==1 ? val.rate_fix_wt : val.rate_fix_amt)+'</td>'
                                         +'<td>'+val.rate_fix_rate+'</td>'
                                         +'<td>'+parseFloat(val.total_amount-val.total_tax_amount).toFixed(2)+'</td>'
                                         +'<td>'+parseFloat(val.total_tax_amount).toFixed(2)+'</td>'
                                         +'<td>'+parseFloat(val.total_amount).toFixed(2)+'</td>'
                                         +'<td></td>'
                                        +'<td></td>'
                                       +'</tr>';
                              });
                        }
                        
                   });
                   trHtml+='<tr style="font-weight:bold;">'
                                 +'<td>TOTAL</td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td></td>'
                                 +'<td>'+parseFloat(total_mc_value).toFixed(2)+'</td>'
                                 +'<td>'+parseFloat(total_pur_wt).toFixed(3)+'</td>'
                               +'</tr>';

                    $('.payable_amt').html(parseFloat(total_mc_value).toFixed(2));
                    $('.payable_weight').html(parseFloat(total_pur_wt).toFixed(2));
                    $('.paid_amt').html(parseFloat(tot_cash_amt).toFixed(2));
                    $('.paid_wt').html(parseFloat(tot_pay_wt).toFixed(2));
                    $('.balance_amt').html(parseFloat(total_mc_value-tot_cash_amt).toFixed(2));
                    $('.balance_wt').html(parseFloat(total_pur_wt-tot_pay_wt).toFixed(2));
                    
                    $('#item_details tbody').append(trHtml);
                    
                    
                    $("div.overlay").css("display", "none"); 
               }
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}
*/

$(document).on('change','.wt_pay_type',function(e){
    var row = $(this).closest('tr'); 
    var received_weight=row.find('.received_weight').val();
    if($('.wt_pay_type:checked').val()==1)
    {
        row.find('.rate_per_gram').prop('readonly',true);
        row.find('.rate_per_gram').val(0);
    }else{
        row.find('.rate_per_gram').prop('readonly',false);
    }
    calculate_weight_payment(row);
});

function calculate_weight_payment(curRow)
{
    var received_weight=curRow.find('.received_weight').val();
    var rate_per_gram=curRow.find('.rate_per_gram').val();
    if($('.wt_pay_type:checked').val()==2)
    {
        curRow.find('.total_payment_wt').val(parseFloat(received_weight)*parseFloat(rate_per_gram));
    }else
    {
         curRow.find('.total_payment_wt').val(parseFloat(received_weight).toFixed(3));
    }
}

$(document).on('keyup','.received_weight,.rate_per_gram',function(e){
    var row = $(this).closest('tr');
    var balance_wt=row.find('.balance_wt').html();
    var received_weight=row.find('.received_weight').val();
    if(parseFloat(balance_wt)<parseFloat(received_weight))
    {
        row.find('.received_weight').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Weight"});
    }
    else
    {
        calculate_weight_payment(row);
    }
});

$(document).on('change','.received_amount',function(e){
    updatePayableAmt();
    calculate_received_amount();
});

function updatePayableAmt(){
    var totalreceivedAmt = $(".received_amount").val();
    var totalassignedAmt = $(".received_amount").val();
    var assignrow = 1;
    var isAdvance = true;
    
     $('#po_pay_details tbody').find('tr').each(function () {
        var row = $(this);
        if (row.find('input[type="checkbox"]').is(':checked')) {
            isAdvance = false;
            /*pototalcost     += parseFloat(row.find('.item_cost').html());
            popaidcost      += parseFloat(row.find('.paidamt').html());
            popayablecost   += parseFloat(row.find('.balanceamt').html());
            row.find('.curpayable').val(parseFloat(row.find('.balanceamt').html()));*/
            if(totalassignedAmt >=  parseFloat(row.find('.balanceamt').html())){
                 row.find('.curpayable').val(parseFloat(row.find('.balanceamt').html()).toFixed(2));
                 totalassignedAmt -= parseFloat(row.find('.curpayable').val());
            }else if(assignrow == 0){
                 row.find('.curpayable').val(0);
                 row.find('input[type="checkbox"]').prop('checked', false);
            }else{
                parseFloat(row.find('.curpayable').val(parseFloat(totalassignedAmt).toFixed(2)));
                assignrow = 0;
                totalassignedAmt -= parseFloat(row.find('.curpayable').val());
            }
        }
    });
    
    $('.selectedpayable').html(totalreceivedAmt);
}


function calculate_received_amount(){
    var row = $(this).closest('tr');
    var balance_amt=row.find('.balance_amt').html();
    var received_amount=row.find('.received_amount').val();
    if(parseFloat(balance_amt)<parseFloat(received_amount))
    {
        row.find('.received_amount').val(0);
        row.find('.total_payment_amount').val(0);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Amount"});
    }
    else
    {
        row.find('.total_payment_amount').val(parseFloat(received_amount).toFixed(3));
    }
    calculatePaymentCost();
    updatePayableAmt();
}

$('#po_pay_submit').on('click',function(){
    var allow_submit=true;
    var tot_pay_amount=0;
    var tot_pay_weight=0;
    var bill_type = 2;
    $('#po_pay_details tbody').find('tr').each(function () {
        var row = $(this);
        if (row.find('input[type="checkbox"]').is(':checked')) {
           bill_type = 1;
        }
    });
    
    $('#bill_type').val(bill_type);
    
    $('#total_summary_details > tbody tr').each(function(bidx, brow){
        curRow = $(this);
        tot_pay_amount+=(isNaN($('.received_amount').val()) || $('.received_amount').val()=='' ? 0:$('.received_amount').val());
    });
    
    if(tot_pay_amount==0)
    {
        allow_submit=false;
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Payment Amount"});
    }
    else
    {
        allow_submit = true;
    }
    
    if($('#net_banking_pay_details').val() == '' && $('#sales_details').val() == '' && $('#chit_details').val() == ''){
        allow_submit=false;
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please fill payment method details"});
    }
    
    if(allow_submit)
    {
        my_Date = new Date();
        $("div.overlay").css("display", "block"); 
    	var form_data = $('#bill_pay').serialize();
    	$('#pay_submit').prop('disabled',true);
    	var url=base_url+ "index.php/admin_ret_purchase/supplier_po_payment/save?nocache=" + my_Date.getUTCSeconds();
        $.ajax({ 
            url:url,
            data: form_data,
            type:"POST",
            dataType:"JSON",
            success:function(data){
    			if(data.status)
    			{
    			    $("div.overlay").css("display", "none"); 
    			    window.location.reload();
    				//window.open( base_url+'index.php/admin_ret_billing/billing_invoice/'+data['id'],'_blank');
    			}
    			else
    			{
    			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
    			    $("div.overlay").css("display", "none"); 
    			}
    			
            },
            error:function(error)  
            {	
            $("div.overlay").css("display", "none"); 
            } 
        });
    }
    
});


$('#pay_submit').on('click',function(){
    
    var allow_submit=true;
    var tot_pay_amount=0;
    var tot_pay_weight=0;
    var bill_type = $("input[name='billing[bill_type]']:checked").val();
    
   
    if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Kaigar"});
        allow_submit=false;
    }
    else if($('.receive_amount').val()=='')
    {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter Received Amount"});
            allow_submit=false;
    }
    else
    {
        allow_submit=true;
    }
    if(allow_submit)
    {
        my_Date = new Date();
        $("div.overlay").css("display", "block"); 
    	var form_data=$('#bill_pay').serialize();
    	$('#pay_submit').prop('disabled',true);
    	var url=base_url+ "index.php/admin_ret_purchase/supplier_po_payment/save?nocache=" + my_Date.getUTCSeconds();
        $.ajax({ 
            url:url,
            data: form_data,
            type:"POST",
            dataType:"JSON",
            success:function(data){
    			if(data.status)
    			{
    			    $("div.overlay").css("display", "none"); 
    			    window.location.reload();
    				//window.open( base_url+'index.php/admin_ret_billing/billing_invoice/'+data['id'],'_blank');
    			}
    			else
    			{
    			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
    			    $("div.overlay").css("display", "none"); 
    			}
    			
            },
            error:function(error)  
            {	
            $("div.overlay").css("display", "none"); 
            } 
        });
    }
});

function get_bank_details(){
	$.ajax({		
	 	type: 'GET',		
	 	url : base_url + 'index.php/admin_ret_billing/get_bank_acc_details',
	 	dataType : 'json',		
	 	success  : function(data){
		 	bank_details = data;
	 	}	
	}); 
}

function get_purchase_order_payment_list(from_date, to_date)
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/supplier_po_payment/ajax?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 data:{'from_date': from_date, 'to_date': to_date},
	 type:"POST",
	 success:function(data){
	 	var list=data.list;
	 	var access=data.access;
		var oTable = $('#payment_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#payment_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [[ 0, "desc" ]],
            "dom": 'lBfrtip',
            "buttons" : ['excel','print'],
            "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            "aaData": list,
            "aoColumns": [
                { "mDataProp": "pay_id" },
                { "mDataProp": "pay_date" },
                { "mDataProp": "pay_refno" },
                { "mDataProp": "karigar_name" },
                { "mDataProp": "pay_amt" },
                { "mDataProp": "bill_type" },
                { "mDataProp": "status" },
                { "mDataProp": function ( row, type, val, meta ) {
                    id=row.pay_id;
                    edit_target=(access.edit=='0'?"":"#confirm-edit");
                    // PUR-CR04: Conditional cheque print + dual-print on Payment Copy click
                    print_url = (base_url + 'index.php/admin_ret_purchase/supplier_po_payment/paymentacknolodgement/' + id);
                    cheque_print_url = (base_url + 'index.php/admin_ret_purchase/supplier_po_payment/print_cheque/' + id);
                    cancel_btn = (row.pay_status==1 && access.edit == 1 ? '<button class="btn btn-warning" onclick="confirm_bill_cancel('+id+')"><i class="fa fa-close" ></i></button>' : '');
                    cheque_btn = (parseInt(row.has_cheque) > 0 ? '<a href="' + cheque_print_url + '" target="_blank" class="btn btn-success" data-toggle="tooltip" title="Cheque Print"><i class="fa fa-money" ></i></a>' : '');
                    print_btn = '<a href="javascript:void(0);" onclick="printPaymentCopy(\'' + print_url + '\', \'' + cheque_print_url + '\', ' + parseInt(row.has_cheque) + ')" class="btn btn-info" data-toggle="tooltip" title="Payment Copy"><i class="fa fa-print" ></i></a>';
                    action_content = (row.pay_status==1 ? print_btn + cheque_btn + cancel_btn : '-');
                    return action_content;
                }
                }
                
              ]
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}

function confirm_bill_cancel(pay_id)
{
	$('#pay_id').val(pay_id);
	$('#confirm-billcancell').modal('show');
}

// PUR-CR04: Opens payment copy, and if has_cheque > 0, also opens cheque print
function printPaymentCopy(paymentUrl, chequeUrl, hasCheque) {
    window.open(paymentUrl, '_blank');
    if (parseInt(hasCheque) > 0) {
        window.open(chequeUrl, '_blank');
    }
}

$('#payment_cancel_remark').on('keypress',function(){
	if(this.value.length>6)
	{
		$('#payment_cancel').prop('disabled',false);
	}else{
		$('#payment_cancel').prop('disabled',true);
	}
});

$('#payment_cancel').on('click',function(){
    $('#payment_cancel').prop('disabled',true);
	my_Date = new Date();
	$.ajax({
		type: 'POST',
		url:base_url+ "index.php/admin_ret_purchase/supplier_po_payment/cancel_pay_entry?nocache=" + my_Date.getUTCSeconds(),
		dataType:'json',
		data:{'cancel_reason':$('#payment_cancel_remark').val(),'pay_id':$('#pay_id').val()},
		success:function(data){
		    window.location.reload();
		}
	});
});


function get_purchase_orderno(searchTxt){

	my_Date = new Date();

	$.ajax({

        url: base_url+'index.php/admin_ret_purchase/getOrderNosBySearch/?nocache=' + my_Date.getUTCSeconds(),             

        dataType: "json", 

        method: "POST", 

        data: {'searchTxt': searchTxt, 'supplierId' : $('#select_karigar').val()}, 

        success: function (data) { 

			$( "#purchase_order_no" ).autocomplete(

			{

				source: data,

				select: function(e, i)

				{ 

					e.preventDefault();

					$("#purchase_order_no").val(i.item.label); 

					$("#purchaseorderno").val(i.item.value); 

					getPurchaseOrderProductDetails(i.item.value);

				},

				change: function (event, ui) {

					if (ui.item === null) {

						$(this).val('');

						$('#purchase_order_no').val('');

						$("#design_id").val(""); 

					}

			    },

				response: function(e, i) {

		            // ui.content is the array that's about to be sent to the response callback.

		            if(searchTxt != ""){

						if (i.content.length === 0) {
						    
						}else{

						   

						} 

					}else{

					
					}

		        },

				 minLength: 2,

			});

        }

     });

}

function getPurchaseOrderProductDetails(orderId)

{

	my_Date = new Date();

	$.ajax({

        url: base_url + 'index.php/admin_ret_tagging/admin_ret_purchase/?nocache=' + my_Date.getUTCSeconds(),             

        dataType: "json", 

        method: "POST", 

        data: { 'designId': designId }, 

        success: function ( data ) { 

			tag_design_stones = data;

			/* console.log(data);

			var html = '';

			$.each(data, function(key, item){

				html += '<tr><td><input type="hidden" name="tagstone[stone_id][]" value="'+item.stone_id+'" />'+item.stone_name+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagstone[pcs][]" value="'+item.stone_pcs+'" required /></div></td><td><div class="input-group"><input type="number" class="form-control" step="any" name="tagstone[weight][]" value="" required /></div></td><td><input type="hidden" name="tagstone[uom_id][]" value="'+item.uom_id+'" />'+item.uom_short_code+'</td><td><div class="input-group"> <input class="form-control" type="number" step="any" name="tagstone[amount][]" value="" required /></div></td></tr>';

			});

			$('#tagging_stone_details tbody').append(html); */

        }

     });

}


//PO Payment


//rate fixing

function get_rate_fixing_po_no()
{
    $("#select_po_ref_no option").remove();
	$.ajax({
	type: 'POST',
	data:{'id_karigar':$('#select_karigar').val()},
	url: base_url+'index.php/admin_ret_purchase/get_rate_fixing_po_no',
	dataType:'json',
	success:function(data){
	    ratefix_po_detail = data;
	    var id=$('#select_po_ref_no').val();
		$.each(data, function (key, item) {   
		    $("#select_po_ref_no").append(
		    $("<option></option>")
		    .attr("value", item.po_id)    
		    .text(item.po_ref_no)  
		    );
		}); 
		$("#select_po_ref_no").select2(
		{
			placeholder:"Select PO Ref",
			closeOnSelect: true		    
		});
		
		if($("#select_po_ref_no").length)
		{
		    $("#select_po_ref_no").select2("val",(id!='' && id>0?id:''));
		}
		    $(".overlay").css("display", "none");
		}
	});
}

$('#rate_fix_item_search').on('click',function(){
    if($('#po_ref_no').val()=='')
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Purchase Order Ref No."});
    }else
    {
        get_rate_fixing_item_search();
    }
});

function set_po_rate_fix_details()
{
    $('#item_details tbody').empty();
    $("div.overlay").css("display", "block"); 
    var trHtml='';
    $.each(ratefix_po_detail,function(key,items){
        if(items.po_id == $('#select_po_ref_no').val())
        {
            var balance_wt = parseFloat(items.tot_purchase_wt)-parseFloat(items.total_fixed_wt);
            trHtml+='<tr>'
            +'<td><input type="hidden" class="form-control po_item_id" name="rate_fixing_items[po_item_id][]" value="'+items.po_id+'">'+items.po_ref_no+'</td>'
            +'<td>'+items.podate+'</td>'
            +'<td>'+items.tot_purchase_wt+'</td>'
            +'<td>'+items.total_fixed_wt+'</td>'
            +'<td><input type="hidden" class="form-control balance_wt" value="'+balance_wt+'">'+parseFloat(balance_wt).toFixed(3)+'</td>'
            +'<td><input type="number" class="form-control fix_weight" name="rate_fixing_items[fix_weight][]"></td>'
            +'<td><input type="number" class="form-control rate_fix_rate" name="rate_fixing_items[rate_per_gram][]"></td>'
            +'<td><input type="number" class="form-control payable_amt" name="rate_fixing_items[payable_amt][]" readonly></td>'
            +'</tr>';
        }
    });
    $('#item_details tbody').append(trHtml);
    $("div.overlay").css("display", "none"); 
}

function get_rate_fixing_item_search()
{
    $('#item_details tbody').empty();
    my_Date = new Date();
    $("div.overlay").css("display", "block"); 
    $.ajax({
        url:base_url+ "index.php/admin_ret_purchase/rate_fixing/rate_fixing_items?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data:  {'po_ref_no':$('#po_ref_no').val()},
        type:"POST",
        async:false,
        dataType:'json',
        success:function(data){
                if(data.length)
                {
                    var trHtml='';
                    $.each(data,function(key,items){
                        trHtml+='<tr>'
                                +'<td><input type="hidden" class="form-control po_item_id" name="rate_fixing_items[po_item_id][]" value="'+items.po_item_id+'">'+items.category_name+'</td>'
                                +'<td>'+items.product_name+'</td>'
                                +'<td>'+items.design_name+'</td>'
                                +'<td>'+items.sub_design_name+'</td>'
                                +'<td>'+items.purity+'</td>'
                                +'<td>'+items.item_wastage+'</td>'
                                +'<td>'+items.item_pure_wt+'</td>'
                                +'<td><input type="hidden" class="form-control balance_wt" value="'+items.balance_wt+'">'+items.balance_wt+'</td>'
                                +'<td><input type="number" class="form-control fix_weight" name="rate_fixing_items[fix_weight][]"></td>'
                                +'<td><input type="number" class="form-control rate_fix_rate" name="rate_fixing_items[rate_per_gram][]"></td>'
                                +'<td><input type="number" class="form-control payable_amt" name="rate_fixing_items[payable_amt][]" readonly></td>'
                                +'</tr>';
                    });
                     $('#item_details tbody').append(trHtml);
                    $("div.overlay").css("display", "none"); 
                }else
                {
                    $("div.overlay").css("display", "none"); 
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
                    $('#po_ref_no').val('');
                    $('#po_ref_no').focus();
                }
                
        },
        error:function(error)  
        {
        $("div.overlay").css("display", "none"); 
        }	 
    });
}

$(document).on('keyup','.fix_weight,.rate_fix_rate',function(e){
    var row = $(this).closest('tr');
    var balance_wt=row.find('.balance_wt').val();
    var fix_weight=row.find('.fix_weight').val();
    if(parseFloat(balance_wt)<parseFloat(fix_weight))
    {
        row.find('.fix_weight').val('');
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Weight"});
    }
    else
    {
        calculate_rate_fixing_items_payment(row);
    }
});

function calculate_rate_fixing_items_payment(curRow)
{
    var fix_weight=(isNaN(curRow.find('.fix_weight').val()) || curRow.find('.fix_weight').val()=='' ? 0 :curRow.find('.fix_weight').val() );
    var rate_per_gram=(isNaN(curRow.find('.rate_fix_rate').val()) || curRow.find('.rate_fix_rate').val()=='' ? 0 :curRow.find('.rate_fix_rate').val() );
    curRow.find('.payable_amt').val(parseFloat(parseFloat(fix_weight)*parseFloat(rate_per_gram)).toFixed(2));
}


$('#rate_fix_submit').on('click',function(){
    var allow_submit=true;
    if($('#item_details > tbody tr').length == 0)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
        $("div.overlay").css("display", "none");
    }
    else
    {
        $('#item_details > tbody tr').each(function(bidx, brow){
             curRow = $(this);
             if(curRow.find('.fix_weight').val()=='' || curRow.find('.fix_weight').val()==0 || curRow.find('.rate_fix_rate').val()=='' || curRow.find('.rate_fix_rate').val()==0)
             {
                 allow_submit=false;
             }
        });
         if(allow_submit)
         {
               $("div.overlay").css("display", "block"); 
            	var form_data=$('#rate_fixing_form').serialize();
            	$('#rate_fix_submit').prop('disabled',true);
            	my_Date = new Date();
            	var url=base_url+ "index.php/admin_ret_purchase/rate_fixing/save?nocache=" + my_Date.getUTCSeconds();
                $.ajax({ 
                    url:url,
                    data: form_data,
                    type:"POST",
                    dataType:"JSON",
                    success:function(data){
            			if(data.status)
            			{
            			    $("div.overlay").css("display", "none"); 
            			    window.location.reload();
            			}
            			else
            			{
            			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
            			    $("div.overlay").css("display", "none"); 
            			}
            			
                    },
                    error:function(error)  
                    {	
                    $("div.overlay").css("display", "none"); 
                    } 
                });
        }
        else
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please File The Required Fields.."});
            $("div.overlay").css("display", "none");
        }
    }
     
});


//Net Banking

$('#net_bank_modal').on('click',function(){
    $('#netbanking_modal').modal('show');
    if($('#net_banking_details > tbody > tr').length==0)
    {
         create_new_empty_net_banking_row();
    }
});

$('#create_net_banking_row').on('click', function(){
	if(validateNetBankingDetailRow())
	{
		create_new_empty_net_banking_row();
	}
	else
	{
	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please File The Required Fields.."});
	}
});

function validateNetBankingDetailRow(){
	var row_validate = true;
	$('#netbanking_modal .modal-body #net_banking_details > tbody  > tr').each(function(index, tr) {
		if($(this).find('.pay_amount').val() == "" || $(this).find('.ref_no').val() == "" || $(this).find('.id_bank').val() == "" || $(this).find('.nb_date').val() == ""){
			row_validate = false;
		}
	});
	return row_validate;
}


function create_new_empty_net_banking_row()
{
	var row = "";
	var bank_list = '';
	$.each(bank_details, function (pkey, item) 
	{
		bank_list += "<option value='"+item.id_bank+"'>"+item.acc_number+"</option>";
	});
	
	row += '<tr>'
	            +'<td><select class="form-control nb_type"><option value="RTGS">RTGS</option><option value="NEFT">NEFT</option></select></td>'
	            +'<td><select class="form-control id_bank" name="nb_details[id_bank][]" >'+bank_list+'</select></td> '
	            +'<td><input class="form-control  datemask date nb_date" data-date-format="yyyy-mm-dd" name="nb_details[nb_date][]" type="text" placeholder="NB Date" /></td>'
    	        +'<td><input type="number" class="form-control pay_amount" type="number"/></td>'
    	        +'<td><input class="form-control ref_no" type="text"/></td>'
    	        +'<td><a href="#" onClick="remove_net_banking_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
	        +'</tr>';
	$('#net_banking_details tbody').append(row);
	$('#net_banking_details > tbody').find('tr:last td:eq(0) .pay_amount').focus();
	var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());		
    $('.nb_date').datepicker({ dateFormat: 'yyyy-mm-dd',endDate:today });
}

function remove_net_banking_row(curRow)
{
	curRow.remove();
}

$('#save_net_banking').on('click',function(){
    if(validateNetBankingDetailRow())
    {
        net_banking_details=[];
       var total_amount=0; 
        $('#netbanking_modal .modal-body #net_banking_details > tbody  > tr').each(function(index, tr) {
    		if($(this).find('.pay_amount').val() != ""){
        		total_amount+=parseFloat($(this).find('.pay_amount').val());
        		net_banking_details.push({
        		    'pay_amount':$(this).find('.pay_amount').val(),
        		    'nb_type':$(this).find('.nb_type').val(),
        		    'ref_no':$(this).find('.ref_no').val(),
        		    'id_bank':$(this).find('.id_bank').val(),
        		    'nb_date':$(this).find('.nb_date').val(),
        		});
    		}
    	});
    
    	$('#payment_modes > tbody >tr').each(function(bidx, brow){
    				bill_chit_row = $(this);
    				bill_chit_row.find('#tot_net_banking_amt').html(parseFloat(total_amount).toFixed(2));
    				bill_chit_row.find('#net_banking_pay_details').val(net_banking_details.length>0 ? JSON.stringify(net_banking_details):'');
    			});
    			
    	$('#netbanking_modal').modal('toggle');
    	calculatePaymentCost();
    }
    else
	{
	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please File The Required Fields.."});
	}
   
});

//Net Banking


//Sales Details
$('#create_sales_details_row').on('click', function(){
	if(validateSalesDetailRow())
	{
		create_new_empty_sales_details_row();
	}
	else
	{
	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please File The Required Fields.."});
	}
});

function validateSalesDetailRow(){
	var row_validate = true;
	$('#sales_adjustment_add .modal-body #bill_details > tbody  > tr').each(function(index, tr) {
		if($(this).find('.bill_id').val() == ""){
			row_validate = false;
		}
	});
	return row_validate;
}


function create_new_empty_sales_details_row()
{
	var row = "";
	row += '<tr>'
    	        +'<td><input type="text" class="form-control bill_no"/><input type="hidden" class="form-control bill_id"/></td>'
    	        +'<td><input type="number" class="form-control payment_amount" type="text" readonly/></td>'
    	        +'<td><a href="#" onClick="remove_sales_details_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
	        +'</tr>';
	$('#bill_details tbody').append(row);
	$('#bill_details > tbody').find('tr:last td:eq(0) .pay_amount').focus();
}

function remove_sales_details_row(curRow)
{
	curRow.remove();
}

$('#save_sales_details').on('click',function(){
   sales_details=[];
   var total_amount=0; 
    $('#sales_adjustment_add .modal-body #bill_details > tbody  > tr').each(function(index, tr) {
		if($(this).find('.payment_amount').val() != ""){
    		total_amount+=parseFloat($(this).find('.payment_amount').val());
    		sales_details.push({'pay_amount':$(this).find('.payment_amount').val(),'bill_id':$(this).find('.bill_id').val()});
		}
	});

	$('#payment_modes > tbody >tr').each(function(bidx, brow){
				bill_chit_row = $(this);
				bill_chit_row.find('#tot_sales_amt').html(total_amount);
				bill_chit_row.find('#sales_details').val(sales_details.length>0 ? JSON.stringify(sales_details):'');
			});
			
	$('#sales_adjustment_add').modal('toggle');
	calculatePaymentCost();
});

$(document).on('keyup','.bill_no', function(e){
		var row = $(this).closest('tr'); 
		var bill_no = row.find(".bill_no").val();
		getSearchAcc(bill_no, row);
});


function getSearchAcc(searchTxt, curRow){
	my_Date = new Date();
	var bill_cus_id=$('#bill_cus_id').val();
	$.ajax({
        url: base_url+'index.php/admin_ret_purchase/get_bill_details/?nocache=' + my_Date.getUTCSeconds(),             
        dataType: "json", 
        method: "POST", 
        data: {'searchTxt': searchTxt,'bill_cus_id':bill_cus_id}, 
        success: function (data) {
        	$.each(data, function(key, item){
				$('#bill_details > tbody tr').each(function(idx, row){
					if(item != undefined){
						if($(this).find('.bill_id').val() == item.value){
							data.splice(key, 1);
						}
					}
				});
			});
			$( ".bill_no" ).autocomplete(
			{
			    appendTo: "#sales_adjustment_add",
				source: data,
				select: function(e, i)
				{ 
					e.preventDefault();
					
					curRow.find(".bill_no").val(i.item.label);
					curRow.find(".bill_id").val(i.item.value);
					curRow.find(".payment_amount").val(i.item.tot_bill_amount);
				},
				change: function (event, ui) {
					if (ui.item === null) {
						$(this).val('');
						curRow.find('.bill_no').val('');
						curRow.find(".bill_id").val("");
						curRow.find(".payment_amount").val("");
					}
			    },
				response: function(e, i) {
		            // ui.content is the array that's about to be sent to the response callback.
		            if(searchTxt != ""){
						if (i.content.length === 0) {
						    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter The Valid Bill No.."});
						}
					}else{
					}
		        },
				 minLength: 1,
			});
        }
     });
}

//Sales Details


//Advance adj
$('#advance_adj').on('click',function(){
    if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Supplier.."});
    }
    else
    {
        get_supplier_advance_details();
    }
    
});

function get_supplier_advance_details()
{
    my_Date = new Date();
	var id_karigar=$('#select_karigar').val();
	$.ajax({
        url: base_url+'index.php/admin_ret_purchase/supplier_po_payment/supplier_advance_details/?nocache=' + my_Date.getUTCSeconds(),             
        dataType: "json", 
        method: "POST", 
        data: {'id_karigar': id_karigar}, 
        success: function (data) {
            if(data.amount > 0)
            {
                $('.total_amount').val(data.amount);
                $('.id_wallet').val(data.id_wallet);
                $('.adjusted_amount').val(data.amount);
                $('#advance_adjustment_add').modal('toggle');
            }
            else
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Your Wallet Amount is 0.."});
            }
        }
     });
}

$(document).on('keyup', ".adjusted_amount", function(e) {
    var curRow     = $(this).parent().closest('tr');
    var total_amount = curRow.find('.total_amount').val();
    var adjusted_amount = curRow.find('.adjusted_amount').val();
    if(parseFloat(adjusted_amount)>parseFloat(total_amount))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Adjusted Amount is Grater Than the Receipt Amount"});
        curRow.find('.adjusted_amount').val(0);
    }
});

$('#save_adv_adj_details').on('click',function(){
   advance_details=[];
   var adjusted_amount=0; 
    $('#advance_adjustment_add .modal-body #advance_adj_details > tbody  > tr').each(function(index, tr) {
		if($(this).find('.adjusted_amount').val() != ""){
    		adjusted_amount+=parseFloat($(this).find('.adjusted_amount').val());
    		advance_details.push({'adjusted_amount':$(this).find('.adjusted_amount').val(),'id_wallet':$(this).find('.id_wallet').val()});
		}
	});

	$('#payment_modes > tbody >tr').each(function(bidx, brow){
				bill_chit_row = $(this);
				bill_chit_row.find('#total_adv_adj').html(parseFloat(adjusted_amount).toFixed(2));
				bill_chit_row.find('#adv_details').val(advance_details.length>0 ? JSON.stringify(advance_details):'');
			});
			
	$('#advance_adjustment_add').modal('toggle');
	calculatePaymentCost();
});

//Advance adj


function calculatePaymentCost()
{
	
    var bal_amount             =0;
    var total_amount           =0;
	var receive_amount         =($('.receive_amount').val()!='' ? $('.receive_amount').val():0);
	var net_banking_pay        =($('#tot_net_banking_amt').html()!='' ? $('#tot_net_banking_amt').html():0);
	var tot_sales_amt          =($('#tot_sales_amt').html()!='' ? $('#tot_sales_amt').html():0);
	var total_adv_adj          =($('#total_adv_adj').html()!='' ? $('#total_adv_adj').html():0);
		
	if(receive_amount>0)
	{
	    $('#pay_submit').prop('disabled',false);
		total_amount=parseFloat(parseFloat(net_banking_pay)+parseFloat(tot_sales_amt)+parseFloat(total_adv_adj)).toFixed(2);
		bal_amount = parseFloat(parseFloat(receive_amount)-parseFloat(total_amount)).toFixed(2);
		if(bal_amount==0)
		{
		    $('#pay_submit').prop('disabled',false);
		}else
		{
		    $('#pay_submit').prop('disabled',true);
		}
	}
	else
	{
	    $('#pay_submit').prop('disabled',true);
	}
	console.log('bal_amount:'+bal_amount);
	console.log('receive_amount:'+receive_amount);
	$('#total_pay_amount').html(total_amount);
	$('#bal_amount').html(bal_amount);

}




//rate fixing


//purchase return
function get_returned_po_details()
{
	$("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/purchasereturn/ajax?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 type:"POST",
	 success:function(data){
	 	var list=data.list;
	 	var access=data.access;
		var oTable = $('#pur_return_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#pur_return_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [[ 0, "desc" ]],
            "dom": 'lBfrtip',
            "buttons" : ['excel','print'],
            "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            "aaData": list,
            "aoColumns": [
                { "mDataProp": "pur_return_id" },
                { "mDataProp": "karigar_name" },
				{ "mDataProp": "pur_ret_ref_no" },
                { "mDataProp": "date_add" },
                { "mDataProp": "ret_pcs" },
                { "mDataProp": "ret_wt" },
                { "mDataProp": "pur_ret_status" },
                { "mDataProp": "reason" },
                { "mDataProp": function ( row, type, val, meta ) {
                    id=row.pur_return_id;
                    edit_target=(access.edit=='0'?"":"#confirm-edit");
                    print_url=base_url+'index.php/admin_ret_purchase/return_receipt_acknowladgement/'+id;
                    
                    action_content='<a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Return Receipt"><i class="fa fa-print" ></i></a>'+(row.bill_status==1 && access.edit == 1 ? '<button class="btn btn-warning" onclick="confirm_return_cancel('+id+')"><i class="fa fa-close" ></i></button>' :'');
                    return action_content;
                }
                }
              ]
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}

function confirm_return_cancel(pur_return_id)
{
	$('#pur_return_id').val(pur_return_id);
	$('#confirm-delete').modal('show');
}

$('#ret_cancel_remark').on('keypress',function(){
	if(this.value.length>6)
	{
		$('#ret_cancel').prop('disabled',false);
	}else{
		$('#ret_cancel').prop('disabled',true);
	}
});

$('#ret_cancel').on('click',function(){
    $('#ret_cancel').prop('disabled',true);
	my_Date = new Date();
	$.ajax({
		type: 'POST',
		url:base_url+ "index.php/admin_ret_purchase/purchasereturn/cancel_ret_entry?nocache=" + my_Date.getUTCSeconds(),
		dataType:'json',
		data:{'cancel_reason':$('#ret_cancel_remark').val(),'pur_return_id':$('#pur_return_id').val()},
		success:function(data){
		    window.location.reload();
		}
	});
});


function get_return_item_po_ref_nos()
{
    $("#select_po_ref_no option").remove();
	$.ajax({
	type: 'POST',
	data:{'id_karigar':$('#select_karigar').val()},
	url: base_url+'index.php/admin_ret_purchase/getreturn_po_list',
	dataType:'json',
	success:function(data){
	    var id=$('#select_po_ref_no').val();
		$.each(data, function (key, item) {   
		    $("#select_po_ref_no").append(
		    $("<option></option>")
		    .attr("value", item.value)    
		    .text(item.label)  
		    );
		}); 
		$("#select_po_ref_no").select2(
		{
			placeholder:"Select PO Ref",
			closeOnSelect: true		    
		});
		
		if($("#select_po_ref_no").length)
		{
		    $("#select_po_ref_no").select2("val",(id!='' && id>0?id:''));
		}
		    $(".overlay").css("display", "none");
		}
	});
}


$("#tag_number,#old_tag_number").keypress(function(e) {
    if(e.which == 13) 
    {
        if($('#old_tag_number').val()!='' || $('#tag_number').val()!='' )
        {
            var old_tag_number=$('#old_tag_number').val().replaceAll(' ',''); 
            get_tag_scan_details($("#tag_number").val(),old_tag_number);
        }
        else
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Tag No.."});
        }
        
    }
});

$('#tag_history_search').on('click',function(){
    if($('#old_tag_number').val()!='' || $('#tag_number').val()!='' )
    {
        var old_tag_number=$('#old_tag_number').val().replaceAll(' ',''); 
        get_tag_scan_details($("#tag_number").val(),old_tag_number);
    }else
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Tag No.."});
    }
});
    
    

function get_tag_scan_details(tag_id,old_tag_id)
{
	$("div.overlay").css("display", "block"); 
   var my_Date = new Date();
	$.ajax({
        url:base_url+ "index.php/admin_ret_reports/tag_history/ajax?nocache=" + my_Date.getUTCSeconds(),       
        dataType: "json", 
        method: "POST", 
        data: {'tag_id': tag_id,'old_tag_id': old_tag_id}, 
        success: function (data) {
			if(data.list.length>0)
			{       
			       var rowExists=false;
				    var trHtml='';
			        $.each(data.list, function(key,items){
			            if(items.karigar_id==$('#select_karigar').val())
			            {
			                $.each(returntaggeditemlist,function(key,val){
								if(val.tag_id==items.tag_id)
								{
									rowExists=true;
									$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
								} 
						   });
    					   if(!rowExists)
    					   {
    					       if(data.list[0].tagging_status==0)
    					       {
    					           returntaggeditemlist.push({'cat_id':data.list[0].cat_id,'tag_id':data.list[0].tag_id,'piece':data.list[0].piece,'gross_wt':data.list[0].gross_wt,'net_wt':data.list[0].net_wt,'catname':data.list[0].catname,'tgrp_id':data.list[0].tgrp_id,'other_metal_details':data.list[0].other_metals});
    					           items = data.list[0];
    					           var stone_wt = 0;
    					           var tag_other_itm_grs_weight = 0;
    					           $.each(items.stone_details,function(k,stn){
    					               if(stn.uom_short_code=='CT')
    					               {
    					                   stone_wt+=parseFloat(stn.stone_wt/5);
    					               }
    					               else
    					               {
    					                   stone_wt+=parseFloat(stn.stone_wt);
    					               }
    					           });
    					           $.each(items.other_metal_details,function(k,stn){
    					               tag_other_itm_grs_weight+=parseFloat(stn.tag_other_itm_grs_weight);
    					           });
    					            if(insertedcatdetails.length > 0)
                                    {
                                        $.each(insertedcatdetails,function(key,val){
                                            if(val.cat_id == items.cat_id)
                                            {
                                                insertedcatdetails[key].piece = parseFloat(insertedcatdetails[key].piece)+parseFloat(items.piece);
                                                insertedcatdetails[key].gross_wt = parseFloat(parseFloat(insertedcatdetails[key].gross_wt)+parseFloat(items.gross_wt)).toFixed(3);
                                            }else
                                            {
                                                insertedcatdetails.push({'cat_id':items.cat_id,'piece':items.piece,'catname':items.catname,'gross_wt':items.gross_wt,'tgrp_id':items.tgrp_id,'less_wt':stone_wt,'tag_other_itm_grs_weight':tag_other_itm_grs_weight,'stone_details':JSON.stringify(data.list[0].stone_details),'other_metal_details':JSON.stringify(data.list[0].other_metal_details)});
                                            }
                                        });
                                    }
                                    else
                                    {
                                        insertedcatdetails.push({'cat_id':items.cat_id,'piece':items.piece,'catname':items.catname,'gross_wt':items.gross_wt,'tgrp_id':items.tgrp_id,'less_wt':stone_wt,'tag_other_itm_grs_weight':tag_other_itm_grs_weight,'stone_details':JSON.stringify(data.list[0].stone_details),'other_metal_details':JSON.stringify(data.list[0].other_metal_details)});
                                    }
    					           
    					       }
    					       else
    					       {
    					           $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Tag Not Availabe."});
    					       }
    						}
			            }
			            else
			            {
			                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Invalid Supplier.."});
			            }
					});
					updatereturncategory();
					$("div.overlay").css("display", "none"); 
				
			}else{
			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Record Found.."});
				$("div.overlay").css("display", "none"); 
			}
			$('#tag_number').val('');
			$('#old_tag_number').val('');
        }
     });
}

function updatereturncategory()
{
    $('#return_item_detail > tbody').empty();
    var cattrHtml ='';
    var purreturnpcs    = 0;
    var purreturnweight = 0;
    var nonTagProdExists=false;
    //insertedcatdetails = [];
    $.each(available_metal_stock, function(key,items){
        if(items.id_product == $('#select_product').val() && items.design == $('#select_design').val() && items.id_sub_design == $('#select_sub_design').val() )
        {
                $.each(nontagreturnitemlist,function(key,val){
                    if(val.id_product == items.id_product && items.design == val.design && items.id_sub_design == val.id_sub_design )
                    {
                        nonTagProdExists=true;
                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
                    }
                });
            if(!nonTagProdExists)
            {
                nontagreturnitemlist.push({'tgrp_id':items.tgrp_id,'cat_id':items.cat_id,'id_product':items.id_product,'id_design':items.design,'id_sub_design':items.id_sub_design,'piece':$('#issue_pcs').val(),'gross_wt':$('#issue_weight').val(),'net_wt':$('#issue_weight').val(),'catname':items.category_name});
                
            }
        }
    });
    
    if(insertedcatdetails.length > 0)
    {
        $.each(insertedcatdetails,function(key,val){
            $.each(non_tag_category,function(k,nt_val){
                if(nt_val.cat_id == val.cat_id && ($('#select_product').val()==nt_val.pro_id) )
                {
                    issue_pcs = ($('#issue_pcs').val()!='' ? $('#issue_pcs').val():0);
                    issue_weight = ($('#issue_weight').val()!='' ? $('#issue_weight').val():0);
                    insertedcatdetails[key].piece = parseFloat(insertedcatdetails[key].piece)+parseFloat(issue_pcs);
                    insertedcatdetails[key].gross_wt = parseFloat(parseFloat(insertedcatdetails[key].gross_wt)+parseFloat(issue_weight)).toFixed(3);
                }
            });
                
         });
    }
    else
    {
        if(nontagreturnitemlist.length > 0)
        {
            $.each(nontagreturnitemlist,function(k,nt_val){
                insertedcatdetails.push({'cat_id':nt_val.cat_id,'piece':$('#issue_pcs').val(),'catname':nt_val.catname,'gross_wt':$('#issue_weight').val(),'tgrp_id':nt_val.tgrp_id});
            });
        }
    }

    
    
    
     
    
    console.log(nontagreturnitemlist);
    console.log(returntaggeditemlist);
    console.log(purchasereturnitemlist);
    console.log(insertedcatdetails);
    $.each(insertedcatdetails, function(key,items){
        var net_wt = parseFloat(parseFloat(items.gross_wt)-parseFloat(items.less_wt)-parseFloat(items.tag_other_itm_grs_weight)).toFixed(3);
        var length  =$('#return_item_detail tbody tr').length+1;
        cattrHtml+='<tr id="'+length+'">'
				+'<td><input type="hidden" class="return_item_tax_id" value="'+items.tgrp_id+'" /><input type="hidden" class="return_item_tax_percent" value="" /><input type="hidden" class="return_item_tax_cgst_value" value="" /><input type="hidden" class="return_item_tax_sgst_value" value="" /><input type="hidden" class="return_item_tax_igst_value" value="" /><input type="hidden" class="return_item_tax_value" value="" /><input type="checkbox" class="return_item_cat_id" name="return_item_cat[catid][]" value="'+items.cat_id+'" checked /></td>'
				+'<td>'+items.catname+'</td>'
                +'<td><input type="number" class="form-control custom-inp purreturnpcs" name="item[pcs][]" value="'+ parseInt(items.piece) +'" style="width:100px;" step="any" readonly></td>'
                +'<td><input type="number" class="form-control custom-inp purreturnweight" name="item[gross_wt][]" value="'+ parseFloat(items.gross_wt).toFixed(3) +'" style="width:100px;" step="any" readonly></td>'
                +'<td><div class="form-group"><div class="input-group "><input class="form-control custom-inp add_less_wt" value="'+ parseFloat(items.less_wt).toFixed(3) +'" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="item[less_wt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'
                +'<td><div class="form-group"><div class="input-group "><input class="form-control custom-inp add_other_metal_wt" value="'+parseFloat(items.tag_other_itm_grs_weight).toFixed(3)+'" onClick="create_new_empty_other_metal_row($(this).closest(\'tr\'));"  type="number" step="any" style="width:100px;" readonly/><span class="input-group-addon input-sm add_other_metal_wt" onClick="create_new_empty_other_metal_row($(this).closest(\'tr\'));">+</span></div></div></td>'
                +'<td><input type="number" class="form-control custom-inp net_wt" name="item[net_wt][]" value="'+net_wt+'" style="width:100px;" step="any" readonly><input type="hidden" class="stone_details" name="item[stone_details][]" value='+items.stone_details+' /><input type="hidden" class="other_metal_details" name="item[other_metal_details][]" value='+items.other_metal_details+' /></td>'
               	+'<td><div class="input-group"><input class="purreturnrate form-control" type="number" name="return_item_cat[price][]" value="" style="width:100px;"><span class="input-group-btn" ><select class="purreturncaltype form-control" name="return_item_cat[caltype][]" style="width: 100px;" ><option value="1" selected="selected">Grm</option><option value="2">Pcs</option></select></span></div></td>'
				+'<td><input type="number" class="form-control custom-inp returnitemcost" style="width:100px;" step="any"><input type="hidden" class="form-control custom-inp itemcaltype" style="width:100px;" step="any" value="1"></td>'
				+'<td><input type="number" class="purreturnamount form-control" value="" name="return_item_cat[amount][]" readonly /></td>'
				+'<td><a href="#" class="btn btn-danger btn-del btn-xs" style="padding:5px;" onClick="remove_pur_ret_row($(this).closest(\'tr\'));"><i class="fa fa-trash"></i></a></td>'
				+'</tr>';
    });
    $('#return_item_detail tbody').append(cattrHtml);
    $('#issue_weight').val("");
    $('#issue_pcs').val("");
    $('.available_weight').html('');
    $('.available_pcs').html('');
    $('#select_product').select2("val","");
    $('#select_design').select2("val","");
    $('#select_sub_design').select2("val","");
    $('#select_po_ref_no').select2("val","");
}

function get_qc_rejected_details_by_poid(poid)
{
	$("div.overlay").css("display", "block"); 
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_purchase/get_qc_faild_items_by_poid',
		dataType:'json',
		data: {'porefid' : poid},
		success:function(data){
			if(data.purchaseaitems.length)
                {
                    var rowExists=false;
                    var catrowExists=false;
				    var trHtml='';
				    var cattrHtml='';
					$.each(data.purchaseaitems, function(key,items){
							$.each(purchasereturnitemlist,function(key,val){
								if(val.po_item_id==items.po_item_id)
								{
									rowExists=true;
									$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
								} 
						   });
					
						   
					   if(!rowExists)
					   {
						    purchasereturnitemlist.push({'cat_id':items.cat_id,'po_item_id':items.po_item_id,'piece':items.qcfaildpcs,'gross_wt':items.qcfaildwt,'net_wt':items.qcfaildwt,'catname':items.catname,'tgrp_id':items.tgrp_id});
                            if(insertedcatdetails.length > 0)
                            {
                                $.each(insertedcatdetails,function(key,val){
                                    if(val.cat_id == items.cat_id)
                                    {
                                        insertedcatdetails[key].piece = parseFloat(insertedcatdetails[key].piece)+parseFloat(items.qcfaildpcs);
                                        insertedcatdetails[key].gross_wt = parseFloat(parseFloat(insertedcatdetails[key].gross_wt)+parseFloat(items.qcfaildwt)).toFixed(3);
                                    }else
                                    {
                                        insertedcatdetails.push({'cat_id':items.cat_id,'piece':items.qcfaildpcs,'catname':items.catname,'gross_wt':items.qcfaildwt,'tgrp_id':items.tgrp_id});
                                    }
                                });
                            }
                            else
                            {
                                insertedcatdetails.push({'cat_id':items.cat_id,'piece':items.qcfaildpcs,'catname':items.catname,'gross_wt':items.qcfaildwt,'tgrp_id':items.tgrp_id});
                            }
                           
					       
					   }
					});
					
					
                    $("div.overlay").css("display", "none"); 
                }else
                {
                    $("div.overlay").css("display", "none"); 
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
                    $('#po_ref_no').val('');
                    $('#po_ref_no').focus();
                }
                updatereturncategory();
                get_purchase_return_total();
		}
	});
}

function get_qc_rejected_details_by_supid(supid)
{
	$("div.overlay").css("display", "block"); 
	$.ajax({
		type: 'POST',
		url: base_url+'index.php/admin_ret_purchase/get_qc_faild_items_by_supid',
		dataType:'json',
		data: {'supid' : supid},
		success:function(data){
			if(data.purchaseaitems.length)
                {
                    var rowExists=false;
				    var trHtml='';
				    returnitemlist = [];
				    $('#item_detail > tbody').empty();
					$.each(data.purchaseaitems, function(key,items){
							$('#item_detail > tbody tr').each(function(bidx, brow){
							curRow = $(this);
								if(curRow.find('.po_item_id').val()==items.po_item_id)
								{
									rowExists=true;
									$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
								} 
						   });
					   if(!rowExists)
					   {
						    returnitemlist.push(data.purchaseaitems);
							trHtml+='<tr>'
							+'<td><input type="checkbox" class="po_item_id" name="po_item_id[]" value="'+items.po_item_id+'" checked/><input type="hidden" class="tag_id" name="tag_id[]" value="" /><input type="hidden" class="po_id" name="po_id[]" value="'+items.po_item_id+'" /><input type="hidden" class="po_kar_id" name="return_po_kar_id[]" value="'+items.karigar_id+'" /><input type="hidden" class="po_cat_id" name="return_po_cat_id[]" value="'+items.cat_id+'" />'+items.po_ref_no+'</td>'
							+'<td>'+items.karigar+'</td>'
							+'<td>'+items.product_name+'</td>'
							+'<td>'+items.design_name+'</td>'
							+'<td>'+items.sub_design_name+'</td>'
							+'<td>'+items.purchasedpcs+'</td>'
							+'<td>'+items.purchasedwt+'</td>'
							+'<td><input type="hidden" class="qcfaildpcs"  value="'+items.qcfaildpcs+'" />'+items.qcfaildpcs+'</td>'
							+'<td><input type="hidden" class="qcfaildwt"   value="'+items.qcfaildwt+'" />'+items.qcfaildwt+'</td>'
							+'<td></td>'
							+'<td><a href="#" class="btn btn-danger btn-del btn-xs" style="padding:5px;" onClick="remove_pur_ret_row($(this).closest(\'tr\'));"><i class="fa fa-trash"></i></a></td>'
							+'</tr>';
						}
					});

					if($('#item_detail > tbody  > tr').length>0)
					{
						$('#item_detail > tbody > tr:first').before(trHtml);
					}else{
						$('#item_detail tbody').append(trHtml);
					}
					get_purchase_return_total();
					updatereturncategory();
                    $("div.overlay").css("display", "none"); 
                }else
                {
                    $("div.overlay").css("display", "none"); 
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
                    $('#po_ref_no').val('');
                    $('#po_ref_no').focus();
                }
		}
	});
}

function get_purchase_return_total()
{
    var total_pcs=0;
    var total_wt=0;
    $("#item_detail tbody tr").each(function(index, value){
        if ($(value).find('input[type="checkbox"]').is(':checked')) {
            total_pcs+=parseFloat($(value).find(".qcfaildpcs").val());
            total_wt+=parseFloat($(value).find(".qcfaildwt").val());
        }
    });
    $('.return_pcs').html(total_pcs);
    $('.return_wt').html(parseFloat(total_wt).toFixed(3));
}

function remove_pur_ret_row(curRow)
{
    var cat_id = curRow.find('.return_item_cat_id').val();
    $.each(nontagreturnitemlist,function(k,nt_val){
        if(nt_val.cat_id == cat_id)
        {
            nontagreturnitemlist.splice(k, 1);
        }
    });
    
    $.each(returntaggeditemlist,function(k,nt_val){
        if(nt_val.cat_id == cat_id)
        {
            returntaggeditemlist.splice(k, 1);
        }
    });
    
    $.each(purchasereturnitemlist,function(k,nt_val){
        if(nt_val.cat_id == cat_id)
        {
            purchasereturnitemlist.splice(k, 1);
        }
    });
    
    console.log(nontagreturnitemlist);
    console.log(returntaggeditemlist);
    curRow.remove();
    calculate_purchase_return_final_cost();
    get_purchase_return_total();
}

$('#return_po_items_submit').on('click',function(){
    var allowsubmit = true;
    var approve = false;
    if($('#select_karigar').val()=='' || $('#select_karigar').val()== null){
        allowsubmit = false;
    }
    if(!allowsubmit)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please select karigar."});
        return;
    }
    $('#return_item_detail > tbody tr').each(function(idx, row){
         curRow = $(this);
         if(curRow.find('.purreturnrate').val() == "" || curRow.find('.purreturnamount').val() == ""){
             allowsubmit = false;
         }
    });
    if($('.return_total_cost').val() == "" || $('.return_total_cost').val() == 0){
        allowsubmit = false;
    }
    
    if(!allowsubmit)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please fill required fields."});
    }
    else
    {
      
    	transCatData = [];
    	$("#return_item_detail tbody tr").each(function(index, value){
    		if($(value).find("input[name='return_item_cat[catid][]']:checked").val() != undefined){
    				transCatData.push({ 
    					'cat_item_id'       : $(value).find("input[name='return_item_cat[catid][]']:checked").val(),
    					'cat_id'            : $(value).find(".return_item_cat_id").val(),
    					'cat_pcs'           : $(value).find(".purreturnpcs").val(),
    					'cat_gwt'           : $(value).find(".purreturnweight").val(),
    					'pur_ret_rate'      : $(value).find(".purreturnrate").val(),
    					'purreturncaltype'  : $(value).find(".purreturncaltype").val(),
    					'ret_item_cost'     : $(value).find(".purreturnamount").val(),
    					'ret_tax_value'     : $(value).find(".return_item_tax_percent").val(),
    					'ret_tax_rate'      : $(value).find(".return_item_tax_value").val(),
    					'ret_cgst_value'    : $(value).find(".return_item_tax_cgst_value").val(),
    					'ret_sgst_value'    : $(value).find(".return_item_tax_sgst_value").val(),
    					'ret_igst_value'    : $(value).find(".return_item_tax_igst_value").val(),
    					'stone_details'     : $(value).find(".stone_details").val(),
    					'other_metal_details'     : $(value).find(".other_metal_details").val(),
    				});
    			approve = true;
    		}
    	});

        if(transCatData.length == 0)
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please select any one items."});
        }
        else
        {
            $("div.overlay").css("display", "block"); 
            $('#return_po_items_submit').prop('disabled',true);
    		$.ajax({
    			type: 'POST',
    			url: base_url+'index.php/admin_ret_purchase/updateporeturnitems',
    			dataType:'json',
    			data: { 
    			        "returncatitems"        : transCatData, 
    			        "nontagreturnitemlist"  : nontagreturnitemlist, 
    			        "returntaggeditemlist"  : returntaggeditemlist, 
    			        "purchasereturnitemlist": purchasereturnitemlist, 
    			        'id_karigar'            : $('#select_karigar').val(), 
    			        'cmp_country'           : $('#cmp_country').val(), 
    			        'cmp_state'             : $('#cmp_state').val(), 
    			        'supplier_country'      : $('#cmp_state').val(), 
    			        'supplier_state'        : $('#supplier_state').val(), 
    			        'returnreason'          : $('input[name="returnreason"]:checked').val(), 
    			        'narration'             : $('#returnnarration').val(), 
    			        'other_charges_details' : $('#other_charges_details').val(), 
    			        'returnamount'          : $('.return_total_cost').val(), 
    			        'returnroundoff'        : $('.return_round_off').val(), 
    			        'return_discount'       : $('.return_discount').val(), 
    			        'tds_percent'           : $('.tds_percent').val(), 
    			        'tds_tax_value'         : $('.tds_tax_value').val(), 
    			        'tcs_percent'           : $('.tcs_percent').val(), 
    			        'tcs_tax_value'         : $('.tcs_tax_value').val(), 
    			        'other_metal_details'   : $('.other_metal_details').val(), 
    			        'charges_tds_percent'   : $('.charges_tds_percent').val(), 
    			        'other_charges_tds_tax_value'   : $('.other_charges_tds_tax_value').val(), 
    			        'other_stone_details'   : $('.stone_details').val(), 
    			        'other_charges'         : parseFloat(parseFloat($('#other_charges_taxable_amount').val())+parseFloat($('.other_charges_tax').val())).toFixed(2), 
    			        'return_item_type'      : return_item_type
    			},
    			success:function(data){
    				if(data.success){
    					$.toaster({ priority : 'success', title : 'Success!', message : ''+data.message});
    					window.open( base_url+'index.php/admin_ret_purchase/return_receipt_acknowladgement/'+data.return_id,'_blank');
    					window.location.href= base_url+'index.php/admin_ret_purchase/purchasereturn/list';
    				}else
                    {
                        $("div.overlay").css("display", "none"); 
                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+data.message});
                        
                    }
    			}
    		});
            
        }
    }
    
	
});
//purchase return end here 



$('#add_image').on('click',function(){
    $('#imageModal_new').modal('show');
});


$("#update_img_new").on('click',function()
{
	var final_file = [];
	var retrive_file = []; 
    let image_details=localStorage['img_details'];
    console.log(image_details);
	if(image_details)
	 {
	   img_final = JSON.parse(image_details);
	 }
	 localStorage.removeItem("img_details");
	 //$('#order_iamges').val(JSON.stringify(image_details));
	 $('#order_iamges').val(encodeURIComponent(JSON.stringify(image_details)));
	  $('#imageModal_new').modal('toggle');
});



//Order Description
$('#add_order_des').on('click',function(){
    var order_des= CKEDITOR.instances.order_des.getData();
    if(order_des=='')
    {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Description"});
    }
    else
    {
        my_Date = new Date();
        $("div.overlay").css("display", "block");
        $.ajax({
            url:base_url+ "index.php/admin_ret_purchase/order_description/save?nocache=" + my_Date.getUTCSeconds(),
            dataType:"JSON",
            data:{'order_des':order_des},
            type:"POST",
            success:function(data){
                if(data.status)
                {
                    $("div.overlay").css("display", "none");
                    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                    window.location.reload();
                }else
                {
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                }
                
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });
    }
});

function get_order_description()
{
       $("div.overlay").css("display", "block"); 
    	my_Date = new Date();
    	$.ajax({
    	 url:base_url+ "index.php/admin_ret_purchase/order_description/ajax?nocache=" + my_Date.getUTCSeconds(),
    	 dataType:"JSON",
    	 type:"POST",
    	 success:function(data){
    	 	var list=data.list;
    	 	var access=data.access;
    		var oTable = $('#des_list').DataTable();
    		oTable.clear().draw();				  
    		if (list!= null && list.length > 0)
    		{  	
    			oTable = $('#des_list').dataTable({
                "bDestroy": true,
                "bInfo": true,
                "bFilter": true,
                "bSort": true,
                "order": [[ 0, "desc" ]],
                "dom": 'lBfrtip',
                "buttons" : ['excel','print'],
                "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
                "aaData": list,
                "aoColumns": [
               
                { "mDataProp": "id_order_des" },
                { "mDataProp": "description" },
                { "mDataProp": function ( row, type, val, meta ) {
                    id=row.id_order_des;
                    edit_target=(access.edit=='0'?"":"#confirm-edit");
                    delete_url=(access.delete=='1'  ? base_url+'index.php/admin_ret_purchase/order_description/delete/'+id : '#' );
                    delete_confirm= (access.delete=='1' ?'#confirm-delete':'');
                    action_content='<a href="#" class="btn btn-primary btn-edit" id="edit" role="button" data-toggle="modal" data-id='+id+'  data-target='+edit_target+'><i class="fa fa-edit" ></i></a> <a href="#" class="btn btn-danger btn-del" data-href='+delete_url+' data-toggle="modal" data-target="#confirm-delete" ><i class="fa fa-trash"></i></a>';
                    return action_content;
                }
                }
                ]
                });
    		
    		}
    		$("div.overlay").css("display", "none"); 
    	  },
    	  error:function(error)  
    	  {
    		 $("div.overlay").css("display", "none"); 
    	  }	 
      });
}


$(document).on('click', "#des_list a.btn-edit", function(e) {
	e.preventDefault();
	id=$(this).data('id');
    get_description(id);
    $("#edit-id").val(id);  
});	

function get_description(id)
{
   my_Date = new Date();
	$.ajax({
		type:"GET",
		url: base_url+"index.php/admin_ret_purchase/order_description/edit/"+id+"?nocache=" + my_Date.getUTCSeconds(),
		cache:false,		
		dataType:"JSON",
		success:function(data){
			$('#ed_rder_des').val(data.description);
			if($('#ed_rder_des').length > 0)
             {
             	CKEDITOR.replace('ed_rder_des');
             }
			// console.log(wt); 
		}
	});
}

$('#update_order_des').on('click',function(){
    var order_des= CKEDITOR.instances.ed_rder_des.getData();
    if(order_des=='')
    {
         $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Description"});
    }
    else
    {
        my_Date = new Date();
        $("div.overlay").css("display", "block");
        $.ajax({
            url:base_url+ "index.php/admin_ret_purchase/order_description/update?nocache=" + my_Date.getUTCSeconds(),
            dataType:"JSON",
            data:{'order_des':order_des,'id_order_des':$("#edit-id").val()},
            type:"POST",
            success:function(data){
                if(data.status)
                {
                    $("div.overlay").css("display", "none");
                    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
                    window.location.reload();
                }else
                {
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
                }
                
            },
            error:function(error)  
            {
                $("div.overlay").css("display", "none"); 
            }	 
        });
    }
});
	
//Order Description


//Karigar Metal Issue

$("input[name='issue[issue_aganist]']:radio").on('change',function(){
    if(this.value==1)
    {
        $('#select_po_no').prop('disabled',false);
        if($('#select_karigar').val()!='' && $('#select_karigar').val()!=null)
        {
            get_karigar_pending_orders(); 
        }
    }else
    {
        $('#select_po_no').prop('disabled',true);
    }
});

$('#add_metal_issue').on('click',function(){
    if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Karigar"});
    }
    else if($('#select_category').val()=='' || $('#select_category').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Category"});
    }else if($('#select_product').val()=='' || $('#select_product').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Product"});
    }
    else if($('#select_design').val()=='' || $('#select_design').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Design"});
    }
    else if($('#select_sub_design').val()=='' || $('#select_sub_design').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Sub Design"});
    }
    else if($('#select_purity').val()=='' || $(select_purity).val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Purity"});
    }
   else if($('#issue_pcs').val()=='' || $('#issue_pcs').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Pcs"});
    }
    else if($('#issue_weight').val()=='' || $('#issue_weight').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Weight"});
    }
    else if($('#pur_weight').val()=='' || $('#pur_weight').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Pure Weight"});
    }
    else
    {
        create_metal_issue_row();
    }
        
});

$('#issue_weight,#purchase_touch').on('keyup',function(){
    var issue_pcs = (isNaN($('#issue_pcs').val()) || $('#issue_pcs').val()=='' ? 0:$('#issue_pcs').val());
    var tot_weight = (isNaN($('#issue_weight').val()) || $('#issue_weight').val()=='' ? 0:$('#issue_weight').val());
    var purity     = (isNaN($('#select_purity').val()) || $('#select_purity').val()=='' ? 0:$('#select_purity option:selected').text());

    var purewt = parseFloat((parseFloat(tot_weight) * (parseFloat(purity))) / 100).toFixed(3);
   
    $('#pur_weight').val(purewt);
});

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


function reset_matal_issue_form()
{
    $('#select_category').select2("val","");
    $('#select_product').select2("val","");
    $('#select_purity').select2("val","");
    $('#issue_weight').val("");
    $('#issue_pcs').val("");
    $('#pur_weight').val("");
}

function create_metal_issue_row()
{
    var trHtml='';
        trHtml+='<tr>'
                 +'<td><input type="hidden" class="metal" name="metal_details[metal][]" value="'+$('#select_metal').val()+'"><input type="hidden" class="category" name="metal_details[category][]" value="'+$('#select_category').val()+'">'+$('#select_category option:selected').text()+'</td>'
                 +'<td><input type="hidden" class="product" name="metal_details[id_product][]" value="'+$('#select_product').val()+'"><input type="hidden" class="design" name="metal_details[id_design][]" value="'+$('#select_design').val()+'"><input type="hidden" class="id_sub_design" name="metal_details[id_sub_design][]" value="'+$('#select_sub_design').val()+'">'+$('#select_product option:selected').text()+'</td>'
                 +'<td><input type="hidden" class="purity" name="metal_details[purity][]" value="'+$('#select_purity').val()+'">'+$('#select_purity option:selected').text()+'</td>'
                 +'<td><input type="hidden" class="issue_pcs" name="metal_details[pcs][]" value="'+$('#issue_pcs').val()+'">'+$('#issue_pcs').val()+'</td>'
                 +'<td><input type="hidden" class="weight" name="metal_details[weight][]" value="'+$('#issue_weight').val()+'">'+$('#issue_weight').val()+'</td>'
                 +'<td><input type="hidden" class="pur_weight" name="metal_details[pur_weight][]" value="'+$('#pur_weight').val()+'">'+$('#pur_weight').val()+'</td>'
                 +'<td><a href="#" class="btn btn-danger btn-del btn-xs" style="padding:5px;" onClick="remove_row($(this).closest(\'tr\'));"><i class="fa fa-trash"></i></a></td>'
        +'</tr>';
        if($('#metal_details > tbody  > tr').length>0)
        {
            $('#metal_details > tbody > tr:first').before(trHtml);
        }else{
            $('#metal_details tbody').append(trHtml);
        }
    reset_matal_issue_form();
}

$('#submit_metal_issue').on('click',function(){
    $('#submit_metal_issue').prop('disabled',true);
    if($('#metal_details >tbody tr').length>0)
    {
        $("div.overlay").css("display", "block"); 
    	$('#submit_metal_issue').prop('disabled',true);
    	var form_data=$('#metal_issue_form').serialize();
    		var url=base_url+ "index.php/admin_ret_purchase/karigarmetalissue/save?nocache=" + my_Date.getUTCSeconds();
    	    $.ajax({ 
    	        url:url,
    	        data: form_data,
    	        type:"POST",
    	        dataType:"JSON",
    	        success:function(data){
    	            if(data.status)
    	            {
    	                $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
    	                window.location.href= base_url+'index.php/admin_ret_purchase/karigarmetalissue/list';
    	                window.open( base_url+'index.php/admin_ret_purchase/karigarmetalissue_acknowladgement/'+data.insId,'_blank');
    	                $("div.overlay").css("display", "none"); 
    	            }else
    	            {
    	                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
    	                $("div.overlay").css("display", "none"); 
    	            }
    				
    	        },
    	        error:function(error)  
    	        {	
    	            $("div.overlay").css("display", "none"); 
    	        } 
    	    });
    	$('#submit_metal_issue').prop('disabled',false);
    }else
    {
        $('#submit_metal_issue').prop('disabled',false);
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records to Update"});
    }
});

function remove_row(curRow)
{
    curRow.remove();
}

function get_karigar_metal_issue_list()
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
	 url:base_url+ "index.php/admin_ret_purchase/karigarmetalissue/ajax?nocache=" + my_Date.getUTCSeconds(),
	 dataType:"JSON",
	 type:"POST",
	 success:function(data){
	 	var list=data.list;
		var oTable = $('#stock_issue_list').DataTable();
		oTable.clear().draw();				  
		if (list!= null && list.length > 0)
		{  	
			oTable = $('#stock_issue_list').dataTable({
            "bDestroy": true,
            "bInfo": true,
            "bFilter": true,
            "bSort": true,
            "order": [[ 0, "desc" ]],
            "dom": 'lBfrtip',
            "buttons" : ['excel','print'],
            "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
            "aaData": list,
            "aoColumns": [
                { "mDataProp": "met_issue_id" },
                { "mDataProp": "issue_date" },
                { "mDataProp": "met_issue_ref_id" },
                { "mDataProp": "karigar_name" },
                { "mDataProp": "metal_wt" },
                { "mDataProp": "issue_metal_pur_wt" },
                { "mDataProp": "pur_no" },
                { "mDataProp": "bill_status" },
                { "mDataProp": function ( row, type, val, meta ) {
                    	 id= row.met_issue_id;
                    	 print_url=base_url+'index.php/admin_ret_purchase/karigarmetalissue_acknowladgement/'+id;
                    	 action_content='<a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Metal Issue Acknowladgement"><i class="fa fa-print" ></i></a>'+(row.bill_status=='Success'?'<button class="btn btn-warning" onclick="confirm_issue_cancel('+id+')"><i class="fa fa-close" ></i></button>':''+'');
                         return action_content;
                	}
                }
              ]
            });
		
		}
		$("div.overlay").css("display", "none"); 
	  },
	  error:function(error)  
	  {
		 $("div.overlay").css("display", "none"); 
	  }	 
  });
}

function confirm_issue_cancel(met_issue_id){
    $('#metal_issue_id').val(met_issue_id);
	$('#confirm-billcancell').modal('show');
}


$('#metal_issue_cancel_remark').on('keypress',function(){
	if(this.value.length>6)
	{
		$('#cancell_delete').prop('disabled',false);
	}else{
		$('#cancell_delete').prop('disabled',true);
	}
});

$('#cancell_delete').on('click',function(){

	my_Date = new Date();
	$.ajax({
		type: 'POST',
		url:base_url+ "index.php/admin_ret_purchase/karigarmetalissue/metalissue_cancel?nocache=" + my_Date.getUTCSeconds(),
		dataType:'json',
		data:{'remarks':$('#metal_issue_cancel_remark').val(),'metal_issue_id':$('#metal_issue_id').val()},
		success:function(data){
		    if(data.status)
		    {
		        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
		        window.location.reload();
		    }else
		    {
		        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
		        window.location.reload();
		    }
		    
		}
	});
});

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

            //set_availabe_purity();
            $("div.overlay").css("display", "none"); 
        },
        error:function(error)  
        {
            $("div.overlay").css("display", "none"); 
        }	 
    });
}

$('#issue_from').on('change',function(){
    set_available_stock_details();
    set_availabe_purity();
});

function set_available_stock_details()
{
    var trHtml='';
    $('#available_stock_details > tbody').empty();
    $.each(available_metal_stock,function(key,items){
        if($('#select_product').val()==items.id_product)
        {
            trHtml+='<tr>'
                    +'<td>'+items.product_name+'</td>'
                    +'<td>'+items.gross_wt+'</td>'
                    +'<td>'+items.net_wt+'</td>'
                +'</tr>';
       }
        
    });
    $('#available_stock_details > tbody').append(trHtml);
}


function set_availabe_purity()
{
       $("#purity option").remove();
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
//Karigar Metal Issue


//Purchase return start here

function create_new_empty_est_cus_stone_item(curRow,id)
{
    
    $('#estimation_stone_cus_item_details tbody').empty();
	if(curRow!=undefined)
	{
		$('#custom_active_id').val(curRow.closest('tr').attr('id'));
	}
	var row = "";

		var catRow=$('#custom_active_id').val();
		
		console.log(catRow);
		var row_st_details=$('#'+catRow).find('.stone_details').val();
		if(row_st_details !='' && row_st_details != '[]' && curRow != undefined)
		{

			var stone_details   = JSON.parse(row_st_details);
			console.log(stone_details);
			$.each(stone_details, function (pkey, pitem) {
	 			var stones_list='';
	 			var stones_type_list='';
	 			var uom_list='';
	 			var html='';
	 			var cal_type = pitem.stone_cal_type;
				$.each(stones, function (pkey, item) 
				{
					var selected = "";
					if(item.stone_id == pitem.stone_id)
					{
						selected = "selected='selected'";
					}
					stones_list += "<option value='"+item.stone_id+"' "+selected+">"+item.stone_name+"</option>";
				});	
				
				
				$.each(stone_types, function (pkey, item) {
				    var st_type_selected = "";
				    if(item.id_stone_type == pitem.stones_type)
					{
						st_type_selected = "selected='selected'";
					}
				    stones_type_list += "<option value='"+item.id_stone_type+"' "+st_type_selected+">"+item.stone_type+"</option>";
    			});
    			
    			$.each(uom_details, function (pkey, item) {
    			     var uom_selected = "";
    			    if(item.uom_id == pitem.stone_uom_id)
					{
						uom_selected = "selected='selected'";
					}
    				uom_list += "<option value='"+item.uom_id+"' "+uom_selected+">"+item.uom_name+"</option>";
    			});
			    
			   
				row += '<tr>'
                	+'<td><input class="show_in_lwt" type="checkbox" name="est_stones_item[show_in_lwt][]" value="'+(pitem.show_in_lwt==1 ? 1:0)+'" '+(pitem.show_in_lwt==1 ? 'checked' :'' )+' ></td>'
                	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]"  >'+stones_type_list+'</select></td>'
					+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"  >'+stones_list+'</select></td>'
					+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]"  value="'+pitem['stone_pcs']+'" style="width: 100%;"/></td>'
					+'<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="'+pitem['stone_wt']+'" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]"  >'+uom_list+'</select></span></div></td>'
					+'<td><div class="form-group"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+pkey+']" value="1" '+(cal_type == 1 ? 'checked' : '')+'> By Wt&nbsp;<input type="radio"  name="est_stones_item[cal_type]['+pkey+']" '+(cal_type == 2 ? 'checked' : '')+' class="stone_cal_type" value="2">By Pcs</div></td>'
					+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value="'+pitem['stone_rate']+'"  style="width:100%;"/></td>'
					+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value="'+pitem['stone_price']+'"  style="width:100%;" /></td>'
					+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';
			});
		
		}
		else
		{
		    
    			var stones_list = "<option value=''>-Select Stone-</option>";
    			var stones_type = "<option value=''>-Stone Type-</option>";
    			var uom_list = "<option value=''>-Stone UOM-</option>";
    			$.each(stones, function (pkey, pitem) {
    				stones_list += "<option value='"+pitem.stone_id+"'>"+pitem.stone_name+"</option>";
    			});
    			
    			$.each(stone_types, function (pkey, pitem) {
    				stones_type += "<option value='"+pitem.id_stone_type+"'>"+pitem.stone_type+"</option>";
    			});
    			
    			$.each(uom_details, function (pkey, pitem) {
    				var selected = pitem.is_default == 1 ? "selected='selected'" : "";
    				uom_list += "<option value='"+pitem.uom_id+"' "+selected+">"+pitem.uom_name+"</option>";
    			});
    			var rowId = $('#estimation_stone_cus_item_details tbody tr').length;
    			var active_row = new Date().getTime();
                    row += '<tr id="'+active_row+'">'
                    	+'<td><input class="show_in_lwt" type="checkbox"name="est_stones_item[show_in_lwt][]" value="1" checked></td>'
                    	+'<td><select class="stones_type form-control" name="est_stones_item[stones_type][]">'+stones_type+'</select></td>'
    					+'<td><select class="stone_id form-control" name="est_stones_item[stone_id][]"></select></td>'
    					+'<td><input type="number" class="stone_pcs form-control" name="est_stones_item[stone_pcs][]" value="" style="width: 100%;"/></td>'
    					+'<td><div class="input-group"><input class="stone_wt form-control" type="number" name="est_stones_item[stone_wt][]" value="" style="width:100%;"/><span class="input-group-btn" style="width: 70px;"><select class="stone_uom_id form-control" name="est_stones_item[uom_id][]">'+uom_list+'</select></span></div></td>'
    					+'<td><div class="form-group"><input class="stone_cal_type" type="radio" name="est_stones_item[cal_type]['+rowId+']" value="1" checked="true"> By Wt&nbsp;<input type="radio" name="est_stones_item[cal_type]['+rowId+']" class="stone_cal_type" value="2">By Pcs</div></td>'
    					+'<td><input type="number" class="stone_rate form-control" name="est_stones_item[stone_rate][]" value=""  style="width:80%;"/></td>'
    					+'<td><input type="number" class="stone_price form-control" name="est_stones_item[stone_price][]" value=""  style="width:100%;" /></td>'
    					+'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td></tr>';
		}
	$('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').append(row);
	$('#cus_stoneModal').modal('show');
    
}

function create_new_empty_other_metal_row(curRow)
{
    
    $('#other_metal_table tbody').empty();
	if(curRow!=undefined)
	{
		$('#custom_active_id').val(curRow.closest('tr').attr('id'));
	}
	var catRow=$('#custom_active_id').val();
	var other_metal_details=$('#'+catRow).find('.other_metal_details').val();
	if(other_metal_details !='' && other_metal_details != '[]' && other_metal_details != undefined)
	{
	    var trHtml='';
	    var metal_details   = JSON.parse(other_metal_details);
		$.each(metal_details, function (pkey, pitem) {
		    var metal='<option value="">Select Metal</option>';
            var purity='<option value="">Select Purity</option>';
            $.each(category_lists, function (mkey, mitem) {
                var selected = "";
    				if(mitem.id_ret_category == pitem.id_metal)
    				{
    					selected = "selected='selected'";
    				}
            		metal += "<option value='"+mitem.id_ret_category+"' "+selected+">"+mitem.name+"</option>";
        	});
        	
        	$.each(purityDetails, function (k, p) {
        	    var selected = "";
				if(p.id_purity == pitem.id_purity)
				{
					selected = "selected='selected'";
				}
        		purity += "<option value='"+p.id_purity+"' "+selected+" >"+p.purity+"</option>";
        	});
        
            trHtml+='<tr>'
                  +'<td><select class="form-control select_metal">'+metal+'</td>'
                  +'<td><select class="form-control select_purity">'+purity+'</td>'
                  +'<td><input type="number" class="form-control pcs" value="'+pitem.pcs+'"></td>'
                  +'<td><input type="number" class="form-control gwt" value="'+pitem.gwt+'"></td>'
                  +'<td  style="display:none;"><input type="number" class="form-control wastage_perc" value="'+pitem.wastage_perc+'" ><input type="hidden" class="wast_wt"></td>'
                  +'<td style="display:none;"><select class="form-control calc_type"><option value="">Mc Type</option><option value="1" selected>Per Gram</option><option value="2">Per Piece</option></select></td>'
                  +'<td style="display:none;"><input type="number" class="form-control making_charge" value="'+pitem.mc_value+'"><input type="hidden" class="mc_value" value="'+pitem.mc_value+'" ></td>'
                  +'<td><input type="number" class="form-control rate_per_gram" value="'+pitem.rate_per_gram+'" ></td>'
                  +'<td><input type="number" class="form-control amount" value="'+pitem.amount+'" ></td>'
                   +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                  +'</tr>';
		});
		$('#other_metal_table tbody').append(trHtml);
	}
	else
	{
	    var trHtml='';
        var metal='<option value="">Select Metal</option>';
        var purity='<option value="">Select Purity</option>';
        $.each(category_lists, function (mkey, mitem) {
    	    metal += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
    	});
    	
    	$.each(purityDetails, function (k, p) {
    		purity += "<option value='"+p.id_purity+"'>"+p.purity+"</option>";
    	});
    
        trHtml+='<tr>'
              +'<td><select class="form-control select_metal">'+metal+'</td>'
              +'<td><select class="form-control select_purity">'+purity+'</td>'
              +'<td><input type="number" class="form-control pcs"></td>'
              +'<td><input type="number" class="form-control gwt"></td>'
              +'<td style="display:none;"><input type="number" class="form-control wastage_perc" value="0"><input type="hidden" class="wast_wt" value="0"></td>'
              +'<td style="display:none;"><select class="form-control calc_type"><option value="">Mc Type</option><option value="1" selected>Per Gram</option><option value="2">Per Piece</option></select></td>'
              +'<td style="display:none;"><input type="number" class="form-control making_charge" value="0"><input type="hidden" class="mc_value" value="0"></td>'
              +'<td><input type="number" class="form-control rate_per_gram"></td>'
              +'<td><input type="number" class="form-control amount" ></td>'
               +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
              +'</tr>';
         $('#other_metal_table tbody').append(trHtml);
	}
	$('#other_metalmodal').modal('show');
    
}

function get_taxgroup_items(){
	my_Date = new Date();
	$.ajax({
		url: base_url+'index.php/admin_ret_billing/getAllTaxgroupItems/?nocache=' + my_Date.getUTCSeconds(),   
		dataType: "json", 
		method: "GET", 
		success: function ( data ) { 
			tax_details = data;
			console.log("tax_details", tax_details);
		}
	 });
}

$(document).on('keyup', '.purreturnrate, .purreturnpcs, .purreturnweight', function(event) {
    var row     = $(this).parent().closest('tr');
    //calculate_returnItem_details(row);
    /*if($(this).attr('class') == 'purreturnrate'){
        curRow.find('.itemcaltype').val(1);
    }*/
    calculate_purchase_return_final_cost();
});

$(document).on('keyup','.purreturnrate',function(){
    curRow.find('.itemcaltype').val(1);
});

$(document).on('keyup','.returnitemcost',function(){
    curRow = $(this).parent().closest('tr');
    curRow.find('.itemcaltype').val(2);
    calculate_returnItem_details(curRow);
});



$(document).on('change', '.purreturncaltype, .returnitemcost,.return_discount', function(event) {
    var row     = $(this).parent().closest('tr');
    //calculate_returnItem_details(row);
    calculate_purchase_return_final_cost();
});

$('#set_non_tag_stock_list').on('click',function(){
    if($('#select_product').val() == null || $('#select_product').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Product"});
	}
	else if($('#select_design').val() == null || $('#select_design').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Design"});
	}
	else if($('#select_sub_design').val() == null || $('#select_sub_design').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Sub Design"});
	}
	else
	{
	    set_non_tag_item_list();
	}
});

function set_non_tag_item_list()
{
    updatereturncategory();
}



function calculate_returnItem_details(row){
    
    var retWeight   = $(row).find('.net_wt').val();
    var retRate     = 0;
    //var retRate     = isNaN(parseFloat($(row).find('.purreturnrate').val())) ? 0 : parseFloat($(row).find('.purreturnrate').val());
    var calType     = $(row).find('.purreturncaltype').val();
    var pcs         = isNaN(parseInt($(row).find('.purreturnpcs').val())) ? 0 : $(row).find('.purreturnpcs').val();
    var stoneAmount = isNaN(parseFloat($(row).find('.ret_add_stone_wt').val())) ? 0 : parseFloat($(row).find('.ret_add_stone_wt').val());
    var otherMetalAmount = isNaN(parseFloat($(row).find('.ret_add_other_metal_wt').val())) ? 0 : parseFloat($(row).find('.ret_add_other_metal_wt').val());
    var retTotal    = 0;
    var retItemRowCost = 0;
    if(calType == 1){
        var retcost = isNaN(parseFloat($(row).find('.returnitemcost').val())) ? 0 : parseFloat($(row).find('.returnitemcost').val());
        retRate     = parseFloat(parseFloat(retcost) / parseFloat(retWeight)).toFixed(2);
        retTotal    = retcost;
    }else{
        var retcost = isNaN(parseFloat($(row).find('.returnitemcost').val())) ? 0 : parseFloat($(row).find('.returnitemcost').val());
        retRate     = parseFloat(parseFloat(retcost) / parseFloat(pcs)).toFixed(2);
        retTotal    = retcost;
    }
    $(row).find('.purreturnrate').val(retRate);
    retItemRowCost = parseFloat(retTotal) + parseFloat(stoneAmount) + parseFloat(otherMetalAmount);
    //$(row).find('.returnitemcost').val(isNaN(retItemRowCost) ? 0 : retItemRowCost.toFixed(2));
    
     var tax_details     = calculate_base_value_tax(parseFloat(retItemRowCost), parseInt($(row).find('.return_item_tax_id').val()));
     var item_tax_amt    = tax_details['totaltax'];
     var tax_percentage  = tax_details['tax_percentage'];
    $(row).find('.purreturnamount').val(parseFloat(parseFloat(retItemRowCost) + parseFloat(item_tax_amt)).toFixed(2));
    
    
    //calculate_purchase_return_final_cost();
}

$(document).on('keyup', '.return_total_cost,.return_total_cost', function (){
    calculate_purchase_return_final_cost();
});

function calculate_purchase_return_final_cost(){
     var total_item_cost    = 0;
     var total_bill_amount  = 0;
     var other_charges_amount = 0;
     var total_taxable_amt = 0;
     
    var cmp_country         = $('#cmp_country').val();
    var cmp_state           = $('#cmp_state').val();
    
   
     $('#return_item_detail > tbody tr').each(function(idx, row){
         curRow = $(this);
         var supplier_country    = '';
         var supplier_state      = '';
         var item_igst           = 0;
         var item_sgst           = 0;
         var item_cgst           = 0;
         
        var retWeight   = curRow.find('.purreturnweight').val();
        var pcs         = isNaN(parseInt(curRow.find('.purreturnpcs').val())) ? 0 : curRow.find('.purreturnpcs').val();
        var stoneAmount = isNaN(parseFloat(curRow.find('.ret_add_stone_wt').val())) ? 0 : parseFloat(curRow.find('.ret_add_stone_wt').val());
        var otherMetalAmount = isNaN(parseFloat(curRow.find('.ret_add_other_metal_wt').val())) ? 0 : parseFloat(curRow.find('.ret_add_other_metal_wt').val());
        
        var retRate     = 0;
        var itemCost    = 0;
        var retItemRowCost = 0;
        var calType     = curRow.find('.purreturncaltype').val();
        if(curRow.find('.itemcaltype').val() == 1){
            retRate     = isNaN(parseFloat(curRow.find('.purreturnrate').val())) ? 0 : parseFloat(curRow.find('.purreturnrate').val());
            if(calType == 1){
                retTotal    = parseFloat(retWeight) * parseFloat(retRate);
            }else{
                retTotal    = parseFloat(pcs) * parseFloat(retRate);
            }
        }else{
            itemCost    = isNaN(parseFloat(curRow.find('.returnitemcost').val())) ? 0 : parseFloat(curRow.find('.returnitemcost').val());
            if(calType == 1){
                retRate     = parseFloat(parseFloat(parseFloat(itemCost)/parseFloat(retWeight))).toFixed(2)
                curRow.find('.purreturnrate').val(retRate);
            }else{
                retRate     = parseFloat(parseFloat(parseFloat(itemCost)/parseFloat(pcs))).toFixed(2)
                curRow.find('.purreturnrate').val(retRate);
            }
            retTotal    = itemCost;
        }
        retItemRowCost = parseFloat(retTotal) + parseFloat(stoneAmount) + parseFloat(otherMetalAmount);
        
        
    
         var tax_details     = calculate_base_value_tax(parseFloat(retItemRowCost), parseInt(curRow.find('.return_item_tax_id').val()));
         var item_tax_amt    = tax_details['totaltax'];
         var tax_percentage  = tax_details['tax_percentage'];
         
          $.each(karigar_details,function(k,val){
                if(val.id_karigar == curRow.find('.return_item_kar_id').val())
                {
                    supplier_country=val.id_country;
                    supplier_state=val.id_state;
                    
                }
            });
        $('#supplier_country').val(supplier_country);
        $('#supplier_state').val(supplier_state);
        if(cmp_country=='' || cmp_state=='')
		{
		    item_sgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
		    item_cgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
		}
		else
		{
		    if(item_tax_amt > 0)
		    {
		        if(cmp_country==supplier_country)
    		    {
    		        if(cmp_state==supplier_state)
    		        {
    		            item_sgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		            item_cgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		        }else
    		        {
    		            item_igst = item_tax_amt;
    		        }
    		    }else
    		    {
    		        item_sgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		        item_cgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		    }
		    }
		    
		}
         curRow.find('.return_item_tax_value').val(item_tax_amt);
         curRow.find('.return_item_tax_igst_value').val(item_igst);
         curRow.find('.return_item_tax_sgst_value').val(item_sgst);
         curRow.find('.return_item_tax_cgst_value').val(item_cgst);
         curRow.find('.return_item_tax_percent').val(tax_percentage);
         total_taxable_amt  += (parseFloat(retItemRowCost));
         total_item_cost  += (parseFloat(retItemRowCost) + parseFloat(item_tax_amt));
         
         curRow.find('.returnitemcost').val(parseFloat(retTotal).toFixed(2));
         curRow.find('.purreturnamount').val(parseFloat(parseFloat(retItemRowCost) + parseFloat(item_tax_amt)).toFixed(2));
        
     });
     other_charges_amount       = (isNaN($('#other_charges_amount').val()) || $('#other_charges_amount').val()=='' ? 0:$('#other_charges_amount').val());
     total_bill_amount          = parseFloat(parseFloat(total_item_cost) + parseFloat(other_charges_amount)).toFixed(2);
     round_of_val               = total_bill_amount;
     tot_cost 			        = parseFloat(Math.round(total_bill_amount));
	 round_of_amt               = parseFloat(tot_cost-round_of_val).toFixed(2);
	 console.log(round_of_amt);
	 $('.return_round_off').val(round_of_amt<0.50 ? round_of_amt : round_of_amt);
     $('.return_total_cost').val(parseFloat(tot_cost).toFixed(2));
     $('.total_summary_payable_amt').val(parseFloat(total_taxable_amt).toFixed(2));
     
     calculate_purchase_return_summary_cost();
 
}


function calculate_purchase_return_summary_cost()
{
     var total_item_cost = 0;
     var total_bill_amount = 0;
     var total_taxable_amount = 0;
     var total_cgst_amount = 0;
     var total_sgst_amount = 0;
     var total_igst_amount = 0;
     
    var tcsval = 0;
    var tdsval = 0;
    var other_charges_tdsval = 0;
    
     $.each(karigar_details,function(k,val){
        if(val.id_karigar == $('#select_karigar').val())
        {
            supplier_country=val.id_country;
            supplier_state=val.id_state;
            $('#tcs_percent').val(val.tcs_tax);
            $('#tds_percent').val(val.tds_tax);
        }
    });
    
    var tcspercent =(isNaN($('#tcs_percent').val()) || $('#tcs_percent').val()=='' ? 0:$('#tcs_percent').val()); 
    var tdspercent =(isNaN($('#tds_percent').val()) || $('#tds_percent').val()=='' ? 0:$('#tds_percent').val());
    var charges_tds_percent =(isNaN($('#charges_tds_percent').val()) || $('#charges_tds_percent').val()=='' ? 0:$('#charges_tds_percent').val());
    
    var other_charges_details = $('#other_charges_details').val();
    var charges_details       = [];
    if(other_charges_details!='')
    {
        var charges_details = JSON.parse($('#other_charges_details').val());
    }
    
    var total_charges_taxable_amount    = 0;
    var total_charges_tax_amount        = 0;
    var total_charges_amount            = 0;
    if(charges_details.length > 0)
    {
        $.each(charges_details,function(k,val){
           total_charges_amount+=parseFloat(val.char_with_tax); 
           total_charges_taxable_amount+=parseFloat(val.charge_value); 
           total_charges_tax_amount+=parseFloat(val.charge_tax_value); 
        });
    }
    
     $('#return_item_detail > tbody tr').each(function(idx, row){
         curRow = $(this);
         total_item_cost    += parseFloat(curRow.find('.purreturnamount').val());
         total_cgst_amount  += parseFloat(curRow.find('.return_item_tax_cgst_value').val());
         total_sgst_amount  += parseFloat(curRow.find('.return_item_tax_sgst_value').val());
         total_igst_amount  += parseFloat(curRow.find('.return_item_tax_igst_value').val());
         total_taxable_amount  += parseFloat((curRow.find('.purreturnamount').val())-(curRow.find('.return_item_tax_value').val()));
     });
     
     
     var tot_discount           = (isNaN($('.grn_discount').val()) || $('.grn_discount').val()=='' ? 0:$('.grn_discount').val());
     var other_charges_amount   = (isNaN($('#other_charges_amount').val()) || $('#other_charges_amount').val()=='' ? 0:$('#other_charges_amount').val());

     $('.total_summary_taxable_amt').val(parseFloat(total_taxable_amount).toFixed(2));
     $('.total_summary_cgst_amount').val(parseFloat(total_cgst_amount).toFixed(2));
     $('.total_summary_sgst_amount').val(parseFloat(total_sgst_amount).toFixed(2));
     $('.total_summary_igst_amount').val(parseFloat(total_igst_amount).toFixed(2));
     
     $('#other_charges_taxable_amount').val(parseFloat(total_charges_taxable_amount).toFixed(2));
     $('#other_charges_tax').val(parseFloat(total_charges_tax_amount).toFixed(2));
     
     
     if(total_charges_tax_amount > 0)
    {
        var cmp_country         = $('#cmp_country').val();
        var cmp_state           = $('#cmp_state').val();
        var supplier_country    = $('#supplier_country').val();
        var supplier_state    = $('#supplier_state').val();

        var other_charges_igst = 0;
        var other_charges_sgst = 0;
        var other_charges_cgst = 0;
        if(cmp_country==supplier_country)
	    {
	        if(cmp_state==supplier_state)
	        {
	            other_charges_cgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	            other_charges_sgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	        }else
	        {
	            other_charges_igst = total_charges_tax_amount;
	        }
	    }else
	    {
	        other_charges_cgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	        other_charges_sgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	    }
	    $('.other_charges_cgst').html(parseFloat(other_charges_cgst).toFixed(2));
        $('.other_charges_sgst').html(parseFloat(other_charges_sgst).toFixed(2));
        $('.other_charges_igst').html(parseFloat(other_charges_igst).toFixed(2));
    }
    
    
    if(tdspercent > 0){
        tdsval = parseFloat(parseFloat(total_taxable_amount) * (tdspercent / 100)).toFixed(2);
    }
    $("#tds_tax_value").val(tdsval);
    
    
    if(tcspercent > 0){
        tcsval = parseFloat(parseFloat(total_item_cost) * (tcspercent / 100)).toFixed(2);
    }
    $("#tcs_tax_value").val(tcsval);
    
    if(charges_tds_percent > 0)
    {
        other_charges_tdsval = parseFloat(parseFloat(total_charges_taxable_amount) * (charges_tds_percent / 100)).toFixed(2);
    }
    $("#other_charges_tds_tax_value").val(other_charges_tdsval);

     calculate_purchase_return_payment_cost();
     
}



function calculate_purchase_return_payment_cost()
{
    var total_summary_taxable_amt       = (isNaN($('.total_summary_taxable_amt').val()) || $('.total_summary_taxable_amt').val()=='' ? 0:$('.total_summary_taxable_amt').val());
    var tds_tax_value                   = (isNaN($('.tds_tax_value').val()) || $('.tds_tax_value').val()=='' ? 0:$('.tds_tax_value').val());
    var total_summary_cgst_amount       = (isNaN($('.total_summary_cgst_amount').val()) || $('.total_summary_cgst_amount').val()=='' ? 0:$('.total_summary_cgst_amount').val());
    var total_summary_sgst_amount       = (isNaN($('.total_summary_sgst_amount').val()) || $('.total_summary_sgst_amount').val()=='' ? 0:$('.total_summary_sgst_amount').val());
    var total_summary_igst_amount       = (isNaN($('.total_summary_igst_amount').val()) || $('.total_summary_igst_amount').val()=='' ? 0:$('.total_summary_igst_amount').val());
    var tcs_tax_value                   = (isNaN($('.tcs_tax_value').val()) || $('.tcs_tax_value').val()=='' ? 0:$('.tcs_tax_value').val());
    var other_charges_tds_tax_value     = (isNaN($('.other_charges_tds_tax_value').val()) || $('.other_charges_tds_tax_value').val()=='' ? 0:$('.other_charges_tds_tax_value').val());
    var other_charges_taxable_amount    = (isNaN($('#other_charges_taxable_amount').val()) || $('#other_charges_taxable_amount').val()=='' ? 0:$('#other_charges_taxable_amount').val());
    var other_charges_tax               = (isNaN($('#other_charges_tax').val()) || $('#other_charges_tax').val()=='' ? 0:$('#other_charges_tax').val());
    var grn_discount                    = (isNaN($('.return_discount').val()) || $('.return_discount').val()=='' ? 0:$('.return_discount').val());
    var final_cost = parseFloat(parseFloat(total_summary_taxable_amt)-parseFloat(tds_tax_value)+parseFloat(total_summary_cgst_amount)+parseFloat(total_summary_sgst_amount)+parseFloat(total_summary_igst_amount)+parseFloat(tcs_tax_value)+parseFloat(other_charges_taxable_amount)-parseFloat(other_charges_tds_tax_value)+parseFloat(other_charges_tax)-parseFloat(grn_discount)).toFixed(2)
    console.log('final_cost:'+final_cost);
    round_of_val               = final_cost;
    tot_cost 			        = parseFloat(Math.round(final_cost));
    
	 round_of_amt               = parseFloat(tot_cost-round_of_val).toFixed(2);
	 console.log(round_of_amt);
	 $('.return_round_off').val(round_of_amt<0.50 ? round_of_amt : round_of_amt);
     $('.return_total_cost').val(parseFloat(tot_cost).toFixed(2));
    
}

function calculate_base_value_tax(taxcallrate, taxgroup){
	var totaltax = 0;
	console.log(tax_details);
	var return_details=[];
	var tax_percentage = 0;
	$.each(tax_details, function(idx, taxitem){
		if(taxitem.tgi_tgrpcode == taxgroup){
			if(taxitem.tgi_calculation == 1){
			    tax_percentage = taxitem.tax_percentage;
				if(taxitem.tgi_type == 1){
					totaltax += parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);
				}else{
					totaltax -= parseFloat(taxcallrate)	* (parseFloat(taxitem.tax_percentage)/100);
				}
			}
		}
	});
	return_details['totaltax'] = parseFloat(totaltax).toFixed(2);
	return_details['tax_percentage'] = tax_percentage;
	return return_details;
}
function get_non_tag_category(){
    $("div.overlay").css("display", "block"); 
    $('#select_product option').remove();
    $.ajax({
	type: 'POST',
	url: base_url+'index.php/admin_ret_catalog/get_NonTagProducts',
	dataType:'json',
	success:function(data){
	    non_tag_category = data;
	            
        $.each(data, function (key, item) { 
            $('#select_product').append(
            $("<option></option>")
            .attr("value", item.pro_id)    
            .text(item.product_name)  
            );
    	});

        	$('#select_product').select2({
        	    placeholder: "Select Product",
        	    allowClear: true
        	});
        	
        	
	        if($('#select_product').length)
	        {
	            $('#select_product').select2("val",'');
	        }
	        $("div.overlay").css("display", "none"); 
		}
	});
}




$('#other_metalmodal  #update_return_other_metal_details').on('click', function(){
	if(validate_other_metal_row())
    {
    	var metal_details=[];
    	var tot_amount=0;
    	var tot_weight=0;
    	var tot_wast_wt=0;
    	var tot_mc_value=0;
    	var catRow              =$('#custom_active_id').val();
    	var gross_wt            =($('#'+catRow).find('.gross_wt').val()!='' ? $('#'+catRow).find('.gross_wt').val():0);
    	var less_wt             =($('#'+catRow).find('.add_less_wt').val()!='' ?$('#'+catRow).find('.add_less_wt').val() :0);
    	$('#other_metalmodal .modal-body #other_metal_table> tbody  > tr').each(function(index, tr) {
    		tot_amount+=parseFloat($(this).find('.amount').val());
    		tot_weight+=parseFloat($(this).find('.gwt').val());
    		tot_wast_wt+=parseFloat($(this).find('.wast_wt').val());
    		tot_mc_value+=parseFloat($(this).find('.mc_value').val());
    		metal_details.push({
    		            'id_metal'      : $(this).find('.select_metal').val(),
    		            'id_purity'     : $(this).find('.select_purity').val(),
    		            'pcs'           : $(this).find('.pcs').val(),
    		            'gwt'           : $(this).find('.gwt').val(),
    		            'wastage_perc'  : $(this).find('.wastage_perc').val(),
    		            'calc_type'     : $(this).find('.calc_type').val(),
    		            'making_charge' : $(this).find('.making_charge').val(),
    		            'rate_per_gram' : $(this).find('.rate_per_gram').val(),
    		            'amount'        : $(this).find('.amount').val(),
    		            'wast_wt'       : $(this).find('.wast_wt').val(),
    		            'mc_value'      : $(this).find('.mc_value').val(),
    		            });
    	});
    	if(parseFloat(parseFloat(gross_wt)+parseFloat(less_wt)) < parseFloat(tot_weight))
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Entered Weight is Grater Than The Gross Wt."});
    	}else
    	{
    	    $('#'+catRow).find('.ret_add_other_metal_wt').val(parseFloat(tot_amount).toFixed(2));
    	    $('#'+catRow).find('.add_other_metal_wt').val(parseFloat(tot_weight).toFixed(3));
    	    $('#'+catRow).find('.other_metal_details').val(JSON.stringify(metal_details));
            $('#other_metalmodal').modal('hide');
            
           if(ctrl_page[1] == 'purchasereturn' && ctrl_page[2] == 'add')
           {
               //calculate_returnItem_details($('#'+catRow));
               calculate_purchase_return_final_cost();
           }
            
    	}
    }
    else
    {
        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>Please Fill The Required Details"});
    }
});
//Purchase reutn end here

//GRN Entry

$("input[name='order[grn_type]']:radio").on('change',function(){
    $('.item_details').css("display","block");
    $('#grn_item_details >tbody').empty();
    var grntype = this.value;
    if(this.value == 3)
    {
        $('.item_details').css("display","none");
        
    }else{
        $('#grn_item_details > tbody tr').each(function(idx, row){
            curRow = $(this);
            if(grntype == 1){
                curRow.find('.wastage').val(0);
                curRow.find('.wastage').prop("readonly",true);
            }else{
                curRow.find('.wastage').prop("readonly",false);
            }
        });
    }
});
function get_cat_details()
{
  
    $.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_catalog/category/active_category',
	dataType:'json',
	success:function(data){
	        categoryDet=data;
	        console.log(categoryDet);
		}
	});
}
$('#add_item_details').on('click',function(){
        if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please select the karigar.."});
        }
        else if($('.referenceno').val()=='' || $('.referenceno').val()==null)
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please enter the reference no.."});
        }
        else if($('.referencedate').val()=='' || $('.referencedate').val()==null)
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select the Reference Date.."});
        }
    	else 
    	{
    	    if(validateGrnItemDetailRow())
    	    {
    	        create_new_empty_grn_entry_row();
                showgrnentrypreview();
    	    }
    	    else
        	{
        	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Fill The Required Fields.."});
        	}
    	}
});
function validateGrnItemDetailRow()
{
    var validate = true;
	$('#grn_item_details > tbody  > tr').each(function(index, tr) {
		if($(this).find('.category_select').val() == "" || $(this).find('.pcs').val() == ""  || $(this).find('.gross_wt').val() == ""  || $(this).find('.net_wt').val() == "" || $(this).find('.rate_per_gram').val() == "" || $(this).find('.rate_per_gram').val() == 0 || $(this).find('.item_cost').val() == "" || $(this).find('.item_cost').val() == 0 ){
			validate = false;
		}
	});
	return validate;
}
function showgrnentrypreview()
{
    var html = "";
    var length  =$('#grn_item_details tbody tr').length;
    if(length > 0){
        $('#item_details_preview tbody').empty();
        $('#grn_item_details > tbody tr').each(function(idx, row){
             curRow = $(this);
             if(curRow.find('.category_select').val() != ""){
                var ratepergrm = isNaN(parseFloat(curRow.find('.rate_per_gram').val())) ? 0 : parseFloat(curRow.find('.rate_per_gram').val());
                var itemcost    = isNaN(parseFloat(curRow.find('.itemcost').val())) ? 0 : parseFloat(curRow.find('.itemcost').val());
                html+='<tr><td>'+curRow.find(".category_select option:selected" ).text()+'</td><td>'+parseInt(curRow.find('.pcs').val())+'</td><td>'+parseFloat(curRow.find('.net_wt').val())+'</td><td>'+ratepergrm+'</td><td>'+itemcost+'</td></tr>';
                if(curRow.find('.stone_details').val() != ""){
                    var st_details = JSON.parse(curRow.find('.stone_details').val());
                    if(st_details.length>0)
                    {
                         $.each(st_details, function (pkey, pitem) {
                             var stonename = "";
                             $.each(stones, function(skey, sval){
                                if(pitem.stone_id == sval.stone_id){
                                    stonename = sval.stone_name;
                                }
                             });
                             html+='<tr><td>'+stonename+'</td><td>'+parseInt(pitem.stone_pcs)+'</td><td>'+parseFloat(pitem.stone_wt)+'</td><td>'+parseFloat(pitem.stone_rate)+'</td><td>'+parseFloat(pitem.stone_price)+'</td></tr>';
                         });
                    }
                }
                if(curRow.find('.other_metal_details').val() != ""){
                    var om_details = JSON.parse(curRow.find('.other_metal_details').val());
                    if(om_details.length>0)
                    {
                         $.each(om_details, function (pkey, pitem) {
                             var catname = "";
                             $.each(category_lists, function(skey, sval){
                                if(pitem.id_metal == sval.id_ret_category){
                                    catname = sval.name;
                                }
                             });
                             html+='<tr><td>'+catname+'</td><td>'+parseInt(pitem.pcs)+'</td><td>'+parseFloat(pitem.gwt)+'</td><td>'+parseFloat(pitem.rate_per_gram)+'</td><td>'+parseFloat(pitem.amount)+'</td></tr>';
                         });
                    }
                }
             }
        });
         $('#item_details_preview tbody').append(html);
    }
}
function create_new_empty_grn_entry_row()
{
    var curgrntype = $("input[name='order[grn_type]']:checked").val();
    if(curgrntype == 1){
        var readtype = "readonly";
    }else if(curgrntype == 2){
        var readtype = "";
    }
    var html = "";
    var length  =$('#grn_item_details tbody tr').length+1;
    var category='<option value="">Select Category</option>';
    $.each(categoryDet, function (mkey, mitem) {
		category += "<option value='"+mitem.id_ret_category+"'>"+mitem.name+"</option>";
	});
	
    html+='<tr id="'+length+'">'
            +'<td><select class="form-control category_select" name="item[category][]" value="" style="width:100px;">'+category+'</td>'
            +'<td><input type="number" class="form-control custom-inp pcs" name="item[pcs][]" style="width:100px;" step="any" ></td>'
            +'<td><input type="number" class="form-control custom-inp gross_wt" name="item[gross_wt][]" style="width:100px;" step="any" ></td>'
            +'<td><div class="form-group"><div class="input-group "><input class="form-control custom-inp add_less_wt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));"  type="number" name="item[less_wt][]" step="any" readonly style="width:100px;"/><span class="input-group-addon input-sm add_tag_lwt" onClick="create_new_empty_est_cus_stone_item($(this).closest(\'tr\'));">+</span></div></div></td>'
            +'<td><div class="form-group"><div class="input-group "><input class="form-control custom-inp add_other_metal_wt" onClick="create_new_empty_other_metal_row($(this).closest(\'tr\'));"  type="number" step="any" style="width:100px;" readonly/><span class="input-group-addon input-sm add_other_metal_wt" onClick="create_new_empty_other_metal_row($(this).closest(\'tr\'));">+</span></div></div></td>'
            +'<td><input type="number" class="form-control custom-inp net_wt" name="item[net_wt][]" style="width:100px;" step="any" readonly><input type="hidden" class="stone_details" name="item[stone_details][]"/><input type="hidden" class="other_metal_details" name="item[other_metal_details][]"/></td>'
            +'<td><input type="number" class="form-control custom-inp wastage" name="item[wastage][]" style="width:100px;" step="any" '+readtype +'></td>'
            +'<td class="input-group"><input type="number" class="form-control custom-inp rate_per_gram" name="item[rate_per_gram][]" style="width:100px;" step="any"><span class="input-group-btn" style="width: 70px;"><select class="ratecaltype form-control" name="item[rate_type][]" style="width:100px;"><option value="1" selected="selected">Grm</option><option value="2">Pcs</option></select></span></td>'
            +'<td><input type="number" class="form-control custom-inp itemcost" style="width:100px;" step="any" ><input type="hidden" class="form-control custom-inp itemcaltype" style="width:100px;" step="any" value= 1 ></td>'
            +'<td><span class="taxable_amt"></span></td>'
            +'<td><input type="hidden" name="item[item_total_tax][]" class="item_total_tax"><input type="hidden" name="item[item_cgst][]" class="item_cgst"><input type="hidden" name="item[item_sgst][]" class="item_sgst"><input type="hidden" name="item[item_igst][]" class="item_igst"><input type="hidden" name="item[tax_percentage][]" class="tax_percentage"><span class="item_tax_amt"></span></td>'
            +'<td><input type="number" class="form-control custom-inp item_cost" name="item[item_cost][]" style="width:100px;" step="any" readonly ></td>'
            +'<td><a href="#" onClick="remove_grn_item_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
           +'</tr>';
   $('#grn_item_details tbody').append(html);
   $('#grn_item_details > tbody').find('.category_select').select2();

}
$(document).on('keyup','.pcs,.gross_wt,.rate_per_gram,.item_cost,.wastage',function(){
    calculate_grnItem_details();
});

$(document).on('keyup','.rate_per_gram',function(){
    curRow = $(this).parent().closest('tr');
    curRow.find('.itemcaltype').val(1);
});
$(document).on('keyup','.itemcost',function(){
    curRow = $(this).parent().closest('tr');
    curRow.find('.itemcaltype').val(2);
    calculate_grnItemcost_details(curRow);
});

$(document).on('keyup', '.stone_price', function() {
    curRow = $(this).parent().closest('tr'); 
    var gross_wt            = (curRow.find('.stone_wt').val()=='' ? 0 :curRow.find('.stone_wt').val());
    var item_pcs            = (curRow.find('.stone_pcs').val() == '' ? 0 : curRow.find('.stone_pcs').val());
    var ratecaltype         = (curRow.find('input[type=radio]:checked').val() == '' ? 1 : curRow.find('input[type=radio]:checked').val());
    var itemcost            = isNaN(curRow.find('.stone_price').val()) ? 0 : curRow.find('.stone_price').val();
    
    if(itemcost > 0){
        if(ratecaltype == 1){ // cal based on net wt
            curRow.find('.stone_rate').val(parseFloat(parseFloat(parseFloat(itemcost)/parseFloat(gross_wt))).toFixed(2));
        }else{ // cal based on pcs
            curRow.find('.stone_rate').val(parseFloat(parseFloat(itemcost) / parseFloat(item_pcs)).toFixed(2));
          
        }
    }
});

$(document).on('keyup', '.amount', function() {
    curRow = $(this).parent().closest('tr'); 
    var gross_wt            = (curRow.find('.gwt').val()=='' ? 0 :curRow.find('.gwt').val());
    var item_pcs            = (curRow.find('.pcs').val() == '' ? 0 : curRow.find('.pcs').val());
    var ratecaltype         = 1; //(curRow.find('input[type=radio]:checked').val() == '' ? 1 : curRow.find('input[type=radio]:checked').val());
    var itemcost            = isNaN(curRow.find('.amount').val()) ? 0 : curRow.find('.amount').val();
    
    if(itemcost > 0){
        if(ratecaltype == 1){ // cal based on net wt
            curRow.find('.rate_per_gram').val(parseFloat(parseFloat(parseFloat(itemcost)/parseFloat(gross_wt))).toFixed(2));
        }else{ // cal based on pcs
            curRow.find('.rate_per_gram').val(parseFloat(parseFloat(itemcost) / parseFloat(item_pcs)).toFixed(2));
          
        }
    }
});

$(document).on('change', '.ratecaltype, .itemcost', function(event) {
    calculate_grnItem_details();
});

$(document).on('keydown','.itemcost',function(e){  
   if (e.which == 9)
      calculate_grnItem_details();
});

function calculate_grnItemcost_details(curRow)
{
    var tot_stone_wt        = 0;
    var gross_wt            = (curRow.find('.gross_wt').val()=='' ? 0 :curRow.find('.gross_wt').val());
    var stone_details       = curRow.find('.stone_details').val();
    var other_metal_details = curRow.find('.other_metal_details').val();
    var item_pcs            = (curRow.find('.pcs').val() == '' ? 0 : curRow.find('.pcs').val());
    var ratecaltype        = (curRow.find('.ratecaltype').val() == '' ? 1 : curRow.find('.ratecaltype').val());
    var itemcost            = isNaN(curRow.find('.itemcost').val()) ? 0 : curRow.find('.itemcost').val();
    var stone_price         = 0;
    var other_metal_price   = 0;
    var other_metal_weight  = 0;
   
    wastage             = isNaN(curRow.find('.wastage').val())  ? 0 : curRow.find('.wastage').val();
    
     if(stone_details!='')
        {
            var st_details = JSON.parse(stone_details);
            if(st_details.length>0)
            {
                 $.each(st_details, function (pkey, pitem) {
                     $.each(uom_details,function(key,item){
                         if(item.uom_id==pitem.stone_uom_id)
                         {
                             if(pitem.show_in_lwt==1)
                             {
                                 if((item.uom_short_code=='CT') && (item.divided_by_value!=null && item.divided_by_value!='')) //For Carat Need to convert into gram
                                 {
                                     stone_wt=parseFloat(parseFloat(pitem.stone_wt)/parseFloat(item.divided_by_value));
                                 }else{
                                     stone_wt=pitem.stone_wt;
                                 }
                                 tot_stone_wt+=parseFloat(stone_wt);
                             }
                             
                             stone_price+=parseFloat(pitem.stone_price);
                         }
                     });
                     
                    
                 });
            }
        }
        
        if(other_metal_details!='')
        {
            var other_met_details = JSON.parse(other_metal_details);
            if(other_met_details.length > 0)
            {
                $.each(other_met_details, function (pkey, pitem) {
                    other_metal_price+=parseFloat(pitem.amount);
                    other_metal_weight+=parseFloat(pitem.gwt);
                });
            }
        }
        var less_wt         =  tot_stone_wt;
        net_wt              =  parseFloat(parseFloat(gross_wt) - parseFloat(less_wt)- parseFloat(other_metal_weight)).toFixed(3);
        var wastage             = isNaN(parseFloat(curRow.find('.wastage').val()))  ? 0 : parseFloat(curRow.find('.wastage').val()).toFixed(3);
        if(ratecaltype == 1){ // cal based on net wt
            //curRow.find('.rate_per_gram').val(parseInt(parseFloat(parseFloat(itemcost)/parseFloat(net_wt)) * 100) / 100);
            curRow.find('.rate_per_gram').val(parseFloat(parseFloat(parseFloat(itemcost)/(parseFloat(net_wt) + parseFloat(wastage)))).toFixed(2));
            
        }else{ // cal based on pcs
            //curRow.find('.rate_per_gram').val(parseInt(parseFloat(itemcost) / parseFloat(item_pcs) * 100) / 100 );
            curRow.find('.rate_per_gram').val(parseFloat(parseFloat(itemcost) / parseFloat(item_pcs)).toFixed(2));
        }
    //calculate_grnItem_details();
}
function calculate_grnItem_details()
{
    var grn_type            = $("input[name='order[grn_type]']:checked").val();
    var cmp_country         = $('#cmp_country').val();
    var cmp_state           = $('#cmp_state').val();
    var supplier_country    = '';
    var supplier_state      = '';
    var tcspercent          = 0;
    var tdspercent          = 0;
    console.log(karigar_details);
    $.each(karigar_details,function(k,val){
        if(val.id_karigar == $('#select_karigar').val())
        {
            supplier_country=val.id_country;
            supplier_state=val.id_state;
            $('#tcs_percent').val(val.tcs_tax);
            $('#tds_percent').val(val.tds_tax);
        }
    });
    $('#supplier_country').val(supplier_country);
    $('#supplier_state').val(supplier_state);
    $('#grn_item_details > tbody tr').each(function(idx, row){
        curRow = $(this);
        var tot_stone_wt        = 0;
        var gross_wt            = (curRow.find('.gross_wt').val()=='' ? 0 :curRow.find('.gross_wt').val());
        var stone_details       = curRow.find('.stone_details').val();
        var other_metal_details = curRow.find('.other_metal_details').val();
        var net_wt              = 0;
        var taxable_amt         = 0;
        var item_cost           = 0;
        var stone_price         = 0;
        var other_metal_price   = 0;
        var other_metal_weight  = 0;
        var item_tax_amt        = 0;
        var tax_percentage      = 0;
        var wastage             = 0;
        
        var rate_per_gram       = (curRow.find('.rate_per_gram').val()=='' ? 0 :curRow.find('.rate_per_gram').val());
        var ratecaltype         = (curRow.find('.ratecaltype').val() == '' ? 1 : curRow.find('.ratecaltype').val());
        var item_pcs            = (curRow.find('.pcs').val() == '' ? 0 : curRow.find('.pcs').val());
        var tax_group_id        = '';
        var item_igst           = 0;
        var item_sgst           = 0;
        var item_cgst           = 0;
        if(stone_details!='')
        {
            var st_details = JSON.parse(stone_details);
            if(st_details.length>0)
            {
                 $.each(st_details, function (pkey, pitem) {
                     $.each(uom_details,function(key,item){
                         if(item.uom_id==pitem.stone_uom_id)
                         {
                             if(pitem.show_in_lwt==1)
                             {
                                 if((item.uom_short_code=='CT') && (item.divided_by_value!=null && item.divided_by_value!='')) //For Carat Need to convert into gram
                                 {
                                     stone_wt=parseFloat(parseFloat(pitem.stone_wt)/parseFloat(item.divided_by_value));
                                 }else{
                                     stone_wt=pitem.stone_wt;
                                 }
                                 tot_stone_wt+=parseFloat(stone_wt);
                             }
                             
                             stone_price+=parseFloat(pitem.stone_price);
                         }
                     });
                     
                    
                 });
            }
        }
        
        if(other_metal_details!='')
        {
            var other_met_details = JSON.parse(other_metal_details);
            if(other_met_details.length > 0)
            {
                $.each(other_met_details, function (pkey, pitem) {
                    other_metal_price+=parseFloat(pitem.amount);
                    other_metal_weight+=parseFloat(pitem.gwt);
                });
            }
        }
        
        $.each(categoryDet, function (mkey, mitem) {
            if(mitem.id_ret_category==curRow.find('.category_select').val())
            {
                tax_group_id=mitem.tgrp_id;
            }
        });
        
        var less_wt         =  tot_stone_wt;
        wastage             = isNaN(parseFloat(curRow.find('.wastage').val()))  ? 0 : parseFloat(curRow.find('.wastage').val()).toFixed(3);
        net_wt              =  parseFloat(parseFloat(gross_wt) - parseFloat(less_wt)- parseFloat(other_metal_weight)).toFixed(3);
        if(ratecaltype == 1){
            if(curRow.find('.itemcaltype').val() == 1){
                var calnet_wt         = parseFloat(net_wt) + parseFloat(wastage);
                curRow.find('.itemcost').val(parseFloat(parseFloat((parseFloat(calnet_wt))*parseFloat(rate_per_gram))).toFixed(2));
                taxable_amt           =  parseFloat(parseFloat(parseFloat(calnet_wt)*parseFloat(rate_per_gram))+parseFloat(stone_price)+parseFloat(other_metal_price)).toFixed(2);
            }else{
                taxable_amt           =  parseFloat(parseFloat(curRow.find('.itemcost').val())+parseFloat(stone_price)+parseFloat(other_metal_price)).toFixed(2);
            }
            
        }else{
            if(curRow.find('.itemcaltype').val() == 1){
                curRow.find('.itemcost').val(parseFloat(parseFloat(item_pcs)*parseFloat(rate_per_gram)).toFixed(2));
                taxable_amt           =  parseFloat(parseFloat(parseFloat(item_pcs)*parseFloat(rate_per_gram))+parseFloat(stone_price)+parseFloat(other_metal_price)).toFixed(2);  
            }else{
                taxable_amt           =  parseFloat(parseFloat(curRow.find('.itemcost').val())+parseFloat(stone_price)+parseFloat(other_metal_price)).toFixed(2);
            }
            
        }
        var tax_details     = calculate_base_value_tax(taxable_amt, tax_group_id)
        
        if(grn_type==1) // No tax Need for Receipts
        {
            item_tax_amt    =  tax_details['totaltax'];
            tax_percentage  =  tax_details['tax_percentage'];
        }
        
		var item_cost       =  parseFloat(parseFloat(taxable_amt)+parseFloat(item_tax_amt)).toFixed(2);
		
		if(cmp_country=='' || cmp_state=='')
		{
		    item_sgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
		    item_cgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
		}
		else
		{
		    if(item_tax_amt > 0)
		    {
		        if(cmp_country==supplier_country)
    		    {
    		        if(cmp_state==supplier_state)
    		        {
    		            item_sgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		            item_cgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		        }else
    		        {
    		            item_igst = item_tax_amt;
    		        }
    		    }else
    		    {
    		        item_sgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		        item_cgst = parseFloat(parseFloat(item_tax_amt)/2).toFixed(2);
    		    }
		    }
		    
		}
		
		curRow.find('.add_less_wt').val(parseFloat(tot_stone_wt).toFixed(3));
		curRow.find('.net_wt').val(parseFloat(net_wt).toFixed(3));
		curRow.find('.item_cost').val(parseFloat(item_cost).toFixed(2));
		curRow.find('.taxable_amt').html(parseFloat(taxable_amt).toFixed(2));
		curRow.find('.item_tax_amt').html(parseFloat(item_tax_amt).toFixed(2));
		curRow.find('.item_total_tax').val(parseFloat(item_tax_amt).toFixed(2));
		curRow.find('.item_cgst').val(parseFloat(item_cgst).toFixed(2));
		curRow.find('.item_sgst').val(parseFloat(item_sgst).toFixed(2));
		curRow.find('.item_igst').val(parseFloat(item_igst).toFixed(2));
		curRow.find('.tax_percentage').val(parseFloat(tax_percentage).toFixed(2));
		
        console.log('gross_wt:'+gross_wt);
        console.log('net_wt:'+net_wt);
        console.log('less_wt:'+less_wt);
        console.log('stone_price:'+stone_price);
        console.log('other_metal_price:'+other_metal_price);
    });
    
    calculate_grn_final_cost();
}

$(document).on('keyup', '.grn_discount', function (){
    calculate_grn_payment_cost();
});

$(document).on('change', '.grn_round_off', function (){
    var final_cost  = $('.grn_total_cost').val();
    var grn_round_off                   = (isNaN($('.grn_round_off').val()) || $('.grn_round_off').val()=='' ? 0:$('.grn_round_off').val());
    var round_off_symbol                = $('.round_off_symbol').val();
    
    if(grn_round_off > 0)
    {
        if(round_off_symbol==1) // Add to Final cost
        {
            final_cost = parseFloat(final_cost)+parseFloat(grn_round_off);
        }else
        {
            final_cost = parseFloat(final_cost)-parseFloat(grn_round_off);
        }
        $('.grn_total_cost').val(parseFloat(final_cost).toFixed(2));
    }else
    {
        calculate_grn_payment_cost();
    }
});


function calculate_grn_final_cost()
{
     var total_item_cost = 0;
     var total_bill_amount = 0;
     var total_taxable_amount = 0;
     var total_cgst_amount = 0;
     var total_sgst_amount = 0;
     var total_igst_amount = 0;
     
    var cmp_country         = $('#cmp_country').val();
    var cmp_state           = $('#cmp_state').val();
    var supplier_country    = $('#supplier_country').val();
    var supplier_state      = $('#supplier_state').val();
     
    var tcsval = 0;
    var tdsval = 0;
    var other_charges_tdsval = 0;
    var tcspercent =(isNaN($('#tcs_percent').val()) || $('#tcs_percent').val()=='' ? 0:$('#tcs_percent').val()); 
    var tdspercent =(isNaN($('#tds_percent').val()) || $('#tds_percent').val()=='' ? 0:$('#tds_percent').val());
    var charges_tds_percent =(isNaN($('#charges_tds_percent').val()) || $('#charges_tds_percent').val()=='' ? 0:$('#charges_tds_percent').val());
    
    var other_charges_details = $('#other_charges_details').val();
    var charges_details       = [];
    if(other_charges_details!='')
    {
        var charges_details = JSON.parse($('#other_charges_details').val());
    }
    
    var total_charges_taxable_amount    = 0;
    var total_charges_tax_amount        = 0;
    var total_charges_amount            = 0;
    if(charges_details.length > 0)
    {
        $.each(charges_details,function(k,val){
           total_charges_amount+=parseFloat(val.char_with_tax); 
           total_charges_taxable_amount+=parseFloat(val.charge_value); 
           total_charges_tax_amount+=parseFloat(val.charge_tax_value); 
        });
    }
    
     $('#grn_item_details > tbody tr').each(function(idx, row){
         curRow = $(this);
         total_item_cost    += parseFloat(curRow.find('.item_cost').val());
         total_cgst_amount  += parseFloat(curRow.find('.item_cgst').val());
         total_sgst_amount  += parseFloat(curRow.find('.item_sgst').val());
         total_igst_amount  += parseFloat(curRow.find('.item_igst').val());
         total_taxable_amount  += parseFloat((curRow.find('.item_cost').val())-(curRow.find('.item_total_tax').val()));
     });
     
     var tot_discount           = (isNaN($('.grn_discount').val()) || $('.grn_discount').val()=='' ? 0:$('.grn_discount').val());
     var other_charges_amount   = (isNaN($('#other_charges_amount').val()) || $('#other_charges_amount').val()=='' ? 0:$('#other_charges_amount').val());

     $('.total_summary_taxable_amt').val(parseFloat(total_taxable_amount).toFixed(2));
     $('.total_summary_cgst_amount').val(parseFloat(total_cgst_amount).toFixed(2));
     $('.total_summary_sgst_amount').val(parseFloat(total_sgst_amount).toFixed(2));
     $('.total_summary_igst_amount').val(parseFloat(total_igst_amount).toFixed(2));
     
     $('#other_charges_taxable_amount').val(parseFloat(total_charges_taxable_amount).toFixed(2));
     $('#other_charges_tax').val(parseFloat(total_charges_tax_amount).toFixed(2));
     
    if(total_charges_tax_amount > 0)
    {
        var other_charges_igst = 0;
        var other_charges_sgst = 0;
        var other_charges_cgst = 0;
        if(cmp_country==supplier_country)
	    {
	        if(cmp_state==supplier_state)
	        {
	            other_charges_cgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	            other_charges_sgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	        }else
	        {
	            other_charges_igst = total_charges_tax_amount;
	        }
	    }else
	    {
	        other_charges_cgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	        other_charges_sgst = parseFloat(parseFloat(total_charges_tax_amount)/2).toFixed(2);
	    }
	    $('.other_charges_cgst').html(parseFloat(other_charges_cgst).toFixed(2));
        $('.other_charges_sgst').html(parseFloat(other_charges_sgst).toFixed(2));
        $('.other_charges_igst').html(parseFloat(other_charges_igst).toFixed(2));
    }
		    
     
     
    
    
    if(tdspercent > 0){
        tdsval = parseFloat(parseFloat(total_taxable_amount) * (tdspercent / 100)).toFixed(2);
    }
    console.log('tdsval:'+tdsval);
    $("#item_tds_tax_value").val(tdsval);
    
    if(tcspercent > 0){
        tcsval = parseFloat(parseFloat(total_item_cost) * (tcspercent / 100)).toFixed(2);
    }
    $("#item_tcs_tax_value").val(tcsval);
    
    if(charges_tds_percent > 0)
    {
        other_charges_tdsval = parseFloat(parseFloat(total_charges_taxable_amount) * (charges_tds_percent / 100)).toFixed(2);
    }
    $("#other_charges_tds_tax_value").val(other_charges_tdsval);

     calculate_grn_payment_cost();
     
}

function calculate_grn_payment_cost()
{
    var total_summary_taxable_amt       = (isNaN($('.total_summary_taxable_amt').val()) || $('.total_summary_taxable_amt').val()=='' ? 0:$('.total_summary_taxable_amt').val());
    var tds_tax_value                   = (isNaN($('.item_tds_tax_value').val()) || $('.item_tds_tax_value').val()=='' ? 0:$('.item_tds_tax_value').val());
    var total_summary_cgst_amount       = (isNaN($('.total_summary_cgst_amount').val()) || $('.total_summary_cgst_amount').val()=='' ? 0:$('.total_summary_cgst_amount').val());
    var total_summary_sgst_amount       = (isNaN($('.total_summary_sgst_amount').val()) || $('.total_summary_sgst_amount').val()=='' ? 0:$('.total_summary_sgst_amount').val());
    var total_summary_igst_amount       = (isNaN($('.total_summary_igst_amount').val()) || $('.total_summary_igst_amount').val()=='' ? 0:$('.total_summary_igst_amount').val());
    var tcs_tax_value                   = (isNaN($('.item_tcs_tax_value').val()) || $('.item_tcs_tax_value').val()=='' ? 0:$('.item_tcs_tax_value').val());
    var other_charges_tds_tax_value     = (isNaN($('.other_charges_tds_tax_value').val()) || $('.other_charges_tds_tax_value').val()=='' ? 0:$('.other_charges_tds_tax_value').val());
    var other_charges_taxable_amount    = (isNaN($('#other_charges_taxable_amount').val()) || $('#other_charges_taxable_amount').val()=='' ? 0:$('#other_charges_taxable_amount').val());
    var other_charges_tax               = (isNaN($('#other_charges_tax').val()) || $('#other_charges_tax').val()=='' ? 0:$('#other_charges_tax').val());
    var grn_discount                    = (isNaN($('.grn_discount').val()) || $('.grn_discount').val()=='' ? 0:$('.grn_discount').val());
    var final_cost                      = parseFloat(parseFloat(total_summary_taxable_amt)-parseFloat(tds_tax_value)+parseFloat(total_summary_cgst_amount)+parseFloat(total_summary_sgst_amount)+parseFloat(total_summary_igst_amount)+parseFloat(tcs_tax_value)+parseFloat(other_charges_taxable_amount)-parseFloat(other_charges_tds_tax_value)+parseFloat(other_charges_tax)-parseFloat(grn_discount)).toFixed(2)
    

    
     round_of_val               = final_cost;
     tot_cost 			        = parseFloat(Math.round(final_cost));
	 round_of_amt               = parseFloat(tot_cost-round_of_val).toFixed(2);
	 $('.grn_round_off').val(round_of_amt<0.50 ? (round_of_amt < 0 ? (round_of_amt*-1) :round_of_amt) : (round_of_amt < 0 ? (round_of_amt*-1) :round_of_amt));
	 $('.round_off_symbol').val(round_of_amt<0 ? 0 : 1);
	 
     $('.grn_total_cost').val(parseFloat(tot_cost).toFixed(2));
     console.log('total_summary_taxable_amt:'+total_summary_taxable_amt);
     console.log('tds_tax_value:'+tds_tax_value);
     console.log('total_summary_cgst_amount:'+total_summary_cgst_amount);
     console.log('total_summary_sgst_amount:'+total_summary_sgst_amount);
     console.log('total_summary_igst_amount:'+total_summary_igst_amount);
     console.log('tcs_tax_value:'+tcs_tax_value);
     console.log('other_charges_tds_tax_value:'+other_charges_tds_tax_value);
     console.log('other_charges_taxable_amount:'+other_charges_taxable_amount);
     console.log('other_charges_tax:'+other_charges_tax);
     console.log('grn_discount:'+grn_discount);
     console.log();
     showgrnentrypreview();
}

function remove_grn_item_row(curRow)
{
    calculate_grnItem_details();
    curRow.remove();
}

$(document).on('change','.show_in_lwt',function(){
   if($(this).is(":checked"))
   {
       $(this).closest('tr').find('.show_in_lwt').val(1);
   }
   else
   {
       $(this).closest('tr').find('.show_in_lwt').val(0);
   }
});

$('#cus_stoneModal  #update_grn_stn_details').on('click', function(){
	if(validateStoneCusItemDetailRow())
	{
    	var stone_details       =[];
    	var stone_price         =0;
    	var stone_wt            =0;
    	var certification_price =0;
    	var catRow              =$('#custom_active_id').val();
    	var gross_wt            =$('#'+catRow).find('.gross_wt').val();
    	$('#cus_stoneModal .modal-body #estimation_stone_cus_item_details > tbody  > tr').each(function(index, tr) {
    		stone_price+=parseFloat($(this).find('.stone_price').val());
    		stone_wt+=parseFloat($(this).find('.stone_wt').val());
        		stone_details.push({
        		            'show_in_lwt'       : $(this).find('.show_in_lwt').val(),
        		            'stone_id'          : $(this).find('.stone_id').val(),
        		            'stones_type'       : $(this).find('.stones_type').val(),
        		            'stone_pcs'         : $(this).find('.stone_pcs').val(),
        		            'stone_wt'          : $(this).find('.stone_wt').val(),
        		            'stone_cal_type'    : $(this).find('input[type=radio]:checked').val(),
        		            'stone_price'       : $(this).find('.stone_price').val(),
        		            'stone_rate'        : $(this).find('.stone_rate').val(),
        		            'stone_type'        : $(this).find('.stone_type').val(),
        		            'stone_uom_id'      : $(this).find('.stone_uom_id').val()
        		});
    	});
    	if(parseFloat(gross_wt)<parseFloat(stone_wt))
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Entered Weight is Grater Than The Gross Wt."});
    	}
    	else
    	{
            $('#cus_stoneModal').modal('toggle');
            $('#'+catRow).find('.stone_details').val(JSON.stringify(stone_details));
            var row = $('.'+catRow).closest('tr');
            $('#cus_stoneModal .modal-body').find('#estimation_stone_cus_item_details tbody').empty();
    	}
    }
    else
    {
    	$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Fill The Required Fields.."});
    }
    calculate_grnItem_details();
});

function validate_grn_entry_form()
{   
        var grn_type = $("input[name='order[grn_type]']:checked").val();
        var form_validate = true;
        if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Karigar.."});
            form_validate = false;
        }
        else if(($('.referenceno').val()=='' || $('.referenceno').val()==null) )
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Invoice No.."});
            form_validate = false;
        }
        else if(($('.referencedate').val()=='' || $('.referencedate').val()==null) )
        {
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select The Ref Date.."});
            form_validate = false;
        }
        else if((grn_type==1 || grn_type==2) && ($('#grn_item_details > tbody tr').length==0))
        {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
                form_validate = false;
        }
        else if((grn_type==3) && ($('#other_charges_taxable_amount').val()==0 || $('#other_charges_taxable_amount').val()==''))
        {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>No Records Found.."});
                form_validate = false;
        }
        else if(!validateGrnItemDetailRow())
        {
             $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Fill The Required Fields.."});
             form_validate = false;
        }
        
        return form_validate;
}


$('#submit_grn_entry').on('click',function(){
    
    if(validate_grn_entry_form())
    {
        $('#submit_grn_entry').prop('disabled',true);
        $("div.overlay").css("display", "block"); 
        var form_data=$('#grn_entry_form').serialize();
    	/*if($('#po_id').val()!='')
    	{
    	    var url=base_url+ "index.php/admin_ret_purchase/purchase/po_entry_update?nocache=" + my_Date.getUTCSeconds();
    	}else
    	{
    	    var url=base_url+ "index.php/admin_ret_purchase/purchase/po_entry_save?nocache=" + my_Date.getUTCSeconds();
    	}*/
    	var url=base_url+ "index.php/admin_ret_purchase/grnentry/save?nocache=" + my_Date.getUTCSeconds();
    	my_Date = new Date();
        $.ajax({ 
            url:url,
            data: form_data,
            type:"POST",
            dataType:"JSON",
            success:function(data){
    			if(data.status)
    			{
    			    $("div.overlay").css("display", "none"); 
    			}
    		    window.location.href= base_url+'index.php/admin_ret_purchase/grnentry/list';
            },
            error:function(error)  
            {	
                $("div.overlay").css("display", "none"); 
            } 
        });
    }
    
    
});

function get_grn_entry_list(from_date,to_date)
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_ret_purchase/grnentry?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 type:"POST",
			 data:{'from_date': from_date, 'to_date': to_date},
			 success:function(data){
			 	var list=data.list;
			 	var access=data.access;
				var oTable = $('#grn_list').DataTable();
				oTable.clear().draw();				  
				if (list!= null && list.length > 0)
				{  	
					oTable = $('#grn_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "order": [[ 0, "desc" ]],
                    "dom": 'lBfrtip',
                    "buttons" : ['excel','print'],
                    "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
                    "aaData": list,
                    "aoColumns": [
                    { "mDataProp": "grn_id" },
                    { "mDataProp": "grn_ref_no" },
                    { "mDataProp": "grn_date" },
                    { "mDataProp": "karigar_name" },
                    { "mDataProp": "mobile" },
                    { "mDataProp": "grn_supplier_ref_no" },
                    { "mDataProp": "grn_ref_date" },
                    { "mDataProp": "grn_purchase_amt" },
                    { "mDataProp": "billstatus" },
                    { "mDataProp": function ( row, type, val, meta ) {
                    id= row.grn_id;
                    print_url=base_url+'index.php/admin_ret_purchase/grn_invoice/'+id;
                    edit_url=(access.edit=='1' ? '<a href="'+base_url+'index.php/admin_ret_purchase/grnentry/edit/'+id+'" class="btn btn-primary btn-edit"><i class="fa fa-edit" ></i></a>' : '#' );
                    billcancel_url=(access.edit=='1' ? base_url+'index.php/admin_ret_billing/billing/cancell/'+id : '#' );
                    action_content=edit_url+''+'<a href="'+print_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Vendor acknowledgement "><i class="fa fa-print" ></i></a>'+(row.grn_bill_status==1 && access.edit==1 ? '<button class="btn btn-warning" onclick="confirm_delete('+id+')"><i class="fa fa-close" ></i></button>' :'');
                    return action_content;
                    }
                    },
                    
                    ]
                    });
				
				}
				$("div.overlay").css("display", "none"); 
			  },
			  error:function(error)  
			  {
				 $("div.overlay").css("display", "none"); 
			  }	 
	      });
}


function get_sbe_rate_fix_entry_list(from_date,to_date)
{
    $("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_ret_purchase/rate_fixing?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 type:"POST",
			 data:{'from_date': from_date, 'to_date': to_date},
			 success:function(data){
			 	var list = data.list;
			 	var access = data.access;
				var oTable = $('#payment_list').DataTable();
				oTable.clear().draw();				  
				if (list!= null && list.length > 0)
				{  	
					oTable = $('#payment_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": true,
                    "order": [[ 0, "desc" ]],
                    "dom": 'lBfrtip',
                    "buttons" : ['excel','print'],
                    "tableTools": { "buttons": [ { "sExtends": "xls", "oSelectorOpts": { page: 'current' } },{ "sExtends": "pdf", "oSelectorOpts": { page: 'current' } } ] },
                    "aaData": list,
                    "aoColumns": [
                    { "mDataProp": "rate_fix_id" },
                    { "mDataProp": "po_ref_no" },
                    { "mDataProp": "karigar" },
                    { "mDataProp": "rate_fix_wt" },
                    { "mDataProp": "rate_fix_rate" },
                    { "mDataProp": "total_amount" },
                    /*{ "mDataProp": function ( row, type, val, meta ) {
                        id= row.grn_id;
                        billcancel_url=(access.edit=='1' ? base_url+'index.php/admin_ret_purchase/rate_fixing/cancell/'+id : '#' );
                        action_content=''+(row.grn_bill_status==1 ? '<button class="btn btn-warning" onclick="confirm_delete('+id+')"><i class="fa fa-close" ></i></button>' :'');
                        return action_content;
                    }
                    },*/
                    ]
                    });
				
				}
				$("div.overlay").css("display", "none"); 
			  },
			  error:function(error)  
			  {
				 $("div.overlay").css("display", "none"); 
			  }	 
	      });
}

function confirm_delete(bill_id,bill_cancel_otp)
{
	$('#grn_id').val(bill_id);
	$('#confirm-delete').modal('show');
}

$('#cancel_remark').on('keypress',function(){
	if(this.value.length>6)
	{
		$('#grn_cancel').prop('disabled',false);
	}else{
		$('#grn_cancel').prop('disabled',true);
	}
});

$('#grn_cancel').on('click',function(){
    $('#grn_cancel').prop('disabled',true);
	my_Date = new Date();
	$.ajax({
		type: 'POST',
		url:base_url+ "index.php/admin_ret_purchase/grnentry/cancel_grn_entry?nocache=" + my_Date.getUTCSeconds(),
		dataType:'json',
		data:{'cancel_reason':$('#cancel_remark').val(),'grn_id':$('#grn_id').val()},
		success:function(data){
		    window.location.reload();
		}
	});
});


$('#other_metalmodal  #update_grn_other_metal_details').on('click', function(){
	if(validate_other_metal_row())
    {
    	var metal_details=[];
    	var tot_amount=0;
    	var tot_weight=0;
    	var tot_wast_wt=0;
    	var tot_mc_value=0;
    	var catRow              =$('#custom_active_id').val();
    	var gross_wt            =($('#'+catRow).find('.gross_wt').val()!='' ? $('#'+catRow).find('.gross_wt').val():0);
    	var less_wt             =($('#'+catRow).find('.add_less_wt').val()!='' ?$('#'+catRow).find('.add_less_wt').val() :0);
    	$('#other_metalmodal .modal-body #other_metal_table> tbody  > tr').each(function(index, tr) {
    		tot_amount+=parseFloat($(this).find('.amount').val());
    		tot_weight+=parseFloat($(this).find('.gwt').val());
    		tot_wast_wt+=parseFloat($(this).find('.wast_wt').val());
    		tot_mc_value+=parseFloat($(this).find('.mc_value').val());
    		metal_details.push({
    		            'id_metal'      : $(this).find('.select_metal').val(),
    		            'id_purity'     : $(this).find('.select_purity').val(),
    		            'pcs'           : $(this).find('.pcs').val(),
    		            'gwt'           : $(this).find('.gwt').val(),
    		            'wastage_perc'  : $(this).find('.wastage_perc').val(),
    		            'calc_type'     : $(this).find('.calc_type').val(),
    		            'making_charge' : $(this).find('.making_charge').val(),
    		            'rate_per_gram' : $(this).find('.rate_per_gram').val(),
    		            'amount'        : $(this).find('.amount').val(),
    		            'wast_wt'       : $(this).find('.wast_wt').val(),
    		            'mc_value'      : $(this).find('.mc_value').val(),
    		            });
    	});
    	if(parseFloat(parseFloat(gross_wt)+parseFloat(less_wt)) < parseFloat(tot_weight))
    	{
    	    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Entered Weight is Grater Than The Gross Wt."});
    	}else
    	{
    	    
    	    $('#'+catRow).find('.add_other_metal_wt').val(parseFloat(tot_weight).toFixed(3));
    	    $('#'+catRow).find('.other_metal_details').val(JSON.stringify(metal_details));
            $('#other_metalmodal').modal('hide');
            calculate_grnItem_details();
    	}
    }
    else
    {
        $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>Please Fill The Required Details"});
    }
});

function get_ActiveGRNS(){
    
	$.ajax({
	type: 'GET',
	url: base_url+'index.php/admin_ret_purchase/purchase/active_grns',
	dataType:'json',
	success:function(data){
	    activeGRNS = data;
	    var id=$('#select_grn').val();
	    var id_grn= $('#id_grn').val();
		$.each(data, function (key, item) {   
		    $("#select_grn").append(
		    $("<option></option>")
		    .attr("value", item.grn_id) 
		    .text(item.grn_ref_no)  
		    );
		}); 
		$("#select_grn").select2(
		{
			placeholder:"Select GRN",
			closeOnSelect: true		    
		});
		if($("#select_grn").length)
		{
		    $("#select_grn").select2("val",(id!='' && id>0? id:(id_grn!='' && id_grn!=undefined ? id_grn :'')));
		}

		    $(".overlay").css("display", "none");
		}
	});

}

$('#select_grn').on('change',function(){
   var currentgrn = this.value;
   $('#grn_item_details_preview > tbdoy').empty();
   var trHtml = '';
   if(currentgrn != '')
   {
       $("div.overlay").css("display", "block"); 
       var url  = base_url+ "index.php/admin_ret_purchase/purchase/grncatdetailsbygrnid?nocache=" + my_Date.getUTCSeconds();
    	my_Date = new Date();
        $.ajax({ 
            url:url,
            data: {"grnId" : currentgrn},
            type:"POST",
            dataType:"JSON",
            success:function(data){
                
                $.each(activeGRNS, function (gkey, gitem) {
                    if(parseInt(gitem.grn_id) === parseInt(currentgrn)){
                        $("#select_karigar").select2("val", gitem.grn_karigar_id);
                        $("#id_karigar").val(gitem.grn_karigar_id);
                        $(".po_irnno").val(gitem.grn_irnno != null ? gitem.grn_irnno : "");
                        $(".ewaybillno").val(gitem.grn_ewaybillno);
                        $(".po_ref_no").val(gitem.grn_supplier_ref_no);
                        $(".po_ref_date").val(gitem.grn_ref_date != null ? gitem.grn_ref_date : "");
                        $('#despatch_through').select2("val",gitem.grn_despatch_through);
                       
                        $('#po_tds_percent').val(gitem.grn_pay_tds_percent);
                        $('#po_tcs_percent').val(gitem.grn_tcs_percent);
                        $('#other_charges_taxable_amount').val(parseFloat(gitem.charges_amount).toFixed(2));
                        $('#charges_tds_percent').val(gitem.grn_other_charges_tds_percent);
                        $('#other_charges_tax').val(gitem.charges_tax);
                        $('#po_discount').val(gitem.grn_discount);
                        
                        console.log(data);
                        $('#grn_item_details_preview > tbody').empty();
                        $.each(data,function(key,val){
                            trHtml+='<tr>'
                                +'<td>'+val.catname+'</td>'
                                +'<td>'+val.no_of_pcs+'</td>'
                                +'<td>'+val.gross_wt+'</td>'
                                +'<td>'+val.rate_per_grm+'</td>'
                                +'<td>'+val.item_cost+'</td>'
                                +'</tr>';
                        });
                        
                    }
                });
                console.log(trHtml);
                $('#select_category option').remove();
                $('#grn_item_details_preview > tbody').append(trHtml);
                $.each(data, function (key, item) { 
                    
                    $("#select_category").append(
            		    $("<option></option>")
            		    .attr("value", item.cat_id) 
            		    .attr("data-cattype", item.cat_type)
            		    .text(item.catname)  
        		    );
                });

            	$('#select_category').select2({
            	    placeholder: "Category",
            	    allowClear: true
            	});
            	 $("#select_category").select2("val","");
    			$("div.overlay").css("display", "none"); 
    		    grncatdetails = data;
            },
            error:function(error)  
            {	
                $("div.overlay").css("display", "none"); 
            } 
        });
   }
});

//GRN Entry


//supplier edit
$('#edit_karigar').on('click',function(){
    if($('#select_karigar').val()=='' || $('#select_karigar').val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Supplier"});
    }
    else
    {
        $('#confirm-edit').modal('show');
         $.ajax({
            type: "POST",
            url: base_url + "index.php/admin_ret_purchase/get_karigar_details",
            dataType: "JSON",
            data: {"karId": $('#select_karigar').val()},
            success: function(data){
                            $('#id_karigar').val(data.id_karigar);
                            $('#kar_first_name').val(data.karigar_name);
                            $('#kar_pan_no').val(data.pan_no);
                            $('#gst_no').val(data.gst_number);
                            $('#address1').val(data.address1);
                            $('#address2').val(data.address2);
                            $('#address3').val(data.address3);
                            $('#pin_code_add').val(data.pincode);
                            $('#ed_id_city').val(data.id_city);
                            $('#ed_id_state').val(data.id_state);
                            $('#id_country').val(data.id_country);
                            get_country();
            }
        });
    }
    
 
});

$('#country').on('change',function(){

    $('#id_country').val(this.value);

    if(this.value!='')
    {   
        get_state(this.value);
    }
    
});

$('#ed_state').on('change',function(){
    $('#ed_id_state').val(this.value);
    if(this.value!='')
    {
        get_city(this.value);
    }
});

$('#ed_city').on('change',function(){
    if(this.value!='')
    {
        $('#ed_id_city').val(this.value);
    }
});

$("#update_kardetails").on('click',function(){

    
	if($('#kar_first_name').val() == null || $('#kar_first_name').val() == '')   
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Karigar Name"});
	}
	else if($('#kar_pan_no').val() == null || $('#kar_pan_no').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter PAN no"});
	}
	else if($('#country').val() == null || $('#country').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select Country"});
	}
	else if($('#ed_state').val() == null || $('#ed_state').val() == '')
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select State"});
	}
	else if(($('#ed_city').val() == null || $('#ed_city').val() == '') && (order_for==1))
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Select City"});
	}
	else if(($('#address1').val() == 0 || $('#address1').val() == '') )
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Address 1"});
	}
    else if(($('#address2').val() == 0 || $('#address2').val() == '') )
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Address 2 "});
	}
    else if(($('#address3').val() == 0 || $('#address3').val() == '') )
	{
		$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter Address 3"});
	}
	else if(($('#pin_code_add').val() == null || $('#pin_code_add').val() == ''))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter PINCODE"});
    }
    else if(($('#gst_no').val() == null || $('#gst_no').val() == ''))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Enter GST no"});
    }
	else
	{       
	    update_karigar();    
        $('#confirm-edit').modal('hide');

	}
});

function update_karigar(){
   var id_karigar = $('#id_karigar').val();
   var first_name =  $('#kar_first_name').val();
   var pan_no =  $('#kar_pan_no').val();
   var gst_no =  $('#gst_no').val();
   var address1 =  $('#address1').val();
   var address2 =  $('#address2').val();
   var address3 =  $('#address3').val();
   var pin_code_add =  $('#pin_code_add').val();
   var id_city =  $('#ed_id_city').val();
   var id_state =  $('#ed_id_state').val();
   var id_country =  $('#id_country').val();
   $.ajax({
       type: "POST",
       dataType: "JSON",
       data: {"id_karigar":id_karigar,"first_name":first_name ,"pan_no":pan_no , "gst_no":gst_no ,"address1":address1,"address2" :address2 ,"address3" :address3,"pin_code_add":pin_code_add,"id_city":id_city,
       "id_state": id_state,"id_country" :id_country},
       url: base_url + "index.php/admin_ret_purchase/update_karigar",
       success: function(data){
               $.toaster({ priority : data.priority, title : data.title, message : ''+"</br>"+data.msg});
               $("div.overlay").css("display", "none");

   }
   })

}

function get_country()
{
    $('#country option').remove();
    $.ajax({
        type: 'GET',
        url:  base_url+'index.php/settings/company/getcountry',
        dataType: 'json',
        success: function(country) {
            
            $.each(country, function (key, country) 
            {
               
                $('#country').append(
                $("<option></option>")
                .attr("value", country.id)
                .text(country.name)
                );
                
            });
            var id_country=$('#id_country').val();
            var ed_id_country=$('#ed_id_country').val();
            
           $("#country").select2({
            placeholder: "Enter Country",
            allowClear: true
            });	
            if($("#country").length)
            {
                $("#country").select2("val",(id_country!='' ? id_country:''));
            }
        },
        error:function(error)  
        {
        
         }
    });
}

function get_state(id)
{
    $('#ed_state option').remove();
    $.ajax({
        type: 'POST',
        data:{'id_country':id },
        url:  base_url+'index.php/settings/company/getstate',
        dataType: 'json',
        success: function(state) {
            console.log($('#ed_id_state').val());
       
        $.each(state, function (key, state) {
                
            $('#state,#ed_state').append(
            $("<option></option>")
            .attr("value", state.id)
            .text(state.name)
            );
        });
         var id_state=$('#id_state').val();
         
         var ed_id_state=$('#ed_id_state').val();
         $("#state,#ed_state").select2({
            placeholder: "Enter State",
            allowClear: true
        });
        
        if($("#state").length)
        {
            $("#state").select2("val",(id_state!='' ? id_state:''));
        }
        
        if($("#ed_state").length)
        {

            $("#ed_state").select2("val",(ed_id_state!=null && ed_id_state>0 ? ed_id_state:''));
        }
            
        },
        error:function(error)  
        {
        }
    });
}







function get_city(id)
{  
    $('#city option').remove();
    $('#ed_city option').remove();
    $.ajax({
        type: 'POST',
        data:{'id_state':id },
        url:  base_url+'index.php/settings/company/getcity',
        dataType: 'json',
        success: function(city) {
        var id_city=$('#id_city').val();
        var ed_id_city=$('#ed_id_city').val();
        $.each(city, function (key, city) {
            $('#city,#ed_city').append(
            $("<option></option>")
            .attr("value", city.id)
            .text(city.name)
            );
        });
        
        $('#city,#ed_city').select2({
			placeholder: "Enter city",
			allowClear: true
		});
		
		if($("#city").length)
		{
		    $("#city").select2("val", (id_city!=null? id_city :''));
		}
		
		if($("#ed_city").length)
		{
		    $("#ed_city").select2("val",(ed_id_city!=null && ed_id_city>0 ? ed_id_city:''));

		}
        
        },
        error:function(error)  
        {
            
        }
    });
}

$('#kar_pan_no').on('change',function(){
    if($("#kar_pan_no").val() != ""){
        var regexp = /^[a-zA-Z]{5}\d{4}[a-zA-Z]{1}$/;
        if(!regexp.test($("#kar_pan_no").val()))
        {
             $("#kar_pan_no").val("");
             $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter valid PAN No..'});
             $("#kar_pan_no").focus();
        }
    }else{
        
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter valid PAN No..'});
    }

});



$('#pin_code_add').on('blur',function(){
    if(this.value.length==6)
    {
        $('#pin_code_add').val(this.value);
    }
    else{
         $.toaster({priority : 'danger',title:'warning!',message:''+"</br>"+'Please enter Valid Number..'});
         $('#pin_code_add').val('');
         $('#pin_code_add').focus();
    }
 });






 $('#gst_no').on('change',function(){
    var reggst = /^([0][1-9]|[1-2][0-9]|[3][0-7])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9])+$/;
    if(!reggst.test($('#gst_no').val()))
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Enter the Valid GST No..'});
        $('#gst_no').val('');
        return false;
    }
});



//supplier edit