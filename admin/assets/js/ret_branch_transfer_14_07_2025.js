var path =  url_params();
var ctrl_page 		= path.route.split('/'); 
var lotDetail = [];
var searchResList = [];
var branchArr = [];
var timer = null;
var oldMetalDetail=[];
var other_inventory_item=[];
$(document).ready(function() { 
    
    switch(ctrl_page[1])
	{
	    case 'branch_transfer':
	             switch(ctrl_page[2])
	             {
	                 case 'list':
	                        $('#from_date').text(moment().startOf('month').format('YYYY-MM-DD'));
                            $('#to_date').text(moment().endOf('month').format('YYYY-MM-DD'));	
                            get_ajaxBranchTransferlist();
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
                            
                            $('#from_date').text(start.format('YYYY-MM-DD'));
                            $('#to_date').text(end.format('YYYY-MM-DD')); 
                            get_ajaxBranchTransferlist(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'))	
                            }
                            );   
							$("#bt_cancel").prop("disabled", true);

	                 break;
	                 
	                 case 'add':
						get_ActiveKarigars();
						var date = new Date();
						var firstDay  = new Date(date.getFullYear(), date.getMonth(),date.getDate() - 0, 1); 
						var from_date=(firstDay.getDate()+"-"+(firstDay.getMonth() + 1)+"-"+firstDay.getFullYear());
						var to_date=(date.getDate()+"-"+(date.getMonth() + 1)+"-"+date.getFullYear());
						$('#rpt_payments1').html(from_date);
						$('#rpt_payments2').html(to_date);
							  $('#bill_date').daterangepicker(
							{
							  ranges: {
								'Today': [moment(), moment()],
								'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
								'Last 7 Days': [moment().subtract(6, 'days'), moment()],
								'Last 30 Days': [moment().subtract(29, 'days'), moment()],
								'This Month': [moment().startOf('month'), moment().endOf('month')],
								'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
							  },
							  startDate: moment().subtract(1, 'days'),
							  endDate: moment()
							},
						  function (start, end) {
						  $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
					  
								$('#rpt_payments1').text(start.format('YYYY-MM-DD'));
								$('#rpt_payments2').text(end.format('YYYY-MM-DD'));		            
						  }
						);
						if($('#allow_transfer_type').val()==2) // For EDA Transfer
						{
							$('#is_eda').val(2);
							$('.toBranch').css("color","red");
						}	
						get_ActiveSections();
						$("#nt_receipt").select2({
                            placeholder: "Select Receipt",
                            allowClear: true
                        });
                        getBTBranches();
					 break;
                     
					 case 'approval_list':
						if($('#allow_approval_type').val()==2) // For EDA Transfer
						{
							$('#is_eda_appr').val(2);
							$('#bt_trans_code').css("background-color","red");
						}
						getBTBranches();
					 break;	
	             }
	    break;
	}
	
	
	 var path =  url_params(); 
    $('.dateRangePicker').daterangepicker({
	  ranges: {
		'Today': [moment(), moment()],
		'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		'This Month': [moment().startOf('month'), moment().endOf('month')],
		'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	  },
	  startDate: moment().subtract(1, 'days'),
	  endDate: moment()
	});  
    $('#bt_list').dataTable({
    "bLengthChange": false,
         "aLengthMenu": [
            [25, 50, 100, 200, 1000],
            [25, 50, 100, 200, 1000]
        ],
        iDisplayLength: 1000,
    });
    if(ctrl_page[3]==2)
    {
        $('#appr_type2').prop('checked', true);
        get_branch_transfer_download();
    }else{
        $('#appr_type2').prop('checked', false);
    }
    
    $("#lotno").select2({			    
	 	placeholder: "Select Lot",			    
	 	allowClear: true		    
 	});
 	
 	$(".from_branch,#to_brn,.filter_to_brn").select2({			    
	 	placeholder: "Select To Branch",			    
	 	allowClear: true		    
 	});
 	
 	$('#isOtherIssue').change(function() {
 	    // $('.remark').css("display","none");
 		if(this.checked){
 			if(ctrl_page[2] == 'add'){
				$('.to_branch_blk').css("display","none");
				if($("input[name='transfer_item_type']:checked").val() == 1 )
				{
				$('.karigar').css("display","block");
				}
				$('input[type=radio][name=bt_approval_type]').attr("disabled",true);
			}else{
				$('.app_to_brn').css("display","none");
			} 
		}else{
			if(ctrl_page[2] == 'add'){
				$('.to_branch_blk').css("display","block");
				if($("input[name='transfer_item_type']:checked").val() == 1 )
				{
				$('.karigar').css("display","none");
				}
				$('input[type=radio][name=bt_approval_type]').attr("disabled",false);
			}else{
				$('.app_to_brn').css("display","block");
			} 
		}
 	})
 	
	$('input[type=radio][name=bt_approval_type]').change(function() { 
	    $(".from_branch").attr("disabled",false);
	    $(".filter_to_brn").attr("disabled",false); 
	    $(".from_branch option,.filter_to_brn option").remove();
	    
	    $('#bt_approval_list >tbody').empty();
	    $('#bt_approval_list_nt >tbody').empty();
	    $('#bt_approval_list_old_metal >tbody').empty();
	    $('#bt_approval_list_orders >tbody').empty();
	    
	    var id_branch = "";
	    var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);   
		var approval_type =  $("input[name='bt_approval_type']:checked").val(); 
		var from_brn = (approval_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());
		var to_brn = ( isOtherIssue == 1? $('#other_issue_branch').val() :(approval_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val()));
		
	    $.each(branchArr, function (key, item) { 
		    if(approval_type == 1){ // Transit Approval  
		    	 $(".app_to_brn").css("display","block");
			 	 if(loggedInBranch != item.id_branch){ 
		    	 	 $(".to_branch,.filter_to_brn,.from_branch").append(						
		        	 	$("<option></option>")						
		        	 	.attr("value", item.id_branch)						  						  
		        	 	.text(item.name )						  					
		    	 	 );
			 	 }
			 	 else{ 
		    	    $(".from_branch").attr("disabled",true);
    			 	$(".from_branch").append(						
    		        	 	$("<option></option>")						
    		        	 	.attr("value", item.id_branch)						  						  
    		        	 	.text(item.name )						  					
    		    	 ); 
			 	    $(".from_branch").select2("val",from_brn);
			 	 }
			 	 $("#filtr_to_brn").select2("val",null);
			 	 /*if(loggedInBranch == item.id_branch){			        	 	
		    	 	$(".app_frm_brn").css("display","none");
			 	 } */
			}else{ // Stock Download 
				 $(".app_frm_brn").css("display","block"); 
		    	 if(loggedInBranch != item.id_branch){			        	 	
		    	 	 $(".from_branch").append(						
		        	 	$("<option></option>")						
		        	 	.attr("value", item.id_branch)						  						  
		        	 	.text(item.name )						  					
		    	 	 ); 
			 	 }else{ 
    			 	 $(".filter_to_brn").append(						
    		        	 	$("<option></option>")						
    		        	 	.attr("value", item.id_branch)						  						  
    		        	 	.text(item.name )						  					
    		    	 );
			 	    $(".filter_to_brn").attr("disabled",true);  
			 	    $(".filter_to_brn").select2("val",to_brn);
			 	 }
			 	 $(".from_branch").select2("val",null);
			 	 /*if(loggedInBranch == item.id_branch){		 
		    	 	 $(".filter_to_brn").css("display","none");
		 	 	}  	*/		        	 	 
			} 
		}); 
		
		if($("input[name='bt_approval_type']:checked").val() == 2  && $("#branch_trans_dnload").val()==2 && $("input[name='transfer_item_type']:checked").val() ==1)
	    {
				$("#bt_approval_list").css("display", "none");
				$('#bt_approval_list').parents('div.dataTables_wrapper').first().hide();
				//$("#bt_approval_list_by_scan").css("display", "block");
				//$("#tag_scan_code").css("display", "block");
				$("#bt_approval_list > tbody > tr").remove();
                $('#upd_status_btn').hide();
		}
		else
		{
            $("#btran_filter").prop('disabled', false);
			$("#bt_trans_code").val('');
			$('#upd_status_btn').show();
			$("#bt_approval_list").css("display", "block");
			$(".scan_summary").css("display", "none");
			$("#bt_dwnload_list").css("display", "none");
			$("#bt_approval_list_by_scan").css("display", "none");
			$("#bt_dwnload_list > tbody > tr").remove();
			$("#bt_approval_list_by_scan > tbody > tr").remove();
		}
		
		if(isOtherIssue){
 			$('.app_to_brn').css("display","none");
		}else{
			$('.app_to_brn').css("display","block");
		}
	    
	});
 	  	
 	/*$("#lotno").on('change', function(e){ //Client asked to remove LOT NO SEARCH 
		if($("#lotno").val() != ''){
			trans_type =  $("input[name='transfer_item_type']:checked").val();
			if(trans_type == 2){ 
			}else{
				$("#product").val("");  
				$("#id_product").val(""); 
				$("#prod").html("");  
			} 
		}else{
			$("#product").val("");  
			$("#id_product").val(""); 
			$("#prod").html(""); 
		}
	});*/ 
	  
	// Approval Listing Page functions
	$("#appr_sel_all_tg").click(function(event) {
		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
		event.stopPropagation(); 
		calcTaggedApprList(); 
	}); 
	
	$(document).on('change',".trans_id", function(){
		calcTaggedApprList(); 
	});
	
	$(document).on('input change',".entered_btcode", function(){
	    var curRow = $(this).closest('tr');
		if($(this).val() == curRow.find('.branch_trans_code').val()){
		   curRow.find('.trans_id').prop('checked', true);
		   curRow.find('.trans_id').prop('disabled', false);
		   calcTaggedApprList(); 
		}else{
		   curRow.find('.trans_id').prop('checked', false);
		   curRow.find('.trans_id').prop('disabled', true);
		}  
	});
	
    function calcTaggedApprList()
    { 
		var pieces = 0;
        var grs_wt = 0;
        var net_wt = 0;
        $("input[name='trans_id[]']:checked").each(function() { 
			var trans_id = $(this).val();
			$('#bt_approval_list > tbody tr').each(function(bidx, brow){
				var row = $(this);  
				if(trans_id == row.find('td:first .trans_id').val()){ 
		            pieces = pieces + (isNaN(row.find('.t_pieces').val() ) ? 0 : parseFloat(row.find('.t_pieces').val())); 
		            grs_wt = grs_wt + (isNaN( row.find('.t_gross_wt').val() ) ? 0 : parseFloat(row.find('.t_gross_wt').val()));
		            net_wt = net_wt + (isNaN( row.find('.t_net_wt').val() ) ? 0 :parseFloat(row.find('.t_net_wt').val())) ;   
				} 
			});
		});
		/*$("#bt_approval_list input[type=checkbox]:checked").each(function () {
			var row = $(this).closest('tr');  
            pieces = pieces + (isNaN(row.find('td:eq(8) .t_pieces').val() ) ? 0 : parseFloat(row.find('td:eq(8) .t_pieces').val())); 
            grs_wt = grs_wt + (isNaN( row.find('td:eq(9) .t_gross_wt').val() ) ? 0 : parseFloat(row.find('td:eq(9) .t_gross_wt').val()));
            net_wt = net_wt + (isNaN( row.find('td:eq(10) .t_net_wt').val() ) ? 0 :parseFloat(row.find('td:eq(10) .t_net_wt').val())) ;             
        });*/ 
		$(".t_tot_pieces").val(pieces);
        $(".t_tot_gross_wt").val(parseFloat(grs_wt).toFixed(3));
        $(".t_tot_net_wt").val(parseFloat(net_wt).toFixed(3));
    }
	
	$("#appr_sel_all_nt").click(function(event) {
		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
		event.stopPropagation();  
		calcNTaggedApprList();
	});
	
	$(document).on('change',".branch_transfer_id", function(){
		calcNTaggedApprList();
	});
	
	function calcNTaggedApprList(){
		var pieces = 0;
        var grs_wt = 0;
        var net_wt = 0;
		$("#bt_approval_list_nt input[type=checkbox]:checked").each(function () { 
			var row = $(this).closest('tr'); 
            pieces = pieces + (isNaN(row.find('.nt_pieces').val() ) ? 0 : parseFloat(row.find('.nt_pieces').val())); 
            grs_wt = grs_wt + (isNaN( row.find('.nt_gross_wt').val() ) ? 0 : parseFloat(row.find('.nt_gross_wt').val()));
            net_wt = net_wt + (isNaN( row.find('.nt_net_wt').val() ) ? 0 :parseFloat(row.find('.nt_net_wt').val())) ;             
        }); 
		$(".nt_tot_pieces").val(pieces);
        $(".nt_tot_gross_wt").val(parseFloat(grs_wt).toFixed(3));
        $(".nt_tot_net_wt").val(parseFloat(net_wt).toFixed(3));
    }
    
    $("#upd_status_btn,#resend_otp").click(function() {
        if($("input[name='trans_id[]']:checked").val())
        {
            var allow_submit = false;
            trans_type =  $("input[name='transfer_item_type']:checked").val();
            if(trans_type==1)
            {
				$("#bt_approval_list tbody tr").each(function(index, value){
				if($(value).find("input[name='trans_id[]']:checked").is(":checked"))
				{
					allow_submit=true;
					return true;
				}
				});
            }
            else if(trans_type==2)
            {
                $("#bt_approval_list_nt tbody tr").each(function(index, value){
				if($(value).find("input[name='trans_id[]']:checked").is(":checked"))
				{
					allow_submit=true;
					return true;
				}
				});
            }
            else if(trans_type==3)
            {
                $("#bt_approval_list_old_metal tbody tr").each(function(index, value){
				if($(value).find("input[name='trans_id[]']:checked").is(":checked"))
				{
					allow_submit=true;
					return true;
				}
				});
            }
            else if(trans_type==4)
            {
                $("#bt_approval_list_packaging tbody tr").each(function(index, value){
				if($(value).find("input[name='trans_id[]']:checked").is(":checked"))
				{
					allow_submit=true;
					return true;
				}
				});
            }
            else if(trans_type==5)
            {
            	$("#bt_approval_list_orders tbody tr").each(function(index, value){
				if($(value).find("input[name='trans_id[]']:checked").is(":checked"))
				{
					allow_submit=true;
					return true;
				}
				});
            }
            if(allow_submit)
            {
				if($('#required_otp_approval').val() == 1){//If otp required
					if($('#BT_otp_approval_type').val()==1){
						$("#otp").attr("disabled",false);
						send_otp(); 
					}else if($('#BT_otp_approval_type').val()==2){
						send_mobile_approval_request();
						$('#upd_status_btn').prop('disabled',true);
					}
				}else{
					approveBranchTransfer();
				}
            }
        }
        else
        {
            //alert('Please Enter The BT Code..');
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please enter the Valid BT code to proceed"});
        }
	}); 
	
	$('#approve').click(function() {
	    approveBranchTransfer();
	});
	
	function approveBranchTransfer()
    {
		$('#close').trigger("click");
    	trans_type =  $("input[name='transfer_item_type']:checked").val();
		var data = [];
		var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);   
		var approval_type =  $("input[name='bt_approval_type']:checked").val(); 
		var from_brn = (approval_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());
		var to_brn = ( isOtherIssue == 1? $('#other_issue_branch').val() :(approval_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val()));
		if(trans_type == 1){ // Tagged  
			$("input[name='trans_id[]']:checked").each(function() { 
				var trans_id = $(this).val();
				$('#bt_approval_list > tbody tr').each(function(bidx, brow){
					var row = $(this); 
					if(trans_id == row.find('td:first .trans_id').val()){
						var dt = {
							"trans_id" : trans_id,
							"to_branch" : row.find('.to_branch').val() , 
						    "from_branch": row.find('.from_branch').val() , 
							"tag_id" : row.find('td:first .tag_id').val(), 
							"is_other_issue" : row.find('td:first .is_other_issue').val() 
						}
						console.log(dt);
						data.push(dt);	
					} 
				});
			});  
		}
		else if(trans_type == 2){
			if($("input[name='trans_id[]']:checked").val())
			{
				$("input[name='trans_id[]']:checked").each(function() { 
				    var trans_id = $(this).val();
			    	$('#bt_approval_list_nt > tbody tr').each(function(bidx, brow){
			    	    	var row = $(this); 
		    	    		if(trans_id == row.find('td:first .trans_id').val())
		    	    		{
		    	    		    var dt = {
            						"trans_id" : trans_id,
            						"to_branch" : row.find('.to_branch').val() , 
            						"from_branch": row.find('.from_branch').val() , 
            						"id_nontag_item" : row.find('.id_nontag_item').val() , 
									"id_nontag_receipt" : row.find('.id_nontag_receipt').val() , 
            						"id_product" : row.find('.id_product').val() , 
            						"id_design" : row.find('.id_design').val() , 
            						"id_sub_design" : row.find('.id_sub_design').val() , 
            						"id_section" : row.find('.id_section').val() , 
            						"no_of_piece" : row.find('.nt_pieces').val(), 
            						"gross_wt" : row.find('.nt_gross_wt').val(), 
            						"net_wt" : row.find('.nt_net_wt').val(),
            						"is_other_issue" : row.find('td:first .is_other_issue').val()   
            					}
            					data.push(dt);
		    	    		}
			    	});
				});  
			}
		}
		else if(trans_type == 3){
			if($("input[name='trans_id[]']:checked").val())
			{
				$("input[name='trans_id[]']:checked").each(function() { 
				    var trans_id = $(this).val();
			    	$('#bt_approval_list_old_metal > tbody tr').each(function(bidx, brow){
			    	    	var row = $(this); 
		    	    		if(trans_id == row.find('td:first .trans_id').val())
		    	    		{
		    	    		    var dt = {
            						"trans_id" : trans_id,
									"to_branch" : row.find('.to_branch').val() , 
            						"from_branch": row.find('.from_branch').val() , 
            					}
            					data.push(dt);
		    	    		}
			    	});
				});  
			}
		}
		else if(trans_type == 4){
			if($("input[name='trans_id[]']:checked").val())
			{
				$("input[name='trans_id[]']:checked").each(function() { 
				    var trans_id = $(this).val();
			    	$('#bt_approval_list_packaging > tbody tr').each(function(bidx, brow){
			    	    	var row = $(this); 
		    	    		if(trans_id == row.find('td:first .trans_id').val())
		    	    		{
		    	    		    var dt = {
            						"trans_id" : trans_id,
									"to_branch" : row.find('.to_branch').val() , 
            						"from_branch": row.find('.from_branch').val() , 
            					}
            					data.push(dt);
		    	    		}
			    	});
				});  
			}
		}
		else if(trans_type == 5)
		{
			if($("input[name='trans_id[]']:checked").val())
			{
				$("input[name='trans_id[]']:checked").each(function() { 
					var row = $(this).closest('tr');  
					var dt = {
						"trans_id" : $(this).val(),
						"to_branch" : row.find('.to_branch').val() , 
						"from_branch": row.find('.from_branch').val() , 
						"id_orderdetails": row.find('.id_orderdetails').val() , 
					}
					data.push(dt);
				});  
			}
		}
		brnTransUpdStatus(data,$('#upd_status_btn').val(),trans_type);
	}
	
    $('#close').on('click',function(){
		clearTimeout(timer); //clears the previous timer.  
		$("#otp_status").css("display", 'none');
		$("#otp").val('');
		$("#closed").val('');
		$("#resend_otp").attr("disabled", true); 
		$("#approve").attr("disabled", true); 
    });
    
     $('#verify_otp').on('click',function(){
        var approval_type =  $("input[name='bt_approval_type']:checked").val(); 
    	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);   
		$("#verify_otp").attr("disabled", true);
		if(approval_type==2 && isOtherIssue==1)
		{
	    	my_Date = new Date();
        	$.ajax({
        		url:base_url+ "index.php/admin_ret_brntransfer/verify_other_issue_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        		data: {"otp":$("#otp").val()},
        		type:"POST",
        		dataType: "json", 
        		async:false,
        		success:function(data){
        			if(data.status)
        			{
        				$('#otp').prop('disabled',true);
        				$('#approve').prop('disabled',false);
        				$('#verify_otp').prop('disabled',true);
        				$('#resend_otp').prop('disabled',true);
        				$(".otp_alert").append('<p style="color:green">'+data.msg+'</p>');
        			}
        			else
        			{
        				$('#approve').prop('disabled',true);
        				$('#otp').prop('disabled',false);
        				$('#verify_otp').prop('disabled',false);
        				$(".otp_alert").append('<p style="color:red">'+data.msg+'</p>');
        			} 
        			setTimeout(function() {
        				$('.otp_alert').css('display','none');
        			},10000);
        		},
        		error:function(error)  
        		{
        			$(".overlay").css('display',"none"); 
        		}	 
        	 }); 
		}
		else
		{
		    verify_otp();
		}
    });  
	
    $('#otp').on('input',function(){
        if(this.value.length==6)
        {        
              $('#verify_otp').prop('disabled',false);
        }
        else
        {
            $('#verify_otp').prop('disabled',true);
           // alert('Please fill the 6 digit Otp');
        }
    })
	
	// Listing Page functions - Ends
	
	// Non Tagged
	/*$('#nt_product').on('keypress', function(e){
		if(($('#nt_product').val()).length == 0){
			alert(1);
		}
	});*/
	
	$('#nt_select_all').click(function(event) {
		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
		event.stopPropagation();  
		calculateNTtotal();
	});
	
	$(document).on('click',".nt_item_sel", function(){
		calculateNTtotal();
	});	
	 
	$(document).on('change',".id_lot_inward_detail", function(){
		calculateNTtotal();
	});
	 
	$(document).on('input',".nt_piece", function(){
		var row = $(this).closest('tr'); 
		blc = parseFloat(row.find('.blc_pieces').val());
		if($(this).val() > blc){
			row.find('.err').text('Invalid');
			row.find('.nt_piece').val(blc);
		}else{
			row.find('.err').text('');
		}
		calculateNTtotal();
	});
	
	$(document).on('input',".nt_gross_wt", function(){
		var row = $(this).closest('tr'); 
		blc = parseFloat(row.find('.blc_gross_wt').val());
		if($(this).val() > blc){
			row.find('.err').text('Invalid');
			row.find('.nt_gross_wt').val(blc);
		}else{
			row.find('.err').text('');
		}
		calculateNTtotal();
	});
	
	$(document).on('input',".nt_net_wgt", function(){
		var row = $(this).closest('tr'); 
		blc = parseFloat(row.find('.blc_net_wgt').val());
		if($(this).val() > blc){
			row.find('.err').text('Invalid');
			row.find('.nt_net_wgt').val(blc);
		}else{
			row.find('.err').text('');
		}
		calculateNTtotal();
	}); 
	
	function calculateNTtotal(){
		var pieces = 0;
        var grs_wt = 0;
        var net_wt = 0;
		$("#bt_nt_search_list input[type=checkbox]:checked").each(function () { 
			var row = $(this).closest('tr'); 
            pieces = pieces + (isNaN(row.find('.nt_piece').val() ) ? 0 : parseFloat(row.find('.nt_piece').val())); 
            grs_wt = grs_wt + (isNaN( row.find('.nt_gross_wt').val() ) ? 0 : parseFloat(row.find('.nt_gross_wt').val()));
            net_wt = net_wt + (isNaN( row.find('.nt_net_wgt').val() ) ? 0 :parseFloat(row.find('.nt_net_wgt').val())) ;             
        });  
		$(".nt_pieces").val(pieces);
        $(".nt_grs_wt").val(parseFloat(grs_wt).toFixed(3));
        $(".nt_net_wt").val(parseFloat(net_wt.toFixed(3)));
    }
    
	// Tagged
	$('#select_all').click(function(event) {
		$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
		event.stopPropagation();
		/*// Reset Values 
        $('#bt_list tbody tr').empty();
        
        var pieces = 0;
        var grs_wt = 0;
        var net_wt = 0;
		$("#bt_search_list input[type=checkbox]:checked").each(function () { 
			var row = $(this).closest('tr');  
             
            // Check for dupicate tag_id in Preview table
            var row_exist = false;
            $("#bt_list  > tbody tr").each(function () { 
            	var prev_row = $(this).closest('tr'); 
            	if(row.find('td:first .tag_id').val() == prev_row.find('td:first .tag_id').val()){
					row_exist = true;
				} 
				else{
					pieces = pieces + (isNaN(row.find('td:eq(6) .piece').val() ) ? 0 : parseFloat(row.find('td:eq(6) .piece').val())) + (isNaN(prev_row.find('td:eq(6) .piece').val() ) ? 0 : parseFloat(prev_row.find('td:eq(6) .piece').val())); 
		            grs_wt = grs_wt + (isNaN( row.find('td:eq(7) .gross_wt').val() ) ? 0 : parseFloat(row.find('td:eq(7) .gross_wt').val())) + (isNaN( prev_row.find('td:eq(7) .gross_wt').val() ) ? 0 : parseFloat(prev_row.find('td:eq(7) .gross_wt').val()));
		            net_wt = net_wt + (isNaN( row.find('td:eq(8) .net_wgt').val() ) ? 0 :parseFloat(row.find('td:eq(8) .net_wgt').val()))  + (isNaN( prev_row.find('td:eq(8) .net_wgt').val() ) ? 0 :parseFloat(prev_row.find('td:eq(8) .net_wgt').val())) ; 
				}
            })
            // Add in preview
            var t = $('#bt_list').DataTable(); 
            if(row.find('td:first .tag_id').val() != undefined && !row_exist){ 
	            t.row.add( [	            
		            '<input type="hidden" name="tag_id[]" class="tag_id" value="'+row.find('td:first .tag_id').val()+'">'+row.find('td:first .tag_id').val(),
		            '<input type="hidden" name="tag_code[]" class="tag_code" value="'+row.find('td:eq(1) .tag_code').val()+'">'+ row.find('td:eq(1) .tag_code').val(),
		            '<input type="hidden" name="id_lot_inward_detail[]" class="id_lot_inward_detail" value="'+row.find('td:eq(2) .id_lot_inward_detail').val()+'">'+ row.find('td:eq(2) .id_lot_inward_detail').val(),
		            '<input type="hidden" name="product[]" class="product" value="'+row.find('td:eq(3) .product').val()+'">'+ row.find('td:eq(3) .product').val(),
		            '<input type="hidden" name="design[]" class="design" value="'+row.find('td:eq(4) .design').val()+'">'+ row.find('td:eq(4) .design').val(),
		            '<input type="hidden" name="tag_datetime[]" class="tag_datetime" value="'+row.find('td:eq(5) .tag_datetime').val()+'">'+ row.find('td:eq(5) .tag_datetime').val(), 
		            '<input type="hidden" name="piece[]" class="piece" value="'+row.find('td:eq(6) .piece').val()+'">'+ row.find('td:eq(6) .piece').val(), 
		            '<input type="hidden" name="gross_wt[]" class="gross_wt" value="'+row.find('td:eq(7) .gross_wt').val()+'">'+ row.find('td:eq(7) .gross_wt').val(), 
		            '<input type="hidden" name="net_wgt[]" class="net_wgt" value="'+row.find('td:eq(8) .net_wgt').val()+'">'+ row.find('td:eq(8) .net_wgt').val()
		        ] ).draw( false ); 
			}
            
        }); 
		$(".prev_pieces").val(pieces);
        $(".prev_grs_wt").val(grs_wt);
        $(".prev_net_wt").val(net_wt);*/
	});
	 
	$(document).on('change',".tag_id", function(){
        var pieces = 0;
        var grs_wt = 0;
        var net_wt = 0;
		$("#bt_search_list input[type=checkbox]:checked").each(function () {
			var row = $(this).closest('tr'); 
            pieces = pieces + parseFloat(row.find('td:eq(6) .piece').val()); 
            grs_wt = grs_wt + parseFloat(row.find('td:eq(7) .gross_wt').val());
            net_wt = net_wt + parseFloat(row.find('td:eq(8) .net_wgt').val());  
			dia_wt = dia_wt + parseFloat(row.find('td:eq(8) .dia_wgt').val());  
        });
        $(".pieces").val(pieces);
        $(".grs_wt").val(grs_wt);
        $(".net_wt").val(net_wt);
		$(".dia_wt").val(dia_wt);
	}) 
	
