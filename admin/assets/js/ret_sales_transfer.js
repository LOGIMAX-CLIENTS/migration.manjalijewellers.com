var path =  url_params();

var ctrl_page 		= path.route.split('/');

$(document).ready(function() {

	var path =  url_params();

	$('#status').bootstrapSwitch();

    $(window).scroll(function() {    // this will work when your window scrolled.

		var height = $(window).scrollTop();  //getting the scrolling height of window

		if(height  > 300) {

			$(".stickyBlk").css({"position": "fixed"});

		} else{

			$(".stickyBlk").css({"position": "static"});

		}

	}); 

	switch(ctrl_page[1]) {

	 	case 'sales_transfer':

	 		if (ctrl_page[2] == 'list') {
	 			// Unified list page — shows both Sales Transfer + Sales Return Transfer
	 			init_sales_transfer_list();
	 		} else if (ctrl_page[2] == 'ret_add') {
	 			// Sales Return Transfer add page
	 			get_metal_rates_by_branch();
	 			get_ActiveCategory();
	 			get_ActiveMetals();
	 			getBTBranches();
	 			get_received_lots();
	 			init_sales_ret_trans_type_handler();
	 		} else {
	 			// Sales Transfer add/edit page
		        get_metal_rates_by_branch();
		        get_ActiveCategory();
		        get_ActiveMetals();
		        getBTBranches();
		        get_received_lots();
	 		}

	    break;

	}

	

	$("#lotno").select2({			    

	 	placeholder: "Select Lot",			    

	 	allowClear: true		    

 	});

 	

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



	$(document).on('change',"#select_metal, .from_branch", function(){

		get_received_lots();

	});



function get_received_lots(){

	//trans_type =  $("input[name='transfer_item_type']:checked").val();

	trans_type =  1;

	from_brn = $("#from_brn").val();

	to_brn = $("#to_brn").val();

	if(from_brn != '' && trans_type != ''){

		$.ajax({		

		 	type: 'POST',		

		 	url : base_url + 'index.php/admin_ret_brntransfer/branch_transfer/getLotsByBranch',		

		 	dataType : 'json',		

		 	data : {'from_branch': from_brn,'to_branch': to_brn,'trans_type' : trans_type, 'page' : 'sales_transfer'},

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





function getBTBranches(){	 

 	$.ajax({		

     	type: 'GET',		

     	url: base_url+'index.php/admin_ret_brntransfer/bt_get_branches',		

     	dataType:'json',		

     	success:function(data){	

     		var id_branch = "";	

     		branchArr = data;

     		$(".from_branch,.to_branch").select2({			    

        	 	placeholder: "Select Branch",			    

        	 	allowClear: true		    

         	});		  

    	  	$.each(data, function (key, item) { 

    	  	    

    	  		if(loggedInBranch > 0)

                { 

                    if(loggedInBranch == item.id_branch)

                    {

                        var loggedGst = item.gst_number;

                        console.log('loggedGst',loggedGst);

                    }

	    	  		if(ctrl_page[1] == 'sales_transfer'){

	    	  		    

	    	  		    if(ctrl_page[2]=='add')

	    	  		    {

	    	  		        var sales_transfer_item_type =  $("input[name='sales_transfer_item_type']:checked").val(); 

	    	  		    }else

	    	  		    {

	    	  		        var sales_transfer_item_type =  $("input[name='sales_ret_transfer_item_type']:checked").val(); 

	    	  		    }

	    	  		    

	    	  			from_brn = (sales_transfer_item_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());

			            to_brn = (sales_transfer_item_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val());

						if(sales_transfer_item_type == 1){ // Transit Approval

                           

							if(loggedInBranch != item.id_branch && (loggedGst==item.gst_number)){

					    	 	 $(".to_branch,.to_branch").append(						

					        	 	$("<option></option>")						

					        	 	.attr("value", item.id_branch)						  						  

					        	 	.attr("data-gst_number", item.gst_number)						  						  

					        	 	.text(item.name )						  					

					    	 	 ); 

						 	 }else{ 

            		    	    $(".from_branch").attr("disabled",true);

                			 	$(".from_branch").append(						

                		        	 	$("<option></option>")						

                		        	 	.attr("value", item.id_branch)

                		        	 	.attr("data-gst_number", item.gst_number)

                		        	 	.text(item.name )						  					

                		    	 ); 

            			 	    $(".from_branch").select2("val",from_brn);

            			 	 }

						 	 $(".to_branch").select2('val','');

						 	 /*if(loggedInBranch == item.id_branch){			        	 	

					    	 	 $(".app_frm_brn").css("display","none");  

						 	} */

						}else{ // Stock Download 

				        	 if(loggedInBranch != item.id_branch){			        	 	

				        	 	 $(".from_branch").append(						

					        	 	$("<option></option>")						

					        	 	.attr("value", item.id_branch)

					        	 	.attr("data-gst_number", item.gst_number)

					        	 	.text(item.name )						  					

				        	 	 ); 

			        	 	 }else{ 

                			 	 $(".to_branch").append(						

                		        	 	$("<option></option>")						

                		        	 	.attr("value", item.id_branch)

                		        	 	.attr("data-gst_number", item.gst_number)

                		        	 	.text(item.name )						  					

                		    	 );

            			 	    $(".to_branch").attr("disabled",true); 

            			 	    $(".to_branch").select2("val",to_brn);

			        	 	 }

			        	 	 /*if(loggedInBranch == item.id_branch){

			        	 	 	$(".app_to_brn").css("display","none");   

			        	 	 }  */			        	 	 

						}

					}else{

						if(loggedInBranch != item.id_branch){		  				  			   		

			        	 	$(".from_branch").append(						

				        	 	$("<option></option>")						

				        	 	.attr("value", item.id_branch)

				        	 	.attr("data-gst_number", item.gst_number)

				        	 	.text(item.name )						  					

			        	 	);	

			        	 }	

			        	 if(loggedInBranch != item.id_branch){			        	 	

			        	 	 $(".to_branch,.to_branch").append(						

				        	 	$("<option></option>")						

				        	 	.attr("value", item.id_branch)

				        	 	.attr("data-gst_number", item.gst_number)

				        	 	.text(item.name )						  					

			        	 	 ); 

		        	 	 } 

		        	 	 $(".from_branch,.to_branch").select2("val","");   

					}

					

				}else{

					$(".from_branch,.to_branch").append(						

		        	 	$("<option></option>")						

		        	 	.attr("value", item.id_branch)

		        	 	.attr("data-gst_number", item.gst_number)

		        	 	.text(item.name )						  					

	        	 	);			

         			$(".from_branch,.to_branch").select2("val","");   

				} 										

         	}); 

         //	$(".from_branch").select2("val",id_branch);    

     	}	

    }); 

}





$(".from_branch,#filter_from_brn").on('change', function(e){

		var isDisabled = $(".to_branch,.to_branch ").prop('disabled');  

		if(this.value != '' && !isDisabled){ 

			$("#to_brn option,.to_branch option").remove();

			$("#to_brn,.to_branch").val(null).trigger('change');

			

			var gst_number = $('.from_branch option:selected').attr('data-gst_number');

			console.log('gst_number:'+gst_number);

		 	$(".from_branch option,#filter_from_brn option").each(function()

			{  	

			    var to_brch_gst_number = $(this).attr('data-gst_number');

				if(($(this).val() != $(".from_branch,#filter_from_brn").val()) && (gst_number!=to_brch_gst_number))

				{   			   		

		    	 	$("#to_brn,.to_branch").append(						

		    	 	$("<option></option>")						

		    	 	.attr("value", $(this).val())

		    	 	.text($(this).text())						  					

		    	 	.attr("data-gst_number",to_brch_gst_number)						  					

		    	 	);	  

				}	

				$("#to_brn,.to_branch").val('');		    	

			});	

			/*if(ctrl_page[2] == 'add'){

				get_received_lots();

			}*/		 

				

		}

	});



//SALES TRANSFER

	

	

	

	

	$('input[type=radio][name="sales_transfer_item_type"]').change(function() {

	    $('.sales_trans').css("display","none");

	    $('.sales_trans_download').css("display","none");

        $('.sales_trans_download_search').css('display','none');

        $('.remark').css("display","none");

	    if(this.value==1)      //sales transfer

	    {

	        $('.sales_trans').css("display","block");

			$('.sales_trans_bill_no').css("display","none");

	        $('.sales_trans_tag_no').css("display","block");

	        // $('.sales_trans_calc_type').css("display","block");

            $('#tag_scan_code').css("display","none");

            $('.sales_submit').css('display','block');

            $('#bill_dwnload_list').css('display','none');

            $('#bill_approval_list_by_scan').css('display','none');

            $('.remark').css("display","block");

	        // Reset transfer type to Tagged when switching back to Request mode
	        $('input[name="sales_trans_type"][value="1"]').prop('checked', true);
	        $('#salesTransType').val(1);
	        onSalesTransTypeChange(1);

	    }else if(this.value==2) //sales retrun transfer

	    {

            $('.sales_trans_download_search').css('display','block');

	        $('.sales_trans_bill_no').css("display","block");

	        $('.sales_trans_tag_no').css("display","none");

	        $('.sales_trans_calc_type').css("display","none");

            var sales_trans_dnload = $('#sales_trans_dnload').val();

            if(sales_trans_dnload==2)

            {

                //$('#tag_scan_code').css("display","block");

                $('.sales_submit').css('display','none');

                //$('.sales_trans_download').css("display","none");



            }

            else

            {

                //$('#tag_scan_code').css("display","none");

                $('.sales_submit').css('display','block');

                $('.sales_trans_download').css("display","block");



            }

	    }

	    

	    $(".from_branch").attr("disabled",false);

	    $(".to_branch").attr("disabled",false); 

	    $(".from_branch option,.to_branch option").remove();

	    $.each(branchArr, function (key, item) { 

    	  	    

    	  		if(loggedInBranch > 0){ 



                    if(loggedInBranch == item.id_branch)

                    {

                        var loggedGst = item.gst_number;

                        console.log('loggedGst',loggedGst);

                    }



	    	  		if(ctrl_page[1] == 'sales_transfer'){

	    	  			if(ctrl_page[2]=='add')

	    	  		    {

	    	  		        var sales_transfer_item_type =  $("input[name='sales_transfer_item_type']:checked").val(); 

	    	  		    }else

	    	  		    {

	    	  		        var sales_transfer_item_type =  $("input[name='sales_ret_transfer_item_type']:checked").val(); 

	    	  		    }

	    	  			from_brn = (sales_transfer_item_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());

			            to_brn = (sales_transfer_item_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val());

						if(sales_transfer_item_type == 1){ // Transit Approval  

							if(loggedInBranch != item.id_branch && loggedGst==item.gst_number){

					    	 	 $(".to_branch,.to_branch").append(						

					        	 	$("<option></option>")						

					        	 	.attr("value", item.id_branch)						  						  

					        	 	.attr("data-gst_number", item.gst_number)						  						  

					        	 	.text(item.name )						  					

					    	 	 ); 

						 	 }else{ 

            		    	    $(".from_branch").attr("disabled",true);

                			 	$(".from_branch").append(						

                		        	 	$("<option></option>")						

                		        	 	.attr("value", item.id_branch)

                		        	 	.attr("data-gst_number", item.gst_number)

                		        	 	.text(item.name )						  					

                		    	 ); 

            			 	    $(".from_branch").select2("val",from_brn);

            			 	 }

						 	 $(".to_branch").select2('val','');

						 	 /*if(loggedInBranch == item.id_branch){			        	 	

					    	 	 $(".app_frm_brn").css("display","none");  

						 	} */

						}else{ // Stock Download 

				        	 if(loggedInBranch != item.id_branch){			        	 	

				        	 	 $(".from_branch").append(						

					        	 	$("<option></option>")						

					        	 	.attr("value", item.id_branch)

					        	 	.attr("data-gst_number", item.gst_number)

					        	 	.text(item.name )						  					

				        	 	 ); 

			        	 	 }else{ 

                			 	 $(".to_branch").append(						

                		        	 	$("<option></option>")						

                		        	 	.attr("value", item.id_branch)

                		        	 	.attr("data-gst_number", item.gst_number)

                		        	 	.text(item.name )						  					

                		    	 );

            			 	    $(".to_branch").attr("disabled",true); 

            			 	    $(".to_branch").select2("val",to_brn);

			        	 	 }

			        	 	 $(".from_branch").select2('val','');

			        	 	 /*if(loggedInBranch == item.id_branch){

			        	 	 	$(".app_to_brn").css("display","none");   

			        	 	 }  */			        	 	 

						}

					}else{

						if(loggedInBranch != item.id_branch){		  				  			   		

			        	 	$(".from_branch").append(						

				        	 	$("<option></option>")						

				        	 	.attr("value", item.id_branch)

				        	 	.attr("data-gst_number", item.gst_number)

				        	 	.text(item.name )						  					

			        	 	);	

			        	 }	

			        	 if(loggedInBranch != item.id_branch){			        	 	

			        	 	 $(".to_branch,.to_branch").append(						

				        	 	$("<option></option>")						

				        	 	.attr("value", item.id_branch)

				        	 	.attr("data-gst_number", item.gst_number)

				        	 	.text(item.name )						  					

			        	 	 ); 

		        	 	 } 

		        	 	 $(".from_branch,.to_branch").select2("val","");   

					}

					

				}else{

					$(".from_branch,.to_branch").append(						

		        	 	$("<option></option>")						

		        	 	.attr("value", item.id_branch)

		        	 	.attr("data-gst_number", item.gst_number)

		        	 	.text(item.name )						  					

	        	 	);			

         			$(".from_branch,.to_branch").select2("val","");   

				} 										

         	});

         	

	});





	$('input[type=radio][name="sales_ret_transfer_item_type"]').change(function() {

	    $('.sales_trans').css("display","none");

	    $('.sales_trans_download').css("display","none");

	    $('#aganist_bill_yes').prop('disabled',false);

	    $('#aganist_bill_no').prop('disabled',false);

        $('.sales_trans_download_search').css('display','none');

        $('.remark').css('display','none');

	    if(this.value==1)      //sales ret request

	    {

	        $('.sales_trans').css("display","block");

			$('.sales_trans_bill_no').css("display","block");

            $('#ret_bill_approval_list_by_scan').css("display","none");

            $('#ret_tag_scan_code').css("display","none");

            $('.sales_submit').css('display','block');

            $('#ret_bill_dwnload_list').css('display','none');

            $('.remark').css('display','block');

            // Show transfer type selector and reset to Tagged
            $('#salesRetTransTypeRow').show();
            $('input[name="sales_ret_trans_type"][value="1"]').prop('checked', true);
            $('#salesRetTransType').val(1);

	        

	    }else if(this.value==2) //sales retrun download

	    {

	        $('.sales_trans_download_search').css("display","block");

	        $('.sales_trans_bill_no').css("display","block");

	        $('.sales_trans_tag_no').css("display","none");

	        $('.sales_trans_calc_type').css("display","none");

	        $('#aganist_bill_yes').prop('disabled',true);

	        $('#aganist_bill_no').prop('disabled',true);

            // Hide transfer type selector in download mode
            $('#salesRetTransTypeRow').hide();



            var sales_ret_trans_dnload = $('#sales_ret_trans_dnload').val();

            if(sales_ret_trans_dnload==2)

            {

                $('.sales_submit').css('display','none');



            }

            else

            {

                $('.sales_submit').css('display','block');

                $('.sales_trans_download').css("display","block");



            }

	    }

	    

	    $(".from_branch").attr("disabled",false);

	    $(".to_branch").attr("disabled",false); 

	    $(".from_branch option,.to_branch option").remove();

	    $.each(branchArr, function (key, item) { 

    	  	    

    	  		if(loggedInBranch > 0){ 

                    if(loggedInBranch == item.id_branch)

                    {

                        var loggedGst = item.gst_number;

                        console.log('loggedGst',loggedGst);

                    }

	    	  		if(ctrl_page[1] == 'sales_transfer'){

	    	  			

	    	  		    var sales_transfer_item_type =  $("input[name='sales_ret_transfer_item_type']:checked").val(); 

	    	  			from_brn = (sales_transfer_item_type == 1 ? (loggedInBranch > 0 ? loggedInBranch : $("#filter_from_brn").val()):$("#filter_from_brn").val());

			            to_brn = (sales_transfer_item_type == 2 ? (loggedInBranch > 0 ? loggedInBranch : $("#filtr_to_brn").val()):$("#filtr_to_brn").val());

						if(sales_transfer_item_type == 1){ // Transit Approval  

							if(loggedInBranch != item.id_branch && (loggedGst==item.gst_number)){

					    	 	 $(".to_branch,.to_branch").append(						

					        	 	$("<option></option>")						

					        	 	.attr("value", item.id_branch)						  						  

					        	 	.attr("data-gst_number", item.gst_number)						  						  

					        	 	.text(item.name )						  					

					    	 	 ); 

						 	 }else{ 

            		    	    $(".from_branch").attr("disabled",true);

                			 	$(".from_branch").append(						

                		        	 	$("<option></option>")						

                		        	 	.attr("value", item.id_branch)

                		        	 	.attr("data-gst_number", item.gst_number)

                		        	 	.text(item.name )						  					

                		    	 ); 

            			 	    $(".from_branch").select2("val",from_brn);

            			 	 }

						 	 $(".to_branch").select2('val','');

						 	 /*if(loggedInBranch == item.id_branch){			        	 	

					    	 	 $(".app_frm_brn").css("display","none");  

						 	} */

						}else{ // Stock Download 

				        	 if(loggedInBranch != item.id_branch){			        	 	

				        	 	 $(".from_branch").append(						

					        	 	$("<option></option>")						

					        	 	.attr("value", item.id_branch)

					        	 	.attr("data-gst_number", item.gst_number)

					        	 	.text(item.name )						  					

				        	 	 ); 

			        	 	 }else{ 

                			 	 $(".to_branch").append(						

                		        	 	$("<option></option>")						

                		        	 	.attr("value", item.id_branch)

                		        	 	.attr("data-gst_number", item.gst_number)

                		        	 	.text(item.name )						  					

                		    	 );

            			 	    $(".to_branch").attr("disabled",true); 

            			 	    $(".to_branch").select2("val",to_brn);

			        	 	 }

			        	 	 $(".from_branch").select2('val','');

			        	 	 /*if(loggedInBranch == item.id_branch){

			        	 	 	$(".app_to_brn").css("display","none");   

			        	 	 }  */			        	 	 

						}

					}else{

						if(loggedInBranch != item.id_branch){		  				  			   		

			        	 	$(".from_branch").append(						

				        	 	$("<option></option>")						

				        	 	.attr("value", item.id_branch)

				        	 	.attr("data-gst_number", item.gst_number)

				        	 	.text(item.name )						  					

			        	 	);	

			        	 }	

			        	 if(loggedInBranch != item.id_branch){			        	 	

			        	 	 $(".to_branch,.to_branch").append(						

				        	 	$("<option></option>")						

				        	 	.attr("value", item.id_branch)

				        	 	.attr("data-gst_number", item.gst_number)

				        	 	.text(item.name )						  					

			        	 	 ); 

		        	 	 } 

		        	 	 $(".from_branch,.to_branch").select2("val","");   

					}

					

				}else{

					$(".from_branch,.to_branch").append(						

		        	 	$("<option></option>")						

		        	 	.attr("value", item.id_branch)

		        	 	.attr("data-gst_number", item.gst_number)

		        	 	.text(item.name )						  					

	        	 	);			

         			$(".from_branch,.to_branch").select2("val","");   

				} 										

         	});

         	

	});

	

	$('input[type=radio][name="aganist_bill"]').change(function() {

	    var trans_type =  $("input[name='sales_ret_transfer_item_type']:checked").val();

	    $('.bill_no').css("display","none");

	    if(this.value==1)      //sales ret request

	    {

	        $('.bill_no').css("display","block");



	    }

	});

	

	

$('.sales_transfer_search').on('click',function(){

            if($('#from_brn').val()=='' || $('#from_brn').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select From Branch..'});

            }

            else if($('#to_brn').val()=='' || $('#to_brn').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Please Select To Branch..'});

            }

            else if($('#from_brn').val() == $('#to_brn').val())

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Cannot transfer to the same branch.'});

            }

            /*else if($('#select_category').val()=='' || $('#select_category').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Please Select The Category..'});

            }*/

            /*else if($('#tag_no').val()=='' && $('#old_tag_no').val()=='')

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Enter The Tag Code.'});

            }*/


            else if($('#is_metal_for_billing').val()=='1' && ($('#select_metal').val()=='' || $('#select_metal').val()==null))

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Metal..'});

            }

            else

            {

                // Dispatch based on Transfer Type
                var stt = parseInt($('#salesTransType').val()) || 1;
                switch(stt) {
                    case 1:
                        get_sales_transfer_tag_list();
                        break;
                    case 2:
                        get_non_tagged_stock_list();
                        break;
                    case 3: // Purchase Items — single combined search (BT parity)
                        st_get_purchase_items();
                        break;
                    default:
                        get_sales_transfer_tag_list();
                }

            }

	});

	



	$('.sales_ret_transfer_search').on('click',function(){

	        var is_aganist_bill =  $("input[name='aganist_bill']:checked").val();

            if($('#from_brn').val()=='' || $('#from_brn').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select From Branch..'});

            }

            else if($('#to_brn').val()=='' || $('#to_brn').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Please Select To Branch..'});

            }

            else if($('#from_brn').val() == $('#to_brn').val())

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Cannot transfer to the same branch.'});

            }

            else if($('#bill_no').val()=='' && is_aganist_bill==1)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+'</br>'+'Please Enter The Bill No'});

            }

            else if($('#is_metal_for_billing').val()=='1' && ($('#select_metal').val()=='' || $('#select_metal').val()==null))

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Metal..'});

            }

            else

            {

                // SRT always uses unified search — model returns all types (Tagged + NT + OG)
                get_sales_return_transfer_tag_list();

            }

	});



	$('.sales_ret_transfer_approval_search').on('click',function(){

	    //trans_type =  $("input[name='sales_transfer_item_type']:checked").val();

            if($('#from_brn').val()=='' || $('#from_brn').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select From Branch..'});

            }

            else if($('#to_brn').val()=='' || $('#to_brn').val()==null)

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select To Branch..'});

            }

            /*else if($('#pur_rate').val()=='')

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Purchase Rate..'});

            }*/

            /*else if($('#bt_code').val()=='' && $('#tag_no').val()=='')

            {

                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Branch Transfer Code or Tag Code'});

            }*/

            else

            {

                get_sales_return_approval_list();

            }

	});





	function get_sales_return_approval_list()

	{

		my_Date = new Date();

        $.ajax({

        url:base_url+ "index.php/admin_ret_sales_transfer/sales_transfer/sales_return_trans_approval_tag?nocache=" + my_Date.getUTCSeconds(),

        data: {'bill_no':$("#bill_no").val(),'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val(),'fin_year_code':$('#fin_year_code').val()},

        dataType:"JSON",

        type:"POST",

        cache:false,

        success:function(data){

        $(".overlay").css("display", "none");

                var searchResList = data;

                $('#total').text(data.length);

              

                if (data!= null && data.length > 0)

                {   

                    var sales_ret_trans_dnload = $('#sales_ret_trans_dnload').val();



                    if(sales_ret_trans_dnload==1)

                    {



                        $('#bt_search_download_list  > tbody').empty();

                        $.each(data, function (key, val) {

                        html='';

                        rowExist=false;

                            /*$('#bt_search_download_list > tbody tr').each(function(bidx, brow){

                                    bt_tagid = $(this);

                                    if( val.bill_id == bt_tagid.find('.bill_id').val())

                                    {

                                        rowExist = true;

                                        //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});

                                    }

                                        

                                });*/

                            if(!rowExist){

                              

                                    html = 

                                    '<tr>'+

                                    '<td><input type="checkbox" name="bill_id[]" class="bill_id" value='+val.bill_id+'>'+val.bill_no+'</td>'+

                                    '<td>'+val.bill_date+'</td>'+

                                    '<td>'+val.piece+'</td>'+

                                    '<td>'+val.gross_wt+'</td>'+

                                    '<td><a href="#"onClick="remove_sales_trans_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

                                    '</tr>';

                                   

                                    if($('#bt_search_download_list  > tbody > tr').length > 0 )

                                    {

                                        $('#bt_search_download_list > tbody > tr:first').before(html);

                                    }

                                    else

                                    {

                                        $('#bt_search_download_list > tbody').append(html);

                                    }

                            }

                        });



                    }

                    else

                    {

                        ret_bill_download_by_scan(data);

                    }



                    $(".sales_ret_transfer_approval_search ").prop('disabled', true);

                }   

                else

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});

					$('#bill_no').val('');

					$("#bill_no").focus();



                    $(".sales_ret_transfer_approval_search ").prop('disabled', false);

                }

            },

            error:function(error)  

            {

            $("div.overlay").css("display", "none");

            }

        });

	}

	

	$('.sales_transfer_approval_search').on('click',function(){

	    trans_type =  $("input[name='sales_transfer_approval_item_type']:checked").val();

	    

	        if($('#from_brn').val()=='' || $('#from_brn').val()==null)

	        {

	            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select From Branch..'});

	        }

	        else if($('#to_brn').val()=='' || $('#to_brn').val()==null)

	        {

	            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select To Branch..'});

	        }

	        else if($('#bill_no').val()=='')

	        {

	            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Bill No..'});

	        }

	        else

	        {

	            get_sales_transfer_approval_list();

	        }

	});

	

	

	function get_sales_transfer_approval_list()

	{

	    my_Date = new Date();

        $.ajax({

        url:base_url+ "index.php/admin_ret_sales_transfer/sales_transfer/sales_trans_approval_tag?nocache=" + my_Date.getUTCSeconds(),

        data: {'bill_no':$("#bill_no").val(),'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val(),'fin_year_code':$('#fin_year_code').val()},

        dataType:"JSON",

        type:"POST",

        cache:false,

        success:function(data){

        $(".overlay").css("display", "none");

                var searchResList = data;

                $('#total').text(data.length);

              

                if (data!= null && data.length > 0)

                {   var sales_trans_dnload = $('#sales_trans_dnload').val();



                    if(sales_trans_dnload==1)

                    {



                    $('#bt_search_download_list  > tbody').empty();

                    $.each(data, function (key, val) {

                    html='';

                    rowExist=false;

                           $('#bt_search_download_list > tbody tr').each(function(bidx, brow){

                                bt_tagid = $(this);

                                if( val.bill_id == bt_tagid.find('.bill_id').val())

                                {

                                    rowExist = true;

                                    //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});

                                }

                                     

                            });

                            if(!rowExist){

                              

                                    html = 

                                    '<tr>'+

                                    '<td><input type="checkbox" name="bill_id[]" class="bill_id" value='+val.bill_id+'>'+val.bill_no+'</td>'+

                                    '<td>'+val.bill_date+'</td>'+

                                    '<td>'+val.piece+'</td>'+

                                    '<td>'+val.gross_wt+'</td>'+

                                    '<td><a href="#"onClick="remove_sales_trans_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

                                    '</tr>';

                                   

                                    if($('#bt_search_download_list  > tbody > tr').length > 0 )

                                    {

                                        $('#bt_search_download_list > tbody > tr:first').before(html);

                                    }

                                    else

                                    {

                                        $('#bt_search_download_list > tbody').append(html);

                                    }

                            }

                     });

                    }

                    else

                    {

                        bill_download_by_scan(data);



                    }



                    $(".sales_transfer_approval_search").prop('disabled', true);

                }   

                else

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});

					$('#bill_no').val('');

					$("#bill_no").focus();

                    $(".sales_transfer_approval_search").prop('disabled', false);

                }

            },

            error:function(error)  

            {

            $("div.overlay").css("display", "none");

            }

        });

	}

	

	

	function get_sales_return_transfer_tag_list()

    {

        var is_aganist_bill =  $("input[name='aganist_bill']:checked").val();

        my_Date = new Date();

        $.ajax({

        url:base_url+ "index.php/admin_ret_sales_transfer/sales_transfer/sales_return_trans_tag?nocache=" + my_Date.getUTCSeconds(),

        data: {'is_aganist_bill':is_aganist_bill,'bt_code':$("#bt_code").val(),'tag_code':$("#tag_no").val(),'old_tag_code':$("#old_tag_no").val(),'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val(),'cat_id':$('#select_category').val(),'bill_no':$('#bill_no').val(),'fin_year_code':$('#fin_year_code').val()},

        dataType:"JSON",

        type:"POST",

        cache:false,

        success:function(data){

        $(".overlay").css("display", "none");

                var searchResList = data;

                $('#total').text(data.length);

              

                if (data!= null && data.length > 0)

                {   

                        $.each(data, function (key, val) {
                        html='';
                        rowExist=false;
                               $('#bt_search_list > tbody tr').each(function(bidx, brow){
                                    bt_tagid = $(this);

                                    if( val.cat_id == bt_tagid.find('.cat_id').val())

                                    {

                                        rowExist = true;

                                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Category Already Exists..'});

                                    }

                                         

                                });

                                if(!rowExist){

                                   

                                      html = 

                                        '<tr>'+

                                        '<td><input type="checkbox" name="cat_id[]" class="cat_id" value='+val.cat_id+'><input type="hidden" class="sales_trans_type" value='+(val.salesTransType || 1)+'><input type="hidden" class="st_bill_no" value="'+(val.bill_no || '')+'">'+(val.category_name)+'</td>'+

                                        '<td><input type="hidden" class="gross_wt" value='+val.gross_wt+'><input type="hidden" class="piece" value='+(val.piece || 1)+'>'+val.gross_wt+'</td>'+

                                        '<td><input type="hidden" name="total_amt[]" class="item_cost" value='+val.item_cost+'>'+val.item_cost+'</td>'+

                                        '<td><a href="#"onClick="remove_sales_trans_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

                                        '</tr>';

                                       

                                        if($('#bt_search_list  > tbody > tr').length > 0 )

                                        {

                                            $('#bt_search_list > tbody > tr:first').before(html);

                                        }

                                        else

                                        {

                                            $('#bt_search_list > tbody').append(html);

                                        }

                                }

                         });

                    }

                    else

                    {

                        $.each(data, function (key, val) {

                        html='';

                        rowExist=false;

                               $('#bt_search_list > tbody tr').each(function(bidx, brow){

                                    bt_tagid = $(this);

                                    if( val.bill_det_id == bt_tagid.find('.bill_det_id').val())
                                    {
                                        rowExist = true;
                                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag No Already Exists..'});
                                    }
                                         
                                });
                                if(!rowExist){
                                   
                                      html = 
                                        '<tr>'+
                                        '<td><input type="checkbox" name="bill_det_id[]" class="bill_det_id" value='+val.bill_det_id+'><input type="hidden" name="bill_id[]" class="bill_id" value='+val.bill_id+'><input type="hidden" name="tag_id[]" class="tag_id" value='+val.tag_id+'>'+val.tag_code+'</td>'+
                                        '<td>'+val.gross_wt+'</td>'+
                                        '<td><input type="hidden" name="total_amt[]" class="item_cost" value='+val.item_cost+'>'+val.item_cost+'</td>'+
                                        '<td><a href="#"onClick="remove_sales_trans_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'
                                        '</tr>';
                                       
                                        if($('#bt_search_list  > tbody > tr').length > 0 )
                                        {
                                            $('#bt_search_list > tbody > tr:first').before(html);
                                        }
                                        else
                                        {
                                            $('#bt_search_list > tbody').append(html);
                                        }
                                }
                         });

                    $('#tag_no').val('');

					$("#tag_no").focus();

                }   

                else

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});

					$('#tag_no').val('');

					$("#tag_no").focus();

                }

            },

            

            error:function(error)  

            {

            $("div.overlay").css("display", "none");

            }

        });

    

    }    

	

	function get_sales_transfer_tag_list()

    {

        my_Date = new Date();

        $.ajax({

        url:base_url+ "index.php/admin_ret_sales_transfer/sales_transfer/sales_trans_tag?nocache=" + my_Date.getUTCSeconds(),

        data: {'bt_code':$("#bt_code").val(),'tag_code':$("#tag_no").val(),'old_tag_code':$("#old_tag_no").val(),'from_brn':$("#from_brn").val(),'cat_id':$('#select_category').val(), 'design_id':$("#id_design").val(),'prodId':$("#id_product").val(),'lotno':$("#lotno").val(), 'tag_no':$("#tag_no").val(),'id_metal':$('#select_metal').val()},

        

        dataType:"JSON",

        type:"POST",

        cache:false,

        success:function(data){

        $(".overlay").css("display", "none");

                var searchResList = data;

                $('#total').text(data.length);

              

                if (data!= null && data.length > 0)

                {   

                    $.each(data, function (key, val) {

                    html='';

                    rowExist=false;

                           $('#bt_search_list > tbody tr').each(function(bidx, brow){

                                bt_tagid = $(this);

                                if( val.tag_id == bt_tagid.find('.tag_id').val())

                                {

                                    rowExist = true;

                                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag No Already Exists..'});

                                }

                            });

                            if(!rowExist){

                                let from_branch_country='';

                                let from_branch_state='';

                                

                                let to_branch_country='';

                                let to_branch_state='';

                                

                                var tax_amount=0;

                                var cgst_amt = 0;

                                var sgst_amt = 0;

                                var igst_amt = 0;

                                

                                 $.each(branchArr, function (key, item) { 

									if($("#from_brn").val()==item.id_branch)

									{

									from_branch_country=item.id_country;

									from_branch_state=item.id_state;

									}

									

									if($("#to_brn").val()==item.id_branch)

									{

									to_branch_country=item.id_country;

									to_branch_state=item.id_state;

									}

                                     

                                 });



                                    html = 

                                    '<tr>'+

                                    '<td><input type="checkbox" name="tag_id[]" class="tag_id" value='+val.tag_id+'><input type="hidden" name="piece[]" class="piece" value='+val.piece+'><input type="hidden" name="gross_wt[]" class="gross_wt" value='+val.gross_wt+'>'+val.tag_code+'</td>'+

                                    '<td>'+val.category_name+'</td>'+

                                    '<td>'+val.piece+'</td>'+

                                    '<td>'+val.gross_wt+'</td>'+

                                    '<td>'+val.less_wt+'<input type="hidden" name="less_wt[]" class="less_wt" value='+val.less_wt+'><input type="hidden" name="product_id[]" class="product_id" value='+val.product_id+'><input type="hidden" name="design_id[]" class="design_id" value='+val.design_id+'><input type="hidden" name="id_sub_design[]" class="id_sub_design" value='+val.id_sub_design+'></td>'+

                                    '<td>'+val.net_wt+'<input type="hidden" name="net_wt[]" class="net_wt" value='+val.net_wt+'><input type="hidden" name="metal_code[]" class="metal_code" value='+val.metal_code+'><input type="hidden" name="id_metal[]" class="id_metal" value='+val.id_metal+'><input type="hidden" name="calculation_based_on[]" class="calculation_based_on" value='+val.calculation_based_on+'><input type="hidden" name="purity[]" class="purity" value='+val.purity+'></td>'+

                                    '<td><Select class="form-control calc_type"><option value="1">Per Gram</option><option value="2">Per Piece</option></select></td>'+

                                    '<td><input type="number"  class="form-control pur_cost" value="'+$('#rate_per_gram').val()+'"></td>'+

                                    '<td><input type="hidden" name="total_amt[]" class="item_cost" value=""><span class="total_cost"></span></td>'+

                                    '<td><a href="#"onClick="remove_sales_trans_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

                                    '</tr>';

                                   

                                    if($('#bt_search_list  > tbody > tr').length > 0 )

                                    {

                                        $('#bt_search_list > tbody > tr:first').before(html);

                                    }

                                    else

                                    {

                                        $('#bt_search_list > tbody').append(html);

                                    }

                                    calculate_sales_trans_details();

                            }

                     });

                    $('#tag_no').val('');

                    $('#old_tag_no').val('');

					$("#tag_no").focus();

                }   

                else

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});

					$('#tag_no').val('');

					$('#old_tag_no').val('');

					$("#tag_no").focus();

                }

            },

            

            error:function(error)  

            {

            $("div.overlay").css("display", "none");

            }

        });

    

    }



    

    $(document).on('change','.calc_type',function(){

        calculateSaleBillRowTotal();

    });



	$(document).on('keyup','.pur_cost',function(){

		calculateSaleBillRowTotal();

	});


	// Apply common Rate Per Gram to all existing tag rows
	$(document).on('click','#apply_rate_to_all',function(){
		var commonRate = $('#rate_per_gram').val();
		if(commonRate == '' || commonRate == null || parseFloat(commonRate) <= 0){
			$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please enter a valid Rate Per Gram..'});
			return;
		}
		$('#bt_search_list > tbody tr').each(function(){
			$(this).find('.pur_cost').val(commonRate);
		});
		calculateSaleBillRowTotal();
		$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+'Rate Per Gram applied to all tags..'});
	});

    

    function calculateSaleBillRowTotal()

    {

        $('#bt_search_list > tbody tr').each(function(idx, row){

		    curRow = $(this);

		    

		    var taxable_amt=0;

		    var tax_amount =0;

			// var tot_amount = 0;

		    

		    var piece       = curRow.find('.piece').val();

		    var gross_wt    = curRow.find('.gross_wt').val();

		    var calc_type   = curRow.find('.calc_type').val();

		    var pur_cost    = (curRow.find('.pur_cost').val()!='' ? curRow.find('.pur_cost').val():0);

		    

		    if(calc_type==1)

			{

			    taxable_amt  = parseFloat(parseFloat(gross_wt)*parseFloat(pur_cost)).toFixed(2);

			}

			else{

			    taxable_amt  = parseFloat(parseFloat(piece)*parseFloat(pur_cost)).toFixed(2);

			}

			

			tax_amount       = parseFloat((taxable_amt)*3/100).toFixed(2);



			tot_amount = parseFloat(taxable_amt) + parseFloat(tax_amount);

			

			curRow.find('.item_cost').val(parseFloat(tot_amount).toFixed(2));

			curRow.find('.total_cost').html(parseFloat(tot_amount).toFixed(2));

		    

        });

    }

    

    

    function remove_sales_trans_row(curRow)

    {

    	curRow.remove();

    	calculate_sales_trans_details();

    }



    function calculate_sales_trans_details()

    {

        calculateSaleBillRowTotal();

        var total_pcs = 0;

        var tot_gross_wt = 0;

        var tot_lwt = 0;

        var tot_nwt = 0;

        var total_cost = 0;

         $('#bt_search_list > tbody tr').each(function(bidx, brow){

            bt_tagid = $(this);

            total_pcs += parseFloat(bt_tagid.find('.piece').val());

            tot_gross_wt += parseFloat(bt_tagid.find('.gross_wt').val());

            tot_lwt += parseFloat(bt_tagid.find('.less_wt').val());

            tot_nwt += parseFloat(bt_tagid.find('.net_wt').val());

            total_cost += parseFloat(bt_tagid.find('.item_cost').val());

         });

         $('.tot_bt_pcs').html(parseFloat(total_pcs));

         $('.tot_bt_gross_wt').html(parseFloat(tot_gross_wt).toFixed(3));

         $('.tot_lwt').html(parseFloat(tot_lwt).toFixed(3));

         $('.tot_nwt').html(parseFloat(tot_nwt).toFixed(3));

         $('.total_item_cost').html(parseFloat(total_cost).toFixed(2));

         

    }

    