$("#add_to_list").on('click',function(){  
		var pieces = 0;
        var grs_wt = 0;
        var net_wt = 0;
		var dia_wt = 0;
		$("#bt_search_list tbody tr").each(function (index, value) {
			var row = $(this).closest('tr'); 
			if(row.find("input[name='tag_id[]']:checked").is(":checked"))
			{
      			// Check for dupicate tag_id in Preview table
                var row_exist = false;  
    			$("#bt_list  > tbody tr").each(function () { 
                	var prev_row = $(this).closest('tr');  
                	if(row.find('td:first .tag_id').val() == prev_row.find('td:first .tag_id').val()){
    					row_exist = true;
    				}
                })
                
      			// Add in preview 
      			if(!row_exist){
    				var t = $('#bt_list').DataTable();
    				
    	            t.row.add( [	            
    		            '<input type="hidden" name="tag_id[]" class="tag_id" value="'+row.find('td:first .tag_id').val()+'">'+row.find('td:first .tag_id').val(),
						'<input type="hidden" name="tag_code[]" class="tag_code" value="'+row.find('td:eq(1) .tag_code').val()+'">'+ row.find('td:eq(1) .tag_code').val(),
						'<input type="hidden" name="old_tag_code[]" class="old_tag_code" value="'+row.find('td:eq(2) .old_tag_code').val()+'">'+ row.find('td:eq(2) .old_tag_code').val(),
						'<input type="hidden" name="id_lot_inward_detail[]" class="id_lot_inward_detail" value="'+row.find('td:eq(3) .id_lot_inward_detail').val()+'">'+ row.find('td:eq(3) .id_lot_inward_detail').val(),
						'<input type="hidden" name="section[]" class="section" value="'+row.find('td:eq(4) .section').val()+'">'+ row.find('td:eq(4) .section').val(),
						'<input type="hidden" name="product[]" class="product" value="'+row.find('td:eq(5) .product').val()+'">'+ row.find('td:eq(5) .product').val(),
						'<input type="hidden" name="design[]" class="design" value="'+row.find('td:eq(6) .design').val()+'">'+ row.find('td:eq(6) .design').val(),
						'<input type="hidden" name="tag_datetime[]" class="tag_datetime" value="'+row.find('td:eq(7) .tag_datetime').val()+'">'+ row.find('td:eq(7) .tag_datetime').val(), 
						'<input type="hidden" name="piece[]" class="piece" value="'+row.find('td:eq(8) .piece').val()+'">'+ row.find('td:eq(8) .piece').val(), 
						'<input type="hidden" name="gross_wt[]" class="gross_wt" value="'+row.find('td:eq(9) .gross_wt').val()+'">'+ row.find('td:eq(9) .gross_wt').val(), 
						'<input type="hidden" name="net_wgt[]" class="net_wgt" value="'+row.find('td:eq(10) .net_wgt').val()+'">'+ row.find('td:eq(10) .net_wgt').val(),
						'<input type="hidden" name="dia_wgt[]" class="dia_wgt" value="'+row.find('td:eq(11) .dia_wgt').val()+'">'+ row.find('td:eq(11) .dia_wgt').val(),
						'<input type="text_area" class="form-control tag_remark">',
    		      
					] ).draw( false ); 
    			}  
		    }
        });
        
        
		
        $("#bt_list  > tbody tr").each(function () { 
        	var prev_row = $(this).closest('tr');  
        	 
			pieces = pieces + (isNaN(prev_row.find('td:eq(8) .piece').val() ) ? 0 : parseFloat(prev_row.find('td:eq(8) .piece').val())); 
            grs_wt = grs_wt + (isNaN( prev_row.find('td:eq(9) .gross_wt').val() ) ? 0 : parseFloat(prev_row.find('td:eq(9) .gross_wt').val()));
            net_wt = net_wt + (isNaN( prev_row.find('td:eq(10) .net_wgt').val() ) ? 0 :parseFloat(prev_row.find('td:eq(10) .net_wgt').val())) ;  
			dia_wt = dia_wt + (isNaN( prev_row.find('td:eq(11) .dia_wgt').val() ) ? 0 :parseFloat(prev_row.find('td:eq(11) .dia_wgt').val())) ;   
		})
        $(".prev_pieces").val(pieces);
        $(".prev_grs_wt").val(parseFloat(grs_wt).toFixed(3));
        $(".prev_net_wt").val(parseFloat(net_wt).toFixed(3));
		$(".prev_dia_wt").val(parseFloat(dia_wt).toFixed(3));
        //$("#bt_search_list > tbody").empty();
	});
	
	function update_order_description(curRow)
	{
		$('#description').val(curRow.closest('tr').find('.order_des').val());
		$('#order_des').modal('show');
	}
	
	/*$(document).on('click',".add_order_desc", function(){ 
		var curRow = $("#cur_des").val();
		var content=$("#order_des #description").val();
		$("#cur_des").val(content);
		$('#order_des').modal('toggle');
	});*/
	
	$("#add_to_transfer").on('click',function(){  
		to_branch =  $("#to_brn").val(); 
		from_branch =  $("#from_brn").val(); 
		var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);  
		if( (!isNaN(to_branch) && to_branch > 0) || isOtherIssue == 1){
			trans_type =  $("input[name='transfer_item_type']:checked").val(); 
			$(".overlay").css("display", "block");
			if(trans_type == 1){ // Tagged
			
				if(isOtherIssue == 0)
			     {
			         if((!isNaN(to_branch) && to_branch > 0)  && (!isNaN(from_branch) && from_branch > 0) ){
    					var tagged_data = [];
    					$("#bt_list > tbody tr").each(function() { 
    						var row = $(this).closest('tr'); 
    						if(row.find('td:first .tag_id').val() != undefined && row.find('td:first .tag_id').val() != null){
    						    tagged_data.push({"tag_id" : row.find('td:first .tag_id').val(), "piece" : row.find('.piece').val(),"id_lot_inward_detail" : row.find('td:eq(2) .id_lot_inward_detail').val(),"tag_remark" : row.find('.tag_remark').val()});
    						}
    					});
    					if(tagged_data.length > 0) {
    					    add_to_trans(tagged_data);
    					}else{
    					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select tag code to proceed..'});
    					    $(".overlay").css("display", "none");
    					}
			         }else{
			             $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select branch properly ..'});
    					 $(".overlay").css("display", "none");
			         }
			     }
			     else
			     {
			         /*if($('#is_otp_required_for_approval').val()==1)
			         {
                         send_other_issue_otp();
			         }
			         else
			         {
			            if($("input[name='tag_id[]']:checked").val())
        				{
        					var tagged_data = [];
        					$("#bt_list > tbody tr").each(function() { 
        						var row = $(this).closest('tr'); 
        						tagged_data.push({"tag_id" : row.find('td:first .tag_id').val(), "id_lot_inward_detail" : row.find('td:eq(2) .id_lot_inward_detail').val()});
        					});
        					if(tagged_data.length > 0) 
        					add_to_trans(tagged_data);
        				}
			         }*/
        					var tagged_data = [];
        					$("#bt_list > tbody tr").each(function() { 
        						var row = $(this).closest('tr'); 
        						if(row.find('td:first .tag_id').val() != undefined && row.find('td:first .tag_id').val() != null){
        					    	tagged_data.push({"tag_id" : row.find('td:first .tag_id').val(), "piece" : row.find('.piece').val(),"id_lot_inward_detail" : row.find('td:eq(2) .id_lot_inward_detail').val()});
        						}
        					});
        					if(tagged_data.length > 0) {
        					    add_to_trans(tagged_data);
        					}else{
        					    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select tag code to proceed..'});
        					    $(".overlay").css("display", "none");
        					}
        					/*if(tagged_data.length > 0) 
        					add_to_trans(tagged_data);*/
			     }
			}
			else if(trans_type == 2){
			    if((!isNaN(to_branch) && to_branch > 0)  && (!isNaN(from_branch) && from_branch > 0) || isOtherIssue == 1 &&  (!isNaN(from_branch) && from_branch > 0)){
    				var non_tagged = [];
    				var grs_wt = 0;
    				var net_wt = 0;
    				var pieces = 0;
    				$("input[name='nt_item_sel[]']:checked").each(function() {
    				    var row = $(this).closest('tr'); 
    				    grs_wt+=parseFloat(row.find('.nt_gross_wt').val());
    				    net_wt+=parseFloat(row.find('.nt_net_wgt').val());
    				    pieces+=parseFloat(row.find('.nt_piece').val());
    					
    					var data = {
    								'id_nontag_item' : row.find('.id_nontag_item').val(),
    								'pieces' : row.find('.nt_piece').val() ,
    								'grs_wt' : row.find('.nt_gross_wt').val() ,
    								'net_wt' : row.find('.nt_net_wgt').val() ,
    								'id_lot_inward_detail': row.find('.id_lot_inward_detail').val(),
									'id_nontag_receipt' : row.find('.id_nontag_receipt').val(),
    							   };
    					non_tagged.push(data); 
    				}); 
    				if(pieces < 0)
    				{
    				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Pcs..'});
    				    $(".overlay").css("display", "none");
    				}
    				else if(grs_wt < 0)
    				{
    				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Gross Weight..'});
    				    $(".overlay").css("display", "none");
    				}
    				else if(net_wt < 0)
    				{
    				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Valid Net Weight..'});
    				    $(".overlay").css("display", "none");
    				}
    				else if(parseFloat(grs_wt) < parseFloat(net_wt))
    				{
    				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Net Weight Is Grater Than The Gross Weight..'});
    				    $(".overlay").css("display", "none");
    				}
    				else
    				{
    				    if(non_tagged.length > 0)
    				    add_to_trans(non_tagged); 
    				}
    				
			    }else{
	                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select branch properly ..'});
				    $(".overlay").css("display", "none");
	            }
			}
			else if(trans_type == 3){		// Old Metal
				var old_metal_data = [];
				$.each(oldMetalDetail,function(key,items){
					if(items.is_checked==1)
					{
						old_metal_data.push({
						    //'old_metal_sale_id':items.old_metal_sale_id,
						    'item_type'        :items.transfer_items,//1-Old Metal,2-Sales Return
						    'trans_id'         :items.trans_id,
						    'bill_det_id'      :items.bill_det_id,
						    'tag_id'           :items.tag_id,
						    'gross_wt'         :items.gross_wt,
						    'net_wt'           :items.net_wt,
						    'is_non_tag'       :items.is_non_tag,
						});
						
					}
				});
				if(old_metal_data.length > 0)
				add_to_trans(old_metal_data); 
			}
			else if(trans_type == 4){ // Other Inventory
				var packaging_data = [];
				$("#packaging_list > tbody > tr").each(function() { 
					var row = $(this).closest('tr'); 
					var data = {
								'id_other_item' : row.find('.id_other_item').val(),
								'no_of_pcs'     : row.find('.no_of_pcs').val() ,
							   };
					packaging_data.push(data); 
				}); 
				if(packaging_data.length > 0)
				add_to_trans(packaging_data); 
			}
			else if(trans_type == 5){
				var order_details = []; 
				if($("input[name='id_orderdetails[]']:checked").val())
				{
				    $("input[name='id_orderdetails[]']:checked").each(function() { 
    					var row = $(this).closest('tr'); 
    					var data = {
    								'id_orderdetails' 	: row.find('.id_orderdetails').val(),
    								 };
    					order_details.push(data); 
    				}); 
    				if(order_details.length > 0)
				    add_to_trans(order_details); 
				}
				else
				{
				    $(".overlay").css("display", "none");
				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Order Item..'});
				}
			}
		} 
		else{
			alert("To Branch Required");
		}
		
	})
	
	/*$("#add_to_trans_list").on('click',function(){ 
		$(".overlay").css("display", "block");		
		var selected = [];
		if($("input[name='tag_id[]']:checked").val())
		{
			var t = $('#bt_list').DataTable();  
			$('#bt_search_list > tbody  > tr').each(function(index, tr) {  
				t.row.add( [	            
		            $(this).find('td:first .tag_id').val(),
		            $(this).find('td:eq(1) .tag_code').val(),
		            $(this).find('td:eq(2) .lot_no').val(),
		            $(this).find('td:eq(3) .product').val(),
		            $(this).find('td:eq(4) .design').val(),
		            $(this).find('td:eq(5) .tag_datetime').val(), 
		            $(this).find('td:eq(6) .piece').val(), 
		            $(this).find('td:eq(7) .gross_wt').val(), 
		            $(this).find('td:eq(8) .net_wgt').val()
		        ] ).draw( false ); 
			});  
		}
		$(".overlay").css("display", "none");		
	});*/
	

	$("#tag_no").on('paste', function () {
        setTimeout(() => {
               
            if ($(this).val().trim() !== '') {

                getTagSearchList();

            }

        }, 200);

    });


	$("#search_est_no").on('click',function(){ 
		if($('#from_brn').val() == null){
			alert("Please fill from branch");
			return false;
		}else{
			if($("#esti_no").val() != ''){
				$(".overlay").css("display", "block");		
				getEstiTags();	
			}else{
				alert("Enter Estimation No.");
			} 
		}
	})

	

	
	$(".btrn_search").on('click',function(){ 
		trans_type =  $("input[name='transfer_item_type']:checked").val();
		if($('#from_brn').val() == null){
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select The From Branch..'});
			return false;
		}
		else if($('#product').val() == '' && (trans_type == 1 && $('#design').val() == '' )){ 
			if((trans_type == 1 && ($('#tag_no').val() != '' || $('#old_tag_no').val() != '' )))
			{
				getTagSearchList(); 
			}
			else if((trans_type == 1 && $('#lotno').val() != '' &&  $('#lotno').val()!=null))
			{
				getTagSearchList(); 
			}
			else if((trans_type == 1 && $('#section_select').val() != '' &&  $('#section_select').val()!=null))
			{
				getTagSearchList(); 
			}
			else
			{
				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Fill 1 or More Search Fields..'});
				return false;
			} 
		}
		else if(trans_type==5 && ($('#order_no').val()==''))
		{
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Order No..'});
		}
		else{  
			$(".overlay").css("display", "block");
			if(trans_type == 1){ // Tagged
				getTagSearchList();
			}
			else if(trans_type == 2 && ($('#nt_receipt').val()==null)){  // Non Tagged
				getNonTaggedItem();
			}
			else if(trans_type==2 && ($('#nt_receipt').val()!='' &&  $('#nt_receipt').val()!=null))
			{
				getNonTaggedReceiptedItem();
			}
			else if(trans_type == 3){  // Old Metal
			    oldMetalDetails=[];
				get_purchase_items();
			}
			else if(trans_type == 5) // Repair Orders
			{
				getRepairOrderDetails();
			}
		}
	});
	
	$("#btran_filter").on('click', function(e){	``
	    trans_type =  $("input[name='transfer_item_type']:checked").val(); 
		approval_type =  $("input[name='bt_approval_type']:checked").val(); 
		var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);   
		from_brn = (approval_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());
		to_brn = ( isOtherIssue == 1 ? $('#other_issue_branch').val() :(approval_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val()));
    	if((from_brn == null || from_brn == undefined) && (to_brn == null || to_brn == undefined)){
    	     $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please select filters properly..'});
    	}else{
    		if(trans_type == 1 ){
    			get_brantranTagged(from_brn,to_brn);	
    		}else if(trans_type == 2){
    			get_brantranNonTagged(from_brn,to_brn);	
    		}
    		else if(trans_type == 3){
    			get_brantranOldMetal(from_brn,to_brn);	
    		}
    		else if(trans_type == 4){
    			get_brantranPackagingItems(from_brn,to_brn);	
    		}
    		else if(trans_type == 5){
    			get_branchtranOrderDetails(from_brn,to_brn);	
    		}
	    }
	});
	
	function get_branch_transfer_download()
	{
	    trans_type =  $("input[name='transfer_item_type']:checked").val(); 
		// From and to branch validation
		approval_type =  $("input[name='bt_approval_type']:checked").val(); 
		var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);   
		/*var proceed = ( loggedInBranch > 0 ? (approval_type == 1 ? ($("#filtr_to_brn").val() != null) : ($("#filter_from_brn").val() != null) ): ($("#filter_from_brn").val() != null && $("#filtr_to_brn").val() != null) ); 
		if( proceed || isOtherIssue == 1){*/
		console.log($("#filter_from_brn").val()+" "+$("#filtr_to_brn").val()); 
			from_brn = (approval_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());
			to_brn = ( isOtherIssue == 1 ? $('#other_issue_branch').val() :(approval_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val()));
			if(trans_type == 1 ){
				get_brantranTagged(from_brn,to_brn);	
			}else if(trans_type == 2){
				get_brantranNonTagged(from_brn,to_brn);	
			}
		/*}else{
			alert("From and To branch are mandatory");
		}*/
	}
	
	$(".from_branch,#filter_from_brn").on('change', function(e){
	     $('#select_item option').remove();
	    trans_type =  $("input[name='transfer_item_type']:checked").val(); 
		var isDisabled = $(".to_branch,.filter_to_brn ").prop('disabled');  
		if(this.value != '' && !isDisabled){ 
			$("#to_brn option,.filter_to_brn option").remove();
			$("#to_brn,.filter_to_brn").val(null).trigger('change');
			var gst_number = $('.from_branch option:selected').attr('data-gst_number');
		 	$(".from_branch option,#filter_from_brn option").each(function()
			{  				
			    var to_brch_gst_number = $(this).attr('data-gst_number');
				if(($(this).val() != $(".from_branch,#filter_from_brn").val()) && (gst_number==to_brch_gst_number))
				{
		    	 	$("#to_brn,.filter_to_brn").append(						
		    	 	$("<option></option>")						
		    	 	.attr("value", $(this).val())
		    	 	.text($(this).text())
		    	 	.attr("data-gst_number",to_brch_gst_number)		
		    	 	);	  
				}	
				$("#to_brn,.filter_to_brn").val('');		    	
			});	
			
			
			if(trans_type==4)
			{
			    get_invnetory_item();
			}
			
			if(ctrl_page[2] == 'add'){
				get_received_lots();
			}	
			
			get_ActiveSections();
				
		}
	});
	$("#to_brn,.filter_to_brn").on('change',function()
	{
		if(this.value!='')
		{
			if(ctrl_page[2] == 'add')
			{
				getNonTagReceiptedLots();
			}
		}
		
	})
 	 
 	jQuery(".product").on("input", function(){  
 		var id = this.id; 
		var prod = $("#"+id).val();  
		if(prod.length >= 3) {  
			getSearchProd(prod);
        }
	});
	
	$("#design").on("keyup",function(e){ 
		var design = $("#design").val(); 
		if(design.length == 2) { 
			getSearchDesign(design);
        }
	}); 
      