// ===================================================================
// DEEMED SALES TRANSFER — Transfer Type Switching & AJAX Loaders
// ===================================================================

/**
 * onSalesTransTypeChange(type)
 * Shows/hides the correct table section & tagged-only fields
 * type: 1=Tagged, 2=Non-Tagged, 3=Old Gold, 4=Sales Return, 5=Partly Sold
 */
function onSalesTransTypeChange(type) {
    // Hide all type-specific table sections
    $('.st_non_tagged, .st_purchase_items').css('display', 'none');

    // Hide the inline purchase items date range/search (in the search box)
    $('.st_purchase_inline').css('display', 'none');

    // Tagged-only fields: Design, Tag Code, Old Tag Code, Lot, Rate Per Gram block
    var taggedFields = $('.tagged');
    var rateBlock = $('#rate_per_gram').closest('.row');

    // The main tagged table (#bt_search_list parent)
    var taggedTable = $('#bt_search_list').closest('.row.sales_trans').not('.st_non_tagged, .st_old_gold, .st_sales_return, .st_partly_sold');

    // Search fields row (Lot No, Category, Product, Design, Tag Code, Old Tag + Search button)
    var searchFieldsRow = $('.st_search_fields');

    switch(parseInt(type)) {
        case 1: // Tagged
            searchFieldsRow.css('display', '');
            taggedFields.css('display', 'block');
            rateBlock.css('display', 'block');
            taggedTable.css('display', 'block');
            break;
        case 2: // Non-Tagged
            searchFieldsRow.css('display', '');
            taggedFields.css('display', 'none');
            rateBlock.css('display', 'none');
            taggedTable.css('display', 'none');
            $('.st_non_tagged').css('display', 'block');
            // Auto-populate board rate from rate_details if not already set
            if (typeof rate_details !== 'undefined' && rate_details && $('#nt_board_rate').val() == '') {
                var metalId = $('#select_metal').val();
                // Default to gold rate; use silver if metal is silver (id=2 typically)
                var autoRate = rate_details.goldrate_22ct || 0;
                if (metalId == '2' && rate_details.silverrate_1gm) {
                    autoRate = rate_details.silverrate_1gm;
                }
                if (parseFloat(autoRate) > 0) {
                    $('#nt_board_rate').val(autoRate);
                }
            }
            break;
        case 3: // Purchase Items (OG + SR + PS combined)
            searchFieldsRow.css('display', 'none');
            taggedFields.css('display', 'none');
            rateBlock.css('display', 'none');
            taggedTable.css('display', 'none');
            $('.st_purchase_inline').css('display', '');
            $('.st_purchase_items').css('display', 'block');
            break;
    }

    // Update hidden field
    $('#salesTransType').val(type);
}

// Transfer Type Radio Handler
$('input[type=radio][name="sales_trans_type"]').change(function() {
    onSalesTransTypeChange(this.value);
});


// ===================================================================
// AJAX Loaders for Non-Tagged, Old Gold, Sales Return, Partly Sold
// ===================================================================

/**
 * get_non_tagged_stock_list()
 * Fetches non-tagged stock from controller and populates #st_nt_search_list
 */
function get_non_tagged_stock_list() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_sales_transfer/sales_transfer/getNonTaggedStock?nocache=" + my_Date.getUTCSeconds(),
        data: {
            'from_brn': $("#from_brn").val(),
            'cat_id': $('#select_category').val(),
            'prodId': $("#id_product").val(),
            'id_metal': $('#select_metal').val()
        },
        dataType: "JSON",
        type: "POST",
        cache: false,
        success: function(data) {
            $(".overlay").css("display", "none");
            $('#st_nt_search_list > tbody').empty();

            if (data != null && data.length > 0) {
                $.each(data, function(key, val) {
                    var boardRate = ($('#nt_board_rate').val() != '' ? $('#nt_board_rate').val() : 0);
                    var taxable_amt = parseFloat(val.gross_wt) * parseFloat(boardRate);
                    var tax_amount = (taxable_amt * 3 / 100);
                    var total_amt = taxable_amt + tax_amount;

                    var html =
                        '<tr>' +
                        '<td><input type="checkbox" name="nt_stock_id[]" class="nt_stock_id" value="' + val.nt_stock_id + '">' +
                            '<input type="hidden" class="nt_piece" value="' + val.piece + '">' +
                            '<input type="hidden" class="nt_gross_wt" value="' + val.gross_wt + '">' +
                            '<input type="hidden" class="nt_net_wt" value="' + val.net_wt + '">' +
                            '<input type="hidden" class="nt_pure_wt" value="' + (val.pure_wt || 0) + '">' +
                            '<input type="hidden" class="nt_product_id" value="' + (val.product_id || 0) + '">' +
                            '<input type="hidden" class="nt_cat_id" value="' + (val.cat_id || 0) + '">' +
                        '</td>' +
                        '<td>' + (val.section_name || '-') + '</td>' +
                        '<td>' + (val.product_name || '-') + '</td>' +
                        '<td>' + val.piece + '</td>' +
                        '<td>' + parseFloat(val.gross_wt).toFixed(3) + '</td>' +
                        '<td>' + parseFloat(val.net_wt).toFixed(3) + '</td>' +
                        '<td>' + parseFloat(val.pure_wt || 0).toFixed(3) + '</td>' +
                        '<td><input type="number" class="form-control nt_rate" value="' + boardRate + '" step="any"></td>' +
                        '<td><input type="hidden" class="nt_item_cost" value="' + total_amt.toFixed(2) + '"><span class="nt_total_cost">' + total_amt.toFixed(2) + '</span></td>' +
                        '</tr>';
                    $('#st_nt_search_list > tbody').append(html);
                });
                calculate_st_nt_totals();
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Non-Tagged Stock Found..' });
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}


/**
 * get_old_gold_items_list()
 * Fetches old gold items from controller and populates #st_og_search_list
 */
function get_old_gold_items_list() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_sales_transfer/sales_transfer/getOldGoldItems?nocache=" + my_Date.getUTCSeconds(),
        data: {
            'from_brn': $("#from_brn").val(),
            'cat_id': $('#select_category').val(),
            'id_metal': $('#select_metal').val()
        },
        dataType: "JSON",
        type: "POST",
        cache: false,
        success: function(data) {
            $(".overlay").css("display", "none");
            $('#st_og_search_list > tbody').empty();

            if (data != null && data.length > 0) {
                $.each(data, function(key, val) {
                    var rate = parseFloat(val.rate_per_grm || 0);
                    var taxable_amt = parseFloat(val.gross_wt) * rate;
                    var tax_amount = (taxable_amt * 3 / 100);
                    var total_amt = taxable_amt + tax_amount;

                    var html =
                        '<tr>' +
                        '<td><input type="checkbox" name="og_purchase_id[]" class="og_purchase_id" value="' + val.og_purchase_id + '">' +
                            '<input type="hidden" class="og_piece" value="' + val.piece + '">' +
                            '<input type="hidden" class="og_gross_wt" value="' + val.gross_wt + '">' +
                            '<input type="hidden" class="og_net_wt" value="' + (val.net_wt || val.gross_wt) + '">' +
                            '<input type="hidden" class="og_pure_wt" value="' + (val.pure_wt || 0) + '">' +
                            '<input type="hidden" class="og_cat_id" value="' + (val.cat_id || 0) + '">' +
                        '</td>' +
                        '<td>' + (val.category_name || '-') + '</td>' +
                        '<td>' + val.piece + '</td>' +
                        '<td>' + parseFloat(val.gross_wt).toFixed(3) + '</td>' +
                        '<td>' + parseFloat(val.net_wt || val.gross_wt).toFixed(3) + '</td>' +
                        '<td>' + parseFloat(val.pure_wt || 0).toFixed(3) + '</td>' +
                        '<td><input type="number" class="form-control og_rate" value="' + rate.toFixed(2) + '" step="any"></td>' +
                        '<td><input type="hidden" class="og_item_cost" value="' + total_amt.toFixed(2) + '"><span class="og_total_cost">' + total_amt.toFixed(2) + '</span></td>' +
                        '<td><a href="#" onClick="view_og_detail(' + val.og_purchase_id + ');" class="btn btn-info btn-xs"><i class="fa fa-eye"></i></a></td>' +
                        '</tr>';
                    $('#st_og_search_list > tbody').append(html);
                });
                calculate_st_og_totals();
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Old Gold Items Found..' });
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}


/**
 * get_sales_return_items_list()
 * Fetches sales return items (is_return=1) and populates #st_sr_search_list
 */
function get_sales_return_items_list() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_sales_transfer/sales_transfer/getSalesReturnItems?nocache=" + my_Date.getUTCSeconds(),
        data: {
            'from_brn': $("#from_brn").val(),
            'cat_id': $('#select_category').val(),
            'id_metal': $('#select_metal').val()
        },
        dataType: "JSON",
        type: "POST",
        cache: false,
        success: function(data) {
            $(".overlay").css("display", "none");
            $('#st_sr_search_list > tbody').empty();

            if (data != null && data.length > 0) {
                $.each(data, function(key, val) {
                    var manualRate = ($('#sr_rate_per_gram').val() != '' ? $('#sr_rate_per_gram').val() : (val.rate_per_grm || 0));
                    var taxable_amt = parseFloat(val.gross_wt) * parseFloat(manualRate);
                    var tax_amount = (taxable_amt * 3 / 100);
                    var total_amt = taxable_amt + tax_amount;

                    var html =
                        '<tr>' +
                        '<td><input type="checkbox" name="sr_tag_id[]" class="sr_tag_id" value="' + val.tag_id + '">' +
                            '<input type="hidden" class="sr_piece" value="' + val.piece + '">' +
                            '<input type="hidden" class="sr_gross_wt" value="' + val.gross_wt + '">' +
                            '<input type="hidden" class="sr_net_wt" value="' + (val.net_wt || val.gross_wt) + '">' +
                            '<input type="hidden" class="sr_product_id" value="' + (val.product_id || 0) + '">' +
                            '<input type="hidden" class="sr_design_id" value="' + (val.design_id || 0) + '">' +
                            '<input type="hidden" class="sr_purity" value="' + (val.purity || 0) + '">' +
                            '<input type="hidden" class="sr_cat_id" value="' + (val.cat_id || 0) + '">' +
                        '</td>' +
                        '<td>' + (val.bill_no || '-') + '</td>' +
                        '<td>' + (val.tag_code || '-') + '</td>' +
                        '<td>' + (val.category_name || '-') + '</td>' +
                        '<td>' + val.piece + '</td>' +
                        '<td>' + parseFloat(val.gross_wt).toFixed(3) + '</td>' +
                        '<td>' + parseFloat(val.net_wt || val.gross_wt).toFixed(3) + '</td>' +
                        '<td><input type="number" class="form-control sr_rate" value="' + parseFloat(manualRate).toFixed(2) + '" step="any"></td>' +
                        '<td><input type="hidden" class="sr_item_cost" value="' + total_amt.toFixed(2) + '"><span class="sr_total_cost">' + total_amt.toFixed(2) + '</span></td>' +
                        '</tr>';
                    $('#st_sr_search_list > tbody').append(html);
                });
                calculate_st_sr_totals();
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Sales Return Items Found..' });
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}


/**
 * get_partly_sold_items_list()
 * Fetches partly sold items (is_partial=1) and populates #st_ps_search_list
 */
function get_partly_sold_items_list() {
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_sales_transfer/sales_transfer/getPartlySoldItems?nocache=" + my_Date.getUTCSeconds(),
        data: {
            'from_brn': $("#from_brn").val(),
            'cat_id': $('#select_category').val(),
            'id_metal': $('#select_metal').val()
        },
        dataType: "JSON",
        type: "POST",
        cache: false,
        success: function(data) {
            $(".overlay").css("display", "none");
            $('#st_ps_search_list > tbody').empty();

            if (data != null && data.length > 0) {
                $.each(data, function(key, val) {
                    var manualRate = ($('#ps_rate_per_gram').val() != '' ? $('#ps_rate_per_gram').val() : (val.rate_per_grm || 0));
                    var residual_wt = parseFloat(val.residual_wt || val.gross_wt);
                    var taxable_amt = residual_wt * parseFloat(manualRate);
                    var tax_amount = (taxable_amt * 3 / 100);
                    var total_amt = taxable_amt + tax_amount;

                    var html =
                        '<tr>' +
                        '<td><input type="checkbox" name="ps_tag_id[]" class="ps_tag_id" value="' + val.tag_id + '">' +
                            '<input type="hidden" class="ps_piece" value="' + val.piece + '">' +
                            '<input type="hidden" class="ps_gross_wt" value="' + val.gross_wt + '">' +
                            '<input type="hidden" class="ps_residual_wt" value="' + residual_wt + '">' +
                            '<input type="hidden" class="ps_product_id" value="' + (val.product_id || 0) + '">' +
                            '<input type="hidden" class="ps_design_id" value="' + (val.design_id || 0) + '">' +
                            '<input type="hidden" class="ps_purity" value="' + (val.purity || 0) + '">' +
                            '<input type="hidden" class="ps_cat_id" value="' + (val.cat_id || 0) + '">' +
                        '</td>' +
                        '<td>' + (val.tag_code || '-') + '</td>' +
                        '<td>' + (val.category_name || '-') + '</td>' +
                        '<td>' + val.piece + '</td>' +
                        '<td>' + parseFloat(val.gross_wt).toFixed(3) + '</td>' +
                        '<td>' + residual_wt.toFixed(3) + '</td>' +
                        '<td><input type="number" class="form-control ps_rate" value="' + parseFloat(manualRate).toFixed(2) + '" step="any"></td>' +
                        '<td><input type="hidden" class="ps_item_cost" value="' + total_amt.toFixed(2) + '"><span class="ps_total_cost">' + total_amt.toFixed(2) + '</span></td>' +
                        '</tr>';
                    $('#st_ps_search_list > tbody').append(html);
                });
                calculate_st_ps_totals();
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'No Partly Sold Items Found..' });
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}


// ===================================================================
// Row Recalculation & Totals per Type
// ===================================================================

// Non-Tagged: recalculate on rate change
$(document).on('keyup change', '.nt_rate', function() {
    recalculate_nt_row($(this).closest('tr'));
    calculate_st_nt_totals();
});

function recalculate_nt_row(row) {
    var gwt = parseFloat(row.find('.nt_gross_wt').val());
    var rate = parseFloat(row.find('.nt_rate').val() || 0);
    var taxable_amt = gwt * rate;
    var tax = taxable_amt * 3 / 100;
    var total = taxable_amt + tax;
    row.find('.nt_item_cost').val(total.toFixed(2));
    row.find('.nt_total_cost').html(total.toFixed(2));
}

function calculate_st_nt_totals() {
    var tot_pcs = 0, tot_gwt = 0, tot_nwt = 0, tot_pwt = 0, tot_amt = 0;
    $('#st_nt_search_list > tbody > tr').each(function() {
        tot_pcs += parseFloat($(this).find('.nt_piece').val() || 0);
        tot_gwt += parseFloat($(this).find('.nt_gross_wt').val() || 0);
        tot_nwt += parseFloat($(this).find('.nt_net_wt').val() || 0);
        tot_pwt += parseFloat($(this).find('.nt_pure_wt').val() || 0);
        tot_amt += parseFloat($(this).find('.nt_item_cost').val() || 0);
    });
    $('.st_nt_tot_pcs').html(tot_pcs);
    $('.st_nt_tot_gwt').html(tot_gwt.toFixed(3));
    $('.st_nt_tot_nwt').html(tot_nwt.toFixed(3));
    $('.st_nt_tot_pwt').html(tot_pwt.toFixed(3));
    $('.st_nt_tot_amt').html(tot_amt.toFixed(2));
}

// NT Board Rate apply
$(document).on('click', '#nt_board_rate', function() {
    // Auto-apply is on search; but also add a manual trigger if needed
});

// Old Gold: recalculate on rate change
$(document).on('keyup change', '.og_rate', function() {
    recalculate_og_row($(this).closest('tr'));
    calculate_st_og_totals();
});

function recalculate_og_row(row) {
    var gwt = parseFloat(row.find('.og_gross_wt').val());
    var rate = parseFloat(row.find('.og_rate').val() || 0);
    var taxable_amt = gwt * rate;
    var tax = taxable_amt * 3 / 100;
    var total = taxable_amt + tax;
    row.find('.og_item_cost').val(total.toFixed(2));
    row.find('.og_total_cost').html(total.toFixed(2));
}

function calculate_st_og_totals() {
    var tot_pcs = 0, tot_gwt = 0, tot_nwt = 0, tot_pwt = 0, tot_amt = 0;
    $('#st_og_search_list > tbody > tr').each(function() {
        tot_pcs += parseFloat($(this).find('.og_piece').val() || 0);
        tot_gwt += parseFloat($(this).find('.og_gross_wt').val() || 0);
        tot_nwt += parseFloat($(this).find('.og_net_wt').val() || 0);
        tot_pwt += parseFloat($(this).find('.og_pure_wt').val() || 0);
        tot_amt += parseFloat($(this).find('.og_item_cost').val() || 0);
    });
    $('.st_og_tot_pcs').html(tot_pcs);
    $('.st_og_tot_gwt').html(tot_gwt.toFixed(3));
    $('.st_og_tot_nwt').html(tot_nwt.toFixed(3));
    $('.st_og_tot_pwt').html(tot_pwt.toFixed(3));
    $('.st_og_tot_amt').html(tot_amt.toFixed(2));
}

function view_og_detail(ogId) {
    $('#og_detail_body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
    $('#og_detail_modal').modal('show');

    $.ajax({
        url: base_url + 'index.php/admin_ret_sales_transfer/sales_transfer/getOldGoldDetail',
        data: { 'og_id': ogId },
        dataType: 'JSON',
        type: 'POST',
        success: function(d) {
            if (d && d.old_metal_sale_id) {
                var html =
                    '<div class="row">' +
                    '  <div class="col-md-6">' +
                    '    <table class="table table-bordered table-condensed">' +
                    '      <tr><th colspan="2" class="bg-primary text-center">Purchase Info</th></tr>' +
                    '      <tr><td><strong>Bill No</strong></td><td>' + (d.bill_no || '-') + '</td></tr>' +
                    '      <tr><td><strong>Bill Date</strong></td><td>' + (d.bill_date || '-') + '</td></tr>' +
                    '      <tr><td><strong>Customer</strong></td><td>' + (d.customer_name || '-') + '</td></tr>' +
                    '      <tr><td><strong>Mobile</strong></td><td>' + (d.mobile_no || '-') + '</td></tr>' +
                    '      <tr><td><strong>Branch</strong></td><td>' + (d.branch_name || '-') + '</td></tr>' +
                    '      <tr><td><strong>Category</strong></td><td>' + (d.category_name || '-') + '</td></tr>' +
                    '    </table>' +
                    '  </div>' +
                    '  <div class="col-md-6">' +
                    '    <table class="table table-bordered table-condensed">' +
                    '      <tr><th colspan="2" class="bg-success text-center">Weight & Rate</th></tr>' +
                    '      <tr><td><strong>Pieces</strong></td><td>' + (d.piece || 0) + '</td></tr>' +
                    '      <tr><td><strong>Gross Wt</strong></td><td>' + parseFloat(d.gross_wt || 0).toFixed(3) + ' g</td></tr>' +
                    '      <tr><td><strong>Net Wt</strong></td><td>' + parseFloat(d.net_wt || 0).toFixed(3) + ' g</td></tr>' +
                    '      <tr><td><strong>Pure Wt</strong></td><td>' + parseFloat(d.pure_wt || 0).toFixed(3) + ' g</td></tr>' +
                    '      <tr><td><strong>Stone Wt</strong></td><td>' + parseFloat(d.stone_wt || 0).toFixed(3) + ' g</td></tr>' +
                    '      <tr><td><strong>Less Wt</strong></td><td>' + parseFloat(d.less_wt || 0).toFixed(3) + ' g</td></tr>' +
                    '      <tr><td><strong>Touch %</strong></td><td>' + (d.touch || '-') + '</td></tr>' +
                    '      <tr><td><strong>Purity</strong></td><td>' + (d.purity || '-') + '</td></tr>' +
                    '      <tr><td><strong>Melt %</strong></td><td>' + (d.melt_percent || '-') + '</td></tr>' +
                    '      <tr><td><strong>Rate/Grm</strong></td><td>₹ ' + parseFloat(d.rate_per_grm || 0).toFixed(2) + '</td></tr>' +
                    '      <tr><td><strong>Total Amount</strong></td><td><strong>₹ ' + parseFloat(d.amount || 0).toFixed(2) + '</strong></td></tr>' +
                    '    </table>' +
                    '  </div>' +
                    '</div>';
                $('#og_detail_body').html(html);
            } else {
                $('#og_detail_body').html('<div class="alert alert-warning">No details found for this Old Gold item.</div>');
            }
        },
        error: function() {
            $('#og_detail_body').html('<div class="alert alert-danger">Failed to load details. Please try again.</div>');
        }
    });
}

// Sales Return: recalculate on rate change
$(document).on('keyup change', '.sr_rate', function() {
    recalculate_sr_row($(this).closest('tr'));
    calculate_st_sr_totals();
});

function recalculate_sr_row(row) {
    var gwt = parseFloat(row.find('.sr_gross_wt').val());
    var rate = parseFloat(row.find('.sr_rate').val() || 0);
    var taxable_amt = gwt * rate;
    var tax = taxable_amt * 3 / 100;
    var total = taxable_amt + tax;
    row.find('.sr_item_cost').val(total.toFixed(2));
    row.find('.sr_total_cost').html(total.toFixed(2));
}

function calculate_st_sr_totals() {
    var tot_pcs = 0, tot_gwt = 0, tot_nwt = 0, tot_amt = 0;
    $('#st_sr_search_list > tbody > tr').each(function() {
        tot_pcs += parseFloat($(this).find('.sr_piece').val() || 0);
        tot_gwt += parseFloat($(this).find('.sr_gross_wt').val() || 0);
        tot_nwt += parseFloat($(this).find('.sr_net_wt').val() || 0);
        tot_amt += parseFloat($(this).find('.sr_item_cost').val() || 0);
    });
    $('.st_sr_tot_pcs').html(tot_pcs);
    $('.st_sr_tot_gwt').html(tot_gwt.toFixed(3));
    $('.st_sr_tot_nwt').html(tot_nwt.toFixed(3));
    $('.st_sr_tot_amt').html(tot_amt.toFixed(2));
}

// SR Apply Rate button
$(document).on('click', '#sr_apply_rate', function() {
    var rate = $('#sr_rate_per_gram').val();
    if (rate == '' || parseFloat(rate) <= 0) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please enter a valid Rate Per Gram..' });
        return;
    }
    $('#st_sr_search_list > tbody > tr').each(function() {
        $(this).find('.sr_rate').val(rate);
        recalculate_sr_row($(this));
    });
    calculate_st_sr_totals();
    $.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + 'Rate applied to all Sales Return items.' });
});

// Partly Sold: recalculate on rate change
$(document).on('keyup change', '.ps_rate', function() {
    recalculate_ps_row($(this).closest('tr'));
    calculate_st_ps_totals();
});

function recalculate_ps_row(row) {
    var rwt = parseFloat(row.find('.ps_residual_wt').val());
    var rate = parseFloat(row.find('.ps_rate').val() || 0);
    var taxable_amt = rwt * rate;
    var tax = taxable_amt * 3 / 100;
    var total = taxable_amt + tax;
    row.find('.ps_item_cost').val(total.toFixed(2));
    row.find('.ps_total_cost').html(total.toFixed(2));
}

function calculate_st_ps_totals() {
    var tot_pcs = 0, tot_gwt = 0, tot_rwt = 0, tot_amt = 0;
    $('#st_ps_search_list > tbody > tr').each(function() {
        tot_pcs += parseFloat($(this).find('.ps_piece').val() || 0);
        tot_gwt += parseFloat($(this).find('.ps_gross_wt').val() || 0);
        tot_rwt += parseFloat($(this).find('.ps_residual_wt').val() || 0);
        tot_amt += parseFloat($(this).find('.ps_item_cost').val() || 0);
    });
    $('.st_ps_tot_pcs').html(tot_pcs);
    $('.st_ps_tot_gwt').html(tot_gwt.toFixed(3));
    $('.st_ps_tot_rwt').html(tot_rwt.toFixed(3));
    $('.st_ps_tot_amt').html(tot_amt.toFixed(2));
}

// PS Apply Rate button
$(document).on('click', '#ps_apply_rate', function() {
    var rate = $('#ps_rate_per_gram').val();
    if (rate == '' || parseFloat(rate) <= 0) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: '' + "</br>" + 'Please enter a valid Rate Per Gram..' });
        return;
    }
    $('#st_ps_search_list > tbody > tr').each(function() {
        $(this).find('.ps_rate').val(rate);
        recalculate_ps_row($(this));
    });
    calculate_st_ps_totals();
    $.toaster({ priority: 'success', title: 'Success!', message: '' + "</br>" + 'Rate applied to all Partly Sold items.' });
});