/*	$('input[type=radio][name="transfer_item_type"]').change(function() {
    	if(this.value == 1){
			$('.non_tagged').css("display","none");	
			$('.tagged').css("display","block");	
		}
		else if(this.value == 2){
			$('.non_tagged').css("display","block");		
			$('.tagged').css("display","none");		
		}
		$("#product").val(""); 
		$("#product").prop("readonly",false); 
		$("#id_product").val(""); 
		$("#prod").html("");  
    })*/
    
    	$('input[type=radio][name="transfer_item_type"]').change(function() {
    	    
    	   // debugger;
    	    
    	    $('.old_metal').css("display","none");	
			$('.non_tagged').css("display","none");	
			$('.tagged').css("display","none");
			$('.packaging').css("display","none");
			$('.orders').css("display","none");
			$('.order_Search').css("display","none");
			$('#is_eda').val(1);
			$('.toBranch').css("color","");
			$('#is_eda_appr').val(1);
			$('#bt_trans_code').css("background-color","");
			$('.brn_nt_rec').css("display","none");
			$('.brn_lot').css("display","block");
			
			
    	if(this.value == 1){
			$('.old_metal').css("display","none");	
			$('.non_tagged').css("display","none");	
			$('.tagged').css("display","block");	
			if(ctrl_page[2]=='approval_list')
			{
				$('#filtr_to_brn').select2("val",'');
			}else if(ctrl_page[2]=='add')
			{
				$('.to_branch').select2("val",'');
			}
			$('.to_branch').prop('disabled',false);
			$('#filtr_to_brn').prop('disabled',false);
		}
		else if(this.value == 2){
			if(ctrl_page[2]=='approval_list')
			{
				$('#filtr_to_brn').select2("val",'')
			}else if(ctrl_page[2]=='add')
			{
				$('.to_branch').select2("val",'');
			}
			$('.non_tagged').css("display","block");		
			$('.tagged').css("display","none");		
			$('.old_metal').css("display","none");
			$('.to_branch').prop('disabled',false);	
			$('#filtr_to_brn').prop('disabled',false);
			$('.brn_nt_rec').css("display","block");
			$('.brn_lot').css("display","none");
			$('#karigar').css("display","none");
		}
		else if(this.value == 3)
		{
			if(ctrl_page[2]=='approval_list')
			{
				$('#filtr_to_brn').select2("val",$('#head_office_branch').val());
				if($('#allow_approval_type').val()==3) // All Transfer
				{
					$(document).on('keypress',function(e){
						if(e.keyCode == 10) //ctrl+enter
						{			 	   
							if($('#is_eda_appr').val()==1) //IF is_eda_appr is 1 Normal Bt receipt,2-No2 Bt receipt
							{
								$('#is_eda_appr').val(2);
								$('#bt_trans_code').css("background-color","red");			
							}
							else
							{
								$('#is_eda_appr').val(1);	
								$('#bt_trans_code').css("background-color","");								
							}										
						}
					});
				}
				else if($('#allow_approval_type').val()==1) // Normal Transfer
				{
					$('#is_eda_appr').val(1);
					$('#bt_trans_code').css("background-color","");	
				}
				else // EDA Transfer
				{
					$('#is_eda_appr').val(2);
					$('#bt_trans_code').css("background-color","red");
				}
			}else if(ctrl_page[2]=='add')
			{
				$('.to_branch').select2("val",$('#head_office_branch').val());
				if($('#allow_transfer_type').val()==3) // All Transfer
				{
					$(document).on('keypress',function(e){
						if(e.keyCode == 10) //ctrl+enter
						{			 	   
							if($('#is_eda').val()==1) //IF is_eda is 1 Normal Sale,2-No2 Sale
							{
								$('#is_eda').val(2);	
								$('.toBranch').css("color","red");
				
							}
							else
							{
								$('#is_eda').val(1);	
								$('.toBranch').css("color","");								
							}										
						}
					});
				}
				else if($('#allow_transfer_type').val()==1) // Normal Transfer
				{
					$('#is_eda').val(1);
					$('.toBranch').css("color","");
				}
				else // EDA Transfer
				{
					$('#is_eda').val(2);
					$('.toBranch').css("color","red");
				}
			}
			
			$('#filtr_to_brn').prop('disabled',true);
			$('.to_branch').prop('disabled',true);
			$('.old_metal').css("display","block");
			$('.non_tagged').css("display","none");	
			$('.tagged').css("display","none");
			$('.brn_lot').css("display","none");
			$('.brn_section').css("display","none");
		}
		else if(this.value == 4)
		{
		    $('.old_metal').css("display","none");	
			$('.non_tagged').css("display","none");	
			$('.tagged').css("display","none");
			$('.packaging').css("display","block");
			$('.brn_lot').css("display","none");
			$('.brn_section').css("display","none");
		}
		
		else if(this.value == 5){
		    if(ctrl_page[2]=='approval_list')
			{
				$('#filtr_to_brn').select2("val",'')
			}else if(ctrl_page[2]=='add')
			{
				$('.to_branch').select2("val",'');
			}
			$('.orders').css("display","block");
			$('.order_Search').css("display","block");
			$('.tagged').css("display","none");	
			$('.non_tagged').css("display","none");	
			$('.otp_block').css("display","block");	
			$('.old_metal').css("display","none");
			$('#filtr_to_brn').prop('disabled',false);
			$('.to_branch').prop('disabled',false);	
			$('.brn_lot').css("display","none");
			$('.brn_section').css("display","none");
		}
		$("#product").val(""); 
		$("#product").prop("readonly",false); 
		$("#id_product").val(""); 
		$("#prod").html("");  
		/*if((ctrl_page[2] == 'approval_list' && $("#filter_from_brn").val() != null) || (ctrl_page[2] == 'add' && $("#from_brn").val() != null)){
			get_received_lots();
		}*/ 
    })
    	 
});
function getSearchProd(searchTxt){
	my_Date = new Date();
	$.ajax({
        url: base_url+'index.php/admin_ret_catalog/product/active_prodBySearch/?nocache=' + my_Date.getUTCSeconds(),             
        dataType: "json", 
        method: "POST", 
        data: {'searchTxt':searchTxt}, 
        success: function (data) { 
			$( ".product" ).autocomplete(
			{
				source: data,
				select: function(e, i)
				{ 
					e.preventDefault();
					$(".product" ).val(i.item.label); 
					$("#id_product" ).val(i.item.value); 
				},
				response: function(e, i) {
		            // ui.content is the array that's about to be sent to the response callback.
		            if (i.content.length === 0) {
 		               $("#prodAlert").html('<p style="color:red">Enter a valid Product</p>');
		               $('#id_product').val('');
		            }else{
 						$("#prodAlert").html('');
					} 
		        },
				 minLength: 0,
			});
        }
     });
}
function getSearchDesign(searchTxt){
	my_Date = new Date();
	$.ajax({
        url: base_url+'index.php/admin_ret_brntransfer/branch_transfer/getDesignByFilter/?nocache=' + my_Date.getUTCSeconds(),             
        dataType: "json", 
        method: "POST", 
        data: {'searchTxt':searchTxt,'prodId':$("#id_product").val()}, 
        success: function (data) { 
			$( "#design" ).autocomplete(
			{
				source: data,
				select: function(e, i)
				{ 
					e.preventDefault();
					$("#design" ).val(i.item.label); 
					$("#id_design" ).val(i.item.value);  
				},
				response: function(e, i) {
		            // ui.content is the array that's about to be sent to the response callback.
		            console.log(i); 
		        },
				 minLength: 0,
			});
        }
     });
}
function get_received_lots(){
	trans_type =  $("input[name='transfer_item_type']:checked").val();
	from_brn = $("#from_brn,#filter_from_brn").val();
	to_brn = $("#filtr_to_brn").val();
	if(from_brn != '' && trans_type != ''){
		$.ajax({		
		 	type: 'POST',		
		 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/getLotsByBranch',		
		 	dataType : 'json',		
		 	data : {'from_branch': from_brn,'to_branch': to_brn,'trans_type' : trans_type, 'page' : ctrl_page[2]},
		 	success  : function(data){
				lotDetail = data;
			 	var id =  $('#lotno').val();	
			 	$("#lotno option").remove();
			 	$("#lotno").append(						
		    	 	$("<option></option>")						
		    	 	.attr("value", "")
		    	 	.text("")						  					
	    	 	);			 	
			 	$.each(data, function (key, item) {				  			   		
		    	 	$("#lotno").append(						
		    	 	$("<option></option>")						
		    	 	.attr("value", item.lot_no)
		    	 	.text(item.lot_no)						  					
		    	 	);	 
		     	});						
		     	$("#lotno").select2("val",(id!='' && id>0?id:''));	 
		     	$(".overlay").css("display", "none");			
		 	}	
		}); 	
	}
} 





$(document).on('keyup change', '.nt_gross_wt', function(e) {
    var curRow = $(this).closest('tr');  // Get the closest row to the input field

    
    var grossWeight = parseFloat(curRow.find('.nt_gross_wt').val());

    if (!isNaN(grossWeight) && grossWeight !== 0) {
    
        curRow.find('.nt_net_wgt').val(grossWeight);
    }
});