// Select-All checkbox handlers for each type table
$(document).on('change', '#st_nt_select_all', function() {
    var isChecked = $(this).is(':checked');
    $('#st_nt_search_list > tbody input[name="nt_stock_id[]"]').prop('checked', isChecked);
});
$(document).on('change', '#st_og_select_all', function() {
    var isChecked = $(this).is(':checked');
    $('#st_og_search_list > tbody input[name="og_purchase_id[]"]').prop('checked', isChecked);
});
$(document).on('change', '#st_sr_select_all', function() {
    var isChecked = $(this).is(':checked');
    $('#st_sr_search_list > tbody input[name="sr_tag_id[]"]').prop('checked', isChecked);
});
$(document).on('change', '#st_ps_select_all', function() {
    var isChecked = $(this).is(':checked');
    $('#st_ps_search_list > tbody input[name="ps_tag_id[]"]').prop('checked', isChecked);
});

// ===================================================================
// End Deemed Sales Transfer JS
// ===================================================================


function validateSalesRequestRow()

{

    var validate = true;

	$('#bt_search_list > tbody  > tr').each(function(index, tr) {

	    if($(this).find("input[name='tag_id[]']:checked").is(":checked"))

	    {

	        if($(this).find('.pur_cost').val() == "" || $(this).find('.pur_cost').val() == 0 || $(this).find('.cat_id').val() ==""){

    			validate = false;

    		}

	    }

		

	});

	return validate;

}



	