function getNonTaggedItem(){ 
	my_Date = new Date();
	var prodId = ($("#nt_product").val() != ""?$("#id_product").val():'');
	$.ajax({
		 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getNonTaggedItem?nocache=" + my_Date.getUTCSeconds(),
		 data: {'lot_dt_rng':"",'prodId':prodId,'lotno':"",'from_brn':$("#from_brn").val(),'id_section':$('#section_select').val()},  
		 dataType:"JSON",
		 type:"POST",
		 cache:false,
		 success:function(data){  
		 	 $("div.overlay").css("display", "none"); 
			 $('#nt_total').text(data.length);
			 var oTable = $('#bt_nt_search_list').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#bt_nt_search_list').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"dom": 'lBfrtip',
						"aaData"  : data,
						"columnDefs": [
							{
								targets: [0, 1, 2, 3, 4],
								className: 'dt-body-left'
							},
							{
								targets: [5, 6],
								className: 'dt-body-right'
							},
						],
						
						"aoColumns": [	{ "mDataProp":function ( row, type, val, meta ){ 
											return '<input type="checkbox" name="nt_item_sel[]" class="nt_item_sel" value="'+row.nt_item_sel+'"><input type="hidden" class="id_lot_inward_detail" name="id_lot_inward_detail[]" value="'+row.id_lot_inward_detail+'"><input type="hidden" class="id_nontag_item" name="id_nontag_item[]" value="'+row.id_nontag_item+'">';
										}},   
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span style="text-align:left;" class="'+cls+'">'+row.section_name+'</span>';
										}},
										
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'">'+row.product_name+'</span>';
										}},
										
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'">'+row.design_name+'</span>';
										}}, 
										
										
										// { "mDataProp":function ( row, type, val, meta ){
										// 	var cls = (row.lot_no == '' ? 'text-maroon' : '' );
										// 	return '<span class="'+cls+'">'+row.sub_design_name+'</span>';
										// }}, 


										
										
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'"><input type="number" name="nt_piece[]" class="nt_piece col-md-6" value="'+row.no_of_piece+'"><input type="hidden" class="blc_pieces col-md-6" value="'+row.no_of_piece+'"> of &nbsp;'+ parseFloat(row.no_of_piece).toFixed(3)+'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';
										}},
                                        


									
                                        

										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'"><input type="number" step=any name="nt_gross_wt[]" class="nt_gross_wt col-md-6" value="'+parseFloat(row.gross_wt).toFixed(3)+'"><input type="hidden" class="blc_gross_wt col-md-6" value="'+row.gross_wt+'"> of &nbsp;'+ parseFloat(row.gross_wt).toFixed(3) +'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'"><input type="number" step=any name="nt_net_wgt[]" class="nt_net_wgt col-md-6" value="'+parseFloat(row.net_wt).toFixed(3)+'"readonly disabled><input type="hidden" class="blc_net_wgt col-md-6" value="'+row.net_wt+'"> of &nbsp;' +parseFloat(row.net_wt).toFixed(3)+'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';
										}}   
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
/*function getTagSearchList()
{
	my_Date = new Date();
	$.ajax({
		 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getTagsByFilter?nocache=" + my_Date.getUTCSeconds(),
		 data: {'lot_dt_rng':$("#lot_dt_rng").val(),'tag_dt_rng':"",'design_id':$("#id_design").val(),'prodId':$("#id_product").val(),'lotno':"",'from_brn':$("#from_brn").val(),'tag_no':$("#tag_no").val()}, 
		 dataType:"JSON",
		 type:"POST",
		 cache:false,
		 success:function(data){  
		 	 $(".overlay").css("display", "none");
		 	 var searchResList = data;
			 $('#total').text(data.length);
			 var oTable = $('#bt_search_list').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#bt_search_list').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"dom": 'lBfrtip',
						"aaData"  : data,
						"aoColumns": [	{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="checkbox" name="tag_id[]" class="tag_id" value="'+row.tag_id+'">';
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="tag_code[]" class="tag_code" value="'+row.tag_code+'">'+ row.tag_code;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="id_lot_inward_detail[]" class="id_lot_inward_detail" value="'+row.id_lot_inward_detail+'">'+ row.lot_no;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="product[]" class="product" value="'+row.product+'">'+ row.product;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="design[]" class="design" value="'+row.design+'">'+ row.design;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="tag_datetime[]" class="tag_datetime" value="'+row.tag_datetime+'">'+ row.tag_datetime;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="piece[]" class="piece" value="'+row.piece+'">'+ row.piece;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="gross_wt[]" class="gross_wt" value="'+row.gross_wt+'">'+ row.gross_wt;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="net_wgt[]" class="net_wgt" value="'+row.net_wt+'">'+ row.net_wt;
										}}   
									]
					});			  	 	
				} 
		  },
		  error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	 
	});
}*/
function getTagSearchList()
    {
        $(".overlay").css("display", "block");
        my_Date = new Date();
        $.ajax({
        url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getTagsByFilter?nocache=" + my_Date.getUTCSeconds(),
        data: {'lot_dt_rng':$("#lot_dt_rng").val(),'tag_dt_rng':"",'design_id':$("#id_design").val(),'prodId':$("#id_product").val(),'lotno':$("#lotno").val(),'from_brn':$("#from_brn").val(),'tag_no':$("#tag_no").val(),'karigar':$("#karigar").val(),'collection_id':$("#select_collection").val(),'id_karigar':$("#select_karigar").val(),'from_date':$('#from_date').text(),'to_date':$('#to_date').text(),'bt_code':$('#bt_code').val(),'old_tag_no':$('#old_tag_no').val(),'id_section':$('#section_select').val(),'tagged_branch':$('#tagged_branch').val()},
        dataType:"JSON",
        type:"POST",
        cache:false,
        success:function(data){
        
                var searchResList = data;
                $('#total').text(data.length);
              
                if (data!= null && data.length > 0)
                {   
                    html='';
                    rowExist=false;
                    
                    
                    $('#bt_search_list > tbody tr').each(function(bidx, brow){
                        bt_tagid = $(this);
                        
                        // CHECK DUPLICATES - TAG
                        
                        if(bt_tagid.find('.tag_code').val() != '')
                        {
                            if( data[0].tag_id == bt_tagid.find('.tag_id').val()){
                            rowExist = true;
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});
                            } 
                        }
                    });
                    
                    if(!rowExist){
                        var total_pcs = parseInt($(".tot_bt_pcs").html());
                        var tot_gross_wt = parseFloat($(".tot_bt_gross_wt").html());
						var tot_net_wt = parseFloat($(".tot_bt_net_wt").html());
                        var tot_dia_wt = parseFloat($(".tot_bt_dia_wt").html());
						var tot_tag_count = parseInt($(".tot_tag_count").html());
                        var html ='';
                        $.each(data, function (key, val) {
                            total_pcs += parseInt(val.piece);
                            tot_gross_wt += parseFloat(val.gross_wt);
							tot_net_wt += parseFloat(val.net_wt);
							tot_dia_wt += parseFloat(val.tag_dia_wt);
							tot_tag_count++;
                            html += '<tr>'+
                            '<td><input type="checkbox" name="tag_id[]" class="tag_id" value='+val.tag_id+' checked></td>'+//add checked
                            '<td><input type="hidden" name="tag_code[]" class="tag_code" value='+val.tag_code+'>'+val.tag_code+'</td>'+
							'<td><input type="hidden" name="old_tag_code[]" class="old_tag_code" value='+val.old_tag_code+'>'+val.old_tag_code+'</td>'+
                            '<td><input type="hidden" name="id_lot_inward_detail[]" class="id_lot_inward_detail" value='+val.lot_no+'></input>'+val.lot_no+'</td>'+
							'<td><input type="hidden" name="section" class="section" value='+val.section+'>'+val.section+'</td>'+
							'<td><input type="hidden" name="product[]" class="product" value='+val.product+'>'+val.product+'</td>'+
                            '<td><input type="hidden" name="design[]" class="design" value='+val.design+'>'+val.design+'</td>'+
                            '<td><input type="hidden" name="tag_datetime[]" class="tag_datetime" value='+val.tag_datetime+'>'+val.tag_datetime+'</td>'+
                            '<td><input type="hidden" name="piece[]" class="piece" value='+val.piece+'>'+val.piece+'</td>'+
                            '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value='+val.gross_wt+'>'+val.gross_wt+'</td>'+
                            '<td><input type="hidden" name="net_wgt[]" class="net_wgt" value='+val.net_wt+'>'+val.net_wt+'</td>'+
							'<td><input type="hidden" name="dia_wgt[]" class="dia_wgt" value='+val.tag_dia_wt+'>'+val.tag_dia_wt+'</td>'+
							'<td><a href="#" onclick="remove_brn_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'+
                            '</tr>';
                        });
						$(".tot_tag_count").html(parseInt(tot_tag_count));
                        $(".tot_bt_pcs").html( parseInt(total_pcs));
                        $(".tot_bt_gross_wt").html(parseFloat(tot_gross_wt).toFixed(3));
						$(".tot_bt_net_wt").html(parseFloat(tot_net_wt).toFixed(3));
                        $(".tot_bt_dia_wt").html(parseFloat(tot_dia_wt).toFixed(3));
                        if($('#bt_search_list  > tbody > tr').length > 0 )
                        {
                        $('#bt_search_list > tbody > tr:first').before(html);
                        }
                        else
                        {
                        $('#bt_search_list > tbody').append(html);
                        }
                        $('#tag_no').val('');
					    $("#tag_no").focus();
                    }
                }   
                else
                {
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
					$('#tag_no').val('');
					$("#tag_no").focus();
                }
                $(".overlay").css("display", "none");
            },
            
            error:function(error)  
            {
            $("div.overlay").css("display", "none");
            }
        });
    
    }
    
    // function remove_brn_row(curRow)
    // {
    // 	curRow.remove();
    // }
	function remove_brn_row(curRow) {
		var total_count = parseInt($(".tot_tag_count").html());
		var total_pcs = parseInt($(".tot_bt_pcs").html());
		var tot_gross_wt = parseFloat($(".tot_bt_gross_wt").html());
		var tot_net_wt = parseFloat($(".tot_bt_net_wt").html());
		var tot_dia_wt = parseFloat($(".tot_bt_dia_wt").html());
	
		// Get values from the row being removed
		var pcsToRemove = parseInt(curRow.find('.piece').val());
		var grossWtToRemove = parseFloat(curRow.find('.gross_wt').val());
		var netWtToRemove = parseFloat(curRow.find('.net_wgt').val());
		var diaWtToRemove = parseFloat(curRow.find('.dia_wgt').val());
	
		// Update totals by subtracting the values of the removed row
		total_count -= 1;
		total_pcs -= pcsToRemove;
		tot_gross_wt -= grossWtToRemove;
		tot_net_wt -= netWtToRemove;
		tot_dia_wt -= diaWtToRemove;
	
		// Update the total values
		$(".tot_tag_count").html(parseInt(total_count));
		$(".tot_bt_pcs").html(parseInt(total_pcs));
		$(".tot_bt_gross_wt").html(parseFloat(tot_gross_wt).toFixed(3));
		$(".tot_bt_net_wt").html(parseFloat(tot_net_wt).toFixed(3));
		$(".tot_bt_dia_wt").html(parseFloat(tot_dia_wt).toFixed(3));
	
		// Remove the row
		curRow.remove();
	}
/*function getEstiTags(){
	my_Date = new Date();
	$.ajax({
		 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getEstiTagsByFilter?nocache=" + my_Date.getUTCSeconds(),
		 data: {'esti_no':$("#esti_no").val(),'from_brn':$("#from_brn").val()}, 
		 dataType:"JSON",
		 type:"POST",
		 cache:false,
		 success:function(data){  
		 	 $(".overlay").css("display", "none");		
		 	 var searchResList = data;
			 $('#total').text(data.length);
			 var oTable = $('#bt_search_list').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#bt_search_list').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"dom": 'lBfrtip',
						"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
						"aaData"  : data,
						"aoColumns": [	{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="checkbox" name="tag_id[]" class="tag_id" value="'+row.tag_id+'">';
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="tag_code[]" class="tag_code" value="'+row.tag_code+'">'+ row.tag_code;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="id_lot_inward_detail[]" class="id_lot_inward_detail" value="'+row.id_lot_inward_detail+'">'+ row.id_lot_inward_detail;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="product[]" class="product" value="'+row.product+'">'+ row.product;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="design[]" class="design" value="'+row.design+'">'+ row.design;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="tag_datetime[]" class="tag_datetime" value="'+row.tag_datetime+'">'+ row.tag_datetime;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="piece[]" class="piece" value="'+row.piece+'">'+ row.piece;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="gross_wt[]" class="gross_wt" value="'+row.gross_wt+'">'+ row.gross_wt;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="net_wgt[]" class="net_wgt" value="'+row.net_wt+'">'+ row.net_wt;
										}}   
									]
					});			  	 	
				} 
		  },
		  error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	 
	});
}*/
function getEstiTags(){
	my_Date = new Date();
	$.ajax({
		 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getEstiTagsByFilter?nocache=" + my_Date.getUTCSeconds(),
		 data: {'esti_no':$("#esti_no").val(),'from_brn':$("#from_brn").val()}, 
		 dataType:"JSON",
		 type:"POST",
		 cache:false,
		 success:function(data){  
		 	  $(".overlay").css("display", "none");
                var searchResList = data;
                $('#total').text(data.length);
              
                if (data!= null && data.length > 0)
                {   
                    html='';
                    rowExist=false;
                    
                    
                    $('#bt_search_list > tbody tr').each(function(bidx, brow){
                        bt_tagid = $(this);
                        
                        // CHECK DUPLICATES - TAG
                        
                        if(bt_tagid.find('.tag_code').val() != '')
                        {
                            if( data[0].tag_id == bt_tagid.find('.tag_id').val()){
                            rowExist = true;
                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});
                            } 
                        }
                    });
                    
                    if(!rowExist){
                        var total_pcs = parseInt($(".tot_bt_pcs").html());
                        var tot_gross_wt = parseFloat($(".tot_bt_gross_wt").html());
						var tot_net_wt = parseFloat($(".tot_bt_net_wt").html());
                        var tot_dia_wt = parseFloat($(".tot_bt_dia_wt").html());
                        $.each(data, function (key, val) {
                            total_pcs += parseInt(val.piece);
                            tot_gross_wt += parseFloat(val.gross_wt);
							tot_net_wt += parseFloat(val.net_wt);
							tot_dia_wt += parseFloat(val.tag_dia_wt);
							
                            html += 
                            '<tr>'+
                            '<td><input type="checkbox" name="tag_id[]" class="tag_id" value='+val.tag_id+'></td>'+
                            '<td><input type="hidden" name="tag_code[]" class="tag_code" value='+val.tag_code+'>'+val.tag_code+'</td>'+
                            '<td><input type="hidden" name="id_lot_inward_detail[]" class="id_lot_inward_detail" value='+val.lot_no+'></input>'+val.lot_no+'</td>'+
                            '<td><input type="hidden" name="product[]" class="product" value='+val.product+'>'+val.product+'</td>'+
                            '<td><input type="hidden" name="design[]" class="design" value='+val.design+'>'+val.design+'</td>'+
                            '<td><input type="hidden" name="tag_datetime[]" class="tag_datetime" value='+val.tag_datetime+'>'+val.tag_datetime+'</td>'+
                            '<td><input type="hidden" name="piece[]" class="piece" value='+val.piece+'>'+val.piece+'</td>'+
                            '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value='+val.gross_wt+'>'+val.gross_wt+'</td>'+
                            '<td><input type="hidden" name="net_wgt[]" class="net_wgt" value='+val.net_wt+'>'+val.net_wt+'</td>'+
							'<td><input type="hidden" name="dia_wgt[]" class="dia_wgt" value='+val.tag_dia_wt+'>'+val.tag_dia_wt+'</td>'+
							'<td><a href="#" onclick="remove_brn_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>'+
							'</tr>';
                        });
                        $(".tot_bt_pcs").html( 	(total_pcs));
                        $(".tot_bt_gross_wt").html(parseFloat(tot_gross_wt).toFixed(3));
						$(".tot_bt_net_wt").html(parseFloat(tot_net_wt).toFixed(3));
                        $(".tot_bt_dia_wt").html(parseFloat(tot_dia_wt).toFixed(3));
                        if($('#bt_search_list  > tbody > tr').length > 0 )
                        {
                        $('#bt_search_list > tbody > tr:first').before(html);
                        }
                        else
                        {
                        $('#bt_search_list > tbody').append(html);
                        }
                        $('#esti_no').val('');
					    $("#esti_no").focus();
                    }
                }   
                else
                {
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Record Found..'});
					$('#esti_no').val('');
					$("#esti_no").focus();
                } 
		  },
		  error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	 
	});
}
 
function add_to_trans(trans_data){
	console.log("trans_data : " , trans_data);
	$(".overlay").css("display", "block");		
	
	var postData = {};
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0); 
	var trans_type =  $("input[name='transfer_item_type']:checked").val();
	var from_brn = $("#from_brn").val(); 
	var to_brn =  ( isOtherIssue == 1? $('#other_issue_branch').val() : $("#to_brn").val());
	var is_eda = $('#is_eda').val();
	var form_secret = $("#form_secret").val(); 
	var remark = $('#remark').val();
	if(trans_type == 1){ 
		// Tagged
		grs_wt = $(".prev_grs_wt").val();
		net_wt = $(".prev_net_wt").val();
		pieces = $(".prev_pieces").val(); 
		postData = {'trans_data': trans_data,'transfer_from' : from_brn,'transfer_to' : to_brn,'item_tag_type' :trans_type, 'grs_wt' : grs_wt, 'net_wt':net_wt, 'pieces' : pieces, 'isOtherIssue' : isOtherIssue,'is_eda':1,'form_secret' :form_secret,'remark':remark}; 
	}
	else if(trans_type == 2){
		// Non Tagged
		pieces = $(".nt_pieces").val();
		grs_wt = $(".nt_grs_wt").val();
		net_wt = $(".nt_net_wt").val();	
		postData = {'trans_data': trans_data,'transfer_from' : from_brn,'transfer_to' : to_brn,'item_tag_type' :trans_type, 'isOtherIssue' : isOtherIssue,'is_eda':1,'nt_grs_wt' : grs_wt, 'nt_net_wt':net_wt, 'nt_pieces' : pieces,'form_secret' :form_secret,'remark':remark};
	}
	else if(trans_type == 3)	// Old Metal
	{
		grs_wt = $(".old_prev_grs_wt").val();
		net_wt = $(".old_prev_net_wt").val();
		postData = {'trans_data': trans_data,'transfer_from' : from_brn,'transfer_to' : 1,'item_tag_type' :trans_type,'grs_wt' : grs_wt, 'net_wt':net_wt,'is_eda':is_eda,'form_secret' :form_secret,'remark':remark};
	}
	else if(trans_type == 4) // Packaging Items
	{
	    var total_pcs=0;
	    $('#packaging_list > tbody  > tr').each(function(index, tr) {
            curRow = $(this);
            total_pcs+=parseFloat(curRow.find('.no_of_pcs').val());
	    });
	    postData = {'trans_data': trans_data,'transfer_from' : from_brn,'transfer_to' : to_brn,'item_tag_type' :trans_type,'pieces':total_pcs,'is_eda':1,'form_secret' :form_secret,'remark':remark};
	}
	else if(trans_type == 5){
		// Repair Order
		grs_wt = $(".cus_gwt").html();
		pieces = $(".total_items").html();
		postData = {'trans_data': trans_data,'transfer_from' : from_brn,'transfer_to' : to_brn,'item_tag_type' :trans_type, 'grs_wt' : grs_wt, 'pieces' : pieces, 'isOtherIssue' : isOtherIssue,'is_eda':1,'form_secret' :form_secret,'remark':remark};
	}
	$.ajax({		
	 	type: 'POST',		
	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/save',		
	 	dataType : 'json',		
	 	data : postData,
	 	success  : function(result){ 
	 		// Save and Print Branch Transfer Info
	 		if(result.status == 1){
				window.open( base_url+'index.php/admin_ret_brntransfer/branch_transfer/print/'+result.trans_code+'/'+result.s_type+'/'+2,'_blank');
			}
			window.location.reload();	 		
	     	$(".overlay").css("display", "none");			
	 	}	
	});
}   
 