$('#sales_trans_submit').on('click',function(){

     $('#sales_trans_submit').prop('disabled',true);

    trans_type =  $("input[name='sales_transfer_item_type']:checked").val();

    

    if(trans_type==1) // Sales Transfer Request

    {

        // Validate metal selection if required
        if($('#is_metal_for_billing').val()=='1' && ($('#select_metal').val()=='' || $('#select_metal').val()==null))
        {
            $("div.overlay").css("display", "none"); 
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select Metal..'});
            $('#sales_trans_submit').prop('disabled',false);
            return;
        }

        var salesTransType = parseInt($('#salesTransType').val()) || 1;

        // === Type 1: Tagged (original flow) ===
        if(salesTransType == 1) {
            if(validateSalesRequestRow()) {
                if($("input[name='tag_id[]']:checked").val()) {
                    var selected = [];
                    var item_cost = 0;

                    $("#bt_search_list tbody tr").each(function(index, value) {
                        if($(value).find("input[name='tag_id[]']:checked").is(":checked")) {
                            item_cost += parseFloat($(value).find(".item_cost").val());
                            transData = {
                                'tag_id'                : $(value).find(".tag_id").val(),
                                'piece'                 : $(value).find(".piece").val(),
                                'gross_wt'              : $(value).find(".gross_wt").val(),
                                'less_wt'               : $(value).find(".less_wt").val(),
                                'net_wt'                : $(value).find(".net_wt").val(),
                                'metal_code'            : $(value).find(".metal_code").val(),
                                'product_id'            : $(value).find(".product_id").val(),
                                'design_id'             : $(value).find(".design_id").val(),
                                'id_sub_design'         : $(value).find(".id_sub_design").val(),
                                'calculation_based_on'  : $(value).find(".calculation_based_on").val(),
                                'purity'                : $(value).find(".purity").val(),
                                'id_metal'              : $(value).find(".id_metal").val(),
                                'item_cost'             : $(value).find(".item_cost").val(),
                                'calc_type'             : $(value).find(".calc_type").val(),
                                'rate_per_grm'          : $(value).find(".pur_cost").val(),
                                'bt_code'               : $(value).find(".bt_code").val(),
                            };
                            selected.push(transData);
                        }
                    });
                    $("div.overlay").css("display", "block");
                    create_sales_transfer(selected, item_cost);
                } else {
                    $("div.overlay").css("display", "none");
                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select At Least One Tag..'});
                    $('#sales_trans_submit').prop('disabled',false);
                }
            } else {
                $("div.overlay").css("display", "none");
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Purchase Cost..'});
                $('#sales_trans_submit').prop('disabled',false);
            }
        }

        // === Type 2: Non-Tagged ===
        else if(salesTransType == 2) {
            // Validate board rate before submit
            var ntBoardRate = parseFloat($('#nt_board_rate').val()) || 0;
            if (ntBoardRate <= 0) {
                $("div.overlay").css("display", "none");
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please enter the Board Rate before submitting Non-Tagged items..'});
                $('#sales_trans_submit').prop('disabled',false);
                return;
            }
            if($("input[name='nt_stock_id[]']:checked").length > 0) {
                var selected = [];
                var item_cost = 0;

                $("#st_nt_search_list tbody tr").each(function(index, value) {
                    if($(value).find("input[name='nt_stock_id[]']:checked").is(":checked")) {
                        item_cost += parseFloat($(value).find(".nt_item_cost").val());
                        transData = {
                            'nt_stock_id'   : $(value).find(".nt_stock_id").val(),
                            'piece'         : $(value).find(".nt_piece").val(),
                            'gross_wt'      : $(value).find(".nt_gross_wt").val(),
                            'net_wt'        : $(value).find(".nt_net_wt").val(),
                            'product_id'    : $(value).find(".nt_product_id").val(),
                            'rate_per_grm'  : $(value).find(".nt_rate").val(),
                            'item_cost'     : $(value).find(".nt_item_cost").val(),
                        };
                        selected.push(transData);
                    }
                });
                $("div.overlay").css("display", "block");
                create_sales_transfer(selected, item_cost);
            } else {
                $("div.overlay").css("display", "none");
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select At Least One NT Stock Item..'});
                $('#sales_trans_submit').prop('disabled',false);
            }
        }

        // === Type 3: Purchase Items (OG + SR + PS combined — BT parity) ===
        else if(salesTransType == 3) {
            var selected = [];
            var item_cost = 0;

            // Collect from unified st_oldMetalDetail array (mirrors BT's oldMetalDetail pattern)
            $.each(st_oldMetalDetail, function(key, items) {
                if(items.is_checked == 1) {
                    item_cost += parseFloat(items.amount) || 0;
                    selected.push({
                        'item_type'     : items.transfer_items,  // 1=Old Metal, 2=Sales Return, 3=Partly Sold
                        'trans_id'      : items.trans_id,
                        'bill_det_id'   : items.bill_det_id,
                        'tag_id'        : items.tag_id,
                        'gross_wt'      : items.gross_wt,
                        'net_wt'        : items.net_wt,
                        'dia_wt'        : items.dia_wt,
                        'is_non_tag'    : items.is_non_tag,
                        'item_cost'     : items.amount,
                        'salesTransType': items.transfer_items == 1 ? 3 
                                        : items.transfer_items == 2 ? 4 
                                        : 5
                    });
                }
            });

            if(selected.length > 0) {
                $("div.overlay").css("display", "block");
                create_sales_transfer(selected, item_cost);
            } else {
                $("div.overlay").css("display", "none");
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Select At Least One Purchase Item (Old Gold / Sales Return / Partly Sold)..'});
                $('#sales_trans_submit').prop('disabled',false);
            }
        }

    }

    else if(trans_type==2)

    {

        if($("input[name='bill_id[]']:checked").val())

        {

            var selected = [];

            var approve=false;

            $("#bt_search_download_list tbody tr").each(function(index, value)

            {

                if($(value).find("input[name='bill_id[]']:checked").is(":checked"))

                {

                    transData = { 

                    'bill_id'                : $(value).find(".bill_id").val(),

                    }

                    selected.push(transData);

                }

            });

            req_data = selected;

            update_sales_transfer_request(req_data);

        }

        else

        {

            $("div.overlay").css("display", "none"); 

             $('#sales_trans_submit').prop('disabled',false);

            alert('Please Select Any One Bill.');

        }

    }

});



function create_sales_transfer(req_data,item_cost)

{

   

    my_Date = new Date();

    

    $.ajax({

    url:base_url+ "index.php/admin_ret_sales_transfer/create_sales_transfer?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

    data:  {'from_brn':$('#from_brn').val(),'to_brn':$('#to_brn').val(),'req_data':req_data,'tot_bill_amount':item_cost,'form_secret':$('#form_secret').val(),'id_metal':($('#select_metal').length > 0 && $('#select_metal').val() != '' ? $('#select_metal').val() : (req_data.length > 0 ? req_data[0].id_metal : '')),'remark':$('#remark').val(),'salesTransType':$('#salesTransType').val()},

    type:"POST",

    async:false,

    dataType: "json",

    success:function(data){

       	if(data.status)

		{

		    

		    $.toaster({ priority : 'success', title : 'Warning!', message : ''+"</br>"+data.message});

			window.open( base_url+'index.php/admin_ret_billing/billing_invoice/'+data.id,'_blank');

			$('#sales_trans_submit').prop('disabled',false);

			window.location.reload();

		}

		else

		{

		    $("div.overlay").css("display", "none"); 

		    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

		    $('#sales_trans_submit').prop('disabled',false);

		}

		

    },

    error:function(error)  

    {

        console.log(error);

        $("div.overlay").css("display", "none"); 

    }	 

    });

}





function get_metal_rates_by_branch()

{

	var id_branch = '';

	my_Date = new Date();

	$.ajax({

		url:base_url+ "index.php/admin_ret_tagging/get_metal_rates_by_branch?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

		data:  {'id_branch':id_branch},

		type:"POST",

		dataType: "json",

		async:false,

		success:function(data){

			rate_details=data;

		},

		error:function(error)  

		{

			$("div.overlay").css("display", "none");

		}

	});

}









function update_sales_transfer_request(req_data)

{

    $('#sales_ret_trans_submit').prop('disabled',true);

    my_Date = new Date();

    $("div.overlay").css("display", "block"); 

    $.ajax({

    url:base_url+ "index.php/admin_ret_sales_transfer/update_sales_transfer_request?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),

    data:  {'from_brn':$('#from_brn').val(),'to_brn':$('#to_brn').val(),'req_data':req_data,'bill_no':$('#bill_no').val(),'form_secret':$('#form_secret').val()},

    type:"POST",

    async:false,

    dataType: "json",

    success:function(data){

       	if(data.status)

		{

		    $("div.overlay").css("display", "none"); 

		    $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.message});

		    $('#sales_ret_trans_submit').prop('disabled',false);

		}

		else

		{

		    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});

		    $('#sales_ret_trans_submit').prop('disabled',false);

		}

		

		window.location.reload();

        $("div.overlay").css("display", "none"); 

    },

    error:function(error)  

    {

        console.log(error);

        $("div.overlay").css("display", "none"); 

    }	 

    });

}









	function get_sales_return_branch_trasnfer()

	

	{

	    my_Date = new Date();

        $.ajax({

        url:base_url+ "index.php/admin_ret_brntransfer/sales_transfer/sales_return_trans_tag?nocache=" + my_Date.getUTCSeconds(),

        data: {'tag_code':$("#tag_code").val(),'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val()},

        dataType:"JSON",

        type:"POST",

        cache:false,

        success:function(data){

        $(".overlay").css("display", "none");

                var searchResList = data;

                $('#total').text(data.length);

              

                if (data!= null && data.length > 0)

                {   

                    

                    

                    

                     $.each(data, function (key, val) {

                    html='';

                    rowExist=false;

                           $('#bt_search_list > tbody tr').each(function(bidx, brow){

                                bt_tagid = $(this);

                                if( val.tag_id == bt_tagid.find('.tag_id').val())

                                {

                                    rowExist = true;

                                    //$.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Exists..'});

                                }

                                     

                            });

                            if(!rowExist){

                                let from_branch_country='';

                                let from_branch_state='';

                                

                                let to_branch_country='';

                                let to_branch_state='';

                                

                                var tax_amount=0;

                                var cgst_amt = 0;

                                var sgst_amt = 0;

                                var igst_amt = 0;

                                

                                 $.each(branchArr, function (key, item) { 

                                     if($("#from_brn").val()==item.id_branch)

                                     {

                                        from_branch_country=item.id_country;

                                        from_branch_state=item.id_state;

                                     }

                                     

                                     if($("#to_brn").val()==item.id_branch)

                                     {

                                        to_branch_country=item.id_country;

                                        to_branch_state=item.id_state;

                                     }

                                     

                                 });

                                var taxable_amt  = parseFloat(parseFloat(val.gross_wt)*parseFloat(rate_details.silverrate_1gm)).toFixed(2);

                                if(from_branch_country==to_branch_country)

                                {

                                    tax_amount       = parseFloat((taxable_amt)*3/100).toFixed(2);

                                    if(from_branch_state==to_branch_state)

                                    {

                                        cgst_amt=parseFloat(parseFloat(tax_amount)/2).toFixed(2);

                                        sgst_amt=parseFloat(parseFloat(tax_amount)/2).toFixed(2);

                                    }else{

                                        igst_amt=tax_amount;

                                    }

                                }

                                

                                var total_amt    = parseFloat(parseFloat(taxable_amt)+parseFloat(tax_amount)).toFixed(2);

                                    html = 

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

                                    '<td><input type="hidden" name="rate_per_grm[]" class="rate_per_grm" value='+rate_details.silverrate_1gm+'><input type="hidden" name="igst_amt[]" class="igst_amt" value='+igst_amt+'><input type="hidden" name="sgst_amt[]" class="sgst_amt" value='+sgst_amt+'><input type="hidden" name="purity[]" class="purity" value='+val.purity+'><input type="hidden" name="product_id[]" class="product_id" value='+val.product_id+'><input type="hidden" name="design_id[]" class="design_id" value='+val.design_id+'><input type="hidden" name="calculation_based_on[]" class="calculation_based_on" value='+val.calculation_based_on+'><input type="hidden" name="item_cost[]" class="item_cost" value='+total_amt+'><input type="hidden" name="tax_amount[]" class="tax_amount" value='+tax_amount+'><input type="hidden" name="cgst_amt[]" class="cgst_amt" value='+cgst_amt+'>'+total_amt+'</td>'+

                                    '<td><a href="#"onClick="remove_sales_trans_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>'

                                    '</tr>';

                                   

                                    if($('#bt_search_list  > tbody > tr').length > 0 )

                                    {

                                        $('#bt_search_list > tbody > tr:first').before(html);

                                    }

                                    else

                                    {

                                        $('#bt_search_list > tbody').append(html);

                                    }

                                    console.log('rate_per_gram:'+rate_details.silverrate_1gm);

                                    console.log('taxable_amt:'+taxable_amt);

                                    console.log('tax_amount:'+tax_amount);

                                    console.log('total_amt:'+total_amt);

                                    

                            }

                     });

                    

                    calculate_sales_trans_details();

                    

                    

                }   

                else

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'No Records Found..'});

					$('#tag_no').val('');

					$("#tag_no").focus();

                }

            },

            error:function(error)  

            {

            $("div.overlay").css("display", "none");

            }

        });

	}





$('#sales_return_trans_select_all').click(function(event) {

	$("#bt_search_download_list tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

});



$('#select_all').click(function(event) {

	$("#bt_search_list tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));

});





function get_ActiveMetals()

{

	$.ajax({

		type: 'GET',

		url: base_url+'index.php/admin_ret_catalog/ret_product/active_metal',

		dataType:'json',

		success:function(data){

			var id =  $("#select_metal").val();

		    $.each(data,function (key, item) {

			   		$('#select_metal').append(

						$("<option></option>")

						  .attr("value", item.id_metal)

						  .text(item.metal)

					);

			});

			$("#select_metal").select2(

			{

				placeholder:"Select Metal",

				allowClear: true		    

			});  	

		}

	});

}



$('#select_metal').on('change',function(){

   if(ctrl_page[1]=='sales_transfer' && ctrl_page[2]=='add')

   {

       $('#bt_search_list > tbody').empty();

   }

});



function get_ActiveCategory()

{

    $.ajax({

	type: 'GET',

	url: base_url+'index.php/admin_ret_catalog/category/active_category',

	dataType:'json',

	success:function(data){

		var id =  $("#select_category").val();

		$.each(data, function (key, item) {   

		    $("#select_category").append(

		    $("<option></option>")

		    .attr("value", item.id_ret_category)    

		    .text(item.name)  

		    );

		});

		$("#select_category").select2(

		{

			placeholder:"Select Category",

			allowClear: true		    

		});





		    $("#select_category").select2("val",(id!='' && id>0?id:''));

		    $(".overlay").css("display", "none");

		}

	});

}



$("#select_calculation_type").select2(

	{

		placeholder:"Select Calculation Type",

		allowClear: true		    

	});





//SALES TRANSFER







$('#sales_ret_trans_submit').on('click',function(){

      $("div.overlay").css("display", "block"); 

    trans_type =  $("input[name='sales_ret_transfer_item_type']:checked").val();

    $('#sales_ret_trans_submit').prop('disabled',true);

    if(trans_type==1) // Sales ret Transfer Request

    {

        var is_aganist_bill =  $("input[name='aganist_bill']:checked").val();
        
        if($("input[name='bill_det_id[]']:checked").val())
        {
            var selected = [];
            var approve=false;
            var item_cost=0;
            $("#bt_search_list tbody tr").each(function(index, value)
            {

                var selected = [];

                var approve=false;

                var item_cost=0;

                $("#bt_search_list tbody tr").each(function(index, value)

                {

                if($(value).find("input[name='cat_id[]']:checked").is(":checked"))

                {

                    item_cost+=parseFloat($(value).find(".item_cost").val());

                    transData = { 

                    'cat_id'                : $(value).find(".cat_id").val(),

                    'item_cost'             : $(value).find(".item_cost").val(),

                    'rate_per_grm'          : $(value).find(".rate_per_grm").val(),

                    'salesTransType'        : $(value).find(".sales_trans_type").val() || '1',

                    'gross_wt'              : $(value).find(".gross_wt").val() || '0',

                    'piece'                 : $(value).find(".piece").val() || '1',

                    'st_bill_no'            : $(value).find(".st_bill_no").val() || '',

                    }

                    selected.push(transData);	

                }
                selected.push(transData);	
            }
            })
            req_data = selected;
            create_sales_ret_transfer(req_data,item_cost);
        }
        else
        {
            $('#sales_ret_trans_submit').prop('disabled',false);
            alert('Please Select Any One Tag.');
            $("div.overlay").css("display", "none"); 
        }

    }

    else if(trans_type==2)

    {

        if($("input[name='bill_id[]']:checked").val())

        {

            var selected = [];

            var approve=false;

            $("#bt_search_download_list tbody tr").each(function(index, value)

            {

                if($(value).find("input[name='bill_id[]']:checked").is(":checked"))

                {

                    transData = { 

                    'bill_id'                : $(value).find(".bill_id").val(),

                    }

                    selected.push(transData);	

                }

            });

            req_data = selected;

            update_sales_ret_transfer_request(req_data);

        }

        else

        {

            $('#sales_ret_trans_submit').prop('disabled',false);

            alert('Please Select Any One Bill.');

            $("div.overlay").css("display", "none"); 

        }

    }

});





function create_sales_ret_transfer(req_data,item_cost)
{
    my_Date = new Date();

    $.ajax({
    url: base_url + "index.php/admin_ret_sales_transfer/create_sales_ret_transfer?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
    data: {
        'from_brn': $('#from_brn').val(),
        'to_brn': $('#to_brn').val(),
        'req_data': req_data,
        'tot_bill_amount': item_cost,
        'form_secret': $('#form_secret').val(),
        'id_metal': ($('#select_metal').length > 0 && $('#select_metal').val() != '' ? $('#select_metal').val() : (req_data.length > 0 ? req_data[0].id_metal : '')),
        'remark': $('#remark').val(),
        'salesTransType': $('#salesRetTransType').val(),
        'against_bill_no': (req_data.length > 0 && req_data[0].st_bill_no ? req_data[0].st_bill_no : '')
    },
    type: "POST",
    async: false,
    dataType: "json",
    success: function(data) {
        if(data.status) {
            $("div.overlay").css("display", "none");
            $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.message});
            window.open(base_url+'index.php/admin_ret_sales_transfer/sales_transfer/print/'+data.id,'_blank');
            $('#sales_ret_trans_submit').prop('disabled',false);
            window.location.reload();
        } else {
            $("div.overlay").css("display", "none");
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
            $('#sales_ret_trans_submit').prop('disabled',false);
        }
    },
    error: function(error) {
        console.log(error);
        $("div.overlay").css("display", "none");
        $('#sales_ret_trans_submit').prop('disabled',false);
    }
    });
}





function update_sales_ret_transfer_request(req_data)
{
    my_Date = new Date();
    $("div.overlay").css("display", "block");

    $.ajax({
    url: base_url + "index.php/admin_ret_sales_transfer/update_sales_ret_transfer?nocache=" + my_Date.getUTCSeconds()+''+my_Date.getUTCMinutes()+''+my_Date.getUTCHours(),
    data: {
        'from_brn': $('#from_brn').val(),
        'to_brn': $('#to_brn').val(),
        'req_data': req_data,
        'form_secret': $('#form_secret').val()
    },
    type: "POST",
    async: false,
    dataType: "json",
    success: function(data) {
        if(data.status) {
            $("div.overlay").css("display", "none");
            $.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+data.message});
        } else {
            $("div.overlay").css("display", "none");
            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+data.message});
        }
        window.location.reload();
    },
    error: function(error) {
        console.log(error);
        $("div.overlay").css("display", "none");
    }
    });
}







/*Sales Transfer Tag Scan*/



function bill_download_by_scan(data)

{

    console.log(data);

    console.log((data.length > 0));

    if(data.length > 0)

    {

        $('#bill_approval_list_by_scan').css('display','table');



        $("#tag_scan_code").css("display", "block");



        $("#scan_tag_no").focus();	



        scan_dwload_data = data[0].bill_tags;

        var $actual_pcs = 0;

        $.each(scan_dwload_data, function (key, item) 

        {

            $actual_pcs += parseFloat(item.piece);

            console.log('act_pcs',$actual_pcs);

            $('#actual_pcs_dnload').val( $actual_pcs);

        });



        var html='';



        html+='<tr>'+

            '<td>'+data[0].bill_no+'</td>'+

            '<td>'+data[0].bill_date+'</td>'+

            '<td>'+data[0].piece+'</td>'+

            '<td>'+parseFloat(data[0].gross_wt).toFixed(3)+'</td>'+

        '</tr>';

        $('#bill_approval_list_by_scan > tbody').append(html);



    }

}



$(document).on('keyup', '#scan_tag_no', function(e) {



    if(e.which==13)

    {

        console.log(scan_dwload_data);



        var scanned_tags_list = localStorage.getItem("scanned_tags");



        var stored_tags = JSON.parse(scanned_tags_list);



        console.log(stored_tags);



        if(Array.isArray(stored_tags))

		{

            $.each(stored_tags, function (key, item) {

                if($("#scan_tag_no").val() == item)

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Scanned..'});

                    $("#scan_tag_no").val("");

                    $("#scan_tag_no").focus();

                }

            });

		}



        getscan_TagSearchList($("#scan_tag_no").val(),'');

        $("#scan_tag_no").val("");

        $("#scan_tag_no").focus();

    }

    

});



$(document).on('keyup', '#old_tag_code', function(e) {

   

    if(e.which==13)

    {

        console.log(scan_dwload_data);



        var scanned_tags_list = localStorage.getItem("scanned_tags");



        var stored_tags = JSON.parse(scanned_tags_list);



        console.log(stored_tags);



        if(Array.isArray(stored_tags))

		{

            $.each(stored_tags, function (key, item) {

                if($("#old_tag_code").val() == item)

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Scanned..'});

                    $("#old_tag_code").val("");

                    $("#old_tag_code").focus();

                }

            });

		}



       getscan_TagSearchList('',$("#old_tag_code").val());

       $("#old_tag_code").val("");

        $("#old_tag_code").focus();

    }

    

});





function getscan_TagSearchList(tag_code,old_tag_code)