function fnFormatRowDetails( oTable, nTr )
{
  var oData = oTable.fnGetData( nTr );
  var rowDetail = '';
  var prodTable = 
     '<div class="innerDetails">'+
      '<table class="table table-responsive table-bordered text-center table-sm">'+ 
        '<tr class="bg-teal">'+
        '<th>S.No</th>'+ 
        '<th>Product </th>'+
        '<th>No. of Tags </th>'+
        '<th></th>'+
        '<th>Pcs  </th>'+
        '<th>G.wt </th>'+
        '<th>N.wt </th>'+
        '</tr>';
    var prod = oData.prod; 
  $.each(prod, function (idx, val) {
  	var ref_no = val.id_prod+'_'+oData.branch_transfer_id;
  	var tagsTable =  
	        '<tr class="tagsTable collapsed bg-info '+ref_no+'"> '+	        
	        '<th></th>'+ 
	        '<th>S.No</th>'+ 
	        '<th>Tag Code </th>'+
	        '<th>Design </th>'+
	        '<th>Pcs  </th>'+
	        '<th>G.wt </th>'+
	        '<th>N.wt </th>'+
	        '</tr>';
  	$.each(val.tags, function (i, v) {
	  	tagsTable += 
	        '<tr class="tagsTable collapsed '+ref_no+'"> '+
	        '<td></td>'+
	        '<td>'+i+'</td>'+
	        '<td>'+v.tag_code+'</td>'+
	        '<td>'+v.design+'</td>'+
	        '<td>'+v.piece+'</td>'+
	        '<td>'+v.gross_wt+'</td>'+
	        '<td>'+v.net_wt+'</td>'+
	        '</tr><span>';
    })
  	prodTable += 
        '<tr class="prod_det_btn">'+
        '<td>'+idx+'</td>'+
        '<td>'+val.product+'</td>'+
        '<td>'+val.no_of_tags+'</td>'+
        '<td><input type="hidden" id="ref_no" value="'+ref_no+'"/><i class="fa fa-chevron-circle-down text-info open"></i><i class="fa fa-chevron-circle-up text-info close"></i></td>'+
        '<td>'+val.piece+'</td>'+
        '<td>'+val.gross_wt+'</td>'+
        '<td>'+val.net_wt+'</td>'+
        tagsTable+
        '</tr>'; 
  }); 
  rowDetail = prodTable+'</table></div>';
  return rowDetail;
}
function get_brantranTagged(from_brn, to_brn)
{
     $("#bt_approval_list > tbody > tr").remove();  
	$(".overlay").css("display", "block");	
	var approval_type = $("input[name='bt_approval_type']:checked").val();
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);
	var branch_trans_dnload = $('#branch_trans_dnload').val();
	if(approval_type==2)
	{
    	$.ajax({		
    	 	type: 'POST',		
    	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/download_pending',		
    	 	dataType : 'json',		
    	 	data : {'branch_trans_code': $("#bt_trans_code").val(),'id_design': $("#id_design").val(),'id_product': $("#id_product").val(),'lot_no': $("#lotno").val(),'from_branch' : from_brn,'to_branch' : to_brn,'item_tag_type' :1,'dt_range' :$("#dt_range").val(),'approval_type':approval_type,'is_other_issue':isOtherIssue} ,
    		success:function(data){
    		 	var list = data.list;
    		 	if(list.length > 0)
    			{
    				$("#btran_filter").prop('disabled', true);
    			}else{
    				$("#btran_filter").prop('disabled', false);
    				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'BT Not Available..'});
    			}
    			branch_download_by_scan(data);
    			
    	 	    if(data.access.edit == 0){
    				$(".status_blk").css("display", "none");		
    			} 
    			set_brantranTagged(data);
    	     	$("div.overlay").css("display", "none"); 
    	  	},
    	  	error:function(error)  
    	  	{
    			$("div.overlay").css("display", "none"); 
    	  	}	 
      	});
    }else
    {
        $.ajax({		
    	 	type: 'POST',		
    	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/approval_pending',		
    	 	dataType : 'json',		
    	 	data : {'branch_trans_code': $("#bt_trans_code").val(),'id_design': $("#id_design").val(),'id_product': $("#id_product").val(),'lot_no': $("#lotno").val(),'from_branch' : from_brn,'to_branch' : to_brn,'item_tag_type' :1,'dt_range' :$("#dt_range").val(),'approval_type':approval_type,'is_other_issue':isOtherIssue} ,
    		success:function(data){
    		 	var list = data.list;
    		 	if(list.length > 0)
    			{
    				$("#btran_filter").prop('disabled', true);
    			}else{
    				$("#btran_filter").prop('disabled', false);
    				$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'BT Not Available..'});
    			}
    		
    	 	    if(data.access.edit == 0){
    				$(".status_blk").css("display", "none");		
    			} 
    			set_brantranTagged(data);
    	     	$("div.overlay").css("display", "none"); 
    	  	},
    	  	error:function(error)  
    	  	{
    			$("div.overlay").css("display", "none"); 
    	  	}	 
      	});
    }
} 
function set_brantranTagged(data)
{
 	var list = data.list; 
 
	var oTable = $('#bt_approval_list').dataTable();
	oTable.fnDestroy();
	if (list!= null && list.length > 0 )
	{  	   
		oTable = $('#bt_approval_list').dataTable(  
			{
				'aaData': list,
				'bProcessing': true,
				"dom": 'lBfrtip',
				"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
				'aoColumns': [ 
							{ "mDataProp": function ( row, type, val, meta ){
							    var f_entry_date = row.f_entry_date; 
                                f_entry_date = f_entry_date.split("-");
                                var fb_entry_date = new Date( f_entry_date[0], f_entry_date[1] - 1, f_entry_date[2]);
                                
                                var t_entry_date = row.t_entry_date;
                                t_entry_date = t_entry_date.split("-");
                                var tb_entry_date = new Date( t_entry_date[0], t_entry_date[1] - 1, t_entry_date[2]);
                                
                                var approval_type = $("input[name='bt_approval_type']:checked").val();
			                    if( tb_entry_date.getTime() < fb_entry_date.getTime() && approval_type == 2){ // Donot allow Stock Download if to branch date is less than from branch
			                        return "<span style='color:red;'>Check Day Close</span>";
			                    }else{
								    return '<input type="checkbox" name="trans_id[]" class="trans_id" value="'+row.branch_transfer_id+'" disabled><input type="hidden" name="tag_id[]" class="tag_id" value="'+row.tag_id+'"><input type="hidden" name="is_other_issue[]" class="is_other_issue" value="'+row.is_other_issue+'">&nbsp;<input type="text" style="width: 90px;" class="entered_btcode form-control" name="entered_btcode" >';
							    }
			                }},  
			                {
				               "mDataProp": null,
				               "sClass": "control center", 
				               "sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
				            },
			                { "mDataProp": "lot_no" },
			                { "mDataProp": "branch_transfer_id" },
			                { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="from_branch" value="'+row.fb_id_branch+'">'+ row.from_branch;
			                }},
			                { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="to_branch" value="'+row.tb_id_branch+'">'+ row.to_branch;
			                }},
				            { "mDataProp": "no_of_prod" }, 
				            { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="t_pieces" value="'+row.piece+'">'+ row.piece;
			                }}, 
			                { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="branch_trans_code" value="'+row.branch_trans_code+'"><input type="hidden" class="t_gross_wt" value="'+row.gross_wt+'">'+ parseFloat(row.gross_wt).toFixed(3);
			                }}, 
			                { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="t_net_wt" value="'+row.net_wt+'">'+ parseFloat(row.net_wt).toFixed(3);
			                }}, 
			                { "mDataProp": function ( row, type, val, meta ){
			                	return "Yet To Approve";
			                }},
						],
				"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) { 
					if (aData['is_other_issue'] == 1) {
						$('td', nRow).css('color', 'Red');
					}
			    }
				
			}
		); 
		 
		// Filter Datatable columns
		$( '#f_transcode' ).on( 'keyup change', function () { 
			oTable.fnFilter( this.value, 0 );
        } );
        $( '#f_lot' ).on( 'keyup change', function () { 
			oTable.fnFilter( this.value, 2 );
        } );
        // Filter Datatable columns - Ends
        
		var anOpen =[]; 
		// For Product List
		$(document).on('click',"#bt_approval_list .control", function(){ 
		   var nTr = this.parentNode;
		   var i = $.inArray( nTr, anOpen );
		 
		   if ( i === -1 ) { 
				$('.drill-val', this).html('<i class="fa fa-chevron-circle-up text-teal"></i>'); 
				oTable.fnOpen( nTr, fnFormatRowDetails(oTable, nTr), 'details' );
				anOpen.push( nTr ); 
		    }
		    else { 
				$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
				oTable.fnClose( nTr );
				anOpen.splice( i, 1 );
		    }
		} );
		
		// For Tag List
		$(document).on('click',".prod_det_btn", function(){  
			var showRow = $('#ref_no',this).val(); 
			console.log(showRow);
			$('.'+showRow).toggleClass('collapsed'); 
		    $('i',this).toggleClass('open'); 
		    $('i',this).toggleClass('close'); 
		}); 
		
	    /*trHTML = '';  
		$.each(list, function (i, branch_trans) {
			$.each(branch_trans, function (idx, item) {
				if(idx == 0){
					td_first = '<td><input type="checkbox" name="trans_id[]" class="trans_id" value="'+item.branch_transfer_id+'"><input type="hidden" name="tag_id[]" class="tag_id" value="'+item.tag_id+'"> '+ item.branch_trans_code+'</td>';
				}else{
					td_first = '<td><input type="hidden" name="trans_id[]" class="trans_id" value="'+item.branch_transfer_id+'"><input type="hidden" name="tag_id[]" class="tag_id" value="'+item.tag_id+'"></td>';
				}
				trHTML += '<tr>' 
							+  td_first
							+'<td>'+item.from_branch+'</td>'
							+'<td>'+item.to_branch+'</td>'
							+'<td>'+item.lot_no+'</td>' 
							+'<td>'+item.tag_code+'</td>'
							+'<td>'+item.product+'</td>'
							+'<td>'+item.design+'</td>'
							+'<td><input type="hidden" class="t_pieces" value="'+item.piece+'">'+ item.piece+'</td>'
							+'<td><input type="hidden" class="t_gross_wt" value="'+item.gross_wt+'">'+ item.gross_wt+'</td>'
							+'<td><input type="hidden" class="t_net_wt" value="'+item.net_wt+'">'+ item.net_wt+'</td>'
							+'<td>Yet To Approve</td>'
			   			+'</tr>'; 
			});	  
		});    
	    $('#bt_approval_list > tbody').html(trHTML);  */
	}
		
}
 
function get_brantranNonTagged(from_brn,to_brn){
	
	$(".overlay").css("display", "block");	
	/*from_brn = $("#filter_from_brn").val();
	to_brn = $("#filtr_to_brn").val();*/
	var approval_type = $("input[name='bt_approval_type']:checked").val();
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0); 
	
	$.ajax({		
	 	type: 'POST',		
	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/approval_pending',		
	 	dataType : 'json',		
	 	data : {'branch_trans_code': $("#bt_trans_code").val(),'id_product': $("#id_product").val(),'lot_no': $("#lotno").val(),'from_branch' : from_brn,'to_branch' : to_brn,'item_tag_type' :2,'dt_range' :$("#dt_range").val(),'approval_type':approval_type,'is_other_issue':isOtherIssue} ,
	 	success  : function(data){ 
	 	    var list = data.list;	
	 	    if(data.access.edit == 0){
				$(".status_blk").css("display", "none");		
			}
	     	$(".overlay").css("display", "none");			
	     	var oTable = $('#bt_approval_list_nt').DataTable();
			 oTable.clear().draw();
			 if (list!= null && list.length > 0)
			 {  	
				oTable = $('#bt_approval_list_nt').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
						"dom": 'lBfrtip',
						"aaData"  : list,

						"columnDefs": [
							{
								targets: [1,2,3, 4],
								className: 'dt-body-left'
							},
							{
								targets: [5,6, 7],
								className: 'dt-body-right'
							},
						],



						"aoColumns": [	
						                { "mDataProp": function ( row, type, val, meta ){
            							    var f_entry_date = row.f_entry_date; 
                                            f_entry_date = f_entry_date.split("-");
                                            var fb_entry_date = new Date( f_entry_date[0], f_entry_date[1] - 1, f_entry_date[2]);
                                            
                                            var t_entry_date = row.t_entry_date;
                                            t_entry_date = t_entry_date.split("-");
                                            var tb_entry_date = new Date( t_entry_date[0], t_entry_date[1] - 1, t_entry_date[2]);
                                            
                                            var approval_type = $("input[name='bt_approval_type']:checked").val();
            
            			                    if( tb_entry_date.getTime() < fb_entry_date.getTime() && approval_type == 2){ // Donot allow Stock Download if to branch date is less than from branch
            			                        return "<span style='color:red;'>Check Day Close</span>";
            			                    }else{
            								    return '<input type="checkbox" name="trans_id[]" class="trans_id" value="'+row.branch_transfer_id+'" disabled><input type="hidden" name="tag_id[]" class="tag_id" value="'+row.tag_id+'"><input type="hidden" name="id_nontag_item[]" class="id_nontag_item" value="'+row.id_nontag_item+'"><input type="hidden" name="id_nontag_receipt[]" class="id_nontag_receipt" value="'+row.id_nontag_receipt+'"><input type="hidden" name="is_other_issue[]" class="is_other_issue" value="'+row.is_other_issue+'">&nbsp;<input type="text" style="width: 90px;" class="entered_btcode form-control" name="entered_btcode" >';
            							    }
            			                }},  
										{ "mDataProp": function ( row, type, val, meta ){
            			                	return '<input type="hidden" class="from_branch" value="'+row.fb_id_branch+'">'+ row.from_branch;
            			                }},
            			                { "mDataProp": function ( row, type, val, meta ){
            			                	return '<input type="hidden" class="to_branch" value="'+row.tb_id_branch+'">'+ row.to_branch;
            			                }},
										{ "mDataProp": "branch_transfer_id" },
										{ "mDataProp":function ( row, type, val, meta ){
											return 'Non Tagged';
										}},
										// {
										//  "mDataProp":function ( row, type, val, meta ){ 
										// 	return '<input type="hidden" class="id_section" value="'+row.id_section+'">'+ row.section_name;
										// }},
										
										// {
										//  "mDataProp":function ( row, type, val, meta ){ 
										// 	return '<input type="hidden" class="id_sub_design" value="'+row.id_sub_design+'"><input type="hidden" class="id_product" value="'+row.id_product+'"><input type="hidden" class="id_design" value="'+row.id_design+'">'+ row.product;
										// }},
										
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" class="branch_trans_code" value="'+row.branch_trans_code+'"><input type="hidden" class="nt_pieces" value="'+row.pieces+'">'+ row.pieces;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" class="nt_gross_wt" value="'+row.grs_wt+'">'+ row.grs_wt;
										}}
										,{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" class="nt_net_wt" value="'+row.net_wt+'">'+ row.net_wt;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return 'Yet To Approve';
										}},
										
									],
						"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) { 
							if (aData['is_other_issue'] == 1) {
								$('td', nRow).css('color', 'Red');
							}
					    }
					});			  	 	
				}  
	 	}	
	});
}
function brnTransUpdStatus(trans_ids,status,trans_type){
     $('#upd_status_btn').prop('disabled',true);
	var approval_type = $("input[name='bt_approval_type']:checked").val();
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0); 
	var form_secret = $("#form_secret").val(); 
	$(".overlay").css("display", "block");	 
	$.ajax({		
	 	type: 'POST',		
	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/updateStatus',		
	 	dataType : 'json',		
	 	data : {'trans_ids':trans_ids,'status':status,'approval_type':approval_type,'is_other_issue':isOtherIssue,'trans_type':trans_type,'form_secret' :form_secret},
	 	success  : function(data){
	 	    $('#upd_status_btn').prop('disabled',false);
	 		$(".alert-msg").html('');
			window.location.reload();
	     	$(".overlay").css("display", "none");			
	 	}	
	});
} 
/*function send_otp(){ 
    var approval_type =  $("input[name='bt_approval_type']:checked").val(); 
	var from_brn = (approval_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());
	var to_brn = ( isOtherIssue == 1? $('#other_issue_branch').val() :(approval_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val()));
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);   
	
	$(".overlay").css('display','block');  
	my_Date = new Date();
	$.ajax({
		url:base_url+ "index.php/admin_ret_brntransfer/send_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
		type:"POST",
		data:{"id_branch":(approval_type==1 ?from_brn :(isOtherIssue==1 ? $('#other_issue_branch').val():to_brn))},
		dataType: "json", 
		async:false,
		success:function(data){
			$(".overlay").css('display','none'); 
			if(data.status)
			{ 
				$("#otp_model").modal({
					backdrop: 'static',
					keyboard: false
					}); 
				var fewSeconds = 30;  
		   		$("#resend_otp").prop('disabled', true);
		   		timer = setTimeout(function(){
			        $("#resend_otp").prop('disabled', false); 
		    	}, fewSeconds*1000);
			} 
		},
		error:function(error)  
		{
			$(".overlay").css('display',"none"); 
		}	 
	 }); 
}*/ 
function send_otp(){ 
    var approval_type =  $("input[name='bt_approval_type']:checked").val(); 
	var from_brn = (approval_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());
	var to_brn = ( isOtherIssue == 1? $('#other_issue_branch').val() :(approval_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val()));
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0);   
	if(approval_type==2 && isOtherIssue==1 )
	{
        my_Date = new Date();
        $.ajax({
            url:base_url+ "index.php/admin_ret_brntransfer/send_other_issue_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
            type:"POST",
            dataType: "json", 
            async:false,
            success:function(data)
            {
                if(data.status)
                {
                   	$("#otp_model").modal({
    					backdrop: 'static',
    					keyboard: false
    					}); 
    				var fewSeconds = 30;  
    		   		$("#resend_otp").prop('disabled', true);
    		   		timer = setTimeout(function(){
    			        $("#resend_otp").prop('disabled', false); 
    		    	}, fewSeconds*1000);
                }
            },
            error:function(error)  
            {
            $(".overlay").css('display',"none"); 
            }	 
        });
	}
	else
	{
	    $(".overlay").css('display','block');  
    	my_Date = new Date();
    	$.ajax({
    		url:base_url+ "index.php/admin_ret_brntransfer/send_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
    		type:"POST",
    		data:{"id_branch": (isOtherIssue==1 ?  $('#other_issue_branch').val() : (approval_type==1 ? from_brn : to_brn ) ) },
    		dataType: "json", 
    		async:false,
    		success:function(data){
    			$(".overlay").css('display','none'); 
    			if(data.status)
    			{ 
    				$("#otp_model").modal({
    					backdrop: 'static',
    					keyboard: false
    					}); 
    				var fewSeconds = 30;  
    		   		$("#resend_otp").prop('disabled', true);
    		   		timer = setTimeout(function(){
    			        $("#resend_otp").prop('disabled', false); 
    		    	}, fewSeconds*1000);
    			} 
    		},
    		error:function(error)  
    		{
    			$(".overlay").css('display',"none"); 
    		}	 
    	 }); 
	}
}
function verify_otp(){   
	my_Date = new Date();
	$.ajax({
		url:base_url+ "index.php/admin_ret_brntransfer/verify_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
		data: {"otp":$("#otp").val()},
		type:"POST",
		dataType: "json", 
		async:false,
		success:function(data){
			if(data.status)
			{
				$('#otp').prop('disabled',true);
				$('#approve').prop('disabled',false);
				$('#verify_otp').prop('disabled',true);
				$('#resend_otp').prop('disabled',true);
				$(".otp_alert").append('<p style="color:green">'+data.msg+'</p>');
			}
			else
			{
				$('#approve').prop('disabled',true);
				$('#otp').prop('disabled',false);
				$('#verify_otp').prop('disabled',false);
				$(".otp_alert").append('<p style="color:red">'+data.msg+'</p>');
			} 
			setTimeout(function() {
				$('.otp_alert').css('display','none');
			},10000);
		},
		error:function(error)  
		{
			$(".overlay").css('display',"none"); 
		}	 
	 }); 
} 
function getBTBranches(){	 
 	$.ajax({		
     	type: 'GET',		
     	url: base_url+'index.php/admin_ret_brntransfer/bt_get_branches',		
     	dataType:'json',		
     	success:function(data){	
     		var id_branch = "";	
     		branchArr = data;
     		$(".from_branch,.to_branch,#tagged_branch").select2({			    
        	 	placeholder: "Select Branch",			    
        	 	allowClear: true		    
         	});	
         	$.each(data, function (key, item) { 
         	    if(loggedInBranch == item.id_branch)
                {
                    var loggedGst = item.gst_number; 
    				$('#logged_gst').val(loggedGst);                    
                }
         	});
    	  	$.each(data, function (key, item) { 
    	  		if(loggedInBranch > 0){
				 
	    	  		if(ctrl_page[2] == 'approval_list'){
	    	  			var bt_approval_type =  $("input[name='bt_approval_type']:checked").val(); 
	    	  			from_brn = (bt_approval_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());
			            to_brn = (bt_approval_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val());
						if(bt_approval_type == 1){ // Transit Approval  
							if(loggedInBranch != item.id_branch && (loggedGst==item.gst_number)){
					    	 	 $(".to_branch,.filter_to_brn").append(						
					        	 	$("<option></option>")						
					        	 	.attr("value", item.id_branch)						  						  
					        	 	.text(item.name )
					        	 	.attr("data-gst_number", item.gst_number)
					    	 	 ); 
						 	 }else{ 

            		    	    $(".from_branch").attr("disabled",true);
								if(item.id_branch != 1){
									$(".from_branch").append(					
									$("<option></option>")						
									.attr("value", item.id_branch)						  						  
									.text(item.name )
									.attr("data-gst_number", item.gst_number)
									); 
									}
								
            			 	    $(".from_branch").select2("val",from_brn);
            			 	 }
						 	 $(".filter_to_brn").select2('val','');
						 	 /*if(loggedInBranch == item.id_branch){			        	 	
					    	 	 $(".app_frm_brn").css("display","none");  
						 	} */
						}else{ // Stock Download 
				        	 if(loggedInBranch != item.id_branch){			        	 	
				        	 	 $(".from_branch").append(						
					        	 	$("<option></option>")						
					        	 	.attr("value", item.id_branch)						  						  
					        	 	.text(item.name )
					        	 	.attr("data-gst_number", item.gst_number)
				        	 	 ); 
			        	 	 }else{ 
                			 	 $(".filter_to_brn").append(						
                		        	 	$("<option></option>")						
                		        	 	.attr("value", item.id_branch)						  						  
                		        	 	.text(item.name )
                		        	 	.attr("data-gst_number", item.gst_number)
                		    	 );
            			 	    $(".filter_to_brn").attr("disabled",true); 
            			 	    $(".filter_to_brn").select2("val",to_brn);
			        	 	 }
			        	 	 /*if(loggedInBranch == item.id_branch){
			        	 	 	$(".app_to_brn").css("display","none");   
			        	 	 }  */			        	 	 
						}
					}else{
						var logged_gst = $('#logged_gst').val();
						if(loggedInBranch != item.id_branch){		  				  			   		
			        	 	$(".from_branch").append(						
				        	 	$("<option></option>")						
				        	 	.attr("value", item.id_branch)						  						  
				        	 	.text(item.name )
				        	 	.attr("data-gfrom_brnt_number", item.gst_number)
			        	 	);	
			        	 }	
			        	 if(loggedInBranch != item.id_branch && (logged_gst==item.gst_number)){	
							var tagged_brn_id = $('#tagged_branch').val();		        	 	
							$(".to_branch,.filter_to_brn,#tagged_branch").append(						
								$("<option></option>")						
								.attr("value", item.id_branch)						  						  
								.text(item.name )
								.attr("data-gst_number", item.gst_number)
							); 
		        	 	 } 
		        	 	 $(".from_branch,.to_branch").select2("val","");  
						  $('#tagged_branch').select2("val",(tagged_brn_id!='' && tagged_brn_id > 0 ? tagged_brn_id : '')); 
					}
					
				}else{
					var tagged_brn_id = $('#tagged_branch').val();
					$(".from_branch,.to_branch,#tagged_branch").append(						
		        	 	$("<option></option>")						
		        	 	.attr("value", item.id_branch)						  						  
		        	 	.text(item.name )
		        	 	.attr("data-gst_number", item.gst_number)
	        	 	);			
         			$(".from_branch,.to_branch").select2("val","");   
					if($('#tagged_branch').length)
					{
						$('#tagged_branch').select2("val",(tagged_brn_id!='' && tagged_brn_id > 0 ? tagged_brn_id : ''));
					}
				} 										
         	}); 
         //	$(".from_branch").select2("val",id_branch);    
     	}	
    }); 
}
$('#tagged_branch').on('change',function()
{
	if(this.value!='')
	{
		if(ctrl_page[1]=='branch_transfer' && ctrl_page[2]=='add')
		{
			$('.to_branch ').select2("val",this.value);
		}
		$('#bt_search_list > tbody tr').empty();
		$('.tot_bt_pcs').html(0);
		$('.tot_bt_gross_wt').html(0);
		$('.tot_bt_net_wt').html(0);
		$('.tot_bt_dia_wt').html(0);
		$('.tot_tag_count').html(0);
	}
})