{

    if($('#scan_tag_no').val()=='' && $('#old_tag_code').val()=='')

    {

        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Please Enter The Tag Code..'});

    }

    else

    {

        $('#bill_dwnload_list').css('display','block');

        $.ajax({

            url:base_url+ "index.php/admin_ret_sales_transfer/sales_transfer/getTagsByFilter?nocache=" + my_Date.getUTCSeconds(),

            data: {'bill_no':$("#bill_no").val(),'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val(),'fin_year_code':$('#fin_year_code').val(),'tag_code':$('#scan_tag_no').val(),'old_tag_code':$('#old_tag_code').val()},

            dataType:"JSON",

            type:"POST",

            cache:false,

            success:function(data)

            {

                console.log(data)

                if(data.length == 0)

    			{

    				 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already download..'});

    			}

                else

                {

                    if(data!=null && data.length>0)

                    {

                        html='';

                        rowExist=false;

    

                        $('#bill_dwnload_list > tbody tr').each(function(bidx, brow)

                        {

                            bill_tagid = $(this);

                            if(bill_tagid.find('.tag_code').val() != '')

                            {

                                if( tag_code == bill_tagid.find('.tag_code').val())

                                {

                                    rowExist = true;

                                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already download..'});

                                } 

    

                            }

                        });

    

                        if(!rowExist)

                        {

                            $.each(data,function(key,val)

                            {

                                html+='<tr>'+

                                   

                                    '<td><input type="hidden" name="tag_code[]" class="tag_id" value='+val.tag_id+'>'+val.tag_code+'</td>'+

                                    '<td><input type="hidden" name="product[]" class="product" value='+val.design+'>'+val.product_name+'</td>'+

                                    '<td><input type="hidden" name="piece[]" class="piece" value='+val.piece+'>'+val.piece+'</td>'+

                                    '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value='+val.gross_wt+'>'+val.gross_wt+'</td>'+

                                 

                                '</tr>'; 

                                

                                $.ajax({

                                    type: 'POST',		

    								url : base_url + 'index.php/admin_ret_sales_transfer/update_TagScan',

                                    dataType : 'json',	

                                    data : {'bill_id':val.bill_id,'tag_code':val.tag_code,'tag_id':val.tag_id,

                                    'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val(),'actual_pcs':$('#actual_pcs_dnload').val()},

                                    success  : function(data)

                                    {

                                        if(data != '')

    								  {

    									 if(data == 'completed')

    									 {

    										localStorage.clear();

    										$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+'This Bill Downloaded Successfully..'});

    										$("#scan_tag_no").prop('disabled', true);

    										$("#old_tag_code").prop('disabled', true);

    										$("#scan_tag_no").blur(); 

    										window.location.reload(); 

    										

    									 }else

    									 {

    										 

    										    //scanned_tags.push(val.tag_code);

    											//localStorage.setItem('scanned_tags', JSON.stringify(scanned_tags));

    											

    											dnloaded_tags = localStorage.getItem("scanned_tags");

    										  if (Array.isArray(JSON.parse(dnloaded_tags))) {

    											

    											dnloaded_tags_new = JSON.parse(dnloaded_tags).push(val.tag_code);

    											

    											localStorage.setItem("scanned_tags", JSON.stringify(dnloaded_tags_new));

    										  }else {

    											 

    											scanned_tags.push(val.tag_code);

    											localStorage.setItem('scanned_tags', JSON.stringify(scanned_tags));

    										   

    										  }

    											

    										 $("#scan_tag_no").focus();

    										$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+'Tag Downloaded Successfully..'});

    									 }

    								  }

                                     else

                                        {

                                            $("#scan_tag_no").focus();

                                            $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Unable to download this tag..'});

                                        }

    

                                    }

                                })

    

                                if($('#bill_dwnload_list  > tbody > tr').length > 0 )

                                {

                                $('#bill_dwnload_list > tbody > tr:first').before(html);

                                }

                                else

                                {

                                $('#bill_dwnload_list > tbody').append(html);

                                }

    

                            })

    

                        }

                    }

                }

            }

        });

    }

}



/*Sales Transfer Tag Scan*/





/*Sales Return Transfer Tag Scan*/



function ret_bill_download_by_scan(data)

{

    console.log(data);



    if(data.length>0)

    {

        $('#ret_bill_approval_list_by_scan').css('display','table');



        $("#ret_tag_scan_code").css("display", "block");



        $("#ret_scan_tag_no").focus();	



        ret_scan_dwload_data = data[0].ret_bill_tags;



        var actual_ret_pcs = 0;



        $.each(ret_scan_dwload_data,function(key,item)

        {

            actual_ret_pcs+=parseFloat(item.piece);



            $('#ret_actual_pcs_dnload').val(actual_ret_pcs);



        })



        var html=''



        html+='<tr>'+

            '<td>'+data[0].bill_no+'</td>'+

            '<td>'+data[0].bill_date+'</td>'+

            '<td>'+data[0].piece+'</td>'+

            '<td>'+parseFloat(data[0].gross_wt).toFixed(3)+'</td>'+

        '</tr>';



        $('#ret_bill_approval_list_by_scan > tbody').append(html);



    }



}







$("#ret_scan_tag_no").keypress(function(e) 

{

    if(e.which==13)

    {

        console.log(ret_scan_dwload_data);



        var scanned_tags_list = localStorage.getItem("scanned_tags");



        var stored_tags = JSON.parse(scanned_tags_list);



        console.log(stored_tags);



        if(Array.isArray(stored_tags))

		{

            $.each(stored_tags, function (key, item) {

                if($("#ret_scan_tag_no").val() == item)

                {

                    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already Scanned..'});

                    $("#ret_scan_tag_no").val("");

                    $("#ret_scan_tag_no").focus();

                }

            });

		}



        $.each(ret_scan_dwload_data, function (key, item) 

        {

			console.log(item.tag_code);

            if ($("#ret_scan_tag_no").val() == item.tag_code)

            {

                get_retscan_TagSearchList($("#ret_scan_tag_no").val());

                $("#ret_scan_tag_no").val("");

                $("#ret_scan_tag_no").focus();

            }

	    });

    }

    

});







function get_retscan_TagSearchList(tag_code)

{

    console.log(tag_code);



    $('#ret_bill_dwnload_list').css('display','block');



    $.ajax({

        url:base_url+ "index.php/admin_ret_sales_transfer/sales_transfer/getReturnTagsByFilter?nocache=" + my_Date.getUTCSeconds(),

        data: {'bill_no':$("#bill_no").val(),'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val(),'fin_year_code':$('#fin_year_code').val(),'tag_code':$('#ret_scan_tag_no').val()},

        dataType:"JSON",

        type:"POST",

        cache:false,

        success:function(data)

        {

            console.log(data)

            if(data.length == 0)

			{

				 $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already download..'});

			}

            else

            {

                if(data!=null && data.length>0)

                {

                    html='';

                    rowExist=false;



                    $('#ret_bill_dwnload_list > tbody tr').each(function(bidx, brow)

                    {

                        ret_bill_tagid = $(this);

                        if(ret_bill_tagid.find('.ret_tag_code').val() != '')

                        {

                            if( tag_code == ret_bill_tagid.find('.ret_tag_code').val())

                            {

                                rowExist = true;

                                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Tag Already download..'});

                            } 



                        }

                    });



                    if(!rowExist)

                    {

                        $.each(data,function(key,val)

                        {

                            html+='<tr>'+

                               

                                '<td><input type="hidden" name="tag_code[]" class="tag_id" value='+val.tag_id+'>'+val.tag_code+'</td>'+

                                '<td><input type="hidden" name="product[]" class="product" value='+val.product+'>'+val.product_name+'</td>'+

                                '<td><input type="hidden" name="piece[]" class="piece" value='+val.piece+'>'+val.piece+'</td>'+

                                '<td><input type="hidden" name="gross_wt[]" class="gross_wt" value='+val.gross_wt+'>'+val.gross_wt+'</td>'+

                             

                            '</tr>'; 

                            

                            $.ajax({

                                type: 'POST',		

								url : base_url + 'index.php/admin_ret_sales_transfer/update_ret_TagScan',

                                dataType : 'json',	

                                data : {'bill_id':val.bill_id,'tag_code':val.tag_code,'tag_id':val.tag_id,'ref_bill_id':val.ref_bill_id,

                                'from_brn':$("#from_brn").val(),'to_brn':$("#to_brn").val(),'actual_pcs':$('#ret_actual_pcs_dnload').val()},

                                success  : function(data)

                                {

                                    if(data != '')

								  {

									 if(data == 'completed')

									 {

										localStorage.clear();

										$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+'This Bill Downloaded Successfully..'});

										$("#ret_scan_tag_no").prop('disabled', true);

										$("#ret_scan_tag_no").blur(); 

										window.location.reload(); 

										

									 }else

									 {

										 

										    //scanned_tags.push(val.tag_code);

											//localStorage.setItem('scanned_tags', JSON.stringify(scanned_tags));

											

											dnloaded_tags = localStorage.getItem("scanned_tags");

										  if (Array.isArray(JSON.parse(dnloaded_tags))) {

											

											dnloaded_tags_new = JSON.parse(dnloaded_tags).push(val.tag_code);

											

											localStorage.setItem("scanned_tags", JSON.stringify(dnloaded_tags_new));

										  }else {

											 

											scanned_tags.push(val.tag_code);

											localStorage.setItem('scanned_tags', JSON.stringify(scanned_tags));

										   

										  }

											

										 $("#scan_tag_no").focus();

										$.toaster({ priority : 'success', title : 'Success!', message : ''+"</br>"+'Tag Downloaded Successfully..'});

									 }

								  }

                                 else

                                    {

                                        $("#scan_tag_no").focus();

                                        $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>"+'Unable to download this tag..'});

                                    }



                                }

                            })



                            if($('#ret_bill_dwnload_list  > tbody > tr').length > 0 )

                            {

                            $('#ret_bill_dwnload_list > tbody > tr:first').before(html);

                            }

                            else

                            {

                            $('#ret_bill_dwnload_list > tbody').append(html);

                            }



                        })



                    }

                }

            }

        }

    })

}









/*Sales Return Transfer Tag Scan*/



// ============================================================
// === Phase 2: Sales Transfer List View Functions ===
// ============================================================

/**
 * init_sales_transfer_list()
 * Sets up the daterangepicker and triggers the first load.
 */
function init_sales_transfer_list() {
    var startDate = moment().subtract(30, 'days');
    var endDate   = moment();

    $('#from_date').text(startDate.format('YYYY-MM-DD'));
    $('#to_date').text(endDate.format('YYYY-MM-DD'));

    $('#account-dt-btn').daterangepicker({
        startDate: startDate,
        endDate: endDate,
        ranges: {
            'Today'       : [moment(), moment()],
            'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month'  : [moment().startOf('month'), moment().endOf('month')],
            'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start, end) {
        $('#from_date').text(start.format('YYYY-MM-DD'));
        $('#to_date').text(end.format('YYYY-MM-DD'));
        get_ajaxSalesTransferList();
    });

    // Initial load
    get_ajaxSalesTransferList();
}

/**
 * get_ajaxSalesTransferList()
 * Fetches sales transfer list from server and renders DataTable with badges.
 */
function get_ajaxSalesTransferList() {
    $("div.overlay").css("display", "block");
    my_Date = new Date();

    $.ajax({
        url: base_url + "index.php/admin_ret_sales_transfer/sales_transfer/getSalesTransferList?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        data: { 'from_date': $('#from_date').text(), 'to_date': $('#to_date').text() },
        success: function(data) {
            $("div.overlay").css("display", "none");
            var list = data.list;
            var st_access = data.access;
            var st_profile = data.profile;

            var oTable = $('#bt_list').DataTable();
            oTable.clear().draw();

            if (list != null && list.length > 0) {
                oTable = $('#bt_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "order": [[ 0, "desc" ]],
                    "lengthMenu": [[-1, 25, 50, 100, 250], ["All", 25, 50, 100, 250]],
                    "scrollX": '100%',
                    "bSort": true,
                    "dom": 'lBfrtip',
                    "aaData": list,
                    "scrollY": '400px',
                    "scrollCollapse": true,
                    "paging": false,
                    "aoColumns": [
                        { "mDataProp": "from_branch_name" },
                        { "mDataProp": "to_branch_name" },
                        { "mDataProp": "bill_date" },
                        { "mDataProp": "bill_no" },
                        // Direction badge (Sales Transfer vs Sales Return Transfer)
                        { "mDataProp": function(row) {
                            var bt = parseInt(row.bill_type);
                            if (bt == 14) {
                                return '<span class="label label-danger"><i class="fa fa-reply"></i> Return</span>';
                            } else {
                                return '<span class="label label-success"><i class="fa fa-share"></i> Transfer</span>';
                            }
                        }},
                        // Transfer Type badge
                        { "mDataProp": function(row) {
                            var type = parseInt(row.salesTransType) || 1;
                            var labels = {
                                1: '<span class="label label-primary">Tagged</span>',
                                2: '<span class="label label-info">Non-Tagged</span>',
                                3: '<span class="label label-warning">Old Gold</span>',
                                4: '<span class="label label-danger">Sales Return</span>',
                                5: '<span class="label label-default">Partly Sold</span>'
                            };
                            return labels[type] || '<span class="label label-primary">Tagged</span>';
                        }},
                        // Status badge
                        { "mDataProp": function(row) {
                            var ds = parseInt(row.download_status) || 0;
                            var bs = parseInt(row.bill_status);
                            if (bs == 4 || bs == 3) {
                                return '<span class="label label-danger">Cancelled</span>';
                            } else if (ds == 1) {
                                return '<span class="label label-success">Downloaded</span>';
                            } else {
                                return '<span class="label label-warning">Dispatched</span>';
                            }
                        }},
                        // Amount
                        { "mDataProp": function(row) {
                            return '\u20b9 ' + parseFloat(row.tot_bill_amount || 0).toFixed(2);
                        }},
                        // Action - View + Cancel buttons (permission-gated like billing)
                        { "mDataProp": function(row) {
                            var bs = parseInt(row.bill_status);
                            var html = '<a href="' + base_url + 'index.php/admin_ret_sales_transfer/sales_transfer/add/' + row.bill_id + '" class="btn btn-info btn-xs" title="View"><i class="fa fa-eye"></i></a>';
                            // Cancel: requires access.edit AND profile.allow_bill_cancel
                            if (bs != 2 && bs != 3 && bs != 4
                                && st_access && st_access.edit == '1'
                                && st_profile && st_profile.allow_bill_cancel == 1) {
                                html += ' <button class="btn btn-warning btn-xs" onclick="confirm_cancel_st(' + row.bill_id + ',' + (st_profile.bill_cancel_otp || 0) + ')" title="Cancel Transfer"><i class="fa fa-close"></i></button>';
                            }
                            return html;
                        }}
                    ]
                });
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none");
        }
    });
}

// ============================================================
// === Sales Return Transfer - Type Switching Handler ===
// ============================================================

/**
 * init_sales_ret_trans_type_handler()
 * Binds the Transfer Type radio on the Sales Return Transfer form.
 */
function init_sales_ret_trans_type_handler() {
    $('input[name="sales_ret_trans_type"]').on('change', function() {
        var selectedType = $(this).val();
        $('#salesRetTransType').val(selectedType);

        if (selectedType == 1) {
            $('.sales_trans_bill_no').show();
            $('.bill_no').show();
            $('#srt_metal_selector').hide();
        } else {
            $('.sales_trans_bill_no').hide();
            $('.bill_no').hide();
            if (selectedType == 2 || selectedType == 3) {
                $('#srt_metal_selector').show();
            } else {
                $('#srt_metal_selector').hide();
            }
        }

        $('#bt_search_list tbody').empty();
        $('.tot_bt_pcs').text('0');
        $('.tot_bt_gross_wt').text('0.000');
    });

    $('input[name="sales_ret_transfer_item_type"]').on('change', function() {
        var mode = $(this).val();
        if (mode == 1) {
            $('#salesRetTransTypeRow').show();
        } else {
            $('#salesRetTransTypeRow').hide();
        }
    });
}

// ============================================================
// === Cancel Sales Transfer / Sales Return Transfer ===
// ============================================================

/**
 * confirm_cancel_st(bill_id, bill_cancel_otp)
 * Opens the cancel confirmation modal with OTP flow if required.
 * Matches the billing cancel permission pattern exactly.
 */
function confirm_cancel_st(bill_id, bill_cancel_otp) {
    $('#st_cancel_bill_id').val(bill_id);
    $('#st_bill_cancel_otp').val(bill_cancel_otp);
    $('#st_cancel_remark').val('');
    $('#st_cancel_otp').val('');
    $('#st_cancell_delete').prop('disabled', true);

    $('#confirm-billcancell').modal({ backdrop: 'static', keyboard: false }).modal('show');

    if (bill_cancel_otp == 1) {
        $('.cancel_otp').css('display', 'block');
        $('.bill_remarks').css('display', 'none');
        st_send_cancel_otp();
    } else {
        $('.cancel_otp').css('display', 'none');
        $('.bill_remarks').css('display', 'block');
    }
}

// Send OTP for cancel (reuses billing OTP endpoint)
function st_send_cancel_otp() {
    $("div.overlay").css("display", "block");
    $.ajax({
        url: base_url + 'index.php/admin_ret_billing/send_bill_cancel_otp',
        dataType: 'json',
        method: 'POST',
        data: {},
        success: function(data) {
            $("div.overlay").css("display", "none");
            if (data.status) {
                $('#st_cancel_otp').val('');
                $.toaster({ priority: 'success', title: 'Success!', message: data.msg });
                $('#st_resend_cancel_otp').prop('disabled', true);
                setTimeout(function() { $('#st_resend_cancel_otp').prop('disabled', false); }, 60000);
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: 'Unable to send OTP.' });
            }
        },
        error: function() { $("div.overlay").css("display", "none"); }
    });
}

    // Resend OTP
$(document).on('click', '#st_resend_cancel_otp', function() {
    st_send_cancel_otp();
});

// Verify OTP
$(document).on('click', '#st_verify_otp', function() {
    if ($('#st_cancel_otp').val().length == 6) {
        $.ajax({
            url: base_url + 'index.php/admin_ret_billing/verify_otp_for_billcancel',
            data: { otp: $('#st_cancel_otp').val() },
            dataType: 'json',
            method: 'POST',
            success: function(data) {
                if (data.status) {
                    $.toaster({ priority: 'success', title: 'Success!', message: data.msg });
                    $('.bill_remarks').css('display', 'block');
                    $('#st_verify_otp').prop('disabled', true);
                } else {
                    $('#st_cancel_otp').val('');
                    $.toaster({ priority: 'danger', title: 'Warning!', message: data.msg });
                }
            }
        });
    } else {
        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter 6-digit OTP.' });
    }
});

// Enable cancel button when remark has content
$(document).on('keyup', '#st_cancel_remark', function() {
    $('#st_cancell_delete').prop('disabled', this.value.length < 6);
});

// Confirm Cancel — send to backend
$(document).on('click', '#st_cancell_delete', function() {
    var bill_id = $('#st_cancel_bill_id').val();
    var reason = $('#st_cancel_remark').val();
    if (!reason || reason.trim().length < 6) {
        $.toaster({ priority: 'danger', title: 'Warning!', message: 'Please enter a valid cancellation reason (min 6 chars).' });
        return;
    }

    $('#st_cancell_delete').prop('disabled', true);
    $("div.overlay").css("display", "block");

    $.ajax({
        url: base_url + 'index.php/admin_ret_sales_transfer/cancel_sales_transfer',
        type: 'POST',
        dataType: 'json',
        data: { 'bill_id': bill_id, 'cancel_reason': reason },
        success: function(data) {
            $("div.overlay").css("display", "none");
            $('#confirm-billcancell').modal('hide');
            if (data.status) {
                $.toaster({ priority: 'success', title: 'Success!', message: data.message });
                setTimeout(function() { window.location.reload(); }, 1000);
            } else {
                $.toaster({ priority: 'danger', title: 'Warning!', message: data.message });
                $('#st_cancell_delete').prop('disabled', false);
            }
        },
        error: function() {
            $("div.overlay").css("display", "none");
            $.toaster({ priority: 'danger', title: 'Error!', message: 'Unable to cancel. Please try again.' });
            $('#st_cancell_delete').prop('disabled', false);
        }
    });
});

// ============================================================
// === ST Purchase Items — BT Parity Functions ===
// ============================================================

// Global array to store purchase item details (mirrors BT's oldMetalDetail)
var st_oldMetalDetail = [];

// --- Daterangepicker init for ST Purchase Items ---
$(function(){
    var from_date = moment().subtract(30, 'days').format('YYYY-MM-DD');
    var to_date = moment().format('YYYY-MM-DD');
    $('#st_rpt_from_date').html(from_date);
    $('#st_rpt_to_date').html(to_date);

    $('#st_purchase_date_range').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(30, 'days'),
        endDate: moment()
    }, function(start, end) {
        $('#st_rpt_from_date').text(start.format('YYYY-MM-DD'));
        $('#st_rpt_to_date').text(end.format('YYYY-MM-DD'));
    });
});

// --- Purchase Search button click ---
$(document).on('click', '.st_purchase_search', function(){
    st_get_purchase_items();
});

// --- Main AJAX function: mirrors BT's get_purchase_items() ---
function st_get_purchase_items()
{
    st_oldMetalDetail = [];
    $("div.overlay").css("display", "block"); 
    my_Date = new Date();
    $.ajax({
        url: base_url + "index.php/admin_ret_sales_transfer/get_purchase_items?nocache=" + my_Date.getUTCSeconds(),
        dataType: "JSON",
        type: "POST",
        data: {
            'from_branch': $('#from_brn').val(),
            'from_date': $('#st_rpt_from_date').text(),
            'to_date': $('#st_rpt_to_date').text()
        },
        success: function(data) {
            $(".overlay").css("display", "none");
            var oTable = $('#old_metal_list').DataTable();
            oTable.clear().draw();
            
            if (data != null && data.length > 0) {
                // Flatten bill_det arrays into st_oldMetalDetail with parent_type
                $.each(data, function(key, val) {
                    $.each(val.bill_det, function(k, items) {
                        items.parent_type = val.type;
                        st_oldMetalDetail.push(items);
                    });
                });
                
                console.log(st_oldMetalDetail);
                
                oTable = $('#old_metal_list').dataTable({
                    "bDestroy": true,
                    "bInfo": true,
                    "bFilter": true,
                    "bSort": false,
                    "order": [[ 0, "desc" ]],
                    "dom": 'lBfrtip',
                    "aaData": data,
                    "aoColumns": [
                        { "mDataProp": function(row, type, val, meta) {
                            return '<input type="checkbox" class="'+row.type+' oldmetalbtselect" name="id_old_metal_type[]"/>';
                        }},
                        { "mDataProp": function(row, type, val, meta) {
                            return row.metal_type;
                        }},
                        { "mDataProp": function(row, type, val, meta) {
                            return '<input type="hidden" name="gross_wt[]" class="gross_wt" value="'+row.gross_wt+'">'+ row.gross_wt;
                        }},
                        { "mDataProp": function(row, type, val, meta) {
                            return '<input type="hidden" name="net_wgt[]" class="net_wgt" value="'+row.net_wt+'">'+ row.net_wt;
                        }},
                        { "mDataProp": function(row, type, val, meta) {
                            return '<input type="hidden" name="dia_wt[]" class="dia_wgt" value="'+row.dia_wt+'">'+ (parseFloat(row.dia_wt).toFixed(3));
                        }},
                        { "mDataProp": function(row, type, val, meta) {
                            return '<input type="hidden" name="rate[]" class="rate" value="'+row.rate+'">'+ row.rate;
                        }},
                        {
                            "mDataProp": null,
                            "sClass": "control center", 
                            "sDefaultContent": '<span class="drill-val"><i class="fa fa-chevron-circle-down text-teal"></i></span>'
                        }
                    ]
                });
                
                // Drill-down click handler for detail rows
                var anOpen = []; 
                $(document).off('click', '#old_metal_list .control').on('click', '#old_metal_list .control', function(){ 
                    var nTr = this.parentNode;
                    var i = $.inArray( nTr, anOpen );
                    if ( i === -1 ) { 
                        $('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>'); 
                        oTable.fnOpen( nTr, st_fnFormatRowBillDetails(oTable, nTr), 'details' );
                        anOpen.push( nTr ); 
                    } else { 
                        $('.drill-val', this).html('<i class="fa fa-chevron-circle-down text-teal"></i>');
                        oTable.fnClose( nTr );
                        anOpen.splice( i, 1 );
                    }
                });
                
                st_calculate_old_metal();
            } else {
                $.toaster({ priority: 'warning', title: 'Info', message: 'No Purchase Items found for the selected date range.' });
            }
        },
        error: function(error) {
            $("div.overlay").css("display", "none"); 
            $.toaster({ priority: 'danger', title: 'Error!', message: 'Failed to fetch purchase items.' });
        }
    });
}

// --- Drill-down detail table: mirrors BT's fnFormatRowBillDetails() ---
function st_fnFormatRowBillDetails(oTable, nTr)
{
    var oData = oTable.fnGetData(nTr);
    var prodTable = 
        '<div class="innerDetails">'+
        '<table class="table table-responsive table-bordered text-center table-sm" id="st_old_metal_bill_details">'+ 
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
    var total_gwt = 0;
    var total_nwt = 0;
    var total_dwt = 0;
    var total_amt = 0;
    
    $.each(bill_det, function(idx, val) {
        var is_checked = 0;
        prodTable += 
            '<tr class="prod_det_btn">'+
            '<td><input type="checkbox" class="'+(val.type=='old_metal_items' ? 'old_metal_sale_id' : (val.item_type.indexOf('sales_ret_items_') !== -1 ? 'sales_ret_tag_id' : (val.item_type.indexOf('partly_sale_')!== -1 ? 'partial_sale_id' :'')) )+'" name="trans_id[]" value="'+val.trans_id+'" '+(is_checked==1 ? 'checked' :'')+' >'+val.trans_id+'</td>'+
            '<td>'+val.bill_date+'</td>'+
            '<td>'+val.bill_no+'</td>'+
            '<td><input type="hidden" class="gross_wt" value="'+val.gross_wt+'">'+val.gross_wt+'</td>'+
            '<td><input type="hidden" class="net_wt" value="'+val.net_wt+'">'+val.net_wt+'</td>'+
            '<td><input type="hidden" class="dia_wt" value="'+val.dia_wt+'">'+val.dia_wt+'</td>'+
            '<td><input type="hidden" class="rate" value="'+val.amount+'">'+val.amount+'</td>'+
            '</tr>'; 
        
        total_gwt += parseFloat(val.gross_wt);
        total_nwt += parseFloat(val.net_wt);
        total_dwt += parseFloat(val.dia_wt);
        total_amt += parseFloat(val.amount);
    }); 
    
    prodTable += 
        '<tr class="prod_det_btn" style="font-weight: bold;">'+
        '<td colspan="3">TOTAL</td>'+
        '<td>'+parseFloat(total_gwt).toFixed(3)+'</td>'+
        '<td>'+parseFloat(total_nwt).toFixed(3)+'</td>'+
        '<td>'+parseFloat(total_dwt).toFixed(3)+'</td>'+
        '<td>'+parseFloat(total_amt).toFixed(3)+'</td>'+
        '</tr>'; 
    
    return prodTable + '</table></div>';
}

// --- Calculate totals for old_metal_list ---
// Sums from st_oldMetalDetail (checked items) so both category-level
// and individual item-level checkbox selections are reflected in the footer.
function st_calculate_old_metal()
{
    var total_gwt = 0;
    var total_nwt = 0;
    var total_dwt = 0;
    var total_amt = 0;

    // Sum from the in-memory item array using is_checked flag
    // This correctly handles both category-level and item-level selections
    $.each(st_oldMetalDetail, function(key, item){
        if (item.is_checked == 1) {
            total_gwt += parseFloat(item.gross_wt) || 0;
            total_nwt += parseFloat(item.net_wt) || 0;
            total_dwt += parseFloat(item.dia_wt) || 0;
            total_amt += parseFloat(item.amount) || 0;
        }
    });

    $('.old_prev_grs_wt').val(parseFloat(total_gwt).toFixed(3));
    $('.old_prev_net_wt').val(parseFloat(total_nwt).toFixed(3));
    $('.old_prev_dia_wt').val(parseFloat(total_dwt).toFixed(3));
    $('.old_prev_amt').val(parseFloat(total_amt).toFixed(2));
}

// --- Select All checkbox for old_metal_list ---
$(document).on('change', '#old_metal_select_all', function(){
    var isChecked = $(this).is(':checked');
    $('#old_metal_list .oldmetalbtselect').prop('checked', isChecked);

    // Also check/uncheck all sub-item checkboxes in expanded detail rows
    $('#old_metal_list .old_metal_sale_id, #old_metal_list .sales_ret_tag_id, #old_metal_list .partial_sale_id').prop('checked', isChecked);

    // Update is_checked flag in st_oldMetalDetail
    $.each(st_oldMetalDetail, function(key, items){
        items.is_checked = isChecked ? 1 : 0;
    });

    st_calculate_old_metal();
});

// --- Individual category row checkbox for old_metal_list ---
$(document).on('change', '.oldmetalbtselect', function(){
    var $row = $(this).closest('tr');
    var isChecked = $(this).is(':checked');
    var oTable = $('#old_metal_list').dataTable();
    var rowData = oTable.fnGetData($row[0]);

    // Find the detail row if it's open and check/uncheck all sub-items
    var $detailRow = $row.next('.details');
    if ($detailRow.length) {
        $detailRow.find('.old_metal_sale_id, .sales_ret_tag_id, .partial_sale_id').prop('checked', isChecked);
    }

    // Update is_checked in st_oldMetalDetail for all items belonging to this parent row
    if (rowData && rowData.bill_det) {
        $.each(rowData.bill_det, function(idx, det) {
            $.each(st_oldMetalDetail, function(key, items) {
                if (items.trans_id == det.trans_id) {
                    items.is_checked = isChecked ? 1 : 0;
                }
            });
        });
    }

    st_calculate_old_metal();
});

// --- Individual sub-item checkbox within expanded detail rows ---
$(document).on('change', '.old_metal_sale_id, .sales_ret_tag_id, .partial_sale_id', function(){
    var transId = $(this).val();
    var isChecked = $(this).is(':checked') ? 1 : 0;

    // Update the is_checked flag in the in-memory array
    $.each(st_oldMetalDetail, function(key, items){
        if (items.trans_id == transId) {
            items.is_checked = isChecked;
        }
    });

    // Sync the parent category checkbox:
    // Find the parent category row for this sub-item
    var $detailRow = $(this).closest('tr.details, tr:has(.innerDetails)');
    if (!$detailRow.length) {
        $detailRow = $(this).closest('.innerDetails').closest('tr');
    }
    if ($detailRow.length) {
        var $parentRow = $detailRow.prev('tr');
        if ($parentRow.length && $parentRow.find('.oldmetalbtselect').length) {
            var allSubItems = $detailRow.find('.old_metal_sale_id, .sales_ret_tag_id, .partial_sale_id');
            var allChecked = allSubItems.length > 0 && allSubItems.filter(':checked').length === allSubItems.length;
            $parentRow.find('.oldmetalbtselect').prop('checked', allChecked);
        }
    }

    st_calculate_old_metal();
});