if ($("input[name='branch_transfer_id[]']:checked").length > 0) {
    $("#bt_cancel").prop("disabled", false);
} else {
    $("#bt_cancel").prop("disabled", true);
}


$("#bt_cancel").on('click',function(){
    if($("input[name='branch_transfer_id[]']:checked").val())
    {
            var selected = [];
            var approve=false;
            $("#bt_list tbody tr").each(function(index, value)
            {
                if($(value).find("input[name='branch_transfer_id[]']:checked").is(":checked"))
                {
                    transData = { 
                        'branch_transfer_id'   : $(value).find(".branch_transfer_id").val(),
                    }
                selected.push(transData);	
                }
            })
            req_data = selected;
            update_branch_transfer_cancel(req_data);
    }
    else
    {
        alert('Please Select Any One Code.');
    }
});
function update_branch_transfer_cancel(data="")
    {
        my_Date = new Date();
        $("div.overlay").css("display", "block"); 
        $.ajax({
            url:base_url+ "index.php/admin_ret_brntransfer/update_branch_transfer_cancel?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
            data:  {'req_data':data},
            type:"POST",
            async:false,
            success:function(data)
            {
                location.reload(false);
                $("div.overlay").css("display", "none"); 
            },
            error:function(error)  
            {
                console.log(error);
                $("div.overlay").css("display", "none"); 
            }	 
        });
    }
function get_ajaxBranchTransferlist()
{
	$("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/ajax?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 type:"POST",
			 data:{'from_date':$('#from_date').text(),'to_date':$('#to_date').text()},
			 success:function(data){
			 	var list = data.list;
			 	var profile = data.profile;
			 	if(profile.allow_branch_transfer_cancel==1)
			 	{
			 	    $('#bt_cancel').css('display','block');
			 	}
				var oTable = $('#bt_list').DataTable();
				oTable.clear().draw();				  
				if (list!= null && list.length > 0)
				{  	
					oTable = $('#bt_list').dataTable({
						"bDestroy": true,
		                "bInfo": true,
		                "bFilter": true,
						"order": [[ 0, "desc" ]],
						"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
		                "scrollX":'100%',
		                "bSort": true,
		                "dom": 'lBfrtip',
						"aaData": list,
						"scrollY": '400px',
            			"scrollCollapse": true,
            			"paging": false,
						"aoColumns": [	
									    { "mDataProp": function ( row, type, val, meta ){ 
                		                	chekbox='<input type="checkbox"  name="branch_transfer_id[]" value="'+row.branch_transfer_id+'"/><input type="hidden" class="branch_transfer_id" value="'+row.branch_transfer_id+'">' 
                		                	if(profile.allow_branch_transfer_cancel==1 && row.status!=3 && row.status!=4)
                		                	{
                		                		return chekbox+" "+row.branch_trans_code;
                		                	}else{
                		                		return row.branch_trans_code;
                		                	}
                		                }},
										{ "mDataProp": "created_date" },
										{ "mDataProp": "item_type" },
										{ "mDataProp": "from_branch" },
										{ "mDataProp": "to_branch" },
										{ "mDataProp": "pieces" },
										{ "mDataProp": "grs_wt" },
										{ "mDataProp": "bt_status" },
										{ "mDataProp": function ( row, type, val, meta ) {
                                            id= row.branch_transfer_id;
                                            branch_trans_code= row.branch_trans_code;
                                            transfer_item_type= row.transfer_item_type;
                                            summary_url=base_url+'index.php/admin_ret_brntransfer/branch_transfer/print/'+branch_trans_code+'/'+transfer_item_type+'/'+1;
                                            detailed_url=base_url+'index.php/admin_ret_brntransfer/branch_transfer/print/'+branch_trans_code+'/'+transfer_item_type+'/'+2;
                                            action_content='<a href="'+summary_url+'" target="_blank" class="btn btn-info btn-print" data-toggle="tooltip" title="Summary Print"><i class="fa fa-print" ></i></a><a href="'+detailed_url+'" target="_blank" class="btn btn-primary btn-print" data-toggle="tooltip" title="Detailed Print"><i class="fa fa-print" ></i></a>';
                                            return action_content;
                                            }
                                        }
									],
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
//Other issue otp
$('#resend_other_issue_otp').on('click',function(){
    send_other_issue_otp();
});
 $('#other_issue_otp').on('input',function(){
    if(this.value.length==6)
    {        
          $('#verify_other_issue_otp').prop('disabled',false);
    }
    else
    {
            $('#verify_other_issue_otp').prop('disabled',true);
    }
});
        
function send_other_issue_otp()
{
	$('#tagResend').css('display','none');
	my_Date = new Date();
	$.ajax({
			url:base_url+ "index.php/admin_ret_brntransfer/send_other_issue_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
			type:"POST",
			dataType: "json", 
			async:false,
			success:function(data){
    	 	  	if(data.status)
    	 	  	{
    	 	  			$("#otp_model").modal({
    					backdrop: 'static',
    					keyboard: false
    					}); 
        				var fewSeconds = 30;  
        		   		$("#resend_otp").prop('disabled', true);
        		   		timer = setTimeout(function(){
        			        $("#resend_otp").prop('disabled', false); 
        		    	}, fewSeconds*1000);
    	 	  	}
			},
		  error:function(error)  
		  {
			 $(".overlay").css('display',"none"); 
		  }	 
		  });
}
$('#verify_other_issue_otp').on('click',function(){
        $("#verify_oi_otp").attr("disabled", true);
        verify_otherissue_otp();
});
function verify_otherissue_otp(){   
    my_Date = new Date();
    $.ajax({
        url:base_url+ "index.php/admin_ret_brntransfer/verify_other_issue_otp?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
        data: {"otp":$("#other_issue_otp").val()},
        type:"POST",
        dataType: "json", 
        async:false,
        success:function(data){
           if(data.status)
			{
				$('#other_issue_otp').prop('disabled',true);
				$('#verify_other_issue_otp').prop('disabled',true);
				$('#approve_other_issue').prop('disabled',false);
				
				$(".otp_alert").append('<p style="color:green">'+data.msg+'</p>');
			}
			else
			{
				$('#other_issue_otp').prop('disabled',false);
				$('#verify_other_issue_otp').prop('disabled',false);
				$('#approve_other_issue').prop('disabled',true);
				$(".otp_alert").append('<p style="color:red">'+data.msg+'</p>');
			} 
			setTimeout(function() {
				$('.otp_alert').css('display','none');
			},10000);
        },
        error:function(error)  
        {
            $(".overlay").css('display',"none"); 
        }    
     }); 
}
$("#approve_other_issue").on('click',function()
{
        trans_type =  $("input[name='transfer_item_type']:checked").val(); 
                $(".overlay").css("display", "block");
                if(trans_type == 1)
                { // Tagged
                    if($("input[name='tag_id[]']:checked").val())
                    {
                        var tagged_data = [];
                        $("#bt_list > tbody tr").each(function() { 
                            var row = $(this).closest('tr'); 
                            tagged_data.push({"tag_id" : row.find('td:first .tag_id').val(), "id_lot_inward_detail" : row.find('td:eq(2) .id_lot_inward_detail').val()});
                        });
                        if(tagged_data.length > 0) 
                        add_to_trans(tagged_data);
                    }
                }
});
//Other issue otp
// old Metal Transfer Details
$('#old_metal_select_all').click(function(event) {
	  $("#old_metal_list tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
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
$(document).on('change',".sales_ret_items_silver", function(){
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='sales_ret_items_silver')
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='sales_ret_items_silver')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
    set_purchase_items_preview();
});
$(document).on('change',".sales_ret_items_gold", function(){
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='sales_ret_items_gold')
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='sales_ret_items_gold')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
    set_purchase_items_preview();
});
$(document).on('change',".partly_sale_silver", function(){
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='partly_sale_silver')
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='partly_sale_silver')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
    set_purchase_items_preview();
});
$(document).on('change',".partly_sale_gold", function(){
   	var trans_id=this.value;
	
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='partly_sale_gold')
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='partly_sale_gold')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
    set_purchase_items_preview();
});
$(document).on('change',".old_metal_silver", function(){
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='old_metal_silver')
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='old_metal_silver')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
    set_purchase_items_preview();
});
$(document).on('change',".old_metal_gold", function(){
    if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='old_metal_gold')
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
    else
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.item_type=='old_metal_gold')
                {
                    oldMetalDetail[key].is_checked=0;
                }
        });
    }
    calculate_old_metal();
    set_purchase_items_preview();
});
$(document).on('change',".old_metal_sale_id", function(){
	var id_old_metal=this.value;
	if($(this).is(":checked"))
	{
		$.each(oldMetalDetail,function(key,items){
			if(items.old_metal_sale_id==id_old_metal)
			{
			    oldMetalDetail[key].is_checked=1;
			}
		});
	}
	else
	{
		$.each(oldMetalDetail,function(key,items){
			if(items.old_metal_sale_id==id_old_metal)
			{
				oldMetalDetail[key].is_checked=0;
			}
		});
	}
	calculate_old_metal();
	set_purchase_items_preview();
});
$(document).on('change',".partial_sale_id", function(){
	var trans_id=this.value;
	if($(this).is(":checked"))
	{
		$.each(oldMetalDetail,function(key,items){
			if(items.trans_id==trans_id)
			{
			    oldMetalDetail[key].is_checked=1;
			}
		});
	}
	else
	{
		$.each(oldMetalDetail,function(key,items){
			if(items.trans_id==trans_id)
			{
				oldMetalDetail[key].is_checked=0;
			}
		});
	}
	calculate_old_metal();
	set_purchase_items_preview();
});
$(document).on('change',".sales_ret_tag_id", function(){
	var trans_id=this.value;
	console.log(oldMetalDetail);
	if($(this).is(":checked"))
	{
		$.each(oldMetalDetail,function(key,items){
			if(items.trans_id==trans_id)
			{
			    oldMetalDetail[key].is_checked=1;
			}
		});
	}
	else
	{
		$.each(oldMetalDetail,function(key,items){
			if(items.trans_id==trans_id)
			{
				oldMetalDetail[key].is_checked=0;
			}
		});
	}
	calculate_old_metal();
	set_purchase_items_preview();
});
$(document).on('change',".old_metal_items", function(){
	 if($(this).is(":checked"))
    {
        $.each(oldMetalDetail,function(key,items){
                if(items.type=='old_metal_items')
                {
                    oldMetalDetail[key].is_checked=1;
                }
                
        });
    }
	calculate_old_metal();
	set_purchase_items_preview();
});
$(document).on('change',".oldmetalbtselect", function(){
    calculate_old_metal();
    set_purchase_items_preview();
});
function calculate_old_metal()
{
	
	var total_gwt=0;
	var total_nwt=0;
	var total_amt=0;
	var total_dwt=0;
	
	$.each(oldMetalDetail,function(key,items){
		if(items.is_checked==1)
		{
			total_gwt+=parseFloat(items.gross_wt);
			total_nwt+=parseFloat(items.net_wt);
			total_dwt+=parseFloat(items.dia_wt);
			total_amt+=parseFloat(items.amount);
		}
	});
	$(".old_prev_grs_wt").val(parseFloat(total_gwt).toFixed(3));
    $(".old_prev_net_wt").val(parseFloat(total_nwt).toFixed(3));
	$(".old_prev_dia_wt").val(parseFloat(total_dwt).toFixed(3));
    $(".old_prev_amt").val(parseFloat(total_amt).toFixed(3));
}
function get_purchase_items()
{
    oldMetalDetail=[];
	$("div.overlay").css("display", "block"); 
	my_Date = new Date();
	$.ajax({
			 url:base_url+ "index.php/admin_ret_brntransfer/get_purchase_items?nocache=" + my_Date.getUTCSeconds(),
			 dataType:"JSON",
			 type:"POST",
			 data:{'from_branch':$('#from_brn').val(),'from_date':$('#rpt_payments1').text(),'to_date':$('#rpt_payments2').text(),'bill_type':$('#is_eda').val()},
			 success:function(data){
			   $(".overlay").css("display", "none");
			   var oTable = $('#old_metal_list').DataTable();
			   oTable.clear().draw();
			   if (data!= null && data.length > 0)
			   {  	
				
					$.each(data,function(key,val){
						$.each(val.bill_det,function(k,items){
							oldMetalDetail.push(items);
						});
					});
				  console.log(oldMetalDetail);
				  oTable = $('#old_metal_list').dataTable({
						  "bDestroy": true,
						  "bInfo": true,
						  "bFilter": true,
						  "bSort": false,
						  "order": [[ 0, "desc" ]],
						  "dom": 'lBfrtip',			
						  "aaData"  : data,
						  "aoColumns": [
						                
						                  { "mDataProp": function ( row, type, val, meta )
                                          { 
                                                return '<input type="checkbox" class="'+row.type+' oldmetalbtselect" name="id_old_metal_type[]"/>';
                                          }},
							             
										  { "mDataProp":function ( row, type, val, meta ){
											return row.metal_type;
										  }},
										  { "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="gross_wt[]" class="gross_wt" value="'+row.gross_wt+'">'+ row.gross_wt;
										  }},
										  { "mDataProp":function ( row, type, val, meta ){
											  return '<input type="hidden" name="net_wgt[]" class="net_wgt" value="'+row.net_wt+'">'+ row.net_wt;
										  }} ,
										  { "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="dia_wt[]" class="dia_wgt" value="'+row.dia_wt+'">'+ (parseFloat(row.dia_wt).toFixed(3));
										  }} ,
										 
										  { "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" name="rate[]" class="rate" value="'+row.rate+'">'+ row.rate;
										  }},
										  {
											"mDataProp": null,
											"sClass": "control center", 
											"sDefaultContent":  '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
											},
					   
									  ]
					  });	
					  
					var anOpen =[]; 
            		$(document).on('click',"#old_metal_list .control", function(){ 
            		   var nTr = this.parentNode;
            		   var i = $.inArray( nTr, anOpen );
            		 
            		   if ( i === -1 ) { 
            				$('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>'); 
            				oTable.fnOpen( nTr, fnFormatRowBillDetails(oTable, nTr), 'details' );
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
				  
			},
			error:function(error)  
			{
			   $("div.overlay").css("display", "none"); 
			}
	      });
}
function set_purchase_items_preview()
{
     $('#purchase_item_preview tbody').html('');
    var total_old_gold_gwt=0;
    var total_old_gold_nwt=0;
    var total_old_gold_amount=0;
    
    var total_partly_sale_gold_gwt=0;
    var total_partly_sale_gold_nwt=0;
    var total_partly_sale_gold_amount=0;
    
    var total_partly_sale_silver_gwt=0;
    var total_partly_sale_silver_nwt=0;
    var total_partly_sale_silver_amount=0;
    
    var total_old_silver_gwt=0;
    var total_old_silver_nwt=0;
    var total_old_silver_amount=0;
    var total_sales_ret_gold_gwt=0;
    var total_sales_ret_gold_nwt=0;
    var total_sales_ret_silver_gwt=0;
    var total_sales_ret_silver_nwt=0;
    $.each(oldMetalDetail,function(key,items){
        if(items.is_checked==1)
        {
            if(items.item_type=='sales_ret_items_gold')
            {
                total_sales_ret_gold_gwt+=parseFloat(items.gross_wt);
                total_sales_ret_gold_nwt+=parseFloat(items.net_wt);
            }
            
            if(items.item_type=='sales_ret_items_silver')
            {
                total_sales_ret_silver_gwt+=parseFloat(items.gross_wt);
                total_sales_ret_silver_nwt+=parseFloat(items.net_wt);
            }
            
            if(items.item_type=='old_metal_gold')
            {
                total_old_gold_gwt+=parseFloat(items.gross_wt);
                total_old_gold_nwt+=parseFloat(items.net_wt);
                total_old_gold_amount+=parseFloat(items.amount);
            }
            
            if(items.item_type=='old_metal_silver')
            {
                total_old_silver_gwt+=parseFloat(items.gross_wt);
                total_old_silver_nwt+=parseFloat(items.net_wt);
                total_old_silver_amount+=parseFloat(items.amount);
            }
            
            if(items.item_type=='partly_sale_gold')
            {
                total_partly_sale_gold_gwt+=parseFloat(items.gross_wt);
                total_partly_sale_gold_nwt+=parseFloat(items.net_wt);
                total_partly_sale_gold_amount+=parseFloat(items.amount);
            }
            
            if(items.item_type=='partly_sale_silver')
            {
                total_partly_sale_silver_gwt+=parseFloat(items.gross_wt);
                total_partly_sale_silver_nwt+=parseFloat(items.net_wt);
                total_partly_sale_silver_amount+=parseFloat(items.amount);
            }
        }
    });
    
    var trHtml='';
    
    trHtml+='<tr>'
            +'<td>OLD GOLD</td>'
            +'<td>'+parseFloat(total_old_gold_gwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_old_gold_nwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_old_gold_amount).toFixed(2)+'</td>'
            +'</tr>';
    
     trHtml+='<tr>'
            +'<td>OLD SILVER</td>'
            +'<td>'+parseFloat(total_old_silver_gwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_old_silver_nwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_old_silver_amount).toFixed(2)+'</td>'
            +'</tr>';
            
    
    trHtml+='<tr>'
            +'<td>SALES RETURN-GOLD</td>'
            +'<td>'+parseFloat(total_sales_ret_gold_gwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_sales_ret_gold_nwt).toFixed(3)+'</td>'
            +'</tr>';
            
    trHtml+='<tr>'
            +'<td>SALES RETURN-SILVER</td>'
            +'<td>'+parseFloat(total_sales_ret_silver_gwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_sales_ret_silver_nwt).toFixed(3)+'</td>'
            +'</tr>';
            
            
     trHtml+='<tr>'
            +'<td>PARTLY SALE -GOLD</td>'
            +'<td>'+parseFloat(total_partly_sale_gold_gwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_partly_sale_gold_nwt).toFixed(3)+'</td>'
            +'</tr>';
            
    trHtml+='<tr>'
            +'<td>PARTLY SALE -SILVER</td>'
            +'<td>'+parseFloat(total_sales_ret_silver_gwt).toFixed(3)+'</td>'
            +'<td>'+parseFloat(total_sales_ret_silver_nwt).toFixed(3)+'</td>'
            +'</tr>';
            
    $('#purchase_item_preview tbody').append(trHtml);
}
function fnFormatRowBillDetails( oTable, nTr )
{
  var oData = oTable.fnGetData( nTr );
  var rowDetail = '';
  var prodTable = 
     '<div class="innerDetails">'+
      '<table class="table table-responsive table-bordered text-center table-sm" id="old_metal_bill_details">'+ 
        '<tr class="bg-teal">'+
        '<th>S.No</th>'+ 
		'<th>Bill Date</th>'+
        '<th>Bill No</th>'+
        '<th>G.Wt</th>'+
		'<th>N.Wt</th>'+
		'<th>Dia.Wt</th>'+
		'<th>Amount</th>'+
        '</tr>';
    var bill_det = oData.bill_det; 
    var total_gwt=0;
    var total_nwt=0;
	var total_dwt=0;
    var total_amt=0;
  $.each(bill_det, function (idx, val) {
      var is_checked=0;
        	// $.each(oldMetalDetail,function(key,items){
        	//     if(val.trans_id==items.trans_id)
        	//     {
        	//         is_checked=items.is_checked;
        	       
        	//     }
        	// });
  	prodTable += 
        '<tr class="prod_det_btn">'+
        '<td><input type="checkbox" class="'+(val.type=='old_metal_items' ? 'old_metal_sale_id' : (val.item_type.indexOf('sales_ret_items_') !== -1  ? 'sales_ret_tag_id' : (val.item_type.indexOf('partly_sale_')!== -1 ? 'partial_sale_id' :'')) )+'" name="trans_id[]" value="'+val.trans_id+'" '+(is_checked==1 ? 'checked' :'')+' >'+val.trans_id+'</td>'+
        '<td>'+val.bill_date+'</td>'+
        '<td>'+val.bill_no+'</td>'+
        '<td><input type="hidden" class="gross_wt" value="'+val.gross_wt+'">'+val.gross_wt+'</td>'+
        '<td><input type="hidden" class="net_wt" value="'+val.net_wt+'">'+val.net_wt+'</td>'+
		'<td><input type="hidden" class="dia_wt" value="'+val.dia_wt+'">'+val.dia_wt+'</td>'+
        '<td><input type="hidden" class="rate" value="'+val.amount+'">'+val.amount+'</td>'+
        '</tr>'; 
		total_gwt+=parseFloat(val.gross_wt);
        total_nwt+=parseFloat(val.net_wt);
		total_dwt+=parseFloat(val.dia_wt);
        total_amt+=parseFloat(val.amount);
  }); 
  prodTable += 
        '<tr class="prod_det_btn" style="font-weight: bold;">'+
        '<td colspan="3">TOTAL</td>'+
        '<td>'+parseFloat(total_gwt).toFixed(3)+'</td>'+
        '<td>'+parseFloat(total_nwt).toFixed(3)+'</td>'+
		'<td>'+parseFloat(total_dwt).toFixed(3)+'</td>'+
        '<td>'+parseFloat(total_amt).toFixed(3)+'</td>'+
        '</tr>'; 
  rowDetail = prodTable+'</table></div>';
  return rowDetail;
}
function get_brantranOldMetal(from_brn,to_brn){
	
	$(".overlay").css("display", "block");	
	
	var approval_type = $("input[name='bt_approval_type']:checked").val();
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0); 
	
	$.ajax({		
	 	type: 'POST',		
	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/approval_pending',		
	 	dataType : 'json',		
	 	data : {'branch_trans_code': $("#bt_trans_code").val(),'id_product': $("#id_product").val(),'lot_no': $("#lotno").val(),'from_branch' : from_brn,'to_branch' : to_brn,'item_tag_type' :3,'dt_range' :$("#dt_range").val(),'approval_type':approval_type,'is_other_issue':isOtherIssue,'is_eda_appr':$('#is_eda_appr').val()} ,
	 	success  : function(data){ 
	 	    var list = data.list;
	 	    if(data.access.edit == 0){
				$(".status_blk").css("display", "none");		
			}
	     	$(".overlay").css("display", "none");			
	     	var oTable = $('#bt_approval_list_old_metal').DataTable();
			 oTable.clear().draw();
			 if (list!= null && list.length > 0)
			 {  	
				oTable = $('#bt_approval_list_old_metal').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
						"dom": 'lBfrtip',
						"aaData"  : list,
						"aoColumns": [	
						                { "mDataProp": function ( row, type, val, meta ){
            							    var f_entry_date = row.f_entry_date; 
                                            f_entry_date = f_entry_date.split("-");
                                            var fb_entry_date = new Date( f_entry_date[0], f_entry_date[1] - 1, f_entry_date[2]);
                                            
                                            var t_entry_date = row.t_entry_date;
                                            t_entry_date = t_entry_date.split("-");
                                            var tb_entry_date = new Date( t_entry_date[0], t_entry_date[1] - 1, t_entry_date[2]);
                                            
                                            var approval_type = $("input[name='bt_approval_type']:checked").val();
            
            			                    if( tb_entry_date.getTime() < fb_entry_date.getTime() && approval_type == 2){ // Donot allow Stock Download if to branch date is less than from branch
            			                        return "<span style='color:red;'>Check Day Close</span>";
            			                    }else{
            								    return '<input type="checkbox" name="trans_id[]" class="trans_id" value="'+row.branch_transfer_id+'" disabled><input type="hidden" name="old_metal_sale_id[]" class="old_metal_sale_id" value="'+row.old_metal_sale_id+'">&nbsp;<input type="text" style="width: 90px;" class="entered_btcode form-control" name="entered_btcode" >';
            							    }
            			                }},  
										{ "mDataProp": function ( row, type, val, meta ){
            			                	return '<input type="hidden" class="from_branch" value="'+row.fb_id_branch+'">'+ row.from_branch;
            			                }},
            			                { "mDataProp": function ( row, type, val, meta ){
            			                	return '<input type="hidden" class="to_branch" value="'+row.tb_id_branch+'">'+ row.to_branch;
            			                }},
										{ "mDataProp": "branch_transfer_id" },
										{ "mDataProp":function ( row, type, val, meta ){
											return 'PURCHASE ITEMS';
										}},
										
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" class="branch_trans_code" value="'+row.branch_trans_code+'"><input type="hidden" class="old_metal_gross_wt" value="'+row.gross_wt+'">'+ row.gross_wt;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" class="old_metal_net_wt" value="'+row.net_wt+'">'+ row.net_wt;
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											return 'Yet To Approve';
										}},
										
									],
						"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) { 
							if (aData['is_other_issue'] == 1) {
								$('td', nRow).css('color', 'Red');
							}
					    }
					});			  	 	
				}  
	 	}	
	});
}
// old Metal Transfer Details
//Packaging Items
function get_invnetory_item()
{
    $('#select_item option').remove();
    $.ajax({
    type: 'POST',
    url: base_url+'index.php/admin_ret_other_inventory/get_invnetory_item',
    dataType:'json',
    data:{"id_branch":$('#from_brn').val()},
    success:function(data){
    other_inventory_item=data;
    var id=$('#select_item').val();
        $.each(data, function (key, val) {
            $('#select_item').append(
            $("<option></option>")
            .attr("value", val.id_other_item)
            .text(val.item_name)
            );
        });
        
        $("#select_item").select2({
            placeholder: "Select Item",
            allowClear: true
         });
         
        $("#select_item").select2("val", (id!=null? id :''));
        
    }
    });
}
$('#packaging_no_of_pcs').on('keyup',function(){
        var id_other_item   = $("#select_item").val();
        var no_of_pcs       = $('#packaging_no_of_pcs').val();
        $.each(other_inventory_item,function(key,items){
            if(items.id_other_item==id_other_item)
            {
                var available_pcs = items.tot_pcs;
                if(parseFloat(available_pcs)<no_of_pcs)
                {
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Available Pieces is "+items.tot_pcs});
                    $('#packaging_no_of_pcs').val('');
                }
            }
    });
});
$('#btn_add_pack_item').on('click',function(){
    var trHtml='';
    allow_submmit=true;
    if($("#select_item").val()=='' || $("#select_item").val()==null)
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Select Item"});
        allow_submmit=false;
    }
    else if($("#packaging_no_of_pcs").val()==0 || $("#packaging_no_of_pcs").val()=='')
    {
        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Please Enter The Pieces"});
        allow_submmit=false;
    }
    else if($('#packaging_list > tbody').length>0)
    {
        $('#packaging_list > tbody  > tr').each(function(index, tr) {
            curRow = $(this);
            if(curRow.find('.id_other_item').val()==$("#select_item").val())
            {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Item Already Exists.."});
                allow_submmit=false;
                return true;
            }
            else
            {
                allow_submmit=true;
            }
        });
    }
    if(allow_submmit)
    {
        trHtml+='<tr>'
            +'<td><input type="hidden" class="id_other_item" value="'+$("#select_item").val()+'">'+$("#select_item option:selected").text()+'</select></td>'
            +'<td><input type="hidden" class="form-control no_of_pcs" value="'+$("#packaging_no_of_pcs").val()+'">'+$("#packaging_no_of_pcs").val()+'</td>'
            +'<td><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
            +'</tr>';
    }
    
    
    if($('#packaging_list > tbody  > tr').length>0)
	{
	    $('#packaging_list > tbody > tr:first').before(trHtml);
	}else{
	    $('#packaging_list tbody').append(trHtml);
	}
	
});

$(document).on('change', "#select_item", function () {
	var item_id = this.value;
	var available_pcs = 0;
	console.log(other_inventory_item);
	$.each(other_inventory_item, function (key, items) {
		if (item_id == items.id_other_item) {
		available_pcs += parseInt(parseInt(items.tot_pcs)-parseInt(items.brch_pcs));
	  }
	  });
	$(".available_tot_pcs").html(available_pcs);
});
function get_brantranPackagingItems(from_brn,to_brn){
	
	$(".overlay").css("display", "block");	
	
	var approval_type = $("input[name='bt_approval_type']:checked").val();
	var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0); 
	
	$.ajax({		
	 	type: 'POST',		
	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/approval_pending',		
	 	dataType : 'json',		
	 	data : {'branch_trans_code': $("#bt_trans_code").val(),'id_product': $("#id_product").val(),'lot_no': $("#lotno").val(),'from_branch' : from_brn,'to_branch' : to_brn,'item_tag_type' :4,'dt_range' :$("#dt_range").val(),'approval_type':approval_type,'is_other_issue':isOtherIssue} ,
	 	success  : function(data){ 
	 	    var list = data.list;
	 	    if(data.access.edit == 0){
				$(".status_blk").css("display", "none");		
			}
	     	$(".overlay").css("display", "none");			
	     	var oTable = $('#bt_approval_list_packaging').DataTable();
			 oTable.clear().draw();
			 if (list!= null && list.length > 0)
			 {  	
				oTable = $('#bt_approval_list_packaging').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
						"dom": 'lBfrtip',
						"aaData"  : list,
						"aoColumns": [	
						                { "mDataProp": function ( row, type, val, meta ){
            							    var f_entry_date = row.f_entry_date; 
                                            f_entry_date = f_entry_date.split("-");
                                            var fb_entry_date = new Date( f_entry_date[0], f_entry_date[1] - 1, f_entry_date[2]);
                                            
                                            var t_entry_date = row.t_entry_date;
                                            t_entry_date = t_entry_date.split("-");
                                            var tb_entry_date = new Date( t_entry_date[0], t_entry_date[1] - 1, t_entry_date[2]);
                                            
                                            var approval_type = $("input[name='bt_approval_type']:checked").val();
            
            			                    if( tb_entry_date.getTime() < fb_entry_date.getTime() && approval_type == 2){ // Donot allow Stock Download if to branch date is less than from branch
            			                        return "<span style='color:red;'>Check Day Close</span>";
            			                    }else{
            								    return '<input type="checkbox" name="trans_id[]" class="trans_id" value="'+row.branch_transfer_id+'" disabled><input type="hidden" name="old_metal_sale_id[]" class="old_metal_sale_id" value="'+row.old_metal_sale_id+'">&nbsp;<input type="text" style="width: 90px;" class="entered_btcode form-control" name="entered_btcode" >';
            							    }
            			                }},  
										{ "mDataProp": function ( row, type, val, meta ){
            			                	return '<input type="hidden" class="from_branch" value="'+row.fb_id_branch+'">'+ row.from_branch;
            			                }},
            			                { "mDataProp": function ( row, type, val, meta ){
            			                	return '<input type="hidden" class="to_branch" value="'+row.tb_id_branch+'">'+ row.to_branch;
            			                }},
										{ "mDataProp": "branch_transfer_id" },
										
										{ "mDataProp":function ( row, type, val, meta ){
											return 'PACKAGING ITEMS';
										}},
										
										{ "mDataProp":function ( row, type, val, meta ){
											return '<input type="hidden" class="branch_trans_code" value="'+row.branch_trans_code+'">'+ row.pieces;
										}},
										
									],
						"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) { 
							if (aData['is_other_issue'] == 1) {
								$('td', nRow).css('color', 'Red');
							}
					    }
					});			  	 	
				}  
	 	}	
	});
}
//Packaging Items
//Repair Orders
function getRepairOrderDetails(){ 
	my_Date = new Date();
	var order_no = ($("#order_no").val() != ""?$("#order_no").val():'');
	$.ajax({
		 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getRepairOrderDetails?nocache=" + my_Date.getUTCSeconds(),
		 data: {'order_no':order_no,'from_brn':$("#from_brn").val()},  
		 dataType:"JSON",
		 type:"POST",
		 cache:false,
		 success:function(data){  
		 	 if(data.length>0)
		 	 {
		 	 	create_new_empty_order_detail_row(data);
		 	 }
			 else{
			    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});
				$('#order_list tbody').empty();
				$('#order_list tfoot').empty();
			  }
		 	 $("div.overlay").css("display", "none"); 
			
		  },
		  error:function(error)  
		  {
			 $("div.overlay").css("display", "none"); 
		  }	 
	});
}
function create_new_empty_order_detail_row(data)
{
	var html='';
	rowExist = false;
	$.each(data,function(key,items){
		
		$('#order_list > tbody tr').each(function(bidx, brow){
			order_row = $(this);
			if(order_row.find('.id_orderdetails').val() != '')
			{
				if(items.id_orderdetails == order_row.find('.id_orderdetails').val())
				{
					rowExist = true; 
					$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Order Details Already Exists..'});
				} 
			}
		});
		if(!rowExist)
		{
			html+='<tr>'+
				'<td><input type="checkbox" name="id_orderdetails[]" class="id_orderdetails" value="'+items.id_orderdetails+'">'+items.id_orderdetails+'</td>'+
				'<td class="order_no">'+items.orderno+'</td>'+
				'<td class="tag_code">'+items.tag_code+'</td>'+
				'<td class="section_name">'+items.section_name+'</td>'+
				'<td class="product_name">'+items.product_name+'</td>'+
				'<td class="design_name">'+items.design_name+'</td>'+
				'<td class="order_weight gross_wt">'+items.gross_wt+'</td>'+
				'<td class="less_wt">'+items.less_wt+'</td>'+
				'<td class="net_wt">'+items.net_wt+'</td>'+
				'<td class="dia_wt">'+items.dia_wt+'</td>'+
				'<td class="totalitems">'+items.totalitems+'</td>'+
				'<td><a href="#"onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
			   '</tr>';
		}
	});
	$('#order_list tbody').append(html);
	calculateOrdertotal();
}
function remove_row(curRow)
{
	curRow.remove();
	calculateOrdertotal();
}
$(document).on('click',".id_orderdetails,input[name='select_all']", function(){
	calculateOrdertotal();
}) 
function calculateOrdertotal()
{
	var pieces = 0;
	var grs_wt = 0;
	var net_wt = 0;
	var dia_wt = 0;
	var less_wt = 0;
	$("#order_list tbody input[type=checkbox]:checked").each(function () {
		var row = $(this).closest('tr'); 
		console.log(row);
		pieces = pieces + parseFloat(row.find('.totalitems').html()); 
		grs_wt = grs_wt + parseFloat(row.find('.gross_wt').html());
		less_wt = less_wt + parseFloat(row.find('.less_wt').html()); 
		net_wt = net_wt + parseFloat(row.find('.net_wt').html());
		dia_wt = dia_wt + parseFloat(row.find('.dia_wt').html()); 
		
	});
	$(".total_items").html(pieces);
	$(".cus_gwt").html(parseFloat(grs_wt).toFixed(3));
	$(".cus_nwt").html(parseFloat(net_wt).toFixed(3));
	$(".cus_lwt").html(parseFloat(less_wt).toFixed(3));
	$(".cus_dwt").html(parseFloat(dia_wt).toFixed(3));
}
$('#order_select_all').click(function(event) {
	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
	// event.stopPropagation();
});
$("#appr_sel_all_orders").click(function(event) {
	$("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
	event.stopPropagation();  
});
function get_branchtranOrderDetails(from_brn, to_brn)
{
    $("#bt_approval_list_orders > tbody > tr").remove();  
	$(".overlay").css("display", "block");	
	var approval_type = $("input[name='bt_approval_type']:checked").val();
	
	$.ajax({		
	 	type: 'POST',		
	 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/approval_pending',		
	 	dataType : 'json',		
	 	data : {'branch_trans_code': $("#bt_trans_code").val(),'from_branch' : from_brn,'to_branch' : to_brn,'item_tag_type' :5,'approval_type':approval_type} ,
		success:function(data){
		 	var list = data.list;
	 	    if(data.access.edit == 0){
				$(".status_blk").css("display", "none");		
			}
			set_brantranOrders(data);
	     	$("div.overlay").css("display", "none"); 
	  	},
	  	error:function(error)  
	  	{
			$("div.overlay").css("display", "none"); 
	  	}	 
  	});
} 
function set_brantranOrders(data)
{
 	var list = data.list; 
 
	var oTable = $('#bt_approval_list_orders').dataTable();
	oTable.fnDestroy();
	if (list!= null && list.length > 0 )
	{  	   
		oTable = $('#bt_approval_list_orders').dataTable(  
			{
				'aaData': list,
				'bProcessing': true,
				"dom": 'lBfrtip',
				"lengthMenu": [[-1, 25, 50, 100, 250], ["All" ,25, 50, 100, 250]],
				'aoColumns': [ 
							{ "mDataProp": function ( row, type, val, meta ){
							    var f_entry_date = row.f_entry_date; 
                                f_entry_date = f_entry_date.split("-");
                                var fb_entry_date = new Date( f_entry_date[0], f_entry_date[1] - 1, f_entry_date[2]);
                                
                                var t_entry_date = row.t_entry_date;
                                t_entry_date = t_entry_date.split("-");
                                var tb_entry_date = new Date( t_entry_date[0], t_entry_date[1] - 1, t_entry_date[2]);
                                
                                var approval_type = $("input[name='bt_approval_type']:checked").val();
			                    if( tb_entry_date.getTime() < fb_entry_date.getTime() && approval_type == 2){ // Donot allow Stock Download if to branch date is less than from branch
			                        return "<span style='color:red;'>Check Day Close</span>";
			                    }else{
								    return '<input type="checkbox" name="trans_id[]" class="trans_id" value="'+row.branch_transfer_id+'">';
							    }
			                }},  
			               
			                { "mDataProp": "branch_transfer_id" },
			                { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="from_branch" value="'+row.fb_id_branch+'">'+ row.from_branch;
			                }},
			                { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="to_branch" value="'+row.tb_id_branch+'">'+ row.to_branch;
			                }},
				             { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="id_orderdetails" value="'+row.id_orderdetails+'">'+ row.orderno;
			                }},
				            { "mDataProp": "product_name" }, 
				            { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="t_pieces" value="'+row.totalitems+'">'+ row.totalitems;
			                }}, 
			              
			                { "mDataProp": function ( row, type, val, meta ){
			                	return '<input type="hidden" class="t_net_wt" value="'+row.weight+'">'+ row.weight;
			                }}, 
			                { "mDataProp": function ( row, type, val, meta ){
			                	return "Yet To Approve";
			                }},
						],
				
			}
		); 
       
	}
		
} 
//Repair Orders
function branch_download_by_scan(data)
{
	if(data.list.length > 0)
	{
        	id_pro = data.list[0].id_prod;
        	// scan_dwload_data = data.list[0].prod[id_pro].tags;
        	$("#dnload_trans_id").val(data.list[0].branch_transfer_id);
        	$("#branch_trans_from_branch").val(data.list[0].fb_id_branch);
        	$("#branch_trans_to_branch").val(data.list[0].tb_id_branch);
        	$("#actual_pcs_dnload").val(data.list[0].actual_pieces);
        	$("#actual_weights_dnload").val(parseFloat(data.list[0].actual_weights).toFixed(3));
        	$("#bt_approval_list_by_scan").css("display", "table");
            $(".tag_scan_download").css("display", "block");
            $("#scan_tag_no").focus();	
        	console.log(data); 
        	var html='';
	
			html+='<tr>'+
				'<td>'+data.list[0].branch_transfer_id+'</td>'+
				'<td>'+data.list[0].piece+'</td>'+
				'<td>'+parseFloat(data.list[0].gross_wt).toFixed(3)+'</td>'+
				'<td class="f_branch">'+data.list[0].from_branch+'</td>'+
				'<td class="t_branch">'+data.list[0].to_branch+'</td>'+
				'<td class="dnload_status">Yet to Download</td>'
			   '</tr>';
			$('#bt_approval_list_by_scan > tbody').append(html);
	}
}
$("#scan_tag_no").keypress(function(e) {
    if(e.which == 13) 
    {
        if($("#scan_tag_no").val()!='')
        {
    		getscan_TagSearchList($("#scan_tag_no").val());
        }
		else
		{
		    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter the Tag Code..'});
		}
	}
});
function getscan_TagSearchList(tag_code)
{
	$(".scan_summary").css("display", "block");
	$("#bt_dwnload_list").css("display", "table");
	var approval_type = $("input[name='bt_approval_type']:checked").val();
    var isOtherIssue = ($('#isOtherIssue').is(":checked") ? 1 : 0); 
    var form_secret = $("#form_secret").val(); 
	$.ajax({
        url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/update_TagsByFilter_scan?nocache=",
        data:{
                'branch_trans_code' :$("#bt_trans_code").val(),
                'tag_code'          :$('#scan_tag_no').val(),
                'approval_type'     :approval_type,
                'is_other_issue'    :isOtherIssue,
				'trans_type'        :$("input[name='transfer_item_type']:checked").val(),
				'from_branch'       :$("#branch_trans_from_branch").val(),
				'to_branch'         :$("#branch_trans_to_branch").val(),
				'form_secret'       :form_secret
             },
        dataType:"JSON",
        type:"POST",
        cache:false,
        success:function(data){
				if(data.status)
				{
				        var tag = data.tag_details;
				        var html = '';
				        html += 
                        '<tr>'+
                            '<td>'+tag.tag_code+'</td>'+
    						'<td>'+tag.product_name+'</td>'+
                            '<td>'+tag.piece+'</td>'+
                            '<td>'+tag.gross_wt+'</td>'+
                            '<td>'+tag.less_wt+'</td>'+
                            '<td>'+tag.net_wt+'</td>'+
                        '</tr>';
                        
                        if($('#bt_dwnload_list  > tbody > tr').length > 0 )
                        {
                            $('#bt_dwnload_list > tbody > tr:first').before(html);
                        }
                        else
                        {
                            $('#bt_dwnload_list > tbody').append(html);
                        }
				    if(data.bt_status)
				    {
				        window.location.reload();
				    }
				    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});
				}else{
				    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
				}
				$('#scan_tag_no').val("");
				$("#scan_tag_no").focus();	
            
            },
            
            error:function(error)  
            {
            $("div.overlay").css("display", "none");
            }
        });
		
}
function get_ActiveSections()
{
	$("#section_select option").remove();
    my_Date = new Date();
    $.ajax({
        type: 'POST',
        url: base_url+"index.php/admin_ret_catalog/get_sectionBranchwise?nocache=" + my_Date.getUTCSeconds(),
        data:{'id_branch':$('#from_brn').val()},
        dataType:'json',
        success:function(data){
            var id=$("#id_section").val();
            $.each(data,function(key, item){
                $("#section_select").append(
                    $("<option></option>")
                    .attr("value",item.id_section)
                    .text(item.section_name)
                );
            });
			$('#section_select').select2({
				placeholder:"Select Section",
				allowClear: true
			});
            
			$("#section_select").select2("val",(id!='' && id>0?id:''));
            
			$(".overlay").css("display","none");
        }
    })
}
function getNonTagReceiptedLots()
{
	$("#nt_receipt option").remove();
    my_Date = new Date();
    $.ajax({
        type: 'POST',
        url: base_url+"index.php/admin_ret_brntransfer/getNonTagReceiptedLots?nocache=" + my_Date.getUTCSeconds(),
        data:{'id_branch':$('#to_brn').val()},
        dataType:'json',
        success:function(data){
            var id=$("#nt_receipt").val();
            $.each(data,function(key, item){
                $("#nt_receipt").append(
                    $("<option></option>")
                    .attr("value",item.id_nontag_receipt)
                    .text(item.nt_receipt_no)
                );
            });
			$('#nt_receipt').select2({
				placeholder:"Select Receipt",
				allowClear: true
			});
            
			$("#nt_receipt").select2("val",(id!='' && id>0?id:''));
            
			$(".overlay").css("display","none");
        }
    })
}
function getNonTaggedReceiptedItem(){ 
	my_Date = new Date();
	$.ajax({
		 url:base_url+ "index.php/admin_ret_brntransfer/branch_transfer/getNonTaggedReceiptedItem?nocache=" + my_Date.getUTCSeconds(),
		 data: {'id_branch':$('#to_brn').val(),'id_nontag_receipt':$('#nt_receipt').val()},  
		 dataType:"JSON",
		 type:"POST",
		 cache:false,
		 success:function(data){  
		 	 $("div.overlay").css("display", "none"); 
			 $('#nt_total').text(data.length);
			 var oTable = $('#bt_nt_search_list').DataTable();
			 oTable.clear().draw();
			 if (data!= null && data.length > 0)
			 {  	
				oTable = $('#bt_nt_search_list').dataTable({
						"bDestroy": true,
						"bInfo": true,
						"bFilter": true,
						"bSort": true,
						"order": [[ 0, "desc" ]],
						"dom": 'lBfrtip',
						"aaData"  : data,
						"aoColumns": [	{ "mDataProp":function ( row, type, val, meta ){ 
											return '<input type="checkbox" name="nt_item_sel[]" class="nt_item_sel" value="'+row.nt_item_sel+'"><input type="hidden" class="id_lot_inward_detail" name="id_lot_inward_detail[]" value="'+row.id_lot_inward_detail+'"><input type="hidden" class="id_nontag_receipt " name="id_nontag_receipt[]" value="'+row.id_nontag_receipt +'"><input type="hidden" class="id_nontag_item" name="id_nontag_item[]" value="'+row.id_nontag_item+'">';
										}},   
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'">'+row.section_name+'</span>';
										}},
										
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'">'+row.product_name+'</span>';
										}},
										
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'">'+row.design_name+'</span>';
										}},  
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'"><input type="number" name="nt_piece[]" class="nt_piece col-md-6" value="'+row.no_of_piece+'"><input type="hidden" class="blc_pieces col-md-6" value="'+row.no_of_piece+'"> of &nbsp;'+ row.no_of_piece +'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'"><input type="number" step=any name="nt_gross_wt[]" class="nt_gross_wt col-md-6" value="'+row.gross_wt+'"><input type="hidden" class="blc_gross_wt col-md-6" value="'+row.gross_wt+'"> of &nbsp;'+ row.gross_wt +'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';
										}},
										{ "mDataProp":function ( row, type, val, meta ){
											var cls = (row.lot_no == '' ? 'text-maroon' : '' );
											return '<span class="'+cls+'"><input type="number" step=any name="nt_net_wgt[]" class="nt_net_wgt col-md-6" value="'+row.net_wt+'"><input type="hidden" class="blc_net_wgt col-md-6" value="'+row.net_wt+'"> of &nbsp;'+ row.net_wt +'<br/><span style="font-size:10px;color:red;" class="err"></span></span>';
										}}   
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
function send_mobile_approval_request(){
	$("#discountApprovalMessage").css("display", "none");
	$("#otp_form").css("display", "block");
	$("#send_discount_otp").css("display", "none");
	$("#otp_submit_validation").css("display", "inline-block");
	$("#resend_otp").prop('disabled', true);
	var appr_trans_id= [];
	var appr_trans_code= [];
	var gross_wt = 0;
	var net_wt = 0;
	var dia_wt = 0;
	trans_type =  $("input[name='transfer_item_type']:checked").val();
	$("input[name='trans_id[]']:checked").each(function() { 
		if(trans_type==1){
				if($('#bt_approval_list > tbody tr').length > 0){
					$('#bt_approval_list > tbody tr').each(function(bidx, brow){
						var is_checked = $(brow).find("input[name='trans_id[]']:checked").is(":checked");
						if(is_checked){
							if(check_duplicates($(this).find('.trans_id').val(),appr_trans_id)){
								appr_trans_id.push($(this).find('.trans_id').val());
								appr_trans_code.push($(this).find('.entered_btcode').val());
								gross_wt +=  parseFloat($(this).find('.t_gross_wt').val());
								net_wt +=  parseFloat($(this).find('.t_net_wt').val());
							}
						}
					});
			}
		}else if(trans_type==2)
		{
			if($('#bt_approval_list_nt > tbody tr').length > 0){
				$('#bt_approval_list_nt > tbody tr').each(function(bidx, brow){
					var is_checked = $(brow).find("input[name='trans_id[]']:checked").is(":checked");
					if(is_checked){
						if(check_duplicates($(this).find('.trans_id').val(),appr_trans_id)){
							appr_trans_id.push($(this).find('.trans_id').val());
							appr_trans_code.push($(this).find('.entered_btcode').val());
							gross_wt +=  parseFloat($(this).find('.nt_gross_wt').val());
							net_wt +=  parseFloat($(this).find('.nt_net_wt').val());
						}
					}
				});
			}
		}else if(trans_type==3){
			if($('#bt_approval_list_old_metal > tbody tr').length > 0){
				$('#bt_approval_list_old_metal > tbody tr').each(function(bidx, brow){
					var is_checked = $(brow).find("input[name='trans_id[]']:checked").is(":checked");
					if(is_checked){
						if(check_duplicates($(this).find('.trans_id').val(),appr_trans_id)){
							appr_trans_id.push($(this).find('.trans_id').val());
							appr_trans_code.push($(this).find('.entered_btcode').val());
							gross_wt +=  parseFloat($(this).find('.old_metal_gross_wt').val());
							net_wt +=  parseFloat($(this).find('.old_metal_net_wt').val());
						}
					}
				});
			}
		}else if(trans_type==4){
			if($('#bt_approval_list_packaging > tbody tr').length > 0){
				$('#bt_approval_list_packaging > tbody tr').each(function(bidx, brow){
					var is_checked = $(brow).find("input[name='trans_id[]']:checked").is(":checked");
					if(is_checked){
						if(check_duplicates($(this).find('.trans_id').val(),appr_trans_id)){
							appr_trans_id.push($(this).find('.trans_id').val());
							appr_trans_code.push($(this).find('.entered_btcode').val());
						}
					}
				});
			}
		}
		else if(trans_type==5){
			if($('#bt_approval_list_orders > tbody tr').length > 0){
				$('#bt_approval_list_orders > tbody tr').each(function(bidx, brow){
					var is_checked = $(brow).find("input[name='trans_id[]']:checked").is(":checked");
					if(is_checked){
						if(check_duplicates($(this).find('.trans_id').val(),appr_trans_id)){
							appr_trans_id.push($(this).find('.trans_id').val());
							appr_trans_code.push($(this).find('.entered_btcode').val());
							net_wt +=  parseFloat($(this).find('.t_net_wt').val());
						}
					}
				});
			}
		}
		
		
	});
	console.log('trans_id : ' + JSON.stringify(appr_trans_id));	
	my_Date = new Date();
	if(appr_trans_id.length > 0 && appr_trans_code.length>0)
	{
		var approval_type =  $("input[name='bt_approval_type']:checked").val(); 
		$('#apprl_status').val(0);
		$.ajax({
			url: base_url + 'index.php/admin_app_api/bt_approval_otp_req/?nocache=' + my_Date.getUTCSeconds(),
			dataType: "json",
			method: "POST",
			data: { 
					
					'OTP_aprvl_type'     : 3,
					'apprl_requested_by' : $('#uid').val(),
					'BT_trans_id'        : appr_trans_id,
					'BT_trans_code'      : appr_trans_code,
					'BT_approval_type'   : approval_type,
					'transfer_item_type' : trans_type,
					'gross_wt'			 : gross_wt,
					'net_wt' 			 : net_wt,
					},
			success: function (data) {
	
				if(data.status){
					$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + data.message });
					$('#approval_id').val(data.approval_id);
					$('#approval_type').val(data.approval_type);
					$('#app_aproval_modal').modal('show');
				}else{
					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.message });
					$('#approval_id').val(data.approval_id);
					$('#approval_type').val(data.approval_type);
				}
			
			},
			error: function (error) {
			}
		});
		show_countdown();
	}else{
		$.toaster({ priority: 'warning', title: 'Warning!', message: '' + "</br>Trans Code Not Found For Approval" });
	}
}
function check_duplicates(cur_id,trans_data) {
	
	var flag = true;
	trans_data.forEach(element => {
		if(element == cur_id){
			flag= false;
		}
 });
 return flag;
}
$(document).on('click','.cancel_bt_aprvl',function(){
	$('#app_aproval_modal').modal('hide');
	let approval_id = $('#approval_id').val();
	update_aprvl_status(approval_id,3);
});
$(document).ready(function () {
	var socket = io('https://liverate.logimaxindia.com:3001/');
	let cliIDcode = $('#cliIDcode').val();
	socket.on("retailupdateapprove:App\\Events\\RETAILDataUpdates", function(data){
		console.log(data);
		console.log(JSON.stringify(data));
		
		if(data.updatedata.clientcode == cliIDcode ){ //add condition with client code
			if(data.updatedata.status==1 && (data.updatedata.approval_id ==  $('#approval_id').val())){	
				$.toaster({ priority: 'success', title: 'Warning!', message: '' + "</br>OTP Successfull Approved" });
				$('#app_aproval_modal').modal('hide');
				$('#approve').trigger('click');
			}
			else if(data.updatedata.status==2 && (data.updatedata.approval_id ==  $('#approval_id').val())){
					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Approval Got Rejected" });
					$('#upd_status_btn').prop('disabled',false);
					$('#app_aproval_modal').modal('hide');
			}
		
		}
		
	});
});
function get_ActiveKarigars(kar_type='') {
	$.ajax({
		type: 'GET',
		url: base_url + 'index.php/admin_ret_catalog/karigar/active_list/'+kar_type,
		dataType: 'json',
		success: function (data) {
			var id = $("#karigar").val();
			$("#karigar").append(
				$("<option></option>")
					.attr("value", 0)
					.text('All')
			);
			$.each(data, function (key, item) {
				$('#karigar').append(
					$("<option></option>")
						.attr("value", item.id_karigar)
						.text(item.karigar)
				);
			});
			$("#karigar").select2(
				{
					placeholder: "Select Karigar",
					allowClear: true
				});
			$('#karigar').select2("val", "");
		}
	});
}
function update_aprvl_status(Active_aprvl_id,status){
	//status = 3 -> Cancelled
	//status = 4 -> Expired
	if(Active_aprvl_id==''){
		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Approvel ID Not Found" });
	}else if(status == ''){
		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Approvel stauts Not Found" });
	}else{
		$.ajax({
			url: base_url + 'index.php/admin_app_api/update_aprvl_status/?nocache=' + my_Date.getUTCSeconds(),
			dataType: "json",
			method: "POST",
			data : {'active_aprvl_id' : Active_aprvl_id , 'approval_status' : status},
			success: function (data) {
					if(data.status){
						if(data.approval_status  == 3) //Cancelled
							{
								$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + data.msg});
								$('#apprl_status').val(data.approval_status);
								$('#upd_status_btn').prop('disabled',false);
							}
							else if(data.approval_status  == 4)//Expired
							{
								$.toaster({ priority: 'warning', title: 'Info', message: '' + "</br>" + data.msg });
								$('#apprl_status').val(data.approval_status);
								$('#upd_status_btn').prop('disabled',false);
							}
					}else{
						$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + data.msg });
						$('#apprl_status').val(data.approval_status);
						$('#upd_status_btn').prop('disabled',false);
					}
				},
				error: function(err){
					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Cannot Update Cancel Status" });
				}
			});
	}
 }
 $(document).on('click','.get_approval',function(){
	get_approval_status();
});
function get_approval_status(){
	my_Date = new Date();
	if($('#approval_id').val()==''){
		$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br> Approval ID not Found ...." });
	}
	else{
		$.ajax({
			url: base_url + 'index.php/admin_app_api/get_approval_status/?nocache=' + my_Date.getUTCSeconds(),
			dataType: "json",
			method: "POST",
			data: {
				'approval_id'          : $('#approval_id').val(),
			},
			success: function (data) {
				if(data.apprl_status==0 && (data.apprl_id == $('#approval_id').val()) && $('#apprl_status').val()!=4){
					$.toaster({ priority: 'warning', title: 'Warning!', message: '' + "</br>Waiting For Approval" });
				}
				else if(data.apprl_status==1 && (data.apprl_id == $('#approval_id').val()) && $('#apprl_status').val()!=4)
				{
					$.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>Approval Successed" });
					$('#approve').trigger('click');
				}
				else if(data.apprl_status==2 && (data.apprl_id == $('#approval_id').val()) && $('#apprl_status').val()!=4)
				{
					$.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>Approval Got Rejected" });
					$('#upd_status_btn').prop('disabled',false);
					$('#app_aproval_modal').modal('hide');
				}
				else if(data.apprl_status==4 && (data.apprl_id == $('#approval_id').val()) && $('#apprl_status').val()!=4)
				{
					$.toaster({ priority: 'danger', title: 'Info!', message: '' + "</br>Approval Request Expired" });
					$('#app_aproval_modal').modal('hide');
				}
			},
			error: function (error) {
			}
		});
	}
}
let intervalId; 
function show_countdown(callback) {
    const now = new Date().getTime();
    const targetTime = now + 60 * 1000;
    if (intervalId) {
        clearInterval(intervalId);
    }
    $(".show_timer").html('');
    intervalId = setInterval(function() {
        const currentTime = new Date().getTime();
        const distance = targetTime - currentTime;
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
		$(".show_timer").html('( ' + minutes + 'm ' + seconds + 's )');
        if (distance < 0) {
            clearInterval(intervalId);
            set_expiry_status();
        }
    }, 1000);
}
function  set_expiry_status(){
	get_approval_status();  //Async Function , Check Status If Socket was not Working
			$(".show_timer").html('');
			let approval_type = $('#approval_type').val();
			let approval_id = $('#approval_id').val();
			let approval_status = $('#apprl_status').val();
			if(approval_id!='' && approval_type==3 && approval_status==0){ //Branch Transfer
				update_aprvl_status(approval_id,4);
				$('#app_aproval_modal').modal('hide');
			}
}